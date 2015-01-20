<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>San Miguel International Beer</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="San Miguel International Beer">
<!-- CSS -->

<link rel="stylesheet" type="text/css" href="<?php echo HTTP_PATH ?>css/bootstrap.css">
<link rel="stylesheet" type="text/css" href="<?php echo HTTP_PATH ?>css/custom2.css">
<link rel="stylesheet" type="text/css" href="<?php echo HTTP_PATH ?>css/custom.css">
<link rel="stylesheet" type="text/css" href="<?php echo HTTP_PATH ?>css/itemsDB.css">
<link rel="stylesheet" type="text/css" href="<?php echo HTTP_PATH ?>css/admin.css">

<!--[if IE]>
<style>/* this style block is for IE */


.item-galla {*width:68%!important;}
.items-fil{ *margin-top:0px!important; }
.searhPanel { *margin-left:60px!important; *margin-top:3px!important; }
.pagination { *padding-top: 10px!important;  *padding-bottom: 10px!important;  *padding-left:15px!important; *padding-right:15px!important;}
.firstnum { *margin-right:-15px!important; }


</style>
<![endif]-->

<script type="text/javascript" src="<?php echo HTTP_PATH ?>js/jquery-1.8.0.js"></script>

<script src="<?php echo HTTP_PATH ?>parsely/parsley_new.js"></script>

<link rel="stylesheet" href="<?php echo HTTP_PATH ?>datepicker/jquery_ui.css" type="text/css" media="all" />
<script type="text/javascript" src="<?php echo HTTP_PATH ?>datepicker/jquery_ui.js"></script>
<script type="text/javascript" src="<?php echo HTTP_PATH ?>js/ajax.js"></script>
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
   <!-- <div class="search-box">  
    	<input name="" type="text" class="search-input">    
        <input name="" type="button" class="search-btn">  	
	</div> -->
        
    <div class="breadcrums" style='text-align:left'>
    	<ul>
        	<li><a href="<?php echo HTTP_PATH ?>"> Home </a></li>
			<li><img src="<?php echo HTTP_PATH ?>img/arrow.png" width="3" height="5"></li>
			<?php echo $breadCrumbs ?> 
     	</ul>
  	</div>
	
    
    <div class="clear"></div>
    
	<?php include($vfile); ?>
    
</div>
<br/><br/><br/>

<script src="<?php echo HTTP_PATH ?>js/bootstrap.min.js" type="text/javascript"></script>
<script>
	$(function() {
		$( "#datepicker" ).datepicker(); 
		$( "#datepicker2" ).datepicker();
		$( "#datepicker3" ).datepicker();
		$( "#datepicker4" ).datepicker();
		$( "#datepicker4" ).datepicker();
		$( "#DateFrom" ).datepicker();
		$( "#DateTo" ).datepicker();
  });
</script>
<link rel="stylesheet" href="<?php echo HTTP_PATH ?>css/jquery.alert.css">
<script src="<?php echo HTTP_PATH ?>js/jquery.alert.js"></script>
<script src="<?php echo HTTP_PATH?>js/jquery.elevateZoom-3.0.8.min.js"></script>
<script>
    $("#zoom").elevateZoom({ensSize:30,gallery:'imageSlider', cursor: 'pointer', galleryActiveClass: 'active', loadingIcon: 'http://www.elevateweb.co.uk/spinner.gif'}); 
</script>	

<script type="text/javascript" src="<?php echo HTTP_PATH ?>js/jquery.tablesorter.js"></script>
<script type="text/javascript" src="<?php echo HTTP_PATH ?>js/jquery.tablesorter.widgets.js"></script>
<script type="text/javascript" src="<?php echo HTTP_PATH ?>js/jquery.tablesorter.pager.js"></script>

<script type="text/javascript">

$(document).ready(function() { 
    $("table") 
    .tablesorter({widthFixed: true, widgets: ['zebra']}) 
    <?php if(isset($pagination)){ ?>
	.tablesorterPager({
      // target the pager markup - see the HTML block below
      container: $(".pager2"),
       output: '{startRow} to {endRow} ({totalRows})'
    })
	<?php } ?>
	;

}); 
</script>
<?php if(isset($jUi)){ ?>
	<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
	<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<?php } ?>

<?php if(isset($RESTORE_POINT)){ ?>
<script type="text/javascript" src="<?php echo HTTP_PATH."js/plupload.full.min.js" ?>"></script>
<script type="text/javascript">
// Custom example logic
var uploader = new plupload.Uploader({
	runtimes : 'html5,browserplus,silverlight,flash,gears,html4',
	browse_button : 'pickfiles', // you can pass in id...
	container: document.getElementById('container'), // ... or DOM Element itself
	url : '<?php echo HTTP_PATH.'itemDatabase/restore_pointsResources' ?>',
	max_file_count: 1,
	multi_selection: false,
	prevent_duplicates:true,
	flash_swf_url : '<?php echo HTTP_PATH."js/plupload.full.min.js/Moxie.swf" ?>',
	silverlight_xap_url : '<?php echo HTTP_PATH."js/Moxie.xap" ?>',
	
	filters : {
		max_file_size : "100000mb",
		mime_types: [
			{title : "Zip files", extensions : "zip"}
		]
	},

	init: {
		PostInit: function() {
			document.getElementById('filelist').innerHTML = '';

			document.getElementById('uploadfiles').onclick = function() {
				uploader.start();
				return false;
			};
		},

		FilesAdded: function(up, files) {
			plupload.each(files, function(file) {
				document.getElementById('filelist').innerHTML += '<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></div>';
			});
		},

		UploadProgress: function(up, file) {
			document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
			document.getElementById('loading').style.display = 'block';
		},
		
		UploadComplete: function(up, file) {
			window.location = "<?php echo HTTP_PATH.'itemDatabase/restore_points/restore_point_save' ?>";
			document.getElementById('plComplete').innerHTML = '<span>' + file.percent + "%</span>";
		},

		Error: function(up, err) {
			document.getElementById('console').innerHTML += "\nError #" + err.code + ": " + err.message;
		}
	}
});
 
 
 
uploader.init();
</script>
<?php } ?>
</body>
</html>
