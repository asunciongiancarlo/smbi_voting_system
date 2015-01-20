<?php
function filtering3($conn,$table)
{
	$SMBi_DEV 		= 'SMBi_DEV';
	$SMBi_Archive 	= 'SMBi_Archive';

	$sql  = "SELECT defaultRange, defaultDate FROM archive_filtering LIMIT 0,1";
	$rows = $conn->Execute($sql);
	while(!$rows->EOF)
	{
		$defaultRange 	= $rows->fields['defaultRange'];	
		$defaultDate 	= $rows->fields['defaultDate'];	
		$rows->moveNext();
	}
	
	if($defaultRange==1){
	
		$sql  = "SELECT tyear, tmonth FROM archive_filtering LIMIT 0,1";
		$rows = $conn->Execute($sql);
		
		while(!$rows->EOF)
		{
			$year 	= $rows->fields['tyear'];	
			$month 	= $rows->fields['tmonth'];	
			$rows->moveNext();
		}
		
		$m=0;
		if($year!=0)
			$m = $year * 12;  
		if($month!=0)
			$m += $month;
	  
		$filter = " $SMBi_DEV.$table.dateAdded <= CURDATE() AND $SMBi_DEV.$table.dateAdded >= (SELECT CURDATE() - INTERVAL $m MONTH)";
		
	}else{
	
		$sql  = "SELECT dateFrom, dateTo FROM archive_filtering LIMIT 0,1";
		$rows = $conn->Execute($sql);
		
		while(!$rows->EOF)
		{
			$dateFrom 	= $rows->fields['dateFrom'];	
			$dateTo 	= $rows->fields['dateTo'];	
			$rows->moveNext();
		}
		
		$filter = " $SMBi_DEV.$table.dateAdded <= '$dateFrom' AND $SMBi_DEV.$table.dateAdded >= '$dateTo'";
		
	}
	
	return $filter;
}

/*CAMPAIGN*/
$table = "campaign";
$CAMPAIGN 	= " INSERT INTO $SMBi_Archive.$table (id, campaignType, campaignName, DateAdded, DateLastEdited, DatePublished,
			    DateFrom, DateTo, adminCreatorID, adminLastEditorID, status, remarks, prevCampaignID, countryID) ";
				
$CAMPAIGN_SEL ="SELECT id, campaignType, campaignName, DateAdded, DateLastEdited, DatePublished,
			    DateFrom, DateTo, adminCreatorID, adminLastEditorID, status, remarks, prevCampaignID, countryID 
			    FROM $SMBi_DEV.$table WHERE ".filtering3($conn,'campaign');
$conn->Execute($CAMPAIGN.$CAMPAIGN_SEL);
select_into_outfile($CAMPAIGN_SEL,'campaign',$conn);


/*CAMPAIGN ITEMS*/
$table 				= "campaignItemsXref";
$CAMPAIGN_ITEMS 	= "INSERT INTO $SMBi_Archive.$table (id, campaignID, itemID)";
 
$CAMPAIGN_ITEMS_SEL = "SELECT id, campaignID, itemID  
					   FROM $SMBi_DEV.$table WHERE campaignID IN 
					   ( SELECT id FROM campaign WHERE ".filtering3($conn,'campaign').")";
$conn->Execute($CAMPAIGN_ITEMS.$CAMPAIGN_ITEMS_SEL);
select_into_outfile($CAMPAIGN_ITEMS_SEL,'campaignItemsXref',$conn);


/*CAMPAIGN ITEMS DELETE*/
$CAMPAIGN_ITEMS_DEL	="DELETE FROM $SMBi_DEV.$table 
					  WHERE $SMBi_DEV.$table.campaignID IN (
						SELECT $SMBi_DEV.campaign.id  
						FROM $SMBi_DEV.campaign  WHERE ". filtering3($conn,'campaign') ."
					)";	
$conn->Execute($CAMPAIGN_ITEMS_DEL);


/*CAMPAIGN VOTERS REF*/
$table = "campaignVotersXref";
$CAMPAIGN_VOTERS_REF 	 = "INSERT INTO $SMBi_Archive.$table (id, campaignID, voterID) ";
$CAMPAIGN_VOTERS_REF_SEL = "SELECT id, campaignID, voterID  
							FROM $SMBi_DEV.$table WHERE campaignID IN 
							(SELECT id FROM campaign WHERE ".filtering3($conn,'campaign').")";
$conn->Execute($CAMPAIGN_VOTERS_REF.$CAMPAIGN_VOTERS_REF_SEL);
select_into_outfile($CAMPAIGN_VOTERS_REF_SEL,'campaignVotersXref',$conn);

/*CAMPAIGN VOTERS REF DELETE*/
$CAMPAIGN_VOTERS_DEL="DELETE FROM $SMBi_DEV.$table 
					WHERE $SMBi_DEV.$table.campaignID IN (
						SELECT $SMBi_DEV.campaign.id  
						FROM $SMBi_DEV.campaign  WHERE ". filtering3($conn,'campaign') ."
					)";	
$conn->Execute($CAMPAIGN_VOTERS_DEL);


