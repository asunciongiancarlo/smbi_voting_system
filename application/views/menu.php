<!DOCTYPE HTML>
<?php 
		$CI2=& get_instance();
		$CI2->load->library('modules');
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>San Miguel International Beer</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="San Miguel International Beer">
<!-- CSS -->
<link rel="stylesheet" type="text/css" href="<?php echo HTTP_PATH ?>css/bootstrap.css">
<link rel="stylesheet" type="text/css" href="<?php echo HTTP_PATH ?>css/custom.css">
<link rel="stylesheet" type="text/css" href="<?php echo HTTP_PATH ?>css/itemsDB.css">
<!--[if lt IE 9]>
	<script src="<?php echo HTTP_PATH ?>http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
</head>
<body data-spy="scroll" data-target=".bs-docs-sidebar" data-twttr-rendered="true">
<div class="container">
	<header>
    	<div class="logo">
       		<a href='<?php echo HTTP_PATH ?>'><img src="<?php echo HTTP_PATH ?>img/logo.png" width="420" height="45"></a>
        </div>
        <div class="nav-top">
        	<a href="<?php echo HTTP_PATH ?>" class="nav-btn" >Home</a>
            <a href="<?php echo HTTP_PATH ?>users/personal_account/view/<?php echo $_SESSION['user_id']; ?>" class="nav-btn" >My Account</a>
      	</div>
        <div class="clear"></div>
		<p class='acc_info'>
			Welcome <?php echo $_SESSION['full_name'];  
			if($_SESSION['super_admin']=='y') echo " (Super Admin)"; ?>! | <a href='<?php echo HTTP_PATH.'login/logout' ?>' style='color:white;'>Log-out</a>
			<br/> <?php echo $_SESSION['countryName']; ?>
		</p>
	</header>
 <table border=0 width='100%'>
   <tr> 
     <td valign='top' width='220' style='padding:0'>
	   <div class='bannerAdds'>
	   <h3>Featured Items </h3>
        <?php
		  foreach($featured1 as $f)
		    {
			  extract($f);
			  if(!$img) $img = 'blank.png';
				 $w = w($img);
				  echo "<div class='itemBOX'>";
					echo "<div  class='dtl' style='background:#999999;color:white;font-weight:bold;'> $POSM_Type_Name </div>";
					 echo "<div class='img' style='height:160px;width:200px;over-flow:hidden;'>
							<table>
								<tr>
									<td class='gal-Icon-Container'><a href='".HTTP_PATH."gallery/itemInfo2/$itemID'><img class='gal-Icon-Img' src='".HTTP_PATH."img/galleryImg/$img' style='$w'></a> </td>
								</tr>
							</table>	
						  </div>";
				    
					echo "<div  class='title' style='text-align:center;color:#bb4041;' title='$itemName'> <b>"; 
						
							if(strlen($itemName)>=20)
								echo substr($itemName,0,20)."...";
							else	
								echo $itemName;
									
					echo"</b> </div>";
				  echo "</div>";
			 
			}
		?>
	   </div>
	 </td>
     <td valign='top' style='padding-left:20px'> 
		<div class="breadcrums" style='text-align:left'>
    	<ul>
        	<li><a href="<?php echo HTTP_PATH ?>"> Home </a></li>
			<li><img src="<?php echo HTTP_PATH ?>img/arrow.png" width="3" height="5"></li>
			<?php echo $breadCrumbs ?> 
     	</ul>
  	</div>
	<?php include($vfile); ?> 
    
     </td>
     <td valign='top' width='220'  style='padding:0'>
	 <div class='bannerAdds'>
	   <h3>Featured Items </h3>
        <?php
		  foreach($featured2 as $f)
		    {
			 extract($f);
			  if(!$img) $img = 'blank.png';
				 $w = w($img);
				  echo "<div class='itemBOX'>";
					echo "<div  class='dtl' style='background:#999999;color:white;font-weight:bold;'> $POSM_Type_Name </div>";
					 echo "<div class='img' style='height:160px;width:200px;over-flow:hidden;'>
							<table>
								<tr>
									<td class='gal-Icon-Container'><a href='".HTTP_PATH."gallery/itemInfo2/$itemID'><img class='gal-Icon-Img' src='".HTTP_PATH."img/galleryImg/$img' style='$w'></a> </td>
								</tr>
							</table>	
						  </div>";
				    
					echo "<div  class='title' style='text-align:center;color:#bb4041;' title='$itemName'> <b>"; 
						
							if(strlen($itemName)>=20)
								echo substr($itemName,0,20)."...";
							else	
								echo $itemName;
									
					echo"</b> </div>";
				  echo "</div>";
			 
			}
		?>
	   </div>
	 </td>
   </tr>
 </table>
 <?php 
	function w($img)
	{
		$w='';
		$HTTP_PATH = getcwd()."/img/galleryImg/$img";
		list($width, $height, $type, $attr) = getimagesize("$HTTP_PATH");
		if($width>$height)
			return $w='width:100%';
		else
			return $w;
	}
?>
 
</div>
<br/>
</body>
<script src="<?php echo HTTP_PATH ?>js/bootstrap.min.js" type="text/javascript"></script>
</html>
