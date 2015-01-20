<?php

function filtering($conn)
{
	$SMBi_DEV 				  = 'SMBi_DEV';
	$SMBi_Archive 			  = 'SMBi_Archive';
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
	  
		$filter = " $SMBi_DEV.items.dateAdded <= CURDATE() AND $SMBi_DEV.items.dateAdded >= (SELECT CURDATE() - INTERVAL $m MONTH)";
		
	}else{
	
		$sql  = "SELECT dateFrom, dateTo FROM archive_filtering LIMIT 0,1";
		$rows = $conn->Execute($sql);
		
		while(!$rows->EOF)
		{
			$dateFrom 	= $rows->fields['dateFrom'];	
			$dateTo 	= $rows->fields['dateTo'];	
			$rows->moveNext();
		}
		
		$filter = " $SMBi_DEV.items.dateAdded <= '$dateFrom' AND $SMBi_DEV.items.dateAdded >= '$dateTo'";
		
}
	
	return $filter;
}

function directory_Name($conn)
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

function img_archive($conn)
{
	$SMBi_DEV 				  = 'SMBi_DEV';
	$SMBi_Archive 			  = 'SMBi_Archive';
	/* CREATE DIRECORY */
	// /xampp/docroot/smbi_dev
	
	echo $archivePath   = realpath($_SERVER["DOCUMENT_ROOT"]).'/img_archive/'.directory_Name($conn).'/';	
	echo $originalPath  = realpath($_SERVER["DOCUMENT_ROOT"]).'/img/';	
	$oldumask = umask(0); 
	mkdir($archivePath, 0777);
	umask($oldumask);
	
	/*COPY IMAGES*/
	$IMAGES = "SELECT image FROM $SMBi_DEV.items_images WHERE itemID IN(SELECT id FROM items WHERE ". filtering($conn) .")";
	$rows	= $conn->Execute($IMAGES);
	
	while(!$rows->EOF)
	{
		$image = $rows->fields['image'];
	
		//BIG
		if(!file_exists($archivePath.'big')){	
			$oldumask = umask(0); 
			mkdir($archivePath.'big', 0777);
			umask($oldumask);
		}
		echo 'copied: '.$originalPath.'big/'.$image .' -> '. $archivePath.'big/'.$image.' \r\n';
		copy($originalPath.'big/'.$image, $archivePath.'big/'.$image);
		
		
		//SMALL
		if(!file_exists($archivePath.'small')){	
			$oldumask = umask(0); 
			mkdir($archivePath.'small', 0777);
			umask($oldumask);
		}
		echo 'copied: '.$originalPath.'small/'.$image .' -> '. $archivePath.'small/'.$image.' \r\n';
		copy($originalPath.'small/'.$image, $archivePath.'small/'.$image);
		
		
		//GALLERY IMG
		if(!file_exists($archivePath.'galleryImg')){	
			$oldumask = umask(0); 
			mkdir($archivePath.'galleryImg', 0777);
			umask($oldumask);
		}
		echo 'copied: '.$originalPath.'galleryImg/'.$image .' -> '. $archivePath.'galleryImg/'.$image.' \r\n';
		copy($originalPath.'galleryImg/'.$image, $archivePath.'galleryImg/'.$image);
		
		
		//THUMB
		if(!file_exists($archivePath.'thumb')){	
			$oldumask = umask(0); 
			mkdir($archivePath.'thumb', 0777);
			umask($oldumask);
		}
		echo 'copied: '.$originalPath.'thumb/'.$image .' -> '. $archivePath.'thumb/'.$image.' \r\n';
		copy($originalPath.'thumb/'.$image, $archivePath.'thumb/'.$image);
		
		$rows->moveNext();
	}
	
}

function select_into_outfile($SQL,$fl_name,$conn)
{
	$SMBi_DEV 				  = 'SMBi_DEV';
	$SMBi_Archive 			  = 'SMBi_Archive';
	$archivePath = realpath($_SERVER["DOCUMENT_ROOT"]).'/img_archive/'.directory_Name($conn);
	
	
	if(!file_exists($archivePath)){	
		$oldumask = umask(0); 
		mkdir($archivePath, 0777);
		umask($oldumask);
	}
	
	/*CREATE FOLDER DATABASE*/
	$archivePath = realpath($_SERVER["DOCUMENT_ROOT"]).'/img_archive/'.directory_Name($conn).'/database/';
	if(!file_exists($archivePath)){	
		$oldumask = umask(0); 
		mkdir($archivePath, 0777);
		umask($oldumask);
	}
	
	$archivePath .= $fl_name.".txt";
	echo $SQL .= "INTO OUTFILE '$archivePath' 
			FIELDS TERMINATED BY '	'
			LINES TERMINATED BY '\r\n'";
	$conn->Execute($SQL);
}


/*BACK UP WHOLE DATABASE
	$archivePath = realpath($_SERVER["DOCUMENT_ROOT"]).'/img_archive/'.directory_Name($conn).'/database/smbi_db.sql';
	echo $sql 		 = "mysqldump -u root -pamurao120282 SMBi_DEV > $archivePath";
	$conn->Execute($sql);
	*/
	
