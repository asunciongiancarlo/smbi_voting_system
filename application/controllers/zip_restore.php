<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Zip_restore extends CI_Controller {
 
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
		$this->load->library('unzip');
		$this->output->enable_profiler(FALSE);
		set_time_limit(0);
		error_reporting(0);
   }
   
  function unzip($src_file='', $dest_dir=false, $create_zip_name_dir=true, $overwrite=true) 
  {
  if ($zip = zip_open($src_file)) 
  {
    if ($zip) 
    {
      $splitter = ($create_zip_name_dir === true) ? "." : "/";
      if ($dest_dir === false) $dest_dir = substr($src_file, 0, strrpos($src_file, $splitter))."/";
      
      // Create the directories to the destination dir if they don't already exist
      $this->create_dirs($dest_dir);

      // For every file in the zip-packet
      while ($zip_entry = zip_read($zip)) 
      {
        // Now we're going to create the directories in the destination directories
        
        // If the file is not in the root dir
        $pos_last_slash = strrpos(zip_entry_name($zip_entry), "/");
        if ($pos_last_slash !== false)
        {
          // Create the directory where the zip-entry should be saved (with a "/" at the end)
          $this->create_dirs($dest_dir.substr(zip_entry_name($zip_entry), 0, $pos_last_slash+1));
        }

        // Open the entry
        if (zip_entry_open($zip,$zip_entry,"r")) 
        {
          
          // The name of the file to save on the disk
          $file_name = $dest_dir.zip_entry_name($zip_entry);
          
          // Check if the files should be overwritten or not
          if ($overwrite === true || $overwrite === false && !is_file($file_name))
          {
		    //if(is_file($file_name)){
            // Get the content of the zip entry
            $fstream = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));

            file_put_contents($file_name, $fstream );
            // Set the rights
            chmod($file_name, 0777);
            echo "save: ".$file_name."<br />";
			//}
          }
          
          // Close the entry
          zip_entry_close($zip_entry);
        }       
      }
      // Close the zip-file
      zip_close($zip);
    }
  } 
  else
  {
    return false;
  }
  
  return true;
}

/**
* This function creates recursive directories if it doesn't already exist
*
* @param String  The path that should be created
*  
* @return  void
*/
function create_dirs($path)
{
  if (!is_dir($path))
  {
    $directory_path = "";
    $directories = explode("/",$path);
    array_pop($directories);
    
    foreach($directories as $directory)
    {
      $directory_path .= $directory."/";
      if (!is_dir($directory_path))
      {
        mkdir($directory_path);
        chmod($directory_path, 0777);
      }
    }
  }
}


