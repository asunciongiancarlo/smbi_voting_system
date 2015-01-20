<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Generate_field extends CI_Controller {
 
	   function __construct()
       {
		parent::__construct();
		date_default_timezone_set('UTC');
		session_start();
		$this->load->model('c3model');
		$this->output->enable_profiler(FALSE);
       }
	
		
   public function index()
	{			

	}
	
	function Users_Field($countryID='')
	{
		if($countryID!='all'){ 
		$users = "SELECT user_id, admin_users.full_name as fname 
				  FROM items LEFT JOIN admin_users ON admin_users.id = items.user_id 
				  WHERE admin_users.countryID = $countryID
				  GROUP BY admin_users.id";
		}else{
		$users = "SELECT user_id, admin_users.full_name as fname 
				  FROM items LEFT JOIN admin_users ON admin_users.id = items.user_id 
				  GROUP BY admin_users.id";
		}
		
		$users = $this->db->query($users);
		$users = $users->result_array();
		
		echo "<select name='user_id' class='fl'  style='width:13%;margin-right:10px;'> "; 

				//if($_SESSION['super_admin']=='y'){
					echo "<option value='all' > All Users </option>";
				//}
				foreach($users as $u) 
				{ 
				 $fname = $u['fname'];
				 $uID   = $u['user_id'];
				 echo "<option value='$uID'> $fname </option>";   
				}  
	  echo "</select>";
		
	}
	
	function POSMType_FIELD($POSM_TypeID='')
	{	
		if($POSM_TypeID!=0){
			$sql = "SELECT table_fieldsID FROM itemType_POSM_table_fields WHERE POSM_TypeID = $POSM_TypeID LIMIT 0,1";
			$row = $this->db->query($sql);
			$row = $row->row();
			
			$this->load->library('forms');
			
			$CI =& get_instance();
			$CI->load->library('fv');
			
			
			switch($row->table_fieldsID){
				case 6:
					$OUTLETStatusID = '';
					echo $this->forms->select('OUTLETStatusID','OUTLET_Status','statusName',$CI->fv->label(6),$OUTLETStatusID,$CI->fv->v(6));
				break;
				
				case 7:
					$PremiumTypeID = '';
					echo $this->forms->form_fields2('select_premium','PremiumTypeID',$PremiumTypeID,$CI->fv->label(7),$CI->fv->v(7));
				break;
			}
		}
	}
	
	function price_range($POSMStatusID='',$POSM_TypeID='')
	{	$CI =& get_instance();
		$CI->load->library('fv');
		$CI2 =& get_instance();
		$CI2->load->library('forms');
		//SET STATUS & TYPE
		if($POSMStatusID!='' & $POSM_TypeID!='')
		{
		 if($CI->fv->fieldChecker($POSMStatusID,31)=='y'){
			echo $CI2->forms->selectPriceRange('price_rangeID','price_range','level_name',$CI->fv->label(86),$price_rangeID='',$CI->fv->v(86),""," WHERE POSMTypeID = $POSM_TypeID");
		 }else{
			echo "<h2>".$CI->fv->label(86)."</h2>";
			echo "<label style='color:gray;'>Price range is not avaialable for this type of item. </label>";
		 }
		}else{
		 echo "<h2>".$CI->fv->label(86)."</h2>";
		 echo "<label style='color:gray;'>Please select an item type. </label>";
		}
	}
	
	
	//TEST ABSOLUTE 
	function testData($POSMStatusID='',$POSM_TypeID='',$price_rangeID='',$USD='')
	{ $CI =& get_instance();
	  $CI->load->library('fv');
	  //CHECK IF POTENTIAL
	  /*
	  if($CI->fv->fieldChecker($POSMStatusID,31)=='y' AND ($POSM_TypeID!='' OR $POSM_TypeID!=0) AND $price_rangeID!='' AND $USD!=''){
		//ITEM TYPE
		//TEST DATA
		 $sql = $this->db->query("SELECT cond1, min_val, logical_operator, cond2, max_val FROM price_range WHERE id = $price_rangeID LIMIT 0,1");
		 $sql 			   = $sql->row();
		 $cond1   		   = $sql->cond1;
		 $min_val 		   = $sql->min_val;
		 $logical_operator = $sql->logical_operator;
		 $cond2 		   = $sql->cond2;
		 $max_val 		   = $sql->max_val;
		 if($logical_operator=="" AND $max_val==""){
		 $query = "SELECT $USD $cond1 $min_val AS test LIMIT 0,1";
		 }else{
		 $query = "SELECT $USD $cond1 $min_val $logical_operator $USD $cond2 $max_val as test LIMIT 0,1";
		 }
		 $query = $this->db->query($query);
		 $query = $query->row();
		 if($query->test==1) echo "ok";
		 else 		   		 echo "not";
		 
	  }*/
	  echo "ok";
	}
	
	function testData2($price_rangeID='',$USD='')
	{ 
	  //CHECK IF POTENTIAL
	  if($price_rangeID!='' AND $USD!=''){
		//ITEM TYPE
		//TEST DATA
		 $sql = $this->db->query("SELECT cond1, min_val, logical_operator, cond2, max_val FROM price_range WHERE id = $price_rangeID LIMIT 0,1");
		 $sql 			   = $sql->row();
		 $cond1   		   = $sql->cond1;
		 $min_val 		   = $sql->min_val;
		 $logical_operator = $sql->logical_operator;
		 $cond2 		   = $sql->cond2;
		 $max_val 		   = $sql->max_val;
		 if($logical_operator=="" AND $cond2=="" AND $max_val==""){
		 $query = "SELECT $USD $cond1 $min_val AS test LIMIT 0,1";
		 }else{
		 $query = "SELECT $USD $cond1 $min_val $logical_operator $USD $cond2 $max_val as test LIMIT 0,1";
		 }
		 $query = $this->db->query($query);
		 $query = $query->row();
		 if($query->test==1) echo "ok";
		 else 		   		 echo "not";
	  }
	}
	
	function testData3($price_rangeID='',$USD='')
	{ 
	  //CHECK IF POTENTIAL
	  if($price_rangeID!='' AND $USD>0){
		//ITEM TYPE
		//TEST DATA
		 $sql = $this->db->query("SELECT cond1, min_val, logical_operator, cond2, max_val FROM price_range WHERE id = $price_rangeID LIMIT 0,1");
		 $sql 			   = $sql->row();
		 $cond1   		   = $sql->cond1;
		 $min_val 		   = $sql->min_val;
		 $logical_operator = $sql->logical_operator;
		 $cond2 		   = $sql->cond2;
		 $max_val 		   = $sql->max_val;
		 if($logical_operator=="" AND $cond2=="" AND $max_val==""){
		 $query = "SELECT $USD $cond1 $min_val AS test LIMIT 0,1";
		 }else{
		 $query = "SELECT $USD $cond1 $min_val $logical_operator $USD $cond2 $max_val as test LIMIT 0,1";
		 }
		 $query = $this->db->query($query);
		 $query = $query->row();
		 if($query->test==1) echo "ok";
		 else 		   		 echo "not";
	  }else{
		echo "ok";
	  }
	}
	
	function Brand_per_BU($countryID='')
	{
		$brands = "SELECT brands.id as bID, brandName FROM brandXref 
				   LEFT JOIN  brands ON brands.id = brandXref.brandID 
				   WHERE brandXref.countryID = $countryID
				   ORDER BY brandName ASC";

		$brands = $this->db->query($brands);
		$brands = $brands->result_array();
		
		echo "<select name='brandID' class='fl' style='width:25%;margin-right:10px;'>> "; 
				echo "<option value=''> BRAND </option>";
				foreach($brands as $u) 
				{ 
				 $bID 		  = $u['bID'];
				 $brandName   = $u['brandName'];
				 echo "<option value='$bID'> $brandName </option>";   
				}  
	  echo "</select>";
	}
	
	function ecPOSMType_FIELD($POSM_TypeID='')
	{
		if($POSM_TypeID!=0){
			$sql = "SELECT table_fieldsID FROM ecitemType_POSM_table_fields WHERE POSM_TypeID = $POSM_TypeID LIMIT 0,1";
			$row = $this->db->query($sql);
			$row = $row->row();
			
			$this->load->library('forms');
			
			$CI =& get_instance();
			$CI->load->library('fv');
			
			
			switch($row->table_fieldsID){
				case 5:
					$OUTLETStatusID = '';
					echo $this->forms->select('OUTLETStatusID','OUTLET_Status','statusName',$CI->fv->label(6),$OUTLETStatusID,$CI->fv->v(6));
				break;
				
				case 7:
					$PremiumTypeID = '';
					echo $this->forms->form_fields2('select_premium','PremiumTypeID',$PremiumTypeID,$CI->fv->label(7),$CI->fv->v(7));
				break;
			}	
		}
	}
	
} ?>