<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Waitlist extends CI_Controller
{
	private $pager_settings;
	public function __construct()
	{
		parent::__construct();

		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->library('csvimport');
		$this->form_validation->set_error_delimiters('', '');
		$this->load->model('Main');
		$this->load->model("waitlist_model", "wmodel");
		$this->load->model("member_model", "mmodel");

		$this->load->library('csvimport');
		$this->load->library('PHPExcel');
		
		$this->load->library('pdf');

		checkLogin();
	}

	public function index()
	{
		$data['app_active'] = true;

		//if(isset($this->session->userdata['logged_in']) && $this->session->userdata['logged_in']){

		$this->template->title('Waiting List');
		$this->template->set_layout('default');
		$this->template->set_partial('header', 'partials/header');
		$this->template->set_partial('sidebar', 'partials/sidebar');
		$this->template->set_partial('aside', 'partials/aside');
		$this->template->set_partial('footer', 'partials/footer');
		$this->template->append_metadata('<script src="' . base_url("assets/js/pages/waitlist.js?ver=") . filemtime(FCPATH . "assets/js/pages/waitlist.js") . '"></script>');

		$this->template->build('waitlist/waitlist_view', $data);

		//}
		// else
		// {
		//   redirect (base_url().'404_override');
		// }	
	}

	//DISPLAY LIST

	public function getAllWaitlist()
	{

		$birth_from = $this->input->post('birth_from');
		$birth_to = $this->input->post('birth_to');

		$count_data = 0;
		$data = $condition = [];
		$query = $this->input->get('query');
		$limit = !empty($this->input->get('limit')) ? $this->input->get('limit') : 10;
		$page = !empty($this->input->get('page')) ? $this->input->get('page') : 1;
		//$priority = !empty($this->input->get('priority'))? $this->input->get('priority'):0;

		$where = [];
		$where_or = "";

		$where["archived"] = 0;


		if (!empty($this->input->get('condition'))) {
			//pdie($this->input->get('condition'), 1);
			$condition = json_decode($this->input->get('condition'));
			if (!empty($condition->prov_code)) {
				$where['prov_code'] = $condition->prov_code;
			}
			if (!empty($condition->mun_code)) {
				$where['mun_code'] = $condition->mun_code;
			}
			if (!empty($condition->bar_code)) {
				$where['bar_code'] = $condition->bar_code;
			}
			if (!empty($condition->gender) && $condition->gender != "0") {
				$where['gender'] = $condition->gender;
			}
			if ($condition->status != "") {
				if ($condition->status == "3") {
					$where["priority"] = "0";
					$where["sent_to_co"] = "1";
				} elseif ($condition->status == "4") {
					$where["priority"] = "0";
					$where["sent_to_co"] = "0";
				} else {
					$where["priority"] = $condition->status;
				}
			}

			if (!empty($condition->birth_from) && !empty($condition->birth_to)) {
				$where['birthdate <='] = $condition->birth_from;
				$where['birthdate >='] = $condition->birth_to;
			}else if(!empty($condition->birth_from) && empty($condition->birth_to)){ 
				$where['birthdate >='] = $condition->birth_from;
			}else if(empty($condition->birth_from) && !empty($condition->birth_to)){
				$where['birthdate <='] = $condition->birth_to;
			}
	
		}

		$orderBy = !empty($this->input->get('orderBy')) ? $this->input->get('orderBy') : "Full_Name";
		$ascending = $this->input->get('ascending');
		$byColumn = $this->input->get('byColumn');
		if ($page == 1) {
			$offset = 0;
		} else {
			$offset = ($page - 1) * $limit;
		}

		//$select = "w_id,reference_code AS Reference_Code, lastname, firstname, CONCAT(lastname, ', ', firstname) AS Full_Name,birthdate AS Birth_Date, YEAR(CURDATE()) - YEAR(birthdate) AS Age, gender AS Gender,osca_id AS OSCA_ID, prov_code AS Province, mun_code AS Municipality, bar_code AS Barangay, sent_to_co as Sent_To_CO";

		$select = "*, CONCAT(lastname, ', ', firstname) AS Full_Name, YEAR(CURDATE()) - YEAR(birthdate) AS Age";

		$order = array(
			'col' => $orderBy,
			'order_by' => $ascending ? "ASC" : "DESC",
		);
		$like = array('column' => ["lastname", "firstname", "middlename", "reference_code", "CONCAT(lastname, ', ', firstname)"], 'data' => $query);
		$limit = empty($query) ? $limit : 10;
		$data =  $this->wmodel->getAllWaitlist($select, $where, $where_or, $like, $offset, $order, $limit);
		$retdata = [];

		$count_qry = array(
			"select" => "count(*) as total",
			"table" => "tblwaitinglist",
			'type' => "row",
			'condition' => $where,
		);
		$count_data = $this->Main->select($count_qry, array('column' => ["lastname", "firstname", "middlename", "reference_code", "CONCAT(lastname, ', ', firstname)"], 'data' => $query), true)->total;
		$response = array(
			'count' => $count_data,
			'data' => $data,
		);
		response_json($response);
	}

	public function getWaitlistDetails()
	{
		$reference_code = $this->input->get('reference_code');
		// $w_id = $this->input->get('w_id');
		// $waitlistdetails = $this->wmodel->getWaitlistData("*",array("w_id"=>$w_id),"tblwaitinglist","","","row");
		// $reference_code = $waitlistdetails->reference_code;
		$waitlistbuf = $this->wmodel->getWaitlistData("*", array("spid" => $reference_code), "tblbufanswers", "", "", "row");
		$response = array(
			'success' => true,
			'wdetails' => $waitlistbuf,
			//'wbuf' => $waitlistbuf,
		);
		response_json($response);
	}

	//END DISPLAY LIST

	//EVENTS
	public function UpdateWaitlistStatus()
	{
		$pensionersList = json_decode($this->input->post('pensionersList'));
		$column = $this->input->post('col');
		$value = $this->input->post('val');
		$msg = $this->input->post('message');

		$dataList = array();
		$dataList[$column] = $value;

		foreach ($pensionersList as $key => $value) {
			$result = $this->wmodel->updateStatus($value, $dataList);
			$logdetail = $msg;
			$memname = getDetails("lastname, extname, firstname, middlename", "tblwaitinglist", array('w_id'=>$value));
			userLogs(sesdata('id') , sesdata('fullname') , "EDIT", $logdetail . ": " . strtoupper($memname->lastname . " " . $memname->extname . "," . $memname->firstname . " " . $memname->middlename));
		}
		$response = array(
			'success' => true,
			'message' => "Successfully updated Waitlist."
		);

		response_json($response);
	}

	public function archiveWaitlist()
	{
		$pensionersList = json_decode($this->input->post('pensionersList'));
		$reason_id = $pensionersList->reason_id;
		$input_remarks = $pensionersList->remarks;
		$msg = "Delete Waiting List";

		$dataList = array();
		$dataList["archived"] = 1;
		//$dataList["priority"] = 2;
		$dataList["reason_id"] = $reason_id;

		$w_id = $pensionersList->w_id;
		$logdetail = $msg;
		$memname = getDetails("lastname, extname, firstname, middlename, remarks", "tblwaitinglist", array('w_id'=>$w_id));

		$old_rem = $memname->remarks;
		$old_rem .= "\r\n" . $input_remarks;

		$dataList["remarks"] = $old_rem;

		$result = $this->wmodel->updateStatus($w_id, $dataList);
		userLogs(sesdata('id') , sesdata('fullname') , "DELETE", $logdetail . ": ". strtoupper($memname->lastname . " " . $memname->extname . "," . $memname->firstname . " " . $memname->middlename));

		$response = array(
			'success' => true,
			'message' => "Successfully Deleted Waitlist"
		);

		response_json($response);
	}

	public function addAsNewBeneficiary()
	{
		$waitlistid = $this->input->post('w_id');
		//$response = $this->f_addAsNewBeneficiary($waitlistid);
		$response = $this->AddNewMemberSubmit($waitlistid);
		response_json($response);
	}

	public function BulkaddAsNewBene()
	{

		$eligiblePensioners = json_decode($this->input->post('eligiblePensioners'));

		if(empty($eligiblePensioners)){
			$response = array(
				'success'	=> false,
				'message'	=> "No priority list was added to the active beneficiaries.",
				'redirect'	=> base_url('waitlist/prioritylist')
			);
		}else{
			$res = "";
			foreach ($eligiblePensioners as $key => $value) {
				$res .= ", " . $this->AddNewMemberSubmit($value);
			}
			$cnt = count($eligiblePensioners);
			$response = array(
				'success'	=> true,
				'message'	=> "$cnt waitlist are now added as new beneficiaries: ",
				'redirect'	=> base_url('waitlist/prioritylist')
			);
		}

		response_json($response);
	}

	public function f_addAsNewBeneficiary($waitlistid)
	{
		//$waitlistid = $this->input->post('wid');

		//if(userAccessStatus(sesdata('role'), "Social Pension - Priority List", "waitlist/prioritylist", "edit")) {
		date_default_timezone_set("Asia/Manila");
		$curmonth = (date("m"));
		$curyear = date("Y");
		$curdate = date('Y-m-d H:i:s');
		if ($curmonth >= 1 and $curmonth <= 3) {
			$curqtr = 1;
		} else if ($curmonth >= 4 and $curmonth <= 6) {
			$curqtr = 2;
		} else if ($curmonth >= 7 and $curmonth <= 9) {
			$curqtr = 3;
		} else if ($curmonth >= 10 and $curmonth <= 12) {
			$curqtr = 4;
		}

		$waitlistdetails = $this->wmodel->getWaitlistData("*", array("w_id" => $waitlistid), "tblwaitinglist", "", "", "row");


		$waitlistrefcode 			= $waitlistdetails->reference_code;
		$lastname 					= $waitlistdetails->lastname;
		$middlename 				= $waitlistdetails->middlename;
		$firstname 					= $waitlistdetails->firstname;
		$extname 					= $waitlistdetails->extname;
		$respondentName 			= $waitlistdetails->respondentName;
		$province 					= $waitlistdetails->prov_code;
		$city 						= $waitlistdetails->mun_code;
		$barangay 					= $waitlistdetails->bar_code;
		$address 					= $waitlistdetails->address;
		$street 					= $waitlistdetails->street;
		$permanent_province 		= $waitlistdetails->permanent_prov_code;
		$permanent_city 			= $waitlistdetails->permanent_mun_code;
		$permanent_barangay 		= $waitlistdetails->permanent_bar_code;
		$permanent_address 			= $waitlistdetails->permanent_address;
		$permanent_street 			= $waitlistdetails->permanent_street;
		$gender 					= $waitlistdetails->gender;
		$birthdate 					= $waitlistdetails->birthdate;
		$birthplace 				= $waitlistdetails->birthplace;
		$hh_id 						= $waitlistdetails->hh_id;
		$hh_size 					= $waitlistdetails->hh_size;
		$contact_no 				= $waitlistdetails->contact_no;
		$osca_id 					= $waitlistdetails->osca_id;
		$marital_status 			= $waitlistdetails->marital_status;
		$mothersMaidenName 			= $waitlistdetails->mothersMaidenName;
		$livingArrangement 			= $waitlistdetails->livingArrangement;
		$nameofCaregiver 			= $waitlistdetails->nameofCaregiver;
		$relationshipofCaregiver 	= $waitlistdetails->relationshipofCaregiver;
		$repname2 					= $waitlistdetails->repname2;
		$reprel2 					= $waitlistdetails->reprel2;
		$repname3 					= $waitlistdetails->repname3;
		$reprel3 					= $waitlistdetails->reprel3;

		$targetcondi = array("year" => $curyear, "tbltarget.mun_code" => $city);
		$this->db->select("*");
		$this->db->from("tbltarget");
		$this->db->join('tblbarangays', 'tblbarangays.`mun_code`=tbltarget.`mun_code`', 'inner');
		$this->db->where($targetcondi);
		$targetresult = $this->db->get()->row();

		$municity = $targetresult->mun_code;
		$munitarget = $targetresult->target;
		$municount = getMemberCount(array("city" => $municity, "sp_status<>" => "Inactive"))->total;

		// if($munitarget>$municount){

		//check if existing if hindi, insert, if yes, magprompt na you cannot add that because existing
		$checkcondi = array(
			'lastname' 		=> $lastname,
			'firstname' 	=> $firstname,
			'middlename' 	=> $middlename,
			'gender' 		=> $gender,
			'sp_status'		=> "Active",
			'birthdate' 	=> $birthdate
		);
		$checkExisting = $this->Main->count("tblgeneral", $checkcondi);

		if ($checkExisting > 1) {
			$success = false;
			$message = " was not added as a new beneficiary.\n\n Record is already existing.";
		} else {
			//get last spid - to generate new spid
			$munlastid = $this->Main->raw("SELECT count(*) as munlastid FROM tblgeneral WHERE city='$municity'", true)->munlastid;
			$spid = $munlastid + 1;
			$n = 1;
			$spid = "SP" . $barangay . "-" . $spid;

			$checkExisting = $this->Main->count("tblgeneral", array("connum" => $spid));

			if ($checkExisting == 0) {

				$checkExistingBUF = $this->Main->count("tblbufanswers", array("spid" => $waitlistrefcode));

				if ($checkExistingBUF > 0) {
					$waitlistinfo = $this->wmodel->getWaitlistData("*", array("spid" => $waitlistrefcode), "tblbufanswers", "", "", "row");
					$bufid				= $waitlistinfo->id;
					$workername			= $waitlistinfo->worker_name;
					$date_accomplished	= $waitlistinfo->date_accomplished;
					$data = array(
						"spid"			=> $spid,
						"date_updated"	=> $curdate
					);
					$this->Main->update("tblbufanswers", array("id" => $bufid), $data);
				} else {
					$workername	= $date_accomplished = "";
				}

				$data = array(
					'connum' 				=> $spid,
					'lastname' 				=> $lastname,
					'firstname' 			=> $firstname,
					'middlename' 			=> $middlename,
					'extensionname' 		=> $extname,
					'gender' 				=> $gender,
					'marital_status_id' 	=> $marital_status,
					'sp_status'				=> "Active",
					'street'				=> $street,
					'address'				=> $address,
					'barangay' 				=> $barangay,
					'city' 					=> $city,
					'province' 				=> $province,
					'permanent_street'		=> $permanent_street,
					'permanent_address'		=> $permanent_address,
					'permanent_barangay' 	=> $permanent_barangay,
					'permanent_city' 		=> $permanent_city,
					'permanent_province' 	=> $permanent_province,
					'registrationdate' 		=> $curdate,
					'year_start' 			=> $curyear,
					'quarter_start' 		=> $curqtr,
					'contactno' 			=> $contact_no,
					'birthdate' 			=> $birthdate,
					'hh_id' 				=> $hh_id,
					'hh_size' 				=> $hh_size,
					'osca_id' 				=> $osca_id,
					'livingarrangement_id' 	=> $livingArrangement,
					'mothersMaidenName' 	=> $mothersMaidenName,
					'respondentName' 		=> $respondentName,
					'worker_name' 			=> $workername,
					'date_accomplished' 	=> $date_accomplished,
					'buf_dateupdated'		=> $curdate
				);
				$query = $this->Main->insert("tblgeneral", $data, true);
				$lastid = $query['lastid'];

				if (!empty($nameofCaregiver)) {
					$dataguardian = array(
						"b_id"	=> $lastid,
						"rel_id" => $relationshipofCaregiver,
						"gname"	=> $nameofCaregiver,
						"date"	=> $curdate
					);
					$this->Main->insert("tblguardians", $dataguardian);
				}
				if (!empty($repname2)) {
					$dataguardian = array(
						"b_id"	=> $lastid,
						"rel_id" => $reprel2,
						"gname"	=> $repname2,
						"date"	=> $curdate
					);
					$this->Main->insert("tblguardians", $dataguardian);
				}
				if (!empty($repname3)) {
					$dataguardian = array(
						"b_id"	=> $lastid,
						"rel_id" => $reprel3,
						"gname"	=> $repname3,
						"date"	=> $curdate
					);
					$this->Main->insert("tblguardians", $dataguardian);
				}

				$dataEligible = array(
					"no"                =>  0,
					"spid"              =>  $spid,
					"bar_code"          =>  $barangay,
					"mun_code"          =>  $city,
					"prov_code"         =>  $province,
					"eligibility_stat"  =>  1,
					"date_updated"      =>  $curdate,
					"upload_batchno"    =>  20,
					"found_stat"        =>  1, //2=not found,,
					"is_pvao"           =>  0,
					"is_gsis"           =>  0
				);
				$this->Main->insert("tblEligible", $dataEligible);

				$data = array('archived' => 1,);
				$this->db->where("w_id", $waitlistid);
				$query = $this->db->update("tblwaitinglist", $data);

				//userLogs(sesdata('id') , sesdata('fullname') , "EDIT", "Added new beneficiary from Priority List: " . strtoupper($lastname . " " . $extname . "," . $firstname . " " . $middlename));
				//beneLogs(sesdata('id'), $spid, "ADD", "Added member as New Beneficiary",(NULL),(NULL));

				$success = true;
				$message = " was added as a new beneficiary successfully.";
			} else {
				$success = false;
				$message = " was not added as a new beneficiary.\n\n An error occured - SPID was taken already.";
			}
		}
		//}else{
		// 	$success=false;
		// 	$message=" was not added as a new beneficiary.\n\n Target Limit for the Municipality has been reached.";
		//}

		$currentUrl = base_url('waitlist/prioritylist') . "?province=" . $province . "&municipality=" . $city;
		$response = array(
			'success'	=> $success,
			'message'	=> $message,
			'redirect'	=> $currentUrl
		);

		return $response;
		//response_json($response);

		//}else{ show_404(); }
	}

	public function AddNewMemberSubmit($waitlistid){

		//For new SPID
		$filtercondition = array( "w_id" => $waitlistid );
		$qry = array(
            "select" => "*",
            "table" => "tblwaitinglist",
			'type' => "row",
			'condition' => $filtercondition,
        );
		$waitlistmem = $this->Main->select($qry, array(),true);

		if(empty($waitlistmem)){
			return "NONE";
		}
		
		$currdate = date("Y-m-d");
		$work_name =  ""; 
		$dateAccomplish =  $currdate;
		$dateofreplacement = $currdate; 
	
		//1. Generate SPID
		$munlastid = $this->Main->raw("SELECT count(*) as munlastid FROM tblgeneral WHERE barangay='$waitlistmem->bar_code'",true)->munlastid;
		$spid = $munlastid+1;
		$n = 1;
		$cnt = str_pad($n, 3, '0', STR_PAD_LEFT);
		$spid = "SP".$waitlistmem->bar_code."-".$cnt;
	
		//Checks if connum exists in the database
		$check_connum = checkConnum($spid);
	
		if($check_connum['inDatabase'] == 1){
			while($check_connum['inDatabase'] == 1){
				$n++;
				$serial = str_pad($n, 3, 0, STR_PAD_LEFT); 
				$spid = "SP".$waitlistmem->bar_code."-".$serial;
				$check_connum = checkConnum($spid);
			}
		}

		$year_start = date("Y");
		$period_start = 1;
		$mode_of_payment = "SEMESTER";

		// 2. Set tbl_general data
		$dataAdd = array(
			'connum' 			   		  => $spid,
			'lastname'   		   		  => $waitlistmem->lastname,
			'firstname'    		   		  => $waitlistmem->firstname,
			'middlename'   		   		  => $waitlistmem->middlename,
			'extensionname'		   		  => $waitlistmem->extname,
			'photo'		      	   		  => null,
			'gender'   			   		  => $waitlistmem->gender,
			'marital_status_id'    		  => $waitlistmem->marital_status,
			'sp_status'   		   		  => 'Active',
			'street'   		   		  	  => $waitlistmem->street,
			'address'   		   		  => $waitlistmem->address,
			'barangay'   		   		  => $waitlistmem->bar_code,
			'city'		   		   		  => $waitlistmem->mun_code,
			'province'   		   		  => $waitlistmem->prov_code,
			
			'permanent_street'   		  => $waitlistmem->permanent_street,
			'permanent_address'   		  => $waitlistmem->permanent_address,
			'permanent_barangay'   		  => $waitlistmem->permanent_bar_code,
			'permanent_city'		   	  => $waitlistmem->permanent_mun_code,
			'permanent_province'   		  => $waitlistmem->permanent_prov_code,

			'registrationdate'     		  => $currdate,
			'contactno'   		   		  => $waitlistmem->contact_no,
			'birthdate'   		   		  => $waitlistmem->birthdate,
			'birthplace'   		   		  => $waitlistmem->birthplace,
			'hh_id'   			   		  => $waitlistmem->hh_id,
			'hh_size'					  => $waitlistmem->hh_size,
			'osca_id'   		   		  => $waitlistmem->osca_id,
			'livingarrangement_id' 		  => $waitlistmem->livingArrangement,
			'mothersMaidenName'			  => $waitlistmem->mothersMaidenName,
			'year_start'     		  	  => $year_start,
			'quarter_start'     		  => $period_start,	
			'period_mode'     		  	  => $mode_of_payment,									
			'remarks'			   		  => "",								
			'representativeName1'		  => $waitlistmem->nameofCaregiver,					
			'representativeRelationship1' => $waitlistmem->relationshipofCaregiver,			
			'representativeName2'		  => $waitlistmem->repname2,						
			'representativeRelationship2' => $waitlistmem->reprel2,							
			'representativeName3'		  => $waitlistmem->repname3,						
			'representativeRelationship3' => $waitlistmem->reprel3,						
			'worker_name' 				  => $work_name,							
			'date_accomplished' 		  => $dateAccomplish,						
			'additional' 		  		  => $year_start,							
			'batch_no' 		  		  	  => 2,						
		);
	
		//3. Insert to tbl_general
		$lastBeneId = $this->Main->insert("tblgeneral", $dataAdd,'lastid')['lastid'];

		//4. Update tblbufanswers - Set spid = new_spid where spid = reference_no
		$this->db->where("spid",$waitlistmem->reference_code);
		$setInactiveResult = $this->db->update("tblbufanswers", array( 'spid'	=> $spid )); 
		
		//6. Delete tblwaitinglist where w_id
		$this->db->where("w_id",$waitlistid);
		$query = $this->db->update("tblwaitinglist", ["archived" => 1, "new_spid" => $spid]); 
		//$query=$this->db->delete('tblwaitinglist');
	
		//Log
		userLogs(sesdata('id') , sesdata('fullname') , "ADD", "ADD NEW MEMBER: $spid");
		beneLogs(sesdata('id'), $lastBeneId, "ADD", "Added as new beneficiary",(NULL),(NULL));

		return $spid;
	}

	public function addnewWaitlist()
	{
		$waitlistid 	= $this->input->post('waitlistid');
		$wdata 			= json_decode($this->input->post('wdata'));
		$isediting 		= $this->input->post('isEditing');

		$response = array(
			'success'	=> "Success",
			'message'	=> "$isediting",
			'redirect'	=> base_url('waitlist/index')
		);
		response_json($response);
	}

	public function checkProbableDuplicate()
	{

		$wdata = json_decode($this->input->post('wdata'));

		$tblgeneral_data = $this->Main->select([
			'select' => 'firstname, middlename, lastname, sp_status, barangay, city, province, connum',
			'type'	 => 'result_array',
			'table'	 => 'tblgeneral'
		]);

		$waitlist_data = $this->Main->select([
			'select' 	=> 'firstname, middlename, lastname, priority, bar_code, mun_code, prov_code, reference_code',
			'type'   	=> 'result_array',
			'table'     => 'tblwaitinglist',
			'condition' => ['archived' => 0]
		]);


		$search_key = strtolower($wdata->lastname . ', ' . $wdata->firstname . ' ' . $wdata->middlename);

		$probableActiveData = [];
		$probableWaitlistData = [];

		foreach ($tblgeneral_data as $key => $value) {

			if ($wdata->middlename == "") {
				$fullname = $value['lastname'] . ', ' . $value['firstname'];
			} else {
				$fullname = $value['lastname'] . ', ' . $value['firstname'] . ' ' . $value['middlename'];
			}

			similar_text($search_key, strtolower($fullname), $percentage);
			if ($percentage > 85) {
				$probableActiveData[] = $value;
			}
		}

		foreach ($waitlist_data as $key => $value) {

			if ($wdata->middlename == "") {
				$fullname = $value['lastname'] . ', ' . $value['firstname'];
			} else {
				$fullname = $value['lastname'] . ', ' . $value['firstname'] . ' ' . $value['middlename'];
			}

			similar_text($search_key, strtolower($fullname), $percentage);
			if ($percentage > 85) {

				$probableActiveData[] = array(
					'lastname' => $value['lastname'],
					'firstname' => $value['firstname'],
					'middlename' => $value['middlename'],
					'barangay' => $value['bar_code'],
					'city' => $value['mun_code'],
					'province' => $value['prov_code'],
					'connum' => $value['reference_code'],
					'sp_status' => $value['priority']
				);
			}
		}

		if (empty($probableActiveData)) {
			$success = true;
		} else {
			$success = false;
		}



		$response = array(
			'success'			   => $success,
			'probableActiveData'   => $probableActiveData,
		);

		response_json($response);
	}

	private function _validateData()
	{

		$config = array(
			array(
				'field' => 'lastname',
				'label' => 'Last Name',
				'rules' => 'required',
			),
			array(
				'field' => 'firstname',
				'label' => 'First Name',
				'rules' => 'required',
			),
			array(
				'field' => 'dateofbirth',
				'label' => 'Date of Birth',
				'rules' => 'required'
			),
			array(
				'field' => 'view_age',
				'label' => 'Age',
				'rules' => 'greater_than[58]'
			),
			array(
				'field' => 'gender',
				'label' => 'Gender',
				'rules' => 'required'
			),
			array(
				'field' => 'maritalstatus',
				'label' => 'Marital Status',
				'rules' => 'required'
			),
			array(
				'field' => 'birthplace',
				'label' => 'Place of Birth',
				'rules' => 'required'
			),
			array(
				'field' => 'mothersMaidenName',
				'label' => "Mother's Maiden Name",
				'rules' => 'required'
			),
			array(
				'field' => 'province_permanent',
				'label' => 'Permanent Province',
				'rules' => 'required'
			),
			array(
				'field' => 'municipality_permanent',
				'label' => 'Permanent Municipality',
				'rules' => 'required'
			),
			array(
				'field' => 'barangay_permanent',
				'label' => 'Permanent Barangay',
				'rules' => 'required'
			),
			array(
				'field' => 'province_present',
				'label' => 'Present Province',
				'rules' => 'required'
			),
			array(
				'field' => 'municipality_present',
				'label' => 'Present Municipality',
				'rules' => 'required'
			),
			array(
				'field' => 'barangay_present',
				'label' => 'Present Barangay',
				'rules' => 'required'
			),
			array(
				'field' => 'caregivername',
				'label' => 'Caregiver Name',
				'rules' => 'required'
			),
			array(
				'field' => 'caregiverrelp',
				'label' => 'Relationship of Caregiver',
				'rules' => 'required'
			),
			array(
				'field' => 'livingArrangement',
				'label' => 'Who are you living with?',
				'rules' => 'required'
			),
			array(
				'field' => 'workerName',
				'label' => 'Name of Worker',
				'rules' => 'required'
			),
			array(
				'field' => 'date_accomplished',
				'label' => 'Date Accomplished',
				'rules' => 'required'
			),

		);

		$this->form_validation->set_rules($config);
	}

	public function updateWaitlistData()
	{

		date_default_timezone_set("Asia/Manila");
		$curdate = date('Y-m-d H:i:s');

		$waitlistid 	= $this->input->post('waitlistid');
		$wdata 			= json_decode($this->input->post('wdata'), TRUE);
		$isediting 		= $this->input->post('isEditing');

		$this->form_validation->set_data($wdata);
		$this->_validateData();



		if ($this->form_validation->run() == FALSE) {
			$message = $this->form_validation->error_array();
			$success = false;
		} else {

			$data = array(
				'lastname'					=> $wdata['lastname'],
				'firstname'					=> $wdata['firstname'],
				'middlename'				=> $wdata['middlename'],
				'extname'					=> $wdata['extname'],
				'birthdate'					=> $wdata['dateofbirth'],
				'gender'					=> $wdata['gender'],
				'marital_status'			=> $wdata['maritalstatus'],
				'birthplace'				=> $wdata['birthplace'],
				'mothersMaidenName'			=> $wdata['mothersMaidenName'],
				'contact_no'				=> $wdata['contactno'],
				'hh_id'						=> $wdata['hhid'],
				'hh_size'					=> $wdata['hhsize'],
				'respondentName'			=> $wdata['respondentName'],
				'osca_id'					=> $wdata['oscaid'],
				'prov_code'					=> $wdata['province_present'],
				'mun_code'					=> $wdata['municipality_present'],
				'bar_code'					=> $wdata['barangay_present'],
				'address'					=> $wdata['address_present'],
				'street'					=> $wdata['street_present'],
				'permanent_prov_code'		=> $wdata['province_permanent'],
				'permanent_mun_code'		=> $wdata['municipality_permanent'],
				'permanent_bar_code'		=> $wdata['barangay_permanent'],
				'permanent_address'			=> $wdata['address_permanent'],
				'permanent_street'			=> $wdata['street_permanent'],
				'nameofCaregiver'			=> $wdata['caregivername'],
				'relationshipofCaregiver'	=> $wdata['caregiverrelp'],
				'repname2'					=> $wdata['rep2name'],
				'reprel2'					=> $wdata['rep2rel'],
				'repname3'					=> $wdata['rep3name'],
				'reprel3'					=> $wdata['rep3rel'],
				'livingArrangement'			=> $wdata['livingArrangement'],
				'grantee'					=> $wdata['input_grantee'],
				'date_updated'				=> $curdate
			);



			// $this->db->where("w_id",$waitlistid);
			// $query = $this->db->update("tblwaitinglist", $data);

			$wrefcode = $wdata['reference_code'];

			// AB Abra
			// AY Apayao
			// BA Baguio
			// BE Benguet
			// IF Ifugao
			// KA Kalinga
			// MO MP

			if ($isediting == "true") {
				$wrefcode = $wdata['reference_code'];
				$this->db->where("w_id", $waitlistid);
				$query = $this->db->update("tblwaitinglist", $data);
			} else {
				//getlastrefcode			
				$prov = strtoupper(substr(getProvinces('prov_name', ['prov_code' => $wdata['province_present']], 'row')->prov_name, 0, 2));
				if ($prov == "AP") {
					$prov = "AY";
				}

				$count = $this->Main->raw("SELECT reference_code FROM tblwaitinglist WHERE reference_code LIKE '%$prov%' ORDER BY reference_code DESC LIMIT 1", true);

				// $count = $this->Main->raw("SELECT reference_code FROM tblwaitinglist ORDER BY reference_code DESC LIMIT 1",true);

				if (!empty($count)) {
					$count = $count->reference_code;
					$count = substr(strrchr($count, "_"), 1) + 1;
				} else {
					$count = 1;
				}

				// $wrefcode="AP_WL_".$count;
				$cnt = str_pad($count, 7, '0', STR_PAD_LEFT);
				$wrefcode = $prov."_WL_".$cnt;

				$data['reference_code'] = $wrefcode;
				$query = $this->Main->insert("tblwaitinglist", $data);
			}

			//update buf info
			if (!empty($wdata['illness'])) {
				$illness = $wdata['illness'];
			} else {
				$illness = "NONE";
			}


			$incomewages = (empty($wdata['income_wages']) || intval($wdata['income_wages']) == 0) ? null : $wdata['ans4'];
			$incomeentrep = (empty($wdata['income_entrep']) || intval($wdata['income_entrep']) == 0) ? null : $wdata['ans6'];
			$incomehousehold = (empty($wdata['income_household']) || intval($wdata['income_household']) == 0) ? null : $wdata['ans8'];
			$incomedomestic = (empty($wdata['income_domestic']) || intval($wdata['income_domestic']) == 0) ? null : $wdata['ans10'];
			$incomeinternational = (empty($wdata['income_international']) || intval($wdata['income_international']) == 0) ? null : $wdata['ans12'];
			$incomefriends = (empty($wdata['income_friends']) || intval($wdata['income_friends']) == 0) ? null : $wdata['ans14'];
			$incomegovernment = (empty($wdata['income_government']) || intval($wdata['income_government']) == 0) ? null : $wdata['ans16'];

			$dataupdate = array(
				'pension_receiver' 			=> $wdata['pensionreceiver'],
				'provcode' 					=> $wdata['province_permanent'],
				'muncode' 					=> $wdata['municipality_permanent'],
				'barcode' 					=> $wdata['barangay_permanent'],
				'income_wages'				=> $incomewages,
				'income_wages_amt'			=> $wdata['ans4_amt'],
				'income_entrep'				=> $incomeentrep,
				'income_entrep_amt'			=> $wdata['ans6_amt'],
				'income_household'			=> $incomehousehold,
				'income_household_amt'		=> $wdata['ans8_amt'],
				'income_domestic'			=> $incomedomestic,
				'income_domestic_amt'		=> $wdata['ans10_amt'],
				'income_international'		=> $incomeinternational,
				'income_international_amt'	=> $wdata['ans12_amt'],
				'income_friends'			=> $incomefriends,
				'income_friends_amt'		=> $wdata['ans14_amt'],
				'income_government'			=> $incomegovernment,
				'income_governement_amt'	=> $wdata['ans16_amt'],
				'income_others'				=> $wdata['sourcesOfIncome_other'],
				'income_others_amt'			=> $wdata['ans18_amt'],

				'frailty_healthlimit'		=> $wdata['ans21'],
				'frailty_needregularhelp'	=> $wdata['ans22'],
				'frailty_healthhome'		=> $wdata['ans23'],
				'frailty_countonsomeone'	=> $wdata['ans24'],
				'frailty_moveabout'			=> $wdata['ans25'],

				'disability_id'				=> $wdata['disability'],
				'illness'					=> $illness,
				'worker_name'				=> $wdata['workerName'],
				'date_accomplished'			=> $wdata['date_accomplished'],

				'pension_dswd'			=> $wdata['pensionsreceived_dswd'],
				'pension_gsis'			=> $wdata['pensionsreceived_gsis'],
				'pension_sss'			=> $wdata['pensionsreceived_sss'],
				'pension_afpslai'		=> $wdata['pensionsreceived_afpslai'],
				'pension_others'		=> $wdata['pensionsreceived_other'],


				'sp_food'		=> $wdata['sp_food'],
				'sp_med'		=> $wdata['sp_med'],
				'sp_checkup'	=> $wdata['sp_checkup'],
				'sp_cloth'		=> $wdata['sp_cloth'],
				'sp_util'		=> $wdata['sp_cloth'],
				'sp_debt'		=> $wdata['sp_debt'],
				'sp_entrep'		=> $wdata['sp_entrep'],
				'sp_others'		=> $wdata['ans28_other'],
			);

			$checkexisting = $this->Main->count("tblbufanswers", array("spid" => $wrefcode));
			if ($checkexisting == 1) {
				// $dataupdateold = array( 
				// 	'pension_dswd' => null,
				// 	'pension_gsis' => null,
				// 	'pension_sss' => null,
				// 	'pension_afpslai' => null,
				// 	'pension_others' => null,

				// 	'income_wages_amt'			=> null,
				// 	'income_entrep_amt'			=> null,
				// 	'income_household_amt'		=> null,
				// 	'income_domestic_amt'		=> null,
				// 	'income_international_amt'	=> null,
				// 	'income_friends_amt'		=> null,
				// 	'income_governement_amt'	=> null,
				// 	'income_others_amt'			=> null,

				// 	'income_wages' => null,
				// 	'income_entrep' => null,
				// 	'income_household' => null,
				// 	'income_domestic' => null,
				// 	'income_international' => null,
				// 	'income_friends' => null,
				// 	'income_government' => null,
				// 	'income_others' => null,

				// 	'frailty_older85'			=> null,
				// 	'frailty_healthlimit'		=> null,
				// 	'frailty_needregularhelp'	=> null,
				// 	'frailty_healthhome'		=> null,
				// 	'frailty_countonsomeone'	=> null,
				// 	'frailty_moveabout'			=> null,

				// 	'sp_food' => null,
				// 	'sp_med' => null,
				// 	'sp_checkup' => null,
				// 	'sp_cloth' => null,
				// 	'sp_util' => null,
				// 	'sp_debt' => null,
				// 	'sp_entrep' => null,
				// 	'sp_others' => null,

				// 	'disability_id'			=> null,
				// 	'illness'				=> null,
				// );
				// $condition = array( 'spid' => $wrefcode );
				// $this->Main->update("tblbufanswers",$condition,$dataupdateold,"");

				$condition = array('spid' => $wrefcode);
				$dataupdate['updated_by_uid'] = sesdata('id');
				$dataupdate['date_updated'] = $curdate;
				$query = $this->Main->update("tblbufanswers", $condition, $dataupdate, "");
			} else {
				$dataupdate['spid'] = $wrefcode;
				$dataupdate['updated_by_uid'] = sesdata('id');
				$dataupdate['date_updated'] = $curdate;
				$query = $this->Main->insert("tblbufanswers", $dataupdate);
			}

			if ($query) {
				$success = true;
				$message = "Social Pension Waiting List data updated successfully.";
			} else {
				$success = false;
				$message = "Something Went Wrong. Please contact your Administrator.";
			}

			if ($isediting == "true") {
				$action = "EDIT";
			} else {
				$action =  "ADD";
			}
			userLogs(sesdata('id'), sesdata('fullname'), $action, "Modified Record in Waiting List: $wrefcode" . strtoupper($wdata['lastname'] . ", " . $wdata['firstname'] . " " . $wdata['middlename']));
		}

		$response = array(
			'success'	=> $success,
			'message'	=> $message,
			'redirect'	=> base_url('waitlist/index')
		);
		response_json($response);
	}

	public function exportWaitlist()
	{
		// ignore_user_abort(true);
		ini_set('max_execution_time', '60000');
		ini_set('memory_limit', '999M');

		$prov_code = $this->input->get('prov_code');
		$mun_code = $this->input->get('mun_code');
		$priority = $this->input->get('status');
		$barlist = "";

		//Start Get Libraries
		$provinces = $this->Main->get_all_provinces();
		$prov_name_list = array_column($provinces, 'prov_name', 'prov_code');
		$municipalities = $this->Main->get_all_municipalities();
		$mun_name_list = array_column($municipalities, 'mun_name', 'mun_code');
		$barangays = $this->Main->getBarangays();
		$bar_name_list = array_column($barangays, 'bar_name', 'bar_code');

		$libraries = $this->Main->getLibraries("tblmaritalstatus");
		$marStatus = array_column($libraries, "name", "id");
		$libraries = $this->Main->getLibraries("tbllivingarrangement");
		$livingArr = array_column($libraries, "name", "id");
		$libraries = $this->Main->getLibraries("tblrelationships");
		$relList = array_column($libraries, "relname", "relid");
		//END Get Libraries

		if (!empty($prov_code) && $prov_code != "" && $prov_code != "all") {
			$prov_name = (isset($prov_name_list[$prov_code])) ? $prov_name_list[$prov_code] : "";
			$filename = $prov_name . "_WAITLIST_";
			$locationcondi = "p.prov_code='$prov_code'";
		} else if (!empty($mun_code) && $mun_code != "" && $mun_code != "all") {
			$mun_name = (isset($mun_name_list[$mun_code])) ? $mun_name_list[$mun_code] : "";
			$filename = $mun_name . "_WAITLIST_";
			$locationcondi = "m.mun_code='$mun_code'";
		} else {
			$filename = "WAITLIST_";
			$locationcondi = "b.bar_code<>''";
		}

		if (!empty($priority)) {
			if ($priority == '0') {
				$filename = "NO_ELIGIBLILITY_" . $filename;
			} else if ($priority == '1') {
				$filename = "ELIGIBLE_" . $filename;
			} else if ($priority == '2') {
				$filename = "INELIGIBLE_" . $filename;
			} else if ($priority == '3') {
				$filename = "WAITING_FOR_ELIGIBILITY_" . $filename;
			} else if ($priority == '4') {
				$filename = "FOR_SENDING_TO_CO_" . $filename;
			} else {
				$filename = "ALL_" . $filename;
			}
		}

		$object = new Spreadsheet();
		$object->createSheet(0);
		$object->createSheet(1);

		//start of sheet 2 psgc
		$object->setActiveSheetIndex(1);
		$activeSheet1 = $object->getActiveSheet();
		$activeSheet1->setTitle("LIBRARIES");

		$activeSheet1->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
		$activeSheet1->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

		$margineset = $activeSheet1->getPageMargins();
		$margineset->setTop(0.25);
		$margineset->setBottom(0.25);
		$margineset->setRight(0.25);
		$margineset->setLeft(0.25);

		$excel_row = 1;

		$activeSheet1->setCellValue("A" . $excel_row, "CAR [CORDILLERA ADMINISTRATIVE REGION]/140000000");
		$excel_row += 2;
		$activeSheet1->setCellValue("A" . $excel_row, "PROVINCE");
		$activeSheet1->setCellValue("B" . $excel_row, "MUNICIPALITY");
		$activeSheet1->setCellValue("C" . $excel_row, "BARANGAY");
		$excel_row++;
		$locations = getLocation($locationcondi);
		foreach ($locations as $loc) {
			$activeSheet1->setCellValue("A" . $excel_row, $loc->prov_name . "/" . $loc->prov_code);
			$activeSheet1->setCellValue("B" . $excel_row, $loc->mun_name . "/" . $loc->mun_code);
			$activeSheet1->setCellValue("C" . $excel_row, $loc->bar_name . "/" . $loc->bar_code);
			$countbar = $excel_row;
			$excel_row++;
		}
		$activeSheet1->getColumnDimension('A')->setAutoSize(true);
		$activeSheet1->getColumnDimension('B')->setAutoSize(true);
		$activeSheet1->getColumnDimension('C')->setAutoSize(true);

		$excel_row = 1;
		$activeSheet1->setCellValue("D" . $excel_row, "Living Arrangement");
		$excel_row++;
		$livinglist = array('Living alone', 'Living with spouse only', 'Living with a child (including adopted children), child-in-law or grandchild', 'Living with another relative (other than a spouse or child/grandchild)', 'Living with unrelated people only, apart from the older persons spouse');
		foreach ($livinglist as $ll) {
			$activeSheet1->setCellValue("D" . $excel_row, $ll);
			$countlivs = $excel_row++;
		}

		//end of sheet 2

		$object->setActiveSheetIndex(0);
		$activeSheet = $object->getActiveSheet();
		$activeSheet->setTitle("Waitlist");

		$activeSheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
		$activeSheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

		$margineset = $activeSheet->getPageMargins();
		$margineset->setTop(0.25);
		$margineset->setBottom(0.25);
		$margineset->setRight(0.25);
		$margineset->setLeft(0.25);

		////// Start of Header //////

		$headerstyleborder =
			[
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER_CONTINUOUS,
					'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
				],
				'font'  => [
					'size'  => 10,
					'name' => 'Arial'
				],
				'borders' => [
					'allBorders' => [
						'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
					]
				]
			];
		$border =
			['borders' => [
				'allBorders' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				]
			]];

		$excel_row = 1;



		$activeSheet->mergeCells('B' . $excel_row . ':AO' . $excel_row)->setCellValue('B' . $excel_row, 'PERSONAL INFORMATION');
		$activeSheet->mergeCells('AP' . $excel_row . ':AU' . $excel_row)->setCellValue('AP' . $excel_row, 'TYPE OF PENSION RECEIVED IN THE PAST SIX MONTHS');
		$activeSheet->mergeCells('AV' . $excel_row . ':BT' . $excel_row)->setCellValue('AV' . $excel_row, 'WHAT ARE YOUR SOURCES OF INCOME AND FINANCIAL SUPPORT IN THE PAST 6 MONTHS');
		$activeSheet->mergeCells('BU' . $excel_row . ':CA' . $excel_row)->setCellValue('BU' . $excel_row, 'FRAILTY QUESTIONS');
		$activeSheet->mergeCells('CB' . $excel_row . ':CC' . $excel_row)->setCellValue('CB' . $excel_row, 'DISABILITY/ ILLNESS');
		$activeSheet->mergeCells('CD' . $excel_row . ':CK' . $excel_row)->setCellValue('CD' . $excel_row, 'UTILIZATION OF SOCIAL PENSION');
		$activeSheet->mergeCells('CL' . $excel_row . ':CM' . $excel_row)->setCellValue('CL' . $excel_row, 'ASSESSMENT');
		$activeSheet->mergeCells('CP' . $excel_row . ':CS' . $excel_row)->setCellValue('CP' . $excel_row, 'OTHER REPRESENTATIVES');

		$activeSheet->getColumnDimension('A')->setWidth(15); //date/time
		$activeSheet->getColumnDimension('B')->setWidth(10); //date/time
		$activeSheet->getColumnDimension('C')->setWidth(18); //refcode
		$activeSheet->getColumnDimension('D')->setWidth(18); //lname
		$activeSheet->getColumnDimension('E')->setWidth(18); //fname
		$activeSheet->getColumnDimension('F')->setWidth(18); //mname
		$activeSheet->getColumnDimension('G')->setWidth(10); //extname
		$activeSheet->getColumnDimension('H')->setWidth(10); //ID NUMBER
		$activeSheet->getColumnDimension('I')->setWidth(10); //ID NO. TYPE
		$activeSheet->getColumnDimension('J')->setWidth(10); //grantee yes/no
		$activeSheet->getColumnDimension('K')->setWidth(15); //name of respondent

		$activeSheet->getColumnDimension('L')->setWidth(13); //PERMANENT (REGION)
		$activeSheet->getColumnDimension('M')->setWidth(13); //PERMANENT (PROVINCE)
		$activeSheet->getColumnDimension('N')->setWidth(15); //PERMANENT (CITY/ MUNICIPALITY)
		$activeSheet->getColumnDimension('O')->setWidth(20); //PERMANENT (BARANGAY)
		$activeSheet->getColumnDimension('P')->setWidth(13); //PERMANENT (HOUSE NO./ZONE/PUROK/SITIO)
		$activeSheet->getColumnDimension('Q')->setWidth(13); //PERMANENT (STREET)

		$activeSheet->getColumnDimension('R')->setWidth(13); //PRESENT (REGION)
		$activeSheet->getColumnDimension('S')->setWidth(13); //PRESENT (PROVINCE)
		$activeSheet->getColumnDimension('T')->setWidth(15); //PRESENT (CITY/ MUNICIPALITY)
		$activeSheet->getColumnDimension('U')->setWidth(20); //PRESENT (BARANGAY)
		$activeSheet->getColumnDimension('V')->setWidth(13); //PRESENT (HOUSE NO./ZONE/PUROK/SITIO)
		$activeSheet->getColumnDimension('W')->setWidth(13); //PRESENT (STREET)

		$activeSheet->getColumnDimension('X')->setWidth(8); //SEX
		$activeSheet->getColumnDimension('Y')->setWidth(11); //BIRTHDATE
		$activeSheet->getColumnDimension('Z')->setWidth(6); //AGE
		$activeSheet->getColumnDimension('AA')->setWidth(11); //PLACE OF BIRTH
		$activeSheet->getColumnDimension('AB')->setWidth(15); //NAME OF CAREGIVER
		$activeSheet->getColumnDimension('AC')->setWidth(15); //RELATIONSHIP
		$column = 'AD';
		for ($i = 0; $i < 43; $i++) {
			$activeSheet->getColumnDimension($column)->setWidth(15);
			$column++;
		}
		$activeSheet->getColumnDimension('AM')->setWidth(15); //MOTHERS MAIDEN NAME
		$activeSheet->getColumnDimension('BU')->setWidth(70); //WHO ARE YOU LIVING WITH
		$column = 'BV';
		for ($i = 0; $i < 7; $i++) {
			$activeSheet->getColumnDimension($column)->setWidth(20);
			$column++;
		}
		$column = 'CB';
		for ($i = 0; $i < 18; $i++) {
			$activeSheet->getColumnDimension($column)->setWidth(15);
			$column++;
		}
		//colors
		$excel_row++;
		$prevrow = $excel_row - 1;
		$activeSheet->getStyle('A' . $prevrow . ':A' . $excel_row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('F7DA88'); //orange
		$activeSheet->getStyle('A' . $prevrow . ':AO' . $excel_row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('179C1C'); //green
		$activeSheet->getStyle('AF' . $excel_row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('A2A2A2'); //gray
		$activeSheet->getStyle('AH' . $excel_row . ':AL' . $excel_row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('A2A2A2'); //gray
		$activeSheet->getStyle('AN' . $excel_row . ':AO' . $excel_row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('A2A2A2'); //gray
		$activeSheet->getStyle('AP' . $prevrow . ':AU' . $excel_row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('1E388E'); //dark blue
		$activeSheet->getStyle('AV' . $prevrow . ':BT' . $excel_row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('405DBA'); //blue
		$activeSheet->getStyle('BU' . $prevrow . ':CK' . $excel_row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('6684E8'); //light blue
		$activeSheet->getStyle('CB' . $prevrow . ':CC' . $excel_row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('C400FF'); //violet
		$activeSheet->getStyle('CD' . $prevrow . ':CM' . $excel_row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('C4D5F2'); //light light blue
		$activeSheet->getStyle('CP' . $prevrow . ':CS' . $excel_row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('179C1C'); //green

		$table_columns = array(
			"DATE/TIME", "SENIOR CITIZEN ID NO./OSCA NO.", "REFERENCE CODE", "LAST NAME / QUALIFIER", "FIRST NAME", "MIDDLE NAME", "EXT NAME",
			"ID NUMBER", "ID NO. TYPE", "GRANTEE (YES/NO)", "NAME OF RESPONDENT",
			"PERMANENT (REGION)", "PERMANENT (PROVINCE)",  "PERMANENT (CITY/ MUNICIPALITY)", "PERMANENT (BARANGAY)", "PERMANENT (HOUSE NO./ZONE/PUROK/SITIO)", "PERMANENT (STREET)",
			"PRESENT (REGION)", "PRESENT (PROVINCE)", "PRESENT (CITY/ MUNICIPALITY)", "PRESENT (BARANGAY)", "PRESENT (HOUSE NO./ZONE/PUROK/SITIO)", "PRESENT (STREET)",
			"SEX", "BIRTHDATE (MM/DD/CCYY)", "AGE", "PLACE OF BIRTH", "NAME OF CARE GIVER", "RELATIONSHIP", "MARITAL STATUS", "HOUSEHOLD SIZE", "TIN", "MOBILE NO.",
			"NATIONALITY", "PROFESSION", "SOURCE OF FUNDS", "GROSS SALARY", "EMAIL", "MOTHER'S MAIDEN NAME", "EMBOSS NAME", "LBP Bank #",
			"RECEIVE OF PENSION (YES/NO)", "DSWD SOCIAL PENSION", "GSIS", "SSS", "AFPSLAI", "OTHERS1",
			"WAGES/SALARIES", "IS IT REGULAR (YES/NO)1", "AMOUNT OF INCOME1",
			"PROFIT FROM ENTERPRENEURIAL ACTIVITIES", "IS IT REGULAR (YES/NO)2", "AMOUNT OF INCOME2",
			"HOUSEHOLD FAMILY MEMBERS/RELATIVES", "IS IT REGULAR (YES/NO)3", "AMOUNT OF INCOME3",
			"DOMESTIC FAMILY MEMBERS/RELATIVES", "IS IT REGULAR (YES/NO)4", "AMOUNT OF INCOME4",
			"INTERNATIONAL FAMILY MEMBERS/RELATIVES", "IS IT REGULAR (YES/NO)5", "AMOUNT OF INCOME5",
			"FRIENDS/NEIGHBORS", "IS IT REGULAR (YES/NO)6", "AMOUNT OF INCOME6",
			"TRANSFERS FROM THE GOVERNMENT", "IS IT REGULAR (YES/NO)7", "AMOUNT OF INCOME7",
			"OTHERS8", "IS IT REGULAR (YES/NO)8", "AMOUNT OF INCOME8", "TOTAL AMOUNT",
			"WHO ARE YOU LIVING WITH?",
			"OLDER THAN 85 YEARS? (YES/NO)",
			"DO YOU HAVE ANY HEALTH PROBLEMS THAT REQUIRE YOU TO LIMIT YOUR ACTIVITIES (YES/NO)",
			"DO YOU NEED SOMEONE TO HELP YOU ON A REGULAR BASIS? (YES/NO)",
			"DO YOU HAVE ANY HEALTH PROBLEMS THAT REQUIRE YOU TO STAY AT HOME? (YES/NO)",
			"IF YOU NEED HELP CAN YOU COUNT ON SOMEONE CLOSE TO YOU? (YES/NO)",
			"DO YOU REGULARLY USE A STICK/WALKER/WHEELCHAIR TO MOVE ABOUT? (YES/NO)",
			"DISABILITY", "ILLNESS/DISEASE", "FOOD", "MEDICINES AND VITAMINS", "HEALTH CHECK-UP AND OTHER OSPITAL/MEDICAL SERVICES", "CLOTHING", "UTILITIES", "DEBT PAYMENT", "LIVELIHOOD/ENTERPRENEURIAL ACITIVIES", "OTHERS2",
			"NAME OF WORKER", "DATE ACCOMPLISHED", "REMARKS", "DATE_ENCODED",
			"REPNAME2", "REPREL2", "REPNAME3", "REPREL3"
		);

		$hs = "A";
		foreach ($table_columns as $tv) {
			$activeSheet->setCellValue($hs . $excel_row, $tv);
			$hs++;
			$activeSheet->getStyle('A' . $prevrow . ':CS' . $excel_row)->applyFromArray($headerstyleborder);
			$activeSheet->getStyle('A' . $prevrow . ':CS' . $excel_row)->getFont()->setBold(true);
			$activeSheet->getStyle('A' . $prevrow . ':CS' . $excel_row)->getAlignment()->setWrapText(true);
		}

		$excel_row++;
		// $yesnolist = trim(implode(',', ['YES', 'NO']));
		$yesnolist = "YES,NO";
		$sexlist = "Female,Male";

		//END OF HEADER

		$condition = array("reference_code<>" => "0", "archived" => "0");
		if ($prov_code != "all") {
			$condition["prov_code"] = $prov_code;
		}
		if ($mun_code != "all") {
			$condition["mun_code"] = $mun_code;
		}
		if ($priority != "all") {
			if ($priority == "3") {
				$condition["priority"] = "0";
				$condition["sent_to_co"] = "1";
			} elseif ($priority == "4") {
				$condition["priority"] = "0";
				$condition["sent_to_co"] = "0";
			} else {
				$condition["priority"] = $priority;
			}
		}

		$queries = array(
			"select"	=> "*",
			"table"		=> "tblwaitinglist",
			"condition"	=> $condition,
			'order'     => array("col" => "lastname", "order_by" => "ASC"),
			"limit"		=> "",
			"offset"	=> "",
			"type"		=> ""
		);
		$waitlist = $this->Main->select($queries);
		$waitlistCount = count($waitlist);

		$rownum = $excel_row;
		$number = 1;

		//for($rownum=$excel_row; $rownum<=$waitlistCount; $rownum++){
		foreach ($waitlist as $wpdata) {
			$per_prov_name = (isset($prov_name_list[$wpdata->permanent_prov_code])) ? $prov_name_list[$wpdata->permanent_prov_code] : "";
			$per_mun_name = (isset($mun_name_list[$wpdata->permanent_mun_code])) ? $mun_name_list[$wpdata->permanent_mun_code] : "";
			$per_bar_name = (isset($bar_name_list[$wpdata->permanent_bar_code])) ? $bar_name_list[$wpdata->permanent_bar_code] : "";
			$prov_name = (isset($prov_name_list[$wpdata->prov_code])) ? $prov_name_list[$wpdata->prov_code] : "";
			$mun_name = (isset($mun_name_list[$wpdata->mun_code])) ? $mun_name_list[$wpdata->mun_code] : "";
			$bar_name = (isset($bar_name_list[$wpdata->bar_code])) ? $bar_name_list[$wpdata->bar_code] : "";
			$grantee = ($wpdata->grantee == 1) ? "YES" : "NO";
			$activeSheet->setCellValue("A" . $rownum, $number);
			$activeSheet->setCellValue("B" . $rownum, $wpdata->osca_id);
			$activeSheet->setCellValue("C" . $rownum, $wpdata->reference_code);
			$activeSheet->setCellValue("D" . $rownum, $wpdata->lastname);
			$activeSheet->setCellValue("E" . $rownum, $wpdata->firstname);
			$activeSheet->setCellValue("F" . $rownum, $wpdata->middlename);
			$activeSheet->setCellValue("G" . $rownum, $wpdata->extname);
			$activeSheet->setCellValue("H" . $rownum, ""); //ID Number
			$activeSheet->setCellValue("I" . $rownum, ""); //ID Number Type
			$activeSheet->setCellValue("J" . $rownum, $grantee); //Grantee
			$activeSheet->setCellValue("K" . $rownum, $wpdata->respondentName);
			$activeSheet->setCellValue("L" . $rownum, "CAR [CORDILLERA ADMINISTRATIVE REGION]/140000000");
			$activeSheet->setCellValue("M" . $rownum, "$per_prov_name/$wpdata->permanent_prov_code");
			$activeSheet->setCellValue("N" . $rownum, "$per_mun_name/$wpdata->permanent_mun_code");
			$activeSheet->setCellValue("O" . $rownum, "$per_bar_name/$wpdata->permanent_bar_code");
			$activeSheet->setCellValue("P" . $rownum, $wpdata->permanent_address);
			$activeSheet->setCellValue("P" . $rownum, $wpdata->permanent_street);
			$activeSheet->setCellValue("R" . $rownum, "CAR [CORDILLERA ADMINISTRATIVE REGION]/140000000");
			$activeSheet->setCellValue("S" . $rownum, "$prov_name/$wpdata->prov_code");
			$activeSheet->setCellValue("T" . $rownum, "$mun_name/$wpdata->mun_code");
			$activeSheet->setCellValue("U" . $rownum, "$bar_name/$wpdata->bar_code");
			$activeSheet->setCellValue("V" . $rownum, $wpdata->address);
			$activeSheet->setCellValue("W" . $rownum, $wpdata->street);
			$activeSheet->setCellValue("X" . $rownum, $wpdata->gender);
			$activeSheet->setCellValue("Y" . $rownum, $wpdata->birthdate);
			$activeSheet->setCellValue("Z" . $rownum, "");

			$activeSheet->setCellValue("AA" . $rownum, $wpdata->birthplace);
			$activeSheet->setCellValue("AB" . $rownum, $wpdata->nameofCaregiver);
			$caregiverRel = (isset($relList[$wpdata->relationshipofCaregiver])) ? $relList[$wpdata->relationshipofCaregiver] : "";
			$activeSheet->setCellValue("AC" . $rownum, $caregiverRel);
			$marital_status = (isset($marStatus[$wpdata->marital_status])) ? $marStatus[$wpdata->marital_status] : "";
			$activeSheet->setCellValue("AD" . $rownum, $marital_status);
			$activeSheet->setCellValue("AE" . $rownum, $wpdata->hh_size);
			//$activeSheet->setCellValue("AF".$rownum,""); //TIN
			$activeSheet->setCellValue("AG" . $rownum, $wpdata->contact_no);

			$activeSheet->setCellValue("AM" . $rownum, $wpdata->mothersMaidenName);

			$remarks = "";
			if ($wpdata->priority == "0") {
				$remarks = ($wpdata->sent_to_co == "1") ? "SENT TO C.O (WAITING FOR ELIGIBILITY)" : "NO ELIGIBILITY STATUS YET";
			} else if ($wpdata->priority == "1") {
				$remarks = "ELIGIBLE WAITLIST";
			} else if ($wpdata->priority == "2") {
				$remarks = "NOT ELIGIBLE";
			}

			if (!empty($wpdata->remarks)) {
				$remarks .= " - $wpdata->remarks";
			}

			$activeSheet->setCellValue("CN" . $rownum, $remarks);

			$activeSheet->setCellValue("CP" . $rownum, $wpdata->repname2);
			$reprel2 = (isset($relList[$wpdata->reprel2])) ? $relList[$wpdata->reprel2] : "";
			$activeSheet->setCellValue("CQ" . $rownum, $reprel2);

			$activeSheet->setCellValue("CR" . $rownum, $wpdata->repname3);
			$reprel3 = (isset($relList[$wpdata->reprel3])) ? $relList[$wpdata->reprel3] : "";
			$activeSheet->setCellValue("CS" . $rownum, $reprel3);

			//Duplicate
			$activeSheet->setCellValue("CT" . $rownum, $wpdata->duplicate);

			//Who are you living with
			$livingArrangement = (isset($livingArr[$wpdata->livingArrangement])) ? $livingArr[$wpdata->livingArrangement] : "";
			$activeSheet->setCellValue("BU" . $rownum, $livingArrangement);

			$bufdata = $this->wmodel->getWaitlistData("*", array("spid" => $wpdata->reference_code), "tblbufanswers", "", "", "row");
			if (!empty($bufdata)) {
				$pension_receiver = (!empty($bufdata->pension_receiver) && $bufdata->pension_receiver != "1") ? "NO" : "YES";
				$pension_dswd = (!empty($bufdata->pension_dswd) && $bufdata->pension_dswd != "0") ? "YES" : "NO";
				$pension_gsis = (!empty($bufdata->pension_gsis) && $bufdata->pension_gsis != "0") ? "YES" : "NO";
				$pension_sss = (!empty($bufdata->pension_sss) && $bufdata->pension_sss != "0") ? "YES" : "NO";
				$pension_afpslai = (!empty($bufdata->pension_afpslai) && $bufdata->pension_afpslai != "0") ? "YES" : "NO";
				$pension_others =  $bufdata->pension_others;

				$income_wages = (!empty($bufdata->income_wages) && $bufdata->income_wages != "0") ? "YES" : "NO";
				$income_wages_amt =  $bufdata->income_wages_amt;
				$income_entrep = (!empty($bufdata->income_entrep) && $bufdata->income_entrep != "0") ? "YES" : "NO";
				$income_entrep_amt =  $bufdata->income_entrep_amt;
				$income_household = (!empty($bufdata->income_household) && $bufdata->income_household != "0") ? "YES" : "NO";
				$income_household_amt =  $bufdata->income_household_amt;
				$income_domestic = (!empty($bufdata->income_domestic) && $bufdata->income_domestic != "0") ? "YES" : "NO";
				$income_domestic_amt =  $bufdata->income_domestic_amt;
				$income_international = (!empty($bufdata->income_international) && $bufdata->income_international != "0") ? "YES" : "NO";
				$income_international_amt =  $bufdata->income_international_amt;
				$income_friends = (!empty($bufdata->income_friends) && $bufdata->income_friends != "0") ? "YES" : "NO";
				$income_friends_amt =  $bufdata->income_friends_amt;
				$income_government = (!empty($bufdata->income_government) && $bufdata->income_government != "0") ? "YES" : "NO";
				$income_governement_amt =  $bufdata->income_governement_amt;

				$income_others =  $bufdata->income_others;
				$income_others_amt =  $bufdata->income_others_amt;

				$frailty_older85 = (!empty($bufdata->frailty_older85) && $bufdata->frailty_older85 != "0") ? "YES" : "NO";
				$frailty_healthlimit = (!empty($bufdata->frailty_healthlimit) && $bufdata->frailty_healthlimit != "0") ? "YES" : "NO";
				$frailty_needregularhelp = (!empty($bufdata->frailty_needregularhelp) && $bufdata->frailty_needregularhelp != "0") ? "YES" : "NO";
				$frailty_healthhome = (!empty($bufdata->frailty_healthhome) && $bufdata->frailty_healthhome != "0") ? "YES" : "NO";
				$frailty_countonsomeone = (!empty($bufdata->frailty_countonsomeone) && $bufdata->frailty_countonsomeone != "0") ? "YES" : "NO";
				$frailty_moveabout = (!empty($bufdata->frailty_moveabout) && $bufdata->frailty_moveabout != "0") ? "YES" : "NO";
				$disability_id = (!empty($bufdata->disability_id) && $bufdata->disability_id != "0") ? "YES" : "NO";
				$illness =  $bufdata->illness;

				$sp_food = (!empty($bufdata->sp_food) && $bufdata->sp_food != "0") ? "YES" : "";
				$sp_med = (!empty($bufdata->sp_med) && $bufdata->sp_med != "0") ? "YES" : "";
				$sp_checkup = (!empty($bufdata->sp_checkup) && $bufdata->sp_checkup != "0") ? "YES" : "";
				$sp_cloth = (!empty($bufdata->sp_cloth) && $bufdata->sp_cloth != "0") ? "YES" : "";
				$sp_util = (!empty($bufdata->sp_util) && $bufdata->sp_util != "0") ? "YES" : "";
				$sp_debt = (!empty($bufdata->sp_debt) && $bufdata->sp_debt != "0") ? "YES" : "";
				$sp_entrep = (!empty($bufdata->sp_entrep) && $bufdata->sp_entrep != "0") ? "YES" : "";
				$sp_others =  $bufdata->sp_others;
				$worker_name =  $bufdata->worker_name;
				$date_accomplished =  $bufdata->date_accomplished;

				$activeSheet->setCellValue("AP" . $rownum, $pension_receiver);
				$activeSheet->setCellValue("AQ" . $rownum, $pension_dswd);
				$activeSheet->setCellValue("AR" . $rownum, $pension_gsis);
				$activeSheet->setCellValue("AS" . $rownum, $pension_sss);
				$activeSheet->setCellValue("AT" . $rownum, $pension_afpslai);
				$activeSheet->setCellValue("AU" . $rownum, $pension_others);

				$activeSheet->setCellValue("AV" . $rownum, $income_wages);
				if ($income_wages == "YES") {
					$isitregular = (!empty($income_wages_amt)) ? "YES" : "NO";
					$activeSheet->setCellValue("AW" . $rownum, $isitregular);
				}
				$activeSheet->setCellValue("AX" . $rownum, $income_wages_amt);

				$activeSheet->setCellValue("AY" . $rownum, $income_entrep);
				if ($income_entrep == "YES") {
					$isitregular = (!empty($income_entrep_amt)) ? "YES" : "NO";
					$activeSheet->setCellValue("AZ" . $rownum, $isitregular);
				}
				$activeSheet->setCellValue("BA" . $rownum, $income_entrep_amt);

				$activeSheet->setCellValue("BB" . $rownum, $income_household);
				if ($income_household == "YES") {
					$isitregular = (!empty($income_household_amt)) ? "YES" : "NO";
					$activeSheet->setCellValue("BC" . $rownum, $isitregular);
				}
				$activeSheet->setCellValue("BD" . $rownum, $income_household_amt);

				$activeSheet->setCellValue("BE" . $rownum, $income_domestic);
				if ($income_domestic == "YES") {
					$isitregular = (!empty($income_domestic_amt)) ? "YES" : "NO";
					$activeSheet->setCellValue("BF" . $rownum, $isitregular);
				}
				$activeSheet->setCellValue("BG" . $rownum, $income_domestic_amt);

				$activeSheet->setCellValue("BH" . $rownum, $income_international);
				if ($income_international == "YES") {
					$isitregular = (!empty($income_international_amt)) ? "YES" : "NO";
					$activeSheet->setCellValue("BI" . $rownum, $isitregular);
				}
				$activeSheet->setCellValue("BJ" . $rownum, $income_international_amt);

				$activeSheet->setCellValue("BK" . $rownum, $income_friends);
				if ($income_friends == "YES") {
					$isitregular = (!empty($income_friends_amt)) ? "YES" : "NO";
					$activeSheet->setCellValue("BL" . $rownum, $isitregular);
				}
				$activeSheet->setCellValue("BM" . $rownum, $income_friends_amt);

				$activeSheet->setCellValue("BN" . $rownum, $income_government);
				if ($income_government == "YES") {
					$isitregular = (!empty($income_governement_amt)) ? "YES" : "NO";
					$activeSheet->setCellValue("BO" . $rownum, $isitregular);
				}
				$activeSheet->setCellValue("BP" . $rownum, $income_governement_amt);

				$activeSheet->setCellValue("BQ" . $rownum, $income_others);
				if ($income_others == "YES") {
					$isitregular = (!empty($income_others_amt)) ? "YES" : "NO";
					$activeSheet->setCellValue("BR" . $rownum, $isitregular);
				}
				$activeSheet->setCellValue("BS" . $rownum, $income_others_amt);

				//$activeSheet->setCellValue("BT".$rownum,"$wpdata->mun_name/$wpdata->mun_code");

				$activeSheet->setCellValue("BV" . $rownum, $frailty_older85);
				$activeSheet->setCellValue("BW" . $rownum, $frailty_healthlimit);
				$activeSheet->setCellValue("BX" . $rownum, $frailty_needregularhelp);
				$activeSheet->setCellValue("BY" . $rownum, $frailty_healthhome);
				$activeSheet->setCellValue("BZ" . $rownum, $frailty_countonsomeone);

				$activeSheet->setCellValue("CA" . $rownum, $frailty_moveabout);
				$activeSheet->setCellValue("CB" . $rownum, $disability_id);
				$activeSheet->setCellValue("CC" . $rownum, $illness);
				$activeSheet->setCellValue("CD" . $rownum, $sp_food);
				$activeSheet->setCellValue("CE" . $rownum, $sp_med);
				$activeSheet->setCellValue("CF" . $rownum, $sp_checkup);
				$activeSheet->setCellValue("CG" . $rownum, $sp_cloth);
				$activeSheet->setCellValue("CH" . $rownum, $sp_util);
				$activeSheet->setCellValue("CI" . $rownum, $sp_debt);
				$activeSheet->setCellValue("CJ" . $rownum, $sp_entrep);
				$activeSheet->setCellValue("CK" . $rownum, $sp_others);
				$activeSheet->setCellValue("CL" . $rownum, $worker_name);
				$activeSheet->setCellValue("CM" . $rownum, $date_accomplished);
			}


			$number++;
			$rownum++;
		}

		//end of sheet 1

		$object->setActiveSheetIndex(0);
		$activeSheet->setSelectedCell('A1');

		$activeSheet->setShowGridlines(true);
		$filename = $filename . "_($waitlistCount).xlsx";

		$writer = new Xlsx($object);
		$writer->setPreCalculateFormulas(true);

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $filename);
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
		userLogs(sesdata('id'), sesdata('fullname'), "EXPORT", "Export Waitlist BUF Details");
	}
	//END EVENTS

	//IMPORT WAITLIST

	public function uploadWaitlist()
	{

		date_default_timezone_set("Asia/Manila");
		$curdate = date('Y-m-d H:i:s');

		// if(!$this->input->is_ajax_request()){
		// 	show_404();
		// }

		ini_set('max_execution_time', '60000');
		ini_set('memory_limit', '999M');

		$config['upload_path'] = "uploads/files/csv";
		$config['allowed_types'] = "csv";
		// $config['file_name'] =
		if (!is_dir($config['upload_path'])) {
			mkdir($config['upload_path'], 0777, TRUE);
		}

		$response = false;
		try {

			$this->load->library('upload', $config);

			if (!$this->upload->do_upload('file')) {
				$error = array('error' => $this->upload->display_errors());
				$response['status'] = 'Error';
				$response['message'] =  $error['error'];
				return $response;
			} else {
				//get Library
				$libraries = $this->Main->getLibraries("tblmaritalstatus");
				$marStatus = array_column($libraries, "id", "name");
				$marStatus = array_change_key_case($marStatus);

				$libraries = $this->Main->getLibraries("tbllivingarrangement");
				$livingArr = array_column($libraries, "id", "name");
				$livingArr = array_change_key_case($livingArr);

				$libraries = $this->Main->getLibraries("tblrelationships");
				$relList = array_column($libraries, "relid", "relname");
				$relList = array_change_key_case($relList);

				$libraries = $this->Main->getLibraries("tbldisability");
				$disabilityList = array_column($libraries, "id", "name");
				$disabilityList = array_change_key_case($disabilityList);

				//Get File Upload
				$path_folder = '/uploads/files/csv';
				$file_name = str_replace(" ", "_", $_FILES['file']['name']);
				$file_path = server_path . $path_folder . "/" . $file_name;
				$file_data = $this->csvimport->get_array($file_path, FALSE, FALSE, 0);

				$checkRow = 0;
				$num_rows = 0;
				$insert_waitlist = [];
				$insert_buf = [];

				$update_waitlist = [];
				$update_buf = [];

				$_count["AB"] = 0; //Abra
				$_count["AY"] = 0; //Apayao
				$_count["BA"] = 0; //Baguio
				$_count["BE"] = 0; //Benguet
				$_count["IF"] = 0; //Ifugao
				$_count["KA"] = 0; //Kalinga
				$_count["MO"] = 0; //MP

				foreach ($file_data as $row) {
					$refcode      = $row["REFERENCE CODE"];

					$oscaid             = $row["SENIOR CITIZEN ID NO./OSCA NO."];
					$lastname			= $row["LAST NAME / QUALIFIER"];
					$firstname			= $row["FIRST NAME"];
					$middlename			= $row["MIDDLE NAME"];
					$extname			= $row["EXT NAME"];
					$respondentName		= $row["NAME OF RESPONDENT"];
					$permanentprov		= $row["PERMANENT (PROVINCE)"];
					$permanentmuni		= $row["PERMANENT (CITY/ MUNICIPALITY)"];
					$permanentbara		= $row["PERMANENT (BARANGAY)"];
					$permanentaddress	= $row["PERMANENT (HOUSE NO./ZONE/PUROK/SITIO)"];
					$permanentstreet	= $row["PERMANENT (STREET)"];
					$presentprov		= $row["PRESENT (PROVINCE)"];
					$presentmuni		= $row["PRESENT (CITY/ MUNICIPALITY)"];
					$presentbara		= $row["PRESENT (BARANGAY)"];
					$presentaddress		= $row["PRESENT (HOUSE NO./ZONE/PUROK/SITIO)"];
					$presentstreet		= $row["PRESENT (STREET)"];
					$sex				= $row["SEX"];
					$birthdate			= $row["BIRTHDATE (MM/DD/CCYY)"];
					$birthplace			= $row["PLACE OF BIRTH"];
					$caregiver			= $row["NAME OF CARE GIVER"];
					$caregiverrel		= $row["RELATIONSHIP"];
					$maritalstat		= $row["MARITAL STATUS"];
					$hhsize				= $row["HOUSEHOLD SIZE"];
					$mobileno			= $row["MOBILE NO."];
					$mothersMaidenName	= $row["MOTHER'S MAIDEN NAME"];
					$livingArrangement	= $row["WHO ARE YOU LIVING WITH?"];
					$repname1			= $row['REPNAME2'];
					$reprel1			= $row['REPREL2'];
					$repname2			= $row['REPNAME3'];
					$reprel2			= $row['REPREL3'];

					//buf answers
					$pensionreceiver			= $row["RECEIVE OF PENSION (YES/NO)"];
					$pension_dswd				= $row["DSWD SOCIAL PENSION"];
					$pension_gsis				= $row["GSIS"];
					$pension_sss				= $row["SSS"];
					$pension_afpslai			= $row["AFPSLAI"];
					$pension_others				= $row["OTHERS1"];
					$income_wages				= $row["WAGES/SALARIES"];
					$income_wages_amt			= $row["AMOUNT OF INCOME1"];
					$income_entrep				= $row["PROFIT FROM ENTERPRENEURIAL ACTIVITIES"];
					$income_entrep_amt			= $row["AMOUNT OF INCOME2"];
					$income_household			= $row["HOUSEHOLD FAMILY MEMBERS/RELATIVES"];
					$income_household_amt		= $row["AMOUNT OF INCOME3"];
					$income_domestic			= $row["DOMESTIC FAMILY MEMBERS/RELATIVES"];
					$income_domestic_amt		= $row["AMOUNT OF INCOME4"];
					$income_international		= $row["INTERNATIONAL FAMILY MEMBERS/RELATIVES"];
					$income_international_amt	= $row["AMOUNT OF INCOME5"];
					$income_friends				= $row["FRIENDS/NEIGHBORS"];
					$income_friends_amt			= $row["AMOUNT OF INCOME6"];
					$income_government			= $row["TRANSFERS FROM THE GOVERNMENT"];
					$income_governement_amt		= $row["AMOUNT OF INCOME7"];
					$income_others				= $row["OTHERS8"];
					$income_others_amt			= $row["AMOUNT OF INCOME8"];
					$frailty_healthlimit		= $row["DO YOU HAVE ANY HEALTH PROBLEMS THAT REQUIRE YOU TO LIMIT YOUR ACTIVITIES (YES/NO)"];
					$frailty_needregularhelp	= $row["DO YOU NEED SOMEONE TO HELP YOU ON A REGULAR BASIS? (YES/NO)"];
					$frailty_healthhome			= $row["DO YOU HAVE ANY HEALTH PROBLEMS THAT REQUIRE YOU TO STAY AT HOME? (YES/NO)"];
					$frailty_countonsomeone		= $row["IF YOU NEED HELP CAN YOU COUNT ON SOMEONE CLOSE TO YOU? (YES/NO)"];
					$frailty_moveabout			= $row["DO YOU REGULARLY USE A STICK/WALKER/WHEELCHAIR TO MOVE ABOUT? (YES/NO)"];
					$disability_id				= $row["DISABILITY"];
					$illness					= $row["ILLNESS/DISEASE"];
					$sp_food					= $row["FOOD"];
					$sp_med						= $row["MEDICINES AND VITAMINS"];
					$sp_checkup					= $row["HEALTH CHECK-UP AND OTHER OSPITAL/MEDICAL SERVICES"];
					$sp_cloth					= $row["CLOTHING"];
					$sp_util					= $row["UTILITIES"];
					$sp_debt					= $row["DEBT PAYMENT"];
					$sp_entrep					= $row["LIVELIHOOD/ENTERPRENEURIAL ACITIVIES"];
					$sp_others					= $row["OTHERS2"];
					$worker_name				= $row["NAME OF WORKER"];
					$date_accomplished			= $row["DATE ACCOMPLISHED"];
					$remarks					= $row["REMARKS"];
					$priority					= 0;
					$batch_no					= 0;
					$sent_to_co					= 0;

					// if ($remarks == "Eligible"){
					// 	$priority	= 1;
					// }elseif ($remarks == "No Eligibility Yet") {
					// 	$priority	= 0;
					// }

					$prov_name = $presentprov;

					$presentbara = (!empty($presentbara)) ? substr($presentbara, strpos($presentbara, "/") + 1) : "";
					$presentmuni = (!empty($presentmuni)) ? substr($presentmuni, strpos($presentmuni, "/") + 1) : "";
					$presentprov = (!empty($presentprov)) ? substr($presentprov, strpos($presentprov, "/") + 1) : "";

					$permanentbara = (!empty($permanentbara)) ? substr($permanentbara, strpos($permanentbara, "/") + 1) : "";
					$permanentmuni = (!empty($permanentmuni)) ? substr($permanentmuni, strpos($permanentmuni, "/") + 1) : "";
					$permanentprov = (!empty($permanentprov)) ? substr($permanentprov, strpos($permanentprov, "/") + 1) : "";

					// $birthdate = (string)$birthdate;
					// if(!empty($birthdate)){
					// 	$date = date_create($birthdate);
					// 	$birthdate = date_format($date, "Y-m-d");
					// }else{ $birthdate=""; }

					// $date_accomplished = (string)$date_accomplished;
					// if(!empty($date_accomplished)){
					// 	$date = date_create($date_accomplished);
					// 	$date_accomplished = date_format($date, "Y-m-d");
					// }else{ $date_accomplished=""; }

					$num_rows++;

					if (empty($lastname) || empty($firstname)) {
						$checkRow++;
						$num_rows--;
					}
					if ($checkRow >= 3) {
						break;
					}

					//get id of maritalstat
					$marital_id = (isset($marStatus[strtolower($maritalstat)])) ? $marStatus[strtolower($maritalstat)] : "";

					//get id of rel
					$caregiverrel = (isset($relList[strtolower($caregiverrel)])) ? $relList[strtolower($caregiverrel)] : "";

					//get id of reprel2
					$reprel1 = (isset($relList[strtolower($reprel1)])) ? $relList[strtolower($reprel1)] : "";

					//get id of reprel2
					$reprel2 = (isset($relList[strtolower($reprel2)])) ? $relList[strtolower($reprel2)] : "";

					//get id of living arrangement
					$livingArrangement = (isset($livingArr[strtolower($livingArrangement)])) ? $livingArr[strtolower($livingArrangement)] : "";

					$duplicate = "";

					//Set Reference_code
					if ($refcode == "NEW") {

						//Check Duplicate
						// $fullname = $lastname . $firstname . $middlename;
						// $duplicate_res = $this->Main->raw("SELECT connum FROM tblgeneral WHERE CONCAT(lastname,firstname,middlename) LIKE '%$fullname%'",true);
						// $duplicate = (!empty($duplicate_res)) ? $duplicate_res->connum : "";  

						// $duplicate_res = $this->Main->raw("SELECT reference_code FROM tblwaitinglist WHERE CONCAT(lastname,firstname,middlename) LIKE '%$fullname%'",true);
						// $duplicate .= (!empty($duplicate_res)) ? $duplicate_res->reference_code : "";  

						$prov = strtoupper(substr($prov_name, 0, 2));
						if ($prov == "AP") {
							$prov = "AY";
						}

						if ($_count[$prov] == 0) {
							$count = $this->Main->raw("SELECT reference_code FROM tblwaitinglist WHERE reference_code LIKE '%$prov%' ORDER BY reference_code DESC LIMIT 1", true);

							if (!empty($count)) {
								$count = $count->reference_code;
								$count = substr(strrchr($count, "_"), 1);
								$count = (int)$count + 1;
							} else {
								$count = 1;
							}
							$_count[$prov] = $count;
						} else {
							$_count[$prov] = $_count[$prov] + 1;
							$count = $_count[$prov];
						}


						$cnt = str_pad($count, 7, '0', STR_PAD_LEFT);
						$referencecode = strtoupper($prov . "_WL_" . $cnt);
					} else {
						$referencecode = $refcode;
					}

					//insert to tblwaitinglist
					$datains_waitlist = array(
						"reference_code"			=> $referencecode,
						"lastname"					=> $lastname,
						"firstname"					=> $firstname,
						"middlename"				=> $middlename,
						"extname"					=> $extname,
						"respondentName"			=> $respondentName,
						"prov_code"					=> $presentprov,
						"mun_code"					=> $presentmuni,
						"bar_code"					=> $presentbara,
						"address"					=> $presentaddress,
						"street"					=> $presentstreet,
						"permanent_prov_code"		=> $permanentprov,
						"permanent_mun_code"		=> $permanentmuni,
						"permanent_bar_code"		=> $permanentbara,
						"permanent_address"			=> $permanentaddress,
						"permanent_street"			=> $permanentstreet,
						"gender"					=> $sex,
						"birthdate"					=> $birthdate,
						"birthplace"				=> $birthplace,
						"hh_size"					=> $hhsize,
						"contact_no"				=> $mobileno,
						"osca_id"					=> $oscaid,
						"marital_status"			=> $marital_id,
						"mothersMaidenName"			=> $mothersMaidenName,
						"livingArrangement"			=> $livingArrangement,
						"nameofCaregiver"			=> $caregiver,
						"relationshipofCaregiver"	=> $caregiverrel,
						"repname2"					=> $repname1,
						"reprel2"					=> $reprel1,
						"repname3"					=> $repname2,
						"reprel3"					=> $reprel2,
						"date_updated"				=> $curdate,
					);

					if ($refcode == "NEW") {
						$datains_waitlist["remarks"] = $remarks;
						$datains_waitlist["priority"] = $priority;
						$datains_waitlist["batch_no"] = $batch_no;
						$datains_waitlist["duplicate"] = $duplicate;
						$datains_waitlist["sent_to_co"] = $sent_to_co;

						$insert_waitlist[] = $datains_waitlist;
						//$waitlist_id = $this->Main->insert("tblwaitinglist", $datains_waitlist, 'lastid')['lastid'];
					} else {
						$update_waitlist[] = $datains_waitlist;
						//$this->Main->update("tblwaitinglist", ["reference_code" => $refcode] , $datains_waitlist);
					}

					//change yes no 1 0
					if ($pensionreceiver == "YES") {
						$pensionreceiver = 1;
					} else {
						$pensionreceiver = 2;
					}
					if ($pension_dswd == "YES") {
						$pension_dswd = 1;
					} else {
						$pension_dswd = NULL;
					}
					if ($pension_gsis == "YES") {
						$pension_gsis = 1;
					} else {
						$pension_gsis = NULL;
					}
					if ($pension_sss == "YES") {
						$pension_sss = 1;
					} else {
						$pension_sss = NULL;
					}
					if ($pension_afpslai == "YES") {
						$pension_afpslai = 1;
					} else {
						$pension_afpslai = NULL;
					}
					if (empty($pension_others)) {
						$pension_others = '';
					}

					if ($income_wages == "YES") {
						$income_wages = 1;
					} else {
						$income_wages = NULL;
					}
					if ($income_entrep == "YES") {
						$income_entrep = 1;
					} else {
						$income_entrep = NULL;
					}
					if ($income_household == "YES") {
						$income_household = 1;
					} else {
						$income_household = NULL;
					}
					if ($income_domestic == "YES") {
						$income_domestic = 1;
					} else {
						$income_domestic = NULL;
					}
					if ($income_international == "YES") {
						$income_international = 1;
					} else {
						$income_international = NULL;
					}
					if ($income_friends == "YES") {
						$income_friends = 1;
					} else {
						$income_friends = NULL;
					}
					if ($income_government == "YES") {
						$income_government = 1;
					} else {
						$income_government = NULL;
					}

					if ($frailty_healthlimit == "YES") {
						$frailty_healthlimit = 1;
					} else {
						$frailty_healthlimit = 0;
					}
					if ($frailty_needregularhelp == "YES") {
						$frailty_needregularhelp = 1;
					} else {
						$frailty_needregularhelp = 0;
					}
					if ($frailty_healthhome == "YES") {
						$frailty_healthhome = 1;
					} else {
						$frailty_healthhome = 0;
					}
					if ($frailty_countonsomeone == "YES") {
						$frailty_countonsomeone = 1;
					} else {
						$frailty_countonsomeone = 0;
					}
					if ($frailty_moveabout == "YES") {
						$frailty_moveabout = 1;
					} else {
						$frailty_moveabout = 0;
					}

					//get id of disability
					$disability_id = (isset($disabilityList[strtolower($reprel2)])) ? $disabilityList[strtolower($disability_id)] : "";

					if ($sp_food == "YES") {
						$sp_food = 1;
					} else {
						$sp_food = 0;
					}
					if ($sp_med == "YES") {
						$sp_med = 1;
					} else {
						$sp_med = 0;
					}
					if ($sp_checkup == "YES") {
						$sp_checkup = 1;
					} else {
						$sp_checkup = 0;
					}
					if ($sp_cloth == "YES") {
						$sp_cloth = 1;
					} else {
						$sp_cloth = 0;
					}
					if ($sp_util == "YES") {
						$sp_util = 1;
					} else {
						$sp_util = 0;
					}
					if ($sp_debt == "YES") {
						$sp_debt = 1;
					} else {
						$sp_debt = 0;
					}
					if ($sp_entrep == "YES") {
						$sp_entrep = 1;
					} else {
						$sp_entrep = 0;
					}

					//insert to tblbufanswers
					$datains_bufans = array(
						"spid"						=> $referencecode,
						"barcode"					=> $presentbara,
						"muncode"					=> $presentmuni,
						"provcode"					=> $presentprov,
						"pension_receiver" 			=> $pensionreceiver,
						"pension_dswd" 				=> $pension_dswd,
						"pension_gsis" 				=> $pension_gsis,
						"pension_sss" 				=> $pension_sss,
						"pension_afpslai" 			=> $pension_afpslai,
						"pension_others" 			=> $pension_others,
						"income_wages" 				=> $income_wages,
						"income_wages_amt" 			=> $income_wages_amt,
						"income_entrep" 			=> $income_entrep,
						"income_entrep_amt" 		=> $income_entrep_amt,
						"income_household" 			=> $income_household,
						"income_household_amt" 		=> $income_household_amt,
						"income_domestic" 			=> $income_domestic,
						"income_domestic_amt" 		=> $income_domestic_amt,
						"income_international" 		=> $income_international,
						"income_international_amt" 	=> $income_international_amt,
						"income_friends"			=> $income_friends,
						"income_friends_amt" 		=> $income_friends_amt,
						"income_government" 		=> $income_government,
						"income_governement_amt" 	=> $income_governement_amt,
						"income_others" 			=> $income_others,
						"income_others_amt" 		=> $income_others_amt,
						"frailty_healthlimit" 		=> $frailty_healthlimit,
						"frailty_needregularhelp"	=> $frailty_needregularhelp,
						"frailty_healthhome" 		=> $frailty_healthhome,
						"frailty_countonsomeone" 	=> $frailty_countonsomeone,
						"frailty_moveabout" 		=> $frailty_moveabout,
						"disability_id" 			=> $disability_id,
						"illness" 					=> $illness,
						"sp_food" 					=> $sp_food,
						"sp_med" 					=> $sp_med,
						"sp_checkup" 				=> $sp_checkup,
						"sp_cloth" 					=> $sp_cloth,
						"sp_util" 					=> $sp_util,
						"sp_debt" 					=> $sp_debt,
						"sp_entrep" 				=> $sp_entrep,
						"sp_others" 				=> $sp_others,
						"worker_name" 				=> $worker_name,
						"date_accomplished" 		=> $date_accomplished,
						"updated_by_uid" 			=> sesdata('id'),
						"date_updated" 				=> $curdate
					);


					if ($refcode == "NEW") {
						//$waitlist_id = $this->Main->insert("tblbufanswers", $datains_bufans);
						$insert_buf[] = $datains_bufans;
					} else {
						//$this->Main->update("tblbufanswers", ["spid" => $refcode] , $datains_bufans);
						$update_buf[] =  $datains_bufans;
					}
				}

				if (!empty($insert_waitlist)) {
					$this->db->insert_batch('tblwaitinglist', $insert_waitlist);
				}
				if (!empty($insert_buf)) {
					$this->db->insert_batch('tblbufanswers', $insert_buf);
				}

				if (!empty($update_waitlist)) {
					$this->db->update_batch('tblwaitinglist', $update_waitlist, 'reference_code');
				}
				if (!empty($update_buf)) {
					$this->db->update_batch('tblbufanswers', $update_buf, 'spid');
				}

				//confirm
				$response['status'] = 'success';
				$response['message'] = 'Success';
				$response['redirect'] = base_url('Waitlist/index');
			}
		} catch (Exception $e) {
			$response['status'] = 'invalid';
			$response['message'] = 'An error occured.';
		}

		unlink('uploads/files/csv/' . str_replace(" ", "_", $_FILES['file']['name']));
		response_json($response);
	}

	public function updateEligibilityStatus()
	{

		date_default_timezone_set("Asia/Manila");
		$curdate = date('Y-m-d H:i:s');

		// if(!$this->input->is_ajax_request()){
		// 	show_404();
		// }

		ini_set('max_execution_time', '60000');
		ini_set('memory_limit', '999M');

		$config['upload_path'] = "uploads/files/csv";
		$config['allowed_types'] = "csv";
		// $config['file_name'] =
		if (!is_dir($config['upload_path'])) {
			mkdir($config['upload_path'], 0777, TRUE);
		}
		$response = false;

		try {

			$this->load->library('upload', $config);

			if (!$this->upload->do_upload('file')) {
				$error = array('error' => $this->upload->display_errors());
				$response['status'] = 'Error';
				$response['message'] =  $error['error'];
				return $response;
			} else {
				//Get File Upload
				$path_folder = '/uploads/files/csv';
				$file_name = str_replace(" ", "_", $_FILES['file']['name']);
				$file_path = server_path . $path_folder . "/" . $file_name;
				$file_data = $this->csvimport->get_array($file_path, FALSE, FALSE, 0);

				$checkRow = 0;
				$num_rows = 0;
				$data = [];

				foreach ($file_data as $row) {
					$num_rows++;

					$referencecode      = $row["REFERENCE CODE"];

					$data[] = array(
						'reference_code' => $row["REFERENCE CODE"],
						'priority' => $row["eligibility"],
						'batch_no' => $row["batch_no"],
						'remarks' => $row["remarks"],
						'duplicate' => $row["duplicate"],
						'archived' => $row["archived"],
						'sent_to_co' => 1
					);
				}
				$this->db->update_batch('tblwaitinglist', $data, 'reference_code');

				//confirm
				$response['status'] = 'success';
				$response['message'] = "Success Update $num_rows Rows";
				$response['redirect'] = base_url('Waitlist/index');
			}
		} catch (Exception $e) {
			$response['status'] = 'invalid';
			$response['message'] = 'An error occured.';
		}

		unlink('uploads/files/csv/' . str_replace(" ", "_", $_FILES['file']['name']));
		response_json($response);
	}

	
	public function waitlisttemplate(){
		// ignore_user_abort(true);

		$prov_code = $this->input->get('prov_code');
		$mun_code = $this->input->get('mun_code');
		$barlist="";

		if(!empty($prov_code) && $prov_code!="" && $prov_code!="null" && $mun_code=="null"){
			$prov_name = getLocation("p.prov_code='$prov_code'","row")->prov_name;
			$provcolval = $prov_name."/".$prov_code;
			$muncolval = "";
			$filename = $prov_name."_WAITLIST_TEMPLATE";
			$locationcondi = "p.prov_code='$prov_code'";
		}else if(!empty($mun_code) && $mun_code!="null"){
			$getLoc = getLocation("m.mun_code='$mun_code'","row");
			$provcolval = $getLoc->prov_name."/".$prov_code;
			$muncolval =  $getLoc->mun_name."/".$mun_code;
			$filename = $getLoc->mun_name."_WAITLIST_TEMPLATE";
			$locationcondi = "m.mun_code='$mun_code'";
		}else{ 
			$provcolval=$muncolval=""; $filename="WAITLIST_TEMPLATE";
			$locationcondi = "b.bar_code<>''"; 
		}

		$object = new Spreadsheet();
		$object->createSheet(0);
		$object->createSheet(1);
		
		//start of sheet 2 psgc
		$object->setActiveSheetIndex(1);
		$activeSheet1 = $object->getActiveSheet();
		$activeSheet1->setTitle("LIBRARIES");

		$activeSheet1->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
		$activeSheet1->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
			
		$margineset = $activeSheet1->getPageMargins();
		$margineset->setTop(0.25);
		$margineset->setBottom(0.25);
		$margineset->setRight(0.25);
		$margineset->setLeft(0.25);

		$excel_row=1;
		
		$activeSheet1->setCellValue("A".$excel_row,"CAR [CORDILLERA ADMINISTRATIVE REGION]/140000000");
		$excel_row+=2;
		$activeSheet1->setCellValue("A".$excel_row,"PROVINCE");
		$activeSheet1->setCellValue("B".$excel_row,"MUNICIPALITY");
		$activeSheet1->setCellValue("C".$excel_row,"BARANGAY");
		$excel_row++;
		$locations = getLocation($locationcondi);
		foreach($locations as $loc){
			$activeSheet1->setCellValue("A".$excel_row,$loc->prov_name."/".$loc->prov_code);
			$activeSheet1->setCellValue("B".$excel_row,$loc->mun_name."/".$loc->mun_code);
			$activeSheet1->setCellValue("C".$excel_row,$loc->bar_name."/".$loc->bar_code);
			$countbar = $excel_row;
			$excel_row++;
		}
		$activeSheet1->getColumnDimension('A')->setAutoSize(true);
		$activeSheet1->getColumnDimension('B')->setAutoSize(true);
		$activeSheet1->getColumnDimension('C')->setAutoSize(true);

		$excel_row=1;
		$activeSheet1->setCellValue("D".$excel_row , "Living Arrangement");
		$excel_row++;
		$livinglist = array('Living alone', 'Living with spouse only', 'Living with a child (including adopted children), child-in-law or grandchild', 'Living with another relative (other than a spouse or child/grandchild)', 'Living with unrelated people only, apart from the older persons spouse');
		foreach($livinglist as $ll){
			$activeSheet1->setCellValue("D".$excel_row , $ll); $countlivs=$excel_row++;
		}

		//end of sheet 2

		$object->setActiveSheetIndex(0);
		$activeSheet = $object->getActiveSheet();
		$activeSheet->setTitle("Waitlist");

		$activeSheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
		$activeSheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
			
		$margineset = $activeSheet->getPageMargins();
		$margineset->setTop(0.25);
		$margineset->setBottom(0.25);
		$margineset->setRight(0.25);
		$margineset->setLeft(0.25);

		$headerstyleborder = 
		[	'alignment' => [
				'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER_CONTINUOUS,
				'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
			],
			'font'  => [
				'size'  => 10,
				'name' => 'Arial'
			],
			'borders' => [
				'allBorders' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
		]]];
		$border = 
		[	'borders' => [
				'allBorders' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
		]]];

		$excel_row=1;

		$activeSheet->mergeCells('B'.$excel_row.':AO'.$excel_row)->setCellValue('B'.$excel_row,'PERSONAL INFORMATION');
		$activeSheet->mergeCells('AP'.$excel_row.':AU'.$excel_row)->setCellValue('AP'.$excel_row,'TYPE OF PENSION RECEIVED IN THE PAST SIX MONTHS');
		$activeSheet->mergeCells('AV'.$excel_row.':BT'.$excel_row)->setCellValue('AV'.$excel_row,'WHAT ARE YOUR SOURCES OF INCOME AND FINANCIAL SUPPORT IN THE PAST 6 MONTHS');
		$activeSheet->mergeCells('BU'.$excel_row.':CA'.$excel_row)->setCellValue('BU'.$excel_row,'FRAILTY QUESTIONS');
		$activeSheet->mergeCells('CB'.$excel_row.':CC'.$excel_row)->setCellValue('CB'.$excel_row,'DISABILITY/ ILLNESS');
		$activeSheet->mergeCells('CD'.$excel_row.':CK'.$excel_row)->setCellValue('CD'.$excel_row,'UTILIZATION OF SOCIAL PENSION');
		$activeSheet->mergeCells('CL'.$excel_row.':CM'.$excel_row)->setCellValue('CL'.$excel_row,'ASSESSMENT');
		$activeSheet->mergeCells('CP'.$excel_row.':CS'.$excel_row)->setCellValue('CP'.$excel_row,'OTHER REPRESENTATIVES');

		$activeSheet->getColumnDimension('A')->setWidth(15); //date/time
		$activeSheet->getColumnDimension('B')->setWidth(10); //date/time
		$activeSheet->getColumnDimension('C')->setWidth(18); //refcode
		$activeSheet->getColumnDimension('D')->setWidth(18); //lname
		$activeSheet->getColumnDimension('E')->setWidth(18); //fname
		$activeSheet->getColumnDimension('F')->setWidth(18); //mname
		$activeSheet->getColumnDimension('G')->setWidth(10); //extname
		$activeSheet->getColumnDimension('H')->setWidth(10); //ID NUMBER
		$activeSheet->getColumnDimension('I')->setWidth(10); //ID NO. TYPE
		$activeSheet->getColumnDimension('J')->setWidth(10); //grantee yes/no
		$activeSheet->getColumnDimension('K')->setWidth(15); //name of respondent
			
		$activeSheet->getColumnDimension('L')->setWidth(13); //PERMANENT (REGION)
		$activeSheet->getColumnDimension('M')->setWidth(13); //PERMANENT (PROVINCE)
		$activeSheet->getColumnDimension('N')->setWidth(15); //PERMANENT (CITY/ MUNICIPALITY)
		$activeSheet->getColumnDimension('O')->setWidth(20); //PERMANENT (BARANGAY)
		$activeSheet->getColumnDimension('P')->setWidth(13); //PERMANENT (HOUSE NO./ZONE/PUROK/SITIO)
		$activeSheet->getColumnDimension('Q')->setWidth(13); //PERMANENT (STREET)
		
		$activeSheet->getColumnDimension('R')->setWidth(13); //PRESENT (REGION)
		$activeSheet->getColumnDimension('S')->setWidth(13); //PRESENT (PROVINCE)
		$activeSheet->getColumnDimension('T')->setWidth(15); //PRESENT (CITY/ MUNICIPALITY)
		$activeSheet->getColumnDimension('U')->setWidth(20); //PRESENT (BARANGAY)
		$activeSheet->getColumnDimension('V')->setWidth(13); //PRESENT (HOUSE NO./ZONE/PUROK/SITIO)
		$activeSheet->getColumnDimension('W')->setWidth(13); //PRESENT (STREET)
		
		$activeSheet->getColumnDimension('X')->setWidth(8); //SEX
		$activeSheet->getColumnDimension('Y')->setWidth(11); //BIRTHDATE
		$activeSheet->getColumnDimension('Z')->setWidth(6); //AGE
		$activeSheet->getColumnDimension('AA')->setWidth(11); //PLACE OF BIRTH
		$activeSheet->getColumnDimension('AB')->setWidth(15); //NAME OF CAREGIVER
		$activeSheet->getColumnDimension('AC')->setWidth(15); //RELATIONSHIP
		$column = 'AD'; for($i = 0; $i < 43; $i++) { $activeSheet->getColumnDimension($column)->setWidth(15); $column++; }
		$activeSheet->getColumnDimension('AM')->setWidth(15); //MOTHERS MAIDEN NAME
		$activeSheet->getColumnDimension('BU')->setWidth(70); //WHO ARE YOU LIVING WITH
		$column = 'BV'; for($i = 0; $i < 7; $i++) { $activeSheet->getColumnDimension($column)->setWidth(20); $column++; }
		$column = 'CB'; for($i = 0; $i < 18; $i++) { $activeSheet->getColumnDimension($column)->setWidth(15); $column++; }
		//colors
		$excel_row++; $prevrow = $excel_row-1;
		$activeSheet->getStyle('A'.$prevrow.':A'.$excel_row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('F7DA88'); //orange
		$activeSheet->getStyle('A'.$prevrow.':AO'.$excel_row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('179C1C'); //green
		$activeSheet->getStyle('AF'.$excel_row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('A2A2A2'); //gray
		$activeSheet->getStyle('AH'.$excel_row.':AL'.$excel_row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('A2A2A2'); //gray
		$activeSheet->getStyle('AN'.$excel_row.':AO'.$excel_row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('A2A2A2'); //gray
		$activeSheet->getStyle('AP'.$prevrow.':AU'.$excel_row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('1E388E'); //dark blue
		$activeSheet->getStyle('AV'.$prevrow.':BT'.$excel_row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('405DBA'); //blue
		$activeSheet->getStyle('BU'.$prevrow.':CK'.$excel_row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('6684E8'); //light blue
		$activeSheet->getStyle('CB'.$prevrow.':CC'.$excel_row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('C400FF'); //violet
		$activeSheet->getStyle('CD'.$prevrow.':CM'.$excel_row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('C4D5F2'); //light light blue
		$activeSheet->getStyle('CP'.$prevrow.':CS'.$excel_row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('179C1C'); //green

		$table_columns = array("DATE/TIME", "SENIOR CITIZEN ID NO./OSCA NO.", "REFERENCE CODE", "LAST NAME / QUALIFIER", "FIRST NAME", "MIDDLE NAME", "EXT NAME",
		"ID NUMBER", "ID NO. TYPE", "GRANTEE (YES/NO)", "NAME OF RESPONDENT", 
		"PERMANENT (REGION)", "PERMANENT (PROVINCE)",  "PERMANENT (CITY/ MUNICIPALITY)", "PERMANENT (BARANGAY)", "PERMANENT (HOUSE NO./ZONE/PUROK/SITIO)", "PERMANENT (STREET)", 
		"PRESENT (REGION)", "PRESENT (PROVINCE)", "PRESENT (CITY/ MUNICIPALITY)", "PRESENT (BARANGAY)", "PRESENT (HOUSE NO./ZONE/PUROK/SITIO)","PRESENT (STREET)", 
		"SEX", "BIRTHDATE (MM/DD/CCYY)", "AGE", "PLACE OF BIRTH", "NAME OF CARE GIVER", "RELATIONSHIP", "MARITAL STATUS", "HOUSEHOLD SIZE", "TIN", "MOBILE NO.", 
		"NATIONALITY", "PROFESSION", "SOURCE OF FUNDS", "GROSS SALARY", "EMAIL", "MOTHER'S MAIDEN NAME", "EMBOSS NAME", "LBP Bank #", 
		"RECEIVE OF PENSION (YES/NO)", "DSWD SOCIAL PENSION", "GSIS", "SSS", "AFPSLAI", "OTHERS1", 
		"WAGES/SALARIES", "IS IT REGULAR (YES/NO)1", "AMOUNT OF INCOME1", 
		"PROFIT FROM ENTERPRENEURIAL ACTIVITIES", "IS IT REGULAR (YES/NO)2", "AMOUNT OF INCOME2", 
		"HOUSEHOLD FAMILY MEMBERS/RELATIVES", "IS IT REGULAR (YES/NO)3", "AMOUNT OF INCOME3", 
		"DOMESTIC FAMILY MEMBERS/RELATIVES", "IS IT REGULAR (YES/NO)4", "AMOUNT OF INCOME4", 
		"INTERNATIONAL FAMILY MEMBERS/RELATIVES", "IS IT REGULAR (YES/NO)5", "AMOUNT OF INCOME5", 
		"FRIENDS/NEIGHBORS", "IS IT REGULAR (YES/NO)6", "AMOUNT OF INCOME6", 
		"TRANSFERS FROM THE GOVERNMENT", "IS IT REGULAR (YES/NO)7", "AMOUNT OF INCOME7", 
		"OTHERS8", "IS IT REGULAR (YES/NO)8", "AMOUNT OF INCOME8", "TOTAL AMOUNT", 
		"WHO ARE YOU LIVING WITH?", 
		"OLDER THAN 85 YEARS? (YES/NO)", 
		"DO YOU HAVE ANY HEALTH PROBLEMS THAT REQUIRE YOU TO LIMIT YOUR ACTIVITIES (YES/NO)", 
		"DO YOU NEED SOMEONE TO HELP YOU ON A REGULAR BASIS? (YES/NO)", 
		"DO YOU HAVE ANY HEALTH PROBLEMS THAT REQUIRE YOU TO STAY AT HOME? (YES/NO)", 
		"IF YOU NEED HELP CAN YOU COUNT ON SOMEONE CLOSE TO YOU? (YES/NO)",	
		"DO YOU REGULARLY USE A STICK/WALKER/WHEELCHAIR TO MOVE ABOUT? (YES/NO)",
		"DISABILITY", "ILLNESS/DISEASE", "FOOD", "MEDICINES AND VITAMINS", "HEALTH CHECK-UP AND OTHER OSPITAL/MEDICAL SERVICES", "CLOTHING", "UTILITIES", "DEBT PAYMENT", "LIVELIHOOD/ENTERPRENEURIAL ACITIVIES", "OTHERS2", 
		"NAME OF WORKER", "DATE ACCOMPLISHED", "REMARKS", "DATE_ENCODED",
		"REPNAME2", "REPREL2", "REPNAME3", "REPREL3");
			
		$hs = "A";
		foreach ($table_columns as $tv) { 
			$activeSheet->setCellValue($hs.$excel_row,$tv); $hs++; 
			$activeSheet->getStyle('A'.$prevrow.':CS'.$excel_row)->applyFromArray($headerstyleborder);
               $activeSheet->getStyle('A'.$prevrow.':CS'.$excel_row)->getFont()->setBold( true );
               $activeSheet->getStyle('A'.$prevrow.':CS'.$excel_row)->getAlignment()->setWrapText(true);
		}

		$excel_row++;
		// $yesnolist = trim(implode(',', ['YES', 'NO']));
		$yesnolist = "YES,NO";
		$sexlist = "Female,Male";

		$rellist = $this->Main->raw("SELECT relname FROM tblrelationships ORDER BY relname ASC");
		if (!empty($rellist)) { $qry=""; foreach ($rellist as $lkey => $lval) { $qry .= $lval->relname.","; } $rellist = substr(trim($qry), 0, -1); }

		$marilist = $this->Main->raw("SELECT name FROM tblmaritalstatus ORDER BY name ASC");
		if (!empty($marilist)) {$qry=""; foreach ($marilist as $lkey => $lval) { $qry .= $lval->name.","; } $marilist = substr(trim($qry), 0, -1); }

		$disabilitylist = $this->Main->raw("SELECT name FROM tbldisability ORDER BY name ASC");
		if (!empty($disabilitylist)) {$qry=""; foreach ($disabilitylist as $lkey => $lval) { $qry .= $lval->name.","; } $disabilitylist = substr(trim($qry), 0, -1); }

		$number=1;
		for($rownum=$excel_row; $rownum<=20; $rownum++){

			//set value for psgc
			$activeSheet->setCellValue("A".$rownum,$number);
			$activeSheet->setCellValue("L".$rownum,"CAR [CORDILLERA ADMINISTRATIVE REGION]/140000000");
			$activeSheet->setCellValue("M".$rownum,$provcolval);
			$activeSheet->setCellValue("N".$rownum,$muncolval);
			$activeSheet->setCellValue("R".$rownum,"CAR [CORDILLERA ADMINISTRATIVE REGION]/140000000");
			$activeSheet->setCellValue("S".$rownum,$provcolval);
			$activeSheet->setCellValue("T".$rownum,$muncolval);

			//barangay dropdown
			if(!empty($muncolval)){
				$activeSheet->getCell('O'.$rownum)->getDataValidation()->setType(PHPExcel_Cell_DataValidation::TYPE_LIST)->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION)->setAllowBlank(false)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list')->setFormula1('LIBRARIES!$C$4:$C$'.$countbar);
				$activeSheet->getCell('U'.$rownum)->getDataValidation()->setType(PHPExcel_Cell_DataValidation::TYPE_LIST)->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION)->setAllowBlank(false)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list')->setFormula1('LIBRARIES!$C$4:$C$'.$countbar);
			}
			//grantee
			$activeSheet->getCell('J'.$rownum)->getDataValidation()->setType(PHPExcel_Cell_DataValidation::TYPE_LIST)->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION)->setAllowBlank(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list')->setFormula1(sprintf('"%s"', $yesnolist));
			//sex
			$activeSheet->getCell('X'.$rownum)->getDataValidation()->setType(PHPExcel_Cell_DataValidation::TYPE_LIST)->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION)->setAllowBlank(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list')->setFormula1(sprintf('"%s"', "Male,Female"));
			//relationships
			$activeSheet->getCell('AC'.$rownum)->getDataValidation()->setType(PHPExcel_Cell_DataValidation::TYPE_LIST)->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION)->setAllowBlank(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list')->setFormula1(sprintf('"%s"', $rellist));
			$activeSheet->getCell('CQ'.$rownum)->getDataValidation()->setType(PHPExcel_Cell_DataValidation::TYPE_LIST)->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION)->setAllowBlank(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list')->setFormula1(sprintf('"%s"', $rellist));
			$activeSheet->getCell('CS'.$rownum)->getDataValidation()->setType(PHPExcel_Cell_DataValidation::TYPE_LIST)->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION)->setAllowBlank(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list')->setFormula1(sprintf('"%s"', $rellist));
			//marital status
			$activeSheet->getCell('AD'.$rownum)->getDataValidation()->setType(PHPExcel_Cell_DataValidation::TYPE_LIST)->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION)->setAllowBlank(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list')->setFormula1(sprintf('"%s"', $marilist));
			//pensionsreceived
			$activeSheet->getCell('AP'.$rownum)->getDataValidation()->setType(PHPExcel_Cell_DataValidation::TYPE_LIST)->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION)->setAllowBlank(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list')->setFormula1(sprintf('"%s"', $yesnolist));
			$activeSheet->getCell('AQ'.$rownum)->getDataValidation()->setType(PHPExcel_Cell_DataValidation::TYPE_LIST)->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION)->setAllowBlank(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list')->setFormula1(sprintf('"%s"', "YES"));
			$activeSheet->getCell('AR'.$rownum)->getDataValidation()->setType(PHPExcel_Cell_DataValidation::TYPE_LIST)->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION)->setAllowBlank(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list')->setFormula1(sprintf('"%s"', "YES"));
			$activeSheet->getCell('AS'.$rownum)->getDataValidation()->setType(PHPExcel_Cell_DataValidation::TYPE_LIST)->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION)->setAllowBlank(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list')->setFormula1(sprintf('"%s"', "YES"));
			$activeSheet->getCell('AT'.$rownum)->getDataValidation()->setType(PHPExcel_Cell_DataValidation::TYPE_LIST)->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION)->setAllowBlank(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list')->setFormula1(sprintf('"%s"', "YES"));
			//sources of income
			$activeSheet->getCell('AV'.$rownum)->getDataValidation()->setType(PHPExcel_Cell_DataValidation::TYPE_LIST)->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION)->setAllowBlank(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list')->setFormula1(sprintf('"%s"', $yesnolist));
			$activeSheet->getCell('AW'.$rownum)->getDataValidation()->setType(PHPExcel_Cell_DataValidation::TYPE_LIST)->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION)->setAllowBlank(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list')->setFormula1(sprintf('"%s"', $yesnolist));
			$activeSheet->getCell('AY'.$rownum)->getDataValidation()->setType(PHPExcel_Cell_DataValidation::TYPE_LIST)->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION)->setAllowBlank(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list')->setFormula1(sprintf('"%s"', $yesnolist));
			$activeSheet->getCell('AZ'.$rownum)->getDataValidation()->setType(PHPExcel_Cell_DataValidation::TYPE_LIST)->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION)->setAllowBlank(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list')->setFormula1(sprintf('"%s"', $yesnolist));
			$activeSheet->getCell('BB'.$rownum)->getDataValidation()->setType(PHPExcel_Cell_DataValidation::TYPE_LIST)->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION)->setAllowBlank(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list')->setFormula1(sprintf('"%s"', $yesnolist));
			$activeSheet->getCell('BC'.$rownum)->getDataValidation()->setType(PHPExcel_Cell_DataValidation::TYPE_LIST)->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION)->setAllowBlank(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list')->setFormula1(sprintf('"%s"', $yesnolist));
			$activeSheet->getCell('BE'.$rownum)->getDataValidation()->setType(PHPExcel_Cell_DataValidation::TYPE_LIST)->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION)->setAllowBlank(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list')->setFormula1(sprintf('"%s"', $yesnolist));
			$activeSheet->getCell('BF'.$rownum)->getDataValidation()->setType(PHPExcel_Cell_DataValidation::TYPE_LIST)->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION)->setAllowBlank(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list')->setFormula1(sprintf('"%s"', $yesnolist));
			$activeSheet->getCell('BH'.$rownum)->getDataValidation()->setType(PHPExcel_Cell_DataValidation::TYPE_LIST)->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION)->setAllowBlank(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list')->setFormula1(sprintf('"%s"', $yesnolist));
			$activeSheet->getCell('BI'.$rownum)->getDataValidation()->setType(PHPExcel_Cell_DataValidation::TYPE_LIST)->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION)->setAllowBlank(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list')->setFormula1(sprintf('"%s"', $yesnolist));
			$activeSheet->getCell('BK'.$rownum)->getDataValidation()->setType(PHPExcel_Cell_DataValidation::TYPE_LIST)->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION)->setAllowBlank(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list')->setFormula1(sprintf('"%s"', $yesnolist));
			$activeSheet->getCell('BL'.$rownum)->getDataValidation()->setType(PHPExcel_Cell_DataValidation::TYPE_LIST)->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION)->setAllowBlank(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list')->setFormula1(sprintf('"%s"', $yesnolist));
			$activeSheet->getCell('BN'.$rownum)->getDataValidation()->setType(PHPExcel_Cell_DataValidation::TYPE_LIST)->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION)->setAllowBlank(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list')->setFormula1(sprintf('"%s"', $yesnolist));
			$activeSheet->getCell('BO'.$rownum)->getDataValidation()->setType(PHPExcel_Cell_DataValidation::TYPE_LIST)->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION)->setAllowBlank(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list')->setFormula1(sprintf('"%s"', $yesnolist));
			$activeSheet->getCell('BR'.$rownum)->getDataValidation()->setType(PHPExcel_Cell_DataValidation::TYPE_LIST)->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION)->setAllowBlank(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list')->setFormula1(sprintf('"%s"', $yesnolist));
			//living arrangement
			$activeSheet->getCell('BU'.$rownum)->getDataValidation()->setType(PHPExcel_Cell_DataValidation::TYPE_LIST)->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION)->setAllowBlank(false)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list')->setFormula1('LIBRARIES!$D$2:$D$'.$countlivs);
			//frailtyquestions
			$activeSheet->getCell('BV'.$rownum)->getDataValidation()->setType(PHPExcel_Cell_DataValidation::TYPE_LIST)->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION)->setAllowBlank(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list')->setFormula1(sprintf('"%s"', $yesnolist));
			$activeSheet->getCell('BW'.$rownum)->getDataValidation()->setType(PHPExcel_Cell_DataValidation::TYPE_LIST)->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION)->setAllowBlank(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list')->setFormula1(sprintf('"%s"', $yesnolist));
			$activeSheet->getCell('BX'.$rownum)->getDataValidation()->setType(PHPExcel_Cell_DataValidation::TYPE_LIST)->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION)->setAllowBlank(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list')->setFormula1(sprintf('"%s"', $yesnolist));
			$activeSheet->getCell('BY'.$rownum)->getDataValidation()->setType(PHPExcel_Cell_DataValidation::TYPE_LIST)->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION)->setAllowBlank(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list')->setFormula1(sprintf('"%s"', $yesnolist));
			$activeSheet->getCell('BZ'.$rownum)->getDataValidation()->setType(PHPExcel_Cell_DataValidation::TYPE_LIST)->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION)->setAllowBlank(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list')->setFormula1(sprintf('"%s"', $yesnolist));
			$activeSheet->getCell('CA'.$rownum)->getDataValidation()->setType(PHPExcel_Cell_DataValidation::TYPE_LIST)->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION)->setAllowBlank(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list')->setFormula1(sprintf('"%s"', $yesnolist));
			//disability
			$activeSheet->getCell('CB'.$rownum)->getDataValidation()->setType(PHPExcel_Cell_DataValidation::TYPE_LIST)->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION)->setAllowBlank(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list')->setFormula1(sprintf('"%s"', $disabilitylist));
			//sp util
			$activeSheet->getCell('CD'.$rownum)->getDataValidation()->setType(PHPExcel_Cell_DataValidation::TYPE_LIST)->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION)->setAllowBlank(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list')->setFormula1(sprintf('"%s"', "Yes"));
			$activeSheet->getCell('CE'.$rownum)->getDataValidation()->setType(PHPExcel_Cell_DataValidation::TYPE_LIST)->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION)->setAllowBlank(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list')->setFormula1(sprintf('"%s"', "Yes"));
			$activeSheet->getCell('CF'.$rownum)->getDataValidation()->setType(PHPExcel_Cell_DataValidation::TYPE_LIST)->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION)->setAllowBlank(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list')->setFormula1(sprintf('"%s"', "Yes"));
			$activeSheet->getCell('CG'.$rownum)->getDataValidation()->setType(PHPExcel_Cell_DataValidation::TYPE_LIST)->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION)->setAllowBlank(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list')->setFormula1(sprintf('"%s"', "Yes"));
			$activeSheet->getCell('CH'.$rownum)->getDataValidation()->setType(PHPExcel_Cell_DataValidation::TYPE_LIST)->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION)->setAllowBlank(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list')->setFormula1(sprintf('"%s"', "Yes"));
			$activeSheet->getCell('CI'.$rownum)->getDataValidation()->setType(PHPExcel_Cell_DataValidation::TYPE_LIST)->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION)->setAllowBlank(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list')->setFormula1(sprintf('"%s"', "Yes"));
			$activeSheet->getCell('CJ'.$rownum)->getDataValidation()->setType(PHPExcel_Cell_DataValidation::TYPE_LIST)->setErrorStyle(PHPExcel_Cell_DataValidation::STYLE_INFORMATION)->setAllowBlank(true)->setShowDropDown(true)->setErrorTitle('Input error')->setError('Value is not in list')->setFormula1(sprintf('"%s"', "Yes"));
			$activeSheet->getStyle('A'.$prevrow.':CS'.$rownum)->applyFromArray($border);

			$number++;
		}
		
		//end of sheet 1

		$object->setActiveSheetIndex(0);
		$activeSheet->setSelectedCell('A1');

		$activeSheet->setShowGridlines(true);
		$filename = $filename.".xlsx";

		$writer = new Xlsx($object);
		$writer->setPreCalculateFormulas(true);

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename); 
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
		userLogs(sesdata('id') , sesdata('fullname') , "EXPORT", "Export Blank Waitlist Template");
		
	}

	public function blankBUF(){
		// $this->load->view('buf/blankbuf',"");

		$this->load->view('buf/newblankbuf',"");
		$html = $this->output->get_output();
		$this->load->library('pdf');
		$pdf = $this->dompdf->loadHtml($html);
		$this->dompdf->setPaper('A4', 'portrait');
		$this->dompdf->render();
		$filename = "blank_BUF.pdf";
		$file = $this->dompdf->stream($filename, array("Attachment"=>0));
		userLogs(sesdata('id') , sesdata('fullname') , "EXPORT", "Generate Blank Beneficiary Update Form");
	}

	//END IMPORT WAITLIST

	//EXPORT OR DOWNLOAD WAITLIST

	public function downloadWaitlist()
	{

		ini_set('memory_limit', '999M');
		ignore_user_abort(true);
		$count_data = 0;
		$data = $condition = [];
		$where = [];
		$where["archived"] = 0;
		$fileN = "";
		$title = "";

		$municipalityname = "ALL";
		$provincename = "ALL";

		$prov_code = $this->input->get('prov_code');
		$mun_code = $this->input->get('mun_code');
		$bar_code = $this->input->get('bar_code');
		$status = $this->input->get('status');
		
		$birth_from = $this->input->get('birth_from');
		$birth_to = $this->input->get('birth_to');

		//Start Get Libraries
		$provinces = $this->Main->get_all_provinces();
		$prov_name_list = array_column($provinces, 'prov_name', 'prov_code');
		$municipalities = $this->Main->get_all_municipalities();
		$mun_name_list = array_column($municipalities, 'mun_name', 'mun_code');
		$barangays = $this->Main->getBarangays();
		$bar_name_list = array_column($barangays, 'bar_name', 'bar_code');
		//END Get 

		if (!empty($birth_from) && !empty($birth_to)) {
			$where['birthdate <='] = $birth_from;
			$where['birthdate >='] = $birth_to;
		}else if(!empty($birth_from) && empty($birth_to)){ 
			$where['birthdate >='] = $birth_from;
		}else if(empty($birth_from) && !empty($birth_to)){
			$where['birthdate <='] = $birth_to;
		}

		if (!empty($prov_code)) {
			$where['prov_code'] = $prov_code;
			$provincename = (isset($prov_name_list[$prov_code])) ? $prov_name_list[$prov_code] : "";
		}
		if (!empty($mun_code)) {
			$where['mun_code'] = $mun_code;
			$municipalityname = (isset($mun_name_list[$mun_code])) ? $mun_name_list[$mun_code] : "";
		}
		if (!empty($bar_code)) {
			$where['bar_code'] = $bar_code;
		}
		if ($status != "") {
			if ($status == "3") {
				$where["priority"] = "0";
				$where["sent_to_co"] = "1";
			} elseif ($status == "4") {
				$where["priority"] = "0";
				$where["sent_to_co"] = "0";
			} else {
				$where["priority"] = $status;
			}

			if ($status == "1") {
				$fileN = "ELIGIBLE_";
				$title = "ELIGIBLE";
			} elseif ($status == "2") {
				$fileN = "NOT_ELIGIBLE_";
				$title = "NOT ELIGIBLE";
			} else {
				$fileN = "WAITING_FOR_ELIGIBILITY_";
				$title = "WAITING FOR ELIGIBILITY";
			}
		}

		$select = "reference_code,lastname, firstname, middlename, extname, prov_code, mun_code, bar_code, priority, birthdate, remarks, duplicate, batch_no";

		$queries = array(
			"select"	=> $select,
			"table"		=> "tblwaitinglist",
			"condition"	=> $where,
			'order'     => array("col" => "lastname", "order_by" => "ASC"),
			"limit"		=> "",
			"offset"	=> "",
			"type"		=> ""
		);
		$waitlist = $this->Main->select($queries);
		$count_datas = count($waitlist);

		if ($count_datas > 0) {

			$object = new Spreadsheet();
			$object->createSheet(0);
			$object->setActiveSheetIndex(0);
			$activeSheet = $object->getActiveSheet();
			$activeSheet->setTitle("WAITLIST ($municipalityname)");

			$activeSheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
			$activeSheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_FOLIO);

			$margineset = $activeSheet->getPageMargins();
			$margineset->setTop(0.25);
			$margineset->setBottom(0.25);
			$margineset->setRight(0.25);
			$margineset->setLeft(0.25);

			//style settings
			$headerstyle =
				[
					'alignment' => [
						'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER_CONTINUOUS,
						'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
					],
					'font'  => [
						'size'  => 11,
						'name' => 'Arial'
					]
				];
			$headerstyleborder =
				[
					'alignment' => [
						'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER_CONTINUOUS,
						'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
					],
					'font'  => [
						'size'  => 11,
						'name' => 'Arial'
					],
					'borders' => [
						'allBorders' => [
							'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
						]
					]
				];
			$textleft =
				[
					'alignment' => [
						'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
						'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
					],
					'font'  => [
						'size'  => 11,
						'name' => 'Arial'
					]
				];
			$textcenter =
				[
					'alignment' => [
						'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
						'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
					],
					'font'  => [
						'size'  => 11,
						'name' => 'Arial'
					]
				];
			$border =
				[
					'borders' => [
						'allBorders' => [
							'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
						]
					]
				];


			//data in sheet
			for ($excel_row = 1; $excel_row <= 3; $excel_row++) {
				$activeSheet->getStyle('A' . $excel_row)->getFont()->setBold(true);
			}
			$activeSheet->mergeCells('A1:K1')->setCellValue('A1', 'PROVINCE OF ' . mb_strtoupper($provincename));
			$activeSheet->mergeCells('A2:K2')->setCellValue('A2', 'MUNICIPALITY OF ' . mb_strtoupper($municipalityname));
			$asOf = date("m/d/yy");
			$activeSheet->mergeCells('A3:K3')->setCellValue('A3', "$title WAITLIST AS OF $asOf");
			$activeSheet->getStyle('A1:K3')->applyFromArray($headerstyle);
			$excel_row++;

			$activeSheet->getColumnDimension('A')->setWidth(6); //no
			$activeSheet->getColumnDimension('B')->setWidth(22); //spid
			$activeSheet->getColumnDimension('C')->setWidth(35); //name
			$activeSheet->getColumnDimension('D')->setWidth(25); //barangay
			$activeSheet->getColumnDimension('E')->setWidth(25); //municipality
			$activeSheet->getColumnDimension('F')->setWidth(20); //province
			$activeSheet->getColumnDimension('G')->setWidth(20); //birthdate
			$activeSheet->getColumnDimension('H')->setWidth(22); //Eligibility Status
			$activeSheet->getColumnDimension('I')->setWidth(30); //remarks
			$activeSheet->getColumnDimension('J')->setWidth(22); //duplicate
			$activeSheet->getColumnDimension('K')->setWidth(10); //batch_no

			$table_columns = array("NO.", "REFERENCE #", "NAME", "BARANGAY", "MUNICIPALITY", "PROVINCE", "BIRTHDATE", "ELIGIBILITY STATUS", "REMARKS", "DUPLICATE", "BATCH NO.");
			$hs = "A";
			foreach ($table_columns as $tv) {
				$activeSheet->setCellValue($hs . $excel_row, $tv);
				$hs++;
				$activeSheet->getStyle('A' . $excel_row . ':K' . $excel_row)->applyFromArray($headerstyleborder);
				$activeSheet->getStyle('A' . $excel_row . ':K' . $excel_row)->getFont()->setBold(true);
				$activeSheet->getStyle('A' . $excel_row . ':K' . $excel_row)->getAlignment()->setWrapText(true);
			}
			$excel_row++;
			$number = 1;
			foreach ($waitlist as $wl) {
				$fullname = strtoupper($wl->lastname . ", " . $wl->firstname . " " . $wl->middlename . " " . $wl->extname);
				$prov_name = (isset($prov_name_list[$wl->prov_code])) ? $prov_name_list[$wl->prov_code] : "";
				$mun_name = (isset($mun_name_list[$wl->mun_code])) ? $mun_name_list[$wl->mun_code] : "";
				$bar_name = (isset($bar_name_list[$wl->bar_code])) ? $bar_name_list[$wl->bar_code] : "";

				$stat = "";

				if ((int)$wl->priority == 0) {
					$stat = "WAITING FOR ELIGIBILITY";
				} else if ((int)$wl->priority == 1) {
					$stat = "ELIGIBILE";
				} else if ((int)$wl->priority == 2) {
					$stat = "NOT ELIGIBILE";
				}

				$activeSheet->setCellValue("A" . $excel_row, (string)$number);
				$activeSheet->setCellValue("B" . $excel_row, mb_strtoupper($wl->reference_code));
				$activeSheet->setCellValue("C" . $excel_row, mb_strtoupper($fullname));
				$activeSheet->setCellValue("D" . $excel_row, mb_strtoupper($bar_name));
				$activeSheet->setCellValue("E" . $excel_row, mb_strtoupper($mun_name));
				$activeSheet->setCellValue("F" . $excel_row, mb_strtoupper($prov_name));
				$activeSheet->setCellValue("G" . $excel_row, $wl->birthdate);
				$activeSheet->setCellValue("H" . $excel_row, mb_strtoupper($stat));
				$activeSheet->setCellValue("I" . $excel_row, $wl->remarks);
				$activeSheet->setCellValue("J" . $excel_row, $wl->duplicate);
				$activeSheet->setCellValue("K" . $excel_row, $wl->batch_no);

				$activeSheet->getRowDimension($excel_row)->setRowHeight(16);
				$activeSheet->getStyle('A' . $excel_row . ':B' . $excel_row)->applyFromArray($textcenter);
				$activeSheet->getStyle('C' . $excel_row . ':K' . $excel_row)->applyFromArray($textleft);
				$activeSheet->getStyle('A' . $excel_row . ':K' . $excel_row)->applyFromArray($border);

				$number++;
				$excel_row++;
			}

			if (!empty($provincename)) {
				$fileN .= $provincename . " ";
			}
			if (!empty($municipalityname)) {
				$fileN .= $municipalityname . " ";
			}

			$filename = $fileN . "WAITLIST_(" . $count_datas . ")_" . date("Y-m-d") . ".xlsx";

			$writer = new Xlsx($object);

			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="' . $filename);
			header('Cache-Control: max-age=0');

			$writer->save('php://output');
		} else {
			show_404("NO RECORDS FOUND");
		}
	}

	//TEST

	public function text()
	{
		$bufdata = $this->wmodel->getWaitlistData("*", array("spid" => "AP_WL_0011"), "tblbufanswers", "", "", "row");

		print_r(count($bufdata));

		$pension_receiver = (!empty($bufdata->pension_receiver) && $bufdata->pension_receiver != "0") ? "YES" : "NO";

		//print_r($bufdata->pension_receiver);
		print_r($pension_receiver);
	}

	public function test11()
	{
		$libraries = $this->Main->getLibraries("tblmaritalstatus");
		$marStatus = array_column($libraries, "name", "id");
		$libraries = $this->Main->getLibraries("tbllivingarrangement");
		$livingArr = array_column($libraries, "name", "id");
		$libraries = $this->Main->getLibraries("tblrelationships");
		$relList = array_column($libraries, "relname", "relid");

		print_r($marStatus);
		print_r($livingArr);
		print_r($relList);
	}
	//END TEST
}
