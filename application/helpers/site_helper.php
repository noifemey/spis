<?php

if (!function_exists('response_json')) {
	function response_json($data = array())
	{
		$_CI = &get_instance();
		$_CI->output->set_content_type('application/json')->set_output(json_encode($data));
	}
}

if (!function_exists('user_info')) {
	function user_info($select = "*", $condition = "", $type = false)
	{
		if (!empty($condition)) {
			$condition = "WHERE $condition";
		}
		$_CI = &get_instance();
		$sql = "SELECT $select 
		FROM `user` AS u 
		INNER JOIN user_info AS uf 
		ON uf.`uid`= u.`id` $condition";

		return $_CI->Main->raw($sql, $type);
	}
}
// function checklogin(){
// 	if (empty( $_SESSION['fullname'])) {
// 		redirect('login','refresh');
// 	}
// }

function sesdata($index)
{
	$_CI = &get_instance();
	return $_CI->session->userdata($index);
}

/**
 * @param -> $date 
 * Date String
 */
function fixDate($date)
{
	$date = "03/25/1930";
	preg_match_all('!\d+!', $date, $matches);
	$date_array = $matches[0];
	pdie($date_array);

	return $date_array;
}

function checkLogin($type = false)
{
	$_CI = &get_instance();
	if ($type) {
		if (!empty($_CI->session->userdata('loggedin'))) {
			$status = getUser('active_status', array('id' => sesdata('id')), 'row')->active_status;
			if ($status == 0) {
				redirect(base_url('activate'), 'refresh');
			} else {
				redirect(base_url('member'), 'refresh');
			}
		}
	} else {
		if (empty($_CI->session->userdata('loggedin'))) {
			redirect(base_url('login'), 'refresh');
		}
	}
}

function getUserRole()
{
	$_CI = &get_instance();
	if (!empty($_CI->session->userdata('loggedin'))) {
		return $_CI->session->userdata('role');
	} else {
		return 0;
	}
}

function getUser($select = "*", $condition = array(), $type = false, $offset = array())
{
	$_CI = &get_instance();
	$offset = checkOffset($offset);
	$qry  = array(
		'select'           	=> $select,
		'table'            	=> 'tblusers',
		'condition'        	=> $condition,
		'type'             	=> $type,
		'limit' 			=>   $offset['limit'],
		'offset' 			=>  $offset['offset'],
	);
	return	$_CI->Main->select($qry);
}

function userLogs($id, $name, $type, $details)
{
	$_CI = &get_instance();
	$ulog = array(
		'luid'		=> $id,
		'luname'	=> $name,
		'laction'	=> $type,
		'ldesc'		=> $details
	);
	return $_CI->Main->insert("tblactivitylog", $ulog, $return = false);
}

function beneLogs($userid, $beneid, $action, $field_edited, $prev_edit, $now_edit)
{
	$_CI = &get_instance();
	$belog = array(
		'b_id'			=> $beneid,
		'user_id'		=> $userid,
		'action'		=> $action,
		'field_edited'	=> $field_edited,
		'prev_edit'		=> $prev_edit,
		'now_edit'		=> $now_edit
	);
	return $_CI->Main->insert("tblbeneficiary_editlogs", $belog, $return = false);
}

function pdie($data = array(), $type = false)
{
	echo "<pre>";
	var_dump($data);
	echo "</pre>";
	if ($type) {
		die();
	}
}

function pageconfig($data = array())
{
	$config['base_url'] = $data['base_url'];
	$config['page_query_string'] = true;
	$config['reuse_query_string'] = true;
	$config['query_string_segment'] = 'p';
	$config['total_rows'] = $data['total_rows'];
	$config['per_page'] = $data['per_page'];
	$config['full_tag_open'] = ' <ul class="pagination justify-content-end">';
	$config['full_tag_close'] = '</ul>';
	$config['first_link'] = 'First Page';
	$config['first_tag_open'] = '<li class="page-item ">';
	$config['first_tag_close'] = '</li>';
	$config['last_link'] = 'Last Page';
	$config['last_tag_open'] = '<li class="page-item ">';
	$config['last_tag_close'] = '</li>';
	$config['next_link'] = 'Next Page';
	$config['next_tag_open'] = '<li class="page-item ">';
	$config['next_tag_close'] = '</li>';
	$config['prev_link'] = 'Prev Page';
	$config['prev_tag_open'] = '<li class="page-item ">';
	$config['prev_tag_close'] = '</li>';
	$config['cur_tag_open'] = '<li class="page-item active"><span class="page-link"><span class="sr-only">(current)</span>';
	$config['cur_tag_close'] = '</span></span></li>';
	$config['num_tag_open'] = '<li class="page-item ">';
	$config['num_tag_close'] = '</li>';
	$config['num_links'] = 2;

	return $config;
}

