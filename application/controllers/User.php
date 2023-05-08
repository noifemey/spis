<?php

class User extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('', '');
		$this->load->model('Main');
		$this->load->library('bcrypt');
	}

	public function index()
	{
		checkLogin();
		$this->template->title('SPIS Users List');
		$this->template->set_layout('default');
		$this->template->set_partial('header', 'partials/header');
		$this->template->set_partial('sidebar', 'partials/sidebar');
		$this->template->set_partial('aside', 'partials/aside');
		$this->template->set_partial('footer', 'partials/footer');
		$this->template->append_metadata('<script src="' . base_url("assets/js/pages/user/users_list.js?ver=") . filemtime(FCPATH . "assets/js/pages/user/users_list.js") . '"></script>');

		$this->template->build('user/users_list');
	}

	public function getUserList()
	{

		$response['success'] = true;
		$user = $this->Main->select([
			'select'    => '*',
			'type'      => '',
			'table'     => 'tblusers',
			'condition' => ['archived' => 0],
			'order'     => ['col' => 'date_registered', 'order_by' => 'DESC']
		]);

		$provinces = $this->Main->select([
			'select'    => '*',
			'type'      => '',
			'table'     => 'tblprovinces',
			'condition' => [],
		]);

		$response['users'] = $user;
		$response['provinces'] = $provinces;

		response_json($response);
	}

	public function updateUser()
	{
		extract($this->input->post());

		$response['success'] = FALSE;

		$update_data = [
			'username' => $username,
			'fname'    => $fname,
			'lname'    => $lname,
			'mname'    => $mname,
			'emailadd' => $emailadd,
			'position' => $position,
			'province' => $province,
			'role'     => $role,
		];

		$response['success'] =  $this->Main->update('tblusers', ['id' => $id], $update_data);
		$response['message'] =  "Successfully updated this user.";


		response_json($response);
	}

	public function deleteUser()
	{
		extract($this->input->post());

		$response['success'] = FALSE;
		$response['success'] =  $this->Main->update('tblusers', ['id' => $id], ['archived' => 1]);
		$response['message'] =  "Successfully archived this user.";

		response_json($response);
	}
	public function activateUser()
	{
		extract($this->input->post());
		$response['success'] = FALSE;

		$update_status = ($active_status) ? 1 : 0;
		$response['success'] =  $this->Main->update('tblusers', ['id' => $id], ['active_status' => $update_status]);
		$response['message'] =  "Successfully updated the status of this user.";

		response_json($response);
	}
	public function profile()
	{
		checkLogin();

		$this->template->title('SPIS User Profile');
		$this->template->set_layout('default');
		$this->template->set_partial('header', 'partials/header');
		$this->template->set_partial('sidebar', 'partials/sidebar');
		$this->template->set_partial('aside', 'partials/aside');
		$this->template->set_partial('footer', 'partials/footer');
		$this->template->append_metadata('<script src="' . base_url("assets/js/pages/user/user.js?ver=") . filemtime(FCPATH . "assets/js/pages/user/user.js") . '"></script>');

		$this->template->build('user/profile_view');
	}

	public function getUserProfile()
	{
		$user_id = $this->session->userdata('id');

		$response['success'] = true;
		$user = $this->Main->select([
			'select'    => '*',
			'type'      => 'row',
			'table'     => 'tblusers',
			'condition' => ['id' => $user_id],
		]);

		$provinces = $this->Main->select([
			'select'    => '*',
			'type'      => '',
			'table'     => 'tblprovinces',
			'condition' => [],
		]);

		$response['user'] = $user;
		$response['provinces'] = $provinces;

		response_json($response);
	}

	public function saveUserProfile()
	{
		extract($this->input->post());

		$response['success'] = FALSE;
		$user_id = $this->session->userdata('id');

		$update_data = [
			'username' => $username,
			'fname'    => $fname,
			'lname'    => $lname,
			'mname'    => $mname,
			'emailadd' => $emailadd,
			'position' => $position,
			'province' => $province,
		];

		if ($password != "") {
			$update_data['password'] = $this->bcrypt->hash_password($password);
		}

		$response['success'] =  $this->Main->update('tblusers', ['id' => $user_id], $update_data);
		$response['message'] =  "Successfully updated your profile.";


		response_json($response);
	}

	public function register()
	{
		$this->template->title('SPIS Registration');
		$this->template->set_layout('default');
		$this->template->set_partial('header', 'partials/header');
		$this->template->set_partial('sidebar', 'partials/sidebar');
		$this->template->set_partial('aside', 'partials/aside');
		$this->template->set_partial('footer', 'partials/footer');
		$this->template->append_metadata('<script src="' . base_url("assets/js/pages/user/user.js?ver=") . filemtime(FCPATH . "assets/js/pages/user/user.js") . '"></script>');

		$this->template->build('user/register');
	}

	public function saveRegister()
	{
		extract($this->input->post());

		$response['success'] = FALSE;
		$user_id = $this->session->userdata('id');
		$hash_password = $this->bcrypt->hash_password($password);
		$ins_data = [
			'username' => $username,
			'password' => $hash_password,
			'fname'    => $fname,
			'lname'    => $lname,
			'mname'    => $mname,
			'emailadd' => $emailadd,
			'position' => $position,
			'province' => $province,
		];

		$response['success'] =  $this->Main->insert('tblusers', $ins_data);
		$response['message'] =  "Successfully updated your profile.";

		response_json($response);
	}


	public function myLogs()
	{
		checkLogin();
		$this->template->title('My Logs');
		$this->template->set_layout('default');
		$this->template->set_partial('header', 'partials/header');
		$this->template->set_partial('sidebar', 'partials/sidebar');
		$this->template->set_partial('aside', 'partials/aside');
		$this->template->set_partial('footer', 'partials/footer');
		$this->template->append_metadata('<script src="' . base_url("assets/js/pages/user/mylogs.js?ver=") . filemtime(FCPATH . "assets/js/pages/user/mylogs.js") . '"></script>');

		$this->template->build('user/mylogs');
	}

	public function getmyLogs()
	{
		$response['success'] = true;
		$from = $this->input->post('from');
		$to = $this->input->post('to');

		if (!empty($from)) {
			$condition['ltime >='] = $from;
			$condition['ltime <='] = $to;
		} else {
			$condition['ltime >='] = date('Y-m-d h:s:i');
		}

		$condition['luid'] = !empty($this->input->post('uid')) ? $this->input->post('uid') : sesdata('id');
		// if(!empty($this->input->post('uid')) && empty($from)){
		// 	$condition['ltime >='] = '';
		// }
		$user = $this->Main->select([
			'select'    => '*',
			'type'      => '',
			'table'     => 'tblactivitylog',
			'condition' => $condition,
			'order'     => ['col' => 'ltime', 'order_by' => 'DESC'],
			'limit' => 10
		]);

		$listOfUsers = $this->Main->select([
			'select'    => 'username,id',
			'type'      => '',
			'table'     => 'tblusers',
			'condition' => [],
		]);

		$data = [];

		foreach ($user as $key => $value) {
			$id = $value->id;
			$luid = $value->luid;
			$lunam = $value->luname;
			$laction = $value->laction;
			$ldesc = $value->ldesc;

			$day = date("F d, Y", strtotime($value->ltime));
			$datetime = date("h:i A", strtotime($value->ltime));
			$diff = date_diff(date_create(date('Y-m-d H:i:s')), date_create(date($value->ltime)));
			$years = $diff->format("%y");
			$months = $diff->format("%m");
			$days = $diff->format("%d");
			$hours = $diff->format("%h");
			$minutes = $diff->format("%i");
			$seconds = $diff->format("%s");

			$datedesc = "";

			if ($years > 0) {
				if ($years > 1) {
					$datedesc =  $years . " years ago";
				} else {
					$datedesc =  $years . " year ago";
				}
			} else if ($months > 0) {
				if ($months > 1) {
					$datedesc =  $months . " months ago";
				} else {
					$datedesc =  $months . " month ago";
				}
			} else if ($days > 0) {
				if ($days > 1) {
					$datedesc =  $days . " days ago";
				} else {
					$datedesc =  $days . " day ago";
				}
			} else if ($hours > 0) {
				if ($hours > 1) {
					$datedesc =  $hours . " hours ago";
				} else {
					$datedesc =  $hours . " hour ago";
				}
			} else if ($minutes > 0) {
				if ($minutes > 1) {
					$datedesc =  $minutes . " minutes ago";
				} else {
					$datedesc =  $minutes . " minute ago";
				}
			} else if ($seconds > 0) {
				$datedesc =  $seconds . " seconds ago";
			} else {
				$datedesc =  $seconds . " second ago";
			}

			$data[] = array(
				"id"		=> $id,
				"luid"		=> $luid,
				"lunam"		=> $lunam,
				"laction"	=> $laction,
				"ldesc"		=> $ldesc,
				"day"		=> $day,
				"datetime"	=> $datetime,
				"datedesc"	=> $datedesc,
			);
		}

		$response['data'] = $data;
		$response['listOfUsers'] = $listOfUsers;

		response_json($response);
	}

	///// START USERLOGS ////////////////////

	public function userLogs()
	{
		checkLogin();
		$this->template->title('User Logs');
		$this->template->set_layout('default');
		$this->template->set_partial('header', 'partials/header');
		$this->template->set_partial('sidebar', 'partials/sidebar');
		$this->template->set_partial('aside', 'partials/aside');
		$this->template->set_partial('footer', 'partials/footer');
		$this->template->append_metadata('<script src="' . base_url("assets/js/pages/user/userlogs.js?ver=") . filemtime(FCPATH . "assets/js/pages/user/userlogs.js") . '"></script>');

		$this->template->build('user/userlogs');
	}

	public function getuserLogs()
	{
		$response['success'] = true;
		$fromUser = $this->input->post('fromUser');
		$toUser = $this->input->post('toUser');
		$user = $this->input->post('user');
		$condition =[];
		if (!empty($fromUser) && !empty($toUser)) {
			$condition['DATE(ltime) >='] = $fromUser;
			$condition['DATE(ltime) <='] = $toUser;
		}else if(empty($fromUser) && !empty($toUser)){
			$condition['DATE(ltime) <='] = $toUser;
		}else if(empty($toUser) && !empty($fromUser)){
			$condition['DATE(ltime) >='] = $fromUser;
		}

		if (!empty($user)) {
			$condition['luid ='] = intval($user);
		}

		$user = $this->Main->select([
			'select'    => '*',
			'type'      => '',
			'table'     => 'tblactivitylog',
			'condition' => $condition,
			'order'     => ['col' => 'ltime', 'order_by' => 'DESC']
		]);

		$data = [];

		if (!empty($user)) {
			foreach ($user as $key => $value) {
				$id = $value->id;
				$luid = $value->luid;
				$lunam = $value->luname;
				$laction = $value->laction;
				$ldesc = $value->ldesc;

				$day = date("F d, Y", strtotime($value->ltime));
				$datetime = date("h:i A", strtotime($value->ltime));
				$diff = date_diff(date_create(date('Y-m-d H:i:s')), date_create(date($value->ltime)));
				$years = $diff->format("%y");
				$months = $diff->format("%m");
				$days = $diff->format("%d");
				$hours = $diff->format("%h");
				$minutes = $diff->format("%i");
				$seconds = $diff->format("%s");

				$datedesc = "";

				if ($years > 0) {
					if ($years > 1) {
						$datedesc =  $years . " years ago";
					} else {
						$datedesc =  $years . " year ago";
					}
				} else if ($months > 0) {
					if ($months > 1) {
						$datedesc =  $months . " months ago";
					} else {
						$datedesc =  $months . " month ago";
					}
				} else if ($days > 0) {
					if ($days > 1) {
						$datedesc =  $days . " days ago";
					} else {
						$datedesc =  $days . " day ago";
					}
				} else if ($hours > 0) {
					if ($hours > 1) {
						$datedesc =  $hours . " hours ago";
					} else {
						$datedesc =  $hours . " hour ago";
					}
				} else if ($minutes > 0) {
					if ($minutes > 1) {
						$datedesc =  $minutes . " minutes ago";
					} else {
						$datedesc =  $minutes . " minute ago";
					}
				} else if ($seconds > 0) {
					$datedesc =  $seconds . " seconds ago";
				} else {
					$datedesc =  $seconds . " second ago";
				}

				$data[] = array(
					"id"		=> $id,
					"luid"		=> $luid,
					"lunam"		=> $lunam,
					"laction"	=> $laction,
					"ldesc"		=> $ldesc,
					"day"		=> $day,
					"datetime"	=> $datetime,
					"datedesc"	=> $datedesc,
				);
			}
		}

		$response['data'] = $data;

		response_json($response);
	}

	public function getUserListLogs(){
		$condition['active_status ='] = 1;
		$user = $this->Main->select([
			'select'    => 'id,CONCAT(fname," ",mname," ",lname) as fullname',
			'type'      => '',
			'table'     => 'tblusers',
			'condition' => $condition,
		]);

		$data['response'] = $user;
		response_json($data);
	}

	public function resetUserPassword(){
		$id = $this->input->post('id');
		$password = $this->bcrypt->hash_password('dswd1234');
		$updatePW = [
			'password' => $password,
		];
		$response['success'] =  $this->Main->update('tblusers', ['id' => $id], $updatePW);

		response_json($response);
	}

	///// END USRLOGS ///////////////////////
}
