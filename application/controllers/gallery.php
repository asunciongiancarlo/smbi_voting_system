<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');  

class Gallery extends CI_Controller {
   public function __construct()
    {
		parent::__construct();
		//date_default_timezone_set('Asia/Manila');
		date_default_timezone_set('UTC');
		session_start();
		$this->load->model('c3model');
		$this->load->library('security');
		$this->load->library('modules');
		$this->load->helper('url');
		$this->output->enable_profiler(FALSE);
		error_reporting(0); 
    }
  
	function vote($cam_id,$email,$pid,$vote='',$kind='')
	{
		$id      =  $this->decode_base64($cam_id);
		$email   =  $this->decode_base64($email);
		if(isset($_SESSION['itemReview'])==false)$_SESSION['itemReview'] = False; 
		$v       =  $this->db->query("SELECT id FROM voters where email='$email'  and campaignID='$id' limit 0,1");
		$v       =  $v->result_array();  		
		$dbFields['campaignID']=$id ;
		$dbFields['voterID']=$v[0]['id'];
		$dbFields['itemID']=$pid;
		$dbFields['vote']=$vote;
		$dbFields['ttime']=date('H:i:s');
		$dbFields['tdate']=date('Y-m-d');
		$sql       = "delete from votexRef where itemID='$pid' and campaignID='$id' and voterID='".$v[0]['id']."'  ";
	    $res       = $this->c3model->c3crud("no-res",'','','',$sql); 
	    if($pid!='-1')  $res       = $this->c3model->c3crud("insert",'votexRef',$dbFields,'');
	
        $sql ="select  i.id,i.itemName,vref.*,(select typeName from POSM_Type as pt where pt.id = i.POSMTypeID) as  posm_status from votexRef as vref left join items as i on i.id =vref.itemID  
					   where vref.campaignID='$id' and voterID='".$v[0]['id']."' and vref.vote='yes'    order by posm_status";
		$voteItems = $this->db->query($sql);		
        $voteItems = $voteItems->result_array();
		echo count($voteItems);		
	}
	
	function getSummary($cam_id,$email)
	{
		$id      =  $this->decode_base64($cam_id);
	 	$email   =  $this->decode_base64($email);
		if(isset($_SESSION['itemReview'])==false)$_SESSION['itemReview'] = False; 
		$v       =  $this->db->query("SELECT id FROM voters where email='$email'  and campaignID='$id' limit 0,1");
		$v       =  $v->result_array();  		
		$dbFields['campaignID']=$id ;
		$dbFields['voterID']=$v[0]['id'];
		$dbFields['itemID']=$pid;
		$dbFields['vote']=$vote;
		$dbFields['ttime']=date('H:i:s');
		$dbFields['tdate']=date('Y-m-d');
	
	
        $sql ="select  i.id,i.itemName,vref.*, extra_label, (select typeName from POSM_Type as pt where pt.id = i.POSMTypeID) as  posm_status from votexRef as vref 
			   left join items as i on i.id = vref.itemID  
			   left join price_range on i.price_rangeID = price_range.id
			   where vref.campaignID='$id' and voterID='".$v[0]['id']."' and vref.vote='yes'    
			   order by posm_status, price_range.id ASC";
		$voteItems = $this->db->query($sql);		
        $voteItems = $voteItems->result_array();
		echo "<h4>$campaign Items (<a href='#' style='color:black;' onclick=\"javascript:jQuery('#likeITEMS').hide('slide',{direction:'right'});\">close</a>)</h4> ";
		
		$lastType		 ="";
		$price_rangeName ="";
		$x=1;
		foreach($voteItems as $v)
	    {
			$id = $v['id'];
			if($lastType!=$v['posm_status']){
			  echo "<h5>".$v['posm_status']."</h5>";
			  $x=1;
			}
			if($price_rangeName!=$v['extra_label'])
			{
			echo "<p style='background:#723f3f;padding: 3px 10px;font-size: 13px;color: white;margin-bottom: -2px;text-align: left;'>".$v['extra_label']."</p>";
			$x=1;
			}
			
			echo "<div style='padding:4px'> <b>".$x++ .".</b> ".$v['itemName']. "</div>";
			$lastType=$v['posm_status'];  
			$price_rangeName = $v['extra_label'];
	    }
		   
		$votes = array();
		if(isset($_SESSION["$cam_id$email"])==true) 
		   $votes = $_SESSION["$cam_id"."$email"];
	
		$test= array_search($pid, $votes);
		$new = true;
		foreach($votes as $k=>$v)
		  {
		    if($v['pid']==$pid)
			 {
			 $votes[$k] = array('pid'=>$pid,'vote'=>$vote); 
			 $new = false;
			 }
			
		  }
		if($new) $votes[] = array('pid'=>$pid,'vote'=>$vote); 
		$_SESSION["$cam_id$email"]=$votes;	
        $campaign = $this->db->query("SELECT campaignType FROM campaign WHERE id=$id");
		$campaign = $campaign->row();
	    $campaign = ($campaign->campaignType=="iLike") ? "Liked" : "Want";
		
				
	}

	function rank($cam_id,$email,$pid,$vote)
	{
	    $id    =  $this->decode_base64($cam_id);
		$email =  $this->decode_base64($email);

		$v =  $this->db->query("SELECT id FROM voters where campaignID='$id' and email='$email' limit 0,1");
	    $v =  $v->result_array();  		

		$dbFields['campaignID']=$id ;
		$dbFields['voterID']=$v[0]['id'];
		$dbFields['itemID']=$pid;
		$dbFields['vote']=$vote;
		$dbFields['ttime']=date('H:i:s');
		$dbFields['tdate']=date('Y-m-d');
		
		$sql       = "delete from votexRef where itemID='$pid' and campaignID='$id' and voterID='".$v[0]['id']."'  ";
	    $res       = $this->c3model->c3crud("no-res",'','','',$sql); 
	    $res       = $this->c3model->c3crud("insert",'votexRef',$dbFields,'');
        $voteItems = $this->db->query("select  i.itemName,vref.*,(select typeName from POSM_Type as pt where pt.id = i.POSMTypeID) as  posm_status from votexRef as vref left join items as i on i.id =vref.itemID  where vref.campaignID='$id'  and  voterID='".$v[0]['id']."'  order by posm_status,vref.vote desc");		
        $voteItems = $voteItems->result_array();
		
		$lastType="";
 		foreach($voteItems as $v)
		  {
  		     //var_dump($v);  
			if($lastType!=$v['posm_status'])
			 {echo "<h5>".$v['posm_status']."</h5>";}
			
			echo "<div style='padding:4px'>(<b style='color:Red'>". $v['vote']."</b>)".$v['itemName']."</div>";
			 
			$lastType=$v['posm_status'];  
		  }
	  }
	  
    function done()
	{
		$data['vfile']		= 'done.php';
	    $data['title']		= 'SMBi';
	    $data['page_title']	= 'Voting';
		$this->load->view('gallery',$data);  
	}
	
    function ended()
	{
		$data['vfile']		= 'ended.php';
	    $data['title']		= 'SMBi';
	    $data['page_title']	= 'Voting';
		$this->load->view('gallery',$data);  
	  }
 
	function thanks()
	{
		$data['vfile']		= 'thanks.php';
	    $data['title']		= 'SMBi';
	    $data['page_title']	= 'Voting';
	    
		$this->load->view('gallery',$data);  
	  }
	
	function review($id,$email)
	{
		$tdate 			 =  date('Y-m-d');
	    $data['cam_id']      =  $id;
	    $data['CID']      	 =  0;
	    $data['VID']      	 =  0;
		$data['encemail']    =  $email;
	    $data['email']   	 =  $email;
		$_SESSION['itemReview'.$email] = true;
		$camid    =   $this->decode_base64($id);;
		$email    =  $this->decode_base64($email);
  
		
		 $sqlVoter  = "select * from voters where campaignID='$camid' and email='$email'"; 		
	     $voterINFO = $this->db->query($sqlVoter);
		 $data['voterINFO']   =  $voterINFO->result_array(); 
		//VOTER INFO
		 $data['VOTER_ID']    = $data['voterINFO'][0]['id'];
		 $data['CAMPAIGN_ID'] = $camid;
		
		$sql ="select * from campaign where   id='$camid' and (DateFrom <= '$tdate' and DateTo >='$tdate') and status='on progress'";
	 	$campaign = $this->db->query($sql);
	 
	    // if ended
        if($campaign->num_rows() == 0) header("location:".HTTP_PATH.'gallery/ended.html');
	
	    $votersSQL1=" SELECT * FROM  `voters` where email='$email'  and  campaignID='$camid' AND votingStatus='done'";  
		$v1 = $this->db->query($votersSQL1);
	 	$v1 = $v1->result_array();
		if(isset($v1[0]))
        {
		 if($v1[0]['votingStatus']=='done'  and $v1[0]['email']=="$email" and  $v1[0]['campaignID']==$camid) {header("location:".HTTP_PATH.'gallery/done.html');}
	    }	
		
		$votersSQL1=" SELECT * FROM  `voters` where email='$email'  and  campaignID='$camid'";
	      
		$v1 = $this->db->query($votersSQL1);
	 	$v1 = $v1->result_array();
	
		$camp = "SELECT * FROM campaign where id='$camid'  ";
		$camp     = $this->db->query($camp );
		$camp      = $camp->result_array();
		$cID = $camp[0]['countryID'];
		
		$sqlVotinRules = "SELECT * FROM  `iLikeVotingRulesRef` where campaignID='$camid'  order by fieldID DESC, rel DESC, val ASC";
		$VotingRules     = $this->db->query($sqlVotinRules);
		$VotingRules     = $VotingRules->result_array();
		$fieldList="";   
		foreach($VotingRules as $k=>$r)
		   {
			 extract($r);
			 if(strpos($fieldList,$fieldName) === false)
			    $fieldList .= "$fieldName,"	;			
		   }
		  $fieldList = substr($fieldList,0,strlen($fieldList)-1);
		 $sql  ="  SELECT $fieldList , COUNT( * ) as tot FROM  `votexRef` AS vref LEFT JOIN items AS i ON vref.itemID = i.id
					LEFT JOIN voters AS v ON vref.voterId = v.id WHERE vref.campaignID =  '$camid' AND v.email =  '$email' and vref.vote='yes'
					GROUP BY $fieldList  order by $fieldList asc"; 

        $ActualVote     =  $this->db->query($sql);
	    $ActualVote     =  $ActualVote->result_array();					
        $xNumTrue=0;

		$lastItem="";
		$Rules="<h4>Voting Rules: <span style='font-size:12px;'>You must vote</span></h4>"; $errorMSG="<h4>Actual Vote</h4>";
		foreach($VotingRules as $k=>$r)
		   {
			 extract($r);
			 $acVote = $ActualVote[$k];
			  
			 $condition  = $this->myEval($acVote['tot'], $rel ,$val);
			$fname = $this->fieldValue($fieldName,$fieldID);
			
			
			if($lastItem!=$fname){
				$Rules .=" $fname $rel $val ";
				if(count($VotingRules)==2) $Rules .="|";
			}else{
				$Rules .=" and $rel $val <br/> ";
			}
		    //if($acVote[$fieldName]==$fieldID and  $condition)  
			 if($this->voteCtr($camid,$email,$fieldName,$fieldID,$rel,$val)=="Good")
			    $xNumTrue++; 
		     
			 if($acVote['tot']=='') $acVote['tot'] = 0; 
				if($lastItem!=$fname){
					$errorMSG .="$fname : " .  $this->actual_vote($camid,$email,$fieldName,$fieldID)  . "<br/>"; 
			    }
			   $lastItem=$fname;
		   }

		   if(count($VotingRules)==2){
			$Rules = substr($Rules, 0, -1);
		   }
		   
		   if(count($VotingRules) == $xNumTrue)
		      {
		      $data['reviewStatus']= "ok";
              }				
		  else
             {
			 $data['reviewStatus']= "error";		     
			 $data['votingError'] ="$errorMSG ";
			 $data['votingRules'] ="$Rules";
			 }
			 
	  
	    $tdate 				 =  date('Y-m-d');
	    $data['vfile']		 =  'voting.php';
	    $data['title']		 =  'SMBi';
	    $data['page_title']	 =  'Voting';
		
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
	  
	   
		$v       =  $this->db->query("SELECT id FROM voters where email='$email'  and campaignID='$camid' limit 0,1");
		$v       =  $v->result_array();   
		        
		$sql ="select  i.id,i.itemName,vref.*,(select typeName from POSM_Type as pt where pt.id = i.POSMTypeID) as  POSM_TypeName ,  
                	   (select image from items_images as img where img.itemID=i.id AND defaultStatus = 1  limit 0,1) as itemImg,
					   ( select label from iLikeVotingRulesRef where price_rangeID=i.price_rangeID and campaignID=$camid) as label
					   from votexRef as vref left join items as i on i.id =vref.itemID
					    
					   where vref.campaignID='$camid' and voterID='".$v1[0]['id']."' and vref.vote='yes'    order by POSM_TypeName,i.price_rangeID asc";
		
		 $items   =  $this->db->query($sql); 
		 $items   =  $items->result_array(); 
         $sqlVoter  = "select * from voters where campaignID='$camid' and email='$email'"; 		
	     $voterINFO = $this->db->query($sqlVoter);
		 $data['voterINFO']   =  $voterINFO->result_array();

         $posmTypeSQL  = "  select  DISTINCT POSM_Type.typeName as POSM_TypeName,POSMTypeID  
							from items as i INNER JOIN campaignItemsXref as ref on i.id = ref.itemID
							LEFT JOIN POSM_Type   ON i.POSMTypeID = POSM_Type.id 
							where ref.campaignID='$camid' ORDER BY POSM_Type.typeName asc, i.itemName asc ";
		 $posmType     =  $this->db->query($posmTypeSQL); 
		 $posmType     =  $posmType->result_array(); 
    
         $curPOSM=$posmType[$curIndexPOSM]['POSMTypeID'];
	     $curPOSMName=$posmType[$curIndexPOSM]['POSM_TypeName'];
		    		 
		 
	     if(isset($_SESSION[$data['cam_id']."$email"])==true)      
	       $data['currentVote']   =  $_SESSION[$data['cam_id']."$email"];
		 else 
		    $data['currentVote']   = ''; 
	
		
		$data['min_number_of_votes']     =  0;
		$data['min_number_of_votes_MSG'] =  "";
		
		$data['max_number_of_votes']     =  0;
		$data['max_number_of_votes_MSG'] =  "";
		
	    $data['posmTypeList']  = $posmType  ;
		$data['curPOSM']       = $curPOSM ;
		$data['curPOSMName']   = $curPOSMName ;
		$data['curIndexPOSM']  = count($posmType);
		$data['limit']         = $limit ;
	    $data['galTitle']      = "iLike Campaign"   ;
	    $data['items']         =  $items;
	    $data['total']         =  $itemTot[0]['tot'];  
	    $data['page']          =  $page;  
	    $data['galType']       =  $items; 
		$data['review']        =  true;
		$data['curLevelID']    =  0; 
		$data['csrf']          =  "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>"; 
	    $this->load->view('iLikeGallery',$data);  
	}
	
	function voteCtr($camid,$email,$type,$id,$rel,$val)
	{
		$sql  ="SELECT  COUNT( * ) as tot FROM  `votexRef` AS vref LEFT JOIN items AS i ON vref.itemID = i.id
				LEFT JOIN voters AS v ON vref.voterId = v.id WHERE vref.campaignID =  '$camid' AND v.email =  '$email' and vref.vote='yes' 
				AND i.$type = $id"; 

        $ActualVote     =  $this->db->query($sql);
	    $ActualVote     =  $ActualVote->row();
		$votes = $ActualVote->tot;
		
		$status="";
		switch($rel){
			case(">"):
				if($votes > $val)
					 $status = "Good";
				else
					 $status = "Not Good";
			break;
			case("<"):
				if($votes < $val)
					 $status = "Good";
				else
					 $status = "Not Good";
			break;
			case(">="):
				if($votes >= $val)
					 $status = "Good";
				else
					 $status = "Not Good";
			break;
			case("<="):
				if($votes <= $val)
					 $status = "Good";
				else
					 $status = "Not Good";
			break;
			case("=="):
				if($votes == $val)
					 $status = "Good";
				else
					 $status = "Not Good";
			break;
		}
		
		return $status;
	}
	
