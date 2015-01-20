
 <div class="content">
		
    	<div class="title-content">
        	<h2><span style="text-transform:lowercase;">i</span>WANT REPORT</span> </h2>
        </div>
        
        <div class="clear"></div>
	
        <div class="working_area">
			<div class="container">
			
		
			<table cellpadding="0" cellspacing="0" style="width:100%;margin: 0px auto;">
				<br/>
			   <tr style="border-radius: 6px;margin-top:10px;">
					<th style="width:200px;text-align:center;">  DATE ADDED 	</th> 
					<th style="width:440px;text-align:center;">  CAMPAIGN NAME 	</th> 
					<th style="width:200px;text-align:center;">  FROM 			</th> 
					<th style="width:200px;text-align:center;">  TO				</th> 
					<th style="width:200px;text-align:center;">  STATUS 		</th> 
					<th style="width:315px;text-align:center;">  REMARKS 		</th> 
				</tr>
				<tr>
				  <td><?php echo $repHeader[0]['DateAdded']?> </td>
				  <td><?php echo $repHeader[0]['campaignName']?> </td>
				  <td><?php echo $repHeader[0]['DateFrom']?> </td>
				  <td><?php echo $repHeader[0]['DateTo']?> </td>
				  <td><?php echo $repHeader[0]['status'] == "on progress" ? "in progress" : $repHeader[0]['status'] ?> </td>
				  <td><?php echo $repHeader[0]['remarks']?> </td>
				</tr>
			</table>
			<div class="clear">&nbsp;</div>	
             <table cellpadding="0" cellspacing="0" border=1 style="width:90%;margin: 0px auto;">
			    <tr style="border-radius: 6px;">
					<td style="width:10px;text-align:center;" bgcolor='#999999'>   <b>No 		 </b></td> 
					<td style="width:10px;text-align:center;" bgcolor='#999999'>   <b>Image 	 </b></td> 
					<td style="width:50px;text-align:center;" bgcolor='#999999'>   <b>Name 	 	 </b></td> 
					<td style="width:120px;text-align:center;" bgcolor='#999999'>  <b>Description</b></td> 
					<td style="width:200px;text-align:center;" bgcolor='#999999'>  <b>Wants  	 </b></td>  
				</tr>
				 <?php 
					$x = 0;
					$total = 0;
					foreach($rep as $r) { 
					$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
					$total += $r['voteTot'];
				 ?>
				<tr>
				  <td <?php echo $c ?> ><?php echo $x ?> </td>
				  <td <?php echo $c ?> > <img src="<?php echo HTTP_PATH .'img/thumb/'.$r['iImg'] ?>" style="width:30px;height:30px">    </td>
				  <td <?php echo $c ?> style='text-align:left;' ><?php echo $r['itemName']?> 											</td>
				  <td <?php echo $c ?> style='text-align:left;' ><?php echo $r['sDescription']?> 										</td>
				  <?php 
					if($r['voteTot']== 0) $r['voteTot']=0 ;  
				  ?>
				  <td <?php echo $c ?> ><?php echo $r['voteTot']?> </td>
				</tr>
				 <?php } ?>
				<tr>
					<td colspan='4' style='text-align:left'>Total </td>
					<td><?php echo $total; ?> </td>
				</tr>
			</table>			
			<div class="clear">&nbsp;</div>	
            </div>
        </div>
           
        <div class="clear"></div>
    </div>