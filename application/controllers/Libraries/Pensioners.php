<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pensioners extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Sp_libraries_model','sl');
        $this->load->model('Main');

        checkLogin();
    }

    public function index()
    {
        $data['title'] = "Pensioner";
        $data['vueid'] = "pensioner";
        $this->template->set_layout('default');
        $this->template->set_partial('header', 'partials/header');
        $this->template->set_partial('sidebar', 'partials/sidebar');
        $this->template->set_partial('aside', 'partials/aside');
        $this->template->set_partial('footer', 'partials/footer');
        $this->template->append_metadata('<script src="' . base_url("assets/js/pages/libraries/pensioners.js?ver=") . filemtime(FCPATH. "assets/js/pages/libraries/pensioners.js") . '"></script>');
        $this->template->build('libraries/pensioners', $data);

    }

    public function save()
    {
        extract($_POST);

        $success = false;
        $semester = (in_array($quarter, [1,2,5])) ? 1 : 2;
        $insdata = [
            "mun_code"     => $mun_code,
            "year" => $year,
            "semester" => $semester,
            "quarter"     => $quarter,
            "target"     => $target,
        ];

        if ($quarter == 1) {
            $qtrsemlogs = "1st QUARTER";
        } else if ($quarter == 2) {
            $qtrsemlogs = "2nd QUARTER";
        } else if ($quarter == 3) {
            $qtrsemlogs = "3rd QUARTER";
        } else if ($quarter == 4) {
            $qtrsemlogs = "4th QUARTER";
        }

        
        $muni_name =  $this->Main->select([
            'select'    => '*',
            'table'     => 'tblmunicipalities',
            'type'      => 'row',
            'condition' => ['mun_code' => $mun_code],
        ]);

        $success =  $this->Main->insert('tbltarget', $insdata);
        userLogs(sesdata('id'), sesdata('fullname'), "ADD TARGET", "$muni_name->mun_name ($qtrsemlogs) Target Added: $target");

        $response = array(
            'success' => $success,
            'message' => "Success",
        );

        response_json($response);
    }

    public function clone_pensioners()
    {
        extract($_POST);

        $clone_targets = $this->sl->get_targets(['archived'=>0,'`year`'=>$prev_year,'`semester`'=>$prev_sem]);

        $pref_array = ['new_year' => $new_year, 'new_sem' => $new_sem];
        $new_data = array_map(function($arr) use ($pref_array){
            unset($arr->id,$arr->mun_name,$arr->prov_name,$arr->prov_code);
            $arr->quarter = 1;
            $arr->year = $pref_array['new_year'];
            $arr->semester = $pref_array['new_sem'];
            return $arr;
        }, $clone_targets);


        $success = false;
        $success =  $this->Main->insertbatch('tbltarget', $new_data);

        $response = array(
            'success' => $success,
            'message' => "Success",
        );

        response_json($response);
    }

    public function get_pensioners()
    {
        $condition = [];

        if($this->input->post('prov_code') !== null && $this->input->post('prov_code') != "")
        {
            $condition['p.prov_code'] = $this->input->post("prov_code");
        }

        if($this->input->post('mun_code') !== null && $this->input->post('mun_code') != "")
        {
            $condition['t.mun_code'] = $this->input->post("mun_code");
        }

        if($this->input->post('year') !== null && $this->input->post('year') != "")
        {
            $condition['t.year'] = $this->input->post("year");
        }
        
        if($this->input->post('semester') !== null && $this->input->post('semester') != "")
        {
            $condition['t.semester'] = $this->input->post("semester");
        }

        if(empty($condition))
        {
           $targets = $this->sl->get_targets(['archived'=>0]); 
        }
        else
        {
            $condition['t.archived'] = 0;
            $targets = $this->sl->get_targets($condition);
        }

		// array_multisort(array_column($targets, 'prov_name'), SORT_ASC,array_column($targets, 'mun_name'), SORT_ASC, $targets);

        $data = array(
            'count' => count($targets),
            'data' => $targets,
        );

        response_json($data);
    }

    public function update_pensioners()
    {
        extract($_POST);
        $success = false;

        $update_data = [
            // "mun_code"     => $mun_code,
            // "year" => $year,
            "target"     => $target,
        ];



        $success =  $this->Main->update('tbltarget', ['id' => $id], $update_data);

        // logs
        if ($quarter == 1) {
            $qtrsemlogs = "1st QUARTER";
        } else if ($quarter == 2) {
            $qtrsemlogs = "2nd QUARTER";
        } else if ($quarter == 3) {
            $qtrsemlogs = "3rd QUARTER";
        } else if ($quarter == 4) {
            $qtrsemlogs = "4th QUARTER";
        }

        
        $muni_name =  $this->Main->select([
            'select'    => '*',
            'table'     => 'tblmunicipalities',
            'type'      => 'row',
            'condition' => ['mun_code' => $mun_code],
        ]);
        userLogs(sesdata('id'), sesdata('fullname'), "EDIT TARGET", "$muni_name->mun_name ($qtrsemlogs) Target Updated: $target");
        // logs

        $response = array(
            'success' => $success,
            'message' => "Success",
        );

        response_json($response);
    }

    public function delete_pensioners()
    {
        extract($_POST);
        $success = false;
        $update_data = ['archived' => 1];
        $success =  $this->Main->update('tbltarget', ['id' => $id], $update_data);

        // logs
        if ($quarter == 1) {
            $qtrsemlogs = "1st QUARTER";
        } else if ($quarter == 2) {
            $qtrsemlogs = "2nd QUARTER";
        } else if ($quarter == 3) {
            $qtrsemlogs = "3rd QUARTER";
        } else if ($quarter == 4) {
            $qtrsemlogs = "4th QUARTER";
        }

        
        $muni_name =  $this->Main->select([
            'select'    => '*',
            'table'     => 'tblmunicipalities',
            'type'      => 'row',
            'condition' => ['mun_code' => $mun_code],
        ]);
        userLogs(sesdata('id'), sesdata('fullname'), "DELETE TARGET", "$muni_name->mun_name ($qtrsemlogs) Target Deleted");
        // logs

        $response = array(
            'success' => $success,
            'message' => "Success",
        );

        response_json($response);
    }


    public function get_provinces()
    {
        $province_params = [
            'select'    => '*',
            'table'     => 'tblprovinces',
            'type'      => '',
            'condition' => [],
        ];

        $data['provinces'] = $this->Main->select($province_params);
        response_json($data);
    }

    public function get_municipalities()
    {
        $municipalities_params = [
            'select'    => '*',
            'table'     => 'tblmunicipalities',
            'type'      => '',
            'condition' => [],
        ];

        $data['municipalities'] = $this->Main->select($municipalities_params);
        response_json($data);
    }

    public function get_target_years()
    {
        $years_params = [
            'select'    => '`year`',
            'table'     => 'tbltarget',
            'type'      => '',
            'group_by'  => '`year`',
            'condition' => [],
        ];

        $data['years'] = $this->Main->select($years_params);
        response_json($data);
    }

}

/* End of file Common.php */
