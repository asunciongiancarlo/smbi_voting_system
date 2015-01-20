<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Alert extends CI_Controller
 {
 
	public function __construct()
       {
            parent::__construct();
			$this->load->model('c3model');
       }
	   
	function check($msg)
	{
		extract($msg);
					
		$HTTP_PATH = HTTP_PATH;
		
		if($msg_type=="alert-success")
			$alert = "<div class='alert alert-success'>
					<span><img src='$HTTP_PATH/img/success.png' width='31' height='31'></span> $msg_desc
				 </div>";
		
		if($msg_type=="alert-warning")
			$alert = "<div class='alert alert-warning'>
					<span><img src='$HTTP_PATH/img/warning.png' width='31' height='31'></span> $msg_desc
				</div>";
		
		if($msg_type=="alert-info")
			$alert = "<div class='alert alert-info'>
				<span><img src='$HTTP_PATH/img/info.png' width='31' height='31'></span> $msg_desc
			</div>";
			
		return $alert;
	}  


 } 

?>