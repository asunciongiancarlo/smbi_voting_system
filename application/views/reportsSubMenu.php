
<?php 
	$CI2=& get_instance();
	$CI2->load->library('modules');
?>
<?php if($CI2->modules->link_checker(46,'REVIEW') == TRUE){ ?>
<a href="<?php echo HTTP_PATH."report/logs" ?>">
	<div class="quick-link">
		<ul class="list_items">
			<li><div style="width:45px; height:45px;margin-top:5px;"><img src="<?php echo HTTP_PATH ?>img/role.png" width="120" height="120"></div></li>
			<li> <h3>SYSTEM LOGS<br/></h3> </li>
		</ul>
	</div>
</a>


<?php } if($CI2->modules->link_checker(47,'REVIEW') == TRUE){ ?>
<a href="<?php echo HTTP_PATH."report/iLike" ?>">
	<div class="quick-link">
		<ul class="list_items">
			<li><div style="width:45px; height:45px;margin-top:5px;"><img src="<?php echo HTTP_PATH ?>img/ilike.png" width="120" height="120"></div></li>
			<li> <h3><span style='text-transform:lowercase;'>i</span>LIKE CAMPAIGN </h3> </li>
		</ul>
	</div>
</a>
<?php } if($CI2->modules->link_checker(48,'REVIEW') == TRUE){ ?>
<a href="<?php echo HTTP_PATH."report/iWant" ?>">
	<div class="quick-link">
		<ul class="list_items">
			<li><div style="width:45px; height:45px;margin-top:5px;"><img src="<?php echo HTTP_PATH ?>img/role.png" width="120" height="120"></div></li>
			<li> <h3><span style='text-transform:lowercase;'>i</span>WANT CAMPAIGN </h3> </li>
		</ul>
	</div>
</a>
<?php } if($CI2->modules->link_checker(49,'REVIEW') == TRUE){ ?>
<a href="<?php echo HTTP_PATH."report/campaign_items_summary/iLike" ?>">
	<div class="quick-link">
		<ul class="list_items">
			<li><div style="width:45px; height:45px;margin-top:5px;"><img src="<?php echo HTTP_PATH ?>img/reports.png" width="120" height="120"></div></li>
			<li> <h3>WINNING ITEMS FROM <span style='text-transform:lowercase;'>i</span>LIKE CAMPAIGN </h3> </li>
		</ul>
	</div>
</a>
<?php  ?>

<?php } if($CI2->modules->link_checker(49,'REVIEW') == TRUE){ ?>
<a href="<?php echo HTTP_PATH."report/campaign_items_summary/iWant" ?>">
	<div class="quick-link">
		<ul class="list_items">
			<li><div style="width:45px; height:45px;margin-top:5px;"><img src="<?php echo HTTP_PATH ?>img/reports.png" width="120" height="120"></div></li>
			<li> <h3>WINNING ITEMS FROM <span style='text-transform:lowercase;'>i</span>WANT CAMPAIGN </h3> </li>
		</ul>
	</div>
</a>
<?php  ?>

<?php } if($CI2->modules->link_checker(75,'REVIEW') == TRUE){ ?>
<a href="<?php echo HTTP_PATH."report/voters_summary" ?>">
	<div class="quick-link">
		<ul class="list_items">
			<li><div style="width:45px; height:45px;margin-top:5px;"><img src="<?php echo HTTP_PATH ?>img/new-account.png" width="120" height="120"></div></li>
			<li> <h3>VOTERS SUMMARY			</h3> </li>
		</ul>
	</div>
</a>
<?php } ?>

<?php if($CI2->modules->link_checker(68,'REVIEW') == TRUE OR
		 $CI2->modules->link_checker(69,'REVIEW') == TRUE OR
		 $CI2->modules->link_checker(70,'REVIEW') == TRUE OR
		 $CI2->modules->link_checker(73,'REVIEW') == TRUE OR
		 $CI2->modules->link_checker(74,'REVIEW') == TRUE 
		){ ?>
<div class="item-database clear">
		<h3 id="item" style="color:white;font-size:18px;">GALLERIES</h3>
</div>
<div class='cl'></div>
<?php } ?>

<?php if($CI2->modules->link_checker(69,'REVIEW') == TRUE){ ?>
<a href="<?php echo HTTP_PATH."report/BU_activeness_index" ?>">
	<div class="quick-link">
		<ul class="list_items">
			<li><div style="width:45px; height:45px;margin-top:5px;"><img src="<?php echo HTTP_PATH ?>img/reports.png" width="120" height="120"></div></li>
			<li> <h3 style='color:white;'>ACTIVENESS OF BUSINESS UNITS </h3> </li>
		</ul>
	</div>
</a>
<?php } ?>

<?php if($CI2->modules->link_checker(68,'REVIEW') == TRUE){ ?>
<a href="<?php echo HTTP_PATH."report/BU_activeness_Users" ?>">
	<div class="quick-link">
		<ul class="list_items">
			<li><div style="width:45px; height:45px;margin-top:5px;"><img src="<?php echo HTTP_PATH ?>img/reports.png" width="120" height="120"></div></li>
			<li> <h3>ACTIVENESS OF USERS </h3> </li>
		</ul>
	</div>
</a>
<?php } ?>