	function iWantreview($id,$email)
	{
		$tdate 			 =  date('Y-m-d');
	    $data['cam_id']      =  $id;
		$data['CID']      	 =  0;
	    $data['VID']      	 =  0;
		$data['encemail']    =  $email;
	    $data['email']   	 =  $email;
		$_SESSION['itemReview'.$email] = true;
		$camid    =   $this->decode_base64($id);;
		$email    =  $this->decode_base64($email);
        error_reporting(0);
		
		 $sqlVoter  = "select * from voters where campaignID='$camid' and email='$email'"; 		
	     $voterINFO = $this->db->query($sqlVoter);
		 $data['voterINFO']   =  $voterINFO->result_array(); 
		//VOTER INFO
		 $data['VOTER_ID']    = $data['voterINFO'][0]['id'];
		 $data['CAMPAIGN_ID'] = $camid;
		
		$sql ="select * from campaign where   id='$camid' and (DateFrom <= '$tdate' and DateTo >='$tdate') and status='on progress'";
	 	$campaign = $this->db->query($sql);
	 
	    // if ended
        if($campaign->num_rows() == 0) header("location:".HTTP_PATH.'gallery/ended.html');
	   
		$votersSQL1=" SELECT * FROM  `voters` where email='$email'  and  campaignID='$camid' AND votingStatus='done'";
	      
		$v1 = $this->db->query($votersSQL1);
	 	$v1 = $v1->result_array();
		if(isset($v1[0]))
        {		
			if($v1[0]['votingStatus']=='done'  and $v1[0]['email']=="$email" and  $v1[0]['campaignID']==$camid) {header("location:".HTTP_PATH.'gallery/done.html');}
	    }	
		
		$votersSQL1=" SELECT * FROM  `voters` where email='$email'  and  campaignID='$camid'";
		$v1 = $this->db->query($votersSQL1);
	 	$v1 = $v1->result_array();
		
		$camp = "SELECT * FROM campaign where id='$camid'  ";
		$camp     = $this->db->query($camp );
		$camp      = $camp->result_array();
		$cID = $camp[0]['countryID'];
		
		$sqlVotinRules = "SELECT * FROM  `iWantVotingRulesRef` where campaignID='$camid' order by fieldID DESC, rel DESC";
		$VotingRules     = $this->db->query($sqlVotinRules);
		$VotingRules     = $VotingRules->result_array();
		$fieldList="";   
		foreach($VotingRules as $k=>$r)
		   {
			 extract($r);
			 if(strpos($fieldList,$fieldName) === false)
			    $fieldList .= "$fieldName,"	;			
		   }
		  $fieldList = substr($fieldList,0,strlen($fieldList)-1);
		  $sql  ="  SELECT $fieldList , COUNT( * ) as tot FROM  `votexRef` AS vref LEFT JOIN items AS i ON vref.itemID = i.id
					LEFT JOIN voters AS v ON vref.voterId = v.id WHERE vref.campaignID =  '$camid' AND v.email =  '$email' and vref.vote='yes'
					GROUP BY $fieldList  order by $fieldList asc"; 

        $ActualVote     =  $this->db->query($sql);
	    $ActualVote     =  $ActualVote->result_array();					
        $xNumTrue=0;
		
		$lastItem="";
		$Rules="<h4>Voting Rules: <span style='font-size:12px;'>You must vote</span></h4>"; $errorMSG="<h4>Actual Vote</h4>";
		foreach($VotingRules as $k=>$r)
		{
			 extract($r);
			 $acVote = $ActualVote[$k];
			  
			 $condition  = $this->myEval($acVote['tot'], $rel ,$val);
			$fname = $this->fieldValue($fieldName,$fieldID);
			
			
			if($lastItem!=$fname){
				$Rules .=" $fname $rel $val ";
				if(count($VotingRules)==2) $Rules .="|";
			}else{
				$Rules .=" and $rel $val <br/> ";
			}
		    //if($acVote[$fieldName]==$fieldID and  $condition)  
			 if($this->voteCtr($camid,$email,$fieldName,$fieldID,$rel,$val)=="Good")
			    $xNumTrue++; 
		     
			 if($acVote['tot']=='') $acVote['tot'] = 0; 
				if($lastItem!=$fname){
					$errorMSG .="$fname : " .  $this->actual_vote($camid,$email,$fieldName,$fieldID)  . "<br/>"; 
			    }
			   $lastItem=$fname;
		}

	    if(count($VotingRules)==2){
		 $Rules = substr($Rules, 0, -1);
	    }

		   if(count($VotingRules) == $xNumTrue)
		      {
		      $data['reviewStatus']= "ok";
              }				
		  else
             {
			 $data['reviewStatus']= "error";		     
			 $data['votingError'] ="$errorMSG ";
			 $data['votingRules'] ="$Rules";
			 }
			 
		/*            */	  
	    $tdate 				 =  date('Y-m-d');
		
	    $data['vfile']		 =  'votingIwant.php';
	    $data['title']		 =  'SMBi';
	    $data['page_title']	 =  'Voting';
		
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
	    	
	    
       
		$v       =  $this->db->query("SELECT id FROM voters where email='$email'  and campaignID='$camid' limit 0,1");
		$v       =  $v->result_array();   
		        
		 
		         
		$sql ="select  i.id,i.itemName,vref.*,(select typeName from POSM_Type as pt where pt.id = i.POSMTypeID) as  POSM_TypeName ,  
                	   (select image from items_images as img where img.itemID=i.id AND defaultStatus = 1  limit 0,1) as itemImg,
					   ( select label from iWantVotingRulesRef where price_rangeID=i.price_rangeID and campaignID=$camid) as label
					   from votexRef as vref left join items as i on i.id =vref.itemID
					    
					   where vref.campaignID='$camid' and voterID='".$v1[0]['id']."' and vref.vote='yes'    order by POSM_TypeName,i.price_rangeID asc";
		
		 $items   =  $this->db->query($sql); 
		 $items   =  $items->result_array(); 
		 
         $sqlVoter  = "select * from voters where campaignID='$camid' and email='$email'"; 		
	     $voterINFO = $this->db->query($sqlVoter);
		 $data['voterINFO']   =  $voterINFO->result_array();

         $posmTypeSQL  = "  select  DISTINCT POSM_Type.typeName as POSM_TypeName,POSMTypeID  
							from items as i INNER JOIN campaignItemsXref as ref on i.id = ref.itemID
							LEFT JOIN POSM_Type   ON i.POSMTypeID = POSM_Type.id 
							where ref.campaignID='$camid' ORDER BY POSM_Type.typeName asc, i.itemName asc ";
		 $posmType     =  $this->db->query($posmTypeSQL); 
		 $posmType     =  $posmType->result_array(); 
    
         $curPOSM=$posmType[$curIndexPOSM]['POSMTypeID'];
	     $curPOSMName=$posmType[$curIndexPOSM]['POSM_TypeName'];
		    		 
		 
	     if(isset($_SESSION[$data['cam_id']."$email"])==true)      
	       $data['currentVote']   =  $_SESSION[$data['cam_id']."$email"];
		 else 
		    $data['currentVote']   = ''; 
		
		$data['min_number_of_votes']     =  0;
		$data['min_number_of_votes_MSG'] =  "";
		
		$data['max_number_of_votes']     =  0;
		$data['max_number_of_votes_MSG'] =  "";
	 
	    $data['posmTypeList']  = $posmType  ;
		$data['curPOSM']       = $curPOSM ;
		$data['curPOSMName']   = $curPOSMName ;
		$data['curIndexPOSM']  = count($posmType);
		$data['limit']         = $limit ;
	    $data['galTitle']      = "iWant Campaign"   ;
	    $data['items']         =  $items;
	    $data['total']         =  $itemTot[0]['tot'];  
	    $data['page']          =  $page;  
	    $data['galType']       =  $items; 
		$data['review']        =  true;
		$data['curLevelID']    =  0; 
		$data['csrf']          =  "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>"; 
	    $this->load->view('iWantGallery',$data);  
	  }
    
	function finish($id,$email)
	{
	    $camid    =   $this->decode_base64($id);;
		$email    =  $this->decode_base64($email);
        error_reporting(0);
		$_SESSION['itemReview'] = true;
		 
		
		$camp = "SELECT * FROM campaign where id='$camid'  ";
		$camp     = $this->db->query($camp );
		$camp      = $camp->result_array();
		$cID = $camp[0]['countryID'];
	 

	   
			    $v =  $this->db->query("SELECT id FROM voters where campaignID='$camid' and email='$email' limit 0,1");
				$v =  $v->result_array();  
		
				$this->db->where('id',$v[0]['id']);  		
				$this->db->update('voters',Array('votingStatus'=>'done'));  		

				echo "oks";
          

	  }
    
    function finishIwant($id,$email) 
	{
	    $camid    =   $this->decode_base64($id);;
		$email    =  $this->decode_base64($email);
        error_reporting(0);
		$_SESSION['itemReview'] = true;
		
		$camp = "SELECT * FROM campaign where id='$camid'";
		$camp     = $this->db->query($camp );
		$camp      = $camp->result_array();
		$cID = $camp[0]['countryID'];
		
	 
	 
	     $v =  $this->db->query("SELECT id FROM voters where campaignID='$camid' and email='$email' limit 0,1");
		 $v =  $v->result_array();  
				
		 $this->db->where('id',$v[0]['id']);  		
		 $this->db->update('voters',Array('votingStatus'=>'done'));  		
         echo "oks";
        
	  }
      
    function actual_vote($camid,$email,$type,$id)
	{
		$sql  ="SELECT  COUNT( * ) as tot FROM  `votexRef` AS vref LEFT JOIN items AS i ON vref.itemID = i.id
				LEFT JOIN voters AS v ON vref.voterId = v.id WHERE vref.campaignID =  '$camid' AND v.email =  '$email' and vref.vote='yes' 
				AND i.$type = $id"; 

        $ActualVote     =  $this->db->query($sql);
	    $ActualVote     =  $ActualVote->row();
		return $ActualVote->tot;
	}
	 
