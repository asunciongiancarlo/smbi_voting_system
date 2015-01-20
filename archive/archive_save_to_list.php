<?php
directory_Name4($conn);

function directory_Name4($conn)
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
		$dateFrom 	= date('Y-m-d');	
		$dateTo 	= $dateTo->fields['m'];	
		$dirName .= date('Y-m-d')."_to_".$dateTo ;

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
	
	
	/*SAVE ARCHIVE*/
	$table = 'archive_list';
	$save = "INSERT INTO $SMBi_DEV.$table (archive_name, startDate, endDate, dateAdded) 
	VALUES ( '$dirName', '".$dateFrom."', '".$dateTo."','".date('Y-m-d')."')";
	$conn->Execute($save);
	
	/*ARCHIVE REF*/
	$sql  = "SELECT max(id) as MaxID FROM $SMBi_DEV.$table";
	$rows = $conn->Execute($sql);
	
	while(!$rows->EOF)
	{
		$MaxID 	= $rows->fields['MaxID'];	
		$rows->moveNext();
	}
	
	$table = 'archive_ref';
	$save = "INSERT INTO $SMBi_DEV.$table (archive_id, e_catalog, items, campaign) VALUES (".$MaxID.", 0, 0, 0)";
	$conn->Execute($save);
	
	
	/*SAVE TO LOGS*/
	$table = 'logs';
	$save = "INSERT INTO $SMBi_DEV.$table (rec_id, rec_name, module_name, table_name, action, tdate, ttime) 
								 VALUES (".$MaxID.", '".$dirName."', 'Archive', 'Archive list', 'add', '".date('Y-m-d')."', '".date('H:i:s')."')";
	$conn->Execute($save);
	
}



?>
