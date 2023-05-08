<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Replacement extends CI_Controller {
	private $pager_settings;
	public function __construct() {
		parent::__construct();

		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('','');
        $this->load->model('Main', 'Main');
        $this->load->library('Pdf');
		$this->load->model("Replacement_model","rm");
		
		checkLogin();
	}

	public function index()
	{
        $data['app_active'] = true;

		//if(sesdata('role') == 1){

			$this->template->title('Replacements');
			$this->template->set_layout('default');
			$this->template->set_partial('header','partials/header');
			$this->template->set_partial('sidebar','partials/sidebar');
			$this->template->set_partial('aside','partials/aside');
			$this->template->set_partial('footer','partials/footer');
			$this->template->append_metadata('<script src="' . base_url("assets/js/pages/replacement/replacement.js?ver=") . filemtime(FCPATH. "assets/js/pages/replacement/replacement.js") . '"></script>');

			$this->template->build('replacement/replacement_view',$data);
	
		// }
	    // else
	    // {
	    //   redirect (base_url().'404_override');
	    // }	
	}
	
	/////////////////////////// ASSIGN REPLACER //////////////////////////////
	public function getAllPayroll()
	{
		$cmt = $this->input->post('claimant');
		$prov_code = $this->input->post('prov_code');
		$mun_code = "";
		$bar_code = "";
		$year = $this->input->post('year');
        $period_condition = 1;
		$type_sem_quart = "Semester";
		
        if($this->input->post('period') !== null && $this->input->post('period') != "")
        {
            $period = $this->input->post('period');
            if(in_array($period, [5,6])){
                $type_sem_quart = "Semester";
                $period_condition = ($period == 5)?1:2;
            }
            else{
                $period_condition = $period;
                $type_sem_quart = "quarter";
            }
		}		
		if($this->input->post('mun_code') !== null && $this->input->post('mun_code') != ""){
			$mun_code = $this->input->post('mun_code');
		}
		if($this->input->post('bar_code') !== null && $this->input->post('bar_code') != ""){
			$bar_code = $this->input->post('bar_code');
		}

		//GET LIBRARIES
		$generalList = $this->rm->get_all_general();
		$bene_ids = array_column($generalList, 'b_id', 'connum');
		$sp_status = array_column($generalList, 'sp_status', 'connum');
		$reason_ids = array_column($generalList, 'inactive_reason_id', 'connum');
		$reasons = array_column($generalList, 'sp_inactive_remarks', 'connum');
		$birthdates = array_column($generalList, 'birthdate', 'connum');
		$barcodes = array_column($generalList, 'barangay', 'connum');
		$muncodes = array_column($generalList, 'city', 'connum');

		$reasons_lib = $this->Main->getLibraries("tblinactivereason");
		$reasons_desc = array_column($reasons_lib , 'name', 'id');

		$municipalities = $this->Main->get_all_municipalities();
		$mun_names = array_column($municipalities, 'mun_name','mun_code');

		$bar_con["prov_code"] = $prov_code;
		if($mun_code != ""){ $bar_con["mun_code"] = $mun_code;}
		$barList = $this->Main->getBarangays($bar_con);
		$bar_names =  array_column($barList, 'bar_name', 'bar_code');

		$fullnameList = [];
		foreach($generalList as $key => $value) {
			$fullnameList[$value['connum']]= $value['lastname'].", ".$value['firstname']. " " . $value['middlename']. " " . $value['extensionname'];          
		}
		//END GET LIBRARIES

		$w_con = ["prov_code" => $prov_code, "archived" => 0, "priority" => 1];
		if($mun_code != ""){ $w_con["mun_code"] = $mun_code; }
		if($bar_code != ""){ $w_con["bar_code"] = $bar_code; }

		//WAITLIST DATA
		$waitlist = $this->rm->get_all_waitlist($w_con);
		$waitlist_data = [];
		$wbrgy_data = [];
		$wmun_data = [];
		
		$month = "01";
		$day = "01";

		if(in_array($period, [5,6])){
			$month = ($period == 5)?"01":"07";
		}
		else{
			if($period == 1){ $month = "01"; }
			elseif($period == 2){ $month = "04"; }
			elseif($period == 3){ $month = "07"; }
			elseif($period == 4){ $month = "10"; }
		}
		
		$end_date = "$year-$month-$day";

		if(!empty($waitlist)){

			foreach ($waitlist as $key => $value) {

				$fullname = $value['lastname'].", ".$value['firstname']. " " . $value['middlename']. " " . $value['extname'];

				$birthdate = $value['birthdate'];
				$d1 = new DateTime($birthdate);
				$d2 = new DateTime($end_date);

				$diff = $d2->diff($d1);
				$age = $diff->y;

				$w_barcode = $value['bar_code'];
				$w_muncode = $value['mun_code'];
				$w_provcode = $value['prov_code'];
				
				$mun_name = isset($mun_names[$w_muncode]) ? $mun_names[$w_muncode] : "";
				$bar_name = isset($bar_names[$w_barcode]) ? $bar_names[$w_barcode] : "";

				if($age > 59){
					$wmun_data[$w_muncode][] = array(
						"reference_code" => $value['reference_code'],
						"fullname" => $fullname,						
						"prov_code" => $value['prov_code'],
						"mun_code" => $value['mun_code'],
						"bar_code" => $w_barcode,
						"mun_name" => $mun_name,
						"bar_name" => $bar_name,
						"birthdate" => $birthdate,
						"age" => $age,
					);

					$wbrgy_data[$w_barcode][] = array(
						"reference_code" => $value['reference_code'],
						"fullname" => $fullname,						
						"prov_code" => $value['prov_code'],
						"mun_code" => $value['mun_code'],
						"bar_code" => $w_barcode,
						"mun_name" => $mun_name,
						"bar_name" => $bar_name,
						"birthdate" => $birthdate,
						"age" => $age,
					);
					
					$waitlist_data[] = array(
						"reference_code" => $value['reference_code'],
						"fullname" => $fullname,						
						"prov_code" => $value['prov_code'],
						"mun_code" => $value['mun_code'],
						"bar_code" => $w_barcode,
						"mun_name" => $mun_name,
						"bar_name" => $bar_name,
						"birthdate" => $birthdate,
						"age" => $age,
					);
				}

			}
			array_multisort(array_column($waitlist_data, 'age'), SORT_DESC, $waitlist_data);

			// //Arrange Active List
			foreach ($wbrgy_data as $key => $value) {
				array_multisort(array_column($wbrgy_data[$key], 'age'), SORT_DESC, $wbrgy_data[$key]);
			}

			// //Arrange Active List
			foreach ($wmun_data as $key => $value) {
				array_multisort(array_column($wmun_data[$key], 'age'), SORT_DESC, $wmun_data[$key]);
			}
		}

		//END WAITLIST DATA
		
		$condition = [ "prov_code" => $prov_code,
					"mode_of_payment"   => $type_sem_quart,
					"period"   => $period_condition,
					"year"      => $year,
					"liquidation" => 0];
		if($mun_code != ""){ $condition["mun_code"] = $mun_code; }
		if($bar_code != ""){ $condition["bar_code"] = $bar_code; }

		$payrollList = $this->rm->get_payroll_list($condition);

		$unpaid_data = [];

		if(!empty($payrollList)){			
			$replacements = $this->rm->get_all_replacement();
			$rep_list =  array_column($replacements, 'replacer', 'replacee');
			
			$used_replacers = [];
			foreach($payrollList as $key => $value){
				$spid = $value['spid'];
				$p_eligible = $value['eligible'];
				$liquidation = $value['liquidation'];
				
				$spstatus = isset($sp_status[$spid]) ? $sp_status[$spid] : "";

				if(strtoupper($spstatus) == "FORREPLACEMENT" || strtoupper($spstatus) == "INACTIVE" ){
					
					$b_id = isset($bene_ids[$spid]) ? $bene_ids[$spid] : "0";
					$fullname = isset($fullnameList[$spid]) ? $fullnameList[$spid] : "Not Found";
					$birthdate = isset($birthdates[$spid]) ? $birthdates[$spid] : "";
					$amount = $value['amount'];
					$date_receive = $value['date_receive'];
					 
					$reason_id = isset($reason_ids[$spid]) ? $reason_ids[$spid] : "";
					$remarks = isset($reasons[$spid]) ? $reasons[$spid] : "";

					$reasonDesc = isset($reasons_desc[$reason_id]) ? $reasons_desc[$reason_id] : "";

					if($remarks != ""){
						$reasonDesc .= " \n($remarks)";
					}

					$prov_code = $value['prov_code'];
					$mun_code = $value['mun_code'];
					$bar_code = $value['bar_code'];
					$bar_name = isset($bar_names[$bar_code]) ? $bar_names[$bar_code] : "";
					
					$mun_name = isset($mun_names[$mun_code]) ? $mun_names[$mun_code] : "";
					
					$replacer_refcode = "";
					$replacer_name = "";
					$replacer_age = "";
					$replacer_barname = "";
					$replacer_adress 	= "";
					$replacer_birthdate = "";
					
					$replacer_prov_code = "";
					$replacer_mun_code = "";
					$replacer_bar_code = "";

					$isReplaced 		= false;

					$replacer_refcode = isset($rep_list[$spid]) ? $rep_list[$spid] : "";
					if($replacer_refcode != ""){
						$rep_name 		= isset($fullnameList[$replacer_refcode]) ? $fullnameList[$replacer_refcode] : "Not Found";
						$isReplaced 		= true;
						$replacer_name 		= $replacer_refcode . ": " . $rep_name;
						$replacer_birthdate = isset($birthdates[$replacer_refcode]) ? $birthdates[$replacer_refcode] : "";

						//$end_date = "$year-$month-$day";

						$d1 = new DateTime($replacer_birthdate);
						$d2 = new DateTime($end_date);
		
						$diff = $d2->diff($d1);
						$age = $diff->y;

						$replacer_age 		= $age;

						
						$rep_bar = isset($barcodes[$replacer_refcode]) ? $barcodes[$replacer_refcode] : "";
						$rep_mun = isset($muncodes[$replacer_refcode]) ? $muncodes[$replacer_refcode] : "";
						$rep_bar_name = isset($bar_names[$rep_bar]) ? $bar_names[$rep_bar] : "";
						$rep_mun_name = isset($mun_names[$rep_mun]) ? $mun_names[$rep_mun] : "";

						$replacer_adress 	= $rep_bar_name. ", " .  $rep_mun_name;

						$replacer_prov_code = $prov_code;
						$replacer_mun_code = $rep_mun;
						$replacer_bar_code = $rep_bar;
						
					}

					// $rep_arrangement = $reason_id;
					// if($reason_id == 2){ $rep_arrangement = 20; }
					
					$unpaid_data[] = array(
						"b_id" 				=> $b_id,
						"spid" 				=> $spid,
						"fullname" 			=> $fullname,
						"birthdate" 		=> $birthdate,
						"reason_id" 		=> $reason_id,
						"amount" 			=> $amount,
						"date_receive" 			=> $date_receive,
						"reason" 			=> $reasonDesc,
						"remarks" 			=> $remarks,
						"spstatus" 			=> $spstatus,
						"prov_code" 		=> $prov_code,
						"mun_code" 			=> $mun_code,
						"bar_code" 			=> $bar_code,
						"bar_name" 			=> $bar_name,
						"mun_name" 			=> $mun_name,
						"adress" 			=> $bar_name . ", " .$mun_name,
						"liquidation" 		=> $liquidation,
						"replacer_name"		=> $replacer_name,
						"replacer_age"		=> $replacer_age,
						"replacer_barname"	=> $replacer_barname,
						"replacer_birthdate"=> $replacer_birthdate,
						"replacer_adress"	=> $replacer_adress,
						"isReplaced"		=> $isReplaced,
						"replacer_refcode"	=> $replacer_refcode,
						"replacer_prov_code" => $replacer_prov_code,
						"replacer_mun_code" => $replacer_mun_code,
						"replacer_bar_code" => $replacer_bar_code,
						"year"				=> $year,
						"mode_of_payment"   => $type_sem_quart,
						"period"   			=> $period_condition,
					);

				}
			}
			//array_multisort(array_column($unpaid_data, 'rep_arrangement'), SORT_ASC,array_column($unpaid_data, 'remarks'), SORT_ASC, $unpaid_data);

			//inter-barangay
			foreach ($unpaid_data as $key => $value) {

				if($value["replacer_refcode"] == "" && $value["liquidation"] != 4){
				//if($value["replacer_refcode"] == "" && $value["eligible"] != 4){

					// if($cmt == "0"){
					// 	if($value["eligible"] != 0){
					// 		continue;
					// 	}
					// }
					
					$replacer_refcode = "";
					$replacer_name = "";
					$replacer_age = "";
					$replacer_barname = "";
					$replacer_adress 	= "";
					$replacer_birthdate = "";
					$replacer_prov_code = "";
					$replacer_mun_code = "";
					$replacer_bar_code = "";

					$isReplaced 		= false;
					$replacers = isset($wbrgy_data[$value["bar_code"]]) ? $wbrgy_data[$value["bar_code"]] : array();
					if(!empty($replacers)){
						$replacer = array_shift($wbrgy_data[$value["bar_code"]]);
						if(!empty($replacer)){
							$replacer_name 		= $replacer["fullname"];
							$replacer_age 		= $replacer["age"];
							$replacer_barname 	= $replacer["bar_name"];
							$replacer_adress 	= $replacer["bar_name"]. ", " .  $replacer["mun_name"];
							$replacer_refcode 	= $replacer["reference_code"];
							$replacer_birthdate = $replacer["birthdate"];
							
							$replacer_prov_code = $replacer["prov_code"];
							$replacer_mun_code = $replacer["mun_code"];
							$replacer_bar_code = $replacer["bar_code"];

							$used_replacers[] = $replacer["reference_code"];
						}

					}

					$unpaid_data[$key]["replacer_name"] = $replacer_name;
					$unpaid_data[$key]["replacer_age"] = $replacer_age;
					$unpaid_data[$key]["replacer_barname"] = $replacer_barname;
					$unpaid_data[$key]["replacer_birthdate"] = $replacer_birthdate;
					$unpaid_data[$key]["replacer_adress"] = $replacer_adress;
					$unpaid_data[$key]["replacer_refcode"] = $replacer_refcode;

					$unpaid_data[$key]["replacer_prov_code"] = $replacer_prov_code;
					$unpaid_data[$key]["replacer_mun_code"] = $replacer_mun_code;
					$unpaid_data[$key]["replacer_bar_code"] = $replacer_bar_code;
				}

			}

			//inter-municipality
			foreach ($unpaid_data as $key => $value) {
				if($value["replacer_refcode"] == "" &&  $value["liquidation"] != 4){
				//if($value["replacer_refcode"] == "" && $value["eligible"] != 4){
					
					// if($cmt == "0"){
					// 	if($value["eligible"] != 0){
					// 		continue;
					// 	}
					// }

					$mun_replacers = isset($wmun_data[$value["mun_code"]]) ? $wmun_data[$value["mun_code"]] : array();

					$replacer_refcode = "";
					$replacer_name = "";
					$replacer_age = "";
					$replacer_barname = "";
					$replacer_adress 	= "";
					$replacer_birthdate = "";
					$replacer_prov_code = "";
					$replacer_mun_code = "";
					$replacer_bar_code = "";

					if(!empty($mun_replacers)){
						$munreplacer = array_shift($wmun_data[$value["mun_code"]]);
						if(in_array($munreplacer["reference_code"],$used_replacers)){
							while(in_array($munreplacer["reference_code"],$used_replacers)){
								
								if(empty($wmun_data[$value["mun_code"]])){
									$munreplacer = [];
									break;
								}

								$munreplacer = array_shift($wmun_data[$value["mun_code"]]);
							}
						}
						if(!empty($munreplacer)){
							$replacer_name 		= $munreplacer["fullname"];
							$replacer_age 		= $munreplacer["age"];
							$replacer_barname 	= $munreplacer["bar_name"];
							$replacer_adress 	= $munreplacer["bar_name"]. ", " .  $munreplacer["mun_name"];
							$replacer_refcode 	= $munreplacer["reference_code"];
							$replacer_birthdate = $munreplacer["birthdate"];
							
							$replacer_prov_code = $munreplacer["prov_code"];
							$replacer_mun_code = $munreplacer["mun_code"];
							$replacer_bar_code = $munreplacer["bar_code"];

							$used_replacers[] = $munreplacer["reference_code"];
						}
					}

					$unpaid_data[$key]["replacer_name"] = $replacer_name;
					$unpaid_data[$key]["replacer_age"] = $replacer_age;
					$unpaid_data[$key]["replacer_barname"] = $replacer_barname;
					$unpaid_data[$key]["replacer_birthdate"] = $replacer_birthdate;
					$unpaid_data[$key]["replacer_adress"] = $replacer_adress;
					$unpaid_data[$key]["replacer_refcode"] = $replacer_refcode;

					$unpaid_data[$key]["replacer_prov_code"] = $replacer_prov_code;
					$unpaid_data[$key]["replacer_mun_code"] = $replacer_mun_code;
					$unpaid_data[$key]["replacer_bar_code"] = $replacer_bar_code;
				}

			}

			//inter-provice
			foreach ($unpaid_data as $key => $value) {
				if($value["replacer_refcode"] == "" && $value["liquidation"] != 4){
					//if($value["replacer_refcode"] == "" && $value["eligible"] != 4){
						//$mun_replacers = isset($wmun_data[$value["mun_code"]]) ? $wmun_data[$value["mun_code"]] : array();

					// if($cmt == "0"){
					// 	if($value["eligible"] != 0){
					// 		continue;
					// 	}
					// }


					$replacer_refcode = "";
					$replacer_name = "";
					$replacer_age = "";
					$replacer_barname = "";
					$replacer_adress 	= "";
					$replacer_birthdate = "";
					$replacer_prov_code = "";
					$replacer_mun_code = "";
					$replacer_bar_code = "";

					if(!empty($waitlist_data)){
						$provreplacer = array_shift($waitlist_data);
						if(in_array($provreplacer["reference_code"],$used_replacers)){
							while(in_array($provreplacer["reference_code"],$used_replacers)){
								
								if(empty($wmun_data[$value["mun_code"]])){
									$provreplacer = [];
									break;
								}

								$provreplacer = array_shift($waitlist_data);
							}
						}
						if(!empty($provreplacer)){
							$replacer_name 		= $provreplacer["fullname"];
							$replacer_age 		= $provreplacer["age"];
							$replacer_barname 	= $provreplacer["bar_name"];
							$replacer_adress 	= $provreplacer["bar_name"]. ", " .  $provreplacer["mun_name"];
							$replacer_refcode 	= $provreplacer["reference_code"];
							$replacer_birthdate = $provreplacer["birthdate"];
							
							$replacer_prov_code = $provreplacer["prov_code"];
							$replacer_mun_code = $provreplacer["mun_code"];
							$replacer_bar_code = $provreplacer["bar_code"];

							$used_replacers[] = $provreplacer["reference_code"];
						}
					}

					$unpaid_data[$key]["replacer_name"] = $replacer_name;
					$unpaid_data[$key]["replacer_age"] = $replacer_age;
					$unpaid_data[$key]["replacer_barname"] = $replacer_barname;
					$unpaid_data[$key]["replacer_birthdate"] = $replacer_birthdate;
					$unpaid_data[$key]["replacer_adress"] = $replacer_adress;
					$unpaid_data[$key]["replacer_refcode"] = $replacer_refcode;

					$unpaid_data[$key]["replacer_prov_code"] = $replacer_prov_code;
					$unpaid_data[$key]["replacer_mun_code"] = $replacer_mun_code;
					$unpaid_data[$key]["replacer_bar_code"] = $replacer_bar_code;
				}

			}
			array_multisort(array_column($unpaid_data, 'bar_name'), SORT_ASC, array_column($unpaid_data, 'fullname'), SORT_ASC, $unpaid_data);
		}

		$data["unpaid_data"] = $unpaid_data;
		$data["waitlist_data"] = $waitlist_data;
		//$data["rep_list"] = $rep_list;

		response_json($data);
	}
	/////////////////////////// ASSIGN REPLACER //////////////////////////////

	/////////////////////////// TRANSFER PAYMENT //////////////////////////////

	public function TransferUnpaidSubmit(){

		extract($_POST);

		$this->db->where(["spid" => $spid, "year" => $year, "mode_of_payment"   => $mode_of_payment, "period" => $period]);
		$setInactiveResult = $this->db->update("tblpayroll", array( 'liquidation'	=> 2, "remarks" => "Transfered to $replacer_name")); 

		$dt = array(
			'spid'	=> $replacer_refcode, 
			'amount'	=> $amount, 
			'receiver'	=> $replacer_name, 
			'liquidation' => 0, 
			'date_receive' => $date_receive, 
			"year" => $year, 
			"mode_of_payment"   => $mode_of_payment, 
			"period" => $period,
			"prov_code" => $replacer_prov_code, 
			"mun_code" => $replacer_mun_code, 
			"bar_code" => $replacer_bar_code,
			"eligible" => 1, 
			"replaced" => 1, 
		);
		$result = $this->db->insert("tblpayroll", $dt); 

		userLogs(sesdata('id') , sesdata('fullname') , "EDIT", "Transfered payment of $spid to $replacer_refcode for $mode_of_payment $period , $year");
		beneLogs(sesdata('id'), $b_id, "EDIT", "Transfered payment to $replacer_refcode for $mode_of_payment $period , $year",(NULL),(NULL));

		$response = array(
			'success'=> true , 
			'message' => "Successfully Transfered payment to $replacer_name from $fullname for $mode_of_payment $period , $year",
		);
		response_json($response);
	}

	public function BulkTransferUnpaid(){
		$input_datas = $this->input->post('data');
		$rep_data = json_decode($input_datas); 

		// pdie($rep_data,1);
		
		$spids = [];
        foreach ($rep_data as $key => $value) {

			$b_id				= $value->b_id;
			$spid				= $value->spid;
			$year				= $value->year;
			$mode_of_payment	= $value->mode_of_payment;
			$period				= $value->period;
			$prov_code			= $value->prov_code;
			$mun_code			= $value->prov_code;
			$bar_code			= $value->bar_code;

			$replacer_refcode	= $value->replacer_refcode;
			$replacer_prov_code = $value->replacer_prov_code;
			$replacer_mun_code  = $value->replacer_mun_code;
			$replacer_bar_code  = $value->replacer_bar_code;

			$this->db->where(["spid" => $spid, "year" => $year, "mode_of_payment"   => $mode_of_payment, "period" => $period]);
			$setInactiveResult = $this->db->update("tblpayroll", array( 'spid'	=> $replacer_refcode, "eligible" => 1, "replaced" => 1, "prov_code" => $replacer_prov_code, "mun_code" => $replacer_mun_code, "bar_code" => $replacer_bar_code )); 
	
			$spids[] = $spid;
			userLogs(sesdata('id') , sesdata('fullname') , "EDIT", "Transfered payment of $spid to $replacer_refcode for $mode_of_payment $period , $year");
			beneLogs(sesdata('id'), $b_id, "EDIT", "Transfered payment to $replacer_refcode for $mode_of_payment $period , $year",(NULL),(NULL));
		}


		$response = array(
			'success'=> true , 
			'message' => "Successfully transfered payment of the following spids" . count($spids),
		);
		response_json($response);
	}

	/////////////////////////// END TRANSFER PAYMENT //////////////////////////////

	/////// REPLACE MEMBER //////////////////////////////////////////////

	public function ReplaceUnpaidSubmit(){
		extract($_POST);
		$new_spid = $this->ReplaceMemberSubmit($replacer_refcode, $b_id, $spid, $year, $period,$reason_id,$remarks,$mode_of_payment);
		$response = array(
			'success'=> true , 
			'message' => $new_spid,
		);
		response_json($response);
	}
	public function BulkReplaceUnpaid(){
		//extract($_POST);
		
		$input_datas = $this->input->post('data');
		$rep_data = json_decode($input_datas); 
		
		$spids = [];
        foreach ($rep_data as $key => $value) {
			$spids[] = $value->spid;

			$replacer_refcode = $value->replacer_refcode; 
			$b_id = $value->b_id; 
			$spid = $value->spid;
			$year = $value->year; 
			$period = $value->period;
			$reason_id = $value->reason_id;
			$remarks = $value->remarks;
			$mode_of_payment = $value->mode_of_payment;
			
			$new_spid = $this->ReplaceMemberSubmit($replacer_refcode, $b_id, $spid, $year, $period,$reason_id,$remarks,$mode_of_payment);
			
		}
		
		$response = array(
			'success'=> true , 
			'message' => "Successfully replaced the following spids" . count($spids),
		);
		response_json($response);
	}
	public function ReplaceMemberSubmit($reference_code, $b_id, $connum, $year_start, $period_start,$reason_id,$remarks,$mode_of_payment){

		//For new SPID
		$filtercondition = array( "reference_code" => $reference_code );
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
			'replacer' 		  			  => 1,							
		);
	
		//3. Insert to tbl_general
		$lastBeneId = $this->Main->insert("tblgeneral", $dataAdd,'lastid')['lastid'];

		//3.1 Insert to tblreplace
		$replaceAdd = array(
			'replacee' 			=> $connum,
			'replacer'   		=> $spid,
			'replacementdate'  	=> $dateofreplacement,
			'reason'   		   	=> $remarks,
			'reason_id'		   	=> $reason_id,	
			'user_name'		   	=> sesdata('fullname'),						
		);
		$insertRep = $this->Main->insert("tblreplace", $replaceAdd,'lastid');

		//4. Update tblbufanswers - Set spid = new_spid where spid = reference_no
		$this->db->where("spid",$reference_code);
		$setInactiveResult = $this->db->update("tblbufanswers", array( 'spid'	=> $spid )); 
	
		//5. Update tblgeneral set sp_status = "inactive" where b_id = $b_id
		$remarks = "Replaced by $waitlistmem->lastname, $waitlistmem->firstname [$spid]";
		$dataReplacee = array(
			'sp_status' 		      => 'Inactive',
			'sp_status_inactive_date' => $dateofreplacement,
			'remarks'   		      => $remarks
		);
		$this->db->where("connum",$connum);
		$setInactiveResult = $this->db->update("tblgeneral", $dataReplacee); 
		
		//6. Delete tblwaitinglist where w_id
		$this->db->where("reference_code",$reference_code);
		$query = $this->db->update("tblwaitinglist", ["archived" => 1, "new_spid" => $spid]); 
		//$query=$this->db->delete('tblwaitinglist');
	
		//Log
		userLogs(sesdata('id') , sesdata('fullname') , "EDIT", "Replace member: $connum replaced by $spid");
		beneLogs(sesdata('id'), $b_id, "EDIT", "Replaced by $spid",(NULL),(NULL));
		beneLogs(sesdata('id'), $lastBeneId, "EDIT", "Replaced $connum",(NULL),(NULL));

		return $spid;
	}

	///////// END REPLACE MEMBER /////////////////////////////////////////////


	public function setBeneStatus(){
		$curdate = date('Y-m-d H:i:s');

		//condition
		$spid = $this->input->post('spid');
        $prov_code = $this->input->post('prov_code');
        $mun_code = $this->input->post('mun_code');
        $bar_code = $this->input->post('bar_code');
		$year = $this->input->post('year');		
		$period = $this->input->post('period');

		//update data
		$amount = $this->input->post('amount');
		$date_receive = $this->input->post('date_receive');
		$receiver = $this->input->post('receiver');
		$liquidation = $this->input->post('liquidation');

        $modepay = "SEMESTER";
        $qtrsem = 1;
		if(in_array($period, [5,6])){
			$modepay = "SEMESTER";
			$qtrsem = ($period == 5)?1:2;
		}else{
			$qtrsem = $period;
			$modepay = "QUARTER";
		}
        if($modepay=="SEMESTER"){
            //$amount = 3000;
            if($qtrsem==1){ $qtrsemlogs="1st SEMESTER"; }
            else if($qtrsem==2){ $qtrsemlogs="2nd SEMESTER"; }
        }else if($modepay=="QUARTER"){
            //$amount = 1500;
            if($qtrsem==1){ $qtrsemlogs="1st QUARTER"; }
            else if($qtrsem==2){ $qtrsemlogs="2nd QUARTER"; }
            else if($qtrsem==3){ $qtrsemlogs="3rd QUARTER"; }
            else if($qtrsem==4){ $qtrsemlogs="4th QUARTER"; }
		}

		$data = array(
			'liquidation' => $liquidation,
			'amount'      => $amount,
			'receiver'      => $receiver,
			'date_receive' => $date_receive,
			'sp_dateupdated' => $curdate
		);
		
		$this->db->where("year",$year);
		$this->db->where("mode_of_payment",$modepay);
		$this->db->where("period",$qtrsem);
		$this->db->where("spid", $spid);
		$result = $this->db->update("tblpayroll", $data);

		if($result){
			$bid = getMemberDetails("b_id",["connum" => $spid])->b_id;
			if((int)$liquidation == 0) { $pstatus = "UNPAID";
			}else{ $pstatus = "PAID"; }

			userLogs(sesdata('id') , sesdata('fullname') , "EDIT", "Updated Payment to $pstatus of $spid for $year ($qtrsemlogs)");
			beneLogs(sesdata('id'), $bid, "EDIT", "Updated Payment to $pstatus of $spid for $year ($qtrsemlogs)",(NULL),(NULL));
			
			$response = array(
				'success'=> true , 
				'message' => "Successfully Updated " ,
			);

		}else{
			$response = array(
				'success'=> false , 
				'message' => "Something went wrong." ,
			);
		}
		
        response_json($response);
	}

	public function updatePaymentDetails(){
		$curdate = date('Y-m-d H:i:s');

		//condition
		$spid = $this->input->post('spid');
        $prov_code = $this->input->post('prov_code');
        $mun_code = $this->input->post('mun_code');
        $bar_code = $this->input->post('bar_code');
		$year = $this->input->post('year');		
		$period = $this->input->post('period');

		//update data
		$amount = $this->input->post('amount');
		$date_receive = $this->input->post('date_receive');
		$receiver = $this->input->post('receiver');

        $modepay = "SEMESTER";
        $qtrsem = 1;
		if(in_array($period, [5,6])){
			$modepay = "SEMESTER";
			$qtrsem = ($period == 5)?1:2;
		}else{
			$qtrsem = $period;
			$modepay = "QUARTER";
		}
        if($modepay=="SEMESTER"){
            //$amount = 3000;
            if($qtrsem==1){ $qtrsemlogs="1st SEMESTER"; }
            else if($qtrsem==2){ $qtrsemlogs="2nd SEMESTER"; }
        }else if($modepay=="QUARTER"){
           //$amount = 1500;
            if($qtrsem==1){ $qtrsemlogs="1st QUARTER"; }
            else if($qtrsem==2){ $qtrsemlogs="2nd QUARTER"; }
            else if($qtrsem==3){ $qtrsemlogs="3rd QUARTER"; }
            else if($qtrsem==4){ $qtrsemlogs="4th QUARTER"; }
		}

		$data = array(
			'amount'      => $amount,
			'receiver'      => $receiver,
			'date_receive' => $date_receive,
			'sp_dateupdated' => $curdate
		);
		
		$this->db->where("year",$year);
		$this->db->where("mode_of_payment",$modepay);
		$this->db->where("period",$qtrsem);
		$this->db->where("spid", $spid);
		$result = $this->db->update("tblpayroll", $data);

		if($result){
			$bid = getMemberDetails("b_id",["connum" => $spid])->b_id;
			userLogs(sesdata('id') , sesdata('fullname') , "EDIT", "Updated Payment Details of $spid for $year ($qtrsemlogs)");
			beneLogs(sesdata('id'), $bid, "EDIT", "Updated Payment Details of $spid for $year ($qtrsemlogs)",(NULL),(NULL));
			
			$response = array(
				'success'=> true , 
				'message' => "Successfully Updated " ,
			);

		}else{
			$response = array(
				'success'=> false , 
				'message' => "Something went wrong." ,
			);
		}
		
        response_json($response);
	}

    //Batch Payment
    public function BatchPayment(){
        ini_set('memory_limit', '999M');
        set_time_limit(0);
        ignore_user_abort(true);
        
        date_default_timezone_set("Asia/Manila");
		$curdate = date('Y-m-d H:i:s');
		
        //condition
		$input_spids = $this->input->post('spids');
		$spids = explode (",", $input_spids);  

        $prov_code = $this->input->post('prov_code');
        $mun_code = $this->input->post('mun_code');
        //$bar_code = $this->input->post('bar_code');
		$year = $this->input->post('year');		
		$period = $this->input->post('period');

		//update data
		$amount = $this->input->post('amount');
		$date_receive = $this->input->post('date_receive');
		$liquidation = $this->input->post('liquidation');

        $modepay = "Semester";
        $qtrsem = 1;
		if(in_array($period, [5,6])){
			$modepay = "Semester";
			$qtrsem = ($period == 5)?1:2;
		}else{
			$qtrsem = $period;
			$modepay = "Quarter";
		}
        if($modepay=="Semester"){
            //$amount = 3000;
            if($qtrsem==1){ $qtrsemlogs="1st SEMESTER"; }
            else if($qtrsem==2){ $qtrsemlogs="2nd SEMESTER"; }
        }else if($modepay=="Quarter"){
            //$amount = 1500;
            if($qtrsem==1){ $qtrsemlogs="1st QUARTER"; }
            else if($qtrsem==2){ $qtrsemlogs="2nd QUARTER"; }
            else if($qtrsem==3){ $qtrsemlogs="3rd QUARTER"; }
            else if($qtrsem==4){ $qtrsemlogs="4th QUARTER"; }
		}
		
		$memberidscount  = count($spids);
		if($memberidscount>0){ 
			$data = array(
				'liquidation' => $liquidation,
				'amount'      => $amount,
				'date_receive' => $date_receive,
				'sp_dateupdated' => $curdate
			);
			
			$result = $this->lm->batchPayment($year,$modepay,$qtrsem,$spids,$data);

			if($result){
				//$bid = getMemberDetails("b_id",["connum" => $spid])->b_id;
				//$bid = getMemberDetails("b_id",["connum" => $spid])->b_id;
				if((int)$liquidation == 0) { $pstatus = "UNPAID";
				}else{ $pstatus = "PAID"; }

				userLogs(sesdata('id') , sesdata('fullname') , "EDIT", "Batch Payment - $mun_code - $year - $qtrsem - liquidation - $pstatus - count - $memberidscount");

				//Bulk Update
				//beneLogs(sesdata('id'), $bid, "EDIT", "Updated Payment Details of $spid for $year ($qtrsemlogs)",(NULL),(NULL));
				
				$response = array(
					'success'=> true , 
					'message' => "Successfully Updated " ,
					'result'	=> $memberidscount
				);
	
			}else{
				$response = array(
					'success'=> FALSE , 
					'message' => "Something went wrong." ,
				);
			}
		} else{
			$response = array(
				'success'=> FALSE , 
				'message' => "There are no selected list. Please Select and try again" ,
			);
		}

        response_json($response);
    }

	public function dlPayrollList(){
		$prov_code = $this->input->get('prov_code');	//148100000
		$mun_code = ""; 
		$bar_code = "";
		$year = $this->input->get('year');
		$period_condition = 1;
		$type_sem_quart = "Semester";
		$period = $this->input->get('period');
		if(in_array($period, [5,6])){
			$type_sem_quart = "Semester";
			$period_condition = ($period == 5)?1:2;
		}
		else{
			$period_condition = $period;
			$type_sem_quart = "quarter";
		}
		
		$condition = [ "prov_code" => $prov_code,
					"mode_of_payment"   => $type_sem_quart,
					"period"   => $period_condition,
					"year"      => $year];

		
		if($this->input->get('mun_code') != ""){
			$mun_code = $this->input->get('mun_code');
			$condition["mun_code"] = $this->input->get('mun_code');
		}
		if($this->input->get('bar_code') != ""){
			$bar_code = $this->input->get('bar_code');
			$condition["bar_code"] = $this->input->get('bar_code');
		}

		$payrollList = $this->rm->get_payroll_list($condition);

		$provinces = $this->Main->get_all_provinces();
		$prov_names = array_column($provinces, 'prov_name','prov_code');
		$municipalities = $this->Main->get_all_municipalities();
		$mun_names = array_column($municipalities, 'mun_name','mun_code');

		$bar_con["prov_code"] =$prov_code;
		if($mun_code != ""){ $bar_con["mun_code"] = $mun_code;}
		$barList = $this->Main->getBarangays($bar_con);
		$bar_names =  array_column($barList, 'bar_name', 'bar_code');

		$provname = isset($prov_names[$prov_code]) ? $prov_names[$prov_code] : "";
		$munname = isset($mun_names[$mun_code]) ? $mun_names[$mun_code] : "";
		$count_spid = 0;

		$object = new Spreadsheet();
		$object->createSheet(0);
		$object->setActiveSheetIndex(0);
		$activeSheet =$object->getActiveSheet();
		$activeSheet->setTitle("ALL DATA");

		$activeSheet->getColumnDimension('A')->setWidth(5.29);
		$activeSheet->getColumnDimension('B')->setWidth(16.29);
		$activeSheet->getColumnDimension('C')->setWidth(16.57);
		$activeSheet->getColumnDimension('D')->setWidth(16.57);
		$activeSheet->getColumnDimension('E')->setWidth(16.57);
		$activeSheet->getColumnDimension('F')->setWidth(15);
		$activeSheet->getColumnDimension('G')->setWidth(15);
		$activeSheet->getColumnDimension('H')->setWidth(35);
		$activeSheet->getColumnDimension('I')->setWidth(17);
		$activeSheet->getColumnDimension('J')->setWidth(17);
		$activeSheet->getColumnDimension('K')->setWidth(34.43);
		$activeSheet->getColumnDimension('L')->setWidth(16.57);
		$activeSheet->getColumnDimension('M')->setWidth(10);
		$activeSheet->getColumnDimension('N')->setWidth(22.29);
		$activeSheet->getColumnDimension('O')->setWidth(17);
		$activeSheet->getColumnDimension('P')->setWidth(15);
		$activeSheet->getColumnDimension('Q')->setWidth(17);
		$activeSheet->getColumnDimension('R')->setWidth(17);
		$activeSheet->getColumnDimension('S')->setWidth(15);
		$activeSheet->getColumnDimension('T')->setWidth(15);

		$styleArray = [
			'font' => [
			'bold' => true,
					], 'borders' => [
						'allBorders' => [
							'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
						],
					],'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER_CONTINUOUS,
					'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
				],
			];

		$bodynamesp =  [
				'font' => [
				'bold' => false,
			], 'borders' => [
				'allBorders' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				],
			],'alignment' => [
			'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
			'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
			],
		];

		$excel_row=1;

		$table_columns = array("#","SPID", "LAST NAME", "FIRST NAME", "MIDDLE NAME", "EXTENSION NAME","BIRTHDATE (mm/dd/yyyy)", "REGION", "PROVINCE", "MUNICIPALITY", "BARANGAY", "PAYMENT STATUS", "SP STATUS", "REMARKS", "REPLACER NAME","REPLACER BIRTHDATE","REPLACER DATE OF REPLACEMENT", "REPLACEE NAME","REPLACEE BIRTHDATE","REPLACEE DATE OF REPLACEMENT" );
		$hs = "A";
		
		foreach ($table_columns as $tv) {
			$activeSheet->setCellValue($hs.$excel_row,$tv);
			$hs++;
		}
		$activeSheet->getStyle('A'.$excel_row.':T'.$excel_row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB("cbf2ce");
		$activeSheet->getStyle('A'.$excel_row.':T'.$excel_row)->applyFromArray($styleArray);
		$activeSheet->getStyle('A'.$excel_row.':T'.$excel_row)->getAlignment()->setWrapText(true);

		$excel_row++;
		$number = 1;

		if(!empty($payrollList)){
			$generalList = $this->lm->get_all_general();
			$firstnames = array_column($generalList, 'firstname', 'connum');
			$lastnames = array_column($generalList, 'lastname', 'connum');
			$middlenames = array_column($generalList, 'middlename', 'connum');
			$birthdates = array_column($generalList, 'birthdate', 'connum');
			$extensionnames = array_column($generalList, 'extensionname', 'connum');

			$sp_status = array_column($generalList, 'sp_status', 'connum');
			$inactive_id = array_column($generalList, 'inactive_reason_id', 'connum');
			$inactive_reason = array_column($generalList, 'sp_inactive_remarks', 'connum');

			foreach($payrollList as $key => $value){
				$count_spid +=1;
				$region = "CAR(Cordillera Administrative Region)";
				$bar_name = isset($bar_names[$value['bar_code']]) ? $bar_names[$value['bar_code']] : "";
				$prov_name = isset($prov_names[$value['prov_code']]) ? $prov_names[$value['prov_code']] : "";
				$mun_name = isset($mun_names[$value['mun_code']]) ? $mun_names[$value['mun_code']] : "";
				$bar_name .= "/" . $value['bar_code'];
				$prov_name .="/" . $value['prov_code'];
				$mun_name .= "/" . $value['mun_code'];

				$spid = $value['spid'];
				$first_name = isset($firstnames[$spid]) ? $firstnames[$spid] : "";
				$last_name = isset($lastnames[$spid]) ? $lastnames[$spid] : "";
				$middle_name = isset($middlenames[$spid]) ? $middlenames[$spid] : "";
				$ext_name = isset($extensionnames[$spid]) ? $extensionnames[$spid] : "";
				$spstatus = isset($sp_status[$spid]) ? $sp_status[$spid] : "";
				$reason_id = isset($inactive_id[$spid]) ? $inactive_id[$spid] : "";
				$reason = isset($inactive_reason[$spid]) ? $inactive_reason[$spid] : "";
				$b_date = isset($birthdates[$spid]) ? $birthdates[$spid] : "";
				//$date = date_create($b_date);
				//$birth_date =  date_format($b_date,"m/d/Y");
				$birth_date = date('m/d/Y', strtotime($b_date));

				$remarks = "";

				if((int)$reason_id == 1){ $remarks = "Double Entry";}	
				if((int)$reason_id == 2){ $remarks = "Deceased";}		
				if((int)$reason_id == 3){ $remarks = "With Regular Support";}
				if((int)$reason_id == 4){ $remarks = "With Pension";}
				if((int)$reason_id == 5){ $remarks = "Cannot be located";}
				if((int)$reason_id == 6){ $remarks = "Transferred";}
				if((int)$reason_id == 7){ $remarks = "Underage - age 59 and below";}
				if((int)$reason_id == 8){ $remarks = "Not Interested";}
				if((int)$reason_id == 11){ $remarks = "Improved Quality of Life";}
				if((int)$reason_id == 12){ $remarks = "With Regular Income";}
				if((int)$reason_id == 13){ $remarks = "Out of town";}
				if((int)$reason_id == 14){ $remarks = "Not Eligible";}
				if((int)$reason_id == 15){ $remarks = "OFW";}
				if((int)$reason_id == 16){ $remarks = "Barangay Official";}

				if(!empty($reason)){
					$remarks .= "( $reason )";
				}

				$liquidation = $value['liquidation'];
				$payment_status = "-";
				if((int)$liquidation == 0){
					$payment_status = "UNPAID";
				}else{
					$payment_status = "PAID";
				}

				$raplacee_name = "";
				$raplacee_date_of_replacement = "";
				$raplacee_birth_date = "";

				$raplacer_name = "";
				$raplacer_date_of_replacement = "";
				$raplacer_birth_date = "";

				//if($spstatus == "Inactive"){
					$repspid = $this->lm->getReplacementHistoryOfPensioner($spid,"replacee");

					if( $repspid != NULL) {
						$rep_spid = $repspid->replacer;
						$rep_first_name = isset($firstnames[$rep_spid]) ? $firstnames[$rep_spid] : "";
						$rep_last_name = isset($lastnames[$rep_spid]) ? $lastnames[$rep_spid] : "";
						$rep_middle_name = isset($middlenames[$rep_spid]) ? $middlenames[$rep_spid] : "";
						
						$raplacer_name = "$rep_spid - $rep_last_name , $rep_first_name $rep_middle_name";
						$raplacer_date_of_replacement = $repspid->replacementdate;
						$raplacer_date_of_replacement = date('m/d/Y', strtotime($raplacer_date_of_replacement));

						$raplacer_birth_date = isset($birthdates[$rep_spid]) ? $birthdates[$rep_spid] : "";
						$raplacer_birth_date = date('m/d/Y', strtotime($raplacer_birth_date));
					}
				//}else{
					$repspid = $this->lm->getReplacementHistoryOfPensioner($spid,"replacer");

					if( $repspid != NULL) {
						$rep_spid = $repspid->replacee;
						$rep_first_name = isset($firstnames[$rep_spid]) ? $firstnames[$rep_spid] : "";
						$rep_last_name = isset($lastnames[$rep_spid]) ? $lastnames[$rep_spid] : "";
						$rep_middle_name = isset($middlenames[$rep_spid]) ? $middlenames[$rep_spid] : "";
	
						$raplacee_name = "$rep_spid - $rep_last_name , $rep_first_name $rep_middle_name";
						$raplacee_date_of_replacement = $repspid->replacementdate;
						$raplacee_date_of_replacement = date('m/d/Y', strtotime($raplacee_date_of_replacement));

						$raplacee_birth_date = isset($birthdates[$rep_spid]) ? $birthdates[$rep_spid] : "";
						$raplacee_birth_date = date('m/d/Y', strtotime($raplacee_birth_date));
					}
				//}

				$activeSheet->setCellValue("A".$excel_row , $count_spid);
				$activeSheet->setCellValue("B".$excel_row , $spid);
				$activeSheet->setCellValue("C".$excel_row , $last_name);
				$activeSheet->setCellValue("D".$excel_row , $first_name);
				$activeSheet->setCellValue("E".$excel_row , $middle_name);
				$activeSheet->setCellValue("F".$excel_row , $ext_name);
				$activeSheet->setCellValue("G".$excel_row , $birth_date);
				$activeSheet->setCellValue("H".$excel_row , $region);
				$activeSheet->setCellValue("I".$excel_row , $prov_name);
				$activeSheet->setCellValue("J".$excel_row , $mun_name);
				$activeSheet->setCellValue("K".$excel_row , $bar_name);
				$activeSheet->setCellValue("L".$excel_row , $payment_status);
				$activeSheet->setCellValue("M".$excel_row , $spstatus);
				$activeSheet->setCellValue("N".$excel_row , $remarks);
				$activeSheet->setCellValue("O".$excel_row , $raplacer_name);
				$activeSheet->setCellValue("P".$excel_row , $raplacer_birth_date);
				$activeSheet->setCellValue("Q".$excel_row , $raplacer_date_of_replacement);
				$activeSheet->setCellValue("R".$excel_row , $raplacee_name);
				$activeSheet->setCellValue("S".$excel_row , $raplacee_birth_date);
				$activeSheet->setCellValue("T".$excel_row , $raplacee_date_of_replacement);
				
				$activeSheet->getStyle('A'.$excel_row.':T'.$excel_row)->applyFromArray($bodynamesp);
				$excel_row++;
			}
		}

		$dt = date("Y-m-d h-i-sa");
		$object->setActiveSheetIndex(0);
		$activeSheet->setSelectedCell('A1');
		
		$filename = "$provname-$munname($year-$type_sem_quart $period_condition)_payment_history_($count_spid)  $dt.xlsx";
		$writer = new Xlsx($object);

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename); 
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
	}

	public function generateCertificate(){
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', '60000');

		extract($_GET);

		$data = '';		
		$name = '';
		$new_data = [];
		$year_cond = (!empty($year)) ? $year : date('Y');
		
		$exportData = $this->_getReplacementData($prov_code,$mun_code,$bar_code,$year_cond);

		if(!empty($prov_code) && empty($mun_code) && empty($bar_code)){
			$name = getProvinces('prov_name',['prov_code'=>$prov_code],'row')->prov_name;	
			
			$municipalities = getMunicipalities('*',['prov_code'=>$prov_code]);			
			
			foreach ($municipalities as $k => $v) {
				foreach ($exportData['data'] as $kk => $vv) {										
					if($v->mun_code == $vv['replacee_muni']){
						$new_data[$v->mun_name][] = $vv;
					}
				}				
			}

		} else if(!empty($prov_code) && !empty($mun_code) && empty($bar_code)){
			$name = getMunicipalities('mun_name',['mun_code'=>$mun_code],'row')->mun_name;	

			$barangays = getBarangays('*',['mun_code'=>$mun_code]);			

			foreach ($exportData['data'] as $k => $v) {										
				if($mun_code == $v['replacee_muni']){
					$new_data[$name][] = $v;
				}
			}				


		}
				
		foreach ($new_data as $key => $value) {									
			$cert_data['province_name'] = $exportData['prov_name'];			
			$cert_data['municipality_name'] = (empty($exportData['mun_name']) ? $key : $exportData['mun_name'] );
			$cert_data['data'] = $value;
			$this->load->view('generated/replacement_certificate',$cert_data);
		}
				
		
        $html = $this->output->get_output();

		$dompdf = new \Dompdf\Dompdf();		

		$dompdf->set_option('isHtml5ParserEnabled', true);
		$dompdf->set_paper('A4', 'portrait');		
		$dompdf->loadHtml($html);
		$dompdf->render();
		$dompdf->stream($year . "_" . $name . ' Certification for Replacement.pdf', array("Attachment"=>0));
	}

	// START - EXPORT REPLACEMENT (CERTIFICATE OF REPLACEMENT)
	public function exportReplacement(){
		$year = $this->input->get('year');
		$prov_code = $this->input->get('prov_code');
		$mun_code = "";
		$bar_code = "";		


		$exportData = $this->_getReplacementData($prov_code,$mun_code,$bar_code,$year);
		$prov_name = $exportData['prov_name'];
		$mun_name = $exportData['mun_name'];
		if(!empty($exportData['data'])){
            
            $object = new Spreadsheet();
            $object->createSheet(0);
            $object->setActiveSheetIndex(0);
            $activeSheet =$object->getActiveSheet();
            $activeSheet->setTitle("Replacements");
    
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
            ]];
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
                    ]]];
            $textleft =
            [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'font'  => [
                    'size'  => 11,
                    'name' => 'Arial'
                ]];
            $textcenter =
            [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                'font'  => [
                    'size'  => 11,
                    'name' => 'Arial'
                ]];
            $border = 
            [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ]]];
    
    
            //data in sheet
            for($excel_row=1; $excel_row<=5; $excel_row++){ $activeSheet->getStyle('A'.$excel_row)->getFont()->setBold(true); }
            $activeSheet->mergeCells('A1:J1')->setCellValue('A1','PROVINCE OF '.mb_strtoupper($prov_name));
            $activeSheet->mergeCells('A2:J2')->setCellValue('A2','SOCIAL PENSION FOR INDIGENT SENIOR CITIZENS');
            $activeSheet->mergeCells('A4:J4')->setCellValue('A4','CERTIFICATE OF REPLACEMENT');
            $activeSheet->getStyle('A1:J4')->applyFromArray($headerstyle);
            $excel_row++;
            $activeSheet->mergeCells('A'.$excel_row.':J'.$excel_row)->setCellValue('A'.$excel_row,"       This is to certify that based on the downloaded list of eligible waitlist from DSWD – Central Office, the following barangay of Social Pension Beneficiaries to be replaced in $prov_name.");
            $activeSheet->getStyle('A'.$excel_row.':J'.$excel_row)->getAlignment()->setWrapText(true);
            $activeSheet->getStyle('A'.$excel_row.':J'.$excel_row)->applyFromArray($textleft);
            $activeSheet->getRowDimension($excel_row)->setRowHeight(100);
    
            $activeSheet->getColumnDimension('A')->setWidth(6); //no
            $activeSheet->getColumnDimension('B')->setWidth(22); //replacee spid
            $activeSheet->getColumnDimension('C')->setWidth(35); //replacee name
            $activeSheet->getColumnDimension('D')->setWidth(20); //replacee municipal
            $activeSheet->getColumnDimension('E')->setWidth(20); //replacee barangay
            $activeSheet->getColumnDimension('F')->setWidth(22); //replacer spid
            $activeSheet->getColumnDimension('G')->setWidth(35); //replacer name
            $activeSheet->getColumnDimension('H')->setWidth(20); //replacer municipal
            $activeSheet->getColumnDimension('I')->setWidth(20); //replacer barangay
            $activeSheet->getColumnDimension('J')->setWidth(20); //replacer period start
    
            $excel_row++;
            $table_columns = array("NO.", "REPLACEE SPID #", "REPLACEE NAME", "REPLACEE BARANGAY", "REPLACEE MUNICIPALITY","REPLACER SPID #", "REPLACER NAME", "REPLACER BARANGAY", "REPLACER MUNICIPALITY" , "PERIOD START");
            $hs = "A";
            foreach ($table_columns as $tv) { 
                $activeSheet->setCellValue($hs.$excel_row,$tv); $hs++; 
                $activeSheet->getStyle('A'.$excel_row.':J'.$excel_row)->applyFromArray($headerstyleborder);
                $activeSheet->getStyle('A'.$excel_row.':J'.$excel_row)->getFont()->setBold( true );
            }
            $excel_row++;
            $number = 1;
			$total_amount = 0;
			
			foreach($exportData['data'] as $ml){

				//print_r($ml);
				
				$activeSheet->setCellValue("A".$excel_row , (string)$number);
				$activeSheet->setCellValue("B".$excel_row , $ml["replacee_spid"]);
				$activeSheet->setCellValue("C".$excel_row , $ml["replacee_name"]);
				$activeSheet->setCellValue("D".$excel_row , $ml["replacee_bar_name"]);
				$activeSheet->setCellValue("E".$excel_row , $ml["replacee_mun_name"]);
				$activeSheet->setCellValue("F".$excel_row , $ml["replacer_spid"]);
				$activeSheet->setCellValue("G".$excel_row , $ml["replacer_name"]);
				$activeSheet->setCellValue("H".$excel_row , $ml["replacer_bar_name"]);
				$activeSheet->setCellValue("I".$excel_row , $ml["replacer_mun_name"]);
				$activeSheet->setCellValue("J".$excel_row , $ml["period_start"]);

				$activeSheet->getRowDimension($excel_row)->setRowHeight(16);
				$activeSheet->getStyle('A'.$excel_row.':J'.$excel_row)->applyFromArray($border);

				$number++;
				$excel_row++;
			}
        
            $excel_row=$excel_row+3;
    
            $uptomerge = $excel_row+1;
            $activeSheet->mergeCells('B'.$excel_row.':E'.$uptomerge)->setCellValue('B'.$excel_row,'            This certification is issued to support the claim of Social Pension stipend of the above cited replacement.');
            $activeSheet->getStyle('B'.$excel_row.':F'.$excel_row)->applyFromArray($textcenter);
            $activeSheet->getStyle('B'.$excel_row.':F'.$uptomerge)->getAlignment()->setWrapText(true);
            
            $excel_row=$excel_row+4;
    
            $signatories = getSignatories("sign1_name, sign1_position, sign2_name, sign2_position, sign3_name, sign3_position, sign4_name, sign4_position",array('file'=>"MASTERLIST"),"","row");
            $sign1_name = $signatories->sign1_name;
            $sign1_position = $signatories->sign1_position;
            $sign2_name = $signatories->sign2_name;
            $sign2_position = $signatories->sign2_position;
            $sign3_name = $signatories->sign3_name;
            $sign3_position = $signatories->sign3_position;
            $sign4_name = $signatories->sign4_name;
            $sign4_position = $signatories->sign4_position;
    
            $activeSheet->setCellValue("B".$excel_row , "Prepared By:");
            $activeSheet->setCellValue("D".$excel_row , "Recommending Approval:");
            $excel_row= $excel_row+3;
    
            $uptomerge = $excel_row+1;
            $activeSheet->mergeCells('B'.$excel_row.':C'.$excel_row)->setCellValue('B'.$excel_row,mb_strtoupper($sign3_name));
            $activeSheet->getStyle('B'.$excel_row)->getFont()->setUnderline(true)->setBold( true );
            $activeSheet->getStyle('B'.$excel_row.':F'.$uptomerge)->applyFromArray($textcenter);
            $activeSheet->mergeCells('D'.$excel_row.':F'.$excel_row)->setCellValue('D'.$excel_row,mb_strtoupper($sign2_name));
            $activeSheet->getStyle('D'.$excel_row)->getFont()->setUnderline(true)->setBold( true );
            $excel_row++;
            $activeSheet->mergeCells('B'.$excel_row.':C'.$excel_row)->setCellValue('B'.$excel_row,$sign3_position);
            $activeSheet->mergeCells('D'.$excel_row.':F'.$excel_row)->setCellValue('D'.$excel_row,$sign2_position);
            $excel_row = $excel_row+2;
    
            $activeSheet->mergeCells('B'.$excel_row.':F'.$excel_row)->setCellValue('B'.$excel_row,"Approved By:");
            $activeSheet->getStyle('B'.$excel_row.':F'.$excel_row)->applyFromArray($textcenter);
            $excel_row = $excel_row+3;
    
            $uptomerge = $excel_row+1;
            $activeSheet->mergeCells('B'.$excel_row.':F'.$excel_row)->setCellValue('B'.$excel_row,mb_strtoupper($sign4_name));
            $activeSheet->getStyle('B'.$excel_row)->getFont()->setUnderline(true)->setBold( true );
            $activeSheet->getStyle('B'.$excel_row.':F'.$uptomerge)->applyFromArray($textcenter);
            $excel_row++;
            $activeSheet->mergeCells('B'.$excel_row.':F'.$excel_row)->setCellValue('B'.$excel_row,$sign4_position);
    
            $activeSheet->setSelectedCell('A1');
    
            //file settings
            $activeSheet->getPageSetup()->setPrintArea('A:F');
			$activeSheet->setShowGridlines(true);
			
			$filename = "$year" . "_Replacements_" . $prov_name . "_" . $mun_name ."_" . $year . ".xlsx";
            
            $writer = new Xlsx($object);
    
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'. $filename); 
            header('Cache-Control: max-age=0');
    
            $writer->save('php://output');
    
        }else{
            show_404("NO RECORDS FOUND"); 
        }
	}

	private function _getReplacementData($prov_code='',$mun_code='',$bar_code='',$year="2019"){
		
		//GET LIBRARIES
		$generalList = $this->rm->get_all_general();
		$barcodes = array_column($generalList, 'barangay', 'connum');
		$muncodes = array_column($generalList, 'city', 'connum');
		$provcodes = array_column($generalList, 'province', 'connum');

        $provinces = $this->Main->get_all_provinces();
		$prov_names = array_column($provinces, 'prov_name','prov_code');
		$municipalities = $this->Main->get_all_municipalities();
		$mun_names = array_column($municipalities, 'mun_name','mun_code');
		$bar_con["prov_code"] = $prov_code;
		if($mun_code != ""){ $bar_con["mun_code"] = $mun_code;}
		$barList = $this->Main->getBarangays($bar_con);
		$bar_names =  array_column($barList, 'bar_name', 'bar_code');

		$fullnameList = [];
		foreach($generalList as $key => $value) {
			$fullnameList[$value['connum']]= $value['lastname'].", ".$value['firstname']. " " . $value['middlename']. " " . $value['extensionname'];          
		}

		$replacements = $this->rm->get_all_replacement();
		$rep_list =  array_column($replacements, 'replacee', 'replacer');
		//END GET LIBRARIES

		$exportData = [];

		foreach ($generalList as $key => $value) {

			if($value["replacer"] != 1){
				continue;
			}

			if($value["year_start"] != $year){
				continue;
			}

			$replacer_spid = $value["connum"];
			$replacer_name = strtoupper($value["lastname"] . ", " . $value["firstname"] . " " . $value["middlename"] . " " . $value["extensionname"]); 
			$replacer_prov = $value["province"];
			$replacer_muni = $value["city"];
			$replacer_brgy = $value["barangay"];
			$replacer_prov_name 		= isset($prov_names[$replacer_prov]) ? $prov_names[$replacer_prov] : "";
			$replacer_mun_name 		= isset($mun_names[$replacer_muni]) ? $mun_names[$replacer_muni] : "";
			$replacer_bar_name 		= isset($bar_names[$replacer_brgy]) ? $bar_names[$replacer_brgy] : "";
			$replacer_address = $replacer_bar_name . ", " . $replacer_mun_name . " " . $replacer_prov_name;

			$p_desc = "1st";
			if($value["quarter_start"] == 2){$p_desc = "2nd";}
			$period_start 	= strtoupper($value["year_start"] . " " . $p_desc . " " . $value["period_mode"]);

			//Replacer
			$replacee_spid = isset($rep_list[$replacer_spid]) ? $rep_list[$replacer_spid] : "Not Found";
			$replacee_name = isset($fullnameList[$replacee_spid]) ? $fullnameList[$replacee_spid] : "Not Found";
			$replacee_prov = isset($provcodes[$replacee_spid]) ? $provcodes[$replacee_spid] : "Not Found";
			$replacee_muni = isset($muncodes[$replacee_spid]) ? $muncodes[$replacee_spid] : "Not Found";
			$replacee_brgy = isset($barcodes[$replacee_spid]) ? $barcodes[$replacee_spid] : "Not Found";

			
			if($replacee_prov != $prov_code){
				continue;
			}

			$replacee_prov_name 	= isset($prov_names[$replacee_prov]) ? $prov_names[$replacee_prov] : "";
			$replacee_mun_name 		= isset($mun_names[$replacee_muni]) ? $mun_names[$replacee_muni] : "";
			$replacee_bar_name 		= isset($bar_names[$replacee_brgy]) ? $bar_names[$replacee_brgy] : "";

			if(!empty($replacee_bar_name)){
				$replacee_address = $replacee_bar_name . ", " . $replacee_mun_name . ", " . $replacee_prov_name;
			} else {
				$replacee_address = $replacee_mun_name . ", " . $replacee_prov_name;
			}

			$exportData[] = array(
				"replacee_spid" => $replacee_spid,
				"replacee_name" => $replacee_name,
				"replacee_prov" => $replacee_prov,
				"replacee_muni" => $replacee_muni,
				"replacee_brgy" => $replacee_brgy,
				"replacee_prov_name" => $replacee_prov_name,
				"replacee_mun_name" => $replacee_mun_name,
				"replacee_bar_name" => $replacee_bar_name,
				"replacee_address" => $replacee_address,
				"replacer_spid" => $replacer_spid,
				"replacer_name" => $replacer_name,
				"replacer_prov" => $replacer_prov,
				"replacer_muni" => $replacer_muni,
				"replacer_brgy" => $replacer_brgy,
				"replacer_prov_name" => $replacer_prov_name,
				"replacer_mun_name" => $replacer_mun_name,
				"replacer_bar_name" => $replacer_bar_name,
				"replacer_address" => $replacer_address,
				"period_start" => $period_start,
			);
		}

		//print_r(count($exportData));
		
		$prov_name = isset($prov_names[$prov_code]) ? $prov_names[$prov_code] : "";
		$mun_name = isset($mun_names[$mun_code]) ? $mun_names[$mun_code] : "";

		array_multisort(array_column($exportData, 'replacee_mun_name'), SORT_ASC,array_column($exportData, 'replacee_bar_name'), SORT_ASC,array_column($exportData, 'replacee_name'), SORT_ASC, $exportData);

		return [
			'data' 		=> $exportData,
			'prov_name' => $prov_name,
			'mun_name'  => $mun_name

		];

	}

