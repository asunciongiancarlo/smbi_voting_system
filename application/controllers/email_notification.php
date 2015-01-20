<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Email_notification extends CI_Controller {
 
public function __construct()
   {
		parent::__construct();
		date_default_timezone_set('UTC');
		session_start();
		$this->load->model('c3model');
		$this->load->library('security');
		$this->load->library('email');
		$this->load->library('modules');
		$this->load->library('smtp');
		$this->load->helper('url');
		$this->modules->session_handler();
		$this->output->enable_profiler(FALSE);
   }

    public function index()
	{			
	   $this->modules->module_checker(27,'REVIEW');
	   
	   $data['vfile']		= 'reportsSubMenu.php';
	   $data['title']		= 'San Miguel Brewing International';
	   $data['page_title']	= 'Reports';
	   $data['meta_description']	= 'San Miguel Brewing International';
	   $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';

	    $sqlSTr2="SELECT *,
				OUTLET_Status.statusName as OutletStatusName, 
				POSM_Status.statusName as POSMStatusName,
				items.id as itemID ,
				(select image from items_images where itemID = items.id  AND defaultStatus=1 limit 0,1) as img
				FROM items 
				LEFT JOIN POSM_Type ON items.POSMTypeID = POSM_Type.id 
				LEFT JOIN POSM_Status ON items.POSMStatusID = POSM_Status.id 
				LEFT JOIN OUTLET_Status ON items.OUTLETStatusID = OUTLET_Status.id
				LEFT JOIN premiumItemType ON items.PremiumTypeID = premiumItemType.id 
				LEFT JOIN MATERIAL_Type ON items.MaterialTypeID = MATERIAL_Type.id 
				LEFT JOIN country ON items.countryID = country.id 
				LEFT JOIN brands  ON items.brandID = brands.id 
				WHERE items.brandID IN 
				(SELECT brandID FROM commonGalleryBrands) 
				AND publish !='n' ORDER BY RAND() limit 3,3";
				
		$sqlSTr1="SELECT *,
				OUTLET_Status.statusName as OutletStatusName, 
				POSM_Status.statusName as POSMStatusName,
				items.id as itemID,(select image from items_images where itemID = items.id AND defaultStatus=1 limit 0,1 ) as img
				FROM items 
				LEFT JOIN POSM_Type ON items.POSMTypeID = POSM_Type.id 
				LEFT JOIN POSM_Status ON items.POSMStatusID = POSM_Status.id 
				LEFT JOIN OUTLET_Status ON items.OUTLETStatusID = OUTLET_Status.id
				LEFT JOIN premiumItemType ON items.PremiumTypeID = premiumItemType.id 
				LEFT JOIN MATERIAL_Type ON items.MaterialTypeID = MATERIAL_Type.id 
				LEFT JOIN country ON items.countryID = country.id 
				LEFT JOIN brands  ON items.brandID = brands.id
				
				WHERE items.brandID IN 
				(SELECT brandID FROM commonGalleryBrands) 
				AND publish !='n' ORDER BY RAND()  limit 0,3 ";
				
	   $data['featured1']       = $this->c3model->c3crud("select",'','','',$sqlSTr1); 
	   $data['featured2']       = $this->c3model->c3crud("select",'','','',$sqlSTr2); 
	   
	   $HTTP_PATH = HTTP_PATH;
	   $data['breadCrumbs']			= '<a href='.$HTTP_PATH.'report> Reports </a>';
	   
	   $this->load->view('menu',$data); 
	}
   
   
   function logs()
   {
	
		$dayToday = date('Y-m-d');
		
		/*ADDED AUTHENTICATION FOR EMAIL*/
		$config = Array(		
		    'protocol' => 'smtp',
		    'smtp_host' => 'ssl://smtp.googlemail.com',
		    'smtp_port' => 465,
		    'smtp_user' => 'asunciongiancarlo@gmail.com',
		    'smtp_pass' => 'G14nc4rl0',
		    'smtp_timeout' => '4',
		    'mailtype'  => 'html', 
		    'charset'   => 'iso-8859-1'
		); 
 
		$this->email->initialize($config);
		$this->email->set_newline("\r\n");
		/*ADDED AUTHENTICATION FOR EMAIL*/
		
		
		//GET ALL THE RESPONDENTS
		$sql					= "SELECT * FROM admin_users WHERE super_admin = 'y'";
		$admin 	= $this->db->query($sql);
		$admin	= $admin->result_array();
		
		
		foreach($admin as $admin)
		{
			extract($admin);
		
			//CREATE MESSAGE
			$msg = "<label style='font-size:19px;font-family:Arial,Helvetica,sans-serif;color:#9e0b0f;'>San Miguel Brewing, International, LTD. </label><br/>
					<label style='font-size:11px;font-family:Arial,Helvetica,sans-serif;color:#777777;border-color: gray;'>Daily reports <br/>Date: $dayToday</label><br/>
					<label style='font-size:11px;font-family:Arial,Helvetica,sans-serif;color:#777777;border-color: gray;'>
					Please click <a href='".HTTP_PATH."email_notification/reportsPreview/$dayToday'>here</label> if you cannot view this email <br/>
					<br/>
					".$this->reports();
			
			//SEND EMAIL
			$this->email->clear();
			$this->email->from('do.not.reply@smg.sanmiguel.com.ph', 'San Miguel Beer International');
			$this->email->to($email_address); 

			$this->email->subject("SMBi $dayToday Reports");
			$this->email->message($msg);	
			
			echo $this->email->print_debugger();
			
			$this->email->send();
		}
		
		echo "Email Sent!";	
   }
   
   function reports()
   {
	$dayToday = date('Y-m-d');
	
	//COUNTRY
	$sql = "SELECT country.id as c_id FROM country";
	$sql = $this->db->query($sql);
	$countries = $sql->result_array();
	
	
	$actions = array(array('action'=>'add'),
			   array('action'=>'edit'),
			   array('action'=>'delete'),
			   array('action'=>'published'),
			   array('action'=>'recreate'),
			   array('action'=>'revote')
			   );
			   
	$pCountry = "";
	
	//MODULES
	$sql = "SELECT distinct(module_name) as dModule FROM logs";
	$sql = $this->db->query($sql);
	$dModules = $sql->result_array();
	
	//print_r($dModules);
	
	$m = "";
	$msg = "";
	foreach($countries as $c)
	{
		extract($c);
		
		foreach($dModules as $d)
		{
			extract($d);
			
			foreach($actions as $a)
			{
				extract($a);
				
				$sql = "SELECT rec_id, action, rec_name, 
						module_name, table_name, country.countryName as cName, 
						admin_users.full_name as fullName
						FROM logs 
						INNER JOIN country 	   ON country.id 	 = logs.country_id  
						INNER JOIN admin_users ON admin_users.id = logs.user_id
						WHERE logs.tdate = '$dayToday' 
						AND country_id= $c_id AND action='$action' AND module_name='$dModule' 
						GROUP BY rec_id 
						ORDER BY module_name";
				$sql = $this->db->query($sql);
				$records = $sql->result_array();
				
				//print_r($records);
				if($records)
				{	
					extract($records);
					
					$c = "";	
					foreach($records as $r)
					{extract($r);
						
						//COUNTYR NAME
						if(($c=="" OR $c != $cName) AND $pCountry!=$cName){
							$msg .= "<label style='font-size:16px;font-family:Arial,Helvetica,sans-serif;color:#777777;'> <b>Country Name: $cName </b></label><br/>";
							$c    = $cName;
						}
					
						$pCountry = $cName;
					}
					
					$msg .="<table style='font-size:12px;font-family:Arial,Helvetica,sans-serif;color:#777777;border-color: gray;'>
							<tr> 
								<th style='width:100px;color:black;font-weight: bold;background:#FCD9D9'> Table 	 	 </th>
								<th style='width:100px;color:black;font-weight: bold;background:#FCD9D9'> Action   		 </th>
								<th style='width:100px;color:black;font-weight: bold;background:#FCD9D9'> Record ID	     </th>
								<th style='width:215px;color:black;font-weight: bold;background:#FCD9D9'> Record Name  	 </th>
								<th style='width:150px;color:black;font-weight: bold;background:#FCD9D9'> User  Name   	 </th>
							</tr>";
					
					$x=0;
					foreach($records as $r)
					{
					$cls = (($x++)%2) != 0 ? "style='background:#f9ebeb'" :  ""; 
					extract($r);
					$msg .= "<tr> 
								<td $cls> $table_name  	  </td> 
								<td $cls> $action  	  	  </td> 
								<td $cls> $rec_id  	  	  </td> 
								<td $cls> $rec_name 	  </td>
								<td $cls> $fullName 	  </td> 
							</tr> ";
					}
					
					$msg .= "</table><br/>";
					
					//echo $pCountry;
					
					//print_r($records);
				}
				
				
			
			}
		
		}
	}
	
    return $msg;
	
   }
   
   
   function reportsPreview($dayToday)
   {
	if(!isset($dayToday))
		$dayToday = date('Y-m-d');
	
	//COUNTRY
	$sql = "SELECT country.id as c_id FROM country";
	$sql = $this->db->query($sql);
	$countries = $sql->result_array();
	
	
	$actions = array(array('action'=>'add'),
			   array('action'=>'edit'),
			   array('action'=>'delete'),
			   array('action'=>'published'),
			   array('action'=>'recreate'),
			   array('action'=>'revote')
			   );
			   
	$pCountry = "";
	
	//MODULES
	$sql = "SELECT distinct(module_name) as dModule FROM logs";
	$sql = $this->db->query($sql);
	$dModules = $sql->result_array();
	
	//print_r($dModules);
	
	$m = "";
	$msg = "";
	foreach($countries as $c)
	{
		extract($c);
		
		foreach($dModules as $d)
		{
			extract($d);
			
			foreach($actions as $a)
			{
				extract($a);
				
				$sql = "SELECT rec_id, action, rec_name, 
						module_name, table_name, country.countryName as cName, 
						admin_users.full_name as fullName
						FROM logs 
						INNER JOIN country 	   ON country.id 	 = logs.country_id  
						INNER JOIN admin_users ON admin_users.id = logs.user_id
						WHERE logs.tdate = '$dayToday' 
						AND country_id= $c_id AND action='$action' AND module_name='$dModule' 
						GROUP BY rec_id 
						ORDER BY module_name";
				$sql = $this->db->query($sql);
				$records = $sql->result_array();
				
				//print_r($records);
				if($records)
				{	
					extract($records);
					
					$c = "";	
					foreach($records as $r)
					{extract($r);
						
						//COUNTYR NAME
						if(($c=="" OR $c != $cName) AND $pCountry!=$cName){
							$msg .= "<label style='font-size:16px;font-family:Arial,Helvetica,sans-serif;color:#777777;'> <b>Country Name: $cName </b></label><br/>";
							$c    = $cName;
						}
					
						$pCountry = $cName;
					}
					
					$msg .="<table style='font-size:12px;font-family:Arial,Helvetica,sans-serif;color:#777777;border-color: gray;'>
							<tr> 
								<th style='width:100px;color:black;font-weight: bold;background:#FCD9D9'> Table 	 	 </th>
								<th style='width:100px;color:black;font-weight: bold;background:#FCD9D9'> Action   		 </th>
								<th style='width:100px;color:black;font-weight: bold;background:#FCD9D9'> Record ID	     </th>
								<th style='width:215px;color:black;font-weight: bold;background:#FCD9D9'> Record Name  	 </th>
								<th style='width:150px;color:black;font-weight: bold;background:#FCD9D9'> User  Name   	 </th>
							</tr>";
					
					$x=0;
					foreach($records as $r)
					{
					$cls = (($x++)%2) != 0 ? "style='background:#f9ebeb'" :  ""; 
					extract($r);
					$msg .= "<tr> 
								<td $cls> $table_name  	  </td> 
								<td $cls> $action  	  	  </td> 
								<td $cls> $rec_id  	  	  </td> 
								<td $cls> $rec_name 	  </td>
								<td $cls> $fullName 	  </td> 
							</tr> ";
					}
					
					$msg .= "</table><br/>";
					
					//echo $pCountry;
					
					//print_r($records);
				}
				
				
			
			}
		
		}
	}
	
    echo "<label style='font-size:20px;font-family:Arial,Helvetica,sans-serif;color:#9e0b0f;'>San Miguel Brewing, International, LTD. </label><br/>
					<label style='font-size:11px;font-family:Arial,Helvetica,sans-serif;color:#777777;border-color: gray;'>Daily reports <br/>Date: $dayToday</label><br/>
					<br/>".$msg;
	
   }
   
   
}