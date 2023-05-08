<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Marital_status extends CI_Controller
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
        $data['title'] = "Marital Status";
        $data['vueid'] = "Marital Status";
        $this->template->set_layout('default');
        $this->template->set_partial('header', 'partials/header');
        $this->template->set_partial('sidebar', 'partials/sidebar');
        $this->template->set_partial('aside', 'partials/aside');
        $this->template->set_partial('footer', 'partials/footer');
        $this->template->append_metadata('<script src="' . base_url("assets/js/pages/libraries/marital_status.js?ver=") . filemtime(FCPATH. "assets/js/pages/libraries/marital_status.js") . '"></script>');
        $this->template->build('libraries/marital_status', $data);

    }

    public function save_marital_status()
    {
        extract($_POST);

        $success = false;
        $insdata = [
            "name"     => $name,
            "date_created"     => date("Y-m-d H:i:s"),
        ];
        
        $success =  $this->Main->insert('tblmaritalstatus', $insdata);

        $response = array(
            'success' => $success,
            'message' => "Success",
        );

        response_json($response);
    }

    public function get_marital_status()
    {
        $marital_status = $this->sl->get_marital_status();

        $data = array(
            'count' => count($marital_status),
            'data' => $marital_status,
        );

        response_json($data);
    }

    public function update_marital_status()
    {
        extract($_POST);
        $success = false;

        $update_data = [
            "name" => $name,
            "status"     => $status,
        ];

        $success =  $this->Main->update('tblmaritalstatus', ['id' => $id], $update_data);

        $response = array(
            'success' => $success,
            'message' => "Success",
        );

        response_json($response);
    }

}

/* End of file Common.php */
