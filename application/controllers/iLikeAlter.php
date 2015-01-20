<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ILikeAlter extends CI_Controller {
 
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
		$this->modules->session_handler();
   }
	   

    function iLikeCampaign_Items_Checker($votingCampaignID='',$revote='')
    {
		$filter_AND   = $this->modules->country2();
		$filter_WHERE = $this->modules->country();
		$CI2 =& get_instance();
		$CI2->load->library('fv');
		
		if($_SESSION['super_admin']=='y'){
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
					WHERE items.publish='y' 	$filter_AND 
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
			
			$query 		= $this->db->query("SELECT $fieldName FROM $table WHERE id=$fieldID LIMIT 1");
			$row 		= $query->row();
			$name_Field = $row->$fieldName;
			
			$sql 		   = $this->db->query($itemDB);
			$data['items'] = $sql->result_array();		
			
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
	
	function votingCampaign($id='',$action='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(27,'REVIEW');
		$filter_WHERE = $this->modules->country();
		$filter_AND = $this->modules->country2();
		$votingCampaignID = $id;
		
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
	  
		//CSRF
		$data['csrf'] = "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>"; 
		
		//TOTAL NUMBER OF ROWS			
		$data['active_page']=1;
		$sql       = $this->db->query("SELECT id FROM campaign WHERE campaignType='iLike' and status!='done' $filter_AND");
		$sql       = $sql->result_array();
		$total_rec = count($sql);

		$pagenum = 1;
		$data['page_rows'] = 10; 
		$data['last'] = ceil($total_rec/$data['page_rows']);		
		$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		
		//RULES
		$iLikeCampaignRules = '';
		$sql = "select * from iLikeCampaignRules $filter_WHERE";
		$rules = $this->db->query($sql);  
		$rules = $rules->result_array(); 
		
		foreach($rules as  $r)
		{
			extract($r); 
			$iLikeCampaignRules .= " items.$fieldName='$fieldID' OR";  
	    }
		$iLikeCampaignRules  = substr($iLikeCampaignRules,0,strlen($iLikeCampaignRules)-2);
		
		
		//ITEMS 
		$itemDB = "SELECT *,
				POSM_Type.typeName as POSM_TypeName,
				items.id 				 as itemID 
				FROM items 
				LEFT JOIN POSM_Type 		ON items.POSMTypeID = POSM_Type.id 
				LEFT JOIN POSM_Status 		ON items.POSMStatusID = POSM_Status.id 
				LEFT JOIN OUTLET_Status 	ON items.OUTLETStatusID = OUTLET_Status.id
				LEFT JOIN premiumItemType 	ON items.PremiumTypeID = premiumItemType.id 
				LEFT JOIN MATERIAL_Type 	ON items.MaterialTypeID = MATERIAL_Type.id 
				LEFT JOIN country 			ON items.countryID = country.id 
				WHERE items.publish='y' 	$filter_AND 
				AND($iLikeCampaignRules) 	
				ORDER by itemID DESC";
		
		$sql 		   = $this->db->query($itemDB);
		$data['items'] = $sql->result_array();	
		
		extract($_POST);
		
	   if($action=="")
		{
			$this->modules->module_checker(27,'EDIT');
			$data['vfile']	= 'votingCampaignFORM.php';
			$data['rules'] 	= $this->iLikeCampaign_Items_Checker($votingCampaignID);
			
			//CHECK IF CAMPAIGN IS ON PROGRESS OR DONE
			$sql = $this->db->query("SELECT status FROM campaign WHERE id = $votingCampaignID LIMIT 1");
			$row = $sql->row();
			$status = $row->status;
			
			if($status == 'on progress' OR $status == 'done'){
				$sql = $this->db->query("SELECT *, items.id AS itemID, POSM_Type.typeName as POSM_TypeName 
										FROM items, POSM_Type 
										WHERE items.id IN (SELECT itemID FROM campaignItemsXref WHERE campaignID = $votingCampaignID)
										AND items.POSMTypeID = POSM_Type.id  ORDER BY itemID DESC");
				$data['items']  		= $sql->result_array();
				
				$data['rules'] 			= $this->iLikeCampaign_Items_Checker($votingCampaignID,$revote=TRUE);
				$data['min_committees'] = $this->min_committees($votingCampaignID,$revote=TRUE);
			}elseif($status == 'revote'){
				$data['revote']		= true;
				$sql = $this->db->query("SELECT *, items.id AS itemID, POSM_Type.typeName as POSM_TypeName 
										FROM items, POSM_Type
										WHERE items.id IN (SELECT itemID FROM campaignItemsXref WHERE campaignID = $votingCampaignID) 
										AND items.POSMTypeID = POSM_Type.id  ORDER BY itemID DESC");
				$data['items']  = $sql->result_array();
				
				$data['rules'] 			= $this->iLikeCampaign_Items_Checker($votingCampaignID,$revote=TRUE);
				$data['min_committees'] = $this->min_committees($votingCampaignID,$revote=TRUE);
			}
			
			/* */
			$sqlCampRules = "SELECT *  FROM  `iLikeCampaignRules`  $filter_WHERE ";
			$CampRules = $this->db->query($sqlCampRules);
			$CampRules = $CampRules->result_array();
			// iLike Voting Rules Campaign Rules
			$sqlVotinRules   = "SELECT * FROM  `iLikeVotingRules` $filter_WHERE ";
			$VotingRules     = $this->db->query($sqlVotinRules);
			$CampRules       = $VotingRules->result_array();
			$cfilters        = "  $filter_WHERE "; $ifAnd="";
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
			$data['rules'] = $CampRules;
			/* */
			

			$data['id']   	= $votingCampaignID;
			$data['vfile']	= 'campaignAlterFORM.php';
			
			$sql = $this->db->query("SELECT voters.lname  , voters.fname ,voters.gender , voters.email AS email_address, department, year_of_birth  
									FROM campaignVotersXref 
									LEFT JOIN voters  
									ON campaignVotersXref.voterID = voters.id
									WHERE campaignVotersXref.campaignID = $votingCampaignID 
									ORDER BY fname,fname ASC");
			
			$data['admin_users'] = $sql->result_array();	
			
		}
		elseif($action=="update")
		{
			$this->modules->module_checker(27,'EDIT');
            $sql       = $this->db->query("SELECT * FROM campaign WHERE id='$votingCampaignID' ");
		    $sql       = $sql->result_array();
			extract($sql[0]);
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
			redirect('iLikeCampaign/votingCampaign', 'refresh');
		}
		 

		$filter_WHERE = $this->modules->iLike_Campaign_country();
     	$sql = $this->db->query("SELECT *,campaign.id AS iLikeCampaignID FROM $table  
								LEFT JOIN admin_users 
								ON campaign.adminCreatorID = admin_users.id
								WHERE campaignType='iLike' $filter_WHERE ORDER BY iLikeCampaignID  DESC $max");
								
		 
		 $viewer = 'iLikeCampaignHeader.php';
		 
         
		if($this->modules->access_checker()==TRUE)
	    {
		    echo $viewer;
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
	
	/*GENERATE EMAIL TD*/
	function generateEmailTD($fname,$lname,$gender,$email,$department,$year,$emailCtr)
	{
	   $c = (($emailCtr)%2) != 0 ? "" : "style='background:#f9ebeb;'"; 
	   $fname = str_replace('%20',' ',$fname);
	   $lname = str_replace('%20',' ',$lname);
	   echo "<tr id='emailCtr".$emailCtr."' $c> 
				<td> 
				   <input type='hidden' name='voterTypes[]' 	value='2'>
				   <input type='text' value='$fname' 	    	name='fnames[]'       readonly='readonly'  onclick='enable(this)' onBlur='lostFocus(this)'> 
				</td>
				<td><input type='text' value='$lname' 	    	name='lnames[]'       readonly='readonly'  onclick='enable(this)' onBlur='lostFocus(this)'></td>
				<td><input type='text' value='$gender' 	    	name='gender[]'       readonly='readonly'  onclick='enable(this)' onBlur='lostFocus(this)'></td>
				<td><input type='text' value='$department'  	name='departments[]' readonly='readonly'  onclick='enable(this)' onBlur='lostFocus(this)'> </td> 
				<td><input type='text' value='$email'  		    class='emails'   name='emails[]' 	 readonly='readonly'  onclick='enable(this);gotFocusEmail(this)' onBlur='lostFocus(this);checkListEmail(this);'> </td> 
				<td><input type='text' value='$year'  			name='years[]' 		 readonly='readonly'  onclick='enable(this)' onBlur='lostFocus(this)'> </td> 
				<td><label onclick='removeEmail(\"emailCtr".$emailCtr."\")'>Del</label> </td>
		    </tr>";
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
	
	/*iLike Campaign*/
	function publishCampaign($campaignID)
	{
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
		/*SAVE ALL THE NOMINESS*/
			
		/*ITEM RULES*/
		$iLikeCampaignRules = '';
		$sql = "select * from iLikeCampaignRules $filter_WHERE";
		$rules = $this->db->query($sql);  
		$rules = $rules->result_array(); 
		
		foreach($rules as  $r)
	    {
			extract($r); 
			$iLikeCampaignRules .= " items.$fieldName='$fieldID' OR";  
	    }
		$iLikeCampaignRules  = substr($iLikeCampaignRules,0,strlen($iLikeCampaignRules)-2);
		/*ITEM RULES*/
		
		
		/*CHECK IF REVOTE*/
		if(isset($prevCampaignID)){
		 $itemDB = "SELECT  itemID FROM campaignItemsXref 
					WHERE campaignID = $prevCampaignID";
		 
		 $iLike_Rules_Ref 				   = $this->iLikeCampaign_Items_Checker($campaignID,$revote=TRUE);
		 $committee_Fields['num_commitee'] = $this->min_committees($campaignID,$revote=TRUE);
		}else{
		//ITEMS 
		$itemDB = "SELECT  items.id as itemID FROM items 
				   WHERE items.publish='y' $filter_AND 
				   AND($iLikeCampaignRules)";
				   
		$iLike_Rules_Ref 				  = $this->iLikeCampaign_Items_Checker($campaignID);
		$committee_Fields['num_commitee'] = $this->min_committees($campaignID);
		}
		
		/*SAVE ITEMS */
		$sql 	= $this->db->query($itemDB);
		$items 	= $sql->result_array();	
		
		foreach($items as $itm)
		{
			extract($itm);
			$refFields['campaignID'] = $campaignID;
			$refFields['itemID']     = $itemID;
			$res = $this->c3model->c3crud("insert",'campaignItemsXref',$refFields,'');
		}
		/*SAVE ITEMS*/
		
		
		/*iLIKE CAMPAIGN RULES REF*/
		foreach($iLike_Rules_Ref as $iLRR)
		{ extract($iLRR);
		  $ref_Fields['campaignID']  	  = $campaignID;
		  $ref_Fields['table']  	   	  = $table;
		  $ref_Fields['fieldName']  	  = $fieldName;
		  $ref_Fields['fieldID']  	      = $fieldID;
		  $ref_Fields['fieldValue']  	  = $fieldValue;
		  $ref_Fields['rel']  	   		  = $rel;
		  $ref_Fields['val']  	   		  = $val;
		  $ref_Fields['status']  	   	  = $status;
		  $ref_Fields['current_num_items']= $current_num_items;
		  
		  $res = $this->c3model->c3crud("insert","iLike_Rules_Ref",$ref_Fields,'');
		}
		/*iLIKE CAMPAIGN RULES REF*/
		
		$sql 	= $this->db->query("select * from iLikeCanvassingRules $filter_WHERE");
		$iLikeCanvssignRef 	= $sql->result_array();	
		
		/*iLIKE CAMPAIGN Canvassing REF*/
		foreach($iLikeCanvssignRef as $iLRR)
		{ extract($iLRR);
		  $ref_Fields['campaignID']  	  = $campaignID;
		  $ref_Fields['countryID']  	  = $_SESSION['countryID'];
		  $ref_Fields['rel']  	          = $iLRR['rel'];
		  $ref_Fields['lrel']  	          = $iLRR['lrel'];;
		  $ref_Fields['val']  	          = $iLRR['val'];
		  $ref_Fields['dateAdded']  	  = date('Y-m-d');
		  $res = $this->c3model->c3crud("insert","iLikeCanvassingRulesXref",$ref_Fields,'');
		}
		
		/*iLIKE CAMPAIGN Campaign REF*/
		foreach($iLikeVotignRef as $iLRR)
		{ extract($iLRR);
		  $ref_Fields['campaignID']  	  = $campaignID;
		  $ref_Fields['table']  	   	  = $table;
		  $ref_Fields['fieldName']  	  = $fieldName;
		  $ref_Fields['fieldID']  	      = $fieldID;
		  $ref_Fields['fieldValue']  	  = $fieldValue;
		  $ref_Fields['rel']  	   		  = $rel;
		  $ref_Fields['val']  	   		  = $val;
		  $ref_Fields['status']  	   	  = $status;
		  $ref_Fields['current_num_items']= $current_num_items;
		  $res = $this->c3model->c3crud("insert","iLike_Rules_Ref",$ref_Fields,'');
		}
		/*iLIKE CAMPAIGN RULES REF*/
		
		
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
		    'smtp_user' => 'asunciongiancarlo@gmail.com',
		    'smtp_pass' => 'G14nc4rl0',
		    'smtp_timeout' => '4',
		    'mailtype'  => 'html', 
		    'charset'   => 'iso-8859-1'
		); 
 
		$this->email->initialize($config);
		$this->email->set_newline("\r\n");
		/*ADDED AUTHENTICATION FOR EMAIL*/
		
		
		foreach($campaignVotersXref as $cVxref)
		{
			extract($cVxref);
			$query 	= $this->db->query("SELECT * FROM voters WHERE id = $voterID LIMIT 0,1");
			$row 	= $query->row();

			//CREATE MESSAGE
			$msg  = "";
			$msg  = "San Miguel Beer International \n\n"; 
			$msg .= "Hello ". $row->fname ." ". $row->lname."! \n"; 
			$msg .= $c->campaignType ." Campaign w/c entitled ". $c->campaignName ."\nis now online don't forget to vote,\nvoting starts from ". $c->DateFrom ." and end on ". $c->DateTo ."\n\n";
			$msg .= "Link to campaign: ".HTTP_PATH."gallery/voting/". $this->encode_base64($campaignID) ."/". $this->encode_base64($row->email) ;
			
			//SEND EMAIL
			$this->email->clear();
			$this->email->from('do.not.reply@smg.sanmiguel.com.ph', 'San Miguel Beer International');
			$this->email->to($row->email); 

			$this->email->subject('iLike Campaign');
			$this->email->message($msg);	
			
			//echo $this->email->print_debugger();
			
			$this->email->send();
		}
		
		//ON PROGRESS CAMPAIGN
		$dbFields['status'] = 'on progress';
		$dbFields['DatePublished'] = date('Y-m-d');
		$this->c3model->c3crud("update","campaign",$dbFields,$campaignID);
		
		//LOGS
		$CI->rec_logs->w($campaignID,$campaignName,'iLike Campaign','iLike','published');
		
		redirect('iLikeCampaign/votingCampaign', 'refresh');		
		
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
		 $sql = "select * from voters where email='$email' order by dateAdded desc limit 0,1";
	 
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
		//YEAR
		$sql ="SELECT DATEDIFF('$DateFrom',curdate()) AS f";
		$sql   = $this->db->query($sql);
		$d = $sql->row();
		$from = $d->f;
		
		$sql ="SELECT DATEDIFF('$DateTo','$DateFrom') AS t";
		$sql   = $this->db->query($sql);
		$d = $sql->row();
		$to = $d->t;
		
		if($from >= 0 & $to >= 1)
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