
 <div class="content">
		
    	<div class="title-content">
        	<h2>System Activities</h2>
        </div>
		<div style="float:right;margin: 16px;">
			<a href='<?php echo HTTP_PATH ."files/user_manual/$USER_MANUAL"?>' target='_new'><img src='<?php echo HTTP_PATH ?>img/help1.png'></a>
		</div>
        
        <div class="clear"></div>
	
        <div class="working_area">
			<div class="container">
			<form method='post' action='<?php echo HTTP_PATH ?>report/system_activities'>
			 <?php 
			 $CI =& get_instance();
			 $CI->load->library('forms');
			 echo $CI->forms->form_fields2('text_short','DateFrom',$DateFrom,'Date From','date'); 
			 echo $CI->forms->form_fields2('text_short','DateTo',$DateTo,'Date To','date2'); 
			 ?>
			 <input type='submit' name='Submit' value='Submit' class='fl' style='margin: 31px 0px 0px 14px;'>
			</form>
			<div class="clear">&nbsp;</div>	
				 <?php
					echo $logs;
				 ?>
			<div class="clear">&nbsp;</div>	
            </div>
        </div>
           
        <div class="clear"></div>
    </div>