<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class GenerateLbp extends CI_Controller {
	private $pager_settings;
	public function __construct() {
		parent::__construct();

		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('','');
        $this->load->model('Main', 'Main');
		$this->load->model("Member_model","mem");
		
        $this->load->library('csvimport');
		$this->load->library('PHPExcel');
		$this->load->library('pdf');
		
		checkLogin();
	}

	public function index()
	{
        $data['app_active'] = true;

		$this->template->title('Social Land Bank Card Enrollment Form');
		$this->template->set_layout('default');
	    $this->template->set_partial('header','partials/header');
	    $this->template->set_partial('sidebar','partials/sidebar');
	    $this->template->set_partial('aside','partials/aside');
	    $this->template->set_partial('footer','partials/footer');
	    $this->template->append_metadata('<script src="' . base_url("assets/js/pages/lbp/lbpform.js?ver=") . filemtime(FCPATH. "assets/js/pages/lbp/lbpform.js") . '"></script>');

	    $this->template->build('lbp/lbpform',$data);
    }
    
    
	public function blankLBP(){
		$this->load->view('lbp/newblanklbp',"");
		$html = $this->output->get_output();
		$this->load->library('pdf');
		$pdf = $this->dompdf->loadHtml($html);
		$this->dompdf->setPaper('A4', 'portrait');
		$this->dompdf->render();
		$filename = "landbankapplicationform.pdf";
		$file = $this->dompdf->stream($filename, array("Attachment"=>0));
	}

	
	public function generatelbpform(){
		$province           = $this->input->get("prov_code");
		$municipality	    = $this->input->get("mun_code");

		$this->db->order_by("barangay", "asc");
		$this->db->order_by("lastname", "asc");
		$this->db->order_by("firstname", "asc"); 
		$condition = array("sp_status"=>"Active", "city"=>$municipality);
		
		//$data['beneficiary'] = $this->mem->memberlist("*",$condition,array('limit'=>5, 'offset'=>0));
		$beneficiary = $this->Main->select([
            'select'    => '*',
            'type'      => '',
            'table'     => 'tblgeneral',
            'limit'     => 5,
            'condition' => $condition,
            'order'     => ['col' => 'lastname', 'order_by' => 'Asc']
		]);

		$data['beneficiary'] = $beneficiary;

		//print_r($beneficiary);

		$this->load->view('lbp/cashcardform',$data);
		$html = $this->output->get_output();
		$this->load->library('pdf');
		$pdf = $this->dompdf->loadHtml($html);
		$this->dompdf->setPaper('A4', 'portrait');
		$this->dompdf->render();
		$filename = "landbankapplicationform.pdf";
		$file = $this->dompdf->stream($filename, array("Attachment"=>0));
	}
    
}