function getLocation($condition = "", $type = "", $other = "")
{
	$_CI = &get_instance();
	$query = "SELECT 
	b.bar_name AS bar_name,
	b.bar_code AS bar_code,
	m.mun_name AS mun_name,
	m.mun_code AS mun_code,
	p.prov_name AS prov_name,
	p.prov_code AS prov_code
	FROM tblbarangays AS b 
	INNER JOIN tblmunicipalities AS m 
	ON b.`mun_code`=m.`mun_code` 
	INNER JOIN tblprovinces AS p
	ON p.`prov_code` = m.`prov_code` WHERE $condition
	ORDER BY p.prov_name, m.mun_name, b.bar_name";
	return	$_CI->Main->raw($query, $type);
}

function getDatasFromOneTable($select, $table, $condition = "", $type = "")
{
	$_CI = &get_instance();
	$query = "SELECT $select
	FROM $table
	WHERE $condition";
	return	$_CI->Main->raw($query, $type);
}

function getProvinces($select = "*", $condition = array(), $type = false, $offset = array())
{
	$_CI = &get_instance();
	$offset = checkOffset($offset);
	$provinces_query  = array(
		'select'           => $select,
		'table'            => 'tblprovinces',
		'condition'        => $condition,
		'order'        	   => array("col" => "prov_name", "order_by" => "ASC"),
		'type'             => $type,
		'limit' =>   $offset['limit'],
		'offset' =>  $offset['offset'],
	);
	return	$_CI->Main->select($provinces_query);
}

function countmemberactiveperbarangay()
{
	$_CI = &get_instance();
	$query  = "
	SELECT 
	p.`prov_name`,
	m.`mun_name`,
	b.`bar_name`,
	COUNT(*) AS total 
  FROM
	tblbarangays b 
	INNER JOIN tblgeneral g 
	  ON g.`barangay` = b.`bar_code` 
	  INNER JOIN tblmunicipalities m ON
	  m.mun_code=b.`mun_code`
	   INNER JOIN tblprovinces p ON
	  p.prov_code=b.`prov_code`
  WHERE g.`sp_status` = 'active' 
  GROUP BY bar_code 
	";
	return	$_CI->Main->raw($query);
}

function checkOffset($data = array())
{
	if (!empty($data)) {
		$result = array('limit' => $data['limit'], 'offset' => $data['offset']);
	} else {
		$result = array('limit' => "", 'offset' => "");
	}
	return $result;
}

function getMunicipalities($select = "*", $condition = array(), $type = false, $offset = array())
{
	$_CI = &get_instance();
	$offset = checkOffset($offset);
	$municipalities_query  = array(
		'select'           => $select,
		'table'            => 'tblmunicipalities',
		'condition'        => $condition,
		'order'        	   => array("col" => "mun_name", "order_by" => "ASC"),
		'type'             => $type,
		'limit' =>   $offset['limit'],
		'offset' =>  $offset['offset'],
	);
	return	$_CI->Main->select($municipalities_query);
}



function getBarangays($select = "*", $condition = array(), $type = false, $offset = array())
{
	$_CI = &get_instance();
	$offset = checkOffset($offset);
	$municipalities_query  = array(
		'select'           => $select,
		'table'            => 'tblbarangays',
		'condition'        => $condition,
		'order'        	   => array("col" => "bar_name", "order_by" => "ASC"),
		'type'             => $type,
		'limit' =>  150,
		'offset' =>  0,
	);
	return	$_CI->Main->select($municipalities_query);
}