function restore($id='')
{
 $CI =& get_instance();
 $CI->load->library('rec_logs');
 //UNZIP FILE
 $row = "";
 $result = "";
 $sql = $this->db->query("SELECT name FROM restore_point WHERE id = $id");
 $row = $sql->row();
 $src_file = getcwd()."/purging/restore_point/".$row->name;
 if($this->unzip($src_file)==true AND $this->Directory_File_Checker(substr($src_file,0,-4),$row->name)==TRUE)
 {
  //ITEMS
  $src_file = substr($src_file,0,-4);
  if(file_exists($src_file."/database/items.txt"))
   $this->load_data_local_file($src_file."/database/items.txt","items_checker_ref",substr($row->name,0,-4));
  if(file_exists($src_file."/database/items_images.txt"))
   $this->load_data_local_file($src_file."/database/items_images.txt","images_checker_ref",substr($row->name,0,-4));
  if(file_exists($src_file."/database/item_views.txt"))
   $this->load_data_local_file($src_file."/database/item_views.txt","item_views",substr($row->name,0,-4));
  //CHECK ITEMS THAT CAN BE RESTORE
  $result = $this->restore_seletected_items(substr($row->name,0,-4));
  
  //DELETE TEMP DIRECTORY
  unlink(getcwd()."/purging/restore_point/".$row->name);
  //DELETE RESTORE POINT + REF POINTS + ADD LOG
  $this->db->query("DELETE FROM restore_point WHERE id='$id'");
  $CI->rec_logs->w($id,$row->name,'Item Database','Restore Point','restore');
  $this->db->query("DELETE FROM purgeArchive_Ref WHERE purgeArchive_Ref.table='".substr($row->name,0,-4)."' AND action='restore'");
  //OH REFTABLE2 AND REFTABLE3
  $this->db->query("DELETE FROM images_checker_ref WHERE refTable2='".substr($row->name,0,-4)."'");
  $this->db->query("DELETE FROM images_checker_ref WHERE refTable3='".substr($row->name,0,-4)."'");
  $this->db->query("DELETE FROM items_checker_ref  WHERE refTable2='".substr($row->name,0,-4)."'");
  $this->db->query("DELETE FROM items_checker_ref  WHERE refTable3='".substr($row->name,0,-4)."'");
  $this->db->query("DELETE FROM item_views_ref     WHERE refTable3='".substr($row->name,0,-4)."'");
  $this->deleteDir(getcwd()."/purging/restore_point/".substr($row->name,0,-4));	
  $this->deleteDir(getcwd()."/purging/restore_point_preview/".substr($row->name,0,-4));	  
  $this->deleteDir(getcwd()."/purging/restore_point_upload_checker/".substr($row->name,0,-4));
  if($result==TRUE) redirect(HTTP_PATH.'itemDatabase/restore_points/restored', 'location', 301);
  else				redirect(HTTP_PATH.'itemDatabase/restore_points/not_all_restored', 'location', 301);
 }else{
  if(file_exists(getcwd()."/purging/restore_point/".substr($row->name,0,-4)))
   $this->deleteDir(getcwd()."/purging/restore_point/".substr($row->name,0,-4));
  
  redirect(HTTP_PATH."itemDatabase/restore_points/invalid_file/$id", 'location', 301);
 }
 
}

