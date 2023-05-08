<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');


class Dashboard extends CI_Controller {

    public function __construct()
	{
		parent::__construct();
		$this->load->helper('form');
		$this->load->library('form_validation');		
		$this->form_validation->set_error_delimiters('','');
		$this->load->model('Main');		
		$this->load->model("dash_model","dm");

	}

	public function index()
	{
		$this->template->title('SPIS Dashboard');
		$this->template->set_layout('default');
	    $this->template->set_partial('header','partials/header');
	    $this->template->set_partial('sidebar','partials/sidebar');
	    $this->template->set_partial('aside','partials/aside');
	    $this->template->set_partial('footer','partials/footer');
	    $this->template->append_metadata('<script src="' . base_url("assets/js/pages/dashboard.js?ver=") . filemtime(FCPATH. "assets/js/pages/dashboard.js") . '"></script>');

	    $this->template->build('dash_view');
	    	
    }	
    
    
	public function get_benes_data()
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
            if(in_array($period, [5,6])){
                $type_sem_quart = "Semester";
                $period_condition = ($period == 5)?1:2;
                $amount = 3000;
            }
            else{
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

        //Get Libraries
        $provinces = $this->dm->get_all_provinces();
        $municipalities = $this->dm->get_all_municipalities();
        $prov_names = array_column($provinces, 'prov_name','prov_code');
        $mun_names = array_column($municipalities, 'mun_name','mun_code');

        //Active and For Replacement Graph Data
        $get_all_general = $this->dm->get_all_general("province,sp_status,connum,gender");
        $all_general = array_column($get_all_general, 'sp_status','connum');
        $_active_data = []; $_active_total = 0;
        $_forrep_data = []; $_forrep_total = 0;

        foreach ($get_all_general as $key => $value) {
            $p_code = $value['province'];
            $p_name = isset($prov_names[$p_code]) ? $prov_names[$p_code] : "";

            $a_status = strtolower($value['sp_status']);

            if($a_status == "forreplacement"){
                if(isset($_forrep_data[$p_name])){  $_forrep_data[$p_name] += 1;
                }else{  $_forrep_data[$p_name] = 1; }
                $_forrep_total ++;
            }
            
            if(strtoupper($a_status) == "ACTIVE" || strtoupper($a_status) == "ADDITIONAL"){
                if(isset($_active_data[$p_name])){  $_active_data[$p_name] += 1;
                }else{  $_active_data[$p_name] = 1; }
                $_active_total ++;
            }
        }
        
        ksort($_active_data);
        $data['active'] = array(
            "data_keys" => array_keys($_active_data),
            "data_values" => array_values($_active_data),
            "total" => number_format($_active_total)
        );
        
        ksort($_forrep_data);
        $data['forrep'] = array(
            "data_keys" => array_keys($_forrep_data),
            "data_values" => array_values($_forrep_data),
            "total" => number_format($_forrep_total)
        );

        //Waitlist Data
        $get_all_waitlist = $this->dm->get_all_waitlist();
        $_waitlist_data = []; $_waitlist_total = 0;

        foreach ($get_all_waitlist as $key => $value) {
            $p_code = $value['prov_code'];
            $p_name = $prov_names[$p_code];

            if(isset($_waitlist_data[$p_name])){  $_waitlist_data[$p_name] += 1;
            }else{  $_waitlist_data[$p_name] = 1; }
            $_waitlist_total ++;
        }
        ksort($_waitlist_data);

        $data['waitlist'] = array(
            "data_keys" => array_keys($_waitlist_data),
            "data_values" => array_values($_waitlist_data),
            "total" => number_format($_waitlist_total)
        );

        //Target Graph Data
        $targets = $this->dm->get_all_targets(['quarter' => $period_target_cond, 'semester'=>$semester_target_cond,'year'=>$year,'archived'=>0]);
        $all_targets = array_column($targets, 'target','mun_code');
        $_target_data = []; $_target_total = 0;
        foreach ($municipalities as $key => $value) {
            $p_code = $value['prov_code'];
            $p_name = $prov_names[$p_code];

            if(isset($_target_data[$p_name])){
                $_target_data[$p_name] += (isset($all_targets[$value['mun_code']]))? $all_targets[$value['mun_code']] : 0;
            }else{
                $_target_data[$p_name] = (isset($all_targets[$value['mun_code']]))? $all_targets[$value['mun_code']] : 0;
            }
            $_target_total += (isset($all_targets[$value['mun_code']]))? $all_targets[$value['mun_code']] : 0;
        }

        ksort($_target_data);
        $data['targets'] =  array(
            "data" => $_target_data,
            "data_keys" => array_keys($_target_data),
            "data_values" => array_values($_target_data),
            "total" => number_format($_target_total)
        );

        //Served Table and Graph (main Data)
        $condition = ["mode_of_payment"   => $type_sem_quart,
                    "period"   => $period_condition,
                    "year"      => $year,
                    "liquidation"      => 1];

        $total_served = $this->dm->get_total_served($condition);
        $served_count = [];
        $total_amount = 0;
        $unpaid_progress = 0;
        $region_unpaid = 0;
        $region_served = 0;
        $region_targets = 0;
        $accomplishment = 0;
        $chart_label = [];
        $chart_target = [];
        $chart_paid = [];
        $chart_accomp = [];
        $region_targets = array_sum($all_targets);
       

        if(!empty($total_served)){
            $prov_count = [];
            $region_served = count($total_served);
            $total_amount = $region_served * $amount;
            
            $accomplishment = round(($region_served / $region_targets) * 100, 2);
            $region_unpaid = $region_targets - $region_served;
            $unpaid_progress = round(($region_unpaid / $region_targets) * 100, 2);
            
            foreach ($total_served as $key => $value) {
                if(isset($all_general[$value['spid']]))
                {
                    if(isset($prov_count[$value['prov_code']])){
                        $prov_count[$value['prov_code']]++;
                    }else{
                        $prov_count[$value['prov_code']] = 1;
                    }
                }
            }

        }
        $chart_label = [];
        $chart_target = [];
        $chart_paid = [];
        $chart_accomp = [];
        foreach($provinces as $key => $value){

            $prov_target = $_target_data[$value["prov_name"]];
            $total_paid = isset($prov_count[$value['prov_code']]) ? $prov_count[$value['prov_code']] : 0;
            $total_amount = $total_paid * $amount;
            $prov_accom = round(($total_paid / $prov_target) * 100, 2);

            $chart_label[] = $value["prov_name"];
            $chart_target[] = $prov_target;
            $chart_paid[] = $total_paid;
            $chart_accomp[] = $prov_accom;

            $served_count[] = array(
                "province" => $value["prov_name"],
                "total_target" => number_format($prov_target),
                "total_paid" => number_format($total_paid),
                "total_amount" => "₱" . number_format($total_amount),
                "accomplishment" => $prov_accom . "%",
            );
        }

        $data['served'] = array(
            "table_data" => $served_count,
            "table_labels" => $chart_label,
            "table_target" => $chart_target,
            "table_paid" => $chart_paid,
            "table_accom" => $chart_accomp,
            "region_served" => $region_served,
            "region_unpaid" => $region_unpaid,
            "region_targets" => $region_targets,
            "total_amount" => $total_amount,
            "accomplishment" => $accomplishment,
            "unpaid_progress" => $unpaid_progress,
            "year" => $year,
            "period" => $period_condition,
        );

        response_json($data);

    }

    
	public function search_served_data()
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
            if(in_array($period, [5,6])){
                $type_sem_quart = "Semester";
                $period_condition = ($period == 5)?1:2;
                $amount = 3000;
            }
            else{
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

        //Get Libraries
        $provinces = $this->dm->get_all_provinces();
        $municipalities = $this->dm->get_all_municipalities();
        $prov_names = array_column($provinces, 'prov_name','prov_code');
        $mun_names = array_column($municipalities, 'mun_name','mun_code');
        $get_all_general = $this->dm->get_all_general("province,sp_status,connum,gender");
        $all_general = array_column($get_all_general, 'sp_status','connum');

        //Target Graph Data
        $targets = $this->dm->get_all_targets(['quarter' => $period_target_cond, 'semester'=>$semester_target_cond,'year'=>$year,'archived'=>0]);
        $all_targets = array_column($targets, 'target','mun_code');
        $_target_data = []; $_target_total = 0;
        foreach ($municipalities as $key => $value) {
            $p_code = $value['prov_code'];
            $p_name = $prov_names[$p_code];

            if(isset($_target_data[$p_name])){
                $_target_data[$p_name] += (isset($all_targets[$value['mun_code']]))? $all_targets[$value['mun_code']] : 0;
            }else{
                $_target_data[$p_name] = (isset($all_targets[$value['mun_code']]))? $all_targets[$value['mun_code']] : 0;
            }
            $_target_total += (isset($all_targets[$value['mun_code']]))? $all_targets[$value['mun_code']] : 0;
        }

        //Served Table and Graph (main Data)
        $condition = ["mode_of_payment"   => $type_sem_quart,
                    "period"   => $period_condition,
                    "year"      => $year,
                    "liquidation" => 1];

        $total_served = $this->dm->get_total_served($condition);
        $served_count = [];
        $total_amount = 0;
        $unpaid_progress = 0;
        $region_unpaid = 0;
        $region_served = 0;
        // $region_targets = 0;
        $accomplishment = 0;
        $chart_label = [];
        $chart_target = [];
        $chart_paid = [];
        $chart_accomp = [];
        $region_targets = array_sum($all_targets);

        if(!empty($total_served)){
            $prov_count = [];
            $region_served = count($total_served);
            $total_amount = $region_served * $amount;
            $accomplishment = round(($region_served / $region_targets) * 100, 2);
            $region_unpaid = $region_targets - $region_served;
            $unpaid_progress = round(($region_unpaid / $region_targets) * 100, 2);

            foreach ($total_served as $key => $value) {
                if(isset($all_general[$value['spid']]))
                {
                    if(isset($prov_count[$value['prov_code']])){
                        $prov_count[$value['prov_code']]++;
                    }else{
                        $prov_count[$value['prov_code']] = 1;
                    }
                }
            }

            foreach($provinces as $key => $value){

                $prov_target = $_target_data[$value["prov_name"]];
                $total_paid = isset($prov_count[$value['prov_code']]) ? $prov_count[$value['prov_code']] : 0;
                $total_amount = $total_paid * $amount;
                $prov_accom = round(($total_paid / $prov_target) * 100, 2);

                $chart_label[] = $value["prov_name"];
                $chart_target[] = $prov_target;
                $chart_paid[] = $total_paid;
                $chart_accomp[] = $prov_accom;

                $served_count[] = array(
                    "province" => $value["prov_name"],
                    "total_target" => number_format($prov_target),
                    "total_paid" => number_format($total_paid),
                    "total_amount" => "₱" . number_format($total_amount),
                    "accomplishment" => $prov_accom . "%",
                );
            }
        }

        $data['served'] = array(
            "table_data" => $served_count,
            "table_labels" => $chart_label,
            "table_target" => $chart_target,
            "table_paid" => $chart_paid,
            "table_accom" => $chart_accomp,
            "region_served" => $region_served,
            "region_unpaid" => $region_unpaid,
            "region_targets" => $region_targets,
            "total_amount" => $total_amount,
            "accomplishment" => $accomplishment,
            "unpaid_progress" => $unpaid_progress
        );

        response_json($data);

    }
}

