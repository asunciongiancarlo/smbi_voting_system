<?php if($CI2->modules->link_checker(4,'REVIEW') == TRUE OR 
		 $CI2->modules->link_checker(3,'REVIEW') == TRUE OR 
		 $CI2->modules->link_checker(2,'REVIEW') == TRUE OR
		 $CI2->modules->link_checker(62,'REVIEW') == TRUE OR 
		 $CI2->modules->link_checker(62,'REVIEW') == TRUE){ ?>
		 
<div class="item-database">
		<h3 id="item" style="color:white;">USER MANAGEMENT</h3>
</div>
<hr>
<?php } ?>
<?php 
	$CI2=& get_instance();
	$CI2->load->library('modules');
?>
<?php if($CI2->modules->link_checker(4,'REVIEW') == TRUE){ ?>
<a href="<?php echo HTTP_PATH."users/admin_users" ?>">
	<div class="quick-link">
		<ul class="list_items">
			<li><div style="width:45px; height:45px;margin-top:5px;"><img src="<?php echo HTTP_PATH ?>img/user.png" width="120" height="120"></div></li>
			<li> <h3>USERS<br/></h3> </li>
		</ul>
	</div>
</a>

<?php } if($CI2->modules->link_checker(3,'REVIEW') == TRUE){ ?>
<a href="<?php echo HTTP_PATH."users/roles" ?>">
	<div class="quick-link">
		<ul class="list_items">
			<li><div style="width:45px; height:45px;margin-top:5px;"><img src="<?php echo HTTP_PATH ?>img/role.png" width="120" height="120"></div></li>
			<li> <h3>ROLES</h3> </li>
		</ul>
	</div>
</a>

<?php } if($CI2->modules->link_checker(2,'REVIEW') == TRUE){ ?>
<a href="<?php echo HTTP_PATH."users/profile" ?>">
<div class="quick-link">
	<ul class="list_items">
		<li><div style="width:45px; height:45px;margin-top:5px;"><img src="<?php echo HTTP_PATH ?>img/profile1.png" width="120" height="120"></div></li>
		<li> <h3>PROFILES</h3></li>
	</ul>
</div>
</a>
<?php } ?>

<?php if($CI2->modules->link_checker(62,'REVIEW') == TRUE){ ?>
<a href="<?php echo HTTP_PATH."users/departments" ?>">
<div class="quick-link">
	<ul class="list_items">
		<li><div style="width:45px; height:45px;margin-top:5px;"><img src="<?php echo HTTP_PATH ?>img/profile1.png" width="120" height="120"></div></li>
		<li> <h3>DEPARTMENTS</h3></li>
	</ul>
</div>
</a>
<?php } ?>

<?php if($CI2->modules->link_checker(63,'REVIEW') == TRUE){ ?>
<a href="<?php echo HTTP_PATH."users/forgot_password_email_receiver" ?>">
<div class="quick-link">
	<ul class="list_items">
		<li><div style="width:45px; height:45px;margin-top:5px;"><img src="<?php echo HTTP_PATH ?>img/profile1.png" width="120" height="120"></div></li>
		<li><h3>FORGOT PASSWORD: <br/>EMAIL RECIPIENTS</h3></li>
	</ul>
</div>
</a>
<?php } ?>

<?php if($CI2->modules->link_checker(31,'REVIEW') == TRUE OR 
		 $CI2->modules->link_checker(33,'REVIEW') == TRUE OR 
		 $CI2->modules->link_checker(45,'REVIEW') == TRUE OR 
		 $CI2->modules->link_checker(50,'REVIEW') == TRUE OR 
		 $CI2->modules->link_checker(51,'REVIEW') == TRUE){ ?>
<div class="item-database clear" >
		<h3 id="item" style="color:white;">FORMS</h3>
</div>
<hr>
<?php } ?>

<?php if($CI2->modules->link_checker(31,'REVIEW') == TRUE){ ?>
<a href="<?php echo HTTP_PATH."users/form_validation" ?>">
<div class="quick-link">
	<ul class="list_items">
		<li><div style="width:45px; height:45px;margin-top:5px;"><img src="<?php echo HTTP_PATH ?>img/form_validation.png" width="120" height="120"></div></li>
		<li> <h3>FORM VALIDATION</h3></li>
	</ul>
</div>
</a>
<?php } if($CI2->modules->link_checker(33,'REVIEW') == TRUE){ ?>
<a href="<?php echo HTTP_PATH."users/POSM_fields" ?>">
<div class="quick-link">
	<ul class="list_items">
		<li><div style="width:45px; height:45px;margin-top:5px;"><img src="<?php echo HTTP_PATH ?>img/posm.png" width="120" height="120"></div></li>
		<li> <h3>POSM FIELDS</h3></li>
	</ul>
</div>
</a>
<?php } ?>

