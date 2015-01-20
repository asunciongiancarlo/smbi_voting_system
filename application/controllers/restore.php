<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Restore extends CI_Controller {
 
   public function __construct()
   {
		parent::__construct();
		date_default_timezone_set('UTC');
		session_start();
		$this->load->model('c3model');
		$this->load->library('security');
		$this->load->library('modules');
		$this->load->library('rec_logs');
		$this->output->enable_profiler(FALSE);
		set_time_limit(0);

   }
    
	
	function itemsFilteringCatalog($aID='')
	{
	$SMBi_DEV 				  = 'SMBi_DEV';
	$SMBi_Archive 			  = 'SMBi_Archive';
	
	/*GET ARCHIVE LOCATION*/
	$sql 	   = "SELECT archive_name, startDate, endDate FROM archive_list WHERE id = $aID";
	$row 	   = $this->db->query($sql);
	$archive   = $row->row();
	$startDate = $archive->startDate;
	$endDate   = $archive->endDate;
	
	$filter = "(SELECT ec_items.id FROM $SMBi_Archive.ec_items WHERE
					$SMBi_Archive.ec_items.dateAdded >= '$endDate' AND $SMBi_Archive.ec_items.dateAdded <= '$startDate' 
				)";
	return $filter;
	}
	
	function itemsFilteringItemDB($aID='')
	{
	$SMBi_DEV 				  = 'SMBi_DEV';
	$SMBi_Archive 			  = 'SMBi_Archive';
	
	/*GET ARCHIVE LOCATION*/
	$sql 	   = "SELECT archive_name, startDate, endDate FROM archive_list WHERE id = $aID";
	$row 	   = $this->db->query($sql);
	$archive   = $row->row();
	$startDate = $archive->startDate;
	$endDate   = $archive->endDate;
	
	$filter = "(SELECT items.id FROM $SMBi_Archive.items WHERE
					$SMBi_Archive.items.dateAdded >= '$endDate' AND $SMBi_Archive.items.dateAdded <= '$startDate' 
				)";
	return $filter;
	}
	
	function restore_all($aID='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		
		$SMBi_DEV     = "SMBi_DEV";
		$SMBi_Archive = "SMBi_Archive";
	
		/*GET ARCHIVE LOCATION*/
		$sql 	   = "SELECT archive_name, startDate, endDate FROM archive_list WHERE id = $aID";
		$row 	   = $this->db->query($sql);
		$archive   = $row->row();
		$archive_name = $archive->archive_name;
		$startDate 	  = $archive->startDate;
		$endDate      = $archive->endDate;
		
		//LOGS
		$sql = $this->db->query("SELECT ec_items.id as itmID FROM $SMBi_Archive.ec_items WHERE $SMBi_Archive.ec_items.id IN (".$this->itemsFilteringCatalog($aID).")");
		$rows = $sql->result_array();
		
		foreach($rows as $r){
		extract($r);
		//RECORD LOG IN
		$SQL = $this->db->query("SELECT id, itemCode, itemName FROM $SMBi_Archive.ec_items WHERE id= $itmID");
		$row = $SQL->row();
		$CI->rec_logs->w($row->id, $row->itemName, 'eCatagoue Items','Items','restore', $row->itemCode);
		}
		
		
		
		//EC ITEM 
		$ITEMS = "INSERT INTO $SMBi_DEV.ec_items 
		(id, itemCode, POSMTypeID, POSMStatusID, OUTLETStatusID, PremiumTypeID, MaterialTypeID, 
		ecID, countryID, brandID, publish_other_country, itemName, Photo, publish, Short_Description, Long_Description, UnitPrice, USD_Price,
		MOQ, UOM, country_of_origin, dateAdded, dateReleased, DateLastEdited, user_id, Fields0001, Fields0002, Fields0003, Fields0004, Fields0005, estimated_production_lead_time, 	price_validity,
		plant_inventory, supplier_stock_on_hand, date_first_issue, date_last_used, activity_event_use, num_views)";
		
		$ITEMS_SEL = "SELECT id, itemCode, POSMTypeID, POSMStatusID, OUTLETStatusID, PremiumTypeID, MaterialTypeID, 
		ecID, countryID, brandID, publish_other_country, itemName, Photo, publish, Short_Description, Long_Description, UnitPrice, USD_Price,
		MOQ, UOM, country_of_origin, CURDATE(), dateReleased, DateLastEdited, user_id, Fields0001, Fields0002, Fields0003, Fields0004, Fields0005, estimated_production_lead_time, 	price_validity,
		plant_inventory, supplier_stock_on_hand, date_first_issue, date_last_used, activity_event_use, num_views 
		FROM $SMBi_Archive.ec_items WHERE $SMBi_Archive.ec_items.id IN (".$this->itemsFilteringCatalog($aID).") "; 	
		$this->db->query($ITEMS.$ITEMS_SEL);
		
		//EC IMAGES
		$ITEMS_IMAGES = "INSERT INTO $SMBi_DEV.ecitems_images 
		(id, itemID, image, defaultStatus)"; 
		
		$ITEMS_IMAGES_SEL = "SELECT id, itemID, image, defaultStatus
		FROM $SMBi_Archive.ecitems_images WHERE itemID  IN (".$this->itemsFilteringCatalog($aID).") ";
		$this->db->query($ITEMS_IMAGES.$ITEMS_IMAGES_SEL);
		
		//EC DELETE IMAGES
		$ITEMS_IMG_DELETE="DELETE FROM $SMBi_Archive.ecitems_images WHERE itemID IN (".$this->itemsFilteringCatalog($aID).") ";
		$this->db->query($ITEMS_IMG_DELETE);
		
		//EC DELETE ITEM
		$ITEMS_DELETE="DELETE FROM $SMBi_Archive.ec_items 
				   WHERE $SMBi_Archive.ec_items.id IN (
						SELECT id FROM (
							SELECT $SMBi_Archive.ec_items.id  
							FROM $SMBi_Archive.ec_items  WHERE $SMBi_Archive.ec_items.id IN (". $this->itemsFilteringCatalog($aID) .")
						) AS c
					)";	
		$this->db->query($ITEMS_DELETE);
		
		//eCATALOGUE ITEMS LOGS
		$sql = $this->db->query("SELECT items.id as itmID FROM $SMBi_Archive.items WHERE $SMBi_Archive.items.id IN (".$this->itemsFilteringItemDB($aID).")");
		$rows2 = $sql->result_array();
		
		foreach($rows2 as $r){
		extract($r);
		//RECORD LOG IN
		$SQL = $this->db->query("SELECT id, itemCode, itemName FROM $SMBi_Archive.items WHERE id= $itmID");
		$row = $SQL->row();
		$CI->rec_logs->w($row->id, $row->itemName, 'Item Database','Items','restore', $row->itemCode);
		}
	
		
		//ITEM DATABASE ITEMS
		$ITEMS = "INSERT INTO $SMBi_DEV.items 
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
		UnitPrice, USD_Price, MOQ, UOM, 
		country_of_origin, CURDATE(), dateReleased, dateLastEdited, user_id,
		estimated_production_lead_time, price_validity,
		Fields0001, Fields0002, Fields0003, Fields0004, Fields0005, 
		plant_inventory, supplier_stock_on_hand, date_first_issue, 
		date_last_used, activity_event_use, num_views 
		FROM $SMBi_Archive.items WHERE $SMBi_Archive.items.id IN (". $this->itemsFilteringItemDB($aID) .")";
		$this->db->query($ITEMS.$ITEMS_SEL);
		
		//EC IMAGES
		$ITEMS_IMAGES = "INSERT INTO $SMBi_DEV.items_images 
		(id, itemID, image, defaultStatus)"; 
		
		$ITEMS_IMAGES_SEL = "SELECT id, itemID, image, defaultStatus
		FROM $SMBi_Archive.items_images WHERE itemID  IN (".$this->itemsFilteringItemDB($aID).") ";
		$this->db->query($ITEMS_IMAGES.$ITEMS_IMAGES_SEL);
		
		//EC DELETE IMAGES
		$ITEMS_IMG_DELETE="DELETE FROM $SMBi_Archive.items_images WHERE itemID IN (".$this->itemsFilteringItemDB($aID).") ";
		$this->db->query($ITEMS_IMG_DELETE);
		
		//EC DELETE ITEM
		$ITEMS_DELETE="DELETE FROM $SMBi_Archive.items 
				   WHERE $SMBi_Archive.items.id IN (
						SELECT id FROM (
							SELECT $SMBi_Archive.items.id  
							FROM $SMBi_Archive.items  WHERE $SMBi_Archive.items.id IN (". $this->itemsFilteringItemDB($aID) .")
						) AS c
					)";	
		$this->db->query($ITEMS_DELETE);
		
		//UNLINK THE ARCHIVE FILE
		$archivePath = "C:/xampp/htdocs/smbi_dev/img_archive/$archive_name";
		//$archivePath = realpath($_SERVER["DOCUMENT_ROOT"])."/img_archive/$archive_name";
		unlink($archivePath);
		
		//RECORD LOG IN
		$CI->rec_logs->w($aID, $archive_name, 'Archive File','Archive','restore');
		
		$this->db->query("DELETE FROM archive_list WHERE id = $aID");
		echo "Restore point has been restored.";
	}
	
	
	
    function filtering($table,$dateFrom,$dateTo)
	{
		$SMBi_Archive = "SMBi_Archive";
		return $filter   = " $SMBi_Archive.$table.dateAdded <= '$dateFrom' AND $SMBi_Archive.$table.dateAdded >= '$dateTo'";
	}
	
	function load_data_infile($archivePath,$file_name,$table)
	{
		$path = $archivePath.$file_name;
		
		echo $sql = "LOAD  DATA INFILE '$path' 
				INTO   TABLE $table 
				FIELDS TERMINATED BY '	'
				LINES  TERMINATED  BY '\r\n'";
		echo "<br/>";
		$row  = $this->db->query($sql);
	}
	
    function restore_eCatalogue($aID='')
    {
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		
		/*GET ARCHIVE LOCATION*/
		$sql 	   = "SELECT archive_name, startDate, endDate FROM archive_list WHERE id = $aID";
		$row 	   = $this->db->query($sql);
		$archive   = $row->row();
		$startDate = $archive->startDate;
		$endDate   = $archive->endDate;
		
		$archivePath   = realpath($_SERVER["DOCUMENT_ROOT"]).'/img_archive/'.$archive->archive_name.'/database/';
		$files = array(array('file_name'=>'e_catalog.txt',
							 'table'=>'e_catalog'),
					   array('file_name'=>'ec_items.txt',
							 'table'=>'ec_items'),
					   array('file_name'=>'ecitems_images.txt',
							 'table'=>'ecitems_images'),
					   array('file_name'=>'ecitemVendorsRef.txt',
							 'table'=>'ecitemVendorsRef'),
					   array('file_name'=>'ec_vendors.txt',
							 'table'=>'ec_vendors')
					   );
		//print_r($files);			   
		
		foreach($files as $f)
		{
			extract($f);
			$this->load_data_infile($archivePath,$file_name,$table);
		}
		
		
		/*DELETE FROM SMBi ARCHIVE*/
		$SMBi_Archive = "SMBi_Archive";
		$sql = "DELETE FROM $SMBi_Archive.e_catalog WHERE ". $this->filtering('e_catalog',$startDate,$endDate);
		$this->db->query($sql);
		
		/*ITEMS VENDORS*/
		$ITEMS_VENDORS="DELETE FROM $SMBi_Archive.ec_vendors 
						   WHERE $SMBi_Archive.ec_vendors.id IN (
								SELECT id FROM (
									SELECT vendorID FROM ecitemVendorsRef WHERE itemID IN(
										SELECT $SMBi_Archive.ec_items.id  
										FROM $SMBi_Archive.ec_items  WHERE ". $this->filtering('ec_items',$startDate,$endDate) ."
									)
								) AS c
							)";				
		$this->db->query($ITEMS_VENDORS);
		
		/*ITEMS VENDOR XREF*/
		$ITEMS_VENDORS_REF="DELETE FROM $SMBi_Archive.ecitemVendorsRef 
						   WHERE $SMBi_Archive.ecitemVendorsRef.itemID IN (
								SELECT id FROM (
									SELECT $SMBi_Archive.ec_items.id  
									FROM $SMBi_Archive.ec_items  WHERE ". $this->filtering('ec_items',$startDate,$endDate) ."
								) AS c
							)";				
		$this->db->query($ITEMS_VENDORS_REF);
		
			
		/*ITEMS IMAGES*/
		$ITEMS_IMG_DELETE="DELETE FROM $SMBi_Archive.ecitems_images 
						   WHERE $SMBi_Archive.ecitems_images.itemID IN (
								SELECT id FROM (
									SELECT $SMBi_Archive.ec_items.id  
									FROM $SMBi_Archive.ec_items  WHERE ". $this->filtering('ec_items',$startDate,$endDate) ."
								) AS c
							)";					
		$this->db->query($ITEMS_IMG_DELETE);
		
		/*ITEMS DELETE*/
		$ITEMS_DELETE="DELETE FROM $SMBi_Archive.ec_items 
					   WHERE $SMBi_Archive.ec_items.id IN (
							SELECT id FROM (
								SELECT $SMBi_Archive.ec_items.id  
								FROM $SMBi_Archive.ec_items  WHERE ". $this->filtering('ec_items',$startDate,$endDate) ."
							) AS c
						)";				
		$this->db->query($ITEMS_DELETE);
		
		$sql = "UPDATE archive_ref SET e_catalog=1 WHERE archive_id = $aID";
		$this->db->query($sql);
		
		/*REC LOGS*/
		$CI->rec_logs->w($aID,$archive->archive_name,'Archive','Archive eCatalogue','restore');
		
		echo "eCatalogue has been restored.";
	}
	
	function restore_Item_Database($aID='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		
		/*GET ARCHIVE LOCATION*/
		$sql 	   = "SELECT archive_name, startDate, endDate FROM archive_list WHERE id = $aID";
		$row 	   = $this->db->query($sql);
		$archive   = $row->row();
		$startDate = $archive->startDate;
		$endDate   = $archive->endDate;
		
		$archivePath   = realpath($_SERVER["DOCUMENT_ROOT"]).'/img_archive/'.$archive->archive_name.'/database/';
		$files = array(array('file_name'=>'items.txt',
							 'table'=>'items'),
					   array('file_name'=>'items_images.txt',
							 'table'=>'items_images'),
					   array('file_name'=>'itemVendorsRef.txt',
							 'table'=>'itemVendorsRef'),
					   array('file_name'=>'vendors.txt',
							 'table'=>'vendors')
					   );
		//print_r($files);			   
		
		foreach($files as $f)
		{	extract($f);
			$this->load_data_infile($archivePath,$file_name,$table);
		}
		
		$SMBi_Archive = "SMBi_Archive";
		/*ITEMS VENDORS*/
		$ITEMS_VENDORS = "DELETE FROM $SMBi_Archive.vendors 
						   WHERE $SMBi_Archive.vendors.id IN (
								SELECT id FROM (
									SELECT vendorID FROM itemVendorsRef WHERE itemID IN(
										SELECT $SMBi_Archive.items.id  
										FROM $SMBi_Archive.items  WHERE ". $this->filtering('items',$startDate,$endDate) ."
									)
								) AS c
							)";				
		$this->db->query($ITEMS_VENDORS);	

		/*ITEMS VENDOR XREF*/
		$ITEMS_VENDORS_REF="DELETE FROM $SMBi_Archive.itemVendorsRef 
						   WHERE $SMBi_Archive.itemVendorsRef.itemID IN (
								SELECT id FROM (
									SELECT $SMBi_Archive.items.id  
									FROM $SMBi_Archive.items  WHERE ". $this->filtering('items',$startDate,$endDate) ."
								) AS c
							)";				
		$this->db->query($ITEMS_VENDORS_REF);
			
			
		/*ITEMS IMAGES*/
		$ITEMS_IMG_DELETE="DELETE FROM $SMBi_Archive.items_images 
						   WHERE $SMBi_Archive.items_images.itemID IN (
								SELECT id FROM (
									SELECT $SMBi_Archive.items.id  
									FROM $SMBi_Archive.items  WHERE ". $this->filtering('items',$startDate,$endDate) ."
								) AS c
							)";				
		$this->db->query($ITEMS_IMG_DELETE);	
			
		/*ITEMS DELETE*/
		$ITEMS_DELETE="DELETE FROM $SMBi_Archive.items 
					   WHERE $SMBi_Archive.items.id IN (
							SELECT id FROM (
								SELECT $SMBi_Archive.items.id  
								FROM $SMBi_Archive.items  WHERE ". $this->filtering('items',$startDate,$endDate) ."
							) AS c
						)";				
		$this->db->query($ITEMS_DELETE);
		
		$sql = "UPDATE archive_ref SET items=1 WHERE archive_id = $aID";
		$this->db->query($sql);
		
		/*REC LOGS*/
		$CI->rec_logs->w($aID,$archive->archive_name,'Archive','Archive Item Database','restore');
		
		echo "Items has been restored.";
	}
    
	function restore_Campaigns($aID='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		
		/*GET ARCHIVE LOCATION*/
		$sql 	   = "SELECT archive_name, startDate, endDate FROM archive_list WHERE id = $aID";
		$row 	   = $this->db->query($sql);
		$archive   = $row->row();
		$startDate = $archive->startDate;
		$endDate   = $archive->endDate;
		
		$archivePath   = realpath($_SERVER["DOCUMENT_ROOT"]).'/img_archive/'.$archive->archive_name.'/database/';
		$files = array(array('file_name'=>'campaign.txt',
							 'table'=>'campaign'),
					   array('file_name'=>'campaignItemsXref.txt',
							 'table'=>'campaignItemsXref'),
					   array('file_name'=>'campaignVotersXref.txt',
							 'table'=>'campaignVotersXref'),
					   array('file_name'=>'voters.txt',
							 'table'=>'voters'),
					   array('file_name'=>'votexRef.txt',
							 'table'=>'votexRef'),
					   array('file_name'=>'iLikeResultRef.txt',
							 'table'=>'iLikeResultRef'),
					   array('file_name'=>'iWantResultRef.txt',
							 'table'=>'iWantResultRef')
					   );
		//print_r($files);			   
		
		foreach($files as $f)
		{	extract($f);
			$this->load_data_infile($archivePath,$file_name,$table);
		}
		
		$SMBi_Archive = "SMBi_Archive";
		/*CAMPAIGN ITEMS DELETE*/
		$CAMPAIGN_ITEMS_DEL	="DELETE FROM $SMBi_Archive.$table 
							  WHERE $SMBi_Archive.$table.campaignID IN (
								SELECT $SMBi_Archive.campaign.id  
								FROM $SMBi_Archive.campaign  WHERE ". $this->filtering('campaign',$startDate,$endDate) ."
							)";	
		$this->db->query($CAMPAIGN_ITEMS_DEL);

		/*CAMPAIGN VOTERS REF DELETE*/
		$CAMPAIGN_VOTERS_DEL="DELETE FROM $SMBi_Archive.$table 
							WHERE $SMBi_Archive.$table.campaignID IN (
								SELECT $SMBi_Archive.campaign.id  
								FROM $SMBi_Archive.campaign  WHERE ". $this->filtering('campaign',$startDate,$endDate) ."
							)";	
		$this->db->query($CAMPAIGN_VOTERS_DEL);
			
			
		/*CAMPAIGN VOTERS DELETE*/
		$CAMPAIGN_VOTERS_DEL="DELETE FROM $SMBi_Archive.$table 
							  WHERE $SMBi_Archive.$table.campaignID IN (
								SELECT $SMBi_Archive.campaign.id  
								FROM $SMBi_Archive.campaign  WHERE ". $this->filtering('campaign',$startDate,$endDate) ."
							)";	
		$this->db->query($CAMPAIGN_VOTERS_DEL);	
			
		/*VOTEXREF DELETE*/
		$VOTE_x_REF_DEL	=  "DELETE FROM $SMBi_Archive.$table 
							WHERE $SMBi_Archive.$table.campaignID IN (
								SELECT $SMBi_Archive.campaign.id  
								FROM $SMBi_Archive.campaign  WHERE ". $this->filtering('campaign',$startDate,$endDate) ."
							)";	
		$this->db->query($VOTE_x_REF_DEL);
		
		/*iLIKE RESULT DELETE*/
		$iLIKE_RESULT_DEL="DELETE FROM $SMBi_Archive.$table 
							WHERE $SMBi_Archive.$table.campaignID IN (
								SELECT $SMBi_Archive.campaign.id  
								FROM $SMBi_Archive.campaign  WHERE ". $this->filtering('campaign',$startDate,$endDate) ."
							)";	
		$this->db->query($iLIKE_RESULT_DEL);
		
		
		/*iWANT RESULT DELETE*/
		$iWANT_RESULT_DEL="DELETE FROM $SMBi_Archive.$table 
							WHERE $SMBi_Archive.$table.campaignID IN (
								SELECT $SMBi_Archive.campaign.id  
								FROM $SMBi_Archive.campaign  WHERE ". $this->filtering('campaign',$startDate,$endDate) ."
							)";	
		$this->db->query($iWANT_RESULT_DEL);
		
		/*CAMPAIGN DELETE*/
		$CAMPAIGN_DELETE ="DELETE FROM $SMBi_Archive.campaign 
							  WHERE $SMBi_Archive.campaign.id IN (
								SELECT ID FROM
								(SELECT $SMBi_Archive.campaign.id  
								FROM $SMBi_Archive.campaign  WHERE ". $this->filtering('campaign',$startDate,$endDate) ."
								) as c
							)";	
					
		$this->db->query($CAMPAIGN_DELETE);
		
		$sql = "UPDATE archive_ref SET campaign=1 WHERE archive_id = $aID";
		$this->db->query($sql);
		
		$sql 	= "SELECT e_catalog,items,campaign FROM archive_ref WHERE archive_id = $aID";
		$row 	= $this->db->query($sql);
		$result = $row->row();
		
		if($result->e_catalog == 1 AND  $result->items == 1 AND $result->campaign == 1)
		{
			$this->db->query("DELETE FROM archive_ref  WHERE archive_id = $aID");
			$this->db->query("DELETE FROM archive_list WHERE id = $aID");
		}
		
		/*REC LOGS*/
		$CI->rec_logs->w($aID,$archive->archive_name,'Archive','Archive Campaigns','restore');
		
		echo "Campaigns has been restored.";
	}
   
}