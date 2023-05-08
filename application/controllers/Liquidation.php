<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Liquidation extends CI_Controller {
	private $pager_settings;
	public function __construct() {
		parent::__construct();

		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('','');
        $this->load->model('Main', 'Main');
		$this->load->model("Liquidation_model","lm");
		$this->load->model("Replacement_model","rm");
		$this->load->model("Payroll_model","pm");
		
        $this->load->library('csvimport');
		$this->load->library('PHPExcel');
		
		checkLogin();
	}

	public function index()
	{
        $data['app_active'] = true;

		//if(isset($this->session->userdata['logged_in']) && $this->session->userdata['logged_in']){

		$this->template->title('Social Pension Active Beneficiaries');
		$this->template->set_layout('default');
	    $this->template->set_partial('header','partials/header');
	    $this->template->set_partial('sidebar','partials/sidebar');
	    $this->template->set_partial('aside','partials/aside');
	    $this->template->set_partial('footer','partials/footer');
	    $this->template->append_metadata('<script src="' . base_url("assets/js/pages/payroll/liquidation.js?ver=") . filemtime(FCPATH. "assets/js/pages/payroll/liquidation.js") . '"></script>');

	    $this->template->build('payroll/liquidation_view',$data);
	
		//}
	    // else
	    // {
	    //   redirect (base_url().'404_override');
	    // }	
	}
	
	public function getAllPayroll()
	{
		$prov_code = $this->input->post('prov_code');
		$mun_code = "";
		$bar_code = "";
		$year = $this->input->post('year');
		$payment_status = $this->input->post('liquidation');
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
		
		$condition = [ "prov_code" => $prov_code,
					"mode_of_payment"   => $type_sem_quart,
					"period"   => $period_condition,
					"year"      => $year,
					"liquidation <>" => 2];
					//"liquidation" => $payment_status];
		
		$ce_type = $this->input->post('type');
		if($ce_type != "all"){
			//$condition['additional'] = $ce_type;
			if($ce_type == "3"){
                $condition["additional"] = [1,2];
            }else{
                $condition["additional"] = $ce_type;
            }
		}
		
		if($this->input->post('mun_code') !== null && $this->input->post('mun_code') != ""){
			$mun_code = $this->input->post('mun_code');
			$condition["mun_code"] = $this->input->post('mun_code');
		}
		if($this->input->post('bar_code') !== null && $this->input->post('bar_code') != ""){
			$bar_code = $this->input->post('bar_code');
			$condition["bar_code"] = $this->input->post('bar_code');
		}

		$payrollList = $this->lm->get_total_served($condition);

		$data = [];
		$total_paid = 0;
		$total_unpaid = 0;
		$total_onhold = 0;
		$total_offset = 0;
		$total_target = 0;

		if(!empty($payrollList)){
			$generalList = $this->lm->get_all_general();

			$bar_con["prov_code"] =$prov_code;
			if($mun_code != ""){ $bar_con["mun_code"] = $mun_code;}
			$barList = $this->Main->getBarangays($bar_con);
			$bar_names =  array_column($barList, 'bar_name', 'bar_code');

			$fullnameList = [];
			foreach($generalList as $key => $value) {
			  $fullnameList[$value['connum']]= $value['lastname'].", ".$value['firstname']. " " . $value['middlename']. " " . $value['extensionname'];          
			}
			$sp_status = array_column($generalList, 'sp_status', 'connum');


			// if((int)$payment_status == 0){
			// 	$total_unpaid = count($payrollList);
			// 	$condition["liquidation"] = 1;
			// 	$total_paid = unpaid_member_list_count($condition)->total; 
			// }else{
			// 	$total_paid = count($payrollList);
			// 	$condition["liquidation"] = 0;
			// 	$total_unpaid = unpaid_member_list_count($condition)->total; 
			// }

			// $total_target = $total_unpaid + $total_paid;
			$total_target = count($payrollList);

			foreach($payrollList as $key => $value){

				if($value['liquidation'] == 0){ $total_unpaid++;}
				else if($value['liquidation'] == 1){ $total_paid++;}
				else if($value['liquidation'] == 3){ $total_offset++;}
				else if($value['liquidation'] == 4){ $total_onhold++;}

				if($value['liquidation'] <> $payment_status){
					continue;
				}

				$spid = $value['spid'];
				$amount = $value['amount'];
				$prov_code = $value['prov_code'];
				$mun_code = $value['mun_code'];
				$bar_code = $value['bar_code'];
				$receiver = $value['receiver'];
				$premarks = $value['remarks'];
				$date_receive = $value['date_receive'];
				$bar_name = isset($bar_names[$bar_code]) ? $bar_names[$bar_code] : "";
				$fullname = isset($fullnameList[$spid]) ? $fullnameList[$spid] : "Not Found";
				$spstatus = isset($sp_status[$spid]) ? $sp_status[$spid] : "";

				$timestamp = strtotime($date_receive);
				$new_date = date("m/d/Y", $timestamp);

				$data[] = array(
					"spid" => $spid,
					"fullname" => $fullname,
					"amount" => $amount,
					"receiver" => $receiver,
					"remarks" => $premarks,
					"date_receive" => $date_receive,
					"liquidation" => $payment_status,
					"spstatus" => $spstatus,
					"prov_code" => $prov_code,
					"mun_code" => $mun_code,
					"bar_code" => $bar_code,
					"bar_name" => $bar_name,
				);
			}
			array_multisort(array_column($data, 'bar_name'), SORT_ASC, array_column($data, 'fullname'), SORT_ASC, $data);
		}

		$data["data"] = $data;
		$data["total_target"] = number_format($total_target);
		$data["total_paid"] = number_format($total_paid);
		$data["total_unpaid"] = number_format($total_unpaid);
		$data["total_offset"] = number_format($total_offset);
		$data["total_onhold"] = number_format($total_onhold);
		$total_unclaimed = $total_unpaid + $total_offset + $total_onhold;
		$data["total_unclaimed"] = number_format($total_unclaimed);

		response_json($data);
	}

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
		$remarks = $this->input->post('remarks');

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
			'liquidation' 	=> $liquidation,
			'amount'      	=> $amount,
			'receiver'      => $receiver,
			'date_receive' 	=> $date_receive,
			'sp_dateupdated'=> $curdate,
			'remarks' 		=> $remarks
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
		$remarks = $this->input->post('remarks');
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

		$oldPaymentData = $this->lm->paymentDetails("*", array('spid' => $spid, 'year' => $year, 'mode_of_payment' => $modepay, 'period' => $qtrsem), array(), 'row');

		$data = array(
			'amount'      => $amount,
			'receiver'      => $receiver,
			'date_receive' => $date_receive,
			'sp_dateupdated' => $curdate,
			'remarks' => $remarks,
			'liquidation' => $liquidation
		);
		
		$this->db->where("year",$year);
		$this->db->where("mode_of_payment",$modepay);
		$this->db->where("period",$qtrsem);
		$this->db->where("spid", $spid);
		$result = $this->db->update("tblpayroll", $data);

		if($result){
			$bid = getMemberDetails("b_id",["connum" => $spid])->b_id;
			userLogs(sesdata('id') , sesdata('fullname') , "EDIT", "Updated Payment Details of $spid for $year ($qtrsemlogs)");
			
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

			// if ($oldPaymentData->date_receive != $date_receive) {
			// 	beneLogs(sesdata('id'), $bid, "EDIT", "$year ($qtrsemlogs) date receive", $oldPaymentData->date_receive, $date_receive);
			// }
			if ($oldPaymentData->liquidation != $liquidation) {
				$oldps = $this->getpaymentstat($oldPaymentData->liquidation);
				$newps = $this->getpaymentstat($liquidation);
				beneLogs(sesdata('id'), $bid, "EDIT", "$year ($qtrsemlogs) Payment Status", $oldps, $newps);
			}
			if ($oldPaymentData->remarks != $remarks) {
				beneLogs(sesdata('id'), $bid, "EDIT", "$year ($qtrsemlogs) Remarks", $oldPaymentData->remarks, $remarks);
			}

			//beneLogs(sesdata('id'), $bid, "EDIT", "Updated Payment Details of $spid for $year ($qtrsemlogs)",(NULL),(NULL));
			
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

	private function getpaymentstat($ps){
		if($ps == "0"){ return "UNPAID"; }
		if($ps == "1"){ return "PAID"; }
		if($ps == "2"){ return "TRANSFERRED"; }
		if($ps == "3"){ return "OFFSET"; }
		if($ps == "4"){ return "ONHOLD"; }
		else { return $ps;}
	}

	public function updatePaymentStatus(){

		date_default_timezone_set("Asia/Manila");
		$curdate = date('Y-m-d H:i:s');

		// if(!$this->input->is_ajax_request()){
		// 	show_404();
		// }

		ini_set('max_execution_time', '60000');
		ini_set('memory_limit', '999M'); 

		$config['upload_path']= "uploads/files/csv";
		$config['allowed_types']= "csv";
		// $config['file_name'] =
		if(!is_dir($config['upload_path'])){ 
			mkdir($config['upload_path'], 0777, TRUE);
		}
		$response=false;

		try{

			$this->load->library('upload', $config);

			if(!$this->upload->do_upload('file')){
				$error = array('error' => $this->upload->display_errors());
				$response['status']='Error';
				$response['message'] =  $error['error'];
				return $response;
			}else{
				//Get File Upload
				$path_folder = '/uploads/files/csv';
				$file_name = str_replace(" ", "_",$_FILES['file']['name']);
				$file_path = server_path.$path_folder."/".$file_name;
				$file_data = $this->csvimport->get_array($file_path,FALSE,FALSE,0);
				
				$generalList = $this->lm->get_all_general();
				$bids = array_column($generalList, 'b_id', 'connum');

				$payrollList = $this->lm->get_total_served(["year >" => "2018"]);
				$pids = [];
				$oldpayments = [];
				$receiverList = [];
				foreach ($payrollList as $key => $value) {
					$kp = strtoupper($value["mode_of_payment"]) . $value["period"];
					$pids[$value["year"]][$kp][$value["spid"]] = $value["p_id"];
					$oldpayments[$value["year"]][$kp][$value["spid"]] = $value["liquidation"];
					$receiverList[$value["year"]][$kp][$value["spid"]] = $value["receiver"];
				}
				
				$checkRow = 0;
				$num_rows = 0;
				$data = [];
				$benelogs = [];
								
				foreach($file_data as $row) {
					$num_rows++;

					$spid = $row["SPID #"];
					$pm_period = strtoupper($row["PERIOD"]);
					$pm_stat = strtoupper($row["PAYMENT STATUS"]);
					$year = $row["YEAR"];

					$period = 1;
					$mode_of_payment = "Semester";
					$liquidation = 0;

					if($pm_stat == "PAID"){ $liquidation = 1;
					}else{ $liquidation = 0; }

					// SET PERIOD
					if(strpos($pm_period, "1ST") !== false){ $period = 1; } 
					else if(strpos($pm_period, "2ND") !== false){ $period = 2; } 
					else if(strpos($pm_period, "3RD") !== false){ $period = 3; } 
					else if(strpos($pm_period, "4TH") !== false){ $period = 4; } 
					
					// SET MODE OF PAYMENT
					if(strpos($pm_period, "SEMESTER") !== false){ $mode_of_payment = "Semester";}
					else{ $mode_of_payment = "Quarter";}

					$where = array(
						'spid' 				=> $spid,
						'year' 				=> $row["YEAR"],
						'mode_of_payment' 	=> $mode_of_payment,
						'period' 			=> $period
					);

					$k_period = strtoupper($mode_of_payment) . $period;
					$p_id =  isset($pids[$year][$k_period][$spid]) ? $pids[$year][$k_period][$spid] : "";

					$receiver = $row["RECEIVER"];
					if(empty($receiver)){
						$receiver = isset($receiverList[$year][$k_period][$spid]) ? $receiverList[$year][$k_period][$spid] : "";
					}
					
					if(!empty($p_id)){
						$data[] = array(
							'p_id'				=> $p_id,
							'spid' 				=> $spid,
							'date_receive' 		=> $row["DATE OF PAYMENT"],
							'liquidation' 		=> $liquidation,
							'receiver'			=> $receiver,
							'remarks' 			=> $row["REMARKS"],
							'sp_dateupdated' 	=> $curdate
						);
					}

					$old_payment =  isset($oldpayments[$year][$k_period][$spid]) ? $oldpayments[$year][$k_period][$spid] : "";
					if($old_payment != $liquidation){
						$prev_edit = ($old_payment == 1) ? "PAID" : "UNPAID";
						$now_edit = ($liquidation == 1) ? "PAID" : "UNPAID";
						$bid = isset($bids[$spid]) ? $bids[$spid] : "";
						$field_edited = "Uploaded Payment Details of $spid for $year ($pm_period)";
						$benelogs[] = array(
							'b_id'			=> $bid,
							'user_id'		=> sesdata('id'),
							'action'		=> "EDIT",
							'field_edited'	=> $field_edited,
							'prev_edit'		=> $prev_edit,
							'now_edit'		=> $now_edit
						);
					}
				}
				
				$this->db->update_batch('tblpayroll', $data, 'p_id');
				$this->db->insert_batch('tblbeneficiary_editlogs', $benelogs);

				//confirm
				$response['status']='success';
				$response['message'] = "Success Update $num_rows Rows";
				$response['redirect'] = base_url('Waitlist/index');
			}

		}catch(Exception $e){
			$response['status']='invalid';
			$response['message'] = 'An error occured.';
		}

		unlink('uploads/files/csv/'.str_replace(" ", "_",$_FILES['file']['name']));
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
		//$spids = explode (",", $input_spids); 
		//$spids = json_decode($input_spids);  

		$selectedData = $this->input->post('selectedData');
		$paid_List = json_decode($selectedData); 

        $prov_code = $this->input->post('prov_code');
        $mun_code = $this->input->post('mun_code');
		$year = $this->input->post('year');		
		$period = $this->input->post('period');
		$saveType = $this->input->post('saveType');

		//update data
		// $amount = $this->input->post('amount');
		// $date_receive = $this->input->post('date_receive');
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
		
		$memberidscount  = count($paid_List);
		if($memberidscount>0){ 

			$generalList = $this->lm->get_all_general();
			$bids = array_column($generalList, 'b_id', 'connum');

			$pcon = array("year" => $year,"mode_of_payment" => $modepay,"period" => $qtrsem);

			$payrollList = $this->lm->get_total_served($pcon);
			$pids = array_column($payrollList, 'p_id', 'spid');
			$oldpayments = array_column($payrollList, 'liquidation', 'spid');

			$data = [];
			$benelogs = [];
			if((int)$liquidation == 0) { $pm_stat = "UNPAID";
			}else{ $pm_stat = "PAID"; }
							
			foreach ($paid_List as $key => $value) {

				$spid = $value->spid;
				$receiver = $value->receiver;
				$payment_remarks = $value->remarks;
				$paid_stat = $liquidation;
				$date_receive = $this->input->post('date_receive');
				if($saveType == 1){
					$paid_stat = $value->liquidation;
					$date_receive = $value->date_receive;
				}

				$p_id =  isset($pids[$spid]) ? $pids[$spid] : "";
				
				if(!empty($p_id)){
					$data[] = array(
						'p_id'				=> $p_id,
						'spid' 				=> $spid,
						'date_receive' 		=> $date_receive,
						'receiver' 			=> $receiver,
						'liquidation' 		=> $paid_stat,
						'remarks' 			=> $payment_remarks,
						'sp_dateupdated' 	=> $curdate
					);
				}

				$old_payment =  isset($oldpayments[$spid]) ? $oldpayments[$spid] : "";
				if($old_payment != $paid_stat){
					$prev_edit = ($old_payment == 1) ? "PAID" : "UNPAID";
					$now_edit = ($paid_stat == 1) ? "PAID" : "UNPAID";
					$bid = isset($bids[$spid]) ? $bids[$spid] : "";
					$field_edited = "Updated Payment Details of $spid for $year ($qtrsemlogs)";
					$benelogs[] = array(
						'b_id'			=> $bid,
						'user_id'		=> sesdata('id'),
						'action'		=> "EDIT",
						'field_edited'	=> $field_edited,
						'prev_edit'		=> $prev_edit,
						'now_edit'		=> $now_edit
					);
				}
			}
			
			if(!empty($data)){
				$this->db->update_batch('tblpayroll', $data, 'p_id');
				
				if(!empty($benelogs)){
					$this->db->insert_batch('tblbeneficiary_editlogs', $benelogs);
				}

				if($saveType == 1){
					$logtext = "Batch Save Details.";
				}else{
					$logtext = "Batch Payment";
				}
				userLogs(sesdata('id') , sesdata('fullname') , "EDIT", "$logtext - $mun_code - $year - $qtrsemlogs - liquidation - $pm_stat - count - $memberidscount");

				$response = array(
					'success'=> TRUE , 
					'message' => "Successfuly Saved $memberidscount selected beneficiaries payment details" ,
					'result'	=> $memberidscount
				);
			}else{
				$response = array(
					'success'=> FALSE , 
					'message' => "Empty Data" ,
					'result'	=> $pcon
				);
			}
			
		} else{
			$response = array(
				'success'=> FALSE , 
				'message' => "There are no selected list. Please Select and try again $selectedData" ,
			);
		}
        response_json($response);
	}
	
    public function BatchPayment1(){
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

		$ce_type = $this->input->get('type');
		if($ce_type != "all"){
			if($ce_type == "3"){
                $condition["additional"] = [1,2];
            }else{
                $condition["additional"] = $ce_type;
            }
		}

		$payrollList = $this->lm->get_total_served($condition);

		$provinces = $this->Main->get_all_provinces();
		$prov_names = array_column($provinces, 'prov_name','prov_code');
		$municipalities = $this->Main->get_all_municipalities();
		$mun_names = array_column($municipalities, 'mun_name','mun_code');

		$bar_con["prov_code"] =$prov_code;
		if($mun_code != ""){ $bar_con["mun_code"] = $mun_code;}
		$barList = $this->Main->getBarangays($bar_con, 0, ['col' => 'bar_name', 'order_by' => 'ASC']);
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
		$activeSheet->getColumnDimension('O')->setWidth(22.29);
		$activeSheet->getColumnDimension('P')->setWidth(17);
		$activeSheet->getColumnDimension('Q')->setWidth(15);
		$activeSheet->getColumnDimension('R')->setWidth(17);
		$activeSheet->getColumnDimension('S')->setWidth(17);
		$activeSheet->getColumnDimension('T')->setWidth(15);
		$activeSheet->getColumnDimension('U')->setWidth(15);

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

		$table_columns = array("#","SPID", "LAST NAME", "FIRST NAME", "MIDDLE NAME", "EXTENSION NAME","BIRTHDATE (mm/dd/yyyy)", "REGION", "PROVINCE", "MUNICIPALITY", "BARANGAY", "PAYMENT STATUS", "SP STATUS", "INACTIVE REASON", "REMARKS", "REPLACER NAME","REPLACER BIRTHDATE","REPLACER DATE OF REPLACEMENT", "REPLACEE NAME","REPLACEE BIRTHDATE","REPLACEE DATE OF REPLACEMENT" );
		$hs = "A";
		
		foreach ($table_columns as $tv) {
			$activeSheet->setCellValue($hs.$excel_row,$tv);
			$hs++;
		}
		$activeSheet->getStyle('A'.$excel_row.':U'.$excel_row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB("cbf2ce");
		$activeSheet->getStyle('A'.$excel_row.':U'.$excel_row)->applyFromArray($styleArray);
		$activeSheet->getStyle('A'.$excel_row.':U'.$excel_row)->getAlignment()->setWrapText(true);

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
			
			$all_data = [];
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

				// add remarks from payroll table
				$payroll_remarks = $value['remarks'];

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

				$all_data[] = [
						'count_spid' => $count_spid,
						'spid' => $spid,
						'last_name' => $last_name,
						'first_name' => $first_name,
						'middle_name' => $middle_name,
						'ext_name' => $ext_name,
						'birth_date' => $birth_date,
						'region' => $region,
						'prov_name' => $prov_name,
						'mun_name' => $mun_name,
						'bar_name' => $bar_name,
						'payment_status' => $payment_status,
						'spstatus' => $spstatus,
						'remarks' => $remarks,
						'payroll_remarks' => $payroll_remarks,
						'raplacer_name' => $raplacer_name,
						'raplacer_birth_date' => $raplacer_birth_date,
						'raplacer_date_of_replacement' => $raplacer_date_of_replacement,
						'raplacee_name' => $raplacee_name,
						'raplacee_birth_date' => $raplacee_birth_date,
						'raplacee_date_of_replacement' => $raplacee_date_of_replacement,
				];
			}

    		array_multisort(array_column($all_data, 'mun_name'), SORT_ASC, array_column($all_data, 'bar_name'), SORT_ASC, array_column($all_data, 'last_name'), SORT_ASC, $all_data);
			foreach ($all_data as $key => $value) {
				$activeSheet->setCellValue("A".$excel_row , $value['count_spid']);
				$activeSheet->setCellValue("B".$excel_row , $value['spid']);
				$activeSheet->setCellValue("C".$excel_row , $value['last_name']);
				$activeSheet->setCellValue("D".$excel_row , $value['first_name']);
				$activeSheet->setCellValue("E".$excel_row , $value['middle_name']);
				$activeSheet->setCellValue("F".$excel_row , $value['ext_name']);
				$activeSheet->setCellValue("G".$excel_row , $value['birth_date']);
				$activeSheet->setCellValue("H".$excel_row , $value['region']);
				$activeSheet->setCellValue("I".$excel_row , $value['prov_name']);
				$activeSheet->setCellValue("J".$excel_row , $value['mun_name']);
				$activeSheet->setCellValue("K".$excel_row , $value['bar_name']);
				$activeSheet->setCellValue("L".$excel_row , $value['payment_status']);
				$activeSheet->setCellValue("M".$excel_row , $value['spstatus']);
				$activeSheet->setCellValue("N".$excel_row , $value['remarks']);
				$activeSheet->setCellValue("O".$excel_row , $value['payroll_remarks']);
				$activeSheet->setCellValue("P".$excel_row , $value['raplacer_name']);
				$activeSheet->setCellValue("Q".$excel_row , $value['raplacer_birth_date']);
				$activeSheet->setCellValue("R".$excel_row , $value['raplacer_date_of_replacement']);
				$activeSheet->setCellValue("S".$excel_row , $value['raplacee_name']);
				$activeSheet->setCellValue("T".$excel_row , $value['raplacee_birth_date']);
				$activeSheet->setCellValue("U".$excel_row , $value['raplacee_date_of_replacement']);
				
				$activeSheet->getStyle('A'.$excel_row.':U'.$excel_row)->applyFromArray($bodynamesp);
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

	public function dlPaidRegistry(){
		error_reporting(E_ERROR | E_PARSE);
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', '60000');

		// $year = "2020";		
		$year = date("Y");		
		$condition = ["year" => $year];
		// $condition = ["year" => $year, 'mun_code' => '148102000'];
		$payrollList = $this->lm->get_total_served($condition);
		$ref_ids = array_column($payrollList, 'spid');

		$provinces = $this->Main->get_all_provinces();
		$prov_names = array_column($provinces, 'prov_name','prov_code');
		$municipalities = $this->Main->get_all_municipalities();
		$mun_names = array_column($municipalities, 'mun_name','mun_code');
		$barList = $this->Main->getBarangays();
		$bar_names =  array_column($barList, 'bar_name', 'bar_code');

		$count_spid = 0;
		$excel_array = [];
		$region = "CAR(Cordillera Administrative Region)";


		if(!empty($payrollList)){

			$getGeneralList = $this->lm->get_all_general(['connum' => $ref_ids]);
			$generalList = array_column($getGeneralList, NULL, 'connum');

			foreach ($payrollList as $key => $value) {
				
				//remove transferred payment and inactive, forreplacement status
				if($value['liquidation'] == 2){
					continue;
				}

				$excel_array[$value['spid']]['ref_id'] = $value['spid'];

				$excel_array[$value['spid']]['fullname'] = $value['receiver'];
				$excel_array[$value['spid']]['lastname'] = $generalList[$value['spid']]['lastname'];
				$excel_array[$value['spid']]['firstname'] = $generalList[$value['spid']]['firstname'];
				$excel_array[$value['spid']]['middlename'] = $generalList[$value['spid']]['middlename'];
				$excel_array[$value['spid']]['extensionname'] = $generalList[$value['spid']]['extensionname'];

				$bar_name = isset($bar_names[$value['bar_code']]) ? $bar_names[$value['bar_code']] : "";
				$prov_name = isset($prov_names[$value['prov_code']]) ? $prov_names[$value['prov_code']] : "";
				$mun_name = isset($mun_names[$value['mun_code']]) ? $mun_names[$value['mun_code']] : "";
				$bar_name .= "/" . $value['bar_code'];
				$prov_name .="/" . $value['prov_code'];
				$mun_name .= "/" . $value['mun_code'];

				$excel_array[$value['spid']]['region'] = $region;
				$excel_array[$value['spid']]['prov_name'] = $prov_name;
				$excel_array[$value['spid']]['mun_name'] = $mun_name;
				$excel_array[$value['spid']]['bar_name'] = $bar_name;

				if($value['period'] == 1){
					$excel_array[$value['spid']]['first_paid'] = ((int)$value['liquidation'] == 0) ? 'UNPAID' : 'PAID';
					$excel_array[$value['spid']]['first_amount'] = ((int)$value['liquidation'] == 0) ? 0 : $value['amount'];
				}

				if($value['period'] == 2){
					$excel_array[$value['spid']]['second_paid'] = ((int)$value['liquidation'] == 0) ? 'UNPAID' : 'PAID';
					$excel_array[$value['spid']]['second_amount'] = ((int)$value['liquidation'] == 0) ? 0 : $value['amount'];
				}

				$remarks = "Active";
				$reason_id = $generalList[$value['spid']]['inactive_reason_id'];
				$reason = isset($generalList[$value['spid']]['sp_inactive_remarks']) ? $generalList[$value['spid']]['sp_inactive_remarks'] : "";


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

				$excel_array[$value['spid']]['remarks'] = $remarks;
				$excel_array[$value['spid']]['date_edit'] = date('m/d/Y', strtotime($value['date_receive']));

			}

		}

		array_multisort( array_column($excel_array, 'prov_name'), SORT_ASC,  array_column($excel_array, 'mun_name'), SORT_ASC, array_column($excel_array, 'bar_name'), SORT_ASC,$excel_array );

		$export_data[] = [
			"REF. ID" => "REF. ID",
			"FULLNAME" => "FULLNAME",
			"LAST NAME" => "LAST NAME",
			"FIRST NAME" => "FIRST NAME",
			"MIDDLE NAME" => "MIDDLE NAME",
			"EXT. NAME" => "EXT. NAME",
			"REGION" => "REGION",
			"PROVINCE" => "PROVINCE",
			"CITY/MUNICIPALITY" => "CITY/MUNICIPALITY",
			"BARANGAY" => "BARANGAY",
			"PAID/UNPAID 1ST-SEM (2020)" => "PAID/UNPAID \n 1ST-SEM ($year)",
			"AMOUNT CLAIMED 1ST-SEM (2020)" => "AMOUNT CLAIMED \n 1ST-SEM ($year)",
			"PAID/UNPAID 2nd-SEM (2020)" => "PAID/UNPAID \n 2nd-SEM ($year)",
			"AMOUNT CLAIMED 2nd-SEM (2020)" => "AMOUNT CLAIMED \n 2nd-SEM ($year)",
			"REMARKS" => "REMARKS",
			"DATE EDIT" => "DATE EDIT",
			"TIME" => "TIME",
		];
		
    	$row = 1;

		if(!empty($excel_array)){
			foreach ($excel_array as $key => $value) {
				$first_paid = (isset($value['first_paid']) && $value['first_paid'] != "") ? $value['first_paid'] : " ";
				$first_amount = (isset($value['first_amount']) && $value['first_amount'] != "") ? $value['first_amount'] : "-";
				$second_paid = (isset($value['second_paid']) && $value['second_paid'] != "") ? $value['second_paid'] : " ";
				$second_amount = (isset($value['second_amount']) && $value['second_amount'] != "") ? $value['second_amount'] : "-";
				$remarks = (isset($value['remarks']) && $value['remarks'] != "") ? $value['remarks'] : " ";

				$export_data[] = [
					"REF. ID" => $value['ref_id'],
					"FULLNAME" => $value['fullname'],
					"LAST NAME" => $value['lastname'],
					"FIRST NAME" => $value['firstname'],
					"MIDDLE NAME" => $value['middlename'],
					"EXT. NAME" => $value['extensionname'],
					"REGION" => $value['region'],
					"PROVINCE" => $value['prov_name'],
					"CITY/MUNICIPALITY" => $value['mun_name'],
					"BARANGAY" => $value['bar_name'],
					"PAID/UNPAID 1ST-SEM (2020)" => $first_paid,
					"AMOUNT CLAIMED 1ST-SEM (2020)" => $first_amount,
					"PAID/UNPAID 2nd-SEM (2020)" => $second_paid,
					"AMOUNT CLAIMED 2nd-SEM (2020)" => $second_amount,
					"REMARKS" => $remarks,
					"DATE EDIT" => $value['date_edit'],
					"TIME" => " ",
				];
				$row++;
			}
		}

		$spreadsheet = new Spreadsheet();

		$styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];
    	
		$styleHeaderArray = [
    		'font' => [
                'bold' => true,
            ],
        ];

        $styleCenter = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER_CONTINUOUS,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];   
        
        $spreadsheet->setActiveSheetIndex(0);
    	$filename = 'Paid Registry of SocPen FOCAR';
        $spreadsheet->getActiveSheet()->setTitle('Paid Registry');

        foreach (range('A','Q') as $columnID)
        {
            $spreadsheet->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);            
        }


        $spreadsheet->getActiveSheet()->fromArray($export_data,NULL,"A1");
        $spreadsheet->getActiveSheet()->getStyle("A1:Q1")->applyFromArray($styleHeaderArray);
        $spreadsheet->getActiveSheet()->getStyle("A1:Q" . $row)->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getStyle("K1:Q" . $row)->applyFromArray($styleCenter);
        $spreadsheet->getActiveSheet()->getStyle("A1:Q" . $row)->getAlignment()->setWrapText(true);
 	
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        
        $writer = new Xlsx($spreadsheet);        
        $writer->save('php://output');
	}

	public function dlPayrollList1(){
		$prov_code = "";
		$mun_code = ""; 
		$bar_code = "";
		$year = "2020";
		$period_condition = $this->input->get('period');
		$type_sem_quart = $this->input->get('mode');		
		$period = $this->input->get('period');

		$condition = [ "mode_of_payment"   => $type_sem_quart,
					"period"   => $period_condition,
					"year"      => $year,
					"liquidation"      => 0];

		$payrollList = $this->lm->get_total_served($condition);

		$provinces = $this->Main->get_all_provinces();
		$prov_names = array_column($provinces, 'prov_name','prov_code');
		$municipalities = $this->Main->get_all_municipalities();
		$mun_names = array_column($municipalities, 'mun_name','mun_code');
		$barList = $this->Main->getBarangays();
		$bar_names =  array_column($barList, 'bar_name', 'bar_code');

		$provname = "CAR";
		$munname = "ALL";
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
				$spstatus = isset($sp_status[$spid]) ? $sp_status[$spid] : "";

				if(strtoupper($spstatus) != "FORREPLACEMENT" && strtoupper($spstatus) != "INACTIVE"){
					continue;
				}

				$first_name = isset($firstnames[$spid]) ? $firstnames[$spid] : "";
				$last_name = isset($lastnames[$spid]) ? $lastnames[$spid] : "";
				$middle_name = isset($middlenames[$spid]) ? $middlenames[$spid] : "";
				$ext_name = isset($extensionnames[$spid]) ? $extensionnames[$spid] : "";
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

	
// START - PREAUDIT DISBURSEMENT REPORT ////////////

	public function payrollreports(){
		$data['app_active'] = true;

		//if(isset($this->session->userdata['logged_in']) && $this->session->userdata['logged_in']){

		$this->template->title('Payroll Reports');
		$this->template->set_layout('default');
		$this->template->set_partial('header','partials/header');
		$this->template->set_partial('sidebar','partials/sidebar');
		$this->template->set_partial('aside','partials/aside');
		$this->template->set_partial('footer','partials/footer');
		$this->template->append_metadata('<script src="' . base_url("assets/js/pages/payroll/liqsummary.js?ver=") . filemtime(FCPATH. "assets/js/pages/payroll/liqsummary.js") . '"></script>');

		$this->template->build('payroll/liqsummary_view',$data);
	}

	public function generatePayrollReports(){

		//$liq_type  		= $this->input->get('type');
		$province       = $this->input->get('prov_code');
		$municipality   = $this->input->get('mun_code');
		$year           = $this->input->get('year');
		$claimant       = $this->input->get('claimant');
		$supervisor     = $this->input->get('supervisor');
		$acctng         = $this->input->get('acctng');
		$reviewer       = $this->input->get('reviewer');
		$disbursing     = $this->input->get('disbursing');
		$disbursingposi = $this->input->get('disbursingposi');
		$period         = $this->input->get('period');
		$ce_type        = $this->input->get('type');
		$no_sp          = $this->input->get('no_sp');
		$forrep_ex      = $this->input->get('forrep_ex');
		$qrt_amount		= 3000;

		$modeofpayment  = "Semester";
		$qtrsem         = 1;
		if(in_array($period, [5,6])){
			$qrt_amount		   = 3000;
			$modeofpayment = "Semester";
			$qtrsem = ($period == 5)?1:2;
		}else{
			$qrt_amount		   = 1500;
			$qtrsem = $period;
			$modeofpayment = "Quarter";
		}

		if($modeofpayment=="Quarter"){
			if($qtrsem==1){ $report_qtrsem="1st quarter"; $headermonth = "January to March"; $amount=1500; }
			else if($qtrsem==2){ $report_qtrsem="2nd quarter"; $headermonth = "April to June"; $amount=1500; }
			else if($qtrsem==3){ $report_qtrsem="3rd quarter"; $headermonth = "July to September"; $amount=1500; }
			else if($qtrsem==4){ $report_qtrsem="4th quarter"; $headermonth = "October to December"; $amount=1500; }
		}else if($modeofpayment=="Semester"){
			if($qtrsem==1){ $report_qtrsem="1st semester"; $headermonth = "January to June"; $amount=3000; }
			else if($qtrsem==2){ $report_qtrsem="2nd semester"; $headermonth = "July to December"; $amount=3000; }
		}

		$condition = [ 
			"prov_code" 		=> $province,
			"mun_code" 			=> $municipality,
			"mode_of_payment"   => $modeofpayment,
			"period"   			=> $qtrsem,
			"year"      		=> $year,
			"liquidation"      	=> 0
		];

		if($ce_type != "all"){
			$condition['additional'] = $ce_type;
		}		
		
		if($ce_type != "all"){
			if($ce_type == "3"){
                $condition["additional"] = [1,2];
            }else{
                $condition["additional"] = $ce_type;
            }
		}

		$payrollList = $this->lm->get_total_served($condition);

		$locationdata = getLocation("m.mun_code = '". $municipality. "'",true);
		$provincename = $locationdata->prov_name;
		$municipalityname = $locationdata->mun_name;

		$Total = 0;
		$t_Amount = 0;
		$unpaidTotal = 0;
		$unpaidAmount = 0;
		$paidTotal = 0;
		$paidAmount = 0;
		$unpaidList = [];

		$generalList = $this->lm->get_all_general(["province" => $province, "city" => $municipality]);
		$lastname_list = array_column($generalList, 'lastname', 'connum');
		$firstname_list = array_column($generalList, 'firstname', 'connum');
		$spStatus_list = array_column($generalList, 'sp_status', 'connum');

		foreach ($payrollList as $key => $value) {
			//$Total++;
			$spstatus = isset($spStatus_list[$value['spid']]) ? $spStatus_list[$value['spid']] : "";
			$lastname = isset($lastname_list[$value['spid']]) ? $lastname_list[$value['spid']] : "";
			$firstname = isset($firstname_list[$value['spid']]) ? $firstname_list[$value['spid']] : "";

			if($forrep_ex == 1 && strtoupper($spstatus) == "FORREPLACEMENT"){
				continue;
			}

			$unpaidList[] = array(
				"bar_code"	=>	$value["bar_code"],
				"spid"		=>	$value["spid"],
				"lastname"	=>	$lastname,
				"firstname"	=>	$firstname,
				"amount"	=>	$value["amount"],
				"remarks"	=>	$value["remarks"],
			);
			$unpaidTotal++;
			$unpaidAmount += $value["amount"];
		}

		
		if(!empty($no_sp) && $no_sp > 0){
			$paidTotal = $no_sp - $unpaidTotal;
		}else{		
			$condition["liquidation"] = 1;
			$count_qry = array(
				"select" => "count(spid) as total",
				"table" => "tblpayroll",
				'type' => "row",
				'condition' => $condition,);
			$paidTotal = $this->Main->select($count_qry, array(), true)->total;
		}

		$paidAmount = $paidTotal * $qrt_amount;
		$Total = $paidTotal + $unpaidTotal;
		$t_Amount += $paidAmount + $unpaidAmount;
		
		if(!empty($unpaidList)){
			$count_datas = count($unpaidList);
		}else{
			$count_datas = 0;
		}
			//if($count_datas>0){
				
			//style settings
				$headerstyle = 
				[
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER_CONTINUOUS,
					'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
				],
				'font'  => [
					'size'  => 12,
					'name' => 'Calibri'
				]];
				$headerstyleborder = 
				[
					'alignment' => [
						'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER_CONTINUOUS,
						'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
					],
					'font'  => [
						'size'  => 12,
						'name' => 'Calibri'
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
					'size'  => 12,
					'name' => 'Calibri'
				]];
				$textright =
				[
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
					'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
				]];
				$textcenter =
				[
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
					'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
				],
				'font'  => [
					'size'  => 12,
					'name' => 'Calibri'
				]];
				$border = 
				[
					'borders' => [
						'allBorders' => [
							'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				]]];
				$bordertop = [ 'borders' => [ 'top' => [ 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, ]]];
				$borderbottom = [ 'borders' => [ 'bottom' => [ 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, ]]];
				$borderright = [ 'borders' => [ 'right' => [ 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, ]]];
				$borderleft = [ 'borders' => [ 'left' => [ 'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, ]]];
				
				$borderbottomdouble= 
				[
					'borders' => [
						'bottom' => [
							'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOUBLE,
				]]];
				$textcenterliquireportcalibrif12 =
				[
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
					'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
				],
				'font'  => [
					'size'  => 12,
					'name' => 'Calibri'
				]];
				$textcenterliquireportf11 =
				[
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
					'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
				],
				'font'  => [
					'size'  => 11,
					'name' => 'Times New Roman'
				]];
				$textrighttopliquireportf14 =
				[
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
					'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
				],
				'font'  => [
					'size'  => 14,
					'name' => 'Calibri'
				]];
				$textrightliquireportf11 =
				[
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
					'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
				],
				'font'  => [
					'size'  => 11,
					'name' => 'Calibri'
				]];
				$textbottomliquireportf11 =
				[
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
					'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_BOTTOM,
				],
				'font'  => [
					'size'  => 11,
					'name' => 'Times New Roman'
				]];
				$textleftliquireportf11 =
				[
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
					'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
				],
				'font'  => [
					'size'  => 11,
					'name' => 'Times New Roman'
				]];
				$textleftbottomliquireportf11 =
				[
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
					'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_BOTTOM,
				],
				'font'  => [
					'size'  => 11,
					'name' => 'Times New Roman'
				]];
				$textleftliquireportf8 =
				[
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
					'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
				],
				'font'  => [
					'size'  => 8,
					'name' => 'Times New Roman'
				]];
				$textcenterliquireportf12 =
				[
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
					'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
				],
				'font'  => [
					'size'  => 12,
					'name' => 'Times New Roman'
				]];
				$texttopliquireportf12 =
				[
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
					'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
				],
				'font'  => [
					'size'  => 12,
					'name' => 'Times New Roman'
				]];
				$textbottomliquireportf12 =
				[
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
					'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_BOTTOM,
				],
				'font'  => [
					'size'  => 12,
					'name' => 'Times New Roman'
				]];
				$textleftliquireportf12 =
				[
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
					'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
				],
				'font'  => [
					'size'  => 12,
					'name' => 'Times New Roman'
				]];
				$textcenterliquireportf16 =
				[
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
					'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
				],
				'font'  => [
					'size'  => 16,
					'name' => 'Times New Roman'
				]];
				$wingdingsf12 =
				[
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
					'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
				],
				'font'  => [
					'size'  => 24,
					'name' => 'Wingdings'
				]];
				$fontred =
				[
				'font'  => [
					'size'  => 11,
					'name' => 'Calibri',
					'bold'  => true,
					'color' => array('rgb' => 'FF0000'),
				]];

				$object = new Spreadsheet();
				$object->createSheet(0); //checklist
				$object->createSheet(1); //liquidation report
				$object->createSheet(2); //liquidation summary
				$object->createSheet(3); //disbursement form

			//////sheet 1 = checklist
				$object->setActiveSheetIndex(0);
				$activeSheet =$object->getActiveSheet();
				$activeSheet->setTitle("CHECKLIST");

				$activeSheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
				$activeSheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
				
				$margineset = $activeSheet->getPageMargins();
				$margineset->setTop(0.75);
				$margineset->setBottom(0.75);
				$margineset->setRight(0.15);
				$margineset->setLeft(0.15);
				
				$activeSheet->getPageSetup()->setPrintArea('A1:J18');
				$activeSheet->getPageSetup()->setFitToPage(true);
				$activeSheet->getPageSetup()->setFitToWidth(1);
				$activeSheet->getPageSetup()->setFitToHeight(0);

				$activeSheet->getColumnDimension('A')->setWidth(4.71);
				$activeSheet->getColumnDimension('B')->setWidth(3.71);
				$activeSheet->getColumnDimension('C')->setWidth(4.71);
				$activeSheet->getColumnDimension('D')->setWidth(3.57);
				$activeSheet->getColumnDimension('E')->setWidth(5.71);
				$activeSheet->getColumnDimension('F')->setWidth(35.85);
				$activeSheet->getColumnDimension('G')->setWidth(9.14);
				$activeSheet->getColumnDimension('H')->setWidth(12.71);
				$activeSheet->getColumnDimension('I')->setWidth(9.14);
				$activeSheet->getColumnDimension('J')->setWidth(11.14);

				$excel_row=1;

				$activeSheet->getStyle('A'.$excel_row.':H'.$excel_row)->applyFromArray($textcenterliquireportf12);
				$activeSheet->mergeCells('A'.$excel_row.':H'.$excel_row)->setCellValue('A'.$excel_row,'CHECKLIST OF SUPPORTING DOCUMENTS FOR PRE - AUDIT');

				$excel_row++;
				$activeSheet->getStyle('A'.$excel_row.':H'.$excel_row)->applyFromArray($textcenterliquireportf12);
				$activeSheet->mergeCells('A'.$excel_row.':H'.$excel_row)->setCellValue('A'.$excel_row,'Liquidation of Cash Advances for Social Pension');
				$activeSheet->getStyle('A'.$excel_row)->getFont()->setBold(true);

				$excel_row++;
				$activeSheet->getRowDimension($excel_row)->setRowHeight(15.75);
				$excel_row++;
				$activeSheet->getRowDimension($excel_row)->setRowHeight(15.75);
				$excel_row++;
				$activeSheet->getRowDimension($excel_row)->setRowHeight(21.75);
				$excel_row++;
				$activeSheet->getStyle('A'.$excel_row)->applyFromArray($wingdingsf12);
				$activeSheet->setCellValue("A".$excel_row,"q");
				$activeSheet->setCellValue("B".$excel_row,"1");
				$activeSheet->setCellValue("C".$excel_row,"Liquidation Report duly signed by Immediate Supervisor and Accountant");
				$activeSheet->getRowDimension($excel_row)->setRowHeight(25);
				$activeSheet->getStyle('C'.$excel_row.':J'.$excel_row)->applyFromArray($textleftliquireportf12);
				$activeSheet->getStyle('B'.$excel_row)->applyFromArray($textcenterliquireportf12);

				$excel_row++;
				$activeSheet->getStyle('A'.$excel_row)->applyFromArray($wingdingsf12);
				$activeSheet->setCellValue("A".$excel_row,"q");
				$activeSheet->setCellValue("B".$excel_row,"2");
				$activeSheet->setCellValue("C".$excel_row,"Cash Assistance Payroll duly approved and filled up and its supporting attachments");
				$activeSheet->getRowDimension($excel_row)->setRowHeight(25);
				$activeSheet->getStyle('C'.$excel_row.':J'.$excel_row)->applyFromArray($textleftliquireportf12);
				$activeSheet->getStyle('B'.$excel_row)->applyFromArray($textcenterliquireportf12);

				$excel_row++;
				$activeSheet->setCellValue("C".$excel_row,"a.");
				$activeSheet->setCellValue("D".$excel_row,"Photocopy of Authorized Representative ID");
				$activeSheet->getRowDimension($excel_row)->setRowHeight(25);
				$activeSheet->getStyle('D'.$excel_row.':J'.$excel_row)->applyFromArray($textleftliquireportf12);
				$activeSheet->getStyle('C'.$excel_row)->applyFromArray($textcenterliquireportf12);

				$excel_row++;
				$activeSheet->setCellValue("C".$excel_row,"b.");
				$activeSheet->setCellValue("D".$excel_row,"Authorization Letter by the Social Pension beneficiary");
				$activeSheet->getRowDimension($excel_row)->setRowHeight(25);
				$activeSheet->getStyle('D'.$excel_row.':J'.$excel_row)->applyFromArray($textleftliquireportf12);
				$activeSheet->getStyle('C'.$excel_row)->applyFromArray($textcenterliquireportf12);

				$excel_row++;
				$activeSheet->getRowDimension($excel_row)->setRowHeight(25);
				$excel_row++;
				$activeSheet->getRowDimension($excel_row)->setRowHeight(25);

				$excel_row++;
				$activeSheet->getRowDimension($excel_row)->setRowHeight(25);
				$activeSheet->getStyle('B'.$excel_row.':J'.$excel_row)->applyFromArray($borderbottomdouble);

				$excel_row++;
				$activeSheet->setCellValue("A".$excel_row,"Reviewed by :");
				$reviewedby = sesdata('first_name') . " " . sesdata('middle_name') . ". " . sesdata('last_name');
				$activeSheet->setCellValue("D".$excel_row,$reviewedby);
				$activeSheet->getStyle('D'.$excel_row)->getFont()->setBold(true);
				$activeSheet->getStyle('D'.$excel_row)->getFont()->setUnderline(true);
				$activeSheet->setCellValue("H".$excel_row,"Date: ______________________");
				$activeSheet->getRowDimension($excel_row)->setRowHeight(25);
				$activeSheet->getStyle('A'.$excel_row.':J'.$excel_row)->applyFromArray($textleftliquireportf12);
				
				$excel_row++;
				$activeSheet->setCellValue("A".$excel_row,"Remarks:");
				$activeSheet->setCellValue("C".$excel_row,"with complete attachment of AUTHORIZATIONS and REPRESENTATIVE's IDs");
				$activeSheet->getStyle('C'.$excel_row)->getFont()->setUnderline(true);
				$activeSheet->getRowDimension($excel_row)->setRowHeight(25);
				$activeSheet->getStyle('A'.$excel_row.':J'.$excel_row)->applyFromArray($textleftliquireportf12);

				$excel_row++;
				$activeSheet->getRowDimension($excel_row)->setRowHeight(25);
				$excel_row++;
				$activeSheet->getRowDimension($excel_row)->setRowHeight(25);

				$excel_row++;
				$activeSheet->setCellValue("A".$excel_row,"Certified complete and accurate: _____________________________");
				$activeSheet->getRowDimension($excel_row)->setRowHeight(25);
				$activeSheet->setCellValue("H".$excel_row,"Date: ______________________");
				$activeSheet->getStyle('A'.$excel_row.':J'.$excel_row)->applyFromArray($textleftliquireportf12);

				$excel_row++;
				$activeSheet->mergeCells('F'.$excel_row.':G'.$excel_row)->setCellValue('F'.$excel_row,'ACCOUNTING PERSONNEL');
				$activeSheet->getStyle('F'.$excel_row.':G'.$excel_row)->applyFromArray($textcenterliquireportf12);
				$activeSheet->getRowDimension($excel_row)->setRowHeight(25);
			//end of sheet 1

			//sheet 3 = liquidation summary
				$object->setActiveSheetIndex(2);
				$activeSheet =$object->getActiveSheet();
				$activeSheet->setTitle("LIQUIDATION SUMMARY");

				$activeSheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
				$activeSheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
				
				$margineset = $activeSheet->getPageMargins();
				$margineset->setTop(0.25);
				$margineset->setBottom(0.25);
				$margineset->setRight(0.25);
				$margineset->setLeft(0.25);

				$activeSheet->getPageSetup()->setFitToPage(true);
				$activeSheet->getPageSetup()->setFitToWidth(1);
				$activeSheet->getPageSetup()->setFitToHeight(0);

				$activeSheet->getColumnDimension('A')->setWidth(26);
				$activeSheet->getColumnDimension('B')->setWidth(9);
				$activeSheet->getColumnDimension('C')->setWidth(22);
				$activeSheet->getColumnDimension('D')->setWidth(19);
				$activeSheet->getColumnDimension('E')->setWidth(9);
				$activeSheet->getColumnDimension('F')->setWidth(22);
				$activeSheet->getColumnDimension('G')->setWidth(19);
				$activeSheet->getColumnDimension('H')->setWidth(18);
				$activeSheet->getColumnDimension('I')->setWidth(18);
				$activeSheet->getColumnDimension('J')->setWidth(18);

				$excel_row=1;

				$activeSheet->getStyle('A'.$excel_row.':H'.$excel_row)->applyFromArray($headerstyle);
				$activeSheet->getStyle('A'.$excel_row)->getFont()->setBold(true);
				$activeSheet->mergeCells('A'.$excel_row.':H'.$excel_row)->setCellValue('A'.$excel_row,'SUMMARY OF LIQUIDATION');

				$excel_row++;
				$activeSheet->getStyle('A'.$excel_row.':H'.$excel_row)->applyFromArray($headerstyle);
				$activeSheet->getStyle('A'.$excel_row)->getFont()->setBold(true);
				$activeSheet->mergeCells('A'.$excel_row.':H'.$excel_row)->setCellValue('A'.$excel_row, mb_strtoupper($claimant)." ".mb_strtoupper($report_qtrsem)." - ACTIVE");

				$excel_row++;
				$activeSheet->mergeCells('A'.$excel_row.':H'.$excel_row)->setCellValue('A'.$excel_row, $municipalityname.", ".$provincename);
				$activeSheet->getStyle('A'.$excel_row.':H'.$excel_row)->applyFromArray($textcenter);
				$excel_row+=2;
				
				$activeSheet->getStyle('A'.$excel_row.':H'.$excel_row)->applyFromArray($headerstyleborder); $activeSheet->getStyle('A'.$excel_row.':J'.$excel_row)->getFont()->setBold(true);
				$activeSheet->mergeCells('B'.$excel_row.':D'.$excel_row)->setCellValue('B'.$excel_row, "CASH ADVANCE");
				$activeSheet->mergeCells('E'.$excel_row.':G'.$excel_row)->setCellValue('E'.$excel_row, "CASH DISBURSED"); $excel_row++;

				$activeSheet->getStyle('A'.$excel_row.':H'.$excel_row)->applyFromArray($headerstyleborder);
				$activeSheet->getStyle('A'.$excel_row.':H'.$excel_row)->getFont()->setBold(true);
				$activeSheet->getStyle('A'.$excel_row.':H'.$excel_row)->getAlignment()->setWrapText(true);
				$table_columns = array("No. of SP", "Amount", "TOTAL AMOUNT of CASH ADVANCE", "No. of SP", "Amount", "TOTAL CASH DISBURSED", "BALANCE");
				$hs = "B";
				foreach ($table_columns as $tv) { $activeSheet->setCellValue($hs.$excel_row,$tv); $hs++; } $excel_row++;
				
				$activeSheet->getStyle('A'.$excel_row.':H'.$excel_row)->applyFromArray($border);
				$activeSheet->setCellValue("A".$excel_row,$report_qtrsem." - Active");
				// $target = getTarget("target",array("tbltarget.mun_code"=>$municipality),"","row")->target;
				// $eligcondi = "eligibility_stat=1 AND (upload_batchno=1 OR upload_batchno=2 OR upload_batchno=3 OR upload_batchno=4 OR upload_batchno=5 OR upload_batchno=6) AND mun_code='$municipality'";
				// $target = $this->capmodel->getEligibleBenes("count(*) as total", $eligcondi, "row")->total;//counteligible batch1-6
				$activeSheet->setCellValue("B".$excel_row,$Total);
				$activeSheet->setCellValue("C".$excel_row,number_format($t_Amount,2));
				$activeSheet->setCellValue("D".$excel_row,number_format($t_Amount,2));
				$activeSheet->setCellValue("E".$excel_row,$paidTotal);
				$activeSheet->setCellValue("F".$excel_row,number_format($paidAmount,2));
				$activeSheet->setCellValue("G".$excel_row,number_format($paidAmount,2));
				$activeSheet->setCellValue("H".$excel_row,number_format($unpaidAmount,2));
				$activeSheet->getStyle('C'.$excel_row.':H'.$excel_row)->applyFromArray($textright);
				$excel_row+=2;

				$activeSheet->getStyle('A'.$excel_row.':H'.$excel_row)->applyFromArray($headerstyleborder);
				$activeSheet->getStyle('A'.$excel_row.':H'.$excel_row)->applyFromArray($textleft);
				$activeSheet->getStyle('A'.$excel_row.':H'.$excel_row)->getFont()->setBold(true);
				$activeSheet->setCellValue("A".$excel_row,"GRAND TOTAL");
				$activeSheet->setCellValue("D".$excel_row,number_format($t_Amount,2));
				$activeSheet->setCellValue("G".$excel_row,number_format($paidAmount,2));
				$activeSheet->setCellValue("H".$excel_row,number_format($unpaidAmount,2));
				$activeSheet->getStyle('C'.$excel_row.':H'.$excel_row)->applyFromArray($textright);
				$excel_row+=2;

				$activeSheet->setCellValue("A".$excel_row,"UNRELEASED");
				$excel_row+=2;
				$table_columns = array("SPID", "Barangay", "Name", "Amount", "Remarks");
				$hs = "A";
				$activeSheet->getStyle('A'.$excel_row.':J'.$excel_row)->applyFromArray($fontred);
				foreach ($table_columns as $tv) { 
					$activeSheet->setCellValue($hs.$excel_row,$tv); $hs++; 
				} 
				$activeSheet->mergeCells('E'.$excel_row.':F'.$excel_row)->setCellValue('E'.$excel_row,"REMARKS");
				$excel_row++;

				$namectr=0; $gtotal=0;
				$secondcol = $excel_row;
				$divideto2 = $count_datas/2;
				foreach($unpaidList as $ul){
					$presentloc = getLocation("b.bar_code = '". $ul["bar_code"]."'",true);
					// if($namectr<$divideto2){
						$activeSheet->setCellValue("A".$excel_row , $ul["spid"]);
						$activeSheet->setCellValue("B".$excel_row , $presentloc->bar_name);
						$activeSheet->setCellValue("C".$excel_row , mb_strtoupper($ul["lastname"]).", ".$ul["firstname"]);
						$activeSheet->setCellValue("D".$excel_row , number_format($ul["amount"],2));
						$activeSheet->setCellValue("E".$excel_row , $ul["remarks"]);
						$activeSheet->mergeCells('E'.$excel_row.':F'.$excel_row)->setCellValue('E'.$excel_row,$ul["remarks"]);
						//$activeSheet->getStyle('E'.$excel_row)->applyFromArray($textright);
						$gtotal += $ul["amount"];
						$excel_row++;
						
						if($excel_row==81){
							$table_columns = array("SPID", "Barangay", "Name", "Amount", "Remarks");
							$hs = "A";
							$activeSheet->getStyle('A'.$excel_row.':E'.$excel_row)->applyFromArray($fontred);
							foreach ($table_columns as $tv) { $activeSheet->setCellValue($hs.$excel_row,$tv); $hs++; } $excel_row++;
						}
					// }else{
					// 	$activeSheet->setCellValue("F".$secondcol , $ul["spid"]);
					// 	$activeSheet->setCellValue("G".$secondcol , $presentloc->bar_name);
					// 	$activeSheet->setCellValue("H".$secondcol , mb_strtoupper($ul["lastname"]).", ".$ul["firstname"]);
					// 	$activeSheet->setCellValue("I".$secondcol , number_format($ul["amount"],2));
					// 	$activeSheet->setCellValue("J".$secondcol , $ul["remarks"]);
					// 	$activeSheet->getStyle('J'.$secondcol)->applyFromArray($textright);
					// 	$gtotal += $ul["amount"];
					// 	$secondcol++;
						
					// 	if($secondcol==81){
					// 		$table_columns = array("SPID", "Barangay", "Name", "Amount", "Remarks");
					// 		$hs = "F";
					// 		$activeSheet->getStyle('E'.$secondcol.':J'.$secondcol)->applyFromArray($fontred);
					// 		foreach ($table_columns as $tv) { $activeSheet->setCellValue($hs.$secondcol,$tv); $hs++; } $secondcol++;
					// 	}
					// }
					// $namectr++;
				}

				$excel_row++;
				$activeSheet->getStyle('A'.$excel_row.':J'.$excel_row)->getFont()->setBold(true);
				$activeSheet->setCellValue("A".$excel_row,"TOTAL");
				$activeSheet->setCellValue("H".$excel_row,number_format($gtotal,2));
				$activeSheet->getStyle('H'.$excel_row)->applyFromArray($textright);
				$activeSheet->getStyle('A'.$excel_row.':J'.$excel_row)->applyFromArray($fontred);
				$excel_row++;
				$activeSheet->setCellValue("A".$excel_row,"No. of SP.");
				$activeSheet->getStyle('A'.$excel_row)->getFont()->setItalic( true );
			//end of sheet 3

			//sheet 2 = liquidation report
				
				$textcentertimesnewromanf14 =
				[
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
					'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
				],
				'font'  => [
					'size'  => 14,
					'name' => 'Times New Roman'
				]];
				
				$object->setActiveSheetIndex(1);
				$activeSheet =$object->getActiveSheet();
				$activeSheet->setTitle("LIQUIDATION REPORT");

				$activeSheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT);
				$activeSheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
				
				$margineset = $activeSheet->getPageMargins();
				$margineset->setTop(0.75);
				$margineset->setBottom(0.75);
				$margineset->setRight(0.7);
				$margineset->setLeft(0.7);

				$activeSheet->getPageSetup()->setPrintArea('A3:C49');
				$activeSheet->getPageSetup()->setFitToPage(true);
				$activeSheet->getPageSetup()->setFitToWidth(1);
				$activeSheet->getPageSetup()->setFitToHeight(0);

				$activeSheet->getColumnDimension('A')->setWidth(29.28);
				$activeSheet->getColumnDimension('B')->setWidth(40.42);
				$activeSheet->getColumnDimension('C')->setWidth(32.14);

				$excel_row=1;
				$activeSheet->setCellValue("C".$excel_row,"Appendix 44");
				$activeSheet->getStyle('C'.$excel_row)->getFont()->setItalic(true);
				$activeSheet->getStyle('C'.$excel_row)->applyFromArray($textcentertimesnewromanf14);
				$excel_row+=2;
				$activeSheet->getStyle('A'.$excel_row)->applyFromArray($bordertop);
				$activeSheet->getStyle('A'.$excel_row)->applyFromArray($borderleft);
				$activeSheet->getStyle('B'.$excel_row)->applyFromArray($bordertop);
				$activeSheet->getStyle('C'.$excel_row)->applyFromArray($bordertop);
				$activeSheet->getStyle('C'.$excel_row)->applyFromArray($borderleft);
				$activeSheet->getStyle('C'.$excel_row)->applyFromArray($borderright);
				$excel_row++;

				$activeSheet->mergeCells('A'.$excel_row.':B'.$excel_row)->setCellValue('A'.$excel_row,'LIQUIDATION REPORT');
				$activeSheet->getStyle('A'.$excel_row.':B'.$excel_row)->applyFromArray($textcenterliquireportf16); $activeSheet->getStyle('A'.$excel_row.':B'.$excel_row)->getFont()->setBold(true);
				$activeSheet->setCellValue("C".$excel_row,"Serial No: __________________");
				$activeSheet->getStyle('C'.$excel_row)->applyFromArray($textbottomliquireportf12);
				$activeSheet->getStyle('A'.$excel_row)->applyFromArray($borderleft);
				$activeSheet->getStyle('C'.$excel_row)->applyFromArray($borderleft);
				$activeSheet->getStyle('C'.$excel_row)->applyFromArray($borderright);
				$excel_row++;

				$activeSheet->getStyle('A'.$excel_row)->applyFromArray($borderleft);
				$activeSheet->getStyle('C'.$excel_row)->applyFromArray($borderleft);
				$activeSheet->getStyle('C'.$excel_row)->applyFromArray($borderright);
				$activeSheet->getStyle('C'.$excel_row)->applyFromArray($borderbottom);
				$activeSheet->setCellValue("C".$excel_row,"Date: ______________________"); $activeSheet->getRowDimension($excel_row)->setRowHeight(32);
				$activeSheet->getStyle('C'.$excel_row)->applyFromArray($texttopliquireportf12);
				$excel_row++;

				$activeSheet->getStyle('A'.$excel_row)->applyFromArray($borderleft);
				$activeSheet->getStyle('C'.$excel_row)->applyFromArray($borderleft);
				$activeSheet->getStyle('C'.$excel_row)->applyFromArray($borderright);
				$activeSheet->mergeCells('A'.$excel_row.':B'.$excel_row)->setCellValue('A'.$excel_row,'Entity Name: DSWD CAR');
				$activeSheet->setCellValue("C".$excel_row,"Responsibility Center Code:");
				$activeSheet->getStyle('A'.$excel_row.':C'.$excel_row)->applyFromArray($textleftliquireportf12); $activeSheet->getStyle('A'.$excel_row)->getFont()->setBold(true);
				$excel_row++;

				$activeSheet->getStyle('A'.$excel_row)->applyFromArray($borderleft);
				$activeSheet->getStyle('C'.$excel_row)->applyFromArray($borderleft);
				$activeSheet->getStyle('C'.$excel_row)->applyFromArray($borderright);
				$activeSheet->mergeCells('A'.$excel_row.':B'.$excel_row)->setCellValue('A'.$excel_row,'Fund Cluster : 101');
				$activeSheet->setCellValue("C".$excel_row,"___________________________");
				$activeSheet->getStyle('A'.$excel_row.':C'.$excel_row)->applyFromArray($textleftliquireportf12); $activeSheet->getStyle('A'.$excel_row)->getFont()->setBold(true);
				
				$excel_row++;
				$activeSheet->getStyle('A'.$excel_row)->applyFromArray($borderleft);
				$activeSheet->getStyle('C'.$excel_row)->applyFromArray($borderleft);
				$activeSheet->getStyle('C'.$excel_row)->applyFromArray($borderright);

				$excel_row++;
				$activeSheet->getStyle('A'.$excel_row)->applyFromArray($borderleft);
				$activeSheet->getStyle('A'.$excel_row)->applyFromArray($bordertop);
				$activeSheet->getStyle('A'.$excel_row)->applyFromArray($borderbottom);
				$activeSheet->getStyle('B'.$excel_row)->applyFromArray($bordertop);
				$activeSheet->getStyle('B'.$excel_row)->applyFromArray($borderbottom);
				$activeSheet->getStyle('C'.$excel_row)->applyFromArray($border);
				$activeSheet->mergeCells('A'.$excel_row.':B'.$excel_row)->setCellValue('A'.$excel_row,'PARTICULARS');
				$activeSheet->setCellValue("C".$excel_row,"AMOUNT");
				$activeSheet->getRowDimension($excel_row)->setRowHeight(32);
				$activeSheet->getStyle('A'.$excel_row.':C'.$excel_row)->applyFromArray($textcenterliquireportf12);
				$activeSheet->getStyle('A'.$excel_row.':C'.$excel_row)->getFont()->setBold(true);

				$excel_row++;
				$activeSheet->getRowDimension($excel_row)->setRowHeight(32);
				$nextrow = $excel_row+27;
				$activeSheet->getStyle('A'.$excel_row.':C'.$nextrow)->applyFromArray($border);
				$activeSheet->mergeCells('A'.$excel_row.':B'.$nextrow)->setCellValue('A'.$excel_row,'To liquidate Cash Advance of '.$claimant.'-                                              '.$report_qtrsem.' '.$year.' - Active in the implementation of Social Pension                                              Program for Indigent Senior Citizens per check # ______________                                              dated ___________________ in the amount of…');
				$activeSheet->getStyle('A'.$excel_row.':B'.$excel_row)->applyFromArray($textcenterliquireportcalibrif12); $activeSheet->getStyle('A'.$excel_row.':B'.$nextrow)->getAlignment()->setWrapText(true);
				$activeSheet->mergeCells('C'.$excel_row.':C'.$nextrow)->setCellValue("C".$excel_row,number_format($paidAmount,2));
				$activeSheet->getStyle('C'.$excel_row)->applyFromArray($textrighttopliquireportf14);
				$activeSheet->getStyle('C'.$excel_row)->getFont()->setBold(true);
				$excel_row = $nextrow+1;

				$nextrow = $excel_row+3;
				$activeSheet->getStyle('A'.$excel_row.':C'.$nextrow)->applyFromArray($border);
				$activeSheet->mergeCells('A'.$excel_row.':B'.$excel_row)->setCellValue('A'.$excel_row,'TOTAL AMOUNT SPENT');
				$activeSheet->getStyle('A'.$excel_row.':B'.$excel_row)->applyFromArray($textleftliquireportf11);
				$activeSheet->setCellValue("C".$excel_row,number_format($paidAmount,2));
				$activeSheet->getStyle('C'.$excel_row)->applyFromArray($textrightliquireportf11);
				$activeSheet->getStyle('C'.$excel_row)->getFont()->setBold(true);
				$excel_row++;
				
				$activeSheet->mergeCells('A'.$excel_row.':B'.$excel_row)->setCellValue('A'.$excel_row,'AMOUNT OF CASH ADVANCE PER DV NO. ________ DTD. ________');
				$activeSheet->getStyle('A'.$excel_row.':B'.$excel_row)->applyFromArray($textleftliquireportf11);
				$activeSheet->setCellValue("C".$excel_row,number_format($t_Amount,2));
				$activeSheet->getStyle('C'.$excel_row)->applyFromArray($textrightliquireportf11);
				$activeSheet->getStyle('C'.$excel_row)->getFont()->setBold(true);
				$excel_row++;

				$activeSheet->mergeCells('A'.$excel_row.':B'.$excel_row)->setCellValue('A'.$excel_row,'AMOUNT REFUNDED PER OR NO. ________ DTD. ________');
				$activeSheet->getStyle('A'.$excel_row.':B'.$excel_row)->applyFromArray($textleftliquireportf11);
				$activeSheet->setCellValue("C".$excel_row,number_format($unpaidAmount ,2));
				$activeSheet->getStyle('C'.$excel_row)->applyFromArray($textrightliquireportf11);
				$activeSheet->getStyle('C'.$excel_row)->getFont()->setBold(true);
				$excel_row++;
				
				$activeSheet->mergeCells('A'.$excel_row.':B'.$excel_row)->setCellValue('A'.$excel_row,'AMOUNT TO BE REIMBURSED:');
				$activeSheet->getStyle('A'.$excel_row.':B'.$excel_row)->applyFromArray($textleftliquireportf11);
				$activeSheet->getStyle('C'.$excel_row)->applyFromArray($textrightliquireportf11);
				$activeSheet->getStyle('C'.$excel_row)->getFont()->setBold(true);

				$excel_row++;
				$activeSheet->getStyle('A'.$excel_row)->applyFromArray($borderleft);
				$activeSheet->getStyle('C'.$excel_row)->applyFromArray($borderright);

				$excel_row++;
				$activeSheet->getStyle('A'.$excel_row)->applyFromArray($borderleft);
				$activeSheet->getStyle('C'.$excel_row)->applyFromArray($borderright);
				$activeSheet->setCellValue("A".$excel_row,"A. Certified: Correctness of the above data");
				$activeSheet->setCellValue("B".$excel_row,"B. Certified: Purpose of travel/cash advance duly accomplished");
				$activeSheet->setCellValue("C".$excel_row,"C. Certified: Supporting documents complete and proper");
				$activeSheet->getRowDimension($excel_row)->setRowHeight(23);
				$activeSheet->getStyle('A'.$excel_row.':C'.$excel_row)->applyFromArray($textleftliquireportf8);
				$activeSheet->getStyle('A'.$excel_row.':C'.$excel_row)->getAlignment()->setWrapText(true);

				$nextrow = $excel_row+3;
				$activeSheet->getStyle('A'.$excel_row.':A'.$nextrow)->applyFromArray($borderleft);
				$activeSheet->getStyle('C'.$excel_row.':C'.$nextrow)->applyFromArray($borderright);
				$activeSheet->mergeCells('A'.$excel_row.':A'.$nextrow)->setCellValue("A".$excel_row,mb_strtoupper($claimant));
				$activeSheet->mergeCells('B'.$excel_row.':B'.$nextrow)->setCellValue("B".$excel_row,mb_strtoupper($supervisor));
				$activeSheet->mergeCells('C'.$excel_row.':C'.$nextrow)->setCellValue("C".$excel_row,mb_strtoupper($acctng));
				$activeSheet->getStyle('A'.$excel_row.':C'.$excel_row)->applyFromArray($textbottomliquireportf11);
				$activeSheet->getStyle('A'.$excel_row.':C'.$excel_row)->getFont()->setBold(true);
				$activeSheet->getStyle('A'.$excel_row.':C'.$excel_row)->getFont()->setUnderline(true);

				$excel_row = $nextrow+1;
				$activeSheet->getStyle('A'.$excel_row)->applyFromArray($borderleft);
				$activeSheet->getStyle('C'.$excel_row)->applyFromArray($borderright);
				$activeSheet->setCellValue("A".$excel_row,"CLAIMANT");
				$activeSheet->setCellValue("B".$excel_row,"Immediate Supervisor");
				$activeSheet->setCellValue("C".$excel_row,"Head, Accounting Division Unit");
				$activeSheet->getStyle('A'.$excel_row.':C'.$excel_row)->applyFromArray($textcenterliquireportf11);

				$excel_row++;
				$activeSheet->getStyle('A'.$excel_row)->applyFromArray($borderleft);
				$activeSheet->getStyle('C'.$excel_row)->applyFromArray($borderright);
				$activeSheet->setCellValue("C".$excel_row,"JEV No.:  ___________________"); $activeSheet->getRowDimension($excel_row)->setRowHeight(32);
				$activeSheet->getStyle('A'.$excel_row.':C'.$excel_row)->applyFromArray($textleftbottomliquireportf11);

				$excel_row++;
				$activeSheet->getStyle('A'.$excel_row)->applyFromArray($borderleft);
				$activeSheet->getStyle('A'.$excel_row)->applyFromArray($borderbottom);
				$activeSheet->getStyle('B'.$excel_row)->applyFromArray($borderbottom);
				$activeSheet->getStyle('C'.$excel_row)->applyFromArray($borderright);
				$activeSheet->getStyle('C'.$excel_row)->applyFromArray($borderbottom);
				$activeSheet->setCellValue("A".$excel_row,"Date: _____________________");
				$activeSheet->setCellValue("B".$excel_row,"Date: _____________________");
				$activeSheet->setCellValue("C".$excel_row,"Date:  ______________________");
				$activeSheet->getStyle('A'.$excel_row.':C'.$excel_row)->applyFromArray($textleftliquireportf11);
			//end of sheet 2

			//sheet 4
				$textcentertimesnewromanf12 =
				[
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
					'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
				],
				'font'  => [
					'size'  => 12,
					'name' => 'Times New Roman'
				]];
				$textcentertimesnewromanf11 =
				[
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
					'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
				],
				'font'  => [
					'size'  => 11,
					'name' => 'Times New Roman'
				]];
				$textcentercentertimesnewromanf11 =
				[
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
					'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
				],
				'font'  => [
					'size'  => 11,
					'name' => 'Times New Roman'
				]];
				$textcenterrighttimesnewromanf11 =
				[
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
					'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
				],
				'font'  => [
					'size'  => 11,
					'name' => 'Times New Roman'
				]];
				$subheaders = 
				[
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER_CONTINUOUS,
					'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
				],
				'font'  => [
					'size'  => 11,
					'name' => 'Times New Roman'
				]];
				$textcentertimesnewroman =
				[
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
					'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
				],
				'font'  => [
					'size'  => 11,
					'name' => 'TimesNewRoman'
				]];
				
				$object->setActiveSheetIndex(3);
				$activeSheet =$object->getActiveSheet();
				$activeSheet->setTitle("RCDisb");

				$activeSheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
				$activeSheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
				
				$margineset = $activeSheet->getPageMargins();
				$margineset->setTop(0.75);
				$margineset->setBottom(0.75);
				$margineset->setRight(0.7);
				$margineset->setLeft(0.7);

				$activeSheet->getPageSetup()->setPrintArea('B1:I33');
				$activeSheet->getPageSetup()->setFitToPage(true);
				$activeSheet->getPageSetup()->setFitToWidth(1);
				$activeSheet->getPageSetup()->setFitToHeight(0);

				$activeSheet->getColumnDimension('A')->setWidth(9.14);
				$activeSheet->getColumnDimension('B')->setWidth(13.28);
				$activeSheet->getColumnDimension('C')->setWidth(12.28);
				$activeSheet->getColumnDimension('D')->setWidth(13.28);
				$activeSheet->getColumnDimension('E')->setWidth(16.42);
				$activeSheet->getColumnDimension('F')->setWidth(41.28);
				$activeSheet->getColumnDimension('G')->setWidth(15.85);
				$activeSheet->getColumnDimension('H')->setWidth(31.57);
				$activeSheet->getColumnDimension('I')->setWidth(15.27);

				$excel_row=1;
				$activeSheet->setCellValue("I".$excel_row,"Appendix 41");
				$activeSheet->getStyle('I'.$excel_row)->getFont()->setItalic(true);
				$activeSheet->getStyle('I'.$excel_row)->applyFromArray($textcentertimesnewromanf14);
				$excel_row+=3;

				$activeSheet->mergeCells('B'.$excel_row.':I'.$excel_row)->setCellValue('B'.$excel_row,'REPORT OF CASH DISBURSEMENTS');
				$activeSheet->getStyle('B'.$excel_row)->applyFromArray($textcentertimesnewromanf14);
				$activeSheet->getStyle('B'.$excel_row)->getFont()->setBold(true);

				$excel_row++;
				$activeSheet->mergeCells('B'.$excel_row.':I'.$excel_row)->setCellValue('B'.$excel_row,'Period Covered: '.$report_qtrsem.' '.$year);
				$activeSheet->getStyle('B'.$excel_row)->applyFromArray($textcentertimesnewromanf12);
				$activeSheet->getStyle('B'.$excel_row)->getFont()->setBold(true);
				$excel_row+=3;

				$activeSheet->setCellValue("B".$excel_row,"Entity Name : DSWD CAR");
				$activeSheet->setCellValue("H".$excel_row,"Report No. :");
				$activeSheet->getStyle('B'.$excel_row.':I'.$excel_row)->applyFromArray($textcentertimesnewromanf11);
				$activeSheet->getStyle('B'.$excel_row.':I'.$excel_row)->getFont()->setBold(true);
				$excel_row++;
				
				$activeSheet->setCellValue("B".$excel_row,"Fund Cluster : 01");
				$activeSheet->setCellValue("H".$excel_row,"Sheet No. : _______________________");
				$activeSheet->getStyle('B'.$excel_row.':I'.$excel_row)->applyFromArray($textcentertimesnewromanf11);
				$activeSheet->getStyle('B'.$excel_row.':I'.$excel_row)->getFont()->setBold(true);
				$excel_row++;

				$activeSheet->getRowDimension($excel_row)->setRowHeight(7.5);
				$excel_row++;

				$table_columns = array("Date", "DV/Payroll No.", "ORS/BURS No.", "Responsibility Center Code", "Payee", "UACS Object Code", "Nature of Payment", "Amount");
				$hs = "B";
				foreach ($table_columns as $tv) { $activeSheet->setCellValue($hs.$excel_row,$tv); $hs++; }
				$activeSheet->getRowDimension($excel_row)->setRowHeight(41);
				$activeSheet->getStyle('B'.$excel_row.':I'.$excel_row)->getAlignment()->setWrapText(true);
				$activeSheet->getStyle('B'.$excel_row.':I'.$excel_row)->applyFromArray($border);
				$activeSheet->getStyle('B'.$excel_row.':I'.$excel_row)->applyFromArray($subheaders);
				$activeSheet->getStyle('B'.$excel_row.':I'.$excel_row)->getFont()->setBold(true);
				$excel_row++;

				$activeSheet->setCellValue("F".$excel_row,"Social Pension for the Municipality of ".$municipalityname);
				$activeSheet->getStyle('F'.$excel_row)->applyFromArray($textcentertimesnewromanf11);
				$activeSheet->getStyle('F'.$excel_row.':I'.$excel_row)->getAlignment()->setWrapText(true);
				$activeSheet->getRowDimension($excel_row)->setRowHeight(-1);
				$activeSheet->setCellValue("G".$excel_row,"50214990 00");
				$activeSheet->getStyle('G'.$excel_row)->applyFromArray($textcentercentertimesnewromanf11);
				$activeSheet->setCellValue("H".$excel_row,"Cash Disbursement");
				$activeSheet->getStyle('H'.$excel_row)->applyFromArray($textcentertimesnewromanf11);
				$activeSheet->setCellValue("I".$excel_row,number_format($paidAmount,2));
				$activeSheet->getStyle('I'.$excel_row)->applyFromArray($textcenterrighttimesnewromanf11);
				$rowheight = $excel_row+1;

				for($i=1; $i<9; $i++){
					$activeSheet->getStyle('B'.$excel_row.':I'.$excel_row)->applyFromArray($border); 
					$activeSheet->getRowDimension($rowheight)->setRowHeight(18);
					$excel_row++; $rowheight++;
				}
				
				$activeSheet->mergeCells('B'.$excel_row.':I'.$excel_row)->setCellValue('B'.$excel_row,"CERTIFICATION");
				$activeSheet->getStyle('B'.$excel_row)->applyFromArray($textcentertimesnewromanf12);
				$activeSheet->getStyle('B'.$excel_row.':I'.$excel_row)->applyFromArray($border); 
				$activeSheet->getStyle('B'.$excel_row)->getFont()->setBold(true);
				$excel_row++;
				
				$nextrowmerge = $excel_row+4;
				$activeSheet->mergeCells('E'.$excel_row.':G'.$nextrowmerge)->setCellValue('E'.$excel_row,"         I hereby certify on my official oath that this Report of Cash Disbursements in _________ sheet(s) is a full, true and correct statement of all cash disbursements during the period stated above actually made by me in payment for obligations shown in pertinent disbursement vouchers/payroll.");
				$activeSheet->getStyle('E'.$excel_row)->applyFromArray($textcentertimesnewromanf12);
				$activeSheet->getStyle('B'.$excel_row.':E'.$excel_row)->getAlignment()->setWrapText(true);
				$excel_row = $nextrowmerge+2;

				$activeSheet->mergeCells('F'.$excel_row.':G'.$excel_row)->setCellValue('F'.$excel_row,$disbursing);
				$activeSheet->getStyle('F'.$excel_row.':G'.$excel_row)->applyFromArray($borderbottom);
				$activeSheet->getStyle('F'.$excel_row.':G'.$excel_row)->applyFromArray($textcentertimesnewroman);
				$excel_row++;

				$activeSheet->mergeCells('F'.$excel_row.':G'.$excel_row)->setCellValue('F'.$excel_row,"Name and Signature of Disbursing Officer/Cashier");
				$excel_row+=2;
				$activeSheet->setCellValue("F".$excel_row,$disbursingposi);
				$activeSheet->getStyle('F'.$excel_row)->applyFromArray($borderbottom);
				$curdate = date('m-d-Y');
				$activeSheet->setCellValue("G".$excel_row,$curdate);
				$activeSheet->getStyle('F'.$excel_row.':G'.$excel_row)->applyFromArray($textcentertimesnewroman);

				$excel_row++;
				$activeSheet->setCellValue("F".$excel_row,"Official Designation");
				$activeSheet->setCellValue("G".$excel_row,"Date");
				$activeSheet->getStyle('F'.$excel_row.':G'.$excel_row)->applyFromArray($textcentertimesnewroman);
			//end of sheet 4

				//file export
				$object->setActiveSheetIndex(1);
				$activeSheet->setSelectedCell('A1');
				$activeSheet->setShowGridlines(true);
				$filename = "PREAUDIT_DISBURSEMENTS_" . mb_strtoupper($municipalityname)."_". $year."_".$report_qtrsem."_".date("Y-m-d").".xlsx";

				$writer = new Xlsx($object);
				
				header('Content-Type: application/vnd.ms-excel');
				header('Content-Disposition: attachment;filename="'. $filename); 
				header('Cache-Control: max-age=0');

				$writer->save('php://output');
		// 	}
		// }
	}