<?php if($CI2->modules->link_checker(45,'REVIEW') == TRUE){ ?>
<a href="<?php echo HTTP_PATH."users/ec_item_fields/edit" ?>">
<div class="quick-link">
	<ul class="list_items">
		<li><div style="width:45px; height:45px;margin-top:5px;"><img src="<?php echo HTTP_PATH ?>img/posm.png" width="120" height="120"></div></li>
		<li> <h3> <span style='text-transform:lowercase;'>e</span>C-Item Fields</h3></li>
	</ul>
</div>
</a>
<?php } ?>

<?php if($CI2->modules->link_checker(50,'REVIEW') == TRUE){ ?>
<a href="<?php echo HTTP_PATH."users/Item_Database_Type_Other_fields" ?>">
<div class="quick-link">
	<ul class="list_items">
		<li><div style="width:45px; height:45px;margin-top:5px;"><img src="<?php echo HTTP_PATH ?>img/posm.png" width="120" height="120"></div></li>
		<li> <h3>ITEM DB: ITEM STATUS - <br/>OTHER FIELD </h3></li>
	</ul>
</div>
</a>
<?php } ?>

<?php if($CI2->modules->link_checker(51,'REVIEW') == TRUE){ ?>
<a href="<?php echo HTTP_PATH."users/eCatalogue_Type_Other_fields" ?>">
<div class="quick-link">
	<ul class="list_items">
		<li><div style="width:45px; height:45px;margin-top:5px;"><img src="<?php echo HTTP_PATH ?>img/posm.png" width="120" height="120"></div></li>
		<li> <h3><span style="text-transform:lowercase">e</span>C: ITEM STATUS - <br/>OTHER FIELD </h3></li>
	</ul>
</div>
</a>
<?php } ?>

<?php if($CI2->modules->link_checker(57,'REVIEW') == TRUE){ ?>
<a href="<?php echo HTTP_PATH."users/user_manual" ?>">
<div class="quick-link">
	<ul class="list_items">
		<li><div style="width:45px; height:45px;margin-top:5px;"><img src="<?php echo HTTP_PATH ?>img/user_manual.png" width="120" height="120"></div></li>
		<li> <h3>USER MANUAL<br/></h3></li>
	</ul>
</div>
</a>
<?php } ?>

