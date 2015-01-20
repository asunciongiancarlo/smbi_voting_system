<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rec_logs extends CI_Controller
{ 
	public function __construct()
    {
		parent::__construct();
		$this->load->model('c3model');
		$this->load->library('security');
    }
	
	function w($rec_id='',$rec_name='',$mod='',$tbl='',$actn='',$itemCode='')
	{
		//SET DATE
		$this->load->helper('date');

		$date = "%Y-%m-%d";
		$time = "%h:%i:%s"; 
		$date = mdate($date,time());
		$time = date('H:i:s');
	
		$dbFields['rec_id'] 		  = $rec_id;
		$dbFields['itemCode']         = isset($itemCode) ? $itemCode : NULL;
		$dbFields['rec_name'] 		  = isset($rec_name) ? $rec_name : '';
		$dbFields['module_name'] 	  = $mod;
		$dbFields['table_name'] 	  = $tbl;
		$dbFields['action']   		  = $actn;
		$dbFields['tdate']    		  = $date;
		$dbFields['ttime']   		  = $time;
		$dbFields['ip_address'] 	  = $_SERVER['REMOTE_ADDR'];
		$dbFields['user_id']          = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
		$dbFields['country_id']       = isset($_SESSION['countryID']) ? $_SESSION['countryID'] : 0;
	
		$res = $this->c3model->c3crud("insert","logs",$dbFields,'');
	}
} 
?>