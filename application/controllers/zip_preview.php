<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Zip_preview extends CI_Controller {
 
   public function __construct()
   {
		parent::__construct();
		date_default_timezone_set('UTC');
		session_start();
		$this->load->model('c3model');
		$this->load->library('security');
		$this->load->library('modules');
		$this->load->library('rec_logs');
		//$this->load->library('unzip');
		$this->load->helper('url');
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
            //echo "save: ".$file_name."<br />";
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

function readFile($id='')
{
 $CI =& get_instance();
 $CI->load->library('rec_logs');
 //UNZIP FILE
 $row = "";
 $sql = $this->db->query("SELECT name FROM restore_point WHERE id = $id");
 $row = $sql->row();
 $src_file = getcwd()."/purging/restore_point/".$row->name;
 //echo "<br/>";
 $dest_dir = getcwd()."/purging/restore_point_preview/".substr($row->name,0,-4)."/";
 //CLEAN EXISTING DB + FILE DIRECTORY
 $this->db->query("DELETE FROM images_checker_ref WHERE refTable2='".substr($row->name,0,-4)."'");
 $this->db->query("DELETE FROM items_checker_ref  WHERE refTable2='".substr($row->name,0,-4)."'");
 $this->deleteDir(getcwd()."/purging/restore_point_preview/".substr($row->name,0,-4));
 //echo "uzip: ".$this->unzip($src_file,$dest_dir);
 if($this->unzip($src_file,$dest_dir)==true AND $this->Directory_File_Checker($dest_dir,substr($row->name,0,-4))==true)
 {
  //ITEMS
  if(file_exists($dest_dir."/database/items.txt"))
   $this->load_data_local_file($dest_dir."/database/items.txt","items_checker_ref",substr($row->name,0,-4));
  if(file_exists($dest_dir."/database/items_images.txt"))
   $this->load_data_local_file($dest_dir."/database/items_images.txt","images_checker_ref",substr($row->name,0,-4));

  $this->zipGlimpse(substr($row->name,0,-4));
 }else{
	echo "Sorry invalid restore point, kindly check if file is not corrupted or modified and the items that you are uploading already exist.";
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

function zipGlimpse($dest_folder)
{
$sqlSTr =  "SELECT itemCode, itemName,
			POSM_Type.typeName as POSM_TypeName,
			POSM_Status.statusName as POSMStatusName,
			(SELECT temporary_file FROM images_checker_ref WHERE refTable2 = '$dest_folder' AND 
			itemID = items_checker_ref.id LIMIT 0,1) AS img, items_checker_ref.dateAdded,  dateReleased, publish, full_name
			FROM items_checker_ref 
			LEFT JOIN POSM_Type 		 ON items_checker_ref.POSMTypeID   	  = POSM_Type.id 
			LEFT JOIN POSM_Status 		 ON items_checker_ref.POSMStatusID 	  = POSM_Status.id 
			LEFT JOIN country 			 ON items_checker_ref.countryID 	  = country.id 
			LEFT JOIN images_checker_ref ON items_checker_ref.id 		  	  = images_checker_ref.itemID
			LEFT JOIN admin_users 		 ON admin_users.id 		  	  		  = items_checker_ref.user_id
			WHERE items_checker_ref.refTable2 = '$dest_folder'
			GROUP BY itemCode
			ORDER BY publish DESC";

$sql = $this->db->query($sqlSTr);
$sql = $sql->result_array();
echo "<table id='large' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:11px;' class='iLike_Result_Table tablesorter'>
		<thead>
		<tr>
			<th style='width:10px;text-align:center;color:white;padding:0px' bgcolor='#bb1d1d'>   					   <b>No 		 		  </b></th>
			<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Image  	      	  </b></th> 
			<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Item Code  	      </b></th> 
			<th style='width:105px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Name  	  		  </b></th>
			<th style='width:19px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Item Status   	  </b></th> 
			<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Item Type  	  	  </b></th> 
			<th style='width:19px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Published   	  	  </b></th> 
			<th style='width:19px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Uploaded By   	  </b></th> 
			<th style='width:19px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Date Uploaded   	  </b></th> 
			<th style='width:19px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Date Published   	  </b></th> 
			
		</tr>
		</thead>
		<tbody>";
				$x = 0;			
				foreach($sql as $r) { 
				extract($r);
				$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 	
				$publish = ($publish=='y') ? "Yes":"No";
				$publish = ($publish=='y') ? "Yes":"No";
	echo 	"<tr>
			  <td $c>											$x    		</td>
			  <td $c style='text-align:center;'> ";
				if($img!="") echo "<img src='".HTTP_PATH."purging/restore_point_preview/$dest_folder/thumb/$img' style='width:30px;height:30px'> ";	
				else 		 echo "<img src='".HTTP_PATH."img/thumb/blank.png' style='width:30px;height:30px'> ";	
	echo 	"</td>
			  <td $c style='text-align:left;padding-left:5px;'>	$itemCode		 </td>
			  <td $c style='text-align:left;padding-left:5px;'>	$itemName		 </td>
			  <td $c style='text-align:left;padding-left:5px;'>	$POSMStatusName  </td>
			  <td $c style='text-align:left;padding-left:5px;'>	$POSM_TypeName   </td>
			  <td $c style='text-align:left;padding-left:5px;'>	$publish  		 </td>
			  <td $c style='text-align:left;padding-left:5px;'>	$full_name  	 </td>
			  <td $c style='text-align:left;padding-left:5px;'>	$dateAdded  	 </td>
			  <td $c style='text-align:left;padding-left:5px;'>	$dateReleased    </td>
			</tr>";}
	echo	"</tbody>";
				if(!$sql) echo "<tr><td colspan='9'>No match found.</td></tr>";
	echo	"</table>";
}

//RESTORE ITEMS
function load_data_local_file($src_file='',$table='',$ref='')
{
 //SAVE TO REF		 
 if($table=='items_checker_ref'){
 $this->db->query("LOAD DATA INFILE '$src_file' IGNORE 
				   INTO TABLE items_checker_ref
				   FIELDS TERMINATED BY '	'
				   LINES TERMINATED BY '\r\n'
				   (`id`,`itemCode`,
					`POSMTypeID`,`POSMStatusID`,`OUTLETStatusID`,`PremiumTypeID`,`MaterialTypeID`,`countryID`,`brandID`,`publish_other_country`,`itemName`,`Photo`,`publish`,`irrelevant`,
					`popular`,`archive`,`purge`,`Short_Description`,`Long_Description`,`UnitPrice`,`USD_Price`,`price_rangeID`,`MOQ`,`UOM`,`country_of_origin`,`dateAdded`,`dateReleased`,`dateLastEdited`,`user_id`,
					`Fields0001`,`Fields0002`,`Fields0003`,`Fields0004`,`Fields0005`,`estimated_production_lead_time`,`price_validity`,`plant_inventory`,`supplier_stock_on_hand`,
					`date_first_issue`,`date_last_used`,`activity_event_use`,`num_views`,`refTable2`)
				     SET refTable2='$ref'");
 }
 else
 {
	$this->db->query("LOAD DATA INFILE '$src_file' IGNORE 
					  INTO TABLE images_checker_ref
					  FIELDS TERMINATED BY '	'
					  LINES TERMINATED BY '\r\n'
					  (id, itemID, temporary_file, refTable2)
					  SET refTable2 = '$ref'");
 }
}

function Directory_File_Checker($dir='',$ref='')
{
 //echo $dir;
 $error=0;
 //DIRECTORY CHECKER
 //echo $dir."database";
 if(!file_exists($dir."database") OR !file_exists($dir."database/items.txt") OR !file_exists($dir."database/items_images.txt"))   $error++;
 if(!file_exists($dir."big")) 		  $error++;
 if(!file_exists($dir."galleryImg"))  $error++;
 if(!file_exists($dir."small")) 	  $error++;
 if(!file_exists($dir."thumb")) 	  $error++;
 //echo "Dir $error";
 if(file_exists($dir."database/items_images.txt"))
 {
 $userID = $_SESSION['user_id'];
 $action = 'temp file';
 $table  = $ref;
 $date   = date('Y-m-d');
 $time   = date('H:i:s');
 $this->db->query("LOAD DATA INFILE '".$dir."database/items_images.txt"."' IGNORE 
				   INTO TABLE images_checker_ref
				   FIELDS TERMINATED BY '	'
				   LINES TERMINATED BY '\r\n'
				   (id, itemID, temporary_file, refTable)
				   SET refTable = '$table'");
 //SELECT DISTINCT ITEM ID
 $purge_items = $this->db->query("SELECT DISTINCT(itemID) as distinctIDs FROM images_checker_ref WHERE refTable='$table'");
 $purge_items = $purge_items->result_array();
 $purge_items_ctr = count($purge_items);
 
 //CHECK IF ITEMS ARE IN THE ITEM DB
 $orig_items = $this->db->query("SELECT id FROM items WHERE id IN (SELECT DISTINCT(itemID) as distinctIDs FROM images_checker_ref WHERE refTable='$table')");
 $orig_items = $orig_items->result_array();
 $orig_items_ctr = count($orig_items);
 
 if($purge_items_ctr==$orig_items_ctr) $error++;
 
 $images = $this->db->query("SELECT temporary_file FROM images_checker_ref WHERE refTable='$table'");
 $images = $images->result_array();
 foreach($images as $img)
 { extract($img);
	if(!file_exists($dir."big/".$temporary_file)) 		   $error++;
	if(!file_exists($dir."galleryImg/".$temporary_file))   $error++;
	if(!file_exists($dir."small/".$temporary_file)) 	   $error++;
	if(!file_exists($dir."thumb/".$temporary_file)) 	   $error++;
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