/*ITEMS*/
	$ITEMS = "INSERT INTO $SMBi_Archive.items 
	(id, itemCode, POSMTypeID, POSMStatusID, 
	OUTLETStatusID, PremiumTypeID, MaterialTypeID, 
	countryID, brandID, publish_other_country, 
	itemName, Photo, publish, 
	irrelevant, Short_Description, Long_Description, 
	UnitPrice, 	USD_Price, MOQ, UOM, 
	country_of_origin, dateAdded, dateReleased, dateLastEdited, user_id,
	estimated_production_lead_time, price_validity,
	Fields0001, Fields0002, Fields0003, Fields0004, Fields0005, 
	plant_inventory, supplier_stock_on_hand, date_first_issue, 
	date_last_used, activity_event_use, num_views)"; 
	
	$ITEMS_SEL = "SELECT 
	id, itemCode, POSMTypeID, POSMStatusID, 
	OUTLETStatusID, PremiumTypeID, MaterialTypeID, 
	countryID, brandID, publish_other_country, 
	itemName, Photo, publish, 
	irrelevant, Short_Description, Long_Description, 
	UnitPrice, MOQ, UOM, 
	country_of_origin, dateAdded, dateReleased, dateLastEdited, user_id,
	estimated_production_lead_time, price_validity,
	Fields0001, Fields0002, Fields0003, Fields0004, Fields0005, 
	plant_inventory, supplier_stock_on_hand, date_first_issue, 
	date_last_used, activity_event_use, num_views 
	FROM $SMBi_DEV.items WHERE ". filtering($conn);
	$conn->Execute($ITEMS.$ITEMS_SEL);
	select_into_outfile($ITEMS_SEL,'items',$conn);
	
/*ITEMS IMAGES*/		
	$ITEMS_IMAGES = "INSERT INTO $SMBi_Archive.items_images 
	(id, itemID, image, defaultStatus)"; 
	
	$ITEMS_IMAGES_SEL = "SELECT id, itemID, image, defaultStatus
	FROM $SMBi_DEV.items_images WHERE itemID IN(SELECT id FROM $SMBi_DEV.items WHERE ". filtering($conn) .")";
	$conn->Execute($ITEMS_IMAGES.$ITEMS_IMAGES_SEL);
	select_into_outfile($ITEMS_IMAGES_SEL,'items_images',$conn);
	
/*COPY IMAGES*/
	$sql = "SELECT id
	FROM $SMBi_DEV.items_images WHERE itemID IN(SELECT id FROM $SMBi_DEV.items WHERE ". filtering($conn) .")";
	$item_images = $conn->Execute($sql);
	if($item_images)
		img_archive($conn);
	
	
/*ITEMS VENDORS*/
	$ITEMS_VENDORS_REF = "INSERT INTO $SMBi_Archive.itemVendorsRef 
	(id, itemID, vendorID)"; 
	
	$ITEMS_VENDORS_REF_SEL = "SELECT id, itemID, vendorID
	FROM $SMBi_DEV.itemVendorsRef WHERE itemID IN(SELECT id FROM $SMBi_DEV.items WHERE ". filtering($conn) .")";
	$conn->Execute($ITEMS_VENDORS_REF.$ITEMS_VENDORS_REF_SEL);
	select_into_outfile($ITEMS_VENDORS_REF_SEL,'itemVendorsRef',$conn);
	
	$VENDORS = "INSERT INTO $SMBi_Archive.vendors 
	(id, company_name, fname, mname, lname, email, telephone, countryID, billing_address, postal_code, 
	city_state, dateAdded, dateLastEdited)"; 
	
	$VENDORS_SEL = "SELECT id, company_name, fname, mname, lname, email, telephone, countryID, billing_address, postal_code, 
	city_state, dateAdded, dateLastEdited
	FROM $SMBi_DEV.vendors WHERE id IN
	(SELECT vendorID FROM $SMBi_DEV.itemVendorsRef WHERE itemID IN(SELECT id FROM $SMBi_DEV.items WHERE ". filtering($conn) ."))";
	$conn->Execute($VENDORS.$VENDORS_SEL);
	select_into_outfile($VENDORS_SEL,'vendors',$conn);


/*ITEMS VENDORS*/
$ITEMS_VENDORS = "DELETE FROM $SMBi_DEV.vendors 
				   WHERE $SMBi_DEV.vendors.id IN (
						SELECT id FROM (
							SELECT vendorID FROM itemVendorsRef WHERE itemID IN(
								SELECT $SMBi_DEV.items.id  
								FROM $SMBi_DEV.items  WHERE ". filtering($conn) ."
							)
						) AS c
					)";				
$conn->Execute($ITEMS_VENDORS);		

/*ITEMS VENDOR XREF*/
$ITEMS_VENDORS_REF="DELETE FROM $SMBi_DEV.itemVendorsRef 
				   WHERE $SMBi_DEV.itemVendorsRef.itemID IN (
						SELECT id FROM (
							SELECT $SMBi_DEV.items.id  
							FROM $SMBi_DEV.items  WHERE ". filtering($conn) ."
						) AS c
					)";				
$conn->Execute($ITEMS_VENDORS_REF);
	
	
/*ITEMS IMAGES*/
$ITEMS_IMG_DELETE="DELETE FROM $SMBi_DEV.items_images 
				   WHERE $SMBi_DEV.items_images.itemID IN (
						SELECT id FROM (
							SELECT $SMBi_DEV.items.id  
							FROM $SMBi_DEV.items  WHERE ". filtering($conn) ."
						) AS c
					)";				
$conn->Execute($ITEMS_IMG_DELETE);		
	
/*ITEMS DELETE*/
	
	$ITEMS_DELETE="DELETE FROM $SMBi_DEV.items 
				   WHERE $SMBi_DEV.items.id IN (
						SELECT id FROM (
							SELECT $SMBi_DEV.items.id  
							FROM $SMBi_DEV.items  WHERE ". filtering($conn) ."
						) AS c
					)";				
	$conn->Execute($ITEMS_DELETE);
?>
