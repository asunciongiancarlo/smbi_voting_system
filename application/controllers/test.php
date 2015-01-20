<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Test extends CI_Controller {
 
   public function __construct()
   {
		parent::__construct();
		date_default_timezone_set('UTC');
		session_start();
		$this->load->model('c3model');
		$this->load->library('security');
		$this->load->library('modules');
		$this->load->helper('url');
		$this->output->enable_profiler(FALSE);
		ini_set('memory_limit', '-1');
		//echo getcwd();
   }
   
   function rename()
   {
    $sql 			     = $this->db->query("SELECT * FROM items_images WHERE itemID = 1866 ORDER BY defaultStatus DESC");
	$data['items_images'] = $sql->result_array();
	$this->load->view('test_rename.php',$data); 
	
    
   }
  
}