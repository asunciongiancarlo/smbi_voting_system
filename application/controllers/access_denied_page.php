<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Access_denied_page extends CI_Controller {
 
	  public function __construct()
       {
            parent::__construct();
			session_start();
			$this->load->model('c3model');
			$this->load->library('modules');
			$this->output->enable_profiler(FALSE);
			
			$this->modules->session_handler();
       }
   
   public function index()
   {			
	   
	   $data['vfile']		= 'access_denied_page.php';
	   $data['title']		= 'Access Denied | San Miguel Brewing International';
	   $data['meta_description']	= 'San Miguel Brewing International';
	   $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
	   
	   $data['breadCrumbs']		= '';
	   
	  if($this->modules->access_checker()==TRUE)
	  {
			$this->load->view('innerPages',$data); 
	  }else{
		$data['vfile']				= 'login.php';
		$data['title']				= 'SMBi System Log-in | SMBi';
		$data['page_title']			= 'SMBi System Log-in';
		$data['meta_description']	= 'San Miguel Brewing International';
		$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
		$this->load->view('login',$data); 	
	   }
   }
	
	

} ?>