<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once __DIR__.'/../../google/vendor/autoload.php';
define('SCOPES', implode(' ', array(
    Google_Service_Sheets::SPREADSHEETS,
    Google_Service_Sheets::DRIVE,
    Google_Service_Sheets::DRIVE_FILE) 
));
define('CLIENT_SECRET_PATH', __DIR__.'/../../google/socpen_report.json');
define('ACCESS_TOKEN', "91c9dca2a01707350ca6fef6ad32cd4baed3ca00");
define('SHEET_ID', "1dMv1uOvpNzwnlp_y4NIDV2UlRTh5e19u5TIbZNE-uZc");
//define('SHEET_ID', "1npWoV2MdS3FlWFwLsOVVfQWjbHAZQXUvX3DfiwZh32M");

class Reports extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Reports_model','rm');
        $this->load->model('Main');
    }

    public function index()
    {
        // $this->template->title('SPIS Dashboard');
		// $this->template->set_layout('default');
	    // $this->template->set_partial('header','partials/header');
	    // $this->template->set_partial('sidebar','partials/sidebar');
	    // $this->template->set_partial('aside','partials/aside');
	    // $this->template->set_partial('footer','partials/footer');
	    // $this->template->append_metadata('<script src="' . base_url("assets/js/pages/reports/total_served.js?ver=") . filemtime(FCPATH. "assets/js/pages/reports/total_served.js") . '"></script>');

	    // $this->template->build('reports/total_served');
    }

//////////// SERVED MATRIX ///////////////////////////////////////////////
    public function served()
    {
        $this->template->title('Served Beneficiaries');
		$this->template->set_layout('default');
	    $this->template->set_partial('header','partials/header');
	    $this->template->set_partial('sidebar','partials/sidebar');
	    $this->template->set_partial('aside','partials/aside');
	    $this->template->set_partial('footer','partials/footer');
	    $this->template->append_metadata('<script src="' . base_url("assets/js/pages/reports/total_served.js?ver=") . filemtime(FCPATH. "assets/js/pages/reports/total_served.js") . '"></script>');

	    $this->template->build('reports/total_served');
    }

    public function get_total_served_beneficiaries()
    {
        $year = date("Y");
        $period_condition = 1;
        $type_sem_quart = "Quarter";
        $amount = 1500;
        $month = date("n");
        $semester_target_cond = floor((intval($month) + 5) / 6);
        $period_target_cond = ceil($month / 3);

        if($this->input->post('year') !== null && $this->input->post('year') != ""){
            $year = $this->input->post('year');
        }

        if($this->input->post('period') !== null && $this->input->post('period') != ""){
            $period = $this->input->post('period');
            if(in_array($period, [5,6])){
                $type_sem_quart = "Semester";
                $period_condition = ($period == 5)?1:2;
                $amount = 3000;
            }else {
                $period_condition = $period;
                $type_sem_quart = "Quarter";
            }

            if(in_array($period, [1,2,3,4])){
                $semester_target_cond = ($period > 2)?2:1;
                $period_target_cond = $period;
            }
            else{
                $period_target_cond = 1;
                $semester_target_cond = ($period == 5)?1:2;
                $amount = 3000;
            }
        }
        
        $provinces = $this->rm->get_all_provinces();
        $municipalities = $this->rm->get_all_municipalities();

        $prov_name = array_column($provinces, 'prov_name','prov_code');
        $mun_name = array_column($municipalities, 'mun_name','mun_code');

        $condition = ["mode_of_payment"   => $type_sem_quart,
                      "period"   => $period_condition,
                      "year"      => $year,
                      "liquidation <>" => 2];
        
        $total_served = $this->rm->get_total_served($condition);
        $prov_data = [];
        $region_data = [];
        $region_count = [];

        if(!empty($total_served))
        {
            $get_all_general = $this->rm->get_all_general();
            $get_all_gender = array_column($get_all_general, 'gender','connum');

            $targets = $this->rm->get_all_targets(['quarter' => $period_target_cond, 'semester'=>$semester_target_cond,'year'=>$year,'archived'=>0]);
            $all_targets = array_column($targets, 'target','mun_code');

            $prov_count = [];
            $mun_count = [];
            $region_count = [];
            $region_targets = array_sum($all_targets);

            $region_data['target'] = number_format($region_targets);
            
            // alternative
                foreach ($total_served as $key => $value) {

                    $region_count['female'] = isset($region_count['female']) ? $region_count['female'] : 0;
                    $region_count['male'] = isset($region_count['male']) ? $region_count['male'] : 0;
                    $region_count['paidtotal'] = isset($region_count['paidtotal']) ? $region_count['paidtotal'] : 0;

                    $region_count['unpaidfemale'] = isset($region_count['unpaidfemale']) ? $region_count['unpaidfemale'] : 0;
                    $region_count['unpaidmale'] = isset($region_count['unpaidmale']) ? $region_count['unpaidmale'] : 0;
                    $region_count['unpaidtotal'] = isset($region_count['unpaidtotal']) ? $region_count['unpaidtotal'] : 0;

                    $region_count['total'] = isset($region_count['total']) ? $region_count['total'] : 0;

                    $prov_count[$value['prov_code']]['female'] = isset($prov_count[$value['prov_code']]['female']) ? $prov_count[$value['prov_code']]['female'] : 0;
                    $prov_count[$value['prov_code']]['male'] = isset($prov_count[$value['prov_code']]['male']) ? $prov_count[$value['prov_code']]['male'] : 0;
                    $prov_count[$value['prov_code']]['paidtotal'] = isset($prov_count[$value['prov_code']]['paidtotal']) ? $prov_count[$value['prov_code']]['paidtotal'] : 0;

                    $mun_count[$value['mun_code']]['female'] = isset($mun_count[$value['mun_code']]['female']) ? $mun_count[$value['mun_code']]['female'] : 0;
                    $mun_count[$value['mun_code']]['male'] = isset($mun_count[$value['mun_code']]['male']) ? $mun_count[$value['mun_code']]['male'] : 0;
                    $mun_count[$value['mun_code']]['paidtotal'] = isset($mun_count[$value['mun_code']]['paidtotal']) ? $mun_count[$value['mun_code']]['paidtotal'] : 0;

                    $prov_count[$value['prov_code']]['unpaidfemale'] = isset($prov_count[$value['prov_code']]['unpaidfemale']) ? $prov_count[$value['prov_code']]['unpaidfemale'] : 0;
                    $prov_count[$value['prov_code']]['unpaidmale'] = isset($prov_count[$value['prov_code']]['unpaidmale']) ? $prov_count[$value['prov_code']]['unpaidmale'] : 0;
                    $prov_count[$value['prov_code']]['unpaidtotal'] = isset($prov_count[$value['prov_code']]['unpaidtotal']) ? $prov_count[$value['prov_code']]['unpaidtotal'] : 0;

                    $mun_count[$value['mun_code']]['unpaidfemale'] = isset($mun_count[$value['mun_code']]['unpaidfemale']) ? $mun_count[$value['mun_code']]['unpaidfemale'] : 0;
                    $mun_count[$value['mun_code']]['unpaidmale'] = isset($mun_count[$value['mun_code']]['unpaidmale']) ? $mun_count[$value['mun_code']]['unpaidmale'] : 0;
                    $mun_count[$value['mun_code']]['unpaidtotal'] = isset($mun_count[$value['mun_code']]['unpaidtotal']) ? $mun_count[$value['mun_code']]['unpaidtotal'] : 0;

                    $prov_count[$value['prov_code']]['paidtotal'] = isset($prov_count[$value['prov_code']]['paidtotal']) ? $prov_count[$value['prov_code']]['paidtotal'] : 0;
                    $mun_count[$value['mun_code']]['paidtotal'] = isset($mun_count[$value['mun_code']]['paidtotal']) ? $mun_count[$value['mun_code']]['paidtotal'] : 0;

                    $prov_count[$value['prov_code']]['unpaidtotal'] = isset($prov_count[$value['prov_code']]['unpaidtotal']) ? $prov_count[$value['prov_code']]['unpaidtotal'] : 0;
                    $mun_count[$value['mun_code']]['unpaidtotal'] = isset($mun_count[$value['mun_code']]['unpaidtotal']) ? $mun_count[$value['mun_code']]['unpaidtotal'] : 0;

                    if(isset($get_all_gender[$value['spid']]) && strtoupper(trim($get_all_gender[$value['spid']])) == "FEMALE")
                    {
                        if($value['liquidation'] == 1){
                            $region_count['female']++;
                            $prov_count[$value['prov_code']]['female']++;
                            $mun_count[$value['mun_code']]['female']++;
                        }else{
                            $region_count['unpaidfemale']++;
                            $prov_count[$value['prov_code']]['unpaidfemale']++;
                            $mun_count[$value['mun_code']]['unpaidfemale']++;
                        }
                    }

                    if(isset($get_all_gender[$value['spid']]) && strtoupper(trim($get_all_gender[$value['spid']])) == "MALE"){
                        if($value['liquidation'] == 1){
                            $region_count['male']++;
                            $prov_count[$value['prov_code']]['male']++;
                            $mun_count[$value['mun_code']]['male']++;
                        }else{
                            $region_count['unpaidmale']++;
                            $prov_count[$value['prov_code']]['unpaidmale']++;
                            $mun_count[$value['mun_code']]['unpaidmale']++;
                        }
                    }

                    if($value['liquidation'] == 1){
                        $region_count['paidtotal']++;
                        $prov_count[$value['prov_code']]['paidtotal']++;
                        $mun_count[$value['mun_code']]['paidtotal']++;
                    }else{
                        $region_count['unpaidtotal']++;
                        $prov_count[$value['prov_code']]['unpaidtotal']++;
                        $mun_count[$value['mun_code']]['unpaidtotal']++;
                    }

                    $region_count['total']++;

                    //province count
                    if(isset($prov_count[$value['prov_code']]['total'])){ $prov_count[$value['prov_code']]['total']++;}
                    else{ $prov_count[$value['prov_code']]['total'] = 1; }
                    //province count

                    //municipality count
                    if(isset($mun_count[$value['mun_code']]['total'])){ $mun_count[$value['mun_code']]['total']++;
                    }else{ $mun_count[$value['mun_code']]['total'] = 1;}
                    //municipality count
                }

                foreach ($municipalities as $key => $value) {
                    // province details
                        //true value
                        $prov_data[$value['prov_code']]['target_val'] = (isset($prov_data[$value['prov_code']]['target_val']))? $prov_data[$value['prov_code']]['target_val']+$all_targets[$value['mun_code']]:$all_targets[$value['mun_code']];

                        $prov_total_val = (isset($prov_count[$value['prov_code']]['total']))? $prov_count[$value['prov_code']]['total'] : 0;

                        $prov_male_val = (isset($prov_count[$value['prov_code']]['male']))? $prov_count[$value['prov_code']]['male'] : 0;
                        $prov_female_val = (isset($prov_count[$value['prov_code']]['female']))? $prov_count[$value['prov_code']]['female'] : 0;
                        $prov_paid_val = (isset($prov_count[$value['prov_code']]['paidtotal']))? $prov_count[$value['prov_code']]['paidtotal'] : 0;

                        $prov_umale_val = (isset($prov_count[$value['prov_code']]['unpaidmale']))? $prov_count[$value['prov_code']]['unpaidmale'] : 0;
                        $prov_ufemale_val = (isset($prov_count[$value['prov_code']]['unpaidfemale']))? $prov_count[$value['prov_code']]['unpaidfemale'] : 0;
                        $prov_unpaid_val = (isset($prov_count[$value['prov_code']]['unpaidtotal']))? $prov_count[$value['prov_code']]['unpaidtotal'] : 0;

                        //true value

                    $prov_data[$value['prov_code']]['name'] = $prov_name[$value['prov_code']];
                    $prov_data[$value['prov_code']]['total'] =  number_format($prov_total_val);

                    $prov_data[$value['prov_code']]['paidtotal'] =  number_format($prov_paid_val);
                    $prov_data[$value['prov_code']]['female'] =  number_format($prov_female_val);
                    $prov_data[$value['prov_code']]['male'] = number_format($prov_male_val);
                    $prov_data[$value['prov_code']]['amount'] = number_format($prov_paid_val * $amount);

                    $prov_data[$value['prov_code']]['unpaidtotal'] =  number_format($prov_unpaid_val);
                    $prov_data[$value['prov_code']]['unpaidfemale'] =  number_format($prov_umale_val);
                    $prov_data[$value['prov_code']]['unpaidmale'] = number_format($prov_ufemale_val);
                    $prov_data[$value['prov_code']]['unpaidamount'] = number_format($prov_unpaid_val * $amount);

                    $prov_data[$value['prov_code']]['mun_show'] = false;
                    $prov_data[$value['prov_code']]['target'] =  number_format($prov_data[$value['prov_code']]['target_val']);
                    // province details


                    // municipality details
                        //true value
                        $mun_target = isset($all_targets[$value['mun_code']]) ? $all_targets[$value['mun_code']] : 0;
                        $mun_total_val = isset($mun_count[$value['mun_code']]['total']) ? $mun_count[$value['mun_code']]['total'] : 0;

                        $mun_male_val = isset($mun_count[$value['mun_code']]['male']) ? $mun_count[$value['mun_code']]['male'] : 0;
                        $mun_female_val = isset($mun_count[$value['mun_code']]['female']) ? $mun_count[$value['mun_code']]['female'] : 0;
                        $mun_paid_val = isset($mun_count[$value['mun_code']]['paidtotal']) ? $mun_count[$value['mun_code']]['paidtotal'] : 0;
                        
                        $mun_umale_val = isset($mun_count[$value['mun_code']]['unpaidmale']) ? $mun_count[$value['mun_code']]['unpaidmale'] : 0;
                        $mun_ufemale_val = isset($mun_count[$value['mun_code']]['unpaidfemale']) ? $mun_count[$value['mun_code']]['unpaidfemale'] : 0;
                        $mun_unpaid_val = isset($mun_count[$value['mun_code']]['unpaidtotal']) ? $mun_count[$value['mun_code']]['unpaidtotal'] : 0;
                        //true value


                    $prov_data[$value['prov_code']]['children'][$value['mun_code']]['name'] = $value['mun_name'];
                    $prov_data[$value['prov_code']]['children'][$value['mun_code']]['target'] = number_format($mun_target);
                    $prov_data[$value['prov_code']]['children'][$value['mun_code']]['total'] = number_format($mun_total_val);

                    $prov_data[$value['prov_code']]['children'][$value['mun_code']]['male'] = number_format($mun_male_val);
                    $prov_data[$value['prov_code']]['children'][$value['mun_code']]['female'] = number_format($mun_female_val);
                    $prov_data[$value['prov_code']]['children'][$value['mun_code']]['paidtotal'] = number_format($mun_paid_val);
                    $prov_data[$value['prov_code']]['children'][$value['mun_code']]['amount'] = number_format($mun_paid_val * $amount);
                    
                    $prov_data[$value['prov_code']]['children'][$value['mun_code']]['unpaidmale'] = number_format($mun_umale_val);
                    $prov_data[$value['prov_code']]['children'][$value['mun_code']]['unpaidfemale'] = number_format($mun_ufemale_val);
                    $prov_data[$value['prov_code']]['children'][$value['mun_code']]['unpaidtotal'] = number_format($mun_unpaid_val);
                    $prov_data[$value['prov_code']]['children'][$value['mun_code']]['unpaidamount'] = number_format($mun_unpaid_val * $amount);
                    // municipality details
                    usort($prov_data[$value['prov_code']]['children'], function($a, $b){ return strcmp($a["name"], $b["name"]); });
                }
            
            usort($prov_data, function($a, $b){ return strcmp($a["name"], $b["name"]); });
            // alternative
            $region_data['r_female'] = number_format($region_count['female']);
            $region_data['r_male'] = number_format($region_count['male']);
            $region_data['r_paidtotal'] = number_format($region_count['paidtotal']);

            $region_data['r_unpaidfemale'] = number_format($region_count['unpaidfemale']);
            $region_data['r_unpaidmale'] = number_format($region_count['unpaidmale']);
            $region_data['r_unpaidtotal'] = number_format($region_count['unpaidtotal']);
            
            $region_data['total'] = number_format($region_count['total']);

            $region_data['paidamount'] = number_format(round($region_count['paidtotal'] * $amount));
            $region_data['unpaidamount'] = number_format(round($region_count['unpaidtotal'] * $amount));

            
            $region_served = (int)$region_count['paidtotal'];
            $target_ = (int)$region_data['target'];
            
            $region_data['accomplishment'] = round(($region_served / $target_) * 100, 2);
            // $region_amount = $region_served * $amount;

        }

        $data['region_served'] = $region_data;
        $data['served'] = $prov_data;
        
        response_json($data);
        //print_r($mun_count);
    }

    public function sendServedData(){

		$input_served = $this->input->post('data');
        $served_data = json_decode($input_served);  
        $search = json_decode($this->input->post('search'), true);
        $client = new Google_Client();
        $client->setApplicationName("Socpen Report");
        $client->setScopes(SCOPES);
        $client->setAuthConfig(CLIENT_SECRET_PATH);
        $client->setAccessToken(ACCESS_TOKEN);

        $service = new Google_Service_Sheets($client);

        //$sheetInfo = $service->spreadsheets->get("1npWoV2MdS3FlWFwLsOVVfQWjbHAZQXUvX3DfiwZh32M")->getProperties();

        //print($sheetInfo['title']. PHP_EOL);

        $options = array('valueInputOption' => 'USER_ENTERED');
        $insertValues = [
            ["PROVINCE", "MUNICIPALITY", "Target Benefeciaries", "Target Amount", "PAID MALE", "PAID FEMALE", "TOTAL PAID","PAID AMOUNT" ]
        ];

        // default values
        $append = false;
        $range = '1st Sem 2020!A1:H78';
        $period_val = '1st Semester';
        $year_val = $search['year'];
        $search['period'] = ($search['period'] != "") ? $search['period'] : 5;
        $period_names = ["","1st Quarter","2nd Quarter","3rd Quarter","4th Quarter","1st Semester","2nd Semester"];

        if($year_val != '2019'){
            if(in_array($search['period'], [5,1,2])){
                $range = '1st Sem ' . $year_val . '!A1:H78';
                if($year_val == "2020"){
                    $range = 'Sheet1!A1:H78';
                }
            } else {
                $range = '2nd Sem ' . $year_val . '!A1:H78';
            }
            $period_val = $period_names[$search['period']];
        } 

        $num = 2;
        
        if($year_val == '2019'){
            $range = '2019!A:I';
            $append = true;

            $response = $service->spreadsheets_values->get(SHEET_ID, $range);
            $values = $response->getValues();
            $insertValues = [];
            $period_val = $period_names[$search['period']];
           
            if(empty($values)){ // for 1st set of data for 2019 sheet
                $insertValues = [
                    ["PROVINCE", "MUNICIPALITY", "Target Benefeciaries", "Target Amount", "PAID MALE", "PAID FEMALE", "TOTAL PAID","PAID AMOUNT", "PERIOD"]
                ];
                $num = count($values) + 1;
            } else {
                $curr_period = array_column($values, 8);
                // if period is already populated in the sheet, update only values for the specific period
                if(in_array($period_val, $curr_period)){
                    $append = false;

                    $get_curr_values = array_filter($values, function($value) use ($period_val) {
                        return $value[8] == $period_val;
                    });

                    $index_arr = array_keys($get_curr_values);

                    $start_range = (int)$index_arr[0] + 1;
                    $end_range = (int)end($index_arr) + 1;

                    $range = '2019!A'.$start_range.':I'.$end_range;
                    $num = $start_range;
                }

            }

        }

        foreach ($served_data as $key => $value) {
            $munisData = $value->children;
            $prov_name = strtoupper($value->name);

            foreach($munisData as $mun_data){
                $name = strtoupper($mun_data->name);
                //$target = $mun_data->target;
                
                if($year_val == '2019'){
                    $target = $mun_data->total;
                }else{
                    $target = $mun_data->target;
                }
                $targetAmount = "=C" . $num . "*" . "3000";
                $male   = $mun_data->male;
                $female = $mun_data->female;
                $total  = $mun_data->paidtotal;
                $amount = "=G" . $num . "*" . "3000";

                // $target = intval(str_replace(",","",$mun_data->target));
                // $male = intval(str_replace(",","",$mun_data->male));
                // $female = intval(str_replace(",","",$mun_data->female));
                // $total = intval(str_replace(",","",$mun_data->total));
                // $amount = intval(str_replace(",","",$mun_data->amount));

                if($year_val == '2019'){
                    $insertValues[] = [$prov_name, $name, $target, $targetAmount, $male, $female, $total, $amount, $period_val];

                } else{
                    $insertValues[] = [$prov_name, $name, $target, $targetAmount, $male, $female, $total, $amount];
                }

                $num++;
            }
        }

        $body   = new Google_Service_Sheets_ValueRange(['values' => $insertValues]);
        $year_val_print = ($year_val != "") ? $year_val : '2020';

        // append only for 2019 and period is not yet existing in the sheet
        if($append){ 
            $result = $service->spreadsheets_values->append(SHEET_ID, $range, $body, $options);
        } else {
            $result = $service->spreadsheets_values->update(SHEET_ID, $range, $body, $options);
        }
        // $result = $service->spreadsheets_values->update(SHEET_ID, 'Sheet2!A1:H78', $body, $options);

        //print($result->updatedRange. PHP_EOL);

        $data["success"] = TRUE;
        // $data["message"] = "Data in googlesheet now updated.";
        $data["message"] = "Data for the " . $year_val_print . " " . $period_val . " in googlesheet now updated.";

        response_json($data);
    }
