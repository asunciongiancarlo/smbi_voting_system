<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class IWantCampaign extends CI_Controller {
 
  public function __construct()
   {
		parent::__construct();
		date_default_timezone_set('UTC');
		session_start();
		$this->load->model('c3model');
		$this->load->library('security');
		$this->load->library('email');
		$this->load->library('modules');
		$this->load->library('smtp');
		$this->load->helper('url');
		$this->output->enable_profiler(FALSE);
		set_time_limit(0);
		//print_r($_SESSION);

		$this->modules->session_handler();
   }

    public function index()
	{			
	   $this->modules->module_checker(29,'REVIEW');
	   
	   $data['vfile']		= 'iWantCampaign.php';
	   $data['title']		= 'San Miguel Brewing International';
	   $data['page_title']	= 'I Want Campaign';
	   $data['meta_description']	= 'San Miguel Brewing International';
	   $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
	   
	   $HTTP_PATH = HTTP_PATH;
	   $data['breadCrumbs']			= '<a href='.$HTTP_PATH.'iWantCampaign> I Want Campaign </a>';
	   
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
	
	function add_div_item($iID)
	{
		$sqlSTr="SELECT items.id as itemID, (SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, itemName, 
				POSM_Type.typeName as POSM_TypeName, countryName, level_name, extra_label
				FROM items
				LEFT JOIN POSM_Type ON items.POSMTypeID = POSM_Type.id 
				LEFT JOIN country ON items.countryID    = country.id
				LEFT JOIN price_range ON items.price_rangeID = price_range.id
				WHERE  items.id = $iID ORDER BY items.id DESC";
		$topItems = $this->db->query($sqlSTr);
		$topItems = $topItems->result_array();
		
		//CHECK IF ITEM EXIST
		$STATUS="ok";
		foreach($_SESSION['iWant_items'] as $i){	
			extract($i);
			if($itemID==$iID) $STATUS = "exist";
		}
		

		if($STATUS=='ok'){
			foreach($topItems as $ti){	
			 extract($ti);
			 $arr = array('level_name'	  =>$level_name,
						  'extra_label'	  =>$extra_label,
						  'itemID'        =>$iID,
						  'item_image'	  =>$item_image,
						  'itemName'  	  =>$this->cutStr($itemName),
						  'POSM_TypeName' =>substr($POSM_TypeName,0,-5),
						  'countryName'   =>$countryName);
			}
			array_unshift($_SESSION['iWant_items'],$arr);
			echo $STATUS;
		}else{
			echo $STATUS;
		}
	}
	
	function view_div_items()
	{
		$lastItem="";
		$ctr=0;
		foreach($_SESSION['iWant_items'] as $d){
			extract($d);
			
			$img_path = HTTP_PATH."img/small/$item_image";
			$itemPreviewLink = HTTP_PATH."gallery/itemInfo2/$itemID";
			//$w = $this->w($item_image);
			$del_shortCut="<img onclick=\"delete_item($itemID)\" src='".HTTP_PATH."img/delete-item.png' style='margin-left:8px;cursor:pointer;'>";
			$title_css="";
			if($lastItem!=$countryName) echo "<h5 style='clear:both;text-align: left;background: #330404;margin-top: 0;color: #fff;padding: 2px 10px;margin-bottom: -5px;'> $countryName  </h5>";
			$ctr++;
		 echo "
			  <div style='width:120px;height:173px;margin: 10px 5px 24px 10px;;background:white;' class='fl'>
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
					<b style='padding-right:10px;'> ". $itemName." </b>  
				</p>
				 <br/>
			 </div>";
			$lastItem=$countryName;
		}
	}
	
	function delete_div_item($iID)
	{
		$sqlSTr="SELECT items.id as itemID, (SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, itemName,
				POSM_Type.typeName as POSM_TypeName, countryName, level_name, extra_label
				FROM items
				LEFT JOIN POSM_Type ON items.POSMTypeID = POSM_Type.id 
				LEFT JOIN country ON items.countryID = country.id
				LEFT JOIN price_range ON items.price_rangeID = price_range.id
				WHERE  items.id = $iID ORDER BY items.id DESC";
		$topItems = $this->db->query($sqlSTr);
		$topItems = $topItems->result_array();
		
		foreach($topItems as $ti){	
		 extract($ti);
		 $arr = array('level_name'	  =>$level_name,
					  'extra_label'	  =>$extra_label,
					  'itemID'	  	  =>$iID,
					  'item_image'	  =>$item_image,
					  'itemName'      =>$this->cutStr($itemName),
					  'POSM_TypeName' =>substr($POSM_TypeName,0,-5),
					  'countryName'=>$countryName);
		}
		
		$key = array_search($arr,$_SESSION['iWant_items']);		
		unset($_SESSION['iWant_items'][$key]);
		
		echo "ok";
	}
	
	function count_div_item()
	{
		echo "Campaign Items: ".count($_SESSION['iWant_items']);
	}
	
	function inform_add_nomimees($campaignID,$campaignName)
	{
	  $sql = "SELECT DISTINCT(admin_users.id) AS aID, uname, full_name, email_address  FROM admin_users
			  LEFT JOIN admin_usersRoles 		ON admin_usersRoles.admin_userID =  admin_users.id
			  LEFT JOIN roles_userProfilesRef	ON roles_userProfilesRef.roleID  =  admin_usersRoles.roleID
			  LEFT JOIN country					ON country.id 					 = admin_users.countryID
			  WHERE roles_userProfilesRef.systemModID = 29 AND roles_userProfilesRef.function ='EDIT NOMINEES'
			  AND admin_users.countryID != 0 AND admin_users.countryID IN (SELECT DISTINCT (country.id) FROM country, items, iLikeResultRef
			  WHERE country.id = items.countryID AND
			  iLikeResultRef.itemID = items.id)
			";
				
	  $admin_users = $this->db->query($sql);
	  $admin_users = $admin_users->result_array();
	  
	  //print_r($admin_users);
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
	  
	  foreach($admin_users as $a)
	  { extract($a);
		
		$hash_id			= $this->encode_base64($aID);
		$hash_uname			= $this->encode_base64($uname);
		$hash_email_address	= $this->encode_base64($email_address);
		$hash_date	    	= $this->encode_base64(date('Y-m-d'));
		
			//BU MARKETING-ITEMS
			$msg = "<label style='font-size:19px;font-family:Arial,Helvetica,sans-serif;color:#9e0b0f;'>San Miguel Brewing, International, LTD. </label><br/>
					<label style='font-size:12px;font-family:Arial,Helvetica,sans-serif;color:#777777;border-color: gray;'>Hi $full_name,<br/></label>
					<label style='font-size:12px;font-family:Arial,Helvetica,sans-serif;color:#777777;border-color: gray;'>
					<b>$campaignName</b> is now open for your Business Unit to add screening committees, <br/>just click \"Add/Edit Committees\" from the list of available campaign. 
					</label><br/>
					<label style='font-size:12px;font-family:Arial,Helvetica,sans-serif;color:#777777;border-color: gray;'>Thank you!<br/></label>
					<br/>";
			$msg .= "<label style='font-size:12px;font-family:Arial,Helvetica,sans-serif;color:#777777;border-color: gray;'>
					Please click the following <a href='".HTTP_PATH."login/automatic_log_in/iwant/$hash_id/$hash_uname/$hash_email_address/$hash_date'>link</a> to automatically log in in the system. 
					</label><br/>";
			//echo $msg;	
			//SEND EMAIL
			$this->email->clear();
			$this->email->from('do.not.reply@smbi.com', 'San Miguel Beer International');
			$this->email->to($email_address); 

			$this->email->subject('iWant Campaign');
			$this->email->message($msg);	
			$this->email->send();
			//$this->email->print_debugger();
		}
		
		/*TAG CAMAPIGN AS NOTIFIED*/
		$this->db->query("UPDATE campaign SET BU_Notified='y' WHERE id=$campaignID");
	}
	
	function min_committees($votingCampaignID='',$revote='')
	{
		$filter_WHERE = $this->modules->country();
		
		//GET NUM COMMITEES FROM REF TABLE
		if($votingCampaignID!=''){
			//echo "SELECT num_commitee FROM iWantCampaignNumber_of_commitees_ref WHERE campaignID=$votingCampaignID LIMIT 0,1";
			$sql = $this->db->query("SELECT num_commitee FROM iWantCampaignNumber_of_commitees_ref WHERE campaignID=$votingCampaignID LIMIT 0,1");
			$row = $sql->row();
		}else{
			$sql = $this->db->query("SELECT num_commitee FROM iWantCampaignNumber_of_commitees WHERE countryID= 0 LIMIT 0,1");
			$row = $sql->row();
		}
	
		
		if($row->num_commitee)
			return $row->num_commitee;
		else
			return 0;
	}
	
	function iWantCampaign_Canvassing_Rules($votingCampaignID='')
	{
		$CI2 =& get_instance();
		$CI2->load->library('fv');
		$filter_WHERE = "WHERE countryID = 0";
		
		//GET ALL ILIKE CONDITIONS
		if($votingCampaignID!=""){
			$sql 				  = "SELECT *, iWantCanvassingRulesRef.price_rangeID as pRangeID FROM iWantCanvassingRulesRef WHERE campaignID = $votingCampaignID 
									 ORDER BY price_rangeID ASC, fieldID DESC, rel DESC, val ASC";
			$sql 				  = $this->db->query($sql);
			$iWantCanvassingRules = $sql->result_array();
		}else{
			$sql 				  = "SELECT *, iWantCanvassingRules.price_rangeID as pRangeID FROM iWantCanvassingRules $filter_WHERE 
									 ORDER BY price_rangeID ASC, fieldID DESC, rel DESC, val ASC";
			$sql 				  = $this->db->query($sql);
			$iWantCanvassingRules = $sql->result_array();
		}
		
		$rules = ""; 
		//print_r($iWantCanvassingRules);
		$tbl="";
		foreach($iWantCanvassingRules as $iL)
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
			$query 		= $this->db->query("SELECT level_name, extra_label FROM price_range WHERE id=$price_rangeID LIMIT 0,1");
			$row 		= $query->row();
			$level_name = $row->level_name;
			$extra_label = $row->extra_label;
			
			$status="";
			$original_val =$val;
			if(strpos($min_val,".")==TRUE) $min_val = $min_val."%";
			if(strpos($max_val,".")==TRUE) $max_val = $max_val."%";
			
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
		
		$tbl .=  "<tr><td colspan='5' style='text-align:left'><b>Canvassing Rules</b></td></tr>
					<tr style='border-radius: 6px;font-size: 10px;'>
						<td style='width:130px;text-align:center;color:white;' bgcolor='#bb1d1d' colspan='1'>   			<b>Item Type  			</b></td> 
						<td style='width:250px;text-align:center;color:white;' bgcolor='#bb1d1d' >   			<b>Price Category  		</b></td> 
						<td style='width:360px;text-align:center;color:white;' bgcolor='#bb1d1d' colspan='3'>   			<b>Canvassing Condition </b></td> 
					</tr>";
		$x=0;
		foreach($rules as $r){
			extract($r);
			$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
			$tbl .= "<tr>
					  <td  $c  style='text-align:left;' colspan='1'>	  $fieldValue  		</td>
					  <td  $c  style='text-align:left;' >	  $price_rangeName  </td>
					  <td  $c  style='text-align:center;' colspan='3'>	  $cond1 $min_val $logical_operator $cond2 $max_val  					</td>
					  ";
			$tbl .= "</tr>";
		}
		return $tbl."</table>";
	}
	
	function alter_canvassing_rules($action='',$campaignID='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		$this->modules->module_checker(29,'ALTER CANVASSING RULES');
		
		$data['vfile']				= 'alter_canvassing_rulesFORM.php';
	    $data['title']				= 'iLike Campaign Rules | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
				
		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
	    $data['breadCrumbs']		= '<a href='.$HTTP_PATH.'iLikeCampaign/votingCampaign>  iLike Campaign </a>';
	    $data['breadCrumbs']	   .= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']	   .= "<a href='".HTTP_PATH."iWantCampaign/alter_canvassing_rules/edit/$campaignID'> Alter Canvassing Rules </a>";

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
			$data['vfile']	    = 'alter_canvassing_rulesFORM_iWant.php';
		}
		elseif($action=="update")
		{
			if($_POST==NULL){
				redirect(HTTP_PATH.'iWantCampaign/iWant', 'location', 301);
				die();
			}
			print_r($_POST);
			$this->db->query("DELETE FROM iWantCanvassingRulesRef WHERE campaignID = $campaignID");
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
				
				$res = $this->c3model->c3crud("insert",'iWantCanvassingRulesRef',$dbFields,'');
			}
			//LOGS
			$sql = $this->db->query("SELECT campaignName FROM campaign WHERE id = $campaignID LIMIT 0,1");
			$row = $sql->row();
			$CI->rec_logs->w($campaignID,$row->campaignName,'iWant','iWant Canvassing Rules','alter canvassing rules');
		    
			//UPDATE CAMPAIGN
			$this->db->query("UPDATE campaign SET campaign.status='on progress', campaign.remarks='alter canvassing rules' WHERE id = $campaignID");
			
			redirect('iWantCampaign/iWant/iWant_canvassing_rules_has_been_altered', 'location', 301);
		}
	    $this->load->view('innerPages',$data); 
	}
	
	function iWantVotingRules($view='',$votingCampaignID='')
	{
	 $CI2 =& get_instance();
	 $CI2->load->library('fv');
	 if($view=='add' OR $view=="refresh")
		$sql = $this->db->query("SELECT * FROM iWantVotingRules WHERE countryID = 0 ORDER BY price_rangeID ASC, fieldID DESC, rel DESC, val ASC");
	 if($view=='edit')
	    $sql = $this->db->query("SELECT *, iWantVotingRulesRef.id as iWVR_ID FROM iWantVotingRulesRef  
								 WHERE campaignID=$votingCampaignID ORDER BY price_rangeID ASC, fieldID DESC, rel DESC, val ASC");
		
	 $iWantVotingRules = $sql->result_array();
	 //print_r($iWantVotingRules);
	 foreach($iWantVotingRules as $iW)
	 { extract($iW);
	   $orig_fieldName = $fieldName;
	   switch($fieldName){
		case("POSMTypeID"):
			$tableName	= $CI2->fv->label(4);;
			$fieldName  = 'typeName';
			$table 		= 'POSM_Type';
			$fieldType  = 'POSM_TypeName';
		break;
		case("POSMStatusID"):
			$tableName	= 'ITEM STATUS';
			$fieldName  = 'statusName';
			$table 		= 'POSM_Status';
			$fieldType  = 'POSM_StatusName';
		break;
		case("OUTLETStatusID"):
			$tableName	= $CI2->fv->label(6); ;
			$fieldName  = 'statusName';
			$table 		= 'OUTLET_Status';
			$fieldType  = 'POSM_OutletName';
		break;
		case("PremiumTypeID"):
			$tableName	= $CI2->fv->label(7);;
			$fieldName  = 'premiumTypeName';
			$table 		= 'premiumItemType';
			$fieldType  = 'POSM_PremiumName';
		break;
		case("MaterialTypeID"):
			$tableName	= $CI2->fv->label(9);;
			$fieldName  = 'materialName';
			$table 		= 'MATERIAL_Type';
			$fieldType  = 'POSM_MaterialName';
		break;
		case("brandID"):
			$tableName	= $CI2->fv->label(3);;
			$fieldName  = 'brandName';
			$table 		= 'brands';
			$fieldType  = 'POSM_BrandName';
		break;
		}
		
		$query 		= $this->db->query("SELECT $fieldName FROM $table WHERE id=$fieldID LIMIT 0,1");
		$row 		= $query->row();
		$name_Field = $row->$fieldName;
		
		//GET PRICE RANGE NAME
		$query 		= $this->db->query("SELECT level_name as price_rangeName, extra_label as eLabel FROM price_range WHERE id=$price_rangeID LIMIT 0,1");
		$row 		= $query->row();
		$price_rangeName = $row->price_rangeName;
		$eLabel 	 	 = $row->eLabel;
		
		//FIND HOW MANY ITEMS
		$items=0;
		//echo "iWant_items";
		//print_r($_SESSION['iWant_items']);
		$status="Good";
		foreach($_SESSION['iWant_items'] as $d){
		extract($d);
		if($POSM_TypeName== (substr($name_Field,0,-5)) AND $price_rangeName==$level_name)
		 $items++;
		}
		if($items==0) $status = "Not Good";
		
		//PERCENTAGE
		//MIN VAL > ITEMS
		if(strpos($min_val,".")==TRUE){
		 if(round($min_val * $items)> $items) $status="Not Good";
		}else{
		 if($min_val > $items) 				  $status="Not Good";
		}
		
		//NOT MIN * MAX == 0
		if(strpos($min_val,".")==TRUE & strpos($max_val,".")==TRUE){
		 if(round($min_val * $items)==0 & round($max_val * $items)==0) $status="Not Good";
		}
		//MIN VOTE == 0
		if(strpos($min_val,".")==TRUE){
		 if(round($min_val * $items)==0 OR round($min_val * $items)>$items) $status="Not Good";
		 $min_val  = "$min_val% (". round($min_val * $items) .")";
		}
		//MAX VOTE == 0
		if(strpos($max_val,".")==TRUE){
		 if(round($max_val * $items)==0) $status="Not Good";
		 $max_val  = "$max_val% (". round($max_val * $items) .")";
		}
		
		
		
		
		$rules[] = array(
						'cond1'				=>$cond1,
						'min_val'			=>$min_val,
						'logical_operator'	=>$logical_operator,
						'cond2'				=>$cond2,
						'max_val'			=>$max_val,
					    'price_rangeName'	=>$eLabel,
						'table'				=>$table,
						'fieldName' 		=>$orig_fieldName, 
						'fieldID'			=>$fieldID, 
						'fieldValue'		=>$name_Field, 
						'stat'				=>$status,
						'current_num_items' =>$items
						);
	 }
	 $tbl ="";
	 $tbl .="<table cellpadding='0' cellspacing='0' border=1 style='width:690px;margin: 0px auto;font-size:12px;background-color:white;left:55%;' class='fr iLike_Result_Table'>";
		$x = 0;
		$invalidRules=0;
		$tbl .="<tr><td colspan='5' style='text-align:left'><b>Voting Rules</b>  <span style='margin-left:276px;cursor:pointer;float:right;margin-right: 10px;' onclick='campaignRules()'><b>(&#x2716; close)</b></span></td></tr>
				<tr style='border-radius: 6px;font-size: 10px;'>
					<td style='width:274px;text-align:center;color:white;' bgcolor='#bb1d1d'>   <b>Item Type  				</b></td> 
					<td style='width:194px;text-align:center;color:white;' bgcolor='#bb1d1d'>   <b>Price Category  			</b></td> 
					<td style='width:430px;text-align:center;color:white;' bgcolor='#bb1d1d'>   <b>Voting Condition  		</b></td> 
					<td style='width:207px;text-align:center;color:white;' bgcolor='#bb1d1d'>   <b>Current Items    		</b></td> 
					<td style='width:130px;text-align:center;color:white;' bgcolor='#bb1d1d'>   <b>Status    				</b></td> 
				</tr>";
		//print_r($voting_rules);
		$TOTALcurrent_num_items=0;
		foreach($rules as $r){
			extract($r);
			$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
			$tbl .="<tr>
					 <td  $c  style='text-align:left;'>	  	  $fieldValue  				</td>
					 <td  $c  style='text-align:left;'>	  	  $price_rangeName  		</td>
					 <td  $c  style='text-align:center;'>	  $cond1 $min_val $logical_operator $cond2 $max_val  					</td>
					 <td  $c  style='text-align:center;'>	  <b>$current_num_items</b>  		</td> 
					 <td  $c  style='text-align:center;'>	  $stat  	</td> 
					</tr>";
			$TOTALcurrent_num_items+= $current_num_items;
			if($stat=='Not Good')
				$invalidRules++;
		}
		$tbl .="<tr> 
				<td colspan='1' style='width:105px;'>
				<b>Total</b></td> 
				<td></td> 
				<td></td> 
				<td><b>$TOTALcurrent_num_items</b></td>
				<td></td>
				</tr>";
		//SECRET ELEMENT
		$tbl .="<input type='hidden' value='$invalidRules' name='invalidRules' id='invalidRules'>
		";
		
	 if($view=='add' OR $view=='edit'){
		$tbl .= $this->iWantCampaign_Canvassing_Rules($votingCampaignID);
		return $tbl;
	 }
	 if($view=='refresh'){
		$tbl .= $this->iWantCampaign_Canvassing_Rules($votingCampaignID);
	    echo $tbl;	
	 }
	}
	
	function screening_committees($action='',$id='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		
		//$this->modules->module_checker(29,'REVIEW');
		$filter_WHERE = "";
		
		if($_SESSION['super_admin']!='y' AND $_SESSION['countryID']!=0){
			$filter_WHERE = " WHERE voters_group.countryID = ".$_SESSION['countryID'];
		}
		
		$votingCampaignID = $id;

		$data['vfile']				= 'screening_committees.php';
	    $data['title']				= 'iWant Campaign | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
	
		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
	    $data['breadCrumbs']		= '<a href='.$HTTP_PATH.'iWantCampaign/iWant>  iWant Campaign </a>';
	    $data['breadCrumbs']	   .= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']	   .= "<a href='".HTTP_PATH."iWantCampaign/screening_committees'> Manage Screening Committees </a>";
		
		
		$data['USER_MANUAL'] = $this->modules->user_manual(42);
		
		//CRUD
		$data['EDIT_NOMINEES'] 	=  $this->modules->crud_checker(29,'EDIT NOMINEES');
		
		
		//CSRF
		$data['csrf'] = "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>"; 

		//print_r($_POST);
		extract($_POST);
	    
		if($action=='insert_success'){
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Group has been save.');
		}
		elseif($action=='delete_success'){
			$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Group has been deleted.');
		}
		elseif($action=='update_success'){
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Group has been updated.');
		}
		elseif($action=="insert")
		{
			$this->modules->module_checker(29,'EDIT NOMINEES');
			if($_POST==NULL){
				redirect(HTTP_PATH.'iWantCampaign/screening_committees', 'location', 301);
				die();
			}
			//GET COUNTRY
			$sql = $this->db->query("SELECT countryCode FROM country WHERE id=". $countryIDs[0] . " LIMIT 0,1");
			$row = $sql->row(); 
			$countryCode = $row->countryCode;
			$dbFields['group_name']    = date('Y-m-d')." ".date('H:i:s')." $countryCode";
			$dbFields['group_type']    = 'iWant';
			$dbFields['countryID'] 	   = $countryIDs[0];
			$dbFields['userID'] 	   = $_SESSION['user_id'];
			$dbFields['dateAdded'] 	   = date('Y-m-d');
		
			//INSERT GROUP
			$res = $this->c3model->c3crud("insert","voters_group",$dbFields,'');
			
			//MAX ID CAMPAIGN
			$sql		= "SELECT max(id) as max_id FROM voters_group";
			$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
			$groupID 	= $lastID[0]['max_id'];
					
			//VOTER FROM TABLE
			if(isset($lnames)) {
				foreach($lnames as $k => $val)
				{	
					$voterFields['lname']  				=  $val;
					$voterFields['fname']  				=  $fnames[$k];
					$voterFields['gender']  			=  $genders[$k];
					$voterFields['email'] 				=  $emails[$k];
					$voterFields['voterTypeID'] 		=  2 ;
					$voterFields['campaignID'] 			=  0;
					$voterFields['department'] 			=  $departments[$k];
					$voterFields['year_of_birth'] 		=  $years[$k];
					$voterFields['fields001'] 		    =  $countryIDs[$k];
					$voterFields['fields002'] 		    =  'iWant';
					$voterFields['fields003'] 		    =  $groupID;
					$voterFields['dateAdded'] 	   		=  date('Y-m-d');
					
					$res = $this->c3model->c3crud("insert",'voters',$voterFields,'');
				}
			}
			$CI->rec_logs->w($groupID,$dbFields['group_name'],'iWant Voters','iWant','add');
		
			redirect('iWantCampaign/screening_committees/insert_success', 'location', 301);
		}
		elseif($action=="deleteOneItem")
		{
			$this->modules->module_checker(29,'EDIT NOMINEES');
			
			//DELETE VOTERS
			$this->c3model->c3crud("no-res",'','','',"DELETE FROM voters WHERE Fields003='$id' AND Fields002='iWant'");
			//LOGS
			$sql = "SELECT group_name FROM voters_group WHERE id = $id";
			$sql = $this->db->query($sql);
			$sql = $sql->row();
			$CI->rec_logs->w($id,$sql->group_name,'iWant Voters','iWant','delete');
			$this->c3model->c3crud("no-res",'','','',"DELETE FROM voters_group WHERE id='$id'");
			
			$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Campaign has been deleted.');
			redirect('iWantCampaign/screening_committees/delete_success', 'location', 301);
		}
		elseif($action=="edit")
		{
			$this->modules->module_checker(29,'EDIT NOMINEES');
			$data['min_committees'] = $this->min_committees();
			$data['id']     		= $id;
			$data['vfile']			= 'screening_committeesFORM.php';
		
			$sql = $this->db->query("SELECT voters.id as voterID, voters.lname, voters.fname ,voters.gender as vGender, voters.email AS email_address, department, year_of_birth,  Fields001 as countryID, country.countryName as countryName, votingStatus 
									FROM voters 
									LEFT JOIN country ON country.id = voters.Fields001  
									WHERE voters.Fields003 = $id
									ORDER BY country.id ASC ");
		
			$data['admin_users'] = $sql->result_array();						 
		}
		elseif($action=="add")
		{
			$this->modules->module_checker(29,'EDIT NOMINEES');
			$data['edit_items'] = TRUE;
			$data['vfile']	= 'screening_committeesFORM.php';
			$data['min_committees'] = $this->min_committees();
		}
		elseif($action=="update")
		{
			$this->modules->module_checker(29,'EDIT NOMINEES');
			if($_POST==NULL){
				redirect(HTTP_PATH.'iWantCampaign/screening_committees', 'location', 301);
				die();
			}
			
			$dbFields['userID'] 	   	= $_SESSION['user_id'];
			$dbFields['dateLastEdited'] = date('Y-m-d');
			$this->c3model->c3crud("update","voters_group",$dbFields,$id);
			$sql = $this->db->query("SELECT group_name FROM voters_group WHERE id= $id LIMIT 0,1");
			$row = $sql->row(); 
			$group_name = $row->group_name;
			$CI->rec_logs->w($id,$group_name,'iWant Voters','iWant','edit');	

			//VOTER FROM TABLE
			$this->c3model->c3crud("no-res",'','','',"DELETE FROM voters WHERE Fields003='$id' AND Fields002='iWant'");
			if(isset($lnames)) {
				foreach($lnames as $k => $val)
				{	
					$voterFields['lname']  				=  $val;
					$voterFields['fname']  				=  $fnames[$k];
					$voterFields['gender']  			=  $genders[$k];
					$voterFields['email'] 				=  $emails[$k];
					$voterFields['voterTypeID'] 		=  2 ;
					$voterFields['campaignID'] 			=  0;
					$voterFields['department'] 			=  $departments[$k];
					$voterFields['year_of_birth'] 		=  $years[$k];
					$voterFields['fields001'] 		    =  $countryIDs[$k];
					$voterFields['fields002'] 		    =  'iWant';
					$voterFields['fields003'] 		    =  $id;
					$voterFields['dateAdded'] 	   		=  date('Y-m-d');
					
					$res = $this->c3model->c3crud("insert",'voters',$voterFields,'');
				}
			}		

			//LOGS
			$CI->rec_logs->w($campaignID,$group_name,'iWant Campaign','iWant','edit');
			
			redirect('iWantCampaign/screening_committees/update_success', 'location', 301);
		}

		$sql2 = $this->db->query("SELECT voters_group.id as vgID, group_name, countryName, full_name, voters_group.dateAdded as tdate FROM voters_group
								  LEFT JOIN country 	ON country.id 	  = voters_group.countryID
								  LEFT JOIN admin_users ON admin_users.id = voters_group.userID 
								  $filter_WHERE
								  ORDER BY group_name ASC
							  ");									
		$data['voters_group']  = $sql2->result_array();
	
	
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
	
	function Delete_screening_committeesGroup()
	{
		//SELECT FROM ALL COUNTRIES
		$sql 	  = $this->db->query("SELECT DISTINCT(countryID) as dCountryID FROM voters_group");
		$dCountry = $sql->result_array();
		$votersList="";
		foreach($dCountry as $dC)
		{
		 extract($dC);
		 $sql  = $this->db->query("SELECT voters_group.id as vgID, MIN(group_name) FROM voters_group WHERE countryID = $dCountryID AND group_type ='iWant' LIMIT 1");
		 $row  = $sql->row();
		 $vgID = $row->vgID;
		 
		 $sql = $this->db->query("DELETE FROM voters  		WHERE voters.Fields003 =  $vgID");
		 $sql = $this->db->query("DELETE FROM voters_group  WHERE id 			   =  $vgID");
		}
	}
	
	function screening_committeesGroup()
	{
		//SELECT FROM ALL COUNTRIES
		$sql 	  = $this->db->query("SELECT DISTINCT(countryID) as dCountryID FROM voters_group");
		$dCountry = $sql->result_array();
		$votersList=array();
		foreach($dCountry as $dC)
		{
		 extract($dC);
		 $sql = $this->db->query("SELECT voters_group.id as vgID, MIN(group_name) FROM voters_group WHERE countryID = $dCountryID AND group_type ='iWant' LIMIT 1");
		 $row = $sql->row();
		 $vgID = $row->vgID;
		 
		 $sql = $this->db->query("SELECT voters.id as voterID, voters.lname, voters.fname ,voters.gender as vGender, voters.email AS email_address, department, year_of_birth,  
								 Fields001 as countryID, country.countryName as countryName, votingStatus 
								 FROM voters 
								 LEFT JOIN country ON country.id = voters.Fields001  
								 WHERE voters.Fields003 =  $vgID
								 ORDER BY country.id ASC ");
		 $voters = $sql->result_array();
		 
		 foreach($voters as $v)
		 {extract($v);
		  $votersList[] = array('voterID'	   =>$voterID, 
								'lname'		   =>$lname,
								'fname'		   =>$fname,
								'vGender'	   =>$vGender,
								'email_address'=>$email_address,
								'department'   =>$department,
								'year_of_birth'=>$year_of_birth,
								'countryID'    =>$countryID,
								'countryName'  =>$countryName,
								'votingStatus' =>$votingStatus);
		 }
		}
		return $votersList;
	}
	
	function newest_iWant_items()
	{
		$cIDs="";
		$sql	 	= $this->db->query("SELECT DISTINCT(countryID) as countryID FROM campaign WHERE countryID!=0 ");
		$countryIDs = $sql->result_array();
		foreach($countryIDs as $cID)
		{ extract($cID);
		  $sql = $this->db->query("SELECT campaign.id as campaignID, campaignName, 
								  dateProcess FROM campaign WHERE countryID = $countryID 
								  AND campaignType='iLike' AND status='done' ORDER BY dateTimeProcess desc LIMIT 1");
		  $row = $sql->row(); 
		  if(isset($row->campaignID))$cIDs .= $row->campaignID.",";
		}		
		return substr($cIDs."0,",0,-1);
	}
	
	function additional_voters($campaignID)
	{
	 //AVAILABLE VOTERS in BU
	 $available_group 	= $this->db->query("SELECT DISTINCT(countryID) as available_groupID FROM voters_group");
	 $available_group 	= $available_group->result_array();
	 
	 //PARTICIPANTS
	 $participants  = $this->db->query("SELECT DISTINCT(Fields001) as participantsID FROM voters WHERE campaignID = $campaignID");
	 $participants  = $participants->result_array();
	 //GET ALL PARTICIPANTS IN AN Array
	 $participants_lists=array();
	 foreach($participants as $part)
	 { extract($part);
	   $participants_lists[] .= "'$participantsID'";
	 }
	 
	 //CHECK IF AVAILBALE IS ALREADY IN THE PARTICIPANTS
	 $available_group_lists=array();
	 foreach($available_group as $avG)
	 { extract($avG);
	   $available_group_lists[] .= "'$available_groupID'";
	 }
	 
	 $availableFinalIDs=array();
	 foreach($available_group_lists as $ag)
	 { 
	   if(!in_array($ag,$participants_lists,true))
			$availableFinalIDs[] .= $ag;
	 }
	 
	//ADDITIONAL VOTERS 
	$votersList=array();
	foreach($availableFinalIDs as $dC)
	{
	 //extract($dC);
	 $sql = $this->db->query("SELECT voters_group.id as vgID, MIN(group_name) FROM voters_group WHERE countryID = $dC AND group_type ='iWant' LIMIT 1");
	 $row = $sql->row();
	 $vgID = $row->vgID;
	 
	 $sql = $this->db->query("SELECT voters.id as voterID, voters.lname, voters.fname ,voters.gender as vGender, voters.email AS email_address, department, year_of_birth,  
							 Fields001 as countryID, country.countryName as countryName, votingStatus 
							 FROM voters 
							 LEFT JOIN country ON country.id = voters.Fields001  
							 WHERE voters.Fields003 =  $vgID
							 ORDER BY country.id ASC ");
	 $voters = $sql->result_array();
	 foreach($voters as $v)
	 {extract($v);
	  $votersList[] = array('voterID'	   =>$voterID, 
							'lname'		   =>$lname,
							'fname'		   =>$fname,
							'vGender'	   =>$vGender,
							'email_address'=>$email_address,
							'department'   =>$department,
							'year_of_birth'=>$year_of_birth,
							'countryID'    =>$countryID,
							'countryName'  =>$countryName,
							'votingStatus' =>$votingStatus);
	 }
	}
	
	//EXISTING
	$sql = $this->db->query("SELECT voters.id as voterID, voters.lname, voters.fname ,voters.gender as vGender, voters.email AS email_address, department, year_of_birth,  Fields001 as countryID, country.countryName as countryName, votingStatus 
							 FROM voters 
							 LEFT JOIN country ON country.id = voters.Fields001  
							 WHERE voters.campaignID = $campaignID 
							 ORDER BY country.id ASC ");
	$sql = $sql->result_array();
	foreach($sql as $voters)
	{extract($voters);
	  $votersList[] = array('voterID'	   =>$voterID, 
							'lname'		   =>$lname,
							'fname'		   =>$fname,
							'vGender'	   =>$vGender,
							'email_address'=>$email_address,
							'department'   =>$department,
							'year_of_birth'=>$year_of_birth,
							'countryID'    =>$countryID,
							'countryName'  =>$countryName,
							'votingStatus' =>$votingStatus);
	}
	return $votersList;
	}
	
	function deleteAdditionalVoters($campaignID)
	{
	 //AVAILABLE VOTERS in BU
	 $available_group 	= $this->db->query("SELECT DISTINCT(countryID) as available_groupID FROM voters_group");
	 $available_group 	= $available_group->result_array();
	 
	 //PARTICIPANTS
	 $participants  = $this->db->query("SELECT DISTINCT(Fields001) as participantsID FROM voters WHERE campaignID = $campaignID");
	 $participants  = $participants->result_array();
	 //GET ALL PARTICIPANTS IN AN Array
	 $participants_lists=array();
	 foreach($participants as $part)
	 { extract($part);
	   $participants_lists[] .= "'$participantsID'";
	 }
	 
	 //CHECK IF AVAILBALE IS ALREADY IN THE PARTICIPANTS
	 $available_group_lists=array();
	 foreach($available_group as $avG)
	 { extract($avG);
	   $available_group_lists[] .= "'$available_groupID'";
	 }
	 
	 $availableFinalIDs=array();
	 foreach($available_group_lists as $ag)
	 { 
	   if(!in_array($ag,$participants_lists,true))
			$availableFinalIDs[] .= $ag;
	 }
	 
	 foreach($availableFinalIDs as $dC)
	 {
	 $sql  = $this->db->query("SELECT voters_group.id as vgID, MIN(group_name) FROM voters_group WHERE countryID = $dC AND group_type ='iWant' LIMIT 1");
	 $row  = $sql->row();
	 $vgID = $row->vgID;
	 
	 $sql = $this->db->query("DELETE FROM voters  		WHERE voters.Fields003 =  $vgID");
	 $sql = $this->db->query("DELETE FROM voters_group  WHERE id 			   =  $vgID");
	 }
	}
	
	function howManyCountryThereIniWantItems()
	{
		$ctr  =0;
		$cName="";
		foreach($_SESSION['iWant_items'] as $iw)
		{ extract($iw);
		  if($cName!=$countryName) $ctr++;
		  
		  $cName=$countryName;
		}
		
		if($ctr==0) 
			$ctr=1;
		else
			$ctr--;
		
		//GET NUMBER OF SCREENING COMMITTEES
		$sql = $this->db->query("SELECT num_commitee FROM iWantCampaignNumber_of_commitees WHERE countryID=0 LIMIT 1");
		$row = $sql->row();
		echo $ctr = ($ctr * $row->num_commitee);
	}
	
	function comittees_vs_canvassing_rules($committees='')
	{
	//DETECT USER ID 
	$iWantCanvassingRules ="";
	$sql 				  = "SELECT min(min_val) as mVal FROM iWantCanvassingRules WHERE countryID = ". $_SESSION['countryID']." LIMIT 0,1";
	$sql 				  = $this->db->query($sql);
	$iWantCanvassingRules = $sql->row();
	//TEST EVERY CANVASSING RULES
	if($iWantCanvassingRules->mVal > $committees) echo "not";
	else										  echo "ok";
	}
	
	function iWant($action='',$id='')
	{	
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		
		$this->modules->module_checker(29,'REVIEW');
		$filter_WHERE = $this->modules->iWant_Campaign_country();
		$filter_AND   = "";
		$voter_AND   = "";
		
		if($_SESSION['super_admin']!='y' AND $_SESSION['countryID']!=0){
			$filter_AND = " AND campaign.countryID = ".$_SESSION['countryID'];
			$voter_AND  = " AND voters.Fields001 = ".$_SESSION['countryID'];
		}
		
		$votingCampaignID = $id;

		$table= "campaign";
		$data['vfile']				= 'iWant.php';
	    $data['title']				= 'iWant Campaign | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."iWantCampaign/iWant";
	    $data['breadCrumbs']		= '<a href='.$HTTP_PATH.'> iWant Campaign </a>';
		
		$data['USER_MANUAL'] = $this->modules->user_manual(42);
		
		//CRUD
		$data['ADD'] 	=  $this->modules->crud_checker(29,'ADD');
		$data['EDIT'] 	=  $this->modules->crud_checker(29,'EDIT');
		$data['DELETE'] =  $this->modules->crud_checker(29,'DELETE');
		$data['ALTER_CAMPAIGN'] 		=  $this->modules->crud_checker(29,'ALTER CAMPAIGN');
		$data['ALTER_RESULTS'] 			=  $this->modules->crud_checker(27,'ALTER RESULTS');
		$data['PUBLISH_CAMPAIGN'] 		=  $this->modules->crud_checker(29,'PUBLISH CAMPAIGN');
		$data['EDIT_NOMINEES'] 			=  $this->modules->crud_checker(29,'EDIT NOMINEES');
		$data['ALTER_CANVASSING_RULES'] =  $this->modules->crud_checker(29,'ALTER CANVASSING RULES');
		
		//CSRF
		$data['csrf'] = "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>"; 
		
		//TOTAL NUMBER OF ROWS			
		$data['active_page']=1;
		$sql       = $this->db->query("SELECT *, campaign.id AS campaignID  FROM $table 
									 LEFT JOIN admin_users 
									 ON campaign.adminCreatorID = admin_users.id
									 WHERE campaign.campaignType = 'iWant' 
									 ORDER BY campaignID DESC");
		$sql       = $sql->result_array();
		$total_rec = count($sql);

		$pagenum = 1;
		$data['page_rows'] 	= 20; 
		$data['last'] 		= ceil($total_rec/$data['page_rows']);		
		$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		

		//print_r($_POST);
		extract($_POST);
	    
		if($action=='insert_success'){
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Campaign has been save.');
		}
		elseif($action=='iWant_canvassing_rules_has_been_altered'){
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'iWant canvassing rules has been altered.');
		}
		elseif($action=='delete_success'){
			$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Campaign has been delete.');
		}
		elseif($action=='update_success'){
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Campaign has been updated.');
		}
		elseif($action=="insert")
		{
			$this->modules->module_checker(29,'ADD');
			if($_POST==NULL){
				redirect(HTTP_PATH.'iWantCampaign/iWant', 'location', 301);
				die();
			}
			//CAMPAIGN
			$dbFields['campaignName']  = $campaignName;
			$dbFields['campaignType']  = 'iWant';
			$dbFields['DateAdded'] 	   = date('Y-m-d');
			$dbFields['DateFrom'] 	   = $DateFrom;
			$dbFields['DateTo'] 	   = $DateTo;
			$dbFields['status'] 	   = 'new';
			$dbFields['adminCreatorID']= $_SESSION['user_id'];
			$dbFields['countryID']	   = $_SESSION['countryID'];
			
			//INSERT CAMPAIGN
			$res = $this->c3model->c3crud("insert",$table,$dbFields,'');
			
			//MAX ID CAMPAIGN
			$sql		= "SELECT max(id) as max_id FROM $table";
			$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
			$campaignID = $lastID[0]['max_id'];
			
			//DELETE VOTING GROUP
			$this->Delete_screening_committeesGroup();
			
			//VOTER FROM TABLE
			if(isset($lnames)) {
				foreach($lnames as $k => $val)
				{	
					$voterFields['lname']  				=  $val;
					$voterFields['fname']  				=  $fnames[$k];
					$voterFields['gender']  			=  $genders[$k];
					$voterFields['email'] 				=  $emails[$k];
					$voterFields['voterTypeID'] 		=  2 ;
					$voterFields['campaignID'] 			=  $campaignID;
					$voterFields['department'] 			=  $departments[$k];
					$voterFields['year_of_birth'] 		=  $years[$k];
					$voterFields['fields001'] 		    =  $countryIDs[$k];
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
			
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'iWant Campaign has been save.');
			
			//LOGS
			$stat='';
			if(isset($prevCampaignID)) { $stat = 'recreate'; }else { $stat = 'add'; }
			$CI->rec_logs->w($campaignID,$campaignName,'iWant Campaign','iWant',$stat);
		
			$this->inform_add_nomimees($campaignID,$campaignName);
			
			redirect('iWantCampaign/iWant/insert_success', 'location', 301);
		}
		elseif($action=="deleteOneItem")
		{
			$this->modules->module_checker(29,'DELETE');
			
			$tables = array(
			  array('tbl'=>'campaign',
					'fld'=>'prevCampaignID'));
		
			if($this->modules->attr($tables,$votingCampaignID)==0)
			{
				//LOGS
				$sql = "SELECT campaignName FROM campaign WHERE id = $votingCampaignID";
				$sql = $this->db->query($sql);
				$sql = $sql->row();
				$CI->rec_logs->w($votingCampaignID,$sql->campaignName,'iWant Campaign','iWant','delete');
				
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
				
				//DELETE RESULT
				$this->c3model->c3crud("no-res",'','','',"DELETE FROM votexRef WHERE campaignID='$votingCampaignID'");
				$this->c3model->c3crud("no-res",'','','',"DELETE FROM iWantResultRef WHERE campaignID='$votingCampaignID'");
				$this->c3model->c3crud("no-res",'','','',"DELETE FROM iWantCampaignNumber_of_commitees_ref WHERE campaignID='$votingCampaignID'");
				$this->c3model->c3crud("no-res",'','','',"DELETE FROM iWantCanvassingRulesRef WHERE campaignID='$votingCampaignID'");
				$this->c3model->c3crud("no-res",'','','',"DELETE FROM iWantVotingRulesRef WHERE campaignID='$votingCampaignID'");
				
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Campaign has been deleted.');
				redirect('iWantCampaign/iWant/delete_success', 'location', 301);
			}else{
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Campaign cannot be delete because it is being link in other campaign.');
			}
			
		}
		elseif($action=="edit" OR $action=="alter")
		{
			$this->modules->module_checker(29,'EDIT NOMINEES');
			//ALTER
			$new_iwan_items="";
			if($action=="alter")
				$data['alter'] = TRUE;
			
			//CHECK IF CAMPAIGN IS ON PROGRESS OR DONE
			$sql = $this->db->query("SELECT status FROM campaign WHERE id = $votingCampaignID LIMIT 1");
			$row = $sql->row();
			$status = $row->status;
			
			/*PUT IT IN iWant_Items_Session*/
			if($status=='on progress' OR $status=='done' OR $status=='failed'){
			$data['edit_items'] = FALSE;
			$sql = $this->db->query("SELECT *, items.id AS itemID, POSM_Type.typeName as POSM_TypeName,(select image from items_images as im where im.itemID=items.id AND defaultStatus=1 limit 0,1) as item_image, countryName 
									 FROM items, POSM_Type, country, price_range  
									 WHERE items.id IN (SELECT itemID FROM campaignItemsXref WHERE campaignID = $votingCampaignID) 
									 AND items.countryID = country.id
									 AND items.POSMTypeID = POSM_Type.id 
									 AND items.price_rangeID  = price_range.id	
									 ORDER BY country.id ASC, POSM_Type.id DESC, price_range.id ASC");
			}else{
			    $data['edit_items'] = TRUE;
				$new_iwan_items = "WHERE iLikeResultRef.campaignID IN (".$this->newest_iWant_items().") ";
				
				$sql = $this->db->query("select *,(select image from items_images as im where im.itemID=i.id AND defaultStatus=1 limit 0,1) as item_image, 
									 i.id as itemID, POSM_Type.typeName as POSM_TypeName, level_name, extra_label 
									 from items as i, POSM_Type, country, price_range  
									 where i.id in (select itemID from iLikeResultRef $new_iwan_items)
									 AND i.id NOT IN (SELECT iWantResultRef.itemID FROM iWantResultRef)
									 AND i.POSMTypeID = POSM_Type.id 
									 AND i.countryID = country.id
									 AND i.price_rangeID  = price_range.id
									 ORDER BY country.id ASC, POSM_Type.id DESC, price_range.id ASC");
			}
			
			$data['items']  = $sql->result_array();			
			$arr=array();
			foreach($data['items'] as $ti)
			{	extract($ti);
				$arr[] = array('level_name'		=>$level_name,
							   'extra_label'	=>$extra_label,
							   'itemID'			=>$itemID,
							   'item_image'		=>$item_image,
							   'itemName'		=>$this->cutStr($itemName),
							   'POSM_TypeName'	=>substr($POSM_TypeName,0,-5),
							   'countryName'	=>$countryName);
			}
			$_SESSION['iWant_items']=$arr;

			 //VOTING RULES
			 if($status=='on progress' OR $status=='done' OR $status=='failed')
			    $data['voting_rules']   = $this->iWantVotingRules('edit',$votingCampaignID);
			 else
				$data['voting_rules']   = $this->iWantVotingRules('add');
			
			//DIV Marketing Items
			$DIV_Marketing_Items = "SELECT items.id as item_ID, (SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, itemName,
			POSM_Type.typeName as POSM_TypeName
			FROM items
			LEFT JOIN POSM_Type ON items.POSMTypeID = POSM_Type.id 
			WHERE  items.countryID = 0 
			AND items.id NOT IN (SELECT iWantResultRef.itemID FROM iWantResultRef)
			ORDER BY POSM_Type.typeName ASC";

			$report    		= $this->db->query($DIV_Marketing_Items);  
			$data['rep']    = $report->result_array(); 
			
			
			if($status=='on progress' OR $status=='done' OR $status=='failed'){
				$data['min_committees'] = $this->min_committees($votingCampaignID);
			}else{
				$data['min_committees'] = $this->min_committees();
			}
		
			$admin_users="";
			$data['id']     = $votingCampaignID;
			$data['vfile']	= 'iWantCampaignFORM.php';
			
			
			//VOTERS
			$sql = $this->db->query("SELECT voters.id as voterID, voters.lname, voters.fname ,voters.gender as vGender, voters.email AS email_address, department, year_of_birth,  Fields001 as countryID, country.countryName as countryName, votingStatus 
									 FROM voters 
									 LEFT JOIN country ON country.id = voters.Fields001  
									 WHERE voters.campaignID = $votingCampaignID $voter_AND
									 ORDER BY country.id ASC ");
			//ADDITIONAL VOTERS
			$admin_users = $sql->result_array();
			if($data['PUBLISH_CAMPAIGN']==TRUE AND ($status=='new' OR $status=='updated'))
				$admin_users = $this->additional_voters($votingCampaignID);
			
			
			$data['admin_users'] = $admin_users;						 
		}
		elseif($action=="add")
		{
			$this->modules->module_checker(29,'ADD');
			$data['edit_items'] = TRUE;
			$data['vfile']	= 'iWantCampaignFORM.php';
			$data['min_committees'] = $this->min_committees();
			
			//STATUS LISTS
			$data['iLikeID'] 	= $votingCampaignID;	
			$new_iwan_items = "";
			$new_iwan_items = "WHERE iLikeResultRef.campaignID IN (".$this->newest_iWant_items().") ";
			$sql = $this->db->query("select *,(select image from items_images as im where im.itemID=i.id AND defaultStatus=1 limit 0,1) as item_image, 
									 i.id as itemID, POSM_Type.typeName as POSM_TypeName, level_name, extra_label 
									 from items as i, POSM_Type, country, price_range  
									 where i.id in(select itemID from iLikeResultRef $new_iwan_items)
									 AND i.id NOT IN (SELECT iWantResultRef.itemID FROM iWantResultRef)
									 AND i.POSMTypeID 	  = POSM_Type.id 
									 AND i.countryID  	  = country.id
									 AND i.price_rangeID  = price_range.id
									 ORDER BY country.id ASC, POSM_Type.id DESC, price_range.id ASC") ;
  
			$data['items'] = $sql->result_array();	

			/*PUT IT IN iWant_Items_Session*/
			$arr=array();
			foreach($data['items'] as $ti)
			{	extract($ti);
				$arr[] = array('level_name'		=>$level_name,
							   'extra_label'	=>$extra_label,
							   'itemID'			=>$itemID,
							   'item_image'		=>$item_image,
							   'itemName'		=>$this->cutStr($itemName),
							   'POSM_TypeName'	=>substr($POSM_TypeName,0,-5),
							   'countryName'	=>$countryName);
			}
			$_SESSION['iWant_items']=$arr;
			
			//VOTING RULES
			$data['voting_rules']   = $this->iWantVotingRules('add');
			
			//IWANT CANVASSING RULES
			$data['canvassing_rules']   = $this->iWantCampaign_Canvassing_Rules();
				
			//DIV Marketing Items
			$sql = "SELECT items.id as item_ID, (SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, itemName,
			POSM_Type.typeName as POSM_TypeName, level_name, extra_label
			FROM items
			LEFT JOIN POSM_Type   ON items.POSMTypeID = POSM_Type.id 
			INNER JOIN price_range ON items.price_rangeID = price_range.id
			WHERE  items.countryID = 0 
			AND items.id NOT IN (SELECT iWantResultRef.itemID FROM iWantResultRef) 
			ORDER BY  POSM_Type.id DESC, price_range.id ASC";

			$report    		= $this->db->query($sql);  
			$data['rep']    = $report->result_array(); 
			
			/*PUT IT IN iWant_Items_Session*/	
			$data['admin_users'] = $this->screening_committeesGroup();
			$data['iWantMode']   = "add";
		}
		elseif($action=="update")
		{
			$this->modules->module_checker(29,'EDIT NOMINEES');
			if($_POST==NULL){
				redirect(HTTP_PATH.'iWantCampaign/iWant', 'location', 301);
				die();
			}
			
			//IF NOT ON PROGRESS
			$allowedToEdit = true;
			if($status == 'on progress')
				$allowedToEdit = false;
			if($status == 'done')
				$allowedToEdit = false;
			
			if($allowedToEdit==true AND $_SESSION['super_admin']!='y'){
			
				isset($campaignName) ? $dbFields['campaignName'] = $campaignName : '';
				$dbFields['campaignType']      = 'iWant';
				$dbFields['DateLastEdited']    = date('Y-m-d');
				isset($DateFrom) ? $dbFields['DateFrom'] = $DateFrom : '';
				isset($DateTo) 	 ? $dbFields['DateTo']   = $DateTo   : '';
				$dbFields['status'] 	       = 'updated';
				$dbFields['adminLastEditorID'] = $_SESSION['user_id'];
				$this->c3model->c3crud("update",$table,$dbFields,$votingCampaignID);
				$campaignID = $votingCampaignID;
				
				//VOTERS
				//DELETE ALL VOTERS FROM ADVANCE INPUTTING
				
				//GET ALL VOTERS FROM campaignVoters and delete
				/*DIVISION MARKETING*/
				if($_SESSION['countryID']==0){
				 $this->deleteAdditionalVoters($campaignID);
				 $campaignVoters  = $this->db->query("SELECT * FROM campaignVotersXref WHERE campaignID=$campaignID");
				 $campaignVoters  = $campaignVoters->result_array();
				
				 foreach($campaignVoters as $cV){
					extract($cV);
					$this->c3model->c3crud("no-res",'','','',"DELETE FROM voters WHERE id=$voterID");
				 }
				 $this->c3model->c3crud("no-res",'','','',"DELETE FROM campaignVotersXref WHERE campaignID='$campaignID'");
				}else{
				 /*BU MARKETING*/
				 $campaignVoters  = $this->db->query("SELECT *, voters.id as voterID FROM voters WHERE campaignID=$campaignID AND Fields001 ='".$_SESSION['countryID']."'");
				 $campaignVoters  = $campaignVoters->result_array();
				
				 foreach($campaignVoters as $cV){
					extract($cV);
					$this->c3model->c3crud("no-res",'','','',"DELETE FROM voters WHERE id=$voterID");
					$this->c3model->c3crud("no-res",'','','',"DELETE FROM campaignVotersXref WHERE voterID='$voterID' AND campaignID=$campaignID");
				 }
				 
				}
				
				//VOTER FROM TABLE
				//print_r($lnames);
				//die();
				if(isset($lnames)) {
					foreach($lnames as $k => $val)
					{	
						$voterFields['lname']  				=  $val;
						$voterFields['fname']  				=  $fnames[$k];
						$voterFields['gender']  			=  $genders[$k];
						$voterFields['email'] 				=  $emails[$k];
						$voterFields['voterTypeID'] 		=  2 ;
						$voterFields['campaignID'] 			=  $campaignID;
						$voterFields['department'] 			=  $departments[$k];
						$voterFields['year_of_birth'] 		=  $years[$k];
						$voterFields['fields001'] 	        =  $countryIDs[$k];
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
			}
		    
			$sql = $this->db->query("SELECT campaignName FROM campaign WHERE id = $campaignID");
			$sql = $sql->row();
			
			//if(isset($Save_and_Notify)) $this->inform_add_nomimees($campaignID,$sql->campaignName);
		   
			$_SESSION['iWant_items'] = '';
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'iWant Campaign has been updated.');
			
			//LOGS
			$CI->rec_logs->w($campaignID,$sql->campaignName,'iWant Campaign','iWant','edit');
			
			redirect('iWantCampaign/iWant/update_success', 'location', 301);
		}
		elseif($action=="page")
		{
			$this->modules->module_checker(29,'REVIEW');
			$pagenum = $votingCampaignID;
			$data['active_page'] = $id; 
			echo $max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		}
		
		$sql2 = $this->db->query("SELECT *, campaign.id AS campaignID  FROM $table 
									 LEFT JOIN admin_users 
									 ON campaign.adminCreatorID = admin_users.id
									 WHERE campaign.campaignType = 'iWant' 
									 ORDER BY campaignID DESC $max");
									
		$data['campaigns']  = $sql2->result_array();
	
	
		$viewer = 'iWantCampaignHeader';
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
	
	function cutStr($itemName=''){
		if(strlen($itemName)>=15)
			return substr($itemName,0,15)."..";
		else	
			return $itemName; 
	}
	
	function resend_Email($campaignID='',$voterID='')
	{
		$sql 		= $this->db->query("SELECT * FROM campaign WHERE id = $campaignID LIMIT 0,1");
		$campaign	= $sql->result_array();
		//PRICE RANGES
		$levels = $this->db->query("SELECT distinct(level_name) as level_name FROM `price_range` ORDER BY id ASC");
		$levels = $levels->result_array();
		
		foreach($campaign as $c)
		{	extract($c);
		
			$voters 	= $this->db->query("SELECT * FROM voters WHERE id = $voterID AND votingStatus = 'invited' LIMIT 0,1");
			$voters		= $voters->result_array();
			
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
				$msg  = "Hello ". $fname ." ". $lname."! <br/><br/>"; 
				$msg .= "Welcome to the SMBIL Regional POSM Project!<br/><br/>"; 
				
				$msg .= "The iWant Campaign is now online and you have been selected as part of the <b>SCREENING COMMITTEE</b>. <br/>"; 
				$msg .= "Please review <b>ALL</b> the items we have prepared for you.  <br/>"; 
				$msg .= "Your choices are important to us  :-)  <br/><br/>";
				
				$msg .= "GUIDELINES: <br/>"; 
				$msg .= "<ul> 
							<li>Please make sure to cast your votes by clicking <b>\"Want\"</b> or <b>\"Not Now\"</b> on all the items.</li>
							<li>You will be asked to vote on <b>ITEMS PER PRICE CATEGORY:</b> </li>
							<li style='list-style: none;'>&nbsp;</li>";
							//LEVELS
							foreach($levels as $level){
							extract($level);
							$msg .= "<li style='list-style: none;margin-left: 25px;'>- <label style='text-transform:uppercase'>$level_name</label> Items</li>";
							}
							
				$msg .=	"<li style='list-style: none;'>&nbsp;</li> <li>Please note that you <b>must</b> reach the <b>\"Thank You\" Page </b> for the system to record your votes. </li>
						 </ul><br/>";
						 
				$msg .= "Voting period is from ".date("M d, Y", strtotime($DateFrom))." to ".date("M d, Y", strtotime($DateTo)).".  <br/><br/>"; 
				
				$msg .= "You may click on the link below to start voting: <br/>";
				$msg .= "<a href='".HTTP_PATH."gallery/iWant/".$this->encode_base64($campaignID) ."/". $this->encode_base64($email)."'>link</a><br/><br/> 
						Having problem with the link? Copy and paste the URL below to your browser's address bar:<br/>".HTTP_PATH."gallery/iWant/".$this->encode_base64($campaignID) ."/". $this->encode_base64($email)."<br/><br/>";
				
				$msg .= "Thank you very much for your participation. <br/>";
				$msg .= "Happy Voting! <br/><br/>";
				$msg .= "This is a follow-up email.<br/><br/>";
			
				
				//SEND EMAIL
				$this->email->clear();
				$this->email->from('do.not.reply@smg.sanmiguel.com.ph', 'San Miguel Beer International');
				$this->email->to($email); 

				$this->email->subject('iWant Campaign Follow Up');
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
	
	/*ALTER CAMPAIGN*/
	function alter($campaignID)
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		
		if($_POST==NULL){
			redirect(HTTP_PATH.'iWantCampaign/iWant', 'location', 301);
			die();
		}
		
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
		$CI->rec_logs->w($campaignID,$campaignName,'iWant Campaign','iWant','alter');
		
		/*SAVE OR READ CAMPAIGN*/
		
		/*SAVE ALL THE NOMINESS*/
		if(isset($lnames)) {
			foreach($lnames as $k => $val)
			{	
				$voterFields['lname']  				=  $val;
				$voterFields['fname']  				=  $fnames[$k];
				$voterFields['gender']  			=  $genders[$k];
				$voterFields['email'] 				=  $emails[$k];
				$voterFields['voterTypeID'] 		=  2 ;
				$voterFields['campaignID'] 			=  $campaignID;
				$voterFields['department'] 			=  $departments[$k];
				$voterFields['year_of_birth'] 		=  $years[$k];
				$voterFields['dateAdded'] 	   		=  date('Y-m-d');
				$voterFields['addedAlter'] 	   		=  'y';
				$voterFields['fields001'] 	        =  $countryIDs[$k];
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
		
		
		//GET ALL THE RESPONDENTS
		$sql					= "SELECT * FROM campaignVotersXref WHERE campaignID = $campaignID";
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
			//CREATE MESSAGE
			$msg  = "";
			$msg  = "Hello ". $row->fname ." ". $row->lname."! <br/><br/>"; 
			$msg .= "Welcome to the SMBIL Regional POSM Project!<br/><br/>"; 
			
			$msg .= "The iWant Campaign is now online and you have been selected as part of the <b>SCREENING COMMITTEE</b>. <br/>"; 
			$msg .= "Please review <b>ALL</b> the items we have prepared for you.  <br/>"; 
			$msg .= "Your choices are important to us  :-)  <br/><br/>";
			
			$msg .= "GUIDELINES: <br/>"; 
			$msg .= "<ul> 
						<li>Please make sure to cast your votes by clicking <b>\"Want\"</b> or <b>\"Not Now\"</b> on all the items.</li>
						<li>You will be asked to vote on <b>ITEMS PER PRICE CATEGORY:</b> </li>
						<li style='list-style: none;'>&nbsp;</li>";
						//LEVELS
						foreach($levels as $level){
						extract($level);
						$msg .= "<li style='list-style: none;margin-left: 25px;'>- <label style='text-transform:uppercase'>$level_name</label> Items</li>";
						}
						
			$msg .=	"<li style='list-style: none;'>&nbsp;</li> <li>Please note that you <b>must</b> reach the <b>\"Thank You\" Page </b> for the system to record your votes. </li>
					 </ul><br/>";
					 
			$msg .= "Voting period is from ".date("M d, Y", strtotime($c->DateFrom))." to ".date("M d, Y", strtotime($c->DateTo)).".  <br/><br/>"; 
			
			$msg .= "You may click on the link below to start voting: <br/>";
			$msg .= "<a href='".HTTP_PATH."gallery/iWant/".$this->encode_base64($campaignID) ."/". $this->encode_base64($row->email)."'>link</a><br/><br/> 
					 Having problem with the link? Copy and paste the URL below to your browser's address bar:<br/>".HTTP_PATH."gallery/iWant/".$this->encode_base64($campaignID) ."/". $this->encode_base64($row->email)."<br/><br/>";
			
			$msg .= "Thank you very much for your participation. <br/>";
			$msg .= "Happy Voting! <br/><br/>";
			$msg .= "This is a follow-up email.<br/><br/>";
			
			//SEND EMAIL
			$this->email->clear();
			$this->email->from('do.not.reply@smbi.com', 'San Miguel Beer International');
			$this->email->to($row->email); 

			$this->email->subject('iWant Campaign');
			$this->email->message($msg);	

			if($this->email->send())
				$this->db->query("UPDATE voters SET email_sent='y' WHERE id = $voterID");
			}
		}
		
		
		//DELETE iLike Result
		$this->c3model->c3crud("no-res",'','','',"DELETE FROM iWantResultRef WHERE campaignID='$campaignID'");
		
		
		//die();
		redirect('iWantCampaign/iWant/', 'location', 301);		
		
	}
	/*ALTER CAMPAIGN*/
	
	/*GENERATE EMAIL TD*/
	function generateEmailTD($fname,$lname,$gender,$email,$department,$year,$countryID,$emailCtr)
	{
	   $emailCtr++;
	   $c = (($emailCtr)%2) == 0 ? "" : "style='background:#f9ebeb;width:100%;height:38px;'"; 
	   $department = str_replace('%20',' ',$department);
	   $fname = str_replace('%20',' ',$fname);
	   $fname = str_replace('%C3%91','Ñ',$fname);
	   $fname = str_replace('%C3%B1','ñ',$fname);
	   $lname = str_replace('%20',' ',$lname);
	   $lname = str_replace('%C3%91','Ñ',$lname);
	   $lname = str_replace('%C3%B1','ñ',$lname);
	   
	   //GET COUNTRY
		$sql = $this->db->query("SELECT countryName FROM country WHERE id=". $countryID . " LIMIT 1");
		$row = $sql->row(); 
		$userCountryName = $row->countryName;
	 
	echo "<div id='emailCtr".$emailCtr."' $c class='emailClass' style='width:100%'>
		 <table>
			<tr>
				<td style='padding: 0 0 0 15px;width: 103px;'> 
				   <input type='hidden' name='voterTypes[]' 	value='2'>
				   <input style='width:90%;margin-bottom: 0;' type='text' value='$fname' 	    	name='fnames[]'       readonly='readonly'> 
				</td>
				<td style='padding:3px;'><input type='text' style='width:114%;margin-bottom: 0;'    value='$lname' 	    	name='lnames[]'   readonly='readonly'></td>
				<td style='padding:3px;text-align: right;width: 117px;'>
					<input type='text' style='width:59%;margin-bottom: 0;' value='$gender' 	    	name='genders[]'       readonly='readonly'>
				</td>
				<td style='padding:3px;width: 145px;'><input type='text' style='width:92%;margin-bottom: 0;' value='$department'  	name='departments[]' readonly='readonly'> </td> 
				<td style='padding:3px;width: 253px;'><input type='text' style='width:110%;margin-bottom: 0;' value='$email'  		    class='emails'   name='emails[]' 	 readonly='readonly'> </td> 
				<td style='padding:3px;text-align: right;'><input type='text' style='width:51%;margin-bottom: 0;' value='$year'  			name='years[]' 		 readonly='readonly'> </td> 
				<td style='padding:3px;'><input type='text' style='width:89px;margin-bottom: 0;'value='$userCountryName' 	    		name='countryName[]' 		  	 readonly='readonly'> 
				<input type='hidden' name='countryIDs[]' value='$countryID' class='countryID'></td>  
				<td style='padding:3px;text-align: center;'>
				<img onclick='removeEmail(\"emailCtr".$emailCtr."\")' style='margin: 0px 18px 0px 9px;padding-top: 4px;cursor:pointer' src='".HTTP_PATH."img/delete.png' title='Delete' class='fl'> 
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
	
	function potentialItems($fieldID='',$price_rangeID='')
	{
		//GET PRICE RANGE NAME
		$price_rangeName = "";
		$query 		= $this->db->query("SELECT level_name as price_rangeName FROM price_range WHERE id=$price_rangeID LIMIT 0,1");
		$row 		= $query->row();
		$price_rangeName = $row->price_rangeName;
		
		$sql = $this->db->query("SELECT typeName FROM POSM_Type WHERE id = $fieldID LIMIT 0,1");
		$row = $sql->row();
		
		//FIND HOW MANY ITEMS
		$items=0;
		foreach($_SESSION['iWant_items'] as $d){
		extract($d);
		if($POSM_TypeName== (substr($row->typeName,0,-5)) AND $price_rangeName==$level_name)
		 $items++;
		}
		
		return $items;
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
			redirect(HTTP_PATH.'iWantCampaign/iWant', 'location', 301);
			die();
		}
		
		$this->Delete_screening_committeesGroup();
		
		$currentDate = date("Y-m-d");
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		
		$filter_WHERE = $this->modules->country_iwant();
		$table	= 'campaign';
	
		extract($_POST);
		/*SAVE OR READ CAMPAIGN*/
		//FROM iLIKE TO iWANT
		if(isset($iLikeID) OR isset($prevCampaignID)){
			//CAMPAIGN
			$dbFields['campaignName']  = $campaignName;
			$dbFields['campaignType']  = 'iWant';
			$dbFields['DateAdded'] 	   = date('Y-m-d');
			$dbFields['DatePublished'] = date('Y-m-d');
			$dbFields['DateFrom'] 	   = $DateFrom;
			$dbFields['DateTo'] 	   = $DateTo;
			$dbFields['status'] 	   = 'new';
			$dbFields['adminCreatorID']= $_SESSION['user_id'];
			$dbFields['countryID']	   = $_SESSION['countryID'];
			
			if(isset($iLikeID)){
				$dbFields['prevCampaignID']= $iLikeID;
			}
			//REVOTE
			if(isset($prevCampaignID)){
				$dbFields['prevCampaignID']	   = isset($prevCampaignID) ? $prevCampaignID : 0;
			}
			
			$res  = $this->c3model->c3crud("insert",$table,$dbFields,'');
			
			//MAX ID CAMPAIGN
			$sql		= "SELECT max(id) as max_id FROM $table";
			$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
			$campaignID = $lastID[0]['max_id'];
			
			//LOGS
			if(isset($prevCampaignID)) { $stat = 'recreate'; }else { $stat = 'add'; }
			$CI->rec_logs->w($campaignID,$campaignName,'iWant Campaign','iWant',$stat);
		}
		/*SAVE OR READ CAMPAIGN*/
		
		//DELETE PREVIOUSLY ADDED VOTERS
		$this->deleteAdditionalVoters($campaignID);
		
		/*SAVE ALL THE NOMINESS*/
		//GET ALL VOTERS FROM campaignVoters and delete
		$campaignVoters  = $this->db->query("SELECT * FROM campaignVotersXref WHERE campaignID=$campaignID");
		$campaignVoters  = $campaignVoters->result_array();
		
		foreach($campaignVoters as $cV)
		{
			extract($cV);
			$this->c3model->c3crud("no-res",'','','',"DELETE FROM voters WHERE id=$voterID");
		}
		$this->c3model->c3crud("no-res",'','','',"DELETE FROM campaignVotersXref WHERE campaignID='$campaignID'");
		
		if(isset($lnames)) {
			foreach($lnames as $k => $val)
			{	
				$voterFields['lname']  				=  $val;
				$voterFields['fname']  				=  $fnames[$k];
				$voterFields['gender']  			=  $genders[$k];
				$voterFields['email'] 				=  $emails[$k];
				$voterFields['voterTypeID'] 		=  2 ;
				$voterFields['campaignID'] 			=  $campaignID;
				$voterFields['department'] 			=  $departments[$k];
				$voterFields['year_of_birth'] 		=  $years[$k];
				$voterFields['dateAdded'] 	   		= date('Y-m-d');
				$voterFields['fields001'] 	        =  $countryIDs[$k];
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
        

		/*GET MINIMUM NUMBER OF COMMITEES*/
		$committee_Fields['num_commitee'] = $this->min_committees();
		$committee_Fields['campaignID']   = $campaignID;
		$res = $this->c3model->c3crud("insert","iWantCampaignNumber_of_commitees_ref",$committee_Fields,'');
		/*GET MINIMUM NUMBER OF COMMITEES*/
		
		
		/*iWantCanvassingRules  to ref*/
		$sql		        = "SELECT * from iWantCanvassingRules  where countryID=0";
		$iwantCanvassing 	= $this->c3model->c3crud("select",'','','',$sql);

		foreach($iwantCanvassing  as $r)
		{
		   extract($r);
		   //PERCENTAGE
		   $actual_val = $val;
		   if(strpos($val,".")==TRUE){
			$val = $val * $this->campaignVoters($campaignID);
			$val = round($val);
		   }
		   $field['price_rangeID'] 	  = $price_rangeID;
		   $field['fieldName'] 	  	  = $fieldName;
		   $field['fieldID'] 	  	  = $fieldID;
		   $field['campaignID'] 	  = $campaignID;
		   $field['countryID']  	  = 0;
		   $field['rel']        	  = $rel;
		   $field['lrel']       	  = $lrel;
		   $field['val']        	  = $val;
		   $field['cond1']   	  	  =  $cond1;
		   $field['min_val']   	  	  =  $min_val;
		   $field['logical_operator'] =  $logical_operator;
		   $field['cond2']   		  =  $cond2;
		   $field['max_val']   		  =  $max_val;
		   //$field['actual_input']  	  = $actual_val;
		   $res = $this->c3model->c3crud("insert",'iWantCanvassingRulesRef',$field,'');
		}
		
		//ITEMS
		$this->c3model->c3crud("no-res",'','','',"DELETE FROM campaignItemsXref WHERE campaignID=$campaignID");
			
		foreach($_SESSION['iWant_items'] as $iw){
			extract($iw);
			$refFields['campaignID'] = $campaignID;
			$refFields['itemID']     = $itemID;
			$res = $this->c3model->c3crud("insert",'campaignItemsXref',$refFields,'');
			
			//RECORD LOG IN
			$SQL = $this->db->query("SELECT id, itemCode, itemName FROM items WHERE id= $itemID");
			$row = $SQL->row();
			$CI->rec_logs->w($row->id, $row->itemName, "$campaignName",'Campaign Items','add', $row->itemCode);
		}
		
		$sqlVotinRules   = "SELECT * FROM  `iWantVotingRules` WHERE iWantVotingRules.countryID = 0";
		$VotingRules     = $this->db->query($sqlVotinRules);
		$VotingRules     = $VotingRules->result_array();
		foreach($VotingRules as $vr)
		{
			extract($vr);
			//PERCENTAGE
			$actual_val = $val;
			/*
			if(strpos($val,".")==TRUE){
			 $val = $val * $this->potentialItems($fieldID,$price_rangeID);
			 $val = round($val);
			}*/
			
			$votingRulesFields['price_rangeID']    = $price_rangeID;
			//SELECT LABEL IN PRICE RANGE REF
			$sql = $this->db->query("SELECT extra_label FROM price_range WHERE id = $price_rangeID LIMIT 0,1");
			$sql = $sql->row();
			$votingRulesFields['label']		   	   = $sql->extra_label;
			$votingRulesFields['fieldName']    	   = $fieldName;
			$votingRulesFields['fieldID'] 	   	   = $fieldID;
			$votingRulesFields['rel'] 	       	   = $rel;
			$votingRulesFields['val'] 		   	   = $val;
			$votingRulesFields['cond1']   	  	   =  $cond1;
		    $votingRulesFields['min_val']   	   =  $min_val;
		    $votingRulesFields['logical_operator'] =  $logical_operator;
		    $votingRulesFields['cond2']   		   =  $cond2;
		    $votingRulesFields['max_val']   	   =  $max_val;
			//$votingRulesFields['val'] 		   	   = $actual_val;
			//$votingRulesFields['actual_input'] 	   = $actual_val;
			$votingRulesFields['countryID']    	   = 0;
			$votingRulesFields['campaignID']   	   = $campaignID;
			$votingRulesFields['dateAdded']    	   = date('Y-m-d');
			$res = $this->c3model->c3crud("insert",'iWantVotingRulesRef',$votingRulesFields,'');
		}
		
		
		//GET ALL THE RESPONDENTS
		$sql					= "SELECT * FROM campaignVotersXref WHERE campaignID = $campaignID";
		$campaignVotersXref 	= $this->db->query($sql);
		$campaignVotersXref		= $campaignVotersXref->result_array();
		
	
		//GET THE CAMPAIGN NAME
		$query 	= $this->db->query("SELECT * FROM campaign WHERE id = $campaignID LIMIT 1");
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
		//PRICE RANGES
		$levels = $this->db->query("SELECT distinct(level_name) as level_name FROM `price_range` ORDER BY id ASC");
		$levels = $levels->result_array();
		
		foreach($campaignVotersXref as $cVxref)
		{
			extract($cVxref);
			$query 	= $this->db->query("SELECT * FROM voters WHERE id = $voterID LIMIT 1");
			$row 	= $query->row();
			
			//CREATE MESSAGE
			$msg  = "";
			$msg  = "Hello ". $row->fname ." ". $row->lname."! <br/><br/>"; 
			$msg .= "Welcome to the SMBIL Regional POSM Project!<br/><br/>"; 
			
			$msg .= "The iWant Campaign is now online and you have been selected as part of the <b>SCREENING COMMITTEE</b>. <br/>"; 
			$msg .= "Please review <b>ALL</b> the items we have prepared for you.  <br/>"; 
			$msg .= "Your choices are important to us  :-)  <br/><br/>";
			
			$msg .= "GUIDELINES: <br/>"; 
			$msg .= "<ul> 
						<li>Please make sure to cast your votes by clicking <b>\"Want\"</b> or <b>\"Not Now\"</b> on all the items.</li>
						<li>You will be asked to vote on <b>ITEMS PER PRICE CATEGORY:</b> </li>
						<li style='list-style: none;'>&nbsp;</li>";
						//LEVELS
						foreach($levels as $level){
						extract($level);
						$msg .= "<li style='list-style: none;margin-left: 25px;'>- <label style='text-transform:uppercase'>$level_name</label> Items</li>";
						}
						
			$msg .=	"<li style='list-style: none;'>&nbsp;</li> <li>Please note that you <b>must</b> reach the <b>\"Thank You\" Page </b> for the system to record your votes. </li>
					 </ul><br/>";
					 
			$msg .= "Voting period is from ".date("M d, Y", strtotime($c->DateFrom))." to ".date("M d, Y", strtotime($c->DateTo)).".  <br/><br/>"; 
			
			$msg .= "You may click on the link below to start voting: <br/>";
			$msg .= "<a href='".HTTP_PATH."gallery/iWant/".$this->encode_base64($campaignID) ."/". $this->encode_base64($row->email)."'>link</a><br/><br/> 
					Having problem with the link? Copy and paste the URL below to your browser's address bar:<br/>".HTTP_PATH."gallery/iWant/".$this->encode_base64($campaignID) ."/". $this->encode_base64($row->email)."<br/><br/>";
			
			$msg .= "Thank you very much for your participation. <br/>";
			$msg .= "Happy Voting! <br/><br/>";
		
			
			//SEND EMAIL
			$this->email->clear();
			$this->email->from('do.not.reply@smbi.com', 'San Miguel Beer International');
			$this->email->to($row->email); 

			$this->email->subject('iWant Campaign');
			$this->email->message($msg);	
			
			if($currentDate==$DateFrom){
				if($this->email->send())
					$this->db->query("UPDATE voters SET email_sent='y' WHERE id = $voterID");
			}
		}
		
		//ON PROGRESS CAMPAIGN
		$dbFields['status'] 	   = 'on progress';
		$dbFields['DatePublished'] = date('Y-m-d');
		$this->c3model->c3crud("update","campaign",$dbFields,$campaignID);		
		
		//LOGS
		$CI->rec_logs->w($campaignID,$campaignName,'iWant Campaign','iWant','published');
		
		//echo $this->email->print_debugger();
		
		redirect('iWantCampaign/iWant/', 'location', 301);
	}
	
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
		 $sql = "select * from voters where email='$email' AND votingStatus='done'  order by dateAdded desc limit 0,1";
	 
		 $e     = $this->db->query($sql);
		 $voter     = $e->row(); 
		if($e->num_rows()>0)
		   {
		     $dateAdded = $voter->dateAdded;
		      
		     $sql       = $this->db->query("select  datediff( '$dateAdded','$cur_date') as dd");
		     $date      = $sql->row();
			 
		     if( $date ->dd < 365) echo  "existing";
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
	
	/*iLikeItem Form*/
	function pushItem($itemID)
	{
		$_SESSION['iWant_Items'] .= $itemID."|";
		
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
			$sql = $this->db->query("SELECT image FROM items_images WHERE id = (SELECT MAX(ID) FROM items_images WHERE itemID = $itemID) LIMIT 1");
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
		$items = explode('|',$_SESSION['iWant_Items']);
			
		$j=0;
		$newSession = '';
		for($i=0;$i<(count($items)-1);$i++)
		{
			if($items[$i] != $itemID)
			{
				$newSession .= $items[$i]."|";
			}
		}
		
		$_SESSION['iWant_Items'] = '';
		$_SESSION['iWant_Items'] = $newSession;
		
		
		//EXPLODE ITEM
		
		$items2 = explode('|',$_SESSION['iWant_Items']);
		
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
					$sql = $this->db->query("SELECT image FROM items_images WHERE id = (SELECT MAX(ID) FROM items_images WHERE itemID = $itemID) LIMIT 1");
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
		$items = explode('|',$_SESSION['iWant_Items']);
		
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
		$_SESSION['iWant_Items'] = '';
		echo "clear";
	}
	
	function test($id)
	{
	 echo $this->decode_base64($id);
	}
	
} ?>