<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class RequestForm extends CI_Controller
{
	private $pager_settings;
	public function __construct()
	{
		parent::__construct();

		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('', '');

		$this->load->model('Main', 'Main');

		$this->load->library('csvimport');
		$this->load->library('PHPExcel');

		checkLogin();
	}

	public function index()
	{
		$data['app_active'] = true;
		$this->template->title('Social Pension Active Beneficiaries');
		$this->template->set_layout('default');
		$this->template->set_partial('header', 'partials/header');
		$this->template->set_partial('sidebar', 'partials/sidebar');
		$this->template->set_partial('aside', 'partials/aside');
		$this->template->set_partial('footer', 'partials/footer');
		$this->template->append_metadata('<script src="' . base_url("assets/js/pages/requestform.js?ver=") . filemtime(FCPATH . "assets/js/pages/requestform.js") . '"></script>');

		$this->template->build('requestform_view', $data);
	}

	function getListOfUsers()
	{
		$users = $this->Main->select([
			'select'    => '*',
			'type'      => '',
			'table'     => 'tblusers',
			'condition' => ['active_status' => 1],
		]);

		$response['users'] = $users;

		response_json($response);
	}

	function getAllRequest()
	{
		$requests = $this->Main->select([
			'select'    => '*,(SELECT CONCAT(fname," ",mname," ",lname) FROM TBLUSERS WHERE id = tblchangerequest.req_by) AS req_by,(SELECT CONCAT(fname," ",mname," ",lname) FROM TBLUSERS WHERE id = tblchangerequest.assign_to) AS assign_to_name',
			'type'      => '',
			'table'     => 'tblchangerequest',
		]);
		$response['sesID'] = sesdata('id');
		$response['role'] = sesdata('role');
		$response['requests'] = $requests;

		response_json($response);
	}

	function addNewRequest()
	{
		$add = $_POST;
		$query = $this->db->insert('tblchangerequest', $add);
		if ($query == true) {
			$data = array(
				'success' => true
			);
		} else {
			$data = array(
				'success' => false
			);
		}

		echo json_encode($data);
	}

	function getAllRequestByStatus()
	{
		$status = $this->input->post('keyStatus');
		if (!empty($status)) {
			$where['status'] = $status;
		} else {
			$where = '';
		}
		$requests = $this->Main->select([
			'select'    => '*,(SELECT CONCAT(fname," ",mname," ",lname) FROM TBLUSERS WHERE id = tblchangerequest.req_by) AS req_by,(SELECT CONCAT(fname," ",mname," ",lname) FROM TBLUSERS WHERE id = tblchangerequest.assign_to) AS assign_to_name',
			'type'      => '',
			'table'     => 'tblchangerequest',
			'condition' => $where
		]);
		$response['requests'] = $requests;
		response_json($response);
	}

	function getAllRequestByDate()
	{

		$date = $this->input->post('keyDate');
		if (!empty($date)) {
			$where['duedate'] = $date;
		} else {
			$where = '';
		}
		$requests = $this->Main->select([
			'select'    => '*,(SELECT CONCAT(fname," ",mname," ",lname) FROM TBLUSERS WHERE id = tblchangerequest.req_by) AS req_by,(SELECT CONCAT(fname," ",mname," ",lname) FROM TBLUSERS WHERE id = tblchangerequest.assign_to) AS assign_to_name',
			'type'      => '',
			'table'     => 'tblchangerequest',
			'condition' => $where
		]);
		$response['date'] = $date;
		$response['requests'] = $requests;
		response_json($response);
	}
	
	function updateRequestForm(){
		$id = $this->input->post('updateId');
		$reqDetails = $this->input->post('updateReqDetails');
		$status = $this->input->post('updateStatus');
		$actionTaken = $this->input->post('updateActionTaken');
		
		$query = $this->Main->updateReqForm($id,$reqDetails,$status,$actionTaken);
		$response['success'] = $query;
		response_json($response);
	}
}
