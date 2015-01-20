<!doctype html>
<?php   extract($item[0]); ?>
<html>
  <head>
      <title>Panzoom for jQuery</title>
		<link rel="stylesheet" type="text/css" href="<?php echo HTTP_PATH ?>css/custom2.css">
		<link rel="stylesheet" type="text/css" href="<?php echo HTTP_PATH ?>css/custom.css">
		<link rel="stylesheet" type="text/css" href="<?php echo HTTP_PATH ?>css/itemsDB.css">
		<link rel="stylesheet" type="text/css" href="<?php echo HTTP_PATH ?>css/admin.css">
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
      <style type="text/css">
          body { background: #ffffff;  }
          section { text-align: center; margin: 50px 0; }
         .panzoom-parent { border: 2px solid #333333; }
         .panzoom-parent .panzoom { border: 2px dashed #666; }
         .buttons { margin: 40px 0 0; }
		 
		 strong{
			color: #710002;
			font-size: 13px;
		 }
      </style>
      <script src="<?php echo HTTP_PATH?>js/jquery-1.8.3.min.js"></script>
      <script src="<?php echo HTTP_PATH?>js/jquery.panzoom.js"></script>
      <script src="<?php echo HTTP_PATH?>js/jquery.mousewheel.js"></script>
      <link rel="stylesheet" type="text/css" href="<?php echo HTTP_PATH?>css/itemsDB.css">
  </head>
  <body style='height:513px;'>
  <table style='margin:0 auto;margin-top:10px;' border='0' width='100%'>
	<tr>
		<td valign='top'   style='text-align:left;width:60%;'> 
			<center>
				<section style='margin:0'>
				  <div  style='width: 60%;border:0;margin-left: 150px;'>
				  <div class="buttons">
					<button class="zoom-in">Zoom In </button>
					<button class="zoom-out">Zoom Out</button>
					<input type="range" class="zoom-range">
					<button class="reset">Reset</button>
				  </div>
				   <hr>
				  <div class="parent">
					<div class="panzoom">
					  <img  src="<?php echo HTTP_PATH . "img/big/$selImg"?>" width="100%">
					</div>
				  
				  </div>
					
				  </div>
				  
				  <div  style='float:left;border:1px solid black'></div>
				  <script>
					(function() {
					  var $section = $('section').first();
					  $section.find('.panzoom').panzoom({
						$zoomIn: $section.find(".zoom-in"),
						$zoomOut: $section.find(".zoom-out"),
						$zoomRange: $section.find(".zoom-range"),
						$reset: $section.find(".reset")
					  });
					})();
				  </script>
				</section>
			<center>
		</td>
		<!-- ------>
		<td style='text-align:left;margin-top:20px;padding-top:80px;width:40%;' valign='top'>
		
		<?php
			if(isset($item[0])){
			
				$CI2 =& get_instance();
				$CI2->load->library('fv');
				
				//ITEM NAME
				if($CI2->fv->fieldChecker($POSMStatusID,12)=='y')
					echo "<strong> ". $CI2->fv->label(11) ." </strong>: $itemName <br/>";
				
				//SHORT DESCRIPTION
				if($CI2->fv->fieldChecker($POSMStatusID,13)=='y')
					echo "<strong> ". $CI2->fv->label(12) ." </strong>: <br/>$Short_Description <br/>";

			}else{
				echo "<strong style='margin-top:300px;'> Sorry, There is no information on this item. </strong>";
			}
		?>
		

		</td>
		</tr>
		</table>
		
  </body>
</html>
