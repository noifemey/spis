<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SP_Crossmatching extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('form');
		$this->load->library('form_validation');		
		$this->form_validation->set_error_delimiters('','');
		$this->load->model("cm_model","cm");
        $this->load->library('csvimport');

	}

	public function index()
	{
		$this->template->title('SAP Beneficiaries');
		$this->template->set_layout('default');
	    $this->template->set_partial('header','partials/header');
	    $this->template->set_partial('sidebar','partials/sidebar');
	    $this->template->set_partial('aside','partials/aside');
	    $this->template->set_partial('footer','partials/footer');
	    $this->template->append_metadata('<script src="' . base_url("assets/js/pages/sp_crossmatching.js?ver=") . filemtime(FCPATH. "assets/js/pages/sp_crossmatching.js") . '"></script>');

	    $this->template->build('cm_view');
	    	
	}	

	public function searchName()
	{
		extract($_POST);
		$data = [];
		$table_name = $agency;	
		$condition = [];	
		
		if(!empty($last_name)){ $condition["lastname"] = $last_name;}
		if(!empty($first_name)){ $condition["firstname"] = $first_name;}
		if(!empty($middle_name)){ $condition["middlename"] = $middle_name;}

		if($agency == "all"){
			$all_tables = array("tblgeneral", "tblwaitinglist");
		}else{
			$all_tables = array($agency);
		}

		foreach($all_tables as $tbls){
			$table_source = "Waitlist";
			$extname = "extname";
			$status = "priority";
			$reference_code = "reference_code";

			if($tbls == "tblgeneral"){
				$extname = "extensionname";
				$status = "sp_status";
				$table_source = "Active";
				$reference_code = "connum";
			}
			$benelist = $this->cm->getAllData($tbls,"lastname, firstname, middlename, $extname,$status,$reference_code",$condition);

			if(!empty($benelist)){
				foreach($benelist as $bene){
					$stat_desc = "";
					$ext = "";
					$spid = "";
					if($tbls == "tblgeneral"){
						$stat_desc = $bene->sp_status;
						$ext = $bene->extensionname;
						$spid = $bene->connum;
					}else{
						$spid = $bene->reference_code;
						$ext = $bene->extname;
						if($bene->priority == 1){
							$stat_desc = "Eligible Waitlist";
						}else if($bene->priority == 0){
							$stat_desc = "Waiting for Eligibility";
						}else if($bene->priority == 2){
							$stat_desc = "Inelligible (For Revalidation)";
						}
					}

					$dup_fullname = strtoupper($bene->lastname . ", " .  $bene->firstname . " ". $bene->middlename . " " . $ext);
					$search_fullname = strtoupper($last_name . ", " .  $first_name . " ". $middle_name);

					$data[] = array(
						"search_fullname" => $search_fullname,
						"dup_fullname" => $dup_fullname,
						"last_name" => $bene->lastname,
						"first_name" => $bene->firstname,
						"middle_name" => $bene->middlename,
						"ext_name" => $ext,
						"table_source" => $table_source,
						"status" => $stat_desc,
						"spid" => $spid
					);
				}
			}
		}		
		
        $response = array(
            'success' => true,
			'data' => $data
        );
		response_json($response);	
	}

	public function checkProbableDuplicate()
	{

		extract($_POST);

		$tblgeneral_data = $this->Main->select([
			'select' => 'firstname, middlename, lastname,extensionname, sp_status, barangay, city, province, connum',
			'type'	 => 'result_array',
			'table'	 => 'tblgeneral'
		]);

		$waitlist_data = $this->Main->select([
			'select' 	=> 'firstname, middlename, lastname, priority, bar_code, mun_code, prov_code, reference_code,extname',
			'type'   	=> 'result_array',
			'table'     => 'tblwaitinglist',
			'condition' => ['archived' => 0]
		]);
		
		$search_fullname = strtoupper($last_name . ", " .  $first_name . " ". $middle_name);
		$search_key = strtolower($last_name . ', ' . $first_name . ' ' . $middle_name);

		$data = [];

		foreach ($tblgeneral_data as $key => $value) {

			$dup_fullname = $value['lastname'] . ', ' . $value['firstname'] . ' ' . $value['middlename'];

			if ($middle_name == "") {
				$fullname = $value['lastname'] . ', ' . $value['firstname'];
			} else {
				$fullname = $value['lastname'] . ', ' . $value['firstname'] . ' ' . $value['middlename'];
			}

			similar_text($search_key, strtolower($fullname), $percentage);
			if ($percentage > 85) {
				$data[] = array(
					"search_fullname" => $search_fullname,
					"dup_fullname" => strtoupper($dup_fullname),
					"last_name" => $value['lastname'] ,
					"first_name" => $value['firstname'],
					"middle_name" => $value['middlename'],
					"ext_name" => $value['extensionname'],
					"table_source" => "Active",
					"status" => $value['sp_status'],
					"spid" => $value['connum'],
					"percentage" =>round($percentage,2) . "%"
				);
			}
		}

		foreach ($waitlist_data as $key => $value) {

			$dup_fullname = $value['lastname'] . ', ' . $value['firstname'] . ' ' . $value['middlename'];

			if ($middle_name == "") {
				$fullname = $value['lastname'] . ', ' . $value['firstname'];
			} else {
				$fullname = $value['lastname'] . ', ' . $value['firstname'] . ' ' . $value['middlename'];
			}

			similar_text($search_key, strtolower($fullname), $percentage);
			if ($percentage > 85) {

				$stat_desc = "";
				if($value['priority'] == 1){
					$stat_desc = "Eligible Waitlist";
				}else if($value['priority'] == 0){
					$stat_desc = "Waiting for Eligibility";
				}else if($value['priority'] == 2){
					$stat_desc = "Inelligible (For Revalidation)";
				}

				$data[] = array(
					"search_fullname" => $search_fullname,
					"dup_fullname" => strtoupper($dup_fullname),
					"last_name" => $value['lastname'] ,
					"first_name" => $value['firstname'],
					"middle_name" => $value['middlename'],
					"ext_name" => $value['extname'],
					"table_source" => "Waitinglist",
					"status" => $stat_desc,
					"spid" => $value['reference_code'],
					"percentage" => round($percentage,2) . "%"
				);
			}
		}

        $response = array(
            'success' => true,
			'data' => $data
        );
		response_json($response);	
	}

	public function uploadSP(){

		date_default_timezone_set("Asia/Manila");
		$curdate = date('Y-m-d H:i:s');

		// ini_set('max_execution_time', '60000');
		ini_set('max_execution_time', '0');
		// ini_set('memory_limit', '999M'); 
		ini_set('memory_limit', '-1'); 

		$config['upload_path']= "uploads/files/csv";
		$config['allowed_types']= "csv";
		
		if(!is_dir($config['upload_path'])){ 
			mkdir($config['upload_path'], 0777, TRUE);
		}

		$response=false;
		try{

			$this->load->library('upload', $config);

			if(!$this->upload->do_upload('file')){
				$error = array('error' => $this->upload->display_errors());
				$response['success'] = FALSE;
				$response['data'] =  array();
				return $response;
			}else{
				
				$tblgeneral_data = $this->Main->select([
					'select' => 'firstname, middlename, lastname,extensionname, sp_status, barangay, city, province, connum',
					'type'	 => 'result_array',
					'table'	 => 'tblgeneral'
				]);
		
				$waitlist_data = $this->Main->select([
					'select' 	=> 'firstname, middlename, lastname, priority, bar_code, mun_code, prov_code, reference_code,extname',
					'type'   	=> 'result_array',
					'table'     => 'tblwaitinglist',
					'condition' => ['archived' => 0]
				]);

				//Get File Upload
				$path_folder = '/uploads/files/csv';
				$file_name = str_replace(" ", "_",$_FILES['file']['name']);
				$file_path = server_path.$path_folder."/".$file_name;
				$file_data = $this->csvimport->get_array($file_path,FALSE,FALSE,0);
				
				$content = "last_name,first_name,middle_name,ext_name,duplicate, \r\n";
				
				$data = [];
				foreach($file_data as $row) {
					$last_name			= $row["last_name"];
					$first_name			= $row["first_name"];
					$middle_name		= $row["middle_name"];
					$ext_name			= $row["ext_name"];
					$condition = [];
					
					$search_fullname = strtoupper($last_name . ", " .  $first_name . " ". $middle_name . " ". $ext_name);
					$dup_fullname = "NONE";
					//$content = [];
					$end = " \r\n";
					$dup = "";
					$content .= "$last_name,$first_name,$middle_name,$ext_name, ";

					if(!empty($last_name)){
						$condition["lastname"] = $last_name;
					}
					if(!empty($first_name)){
						$condition["firstname"] = $first_name;
					}
					if(!empty($middle_name)){
						$condition["middlename"] = $middle_name;
					}

					foreach ($tblgeneral_data as $key => $value) {

						$dup_fullname = $value['lastname'] . ', ' . $value['firstname'] . ' ' . $value['middlename'];
			
						if ($middle_name == "") {
							$fullname = $value['lastname'] . ', ' . $value['firstname'];
						} else {
							$fullname = $value['lastname'] . ', ' . $value['firstname'] . ' ' . $value['middlename'];
						}
			
						similar_text($search_fullname, strtoupper($fullname), $percentage);
						if ($percentage > 85) {
							$data[] = array(
								"search_fullname" => $search_fullname,
								"dup_fullname" => strtoupper($dup_fullname),
								"last_name" => $value['lastname'] ,
								"first_name" => $value['firstname'],
								"middle_name" => $value['middlename'],
								"ext_name" => $value['extensionname'],
								"table_source" => "Active",
								"status" => $value['sp_status'],
								"spid" => $value['connum'],
								"percentage" =>round($percentage,2) . "%"
							);

							$spid = $value['connum'];
							$stat_desc = $value['sp_status'];

							$fullname = "(" . $value['lastname'] . " " . $value['firstname'] . " " . $value['middlename'] . " | Current Beneficiary | $spid | $stat_desc ) ,";
							$dup .= $fullname;
						}
					}
					
					foreach ($waitlist_data as $key => $value) {
						$dup_fullname = $value['lastname'] . ', ' . $value['firstname'] . ' ' . $value['middlename'];
						if ($middle_name == "") {
							$fullname = $value['lastname'] . ', ' . $value['firstname'];
						} else {
							$fullname = $value['lastname'] . ', ' . $value['firstname'] . ' ' . $value['middlename'];
						}
						similar_text($search_fullname, strtoupper($fullname), $percentage);
						if ($percentage > 85) {
							$stat_desc = "";
							if($value['priority'] == 1){$stat_desc = "Eligible Waitlist";}
							else if($value['priority'] == 0){$stat_desc = "Waiting for Eligibility";}
							else if($value['priority'] == 2){$stat_desc = "Inelligible (For Revalidation)";}

							$data[] = array(
								"search_fullname" => $search_fullname,
								"dup_fullname" => strtoupper($dup_fullname),
								"last_name" => $value['lastname'] ,
								"first_name" => $value['firstname'],
								"middle_name" => $value['middlename'],
								"ext_name" => $value['extname'],
								"table_source" => "Waitinglist",
								"status" => $stat_desc,
								"spid" => $value['reference_code'],
								"percentage" => round($percentage,2) . "%"
							);
							
							$spid = $value['reference_code'];
							$fullname = "(" . $value['lastname'] . " " . $value['firstname'] . " " . $value['middlename'] . " | Waiting List | $spid | $stat_desc ) ,";
							$dup .= $fullname;
						}
					}

					//	$all_tables = array("tblgeneral", "tblwaitinglist");

					// foreach($all_tables as $tbls){
						// 	$table_source = "Waitlist";
						// 	$extname = "extname";
						// 	$status = "priority";
						// 	$reference_code = "reference_code";
				
						// 	if($tbls == "tblgeneral"){
						// 		$extname = "extensionname";
						// 		$status = "sp_status";
						// 		$table_source = "Active";
						// 		$reference_code = "connum";
						// 	}

						// 	$benelist = $this->cm->getAllData($tbls,"lastname, firstname, middlename,$extname,$reference_code,$status",$condition);

						// 	if(!empty($benelist)){
						// 		foreach($benelist as $bene){
						// 			$stat_desc = "";
						// 			$ext = "";
						// 			$spid = "";
						// 			if($tbls == "tblgeneral"){
						// 				$stat_desc = $bene->sp_status;
						// 				$ext = $bene->extensionname;
						// 				$spid = $bene->connum;
						// 			}else{
						// 				$spid = $bene->reference_code;
						// 				$ext = $bene->extname;
						// 				if($bene->priority == 1){
						// 					$stat_desc = "Eligible Waitlist";
						// 				}else if($bene->priority == 0){
						// 					$stat_desc = "Waiting for Eligibility";
						// 				}else if($bene->priority == 2){
						// 					$stat_desc = "Inelligible (For Revalidation)";
						// 				}
						// 			}
									
						// 			$dup_fullname = strtoupper($bene->lastname . ", " .  $bene->firstname . " ". $bene->middlename);
									
						// 			$single_dt = array(
						// 				"search_fullname" => $search_fullname,
						// 				"dup_fullname" => $dup_fullname,
						// 				"last_name" => $bene->lastname,
						// 				"first_name" => $bene->firstname,
						// 				"middle_name" => $bene->middlename,
						// 				"ext_name" => $ext,
						// 				"table_source" => $table_source,
						// 				"status" => $stat_desc,
						// 				"spid" => $spid,
						// 				"percentage" => "100%"
						// 			);

						// 			$data[] = $single_dt;

						// 			$fullname = "(" . $bene->lastname . " " . $bene->firstname . " " . $bene->middlename . " | $table_source | $spid | $stat_desc ) ,";
						// 			$dup .= $fullname;
						// 		}
						// 	}
					// }	
					if(!empty($dup)){ $content .= $dup; }
					else { $content .= "NONE"; }
					$content .= "  \r\n";

				}
			}

		}catch(Exception $e){
			$response['success']=FALSE;
			$response['data'] = $data;
		}

		$dt = date("Y-m-d h-i-sa");
		$fname = str_replace(".csv", "",$_FILES['file']['name']);
		$filepath = "downloads/" . $fname . "_duplicates_($dt).csv";

		file_put_contents($filepath , $content);

		//confirm
		$response['success']=TRUE;
		$response['data'] = $data;
		$response['content'] = $filepath;

		unlink('uploads/files/csv/'.str_replace(" ", "_",$_FILES['file']['name']));
		response_json($response);
	}


	public function exportDuplicate(){

		$content = "last_name,first_name,middle_name,ext_name,duplicate \r\n";
		$list = $this->input->get('content');
		$list_data = str_replace("@","\r\n",$list);

		$content .= $list_data;
		
		$dt = date("Y-m-d");
		$filepath = "downloads/sap_duplicates_($dt).csv";

		file_put_contents($filepath , $content);

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
		} else {
			http_response_code(404);
		}
	}

	public function uploadUCT(){

		date_default_timezone_set("Asia/Manila");
		$curdate = date('Y-m-d H:i:s');

		ini_set('max_execution_time', '60000');
		ini_set('memory_limit', '-1'); 

		$config['upload_path']= "uploads/files/csv";
		$config['allowed_types']= "csv";
		
		if(!is_dir($config['upload_path'])){ 
			mkdir($config['upload_path'], 0777, TRUE);
		}
		$response=false;
		try{

			$this->load->library('upload', $config);

			if(!$this->upload->do_upload('file')){
				$error = array('error' => $this->upload->display_errors());
				$response['success'] = FALSE;
				$response['data'] =  array();
				return $response;
			}else{
				
				$tblgeneral_data = $this->Main->select([
					'select' => 'first_name, middle_name, last_name,permanent_barangay, permanent_city, permanent_province, birthday',
					'type'	 => 'result_array',
					'table'	 => 'tbl_uct'
				]);

				//Get File Upload
				$path_folder = '/uploads/files/csv';
				$file_name = str_replace(" ", "_",$_FILES['file']['name']);
				$file_path = server_path.$path_folder."/".$file_name;
				$file_data = $this->csvimport->get_array($file_path,FALSE,FALSE,0);
				$content = "branch,last_name,first_name,middle_name,ext_name,duplicate, \r\n";
				
				$data = [];
				foreach($file_data as $row) {
					$branch			    = $row["branch"];
					$last_name			= $row["last_name"];
					$first_name			= $row["first_name"];
					$middle_name		= $row["middle_name"];
					$ext_name		    = $row["ext_name"];
					$condition = [];
					
					$search_fullname = strtoupper($last_name . ", " .  $first_name . " ". $middle_name);
					$dup_fullname = "NONE";
					//$content = [];
					$end = " \r\n";
					$dup = "";
					$content .= "$branch,$last_name,$first_name,$middle_name,$ext_name,";

					foreach ($tblgeneral_data as $key => $value) {

						$dup_fullname = $value['last_name'] . ', ' . $value['first_name'] . ' ' . $value['middle_name'];
			
						if ($middle_name == "") {
							$fullname = $value['last_name'] . ', ' . $value['first_name'];
						} else {
							$fullname = $value['last_name'] . ', ' . $value['first_name'] . ' ' . $value['middle_name'];
						}
			
						similar_text($search_fullname, strtoupper($fullname), $percentage);

						if ($percentage > 85) {
							$address = $value['permanent_barangay'] . " " . $value['permanent_city']. " " . $value['permanent_province'];
							$birthday = $value['birthday'];
							$percentage = round($percentage, 2). "%";
							$fullname = "($percentage | " . $value['last_name'] . " " . $value['first_name'] . " " . $value['middle_name'] . " | $birthday | $address ) ,";
							$dup .= $fullname;
						}
					}

					if(!empty($dup)){ $content .= $dup; }
					else { $content .= "NONE"; }
					$content .= "  \r\n";

				}
			}

		}catch(Exception $e){
			$response['success']=FALSE;
			$response['data'] = $data;
		}

		$dt = date("Y-m-d h-i-sa");
		$fname = str_replace(".csv", "",$_FILES['file']['name']);
		$filepath = "downloads/" . $fname . "_duplicates_($dt).csv";

		file_put_contents($filepath , $content);

		//confirm
		$response['success']=TRUE;
		$response['data'] = $data;
		$response['content'] = $filepath;

		unlink('uploads/files/csv/'.str_replace(" ", "_",$_FILES['file']['name']));
		response_json($response);
	}

	public function crossmatchUCT(){

		date_default_timezone_set("Asia/Manila");
		$curdate = date('Y-m-d H:i:s');

		ini_set('max_execution_time', '60000');
		ini_set('memory_limit', '-1'); 

		$config['upload_path']= "uploads/files/csv";
		$config['allowed_types']= "csv";
		
		if(!is_dir($config['upload_path'])){ 
			mkdir($config['upload_path'], 0777, TRUE);
		}
		$response=false;
		try{

			$this->load->library('upload', $config);

			if(!$this->upload->do_upload('file')){
				$error = array('error' => $this->upload->display_errors());
				$response['success'] = FALSE;
				$response['data'] =  array();
				return $response;
			}else{
				$barList = $this->Main->getBarangays();
        		$bar_names =  array_column($barList, 'bar_name', 'bar_code');

        		$provinces = $this->Main->get_all_provinces();
		        $prov_names = array_column($provinces, 'prov_name','prov_code');

		        $municipalities = $this->Main->get_all_municipalities();
		        $mun_names = array_column($municipalities, 'mun_name','mun_code');

				$tblgeneral_data = $this->Main->select([
					'select' => 'firstname, middlename, lastname,extensionname,connum, birthdate,  inactive_reason_id, sp_inactive_remarks, sp_status_inactive_date, barangay,city,province',
					'type'	 => 'result_array',
					'table'	 => 'tblgeneral',
					'condition' => ['sp_status' => 'Inactive']
				]);
				$tbl_inactive_reasons = $this->Main->select([
					'select' => '*',
					'type'	 => 'result_array',
					'table'	 => 'tblinactivereason',
				]);
				$inactive_reasons = array_column($tbl_inactive_reasons, 'name' , 'id');

				//Get File Upload
				$path_folder = '/uploads/files/csv';
				$file_name = str_replace(" ", "_",$_FILES['file']['name']);
				$file_path = server_path.$path_folder."/".$file_name;
				$file_data = $this->csvimport->get_array($file_path,FALSE,FALSE,0);
				$content = "card_number,name,duplicate, \r\n";
				
				$data = [];
				foreach($file_data as $row) {
					$card_number		= $row["card_number"];
					$fullname			= $row["name"];
					$condition = [];
					
					$search_fullname = strtoupper($fullname);
					$dup_fullname = "NONE";
					//$content = [];
					$end = " \r\n";
					$dup = "";
					$content .= "$card_number,$fullname,";

					foreach ($tblgeneral_data as $key => $value) {
						$fullname = $value['firstname'] . ' ' . $value['middlename'] . ' ' . $value['lastname'] . ' ' . $value['extensionname'];
						
						if ($value['middlename'] == "" && $value['extensionname'] == "") {
							$fullname = $value['firstname'] . ' ' . $value['lastname'];
						}

						if ($value['middlename'] == "") {
							$fullname = $value['firstname'] . ' ' . $value['lastname']  . ' ' . $value['extensionname'];
						}
						if ($value['extensionname'] == "") {
							$fullname = $value['firstname'] . ' ' . $value['middlename'] . ' ' . $value['lastname'];
						}
			
						similar_text($search_fullname, strtoupper($fullname), $percentage);

						if ($percentage > 85) {
							$birthday = $value['birthdate'];
							$spid = $value['connum'];
							$percentage = round($percentage, 2). "%";

							$reason = (isset($inactive_reasons[$value['inactive_reason_id']])) ? $inactive_reasons[$value['inactive_reason_id']] : '';
							$sp_status_inactive_date = ($value['sp_status_inactive_date'] != "") ? ' - ' . $value['sp_status_inactive_date'] : '';
							$sp_inactive_remarks = ($value['sp_inactive_remarks'] != "") ? ' - ' . $value['sp_inactive_remarks'] : '';
							$inactive = $reason . $sp_inactive_remarks . $sp_status_inactive_date;

							$bar_name = isset($bar_names[$value['barangay']]) ? $bar_names[$value['barangay']] : "";
			                $mun_name = isset($mun_names[$value['city']]) ? $mun_names[$value['city']] : "";
			                $prov_name = isset($prov_names[$value['province']]) ? $prov_names[$value['province']] : "";

			                $address = $bar_name . " - " . $mun_name . " - " . $prov_name;

							$fullname = "($percentage | " . $fullname . " | $birthday | $spid | $inactive | $address) ,";
							$dup .= $fullname;
						}
					}

					if(!empty($dup)){ $content .= $dup; }
					else { $content .= "NONE"; }
					$content .= "  \r\n";

				}
			}
		}catch(Exception $e){
			$response['success']=FALSE;
			$response['data'] = $data;
		}

		$dt = date("Y-m-d h-i-sa");
		$fname = str_replace(".csv", "",$_FILES['file']['name']);
		$filepath = "downloads/" . $fname . "_duplicates_($dt).csv";

		file_put_contents($filepath , $content);

		//confirm
		$response['success']=TRUE;
		$response['data'] = $data;
		$response['content'] = $filepath;

		unlink('uploads/files/csv/'.str_replace(" ", "_",$_FILES['file']['name']));
		response_json($response);
	}


}

/* End of file Dashboard.php */
/* Location: ./application/modules/admin/controllers/Dashboard.php */