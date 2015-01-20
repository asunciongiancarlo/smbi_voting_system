<?php
     date_default_timezone_set('UTC');
     include('conn.php');
	 
	 $sql = "select * from campaign where campaignType='iLike' and status ='on progress'";
	 $iLikes= $conn->Execute($sql);


Echo "processing ilike:"	; 
	 while(!$iLikes->EOF)
	   {
	      extract($iLikes->fields);
	      $cID = $id;
		    
	      $sql =  "select count(id) as tot from voters where campaignID='$cID'";
		  $voters = $conn->Execute($sql);
		  $totVoters = $voters->fields['tot'];
		  
		  $sql =  "select count(id) as tot from voters where campaignID='$cID' and votingStatus='done'";
		  $done = $conn->Execute($sql);
		 $totDone =  $done->fields['tot'];
		  /// if all has been vote, even if the duration is end yet end
		  if($totDone == $totVoters and $status='on progress')
		    {
			  
		        echo "$id.";
			    $condition="";
		        $sqlRules = "select * from iLikeCanvassingRulesXref where campaignID='$cID' and countryID='$countryID'";
				$rules = $conn->Execute($sqlRules);
				while(!$rules->EOF)
				    {
					  extract($rules->fields);
					  $rel = $rel=="=="?"=":"$rel";
					  if($condition=="")
					     $condition .= "totalVote  $rel '$val'";
					  else
					     $condition .= " $lrel totalVote  $rel '$val'";
					  $rules->moveNext(); 
				    }

			     $sqlVotes = "SELECT *  from TotalVotes where  (campaignID='$cID' and countryID='$countryID')  and ( $condition)";
			    $votes = $conn->Execute($sqlVotes);
				  $tdate = date('Y-m-d');
				if($votes->recordCount()>0)
				  {
				 
				   //echo "update campaign set status='done',dateProcess='$tdate' where id='$cID'";
				   $conn->Execute("update campaign set status='done',dateProcess='$tdate' where id='$cID'");
				   
				   while(!$votes->EOF)
				   {
				     $itemID = $votes->fields['itemID'];
				     $tot = $votes->fields['totalVote'];
				     $sql ="insert into `iLikeResultRef`(campaignID,itemID,totvote) values('$cID','$itemID','$tot') ";
					 $conn->Execute($sql);
					 $votes->moveNext();
				   }
				  }  
				 else
				    $conn->Execute("update campaign set remarks='condition does not meet', status='failed',dateProcess='$tdate' where id='$cID'");   
			}
			 /// END  if all has been vote, even if the duration is end yet end
		   // duration end
		   //$dateTo
		 $iLikes->moveNext();
	   }
	   
	/// iwant    
	   
	   $sql = "select * from campaign where campaignType='iWant' and status ='on progress'";
	   $iWant= $conn->Execute($sql);
Echo "processing iwant:"	; 
	 while(!$iWant->EOF)
	   {
	      extract($iWant->fields);
	      $cID = $id;
		  
	      $sql =  "select count(id) as tot from voters where campaignID='$cID'";
		  $voters = $conn->Execute($sql);
		  $totVoters = $voters->fields['tot'];
		  
		  $sql =  "select count(id) as tot from voters where campaignID='$cID' and votingStatus='done'";
		  $done = $conn->Execute($sql);
		  $totDone =  $done->fields['tot'];
		  /// if all has been vote, even if the duration is end yet end
		  if($totDone == $totVoters and $status='on progress')
		    {
			    
		      echo "$id.";
			    $condition="";
		         $sqlRules = "select * from iWantCanvassingRulesRef where campaignID='$cID' and countryID='$countryID'";
				$rules = $conn->Execute($sqlRules);
				while(!$rules->EOF)
				    {
					  extract($rules->fields);
					  $rel = $rel=="=="?"=":"$rel";
					  if($condition=="")
					     $condition .= "totalVote  $rel $val";
					  else
					     $condition .= " $lrel totalVote  $rel $val";
					  $rules->moveNext(); 
				    }
       
			    $sqlVotes = "SELECT campaignID ,itemID,sum(vote)  as totalVote FROM `votexRef` WHERE (vote!='yes' and vote!='no')
                             and campaignID='$cID'group by itemID  ";
			    $votes = $conn->Execute($sqlVotes);
				echo "<br>";
				echo  $condition;
				echo "<br>";
				if($votes->recordCount()>0)
				  {
				   $thereIsResult=0; 
				   while(!$votes->EOF)
				   {
				     $itemID = $votes->fields['itemID'];
				     $tot1    = round($votes->fields['totalVote'] / $totVoters,2) ;
					 $condition1= str_replace("totalVote",$tot1,$condition);
					 $rs = $conn->Execute("select ($condition1) as res");
					 
					  
					 if($rs->fields['res']>0) 
					 {
						$sql ="insert into `iWantResultRef`(campaignID,itemID,totvote) values('$cID','$itemID','$tot1') ";
                        $conn->Execute($sql);
						$thereIsResult++;
					 }
	                 
					 $votes->moveNext();
					 echo "<br>";
				   }
				   $tdate = date('Y-m-d');
				   if($thereIsResult>0)
				      $conn->Execute("update campaign set status='done',dateProcess='$tdate' where id='$cID'");
				   else
				      $conn->Execute("update campaign set remarks='Condition does not meet',status='failed',dateProcess='$tdate' where id='$cID'");
				  }
			}
			 /// END  if all has been vote, even if the duration is end yet end
		   // duration end
		   //$dateTo
		 $iWant->moveNext();
	   }
	   
	   

   echo ".DONE";
	  
	  function myEval($tot,$rel,$val)
	  {
		 if($rel=="==") return ($tot == $val);
		 if($rel==">") return  ($tot > $val);
		 if($rel=="<") return  ($tot < $val);
		 if($rel=="<=") return ($tot <= $val);
		 if($rel==">=") return ($tot >= $val);
	  }
		
    		
	   
	  
?>