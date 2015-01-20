<!DOCTYPE HTML>
<?php 
		$CI2=& get_instance();
		$CI2->load->library('modules');
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>San Miguel International Beer</title>
<meta name="Viewport" content="width=device-width, initial-scale=1.0">
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
	<div style="height:20px;"></div>
 <table border=0 width='100%'>
   <tr> 
     <td valign='top' style='padding:0' class="td-FI">
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
									<td class='gal-Icon-Container home-items'><a href='".HTTP_PATH."gallery/itemInfo2/$itemID'><img class='gal-Icon-Img' src='".HTTP_PATH."img/galleryImg/$img' style='$w'> </a></td>
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
			</ul>
		</div>

		<div class="clear"><br/></div>
     
	<?php 
	//MESSAGE ALERT
	$CI =& get_instance();
	if(isset($msg)){
		$CI->load->library('alert');
		echo $CI->alert->check($msg);
	}
	?>

	<?php  if($CI2->modules->link_checker(18,'ADD') == TRUE & $_SESSION['super_admin']!='y'){ ?>
    <a href="<?php echo HTTP_PATH ?>itemDatabase/items/add.html">
		<div class="quick-link">
			<ul>
			<li class="tabs"><div style="width:45px; height:45px;"><img src="<?php echo HTTP_PATH ?>img/upload-new.png" width="120" height="120"></div></li>
			<li class="tabs"> <h3 class="tabs-title">Upload New Item</h3> </li>
			</ul>
		</div>
	</a>
	
	<?php } if($CI2->modules->link_checker(43,'ADD') == TRUE){ ?>
    <a href="<?php echo HTTP_PATH ?>eCatalog/items/add">
		<div class="quick-link">
			<ul>
			<li class="tabs"><div style="width:45px; height:45px;"><img class="tabs-icon" src="<?php echo HTTP_PATH ?>img/upload-new.png" width="120" height="120"></div></li>
			<li class="tabs"> <h3 class="tabs-title">Upload <span style='text-transform:lowercase;'>e</span>Catalogue Item</h3> </li>
			</ul>
		</div>
	</a>
	
	<?php } if($CI2->modules->link_checker(34,'REVIEW') == TRUE){ ?>
	<a href="<?php echo HTTP_PATH ?>gallery/eCatalog.html">
		<div class="quick-link">
			<ul>
				<li><div style="width:45px; height:45px;"><img class="tabs-icon" src="<?php echo HTTP_PATH ?>img/e-catalogue.png" width="120" height="120"></div></li>
				<li> <h3 class="tabs-title"><span style='text-transform:lowercase;'>e</span>Catalogue</h3>  </li>
		 </ul>
		</div>
	</a>
	
	<?php } if($CI2->modules->link_checker(17,'REVIEW') == TRUE){ ?>
	<a href="<?php echo HTTP_PATH ?>itemDatabase">
		<div class="quick-link">
			<ul>
				<li><div style="width:45px; height:45px;margin-top:5px;"><img class="tabs-icon" src="<?php echo HTTP_PATH ?>img/new-account.png" width="120" height="120"></div></li>
				<li> <h3 class="tabs-title">Item Database</h3> </li>
			</ul>
		</div>
	</a>
	
	<?php } if($CI2->modules->link_checker(32,'REVIEW') == TRUE){ ?>
	<a href="<?php echo HTTP_PATH ?>gallery/common">
	 <div class="quick-link">
    	<ul>
    		<li><div style="width:45px; height:45px; margin-top:5px;"><img src="<?php echo HTTP_PATH ?>img/e-catalogue.png" width="120" height="120"></div></li>
	    	<li><h3 class="tabs-title">COMMON GALLERY</h3>  </li>
        </ul>
    </div>
	</a>
	
	<?php } if($CI2->modules->link_checker(27,'REVIEW') == TRUE){ ?>
	<a href="<?php echo HTTP_PATH ?>iLikeCampaign/votingCampaign">
		<div class="quick-link">
			<ul>
				<li><div style="width:45px; height:45px; margin-top:5px;"><img class="tabs-icon" src="<?php echo HTTP_PATH ?>img/i-like.png" width="120" height="120"></div></li>
				<li> <h3 class="tabs-title"><span style='text-transform:lowercase;'>i</span>Like Campaign</h3>  </li>
			</ul>
		</div>
	</a>
	
	<?php } if($CI2->modules->link_checker(35,'REVIEW') == TRUE){ ?>
	<a href="<?php echo HTTP_PATH ?>gallery/my_Gallery">
	 <div class="quick-link">
    	<ul>
    		<li><div style="width:45px; height:45px; margin-top:5px;"><img class="tabs-icon" src="<?php echo HTTP_PATH ?>img/e-catalogue.png" width="120" height="120"></div></li>
	    	<li><h3 class="tabs-title">MY GALLERY</h3> </li>
        </ul>
    </div>
	</a> 
	
	<?php } if($CI2->modules->link_checker(81,'REVIEW') == TRUE){ ?>
	<a href="<?php echo HTTP_PATH ?>gallery/popular_items_gallery">
	 <div class="quick-link">
    	<ul>
    		<li><div style="width:45px; height:45px; margin-top:5px;"><img class="tabs-icon" src="<?php echo HTTP_PATH ?>img/brand.png" width="120" height="120"></div></li>
	    	<li><h3 class="tabs-title">POPULAR ITEMS</h3> </li>
        </ul>
    </div>
	</a> 
	
	<?php } if($CI2->modules->link_checker(29,'REVIEW') == TRUE){ ?>
	<a href="<?php echo HTTP_PATH ?>iWantCampaign/iWant/">
		<div class="quick-link">
			<ul>
				<li><div style="width:45px; height:45px; margin-top:5px;"><img class="tabs-icon" src="<?php echo HTTP_PATH ?>img/i-want.png" width="120" height="120"></div></li>
				<li><h3 class="tabs-title"><span style='text-transform:lowercase;'>i</span>Want Campaign</h3></li>
			</ul>
		</div>
	</a>
	<?php } ?>

    <?php if($CI2->modules->link_checker(36,'REVIEW') == TRUE){ ?>
	<a href="<?php echo HTTP_PATH ?>report"> 
		<div class="quick-link">
			<ul>
				<li><div style="width:45px; height:45px; margin-top:5px;"><img class="tabs-icon" src="<?php echo HTTP_PATH ?>img/reports.png" width="120" height="120"></div></li>
				<li> <h3 class="tabs-title"> Analytics</h3></li>
			</ul>
		</div>
	</a>
	
	<?php } if($CI2->modules->link_checker(1,'REVIEW') == TRUE){ ?>
	<a href="<?php echo HTTP_PATH ?>users.html"> 
		<div class="quick-link">
			<ul>
				<li><div style="width:45px; height:45px; margin-top:5px;"><img class="tabs-icon" src="<?php echo HTTP_PATH ?>img/new-account.png" width="120" height="120"></div></li>
				<li><h3 class="tabs-title">Admin</h3> </li>
			</ul>
		</div>
	</a>
	<?php } ?>
	
	</td>
    <td valign='top' width='220' style='padding:0'>
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
									<td class='gal-Icon-Container home-items'><a href='".HTTP_PATH."gallery/itemInfo2/$itemID'><img class='gal-Icon-Img' src='".HTTP_PATH."img/galleryImg/$img' style='$w'> </a></td>
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