<?php if($CI2->modules->link_checker(21,'REVIEW') == TRUE OR $CI2->modules->link_checker(63,'REVIEW') == TRUE OR $CI2->modules->link_checker(20,'REVIEW') == TRUE OR $CI2->modules->link_checker(22,'REVIEW') == TRUE OR $CI2->modules->link_checker(23,'REVIEW') == TRUE OR $CI2->modules->link_checker(24,'REVIEW') == TRUE OR $CI2->modules->link_checker(25,'REVIEW') == TRUE OR $CI2->modules->link_checker(63,'REVIEW') == TRUE){ ?>

<div class="item-database clear" >
		<h3 id="item" style="color:white;">ITEM ATTRIBUTES</h3>
</div>
<hr>

<?php } if($CI2->modules->link_checker(83,'REVIEW') == TRUE){ ?>
<a href="<?php echo HTTP_PATH."users/price_range" ?>">
	<div class="quick-link">
		<ul class="list_items">
			<li><div style="width:45px; height:45px;margin-top:5px;"><img src="<?php echo HTTP_PATH ?>img/Currency Dollar.png" width="120" height="120"></div></li>
			<li> <h3>US DOLLAR - PRICE RANGE</h3> </li>
		</ul>
	</div>
</a>

<?php } if($CI2->modules->link_checker(21,'REVIEW') == TRUE){ ?>
<a href="<?php echo HTTP_PATH."itemDatabase/POSM_status" ?>">
	<div class="quick-link">
		<ul class="list_items">
			<li><div style="width:45px; height:45px;margin-top:5px;"><img src="<?php echo HTTP_PATH ?>img/posm.png" width="120" height="120"></div></li>
			<li> <h3>POSM STATUS</h3> </li>
		</ul>
	</div>
</a>

<?php } if($CI2->modules->link_checker(63,'REVIEW') == TRUE){ ?>

<a href="<?php echo HTTP_PATH."itemDatabase/brands" ?>">
	<div class="quick-link">
		<ul class="list_items">
			<li><div style="width:45px; height:45px;margin-top:5px;"><img src="<?php echo HTTP_PATH ?>img/brand.png" width="120" height="120"></div></li>
			<li> <h3>Brands</h3> </li>
		</ul>
	</div>
</a>

<?php } if($CI2->modules->link_checker(20,'REVIEW') == TRUE){ ?>
<a href="<?php echo HTTP_PATH."itemDatabase/POSM_type" ?>">
<div class="quick-link">
	<ul class="list_items">
		<li><div style="width:45px; height:45px;margin-top:5px;"><img src="<?php echo HTTP_PATH ?>img/posm.png" width="120" height="120"></div></li>
		<li> <h3>POSM TYPE</h3></li>
	</ul>
</div>
</a>

<?php } if($CI2->modules->link_checker(22,'REVIEW') == TRUE){ ?>
<a href="<?php echo HTTP_PATH."itemDatabase/OUTLET_status" ?>">
	<div class="quick-link">
		<ul class="list_items">
			<li><div style="width:45px; height:45px;margin-top:5px;"><img src="<?php echo HTTP_PATH ?>img/outlet.png" width="120" height="120"></div></li>
			<li> <h3>OUTLET TYPE</h3> </li>
		</ul>
	</div>
</a>
<?php } if($CI2->modules->link_checker(23,'REVIEW') == TRUE){ ?>
<a href="<?php echo HTTP_PATH."itemDatabase/MATERIAL_type" ?>">
	<div class="quick-link">
		<ul class="list_items">
			<li><div style="width:45px; height:45px;margin-top:5px;"><img src="<?php echo HTTP_PATH ?>img/material.png" width="120" height="120"></div></li>
			<li> <h3>MATERIAL TYPE</h3></li>
		</ul>
	</div>
</a>
<?php } if($CI2->modules->link_checker(24,'REVIEW') == TRUE){ ?>
<a href="<?php echo HTTP_PATH."itemDatabase/country" ?>">
	<div class="quick-link">
		<ul class="list_items">
			<li><div style="width:45px; height:45px;margin-top:5px;"><img src="<?php echo HTTP_PATH ?>img/country.png" width="120" height="120"></div></li>
			<li> <h3>COUNTRY</h3></li>
		</ul>
	</div>
</a>

<?php } if($CI2->modules->link_checker(25,'REVIEW') == TRUE){ ?>
<a href="<?php echo HTTP_PATH."itemDatabase/PremiumItemType" ?>">
	<div class="quick-link">
		<ul class="list_items">
			<li><div style="width:45px; height:45px;margin-top:5px;"><img src="<?php echo HTTP_PATH ?>img/premium.png" width="120" height="120"></div></li>
			<li> <h3>PREMIUM TYPE</h3> </li>
		</ul>
	</div>
</a>
<?php } ?>

<?php if($CI2->modules->link_checker(63,'REVIEW') == TRUE){ ?>
<a href="<?php echo HTTP_PATH."itemDatabase/brandPerCountry" ?>">
	<div class="quick-link">
		<ul class="list_items">
			<li><div style="width:45px; height:45px;margin-top:5px;"><img src="<?php echo HTTP_PATH ?>img/country.png" width="120" height="120"></div></li>
			<li> <h3>Brand Per Country</h3></li>
		</ul>
	</div>
</a>
<?php } ?>

<?php if($CI2->modules->link_checker(63,'REVIEW') == TRUE ||  $CI2->modules->link_checker(50,'REVIEW') == TRUE){ ?>
<div class="item-database clear">
		<h3 id="item" style="color:white;"> FEATURED ITEMS</h3>
</div>
<hr>
<?php } ?>

<?php if($CI2->modules->link_checker(63,'REVIEW') == TRUE){ ?>
<a href="<?php echo HTTP_PATH."itemDatabase/brandsInCommonGallery" ?>">
	<div class="quick-link">
		<ul class="list_items">
			<li><div style="width:45px; height:45px;margin-top:5px;"><img src="<?php echo HTTP_PATH ?>img/brand.png" width="120" height="120"></div></li>
			<li> <h3>Brands</h3> </li>
		</ul>
	</div>
</a>
<?php } ?>

