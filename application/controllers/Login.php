<?php

class Login extends CI_Controller {

	public function __construct()
    {
        parent::__construct();

        $this->load->helper('form');
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('','');
        $this->load->model('Main');
        $this->load->library('bcrypt');
        
		//checkLogin(true);
    }

	public function index()
	{
		
        $this->template->title('SPIS Login');
		$this->template->set_layout('default');
	    $this->template->set_partial('header','partials/header');
	    $this->template->set_partial('sidebar','partials/sidebar');
	    $this->template->set_partial('aside','partials/aside');
	    $this->template->set_partial('footer','partials/footer');
	    $this->template->append_metadata('<script src="' . base_url("assets/js/pages/login.js?ver=") . filemtime(FCPATH. "assets/js/pages/login.js") . '"></script>');

	    $this->template->build('login_view');
	}

	public function chklogin()
    {		
        $username = $this->input->post('username');
		$password = $this->input->post('password');
        $login = "false";
        $entries = [];
		$r=array();

		$qry  = array(
			'select'           => "*",
			'table'            => 'tblusers',
			'condition'        => array('username' => $username),
			'type'             => 'row',
		);  
		$r = $this->Main->select($qry);
        $activated = "false";
        $message = '';

		if(!empty($r)){
			if($this->bcrypt->check_password($password, $r->password)){
                
                // $activation = getUser('active_status',array('id'=>sesdata('id')),'row');
                // if(!empty($activation)){ $activation = $activation->active_status;
                // }else{ $activation = 0; }

                if ($r->active_status == 0) { 
					$activated = "false";
				}else{ 
					$activated = "true";
					$sessiondata = array(
						'loggedin' => true,
						'username' => $r->username,
						'first_name' => $r->fname,
						'last_name' => $r->lname,
						'middle_name' => $r->mname,
						'fullname' => $r->lname . ", " .$r->fname . " " . $r->mname . ".",
						'role' => $r->role,
						'id' => $r->id,
						'province' => $r->province,
						'activated'=>$r->active_status,
					);
					$data = array('logged_in'=>'1');
					$condition = array('id'=>$r->id);
					$query = $this->Main->update('tblusers', $condition, $data);
					$this->session->set_userdata($sessiondata);
	
					$checkldap = true;
					userLogs($r->id , $r->lname . ", " .$r->fname . " " . $r->mname . ". " , "LOGIN", "User Login");
				 }
				 $login = "true";
				 $message = "Success";
            
			} else{
				$login = "false";
				$message = "Incorrect Username or Password.";
			}
		} else {
            $domain = 'entdswd.local';
            $ldapconfig['host'] = '172.26.144.10';
            $ldapconfig['port'] = 389;
            $ldapconfig['basedn'] = 'dc=entdswd,dc=local';
            $ds = ldap_connect($ldapconfig['host'], $ldapconfig['port']);
            ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($ds, LDAP_OPT_REFERRALS, 0);
            $dn = $ldapconfig['basedn'];
            $bind = @ldap_bind($ds, $username . '@' . $domain, $password);
            //$bind = @ldap_bind($ds, 'amrosero' . '@' . $domain, 'allanrosero123123');
            $message = "Username does not yet exist.";
            if (!empty($bind) || $bind != "") {
                $login = "true";
                $column = array("sn", "initials", "cn", "samaccountname", "givenname");
                $result = @ldap_search($ds, $dn, '(&(objectClass=person)(sAMAccountName=' . $username . '))', $column);
                $entries = ldap_get_entries($ds, $result);
                $first_name = $entries[0]['givenname'][0];
                $last_name = $entries[0]['sn'][0];
                $middle_name = $entries[0]['initials'][0];
                $fullname = $entries[0]['cn'][0];
                $username = $entries[0]['samaccountname'][0];
                $password = $this->bcrypt->hash_password($password);
                $indata = [
                    'username' => $username,
                    'password' => $password,
                    'fullname' => strtoupper($fullname),
				];				
                $lastid =  $this->Main->insert('tblusers', $indata, true,true)['lastid'];

                $this->Main->insert('tblusers', ['user_id'=>$lastid]);

                $sessiondata = array(
					'loggedin' => true,
                    'id' => $lastid,
					'username' => $username,
					'first_name' => $first_name,
					'last_name' => $last_name,
					'middle_name' => $middle_name,
					'fullname' =>  $fullname,
					'role' => "4",
                    'activated'=>"0",
                    'setup' => 0,
                );
                
                $this->session->set_userdata($sessiondata);
            }
        }
        $response = ['success' => $login, 'active' => $activated, 'message' => $message];
        response_json($response);
    }

    public function activate()
	{
	    $this->load->view('activate');
    }
    
	public function logout()
	{
		
		$data = array('logged_in'=>'0');
		$condition = array('id'=>sesdata('id'));
		$query = $this->Main->update('tblusers', $condition, $data);

	    session_destroy();
        $this->session->sess_destroy();
		redirect(base_url().'login');
	}

	public function profile()
	{
	    $this->template->title('SPIS User Profile');
		$this->template->set_layout('default');
	    $this->template->set_partial('header','partials/header');
	    $this->template->set_partial('sidebar','partials/sidebar');
	    $this->template->set_partial('aside','partials/aside');
	    $this->template->set_partial('footer','partials/footer');
	    $this->template->append_metadata('<script src="' . base_url("assets/js/pages/profile.js?ver=") . filemtime(FCPATH. "assets/js/pages/profile.js") . '"></script>');

	    $this->template->build('profile_view');
    }
}
