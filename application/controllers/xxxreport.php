<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report extends CI_Controller {
 
public function __construct()
   {
		parent::__construct();
		date_default_timezone_set('UTC');
		session_start();
		$this->load->model('c3model');
		$this->load->library('modules');
		$this->load->helper('file');
		$this->load->helper('download');
		$this->output->enable_profiler(FALSE);
		$this->modules->session_handler();
   }

    public function index()
	{			
	   $this->modules->module_checker(36,'REVIEW');
	   
	   $data['vfile']		= 'reportsSubMenu.php';
	   $data['title']		= 'San Miguel Brewing International';
	   $data['page_title']	= 'Reports';
	   $data['meta_description']	= 'San Miguel Brewing International';
	   $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';

	    $sqlSTr1="SELECT *,
				POSM_Type.typeName as POSM_Type_Name, 
				items.id as itemID ,
				(select image from items_images where itemID = items.id  AND defaultStatus=1 limit 0,1) as img
				FROM items 
				LEFT JOIN POSM_Type ON items.POSMTypeID = POSM_Type.id 
				WHERE items.brandID IN 
				(SELECT brandID FROM commonGalleryBrands) 
				AND publish !='n' AND items.POSMTypeID = (SELECT item_typeID FROM featured_items WHERE position='left') ORDER BY RAND() limit 3,3";
				
		$sqlSTr2="SELECT *,
				POSM_Type.typeName as POSM_Type_Name, 
				items.id as itemID,(select image from items_images where itemID = items.id AND defaultStatus=1 limit 0,1 ) as img
				FROM items 
				LEFT JOIN POSM_Type ON items.POSMTypeID = POSM_Type.id 
				WHERE items.brandID IN 
				(SELECT brandID FROM commonGalleryBrands) 
				AND publish !='n' AND items.POSMTypeID = (SELECT item_typeID FROM featured_items WHERE position='right') ORDER BY RAND()  limit 0,3 ";
				
	   $data['featured1']       = $this->c3model->c3crud("select",'','','',$sqlSTr1); 
	   $data['featured2']       = $this->c3model->c3crud("select",'','','',$sqlSTr2); 
	   
	   $HTTP_PATH = HTTP_PATH;
	   $data['breadCrumbs']			= '<a href='.$HTTP_PATH.'report>Analytics </a>';
	   
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
   
	function system_activities()
    {
	$this->modules->module_checker(46,'REVIEW');
	
	//USER MANUAL
	$data['USER_MANUAL'] = $this->modules->user_manual(44);
	
	$WHERE = "";
	if($_POST!=NULL){
		$dateFrom = $_POST['DateFrom'];
		$dateTo   = $_POST['DateTo'];
		$WHERE   = "logs.tdate >= '$dateFrom' AND logs.tdate <= '$dateTo'";
	}else{
		$dateFrom = date('Y-m-d');
		$dateTo   = date('Y-m-d');
		$WHERE   = "logs.tdate = '$dateFrom'";
	}
	$data['DateFrom'] = $dateFrom;
	$data['DateTo']   = $dateTo;
	
	//COUNTRY
	$sql = "SELECT country.id as c_id FROM country";
	$countries = $this->db->query($sql);
	$countries = $countries->result_array();
	
	$actions = array(array('action'=>'add'),
			   array('action'=>'edit'),
			   array('action'=>'delete'),
			   array('action'=>'published'),
			   array('action'=>'recreate'),
			   array('action'=>'revote')
			   );
			   
	$pCountry = "";
	
	//MODULES
	$sql 	  = "SELECT distinct(module_name) as dModule FROM logs";
	$dModules = $this->db->query($sql);
	$dModules = $dModules->result_array();
	
	//print_r($dModules);
	
	$m = "";
	$msg = "";
	foreach($countries as $c)
	{	extract($c);
		
		foreach($dModules as $d)
		{	extract($d);
			
			foreach($actions as $a)
			{	extract($a);
				$sql = "SELECT rec_id, action, rec_name, 
						module_name, table_name, country.countryName as cName, 
						admin_users.full_name as fullName, ttime, tdate,
						itemCode
						FROM logs 
						INNER JOIN country 	   ON country.id 	 = logs.country_id  
						INNER JOIN admin_users ON admin_users.id = logs.user_id
						WHERE  $WHERE
						AND country_id= $c_id AND action='$action' AND module_name='$dModule' 
						GROUP BY rec_id 
						ORDER BY module_name ASC, tdate DESC";
				$records = $this->db->query($sql);		
				$records = $records->result_array();
				
				//print_r($records);
				if(count($records)>0)
				{	extract($records);
					
					$c = "";	
					foreach($records as $r)
					{extract($r);
						
						//COUNTYR NAME
						if(($c=="" OR $c != $cName) AND $pCountry!=$cName){
							$msg .= "<label style='font-size:16px;font-family:Arial,Helvetica,sans-serif;color:#777777;'> <b>Country Name: $cName </b></label>";
							$c    = $cName;
						}
					
						$pCountry = $cName;
					}
					
					$msg .="<table style='font-size:12px;font-family:Arial,Helvetica,sans-serif;color:#777777;border-color: gray;'>
							<tr> 
								<td style='width:120px;color:black;font-weight: bold;background:#FCD9D9;text-align:center;'> Table 	 	 	 </td>
								<td style='width:120px;color:black;font-weight: bold;background:#FCD9D9;text-align:center;'> Action   		 </td>
								<td style='width:120px;color:black;font-weight: bold;background:#FCD9D9;text-align:center;'> Record ID	     </td>
								<td style='width:120px;color:black;font-weight: bold;background:#FCD9D9;text-align:center;'> Item Code	     </td>
								<td style='width:300px;color:black;font-weight: bold;background:#FCD9D9;text-align:center;'> Record Name  	 </td>
								<td style='width:220px;color:black;font-weight: bold;background:#FCD9D9;text-align:center;'> User  Name   	 </td>
								<td style='width:100px;color:black;font-weight: bold;background:#FCD9D9;text-align:center;'> Date		   	 </td>
								<td style='width:100px;color:black;font-weight: bold;background:#FCD9D9;text-align:center;'> Time		   	 </td>
							</tr>";
					
					$x=0;
					foreach($records as $r)
					{
					$cls = (($x++)%2) != 0 ? "style='background:#f9ebeb'" :  ""; 
					extract($r);
					$msg .= "<tr> 
								<td $cls> $table_name  	  </td> 
								<td $cls> $action  	  	  </td> 
								<td $cls> $rec_id  	  	  </td> 
								<td $cls> $itemCode  	  </td> 
								<td $cls> $rec_name 	  </td>
								<td $cls> $fullName 	  </td> 
								<td $cls> $tdate 	  	  </td> 
								<td $cls> $ttime 	  	  </td> 
							</tr> ";
					}
					
					$msg .= "</table><br/>";
					
				}
			}
		}
	}
    
	$data['logs']			= $msg;

    $data['vfile']			= 'system_activities.php';
	$data['title']			= 'System Logs';
	
	$data['breadCrumbs']	 = '<a href='.HTTP_PATH.'report>Analytics </a>';
	$data['breadCrumbs']	.= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	$data['breadCrumbs']	.= '<a href='.HTTP_PATH.'report/system_activities> System Activities </a>';
	 

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
	
    function logs()
    {
	$this->modules->module_checker(46,'REVIEW');
	
	//USER MANUAL
	$data['USER_MANUAL'] = $this->modules->user_manual(43);
	
	$filter = "WHERE ";
	if($_SESSION['super_admin']!='y' AND $_SESSION['countryID']!=0)
		$filter = " country_id = ".$_SESSION['countryID']." AND ";
	

	if($_POST!=NULL){
		$dateFrom = $_POST['DateFrom'];
		$dateTo   = $_POST['DateTo'];
		$filter   .= "logs.tdate >= '$dateFrom' AND logs.tdate <= '$dateTo' AND";
	}else{
		$dateFrom = date('Y-m-d');
		$dateTo   = date('Y-m-d');
		$filter   .= "logs.tdate = '$dateFrom' AND";
	}
	
	$filter = substr($filter, 0,-3);
	
	$data['DateFrom'] = $dateFrom;
	$data['DateTo']   = $dateTo;
	
	/*LIMIT*/
	$limit =isset($_POST['selpage'])? $_POST['selpage']:0;
	
	$query = $this->db->query("SELECT count(*) as tot FROM logs $filter");
	$total = $query->result_array();
	$data['totrec'] = $total[0]['tot'];
	
	$data['limit']=$limit;
	
	/*LIMIT*/
	$sql = "SELECT *, logs.id as lID, admin_users.full_name as fullname, country.countryName as cName  
			FROM logs 
			LEFT JOIN admin_users ON admin_users.id = logs.user_id 
			LEFT JOIN country ON country.id 		= logs.country_id
			$filter ORDER BY lID DESC LIMIT $limit,20";

	$report    = $this->db->query($sql);  
	$logs      = $report->result_array(); 
 
    $data['vfile']			= 'logs.php';
	$data['title']			= 'System Logs';
	$data['logs']			= $logs;
	$data['breadCrumbs']	 = '<a href='.HTTP_PATH.'report>Analytics </a>';
	$data['breadCrumbs']	.= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	$data['breadCrumbs']	.= '<a href='.HTTP_PATH.'report/logs> System Logs </a>';
	 

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
   
	public function partial($id)
    {
    $sql = "SELECT itemID, itm.itemName, itm.itemCode as iCode, campaignID,
		   (SELECT COUNT( id ) FROM votexRef AS vref WHERE vref.itemID = itemREF.itemID AND vote =  'yes' and vref.campaignID =$id 
		   AND vref.voterID IN (SELECT voters.id FROM voters WHERE voters.id = vref.voterID AND votingStatus = 'done')) AS voteTot,
		   (select typeName from POSM_Type as pt where pt.id=i.POSMTypeID) as ptype, 
		   (SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = i.id) as item_image
		   FROM  `campaignItemsXref` AS itemREF LEFT JOIN items AS i ON itemREF.itemID = i.id  left join items as itm on itemREF.itemID=itm.id where   itemREF.campaignID=$id 
           ORDER BY `voteTot` DESC";

	 $report    = $this->db->query($sql);  
	 $rep       = $report->result_array(); 
	 
	 
	 $sql       = "select *,full_name from campaign as c inner join admin_users as u on c.adminCreatorID=u.id where c.id='$id'   ";
	 $header    = $this->db->query($sql);  
	 $header    = $header->result_array(); 
	 
	 $sql   		  = "SELECT * FROM voters WHERE campaignID = $id"; 
	 $voters    	  = $this->db->query($sql);  
	 $data['voters']  = $voters->result_array();
	 
	 //CAMPAIGN REF
	 $sql   		  = "SELECT * FROM iLike_Rules_Ref WHERE campaignID = $id"; 
	 $voters    	  = $this->db->query($sql);  
	 $data['iLike_Rules_Ref']  = $voters->result_array();
	 
	 $sql   		  = "SELECT * FROM iLike_Rules_No_Committes_Ref WHERE campaignID = $id"; 
	 $voters    	  = $this->db->query($sql);  
	 $data['iLike_Rules_No_Committes_Ref']  = $voters->result_array();
	 
	 $sql   		  = "SELECT * FROM iLikeCanvassingRulesXref WHERE campaignID = $id"; 
	 $voters    	  = $this->db->query($sql);  
	 $data['iLikeCanvassingRulesXref']  = $voters->result_array();
	 
	 $sql   		  = "SELECT * FROM iLikeVotingRulesRef WHERE campaignID = $id"; 
	 $voters    	  = $this->db->query($sql);  
	 $data['iLikeVotingRulesRef']  = $voters->result_array();
	 
	 $sql   = "SELECT * FROM iLikeVotingRulesRef WHERE campaignID = $id ORDER BY fieldID DESC"; 
	 $sql   = $this->db->query($sql); 
	 $iLikeVotingRulesRef = $sql->result_array();
	 
	 $VotingRules[]= array('table'=>'',
							'fieldName'=>'', 
							'fieldID'=>'', 
							'fieldValue'=>'', 
							'relation'=>'',
							'value'=>''
							);
	 foreach($iLikeVotingRulesRef as $iW)
	 {extract($iW);
		switch($fieldName){
			case("POSMTypeID"):
				$tableName	= 'ITEM TYPE';
				$fieldName  = 'typeName';
				$table 		= 'POSM_Type';
			break;
			case("POSMStatusID"):
				$tableName	= 'ITEM STATUS';
				$fieldName  = 'statusName';
				$table 		= 'POSM_Status';
			break;
			case("OUTLETStatusID"):
				$tableName	= 'OUTLET STATUS';
				$fieldName  = 'statusName';
				$table 		= 'OUTLET_Status';
			break;
			case("PremiumTypeID"):
				$tableName	= 'PREMIUM TYPE NAME';
				$fieldName  = 'premiumTypeName';
				$table 		= 'premiumItemType';
			break;
			case("MaterialTypeID"):
				$tableName	= 'MATERIAL TYPE';;
				$fieldName  = 'materialName';
				$table 		= 'MATERIAL_Type';
			break;
			case("brandID"):
				$tableName	= 'BRAND NAME';
				$fieldName  = 'brandName';
				$table 		= 'brands';
			break;
		}
		
		$query 		= $this->db->query("SELECT $fieldName FROM $table WHERE id=$fieldID LIMIT 1");
		$row 		= $query->row();
		$name_Field = $row->$fieldName;
		
		$VotingRules[] = array('table'=>$tableName,
							'fieldName'=>$name_Field, 
							'fieldID'=>$fieldID, 
							'fieldValue'=>$name_Field, 
							'relation'=>$rel,
							'value'=>$val
							);
	}
	 
	 $data['iLikeVotingRulesRef']  = $VotingRules;
	 
     $data['vfile']			= 'partialRep.php';
	 $data['title']			= 'iLike Report';
	 $data['rep']			= $rep;
	 $data['repHeader']		= $header;
	 $data['breadCrumbs']	= '<a href='.HTTP_PATH.'iLikeCampaign/votingCampaign> iLike Campaign </a>';
	 $data['cID']			= $id;
	 
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
    
	function vote_items($ctype='',$cID='',$vID)
	{
		if($ctype=='iLike')
		{
			$sql = "SELECT itemID, itemName, itemCode, (SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id LIMIT 0,1) as item_image, 
					POSM_Type.typeName as ptype  FROM votexRef 
					LEFT JOIN items     ON items.id = votexRef.itemID
					LEFT JOIN POSM_Type ON items.POSMTypeID = POSM_Type.id
					WHERE campaignID=$cID AND voterID = $vID AND vote='yes';
					";
			
			$sql   = $this->db->query($sql);
			$items = $sql->result_array();		
			echo "<table id='large' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:12px;' class='iLike_Result_Table tablesorter'>
				<thead>
				<tr>
					<th style='width:10px;text-align:center;color:white;padding:0px' bgcolor='#bb1d1d'>   					   <b>No 		 		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Item Code  	  	  </b></th> 
					<th style='width:19px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Image   	 	      </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Name  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Type  	  		  </b></th> 
				</tr>
				</thead>
				<tbody>";
				 
						$x = 0;			
						$y=1;
						$z=1;
						$total=0;
						$total_target=0;
						foreach($items as $r) { 
						extract($r);
						$ptype = ($ptype=='') 	   ? 'Uncategorize' : $ptype;
						$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 	
			echo 	"<tr>
					  <td $c>													$x      																		</td>
					  <td $c>													$itemCode      																	</td>
					  <td $c style='text-align:center;'>			    	<img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 	</td>
					  <td $c style='text-align:left;padding-left:5px;'>	<a href='".HTTP_PATH."gallery/itemInfo2/$itemID' target='_blank'>		$itemName</a>  </td>
					  <td $c style='text-align:left;padding-left:5px;'>	$ptype											</td>
					</tr>";}
			echo	"</tbody>";
						if(!$items)
							echo "<tr><td colspan='7'>No match found.</td></tr>";
			echo	"</table>";
		}elseif($ctype=='iWant')
		{
			$sql = "SELECT itemID, itemName, itemCode, (SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id LIMIT 0,1) as item_image, 
					POSM_Type.typeName as ptype  FROM votexRef 
					LEFT JOIN items     ON items.id = votexRef.itemID
					LEFT JOIN POSM_Type ON items.POSMTypeID = POSM_Type.id
					WHERE campaignID=$cID AND voterID = $vID AND vote='yes';
					";

			$sql   = $this->db->query($sql);
			$items = $sql->result_array();		
			echo "<table id='large' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:12px;' class='iLike_Result_Table tablesorter'>
				<thead>
				<tr>
					<th style='width:10px;text-align:center;color:white;padding:0px' bgcolor='#bb1d1d'>   					   <b>No 		 		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Item Code  	  	  </b></th> 
					<th style='width:19px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Image   	 	      </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Name  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Type  	  		  </b></th> 
				</tr>
				</thead>
				<tbody>";
				 
						$x = 0;			
						$y=1;
						$z=1;
						$total=0;
						$total_target=0;
						foreach($items as $r) { 
						extract($r);
						$ptype = ($ptype=='') 	   ? 'Uncategorize' : $ptype;
						$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 	
			echo 	"<tr>
					  <td $c>											$x      																		</td>
					  <td $c>											$itemCode      																	</td>
					  <td $c style='text-align:center;'>			    <img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 	</td>
					  <td $c style='text-align:left;padding-left:5px;'>	<a href='".HTTP_PATH."gallery/itemInfo2/$itemID' target='_blank'>		$itemName</a>  </td>
					  <td $c style='text-align:left;padding-left:5px;'>	$ptype											</td>
					</tr>";}
			echo	"</tbody>";
						if(!$items)
							echo "<tr><td colspan='7'>No match found.</td></tr>";
			echo	"</table>";
		}
	}
	
	function iWantPartial($id)
    {
	 $voter_AND="";
	 if($_SESSION['super_admin']!='y' AND $_SESSION['countryID']!=0){
			$voter_AND = " AND voters.Fields001 = ".$_SESSION['countryID'];
	 }
	$data['PUBLISH_CAMPAIGN'] =  $this->modules->crud_checker(29,'PUBLISH CAMPAIGN');
	
		
	 $sql = "SELECT itemID, itm.itemName, itm.itemCode as iCode, campaignID,
			(SELECT COUNT( id ) FROM votexRef AS vref WHERE vref.itemID = itemREF.itemID AND vote =  'yes' and vref.campaignID =$id 
			AND vref.voterID IN (SELECT voters.id FROM voters WHERE voters.id = vref.voterID AND votingStatus = 'done')) AS voteTot,
		   (select typeName from POSM_Type as pt where pt.id=i.POSMTypeID) as ptype, 
		   (SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = i.id) as item_image
		   FROM  `campaignItemsXref` AS itemREF LEFT JOIN items AS i ON itemREF.itemID = i.id  left join items as itm on itemREF.itemID=itm.id where   itemREF.campaignID=$id 
           ORDER BY `voteTot` DESC";

	 $report    = $this->db->query($sql);  
	 $rep       = $report->result_array(); 
 
	 
	 $sql       = "select *,full_name from campaign as c inner join admin_users as u on c.adminCreatorID=u.id where c.id='$id'   ";
	 $header    = $this->db->query($sql);  
	 $header    = $header->result_array(); 
	 
	 $sql   		  = "SELECT *, voters.id as voterID FROM voters 
						LEFT JOIN country ON voters.Fields001 = country.id 
						where campaignID = $id $voter_AND ORDER BY country.id ASC"; 
	 $voters    	  = $this->db->query($sql);  
	 $data['voters']  = $voters->result_array();
	 
	 //CAMPAIGN REF
	 $sql   		  = "SELECT * FROM iWantCampaignNumber_of_commitees_ref WHERE campaignID = $id"; 
	 $voters    	  = $this->db->query($sql);  
	 $data['iWantCampaignNumber_of_commitees_ref']  = $voters->result_array();
	 
	 $sql   		  = "SELECT * FROM iWantCanvassingRulesRef WHERE campaignID = $id"; 
	 $voters    	  = $this->db->query($sql);  
	 $data['iWantCanvassingRulesRef']  = $voters->result_array();
	
	 $sql   = "SELECT * FROM iWantVotingRulesRef WHERE campaignID = $id ORDER BY fieldID DESC"; 
	 $sql   = $this->db->query($sql); 
	 $iWantVotingRulesRef = $sql->result_array();
	 
	  $VotingRules[]= array('table'=>'',
							'fieldName'=>'', 
							'fieldID'=>'', 
							'fieldValue'=>'', 
							'relation'=>'',
							'value'=>''
							);
	 foreach($iWantVotingRulesRef as $iW)
	 {extract($iW);
		switch($fieldName){
			case("POSMTypeID"):
				$tableName	= 'ITEM TYPE';
				$fieldName  = 'typeName';
				$table 		= 'POSM_Type';
			break;
			case("POSMStatusID"):
				$tableName	= 'ITEM STATUS';
				$fieldName  = 'statusName';
				$table 		= 'POSM_Status';
			break;
			case("OUTLETStatusID"):
				$tableName	= 'OUTLET STATUS';
				$fieldName  = 'statusName';
				$table 		= 'OUTLET_Status';
			break;
			case("PremiumTypeID"):
				$tableName	= 'PREMIUM TYPE NAME';
				$fieldName  = 'premiumTypeName';
				$table 		= 'premiumItemType';
			break;
			case("MaterialTypeID"):
				$tableName	= 'MATERIAL TYPE';;
				$fieldName  = 'materialName';
				$table 		= 'MATERIAL_Type';
			break;
			case("brandID"):
				$tableName	= 'BRAND NAME';
				$fieldName  = 'brandName';
				$table 		= 'brands';
			break;
		}
		
		$query 		= $this->db->query("SELECT $fieldName FROM $table WHERE id=$fieldID LIMIT 1");
		$row 		= $query->row();
		$name_Field = $row->$fieldName;
		
		$VotingRules[] = array('table'=>$tableName,
							'fieldName'=>$name_Field, 
							'fieldID'=>$fieldID, 
							'fieldValue'=>$name_Field, 
							'relation'=>$rel,
							'value'=>$val
							);
	}
	
	
	 
	 $data['iWantVotingRulesRef']  = $VotingRules;
	 
	 
	 $data['cID']	    = $id;
     $data['vfile']		= 'iWantPartial.php';
	 $data['title']		= 'iWant Report';
	 $data['rep']		= $rep;
	 $data['repHeader']		= $header;
	 $data['breadCrumbs']		= '<a href='.HTTP_PATH.'iWantCampaign/iWant> iWant Campaign </a>';
	 
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

	function iLike($action='',$id='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(47,'REVIEW');
		$filter_WHERE 		= $this->modules->country();
		$filter_AND 		= $this->modules->country2();
		//$votingCampaignID 	= $id;
		
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(45);
		
		$table						= 'campaign';
		$data['vfile']				= 'iLike_report.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/iLike> iLike Report </a>';
		
		//TOTAL NUMBER OF ROWS			
		$data['active_page']=1;
		$sql       = $this->db->query("SELECT id FROM campaign WHERE campaignType='iLike' and status='done' $filter_AND");
		$sql       = $sql->result_array();
		$total_rec = count($sql);

		$pagenum = 1;
		$data['page_rows'] = 10; 
		$data['last'] = ceil($total_rec/$data['page_rows']);		
		$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];

	
		extract($_POST);
		if($action=="page")
		{
			$this->modules->module_checker(27,'REVIEW');
			$pagenum = $id;
			$data['active_page'] = $id; 
			$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		}

		$filter_WHERE = $this->modules->iLike_Campaign_country();
     	$sql = $this->db->query("SELECT *,campaign.id AS iLikeCampaignID FROM $table  
								 LEFT JOIN admin_users 
								 ON campaign.adminCreatorID = admin_users.id
								 WHERE campaignType='iLike' AND status='done' $filter_WHERE ORDER BY iLikeCampaignID  DESC $max");
								
		$data['campaigns'] = $sql->result_array();
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
	
	
	function monthDiff($DateFrom,$DateTo)
	{
		$sql = "SELECT 
		  (TIMESTAMPDIFF(MONTH, '$DateFrom', '$DateTo') +
		  DATEDIFF(
			'$DateTo',
			'$DateFrom' + INTERVAL
			  TIMESTAMPDIFF(MONTH, '$DateFrom', '$DateTo')
			MONTH
		  ) /
		  DATEDIFF(
			'$DateFrom' + INTERVAL
			  TIMESTAMPDIFF(MONTH, '$DateFrom', '$DateTo') + 1
			MONTH,
			'$DateFrom' + INTERVAL
			  TIMESTAMPDIFF(MONTH, '$DateFrom', '$DateTo')
			MONTH
		  )) AS months";
		  
		  $query = $this->db->query($sql);
		  $row = $query->row();
		  return CEIL($row->months);
	}
	
	function perQuarterMonths($type='')
	{
		$month = date('m');
		$year  = date('Y');
		
		$sql 	 = $this->db->query("SELECT ceiling((month(CURDATE()) / 3)) AS quarter");
		$sql 	 = $sql->row();
		$quarter = $sql->quarter; 
	
		switch($quarter)
		{
			case 1:
				$prevMonth = "$year-01-01";
				$date 	   = "$year-02-15";
			break;
			case 2:
				$prevMonth = "$year-04-01";
				$date 	   = "$year-05-15";
			break;
			case 2:
				$prevMonth = "$year-07-01";
				$date 	   = "$year-08-15";
			break;
			case 2:
				$prevMonth = "$year-10-01";
				$date 	   = "$year-11-15";
			break;
		}
		
		//GET THE LAST DAY OF THE MONTH
		$sql 	  = $this->db->query("SELECT LAST_DAY(DATE_ADD('$date', INTERVAL 1 MONTH)) AS lastOfNextMonth");
		$sql 	  = $sql->row();
		$lastOfNextMonth = $sql->lastOfNextMonth; 
		
		$str = "";
		if($type=='prevMonth')
			$str = "$prevMonth";
		if($type=='nextMonth')
			$str = "$lastOfNextMonth";
		if($type=='filter')
			$str = " dUploaded BETWEEN '$prevMonth' AND '$lastOfNextMonth'";
		
		return $str;
	}
	
	//GROUP BY COUNTRY
	function BU_activeness_index()
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		
		$this->modules->module_checker(69,'REVIEW');
		
		//USER MANUAL
	    $data['USER_MANUAL'] = $this->modules->user_manual(48);
		
		$WHERE="";
		if($_SESSION['super_admin']!='y' AND $_SESSION['countryID']!=0){
			$WHERE 		= " WHERE cID =".$_SESSION['countryID'] ." AND ";
		}else{
			$WHERE 		= " WHERE cID != 0 AND ";
		}
		
		extract($_POST);
		
		$data['quarterStr'] = "";
		$data['DateFrom'] 	= "null";
		$data['DateTo']   	= "null";
		$data['months']	  	= "";
		if((isset($DateFrom) AND isset($DateTo)) AND ($DateFrom!='' AND $DateTo!='') AND !isset($Reset)){
			$WHERE   .= " dUploaded >= '$DateFrom' AND dUploaded <= '$DateTo' ";
			$data['DateFrom'] = $DateFrom;
			$data['DateTo']   = $DateTo;
			$data['months']	  = $this->monthDiff($DateFrom,$DateTo);
		}else{
			$WHERE    .= $this->perQuarterMonths('filter');
			$data['quarterStr'] = $this->quaterStr('condition');
			$data['months']	  = 3;
			$data['DateFrom'] = $this->perQuarterMonths('prevMonth');
			$data['DateTo']   = $this->perQuarterMonths('nextMonth');
		}
		
		$HAVING="";
		$cond = "";
		if(isset($Submit) OR isset($selpage))
		{	
			$val1 = mysql_real_escape_string($val1);
			$val1 = trim($val1);
			$val2 = mysql_real_escape_string($val2);
			$val2 = trim($val2);
			
			$condition = '';
			switch($cond1){
				case 'equal': 
					$condition = '=';
				break;
				case 'containing': 
					$condition = 'like';
				break;
				case 'in': 
					$condition = 'in';
				break;
				case 'greaterThan': 
					$condition = '>=';
				break;
				case 'lessThan': 
					$condition = '<=';
				break;
			}
			
			
			if(($opt1=='pcountry') AND $val1!='')
			{
				if($condition=='=')
					$cond = "  $opt1 $condition '$val1'";
				if($condition=='like')
					$cond = "  $opt1 $condition '%$val1%'";
				if($condition=='in')
					$cond = "  $opt1 $condition ('" . str_replace(",", "','", $val1) . "')";
			}
			
			//PUBLISH
			$field="";
			if(($opt1=='Uploaded_Items' OR $opt1=='Publish' OR $opt1=='Not_Yet_Publish' OR  $opt1=='Disapprove' OR  $opt1=='AVG_Local_Price' OR $opt1=='AVG_USD_Price') AND $val1!='' AND is_numeric($val1))
			{
				if($opt1=='Uploaded_Items')
					$field="COUNT(itemID)";
				if($opt1=='Publish')
					$field="SUM(publish='y')";
				if($opt1=='Not_Yet_Publish')
					$field="SUM(publish='n')";
				if($opt1=='Disapprove')
					$field="SUM(disapprove='y')";
				if($opt1=='AVG_Local_Price')
					$field="AVG(UnitPrice)";
				if($opt1=='AVG_USD_Price')
					$field="AVG(USD_Price)";
				
				if($condition=='=')
					$HAVING = " HAVING $field $condition $val1";
				if($condition=='>=')
					$HAVING = " HAVING $field $condition $val1";
				if($condition=='<=')
					$HAVING = " HAVING $field $condition $val1";
			}
			
			/*2ND SET*/
			switch($cond2){
				case 'equal': 
					$condition2 = '=';
				break;
				case 'containing': 
					$condition2 = 'like';
				break;
				case 'in': 
					$condition2 = 'in';
				break;
				case 'greaterThan': 
					$condition2 = '>=';
				break;
				case 'lessThan': 
					$condition2 = '<=';
				break;
			}
			
			if(($opt2=='pcountry') AND $val2!='')
			{
				if($condition2=='=')
					$cond .= "  $operator $condition2 '$val2'";
				if($condition2=='like')
					$cond .= "  $operator $condition2 '%$val2%'";
				if($condition2=='in')
					$cond .= "  $operator $condition2 ('" . str_replace(",", "','", $val2) . "')";
			}
			
			
			$field="";
			if(($opt2=='Uploaded_Items' OR $opt2=='Publish' OR $opt2=='Not_Yet_Publish' OR  $opt2=='Disapprove' OR  $opt2=='AVG_Local_Price' OR $opt2=='AVG_USD_Price') AND $val2!='' AND is_numeric($val2))
			{
				if($opt2=='Uploaded_Items')
					$field="COUNT(itemID)";
				if($opt2=='Publish')
					$field="SUM(publish='y')";
				if($opt2=='Not_Yet_Publish')
					$field="SUM(publish='n')";
				if($opt2=='Disapprove')
					$field="SUM(disapprove='y')";
				if($opt2=='AVG_Local_Price')
					$field="AVG(UnitPrice)";
				if($opt2=='AVG_USD_Price')
					$field="AVG(USD_Price)";
				
				if($condition2=='=')
					$HAVING .= " AND $field $condition2 $val2";
				if($condition2=='>=')
					$HAVING .= " AND $field $condition2 $val2";
				if($condition2=='<=')
					$HAVING .= " AND $field $condition2 $val2";
			}
			
			$cond = ($cond=="") ? "" : " AND $cond";
			$WHERE  .=  " $cond";
		}
		
		$data['POST'] = $_POST;
		if(isset($Reset)){
			$data['POST'] = array();
			$data['quarterStr'] = $this->quaterStr('condition');
			$data['DateFrom'] = $this->perQuarterMonths('prevMonth');
			$data['DateTo']   = $this->perQuarterMonths('nextMonth');
		}
		
	
		$table						= 'BU_activeness_index';
		$data['vfile']				= 'BU_activeness_index.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/BU_activeness_index> Activeness of Business Units Index </a>';
		
		//TOTAL NUMBER OF ROWS					
		$data['per_country'] = TRUE;
		
		$sql   = "SELECT cID, pcountry, COUNT(itemID) AS num_items,  AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice, 
				  SUM(publish='y') AS publish_items, SUM(publish='n') AS not_publish, SUM(disapprove='y') as disapprove_items, target
				  FROM item_db_reports 
				  LEFT JOIN target_items ON target_items.countryID = item_db_reports.cID
				  $WHERE GROUP BY cID $HAVING";
		
		$sql_csv = "SELECT pcountry as Country, COUNT(itemID) AS Items_Uploaded, SUM(publish='y') AS Publish_Items, SUM(publish='n') AS Not_Yet_Publish, SUM(disapprove='y') as Disapprove_Items, 
					AVG(UnitPrice) AS AVG_Local_Price, AVG(USD_Price) AS AVG_US_Price
					FROM item_db_reports $WHERE GROUP BY cID $HAVING";
		
		//generate csv file
		$this->generateCSVFile('item_views',$sql_csv,'SMBi_BU_activeness_Index.csv');
		$data['csvFile']			= "SMBi_BU_activeness_Index.csv";
		
		$sql   = $this->db->query($sql);
		$data['reports']   = $sql->result_array();
		

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
	
	function BU_activeness_details($cID='',$DateFrom='',$DateTo='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(69,'REVIEW');
		
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(50);
		
		$filter_WHERE="";
		if($_SESSION['super_admin']!='y'  AND $_SESSION['countryID']!=0)
			$filter_WHERE = "WHERE cID =".$_SESSION['countryID']." ";
		
		//$votingCampaignID 	= $id;
		
		$table						= 'BU_activeness_details';
		$data['vfile']				= 'BU_activeness_details.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/BU_activeness_index> BU Activeness Index </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/BU_activeness_details/$cID/$DateFrom/$DateTo'> BU Activeness Index in Details </a>";
		
		//TOTAL NUMBER OF ROWS			
		extract($_POST);
		$data['cID']= $cID;
		$cond="";
		$having="";
		$limit =isset($selpage)? $selpage:0;
		
		$WHERE  = " WHERE ";
		if($cID!=0)
			$WHERE  = " WHERE cID=$cID AND";
			
		$data['quarterStr'] = "";
		$data['DateFrom'] = "";
		$data['DateTo'] = "";
		if((isset($DateFrom) AND isset($DateTo)) AND ($DateFrom!='null' AND $DateTo!='null')){
			$WHERE   .= " dUploaded >= '$DateFrom' AND dUploaded <= '$DateTo' ";
			$data['DateFrom'] = $DateFrom;
			$data['DateTo'] = $DateTo;
		}else{
			$WHERE    .= $this->perQuarterMonths('filter');
			$data['quarterStr'] = $this->quaterStr('condition');
			$data['DateFrom'] = $this->perQuarterMonths('prevMonth');
			$data['DateTo']   = $this->perQuarterMonths('nextMonth');
		}
		
		if(isset($Submit) OR isset($selpage))
		{	
			$val1 = mysql_real_escape_string($val1);
			$val1 = trim($val1);
			$val2 = mysql_real_escape_string($val2);
			$val2 = trim($val2);
			
			$condition = '';
			switch($cond1){
				case 'equal': 
					$condition = '=';
				break;
				case 'containing': 
					$condition = 'like';
				break;
				case 'in': 
					$condition = 'in';
				break;
				case 'greaterThan': 
					$condition = '>=';
				break;
				case 'lessThan': 
					$condition = '<=';
				break;
			}
			
			
			if(($opt1=='itemCode' OR $opt1=='itemName' OR $opt1=='pstatus' OR $opt1=='ptype' OR $opt1=='poutlet_status' OR $opt1=='ppremium_type' OR $opt1=='pmaterial' OR $opt1=='pbrand' OR $opt1=='full_name' OR $opt1=='pcountry') AND $val1!='')
			{
				if($condition=='=')
					$cond = "  $opt1 $condition '$val1'";
				if($condition=='like')
					$cond = "  $opt1 $condition '%$val1%'";
				if($condition=='in')
					$cond = "  $opt1 $condition ('" . str_replace(",", "','", $val1) . "')";
			}
			
			//PUBLISH
			if(($opt1=='publish' OR $opt1=='disapprove') AND $val1!='')
			{
				if($condition=='=')
					$val1 = ($val1=='yes' OR $val1=='Yes') ? 'y' : 'n';
					$cond = "  $opt1 $condition '$val1'";
				if($condition=='like')
					$cond = "  $opt1 $condition '%$val1%'";
			}
			
			//VIEWS
			if(($opt1=='num_views' OR $opt1=='UnitPrice' OR $opt1=='likes' OR $opt1=='wants' OR $opt1=='USD_Price') AND $val1!='' AND $condition!='like' AND is_numeric($val1)) 
				$cond = "  $opt1 $condition $val1";
		
			//dateReleased
			if($opt1=='dateReleased' AND $val1!='' AND $condition!='like') 
				$cond = "  dReleased $condition '$val1'";
			
			/*2ND SET*/
			switch($cond2){
				case 'equal': 
					$condition2 = '=';
				break;
				case 'containing': 
					$condition2 = 'like';
				break;
				case 'in': 
					$condition2 = 'in';
				break;
				case 'greaterThan': 
					$condition2 = '>=';
				break;
				case 'lessThan': 
					$condition2 = '<=';
				break;
			}
			
			
			if(($opt2=='itemCode' OR $opt2=='itemName' OR $opt2=='pstatus' OR $opt2=='ptype' OR $opt2=='poutlet_status' OR $opt2=='ppremium_type' OR $opt2=='pmaterial' OR $opt2=='pbrand' OR $opt2=='full_name' OR $opt2=='pcountry') AND $val2!='')
			{
				if($condition2=='=')
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator $opt2 $condition2 '%$val2%'";
				if($condition=='in')
					$cond .= " $operator $opt1 $condition ('" . str_replace(",", "','", $val2) . "')";
			}
			
			
			//PUBLISH
			echo $val2;
			if(($opt2=='publish' OR $opt2=='disapprove') AND $val2!='')
			{
				if($condition2=='=')
					$val2 = ($val2=='yes' OR $val2=='Yes') ? 'y' : 'n';
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator $opt2 $condition2 '%$val2%'";
			}
		
			
			//LOCAL PRICE
			if(($opt2=='UnitPrice' OR $opt2=='USD_Price') AND $val2!='' AND $condition2!='like' AND is_numeric($val2)) 
				$cond .= "$operator $opt2 $condition2 $val2";
			
			
			//SPECIAL CASE
			if($opt1=='dateAdded' AND $val1!='' AND $condition=='>=' AND $opt2=='dateAdded' AND $val2!='' AND $condition2=='<=')
				$operator = 'AND';
			
			if($opt1=='dateReleased' AND $val1!='' AND $condition=='>=' AND $opt2=='dateReleased' AND $val2!='' AND $condition2=='<=')
				$operator = 'AND';
			
			if($opt1=='num_views' AND $val1!='' AND $condition=='>=' AND $opt2=='num_views' AND $val2!='' AND $condition2=='<=')
				$operator = 'AND';
			
			//Views 
			$op="";
			if($opt2=='num_views' AND $val2!='' AND $condition2!='like' AND is_numeric($val2)){
				$cond .= " $operator num_views $condition2 '$val2'";
			}
			
			//dateAdded 
			if($opt2=='dateAdded' AND $val2!='' AND $condition2!='like') 
				$cond .= " $operator dUploaded $condition2 '$val2'";
				
			//dateReleased
			if($opt2=='dateReleased' AND $val2!='' AND $condition2!='like') 
				$cond .= " $operator dReleased $condition2 '$val2'";
			
			$cond = ($cond=="") ? "" : " AND $cond";
			$WHERE  .=  " $cond";
		}
		
		$data['POST'] = $_POST;
		if(isset($Reset)){
			$data['POST'] = array();
			$data['quarterStr'] = $this->quaterStr('condition');
			$data['DateFrom'] = $this->perQuarterMonths('prevMonth');
			$data['DateTo']   = $this->perQuarterMonths('nextMonth');
		}
		
		$sql = "SELECT itemID, num_views, likes, wants, itemCode, item_image, itemName, pstatus, ptype, poutlet_status, ppremium_type, pmaterial, pbrand, full_name, cName, publish,disapprove, UnitPrice, USD_Price, dUploaded, dReleased
				FROM item_db_reports		 
				$WHERE ORDER BY num_views DESC";
				
		$sql_csv = "SELECT cName as Country, num_views as Views, likes as Likes, wants as Wants, itemCode as Item_Code, itemName as Item_Name, pstatus as Status, ptype as Type, poutlet_status as Outlet_Status, 
					ppremium_type as Premium_Type, pmaterial as Material_Type, pbrand as Brand, full_name as User, publish as Publish, disapprove as Disapprove, UnitPrice, USD_Price, dUploaded as Date_Uploaded, dReleased as Date_Released
				FROM item_db_reports		 
				$WHERE ORDER BY num_views DESC";
		
		//generate csv file
		$this->generateCSVFile('item_views',$sql_csv,'SMBi_BU_activeness_details.csv');
		$data['csvFile']			= "SMBi_BU_activeness_details.csv";
		
		$ctr = $this->db->query($sql);
		$ctr = $ctr->result_array();
		$data['totrec'] = count($ctr);
		$data['limit']  = $limit;
		
		$sql 	 = $this->db->query($sql);
		$items	 = $sql->result_array();
		
	
		$table= "<table id='large' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:11px;' class='iLike_Result_Table tablesorter'>
				<thead>
				<tr>
					<th style='width:80px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Country  	  		  </b></th> 
					<th style='width:25px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Views  	  		  </b></th> 
					<th style='width:25px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Likes  	  		  </b></th> 
					<th style='width:25px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Wants  	  		  </b></th> 
					<th style='width:100px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >      <b>Item Code  	  	  </b></th> 
					<th style='width:19px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Image   	 	      </b></th> 
					<th style='width:100px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >      <b>Name  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Status  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Type  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Outlet  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Premium  	  		  </b></th> 
					<th style='width:50px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Material  	  	  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Brand  	  	  	  </b></th> 
					<th style='width:30px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>User  	  		  </b></th> 
					<th style='width:30px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Publish  	  		  </b></th> 
					<th style='width:30px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Disapprove  	  	  </b></th> 
					<th style='width:60px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>L. Price  	  	  </b></th> 
					<th style='width:60px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>US. Price  	  	  </b></th> 
					<th style='width:80px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Uploaded  	  	  </b></th> 
					<th style='width:80px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Released  	  	  </b></th> 
				</tr>
				</thead>
				<tbody>";
				 
					$x = $limit;			
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					$orig_itemName="";
					foreach($items as $r) { 
					extract($r);
					$ptype = ($ptype=='') 	   ? 'Uncategorize' : $ptype;
					$publish = ($publish=='y') ? 'Yes' : 'No';
					$disapprove = ($disapprove=='y') ? 'Yes' : 'No';
					$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
					$orig_itemName=$itemName;
					if(strlen($itemName)>=15)
							$itemName = substr($itemName,0,15)."...";
							
					$poutlet_status = ($poutlet_status=="") ? "-" : $poutlet_status;
					$ppremium_type  = ($ppremium_type=="")  ? "-" : $ppremium_type;
					$pbrand 		= ($pbrand=="") 		? "-" : $pbrand;
					$likes 			= ($likes=="") 		? 0 : $likes;
					$wants 		    = ($wants=="") 		? 0 : $wants;
		$table.= "<tr>
				  <td style='text-align:left;padding-left:5px;'>		$cName																			</td>
				  <td>													<a onclick=\"viewDialog('item_database',$itemID)\" style='cursor:pointer;'><b>$num_views</b> </a> </td>
				  <td>													<a onclick=\"showVoters('iLike',$itemID)\" style='cursor:pointer;'><b>$likes</b></a>      																</td>
				  <td>													<a onclick=\"showVoters('iWant',$itemID)\" style='cursor:pointer;'><b>$wants</b></a>      																</td>
				  <td>													$itemCode      																	</td>
				  <td style='text-align:center;'>			    		<img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 	</td>
				  <td style='text-align:left;padding-left:5px;' title='$orig_itemName'>		<a href='".HTTP_PATH."gallery/itemInfo2/$itemID' target='_blank'>		$itemName</a>  </td>
				   <td style='text-align:left;padding-left:5px;'>		$pstatus																		</td>
				  <td style='text-align:left;padding-left:5px;'>		$ptype																			</td>
				  <td style='text-align:left;padding-left:5px;'>		$poutlet_status																	</td>
				  <td style='text-align:left;padding-left:5px;'>		$ppremium_type																	</td>
				  <td style='text-align:left;padding-left:5px;'>		$pmaterial																	    </td>
				  <td style='text-align:left;padding-left:5px;'>		$pbrand																	    	</td>
				  <td style='text-align:left;padding-left:5px;'>		$full_name																		</td>
				  <td style='text-align:center;'>						$publish																		</td>
				  <td style='text-align:center;'>						$disapprove																		</td>
				  <td style='text-align:center;'>						$UnitPrice																		</td>
				  <td style='text-align:center;'>						$USD_Price																		</td>
				  <td style='text-align:center;'>				        ". $dUploaded ."										</td>
				  <td style='text-align:center;'>				        ". $dReleased ."										</td>
				</tr>";}
		$table.= "</tbody>";
					if(!$items)
						$table.=  "<tr><td colspan='20'>No match found.</td></tr>";
		$table.= "</table>";	
		
		$data['pagination']	= true;
		$data['table'] 		= $table;
		
	
		
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
		
	function eCatalogue_index()
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(73,'REVIEW');
		
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(43);
		
		extract($_POST);
		
		$filter_WHERE='';
		
		$table						= 'eCatalogue_index';
		$data['vfile']				= 'eCatalogue_index.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/eCatalogue_index> eCatalogue Report </a>';
		
		//TOTAL NUMBER OF ROWS			
		
		$WHERE 	 =  $filter_WHERE;
		
		$data['per_country'] = TRUE;
		$sql   = $this->db->query("SELECT e_catalog.id as eID, title, cover, 
								   (SELECT avg(UnitPrice) FROM ec_items WHERE ec_items.ecID = e_catalog.id) as avgUnitPrice, 
								   (SELECT avg(USD_Price) FROM ec_items WHERE ec_items.ecID = e_catalog.id) as avgUSDPrice, 			   
								   (SELECT COUNT(ec_items.id)  FROM ec_items WHERE ec_items.ecID = e_catalog.id) as eCatalogueItems 			   
								   FROM e_catalog GROUP BY e_catalog.id");
				
		$data['POST'] = $_POST;
		$data['reports']   = $sql->result_array();
		

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
	
	function eCatalogue_per_Year($ecID='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(69,'REVIEW');
		
		extract($_POST);
		$filter_WHERE='';
		
		$table						= 'eCatalogue_per_Year';
		$data['vfile']				= 'eCatalogue_per_Year.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/eCatalogue_index> eCatalogue Report </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/eCatalogue_per_Year/$ecID'> eCatalogue Yearly Report </a>";
		
		//TOTAL NUMBER OF ROWS			
		
		$WHERE 	 =  $filter_WHERE;
		
		$sql = "SELECT e_catalog.title  as eTitle, YEAR(ec_items.dateAdded) as year, COUNT(ec_items.id) as eCatalogueItems, 
				avg(ec_items.UnitPrice) as avgUnitPrice, 
				avg(ec_items.USD_Price)  as avgUSDPrice	
				FROM ec_items 
				LEFT JOIN e_catalog ON e_catalog.id = ec_items.ecID
				WHERE ec_items.ecID = $ecID
				GROUP BY YEAR(ec_items.dateAdded), e_catalog.title;   
				";
		$sql   = $this->db->query($sql);
				
		$data['POST']   = $_POST;
		$data['reports']= $sql->result_array();
		$data['ecID']	= $ecID;

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
	
	function eCatalogue_per_Month($ecID='',$year)
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(69,'REVIEW');
		
		extract($_POST);
		
		$filter_WHERE='';
		
		$table						= 'eCatalogue_per_Month';
		$data['vfile']				= 'eCatalogue_per_Month.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/eCatalogue_index> eCatalogue Report </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/eCatalogue_per_Year/$ecID'> eCatalogue Yearly Report </a>";
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/eCatalogue_per_Month/$ecID/$year'> eCatalogue Monthly Report </a>";
		
		//TOTAL NUMBER OF ROWS			
		
		$WHERE 	 =  $filter_WHERE;
		
		$sql = "SELECT e_catalog.title  as eTitle, YEAR(ec_items.dateAdded) as year, DATE_FORMAT(ec_items.dateAdded,'%M') as month, MONTH(ec_items.dateAdded) AS mID,  COUNT(ec_items.id) as eCatalogueItems, 
				avg(ec_items.UnitPrice) as avgUnitPrice, 
				avg(ec_items.USD_Price)  as avgUSDPrice	
				FROM ec_items 
				LEFT JOIN e_catalog ON e_catalog.id = ec_items.ecID
				WHERE ec_items.ecID = $ecID AND YEAR(ec_items.dateAdded)=$year
				GROUP BY MONTH(ec_items.dateAdded), YEAR(ec_items.dateAdded);   
				";
		$sql   = $this->db->query($sql);
				
		$data['POST']   = $_POST;
		$data['reports']= $sql->result_array();
		$data['ecID']	= $ecID;
		$data['year']	= $year;

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
	
	function eCatalogue_items($view='',$ecID='',$year='',$month='')
	{
		$data['typeView']  = $view;
		$data['month']     = ($month=='') 	  ? 'null' : $month;
		$data['year']      = ($year=='') 	  ? 'all' :  $year;
		$data['month_in_word'] = $this->month($month);
		$data['ecID']	   = $ecID;
		
		//eCatalogue Name
		$sql = $this->db->query("SELECT title FROM e_catalog WHERE id= $ecID LIMIT 0,1");
		$sql = $sql->row();
		$data['ecTitle'] = $sql->title;
		
		//print_r($_POST);
		extract($_POST);
		$cond="WHERE ec_item_reports.ecID = $ecID";
		$having="";
		if(isset($Submit) OR isset($selpage))
		{	
			$val1 = mysql_real_escape_string($val1);
			$val1 = trim($val1);
			$val2 = mysql_real_escape_string($val2);
			$val2 = trim($val2);
			
			$condition = '';
			switch($cond1){
				case 'equal': 
					$condition = '=';
				break;
				case 'containing': 
					$condition = 'like';
				break;
				case 'greaterThan': 
					$condition = '>=';
				break;
				case 'lessThan': 
					$condition = '<=';
				break;
			}
			
			//ITEM CODE
			if($opt1=='itemCode' AND $val1!='')
			{
				if($condition=='=')
					$cond .= " AND itemCode $condition '$val1'";
				if($condition=='like')
					$cond .= " AND itemCode $condition '%$val1%'";
			}
			
			//ITEM NAME
			if($opt1=='itemName')
			{
				if($condition=='=')
					$cond .= " AND itemName $condition '$val1'";
				if($condition=='like')
					$cond .= " AND itemName $condition '%$val1%'";
			}
			
			//ITEM TYPE
			if($opt1=='itemType')
			{
				if($condition=='=')
					$cond .= " AND ptype $condition '$val1'";
				if($condition=='like')
					$cond .= " AND ptype $condition '%$val1%'";
			}
			
			//USER NAME
			if($opt1=='user_id')
			{
				if($condition=='=')
					$cond .= " AND full_name $condition '$val1'";
				if($condition=='like')
					$cond .= " AND full_name $condition '%$val1%'";
			}
			
			//PUBLISH
			if($opt1=='publish')
			{
				if($condition=='=')
					$val1 = ($val1=='yes' OR $val1=='Yes') ? 'y' : 'n';
					$cond .= " AND publish $condition '$val1'";
				if($condition=='like')
					$cond .= " AND publish $condition '%$val1%'";
			}
			
			//VIEWS
			if($opt1=='num_views' AND $val1!='' AND $condition!='like' AND is_numeric($val1)) 
				$cond .= " AND num_views $condition $val1";
			
			//LOCAL PRICE
			if($opt1=='UnitPrice' AND $val1!='' AND $condition!='like' AND is_numeric($val1)) 
				$cond .= " AND UnitPrice $condition $val1";
			
			//USD PRICE
			if($opt1=='USD_Price' AND $val1!='' AND $condition!='like' AND is_numeric($val1)) 
				$cond .= " AND USD_Price $condition $val1";
			
			//dateAdded
			if($opt1=='dateAdded' AND $val1!='' AND $condition!='like') 
				$cond .= " AND dUploaded $condition '$val1'";
				
			//dateReleased
			if($opt1=='dateReleased' AND $val1!='' AND $condition!='like') 
				$cond .= " AND dReleased $condition '$val1'";
			
			/*2ND SET*/
			switch($cond2){
				case 'equal': 
					$condition2 = '=';
				break;
				case 'containing': 
					$condition2 = 'like';
				break;
				case 'greaterThan': 
					$condition2 = '>=';
				break;
				case 'lessThan': 
					$condition2 = '<=';
				break;
			}
			
			//ITEM CODE
			if($opt2=='itemCode' AND $val2!='')
			{
				if($condition2=='=')
					$cond .= " $operator itemCode $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator itemCode $condition2 '%$val2%'";
			}
			
			//ITEM NAME
			if($opt2=='itemName' AND $val2!='')
			{
				if($condition2=='=')
					$cond .= " $operator itemName $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator itemName $condition2 '%$val2%'";
			}
			
			//ITEM TYPE
			if($opt2=='itemType' AND $val2!='')
			{
				if($condition2=='=')
					$cond .= " $operator typeName $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator typeName $condition2 '%$val2%'";
			}
			
			//USER NAME
			if($opt2=='user_id' AND $val2!='')
			{
				if($condition2=='=')
					$cond .= " $operator full_name $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator full_name $condition2 '%$val2%'";
			}
			
			//PUBLISH
			if($opt2=='publish' AND $val2!='')
			{
				if($condition2=='=')
					$val2  = ($val2=='yes' OR $val2=='Yes') ? 'y' : 'n';
					$cond .= " $operator publish $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator publish $condition2 '%$val2%'";
			}
			
			
			//LOCAL PRICE
			if($opt2=='UnitPrice' AND $val2!='' AND $condition2!='like' AND is_numeric($val2)) 
				$cond .= "$operator UnitPrice $condition2 $val2";
			
			//USD PRICE
			if($opt2=='USD_Price' AND $val2!='' AND $condition2!='like' AND is_numeric($val2)) 
				$cond .= " $operator USD_Price $condition2 $val2";
			
			//SPECIAL CASE
			if($opt1=='dateAdded' AND $val1!='' AND $condition=='>=' AND $opt2=='dateAdded' AND $val2!='' AND $condition2=='<=')
				$operator = 'AND';
			
			if($opt1=='dateReleased' AND $val1!='' AND $condition=='>=' AND $opt2=='dateReleased' AND $val2!='' AND $condition2=='<=')
				$operator = 'AND';
			
			if($opt1=='num_views' AND $val1!='' AND $condition=='>=' AND $opt2=='num_views' AND $val2!='' AND $condition2=='<=' AND $condition2!='like' AND is_numeric($val2))
				$operator = 'AND';
			
			//Views 
			$op="";
			if($opt2=='num_views' AND $val2!='' AND $condition2!='like')
				$having .= " $operator num_views $condition2 $val2";

			
			//dateAdded 
			if($opt2=='dateAdded' AND $val2!='' AND $condition2!='like') 
				$cond .= " $operator dUploaded $condition2 '$val2'";
				
			//dateReleased
			if($opt2=='dateReleased' AND $val2!='' AND $condition2!='like') 
				$cond .= " $operator dReleased $condition2 '$val2'";
		}
		
		$data['POST'] = $_POST;
		
		if(isset($Reset)){
			$data['POST'] = array();
			$cond='';
		}
		
		//echo $cond;
		$limit =isset($selpage)? $selpage:0;
		if($view=='default'){
		$sql = "SELECT iID as itemID, ecID, itemName, ptype, itemCode, num_views, 
				item_image, dUploaded, full_name, publish,  UnitPrice, USD_Price, dReleased
				FROM ec_item_reports 
				$cond ORDER BY iID DESC";
		}
		elseif($view=='gYear')
		{
			$sql = "SELECT iID as itemID, ecID, itemName, ptype, itemCode,  num_views,
					item_image, dUploaded, full_name, publish, UnitPrice, USD_Price, dReleased 
					FROM ec_item_reports 
					$cond AND YEAR(dUploaded) = $year ORDER BY itemID DESC";
		}
		elseif($view=='gMonth')
		{
			$sql = "SELECT iID as itemID, ecID, itemName, ptype, itemCode, num_views,
					item_image, dUploaded, full_name, publish, UnitPrice, USD_Price, dReleased 
					FROM ec_item_reports 
					$cond AND MONTH(dUploaded) = $month AND YEAR(dUploaded) = $year ORDER BY itemID DESC";
		}
	
		//echo $sql;
	
		$ctr = $this->db->query($sql);
		$ctr = $ctr->result_array();
		$data['totrec'] = count($ctr);
		$data['limit']  = $limit;
		
		$sql 	 = $this->db->query($sql." LIMIT $limit,20");
		$items	 = $sql->result_array();
		
	
		$table= "<table id='large' cellpadding='0' cellspacing='0' border=1 style='width:120%;font-size:12px;' class='iLike_Result_Table tablesorter'>
				<thead>
				<tr>
					<th style='width:10px;text-align:center;color:white;padding:0px' bgcolor='#bb1d1d'>   					   <b>No 		 		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Item Code  	  	  </b></th> 
					<th style='width:19px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Image   	 	      </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Name  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Type  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>User  	  		  </b></th> 
					<th style='width:40px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Publish  	  		  </b></th> 
					<th style='width:40px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Views  	  		  </b></th> 
					<th style='width:70px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Local Price  	  	  </b></th> 
					<th style='width:70px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>USD Price  	  	  </b></th> 
					<th style='width:75px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Uploaded  	  	  </b></th> 
					<th style='width:75px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Released  	  	  </b></th> 
				</tr>
				</thead>
				<tbody>";
				 
					$x = $limit;			
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					foreach($items as $r) { 
					extract($r);
					$ptype = ($ptype=='') 	   ? 'Uncategorize' : $ptype;
					$publish = ($publish=='y') ? 'Yes' : 'No';
					$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 	
		$table.= "<tr>
				  <td>													$x      																		</td>
				  <td>													$itemCode      																	</td>
				  <td style='text-align:center;'>			    		<img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 	</td>
				  <td style='text-align:left;padding-left:5px;'>		<a href='".HTTP_PATH."gallery/itemInfoECatalog/$ecID/$itemID' target='_blank'>		$itemName</a>  </td>
				  <td style='text-align:left;padding-left:5px;'>		$ptype																			</td>
				  <td style='text-align:left;padding-left:5px;'>		$full_name																		</td>
				  <td style='text-align:center;'>						$publish																		</td>
				  <td style='text-align:center;'>						 <a onclick=\"viewDialog('eCatalogue',$itemID)\" style='cursor:pointer;'> $num_views </a> 																		</td>
				  <td style='text-align:center;'>						$UnitPrice																		</td>
				  <td style='text-align:center;'>						$USD_Price																		</td>
				  <td style='text-align:center;'>				        ". $dUploaded ."										</td>
				  <td style='text-align:center;'>				        ". $dReleased ."										</td>
				</tr>";}
		$table.= "</tbody>";
					if(!$items)
						$table.=  "<tr><td colspan='14'>No match found.</td></tr>";
		$table.= "</table>";	
		
		
		$data['table'] 		= $table;
		$data['vfile']		= 'eCatalogue_items.php';
		$data['title']		= 'Items Preview';
		
		//BREAD CRUMBS
		$HTTP_PATH 					 = HTTP_PATH."report/iLike";
		$data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
		$data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/eCatalogue_index>  eCatalogue Items </a>';
	
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
	
	function eCatalogue_item_division($view='',$ecID='',$year='',$month='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(73,'REVIEW');
		//eCatalogue Name
		$sql = $this->db->query("SELECT title FROM e_catalog WHERE id= $ecID LIMIT 0,1");
		$sql = $sql->row();
		$data['ecTitle'] = $sql->title;
		
		$filter_WHERE='WHERE ';
		if($view=='default'){
			$filter_WHERE .= " ec_items.ecID = $ecID";
			$groupBy	   = " ec_items.ecID";
			$data['vType'] = $view;
		}elseif($view=='gYear'){
			$filter_WHERE .= " ec_items.ecID = $ecID AND YEAR(ec_items.dateAdded) = $year";
			$data['vType'] = $view;
			$groupBy	   = " YEAR(ec_items.ecID)";
		}elseif($view=='gMonth'){
			$filter_WHERE .= " ec_items.ecID = $ecID AND YEAR(ec_items.dateAdded) = $year AND MONTH(ec_items.dateAdded) = $month";
			$data['vType'] = $view;
			$groupBy	   = " MONTH(ec_items.ecID)";
		}	
		
		$table						= 'eCatalogue_item_division';
		$data['vfile']				= 'eCatalogue_item_division.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		$data['ecID']			= $ecID;
		$data['cyear']				= $year;	
		$data['previewType']		= $view;
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/eCatalogue_index> eCatalogue Item Division </a>';
		
		//POSM TYPE;
		$arr = '';
		$sql   = $this->db->query("SELECT DISTINCT(ec_items.POSMTypeID) as fieldValue, (SELECT typeName FROM POSM_Type WHERE POSM_Type.id = ec_items.POSMTypeID) as col, AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice, 
								   COUNT(ec_items.id) as num_ec_items, DATE_FORMAT(ec_items.dateAdded,'%M') as month, MONTH(ec_items.dateAdded) AS mID, YEAR(ec_items.dateAdded)  as year, country.id as cID 
								   FROM   ec_items 	
								   LEFT   JOIN country 		ON country.id  = ec_items.countryID
								   $filter_WHERE
								   GROUP BY ec_items.POSMTypeID, $groupBy ORDER BY YEAR(ec_items.dateAdded) DESC, MONTH(ec_items.dateAdded) DESC");
		$arr[] = array('table'=>'POSM TYPE',
					   'fld'=>'POSMTypeID',
					   'rows'=>$sql->result_array());
	
		
		//OUTLET TYPE
		$sql   = $this->db->query("SELECT DISTINCT(ec_items.OUTLETStatusID) as fieldValue, (SELECT statusName FROM OUTLET_Status WHERE OUTLET_Status.id = ec_items.OUTLETStatusID) as col, AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice, 
								   COUNT(ec_items.id) as num_ec_items, DATE_FORMAT(ec_items.dateAdded,'%M') as month, MONTH(ec_items.dateAdded) AS mID, YEAR(ec_items.dateAdded)  as year, country.id as cID 
								   FROM   ec_items 	
								   LEFT   JOIN country 		ON country.id  = ec_items.countryID
								   $filter_WHERE
								   GROUP BY ec_items.OUTLETStatusID, $groupBy ORDER BY YEAR(ec_items.dateAdded) DESC, MONTH(ec_items.dateAdded) DESC");
		$arr[] = array('table'=>'SERVICE ITEM OUTLET TYPE',
					   'fld'=>'OUTLETStatusID',
					   'rows'=>$sql->result_array());
		
		//PREMIUM ITEM TYPE
		$sql   = $this->db->query("SELECT DISTINCT(ec_items.PremiumTypeID) as fieldValue, (SELECT premiumTypeName FROM premiumItemType WHERE premiumItemType.id = ec_items.PremiumTypeID) as col, AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice, 
								   COUNT(ec_items.id) as num_ec_items, DATE_FORMAT(ec_items.dateAdded,'%M') as month, MONTH(ec_items.dateAdded) AS mID, YEAR(ec_items.dateAdded)  as year, country.id as cID 
								   FROM   ec_items 	
								   LEFT   JOIN country 		ON country.id  = ec_items.countryID
								   $filter_WHERE
								   GROUP BY ec_items.PremiumTypeID, $groupBy ORDER BY YEAR(ec_items.dateAdded) DESC, MONTH(ec_items.dateAdded) DESC");
		$arr[] = array('table'=>'PREMIUM ITEM TYPE',
					   'fld'=>'PremiumTypeID',
					   'rows'=>$sql->result_array());
		
		//MATERIAL TYPE
		$sql   = $this->db->query("SELECT DISTINCT(ec_items.MaterialTypeID) as fieldValue, (SELECT materialName FROM MATERIAL_Type WHERE MATERIAL_Type.id = ec_items.MaterialTypeID) as col, AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice, 
								   COUNT(ec_items.id) as num_ec_items, DATE_FORMAT(ec_items.dateAdded,'%M') as month, MONTH(ec_items.dateAdded) AS mID, YEAR(ec_items.dateAdded)  as year, country.id as cID 
								   FROM   ec_items 	
								   LEFT   JOIN country 		ON country.id  = ec_items.countryID
								   $filter_WHERE
								   GROUP BY ec_items.MaterialTypeID, $groupBy ORDER BY YEAR(ec_items.dateAdded) DESC, MONTH(ec_items.dateAdded) DESC");
		$arr[] = array('table'=>'MATERIAL TYPE',
						'fld'=>'MaterialTypeID',
						'rows'=>$sql->result_array());
	
		
		$data['results'] = $arr;
		
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
	
	function ecitem_distribution_Preview($view='',$ecID='',$month='',$year='',$fld='',$fld_val='')
	{
		$table='';
		$fld_val = ($fld_val=="") ? 0 : $fld_val;
		$data['typeView']  = $view;
		$data['ecID']      = $ecID;
		$data['fld']       = $fld;
		$data['fld_val']   = $fld_val;
		$data['month']     = ($month=='') 	  ? 'null' : $this->month($month);
		$data['cyear']      = ($year=='') 	  ? 'null' : $year;
		
		//eCatalogue Name
		$sql = $this->db->query("SELECT title FROM e_catalog WHERE id= $ecID LIMIT 0,1");
		$sql = $sql->row();
		$data['ecTitle'] = $sql->title;
		
		//POSM TYPE
		//echo $fld;
		switch($fld){
		case 'POSMTypeID':
			$itm_type = "(SELECT typeName FROM POSM_Type WHERE POSM_Type.id = ec_items.POSMTypeID)";
		break;		
		case 'OUTLETStatusID':
			$itm_type = "(SELECT statusName FROM OUTLET_Status WHERE OUTLET_Status.id = ec_items.OUTLETStatusID)";
		break;
		case 'PremiumTypeID':
			$itm_type = "(SELECT premiumTypeName FROM premiumItemType WHERE premiumItemType.id = ec_items.PremiumTypeID)";
		break;
		case 'MaterialTypeID':
			$itm_type = "(SELECT materialName FROM MATERIAL_Type WHERE MATERIAL_Type.id = ec_items.MaterialTypeID)";
		break;
		}
		
		$fld = "AND ec_items.$fld = '$fld_val'"; 
		
		$data['month_in_word'] = $this->month($month);
		
		//print_r($_POST);
		extract($_POST);
		$data['user_id'] = isset($user_id) ? $user_id : 0;
		$cond='';
		
		if(isset($Submit) OR isset($selpage))
		{	
			$val1 = mysql_real_escape_string($val1);
			$val2 = mysql_real_escape_string($val2);
			
			$condition = '';
			switch($cond1){
				case 'equal': 
					$condition = '=';
				break;
				case 'containing': 
					$condition = 'like';
				break;
				case 'greaterThan': 
					$condition = '>=';
				break;
				case 'lessThan': 
					$condition = '<=';
				break;
			}
			
			//ITEM CODE
			if($opt1=='itemCode' AND $val1!='')
			{
				if($condition=='=')
					$cond = " AND ec_items.itemCode $condition '$val1'";
				if($condition=='like')
					$cond = " AND ec_items.itemCode $condition '%$val1%'";
			}
			
			//ITEM NAME
			if($opt1=='itemName')
			{
				if($condition=='=')
					$cond = " AND ec_items.itemName $condition '$val1'";
				if($condition=='like')
					$cond = " AND ec_items.itemName $condition '%$val1%'";
			}
			
			//USER NAME
			if($opt1=='user_id')
			{
				if($condition=='=')
					$cond = " AND admin_users.full_name $condition '$val1'";
				if($condition=='like')
					$cond = " AND admin_users.full_name $condition '%$val1%'";
			}
			
			//PUBLISH
			if($opt1=='publish')
			{
				if($condition=='=')
					$val1 = ($val1=='yes' OR $val1=='Yes') ? 'y' : 'n';
					$cond = " AND ec_items.publish $condition '$val1'";
				if($condition=='like')
					$cond = " AND ec_items.publish $condition '%$val1%'";
			}
			
			//LOCAL PRICE
			if($opt1=='UnitPrice' AND $val1!='' AND $condition!='like') 
				$cond = " AND ec_items.UnitPrice $condition $val1";
			
			//USD PRICE
			if($opt1=='USD_Price' AND $val1!='' AND $condition!='like') 
				$cond = " AND ec_items.USD_Price $condition $val1";
			
			//dateAdded
			if($opt1=='dateAdded' AND $val1!='' AND $condition!='like') 
				$cond = " AND ec_items.dateAdded $condition '$val1'";
				
			//dateReleased
			if($opt1=='dateReleased' AND $val1!='' AND $condition!='like') 
				$cond = " AND ec_items.dateReleased $condition '$val1'";
			
			/*2ND SET*/
			switch($cond2){
				case 'equal': 
					$condition2 = '=';
				break;
				case 'containing': 
					$condition2 = 'like';
				break;
				case 'greaterThan': 
					$condition2 = '>=';
				break;
				case 'lessThan': 
					$condition2 = '<=';
				break;
			}
			
			//ITEM CODE
			if($opt2=='itemCode' AND $val2!='')
			{
				if($condition2=='=')
					$cond .= " $operator ec_items.itemCode $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator ec_items.itemCode $condition2 '%$val2%'";
			}
			
			//ITEM NAME
			if($opt2=='itemName' AND $val2!='')
			{
				if($condition2=='=')
					$cond .= " $operator ec_items.itemName $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator ec_items.itemName $condition2 '%$val2%'";
			}
			
			//USER NAME
			if($opt2=='user_id' AND $val2!='')
			{
				if($condition2=='=')
					$cond .= " $operator admin_users.full_name $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator admin_users.full_name $condition2 '%$val2%'";
			}
			
			//PUBLISH
			if($opt2=='publish' AND $val2!='')
			{
				if($condition2=='=')
					$val2  = ($val2=='yes' OR $val2=='Yes') ? 'y' : 'n';
					$cond .= " $operator ec_items.publish $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator ec_items.publish $condition2 '%$val2%'";
			}
			
			
			//LOCAL PRICE
			if($opt2=='UnitPrice' AND $val2!='') 
				$cond .= "$operator ec_items.UnitPrice $condition2 $val2";
			
			//USD PRICE
			if($opt2=='USD_Price' AND $val2!='') 
				$cond .= " $operator ec_items.USD_Price $condition2 $val2";
			
			//SPECIAL CASE
			if($opt1=='dateAdded' AND $val1!='' AND $condition=='>=' AND $opt2=='dateAdded' AND $val2!='' AND $condition2=='<=')
				$operator = 'AND';
			
			if($opt1=='dateReleased' AND $val1!='' AND $condition=='>=' AND $opt2=='dateReleased' AND $val2!='' AND $condition2=='<=')
				$operator = 'AND';
			
			//dateAdded 
			if($opt2=='dateAdded' AND $val2!='' AND $condition2!='like') 
				$cond .= " $operator ec_items.dateAdded $condition2 '$val2'";
				
			//dateReleased
			if($opt2=='dateReleased' AND $val2!='' AND $condition2!='like') 
				$cond .= " $operator ec_items.dateReleased $condition2 '$val2'";
		}
		
		$data['POST'] = $_POST;
		
		if(isset($Reset)){
			$data['POST'] = array();
			$cond='';
		}
		
		//echo $cond;
		$limit =isset($selpage)? $selpage:0;
		
		if($view=='default'){
			
			$sql = "SELECT ec_items.id as itemID, itemName, $itm_type as ptype, itemCode, 
					(SELECT image FROM ecitems_images WHERE defaultStatus = 1 AND itemID = ec_items.id) as item_image, ec_items.dateAdded as dUploaded, full_name, publish,
					UnitPrice, USD_Price, ec_items.dateReleased as dReleased
					FROM ec_items LEFT JOIN admin_users ON admin_users.id = ec_items.user_id
					WHERE ec_items.ecID = $ecID $fld $cond ORDER BY ec_items.id DESC";
		}if($view=='gYear'){
			$sql = "SELECT ec_items.id as itemID, itemName, $itm_type as ptype, itemCode, 
					(SELECT image FROM ecitems_images WHERE defaultStatus = 1 AND itemID = ec_items.id) as item_image, ec_items.dateAdded as dUploaded, full_name, publish,
					UnitPrice, USD_Price, ec_items.dateReleased as dReleased
					FROM ec_items LEFT JOIN admin_users ON admin_users.id = ec_items.user_id
					WHERE ec_items.ecID = $ecID $fld $cond AND YEAR(ec_items.dateAdded)=$year ORDER BY ec_items.id DESC";
		}if($view=='gMonth'){
			$sql = "SELECT ec_items.id as itemID, itemName, $itm_type as ptype, itemCode, 
					(SELECT image FROM ecitems_images WHERE defaultStatus = 1 AND itemID = ec_items.id) as item_image, ec_items.dateAdded as dUploaded, full_name, publish,
					UnitPrice, USD_Price, ec_items.dateReleased as dReleased
					FROM ec_items LEFT JOIN admin_users ON admin_users.id = ec_items.user_id
					WHERE ec_items.ecID = $ecID $fld $cond AND YEAR(ec_items.dateAdded)=$year AND MONTH(ec_items.dateAdded)=$month ORDER BY ec_items.id DESC";
		}
		
		$ctr = $this->db->query($sql);
		$ctr = $ctr->result_array();
		$data['totrec'] = count($ctr);
		$data['limit']  = $limit;
		
		$sql 	 = $this->db->query($sql." LIMIT $limit,20");
		$items	 = $sql->result_array();
	
		//print_r($items);
			
		$table = "<table id='large' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:12px;' class='iLike_Result_Table tablesorter'>
				<thead>
				<tr>
					<th style='width:10px;text-align:center;color:white;padding:0px' bgcolor='#bb1d1d'>   					   <b>No 		 		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Item Code  	  	  </b></th> 
					<th style='width:19px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Image   	 	      </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Name  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Type  	  		  </b></th> 
					<th style='width:30px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>User  	  		  </b></th> 
					<th style='width:30px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Publish  	  		  </b></th> 
					<th style='width:70px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Local Price  	  		  </b></th> 
					<th style='width:70px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>USD Price  	  		  </b></th> 
					<th style='width:75px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Uploaded  	  </b></th> 
					<th style='width:75px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Released  	  </b></th> 
				</tr>
				</thead>
				<tbody>";
				 
					$x = 0;			
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					foreach($items as $r) { 
					extract($r);
					$ptype = ($ptype=='') 	   ? 'Uncategorize' : $ptype;
					$publish = ($publish=='y') ? 'Yes' : 'No';
					$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 	
		$table .="<tr>
				  <td >													$x      																		</td>
				  <td >													$itemCode      																	</td>
				  <td  style='text-align:center;'>			    		<img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 			</td>
				  <td  style='text-align:left;padding-left:5px;'>		<a href='".HTTP_PATH."gallery/itemInfo2/$itemID' target='_blank'>		$itemName</a>   </td>
				  <td  style='text-align:left;padding-left:5px;'>		$ptype											</td>
				   <td  style='text-align:center;'>						$full_name										</td>
				  <td  style='text-align:center;'>						$publish										</td>
				  <td style='text-align:center;'>						$UnitPrice																		</td>
				  <td style='text-align:center;'>						$USD_Price																		</td>
				  <td style='text-align:center;'>				        ". $dUploaded ."										</td>
				  <td style='text-align:center;'>				        ". $dReleased ."										</td>
				</tr>";}
		$table .="</tbody>";
					if(!$items)
						$table .= "<tr><td colspan='11'>No match found.</td></tr>";
		$table .="</table>";
		
		$data['POST']		= $_POST;
		$data['table'] 		= $table;
		$data['vfile']		= 'ecitem_distribution_Preview.php';
		$data['title']		= 'Items Preview';
		
		//BREAD CRUMBS
		$HTTP_PATH 					 = HTTP_PATH."report/iLike";
		$data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
		$data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/eCatalogue_index> eCatalogue Item Division </a>';
		
	
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
	
	function BU_activeness()
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(69,'REVIEW');
		
		extract($_POST);
		
		$filter_WHERE='';
		if($_SESSION['super_admin']!='y'){
			$filter_WHERE 		= "WHERE items.countryID =".$_SESSION['countryID'] ." AND YEAR(items.dateAdded) = ".DATE("Y");
			$data['cyear']		= isset($cyear) ? $cyear : DATE("Y");
		}else{
			$filter_WHERE 		= "WHERE YEAR(items.dateAdded) = ".DATE("Y");
			$data['cyear']		= isset($cyear) ? $cyear : DATE("Y");;
		}
		
		$table						= 'BU_activeness';
		$data['vfile']				= 'BU_activeness.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/BU_activeness> Activeness of Business Units </a>';
		
		//TOTAL NUMBER OF ROWS			
		
		$WHERE 	 =  $filter_WHERE;
		
		$data['per_country'] = TRUE;
		$sql   = $this->db->query("SELECT country.countryName AS cName, country.id AS cID,  
								   COUNT(items.id) as num_items, target, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year 
								   FROM   items 		 
								   LEFT   JOIN country 		ON country.id  = items.countryID 
								   LEFT   JOIN target_items ON target_items.countryID = items.countryID 
								   $WHERE
								   GROUP BY country.id ORDER BY YEAR(items.dateAdded) DESC");
		
		if(isset($filter))
		{   
			$data['per_country'] = FALSE;
			$WHERE 	 = '';
			if($countryID!='' AND $countryID!='all') $WHERE  .=  " items.countryID = $countryID AND";
			if($countryID=='all')					 $WHERE   =  "";
			if($cyear!=''  AND $cyear!='all') 	 	 $WHERE  .=  " YEAR(items.dateAdded) = $cyear AND";		
			if($WHERE!='')  $WHERE  =  substr("WHERE ".$WHERE,0,-3);
			
			$sql   = $this->db->query("SELECT country.countryName AS cName, country.id AS cID,  
									   COUNT(items.id) as num_items, target, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year 
									   FROM items 		 
									   LEFT   JOIN country 		ON country.id  = items.countryID 
									   LEFT   JOIN target_items ON target_items.countryID = items.countryID 
									   $WHERE
									   GROUP BY cName, YEAR(items.dateAdded) ORDER BY YEAR(items.dateAdded) DESC");
		}
		
		$data['POST'] = $_POST;
			
		
		$data['reports']   = $sql->result_array();
		

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
	
	function BU_activeness_per_Year($cID='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(69,'REVIEW');
		$c='';
		
		if($cID!=''){
			$data['countryID'] = $cID;
			$c = "WHERE country.id = $cID"; 
		}
		if($cID==0){
			$c = "WHERE country.id!=0";
		}
		
		$filter_WHERE	= $c;
		
		$table						= 'BU_activeness';
		$data['vfile']				= 'BU_activeness_per_Year.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					 = HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/BU_activeness_index> Activeness of Business Units Index</a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/BU_activeness_per_Year/$cID'> Activeness of Business Units per Year</a>";
		//TOTAL NUMBER OF ROWS			
		
		extract($_POST);
		$WHERE 	 =  $filter_WHERE;
		
		$data['per_country'] = TRUE;
		
		$sql   = $this->db->query("SELECT country.countryName AS cName, country.id AS cID,  
								   COUNT(items.id) as num_items, target, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year,
								   AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice
								   FROM   items 		 
								   LEFT   JOIN country 		ON country.id  = items.countryID 
								   LEFT   JOIN target_items ON target_items.countryID = items.countryID 
								   $WHERE
								   GROUP BY YEAR(items.dateAdded) ORDER BY YEAR(items.dateAdded) DESC");

		if(isset($filter))
		{   
			$data['per_country'] = FALSE;
			$data['countryID'] = $countryID;
			$WHERE 	 = '';
			if($countryID!='' AND $countryID!='all') $WHERE  .=  " items.countryID = $countryID AND";
			if($countryID=='all')					 $WHERE   =  "";
			if($cyear!=''  AND $cyear!='all') 	 	 $WHERE  .=  " YEAR(items.dateAdded) = $cyear AND";
			/*
			if($fmonth!='' AND $fmonth!='all' AND $tmonth!='' AND $tmonth!='all') 	 	 
					$WHERE  .=  " MONTH(items.dateAdded) >= $fmonth AND  MONTH(items.dateAdded) <= $tmonth AND";
			*/
			if($WHERE!='')  $WHERE  =  substr("WHERE ".$WHERE,0,-3);
			
			$sql   = $this->db->query("SELECT country.countryName AS cName, country.id AS cID,  
									   COUNT(items.id) as num_items, target, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year,
									   AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice
									   FROM items 		 
									   LEFT   JOIN country 		ON country.id  = items.countryID 
									   LEFT   JOIN target_items ON target_items.countryID = items.countryID 
									   $WHERE
									   GROUP BY YEAR(items.dateAdded) ORDER BY YEAR(items.dateAdded) DESC");
		}
		
		$data['POST'] = $_POST;
		$data['reports']   = $sql->result_array();
		

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
	
	function BU_activeness_per_Month($cID='',$year='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(69,'REVIEW');
		$c='';
		
		
		if($cID!=''){
			$data['countryID'] = $cID;
			$c = " country.id = $cID AND"; 
		}
		if($cID==0){
			$c = " country.id!=0 AND";
		}
		
		$filter_WHERE	= "";
		$filter_WHERE 	= "WHERE $c YEAR(items.dateAdded) = ".$year;
		$data['cyear']	= $year;
		//die();
		
		$table						= 'BU_activeness';
		$data['vfile']				= 'BU_activeness_per_Month.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					 = HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/BU_activeness_index> Activeness of Business Units Index</a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/BU_activeness_per_Year/$cID'> Activeness of Business Units per Year</a>";
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/BU_activeness_per_Month/$cID/$year'> Activeness of Business Units per Month</a>";
		
		//TOTAL NUMBER OF ROWS			
		
		extract($_POST);
		$WHERE 	 =  $filter_WHERE;
		
		$data['per_country'] = TRUE;
		$sql   = $this->db->query("SELECT country.countryName AS cName, country.id AS cID,  
								   COUNT(items.id) as num_items, target, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year,
								   AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice
								   FROM   items 		 
								   LEFT   JOIN country 		ON country.id  = items.countryID 
								   LEFT   JOIN target_items ON target_items.countryID = items.countryID 
								   $WHERE
								   GROUP BY MONTH(items.dateAdded), YEAR(items.dateAdded) ORDER BY MONTH(items.dateAdded) DESC, YEAR(items.dateAdded) DESC");

		if(isset($filter))
		{   
			$data['per_country'] = FALSE;
			$data['countryID'] = $countryID;
			$WHERE 	 = '';
			if($countryID!='' AND $countryID!='all') $WHERE  .=  " items.countryID = $countryID AND";
			if($countryID=='all')					 $WHERE   =  "";
			if($cyear!=''  AND $cyear!='all') 	 	 $WHERE  .=  " YEAR(items.dateAdded) = $cyear AND";
			if($fmonth!='' AND $fmonth!='all' AND $tmonth!='' AND $tmonth!='all') 	 	 
					$WHERE  .=  " MONTH(items.dateAdded) >= $fmonth AND  MONTH(items.dateAdded) <= $tmonth AND";
		
			if($WHERE!='')  $WHERE  =  substr("WHERE ".$WHERE,0,-3);
			
			$sql   = $this->db->query("SELECT country.countryName AS cName, country.id AS cID,  
									   COUNT(items.id) as num_items, target, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year,
									   AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice
									   FROM items 		 
									   LEFT   JOIN country 		ON country.id  = items.countryID 
									   LEFT   JOIN target_items ON target_items.countryID = items.countryID 
									   $WHERE
									   GROUP BY MONTH(items.dateAdded), YEAR(items.dateAdded) ORDER BY YEAR(items.dateAdded) DESC");
		}
		
		$data['POST'] = $_POST;
		
		$data['reports']   = $sql->result_array();
		

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
	
	function BU_activeness_Users()
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(68,'REVIEW');
		
		//USER MANUAL
	    $data['USER_MANUAL'] = $this->modules->user_manual(43);
		
		$table						= 'BU_activeness_Users';
		$data['vfile']				= 'BU_activeness_Users.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/BU_activeness_Users> Activeness of Business Units Index </a>';
		
		//TOTAL NUMBER OF ROWS			
		
		extract($_POST);
		$WHERE="";
		if($_SESSION['super_admin']!='y' AND $_SESSION['countryID']!=0){
			$WHERE 		= " WHERE cID =".$_SESSION['countryID'] ." AND ";
		}else{
			$WHERE 		= " WHERE cID != 0 AND ";
		}
		
		extract($_POST);
		
		$data['quarterStr'] = "";
		$data['DateFrom'] = "null";
		$data['DateTo'] = "null";
		if((isset($DateFrom) AND isset($DateTo)) AND ($DateFrom!='' AND $DateTo!='')){
			$WHERE   .= " dUploaded >= '$DateFrom' AND dUploaded <= '$DateTo' ";
		}else{
			$WHERE    .= $this->perQuarterMonths('filter');
			$data['quarterStr'] = $this->quaterStr('condition');
			$data['months']	  = 3;
			$data['DateFrom'] = $this->perQuarterMonths('prevMonth');
			$data['DateTo']   = $this->perQuarterMonths('nextMonth');
		}
	
		$HAVING="";
		$cond = "";
		if(isset($Submit) OR isset($selpage))
		{	
			$val1 = mysql_real_escape_string($val1);
			$val1 = trim($val1);
			$val2 = mysql_real_escape_string($val2);
			$val2 = trim($val2);
			
			$condition = '';
			switch($cond1){
				case 'equal': 
					$condition = '=';
				break;
				case 'containing': 
					$condition = 'like';
				break;
				case 'in': 
					$condition = 'in';
				break;
				case 'greaterThan': 
					$condition = '>=';
				break;
				case 'lessThan': 
					$condition = '<=';
				break;
			}
			
			
			if(($opt1=='pcountry' OR $opt1=='full_name') AND $val1!='')
			{
				if($condition=='=')
					$cond = "  $opt1 $condition '$val1'";
				if($condition=='like')
					$cond = "  $opt1 $condition '%$val1%'";
				if($condition=='in')
					$cond = "  $opt1 $condition ('" . str_replace(",", "','", $val1) . "')";
			}
			
			//PUBLISH
			$field="";
			if(($opt1=='Uploaded_Items' OR $opt1=='Publish' OR $opt1=='Not_Yet_Publish' OR  $opt1=='Disapprove' OR  $opt1=='AVG_Local_Price' OR $opt1=='AVG_USD_Price') AND $val1!='' AND is_numeric($val1))
			{
				if($opt1=='Uploaded_Items')
					$field="COUNT(itemID)";
				if($opt1=='Publish')
					$field="SUM(publish='y')";
				if($opt1=='Not_Yet_Publish')
					$field="SUM(publish='n')";
				if($opt1=='Disapprove')
					$field="SUM(disapprove='y')";
				if($opt1=='AVG_Local_Price')
					$field="AVG(UnitPrice)";
				if($opt1=='AVG_USD_Price')
					$field="AVG(USD_Price)";
				
				if($condition=='=')
					$HAVING = " HAVING $field $condition $val1";
				if($condition=='>=')
					$HAVING = " HAVING $field $condition $val1";
				if($condition=='<=')
					$HAVING = " HAVING $field $condition $val1";
			}
			
			/*2ND SET*/
			switch($cond2){
				case 'equal': 
					$condition2 = '=';
				break;
				case 'containing': 
					$condition2 = 'like';
				break;
				case 'in': 
					$condition2 = 'in';
				break;
				case 'greaterThan': 
					$condition2 = '>=';
				break;
				case 'lessThan': 
					$condition2 = '<=';
				break;
			}
			
			if(($opt2=='pcountry' OR $opt2=='full_name') AND $val2!='')
			{
				if($condition2=='=')
					$cond .= "  $operator $condition2 '$val2'";
				if($condition2=='like')
					$cond .= "  $operator $condition2 '%$val2%'";
				if($condition2=='in')
					$cond .= "  $operator $condition2 ('" . str_replace(",", "','", $val2) . "')";
			}
			
			
			$field="";
			if(($opt2=='Uploaded_Items' OR $opt2=='Publish' OR $opt2=='Not_Yet_Publish' OR  $opt2=='Disapprove' OR  $opt2=='AVG_Local_Price' OR $opt2=='AVG_USD_Price') AND $val2!='' AND is_numeric($val2))
			{
				if($opt2=='Uploaded_Items')
					$field="COUNT(itemID)";
				if($opt2=='Publish')
					$field="SUM(publish='y')";
				if($opt2=='Not_Yet_Publish')
					$field="SUM(publish='n')";
				if($opt2=='Disapprove')
					$field="SUM(disapprove='y')";
				if($opt2=='AVG_Local_Price')
					$field="AVG(UnitPrice)";
				if($opt2=='AVG_USD_Price')
					$field="AVG(USD_Price)";
				
				if($condition2=='=')
					$HAVING .= " AND $field $condition2 $val2";
				if($condition2=='>=')
					$HAVING .= " AND $field $condition2 $val2";
				if($condition2=='<=')
					$HAVING .= " AND $field $condition2 $val2";
			}
			
			$cond = ($cond=="") ? "" : " AND $cond";
			$WHERE  .=  " $cond";
		}
		
		$data['POST']      = $_POST;
		if(isset($Reset)){
			$data['POST'] = array();
			$data['quarterStr'] = $this->quaterStr('condition');
			$data['DateFrom'] = $this->perQuarterMonths('prevMonth');
			$data['DateTo']   = $this->perQuarterMonths('nextMonth');
		}
		
		
		//TOTAL NUMBER OF ROWS					
		$data['per_country'] = TRUE;
		
		$sql   = "SELECT cID, userID, pcountry, full_name, COUNT(itemID) AS num_items, SUM(publish='y') AS publish_items, SUM(publish='n') AS not_publish, SUM(disapprove='y') as disapprove_items,
				  AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice
				  FROM item_db_reports $WHERE GROUP BY userID $HAVING ORDER BY pcountry ASC";
		
		$sql_csv = "SELECT pcountry as Country, full_name as User, COUNT(itemID) AS Uploaded_Items, SUM(publish='y') AS Publish_Items, SUM(publish='n') AS Not_Publish, SUM(disapprove='y') as Disapprove_Items,
				    AVG(UnitPrice) AS AVG_Local_Price, AVG(USD_Price) AS AVG_US_Price
				    FROM item_db_reports $WHERE GROUP BY  userID $HAVING ORDER BY pcountry ASC";
		
		//generate csv file
		$this->generateCSVFile('item_views',$sql_csv,'SMBi_BU_Activeness_Users_Index.csv');
		$data['csvFile']			= "SMBi_BU_Activeness_Users_Index.csv";
		
		$sql   = $this->db->query($sql);
		
		$data['reports']   = $sql->result_array();
		
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
	
	function BU_activeness_Users_details($cID='',$userID='',$DateFrom='',$DateTo='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(69,'REVIEW');
		
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(50);
		
		
		//$votingCampaignID 	= $id;
		
		$table						= 'BU_activeness_Users_details';
		$data['vfile']				= 'BU_activeness_Users_details.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/BU_activeness_Users> BU Activeness of Users Index </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/BU_activeness_Users_details/$cID/$userID/$DateFrom/$DateTo'> BU Activeness of Users in Details </a>";
		
		//TOTAL NUMBER OF ROWS			
		extract($_POST);
		$data['cID']   = $cID;
		$data['userID']= $userID;
		$cond="";
		$having="";
		$limit =isset($selpage)? $selpage:0;
		
		$WHERE  = " WHERE ";
		if($cID!=0)
			$WHERE  = " WHERE userID=$userID AND";
			
		$data['quarterStr'] = "";
		$data['DateFrom'] = "null";
		$data['DateTo'] = "null";
		if((isset($DateFrom) AND isset($DateTo)) AND ($DateFrom!='null' AND $DateTo!='null')){
			$WHERE   .= " (dUploaded >= '$DateFrom' AND dUploaded <= '$DateTo') ";
			$data['DateFrom'] = $DateFrom;
			$data['DateTo'] = $DateTo;
		}else{
			$data['quarterStr'] = $this->quaterStr('condition');
			$data['DateFrom'] = $this->perQuarterMonths('prevMonth');
			$data['DateTo']   = $this->perQuarterMonths('nextMonth');
		}
		
		if(isset($Submit) OR isset($selpage))
		{	
			$val1 = mysql_real_escape_string($val1);
			$val1 = trim($val1);
			$val2 = mysql_real_escape_string($val2);
			$val2 = trim($val2);
			
			$condition = '';
			switch($cond1){
				case 'equal': 
					$condition = '=';
				break;
				case 'containing': 
					$condition = 'like';
				break;
				case 'in': 
					$condition = 'in';
				break;
				case 'greaterThan': 
					$condition = '>=';
				break;
				case 'lessThan': 
					$condition = '<=';
				break;
			}
			
			
			if(($opt1=='itemCode' OR $opt1=='itemName' OR $opt1=='pstatus' OR $opt1=='ptype' OR $opt1=='poutlet_status' OR $opt1=='ppremium_type' OR $opt1=='pmaterial' OR $opt1=='pbrand' OR $opt1=='full_name' OR $opt1=='pcountry') AND $val1!='')
			{
				if($condition=='=')
					$cond = "  $opt1 $condition '$val1'";
				if($condition=='like')
					$cond = "  $opt1 $condition '%$val1%'";
				if($condition=='in')
					$cond = "  $opt1 $condition ('" . str_replace(",", "','", $val1) . "')";
			}
			
			//PUBLISH
			if(($opt1=='publish' OR $opt1=='disapprove') AND $val1!='')
			{
				if($condition=='=')
					$val1 = ($val1=='yes' OR $val1=='Yes') ? 'y' : 'n';
					$cond = "  $opt1 $condition '$val1'";
				if($condition=='like')
					$cond = "  $opt1 $condition '%$val1%'";
			}
			
			//VIEWS
			if(($opt1=='num_views' OR $opt1=='UnitPrice' OR $opt1=='likes' OR $opt1=='wants' OR $opt1=='USD_Price') AND $val1!='' AND $condition!='like' AND is_numeric($val1)) 
				$cond = "  $opt1 $condition $val1";
		
			//dateReleased
			if($opt1=='dateReleased' AND $val1!='' AND $condition!='like') 
				$cond = "  dReleased $condition '$val1'";
			
			/*2ND SET*/
			switch($cond2){
				case 'equal': 
					$condition2 = '=';
				break;
				case 'containing': 
					$condition2 = 'like';
				break;
				case 'in': 
					$condition2 = 'in';
				break;
				case 'greaterThan': 
					$condition2 = '>=';
				break;
				case 'lessThan': 
					$condition2 = '<=';
				break;
			}
			
			
			if(($opt2=='itemCode' OR $opt2=='itemName' OR $opt2=='pstatus' OR $opt2=='ptype' OR $opt2=='poutlet_status' OR $opt2=='ppremium_type' OR $opt2=='pmaterial' OR $opt2=='pbrand' OR $opt2=='full_name' OR $opt2=='pcountry') AND $val2!='')
			{
				if($condition2=='=')
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator $opt2 $condition2 '%$val2%'";
				if($condition2=='in')
					$cond .= " $operator $opt1 $condition ('" . str_replace(",", "','", $val2) . "')";
			}
			
			
			//PUBLISH
			if(($opt2=='publish' OR $opt2=='disapprove') AND $val2!='')
			{
				if($condition2=='=')
					$val2 = ($val2=='yes' OR $val2=='Yes') ? 'y' : 'n';
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator $opt2 $condition2 '%$val2%'";
			}
			
			
			//LOCAL PRICE
			if(($opt2=='num_views' OR $opt2=='UnitPrice' OR $opt2=='likes' OR $opt2=='wants' OR $opt2=='USD_Price') AND $val2!='' AND $condition2!='like' AND is_numeric($val2))
				$cond .= "$operator $opt2 $condition2 $val2";
			
			
			//SPECIAL CASE
			if($opt2=='dateAdded' AND $val2!='' AND $condition=='>=' AND $opt2=='dateAdded' AND $val2!='' AND $condition2=='<=')
				$operator = 'AND';
			
			if($opt2=='dateReleased' AND $val2!='' AND $condition=='>=' AND $opt2=='dateReleased' AND $val2!='' AND $condition2=='<=')
				$operator = 'AND';
			
			if($opt2=='num_views' AND $val2!='' AND $condition=='>=' AND $opt2=='num_views' AND $val2!='' AND $condition2=='<=')
				$operator = 'AND';
			
			//Views 
			$op="";
			if($opt2=='num_views' AND $val2!='' AND $condition2!='like' AND is_numeric($val2)){
				$cond .= " $operator num_views $condition2 '$val2'";
			}
			
			//dateAdded 
			if($opt2=='dateAdded' AND $val2!='' AND $condition2!='like') 
				$cond .= " $operator dUploaded $condition2 '$val2'";
				
			//dateReleased
			if($opt2=='dateReleased' AND $val2!='' AND $condition2!='like') 
				$cond .= " $operator dReleased $condition2 '$val2'";
			
			$cond = ($cond=="") ? "" : " AND $cond";
			$WHERE  .=  " $cond";
		}
		
		$data['POST']      = $_POST;
		if(isset($Reset)){
			$data['POST'] = array();
			$data['quarterStr'] = $this->quaterStr('condition');
			$data['DateFrom'] = $this->perQuarterMonths('prevMonth');
			$data['DateTo']   = $this->perQuarterMonths('nextMonth');
		}
		
		$sql = "SELECT itemID, num_views, likes, wants, itemCode, item_image, itemName, pstatus, ptype, poutlet_status, ppremium_type, pmaterial, pbrand, full_name, cName, publish,disapprove, UnitPrice, USD_Price, dUploaded, dReleased
				FROM item_db_reports		 
				$WHERE ORDER BY num_views DESC";
				
		$sql_csv = "SELECT cName as Country, num_views as Views, likes as Likes, wants as Wants, itemCode as Item_Code, itemName as Item_Name, pstatus as Status, ptype as Type, poutlet_status as Outlet_Status, 
					ppremium_type as Premium_Type, pmaterial as Material_Type, pbrand as Brand, full_name as User, publish as Publish, disapprove as Disapprove, UnitPrice, USD_Price, dUploaded as Date_Uploaded, dReleased as Date_Released
				FROM item_db_reports		 
				$WHERE ORDER BY num_views DESC";
		
		//generate csv file
		$this->generateCSVFile('item_views',$sql_csv,'BU_activeness_Users_details.csv');
		$data['csvFile']			= "BU_activeness_Users_details.csv";
		
		$ctr = $this->db->query($sql);
		$ctr = $ctr->result_array();
		$data['totrec'] = count($ctr);
		$data['limit']  = $limit;
		
		$sql 	 = $this->db->query($sql);
		$items	 = $sql->result_array();
		
	
		$table= "<table id='large' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:11px;' class='iLike_Result_Table tablesorter'>
				<thead>
				<tr>
					<th style='width:80px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>User  	  		  </b></th> 
					<th style='width:25px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Views  	  		  </b></th> 
					<th style='width:25px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Likes  	  		  </b></th> 
					<th style='width:25px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Wants  	  		  </b></th> 
					<th style='width:100px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >      <b>Item Code  	  	  </b></th> 
					<th style='width:19px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Image   	 	      </b></th> 
					<th style='width:100px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >      <b>Name  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Status  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Type  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Outlet  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Premium  	  		  </b></th> 
					<th style='width:50px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Material  	  	  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Brand  	  	  	  </b></th> 
					<th style='width:30px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Country  	  		  </b></th> 
					<th style='width:30px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Publish  	  		  </b></th> 
					<th style='width:30px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Disapprove  	  	  </b></th> 
					<th style='width:60px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>L. Price  	  	  </b></th> 
					<th style='width:60px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>US. Price  	  	  </b></th> 
					<th style='width:80px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Uploaded  	  	  </b></th> 
					<th style='width:80px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Released  	  	  </b></th> 
				</tr>
				</thead>
				<tbody>";
				 
					$x = $limit;			
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					$orig_itemName="";
					foreach($items as $r) { 
					extract($r);
					$ptype = ($ptype=='') 	   ? 'Uncategorize' : $ptype;
					$publish = ($publish=='y') ? 'Yes' : 'No';
					$disapprove = ($disapprove=='y') ? 'Yes' : 'No';
					$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
					$orig_itemName=$itemName;
					if(strlen($itemName)>=15)
							$itemName = substr($itemName,0,15)."...";
							
					$poutlet_status = ($poutlet_status=="") ? "-" : $poutlet_status;
					$ppremium_type  = ($ppremium_type=="")  ? "-" : $ppremium_type;
					$pbrand 		= ($pbrand=="") 		? "-" : $pbrand;
					$likes 			= ($likes=="") 		? 0 : $likes;
					$wants 		    = ($wants=="") 		? 0 : $wants;
		$table.= "<tr>
				  <td style='text-align:left;padding-left:5px;'>		$full_name																			</td>
				  <td>													<a onclick=\"viewDialog('item_database',$itemID)\" style='cursor:pointer;'><b>$num_views</b> </a> </td>
				  <td>													<a onclick=\"showVoters('iLike',$itemID)\" style='cursor:pointer;'><b>$likes</b></a>      																</td>
				  <td>													<a onclick=\"showVoters('iWant',$itemID)\" style='cursor:pointer;'><b>$wants</b></a>      																</td>
				  <td>													$itemCode      																	</td>
				  <td style='text-align:center;'>			    		<img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 	</td>
				  <td style='text-align:left;padding-left:5px;' title='$orig_itemName'>		<a href='".HTTP_PATH."gallery/itemInfo2/$itemID' target='_blank'>		$itemName</a>  </td>
				   <td style='text-align:left;padding-left:5px;'>		$pstatus																		</td>
				  <td style='text-align:left;padding-left:5px;'>		$ptype																			</td>
				  <td style='text-align:left;padding-left:5px;'>		$poutlet_status																	</td>
				  <td style='text-align:left;padding-left:5px;'>		$ppremium_type																	</td>
				  <td style='text-align:left;padding-left:5px;'>		$pmaterial																	    </td>
				  <td style='text-align:left;padding-left:5px;'>		$pbrand																	    	</td>
				  <td style='text-align:left;padding-left:5px;'>		$cName																		</td>
				  <td style='text-align:center;'>						$publish																		</td>
				  <td style='text-align:center;'>						$disapprove																		</td>
				  <td style='text-align:center;'>						$UnitPrice																		</td>
				  <td style='text-align:center;'>						$USD_Price																		</td>
				  <td style='text-align:center;'>				        ". $dUploaded ."										</td>
				  <td style='text-align:center;'>				        ". $dReleased ."										</td>
				</tr>";}
		$table.= "</tbody>";
					if(!$items)
						$table.=  "<tr><td colspan='20'>No match found.</td></tr>";
		$table.= "</table>";	
		
		$data['pagination']	= true;
		$data['table'] 		= $table;
		
	
		
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
	
	function BU_activeness_Users_per_Year($user_id='',$cID='',$year='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(68,'REVIEW');
		
		extract($_POST);
		$filter_WHERE='';
		$filter_WHERE 		= "WHERE admin_users.id = $user_id";
 		
		$table						= 'BU_activeness_Users';
		$data['vfile']				= 'BU_activeness_Users_per_Year.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/BU_activeness_Users> Activeness of Users Index  </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/BU_activeness_Users_per_Year/$user_id/$cID'> Activeness of Users per Year  </a>";
		//TOTAL NUMBER OF ROWS			
		
		
		$WHERE 	 		  = $filter_WHERE;
		$data['per_user'] = TRUE;
		$sql   = $this->db->query("SELECT country.countryName AS cName, admin_users.full_name as fname, admin_users.id as uID, country.id AS cID,  
								   COUNT(items.id) as num_items, target, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year 
								   FROM items 		 
								   LEFT   JOIN country 		ON country.id  = items.countryID 
								   LEFT   JOIN target_items ON target_items.countryID = items.countryID 
								   LEFT   JOIN admin_users  ON admin_users.id  = items.user_id 
								   $WHERE
								   GROUP BY YEAR(items.dateAdded) ORDER BY YEAR(items.dateAdded) DESC");
		
		if(isset($filter))
		{
			$data['per_user'] = FALSE;
			$WHERE 	 = '';
			if($countryID!='' AND $countryID!='all') $WHERE  .=  " items.countryID = $countryID AND";
			if($countryID=='all')					 $WHERE   =  "";
			if($user_id!=''  AND $user_id!='all') 	 $WHERE  .=  " items.user_id = $user_id     AND";
			/*
			if($cyear!=''  AND $cyear!='all') 	 	 $WHERE  .=  " YEAR(items.dateAdded) = $cyear AND";
			if($fmonth!='' AND $fmonth!='all' AND $tmonth!='' AND $tmonth!='all') 	 	 
					$WHERE  .=  " MONTH(items.dateAdded) >= $fmonth AND  MONTH(items.dateAdded) <= $tmonth AND";
			*/
			if($WHERE!='')  $WHERE  =  substr("WHERE ".$WHERE,0,-3);
			
			$sql   = $this->db->query("SELECT country.countryName AS cName, admin_users.full_name as fname, admin_users.id as uID, country.id AS cID,  
								   COUNT(items.id) as num_items, target, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year 
								   FROM items 		 
								   LEFT   JOIN country 		ON country.id  = items.countryID 
								   LEFT   JOIN target_items ON target_items.countryID = items.countryID 
								   LEFT   JOIN admin_users  ON admin_users.id  = items.user_id 
								   $WHERE
								   GROUP BY YEAR(items.dateAdded) ORDER BY YEAR(items.dateAdded) DESC");
		}
		
		$data['user_id']    = $user_id;
		$data['countryID']  = $cID;
		$data['POST'] = $_POST;		
		$data['reports']   = $sql->result_array();
		

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
	
	function BU_activeness_Users_per_Month($user_id='',$cID='',$year='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(68,'REVIEW');
		
		extract($_POST);
		$filter_WHERE='';
		$filter_WHERE 		= "WHERE admin_users.id = $user_id AND YEAR(items.dateAdded) = ".$year;
 		
		$table						= 'BU_activeness_Users';
		$data['vfile']				= 'BU_activeness_Users_per_Month.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/BU_activeness_Users> Activeness of Users Index  </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/BU_activeness_Users_per_Year/$user_id/$cID'> Activeness of Users per Year  </a>";
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/BU_activeness_Users_per_Month/$user_id/$cID/$year'> Activeness of Users per Month  </a>";
		//TOTAL NUMBER OF ROWS			
		
		
		$WHERE 	 		  = $filter_WHERE;
		$data['per_user'] = TRUE;
		$sql   = $this->db->query("SELECT country.countryName AS cName, admin_users.full_name as fname, admin_users.id as uID, country.id AS cID,  
								   COUNT(items.id) as num_items, target, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year 
								   FROM items 		 
								   LEFT   JOIN country 		ON country.id  = items.countryID 
								   LEFT   JOIN target_items ON target_items.countryID = items.countryID 
								   LEFT   JOIN admin_users  ON admin_users.id  = items.user_id 
								   $WHERE
								   GROUP BY MONTH(items.dateAdded), YEAR(items.dateAdded) ORDER BY MONTH(items.dateAdded) DESC, YEAR(items.dateAdded) DESC");
		
		if(isset($filter))
		{
			$data['per_user'] = FALSE;
			$WHERE 	 = '';
			if($countryID!='' AND $countryID!='all') $WHERE  .=  " items.countryID = $countryID AND";
			if($countryID=='all')					 $WHERE   =  "";
			if($user_id!=''  AND $user_id!='all') 	 $WHERE  .=  " items.user_id = $user_id     AND";
			if($cyear!=''  AND $cyear!='all') 	 	 $WHERE  .=  " YEAR(items.dateAdded) = $cyear AND";
			if($fmonth!='' AND $fmonth!='all' AND $tmonth!='' AND $tmonth!='all') 	 	 
					$WHERE  .=  " MONTH(items.dateAdded) >= $fmonth AND  MONTH(items.dateAdded) <= $tmonth AND";
					
			if($WHERE!='')  $WHERE  =  substr("WHERE ".$WHERE,0,-3);
			
			$sql   = $this->db->query("SELECT country.countryName AS cName, admin_users.full_name as fname, admin_users.id as uID, country.id AS cID,  
								   COUNT(items.id) as num_items, target, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year 
								   FROM items 		 
								   LEFT   JOIN country 		ON country.id  = items.countryID 
								   LEFT   JOIN target_items ON target_items.countryID = items.countryID 
								   LEFT   JOIN admin_users  ON admin_users.id  = items.user_id 
								   $WHERE
								   GROUP BY MONTH(items.dateAdded), YEAR(items.dateAdded) ORDER BY MONTH(items.dateAdded) DESC, YEAR(items.dateAdded) DESC");
		}
		
		$data['user_id']    = $user_id;
		$data['countryID']  = $cID;
		$data['cyear']  	= $year;
		$data['POST'] 		= $_POST;		
		$data['reports']    = $sql->result_array();
		

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
	
	//AVG Price per Item
	function AVG_Price_index()
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(69,'REVIEW');
		extract($_POST);
		
		$filter_WHERE='';
		if($_SESSION['super_admin']!='y'){
			$filter_WHERE 		= "WHERE items.countryID =".$_SESSION['countryID'];
		}
		
		$table						= 'AVG_Price_index';
		$data['vfile']				= 'AVG_Price_index.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/AVG_Price_index> Average Price Index </a>';
		
		//TOTAL NUMBER OF ROWS			
		
		$WHERE 	 =  $filter_WHERE;
		
		$data['per_country'] = TRUE;
		$sql   = $this->db->query("SELECT country.countryName AS cName, country.id AS cID,  
								   COUNT(items.id) as num_items, target, DATE_FORMAT(items.dateAdded,'%M') as month, 
								   MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year,
								   AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice
								   FROM   items 		 
								   LEFT   JOIN country 		ON country.id  = items.countryID 
								   LEFT   JOIN target_items ON target_items.countryID = items.countryID 
								   $WHERE
								   GROUP BY country.id  ORDER BY YEAR(items.dateAdded) DESC");
		
		if(isset($filter))
		{   
			$data['per_country'] = FALSE;
			$WHERE 	 = '';
			if($countryID!='' AND $countryID!='all') $WHERE  .=  " items.countryID = $countryID AND";
			if($countryID=='all')					 $WHERE   =  "";	
			if($WHERE!='')  $WHERE  =  substr("WHERE ".$WHERE,0,-3);
			
			$sql   = $this->db->query("SELECT country.countryName AS cName, country.id AS cID,  
									   COUNT(items.id) as num_items, target, DATE_FORMAT(items.dateAdded,'%M') as month, 
									   MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year,
									   AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice
									   FROM items 		 
									   LEFT   JOIN country 		ON country.id  = items.countryID 
									   LEFT   JOIN target_items ON target_items.countryID = items.countryID 
									   $WHERE
									   GROUP BY cName ORDER BY YEAR(items.dateAdded) DESC");
		}
		
		$data['POST'] = $_POST;
		$data['reports']   = $sql->result_array();
		

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
	
	function AVG_Price_per_Year($cID='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(69,'REVIEW');
		extract($_POST);
		
		$filter_WHERE='';
		$filter_WHERE 		= "WHERE items.countryID =$cID";
		$data['countryID']	= $cID;
		
		$table						= 'AVG_Price_per_Year';
		$data['vfile']				= 'AVG_Price_per_Year.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/AVG_Price_index> Average Price per Year </a>';
		
		//TOTAL NUMBER OF ROWS			
		
		$WHERE 	 =  $filter_WHERE;
		
		$data['per_country'] = TRUE;
		$sql   = $this->db->query("SELECT country.countryName AS cName, country.id AS cID,  
								   COUNT(items.id) as num_items, target, DATE_FORMAT(items.dateAdded,'%M') as month, 
								   MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year,
								   AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice
								   FROM   items 		 
								   LEFT   JOIN country 		ON country.id  = items.countryID 
								   LEFT   JOIN target_items ON target_items.countryID = items.countryID 
								   $WHERE
								   GROUP BY YEAR(items.dateAdded) ORDER BY YEAR(items.dateAdded) DESC");
		
		if(isset($filter))
		{   
			$data['per_country'] = FALSE;
			$WHERE 	 = '';
			if($countryID!='' AND $countryID!='all') $WHERE  .=  " items.countryID = $countryID AND";
			if($countryID=='all')					 $WHERE   =  "";	
			if($WHERE!='')  $WHERE  =  substr("WHERE ".$WHERE,0,-3);
			
			$sql   = $this->db->query("SELECT country.countryName AS cName, country.id AS cID,  
									   COUNT(items.id) as num_items, target, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year 
									   FROM items 		 
									   LEFT   JOIN country 		ON country.id  = items.countryID 
									   LEFT   JOIN target_items ON target_items.countryID = items.countryID 
									   $WHERE
									   GROUP BY YEAR(items.dateAdded) ORDER BY YEAR(items.dateAdded) DESC");
		}
		
		$data['POST'] = $_POST;
		$data['reports']   = $sql->result_array();
		

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
	
	function AVG_Price_per_Month($cID='',$year='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(69,'REVIEW');
		extract($_POST);
		
		$filter_WHERE='';
		$filter_WHERE 		= "WHERE items.countryID =$cID AND YEAR(items.dateAdded) = $year";
		$data['countryID']	= $cID;
		$data['cyear']		= $year;
		
		$table						= 'AVG_Price_per_Month';
		$data['vfile']				= 'AVG_Price_per_Month.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/AVG_Price_index> Average Price per Month </a>';
		
		//TOTAL NUMBER OF ROWS			
		
		$WHERE 	 =  $filter_WHERE;
		
		$data['per_country'] = TRUE;
		$sql   = $this->db->query("SELECT country.countryName AS cName, country.id AS cID,  
								   COUNT(items.id) as num_items, target, DATE_FORMAT(items.dateAdded,'%M') as month, 
								   MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year,
								   AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice
								   FROM   items 		 
								   LEFT   JOIN country 		ON country.id  = items.countryID 
								   LEFT   JOIN target_items ON target_items.countryID = items.countryID 
								   $WHERE
								   GROUP BY MONTH(items.dateAdded), YEAR(items.dateAdded) ORDER BY MONTH(items.dateAdded) DESC, YEAR(items.dateAdded) DESC");
		
		if(isset($filter))
		{   
			$data['per_country'] = FALSE;
			$WHERE 	 = '';
			if($countryID!='' AND $countryID!='all') $WHERE  .=  " items.countryID = $countryID AND YEAR(items.dateAdded) = $year AND";
			if($countryID=='all')					 $WHERE   =  "";
			if($fmonth!='' AND $fmonth!='all' AND $tmonth!='' AND $tmonth!='all') 	 	 
					$WHERE  .=  " MONTH(items.dateAdded) >= $fmonth AND  MONTH(items.dateAdded) <= $tmonth AND";
					
			if($WHERE!='')  $WHERE  =  substr("WHERE ".$WHERE,0,-3);
			
			$sql   = $this->db->query("SELECT country.countryName AS cName, country.id AS cID,  
									   COUNT(items.id) as num_items, target, DATE_FORMAT(items.dateAdded,'%M') as month, 
									   MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year,
									   AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice
									   FROM items 		 
									   LEFT   JOIN country 		ON country.id  = items.countryID 
									   LEFT   JOIN target_items ON target_items.countryID = items.countryID 
									   $WHERE
									   GROUP BY MONTH(items.dateAdded), YEAR(items.dateAdded) ORDER BY MONTH(items.dateAdded) DESC, YEAR(items.dateAdded) DESC");
		}
		
		$data['POST'] = $_POST;
		$data['reports']   = $sql->result_array();
		

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
	
	
	function detectQuater()
	{
		return $quater = CEIL(date('m')/3);
	}
	
	function quaterStr()
	{
		$quater = CEIL(date('m')/3);
		$str="";
		switch($quater)
		{
			case 1:
				$str="Q1: January-March";
			break; 
			case 2:
				$str="Q2: April-June";
			break; 
			case 4:
				$str="Q3: August-September";
			break; 
			case 2:
				$str="Q4: October-December";
			break; 
		}
		
		return $str;
	}
	
	function csv_from_result($query, $delim = ",", $newline = "\n", $enclosure = '"')
	{
		if ( ! is_object($query) OR ! method_exists($query, 'list_fields'))
		{
			show_error('You must submit a valid result object');
		}

		$out = '';

		// First generate the headings from the table column names
		foreach ($query->list_fields() as $name)
		{
			$out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, $name).$enclosure.$delim;
		}

		$out = rtrim($out);
		$out .= $newline;

		// Next blast through the result array and build out the rows
		foreach ($query->result_array() as $row)
		{
			foreach ($row as $item)
			{
				$out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, $item).$enclosure.$delim;
			}
			$out = rtrim($out);
			$out .= $newline;
		}

		return $out;
	}
	
	
	function generateCSVFile($view='',$sql='',$fileName='')
	{
		$query = $this->db->query($sql);
		$new_report = $this->csv_from_result($query,",","\n");
		write_file(realpath($_SERVER["DOCUMENT_ROOT"])."/files/csv/$fileName",$new_report);
	}
	
	function downloadCSV($fileName='')
	{
		force_download($fileName, file_get_contents(realpath($_SERVER["DOCUMENT_ROOT"])."/files/csv/$fileName"));
	}
	
	function item_views($action='',$id='')
	{
		
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(70,'REVIEW');
		
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(50);
		
		$filter_WHERE="";
		if($_SESSION['super_admin']!='y'  AND $_SESSION['countryID']!=0)
			$filter_WHERE = "WHERE cID =".$_SESSION['countryID']." ";
		
		//$votingCampaignID 	= $id;
		
		$table						= 'item_views';
		$data['vfile']				= 'item_views.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/item_views> Number of Views per Item </a>';
		
		//TOTAL NUMBER OF ROWS			
		extract($_POST);
		
		$WHERE="";
		if($_SESSION['super_admin']!='y' AND $_SESSION['countryID']!=0){
			$WHERE 		= " WHERE cID =".$_SESSION['countryID'] ." AND publish='y' AND ";
		}else{
			$WHERE 		= " WHERE cID != 0 AND  publish='y' AND ";
		}
		
		$cond="";
		$having="";
		$limit =isset($selpage)? $selpage:0;
	
		$data['quarterStr'] = "";
		$data['DateFrom'] = "";
		$data['DateTo'] = "";
		if((isset($DateFrom) AND isset($DateTo)) AND ($DateFrom!='' AND $DateTo!='')){
			$WHERE   .= " dUploaded >= '$DateFrom' AND dUploaded <= '$DateTo' ";
		}else{
			$WHERE    .= $this->perQuarterMonths('filter');
			$data['quarterStr'] = $this->quaterStr('condition');
			$data['DateFrom'] = $this->perQuarterMonths('prevMonth');
			$data['DateTo']   = $this->perQuarterMonths('nextMonth');
		}
		
		if(isset($Submit) OR isset($selpage))
		{	
			$val1 = mysql_real_escape_string($val1);
			$val1 = trim($val1);
			$val2 = mysql_real_escape_string($val2);
			$val2 = trim($val2);
			
			$condition = '';
			switch($cond1){
				case 'equal': 
					$condition = '=';
				break;
				case 'containing': 
					$condition = 'like';
				break;
				case 'in': 
					$condition = 'in';
				break;
				case 'greaterThan': 
					$condition = '>=';
				break;
				case 'lessThan': 
					$condition = '<=';
				break;
			}
			
			
			if(($opt1=='itemCode' OR $opt1=='itemName' OR $opt1=='pstatus' OR $opt1=='ptype' OR $opt1=='poutlet_status' OR $opt1=='ppremium_type' OR $opt1=='pmaterial' OR $opt1=='pbrand' OR $opt1=='full_name' OR $opt1=='pcountry') AND $val1!='')
			{
				if($condition=='=')
					$cond = "  $opt1 $condition '$val1'";
				if($condition=='like')
					$cond = "  $opt1 $condition '%$val1%'";
				if($condition=='in')
					$cond = "  $opt1 $condition ('" . str_replace(",", "','", $val1) . "')";
			}
			
			//PUBLISH
			if(($opt1=='publish' OR $opt1=='disapprove') AND $val1!='')
			{
				if($condition=='=')
					$val1 = ($val1=='yes' OR $val1=='Yes') ? 'y' : 'n';
					$cond = "  $opt1 $condition '$val1'";
				if($condition=='like')
					$cond = "  $opt1 $condition '%$val1%'";
			}
			
			//VIEWS
			if(($opt1=='num_views' OR $opt1=='UnitPrice' OR $opt1=='likes' OR $opt1=='wants' OR $opt1=='USD_Price') AND $val1!='' AND $condition!='like' AND is_numeric($val1)) 
				$cond = "  $opt1 $condition $val1";
		
			//dateReleased
			if($opt1=='dateReleased' AND $val1!='' AND $condition!='like') 
				$cond = "  dReleased $condition '$val1'";
			
			/*2ND SET*/
			switch($cond2){
				case 'equal': 
					$condition2 = '=';
				break;
				case 'containing': 
					$condition2 = 'like';
				break;
				case 'in': 
					$condition = 'in';
				break;
				case 'greaterThan': 
					$condition2 = '>=';
				break;
				case 'lessThan': 
					$condition2 = '<=';
				break;
			}
			
			
			if(($opt2=='itemCode' OR $opt2=='itemName' OR $opt2=='pstatus' OR $opt2=='ptype' OR $opt2=='poutlet_status' OR $opt2=='ppremium_type' OR $opt2=='pmaterial' OR $opt2=='pbrand' OR $opt2=='full_name' OR $opt2=='pcountry') AND $val2!='')
			{
				if($condition2=='=')
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator $opt2 $condition2 '%$val2%'";
				if($condition=='in')
					$cond .= " $operator $opt1 $condition ('" . str_replace(",", "','", $val2) . "')";
			}
			
			
			//PUBLISH
			echo $val2;
			if(($opt2=='publish' OR $opt2=='disapprove') AND $val2!='')
			{
				if($condition2=='=')
					$val2 = ($val2=='yes' OR $val2=='Yes') ? 'y' : 'n';
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator $opt2 $condition2 '%$val2%'";
			}
			
			
			//LOCAL PRICE
			if((($opt2=='num_views' OR $opt2=='UnitPrice' OR $opt2=='likes' OR $opt2=='wants' OR $opt2=='USD_Price') AND $val2!='' AND $condition2!='like' AND is_numeric($val2))) 
				$cond .= "$operator $opt2 $condition2 $val2";
			
			
			//SPECIAL CASE
			if($opt1=='dateAdded' AND $val1!='' AND $condition=='>=' AND $opt2=='dateAdded' AND $val2!='' AND $condition2=='<=')
				$operator = 'AND';
			
			if($opt1=='dateReleased' AND $val1!='' AND $condition=='>=' AND $opt2=='dateReleased' AND $val2!='' AND $condition2=='<=')
				$operator = 'AND';
			
			if($opt1=='num_views' AND $val1!='' AND $condition=='>=' AND $opt2=='num_views' AND $val2!='' AND $condition2=='<=')
				$operator = 'AND';
			
			//Views 
			$op="";
			if($opt2=='num_views' AND $val2!='' AND $condition2!='like' AND is_numeric($val2)){
				$cond .= " $operator num_views $condition2 '$val2'";
			}
			
			//dateAdded 
			if($opt2=='dateAdded' AND $val2!='' AND $condition2!='like') 
				$cond .= " $operator dUploaded $condition2 '$val2'";
				
			//dateReleased
			if($opt2=='dateReleased' AND $val2!='' AND $condition2!='like') 
				$cond .= " $operator dReleased $condition2 '$val2'";
			
			$cond = ($cond=="") ? "" : " AND $cond";
			$WHERE  .=  " $cond";
		}
		
		$data['POST']      = $_POST;
		if(isset($Reset)){
			$data['POST'] = array();
			$data['quarterStr'] = $this->quaterStr('condition');
			$data['DateFrom'] = $this->perQuarterMonths('prevMonth');
			$data['DateTo']   = $this->perQuarterMonths('nextMonth');
		}
		
		$sql = "SELECT itemID, num_views, likes, wants, itemCode, item_image, itemName, pstatus, ptype, poutlet_status, ppremium_type, pmaterial, pbrand, full_name, cName, publish,disapprove, UnitPrice, USD_Price, dUploaded, dReleased
				FROM item_db_reports		 
				$WHERE ORDER BY num_views DESC";
				
		$sql_csv = "SELECT cName as Country, num_views as Views, likes as Likes, wants as Wants, itemCode as Item_Code, itemName as Item_Name, pstatus as Status, ptype as Type, poutlet_status as Outlet_Status, 
					ppremium_type as Premium_Type, pmaterial as Material_Type, pbrand as Brand, full_name as User, publish as Publish, disapprove as Disapprove, UnitPrice, USD_Price, dUploaded as Date_Uploaded, dReleased as Date_Released
				FROM item_db_reports		 
				$WHERE ORDER BY num_views DESC";
		
		//generate csv file
		$this->generateCSVFile('item_views',$sql_csv,'SMBi_Item_Views.csv');
		$data['csvFile']			= "SMBi_Item_Views.csv";
		
		$ctr = $this->db->query($sql);
		$ctr = $ctr->result_array();
		$data['totrec'] = count($ctr);
		$data['limit']  = $limit;
		
		$sql 	 = $this->db->query($sql);
		$items	 = $sql->result_array();
		
	
		$table= "<table id='large' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:11px;' class='iLike_Result_Table tablesorter'>
				<thead>
				<tr>
					<th style='width:25px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Views  	  		  </b></th> 
					<th style='width:25px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Likes  	  		  </b></th> 
					<th style='width:25px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Wants  	  		  </b></th> 
					<th style='width:100px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >      <b>Item Code  	  	  </b></th> 
					<th style='width:19px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Image   	 	      </b></th> 
					<th style='width:100px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >      <b>Name  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Status  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Type  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Outlet  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Premium  	  		  </b></th> 
					<th style='width:50px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Material  	  	  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Brand  	  	  	  </b></th> 
					<th style='width:30px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>User  	  		  </b></th> 
					<th style='width:80px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Country  	  		  </b></th> 
					<th style='width:30px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Publish  	  		  </b></th> 
					<th style='width:30px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Disapprove  	  	  </b></th> 
					<th style='width:60px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>L. Price  	  	  </b></th> 
					<th style='width:60px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>US. Price  	  	  </b></th> 
					<th style='width:80px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Uploaded  	  	  </b></th> 
					<th style='width:80px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Released  	  	  </b></th> 
				</tr>
				</thead>
				<tbody>";
				 
					$x = $limit;			
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					$orig_itemName="";
					foreach($items as $r) { 
					extract($r);
					$ptype = ($ptype=='') 	   ? 'Uncategorize' : $ptype;
					$publish = ($publish=='y') ? 'Yes' : 'No';
					$disapprove = ($disapprove=='y') ? 'Yes' : 'No';
					$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
					$orig_itemName=$itemName;
					if(strlen($itemName)>=15)
							$itemName = substr($itemName,0,15)."...";
							
					$poutlet_status = ($poutlet_status=="") ? "-" : $poutlet_status;
					$ppremium_type  = ($ppremium_type=="")  ? "-" : $ppremium_type;
					$likes 			= ($likes=="") 		? 0 : $likes;
					$wants 		    = ($wants=="") 		? 0 : $wants;
		$table.= "<tr>
				  <td>													<a onclick=\"viewDialog('item_database',$itemID)\" style='cursor:pointer;'><b>$num_views</b> </a> </td>
				  <td>													<a onclick=\"showVoters('iLike',$itemID)\" style='cursor:pointer;'><b>$likes</b></a>      																</td>
				  <td>													<a onclick=\"showVoters('iWant',$itemID)\" style='cursor:pointer;'><b>$wants</b></a>      																</td>
				  <td>													$itemCode      																	</td>
				  <td style='text-align:center;'>			    		<img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 	</td>
				  <td style='text-align:left;padding-left:5px;' title='$orig_itemName'>		<a href='".HTTP_PATH."gallery/itemInfo2/$itemID' target='_blank'>		$itemName</a>  </td>
				   <td style='text-align:left;padding-left:5px;'>		$pstatus																		</td>
				  <td style='text-align:left;padding-left:5px;'>		$ptype																			</td>
				  <td style='text-align:left;padding-left:5px;'>		$poutlet_status																	</td>
				  <td style='text-align:left;padding-left:5px;'>		$ppremium_type																	</td>
				  <td style='text-align:left;padding-left:5px;'>		$pmaterial																	    </td>
				  <td style='text-align:left;padding-left:5px;'>		$pbrand																	    	</td>
				  <td style='text-align:left;padding-left:5px;'>		$full_name																		</td>
				  <td style='text-align:left;padding-left:5px;'>		$cName																			</td>
				  <td style='text-align:center;'>						$publish																		</td>
				  <td style='text-align:center;'>						$disapprove																		</td>
				  <td style='text-align:center;'>						$UnitPrice																		</td>
				  <td style='text-align:center;'>						$USD_Price																		</td>
				  <td style='text-align:center;'>				        ". $dUploaded ."										</td>
				  <td style='text-align:center;'>				        ". $dReleased ."										</td>
				</tr>";}
		$table.= "</tbody>";
					if(!$items)
						$table.=  "<tr><td colspan='20'>No match found.</td></tr>";
		$table.= "</table>";	
		
		$data['pagination']	= true;
		$data['table'] 		= $table;
		
	
		
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
	
	
	function items_summary()
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(73,'REVIEW');
		
		$filter_WHERE="WHERE items.countryID = $cID";
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/BU_activeness_index> Activeness of Business Units Index </a>';
		
		$groupBy      = "";
		if($view=='gCountry'){
			$data['vType'] = $view;
		}elseif($view=='gYear'){
			$filter_WHERE .= "  AND YEAR(items.dateAdded) = $year";
			$data['vType'] = $view;
			$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
			$data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/BU_activeness_per_Year/$cID'> Activeness of Business Units per Year</a>";
		}elseif($view=='gMonth'){
			$filter_WHERE .= " AND YEAR(items.dateAdded) = $year AND MONTH(items.dateAdded) = $month";
			$data['vType'] = $view;
			
			$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
			$data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/BU_activeness_per_Year/$cID'> Activeness of Business Units per Year</a>";
			$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
			$data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/BU_activeness_per_Month/$cID/$year'> Activeness of Business Units per Month</a>";
		}	
		
		if($cID==0){
			$filter_WHERE = "WHERE items.countryID != 0";
		}if($cID==0 AND $view=='gYear'){
			$filter_WHERE = "WHERE items.countryID != 0 AND YEAR(items.dateAdded) = $year";
		}if($cID==0 AND $view=='gMonth'){
			$filter_WHERE = "WHERE items.countryID != 0 AND YEAR(items.dateAdded) = $year AND MONTH(items.dateAdded) = $month";
		}
		
		
		$table						= 'item_division';
		$data['vfile']				= 'item_division.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		$data['countryID']			= $cID;
		$data['cyear']				= $year;	
		$data['previewType']		= $view;
		
		
		//POSM TYPE;
		$arr = '';
		$sql   = $this->db->query("SELECT DISTINCT(items.POSMTypeID) as fieldValue, (SELECT typeName FROM POSM_Type WHERE POSM_Type.id = items.POSMTypeID) as col, AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice, 
								   COUNT(items.id) as num_items, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year, country.id as cID 
								   FROM   items 	
								   LEFT   JOIN country 		ON country.id  = items.countryID
								   $filter_WHERE
								   GROUP BY items.POSMTypeID ORDER BY YEAR(items.dateAdded) DESC, MONTH(items.dateAdded) DESC");
		$arr[] = array('table'=>'POSM TYPE',
					   'fld'=>'POSMTypeID',
					   'rows'=>$sql->result_array());
		
		//POSM STATUS
		$sql   = $this->db->query("SELECT DISTINCT(items.POSMStatusID) as fieldValue, (SELECT statusName FROM POSM_Status WHERE POSM_Status.id = items.POSMStatusID) as col, AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice, 
								   COUNT(items.id) as num_items, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year, country.id as cID 
								   FROM   items 	
								   LEFT   JOIN country 		ON country.id  = items.countryID
								   $filter_WHERE
								   GROUP BY items.POSMStatusID ORDER BY YEAR(items.dateAdded) DESC, MONTH(items.dateAdded) DESC");
		$arr[] = array('table'=>'POSM STATUS',
					   'fld'=>'POSMStatusID',
					   'rows'=>$sql->result_array());
		
		//OUTLET TYPE
		$sql   = $this->db->query("SELECT DISTINCT(items.OUTLETStatusID) as fieldValue, (SELECT statusName FROM OUTLET_Status WHERE OUTLET_Status.id = items.OUTLETStatusID) as col, AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice, 
								   COUNT(items.id) as num_items, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year, country.id as cID 
								   FROM   items 	
								   LEFT   JOIN country 		ON country.id  = items.countryID
								   $filter_WHERE
								   GROUP BY items.OUTLETStatusID ORDER BY YEAR(items.dateAdded) DESC, MONTH(items.dateAdded) DESC");
		$arr[] = array('table'=>'SERVICE ITEM OUTLET TYPE',
					   'fld'=>'OUTLETStatusID',
					   'rows'=>$sql->result_array());
		
		//PREMIUM ITEM TYPE
		$sql   = $this->db->query("SELECT DISTINCT(items.PremiumTypeID) as fieldValue, (SELECT premiumTypeName FROM premiumItemType WHERE premiumItemType.id = items.PremiumTypeID) as col, AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice, 
								   COUNT(items.id) as num_items, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year, country.id as cID 
								   FROM   items 	
								   LEFT   JOIN country 		ON country.id  = items.countryID
								   $filter_WHERE
								   GROUP BY items.PremiumTypeID ORDER BY YEAR(items.dateAdded) DESC, MONTH(items.dateAdded) DESC");
		$arr[] = array('table'=>'PREMIUM ITEM TYPE',
					   'fld'=>'PremiumTypeID',
					   'rows'=>$sql->result_array());
		
		//MATERIAL TYPE
		$sql   = $this->db->query("SELECT DISTINCT(items.MaterialTypeID) as fieldValue, (SELECT materialName FROM MATERIAL_Type WHERE MATERIAL_Type.id = items.MaterialTypeID) as col, AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice, 
								   COUNT(items.id) as num_items, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year, country.id as cID 
								   FROM   items 	
								   LEFT   JOIN country 		ON country.id  = items.countryID
								   $filter_WHERE
								   GROUP BY items.MaterialTypeID ORDER BY YEAR(items.dateAdded) DESC, MONTH(items.dateAdded) DESC");
		$arr[] = array('table'=>'MATERIAL TYPE',
						'fld'=>'MaterialTypeID',
						'rows'=>$sql->result_array());
		
		//BRAND
		$sql   = $this->db->query("SELECT DISTINCT(items.brandID) as fieldValue, (SELECT brandName FROM brands WHERE brands.id = items.brandID) as col, AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice, 
								   COUNT(items.id) as num_items, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year, country.id as cID 
								   FROM   items 	
								   LEFT   JOIN country 		ON country.id  = items.countryID
								   $filter_WHERE
								   GROUP BY items.brandID ORDER BY YEAR(items.dateAdded) DESC, MONTH(items.dateAdded) DESC");
		$arr[] = array('table'=>'BRAND',
					   'fld'=>'brandID',
					   'rows'=>$sql->result_array());
		
		$data['results'] = $arr;
		
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
	
	function voters_summary($action='',$id='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(75,'REVIEW');
		
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(52);
		
		$WHERE  = "";
		if($_SESSION['super_admin']!='y'  AND $_SESSION['countryID']!=0)
			$WHERE = "WHERE countryID =".$_SESSION['countryID']." ";
		
		//$votingCampaignID 	= $id;
		
		$table						= 'item_views';
		$data['vfile']				= 'voters_summary.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/voters_summary> Voters Summary </a>';
		
		//TOTAL NUMBER OF ROWS			
		extract($_POST);
		$cond="";
		$having="";
		$limit =isset($selpage)? $selpage:0;
		$campaignType = "";
		
		if(!isset($Submit) AND !isset($selpage)){
			$data['iLike'] = 'iLike';
			$data['iWant'] = 'iWant';
		}
		
		if(isset($Submit) OR isset($selpage))
		{	
			$val1 = mysql_real_escape_string($val1);
			$val1 = trim($val1);
			$val2 = mysql_real_escape_string($val2);
			$val2 = trim($val2);
			
			
			$condition = '';
			switch($cond1){
				case 'equal': 
					$condition = '=';
				break;
				case 'containing': 
					$condition = 'like';
				break;
				case 'greaterThan': 
					$condition = '>=';
				break;
				case 'lessThan': 
					$condition = '<=';
				break;
			}
			
			//fname, lname, gender, email, department, camapignName, countryName
			if(($opt1=='fname' OR $opt1=='lname' OR $opt1=='lname' OR $opt1=='gender' OR $opt1=='department' OR $opt1=='camapaignName' OR $opt1=='countryName') AND $val1!='')
			{
				if($condition=='=')
					$cond = "  $opt1 $condition '$val1'";
				if($condition=='like')
					$cond = "  $opt1 $condition '%$val1%'";
			}
			
			//Year of birth, age
			if(($opt1=='year_of_birth' OR $opt1=='age') AND $val1!='' AND $condition!='like' AND is_numeric($val1)) 
				$cond = "  $opt1 $condition $val1";
			
	
			//dateReleased
			if($opt1=='dateAdded' AND $val1!='' AND $condition!='like') 
				$cond = "  tdate $condition '$val1'";
			
			/*2ND SET*/
			switch($cond2){
				case 'equal': 
					$condition2 = '=';
				break;
				case 'containing': 
					$condition2 = 'like';
				break;
				case 'greaterThan': 
					$condition2 = '>=';
				break;
				case 'lessThan': 
					$condition2 = '<=';
				break;
			}
			
			if($val1!=""){
				//fname, lname, gender, email, department, camapignName, countryName
				if(($opt2=='fname' OR $opt2=='lname' OR $opt2=='lname' OR $opt2=='gender' OR $opt2=='department' OR $opt2=='camapaignName' OR $opt2=='countryName') AND $val2!='')
				{
					if($condition2=='=')
						$cond .= " $operator $opt2 $condition2 '$val2'";
					if($condition2=='like')
						$cond .= " $operator $opt2 $condition2 '%$val2%'";
				}
				
				//Year of birth, age
				if(($opt2=='year_of_birth' OR $opt2=='age') AND $val2!='' AND $condition2!='like' AND is_numeric($val2)) 
					$cond .= " $operator $opt2 $condition2 $val2";
				
				//dateReleased
				if($opt2=='dateAdded' AND $val2!='' AND $condition2!='like') 
					$cond .= " $operator tdate $condition2 '$val2'";
			}
			
			//CAMPAIGN TYPE
			$cond .= ($cond!="") ? " AND " : "";
			if(isset($iLike) AND isset($iWant))
				$cond .= " (campaignType='iLike' OR  campaignType='iWant') ";
			elseif(isset($iLike) AND !isset($iWant))
				$cond .= " campaignType='iLike' ";
			elseif(isset($iWant) AND !isset($iLike))
				$cond .= " campaignType='iWant' ";
			if(!isset($iLike) AND !isset($iWant))
				$cond .= " (campaignType!='iLike' AND  campaignType!='iWant') ";
			
			$cond = ($cond=="") ? "" : "WHERE $cond";
			$WHERE  .=  " $cond";
		}
		
		$data['POST'] = $_POST;
		if(isset($Reset)){
			$data['POST'] = array();
			$WHERE='';
		}
		
		$sql   = "SELECT campaignID, vID, fname, lname, gender, email, department, year_of_birth, age, 
				  tdate, campaignType, campaignName, countryName
				  FROM voters_reports
				  $WHERE   ORDER BY tdate DESC, campaignName ASC, countryName ASC";
	
		$ctr = $this->db->query($sql);
		$ctr = $ctr->result_array();
		$data['totrec'] = count($ctr);
		$data['limit']  = $limit;
		
		$sql 	 = $this->db->query($sql." LIMIT $limit,20");
		$items	 = $sql->result_array();
		
	
		$table= "<table id='large' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:12px;' class='iLike_Result_Table tablesorter'>
				<thead>
				<tr>
					<th style='width:10px;text-align:center;color:white;padding:0px' bgcolor='#bb1d1d'>   					   <b>No 		 		  	</b></th> 
					<th style='width:25px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>First Name  	  		</b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Last Name  	  	  	</b></th> 
					<th style='width:19px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Gender   	 	      	</b></th> 
					<th style='width:100px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >      <b>Email  	  		  	</b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Dept  	  			</b></th> 
					<th style='width:50px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Year  	  		  	</b></th> 
					<th style='width:80px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Age  	  		  		</b></th> 
					<th style='width:50px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>C. Type  	  			</b></th> 
					<th style='width:70px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Campaign Name  	  	</b></th> 
					<th style='width:70px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Country  	  			</b></th> 
					<th style='width:100px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Date  	  	  		</b></th> 
					<th style='width:50px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Votes  	  	  		</b></th> 
				</tr>
				</thead>
				<tbody>";
				 
					$x = $limit;			
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					$orig_itemName="";
					foreach($items as $r) { 
					extract($r);
					$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
		$table.= "<tr>
				  <td>													$x      		</td>
				  <td style='text-align:left;padding-left:5px;'>		$fname  		</td>
				  <td style='text-align:left;padding-left:5px;'>		$lname  		</td>
				  <td style='text-align:left;padding-left:5px;'>		$gender  		</td>
				  <td style='text-align:left;padding-left:5px;'>		$email  		</td>
				  <td style='text-align:left;padding-left:5px;'>		$department  	</td>
				  <td style='text-align:center;padding-left:5px;'>		$year_of_birth  </td>
				  <td style='text-align:center;padding-left:5px;'>		$age  			</td>
				  <td style='text-align:left;padding-left:5px;'>		$campaignType  	</td>
				  <td style='text-align:left;padding-left:5px;'>		$campaignName  	</td>
				  <td style='text-align:left;padding-left:5px;'>		$countryName  	</td>
				  <td style='text-align:left;padding-left:5px;'>		$tdate  		</td>
				  <td style='text-align:center;'>				        <a onclick=\"viewDialog('$campaignType',$campaignID,$vID)\" style='cursor:pointer;'> Details </a>	</td>
				</tr>";}
		$table.= "</tbody>";
					if(!$items)
						$table.=  "<tr><td colspan='14'>No match found.</td></tr>";
		$table.= "</table>";	
		
		
		$data['table'] 		= $table;

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
	
	function item_views_eCat($action='',$id='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(70,'REVIEW');
		
		$filter_WHERE='WHERE ec_items.id IN (SELECT DISTINCT(itemID) FROM ecitem_views)';
		
		//$votingCampaignID 	= $id;
		
		$table						= 'item_views';
		$data['vfile']				= 'item_views_eCat.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/item_views_eCat> eCatalogue Views per Item </a>';
		
		//TOTAL NUMBER OF ROWS			
		
		extract($_POST);
		$WHERE 	 =  $filter_WHERE;
	
		if(isset($filter))
		{
			$WHERE 	 = '';
			if($cyear!=''  AND $cyear!='all') 	 	 $WHERE  .=  " YEAR(ec_items.dateReleased) = $cyear AND";
			
			if($fmonth!='' AND $fmonth!='all' AND $tmonth!='' AND $tmonth!='all'){ 	 	 
					$WHERE  .=  " MONTH(ec_items.dateAdded) >= $fmonth AND  MONTH(ec_items.dateAdded) <= $tmonth AND";
			}
			$WHERE  =  substr("WHERE ec_items.id IN (SELECT DISTINCT(itemID) FROM ecitem_views) AND ".$WHERE,0,-4);
		}
		
		$data['POST'] = $_POST;
		
		$sql   = $this->db->query("SELECT ec_items.id as itemID, itemCode, ecID,
								  (SELECT image FROM ecitems_images WHERE defaultStatus = 1 AND itemID = ec_items.id) as item_image,
								   itemName, (SELECT COUNT(id) FROM ecitem_views WHERE ecitem_views.itemID = ec_items.id) num_views, dateReleased
								   FROM ec_items 		 
								   $WHERE
								   GROUP BY ec_items.id ORDER BY num_views DESC");
		$data['reports']   = $sql->result_array();
		

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
	
	function item_distribution_Preview($view='',$countryID='',$month='',$year='',$fld='',$fld_val='')
	{
		$table='';
		$data['typeView']      = $view;
		$data['fld']       = $fld;
		$data['fld_val']   = $fld_val;
		$data['month']     = ($month=='') 	  ? 'null' : $this->month($month);
		$data['year']      = ($year=='') 	  ? 'null' : $year;
		$data['countryID'] = ($countryID=='') ? 'null' : $countryID;
		
		//POSM TYPE
		//echo $fld;
		switch($fld){
		case 'POSMTypeID':
			$itm_type = "(SELECT typeName FROM POSM_Type WHERE POSM_Type.id = items.POSMTypeID)";
		break;		
		case 'POSMStatusID':
			$itm_type = "(SELECT statusName FROM POSM_Status WHERE POSM_Status.id = items.POSMStatusID)";
		break;
		case 'OUTLETStatusID':
			$itm_type = "(SELECT statusName FROM OUTLET_Status WHERE OUTLET_Status.id = items.OUTLETStatusID)";
		break;
		case 'PremiumTypeID':
			$itm_type = "(SELECT premiumTypeName FROM premiumItemType WHERE premiumItemType.id = items.PremiumTypeID)";
		break;
		case 'MaterialTypeID':
			$itm_type = "(SELECT materialName FROM MATERIAL_Type WHERE MATERIAL_Type.id = items.MaterialTypeID)";
		break;
		case 'brandID':
			$itm_type = "(SELECT brandName FROM brands WHERE brands.id = items.brandID)";
		break;
		}
		
		$fld = "AND items.$fld = $fld_val"; 
		
		$data['month_in_word'] = $this->month($month);
		
		//print_r($_POST);
		extract($_POST);
		$data['user_id'] = isset($user_id) ? $user_id : 0;
		$cond='';
		
		if(isset($Submit) OR isset($selpage))
		{	
			$val1 = mysql_real_escape_string($val1);
			$val2 = mysql_real_escape_string($val2);
			
			$condition = '';
			switch($cond1){
				case 'equal': 
					$condition = '=';
				break;
				case 'containing': 
					$condition = 'like';
				break;
				case 'greaterThan': 
					$condition = '>=';
				break;
				case 'lessThan': 
					$condition = '<=';
				break;
			}
			
			//ITEM CODE
			if($opt1=='itemCode' AND $val1!='')
			{
				if($condition=='=')
					$cond = " AND items.itemCode $condition '$val1'";
				if($condition=='like')
					$cond = " AND items.itemCode $condition '%$val1%'";
			}
			
			//ITEM NAME
			if($opt1=='itemName')
			{
				if($condition=='=')
					$cond = " AND items.itemName $condition '$val1'";
				if($condition=='like')
					$cond = " AND items.itemName $condition '%$val1%'";
			}
			
			//USER NAME
			if($opt1=='user_id')
			{
				if($condition=='=')
					$cond = " AND admin_users.full_name $condition '$val1'";
				if($condition=='like')
					$cond = " AND admin_users.full_name $condition '%$val1%'";
			}
			
			//PUBLISH
			if($opt1=='publish')
			{
				if($condition=='=')
					$val1 = ($val1=='yes' OR $val1=='Yes') ? 'y' : 'n';
					$cond = " AND items.publish $condition '$val1'";
				if($condition=='like')
					$cond = " AND items.publish $condition '%$val1%'";
			}
			
			//LOCAL PRICE
			if($opt1=='UnitPrice' AND $val1!='' AND $condition!='like' AND is_numeric($val1)) 
				$cond = " AND items.UnitPrice $condition $val1";
			
			//USD PRICE
			if($opt1=='USD_Price' AND $val1!='' AND $condition!='like' AND is_numeric($val1)) 
				$cond = " AND items.USD_Price $condition $val1";
			
			//dateAdded
			if($opt1=='dateAdded' AND $val1!='' AND $condition!='like') 
				$cond = " AND items.dateAdded $condition '$val1'";
				
			//dateReleased
			if($opt1=='dateReleased' AND $val1!='' AND $condition!='like') 
				$cond = " AND items.dateReleased $condition '$val1'";
			
			/*2ND SET*/
			switch($cond2){
				case 'equal': 
					$condition2 = '=';
				break;
				case 'containing': 
					$condition2 = 'like';
				break;
				case 'greaterThan': 
					$condition2 = '>=';
				break;
				case 'lessThan': 
					$condition2 = '<=';
				break;
			}
			
			//ITEM CODE
			if($opt2=='itemCode' AND $val2!='')
			{
				if($condition2=='=')
					$cond .= " $operator items.itemCode $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator items.itemCode $condition2 '%$val2%'";
			}
			
			//ITEM NAME
			if($opt2=='itemName' AND $val2!='')
			{
				if($condition2=='=')
					$cond .= " $operator items.itemName $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator items.itemName $condition2 '%$val2%'";
			}
			
			//USER NAME
			if($opt2=='user_id' AND $val2!='')
			{
				if($condition2=='=')
					$cond .= " $operator admin_users.full_name $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator admin_users.full_name $condition2 '%$val2%'";
			}
			
			//PUBLISH
			if($opt2=='publish' AND $val2!='')
			{
				if($condition2=='=')
					$val2 = ($val2=='yes' OR $val2=='Yes') ? 'y' : 'n';
					$cond .= " $operator items.publish $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator items.publish $condition2 '%$val2%'";
			}
			
			
			//LOCAL PRICE
			if($opt2=='UnitPrice' AND $val2!='' AND $condition2!='like' AND is_numeric($val2)) 
				$cond .= "$operator items.UnitPrice $condition2 $val2";
			
			//USD PRICE
			if($opt2=='USD_Price' AND $val2!='' AND $condition2!='like' AND is_numeric($val2)) 
				$cond .= " $operator items.USD_Price $condition2 $val2";
			
			//SPECIAL CASE
			if($opt1=='dateAdded' AND $val1!='' AND $condition=='>=' AND $opt2=='dateAdded' AND $val2!='' AND $condition2=='<=')
				$operator = 'AND';
			
			if($opt1=='dateReleased' AND $val1!='' AND $condition=='>=' AND $opt2=='dateReleased' AND $val2!='' AND $condition2=='<=')
				$operator = 'AND';
			
			//dateAdded 
			if($opt2=='dateAdded' AND $val2!='' AND $condition2!='like') 
				$cond .= " $operator items.dateAdded $condition2 '$val2'";
				
			//dateReleased
			if($opt2=='dateReleased' AND $val2!='' AND $condition2!='like') 
				$cond .= " $operator items.dateReleased $condition2 '$val2'";
		}
		
		$data['POST'] = $_POST;
		
		if(isset($Reset)){
			$data['POST'] = array();
			$cond='';
		}
		
		//BREAD CRUMBS
		$HTTP_PATH 					 = HTTP_PATH."report/iLike";
		$data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
		$data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/BU_activeness_index> Activeness of Business Units Index </a>';
		
		$limit =isset($selpage)? $selpage:0;
		
		$WHERE = "WHERE items.countryID = $countryID";
		if($countryID==0)
			$WHERE = "WHERE items.countryID != 0";
		
		if($view=='gCountry'){
			
			$sql = "SELECT items.id as itemID, itemName, $itm_type as ptype, itemCode, 
					(SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, items.dateAdded as dUploaded, full_name, publish,
					UnitPrice, USD_Price, items.dateReleased as dReleased
					FROM items LEFT JOIN admin_users ON admin_users.id = items.user_id
					$WHERE $fld $cond ORDER BY items.id DESC";
		}if($view=='gYear'){
			$sql = "SELECT items.id as itemID, itemName, $itm_type as ptype, itemCode, 
					(SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, items.dateAdded as dUploaded, full_name, publish,
					UnitPrice, USD_Price, items.dateReleased as dReleased
					FROM items LEFT JOIN admin_users ON admin_users.id = items.user_id
					$WHERE $fld $cond AND YEAR(items.dateAdded)=$year ORDER BY items.id DESC";
					
			$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
			$data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/BU_activeness_per_Year/$countryID'> Activeness of Business Units per Year</a>";		
		}if($view=='gMonth'){
			$sql = "SELECT items.id as itemID, itemName, $itm_type as ptype, itemCode, 
					(SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, items.dateAdded as dUploaded, full_name, publish,
					UnitPrice, USD_Price, items.dateReleased as dReleased
					FROM items LEFT JOIN admin_users ON admin_users.id = items.user_id
					$WHERE $fld $cond AND YEAR(items.dateAdded)=$year AND MONTH(items.dateAdded)=$month ORDER BY items.id DESC";
					
			$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
			$data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/BU_activeness_per_Year/$countryID'> Activeness of Business Units per Year</a>";
			$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
			$data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/BU_activeness_per_Month/$countryID/$year'> Activeness of Business Units per Month</a>";
		}
		
		$ctr = $this->db->query($sql);
		$ctr = $ctr->result_array();
		$data['totrec'] = count($ctr);
		$data['limit']  = $limit;
		
		$sql 	 = $this->db->query($sql." LIMIT $limit,20");
		$items	 = $sql->result_array();
	
		//print_r($items);
			
		$table = "<table id='large' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:12px;' class='iLike_Result_Table tablesorter'>
				<thead>
				<tr>
					<th style='width:10px;text-align:center;color:white;padding:0px' bgcolor='#bb1d1d'>   					   <b>No 		 		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Item Code  	  	  </b></th> 
					<th style='width:19px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Image   	 	      </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Name  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Type  	  		  </b></th> 
					<th style='width:30px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>User  	  		  </b></th> 
					<th style='width:30px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Publish  	  		  </b></th> 
					<th style='width:70px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Local Price  	  		  </b></th> 
					<th style='width:70px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>USD Price  	  		  </b></th> 
					<th style='width:75px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Uploaded  	  </b></th> 
					<th style='width:75px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Released  	  </b></th> 
				</tr>
				</thead>
				<tbody>";
				 
					$x = 0;			
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					foreach($items as $r) { 
					extract($r);
					$ptype = ($ptype=='') 	   ? 'Uncategorize' : $ptype;
					$publish = ($publish=='y') ? 'Yes' : 'No';
					$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 	
		$table .="<tr>
				  <td >													$x      																		</td>
				  <td >													$itemCode      																	</td>
				  <td  style='text-align:center;'>			    		<img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 			</td>
				  <td  style='text-align:left;padding-left:5px;'>		<a href='".HTTP_PATH."gallery/itemInfo2/$itemID' target='_blank'>		$itemName</a>   </td>
				  <td  style='text-align:left;padding-left:5px;'>		$ptype											</td>
				   <td  style='text-align:center;'>						$full_name										</td>
				  <td  style='text-align:center;'>						$publish										</td>
				  <td style='text-align:center;'>						$UnitPrice																		</td>
				  <td style='text-align:center;'>						$USD_Price																		</td>
				  <td style='text-align:center;'>				        ". $dUploaded ."										</td>
				  <td style='text-align:center;'>				        ". $dReleased ."										</td>
				</tr>";}
		$table .="</tbody>";
					if(!$items)
						$table .= "<tr><td colspan='11'>No match found.</td></tr>";
		$table .="</table>";
		
		$data['POST']		= $_POST;
		$data['table'] 		= $table;
		$data['vfile']		= 'item_distribution_Preview.php';
		$data['title']		= 'Items Preview';
		
		
		
	
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
	
	function BU_items($view='',$countryID='',$month='',$year='',$user_id='')
	{
		$data['typeView']  = $view;
		$data['month']     = ($month=='') 	  ? 'null' : $month;
		$data['year']      = ($year=='') 	  ? 'all' :  $year;
		$data['user_id']   = ($user_id=='')   ? 'null' : $user_id;
		$data['countryID'] = ($countryID=='') ? 'null' : $countryID;
		$data['month_in_word'] = $this->month($month);
		
		//print_r($_POST);
		extract($_POST);
		$cond='';
		
		if(isset($Submit) OR isset($selpage))
		{	
			$val1 = mysql_real_escape_string($val1);
			$val1 = str_replace("'","",$val1);
			$val1 = trim($val1);
			$val2 = mysql_real_escape_string($val2);
			$val2 = str_replace("'","",$val2);
			$val2 = trim($val2);
			
			$condition = '';
			switch($cond1){
				case 'equal': 
					$condition = '=';
				break;
				case 'containing': 
					$condition = 'like';
				break;
				case 'greaterThan': 
					$condition = '>=';
				break;
				case 'lessThan': 
					$condition = '<=';
				break;
			}
			
			//COUNTRY
			if($opt1=='cName' AND $val1!='')
			{
				if($condition=='=')
					$cond = " AND cName $condition '$val1'";
				if($condition=='like')
					$cond = " AND cName $condition '%$val1%'";
			}
			
			//ITEM CODE
			if($opt1=='itemCode' AND $val1!='')
			{
				if($condition=='=')
					$cond = " AND itemCode $condition '$val1'";
				if($condition=='like')
					$cond = " AND itemCode $condition '%$val1%'";
			}
			
			//ITEM NAME
			if($opt1=='itemName')
			{
				if($condition=='=')
					$cond = " AND itemName $condition '$val1'";
				if($condition=='like')
					$cond = " AND itemName $condition '%$val1%'";
			}
			
			//ITEM TYPE
			if($opt1=='itemType')
			{
				if($condition=='=')
					$cond = " AND ptype $condition '$val1'";
				if($condition=='like')
					$cond = " AND ptype $condition '%$val1%'";
			}
			
			//USER NAME
			if($opt1=='user_id')
			{
				if($condition=='=')
					$cond = " AND full_name $condition '$val1'";
				if($condition=='like')
					$cond = " AND full_name $condition '%$val1%'";
			}
			
			//PUBLISH
			if($opt1=='publish')
			{
				if($condition=='=')
					$val1 = ($val1=='yes' OR $val1=='Yes') ? 'y' : 'n';
					$cond = " AND publish $condition '$val1'";
				if($condition=='like')
					$cond = " AND publish $condition '%$val1%'";
			}
			
			//LOCAL PRICE
			if($opt1=='UnitPrice' AND $val1!='' AND $condition!='like' AND is_numeric($val1)) 
				$cond = " AND UnitPrice $condition $val1";
			
			//USD PRICE
			if($opt1=='USD_Price' AND $val1!='' AND $condition!='like' AND is_numeric($val1)) 
				$cond = " AND USD_Price $condition $val1";
			
			//dateAdded
			if($opt1=='dateAdded' AND $val1!='' AND $condition!='like') 
				$cond = " AND dUploaded $condition '$val1'";
				
			//dateReleased
			if($opt1=='dateReleased' AND $val1!='' AND $condition!='like') 
				$cond = " AND dReleased $condition '$val1'";
			
			/*2ND SET*/
			switch($cond2){
				case 'equal': 
					$condition2 = '=';
				break;
				case 'containing': 
					$condition2 = 'like';
				break;
				case 'greaterThan': 
					$condition2 = '>=';
				break;
				case 'lessThan': 
					$condition2 = '<=';
				break;
			}
			
			//COUNTRY
			if($opt2=='cName' AND $val2!='')
			{
				if($condition2=='=')
					$cond .= " $operator cName $condition '$val1'";
				if($condition2=='like')
					$cond .= " $operator cName $condition '%$val1%'";
			}
			
			//ITEM CODE
			if($opt2=='itemCode' AND $val2!='')
			{
				if($condition2=='=')
					$cond .= " $operator itemCode $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator itemCode $condition2 '%$val2%'";
			}
			
			//ITEM NAME
			if($opt2=='itemName' AND $val2!='')
			{
				if($condition2=='=')
					$cond .= " $operator itemName $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator itemName $condition2 '%$val2%'";
			}
			
			//ITEM TYPE
			if($opt2=='itemType' AND $val2!='')
			{
				if($condition2=='=')
					$cond .= " $operator ptype $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator ptype $condition2 '%$val2%'";
			}
			
			//USER NAME
			if($opt2=='user_id' AND $val2!='')
			{
				if($condition2=='=')
					$cond .= " $operator full_name $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator full_name $condition2 '%$val2%'";
			}
			
			//PUBLISH
			if($opt2=='publish' AND $val2!='')
			{
				if($condition2=='=')
					$val2  = ($val2=='yes' OR $val2=='Yes') ? 'y' : 'n';
					$cond .= " $operator publish $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator publish $condition2 '%$val2%'";
			}
			
			
			//LOCAL PRICE
			if($opt2=='UnitPrice' AND $val2!='' AND $condition2!='like'  AND is_numeric($val2)) 
				$cond .= "$operator UnitPrice $condition2 $val2";
			
			//USD PRICE
			if($opt2=='USD_Price' AND $val2!='' AND $condition2!='like'  AND is_numeric($val2)) 
				$cond .= " $operator USD_Price $condition2 $val2";
			
			//SPECIAL CASE
			if($opt1=='dateAdded' AND $val1!='' AND $condition=='>=' AND $opt2=='dateAdded' AND $val2!='' AND $condition2=='<=')
				$operator = 'AND';
			
			if($opt1=='dateReleased' AND $val1!='' AND $condition=='>=' AND $opt2=='dateReleased' AND $val2!='' AND $condition2=='<=')
				$operator = 'AND';
			
			//dateAdded 
			if($opt2=='dateAdded' AND $val2!='' AND $condition2!='like') 
				$cond .= " $operator dUploaded $condition2 '$val2'";
				
			//dateReleased
			if($opt2=='dateReleased' AND $val2!='' AND $condition2!='like') 
				$cond .= " $operator dReleased $condition2 '$val2'";
		}
		
		$data['POST'] = $_POST;
		
		if(isset($Reset)){
			$data['POST'] = array();
			$cond='';
		}
		
		$WHERE = "WHERE cID = $countryID";
		if($countryID==0){
			$WHERE = "WHERE cID!=0";
		}
		
		//BREAD CRUMBS
		$data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	
		
		$limit =isset($selpage)? $selpage:0;
		if($view=='gCountry'){
			$sql = "SELECT itemID, itemName, ptype, itemCode, cName,
			    item_image, dUploaded, full_name, publish,  UnitPrice, USD_Price,  dReleased
				FROM item_db_reports
				$WHERE $cond ORDER BY iID DESC";	
			$data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/BU_activeness_index> Activeness of Business Units Index </a>';

		}
		elseif($view=='gYear'){
		
			$sql = "SELECT itemID, itemName, ptype, itemCode, cName,
					item_image, dUploaded, full_name, publish, UnitPrice, USD_Price,  dReleased 
					FROM item_db_reports 
					$WHERE $cond AND YEAR(dUploaded) = $year ORDER BY iID DESC";
			$data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/BU_activeness_index> Activeness of Business Units Index </a>';		
			$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
			$data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/BU_activeness_per_Year/$countryID'> Activeness of Business Units per Year</a>";
		}
		elseif($view=='gMonth')
		{
			$sql = "SELECT itemID, itemName, ptype, itemCode, cName,
					item_image, dUploaded, full_name, publish, UnitPrice, USD_Price,  dReleased 
				    FROM item_db_reports 
					$WHERE $cond AND MONTH(dUploaded) = $month AND YEAR(dUploaded) = $year ORDER BY iID DESC";
			$data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/BU_activeness_index> Activeness of Business Units Index </a>';		
			$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
			$data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/BU_activeness_per_Year/$countryID'> Activeness of Business Units per Year</a>";
			$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
			$data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/BU_activeness_per_Month/$countryID/$year'> Activeness of Business Units per Month</a>";
		}
		elseif($view=='BU_activeness_Users')
		{
			$data['disable_user'] = TRUE;
			$sql = "SELECT itemID, itemName, ptype, itemCode, cName,
					item_image, dUploaded, full_name, publish, UnitPrice, USD_Price,  dReleased 
					FROM item_db_reports 
					WHERE cID = $countryID AND userID = $user_id $cond ORDER BY iID DESC";
					
			$data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/BU_activeness_Users>  Activeness of Users Index</a>';
		}
		elseif($view=='BU_activeness_Users_per_Year')
		{
			$data['disable_user'] = TRUE;
			$sql = "SELECT itemID, itemName, ptype, itemCode, cName,
					item_image, dUploaded, full_name, publish, UnitPrice, USD_Price,  dReleased 
					FROM item_db_reports 
					WHERE cID = $countryID AND userID = $user_id AND YEAR(dUploaded) = $year $cond ORDER BY iID DESC";
			
			$data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
			$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
			$data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/BU_activeness_Users> Activeness of Users Index  </a>';
			$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
			$data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/BU_activeness_Users_per_Year/$user_id/$countryID'> Activeness of Users per Year  </a>";

		}
		elseif($view=='BU_activeness_Users_per_Month')
		{
			$data['disable_user'] = TRUE;
			$sql = "SELECT itemID, itemName, ptype, itemCode, cName,
					item_image, dUploaded, full_name, publish, UnitPrice, USD_Price,  dReleased 
					FROM item_db_reports 
					WHERE cID = $countryID AND userID = $user_id AND YEAR(dUploaded) = $year AND MONTH(dUploaded) = $month $cond ORDER BY iID DESC";
			
			$data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
			$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
			$data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/BU_activeness_Users> Activeness of Users Index  </a>';
			$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
			$data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/BU_activeness_Users_per_Year/$user_id/$countryID'> Activeness of Users per Year  </a>";
			$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
			$data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/BU_activeness_Users_per_Month/$user_id/$countryID/$year'> Activeness of Users per Month  </a>";
		}
	
		$ctr = $this->db->query($sql);
		$ctr = $ctr->result_array();
		$data['totrec'] = count($ctr);
		$data['limit']  = $limit;
		
		$sql 	 = $this->db->query($sql." LIMIT $limit,20");
		$items	 = $sql->result_array();
		
	
		$table= "<table id='large' cellpadding='0' cellspacing='0' border=1 style='width:120%;font-size:12px;' class='iLike_Result_Table tablesorter'>
				<thead>
				<tr>
					<th style='width:10px;text-align:center;color:white;padding:0px' bgcolor='#bb1d1d'>   					   <b>No 		 		  </b></th> 
					<th style='width:10px;text-align:center;color:white;padding:0px' bgcolor='#bb1d1d'>   					   <b>Country 		 	  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Item Code  	  	  </b></th> 
					<th style='width:19px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Image   	 	      </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Name  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Type  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>User  	  		  </b></th> 
					<th style='width:30px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Publish  	  		  </b></th> 
					<th style='width:70px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Local Price  	  	  </b></th> 
					<th style='width:70px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>USD Price  	  	  </b></th> 
					<th style='width:75px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Uploaded  	  	  </b></th> 
					<th style='width:75px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Released  	  	  </b></th> 
				</tr>
				</thead>
				<tbody>";
				 
					$x = $limit;			
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					foreach($items as $r) { 
					extract($r);
					$ptype = ($ptype=='') 	   ? 'Uncategorize' : $ptype;
					$publish = ($publish=='y') ? 'Yes' : 'No';
					$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 	
		$table.= "<tr>
				  <td>													$x      																		</td>
				  <td>													$cName      																	</td>
				  <td>													$itemCode      																	</td>
				  <td style='text-align:center;'>			    		<img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 	</td>
				  <td style='text-align:left;padding-left:5px;'>		<a href='".HTTP_PATH."gallery/itemInfo2/$itemID' target='_blank'>		$itemName</a>  </td>
				  <td style='text-align:left;padding-left:5px;'>		$ptype																			</td>
				  <td style='text-align:left;padding-left:5px;'>		$full_name																		</td>
				  <td style='text-align:center;'>						$publish																		</td>
				  <td style='text-align:center;'>						$UnitPrice																		</td>
				  <td style='text-align:center;'>						$USD_Price																		</td>
				  <td style='text-align:center;'>				        ". $dUploaded ."										</td>
				  <td style='text-align:center;'>				        ". $dReleased ."										</td>
				</tr>";}
		$table.= "</tbody>";
					if(!$items)
						$table.=  "<tr><td colspan='12'>No match found.</td></tr>";
		$table.= "</table>";	
		
		
		$data['table'] 		= $table;
		$data['vfile']		= 'BU_items.php';
		$data['title']		= 'Items Preview';
		
		
	
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
	
	function month($month)
	{
		switch ($month) {
            case 1:  $monthString = "January";
                     break;
            case 2:  $monthString = "February";
                     break;
            case 3:  $monthString = "March";
                     break;
            case 4:  $monthString = "April";
                     break;
            case 5:  $monthString = "May";
                     break;
            case 6:  $monthString = "June";
                     break;
            case 7:  $monthString = "July";
                     break;
            case 8:  $monthString = "August";
                     break;
            case 9:  $monthString = "September";
                     break;
            case 10: $monthString = "October";
                     break;
            case 11: $monthString = "November";
                     break;
            case 12: $monthString = "December";
                     break;
            default: $monthString = "All";
                     break;
        }
		
		return $monthString;
	}
	
	function items_price($view='',$countryID='',$month='',$year='')
	{
		$data['typeView']  = $view;
		$data['month']     = ($month=='') 	  ? 'null' : $this->month($month);
		$data['year']      = ($year=='') 	  ? 'null' : $year;
		$data['countryID'] = ($countryID=='') ? 'null' : $countryID;
		
		//print_r($_POST);
		$table='';
		extract($_POST);
		$cond='';
		if(isset($Submit))
		{
			$cond 	 = '';
			if($itemCode!='') 		$cond  .=  " items.itemCode LIKE  '%$itemCode%' AND";
			if($itemName!='') 		$cond  .=  " items.itemName LIKE  '%$itemName%' AND";
			if($selPOSMType!='')	$cond  .=  " items.POSMTypeID =   $selPOSMType  AND";
			if($publish!='')		$cond  .=  " items.publish =      '$publish'  	AND";
			
			if($localPriceFrom!='' AND $localPriceTo!='') 	 	 
					$cond  .=  " items.UnitPrice >= '$localPriceFrom' AND  items.UnitPrice <= '$localPriceTo' AND";
			
			if($USDFrom!='' AND $USDTo!='') 	 	 
					$cond  .=  " items.USD_Price >= '$USDFrom' AND  items.USD_Price <= '$USDTo' AND";
			
			if($DateFrom!='' AND $DateTo!='') 	 	 
					$cond  .=  " date(items.dateAdded) >= '$DateFrom' AND  date(items.dateAdded) <= '$DateTo' AND";
					
			if($cond!='')  $cond  =  substr("AND ".$cond,0,-3);
		}
		
		$data['POST'] = $_POST;
		
		if($view=='GroupByCountry_Price'){
		$sql = "SELECT items.id as itemID, itemName, (SELECT typeName FROM POSM_Type where POSM_Type.id=items.POSMTypeID) as ptype, itemCode, 
					(SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, items.dateAdded as dUploaded, full_name, publish,
					 UnitPrice, USD_Price
					 FROM items LEFT JOIN admin_users ON admin_users.id = items.user_id
					 WHERE items.countryID = $countryID $cond ORDER BY items.id DESC";
		}
		if($view=='AVG_Price_per_Year'){
		$sql = "SELECT items.id as itemID, itemName, (SELECT typeName FROM POSM_Type where POSM_Type.id=items.POSMTypeID) as ptype, itemCode, 
					(SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, items.dateAdded as dUploaded, full_name, publish,
					 UnitPrice, USD_Price
					 FROM items LEFT JOIN admin_users ON admin_users.id = items.user_id
					 WHERE items.countryID = $countryID AND YEAR(items.dateAdded)=$year $cond ORDER BY items.id DESC";
		}if($view=='AVG_Price_per_Month'){
		$sql = "SELECT items.id as itemID, itemName, (SELECT typeName FROM POSM_Type where POSM_Type.id=items.POSMTypeID) as ptype, itemCode, 
					(SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, items.dateAdded as dUploaded, full_name, publish,
					 UnitPrice, USD_Price
					 FROM items LEFT JOIN admin_users ON admin_users.id = items.user_id
					 WHERE items.countryID = $countryID AND YEAR(items.dateAdded)=$year AND MONTH(items.dateAdded)=$month $cond ORDER BY items.id DESC";
		}
		
		$sql 	 = $this->db->query($sql);
		$items	 = $sql->result_array();
		//print_r($items);
			
		$table.= "<table id='large' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:12px;' class='iLike_Result_Table tablesorter'>
				<thead>
				<tr>
					<th style='width:10px;text-align:center;color:white;padding:0px' bgcolor='#bb1d1d'>   					   <b>No 		 		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Item Code  	  	  </b></th> 
					<th style='width:19px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Image   	 	      </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Name  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Type  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Local Price  	  	  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>USD   Price  	  	  </b></th> 
					<th style='width:30px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Publish  	  		  </b></th> 
					<th style='width:75px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Date uploaded  	  </b></th> 
				</tr>
				</thead>
				<tbody>";
				 
					$x = 0;			
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					$sumUnitPrice=0;
					$sumUSD_Price=0;
					foreach($items as $r) { 
					extract($r);
					$ptype = ($ptype=='') 	   ? 'Uncategorize' : $ptype;
					$publish = ($publish=='y') ? 'Yes' : 'No';
					$c = (($x++)%2) == 0 ? "class='alter'" :  "";
					$sumUnitPrice += number_format($UnitPrice, 2, '.', '');
					$sumUSD_Price +=  number_format($USD_Price, 2, '.', '');
		$table.= 	"<tr>
				  <td $c>													$x      																		</td>
				  <td $c>													$itemCode      																	</td>
				  <td $c style='text-align:center;'>			    <img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 			</td>
				  <td $c style='text-align:left;padding-left:5px;'>	<a href='".HTTP_PATH."gallery/itemInfo2/$itemID' target='_blank'>		$itemName</a>   </td>
				  <td $c style='text-align:left;padding-left:5px;'>	$ptype																					</td>
				  <td $c style='text-align:right;padding-right:20px;'>	". number_format($UnitPrice, 2, '.', '') ."											</td>
				  <td $c style='text-align:right;padding-right:20px;'>	". number_format($USD_Price, 2, '.', '')."											</td>
				  <td $c style='text-align:center;'>	$publish																							</td>
				  <td $c style='text-align:center;'>				". date("M d, Y", strtotime($dUploaded)) ."												</td>
				</tr>";}
		$table.= 	"<tr> 
					<td>Average</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td style='text-align:right;padding-right:20px;'>". number_format($sumUnitPrice, 2, '.', '') ."</td>
					<td style='text-align:right;padding-right:20px;'>". number_format($sumUSD_Price, 2, '.', '') ."</td>
					<td></td>
					<td></td>
				</tr>";		
		$table.=	"</tbody>";
					if(!$items)
						$table.= "<tr><td colspan='9'>No match found.</td></tr>"; 
		$table.=	"</table>";
		
		$data['POST']		= $_POST;
		$data['table'] 		= $table;
		$data['vfile']		= 'items_price.php';
		$data['title']		= 'Items Preview';
		
		//BREAD CRUMBS
		$HTTP_PATH 					 = HTTP_PATH."report/iLike";
		$data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
		$data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/AVG_Price_index> Average Price Index </a>';
		
	
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
	
	function viewItems($view='',$countryID='',$month='',$year='',$user_id='')
	{
		if($view=='per_countryAVG')
		{
			$sql = "SELECT items.id as itemID, itemName, (SELECT typeName FROM POSM_Type where POSM_Type.id=items.POSMTypeID) as ptype, itemCode, UnitPrice, USD_Price, 
						(SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, items.dateAdded as dUploaded, full_name, publish 
						 FROM items LEFT JOIN admin_users ON admin_users.id = items.user_id
						 WHERE items.countryID = $countryID AND YEAR(items.dateAdded) = $year ORDER BY UnitPrice DESC";
			$sql 	 = $this->db->query($sql);
			$items	 = $sql->result_array();
			//print_r($items);
			
		echo "<table id='large' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:12px;' class='iLike_Result_Table tablesorter'>
				<thead>
				<tr>
					<th style='width:10px;text-align:center;color:white;padding:0px' bgcolor='#bb1d1d'>   					   <b>No 		 		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Item Code  	  	  </b></th> 
					<th style='width:19px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Image   	 	      </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Name  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Type  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Local Price  	  	  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>USD   Price  	  	  </b></th> 
					<th style='width:30px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Publish  	  		  </b></th> 
					<th style='width:75px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Date uploaded  	  </b></th> 
				</tr>
				</thead>
				<tbody>";
				 
					$x = 0;			
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					$sumUnitPrice=0;
					$sumUSD_Price=0;
					foreach($items as $r) { 
					extract($r);
					$ptype = ($ptype=='') 	   ? 'Uncategorize' : $ptype;
					$publish = ($publish=='y') ? 'Yes' : 'No';
					$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
					$sumUnitPrice += number_format($UnitPrice, 2, '.', '');
					$sumUSD_Price +=  number_format($USD_Price, 2, '.', '');
		echo 	"<tr>
				  <td $c>													$x      																		</td>
				  <td $c>													$itemCode      																	</td>
				  <td $c style='text-align:center;'>			  <img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 	</td>
				  <td $c style='text-align:left;padding-left:5px;'>	<a href='".HTTP_PATH."gallery/itemInfo2/$itemID' target='_blank'>		$itemName</a>  </td>
				  <td $c style='text-align:left;padding-right:20px;'>	$ptype											</td>
				  <td $c style='text-align:right;padding-right:20px;'>	". number_format($UnitPrice, 2, '.', '') ."										</td>
				  <td $c style='text-align:right;padding-right:20px;'>	". number_format($USD_Price, 2, '.', '')."										</td>
				  <td $c style='text-align:center;'>	$publish										</td>
				  <td $c style='text-align:center;'>				". date("M d, Y", strtotime($dUploaded)) ."			</td>
				</tr>";}
		echo 	"<tr> 
					<td>Average</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td style='text-align:right;padding-right:20px;'>". number_format($sumUnitPrice/$x, 2, '.', '') ."</td>
					<td style='text-align:right;padding-right:20px;'>". number_format($sumUSD_Price/$x, 2, '.', '') ."</td>
					<td></td>
					<td></td>
				</tr>";		
				
		echo	"</tbody>";
					if(!$items)
						echo "<tr><td colspan='7'>No match found.</td></tr>";
		echo	"</table>";
			
		}
		elseif($view=='GroupByCountry_Price')
		{
			$sql = "SELECT items.id as itemID, itemName, (SELECT typeName FROM POSM_Type where POSM_Type.id=items.POSMTypeID) as ptype, itemCode, 
					(SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, items.dateAdded as dUploaded, full_name, publish,
					 UnitPrice, USD_Price
					 FROM items LEFT JOIN admin_users ON admin_users.id = items.user_id
					 WHERE items.countryID = $countryID ORDER BY items.id DESC";
			$sql 	 = $this->db->query($sql);
			$items	 = $sql->result_array();
			//print_r($items);
			
		echo "<table id='large' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:12px;' class='iLike_Result_Table tablesorter'>
				<thead>
				<tr>
					<th style='width:10px;text-align:center;color:white;padding:0px' bgcolor='#bb1d1d'>   					   <b>No 		 		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Item Code  	  	  </b></th> 
					<th style='width:19px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Image   	 	      </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Name  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Type  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Local Price  	  	  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>USD   Price  	  	  </b></th> 
					<th style='width:30px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Publish  	  		  </b></th> 
					<th style='width:75px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Date uploaded  	  </b></th> 
				</tr>
				</thead>
				<tbody>";
				 
					$x = 0;			
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					$sumUnitPrice=0;
					$sumUSD_Price=0;
					foreach($items as $r) { 
					extract($r);
					$ptype = ($ptype=='') 	   ? 'Uncategorize' : $ptype;
					$publish = ($publish=='y') ? 'Yes' : 'No';
					$c = (($x++)%2) == 0 ? "class='alter'" :  "";
					$sumUnitPrice += number_format($UnitPrice, 2, '.', '');
					$sumUSD_Price +=  number_format($USD_Price, 2, '.', '');
		echo 	"<tr>
				  <td $c>													$x      																		</td>
				  <td $c>													$itemCode      																	</td>
				  <td $c style='text-align:center;'>			    <img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 			</td>
				  <td $c style='text-align:left;padding-left:5px;'>	<a href='".HTTP_PATH."gallery/itemInfo2/$itemID' target='_blank'>		$itemName</a>   </td>
				  <td $c style='text-align:left;padding-left:5px;'>	$ptype																					</td>
				  <td $c style='text-align:right;padding-right:20px;'>	". number_format($UnitPrice, 2, '.', '') ."											</td>
				  <td $c style='text-align:right;padding-right:20px;'>	". number_format($USD_Price, 2, '.', '')."											</td>
				  <td $c style='text-align:center;'>	$publish																							</td>
				  <td $c style='text-align:center;'>				". date("M d, Y", strtotime($dUploaded)) ."												</td>
				</tr>";}
		echo 	"<tr> 
					<td>Average</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td style='text-align:right;padding-right:20px;'>". number_format($sumUnitPrice/$x, 2, '.', '') ."</td>
					<td style='text-align:right;padding-right:20px;'>". number_format($sumUSD_Price/$x, 2, '.', '') ."</td>
					<td></td>
					<td></td>
				</tr>";		
		echo	"</tbody>";
					if(!$items)
						echo "<tr><td colspan='7'>No match found.</td></tr>";
		echo	"</table>";
			
		}
		elseif($view=='AVG_Price_per_Year')
		{
			$sql = "SELECT items.id as itemID, itemName, (SELECT typeName FROM POSM_Type where POSM_Type.id=items.POSMTypeID) as ptype, itemCode, 
					(SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, items.dateAdded as dUploaded, full_name, publish,
					 UnitPrice, USD_Price
					 FROM items LEFT JOIN admin_users ON admin_users.id = items.user_id
					 WHERE items.countryID = $countryID AND YEAR(items.dateAdded)=$year ORDER BY items.id DESC";
			$sql 	 = $this->db->query($sql);
			$items	 = $sql->result_array();
			//print_r($items);
			
		echo "<table id='large' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:12px;' class='iLike_Result_Table tablesorter'>
				<thead>
				<tr>
					<th style='width:10px;text-align:center;color:white;padding:0px' bgcolor='#bb1d1d'>   					   <b>No 		 		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Item Code  	  	  </b></th> 
					<th style='width:19px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Image   	 	      </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Name  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Type  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Local Price  	  	  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>USD   Price  	  	  </b></th> 
					<th style='width:30px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Publish  	  		  </b></th> 
					<th style='width:75px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Date uploaded  	  </b></th> 
				</tr>
				</thead>
				<tbody>";
				 
					$x = 0;			
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					$sumUnitPrice=0;
					$sumUSD_Price=0;
					foreach($items as $r) { 
					extract($r);
					$ptype = ($ptype=='') 	   ? 'Uncategorize' : $ptype;
					$publish = ($publish=='y') ? 'Yes' : 'No';
					$c = (($x++)%2) == 0 ? "class='alter'" :  "";
					$sumUnitPrice += number_format($UnitPrice, 2, '.', '');
					$sumUSD_Price +=  number_format($USD_Price, 2, '.', '');
		echo 	"<tr>
				  <td $c>													$x      																		</td>
				  <td $c>													$itemCode      																	</td>
				  <td $c style='text-align:center;'>			    <img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 			</td>
				  <td $c style='text-align:left;padding-left:5px;'>	<a href='".HTTP_PATH."gallery/itemInfo2/$itemID' target='_blank'>		$itemName</a>   </td>
				  <td $c style='text-align:left;padding-left:5px;'>	$ptype																					</td>
				  <td $c style='text-align:right;padding-right:20px;'>	". number_format($UnitPrice, 2, '.', '') ."											</td>
				  <td $c style='text-align:right;padding-right:20px;'>	". number_format($USD_Price, 2, '.', '')."											</td>
				  <td $c style='text-align:center;'>	$publish																							</td>
				  <td $c style='text-align:center;'>				". date("M d, Y", strtotime($dUploaded)) ."												</td>
				</tr>";}
		echo 	"<tr> 
					<td>Average</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td style='text-align:right;padding-right:20px;'>". number_format($sumUnitPrice/$x, 2, '.', '') ."</td>
					<td style='text-align:right;padding-right:20px;'>". number_format($sumUSD_Price/$x, 2, '.', '') ."</td>
					<td></td>
					<td></td>
				</tr>";		
		echo	"</tbody>";
					if(!$items)
						echo "<tr><td colspan='7'>No match found.</td></tr>";
		echo	"</table>";
			
		}
		elseif($view=='AVG_Price_per_Month')
		{
			$sql = "SELECT items.id as itemID, itemName, (SELECT typeName FROM POSM_Type where POSM_Type.id=items.POSMTypeID) as ptype, itemCode, 
					(SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, items.dateAdded as dUploaded, full_name, publish,
					 UnitPrice, USD_Price
					 FROM items LEFT JOIN admin_users ON admin_users.id = items.user_id
					 WHERE items.countryID = $countryID AND MONTH(items.dateAdded)=$month AND YEAR(items.dateAdded)=$year ORDER BY items.id DESC";
			$sql 	 = $this->db->query($sql);
			$items	 = $sql->result_array();
			//print_r($items);
			
		echo "<table id='large' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:12px;' class='iLike_Result_Table tablesorter'>
				<thead>
				<tr>
					<th style='width:10px;text-align:center;color:white;padding:0px' bgcolor='#bb1d1d'>   					   <b>No 		 		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Item Code  	  	  </b></th> 
					<th style='width:19px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Image   	 	      </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Name  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Type  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Local Price  	  	  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>USD   Price  	  	  </b></th> 
					<th style='width:30px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Publish  	  		  </b></th> 
					<th style='width:75px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Date uploaded  	  </b></th> 
				</tr>
				</thead>
				<tbody>";
				 
					$x = 0;			
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					$sumUnitPrice=0;
					$sumUSD_Price=0;
					foreach($items as $r) { 
					extract($r);
					$ptype = ($ptype=='') 	   ? 'Uncategorize' : $ptype;
					$publish = ($publish=='y') ? 'Yes' : 'No';
					$c = (($x++)%2) == 0 ? "class='alter'" :  "";
					$sumUnitPrice += number_format($UnitPrice, 2, '.', '');
					$sumUSD_Price +=  number_format($USD_Price, 2, '.', '');
		echo 	"<tr>
				  <td $c>													$x      																		</td>
				  <td $c>													$itemCode      																	</td>
				  <td $c style='text-align:center;'>			    <img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 			</td>
				  <td $c style='text-align:left;padding-left:5px;'>	<a href='".HTTP_PATH."gallery/itemInfo2/$itemID' target='_blank'>		$itemName</a>   </td>
				  <td $c style='text-align:left;padding-left:5px;'>	$ptype																					</td>
				  <td $c style='text-align:right;padding-right:20px;'>	". number_format($UnitPrice, 2, '.', '') ."											</td>
				  <td $c style='text-align:right;padding-right:20px;'>	". number_format($USD_Price, 2, '.', '')."											</td>
				  <td $c style='text-align:center;'>	$publish																							</td>
				  <td $c style='text-align:center;'>				". date("M d, Y", strtotime($dUploaded)) ."												</td>
				</tr>";}
		echo 	"<tr> 
					<td>Average</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td style='text-align:right;padding-right:20px;'>". number_format($sumUnitPrice/$x, 2, '.', '') ."</td>
					<td style='text-align:right;padding-right:20px;'>". number_format($sumUSD_Price/$x, 2, '.', '') ."</td>
					<td></td>
					<td></td>
				</tr>";		
		echo	"</tbody>";
					if(!$items)
						echo "<tr><td colspan='7'>No match found.</td></tr>";
		echo	"</table>";
			
		}
		elseif($view=='GroupByCountry')
		{
			$sql = "SELECT items.id as itemID, itemName, (SELECT typeName FROM POSM_Type where POSM_Type.id=items.POSMTypeID) as ptype, itemCode, 
					(SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, items.dateAdded as dUploaded, full_name, publish 
					 FROM items LEFT JOIN admin_users ON admin_users.id = items.user_id
					 WHERE items.countryID = $countryID ORDER BY items.id DESC";
			$sql 	 = $this->db->query($sql);
			$items	 = $sql->result_array();
			//print_r($items);
			
		echo "<table id='large' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:12px;' class='iLike_Result_Table tablesorter'>
				<thead>
				<tr>
					<th style='width:10px;text-align:center;color:white;padding:0px' bgcolor='#bb1d1d'>   					   <b>No 		 		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Item Code  	  	  </b></th> 
					<th style='width:19px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Image   	 	      </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Name  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Type  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>User  	  		  </b></th> 
					<th style='width:30px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Publish  	  		  </b></th> 
					<th style='width:75px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Date uploaded  	  </b></th> 
				</tr>
				</thead>
				<tbody>";
				 
					$x = 0;			
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					foreach($items as $r) { 
					extract($r);
					$ptype = ($ptype=='') 	   ? 'Uncategorize' : $ptype;
					$publish = ($publish=='y') ? 'Yes' : 'No';
					$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 	
		echo 	"<tr>
				  <td $c>													$x      																		</td>
				  <td $c>													$itemCode      																	</td>
				  <td $c style='text-align:center;'>			    	<img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 	</td>
				  <td $c style='text-align:left;padding-left:5px;'>	<a href='".HTTP_PATH."gallery/itemInfo2/$itemID' target='_blank'>		$itemName</a>  </td>
				  <td $c style='text-align:left;padding-left:5px;'>	$ptype											</td>
				  <td $c style='text-align:left;padding-left:5px;'>	$full_name										</td>
				  <td $c style='text-align:center;'>	$publish										</td>
				  <td $c style='text-align:center;'>				". date("M d, Y", strtotime($dUploaded)) ."			</td>
				</tr>";}
		echo	"</tbody>";
					if(!$items)
						echo "<tr><td colspan='7'>No match found.</td></tr>";
		echo	"</table>";
			
		}
		elseif($view=='per_country')
		{
			$sql = "SELECT items.id as itemID, itemName, (SELECT typeName FROM POSM_Type where POSM_Type.id=items.POSMTypeID) as ptype, itemCode, 
						(SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, items.dateAdded as dUploaded, full_name, publish 
						 FROM items LEFT JOIN admin_users ON admin_users.id = items.user_id
						 WHERE items.countryID = $countryID AND YEAR(items.dateAdded) = $year ORDER BY items.id DESC";
			$sql 	 = $this->db->query($sql);
			$items	 = $sql->result_array();
			//print_r($items);
			
		echo "<table id='large' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:12px;' class='iLike_Result_Table tablesorter'>
				<thead>
				<tr>
					<th style='width:10px;text-align:center;color:white;padding:0px' bgcolor='#bb1d1d'>   					   <b>No 		 		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Item Code  	  	  </b></th> 
					<th style='width:19px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Image   	 	      </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Name  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Type  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>User  	  		  </b></th> 
					<th style='width:30px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Publish  	  		  </b></th> 
					<th style='width:75px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Date uploaded  	  </b></th> 
				</tr>
				</thead>
				<tbody>";
				 
					$x = 0;			
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					foreach($items as $r) { 
					extract($r);
					$ptype = ($ptype=='') 	   ? 'Uncategorize' : $ptype;
					$publish = ($publish=='y') ? 'Yes' : 'No';
					$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 	
		echo 	"<tr>
				  <td $c>													$x      																		</td>
				  <td $c>													$itemCode      																	</td>
				  <td $c style='text-align:center;'>			    	<img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 	</td>
				  <td $c style='text-align:left;padding-left:5px;'>	<a href='".HTTP_PATH."gallery/itemInfo2/$itemID' target='_blank'>		$itemName</a>  </td>
				  <td $c style='text-align:left;padding-left:5px;'>	$ptype											</td>
				  <td $c style='text-align:left;padding-left:5px;'>	$full_name										</td>
				  <td $c style='text-align:center;'>	$publish										</td>
				  <td $c style='text-align:center;'>				". date("M d, Y", strtotime($dUploaded)) ."			</td>
				</tr>";}
		echo	"</tbody>";
					if(!$items)
						echo "<tr><td colspan='7'>No match found.</td></tr>";
		echo	"</table>";
			
		}
		elseif($view=='default')
		{
			$sql = "SELECT items.id as itemID, itemName, (SELECT typeName FROM POSM_Type where POSM_Type.id=items.POSMTypeID) as ptype, itemCode, 
						(SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, items.dateAdded as dUploaded, full_name, publish 
						 FROM items LEFT JOIN admin_users ON admin_users.id = items.user_id
						 WHERE items.countryID = $countryID AND YEAR(items.dateAdded) = $year ORDER BY items.id DESC";
			$sql 	 = $this->db->query($sql);
			$items	 = $sql->result_array();
			//print_r($items);
			
		echo "<table id='large' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:12px;' class='iLike_Result_Table tablesorter'>
				<thead>
				<tr>
					<th style='width:10px;text-align:center;color:white;padding:0px' bgcolor='#bb1d1d'>   					   <b>No 		 		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Item Code  	  	  </b></th> 
					<th style='width:19px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Image   	 	      </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Name  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Type  	  		  </b></th> 
					<th style='width:30px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Publish  	  		  </b></th> 
					<th style='width:75px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Date uploaded  	  </b></th> 
				</tr>
				</thead>
				<tbody>";
				 
					$x = 0;			
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					foreach($items as $r) { 
					extract($r);
					$ptype = ($ptype=='') 	   ? 'Uncategorize' : $ptype;
					$publish = ($publish=='y') ? 'Yes' : 'No';
					$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 	
		echo 	"<tr>
				  <td $c>													$x      																		</td>
				  <td $c>													$itemCode      																	</td>
				  <td $c style='text-align:center;'>			    <img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 	</td>
				  <td $c style='text-align:left;padding-left:5px;'>	<a href='".HTTP_PATH."gallery/itemInfo2/$itemID' target='_blank'>		$itemName</a>  </td>
				  <td $c style='text-align:left;padding-left:5px;'>	$ptype											</td>
				  <td $c style='text-align:center;'>	$publish										</td>
				  <td $c style='text-align:center;'>				". date("M d, Y", strtotime($dUploaded)) ."			</td>
				</tr>";}
		echo	"</tbody>";
					if(!$items)
						echo "<tr><td colspan='7'>No match found.</td></tr>";
		echo	"</table>";
			
		}
		elseif($view=='BU_activeness_per_Month')
		{
			$sql = "SELECT items.id as itemID, itemName, (SELECT typeName FROM POSM_Type where POSM_Type.id=items.POSMTypeID) as ptype, itemCode, 
						(SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, items.dateAdded as dUploaded, full_name, publish 
						 FROM items LEFT JOIN admin_users ON admin_users.id = items.user_id
						 WHERE items.countryID = $countryID AND MONTH(items.dateAdded) = $month AND YEAR(items.dateAdded) = $year ORDER BY items.id DESC";
			$sql 	 = $this->db->query($sql);
			$items	 = $sql->result_array();
			//print_r($items);
			
		echo "<table id='large' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:12px;' class='iLike_Result_Table tablesorter'>
				<thead>
				<tr>
					<th style='width:10px;text-align:center;color:white;padding:0px' bgcolor='#bb1d1d'>   					   <b>No 		 		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Item Code  	  	  </b></th> 
					<th style='width:19px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Image   	 	      </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Name  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Type  	  		  </b></th> 
					<th style='width:30px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Publish  	  		  </b></th> 
					<th style='width:75px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Date uploaded  	  </b></th> 
				</tr>
				</thead>
				<tbody>";
				 
					$x = 0;			
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					foreach($items as $r) { 
					extract($r);
					$ptype = ($ptype=='') 	   ? 'Uncategorize' : $ptype;
					$publish = ($publish=='y') ? 'Yes' : 'No';
					$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 	
		echo 	"<tr>
				  <td $c>													$x      																		</td>
				  <td $c>													$itemCode      																	</td>
				  <td $c style='text-align:center;'>			    <img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 	</td>
				  <td $c style='text-align:left;padding-left:5px;'>	<a href='".HTTP_PATH."gallery/itemInfo2/$itemID' target='_blank'>		$itemName</a>  </td>
				  <td $c style='text-align:left;padding-left:5px;'>	$ptype											</td>
				  <td $c style='text-align:center;'>	$publish										</td>
				  <td $c style='text-align:center;'>				". date("M d, Y", strtotime($dUploaded)) ."			</td>
				</tr>";}
		echo	"</tbody>";
					if(!$items)
						echo "<tr><td colspan='7'>No match found.</td></tr>";
		echo	"</table>";
			
		}
		elseif($view=='BU_activeness_Users')
		{
			$sql = "SELECT items.id as itemID, itemName, (SELECT typeName FROM POSM_Type where POSM_Type.id=items.POSMTypeID) as ptype, itemCode,
						(SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, items.dateAdded as dUploaded, full_name, publish 
						 FROM items LEFT JOIN admin_users ON admin_users.id = items.user_id
						 WHERE items.countryID = $countryID AND items.user_id = $user_id
						 ORDER BY items.id DESC";
			$sql 	 = $this->db->query($sql);
			$items	 = $sql->result_array();
			//print_r($items);
			
		echo "<table id='large' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:12px;' class='iLike_Result_Table tablesorter'>
				<thead>
				<tr>
					<th style='width:10px;text-align:center;color:white;padding:0px' bgcolor='#bb1d1d'>   					   <b>No 		 		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Item Code  	  	  </b></th> 
					<th style='width:19px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Image   	 	      </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Name  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Type  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>User  	  		  </b></th> 
					<th style='width:30px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Publish  	  		  </b></th> 
					<th style='width:75px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Date uploaded  	  </b></th> 
				</tr>
				</thead>
				<tbody>";
				 
					$x = 0;			
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					foreach($items as $r) { 
					extract($r);
					$ptype = ($ptype=='') 	   ? 'Uncategorize' : $ptype;
					$publish = ($publish=='y') ? 'Yes' : 'No';
					$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 	
		echo 	"<tr>
				  <td $c>													$x      																		</td>
				  <td $c>													$itemCode      																	</td>
				  <td $c style='text-align:center;'>			    	<img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 	</td>
				  <td $c style='text-align:left;padding-left:5px;'>	<a href='".HTTP_PATH."gallery/itemInfo2/$itemID' target='_blank'>		$itemName</a>  </td>
				  <td $c style='text-align:left;padding-left:5px;'>	$ptype											</td>
				  <td $c style='text-align:left;padding-left:5px;'>	$full_name										</td>
				  <td $c style='text-align:center;'>	$publish										</td>
				  <td $c style='text-align:center;'>				". date("M d, Y", strtotime($dUploaded)) ."			</td>
				</tr>";}
		echo	"</tbody>";
					if(!$items)
						echo "<tr><td colspan='7'>No match found.</td></tr>";
		echo	"</table>";
			
		}
		elseif($view=='BU_activeness_Users_per_Year')
		{
			$sql = "SELECT items.id as itemID, itemName, (SELECT typeName FROM POSM_Type where POSM_Type.id=items.POSMTypeID) as ptype, itemCode, 
						(SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, items.dateAdded as dUploaded, full_name, publish 
						 FROM items LEFT JOIN admin_users ON admin_users.id = items.user_id
						 WHERE items.user_id = $user_id AND YEAR(items.dateAdded) = $year ORDER BY items.id DESC";
			$sql 	 = $this->db->query($sql);
			$items	 = $sql->result_array();
			//print_r($items);
			
		echo "<table id='large' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:12px;' class='iLike_Result_Table tablesorter'>
				<thead>
				<tr>
					<th style='width:10px;text-align:center;color:white;padding:0px' bgcolor='#bb1d1d'>   					   <b>No 		 		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Item Code  	  	  </b></th> 
					<th style='width:19px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Image   	 	      </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Name  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Type  	  		  </b></th> 
					<th style='width:30px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Publish  	  		  </b></th> 
					<th style='width:75px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Date uploaded  	  </b></th> 
				</tr>
				</thead>
				<tbody>";
				 
					$x = 0;			
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					foreach($items as $r) { 
					extract($r);
					$ptype = ($ptype=='') 	   ? 'Uncategorize' : $ptype;
					$publish = ($publish=='y') ? 'Yes' : 'No';
					$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 	
		echo 	"<tr>
				  <td $c>													$x      																		</td>
				  <td $c>													$itemCode      																	</td>
				  <td $c style='text-align:center;'>			    <img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 	</td>
				  <td $c style='text-align:left;padding-left:5px;'>	<a href='".HTTP_PATH."gallery/itemInfo2/$itemID' target='_blank'>		$itemName</a>  </td>
				  <td $c style='text-align:left;padding-left:5px;'>	$ptype											</td>
				  <td $c style='text-align:center;'>	$publish										</td>
				  <td $c style='text-align:center;'>				". date("M d, Y", strtotime($dUploaded)) ."			</td>
				</tr>";}
		echo	"</tbody>";
					if(!$items)
						echo "<tr><td colspan='7'>No match found.</td></tr>";
		echo	"</table>";
			
		}
		elseif($view=='groupByUser')
		{
			$sql = "SELECT items.id as itemID, itemName, (SELECT typeName FROM POSM_Type where POSM_Type.id=items.POSMTypeID) as ptype, itemCode,
						(SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, items.dateAdded as dUploaded, full_name, publish 
						 FROM items LEFT JOIN admin_users ON admin_users.id = items.user_id
						 WHERE items.countryID = $countryID AND items.user_id = $user_id
						 ORDER BY items.id DESC";
			$sql 	 = $this->db->query($sql);
			$items	 = $sql->result_array();
			//print_r($items);
			
		echo "<table id='large' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:12px;' class='iLike_Result_Table tablesorter'>
				<thead>
				<tr>
					<th style='width:10px;text-align:center;color:white;padding:0px' bgcolor='#bb1d1d'>   					   <b>No 		 		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Item Code  	  	  </b></th> 
					<th style='width:19px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Image   	 	      </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Name  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Type  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>User  	  		  </b></th> 
					<th style='width:30px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Publish  	  		  </b></th> 
					<th style='width:75px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Date uploaded  	  </b></th> 
				</tr>
				</thead>
				<tbody>";
				 
					$x = 0;			
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					foreach($items as $r) { 
					extract($r);
					$ptype = ($ptype=='') 	   ? 'Uncategorize' : $ptype;
					$publish = ($publish=='y') ? 'Yes' : 'No';
					$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 	
		echo 	"<tr>
				  <td $c>													$x      																		</td>
				  <td $c>													$itemCode      																	</td>
				  <td $c style='text-align:center;'>			    	<img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 	</td>
				  <td $c style='text-align:left;padding-left:5px;'>	<a href='".HTTP_PATH."gallery/itemInfo2/$itemID' target='_blank'>		$itemName</a>  </td>
				  <td $c style='text-align:left;padding-left:5px;'>	$ptype											</td>
				  <td $c style='text-align:left;padding-left:5px;'>	$full_name										</td>
				  <td $c style='text-align:center;'>	$publish										</td>
				  <td $c style='text-align:center;'>				". date("M d, Y", strtotime($dUploaded)) ."			</td>
				</tr>";}
		echo	"</tbody>";
					if(!$items)
						echo "<tr><td colspan='7'>No match found.</td></tr>";
		echo	"</table>";
			
		}
		
	}
	
	
	
	function hits($view='',$itemID)
	{
		if($view=='item_database')
		{
			$sql = "SELECT countryName, full_name, department_name, date_time FROM item_views 
					LEFT JOIN admin_users  ON admin_users.id  = item_views.user_id 
					LEFT JOIN country	   ON country.id      = admin_users.countryID
					LEFT JOIN departments  ON departments.id  = admin_users.department_id
					WHERE item_views.itemID = $itemID ORDER BY date_time DESC";
		}if($view=='eCatalogue')
		{
			$sql = "SELECT countryName, full_name, department_name, date_time FROM ecitem_views 
					LEFT JOIN admin_users  ON admin_users.id  = ecitem_views.user_id 
					LEFT JOIN country	   ON country.id      = admin_users.countryID
					LEFT JOIN departments  ON departments.id  = admin_users.department_id
					WHERE ecitem_views.itemID = $itemID ORDER BY date_time DESC";
		}
		
		$sql = $this->db->query($sql);
		$rows = $sql->result_array();
		
		echo "<table id='large' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:12px;' class='iLike_Result_Table tablesorter'>
			<thead>
			<tr>
				<th style='width:10px;text-align:center;color:white;padding:0px' bgcolor='#bb1d1d'>   					   <b>No 		 		  </b></th> 
				<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Country  	  	      </b></th> 
				<th style='width:19px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Full Name   	 	  </b></th> 
				<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Department  	  	  </b></th> 
				<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Date Time  	  	  </b></th> 
			</tr>
			</thead>
			<tbody>";
			 
				$x = 0;			
				$y=1;
				$z=1;
				$total=0;
				$total_target=0;
				foreach($rows as $r) { 
				extract($r);
				$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 	
		echo 	"<tr>
				  <td $c>											    $x      	 </td>
				  <td $c style='text-align:left;padding-left:5px;'>	$countryName     </td>
				  <td $c style='text-align:left;padding-left:5px;'>	$full_name 		 </td>
				  <td $c style='text-align:left;padding-left:5px;'>	$department_name </td>
				  <td $c style='text-align:left;padding-left:5px;'>	$date_time		 </td>
				</tr>";}
		echo	"</tbody>";
		echo	"</table>";
		
	}
	
	function iLike_Items($cID='')
	{
	 $data['EDIT'] 	=  $this->modules->crud_checker(18,'EDIT');
	 $data['cID'] = $cID;
	 
	 $sql = "SELECT itemID, itm.itemName, itm.itemCode as iCode, campaignID,
		   (SELECT COUNT( id ) FROM votexRef AS vref WHERE vref.itemID = itemREF.itemID AND vote =  'yes' and vref.campaignID =$cID 
		   AND vref.voterID IN (SELECT voters.id FROM voters WHERE voters.id = vref.voterID AND votingStatus = 'done')) AS voteTot,
		   (select typeName from POSM_Type as pt where pt.id=i.POSMTypeID) as ptype, 
		   (SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = i.id) as item_image
		   FROM  `campaignItemsXref` AS itemREF LEFT JOIN items AS i ON itemREF.itemID = i.id  left join items as itm on itemREF.itemID=itm.id where   itemREF.campaignID=$cID 
           ORDER BY `voteTot` DESC";

	 $report    = $this->db->query($sql);  
	 $rep       = $report->result_array(); 
 
	 
	$sql       = "select *,full_name, c.id as cID from campaign as c inner join admin_users as u on c.adminCreatorID=u.id where c.id='$cID'   ";
	$header    = $this->db->query($sql);  
	$header    = $header->result_array(); 
	 
 
	 $sql   		  = "SELECT * FROM voters WHERE campaignID = $cID"; 
	 $voters    	  = $this->db->query($sql);  
	 $data['voters']  = $voters->result_array();
	 
	 //CAMPAIGN REF
	 $sql   		  = "SELECT * FROM iLike_Rules_Ref WHERE campaignID = $cID"; 
	 $voters    	  = $this->db->query($sql);  
	 $data['iLike_Rules_Ref']  = $voters->result_array();
	 
	 $sql   		  = "SELECT * FROM iLike_Rules_No_Committes_Ref WHERE campaignID = $cID"; 
	 $voters    	  = $this->db->query($sql);  
	 $data['iLike_Rules_No_Committes_Ref']  = $voters->result_array();
	 
	 $sql   		  = "SELECT * FROM iLikeCanvassingRulesXref WHERE campaignID = $cID"; 
	 $voters    	  = $this->db->query($sql);  
	 $data['iLikeCanvassingRulesXref']  = $voters->result_array();
	
		 $sql   = "SELECT * FROM iLikeVotingRulesRef WHERE campaignID = $cID ORDER BY fieldID DESC"; 
	 $sql   = $this->db->query($sql); 
	 $iLikeVotingRulesRef = $sql->result_array();
	 
	 $VotingRules[]= array('table'=>'',
							'fieldName'=>'', 
							'fieldID'=>'', 
							'fieldValue'=>'', 
							'relation'=>'',
							'value'=>''
							);
	 foreach($iLikeVotingRulesRef as $iW)
	 {extract($iW);
		switch($fieldName){
			case("POSMTypeID"):
				$tableName	= 'ITEM TYPE';
				$fieldName  = 'typeName';
				$table 		= 'POSM_Type';
			break;
			case("POSMStatusID"):
				$tableName	= 'ITEM STATUS';
				$fieldName  = 'statusName';
				$table 		= 'POSM_Status';
			break;
			case("OUTLETStatusID"):
				$tableName	= 'OUTLET STATUS';
				$fieldName  = 'statusName';
				$table 		= 'OUTLET_Status';
			break;
			case("PremiumTypeID"):
				$tableName	= 'PREMIUM TYPE NAME';
				$fieldName  = 'premiumTypeName';
				$table 		= 'premiumItemType';
			break;
			case("MaterialTypeID"):
				$tableName	= 'MATERIAL TYPE';;
				$fieldName  = 'materialName';
				$table 		= 'MATERIAL_Type';
			break;
			case("brandID"):
				$tableName	= 'BRAND NAME';
				$fieldName  = 'brandName';
				$table 		= 'brands';
			break;
		}
		
		$query 		= $this->db->query("SELECT $fieldName FROM $table WHERE id=$fieldID LIMIT 1");
		$row 		= $query->row();
		$name_Field = $row->$fieldName;
		
		$VotingRules[] = array('table'=>$tableName,
							'fieldName'=>$name_Field, 
							'fieldID'=>$fieldID, 
							'fieldValue'=>$name_Field, 
							'relation'=>$rel,
							'value'=>$val
							);
	}
	 
	 $data['iLikeVotingRulesRef']  = $VotingRules;
	
	$sqlSTr="SELECT items.id as iID, (SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, itemName,
			(SELECT totvote FROM iLikeResultRef WHERE itemID = iID AND campaignID = $cID) AS voteTot,
			POSM_Type.typeName as POSM_TypeName 
			FROM items
			LEFT JOIN POSM_Type ON items.POSMTypeID = POSM_Type.id 
			WHERE  items.id IN (SELECT itemID FROM iLikeResultRef WHERE campaignID = $cID) ORDER BY POSM_TypeName ASC, voteTot DESC";
	$voters = $this->db->query($sqlSTr);
	$data['topItems'] = $voters->result_array();
	
	
    $data['vfile']		= 'iLike_Items.php';
	$data['title']		= 'iLike Report';
	$data['rep']		= $rep;
	$data['repHeader']	= $header;
	
	//BREAD CRUMBS
	$HTTP_PATH 					 = HTTP_PATH."report/iLike";
	$data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
	$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	$data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/iLike> iLike Report </a>';
	$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	$data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/iLike_Items/$cID'>". $header[0]['campaignName'] ."</a>";
	
	
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
	
	function iWant($action='',$id='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(48,'REVIEW');
		$filter_WHERE 		= $this->modules->country();
		$filter_AND 		= $this->modules->country2();
		//$votingCampaignID 	= $id;
		
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(46);
		
		$table						= 'campaign';
		$data['vfile']				= 'iWant_report.php';
	    $data['title']				= 'iWant Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					 = HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/iWant> iWant Report </a>';
		
		//TOTAL NUMBER OF ROWS			
		$data['active_page']=1;
		$sql       = $this->db->query("SELECT id FROM campaign WHERE campaignType='iWant' and status='done'");
		$sql       = $sql->result_array();
		$total_rec = count($sql);

		$pagenum = 1;
		$data['page_rows'] = 10; 
		$data['last'] = ceil($total_rec/$data['page_rows']);		
		$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];

	
		extract($_POST);
		if($action=="page")
		{
			$this->modules->module_checker(27,'REVIEW');
			$pagenum = $id;
			$data['active_page'] = $id; 
			$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		}

		$filter_WHERE = $this->modules->iLike_Campaign_country();
     	$sql = $this->db->query("SELECT *,campaign.id AS iWantCampaignID FROM $table  
								LEFT JOIN admin_users 
								ON campaign.adminCreatorID = admin_users.id
								WHERE campaignType='iWant' AND status='done' ORDER BY iWantCampaignID  DESC $max");
								
		$data['campaigns'] = $sql->result_array();
		
		
		$viewer = 'innerPages';
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
	
	function iWant_Items($cID='')
	{
	 $voter_AND="";
	 if($_SESSION['super_admin']!='y' AND $_SESSION['countryID']!=0){
			$voter_AND = " AND voters.Fields001 = ".$_SESSION['countryID'];
	 }
	$data['PUBLISH_CAMPAIGN'] =  $this->modules->crud_checker(29,'PUBLISH CAMPAIGN');
	
	$sql = "SELECT itemID, itm.itemName, itm.itemCode as iCode, campaignID,
			(SELECT COUNT( id ) FROM votexRef AS vref WHERE vref.itemID = itemREF.itemID AND vote =  'yes' and vref.campaignID =$cID 
			AND vref.voterID IN (SELECT voters.id FROM voters WHERE voters.id = vref.voterID AND votingStatus = 'done')) AS voteTot,
		    (select typeName from POSM_Type as pt where pt.id=i.POSMTypeID) as ptype, 
		    (SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = i.id) as item_image
		    FROM  `campaignItemsXref` AS itemREF LEFT JOIN items AS i ON itemREF.itemID = i.id  left join items as itm on itemREF.itemID=itm.id where   itemREF.campaignID=$cID 
            ORDER BY `voteTot` DESC";
		   
	$report    = $this->db->query($sql);  
	$rep       = $report->result_array(); 
	$data['cID'] = $cID;
	 
	$sql       = "select *, full_name, c.id as cID from campaign as c inner join admin_users as u on c.adminCreatorID=u.id where c.id='$cID'   ";
	$header    = $this->db->query($sql);  
	$header    = $header->result_array(); 
	
	 $sql   		  = "SELECT *, voters.id as voterID, countryName FROM voters 
						LEFT JOIN country ON voters.Fields001 = country.id 
						where campaignID = $cID ORDER BY country.id ASC"; 
	$voters = $this->db->query($sql);
	$data['voters'] = $voters->result_array();
    
	 
	 //CAMPAIGN REF
	 $sql   		  = "SELECT * FROM iWantCampaignNumber_of_commitees_ref WHERE campaignID = $cID"; 
	 $voters    	  = $this->db->query($sql);  
	 $data['iWantCampaignNumber_of_commitees_ref']  = $voters->result_array();
	 
	 $sql   		  = "SELECT * FROM iWantCanvassingRulesRef WHERE campaignID = $cID"; 
	 $voters    	  = $this->db->query($sql);  
	 $data['iWantCanvassingRulesRef']  = $voters->result_array();
	
	 $sql   = "SELECT * FROM iWantVotingRulesRef WHERE campaignID = $cID ORDER BY fieldID DESC"; 
	 $sql   = $this->db->query($sql); 
	 $iWantVotingRulesRef = $sql->result_array();
	 
	  $VotingRules[]= array('table'=>'',
							'fieldName'=>'', 
							'fieldID'=>'', 
							'fieldValue'=>'', 
							'relation'=>'',
							'value'=>''
							);
	 foreach($iWantVotingRulesRef as $iW)
	 {extract($iW);
		switch($fieldName){
			case("POSMTypeID"):
				$tableName	= 'ITEM TYPE';
				$fieldName  = 'typeName';
				$table 		= 'POSM_Type';
			break;
			case("POSMStatusID"):
				$tableName	= 'ITEM STATUS';
				$fieldName  = 'statusName';
				$table 		= 'POSM_Status';
			break;
			case("OUTLETStatusID"):
				$tableName	= 'OUTLET STATUS';
				$fieldName  = 'statusName';
				$table 		= 'OUTLET_Status';
			break;
			case("PremiumTypeID"):
				$tableName	= 'PREMIUM TYPE NAME';
				$fieldName  = 'premiumTypeName';
				$table 		= 'premiumItemType';
			break;
			case("MaterialTypeID"):
				$tableName	= 'MATERIAL TYPE';;
				$fieldName  = 'materialName';
				$table 		= 'MATERIAL_Type';
			break;
			case("brandID"):
				$tableName	= 'BRAND NAME';
				$fieldName  = 'brandName';
				$table 		= 'brands';
			break;
		}
		
		$query 		= $this->db->query("SELECT $fieldName FROM $table WHERE id=$fieldID LIMIT 1");
		$row 		= $query->row();
		$name_Field = $row->$fieldName;
		
		$VotingRules[] = array('table'=>$tableName,
							'fieldName'=>$name_Field, 
							'fieldID'=>$fieldID, 
							'fieldValue'=>$name_Field, 
							'relation'=>$rel,
							'value'=>$val
							);
	}
	
	 $data['iWantVotingRulesRef']  = $VotingRules;
	 /*$sql   		  = "SELECT * FROM iWantRankRef WHERE campaignID = $cID"; 
	 $voters    	  = $this->db->query($sql);  
	 $data['iWantRankRef']  = $voters->result_array(); */
	
	$sqlSTr="SELECT items.id as iID, (SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, itemName,
			(SELECT totvote FROM iWantResultRef WHERE itemID = iID AND campaignID = $cID) AS voteTot,
			POSM_Type.typeName as POSM_TypeName 
			FROM items
			LEFT JOIN POSM_Type ON items.POSMTypeID = POSM_Type.id 
			WHERE  items.id IN (SELECT itemID FROM iWantResultRef WHERE campaignID = $cID) ORDER BY POSM_TypeName ASC, voteTot DESC";
	$voters = $this->db->query($sqlSTr);
	$data['topItems'] = $voters->result_array();
	
	
    $data['vfile']		= 'iWant_Items.php';
	$data['title']		= 'iWant Report';
	$data['rep']		= $rep;
	$data['repHeader']		= $header;
	//BREAD CRUMBS
	$HTTP_PATH 					 = HTTP_PATH."report/iWant";
	$data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
	$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	$data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/iWant> iWant Report </a>';
	$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	$data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/iWant_Items/$cID'>". $header[0]['campaignName'] ."</a>";
	 
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
	
	function per_country()
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		//$this->modules->module_checker(27,'REVIEW');
		$filter_WHERE 		= $this->modules->country();
		$filter_AND 		= $this->modules->country2();
		//$votingCampaignID 	= $id;
		
		$table						= 'campaign';
		$data['vfile']				= 'per_country.php';
	    $data['title']				= 'Per Country Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/iLike> iLike Report </a>';
		
		//TOTAL NUMBER OF ROWS			
		$data['active_page']=1;
		$sql       = $this->db->query("SELECT itemID FROM iLikeResultRef WHERE campaign = (SELECT id FROM campaign WHERE campaignType='iLike' and status='done' $filter_AND)");
		$sql       = $sql->result_array();
		$total_rec = count($sql);

		$pagenum = 1;
		$data['page_rows'] = 10; 
		$data['last'] = ceil($total_rec/$data['page_rows']);		
		$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];

	
		extract($_POST);
		if($action=="page")
		{
			$this->modules->module_checker(27,'REVIEW');
			$pagenum = $id;
			$data['active_page'] = $id; 
			$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		}

		$filter_WHERE = $this->modules->iLike_Campaign_country();
     	$sql = $this->db->query("SELECT *,campaign.id AS iLikeCampaignID FROM $table  
								LEFT JOIN admin_users 
								ON campaign.adminCreatorID = admin_users.id
								WHERE campaignType='iLike' AND status='done' $filter_WHERE ORDER BY iLikeCampaignID  DESC $max");
								
		$data['campaigns'] = $sql->result_array();
		
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
	
	function iLike_Voters($cID,$iID)
	{
		$sql = "SELECT voterID, fname, lname, gender, email, department, year_of_birth, tdate, ttime  FROM voters
				LEFT JOIN votexRef ON votexRef.voterID = voters.id 
				WHERE voters.campaignID = $cID AND votexRef.itemID=$iID AND votexRef.vote = 'yes' AND votingStatus = 'done'
				";
		
		$sql = $this->db->query($sql);
		$voters = $sql->result_array();
		
	
		echo "<table cellpadding='0' cellspacing='0' border=1 style='width:100%;margin: 0px auto;font-size:12px;' class='iLike_Result_Table'>
			<tr style='border-radius: 6px;'>
				<td style='width:10px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>No 		   </b></td> 
				<td style='width:100px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>   <b>First Name  </b></td> 
				<td style='width:100px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>   <b>Last  Name  </b></td> 
				<td style='width:30px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Gender  	   </b></td> 
				<td style='width:223px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>   <b>Email  	   </b></td> 
				<td style='width:50px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Department  </b></td> 
				<td style='width:50px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Year 	   </b></td> 
				<td style='width:50px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Age 	   </b></td> 
				<td style='width:90px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Date 	   </b></td> 
				<td style='width:30px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Time 	   </b></td> 
			</tr>";
		 
			$x = 0;
			$total = 0;
			foreach($voters as $v) { 
			extract($v);
			$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
		 
			echo	"<tr>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>	$x 				</td>
					  <td $c  style='text-align:left;padding: 0px 2px 0px 10px;'>	$fname 			</td>
					  <td $c  style='text-align:left;padding: 0px 2px 0px 10px;'>	$lname  		</td>
					  <td $c  style='text-align:left;padding: 0px 2px 0px 10px;'>	$gender  		</td>
					  <td $c  style='text-align:left;padding: 0px 2px 0px 10px;'>		$email  		</td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 10px;'>		$department  	</td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>		$year_of_birth  </td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>";
			echo 	  (date('Y')-$year_of_birth);		  
			echo      "</td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>		$tdate  		</td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>		$ttime  		</td>
					</tr>";
			}
		echo"</table>";
		
		
	}
	
	function iWant_Voters($cID,$iID)
	{	
		
		$sql = "SELECT voterID, fname, lname, gender, email, department, year_of_birth, tdate, ttime  FROM voters
				LEFT JOIN votexRef ON votexRef.voterID = voters.id 
				WHERE voters.campaignID = $cID AND votexRef.itemID=$iID AND votexRef.vote = 'yes' AND votingStatus = 'done'
				";

		$sql = $this->db->query($sql);
		$voters = $sql->result_array();
		
		//print_r($voters);
		
		echo "<table cellpadding='0' cellspacing='0' border=1 style='width:100%;margin: 0px auto;font-size:12px;' class='iLike_Result_Table'>
			<tr style='border-radius: 6px;'>
				<td style='width:10px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding:  2px 2px 2px 5px;'>    <b>No 		   	</b></td> 
				<td style='width:130px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>First Name  	</b></td> 
				<td style='width:130px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Last  Name  	</b></td> 
				<td style='width:80px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding:  2px 2px 2px 5px;'>    <b>Gender  	   	</b></td> 
				<td style='width:230px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Email  	   	</b></td> 
				<td style='width:50px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding:  2px 2px 2px 5px;'>    <b>Department  	</b></td> 
				<td style='width:50px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding:  2px 2px 2px 5px;'>    <b>Year 	   		</b></td> 
				<td style='width:50px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding:  2px 2px 2px 5px;'>    <b>Age 	   		</b></td> 
				<td style='width:90px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding:  2px 2px 2px 5px;'>    <b>Date 	   		</b></td> 
				<td style='width:30px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding:  2px 2px 2px 5px;'>    <b>Time 	   		</b></td> 
			</tr>";
		 
			$x = 0;
			$total = 0;
			foreach($voters as $v) { 
			extract($v);
			$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
		 
			echo	"<tr>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>		$x 				</td>
					  <td $c  style='text-align:left;padding: 0px 2px 0px 10px;'>		$fname 			</td>
					  <td $c  style='text-align:left;padding: 0px 2px 0px 10px;'>		$lname  		</td>
					  <td $c  style='text-align:left;padding: 0px 2px 0px 10px;'>		$gender  		</td>
					  <td $c  style='text-align:left;padding: 0px 2px 0px 10px;'>		$email  		</td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 10px;'>		$department  	</td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>		$year_of_birth  </td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>";
			echo 	  (date('Y')-$year_of_birth);		  
			echo      "</td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>		$tdate  		</td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>		$ttime  		</td>
					</tr>";
			}
		echo"</table>";
		
	}
	
	function campaign_items_summary($view='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(49,'REVIEW');
		
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(52);
		
		$filter="";
		$WHERE="";
		if($_SESSION['super_admin']!='y'  AND $_SESSION['countryID']!=0)
			$WHERE = "AND items.countryID =".$_SESSION['countryID']." ";
		
		//$votingCampaignID 	= $id;
		
		$table						= '';
		$data['vfile']				= 'voters_summary.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/campaign_items_summary/$view'> Campaign Items Summary </a>";
		
		//TOTAL NUMBER OF ROWS			
		extract($_POST);
		$cond="";
		$having="";
		$limit =isset($selpage)? $selpage:0;
		$campaignType = $view;
		
		if($view=="iLike")
			$table = "iLikeResultRef";
		else
			$table = "iWantResultRef";
			
		if(isset($Submit) OR isset($selpage))
		{	
			$val1 = mysql_real_escape_string($val1);
			$val1 = trim($val1);
			$val2 = mysql_real_escape_string($val2);
			$val2 = trim($val2);
			
			
			$condition = '';
			switch($cond1){
				case 'equal': 
					$condition = '=';
				break;
				case 'containing': 
					$condition = 'like';
				break;
				case 'greaterThan': 
					$condition = '>=';
				break;
				case 'lessThan': 
					$condition = '<=';
				break;
			}
			
			//itemcode, itemName, countryName
			if(($opt1=='itemCode' OR $opt1=='itemName') AND $val1!='')
			{
				if($condition=='=')
					$cond = " AND  $opt1 $condition '$val1'";
				if($condition=='like')
					$cond = " AND $opt1 $condition '%$val1%'";
			}
			
			if(($opt1=='countryName') AND $val1!='')
			{
				if($condition=='=')
					$cond = " AND  (SELECT countryName FROM country WHERE id = items.countryID) $condition '$val1'";
				if($condition=='like')
					$cond = " AND (SELECT countryName FROM country WHERE id = items.countryID) $condition '%$val1%'";
			}
			
			
			//vote
			$sum = "totvote";
			if($table=="iWantResultRef") $sum = "sum(totvote)";
			if(($opt1=='voteTot') AND $val1!='' AND $condition!='like' AND is_numeric($val1)) 
				$cond = " AND (SELECT $sum FROM $table WHERE itemID = items.id  )  $condition $val1";
			
	
			//dateReleased
			if($opt1=='tdate' AND $val1!='' AND $condition!='like') 
				$cond = " AND items.dateAdded $condition '$val1'";
			
			/*2ND SET*/
			switch($cond2){
				case 'equal': 
					$condition2 = '=';
				break;
				case 'containing': 
					$condition2 = 'like';
				break;
				case 'greaterThan': 
					$condition2 = '>=';
				break;
				case 'lessThan': 
					$condition2 = '<=';
				break;
			}
			
			//itemcode, itemName, countryName
			if(($opt2=='itemCode' OR $opt2=='itemName') AND $val2!='')
			{
				if($condition2=='=')
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator $opt2 $condition2 '%$val2%'";
			}
			
			if(($opt2=='countryName') AND $val2!='')
			{
				if($condition2=='=')
					$cond .= " $operator  (SELECT countryName FROM country WHERE id = items.countryID) $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator (SELECT countryName FROM country WHERE id = items.countryID) $condition2 '%$val2%'";
			}
			
			//vote
			$sum = "totvote";
			if($table=="iWantResultRef") $sum = "sum(totvote)";
			if(($opt2=='voteTot') AND $val2!='' AND $condition2!='like' AND is_numeric($val2)) 
				$cond .= " $operator (SELECT $sum FROM $table WHERE itemID = items.id  )  $condition2 $val2";
	
			//dateReleased
			if($opt2=='tdate' AND $val2!='' AND $condition2!='like') 
				$cond .= " $operator  items.dateAdded $condition2 '$val2'";

			$cond = ($cond=="") ? "" : " $cond";
			$WHERE  =  " $cond";
		}
		
		$data['POST'] = $_POST;
		if(isset($Reset)){
			$data['POST'] = array();
			$WHERE='';
		}
		
		
		if($view=='iLike'){
		$data['ViewType'] = "iLike";
		$data['ViewLabel'] = "MOST LIKE ITEMS";
		$sql = "SELECT items.id as iID, itemCode, (select typeName from POSM_Type as pt where pt.id=items.POSMTypeID) as ptype, itemName,
				(SELECT totvote FROM iLikeResultRef WHERE itemID = iID ) AS voteTot,
				(SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = iID) as item_image,
				(SELECT countryName FROM country WHERE id = items.countryID) as cName,
				items.dateAdded as tdate
			    FROM items WHERE items.id IN
			    (SELECT itemID FROM iLikeResultRef)  $WHERE
			    ORDER BY voteTot DESC";
	    }
		else{
		$data['ViewType'] = "iWant";
		$data['ViewLabel'] = "MOST WANT ITEMS";
		
		
		$sql = "SELECT items.id as iID, itemCode, (select typeName from POSM_Type as pt where pt.id=items.POSMTypeID) as ptype, itemName,
				(SELECT sum(totvote) FROM iWantResultRef WHERE itemID = items.id group by items.id) AS voteTot,
				(SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = iID LIMIT 0,1) as item_image,
				(SELECT countryName FROM country WHERE id = items.countryID) as cName,
				items.dateAdded as tdate
			    FROM items WHERE items.id IN
			    (SELECT itemID FROM iWantResultRef)  $WHERE group by items.id
			    ORDER BY voteTot DESC";
	    }
		
		$data['vfile']		= 'campaign_items_summary.php';

		$ctr = $this->db->query($sql);
		$ctr = $ctr->result_array();
		$data['totrec'] = count($ctr);
		$data['limit']  = $limit;
		
		$sql 	 = $this->db->query($sql." LIMIT $limit,20");
		$items	 = $sql->result_array();
		
	
		$table= "<table id='large' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:12px;' class='iLike_Result_Table tablesorter'>
				<thead>
				<tr>
					<th style='width:10px;text-align:center;color:white;padding:0px' bgcolor='#bb1d1d'>   					   <b>No 		 		  	</b></th> 
					<th style='width:10px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Item Code  	  		</b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Image  	  	  		</b></th> 
					<th style='width:50px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Item Name   	 	    </b></th> 
					<th style='width:100px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >      <b>Country  	  		  	</b></th> 
					<th style='width:100px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >      <b>Date  	  		  	</b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Vote  	  			</b></th>  
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Details  	  			</b></th>  
				</tr>
				</thead>
				<tbody>";
				 
					$x = $limit;			
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					$orig_itemName="";
					foreach($items as $r) { 
					extract($r);
					$c = (($x++)%2) == 0 ? "class='alter'" :  "";
					/*
					if($data['ViewType'] == "iWant"){
						$sql = $this->db->query("SELECT SUM(totvote) as voteTot FROM iWantResultRef WHERE itemID = $iID group by itemID");
						$sql = $sql->row();
						$voteTot = $sql->voteTot; 
					}*/
					
		$table.= "<tr>
				  <td>													$x      		</td>
				  <td style='text-align:left;padding-left:5px;'>		$itemCode  		</td>
				  <td style='text-align:center;'>			    		<img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 	</td>
				  <td style='text-align:left;padding-left:5px;' title='$orig_itemName'>		<a href='".HTTP_PATH."gallery/itemInfo2/$iID' target='_blank'>		$itemName</a>  </td>
				  <td style='text-align:left;padding-left:5px;'>		$cName  	</td>
				  <td style='text-align:center;padding-left:5px;'>		$tdate  </td>
				  <td style='text-align:center;padding-left:5px;'>		$voteTot  			</td>
				  <td style='text-align:center;'>				        <a onclick=\"showVoters('$campaignType',$iID)\" style='cursor:pointer;'> Details </a>	</td>
				</tr>";}
		$table.= "</tbody>";
					if(!$items)
						$table.=  "<tr><td colspan='14'>No match found.</td></tr>";
		$table.= "</table>";	
		
		
		$data['table'] 		= $table;

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
	
	/*
	function campaign_items_summary()
	{		
		$this->modules->module_checker(49,'REVIEW');
		
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(47);
		
		$sortOpt = "";
		$filter = "";
		$data['Item_Type'] = "D|1|0";
		$data['Name'] 	   = "D|2|0";
		$data['Brand'] 	   = "D|3|0";
		$data['Name'] 	   = "D|4|0";
		$data['Country']   = "D|4|0";
		$data['Likes'] 	   = "D|5|0";
		
		$data['Item_Type2'] = "D|1b|1";
		$data['Name2'] 	   	= "D|2b|1";
		$data['Brand2'] 	= "D|3b|1";
		$data['Name2'] 	    = "D|4b|1";
		$data['Country2']   = "D|4b|1";
		$data['Likes2'] 	= "D|5b|1";
		$data['activeTab'] 	= 0;
		
		//if($_SESSION['super_admin']!='y' AND $_SESSION['countryID']!=0)
		//	$filter = " AND items.countryID = ".$_SESSION['countryID'];
		
		//echo $filter;
		
		//ORDER
		$order = "voteTot ";
		$order2 = "voteTot ";
		if(!empty($_POST))
		{
			extract($_POST);
			
			$pieces = explode('|',$sortOpt);
		
			switch($pieces[1]){
				case '1':
					$order = "ptype ";
					$data['Item_Type'] = $sortOpt;
				break;
				case '1b':
					$order2 = "ptype ";
					$data['Item_Type2'] = $sortOpt;
				break;
				
				case '2':
					$order = "itemName ";
					$data['Name'] 	   = $sortOpt;
				break;
				case '2b':
					$order2 = "itemName ";
					$data['Name2'] 	   = $sortOpt;
				break;
				
				case '3':
					$order = "bName ";
					$data['Brand'] 	  = $sortOpt;
				break;
				case '3b':
					$order2 = "bName ";
					$data['Brand2']  = $sortOpt;
				break;
				
				case '4':
					$order = "cName ";
					$data['Country']  = $sortOpt;
				break;
				case '4b':
					$order2 = "cName ";
					$data['Country2']  = $sortOpt;
				break;
				
				case '5':
					$order = "voteTot ";
					$data['Likes']  = $sortOpt;
				break;
				case '5b':
					$order2 = "voteTot ";
					$data['Likes2']  = $sortOpt;
				break;
			}
			
			if($pieces[0]=="A"){
				$order .= "ASC";
				$order2 .= "ASC";
			}else{
				$order .= "DESC";
				$order2 .= "DESC";
			}
	
			$data['activeTab'] 	= $pieces[2];
		}
		
		
		if($order=="voteTot ")
			$order="voteTot DESC";
			
		$sql = "SELECT items.id as iID, itemCode, (select typeName from POSM_Type as pt where pt.id=items.POSMTypeID) as ptype, itemName,
				(SELECT totvote FROM iLikeResultRef WHERE itemID = iID ) AS voteTot,
				(SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = iID) as item_image,
				(SELECT countryName FROM country WHERE id = items.countryID) as cName,
				items.dateAdded as tdate
			    FROM items WHERE items.id IN
			    (SELECT itemID FROM iLikeResultRef)  $filter
			    ORDER BY $order";
	 
		$report    = $this->db->query($sql);  
		$rep       = $report->result_array(); 
		$data['vfile']		= 'campaign_items_summary.php';
		
		
		if($order2=="voteTot ")
			$order2="voteTot DESC";
			
		$sql = "SELECT items.id as iID, itemCode, (select typeName from POSM_Type as pt where pt.id=items.POSMTypeID) as ptype, itemName,
				(SELECT totvote FROM iWantResultRef WHERE itemID = iID ) AS voteTot,
				(SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = iID) as item_image,
				(SELECT countryName FROM country WHERE id = items.countryID) as cName,
				items.dateAdded as tdate
			    FROM items WHERE items.id IN
			    (SELECT itemID FROM iWantResultRef) $filter
			    ORDER BY $order2";
	 
		$report    = $this->db->query($sql);  
		$rep2       = $report->result_array(); 


		$data['vfile']		= 'campaign_items_summary.php';
		$data['title']		= 'Items Summary Report';
		$data['rep']		= $rep;
		$data['rep2']		= $rep2;
		
		//BREAD CRUMBS
		$HTTP_PATH 					 = HTTP_PATH."report/iLike";
		$data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
		$data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/campaign_items_summary> Items Ranking </a>';
		
		
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
	*/
	function Summay_iLike_Voters($iID)
	{
		$sql = "SELECT voterID, fname, lname, gender, email, department, year_of_birth, tdate, ttime  FROM votexRef
				LEFT JOIN voters   ON votexRef.voterID    = voters.id 
				LEFT JOIN campaign ON votexRef.campaignID = campaign.id 
				WHERE votexRef.itemID=$iID AND votexRef.vote = 'yes' AND voters.votingStatus = 'done' AND campaign.campaignType = 'iLike'
				GROUP BY voters.id";
		
		$sql = $this->db->query($sql);
		$voters = $sql->result_array();
		
		//print_r($voters);
		
		echo "<table cellpadding='0' cellspacing='0' border=1 style='width:100%;margin: 0px auto;font-size:12px;' class='iLike_Result_Table'>
			<tr style='border-radius: 6px;'>
				<td style='width:10px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>No 		   	</b></td> 
				<td style='width:130px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>   <b>First Name  	</b></td> 
				<td style='width:130px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>   <b>Last  Name  	</b></td> 
				<td style='width:30px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Gender  	   	</b></td> 
				<td style='width:230px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>   <b>Email  	   	</b></td> 
				<td style='width:50px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Department  	</b></td> 
				<td style='width:50px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Year 	   		</b></td> 
				<td style='width:50px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Age 	   		</b></td> 
				<td style='width:100px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Date 	   		</b></td> 
				<td style='width:30px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Time 	   		</b></td> 
			</tr>";
		 
			$x = 0;
			$total = 0;
			foreach($voters as $v) { 
			extract($v);
			$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
		 
			echo	"<tr>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>		$x 				</td>
					  <td $c  style='text-align:left;padding: 0px 2px 0px 10px;'>		$fname 			</td>
					  <td $c  style='text-align:left;padding: 0px 2px 0px 10px;'>		$lname  		</td>
					  <td $c  style='text-align:left;padding: 0px 2px 0px 10px;'>		$gender  		</td>
					  <td $c  style='text-align:left;padding: 0px 2px 0px 10px;'>		$email  		</td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 10px;'>		$department  	</td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>		$year_of_birth  </td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>".	(date('Y')-$year_of_birth) ."</td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>		$tdate  		</td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>		$ttime  		</td>
					</tr>";
			}
		echo"</table>";
		
	}

	function Summay_iWant_Voters($iID)
	{
		$sql = "SELECT voterID, fname, lname, gender, email, department, year_of_birth, tdate, ttime  FROM voters
				LEFT JOIN votexRef   ON votexRef.voterID    = voters.id 
				LEFT JOIN campaign   ON votexRef.campaignID = campaign.id
				LEFT JOIN iWantResultRef ON iWantResultRef.campaignID = campaign.id
				WHERE (votexRef.itemID=$iID AND votexRef.vote = 'yes' AND voters.votingStatus = 'done' AND campaign.campaignType = 'iWant' AND iWantResultRef.itemID = $iID)
				GROUP BY voters.id";
			
		$sql = $this->db->query($sql);
		$voters = $sql->result_array();
	
		echo "<table cellpadding='0' cellspacing='0' border=1 style='width:100%;margin: 0px auto;font-size:12px;' class='iLike_Result_Table'>
			<tr style='border-radius: 6px;'>
				<td style='width:10px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>No 		    </b></td> 
				<td style='width:130px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>   <b>First Name  	</b></td> 
				<td style='width:130px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>   <b>Last  Name  	</b></td> 
				<td style='width:30px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Gender  	   	</b></td> 
				<td style='width:230px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>   <b>Email  	   	</b></td> 
				<td style='width:50px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Department  	</b></td> 
				<td style='width:50px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Year 	   		</b></td> 
				<td style='width:50px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Age 	   		</b></td> 
				<td style='width:100px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Date 	   		</b></td> 
				<td style='width:30px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Time 	   		</b></td>  
			</tr>";
		 
			$x = 0;
			$total = 0;
			foreach($voters as $v) { 
			extract($v);
			$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
		 
			echo	"<tr>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>		$x 				</td>
					  <td $c  style='text-align:left;padding: 0px 2px 0px 10px;'>		$fname 			</td>
					  <td $c  style='text-align:left;padding: 0px 2px 0px 10px;'>		$lname  		</td>
					  <td $c  style='text-align:left;padding: 0px 2px 0px 10px;'>		$gender  		</td>
					  <td $c  style='text-align:left;padding: 0px 2px 0px 10px;'>		$email  		</td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 10px;'>		$department  	</td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>		$year_of_birth  </td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>".	(date('Y')-$year_of_birth) ."</td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>		$tdate  		</td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>		$ttime  		</td>
					</tr>";
			}
		echo"</table>";
		
	}
	
	function Summay_iWant_Voters2($iID)
	{
		$sql = "SELECT voterID, fname, lname, gender, email, department, year_of_birth, tdate, ttime  FROM voters
				LEFT JOIN votexRef   ON votexRef.voterID    = voters.id 
				LEFT JOIN campaign   ON votexRef.campaignID = campaign.id
				LEFT JOIN iWantResultRef ON iWantResultRef.campaignID = campaign.id
				WHERE (votexRef.itemID=$iID AND votexRef.vote = 'yes' AND voters.votingStatus = 'done' AND campaign.campaignType = 'iWant')
				GROUP BY voters.id";
			
		$sql = $this->db->query($sql);
		$voters = $sql->result_array();
	
		echo "<table cellpadding='0' cellspacing='0' border=1 style='width:100%;margin: 0px auto;font-size:12px;' class='iLike_Result_Table'>
			<tr style='border-radius: 6px;'>
				<td style='width:10px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>No 		    </b></td> 
				<td style='width:130px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>   <b>First Name  	</b></td> 
				<td style='width:130px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>   <b>Last  Name  	</b></td> 
				<td style='width:30px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Gender  	   	</b></td> 
				<td style='width:230px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>   <b>Email  	   	</b></td> 
				<td style='width:50px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Department  	</b></td> 
				<td style='width:50px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Year 	   		</b></td> 
				<td style='width:50px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Age 	   		</b></td> 
				<td style='width:100px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Date 	   		</b></td> 
				<td style='width:30px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Time 	   		</b></td>  
			</tr>";
		 
			$x = 0;
			$total = 0;
			foreach($voters as $v) { 
			extract($v);
			$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
		 
			echo	"<tr>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>		$x 				</td>
					  <td $c  style='text-align:left;padding: 0px 2px 0px 10px;'>		$fname 			</td>
					  <td $c  style='text-align:left;padding: 0px 2px 0px 10px;'>		$lname  		</td>
					  <td $c  style='text-align:left;padding: 0px 2px 0px 10px;'>		$gender  		</td>
					  <td $c  style='text-align:left;padding: 0px 2px 0px 10px;'>		$email  		</td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 10px;'>		$department  	</td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>		$year_of_birth  </td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>".	(date('Y')-$year_of_birth) ."</td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>		$tdate  		</td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>		$ttime  		</td>
					</tr>";
			}
		echo"</table>";
		
	}
	
	
	function item_division($view='',$cID='',$year='',$month='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(73,'REVIEW');
		
		$filter_WHERE="WHERE items.countryID = $cID";
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/BU_activeness_index> Activeness of Business Units Index </a>';
		
		$groupBy      = "";
		if($view=='gCountry'){
			$data['vType'] = $view;
		}elseif($view=='gYear'){
			$filter_WHERE .= "  AND YEAR(items.dateAdded) = $year";
			$data['vType'] = $view;
			$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
			$data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/BU_activeness_per_Year/$cID'> Activeness of Business Units per Year</a>";
		}elseif($view=='gMonth'){
			$filter_WHERE .= " AND YEAR(items.dateAdded) = $year AND MONTH(items.dateAdded) = $month";
			$data['vType'] = $view;
			
			$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
			$data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/BU_activeness_per_Year/$cID'> Activeness of Business Units per Year</a>";
			$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
			$data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/BU_activeness_per_Month/$cID/$year'> Activeness of Business Units per Month</a>";
		}	
		
		if($cID==0){
			$filter_WHERE = "WHERE items.countryID != 0";
		}if($cID==0 AND $view=='gYear'){
			$filter_WHERE = "WHERE items.countryID != 0 AND YEAR(items.dateAdded) = $year";
		}if($cID==0 AND $view=='gMonth'){
			$filter_WHERE = "WHERE items.countryID != 0 AND YEAR(items.dateAdded) = $year AND MONTH(items.dateAdded) = $month";
		}
		
		
		$table						= 'item_division';
		$data['vfile']				= 'item_division.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		$data['countryID']			= $cID;
		$data['cyear']				= $year;	
		$data['previewType']		= $view;
		
		
		//POSM TYPE;
		$arr = '';
		$sql   = $this->db->query("SELECT DISTINCT(items.POSMTypeID) as fieldValue, (SELECT typeName FROM POSM_Type WHERE POSM_Type.id = items.POSMTypeID) as col, AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice, 
								   COUNT(items.id) as num_items, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year, country.id as cID 
								   FROM   items 	
								   LEFT   JOIN country 		ON country.id  = items.countryID
								   $filter_WHERE
								   GROUP BY items.POSMTypeID ORDER BY YEAR(items.dateAdded) DESC, MONTH(items.dateAdded) DESC");
		$arr[] = array('table'=>'POSM TYPE',
					   'fld'=>'POSMTypeID',
					   'rows'=>$sql->result_array());
		
		//POSM STATUS
		$sql   = $this->db->query("SELECT DISTINCT(items.POSMStatusID) as fieldValue, (SELECT statusName FROM POSM_Status WHERE POSM_Status.id = items.POSMStatusID) as col, AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice, 
								   COUNT(items.id) as num_items, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year, country.id as cID 
								   FROM   items 	
								   LEFT   JOIN country 		ON country.id  = items.countryID
								   $filter_WHERE
								   GROUP BY items.POSMStatusID ORDER BY YEAR(items.dateAdded) DESC, MONTH(items.dateAdded) DESC");
		$arr[] = array('table'=>'POSM STATUS',
					   'fld'=>'POSMStatusID',
					   'rows'=>$sql->result_array());
		
		//OUTLET TYPE
		$sql   = $this->db->query("SELECT DISTINCT(items.OUTLETStatusID) as fieldValue, (SELECT statusName FROM OUTLET_Status WHERE OUTLET_Status.id = items.OUTLETStatusID) as col, AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice, 
								   COUNT(items.id) as num_items, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year, country.id as cID 
								   FROM   items 	
								   LEFT   JOIN country 		ON country.id  = items.countryID
								   $filter_WHERE
								   GROUP BY items.OUTLETStatusID ORDER BY YEAR(items.dateAdded) DESC, MONTH(items.dateAdded) DESC");
		$arr[] = array('table'=>'SERVICE ITEM OUTLET TYPE',
					   'fld'=>'OUTLETStatusID',
					   'rows'=>$sql->result_array());
		
		//PREMIUM ITEM TYPE
		$sql   = $this->db->query("SELECT DISTINCT(items.PremiumTypeID) as fieldValue, (SELECT premiumTypeName FROM premiumItemType WHERE premiumItemType.id = items.PremiumTypeID) as col, AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice, 
								   COUNT(items.id) as num_items, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year, country.id as cID 
								   FROM   items 	
								   LEFT   JOIN country 		ON country.id  = items.countryID
								   $filter_WHERE
								   GROUP BY items.PremiumTypeID ORDER BY YEAR(items.dateAdded) DESC, MONTH(items.dateAdded) DESC");
		$arr[] = array('table'=>'PREMIUM ITEM TYPE',
					   'fld'=>'PremiumTypeID',
					   'rows'=>$sql->result_array());
		
		//MATERIAL TYPE
		$sql   = $this->db->query("SELECT DISTINCT(items.MaterialTypeID) as fieldValue, (SELECT materialName FROM MATERIAL_Type WHERE MATERIAL_Type.id = items.MaterialTypeID) as col, AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice, 
								   COUNT(items.id) as num_items, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year, country.id as cID 
								   FROM   items 	
								   LEFT   JOIN country 		ON country.id  = items.countryID
								   $filter_WHERE
								   GROUP BY items.MaterialTypeID ORDER BY YEAR(items.dateAdded) DESC, MONTH(items.dateAdded) DESC");
		$arr[] = array('table'=>'MATERIAL TYPE',
						'fld'=>'MaterialTypeID',
						'rows'=>$sql->result_array());
		
		//BRAND
		$sql   = $this->db->query("SELECT DISTINCT(items.brandID) as fieldValue, (SELECT brandName FROM brands WHERE brands.id = items.brandID) as col, AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice, 
								   COUNT(items.id) as num_items, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year, country.id as cID 
								   FROM   items 	
								   LEFT   JOIN country 		ON country.id  = items.countryID
								   $filter_WHERE
								   GROUP BY items.brandID ORDER BY YEAR(items.dateAdded) DESC, MONTH(items.dateAdded) DESC");
		$arr[] = array('table'=>'BRAND',
					   'fld'=>'brandID',
					   'rows'=>$sql->result_array());
		
		$data['results'] = $arr;
		
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
	
	function Distribution_Items($action='',$id='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(73,'REVIEW');
		
		$filter_WHERE='';
		if($_SESSION['super_admin']!='y')
			$filter_WHERE 		= "WHERE items.countryID =".$_SESSION['countryID'] ." AND YEAR(items.dateAdded) = ".DATE("Y");
		else
			$filter_WHERE 		= "WHERE YEAR(items.dateAdded) = ".DATE("Y");
		
		$table						= 'Distribution_Items';
		$data['vfile']				= 'Distribution_Items.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/Distribution_Items> Distribution of Promo Items </a>';
		
		//TOTAL NUMBER OF ROWS			
		
		extract($_POST);
		$WHERE 	 =  $filter_WHERE;
		
		$data['per_country'] = TRUE;
		$sql   = $this->db->query("SELECT country.countryName AS cName, country.id AS cID, AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice, 
								   COUNT(items.id) as num_items, target, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year 
								   FROM   items 		 
								   LEFT   JOIN country 		ON country.id  = items.countryID 
								   LEFT   JOIN target_items ON target_items.countryID = items.countryID 
								   $WHERE
								   GROUP BY country.id ORDER BY YEAR(items.dateAdded) DESC");
		
		if(isset($filter))
		{   
			$data['per_country'] = FALSE;
			$WHERE 	 = '';
			if($countryID!='' AND $countryID!='all') $WHERE  .=  " items.countryID = $countryID AND";
			if($countryID=='all')					 $WHERE   =  "";
			if($cyear!=''  AND $cyear!='all') 	 	 $WHERE  .=  " YEAR(items.dateAdded) = $cyear AND";
			
			if($WHERE!='')  $WHERE  =  substr("WHERE ".$WHERE,0,-3);
			
			$sql   = $this->db->query("SELECT country.countryName AS cName, country.id AS cID,  AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice, 
									   COUNT(items.id) as num_items, target, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year 
									   FROM items 		 
									   LEFT   JOIN country 		ON country.id  = items.countryID 
									   LEFT   JOIN target_items ON target_items.countryID = items.countryID 
									   $WHERE
									   GROUP BY cName, YEAR(items.dateAdded) ORDER BY YEAR(items.dateAdded) DESC");
		}
		
		$data['POST'] = $_POST;
		$data['reports']   = $sql->result_array();
		

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
	
	function Distribution_Items_in_Details($cID='',$category='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(73,'REVIEW');
		$c='';
		
		if($cID!=''){
			$data['countryID'] = $cID;
			$c = "country.id = $cID AND"; 
		}	
			
		$filter_WHERE	= "";
		$filter_WHERE 	= "WHERE $c YEAR(items.dateAdded) = ".DATE("Y");
		
		//die();
		
		$table						= 'BU_activeness';
		$data['vfile']				= 'Distribution_Items_in_Details.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					 = HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/Distribution_Items> Distribution of Promo Items in Details </a>';
		
		//TOTAL NUMBER OF ROWS			
		
		extract($_POST);
		$WHERE 	 =  $filter_WHERE;
		
		$data['default'] = TRUE;
		$groupBy = "items.POSMTypeID";
		if(isset($filter))
		{   
			$data['default'] = FALSE;
			$data['countryID'] = $countryID;
			$WHERE 	 = '';
			if($countryID!='' AND $countryID!='all') $WHERE  .=  " items.countryID = $countryID AND";
			if($countryID=='all')					 $WHERE   =  "";
			if($cyear!=''  AND $cyear!='all') 	 	 $WHERE  .=  " YEAR(items.dateAdded) = $cyear AND";
			if($fmonth!='' AND $fmonth!='all' AND $tmonth!='' AND $tmonth!='all') 	 	 
					$WHERE  .=  " MONTH(items.dateAdded) >= $fmonth AND  MONTH(items.dateAdded) <= $tmonth AND";
			
			if($WHERE!='')  $WHERE  =  substr("WHERE ".$WHERE,0,-3);
			$groupBy =  "items.POSMTypeID, MONTH(items.dateAdded)";
		}
		
		switch($category)
		{
			case 'POSMTypeID':
			$data['category'] = 'POSMTypeID';
			$data['category_label'] = 'POSM TYPE';
			$sql   = $this->db->query("SELECT DISTINCT(items.POSMTypeID) as fieldValue, (SELECT typeName FROM POSM_Type WHERE POSM_Type.id = items.POSMTypeID) as col, AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice, 
									   COUNT(items.id) as num_items, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year, country.id as cID 
									   FROM   items 	
									   LEFT   JOIN country 		ON country.id  = items.countryID
									   $WHERE
									   GROUP BY $groupBy ORDER BY YEAR(items.dateAdded) DESC, MONTH(items.dateAdded) DESC");
			break;
			case 'POSMStatusID':
			$data['category'] = 'POSMStatusID';
			$data['category_label'] = 'POSM STATUS';
			$sql   = $this->db->query("SELECT DISTINCT(items.POSMStatusID) as fieldValue, (SELECT statusName FROM POSM_Status WHERE POSM_Status.id = items.POSMStatusID) as col, AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice, 
									   COUNT(items.id) as num_items, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year, country.id as cID 
									   FROM   items 	
									   LEFT   JOIN country 		ON country.id  = items.countryID
									   $WHERE
									   GROUP BY items.POSMStatusID, MONTH(items.dateAdded) ORDER BY YEAR(items.dateAdded) DESC, MONTH(items.dateAdded) DESC");
			break;
			case 'OUTLETStatusID':
			$data['category'] = 'OUTLETStatusID';
			$data['category_label'] = 'SERVICE ITEM OUTLET TYPE';
			$sql   = $this->db->query("SELECT DISTINCT(items.OUTLETStatusID) as fieldValue, (SELECT statusName FROM OUTLET_Status WHERE OUTLET_Status.id = items.OUTLETStatusID) as col, AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice, 
									   COUNT(items.id) as num_items, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year, country.id as cID 
									   FROM   items 	
									   LEFT   JOIN country 		ON country.id  = items.countryID
									   $WHERE
									   GROUP BY items.OUTLETStatusID, MONTH(items.dateAdded) ORDER BY YEAR(items.dateAdded) DESC, MONTH(items.dateAdded) DESC");
			break;
			case 'PremiumTypeID':
			$data['category'] = 'PremiumTypeID';
			$data['category_label'] = 'PREMIUM ITEM TYPE';
			$sql   = $this->db->query("SELECT DISTINCT(items.PremiumTypeID) as fieldValue, (SELECT premiumTypeName FROM premiumItemType WHERE premiumItemType.id = items.PremiumTypeID) as col, AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice, 
									   COUNT(items.id) as num_items, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year, country.id as cID 
									   FROM   items 	
									   LEFT   JOIN country 		ON country.id  = items.countryID
									   $WHERE
									   GROUP BY items.PremiumTypeID, MONTH(items.dateAdded) ORDER BY YEAR(items.dateAdded) DESC, MONTH(items.dateAdded) DESC");
			break;
			case 'MaterialTypeID':
			$data['category'] 		= 'MaterialTypeID';
			$data['category_label'] = 'MATERIAL TYPE';
			$sql   = $this->db->query("SELECT DISTINCT(items.MaterialTypeID) as fieldValue, (SELECT materialName FROM MATERIAL_Type WHERE MATERIAL_Type.id = items.MaterialTypeID) as col, AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice, 
									   COUNT(items.id) as num_items, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year, country.id as cID 
									   FROM   items 	
									   LEFT   JOIN country 		ON country.id  = items.countryID
									   $WHERE
									   GROUP BY items.MaterialTypeID, MONTH(items.dateAdded) ORDER BY YEAR(items.dateAdded) DESC, MONTH(items.dateAdded) DESC");
			break;
			case 'brandID':
			$data['category'] = 'brandID';
			$data['category_label'] = 'BRAND';
			$sql   = $this->db->query("SELECT DISTINCT(items.brandID) as fieldValue, (SELECT brandName FROM brands WHERE brands.id = items.brandID) as col, AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice, 
									   COUNT(items.id) as num_items, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year, country.id as cID 
									   FROM   items 	
									   LEFT   JOIN country 		ON country.id  = items.countryID
									   $WHERE
									   GROUP BY items.brandID, MONTH(items.dateAdded) ORDER BY YEAR(items.dateAdded) DESC, MONTH(items.dateAdded) DESC");
			break;
			
			$data['countryID'] = $countryID;
		}
		
		$data['POST'] = $_POST;			
		$data['reports']   = $sql->result_array();
		

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
	
	function Distribution_Items_Preview($view='',$category='',$fieldValue='',$countryID='',$month='',$year='')
	{
		echo "<!DOCTYPE HTML>
			  <html>
			  <head>";
		echo "<script type='text/javascript' src='".HTTP_PATH ."js/jquery-1.8.0.js'></script>";
		echo "<script type='text/javascript' src='".HTTP_PATH ."js/jquery.tablesorter.js'></script>";
		echo "<script type='text/javascript'>
				$(function() {
						$('table').tablesorter({widgets: ['zebra'] })
				});
			</script>
			</head>
			</body>";
		
		$field  ='';
		$filter ='';
		switch($category)
		{
			case 'POSMTypeID':
			$field   = "(SELECT typeName FROM POSM_Type where POSM_Type.id=items.POSMTypeID) as ptype";
			$filter  = "items.POSMTypeID = $fieldValue";
			$label 	 = "POSM TYPE";
			break;
			
			case 'POSMStatusID':
			$data['category'] = 'POSMStatusID';
			$field   = "(SELECT statusName FROM POSM_Status where POSM_Status.id=items.POSMStatusID) as ptype";
			$filter  = "items.POSMStatusID = $fieldValue";
			$label 	 = "POSM STATUS";
			break;
			
			case 'OUTLETStatusID':
			$data['category'] = 'OUTLETStatusID';
			$field   = "(SELECT statusName FROM OUTLET_Status where OUTLET_Status.id=items.OUTLETStatusID) as ptype";
			$filter  =  "items.OUTLETStatusID = $fieldValue";
			$label 	 = "OUTLET TYPE";
			break;
			
			case 'PremiumTypeID':
			$data['category'] = 'PremiumTypeID';
			$field   = "(SELECT premiumTypeName FROM premiumItemType where premiumItemType.id=items.PremiumTypeID) as ptype";
			$filter  =  "items.PremiumTypeID = $fieldValue";
			$label 	 = "PREMIUM TYPE";
			break;
			
			case 'MaterialTypeID':
			$data['category'] 		= 'MaterialTypeID';
			$field   = "(SELECT materialName FROM MATERIAL_Type where MATERIAL_Type.id=items.MaterialTypeID) as ptype";
			$filter  =  "items.MaterialTypeID = $fieldValue";
			$label 	 = "MATERIAL";
			break;
			
			case 'brandID':
			$data['category'] = 'brandID';
			$field   = "(SELECT brandName FROM brands where brands.id=items.brandID) as ptype";
			$filter  =  "items.brandID = $fieldValue";
			$label 	 = "BRAND";
			break;
			
			$data['countryID'] = $countryID;
		}
		
		if($view=='default'){
			$sql = "SELECT items.id as itemID, $field, itemName,  itemCode, UnitPrice, USD_Price, 
						(SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, items.dateAdded as dUploaded, full_name, publish 
						 FROM items LEFT JOIN admin_users ON admin_users.id = items.user_id
						 WHERE items.countryID = $countryID AND YEAR(items.dateAdded) = $year  
						 AND $filter
						 ORDER BY UnitPrice DESC";
		}else{
			$sql = "SELECT items.id as itemID, $field, itemName,  itemCode, UnitPrice, USD_Price, 
						(SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, items.dateAdded as dUploaded, full_name, publish 
						 FROM items LEFT JOIN admin_users ON admin_users.id = items.user_id
						 WHERE items.countryID = $countryID AND MONTH(items.dateAdded) = $month AND YEAR(items.dateAdded) = $year  
						 AND $filter
						 ORDER BY UnitPrice DESC";
		}
			$sql 	 = $this->db->query($sql);
			$items	 = $sql->result_array();
			//print_r($items);
			
		echo "<table id='large' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:12px;' class='iLike_Result_Table tablesorter'>
				<thead>
				<tr>
					<th style='width:10px;text-align:center;color:white;padding:0px' bgcolor='#bb1d1d'>   					   <b>No 		 		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Item Code  	  	  </b></th> 
					<th style='width:19px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Image   	 	      </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Name  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>$label  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Local Price  	  	  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>USD   Price  	  	  </b></th> 
					<th style='width:30px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Publish  	  		  </b></th> 
					<th style='width:75px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Date uploaded  	  </b></th> 
				</tr>
				</thead>
				<tbody>";
				 
					$x = 0;			
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					$sumUnitPrice=0;
					$sumUSD_Price=0;
					foreach($items as $r) { 
					extract($r);
					$ptype = ($ptype=='') 	   ? 'Uncategorize' : $ptype;
					$publish = ($publish=='y') ? 'Yes' : 'No';
					$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
					$sumUnitPrice += number_format($UnitPrice, 2, '.', '');
					$sumUSD_Price +=  number_format($USD_Price, 2, '.', '');
		echo 	"<tr>
				  <td $c>													$x      																		</td>
				  <td $c>													$itemCode      																	</td>
				  <td $c style='text-align:center;'>			  <img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 	</td>
				  <td $c style='text-align:left;padding-left:5px;'>	<a href='".HTTP_PATH."gallery/itemInfo2/$itemID' target='_blank'>		$itemName</a>  </td>
				  <td $c style='text-align:left;padding-right:20px;'>	$ptype											</td>
				  <td $c style='text-align:right;padding-right:20px;'>	". number_format($UnitPrice, 2, '.', '') ."										</td>
				  <td $c style='text-align:right;padding-right:20px;'>	". number_format($USD_Price, 2, '.', '')."										</td>
				  <td $c style='text-align:center;'>	$publish										</td>
				  <td $c style='text-align:center;'>				". date("M d, Y", strtotime($dUploaded)) ."			</td>
				</tr>";}
		echo 	"<tr> 
					<td>Average</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td style='text-align:right;padding-right:20px;'>". number_format($sumUnitPrice/$x, 2, '.', '') ."</td>
					<td style='text-align:right;padding-right:20px;'>". number_format($sumUSD_Price/$x, 2, '.', '') ."</td>
					<td></td>
					<td></td>
				</tr>";		
				
		echo	"</tbody>";
					if(!$items)
						echo "<tr><td colspan='7'>No match found.</td></tr>";
		echo	"</table>";	
		
		
		echo "</body>
			  </html>";
	}
	
}