//////////// SERVED MATRIX ///////////////////////////////////////////////

/////////// START UNPAID MATRIX ////////////////////////////////////////////////
    public function unclaimed()
    {
        $this->template->title('Served Beneficiaries');
        $this->template->set_layout('default');
        $this->template->set_partial('header','partials/header');
        $this->template->set_partial('sidebar','partials/sidebar');
        $this->template->set_partial('aside','partials/aside');
        $this->template->set_partial('footer','partials/footer');
        $this->template->append_metadata('<script src="' . base_url("assets/js/pages/reports/unclaimed.js?ver=") . filemtime(FCPATH. "assets/js/pages/reports/unclaimed.js") . '"></script>');

        $this->template->build('reports/unclaimed');
    }

    public function get_total_unclaimed()
    {
        $year = date("Y");
        $period_condition = 1;
        $type_sem_quart = "Quarter";
        $amount = 1500;
        $month = date("n");
        $semester_target_cond = floor((intval($month) + 5) / 6);
        $period_target_cond = ceil($month / 3);
        
        if($this->input->post('year') !== null && $this->input->post('year') != ""){
            $year = $this->input->post('year');
        }

        if($this->input->post('period') !== null && $this->input->post('period') != "")
        {
            $period = $this->input->post('period');
            if(in_array($period, [5,6])) {
                $type_sem_quart = "Semester";
                $period_condition = ($period == 5)?1:2;
                $amount = 3000;
            }else{
                $period_condition = $period;
                $type_sem_quart = "quarter";
            }

            if(in_array($period, [1,2,3,4])){
                $semester_target_cond = ($period > 2)?2:1;
                $period_target_cond = $period;
            }
            else{
                $period_target_cond = 1;
                $semester_target_cond = ($period == 5)?1:2;
                $amount = 3000;
            }
        }
        
        $provinces = $this->rm->get_all_provinces();
        $prov_name = array_column($provinces, 'prov_name','prov_code');

        $municipalities = $this->rm->get_all_municipalities();
        $mun_name = array_column($municipalities, 'mun_name','mun_code');

        $condition = ["mode_of_payment"   => $type_sem_quart,
                    "period"              => $period_condition,
                    "year"                => $year,
                    "liquidation"         => [0,3,4]];
        
        $total_unclaimed = $this->rm->get_total_served($condition);
        $prov_data = [];
        $region_count = [];
        $get_all_sptatus = [];

        if(!empty($total_unclaimed)){
            $get_all_general = $this->rm->get_all_general("connum,gender,sp_status");
            $get_all_gender = array_column($get_all_general, 'gender','connum');
            $get_all_sptatus = array_column($get_all_general, 'sp_status','connum');

            $targets = $this->rm->get_all_targets(['quarter' => $period_target_cond, 'semester'=>$semester_target_cond,'year'=>$year,'archived'=>0]);
            $all_targets = array_column($targets, 'target','mun_code');

            $prov_count = [];
            $mun_count = [];
            $region_unclaimed = count($total_unclaimed);
            $region_targets = array_sum($all_targets);
            $accomplishment = round(($region_unclaimed / $region_targets) * 100, 2);
            $region_amount = $region_unclaimed * $amount;
            $region_count = ['total'=>number_format($region_unclaimed),'amount' => number_format($region_amount),'target'=>number_format($region_targets), 'accomplishment' => $accomplishment];
           
            // alternative
            foreach ($total_unclaimed as $key => $value) {
                
                $region_count['female']         = (isset($region_count['female'])) ? $region_count['female'] : 0;
                $region_count['male']           = (isset($region_count['male'])) ? $region_count['male'] : 0;
                $region_count['onhold']        = (isset($region_count['onhold'])) ? $region_count['onhold'] : 0;
                $region_count['offset']         = (isset($region_count['offset'])) ? $region_count['offset'] : 0;
                $region_count['unreleased']     = (isset($region_count['unreleased'])) ? $region_count['unreleased'] : 0;
                $region_count['forreplacement'] = (isset($region_count['forreplacement'])) ? $region_count['forreplacement'] : 0;

                $prov_count[$value['prov_code']]['total'] = (isset($prov_count[$value['prov_code']]['total']))? $prov_count[$value['prov_code']]['total'] : 0;
                $prov_count[$value['prov_code']]['female'] = (isset($prov_count[$value['prov_code']]['female']))? $prov_count[$value['prov_code']]['female'] : 0;
                $prov_count[$value['prov_code']]['male'] = (isset($prov_count[$value['prov_code']]['male']))? $prov_count[$value['prov_code']]['male'] : 0;
                $prov_count[$value['prov_code']]['onhold'] = (isset($prov_count[$value['prov_code']]['onhold']))? $prov_count[$value['prov_code']]['onhold'] : 0;
                $prov_count[$value['prov_code']]['offset'] = (isset($prov_count[$value['prov_code']]['offset']))? $prov_count[$value['prov_code']]['offset'] : 0;
                $prov_count[$value['prov_code']]['unreleased'] = (isset($prov_count[$value['prov_code']]['unreleased']))? $prov_count[$value['prov_code']]['unreleased'] : 0;
                $prov_count[$value['prov_code']]['forreplacement'] = (isset($prov_count[$value['prov_code']]['forreplacement']))? $prov_count[$value['prov_code']]['forreplacement'] : 0;

                $mun_count[$value['mun_code']]['total'] = (isset($mun_count[$value['mun_code']]['total']))? $mun_count[$value['mun_code']]['total'] : 0;
                $mun_count[$value['mun_code']]['female'] = (isset($mun_count[$value['mun_code']]['female']))? $mun_count[$value['mun_code']]['female'] : 0;
                $mun_count[$value['mun_code']]['male'] = (isset($mun_count[$value['mun_code']]['male']))? $mun_count[$value['mun_code']]['male'] : 0;
                $mun_count[$value['mun_code']]['onhold'] = (isset($mun_count[$value['mun_code']]['onhold']))? $mun_count[$value['mun_code']]['onhold'] : 0;
                $mun_count[$value['mun_code']]['offset'] = (isset($mun_count[$value['mun_code']]['offset']))? $mun_count[$value['mun_code']]['offset'] : 0;
                $mun_count[$value['mun_code']]['unreleased'] = (isset($mun_count[$value['mun_code']]['unreleased']))? $mun_count[$value['mun_code']]['unreleased'] : 0;
                $mun_count[$value['mun_code']]['forreplacement'] = (isset($mun_count[$value['mun_code']]['forreplacement']))? $mun_count[$value['mun_code']]['forreplacement'] : 0;

                if(isset($get_all_gender[$value['spid']]) && strtoupper(trim($get_all_gender[$value['spid']])) == "FEMALE"){
                    $region_count['female']++;
                    $prov_count[$value['prov_code']]['female']++;
                    $mun_count[$value['mun_code']]['female']++;
                }else{
                    $region_count['male']++;
                    $prov_count[$value['prov_code']]['male']++;
                    $mun_count[$value['mun_code']]['male']++;
                }
                $prov_count[$value['prov_code']]['total']++;
                $mun_count[$value['mun_code']]['total']++;

                if(isset($get_all_sptatus[$value['spid']]) && strtoupper($get_all_sptatus[$value['spid']]) == "FORREPLACEMENT"){
                    $region_count['forreplacement']++;
                    $prov_count[$value['prov_code']]['forreplacement']++;
                    $mun_count[$value['mun_code']]['forreplacement']++;
                }
                
                if($value['liquidation'] == 0){
                    $region_count['unreleased']++;
                    $prov_count[$value['prov_code']]['unreleased']++;
                    $mun_count[$value['mun_code']]['unreleased']++;
                }else if($value['liquidation'] == 3){
                    $region_count['offset']++;
                    $prov_count[$value['prov_code']]['offset']++; 
                    $mun_count[$value['mun_code']]['offset']++;
                }else if($value['liquidation'] == 4){
                    $region_count['onhold']++;
                    $prov_count[$value['prov_code']]['onhold']++;
                    $mun_count[$value['mun_code']]['onhold']++;
                }
            }

            foreach ($municipalities as $key => $value) {
                // province details
                //true value
                $prov_total_val = (isset($prov_count[$value['prov_code']]['total']))? $prov_count[$value['prov_code']]['total'] : 0;
                $prov_male_val = (isset($prov_count[$value['prov_code']]['male']))? $prov_count[$value['prov_code']]['male'] : 0;
                $prov_female_val = (isset($prov_count[$value['prov_code']]['female']))? $prov_count[$value['prov_code']]['female'] : 0;

                $prov_unreleased_val = (isset($prov_count[$value['prov_code']]['unreleased']))? $prov_count[$value['prov_code']]['unreleased'] : 0;
                $prov_offset_val = (isset($prov_count[$value['prov_code']]['offset']))? $prov_count[$value['prov_code']]['offset'] : 0;
                $prov_onhold_val = (isset($prov_count[$value['prov_code']]['onhold']))? $prov_count[$value['prov_code']]['onhold'] : 0;
                $prov_forrep_val = (isset($prov_count[$value['prov_code']]['forreplacement']))? $prov_count[$value['prov_code']]['forreplacement'] : 0;

                $prov_data[$value['prov_code']]['target_val'] = (isset($prov_data[$value['prov_code']]['target_val']))? $prov_data[$value['prov_code']]['target_val']+$all_targets[$value['mun_code']]:$all_targets[$value['mun_code']];

                //true value
                $prov_data[$value['prov_code']]['name'] = $prov_name[$value['prov_code']];
                $prov_data[$value['prov_code']]['total'] =  number_format($prov_total_val);
                $prov_data[$value['prov_code']]['female'] =  number_format($prov_female_val);
                $prov_data[$value['prov_code']]['male'] = number_format($prov_male_val);
                $prov_data[$value['prov_code']]['amount'] = number_format($prov_total_val * $amount);
                $prov_data[$value['prov_code']]['mun_show'] = false;
                $prov_data[$value['prov_code']]['target'] =  number_format($prov_data[$value['prov_code']]['target_val']);
                
                $prov_data[$value['prov_code']]['unreleased'] =  number_format($prov_unreleased_val);
                $prov_data[$value['prov_code']]['offset'] =  number_format($prov_offset_val);
                $prov_data[$value['prov_code']]['onhold'] = number_format($prov_onhold_val);
                $prov_data[$value['prov_code']]['forreplacement'] = number_format($prov_forrep_val);
                // province details


                // municipality details
                //true value
                $mun_total_val = isset($mun_count[$value['mun_code']]['total']) ? $mun_count[$value['mun_code']]['total'] : 0;
                $mun_male_val = isset($mun_count[$value['mun_code']]['male']) ? $mun_count[$value['mun_code']]['male'] : 0;
                $mun_female_val = isset($mun_count[$value['mun_code']]['female']) ? $mun_count[$value['mun_code']]['female'] : 0;
                $mun_target = isset($all_targets[$value['mun_code']]) ? $all_targets[$value['mun_code']] : 0;
                
                $mun_unreleased_val = isset($mun_count[$value['mun_code']]['unreleased']) ? $mun_count[$value['mun_code']]['unreleased'] : 0;
                $mun_offset_val = isset($mun_count[$value['mun_code']]['offset']) ? $mun_count[$value['mun_code']]['offset'] : 0;
                $mun_onhold_val = isset($mun_count[$value['mun_code']]['onhold']) ? $mun_count[$value['mun_code']]['onhold'] : 0;
                $mun_forrep_val = isset($mun_count[$value['mun_code']]['forreplacement']) ? $mun_count[$value['mun_code']]['forreplacement'] : 0;
                //true value

                $prov_data[$value['prov_code']]['children'][$value['mun_code']]['name'] = $value['mun_name'];
                $prov_data[$value['prov_code']]['children'][$value['mun_code']]['total'] = number_format($mun_total_val);
                $prov_data[$value['prov_code']]['children'][$value['mun_code']]['amount'] = number_format($mun_total_val * $amount);
                $prov_data[$value['prov_code']]['children'][$value['mun_code']]['male'] = number_format($mun_male_val);
                $prov_data[$value['prov_code']]['children'][$value['mun_code']]['female'] = number_format($mun_female_val);
                $prov_data[$value['prov_code']]['children'][$value['mun_code']]['target'] = number_format($mun_target);

                $prov_data[$value['prov_code']]['children'][$value['mun_code']]['unreleased'] = number_format($mun_unreleased_val);
                $prov_data[$value['prov_code']]['children'][$value['mun_code']]['offset'] = number_format($mun_offset_val);
                $prov_data[$value['prov_code']]['children'][$value['mun_code']]['onhold'] = number_format($mun_onhold_val);
                $prov_data[$value['prov_code']]['children'][$value['mun_code']]['forreplacement'] = number_format($mun_forrep_val);
                // municipality details
                
                usort($prov_data[$value['prov_code']]['children'], function($a, $b){ return strcmp($a["name"], $b["name"]); });
            }

            usort($prov_data, function($a, $b){ return strcmp($a["name"], $b["name"]); });

            // alternative
            $region_count['r_female'] = number_format($region_count['female']);
            $region_count['r_male'] = number_format($region_count['male']);
            
            $region_count['r_onhold'] = number_format($region_count['onhold']);
            $region_count['r_offset'] = number_format($region_count['offset']);
            $region_count['r_unreleased'] = number_format($region_count['unreleased']);
            $region_count['r_forrep'] = number_format($region_count['forreplacement']);
        }

        $data['region_unclaimed'] = $region_count;
        $data['unclaimed'] = $prov_data;
        $data['get_all_sptatus'] = $get_all_sptatus;

        response_json($data);
    }   


