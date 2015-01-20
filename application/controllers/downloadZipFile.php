<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DownloadZipFile extends CI_Controller {
 
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
		$this->modules->session_handler();
		set_time_limit(0);
   }
   
	function db($aID)
	{
		$sql 	      = $this->db->query("SELECT * FROM archive_list WHERE id = $aID");
		$archive      = $sql->row();
		$startDate    = $archive->startDate;
		$endDate      = $archive->endDate;
		$archive_name = $archive->archive_name;
		$archive_path  = "";
		$archive_path = $_SERVER['DOCUMENT_ROOT']."/img_archive/".$archive_name."/";
		//$archive_path   = "C:/xampp/htdocs/smbi_dev/img_archive/$archive_name/";
		
		$databases = array(
					   array('file_name'=>'items.txt'),
					   array('file_name'=>'items_images.txt'),
					   array('file_name'=>'ec_items.txt'),
					   array('file_name'=>'ecitems_images.txt'));
		
		foreach($databases as $d)
		{ extract($d);
		  $files[] = $archive_path.'database/'.$file_name;
		}
		
		print_r($files);
		
		/*IMG + COVER*/
		$this->img_cover($files, $archive_path, $startDate, $endDate, $archive_name);
	}
	

	function img_cover($files, $archive_path, $startDate, $endDate, $archive_name)
	{
		/*IMAGES*/
		$this->archive = $this->load->database('archive',TRUE);
		
		$sql    = "SELECT image FROM items_images WHERE itemID IN(SELECT id FROM items WHERE items.dateAdded <= '$startDate' AND items.dateAdded >= '$endDate') LIMIT 0,100";
		$images	= $this->archive->query($sql);
		$images = $images->result_array();
				
		$image_folder_container = array(array('img_folder'=>'big/'),
										array('img_folder'=>'small/'),
										array('img_folder'=>'galleryImg/'),
										array('img_folder'=>'thumb/'));
		foreach($images as $i)
		{ extract($i);
		  foreach($image_folder_container as $ifc){
		    extract($ifc);
				$files[] .= $archive_path.$img_folder.$image;
		  }
		}
		
		//print_r($files);
		$this->generate_zipFile($files, $archive_path);
	}

	
	function generate_zipFile($files,$zip_name)
	{
		$valid_files = array();
		if(is_array($files)) {
			foreach($files as $file) {
				if(file_exists($file)) {
					$valid_files[] = $file;
				}else{
					echo "Warning: Does not exist $file <br/>";
				}
			}
		}
		
		$zip_name = $zip_name.'.zip';
		
		
		if(count($valid_files > 0)){
			echo "Please wait for files to be downloaded ....";
		
			$zip = new ZipArchive();
		
			if($zip->open($zip_name, ZIPARCHIVE::CREATE)!==TRUE){
				echo $error .= "* Sorry ZIP creation failed at this time";
			}
			
			foreach($valid_files as $file){
				$zip->addFile($file);
			}
			
			
			$zip->close();
			if(file_exists($zip_name)){
				// force to download the zip
				header("Pragma: public");
				header("Expires: 0");
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
				header("Cache-Control: private",false);
				header('Content-type: application/zip');
				header('Content-Disposition: attachment; filename="'.$zip_name.'"');
				readfile($zip_name);
				// remove zip file from temp path
				unlink($zip_name);
			}else{
				echo "Zip file doesn't exist: $zip_name";			
			}

		} else {
			echo "No valid files to zip";
			exit;
		}
		
		
	}
	
}