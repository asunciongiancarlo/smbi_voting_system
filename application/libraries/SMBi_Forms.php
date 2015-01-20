<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Forms extends CI_Controller
 { 
	public function __construct()
    {
		parent::__construct();
		$this->load->model('c3model');
		$this->load->library('security');
    }


	

 }
 
?>