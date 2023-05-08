<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Signatories extends CI_Controller
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
        $this->template->append_metadata('<script src="' . base_url("assets/js/pages/libraries/signatories.js?ver=") . filemtime(FCPATH. "assets/js/pages/libraries/signatories.js") . '"></script>');
        $this->template->build('libraries/signatories', $data);

    }

    public function save()
    {
        extract($_POST);

        $success = false;
        $insdata = [
            "office_id"     => $office_id,
            "div_shortname" => $div_shortname,
            "div_title"     => $div_title,
            "div_head_emp_id"     => $head_id,
        ];
        
        $success =  $this->Main->insert('lib_division', $insdata);

        $response = array(
            'success' => $success,
            'message' => "Success",
        );

        response_json($response);
    }

    public function get_signatories()
    {
        $signatories = $this->sl->get_signatories();

        $data = array(
            'count' => count($signatories),
            'data' => $signatories,
        );

        response_json($data);
    }

    public function update_signatories()
    {
        $update_data = $_POST;

        extract($_POST);
        $success = false;

        $success =  $this->Main->update('tblsignatories', ['id' => $id], $update_data);

        $response = array(
            'success' => $success,
            'message' => "Success",
        );

        response_json($response);
    }

}

/* End of file Common.php */