function getTarget($select, $condition = array(), $pager = array(), $type = false)
{
	$_CI = &get_instance();
	$_CI->Main->db->select($select);
	$_CI->Main->db->from("tbltarget");
	$_CI->Main->db->join('tblmunicipalities', 'tblmunicipalities.`mun_code`=tbltarget.`mun_code`', 'inner');
	if (!empty($condition)) {
		$_CI->Main->db->where($condition);
	}
	$_CI->Main->db->order_by("prov_code", "asc");
	$_CI->Main->db->order_by("year", "desc");
	if (!empty($pager)) {
		$_CI->Main->db->limit($pager['limit'], $pager['offset']);
	}

	$query = $_CI->Main->db->get();
	if ($query->num_rows()) {

		if ($type == "row") {
			return $query->row();
		} elseif ($type == "count_row") {
			return $query->num_rows();
		} elseif ($type == "is_array") {
			return $query->result_array();
		} else {
			return $query->result();
		}
	}
	return null;
}

function getSemTarget($select, $condition = array(), $pager = array(), $type = false)
{
	$_CI = &get_instance();
	$_CI->Main->db->select($select);
	$_CI->Main->db->from("tblsemtarget");
	$_CI->Main->db->join('tblmunicipalities', 'tblmunicipalities.`mun_code`=tblsemtarget.`mun_code`', 'inner');
	if (!empty($condition)) {
		$_CI->Main->db->where($condition);
	}
	$_CI->Main->db->order_by("prov_code", "asc");
	$_CI->Main->db->order_by("mun_name", "asc");
	$_CI->Main->db->order_by("year", "desc");
	if (!empty($pager)) {
		$_CI->Main->db->limit($pager['limit'], $pager['offset']);
	}

	$query = $_CI->Main->db->get();
	if ($query->num_rows()) {

		if ($type == "row") {
			return $query->row();
		} elseif ($type == "count_row") {
			return $query->num_rows();
		} elseif ($type == "is_array") {
			return $query->result_array();
		} else {
			return $query->result();
		}
	}
	return null;
}

function getDetails($select, $table, $member_condition)
{
	$_CI = &get_instance();
	$_CI->Main->db->select($select);
	$_CI->Main->db->from($table);
	$_CI->Main->db->where($member_condition);
	$query = $_CI->Main->db->get()->row();
	return $query;
}

function getUserDetails($select, $member_condition)
{
	$_CI = &get_instance();
	$_CI->Main->db->select($select);
	$_CI->Main->db->from("tblusers");
	$_CI->Main->db->where($member_condition);
	$query = $_CI->Main->db->get();
	return $query->row();
}

function getMemberDetails($select, $member_condition)
{
	$_CI = &get_instance();
	$_CI->Main->db->select($select);
	$_CI->Main->db->from("tblgeneral");
	$_CI->Main->db->where($member_condition);
	$query = $_CI->Main->db->get();
	return $query->row();
}

function getMemberCount($member_condition)
{
	$_CI = &get_instance();
	$_CI->Main->db->select("count(*) as total");
	$_CI->Main->db->from("tblgeneral");
	$_CI->Main->db->where($member_condition);
	$query = $_CI->Main->db->get();
	return $query->row();
}

function member_payroll_list_count($condition)
{
	$_CI = &get_instance();
	$_CI->Main->db->select("count(spid) as total");
	$_CI->Main->db->from("tblpayroll");
	$_CI->Main->db->where($condition);
	$query = $_CI->Main->db->get();
	return $query->row();
}

function getSignatories($select, $condition = array(), $pager = array(), $type = false)
{
	$_CI = &get_instance();
	$_CI->Main->db->select($select);
	$_CI->Main->db->from("tblsignatories");
	if (!empty($condition)) {
		$_CI->Main->db->where($condition);
	}
	if (!empty($pager)) {
		$_CI->Main->db->limit($pager['limit'], $pager['offset']);
	}

	$query = $_CI->Main->db->get();
	if ($query->num_rows()) {

		if ($type == "row") {
			return $query->row();
		} elseif ($type == "count_row") {
			return $query->num_rows();
		} elseif ($type == "is_array") {
			return $query->result_array();
		} else {
			return $query->result();
		}
	}
	return null;
}