////////// END UNPAID MATRIX ////////////////////////////////////////////////

//////// START TARGET BREAKDOWN PAGE ////////////////////////////

    public function target()
    {
        $this->template->title('Target Breakdown');
        $this->template->set_layout('default');
        $this->template->set_partial('header','partials/header');
        $this->template->set_partial('sidebar','partials/sidebar');
        $this->template->set_partial('aside','partials/aside');
        $this->template->set_partial('footer','partials/footer');
        $this->template->append_metadata('<script src="' . base_url("assets/js/pages/reports/t_breakdown.js?ver=") . filemtime(FCPATH. "assets/js/pages/reports/t_breakdown.js") . '"></script>');

        $this->template->build('reports/t_breakdown_view');
    }

    public function get_total_target(){
        
        $year = date("Y");
        $month = date("n");
        $semester = floor((intval($month) + 5) / 6);
        $period_condition = ceil($month / 3);

        if($this->input->post('year') !== null && $this->input->post('year') != "")
        {
            $year = $this->input->post('year');
        }

        if($this->input->post('semester') !== null && $this->input->post('semester') != "")
        {
            $semester = $this->input->post('semester');

            // $period = $this->input->post('period');
            if(in_array($semester, [1,2,3,4])){
                $semester_target_cond = ($semester > 2)?2:1;
                $period_condition = $semester;
            }
            else{
                $period_condition = 1;
                $semester_target_cond = ($semester == 5)?1:2;
            }
        }

        $targets = $this->rm->get_all_targets(['quarter'=>$period_condition, 'semester'=>$semester_target_cond,'year'=>$year,'archived'=>0]);

        $prov_data = [];
        $total_data = [];
        
        $prov_count = [];
        $region_count = 0;
        
        if(!empty($targets))
        {
            $provinces = $this->rm->get_all_provinces();
            $municipalities = $this->rm->get_all_municipalities();
            $prov_name = array_column($provinces, 'prov_name','prov_code');
            $mun_name = array_column($municipalities, 'mun_name','mun_code');
                
            $prov_count = [];
            $mun_count = array_column($targets, 'target','mun_code');
            $region_count = 0;

            $_target_data = []; $_target_total = 0;
            foreach ($municipalities as $key => $value) {
                $p_code = $value['prov_code'];
                if(isset($prov_count[$p_code])){
                    $prov_count[$p_code] += (isset($mun_count[$value['mun_code']]))? $mun_count[$value['mun_code']] : 0;
                }else{
                    $prov_count[$p_code] = (isset($mun_count[$value['mun_code']]))? $mun_count[$value['mun_code']] : 0;
                }
                $region_count += (isset($mun_count[$value['mun_code']]))? $mun_count[$value['mun_code']] : 0;
            }

            foreach ($municipalities as $key => $value) {
                // province details
                $prov_code = $value['prov_code'];
                $prov_target_val = (isset($prov_count[$prov_code]))? $prov_count[$prov_code] : 0;

                $prov_data[$prov_code]['name'] = $prov_name[$prov_code];
                $prov_data[$prov_code]['total'] =  number_format($prov_target_val);
                $prov_data[$prov_code]['mun_show'] = false;
                // province details

                // municipality details
                $mun_code = $value['mun_code'];
                $mun_target_val = isset($mun_count[$mun_code]) ? $mun_count[$value['mun_code']] : 0;
                
                $prov_data[$prov_code]['municipality'][$mun_code]['name'] = $value['mun_name'];
                $prov_data[$prov_code]['municipality'][$mun_code]['total'] = number_format($mun_target_val);
                // municipality details

                usort($prov_data[$prov_code]['municipality'], function($a, $b){ return strcmp($a["name"], $b["name"]); });
            }
        }
        
        usort($prov_data, function($a, $b){ return strcmp($a["name"], $b["name"]); });

        $total_data = ['total'=>number_format($region_count)];
        $data["total_data"] = $total_data;
        $data["data"] = $prov_data;
        response_json($data);
    }

