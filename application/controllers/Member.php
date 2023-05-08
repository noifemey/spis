<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Member extends CI_Controller
{
	private $pager_settings;
	public function __construct()
	{
		parent::__construct();

		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('', '');
		$this->load->model('Main', 'Main');
		$this->load->model("member_model", "mem");

		checkLogin();
	}

	public function index()
	{
		$data['app_active'] = true;

		//if(isset($this->session->userdata['logged_in']) && $this->session->userdata['logged_in']){

		$this->template->title('Social Pension Active Beneficiaries');
		$this->template->set_layout('default');
		$this->template->set_partial('header', 'partials/header');
		$this->template->set_partial('sidebar', 'partials/sidebar');
		$this->template->set_partial('aside', 'partials/aside');
		$this->template->set_partial('footer', 'partials/footer');
		$this->template->append_metadata('<script src="' . base_url("assets/js/pages/member/member.js?ver=") . filemtime(FCPATH . "assets/js/pages/member/member.js") . '"></script>');

		$this->template->build('member/member_view', $data);

		//}
		// else
		// {
		//   redirect (base_url().'404_override');
		// }	
	}
	// Routes: get-all-Members
	public function getAllMembers()
	{
		$count_data = 0;
		$data = $condition = [];
		$query = $this->input->get('query');
		$limit = !empty($this->input->get('limit')) ? $this->input->get('limit') : 10;
		$page = !empty($this->input->get('page')) ? $this->input->get('page') : 1;
		//$priority = !empty($this->input->get('priority'))? $this->input->get('priority'):0;

		$where = [];
		$where_or = "";

		//Condition
		if (empty($query)) {
			$where["sp_status<>"] = "Inactive";
		}

		if (!empty($this->input->get('condition'))) {
			$condition = json_decode($this->input->get('condition'));
			if (!empty($condition->prov_code)) {
				$where['province'] = $condition->prov_code;
			}
			if (!empty($condition->mun_code)) {
				$where['city'] = $condition->mun_code;
			}
			if (!empty($condition->bar_code)) {
				$where['barangay'] = $condition->bar_code;
			}
			if (!empty($condition->gender) && $condition->gender != "0") {
				$where['gender'] = $condition->gender;
			}
			if (!empty($condition->status) && $condition->status != "0") {
				$where['sp_status'] = $condition->status;
				if ($condition->status == "Inactive") {
					unset($where["sp_status<>"]);
				}
				// if ($condition->status == "Active") {
				// 	$where_or = "(SP_Status = 'Active' OR SP_Status = 'Additional')";
				// } else {
				// 	$where['sp_status'] = $condition->status;
				// }
			}
		}

		//Order
		$orderBy = !empty($this->input->get('orderBy')) ? $this->input->get('orderBy') : "lastname";
		$ascending = $this->input->get('ascending');
		$byColumn = $this->input->get('byColumn');
		if ($page == 1) {
			$offset = 0;
		} else {
			$offset = ($page - 1) * $limit;
		}

		//Select Columns
		$select = "b_id,connum AS SPID, lastname, firstname,middlename, extensionname, birthdate, gender, sp_status, registrationdate, province, city, barangay,additional";

		$order = array(
			'col' => $orderBy,
			'order_by' => $ascending ? "ASC" : "DESC",
		);

		//like
		$like = array();
		if (!empty($query)) {
			$like = array('column' => ["lastname", "firstname", "middlename", "connum", "CONCAT(lastname, ', ', firstname)"], 'data' => $query);
		}

		//$limit = empty($query) ? $limit : 10;
		$data =  $this->mem->getAllMembers($select, $where, $where_or, $like, $offset, $order, $limit);

		//Count of All Data
		$count_qry = array(
			"select" => "count(*) as total",
			"table" => "tblgeneral",
			'type' => "row",
			'condition' => $where,
		);
		$count_data = $this->Main->select($count_qry, $like, true)->total;
		$response = array(
			'count' => $count_data,
			'data' => $data,
		);
		response_json($response);
	}

	public function setForReplacementIndividual()
	{

		$memberid = $this->input->post('mem_id');
		$inactivereason = $this->input->post('reason_id');
		$reason_desc = "";

		$this->form_validation->set_rules('reason_id', 'Reason For Replacement', 'required');
		if ($inactivereason == 1) {
			$reason_desc = empty($this->input->post('duplicate')) ? "" : $this->input->post('duplicate');
			$this->form_validation->set_rules('duplicate', 'Beneficiary Duplicate', 'required');
		} else if ($inactivereason == 2) {
			$reason_desc = empty($this->input->post('dateofdeath')) ? "" : $this->input->post('dateofdeath');
			$this->form_validation->set_rules('dateofdeath', 'Date of Death', 'required');
		} else if ($inactivereason == 3) {
			$reason_desc = empty($this->input->post('pension_select')) ? "" : $this->input->post('pension_select');
			$this->form_validation->set_rules('pension_select', 'Type of Support', 'required');
			if($reason_desc == 'Others'){
				$this->form_validation->set_rules('otherreason', 'Please specify', 'required');
				$otherreason = empty($this->input->post('otherreason')) ? "" : $this->input->post('otherreason');
				$reason_desc .= ": " .$otherreason;
			}
		} else if ($inactivereason == 12) {
			$reason_desc = empty($this->input->post('pension_select')) ? "" : $this->input->post('pension_select');
			$this->form_validation->set_rules('pension_select', 'Type of Regular Income', 'required');
			if($reason_desc == 'Others'){
				$this->form_validation->set_rules('otherreason', 'Please specify', 'required');
				$otherreason = empty($this->input->post('otherreason')) ? "" : $this->input->post('otherreason');
				$reason_desc .= ": " .$otherreason;
			}
		} else if ($inactivereason == 6) {
			$reason_desc = empty($this->input->post('placeoftransfer')) ? "" : $this->input->post('placeoftransfer');
			$this->form_validation->set_rules('placeoftransfer', 'Place of Transfer', 'required');
		} else if ($inactivereason == 16) {
			$reason_desc = empty($this->input->post('otherreason')) ? "" : $this->input->post('otherreason');
		}

		//$this->form_validation->set_rules('password', 'Password', 'required');

		if ($this->form_validation->run() == FALSE) {
			$response['success'] = false;
			$response['message'] = $this->form_validation->error_array();
		} else {
			$res = memberlist("*", array('connum' => $memberid), "", "row");
			$fullname = $res->lastname . ", " . $res->firstname . " " . $res->middlename . " " . $res->extensionname;
			if ($inactivereason == 2) {
				$curdate = $reason_desc;
			} else {
				$curdate = date('Y-m-d');
			}

			$data = array(
				'sp_status'   			  => 'ForReplacement',
				'inactive_reason_id'      => $inactivereason,
				'sp_status_inactive_date' => $curdate,
				'sp_inactive_remarks' => $reason_desc
			);

			$this->db->where("connum", $memberid);
			$result = $this->db->update("tblgeneral", $data);

			$condi = array("id" => $inactivereason);
			$repreason = $this->Main->select(array('select' => "*", 'table' => "tblinactivereason", 'condition' => $condi, 'type' => "row"));
			if (!empty($repreason)) {
				$repreason = $repreason->name;
			} else {
				$repreason = $inactivereason;
			}

			beneLogs(sesdata('id'), $res->b_id, "EDIT", "Set SP Status For Replacement", (NULL), $repreason);
			userLogs(sesdata('id'), sesdata('fullname'), "EDIT", "Set SP Status 'For Replacement': [$res->connum] $fullname");

			$response = array(
				'success'	=> true,
				'message'	=> "Member SP Status was set for replacement successfully "
			);
		}

		response_json($response);
	}

	public function setActiveIndividual()
	{
		$memberid = $this->input->get('bid');

		$curdate = date('Y-m-d');
		$res = memberlist("*", array('b_id' => $memberid), "", "row");
		$fullname = $res->lastname . ", " . $res->firstname . " " . $res->middlename . " " . $res->extensionname;

		$data = array(
			'sp_status'   			  => 'Active'
		);

		$this->db->where("b_id", $memberid);
		$result = $this->db->update("tblgeneral", $data);

		userLogs(sesdata('id'), sesdata('fullname'), "EDIT", "Set Status 'Active': [$res->connum] $fullname");
		beneLogs(sesdata('id'), $memberid, "EDIT", "Set SP Status Active", (NULL), (NULL));

		$response = array(
			'success'	=> true,
			'message'	=> "Member SP Status was set to active successfully"
		);
		response_json($response);
	}

	public function archiveIndividual()
	{
		$id = $this->input->post('pensionerid');
		$curdate = date('Y-m-d');
		$data = array(
			'archive_status' => 1,
			'sp_status' => 'Inactive',
			'sp_status_inactive' => 'Was archived',
			'sp_status_inactive_date' => $curdate
		);
		$this->db->where("b_id", $id);
		$query = $this->db->update("tblgeneral", $data);

		$res = memberlist("*", array('b_id' => $id), "", "row");
		$fullname = $res->lastname . ", " . $res->firstname . " " . $res->middlename . " " . $res->extensionname;
		userLogs(sesdata('id'), sesdata('fullname'), "EDIT", "Set Archived: [$res->connum] $fullname");

		$response = array(
			'success'	=> true,
			'redirect'	=> base_url('member')
		);
		response_json($response);
	}

	public function exportMasterlist()
	{

		ini_set('memory_limit', '999M');
		$prov_code  = $this->input->get("prov_code");
		$mun_code  = $this->input->get("mun_code");
		$bar_code  = $this->input->get("bar_code");
		$status  = $this->input->get("status");
		$locname = "";

		//Start Get Libraries
		$provinces = $this->Main->get_all_provinces();
		$prov_name_list = array_column($provinces, 'prov_name', 'prov_code');
		$municipalities = $this->Main->get_all_municipalities();
		$mun_name_list = array_column($municipalities, 'mun_name', 'mun_code');
		$barangays = $this->Main->getBarangays();
		$bar_name_list = array_column($barangays, 'bar_name', 'bar_code');

		$libraries = $this->Main->getLibraries("tblinactivereason");
		$inactiveReason = array_column($libraries, "name", "id");
		//END Get 

		if($status == 'Inactive'){
			$condition = array(
				"sp_status"      => "Inactive",
				"archive_status" => 0,
			);
		} else {
			$condition = array(
				"sp_status <>"      => "Inactive",
				"archive_status" 	=> 0,
			);
		}


		if (!empty($prov_code)) {
			$condition["province"] = $prov_code;
			$locname .= (isset($prov_name_list[$prov_code])) ? " " . $prov_name_list[$prov_code] : "";
		}
		if (!empty($mun_code)) {
			$condition["city"] = $mun_code;
			$locname .= (isset($mun_name_list[$mun_code])) ? " " . $mun_name_list[$mun_code] : "";
		}
		if (!empty($bar_code)) {
			$condition["barangay"] = $bar_code;
		}

		$select = "connum,lastname, firstname, middlename, extensionname, province, city, barangay, birthdate,uct_id,gender, sp_status, mothersMaidenName, inactive_reason_id,sp_inactive_remarks,registrationdate,additional";

		$queries = array(
			"select"	=> $select,
			"table"		=> "tblgeneral",
			"condition"	=> $condition,
			'order'     => array("col" => "lastname", "order_by" => "ASC"),
			"limit"		=> "",
			"offset"	=> "",
			"type"		=> ""
		);
		$memberlist = $this->Main->select($queries);
		$count_datas = count($memberlist);

		if ($count_datas > 0) {
			$object = new Spreadsheet();

			$object->createSheet(0);
			$object->setActiveSheetIndex(0);
			$activeSheet = $object->getActiveSheet();
			$activeSheet->setTitle("ALL DATA");

			$activeSheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
			$activeSheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

			$margineset = $activeSheet->getPageMargins();
			$margineset->setTop(0.25);
			$margineset->setBottom(0.25);
			$margineset->setRight(0.25);
			$margineset->setLeft(0.25);

			//style settings
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
				[
					'borders' => [
						'allBorders' => [
							'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
						]
					]
				];

			$activeSheet->getColumnDimension('A')->setWidth(5); //no
			$activeSheet->getColumnDimension('B')->setWidth(17); //no
			$activeSheet->getColumnDimension('C')->setWidth(17); //no
			$activeSheet->getColumnDimension('D')->setWidth(20); //lastname
			$activeSheet->getColumnDimension('E')->setWidth(20); //firstname
			$activeSheet->getColumnDimension('F')->setWidth(20); //middlename
			$activeSheet->getColumnDimension('G')->setWidth(15); //ext name
			$activeSheet->getColumnDimension('H')->setWidth(18); //prov
			$activeSheet->getColumnDimension('I')->setWidth(14); //muni
			$activeSheet->getColumnDimension('J')->setWidth(35); //barname
			$activeSheet->getColumnDimension('K')->setWidth(8); //sex
			$activeSheet->getColumnDimension('L')->setWidth(8); //bdate
			$activeSheet->getColumnDimension('M')->setWidth(15); //mmn
			$activeSheet->getColumnDimension('N')->setWidth(20); //mmn
			$activeSheet->getColumnDimension('O')->setWidth(25); //remarks

			$excel_row = 1;

			$table_columns = array("NO.", "SPID #", "UCT ID #", "LAST NAME", "FIRST NAME", "MIDDLE NAME", "EXTENSION NAME", "PROVINCE", "MUNICIPALITY", "BARANGAY", "SEX", "BIRTHDATE", "MOTHER'S MAIDEN NAME", "REGISTRATION DATE", "REMARKS");

			$hs = "A";
			foreach ($table_columns as $tv) {
				$activeSheet->setCellValue($hs . $excel_row, $tv);
				$hs++;
				$activeSheet->getStyle('A' . $excel_row . ':O' . $excel_row)->applyFromArray($headerstyleborder);
				$activeSheet->getStyle('A' . $excel_row . ':O' . $excel_row)->getFont()->setBold(true);
				$activeSheet->getStyle('A' . $excel_row . ':O' . $excel_row)->getAlignment()->setWrapText(true);
			}
			$excel_row++;
			$number = 1;
			$all_data = [];

			foreach ($memberlist as $ml) {
				$bar_name = (isset($bar_name_list[$ml->barangay])) ? $bar_name_list[$ml->barangay] . "/" . $ml->barangay : "";
				$mun_name = (isset($mun_name_list[$ml->city])) ? $mun_name_list[$ml->city] . "/" . $ml->city : "";
				$prov_name = (isset($prov_name_list[$ml->province])) ? " " . $prov_name_list[$ml->province] . "/" . $ml->province  : "";

				$reasonforrep = "";
				if ($ml->sp_status == "ForReplacement") {

					if (!empty($ml->inactive_reason_id)) {

						$reasonforrep = (isset($inactiveReason[$ml->inactive_reason_id])) ? $inactiveReason[$ml->inactive_reason_id] : "";
						//$reasonforrep = getForRepReason($ml->inactive_reason_id,"row")->name;
						if ($reasonforrep == "Deceased" & !empty($ml->sp_inactive_remarks)) {
							$reasonforrep = $reasonforrep . " (" . date_format(new DateTime($ml->sp_inactive_remarks), "Y-m-d") . ")";
						}
						if ($reasonforrep == "With Pension" & !empty($ml->sp_inactive_remarks)) {
							$reasonforrep = $reasonforrep . " (" . $ml->sp_inactive_remarks . ")";
						}
					} else {
						$reasonforrep = "No reason set.";
					}

				}

				$all_data[] = [
					'number' => $number,
					'connum' => $ml->connum,
					'uct_id' => $ml->uct_id,
					'lastname' => $ml->lastname,
					'firstname' => $ml->firstname,
					'middlename' => $ml->middlename,
					'extensionname' => $ml->extensionname,
					'prov_name' => $prov_name,
					'mun_name' => $mun_name,
					'bar_name' => $bar_name,
					'gender' => $ml->gender,
					'birthdate' => $ml->birthdate,
					'mothersMaidenName' => $ml->mothersMaidenName,
					'registrationdate' => $ml->registrationdate,
					'reasonforrep' => $reasonforrep,
					'sp_status' => $ml->sp_status,
					'additional' => $ml->additional,
				];
				$number++;
			}
    		
    		array_multisort(array_column($all_data, 'mun_name'), SORT_ASC, array_column($all_data, 'bar_name'), SORT_ASC, array_column($all_data, 'lastname'), SORT_ASC, $all_data);
			
			foreach ($all_data as $key => $value) {

				$activeSheet->setCellValue("A" . $excel_row, (string)$value['number']);
				$activeSheet->setCellValue("B" . $excel_row, $value['connum']);
				$activeSheet->setCellValue("C" . $excel_row, $value['uct_id']);
				$activeSheet->setCellValue("D" . $excel_row, $value['lastname']);
				$activeSheet->setCellValue("E" . $excel_row, $value['firstname']);
				$activeSheet->setCellValue("F" . $excel_row, $value['middlename']);
				$activeSheet->setCellValue("G" . $excel_row, $value['extensionname']);
				$activeSheet->setCellValue("H" . $excel_row, $value['prov_name']);
				$activeSheet->setCellValue("I" . $excel_row, $value['mun_name']);
				$activeSheet->setCellValue("J" . $excel_row, $value['bar_name']);
				$activeSheet->setCellValue("K" . $excel_row, $value['gender']);
				$activeSheet->setCellValue("L" . $excel_row, $value['birthdate']);
				$activeSheet->setCellValue("M" . $excel_row, $value['mothersMaidenName']);
				$activeSheet->setCellValue("N" . $excel_row, $value['registrationdate']);

				if($value['additional'] > 0){
					$activeSheet->setCellValue("O" . $excel_row, $value['additional'] . " Additional");
				}

				if ($value['sp_status'] == "ForReplacement") {
					$activeSheet->getStyle('A' . $excel_row . ':O' . $excel_row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('f46242');

					$activeSheet->setCellValue("O" . $excel_row, "*FOR REPLACEMENT - " . $value['reasonforrep']);
				}

				$activeSheet->getStyle('A' . $excel_row . ':O' . $excel_row)->applyFromArray($border);
				$excel_row++;
			}
		}

		$activeSheet->setSelectedCell('A1');
		$activeSheet->setShowGridlines(true);
		$filename = strtoupper($locname) . " MASTERLIST_(" . $count_datas . ")_" . date("Y-m-d") . ".xlsx";

		$writer = new Xlsx($object);
		$writer->setPreCalculateFormulas(true);

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="' . $filename);
		header('Cache-Control: max-age=0');

		$writer->save('php://output');

		userLogs(sesdata('id'), sesdata('fullname'), "EXPORT", "Export Database Masterlist: $locname");
	}

	/////// REPLACE MEMBER ////////////////////////////////////

	public function getEligibleList()
	{
		//$wid = $this->input->post('wid');
		$mun_code = $this->input->get('mun_code');

		$success = true;

		$masterlist_query  = array(
			'select'           => 'w_id, reference_code, lastname, firstname, lastname, middlename, extname, bar_code,birthdate ',
			'table'            => 'tblwaitinglist',
			'condition'        => array("priority" => '1', "archived" => '0', "mun_code" => $mun_code),
			'order'        	   => array("col" => "lastname", "order_by" => "ASC"),
			'type'             => false,
		);
		$selected = $this->Main->select($masterlist_query);
		$waitlist = [];
		$brgyList = array_column($selected, 'bar_code');
		$brgylistulit = !empty($brgyList) ? getBarangays("*", ['bar_code' => $brgyList]) : [];
		$brgyname = array_column($brgylistulit, 'bar_name', 'bar_code');
		
		$waitID = array_column($selected, 'reference_code');

		$bufID = [];
		if (!empty($waitID)) {
			$bufAnswer_query  = array(
				'select'           => 'spid,worker_name,date_accomplished,',
				'table'            => 'tblbufanswers',
				'condition'        => array('spid' => $waitID),
				'type'             => "result_array",
			);
			$bufResult = $this->Main->select($bufAnswer_query);
			$bufID = array_column($bufResult, NULL, 'spid');
		}


		// pdie($bufID, 1);
		// if (empty($selected)) {
		// 	$success = false;
		// }
		if (!empty($selected)) {
			foreach ($selected as $value) {
				$label = $value->reference_code . " - " . $value->lastname . ", " . $value->firstname . " " . $value->middlename . " " . $value->middlename . " " . $value->extname;
				$barangayName = !empty($brgyname[$value->bar_code]) ? $brgyname[$value->bar_code] : "";
				if (isset($bufID[$value->reference_code])) {
					$bufID[$value->reference_code]['date_accomplished'] = ($bufID[$value->reference_code]['date_accomplished'] != "0000-00-00 00:00:00") ? date('Y-m-d', strtotime($bufID[$value->reference_code]['date_accomplished'])) : "";
				}
				$waitlist[] = array(
					"w_id" => $value->w_id,
					"label" => strtoupper($label) . " - " . $barangayName,
					"barangay" => $barangayName,
					"birthdate" => $value->birthdate,
					"age" => getAge($value->birthdate),
					'buf' => isset($bufID[$value->reference_code]) ? $bufID[$value->reference_code] : []
				);
			}
		}
		$response = array(
			'success' => $success,
			'data' 	=> $waitlist
		);

		response_json($response);
	}

	public function ReplaceMemberSubmit()
	{
		//ID of benificiary to be replaced
		$b_id = $this->input->post('m_id');
		//ID of eligible waitlist to replace
		$w_id = $this->input->post('w_id');
		$work_name = $this->input->post('work_name');
		$dateAccomplish = $this->input->post('dateAccomplish');
		$dateofreplacement = $this->input->post('dateofreplacement');
		$liquidation = json_decode($this->input->post('liquidation'),true);

		//For new SPID
		$curmonth = (date("m"));
		$curyear = date("Y");
		$curdate = date('Y-m-d');
		if ($curmonth >= 1 and $curmonth <= 6) {
			$cursem = 1;
		} else if ($curmonth >= 7 and $curmonth <= 12) {
			$cursem = 2;
		}

		$filtercondition = array("w_id" => $w_id);
		$qry = array(
			"select" => "*",
			"table" => "tblwaitinglist",
			'type' => "row",
			'condition' => $filtercondition,
		);
		$waitlistmem = $this->Main->select($qry, array(), true);

		$reference_code = $waitlistmem->reference_code;
		$currdate = date("Y-m-d");

		//1. Generate SPID
		// $munlastid = $this->Main->raw("SELECT count(*) as munlastid FROM tblgeneral WHERE city='$waitlistmem->bar_code'", true)->munlastid;
		$munlastid = $this->Main->raw("SELECT count(*) as munlastid FROM tblgeneral WHERE barangay='$waitlistmem->bar_code'", true)->munlastid;
		$spid = $munlastid + 1;
		$n = 1;
		$cnt = str_pad($n, 3, '0', STR_PAD_LEFT);
		$spid = "SP" . $waitlistmem->bar_code . "-" . $cnt;

		//Checks if connum exists in the database
		$check_connum = checkConnum($spid);

		if ($check_connum['inDatabase'] == 1) {
			while ($check_connum['inDatabase'] == 1) {
				$n++;
				$serial = str_pad($n, 3, 0, STR_PAD_LEFT);
				$spid = "SP" . $waitlistmem->bar_code . "-" . $serial;
				$check_connum = checkConnum($spid);
			}
		}

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
			'address'   		   		  => $waitlistmem->address,
			'barangay'   		   		  => $waitlistmem->bar_code,
			'city'		   		   		  => $waitlistmem->mun_code,
			'province'   		   		  => $waitlistmem->prov_code,
			'registrationdate'     		  => $currdate,
			'contactno'   		   		  => $waitlistmem->contact_no,
			'birthdate'   		   		  => $waitlistmem->birthdate,
			'birthplace'   		   		  => $waitlistmem->birthplace,
			'hh_id'   			   		  => $waitlistmem->hh_id,
			'hh_size'					  => $waitlistmem->hh_size,
			'osca_id'   		   		  => $waitlistmem->osca_id,
			'livingarrangement_id' 		  => $waitlistmem->livingArrangement,
			'mothersMaidenName'			  => $waitlistmem->mothersMaidenName,
			'year_start'     		  	  => $curyear,
			'quarter_start'     		  => $cursem,
			'remarks'			   		  => "",
			'representativeName1'		  => $waitlistmem->nameofCaregiver,
			'representativeRelationship1' => $waitlistmem->relationshipofCaregiver,
			'representativeName2'		  => $waitlistmem->repname2,
			'representativeRelationship2' => $waitlistmem->reprel2,
			'representativeName3'		  => $waitlistmem->repname3,
			'representativeRelationship3' => $waitlistmem->reprel3,
			'worker_name' 				  => $work_name,
			'date_accomplished' 		  => $dateAccomplish,
			'replacer' 		  			  => 1,							
		);

		//3. Insert to tbl_general
		$lastBeneId = $this->Main->insert("tblgeneral", $dataAdd, 'lastid')['lastid'];

		//$replaceeData = $this->member->memberlist("connum",array( "b_id" => $b_id ),"","row");
		$where = array("b_id" => $b_id);
		$qry = array(
			"select" => "connum,inactive_reason_id,sp_inactive_remarks,",
			"table" => "tblgeneral",
			'type' => "row",
			'condition' => $where,
		);
		$replaceeData = $this->Main->select($qry, array(), true);

		//3.1 Insert to tblreplace
		$replaceAdd = array(
			'replacee' 			=> $replaceeData->connum,
			'replacer'   		=> $spid,
			'replacementdate'  	=> $dateofreplacement,
			'reason'   		   	=> $replaceeData->sp_inactive_remarks,
			'reason_id'		   	=> $replaceeData->inactive_reason_id,
			'user_name'		   	=> sesdata('fullname'),
		);
		$insertRep = $this->Main->insert("tblreplace", $replaceAdd, 'lastid');

		//4. Update tblbufanswers - Set spid = new_spid where spid = reference_no
		$this->db->where("spid", $reference_code);
		$setInactiveResult = $this->db->update("tblbufanswers", array('spid'	=> $spid));

		//5. Update tblgeneral set sp_status = "inactive" where b_id = $b_id
		$remarks = "Replaced by $waitlistmem->lastname, $waitlistmem->firstname [$spid]";
		$dataReplacee = array(
			'sp_status' 		      => 'Inactive',
			'sp_status_inactive_date' => $dateofreplacement,
			'remarks'   		      => $remarks
		);
		$this->db->where("b_id", $b_id);
		$setInactiveResult = $this->db->update("tblgeneral", $dataReplacee);

		//6. Delete tblwaitinglist where w_id
		$this->db->where("w_id", $w_id);
		$query = $this->db->update("tblwaitinglist", ["archived" => 1, "new_spid" => $spid]);
		//$query=$this->db->delete('tblwaitinglist');

		// CHange Status of payment
		// Query loop ayaw ni benz
		if (!empty($liquidation)) {
			foreach ($liquidation as $lk => $lv) {
				$this->memberTransfer(false, $lv, $w_id,$spid);
			}
		}
		//Log
		$replaceeSpid = $replaceeData->connum;
		userLogs(sesdata('id'), sesdata('fullname'), "EDIT", "Replace member: $replaceeSpid replaced by $spid");
		beneLogs(sesdata('id'), $b_id, "EDIT", "Replaced by $spid", (NULL), (NULL));
		beneLogs(sesdata('id'), $lastBeneId, "EDIT", "Replaced $replaceeSpid", (NULL), (NULL));

		$response = array(
			'success' => true,
			'message' => "$replaceeSpid was sucessfully replaced by $spid",
			'redirect'	=> base_url("Member/edit/$lastBeneId")
		);
		response_json($response);
	}

	/////// END REPLACE MEMBER ////////////////////////////////////

	/////// VALIDATION ////////////////////////////////////
	private function _validateSetForReplacement()
	{
		$rules = [
			[
				'field' => 'reason_id',
				'label' => 'Reason For Replacement',
				'rules' => 'required'
			]
		];

		$this->form_validation->set_rules($rules);
	}
	////// END Validation /////////////////////////////////


	////// MEMBER PAYMENT HISTORY ///////////////////

	public function getMemPayment()
	{
		$spid = $this->input->post();
		$condition = array("spid" => $spid);

		$mem_payments = $this->mem->get_member_payment($condition);

		response_json($mem_payments);
	}

	public function addMemPayment()
	{
		$curdate = date('Y-m-d H:i:s');
		//condition
		$spid = $this->input->post('spid');
		$year = $this->input->post('year');
		$period = $this->input->post('period');
		$remarks = $this->input->post('remarks');
		$prov_code = $this->input->post('prov_code');
		$mun_code = $this->input->post('mun_code');
		$bar_code = $this->input->post('bar_code');

		//update data
		$amount = $this->input->post('amount');
		$date_receive = $this->input->post('date_receive');
		$receiver = $this->input->post('receiver');
		$liquidation = $this->input->post('liquidation');

		$modepay = "SEMESTER";
		$qtrsem = 1;
		if (in_array($period, [5, 6])) {
			$modepay = "SEMESTER";
			$qtrsem = ($period == 5) ? 1 : 2;
			if ($qtrsem == 1) {
				$qtrsemlogs = "1st SEMESTER";
			} else if ($qtrsem == 2) {
				$qtrsemlogs = "2nd SEMESTER";
			}
		} else {
			$qtrsem = $period;
			$modepay = "QUARTER";
			if ($qtrsem == 1) {
				$qtrsemlogs = "1st QUARTER";
			} else if ($qtrsem == 2) {
				$qtrsemlogs = "2nd QUARTER";
			} else if ($qtrsem == 3) {
				$qtrsemlogs = "3rd QUARTER";
			} else if ($qtrsem == 4) {
				$qtrsemlogs = "4th QUARTER";
			}
		}

		$data = array(
			"spid" 	  		  => $spid,
			"prov_code" 	  => $prov_code,
			"mun_code" 		  => $mun_code,
			"bar_code" 		  => $bar_code,
			"year" 			  => $year,
			"mode_of_payment" => $modepay,
			"period" 		=> $qtrsem,
			'amount'      	=> $amount,
			'receiver'      => $receiver,
			'liquidation' 	=> $liquidation,
			'date_receive' 	=> $date_receive,
			'sp_dateupdated' => $curdate,
			'remarks'		=> $remarks
		);

		$result = $this->db->insert("tblpayroll", $data);

		if ($result) {
			$bid = getMemberDetails("b_id", ["connum" => $spid])->b_id;
			if ((int)$liquidation == 0) {
				$pstatus = "UNPAID";
			} else {
				$pstatus = "PAID";
			}

			userLogs(sesdata('id'), sesdata('fullname'), "ADD", "Added Payment [payment : $pstatus, amount : $amount,  receiver : $receiver,  date receive : $date_receive] of $spid for $year ($qtrsemlogs)");
			beneLogs(sesdata('id'), $bid, "ADD", "Added Payment [payment : $pstatus, amount : $amount,  receiver : $receiver,  date receive : $date_receive] of $spid for $year ($qtrsemlogs)", (NULL), (NULL));

			$response = array(
				'success' => true,
				'message' => "Successfully Updated ",
			);
		} else {
			$response = array(
				'success' => false,
				'message' => "Something went wrong.",
			);
		}

		response_json($response);
	}

	public function updateMemPayment()
	{
		$curdate = date('Y-m-d H:i:s');
		//condition
		$p_id = $this->input->post('p_id');
		$spid = $this->input->post('spid');
		$year = $this->input->post('year');
		$period = $this->input->post('period');
		$remarks = $this->input->post('remarks');

		//update data
		$amount = $this->input->post('amount');
		$date_receive = $this->input->post('date_receive');
		$receiver = $this->input->post('receiver');
		$liquidation = $this->input->post('liquidation');

		$modepay = "SEMESTER";
		$qtrsem = 1;
		if (in_array($period, [5, 6])) {
			$modepay = "SEMESTER";
			$qtrsem = ($period == 5) ? 1 : 2;
			if ($qtrsem == 1) {
				$qtrsemlogs = "1st SEMESTER";
			} else if ($qtrsem == 2) {
				$qtrsemlogs = "2nd SEMESTER";
			}
		} else {
			$qtrsem = $period;
			$modepay = "QUARTER";
			if ($qtrsem == 1) {
				$qtrsemlogs = "1st QUARTER";
			} else if ($qtrsem == 2) {
				$qtrsemlogs = "2nd QUARTER";
			} else if ($qtrsem == 3) {
				$qtrsemlogs = "3rd QUARTER";
			} else if ($qtrsem == 4) {
				$qtrsemlogs = "4th QUARTER";
			}
		}

		$oldPaymentData = $this->mem->paymentDetails("*", array("p_id" => $p_id), array(), 'row');

		$data = array(
			"year" 			  => $year,
			"mode_of_payment" => $modepay,
			"period" 		=> $qtrsem,
			'liquidation' 	=> $liquidation,
			'amount'      	=> $amount,
			'receiver'      => $receiver,
			'date_receive' 	=> $date_receive,
			'sp_dateupdated' => $curdate,
			'remarks'		=> $remarks
		);

		$this->db->where("p_id", $p_id);
		$result = $this->db->update("tblpayroll", $data);

		if ($result) {
			$bid = getMemberDetails("b_id", ["connum" => $spid])->b_id;
			$pstatus = $this->getpaymentstat($liquidation);

			userLogs(sesdata('id'), sesdata('fullname'), "EDIT", "Updated Payment Details [payment : $pstatus, amount : $amount,  receiver : $receiver,  date receive : $date_receive] of $spid for $year ($qtrsemlogs)");

			if ($oldPaymentData->amount != $amount) {
				beneLogs(sesdata('id'), $bid, "EDIT", "$year ($qtrsemlogs) Amount", $oldPaymentData->amount, $amount);
			}
			if ($oldPaymentData->receiver != $receiver) {
				beneLogs(sesdata('id'), $bid, "EDIT", "$year ($qtrsemlogs) receiver", $oldPaymentData->receiver, $receiver);
			}

			$oldDate_ = date_create($oldPaymentData->date_receive);
			$oldDate = date_format($oldDate_,"Y-m-d");
			
			$newDate_ = date_create($date_receive);
			$newDate = date_format($newDate_,"Y-m-d");

			if ($oldDate != $newDate) {
				beneLogs(sesdata('id'), $bid, "EDIT", "$year ($qtrsemlogs) date receive", $oldDate, $newDate);
			}

			if ($oldPaymentData->liquidation != $liquidation) {
				$oldps = $this->getpaymentstat($oldPaymentData->liquidation);
				$newps = $this->getpaymentstat($liquidation);
				beneLogs(sesdata('id'), $bid, "EDIT", "$year ($qtrsemlogs) Payment Status", $oldps, $newps);
			}
			if ($oldPaymentData->remarks != $remarks) {
				beneLogs(sesdata('id'), $bid, "EDIT", "$year ($qtrsemlogs) Remarks", $oldPaymentData->remarks, $remarks);
			}

			$response = array(
				'success' => true,
				'message' => "Successfully Updated ",
			);
		} else {
			$response = array(
				'success' => false,
				'message' => "Something went wrong.",
			);
		}

		response_json($response);
	}
	
	private function getpaymentstat($ps){
		if($ps == "0"){ return "UNPAID"; }
		if($ps == "1"){ return "PAID"; }
		if($ps == "2"){ return "TRANSFERRED"; }
		if($ps == "3"){ return "OFFSET"; }
		if($ps == "4"){ return "ONHOLD"; }
		else { return $ps;}
	}

	public function deleteMemPayment()
	{
		//condition
		$p_id = $this->input->post('p_id');
		$spid = $this->input->post('spid');
		$year = $this->input->post('year');
		$period = $this->input->post('period');

		//Delete Payroll
		$this->db->where("p_id", $p_id);
		$result = $this->db->delete("tblpayroll");

		if ($result) {
			$bid = getMemberDetails("b_id", ["connum" => $spid])->b_id;
			if (in_array($period, [5, 6])) {
				$qtrsem = ($period == 5) ? 1 : 2;
				if ($qtrsem == 1) {
					$qtrsemlogs = "1st SEMESTER";
				} else if ($qtrsem == 2) {
					$qtrsemlogs = "2nd SEMESTER";
				}
			} else {
				$qtrsem = $period;
				if ($qtrsem == 1) {
					$qtrsemlogs = "1st QUARTER";
				} else if ($qtrsem == 2) {
					$qtrsemlogs = "2nd QUARTER";
				} else if ($qtrsem == 3) {
					$qtrsemlogs = "3rd QUARTER";
				} else if ($qtrsem == 4) {
					$qtrsemlogs = "4th QUARTER";
				}
			}

			userLogs(sesdata('id'), sesdata('fullname'), "DELETE", "Deleted Payment of $spid for $year ($qtrsemlogs)");
			beneLogs(sesdata('id'), $bid, "DELETE", "Deleted Payment of $spid for $year ($qtrsemlogs)", (NULL), (NULL));

			$response = array(
				'success' => true,
				'message' => "Successfully Updated ",
			);
		} else {
			$response = array(
				'success' => false,
				'message' => "Something went wrong.",
			);
		}

		response_json($response);
	}

	////// END PAYMENT HISTORY /////////////////////

	//Download payment history of current beneficiary
	public function download_active_payments()
	{
		$provinces = $this->Main->get_all_provinces();
		$prov_name_list = array_column($provinces, 'prov_name', 'prov_code');
		$municipalities = $this->Main->get_all_municipalities();
		$mun_name_list = array_column($municipalities, 'mun_name', 'mun_code');
		$barangays = $this->Main->getBarangays();
		$bar_name_list = array_column($barangays, 'bar_name', 'bar_code');

		$content = "osca_id , connum , lastname , firstname , middlename , extensionname , region , permanent_province , permanent_city , permanent_barangay , permanent_address , permanent_street , region , province , city , barangay , address , street , gender , birthdate  , Q1_paid , Q1_UNpaid , Q2_paid , Q2_Unpaid , S1_paid , S1_Upaid , S2_paid , S2_Upaid , S11_paid , S11_Upaid, sp_status \r\n";

		$prov_code = $this->input->get('prov_code');

		$mun_code = $this->input->get('mun_code');

		$condi = array("mode_of_payment" => "QUARTER", "period" => "1", "year" => "2019");
		if (!empty($prov_code)) {
			$condi["prov_code"] = $prov_code;
		}

		$this->db->select("spid,liquidation");
		$this->db->from("tblpayroll");
		$this->db->where(array("mode_of_payment" => "QUARTER", "period" => "1", "year" => "2019"));
		$query = $this->db->get();
		$q1_payment = $query->result_array();
		$q1 = array_column($q1_payment, 'liquidation', 'spid');
		//$q1 = array_change_key_case($q1,CASE_UPPER);

		$this->db->select("spid,liquidation");
		$this->db->from("tblpayroll");
		$this->db->where(array("mode_of_payment" => "QUARTER", "period" => "2", "year" => "2019"));
		$query = $this->db->get();
		$q2_payment = $query->result_array();
		$q2 = array_column($q2_payment, 'liquidation', 'spid');
		//$q2 = array_change_key_case($q2,CASE_UPPER);

		$this->db->select("spid,liquidation");
		$this->db->from("tblpayroll");
		$this->db->where(array("mode_of_payment" => "SEMESTER", "period" => "1", "year" => "2019"));
		$query = $this->db->get();
		$s1_payment = $query->result_array();
		$s1 = array_column($s1_payment, 'liquidation', 'spid');
		//$s1 = array_change_key_case($s1,CASE_UPPER);

		$this->db->select("spid,liquidation");
		$this->db->from("tblpayroll");
		$this->db->where(array("mode_of_payment" => "SEMESTER", "period" => "2", "year" => "2019"));
		$query = $this->db->get();
		$s2_payment = $query->result_array();
		$s2 = array_column($s2_payment, 'liquidation', 'spid');
		//$s2 = array_change_key_case($s2,CASE_UPPER);

		$this->db->select("spid,liquidation");
		$this->db->from("tblpayroll");
		$this->db->where(array("mode_of_payment" => "SEMESTER", "period" => "1", "year" => "2020"));
		$query = $this->db->get();
		$s11_payment = $query->result_array();
		$s11 = array_column($s11_payment, 'liquidation', 'spid');
		//$s11 = array_change_key_case($s11,CASE_UPPER);


		// $this->db->select("DISTINCT(spid)");
		// $this->db->from("tblpayroll");
		// $query = $this->db->get();
		// $spids = $query->result();
		$this->db->select("connum,osca_id, lastname, firstname, middlename, extensionname, province, city, barangay, address, street,permanent_province, permanent_city, permanent_barangay, permanent_address, permanent_street, gender,birthdate,sp_status,inactive_reason_id");
		$this->db->from("tblgeneral");
		$condition = array("sp_status<>" => "Inactive");
		if (!empty($prov_code)) {
			$condition["province"] = $prov_code;
		}
		$this->db->where($condition);
		$query = $this->db->get();
		$spids = $query->result();
		$count_spid = count($spids);

		foreach ($spids as $bene) {

			// $this->db->select("connum,osca_id, lastname, firstname, middlename, extensionname, province, city, barangay, address, street,permanent_province, permanent_city, permanent_barangay, permanent_address, permanent_street, gender,birthdate,sp_status,inactive_reason_id");
			// $this->db->from("tblgeneral");
			// $this->db->where(array("connum" => $connum->spid));
			// $query = $this->db->get();
			// $bene = $query->row();


			// if(empty($bene)){

			// 	echo $bene->connum . "<br>";

			// }else{
			$permanent_province = (isset($prov_name_list[$bene->permanent_province])) ? $prov_name_list[$bene->permanent_province] : " ";
			$permanent_city = (isset($mun_name_list[$bene->permanent_city])) ? $mun_name_list[$bene->permanent_city] : " ";
			$permanent_barangay = (isset($bar_name_list[$bene->permanent_barangay])) ? $bar_name_list[$bene->permanent_barangay] : " ";

			// $permanent_province = str_replace(",","",$permanent_province);
			// $permanent_city = str_replace(",","",$permanent_city);
			$permanent_barangay = str_replace(",", " ", $permanent_barangay);
			$permanent_address = str_replace(",", " ", $bene->permanent_address);
			$permanent_street = str_replace(",", " ", $bene->permanent_street);

			$province = (isset($prov_name_list[$bene->province])) ? $prov_name_list[$bene->province] : " ";
			$city = (isset($mun_name_list[$bene->city])) ? $mun_name_list[$bene->city] : " ";
			$barangay = (isset($bar_name_list[$bene->barangay])) ? $bar_name_list[$bene->barangay] : " ";


			// $province =	$bene->province;
			// $city =	$bene->city;
			// $barangay =	$bene->barangay;

			// $province = str_replace(",","",$province);
			// $city = str_replace(",","",$city);
			$barangay = str_replace(",", " ", $barangay);
			$address = str_replace(",", " ", $bene->address);
			$street = str_replace(",", " ", $bene->street);

			// $extensionname = str_replace(",","",$bene->extensionname);
			// $middlename = str_replace(",","",$bene->middlename);
			// $firstname = str_replace(",","",$bene->firstname);
			// $lastname = str_replace(",","",$bene->lastname);

			$extensionname = $bene->extensionname;
			$middlename = $bene->middlename;
			$firstname = $bene->firstname;
			$lastname = $bene->lastname;

			$spid = $bene->connum;
			$q1_paid = (isset($q1[$spid])) ? $q1[$spid] : "--";
			$q2_paid = (isset($q2[$spid])) ? $q2[$spid] : "--";
			$s1_paid = (isset($s1[$spid])) ? $s1[$spid] : "--";
			$s2_paid = (isset($s2[$spid])) ? $s2[$spid] : "--";
			$s11_paid = (isset($s11[$spid])) ? $s11[$spid] : "--";

			$status = $bene->sp_status;
			if ($status == "ForReplacement" && $bene->inactive_reason_id == "1") {
				$status = "For Replacement - Double Entry";
			}
			if ($status == "ForReplacement" &&  $bene->inactive_reason_id == "2") {
				$status = "For Replacement - Deceased";
			}
			if ($status == "ForReplacement" &&  $bene->inactive_reason_id == "3") {
				$status = "For Replacement - With Regular Support";
			}
			if ($status == "ForReplacement" &&  $bene->inactive_reason_id == "4") {
				$status = "For Replacement - With Pension";
			}
			if ($status == "ForReplacement" &&  $bene->inactive_reason_id == "6") {
				$status = "For Replacement - Transferred";
			}
			if ($status == "ForReplacement"  && $bene->inactive_reason_id == "16") {
				$status = "For Replacement - Barangay Official";
			}

			//$osca_id = str_replace(",","",$bene->osca_id);
			$osca_id = $bene->osca_id;

			$cnt = "$osca_id,$bene->connum,$lastname,$firstname,$middlename,$extensionname,CAR[Cordillera Administrative Region],$permanent_province,$permanent_city,$permanent_barangay,$permanent_address,$permanent_street,CAR[Cordillera Administrative Region],$province,$city,$barangay,$address,$street,$bene->gender,$bene->birthdate,$q1_paid,$q1_paid,$q2_paid,$q2_paid,$s1_paid,$s1_paid,$s2_paid,$s2_paid,$s11_paid,$s11_paid,$status \r\n";

			$content .= $cnt;

			//echo "$content <br>";


			// }

		}
		$pname = (isset($prov_name_list[$prov_code])) ? $prov_name_list[$prov_code] : " ";
		$mname = (isset($mun_name_list[$mun_code])) ? $mun_name_list[$mun_code] : " ";

		$dt = date("Y-m-d");

		$filepath = "downloads/$pname _ $dt _active_payment_history_($count_spid).csv";

		file_put_contents($filepath, $content);

		// Process download
		if (file_exists($filepath)) {
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($filepath));
			flush(); // Flush system output buffer
			readfile($filepath);
			die();
		} else {
			http_response_code(404);
			die();
		}
	}

	//Download payment history of current beneficiary
	public function download_waitlist()
	{
		$provinces = $this->Main->get_all_provinces();
		$prov_name_list = array_column($provinces, 'prov_name', 'prov_code');
		$municipalities = $this->Main->get_all_municipalities();
		$mun_name_list = array_column($municipalities, 'mun_name', 'mun_code');
		$barangays = $this->Main->getBarangays();
		$bar_name_list = array_column($barangays, 'bar_name', 'bar_code');

		$content = "osca_id , connum , lastname , firstname , middlename , extensionname , region , permanent_province , permanent_city , permanent_barangay , permanent_address , permanent_street , region , province , city , barangay , address , street , gender , birthdate,status,duplicate \r\n";

		$this->db->select("osca_id, reference_code, lastname, firstname, middlename, extname, prov_code, mun_code, bar_code, address, street,permanent_prov_code, permanent_mun_code, permanent_bar_code, permanent_address, permanent_street, gender,birthdate,priority,duplicate,sent_to_co");
		$this->db->from("tblwaitinglist");
		$this->db->where(array("archived" => "0"));
		$query = $this->db->get();
		$spids = $query->result();
		$count_spid = count($spids);

		foreach ($spids as $bene) {

			$permanent_province = (isset($prov_name_list[$bene->permanent_prov_code])) ? $prov_name_list[$bene->permanent_prov_code] : "";
			$permanent_city = (isset($mun_name_list[$bene->permanent_mun_code])) ? $mun_name_list[$bene->permanent_mun_code] : "";
			$permanent_brgy = (isset($bar_name_list[$bene->permanent_bar_code])) ? $bar_name_list[$bene->permanent_bar_code] : "";

			$permanent_province = str_replace(",", "", $permanent_province);
			$permanent_city = str_replace(",", "", $permanent_city);
			$permanent_barangay = str_replace(",", "", $permanent_brgy);
			$permanent_address = str_replace(",", "", $bene->permanent_address);
			$permanent_street = str_replace(",", "", $bene->permanent_street);

			$province = (isset($prov_name_list[$bene->prov_code])) ? $prov_name_list[$bene->prov_code] : "";
			$city = (isset($mun_name_list[$bene->mun_code])) ? $mun_name_list[$bene->mun_code] : "";
			$brgy = (isset($bar_name_list[$bene->bar_code])) ? $bar_name_list[$bene->bar_code] : "";

			$province = str_replace(",", "", $province);
			$city = str_replace(",", "", $city);
			$barangay = str_replace(",", "", $brgy);
			$address = str_replace(",", "", $bene->address);
			$street = str_replace(",", "", $bene->street);

			$extensionname = str_replace(",", "", $bene->extname);
			$middlename = str_replace(",", "", $bene->middlename);
			$firstname = str_replace(",", "", $bene->firstname);
			$lastname = str_replace(",", "", $bene->lastname);

			$status = "";

			if ($bene->priority == "0") {
				if ($bene->sent_to_co == "1") {
					$status = "WAITING FOR ELIGIBILITY (ALREADY SENT TO CO)";
				} else {
					$status = "ADDITIONAL WAITLIST (FOR CROSSMATCHING AND ELIGIBILITY CHECKING)";
				}
			} else if ($bene->priority == "1") {
				$status = "ELIGIBLE WAITLIST";
			} else {
				$status = $bene->priority;
			}

			$osca_id = str_replace(",", "", $bene->osca_id);
			$content .= "$osca_id,$bene->reference_code,$lastname,$firstname,$middlename,$extensionname,CAR[Cordillera Administrative Region],$permanent_province,$permanent_city,$permanent_barangay,$permanent_address,$permanent_street,CAR[Cordillera Administrative Region],$province,$city,$barangay,$address,$street,$bene->gender,$bene->birthdate,$status,$bene->duplicate \r\n";
		}
		$dt = date("Y-m-d");

		$filepath = "downloads/eligible_waitlist_ $dt _($count_spid).csv";
		//echo "Saving Data";
		file_put_contents($filepath, $content);
		// echo "Saved Success";

		// echo "downloading";

		// Process download
		if (file_exists($filepath)) {
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($filepath));
			flush(); // Flush system output buffer
			readfile($filepath);
			die();
		} else {
			http_response_code(404);
			die();
		}
	}


	public function download_payroll_list()
	{
		$provinces = $this->Main->get_all_provinces();
		$prov_name_list = array_column($provinces, 'prov_name', 'prov_code');
		$municipalities = $this->Main->get_all_municipalities();
		$mun_name_list = array_column($municipalities, 'mun_name', 'mun_code');
		$barangays = $this->Main->getBarangays();
		$bar_name_list = array_column($barangays, 'bar_name', 'bar_code');

		$content = "osca_id , connum , lastname , firstname , middlename , extensionname , province , city , barangay , address , street , gender , birthdate  , 2019 Q1 paid , 2019 Q2 paid , 2019 S1 paid , 2019 S2 paid , 2020 S1 paid, sp_status, reason \r\n";

		$this->db->select("spid,liquidation");
		$this->db->from("tblpayroll");
		$this->db->where(array("mode_of_payment" => "QUARTER", "period" => "1", "year" => "2019"));
		$query = $this->db->get();
		$q1_payment = $query->result_array();
		$q1 = array_column($q1_payment, 'liquidation', 'spid');

		$this->db->select("spid,liquidation");
		$this->db->from("tblpayroll");
		$this->db->where(array("mode_of_payment" => "QUARTER", "period" => "2", "year" => "2019"));
		$query = $this->db->get();
		$q2_payment = $query->result_array();
		$q2 = array_column($q2_payment, 'liquidation', 'spid');

		$this->db->select("spid,liquidation");
		$this->db->from("tblpayroll");
		$this->db->where(array("mode_of_payment" => "SEMESTER", "period" => "1", "year" => "2019"));
		$query = $this->db->get();
		$s1_payment = $query->result_array();
		$s1 = array_column($s1_payment, 'liquidation', 'spid');

		$this->db->select("spid,liquidation");
		$this->db->from("tblpayroll");
		$this->db->where(array("mode_of_payment" => "SEMESTER", "period" => "2", "year" => "2019"));
		$query = $this->db->get();
		$s2_payment = $query->result_array();
		$s2 = array_column($s2_payment, 'liquidation', 'spid');

		$this->db->select("spid,liquidation");
		$this->db->from("tblpayroll");
		$this->db->where(array("mode_of_payment" => "SEMESTER", "period" => "1", "year" => "2020"));
		$query = $this->db->get();
		$s11_payment = $query->result_array();
		$s11 = array_column($s11_payment, 'liquidation', 'spid');

		$this->db->select("DISTINCT(spid)");
		$this->db->from("tblpayroll");
		$this->db->where(array("year" => "2020"));
		$query = $this->db->get();
		$spids = $query->result();
		$count_spid = count($spids);

		foreach ($spids as $connum) {

			$this->db->select("connum,osca_id, lastname, firstname, middlename, extensionname, province, city, barangay, address, street, gender,birthdate,sp_status,inactive_reason_id");
			$this->db->from("tblgeneral");
			$this->db->where(array("connum" => $connum->spid));
			$query = $this->db->get();
			$bene = $query->row();

			$spid = $connum->spid;

			if (empty($bene)) {

				$content .= "$connum->spid . \r\n";
			} else {

				$province = (isset($prov_name_list[$bene->province])) ? $prov_name_list[$bene->province] : " ";
				$city = (isset($mun_name_list[$bene->city])) ? $mun_name_list[$bene->city] : " ";
				$barangay = (isset($bar_name_list[$bene->barangay])) ? $bar_name_list[$bene->barangay] : " ";

				$barangay = str_replace(",", " ", $barangay);
				$address = str_replace(",", " ", $bene->address);
				$street = str_replace(",", " ", $bene->street);

				$extensionname = $bene->extensionname;
				$middlename = $bene->middlename;
				$firstname = $bene->firstname;
				$lastname = $bene->lastname;
				$status = $bene->sp_status;
				$osca_id = $bene->osca_id;

				$reason = "";

				if ($bene->inactive_reason_id == "1") {
					$reason = "For Replacement - Double Entry";
				}
				if ($bene->inactive_reason_id == "2") {
					$reason = "For Replacement - Deceased";
				}
				if ($bene->inactive_reason_id == "3") {
					$reason = "For Replacement - With Regular Support";
				}
				if ($bene->inactive_reason_id == "4") {
					$reason = "For Replacement - With Pension";
				}
				if ($bene->inactive_reason_id == "6") {
					$reason = "For Replacement - Transferred";
				}
				if ($bene->inactive_reason_id == "16") {
					$reason = "For Replacement - Barangay Official";
				}

				$q1_paid = (isset($q1[$spid])) ? $q1[$spid] : "--";
				$q2_paid = (isset($q2[$spid])) ? $q2[$spid] : "--";
				$s1_paid = (isset($s1[$spid])) ? $s1[$spid] : "--";
				$s2_paid = (isset($s2[$spid])) ? $s2[$spid] : "--";
				$s11_paid = (isset($s11[$spid])) ? $s11[$spid] : "--";

				$content .= "$osca_id,$spid,$lastname,$firstname,$middlename,$extensionname ,$province,$city,$barangay,$address,$street,$bene->gender,$bene->birthdate,$q1_paid,$q2_paid,$s1_paid,$s2_paid,$s11_paid,$status,$reason \r\n";
			}
		}

		$filepath = "downloads/payroll_payment_history_($count_spid).csv";

		file_put_contents($filepath, $content);

		// Process download
		if (file_exists($filepath)) {
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($filepath));
			flush(); // Flush system output buffer
			readfile($filepath);
			die();
		} else {
			http_response_code(404);
			die();
		}
	}

	//END Download payment history of current beneficiary

	/////////////////////////// START - VIEW MEMBER DETAILS ///////////////////////////

	public function viewMember($spid)
	{
		$data['app_active'] = true;
		$data['spid'] = $spid;

		$this->template->title('Pensioner Details');
		$this->template->set_layout('default');
		$this->template->set_partial('header', 'partials/header');
		$this->template->set_partial('sidebar', 'partials/sidebar');
		$this->template->set_partial('aside', 'partials/aside');
		$this->template->set_partial('footer', 'partials/footer');
		$this->template->append_metadata('<script src="' . base_url("assets/js/pages/member/memDetails.js?ver=") . filemtime(FCPATH . "assets/js/pages/member/memDetails.js") . '"></script>');

		$this->template->build('member/memDetails_view', $data);
	}

	public function getMemberDetail()
	{
		$spid = $this->input->post("spid");

		$replacerData = array();
		$replaceeData = array();
		$membereditlogs = array();

		//Replacement
		$data = [];
		$replacer = $this->mem->getReplacementHistoryOfPensioner($spid, "replacee");
		$replacee = $this->mem->getReplacementHistoryOfPensioner($spid, "replacer");
		if ($replacer != NULL) {
			$replacerData = $this->mem->memberDetails("*", array('connum' => $replacer->replacer), array(), 'row');
		}
		if ($replacee != NULL) {
			$replaceeData = $this->mem->memberDetails("*", array('connum' => $replacee->replacee), array(), 'row');
		}

		$data['replacerData'] = ($replacerData != NULL) ? $replacerData : array();
		$data['replaceeData'] = ($replaceeData != NULL) ? $replaceeData : array();

		$memData = $this->mem->memberDetails("*", array('connum' => $spid), array(), 'row');
		$data['memData'] = ($memData != NULL) ? $memData : array();

		if (!empty($memData)) {
			$b_id = $memData->b_id;
			$selectmemberedits = array(
				'select'	=> '*',
				'table'		=> 'tblbeneficiary_editlogs',
				'condition'	=> "b_id=$b_id",
				'limit'		=> 15,
				'offset'	=> 0,
				'order'		=> array('col' => "date_edit", 'order_by' => "DESC"),
				'type'		=> ""
			);
			$membereditlogs = $this->Main->select($selectmemberedits);
		}

		$memEditLog = [];

		foreach ($membereditlogs as $key => $value) {

			$time_date = date("M d, Y", strtotime($value->date_edit));
			$lapse = "";

			date_default_timezone_set('Asia/Manila');
			$diff = date_diff(date_create(date('Y-m-d H:i:s')), date_create(date($value->date_edit)));
			$years = $diff->format("%y");
			$months = $diff->format("%m");
			$days = $diff->format("%d");
			$hours = $diff->format("%h");
			$minutes = $diff->format("%i");
			$seconds = $diff->format("%s");

			if ($years > 0) {
				if ($years > 1) {
					$lapse = $years . " years ago";
				} else {
					$lapse = $years . " year ago";
				}
			} else if ($months > 0) {
				if ($months > 1) {
					$lapse =  $months . " months ago";
				} else {
					$lapse =  $months . " month ago";
				}
			} else if ($days > 0) {
				if ($days > 1) {
					$lapse =  $days . " days ago";
				} else {
					$lapse = $days . " day ago";
				}
			} else if ($hours > 0) {
				if ($hours > 1) {
					$lapse =  $hours . " hours ago";
				} else {
					$lapse = $hours . " hour ago";
				}
			} else if ($minutes > 0) {
				if ($minutes > 1) {
					$lapse =  $minutes . " minutes ago";
				} else {
					$lapse = $minutes . " minute ago";
				}
			} else if ($seconds > 0) {
				$lapse = $seconds . " seconds ago";
			} else {
				$lapse = $seconds . " second ago";
			}

			$usereditor = getUser("*", array("id" => $value->user_id), "row", "");

			$edits = "";

			if (!empty($value->prev_edit) & !empty($value->now_edit)) {
				$edits =  ": $value->prev_edit => $value->now_edit";
			} else if (!empty($value->now_edit)) {
				$edits =  ": NULL => $value->now_edit";
			} else if (!empty($value->prev_edit)) {
				$edits =  ": $value->prev_edit => NULL";
			}

			$memEditLog[] = array(
				"date" => $time_date,
				"lapse" => $lapse,
				"user" => $usereditor->username,
				"action" => $value->action,
				"log_details" => $value->field_edited,
				"edits"		=> $edits
			);
		}

		$data['membereditlogs'] = ($memEditLog != NULL) ? $memEditLog : array();

		$data['memberPayments'] = $this->mem->get_member_payment(["spid" => $spid]);

		response_json($data);
	}

	public function editMember($spid)
	{
		$data['app_active'] = true;
		$data['spid'] = $spid;

		$this->template->title('Pensioner Details');
		$this->template->set_layout('default');
		$this->template->set_partial('header', 'partials/header');
		$this->template->set_partial('sidebar', 'partials/sidebar');
		$this->template->set_partial('aside', 'partials/aside');
		$this->template->set_partial('footer', 'partials/footer');
		$this->template->append_metadata('<script src="' . base_url("assets/js/pages/member/memEdit.js?ver=") . filemtime(FCPATH . "assets/js/pages/member/memEdit.js") . '"></script>');

		$this->template->build('member/member_edit', $data);
	}

	public function printlbp($spid)
	{
		$pensiondata = $this->mem->memberDetails("*", array('connum' => $spid), array(), 'row');

		if (empty($pensiondata)) {
			show_404();
		}

		$data['pensiondata'] = $pensiondata;

		$locationdata = getLocation("b.bar_code = " . $pensiondata->barangay, true);
		$data['locationdata'] = $locationdata;

		$permanentlocdata = getLocation("b.bar_code = " . $pensiondata->barangay, true);
		$data['permanentlocdata'] = $permanentlocdata;

		$this->load->view('formprint/lbpform', $data);
	}

	public function printbuf($spid)
	{
		$pensiondata = $this->mem->memberDetails("*", array('connum' => $spid), array(), 'row');

		if (empty($pensiondata)) {
			show_404();
		}

		$data['pensiondata'] = $pensiondata;

		$locationdata = getLocation("b.bar_code = " . $pensiondata->barangay, true);
		$data['locationdata'] = $locationdata;
		$this->load->view('formprint/updateform', $data);
	}

	public function updateMemDetail()
	{
		extract($_POST);

		$memData = $_POST;

		$data = [];

		$spid = $memData["connum"];

		foreach ($memData as $key => $value) {
			if($memData[$key] == "null"){
				$memData[$key] = "";
			}
		}

		$oldMemberData = $this->mem->memberDetails("*", array('b_id' => $memData["b_id"]), array(), 'row');
		$res = $this->Main->update("tblgeneral", array("connum" => $memData["connum"]), $memData);

		if ($res) {
			userLogs(sesdata('id'), sesdata('fullname'), "EDIT", "Member data updated:  [$spid] $lastname, $firstname $middlename");

			if ($oldMemberData->lastname != $memData["lastname"]) {
				beneLogs(sesdata('id'), $memData["b_id"], "EDIT", "Last Name", $oldMemberData->lastname, $memData["lastname"]);
			}
			if ($oldMemberData->firstname != $memData["firstname"]) {
				beneLogs(sesdata('id'), $memData["b_id"], "EDIT", "First Name", $oldMemberData->firstname, $memData["firstname"]);
			}
			if ($oldMemberData->middlename != $memData["middlename"]) {
				beneLogs(sesdata('id'), $memData["b_id"], "EDIT", "Middle Name", $oldMemberData->middlename, $memData["middlename"]);
			}
			if ($oldMemberData->extensionname != $memData["extensionname"]) {
				beneLogs(sesdata('id'), $memData["b_id"], "EDIT", "Extension Name", $oldMemberData->extensionname, $memData["extensionname"]);
			}
			if ($oldMemberData->gender != $memData["gender"]) {
				beneLogs(sesdata('id'), $memData["b_id"], "EDIT", "Sex", $oldMemberData->gender, $memData["gender"]);
			}
			if ($oldMemberData->marital_status_id != $memData["marital_status_id"]) {
				beneLogs(sesdata('id'), $memData["b_id"], "EDIT", "Marital Status", $oldMemberData->marital_status_id, $memData["marital_status_id"]);
			}
			if ($oldMemberData->sp_status != $memData["sp_status"]) {
				beneLogs(sesdata('id'), $memData["b_id"], "EDIT", "SP Status", $oldMemberData->sp_status, $memData["sp_status"]);
			}


			if ($oldMemberData->address != $memData["address"]) {
				beneLogs(sesdata('id'), $memData["b_id"], "EDIT", "Address", $oldMemberData->address, $memData["address"]);
			}
			if ($oldMemberData->barangay != $memData["barangay"]) {
				beneLogs(sesdata('id'), $memData["b_id"], "EDIT", "Barangay", $oldMemberData->barangay, $memData["barangay"]);
			}
			if ($oldMemberData->city != $memData["city"]) {
				beneLogs(sesdata('id'), $memData["b_id"], "EDIT", "City", $oldMemberData->city, $memData["city"]);
			}
			if ($oldMemberData->province != $memData["province"]) {
				beneLogs(sesdata('id'), $memData["b_id"], "EDIT", "Province", $oldMemberData->province, $memData["oldMemberData"]);
			}

			if ($oldMemberData->year_start != $memData["year_start"]) {
				beneLogs(sesdata('id'), $memData["b_id"], "EDIT", "Start Year", $oldMemberData->year_start, $memData["year_start"]);
			}
			if ($oldMemberData->quarter_start != $memData["quarter_start"]) {
				beneLogs(sesdata('id'), $memData["b_id"], "EDIT", "Start Quarter", $oldMemberData->quarter_start, $memData["quarter_start"]);
			}

			if ($oldMemberData->contactno != $memData["contactno"]) {
				beneLogs(sesdata('id'), $memData["b_id"], "EDIT", "Contact No", $oldMemberData->contactno, $memData["contactno"]);
			}
			if ($oldMemberData->affiliation != $memData["affiliation"]) {
				beneLogs(sesdata('id'), $memData["b_id"], "EDIT", "Affiliation", $oldMemberData->affiliation, $memData["affiliation"]);
			}
			if ($oldMemberData->birthdate != $memData["birthdate"]) {
				beneLogs(sesdata('id'), $memData["b_id"], "EDIT", "Birthdate", $oldMemberData->birthdate, $memData["birthdate"]);
			}
			if ($oldMemberData->birthplace != $memData["birthplace"]) {
				beneLogs(sesdata('id'), $memData["b_id"], "EDIT", "Birthplace", $oldMemberData->birthplace, $memData["birthplace"]);
			}
			if ($oldMemberData->hh_id != $memData["hh_id"]) {
				beneLogs(sesdata('id'), $memData["b_id"], "EDIT", "HH ID", $oldMemberData->hh_id, $memData["hh_id"]);
			}
			if ($oldMemberData->hh_size != $memData["hh_size"]) {
				beneLogs(sesdata('id'), $memData["b_id"], "EDIT", "HH SIZE", $oldMemberData->hh_size, $memData["hh_size"]);
			}
			if ($oldMemberData->osca_id != $memData["osca_id"]) {
				beneLogs(sesdata('id'), $memData["b_id"], "EDIT", "OSCA ID", $oldMemberData->osca_id, $memData["osca_id"]);
			}
			if ($oldMemberData->uct_status != $memData["uct_status"]) {
				beneLogs(sesdata('id'), $memData["b_id"], "EDIT", "UCT Status", $oldMemberData->uct_status, $memData["uct_status"]);
			}
			if ($oldMemberData->uct_id != $memData["uct_id"]) {
				beneLogs(sesdata('id'), $memData["b_id"], "EDIT", "UCT ID", $oldMemberData->uct_id, $memData["uct_id"]);
			}
			// if($oldMemberData->livingarrangement_id!=$livingarrangement_id){ beneLogs(sesdata('id'), $memData["b_id, "EDIT", "Living Arrangement",$oldMemberData->livingarrangement_id,$livingarrangement_id); }

			if ($oldMemberData->mothersMaidenName != $memData["mothersMaidenName"]) {
				beneLogs(sesdata('id'), $memData["b_id"], "EDIT", "Mother's Maiden Name", $oldMemberData->mothersMaidenName, $memData["mothersMaidenName"]);
			}
			if ($oldMemberData->localname != $memData["localname"]) {
				beneLogs(sesdata('id'), $memData["b_id"], "EDIT", "Local Name", $oldMemberData->localname, $memData["localname"]);
			}

			if ($oldMemberData->healthcondition != $memData["healthcondition"]) {
				beneLogs(sesdata('id'), $memData["b_id"], "EDIT", "Health Condition", $oldMemberData->healthcondition, $memData["healthcondition"]);
			}
			if ($oldMemberData->assistivedevice != $memData["assistivedevice"]) {
				beneLogs(sesdata('id'), $memData["b_id"], "EDIT", "Assistive Device", $oldMemberData->assistivedevice, $memData["assistivedevice"]);
			}
			if ($oldMemberData->housetype_id != $memData["housetype_id"]) {
				beneLogs(sesdata('id'), $memData["b_id"], "EDIT", "House Type", $oldMemberData->housetype_id, $memData["housetype_id"]);
			}
			if ($oldMemberData->remarks != $memData["remarks"]) {
				beneLogs(sesdata('id'), $memData["b_id"], "EDIT", "Remarks", $oldMemberData->remarks, $memData["remarks"]);
			}

			$data["success"] = TRUE;
			$data["message"] = "Social Pension data updated successfully.";
		} else {
			$success = false;
			$data["message"] = "Social Pension data not updated.";
		}

		response_json($data);
	}

	public function memberTransferInfo()
	{
		$curSPID = $this->input->post('spid');
		$qry = array(
			"select" => "*",
			"table" => "tblreplace",
			'type' => "row",
			'order' => array(
				'col' => "r_id",
				'order_by' => "DESC"),
			'condition' => ['replacee' => "$curSPID"],
		);
		$getReplacer = $this->Main->select($qry);
		$data['data'] = $getReplacer;
		$data['success'] = false;
		$data['message'] = "";

		if (!empty($getReplacer)) {
			$replaceeSPID = $getReplacer->replacer;
			$qryreplacer = array(
				"select" => 'connum,`lastname`,`firstname`,`middlename`, CONCAT(lastname,", ", firstname, " " ,middlename) as fullname,province, city, barangay',
				"table" => "tblgeneral",
				'type' => "row",
				'condition' => ['connum' => $replaceeSPID],
			);
			$replacerInfo =  $this->Main->select($qryreplacer);
			if (!empty($replacerInfo)) {
				$data['data'] = $replacerInfo;
				$data['success'] = true;
				$data['message'] = "success";
			}
		}

		response_json($data);
	}

	public function memberTransfer($type = true, $payrollID = "", $tranSPID = "",$spid = "")
	{

		if ($type == true) {
			// $payrollID = $this->input->post('payrollID');
			// $tranSPID = $this->input->post('tranSPID');
			// $curSPID = $this->input->post('curSPID');
			// $qryreplacer = array(
			// 	"select" => 'connum,`lastname`,`firstname`,`middlename`, CONCAT(lastname,", ", firstname, " " ,middlename) as fullname',
			// 	"table" => "tblgeneral",
			// 	'type' => "row",
			// 	'condition' => ['connum' => $tranSPID],
			// );
			// $replacerInfo =  $this->Main->select($qryreplacer);

			// //userLogs(sesdata('id') , sesdata('fullname') , "EDIT", "Transfered payment of $spid to $replacer_refcode for $mode_of_payment $period , $year");
			// //beneLogs(sesdata('id'), $b_id, "EDIT", "Transfered payment to $replacer_refcode for $mode_of_payment $period , $year",(NULL),(NULL));

			extract($_POST);
			$this->db->where(["p_id" => $p_id]);
			$setInactiveResult = $this->db->update("tblpayroll", array( 'liquidation'	=> 2, "remarks" => "Transfered to $tran_fullname")); 
	
			$dt = array(
				'spid'				=> $tranSPID, 
				"prov_code" 		=> $tran_provcode, 
				"mun_code" 			=> $tran_muncode, 
				"bar_code" 			=> $tran_barcode,
				'receiver'			=> $tran_fullname, 
				'amount'			=> $p_amount, 
				'liquidation' 		=> $p_liquidation, 
				'date_receive' 		=> $p_date_receive, 
				"year" 				=> $p_year, 
				"mode_of_payment"   => $p_mode_of_payment, 
				"period" 			=> $p_period,
				"eligible" 			=> 1, 
				"replaced" 			=> 1, 
			);
			$success = $this->db->insert("tblpayroll", $dt); 

			if($p_liquidation == 0){
				$pstat = "UNPAID";
			}else{
				$pstat = "PAID";
			}
	
			userLogs(sesdata('id') , sesdata('fullname') , "EDIT", "Transfered $pstat payment of $curSPID to $tranSPID for $p_mode_of_payment $p_period , $p_year");
			beneLogs(sesdata('id'), $curb_id, "EDIT", "Transfered $pstat payment to $tranSPID for $p_mode_of_payment $p_period , $p_year",(NULL),(NULL));
	
			$message = "Successfully Transfered $pstat payment to $tran_fullname for $p_mode_of_payment $p_period , $p_year";

		} else {
			$qryreplacer = array(
				"select" => '*,`lastname`,`firstname`,`middlename`, CONCAT(lastname,", ", firstname, " " ,middlename) as fullname',
				"table" => "tblwaitinglist",
				'type' => "row",
				'condition' => ['w_id' => $tranSPID],
			);
			$replacerInfo =  $this->Main->select($qryreplacer);
			$qry = array(
				"select" => "*",
				"table" => "tblpayroll",
				'type' => "row_array",
				'condition' => ['p_id' => "$payrollID"],
			);
			$getPayroll = $this->Main->select($qry);
			$getbar_code = $getPayroll["bar_code"];
			unset($getPayroll['p_id']);
			// $munlastid = $this->Main->raw("SELECT count(*) as munlastid FROM tblgeneral WHERE barangay='$getbar_code'", true)->munlastid;
			// $spid = $munlastid + 1;
			// $n = 1;
			// $cnt = str_pad($spid, 3, '0', STR_PAD_LEFT);
			// $spid = "SP" . $getbar_code . "-" . $cnt;
			$getPayroll['spid'] = $spid;
			$getPayroll['receiver'] = $replacerInfo->fullname;
			$success = $this->Main->insert('tblpayroll', $getPayroll);

			$this->Main->update('tblpayroll', ['p_id' => $payrollID], ['liquidation' => 2]);

			$message = "Transfer Success";
		}

		$data = [
			'success' => $success,
			'message' => $message,
		];
		response_json($data);
		// $this->Main->insert('tblpayroll',$data);
	}

	/////////////////////////// END - VIEW MEMBER DETAILS ///////////////////////////

	public function transferPaymentLocation()
	{	

		// $selected = explode(',', $this->input->post('selected'));
		$selected = json_decode($this->input->post('selected'), true);
		$selected_pids = (!empty($selected)) ? array_column($selected,'p_id') : [];
		$member = json_decode($this->input->post('member'), true);

		$curdate = date('Y-m-d H:i:s');
		//condition
		$p_id = $this->input->post('p_id');
		$spid = $this->input->post('spid');
		$year = $this->input->post('year');
		$period = $this->input->post('period');
		$remarks = $this->input->post('remarks');

		//update data
		$amount = $this->input->post('amount');
		$date_receive = $this->input->post('date_receive');
		$receiver = $this->input->post('receiver');
		$liquidation = $this->input->post('liquidation');

		$modepay = "SEMESTER";
		$qtrsemlogs = ["", "1st SEMESTER", "2nd SEMESTER"];
		$qtrsem = 1;
		if (in_array($period, [5, 6])) {
			$modepay = "SEMESTER";
			$qtrsem = ($period == 5) ? 1 : 2;
			if ($qtrsem == 1) {
				$qtrsemlogs = "1st SEMESTER";
			} else if ($qtrsem == 2) {
				$qtrsemlogs = "2nd SEMESTER";
			}
		} else {
			$qtrsem = $period;
			$modepay = "QUARTER";
			if ($qtrsem == 1) {
				$qtrsemlogs = "1st QUARTER";
			} else if ($qtrsem == 2) {
				$qtrsemlogs = "2nd QUARTER";
			} else if ($qtrsem == 3) {
				$qtrsemlogs = "3rd QUARTER";
			} else if ($qtrsem == 4) {
				$qtrsemlogs = "4th QUARTER";
			}
		}

		$update_data = [];
		$get_oldPaymentData = $this->Main->select(array('select' => "*", 'table' => "tblpayroll", 'condition' => ["p_id" => $selected_pids], 'type' => "result_array"));
		$oldPaymentData = !empty($get_oldPaymentData) ? array_column($get_oldPaymentData, NULL, 'p_id') : [];

		if(!empty($selected_pids)){
			foreach ($selected_pids as $key => $value) {
				$update_data[] = [
					"p_id" 		 => $value,
					"prov_code"  => $member['province'],
					"mun_code" 	 => $member['city'],
					"bar_code" 	 => $member['barangay'],
				];

				if(isset($oldPaymentData[$value])){

					$old_mem_payment = $oldPaymentData[$value];
					$year = $old_mem_payment['year'];
					$sem = $qtrsemlogs[$old_mem_payment['period']];

					$old_province = $oldPaymentData[$value]['prov_code'];
					$old_city = $oldPaymentData[$value]['mun_code'];
					$old_barangay = $oldPaymentData[$value]['bar_code'];

					$province = $member['province'];
					$city = $member['city'];
					$barangay = $member['barangay'];
					$spid = $member['SPID'];

					userLogs(sesdata('id'), sesdata('fullname'), "EDIT", "Updated Payment Location Details [province : $province, municipality : $city, barangay : $barangay of $spid for $year ($sem)");

					beneLogs(sesdata('id'), $member['b_id'], "EDIT", "Updated Payment Location Details [province : $old_province => $province, municipality : $old_city => $city, barangay : $old_barangay => $barangay for $year ($sem)",(NULL),(NULL));
				}

			}
		}

		$result = $this->Main->updatebatch("tblpayroll", $update_data, "p_id");

		if ($result) {
			$response = array(
				'success' => true,
				'message' => "Successfully Updated ",
			);
		} else {
			$response = array(
				'success' => false,
				'message' => "Something went wrong.",
			);
		}

		response_json($response);
	}

}