function memberlist($select = "*", $condition = array(), $pager = array(), $type = false)
{
	$_CI = &get_instance();
	$_CI->Main->db->select("*");
	$_CI->Main->db->from("tblgeneral");
	$_CI->Main->db->where($condition);
	$_CI->Main->db->order_by("lastname", "asc");
	if (!empty($pager)) {
		$_CI->Main->db->limit($pager['limit'], $pager['offset']);
	}

	$query = $_CI->Main->db->get();
	if ($query->num_rows()) {

		if ($type == "row") {
			return $query->row();
		} elseif ($type == "count_row") {
			return $query->num_rows();
		} elseif ($type == "is_array") {
			return $query->result_array();
		} else {
			return $query->result();
		}
	}
	return null;
}

function checkConnum($spid)
{

	$_CI = &get_instance();
	$sql = "SELECT COUNT(*) as count, b_id
	FROM tblgeneral
	WHERE connum = '$spid'";

	$check = $_CI->Main->raw($sql, true);
	$data = array();

	if ($check->count != 0) {

		$data['b_id'] = $check->b_id;
		$data['inDatabase'] = 1;
	} else {

		$data['b_id'] = 0;
		$data['inDatabase'] = 0;
	}

	return $data;
}

function getNameByID($id, $table)
{
	$_CI = &get_instance();
	$sql = "SELECT *
	FROM $table
	WHERE id = '$id'";

	return	$_CI->Main->raw($sql, true)->name;
}

function getRelNameByRelID($id, $table)
{
	$_CI = &get_instance();
	$sql = "SELECT *
	FROM $table
	WHERE relid = '$id'";

	return	$_CI->Main->raw($sql, true)->relname;
}

function upperCaseWords($string)
{
	return ucwords(strtolower($string));
}

function sentenceCase($string)
{

	return ucfirst((strtolower($string)));
}

function validateDate($date, $format)
{
	$d = DateTime::createFromFormat($format, $date);
	// The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
	return $d && $d->format($format) === $date;
}

function checkAccess($access_type = "", $url = "")
{
	$_CI = &get_instance();
	$url = !empty($url) ? $url : $_CI->uri->segment(1) . "/" . $_CI->uri->segment(2);
	$role = $_CI->session->userdata('role');
	$condition = array('role_id' => $role, 'url' => $url, 'access_type' => $access_type);
	$useraccess_query  = array(
		'select'           => "status",
		'table'            => 'user_access',
		'condition'        => $condition,
		'type'             => 'row',
		'limit' =>   '',
		'offset' =>  '',
	);
	$result =  $_CI->Main->select($useraccess_query);
	if (!empty($result)) {
		if ($role == 1) {
			return 1;
		}
		return $result->status;
	}
	return 0;
}

function userRole($condition = array(), $type = "")
{
	$_CI = &get_instance();

	$role_query  = array(
		'select'           => "*",
		'table'            => 'user_role',
		'condition'        => $condition,
		'type'             => $type,
		'limit' =>   '',
		'offset' =>  '',
	);
	$result =  $_CI->Main->select($role_query);
	if (!empty($result)) {
		return $result;
	}

	return  (object)array("name" => "Inactive", 'id' => 0);
}

function arrayToString($array = array())
{
	$string = "";

	if (!empty($array)) {
		foreach ($array as $ak => $av) {
			$string .= "'" . $av . "',";
		}
	}
	// $string = implode(",",$array);
	$string = substr(trim($string), 0, -1);

	return $string;
}