//////// END TARGET BREAKDOWN PAGE ////////////////////////////

//////// START WAITLIST BREAKDOWN PAGE ////////////////////////////

    public function waitlist()
    {
        $this->template->title('Waitlist Breakdown');
		$this->template->set_layout('default');
	    $this->template->set_partial('header','partials/header');
	    $this->template->set_partial('sidebar','partials/sidebar');
	    $this->template->set_partial('aside','partials/aside');
	    $this->template->set_partial('footer','partials/footer');
	    $this->template->append_metadata('<script src="' . base_url("assets/js/pages/reports/w_breakdown.js?ver=") . filemtime(FCPATH. "assets/js/pages/reports/w_breakdown.js") . '"></script>');

	    $this->template->build('reports/w_breakdown_view');
    }

    public function get_total_waitlist(){
        $total_waitlist = $this->rm->get_all_waitlist();
        $prov_data = [];
        $total_data = [];
        
        if(!empty($total_waitlist))
        {
            $provinces = $this->rm->get_all_provinces();
            $municipalities = $this->rm->get_all_municipalities();
            $prov_name = array_column($provinces, 'prov_name','prov_code');
            $mun_name = array_column($municipalities, 'mun_name','mun_code');

            $region_waitlist = count($total_waitlist);
            $total_data = ['total'=>number_format($region_waitlist),'eligible'=> "0",'not_eligible'=> "0", 'wfe' => "0", 'fstoco' => "0"];

            $region_count = [];
            $prov_count = [];
            $mun_count = [];

            foreach ($total_waitlist as $key => $value) {

                $eligibility = $value["priority"];
                $sent_to_co = $value["sent_to_co"];
                $prov_code = $value["prov_code"];
                $mun_code = $value["mun_code"];

                //province total count
                if(isset($prov_count[$prov_code]['total'])){
                    $prov_count[$prov_code]['total']++;
                }else{
                    $prov_count[$prov_code]['total'] = 1;
                }

                //municipality total count
                if(isset($mun_count[$mun_code]['total'])){
                    $mun_count[$mun_code]['total']++;
                }else{
                    $mun_count[$mun_code]['total'] = 1;
                }
    
                //Counting each eligibility  Waitlist
                if($eligibility == 1){
                    //region count
                    if(isset($region_count["eligible"])){  
                        $region_count["eligible"] += 1;
                    }else{  
                        $region_count["eligible"] = 1; 
                    }

                    //province count
                    if(isset($prov_count[$value['prov_code']]['eligible'])){
                        $prov_count[$value['prov_code']]['eligible']++;
                    }else{
                        $prov_count[$value['prov_code']]['eligible'] = 1;
                    }

                    //municipality count
                    if(isset($mun_count[$value['mun_code']]['eligible'])){
                        $mun_count[$value['mun_code']]['eligible']++;
                    }else{
                        $mun_count[$value['mun_code']]['eligible'] = 1;
                    }

                }else if($eligibility == 2){
                    //region count
                    if(isset($region_count["not_eligible"])){  
                        $region_count["not_eligible"] += 1;
                    }else{  
                        $region_count["not_eligible"] = 1; 
                    }

                    //province count
                    if(isset($prov_count[$value['prov_code']]['not_eligible'])){
                        $prov_count[$value['prov_code']]['not_eligible']++;
                    }else{
                        $prov_count[$value['prov_code']]['not_eligible'] = 1;
                    }

                    //municipality count
                    if(isset($mun_count[$value['mun_code']]['not_eligible'])){
                        $mun_count[$value['mun_code']]['not_eligible']++;
                    }else{
                        $mun_count[$value['mun_code']]['not_eligible'] = 1;
                    }
                }else if($eligibility == 0){
                    if($sent_to_co == 1){
                        //region count
                        if(isset($region_count["wfe"])){  
                            $region_count["wfe"] += 1;
                        }else{  
                            $region_count["wfe"] = 1; 
                        }

                        //province count
                        if(isset($prov_count[$value['prov_code']]['wfe'])){
                            $prov_count[$value['prov_code']]['wfe']++;
                        }else{
                            $prov_count[$value['prov_code']]['wfe'] = 1;
                        }

                        //municipality count
                        if(isset($mun_count[$value['mun_code']]['wfe'])){
                            $mun_count[$value['mun_code']]['wfe']++;
                        }else{
                            $mun_count[$value['mun_code']]['wfe'] = 1;
                        }
                    }else{
                         //region count
                         if(isset($region_count["fstoco"])){  
                            $region_count["fstoco"] += 1;
                        }else{  
                            $region_count["fstoco"] = 1; 
                        }

                        //province count
                        if(isset($prov_count[$value['prov_code']]['fstoco'])){
                            $prov_count[$value['prov_code']]['fstoco']++;
                        }else{
                            $prov_count[$value['prov_code']]['fstoco'] = 1;
                        }

                        //municipality count
                        if(isset($mun_count[$value['mun_code']]['fstoco'])){
                            $mun_count[$value['mun_code']]['fstoco']++;
                        }else{
                            $mun_count[$value['mun_code']]['fstoco'] = 1;
                        }
                    }
                    
                }
            }

            foreach ($municipalities as $key => $value) {
                // province details
                $prov_total_val = (isset($prov_count[$value['prov_code']]['total']))? $prov_count[$value['prov_code']]['total'] : 0;
                $prov_eligible_val = (isset($prov_count[$value['prov_code']]['eligible']))? $prov_count[$value['prov_code']]['eligible'] : 0;
                $prov_ne_val = (isset($prov_count[$value['prov_code']]['not_eligible']))? $prov_count[$value['prov_code']]['not_eligible'] : 0;
                $prov_wfe_val = (isset($prov_count[$value['prov_code']]['wfe']))? $prov_count[$value['prov_code']]['wfe'] : 0;
                $prov_fstoco_val = (isset($prov_count[$value['prov_code']]['fstoco']))? $prov_count[$value['prov_code']]['fstoco'] : 0;


                $prov_data[$value['prov_code']]['name'] = $prov_name[$value['prov_code']];
                $prov_data[$value['prov_code']]['total'] =  number_format($prov_total_val);
                $prov_data[$value['prov_code']]['eligible'] =  number_format($prov_eligible_val);
                $prov_data[$value['prov_code']]['not_eligible'] = number_format($prov_ne_val);
                $prov_data[$value['prov_code']]['wfe'] = number_format($prov_wfe_val);
                $prov_data[$value['prov_code']]['fstoco'] = number_format($prov_fstoco_val);

                $prov_data[$value['prov_code']]['mun_show'] = false;
                // province details

                // municipality details
                $mun_total_val = isset($mun_count[$value['mun_code']]['total']) ? $mun_count[$value['mun_code']]['total'] : 0;
                $mun_eligible_val = isset($mun_count[$value['mun_code']]['eligible']) ? $mun_count[$value['mun_code']]['eligible'] : 0;
                $mun_ne_val = isset($mun_count[$value['mun_code']]['not_eligible']) ? $mun_count[$value['mun_code']]['not_eligible'] : 0;
                $mun_wfe_val = isset($mun_count[$value['mun_code']]['wfe']) ? $mun_count[$value['mun_code']]['wfe'] : 0;
                $mun_fstoco_val = isset($mun_count[$value['mun_code']]['fstoco']) ? $mun_count[$value['mun_code']]['fstoco'] : 0;

                $prov_data[$value['prov_code']]['municipality'][$value['mun_code']]['name'] = $value['mun_name'];
                $prov_data[$value['prov_code']]['municipality'][$value['mun_code']]['total'] = number_format($mun_total_val);
                $prov_data[$value['prov_code']]['municipality'][$value['mun_code']]['eligible'] = number_format($mun_eligible_val);
                $prov_data[$value['prov_code']]['municipality'][$value['mun_code']]['not_eligible'] = number_format($mun_ne_val);
                $prov_data[$value['prov_code']]['municipality'][$value['mun_code']]['wfe'] = number_format($mun_wfe_val);
                $prov_data[$value['prov_code']]['municipality'][$value['mun_code']]['fstoco'] = number_format($mun_fstoco_val);
                // municipality details
                
                usort($prov_data[$value['prov_code']]['municipality'], function($a, $b){ return strcmp($a["name"], $b["name"]); });
            }
        }

        usort($prov_data, function($a, $b){ return strcmp($a["name"], $b["name"]); });

        $total_data['eligible'] = isset($region_count['eligible']) ? number_format($region_count['eligible']) : 0;
        $total_data['not_eligible'] = isset($region_count['not_eligible']) ? number_format($region_count['not_eligible']) : 0;
        $total_data['wfe'] = isset($region_count['wfe']) ? number_format($region_count['wfe']) : 0;
        $total_data['fstoco'] = isset($region_count['fstoco']) ? number_format($region_count['fstoco']) : 0;

        $data["total_data"] = $total_data;
        $data["data"] = $prov_data;
        response_json($data);
    }

//////// END WAITLIST BREAKDOWN PAGE ////////////////////////////