// END - PREAUDIT DISBURSEMENT REPORT 

///////////////////// TEST ////////////////////////////////////////////

	public function dlYearUnpaid(){
		$payrollList = $this->lm->get_total_served(["year" => "2018", "liquidation" => 0]);

		$provinces = $this->Main->get_all_provinces();
		$prov_names = array_column($provinces, 'prov_name','prov_code');
		$municipalities = $this->Main->get_all_municipalities();
		$mun_names = array_column($municipalities, 'mun_name','mun_code');
		$barList = $this->Main->getBarangays();
		$bar_names =  array_column($barList, 'bar_name', 'bar_code');

		$exportList = [];

		
		$count_spid = 0;

		$object = new Spreadsheet();
		$object->createSheet(0);
		$object->setActiveSheetIndex(0);
		$activeSheet =$object->getActiveSheet();
		$activeSheet->setTitle("ALL DATA");

		$activeSheet->getColumnDimension('A')->setWidth(15.29); 	//SPID
		$activeSheet->getColumnDimension('B')->setWidth(16.29); //FIRST NAME
		$activeSheet->getColumnDimension('C')->setWidth(16.57); //LAST NAME
		$activeSheet->getColumnDimension('D')->setWidth(16.57); //MIDDLE NAME
		$activeSheet->getColumnDimension('E')->setWidth(16.57); //DOB
		$activeSheet->getColumnDimension('F')->setWidth(16.57); //CP NO.
		$activeSheet->getColumnDimension('G')->setWidth(16.57); // PROVINCE
		$activeSheet->getColumnDimension('H')->setWidth(15);    //MUNICIPALITY
		$activeSheet->getColumnDimension('I')->setWidth(15);	//BARANGAY
		$activeSheet->getColumnDimension('J')->setWidth(15);	//Amount to be paid
		$activeSheet->getColumnDimension('K')->setWidth(15);    //SP Status
		$activeSheet->getColumnDimension('L')->setWidth(15);    //Remarks
		$activeSheet->getColumnDimension('M')->setWidth(15);    //Active Raplacee / Inactive Replacer
		$activeSheet->getColumnDimension('N')->setWidth(15);    //Date of Replacement

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

		$table_columns = array("SPID", "FIRST NAME", "MIDDLE NAME", "LAST NAME", "DATE OF BIRTH", "CONTACT#", "PROVINCE", "MUNICIPALITY", "BARANGAY", "AMOUNT TO BE PAID", "SP STATUS", "REMARKS", "ACTIVE REPLACEE / INACTIVE REPLACER", "DATE OF REPLACEMENT");
		$hs = "A";
		
		foreach ($table_columns as $tv) {
			$activeSheet->setCellValue($hs.$excel_row,$tv);
			$hs++;
		}
		$activeSheet->getStyle('A'.$excel_row.':N'.$excel_row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB("cbf2ce");
		$activeSheet->getStyle('A'.$excel_row.':N'.$excel_row)->applyFromArray($styleArray);
		$activeSheet->getStyle('A'.$excel_row.':N'.$excel_row)->getAlignment()->setWrapText(true);

		$excel_row++;
		$number = 1;

		$all_data = [];

		if(!empty($payrollList)){
			$generalList = $this->lm->get_all_general();
			$firstnames = array_column($generalList, 'firstname', 'connum');
			$lastnames = array_column($generalList, 'lastname', 'connum');
			$middlenames = array_column($generalList, 'middlename', 'connum');
			$extensionnames = array_column($generalList, 'extensionname', 'connum');
			$sp_status = array_column($generalList, 'sp_status', 'connum');
			$inactive_id = array_column($generalList, 'inactive_reason_id', 'connum');
			$inactive_reason = array_column($generalList, 'sp_inactive_remarks', 'connum');
			$birth_dates = array_column($generalList, 'birthdate', 'connum');
			$cp_nos = array_column($generalList, 'contactno', 'connum');

			foreach ($payrollList as $key => $value) {
				$bar_name = isset($bar_names[$value['bar_code']]) ? $bar_names[$value['bar_code']] : "";
				$prov_name = isset($prov_names[$value['prov_code']]) ? $prov_names[$value['prov_code']] : "";
				$mun_name = isset($mun_names[$value['mun_code']]) ? $mun_names[$value['mun_code']] : "";

				$adress = $bar_name . ", " . $mun_name . " " . $prov_name . " ";

				$spid = $value['spid'];
				$first_name = isset($firstnames[$spid]) ? $firstnames[$spid] : "";
				$last_name = isset($lastnames[$spid]) ? $lastnames[$spid] : "";
				$middle_name = isset($middlenames[$spid]) ? $middlenames[$spid] : "";
				$ext_name = isset($extensionnames[$spid]) ? $extensionnames[$spid] : "";
				$last = $last_name . " " . $ext_name;
				$birthdate = isset($birth_dates[$spid]) ? $birth_dates[$spid] : "";
				$contact = isset($cp_nos[$spid]) ? $cp_nos[$spid] : "n/a";
				
				$spstatus = isset($sp_status[$spid]) ? $sp_status[$spid] : "";
				$reason_id = isset($inactive_id[$spid]) ? $inactive_id[$spid] : "";
				$reason = isset($inactive_reason[$spid]) ? $inactive_reason[$spid] : "";
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

				if(strtoupper($value["mode_of_payment"]) == "QUARTER"){
					$amt = 1500;
				}else{
					$amt = 3000;
				}

				if(isset($amount[$spid])){
					$amount[$spid] += $amt;
				}else{
					$amount[$spid] = $amt;
				}

				$raplace_name = "";
				$date_of_forreplacement = "";

				// $replacer = $this->mem->getReplacementHistoryOfPensioner($spid,"replacee");
				// $replacee = $this->mem->getReplacementHistoryOfPensioner($spid,"replacer");
				if( $spstatus == "Active") {
					$repspid = $this->lm->getReplacementHistoryOfPensioner($spid,"replacer");

					if( $repspid != NULL) {
						$rep_spid = $repspid->replacee;
						$rep_first_name = isset($firstnames[$rep_spid]) ? $firstnames[$rep_spid] : "";
						$rep_last_name = isset($lastnames[$rep_spid]) ? $lastnames[$rep_spid] : "";
						$rep_middle_name = isset($middlenames[$rep_spid]) ? $middlenames[$rep_spid] : "";
	
						$raplace_name = "$rep_spid - $rep_last_name , $rep_first_name $rep_middle_name";
						$date_of_forreplacement = $repspid->replacementdate;
					}

				}elseif($spstatus == "Inactive"){
					$repspid = $this->lm->getReplacementHistoryOfPensioner($spid,"replacee");

					if( $repspid != NULL) {
						$rep_spid = $repspid->replacer;
						$rep_first_name = isset($firstnames[$rep_spid]) ? $firstnames[$rep_spid] : "";
						$rep_last_name = isset($lastnames[$rep_spid]) ? $lastnames[$rep_spid] : "";
						$rep_middle_name = isset($middlenames[$rep_spid]) ? $middlenames[$rep_spid] : "";
	
						$raplace_name = "$rep_spid - $rep_last_name , $rep_first_name $rep_middle_name";
						$date_of_forreplacement = $repspid->replacementdate;
					}
				}

				$all_data[$spid] = array(
					"spid"				=>		$spid,
					"first_name"		=>		$first_name,
					"middle_name"		=>		$middle_name,
					"last_name"			=>		$last_name,
					"birthdate"			=>		$birthdate,
					"contact"			=>		$contact,
					"prov_name"			=>		$prov_name,
					"mun_name"			=>		$mun_name,
					"bar_name"			=>		$bar_name,
					"amount"			=>		$amount[$spid],
					"spstatus"			=>		$spstatus,
					"remarks"			=>		$remarks,
					"raplace_name"		=>		$raplace_name,
					"date_of_forreplacement" =>	$date_of_forreplacement,
				);
			}

			foreach ($all_data as $key => $value) {
				$activeSheet->setCellValue("A".$excel_row , $value['spid']);						//SPID
				$activeSheet->setCellValue("B".$excel_row , $value['first_name']);				//FIRST NAME
				$activeSheet->setCellValue("C".$excel_row , $value['middle_name']);  			//MIDDLE NAME
				$activeSheet->setCellValue("D".$excel_row , $value['last_name']);  				//LAST NAME
				$activeSheet->setCellValue("E".$excel_row , $value['birthdate']);				//DOB
				$activeSheet->setCellValue("F".$excel_row , $value['contact']);     				//CP#
				$activeSheet->setCellValue("G".$excel_row , $value['prov_name']);  				//PROVINCE
				$activeSheet->setCellValue("H".$excel_row , $value['mun_name']); 				//MUNICIPALITY
				$activeSheet->setCellValue("I".$excel_row , $value['bar_name']);   				//BARANGAY
				$activeSheet->setCellValue("J".$excel_row , $value['amount']);			//aMOUNT TO BE PAID
				$activeSheet->setCellValue("K".$excel_row , $value['spstatus']);					//SP STATUS
				$activeSheet->setCellValue("L".$excel_row , $value['remarks']);					//Remarks		
				$activeSheet->setCellValue("M".$excel_row , $value['raplace_name']);	 			//Active Raplacee / Inactive Replacer
				$activeSheet->setCellValue("N".$excel_row , $value['date_of_forreplacement']); 	//Date of Replacement
				
				$activeSheet->getStyle('A'.$excel_row.':N'.$excel_row)->applyFromArray($bodynamesp);
				$excel_row++;
			}
		}



		$dt = date("Y-m-d h-i-sa");
		$object->setActiveSheetIndex(0);
		$activeSheet->setSelectedCell('A1');
		
		$filename = "2019_UNPAID_($count_spid)  $dt.xlsx";
		$writer = new Xlsx($object);

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename); 
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
	}

	public function dlCleanList(){
		$payrollList = $this->lm->get_all_yearbenes(["year" => "2019"]);

		$provinces = $this->Main->get_all_provinces();
		$prov_names = array_column($provinces, 'prov_name','prov_code');
		$municipalities = $this->Main->get_all_municipalities();
		$mun_names = array_column($municipalities, 'mun_name','mun_code');
		$barList = $this->Main->getBarangays();
		$bar_names =  array_column($barList, 'bar_name', 'bar_code');

		$exportList = [];

		$count_spid = 0;

		$object = new Spreadsheet();
		$object->createSheet(0);
		$object->setActiveSheetIndex(0);
		$activeSheet =$object->getActiveSheet();
		$activeSheet->setTitle("ALL DATA");

		$activeSheet->getColumnDimension('A')->setWidth(15.29); 	//SPID
		$activeSheet->getColumnDimension('B')->setWidth(16.29); //FIRST NAME
		$activeSheet->getColumnDimension('C')->setWidth(16.57); //LAST NAME
		$activeSheet->getColumnDimension('D')->setWidth(16.57); //MIDDLE NAME
		$activeSheet->getColumnDimension('E')->setWidth(16.57); //DOB
		$activeSheet->getColumnDimension('F')->setWidth(16.57); //CP NO.
		$activeSheet->getColumnDimension('G')->setWidth(16.57); // PROVINCE
		$activeSheet->getColumnDimension('H')->setWidth(15);    //MUNICIPALITY
		$activeSheet->getColumnDimension('I')->setWidth(15);	//BARANGAY
		$activeSheet->getColumnDimension('J')->setWidth(15);    //SP Status
		$activeSheet->getColumnDimension('K')->setWidth(15);    //Remarks
		
		$activeSheet->getColumnDimension('L')->setWidth(15.29); 	//SPID
		$activeSheet->getColumnDimension('M')->setWidth(16.29); //FIRST NAME
		$activeSheet->getColumnDimension('N')->setWidth(16.57); //LAST NAME
		$activeSheet->getColumnDimension('O')->setWidth(16.57); //MIDDLE NAME
		$activeSheet->getColumnDimension('P')->setWidth(16.57); //DOB
		$activeSheet->getColumnDimension('Q')->setWidth(16.57); //CP NO.
		$activeSheet->getColumnDimension('R')->setWidth(16.57); // PROVINCE
		$activeSheet->getColumnDimension('S')->setWidth(15);    //MUNICIPALITY
		$activeSheet->getColumnDimension('T')->setWidth(15);	//BARANGAY
		$activeSheet->getColumnDimension('U')->setWidth(15);    //SP Status
		$activeSheet->getColumnDimension('V')->setWidth(15);    //Remarks

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

		$table_columns = array("SPID", "FIRST NAME", "MIDDLE NAME", "LAST NAME", "DATE OF BIRTH", "CONTACT#", "PROVINCE", "MUNICIPALITY", "BARANGAY", "SP STATUS", "REMARKS", "REPLACER SPID", "FIRST NAME", "MIDDLE NAME", "LAST NAME", "DATE OF BIRTH", "CONTACT#", "PROVINCE", "MUNICIPALITY", "BARANGAY", "SP STATUS", "REMARKS");
		$hs = "A";
		
		foreach ($table_columns as $tv) {
			$activeSheet->setCellValue($hs.$excel_row,$tv);
			$hs++;
		}
		$activeSheet->getStyle('A'.$excel_row.':V'.$excel_row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB("cbf2ce");
		$activeSheet->getStyle('A'.$excel_row.':V'.$excel_row)->applyFromArray($styleArray);
		$activeSheet->getStyle('A'.$excel_row.':V'.$excel_row)->getAlignment()->setWrapText(true);

		$excel_row++;
		$number = 1;

		$all_data = [];

		if(!empty($payrollList)){
			$generalList = $this->lm->get_all_general();
			$firstnames = array_column($generalList, 'firstname', 'connum');
			$lastnames = array_column($generalList, 'lastname', 'connum');
			$middlenames = array_column($generalList, 'middlename', 'connum');
			$extensionnames = array_column($generalList, 'extensionname', 'connum');
			$sp_status = array_column($generalList, 'sp_status', 'connum');
			$inactive_id = array_column($generalList, 'inactive_reason_id', 'connum');
			$inactive_reason = array_column($generalList, 'sp_inactive_remarks', 'connum');
			$birth_dates = array_column($generalList, 'birthdate', 'connum');
			$cp_nos = array_column($generalList, 'contactno', 'connum');
			$provcodes = array_column($generalList, 'province', 'connum');
			$muncodes = array_column($generalList, 'city', 'connum');
			$barcodes = array_column($generalList, 'barangay', 'connum');

			foreach ($payrollList as $key => $value) {
				$spid = $value['spid'];

				$prov = isset($provcodes[$spid]) ? $provcodes[$spid] : "";
				$mun = isset($muncodes[$spid]) ? $muncodes[$spid] : "";
				$bar = isset($barcodes[$spid]) ? $barcodes[$spid] : "";

				$bar_name = isset($bar_names[$bar]) ? $bar_names[$bar] : "";
				$prov_name = isset($prov_names[$prov]) ? $prov_names[$prov] : "";
				$mun_name = isset($mun_names[$mun]) ? $mun_names[$mun] : "";

				$first_name = isset($firstnames[$spid]) ? $firstnames[$spid] : "";
				$last_name = isset($lastnames[$spid]) ? $lastnames[$spid] : "";
				$middle_name = isset($middlenames[$spid]) ? $middlenames[$spid] : "";
				$ext_name = isset($extensionnames[$spid]) ? $extensionnames[$spid] : "";
				$last = $last_name . " " . $ext_name;
				$birthdate = isset($birth_dates[$spid]) ? $birth_dates[$spid] : "";
				$contact = isset($cp_nos[$spid]) ? $cp_nos[$spid] : "n/a";
				
				$spstatus = isset($sp_status[$spid]) ? $sp_status[$spid] : "";
				$reason_id = isset($inactive_id[$spid]) ? $inactive_id[$spid] : "";
				$reason = isset($inactive_reason[$spid]) ? $inactive_reason[$spid] : "";
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

				// if(strtoupper($value["mode_of_payment"]) == "QUARTER"){
				// 	$amt = 1500;
				// }else{
				// 	$amt = 3000;
				// }

				// if(isset($amount[$spid])){
				// 	$amount[$spid] += $amt;
				// }else{
				// 	$amount[$spid] = $amt;
				// }
				$rep_spid = "";
				$rep_first_name = "";
				$rep_middle_name = "";
				$rep_last_name = "";
				$rep_birthdate = "";
				$rep_contact = "";
				$rep_prov_name = "";
				$rep_mun_name = "";
				$rep_bar_name = "";
				$rep_spstatus = "";
				$rep_remarks = "";

				if($spstatus == "Inactive"){
					$repspid = $this->lm->getReplacementHistoryOfPensioner($spid,"replacee");
					if( $repspid != NULL) {
						$rep_spid = $repspid->replacer;
						$rep_first_name = isset($firstnames[$rep_spid]) ? $firstnames[$rep_spid] : "";
						$rep_last_name = isset($lastnames[$rep_spid]) ? $lastnames[$rep_spid] : "";
						$rep_middle_name = isset($middlenames[$rep_spid]) ? $middlenames[$rep_spid] : "";
						$rep_ext_name = isset($extensionnames[$rep_spid]) ? $extensionnames[$rep_spid] : "";
						$rep_last = $last_name . " " . $ext_name;
						$rep_birthdate = isset($birth_dates[$rep_spid]) ? $birth_dates[$rep_spid] : "";
						$rep_contact = isset($cp_nos[$rep_spid]) ? $cp_nos[$rep_spid] : "n/a";

						$rep_prov = isset($provcodes[$rep_spid]) ? $provcodes[$rep_spid] : "";
						$rep_mun = isset($muncodes[$rep_spid]) ? $muncodes[$rep_spid] : "";
						$rep_bar = isset($barcodes[$rep_spid]) ? $barcodes[$rep_spid] : "";

						$rep_bar_name = isset($bar_names[$rep_bar]) ? $bar_names[$rep_bar] : "";
						$rep_prov_name = isset($prov_names[$rep_prov]) ? $prov_names[$rep_prov] : "";
						$rep_mun_name = isset($mun_names[$rep_mun]) ? $mun_names[$rep_mun] : "";

						$rep_spstatus = isset($sp_status[$spid]) ? $sp_status[$spid] : "";
						$rep_reason_id = isset($inactive_id[$spid]) ? $inactive_id[$spid] : "";
						$rep_reason = isset($inactive_reason[$spid]) ? $inactive_reason[$spid] : "";
						$rep_remarks = "";

						if((int)$rep_reason_id == 1){ $rep_remarks = "Double Entry";}	
						if((int)$rep_reason_id == 2){ $rep_remarks = "Deceased";}		
						if((int)$rep_reason_id == 3){ $rep_remarks = "With Regular Support";}
						if((int)$rep_reason_id == 4){ $rep_remarks = "With Pension";}
						if((int)$rep_reason_id == 5){ $rep_remarks = "Cannot be located";}
						if((int)$rep_reason_id == 6){ $rep_remarks = "Transferred";}
						if((int)$rep_reason_id == 7){ $rep_remarks = "Underage - age 59 and below";}
						if((int)$rep_reason_id == 8){ $rep_remarks = "Not Interested";}
						if((int)$rep_reason_id == 11){ $rep_remarks = "Improved Quality of Life";}
						if((int)$rep_reason_id == 12){ $rep_remarks = "With Regular Income";}
						if((int)$rep_reason_id == 13){ $rep_remarks = "Out of town";}
						if((int)$rep_reason_id == 14){ $rep_remarks = "Not Eligible";}
						if((int)$rep_reason_id == 15){ $rep_remarks = "OFW";}
						if((int)$rep_reason_id == 16){ $rep_remarks = "Barangay Official";}

						if(!empty($rep_reason)){
							$rep_remarks .= "( $rep_reason )";
						}
					}
				}

				$all_data[$spid] = array(
					"spid"				=>		$spid,
					"first_name"		=>		$first_name,
					"middle_name"		=>		$middle_name,
					"last_name"			=>		$last_name,
					"birthdate"			=>		$birthdate,
					"contact"			=>		$contact,
					"prov_name"			=>		$prov_name,
					"mun_name"			=>		$mun_name,
					"bar_name"			=>		$bar_name,
					"spstatus"			=>		$spstatus,
					"remarks"			=>		$remarks,
					"rep_spid"			=>		$rep_spid,
					"rep_first_name"	=>		$rep_first_name,
					"rep_middle_name"	=>		$rep_middle_name,
					"rep_last_name"		=>		$rep_last_name,
					"rep_birthdate"		=>		$rep_birthdate,
					"rep_contact"		=>		$rep_contact,
					"rep_prov_name"		=>		$rep_prov_name,
					"rep_mun_name"		=>		$rep_mun_name,
					"rep_bar_name"		=>		$rep_bar_name,
					"rep_spstatus"		=>		$rep_spstatus,
					"rep_remarks"		=>		$rep_remarks,
				);
			}

			foreach ($all_data as $key => $value) {
				$activeSheet->setCellValue("A".$excel_row , $value['spid']);						//SPID
				$activeSheet->setCellValue("B".$excel_row , $value['first_name']);				//FIRST NAME
				$activeSheet->setCellValue("C".$excel_row , $value['middle_name']);  			//MIDDLE NAME
				$activeSheet->setCellValue("D".$excel_row , $value['last_name']);  				//LAST NAME
				$activeSheet->setCellValue("E".$excel_row , $value['birthdate']);				//DOB
				$activeSheet->setCellValue("F".$excel_row , $value['contact']);     				//CP#
				$activeSheet->setCellValue("G".$excel_row , $value['prov_name']);  				//PROVINCE
				$activeSheet->setCellValue("H".$excel_row , $value['mun_name']); 				//MUNICIPALITY
				$activeSheet->setCellValue("I".$excel_row , $value['bar_name']);   				//BARANGAY
				$activeSheet->setCellValue("J".$excel_row , $value['spstatus']);					//SP STATUS
				$activeSheet->setCellValue("K".$excel_row , $value['remarks']);					//Remarks	
				
				$activeSheet->setCellValue("L".$excel_row , $value['rep_spid']);						//SPID
				$activeSheet->setCellValue("M".$excel_row , $value['rep_first_name']);				//FIRST NAME
				$activeSheet->setCellValue("N".$excel_row , $value['rep_middle_name']);  			//MIDDLE NAME
				$activeSheet->setCellValue("O".$excel_row , $value['rep_last_name']);  				//LAST NAME
				$activeSheet->setCellValue("P".$excel_row , $value['rep_birthdate']);				//DOB
				$activeSheet->setCellValue("Q".$excel_row , $value['rep_contact']);     				//CP#
				$activeSheet->setCellValue("R".$excel_row , $value['rep_prov_name']);  				//PROVINCE
				$activeSheet->setCellValue("S".$excel_row , $value['rep_mun_name']); 				//MUNICIPALITY
				$activeSheet->setCellValue("T".$excel_row , $value['rep_bar_name']);   				//BARANGAY
				$activeSheet->setCellValue("U".$excel_row , $value['rep_spstatus']);					//SP STATUS
				$activeSheet->setCellValue("V".$excel_row , $value['rep_remarks']);					//Remarks		
				
				$activeSheet->getStyle('A'.$excel_row.':V'.$excel_row)->applyFromArray($bodynamesp);
				$excel_row++;
			}
		}



		$dt = date("Y-m-d h-i-sa");
		$object->setActiveSheetIndex(0);
		$activeSheet->setSelectedCell('A1');
		
		$filename = "2019_LIST_($count_spid)  $dt.xlsx";
		$writer = new Xlsx($object);

		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename); 
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
	}

	//Download payment history of current beneficiary
	public function download_active_payments(){
		$provinces = $this->Main->get_all_provinces();
		$prov_name_list = array_column($provinces, 'prov_name','prov_code');
		$municipalities = $this->Main->get_all_municipalities();
		$mun_name_list = array_column($municipalities, 'mun_name','mun_code');
		$barangays = $this->Main->getBarangays();
		$bar_name_list = array_column($barangays, 'bar_name','bar_code');

		$content = "osca_id , connum , lastname , firstname , middlename , extensionname , region , permanent_province , permanent_city , permanent_barangay , permanent_address , permanent_street , region , province , city , barangay , address , street , gender , birthdate  , Q1_paid , Q1_UNpaid , Q2_paid , Q2_Unpaid , S1_paid , S1_Upaid , S2_paid , S2_Upaid , S11_paid , S11_Upaid, sp_status \r\n";

		$prov_code = $this->input->get('prov_code');

		$mun_code = $this->input->get('mun_code');

		$condi = array("mode_of_payment" => "QUARTER", "period" => "1","year" => "2019");
		if(!empty($prov_code)) {
			$condi["prov_code"] = $prov_code;
		}
		
		$this->db->select("spid,liquidation");
		$this->db->from("tblpayroll");
		$this->db->where(array("mode_of_payment" => "QUARTER", "period" => "1","year" => "2019"));
		$query = $this->db->get();
		$q1_payment = $query->result_array();
		$q1 = array_column($q1_payment, 'liquidation','spid');
		//$q1 = array_change_key_case($q1,CASE_UPPER);
		
		$this->db->select("spid,liquidation");
		$this->db->from("tblpayroll");
		$this->db->where(array("mode_of_payment" => "QUARTER", "period" => "2","year" => "2019"));
		$query = $this->db->get();
		$q2_payment = $query->result_array();
		$q2 = array_column($q2_payment, 'liquidation','spid');
		//$q2 = array_change_key_case($q2,CASE_UPPER);
		
		$this->db->select("spid,liquidation");
		$this->db->from("tblpayroll");
		$this->db->where(array("mode_of_payment" => "SEMESTER", "period" => "1","year" => "2019"));
		$query = $this->db->get();
		$s1_payment = $query->result_array();
		$s1 = array_column($s1_payment, 'liquidation','spid');
		//$s1 = array_change_key_case($s1,CASE_UPPER);
		
		$this->db->select("spid,liquidation");
		$this->db->from("tblpayroll");
		$this->db->where(array("mode_of_payment" => "SEMESTER", "period" => "2","year" => "2019"));
		$query = $this->db->get();
		$s2_payment = $query->result_array();
		$s2 = array_column($s2_payment, 'liquidation','spid');
		//$s2 = array_change_key_case($s2,CASE_UPPER);
		
		$this->db->select("spid,liquidation");
		$this->db->from("tblpayroll");
		$this->db->where(array("mode_of_payment" => "SEMESTER", "period" => "1","year" => "2020"));
		$query = $this->db->get();
		$s11_payment = $query->result_array();
		$s11 = array_column($s11_payment, 'liquidation','spid');
		//$s11 = array_change_key_case($s11,CASE_UPPER);

				
		// $this->db->select("DISTINCT(spid)");
		// $this->db->from("tblpayroll");
		// $query = $this->db->get();
		// $spids = $query->result();
		$this->db->select("connum,osca_id, lastname, firstname, middlename, extensionname, province, city, barangay, address, street,permanent_province, permanent_city, permanent_barangay, permanent_address, permanent_street, gender,birthdate,sp_status,inactive_reason_id");
		$this->db->from("tblgeneral");
		$condition = array("sp_status<>" => "Inactive");
		if(!empty($prov_code)) {
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
				$permanent_province = (isset($prov_name_list[$bene->permanent_province]))? $prov_name_list[$bene->permanent_province] : " ";
				$permanent_city =(isset($mun_name_list[$bene->permanent_city]))? $mun_name_list[$bene->permanent_city]: " ";
				$permanent_barangay = (isset($bar_name_list[$bene->permanent_barangay]))? $bar_name_list[$bene->permanent_barangay]: " ";
	
				// $permanent_province = str_replace(",","",$permanent_province);
				// $permanent_city = str_replace(",","",$permanent_city);
				$permanent_barangay = str_replace(","," ",$permanent_barangay);
				$permanent_address = str_replace(","," ",$bene->permanent_address);
				$permanent_street = str_replace(","," ",$bene->permanent_street);
			
				$province =(isset($prov_name_list[$bene->province]))? $prov_name_list[$bene->province] : " ";
				$city =(isset($mun_name_list[$bene->city]))? $mun_name_list[$bene->city]: " ";
				$barangay =(isset($bar_name_list[$bene->barangay]))? $bar_name_list[$bene->barangay]: " ";

				
				// $province =	$bene->province;
				// $city =	$bene->city;
				// $barangay =	$bene->barangay;
	
				// $province = str_replace(",","",$province);
				// $city = str_replace(",","",$city);
				$barangay = str_replace(","," ",$barangay);
				$address = str_replace(","," ",$bene->address);
				$street = str_replace(","," ",$bene->street);
	
				// $extensionname = str_replace(",","",$bene->extensionname);
				// $middlename = str_replace(",","",$bene->middlename);
				// $firstname = str_replace(",","",$bene->firstname);
				// $lastname = str_replace(",","",$bene->lastname);

				$extensionname = $bene->extensionname;
				$middlename = $bene->middlename;
				$firstname = $bene->firstname;
				$lastname = $bene->lastname;
			
				$spid = $bene->connum;
				$q1_paid = (isset($q1[$spid]))? $q1[$spid]: "--";
				$q2_paid = (isset($q2[$spid]))? $q2[$spid]: "--";
				$s1_paid = (isset($s1[$spid]))? $s1[$spid]: "--";
				$s2_paid = (isset($s2[$spid]))? $s2[$spid]: "--";
				$s11_paid = (isset($s11[$spid]))? $s11[$spid]: "--";
	
				$status = $bene->sp_status;			
				if($status == "ForReplacement" && $bene->inactive_reason_id == "1"){
					$status = "For Replacement - Double Entry";
				}	
				if($status == "ForReplacement" &&  $bene->inactive_reason_id == "2"){
					$status = "For Replacement - Deceased";
				}		
				if($status == "ForReplacement" &&  $bene->inactive_reason_id == "3"){
					$status = "For Replacement - With Regular Support";
				}
				if($status == "ForReplacement" &&  $bene->inactive_reason_id == "4"){
					$status = "For Replacement - With Pension";
				}
				if($status == "ForReplacement" &&  $bene->inactive_reason_id == "6"){
					$status = "For Replacement - Transferred";
				}
				if($status == "ForReplacement"  && $bene->inactive_reason_id == "16"){
					$status = "For Replacement - Barangay Official";
				}
				
				//$osca_id = str_replace(",","",$bene->osca_id);
				$osca_id = $bene->osca_id;
				
				$cnt = "$osca_id,$bene->connum,$lastname,$firstname,$middlename,$extensionname,CAR[Cordillera Administrative Region],$permanent_province,$permanent_city,$permanent_barangay,$permanent_address,$permanent_street,CAR[Cordillera Administrative Region],$province,$city,$barangay,$address,$street,$bene->gender,$bene->birthdate,$q1_paid,$q1_paid,$q2_paid,$q2_paid,$s1_paid,$s1_paid,$s2_paid,$s2_paid,$s11_paid,$s11_paid,$status \r\n";

				$content .= $cnt;

				//echo "$content <br>";


			// }
			
		}
		$pname =(isset($prov_name_list[$prov_code]))? $prov_name_list[$prov_code] : " ";
		$mname =(isset($mun_name_list[$mun_code]))? $mun_name_list[$mun_code] : " ";

		$dt = date("Y-m-d");

		$filepath = "downloads/$pname _ $dt _active_payment_history_($count_spid).csv";

		file_put_contents($filepath, $content);

		// Process download
		if(file_exists($filepath)) {
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="'.basename($filepath).'"');
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
	public function download_waitlist(){
		$provinces = $this->Main->get_all_provinces();
		$prov_name_list = array_column($provinces, 'prov_name','prov_code');
		$municipalities = $this->Main->get_all_municipalities();
		$mun_name_list = array_column($municipalities, 'mun_name','mun_code');
		$barangays = $this->Main->getBarangays();
		$bar_name_list = array_column($barangays, 'bar_name','bar_code');

		$content = "osca_id , connum , lastname , firstname , middlename , extensionname , region , permanent_province , permanent_city , permanent_barangay , permanent_address , permanent_street , region , province , city , barangay , address , street , gender , birthdate,status,duplicate \r\n";

		$this->db->select("osca_id, reference_code, lastname, firstname, middlename, extname, prov_code, mun_code, bar_code, address, street,permanent_prov_code, permanent_mun_code, permanent_bar_code, permanent_address, permanent_street, gender,birthdate,priority,duplicate,sent_to_co");
		$this->db->from("tblwaitinglist");
		$this->db->where(array("archived" => "0"));
		$query = $this->db->get();
		$spids = $query->result();
		$count_spid = count($spids);
		
		foreach ($spids as $bene) {

				$permanent_province = (isset($prov_name_list[$bene->permanent_prov_code]))? $prov_name_list[$bene->permanent_prov_code] : "";
				$permanent_city =(isset($mun_name_list[$bene->permanent_mun_code]))? $mun_name_list[$bene->permanent_mun_code]: "";
				$permanent_brgy = (isset($bar_name_list[$bene->permanent_bar_code]))? $bar_name_list[$bene->permanent_bar_code]: "";
	
				$permanent_province = str_replace(",","",$permanent_province);
				$permanent_city = str_replace(",","",$permanent_city);
				$permanent_barangay = str_replace(",","",$permanent_brgy);
				$permanent_address = str_replace(",","",$bene->permanent_address);
				$permanent_street = str_replace(",","",$bene->permanent_street);
			
				$province =(isset($prov_name_list[$bene->prov_code]))? $prov_name_list[$bene->prov_code] : "";
				$city =(isset($mun_name_list[$bene->mun_code]))? $mun_name_list[$bene->mun_code]: "";
				$brgy =(isset($bar_name_list[$bene->bar_code]))? $bar_name_list[$bene->bar_code]: "";
	
				$province = str_replace(",","",$province);
				$city = str_replace(",","",$city);
				$barangay = str_replace(",","",$brgy);
				$address = str_replace(",","",$bene->address);
				$street = str_replace(",","",$bene->street);
	
				$extensionname = str_replace(",","",$bene->extname);
				$middlename = str_replace(",","",$bene->middlename);
				$firstname = str_replace(",","",$bene->firstname);
				$lastname = str_replace(",","",$bene->lastname);

				$status = "";

				if($bene->priority == "0"){
					if($bene->sent_to_co == "1"){
						$status = "WAITING FOR ELIGIBILITY (ALREADY SENT TO CO)";
					}else{
						$status = "ADDITIONAL WAITLIST (FOR CROSSMATCHING AND ELIGIBILITY CHECKING)";
					}
				}else if($bene->priority == "1"){
					$status = "ELIGIBLE WAITLIST";
				}else{
					$status = $bene->priority;
				}

				$osca_id = str_replace(",","",$bene->osca_id);
				$content .= "$osca_id,$bene->reference_code,$lastname,$firstname,$middlename,$extensionname,CAR[Cordillera Administrative Region],$permanent_province,$permanent_city,$permanent_barangay,$permanent_address,$permanent_street,CAR[Cordillera Administrative Region],$province,$city,$barangay,$address,$street,$bene->gender,$bene->birthdate,$status,$bene->duplicate \r\n";
			
		}
		$dt = date("Y-m-d");

		$filepath = "downloads/eligible_waitlist_ $dt _($count_spid).csv";
		//echo "Saving Data";
		file_put_contents($filepath , $content);
		// echo "Saved Success";

		// echo "downloading";

		// Process download
		if(file_exists($filepath)) {
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="'.basename($filepath).'"');
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

	public function download_payroll_list(){
		$provinces = $this->Main->get_all_provinces();
		$prov_name_list = array_column($provinces, 'prov_name','prov_code');
		$municipalities = $this->Main->get_all_municipalities();
		$mun_name_list = array_column($municipalities, 'mun_name','mun_code');
		$barangays = $this->Main->getBarangays();
		$bar_name_list = array_column($barangays, 'bar_name','bar_code');

		$content = "osca_id , connum , lastname , firstname , middlename , extensionname , province , city , barangay , address , street , gender , birthdate  , 2019 Q1 paid , 2019 Q2 paid , 2019 S1 paid , 2019 S2 paid , 2020 S1 paid, sp_status, reason \r\n";
		
		$this->db->select("spid,liquidation");
		$this->db->from("tblpayroll");
		$this->db->where(array("mode_of_payment" => "QUARTER", "period" => "1","year" => "2019"));
		$query = $this->db->get();
		$q1_payment = $query->result_array();
		$q1 = array_column($q1_payment, 'liquidation','spid');
		
		$this->db->select("spid,liquidation");
		$this->db->from("tblpayroll");
		$this->db->where(array("mode_of_payment" => "QUARTER", "period" => "2","year" => "2019"));
		$query = $this->db->get();
		$q2_payment = $query->result_array();
		$q2 = array_column($q2_payment, 'liquidation','spid');
		
		$this->db->select("spid,liquidation");
		$this->db->from("tblpayroll");
		$this->db->where(array("mode_of_payment" => "SEMESTER", "period" => "1","year" => "2019"));
		$query = $this->db->get();
		$s1_payment = $query->result_array();
		$s1 = array_column($s1_payment, 'liquidation','spid');
		
		$this->db->select("spid,liquidation");
		$this->db->from("tblpayroll");
		$this->db->where(array("mode_of_payment" => "SEMESTER", "period" => "2","year" => "2019"));
		$query = $this->db->get();
		$s2_payment = $query->result_array();
		$s2 = array_column($s2_payment, 'liquidation','spid');
		
		$this->db->select("spid,liquidation");
		$this->db->from("tblpayroll");
		$this->db->where(array("mode_of_payment" => "SEMESTER", "period" => "1","year" => "2020"));
		$query = $this->db->get();
		$s11_payment = $query->result_array();
		$s11 = array_column($s11_payment, 'liquidation','spid');

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
			
			if(empty($bene)){

				$content .= "$connum->spid . \r\n";

			}else{
			
				$province =(isset($prov_name_list[$bene->province]))? $prov_name_list[$bene->province] : " ";
				$city =(isset($mun_name_list[$bene->city]))? $mun_name_list[$bene->city]: " ";
				$barangay =(isset($bar_name_list[$bene->barangay]))? $bar_name_list[$bene->barangay]: " ";

				$barangay = str_replace(","," ",$barangay);
				$address = str_replace(","," ",$bene->address);
				$street = str_replace(","," ",$bene->street);

				$extensionname = $bene->extensionname;
				$middlename = $bene->middlename;
				$firstname = $bene->firstname;
				$lastname = $bene->lastname;
				$status = $bene->sp_status;		
				$osca_id = $bene->osca_id;

				$reason = "";

				if($bene->inactive_reason_id == "1"){
					$reason = "For Replacement - Double Entry";
				}	
				if($bene->inactive_reason_id == "2"){
					$reason = "For Replacement - Deceased";
				}		
				if($bene->inactive_reason_id == "3"){
					$reason = "For Replacement - With Regular Support";
				}
				if($bene->inactive_reason_id == "4"){
					$reason = "For Replacement - With Pension";
				}
				if($bene->inactive_reason_id == "6"){
					$reason = "For Replacement - Transferred";
				}
				if($bene->inactive_reason_id == "16"){
					$reason = "For Replacement - Barangay Official";
				}
			
				$q1_paid = (isset($q1[$spid]))? $q1[$spid]: "--";
				$q2_paid = (isset($q2[$spid]))? $q2[$spid]: "--";
				$s1_paid = (isset($s1[$spid]))? $s1[$spid]: "--";
				$s2_paid = (isset($s2[$spid]))? $s2[$spid]: "--";
				$s11_paid = (isset($s11[$spid]))? $s11[$spid]: "--";
	
				$content .= "$osca_id,$spid,$lastname,$firstname,$middlename,$extensionname ,$province,$city,$barangay,$address,$street,$bene->gender,$bene->birthdate,$q1_paid,$q2_paid,$s1_paid,$s2_paid,$s11_paid,$status,$reason \r\n";

			}
			
		}

		$filepath = "downloads/payroll_payment_history_($count_spid).csv";

		file_put_contents($filepath, $content);

		// Process download
		if(file_exists($filepath)) {
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="'.basename($filepath).'"');
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

	public function updatepayrollhistory(){

		$qrt = $this->input->get('qrt');	//148100000

		$payrollList = [];
		$this->db->select("*");
        $this->db->from("tblsocpen");
		$this->db->where("year","2018");
		$this->db->where("quarter",$qrt);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $payrollList =  $query->result_array();
		}

		if(!empty($payrollList)){
			$insert_data = [];
			$generalList = $this->lm->get_all_general();

			$spids = array_column($generalList, 'connum', 'b_id');
			$provcodes = array_column($generalList, 'province', 'b_id');
			$muncodes = array_column($generalList, 'city', 'b_id');
			$barcodes = array_column($generalList, 'barangay', 'b_id');
			$reasonids = array_column($generalList, 'inactive_reason_id', 'b_id');

			foreach ($payrollList as $key => $value) {
				$bid = $value["b_id"];

				$insert_data[] = array(
					"spid" 				=> $spids[$bid],
					"prov_code" 		=> $provcodes[$bid],
					"mun_code" 			=> $muncodes[$bid],
					"bar_code" 			=> $barcodes[$bid],
					"year" 				=> "2018",
					"mode_of_payment" 	=> "QUARTER",
					"period" 			=> $value["quarter"],
					"amount"			=> $value["amount"],
					"receiver"			=> $value["receiver"],
					"date_receive" 		=> $value["datereceived"],
					"liquidation" 		=> 0,
					"reason_id" 		=> $reasonids[$bid],
					"remarks" 			=> $value["remarks"],
					"sp_dateupdated" 	=> $value["sp_dateupdated"],
				);
			}
			
			$this->db->insert_batch('tblpayroll', $insert_data);

			print_r(count($insert_data));
			print_r("success");

		}

	}
    public function exportPaymentLiquidation(){
		$prov_code = $this->input->get('prov_code');
		$mun_code = "";
		$bar_code = "";
		$year = $this->input->get('year');

		if($this->input->get('mun_code') !== null && $this->input->get('mun_code') != ""){
			$mun_code = $this->input->get('mun_code');
		}
		if($this->input->get('bar_code') !== null && $this->input->get('bar_code') != ""){
			$bar_code = $this->input->get('bar_code');
		}

		//GET LIBRARIES
		$generalList = $this->rm->get_all_general();
		$remarklist = array_column($generalList, 'remarks', 'connum');
		$sp_status = array_column($generalList, 'sp_status', 'connum');
		$reason_ids = array_column($generalList, 'inactive_reason_id', 'connum');
		$reasons = array_column($generalList, 'sp_inactive_remarks', 'connum');
		$birthdates = array_column($generalList, 'birthdate', 'connum');
		$barcodes = array_column($generalList, 'barangay', 'connum');
		$muncodes = array_column($generalList, 'city', 'connum');

		$reasons_lib = $this->Main->getLibraries("tblinactivereason");
		$reason_names = array_column($reasons_lib , 'name', 'id');

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
        //END GET LIBRARIES
        $unpaidCon = array(
            "prov_code" => $prov_code,
            "year" => $year,
            "liquidation <>" => 2,
        );
        if($mun_code != ""){
            $unpaidCon["mun_code"] = $mun_code;
		}
		
		$ce_type = $this->input->get('type');
		if($ce_type != "all"){
			if($ce_type == "3"){
                $unpaidCon["additional"] = [1,2];
            }else{
                $unpaidCon["additional"] = $ce_type;
            }
		}

        $unpaidList = $this->pm->get_payroll($unpaidCon);

		$replacements = $this->pm->get_all_replacement();
		$rep_list =  array_column($replacements, 'replacer', 'replacee');

        $activeList = [];
        $inactive_list = [];

        foreach ($unpaidList as $key => $value) {

            if($value["eligible"] != 1 && $value["eligible"] != 2){
                continue;
            }
            $spid = $value["spid"];
            $amount = $value["amount"];
            
            $prov_name = isset($prov_names[$value["prov_code"]])?$prov_names[$value["prov_code"]] : "";
            $mun_name = isset($mun_names[$value["mun_code"]])?$mun_names[$value["mun_code"]] : "";
            $bar_name = isset($bar_names[$value["bar_code"]])?$bar_names[$value["bar_code"]] : "";
            
			$fullname = isset($fullnameList[$spid]) ? $fullnameList[$spid] : "Not Found";
            $birthdate = isset($birthdates[$spid]) ? $birthdates[$spid] : "";
            $remark = isset($remarklist[$spid]) ? $remarklist[$spid] : "";
            $sp_inactive_remarks = isset($reasons[$spid]) ? $reasons[$spid] : "";
            $inactive_reason_id = isset($reason_ids[$spid]) ? $reason_ids[$spid] : "";
            
            $spstat = isset($sp_status[$spid]) ? $sp_status[$spid] : "";
            
            if( strtoupper($spstat) == "INACTIVE" || strtoupper($spstat) == "FORREPLACEMENT"){
                $reasonforrep="No reason set."; 
                if(!empty($reason_names[$inactive_reason_id])){
                    $reasonforrep = isset($reason_names[$inactive_reason_id]) ? $reason_names[$inactive_reason_id] : "";
                    if( strtoupper($reasonforrep)=="DECEASED"){
                        if(!empty($sp_inactive_remarks)){
                            $reasonforrep = $reasonforrep." (".date_format(new DateTime($sp_inactive_remarks),"Y-m-d").")";
                        }
                    }else{
                        if(!empty($ml->sp_inactive_remarks)){
                            $reasonforrep = $reasonforrep." (".$sp_inactive_remarks.")";
                        }
                    }
                }

                $fullname = $fullname . "( $reasonforrep )";

                $replacer_spid = isset($rep_list[$spid]) ? $rep_list[$spid] : "";
                $replacer_fullname = isset($fullnameList[$replacer_spid]) ? $fullnameList[$replacer_spid] : "";
				$replacer_birthdate = isset($birthdates[$replacer_spid]) ? $birthdates[$replacer_spid] : "";
                $replacer_muncode = isset($muncodes[$replacer_spid]) ? $muncodes[$replacer_spid] : "";
                $replacer_barcode = isset($barcodes[$replacer_spid]) ? $barcodes[$replacer_spid] : "";

                $replacer_munname = isset($mun_names[$replacer_muncode])?$mun_names[$replacer_muncode] : "";
				$replacer_barname = isset($bar_names[$replacer_barcode])?$bar_names[$replacer_barcode] : "";
				
				if(!empty($replacer_spid)){
                    $spid .= "\r\n" . $replacer_spid;
                    $fullname .= "\r\n" . "Replacer: " . $replacer_fullname. "\r\n" . "Address: $replacer_barname, $replacer_munname";
                    $remark = "Replacer Birthdate: $replacer_birthdate" . "\r\n" . $remark;
				}
                
                // if($year == "2019"){
                //     $spid .= "\r\n" . $replacer_spid;
                //     $fullname .= "\r\n" . "Replacer: " . $replacer_fullname. "\r\n" . "Address: $replacer_barname, $replacer_munname";
                //     $remark = "Replacer Birthdate: $replacer_birthdate" . "\r\n" . $remark;
                // }else{
                //     if($value["eligible"] > 1){
                //         $spid .= "\r\n" . $replacer_spid;
                //         $fullname .= "\r\n" . "Replacer: " . $replacer_fullname. "\r\n" . "Address: $replacer_barname, $replacer_munname";
                //         $remark = "Replacer Birthdate: $replacer_birthdate" . "\r\n" . $remark;
                //     }
                // }
			}
			
			$year = $value["year"];
			$period = "";
			if($value["period"] == 1){
				$period = "1ST " . $value["mode_of_payment"];
			}else{
				$period = "2ND " . $value["mode_of_payment"];
			}

			$payment = "UNPAID";
			if($value["liquidation"] == 1){
				$payment = "PAID";
			}else if($value["liquidation"] == 0){
				$payment = "UNPAID";
			}else if($value["liquidation"] == 3){
				$payment = "OFFSET";
			}else if($value["liquidation"] == 4){
				$payment = "ONHOLD";
			}
			
			$activeList[] = array(
				"spid" => $spid,
				"fullname" => $fullname,
				"province" => $prov_name,
				"municipality" => $mun_name,
				"barangay" => $bar_name,
				"amount" => $amount,
				"payment" => $payment,
				"year" => $year,
				"period" =>  strtoupper($period),
			);

        }

		array_multisort(array_column($activeList, 'barangay'), SORT_ASC,array_column($activeList, 'fullname'), SORT_ASC, $activeList);

        if(!empty($activeList)){            
            $provincename = isset($prov_names[$prov_code])?$prov_names[$prov_code] : "";
            $municipalityname = "ALL";
            if($mun_code != "" ){
                $municipalityname = isset($mun_names[$mun_code])?$mun_names[$mun_code] : "";
            }
            
            $object = new Spreadsheet();
            $object->createSheet(0);
            $object->setActiveSheetIndex(0);
            $activeSheet =$object->getActiveSheet();
            $activeSheet->setTitle("All Data");

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
            for($excel_row=1; $excel_row<=2; $excel_row++){ $activeSheet->getStyle('A'.$excel_row)->getFont()->setBold(true); }
            $activeSheet->mergeCells('A1:H1')->setCellValue('A1','PROVINCE OF '.mb_strtoupper($provincename));
            $activeSheet->mergeCells('A2:H2')->setCellValue('A2','MUNICIPALITY OF '.mb_strtoupper($municipalityname));
            $activeSheet->getStyle('A1:H2')->applyFromArray($headerstyle);

            $activeSheet->getColumnDimension('A')->setWidth(6); //no
            $activeSheet->getColumnDimension('B')->setWidth(22); //spid
            $activeSheet->getColumnDimension('C')->setWidth(35); //name
            $activeSheet->getColumnDimension('D')->setWidth(20); //barangay
            $activeSheet->getColumnDimension('E')->setWidth(10); //year
            $activeSheet->getColumnDimension('F')->setWidth(20); //period
            $activeSheet->getColumnDimension('G')->setWidth(15); //amount
            $activeSheet->getColumnDimension('H')->setWidth(20); //payment status
            $activeSheet->getColumnDimension('I')->setWidth(15); //REMARKS
            $activeSheet->getColumnDimension('J')->setWidth(15); //DATE OF PAYMENT
            $activeSheet->getColumnDimension('K')->setWidth(15); //RECEIVER

            $table_columns = array("NO.", "SPID #", "FULLNAME", "BARANGAY", "YEAR", "PERIOD", "AMOUNT", "PAYMENT STATUS","REMARKS","DATE OF PAYMENT","RECEIVER");
            $hs = "A";
            foreach ($table_columns as $tv) { 
                $activeSheet->setCellValue($hs.$excel_row,$tv); $hs++; 
                $activeSheet->getStyle('A'.$excel_row.':K'.$excel_row)->applyFromArray($headerstyleborder);
                $activeSheet->getStyle('A'.$excel_row.':K'.$excel_row)->getFont()->setBold( true );
            }
            $excel_row++;
            $number = 1;
            $total_amount = 0;
            $count_datas = 0;

            if(!empty($activeList)){
                foreach($activeList as $ml){
					$count_datas++;
					$amount = $ml["amount"];
					$fullname = strtoupper($ml["fullname"]);
                    $activeSheet->setCellValue("A".$excel_row , (string)$number);
                    $activeSheet->setCellValue("B".$excel_row , $ml["spid"]);
                    $activeSheet->setCellValue("C".$excel_row , $fullname);
                    $activeSheet->setCellValue("D".$excel_row , $ml["barangay"]);
                    $activeSheet->setCellValue("E".$excel_row , $ml["year"]);
                    $activeSheet->setCellValue("F".$excel_row , $ml["period"]);
                    $activeSheet->setCellValue("G".$excel_row , "₱ ".number_format($amount,2)."\t");
                    $activeSheet->setCellValue("H".$excel_row , $ml["payment"]);

                    $activeSheet->getRowDimension($excel_row)->setRowHeight(16);
                    $activeSheet->getStyle('A'.$excel_row.':B'.$excel_row)->applyFromArray($textcenter);
                    $activeSheet->getStyle('C'.$excel_row)->applyFromArray($textleft);
                    $activeSheet->getStyle('D'.$excel_row.':K'.$excel_row)->applyFromArray($textcenter);
					$activeSheet->getStyle('A'.$excel_row.':K'.$excel_row)->applyFromArray($border);
                        
                    $activeSheet->getStyle('B'.$excel_row.':C'.$excel_row)->getAlignment()->setWrapText(true);
					if(strpos($fullname, "REPLACER") !== false || strpos($fullname, "DECEASED") !== false){
						$activeSheet->getRowDimension($excel_row)->setRowHeight(43);
						$activeSheet->getStyle('C'.$excel_row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('f7a8a8');
						$smallfont = array( 'font'  => array( 'size'  => 10, 'name' => 'Calibri' ));
						$activeSheet->getStyle('C'.$excel_row)->applyFromArray($smallfont);
					}
					
                    $total_amount += $amount;
                    $number++;
                    $excel_row++;
                }
            }

            //file settings
            $activeSheet->getPageSetup()->setPrintArea('A:K');
            $activeSheet->setShowGridlines(true);  

            $filename = "Payment_liquidation_" . $provincename. "_" . $municipalityname . "_(".$count_datas.")_".date("Y-m-d").".xlsx";
            
            $writer = new Xlsx($object);

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'. $filename); 
            header('Cache-Control: max-age=0');

            $writer->save('php://output');

        }else{
            show_404("NO RECORDS FOUND"); 
        }
    }

////////////////////// TEST //////////////////////////////////////////
    
}
