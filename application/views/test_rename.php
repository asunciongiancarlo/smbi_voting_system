<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>San Miguel International Beer</title>
</head>
<?php 
	$CI =& get_instance();
	$CI->load->database('default');
	$items = $CI->db->query("SELECT id AS itemID, itemCode FROM items WHERE id='1982' LIMIT 0,5");
	$items = $items->result_array();
	$items_images = "";
	$number_of_items=0;
	foreach($items as $item)
	{ extract($item);
	  $items_images = $CI->db->query("SELECT image, items_images.id as imgID FROM items_images WHERE itemID = '$itemID'");
	  $items_images = $items_images->result_array();
	  foreach($items_images as $item_image)
	  { extract($item_image);
	    $new_filename 	= generateImgCode($itemID,$itemCode,$image);
	    echo $Thumbfrom = getcwd()."/img/thumb/$image"; echo "<br/>";
		echo $Thumbto   = getcwd()."/img/thumb/$new_filename"; echo "<br/>";
		copyImg($Thumbfrom,$Thumbto);
		echo $Smallfrom 	 	= getcwd()."/img/small/$image"; echo "<br/>";
		echo $Smallto   	 	= getcwd()."/img/small/$new_filename"; echo "<br/>";
		copyImg($Smallfrom,$Smallto);
		echo $GalleryImgfrom = getcwd()."/img/galleryImg/$image"; echo "<br/>";
		echo $GalleryImgto   = getcwd()."/img/galleryImg/$new_filename"; echo "<br/>";
		copyImg($GalleryImgfrom,$GalleryImgto);
		echo $Bigfrom 		= getcwd()."/img/big/$image"; echo "<br/>";
		echo $Bigto   		= getcwd()."/img/big/$new_filename"; echo "<br/>";
		copyImg($Bigfrom,$Bigto);
		$CI->db->query("UPDATE items_images SET image='$new_filename' WHERE id='$imgID'");
	  }
	  $number_of_items++;
	}
	
	echo "No of items: $number_of_items";
	
	function copyImg($from,$to)
	{
	 if(file_exists($from))
	 {echo " - exist! <br/> ";
		copy($from,$to);
	 }
	}
	
	function generateImgCode($itemID='',$itemCode='',$file_name='')
	{ 
	  $CI =& get_instance();
	  $CI->load->database('default');
	  $ctr = 0;
	  $new_filename = "";
	  $row = $CI->db->query("SELECT COUNT(id) as ctr FROM items_images WHERE itemID = $itemID");
	  $row = $row->row();
	  $ctr = $row->ctr++;
	  $file_name = substr($file_name,-4);
	  //CHECK IF EXIST
	 
	  $sql =  $CI->db->query("SELECT COUNT(id) as ctr FROM items_images WHERE itemID = $itemID AND image = '$new_filename'");
      $sql =  $sql->row();
	  if($sql->ctr==1) $ctr = $ctr++;
	  $new_filename = $itemCode."-".$ctr.$file_name;
	  
	  $nameChecker=false;
	  $present = "";
	  while($nameChecker!=true)
	  {
	   //DOUBLE CHECK IF EXIST
	   $sql =  $CI->db->query("SELECT COUNT(id) as present FROM items_images WHERE itemID = $itemID AND image = '$new_filename'");
       $sql =  $sql->row();
       $present =  $sql->present;
	   if($present>=1){ 
	    $ctr = $ctr++;
		$new_filename = $itemCode."-".$ctr.$file_name;
		$nameChecker=false;
	   }else{
	    $nameChecker=true;
	    return $new_filename = $itemCode."-".$ctr.$file_name;
	   }
	  }
	}

?>