//SAVE TEMP ITEMS 
function load_data_local_file($src_file='',$table='',$ref='')
{
 //SAVE TO TEMP TABLE
  if($table=="items_checker_ref")
  {
  $this->db->query("LOAD DATA INFILE '$src_file' IGNORE 
				   INTO TABLE $table
				   FIELDS TERMINATED BY '	'
				   LINES TERMINATED BY '\r\n'
				   (`id`,`itemCode`,
					`POSMTypeID`,`POSMStatusID`,`OUTLETStatusID`,`PremiumTypeID`,`MaterialTypeID`,`countryID`,`brandID`,`publish_other_country`,`itemName`,`Photo`,`publish`,`irrelevant`,
					`popular`,`archive`,`purge`,`Short_Description`,`Long_Description`,`UnitPrice`,`USD_Price`,`price_rangeID`,`MOQ`,`UOM`,`country_of_origin`,`dateAdded`,`dateReleased`,`dateLastEdited`,`user_id`,
					`Fields0001`,`Fields0002`,`Fields0003`,`Fields0004`,`Fields0005`,`estimated_production_lead_time`,`price_validity`,`plant_inventory`,`supplier_stock_on_hand`,
					`date_first_issue`,`date_last_used`,`activity_event_use`,`num_views`,`refTable3`)
				     SET refTable3='$ref'");
  }
  elseif($table=="images_checker_ref")
  {
  //INSERT TEMP IMAGES
	$this->db->query("LOAD DATA INFILE '$src_file' IGNORE 
				      INTO TABLE images_checker_ref
				      FIELDS TERMINATED BY '	'
				      LINES TERMINATED BY '\r\n'
				      (id, itemID, temporary_file, defaultStatus, refTable3)
				      SET refTable3 = '$ref'");
  }
  elseif($table=="item_views_ref")
  {
  //INSERT TEMP IMAGES
	$this->db->query("LOAD DATA INFILE '$src_file' IGNORE 
				      INTO TABLE $table
				      FIELDS TERMINATED BY '	'
				      LINES TERMINATED BY '\r\n'
				      (id, itemID, user_id, date_time, refTable3)
				      SET refTable3 = '$ref'");
  }
}


function restore_seletected_items($ref='')
{
//GET ALL INSERTED DATA
$items_from_archive = $this->db->query("SELECT id as items_checker_refID FROM items_checker_ref WHERE refTable3='$ref'");
$items_from_archive = $items_from_archive->result_array();

$list_items="";
foreach($items_from_archive as $archive_item)
{ extract($archive_item);
  //GET ALL ITEMS FROM 
  echo "SELECT id FROM items WHERE id = $items_checker_refID";
  $db_item = $this->db->query("SELECT id FROM items WHERE id = $items_checker_refID");
  $db_item = $db_item->result_array();
  
  //IF DOES NOT EXIST
  if(count($db_item)==0)	$list_items .= "$items_checker_refID,";
}

//RESTORE ONLY THE LIST ITEMS
$list_items = substr($list_items,0,-1);
//SELECT INSERT ITEMS
$this->db->query("INSERT INTO items
			   (`id`,`itemCode`,
				`POSMTypeID`,`POSMStatusID`,`OUTLETStatusID`,`PremiumTypeID`,`MaterialTypeID`,`countryID`,`brandID`,`publish_other_country`,`itemName`,`Photo`,`publish`,`irrelevant`,
				`popular`,`archive`,`purge`,`Short_Description`,`Long_Description`,`UnitPrice`,`USD_Price`,`price_rangeID`,`MOQ`,`UOM`,`country_of_origin`,`dateAdded`,`dateReleased`,`dateLastEdited`,`user_id`,
				`Fields0001`,`Fields0002`,`Fields0003`,`Fields0004`,`Fields0005`,`estimated_production_lead_time`,`price_validity`,`plant_inventory`,`supplier_stock_on_hand`,
				`date_first_issue`,`date_last_used`,`activity_event_use`,`num_views`)
				SELECT 
				`id`,`itemCode`,
				`POSMTypeID`,`POSMStatusID`,`OUTLETStatusID`,`PremiumTypeID`,`MaterialTypeID`,`countryID`,`brandID`,`publish_other_country`,`itemName`,`Photo`,`publish`,`irrelevant`,
				`popular`,`archive`,`purge`,`Short_Description`,`Long_Description`,`UnitPrice`,`USD_Price`,`price_rangeID`,`MOQ`,`UOM`,`country_of_origin`,`dateAdded`,`dateReleased`,`dateLastEdited`,`user_id`,
				`Fields0001`,`Fields0002`,`Fields0003`,`Fields0004`,`Fields0005`,`estimated_production_lead_time`,`price_validity`,`plant_inventory`,`supplier_stock_on_hand`,
				`date_first_issue`,`date_last_used`,`activity_event_use`,`num_views`
				FROM items_checker_ref 
				WHERE items_checker_ref.id IN ($list_items) AND items_checker_ref.refTable3 = '$ref'");
$this->insert_logs($list_items,$ref);

//SELECT INSERT IMAGES
$this->db->query("INSERT INTO items_images
			   (`id`,`itemID`,`image`,`defaultStatus`)
				SELECT 
				`id`,`itemID`,`temporary_file`,`defaultStatus`
				FROM  images_checker_ref 
				WHERE images_checker_ref.itemID IN ($list_items) AND images_checker_ref.refTable3 = '$ref'");
$this->restore_imgs($list_items,$ref);
				
//SELECT INSERT VIEWS
$this->db->query("INSERT INTO item_views
			   (`id`,`itemID`,`user_id`,`date_time`)
				SELECT 
				`id`,`itemID`,`user_id`,`date_time`
				FROM  item_views_ref 
				WHERE item_views_ref.itemID IN ($list_items) AND item_views_ref.refTable3 = '$ref'");
				
			
if(COUNT($items_from_archive)==COUNT(EXPLODE(",",$list_items))) return "all";				
else															return "partial";
}

function insert_logs($list_items,$ref)
{
 $CI =& get_instance();
 $CI->load->library('rec_logs');
 $items = $this->db->query("SELECT items_checker_ref.id as iID, itemCode, itemName FROM items_checker_ref WHERE items_checker_ref.id IN
						   ($list_items) AND items_checker_ref.refTable3 = '$ref'");
 $items = $items->result_array();
 foreach($items as $item)
 { extract($item);
   $CI->rec_logs->w($iID,$itemName,'Item Database','Items','restore from disk',$itemCode);
 }
}

function restore_imgs($list_items='',$ref='')
{
$sql="SELECT temporary_file as image FROM images_checker_ref WHERE itemID IN  ($list_items) AND images_checker_ref.refTable3 = '$ref'";
//TEST & EXECUTE
$imgs = $this->db->query($sql);
$imgs = $imgs->result_array();
if($imgs){
 foreach($imgs as $img)
 { extract($img);
   copy(getcwd()."/purging/restore_point/$ref/big/$image",getcwd()."/img/big/$image");
   unlink(getcwd()."/purging/restore_point/$ref/big/$image");
   copy(getcwd()."/purging/restore_point/$ref/small/$image",getcwd()."/img/small/$image");
   unlink(getcwd()."/purging/restore_point/$ref/small/$image");
   copy(getcwd()."/purging/restore_point/$ref/galleryImg/$image",getcwd()."/img/galleryImg/$image");
   unlink(getcwd()."/purging/restore_point/$ref/galleryImg/$image"); 
   copy(getcwd()."/purging/restore_point/$ref/thumb/$image",getcwd()."/img/thumb/$image");
   unlink(getcwd()."/purging/restore_point/$ref/thumb/$image"); 
 }
}
}

//REMOVE DIRECTORY RECUSIVELY
function deleteDir($dirPath) {
//echo $dirPath;
if (! is_dir($dirPath)) {
	//throw new InvalidArgumentException("$dirPath must be a directory");
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

function Directory_File_Checker($dir='',$ref='')
{
 $error=0;
 //DIRECTORY CHECKER
 if(!file_exists($dir."/database") OR !file_exists($dir."/database/items.txt") OR !file_exists($dir."/database/items_images.txt"))   $error++;
 if(!file_exists($dir."/big")) 		  $error++;
 if(!file_exists($dir."/galleryImg")) $error++;
 if(!file_exists($dir."/small")) 	  $error++;
 if(!file_exists($dir."/thumb")) 	  $error++;
 //FILE CHECKER
 if(file_exists($dir."/database/items_images.txt"))
 {
 $userID = $_SESSION['user_id'];
 $action = 'temp file';
 $table  = substr($ref,0,-4);
 $date   = date('Y-m-d');
 $time   = date('H:i:s');
 $this->db->query("LOAD DATA INFILE '".$dir."/database/items_images.txt"."' IGNORE 
				   INTO TABLE images_checker_ref
				   FIELDS TERMINATED BY '	'
				   LINES TERMINATED BY '\r\n'
				   (id, itemID, temporary_file, refTable)
				   SET refTable = '$table'");
 $images = $this->db->query("SELECT temporary_file FROM images_checker_ref WHERE refTable='$table'");
 $images = $images->result_array();
 foreach($images as $img)
 { extract($img);
	if(!file_exists($dir."/big/".$temporary_file)) 		   $error++;
	if(!file_exists($dir."/galleryImg/".$temporary_file))  $error++;
	if(!file_exists($dir."/small/".$temporary_file)) 	   $error++;
	if(!file_exists($dir."/thumb/".$temporary_file)) 	   $error++;
 }
  //DELETE TEMP FILES
  $this->db->query("DELETE FROM images_checker_ref WHERE refTable='$table'");
  //CHECK FOR ERROR
  if($error==0)
  { 
   return TRUE;
  }else{		   
   return FALSE;
  }
 }

}

}