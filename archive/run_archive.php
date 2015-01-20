<?php
include("conn.php");
set_time_limit(0);
date_default_timezone_set("UTC");

$SMBi_DEV 				  = 'SMBi_DEV';
$SMBi_Archive 			  = 'SMBi_Archive';
$_SERVER["DOCUMENT_ROOT"] = '/xampp/docroot/smbi_dev';
global $SMBi_DEV;
global $SMBi_Archive;


/*CHECK IF ARCHIVE ALREADY RUNED*/

function dName($conn)
{	
	$SMBi_DEV 				  = 'SMBi_DEV';
	$SMBi_Archive 			  = 'SMBi_Archive';
	$dirName = 'Archive_';
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
		
		$sql = "SELECT CURDATE() - INTERVAL $m MONTH as m";
		$dateTo = $conn->Execute($sql);
		$dirName .= date('Y-m-d')."_to_".$dateTo->fields['m'];

	}else{
		$sql  = "SELECT dateFrom, dateTo FROM archive_filtering LIMIT 0,1";
		$rows = $conn->Execute($sql);
		
		while(!$rows->EOF)
		{
			$dateFrom 	= $rows->fields['dateFrom'];	
			$dateTo 	= $rows->fields['dateTo'];	
			$rows->moveNext();
		}
		
		$dirName .= $dateFrom."_to_".$dateTo;
	}
	
	return $dirName;
}

$sql = "SELECT id FROM archive_list WHERE archive_name='".dName($conn)."'";
$aN  = $conn->Execute($sql);

if($aN->RecordCount()==0){
include("archive_items.php");
include("archive_catalog.php");
//include("archive_campaign.php");
include("archive_save_to_list.php");
}else{
	echo "archive already run";
}



?>