function userAccessStatus($rid, $page, $url, $access_type, $type = "")
{
	$_CI = &get_instance();
	$condition = array(
		"role_id" => $rid,
		"page" => $page,
		"url" => $url,
		"access_type" => $access_type,
	);

	$useraccess_query  = array(
		'select'           => "status",
		'table'            => 'user_access',
		'condition'        => $condition,
		'type'             => 'row',
		'limit' =>   '',
		'offset' =>  '',
	);
	$result =  $_CI->Main->select($useraccess_query);


	if ($type == "checkbox") {
		if (!empty($result)) {
			$status =  $result->status;
			if ($status == 1) {
				return "checked";
			}
		}
	} else {
		if (!empty($result)) {
			return $result->status;
		}
		return 0;
	}

	return null;
}

function getForRepReason($id, $type)
{
	$_CI = &get_instance();
	$_CI->db->select("name");
	$_CI->db->from("tblinactivereason");
	$condition = array(
		"id" => $id
	);
	$_CI->db->where($condition);

	$query = $_CI->db->get();
	if ($query->num_rows()) {
		if ($type == "row") {
			return $query->row();
		} elseif ($type == "count_row") {
			return $query->num_rows();
		} elseif ($type == "is_array") {
			return $query->result_array();
		} else {
			return $query->result();
		}
	}
	return null;
}

function getEligibility($spid, $type)
{
	$_CI = &get_instance();
	$_CI->db->select("eligibility_stat, upload_batchno");
	$_CI->db->from("tbleligible");
	$condition = array(
		"spid" => $spid
	);
	$_CI->db->where($condition);

	$query = $_CI->db->get();
	if ($query->num_rows()) {
		if ($type == "row") {
			return $query->row();
		} elseif ($type == "count_row") {
			return $query->num_rows();
		} elseif ($type == "is_array") {
			return $query->result_array();
		} else {
			return $query->result();
		}
	}
	return null;
}

function getGuardians($bid, $type)
{
	$_CI = &get_instance();
	$_CI->db->select("id, gname, relname");
	$_CI->db->from("tblguardians tg");
	$_CI->db->join("tblrelationships tr", "tr.`relid` = tg.`rel_id`", 'left');
	$condition = array(
		"b_id" => $bid
	);
	$_CI->db->where($condition);

	$query = $_CI->db->get();
	if ($query->num_rows()) {
		if ($type == "row") {
			return $query->row();
		} elseif ($type == "count_row") {
			return $query->num_rows();
		} elseif ($type == "is_array") {
			return $query->result_array();
		} else {
			return $query->result();
		}
	}
	return null;
}

if (!function_exists('scrypthash')) {
	function scrypthash($string, $action = '')
	{
		$secret_key = 'socpen_secretkey_do_not_key';
		$secret_iv = 'socpen_secretkey_do_not_iv';
		$output = false;
		$encrypt_method = "AES-256-CBC";
		$key = hash('sha256', $secret_key);
		$iv = substr(hash('sha256', $secret_iv), 0, 16);
		if ($action == 'e') {
			$output = base64_encode(openssl_encrypt($string, $encrypt_method, $key, 0, $iv));
		} elseif ($action == 'de') {
			$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
		}
		return $output;
	}
}

function countOccurence($table = "", $condition = array())
{
	$_CI = &get_instance();
	$_CI->db->select("count(*) as total");
	$_CI->db->from($table);
	if (!empty($condition)) {
		$_CI->db->where($condition);
	}
	return $_CI->db->get()->row()->total;
}

function getLibrary($table = "")
{
	if ($table == "") {
		return null;
	} else {
		$data = $this->Main->raw("SELECT * FROM $table");
		return $data;
	}
}

function unpaid_member_list_count($condition)
{
	$_CI = &get_instance();
	$_CI->Main->db->select("count(tblpayroll.spid) as total");
	$_CI->Main->db->from("tblpayroll");
	$_CI->Main->db->where($condition);
	$query = $_CI->Main->db->get();
	return $query->row();
}

function searchLocation($location, $bar_code)
{
	foreach ($location as $key => $value) {
		if ($value->bar_code == $bar_code) {
			return $location[$key];
		}
	}
	return null;
}

function getAge($birthDate = "")
{
	$age = "N/A";
	if (!empty($birthDate)) {
		$date = new DateTime($birthDate);
		$now = new DateTime();
		$interval = $now->diff($date);
		return $interval->y;
	   
	   
	}
	return $age;
}