// END - EXPORT MASTERLIST (CERTIFICATE OF ELIGIBILITY)

	// UNDO REPLACEMENT
	public function BulkReplaceMemberUndo(){
        $get_replacee = file_get_contents(FCPATH."/assets/json/kalinga_replacee_undo.json");
        $get_replacer = file_get_contents(FCPATH."/assets/json/kalinga_replacer_undo.json");

        $replacee_data = json_decode($get_replacee, true);
        $replacer_data = json_decode($get_replacer, true);
        $data = [];
        
        foreach ($replacee_data as $key => $value) {
        	$data = $this->ReplaceMemberUndo($replacer_data[$key], $value);
        }

		response_json($data);

	}

	public function ReplaceMemberUndo($getReplacerData = [], $getMemDetails = []){

		$replacerData = (empty($getReplacerData)) ? json_decode($_POST['replacerData'], true) : $getReplacerData;
		$memDetails = (empty($getMemDetails)) ? json_decode($_POST['memDetails'], true) : $getMemDetails;
		
		// pdie($replacerData,1);

		$connum = "";
		$spid = "";
		$lastBeneId = "";
		$b_id = "";
		$replacer_payments = [];
		$replacee_payments = [];
		$success = false;

		if(!empty($replacerData)){

			$mem_replacer = $this->Main->select([
				"select" => "*",
	            "table" => "tblgeneral",
				'type' => "row_array",
				'condition' => ['connum' => $replacerData['connum']],
			]);

			if(!empty($mem_replacer)){
				// delete replacer data to tblgeneral
				$success = $this->Main->delete("tblgeneral", ['connum' => $mem_replacer['connum']]); 

				$spid = $replacerData['connum'];
				$lastBeneId = $replacerData['b_id'];
			}

			//get waitlist data where new_spid = replacer spid
			$waitlistmem = $this->Main->select([
				"select" => "*",
	            "table" => "tblwaitinglist",
				'type' => "row_array",
				'condition' => ['new_spid' => $replacerData['connum']],
			]);

			if(!empty($waitlistmem)){
				// set archived = 0
				$success = $this->Main->update("tblwaitinglist", ['w_id' => $waitlistmem['w_id']], ['archived' => 0]); 

				// set spid to waitlist ref code
				$success = $this->Main->update("tblbufanswers", ['spid' => $waitlistmem['new_spid']], ['spid' => $waitlistmem['reference_code']]); 
			}
			
		}

		if(!empty($memDetails)){
			//get member data 
			$mem = $this->Main->select([
				"select" => "*",
	            "table" => "tblgeneral",
				'type' => "row_array",
				'condition' => ['connum' => $memDetails['connum']],
			]);

			if(!empty($mem)){
				// set status = ForReplacement
				$success = $this->Main->update("tblgeneral", ['connum' => $mem['connum']], ['sp_status' => 'ForReplacement']); 
				$connum = $memDetails['connum'];
				$b_id = $memDetails['b_id'];

			}
		}

		if(!empty($mem) && !empty($waitlistmem)){
			// remove replacement data in tblreplace
			$insertRep = $this->Main->delete("tblreplace", ['replacer' => $mem_replacer['connum'], 'replacee' => $memDetails['connum']]);
			
			userLogs(sesdata('id') , sesdata('fullname') , "EDIT", "Undo replacement of member: $connum replaced by $spid");
			beneLogs(sesdata('id'), $b_id, "EDIT", "Undo replacement by $spid",(NULL),(NULL));
			beneLogs(sesdata('id'), $lastBeneId, "EDIT", "Undo replacement of $connum",(NULL),(NULL));
		}


		$message = ($success) ? 'Successful undo of replacement' : 'Error';

		$data = [
			'success' => $success,
			'message' => $message,
		];

		response_json($data);
	}
}
