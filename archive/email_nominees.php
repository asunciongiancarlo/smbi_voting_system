<?php
include("conn.php");
set_time_limit(0);
date_default_timezone_set("UTC");

include("smtpmail/library.php");
include("smtpmail/classes/class.phpmailer.php");


//GET ALL IN PROGRESS CAMPAIGNS
$sql 	     = "SELECT *, campaign.id as cID FROM campaign WHERE status='on progress' AND dateTo >= CURDATE()";
$campaigns   = $conn->Execute($sql);
$currentDate = date('Y-m-d');

$remaining_days=0;
$total_days=0;

foreach($campaigns as $campaign)
{ extract($campaign);
  
  //TODAY - DATE TO
  $sql 	= "SELECT DATEDIFF('$DateTo', CURDATE()) as r_d";
  $rd 	= $conn->Execute($sql);
  
  foreach($rd as $rd)
  {	extract($rd);
	$remaining_days = $r_d;
  }
  
  //TOTAL DAYS
  $sql 			= "SELECT DATEDIFF(DateTo, DateFrom) as t_d FROM campaign WHERE campaign.id = $cID";
  $total_days 	= $conn->Execute($sql);
  
  foreach($total_days as $td)
  {	extract($td);
	$total_days = $t_d;
  }
  
  echo "Total: ".$total_days;
  echo "<br/>" ;
 // $remaining_days=4;
  
  
  if($remaining_days>=1)
  {
	echo "Remaining: ".$remaining_days;
	echo "<br/>";
	for($x=1;$x<=3;$x++)
	{
		echo "these dates: ". floor(($total_days/3)*$x)."<br/>";
		if($remaining_days == floor(($total_days/3)*$x)){
			floor(($total_days/3)*$x);
			echo "pwede";
			resendEmail($cID,$conn);
		}
	}
	
  }
  
}

function resendEmail($campaignID='',$conn)
{
	//GET THE CAMPAIGN NAME
	$campaign 	= $conn->execute("SELECT * FROM campaign WHERE id = $campaignID LIMIT 0,1");
	foreach($campaign as $c)
	{	extract($c);
	
		$campaignVotersXref = $conn->execute("SELECT * FROM campaignVotersXref WHERE campaignID = $campaignID");
		
		foreach($campaignVotersXref as $cVxref)
		{
			extract($cVxref);
			$voters 	= $conn->execute("SELECT * FROM voters WHERE id = $voterID AND votingStatus = 'invited' LIMIT 0,1");
			
			foreach($voters as $voter){
				extract($voter);
				//CREATE MESSAGE
				$msg  = "";
				$msg  = "San Miguel Beer International <br/>"; 
				$msg .= "Hello ". $fname ." ". $lname."! <br/>"; 
				$msg .= $campaignType ." Campaign w/c entitled ". $campaignName ."<br/> is now online don't forget to vote, <br/> 
						voting starts from ". $DateFrom ." and end on ". $DateTo ." this is a reminder.<br/>";
				
				if($campaignType=='iLike')		
					$msg .= "Link to campaign: ".HTTP_PATH."/gallery/voting/". encode_base64($campaignID) ."/". encode_base64($email) ;
				else
					$msg .= "Link to campaign: ".HTTP_PATH."/gallery/iWant/". encode_base64($campaignID) ."/". encode_base64($email) ;
				
				echo $msg;
				
				sendEmail($email, $msg, $fname.' '.$lname,$campaignType);
				
			}
		}
	}
}

function encode_base64($sData){
	$sBase64 = base64_encode($sData);
	return str_replace('=', '', strtr($sBase64, '+/', '-_'));
}


function sendEmail($email_address, $msg, $full_name,$campaignType)
{
	echo $email = $email_address;
    $mail  = new PHPMailer; 																 // call the class 
    $mail->IsSMTP(); 
    $mail->Host 	= SMTP_HOST; 															 //Hostname of the mail server
    $mail->Port 	= SMTP_PORT; 															 //Port of the SMTP like to be 25, 80, 465 or 587
    $mail->SMTPAuth = true; 																 //Whether to use SMTP authentication
    $mail->Username = SMTP_UNAME; 															 //Username for SMTP authentication any valid email created in your domain
    $mail->Password = SMTP_PWORD; 															 //Password for SMTP authentication
    $mail->AddReplyTo("do.not.reply@smg.sanmiguel.com.ph", "San Miguel Beer International"); //reply-to address
    $mail->SetFrom("do.not.reply@smg.sanmiguel.com.ph", "San Miguel Beer International"); 	 //From address of the mail
    // put your while loop here like below,
    $mail->Subject = "$campaignType Campaign"; 												//Subject od your mail
    $mail->AddAddress($email, $full_name); 													//To address who will receive this email
    $mail->MsgHTML($msg); 																	//Put your body of the message you can place html code here
    $send = $mail->Send(); 																	//Send the mails
	
    if($send){
        echo '<center><h3 style="color:#009933;">Mail sent successfully</h3></center>';
    }
    else{
        echo '<center><h3 style="color:#FF3300;">Mail error: </h3></center>'.$mail->ErrorInfo;
    }
}


?>