	function getCurVote($curLevelID,$CAMPAIGN_ID,$VOTER_ID)
	  {
	    $sql = $this->db->query("SELECT count(votexRef.id) as totalVote FROM votexRef
                                 left join items on votexRef.itemID = items.id
								 WHERE price_rangeID=$curLevelID and campaignID=$CAMPAIGN_ID AND voterID=$VOTER_ID AND  vote='yes'");
		$curVotes = $sql->result_array(); 
		  
	   echo $curVotes[0]['totalVote']; ;
	  }
	
	function voting($id,$email,$page=1,$curIndexPOSM=0,$curLevelID=0,$review='')
	{
	    $tdate 				 =  date('Y-m-d');
		$data['encemail']    =  $email;
		$data['cam_id']      =  $id;
	    $data['email']   	 =  $email;
		$id    				 =  $this->decode_base64($id);
	    $CAMPAIGN_ID		 =  $id;
		$email 				 =  $this->decode_base64($email);
	    $data['vfile']		 =  'voting.php';
	    $data['title']		 =  'SMBi';
	    $data['page_title']	 =  'Voting';
		$limit  			 =  ( $page  * 20 - 20) < 0 ? "0":( $page  * 20 - 20);
		
		
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
	    $votersSQL1=" SELECT * FROM  `voters` where email='$email'  and  campaignID='$id' and votingStatus='done'";
	      
		$v1 = $this->db->query($votersSQL1);
	 	$v1 = $v1->result_array();
		
		// if done 
		if(isset($v1[0]))
        {		
		 if($v1[0]['votingStatus']=='done'  and $v1[0]['email']=="$email" and  $v1[0]['campaignID']==$id) {header("location:".HTTP_PATH.'gallery/done.html');}
	    }		
		
		$sql ="select * from campaign where   id='$id' and (DateFrom <= '$tdate' and DateTo >='$tdate') and status='on progress'";
	 	$campaign = $this->db->query($sql);
		 
	 
	    // if ended
        if($campaign->num_rows() == 0) header("location:".HTTP_PATH.'gallery/ended.html');
	   			
	    $posmTypeSQL    = "  select  DISTINCT POSM_Type.typeName as POSM_TypeName,POSMTypeID  
							from items as i INNER JOIN campaignItemsXref as ref on i.id = ref.itemID
							LEFT JOIN POSM_Type   ON i.POSMTypeID = POSM_Type.id 
							where ref.campaignID='$id' ORDER BY POSM_Type.typeName asc, i.itemName asc ";
		$posmType       =  $this->db->query($posmTypeSQL); 
		$posmType       =  $posmType->result_array(); 
    
        $curPOSM        =  $posmType[$curIndexPOSM]['POSMTypeID'];
	    $curPOSMName    =  $posmType[$curIndexPOSM]['POSM_TypeName'];
		
		$sqlLevel       = "select * from price_range where POSMTypeID='$curPOSM' order by xOrder asc";
		$priceRange     =  $this->db->query($sqlLevel);
		$priceRange     =  $priceRange->result_array(); 
		$PRID           =  $curLevelID==0 ? $priceRange[0]['id']: $curLevelID;
	    $curLevelID     =  $PRID ;
		
		//REVIEW URL
		$data['review_url_no_extension'] 	 = $this->review_url2($_SERVER['HTTP_REFERER']);
		$data['review_url'] 	     		 = $this->review_url('url',HTTP_PATH."".substr($_SERVER['REDIRECT_QUERY_STRING'],1),"show_liked_items");
		$data['review_url_stat']     		 = $this->review_url('stat',HTTP_PATH."".substr($_SERVER['REDIRECT_QUERY_STRING'],1),"show_liked_items");
		
		//REVIEW ALL LIKE
		if($page=='show_liked_items' OR $review=='show_liked_items' OR $curLevelID=='show_liked_items')
		{
		$sql1 = $this->db->query("SELECT voters.id as voterID FROM voters WHERE email='$email' AND campaignID='$CAMPAIGN_ID' LIMIT 0,1");
		$sql1 = $sql1->row();
		
		$sql  = "select *,(select image from items_images as img where img.itemID=i.id AND defaultStatus = 1  limit 0,1) as itemImg,
				POSM_Type.typeName as POSM_TypeName
				from items as i INNER JOIN campaignItemsXref as ref on i.id = ref.itemID
				inner join price_range as pr on i.price_rangeID = pr.id
				inner join votexRef  as vRef ON vRef.itemID = i.id
				LEFT JOIN POSM_Type   ON i.POSMTypeID = POSM_Type.id 
				where pr.id =$PRID and ref.campaignID='$id' and i.POSMTypeID='$curPOSM'  AND (voterID =". $sql1->voterID ."  AND vote='yes') ";
		}
		else
		{
        $sql  = "select *,(select image from items_images as img where img.itemID=i.id AND defaultStatus = 1  limit 0,1) as itemImg,
				POSM_Type.typeName as POSM_TypeName
				from items as i INNER JOIN campaignItemsXref as ref on i.id = ref.itemID
				inner join price_range as pr on i.price_rangeID = pr.id
				LEFT JOIN POSM_Type   ON i.POSMTypeID = POSM_Type.id 
				where pr.id =$PRID and ref.campaignID='$id' and i.POSMTypeID='$curPOSM'     ORDER BY RAND() ";
		}
		
        $sqlTOT  = "select count(*) tot from items as i INNER JOIN campaignItemsXref as ref on i.id = ref.itemID
				inner join price_range as pr on i.price_rangeID = pr.id
				LEFT JOIN POSM_Type   ON i.POSMTypeID = POSM_Type.id 
				where pr.id =$PRID and ref.campaignID='$id' and i.POSMTypeID='$curPOSM'     ";
 
		
		 $items        =  $this->db->query($sql); 
		 $items        =  $items->result_array(); 
		 
         $itemTot =  $this->db->query($sqlTOT);
	     $itemTot =  $itemTot->result_array(); 
         $sqlVoter  = "select * from voters where campaignID='$id' and email='$email'"; 		
	     $voterINFO = $this->db->query($sqlVoter);
		 $data['voterINFO']   =  $voterINFO->result_array(); 
		 
	     if(isset($_SESSION[$data['cam_id']."$email"])==true)
           {		 
	       $data['currentVote']   =  $_SESSION[$data['cam_id']."$email"];
		    if(count($_SESSION[$data['cam_id']."$email"]) <= 1){
		    $boto="";
			$sql = "SELECT itemID, vote FROM votexRef WHERE campaignID = $id AND voterID = ".$data['voterINFO'][0]['id'];
			$sql = $this->db->query($sql);
			$votes = $sql->result_array();
			
			foreach($votes as $v){
				$boto[] = array('pid'=>$v['itemID'],'vote'=>$v['vote']);
			}
			$data['currentVote'] = $boto;
		 }
		   }
		 else 
		    $data['currentVote']   = ''; 
		
		 
		 //VOTER INFO
		 $data['VOTER_ID']    =  $VOTER_ID   = $data['voterINFO'][0]['id'];
		 $data['CAMPAIGN_ID'] = $CAMPAIGN_ID;
		 
		 
		
		
		$data['campaign']    =  $campaign->result_array(); 
		//getCurrentVote;
		  
		$sql = $this->db->query("SELECT count(votexRef.id) as totalVote FROM votexRef
                                 left join items on votexRef.itemID = items.id
								 WHERE price_rangeID=$curLevelID and campaignID=$CAMPAIGN_ID AND voterID=$VOTER_ID AND  vote='yes'");
		$curVotes = $sql->result_array(); 
		  
	    $data['curVotes']      = $curVotes[0]['totalVote']; ;
	    $data['CID']           = $CAMPAIGN_ID;
	    $data['VID']           = $VOTER_ID ;
	    $data['posmTypeList']  = $posmType  ;
		$data['curPOSM']       = $curPOSM ;
		$data['curPOSMName']   = $curPOSMName ;
		$data['curIndexPOSM']  = $curIndexPOSM ;
		$data['limit']         = $limit ;
		$data['priceRange']    = $priceRange ;
		$data['totalLevel']    = count($priceRange) ;
	    $data['galTitle']      = "iLike Campaign"   ;
	    $data['items']         =  $items;
	    $data['total']         =  $itemTot[0]['tot'];  
	    $data['page']          =  $page;  
	    $data['galType']       =  $items;  
	    $data['curLevelID']    =  $curLevelID ;  
	    $data['curLevelCondition']=  $this->getCurVoteRules($curLevelID,$curPOSM,$CAMPAIGN_ID) ;
		
		//MINIMUM NUMBER OF VOTES
		$data['min_number_of_votes']     =  $this->min_number_of_votes('MIN_VAL',$curLevelID,$curPOSM,$CAMPAIGN_ID);
		$data['min_number_of_votes_MSG'] =  $this->min_number_of_votes('MIN_VAL_MSG',$curLevelID,$curPOSM,$CAMPAIGN_ID);
		
		$data['max_number_of_votes']     =  $this->min_number_of_votes('MAX_VAL',$curLevelID,$curPOSM,$CAMPAIGN_ID);
		$data['max_number_of_votes_MSG'] =  $this->min_number_of_votes('MAX_VAL_MSG',$curLevelID,$curPOSM,$CAMPAIGN_ID);
		
	    $data['totalLevel']    = count($priceRange) ;		
		$data['csrf']          =  "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>"; 
	    $this->load->view('iLikeGallery',$data);  
	}
	
	function review_url($type='',$referer='',$initail_val='')
	{
	 //echo $_SERVER['HTTP_REFERER'];
	 $ref = $referer;
	 $ref = explode("/",$ref);
	 $new_Str=""; 	 
	 //IF NOT show_liked_items
	 if(end($ref)=="show_liked_items")
	 {
	  foreach($ref as $s)
	  if($s!=end($ref)) $new_Str.="$s/";
	  
	  if($type=='url')  return $new_Str."show_liked_items";
	  if($type=='stat') return "reload";
	 }else{
	  if($type=='url')  return $referer."/".$initail_val;
	  if($type=='stat') return "no_reload";
	 }
	}
	
	function review_url_iwant($type='',$referer='',$initail_val='')
	{
	 //echo $_SERVER['HTTP_REFERER'];
	 $ref = $referer;
	 $ref = explode("/",$ref);
	 $new_Str=""; 	 
	 //IF NOT show_liked_items
	 if(end($ref)=="show_wanted_items")
	 {
	  foreach($ref as $s)
	  if($s!=end($ref)) $new_Str.="$s/";
	  
	  if($type=='url')  return $new_Str."show_wanted_items";
	  if($type=='stat') return "reload";
	 }else{
	  if($type=='url')  return $referer."/".$initail_val;
	  if($type=='stat') return "no_reload";
	 }
	}
	
	function review_url2($referer='')
	{
	 $ref = $referer;
	 $ref = explode("/",$ref);
	 $new_Str=""; 	 
	 //IF NOT show_liked_items
	 if(end($ref)=="show_liked_items")
	 {
	  foreach($ref as $s)
	  if($s!=end($ref)) $new_Str.="$s/";
	  
	  return $new_Str;
	 }
	 else
	 {
	  return $referer;
	 }
	}
	
	function review_url2_iwant($referer='')
	{
	 $ref = $referer;
	 $ref = explode("/",$ref);
	 $new_Str=""; 	 
	 //IF NOT show_liked_items
	 if(end($ref)=="show_wanted_items")
	 {
	  foreach($ref as $s)
	  if($s!=end($ref)) $new_Str.="$s/";
	  
	  return $new_Str;
	 }
	 else
	 {
	  return $referer;
	 }
	}

	private function min_number_of_votes($typeVal,$curLevelID,$curPOSM,$CAMPAIGN_ID) 
	{
	    $iLikeVotingRulesRef  	  = "select * from iLikeVotingRulesRef where campaignID=$CAMPAIGN_ID and price_rangeID=$curLevelID and fieldID=$curPOSM ";
        $iLikeVotingRulesRef     =  $this->db->query($iLikeVotingRulesRef);
		$iLikeVotingRulesRef     =  $iLikeVotingRulesRef->result_array();
		
		$cItems  				  = "select count(*) totItems from items as i INNER JOIN campaignItemsXref as ref on i.id = ref.itemID
									inner join price_range as pr on i.price_rangeID = pr.id
									LEFT JOIN POSM_Type   ON i.POSMTypeID = POSM_Type.id 
									where pr.id =$curLevelID and ref.campaignID='$CAMPAIGN_ID' and i.POSMTypeID='$curPOSM' ";
		$cItems     =  $this->db->query($cItems);
		$cItems     =  $cItems->result_array();
		
		if($iLikeVotingRulesRef[0]['min_val']!='' AND $iLikeVotingRulesRef[0]['max_val']!='')
		{//CONVERT POINT TO WHOLE NUMBER
		 if($iLikeVotingRulesRef[0]['min_val']<1) $iLikeVotingRulesRef[0]['min_val']= round($iLikeVotingRulesRef[0]['min_val']*$cItems[0]['totItems'],0);
		 if($iLikeVotingRulesRef[0]['max_val']<1) $iLikeVotingRulesRef[0]['max_val']= round($iLikeVotingRulesRef[0]['max_val']*$cItems[0]['totItems'],0);
		 
		 if($typeVal=='MIN_VAL')
		 {
		  return $iLikeVotingRulesRef[0]['min_val'];
		 }
		 elseif($typeVal=='MIN_VAL_MSG')
		 {
		  if($iLikeVotingRulesRef[0]['min_val']==$iLikeVotingRulesRef[0]['max_val'])
		  {
		   return "You LIKED XX items. Kindly view and add more :)<br/>Please make your LIKED items equal to ".$iLikeVotingRulesRef[0]['min_val']." item/s.<br/>Your votes are important to us.";
		  }else{
		   return "You LIKED XX items. Kindly view and add more :)<br/>Please make your LIKED items between ".$iLikeVotingRulesRef[0]['min_val']." and ".$iLikeVotingRulesRef[0]['max_val']." items.<br/>Your votes are important to us.";
		  }
		 }
		 elseif($typeVal=='MAX_VAL')
		 {
		  return $iLikeVotingRulesRef[0]['max_val'];
		 }
		 elseif($typeVal=='MAX_VAL_MSG')
		 {
		  if($iLikeVotingRulesRef[0]['min_val']==$iLikeVotingRulesRef[0]['max_val'])
		  {
		   return "You LIKED XX items which is more than what is needed.<br/> Although we appreciate that you LIKED many of our items,<br/> kindly choose your Top ".$iLikeVotingRulesRef[0]['min_val']." item/s.<br/>Your votes are important to us.";
		  }
		  else{
		   return "You LIKED XX items which is more than what is needed.<br/> Although we appreciate that you LIKED many of our items,<br/> kindly choose your Top ".$iLikeVotingRulesRef[0]['min_val']." to ".$iLikeVotingRulesRef[0]['max_val']." items.<br/>Your votes are important to us.";
		  }
		 }
		}
		elseif($iLikeVotingRulesRef[0]['min_val']!='' AND $iLikeVotingRulesRef[0]['max_val']=='')
		{
		 if($iLikeVotingRulesRef[0]['min_val']<1) $iLikeVotingRulesRef[0]['min_val']= round($iLikeVotingRulesRef[0]['min_val']*$cItems[0]['totItems'],0);
		 if($iLikeVotingRulesRef[0]['max_val']<1) $iLikeVotingRulesRef[0]['max_val']= round($iLikeVotingRulesRef[0]['max_val']*$cItems[0]['totItems'],0);
		 
		 if($iLikeVotingRulesRef[0]['cond1']=="==") $iLikeVotingRulesRef[0]['cond1'] = "equal to";
		 if($iLikeVotingRulesRef[0]['cond1']=="=")  $iLikeVotingRulesRef[0]['cond1'] = "equal to";
		 if($iLikeVotingRulesRef[0]['cond1']==">")  $iLikeVotingRulesRef[0]['cond1'] = "greater than";
		 if($iLikeVotingRulesRef[0]['cond1']==">=") $iLikeVotingRulesRef[0]['cond1'] = "greater than or equal to";
		 if($iLikeVotingRulesRef[0]['cond1']=="<")  $iLikeVotingRulesRef[0]['cond1'] = "less than";
		 if($iLikeVotingRulesRef[0]['cond1']=="<=") $iLikeVotingRulesRef[0]['cond1'] = "less than or equal to";
		 
		 if($typeVal=='MIN_VAL')
		 {
		  return $iLikeVotingRulesRef[0]['min_val'];
		 }
		 elseif($typeVal=='MIN_VAL_MSG')
		 {
		  return "You LIKED XX items. Kindly view and add more :)<br/>Please make your LIKED items ".$iLikeVotingRulesRef[0]['cond1']." ".$iLikeVotingRulesRef[0]['min_val']." items.<br/>Your votes are important to us.";
		 }
		 elseif($typeVal=='MAX_VAL')
		 {
		  return $iLikeVotingRulesRef[0]['min_val'];
		 }
		 elseif($typeVal=='MAX_VAL_MSG')
		 {
		  return "You LIKED XX items which is more than what is needed.<br/> Although we appreciate that you LIKED many of our items,<br/> please make your LIKED items ".$iLikeVotingRulesRef[0]['cond1']." ".$iLikeVotingRulesRef[0]['min_val']." items.<br/>Your votes are important to us.";
		 }
		}
	}
	
	private function min_number_of_votes2($typeVal,$curLevelID,$curPOSM,$CAMPAIGN_ID) 
	{
	    $iWantVotingRulesRef  	  = "select * from iWantVotingRulesRef where campaignID=$CAMPAIGN_ID and price_rangeID=$curLevelID and fieldID=$curPOSM ";
        $iWantVotingRulesRef     =  $this->db->query($iWantVotingRulesRef);
		$iWantVotingRulesRef     =  $iWantVotingRulesRef->result_array();
		
		$cItems  				  = "select count(*) totItems from items as i INNER JOIN campaignItemsXref as ref on i.id = ref.itemID
									inner join price_range as pr on i.price_rangeID = pr.id
									LEFT JOIN POSM_Type   ON i.POSMTypeID = POSM_Type.id 
									where pr.id =$curLevelID and ref.campaignID='$CAMPAIGN_ID' and i.POSMTypeID='$curPOSM' ";
		$cItems     =  $this->db->query($cItems);
		$cItems     =  $cItems->result_array();
		
		if($iWantVotingRulesRef[0]['min_val']!='' AND $iWantVotingRulesRef[0]['max_val']!='')
		{//CONVERT POINT TO WHOLE NUMBER
		 if($iWantVotingRulesRef[0]['min_val']<1) $iWantVotingRulesRef[0]['min_val']= round($iWantVotingRulesRef[0]['min_val']*$cItems[0]['totItems'],0);
		 if($iWantVotingRulesRef[0]['max_val']<1) $iWantVotingRulesRef[0]['max_val']= round($iWantVotingRulesRef[0]['max_val']*$cItems[0]['totItems'],0);
		 
		 if($typeVal=='MIN_VAL')
		 {
		  return $iWantVotingRulesRef[0]['min_val'];
		 }
		 elseif($typeVal=='MIN_VAL_MSG')
		 {
		  if($iWantVotingRulesRef[0]['min_val']==$iWantVotingRulesRef[0]['max_val']){
		   return "You WANTED XX items. Kindly view and add more :)<br/>Please make your WANTED items equal to ".$iWantVotingRulesRef[0]['min_val']." item/s.<br/>Your votes are important to us.";
		  }else{
		   return "You WANTED XX items. Kindly view and add more :)<br/>Please make your WANTED items between ".$iWantVotingRulesRef[0]['min_val']." and ".$iWantVotingRulesRef[0]['max_val']." items.<br/>Your votes are important to us.";
		  }
		 }
		 elseif($typeVal=='MAX_VAL')
		 {
		  return $iWantVotingRulesRef[0]['max_val'];
		 }
		 elseif($typeVal=='MAX_VAL_MSG')
		 {
		  if($iWantVotingRulesRef[0]['min_val']==$iWantVotingRulesRef[0]['max_val']){
		   return "You WANTED XX items which is more than what is needed.<br/> Although we appreciate that you WANTED many of our items,<br/> kindly choose your Top ".$iWantVotingRulesRef[0]['min_val']." item/s.<br/>Your votes are important to us.";
		  }else{
		   return "You WANTED XX items which is more than what is needed.<br/> Although we appreciate that you WANTED many of our items,<br/> kindly choose your Top ".$iWantVotingRulesRef[0]['min_val']." to ".$iWantVotingRulesRef[0]['max_val']." items.<br/>Your votes are important to us.";
		  }
		 }
		}
		elseif($iWantVotingRulesRef[0]['min_val']!='' AND $iWantVotingRulesRef[0]['max_val']=='')
		{
		 if($iWantVotingRulesRef[0]['min_val']<1) $iWantVotingRulesRef[0]['min_val']= round($iWantVotingRulesRef[0]['min_val']*$cItems[0]['totItems'],0);
		 if($iWantVotingRulesRef[0]['max_val']<1) $iWantVotingRulesRef[0]['max_val']= round($iWantVotingRulesRef[0]['max_val']*$cItems[0]['totItems'],0);
		 
		 if($iWantVotingRulesRef[0]['cond1']=="==") $iWantVotingRulesRef[0]['cond1'] = "equal to";
		 if($iWantVotingRulesRef[0]['cond1']=="=")  $iWantVotingRulesRef[0]['cond1'] = "equal to";
		 if($iWantVotingRulesRef[0]['cond1']==">")  $iWantVotingRulesRef[0]['cond1'] = "greater than";
		 if($iWantVotingRulesRef[0]['cond1']==">=") $iWantVotingRulesRef[0]['cond1'] = "greater than or equal to";
		 if($iWantVotingRulesRef[0]['cond1']=="<")  $iWantVotingRulesRef[0]['cond1'] = "less than";
		 if($iWantVotingRulesRef[0]['cond1']=="<=") $iWantVotingRulesRef[0]['cond1'] = "less than or equal to";
		 
		 if($typeVal=='MIN_VAL')
		 {
		  return $iWantVotingRulesRef[0]['min_val'];
		 }
		 elseif($typeVal=='MIN_VAL_MSG')
		 {
		  return "You WANTED XX items. Kindly view and add more :)<br/>Please make your WANTED items ".$iWantVotingRulesRef[0]['cond1']." ".$iWantVotingRulesRef[0]['min_val']." items.<br/>Your votes are important to us.";
		 }
		 elseif($typeVal=='MAX_VAL')
		 {
		  return $iWantVotingRulesRef[0]['min_val'];
		 }
		 elseif($typeVal=='MAX_VAL_MSG')
		 {
		  return "You WANTED XX items which is more than what is needed.<br/> Although we appreciate that you WANTED many of our items,<br/> please make your WANTED items ".$iWantVotingRulesRef[0]['cond1']." ".$iWantVotingRulesRef[0]['min_val']." items.<br/>Your votes are important to us.";
		 }
		}
	}

	private function getCurVoteRules($curLevelID,$curPOSM,$CAMPAIGN_ID) 
	 {
	     $sqlTOTLikes  = "select * from iLikeVotingRulesRef where campaignID=$CAMPAIGN_ID and price_rangeID=$curLevelID and fieldID=$curPOSM ";
         $totLikes     =  $this->db->query($sqlTOTLikes);
		 $totLikes     =  $totLikes->result_array();
		 $sql  = "select count(*) totItems from items as i INNER JOIN campaignItemsXref as ref on i.id = ref.itemID
				inner join price_range as pr on i.price_rangeID = pr.id
				LEFT JOIN POSM_Type   ON i.POSMTypeID = POSM_Type.id 
				where pr.id =$curLevelID and ref.campaignID='$CAMPAIGN_ID' and i.POSMTypeID='$curPOSM'     ";
		 $cItems     =  $this->db->query($sql);
		 $cItems     =  $cItems->result_array();

        if($totLikes[0]['max_val']!=''  and $totLikes[0]['min_val']!='' )
		 {
		 if($totLikes[0]['max_val']<1) $totLikes[0]['max_val']= round($totLikes[0]['max_val']*$cItems[0]['totItems'],0);
		 if($totLikes[0]['min_val']<1) $totLikes[0]['min_val']= round($totLikes[0]['min_val']*$cItems[0]['totItems'],0);
		 $min   = $totLikes[0]['min_val'];
		 $max   = $totLikes[0]['max_val'];
		 $con1  = $totLikes[0]['cond1'];
		 $con2  = $totLikes[0]['cond2'];
		 $lopt  = $totLikes[0]['logical_operator'];
		 //echo "likes $con1 $min  $lopt likes  $con2 $max ";
		 return "likes $con1 $min  $lopt likes  $con2 $max ";
         }
        elseif($totLikes[0]['min_val']!='' and $totLikes[0]['max_val']=='')
         {
		 
	 	 if($totLikes[0]['min_val']<1) $totLikes[0]['min_val']= round($totLikes[0]['min_val']*$cItems[0]['totItems'],0);
		 $min   = $totLikes[0]['min_val'];
		 $con1  = $totLikes[0]['cond1'];
		// echo "likes $con1 $min";
		 return "likes $con1 $min";
		 }		
		
		//$total_likes=0;
		//return $total_likes;
		 
	 }
	
	function iWant($id,$email,$page=1,$curIndexPOSM=0,$curLevelID=0,$review='')
	{
	    $tdate 				 =  date('Y-m-d');
		$data['encemail']    =  $email;
		$data['cam_id']      =  $id;
	    $data['email']   	 =  $email;
		$id    				 =  $this->decode_base64($id);
		$CAMPAIGN_ID		 =  $id;
		$email 				 =  $this->decode_base64($email);
	    $data['vfile']		 =  'votingIwant.php';
	    $data['title']		 =  'SMBi';
	    $data['page_title']	 =  'Voting';
		$limit  			 =  ( $page  * 20 - 20) < 0 ? "0":( $page  * 20 - 20);
		
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
	    $votersSQL1=" SELECT * FROM  `voters` where email='$email'  and  campaignID='$id'  and votingStatus='done'";
		$v1 = $this->db->query($votersSQL1);
	 	$v1 = $v1->result_array();
		 // if done 
		if(isset($v1[0]))
        {		
		 if($v1[0]['votingStatus']=='done'  and $v1[0]['email']=="$email" and  $v1[0]['campaignID']==$id) {header("location:".HTTP_PATH.'gallery/done.html');}
	    }		
		
		$sql ="select * from campaign where   id='$id' and (DateFrom <= '$tdate' and DateTo >='$tdate') and status='on progress'";
	 	$campaign = $this->db->query($sql);
	 
	    // if ended
        if($campaign->num_rows() == 0) header("location:".HTTP_PATH.'gallery/ended.html');
	   			
	    $posmTypeSQL  = "  select  DISTINCT POSM_Type.typeName as POSM_TypeName,POSMTypeID  
							from items as i INNER JOIN campaignItemsXref as ref on i.id = ref.itemID
							LEFT JOIN POSM_Type   ON i.POSMTypeID = POSM_Type.id 
							where ref.campaignID='$id' ORDER BY POSM_Type.typeName asc, i.itemName asc ";
		$posmType     =  $this->db->query($posmTypeSQL); 
		$posmType     =  $posmType->result_array(); 
    
        $curPOSM=$posmType[$curIndexPOSM]['POSMTypeID'];
	    $curPOSMName=$posmType[$curIndexPOSM]['POSM_TypeName'];
		
		$sqlLevel       = "select * from price_range where POSMTypeID='$curPOSM' order by xOrder asc";
		$priceRange     =  $this->db->query($sqlLevel);
		$priceRange     =  $priceRange->result_array(); 
		$PRID           =  $curLevelID==0 ? $priceRange[0]['id']: $curLevelID;
	    $curLevelID     =  $PRID ;
		
		//REVIEW URL
		$data['review_url_no_extension'] 	 = $this->review_url2_iwant($_SERVER['HTTP_REFERER']);
		$data['review_url'] 	     		 = $this->review_url_iwant('url',HTTP_PATH."".substr($_SERVER['REDIRECT_QUERY_STRING'],1),"show_wanted_items");
		$data['review_url_stat']     		 = $this->review_url_iwant('stat',HTTP_PATH."".substr($_SERVER['REDIRECT_QUERY_STRING'],1),"show_wanted_items");
		
		//REVIEW ALL LIKE
		if($page=='show_wanted_items' OR $review=='show_wanted_items' OR $curLevelID=='show_wanted_items')
		{
		$sql1 = $this->db->query("SELECT voters.id as voterID FROM voters WHERE email='$email' AND campaignID='$CAMPAIGN_ID' LIMIT 0,1");
		$sql1 = $sql1->row();
		
		$sql  = "select *,(select image from items_images as img where img.itemID=i.id AND defaultStatus = 1  limit 0,1) as itemImg,
				POSM_Type.typeName as POSM_TypeName
				from items as i INNER JOIN campaignItemsXref as ref on i.id = ref.itemID
				inner join price_range as pr on i.price_rangeID = pr.id
				inner join votexRef  as vRef ON vRef.itemID = i.id
				LEFT JOIN POSM_Type   ON i.POSMTypeID = POSM_Type.id 
				where pr.id =$PRID and ref.campaignID='$CAMPAIGN_ID' and i.POSMTypeID='$curPOSM'  AND (voterID =". $sql1->voterID ."  AND vote='yes') ";
		}
		else
		{
        $sql  = "select *,(select image from items_images as img where img.itemID=i.id AND defaultStatus = 1  limit 0,1) as itemImg,
				POSM_Type.typeName as POSM_TypeName
				from items as i INNER JOIN campaignItemsXref as ref on i.id = ref.itemID
				inner join price_range as pr on i.price_rangeID = pr.id
				LEFT JOIN POSM_Type   ON i.POSMTypeID = POSM_Type.id 
				where pr.id =$PRID and ref.campaignID='$CAMPAIGN_ID' and i.POSMTypeID='$curPOSM'     ORDER BY RAND() ";
		}
	
        $sqlTOT  = "select count(*) tot from items as i INNER JOIN campaignItemsXref as ref on i.id = ref.itemID
				inner join price_range as pr on i.price_rangeID = pr.id
				LEFT JOIN POSM_Type   ON i.POSMTypeID = POSM_Type.id 
				where pr.id =$PRID and ref.campaignID='$CAMPAIGN_ID' and i.POSMTypeID='$curPOSM'     ";
 
		
		 $items        =  $this->db->query($sql); 
		 $items        =  $items->result_array(); 
		 
         $itemTot =  $this->db->query($sqlTOT);
	     $itemTot =  $itemTot->result_array();
         $sqlVoter  = "select * from voters where campaignID='$CAMPAIGN_ID' and email='$email'"; 		
	     $voterINFO = $this->db->query($sqlVoter);
		 $data['voterINFO']   =  $voterINFO->result_array(); 
		 
	     if(isset($_SESSION[$data['cam_id']."$email"])==true)
         {		 
	       $data['currentVote']   =  $_SESSION[$data['cam_id']."$email"];
		    if(count($_SESSION[$data['cam_id']."$email"]) <= 1){
		    $boto="";
			$sql = "SELECT itemID, vote FROM votexRef WHERE campaignID = $CAMPAIGN_ID AND voterID = ".$data['voterINFO'][0]['id'];
			$sql = $this->db->query($sql);
			$votes = $sql->result_array();
			
			foreach($votes as $v){
				$boto[] = array('pid'=>$v['itemID'],'vote'=>$v['vote']);
			}
			$data['currentVote'] = $boto;
			}
		 }
		 else 
		    $data['currentVote']   = '';  
		
		 
		 //VOTER INFO
		 $data['VOTER_ID']    =  $VOTER_ID   = $data['voterINFO'][0]['id'];
		 $data['CAMPAIGN_ID'] = $CAMPAIGN_ID;
		
		
		$data['campaign']    =  $campaign->result_array(); 
		//print_r($data['voterINFO']);
		$sql = $this->db->query("SELECT count(votexRef.id) as totalVote FROM votexRef
                                 left join items on votexRef.itemID = items.id
								 WHERE price_rangeID=$curLevelID and campaignID=$CAMPAIGN_ID AND voterID=$VOTER_ID AND  vote='yes'");
		$curVotes = $sql->result_array(); 
		
		$data['curVotes']      = $curVotes[0]['totalVote']; ;
	    $data['CID']           = $CAMPAIGN_ID;
	    $data['VID']           = $VOTER_ID ;
	    $data['posmTypeList']  = $posmType  ;
		$data['curPOSM']       = $curPOSM ;
		$data['curPOSMName']   = $curPOSMName ;
		$data['curIndexPOSM']  = $curIndexPOSM ;
		$data['limit']         = $limit ;
		$data['priceRange']    = $priceRange ;
		$data['totalLevel']    = count($priceRange) ;
	    $data['galTitle']      = "iWant Campaign"   ;
	    $data['items']         =  $items;
	    $data['total']         =  $itemTot[0]['tot'];  
	    $data['page']          =  $page;  
	    $data['galType']       =  $items;  
	    $data['curLevelID']    =  $curLevelID ;  
	    $data['curLevelCondition']=  $this->getCurVoteRulesIwant($curLevelID,$curPOSM,$CAMPAIGN_ID) ;
		
		//MINIMUM NUMBER OF VOTES
		$data['min_number_of_votes']     =  $this->min_number_of_votes2('MIN_VAL',$curLevelID,$curPOSM,$CAMPAIGN_ID);
		$data['min_number_of_votes_MSG'] =  $this->min_number_of_votes2('MIN_VAL_MSG',$curLevelID,$curPOSM,$CAMPAIGN_ID);
		
		$data['max_number_of_votes']     =  $this->min_number_of_votes2('MAX_VAL',$curLevelID,$curPOSM,$CAMPAIGN_ID);
		$data['max_number_of_votes_MSG'] =  $this->min_number_of_votes2('MAX_VAL_MSG',$curLevelID,$curPOSM,$CAMPAIGN_ID);
		
	    $data['totalLevel']    = count($priceRange) ;		
		$data['csrf']          =  "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>"; 
	    $this->load->view('iWantGallery',$data); 
	}
    
	private function getCurVoteRulesIwant($curLevelID,$curPOSM,$CAMPAIGN_ID) 
	 {
	     $sqlTOTLikes  = "select * from iWantVotingRulesRef where campaignID=$CAMPAIGN_ID and price_rangeID=$curLevelID and fieldID=$curPOSM ";
         $totLikes     =  $this->db->query($sqlTOTLikes);
		 $totLikes     =  $totLikes->result_array();
		 $sql  = "select count(*) totItems from items as i INNER JOIN campaignItemsXref as ref on i.id = ref.itemID
				inner join price_range as pr on i.price_rangeID = pr.id
				LEFT JOIN POSM_Type   ON i.POSMTypeID = POSM_Type.id 
				where pr.id =$curLevelID and ref.campaignID='$CAMPAIGN_ID' and i.POSMTypeID='$curPOSM'     ";
		 $cItems     =  $this->db->query($sql);
		 $cItems     =  $cItems->result_array();
        if($totLikes[0]['max_val']!=''  and $totLikes[0]['min_val']!='' )
		 {
		 if($totLikes[0]['max_val']<1) $totLikes[0]['max_val']= round($totLikes[0]['max_val']*$cItems[0]['totItems'],0);
		 if($totLikes[0]['min_val']<1) $totLikes[0]['min_val']= round($totLikes[0]['min_val']*$cItems[0]['totItems'],0);
		 $min   = $totLikes[0]['min_val'];
		 $max   = $totLikes[0]['max_val'];
		 $con1  = $totLikes[0]['cond1'];
		 $con2  = $totLikes[0]['cond2'];
		 $lopt  = $totLikes[0]['logical_operator'];
		 return "likes $con1 $min  $lopt likes  $con2 $max ";
         }
        elseif($totLikes[0]['min_val']!='' and $totLikes[0]['max_val']=='')
         {
		 
	 	 if($totLikes[0]['min_val']<1) $totLikes[0]['min_val']= round($totLikes[0]['min_val']*$cItems[0]['totItems'],0);
		 $min   = $totLikes[0]['min_val'];
		 $con1  = $totLikes[0]['cond1'];
		 return "likes $con1 $min";
		 }		
	
		 
	 }
	function common($action='',$id='',$txtsearch='',$selPOSMType='',$selPOSMStatus='',$selPremiumType='',$seloutlet='',$selCountry='',$selBrand='',$selMaterial='',$items_date='',$nviews='',$sort_by_price='',$priceRange='',$priceFrom='',$priceTo='',$year='',$month='',$priceRangeID='')
	{
		$this->modules->session_handler();
	    $this->modules->module_checker(32,'REVIEW');
        $countryID = $_SESSION['countryID'];
	    $table= "items";
		$data['vfile']				= 'common.php';
	    $data['title']				= 'Common | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia'; 
		$data['galTitle']			= 'Common Gallery';
		
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(1);
		
		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
		$data['breadCrumbs']	= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']   .= '<a href='.$HTTP_PATH.'gallery/common> Common Gallery </a>';
        $data['csrf'] = "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>"; 
		$data['searchAction'] = HTTP_PATH."gallery/common"; 
		$data['post'] = $_POST; $filter2="";
		
		$order = "COUNT(item_views.itemID) DESC, items.dateReleased DESC,";
		
        $data['redirectTo']	= "common";
        if(($txtsearch!='' OR $selPOSMType!='' OR $selPOSMStatus!='' OR $selPremiumType!='' OR $seloutlet!='' OR $selCountry!='' OR $selBrand!='' OR $selMaterial!='' OR $items_date=!'' OR $nviews!='' OR $sort_by_price!='' OR $priceRange!='' OR $priceRangeID!=''))		
		{ 
			extract($_POST);  
			//$_SESSION['txtsearch']="";
              if($selPOSMType!='null' 	 AND $selPOSMType!='')     	$filter2 .= " AND POSMTypeID		='$selPOSMType'";
              if($selPOSMStatus!='null'  AND $selPOSMStatus!='')   	$filter2 .= " AND POSMStatusID		='$selPOSMStatus'";
			  if($selPremiumType!='null' AND $selPremiumType!='')  	$filter2 .= " AND PremiumTypeID		='$selPremiumType'";
			  if($seloutlet!='null' 	 AND $seloutlet!='')       	$filter2 .= " AND OUTLETStatusID	='$seloutlet'";
			  if($selCountry!='null'	 AND $selCountry!='')      	$filter2 .= " AND countryID			='$selCountry'";
			  if($selBrand!='null' 		 AND $selBrand!='')        	$filter2 .= " AND brandID 			='$selBrand'";
			  if($selMaterial!='null' 	 AND $selMaterial!='')     	$filter2 .= " AND MaterialTypeID	='$selMaterial'";
			  if($priceRangeID!='null'   AND $priceRangeID!='')    $filter2 .= " AND price_rangeID	    ='$priceRangeID'";
			  
			  if($txtsearch!='' AND $txtsearch!='null'){
				$txtsearch = addslashes($txtsearch);
				$txtsearch = trim($txtsearch);
				$txtsearch = str_replace("%20"," ",$txtsearch);
				$filter2 .= " AND ( itemCode like  '%$txtsearch%'  or itemName like  '%$txtsearch%'  or Short_Description like '%$txtsearch%' or Long_Description like '%$txtsearch%')";
			  }   
			  

			  //PRICE RANGE	
			  if(($priceRange!='' AND $priceRange!='null') AND is_numeric($priceFrom) AND is_numeric($priceTo))
					$filter2 = "AND $priceRange >= $priceFrom AND $priceRange <= $priceTo ";
			  
			  
			  //DATE RANGE
			  $m=0;
			  if($year!='' AND $year!='null')
				$m = $year * 12;  
			  if($month!='' AND $month!='null')
				$m += $month;
			  
			  if(($year!='' AND $year!='null' AND $year!=0)  OR ($month!='' AND $month!='null' AND $month!=0)){
				$filter2 .= " AND items.dateAdded <= CURDATE() AND items.dateAdded >= (SELECT CURDATE() - INTERVAL $m MONTH) ";
			  }
			  
			  
			   //SORT BY DATE
			   if(($nviews!='' AND $nviews!='null') OR ($sort_by_price!='' AND $sort_by_price!='null') OR ($items_date!=''  AND $items_date!='null'))
					$order = "";
			 
			   if($nviews!='null' 	 	 AND $nviews!='')          $order  .= " COUNT(item_views.itemID) $nviews,";
			   if($sort_by_price!='null' AND $sort_by_price!='')   $order  .= str_replace("-"," ",$sort_by_price.","); 
			   if($items_date!='null' 	 AND $items_date!='')      $order  .= " items.dateReleased $items_date,"; 
			  
		} 
		$order = substr($order, 0,-1);
		
		
		//STATUS LISTS
        $sqlSTr="SELECT *,
				OUTLET_Status.statusName as OutletStatusName, 
				POSM_Status.statusName as POSMStatusName,
				POSM_Type.typeName as POSM_TypeName,
				items.id as itemID, count(item_views.itemID) as iViews 
				FROM items 
				LEFT JOIN POSM_Type 		ON items.POSMTypeID = POSM_Type.id 
				LEFT JOIN POSM_Status 		ON items.POSMStatusID = POSM_Status.id 
				LEFT JOIN OUTLET_Status 	ON items.OUTLETStatusID = OUTLET_Status.id
				LEFT JOIN premiumItemType 	ON items.PremiumTypeID = premiumItemType.id 
				LEFT JOIN MATERIAL_Type 	ON items.MaterialTypeID = MATERIAL_Type.id 
				LEFT JOIN country 			ON items.countryID = country.id 
				LEFT JOIN brands  			ON items.brandID = brands.id 
				LEFT JOIN item_views  		ON items.id = item_views.itemID
				WHERE items.brandID IN 
				(SELECT brandID FROM commonGalleryBrands) 
				AND publish !='n' $filter2 
				AND items.purge='n' AND items.archive='n' AND items.id NOT IN (".$this->modules->generateItemsForArchive().")
				GROUP BY items.id ORDER BY $order ";
				
	
		//TOTAL NUMBER OF ROWS
		$data['active_page']= 1;
		if($id!='') 
			$data['active_page']= $id;
		
		
		$sql = $this->db->query($sqlSTr);
		$sql = $sql->result_array();
		$total_rec = count($sql);

		$data['total_rec'] = $total_rec; 
		$pagenum = $data['active_page'];
		$data['page_rows'] = 15; 
		$data['last'] = ceil($total_rec/$data['page_rows']);		
		$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		
				//NEW FUNCTION
		$url="";
		$data['txtsearch'] 		= "null";
		$data['selPOSMType'] 	= "null";
		$data['selPOSMStatus'] 	= "null";
		$data['selPremiumType'] = "null";
		$data['seloutlet'] 		= "null";
		$data['selCountry'] 	= "null";
		$data['selBrand'] 		= "null";
		$data['selMaterial'] 	= "null";
		$data['items_date'] 	= "null";
		$data['nviews'] 		= "null";
		$data['sort_by_price'] 	= "null";
		$data['priceRange'] 	= "null";
		$data['priceFrom'] 		= "null";
		$data['priceTo'] 		= "null";
		$data['year'] 			= "null";
		$data['month'] 			= "null";
		$data['priceRangeID'] 	= "null";

		if($action=="page")
		{
			$this->modules->module_checker(18,'REVIEW');
			//REPOST DATA
			if($txtsearch!='')   	
				$data['txtsearch'] 		=  $txtsearch;
			if($selPOSMType!='')   	
				$data['selPOSMType'] 	=  $selPOSMType;
			if($selPOSMStatus!='')   	
				$data['selPOSMStatus'] 	=  $selPOSMStatus;	
			if($selPremiumType!='')   	
				$data['selPremiumType'] =  $selPremiumType;	
			if($seloutlet!='')   	
				$data['seloutlet'] 		=  $seloutlet;
			if($selCountry!='')   	
				$data['selCountry'] 	=  $selCountry;
			if($selBrand!='')   	
				$data['selBrand'] 		=  $selBrand;
			if($selMaterial!='')   	
				$data['selMaterial'] 	=  $selMaterial;
			if($items_date!='')   	
				$data['items_date'] 	=  $items_date;
			if($nviews!='')   	
				$data['nviews'] 		=  $nviews;
			if($sort_by_price!='')   	
				$data['sort_by_price'] 	=  $sort_by_price;
			if($priceRange!='')   	
				$data['priceRange'] 	=  $priceRange;
			if($priceFrom!='')   	
				$data['priceFrom'] 		=  $priceFrom;
			if($priceTo!='')   	
				$data['priceTo'] 		=  $priceTo;
			if($year!='')   	
				$data['year'] 			=  $year;
			if($month!='')   	
				$data['month'] 			=  $month;
			if($priceRangeID!='')   	
				$data['priceRangeID'] 	=  $priceRangeID;
		}
		
		//SEARCH ACTION
		$data['url']  = $data['txtsearch']."/".$data['selPOSMType']."/".$data['selPOSMStatus']."/".$data['selPremiumType']."/".$data['seloutlet']."/".$data['selCountry']."/".$data['selBrand']."/".$data['selMaterial'].'/';
		$data['url'] .= $data['items_date']."/".$data['nviews']."/".$data['sort_by_price']."/".$data['priceRange']."/".$data['priceFrom']."/".$data['priceTo']."/".$data['year']."/".$data['month']."/".$data['priceRangeID']; 
		$data['searchAction'] = HTTP_PATH. "gallery/redirect_link/".$data['redirectTo']."/page/1/".$data['url']; 

		$sql = $this->db->query($sqlSTr ." ". $max);
		$data['items'] = $sql->result_array();
		
		//FEATURED BRANDS
		$sql = $this->db->query("SELECT brandName FROM brands 
								 INNER JOIN commonGalleryBrands ON commonGalleryBrands.brandID = brands.id 
								 ORDER BY brandName ASC");
		$data['featured_brands'] = $sql->result_array();
		
		if($this->modules->access_checker()==TRUE)
	    {
			$this->load->view('galleryHeader',$data); 
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
	
	function eCatalog($action='',$id='',$page='',$page_num='',$txtsearch='',$selPOSMType='',$selPremiumType='',$seloutlet='',$selMaterial='',$items_date='',$nviews='',$sort_by_price='',$priceRange='',$priceFrom='',$priceTo='',$year='',$month='')
	{
		$this->modules->session_handler();
		$this->modules->module_checker(34,'REVIEW');
		$filter_AND = $this->modules->country2();
	    //$filter = $this->modules->country();
	    $filter = '';
		
	    $data['title']				= 'Items | San Miguel Brewing International';
	    $data['galTitle']			= 'eCatalogue';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia'; 
		$data['ecID']				= $id;
		
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(3);
		
		$data['EDIT'] 	=  $this->modules->crud_checker(34,'EDIT');
		
		$data['searchAction'] = HTTP_PATH. "gallery/eCatalog/view"; 
		
		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
		
		$data['breadCrumbs']	= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']   .= '<a href='.$HTTP_PATH.'gallery/eCatalog> eCatalogue </a>';
		
		if($id!=0){
		$sql = $this->db->query("SELECT title FROM e_catalog WHERE id=$id");
		$row = $sql->row();
		
		$data['breadCrumbs']   .= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
		$data['breadCrumbs']   .= "<a href='".HTTP_PATH."gallery/eCatalog/view/$id'> ". $row->title ."</a>";
		}
			
		$data['csrf'] 			= "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>"; 
		$data['searchAction'] 	= HTTP_PATH."gallery/eCatalog/view/$id.html"; 
		$f=""; $countryID 		= $_SESSION['countryID'];
		
		
		// search filter
		$data['post'] = $_POST; $filter2="";
		 
		$order = "count(ecitem_views.itemID) DESC, ec_items.dateReleased DESC,";
        
		$data['redirectTo']	= "eCatalog";
        if(($txtsearch!='' OR $selPOSMType!='' OR $selPremiumType!='' OR $seloutlet!='' OR $selMaterial!='' OR $items_date=!'' OR $nviews!='' OR $sort_by_price!='' OR $priceRange!='' OR $year!='' OR $month!=''))		
		{ 
			extract($_POST);  
			//$_SESSION['txtsearch']="";
              if($selPOSMType!='null' 	 AND $selPOSMType!='')     	$filter2 .= " AND POSMTypeID		='$selPOSMType'";
			  if($selPremiumType!='null' AND $selPremiumType!='')  	$filter2 .= " AND PremiumTypeID		='$selPremiumType'";
			  if($seloutlet!='null' 	 AND $seloutlet!='')       	$filter2 .= " AND OUTLETStatusID	='$seloutlet'";
			  if($selMaterial!='null' 	 AND $selMaterial!='')     	$filter2 .= " AND MaterialTypeID	='$selMaterial'";
			  
			  
			  if($txtsearch!='' AND $txtsearch!='null'){
				$txtsearch = addslashes($txtsearch);
				$txtsearch = trim($txtsearch);
				$txtsearch = str_replace("%20"," ",$txtsearch);
				$filter2 .= " AND ( itemCode like  '%$txtsearch%'  or itemName like  '%$txtsearch%'  or Short_Description like '%$txtsearch%' or Long_Description like '%$txtsearch%')";
			  }   
			  

			  //PRICE RANGE	
			  if(($priceRange!='' AND $priceRange!='null') AND is_numeric($priceFrom) AND is_numeric($priceTo))
					$filter2 = "AND $priceRange >= $priceFrom AND $priceRange <= $priceTo ";
			  
			  
			  //DATE RANGE
			  $m=0;
			  if($year!='' AND $year!='null')
				$m = $year * 12;  
			  if($month!='' AND $month!='null')
				$m += $month;
			  
			  if(($year!='' AND $year!='null' AND $year!=0)  OR ($month!='' AND $month!='null' AND $month!=0)){
				$filter2 .= " AND ec_items.dateAdded <= CURDATE() AND ec_items.dateAdded >= (SELECT CURDATE() - INTERVAL $m MONTH) ";
			  }
			  
			  
			   //SORT BY DATE
			   if(($nviews!='' AND $nviews!='null') OR ($sort_by_price!='' AND $sort_by_price!='null') OR ($items_date!=''  AND $items_date!='null'))
					$order = "";
			 
			   if($nviews!='null' 	 	 AND $nviews!='')          $order  .= " COUNT(ecitem_views.itemID) $nviews,";
			   if($sort_by_price!='null' AND $sort_by_price!='')   $order  .= str_replace("-"," ",$sort_by_price.","); 
			   if($items_date!='null' 	 AND $items_date!='')      $order  .= " ec_items.dateReleased $items_date,"; 
			  
		} 
		$order = substr($order, 0,-1);
		
		if($filter=='' & $filter2!='') $filter = $filter2;
		else $filter = "$filter   $filter2"; 
		
		
		$sqlSTr="SELECT *,
					OUTLET_Status.statusName as OutletStatusName, 
					POSM_Status.statusName as POSMStatusName,
					ec_items.id as itemID,
					e_catalog.title as eTitle, COUNT(ecitem_views.itemID) as iViews
					FROM ec_items 
					LEFT JOIN POSM_Type 			ON ec_items.POSMTypeID = POSM_Type.id 
					LEFT JOIN POSM_Status 			ON ec_items.POSMStatusID = POSM_Status.id 
					LEFT JOIN OUTLET_Status 		ON ec_items.OUTLETStatusID = OUTLET_Status.id
					LEFT JOIN premiumItemType 		ON ec_items.PremiumTypeID = premiumItemType.id 
					LEFT JOIN MATERIAL_Type 		ON ec_items.MaterialTypeID = MATERIAL_Type.id 
					LEFT JOIN country 				ON ec_items.countryID = country.id 
					LEFT JOIN e_catalog  			ON ec_items.ecID = e_catalog.id 
					LEFT JOIN ecitem_views  		ON ec_items.id = ecitem_views.itemID
					where ec_items.publish ='y' and ec_items.ecID='$id' $filter 
					GROUP BY ec_items.id ORDER BY $order";
		
		if($action=='')
		{
		   $this->modules->module_checker(34,'REVIEW');
		   $sqlSTr			= "select * from e_catalog where publish='y'";
		   $data['vfile']	= 'eCatalogList.php';
		}
		if($action=='view')
        { 
		   $this->modules->module_checker(34,'REVIEW');
		   $sql = $this->db->query("SELECT title FROM e_catalog WHERE id = $id");
		   $row = $sql->row();
		   
		    $sql = $this->db->query($sqlSTr);
			$sql = $sql->result_array();
			$total_rec = count($sql);
			$data['total_rec'] = $total_rec;
		   
		   $data['galTitle']	= "eCatalogue: ".$row->title;
		   $data['vfile'] 		= 'eCatalogItems.php';
		   $data['eCatalogItems'] = true;
		   
		   //NEW FUNCTION
			$url="";
			$data['txtsearch'] 		= "null";
			$data['selPOSMType'] 	= "null";
			$data['selPremiumType'] = "null";
			$data['seloutlet'] 		= "null";
			$data['selMaterial'] 	= "null";
			$data['items_date'] 	= "null";
			$data['nviews'] 		= "null";
			$data['sort_by_price'] 	= "null";
			$data['priceRange'] 	= "null";
			$data['priceFrom'] 		= "null";
			$data['priceTo'] 		= "null";
			$data['year'] 			= "null";
			$data['month'] 			= "null";


			//REPOST DATA
			if($txtsearch!='')   	
				$data['txtsearch'] 		=  $txtsearch;
			if($selPOSMType!='')   	
				$data['selPOSMType'] 	=  $selPOSMType;
			if($selPremiumType!='')   	
				$data['selPremiumType'] =  $selPremiumType;	
			if($seloutlet!='')   	
				$data['seloutlet'] 		=  $seloutlet;
			if($selMaterial!='')   	
				$data['selMaterial'] 	=  $selMaterial;
			if($items_date!='')   	
				$data['items_date'] 	=  $items_date;
			if($nviews!='')   	
				$data['nviews'] 		=  $nviews;
			if($sort_by_price!='')   	
				$data['sort_by_price'] 	=  $sort_by_price;
			if($priceRange!='')   	
				$data['priceRange'] 	=  $priceRange;
			if($priceFrom!='')   	
				$data['priceFrom'] 		=  $priceFrom;
			if($priceTo!='')   	
				$data['priceTo'] 		=  $priceTo;
			if($year!='')   	
				$data['year'] 			=  $year;
			if($month!='')   	
				$data['month'] 			=  $month;
			
			if($items_date==1) $data['items_date']= "null";
			
			//SEARCH ACTION
			$data['url']  = $data['txtsearch']."/".$data['selPOSMType']."/".$data['selPremiumType']."/".$data['seloutlet']."/".$data['selMaterial'].'/';
			$data['url'] .= $data['items_date']."/".$data['nviews']."/".$data['sort_by_price']."/".$data['priceRange']."/".$data['priceFrom']."/".$data['priceTo']."/".$data['year']."/".$data['month']; 
			$data['searchAction'] = HTTP_PATH. "gallery/redirect_link_ecat/$id/page/1/".$data['url']; 
		}	
		
		
		$data['active_page']= 1;
		if($page_num!='') 
			$data['active_page']= $page_num;
		
		$sql = $this->db->query($sqlSTr);
		$sql = $sql->result_array();
		$total_rec = count($sql);

		$pagenum = $data['active_page'];
		$data['page_rows'] = 15; 
		$data['last'] = ceil($total_rec/$data['page_rows']);		
		$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		
		
		$sql 			= $this->db->query($sqlSTr ."  ". $max ."  ");
		$data['items'] 	= $sql->result_array();
	
		
		if($this->modules->access_checker()==TRUE)
	    {
			$this->load->view('galleryHeader',$data); 
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
	
	function redirect_link($view='',$action='',$id='',$txtsearch='',$selPOSMType='',$selPOSMStatus='',$selPremiumType='',$seloutlet='',$selCountry='',$selBrand='',$selMaterial='',$items_date='',$nviews='',$sort_by_price='',$priceRange='',$priceFrom='',$priceTo='',$year='',$month='',$priceRangeID='')
	{
		extract($_POST); 
		//NEW FUNCTION
		$url="";
		$data['txtsearch'] 		= "null";
		$data['selPOSMType'] 	= "null";
		$data['selPOSMStatus'] 	= "null";
		$data['selPremiumType'] = "null";
		$data['seloutlet'] 		= "null";
		$data['selCountry'] 	= "null";
		$data['selBrand'] 		= "null";
		$data['selMaterial'] 	= "null";
		$data['items_date'] 	= "null";
		$data['nviews'] 		= "null";
		$data['sort_by_price'] 	= "null";
		$data['priceRange'] 	= "null";
		$data['priceFrom'] 		= "null";
		$data['priceTo'] 		= "null";
		$data['year'] 			= "null";
		$data['month'] 			= "null";
		$data['priceRangeID'] 	= "null";

		if($action=="page")
		{
			//REPOST DATA
			$txtsearch = addslashes($txtsearch);
			$txtsearch = trim($txtsearch);
			$txtsearch = str_replace("%20"," ",$txtsearch);
			$txtsearch = str_replace("'","",$txtsearch);
			$txtsearch = str_replace("/","",$txtsearch);
			if($txtsearch!='')   	
				$data['txtsearch'] 		=  $txtsearch;
			if($selPOSMType!='')   	
				$data['selPOSMType'] 	=  $selPOSMType;
			if($selPOSMStatus!='')   	
				$data['selPOSMStatus'] 	=  $selPOSMStatus;	
			if($selPremiumType!='')   	
				$data['selPremiumType'] =  $selPremiumType;	
			if($seloutlet!='')   	
				$data['seloutlet'] 		=  $seloutlet;
			if($selCountry!='')   	
				$data['selCountry'] 	=  $selCountry;
			if($selBrand!='')   	
				$data['selBrand'] 		=  $selBrand;
			if($selMaterial!='')   	
				$data['selMaterial'] 	=  $selMaterial;
			if($items_date!='')   	
				$data['items_date'] 	=  $items_date;
			if($nviews!='')   	
				$data['nviews'] 		=  $nviews;
			if($sort_by_price!='')   	
				$data['sort_by_price'] 	=  $sort_by_price;
			if($priceRange!='')   	
				$data['priceRange'] 	=  $priceRange;
			if($priceFrom!='')   	
				$data['priceFrom'] 		=  $priceFrom;
			if($priceTo!='')   	
				$data['priceTo'] 		=  $priceTo;
			if($year!='')   	
				$data['year'] 			=  $year;
			if($month!='')   	
				$data['month'] 			=  $month;
			if($priceRangeID!='')   	
				$data['priceRangeID'] 	=  $priceRangeID;
		}
		
		
		$data['url']  = $data['txtsearch']."/".$data['selPOSMType']."/".$data['selPOSMStatus']."/".$data['selPremiumType']."/".$data['seloutlet']."/".$data['selCountry']."/".$data['selBrand']."/".$data['selMaterial'].'/';
		$data['url'] .= $data['items_date']."/".$data['nviews']."/".$data['sort_by_price']."/".$data['priceRange']."/".$data['priceFrom']."/".$data['priceTo']."/".$data['year']."/".$data['month']."/".$data['priceRangeID'] ; 
		if($view=='my_Gallery')
		{			
			$data['searchAction'] = HTTP_PATH. "gallery/my_Gallery/page/$id/".$data['url']; 	
		}
		elseif($view=='common')
		{			
			$data['searchAction'] = HTTP_PATH. "gallery/common/page/$id/".$data['url']; 	
		}		
		elseif($view=='popular_items_gallery')
		{			
			$data['searchAction'] = HTTP_PATH. "gallery/popular_items_gallery/page/$id/".$data['url']; 	
		}
	
		redirect($data['searchAction'], 'location', 301);
	}
	
	//http://smbi.dev.c3-interactive.com.ph/gallery/redirect_link_ecat/eCatalog/view/31/page/1/null/null/null/null/null/null/null/null/null/null/null/null/null
	function redirect_link_ecat($id='',$page='',$page_num='',$txtsearch='',$selPOSMType='',$selPremiumType='',$seloutlet='',$selMaterial='',$items_date='',$nviews='',$sort_by_price='',$priceRange='',$priceFrom='',$priceTo='',$year='',$month='')
	{
		extract($_POST); 
		//NEW FUNCTION
		$url="";
		$data['txtsearch'] 		= "null";
		$data['selPOSMType'] 	= "null";
		$data['selPremiumType'] = "null";
		$data['seloutlet'] 		= "null";
		$data['selMaterial'] 	= "null";
		$data['items_date'] 	= "null";
		$data['nviews'] 		= "null";
		$data['sort_by_price'] 	= "null";
		$data['priceRange'] 	= "null";
		$data['priceFrom'] 		= "null";
		$data['priceTo'] 		= "null";
		$data['year'] 			= "null";
		$data['month'] 			= "null";


		if($page=="page")
		{
			$txtsearch = addslashes($txtsearch);
			$txtsearch = trim($txtsearch);
			$txtsearch = str_replace("%20"," ",$txtsearch);
			$txtsearch = str_replace("'","",$txtsearch);
			$txtsearch = str_replace("/","",$txtsearch);
			//REPOST DATA
			if($txtsearch!='')   	
				$data['txtsearch'] 		=  $txtsearch;
			if($selPOSMType!='')   	
				$data['selPOSMType'] 	=  $selPOSMType;
			if($selPremiumType!='')   	
				$data['selPremiumType'] =  $selPremiumType;	
			if($seloutlet!='')   	
				$data['seloutlet'] 		=  $seloutlet;
			if($selMaterial!='')   	
				$data['selMaterial'] 	=  $selMaterial;
			if($items_date!='')   	
				$data['items_date'] 	=  $items_date;
			if($nviews!='')   	
				$data['nviews'] 		=  $nviews;
			if($sort_by_price!='')   	
				$data['sort_by_price'] 	=  $sort_by_price;
			if($priceRange!='')   	
				$data['priceRange'] 	=  $priceRange;
			if($priceFrom!='')   	
				$data['priceFrom'] 		=  $priceFrom;
			if($priceTo!='')   	
				$data['priceTo'] 		=  $priceTo;
			if($year!='')   	
				$data['year'] 			=  $year;
			if($month!='')   	
				$data['month'] 			=  $month;
		}
		
		//SEARCH ACTION
		$data['url']  = $data['txtsearch']."/".$data['selPOSMType']."/".$data['selPremiumType']."/".$data['seloutlet']."/".$data['selMaterial'].'/';
		$data['url'] .= $data['items_date']."/".$data['nviews']."/".$data['sort_by_price']."/".$data['priceRange']."/".$data['priceFrom']."/".$data['priceTo']."/".$data['year']."/".$data['month']; 
		$data['searchAction'] = HTTP_PATH. "gallery/eCatalog/view/$id/page/$page_num/".$data['url']; 
		
		redirect($data['searchAction'], 'location', 301);
	}
	
	function popular_items_gallery($action='',$id='',$txtsearch='',$selPOSMType='',$selPOSMStatus='',$selPremiumType='',$seloutlet='',$selCountry='',$selBrand='',$selMaterial='',$items_date='',$nviews='',$sort_by_price='',$priceRange='',$priceFrom='',$priceTo='',$year='',$month='',$priceRangeID='')
	{ 
		$this->modules->session_handler();
		$this->modules->module_checker(81,'REVIEW');
		
	    $filter = $this->modules->country2();
		
	    $table= "items";
		$data['vfile']				= 'popular_items_gallery.php';
	    $data['title']				= 'Items | San Miguel Brewing International';
	    $data['galTitle']			= 'Popular Items';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia'; 
		
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(2);
		
		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
		$data['breadCrumbs']	= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']   .= '<a href='.$HTTP_PATH.'gallery/popular_items_gallery> Popular Items </a>';
		$data['csrf'] 			= "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>"; 
		$data['searchAction']   = HTTP_PATH."gallery/popular_items_gallery"; 
		
		//print_r($_POST);
		$data['post'] = $_POST; $filter2="";
		
		$order = "COUNT(item_views.itemID) DESC, items.dateReleased DESC,";
        
		$data['redirectTo']	= "popular_items_gallery";
        if(($txtsearch!='' OR $selPOSMType!='' OR $selPOSMStatus!='' OR $selPremiumType!='' OR $seloutlet!='' OR $selCountry!='' OR $selBrand!='' OR $selMaterial!='' OR $items_date=!'' OR $nviews!='' OR $sort_by_price!='' OR $priceRange!=''  OR $priceRangeID!='' ))		
		{ 
			extract($_POST);  
			//$_SESSION['txtsearch']="";
              if($selPOSMType!='null' 	 AND $selPOSMType!='')     	$filter2 .= " AND POSMTypeID		='$selPOSMType'";
              if($selPOSMStatus!='null'  AND $selPOSMStatus!='')   	$filter2 .= " AND POSMStatusID		='$selPOSMStatus'";
			  if($selPremiumType!='null' AND $selPremiumType!='')  	$filter2 .= " AND PremiumTypeID		='$selPremiumType'";
			  if($seloutlet!='null' 	 AND $seloutlet!='')       	$filter2 .= " AND OUTLETStatusID	='$seloutlet'";
			  if($selCountry!='null'	 AND $selCountry!='')      	$filter2 .= " AND countryID			='$selCountry'";
			  if($selBrand!='null' 		 AND $selBrand!='')        	$filter2 .= " AND brandID 			='$selBrand'";
			  if($selMaterial!='null' 	 AND $selMaterial!='')     	$filter2 .= " AND MaterialTypeID	='$selMaterial'";
			  if($priceRangeID!='null'   AND $priceRangeID!='')    $filter2 .= " AND price_rangeID	    ='$priceRangeID'";
			  
			  if($txtsearch!='' AND $txtsearch!='null'){
				$txtsearch = addslashes($txtsearch);
				$txtsearch = trim($txtsearch);
				$txtsearch = str_replace("%20"," ",$txtsearch);
				$filter2 .= " AND ( itemCode like  '%$txtsearch%'  or itemName like  '%$txtsearch%'  or Short_Description like '%$txtsearch%' or Long_Description like '%$txtsearch%')";
			  }   
			  

			  //PRICE RANGE	
			  if(($priceRange!='' AND $priceRange!='null') AND is_numeric($priceFrom) AND is_numeric($priceTo))
					$filter2 = "AND $priceRange >= $priceFrom AND $priceRange <= $priceTo ";
			  
			  
			  //DATE RANGE
			  $m=0;
			  if($year!='' AND $year!='null')
				$m = $year * 12;  
			  if($month!='' AND $month!='null')
				$m += $month;
			  
			  if(($year!='' AND $year!='null' AND $year!=0)  OR ($month!='' AND $month!='null' AND $month!=0)){
				$filter2 .= " AND items.dateAdded <= CURDATE() AND items.dateAdded >= (SELECT CURDATE() - INTERVAL $m MONTH) ";
			  }
			  
			  
			   //SORT BY DATE
			   if(($nviews!='' AND $nviews!='null') OR ($sort_by_price!='' AND $sort_by_price!='null') AND ($items_date!=''  OR $items_date!='null'))
					$order = "";
			 
			   if($nviews!='null' 	 	 AND $nviews!='')          $order  .= " COUNT(item_views.itemID) $nviews,";
			   if($sort_by_price!='null' AND $sort_by_price!='')   $order  .= str_replace("-"," ",$sort_by_price.","); 
			   if($items_date!='null' 	 AND $items_date!='')      $order  .= " items.dateReleased $items_date,"; 
			  
		} 
		$order = substr($order, 0,-1);
		
		if($filter=='' & $filter2!='') $filter = " AND ". substr($filter2,4)." AND items.popular = 'y' ";
		else $filter = "$filter   $filter2 AND items.popular = 'y'"; 
		
		$sqlSTr="SELECT *,
				OUTLET_Status.statusName as OutletStatusName, 
				POSM_Status.statusName as POSMStatusName,
				POSM_Type.typeName as POSM_TypeName,
				items.id as itemID, count(item_views.itemID) as iViews  
				FROM items 
				LEFT JOIN POSM_Type ON items.POSMTypeID = POSM_Type.id 
				LEFT JOIN POSM_Status ON items.POSMStatusID = POSM_Status.id 
				LEFT JOIN OUTLET_Status ON items.OUTLETStatusID = OUTLET_Status.id
				LEFT JOIN premiumItemType ON items.PremiumTypeID = premiumItemType.id 
				LEFT JOIN MATERIAL_Type ON items.MaterialTypeID = MATERIAL_Type.id 
				LEFT JOIN country ON items.countryID = country.id 
				LEFT JOIN brands  ON items.brandID = brands.id
				LEFT JOIN item_views  		ON items.id = item_views.itemID
				WHERE publish ='y' $filter 
				AND items.purge='n' AND items.archive='n' 
				AND DATEDIFF(CURDATE(),items.dateReleased) < ".$this->modules->itemsAge()."
				GROUP BY items.id ORDER BY $order ";
		
		
		//TOTAL NUMBER OF ROWS
		$data['active_page']= 1;
		if($id!='') 
			$data['active_page']= $id;
		
		$sql = $this->db->query($sqlSTr);
		$sql = $sql->result_array();
		$total_rec = count($sql);

		$data['total_rec'] = $total_rec;
		$pagenum = $data['active_page'];
		$data['page_rows'] = 15; 
		$data['last'] = ceil($total_rec/$data['page_rows']);		
		$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		
		$sql 		= $this->db->query($sqlSTr ." ". $max);
		$data['items'] = $sql->result_array();

		//NEW FUNCTION
		$url="";
		$data['txtsearch'] 		= "null";
		$data['selPOSMType'] 	= "null";
		$data['selPOSMStatus'] 	= "null";
		$data['selPremiumType'] = "null";
		$data['seloutlet'] 		= "null";
		$data['selCountry'] 	= "null";
		$data['selBrand'] 		= "null";
		$data['selMaterial'] 	= "null";
		$data['items_date'] 	= "null";
		$data['nviews'] 		= "null";
		$data['sort_by_price'] 	= "null";
		$data['priceRange'] 	= "null";
		$data['priceFrom'] 		= "null";
		$data['priceTo'] 		= "null";
		$data['year'] 			= "null";
		$data['month'] 			= "null";
		$data['priceRangeID'] 	= "null";

		if($action=="page")
		{
			$this->modules->module_checker(81,'REVIEW');
			//REPOST DATA
			if($txtsearch!='')   	
				$data['txtsearch'] 		=  $txtsearch;
			if($selPOSMType!='')   	
				$data['selPOSMType'] 	=  $selPOSMType;
			if($selPOSMStatus!='')   	
				$data['selPOSMStatus'] 	=  $selPOSMStatus;	
			if($selPremiumType!='')   	
				$data['selPremiumType'] =  $selPremiumType;	
			if($seloutlet!='')   	
				$data['seloutlet'] 		=  $seloutlet;
			if($selCountry!='')   	
				$data['selCountry'] 	=  $selCountry;
			if($selBrand!='')   	
				$data['selBrand'] 		=  $selBrand;
			if($selMaterial!='')   	
				$data['selMaterial'] 	=  $selMaterial;
			if($items_date!='')   	
				$data['items_date'] 	=  $items_date;
			if($nviews!='')   	
				$data['nviews'] 		=  $nviews;
			if($sort_by_price!='')   	
				$data['sort_by_price'] 	=  $sort_by_price;
			if($priceRange!='')   	
				$data['priceRange'] 	=  $priceRange;
			if($priceFrom!='')   	
				$data['priceFrom'] 		=  $priceFrom;
			if($priceTo!='')   	
				$data['priceTo'] 		=  $priceTo;
			if($year!='')   	
				$data['year'] 			=  $year;
			if($month!='')   	
				$data['month'] 			=  $month;
			if($priceRangeID!='')   	
				$data['priceRangeID'] 	=  $priceRangeID;
		}
		
		
		//SEARCH ACTION
		$data['url']  = $data['txtsearch']."/".$data['selPOSMType']."/".$data['selPOSMStatus']."/".$data['selPremiumType']."/".$data['seloutlet']."/".$data['selCountry']."/".$data['selBrand']."/".$data['selMaterial'].'/';
		$data['url'] .= $data['items_date']."/".$data['nviews']."/".$data['sort_by_price']."/".$data['priceRange']."/".$data['priceFrom']."/".$data['priceTo']."/".$data['year']."/".$data['month']."/".$data['priceRangeID']; 
		$data['searchAction'] = HTTP_PATH. "gallery/redirect_link/".$data['redirectTo']."/page/1/".$data['url']; 
		
		if($this->modules->access_checker()==TRUE)
	    {
			$this->load->view('galleryHeader',$data); 
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
	
	function my_Gallery($action='',$id='',$txtsearch='',$selPOSMType='',$selPOSMStatus='',$selPremiumType='',$seloutlet='',$selCountry='',$selBrand='',$selMaterial='',$items_date='',$nviews='',$sort_by_price='',$priceRange='',$priceFrom='',$priceTo='',$year='',$month='',$priceRangeID='')
	{ 
		$this->modules->session_handler();
		$this->modules->module_checker(35,'REVIEW');
		
	    $filter = $this->modules->country2();
		
	    $table= "items";
		$data['vfile']				= 'my_Gallery.php';
	    $data['title']				= 'Items | San Miguel Brewing International';
	    $data['galTitle']			= 'My Gallery';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia'; 
		
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(2);
		
		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
		$data['breadCrumbs']	= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']   .= '<a href='.$HTTP_PATH.'gallery/my_Gallery> My Gallery </a>';
		$data['csrf'] 			= "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>"; 
		$data['searchAction']   = HTTP_PATH."gallery/my_Gallery"; 
		
		//print_r($_POST);
		$data['post'] = $_POST; $filter2="";
		
		$order = "COUNT(item_views.itemID) DESC, items.dateReleased DESC,";
        
		$data['redirectTo']	= "my_Gallery";
        if(($txtsearch!='' OR $selPOSMType!='' OR $selPOSMStatus!='' OR $selPremiumType!='' OR $seloutlet!='' OR $selCountry!='' OR $selBrand!='' OR $selMaterial!='' OR $items_date=!'' OR $nviews!='' OR $sort_by_price!='' OR $priceRange!='' OR $priceRangeID!=''))		
		{ 
			extract($_POST);  
			//$_SESSION['txtsearch']="";
              if($selPOSMType!='null' 	 AND $selPOSMType!='')     	$filter2 .= " AND POSMTypeID		='$selPOSMType'";
              if($selPOSMStatus!='null'  AND $selPOSMStatus!='')   	$filter2 .= " AND POSMStatusID		='$selPOSMStatus'";
			  if($selPremiumType!='null' AND $selPremiumType!='')  	$filter2 .= " AND PremiumTypeID		='$selPremiumType'";
			  if($seloutlet!='null' 	 AND $seloutlet!='')       	$filter2 .= " AND OUTLETStatusID	='$seloutlet'";
			  if($selCountry!='null'	 AND $selCountry!='')      	$filter2 .= " AND countryID			='$selCountry'";
			  if($selBrand!='null' 		 AND $selBrand!='')        	$filter2 .= " AND brandID 			='$selBrand'";
			  if($selMaterial!='null' 	 AND $selMaterial!='')     	$filter2 .= " AND MaterialTypeID	='$selMaterial'";
			  if($priceRangeID!='null'   AND $priceRangeID!='')    $filter2 .= " AND price_rangeID	    ='$priceRangeID'";
			  
			  if($txtsearch!='' AND $txtsearch!='null'){
				$txtsearch = addslashes($txtsearch);
				$txtsearch = trim($txtsearch);
				$txtsearch = str_replace("%20"," ",$txtsearch);
				$filter2 .= " AND ( itemCode like  '%$txtsearch%'  or itemName like  '%$txtsearch%'  or Short_Description like '%$txtsearch%' or Long_Description like '%$txtsearch%')";
			  }   
			  

			  //PRICE RANGE	
			  if(($priceRange!='' AND $priceRange!='null') AND is_numeric($priceFrom) AND is_numeric($priceTo))
					$filter2 = "AND $priceRange >= $priceFrom AND $priceRange <= $priceTo ";
			  
			  
			  //DATE RANGE
			  $m=0;
			  if($year!='' AND $year!='null')
				$m = $year * 12;  
			  if($month!='' AND $month!='null')
				$m += $month;
			  
			  if(($year!='' AND $year!='null' AND $year!=0)  OR ($month!='' AND $month!='null' AND $month!=0)){
				$filter2 .= " AND items.dateAdded <= CURDATE() AND items.dateAdded >= (SELECT CURDATE() - INTERVAL $m MONTH) ";
			  }
			  
			  
			   //SORT BY DATE
			   if(($nviews!='' AND $nviews!='null') OR ($sort_by_price!='' AND $sort_by_price!='null') OR ($items_date!=''  AND $items_date!='null'))
					$order = "";
			 
			   if($nviews!='null' 	 	 AND $nviews!='')          $order  .= " COUNT(item_views.itemID) $nviews,";
			   if($sort_by_price!='null' AND $sort_by_price!='')   $order  .= str_replace("-"," ",$sort_by_price.","); 
			   if($items_date!='null' 	 AND $items_date!='')      $order  .= " items.dateReleased $items_date,"; 	  
		} 
		$order = substr($order, 0,-1);
		
		if($filter=='' & $filter2!='') $filter = " AND ". substr($filter2,4);
		else $filter = "$filter   $filter2"; 
		
		$sqlSTr="SELECT *,
				OUTLET_Status.statusName as OutletStatusName, 
				POSM_Status.statusName as POSMStatusName,
				POSM_Type.typeName as POSM_TypeName,
				items.id as itemID, count(item_views.itemID) as iViews  
				FROM items 
				LEFT JOIN POSM_Type ON items.POSMTypeID = POSM_Type.id 
				LEFT JOIN POSM_Status ON items.POSMStatusID = POSM_Status.id 
				LEFT JOIN OUTLET_Status ON items.OUTLETStatusID = OUTLET_Status.id
				LEFT JOIN premiumItemType ON items.PremiumTypeID = premiumItemType.id 
				LEFT JOIN MATERIAL_Type ON items.MaterialTypeID = MATERIAL_Type.id 
				LEFT JOIN country ON items.countryID = country.id 
				LEFT JOIN brands  ON items.brandID = brands.id
				LEFT JOIN item_views  		ON items.id = item_views.itemID
				WHERE publish ='y' $filter 
				AND items.purge='n' AND items.archive='n' AND DATEDIFF(CURDATE(),items.dateReleased) < ".$this->modules->itemsAge()."
				GROUP BY items.id ORDER BY $order ";
		
		
		//TOTAL NUMBER OF ROWS
		$data['active_page']= 1;
		if($id!='') 
			$data['active_page']= $id;
		
		$sql = $this->db->query($sqlSTr);
		$sql = $sql->result_array();
		$total_rec = count($sql);

		$data['total_rec'] = $total_rec;
		$pagenum = $data['active_page'];
		$data['page_rows'] = 15; 
		$data['last'] = ceil($total_rec/$data['page_rows']);		
		$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		
		//NEW FUNCTION
		$url="";
		$data['txtsearch'] 		= "null";
		$data['selPOSMType'] 	= "null";
		$data['selPOSMStatus'] 	= "null";
		$data['selPremiumType'] = "null";
		$data['seloutlet'] 		= "null";
		$data['selCountry'] 	= "null";
		$data['selBrand'] 		= "null";
		$data['selMaterial'] 	= "null";
		$data['items_date'] 	= "null";
		$data['nviews'] 		= "null";
		$data['sort_by_price'] 	= "null";
		$data['priceRange'] 	= "null";
		$data['priceFrom'] 		= "null";
		$data['priceTo'] 		= "null";
		$data['year'] 			= "null";
		$data['month'] 			= "null";
		$data['priceRangeID'] 	= "null";

		if($action=="page")
		{
			$this->modules->module_checker(18,'REVIEW');
			//REPOST DATA
			if($txtsearch!='')   	
				$data['txtsearch'] 		=  $txtsearch;
			if($selPOSMType!='')   	
				$data['selPOSMType'] 	=  $selPOSMType;
			if($selPOSMStatus!='')   	
				$data['selPOSMStatus'] 	=  $selPOSMStatus;	
			if($selPremiumType!='')   	
				$data['selPremiumType'] =  $selPremiumType;	
			if($seloutlet!='')   	
				$data['seloutlet'] 		=  $seloutlet;
			if($selCountry!='')   	
				$data['selCountry'] 	=  $selCountry;
			if($selBrand!='')   	
				$data['selBrand'] 		=  $selBrand;
			if($selMaterial!='')   	
				$data['selMaterial'] 	=  $selMaterial;
			if($items_date!='')   	
				$data['items_date'] 	=  $items_date;
			if($nviews!='')   	
				$data['nviews'] 		=  $nviews;
			if($sort_by_price!='')   	
				$data['sort_by_price'] 	=  $sort_by_price;
			if($priceRange!='')   	
				$data['priceRange'] 	=  $priceRange;
			if($priceFrom!='')   	
				$data['priceFrom'] 		=  $priceFrom;
			if($priceTo!='')   	
				$data['priceTo'] 		=  $priceTo;
			if($year!='')   	
				$data['year'] 			=  $year;
			if($month!='')   	
				$data['month'] 			=  $month;
			if($priceRangeID!='')   	
				$data['priceRangeID'] 	=  $priceRangeID;
		}
		
		
		//SEARCH ACTION
		$data['url']  = $data['txtsearch']."/".$data['selPOSMType']."/".$data['selPOSMStatus']."/".$data['selPremiumType']."/".$data['seloutlet']."/".$data['selCountry']."/".$data['selBrand']."/".$data['selMaterial'].'/';
		$data['url'] .= $data['items_date']."/".$data['nviews']."/".$data['sort_by_price']."/".$data['priceRange']."/".$data['priceFrom']."/".$data['priceTo']."/".$data['year']."/".$data['month']."/".$data['priceRangeID'];
		$data['searchAction'] = HTTP_PATH. "gallery/redirect_link/".$data['redirectTo']."/page/1/".$data['url']; 
		
		
		$sql = $this->db->query($sqlSTr ." ". $max);
		$data['items'] = $sql->result_array();

		
		if($this->modules->access_checker()==TRUE)
	    {
			$this->load->view('galleryHeader',$data); 
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
 
	function itemInfo($id)
    {
	   $data['id'] 	         = $id;
	   $sql 			     = $this->db->query("SELECT * FROM items_images WHERE itemID = $id ORDER BY defaultStatus DESC");
	   $data['items_images'] = $sql->result_array();
	   $sqlSTr="SELECT *,
				OUTLET_Status.statusName as OutletStatusName, 
				POSM_Status.statusName as POSMStatusName,
				items.id as itemID 
				FROM items  
				LEFT JOIN POSM_Type ON items.POSMTypeID = POSM_Type.id 
				LEFT JOIN POSM_Status ON items.POSMStatusID = POSM_Status.id 
				LEFT JOIN OUTLET_Status ON items.OUTLETStatusID = OUTLET_Status.id
				LEFT JOIN premiumItemType ON items.PremiumTypeID = premiumItemType.id 
				LEFT JOIN MATERIAL_Type ON items.MaterialTypeID = MATERIAL_Type.id 
				LEFT JOIN country ON items.countryID = country.id 
				LEFT JOIN brands  ON items.brandID = brands.id  
				LEFT JOIN countries  ON items.country_of_origin = countries.id  
				where items.id='$id'";
	   $sql 			     = $this->db->query($sqlSTr);
	  
	   $item = $sql->result_array();
	  
	   $data['item'] 			 = $item;
	   $data['vfile']	     	 = 'itemInfo.php';
	   $data['galTitle']	     = isset($item[0]['itemName']) ? $item[0]['itemName'] : 'No Title';
	   $itemName				 = isset($item[0]['itemName']) ? $item[0]['itemName'] : 'No Title';
	   
	   $data['breadCrumbs']    = '<li><img src="'. HTTP_PATH .'img/arrow.png" width="3" height="5"></li> '.$itemName;
	   $data['itemPreview']	   = true;
	   
	   
	   $this->load->view('simpleGalleryHeader',$data); 
	}
	
	function itemInfoECatalog($ecID=0,$id=0,$b='')
    {
	   $this->modules->session_handler();
	   $this->modules->module_checker(34,'REVIEW');
	   $data['VENDOR_REVIEW'] =  $this->modules->crud_checker(44,'REVIEW');
	   
	   //UPDATE NUMBER OF VIEWS
	   if($this->modules->viewChecker()==TRUE)
			$this->modules->item_views("ec_items","ecitem_views",$id);
	   
	   $data['jUi'] 	     = true;
	   $data['id'] 	         = $id;
	   $sql 			     = $this->db->query("SELECT * FROM ecitems_images WHERE itemID = $id ORDER BY defaultStatus DESC");
	   $data['items_images'] = $sql->result_array();
	   
	   //ITEM HAS BEEN TRANSFERRED
	   $hasBeenTranferred=0;
	   $sql = $this->db->query("SELECT id FROM itemsTurnOverRef WHERE itemID = $id AND transfer_type='original' AND item_type='ec_item' LIMIT 0,1");
	   $row = $sql->row();
	   if($row->id) $hasBeenTranferred=$row->id;
	   $data['hasBeenTranferred'] = $hasBeenTranferred;

	   $sql = $this->db->query("SELECT *,
								OUTLET_Status.statusName as OutletStatusName, 
								POSM_Status.statusName as POSMStatusName,
								e_catalog.title as e_catalogBrand,
								ec_items.id as itemID,
								admin_users.full_name 	 as fname,
								ec_items.dateAdded 		 as uploaded_Date,
								ec_items.dateReleased 		 as released_Date,
								(SELECT COUNT(id) FROM ecitem_views WHERE ecitem_views.itemID = ec_items.id) as tot_views
								FROM ec_items 
								LEFT JOIN POSM_Type 		ON ec_items.POSMTypeID 		= POSM_Type.id 
								LEFT JOIN POSM_Status 		ON ec_items.POSMStatusID 	= POSM_Status.id 
								LEFT JOIN OUTLET_Status 	ON ec_items.OUTLETStatusID 	= OUTLET_Status.id
								LEFT JOIN premiumItemType 	ON ec_items.PremiumTypeID 	= premiumItemType.id 
								LEFT JOIN MATERIAL_Type 	ON ec_items.MaterialTypeID 	= MATERIAL_Type.id 
								LEFT JOIN country 			ON ec_items.countryID 		= country.id 
								LEFT JOIN countries 		ON ec_items.country_of_origin = countries.id
								LEFT JOIN e_catalog  		ON ec_items.ecID 			= e_catalog.id  
								LEFT JOIN admin_users  		ON ec_items.user_id = admin_users.id 
								WHERE ec_items.ID='$id'");
	
	   //BREAD CRUMBS
	   $data['breadCrumbs']	   = '<li><img src="'. HTTP_PATH .'img/arrow.png" width="3" height="5"></li> ';
	   $data['breadCrumbs']   .= '<a href='. HTTP_PATH .'gallery/eCatalog> eCatalogue </a>';
	   $data['breadCrumbs']	  .= '<li><img src="'. HTTP_PATH .'img/arrow.png" width="3" height="5"></li> ';	
	   
	 
	  
	   switch($b){
		case 'eCITEMS':
			$data['breadCrumbs']   .= '<a href='. HTTP_PATH .'eCatalog/eCatalog_Items_Menu> eCatalogue items </a>';
		break;
		case '':
			//Ecatalog name
			$sql2 = $this->db->query("SELECT title FROM e_catalog WHERE id = '$ecID'");
			$row = $sql2->row();
			$data['breadCrumbs']   .= '<a href="'.HTTP_PATH.'gallery/eCatalog/view/'.$ecID.'"> '.$row->title.' </a>';
		break;
	   }
	   
	   $item = $sql->result_array();
	   //print_r($item);
	   $data['item'] = $item;
	   $data['vfile']	     	 = 'itemInfoEcatalog.php';
	   $data['galTitle']	     = $item[0]['itemName'];
	   $itemName				 = $item[0]['itemName'];
	   $data['itemPreview']		 = true;
	   
	   $sql = $this->db->query("SELECT *, ec_vendors.id as vID FROM ecitemVendorsRef 
							  LEFT JOIN ec_vendors ON ec_vendors.id = ecitemVendorsRef.vendorID 
							  WHERE ecitemVendorsRef.itemID = $id");
	   $data['vendors'] = $sql->result_array();
	   
	    $sql = $this->db->query("SELECT full_name, transfer_type, date FROM itemsTurnOverRef
								LEFT JOIN admin_users ON admin_users.id = itemsTurnOverRef.userID
								WHERE itemID = $id AND item_type = 'ec_item' ORDER BY transfer_type ASC, date_time DESC");
	   $data['item_logs'] = $sql->result_array();
	   
	    if($this->modules->access_checker()==TRUE)
	    {
			$this->load->view('galleryHeader_Item_info',$data); 
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
	
	function itemZoom()
    {
	   if($_POST)
	   {extract($_POST);
		$_SESSION['previewItemID'] = $itemID;
		$_SESSION['previewItemIMG'] = $imgSrc;
	   }
	   
	   $sql     = $this->db->query("SELECT *,
					OUTLET_Status.statusName as OutletStatusName, 
					POSM_Status.statusName as POSMStatusName,
					items.id as itemID 
					FROM items 
					LEFT JOIN POSM_Type ON items.POSMTypeID = POSM_Type.id 
					LEFT JOIN POSM_Status ON items.POSMStatusID = POSM_Status.id 
					LEFT JOIN OUTLET_Status ON items.OUTLETStatusID = OUTLET_Status.id
					LEFT JOIN premiumItemType ON items.PremiumTypeID = premiumItemType.id 
					LEFT JOIN MATERIAL_Type ON items.MaterialTypeID = MATERIAL_Type.id 
					LEFT JOIN country ON items.countryID = country.id 
					LEFT JOIN countries  ON items.country_of_origin = countries.id  
					LEFT JOIN brands  ON items.brandID = brands.id  WHERE items.ID='".$_SESSION['previewItemID']."'");
				
	   $data['breadCrumbs']	 = '';
	   $item 					 = $sql->result_array();
	   //print_r($item);
	   $data['item'] 			 = $item;
	   $data['vfile']	     	 = 'itemZoom.php';
	   $data['galTitle']	     = $item[0]['itemName'];
	   $itemName				 = $item[0]['itemName'];
	   $data['itemPreview']		 = true;
	   $data['selImg']			 =  $_SESSION['previewItemIMG'];
	   $this->load->view('itemZoom.php',$data);  
	}
		
	function itemZoomVoting()
    {
	   if($_POST)
	   {extract($_POST);
		$_SESSION['preview_votingItemID'] = $itemID;
		$_SESSION['preview_votingItemIMG'] = $imgSrc;
	   }
	   
	   $sql     = $this->db->query("SELECT *,
					OUTLET_Status.statusName as OutletStatusName, 
					POSM_Status.statusName as POSMStatusName,
					items.id as itemID 
					FROM items 
					LEFT JOIN POSM_Type ON items.POSMTypeID = POSM_Type.id 
					LEFT JOIN POSM_Status ON items.POSMStatusID = POSM_Status.id 
					LEFT JOIN OUTLET_Status ON items.OUTLETStatusID = OUTLET_Status.id
					LEFT JOIN premiumItemType ON items.PremiumTypeID = premiumItemType.id 
					LEFT JOIN MATERIAL_Type ON items.MaterialTypeID = MATERIAL_Type.id 
					LEFT JOIN country ON items.countryID = country.id 
					LEFT JOIN countries  ON items.country_of_origin = countries.id  
					LEFT JOIN brands  ON items.brandID = brands.id  WHERE items.ID='".$_SESSION['preview_votingItemID']."'");
					
	   $data['breadCrumbs']	 = '';
	   $item 					 = $sql->result_array();
	   //print_r($item);
	   $data['item'] 			 = $item;
	   $data['vfile']	     	 = 'itemZoomVoting.php';
	   $data['galTitle']	     = $item[0]['itemName'];
	   $itemName				 = $item[0]['itemName'];
	   $data['itemPreview']		 = true;
	   $data['selImg']			 = $_SESSION['preview_votingItemIMG'];

	   $this->load->view('itemZoomVoting.php',$data);  
	}
	
	function itemZoom2()
    {   
	   if($_POST)
	   {extract($_POST);
		$_SESSION['preview_ecItemID'] = $itemID;
		$_SESSION['preview_ecItemIMG'] = $imgSrc;
	   }
	   
	   $sql     = $this->db->query("SELECT *,
				OUTLET_Status.statusName as OutletStatusName, 
				POSM_Status.statusName as POSMStatusName,
				e_catalog.title as e_catalogBrand,
				ec_items.id as itemID 
				FROM ec_items 
				LEFT JOIN POSM_Type 		ON ec_items.POSMTypeID  		= POSM_Type.id 
				LEFT JOIN POSM_Status 		ON ec_items.POSMStatusID 		= POSM_Status.id 
				LEFT JOIN OUTLET_Status 	ON ec_items.OUTLETStatusID 		= OUTLET_Status.id
				LEFT JOIN premiumItemType 	ON ec_items.PremiumTypeID 		= premiumItemType.id 
				LEFT JOIN MATERIAL_Type 	ON ec_items.MaterialTypeID 		= MATERIAL_Type.id 
				LEFT JOIN country 			ON ec_items.countryID 			= country.id 
				LEFT JOIN countries  		ON ec_items.country_of_origin 	= countries.id
				LEFT JOIN e_catalog  		ON ec_items.ecID 			= e_catalog.id   WHERE ec_items.ID='".$_SESSION['preview_ecItemID'] ."'");
					
	   //$data['items_images'] = $sql->result_array();
	   //$sql 			     = $this->db->query("SELECT * FROM items WHERE id = $id");
	   $data['breadCrumbs']	 = '';
	   $item 					 = $sql->result_array();
	   $data['item'] 			 = $item;
	   $data['vfile']	     	 = 'itemZoomECItem.php';
	   $data['galTitle']	     = $item[0]['itemName'];
	   $itemName				 = $item[0]['itemName'];
	   $data['itemPreview']		 = true;
	   $data['selImg']			 = $_SESSION['preview_ecItemIMG'];

	   $this->load->view('itemZoomECItem.php',$data);  
	} 
	
	function itemInfo2($id,$gal='',$cID='')
    {
	   $this->modules->session_handler();
	   
	   //UPDATE NUMBER OF VIEWS
	   if($this->modules->viewChecker()==TRUE)
		   $this->modules->item_views("items","item_views",$id);
	   
	   $data['VENDORS_REVIEW'] = $this->modules->crud_checker(26,'REVIEW');
	   
	   $data['jUi'] 	     = true;
	   $data['id'] 	         = $id;
	   $sql 			     = $this->db->query("SELECT * FROM items_images WHERE itemID = $id ORDER BY defaultStatus DESC");
	   $data['items_images'] = $sql->result_array();

	   $sqlSTr="SELECT *,
				OUTLET_Status.statusName as OutletStatusName, 
				POSM_Status.statusName   as POSMStatusName,
				items.id 				 as itemID,  
				admin_users.full_name 	 as fname,
				items.dateAdded as uploaded_Date,
				items.dateReleased as released_Date,
				(SELECT COUNT(id) FROM item_views WHERE item_views.itemID = items.id) as tot_views
				FROM items  
				LEFT JOIN POSM_Type 		ON items.POSMTypeID = POSM_Type.id 
				LEFT JOIN POSM_Status 		ON items.POSMStatusID = POSM_Status.id 
				LEFT JOIN OUTLET_Status 	ON items.OUTLETStatusID = OUTLET_Status.id
				LEFT JOIN premiumItemType 	ON items.PremiumTypeID = premiumItemType.id 
				LEFT JOIN MATERIAL_Type 	ON items.MaterialTypeID = MATERIAL_Type.id 
				LEFT JOIN country 			ON items.countryID = country.id 
				LEFT JOIN brands  			ON items.brandID = brands.id  
				LEFT JOIN countries  		ON items.country_of_origin = countries.id  
				LEFT JOIN admin_users  		ON items.user_id = admin_users.id 
				LEFT JOIN price_range  		ON items.price_rangeID = price_range.id 
				where items.id='$id'";
	   $sql 			     = $this->db->query($sqlSTr);
	  
	   $item = $sql->result_array();
	  
	   $data['item'] 			 = $item;
	   $data['vfile']	     	 = 'itemInfo2.php';
	   $data['galTitle']	     = isset($item[0]['itemName']) ? $item[0]['itemName'] : 'No Title';
	   $itemName				 = isset($item[0]['itemName']) ? $item[0]['itemName'] : 'No Title';
	   $data['breadCrumbs']		 = "";
	   
	   //ITEM HAS BEEN TRANSFERRED
	   $hasBeenTranferred=0;
	   $sql = $this->db->query("SELECT id FROM itemsTurnOverRef WHERE itemID = $id AND transfer_type='original' AND item_type='item_db' LIMIT 0,1");
	   $row = $sql->row();
	   if($row->id) $hasBeenTranferred=$row->id;
	   $data['hasBeenTranferred'] = $hasBeenTranferred;
	   
	   //SELECT TURN OVER REF
	   $sql = $this->db->query("SELECT full_name, transfer_type, date FROM itemsTurnOverRef
								LEFT JOIN admin_users ON admin_users.id = itemsTurnOverRef.userID
								WHERE itemID = $id AND item_type = 'item_db' ORDER BY transfer_type ASC, date_time DESC");
	   $data['item_logs'] = $sql->result_array();
	   //RESTORE FROM PURGING
	   $sql = $this->db->query("SELECT full_name, tdate FROM logs 
								LEFT JOIN items 	  ON items.id 	   = logs.rec_id
								LEFT JOIN admin_users ON logs.user_id = admin_users.id
								WHERE action = 'restore from disk' AND items.id = $id
								");
	   $data['restore_history'] = $sql->result_array();
	   //RESTORE FROM ARCHIVE 
	   $sql = $this->db->query("SELECT full_name, tdate FROM logs 
								LEFT JOIN items 	  ON items.id 	   = logs.rec_id
								LEFT JOIN admin_users ON logs.user_id = admin_users.id
								WHERE action = 'restore from archive' AND items.id = $id
								");
	   $data['archive_history'] = $sql->result_array();
	   //RESTORE FROM PURGING 
	   $sql = $this->db->query("SELECT full_name, tdate FROM logs 
								LEFT JOIN items 	  ON items.id 	   = logs.rec_id
								LEFT JOIN admin_users ON logs.user_id = admin_users.id
								WHERE action = 'restore from purging' AND items.id = $id
								");
	   $data['purging_history'] = $sql->result_array(); 
		switch($gal){
			case 'iR':
				//BREAD CRUMBS
				$sql       = "select campaignName FROM campaign WHERE id='$cID'";
				$header    = $this->db->query($sql);  
				$header    = $header->result_array(); 
				
				$HTTP_PATH 					 = HTTP_PATH."report/iLike";
				$data['breadCrumbs']	     = '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
				$data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report> Reports </a>';
				$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
				$data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/iLike> iLike Report </a>';
				$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
				$data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/iLike_Items/$cID'>". $header[0]['campaignName'] ."</a>";
			break;
			case 'iW':
				//BREAD CRUMBS
				$sql       = "select campaignName FROM campaign WHERE id='$cID'   ";
				$header    = $this->db->query($sql);  
				$header    = $header->result_array(); 
				
				$HTTP_PATH 					 = HTTP_PATH."report/iLike";
				$data['breadCrumbs']	     = '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
				$data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report> Reports </a>';
				$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
				$data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/iWant> iWant Report </a>';
				$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
				$data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/iWant_Items/$cID'>". $header[0]['campaignName'] ."</a>";
			break;
			case 'i':
				$data['breadCrumbs']	= '<li><img src="'. HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
				$data['breadCrumbs']   .= '<a href='. HTTP_PATH.'itemDatabase/items> Item Database </a>';
			break;
			case 'm':
				$data['breadCrumbs']	= '<li><img src="'. HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
				$data['breadCrumbs']   .= '<a href='. HTTP_PATH.'gallery/my_Gallery> My Gallery </a>';
			break;
			case 'c':
				$data['breadCrumbs']	= '<li><img src="'. HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
				$data['breadCrumbs']   .= '<a href='. HTTP_PATH.'gallery/common> Common Gallery </a>';
			break;
			case 'iS':
				$data['breadCrumbs']	= '<li><img src="'. HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
				$data['breadCrumbs']   .= '<a href='. HTTP_PATH.'report/campaign_items_summary> Items Summary </a>';
			break;	
		}
	   
	   $data['breadCrumbs']    .= '<li><img src="'. HTTP_PATH .'img/arrow.png" width="3" height="5"></li> '.$itemName;
	   $data['itemPreview']	   = true;
	   
	   $sql 		  = $this->db->query("SELECT *, vendors.id as vID, (SELECT countryName FROM country WHERE country.id = vendors.countryID) AS cName FROM itemVendorsRef 
										  LEFT JOIN vendors ON vendors.id = itemVendorsRef.vendorID 
										  WHERE itemVendorsRef.itemID = $id");
	   $data['vendors'] = $sql->result_array();
	   
	    if($this->modules->access_checker()==TRUE)
	    {
			$this->load->view('galleryHeader_Item_info',$data); 
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
	
	function itemEC($id)
    {
	   $this->modules->session_handler();
	   $data['id'] 	         = $id;
	   $sql 			     = $this->db->query("SELECT * FROM ecitems_images WHERE itemID = $id");
	   $data['items_images'] = $sql->result_array();
	   $sqlSTr="SELECT *,
				OUTLET_Status.statusName as OutletStatusName, 
				POSM_Status.statusName as POSMStatusName,
				ec_items.id as itemID 
				FROM ec_items 
				LEFT JOIN POSM_Type ON ec_items.POSMTypeID = POSM_Type.id 
				LEFT JOIN POSM_Status ON ec_items.POSMStatusID = POSM_Status.id 
				LEFT JOIN OUTLET_Status ON ec_items.OUTLETStatusID = OUTLET_Status.id
				LEFT JOIN premiumItemType ON ec_items.PremiumTypeID = premiumItemType.id 
				LEFT JOIN MATERIAL_Type ON ec_items.MaterialTypeID = MATERIAL_Type.id 
				LEFT JOIN country ON ec_items.countryID = country.id 
				LEFT JOIN brands  ON ec_items.brandID = brands.id  
				where ec_items.publish ='y' and ec_items.id='$id'";
	   $sql 			     = $this->db->query($sqlSTr);
	   $data['breadCrumbs']	 = '';
	   $item = $sql->result_array();
	   $data['item'] = $item;
	   $data['vfile']	     	 = 'itemInfo2.php';
	   $data['galTitle']	     = $item[0]['itemName'];
	   $itemName				 = $item[0]['itemName'];
	   
	   
	   $this->load->view('galleryHeader',$data);  
	}
    
	function myEval($tot,$rel,$val)
	{
	    if($rel=="==") return ($tot == $val);
	    if($rel==">") return  ($tot > $val);
	    if($rel=="<") return  ($tot < $val);
	    if($rel=="<=") return ($tot <= $val);
	    if($rel==">=") return ($tot >= $val);
	}
	  
   function fieldValue($fieldName,$fieldID)
   {
	      if ($fieldName=='POSMTypeID') $sql     = $this->db->query("select typeName as fname from  POSM_Type  where id='$fieldID'  ");
		  if ($fieldName=='POSMStatusID') $sql     = $this->db->query("select statusName as fname  from POSM_Status where id='$fieldID' ");
		  if ($fieldName=='OUTLETStatusID') $sql     = $this->db->query("select statusName as fname  from OUTLET_Status where id='$fieldID'");
		  if ($fieldName=='brandID') $sql     = $this->db->query("select brandName as fnamefrom brands where id='$fieldID'");
		  if ($fieldName=='MaterialTypeID')$sql     = $this->db->query("select materialName from MATERIAL_Type where id='$fieldID' ");
		  if ($fieldName=='PremiumTypeID')$sql     = $this->db->query("select premiumTypeName from premiumItemType where id='$fieldID'");
		  $sql = $sql->result_array(); 
          return $sql[0]['fname'];		  
	   }
	
	 
	function encode_base64($sData){
		$sBase64 = base64_encode($sData);
		return str_replace('=', '', strtr($sBase64, '+/', '-_'));
	}

	function decode_base64($sData){
		$sBase64 = strtr($sData, '-_', '+/');
		return base64_decode($sBase64.'==');
	}
	
	function test($id,$email)
	  {
	  ////echo $id;
	   echo  "http://42.61.55.70/smbi_test/gallery/voting/". $this->encode_base64($id) . "/" .$this->encode_base64($email).".html";
	  }
	   
} ?>