<?php if($CI2->modules->link_checker(50,'REVIEW') == TRUE){ ?>
<a href="<?php echo HTTP_PATH."users/featured_items" ?>">
<div class="quick-link">
	<ul class="list_items">
		<li><div style="width:45px; height:45px;margin-top:5px;"><img src="<?php echo HTTP_PATH ?>img/posm.png" width="120" height="120"></div></li>
		<li> <h3>FEATURED ITEMS <br/>SETTING </h3></li>
	</ul>
</div>
</a>
<?php } ?>

<?php if($CI2->modules->link_checker(71,'REVIEW') == TRUE){ ?>
<div class="item-database clear">
		<h3 id="item" style="color:white;">GALLERY</h3>
</div>
<hr>
<?php } ?>

<?php if($CI2->modules->link_checker(71,'REVIEW') == TRUE){ ?>
<a href="<?php echo HTTP_PATH."users/target_items" ?>">
	<div class="quick-link">
		<ul class="list_items">
			<li><div style="width:45px; height:45px;margin-top:5px;"><img src="<?php echo HTTP_PATH ?>img/role.png" width="120" height="120"></div></li>
			<li> <h3>TARGET ITEMS PER <br/>MONTH</h3> </li>
		</ul>
	</div>
</a>
<?php } ?>


<?php if($CI2->modules->link_checker(41,'REVIEW') == TRUE OR 
		 $CI2->modules->link_checker(42,'REVIEW') == TRUE OR 
		 $CI2->modules->link_checker(58,'REVIEW') == TRUE OR
		 $CI2->modules->link_checker(59,'REVIEW') == TRUE OR
		 $CI2->modules->link_checker(60,'REVIEW') == TRUE OR
		 $CI2->modules->link_checker(61,'REVIEW') == TRUE 
		 ){ ?>
<div class="item-database clear">
		<h3 id="item" style="color:white;">CAMPAIGN SETTING</h3>
</div>
<hr>
<?php } ?>
<?php if($CI2->modules->link_checker(62,'REVIEW') == TRUE){ ?>
<div class="item-database clear">
	<h3 id="item" style="color:white;font-size:18px;">Voters</h3>
</div>
<?php } ?>
<?php if($CI2->modules->link_checker(62,'REVIEW') == TRUE){ ?>
<a href="<?php echo HTTP_PATH."users/voters_department" ?>">
<div class="quick-link">
	<ul class="list_items">
		<li><div style="width:45px; height:45px;margin-top:5px;"><img src="<?php echo HTTP_PATH ?>img/profile1.png" width="120" height="120"></div></li>
		<li> <h3>VOTERS DEPARTMENT</h3></li>
	</ul>
</div>
</a>
<?php } ?>

<?php if($CI2->modules->link_checker(41,'REVIEW') == TRUE OR 
		 $CI2->modules->link_checker(58,'REVIEW') == TRUE OR 
		 $CI2->modules->link_checker(59,'REVIEW') == TRUE OR
		 $CI2->modules->link_checker(64,'REVIEW') == TRUE){ ?>
<div class="item-database clear">
		<h3 id="item" style="color:white;font-size:18px;">iLike Campaign</h3>
</div>
<div class='cl'></div>
<?php } ?>

<?php if($CI2->modules->link_checker(41,'REVIEW') == TRUE){ ?>
<a href="<?php echo HTTP_PATH."users/iLikeCampaignRules" ?>">
	<div class="quick-link">
		<ul class="list_items">
			<li><div style="width:45px; height:45px;margin-top:5px;"><img src="<?php echo HTTP_PATH ?>img/role.png" width="120" height="120"></div></li>
			<li> <h3><span style='text-transform:lowercase;'>i</span>Like Campaign Rules</h3> </li>
		</ul>
	</div>
</a>
<?php } ?>

<?php if($CI2->modules->link_checker(59,'REVIEW') == TRUE){ ?>
<a href="<?php echo HTTP_PATH."users/iLikeVotingRules" ?>">
	<div class="quick-link">
		<ul class="list_items">
			<li><div style="width:45px; height:45px;margin-top:5px;"><img src="<?php echo HTTP_PATH ?>img/role.png" width="120" height="120"></div></li>
			<li> <h3><span style='text-transform:lowercase;'>i</span>Like Voting Rules</h3> </li>
		</ul>
	</div>
</a>
<?php } ?>

