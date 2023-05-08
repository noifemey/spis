<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Reasons extends CI_Controller
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

        $data['title'] = "Inactive Reasons";
        $data['vueid'] = "Inactive Reasons";
        $this->template->set_layout('default');
        $this->template->set_partial('header', 'partials/header');
        $this->template->set_partial('sidebar', 'partials/sidebar');
        $this->template->set_partial('aside', 'partials/aside');
        $this->template->set_partial('footer', 'partials/footer');
        $this->template->append_metadata('<script src="' . base_url("assets/js/pages/libraries/reasons.js?ver=") . filemtime(FCPATH. "assets/js/pages/libraries/reasons.js") . '"></script>');
        $this->template->build('libraries/reasons', $data);

    }

    public function save_reasons()
    {
        extract($_POST);

        $success = false;
        $insdata = [
            "name"     => $name,
            "date_created"     => date("Y-m-d H:i:s"),
        ];
        
        $success =  $this->Main->insert('tblinactivereason', $insdata);

        $response = array(
            'success' => $success,
            'message' => "Success",
        );

        response_json($response);
    }

    public function get_reasons()
    {
        $reasons = $this->sl->get_reasons();

        $data = array(
            'count' => count($reasons),
            'data' => $reasons,
        );

        response_json($data);
    }

    public function update_reasons()
    {
        extract($_POST);
        $success = false;

        $update_data = [
            "name" => $name,
            "status"     => $status,
        ];

        $success =  $this->Main->update('tblinactivereason', ['id' => $id], $update_data);

        $response = array(
            'success' => $success,
            'message' => "Success",
        );

        response_json($response);
    }

}

/* End of file Common.php */
