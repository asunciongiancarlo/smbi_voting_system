<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Purging2 extends CI_Controller {
 
   public function __construct()
   {
		parent::__construct();
		date_default_timezone_set('UTC');
		session_start();
		$this->load->model('c3model');
		$this->load->library('security');
		$this->load->library('modules');
		$this->load->library('rec_logs');
		$this->load->helper('url');
		$this->load->helper('file');
		$this->load->helper('download');
		$this->output->enable_profiler(FALSE);
		$this->modules->session_handler();
		set_time_limit(0);
		//echo getcwd();
   }
      
   function zipDir($source,$destination,$zipName)
   {
	if (!extension_loaded('zip') || !file_exists($source)) {
        return false;
    }

    $zip = new ZipArchive();
    if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
        return false;
    }

    $source = str_replace('\\', '/', realpath($source));

    if (is_dir($source) === true)
    {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

        foreach ($files as $file)
        {
            $file = str_replace('\\', '/', $file);

            // Ignore "." and ".." folders
            if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )
                continue;

            $file = realpath($file);

            if (is_dir($file) === true)
            {
                $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
            }
            else if (is_file($file) === true)
            {
                $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
            }
        }
    }
    else if (is_file($source) === true)
    {
        $zip->addFromString(basename($source), file_get_contents($source));
    }

    $zip->close();

	$this->deleteDir($source);
	if(file_exists($destination)){
		//REDIRECT TO DOWNLOAD LINK	
		// force to download the zip
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false);
		header('Content-type: application/zip');
		header('Content-Disposition: attachment; filename="'.$zipName.'"');
		readfile($destination);
		// remove zip file from temp path
		unlink($destination);
	}else{
		echo "$zipName doesn't exist!";
	}
   }
   
   //REMOVE DIRECTORY RECUSIVELY
	function deleteDir($dirPath) {
	//echo $dirPath;
    if (! is_dir($dirPath)) {
        throw new InvalidArgumentException("$dirPath must be a directory");
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            self::deleteDir($file);
        } else {
            unlink($file);
        }
    }
    rmdir($dirPath);
	}
   
   
   //START CREATE TEMP FOLDER
   function saveToDisk($action='',$IDs='')
   {
    $CI =& get_instance();
	$CI->load->library('rec_logs');
	//GENERATE FOLDER NAME
	$sql = $this->db->query("SELECT countryCode FROM country WHERE id = ".$_SESSION['countryID']." LIMIT 0,1");
	$sql = $sql->row();
	$folder_name = $sql->countryCode ."".$this->reportCode();
    //CREATE FOLDER
	//echo getcwd()."/purging/$folder_name";
    $this->createFolder(getcwd()."/purging/$folder_name");	
	
	//DUMP ITEMS
	$itm_path = getcwd()."/purging/$folder_name/database/";
	$this->createFolder($itm_path);
	if($action=='saveOneItem')
	{
	 //ITEMS
	 $this->select_into_outfile("items",$IDs,$itm_path);
	 $this->select_into_outfile("items_images",$IDs,$itm_path);
	 $this->select_into_outfile("item_views",$IDs,$itm_path);
	 $this->img_dir($IDs,getcwd()."/purging/$folder_name");
	 $this->zipDir(getcwd()."/purging/$folder_name",getcwd()."/purging/$folder_name.zip",$folder_name);
	 //LOGS
	 $sql = "SELECT itemName, itemCode FROM items WHERE id = $IDs";
	 $sql = $this->db->query($sql);
	 $sql = $sql->row();
	 $CI->rec_logs->w($IDs,$sql->itemName,'Item Database','Items','save to disk & delete',$sql->itemCode);
	 
	 $this->c3model->c3crud("no-res",'','','',"DELETE FROM purgeArchive_Ref  WHERE itemID='$IDs' AND action='submit for purging' AND purgeArchive_Ref.table='PURGE'");
	 $this->c3model->c3crud("no-res",'','','',"DELETE FROM items 		       WHERE id	='$IDs'");
	 $this->c3model->c3crud("no-res",'','','',"DELETE FROM itemVendorsRef    WHERE itemID='$IDs'");
	 $this->c3model->c3crud("no-res",'','','',"DELETE FROM items_images      WHERE itemID='$IDs'");
	 $this->c3model->c3crud("no-res",'','','',"DELETE FROM item_views        WHERE itemID='$IDs'");
	}else{
	 extract($_POST);
	 if(isset($selectedItems)){
		foreach($selectedItems as $cbr => $value)
		{
		$IDs .= "$value,";
		//LOGS
		$sql = "SELECT itemName, itemCode FROM items WHERE id = $value";
		$sql = $this->db->query($sql);
		$sql = $sql->row();
		$CI->rec_logs->w($value,$sql->itemName,'Item Database','Items','save to disk & delete',$sql->itemCode);
		}
		$IDs = substr($IDs,0,-1);
		$this->select_into_outfile("items",$IDs,$itm_path);
		$this->select_into_outfile("items_images",$IDs,$itm_path);
		$this->select_into_outfile("item_views",$IDs,$itm_path);
		$this->img_dir($IDs,getcwd()."/purging/$folder_name");
		$this->zipDir(getcwd()."/purging/$folder_name",getcwd()."/purging/$folder_name.zip",$folder_name);
		//ITEMS FOR PURGING
		$this->c3model->c3crud("no-res",'','','',"DELETE FROM purgeArchive_Ref  WHERE itemID IN ($IDs) AND action='submit for purging' AND purgeArchive_Ref.table='PURGE'");
		$this->c3model->c3crud("no-res",'','','',"DELETE FROM items 		    WHERE id	 IN ($IDs) ");
		$this->c3model->c3crud("no-res",'','','',"DELETE FROM itemVendorsRef    WHERE itemID IN ($IDs)");
		$this->c3model->c3crud("no-res",'','','',"DELETE FROM items_images      WHERE itemID IN ($IDs)");
		$this->c3model->c3crud("no-res",'','','',"DELETE FROM item_views        WHERE itemID IN ($IDs)");
	 }
	 redirect(HTTP_PATH.'itemDatabase/items_for_purging/no_selected_item', 'location', 301);
	}
   }
   
   //GENERATE OUTFILE
   function select_into_outfile($type='',$IDs='',$path='')
   {
    $sql  = "";
	$test = "";
    if($type=='items')
	{
	 $sql="SELECT 
	   id, itemCode, POSMTypeID, POSMStatusID, 
	   OUTLETStatusID, PremiumTypeID, MaterialTypeID, 
	   countryID, brandID, publish_other_country, 
	   itemName, Photo, publish, 
	   irrelevant, popular, archive, items.purge,  Short_Description, Long_Description, 
	   UnitPrice, USD_Price, MOQ, UOM, 
	   country_of_origin, dateAdded, dateReleased, dateLastEdited, user_id,
	   estimated_production_lead_time, price_validity,
	   Fields0001, Fields0002, Fields0003, Fields0004, Fields0005, 
	   plant_inventory, supplier_stock_on_hand, date_first_issue, 
	   date_last_used, activity_event_use, num_views 
	   FROM items WHERE items.id IN ($IDs) ";
     $path .= "items.txt";
	}
	elseif($type=='items_images')
	{
	 $sql="SELECT id, itemID, image, defaultStatus
		   FROM items_images WHERE itemID IN  ($IDs) ";
     $path .= "items_images.txt";
	}
	elseif($type=='item_views')
	{
	 $sql="SELECT id, itemID, user_id, date_time
		   FROM item_views WHERE itemID IN  ($IDs) ";
     $path .= "item_views.txt";
	}
	//TEST & EXECUTE
	$test = $this->db->query($sql);
	$test = $test->result_array();
	if($test){
	 if(file_exists($path)==FALSE){
	 $sql .= "INTO OUTFILE '$path' 
			 FIELDS TERMINATED BY '	'
			 LINES TERMINATED BY '\r\n'";
	 $this->db->query($sql);
	 }
	}
   }
   
   function img_dir($IDs,$path)
   {
    //CREATE DIR
	$imgs = "";
	$this->createFolder($path."/big");
	$this->createFolder($path."/small");
	$this->createFolder($path."/galleryImg");
	$this->createFolder($path."/thumb");
	$sql="SELECT image FROM items_images WHERE itemID IN  ($IDs)";
	//TEST & EXECUTE
	$imgs = $this->db->query($sql);
	$imgs = $imgs->result_array();
	//print_r($imgs);
	if($imgs){
	 foreach($imgs as $img)
	 { extract($img);
	   copy(getcwd()."/img/big/$image","$path/big/$image");
	   unlink(getcwd()."/img/big/$image");
	   copy(getcwd()."/img/small/$image","$path/small/$image");
	   unlink(getcwd()."/img/small/$image");
	   copy(getcwd()."/img/galleryImg/$image","$path/galleryImg/$image");
	   unlink(getcwd()."/img/galleryImg/$image");
	   copy(getcwd()."/img/thumb/$image","$path/thumb/$image");
	   unlink(getcwd()."/img/thumb/$image");
	 }
	}
   }
   
   function createFolder($archivePath='')
	{
	 if(!file_exists($archivePath)){	
		$oldumask = umask(0); 
		mkdir($archivePath, 0777);
		umask($oldumask);
	 }
	}
	
	function reportCode()
    {
	 $sBase64 = base64_encode($_SESSION['countryID']);
	 return date('Y-m-d_H:m:s_').str_replace('=', '', strtr($sBase64, '+/', '-_'));
    }
   
  
}