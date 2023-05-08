<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Living_arrangement extends CI_Controller
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
        $this->template->append_metadata('<script src="' . base_url("assets/js/pages/libraries/living_arrangement.js?ver=") . filemtime(FCPATH. "assets/js/pages/libraries/living_arrangement.js") . '"></script>');
        $this->template->build('libraries/living_arrangement', $data);

    }

    public function save_living_arrangement()
    {
        extract($_POST);

        $success = false;
        $insdata = [
            "name"     => $name,
            "date_created"     => date("Y-m-d H:i:s"),
        ];
        
        $success =  $this->Main->insert('tbllivingarrangement', $insdata);

        $response = array(
            'success' => $success,
            'message' => "Success",
        );

        response_json($response);
    }

    public function get_living_arrangement()
    {
        $living_arrangement = $this->sl->get_living_arrangement();

        $data = array(
            'count' => count($living_arrangement),
            'data' => $living_arrangement,
        );

        response_json($data);
    }

    public function update_living_arrangement()
    {
        extract($_POST);
        $success = false;

        $update_data = [
            "name" => $name,
            "status"     => $status,
        ];

        $success =  $this->Main->update('tbllivingarrangement', ['id' => $id], $update_data);

        $response = array(
            'success' => $success,
            'message' => "Success",
        );

        response_json($response);
    }

}

/* End of file Common.php */