<?php if($CI2->modules->link_checker(58,'REVIEW') == TRUE){ ?>
<a href="<?php echo HTTP_PATH."users/min_number_commitees" ?>">
	<div class="quick-link">
		<ul class="list_items">
			<li><div style="width:45px; height:45px;margin-top:5px;"><img src="<?php echo HTTP_PATH ?>img/role.png" width="120" height="120"></div></li>
			<li> <h3><span style='text-transform:lowercase;'>i</span>LIKE CAMPAIGN <br>NUMBER OF NOMINEES</h3> </li>
		</ul>
	</div>
</a>
<?php } ?>

<?php if($CI2->modules->link_checker(64,'REVIEW') == TRUE){ ?>
<a href="<?php echo HTTP_PATH."users/iLikeCanvassingRules" ?>">
	<div class="quick-link">
		<ul class="list_items">
			<li><div style="width:45px; height:45px;margin-top:5px;"><img src="<?php echo HTTP_PATH ?>img/role.png" width="120" height="120"></div></li>
			<li> <h3><span style='text-transform:lowercase;'>i</span>LIKE CAMPAIGN <br>CANVASSING RULES</h3> </li>
		</ul>
	</div>
</a>
<?php } ?>

<?php if($CI2->modules->link_checker(61,'REVIEW') == TRUE OR
		 $CI2->modules->link_checker(72,'REVIEW') == TRUE OR 
		 $CI2->modules->link_checker(66,'REVIEW') == TRUE){ ?>
<div class="item-database clear">
		<h3 id="item" style="color:white;font-size:18px;">iWant Campaign</h3>
</div>
<div class='cl'></div>
<?php } ?>


<?php if($CI2->modules->link_checker(61,'REVIEW') == TRUE){ ?>
<a href="<?php echo HTTP_PATH."users/iWant_min_number_commitees" ?>">
	<div class="quick-link">
		<ul class="list_items">
			<li><div style="width:45px; height:45px;margin-top:5px;"><img src="<?php echo HTTP_PATH ?>img/role.png" width="120" height="120"></div></li>
			<li> <h3><span style='text-transform:lowercase;'>i</span>WANT CAMPAIGN <br>NUMBER OF NOMINEES</h3> </li>
		</ul>
	</div>
</a>
<?php } ?>

<?php if($CI2->modules->link_checker(72,'REVIEW') == TRUE){ ?>
<a href="<?php echo HTTP_PATH."users/iWantVotingRules" ?>">
	<div class="quick-link">
		<ul class="list_items">
			<li><div style="width:45px; height:45px;margin-top:5px;"><img src="<?php echo HTTP_PATH ?>img/role.png" width="120" height="120"></div></li>
			<li> <h3><span style='text-transform:lowercase;'>i</span>Want Voting Rules</h3> </li>
		</ul>
	</div>
</a>
<?php } ?>

<?php if($CI2->modules->link_checker(65,'REVIEW') == TRUE){ ?>
<a href="<?php echo HTTP_PATH."users/iWantCanvassingRules" ?>">
	<div class="quick-link">
		<ul class="list_items">
			<li><div style="width:45px; height:45px;margin-top:5px;"><img src="<?php echo HTTP_PATH ?>img/role.png" width="120" height="120"></div></li>
			<li> <h3><span style='text-transform:lowercase;'>i</span>WANT CAMPAIGN <br>CANVASSING RULES</h3> </li>
		</ul>
	</div>
</a>
<?php } ?>


<?php if($CI2->modules->link_checker(54,'REVIEW') == TRUE OR $CI2->modules->link_checker(54,'REVIEW') == TRUE){ ?>
<div class="item-database clear">
	<h3 id="item" style="color:white;">ARCHIVE AND RESTORE</h3>
</div>
<hr>
<?php } ?>

<?php if($CI2->modules->link_checker(54,'REVIEW') == TRUE){ ?>
<a href="<?php echo HTTP_PATH."users/archive_filtering" ?>">
	<div class="quick-link">
		<ul class="list_items">
			<li><div style="width:45px; height:45px;margin-top:5px;"><img src="<?php echo HTTP_PATH ?>img/role.png" width="120" height="120"></div></li>
			<li> <h3>ARCHIVE FILTERING</h3> </li>
		</ul>
	</div>
</a>
<?php } ?>
