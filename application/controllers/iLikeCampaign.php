<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ILikeCampaign extends CI_Controller {
 
  public function __construct()
   {
		parent::__construct();
		error_reporting(E_ALL);
		date_default_timezone_set('UTC');
		session_start();
		$this->load->model('c3model');
		$this->load->library('security');
		$this->load->library('email');
		$this->load->library('modules');
		$this->load->library('smtp');
		$this->load->helper('url');
		$this->output->enable_profiler(FALSE);
		//print_r($_SESSION);
		$this->modules->session_handler();
   }
	
	public function index()
	{			
	   $this->modules->module_checker(27,'REVIEW');
	   
	   $data['vfile']		= 'iLikeCampaign.php';
	   $data['title']		= 'San Miguel Brewing International';
	   $data['page_title']	= 'I Like Campaign';
	   $data['meta_description']	= 'San Miguel Brewing International';
	   $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
	   
	   $HTTP_PATH = HTTP_PATH;
	   $data['breadCrumbs']			= '<a href='.$HTTP_PATH.'iLikeCampaign> I Like Campaign </a>';
	   
	   if($this->modules->access_checker()==TRUE)
	   {
		$this->load->view('menu',$data); 
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
    
	function iLikeCampaign_Canvassing_Rules($votingCampaignID='',$revote='')
	{
		$filter_AND   = $this->modules->country2();
		$filter_WHERE = $this->modules->country();
		$CI2 =& get_instance();
		$CI2->load->library('fv');
		
		if($_SESSION['super_admin']=='y' OR $_SESSION['countryID']==0){
			$sql = "SELECT countryID FROM campaign WHERE id = $votingCampaignID LIMIT 0,1";
			$sql = $this->db->query($sql);
			$row = $sql->row();
			
			$filter_WHERE = "WHERE countryID = ".$row->countryID;
		}
		
		//GET ALL ILIKE CONDITIONS
		if($revote==TRUE){
			$sql 				  = "SELECT *, iLikeCanvassingRulesXref.price_rangeID as pRangeID FROM iLikeCanvassingRulesXref WHERE campaignID = $votingCampaignID ORDER BY price_rangeID ASC, fieldID DESC, rel DESC, val ASC";
			$sql 				  = $this->db->query($sql);
			$iLikeCanvassingRules = $sql->result_array();
		}else{
			$sql 				  = "SELECT *, iLikeCanvassingRules.price_rangeID as pRangeID FROM iLikeCanvassingRules $filter_WHERE ORDER BY price_rangeID ASC, fieldID DESC, rel DESC, val ASC";
			$sql 				  = $this->db->query($sql);
			$iLikeCanvassingRules = $sql->result_array();
		}
		
		$rules = ""; 
		//print_r($iLikeCanvassingRules);
		foreach($iLikeCanvassingRules as $iL)
		{
			extract($iL);		
			$orig_fieldName = $fieldName;
			switch($fieldName){
					case("POSMTypeID"):
						$tableName	= $CI2->fv->label(4);;
						$fieldName  = 'typeName';
						$table 		= 'POSM_Type';
					break;
					case("POSMStatusID"):
						$tableName	= 'ITEM STATUS';
						$fieldName  = 'statusName';
						$table 		= 'POSM_Status';
					break;
					case("OUTLETStatusID"):
						$tableName	= $CI2->fv->label(6); ;
						$fieldName  = 'statusName';
						$table 		= 'OUTLET_Status';
					break;
					case("PremiumTypeID"):
						$tableName	= $CI2->fv->label(7);;
						$fieldName  = 'premiumTypeName';
						$table 		= 'premiumItemType';
					break;
					case("MaterialTypeID"):
						$tableName	= $CI2->fv->label(9);;
						$fieldName  = 'materialName';
						$table 		= 'MATERIAL_Type';
					break;
					case("brandID"):
						$tableName	= $CI2->fv->label(3);;
						$fieldName  = 'brandName';
						$table 		= 'brands';
					break;
			}
			
			$query 		= $this->db->query("SELECT $fieldName FROM $table WHERE id=$fieldID LIMIT 0,1");
			$row 		= $query->row();
			$name_Field = $row->$fieldName;
			
			//GET PRICE RANGE NAME
			$query 		= $this->db->query("SELECT extra_label FROM price_range WHERE id=$price_rangeID LIMIT 0,1");
			$row 		= $query->row();
			$extra_label = $row->extra_label;
			
			$status="";
			$original_val =$val;
			if(strpos($min_val,".")==TRUE) $min_val = $min_val."%";
			if(strpos($max_val,".")==TRUE) $max_val = $min_val."%";
			
			$rules[] = array(
							'cond1'				=>$cond1,
							'min_val'			=>$min_val,
							'logical_operator'	=>$logical_operator,
							'cond2'				=>$cond2,
							'max_val'			=>$max_val,
							'price_rangeName'	=>$extra_label,
							'table'				=>$table,
							'fieldName'			=>$orig_fieldName, 
							'fieldID'			=>$fieldID, 
							'fieldValue'		=>$name_Field, 
							'rel'				=>$rel,
							'val'				=>"$original_val",
							'lrel'				=>$lrel,
							'stat'				=>$status
							);
		}
		return $rules;
	}
	
	function comittees_vs_canvassing_rules($committees='')
	{
	//DETECT USER ID 
	$sql 				  = "SELECT min(min_val) as mVal FROM iLikeCanvassingRules WHERE countryID =". $_SESSION['countryID']." limit 0,1";
	$sql 				  = $this->db->query($sql);
	$iLikeCanvassingRules = $sql->row();
	//TEST EVERY CANVASSING RULES
	if($iLikeCanvassingRules->mVal > $committees) echo "not";
	else										  echo "ok";
	}
	
	function iLikeCampaign_Voting_Rules($votingCampaignID='',$revote='')
    {
		$filter_AND   = $this->modules->country2();
		$filter_WHERE = $this->modules->country();
		$CI2 =& get_instance();
		$CI2->load->library('fv');
		
		if($_SESSION['super_admin']=='y' OR $_SESSION['countryID']==0){
			$sql = "SELECT countryID FROM campaign WHERE id = $votingCampaignID LIMIT 0,1";
			$sql = $this->db->query($sql);
			$row = $sql->row();
			
			$filter_WHERE = "WHERE countryID = ".$row->countryID;
		}
		
		$NOT_IN = "AND items.id NOT IN (SELECT iLikeResultRef.itemID FROM iLikeResultRef) AND items.purge='n' AND items.archive = 'n'  AND DATEDIFF(CURDATE(),items.dateReleased) < ".$this->modules->itemsAge()."";
		//GET ALL ILIKE CONDITIONS
		if($revote==TRUE){
			$sql = "SELECT *, iLikeVotingRulesRef.price_rangeID as pRangeID FROM iLikeVotingRulesRef WHERE campaignID = $votingCampaignID ORDER BY price_rangeID ASC, fieldID DESC, rel DESC, val ASC";
			$sql = $this->db->query($sql);
			$iLikeVotingRules = $sql->result_array();
		}else{
			$sql = "SELECT *, iLikeVotingRules.price_rangeID as pRangeID FROM iLikeVotingRules $filter_WHERE ORDER BY price_rangeID ASC, fieldID DESC, rel DESC, val ASC";
			$sql = $this->db->query($sql);
			$iLikeVotingRules = $sql->result_array();
			$NOT_IN = "AND items.id NOT IN (SELECT iLikeResultRef.itemID FROM iLikeResultRef) AND items.purge='n' AND items.archive = 'n'  AND DATEDIFF(CURDATE(),items.dateReleased) < ".$this->modules->itemsAge()."";
		}
		
		//ITEMS
		$sqlCampRules = "SELECT *  FROM `iLikeCampaignRules` $filter_WHERE ";
		$CampRules = $this->db->query($sqlCampRules);
		$CampRules = $CampRules->result_array();
		$cfilters = "  $filter_WHERE "; $ifAnd="";
		//Select items  
			
		$range=0; $m=''; $and='';
		foreach($CampRules as $k=>$r)
		{
			extract($r);
			$rel=($rel=='==')?"=":$rel;
			if($rel=="" and ($val=='' or $val=="0")) // all items
			   $cfilters  .= " AND ($fieldName=$fieldID)";
			else
			   $cfilters  .=" AND ((select count(id) from items where $fieldName=$fieldID $filter_AND)  $rel $val ) ";  				
			   
			//DETERMIN IF IN RANGE
			if($rel=='<=' OR $rel=='>=')
				$range++;
		}
			
		//SET LIMIT
		if($range==2){
			foreach($CampRules as $k=>$r){
				extract($r);
				$and  = " AND ($fieldName=$fieldID)";
			}
			$cfilters = "  $filter_WHERE $and";
			$sql = "SELECT MIN(val) AS f, MAX(val) AS t FROM iLikeCampaignRules $filter_WHERE";
			$sql = $this->db->query($sql);
			$row = $sql->row();
			$m = "LIMIT ".$row->f.",".$row->t;
		}
		
		$rules = ""; 
		//print_r($iLikeVotingRules);
		foreach($iLikeVotingRules as $iL)
		{
			extract($iL);
			if($revote==TRUE){
				$itemDB = "SELECT count(items.id) as tot_items
						   FROM items  
						   LEFT JOIN campaignItemsXref ON campaignItemsXref.itemID = items.id  
						   WHERE campaignItemsXref.campaignID = $votingCampaignID
						   AND (items.$fieldName = $fieldID) AND items.price_rangeID =  $pRangeID";
			}else{
				$itemDB = "SELECT count(items.id) as tot_items
						   FROM items  $cfilters AND(items.$fieldName = $fieldID) AND items.publish = 'y' 
						   $NOT_IN AND items.price_rangeID =  $pRangeID";
			}
			
		
			$orig_fieldName = $fieldName;
			switch($fieldName){
					case("POSMTypeID"):
						$tableName	= $CI2->fv->label(4);;
						$fieldName  = 'typeName';
						$table 		= 'POSM_Type';
					break;
					case("POSMStatusID"):
						$tableName	= 'ITEM STATUS';
						$fieldName  = 'statusName';
						$table 		= 'POSM_Status';
					break;
					case("OUTLETStatusID"):
						$tableName	= $CI2->fv->label(6); ;
						$fieldName  = 'statusName';
						$table 		= 'OUTLET_Status';
					break;
					case("PremiumTypeID"):
						$tableName	= $CI2->fv->label(7);;
						$fieldName  = 'premiumTypeName';
						$table 		= 'premiumItemType';
					break;
					case("MaterialTypeID"):
						$tableName	= $CI2->fv->label(9);;
						$fieldName  = 'materialName';
						$table 		= 'MATERIAL_Type';
					break;
					case("brandID"):
						$tableName	= $CI2->fv->label(3);;
						$fieldName  = 'brandName';
						$table 		= 'brands';
					break;
			}
			
			$query 		= $this->db->query("SELECT $fieldName FROM $table WHERE id=$fieldID LIMIT 0,1");
			$row 		= $query->row();
			$name_Field = $row->$fieldName;
			
			//GET PRICE RANGE NAME
			$query 		= $this->db->query("SELECT extra_label FROM price_range WHERE id=$price_rangeID LIMIT 0,1");
			$row 		= $query->row();
			$extra_label = $row->extra_label;
			
			$s 		   		= $this->db->query($itemDB);
			$data['items']  = $s->result_array();		
			//print_r($data['items']);
			$status="Good";
			
			//TEST EVERY RULE
			$tot_items = $data['items'][0]['tot_items'];
			$min_vote = $min_val;
			$max_vote = $max_val;
			if($cond1=="==") $cond1="=";
			if($cond2=="==") $cond2="=";
			if(strpos($min_val,".")==TRUE)
				$min_vote = round($min_val * $data['items'][0]['tot_items']); 
			if(strpos($max_val,".")==TRUE)
				$max_vote = round($max_vote * $data['items'][0]['tot_items']); 
			
			$min_number_of_items = $min_vote;
			
			if($logical_operator!="" AND $max_vote!="" AND $cond2!=""){
			$sql = $this->db->query("SELECT $tot_items >= $min_vote  as result LIMIT 0,1");			
			}else{
			$sql = $this->db->query("SELECT $tot_items >= $min_vote as result LIMIT 0,1");
			}
			$sql = $sql->row();
			if($sql->result==0) 				$status="Not Good";
			if($tot_items==0 OR $min_vote==0)   $status="Not Good";
			//PERCENTAGE
			if(strpos($min_val,".")==TRUE){
			 $min_number_of_items   = round($min_val * $data['items'][0]['tot_items']);
			 $min_val  				= ($min_val*100)."% (". round($min_val * $data['items'][0]['tot_items']) .")";
			}if(strpos($max_val,".")==TRUE){
			 $max_val 			    = ($max_val*100)."% (". round($max_val * $data['items'][0]['tot_items']) .")";	
			}
			
			$rules[] = array(
							'cond1'				=>$cond1,
							'min_val'			=>$min_val,
							'logical_operator'	=>$logical_operator,
							'cond2'				=>$cond2,
							'max_val'			=>$max_val,
							'price_rangeName'	=>$extra_label,
							'table'				=>$table,
							'fieldName'			=>$orig_fieldName, 
							'fieldID'			=>$fieldID, 
							'fieldValue'		=>$name_Field, 
							'min_number_of_items'=>$min_number_of_items, 
							'stat'				=>$status,
							'current_num_items' =>$data['items'][0]['tot_items']
							);
		}
		return $rules;
	}	

    function iLikeCampaign_Items_Checker($votingCampaignID='',$revote='')
    {
		$filter_AND   = $this->modules->country2();
		$filter_WHERE = $this->modules->country();
		$CI2 =& get_instance();
		$CI2->load->library('fv');
		
		if($_SESSION['super_admin']=='y' OR $_SESSION['countryID']==0){
			$sql = "SELECT countryID FROM campaign WHERE id = $votingCampaignID LIMIT 0,1";
			$sql = $this->db->query($sql);
			$row = $sql->row();
			
			$filter_WHERE = "WHERE countryID = ".$row->countryID;
		}
		
		//GET ALL ILIKE CONDITIONS
		if($revote==TRUE){
			$sql = "SELECT * FROM iLike_Rules_Ref WHERE campaignID = $votingCampaignID";
			$sql = $this->db->query($sql);
			$iLikeCampaignRules = $sql->result_array();
		}else{
			$sql = "SELECT * FROM iLikeCampaignRules $filter_WHERE ORDER BY fieldID DESC";
			$sql = $this->db->query($sql);
			$iLikeCampaignRules = $sql->result_array();
		}
		
		$rules = ""; 
		foreach($iLikeCampaignRules as $iL)
		{
			extract($iL);
			
			//ITEMS 
			$itemDB = "SELECT count(items.id) as tot_items
					FROM items 
					LEFT JOIN POSM_Type 		ON items.POSMTypeID = POSM_Type.id 
					LEFT JOIN POSM_Status 		ON items.POSMStatusID = POSM_Status.id 
					LEFT JOIN OUTLET_Status 	ON items.OUTLETStatusID = OUTLET_Status.id
					LEFT JOIN premiumItemType 	ON items.PremiumTypeID = premiumItemType.id 
					LEFT JOIN MATERIAL_Type 	ON items.MaterialTypeID = MATERIAL_Type.id 
					LEFT JOIN country 			ON items.countryID = country.id 
					WHERE items.id NOT IN (SELECT iLikeResultRef.itemID FROM iLikeResultRef) AND items.publish='y' 	
					$filter_AND 
					AND(items.$fieldName = $fieldID)";
			
			$orig_fieldName = $fieldName;
			switch($fieldName){
					case("POSMTypeID"):
						$tableName	= $CI2->fv->label(4);;
						$fieldName  = 'typeName';
						$table 		= 'POSM_Type';
					break;
					case("POSMStatusID"):
						$tableName	= 'ITEM STATUS';
						$fieldName  = 'statusName';
						$table 		= 'POSM_Status';
					break;
					case("OUTLETStatusID"):
						$tableName	= $CI2->fv->label(6); ;
						$fieldName  = 'statusName';
						$table 		= 'OUTLET_Status';
					break;
					case("PremiumTypeID"):
						$tableName	= $CI2->fv->label(7);;
						$fieldName  = 'premiumTypeName';
						$table 		= 'premiumItemType';
					break;
					case("MaterialTypeID"):
						$tableName	= $CI2->fv->label(9);;
						$fieldName  = 'materialName';
						$table 		= 'MATERIAL_Type';
					break;
					case("brandID"):
						$tableName	= $CI2->fv->label(3);;
						$fieldName  = 'brandName';
						$table 		= 'brands';
					break;
			}
			
			$query 		= $this->db->query("SELECT $fieldName FROM $table WHERE id=$fieldID LIMIT 0,1");
			$row 		= $query->row();
			$name_Field = $row->$fieldName;
			
			$sql 		   = $this->db->query($itemDB);
			$data['items'] = $sql->result_array();		
			$status="";
			
			switch($rel){
				case(">"):
					if($data['items'][0]['tot_items'] > $val)
						 $status = "Good";
					else
						 $status = "Not Good";
				break;
				case("<"):
					if($data['items'][0]['tot_items'] < $val)
						 $status = "Good";
					else
						 $status = "Not Good";
				break;
				case(">="):
					if($data['items'][0]['tot_items'] >= $val)
						 $status = "Good";
					else
						 $status = "Not Good";
				break;
				case("<="):
					if($data['items'][0]['tot_items'] <= $val)
						 $status = "Good";
					else
						 $status = "Not Good";
				break;
				case("=="):
					if($data['items'][0]['tot_items'] == $val)
						 $status = "Good";
					else
						 $status = "Not Good";
				break;
			}
			
			$rules[] = array('table'=>$table,
							'fieldName'=>$orig_fieldName, 
							'fieldID'=>$fieldID, 
							'fieldValue'=>$name_Field, 
							'rel'=>$rel,
							'val'=>$val,
							'status'=>$status,
							'current_num_items'=>$data['items'][0]['tot_items']
							);
		}

		return $rules;
	}	

	function min_committees($votingCampaignID='',$revote='')
	{
		$filter_WHERE = $this->modules->country();
		
		if($_SESSION['super_admin']=='y'){
			$sql = "SELECT countryID FROM campaign WHERE id = $votingCampaignID LIMIT 0,1";
			$sql = $this->db->query($sql);
			$row = $sql->row();
			
			$filter_WHERE = "WHERE countryID = ".$row->countryID;
		}
		
		//GET NUM COMMITEES FROM REF TABLE
		if($revote==TRUE){
			$sql = $this->db->query("SELECT num_commitee FROM iLike_Rules_No_Committes_Ref WHERE campaignID = $votingCampaignID LIMIT 0,1");
			$row = $sql->row();
		}else{
			$sql = $this->db->query("SELECT num_commitee FROM iLikeCampaignNumber_of_commitees $filter_WHERE LIMIT 0,1");
			$row = $sql->row();
		}
		
		if($row->num_commitee)
			return $row->num_commitee;
		else
			return 0;
	}
	
	function min_number_of_committees($votingCampaignID='',$revote='')
	{
		$filter_WHERE = $this->modules->country();
		
		if($_SESSION['super_admin']=='y'){
			$sql = "SELECT countryID FROM campaign WHERE id = $votingCampaignID LIMIT 0,1";
			$sql = $this->db->query($sql);
			$row = $sql->row();
			
			$filter_WHERE = "WHERE countryID = ".$row->countryID;
		}
		
		//GET NUM COMMITEES FROM REF TABLE
		if($revote==TRUE){
			$sql = $this->db->query("SELECT min(min_val) as mVal FROM iLikeCanvassingRulesXref WHERE campaignID = $votingCampaignID LIMIT 0,1");
			$row = $sql->row();
		}else{
			$sql = $this->db->query("SELECT min(min_val) as mVal FROM iLikeCanvassingRules $filter_WHERE LIMIT 0,1");
			$row = $sql->row();
		}
		
		if($row->mVal)
			return $row->mVal;
		else
			return 0;
	}
	
	function alter_canvassing_rules($action='',$campaignID='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		$this->modules->module_checker(27,'ALTER CANVASSING RULES');
		
		$data['vfile']				= 'alter_canvassing_rulesFORM.php';
	    $data['title']				= 'iLike Campaign Rules | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
				
		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
	    $data['breadCrumbs']		= '<a href='.$HTTP_PATH.'iLikeCampaign/votingCampaign>  iLike Campaign </a>';
	    $data['breadCrumbs']	   .= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']	   .= "<a href='".HTTP_PATH."iLikeCampaign/alter_canvassing_rules/edit/$campaignID'> Alter Canvassing Rules </a>";

		//CSRF
		$data['csrf'] = "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>"; 
		$table 		  = "iLikeCanvassingRules";
		
		extract($_POST);
		if($action=="edit")
		{
			$data['campaignID']	= $campaignID;
			$sql = $this->db->query("SELECT countryID FROM campaign WHERE id = $campaignID LIMIT 0,1");
			$row = $sql->row();
			$data['countryID']	= $row->countryID;
			$data['vfile']	    = 'alter_canvassing_rulesFORM.php';
		}
		elseif($action=="update")
		{
			if($_POST==NULL){
				redirect(HTTP_PATH.'iLikeCampaign/votingCampaign', 'location', 301);
				die();
			}
			
			$this->db->query("DELETE FROM iLikeCanvassingRulesXref WHERE campaignID = $campaignID");
			foreach($fieldIDs as $k => $val)
			{	
				$dbFields['fieldName']  		=  'POSMTypeID';
				$dbFields['campaignID']  		=  $campaignID;
				$dbFields['fieldID']  			=  $val;
				$dbFields['price_rangeID']  	=  $price_rangeIDs[$k];
				$dbFields['cond1']  			=  $cond1s[$k];
				$dbFields['min_val'] 			=  $min_vals[$k];
				$dbFields['logical_operator'] 	=  $logical_operators[$k];
				$dbFields['cond2'] 				=  $cond2s[$k];
				$dbFields['max_val'] 			=  $max_vals[$k];
				$dbFields['countryID'] 			=  $countryID;
				$dbFields['dateAdded'] 	   		=  date('Y-m-d');
				
				$res = $this->c3model->c3crud("insert",'iLikeCanvassingRulesXref',$dbFields,'');
			}
			//LOGS
			$sql = $this->db->query("SELECT campaignName FROM campaign WHERE id = $campaignID LIMIT 0,1");
			$row = $sql->row();
			$CI->rec_logs->w($campaignID,$row->campaignName,'iLike','iLike Canvassing Rules','alter canvassing rules');
		
			//UPDATE CAMPAIGN
			$this->db->query("UPDATE campaign SET campaign.status='on progress', campaign.remarks='alter canvassing rules' WHERE id = $campaignID");
			
			redirect('iLikeCampaign/votingCampaign/iLike_canvassing_rules_has_been_altered', 'location', 301);
		}
	    $this->load->view('innerPages',$data); 
	}
	
	function resend_Email($campaignID='',$voterID='')
	{
		$sql 		= $this->db->query("SELECT * FROM campaign WHERE id = $campaignID LIMIT 0,1");
		$campaign	= $sql->result_array();
		
		foreach($campaign as $c)
		{	extract($c);
		
			$voters 	= $this->db->query("SELECT * FROM voters WHERE id = $voterID AND votingStatus = 'invited' LIMIT 0,1");
			$voters		= $voters->result_array();
			
			//PRICE RANGES
			$levels = $this->db->query("SELECT distinct(level_name) as level_name FROM `price_range` ORDER BY id ASC");
			$levels = $levels->result_array();
			
			foreach($voters as $voter){
				extract($voter);
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
		
				//CREATE MESSAGE
				$msg  = "";
				$msg = "Hello ". $fname ." ". $lname."! <br/><br/>"; 
				$msg .= "Welcome to the SMBIL Regional POSM Project!<br/><br/>"; 
				
				$msg .= "The iLike Campaign is now online and you have been selected as part of the <b>NOMINATION COMMITTEE</b>. <br/>"; 
				$msg .= "Please review <b>ALL</b> the items we have prepared for you.  <br/>"; 
				$msg .= "Your choices are important to us  :-)  <br/><br/>";
				
				$msg .= "GUIDELINES: <br/>"; 
				$msg .= "<ul> 
							<li>Please make sure to cast your votes by clicking <b>\"Like\"</b> or <b>\"Not Now\"</b> on all the items.</li>
							<li>You will be asked to vote on <b>ITEMS PER PRICE CATEGORY:</b> </li>
							<li style='list-style: none;'>&nbsp;</li>";
							//LEVELS
							foreach($levels as $level){
							extract($level);
							$msg .= "<li style='list-style: none;margin-left: 25px;'>- <label style='text-transform:uppercase'>$level_name</label> Items</li>";
							}
							
				$msg .=		"<li style='list-style: none;'>&nbsp;</li> <li>Please note that you <b>must</b> reach the <b>\"Thank You\" Page </b> for the system to record your votes. </li>
						 </ul><br/>";
						 
				$msg .= "Voting period is from ".date("M d, Y", strtotime($DateFrom))." to ".date("M d, Y", strtotime($DateTo)).".  <br/><br/>"; 
				
				$msg .= "You may click on the link below to start voting: <br/>";	
				$msg .= "<a href='".HTTP_PATH."gallery/voting/".$this->encode_base64($campaignID) ."/". $this->encode_base64($email)."'>link</a><br/><br/> 
						Having problem with the link? Copy and paste the URL below to your browser's address bar:<br/>".HTTP_PATH."gallery/voting/".$this->encode_base64($campaignID) ."/". $this->encode_base64($email)."<br/><br/>";	
				
				$msg .= "Thank you very much for your participation. <br/>";
				$msg .= "Happy Voting! <br/><br/>";
				
				$msg .= "This is a follow-up email.<br/><br/>";
				
				
				//SEND EMAIL
				$this->email->clear();
				$this->email->from('do.not.reply@smg.sanmiguel.com.ph', 'San Miguel Beer International');
				$this->email->to($email); 

				$this->email->subject('iLike Campaign - Follow Up');
				$this->email->message($msg);	
				
				//CURRENT DATE
				$sql   = "SELECT DATEDIFF('$DateTo',curdate()) AS f";
				$sql   = $this->db->query($sql);
				$d 	   = $sql->row();
				$from  = $d->f;
				
				if($from>=0){
					if($this->email->send()==TRUE)
						echo "ok";
					else
						echo "not";
				}else{
					echo "not";
				}
				
			}
	
		}
		
	}
	
	function votingCampaign($action='',$id='',$isPub='')
	{	
		//print_r($_SESSION);
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(27,'REVIEW');
		$filter_WHERE = $this->modules->country();
		$filter_AND   = $this->modules->country2();
		$votingCampaignID = $id;
		
		$data['USER_MANUAL'] = $this->modules->user_manual(41);
		
		$table						= 'campaign';
		$data['vfile']				= 'votingCampaign.php';
	    $data['title']				= 'Voting Campaign | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."iLikeCampaign/votingCampaign";
	    $data['breadCrumbs']		= '<a href='.$HTTP_PATH.'> iLike Campaign </a>';
		
		//CRUD
		$data['ADD'] 	=  $this->modules->crud_checker(27,'ADD');
		$data['EDIT'] 	=  $this->modules->crud_checker(27,'EDIT');
		$data['DELETE'] =  $this->modules->crud_checker(27,'DELETE');
		$data['REVIEW'] =  $this->modules->crud_checker(27,'REVIEW');
		$data['ALTER_CAMPAIGN'] =  $this->modules->crud_checker(27,'ALTER CAMPAIGN');
		$data['ALTER_RESULTS'] =  $this->modules->crud_checker(27,'ALTER RESULTS');
		$data['ALTER_CANVASSING_RULES'] =  $this->modules->crud_checker(27,'ALTER CANVASSING RULES');
	  
		//CSRF
		$data['csrf'] = "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>"; 
		
		//TOTAL NUMBER OF ROWS			
		$data['active_page']=1;
		$active_page=$id;
		$sql       = $this->db->query("SELECT id FROM campaign WHERE campaignType='iLike' and status!='done' $filter_AND");
		$sql       = $sql->result_array();
		$total_rec = count($sql);

		$pagenum = 1;
		$data['page_rows'] = 20; 
		$data['last'] = ceil($total_rec/$data['page_rows']);		
		$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		
		/*ITEMS*/
		// iLike Campaign Rules
		if(($_SESSION['super_admin']=='y' OR $_SESSION['countryID']==0) AND $id!='' AND $action!='page'){
			$sql = "SELECT countryID FROM campaign WHERE id = $id LIMIT 0,1";
			$sql = $this->db->query($sql);
			$c   = $sql->row();
			$filter_WHERE = "WHERE countryID = ". $c->countryID;
		}
		
		$sqlCampRules = "SELECT *  FROM `iLikeCampaignRules` $filter_WHERE ";
		$CampRules = $this->db->query($sqlCampRules);
		$CampRules = $CampRules->result_array();
		// iLike Voting Rules Campaign Rules
		$sqlVotinRules   = "SELECT * FROM  `iLikeVotingRules` $filter_WHERE ";
		$VotingRules     = $this->db->query($sqlVotinRules);
		$VotingRules       = $VotingRules->result_array();
		$cfilters = "  $filter_WHERE "; $ifAnd="";
		//Select items  
		
		$range=0; $m=''; $and='';
		foreach($CampRules as $k=>$r)
		{
			extract($r);
			$rel=($rel=='==')?"=":$rel;
			if($rel=="" and ($val=='' or $val=="0")) // all items
			   $cfilters  .= " AND ($fieldName='$fieldID')";
			else
			   $cfilters  .=" AND ((select count(id) from items where $fieldName='$fieldID' $filter_AND)  $rel $val ) ";  				
			   
			//DETERMIN IF IN RANGE
			if($rel=='<=' OR $rel=='>=')
				$range++;
		}
		
		//SET LIMIT
		if($range==2){
			foreach($CampRules as $k=>$r){
				extract($r);
				$and  .= " AND ($fieldName='$fieldID')";
			}
			$cfilters = "  $filter_WHERE $and";
			$sql = "SELECT MIN(val) AS f, MAX(val) AS t FROM iLikeCampaignRules $filter_WHERE";
			$sql = $this->db->query($sql);
			$row = $sql->row();
			$m = "LIMIT ".$row->f.",".$row->t;
		}
		
		//GET ALL ITEMS THAT HAS PRICE RANGE
		$sqlVotingRules = "SELECT iLikeVotingRules.price_rangeID as pRangeID  FROM `iLikeVotingRules` $filter_WHERE ";
		$sqlVotingRules = $this->db->query($sqlVotingRules);
		$sqlVotingRules = $sqlVotingRules->result_array();
		$price_rangeFilter = "";
		foreach($sqlVotingRules as $sqlVotingRule)
		{ extract($sqlVotingRule);
		  $price_rangeFilter .= "  items.price_rangeID = $pRangeID OR";
		}
		if($price_rangeFilter!="") $price_rangeFilter = "AND (".substr($price_rangeFilter,0,-2).")";
		
		$sqlItems = "SELECT *, items.id AS itemID, POSM_Type.typeName as POSM_TypeName 
					FROM items 
					LEFT JOIN POSM_Type   ON  POSM_Type.id   = items.POSMTypeID  
					LEFT JOIN price_range ON  price_range.id = items.price_rangeID
					$cfilters AND (items.publish = 'y') 
					AND items.id NOT IN (SELECT iLikeResultRef.itemID FROM iLikeResultRef) 
					AND items.archive = 'n'  AND DATEDIFF(CURDATE(),items.dateReleased) < ".$this->modules->itemsAge()."
					AND items.purge='n' $price_rangeFilter
					ORDER BY POSM_Type.typeName ASC, price_rangeID ASC $m";
		//STATUS LISTS
		$sql 		   = $this->db->query($sqlItems);
		$data['items'] = $sql->result_array();
		/*ITEMS*/
		
		extract($_POST);
		
	    if($action=='insert_success'){
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Campaign has been save.');
		}
		elseif($action=='delete_success'){
			$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Campaign has been delete.');
		}
		elseif($action=='update_success'){
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Campaign has been updated.');
		}
		elseif($action=='iLike_canvassing_rules_has_been_altered'){
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'iLike canvassing rules has been altered.');
		}
		elseif($action=="insert")
		{
			$this->modules->module_checker(27,'ADD');
			if($_POST==NULL){
				redirect(HTTP_PATH.'iLikeCampaign/votingCampaign', 'location', 301);
				die();
			}
			
			//CAMPAIGN
			$dbFields['campaignName']  = $campaignName;
			$dbFields['campaignType']  = 'iLike';
			$dbFields['DateAdded'] 	   = date('Y-m-d');
			$dbFields['DateFrom'] 	   = $DateFrom;
			$dbFields['DateTo'] 	   = $DateTo;
			$dbFields['status'] 	   = 'new';
			$dbFields['adminCreatorID']= $_SESSION['user_id'];
			$dbFields['countryID']	   = $_SESSION['countryID'];
			//REVOTE
			$dbFields['prevCampaignID']	   = isset($prevCampaignID) ? $prevCampaignID : 0;
			$res = $this->c3model->c3crud("insert",$table,$dbFields,'');
			
			//MAX ID CAMPAIGN
			$sql		= "SELECT max(id) as max_id FROM $table";
			$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
			$campaignID = $lastID[0]['max_id'];
			
			
			//VOTER FROM TABLE
			if(isset($lnames)) {
				foreach($lnames as $k => $val)
				{	
					$voterFields['lname']  				=  $val;
					$voterFields['fname']  				=  $fnames[$k];
					$voterFields['gender']  			=  $gender[$k];
					$voterFields['email'] 				=  $emails[$k];
					$voterFields['voterTypeID'] 		=  2 ;
					$voterFields['campaignID'] 			=  $campaignID;
					$voterFields['department'] 			=  $departments[$k];
					$voterFields['Fields001'] 			=  $_SESSION['countryID'];
					$voterFields['year_of_birth'] 		=  $years[$k];
					$voterFields['dateAdded'] 	   		= date('Y-m-d');
					
					$res = $this->c3model->c3crud("insert",'voters',$voterFields,'');
					
					//MAX VOTER ID
					$sql		= "SELECT max(id) as max_id FROM voters";
					$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
					$voterID    = $lastID[0]['max_id'];
					
					$campaignVotersXrefFields['campaignID'] = $campaignID; 
					$campaignVotersXrefFields['voterID']    = $voterID; 
					$res = $this->c3model->c3crud("insert",'campaignVotersXref',$campaignVotersXrefFields,'');
				}
			}
			
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'iLike Campaign has been save.');
			
			//LOGS
			$CI->rec_logs->w($campaignID,$campaignName,'iLike Campaign','iLike','add');
			
			redirect('iLikeCampaign/votingCampaign/insert_success', 'location', 301);
		}
		elseif($action=="deleteOneItem")
		{
			$this->modules->module_checker(27,'DELETE');
			
			$tables = array(
			  array('tbl'=>'campaign',
					'fld'=>'prevCampaignID'));
		
			if($this->modules->attr($tables,$votingCampaignID)==0)
			{
			//LOGS
			$sql = "SELECT campaignName FROM campaign WHERE id = $votingCampaignID";
			$sql = $this->db->query($sql);
			$sql = $sql->row();
			$CI->rec_logs->w($votingCampaignID,$sql->campaignName,'iLike Campaign','iLike','delete');
			
			$this->c3model->c3crud('delete',$table,'',$votingCampaignID,'');
			$this->c3model->c3crud("no-res",'','','',"DELETE FROM campaignItemsXref WHERE campaignID='$votingCampaignID'");
			
			//GET ALL VOTERS FROM campaignVoters and delete
			$campaignVoters  = $this->db->query("SELECT * FROM campaignVotersXref WHERE campaignID=$votingCampaignID");
			$campaignVoters  = $campaignVoters->result_array();
			
			foreach($campaignVoters as $cV)
			{
				extract($cV);
				$this->c3model->c3crud("no-res",'','','',"DELETE FROM voters WHERE id=$voterID");
			}
			
			$this->c3model->c3crud("no-res",'','','',"DELETE FROM campaignVotersXref WHERE campaignID='$votingCampaignID'");
			$this->c3model->c3crud("no-res",'','','',"DELETE FROM iLikeResultRef WHERE campaignID='$votingCampaignID'");
			$this->c3model->c3crud("no-res",'','','',"DELETE FROM iLike_Rules_No_Committes_Ref WHERE campaignID='$votingCampaignID'");
			$this->c3model->c3crud("no-res",'','','',"DELETE FROM iLike_Rules_Ref WHERE campaignID='$votingCampaignID'");
			$this->c3model->c3crud("no-res",'','','',"DELETE FROM iLikeCanvassingRulesXref WHERE campaignID='$votingCampaignID'"); 
			$this->c3model->c3crud("no-res",'','','',"DELETE FROM iLikeVotingRulesRef WHERE campaignID='$votingCampaignID'"); 
			$this->c3model->c3crud("no-res",'','','',"DELETE FROM votexRef WHERE campaignID='$votingCampaignID'"); 
			
			$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Campaign has been deleted.');
			
			redirect('iLikeCampaign/votingCampaign/delete_success', 'location', 301);
			}else{
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Campaign cannot be delete because it is being link in other campaign.');
			}
		
		}
		elseif($action=="edit" or $action=="alter")
		{
			$this->modules->module_checker(27,'EDIT');
			
			//ALTER STATUS
			if($action=="alter")
				$data['alter'] = TRUE;
			
			$data['rules'] 				= $this->iLikeCampaign_Items_Checker($votingCampaignID);
			$data['voting_rules']   	= $this->iLikeCampaign_Voting_Rules($votingCampaignID);
			$data['canvassing_rules']   = $this->iLikeCampaign_Canvassing_Rules($votingCampaignID);
			//CHECK IF CAMPAIGN IS ON PROGRESS OR DONE
			$sql = $this->db->query("SELECT status FROM campaign WHERE id = $votingCampaignID LIMIT 0,1");
			$row = $sql->row();
			$status = $row->status;
			
			if($status == 'on progress' OR $status == 'done' OR $status=='failed'){
				$sql = $this->db->query("SELECT *, items.id AS itemID, POSM_Type.typeName as POSM_TypeName 
										FROM items 
										LEFT JOIN POSM_Type   ON  POSM_Type.id   = items.POSMTypeID 
										LEFT JOIN price_range ON  price_range.id = items.price_rangeID
										WHERE items.id IN (SELECT itemID FROM campaignItemsXref WHERE campaignID = $votingCampaignID)
										AND items.POSMTypeID = POSM_Type.id  ORDER BY POSM_Type.typeName ASC, price_rangeID ASC");
				$data['items']  		= $sql->result_array();
				
				$data['rules'] 			= $this->iLikeCampaign_Items_Checker($votingCampaignID,$revote=TRUE);
				$data['min_committees'] = $this->min_committees($votingCampaignID,$revote=TRUE);
				$data['min_number_of_committees'] = $this->min_number_of_committees($votingCampaignID,$revote=TRUE);
				$data['voting_rules']   = $this->iLikeCampaign_Voting_Rules($votingCampaignID,$revote=TRUE);
				$data['canvassing_rules']   = $this->iLikeCampaign_Canvassing_Rules($votingCampaignID,$revote=TRUE);
			}elseif($status == 'revote'){
				$data['revote']		= true;
				$sql = $this->db->query("SELECT *, items.id AS itemID, POSM_Type.typeName as POSM_TypeName 
										FROM items
										LEFT JOIN POSM_Type   ON  POSM_Type.id   = items.POSMTypeID 
										LEFT JOIN price_range ON  price_range.id = items.price_rangeID
										WHERE items.id IN (SELECT itemID FROM campaignItemsXref WHERE campaignID = $votingCampaignID) 
										AND items.POSMTypeID = POSM_Type.id  ORDER BY POSM_Type.typeName ASC, price_rangeID ASC");
				$data['items']  = $sql->result_array();
				
				$data['rules'] 			= $this->iLikeCampaign_Items_Checker($votingCampaignID,$revote=TRUE);
				$data['voting_rules']   = $this->iLikeCampaign_Voting_Rules($votingCampaignID,$revote=TRUE);
				$data['min_committees'] = $this->min_committees($votingCampaignID,$revote=TRUE);
				$data['min_number_of_committees'] = $this->min_number_of_committees($votingCampaignID,$revote=TRUE);
				$data['canvassing_rules']   = $this->iLikeCampaign_Canvassing_Rules($votingCampaignID,$revote=TRUE);
			}
			
			$sqlCampRules = "SELECT *  FROM  `iLikeCampaignRules`  $filter_WHERE ";
			$CampRules 	  = $this->db->query($sqlCampRules);
			$CampRules 	  = $CampRules->result_array();
			// iLike Voting Rules Campaign Rules
			$sqlVotinRules = "SELECT * FROM  `iLikeVotingRules` $filter_WHERE ";
			$VotingRules   = $this->db->query($sqlVotinRules);
			$CampRules 	   = $VotingRules->result_array();
			$cfilters 	   = "  $filter_WHERE "; $ifAnd="";
			//Select items 
		    
			foreach($CampRules as $k=>$r)
			{
			    extract($r);
				if($rel=="" and ($val=='' or $val=="0")) // all items
				   $cfilters  .= " and ($fieldName='$fieldID')";
				else
                   $cfilters  .=" and ((select count(id) from items where $fieldName='$fieldID' $filter_AND)  $rel $val ) ";  				
			}
			$sqlItems = "select * from items  $cfilters "; 
			
			$data['min_committees'] = $this->min_committees($votingCampaignID);
			$data['min_number_of_committees'] = $this->min_number_of_committees($votingCampaignID);


			$data['id']   	= $votingCampaignID;
			$data['vfile']	= 'votingCampaignFORM.php';
			
			$sql = $this->db->query("SELECT voters.id as voterID, voters.lname  , voters.fname ,voters.gender , voters.email AS email_address, department, year_of_birth,  votingStatus
									FROM campaignVotersXref 
									LEFT JOIN voters  
									ON campaignVotersXref.voterID = voters.id
									WHERE campaignVotersXref.campaignID = $votingCampaignID 
									ORDER BY fname ASC");
			
			$data['admin_users'] = $sql->result_array();						 
		}
		elseif($action=="add")
		{
			$this->modules->module_checker(27,'ADD');
			$data['vfile']	= 'votingCampaignFORM.php'; 
			
			$data['min_committees'] 	= $this->min_committees($votingCampaignID);
			$data['min_number_of_committees']	= $this->min_number_of_committees($votingCampaignID);
			$data['rules'] 				= $this->iLikeCampaign_Items_Checker($votingCampaignID='',$revote='');
			$data['voting_rules']   	= $this->iLikeCampaign_Voting_Rules($votingCampaignID='',$revote='');
			$data['canvassing_rules']   = $this->iLikeCampaign_Canvassing_Rules($votingCampaignID='',$revote='');
			
			$data['admin_users'] = array();
		}
		elseif($action=="update")
		{
			$this->modules->module_checker(27,'EDIT');
			if($_POST==NULL){
				redirect(HTTP_PATH.'iLikeCampaign/votingCampaign', 'location', 301);
				die();
			}
			
            $sql       = $this->db->query("SELECT * FROM campaign WHERE id='$votingCampaignID' ");
		    $sql       = $sql->result_array();
			extract($sql[0]);
			//IF NOT ON PROGRESS
			$allowedToEdit = true;
			if($status == 'on progress')
				$allowedToEdit = false;
			if($status == 'done')
				$allowedToEdit = false;
			extract($_POST); 
			if($allowedToEdit==true AND $_SESSION['super_admin']!='y'){
			
				$dbFields['campaignName']      = $campaignName;
				$dbFields['campaignType']      = 'iLike';
				$dbFields['DateLastEdited']    = date('Y-m-d');
				$dbFields['DateFrom'] 	       = $DateFrom;
				$dbFields['DateTo'] 	       = $DateTo;
				$dbFields['status'] 	       = 'updated';
				$dbFields['adminLastEditorID'] = $_SESSION['user_id'];
				 
				 
				
				$this->c3model->c3crud("update",$table,$dbFields,$votingCampaignID);
				$campaignID = $votingCampaignID;
 	
				//VOTERS
				//DELETE ALL VOTERS
				//GET ALL VOTERS FROM campaignVoters and delete
				$campaignVoters  = $this->db->query("SELECT * FROM campaignVotersXref WHERE campaignID=$campaignID");
				$campaignVoters  = $campaignVoters->result_array();
				
				foreach($campaignVoters as $cV)
				{
					extract($cV);
					$this->c3model->c3crud("no-res",'','','',"DELETE FROM voters WHERE id=$voterID");
				}
				$this->c3model->c3crud("no-res",'','','',"DELETE FROM campaignVotersXref WHERE campaignID='$campaignID'");
 
				//VOTERS
				$names_emails = array();
				$i=0;
				$l=0;
				//VOTER FROM TABLE
				if(isset($lnames)) {
					foreach($lnames as $k => $val)
					{	
						$voterFields['lname']  				=  $val;
						$voterFields['fname']  				=  $fnames[$k];
						$voterFields['gender']  			=  $gender[$k];
						$voterFields['email'] 				=  $emails[$k];
						$voterFields['voterTypeID'] 		=  2 ;
						$voterFields['campaignID'] 			=  $campaignID;
						$voterFields['department'] 			=  $departments[$k];
						$voterFields['year_of_birth'] 		=  $years[$k];
						$voterFields['Fields001'] 			=  $_SESSION['countryID'];
						//es = $this->c3model->c3crud("insert",'voters',$voterFields,'');
						$voterFields['dateAdded'] 	   			= date('Y-m-d');
						$res = $this->c3model->c3crud("insert",'voters',$voterFields,'');
	 
						//MAX VOTER ID
						$sql		= "SELECT max(id) as max_id FROM voters";
						$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
						$voterID    = $lastID[0]['max_id'];
						
						$campaignVotersXrefFields['campaignID'] = $campaignID; 
						$campaignVotersXrefFields['voterID']    = $voterID; 
						$res = $this->c3model->c3crud("insert",'campaignVotersXref',$campaignVotersXrefFields,'');
					}
					 
				}
			}
			
			//LOGS
			$CI->rec_logs->w($campaignID,$campaignName,'iLike Campaign','iLike','edit');
			redirect('iLikeCampaign/votingCampaign/update_success', 'location', 301);
		}
		elseif($action=="page")
		{
			$this->modules->module_checker(27,'REVIEW');
			$pagenum 			 = $votingCampaignID;
			$data['active_page'] = $active_page; 
			$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		}
		elseif($action=="alter_items_success")
		{
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Campaign result has been altered.'); 
		}
		elseif($action=="alter_div_items_success")
		{
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Division Marketing iLike Items has been save.'); 
		}

		$filter_WHERE = $this->modules->iLike_Campaign_country();
     	$sql = $this->db->query("SELECT *,campaign.id AS iLikeCampaignID FROM $table  
								LEFT JOIN admin_users 
								ON campaign.adminCreatorID = admin_users.id
								WHERE campaignType='iLike' $filter_WHERE ORDER BY iLikeCampaignID  DESC $max");
								
		$data['campaigns'] = $sql->result_array();
		
		if($action == 'add' OR $action == 'edit' OR $action == 'alter')
			$viewer = 'iLikeCampaignHeader';
		else
			$viewer = 'innerPages';
		

		if($this->modules->access_checker()==TRUE)
	    {
			$this->load->view($viewer,$data); 
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
	
	function Division_Marketing_items($cID='')
	{
	$this->modules->module_checker(27,'ALTER RESULTS');
	
	$data['EDIT'] 	=  $this->modules->crud_checker(18,'EDIT');
	$data['ALTER_RESULTS'] =  $this->modules->crud_checker(27,'ALTER RESULTS');
	//$data['cID'] = $cID;
	
	$sql = "SELECT items.id as itemID, (SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, itemName,
			POSM_Type.typeName as POSM_TypeName, level_name, extra_label
			FROM items
			LEFT JOIN POSM_Type   ON items.POSMTypeID = POSM_Type.id 
			INNER JOIN price_range ON items.price_rangeID = price_range.id
			WHERE  items.countryID = 0 
			AND items.id NOT IN (SELECT iWantResultRef.itemID FROM iWantResultRef) 
			AND items.id NOT IN (".$this->modules->generateItemsForiLike().")
			AND items.purge='n'
			ORDER BY POSM_Type.id DESC, price_range.id ASC";

	 $report    = $this->db->query($sql);  
	 $rep       = $report->result_array(); 
	
		
		$sqlSTr="SELECT items.id as itemID, (SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, itemName,
				POSM_Type.typeName as POSM_TypeName, level_name, extra_label
				FROM items
				LEFT JOIN POSM_Type ON items.POSMTypeID = POSM_Type.id 
				INNER JOIN price_range ON items.price_rangeID = price_range.id
				WHERE  items.countryID = 0 AND items.id IN (SELECT itemID FROM iLikeResultRef WHERE campaignID = 0) 
				AND items.id NOT IN (SELECT iWantResultRef.itemID FROM iWantResultRef)
				ORDER BY POSM_Type.id DESC, price_range.id ASC";
		$topItems = $this->db->query($sqlSTr);
		$topItems = $topItems->result_array();
		$type =""; 	 
		$arr=array();
		foreach($topItems as $ti)
		{	extract($ti);
			$arr[] = array('level_name'		=>$level_name,
						   'extra_label'	=>$extra_label,
						   'itemID'			=>$itemID,
						   'item_image'		=>$item_image,
						   'itemName'		=>$this->cutStr($itemName),
						   'POSM_TypeName'	=>substr($POSM_TypeName,0,-5));
		}
		
		$_SESSION['Division_Marketing_items']=$arr;
		
		//print_r($_SESSION['iLike_items']);
		
		$data['topItems'] = $topItems;
		
		
		$data['vfile']		= 'Division_Marketing_items.php';
		$data['title']		= 'iLike Report';
		$data['rep']		= $rep;
		
		//BREAD CRUMBS
	    $data['breadCrumbs']	 = "<a href='".HTTP_PATH."iWantCampaign/iWant/'> iWant Campaign </a>";
		$data['breadCrumbs']	.= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
		$data['breadCrumbs']	.= '<a href='.HTTP_PATH.'iLikeCampaign/Division_Marketing_items> Division Marketing items </a>';
		
		$data['jUi'] = true; 
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
	
	function add_div_item($iID)
	{
		$sqlSTr="SELECT items.id as itemID, (SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, itemName,
				POSM_Type.typeName as POSM_TypeName, level_name, extra_label
				FROM items
				LEFT JOIN POSM_Type   ON items.POSMTypeID = POSM_Type.id 
				LEFT JOIN price_range ON items.price_rangeID = price_range.id
				WHERE  items.id = $iID ORDER BY items.id DESC";
		$topItems = $this->db->query($sqlSTr);
		$topItems = $topItems->result_array();
		
		//CHECK IF ITEM EXIST
		$STATUS="ok";
		foreach($_SESSION['Division_Marketing_items'] as $i){	
			extract($i);
			if($itemID==$iID) $STATUS = "exist";
		}
		

		if($STATUS=='ok'){
			foreach($topItems as $ti){	
			 extract($ti);
			 $arr = array('level_name'	  =>$level_name,
						  'extra_label'	=>$extra_label,
						  'itemID'		  =>$iID,
						  'item_image'	  =>$item_image,
						  'itemName'	  =>$this->cutStr($itemName),
						  'POSM_TypeName' =>substr($POSM_TypeName,0,-5));
			}
			array_unshift($_SESSION['Division_Marketing_items'],$arr);
			//$_SESSION['Division_Marketing_items'][] = $arr;
			echo $STATUS;
		}else{
			echo $STATUS;
		}
	}
	
	function view_div_items()
	{   $ctr=0;
		foreach($_SESSION['Division_Marketing_items'] as $d){
			extract($d);
			
			$img_path = HTTP_PATH."img/small/$item_image";
			$itemPreviewLink = HTTP_PATH."gallery/itemInfo2/$itemID";
			$w = $this->w($item_image);
			$del_shortCut="<img onclick=\"delete_item($itemID)\" src='".HTTP_PATH."img/delete-item.png' style='margin-left:8px;cursor:pointer;'>";
			$title_css="";
			
			$ctr++;
			echo "<div style='width:120px;height:173px;margin: 10px 5px 24px 10px;;background:white;' class='fl'>
				<p style='font-size:12px;text-align:center;padding-bottom:3px;margin-bottom:-1px;background-color:#757575;color:white;'> 
					<b style='color:#330404;'>$ctr. </b><b>  $POSM_TypeName </b>  $del_shortCut
			   </p>
			   <p style='font-size:12px;text-align:center;padding-bottom:3px;margin-bottom:-1px;background-color:#999999;color:white;'> 
				$extra_label 
			   </p>
			   <input type='hidden' name='items[]' value='$itemID'>
				<div style='border: 1px solid #ccc;text-align:center;padding:5px;height:110px;overflow: hidden;background-color: white;'>
					<a href='$itemPreviewLink' target='_blank' class='itemLink'>
						<table>
						 <tr>
							<td class='gal-Icon-Container'><img class='gal-Icon-Img' src='$img_path' style='width:100%;margin-top:-65px;'></td>
						 </tr>	
						</table> 
					</a>
				</div>
				<p style='font-size:10px;text-align:center;border: 1px solid #ccc;padding-bottom:3px;background:white;color: #555;margin-bottom:10px;height: 14px;' label='$itemName'> 
					<b style='padding-right:10px;'> ". $itemName ." </b>  
				</p>
				 <br/>
			 </div>";
		}
	}
	
	function delete_div_item($iID)
	{
		$sqlSTr="SELECT items.id as itemID, (SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, itemName,
				POSM_Type.typeName as POSM_TypeName, level_name, extra_label
				FROM items
				LEFT JOIN POSM_Type   ON items.POSMTypeID    = POSM_Type.id 
				LEFT JOIN price_range ON items.price_rangeID = price_range.id
				WHERE  items.id = $iID ORDER BY items.id DESC";
		$topItems = $this->db->query($sqlSTr);
		$topItems = $topItems->result_array();
		
		foreach($topItems as $ti){	
		 extract($ti);
		 $arr = array('level_name'	  =>$level_name,
					  'extra_label'	  =>$extra_label,
					  'itemID'		  =>$iID,
					  'item_image'	  =>$item_image,
					  'itemName'	  =>$this->cutStr($itemName),
					  'POSM_TypeName' =>substr($POSM_TypeName,0,-5));
		}
		
		$key = array_search($arr,$_SESSION['Division_Marketing_items']);		
		unset($_SESSION['Division_Marketing_items'][$key]);
		
		echo "ok";
	}
	
	function save_new_div_items($cID='')
	{
		extract($_POST);
		/*DELETE PREVIOUS ITEMS*/
		$this->db->query("DELETE FROM iLikeResultRef WHERE campaignID=0");
		
		/*PREPARE NEW ITEMS*/
		foreach($_SESSION['Division_Marketing_items'] as $it)
		{
			extract($it);
			$data['campaignID']  = 0;
			$data['itemID']  	 = $itemID;
			$data['totvote'] 	 = 0;
			$data['alt']	 	 = 'y';
			//print_r($data);
			$res = $this->c3model->c3crud("insert",'iLikeResultRef',$data,'');
		}
		
		/*INSERT INTO LOGS*/
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		$CI->rec_logs->w(0,'Division Marketing Items','iLike Campaign','iLike','items-altered');
		
		unset($_SESSION['Division_Marketing_items']);		
		redirect('iWantCampaign/iWant/alter_div_items_success', 'location', 301);
	}
	
	function alter_iLike_results($cID='')
	{
		$data['EDIT'] 	=  $this->modules->crud_checker(18,'EDIT');
		$data['ALTER_RESULTS'] =  $this->modules->crud_checker(27,'ALTER RESULTS');
		$data['cID'] = $cID;
		$sql = "SELECT itemID, itm.itemName, itm.itemCode as iCode, campaignID,(SELECT COUNT( id ) FROM votexRef AS vref WHERE vref.itemID = itemREF.itemID AND vote =  'yes' and vref.campaignID =$cID ) AS voteTot,
				(select typeName from POSM_Type as pt where pt.id=i.POSMTypeID) as ptype, extra_label,
				(SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = i.id) as item_image
				FROM  `campaignItemsXref` AS itemREF 
				LEFT JOIN items AS i   ON itemREF.itemID = i.id  
				left join items as itm ON itemREF.itemID = itm.id 
				LEFT JOIN price_range  ON price_range.id = itm.price_rangeID
				where itemREF.campaignID=$cID
				AND itemREF.itemID NOT IN (SELECT itemID FROM iLikeResultRef WHERE campaignID = $cID)
				ORDER BY ptype ASC, price_range.id ASC, voteTot DESC";

	 $report    = $this->db->query($sql);  
	 $rep       = $report->result_array(); 
 
	 
	$sql       = "select *,full_name, c.id as cID from campaign as c inner join admin_users as u on c.adminCreatorID=u.id where c.id='$cID'   ";
	$header    = $this->db->query($sql);  
	$header    = $header->result_array(); 
	 
		
		$sqlSTr="SELECT items.id as iID, (SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, itemName,
				(SELECT totvote FROM iLikeResultRef WHERE itemID = iID AND campaignID = $cID) AS voteTot,
				POSM_Type.typeName as POSM_TypeName , extra_label,
				(SELECT alt FROM iLikeResultRef WHERE iLikeResultRef.campaignID = $cID AND iLikeResultRef.itemID = items.id) as alt
				FROM items
				LEFT JOIN POSM_Type    ON items.POSMTypeID = POSM_Type.id 
				LEFT JOIN price_range  ON price_range.id   = items.price_rangeID
				WHERE  items.id IN (SELECT itemID FROM iLikeResultRef WHERE campaignID = $cID) ORDER BY POSM_TypeName ASC, price_range.id ASC, voteTot DESC";
		$topItems = $this->db->query($sqlSTr);
		$topItems = $topItems->result_array();
		$type =""; 	 
		$arr="";
		
		foreach($topItems as $ti)
		{	extract($ti);
			$type = ($alt=='n') ? 'no':'yes';
			$arr[] = array('alter'=>$type,
						   'itemID'=>$iID,
						   'extra_label'=>$extra_label,
						   'itemID'=>$iID,
						   'item_image'=>$item_image,
						   'itemName'=>$this->cutStr($itemName),
						   'voteTot'=>$voteTot,
						   'POSM_TypeName'=>substr($POSM_TypeName,0,-5)
						   );
		}
		
		$_SESSION['iLike_items']=$arr;
		
		//print_r($_SESSION['iLike_items']);
		
		$data['topItems'] = $topItems;
		
		
		$data['vfile']		= 'alter_iLike_results.php';
		$data['title']		= 'iLike Report';
		$data['rep']		= $rep;
		$data['repHeader']	= $header;
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
		$data['breadCrumbs']		= '<a href='.HTTP_PATH.'iLikeCampaign/votingCampaign> iLike Campaign </a>';
		
	
		$data['jUi'] = true; 
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
	
	function save_new_iLike_items($cID='')
	{
		extract($_POST);
		/*DELETE PREVIOUS ITEMS*/
		$this->db->query("DELETE FROM iLikeResultRef WHERE campaignID=$cID");
		
		//print_r($_SESSION['iLike_items']);
		
		/*PREPARE NEW ITEMS*/
		foreach($_SESSION['iLike_items'] as $it)
		{
			extract($it);
			$data['campaignID']  = $cID;
			$data['itemID']  	 = $itemID;
			$data['totvote'] 	 = $voteTot;
			$data['alt']	 	 = ($alter=='no') ? 'n':'y';
			//print_r($data);
			$res = $this->c3model->c3crud("insert",'iLikeResultRef',$data,'');
		}
		
		//die();
		
		/*UPDATE CAMPAIGN STAT*/
		$this->db->query("UPDATE campaign SET status='done', remarks='iLike result has been altered' WHERE id=$cID");
		
		/*INSERT INTO LOGS*/
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		$CI->rec_logs->w($cID,$campaignName,'iLike Campaign','iLike','result-altered');
		
		unset($_SESSION['iLike_items']);		
		redirect('iLikeCampaign/votingCampaign/alter_items_success', 'location', 301);
	}
	
	function add_item($iID,$cID)
	{
		$sqlSTr="SELECT items.id as iID, (SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, itemName,
				(SELECT count(vote) FROM votexRef WHERE itemID = $iID AND campaignID = $cID AND vote='yes') AS voteTot,
				POSM_Type.typeName as POSM_TypeName , extra_label
				FROM items
				LEFT JOIN POSM_Type    ON items.POSMTypeID = POSM_Type.id 
				LEFT JOIN price_range  ON price_range.id   = items.price_rangeID
				WHERE  items.id = $iID LIMIT 0,1";
		$topItems = $this->db->query($sqlSTr);
		$topItems = $topItems->result_array();
		
		//CHECK IF ITEM EXIST
		$STATUS="ok";
		foreach($_SESSION['iLike_items'] as $i){	
			extract($i);
			if($itemID==$iID) $STATUS = "exist";
		}
		

		if($STATUS=='ok'){
			foreach($topItems as $ti){	
			 extract($ti);
			 $arr = array('alter'=>'yes',
			 'itemID'=>$iID,
			 'extra_label'=>$extra_label,
			 'item_image'=>$item_image,
			 'itemName'=>$this->cutStr($itemName),
			 'voteTot'=>$voteTot,
			 'POSM_TypeName'=>substr($POSM_TypeName,0,-5));
			}
			$_SESSION['iLike_items'][] = $arr;
			echo $STATUS;
		}else{
			echo $STATUS;
		}
	}
	
	function delete_item($iID,$cID,$alt)
	{
		$sqlSTr="SELECT items.id as iID, (SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, itemName,
				(SELECT count(vote) FROM votexRef WHERE itemID = $iID AND campaignID = $cID AND vote='yes') AS voteTot,
				POSM_Type.typeName as POSM_TypeName, extra_label 
				FROM items
				LEFT JOIN POSM_Type ON items.POSMTypeID = POSM_Type.id 
				LEFT JOIN price_range  ON price_range.id   = items.price_rangeID
				WHERE  items.id = $iID LIMIT 0,1";
		$topItems = $this->db->query($sqlSTr);
		$topItems = $topItems->result_array();
		
		foreach($topItems as $ti){	
		 extract($ti);
		 $arr = array('alter'=>$alt,
					  'itemID'=>$iID,
					  'extra_label'=>$extra_label,
					  'item_image'=>$item_image,
					  'itemName'=>$this->cutStr($itemName),
					  'voteTot'=>$voteTot,
					  'POSM_TypeName'=>substr($POSM_TypeName,0,-5));
		}
		
		$key = array_search($arr,$_SESSION['iLike_items']);		
		unset($_SESSION['iLike_items'][$key]);
		
		echo "ok";
	}
	
	function view_items($cID)
	{
	$EDIT 	=  $this->modules->crud_checker(18,'EDIT');
	
		foreach($_SESSION['iLike_items'] as $d){
			extract($d);
			if($EDIT)
				$edit_shortCut="<a href='".HTTP_PATH."/itemDatabase/items/edit/$itemID' target='_blank'> <img src='".HTTP_PATH."img/edit-item.png' style='margin-left:8px;cursor:pointer;'></a>";
			
			$img_path = HTTP_PATH."img/small/$item_image";
			$itemPreviewLink = HTTP_PATH."gallery/itemInfo2/$itemID";
			$w = $this->w($item_image);
			$del_shortCut="<img onclick=\"delete_item($itemID,$cID,'$alter')\" src='".HTTP_PATH."img/delete-item.png' style='margin-left:8px;cursor:pointer;'>";
			$title_css="";
			if($alter=="no"){
				$color = "#4E4545";
			}else{
				$title_css="style='margin-left: 23px;'";
				$color = "#999999";
			}	
		echo "
			  <div style='width:120px;height:180px;margin: 10px 5px 20px 10px;;background:white;' class='fl'>
				<p style='font-size:12px;text-align:center;border: 1px solid #ccc;padding-bottom:3px;margin-bottom:-1px;background-color:$color;color:white;'> 
					<b $title_css> ". $POSM_TypeName ."</b> $del_shortCut
			   </p>
			    <p style='font-size:12px;text-align:center;border: 1px solid #ccc;margin-bottom:-1px;background-color:#bbbbbb;color:white;'> 
					<label style='margin-left:0px;font-size:11px;'> $extra_label </label> 
				  </p>
			   <input type='hidden' name='items[]' value='$itemID'>
				<div style='border: 1px solid #ccc;text-align:center;padding:5px;height:100px;overflow: hidden;'>
					<a href='$itemPreviewLink' target='_blank' class='itemLink'>
						<table>
						 <tr>
							<td class='gal-Icon-Container'><img class='gal-Icon-Img' src='$img_path' style='$w;margin-top:-65px;'></td>
						 </tr>	
						</table> 
					</a>
				</div>
				<p style='font-size:10px;text-align:center;border: 1px solid #ccc;padding-bottom:3px;background:white;color: #555;margin-bottom:10px;' label='$itemName'> 
					<b> ". $itemName ."</b><br/>
					<b>Likes: $voteTot</b> $edit_shortCut
				</p>
				 <br/>
			 </div>";
		}
	}
	
	function w($img)
	{
		$w='';
		$HTTP_PATH = getcwd()."/img/galleryImg/$img";
		list($width, $height, $type, $attr) = getimagesize("$HTTP_PATH");
		if($width>$height)
			return $w='width:100%';
		else
			return $w;
	}
	
	function cutStr($itemName=''){
		if(strlen($itemName)>=15)
			return substr($itemName,0,15)."..";
		else	
			return $itemName; 
	}
	
	
	/*GENERATE EMAIL TD*/
	function generateEmailTD($fname,$lname,$gender,$email,$department,$year,$emailCtr)
	{
	   $c = (($emailCtr)%2) != 0 ? "" : "style='background:#f9ebeb;width:100%;height:38px;'"; 
	   $fname = str_replace('%20',' ',$fname);
	   $department = str_replace('%20',' ',$department);
	   $fname = str_replace('%C3%91','Ñ',$fname);
	   $fname = str_replace('%C3%B1','ñ',$fname);
	   $lname = str_replace('%20',' ',$lname);
	   $lname = str_replace('%C3%91','Ñ',$lname);
	   $lname = str_replace('%C3%B1','ñ',$lname);
	   
	   echo "<div id='emailCtr".$emailCtr."' $c class='emailClass' style='width:100%'>
			 <table>
				<tr>
					<td style='padding: 0 0 0 15px;'> 
					   <input type='hidden' name='voterTypes[]' 	value='2'>
					   <input style='width:90%;margin-bottom: 0;' type='text' value='$fname' 	 name='fnames[]'       readonly='readonly' > 
					</td>
					<td style='padding:3px;'><input type='text' style='width:114%;margin-bottom: 0;' value='$lname' name='lnames[]'  readonly='readonly'></td>
					<td style='padding:3px;text-align: right;width: 117px;'>
						<input type='text' style='width:59%;margin-bottom: 0;' value='$gender' 	    	name='gender[]'       readonly='readonly'  >
					</td>
					<td style='padding:3px;width: 145px;'><input type='text' style='width:92%;margin-bottom: 0;' value='$department'  	name='departments[]' readonly='readonly'> </td> 
					<td style='padding:3px;width: 253px;'><input type='text' style='width:116%;margin-bottom: 0;' value='$email'  		    class='emails'   name='emails[]' readonly='readonly'> </td> 
					<td style='padding:3px;text-align: right;'><input type='text' style='width:51%;margin-bottom: 0;' value='$year'  			name='years[]' 		 readonly='readonly' > </td> 
					<td style='padding:3px;text-align: center;'>
							<img onclick='removeEmail(\"emailCtr".$emailCtr."\")' style='margin: 0 10px 0 36px;padding-top: 4px;' src='".HTTP_PATH."img/delete.png' title='delete' class='fl'> 
					</td>
				</tr>
			</table>
			</div>";
	}
	/*GENERATE EMAIL TD*/
	
	/*SAVE CSV FILES*/
	function insertCSVData($file,$campaignID,$csvID)
	{
		$file = fopen($file,'r');
		while(!feof($file))
		{
			//print_r(fgetcsv($file));
			$data = fgetcsv($file);
			
			$name  = $data[0];
			$email = $data[1];
			
			$voterFields['name']  = $name;
			$voterFields['email'] = $email;
			$voterFields['CSV']   = $csvID;
			
			if($voterFields!=NULL){
			$res = $this->c3model->c3crud("insert",'voters',$voterFields,'');
			
			//MAX VOTER ID
			$sql		= "SELECT max(id) as max_id FROM voters";
			$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
			$voterID    = $lastID[0]['max_id'];
			
			$campaignVotersXrefFields['campaignID'] = $campaignID; 
			$campaignVotersXrefFields['voterID']    = $voterID; 
			$res = $this->c3model->c3crud("insert",'campaignVotersXref',$campaignVotersXrefFields,'');
			}
		}
		fclose($file);
	}
	/*SAVE CSV FILES*/
	
	function potentialItems($field_Name='',$field_ID='',$price_rangeID='')
	{
		$filter_AND   = $this->modules->country2();
		$filter_WHERE = $this->modules->country();
		$NOT_IN = "AND items.id NOT IN (SELECT iLikeResultRef.itemID FROM iLikeResultRef)";
		//ITEMS
		$sqlCampRules = "SELECT *  FROM `iLikeCampaignRules` $filter_WHERE ";
		$CampRules = $this->db->query($sqlCampRules);
		$CampRules = $CampRules->result_array();
		$cfilters = "  $filter_WHERE "; 
		
		$range=0; $m=''; $and='';
		foreach($CampRules as $k=>$r)
		{
			extract($r);
			$rel=($rel=='==')?"=":$rel;
			if($rel=="" and ($val=='' or $val=="0")) // all items
			   $cfilters  .= " AND ($fieldName=$fieldID)";
			else
			   $cfilters  .=" AND ((select count(id) from items where $fieldName=$fieldID $filter_AND)  $rel $val ) ";  				
			   
			//DETERMIN IF IN RANGE
			if($rel=='<=' OR $rel=='>=')
				$range++;
		}
			
		//SET LIMIT
		if($range==2){
			foreach($CampRules as $k=>$r){
				extract($r);
				$and  = " AND ($fieldName=$fieldID)";
			}
			$cfilters = "  $filter_WHERE $and";
			$sql = "SELECT MIN(val) AS f, MAX(val) AS t FROM iLikeCampaignRules $filter_WHERE";
			$sql = $this->db->query($sql);
			$row = $sql->row();
			$m = "LIMIT ".$row->f.",".$row->t;
		}
		
		$itemDB = "SELECT count(items.id) as tot_items
				   FROM items  $cfilters AND(items.$field_Name = $field_ID) AND items.publish = 'y' 
				   AND items.archive = 'n'  AND DATEDIFF(CURDATE(),items.dateReleased) < ".$this->modules->itemsAge()."
				   AND items.purge = 'n'
				   AND items.price_rangeID = $price_rangeID
				   $NOT_IN LIMIT 0,1";
		
		$s 	  = $this->db->query($itemDB);
		$row  = $s->row();
		return $row->tot_items;
	}
	
	function campaignVoters($campaignID="")
	{
	$sql = $this->db->query("SELECT COUNT(id) as ctr FROM voters WHERE campaignID = $campaignID LIMIT 0,1");
	$row = $sql->row();
	return $row->ctr;
	}
	
	/*iLike Campaign*/
	function publishCampaign($campaignID)
	{
		if($_POST==NULL){
			redirect(HTTP_PATH.'iLikeCampaign/votingCampaign', 'location', 301);
			die();
		}
	
		$currentDate = date("Y-m-d");
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		
		$table = "campaign";
		$filter_WHERE = $this->modules->country();
		$filter_AND = $this->modules->country2();
		
		extract($_POST);
		/*SAVE OR READ CAMPAIGN*/
		if($campaignID==0 OR isset($prevCampaignID)){
			//CAMPAIGN
			$dbFields['campaignName']  = $campaignName;
			$dbFields['campaignType']  = 'iLike';
			$dbFields['DateAdded'] 	   = date('Y-m-d');
			$dbFields['DateFrom'] 	   = $DateFrom;
			$dbFields['DateTo'] 	   = $DateTo;
			$dbFields['status'] 	   = 'new';
			$dbFields['adminCreatorID']= $_SESSION['user_id'];
			$dbFields['countryID']	   = $_SESSION['countryID'];
			$dbFields['prevCampaignID']= isset($prevCampaignID) ? $prevCampaignID : 0;
			$res 					   = $this->c3model->c3crud("insert",$table,$dbFields,'');
			
			//MAX ID CAMPAIGN
			$sql		= "SELECT max(id) as max_id FROM $table";
			$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
			$campaignID = $lastID[0]['max_id'];
			
			//LOGS
			if(isset($prevCampaignID)) { $stat = 'recreate'; }else { $stat = 'add'; }
			$CI->rec_logs->w($campaignID,$campaignName,'iLike Campaign','iLike',$stat);
		}else{
			//GET ALL VOTERS FROM campaignVoters and delete
			$campaignVoters  = $this->db->query("SELECT * FROM campaignVotersXref WHERE campaignID=$campaignID");
			$campaignVoters  = $campaignVoters->result_array();
			$dbFields['DateFrom'] 	   	  = $DateFrom;
			$dbFields['DateTo'] 	   	  = $DateTo;
			$dbFields['adminLastEditorID']= $_SESSION['user_id'];
			
			foreach($campaignVoters as $cV)
			{
				extract($cV);
				$this->c3model->c3crud("no-res",'','','',"DELETE FROM voters WHERE id=$voterID");
			}
			$this->c3model->c3crud("no-res",'','','',"DELETE FROM campaignVotersXref WHERE campaignID='$campaignID'");
		}
		/*SAVE OR READ CAMPAIGN*/
		
		/*SAVE ALL THE NOMINESS*/
		if(isset($lnames)) {
			foreach($lnames as $k => $val)
			{	
				$voterFields['lname']  				=  $val;
				$voterFields['fname']  				=  $fnames[$k];
				$voterFields['gender']  			=  $gender[$k];
				$voterFields['email'] 				=  $emails[$k];
				$voterFields['voterTypeID'] 		=  2 ;
				$voterFields['campaignID'] 			=  $campaignID;
				$voterFields['department'] 			=  $departments[$k];
				$voterFields['year_of_birth'] 		=  $years[$k];
				$voterFields['Fields001'] 			=  $_SESSION['countryID'];
				$voterFields['dateAdded'] 	   		=  date('Y-m-d');
				
				$res = $this->c3model->c3crud("insert",'voters',$voterFields,'');
				
				//MAX VOTER ID
				$sql		= "SELECT max(id) as max_id FROM voters";
				$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
				$voterID    = $lastID[0]['max_id'];
				
				$campaignVotersXrefFields['campaignID'] = $campaignID; 
				$campaignVotersXrefFields['voterID']    = $voterID; 
				$res = $this->c3model->c3crud("insert",'campaignVotersXref',$campaignVotersXrefFields,'');
			}
		}
		/*SAVE ALL THE NOMINESS*/
			
		/*ITEM RULES*/
		// iLike Campaign Rules
		$sqlCampRules = "SELECT *  FROM `iLikeCampaignRules` $filter_WHERE ";
		$CampRules    = $this->db->query($sqlCampRules);
		$CampRules    = $CampRules->result_array();
		
		// iLike Voting Rules 
		$sqlVotinRules   = "SELECT * FROM  `iLikeVotingRules` $filter_WHERE ";
		$VotingRules     = $this->db->query($sqlVotinRules);
		$VotingRules     = $VotingRules->result_array();
		foreach($VotingRules as $vr)
		{
			extract($vr);
			//PERCENTAGE
			$actual_val = $val;
			/*if(strpos($val,".")==TRUE){
			 $val = $val * $this->potentialItems($fieldName,$fieldID,$price_rangeID);
			 $val = round($val);
			}*/
			
			$votingRulesFields['price_rangeID']= $price_rangeID;
			//SELECT LABEL IN PRICE RANGE REF
			$sql = $this->db->query("SELECT extra_label FROM price_range WHERE id = $price_rangeID LIMIT 0,1");
			$sql = $sql->row();
			$votingRulesFields['label']		   = $sql->extra_label;
			
			$votingRulesFields['fieldName']    = $fieldName;
			$votingRulesFields['fieldID'] 	   = $fieldID;
			$votingRulesFields['rel'] 	       = $rel;
			$votingRulesFields['val'] 	   	   = $val;
			//$votingRulesFields['val'] 		   = $actual_val;
			$votingRulesFields['cond1']   	  			=  $cond1;
			$votingRulesFields['min_val']   	  		=  $min_val;
			$votingRulesFields['logical_operator']   	=  $logical_operator;
			$votingRulesFields['cond2']   				=  $cond2;
			$votingRulesFields['max_val']   			=  $max_val;
			$votingRulesFields['countryID']    = $countryID;
			$votingRulesFields['campaignID']   = $campaignID;
			$votingRulesFields['dateAdded']    = date('Y-m-d');
			$res = $this->c3model->c3crud("insert",'iLikeVotingRulesRef',$votingRulesFields,'');
		}
		
		//Select items  
		$cfilters 		 = "  $filter_WHERE "; $ifAnd="";
		$range=0; $m=''; $and='';
		foreach($CampRules as $k=>$r)
		{
			extract($r);
			$rel=($rel=='==')?"=":$rel;
			if($rel=="" and ($val=='' or $val=="0")) // all items
			   $cfilters  .= " AND ($fieldName='$fieldID')";
			else
			   $cfilters  .= " AND ((select count(id) from items where $fieldName='$fieldID' $filter_AND)  $rel $val ) ";  				
			   
			//DETERMIN IF IN RANGE
			if($rel=='<=' OR $rel=='>=')
				$range++;
		}
		
		//SET LIMIT
		if($range==2){
			foreach($CampRules as $k=>$r){
				extract($r);
				$and  .= " AND ($fieldName='$fieldID')";
			}
			$cfilters = "  $filter_WHERE $and";
			$sql = "SELECT MIN(val) AS f, MAX(val) AS t FROM iLikeCampaignRules $filter_WHERE";
			$sql = $this->db->query($sql);
			$row = $sql->row();
			$m = "LIMIT ".$row->f.",".$row->t;
		}
		/*ITEM RULES*/
		
		//GET ALL ITEMS THAT HAS PRICE RANGE
		$sqlVotingRules = "SELECT iLikeVotingRules.price_rangeID as pRangeID  FROM `iLikeVotingRules` $filter_WHERE ";
		$sqlVotingRules = $this->db->query($sqlVotingRules);
		$sqlVotingRules = $sqlVotingRules->result_array();
		$price_rangeFilter = "";
		foreach($sqlVotingRules as $sqlVotingRule)
		{ extract($sqlVotingRule);
		  $price_rangeFilter .= "  items.price_rangeID = $pRangeID OR";
		}
		if($price_rangeFilter!="") $price_rangeFilter = "AND (".substr($price_rangeFilter,0,-2).")";
		
		/*CHECK IF REVOTE*/
		if(isset($prevCampaignID)){
		 $itemDB = "SELECT  itemID FROM campaignItemsXref 
					WHERE campaignID = $prevCampaignID";
		 
		 $iLike_Rules_Ref 				   = $this->iLikeCampaign_Items_Checker($campaignID,$revote=TRUE);
		 $committee_Fields['num_commitee'] = $this->min_committees($campaignID,$revote=TRUE);
		}else{
		//ITEMS				   
		$itemDB = "SELECT *, items.id AS itemID, POSM_Type.typeName as POSM_TypeName 
					FROM items LEFT JOIN POSM_Type ON  POSM_Type.id = items.POSMTypeID  $cfilters AND items.publish = 'y' 
					AND items.id NOT IN (SELECT iLikeResultRef.itemID FROM iLikeResultRef)
					AND items.archive = 'n'  AND DATEDIFF(CURDATE(),items.dateReleased) < ".$this->modules->itemsAge()."
					AND items.purge='n' $price_rangeFilter
					ORDER BY itemID DESC $m  "; 
				   
		$iLike_Rules_Ref 				  = $this->iLikeCampaign_Items_Checker($campaignID);
		$committee_Fields['num_commitee'] = $this->min_committees($campaignID);
		}
		
		/*SAVE ITEMS */
		$sql 	= $this->db->query($itemDB);
		$items 	= $sql->result_array();	
		
		//echo count($items);
		//print_r($items);
		
		foreach($items as $itm)
		{
			extract($itm);
			$refFields['campaignID'] = $campaignID;
			$refFields['itemID']     = $itemID;
			$res = $this->c3model->c3crud("insert",'campaignItemsXref',$refFields,'');
			
			//RECORD LOG IN
			$SQL = $this->db->query("SELECT id, itemCode, itemName FROM items WHERE id= $itemID");
			$row = $SQL->row();
			$CI->rec_logs->w($row->id, $row->itemName, "$campaignName",'Campaign Items','add', $row->itemCode);
		}
		/*SAVE ITEMS*/
		
		
		/*iLIKE CAMPAIGN RULES REF*/
		foreach($iLike_Rules_Ref as $iLRR)
		{ extract($iLRR);
		  $field['campaignID']  	  = $campaignID;
		  $field['table']  	   	  	  = $table;
		  $field['fieldName']  	  	  = $fieldName;
		  $field['fieldID']  	      = $fieldID;
		  $field['fieldValue']  	  = $fieldValue;
		  $field['rel']  	   		  = $rel;
		  $field['val']  	   		  = $val;
		  $field['status']  	   	  = $status;
		  $field['current_num_items'] = $current_num_items;
		  
		  $res = $this->c3model->c3crud("insert","iLike_Rules_Ref",$field,'');
		}
		/*iLIKE CAMPAIGN RULES REF*/
		
		$sql 	= $this->db->query("select * from iLikeCanvassingRules $filter_WHERE");
		$iLikeCanvssignRef 	= $sql->result_array();	
		
		/*iLIKE CAMPAIGN Canvassing REF*/
		foreach($iLikeCanvssignRef as $iLRR)
		{ extract($iLRR);
		  //PERCENTAGE
		  $actual_val = $val;
		  if(strpos($val,".")==TRUE){
			$val = $val * $this->campaignVoters($campaignID);
			$val = round($val);
		  }
		
		  $field3['price_rangeID']    =  $price_rangeID;
		  $field3['fieldName']    	  =  $fieldName;
		  $field3['fieldID']  	      =  $fieldID;
		  $field3['campaignID']  	  =  $campaignID;
		  $field3['countryID']  	  =  $_SESSION['countryID'];
		  $field3['rel']  	          =  $iLRR['rel'];
		  $field3['lrel']  	          =  $iLRR['lrel'];;
		  $field3['val']  	          =  $val;
		  $field3['cond1']   	  	  =  $cond1;
		  $field3['min_val']   	  	  =  $min_val;
		  $field3['logical_operator'] =  $logical_operator;
		  $field3['cond2']   		  =  $cond2;
		  $field3['max_val']   		  =  $max_val;
		  //$field3['actual_input']  	  =  $actual_val;
		  $field3['dateAdded']  	  =  date('Y-m-d');
		  $res = $this->c3model->c3crud("insert","iLikeCanvassingRulesXref",$field3,'');
		}
	
		
		/*GET MINIMUM NUMBER OF COMMITEES*/
		$committee_Fields['campaignID']   = $campaignID;
		$res = $this->c3model->c3crud("insert","iLike_Rules_No_Committes_Ref",$committee_Fields,'');
		/*GET MINIMUM NUMBER OF COMMITEES*/
		
		
		//GET ALL THE RESPONDENTS
		$sql					= "SELECT * FROM campaignVotersXref WHERE campaignID = $campaignID";
		$campaignVotersXref 	= $this->db->query($sql);
		$campaignVotersXref		= $campaignVotersXref->result_array();
		
		//GET THE CAMPAIGN NAME
		$query 	= $this->db->query("SELECT * FROM campaign WHERE id = $campaignID LIMIT 0,1");
		$c 	= $query->row();
		$c->campaignName;
		$c->DateFrom;
		$c->DateTo;
		$c->campaignType;
		
		
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
		
		
		//CURRENT DATE
		$sql   = "SELECT DATEDIFF('$DateFrom',curdate()) AS f";
		$sql   = $this->db->query($sql);
		$d 	   = $sql->row();
		$from  = $d->f;
		
		//PRICE RANGES
		$levels = $this->db->query("SELECT distinct(level_name) as level_name FROM `price_range` ORDER BY id ASC");
		$levels = $levels->result_array();
		
		foreach($campaignVotersXref as $cVxref)
		{
			extract($cVxref);
			$query 	= $this->db->query("SELECT * FROM voters WHERE id = $voterID LIMIT 0,1");
			$row 	= $query->row();

			//CREATE MESSAGE
			$msg  = "";
			$msg = "Hello ". $row->fname ." ". $row->lname."! <br/><br/>"; 
			$msg .= "Welcome to the SMBIL Regional POSM Project!<br/><br/>"; 
			
			$msg .= "The iLike Campaign is now online and you have been selected as part of the <b>NOMINATION COMMITTEE</b>. <br/>"; 
			$msg .= "Please review <b>ALL</b> the items we have prepared for you.  <br/>"; 
			$msg .= "Your choices are important to us  :-)  <br/><br/>";
			
			$msg .= "GUIDELINES: <br/>"; 
			$msg .= "<ul> 
						<li>Please make sure to cast your votes by clicking <b>\"Like\"</b> or <b>\"Not Now\"</b> on all the items.</li>
						<li>You will be asked to vote on <b>ITEMS PER PRICE CATEGORY:</b> </li>
						<li style='list-style: none;'>&nbsp;</li>";
						//LEVELS
						foreach($levels as $level){
						extract($level);
						$msg .= "<li style='list-style: none;margin-left: 25px;'>- <label style='text-transform:uppercase'>$level_name</label> Items</li>";
						}
						
			$msg .=		"<li style='list-style: none;'>&nbsp;</li> <li>Please note that you <b>must</b> reach the <b>\"Thank You\" Page </b> for the system to record your votes. </li>
					 </ul><br/>";
					 
			$msg .= "Voting period is from ".date("M d, Y", strtotime($DateFrom))." to ".date("M d, Y", strtotime($DateTo)).".  <br/><br/>"; 
			
			$msg .= "You may click on the link below to start voting: <br/>";
			$msg .= "<a href='".HTTP_PATH."gallery/voting/".$this->encode_base64($campaignID) ."/". $this->encode_base64($row->email)."'>link</a><br/><br/> 
					Having problem with the link? Copy and paste the URL below to your browser's address bar:<br/>".HTTP_PATH."gallery/voting/".$this->encode_base64($campaignID) ."/". $this->encode_base64($row->email)."<br/><br/>";	
			
			$msg .= "Thank you very much for your participation. <br/>";
			$msg .= "Happy Voting! <br/><br/>";
			
			
			
			//SEND EMAIL
			$this->email->clear();
			$this->email->from('do.not.reply@smg.sanmiguel.com.ph', 'San Miguel Beer International');
			$this->email->to($row->email); 

			$this->email->subject('iLike Campaign');
			$this->email->message($msg);	
			
			
			//die();
			if($currentDate==$DateFrom OR $from<=0){
				if($this->email->send())
					$this->db->query("UPDATE voters SET email_sent='y' WHERE id = $voterID");
			}
			//echo $this->email->print_debugger();
		}
		
		//ON PROGRESS CAMPAIGN
		$dbFields['status'] = 'on progress';
		$dbFields['DatePublished'] = date('Y-m-d');
		$this->c3model->c3crud("update","campaign",$dbFields,$campaignID);
		
		//LOGS
		$CI->rec_logs->w($campaignID,$campaignName,'iLike Campaign','iLike','published');
		
		redirect('iLikeCampaign/votingCampaign', 'location', 301);		
		//die();
	}
	
	/*ALTER CAMPAIGN*/
	function alter($campaignID)
	{
		if($_POST==NULL){
			redirect(HTTP_PATH.'iLikeCampaign/votingCampaign', 'location', 301);
			die();
		}
			
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		
		$table = "campaign";
		$filter_WHERE = $this->modules->country();
		$filter_AND = $this->modules->country2();
		
		extract($_POST);
		
		//UPADATE CAMPAIGN
		$dbFields['DateFrom'] 	   = $DateFrom;
		$dbFields['DateTo'] 	   = $DateTo;
		$dbFields['DatePublished'] = date('Y-m-d');
		$dbFields['status'] 	   = 'on progress';
		$dbFields['remarks'] 	   = 'alter';
		$this->c3model->c3crud("update",$table,$dbFields,$campaignID);
		
		//LOGS
		$CI->rec_logs->w($campaignID,$campaignName,'iLike Campaign','iLike','alter');
		
		/*SAVE OR READ CAMPAIGN*/
		
		/*SAVE ALL THE NOMINESS*/
		if(isset($lnames)) {
			foreach($lnames as $k => $val)
			{	
				$voterFields['lname']  				=  $val;
				$voterFields['fname']  				=  $fnames[$k];
				$voterFields['gender']  			=  $gender[$k];
				$voterFields['email'] 				=  $emails[$k];
				$voterFields['voterTypeID'] 		=  2 ;
				$voterFields['campaignID'] 			=  $campaignID;
				$voterFields['department'] 			=  $departments[$k];
				$voterFields['year_of_birth'] 		=  $years[$k];
				$voterFields['Fields001'] 			=  $_SESSION['countryID'];
				$voterFields['dateAdded'] 	   		=  date('Y-m-d');
				$voterFields['addedAlter'] 	   		=  'y';
				
				$res = $this->c3model->c3crud("insert",'voters',$voterFields,'');
				
				//MAX VOTER ID
				$sql		= "SELECT max(id) as max_id FROM voters";
				$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
				$voterID    = $lastID[0]['max_id'];
				
				$campaignVotersXrefFields['campaignID'] = $campaignID; 
				$campaignVotersXrefFields['voterID']    = $voterID; 
				$res = $this->c3model->c3crud("insert",'campaignVotersXref',$campaignVotersXrefFields,'');
			}
		}
		/*SAVE ALL THE NOMINESS*/

		//GET THE CAMPAIGN NAME
		$query 	= $this->db->query("SELECT * FROM campaign WHERE id = $campaignID LIMIT 0,1");
		$c 	= $query->row();
		$c->campaignName;
		$c->DateFrom;
		$c->DateTo;
		$c->campaignType;
		
		
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
		
		//GET ALL THE RESPONDENTS WHO ARE INVITED
		$sql					= "SELECT * FROM campaignVotersXref WHERE campaignID = $campaignID ";
		$campaignVotersXref 	= $this->db->query($sql);
		$campaignVotersXref		= $campaignVotersXref->result_array();
		
		//PRICE RANGES
		$levels = $this->db->query("SELECT distinct(level_name) as level_name FROM `price_range` ORDER BY id ASC");
		$levels = $levels->result_array();
		
		foreach($campaignVotersXref as $cVxref)
		{
			extract($cVxref);
			$query 	= $this->db->query("SELECT * FROM voters WHERE id = $voterID AND votingStatus='invited' LIMIT 0,1");
			$row 	= $query->row();
			
			if($row){
			$msg  = "";
			$msg = "Hello ". $row->fname ." ". $row->lname."! <br/><br/>"; 
			$msg .= "Welcome to the SMBIL Regional POSM Project!<br/><br/>"; 
			
			$msg .= "The iLike Campaign is now online and you have been selected as part of the <b>NOMINATION COMMITTEE</b>. <br/>"; 
			$msg .= "Please review <b>ALL</b> the items we have prepared for you.  <br/>"; 
			$msg .= "Your choices are important to us  :-)  <br/><br/>";
			
			$msg .= "GUIDELINES: <br/>"; 
			$msg .= "<ul> 
						<li>Please make sure to cast your votes by clicking <b>\"Like\"</b> or <b>\"Not Now\"</b> on all the items.</li>
						<li>You will be asked to vote on <b>ITEMS PER PRICE CATEGORY:</b> </li>
						<li style='list-style: none;'>&nbsp;</li>";
						//LEVELS
						foreach($levels as $level){
						extract($level);
						$msg .= "<li style='list-style: none;margin-left: 25px;'>- <label style='text-transform:uppercase'>$level_name</label> Items</li>";
						}
						
			$msg .=		"<li style='list-style: none;'>&nbsp;</li> <li>Please note that you <b>must</b> reach the <b>\"Thank You\" Page </b> for the system to record your votes. </li>
					 </ul><br/>";
					 
			$msg .= "Voting period is from ".date("M d, Y", strtotime($c->DateFrom))." to ".date("M d, Y", strtotime($c->DateTo)).".  <br/><br/>"; 
			
			$msg .= "You may click on the link below to start voting: <br/>";
			$msg .= "<a href='".HTTP_PATH."gallery/voting/".$this->encode_base64($campaignID) ."/". $this->encode_base64($row->email)."'>link</a><br/><br/> 
					Having problem with the link? Copy and paste the URL below to your browser's address bar:<br/>".HTTP_PATH."gallery/voting/".$this->encode_base64($campaignID) ."/". $this->encode_base64($row->email)."<br/><br/>";	
			
			
			$msg .= "Thank you very much for your participation. <br/>";
			$msg .= "Happy Voting! <br/><br/>";
			$msg .= "This is a follow-up email.<br/><br/>";
			
			
			//SEND EMAIL
			$this->email->clear();
			$this->email->from('do.not.reply@smg.sanmiguel.com.ph', 'San Miguel Beer International');
			$this->email->to($row->email); 

			$this->email->subject('iLike Campaign');
			$this->email->message($msg);	
			
			
			//die();
			if($this->email->send())
				$this->db->query("UPDATE voters SET email_sent='y' WHERE id = $voterID");
			}
		}
	
		//DELETE iLike Result
		$this->c3model->c3crud("no-res",'','','',"DELETE FROM iLikeResultRef WHERE campaignID='$campaignID'");
		
		//die();
		redirect('iLikeCampaign/votingCampaign', 'location', 301);		
	}
	
	/*ALTER CAMPAIGN*/
	function encode_base64($sData){
		$sBase64 = base64_encode($sData);
		return str_replace('=', '', strtr($sBase64, '+/', '-_'));
	}

	function decode_base64($sData){
		$sBase64 = strtr($sData, '-_', '+/');
		return base64_decode($sBase64.'==');
	}
	
	/*iLike Campaign*/
	function lastVoted($email) 
	{
		 $last_vote = '';
		 $cur_date = date('Y-m-d');
		 $sql = "select * from voters where email='$email' AND votingStatus='done' order by dateAdded desc limit 0,1";
	 
		 $e     = $this->db->query($sql);
		 $voter     = $e->row(); 
		if($e->num_rows()>0)
		   {
		     $dateAdded = $voter->dateAdded;
		      
		     $sql       = $this->db->query("select  datediff( '$dateAdded','$cur_date') as dd");
		     $date      = $sql->row();
			 
		     if( $date->dd < 365) echo  "existing";
		   } 
	}
	
	function lastVotedReturn($email)
	{
		$last_vote = '';
		$sql = "SELECT tdate as last_vote FROM votexRef WHERE id = 
			   (SELECT MAX(id) FROM votexRef WHERE voterID IN
			   (SELECT id  FROM `voters` WHERE `email` = '$email'))";
	 
		$sql   = $this->db->query($sql);
		$tdate = $sql->row();
		
		//CHECK IF EMAIL EXIST
		if($tdate==NULL)
		{
			return "ok";
		}
		else
		{
			$last_vote = $tdate->last_vote;
			$cur_date = date('Y-m-d');
		
			//YEAR
			$sql ="SELECT DATEDIFF('$cur_date','$last_vote') AS DAYS";
			$sql   = $this->db->query($sql);
			$d = $sql->row();
			
			if($d->DAYS >= 365)
			{
				return "ok";
			}
			
			return "";
		}
	}
	
	function check_date($DateFrom,$DateTo)
	{			
		$sql ="SELECT DATEDIFF('$DateTo','$DateFrom') AS t";
		$sql   = $this->db->query($sql);
		$d = $sql->row();
		$to = $d->t;
		
		if($to >= 1)
		{
			echo 'good';
		}else{
			echo 'bad';
		}
	}
	
	/*iLikeItem Form*/
	function pushItem($itemID)
	{
		$_SESSION['iLike_Items'] .= $itemID."|";
		
		//ITEMS PAGINATION
		$itemDB =   "SELECT *,
					OUTLET_Status.statusName as OutletStatusName, 
					POSM_Status.statusName	 as POSMStatusName,
					items.id 				 as itemID 
					FROM items 
					LEFT JOIN POSM_Type 		ON items.POSMTypeID = POSM_Type.id 
					LEFT JOIN POSM_Status 		ON items.POSMStatusID = POSM_Status.id 
					LEFT JOIN OUTLET_Status 	ON items.OUTLETStatusID = OUTLET_Status.id
					LEFT JOIN premiumItemType 	ON items.PremiumTypeID = premiumItemType.id 
					LEFT JOIN MATERIAL_Type 	ON items.MaterialTypeID = MATERIAL_Type.id 
					LEFT JOIN country 			ON items.countryID = country.id 
					WHERE items.id = $itemID";
		
		$sql = $this->db->query($itemDB);
		$items = $sql->result_array();
		
		foreach($items as $d){
			extract($d);
			
			//GET FIRST ITEM IMAGE
			$sql = $this->db->query("SELECT image FROM items_images WHERE id = (SELECT MAX(ID) FROM items_images WHERE itemID = $itemID) LIMIT 0,1");
			$item_img = $sql->result_array();
			extract($item_img);
			$item_img = isset($item_img[0]['image']) ? $item_img[0]['image'] : 'blank.png';
		
			$img_link = HTTP_PATH."img/items/".$item_img;
			$itemPreviewLink = HTTP_PATH."itemDatabase/items/preview/$itemID"; 
			
			echo "<div style='width:160px;height:150px;margin:10px;' class='fl'>
					<div style='border: 1px solid #ccc;text-align:center;padding:5px;height:100px;'>
						<img src='$img_link' class='itemIcon'>
					</div>
					
					<p style='font-size:13px;text-align:center;border: 1px solid #ccc;padding-bottom:3px;'> 
						<b><a href='$itemPreviewLink' target='_blank' class='itemLink'> $itemName </a></b> <br/>
						<span onclick='removeItem($itemID)' class='removeIcon'> Remove  </span> 
					</p>
				</div>";
		}
	}
	
	function removeItem($itemID)
	{
		
		//REMOVE ITEM FROM DATABASE
		$items = explode('|',$_SESSION['iLike_Items']);
		
		
		$j=0;
		$newSession = '';
		for($i=0;$i<(count($items)-1);$i++)
		{
			if($items[$i] != $itemID)
			{
				$newSession .= $items[$i]."|";
			}
		}
		
		$_SESSION['iLike_Items'] = '';
		$_SESSION['iLike_Items'] = $newSession;
		
		
		//EXPLODE ITEM
		
		$items2 = explode('|',$_SESSION['iLike_Items']);
		
		for($k=0;$k<(count($items2));$k++){
		
			if($items2[$k]!=NULL){
				//ITEMS PAGINATION
				$itemDB =   "SELECT *,
							OUTLET_Status.statusName as OutletStatusName, 
							POSM_Status.statusName	 as POSMStatusName,
							items.id 				 as itemID 
							FROM items 
							LEFT JOIN POSM_Type 		ON items.POSMTypeID = POSM_Type.id 
							LEFT JOIN POSM_Status 		ON items.POSMStatusID = POSM_Status.id 
							LEFT JOIN OUTLET_Status 	ON items.OUTLETStatusID = OUTLET_Status.id
							LEFT JOIN premiumItemType 	ON items.PremiumTypeID = premiumItemType.id 
							LEFT JOIN MATERIAL_Type 	ON items.MaterialTypeID = MATERIAL_Type.id 
							LEFT JOIN country 			ON items.countryID = country.id 
							WHERE items.id =".$items2[$k] ;
				
				
				$sql = $this->db->query($itemDB);
				$items = $sql->result_array();
				
				foreach($items as $d){
					extract($d);
					
					//GET FIRST ITEM IMAGE
					$sql = $this->db->query("SELECT image FROM items_images WHERE id = (SELECT MAX(ID) FROM items_images WHERE itemID = $itemID) LIMIT 0,1");
					$item_img = $sql->result_array();
					extract($item_img);
					$item_img = isset($item_img[0]['image']) ? $item_img[0]['image'] : 'blank.png';
				
					$link = HTTP_PATH."img/items/".$item_img;
					$itemPreviewLink = HTTP_PATH."itemDatabase/items/preview/$itemID"; 
					
					echo "<div style='width:160px;height:150px;margin:10px;' class='fl'>
							<div style='border: 1px solid #ccc;text-align:center;padding:5px;height:100px;'>
								<img src='$link' class='itemIcon'>
							</div>
							
							<p style='font-size:13px;text-align:center;border: 1px solid #ccc;padding-bottom:3px;'> 
								<b><a href='$itemPreviewLink' target='_blank' class='itemLink'> $itemName </a></b> <br/>
								<span onclick='removeItem($itemID)' class='removeIcon'> Remove  </span> 
							</p>
						</div>";
				}
			}
		}
		
	}
	
	function itemOnSession($itemID)
	{
		//REMOVE ITEM FROM DATABASE
		$items = explode('|',$_SESSION['iLike_Items']);
		
		$j=0;
		$e = '';
		for($i=0;$i<(count($items)-1);$i++)
		{
			if($items[$i] == $itemID)
				$exist = 'exist';
		}
		
		echo $exist;
	}

	function cleariLikeSessionItems()
	{
		$_SESSION['iLike_Items'] = '';
		echo "clear";
	}
	
} ?>