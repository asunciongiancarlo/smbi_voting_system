<?php
function filtering2($conn,$table)
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


function directory_Name2($conn)
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


function img_archive2($conn)
{
	$SMBi_DEV 				  = 'SMBi_DEV';
	$SMBi_Archive 			  = 'SMBi_Archive';
	/* CREATE DIRECORY */
	// /xampp/docroot/smbi_dev
	$archivePath   = realpath($_SERVER["DOCUMENT_ROOT"]).'/img_archive/'.directory_Name2($conn).'/';	
	$originalPath  = realpath($_SERVER["DOCUMENT_ROOT"]).'/img/';	

	
	/*COPY IMAGES*/
	$IMAGES = "SELECT image FROM $SMBi_DEV.ecitems_images WHERE itemID IN(SELECT id FROM ec_items WHERE ". filtering2($conn,'ec_items') .")";
	$rows	= $conn->Execute($IMAGES);
	while(!$rows->EOF)
	{
		$image = $rows->fields['image'];
	
		//BIG
		echo 'copied: '.$originalPath.'big/'.$image .' -> '. $archivePath.'big/'.$image.' \r\n';
		copy($originalPath.'big/'.$image, $archivePath.'big/'.$image);
		
		
		//SMALL
		echo 'copied: '.$originalPath.'small/'.$image .' -> '. $archivePath.'small/'.$image.' \r\n';
		copy($originalPath.'small/'.$image, $archivePath.'small/'.$image);
		
		
		//GALLERY IMG
		echo 'copied: '.$originalPath.'galleryImg/'.$image .' -> '. $archivePath.'galleryImg/'.$image.' \r\n';
		copy($originalPath.'galleryImg/'.$image, $archivePath.'galleryImg/'.$image);
		
		
		//THUMB
		echo 'copied: '.$originalPath.'thumb/'.$image .' -> '. $archivePath.'thumb/'.$image.' \r\n';
		copy($originalPath.'thumb/'.$image, $archivePath.'thumb/'.$image);
		
		$rows->moveNext();
	}
	
}


function cover($conn)
{
	$SMBi_DEV 				  = 'SMBi_DEV';
	$SMBi_Archive 			  = 'SMBi_Archive';
	/* CREATE DIRECORY */
	// /xampp/docroot/smbi_dev
	echo $archivePath   = realpath($_SERVER["DOCUMENT_ROOT"]).'/img_archive/'.directory_Name2($conn).'/';	
	echo $originalPath  = realpath($_SERVER["DOCUMENT_ROOT"]).'/img/';	

	/*COPY IMAGES*/
	$IMAGES = "SELECT cover FROM $SMBi_DEV.e_catalog WHERE ". filtering2($conn,'e_catalog');
	$rows	= $conn->Execute($IMAGES);
	while(!$rows->EOF)
	{
		$cover = $rows->fields['cover'];
	
		//BIG
		echo $archivePath.'cover';
		if(!file_exists($archivePath.'cover')){	
			$oldumask = umask(0); 
			mkdir($archivePath.'cover', 0777);
			umask($oldumask);
		}
		echo 'copied: '.$originalPath.'cover/'.$cover .' -> '. $archivePath.'cover/'.$cover.' \r\n';
		copy($originalPath.'cover/'.$cover, $archivePath.'cover/'.$cover);
		
		$rows->moveNext();
	}
	
	//die();
}


function brand_guidelines($conn)
{
	$SMBi_DEV 				  = 'SMBi_DEV';
	$SMBi_Archive 			  = 'SMBi_Archive';
	/* CREATE DIRECORY */
	// /xampp/docroot/smbi_dev
	echo $archivePath   = realpath($_SERVER["DOCUMENT_ROOT"]).'/img_archive/'.directory_Name2($conn).'/';	
	echo $originalPath  = realpath($_SERVER["DOCUMENT_ROOT"]).'/files/';	

	/*COPY IMAGES*/
	$IMAGES = "SELECT brand_guidelines FROM $SMBi_DEV.e_catalog WHERE ". filtering2($conn,'e_catalog');
	$rows	= $conn->Execute($IMAGES);
	while(!$rows->EOF)
	{
		$brand_guidelines = $rows->fields['brand_guidelines'];
	
		//BIG
		echo $archivePath.'brand_guidelines';
		if(!file_exists($archivePath.'brand_guidelines')){	
			$oldumask = umask(0); 
			mkdir($archivePath.'brand_guidelines', 0777);
			umask($oldumask);
		}
		echo 'copied: '.$originalPath.'brand_guidelines/'.$brand_guidelines .' -> '. $archivePath.'brand_guidelines/'.$brand_guidelines.' \r\n';
		copy($originalPath.'brand_guidelines/'.$brand_guidelines, $archivePath.'brand_guidelines/'.$brand_guidelines);
		
		$rows->moveNext();
	}
}