<?php if($CI2->modules->link_checker(70,'REVIEW') == TRUE){ ?>
<a href="<?php echo HTTP_PATH."report/item_views" ?>">
	<div class="quick-link">
		<ul class="list_items">
			<li><div style="width:45px; height:45px;margin-top:5px;"><img src="<?php echo HTTP_PATH ?>img/view_watch_eye.png" width="120" height="120"></div></li>
			<li> <h3 style='color:white;'>  NUMBER OF VIEWS PER ITEM   </h3> </li>
		</ul>
	</div>
</a>
<?php } ?>

<?php if($CI2->modules->link_checker(78,'REVIEW') == TRUE){ ?>
<a href="<?php echo HTTP_PATH."report/myGallery_views" ?>">
	<div class="quick-link">
		<ul class="list_items">
			<li><div style="width:45px; height:45px;margin-top:5px;"><img src="<?php echo HTTP_PATH ?>img/view_watch_eye.png" width="120" height="120"></div></li>
			<li> <h3 style='color:white;'>  NUMBER OF VIEWS FROM MY GALLERY  </h3> </li>
		</ul>
	</div>
</a>
<?php } ?>

<?php if($CI2->modules->link_checker(79,'REVIEW') == TRUE){ ?>
<a href="<?php echo HTTP_PATH."report/commonGallery_views" ?>">
	<div class="quick-link">
		<ul class="list_items">
			<li><div style="width:45px; height:45px;margin-top:5px;"><img src="<?php echo HTTP_PATH ?>img/view_watch_eye.png" width="120" height="120"></div></li>
			<li> <h3 style='color:white;'> NUMBER OF VIEWS FROM COMMON GALLERY   </h3> </li>
		</ul>
	</div>
</a>
<?php } ?>

<?php if($CI2->modules->link_checker(76,'REVIEW') == TRUE){ ?>
<a href="<?php echo HTTP_PATH."report/item_summary" ?>">
	<div class="quick-link">
		<ul class="list_items">
			<li><div style="width:45px; height:45px;margin-top:5px;"><img src="<?php echo HTTP_PATH ?>img/chart_bar_down.png" width="120" height="120"></div></li>
			<li> <h3 style='color:white;'>ITEM DATABASE - Summary</h3> </li>
		</ul>
	</div>
</a>
<?php } ?>

<?php if($CI2->modules->link_checker(76,'REVIEW') == TRUE){ ?>
<a href="<?php echo HTTP_PATH."report2/price_range_summary" ?>">
	<div class="quick-link">
		<ul class="list_items">
			<li><div style="width:45px; height:45px;margin-top:5px;"><img src="<?php echo HTTP_PATH ?>img/chart_bar_down.png" width="120" height="120"></div></li>
			<li> <h3 style='color:white;'>PRICE CATEGORY - Summary</h3> </li>
		</ul>
	</div>
</a>
<?php } ?>

<!--<?php if($CI2->modules->link_checker(76,'REVIEW') == TRUE){ ?>
<a href="<?php echo HTTP_PATH."report/item_views" ?>">
	<div class="quick-link">
		<ul class="list_items">
			<li><div style="width:45px; height:45px;margin-top:5px; margin-right:10px;"><img src="<?php echo HTTP_PATH ?>img/view_watch_eye.png" width="120" height="120"></div></li>
			<li> <h5 style='color:white;font-size:15px;margin-left:10px;'>ITEM DATABASE VIEWS <br/> PER ITEM </h5> </li>
		</ul>
	</div>
</a>
<?php } ?>-->

<!--<?php if($CI2->modules->link_checker(73,'REVIEW') == TRUE){ ?>
<a href="<?php echo HTTP_PATH."report/eCatalogue_index" ?>">
	<div class="quick-link">
		<ul class="list_items">
			<li><div style="width:45px; height:45px;margin-top:5px; margin-right:10px;"><img src="<?php echo HTTP_PATH ?>img/reports.png" width="120" height="120"></div></li>
			<li> <h5 style='color:white;font-size:15px;margin-left:10px;'>eCATALOGUE ITEMS &nbsp; &nbsp; </h5> </li>
		</ul>
	</div>
</a>

<?php } ?>-->
<?php if($CI2->modules->link_checker(73,'REVIEW') == TRUE){ ?>
<a href="<?php echo HTTP_PATH."report/eCatalogue_index" ?>">
	<div class="quick-link">
		<ul class="list_items">
			<li><div style="width:45px; height:45px;margin-top:5px;"><img src="<?php echo HTTP_PATH ?>img/reports.png" width="120" height="120"></div></li>
			<li> <h3><span style='text-transform:lowercase;'>e</span>CATALOGUE ITEMS</h3> </li>
		</ul>
	</div>
</a>
<?php } ?>

<?php if($CI2->modules->link_checker(73,'REVIEW') == TRUE){ ?>
<a href="<?php echo HTTP_PATH."report/item_summary_eCatalogue" ?>">
	<div class="quick-link">
		<ul class="list_items">
			<li><div style="width:45px; height:45px;margin-top:5px;"><img src="<?php echo HTTP_PATH ?>img/chart_bar_down.png" width="120" height="120"></div></li>
			<li> <h3 style='color:white;'><span style='text-transform:lowercase;'>e</span>CATALOGUE - SUMMARY</h3> </li>
		</ul>
	</div>
</a>
<?php } ?>



