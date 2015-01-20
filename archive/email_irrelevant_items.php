<?php
include("conn.php");
set_time_limit(0);
date_default_timezone_set("UTC");

include("smtpmail/library.php");
include("smtpmail/classes/class.phpmailer.php");

$sql   = "SELECT *, admin_users.id as user_id FROM admin_users";
$users = $conn->Execute($sql);

foreach($users as $u)
{ extract($u);
  if(items($user_id,$conn)){
		$msg = "<label style='font-size:19px;font-family:Arial,Helvetica,sans-serif;color:#9e0b0f;'>San Miguel Brewing, International, LTD. </label><br/>
				<label style='font-size:12px;font-family:Arial,Helvetica,sans-serif;color:#777777;border-color: gray;'>Hi $full_name!<br/></label>
				<label style='font-size:12px;font-family:Arial,Helvetica,sans-serif;color:#777777;border-color: gray;'>Please review the follwing items because it is being tagged as irrelevant item. <br/>Date: ". date('Y-m-d') ."</label><br/>
				<br/>";
		$msg .= items($user_id,$conn);
		sendEmail($email_address, $msg, $full_name);
		echo items($user_id,$conn);
	}
}


function sendEmail($email_address, $msg, $full_name)
{
	$email = $email_address;
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
    $mail->Subject = "New Items in Item Database ".date('Y-m-d'); 							//Subject od your mail
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


function items($user_id,$conn)
{
$dayToday = date('Y-m-d');

$sqlSTr =  "SELECT *,
			POSM_Type.typeName 		as POSM_TypeName,
			country.countryName 	as cName,
			admin_users.full_name 	as fullName
			FROM items 
			LEFT JOIN POSM_Type 			   ON  items.POSMTypeID = POSM_Type.id 
			LEFT JOIN country 				   ON  items.countryID  = country.id 
			LEFT JOIN admin_users			   ON  items.user_id    = admin_users.id
			WHERE items.dateLastEdited		   ='$dayToday'  AND 	user_id=$user_id  AND irrelevant='y'
			ORDER BY items.id DESC";
$items = $conn->Execute($sqlSTr);

$m = "";
$msg = "";

	if($items->RecordCount()>0)
	{			
		$c = "";
        $pCountry="";		
		foreach($items as $r)
		{extract($r);
			//COUNTYR NAME
			if(($c=="" OR $c != $cName) AND $pCountry!=$cName){
				$msg .= "<label style='font-size:16px;font-family:Arial,Helvetica,sans-serif;color:#777777;'> <b>Country Name: $cName </b></label><br/>";
				$c    = $cName;
			}
			$pCountry = $cName;
		}
		
		$msg .="<table style='font-size:12px;font-family:Arial,Helvetica,sans-serif;color:#777777;border-color: gray;text-align: center;'>
				<tr> 
					<th style='width:50px;color:black;font-weight: bold;background:#FCD9D9'> No 	 	 	 </th>
					<th style='width:100px;color:black;font-weight: bold;background:#FCD9D9'> Item Code   	 </th>
					<th style='width:100px;color:black;font-weight: bold;background:#FCD9D9'> Item Type   	 </th>
					<th style='width:190px;color:black;font-weight: bold;background:#FCD9D9'> Record Name	 </th>
					<th style='width:100px;color:black;font-weight: bold;background:#FCD9D9'> Publish	 	 </th>
					<th style='width:100px;color:black;font-weight: bold;background:#FCD9D9'> Irrelevant	 </th>
					<th style='width:150px;color:black;font-weight: bold;background:#FCD9D9'> Uploaded  By   </th>
				</tr>";
		
		$x=0;
		foreach($items as $r)
		{
		$cls = (($x++)%2) != 0 ? "style='background:#f9ebeb'" :  ""; 
		extract($r);
		$POSM_TypeName = ($POSM_TypeName=='') ?  '-' : $POSM_TypeName; 	  
		$publish 	   = ($publish=='n') 	  ? 'No' : 'Yes'; 	  
		$irrelevant    = ($irrelevant=='n') 	  ? 'No' : 'Yes'; 	  
		$msg .= "<tr> 
					<td $cls> $x  	  		  		  </td> 
					<td $cls> $itemCode  	  		  </td> 
					<td $cls> $POSM_TypeName 		  </td> 
					<td $cls> $itemName 	  		  </td>
					<td $cls> $publish 	  		 	  </td>
					<td $cls> $irrelevant 	  		  </td>
					<td $cls> $fullName 	  		  </td> 
				</tr> ";
		}
		
		$msg .= "</table><br/>";
	}
	
	return $msg;
}
?>