/*eCATALOGUE*/
	$table 		= "e_catalog";
	$eCATALOGUE = "INSERT INTO $SMBi_Archive.$table (id, title, brand_guidelines, cover, tdate, publish, countryID,
				   dateAdded, DateLastEdited)"; 
				   
	$eCATALOGUE_SEL ="SELECT id, title, brand_guidelines, cover, tdate, publish, countryID,
				   dateAdded, DateLastEdited FROM $SMBi_DEV.$table WHERE ". filtering2($conn,'e_catalog');
				   $conn->Execute($eCATALOGUE.$eCATALOGUE_SEL);
				   select_into_outfile($eCATALOGUE_SEL,'e_catalog',$conn);
/*COPY COVER*/
	cover($conn);
				   
/*COPY brand guidelines*/
	$sql = "SELECT id FROM $SMBi_DEV.$table WHERE ". filtering2($conn,'e_catalog');
	$id = $conn->Execute($sql);
	if($id)
		brand_guidelines($conn);	

/*DELETE CATALOG*/
	$sql = "DELETE FROM e_catalog WHERE ". filtering2($conn,'e_catalog');
	$conn->Execute($sql);

/*ITEMS*/
	$ITEMS = "INSERT INTO $SMBi_Archive.ec_items 
	(id, itemCode, POSMTypeID, POSMStatusID, OUTLETStatusID, PremiumTypeID, MaterialTypeID, 
	ecID, countryID, brandID, publish_other_country, itemName, Photo, publish, Short_Description, Long_Description, UnitPrice, USD_Price,
	MOQ, UOM, country_of_origin, dateAdded, dateReleased, DateLastEdited, user_id, Fields0001, Fields0002, Fields0003, Fields0004, Fields0005, estimated_production_lead_time, 	price_validity
	plant_inventory, supplier_stock_on_hand, date_first_issue, date_last_used, activity_event_use, num_views)";
	
	$ITEMS_SEL = "SELECT id, itemCode, POSMTypeID, POSMStatusID, OUTLETStatusID, PremiumTypeID, MaterialTypeID, 
	ecID, countryID, brandID, publish_other_country, itemName, Photo, publish, Short_Description, Long_Description, UnitPrice, USD_Price,
	MOQ, UOM, country_of_origin, dateAdded, dateReleased, DateLastEdited, user_id, Fields0001, Fields0002, Fields0003, Fields0004, Fields0005, estimated_production_lead_time, 	price_validity,
	plant_inventory, supplier_stock_on_hand, date_first_issue, date_last_used, activity_event_use, num_views 
	FROM $SMBi_DEV.ec_items WHERE ". filtering2($conn,'ec_items');
	$conn->Execute($ITEMS.$ITEMS_SEL);
	select_into_outfile($ITEMS_SEL,'ec_items',$conn);


/*ITEMS IMAGES*/		
	$ITEMS_IMAGES = "INSERT INTO $SMBi_Archive.ecitems_images 
	(id, itemID, image, defaultStatus)"; 
	
	$ITEMS_IMAGES_SEL = "SELECT id, itemID, image, defaultStatus
	FROM $SMBi_DEV.ecitems_images WHERE itemID IN(SELECT id FROM $SMBi_DEV.ec_items WHERE ". filtering2($conn,'ec_items') .")";
	$conn->Execute($ITEMS_IMAGES.$ITEMS_IMAGES_SEL);	
	select_into_outfile($ITEMS_IMAGES_SEL,'ecitems_images',$conn);
	
