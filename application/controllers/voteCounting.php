<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class VoteCounting extends CI_Controller {
 
	public function __construct()
    {
		parent::__construct();
		date_default_timezone_set('UTC');
		session_start();
		$this->load->model('c3model');
		$this->load->library('security');
		$this->output->enable_profiler(FALSE);
    }
    
	   
function iLike()
    {
	 
	 $sql = "select * from campaign where campaignType='iLike' and status ='on progress' ";
	 $query = $this->db->query($sql);
	 $iLikeCampaign = $query->result_array();

	 foreach( $iLikeCampaign as $lc)
	   {
	      $cID = $lc['id'];
		  $sql =  "select * from campaign where id='$cID'";
		  $campaign = $this->db->query($sql);
		  $campaign = $campaign->result_array();
		  extract($campaign[0]);

	      $sql =  "select count(id) as tot from voters where campaignID='$cID'";
		  $totVoters = $this->db->query($sql);
		  $totVoters = $totVoters->result_array();
		  $totVoters = $totVoters[0]['tot'];
		  
		  $sql =  "select count(id) as tot from voters where campaignID='$cID' and votingStatus='done'";
		  $totDone = $this->db->query($sql);
		  $totDone = $totDone->result_array();
		  $totDone = $totDone[0]['tot'];
		  
		  if($totDone == $totVoters and $status='on progress')
		    {
			  $this->countiLike($cID);
			}
	   }
	  echo "Done";
	}
	
private function countiLike($cID)
   {
	 $CI =& get_instance();
	 $CI->load->library('rec_logs');
	
     $sql = "select * from iLIkeVotingRules";
	 $rules = $this->db->query($sql);  
	 $rules = $rules->result_array(); 
	 
	 $sql = "select sum(val) as tot from iLIkeVotingRules";
	 $TOP = $this->db->query($sql);  
	 $TOP = $TOP->result_array(); 
	 
	 $filter = "";$ctr = count($rules);$x=0; $fIDs="";$ff="";
	 foreach($rules as   $r)
	 {
		extract($r); 
	    $fIDs .= " i.$fieldName='$fieldID' or";
		$x++;  
	 }
        $fIDs  = substr($fIDs,0,strlen($fIDs)-2);
	    $sql1 = "SELECT itemID, campaignID,(SELECT COUNT( id ) FROM votexRef AS vref WHERE vref.itemID = itemREF.itemID AND vote =  'yes' and vref.campaignID =   '$cID' ) AS voteTot
                FROM  `campaignItemsXref` AS itemREF LEFT JOIN items AS i ON itemREF.itemID = i.id 
				WHERE itemREF.campaignID =   '$cID' and($fIDs)  ORDER BY  `voteTot` DESC limit 0," .($TOP[0]['tot']+1);
				
	    $sql2 = "SELECT itemID, campaignID, (SELECT COUNT( id ) FROM votexRef AS vref WHERE vref.itemID = itemREF.itemID AND vote =  'yes' and vref.campaignID =   '$cID' ) AS voteTot
                FROM  `campaignItemsXref` AS itemREF LEFT JOIN items AS i ON itemREF.itemID = i.id 
				WHERE itemREF.campaignID =   '$cID' and($fIDs)  ORDER BY  `voteTot` DESC"  ;
  		
		$res = $this->db->query($sql1);
        $resTot = $res->num_rows();		
  		$res = $res->result_array();
        $lastRec    = isset($res[$TOP[0]['tot']-1]['voteTot']) ? $res[$TOP[0]['tot']-1]['voteTot']:"1st";
	    $secondLast = isset($res[$TOP[0]['tot']]['voteTot']) ? $res[$TOP[0]['tot']]['voteTot']:"2nd";
	
	  //LOGS
	  $sqlCn = "SELECT campaignName FROM campaign WHERE id = $cID";
	  $sqlCn = $this->db->query($sqlCn);
	  $sqlCn = $sqlCn->row();
	 
	  if($lastRec==$secondLast or $resTot < $TOP[0]['tot']-1) // tagged as revote
	  {
		  $this->db->where("id",$cID);
		  $fre['status'] = 'revote';
		  $fre['remarks'] =  $lastRec==$secondLast ? "more than top ".$TOP[0]['tot'] :"less than Top".$TOP[0]['tot']  ;
		  $this->db->update("campaign",$fre);
		  $sqlInsertInto = "insert  into iLikeRevotingxRef(itemID,campaignID,totvote)  $sql1 ";
		  $this->db->query($sqlInsertInto); 
		  
		  //LOGS
		  $CI->rec_logs->w($cID,$sqlCn->campaignName,'iLike Campaign','iLike','revote');
      }
	  else if($lastRec!=$secondLast ) // tagged as done
	  {
		$sql1 = "SELECT itemID, campaignID,(SELECT COUNT( id ) FROM votexRef AS vref WHERE vref.itemID = itemREF.itemID AND vote =  'yes' and vref.campaignID =   '$cID' ) AS voteTot
                FROM  `campaignItemsXref` AS itemREF LEFT JOIN items AS i ON itemREF.itemID = i.id 
				WHERE itemREF.campaignID =   '$cID' and($fIDs)  ORDER BY  `voteTot` DESC limit 0," .($TOP[0]['tot']);
		
		$this->db->where("id",$cID);
		 
		$this->db->update("campaign",array('status'=>'done'));
		$sqlInsertInto = "insert  into iLikeResultRef(itemID,campaignID,totvote)  $sql1 ";
		$this->db->query($sqlInsertInto); 
		 
		//LOGS
		$CI->rec_logs->w($cID,$sqlCn->campaignName,'iLike Campaign','iLike','done'); 
     } 
   }
  
function iWant()
    {
	 
	 $sql = "select * from campaign where campaignType='iWant' and status ='on progress' ";
	 $query = $this->db->query($sql);
	 $iLikeCampaign = $query->result_array();

	 foreach( $iLikeCampaign as $lc)
	   {
	      $cID = $lc['id'];
	 
	      $sql =  "select count(id) as tot from voters where campaignID='$cID'";
		  $totVoters = $this->db->query($sql);
		  $totVoters = $totVoters->result_array();
		  $totVoters = $totVoters[0]['tot'];
		  
		  $sql =  "select count(id) as tot from voters where campaignID='$cID' and votingStatus='done'";
		  $totDone = $this->db->query($sql);
		  $totDone = $totDone->result_array();
		  $totDone = $totDone[0]['tot'];
		  
		  if($totDone == $totVoters and $status='on progress')
		    {
			  $this->countWant($cID);
			}
	   }
	   
	  
		
	} 
}