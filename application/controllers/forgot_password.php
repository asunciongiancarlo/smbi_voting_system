<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Forgot_password extends CI_Controller {
 
	  public function __construct()
       {
        parent::__construct();
		date_default_timezone_set('UTC');
		session_start();
		$this->load->model('c3model');
		$this->load->library('security');
		$this->load->library('email');
		$this->load->library('modules');
		$this->load->library('image_lib');
		$this->load->library('smtp');
		$this->load->helper('url');

		$this->load->library('forms');
		$this->load->library('fv');
		
		$this->output->enable_profiler(FALSE);
       }
   
   public function index()
   {			
	   
	   $data['vfile']				= 'forgot_password.php';
	   $data['meta_description']	= 'San Miguel Brewing International';
	   $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
	   
	   $sql = $this->db->query("select * from country WHERE id!=0");
	   $data['countries']   = $sql->result_array(); 
	   $data['breadCrumbs']		= '';
	    
	   $this->load->view('viewContainer',$data); 
   }
   
   
   function send()
   {
		extract($_POST);
		
		/*ADDED AUTHENTICATION FOR EMAIL*/
		$config = Array(		
		    'protocol' => 'smtp',
		    'smtp_host' => 'ssl://smtp.googlemail.com',
		    'smtp_port' => 465,
		    'smtp_user' => 'gian.asuncion@ph.c3-interactive.com',
		    'smtp_pass' => 'G14nc4rl04sunc10n',
		    'smtp_timeout' => '4',
		    'mailtype'  => 'html', 
		    'charset'   => 'iso-8859-1'
		); 
 
		$this->email->initialize($config);
		$this->email->set_newline("\r\n");
		/*ADDED AUTHENTICATION FOR EMAIL*/
		
		
		
		$sql = "SELECT full_name as admin_full_name, email_address  FROM forgot_password_email_receiver WHERE main_default != 'yes'";
		$sql = $this->db->query($sql);
		$receivers2 = $sql->result_array();
		
		$cc="";
		foreach($receivers2 as $r)
		{
			extract($r);
			$cc .= $email_address.",";
		}
		
		$cc = substr($cc, 0, -1);
		
		$sql = "SELECT full_name as admin_full_name, email_address as admin_email FROM forgot_password_email_receiver WHERE main_default = 'yes' LIMIT 0,1";
		$sql = $this->db->query($sql);
		$receivers = $sql->result_array();
		
		foreach($receivers as $receiver)
		{
			extract($receiver);
			
			$msg   = "Hi $admin_full_name, <br/><br/> From: ".$full_name ."<br/>";
			$msg  .= "User name: ".		$user_name 	    ."<br/>";
			$msg  .= "Email Address: ". $email_address2  ."<br/> ";
			$msg  .= "Was requesting for password reset of his/her account.<br/><br/>";
			$msg  .= "Thank you.<br/>";
			
			//SEND EMAIL
			$this->email->clear();
			$this->email->from('do.not.reply@smg.sanmiguel.com.ph', 'San Miguel Beer International, Request for Password Reset');
			$this->email->to($admin_email); 
			$this->email->cc($cc); 

			$this->email->subject('Request for Password Reset');
			$this->email->message($msg);	

			$this->email->send();
			
			//echo $this->email->print_debugger();
		}
		
		$data['vfile']		= 'forgot_password.php';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
	   
	    $sql = $this->db->query("select * from country WHERE id!=0");
	    $data['countries']   = $sql->result_array(); 
	    $data['breadCrumbs']		= '';
		$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Request for password reset has been sent, Thank you.");
		
	   $this->load->view('viewContainer',$data); 
   }
	
	

} ?>