/*COPY IMAGES*/
	$sql = "SELECT id
	FROM $SMBi_DEV.ecitems_images WHERE itemID IN(SELECT id FROM $SMBi_DEV.ec_items WHERE ". filtering2($conn,'ec_items') .")";
	$item_images = $conn->Execute($sql);
	if($item_images)
		img_archive2($conn);	
	
/*ITEMS VENDORS REF*/
	$ITEMS_VENDORS_REF = "INSERT INTO $SMBi_Archive.ecitemVendorsRef (id, itemID, vendorID)"; 
	
	$ITEMS_VENDORS_REF_SEL = "SELECT id, itemID, vendorID
	FROM $SMBi_DEV.ecitemVendorsRef WHERE itemID IN(SELECT id FROM $SMBi_DEV.ec_items WHERE ". filtering2($conn,'ec_items') .")";
	$conn->Execute($ITEMS_VENDORS_REF.$ITEMS_VENDORS_REF_SEL);
	select_into_outfile($ITEMS_VENDORS_REF_SEL,'ecitemVendorsRef',$conn);

/*ITEMS VENDORS*/	
	$VENDORS = "INSERT INTO $SMBi_Archive.ec_vendors 
	(id, company_name, fname, mname, lname, email, telephone,  billing_address, postal_code, 
	city_state, dateAdded, dateLastEdited)"; 
	
	$VENDORS_REF = "SELECT id, company_name, fname, mname, lname, email, telephone,  billing_address, postal_code, 
	city_state, dateAdded, dateLastEdited
	FROM $SMBi_DEV.ec_vendors WHERE id IN
	(SELECT vendorID FROM $SMBi_DEV.ecitemVendorsRef WHERE itemID IN(SELECT id FROM $SMBi_DEV.ec_items WHERE ". filtering2($conn,'ec_items') ."))";
	$conn->Execute($VENDORS.$VENDORS_REF);
	select_into_outfile($VENDORS_REF,'ec_vendors',$conn);
	
/*ITEMS VENDORS*/
$ITEMS_VENDORS="DELETE FROM $SMBi_DEV.ec_vendors 
				   WHERE $SMBi_DEV.ec_vendors.id IN (
						SELECT id FROM (
							SELECT vendorID FROM ecitemVendorsRef WHERE itemID IN(
								SELECT $SMBi_DEV.ec_items.id  
								FROM $SMBi_DEV.ec_items  WHERE ". filtering2($conn,'ec_items') ."
							)
						) AS c
					)";				
$conn->Execute($ITEMS_VENDORS);		

/*ITEMS VENDOR XREF*/
$ITEMS_VENDORS_REF="DELETE FROM $SMBi_DEV.ecitemVendorsRef 
				   WHERE $SMBi_DEV.ecitemVendorsRef.itemID IN (
						SELECT id FROM (
							SELECT $SMBi_DEV.ec_items.id  
							FROM $SMBi_DEV.ec_items  WHERE ". filtering2($conn,'ec_items') ."
						) AS c
					)";				
$conn->Execute($ITEMS_VENDORS_REF);
	
	
/*ITEMS IMAGES*/
$ITEMS_IMG_DELETE="DELETE FROM $SMBi_DEV.ecitems_images 
				   WHERE $SMBi_DEV.ecitems_images.itemID IN (
						SELECT id FROM (
							SELECT $SMBi_DEV.ec_items.id  
							FROM $SMBi_DEV.ec_items  WHERE ". filtering2($conn,'ec_items') ."
						) AS c
					)";				
$conn->Execute($ITEMS_IMG_DELETE);		
	
/*ITEMS DELETE*/
$ITEMS_DELETE="DELETE FROM $SMBi_DEV.ec_items 
			   WHERE $SMBi_DEV.ec_items.id IN (
					SELECT id FROM (
						SELECT $SMBi_DEV.ec_items.id  
						FROM $SMBi_DEV.ec_items  WHERE ". filtering2($conn,'ec_items') ."
					) AS c
				)";				
$conn->Execute($ITEMS_DELETE);	

?>
