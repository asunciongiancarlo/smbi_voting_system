<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Gmail_SMTP extends CI_Controller {
 
  public function __construct()
   {
		parent::__construct();
		date_default_timezone_set('UTC');
		session_start();
		$this->load->model('c3model');
		$this->load->library('email');
		//$this->load->library('modules');
		$this->output->enable_profiler(FALSE);
		
   }
   
	/*iLike Campaign*/
	function index()
	{
		phpinfo();
		
		$config = Array(		
		    'protocol' => 'smtp',
		    'smtp_host' => 'ssl://smtp.googlemail.com',
		    'smtp_port' => 465,
		    'smtp_user' => 'asunciongiancarlo@gmail.com',
		    'smtp_pass' => 'G14nc4rl0',
		    'smtp_timeout' => '4',
		    'mailtype'  => 'text', 
		    'charset'   => 'iso-8859-1'
		); 
 
		$this->email->initialize($config);
		$this->email->set_newline("\r\n");
		


		$this->email->from('blablabla@gmail.com', 'Blabla');
		$list = array('asunciongiancarlo@gmail.com','gian.asuncion@ph.c3-interactive.com','asuncion.giancarlo@yahoo.com');
		$this->email->to($list);
		$this->email->subject('This is an email test');
		$this->email->message('It is working. Great!');
		
		if($this->email->send())
		{
			echo 'Email sent.';
		}
		else
		{
			show_error($this->email->print_debugger());
		}
	
	}

}	
?>	