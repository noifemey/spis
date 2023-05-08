<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class API extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Main');		
		$this->load->model('Api_model', 'api');
	}

	public function getLoginData()
	{	
		$_CI =& get_instance();
		$data = $this->session->all_userdata();
		response_json($data);
	}

	public function getProvinces($code = "")
	{

		$condition = array();
		if (!empty($code)) {
			$condition = array("prov_code" => $code);
		}
		$provinces_query  = array(
			'select'           => "*",
			'table'            => 'provinces',
			'condition'        => $condition,
			'type'             => "",
			'order' => array(
				'col' => 'prov_name',
				'order_by' => "ASC",
			),

		);
		$result = 	$this->Main->select($provinces_query);

		$data = array(
			"success" => true,
			"message" => "Success",
			"data" => $result
		);
		response_json($data);
	}

	public function getMunicipalities($code = "")
	{
		$code = !empty($this->input->post('prov_code')) ? $this->input->post('prov_code') : $code;
		$condition = array();
		$result = array();
		if (!empty($code)) {
			if (is_array($code)) {
				$result = 	!empty($code) ? $this->api->getmunicipalities($code) : array();
			} else {
				$condition = array("prov_code" => $code);
				$municipalities_query  = array(
					'select'           => "*",
					'table'            => 'municipalities',
					'condition'        => $condition,
					'type'             => "",
					'order' => array(
						'col' => 'mun_name',
						'order_by' => "ASC",
					),
				);
				$result = 	$this->Main->select($municipalities_query);
			}
		}


		$data = array(
			"success" => true,
			"message" => "Success",
			"data" => $result
		);
		response_json($data);
	}

	public function getBarangays($code = "")
	{

		$code = $this->input->post('mun_code');
		$code = !empty($this->input->post('mun_code')) ? $this->input->post('mun_code') : $code;
		$result = 	!empty($code) ? $this->api->getbarangay($code) : array();
		$data = array(
			"success" => true,
			"message" => "Success",
			"data" => $result
		);
		response_json($data);
	}

	//GET LIBRARY
	public function getallLocation(){
		//get all provinces
		$prov_name_list = $this->api->getProvinces([], 0, ['col' => 'prov_name', 'order_by' => 'ASC']);
		//$prov_name_list = array_column($provinces, 'prov_name','prov_code');

		//get all municipalities
		$municipalities = $this->api->getmunicipalities([], 0, ['col' => 'mun_name', 'order_by' => 'ASC']);
		$mun_name_list = [];
		foreach ($municipalities as $key => $value) {
			$mun_name_list[$value["prov_code"]][] = array(
				"mun_code" => $value["mun_code"],
				"mun_name" => $value["mun_name"]
			);
		}

		//get all barangays
		$barangays = $this->api->getBarangays([], 0, ['col' => 'bar_name', 'order_by' => 'ASC']);
		$bar_name_list = [];
		foreach ($barangays as $key => $value) {
			$bar_name_list[$value["mun_code"]][] = array(
				"bar_code" => $value["bar_code"],
				"bar_name" => $value["bar_name"]
			);
		}

		$ret = array(
			"provinces" => $prov_name_list,
			"municipalities" => $mun_name_list,
			"barangays" => $bar_name_list
		);

		response_json($ret);
	}

	public function getallLibrary(){
		$marStatus = $this->api->getLibraries("tblmaritalstatus");
		$livingArr = $this->api->getLibraries("tbllivingarrangement");
		$relList = $this->api->getLibraries("tblrelationships");
		$disabilities = $this->api->getLibraries("tbldisability");
		$inactivereason = $this->api->getLibraries("tblinactivereason");
		$ret = array(
			"marStatus" => $marStatus,
			"livingArr" => $livingArr,
			"relList" => $relList,
			"disabilities" => $disabilities,
			"inactivereason" => $inactivereason
		);
		response_json($ret);
	}

	public function getallReplacementReason(){
		$inactivereason = $this->api->getLibraries("tblinactivereason");
		if(empty($inactivereason)) {
			$inactivereason =[];
		}
		response_json($inactivereason);
	}
	
	public function getallMaritalStatus(){
		$marStatus = $this->api->getLibraries("tblmaritalstatus");
		if(empty($marStatus)) {
			$marStatus =[];
		}
		response_json($marStatus);
	}
	
	public function getallLivingArrangement(){
		$livingArr = $this->api->getLibraries("tbllivingarrangement");
		if(empty($livingArr)) {
			$livingArr =[];
		}
		response_json($livingArr);
	}
	
	public function getallRelationships(){
		$relList = $this->api->getLibraries("tblrelationships");
		if(empty($relList)) {
			$relList =[];
		}
		response_json($relList);
	}

	public function getallDisabilities(){
		$disabilities = $this->api->getLibraries("tbldisability");
		if(empty($disabilities)) {
			$disabilities =[];
		}
		response_json($disabilities);
	}

	public function getCurrentPeriod(){
		$year = date("Y");
        $month = date("n");
        $semester = floor((intval($month) + 5) / 6);
        $period = ceil($month / 3);

		response_json([
			'year' => $year,
			'semester' => $semester,
			'period' => $period,
		]);

	}

//END GET LIBRARY
}

/* End of file Dashboard.php */
/* Location: ./application/modules/admin/controllers/Dashboard.php */