//////// START ACTIVE BREAKDOWN PAGE ////////////////////////////

    public function active()
    {
        $this->template->title('Waitlist Breakdown');
        $this->template->set_layout('default');
        $this->template->set_partial('header','partials/header');
        $this->template->set_partial('sidebar','partials/sidebar');
        $this->template->set_partial('aside','partials/aside');
        $this->template->set_partial('footer','partials/footer');
        $this->template->append_metadata('<script src="' . base_url("assets/js/pages/reports/a_breakdown.js?ver=") . filemtime(FCPATH. "assets/js/pages/reports/a_breakdown.js") . '"></script>');

        $this->template->build('reports/a_breakdown_view');
    }

    public function get_total_active(){
        $condition = ["sp_status <>"   => "Inactive"];

        $get_all_active = $this->rm->get_all_general("connum,province,city,gender,sp_status", $condition);
        $get_all_gender = array_column($get_all_active, 'gender','connum');

        $prov_data = [];
        $total_data = [];

        if(!empty($get_all_active))
        {
            $provinces = $this->rm->get_all_provinces();
            $municipalities = $this->rm->get_all_municipalities();
            $prov_name = array_column($provinces, 'prov_name','prov_code');
            $mun_name = array_column($municipalities, 'mun_name','mun_code');

            $region_active = count($get_all_active);
            $total_data = ['total'=>number_format($region_active),'active_male'=> "0",'active_female'=> "0", 'total_active' => "0",'forrep_male'=> "0",'forrep_female'=> "0", 'total_forrep' => "0"];

            $region_count = [];
            $prov_count = [];
            $mun_count = [];

            foreach ($get_all_active as $key => $value) {

                $sp_status = $value["sp_status"];
                $gender = $value["gender"];
                $prov_code = $value["province"];
                $mun_code = $value["city"];

                //province total count
                if(isset($prov_count[$prov_code]['total'])){
                    $prov_count[$prov_code]['total']++;
                }else{
                    $prov_count[$prov_code]['total'] = 1;
                }
                //municipality total count
                if(isset($mun_count[$mun_code]['total'])){
                    $mun_count[$mun_code]['total']++;
                }else{
                    $mun_count[$mun_code]['total'] = 1;
                }

                //Counting each eligibility  Waitlist
                if(strtoupper(trim($sp_status)) == "FORREPLACEMENT"){
                    //region count
                    if(isset($region_count["total_forrep"])){ $region_count["total_forrep"] += 1;
                    }else{ $region_count["total_forrep"] = 1; }
                    //province count
                    if(isset($prov_count[$prov_code]['total_forrep'])){ $prov_count[$prov_code]['total_forrep']++;
                    }else{ $prov_count[$prov_code]['total_forrep'] = 1; }
                    //municipality count
                    if(isset($mun_count[$mun_code]['total_forrep'])){  $mun_count[$mun_code]['total_forrep']++;
                    }else{ $mun_count[$mun_code]['total_forrep'] = 1; }

                    
                    if(strtoupper(trim($gender)) == "MALE"){
                        //region count
                        if(isset($region_count["forrep_male"])){ $region_count["forrep_male"] += 1;
                        }else{ $region_count["forrep_male"] = 1; }
                        //province count
                        if(isset($prov_count[$prov_code]['forrep_male'])){ $prov_count[$prov_code]['forrep_male']++;
                        }else{ $prov_count[$prov_code]['forrep_male'] = 1; }
                        //municipality count
                        if(isset($mun_count[$mun_code]['forrep_male'])){  $mun_count[$mun_code]['forrep_male']++;
                        }else{ $mun_count[$mun_code]['forrep_male'] = 1; }

                    }else if(strtoupper(trim($gender)) == "FEMALE"){
                        //region count
                        if(isset($region_count["forrep_female"])){ $region_count["forrep_female"] += 1;
                        }else{ $region_count["forrep_female"] = 1; }
                        //province count
                        if(isset($prov_count[$prov_code]['forrep_female'])){ $prov_count[$prov_code]['forrep_female']++;
                        }else{ $prov_count[$prov_code]['forrep_female'] = 1; }
                        //municipality count
                        if(isset($mun_count[$mun_code]['forrep_female'])){  $mun_count[$mun_code]['forrep_female']++;
                        }else{ $mun_count[$mun_code]['forrep_female'] = 1; }
                    }

                }else{
                    //region count
                    if(isset($region_count["total_active"])){ $region_count["total_active"] += 1;
                    }else{ $region_count["total_active"] = 1; }
                    //province count
                    if(isset($prov_count[$prov_code]['total_active'])){ $prov_count[$prov_code]['total_active']++;
                    }else{ $prov_count[$prov_code]['total_active'] = 1; }
                    //municipality count
                    if(isset($mun_count[$mun_code]['total_active'])){  $mun_count[$mun_code]['total_active']++;
                    }else{ $mun_count[$mun_code]['total_active'] = 1; }

                    if(strtoupper(trim($gender)) == "MALE"){
                        //region count
                        if(isset($region_count["active_male"])){ $region_count["active_male"] += 1;
                        }else{ $region_count["active_male"] = 1; }
                        //province count
                        if(isset($prov_count[$prov_code]['active_male'])){ $prov_count[$prov_code]['active_male']++;
                        }else{ $prov_count[$prov_code]['active_male'] = 1; }
                        //municipality count
                        if(isset($mun_count[$mun_code]['active_male'])){  $mun_count[$mun_code]['active_male']++;
                        }else{ $mun_count[$mun_code]['active_male'] = 1; }

                    }else if(strtoupper(trim($gender)) == "FEMALE"){
                        //region count
                        if(isset($region_count["active_female"])){ $region_count["active_female"] += 1;
                        }else{ $region_count["active_female"] = 1; }
                        //province count
                        if(isset($prov_count[$prov_code]['active_female'])){ $prov_count[$prov_code]['active_female']++;
                        }else{ $prov_count[$prov_code]['active_female'] = 1; }
                        //municipality count
                        if(isset($mun_count[$mun_code]['active_female'])){  $mun_count[$mun_code]['active_female']++;
                        }else{ $mun_count[$mun_code]['active_female'] = 1; }
                    }

                }
            }

            foreach ($municipalities as $key => $value) {
                $prov_code = $value["prov_code"];
                $mun_code = $value["mun_code"];

                // province details
                $prov_total = (isset($prov_count[$prov_code]['total']))? $prov_count[$value['prov_code']]['total'] : 0;

                $prov_total_active = (isset($prov_count[$prov_code]['total_active']))? $prov_count[$value['prov_code']]['total_active'] : 0;
                $prov_male_active = (isset($prov_count[$prov_code]['active_male']))? $prov_count[$value['prov_code']]['active_male'] : 0;
                $prov_female_active = (isset($prov_count[$prov_code]['active_female']))? $prov_count[$value['prov_code']]['active_female'] : 0;

                $prov_total_forrep = (isset($prov_count[$prov_code]['total_forrep']))? $prov_count[$value['prov_code']]['total_forrep'] : 0;
                $prov_male_forrep = (isset($prov_count[$prov_code]['forrep_male']))? $prov_count[$value['prov_code']]['forrep_male'] : 0;
                $prov_female_forrep = (isset($prov_count[$prov_code]['forrep_female']))? $prov_count[$value['prov_code']]['forrep_female'] : 0;


                $prov_data[$prov_code]['name'] = $prov_name[$prov_code];
                $prov_data[$prov_code]['total'] =  number_format($prov_total);
                $prov_data[$prov_code]['total_active'] =  number_format($prov_total_active);
                $prov_data[$prov_code]['active_male'] = number_format($prov_male_active);
                $prov_data[$prov_code]['active_female'] = number_format($prov_female_active);
                $prov_data[$prov_code]['total_forrep'] =  number_format($prov_total_forrep);
                $prov_data[$prov_code]['forrep_male'] = number_format($prov_male_forrep);
                $prov_data[$prov_code]['forrep_female'] = number_format($prov_female_forrep);

                $prov_data[$value['prov_code']]['mun_show'] = false;
                // province details

                // municipality details
                $mun_total = isset($mun_count[$mun_code]['total']) ? $mun_count[$mun_code]['total'] : 0;
                $mun_total_active = isset($mun_count[$mun_code]['total_active']) ? $mun_count[$mun_code]['total_active'] : 0;
                $mun_male_active = isset($mun_count[$mun_code]['active_male']) ? $mun_count[$mun_code]['active_male'] : 0;
                $mun_female_active = isset($mun_count[$mun_code]['active_female']) ? $mun_count[$mun_code]['active_female'] : 0;
                $mun_total_forrep = isset($mun_count[$mun_code]['total_forrep']) ? $mun_count[$mun_code]['total_forrep'] : 0;
                $mun_male_forrep = isset($mun_count[$mun_code]['forrep_male']) ? $mun_count[$mun_code]['forrep_male'] : 0;
                $mun_female_forrep = isset($mun_count[$mun_code]['forrep_female']) ? $mun_count[$mun_code]['forrep_female'] : 0;

                $prov_data[$prov_code]['municipality'][$mun_code]['name'] = $value['mun_name'];
                $prov_data[$prov_code]['municipality'][$mun_code]['total'] = number_format($mun_total);
                $prov_data[$prov_code]['municipality'][$mun_code]['total_active'] = number_format($mun_total_active);
                $prov_data[$prov_code]['municipality'][$mun_code]['active_male'] = number_format($mun_male_active);
                $prov_data[$prov_code]['municipality'][$mun_code]['active_female'] = number_format($mun_female_active);
                $prov_data[$prov_code]['municipality'][$mun_code]['total_forrep'] = number_format($mun_total_forrep);
                $prov_data[$prov_code]['municipality'][$mun_code]['forrep_male'] = number_format($mun_male_forrep);
                $prov_data[$prov_code]['municipality'][$mun_code]['forrep_female'] = number_format($mun_female_forrep);
                // municipality details
                
                usort($prov_data[$prov_code]['municipality'], function($a, $b){ return strcmp($a["name"], $b["name"]); });
            }
        }

        usort($prov_data, function($a, $b){ return strcmp($a["name"], $b["name"]); });

        $total_data['total_active'] = isset($region_count['total_active']) ? number_format($region_count['total_active']) : 0;
        $total_data['active_male'] = isset($region_count['active_male']) ? number_format($region_count['active_male']) : 0;
        $total_data['active_female'] = isset($region_count['active_female']) ? number_format($region_count['active_female']) : 0;

        $total_data['total_forrep'] = isset($region_count['total_forrep']) ? number_format($region_count['total_forrep']) : 0;
        $total_data['forrep_male'] = isset($region_count['forrep_male']) ? number_format($region_count['forrep_male']) : 0;
        $total_data['forrep_female'] = isset($region_count['forrep_female']) ? number_format($region_count['forrep_female']) : 0;

        $data["total_data"] = $total_data;
        $data["data"] = $prov_data;
        response_json($data);
    }

//////// END WAITLIST BREAKDOWN PAGE ////////////////////////////