/*CAMPAIGN VOTERS*/
$table = "voters";
$CAMPAIGN_VOTERS = 	"INSERT INTO $SMBi_Archive.$table (id, voterTypeID, campaignID, fname, lname, gender, email, department, 
					 year_of_birth, Fields001, Fields002, Fields003, Fields004, Fields005, dateAdded, votingStatus) ";
					
$CAMPAIGN_VOTERS_SEL ="SELECT id, voterTypeID, campaignID, fname, lname, gender, email, department, 
					   year_of_birth, Fields001, Fields002, Fields003, Fields004, Fields005, dateAdded, votingStatus  
					   FROM $SMBi_DEV.$table WHERE campaignID IN 
					   ( SELECT id FROM campaign WHERE ".filtering3($conn,'campaign').")";
$conn->Execute($CAMPAIGN_VOTERS.$CAMPAIGN_VOTERS_SEL);
select_into_outfile($CAMPAIGN_VOTERS_SEL,'voters',$conn);


/*CAMPAIGN VOTERS DELETE*/
$CAMPAIGN_VOTERS_DEL="DELETE FROM $SMBi_DEV.$table 
					  WHERE $SMBi_DEV.$table.campaignID IN (
						SELECT $SMBi_DEV.campaign.id  
						FROM $SMBi_DEV.campaign  WHERE ". filtering3($conn,'campaign') ."
					)";	
$conn->Execute($CAMPAIGN_VOTERS_DEL);


/*VOTEXREF*/
$table = "votexRef";
$VOTE_x_REF 	= 	"INSERT INTO $SMBi_Archive.$table (id, campaignID, voterID, itemID, vote, tdate, ttime) ";
				
$VOTE_x_REF_SEL = 	"SELECT id, campaignID, voterID, itemID, vote, tdate, ttime  
					 FROM $SMBi_DEV.$table WHERE campaignID IN 
					 ( SELECT id FROM campaign WHERE ".filtering3($conn,'campaign').")";
$conn->Execute($VOTE_x_REF.$VOTE_x_REF_SEL);
select_into_outfile($VOTE_x_REF_SEL,'votexRef',$conn);

/*VOTEXREF DELETE*/
$VOTE_x_REF_DEL	=  "DELETE FROM $SMBi_DEV.$table 
					WHERE $SMBi_DEV.$table.campaignID IN (
						SELECT $SMBi_DEV.campaign.id  
						FROM $SMBi_DEV.campaign  WHERE ". filtering3($conn,'campaign') ."
					)";	
$conn->Execute($VOTE_x_REF_DEL);

	
/*iLIKE RESULT*/
$table 				= "iLikeResultRef";
$iLIKE_RESULT   	= "INSERT INTO $SMBi_Archive.$table (id, campaignID, itemID, totvote) ";
$iLIKE_RESULT_SEL   = "SELECT id, campaignID, itemID, totvote  
					   FROM $SMBi_DEV.$table WHERE campaignID IN 
					  ( SELECT id FROM campaign WHERE ".filtering3($conn,'campaign').")";
$conn->Execute($iLIKE_RESULT.$iLIKE_RESULT_SEL);
select_into_outfile($iLIKE_RESULT_SEL,'iLikeResultRef',$conn);

/*iLIKE RESULT DELETE*/
$iLIKE_RESULT_DEL="DELETE FROM $SMBi_DEV.$table 
					WHERE $SMBi_DEV.$table.campaignID IN (
						SELECT $SMBi_DEV.campaign.id  
						FROM $SMBi_DEV.campaign  WHERE ". filtering3($conn,'campaign') ."
					)";	
$conn->Execute($iLIKE_RESULT_DEL);

/*iWANT RESULT*/
$table 				= "iWantResultRef";
$iWANT_RESULT 		= "INSERT INTO $SMBi_Archive.$table (id, campaignID, itemID, totvote) ";
$iWANT_RESULT_SEL 	= "SELECT id, campaignID, itemID, totvote  
						FROM $SMBi_DEV.$table WHERE campaignID IN 
						(SELECT id FROM campaign WHERE ".filtering3($conn,'campaign').")";
$conn->Execute($iWANT_RESULT.$iWANT_RESULT_SEL);
select_into_outfile($iWANT_RESULT_SEL,'iWantResultRef',$conn);

/*iWANT RESULT DELETE*/
$iWANT_RESULT_DEL="DELETE FROM $SMBi_DEV.$table 
					WHERE $SMBi_DEV.$table.campaignID IN (
						SELECT $SMBi_DEV.campaign.id  
						FROM $SMBi_DEV.campaign  WHERE ". filtering3($conn,'campaign') ."
					)";	
$conn->Execute($iWANT_RESULT_DEL);

/*CAMPAIGN DELETE*/
$table = 'campaign';
$CAMPAIGN_DELETE ="DELETE FROM $SMBi_DEV.$table 
					  WHERE $SMBi_DEV.$table.id IN (
						SELECT ID FROM
						(SELECT $SMBi_DEV.campaign.id  
						FROM $SMBi_DEV.campaign  WHERE ". filtering3($conn,'campaign') ."
						) as c
					)";	
			
$conn->Execute($CAMPAIGN_DELETE);	
//die();
	
?>
