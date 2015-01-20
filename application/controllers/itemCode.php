<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class itemCode extends CI_Controller {
 
   public function __construct()
   {
		parent::__construct();
		date_default_timezone_set('UTC');
		session_start();
		$this->load->model('c3model');
		$this->load->library('security');
		$this->load->library('modules');
		$this->load->library('rec_logs');
		$this->output->enable_profiler(FALSE);
		set_time_limit(0);

   }
    
    function index()
    {
		$sql 	= "SELECT *, items.countryID as item_Country_ID, items.id as itemID FROM items";
		$sql 	= $this->db->query($sql);
		$items 	= $sql->result_array();
		
		$sql 	   = "SELECT *, id as cID FROM country";
		$sql 	   = $this->db->query($sql);
		$countries = $sql->result_array();
		
		foreach($items as $i)
		{	$item_code='';
			extract($i);
			
			/*COUNTRY CODE*/
			foreach($countries as $country)
			{ extract($country);
			  if($cID == $item_Country_ID)
				$itemCode .= $countryCode;
			}
			
			/*ITEM TYPE*/
			if($POSMTypeID==23){
				$itemCode .= "-SI-";
			}elseif($POSMTypeID==29){
				$itemCode .= "-PI-";
			}else{
				$itemCode .= "-XX-";
			}
			
			/*SERIES*/
			$coding = "%04d";
			if($itemID>9999)
				$coding = "%0".strlen($itemID)."d";
			
			$itemCode .= sprintf($coding, $itemID);
			
			
			/*DATE RELEASED*/
			$dateRealesed = '0000-00-00';
			if($publish=='y')
				$dateRealesed = date('Y-m-d');
			
			
			/*CREATOR*/
			if($item_Country_ID==17)
				$user_id = 61;
			if($item_Country_ID==18)
				$user_id = 75;
			if($item_Country_ID==19)
				$user_id = 74;
			if($item_Country_ID==20)
				$user_id = 63;
			if($item_Country_ID==21)
				$user_id = 84;
			if($item_Country_ID==22)
				$user_id = 73;
			if($item_Country_ID==23)
				$user_id = 72;

			
			$sql = "UPDATE items SET itemCode='$itemCode', dateReleased='$dateRealesed', user_id='$user_id' WHERE id = $itemID";
			$this->db->query($sql);
			
			echo "Item code: ".$itemCode." Date RELEASED $dateRealesed User ID: $user_id <br/>";
		}

	}
	
	function ec()
    {
		$sql 	= "SELECT *, ec_items.id as itemID FROM ec_items";
		$sql 	= $this->db->query($sql);
		$items 	= $sql->result_array();
		
		$sql 	   = "SELECT *, id as cID FROM country";
		$sql 	   = $this->db->query($sql);
		$countries = $sql->result_array();
		
		foreach($items as $i)
		{	$item_code='';
			extract($i);
			$itemCode = "EC";
			
			/*ITEM TYPE*/
			if($POSMTypeID==23){
				$itemCode .= "-SI-";
			}elseif($POSMTypeID==29){
				$itemCode .= "-PI-";
			}else{
				$itemCode .= "-XX-";
			}
			
			/*SERIES*/
			$coding = "%04d";
			if($itemID>9999)
				$coding = "%0".strlen($itemID)."d";
			
			$itemCode .= sprintf($coding, $itemID);
			
			
			/*DATE RELEASED*/
			$dateRealesed = '0000-00-00';
			if($publish=='y')
				$dateRealesed = date('Y-m-d');
			
			$user_id = 58;
			
			$sql = "UPDATE ec_items SET itemCode='$itemCode', dateReleased='$dateRealesed', user_id='$user_id' WHERE id = $itemID";
			$this->db->query($sql);
			
			echo "Item code: ".$itemCode." Date RELEASED $dateRealesed User ID: $user_id <br/>";
		}

	}

}