//////// REPLACEMENT MONITORING ////////////////////////////////


    public function repwaitlistMonitoring()
    {
        $this->template->title('Replacement Monitoring');
        $this->template->set_layout('default');
        $this->template->set_partial('header','partials/header');
        $this->template->set_partial('sidebar','partials/sidebar');
        $this->template->set_partial('aside','partials/aside');
        $this->template->set_partial('footer','partials/footer');
        $this->template->append_metadata('<script src="' . base_url("assets/js/pages/reports/repwaitlist.js?ver=") . filemtime(FCPATH. "assets/js/pages/reports/repwaitlist.js") . '"></script>');

        $this->template->build('reports/repwaitlist_view');
    }

    public function get_repwaitlistMonitoring(){
        $condition = ["sp_status <>"   => "Inactive"];
        $get_all_active = $this->rm->get_all_general("connum,province,city,sp_status", $condition);
        $year = date("Y");
        $month = date("n");
        $semester = floor((intval($month) + 5) / 6);
        if(!empty($get_all_active))
        {
            $provinces = $this->rm->get_all_provinces();
            $municipalities = $this->rm->get_all_municipalities();
            $prov_names = array_column($provinces, 'prov_name','prov_code');
            $mun_names = array_column($municipalities, 'mun_name','mun_code');

            $get_waitlist = $this->rm->get_all_waitlist(["priority" => 1]);
            $targets = $this->rm->get_all_targets(['semester'=>$semester,'year'=>$year,'archived'=>0]);

            $all_targets = array_column($targets, 'target','mun_code');

            $region_active = count($get_all_active);
            $region_waitlist = count($get_waitlist);
            $region_target = array_sum($all_targets);

            $region_data   = ['target'=> $region_target, 'active'=> 0, 'forReplacement'=> 0, 'current_benes' => $region_active, 'eligible_waitlist'=> $region_waitlist];

            $prov_data = [];

            foreach ($get_all_active as $key => $value) {

                $sp_status = $value["sp_status"];
                $prov_code = $value["province"];
                $mun_code = $value["city"];

                $prov_data[$prov_code]['name'] = (isset($prov_names[$prov_code])) ? $prov_names[$prov_code] : "";
                $prov_data[$prov_code]['mun_show'] = false;

                $prov_data[$prov_code]['target'] = isset($prov_data[$prov_code]['target']) ? $prov_data[$prov_code]['target'] : 0;
                $prov_data[$prov_code]['active'] = isset($prov_data[$prov_code]['active']) ? $prov_data[$prov_code]['active'] : 0;
                $prov_data[$prov_code]['forReplacement'] = isset($prov_data[$prov_code]['forReplacement']) ? $prov_data[$prov_code]['forReplacement'] : 0;
                $prov_data[$prov_code]['current_benes'] = isset($prov_data[$prov_code]['current_benes']) ? $prov_data[$prov_code]['current_benes'] : 0;
                $prov_data[$prov_code]['eligible_waitlist'] = isset($prov_data[$prov_code]['eligible_waitlist']) ? $prov_data[$prov_code]['eligible_waitlist'] : 0;

                
                $prov_data[$prov_code]["child"][$mun_code]['name'] = (isset($mun_names[$mun_code])) ? $mun_names[$mun_code] : "";
                $prov_data[$prov_code]["child"][$mun_code]['target'] = isset($prov_data[$prov_code]["child"][$mun_code]['target']) ? $prov_data[$prov_code]["child"][$mun_code]['target'] : 0;
                $prov_data[$prov_code]["child"][$mun_code]['active'] = isset($prov_data[$prov_code]["child"][$mun_code]['active']) ? $prov_data[$prov_code]["child"][$mun_code]['active'] : 0;
                $prov_data[$prov_code]["child"][$mun_code]['forReplacement'] = isset($prov_data[$prov_code]["child"][$mun_code]['forReplacement']) ? $prov_data[$prov_code]["child"][$mun_code]['forReplacement'] : 0;
                $prov_data[$prov_code]["child"][$mun_code]['current_benes'] = isset($prov_data[$prov_code]["child"][$mun_code]['current_benes']) ? $prov_data[$prov_code]["child"][$mun_code]['current_benes'] : 0;
                $prov_data[$prov_code]["child"][$mun_code]['eligible_waitlist'] = isset($prov_data[$prov_code]["child"][$mun_code]['eligible_waitlist']) ? $prov_data[$prov_code]["child"][$mun_code]['eligible_waitlist'] : 0;

                
                //province count
                $prov_data[$prov_code]['current_benes']++;
                //municipality count
                $prov_data[$prov_code]["child"][$mun_code]['current_benes']++;

                //Counting each eligibility  Waitlist
                if(strtoupper(trim($sp_status)) == "FORREPLACEMENT"){
                    //region count
                    $region_data['forReplacement']++;
                    //province count
                    $prov_data[$prov_code]['forReplacement']++;
                    //municipality count
                    $prov_data[$prov_code]["child"][$mun_code]['forReplacement']++;
                }else{
                    //region count
                    $region_data['active']++;
                    //province count
                    $prov_data[$prov_code]['active']++;
                    //municipality count
                    $prov_data[$prov_code]["child"][$mun_code]['active']++;
                }
            }

            foreach ($get_waitlist as $key => $value) {

                $prov_code = $value["prov_code"];
                $mun_code = $value["mun_code"];

                if(isset($mun_names[$mun_code])){
                    $prov_data[$prov_code]['eligible_waitlist'] = isset($prov_data[$prov_code]['eligible_waitlist']) ? $prov_data[$prov_code]['eligible_waitlist'] : 0;
                    $prov_data[$prov_code]["child"][$mun_code]['eligible_waitlist'] = isset($prov_data[$prov_code]["child"][$mun_code]['eligible_waitlist']) ? $prov_data[$prov_code]["child"][$mun_code]['eligible_waitlist'] : 0;

                    $prov_data[$prov_code]['eligible_waitlist']++;
                    $prov_data[$prov_code]["child"][$mun_code]['eligible_waitlist']++;
                }
            }

            foreach ($municipalities as $key => $value) {
                $prov_code = $value["prov_code"];
                $mun_code = $value["mun_code"];

                $prov_data[$prov_code]['target'] = isset($prov_data[$prov_code]['target']) ? $prov_data[$prov_code]['target'] : 0;
                $target = isset($all_targets[$mun_code]) ? $all_targets[$mun_code] : 0;
                
                $prov_data[$prov_code]['target'] += $target;
                $prov_data[$prov_code]["child"][$mun_code]['target'] = $target;
            }

            // reset key for sorting
            foreach ($prov_data as $key => $value) {
                $value['child'] = array_values($value['child']);
                $prov_data[$key] = $value;
                usort($prov_data[$key]['child'], function($a, $b){ return strcmp($a["name"], $b["name"]); });
            }

        }

        usort($prov_data, function($a, $b){ return strcmp($a["name"], $b["name"]); });

        $data["region_data"] = $region_data;
        $data["prov_data"] = $prov_data;
        response_json($data);
    }

    public function get_monthly_served()
    {
        ini_set('memory_limit', '-1');
        // error_reporting(E_ERROR | E_PARSE);
        extract($_POST);
        $amount = 3000;

        $provinces = $this->rm->get_all_provinces();
        $municipalities = $this->rm->get_all_municipalities();

        $prov_name = array_column($provinces, 'prov_name','prov_code');
        $mun_name = array_column($municipalities, 'mun_name','mun_code');
        
        $prov_data = [];
        $region_data = [];
        $region_count = [];

        if(in_array($period, [5,6])){
            $type_sem_quart = "Semester";
            $period_condition = ($period == 5)?1:2;
        }else {
            $period_condition = $period;
            $type_sem_quart = "Quarter";
            $amount = 1500;
        }

        $get_served = $this->Main->select([
            'select'    => 'spid,prov_code,mun_code,bar_code,liquidation, MONTH(date_receive) as month',
            'table'     => 'tblpayroll',
            'type'      => 'result_array',
            'condition' => ['year' => $year,
                            'period' => $period_condition,
                            'mode_of_payment' => $type_sem_quart,
                            'liquidation <>'  => 2]   
        ]);

        if(!empty($get_served))
        {
            $get_all_general = $this->rm->get_all_general();
            $get_all_gender = array_column($get_all_general, 'gender','connum');

            $targets = $this->rm->get_all_targets(['year'=>$year,'archived'=>0]);
            $all_targets = array_column($targets, 'target','mun_code');

            $prov_count = [];
            $mun_count = [];
            $region_count = [];
            $region_targets = array_sum($all_targets);

            $region_data['target'] = number_format($region_targets);
            
            foreach ($get_served as $key => $value) {
                // get paid
                if($value['month'] <= $month && $value['liquidation'] == 1){

                    //mun_data
                    if(isset($mun_count[$value['mun_code']]['paidtotal'])){
                        $mun_count[$value['mun_code']]['paidtotal']++;
                    } else {
                        $mun_count[$value['mun_code']]['paidtotal'] = 1;
                    }

                    if(isset($get_all_gender[$value['spid']]) && strtoupper(trim($get_all_gender[$value['spid']])) == "FEMALE"){
                        if(isset($mun_count[$value['mun_code']]['female'])){
                            $mun_count[$value['mun_code']]['female']++;
                        } else {
                            $mun_count[$value['mun_code']]['female'] = 1;
                        }

                    }

                    if(isset($get_all_gender[$value['spid']]) && strtoupper(trim($get_all_gender[$value['spid']])) == "MALE"){
                        if(isset($mun_count[$value['mun_code']]['male'])){
                            $mun_count[$value['mun_code']]['male']++;
                        } else {
                            $mun_count[$value['mun_code']]['male'] = 1;
                        }

                    }

                    //mun_data


                    //prov_data
                    if(isset($prov_count[$value['prov_code']]['paidtotal'])){
                        $prov_count[$value['prov_code']]['paidtotal']++;
                    } else {
                        $prov_count[$value['prov_code']]['paidtotal'] = 1;
                    }

                    if(isset($get_all_gender[$value['spid']]) && strtoupper(trim($get_all_gender[$value['spid']])) == "FEMALE"){
                        if(isset($prov_count[$value['prov_code']]['female'])){
                            $prov_count[$value['prov_code']]['female']++;
                        } else {
                            $prov_count[$value['prov_code']]['female'] = 1;
                        }
                    }

                    if(isset($get_all_gender[$value['spid']]) && strtoupper(trim($get_all_gender[$value['spid']])) == "MALE"){
                        if(isset($prov_count[$value['prov_code']]['male'])){
                            $prov_count[$value['prov_code']]['male']++;
                        } else {
                            $prov_count[$value['prov_code']]['male'] = 1;
                        }
                    }
                    //prov_data 

                } else {

                    //mun_data


                    if(isset($get_all_gender[$value['spid']]) && strtoupper(trim($get_all_gender[$value['spid']])) != ""){

                        if(isset($mun_count[$value['mun_code']]['unpaidtotal'])){
                            $mun_count[$value['mun_code']]['unpaidtotal']++;
                        } else {
                            $mun_count[$value['mun_code']]['unpaidtotal'] = 1;
                        }

                        if(strtoupper(trim($get_all_gender[$value['spid']])) == "MALE"){
                            if(isset($mun_count[$value['mun_code']]['unpaidmale'])){
                                $mun_count[$value['mun_code']]['unpaidmale']++;
                            }else{
                                $mun_count[$value['mun_code']]['unpaidmale'] = 1;
                            }
                        }
                        if(strtoupper(trim($get_all_gender[$value['spid']])) == "FEMALE"){
                            if(isset($mun_count[$value['mun_code']]['unpaidfemale'])){
                                $mun_count[$value['mun_code']]['unpaidfemale']++;
                            }else{
                                $mun_count[$value['mun_code']]['unpaidfemale'] = 1;
                            }
                        }
                    }
                    //mun_data


                    //prov_data
                    if(isset($prov_count[$value['prov_code']]['unpaidtotal'])){
                        $prov_count[$value['prov_code']]['unpaidtotal']++;
                    } else {
                        $prov_count[$value['prov_code']]['unpaidtotal'] = 1;
                    }

                    if(isset($get_all_gender[$value['spid']]) && strtoupper(trim($get_all_gender[$value['spid']])) == "MALE"){
                        if(isset($prov_count[$value['prov_code']]['unpaidmale'])){
                            $prov_count[$value['prov_code']]['unpaidmale']++;
                        }else{
                            $prov_count[$value['prov_code']]['unpaidmale'] = 1;
                        }
                    }
                    if(isset($get_all_gender[$value['spid']]) && strtoupper(trim($get_all_gender[$value['spid']])) == "FEMALE"){
                        if(isset($prov_count[$value['prov_code']]['unpaidfemale'])){
                            $prov_count[$value['prov_code']]['unpaidfemale']++;
                        }else{
                            $prov_count[$value['prov_code']]['unpaidfemale'] = 1;
                        }
                    }
                    //prov_data

                }

                //mun_data
                if(isset($mun_count[$value['mun_code']]['total'])){
                    $mun_count[$value['mun_code']]['total']++;
                } else {
                    $mun_count[$value['mun_code']]['total'] = 1;
                }
                //mun_data

                //prov_data
                 if(isset($prov_count[$value['prov_code']]['total'])){
                    $prov_count[$value['prov_code']]['total']++;
                } else {
                    $prov_count[$value['prov_code']]['total'] = 1;
                }
                //prov_data

            }

                foreach ($municipalities as $key => $value) {
                    // province details
                        //true value
                        $prov_data[$value['prov_code']]['target_val'] = (isset($prov_data[$value['prov_code']]['target_val']))? $prov_data[$value['prov_code']]['target_val']+$all_targets[$value['mun_code']]:$all_targets[$value['mun_code']];

                        $prov_total_val = (isset($prov_count[$value['prov_code']]['total']))? $prov_count[$value['prov_code']]['total'] : 0;

                        $prov_male_val = (isset($prov_count[$value['prov_code']]['male']))? $prov_count[$value['prov_code']]['male'] : 0;
                        $prov_female_val = (isset($prov_count[$value['prov_code']]['female']))? $prov_count[$value['prov_code']]['female'] : 0;
                        $prov_paid_val = (isset($prov_count[$value['prov_code']]['paidtotal']))? $prov_count[$value['prov_code']]['paidtotal'] : 0;

                        $prov_umale_val = (isset($prov_count[$value['prov_code']]['unpaidmale']))? $prov_count[$value['prov_code']]['unpaidmale'] : 0;
                        $prov_ufemale_val = (isset($prov_count[$value['prov_code']]['unpaidfemale']))? $prov_count[$value['prov_code']]['unpaidfemale'] : 0;
                        $prov_unpaid_val = (isset($prov_count[$value['prov_code']]['unpaidtotal']))? $prov_count[$value['prov_code']]['unpaidtotal'] : 0;

                        //true value

                    $prov_data[$value['prov_code']]['name'] = $prov_name[$value['prov_code']];
                    $prov_data[$value['prov_code']]['total'] =  number_format($prov_total_val);

                    $prov_data[$value['prov_code']]['paidtotal'] =  number_format($prov_paid_val);
                    $prov_data[$value['prov_code']]['female'] =  number_format($prov_female_val);
                    $prov_data[$value['prov_code']]['male'] = number_format($prov_male_val);
                    $prov_data[$value['prov_code']]['amount'] = number_format($prov_paid_val * $amount);

                    $prov_data[$value['prov_code']]['unpaidtotal'] =  number_format($prov_unpaid_val);
                    $prov_data[$value['prov_code']]['unpaidfemale'] =  number_format($prov_ufemale_val);
                    $prov_data[$value['prov_code']]['unpaidmale'] = number_format($prov_umale_val);
                    $prov_data[$value['prov_code']]['unpaidamount'] = number_format($prov_unpaid_val * $amount);

                    $prov_data[$value['prov_code']]['total_int'] =  $prov_total_val;
                    $prov_data[$value['prov_code']]['paidtotal_int'] =  $prov_paid_val;
                    $prov_data[$value['prov_code']]['female_int'] =  $prov_female_val;
                    $prov_data[$value['prov_code']]['male_int'] = $prov_male_val;
                    $prov_data[$value['prov_code']]['amount_int'] = $prov_paid_val * $amount;

                    $prov_data[$value['prov_code']]['unpaidtotal_int'] =  $prov_unpaid_val;
                    $prov_data[$value['prov_code']]['unpaidfemale_int'] =  $prov_umale_val;
                    $prov_data[$value['prov_code']]['unpaidmale_int'] = $prov_ufemale_val;
                    $prov_data[$value['prov_code']]['unpaidamount_int'] = $prov_unpaid_val * $amount;

                    $prov_data[$value['prov_code']]['mun_show'] = false;
                    $prov_data[$value['prov_code']]['target'] =  number_format($prov_data[$value['prov_code']]['target_val']);
                    // province details


                    // municipality details
                        //true value
                        $mun_target = isset($all_targets[$value['mun_code']]) ? $all_targets[$value['mun_code']] : 0;
                        $mun_total_val = isset($mun_count[$value['mun_code']]['total']) ? $mun_count[$value['mun_code']]['total'] : 0;

                        $mun_male_val = isset($mun_count[$value['mun_code']]['male']) ? $mun_count[$value['mun_code']]['male'] : 0;
                        $mun_female_val = isset($mun_count[$value['mun_code']]['female']) ? $mun_count[$value['mun_code']]['female'] : 0;
                        $mun_paid_val = isset($mun_count[$value['mun_code']]['paidtotal']) ? $mun_count[$value['mun_code']]['paidtotal'] : 0;
                        
                        $mun_umale_val = isset($mun_count[$value['mun_code']]['unpaidmale']) ? $mun_count[$value['mun_code']]['unpaidmale'] : 0;
                        $mun_ufemale_val = isset($mun_count[$value['mun_code']]['unpaidfemale']) ? $mun_count[$value['mun_code']]['unpaidfemale'] : 0;
                        $mun_unpaid_val = isset($mun_count[$value['mun_code']]['unpaidtotal']) ? $mun_count[$value['mun_code']]['unpaidtotal'] : 0;
                        $prov_unpaid_val = isset($prov_count[$value['mun_code']]['unpaidtotal']) ? $prov_count[$value['mun_code']]['unpaidtotal'] : 0;
                        //true value


                    $prov_data[$value['prov_code']]['children'][$value['mun_code']]['name'] = $value['mun_name'];
                    $prov_data[$value['prov_code']]['children'][$value['mun_code']]['target'] = number_format($mun_target);
                    $prov_data[$value['prov_code']]['children'][$value['mun_code']]['total'] = number_format($mun_total_val);

                    $prov_data[$value['prov_code']]['children'][$value['mun_code']]['male'] = number_format($mun_male_val);
                    $prov_data[$value['prov_code']]['children'][$value['mun_code']]['female'] = number_format($mun_female_val);
                    $prov_data[$value['prov_code']]['children'][$value['mun_code']]['paidtotal'] = number_format($mun_paid_val);
                    $prov_data[$value['prov_code']]['children'][$value['mun_code']]['amount'] = number_format($mun_paid_val * $amount);
                    
                    $prov_data[$value['prov_code']]['children'][$value['mun_code']]['unpaidmale'] = number_format($mun_umale_val);
                    $prov_data[$value['prov_code']]['children'][$value['mun_code']]['unpaidfemale'] = number_format($mun_ufemale_val);
                    $prov_data[$value['prov_code']]['children'][$value['mun_code']]['unpaidtotal'] = number_format($mun_unpaid_val);
                    $prov_data[$value['prov_code']]['children'][$value['mun_code']]['unpaidamount'] = number_format($mun_unpaid_val * $amount);
                    // municipality details
                    usort($prov_data[$value['prov_code']]['children'], function($a, $b){ return strcmp($a["name"], $b["name"]); });
                }
            // alternative

            usort($prov_data, function($a, $b){ return strcmp($a["name"], $b["name"]); });
            $region_data['r_female'] = number_format(array_sum(array_column($prov_data, 'female_int')));
            $region_data['r_male'] = number_format(array_sum(array_column($prov_data, 'male_int')));
            $region_data['r_paidtotal'] = number_format(array_sum(array_column($prov_data, 'paidtotal_int')));

            $region_data['r_unpaidfemale'] = number_format(array_sum(array_column($prov_data, 'unpaidfemale_int')));
            $region_data['r_unpaidmale'] = number_format(array_sum(array_column($prov_data, 'unpaidmale_int')));
            $region_data['r_unpaidtotal'] = number_format(array_sum(array_column($prov_data, 'unpaidtotal_int')));
            
            $region_data['total'] = number_format(array_sum(array_column($prov_data, 'total_int')));

            $region_data['paidamount'] = number_format(round(array_sum(array_column($prov_data, 'paidtotal_int')) * $amount));
            $region_data['unpaidamount'] = number_format(round(array_sum(array_column($prov_data, 'unpaidtotal_int')) * $amount));

            
            $region_served = array_sum(array_column($prov_data, 'paidtotal_int'));
            $target_val = array_sum(array_column($prov_data, 'target_val'));
            
            $region_data['accomplishment'] = round(($region_served / $target_val) * 100, 2);
            $region_amount = $region_served * $amount;

        }

        $data['month_region_served'] = $region_data;
        $data['month_served'] = $prov_data;

        response_json($data);
        //print_r($mun_count);
    }

    public function inactive_report()
    {
        $this->template->title('Inactive Report');
        $this->template->set_layout('default');
        $this->template->set_partial('header','partials/header');
        $this->template->set_partial('sidebar','partials/sidebar');
        $this->template->set_partial('aside','partials/aside');
        $this->template->set_partial('footer','partials/footer');
        $this->template->append_metadata('<script src="' . base_url("assets/js/pages/reports/inactive_report.js?ver=") . filemtime(FCPATH. "assets/js/pages/reports/inactive_report.js") . '"></script>');

        $this->template->build('reports/inactive_report');
    }

    public function get_inactive_report()
    {
        $provinces = $this->rm->get_all_provinces();
        $municipalities = $this->rm->get_all_municipalities();
        $provs = array_column($provinces, NULL,'prov_code');
        $munis = array_column($municipalities, NULL,'mun_code');
        $condition = [];

        if(isset($_POST['year']) && $_POST['year'] != ""){
            $condition['year'] = $_POST['year'];
        }

        if(isset($_POST['period']) && $_POST['period'] != ""){
            $condition['period'] = $_POST['period'];
        }

        if(isset($_POST['month']) && $_POST['month'] != ""){
            $condition['month'] = $_POST['month'];
        }

        $get_all_inactive = $this->rm->get_all_inactive($condition);
        $get_all_reasons = $this->Main->getreasonforrep(['status' => 1]);
        $new_reasons = array_column($get_all_reasons, 'name', 'id');
        $old_reasons = [
            1 => "Double Entry",
            2 => "Deceased",
            3 => "With Regular Support",
            4 => "With Pension",
            5 => "Cannot be located",
            6 => "Transferred",
            7 => "Underage (age 59 and below)",
            8 => "Not Interested",
            11 => "Improved Quality of Life",
            12 => "With Regular Income",
            13 => "Out of town",
            14 => "Not Eligible",
            15 => "OFW",
            16 => "Barangay Official"
        ];
        if(isset($_POST['year']) && $_POST['year'] != "" && $_POST['year'] > 2020){
            $reasons = $new_reasons;
        } else {
            $reasons = $old_reasons;
        }

        foreach ($get_all_inactive as $key => $value) {

            if(isset($reasons[$value['inactive_reason_id']])){

                $reason_name = $reasons[$value['inactive_reason_id']];

                if(isset($inactive_count[$value['city']][$value['inactive_reason_id']]))
                {
                    $inactive_count[$value['city']][$value['inactive_reason_id']] = $inactive_count[$value['city']][$value['inactive_reason_id']] + $value['total'];
                }
                else
                {
                    $inactive_count[$value['city']][$value['inactive_reason_id']] = $value['total'];
                }
            }

        }

        $new_mun_data = [];

        $data_list = [];
        $region_inactive = [];

        foreach ($munis as $key => $value) {
            foreach ($reasons as $key_r => $value_r) {

                $reason_id = isset($inactive_count[$value['mun_code']][$key_r]) ? $key_r : 0;

                $value[$value_r] = isset($inactive_count[$value['mun_code']][$reason_id]) ? number_format($inactive_count[$value['mun_code']][$reason_id]) : 0;
            }

            $value['name'] = $value['mun_name'];
            $value['total'] = isset($inactive_count[$value['mun_code']]) ? array_sum($inactive_count[$value['mun_code']]) : 0;
            $new_mun_data[$value['prov_code']][$value['mun_code']] = $value;
        }

        foreach ($provs as $key => $value) {
            $value['show_child'] = false;
            $value['children'] = $new_mun_data[$value['prov_code']];

                foreach ($reasons as $key_r => $value_r) {
                    $value[$value_r] = array_sum(array_column($new_mun_data[$value['prov_code']],$value_r));
                }

            $value['name'] = $value['prov_name'];
            $value['total'] = array_sum(array_column($new_mun_data[$value['prov_code']],'total'));

            $data_list[$key] = $value;

            usort($data_list[$key]['children'], function($a, $b){ return strcmp($a["name"], $b["name"]); });
        }

        usort($data_list, function($a, $b){ return strcmp($a["name"], $b["name"]); });

        foreach ($reasons as $key_r => $value_r) {
            $region_inactive[$value_r] = array_sum(array_column($data_list,$value_r));
        }
        $region_inactive['total'] = array_sum(array_column($data_list,'total'));

        $response['success'] = true;

        $response['inactive'] = $data_list;
        $response['region_inactive'] = $region_inactive;

        $response['reasons'] = $reasons;
        response_json($response);

    }
      
    public function get_actual_month_served()
    {
        ini_set('memory_limit', '-1');
        // error_reporting(E_ERROR | E_PARSE);
        extract($_POST);
        $amount = 3000;

        $provinces = $this->rm->get_all_provinces();
        $municipalities = $this->rm->get_all_municipalities();

        $prov_name = array_column($provinces, 'prov_name','prov_code');
        $mun_name = array_column($municipalities, 'mun_name','mun_code');
        
        $prov_data = [];
        $region_data = [];
        $region_count = [];
        $amount = 500;

        $get_served = $this->Main->select([
            'select'    => 'spid,prov_code,mun_code,bar_code,liquidation, MONTH(date_receive) as month',
            'table'     => 'tblpayroll',
            'type'      => 'result_array',
            'condition' => ['year' => $year,
                            'MONTH(date_receive)' => $month,
                            'liquidation <>'  => 2]   
        ]);

        if(!empty($get_served))
        {
            $get_all_general = $this->rm->get_all_general();
            $get_all_gender = array_column($get_all_general, 'gender','connum');

            $targets = $this->rm->get_all_targets(['year'=>$year,'archived'=>0]);
            $all_targets = array_column($targets, 'target','mun_code');

            $prov_count = [];
            $mun_count = [];
            $region_count = [];
            $region_targets = array_sum($all_targets);

            $region_data['target'] = number_format($region_targets);
            
            foreach ($get_served as $key => $value) {
                // get paid
                if($value['month'] <= $month && $value['liquidation'] == 1){

                    //mun_data
                    if(isset($mun_count[$value['mun_code']]['paidtotal'])){
                        $mun_count[$value['mun_code']]['paidtotal']++;
                    } else {
                        $mun_count[$value['mun_code']]['paidtotal'] = 1;
                    }

                    if(isset($get_all_gender[$value['spid']]) && strtoupper(trim($get_all_gender[$value['spid']])) == "FEMALE"){
                        if(isset($mun_count[$value['mun_code']]['female'])){
                            $mun_count[$value['mun_code']]['female']++;
                        } else {
                            $mun_count[$value['mun_code']]['female'] = 1;
                        }

                    }

                    if(isset($get_all_gender[$value['spid']]) && strtoupper(trim($get_all_gender[$value['spid']])) == "MALE"){
                        if(isset($mun_count[$value['mun_code']]['male'])){
                            $mun_count[$value['mun_code']]['male']++;
                        } else {
                            $mun_count[$value['mun_code']]['male'] = 1;
                        }

                    }

                    //mun_data


                    //prov_data
                    if(isset($prov_count[$value['prov_code']]['paidtotal'])){
                        $prov_count[$value['prov_code']]['paidtotal']++;
                    } else {
                        $prov_count[$value['prov_code']]['paidtotal'] = 1;
                    }

                    if(isset($get_all_gender[$value['spid']]) && strtoupper(trim($get_all_gender[$value['spid']])) == "FEMALE"){
                        if(isset($prov_count[$value['prov_code']]['female'])){
                            $prov_count[$value['prov_code']]['female']++;
                        } else {
                            $prov_count[$value['prov_code']]['female'] = 1;
                        }
                    }

                    if(isset($get_all_gender[$value['spid']]) && strtoupper(trim($get_all_gender[$value['spid']])) == "MALE"){
                        if(isset($prov_count[$value['prov_code']]['male'])){
                            $prov_count[$value['prov_code']]['male']++;
                        } else {
                            $prov_count[$value['prov_code']]['male'] = 1;
                        }
                    }
                    //prov_data 

                } else {

                    //mun_data


                    if(isset($get_all_gender[$value['spid']]) && strtoupper(trim($get_all_gender[$value['spid']])) != ""){

                        if(isset($mun_count[$value['mun_code']]['unpaidtotal'])){
                            $mun_count[$value['mun_code']]['unpaidtotal']++;
                        } else {
                            $mun_count[$value['mun_code']]['unpaidtotal'] = 1;
                        }

                        if(strtoupper(trim($get_all_gender[$value['spid']])) == "MALE"){
                            if(isset($mun_count[$value['mun_code']]['unpaidmale'])){
                                $mun_count[$value['mun_code']]['unpaidmale']++;
                            }else{
                                $mun_count[$value['mun_code']]['unpaidmale'] = 1;
                            }
                        }
                        if(strtoupper(trim($get_all_gender[$value['spid']])) == "FEMALE"){
                            if(isset($mun_count[$value['mun_code']]['unpaidfemale'])){
                                $mun_count[$value['mun_code']]['unpaidfemale']++;
                            }else{
                                $mun_count[$value['mun_code']]['unpaidfemale'] = 1;
                            }
                        }
                    }
                    //mun_data


                    //prov_data
                    if(isset($prov_count[$value['prov_code']]['unpaidtotal'])){
                        $prov_count[$value['prov_code']]['unpaidtotal']++;
                    } else {
                        $prov_count[$value['prov_code']]['unpaidtotal'] = 1;
                    }

                    if(isset($get_all_gender[$value['spid']]) && strtoupper(trim($get_all_gender[$value['spid']])) == "MALE"){
                        if(isset($prov_count[$value['prov_code']]['unpaidmale'])){
                            $prov_count[$value['prov_code']]['unpaidmale']++;
                        }else{
                            $prov_count[$value['prov_code']]['unpaidmale'] = 1;
                        }
                    }
                    if(isset($get_all_gender[$value['spid']]) && strtoupper(trim($get_all_gender[$value['spid']])) == "FEMALE"){
                        if(isset($prov_count[$value['prov_code']]['unpaidfemale'])){
                            $prov_count[$value['prov_code']]['unpaidfemale']++;
                        }else{
                            $prov_count[$value['prov_code']]['unpaidfemale'] = 1;
                        }
                    }
                    //prov_data

                }

                //mun_data
                if(isset($mun_count[$value['mun_code']]['total'])){
                    $mun_count[$value['mun_code']]['total']++;
                } else {
                    $mun_count[$value['mun_code']]['total'] = 1;
                }
                //mun_data

                //prov_data
                 if(isset($prov_count[$value['prov_code']]['total'])){
                    $prov_count[$value['prov_code']]['total']++;
                } else {
                    $prov_count[$value['prov_code']]['total'] = 1;
                }
                //prov_data

            }

                foreach ($municipalities as $key => $value) {
                    // province details
                        //true value
                        $prov_data[$value['prov_code']]['target_val'] = (isset($prov_data[$value['prov_code']]['target_val']))? $prov_data[$value['prov_code']]['target_val']+$all_targets[$value['mun_code']]:$all_targets[$value['mun_code']];

                        $prov_total_val = (isset($prov_count[$value['prov_code']]['total']))? $prov_count[$value['prov_code']]['total'] : 0;

                        $prov_male_val = (isset($prov_count[$value['prov_code']]['male']))? $prov_count[$value['prov_code']]['male'] : 0;
                        $prov_female_val = (isset($prov_count[$value['prov_code']]['female']))? $prov_count[$value['prov_code']]['female'] : 0;
                        $prov_paid_val = (isset($prov_count[$value['prov_code']]['paidtotal']))? $prov_count[$value['prov_code']]['paidtotal'] : 0;

                        $prov_umale_val = (isset($prov_count[$value['prov_code']]['unpaidmale']))? $prov_count[$value['prov_code']]['unpaidmale'] : 0;
                        $prov_ufemale_val = (isset($prov_count[$value['prov_code']]['unpaidfemale']))? $prov_count[$value['prov_code']]['unpaidfemale'] : 0;
                        $prov_unpaid_val = (isset($prov_count[$value['prov_code']]['unpaidtotal']))? $prov_count[$value['prov_code']]['unpaidtotal'] : 0;

                        //true value

                    $prov_data[$value['prov_code']]['name'] = $prov_name[$value['prov_code']];
                    $prov_data[$value['prov_code']]['total'] =  number_format($prov_total_val);

                    $prov_data[$value['prov_code']]['paidtotal'] =  number_format($prov_paid_val);
                    $prov_data[$value['prov_code']]['female'] =  number_format($prov_female_val);
                    $prov_data[$value['prov_code']]['male'] = number_format($prov_male_val);
                    $prov_data[$value['prov_code']]['amount'] = number_format($prov_paid_val * $amount);

                    $prov_data[$value['prov_code']]['unpaidtotal'] =  number_format($prov_unpaid_val);
                    $prov_data[$value['prov_code']]['unpaidfemale'] =  number_format($prov_ufemale_val);
                    $prov_data[$value['prov_code']]['unpaidmale'] = number_format($prov_umale_val);
                    $prov_data[$value['prov_code']]['unpaidamount'] = number_format($prov_unpaid_val * $amount);

                    $prov_data[$value['prov_code']]['total_int'] =  $prov_total_val;
                    $prov_data[$value['prov_code']]['paidtotal_int'] =  $prov_paid_val;
                    $prov_data[$value['prov_code']]['female_int'] =  $prov_female_val;
                    $prov_data[$value['prov_code']]['male_int'] = $prov_male_val;
                    $prov_data[$value['prov_code']]['amount_int'] = $prov_paid_val * $amount;

                    $prov_data[$value['prov_code']]['unpaidtotal_int'] =  $prov_unpaid_val;
                    $prov_data[$value['prov_code']]['unpaidfemale_int'] =  $prov_umale_val;
                    $prov_data[$value['prov_code']]['unpaidmale_int'] = $prov_ufemale_val;
                    $prov_data[$value['prov_code']]['unpaidamount_int'] = $prov_unpaid_val * $amount;

                    $prov_data[$value['prov_code']]['mun_show'] = false;
                    $prov_data[$value['prov_code']]['target'] =  number_format($prov_data[$value['prov_code']]['target_val']);
                    // province details


                    // municipality details
                        //true value
                        $mun_target = isset($all_targets[$value['mun_code']]) ? $all_targets[$value['mun_code']] : 0;
                        $mun_total_val = isset($mun_count[$value['mun_code']]['total']) ? $mun_count[$value['mun_code']]['total'] : 0;

                        $mun_male_val = isset($mun_count[$value['mun_code']]['male']) ? $mun_count[$value['mun_code']]['male'] : 0;
                        $mun_female_val = isset($mun_count[$value['mun_code']]['female']) ? $mun_count[$value['mun_code']]['female'] : 0;
                        $mun_paid_val = isset($mun_count[$value['mun_code']]['paidtotal']) ? $mun_count[$value['mun_code']]['paidtotal'] : 0;
                        
                        $mun_umale_val = isset($mun_count[$value['mun_code']]['unpaidmale']) ? $mun_count[$value['mun_code']]['unpaidmale'] : 0;
                        $mun_ufemale_val = isset($mun_count[$value['mun_code']]['unpaidfemale']) ? $mun_count[$value['mun_code']]['unpaidfemale'] : 0;
                        $mun_unpaid_val = isset($mun_count[$value['mun_code']]['unpaidtotal']) ? $mun_count[$value['mun_code']]['unpaidtotal'] : 0;
                        $prov_unpaid_val = isset($prov_count[$value['mun_code']]['unpaidtotal']) ? $prov_count[$value['mun_code']]['unpaidtotal'] : 0;
                        //true value


                    $prov_data[$value['prov_code']]['children'][$value['mun_code']]['name'] = $value['mun_name'];
                    $prov_data[$value['prov_code']]['children'][$value['mun_code']]['target'] = number_format($mun_target);
                    $prov_data[$value['prov_code']]['children'][$value['mun_code']]['total'] = number_format($mun_total_val);

                    $prov_data[$value['prov_code']]['children'][$value['mun_code']]['male'] = number_format($mun_male_val);
                    $prov_data[$value['prov_code']]['children'][$value['mun_code']]['female'] = number_format($mun_female_val);
                    $prov_data[$value['prov_code']]['children'][$value['mun_code']]['paidtotal'] = number_format($mun_paid_val);
                    $prov_data[$value['prov_code']]['children'][$value['mun_code']]['amount'] = number_format($mun_paid_val * $amount);
                    
                    $prov_data[$value['prov_code']]['children'][$value['mun_code']]['unpaidmale'] = number_format($mun_umale_val);
                    $prov_data[$value['prov_code']]['children'][$value['mun_code']]['unpaidfemale'] = number_format($mun_ufemale_val);
                    $prov_data[$value['prov_code']]['children'][$value['mun_code']]['unpaidtotal'] = number_format($mun_unpaid_val);
                    $prov_data[$value['prov_code']]['children'][$value['mun_code']]['unpaidamount'] = number_format($mun_unpaid_val * $amount);
                    // municipality details
                    usort($prov_data[$value['prov_code']]['children'], function($a, $b){ return strcmp($a["name"], $b["name"]); });
                }
            // alternative

            usort($prov_data, function($a, $b){ return strcmp($a["name"], $b["name"]); });
            $region_data['r_female'] = number_format(array_sum(array_column($prov_data, 'female_int')));
            $region_data['r_male'] = number_format(array_sum(array_column($prov_data, 'male_int')));
            $region_data['r_paidtotal'] = number_format(array_sum(array_column($prov_data, 'paidtotal_int')));

            $region_data['r_unpaidfemale'] = number_format(array_sum(array_column($prov_data, 'unpaidfemale_int')));
            $region_data['r_unpaidmale'] = number_format(array_sum(array_column($prov_data, 'unpaidmale_int')));
            $region_data['r_unpaidtotal'] = number_format(array_sum(array_column($prov_data, 'unpaidtotal_int')));
            
            $region_data['total'] = number_format(array_sum(array_column($prov_data, 'total_int')));

            $region_data['paidamount'] = number_format(round(array_sum(array_column($prov_data, 'paidtotal_int')) * $amount));
            $region_data['unpaidamount'] = number_format(round(array_sum(array_column($prov_data, 'unpaidtotal_int')) * $amount));

            
            $region_served = array_sum(array_column($prov_data, 'paidtotal_int'));
            $target_val = array_sum(array_column($prov_data, 'target_val'));
            
            $region_data['accomplishment'] = round(($region_served / $target_val) * 100, 2);
            $region_amount = $region_served * $amount;

        }

        $data['month_region_served'] = $region_data;
        $data['month_served'] = $prov_data;

        response_json($data);
        //print_r($mun_count);
    }


//////// END REPLACEMENT MONITORING 
}

/* End of file Common.php */
