<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class mail_checker extends CI_Controller {
 
  public function __construct()
   {
		parent::__construct();
		date_default_timezone_set('UTC');
		session_start();
		$this->output->enable_profiler(FALSE);
		$this->load->library('smtp');
		
   }
   
	/*iLike Campaign*/
	function index()
	{
	
	}
	
	function m($email)
	{
		// an optional sender
		$sender = 'asunciongiancarlo@gmail.com';

		$SMTP_Validator->debug = true;
		// do the validation
		$results = $this->smtp->validate(array($email), $sender);

		if ($results[$email]) {
		  echo "valid";
		} else {
		  echo "invalid";
		}
	}
	
	
}	
?>	