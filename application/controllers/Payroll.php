<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Payroll extends CI_Controller {
	private $pager_settings;
	public function __construct() {
		parent::__construct();

		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('','');
        $this->load->model('Main', 'Main');
		$this->load->model("Payroll_model","pm");
		$this->load->model("Replacement_model","rm");
		
        $this->load->library('csvimport');
		$this->load->library('PHPExcel');
		
		checkLogin();
	}

	public function index()
	{
        $data['app_active'] = true;

		//if(isset($this->session->userdata['logged_in']) && $this->session->userdata['logged_in']){

		$this->template->title('Social Pension Generation of Payroll');
		$this->template->set_layout('default');
	    $this->template->set_partial('header','partials/header');
	    $this->template->set_partial('sidebar','partials/sidebar');
	    $this->template->set_partial('aside','partials/aside');
	    $this->template->set_partial('footer','partials/footer');
	    $this->template->append_metadata('<script src="' . base_url("assets/js/pages/payroll/payroll.js?ver=") . filemtime(FCPATH. "assets/js/pages/payroll/payroll.js") . '"></script>');

	    $this->template->build('payroll/payroll_view',$data);
	
		//}
	    // else
	    // {
	    //   redirect (base_url().'404_override');
	    // }	
	}
    
// START - GENERATE CASH ASSISTANCE PAYROLL
    //Export CAP
    public function capGenerate(){
        $amount = $this->input->get('amount');
        $prov_code = $this->input->get('prov_code');
        $mun_code = $this->input->get('mun_code');
        $year = $this->input->get('year');
        $payment_status = $this->input->get('liquidation');
        $ce_type = $this->input->get('type');
        $qtrsem = 1;
        $modeofpayment = "";
        $period_name = $year;
        $unpaid = $this->input->get('unpaid');
        $additional = 0;

        $period = $this->input->get('period');
        if(in_array($period, [5,6])){
            $amount = 3000;
            $modeofpayment = "Semester";
            $qtrsem = ($period == 5)?1:2;
            if($qtrsem == 1){ $period_name = "1stSemester_" . $year; }
            else if($qtrsem == 2){ $period_name = "2ndSemester_" . $year;}
        }else{
            $amount = 1500;
            $qtrsem = $period;
            $modeofpayment = "Quarter";
            if($qtrsem == 1){ $period_name = "1stQuarter_" . $year; }
            else if($qtrsem == 2){ $period_name = "2ndQuarter_" . $year;}
            else if($qtrsem == 3){ $period_name = "3rdQuarter_" . $year;}
            else if($qtrsem == 4){ $period_name = "4thQuarter_" . $year;}
        }
        
        $generalCon = [];
        if($prov_code != ""){ $generalCon["province"] = $prov_code;}
        if($mun_code != ""){ $generalCon["city"] = $mun_code; }

        if($ce_type != "all"){
            if($ce_type == "0"){ 
                $generalCon["additional <>"] = $year;
            }else{ 
                $generalCon["additional"] = $year; 
                if($ce_type != "3"){
                    $generalCon["batch_no"] = $ce_type;
                }
            }
        }

        $select = "connum,lastname,firstname,middlename,extensionname, sp_status,barangay,city,province, inactive_reason_id, sp_inactive_remarks,replacer,birthdate,year_start,quarter_start";
        
        $tp = "ALL";
        if($unpaid == "true"){
            $tp = "UNPAID";
        }

        //Check if payroll exist
        $condi = array(
            "year"			=> $year,
            "prov_code"		=> $prov_code,
            "mun_code" 	    => $mun_code,
            "period" 	    => $modeofpayment,
            "period_no" 	=> $qtrsem,
            "type" 	        => $tp,
        );
        if($ce_type == "all"){
            $condi["additional"] = "5";
            $additional = 5;
        }else{
            $condi["additional"] = $ce_type;
            $additional = $ce_type;
        }
        $generated_payroll = $this->Main->select([
            'select'    => '*',
            'type'      => '',
            'table'     => 'tblgeneratedpayroll',
            'condition' => $condi,
            'order'     => []
        ]);
        
        //$generated_payroll = $this->Main->count("tblgeneratedpayroll",$condi); //66

        $exporlist = [];
        $gen_payroll_id = "";

        //Get Generated Payroll
        if(count($generated_payroll) < 1){
            $generalCon["sp_status<>"] = "Inactive";
            $exporlist = $this->pm->get_all_general($select,$generalCon);
            if(!empty($exporlist)){
                //Insert to tblgeneratedpayroll
                
                $generated_payroll = array(
                    "year"			=> $year,
                    "prov_code"		=> $prov_code,
                    "mun_code" 	    => $mun_code,
                    "period" 	    => $modeofpayment,
                    "period_no" 	=> $qtrsem,
                    "count" 	    => count($exporlist),
                    "type" 	        => $tp,
                    "uid" 	        => sesdata('id'),
                    'additional'    => $additional
                );
                $this->db->set($generated_payroll);
                $this->db->insert('tblgeneratedpayroll');
                $gen_payroll_id = $this->db->insert_id();
            }
        }else{
            $gen_payroll = $generated_payroll[0];
            $gen_payroll_id =  $gen_payroll->id;
        }

        //Check if unpaidlist already exist
        $unpaidCon = array(
            "prov_code" => $prov_code,
            "mun_code" => $mun_code,
            "year" => $year,
            "mode_of_payment" => $modeofpayment,
            "period" => $qtrsem,
        );
        if($ce_type != "all"){
            if($ce_type == "3"){
                $unpaidCon["additional"] = [1,2];
                $additional = 0;
            }else{
                $unpaidCon["additional"] = $ce_type;
                $additional = $ce_type;
            }
        }else{
            $additional = 0;
        }
        $unpaid_cnt = $this->Main->count("tblpayroll",$unpaidCon);

        if($unpaid_cnt < 1){
            //if(count($generated_payroll) < 1){
            $data = [];
            if(empty($exporlist)){
                $generalCon["sp_status<>"] = "Inactive";
                $exporlist = $this->pm->get_all_general($select,$generalCon);
            }

            //Save to tblpayroll
            foreach ($exporlist as $key => $mb) {
                $reciever = $mb["lastname"] . ", " . $mb["firstname"] . " " . $mb["middlename"] . " " . $mb["extensionname"];
                $date_recieve = date("Y-m-d");

                $data[] = array(
                    'spid' => $mb["connum"],
                    'prov_code' => $prov_code,
                    'mun_code' => $mun_code,
                    'bar_code' => $mb["barangay"],
                    'year' => $year,
                    'mode_of_payment' => $modeofpayment,
                    'period' => $qtrsem,
                    'amount' => $amount,
                    'receiver' => $reciever,
                    'date_receive' => $date_recieve,
                    'remarks' => $mb["sp_status"],
                    'liquidation' => '0',
                    'generated_id' => $gen_payroll_id,
                    'additional' => $additional
                 );
            }

            if(!empty($data)){
                $this->db->insert_batch('tblpayroll', $data);
            }
        }else{
            unset($generalCon["sp_status<>"]);
            $exporlist = $this->pm->get_all_general($select,$generalCon);

            if($unpaid == "true"){
                $unpaidCon["liquidation"] = 0;
            }
            $unpaidCon["liquidation <>"] = 2;

            $unpaidList = $this->pm->get_payroll($unpaidCon);

            $this->db->set('count', count($unpaidList));
            $this->db->where($condi);
            $this->db->update('tblgeneratedpayroll'); // gives UPDATE `mytable` SET `field` = 'field+1' WHERE `id` = 2
		    // response_json($unpaidList);
            //get spids from generated table
        }

        $activeList = [];
        $forrepList = [];
        $activeCount = 0;
        $forrepCount = 0;

        $provinces = $this->Main->get_all_provinces();
        $prov_names = array_column($provinces, 'prov_name','prov_code');

        $municipalities = $this->Main->get_all_municipalities(["prov_code" => $prov_code]);
        $mun_names = array_column($municipalities, 'mun_name','mun_code');

        $bar_con["prov_code"] =$prov_code;
        if($mun_code != ""){ $bar_con["mun_code"] = $mun_code;}
        $barList = $this->Main->getBarangays($bar_con,0,['col'=>'bar_name','order_by' => 'ASC']);
        $bar_names =  array_column($barList, 'bar_name', 'bar_code');

        if(!empty($exporlist)){  
            $spids = [];
            if(!empty($unpaidList)){ $spids = array_column($unpaidList, 'spid'); }

            $reasonforrep = $this->Main->getreasonforrep();
            $reason_names =  array_column($reasonforrep, 'name', 'id');
            
            $replacements = $this->pm->get_all_replacement();
            $rep_list =  array_column($replacements, 'replacer', 'replacee');
            
            $fullnameList = [];
            foreach($exporlist as $key => $value) {
                $fullnameList[$value['connum']]= $value['lastname'].", ".$value['firstname']. " " . $value['middlename']. " " . $value['extensionname'];          
            } 
            $reasons = array_column($exporlist, 'sp_inactive_remarks', 'connum');
            $birthdates = array_column($exporlist, 'birthdate', 'connum');
            $barcodes = array_column($exporlist, 'barangay', 'connum');
            $muncodes = array_column($exporlist, 'city', 'connum');

            foreach($exporlist as $key => $value){

                if(!empty($spids) && !in_array($value['connum'], $spids)){
                    continue;
                }

                $sp_status = $value['sp_status'];
                $fullname = strtoupper($value["lastname"]) . ", " .  strtoupper($value["firstname"]) . " " . strtoupper($value["middlename"]) . " " . strtoupper($value["extensionname"]);

                $bar_name = isset($bar_names[$value['barangay']]) ? $bar_names[$value['barangay']] : "";
                $prov_name = isset($prov_names[$value['province']]) ? $prov_names[$value['province']] : "";
                $mun_name = isset($mun_names[$value['city']]) ? $mun_names[$value['city']] : "";

                if( strtoupper($sp_status) == "FORREPLACEMENT" || strtoupper($sp_status) == "INACTIVE"){
                    $reasonforrep="No reason set."; 
                    if(!empty($reason_names[$value['inactive_reason_id']])){
                        $reasonforrep = isset($reason_names[$value['inactive_reason_id']]) ? $reason_names[$value['inactive_reason_id']] : "";
                        if( strtoupper($reasonforrep)=="DECEASED"){
                            if(!empty($value["sp_inactive_remarks"])){
                                $reasonforrep = $reasonforrep." (".date_format(new DateTime($value["sp_inactive_remarks"]),"Y-m-d").")";
                            }
                        }else{
                            if(!empty($ml->sp_inactive_remarks)){
                                $reasonforrep = $reasonforrep." (".$value["sp_inactive_remarks"].")";
                            }
                        }
                    }
                    $replacer_spid = "";
                    $replacer_fullname  = "";
                    $replacer_munname   = "";
                    $replacer_barname   = "";
                    $replacer_birthdate = "";

                    if(strtoupper($sp_status) == "INACTIVE"){
                        $replacer_spid      = isset($rep_list[$value['connum']]) ? $rep_list[$value['connum']] : "";
                        $replacer_fullname  = isset($fullnameList[$replacer_spid]) ? $fullnameList[$replacer_spid] : "";
                        $replacer_birthdate = isset($birthdates[$replacer_spid]) ? $birthdates[$replacer_spid] : "";
                        $replacer_muncode   = isset($muncodes[$replacer_spid]) ? $muncodes[$replacer_spid] : "";
                        $replacer_barcode   = isset($barcodes[$replacer_spid]) ? $barcodes[$replacer_spid] : "";
        
                        $replacer_munname   = isset($mun_names[$replacer_muncode])?$mun_names[$replacer_muncode] : "";
                        $replacer_barname   = isset($bar_names[$replacer_barcode])?$bar_names[$replacer_barcode] : "";
                    }

                    $forrepCount += 1;
                    $forrepList[$value['barangay']][] = array(
                        "spid" => $value["connum"],
                        "fullname" => $fullname  . "*",
                        "province" => $prov_name,
                        "municipality" => $mun_name,
                        "barangay" => $bar_name,
                        "amount" => $amount,
                        "remarks" => $reasonforrep,
                        "replacer_fullname" => $replacer_fullname,
                        "replacer_munname" => $replacer_munname,
                        "replacer_barname" => $replacer_barname,
                        "replacer_spid"    => $replacer_spid,
                        "replacer_birthdate"    => $replacer_birthdate,
                    );
                }else{
                    // if($value['replacer']==1 && $value['year_start']=='2021' && $value['quarter_start']=='2'){
                    // if($value['replacer']==1 && $value['year_start']==$year && $value['quarter_start']==$qtrsem){
                    if($value['replacer']==1 && $value['year_start']==$year){
                        $fullname .= "(NEW)";
                    }
                    $activeCount += 1;
                    $activeList[$value['barangay']][] = array(
                        "spid" => $value["connum"],
                        "fullname" => $fullname,
                        "province" => $prov_name,
                        "municipality" => $mun_name,
                        "barangay" => $bar_name,
                        "amount" => $amount,
                        "birthdate" => $value['birthdate'],
                    );
                }
            }
            // //Arrange Active List
            foreach ($activeList as $key => $value) {
                array_multisort(array_column($activeList[$key], 'barangay'), SORT_ASC, array_column($activeList[$key], 'fullname'), SORT_ASC, $activeList[$key]);
            }
            //Arrange Forrep List
            foreach ($forrepList as $key => $value) {
                array_multisort(array_column($forrepList[$key], 'barangay'), SORT_ASC, array_column($forrepList[$key], 'fullname'), SORT_ASC, $forrepList[$key]);
            }
        }

        $prov_name = isset($prov_names[$prov_code]) ? $prov_names[$prov_code] : "";
        $mun_name = isset($mun_names[$mun_code]) ? $mun_names[$mun_code] : "";

        $fileTitle = "CAP_" . $mun_name;

        //print_r($activeList);

        $this->exportPayroll($fileTitle, $prov_name,$mun_name, $year,$modeofpayment,$qtrsem, $bar_names, $activeList, $forrepList,$activeCount,$forrepCount);

        // //export
        // if ($municipality_select!="141102000") {
                // //Arrange Active List
                // if(!empty($activeList)){
                //     array_multisort(array_column($activeList, 'barangay'), SORT_ASC, array_column($activeList, 'fullname'), SORT_ASC, $activeList);
                // }
                // //Arrange Forrep List
                // if(!empty($forrepList)){
                //     array_multisort(array_column($forrepList, 'barangay'), SORT_ASC, array_column($forrepList, 'fullname'), SORT_ASC, $forrepList);
                // }
        //     $this->exportPayroll($municipality_select,$input_year,$modeofpayment,$input_qtrsem,$spstatus,"Cap");
        // }
        // else if($municipality_select=="141102000"){
            // arrange by alphabets
        //     $this->exportPayrollBaguio($municipality_select,$input_year,$modeofpayment,$input_qtrsem,$spstatus,"Cap");
        // }
    }

    public function exportPayroll($fileTitle, $provname,$munname, $year,$modepayment,$qtrsem, $barlist, $activelist, $forreplist,$activeCount,$forrepCount,$cap_type = "active"){
        ini_set('memory_limit', '20000M');
        set_time_limit(0);
        // ini_set('max_execution_time', 3000);
        ignore_user_abort(true);

        $signatories = getSignatories("sign1_name, sign1_position, sign2_name, sign2_position",array('file'=>"CAP"),"","row");

        if(strtoupper($modepayment)=="QUARTER"){
            $amount = 1500;
            if($qtrsem==1){ $headermonth = "January to March"; $generatesemqtr="1st quarter"; }
            else if($qtrsem==2){ $headermonth = "April to June"; $generatesemqtr="2nd quarter"; }
            else if($qtrsem==3){ $headermonth = "July to September"; $generatesemqtr="3rd quarter"; }
            else if($qtrsem==4){ $headermonth = "October to December"; $generatesemqtr="4th quarter"; }
        }else if(strtoupper($modepayment)=="SEMESTER"){
            $amount = 3000;
            if($qtrsem==1){ $headermonth = "January to June"; $generatesemqtr="1st semester"; }
            else if($qtrsem==2){ $headermonth = "July to December"; $generatesemqtr="2nd semester"; }
        }else{
            $amount = 6000; 
            $generatesemqtr="1st semester and 2nd semester";
            $headermonth = "January to December";
        }

        $cap_title = "CASH ASSISTANCE PAYROLL";
        if($cap_type != "active"){
            $cap_title = "UNPAID CASH ASSISTANCE PAYROLL";
        }

        $footerstyle = array(
            'font'  => array('size'  => 9, 'name' => 'Calibri')
        );

        date_default_timezone_set("Asia/Manila");

        $object = new Spreadsheet();
        $object->createSheet(0);
        $object->setActiveSheetIndex(0);
        $activeSheet =$object->getActiveSheet();
        $activeSheet->setTitle($cap_title);

        $activeSheet->getColumnDimension('A')->setWidth(4.71);
        $activeSheet->getColumnDimension('B')->setWidth(29.85);
        $activeSheet->getColumnDimension('C')->setWidth(13.28);
        $activeSheet->getColumnDimension('D')->setWidth(19.71);
        $activeSheet->getColumnDimension('E')->setWidth(11.71);
        $activeSheet->getColumnDimension('F')->setWidth(11.42);
        $activeSheet->getColumnDimension('G')->setWidth(21.42);
        $activeSheet->getColumnDimension('H')->setWidth(4.42);
        $activeSheet->getColumnDimension('I')->setWidth(16.42);

        $activeSheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $activeSheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        $margineset = $activeSheet->getPageMargins();
        $margineset->setTop(0.20);
        $margineset->setBottom(0.3);
        $margineset->setRight(0.20);
        $margineset->setLeft(0.80);

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
                'bold' => true,
            ], 'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];

        $headersyle = ['alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER_CONTINUOUS,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        ]];
        $verticaltop = array( 'alignment' => array( 'vertical'  => PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP, ) );
        
        $excel_row=1;
        $count_datas = 0;
        if (!empty($barlist)) {
            foreach ($barlist as $key=>$value) {
                $brgy = $key;
                $brgyname = $value;
                $exporlist = isset($activelist[$brgy]) ? $activelist[$brgy] : array();
                
                if (!empty($exporlist)) {
                    $numItems = count($exporlist);
                    $count_datas = $count_datas + $numItems;
                    $i = $number = $start = $page = 1;
                    $start_amount = 0;
                
                    foreach ($exporlist as $key => $mb) {

                        $totalpage = count($exporlist);
                        $startrow = $excel_row;
                        $fullname = mb_strtoupper($mb["fullname"]);

                        if ($start == 1) {
                            $activeSheet->setCellValue('A'.$excel_row,'Department of Social Welfare and Development');
                            $activeSheet->mergeCells('A'.$excel_row.':I'.$excel_row);
                            $excel_row++;
                            $activeSheet->setCellValue('A'.$excel_row,'Social Pension for Indigent Citizen - CAR');
                            $activeSheet->mergeCells('A'.$excel_row.':I'.$excel_row);

                            $excel_row+=2;
                            $activeSheet->setCellValue('A'.$excel_row, $cap_title);
                            $activeSheet->mergeCells('A'.$excel_row.':I'.$excel_row);
                            $activeSheet->getStyle('A'.$startrow.':I'.$excel_row)->applyFromArray($headersyle);
                            $activeSheet->getStyle('A'.$excel_row)->getFont()->setBold( true );

                            $excel_row++;

                            $activeSheet->setCellValue('A'.$excel_row,"FOR PAYMENT OF 'Social Pension for Indigent Senior Citizen' at ". $brgyname.", ". $munname.", ". $provname ." for the period $headermonth $year");
                            $activeSheet->mergeCells('A'.$excel_row.':I'.$excel_row);
                            $excel_row++;

                            // $table_columns = array("#", "Name", "Birthdate", "SPID #", "Amount", "Signature", "", "Date Paid");
                            $table_columns = array("#", "Name", "", "SPID #", "Amount", "Signature", "", "Date Paid");
                            $hs = "A";
                            $activeSheet->mergeCells('B'.$excel_row.':C'.$excel_row);
                            $activeSheet->mergeCells('F'.$excel_row.':H'.$excel_row);
                            
                            foreach ($table_columns as $tv) {
                                $activeSheet->setCellValue($hs.$excel_row,$tv);
                                $hs++;
                            }
                            $activeSheet->getStyle('A'.$excel_row.':I'.$excel_row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB("cbf2ce");
                            $activeSheet->getStyle('A'.$excel_row.':I'.$excel_row)->applyFromArray($styleArray);

                            $column = 0;
                            $excel_row++;
                        }

                        $start_amount = $start_amount + (int)$mb["amount"];

                        $activeSheet->setCellValue("A".$excel_row , (string)$number);
                        $activeSheet->mergeCells('B'.$excel_row.':C'.$excel_row);
                        $activeSheet->setCellValue("B".$excel_row , $fullname);
                        // $activeSheet->setCellValue("C".$excel_row , $mb["birthdate"]);
                        $activeSheet->setCellValue("D".$excel_row , $mb["spid"]);
                        $activeSheet->setCellValue("E".$excel_row , "₱ ".number_format($mb["amount"],2));
                        $activeSheet->setCellValue("F".$excel_row , "\n\n\n\n\n");
                        $activeSheet->setCellValue("I".$excel_row , "\n\n\n\n\n");
                        $activeSheet->getRowDimension($excel_row)->setRowHeight(35);

                        if(strpos($fullname, "(NEW)") !== false){
                            $activeSheet->getStyle('B'.$excel_row.':C'.$excel_row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('a8f7f7');
                        }

                        $number++; $start++;

                        $activeSheet->getStyle('D'.$excel_row.':I'.$excel_row)->applyFromArray($styleArray);
                        $activeSheet->getStyle('A'.$excel_row)->applyFromArray($styleArray);
                        $activeSheet->getStyle('B'.$excel_row.':D'.$excel_row)->getAlignment()->setWrapText(true);
                        $activeSheet->getStyle('B'.$excel_row.':C'.$excel_row)->applyFromArray($bodynamesp);

                        
                        if(strpos($fullname, "REPLACER") !== false || strpos($fullname, "DECEASED") !== false){
                            $activeSheet->getStyle('B'.$excel_row.':C'.$excel_row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('f7a8a8');
                            $smallfont = array( 'font'  => array( 'size'  => 8, 'name' => 'Calibri' ));
                            $activeSheet->getStyle('B'.$excel_row.':C'.$excel_row)->applyFromArray($smallfont);
                        }

                        if ($start == 11 || $i == $numItems ) {

                            $footerstyle = array(
                                'font'  => array(
                                'size'  => 9,
                                'name' => 'Calibri'
                            ));
                            $excel_row++;

                            $activeSheet->setCellValue("A".$excel_row,"\n");
                            $activeSheet->mergeCells('B'.$excel_row.':C'.$excel_row);
                            $activeSheet->setCellValue("B".$excel_row,"Total");
                            $activeSheet->setCellValue("D".$excel_row,"\n");
                            $activeSheet->setCellValue("E".$excel_row,"₱ ".number_format($start_amount,2));
                            $activeSheet->setCellValue("F".$excel_row,"\n");
                            $activeSheet->setCellValue("I".$excel_row,"\n");
                        
                            $activeSheet->getStyle('A'.$excel_row.':I'.$excel_row)->applyFromArray($styleArray);
                            $activeSheet->getStyle('B'.$excel_row.':C'.$excel_row)->applyFromArray($bodynamesp);
                            
                            $mergeto = $excel_row+2;
                            $activeSheet->getRowDimension($mergeto)->setRowHeight(32);
                            $headerfoot = $excel_row+1;
                            $excel_row++;
                            $activeSheet->mergeCells('A'.$excel_row.':B'.$mergeto);
                            $activeSheet->setCellValue('A'.$excel_row,'I hereby certify that each person whose name appears on this payroll is entitled to cash assistance.');
                            $activeSheet->getStyle('A'.$excel_row)->applyFromArray($headersyle);
                            $activeSheet->getStyle('A'.$excel_row)->getAlignment()->setWrapText(true);
                        
                            $activeSheet->mergeCells('C'.$excel_row.':D'.$mergeto);
                            $activeSheet->setCellValue('C'.$excel_row,'A P P R O V E D  F O R  P A Y M E N T');
                            $activeSheet->getStyle('C'.$excel_row)->applyFromArray($headersyle);
                            
                            $activeSheet->mergeCells('E'.$excel_row.':G'.$mergeto);
                            $icertrow = $excel_row;
                            $activeSheet->setCellValue('E'.$excel_row,'I certify on my official oath that I have paid in cash to each person whose name appears on the payroll the amount set opposite his/her name, he/she having presented himself, established identity, affixed his signature or thumb mark in the space provided therefore.');
                            $smallfont = array( 'font'  => array( 'size'  => 8, 'name' => 'Calibri' ));
                            $activeSheet->getStyle('E'.$excel_row)->applyFromArray($headersyle);
                            $activeSheet->getStyle('E'.$excel_row)->getAlignment()->setWrapText(true);
                        
                            $activeSheet->mergeCells('H'.$excel_row.':I'.$mergeto);
                            $activeSheet->setCellValue('H'.$excel_row,'Witnessed by:');
                            $activeSheet->getStyle('H'.$excel_row)->applyFromArray($headersyle); 
                            $activeSheet->getStyle('H'.$excel_row)->getAlignment()->setWrapText(true);
                        
                            $excel_row+=3;
                        
                            $sign1_name = $signatories->sign1_name;
                            $sign1_position = $signatories->sign1_position;
                            $sign2_name = $signatories->sign2_name;
                            $sign2_position = $signatories->sign2_position;
                        
                            $activeSheet->mergeCells('A'.$excel_row.':B'.$excel_row);
                            $activeSheet->setCellValue('A'.$excel_row,mb_strtoupper($sign1_name));
                            $activeSheet->getStyle('A'.$excel_row)->getFont()->setUnderline(true)->setBold( true );
                            $activeSheet->getStyle('A'.$excel_row.':B'.$excel_row)->applyFromArray($headersyle);
                        
                            $activeSheet->mergeCells('C'.$excel_row.':D'.$excel_row);
                            $activeSheet->setCellValue('C'.$excel_row,mb_strtoupper($sign2_name));
                            $activeSheet->getStyle('C'.$excel_row)->getFont()->setUnderline(true)->setBold( true );
                            $activeSheet->getStyle('C'.$excel_row.':D'.$excel_row)->applyFromArray($headersyle);
                        
                            $activeSheet->mergeCells('E'.$excel_row.':F'.$excel_row);
                            $activeSheet->setCellValue('E'.$excel_row,'_________________________');
                            $activeSheet->mergeCells('E'.$excel_row.':F'.$excel_row);
                            $activeSheet->getStyle('E'.$excel_row.':F'.$excel_row)->applyFromArray($headersyle);

                            $activeSheet->setCellValue('G'.$excel_row,'_________________________');
                            $activeSheet->getStyle('G'.$excel_row)->applyFromArray($headersyle);
                        
                            $activeSheet->mergeCells('H'.$excel_row.':I'.$excel_row);
                            $activeSheet->setCellValue('H'.$excel_row,'_________________________');
                            $activeSheet->mergeCells('H'.$excel_row.':I'.$excel_row);
                            $activeSheet->getStyle('H'.$excel_row.':I'.$excel_row)->applyFromArray($headersyle);
                        
                            $excel_row++;            
                            $activeSheet->mergeCells('A'.$excel_row.':B'.$excel_row);
                            $activeSheet->setCellValue('A'.$excel_row,$sign1_position);
                            $activeSheet->getStyle('A'.$excel_row.':B'.$excel_row)->applyFromArray($headersyle);
                        
                            $activeSheet->mergeCells('C'.$excel_row.':D'.$excel_row);
                            $activeSheet->setCellValue('C'.$excel_row,$sign2_position);
                            $activeSheet->getStyle('C'.$excel_row.':D'.$excel_row)->applyFromArray($headersyle);
                        
                            $activeSheet->mergeCells('E'.$excel_row.':F'.$excel_row);
                            $activeSheet->setCellValue('E'.$excel_row,'PAYMASTER');
                            $activeSheet->mergeCells('E'.$excel_row.':F'.$excel_row);
                            $activeSheet->getStyle('E'.$excel_row.':F'.$excel_row)->applyFromArray($headersyle);
                        
                            $activeSheet->setCellValue('G'.$excel_row,'SDO');
                            $activeSheet->getStyle('G'.$excel_row)->applyFromArray($headersyle);

                            $activeSheet->mergeCells('H'.$excel_row.':I'.$excel_row);
                            $activeSheet->setCellValue('H'.$excel_row,'MSWDO/FOCAL/OSCA');
                            $activeSheet->mergeCells('H'.$excel_row.':I'.$excel_row);
                            $activeSheet->getStyle('H'.$excel_row.':I'.$excel_row)->applyFromArray($headersyle);
                        
                            $start = 1;
                            $excel_row=$excel_row+1;

                            $activeSheet->setBreak('A'. $excel_row, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
                            $activeSheet->getStyle('A'.$headerfoot.":I".$excel_row)->applyFromArray($footerstyle);
                            $activeSheet->getStyle('E'.$icertrow)->applyFromArray($smallfont); 
                            $start_amount =0;
                            $total_page = $totalpage/10;
                            // $activeSheet->setCellValue('F'.$excel_row,"Page ". $page. " of " . ceil($totalpage/10));
                            $page++;

                            if ($page == $totalpage) { $page =1; }
                        }

                        $excel_row++;
                        $i++;
                    }

                }
            }

            // $activeSheet->getCell('A:F');
            $excel_row--;
            $activeSheet->getPageSetup()->setPrintArea('A:I');
            $activeSheet->getHeaderFooter()->setOddFooter('&R page &P of &N');
            // ->setOddHeader('&R&P');
            $activeSheet->setShowGridlines(true);
            //1st sheet until here

        // pinabalik rep cap sheet as of  07/21/2021
        // removed rep cap sheet as of  04/21/2021
        //2nd Sheet
        if(!empty($forreplist)){
            $smallfont =[
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_BOTTOM,
                ],
                'font'  => [
                    'size'  => 8,
                    'name' => 'Calibri'
            ]];

            $border = ['borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ]]];

            $object->createSheet(1);
            $object->setActiveSheetIndex(1);
            $activeSheet1=$object->getActiveSheet();
            $activeSheet1->setTitle("REPCAP");
            $activeSheet1->getColumnDimension('A')->setWidth(3); //#
            $activeSheet1->getColumnDimension('B')->setWidth(31.28); //replacement name
            $activeSheet1->getColumnDimension('C')->setWidth(14.42);
            $activeSheet1->getColumnDimension('D')->setWidth(15); //barangay
            $activeSheet1->getColumnDimension('E')->setWidth(10); //osca
            $activeSheet1->getColumnDimension('F')->setWidth(15); //bdate
            $activeSheet1->getColumnDimension('G')->setWidth(7.42); //amount 
            $activeSheet1->getColumnDimension('H')->setWidth(20); //signature 
            $activeSheet1->getColumnDimension('I')->setWidth(7.28); //signature 
            $activeSheet1->getColumnDimension('J')->setWidth(12.42); //date paid 

            $activeSheet1->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
            $activeSheet1->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
            $margineset1=$activeSheet1->getPageMargins();
            $margineset1->setTop(0.20);
            $margineset1->setBottom(0.3);
            $margineset1->setRight(0.20);
            $margineset1->setLeft(0.80);

            $excel_row=1;
            $i = $number = $start = $page = 1;
            $start_amount = 0;
            $numItems = 0;

            foreach ($barlist as $key=>$value) {
                $brgy = $key;
                $brgyname = $value;
                $exporlist = isset($forreplist[$brgy]) ? $forreplist[$brgy] : array();
                
                if (!empty($exporlist)) {
                    $numItems += count($exporlist);
                    $count_datas = $count_datas + count($exporlist);
                
                    foreach ($exporlist as $key => $mb) {

                        $totalpage = count($exporlist);
                        $startrow = $excel_row;
                        $fullname = mb_strtoupper($mb["fullname"]);
                        $brgyname = $mb["barangay"];
                        $reasonforrep = $mb["remarks"];
                        if($reasonforrep == ""){
                            $reason="Reason: _________________________________";
                        }else{
                            $reason = "Reason: " . $reasonforrep;
                        }

                        $reason .= " [" . $brgyname. "]";

                        if ($start == 1) {
                            $activeSheet1->setCellValue('A'.$excel_row,'Department of Social Welfare and Development');
                            $activeSheet1->mergeCells('A'.$excel_row.':J'.$excel_row);
                            $excel_row++;
                            $activeSheet1->setCellValue('A'.$excel_row,'Social Pension for Indigent Citizen - CAR');
                            $activeSheet1->mergeCells('A'.$excel_row.':J'.$excel_row);

                            $excel_row++;
                            $excel_row++;
                            $activeSheet1->setCellValue('A'.$excel_row,'CASH ASSISTANCE PAYROLL');
                            $activeSheet1->mergeCells('A'.$excel_row.':J'.$excel_row);
                            $activeSheet1->getStyle('A'.$startrow.':J'.$excel_row)->applyFromArray($headersyle);
                            $activeSheet1->getStyle('A'.$excel_row)->getFont()->setBold( true );
                            $excel_row++;

                            $activeSheet1->setCellValue('A'.$excel_row,"FOR PAYMENT OF 'Social Pension for Indigent Senior Citizen' at ". $munname.", ". $provname ." for the period $headermonth $year (Replacement)");
                            $activeSheet1->mergeCells('A'.$excel_row.':J'.$excel_row);
                            $excel_row++;

                            $table_columns = array("#", "Name(Last, First, Middle)", "", "Barangay", "SPID ID", "Birthdate", "Amount", "Signature", "", "Date Paid");
                            $hs = "A";
                            foreach ($table_columns as $tv) {
                                $activeSheet1->setCellValue($hs.$excel_row,$tv);
                                $hs++;
                            }
                            $activeSheet1->getStyle('A'.$excel_row.':J'.$excel_row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB("cbf2ce");
                            $activeSheet1->getStyle('A'.$excel_row.':J'.$excel_row)->applyFromArray($styleArray);

                            $column = 0;
                            $excel_row++;
                        }

                        $activeSheet1->setCellValue("A".$excel_row , (string)$number);
                        $activeSheet1->getStyle('A'.$excel_row)->applyFromArray($styleArray);
                        $replacer_spid = "";
                        $replacer_birthdate = "";
                        $replacer_barname = "";
                        $ColB = "";

                        if(!empty($mb["replacer_spid"])){
                            $ColB = $mb["replacer_fullname"] . " \r\n  \r\n";
                            $replacer_barname = $mb["replacer_barname"] . ", " . $mb["replacer_munname"]. " \r\n  \r\n \r\n";
                            $replacer_spid = $mb["replacer_spid"]. " \r\n  \r\n \r\n";
                            $replacer_birthdate = $mb["replacer_birthdate"]. " \r\n  \r\n \r\n";
                        }
                        $ColB .= "Rep of: ".mb_strtoupper($fullname)." (".$mb["spid"].") \r\n".$reason;
                        $activeSheet1->mergeCells('B'.$excel_row.':C'.$excel_row)->setCellValue("B".$excel_row , $ColB);
                        $activeSheet1->setCellValue("D".$excel_row , $replacer_barname);
                        $activeSheet1->setCellValue("E".$excel_row , $replacer_spid);
                        $activeSheet1->setCellValue("F".$excel_row , $replacer_birthdate);

                        $activeSheet1->mergeCells('H'.$excel_row.':I'.$excel_row);
                        $activeSheet1->getStyle('B'.$excel_row.':F'.$excel_row)->getAlignment()->setWrapText(true);
                        $activeSheet1->getStyle('B'.$excel_row)->applyFromArray($smallfont);
                        $activeSheet1->getRowDimension($excel_row)->setRowHeight(60);
                        $activeSheet1->getStyle('A'.$excel_row.':J'.$excel_row)->applyFromArray($border);

                        $number++; $start++;

                        if ($start == 6 || $i == $forrepCount ) {     
                        
                            $excel_row++;
                            $activeSheet1->setCellValue("A".$excel_row,"\n");
                            $activeSheet1->mergeCells('B'.$excel_row.':C'.$excel_row)->setCellValue("B".$excel_row,"Total");
                            $activeSheet1->mergeCells('H'.$excel_row.':I'.$excel_row);
                            $activeSheet1->getStyle('A'.$excel_row.':J'.$excel_row)->applyFromArray($border);
                            
                            $mergeto = $excel_row+4;
                            $headerfoot = $excel_row;
                            $excel_row++;
                            $activeSheet1->mergeCells('A'.$excel_row.':B'.$mergeto);
                            $activeSheet1->setCellValue('A'.$excel_row,'I hereby certify that each person whose name appears on this payroll is entitled to cash assistance.');
                            $activeSheet1->getStyle('A'.$excel_row)->applyFromArray($headersyle);
                            $activeSheet1->getStyle('A'.$excel_row)->getAlignment()->setWrapText(true);

                            $activeSheet1->mergeCells('C'.$excel_row.':D'.$mergeto);
                            $activeSheet1->setCellValue('C'.$excel_row,'A P P R O V E D  F O R  P A Y M E N T');
                            $activeSheet1->getStyle('C'.$excel_row)->applyFromArray($headersyle);
                            $activeSheet1->getStyle('C'.$excel_row)->getAlignment()->setWrapText(true);

                            $activeSheet1->mergeCells('E'.$excel_row.':H'.$mergeto);
                            $activeSheet1->setCellValue('E'.$excel_row,'I certify on my official oath that I have paid in cash to each person whose name appears on the payroll the amount set opposite his/her name, he/she having presented himself, established identity, affixed his signature or thumb mark in the space provided therefore.');
                            $activeSheet1->getStyle('E'.$excel_row)->applyFromArray($headersyle);
                            $activeSheet1->getStyle('E'.$excel_row.':H'.$mergeto)->getAlignment()->setWrapText(true);

                            $activeSheet1->mergeCells('I'.$excel_row.':J'.$mergeto);
                            $activeSheet1->setCellValue('I'.$excel_row,'Witnessed by:');
                            $activeSheet1->getStyle('I'.$excel_row)->applyFromArray($headersyle);
                            $activeSheet1->getStyle('I'.$excel_row)->getAlignment()->setWrapText(true);

                            $excel_row++;

                            $sign1_name = $signatories->sign1_name;
                            $sign1_position = $signatories->sign1_position;
                            $sign2_name = $signatories->sign2_name;
                            $sign2_position = $signatories->sign2_position;

                            $excel_row = $excel_row + 5;
                            $footerstart = $excel_row;
                            $activeSheet1->setCellValue('A'.$excel_row,mb_strtoupper($sign1_name));
                            $activeSheet1->getStyle('A'.$excel_row)->getFont()->setUnderline(true)->setBold( true );
                            $activeSheet1->mergeCells('A'.$excel_row.':B'.$excel_row);
                            $activeSheet1->getStyle('A'.$excel_row.':B'.$excel_row)->applyFromArray($headersyle);

                            $activeSheet1->setCellValue('C'.$excel_row,mb_strtoupper($sign2_name));
                            $activeSheet1->getStyle('C'.$excel_row)->getFont()->setUnderline(true)->setBold( true );
                            $activeSheet1->mergeCells('C'.$excel_row.':D'.$excel_row);
                            $activeSheet1->getStyle('C'.$excel_row.':D'.$excel_row)->applyFromArray($headersyle);

                            $activeSheet1->setCellValue('E'.$excel_row,'_________________________');
                            $activeSheet1->mergeCells('E'.$excel_row.':F'.$excel_row);
                            $activeSheet1->getStyle('E'.$excel_row.':F'.$excel_row)->applyFromArray($headersyle);

                            $activeSheet1->setCellValue('G'.$excel_row,'_________________________');
                            $activeSheet1->mergeCells('G'.$excel_row.':H'.$excel_row);
                            $activeSheet1->getStyle('G'.$excel_row.':H'.$excel_row)->applyFromArray($headersyle);

                            $activeSheet1->setCellValue('I'.$excel_row,'______________________');
                            $activeSheet1->mergeCells('I'.$excel_row.':J'.$excel_row);
                            $activeSheet1->getStyle('I'.$excel_row.':J'.$excel_row)->applyFromArray($headersyle);

                            $excel_row++;            
                            $activeSheet1->setCellValue('A'.$excel_row,$sign1_position);
                            $activeSheet1->mergeCells('A'.$excel_row.':B'.$excel_row);
                            $activeSheet1->getStyle('A'.$excel_row.':B'.$excel_row)->applyFromArray($headersyle);

                            $activeSheet1->setCellValue('C'.$excel_row,$sign2_position);
                            $activeSheet1->getStyle('C'.$excel_row.':D'.$excel_row)->applyFromArray($headersyle);

                            $activeSheet1->mergeCells('E'.$excel_row.':F'.$excel_row);
                            $activeSheet1->setCellValue('E'.$excel_row,"PAYMASTER");
                            $activeSheet1->mergeCells('G'.$excel_row.':H'.$excel_row);
                            $activeSheet1->setCellValue('G'.$excel_row,"SDO");
                            $activeSheet1->mergeCells('I'.$excel_row.':J'.$excel_row);
                            $activeSheet1->setCellValue('I'.$excel_row,"MSWDO/FOCAL/OSCA");
                            $activeSheet1->getStyle('E'.$excel_row.':J'.$excel_row)->applyFromArray($headersyle);

                            $start = 1;
                            $excel_row=$excel_row+1;

                            $activeSheet1->setBreak('A'. $excel_row, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
                            $activeSheet1->getStyle('A'.$headerfoot.":J".$excel_row)->applyFromArray($footerstyle);
                            $start_amount =0;
                            $total_page = $totalpage/10;
                            $page++;

                            if ($page == $totalpage) {
                                $page =1;
                            }
                        }

                        $excel_row++;
                        $i++;
                    }

                }
            }

            $activeSheet1->getPageSetup()->setPrintArea('A:J');
            $activeSheet1->getHeaderFooter()->setOddFooter('&R page &P of &N');
            $activeSheet1->setShowGridlines(true);
        }
         //2nd Sheet
        // removed rep cap sheet as of  04/21/2021
        // pinabalik rep cap sheet as of  07/21/2021
        }

            // if(!empty($countrepcap)){
            // $totalcount=$count_datas+$countrepcap; }
            // else { $totalcount=$count_datas; }

            $totalcount=$count_datas;

            $object->setActiveSheetIndex(0);
            $activeSheet->setSelectedCell('A1');
            
            $filename = $fileTitle."_(".$totalcount.")_".date("Y-m-d")."_".$generatesemqtr.".xlsx";
            $writer = new Xlsx($object);

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'. $filename); 
            header('Cache-Control: max-age=0');

            $writer->save('php://output');

            userLogs(sesdata('id') , sesdata('fullname') , "EXPORT", "Export Payroll: $munname");

    }

    public function exportPayrollBaguio($municipality,$generate_year,$generate_modepayment,$generate_qtrsem,$spstatus,$type="Cap"){
        ini_set('memory_limit', '999M');
        set_time_limit(0);
        // ini_set('max_execution_time', 3000);
        ignore_user_abort(true);

        if($generate_modepayment=="Quarter"){
            $amount = 1500;
            if($generate_qtrsem==1){ $headermonth = "January to March"; $generatesemqtr="1st quarter"; }
            else if($generate_qtrsem==2){ $headermonth = "April to June"; $generatesemqtr="2nd quarter"; }
            else if($generate_qtrsem==3){ $headermonth = "July to September"; $generatesemqtr="3rd quarter"; }
            else if($generate_qtrsem==4){ $headermonth = "October to December"; $generatesemqtr="4th quarter"; }
        }else if($generate_modepayment=="Semester"){
            $amount = 3000;
            if($generate_qtrsem==1){ $headermonth = "January to June"; $generatesemqtr="1st semester"; }
            else if($generate_qtrsem==2){ $headermonth = "July to December"; $generatesemqtr="2nd semester"; }
        }else{
            $amount = 6000;
            $headermonth = "January to December"; $generatesemqtr="year"; 
        }

        $object = new Spreadsheet();
        $object->createSheet(0);
        $object->setActiveSheetIndex(0);
        $activeSheet =$object->getActiveSheet();
        $activeSheet->setTitle("CASH ASSISTANCE PAYROLL");
        
        $alphabet = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");
        $excel_row=1;
        $count_datas = 0;

        if (!empty($alphabet)) {
            foreach ($alphabet as $aaa) {
            $exporlist="";

           // if(!empty($qry)){
                if ($type == "Cap") {
                    $query_data = "
                    SELECT tg.b_id,tg.lastname,tg.firstname,tg.middlename,CONCAT(tg.lastname,', ',tg.firstname,' ',tg.middlename) as fullname,tg.extensionname,tg.connum,tg.`barangay` FROM tblgeneral tg
                    WHERE  tg.city = '$municipality' AND lastname LIKE '$aaa%' AND archive_status = '0'  AND (tg.sp_status = 'Active' OR tg.sp_status = 'Additional')  ORDER BY fullname ASC";
                } else{
                    $query_data = "
                    SELECT tp.*, tg.b_id,tg.lastname,tg.firstname,tg.middlename,CONCAT(tg.lastname,', ',tg.firstname,' ',tg.middlename) as fullname,tg.extensionname,tg.connum,tg.`barangay` FROM tblpayroll tp LEFT JOIN tblgeneral tg ON tg.connum = tp.spid WHERE tp.liquidation = '0' AND  tp.mun_code = '$municipality' AND lastname LIKE '$aaa%' AND tp.mode_of_payment = '$generate_modepayment' AND tp.period = '$generate_qtrsem'  AND tp.year = '$generate_year'  ORDER BY fullname ASC";
                }

                $exporlist =   $this->Main->raw($query_data);
                if(!empty($exporlist)){$count_datas = $count_datas + count($exporlist);}
            //}else{ $exporlist=""; }

                $cap_location_data = getLocation("m.mun_code='$municipality'",'row');
                $provincename = $cap_location_data->prov_name;
                $municipalityname = $cap_location_data->mun_name;
                $barangayname = $cap_location_data->bar_name;

                $activeSheet->getColumnDimension('A')->setWidth(4);
                $activeSheet->getColumnDimension('B')->setWidth(25.42);
                $activeSheet->getStyle('B')->getAlignment()->setWrapText(true);
                $activeSheet->getColumnDimension('C')->setWidth(11);
                $activeSheet->getColumnDimension('D')->setWidth(17);
                $activeSheet->getColumnDimension('E')->setWidth(30);
                $activeSheet->getStyle('E')->getAlignment()->setWrapText(true);
                $activeSheet->getColumnDimension('F')->setWidth(10.57);
                $activeSheet->getColumnDimension('G')->setWidth(18.14);
                $activeSheet->getColumnDimension('H')->setWidth(6);
                $activeSheet->getColumnDimension('I')->setWidth(13.42);

                $activeSheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $activeSheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
                $margineset = $activeSheet->getPageMargins();
                $margineset->setTop(0.20);
                $margineset->setBottom(0.3);
                $margineset->setRight(0.20);
                $margineset->setLeft(0.80);

                $styleArray = [
                'font' => [
                'bold' => true,
                ], 'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ] ,'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER_CONTINUOUS,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ];

                $bodynamesp =  [
                    'font' => [
                    'bold' => true,
                ], 'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    ],
                ],'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
                ];

                $headersyle = ['alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER_CONTINUOUS,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ]];
                $verticaltop = array( 'alignment' => array( 'vertical'  => PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP, ) );

                if (!empty($exporlist)) {
                    $numItems = count($exporlist);
                    $i = 1;
                    $number = 1;
                    $start = 1;
                    $start_amount = 0;
                    $page = 1;

                    foreach ($exporlist as $key => $mb) {
                        $bid = $mb->b_id;
                        $totalpage = count($exporlist);

                        $startrow = $excel_row;
                        $fullname = $mb->lastname. ", ". $mb->firstname . " " . $mb->middlename . " " . $mb->extensionname;

                        if ($start == 1) {
                            $activeSheet->setCellValue('A'.$excel_row,'Department of Social Welfare and Development');
                            $activeSheet->mergeCells('A'.$excel_row.':I'.$excel_row);
                            $excel_row++;
                            $activeSheet->setCellValue('A'.$excel_row,'Social Pension for Indigent Citizen - CAR');
                            $activeSheet->mergeCells('A'.$excel_row.':I'.$excel_row);

                            $excel_row++;
                            $excel_row++;
                            $activeSheet->setCellValue('A'.$excel_row,'CASH ASSISTANCE PAYROLL');
                            $activeSheet->mergeCells('A'.$excel_row.':I'.$excel_row);
                            $activeSheet->getStyle('A'.$startrow.':I'.$excel_row)->applyFromArray($headersyle);
                            $activeSheet->getStyle('A'.$excel_row)->getFont()->setBold( true );

                            $excel_row++;

                            $activeSheet->setCellValue('A'.$excel_row,"FOR PAYMENT OF 'Social Pension for Indigent Senior Citizen' at ". mb_strtoupper($municipalityname) ." for the period $headermonth $generate_year");
                            $activeSheet->mergeCells('A'.$excel_row.':I'.$excel_row);
                            $excel_row++;

                            $table_columns = array("#", "Name", "", "SPID #", "Barangay", "Amount", "Signature", "", "Date Paid");
                            $hs = "A";
                            foreach ($table_columns as $tv) {
                                $activeSheet->setCellValue($hs.$excel_row,$tv);
                                $hs++;
                            }
                            $activeSheet->getStyle('A'.$excel_row.':I'.$excel_row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB("cbf2ce");
                            $activeSheet->getStyle('A'.$excel_row.':I'.$excel_row)->applyFromArray($styleArray);

                            $column = 0;
                            $excel_row++;
                        }
                    
                        $start_amount = $start_amount +$amount;
                        // if($generate_year==2019 && $generate_modepayment=="Semester"){ //pansamantala kasi next year per sem na
                        //     if(($this->Main->count("tblsocpen",array("b_id"=>$bid, "year"=>$generate_year, "quarter"=>2, "liquidation"=>1)))==0 || ($this->Main->count("tblsocpen",array("b_id"=>$bid, "year"=>$generate_year, "quarter"=>1, "liquidation"=>1)))==0){
                        //         $activeSheet->getStyle('A'.$excel_row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB("ede79f");
                        //     }
                        // }else if($generate_year==2020 && $generate_modepayment=="Semester" && $generate_qtrsem==2){ //for next year
                        //     if(($this->Main->count("tblsocpen",array("b_id"=>$bid, "year"=>$generate_year, $generate_modepayment=>1, "liquidation"=>1)))==0){
                        //         $activeSheet->getStyle('A'.$excel_row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB("ede79f");
                        //     }
                        // }
                        $activeSheet->setCellValue("A".$excel_row , (string)$number);
                        $activeSheet->mergeCells('B'.$excel_row.':C'.$excel_row)->setCellValue("B".$excel_row , mb_strtoupper($fullname));
                        $activeSheet->setCellValue("D".$excel_row , $mb->connum);
                        $brgyname = getLocation("b.bar_code = '$mb->barangay'","row")->bar_name;
                        $activeSheet->setCellValue("E".$excel_row , $brgyname);
                        $activeSheet->setCellValue("F".$excel_row , "₱ ".number_format($amount,2));
                        $activeSheet->mergeCells('G'.$excel_row.':H'.$excel_row)->setCellValue("G".$excel_row , "\n\n\n\n\n");
                        $activeSheet->setCellValue("I".$excel_row , "\n\n\n\n\n");
                        $activeSheet->getRowDimension($excel_row)->setRowHeight(35);

                        $number++; $start++;

                        $activeSheet->getStyle('D'.$excel_row.':I'.$excel_row)->applyFromArray($styleArray);
                        $activeSheet->getStyle('A'.$excel_row)->applyFromArray($styleArray);
                        $activeSheet->getStyle('B'.$excel_row.':C'.$excel_row)->applyFromArray($bodynamesp);

                            if ($start == 11 || $i == $numItems ) {

                                $footerstyle = array(
                                    'font'  => array(
                                    'size'  => 9,
                                    'name' => 'Calibri'
                                ));
                                $excel_row++;
                                $activeSheet->setCellValue("A".$excel_row,"\n");
                                $activeSheet->mergeCells('B'.$excel_row.':C'.$excel_row)->setCellValue("B".$excel_row,"Total");
                                $activeSheet->setCellValue("D".$excel_row,"\n");
                                $activeSheet->setCellValue("E".$excel_row,"  ");
                                $activeSheet->setCellValue("F".$excel_row,"₱ ".number_format($start_amount,2));
                                $activeSheet->mergeCells('G'.$excel_row.':H'.$excel_row)->setCellValue("G".$excel_row,"\n");
                                $activeSheet->setCellValue("I".$excel_row,"\n");
                            
                                $activeSheet->getStyle('A'.$excel_row.':I'.$excel_row)->applyFromArray($styleArray);
                                $activeSheet->getStyle('B'.$excel_row.':C'.$excel_row)->applyFromArray($bodynamesp);
                                $mergeto = $excel_row+3;
                                $excel_row++;
                                $headerfoot = $excel_row;
                                $activeSheet->mergeCells('A'.$excel_row.':B'.$mergeto)->setCellValue('A'.$excel_row,'I hereby certify that each person whose name appears on this roll is entitled to cash assistance.');
                                $activeSheet->getStyle('A'.$excel_row)->applyFromArray($headersyle); 
                                $activeSheet->getStyle('A'.$excel_row)->getAlignment()->setWrapText(true);
                            
                                $activeSheet->mergeCells('C'.$excel_row.':D'.$mergeto)->setCellValue('C'.$excel_row,'A P P R O V E D  F O R  P A Y M E N T');
                                $activeSheet->getStyle('C'.$excel_row)->applyFromArray($headersyle);
                                $activeSheet->getStyle('C'.$excel_row)->getAlignment()->setWrapText(true);
                            
                                $icertrow = $excel_row;
                                $activeSheet->mergeCells('E'.$excel_row.':G'.$mergeto)->setCellValue('E'.$excel_row,'I certify on my official oath that I have paid in cash to each person whose name appears on the payroll the amount set opposite his/her name, he/she having presented himself, established identity, affixed his signature or thumb mark in the space provided therefore.');
                                $smallfont = array( 'font'  => array( 'size'  => 8, 'name' => 'Calibri' ));
                                $activeSheet->getStyle('E'.$excel_row)->applyFromArray($headersyle); 
                                $activeSheet->getStyle('E'.$excel_row.':G'.$mergeto)->getAlignment()->setWrapText(true);
                            
                                $activeSheet->mergeCells('H'.$excel_row.':I'.$mergeto)->setCellValue('H'.$excel_row,'Witnessed by:');
                                $activeSheet->getStyle('H'.$excel_row)->applyFromArray($headersyle);
                                $activeSheet->getStyle('H'.$excel_row)->getAlignment()->setWrapText(true);
                            
                                $signatories = getSignatories("sign1_name, sign1_position, sign2_name, sign2_position",array('file'=>"CAP"),"","row");
                                $sign1_name = $signatories->sign1_name;
                                $sign1_position = $signatories->sign1_position;
                                $sign2_name = $signatories->sign2_name;
                                $sign2_position = $signatories->sign2_position;

                                $excel_row = $mergeto + 3;
                                $footerstart = $excel_row;
                                $activeSheet->setCellValue('A'.$excel_row,mb_strtoupper($sign1_name));
                                $activeSheet->getStyle('A'.$excel_row)->getFont()->setUnderline(true)->setBold( true );
                                $activeSheet->mergeCells('A'.$excel_row.':B'.$excel_row);
                                $activeSheet->getStyle('A'.$excel_row.':B'.$excel_row)->applyFromArray($headersyle);
                            
                                $activeSheet->setCellValue('C'.$excel_row,mb_strtoupper($sign2_name));
                                $activeSheet->getStyle('C'.$excel_row)->getFont()->setUnderline(true)->setBold( true );
                            
                                $activeSheet->mergeCells('C'.$excel_row.':D'.$excel_row);
                                $activeSheet->getStyle('C'.$excel_row.':D'.$excel_row)->applyFromArray($headersyle);
                            
                                $activeSheet->mergeCells('E'.$excel_row.':G'.$excel_row)->setCellValue('E'.$excel_row,'________________________________________________________');
                                $activeSheet->getStyle('E'.$excel_row.':G'.$excel_row)->applyFromArray($headersyle);
                            
                                $activeSheet->mergeCells('H'.$excel_row.':I'.$excel_row)->setCellValue('H'.$excel_row,'__________________');
                                $activeSheet->getStyle('H'.$excel_row.':I'.$excel_row)->applyFromArray($headersyle);
                            
                                $excel_row++;            
                                $activeSheet->setCellValue('A'.$excel_row,$sign1_position);
                                $activeSheet->getStyle('A'.$excel_row.':B'.$excel_row)->applyFromArray($headersyle);
                            
                                $activeSheet->setCellValue('C'.$excel_row,$sign2_position);
                                $activeSheet->getStyle('C'.$excel_row.':D'.$excel_row)->applyFromArray($headersyle);
                            
                                $activeSheet->mergeCells('E'.$excel_row.':G'.$excel_row)->setCellValue('E'.$excel_row,'PAYMASTER');
                                $activeSheet->getStyle('E'.$excel_row.':G'.$excel_row)->applyFromArray($headersyle);
                            
                                $activeSheet->mergeCells('H'.$excel_row.':I'.$excel_row)->setCellValue('H'.$excel_row,'MSWDO/FOCAL/OSCA');
                                $activeSheet->getStyle('H'.$excel_row.':I'.$excel_row)->applyFromArray($headersyle);
                            
                                $start = 1;
                                $excel_row=$excel_row+1;

                                $activeSheet->setBreak('A'. $excel_row, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
                                $activeSheet->getStyle('A'.$headerfoot.":I".$excel_row)->applyFromArray($footerstyle);
                                $activeSheet->getStyle('E'.$icertrow)->applyFromArray($smallfont); 
                                $start_amount =0;
                                $total_page = $totalpage/10;
                                $page++;

                                if ($page == $totalpage) {
                                    $page =1;
                                }

                            }

                        $excel_row++;
                        $i++;
                    }

                }

            }
        }

        $activeSheet->getPageSetup()->setPrintArea('A:I');
        $activeSheet->getHeaderFooter()->setOddFooter('&R page &P of &N');
        $activeSheet->setShowGridlines(true);
        //1st sheet until here

        // //2nd sheet starts here //repcap

        // if ($type == "Cap") {
        //     $queryforrep_data = "
        //     SELECT tg.lastname,tg.firstname,tg.middlename,CONCAT(tg.lastname,', ',tg.firstname,' ',tg.middlename) as fullname,
        //     tg.extensionname,tg.connum,tg.`barangay`, tr.name, tg.sp_inactive_remarks, tg.inactive_reason_id FROM tblgeneral tg
        //     LEFT JOIN tblinactivereason tr ON tg.inactive_reason_id=tr.id 
        //     WHERE  tg.city = '$municipality' AND tg.sp_status = 'ForReplacement' AND tg.archive_status = 0 ORDER BY fullname ASC";
        // } else{
        //     // $queryforrep_data = "
        //     // SELECT  tp.*, tg.lastname,tg.firstname,tg.middlename,CONCAT(tg.lastname,', ',tg.firstname,' ',tg.middlename) as fullname, tg.extensionname,tg.connum,tg.`barangay`, tg.inactive_reason_id, tg.sp_inactive_remarks FROM tblpayroll tp 
        //     // LEFT JOIN tblgeneral tg ON tg.connum = tp.spid WHERE  tg.city = '$municipality' AND tg.sp_status = 'ForReplacement' AND tp.mode_of_payment = '$generate_modepayment' AND tp.period = '$generate_qtrsem'  AND tp.year = '$generate_year'  ORDER BY fullname ASC";

        //     $queryforrep_data = "
        //     SELECT tp.*, tg.b_id,tg.lastname,tg.firstname,tg.middlename,CONCAT(tg.lastname,', ',tg.firstname,' ',tg.middlename) as fullname,tg.extensionname,tg.connum,tg.`barangay`, tg.inactive_reason_id, tg.sp_inactive_remarks FROM tblpayroll tp LEFT JOIN tblgeneral tg ON tg.connum = tp.spid WHERE tp.liquidation = '0' AND  tp.mun_code = '$municipality' AND tg.sp_status = 'ForReplacement'  AND tp.mode_of_payment = '$generate_modepayment' AND tp.period = '$generate_qtrsem'  AND tp.year = '$generate_year' ";

        //     if($spstatus == "Additional"){
        //         $queryforrep_data .= " AND tg.batch_no = '20' ";
        //     }else{
        //         $queryforrep_data .= " AND tg.batch_no <> '20' ";
        //     }
        //     $queryforrep_data .= " ORDER BY fullname ASC";
        // }

        // $exportrepcap =   $this->Main->raw($queryforrep_data);

        // if(!empty($exportrepcap)){

        //     $smallfont =
        //     [
        //     'alignment' => [
        //         'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
        //         'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_BOTTOM,
        //     ],
        //     'font'  => [
        //         'size'  => 8,
        //         'name' => 'Calibri'
        //     ]];
        //     $border = 
        //     [
        //     'borders' => [
        //         'allBorders' => [
        //             'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
        //     ]]];

        //     $object->createSheet(1);
        //     $object->setActiveSheetIndex(1);
        //     $activeSheet1=$object->getActiveSheet();
        //     $activeSheet1->setTitle("REPCAP");
        //     $countrepcap=0;

        //     $countrepcap = count($exportrepcap);

        //     $activeSheet1->getColumnDimension('A')->setWidth(3); //#
        //     $activeSheet1->getColumnDimension('B')->setWidth(31.28); //replacement name
        //     $activeSheet1->getColumnDimension('C')->setWidth(14.42);
        //     $activeSheet1->getColumnDimension('D')->setWidth(15); //barangay
        //     $activeSheet1->getColumnDimension('E')->setWidth(10); //osca
        //     $activeSheet1->getColumnDimension('F')->setWidth(15); //bdate
        //     $activeSheet1->getColumnDimension('G')->setWidth(7.42); //amount 
        //     $activeSheet1->getColumnDimension('H')->setWidth(20); //signature 
        //     $activeSheet1->getColumnDimension('I')->setWidth(7.28); //signature 
        //     $activeSheet1->getColumnDimension('J')->setWidth(12.42); //date paid 

        //     $activeSheet1->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        //     $activeSheet1->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        //     $margineset1=$activeSheet1->getPageMargins();
        //     $margineset1->setTop(0.20);
        //     $margineset1->setBottom(0.3);
        //     $margineset1->setRight(0.20);
        //     $margineset1->setLeft(0.80);

        //     $numItems1 = $countrepcap;
        //     $i = $number = $start = $page = $excel_row = 1;

        //     foreach ($exportrepcap as $key => $mb) {
        //         $totalpage = count($exportrepcap);
        //         $startrow = $excel_row;
        //         $fullname = $mb->lastname. ", ". $mb->firstname . " " . $mb->middlename . " " . $mb->extensionname;

        //         if ($start == 1) {
        //             $activeSheet1->setCellValue('A'.$excel_row,'Department of Social Welfare and Development');
        //             $activeSheet1->mergeCells('A'.$excel_row.':J'.$excel_row);
        //             $excel_row++;
        //             $activeSheet1->setCellValue('A'.$excel_row,'Social Pension for Indigent Citizen - CAR');
        //             $activeSheet1->mergeCells('A'.$excel_row.':J'.$excel_row);

        //             $excel_row++;
        //             $excel_row++;
        //             $activeSheet1->setCellValue('A'.$excel_row,'CASH ASSISTANCE PAYROLL');
        //             $activeSheet1->mergeCells('A'.$excel_row.':J'.$excel_row);
        //             $activeSheet1->getStyle('A'.$startrow.':J'.$excel_row)->applyFromArray($headersyle);
        //             $activeSheet1->getStyle('A'.$excel_row)->getFont()->setBold( true );
        //             $excel_row++;

        //             $activeSheet1->setCellValue('A'.$excel_row,"FOR PAYMENT OF 'Social Pension for Indigent Senior Citizen' at ". $municipalityname.", ". $provincename ." for the period $headermonth $generate_year (Replacement)");
        //             $activeSheet1->mergeCells('A'.$excel_row.':J'.$excel_row);
        //             $excel_row++;

        //             $table_columns = array("#", "Name(Last, First, Middle)", "", "Barangay", "OSCA ID", "Birthdate", "Amount", "Signature", "", "Date Paid");
        //             $hs = "A";
        //             foreach ($table_columns as $tv) {
        //                 $activeSheet1->setCellValue($hs.$excel_row,$tv);
        //                 $hs++;
        //             }
        //             $activeSheet1->getStyle('A'.$excel_row.':J'.$excel_row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB("cbf2ce");
        //             $activeSheet1->getStyle('A'.$excel_row.':J'.$excel_row)->applyFromArray($styleArray);

        //             $column = 0;
        //             $excel_row++;
        //         }

        //         $activeSheet1->setCellValue("A".$excel_row , (string)$number);
        //         $activeSheet1->getStyle('A'.$excel_row)->applyFromArray($styleArray);
        //         $inactivedate=""; $reason="Reason: _________________________________";
                
		// 		$reasonforrep = getForRepReason($mb->inactive_reason_id,"row");
        //         if(!empty($reasonforrep)){
        //             if($reasonforrep->name=="Deceased" AND !empty($mb->sp_inactive_remarks)){ $inactivedate = "(".date_format(new DateTime($mb->sp_inactive_remarks),"Y-m-d").")"; }
        //             else if($reasonforrep->name=="Deceased" AND empty($mb->sp_inactive_remarks)){$inactivedate="(Date Deceased:___________________)";}
        //             else if($reasonforrep->name=="With Pension" AND !empty($mb->sp_inactive_remarks)){$inactivedate="($mb->sp_inactive_remarks)";}
        //             else if($reasonforrep->name=="Transferred" AND !empty($mb->sp_inactive_remarks)){$inactivedate="($mb->sp_inactive_remarks)";}
        //             $reason = $reasonforrep->name;
        //         }
        //         $activeSheet1->mergeCells('B'.$excel_row.':C'.$excel_row)->setCellValue("B".$excel_row , "Rep of: ".mb_strtoupper($fullname)." (".$mb->connum.") \n".$reason." ".$inactivedate);
        //         $activeSheet1->mergeCells('H'.$excel_row.':I'.$excel_row);
        //         $activeSheet1->getStyle('B'.$excel_row)->getAlignment()->setWrapText(true);
        //         $activeSheet1->getStyle('B'.$excel_row)->applyFromArray($smallfont);
        //         $activeSheet1->getRowDimension($excel_row)->setRowHeight(60);
        //         $activeSheet1->getStyle('A'.$excel_row.':J'.$excel_row)->applyFromArray($border);

        //         $number++;
        //         $start++;

        //         if ($start == 6 || $i == $numItems1 ) {     
                
        //             $excel_row++;
        //             $activeSheet1->mergeCells('B'.$excel_row.':C'.$excel_row)->setCellValue("B".$excel_row,"Total");
        //             $activeSheet1->mergeCells('H'.$excel_row.':I'.$excel_row);
        //             $activeSheet1->getStyle('A'.$excel_row.':J'.$excel_row)->applyFromArray($border);
        //             // $excel_row++;
        //             $mergeto = $excel_row+4;
        //             $headerfoot = $excel_row;
        //             $excel_row++;
        //             $activeSheet1->mergeCells('A'.$excel_row.':B'.$mergeto);
        //             $activeSheet1->setCellValue('A'.$excel_row,'I hereby certify that each person whose name appears on this roll is entitled to cash assistance.');
        //             $activeSheet1->getStyle('A'.$excel_row)->applyFromArray($headersyle);
        //             $activeSheet1->getStyle('A'.$excel_row)->getAlignment()->setWrapText(true);

        //             // $excel_row++;
        //             $activeSheet1->mergeCells('C'.$excel_row.':D'.$mergeto);
        //             $activeSheet1->setCellValue('C'.$excel_row,'A P P R O V E D  F O R  P A Y M E N T');
        //             $activeSheet1->getStyle('C'.$excel_row)->applyFromArray($headersyle);
        //             $activeSheet1->getStyle('C'.$excel_row)->getAlignment()->setWrapText(true);
        //             $activeSheet1->mergeCells('C'.$excel_row.':E'.$excel_row);

        //             // $excel_row--;
        //             $activeSheet1->mergeCells('E'.$excel_row.':H'.$mergeto);
        //             $activeSheet1->setCellValue('E'.$excel_row,'I certify on my official oath that I have paid in cash to each person whose name appears on the payroll the amount set opposite his/her name, he/she having presented himself, established identity, affixed his signature or thumb mark in the space provided therefore.');
        //             // $activeSheet1->mergeCells('F'.$excel_row.':H'.$mergeto);
        //             $activeSheet1->getStyle('E'.$excel_row)->applyFromArray($headersyle);
        //             $activeSheet1->getStyle('E'.$excel_row.':H'.$mergeto)->getAlignment()->setWrapText(true);

        //             $activeSheet1->mergeCells('I'.$excel_row.':J'.$mergeto);
        //             $activeSheet1->setCellValue('I'.$excel_row,'Witnessed by:');
        //             $activeSheet1->getStyle('I'.$excel_row)->applyFromArray($headersyle);
        //             $activeSheet1->getStyle('I'.$excel_row)->getAlignment()->setWrapText(true);

        //             $excel_row++;

        //             $signatories = getSignatories("sign1_name, sign1_position, sign2_name, sign2_position",array('file'=>"CAP"),"","row");
        //             $sign1_name = $signatories->sign1_name;
        //             $sign1_position = $signatories->sign1_position;
        //             $sign2_name = $signatories->sign2_name;
        //             $sign2_position = $signatories->sign2_position;

        //             $excel_row = $excel_row + 5;
        //             $footerstart = $excel_row;
        //             $activeSheet1->setCellValue('A'.$excel_row,mb_strtoupper($sign1_name));
        //             $activeSheet1->getStyle('A'.$excel_row)->getFont()->setUnderline(true)->setBold( true );
        //             $activeSheet1->mergeCells('A'.$excel_row.':B'.$excel_row);
        //             $activeSheet1->getStyle('A'.$excel_row.':B'.$excel_row)->applyFromArray($headersyle);

        //             $activeSheet1->setCellValue('C'.$excel_row,mb_strtoupper($sign2_name));
        //             $activeSheet1->getStyle('C'.$excel_row)->getFont()->setUnderline(true)->setBold( true );
        //             $activeSheet1->mergeCells('C'.$excel_row.':D'.$excel_row);
        //             $activeSheet1->getStyle('C'.$excel_row.':D'.$excel_row)->applyFromArray($headersyle);

        //             $activeSheet1->setCellValue('E'.$excel_row,'__________________________________________________');
        //             $activeSheet1->mergeCells('E'.$excel_row.':H'.$excel_row);
        //             $activeSheet1->getStyle('E'.$excel_row.':H'.$excel_row)->applyFromArray($headersyle);

        //             $activeSheet1->setCellValue('I'.$excel_row,'______________________');
        //             $activeSheet1->mergeCells('I'.$excel_row.':J'.$excel_row);
        //             $activeSheet1->getStyle('I'.$excel_row.':J'.$excel_row)->applyFromArray($headersyle);

        //             $excel_row++;            
        //             $activeSheet1->setCellValue('A'.$excel_row,$sign1_position);
        //             $activeSheet1->mergeCells('A'.$excel_row.':B'.$excel_row);
        //             $activeSheet1->getStyle('A'.$excel_row.':B'.$excel_row)->applyFromArray($headersyle);

        //             $activeSheet1->setCellValue('C'.$excel_row,$sign2_position);
        //             $activeSheet1->getStyle('C'.$excel_row.':D'.$excel_row)->applyFromArray($headersyle);

        //             $activeSheet1->mergeCells('E'.$excel_row.':H'.$excel_row);
        //             $activeSheet1->setCellValue('E'.$excel_row,"PAYMASTER");
        //             $activeSheet1->mergeCells('I'.$excel_row.':J'.$excel_row);
        //             $activeSheet1->setCellValue('I'.$excel_row,"MSWDO/FOCAL/OSCA");
        //             $activeSheet1->getStyle('E'.$excel_row.':J'.$excel_row)->applyFromArray($headersyle);

        //             $start = 1;
        //             $excel_row=$excel_row+1;

        //             $activeSheet1->setBreak('A'. $excel_row, \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet::BREAK_ROW);
        //             $activeSheet1->getStyle('A'.$headerfoot.":J".$excel_row)->applyFromArray($footerstyle);
        //             $start_amount =0;
        //             $total_page = $totalpage/10;
        //             $page++;

        //             if ($page == $totalpage) {
        //                 $page =1;
        //             }
        //         }
        //         $excel_row++;
        //         $i++;
        //     }
        //     $activeSheet1->getPageSetup()->setPrintArea('A:J');
        //     $activeSheet1->getHeaderFooter()->setOddFooter('&R page &P of &N');
        //     $activeSheet1->setShowGridlines(true);
        // }

        // //2nd sheet ends here

        $object->setActiveSheetIndex(0);
        $activeSheet->setSelectedCell('A1');
        if(!empty($countrepcap)){
        $totalcount=$count_datas+$countrepcap; }
        else { $totalcount=$count_datas; }

        $filename = $municipalityname;
        $filename = "CAP_".$filename."_(".$totalcount.")_".date("Y-m-d")."_".$generatesemqtr.".xlsx";
        $writer = new Xlsx($object);

        $writer->setPreCalculateFormulas(false);

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'. $filename); 
        header('Cache-Control: max-age=0');

        $writer->save('php://output');

        userLogs(sesdata('id') , sesdata('fullname') , "EXPORT", "Export Payroll: $municipalityname");

    }

// END - GENERATE CASH ASSISTANCE PAYROLL

// START - EXPORT MASTERLIST (CERTIFICATE OF ELIGIBILITY)
    public function exportMasterlist(){
        $amount = $this->input->get('amount');
        $prov_code = $this->input->get('prov_code');
        $mun_code = $this->input->get('mun_code');
        $year = $this->input->get('year');
        $payment_status = $this->input->get('liquidation');
        $ce_type = $this->input->get('type');
        $qtrsem = 1;
        $modeofpayment = "";
        $period_name = $year;

        $period = $this->input->get('period');
        if(!empty($period)){
            if(in_array($period, [5,6])){
                $modeofpayment = "Semester";
                $qtrsem = ($period == 5)?1:2;
                if($qtrsem == 1){ $period_name = "1stSemester_" . $year; }
                else if($qtrsem == 2){ $period_name = "2ndSemester_" . $year;}
            }else{
                $qtrsem = $period;
                $modeofpayment = "Quarter";
                if($qtrsem == 1){ $period_name = "1stQuarter_" . $year; }
                else if($qtrsem == 2){ $period_name = "2ndQuarter_" . $year;}
                else if($qtrsem == 3){ $period_name = "3rdQuarter_" . $year;}
                else if($qtrsem == 4){ $period_name = "4thQuarter_" . $year;}
            }
        }
        
        $generalCon = [ "sp_status<>" => "Inactive"];
        if($prov_code != ""){ $generalCon["province"] = $prov_code;}
        if($mun_code != ""){ $generalCon["city"] = $mun_code; }

        if($ce_type != "all"){
            if($ce_type != "0"){
                $generalCon["additional"] = $year;
                if($ce_type != "3"){
                    $generalCon["batch_no"] = $ce_type;
                }
                $generalCon["additional"] = $year;
            }else{
                $generalCon["additional <> "] = $year;
            }
        }

        $select = "connum, lastname, firstname,middlename, extensionname, sp_status,inactive_reason_id,sp_inactive_remarks, province, city, barangay, birthdate,remarks, representativeName1,representativeRelationship1, representativeName2, representativeRelationship2, representativeName3, representativeRelationship3,replacer";
        $beneList = $this->pm->get_all_general($select,$generalCon);
        $activeList = [];
        $forrepList = [];

        if(!empty($beneList)){
            $provinces = $this->Main->get_all_provinces();
            $prov_names = array_column($provinces, 'prov_name','prov_code');
            $municipalities = $this->Main->get_all_municipalities(["prov_code" => $prov_code]);
            $mun_names = array_column($municipalities, 'mun_name','mun_code');
            $bar_con["prov_code"] =$prov_code;
            if($mun_code != ""){ $bar_con["mun_code"] = $mun_code;}
            $barList = $this->Main->getBarangays($bar_con);
            $bar_names =  array_column($barList, 'bar_name', 'bar_code');

            $relationships = $this->Main->getrelationships();
            $relation_names =  array_column($relationships, 'relname', 'relid');

            $reasonforrep = $this->Main->getreasonforrep();
            $reason_names =  array_column($reasonforrep, 'name', 'id');

            foreach($beneList as $key => $value){

                // if($value['inactive_reason_id'] == 16){
                //     continue;
                // }

                $sp_status = $value['sp_status'];
                $fullname = strtoupper($value["lastname"]) . ", " .  strtoupper($value["firstname"]) . " " . strtoupper($value["middlename"]) . " " . strtoupper($value["extensionname"]);

                $bar_name = isset($bar_names[$value['barangay']]) ? $bar_names[$value['barangay']] : "";
                $prov_name = isset($prov_names[$value['province']]) ? $prov_names[$value['province']] : "";
                $mun_name = isset($mun_names[$value['city']]) ? $mun_names[$value['city']] : "";

                $rel1 = isset($relation_names[$value['representativeRelationship1']]) ? $relation_names[$value['representativeRelationship1']] : "";
                $rel2 = isset($relation_names[$value['representativeRelationship2']]) ? $relation_names[$value['representativeRelationship2']] : "";
                $rel3 = isset($relation_names[$value['representativeRelationship3']]) ? $relation_names[$value['representativeRelationship3']] : "";

                if( strtoupper($sp_status) == "FORREPLACEMENT" || strtoupper($sp_status) == "INACTIVE"){
                    $reasonforrep="No reason set."; 
                    if(!empty($reason_names[$value['inactive_reason_id']])){
                        $reasonforrep = isset($reason_names[$value['inactive_reason_id']]) ? $reason_names[$value['inactive_reason_id']] : "";
                        if( strtoupper($reasonforrep)=="DECEASED"){
                            if(!empty($value["sp_inactive_remarks"])){
                                $reasonforrep = $reasonforrep." (".date_format(new DateTime($value["sp_inactive_remarks"]),"Y-m-d").")";
                            }
                        }else{
                            if(!empty($ml->sp_inactive_remarks)){
                                $reasonforrep = $reasonforrep." (".$value["sp_inactive_remarks"].")";
                            }
                        }
                    }
                    
                    $forrepList[] = array(
                        "spid" => $value["connum"],
                        "fullname" => $fullname  . "*",
                        "province" => $prov_name,
                        "municipality" => $mun_name,
                        "barangay" => $bar_name,
                        "amount" => $amount,
                        "birthdate" => $value["birthdate"],
                        "remarks" => $reasonforrep,
                        "rep1" => $value["representativeName1"] . " / " . $rel1,
                        "rep2" => $value["representativeName2"] . " / " . $rel2,
                        "rep3" => $value["representativeName3"]. " / " . $rel3,
                    );
                }else{

                    
                    // if($value['replacer']==1){
                    //     $fullname .= "(NEW)";
                    // }
                    
                    $activeList[] = array(
                        "spid" => $value["connum"],
                        "fullname" => $fullname,
                        "province" => $prov_name,
                        "municipality" => $mun_name,
                        "barangay" => $bar_name,
                        "amount" => $amount,
                        "birthdate" => $value["birthdate"],
                        "remarks" =>  $value["remarks"],
                        "rep1" => $value["representativeName1"] . " / " . $rel1,
                        "rep2" => $value["representativeName2"] . " / " . $rel2,
                        "rep3" => $value["representativeName3"]. " / " . $rel3,
                    );
                }
            }
            //Arrange Active List
            if(!empty($activeList)){
                if(!empty($mun_code)){
                    array_multisort(array_column($activeList, 'barangay'), SORT_ASC, array_column($activeList, 'fullname'), SORT_ASC, $activeList);
                }else{
                    array_multisort(array_column($activeList, 'municipality'),array_column($activeList, 'barangay'), SORT_ASC, array_column($activeList, 'fullname'), SORT_ASC, $activeList);
                }
            }
            
            //Arrange Forrep List
            if(!empty($forrepList)){
                if(!empty($mun_code)){
                    array_multisort(array_column($forrepList, 'barangay'), SORT_ASC, array_column($forrepList, 'fullname'), SORT_ASC, $forrepList);
                }else{
                    array_multisort(array_column($forrepList, 'municipality'),array_column($forrepList, 'barangay'), SORT_ASC, array_column($forrepList, 'fullname'), SORT_ASC, $forrepList);
                }
            }
        }


        $prov_name = isset($prov_names[$prov_code]) ? $prov_names[$prov_code] : "";
        $mun_name = isset($mun_names[$mun_code]) ? $mun_names[$mun_code] : "";

        $fileN = "CE_" . $prov_name . "_" . $mun_name ."_" . $period_name;

        //pdie("$fileN,$prov_name,$mun_name,$year,$modeofpayment,$qtrsem,",1);

        $this->excelMasterlist($fileN,$prov_name,$mun_name,$year,$modeofpayment,$qtrsem,$activeList,$forrepList);
    }

    public function exportUnpaidMasterlist(){
        $amount = $this->input->get('amount');
        $prov_code = $this->input->get('prov_code');
        $mun_code = $this->input->get('mun_code');
        $year = $this->input->get('year');
        $payment_status = $this->input->get('liquidation');
        $ce_type = $this->input->get('type');
        $qtrsem = 1;
        $modeofpayment = "";
        $period_name = $year;

        $period = $this->input->get('period');
        if(in_array($period, [5,6])){
            $modeofpayment = "Semester";
            $qtrsem = ($period == 5)?1:2;
            if($qtrsem == 1){ $period_name = "1stSemester_" . $year; }
            else if($qtrsem == 2){ $period_name = "2ndSemester_" . $year;}
        }else{
            $qtrsem = $period;
            $modeofpayment = "Quarter";
            if($qtrsem == 1){ $period_name = "1stQuarter_" . $year; }
            else if($qtrsem == 2){ $period_name = "2ndQuarter_" . $year;}
            else if($qtrsem == 3){ $period_name = "3rdQuarter_" . $year;}
            else if($qtrsem == 4){ $period_name = "4thQuarter_" . $year;}
        }
        $unpaidCon = array(
            "prov_code" => $prov_code,
            "mun_code" => $mun_code,
            "year" => $year,
            "mode_of_payment" => $modeofpayment,
            "period" => $qtrsem,
            "liquidation" => $payment_status,
        );
        
        if($ce_type != "all"){
            $unpaidCon["additional"] = $ce_type;
        }

        $unpaidList = $this->pm->get_payroll($unpaidCon);

        $activeList = [];
        $forrepList = [];

        $spids = array_column($unpaidList, 'spid');
    
        $provinces = $this->Main->get_all_provinces();
        $prov_names = array_column($provinces, 'prov_name','prov_code');

        $municipalities = $this->Main->get_all_municipalities(["prov_code" => $prov_code]);
        $mun_names = array_column($municipalities, 'mun_name','mun_code');
        if(!empty($unpaidList)){    

            $bar_con["prov_code"] =$prov_code;
            if($mun_code != ""){ $bar_con["mun_code"] = $mun_code;}
            $barList = $this->Main->getBarangays($bar_con);
            $bar_names =  array_column($barList, 'bar_name', 'bar_code');

            $relationships = $this->Main->getrelationships();
            $relation_names =  array_column($relationships, 'relname', 'relid');

            $reasonforrep = $this->Main->getreasonforrep();
            $reason_names =  array_column($reasonforrep, 'name', 'id');
            
            //$generalCon = [ "sp_status<>" => "Inactive"];
            if($prov_code != ""){ $generalCon["province"] = $prov_code;}
            if($mun_code != ""){ $generalCon["city"] = $mun_code; }
            $select = "connum,lastname, firstname,middlename, extensionname, sp_status,inactive_reason_id,sp_inactive_remarks, province, city, barangay, birthdate,remarks, representativeName1,representativeRelationship1, representativeName2, representativeRelationship2, representativeName3, representativeRelationship3";
            $beneList = $this->pm->get_all_general($select,$generalCon);

            foreach($beneList as $key => $value){

                if(!in_array($value['connum'], $spids)){
                    continue;
                }

                $sp_status = $value['sp_status'];
                $fullname = strtoupper($value["lastname"]) . ", " .  strtoupper($value["firstname"]) . " " . strtoupper($value["middlename"]) . " " . strtoupper($value["extensionname"]);

                $bar_name = isset($bar_names[$value['barangay']]) ? $bar_names[$value['barangay']] : "";
                $prov_name = isset($prov_names[$value['province']]) ? $prov_names[$value['province']] : "";
                $mun_name = isset($mun_names[$value['city']]) ? $mun_names[$value['city']] : "";

                $rel1 = isset($relation_names[$value['representativeRelationship1']]) ? $relation_names[$value['representativeRelationship1']] : "";
                $rel2 = isset($relation_names[$value['representativeRelationship2']]) ? $relation_names[$value['representativeRelationship2']] : "";
                $rel3 = isset($relation_names[$value['representativeRelationship3']]) ? $relation_names[$value['representativeRelationship3']] : "";

                if( strtoupper($sp_status) == "FORREPLACEMENT"){
                    $reasonforrep="No reason set."; 
                    if(!empty($reason_names[$value['inactive_reason_id']])){
                        $reasonforrep = isset($reason_names[$value['inactive_reason_id']]) ? $reason_names[$value['inactive_reason_id']] : "";
                        if( strtoupper($reasonforrep)=="DECEASED"){
                            if(!empty($value["sp_inactive_remarks"])){
                                $reasonforrep = $reasonforrep." (".date_format(new DateTime($value["sp_inactive_remarks"]),"Y-m-d").")";
                            }
                        }else{
                            if(!empty($ml->sp_inactive_remarks)){
                                $reasonforrep = $reasonforrep." (".$value["sp_inactive_remarks"].")";
                            }
                        }
                    }
                    
                    $forrepList[] = array(
                        "spid" => $value["connum"],
                        "fullname" => $fullname  . "*",
                        "province" => $prov_name,
                        "municipality" => $mun_name,
                        "barangay" => $bar_name,
                        "amount" => $amount,
                        "birthdate" => $value["birthdate"],
                        "remarks" => $reasonforrep,
                        "rep1" => $value["representativeName1"] . " / " . $rel1,
                        "rep2" => $value["representativeName2"] . " / " . $rel2,
                        "rep3" => $value["representativeName3"]. " / " . $rel3,
                    );
                }else{
                    $remarks = $value["remarks"];
                    if( strtoupper($sp_status) == "INACTIVE"){
                        $remarks = "INACTIVE" . "-" . $value["remarks"];
                    }
                    $activeList[] = array(
                        "spid" => $value["connum"],
                        "fullname" => $fullname,
                        "province" => $prov_name,
                        "municipality" => $mun_name,
                        "barangay" => $bar_name,
                        "amount" => $amount,
                        "birthdate" => $value["birthdate"],
                        "remarks" =>  $remarks,
                        "rep1" => $value["representativeName1"] . " / " . $rel1,
                        "rep2" => $value["representativeName2"] . " / " . $rel2,
                        "rep3" => $value["representativeName3"]. " / " . $rel3,
                    );
                }
            }
            //Arrange Active List
            if(!empty($activeList)){
                if(!empty($mun_code)){
                    array_multisort(array_column($activeList, 'barangay'), SORT_ASC, array_column($activeList, 'fullname'), SORT_ASC, $activeList);
                }else{
                    array_multisort(array_column($activeList, 'municipality'),array_column($activeList, 'barangay'), SORT_ASC, array_column($activeList, 'fullname'), SORT_ASC, $activeList);
                }
            }
            
            //Arrange Forrep List
            if(!empty($forrepList)){
                if(!empty($mun_code)){
                    array_multisort(array_column($forrepList, 'barangay'), SORT_ASC, array_column($forrepList, 'fullname'), SORT_ASC, $forrepList);
                }else{
                    array_multisort(array_column($forrepList, 'municipality'),array_column($forrepList, 'barangay'), SORT_ASC, array_column($forrepList, 'fullname'), SORT_ASC, $forrepList);
                }
            }
        }

        $prov_name = isset($prov_names[$prov_code]) ? $prov_names[$prov_code] : "";
        $mun_name = isset($mun_names[$mun_code]) ? $mun_names[$mun_code] : "";

        $fileN = "UNPAIDCE_" . $prov_name . "_" . $mun_name ."_" . $period_name;

        $this->excelMasterlist($fileN,$prov_name,$mun_name,$year,$modeofpayment,$qtrsem,$activeList,$forrepList);
    }

    public function excelMasterlist($fileN="",$provincename="",$municipalityname="",$input_year="",$modeofpayment="",$input_qtrsem="",$memberlist=array(),$forreplist=array(),$count_datas = 0){
        ini_set('memory_limit', '999M');
        ignore_user_abort(true);

        if($count_datas == 0){
            $count_datas = 0;
            if(!empty($memberlist)){ $count_datas = count($memberlist);}
            if(!empty($forreplist)){ $count_datas = $count_datas + count($forreplist); }
        }

        if(empty($municipalityname)){ $municipalityname = "All";}
        
        if($modeofpayment=="Quarter"){
            $amount = 1500; $amountwords = "One Thousand Five Hundred";
            if($input_qtrsem==1){ $selected_qtrsem="1st quarter"; $headermonth = "January to March"; }
            else if($input_qtrsem==2){ $selected_qtrsem="2nd quarter"; $headermonth = "April to June"; }
            else if($input_qtrsem==3){ $selected_qtrsem="3rd quarter"; $headermonth = "July to September"; }
            else if($input_qtrsem==4){ $selected_qtrsem="4th quarter"; $headermonth = "October to December"; }
        }else if($modeofpayment=="Semester"){
            $amount = 3000; $amountwords = "Three Thousand";
            if($input_qtrsem==1){ $selected_qtrsem="1st semester"; $headermonth = "January to June"; }
            else if($input_qtrsem==2){ $selected_qtrsem="2nd semester"; $headermonth = "July to December"; }
        } else {
            $amount = 6000; 
            $amountwords = "Six Thousand";
            $selected_qtrsem="1st Semester and 2nd Semester";
            $headermonth = "January to December";
        }
    
        if($count_datas>0){
            
            $object = new Spreadsheet();
            $object->createSheet(0);
            $object->setActiveSheetIndex(0);
            $activeSheet =$object->getActiveSheet();
            $activeSheet->setTitle("$municipalityname");
    
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
            for($excel_row=1; $excel_row<=6; $excel_row++){ $activeSheet->getStyle('A'.$excel_row)->getFont()->setBold(true); }
            $activeSheet->mergeCells('A1:F1')->setCellValue('A1','PROVINCE OF '.mb_strtoupper($provincename));
            $activeSheet->mergeCells('A2:F2')->setCellValue('A2','MUNICIPALITY OF '.mb_strtoupper($municipalityname));
            $activeSheet->mergeCells('A3:F3')->setCellValue('A3','SOCIAL PENSION FOR INDIGENT SENIOR CITIZENS');
            $activeSheet->mergeCells('A5:F5')->setCellValue('A5','CERTIFICATE OF ELIGIBILITY');
            $activeSheet->getStyle('A1:G5')->applyFromArray($headerstyle);
            $excel_row++;
            $activeSheet->mergeCells('A'.$excel_row.':F'.$excel_row)->setCellValue('A'.$excel_row,'       This is to certify that the following indigent senior citizens are qualified as per RA 9994 and Administrative Order No. 4, series of 2014 amending the guidelines on the qualifications of social pension program beneficiaries “ based on the assessment of the LGU social worker that the senior citizen is not receiving any pension (SSS, GSIS or other insurance company), without regular source of income, compensation or financial assistance from his/her relatives to support his/her basic needs,” and eligible for Social Pension stipend in the amount of ' . $amountwords . ' (₱ '.number_format($amount,2).') each covering the period ' . $selected_qtrsem . ' (' . $headermonth . ') '. $input_year . '.');
            $activeSheet->getStyle('A'.$excel_row.':F'.$excel_row)->getAlignment()->setWrapText(true);
            $activeSheet->getStyle('A'.$excel_row.':F'.$excel_row)->applyFromArray($textleft);
            $activeSheet->getRowDimension($excel_row)->setRowHeight(100);
    
            $activeSheet->getColumnDimension('A')->setWidth(6); //no
            $activeSheet->getColumnDimension('B')->setWidth(22); //spid
            $activeSheet->getColumnDimension('C')->setWidth(35); //name
            $activeSheet->getColumnDimension('D')->setWidth(20); //barangay
            $activeSheet->getColumnDimension('E')->setWidth(20); //barangay
            $activeSheet->getColumnDimension('F')->setWidth(15); //amount
            $activeSheet->getColumnDimension('G')->setWidth(23); //Birthdate
            $activeSheet->getColumnDimension('H')->setWidth(25); //remarks
            $activeSheet->getColumnDimension('I')->setWidth(40); //rep 1
            $activeSheet->getColumnDimension('J')->setWidth(30); //rep 2
            $activeSheet->getColumnDimension('K')->setWidth(30); //rep 3
    
            $excel_row++;
            $table_columns = array("NO.", "SPID #", "NAME", "BARANGAY", "MUNICIPALITY", "AMOUNT", "BIRTHDATE", "REMARKS", "REPRESENTATIVE 1", "REPRESENTATIVE 2", "REPRESENTATIVE 3");
            $hs = "A";
            foreach ($table_columns as $tv) { 
                $activeSheet->setCellValue($hs.$excel_row,$tv); $hs++; 
                $activeSheet->getStyle('A'.$excel_row.':K'.$excel_row)->applyFromArray($headerstyleborder);
                $activeSheet->getStyle('A'.$excel_row.':K'.$excel_row)->getFont()->setBold( true );
            }
            $excel_row++;
            $number = 1;
            $total_amount = 0;
            // pdie($memberlist,1);
            if(!empty($memberlist)){
                foreach($memberlist as $ml){
                    $activeSheet->setCellValue("A".$excel_row , (string)$number);
                    $activeSheet->setCellValue("B".$excel_row , $ml["spid"]);
                    $activeSheet->setCellValue("C".$excel_row , $ml["fullname"]);
                    $activeSheet->setCellValue("D".$excel_row , $ml["barangay"]);
                    $activeSheet->setCellValue("E".$excel_row , $ml["municipality"]);
                    $activeSheet->setCellValue("F".$excel_row , "₱ ".number_format($amount,2)."\t");
                    $activeSheet->setCellValue("G".$excel_row , $ml["birthdate"]);
                    $activeSheet->setCellValue("H".$excel_row , $ml["remarks"]);
                    $activeSheet->setCellValue("I".$excel_row , $ml["rep1"]);
                    $activeSheet->setCellValue("J".$excel_row , $ml["rep2"]);
                    $activeSheet->setCellValue("K".$excel_row , $ml["rep3"]);
                    
    
                    $activeSheet->getRowDimension($excel_row)->setRowHeight(16);
                    $activeSheet->getStyle('A'.$excel_row.':B'.$excel_row)->applyFromArray($textcenter);
                    $activeSheet->getStyle('C'.$excel_row)->applyFromArray($textleft);
                    $activeSheet->getStyle('D'.$excel_row.':K'.$excel_row)->applyFromArray($textcenter);
                    $activeSheet->getStyle('A'.$excel_row.':K'.$excel_row)->applyFromArray($border);
                    
                    if(strpos($ml["fullname"], "(NEW)") !== false){
                        $activeSheet->getStyle('A'.$excel_row.':G'.$excel_row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('a8f7f7');
                    }

                    $total_amount += $amount;
                    $number++;
                    $excel_row++;
                }
            }
    
            if(!empty($forreplist)){
                foreach($forreplist as $ml){
                    
                    $activeSheet->setCellValue("A".$excel_row , (string)$number);
                    $activeSheet->setCellValue("B".$excel_row , $ml["spid"]);
                    $activeSheet->setCellValue("C".$excel_row , $ml["fullname"]);
                    $activeSheet->setCellValue("D".$excel_row , $ml["barangay"]);
                    $activeSheet->setCellValue("E".$excel_row , $ml["municipality"]);
                    $activeSheet->setCellValue("F".$excel_row , "₱ ".number_format($amount,2)."\t");
                    $activeSheet->setCellValue("G".$excel_row , $ml["birthdate"]);
                    $activeSheet->setCellValue("H".$excel_row , $ml["remarks"]);
                    $activeSheet->setCellValue("I".$excel_row , $ml["rep1"]);
                    $activeSheet->setCellValue("J".$excel_row , $ml["rep2"]);
                    $activeSheet->setCellValue("K".$excel_row , $ml["rep3"]);

                    $activeSheet->getRowDimension($excel_row)->setRowHeight(16);
                    $activeSheet->getStyle('A'.$excel_row.':B'.$excel_row)->applyFromArray($textcenter);
                    $activeSheet->getStyle('C'.$excel_row)->applyFromArray($textleft);
                    $activeSheet->getStyle('D'.$excel_row.':K'.$excel_row)->applyFromArray($textcenter);
                    $activeSheet->getStyle('A'.$excel_row.':K'.$excel_row)->applyFromArray($border);
                    $total_amount += $amount;
                    $number++;
                    $excel_row++;
                }
            }
            
            $activeSheet->setCellValue("A".$excel_row , "* For replacement");
            $activeSheet->getStyle('A'.$excel_row)->getFont()->setItalic( true )->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
            $activeSheet->setCellValue("E".$excel_row , "TOTAL");
            $activeSheet->getStyle('E'.$excel_row.':F'.$excel_row)->getFont()->setBold( true );
            $activeSheet->setCellValue("F".$excel_row , "₱ ".number_format($total_amount,2)."\t");
            $activeSheet->getStyle('F'.$excel_row)->applyFromArray($textcenter);
            $activeSheet->getStyle('F'.$excel_row)->applyFromArray($border);
            // $excel_row=$excel_row+3;
    
            // //footer
    
            $excel_row=$excel_row+3;
    
            $uptomerge = $excel_row+1;
            $activeSheet->mergeCells('B'.$excel_row.':E'.$uptomerge)->setCellValue('B'.$excel_row,'            I hereby certify that each person whose name appears on above Certificate of Eligibility is entitled to SOCIAL PENSION PROGRAM stipend.');
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
            $filename = $fileN."_(".$count_datas.")_".date("Y-m-d").".xlsx";
            
            $writer = new Xlsx($object);
    
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'. $filename); 
            header('Cache-Control: max-age=0');
    
            $writer->save('php://output');
    
            userLogs(sesdata('id') , sesdata('fullname') , "EXPORT", "Export Certificate of Eligibility Masterlist: $filename");
    
        }else{
            show_404("NO RECORDS FOUND"); 
        }
    }

// END - EXPORT MASTERLIST (CERTIFICATE OF ELIGIBILITY)

// START - EXPORT UNPAID MASTERLIST (CERTIFICATE OF ELIGIBILITY) and UNPAID CASH ASSISTANCE PAYROLL (CAP)
    public function exportReplacement(){
		$prov_code = $this->input->get('prov_code');
		$mun_code = "";
		$bar_code = "";
		$year = $this->input->get('year');
        $period_condition = 1;
		$type_sem_quart = "Semester";
        $ce_type = $this->input->get('type');
		
        if($this->input->get('period') !== null && $this->input->get('period') != "")
        {
            $period = $this->input->get('period');
            if(in_array($period, [5,6])){
                $type_sem_quart = "Semester";
                $period_condition = ($period == 5)?1:2;
            }
            else{
                $period_condition = $period;
                $type_sem_quart = "quarter";
            }
		}		
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
		$additional_years = array_column($generalList, 'additional', 'connum');

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
            "mode_of_payment" => $type_sem_quart,
            "period" => $period_condition,
            "liquidation" => 0,
        );

        // if($ce_type != "all"){
        //     if($ce_type == "3"){
        //         $unpaidCon["additional"] = [1,2];
        //     }else{
        //         $unpaidCon["additional"] = $ce_type;
        //     }
        // }

        if($ce_type != "all"){
            if($ce_type != "0"){
                $unpaidCon["additional"] = $year;
                if($ce_type != "3"){
                    $unpaidCon["additional"] = $ce_type;
                }
                $unpaidCon["additional"] = [1,2];
            }else{
                $unpaidCon["additional <> "] = $year;
            }
        }
        if($mun_code != ""){
            $unpaidCon["mun_code"] = $mun_code;
        }
        $unpaidList = $this->pm->get_payroll($unpaidCon);

		$replacements = $this->pm->get_all_replacement();
		$rep_list =  array_column($replacements, 'replacer', 'replacee');

        $activeList = [];
        $inactive_list = [];

        foreach ($unpaidList as $key => $value) {

            // if($value["eligible"] != 1 && $value["eligible"] != 2){
            //     continue;
            // }
            $spid = $value["spid"];
            $amount = $value["amount"];
            

            $prov_name = isset($prov_names[$value["prov_code"]])?$prov_names[$value["prov_code"]] : "";
            $mun_name = isset($mun_names[$value["mun_code"]])?$mun_names[$value["mun_code"]] : "";
            $bar_name = isset($bar_names[$value["bar_code"]])?$bar_names[$value["bar_code"]] : "";
            
            $add_year = isset($additional_years[$spid]) ? $additional_years[$spid] : "";
			$fullname = isset($fullnameList[$spid]) ? $fullnameList[$spid] : "Not Found";
            $birthdate = isset($birthdates[$spid]) ? $birthdates[$spid] : "";
            $sp_inactive_remarks = isset($reasons[$spid]) ? $reasons[$spid] : "";
            $inactive_reason_id = isset($reason_ids[$spid]) ? $reason_ids[$spid] : "";

            $remark = "";
            if($add_year > 0){
                $remark = "$add_year Additional. \r\n";
            }
            $remark = isset($remarklist[$spid]) ? $remarklist[$spid] : "";

            
            $spstat = isset($sp_status[$spid]) ? $sp_status[$spid] : "";
            
            if( strtoupper($spstat) == "INACTIVE" ||  strtoupper($spstat) == "FORREPLACEMENT"){
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

                $f_name = $fullname . " * (" . $reasonforrep . ")";
                $replacer_spid = "";
                $replacer_fullname = "";
                $replacer_birthdate = "";

                if(strtoupper($spstat) == "INACTIVE"){
                    $replacer_spid = isset($rep_list[$spid]) ? $rep_list[$spid] : "";
                    $replacer_fullname = isset($fullnameList[$replacer_spid]) ? $fullnameList[$replacer_spid] : "";
                    $replacer_birthdate = isset($birthdates[$replacer_spid]) ? $birthdates[$replacer_spid] : "";
                    $spid .= "\r\n" . $replacer_spid;
                    $f_name .= "\r\n" . "Replacer: " . $replacer_fullname;
                    $remark = "Replacer Birthdate: $replacer_birthdate" . "\r\n" . $remark;
                }


                // if($year == "2019"){
                //     $spid .= "\r\n" . $replacer_spid;
                //     $f_name .= "\r\n" . "Replacer: " . $replacer_fullname;
                //     $remark = "Replacer Birthdate: $replacer_birthdate" . "\r\n" . $remark;
                // }else{
                //     if($value["eligible"] > 1){
                //         $spid .= "\r\n" . $replacer_spid;
                //         $f_name .= "\r\n" . "Replacer: " . $replacer_fullname;
                //         $remark = "Replacer Birthdate: $replacer_birthdate" . "\r\n" . $remark;
                //     }
                // }

                
                $inactive_list[] = array(
                    "spid" => $spid,
                    "fullname" => $f_name,
                    "province" => $prov_name,
                    "municipality" => $mun_name,
                    "barangay" => $bar_name,
                    "amount" => $amount,
                    "birthdate" => $birthdate,
                    "remarks" => $remark,
                );
            }else{
                $activeList[] = array(
                    "spid" => $spid,
                    "fullname" => $fullname,
                    "province" => $prov_name,
                    "municipality" => $mun_name,
                    "barangay" => $bar_name,
                    "amount" => $amount,
                    "birthdate" => $birthdate,
                    "remarks" =>  $remark,
                );
            }

        }

		array_multisort(array_column($activeList, 'municipality'), SORT_ASC,array_column($activeList, 'barangay'), SORT_ASC,array_column($activeList, 'fullname'), SORT_ASC, $activeList);
		array_multisort(array_column($inactive_list, 'municipality'), SORT_ASC,array_column($inactive_list, 'barangay'), SORT_ASC,array_column($inactive_list, 'fullname'), SORT_ASC, $inactive_list);

        
        if(!empty($activeList) || !empty($inactive_list)){

            $amount_text = 6000; 
            $amountwords = "Six Thousand";
            $selected_qtrsem="1st Semester and 2nd Semester";
            $headermonth = "January to December";

            if(strtoupper($type_sem_quart)=="QUARTER"){
                $amount_text = 1500; $amountwords = "One Thousand Five Hundred";
                if($period_condition==1){ $selected_qtrsem="1st quarter"; $headermonth = "January to March"; }
                else if($period_condition==2){ $selected_qtrsem="2nd quarter"; $headermonth = "April to June"; }
                else if($period_condition==3){ $selected_qtrsem="3rd quarter"; $headermonth = "July to September"; }
                else if($period_condition==4){ $selected_qtrsem="4th quarter"; $headermonth = "October to December"; }
            }else if(strtoupper($type_sem_quart)=="SEMESTER"){
                $amount_text = 3000; $amountwords = "Three Thousand";
                if($period_condition==1){ $selected_qtrsem="1st semester"; $headermonth = "January to June"; }
                else if($period_condition==2){ $selected_qtrsem="2nd semester"; $headermonth = "July to December"; }
            } else {
                $amount_text = 6000; 
                $amountwords = "Six Thousand";
                $selected_qtrsem="1st Semester and 2nd Semester";
                $headermonth = "January to December";
            }
            
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
            for($excel_row=1; $excel_row<=6; $excel_row++){ $activeSheet->getStyle('A'.$excel_row)->getFont()->setBold(true); }
            $activeSheet->mergeCells('A1:F1')->setCellValue('A1','PROVINCE OF '.mb_strtoupper($provincename));
            $activeSheet->mergeCells('A2:F2')->setCellValue('A2','MUNICIPALITY OF '.mb_strtoupper($municipalityname));
            $activeSheet->mergeCells('A3:F3')->setCellValue('A3','SOCIAL PENSION FOR INDIGENT SENIOR CITIZENS');
            $activeSheet->mergeCells('A5:F5')->setCellValue('A5','CERTIFICATE OF ELIGIBILITY');
            $activeSheet->getStyle('A1:G5')->applyFromArray($headerstyle);
            $excel_row++;
            $activeSheet->mergeCells('A'.$excel_row.':F'.$excel_row)->setCellValue('A'.$excel_row,'       This is to certify that the following indigent senior citizens are qualified as per RA 9994 and Administrative Order No. 4, series of 2014 amending the guidelines on the qualifications of social pension program beneficiaries “ based on the assessment of the LGU social worker that the senior citizen is not receiving any pension (SSS, GSIS or other insurance company), without regular source of income, compensation or financial assistance from his/her relatives to support his/her basic needs,” and eligible for Social Pension stipend in the amount of ' . $amountwords . ' (₱ '.number_format($amount_text,2).') each covering the period ' . $selected_qtrsem . ' (' . $headermonth . ') '. $year . '.');
            $activeSheet->getStyle('A'.$excel_row.':F'.$excel_row)->getAlignment()->setWrapText(true);
            $activeSheet->getStyle('A'.$excel_row.':F'.$excel_row)->applyFromArray($textleft);
            $activeSheet->getRowDimension($excel_row)->setRowHeight(100);

            $activeSheet->getColumnDimension('A')->setWidth(6); //no
            $activeSheet->getColumnDimension('B')->setWidth(22); //spid
            $activeSheet->getColumnDimension('C')->setWidth(35); //name
            $activeSheet->getColumnDimension('D')->setWidth(20); //barangay
            $activeSheet->getColumnDimension('E')->setWidth(20); //barangay
            $activeSheet->getColumnDimension('F')->setWidth(15); //amount
            $activeSheet->getColumnDimension('G')->setWidth(23); //Birthdate
            $activeSheet->getColumnDimension('H')->setWidth(25); //remarks

            $excel_row++;
            $table_columns = array("NO.", "SPID #", "NAME", "BARANGAY", "MUNICIPALITY", "AMOUNT", "BIRTHDATE", "REMARKS");
            $hs = "A";
            foreach ($table_columns as $tv) { 
                $activeSheet->setCellValue($hs.$excel_row,$tv); $hs++; 
                $activeSheet->getStyle('A'.$excel_row.':H'.$excel_row)->applyFromArray($headerstyleborder);
                $activeSheet->getStyle('A'.$excel_row.':H'.$excel_row)->getFont()->setBold( true );
            }
            $excel_row++;
            $number = 1;
            $total_amount = 0;
            $count_datas = 0;

            if(!empty($activeList)){
                foreach($activeList as $ml){
                    $count_datas++;
                    $activeSheet->setCellValue("A".$excel_row , (string)$number);
                    $activeSheet->setCellValue("B".$excel_row , $ml["spid"]);
                    $activeSheet->setCellValue("C".$excel_row , $ml["fullname"]);
                    $activeSheet->setCellValue("D".$excel_row , $ml["barangay"]);
                    $activeSheet->setCellValue("E".$excel_row , $ml["municipality"]);
                    $activeSheet->setCellValue("F".$excel_row , "₱ ".number_format($amount,2)."\t");
                    $activeSheet->setCellValue("G".$excel_row , $ml["birthdate"]);
                    $activeSheet->setCellValue("H".$excel_row , $ml["remarks"]);

                    $activeSheet->getRowDimension($excel_row)->setRowHeight(16);
                    $activeSheet->getStyle('A'.$excel_row.':B'.$excel_row)->applyFromArray($textcenter);
                    $activeSheet->getStyle('C'.$excel_row)->applyFromArray($textleft);
                    $activeSheet->getStyle('D'.$excel_row.':H'.$excel_row)->applyFromArray($textcenter);
                    $activeSheet->getStyle('A'.$excel_row.':H'.$excel_row)->applyFromArray($border);
                    $total_amount += $amount;
                    $number++;
                    $excel_row++;
                }
            }

            if(!empty($inactive_list)){
                foreach($inactive_list as $ml){
                    $count_datas++;
                    
                    $activeSheet->setCellValue("A".$excel_row , (string)$number);
                    $activeSheet->setCellValue("B".$excel_row , $ml["spid"]);
                    $activeSheet->setCellValue("C".$excel_row , $ml["fullname"]);
                    $activeSheet->setCellValue("D".$excel_row , $ml["barangay"]);
                    $activeSheet->setCellValue("E".$excel_row , $ml["municipality"]);
                    $activeSheet->setCellValue("F".$excel_row , "₱ ".number_format($amount,2)."\t");
                    $activeSheet->setCellValue("G".$excel_row , $ml["birthdate"]);
                    $activeSheet->setCellValue("H".$excel_row , $ml["remarks"]);

                    $activeSheet->getRowDimension($excel_row)->setRowHeight(16);
                    $activeSheet->getStyle('A'.$excel_row.':B'.$excel_row)->applyFromArray($textcenter);
                    $activeSheet->getStyle('C'.$excel_row)->applyFromArray($textleft);
                    $activeSheet->getStyle('D'.$excel_row.':H'.$excel_row)->applyFromArray($textcenter);
                    $activeSheet->getStyle('A'.$excel_row.':H'.$excel_row)->applyFromArray($border);
                    
                    $activeSheet->getStyle('B'.$excel_row.':C'.$excel_row)->getAlignment()->setWrapText(true);
                    $activeSheet->getRowDimension($excel_row)->setRowHeight(43);

                    $total_amount += $amount;
                    $number++;
                    $excel_row++;
                }
            }
            
            $activeSheet->setCellValue("A".$excel_row , "* 2 names indicates that either replacer or replacee can receive the stipend");
            $activeSheet->getStyle('A'.$excel_row)->getFont()->setItalic( true )->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);
            $activeSheet->setCellValue("E".$excel_row , "TOTAL");
            $activeSheet->getStyle('E'.$excel_row.':F'.$excel_row)->getFont()->setBold( true );
            $activeSheet->setCellValue("F".$excel_row , "₱ ".number_format($total_amount,2)."\t");
            $activeSheet->getStyle('F'.$excel_row)->applyFromArray($textcenter);
            $activeSheet->getStyle('F'.$excel_row)->applyFromArray($border);
            // $excel_row=$excel_row+3;

            // //footer

            $excel_row=$excel_row+3;

            $uptomerge = $excel_row+1;
            $activeSheet->mergeCells('B'.$excel_row.':E'.$uptomerge)->setCellValue('B'.$excel_row,'            I hereby certify that each person whose name appears on above Certificate of Eligibility is entitled to SOCIAL PENSION PROGRAM stipend.');
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

            $filename = "UNPAID_MASTERLIST" . $provincename. "_" . $municipalityname . "_(".$count_datas.")_".date("Y-m-d").".xlsx";
            
            $writer = new Xlsx($object);

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'. $filename); 
            header('Cache-Control: max-age=0');

            $writer->save('php://output');

            userLogs(sesdata('id') , sesdata('fullname') , "EXPORT", "Export Certificate of Eligibility Masterlist: $filename");

        }else{
            show_404("NO RECORDS FOUND"); 
        }
    }
    
    public function generateUnpaidCAP(){
        ini_set('memory_limit', '20000M');
        set_time_limit(0);

        $prov_code = $this->input->get('prov_code');
		$mun_code = "";
		$bar_code = "";
		$year = $this->input->get('year');
        $period_condition = 1;
		$type_sem_quart = "Semester";
        $ce_type = $this->input->get('type');
		
        if($this->input->get('period') !== null && $this->input->get('period') != "")
        {
            $period = $this->input->get('period');
            if(in_array($period, [5,6])){
                $qtrsem = ($period == 5)?1:2;
                $type_sem_quart = "Semester";
                $period_condition = ($period == 5)?1:2;
            }
            else{
                $qtrsem = $period;
                $period_condition = $period;
                $type_sem_quart = "quarter";
            }
		}		
		if($this->input->get('mun_code') !== null && $this->input->get('mun_code') != ""){
			$mun_code = $this->input->get('mun_code');
		}
		if($this->input->get('bar_code') !== null && $this->input->get('bar_code') != ""){
			$bar_code = $this->input->get('bar_code');
        }
        
        //Get Unpaids
        $unpaidCon = array(
            "prov_code" => $prov_code,
            "year" => $year,
            "mode_of_payment" => $type_sem_quart,
            "period" => $period_condition,
            "liquidation" => 0,
        );
        if($ce_type != "all"){
            if($ce_type == "3"){
                // $unpaidCon["additional <"] = 3;
                // $unpaidCon["additional >"] = 0;
                $unpaidCon["additional"] = [1,2];
            }else{
                $unpaidCon["additional"] = $ce_type;
            }
        }
        if($mun_code != ""){
            $unpaidCon["mun_code"] = $mun_code;
        }
        $unpaidList = $this->pm->get_payroll($unpaidCon);
        $unpaidspids = array_column($unpaidList, 'spid');

		//GET LIBRARIES
		$generalList = $this->rm->get_all_general();
		$remarklist = array_column($generalList, 'remarks', 'connum');
		$sp_status = array_column($generalList, 'sp_status', 'connum');
		$reason_ids = array_column($generalList, 'inactive_reason_id', 'connum');
		$reasons = array_column($generalList, 'sp_inactive_remarks', 'connum');
		$birthdates = array_column($generalList, 'birthdate', 'connum');
		$barcodes = array_column($generalList, 'barangay', 'connum');
		$muncodes = array_column($generalList, 'city', 'connum');
        //pdie($generalList,1);

		$reasons_lib = $this->Main->getLibraries("tblinactivereason");
		$reason_names = array_column($reasons_lib , 'name', 'id');

        $provinces = $this->Main->get_all_provinces();
		$prov_names = array_column($provinces, 'prov_name','prov_code');
		$municipalities = $this->Main->get_all_municipalities();
		$mun_names = array_column($municipalities, 'mun_name','mun_code');

		$bar_con["prov_code"] = $prov_code;
		if($mun_code != ""){ $bar_con["mun_code"] = $mun_code;}
		$barList = $this->Main->getBarangays($bar_con, 0, ['col' => 'bar_name', 'order_by' => 'ASC']);
		$bar_names =  array_column($barList, 'bar_name', 'bar_code');

		$fullnameList = [];
		foreach($generalList as $key => $value) {
            $fullname = $value['lastname'].", ".$value['firstname']. " " . $value['middlename']. " " . $value['extensionname'];
            // if($value['replacer']==1 && $value['year_start']=='2021' && $value['quarter_start']=='2'){
            // if($value['replacer']==1 && $value['year_start']==$year && $value['quarter_start']==$qtrsem){
            if($value['replacer']==1 && $value['year_start']==$year){
                $fullname .= "(NEW)";
            }             
			$fullnameList[$value['connum']]= $fullname;
		}
        //END GET LIBRARIES


		$replacements = $this->pm->get_all_replacement();
		$rep_list =  array_column($replacements, 'replacer', 'replacee');

        $exportList = [];
        $exportCount = 0;

        $forrepList = [];
        $forrepCount = 0;

        foreach ($unpaidList as $key => $value) {

            // if($value["eligible"] != 1 && $value["eligible"] != 2){
            //     continue;
            // }
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
            
            if( strtoupper($spstat) == "INACTIVE" || strtoupper($spstat) == "FORREPLACEMENT" ){
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

                $fullname = $fullname . " ($reasonforrep)";

                $replacer_spid = isset($rep_list[$spid]) ? $rep_list[$spid] : "";
                $replacer_fullname  = "";
                $replacer_munname   = "";
                $replacer_barname   = "";
                $replacer_birthdate = "";

                if($replacer_spid != ""){
                    $replacer_fullname  = isset($fullnameList[$replacer_spid]) ? $fullnameList[$replacer_spid] : "";
                    $replacer_birthdate = isset($birthdates[$replacer_spid]) ? $birthdates[$replacer_spid] : "";
                    $replacer_muncode   = isset($muncodes[$replacer_spid]) ? $muncodes[$replacer_spid] : "";
                    $replacer_barcode   = isset($barcodes[$replacer_spid]) ? $barcodes[$replacer_spid] : "";
    
                    $replacer_munname   = isset($mun_names[$replacer_muncode])?$mun_names[$replacer_muncode] : "";
                    $replacer_barname   = isset($bar_names[$replacer_barcode])?$bar_names[$replacer_barcode] : "";
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
                
                $forrepCount += 1;
                $forrepList[$value['bar_code']][] = array(
                    "spid" => $spid,
                    "fullname" => $fullname,
                    "province" => $prov_name,
                    "municipality" => $mun_name,
                    "barangay" => $bar_name,
                    "amount" => $amount,
                    "remarks" => $reasonforrep,
                    "replacer_fullname" => $replacer_fullname,
                    "replacer_munname" => $replacer_munname,
                    "replacer_barname" => $replacer_barname,
                    "replacer_spid"    => $replacer_spid,
                    "replacer_birthdate"    => $replacer_birthdate,
                );
            }else{
                
                $birthdate = isset($birthdates[$spid]) ? $birthdates[$spid] : "";
                $exportCount += 1;
                $exportList[$value["bar_code"]][] = array(
                    "spid" => $spid,
                    "fullname" => $fullname,
                    "province" => $prov_name,
                    "municipality" => $mun_name,
                    "barangay" => $bar_name,
                    "amount" => $amount,
                    "birthdate" => $birthdate,
                    "remarks" => $remark,
                    "birthdate" => $birthdate,
                ); 

            }
            
        }

        $exportCount = count($exportList);
        $prov_name = isset($prov_names[$prov_code]) ? $prov_names[$prov_code] : "";
        $mun_name = isset($mun_names[$mun_code]) ? $mun_names[$mun_code] : "";

        $fileTitle = "UNPAID_CAP_" . $mun_name;

        // //export
        // if ($mun_code!="141102000") {

            // //Arrange Active List
            foreach ($exportList as $key => $value) {
                array_multisort(array_column($exportList[$key], 'barangay'), SORT_ASC, array_column($exportList[$key], 'fullname'), SORT_ASC, $exportList[$key]);
            }

            //Arrange Forrep List
            foreach ($forrepList as $key => $value) {
                array_multisort(array_column($forrepList[$key], 'barangay'), SORT_ASC, array_column($forrepList[$key], 'fullname'), SORT_ASC, $forrepList[$key]);
            }

            $this->exportPayroll($fileTitle, $prov_name,$mun_name, $year,$type_sem_quart,$period_condition, $bar_names, $exportList, $forrepList,$exportCount,$forrepCount,"Unpaid");

        // }
        // else if($mun_code=="141102000"){
        //     //arrange by alphabets
        //     $this->exportPayrollBaguio($mun_code,$year,$type_sem_quart,$period_condition,"","Unpaid");
        // }

        //print_r($exportList);
      
    }
// END - EXPORT UNPAID MASTERLIST (CERTIFICATE OF ELIGIBILITY) and UNPAID CASH ASSISTANCE PAYROLL (CAP)

}
