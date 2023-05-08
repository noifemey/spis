<?php
defined('BASEPATH') or exit('No direct script access allowed');

class House_type extends CI_Controller
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
        $data['title'] = "House Type";
        $data['vueid'] = "House Type";
        $this->template->set_layout('default');
        $this->template->set_partial('header', 'partials/header');
        $this->template->set_partial('sidebar', 'partials/sidebar');
        $this->template->set_partial('aside', 'partials/aside');
        $this->template->set_partial('footer', 'partials/footer');
        $this->template->append_metadata('<script src="' . base_url("assets/js/pages/libraries/house_type.js?ver=") . filemtime(FCPATH. "assets/js/pages/libraries/house_type.js") . '"></script>');
        $this->template->build('libraries/house_type', $data);

    }

    public function save_house_type()
    {
        extract($_POST);

        $success = false;
        $insdata = [
            "name"     => $name,
            "date_created"     => date("Y-m-d H:i:s"),
        ];
        
        $success =  $this->Main->insert('tblhousetype', $insdata);

        $response = array(
            'success' => $success,
            'message' => "Success",
        );

        response_json($response);
    }

    public function get_house_type()
    {
        $house_type = $this->sl->get_house_type();

        $data = array(
            'count' => count($house_type),
            'data' => $house_type,
        );

        response_json($data);
    }

    public function update_house_type()
    {
        extract($_POST);
        $success = false;

        $update_data = [
            "name" => $name,
            "status"     => $status,
        ];

        $success =  $this->Main->update('tblhousetype', ['id' => $id], $update_data);

        $response = array(
            'success' => $success,
            'message' => "Success",
        );

        response_json($response);
    }

}

/* End of file Common.php */
