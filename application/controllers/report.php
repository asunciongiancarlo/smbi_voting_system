<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report extends CI_Controller {
 
	public function __construct()
   {
		parent::__construct();
		date_default_timezone_set('UTC');
		session_start();
		//error_reporting(E_ALL);
		$this->load->model('c3model');
		$this->load->library('modules');
		$this->load->helper('file');
		$this->load->helper('download');
		$this->output->enable_profiler(FALSE);
		$this->modules->session_handler();
   }

    public function index()
	{			
	   $this->modules->module_checker(36,'REVIEW');
	   
	   $data['vfile']		= 'reportsSubMenu.php';
	   $data['title']		= 'San Miguel Brewing International';
	   $data['page_title']	= 'Reports';
	   $data['meta_description']	= 'San Miguel Brewing International';
	   $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';

	    $sqlSTr1="SELECT *,
				POSM_Type.typeName as POSM_Type_Name, 
				items.id as itemID ,
				(select image from items_images where itemID = items.id  AND defaultStatus=1 limit 0,1) as img
				FROM items 
				LEFT JOIN POSM_Type ON items.POSMTypeID = POSM_Type.id 
				WHERE items.brandID IN 
				(SELECT brandID FROM commonGalleryBrands) 
				AND publish !='n' AND items.POSMTypeID = (SELECT item_typeID FROM featured_items WHERE position='left') ORDER BY RAND() limit 3,3";
				
		$sqlSTr2="SELECT *,
				POSM_Type.typeName as POSM_Type_Name, 
				items.id as itemID,(select image from items_images where itemID = items.id AND defaultStatus=1 limit 0,1 ) as img
				FROM items 
				LEFT JOIN POSM_Type ON items.POSMTypeID = POSM_Type.id 
				WHERE items.brandID IN 
				(SELECT brandID FROM commonGalleryBrands) 
				AND publish !='n' AND items.POSMTypeID = (SELECT item_typeID FROM featured_items WHERE position='right') ORDER BY RAND()  limit 0,3 ";
				
	   $data['featured1']       = $this->c3model->c3crud("select",'','','',$sqlSTr1); 
	   $data['featured2']       = $this->c3model->c3crud("select",'','','',$sqlSTr2); 
	   
	   $HTTP_PATH = HTTP_PATH;
	   $data['breadCrumbs']			= '<a href='.$HTTP_PATH.'report>Analytics </a>';
	   
	   if($this->modules->access_checker()==TRUE)
	   {
		$this->load->view('menu',$data); 
	   }else{
		$data['vfile']				= 'login.php';
		$data['title']				= 'SMBi System Log-in | SMBi';
		$data['page_title']			= 'SMBi System Log-in';
		$data['meta_description']	= 'San Miguel Brewing International';
		$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
		$this->load->view('login',$data); 	
	   }
	}
   
	function system_activities()
    {
	$this->modules->module_checker(46,'REVIEW');
	
	//USER MANUAL
	$data['USER_MANUAL'] = $this->modules->user_manual(44);
	
	$WHERE = "";
	if($_POST!=NULL){
		$dateFrom = $_POST['DateFrom'];
		$dateTo   = $_POST['DateTo'];
		$WHERE   = "logs.tdate >= '$dateFrom' AND logs.tdate <= '$dateTo'";
	}else{
		$dateFrom = date('Y-m-d');
		$dateTo   = date('Y-m-d');
		$WHERE   = "logs.tdate = '$dateFrom'";
	}
	$data['DateFrom'] = $dateFrom;
	$data['DateTo']   = $dateTo;
	
	//COUNTRY
	$sql = "SELECT country.id as c_id FROM country";
	$countries = $this->db->query($sql);
	$countries = $countries->result_array();
	
	$actions = array(array('action'=>'add'),
			   array('action'=>'edit'),
			   array('action'=>'delete'),
			   array('action'=>'published'),
			   array('action'=>'recreate'),
			   array('action'=>'revote')
			   );
			   
	$pCountry = "";
	
	//MODULES
	$sql 	  = "SELECT distinct(module_name) as dModule FROM logs";
	$dModules = $this->db->query($sql);
	$dModules = $dModules->result_array();
	
	//print_r($dModules);
	
	$m = "";
	$msg = "";
	foreach($countries as $c)
	{	extract($c);
		
		foreach($dModules as $d)
		{	extract($d);
			
			foreach($actions as $a)
			{	extract($a);
				$sql = "SELECT rec_id, action, rec_name, 
						module_name, table_name, country.countryName as cName, 
						admin_users.full_name as fullName, ttime, tdate,
						itemCode
						FROM logs 
						INNER JOIN country 	   ON country.id 	 = logs.country_id  
						INNER JOIN admin_users ON admin_users.id = logs.user_id
						WHERE  $WHERE
						AND country_id= $c_id AND action='$action' AND module_name='$dModule' 
						GROUP BY rec_id 
						ORDER BY module_name ASC, tdate DESC";
				$records = $this->db->query($sql);		
				$records = $records->result_array();
				
				//print_r($records);
				if(count($records)>0)
				{	extract($records);
					
					$c = "";	
					foreach($records as $r)
					{extract($r);
						
						//COUNTYR NAME
						if(($c=="" OR $c != $cName) AND $pCountry!=$cName){
							$msg .= "<label style='font-size:16px;font-family:Arial,Helvetica,sans-serif;color:#777777;'> <b>Country Name: $cName </b></label>";
							$c    = $cName;
						}
					
						$pCountry = $cName;
					}
					
					$msg .="<table style='font-size:12px;font-family:Arial,Helvetica,sans-serif;color:#777777;border-color: gray;'>
							<tr> 
								<td style='width:120px;color:black;font-weight: bold;background:#FCD9D9;text-align:center;'> Table 	 	 	 </td>
								<td style='width:120px;color:black;font-weight: bold;background:#FCD9D9;text-align:center;'> Action   		 </td>
								<td style='width:120px;color:black;font-weight: bold;background:#FCD9D9;text-align:center;'> Record ID	     </td>
								<td style='width:120px;color:black;font-weight: bold;background:#FCD9D9;text-align:center;'> Item Code	     </td>
								<td style='width:300px;color:black;font-weight: bold;background:#FCD9D9;text-align:center;'> Record Name  	 </td>
								<td style='width:220px;color:black;font-weight: bold;background:#FCD9D9;text-align:center;'> User  Name   	 </td>
								<td style='width:100px;color:black;font-weight: bold;background:#FCD9D9;text-align:center;'> Date		   	 </td>
								<td style='width:100px;color:black;font-weight: bold;background:#FCD9D9;text-align:center;'> Time		   	 </td>
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
								<td $cls> $itemCode  	  </td> 
								<td $cls> $rec_name 	  </td>
								<td $cls> $fullName 	  </td> 
								<td $cls> $tdate 	  	  </td> 
								<td $cls> $ttime 	  	  </td> 
							</tr> ";
					}
					
					$msg .= "</table><br/>";
					
				}
			}
		}
	}
    
	$data['logs']			= $msg;

    $data['vfile']			= 'system_activities.php';
	$data['title']			= 'System Logs';
	
	$data['breadCrumbs']	 = '<a href='.HTTP_PATH.'report>Analytics </a>';
	$data['breadCrumbs']	.= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	$data['breadCrumbs']	.= '<a href='.HTTP_PATH.'report/system_activities> System Activities </a>';
	 

	if($this->modules->access_checker()==TRUE)
	{
		$this->load->view('innerPages',$data); 
	}else{
		$data['vfile']				= 'login.php';
		$data['title']				= 'SMBi System Log-in | SMBi';
		$data['page_title']			= 'SMBi System Log-in';
		$data['meta_description']	= 'San Miguel Brewing International';
		$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
		$this->load->view('login',$data); 	
	}
   }
	
	function sortingRef_Logs($id='',$returnType='')
	{
		$query	  ='';
		$Orig_code='';
		$Rev_code ='';
		switch($id)
		{
		 case '0-A':
		  $query 	 	 = "rec_id ASC";
		  $Orig_code 	 = "0-A";
		  $Rev_code  	 = "0-D";
		  $label	 	 = "Record ID ";
		  $label_symbol	 = "Record ID &#x25B2;";
		  //$label
		 break;
		 case '0-D':
		  $query 	 	 = "rec_id DESC"; 
		  $Orig_code 	 = "0-D";
		  $Rev_code  	 = "0-A";
		  $label	 	 = "Record ID ";
		  $label_symbol	 = "Record ID &#x25BC;";
		 break;
		 case '1-A':
		  $query 	 = " itemCode ASC"; 
		  $Orig_code = "1-A";
		  $Rev_code  = "1-D";
		  $label	 	 = "Item Code ";
		  $label_symbol	 = "Item Code &#x25B2;";
		 break;
		 case '1-D':
		  $query     	 = "itemCode DESC"; 
		  $Orig_code 	 = "1-D";
		  $Rev_code  	 = "1-A";
		  $label	 	 = "Item Code ";
		  $label_symbol	 = "Item Code &#x25BC;";
		 break;
		 case '2-A':
		  $query     	 = "action ASC"; 
		  $Orig_code 	 = "2-A";
		  $Rev_code  	 = "2-D";
		  $label	 	 = "Action ";
		  $label_symbol	 = "Action &#x25B2;";
		 break;
		 case '2-D':
		  $query         = "action DESC"; 
		  $Orig_code     = "2-D";
		  $Rev_code  	 = "2-A";
		  $label	 	 = "Action ";
		  $label_symbol	 = "Action &#x25BC;";
		 break;
		 case '3-A':
		  $query     = "rec_name ASC";
	      $Orig_code = "3-A";
		  $Rev_code  = "3-D";
		  $label	 	 = "Record Name ";
		  $label_symbol	 = "Record Name &#x25B2;";
		 break;
		 case '3-D':
		  $query     = "rec_name DESC"; 
		  $Orig_code = "3-D";
		  $Rev_code  = "3-A";
		   $label	 	 = "Record Name ";
		  $label_symbol	 = "Record Name &#x25BC;";
		 break;
		 case '4-A':
		  $query 	 = "module_name ASC"; 
		  $Orig_code = "4-A";
		  $Rev_code  = "4-D";
		  $label	 	 = "Module";
		  $label_symbol	 = "Module &#x25B2;";
		 break;
		 case '4-D':
		  $query = " module_name DESC";
		  $Orig_code = "4-D";
		  $Rev_code  = "4-A";
		  $label	 	 = "Module";
		  $label_symbol	 = "Module &#x25BC;";
		 break;
		 case '5-A':
		  $query 	 	 = "table_name ASC"; 
		  $Orig_code 	 = "5-A";
		  $Rev_code  	 = "5-D";
		  $label	 	 = "Table";
		  $label_symbol	 = "Table &#x25B2;";
		 break;
		 case '5-D':
		  $query 		 = "table_name DESC";
		  $Orig_code 	 = "5-D";
		  $Rev_code  	 = "5-A";
		  $label	 	 = "Table";
		  $label_symbol	 = "Table &#x25BC;";
		 break;
		 case '6-A':
		  $query     = "tdate ASC";
		  $Orig_code = "6-A";
		  $Rev_code  = "6-D";
		  $label	 	 = "Date";
		  $label_symbol	 = "Date &#x25B2;";
		 break;
		 case '6-D':
		  $query 	 = "tdate DESC"; 
		  $Orig_code = "6-D";
		  $Rev_code  = "6-A";
		  $label	 	 = "Date";
		  $label_symbol	 = "Date &#x25BC;";
		 break;
		 case '7-A':
		  $query = " tdate ASC, ttime ASC"; 
		  $Orig_code = "7-A";
		  $Rev_code  = "7-D";
		  $label	 	 = "Time";
		  $label_symbol	 = "Time &#x25B2;";
		 break;
		 case '7-D':
		  $query 	 = "tdate DESC, ttime DESC"; 
		  $Orig_code = "7-D";
		  $Rev_code  = "7-A";
		  $label	 	 = "Time";
		  $label_symbol	 = "Time &#x25BC;";
		 break;
		 case '8-A':
		  $query = " admin_users.full_name ASC"; 
		  $Orig_code = "8-A";
		  $Rev_code  = "8-D";
		  $label	 	 = "User";
		  $label_symbol	 = "User &#x25B2;";
		 break;
		 case '8-D':
		  $query = " admin_users.full_name DESC"; 
		  $Orig_code = "8-D";
		  $Rev_code  = "8-A";
		  $label	 	 = "User";
		  $label_symbol	 = "User &#x25BC;";
		 break;
		 case '9-A':
		  $query = " country.countryName ASC";
		  $Orig_code = "9-A";
		  $Rev_code  = "9-D";
		  $label	 	 = "Country";
		  $label_symbol	 = "Country &#x25B2;";
		 break;
		 case '9-D':
		  $query = " country.countryName DESC"; 
		  $Orig_code = "9-D";
		  $Rev_code  = "9-A";
		  $label	 	 = "Country";
		  $label_symbol	 = "Country &#x25BC;";
		 break;
		}
		if($returnType=='query')
			return $query;
		elseif($returnType=='Orig_code')
			return $Orig_code;
		elseif($returnType=='Rev_code')
			return $Rev_code;
		elseif($returnType=='label')
			return $label;
		elseif($returnType=='label_symbol')
			return $label_symbol;
	}
	
	function convertDate($type='',$dateTime='')
	{
	 $utc_date = DateTime::createFromFormat(
		"Y-m-d H:i:s",
		"$dateTime",
		new DateTimeZone('UTC')
	  );
      
	  if($dateTime=="0000-00-00 00:00:00"){ return "0000-00-00"; die(); }
	  
	  $new_date_format = clone $utc_date; // we don't want PHP's default pass object by reference here
	  //SELECT TIME ZONE FROM THE DB
	  $sql = $this->db->query("SELECT time_zone FROM country WHERE id = ". $_SESSION['countryID']." LIMIT 0,1");
	  $sql = $sql->row();
	  $new_date_format->setTimeZone(new DateTimeZone($sql->time_zone));
	  
	  if($type=='date') return $new_date_format->format('Y-m-d');
	  else 				return $new_date_format->format('H:i:s');
	}
	
    function logs()
    {
	$this->modules->module_checker(46,'REVIEW');
	
	//USER MANUAL
	$data['USER_MANUAL'] = $this->modules->user_manual(43);
	
	$WHERE = "WHERE ";
	if($_SESSION['super_admin']!='y' AND $_SESSION['countryID']!=0)
		$WHERE = " WHERE country_id = ".$_SESSION['countryID']." AND  ";
	
	$sort='n';
	extract($_POST);
	$cond="";
	if((isset($DateFrom) AND isset($DateTo)) AND ($DateFrom!='' AND $DateTo!='') AND !isset($Reset)){
		$dateFrom = $_POST['DateFrom'];
		$dateTo   = $_POST['DateTo'];
		$WHERE   .= " (logs.tdate >= '$dateFrom' AND logs.tdate <= '$dateTo') ";
	}else{
		$dateFrom = date('Y-m-d');
		$dateTo   = date('Y-m-d');
		$WHERE   .= " logs.tdate = '$dateFrom' ";
	}
	
	$data['DateFrom'] = $dateFrom;
	$data['DateTo']   = $dateTo;
	
	//TOTAL NUMBER OF ROWS	
	
	if(isset($Submit) OR isset($selpage))
	{	
		$val1 = mysql_real_escape_string($val1);
		$val1 = trim($val1);
		$val2 = mysql_real_escape_string($val2);
		$val2 = trim($val2);
		
		$condition = '';
		switch($cond1){
			case 'equal': 
				$condition = '=';
			break;
			case 'containing': 
				$condition = 'like';
			break;
			case 'in': 
				$condition = 'in';
			break;
			case 'greaterThan': 
				$condition = '>=';
			break;
			case 'lessThan': 
				$condition = '<=';
			break;
			case 'between': 
					$condition = 'between';
			break;
		}
		
		if(($opt1=='itemCode' OR $opt1=='action' OR $opt1=='rec_name' OR $opt1=='module_name' OR $opt1=='table_name' OR $opt1=='ttime') AND $val1!='')
		{
			if($condition=='=' OR $condition=='>=' OR $condition=='<=')
				$cond = "  $opt1 $condition '$val1'";
			if($condition=='like')
				$cond = "  $opt1 $condition '%$val1%'";
			if($condition=='in')
				$cond = "  $opt1 $condition ('" . str_replace(",", "','", $val1) . "')";
			if($condition=='between' & $this->checkStr($val1)==TRUE)
					$cond = "  $opt1 $condition ".stripslashes($val1);
		}
	
		//VIEWS
		if(($opt1=='rec_id') AND $val1!='' AND $condition!='like'){ 
			if($condition=='in' AND (is_numeric($val1) OR strpos($val1,',')==TRUE))
				$cond = "  $opt1 $condition ('" . str_replace(",", "','", $val1) . "')";
			elseif($condition!='in' AND is_numeric($val1) AND $condition!='between')
				$cond = "  $opt1 $condition $val1";
		}
		
		if($condition=='between' & $this->checkStr($val1)==TRUE)
			$cond = "  $opt1 $condition ".stripslashes($val1);
		
		//USERS
		if(($opt1=='user_id') AND $val1!='')
		{
			if($condition=='=' OR $condition=='>=' OR $condition=='<=')
				$cond = "  admin_users.full_name $condition '$val1'";
			if($condition=='like')
				$cond = "  admin_users.full_name $condition '%$val1%'";
			if($condition=='in')
				$cond = "  admin_users.full_name $condition ('" . str_replace(",", "','", $val1) . "')";
		}
		
		//COUNTRY
		if(($opt1=='country_id') AND $val1!='')
		{
			if($condition=='=' OR $condition=='>=' OR $condition=='<=')
				$cond = "  country.countryName $condition '$val1'";
			if($condition=='like')
				$cond = "  country.countryName $condition '%$val1%'";
			if($condition=='in')
				$cond = "  country.countryName $condition ('" . str_replace(",", "','", $val1) . "')";
		}
		
		/*2ND SET*/
		switch($cond2){
			case 'equal': 
				$condition2 = '=';
			break;
			case 'containing': 
				$condition2 = 'like';
			break;
			case 'in': 
				$condition2 = 'in';
			break;
			case 'greaterThan': 
				$condition2 = '>=';
			break;
			case 'lessThan': 
				$condition2 = '<=';
			break;
			case 'between': 
					$condition2 = 'between';
			break;
		}
		
		if(($opt2=='itemCode' OR $opt2=='action' OR $opt2=='rec_name' OR $opt2=='module_name' OR $opt2=='table_name' OR $opt2=='ttime') AND $val2!='')
		{
			if($condition2=='=' OR $condition2=='>=' OR $condition2=='<=')
				$cond .= " $operator $opt2 $condition2 '$val2'";
			if($condition2=='like')
				$cond .= " $operator $opt2 $condition2 '%$val2%'";
			if($condition2=='in')
				$cond .= " $operator $opt2 $condition2 ('" . str_replace(",", "','", $val2) . "')";
			if($condition2=='between'  & $this->checkStr($val2)==TRUE)
					$cond .= "  $operator $opt2 $condition2 ".stripslashes($val2);
		}
	
		//VIEWS
		if(($opt2=='rec_id') AND $val2!='' AND $condition2!='like'){ 
			if($condition2=='in' AND (is_numeric($val2) OR strpos($val2,',')==TRUE))
				$cond .= " $operator $opt2 $condition2 ('" . str_replace(",", "','", $val2) . "')";
			elseif($condition2!='in' AND is_numeric($val2) AND $condition2!='between' )
				$cond .= " $operator $opt2 $condition2 $val2";
		}
		
		if($condition2=='between' & $this->checkStr($val2)==TRUE)
			$cond .= "  $operator $opt2 $condition2 ".stripslashes($val2);
		
		//USERS
		if(($opt2=='user_id') AND $val2!='')
		{
			if($condition2=='=' OR $condition2=='>=' OR $condition2=='<=')
				$cond .= " $operator admin_users.full_name $condition2 '$val2'";
			if($condition2=='like')
				$cond .= " $operator admin_users.full_name $condition2 '%$val2%'";
			if($condition2=='in')
				$cond .= " $operator  admin_users.full_name $condition2 ('" . str_replace(",", "','", $val2) . "')";
		}
		
		//COUNTRY
		if(($opt2=='country_id') AND $val2!='')
		{
			if($condition2=='=' OR $condition2=='>=' OR $condition2=='<=')
				$cond .= " $operator country.countryName $condition2 '$val2'";
			if($condition2=='like')
				$cond .= " $operator country.countryName $condition2 '%$val2%'";
			if($condition2=='in')
				$cond .= " $operator country.countryName $condition2 ('" . str_replace(",", "','", $val2) . "')";
		}
		
		$cond = ($cond=="") ? "" : " AND ($cond) ";
		$WHERE  .=  " $cond ";
	}
	
	//IF THEIR NO POSSIBLE RESULT
	$valid_query=TRUE;
	if(isset($Submit)){
		if($cond=="" AND ($val1!='' OR $val2!='')){ 
		$valid_query=FALSE;
		$WHERE = "WHERE logs.id=0";
		}
	}
	
	/*LIMIT*/
	$limit =isset($_POST['selpage'])? $_POST['selpage']:0;
	
	$data['POST']      = $_POST;
	if(isset($Reset)){
		$data['POST'] = array();
		$limit = 0;
		$WHERE   = "WHERE logs.tdate = '".date('Y-m-d')."' ";
	}
	
	//ORDER
	$ORDER 		   = $this->sortingRef_Logs('7-D','query');
	$data['order'] = $this->sortingRef_Logs('7-D','Orig_code');
	$order_code = '7-D';
	$label 		= "Time";
	if(isset($order)){
		$ORDER 		   = $this->sortingRef_Logs($order,'query');
		$data['order'] = $this->sortingRef_Logs($order,'Orig_code');
		$order_code    = $order;
		$label 	       = $this->sortingRef_Logs($order,'label');
	}
	
	/*LIMIT*/
	$sql = "SELECT *, logs.id as lID, admin_users.full_name as fullname, country.countryName as cName  
			FROM logs 
			LEFT JOIN admin_users ON admin_users.id = logs.user_id 
			LEFT JOIN country ON country.id 		= logs.country_id
			$WHERE ORDER BY $ORDER";

	
	$sql_csv = $this->db->query("SELECT rec_id, itemCode, action, rec_name, module_name,
								table_name, tdate, ttime, admin_users.full_name as fullname, country.countryName  as cName
							    FROM logs 
								LEFT JOIN admin_users ON admin_users.id = logs.user_id 
								LEFT JOIN country ON country.id 		= logs.country_id
								$WHERE ORDER BY $ORDER");
	$sql_csv = $sql_csv->result_array();
	$data['csvFile']			= "SMBi_Logs".$this->reportCode().".csv";
	
	$all_items = $this->db->query($sql);
	$all_items = $all_items->result_array();
	
	//SORT STATUS
	$limit = ($sort)=='y' ? 0 : $limit;
	
	$limit_items = $this->db->query($sql." LIMIT $limit,20");
	$limit_items = $limit_items->result_array();
	$data['totrec'] = count($all_items);
	$data['limit']  = $limit;
	
	$items	= $limit_items;	
	
	$csv  = "No, REC ID, Item Code, Action, Record Name, Module, Table, Date, Time, User, Country \n"; $x=0;
	foreach($sql_csv as $sql_c)
	{ extract($sql_c); $x++;
	  $csv  .= "$x, $rec_id, $itemCode, $action, $rec_name, $module_name, $table_name,".$this->convertDate('date',"$tdate $ttime").",".$this->convertDate('time',"$tdate $ttime").",$fullname, $cName \n";
	}
	write_file(getcwd()."/files/csv/SMBi_Logs".$this->reportCode().".csv",$csv);
	
	$table= "<table id='large2' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:12px;margin-top:13px;' class='iLike_Result_Table2'>
			<tr style='height: 22px;'>
				<th style='width:25px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'>	   No.  	  		  				  					  </th> 
				<th style='width:48px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"0-D\")'>           <b>Rec ID  	   	  </b></th> 
				<th style='width:75px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"1-D\")'>           <b>Item Code  	  </b></th> 
				<th style='width:50px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"2-D\")'>           <b>Action  	  	  </b></th> 
				<th style='width:190px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"3-A\")'>           <b>Record Name  	  </b></th> 
				<th style='width:120px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"4-A\")'>           <b>Module  	  	  </b></th> 
				<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"5-A\")'>           <b>Table  	  	  </b></th> 
				<th style='width:72px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"6-A\")'>        	  <b>Date  	  		  </b></th> 
				<th style='width:60px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"7-D\")'>   	      <b>Time  	  	  	  </b></th> 
				<th style='width:125px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"8-D\")'>    		  <b>User  	  	  	  </b></th> 
				<th style='width:77px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"9-A\")'>        	  <b>Country  	  	  </b></th> 
			</tr>";
		
	//REPLACE ORDER
	$table = str_replace($order_code,$this->sortingRef_Logs($order_code,'Rev_code'),$table);
	$table = str_replace($label,$this->sortingRef_Logs($order_code,'label_symbol'),$table);
		
	$x = $limit;			
	$y=1;
	$z=1;
	$total=0;
	$total_target=0;
	$orig_itemName="";
	if($valid_query==TRUE)
	{
	 foreach($items as $r) { 
	 extract($r);
	 $c = (($x++)%2) == 0 ? "class='alter'" :  ""; 			
     
	 $table.= "<tr>
			  <td $c>													$x </td>
			  <td $c style='text-align:center;padding-left:5px;'>	$rec_id	  						</td>
			  <td $c style='text-align:left;padding-left:5px;'>	$itemCode						</td>
			  <td $c style='text-align:left;padding-left:5px;'>	$action							</td>
			  <td $c style='text-align:left;padding-left:5px;'>	$rec_name						</td>
			  <td $c style='text-align:left;padding-left:5px;'>	$module_name					</td>
			  <td $c style='text-align:left;padding-left:5px;'>	$table_name						</td>
			  <td $c style='text-align:left;padding-left:5px;'>	". $this->convertDate('date',"$tdate $ttime") ."</td>
			  <td $c style='text-align:left;padding-left:5px;'> ". $this->convertDate('time',"$tdate $ttime") ."</td>
			  <td $c style='text-align:left;padding-left:5px;'>	$fullname						</td>
			  <td $c style='text-align:left;padding-left:5px;'>	$cName						</td>
			</tr>";
	 }
	}
	if(!$items OR $valid_query==FALSE)
		$table.=  "<tr><td colspan='20'>Sorry no items found, check your search parameters.</td></tr>";
	
	$table.= "</table>";		
	$data['table'] 		= $table;
	
    $data['vfile']			= 'logs.php';
	$data['title']			= 'System Logs';
	$data['breadCrumbs']	 = '<a href='.HTTP_PATH.'report>Analytics </a>';
	$data['breadCrumbs']	.= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	$data['breadCrumbs']	.= '<a href='.HTTP_PATH.'report/logs> System Logs </a>';
	 

	if($this->modules->access_checker()==TRUE)
	{
		$this->load->view('innerPages',$data); 
	}else{
		$data['vfile']				= 'login.php';
		$data['title']				= 'SMBi System Log-in | SMBi';
		$data['page_title']			= 'SMBi System Log-in';
		$data['meta_description']	= 'San Miguel Brewing International';
		$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
		$this->load->view('login',$data); 	
	}
   }
    
	function partial($id)
    {
     $CI2 =& get_instance();
	 $CI2->load->library('fv');
		
	 $sql = "SELECT itemID, IFNULL((itm.itemName),'Sorry this is has been purged') as itemName , IFNULL(itm.itemCode,'-') as iCode, campaignID,
			   (SELECT COUNT( id ) FROM votexRef AS vref WHERE vref.itemID = itemREF.itemID AND vote =  'yes' and vref.campaignID =$id 
			   AND vref.voterID IN (SELECT voters.id FROM voters WHERE voters.id = vref.voterID)) AS voteTot,
			   IFNULL((select typeName from POSM_Type as pt where pt.id=i.POSMTypeID),'-') as ptype, 
			   IFNULL((SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = i.id LIMIT 0,1),'blank.png') as item_image, extra_label, itm.USD_Price as uPrice,  countryName, itm.dateReleased as dReleased
			   FROM  `campaignItemsXref` AS itemREF 
			   LEFT JOIN items AS i ON itemREF.itemID = i.id  
			   left join items as itm on itemREF.itemID=itm.id 
			   LEFT JOIN price_range ON price_range.id = itm.price_rangeID
			   LEFT JOIN country ON country.id =  itm.countryID
			   where   itemREF.campaignID=$id 
			   ORDER BY ptype ASC, price_range.id ASC, voteTot DESC";
	 
	 $report    = $this->db->query($sql);  
	 $rep       = $report->result_array(); 
	 
	 
	 $sql       = "select *,full_name from campaign as c inner join admin_users as u on c.adminCreatorID=u.id where c.id='$id'   ";
	 $header    = $this->db->query($sql);  
	 $header    = $header->result_array(); 
	 
	 $sql   		  = "SELECT * FROM voters WHERE campaignID = $id"; 
	 $voters    	  = $this->db->query($sql);  
	 $data['voters']  = $voters->result_array();
	 
	 //CAMPAIGN REF
	 $sql   		  = "SELECT * FROM iLike_Rules_Ref WHERE campaignID = $id"; 
	 $voters    	  = $this->db->query($sql);  
	 $data['iLike_Rules_Ref']  = $voters->result_array();
	 
	 $sql   		  = "SELECT * FROM iLike_Rules_No_Committes_Ref WHERE campaignID = $id"; 
	 $voters    	  = $this->db->query($sql);  
	 $data['iLike_Rules_No_Committes_Ref']  = $voters->result_array();
	 
	 $sql   		  = "SELECT * FROM iLikeCanvassingRulesXref WHERE campaignID = $id"; 
	 $voters    	  = $this->db->query($sql);  
	 $data['iLikeCanvassingRulesXref']  = $voters->result_array();
	 
	 $sql   		  = "SELECT * FROM iLikeVotingRulesRef WHERE campaignID = $id"; 
	 $voters    	  = $this->db->query($sql);  
	 $data['iLikeVotingRulesRef']  = $voters->result_array();
	 
	 $sql = "SELECT *, iLikeVotingRulesRef.price_rangeID as pRangeID FROM iLikeVotingRulesRef WHERE campaignID = $id 
			ORDER BY price_rangeID ASC, fieldID DESC, rel DESC, val ASC";
	$sql 			   = $this->db->query($sql);
	$iLikeVotingRules = $sql->result_array();
	$campaignID = $id;
	foreach($iLikeVotingRules as $iL)
	{
		extract($iL);
		$itemDB = "SELECT count(items.id) as tot_items
				   FROM items  
				   LEFT JOIN campaignItemsXref ON campaignItemsXref.itemID = items.id  
				   WHERE campaignItemsXref.campaignID = $campaignID
				   AND (items.$fieldName = $fieldID) AND items.price_rangeID =  $pRangeID";
		
		$orig_fieldName = $fieldName;
		switch($fieldName){
				case("POSMTypeID"):
					$tableName	= $CI2->fv->label(4);;
					$fieldName  = 'typeName';
					$table 		= 'POSM_Type';
				break;
				case("POSMStatusID"):
					$tableName	= 'ITEM STATUS';
					$fieldName  = 'statusName';
					$table 		= 'POSM_Status';
				break;
				case("OUTLETStatusID"):
					$tableName	= $CI2->fv->label(6); ;
					$fieldName  = 'statusName';
					$table 		= 'OUTLET_Status';
				break;
				case("PremiumTypeID"):
					$tableName	= $CI2->fv->label(7);;
					$fieldName  = 'premiumTypeName';
					$table 		= 'premiumItemType';
				break;
				case("MaterialTypeID"):
					$tableName	= $CI2->fv->label(9);;
					$fieldName  = 'materialName';
					$table 		= 'MATERIAL_Type';
				break;
				case("brandID"):
					$tableName	= $CI2->fv->label(3);;
					$fieldName  = 'brandName';
					$table 		= 'brands';
				break;
		}
		
		$query 		= $this->db->query("SELECT $fieldName FROM $table WHERE id=$fieldID LIMIT 0,1");
		$row 		= $query->row();
		$name_Field = $row->$fieldName;
		
		//GET PRICE RANGE NAME
		$query 		= $this->db->query("SELECT extra_label FROM price_range WHERE id=$price_rangeID LIMIT 0,1");
		$row 		= $query->row();
		$extra_label = $row->extra_label;
		
		$s 		   		= $this->db->query($itemDB);
		$data['items']  = $s->result_array();		
		//print_r($data['items']);
		$status="Good";
		
		//TEST EVERY RULE
		$tot_items = $data['items'][0]['tot_items'];
		$min_vote = $min_val;
		$max_vote = $max_val;
		if($cond1=="==") $cond1="=";
		if($cond2=="==") $cond2="=";
		if(strpos($min_val,".")==TRUE)
			$min_vote = round($min_val * $data['items'][0]['tot_items']);
		if(strpos($max_val,".")==TRUE)
			$max_vote = round($max_val * $data['items'][0]['tot_items']);
		
		$min_number_of_items = $min_vote;
		
		if($logical_operator!="" AND $max_vote!="" AND $cond2!=""){
		$sql = $this->db->query("SELECT $tot_items >= $min_vote  as result LIMIT 0,1");			
		}else{
		$sql = $this->db->query("SELECT $tot_items >= $min_vote as result LIMIT 0,1");
		}
		$sql = $sql->row();
		if($sql->result==0) $status="Not Good";
		
		//PERCENTAGE
		if(strpos($min_val,".")==TRUE)
		 $min_val  = "$min_val% (". round($min_val * $data['items'][0]['tot_items']) .")";
		if(strpos($max_val,".")==TRUE)
		 $max_val  = "$max_val% (". round($max_val * $data['items'][0]['tot_items']) .")";	
		
		
		$rules[] = array(
						'cond1'				=>$cond1,
						'min_val'			=>$min_val,
						'logical_operator'	=>$logical_operator,
						'cond2'				=>$cond2,
						'max_val'			=>$max_val,
						'price_rangeName'	=>$extra_label,
						'table'				=>$table,
						'fieldName'			=>$orig_fieldName, 
						'fieldID'			=>$fieldID, 
						'fieldValue'		=>$name_Field, 
						'min_number_of_items'=>$min_number_of_items, 
						'stat'				=>$status,
						'current_num_items' =>$data['items'][0]['tot_items']
						);
	}
	$data['VotingRules'] 		   = $rules;
	
	$sql 				  = "SELECT *, iLikeCanvassingRulesXref.price_rangeID as pRangeID FROM iLikeCanvassingRulesXref WHERE campaignID = $campaignID 
							 ORDER BY price_rangeID ASC, fieldID DESC, rel DESC, val ASC";
	$sql 				  = $this->db->query($sql);
	$iLikeCanvassingRules = $sql->result_array();	
	foreach($iLikeCanvassingRules as $iL)
	{
		extract($iL);		
		$orig_fieldName = $fieldName;
		switch($fieldName){
				case("POSMTypeID"):
					$tableName	= $CI2->fv->label(4);;
					$fieldName  = 'typeName';
					$table 		= 'POSM_Type';
				break;
				case("POSMStatusID"):
					$tableName	= 'ITEM STATUS';
					$fieldName  = 'statusName';
					$table 		= 'POSM_Status';
				break;
				case("OUTLETStatusID"):
					$tableName	= $CI2->fv->label(6); ;
					$fieldName  = 'statusName';
					$table 		= 'OUTLET_Status';
				break;
				case("PremiumTypeID"):
					$tableName	= $CI2->fv->label(7);;
					$fieldName  = 'premiumTypeName';
					$table 		= 'premiumItemType';
				break;
				case("MaterialTypeID"):
					$tableName	= $CI2->fv->label(9);;
					$fieldName  = 'materialName';
					$table 		= 'MATERIAL_Type';
				break;
				case("brandID"):
					$tableName	= $CI2->fv->label(3);;
					$fieldName  = 'brandName';
					$table 		= 'brands';
				break;
		}
		
		//TOTAL ITEMS
		$itemDB = "SELECT items.id as iID
				   FROM items  
				   LEFT JOIN campaignItemsXref ON campaignItemsXref.itemID = items.id  
				   WHERE campaignItemsXref.campaignID = $campaignID
				   AND (items.POSMTypeID = $fieldID) AND items.price_rangeID =  $price_rangeID";
		$s 		   		= $this->db->query($itemDB);
		$data['items']  = $s->result_array();	
		
		$query 		= $this->db->query("SELECT $fieldName FROM $table WHERE id=$fieldID LIMIT 0,1");
		$row 		= $query->row();
		$name_Field = $row->$fieldName;
		
		//GET PRICE RANGE NAME
		$query 		= $this->db->query("SELECT extra_label FROM price_range WHERE id=$price_rangeID LIMIT 0,1");
		$row 		= $query->row();
		$extra_label = $row->extra_label;
		
		$status="";
		$original_val =$val;
		
		
		if(strpos($min_val,".")==TRUE) $min_val = $min_val."% (".round(count($data['items'])*$min_val).")";
		if(strpos($max_val,".")==TRUE) $max_val = $max_val."% (".round(count($data['items'])*$max_val).")";
		
		$rules2[] = array(
						'cond1'				=>$cond1,
						'min_val'			=>$min_val,
						'logical_operator'	=>$logical_operator,
						'cond2'				=>$cond2,
						'max_val'			=>$max_val,
						'price_rangeName'	=>$extra_label,
						'table'				=>$table,
						'fieldName'			=>$orig_fieldName, 
						'fieldID'			=>$fieldID, 
						'fieldValue'		=>$name_Field, 
						'rel'				=>$rel,
						'val'				=>"$original_val",
						'lrel'				=>$lrel,
						'stat'				=>$status
						);
	}
	$data['iLikeCanvassingRulesXref']  = $rules2;
	 
     $data['vfile']			= 'partialRep.php';
	 $data['title']			= 'iLike Report';
	 $data['rep']			= $rep;
	 $data['repHeader']		= $header;
	 $data['breadCrumbs']	= '<a href='.HTTP_PATH.'iLikeCampaign/votingCampaign> iLike Campaign </a>';
	 $data['cID']			= $id;
	 
	if($this->modules->access_checker()==TRUE)
	{
		$this->load->view('innerPages',$data); 
	}else{
		$data['vfile']				= 'login.php';
		$data['title']				= 'SMBi System Log-in | SMBi';
		$data['page_title']			= 'SMBi System Log-in';
		$data['meta_description']	= 'San Miguel Brewing International';
		$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
		$this->load->view('login',$data); 	
	}

   }  
    
	function vote_items($ctype='',$cID='',$vID)
	{
		if($ctype=='iLike')
		{
			$sql = "SELECT itemID, IFNULL(items.itemName,'Sorry this is has been purged') AS itemName, 
					IFNULL(items.itemCode,'-') as itemCode, 
				    IFNULL((SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id LIMIT 0,1),'blank.png') as item_image, 
					POSM_Type.typeName as ptype, extra_label, items.USD_Price as uPrice,  countryName, items.dateReleased as dReleased
					FROM votexRef 
					LEFT JOIN items     	ON items.id 		= votexRef.itemID
					LEFT JOIN POSM_Type 	ON items.POSMTypeID = POSM_Type.id
					LEFT JOIN country 		ON country.id 		= items.countryID
					LEFT JOIN price_range   ON price_range.id   = items.price_rangeID
					WHERE campaignID=$cID AND voterID = $vID AND vote='yes'
					ORDER BY ptype ASC, price_range.id ASC;
					";
			
			$sql   = $this->db->query($sql);
			$items = $sql->result_array();		
			echo "<table id='large' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:12px;' class='iLike_Result_Table tablesorter'>
				<tbody>";
				$iType="";
				$pType=""; 
				
				$x = 0;			
				$y=1;
				$z=1;
				$total=0;
				$total_target=0;
				foreach($items as $r) { 
				extract($r);
				$itemName =  ($itemCode!='-') ? "<a href='".HTTP_PATH."gallery/itemInfo2/$itemID' target='_blank'> $itemName </a>" : "$itemName";
				if($iType!=$ptype) 		 echo "<tr> <td colspan='7' style='background:#900808;color:white;font-size:16px;text-align:left;padding:10px;'>$ptype</td></tr>";
				if($pType!=$extra_label){ echo "<tr> <td colspan='7' style='background:#AFAFAF;color:black;font-size:12px;text-align:left;padding: 5px 5px 5px 10px;'>$extra_label</td></tr>"; $x=0;}
				if($iType!=$ptype) {
				echo  "<tr style='border-radius: 6px;'>
						<td style='width:10px;text-align:center;' bgcolor='#d3d3d3'>   <b>No 		 </b></td> 
						<td style='width:76px;text-align:center;' bgcolor='#d3d3d3'>   <b>Item Code  </b></td> 
						<td style='width:76px;text-align:center;' bgcolor='#d3d3d3'>   <b>USD Price  </b></td> 
						<td style='width:50px;text-align:center;' bgcolor='#d3d3d3'>   <b>Image  	 </b></td> 
						<td style='width:150px;text-align:center;' bgcolor='#d3d3d3'>  <b>Item Name  </b></td> 
						<td style='width:150px;text-align:center;' bgcolor='#d3d3d3'>  <b>Country    </b></td> 
						<td style='width:150px;text-align:center;' bgcolor='#d3d3d3'>  <b>Date Released    </b></td> 
					</tr>";
				}
			
				$ptype = ($ptype=='') 	   ? '-' : $ptype;
				$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 	
			echo 	"<tr>
					  <td $c>													$x      																		</td>
					   <td $c style='text-align:left;padding-left:5px;'>		$itemCode											</td>
					  <td $c>													$uPrice      																	</td>
					  <td $c style='text-align:center;'>		<img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 	</td>
					  <td $c style='text-align:left;padding-left:5px;'>			$itemName  </td>
					  <td $c style='text-align:left;padding-left:5px;'>			$countryName  </td>
					  <td $c style='text-align:left;padding-left:5px;'>			$dReleased  </td>
					</tr>";
			$iType = $ptype;
			$pType = $extra_label;		
			}
					
					
			echo	"</tbody>";
						if(!$items)
							echo "<tr><td colspan='7'>No match found.</td></tr>";
			echo	"</table>";
		}elseif($ctype=='iWant')
		{
			$sql = "SELECT itemID, IFNULL(items.itemName,'Sorry this is has been purged') AS itemName, 
					IFNULL(items.itemCode,'-') as itemCode, 
				    IFNULL((SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id LIMIT 0,1),'blank.png') as item_image, 
					POSM_Type.typeName as ptype, extra_label, items.USD_Price as uPrice,  countryName, items.dateReleased as dReleased
					FROM votexRef 
					LEFT JOIN items     	ON items.id 		= votexRef.itemID
					LEFT JOIN POSM_Type 	ON items.POSMTypeID = POSM_Type.id
					LEFT JOIN country 		ON country.id 		= items.countryID
					LEFT JOIN price_range   ON price_range.id   = items.price_rangeID
					WHERE campaignID=$cID AND voterID = $vID AND vote='yes'
					ORDER BY ptype ASC, price_range.id ASC;
					";
			
			$sql   = $this->db->query($sql);
			$items = $sql->result_array();		
			echo "<table id='large' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:12px;' class='iLike_Result_Table tablesorter'>
				<tbody>";
				$iType="";
				$pType=""; 
				
				$x = 0;			
				$y=1;
				$z=1;
				$total=0;
				$total_target=0;
				foreach($items as $r) { 
				extract($r);
				$itemName =  ($itemCode!='-') ? "<a href='".HTTP_PATH."gallery/itemInfo2/$itemID' target='_blank'> $itemName </a>" : "$itemName";
				if($iType!=$ptype) 		 echo "<tr> <td colspan='7' style='background:#900808;color:white;font-size:16px;text-align:left;padding:10px;'>$ptype</td></tr>";
				if($pType!=$extra_label){ echo "<tr> <td colspan='7' style='background:#AFAFAF;color:black;font-size:12px;text-align:left;padding: 5px 5px 5px 10px;'>$extra_label</td></tr>"; $x=0;}
				if($iType!=$ptype) {
				echo  "<tr style='border-radius: 6px;'>
						<td style='width:10px;text-align:center;' bgcolor='#d3d3d3'>   <b>No 		 </b></td> 
						<td style='width:76px;text-align:center;' bgcolor='#d3d3d3'>   <b>Item Code  </b></td> 
						<td style='width:76px;text-align:center;' bgcolor='#d3d3d3'>   <b>USD Price  </b></td> 
						<td style='width:50px;text-align:center;' bgcolor='#d3d3d3'>   <b>Image  	 </b></td> 
						<td style='width:150px;text-align:center;' bgcolor='#d3d3d3'>  <b>Item Name  </b></td> 
						<td style='width:150px;text-align:center;' bgcolor='#d3d3d3'>  <b>Country    </b></td> 
						<td style='width:150px;text-align:center;' bgcolor='#d3d3d3'>  <b>Date Released    </b></td> 
					</tr>";
				}
			
				$ptype = ($ptype=='') 	   ? '-' : $ptype;
				$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 	
			echo 	"<tr>
					  <td $c>													$x      																		</td>
					   <td $c style='text-align:left;padding-left:5px;'>		$itemCode											</td>
					  <td $c>													$uPrice      																	</td>
					  <td $c style='text-align:center;'>		<img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 	</td>
					  <td $c style='text-align:left;padding-left:5px;'>			$itemName  </td>
					  <td $c style='text-align:left;padding-left:5px;'>			$countryName  </td>
					  <td $c style='text-align:left;padding-left:5px;'>			$dReleased  </td>
					</tr>";
			$iType = $ptype;
			$pType = $extra_label;		
			}
			echo	"</tbody>";
						if(!$items)
							echo "<tr><td colspan='7'>No match found.</td></tr>";
			echo	"</table>";
		}
	}
	
	function iWant_Items($id)
    {
     $CI2 =& get_instance();
	 $CI2->load->library('fv');
	 
	 $this->iWant_ReportCSV($id); 
	 $data['csvFile'] = "iWant_Report".$this->reportCode().".csv";
	 $data['campaignID'] = $id;
	 
	 $voter_AND = "";
	 $sql = "SELECT itemID, IFNULL((itm.itemName),'Sorry this is has been purged') as itemName , IFNULL(itm.itemCode,'-') as iCode, campaignID,
			   (SELECT COUNT( id ) FROM votexRef AS vref WHERE vref.itemID = itemREF.itemID AND vote =  'yes' and vref.campaignID =$id 
			   AND vref.voterID IN (SELECT voters.id FROM voters WHERE voters.id = vref.voterID)) AS voteTot,
			   IFNULL((select typeName from POSM_Type as pt where pt.id=i.POSMTypeID),'-') as ptype, 
			   IFNULL((SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = i.id),'blank.png') as item_image, extra_label, 
			   itm.USD_Price as uPrice,  countryName, itm.dateReleased as dReleased
			   FROM  `campaignItemsXref` AS itemREF 
			   LEFT JOIN items AS i ON itemREF.itemID = i.id  
			   left join items as itm on itemREF.itemID=itm.id 
			   LEFT JOIN price_range ON price_range.id = itm.price_rangeID
			   LEFT JOIN country ON country.id =  itm.countryID
			   where   itemREF.campaignID=$id 
			   ORDER BY ptype ASC, price_range.id ASC, voteTot DESC, countryName ASC";
	 
	 $report    = $this->db->query($sql);  
	 $rep       = $report->result_array(); 
	 
	 $sql 			   = $this->db->query("SELECT POSM_Type.id as pID, typeName FROM POSM_Type ORDER BY typeName ASC");
	$data['POSM_Type'] = $sql->result_array();
	 
	 $sql       = "select *,full_name from campaign as c inner join admin_users as u on c.adminCreatorID=u.id where c.id='$id'   ";
	 $header    = $this->db->query($sql);  
	 $header    = $header->result_array(); 
	 
	 $sql   		  = "SELECT *, voters.id as voterID FROM voters 
						LEFT JOIN country ON voters.Fields001 = country.id 
						where campaignID = $id $voter_AND ORDER BY country.id ASC"; 
	 $voters    	  = $this->db->query($sql);  
	 $data['voters']  = $voters->result_array();
	 
	 
	 $sql   		  = "SELECT * FROM iWantCampaignNumber_of_commitees_ref WHERE campaignID = $id"; 
	 $voters    	  = $this->db->query($sql);  
	 $data['iWant_Rules_No_Committes_Ref']  = $voters->result_array();
	 
	 $sql   		  = "SELECT * FROM iWantCanvassingRulesRef WHERE campaignID = $id"; 
	 $voters    	  = $this->db->query($sql);  
	 $data['iWantCanvassingRulesXref']  = $voters->result_array();
	 
	 $sql   		  = "SELECT * FROM iWantVotingRulesRef WHERE campaignID = $id"; 
	 $voters    	  = $this->db->query($sql);  
	 $data['iWantVotingRulesRef']  = $voters->result_array();
	 
	 $sql = "SELECT *, iWantVotingRulesRef.price_rangeID as pRangeID FROM iWantVotingRulesRef WHERE campaignID = $id 
			ORDER BY price_rangeID ASC, fieldID DESC, rel DESC, val ASC";
	$sql 			   = $this->db->query($sql);
	$iWantVotingRules = $sql->result_array();
	$campaignID = $id;
	foreach($iWantVotingRules as $iL)
	{
		extract($iL);
		$itemDB = "SELECT count(items.id) as tot_items
				   FROM items  
				   LEFT JOIN campaignItemsXref ON campaignItemsXref.itemID = items.id  
				   WHERE campaignItemsXref.campaignID = $campaignID
				   AND (items.$fieldName = $fieldID) AND items.price_rangeID =  $pRangeID";
		
		$orig_fieldName = $fieldName;
		switch($fieldName){
				case("POSMTypeID"):
					$tableName	= $CI2->fv->label(4);;
					$fieldName  = 'typeName';
					$table 		= 'POSM_Type';
				break;
				case("POSMStatusID"):
					$tableName	= 'ITEM STATUS';
					$fieldName  = 'statusName';
					$table 		= 'POSM_Status';
				break;
				case("OUTLETStatusID"):
					$tableName	= $CI2->fv->label(6); ;
					$fieldName  = 'statusName';
					$table 		= 'OUTLET_Status';
				break;
				case("PremiumTypeID"):
					$tableName	= $CI2->fv->label(7);;
					$fieldName  = 'premiumTypeName';
					$table 		= 'premiumItemType';
				break;
				case("MaterialTypeID"):
					$tableName	= $CI2->fv->label(9);;
					$fieldName  = 'materialName';
					$table 		= 'MATERIAL_Type';
				break;
				case("brandID"):
					$tableName	= $CI2->fv->label(3);;
					$fieldName  = 'brandName';
					$table 		= 'brands';
				break;
		}
		
		$query 		= $this->db->query("SELECT $fieldName FROM $table WHERE id=$fieldID LIMIT 0,1");
		$row 		= $query->row();
		$name_Field = $row->$fieldName;
		
		//GET PRICE RANGE NAME
		$query 		= $this->db->query("SELECT extra_label FROM price_range WHERE id=$price_rangeID LIMIT 0,1");
		$row 		= $query->row();
		$extra_label = $row->extra_label;
		
		$s 		   		= $this->db->query($itemDB);
		$data['items']  = $s->result_array();		
		//print_r($data['items']);
		$status="Good";
		
		//TEST EVERY RULE
		$tot_items = $data['items'][0]['tot_items'];
		$min_vote = $min_val;
		$max_vote = $max_val;
		if($cond1=="==") $cond1="=";
		if($cond2=="==") $cond2="=";
		if(strpos($min_val,".")==TRUE)
			$min_vote = round($min_val * $data['items'][0]['tot_items']);
		if(strpos($max_val,".")==TRUE)
			$max_vote = round($max_val * $data['items'][0]['tot_items']);

		
		//PERCENTAGE
		if(strpos($min_val,".")==TRUE)
		 $min_val  = "$min_val% (". round($min_val * $data['items'][0]['tot_items']) .")";
		if(strpos($max_val,".")==TRUE)
		 $max_val  = "$max_val% (". round($max_val * $data['items'][0]['tot_items']) .")";	
		
		
		$rules[] = array(
						'cond1'				=>$cond1,
						'min_val'			=>$min_val,
						'logical_operator'	=>$logical_operator,
						'cond2'				=>$cond2,
						'max_val'			=>$max_val,
						'price_rangeName'	=>$extra_label,
						'table'				=>$table,
						'fieldName'			=>$orig_fieldName, 
						'fieldID'			=>$fieldID, 
						'fieldValue'		=>$name_Field, 
						'current_num_items' =>$data['items'][0]['tot_items']
						);
	}
	$data['VotingRules'] 		   = $rules;
	
	$sql 				  = "SELECT *, iWantCanvassingRulesRef.price_rangeID as pRangeID FROM iWantCanvassingRulesRef WHERE campaignID = $campaignID 
							 ORDER BY price_rangeID ASC, fieldID DESC, rel DESC, val ASC";
	$sql 				  = $this->db->query($sql);
	$iWantCanvassingRules = $sql->result_array();	
	foreach($iWantCanvassingRules as $iL)
	{
		extract($iL);		
		$orig_fieldName = $fieldName;
		switch($fieldName){
				case("POSMTypeID"):
					$tableName	= $CI2->fv->label(4);;
					$fieldName  = 'typeName';
					$table 		= 'POSM_Type';
				break;
				case("POSMStatusID"):
					$tableName	= 'ITEM STATUS';
					$fieldName  = 'statusName';
					$table 		= 'POSM_Status';
				break;
				case("OUTLETStatusID"):
					$tableName	= $CI2->fv->label(6); ;
					$fieldName  = 'statusName';
					$table 		= 'OUTLET_Status';
				break;
				case("PremiumTypeID"):
					$tableName	= $CI2->fv->label(7);;
					$fieldName  = 'premiumTypeName';
					$table 		= 'premiumItemType';
				break;
				case("MaterialTypeID"):
					$tableName	= $CI2->fv->label(9);;
					$fieldName  = 'materialName';
					$table 		= 'MATERIAL_Type';
				break;
				case("brandID"):
					$tableName	= $CI2->fv->label(3);;
					$fieldName  = 'brandName';
					$table 		= 'brands';
				break;
		}
		
		$query 		= $this->db->query("SELECT $fieldName FROM $table WHERE id=$fieldID LIMIT 0,1");
		$row 		= $query->row();
		$name_Field = $row->$fieldName;
		
		//GET PRICE RANGE NAME
		$query 		= $this->db->query("SELECT extra_label FROM price_range WHERE id=$price_rangeID LIMIT 0,1");
		$row 		= $query->row();
		$extra_label = $row->extra_label;
		
		$status="";
		$original_val =$val;
		if(strpos($min_val,".")==TRUE) $min_val = $min_val."% (".round(count($data['voters'])*$min_val).")";
		if(strpos($max_val,".")==TRUE) $max_val = $max_val."% (".round(count($data['voters'])*$max_val).")";
		
		$rules2[] = array(
						'cond1'				=>$cond1,
						'min_val'			=>$min_val,
						'logical_operator'	=>$logical_operator,
						'cond2'				=>$cond2,
						'max_val'			=>$max_val,
						'price_rangeName'	=>$extra_label,
						'table'				=>$table,
						'fieldName'			=>$orig_fieldName, 
						'fieldID'			=>$fieldID, 
						'fieldValue'		=>$name_Field, 
						'rel'				=>$rel,
						'val'				=>"$original_val",
						'lrel'				=>$lrel,
						'stat'				=>$status
						);
	}
	$data['iWantCanvassingRulesXref']  = $rules2;
	 
     $data['vfile']			= 'iWant_Items.php';
	 $data['title']			= 'iLike Report';
	 $data['rep']			= $rep;
	 $data['repHeader']		= $header;
	 $HTTP_PATH 					 = HTTP_PATH."report/iWant";
	 $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
	 $data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	 $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/iWant> iWant Report </a>';
	 $data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	 $data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/iWant_Items/$campaignID'>". $header[0]['campaignName'] ."</a>";
	 $data['cID']			= $id;
	 
	if($this->modules->access_checker()==TRUE)
	{
		$this->load->view('innerPages',$data); 
	}else{
		$data['vfile']				= 'login.php';
		$data['title']				= 'SMBi System Log-in | SMBi';
		$data['page_title']			= 'SMBi System Log-in';
		$data['meta_description']	= 'San Miguel Brewing International';
		$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
		$this->load->view('login',$data); 	
	}

   } 
	
	function iWantPartial($id)
    {
     $CI2 =& get_instance();
	 $CI2->load->library('fv');
	 $data['PUBLISH_CAMPAIGN'] =  $this->modules->crud_checker(29,'PUBLISH CAMPAIGN');
	 $voter_AND = "";
	 if($_SESSION['super_admin']!='y' AND $_SESSION['countryID']!=0) $voter_AND = " AND voters.Fields001 = ".$_SESSION['countryID'];
	 
	 $sql = "SELECT itemID, IFNULL((itm.itemName),'Sorry this is has been purged') as itemName , IFNULL(itm.itemCode,'-') as iCode, campaignID,
			   (SELECT COUNT( id ) FROM votexRef AS vref WHERE vref.itemID = itemREF.itemID AND vote =  'yes' and vref.campaignID =$id 
			   AND vref.voterID IN (SELECT voters.id FROM voters WHERE voters.id = vref.voterID)) AS voteTot,
			   IFNULL((select typeName from POSM_Type as pt where pt.id=i.POSMTypeID),'-') as ptype, 
			   IFNULL((SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = i.id),'blank.png') as item_image, extra_label, 
			   itm.USD_Price as uPrice,  countryName, itm.dateReleased as dReleased
			   FROM  `campaignItemsXref` AS itemREF 
			   LEFT JOIN items AS i ON itemREF.itemID = i.id  
			   left join items as itm on itemREF.itemID=itm.id 
			   LEFT JOIN price_range ON price_range.id = itm.price_rangeID
			   LEFT JOIN country ON country.id =  itm.countryID
			   where   itemREF.campaignID=$id 
			   ORDER BY ptype ASC, price_range.id ASC, voteTot DESC, countryName ASC";
	 
	 $report    = $this->db->query($sql);  
	 $rep       = $report->result_array(); 
	 
	 
	 $sql       = "select *,full_name from campaign as c inner join admin_users as u on c.adminCreatorID=u.id where c.id='$id'   ";
	 $header    = $this->db->query($sql);  
	 $header    = $header->result_array(); 
	 
	 $sql   		  = "SELECT *, voters.id as voterID FROM voters 
						LEFT JOIN country ON voters.Fields001 = country.id 
						where campaignID = $id $voter_AND ORDER BY country.id ASC"; 
	 $voters    	  = $this->db->query($sql);  
	 $data['voters']  = $voters->result_array();
	 
	 
	 $sql   		  = "SELECT * FROM iWantCampaignNumber_of_commitees_ref WHERE campaignID = $id"; 
	 $voters    	  = $this->db->query($sql);  
	 $data['iWant_Rules_No_Committes_Ref']  = $voters->result_array();
	 
	 $sql   		  = "SELECT * FROM iWantCanvassingRulesRef WHERE campaignID = $id"; 
	 $voters    	  = $this->db->query($sql);  
	 $data['iWantCanvassingRulesXref']  = $voters->result_array();
	 
	 $sql   		  = "SELECT * FROM iWantVotingRulesRef WHERE campaignID = $id"; 
	 $voters    	  = $this->db->query($sql);  
	 $data['iWantVotingRulesRef']  = $voters->result_array();
	 
	 $sql = "SELECT *, iWantVotingRulesRef.price_rangeID as pRangeID FROM iWantVotingRulesRef WHERE campaignID = $id 
			ORDER BY price_rangeID ASC, fieldID DESC, rel DESC, val ASC";
	$sql 			   = $this->db->query($sql);
	$iWantVotingRules = $sql->result_array();
	$campaignID = $id;
	foreach($iWantVotingRules as $iL)
	{
		extract($iL);
		$itemDB = "SELECT count(items.id) as tot_items
				   FROM items  
				   LEFT JOIN campaignItemsXref ON campaignItemsXref.itemID = items.id  
				   WHERE campaignItemsXref.campaignID = $campaignID
				   AND (items.$fieldName = $fieldID) AND items.price_rangeID =  $pRangeID";
		
		$orig_fieldName = $fieldName;
		switch($fieldName){
				case("POSMTypeID"):
					$tableName	= $CI2->fv->label(4);;
					$fieldName  = 'typeName';
					$table 		= 'POSM_Type';
				break;
				case("POSMStatusID"):
					$tableName	= 'ITEM STATUS';
					$fieldName  = 'statusName';
					$table 		= 'POSM_Status';
				break;
				case("OUTLETStatusID"):
					$tableName	= $CI2->fv->label(6); ;
					$fieldName  = 'statusName';
					$table 		= 'OUTLET_Status';
				break;
				case("PremiumTypeID"):
					$tableName	= $CI2->fv->label(7);;
					$fieldName  = 'premiumTypeName';
					$table 		= 'premiumItemType';
				break;
				case("MaterialTypeID"):
					$tableName	= $CI2->fv->label(9);;
					$fieldName  = 'materialName';
					$table 		= 'MATERIAL_Type';
				break;
				case("brandID"):
					$tableName	= $CI2->fv->label(3);;
					$fieldName  = 'brandName';
					$table 		= 'brands';
				break;
		}
		
		$query 		= $this->db->query("SELECT $fieldName FROM $table WHERE id=$fieldID LIMIT 0,1");
		$row 		= $query->row();
		$name_Field = $row->$fieldName;
		
		//GET PRICE RANGE NAME
		$query 		= $this->db->query("SELECT extra_label FROM price_range WHERE id=$price_rangeID LIMIT 0,1");
		$row 		= $query->row();
		$extra_label = $row->extra_label;
		
		$s 		   		= $this->db->query($itemDB);
		$data['items']  = $s->result_array();		
		//print_r($data['items']);
		$status="Good";
		
		//TEST EVERY RULE
		$tot_items = $data['items'][0]['tot_items'];
		$min_vote = $min_val;
		$max_vote = $max_val;
		if($cond1=="==") $cond1="=";
		if($cond2=="==") $cond2="=";
		if(strpos($min_val,".")==TRUE)
			$min_vote = round($min_val * $data['items'][0]['tot_items']);
		if(strpos($max_val,".")==TRUE)
			$max_vote = round($max_val * $data['items'][0]['tot_items']);

		
		//PERCENTAGE
		if(strpos($min_val,".")==TRUE)
		 $min_val  = "$min_val% (". round($min_val * $data['items'][0]['tot_items']) .")";
		if(strpos($max_val,".")==TRUE)
		 $max_val  = "$max_val% (". round($max_val * $data['items'][0]['tot_items']) .")";	
		
		
		$rules[] = array(
						'cond1'				=>$cond1,
						'min_val'			=>$min_val,
						'logical_operator'	=>$logical_operator,
						'cond2'				=>$cond2,
						'max_val'			=>$max_val,
						'price_rangeName'	=>$extra_label,
						'table'				=>$table,
						'fieldName'			=>$orig_fieldName, 
						'fieldID'			=>$fieldID, 
						'fieldValue'		=>$name_Field, 
						'current_num_items' =>$data['items'][0]['tot_items']
						);
	}
	$data['VotingRules'] 		   = $rules;
	
	$sql 				  = "SELECT *, iWantCanvassingRulesRef.price_rangeID as pRangeID FROM iWantCanvassingRulesRef WHERE campaignID = $campaignID 
							 ORDER BY price_rangeID ASC, fieldID DESC, rel DESC, val ASC";
	$sql 				  = $this->db->query($sql);
	$iWantCanvassingRules = $sql->result_array();	
	foreach($iWantCanvassingRules as $iL)
	{
		extract($iL);		
		$orig_fieldName = $fieldName;
		switch($fieldName){
				case("POSMTypeID"):
					$tableName	= $CI2->fv->label(4);;
					$fieldName  = 'typeName';
					$table 		= 'POSM_Type';
				break;
				case("POSMStatusID"):
					$tableName	= 'ITEM STATUS';
					$fieldName  = 'statusName';
					$table 		= 'POSM_Status';
				break;
				case("OUTLETStatusID"):
					$tableName	= $CI2->fv->label(6); ;
					$fieldName  = 'statusName';
					$table 		= 'OUTLET_Status';
				break;
				case("PremiumTypeID"):
					$tableName	= $CI2->fv->label(7);;
					$fieldName  = 'premiumTypeName';
					$table 		= 'premiumItemType';
				break;
				case("MaterialTypeID"):
					$tableName	= $CI2->fv->label(9);;
					$fieldName  = 'materialName';
					$table 		= 'MATERIAL_Type';
				break;
				case("brandID"):
					$tableName	= $CI2->fv->label(3);;
					$fieldName  = 'brandName';
					$table 		= 'brands';
				break;
		}
		
		$query 		= $this->db->query("SELECT $fieldName FROM $table WHERE id=$fieldID LIMIT 0,1");
		$row 		= $query->row();
		$name_Field = $row->$fieldName;
		
		//GET PRICE RANGE NAME
		$query 		= $this->db->query("SELECT extra_label FROM price_range WHERE id=$price_rangeID LIMIT 0,1");
		$row 		= $query->row();
		$extra_label = $row->extra_label;
		
		$status="";
		$original_val =$val;
		if(strpos($min_val,".")==TRUE) $min_val = $min_val."% (".round(count($data['voters'])*$min_val).")";
		if(strpos($max_val,".")==TRUE) $max_val = $max_val."% (".round(count($data['voters'])*$max_val).")";
		
		$rules2[] = array(
						'cond1'				=>$cond1,
						'min_val'			=>$min_val,
						'logical_operator'	=>$logical_operator,
						'cond2'				=>$cond2,
						'max_val'			=>$max_val,
						'price_rangeName'	=>$extra_label,
						'table'				=>$table,
						'fieldName'			=>$orig_fieldName, 
						'fieldID'			=>$fieldID, 
						'fieldValue'		=>$name_Field, 
						'rel'				=>$rel,
						'val'				=>"$original_val",
						'lrel'				=>$lrel,
						'stat'				=>$status
						);
	}
	$data['iWantCanvassingRulesXref']  = $rules2;
	 
     $data['vfile']			= 'iWantPartial.php';
	 $data['title']			= 'iLike Report';
	 $data['rep']			= $rep;
	 $data['repHeader']		= $header;
	 $data['breadCrumbs']	= '<a href='.HTTP_PATH.'iWantCampaign/iWant> iWant Campaign </a>';
	 $data['cID']			= $id;
	 
	if($this->modules->access_checker()==TRUE)
	{
		$this->load->view('innerPages',$data); 
	}else{
		$data['vfile']				= 'login.php';
		$data['title']				= 'SMBi System Log-in | SMBi';
		$data['page_title']			= 'SMBi System Log-in';
		$data['meta_description']	= 'San Miguel Brewing International';
		$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
		$this->load->view('login',$data); 	
	}

   } 
	
	function iLike($action='',$id='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(47,'REVIEW');
		$filter_WHERE 		= $this->modules->country();
		$filter_AND 		= $this->modules->country2();
		//$votingCampaignID 	= $id;
		
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(45);
		
		$table						= 'campaign';
		$data['vfile']				= 'iLike_report.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/iLike> iLike Report </a>';
		
		//TOTAL NUMBER OF ROWS			
		$data['active_page']=1;
		$sql       = $this->db->query("SELECT id FROM campaign WHERE campaignType='iLike' and status='done' $filter_AND");
		$sql       = $sql->result_array();
		$total_rec = count($sql);

		$pagenum = 1;
		$data['page_rows'] = 10; 
		$data['last'] = ceil($total_rec/$data['page_rows']);		
		$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];

	
		extract($_POST);
		if($action=="page")
		{
			$this->modules->module_checker(27,'REVIEW');
			$pagenum = $id;
			$data['active_page'] = $id; 
			$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		}

		$filter_WHERE = $this->modules->iLike_Campaign_country();
     	$sql = $this->db->query("SELECT *,campaign.id AS iLikeCampaignID FROM $table  
								 LEFT JOIN admin_users 
								 ON campaign.adminCreatorID = admin_users.id
								 WHERE campaignType='iLike' AND status='done' $filter_WHERE ORDER BY iLikeCampaignID  DESC $max");
								
		$data['campaigns'] = $sql->result_array();
		$viewer = 'innerPages';
		
	    
		if($this->modules->access_checker()==TRUE)
		{
			$this->load->view($viewer,$data); 
		}else{
			$data['vfile']				= 'login.php';
			$data['title']				= 'SMBi System Log-in | SMBi';
			$data['page_title']			= 'SMBi System Log-in';
			$data['meta_description']	= 'San Miguel Brewing International';
			$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
			$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
			$this->load->view('login',$data); 	
		}
	}
	
	function monthDiff($DateFrom,$DateTo)
	{
		$sql = "SELECT 
		  (TIMESTAMPDIFF(MONTH, '$DateFrom', '$DateTo') +
		  DATEDIFF(
			'$DateTo',
			'$DateFrom' + INTERVAL
			  TIMESTAMPDIFF(MONTH, '$DateFrom', '$DateTo')
			MONTH
		  ) /
		  DATEDIFF(
			'$DateFrom' + INTERVAL
			  TIMESTAMPDIFF(MONTH, '$DateFrom', '$DateTo') + 1
			MONTH,
			'$DateFrom' + INTERVAL
			  TIMESTAMPDIFF(MONTH, '$DateFrom', '$DateTo')
			MONTH
		  )) AS months";
		  
		  $query = $this->db->query($sql);
		  $row = $query->row();
		  return CEIL($row->months);
	}
	
	function perQuarterMonths($type='')
	{
		$month = date('m');
		$year  = date('Y');
		$date  = date('Y-m-d');
		
		$sql 	 = $this->db->query("SELECT ceiling((month('$date') / 3)) AS quarter");
		$sql 	 = $sql->row();
		$quarter = $sql->quarter; 
	
		switch($quarter)
		{
			case 1:
				$prevMonth = "$year-01-01";
				$date 	   = "$year-02-15";
			break;
			case 2:
				$prevMonth = "$year-04-01";
				$date 	   = "$year-05-15";
			break;
			case 3:
				$prevMonth = "$year-07-01";
				$date 	   = "$year-08-15";
			break;
			case 4:
				$prevMonth = "$year-10-01";
				$date 	   = "$year-11-15";
			break;
		}
		
		//GET THE LAST DAY OF THE MONTH
		$sql 	  = $this->db->query("SELECT LAST_DAY(DATE_ADD('$date', INTERVAL 1 MONTH)) AS lastOfNextMonth");
		$sql 	  = $sql->row();
		$lastOfNextMonth = $sql->lastOfNextMonth; 
		
		$str = "";
		if($type=='prevMonth')
			$str = "$prevMonth";
		if($type=='nextMonth')
			$str = "$lastOfNextMonth";
		if($type=='filter')
			$str = " (dUploaded BETWEEN '$prevMonth' AND '$lastOfNextMonth') ";
		
		return $str;
	}
	
	function setPreviousQTR($date='',$type='')
	{	
		$sql 	 = $this->db->query("SELECT ceiling((month('$date') / 3)) AS quarter, YEAR('$date') as tyear");
		$sql 	 = $sql->row();
		$quarter = $sql->quarter; 
		$tyear   = $sql->tyear; 
		$prevMonth ="";
		switch($quarter)
		{
			case 1:
				$prevMonth = "$tyear-01-01";
				$date 	   = "$tyear-03-15";
			break;
			case 2:
				$prevMonth = "$tyear-04-01";
				$date 	   = "$tyear-06-15";
			break;
			case 3:
				$prevMonth = "$tyear-07-01";
				$date 	   = "$tyear-09-15";
			break;
			case 4:
				$prevMonth = "$tyear-10-01";
				$date 	   = "$tyear-12-15";
			break;
		}
		
		//GET THE LAST DAY OF THE MONTH
		$sql 	  = $this->db->query("SELECT LAST_DAY('$date') AS lastOfNextMonth");
		$sql 	  = $sql->row();
		$lastOfNextMonth = $sql->lastOfNextMonth; 
		
		$str = "";
		if($type=='prevMonth')
			$str = "$prevMonth";
		if($type=='nextMonth')
			$str = "$lastOfNextMonth";
		
		return $str;
	}
	
	function SetPrevQuaterStr($date='')
	{
		$sql 	 = $this->db->query("SELECT ceiling((month('$date') / 3)) AS quarter");
		$sql 	 = $sql->row();
		$quarter = $sql->quarter; 
		
		$str="";
		switch($quarter)
		{
			case 1:
				$str="1st Quarter: January-March";
			break; 
			case 2:
				$str="2nd Quarter: April-June";
			break; 
			case 3:
				$str="3rd Quarter: July-September";
			break; 
			case 4:
				$str="4th Quarter: October-December";
			break; 
		}
		
		return $str;
	}
	
	function perQuarterMonths_Published($type='')
	{
		$month = date('m');
		$year  = date('Y');
		
		$sql 	 = $this->db->query("SELECT ceiling((month(CURDATE()) / 3)) AS quarter");
		$sql 	 = $sql->row();
		$quarter = $sql->quarter; 
	
		switch($quarter)
		{
			case 1:
				$prevMonth = "$year-01-01";
				$date 	   = "$year-02-15";
			break;
			case 2:
				$prevMonth = "$year-04-01";
				$date 	   = "$year-05-15";
			break;
			case 3:
				$prevMonth = "$year-07-01";
				$date 	   = "$year-08-15";
			break;
			case 4:
				$prevMonth = "$year-10-01";
				$date 	   = "$year-11-15";
			break;
		}
		
		//GET THE LAST DAY OF THE MONTH
		$sql 	  = $this->db->query("SELECT LAST_DAY(DATE_ADD('$date', INTERVAL 1 MONTH)) AS lastOfNextMonth");
		$sql 	  = $sql->row();
		$lastOfNextMonth = $sql->lastOfNextMonth; 
		
		$str = "";
		if($type=='prevMonth')
			$str = "$prevMonth";
		if($type=='nextMonth')
			$str = "$lastOfNextMonth";
		if($type=='filter')
			$str = " (dReleased BETWEEN '$prevMonth' AND '$lastOfNextMonth') ";
		
		return $str;
	}
	
	function minMaxDate($type='')
	{
		//GET CURRENT
		$curDate = date('Y-m-d');
		if($_SESSION['super_admin']!='y' AND $_SESSION['countryID']!=0){
			$sql = $this->db->query("SELECT min(dateAdded) AS minDate FROM items WHERE countryID =".$_SESSION['countryID'] ." LIMIT 0,1");
		}else{
			$sql = $this->db->query("SELECT min(dateAdded) AS minDate FROM items WHERE countryID != 0 LIMIT 0,1");
		}
		$sql	   = $sql->row();
		$prevMonth = $sql->minDate;
		
		if($_SESSION['super_admin']!='y' AND $_SESSION['countryID']!=0){
			$sql = $this->db->query("SELECT max(dateAdded) AS maxDate FROM items WHERE countryID =".$_SESSION['countryID'] ." LIMIT 0,1");
		}else{
			$sql = $this->db->query("SELECT max(dateAdded) AS maxDate FROM items WHERE countryID != 0 LIMIT 0,1");
		}
		$sql	   = $sql->row();
		$nextMonth = $sql->maxDate;
		
		$str = "";
		if($type=='prevMonth')
			$str = "$prevMonth";
		if($type=='nextMonth')
			$str = $nextMonth;
		if($type=='filter')
			$str = " (dUploaded BETWEEN '$prevMonth' AND '$nextMonth') ";
		if($type=='condition')
		    $str = "Report as of Today: $curDate";
		
		return $str;
	}
	
	//GROUP BY COUNTRY
	function BU_activeness_index()
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		
		$this->modules->module_checker(69,'REVIEW');
		
		//USER MANUAL
	    $data['USER_MANUAL'] = $this->modules->user_manual(43);
		
		$WHERE="";
		if($_SESSION['super_admin']!='y' AND $_SESSION['countryID']!=0){
			$WHERE 		= " WHERE cID = ".$_SESSION['countryID'] ." AND iID NOT IN (".$this->modules->generateItemsForArchive().") AND forPurging='n' AND";
		}else{
			$WHERE 		= " WHERE cID != 0 AND iID NOT IN (".$this->modules->generateItemsForArchive().") AND forPurging='n' AND";
		}
		
		extract($_POST);
		
		$data['quarterStr'] = "";
		$data['DateFrom'] 	= "null";
		$data['DateTo']   	= "null";
		$data['months']	  	= "";
		if((isset($DateFrom) AND isset($DateTo)) AND ($DateFrom!='' AND $DateTo!='') AND !isset($Reset)){
			$WHERE   .= " dUploaded >= '$DateFrom' AND dUploaded <= '$DateTo' ";
			$data['DateFrom'] = $DateFrom;
			$data['DateTo']   = $DateTo;
			$data['months']	  = $this->monthDiff($DateFrom,$DateTo);
			
			//DETECT QUARTER STRING
			if($DateFrom==$this->perQuarterMonths('prevMonth') & $DateTo==$this->perQuarterMonths('nextMonth'))
			 $data['quarterStr'] = $this->quaterStr('condition');
			//PREVIOUS QUARTER
			if($DateFrom==$this->setPreviousQTR($DateFrom,'prevMonth') & $DateTo==$this->setPreviousQTR($DateTo,'nextMonth'))
			 $data['quarterStr'] = $this->SetPrevQuaterStr($DateFrom);
			//PREVIOUS QUARTER
			if($DateFrom==$this->minMaxDate('prevMonth') & $DateTo==$this->minMaxDate('nextMonth'))
			 $data['quarterStr'] = $this->minMaxDate('condition');
		}else{
			$WHERE   		   .= $this->minMaxDate('filter');
			$data['quarterStr'] = $this->minMaxDate('condition');
			$data['months']	    = $this->monthDiff($this->minMaxDate('prevMonth'),$this->minMaxDate('nextMonth'));
			$data['DateFrom']   = $this->minMaxDate('prevMonth');
			$data['DateTo']     = $this->minMaxDate('nextMonth');
		}
		
		$HAVING="";
		$cond = "";
		if(isset($Submit) OR isset($selpage))
		{	
			$val1 = mysql_real_escape_string($val1);
			$val1 = trim($val1);
			$val2 = mysql_real_escape_string($val2);
			$val2 = trim($val2);
			
			$condition = '';
			switch($cond1){
				case 'equal': 
					$condition = '=';
				break;
				case 'containing': 
					$condition = 'like';
				break;
				case 'in': 
					$condition = 'in';
				break;
				case 'greaterThan': 
					$condition = '>=';
				break;
				case 'lessThan': 
					$condition = '<=';
				break;
				case 'between': 
					$condition = 'between';
				break;
			}
			
			
			if(($opt1=='pcountry') AND $val1!='')
			{
				if($condition=='=')
					$cond = " AND $opt1 $condition '$val1'";
				if($condition=='like')
					$cond = " AND $opt1 $condition '%$val1%'";
				if($condition=='in')
					$cond = " AND $opt1 $condition ('" . str_replace(",", "','", $val1) . "')";
			}
			
			//PUBLISH
			$field="";
			if($opt1=='Uploaded_Items')
				$field=" COUNT(itemID)";
			if($opt1=='Publish')
				$field=" SUM(publish='y' AND disapprove='n')";
			if($opt1=='Not_Yet_Publish')
				$field=" SUM(publish='n')";
			if($opt1=='Disapprove')
				$field=" SUM(disapprove='y')";
			if($opt1=='AVG_Local_Price')
				$field=" ROUND(AVG(UnitPrice),2)";
			if($opt1=='AVG_USD_Price')
				$field=" ROUND(AVG(USD_Price),2)";
			if(($opt1=='Uploaded_Items' OR $opt1=='Publish' OR $opt1=='Not_Yet_Publish' OR  $opt1=='Disapprove' OR  $opt1=='AVG_Local_Price' OR $opt1=='AVG_USD_Price') AND $val1!='' AND is_numeric($val1))
			{
				if($condition=='=')
					$HAVING = " HAVING $field $condition $val1 ";
				if($condition=='>=')
					$HAVING = " HAVING $field $condition $val1 ";
				if($condition=='<=')
					$HAVING = " HAVING $field $condition $val1 ";
			}
			
			if($condition=='between' & $this->checkStr($val1)==TRUE)
					$HAVING = " HAVING $field $condition ".stripslashes($val1);
			
			/*2ND SET*/
			switch($cond2){
				case 'equal': 
					$condition2 = '=';
				break;
				case 'containing': 
					$condition2 = 'like';
				break;
				case 'in': 
					$condition2 = 'in';
				break;
				case 'greaterThan': 
					$condition2 = '>=';
				break;
				case 'lessThan': 
					$condition2 = '<=';
				break;
				case 'between': 
					$condition2 = 'between';
				break;
			}
			
			if(($opt2=='pcountry') AND $val2!='')
			{
				if($condition2=='=')
					$cond .= " $operator $opt2 $condition2 '$val2' ";
				if($condition2=='like')
					$cond .= " $operator $opt2 $condition2 '%$val2%' ";
				if($condition2=='in')
					$cond .= " $operator $opt2 $condition2 ('" . str_replace(",", "','", $val2) . "') ";
			}
			
			
			$field="";
			if($opt2=='Uploaded_Items')
				$field=" COUNT(itemID) ";
			if($opt2=='Publish')
				$field=" SUM(publish='y' AND disapprove='n') ";
			if($opt2=='Not_Yet_Publish')
				$field=" SUM(publish='n') ";
			if($opt2=='Disapprove')
				$field=" SUM(disapprove='y') ";
			if($opt2=='AVG_Local_Price')
				$field=" ROUND(AVG(UnitPrice),2) ";
			if($opt2=='AVG_USD_Price')
				$field=" ROUND(AVG(USD_Price),2) ";
			if(($opt2=='Uploaded_Items' OR $opt2=='Publish' OR $opt2=='Not_Yet_Publish' OR  $opt2=='Disapprove' OR  $opt2=='AVG_Local_Price' OR $opt2=='AVG_USD_Price') AND $val2!='' AND (is_numeric($val2) OR $this->checkStr($val2)==TRUE))
			{	
				$HAVING .= ($HAVING=="") ? "HAVING" : "$operator";
				if($condition2=='=')
					$HAVING .= "  $field $condition2 $val2";
				if($condition2=='>=')
					$HAVING .= "  $field $condition2 $val2";
				if($condition2=='<=')
					$HAVING .= "  $field $condition2 $val2";
			}
			
			if($condition2=='between' & $this->checkStr($val2)==TRUE)
					$HAVING .= " $field $condition2 ".stripslashes($val2);
			
			$cond = ($cond=="") ? "" : " $cond ";
			$WHERE  .=  " $cond";
		}
		
		$data['POST'] = $_POST;
		if(isset($Reset)){
			$data['POST'] = array();
			$data['quarterStr'] = $this->minMaxDate('condition');
			$data['DateFrom'] = $this->minMaxDate('prevMonth');
			$data['DateTo']   = $this->minMaxDate('nextMonth');
		}
		
	
		$table						= 'BU_activeness_index';
		$data['vfile']				= 'BU_activeness_index.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/BU_activeness_index> Activeness of Business Units Index </a>';
		
		//TOTAL NUMBER OF ROWS					
		$data['per_country'] = TRUE;
		
		$sql   = "SELECT cID, pcountry, COUNT(itemID) AS num_items,  AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice, 
				  SUM(publish='y') AS publish_items, SUM(publish='n' AND disapprove='n') AS not_publish, SUM(disapprove='y') as disapprove_items, target
				  FROM item_db_reports 
				  LEFT JOIN target_items ON target_items.countryID = item_db_reports.cID
				  $WHERE GROUP BY cID $HAVING";
		
		$sql_csv = "SELECT pcountry as Country, COUNT(itemID) AS Items_Uploaded, SUM(publish='n' AND disapprove='n') AS For_Approval,  
					SUM(disapprove='y') as Disapproved_Items,  SUM(publish='y') AS Publish_Items, (target* ".$data['months'].") as Target_Items,
					AVG(UnitPrice) AS AVG_Local_Price, AVG(USD_Price) AS AVG_US_Price
					FROM item_db_reports 
					LEFT JOIN target_items ON target_items.countryID = item_db_reports.cID
					$WHERE GROUP BY cID $HAVING";
		
		//generate csv file
		$this->generateCSVFile('item_views',$sql_csv,"SMBi_BU_activeness_Index".$this->reportCode().".csv");
		$data['csvFile'] = "SMBi_BU_activeness_Index".$this->reportCode().".csv";
		
		$sql   = $this->db->query($sql);
		$data['reports']   = $sql->result_array();
		

		$viewer = 'innerPages';	    
		if($this->modules->access_checker()==TRUE)
		{
			$this->load->view($viewer,$data); 
		}else{
			$data['vfile']				= 'login.php';
			$data['title']				= 'SMBi System Log-in | SMBi';
			$data['page_title']			= 'SMBi System Log-in';
			$data['meta_description']	= 'San Miguel Brewing International';
			$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
			$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
			$this->load->view('login',$data); 	
		}
	}
	
	function BU_activeness_details($cID='',$DateFrom='',$DateTo='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(69,'REVIEW');
		
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(43);
		
		$filter_WHERE= "";
		$csv  		 = "";
		if($_SESSION['super_admin']!='y'  AND $_SESSION['countryID']!=0)
			$filter_WHERE = "WHERE cID =".$_SESSION['countryID']." ";
		
		//$votingCampaignID 	= $id;
		
		$table						= 'BU_activeness_details';
		$data['vfile']				= 'BU_activeness_details.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/BU_activeness_index> BU Activeness Index </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/BU_activeness_details/$cID/$DateFrom/$DateTo'> BU Activeness Index in Details </a>";
		
		//COUNTRY
		$data['country_name'] = "Country: All Country";
		if($cID!=0){
			$sql = $this->db->query("SELECT countryName FROM country WHERE id = $cID LIMIT 0,1");
			$row = $sql->row();
			$data['country_name'] = "Country: ".$row->countryName;
		}
		
		//TOTAL NUMBER OF ROWS		
		$sort='n';
		extract($_POST);
		$data['cID']= $cID;
		$cond="";
		$having="";
		$limit =isset($selpage)? $selpage:0;
		
		$WHERE  = " WHERE cID!=0 AND forArchiving='n' AND forPurging='n' AND (itemAge <".$this->modules->itemsAge().") AND";
		if($cID!=0)
			$WHERE  = " WHERE cID=$cID AND cID!=0 AND forArchiving='n' AND forPurging='n' AND (itemAge <".$this->modules->itemsAge().") AND";
			
		$data['quarterStr'] = "";
		$data['DateFrom'] = "";
		$data['DateTo'] = "";
		if((isset($DateFrom) AND isset($DateTo)) AND ($DateFrom!='null' AND $DateTo!='null') AND !isset($Reset)){
			$WHERE   .= " (dUploaded >= '$DateFrom' AND dUploaded <= '$DateTo') ";
			$data['DateFrom'] = $DateFrom;
			$data['DateTo']   = $DateTo;
			//DETECT QUARTER STRING
			if($DateFrom==$this->perQuarterMonths('prevMonth') & $DateTo==$this->perQuarterMonths('nextMonth'))
			 $data['quarterStr'] = $this->quaterStr('condition');
			//PREVIOUS QUARTER
			if($DateFrom==$this->setPreviousQTR($DateFrom,'prevMonth') & $DateTo==$this->setPreviousQTR($DateTo,'nextMonth'))
			 $data['quarterStr'] = $this->SetPrevQuaterStr($DateFrom);
			//PREVIOUS QUARTER
			if($DateFrom==$this->minMaxDate('prevMonth') & $DateTo==$this->minMaxDate('nextMonth'))
			 $data['quarterStr'] = $this->minMaxDate('condition');
		}elseif(!$_POST){
			$WHERE   		   .= $this->minMaxDate('filter');
			$data['quarterStr'] = $this->minMaxDate('condition');
			$data['DateFrom']   = $this->minMaxDate('prevMonth');
			$data['DateTo']     = $this->minMaxDate('nextMonth');
		}
		
		if($_POST AND $DateFrom=='' AND $DateTo=='') $WHERE = substr($WHERE,0,-3);
		
		if(isset($Submit) OR isset($selpage) AND !isset($Reset))
		{	
			$val1 = mysql_real_escape_string($val1);
			$val1 = trim($val1);
			$val2 = mysql_real_escape_string($val2);
			$val2 = trim($val2);
			
			$condition = '';
			switch($cond1){
				case 'equal': 
					$condition = '=';
				break;
				case 'containing': 
					$condition = 'like';
				break;
				case 'in': 
					$condition = 'in';
				break;
				case 'greaterThan': 
					$condition = '>=';
				break;
				case 'lessThan': 
					$condition = '<=';
				break;
				case 'between': 
					$condition = 'between';
				break;
			}
			
			
			if(($opt1=='itemCode' OR $opt1=='itemName' OR $opt1=='pstatus' OR $opt1=='ptype' OR $opt1=='poutlet_status' OR $opt1=='ppremium_type' OR $opt1=='pmaterial' OR $opt1=='pbrand' OR $opt1=='full_name' OR $opt1=='pcountry' OR $opt1=='price_rangeName') AND $val1!='')
			{
				if($condition=='=')
					$cond = "  $opt1 $condition '$val1'";
				if($condition=='>=')
					$cond = "  $opt1 $condition '$val1'";
				if($condition=='<=')
					$cond = "  $opt1 $condition '$val1'";
				if($condition=='like')
					$cond = "  $opt1 $condition '%$val1%'";
				if($condition=='in')
					$cond = "  $opt1 $condition ('" . str_replace(",", "','", $val1) . "')";
				if($condition=='between' & $this->checkStr($val1)==TRUE)
					$cond = "  $opt1 $condition ".stripslashes($val1);
			}
			
			//PUBLISH
			if(($opt1=='publish' OR $opt1=='disapprove') AND $val1!='')
			{
				if($condition=='=')
					$val1 = ($val1=='yes' OR $val1=='Yes') ? 'y' : 'n';
					$cond = "  $opt1 $condition '$val1'";
				if($condition=='like')
					$cond = "  $opt1 $condition '%$val1%'";
			}
			
			
			//VIEWS
			if(($opt1=='num_views' OR $opt1=='UnitPrice' OR $opt1=='likes' OR $opt1=='wants' OR $opt1=='USD_Price') AND $val1!='' AND $condition!='like'){ 
				if($condition=='in' AND (is_numeric($val1) OR strpos($val1,',')==TRUE))
					$cond = "  $opt1 $condition ('" . str_replace(",", "','", $val1) . "')";
				elseif($condition!='in' AND is_numeric($val1))
					$cond = "  $opt1 $condition $val1";
				elseif($condition=='between' & $this->checkStr($val1)==TRUE)
					$cond = "  $opt1 $condition ".stripslashes($val1);
			}
		
			//dateReleased
			if($opt1=='dateAdded' AND $val1!='' AND $condition!='like' AND $condition!='between') 
				$cond = "  dateAdded $condition '$val1'";
			if($opt1=='dateAdded' AND $val1!='' AND $condition=='between' & $this->checkStr($val1)==TRUE) 
				$cond = "  dateAdded $condition ".stripslashes($val1);
			
			/*2ND SET*/
			switch($cond2){
				case 'equal': 
					$condition2 = '=';
				break;
				case 'containing': 
					$condition2 = 'like';
				break;
				case 'in': 
					$condition2 = 'in';
				break;
				case 'greaterThan': 
					$condition2 = '>=';
				break;
				case 'lessThan': 
					$condition2 = '<=';
				break;
				case 'between': 
					$condition2 = 'between';
				break;
			}
			
			//CHECK IF VAL1 IS SET
			if($val1=="")  $condition2 ="";
			
			if(($opt2=='itemCode' OR $opt2=='itemName' OR $opt2=='pstatus' OR $opt2=='ptype' OR $opt2=='poutlet_status' OR $opt2=='ppremium_type' OR $opt2=='pmaterial' OR $opt2=='pbrand' OR $opt2=='full_name' OR $opt2=='pcountry' OR $opt2=='price_rangeName') AND $val2!='')
			{
				if($condition2=='=')
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator $opt2 $condition2 '%$val2%'";
				if($condition2=='>=')
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='<=')
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='in')
					$cond .= " $operator $opt2 $condition2 ('" . str_replace(",", "','", $val2) . "')";
				if($condition2=='between'  & $this->checkStr($val2)==TRUE)
					$cond .= "  $operator $opt2 $condition2 ".stripslashes($val2);
			}
			
			
			//PUBLISH
			if(($opt2=='publish' OR $opt2=='disapprove') AND $val2!='')
			{
				if($condition2=='=')
					$val2 = ($val2=='yes' OR $val2=='Yes') ? 'y' : 'n';
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator $opt2 $condition2 '%$val2%'";
			}
			
			//LOCAL PRICE
			//VIEWS
			if(($opt2=='num_views' OR $opt2=='UnitPrice' OR $opt2=='likes' OR $opt2=='wants' OR $opt2=='USD_Price') AND $val2!='' AND $condition2!='like'){ 
				if($condition2=='in' AND (is_numeric($val2) OR strpos($val2,',')==TRUE))
					$cond .= " $operator $opt2 $condition2 ('" . str_replace(",", "','", $val2) . "')";
				elseif($condition2!='in' AND is_numeric($val2))
					$cond .= " $operator $opt2 $condition2 $val2";
				elseif($condition2=='between' & $this->checkStr($val2)==TRUE)
					$cond .= "  $operator $opt2 $condition2 ".stripslashes($val2);
			}
	
				
			//dUploaded
			if($opt2=='dateAdded' AND $val2!='' AND $condition2!='like' AND $condition2!='between') 
				$cond .= " $operator dateAdded $condition2 '$val2'";
			if($opt2=='dateAdded' AND $val2!='' AND $condition2=='between' & $this->checkStr($val2)==TRUE) 
				$cond .= "  $operator dateAdded $condition2 ".stripslashes($val2);
				
			$cond = ($cond=="") ? "" : " AND ($cond) ";
			$WHERE  .=  " $cond ";
		}
		
		
		//IF THEIR NO POSSIBLE RESULT
		$valid_query=TRUE;
		if(isset($Submit)){
			if($cond=="" AND ($val1!='' OR $val2!='')){ 
			$valid_query=FALSE;
			$WHERE = "WHERE itemID=0";
			}
		}
		
		$data['POST']      = $_POST;
		if(isset($Reset)){
			$WHERE = substr($WHERE,0,-3);
			$data['POST'] = array();
			$data['quarterStr'] = $this->minMaxDate('condition');
			$data['DateFrom'] = $this->minMaxDate('prevMonth');
			$data['DateTo']   = $this->minMaxDate('nextMonth');
		}
		
		//ORDER
		$ORDER 			= $this->sortingRef('0-D','query');
		$data['order']  = $this->sortingRef('0-D','Orig_code');
		$order_code 	= '0-D';
		$label 			= "Views";
		if(isset($order)){
			$ORDER = $this->sortingRef($order,'query');
			$data['order'] = $this->sortingRef($order,'Orig_code');
			$order_code = $order;
			$label = $this->sortingRef($order,'label');
		}	
		
		$sql = "SELECT itemID, num_views, likes, wants, itemCode, item_image, itemName, pstatus, ptype, poutlet_status, ppremium_type, pmaterial, pbrand, full_name, cName, publish,disapprove, UnitPrice, USD_Price,price_rangeName, dUploaded, dReleased
				FROM item_db_reports		 
				$WHERE ORDER BY $ORDER";
				
		$sql_csv = $this->db->query("SELECT cName as Country, num_views as Views, likes as Likes, wants as Wants, itemCode as Item_Code, itemName as Item_Name, 
									pstatus as Status, ptype as Type, poutlet_status as Outlet_Status, 
									ppremium_type as Premium_Type, pmaterial as Material_Type, pbrand as Brand, full_name as User, publish as Publish,
									disapprove as Disapprove, UnitPrice, USD_Price, price_rangeName as Price_Category, dUploaded as Date_Uploaded, dReleased as Date_Released
									FROM item_db_reports		 
									$WHERE ORDER BY $ORDER");
		$all_items = "";
		
		//generate csv file
		$csv  = "Activeness of Business Unit in Details\n";
		$csv .= "No, Country, Views, Likes, Wants, Item Code,  Item Name, Status, Type, Outlet Status, Premium Type, Material Type, Brand, User, Publish, Disapprove, UnitPrice, USD Price, Price Category, Date Uploaded, Date Released\n";
		$sql_csv = $sql_csv->result_array(); $x=0;
		foreach($sql_csv as $s)
		{ extract($s); $x++;
		  $Publish 	  	 = ($Publish=='y')    ? "Yes" : "No";
		  $Disapprove 	 = ($Disapprove=='y') ? "Yes" : "No";
		  $Date_Uploaded =  $this->convertDate('date',"$Date_Uploaded 00:00:00");
		  $Date_Released =  $this->convertDate('date',"$Date_Released 00:00:00");
		  $Item_Name     =  str_replace(",","-",$Item_Name);
		  $csv .= "$x, $Country, $Views, $Likes, $Wants, $Item_Code, $Item_Name, $Status, $Type, $Outlet_Status, $Premium_Type, $Material_Type, $Brand, $User, $Publish, $Disapprove, $UnitPrice, $USD_Price, $Price_Category, $Date_Uploaded, $Date_Released\n";
		}
	    write_file(getcwd()."/files/csv/BU_activeness_details".$this->reportCode().".csv",$csv);
		$data['csvFile']			= "BU_activeness_details".$this->reportCode().".csv";
		 
		$all_items = $this->db->query($sql);
		$all_items = $all_items->result_array();
		//SORT STATUS
		$limit = ($sort)=='y' ? 0 : $limit;
		
		$limit_items = $this->db->query($sql." LIMIT $limit,20");
		$limit_items = $limit_items->result_array();
		$data['totrec'] = count($all_items);
		$data['limit']  = $limit;
		
		$items	 = $limit_items;
		
		$table= "<table id='large2' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:11px;' class='iLike_Result_Table2'>
				<tr style='height: 40px;'>
					<th style='width:25px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'>	   No.  	  		  				  					  </th> 
					<th style='width:96px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"12-A\")'>          <b>Country  	  	  </b></th> 
					<th style='width:83px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"0-A\")'>           <b>Views  	   	  </b></th> 
					<th style='width:83px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"1-D\")'>           <b>Likes  	  	  </b></th> 
					<th style='width:83px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"2-D\")'>           <b>Wants  	  	  </b></th> 
					<th style='width:191px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"3-A\")'>           <b>Item Code  	  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'>  <b>Image   	 	     							  	  </b></th> 
					<th style='width:195px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"4-A\")'>            <b>Item Name  	  </b></th> 
					<th style='width:120px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"5-A\")'>           <b>Status  	  	  </b></th> 
					<th style='width:120px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"6-A\")'>           <b>Type  	  		  </b></th> 
					<th style='width:153px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"7-D\")'>   	       <b>Outlet  	  	  </b></th> 
					<th style='width:125px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"8-D\")'>    		   <b>Premium  	  	  </b></th> 
					<th style='width:114px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"9-A\")'>           <b>Material  	  	  </b></th> 
					<th style='width:110px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"10-A\")'>          <b>Brand  	  	  </b></th> 
					<th style='width:120px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"11-A\")'>           <b>User  	  		  </b></th> 
					<th style='width:73px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"13-A\")'>           <b>Publish  	  	  </b></th>
					<th style='width:73px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"21-D\")'>           <b>Disapprove  	  	  </b></th>
					<th style='width:100px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"14-A\")'>          <b>L. Price  	  	  </b></th> 
					<th style='width:116px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"15-A\")'>          <b>US. Price  	  </b></th> 
					<th style='width:60px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"20-A\")'>          <b>Price Category   </b></th> 
					<th style='width:100px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"16-A\")'>          <b>Uploaded  	  	  </b></th> 
					<th style='width:100px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"17-A\")'>          <b>Released  	  	  </b></th> 
				</tr>";
		
				//REPLACE ORDER
				$table = str_replace($order_code,$this->sortingRef($order_code,'Rev_code'),$table);
				$table = str_replace($label,$this->sortingRef($order_code,'label_symbol'),$table);
				 
					$x = $limit;			
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					$orig_itemName="";
					if($valid_query==TRUE)
					{
					 foreach($items as $r) { 
					 extract($r);
					 $ptype = ($ptype=='') 	   ? 'Uncategorize' : $ptype;
					 $publish = ($publish=='y') ? 'Yes' : 'No';
					 $disapprove = ($disapprove=='y') ? 'Yes' : 'No';
					 $c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
					 $orig_itemName=$itemName;
					 if(strlen($itemName)>=15)
							$itemName = substr($itemName,0,15)."...";
				
					 $likes 			= ($likes=="") 		? 0 : $likes;
					 $wants 		    = ($wants=="") 		? 0 : $wants;
		$table.= "<tr>
				  <td $c>													$x </td>
				  <td $c style='text-align:left;padding-left:5px;'>		$cName																			</td>
				  <td $c>													<a onclick=\"viewDialog('item_database',$itemID)\" style='cursor:pointer;'><b>$num_views</b> </a> </td>
				  <td $c>													<a onclick=\"showVoters('iLike',$itemID)\" style='cursor:pointer;'><b>$likes</b></a>      																</td>
				  <td $c>													<a onclick=\"showVoters('iWant',$itemID)\" style='cursor:pointer;'><b>$wants</b></a>      																</td>
				  <td $c>													$itemCode      																	</td>
				  <td $c style='text-align:center;'>			    		<img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 	</td>
				  <td $c style='text-align:left;padding-left:5px;' title='$orig_itemName'>		<a href='".HTTP_PATH."gallery/itemInfo2/$itemID' target='_blank'>		$itemName</a>  </td>
				  <td $c style='text-align:left;padding-left:5px;'>		$pstatus																		</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$ptype																			</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$poutlet_status																	</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$ppremium_type																	</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$pmaterial																	    </td>
				  <td $c style='text-align:left;padding-left:5px;'>		$pbrand																	    	</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$full_name																		</td>
				  <td $c style='text-align:center;'>						$publish																		</td>
				  <td $c style='text-align:center;'>						$disapprove																		</td>
				  <td $c style='text-align:center;'>						$UnitPrice																		</td>
				  <td $c style='text-align:center;'>						$USD_Price																		</td>
				    <td $c style='text-align:center;'>						$price_rangeName																</td>
				  <td $c style='text-align:center;'>				        ". $this->convertDate('date',"$dUploaded 00:00:00") ."										</td>
				  <td $c style='text-align:center;'>				        ". $this->convertDate('date',"$dReleased 00:00:00") ."										</td>
				</tr>";}
				   }
		$table.= "</tbody>";
					if(!$items OR $valid_query==FALSE)
						$table.=  "<tr><td colspan='21'>Sorry no items found, check your search parameters.</td></tr>";
		$table.= "</table>";	
		$data['table'] 		= $table;
	
		
		$viewer = 'innerPages';	    
		if($this->modules->access_checker()==TRUE)
		{
			$this->load->view($viewer,$data); 
		}else{
			$data['vfile']				= 'login.php';
			$data['title']				= 'SMBi System Log-in | SMBi';
			$data['page_title']			= 'SMBi System Log-in';
			$data['meta_description']	= 'San Miguel Brewing International';
			$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
			$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
			$this->load->view('login',$data); 	
		}
	}
	
	function item_summary_eCatalogue_details($ecID='',$fldName='',$fldVal='',$DateFrom='',$DateTo='')
	{	
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(73,'REVIEW');
		
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(43);
		
		$table						= 'item_summary_eCatalogue_details';
		$data['vfile']				= 'item_summary_eCatalogue_details.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/item_summary_eCatalogue> eCatalogue Item Summary </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/item_summary_eCatalogue_details/$ecID/$fldName/$fldVal/$DateFrom/$DateTo'> eCatalogue Item Summary In Details </a>";
		
		//TOTAL NUMBER OF ROWS
		$data['ecID'] 	 = $ecID;
		$data['fieldName'] = $fldName;
		$data['fieldVal']  = $fldVal;
		$sort='n';
		extract($_POST);
		//COUNTRY
		if($ecID!=0){
			$sql = $this->db->query("SELECT title FROM e_catalog WHERE id = $ecID LIMIT 0,1");
			$row = $sql->row();
			$data['eCatalogue_title'] = "eCatalogue: ".$row->title;
		}	
		//FIELD SWITCHER
		$fValue=($fldVal==0) ? "Uncategorized" : $this->fieldSwitcher('fldValue',$fldName,$fldVal);
		$data['fldName'] = "(<i>".$this->fieldSwitcher('fldName',$fldName)." $fValue</i>)";		
		
		//COUNTRY ID
		$WHERE="WHERE ecID = '$ecID' AND $fldName = '$fldVal' ";
	
		$cond="";
		$sort='n';
		extract($_POST);
		$data['DateFrom'] = "";
		$data['DateTo'] = "";
		$data['quarterStr'] = "";
		if((isset($DateFrom) AND isset($DateTo)) AND ($DateFrom!='' AND $DateTo!='') AND !isset($Reset)){
		$WHERE   .= "AND dReleased >= '$DateFrom' AND dReleased <= '$DateTo' ";
		$data['DateFrom'] = $DateFrom;
		$data['DateTo']   = $DateTo;

		//DETECT QUARTER STRING
		if($DateFrom==$this->perQuarterMonths('prevMonth') & $DateTo==$this->perQuarterMonths('nextMonth'))
		 $data['quarterStr'] = $this->quaterStr('condition');
		//PREVIOUS QUARTER
		if($DateFrom==$this->setPreviousQTR($DateFrom,'prevMonth') & $DateTo==$this->setPreviousQTR($DateTo,'nextMonth'))
		 $data['quarterStr'] = $this->SetPrevQuaterStr($DateFrom);
		//PREVIOUS QUARTER
		if($DateFrom==$this->minMaxDatePublishedEC('prevMonth') & $DateTo==$this->minMaxDatePublishedEC('nextMonth'))
		 $data['quarterStr'] = $this->minMaxDatePublishedEC('condition');
		}else{
		$WHERE   		   .= "AND ".$this->minMaxDatePublishedEC('filter');
		$data['quarterStr'] = $this->minMaxDatePublishedEC('condition');

		$data['DateFrom']   = $this->minMaxDatePublishedEC('prevMonth');
		$data['DateTo']     = $this->minMaxDatePublishedEC('nextMonth');
		}
		
		
		$having="";
		if(isset($Submit) OR isset($selpage))
		{	
			$val1 = mysql_real_escape_string($val1);
			$val1 = trim($val1);
			$val2 = mysql_real_escape_string($val2);
			$val2 = trim($val2);
			
			$condition = '';
			switch($cond1){
				case 'equal': 
					$condition = '=';
				break;
				case 'containing': 
					$condition = 'like';
				break;
				case 'in': 
					$condition = 'in';
				break;
				case 'greaterThan': 
					$condition = '>=';
				break;
				case 'lessThan': 
					$condition = '<=';
				break;
				case 'between': 
					$condition = 'between';
				break;
			}
			
			//VIEWS
			if(($opt1=='num_views' OR $opt1=='UnitPrice' OR $opt1=='USD_Price') AND $val1!='' AND $condition!='like'){ 
				if($condition=='in' AND (is_numeric($val1) OR strpos($val1,',')==TRUE))
					$cond .= "  $opt1 $condition ('" . str_replace(",", "','", $val1) . "')";
				elseif($condition!='in' AND is_numeric($val1))
					$cond .= "  $opt1 $condition $val1";
				elseif($condition=='between' & $this->checkStr($val1)==TRUE)
					$cond = "  $opt1 $condition ".stripslashes($val1);
			}
			
			if(($opt1=='itemCode' OR $opt1=='itemName' OR $opt1=='ptype' OR $opt1=='poutlet_status' OR $opt1=='ppremium_type' OR $opt1=='pmaterial' OR $opt1=='full_name') AND $val1!='')
			{
				if($condition=='=')
					$cond .= "  $opt1 $condition '$val1'";
				if($condition=='>=')
					$cond .= "  $opt1 $condition '$val1'";
				if($condition=='<=')
					$cond .= "  $opt1 $condition '$val1'";
				if($condition=='like')
					$cond .= "  $opt1 $condition '%$val1%'";
				if($condition=='in')
					$cond .= "  $opt1 $condition ('" . str_replace(",", "','", $val1) . "')";
			}
			
			//PUBLISH
			if(($opt1=='publish') AND $val1!='')
			{
				if($condition=='=')
					$val1 = ($val1=='yes' OR $val1=='Yes') ? 'y' : 'n';
					$cond .= "  $opt1 $condition '$val1'";
				if($condition=='like')
					$cond .= "  $opt1 $condition '%$val1%'";
			}
			
			//dateReleased
			if($opt1=='dateReleased' AND $val1!='' AND $condition!='like') 
				$cond .= "  dReleased $condition '$val1'";
			
			/*2ND SET*/
			switch($cond2){
				case 'equal': 
					$condition2 = '=';
				break;
				case 'containing': 
					$condition2 = 'like';
				break;
				case 'in': 
					$condition2 = 'in';
				break;
				case 'greaterThan': 
					$condition2 = '>=';
				break;
				case 'lessThan': 
					$condition2 = '<=';
				break;
				case 'between': 
					$condition2 = 'between';
				break;
			}
			
			
			//VIEWS
			if(($opt2=='num_views' OR $opt2=='UnitPrice' OR $opt2=='USD_Price') AND $val2!='' AND $condition2!='like'){ 
				if($condition2=='in' AND (is_numeric($val2) OR strpos($val2,',')==TRUE))
					$cond .= " $operator $opt2 $condition2 ('" . str_replace(",", "','", $val2) . "')";
				elseif($condition2!='in' AND is_numeric($val2))
					$cond .= " $operator $opt2 $condition2 $val2";
				elseif($condition2=='between'  & $this->checkStr($val2)==TRUE)
					$cond .= "  $operator $opt2 $condition2 ".stripslashes($val2);
			}
			
			if(($opt2=='itemCode' OR $opt2=='itemName' OR $opt2=='ptype' OR $opt2=='poutlet_status' OR $opt2=='ppremium_type' OR $opt2=='pmaterial' OR $opt2=='full_name') AND $val2!='')
			{
				if($condition2=='=')
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='>=')
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='<=')
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator $opt2 $condition2 '%$val2%'";
				if($condition2=='in')
					$cond .= " $operator $opt2 $condition2 ('" . str_replace(",", "','", $val2) . "')";
			}
			
			//PUBLISH
			if(($opt2=='publish') AND $val2!='')
			{
				if($condition2=='=')
					$val2 = ($val2=='yes' OR $val2=='Yes') ? 'y' : 'n';
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator $opt2 $condition2 '%$val2%'";
			}
			
			//dateReleased
			if($opt2=='dateReleased' AND $val2!='' AND $condition2!='like') 
				$cond .= " $operator dReleased $condition2 '$val2'";
			
			$cond = ($cond=="") ? "" : "AND ($cond) ";
			$WHERE  .=  " $cond ";
		}
		
		$data['POST'] = $_POST;
		$limit =isset($selpage)? $selpage:0;
		if(isset($Reset)){
			$data['POST'] = array();
			$cond="WHERE ecID = '$ecID' AND $fldName = '$fldVal' ";
			$limit=0;
		}
		
		//ORDER
		$ORDER = $this->sortingRef_eCat('0-D','query');
		$data['order'] = $this->sortingRef_eCat('0-D','Orig_code');
		$order_code = '0-D';
		$label = "Views";
		if(isset($order)){
			$ORDER = $this->sortingRef_eCat($order,'query');
			$data['order'] = $this->sortingRef_eCat($order,'Orig_code');
			$order_code = $order;
			$label = $this->sortingRef_eCat($order,'label');
		}	
		
		$sql = "SELECT iID as itemID, ecID, itemName, ptype, poutlet_status, ppremium_type, pmaterial,
				itemCode, num_views, 
				item_image, dUploaded, full_name, publish,  UnitPrice, USD_Price, dReleased, ecName as Brand
				FROM ec_item_reports 
				$WHERE ORDER BY $ORDER";
		
		$sql_csv = $this->db->query("SELECT num_views as Views, itemCode as Item_Code, itemName as Item_Name, ptype as Item_Type, poutlet_status as Outlet_Status, ppremium_type as Premium_Type, pmaterial as Material_Type,
									 full_name as User, publish as Publish, UnitPrice as Unit_Price, USD_Price as USD_Price, dUploaded as Date_Uploaded, dReleased as Date_Released , ecName as Brand 
									 FROM ec_item_reports 
									 $WHERE ORDER BY $ORDER");
		//SORT STATUS
		//generate csv file
		$csv  = "eCatalogue Report in Details\n";
		$csv .= "No, Views, Item Code,  Item Name, Type, Outlet Status, Premium Type, Material Type, Brand, User, Publish, Local Price, USD Price, Date Uploaded, Date Released\n";
		$sql_csv = $sql_csv->result_array(); $x=0;
		foreach($sql_csv as $s)
		{ extract($s); $x++;
		  $Publish 	  	 = ($Publish=='y')    ? "Yes" : "No";
		  $Date_Uploaded =  $this->convertDate('date',"$Date_Uploaded 00:00:00");
		  $Date_Released =  $this->convertDate('date',"$Date_Released 00:00:00");
		  $Item_Name     =  str_replace(",","-",$Item_Name);
		  $csv .= "$x, $Views, $Item_Code, $Item_Name, $Item_Type, $Outlet_Status, $Premium_Type, $Material_Type, $Brand, $User, $Publish, $Unit_Price, $USD_Price,  $Date_Uploaded, $Date_Released\n";
		}
	    write_file(getcwd()."/files/csv/eCatalogue_Item_Summary_in_Details".$this->reportCode().".csv",$csv);
		$data['csvFile'] = "eCatalogue_Item_Summary_in_Details".$this->reportCode().".csv";
		
		$limit = ($sort)=='y' ? 0 : $limit;
		if(isset($Submit)) $limit=0;
		//echo $sql;
	
		$ctr = $this->db->query($sql);
		$ctr = $ctr->result_array();
		$data['totrec'] = count($ctr);
		$data['limit']  = $limit;
		
		$sql 	 = $this->db->query($sql." LIMIT $limit,20");
		$items	 = $sql->result_array();
	
	
		$table= "<table id='large2' cellpadding='0' cellspacing='0' border=1 style='width:120%;font-size:12px;' class='iLike_Result_Table2'>
				<tr>
					<th style='width:10px;text-align:center;color:white;padding:0px' bgcolor='#bb1d1d'>   					   <b>No 		 		  	 </b></th> 
					<th style='width:40px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"0-D\")'>       <b>Views  	  		  </b></th> 
					<th style='width:100px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' onclick='sortBy(\"1-A\")'>       <b>Item Code  	  	  </b></th> 
					<th style='width:53px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Image   	 	      	 </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' onclick='sortBy(\"2-A\")'>        <b>Name  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' onclick='sortBy(\"3-A\")'>        <b>Item Type  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' onclick='sortBy(\"4-A\")'>        <b>Outlet Type 	  	  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' onclick='sortBy(\"5-A\")'>        <b>Premium Type  	  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' onclick='sortBy(\"6-A\")'>        <b>Material  	  	  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >        <b>Brand  	  	  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' onclick='sortBy(\"10-A\")'>       <b>User  	  		  </b></th> 
					<th style='width:40px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' onclick='sortBy(\"11-A\")'>       <b>Publish  	  		  </b></th> 
					<th style='width:70px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' onclick='sortBy(\"12-D\")'>       <b>Local Price  	  	  </b></th> 
					<th style='width:70px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' onclick='sortBy(\"13-D\")'>       <b>USD Price  	  	  </b></th> 
					<th style='width:75px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' onclick='sortBy(\"14-A\")'>       <b>Uploaded  	  	  </b></th> 
					<th style='width:75px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' onclick='sortBy(\"15-A\")'>       <b>Released  	  	  </b></th> 
				</tr>";
		//REPLACE ORDER
		$table = str_replace($order_code,$this->sortingRef_eCat($order_code,'Rev_code'),$table);
		$table = str_replace($label,$this->sortingRef_eCat($order_code,'label_symbol'),$table);
		
					$x = $limit;			
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					foreach($items as $r) { 
					extract($r);
					$ptype = ($ptype=='') 	   ? 'Uncategorize' : $ptype;
					$publish = ($publish=='y') ? 'Yes' : 'No';
					$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 	
		$table.= "<tr>
				  <td $c>													$x      																		</td>
				  <td $c style='text-align:center;'>						 <a onclick=\"viewDialog('eCatalogue',$itemID)\" style='cursor:pointer;'> $num_views </a></td> 
				  <td $c>													$itemCode      																	</td>
				  <td $c style='text-align:center;'>			    		<img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 	</td>
				  <td $c style='text-align:left;padding-left:5px;'>		<a href='".HTTP_PATH."gallery/itemInfoECatalog/$ecID/$itemID' target='_blank'>		$itemName</a>  </td>
				  <td $c style='text-align:left;padding-left:5px;'>		$ptype																			</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$poutlet_status																			</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$ppremium_type																			</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$pmaterial																			</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$Brand																			</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$full_name																		</td>
				  <td $c style='text-align:center;'>						$publish																		</td>
				  <td $c style='text-align:center;'>						$UnitPrice																		</td>
				  <td $c style='text-align:center;'>						$USD_Price																		</td>
				  <td $c style='text-align:center;'>				         ". $this->convertDate('date',"$dUploaded 00:00:00") ."					</td>
				  <td $c style='text-align:center;'>				        ". $this->convertDate('date',"$dReleased 00:00:00") ." 					</td>
				</tr>";}
					if(!$items)
						$table.=  "<tr><td colspan='16'>No match found, please check search parameters.</td></tr>";
		$table.= "</table>";	
		
		$data['table'] 		= $table;
		
		$viewer = 'innerPages';	    
		if($this->modules->access_checker()==TRUE)
		{
			$this->load->view($viewer,$data); 
		}else{
			$data['vfile']				= 'login.php';
			$data['title']				= 'SMBi System Log-in | SMBi';
			$data['page_title']			= 'SMBi System Log-in';
			$data['meta_description']	= 'San Miguel Brewing International';
			$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
			$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
			$this->load->view('login',$data); 	
		}
	}
		
	function item_summary_eCatalogue($view='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(73,'REVIEW');
		//USER MANUAL
	    $data['USER_MANUAL'] = $this->modules->user_manual(43);
		
		//DETECT COUNTRY
		$cond = "";
		$WHERE="";
		$HAVING="";
		$data['cID']=0;
		$data['sa']=TRUE;

		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/item_summary_eCatalogue> eCatalogue Item Summary </a>';
		
		$table						= 'item_division';
		$data['vfile']				= 'item_summary_eCatalogue.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//TOTAL NUMBER OF ROWS	
		$sort='n';
		$WHERE = "WHERE ecID!=0 ";
		extract($_POST);
		$cond = "";
		$HAVING = "";
		//print_r($_POST);
		$data['DateFrom'] = "";
		$data['DateTo'] = "";
		$data['quarterStr'] = "";
		if((isset($DateFrom) AND isset($DateTo)) AND ($DateFrom!='' AND $DateTo!='') AND !isset($Reset)){
		$WHERE   .= "AND dReleased >= '$DateFrom' AND dReleased <= '$DateTo' ";
		$data['DateFrom'] = $DateFrom;
		$data['DateTo']   = $DateTo;

		//DETECT QUARTER STRING
		if($DateFrom==$this->perQuarterMonths('prevMonth') & $DateTo==$this->perQuarterMonths('nextMonth'))
		 $data['quarterStr'] = $this->quaterStr('condition');
		//PREVIOUS QUARTER
		if($DateFrom==$this->setPreviousQTR($DateFrom,'prevMonth') & $DateTo==$this->setPreviousQTR($DateTo,'nextMonth'))
		 $data['quarterStr'] = $this->SetPrevQuaterStr($DateFrom);
		//PREVIOUS QUARTER
		if($DateFrom==$this->minMaxDatePublishedEC('prevMonth') & $DateTo==$this->minMaxDatePublishedEC('nextMonth'))
		 $data['quarterStr'] = $this->minMaxDatePublishedEC('condition');
		}else{
		$WHERE   		   .= "AND ".$this->minMaxDatePublishedEC('filter');
		$data['quarterStr'] = $this->minMaxDatePublishedEC('condition');

		$data['DateFrom']   = $this->minMaxDatePublishedEC('prevMonth');
		$data['DateTo']     = $this->minMaxDatePublishedEC('nextMonth');
		}
		
		
		if(isset($Submit) OR isset($selpage))
		{	
			$val1 = mysql_real_escape_string($val1);
			$val1 = trim($val1);
			$val2 = mysql_real_escape_string($val2);
			$val2 = trim($val2);
			
			$condition = '';
			switch($cond1){
				case 'equal': 
					$condition = '=';
				break;
				case 'containing': 
					$condition = 'like';
				break;
				case 'in': 
					$condition = 'in';
				break;
				case 'greaterThan': 
					$condition = '>=';
				break;
				case 'lessThan': 
					$condition = '<=';
				break;
				case 'between': 
					$condition = 'between';
				break;
			}
			
			//COUNTRY NAME
			if(($opt1=='ecName' OR $opt1=='ptype' OR $opt1=='poutlet_status' OR $opt1=='ppremium_type' OR $opt1=='pmaterial') AND $val1!='')
			{
				if($condition=='=')
					$cond .= " AND $opt1 $condition '$val1'";
				if($condition=='>=')
					$cond .= " AND $opt1 $condition '$val1'";
				if($condition=='<=')
					$cond .= " AND $opt1 $condition '$val1'";
				if($condition=='like')
					$cond .= " AND $opt1 $condition '%$val1%'";
				if($condition=='in')
					$cond .= " AND $opt1 $condition ('" . str_replace(",", "','", $val1) . "')";
			}

			//PUBLISH
			$field="";
			if($opt1=='uploaded')
					$field=" COUNT(itemID) ";
			if($opt1=='views')
				$field=" COUNT(num_views) ";
			if(($opt1=='uploaded' OR $opt1=='views') AND $val1!='' AND is_numeric($val1))
			{
				if($condition=='=')
					$HAVING = " HAVING $field $condition $val1 ";
				if($condition=='>=')
					$HAVING = " HAVING $field $condition $val1 ";
				if($condition=='<=')
					$HAVING = " HAVING $field $condition $val1 ";
				if($condition=='in' AND (is_numeric($val1) OR strpos($val1,',')==TRUE))
					$HAVING = "   HAVING $field $condition ('" . str_replace(",", "','", $val1) . "')";
			}
			
			if($condition=='between' & $this->checkStr($val1)==TRUE)
					$HAVING = " HAVING $field $condition ".stripslashes($val1);
			
			/*2ND SET*/
			switch($cond2){
				case 'equal': 
					$condition2 = '=';
				break;
				case 'containing': 
					$condition2 = 'like';
				break;
				case 'in': 
					$condition2 = 'in';
				break;
				case 'greaterThan': 
					$condition2 = '>=';
				break;
				case 'lessThan': 
					$condition2 = '<=';
				break;
				case 'between': 
					$condition2 = 'between';
				break;
			}
			
			//COUNTRY NAME
			if(($opt2=='ecName' OR $opt2=='ptype' OR $opt2=='poutlet_status' OR $opt2=='ppremium_type' OR $opt2=='pmaterial') AND $val2!='')
			{
				if($condition2=='=')
					$cond .= "$operator  $opt2 $condition2 '$val2'";
				if($condition2=='>=')
					$cond .= "$operator $opt2 $condition2 '$val2'";
				if($condition2=='<=')
					$cond .= "$operator $opt2 $condition2 '$val2'";
				if($condition2=='like')
					$cond .= "$operator $opt2 $condition2 '%$val2%'";
				if($condition2=='in')
					$cond .= "$operator $opt2 $condition2 ('" . str_replace(",", "','", $val2) . "')";
			}			
			
			$field="";
			if($opt2=='uploaded')
					$field=" COUNT(itemID) ";
			if($opt2=='views')
				$field=" COUNT(num_views) ";
			if(($opt2=='uploaded' OR $opt2=='views') AND $val2!='' AND (is_numeric($val2) OR $this->checkStr($val2)==TRUE))
			{
				$HAVING .= ($HAVING=="") ? "HAVING" : "$operator";
				if($condition2=='=')
					$HAVING .= " $field $condition2 $val2 ";
				if($condition2=='>=')
					$HAVING .= " $field $condition2 $val2 ";
				if($condition2=='<=')
					$HAVING .= " $field $condition2 $val2 ";
				if($condition2=='in' AND (is_numeric($val2) OR strpos($val2,',')==TRUE))
					$HAVING .= "  $field $condition2 ('" . str_replace(",", "','", $val2) . "')";
			}	
			
			if($condition2=='between' & $this->checkStr($val2)==TRUE)
				$HAVING .= " $field $condition2 ".stripslashes($val2);
			
			$cond = ($cond=="") ? "" : " $cond ";
			$WHERE  .=  " $cond ";
		}
		
		//IF THEIR NO POSSIBLE RESULT
		$valid_query=TRUE;
		if(isset($Submit)){
			if(($cond=="" AND $HAVING=="") AND ($val1!='' OR $val2!='')){ 
			$valid_query=FALSE;
			$WHERE = " WHERE ecID='-1'";
			}
		}
		
		$data['POST'] = $_POST;
		if(isset($Reset)) $data['POST'] = array();
		
		//POSM STATUS;
		$arr = '';
		$sql_csv='';
		switch($view)
		{
		case '':
		case 'POSM_TYPE':
		//POSM TYPE;
		$data['tab'] = 'POSM_TYPE';
		$q = "SELECT 
				ecName   		 as Catalogue_Name, ecID,
				ptype 			 as fldVal,
				ptypeID 		 as fldID,
				COUNT(itemID)    as Uploaded_Items,
				SUM(num_views)   as Views
				FROM ec_item_reports $WHERE
				GROUP BY ecName, ptype $HAVING ORDER BY ptype ASC, ecName ASC";
		
		$csv = "SELECT 
				ecName   		 as Catalogue_Name,
				ptype 			 as POSM_TYPE,
				COUNT(itemID)    as Uploaded_Items,
				SUM(num_views) as Views
				FROM ec_item_reports $WHERE
				GROUP BY ecName, ptype $HAVING ORDER BY ptype ASC, ecName ASC";
		
		$sql   = $this->db->query($q);
		$arr[] = array('table'=>'POSM Type',
					   'fld'=>'ptypeID',
					   'rows'=>$sql->result_array());
		break;
		case 'OUTLET_TYPE':
		//OUTLET TYPE;
		$data['tab'] = 'OUTLET_TYPE';
		$q = "SELECT 
				ecName   		 	as Catalogue_Name, ecID,
				poutlet_status 	 	as fldVal,
				poutlet_statusID 	as fldID,
				COUNT(itemID)    	as Uploaded_Items,
				SUM(num_views) as Views
				FROM ec_item_reports $WHERE
				GROUP BY ecName, poutlet_status $HAVING ORDER BY poutlet_status ASC, ecName ASC";
		$csv = "SELECT 
				ecName   		 	as Catalogue_Name,
				poutlet_status 	 	as POSM_STATUS,
				COUNT(itemID)    	as Uploaded_Items,
				SUM(num_views) as Views
				FROM ec_item_reports $WHERE
				GROUP BY ecName, poutlet_status $HAVING ORDER BY poutlet_status ASC, ecName ASC";		
		$sql = $this->db->query($q);
		$arr[] = array('table'=>'OUTLET TYPE',
					   'fld'=>'poutlet_statusID',
					   'rows'=>$sql->result_array());
		break;
		case 'PREMIUM_TYPE':
		//PREMIUM TYPE;
		$data['tab'] = 'PREMIUM_TYPE';
		$q = "SELECT 
				ecName   		 	as Catalogue_Name, ecID,
				ppremium_type 	 	as fldVal,
				ppremium_typeID 	as fldID,
				COUNT(itemID)    	as Uploaded_Items,
				SUM(num_views) as Views
				FROM ec_item_reports $WHERE
				GROUP BY ecName, ppremium_type $HAVING ORDER BY ppremium_type ASC, ecName ASC";
		$csv = "SELECT 
				ecName   		 	as Catalogue_Name,
				ppremium_type 	 	as PREMIUM_TYPE,
				COUNT(itemID)    	as Uploaded_Items,
				SUM(num_views) as Views
				FROM ec_item_reports $WHERE
				GROUP BY ecName, ppremium_type $HAVING ORDER BY ppremium_type ASC, ecName ASC";
		$sql   = $this->db->query($q);
		$arr[] = array('table'=>'PREMIUM Type',
					   'fld'=>'ppremium_typeID',
					   'rows'=>$sql->result_array());
		break;
		case 'MATERIAL_TYPE':
		//MATERIAL TYPE;
		$data['tab'] = 'MATERIAL_TYPE';
		$q = "SELECT 
				ecName   		 	as Catalogue_Name, ecID,
				pmaterial 	 		as fldVal,
				pmaterialID 		as fldID,
				COUNT(itemID)    	as Uploaded_Items,
				SUM(num_views) as Views
				FROM ec_item_reports  $WHERE
				GROUP BY ecName, pmaterial $HAVING ORDER BY pmaterial ASC, ecName ASC";
		$csv = "SELECT 
				ecName   		 	as Catalogue_Name, 
				pmaterial 	 		as MATERIAL_TYPE,
				COUNT(itemID)    	as Uploaded_Items,
				SUM(num_views) as Views
				FROM ec_item_reports  $WHERE
				GROUP BY ecName, pmaterial $HAVING ORDER BY pmaterial ASC, ecName ASC";
		$sql   = $this->db->query($q);
		$arr[] = array('table'=>'MATERIAL Type',
					   'fld'=>'pmaterialID',
					   'rows'=>$sql->result_array());
		break;
		}
		
		
		$data['results'] = $arr;
		
		//generate csv file
		$this->generateCSVFile('item_views',$csv,"eCatalogue_Item_Summary".$this->reportCode().".csv");
		$data['csvFile']			= "eCatalogue_Item_Summary".$this->reportCode().".csv";
		
		$viewer = 'innerPages';	    
		if($this->modules->access_checker()==TRUE)
		{
			$this->load->view($viewer,$data); 
		}else{
			$data['vfile']				= 'login.php';
			$data['title']				= 'SMBi System Log-in | SMBi';
			$data['page_title']			= 'SMBi System Log-in';
			$data['meta_description']	= 'San Miguel Brewing International';
			$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
			$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
			$this->load->view('login',$data); 	
		}
	}
	
	function minMaxDatePublishedEC($type='')
	{
		//GET CURRENT
		$curDate = date('Y-m-d');
		$sql = $this->db->query("SELECT min(dReleased) AS minDate, max(dReleased) AS maxDate  FROM ec_item_reports LIMIT 0,1");
		$sql = $sql->row();
		$prevMonth = $sql->minDate;
		$nextMonth = $sql->maxDate;
		
		$str = "";
		if($type=='prevMonth')
			$str = "$prevMonth";
		if($type=='nextMonth')
			$str = $nextMonth;
		if($type=='filter')
			$str = " (dReleased BETWEEN '$prevMonth' AND '$nextMonth') ";
		if($type=='condition')
		    $str = "Report as of Today: $curDate";
		
		return $str;
	}
	
	function eCatalogue_index()
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(73,'REVIEW');
		
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(43);
		
		extract($_POST);
		
		$table						= 'eCatalogue_index';
		$data['vfile']				= 'eCatalogue_index.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/eCatalogue_index> eCatalogue Report </a>';
		
		//TOTAL NUMBER OF ROWS			
		extract($_POST);
		$cond = "";
		$HAVING = "";
		$WHERE = "WHERE ecID!=0 ";
		$data['DateFrom'] = "";
		$data['DateTo'] = "";
		$data['quarterStr'] = "";
		if((isset($DateFrom) AND isset($DateTo)) AND ($DateFrom!='' AND $DateTo!='') AND !isset($Reset)){
		$WHERE   .= "AND dReleased >= '$DateFrom' AND dReleased <= '$DateTo' ";
		$data['DateFrom'] = $DateFrom;
		$data['DateTo']   = $DateTo;

		//DETECT QUARTER STRING
		if($DateFrom==$this->perQuarterMonths('prevMonth') & $DateTo==$this->perQuarterMonths('nextMonth'))
		 $data['quarterStr'] = $this->quaterStr('condition');
		//PREVIOUS QUARTER
		if($DateFrom==$this->setPreviousQTR($DateFrom,'prevMonth') & $DateTo==$this->setPreviousQTR($DateTo,'nextMonth'))
		 $data['quarterStr'] = $this->SetPrevQuaterStr($DateFrom);
		//PREVIOUS QUARTER
		if($DateFrom==$this->minMaxDatePublishedEC('prevMonth') & $DateTo==$this->minMaxDatePublishedEC('nextMonth'))
		 $data['quarterStr'] = $this->minMaxDatePublishedEC('condition');
		}else{
		$WHERE   		   .= "AND ".$this->minMaxDatePublishedEC('filter');
		$data['quarterStr'] = $this->minMaxDatePublishedEC('condition');

		$data['DateFrom']   = $this->minMaxDatePublishedEC('prevMonth');
		$data['DateTo']     = $this->minMaxDatePublishedEC('nextMonth');
		}
		
		if(isset($Submit) OR isset($selpage))
		{	
			$val1 = mysql_real_escape_string($val1);
			$val1 = trim($val1);
			$val2 = mysql_real_escape_string($val2);
			$val2 = trim($val2);
			
			$condition = '';
			switch($cond1){
				case 'equal': 
					$condition = '=';
				break;
				case 'containing': 
					$condition = 'like';
				break;
				case 'in': 
					$condition = 'in';
				break;
				case 'greaterThan': 
					$condition = '>=';
				break;
				case 'lessThan': 
					$condition = '<=';
				break;
				case 'between': 
					$condition = 'between';
				break;
			}
			
			//CATALOGUE NAME
			if(($opt1=='ecName') AND $val1!='')
			{
				if($condition=='=')
					$cond = " AND $opt1 $condition '$val1'";
				if($condition=='>=')
					$cond = " AND $opt1 $condition '$val1'";
				if($condition=='<=')
					$cond = " AND $opt1 $condition '$val1'";
				if($condition=='like')
					$cond = " AND $opt1 $condition '%$val1%'";
				if($condition=='in')
					$cond = " AND $opt1 $condition ('" . str_replace(",", "','", $val1) . "')";
			}

			//PUBLISH
			$field="";
			if($opt1=='uploaded')
				$field=" COUNT(itemID) ";
			if($opt1=='avg_local')
				$field=" ROUND(AVG(UnitPrice),2)";
			if($opt1=='avg_usd')
				$field=" ROUND(AVG(USD_Price),2)";
			if(($opt1=='uploaded' OR $opt1=='avg_local' OR $opt1=='avg_usd') AND $val1!='' AND is_numeric($val1))
			{				
				if($condition=='=')
					$HAVING = " HAVING $field $condition '$val1' ";
				if($condition=='>=')
					$HAVING = " HAVING $field $condition '$val1' ";
				if($condition=='<=')
					$HAVING = " HAVING $field $condition '$val1' ";
				if($condition=='in' AND (is_numeric($val1) OR strpos($val1,',')==TRUE))
					$HAVING = "   HAVING $field $condition ('" . str_replace(",", "','", $val1) . "')";
			}
			
			if($condition=='between' & $this->checkStr($val1)==TRUE)
					$HAVING = " HAVING $field $condition ".stripslashes($val1);
			
			/*2ND SET*/
			switch($cond2){
				case 'equal': 
					$condition2 = '=';
				break;
				case 'containing': 
					$condition2 = 'like';
				break;
				case 'in': 
					$condition2 = 'in';
				break;
				case 'greaterThan': 
					$condition2 = '>=';
				break;
				case 'lessThan': 
					$condition2 = '<=';
				break;
				case 'between': 
					$condition2 = 'between';
				break;
			}
			
			//CATALOGUE NAME
			if(($opt2=='ecName') AND $val2!='')
			{
				if($condition2=='=')
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='>=')
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='<=')
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator $opt2 $condition2 '%$val2%'";
				if($condition2=='in')
					$cond .= " $operator $opt2 $condition2 ('" . str_replace(",", "','", $val2) . "')";
			}
			
			//PUBLISH
			$field="";
			if($opt2=='uploaded')
				$field=" COUNT(itemID) ";
			if($opt2=='avg_local')
				$field=" ROUND(AVG(UnitPrice),2)";
			if($opt2=='avg_usd')
				$field=" ROUND(AVG(USD_Price),2)";
			if(($opt2=='uploaded' OR $opt2=='avg_local' OR $opt2=='avg_usd') AND $val2!='' AND is_numeric($val2))
			{				
				$HAVING .= ($HAVING=="") ? "HAVING" : "$operator";
				if($condition2=='=')
					$HAVING .= " $field $condition2 '$val2' ";
				if($condition2=='>=')
					$HAVING .= " $field $condition2 '$val2' ";
				if($condition2=='<=')
					$HAVING .= " $field $condition2 '$val2' ";
				if($condition2=='in' AND (is_numeric($val2) OR strpos($val2,',')==TRUE))
					$HAVING .= " $field $condition2 ('" . str_replace(",", "','", $val2) . "')";
			}
			
			if($condition2=='between' & $this->checkStr($val2)==TRUE)
				$HAVING .= " $field $condition2 ".stripslashes($val2);
			
			$cond = ($cond=="") ? "" : " $cond ";
			$WHERE  .=  " $cond ";
		}
		
	
	
		$sql = "SELECT ecID as eID, ecName as title, ecCover as cover, COUNT(itemID) AS eCatalogueItems, 
				AVG(UnitPrice) as avgUnitPrice, AVG(USD_Price) as avgUSDPrice 
				FROM ec_item_reports $WHERE
			    GROUP BY ecName $HAVING";
				
		$sql   = $this->db->query($sql);
		$data['POST'] 	   = $_POST;
		if(isset($Reset)) $data['POST'] = array();
		$data['reports']   = $sql->result_array();
		
		$sql_csv = "SELECT ecName as Title, COUNT(itemID) AS eCatalogue_Items, 
				AVG(UnitPrice) as Average_Unit_Price, AVG(USD_Price) as Average_USD_Price 
				FROM ec_item_reports $WHERE
			    GROUP BY ecName $HAVING";
		//generate csv file
		$this->generateCSVFile('item_views',$sql_csv,"eCatalogue_Report".$this->reportCode().".csv");
		$data['csvFile']			= "eCatalogue_Report".$this->reportCode().".csv";
	
		$viewer = 'innerPages';	    
		if($this->modules->access_checker()==TRUE)
		{
			$this->load->view($viewer,$data); 
		}else{
			$data['vfile']				= 'login.php';
			$data['title']				= 'SMBi System Log-in | SMBi';
			$data['page_title']			= 'SMBi System Log-in';
			$data['meta_description']	= 'San Miguel Brewing International';
			$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
			$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
			$this->load->view('login',$data); 	
		}
	}
	
	function sortingRef_eCat($id='',$returnType='')
	{
		$query	  ='';
		$Orig_code='';
		$Rev_code ='';
		switch($id)
		{
		 case '0-A':
		  $query 	 	 = "num_views ASC";
		  $Orig_code 	 = "0-A";
		  $Rev_code  	 = "0-D";
		  $label	 	 = "Views ";
		  $label_symbol	 = "Views &#x25B2;";
		  //$label
		 break;
		 case '0-D':
		  $query 	 	 = "num_views DESC"; 
		  $Orig_code 	 = "0-D";
		  $Rev_code  	 = "0-A";
		  $label	 	 = "Views ";
		  $label_symbol	 = "Views &#x25BC;";
		 break;
		 case '1-A':
		  $query = " itemCode ASC"; 
		  $Orig_code = "1-A";
		  $Rev_code  = "1-D";
		  $label	 	 = "Item Code ";
		  $label_symbol	 = "Item Code &#x25B2;";
		 break;
		 case '1-D':
		  $query     	 = "itemCode DESC"; 
		  $Orig_code 	 = "1-D";
		  $Rev_code  	 = "1-A";
		  $label	 	 = "Item Code ";
		  $label_symbol	 = "Item Code &#x25BC;";
		 break;
		 case '2-A':
		  $query     	 = "itemName ASC"; 
		  $Orig_code 	 = "2-A";
		  $Rev_code  	 = "2-D";
		  $label	 	 = "Name ";
		  $label_symbol	 = "Name &#x25B2;";
		 break;
		 case '2-D':
		  $query     = "itemName DESC"; 
		  $Orig_code = "2-D";
		  $Rev_code  = "2-A";
		  $label	 	 = "Name ";
		  $label_symbol	 = "Name &#x25BC;";
		 break;
		 case '3-A':
		  $query     = "ptype ASC";
	      $Orig_code = "3-A";
		  $Rev_code  = "3-D";
		  $label	 	 = "Item Type ";
		  $label_symbol	 = "Item Type &#x25B2;";
		 break;
		 case '3-D':
		  $query     = "ptype DESC"; 
		  $Orig_code = "3-D";
		  $Rev_code  = "3-A";
		   $label	 	 = "Item Type ";
		  $label_symbol	 = "Item Type &#x25BC;";
		 break;
		 case '4-A':
		  $query 	 = "poutlet_status ASC"; 
		  $Orig_code = "4-A";
		  $Rev_code  = "4-D";
		  $label	 	 = "Outlet Type";
		  $label_symbol	 = "Outlet Type &#x25B2;";
		 break;
		 case '4-D':
		  $query = " poutlet_status DESC";
		  $Orig_code = "4-D";
		  $Rev_code  = "4-A";
		  $label	 	 = "Outlet Type";
		  $label_symbol	 = "Outlet Type &#x25BC;";
		 break;
		 case '5-A':
		  $query 	 	 = " ppremium_type ASC"; 
		  $Orig_code 	 = "5-A";
		  $Rev_code  	 = "5-D";
		  $label	 	 = "Premium Type";
		  $label_symbol	 = "Premium Type &#x25B2;";
		 break;
		 case '5-D':
		  $query 		 = " ppremium_type DESC";
		  $Orig_code 	 = "5-D";
		  $Rev_code  	 = "5-A";
		  $label	 	 = "Premium Type";
		  $label_symbol	 = "Premium Type &#x25BC;";
		 break;
		 case '6-A':
		  $query = " pmaterial ASC";
		  $Orig_code = "6-A";
		  $Rev_code  = "6-D";
		  $label	 	 = "Material";
		  $label_symbol	 = "Material &#x25B2;";
		 break;
		 case '6-D':
		  $query = " pmaterial DESC"; 
		  $Orig_code = "6-D";
		  $Rev_code  = "6-A";
		  $label	 	 = "Material";
		  $label_symbol	 = "Material &#x25BC;";
		 break;
		 case '7-A':
		  $query = " poutlet_status ASC"; 
		  $Orig_code = "7-A";
		  $Rev_code  = "7-D";
		  $label	 	 = "Outlet";
		  $label_symbol	 = "Outlet &#x25B2;";
		 break;
		 case '7-D':
		  $query = " poutlet_status DESC"; 
		  $Orig_code = "7-D";
		  $Rev_code  = "7-A";
		  $label	 	 = "Outlet";
		  $label_symbol	 = "Outlet &#x25BC;";
		 break;
		 case '8-A':
		  $query = " ppremium_type ASC"; 
		  $Orig_code = "8-A";
		  $Rev_code  = "8-D";
		  $label	 	 = "Premium";
		  $label_symbol	 = "Premium &#x25B2;";
		 break;
		 case '8-D':
		  $query = " ppremium_type DESC"; 
		  $Orig_code = "8-D";
		  $Rev_code  = "8-A";
		  $label	 	 = "Premium";
		  $label_symbol	 = "Premium &#x25BC;";
		 break;
		 case '9-A':
		  $query = " pmaterial ASC";
		  $Orig_code = "9-A";
		  $Rev_code  = "9-D";
		  $label	 	 = "Material";
		  $label_symbol	 = "Material &#x25B2;";
		 break;
		 case '9-D':
		  $query = " pmaterial DESC"; 
		  $Orig_code = "9-D";
		  $Rev_code  = "9-A";
		  $label	 	 = "Material";
		  $label_symbol	 = "Material &#x25BC;";
		 break;
		 case '10-A':
		  $query = " full_name ASC"; 
		  $Orig_code = "10-A";
		  $Rev_code  = "10-D";
		  $label	 	 = "User";
		  $label_symbol	 = "User &#x25B2;";
		 break;
		 case '10-D':
		  $query = " full_name DESC"; 
		  $Orig_code = "10-D";
		  $Rev_code  = "10-A";
		  $label	 	 = "User";
		  $label_symbol	 = "User &#x25BC;";
		 break;
		 case '11-A':
		  $query = " publish ASC"; 
		  $Orig_code = "11-A";
		  $Rev_code  = "11-D";
		  $label	 	 = "Publish";
		  $label_symbol	 = "Publish &#x25B2;";
		 break;
		 case '11-D':
		  $query = " publish DESC"; 
		  $Orig_code = "11-D";
		  $Rev_code  = "11-A";
		  $label	 	 = "Publish";
		  $label_symbol	 = "Publish &#x25BC;";
		 break;
		 case '12-A':
		  $query = " UnitPrice ASC"; 
		  $Orig_code = "12-A";
		  $Rev_code  = "12-D";
		  $label	 	 = "Local Price";
		  $label_symbol	 = "Local Price &#x25B2;";
		 break;
		 case '12-D':
		  $query = " UnitPrice DESC"; 
		  $Orig_code = "12-D";
		  $Rev_code  = "12-A";
		  $label	 	 = "Local Price";
		  $label_symbol	 = "Local Price &#x25BC;";
		 break;
		 case '13-A':
		  $query = " USD_Price ASC"; 
		  $Orig_code = "13-A";
		  $Rev_code  = "13-D";
		  $label	 	 = "USD Price";
		  $label_symbol	 = "USD Price &#x25B2;";
		 break;
		 case '13-D':
		  $query = " USD_Price DESC"; 
		  $Orig_code = "13-D";
		  $Rev_code  = "13-A";
		  $label	 	 = "USD Price";
		  $label_symbol	 = "USD Price &#x25BC;";
		 break;
		 case '14-A':
		  $query = " dUploaded ASC"; 
		  $Orig_code = "14-A";
		  $Rev_code  = "14-D";
		  $label	 	 = "Uploaded";
		  $label_symbol	 = "Uploaded &#x25B2;";
		 break;
		 case '14-D':
		  $query = " dUploaded DESC"; 
		  $Orig_code = "14-D";
		  $Rev_code  = "14-A";
		  $label	 	 = "Uploaded";
		  $label_symbol	 = "Uploaded &#x25BC;";
		 break;
		 case '15-A':
		  $query = " dReleased ASC"; 
		  $Orig_code = "15-A";
		  $Rev_code  = "15-D";
		  $label	 	 = "Released";
		  $label_symbol	 = "Released &#x25B2;";
		 break;
		 case '15-D':
		  $query = " dReleased DESC"; 
		  $Orig_code = "15-D";
		  $Rev_code  = "15-A";
		  $label	 	 = "Released";
		  $label_symbol	 = "Released &#x25BC;";
		 break;
		}
		if($returnType=='query')
			return $query;
		elseif($returnType=='Orig_code')
			return $Orig_code;
		elseif($returnType=='Rev_code')
			return $Rev_code;
		elseif($returnType=='label')
			return $label;
		elseif($returnType=='label_symbol')
			return $label_symbol;
	}
	
	function eCatalogue_report_details($ecID='',$DateFrom='',$DateTo='')
	{
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(43);
		$data['ecID']	   = $ecID;
		
		//eCatalogue Name
		$sql = $this->db->query("SELECT title FROM e_catalog WHERE id= $ecID LIMIT 0,1");
		$sql = $sql->row();
		$data['ecTitle'] = $sql->title;
		
		//BREAD CRUMBS
		$HTTP_PATH 					 = HTTP_PATH."report/iLike";
		$data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
		$data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/eCatalogue_index>  eCatalogue Report </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
		$data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/eCatalogue_report_details/$ecID/$DateFrom/$DateTo'>  eCatalogue Report in Details </a>";
		
		//print_r($_POST);
		$WHERE="WHERE ec_item_reports.ecID = $ecID ";
		$cond="";
		$sort='n';
		extract($_POST);
		$data['DateFrom'] = "";
		$data['DateTo'] = "";
		$data['quarterStr'] = "";
		if((isset($DateFrom) AND isset($DateTo)) AND ($DateFrom!='' AND $DateTo!='') AND !isset($Reset)){
		$WHERE   .= "AND dReleased >= '$DateFrom' AND dReleased <= '$DateTo' ";
		$data['DateFrom'] = $DateFrom;
		$data['DateTo']   = $DateTo;

		//DETECT QUARTER STRING
		if($DateFrom==$this->perQuarterMonths('prevMonth') & $DateTo==$this->perQuarterMonths('nextMonth'))
		 $data['quarterStr'] = $this->quaterStr('condition');
		//PREVIOUS QUARTER
		if($DateFrom==$this->setPreviousQTR($DateFrom,'prevMonth') & $DateTo==$this->setPreviousQTR($DateTo,'nextMonth'))
		 $data['quarterStr'] = $this->SetPrevQuaterStr($DateFrom);
		//PREVIOUS QUARTER
		if($DateFrom==$this->minMaxDatePublishedEC('prevMonth') & $DateTo==$this->minMaxDatePublishedEC('nextMonth'))
		 $data['quarterStr'] = $this->minMaxDatePublishedEC('condition');
		}else{
		$WHERE   		   .= "AND ".$this->minMaxDatePublishedEC('filter');
		$data['quarterStr'] = $this->minMaxDatePublishedEC('condition');

		$data['DateFrom']   = $this->minMaxDatePublishedEC('prevMonth');
		$data['DateTo']     = $this->minMaxDatePublishedEC('nextMonth');
		}
		
		
		$having="";
		if(isset($Submit) OR isset($selpage))
		{	
			$val1 = mysql_real_escape_string($val1);
			$val1 = trim($val1);
			$val2 = mysql_real_escape_string($val2);
			$val2 = trim($val2);
			
			$condition = '';
			switch($cond1){
				case 'equal': 
					$condition = '=';
				break;
				case 'containing': 
					$condition = 'like';
				break;
				case 'in': 
					$condition = 'in';
				break;
				case 'greaterThan': 
					$condition = '>=';
				break;
				case 'lessThan': 
					$condition = '<=';
				break;
				case 'between': 
					$condition = 'between';
				break;
			}
			
			//VIEWS
			if(($opt1=='num_views' OR $opt1=='UnitPrice' OR $opt1=='USD_Price') AND $val1!='' AND $condition!='like'){ 
				if($condition=='in' AND (is_numeric($val1) OR strpos($val1,',')==TRUE))
					$cond = "  $opt1 $condition ('" . str_replace(",", "','", $val1) . "')";
				elseif($condition!='in' AND is_numeric($val1) AND $condition!='between')
					$cond = "  $opt1 $condition $val1";
				elseif($condition=='between' & $this->checkStr($val1)==TRUE)
					$cond = "  $opt1 $condition ".stripslashes($val1);
			}
			
			if(($opt1=='itemCode' OR $opt1=='itemName' OR $opt1=='ptype' OR $opt1=='poutlet_status' OR $opt1=='ppremium_type' OR $opt1=='pmaterial' OR $opt1=='full_name') AND $val1!='')
			{
				if($condition=='=')
					$cond = "  $opt1 $condition '$val1'";
				if($condition=='>=')
					$cond = "  $opt1 $condition '$val1'";
				if($condition=='<=')
					$cond = "  $opt1 $condition '$val1'";
				if($condition=='like')
					$cond = "  $opt1 $condition '%$val1%'";
				if($condition=='in')
					$cond = "  $opt1 $condition ('" . str_replace(",", "','", $val1) . "')";
			}
			
			//PUBLISH
			if(($opt1=='publish') AND $val1!='')
			{
				if($condition=='=')
					$val1 = ($val1=='yes' OR $val1=='Yes') ? 'y' : 'n';
					$cond = "  $opt1 $condition '$val1'";
				if($condition=='like')
					$cond = "  $opt1 $condition '%$val1%'";
			}
			
			//dateReleased
			if($opt1=='dUploaded' AND $val1!='' AND $condition!='like' AND $condition!='between') 
				$cond = "  dUploaded $condition '$val1'";
			if($opt1=='dUploaded' AND $val1!='' AND $condition=='between' & $this->checkStr($val1)==TRUE) 
				$cond = "  dUploaded $condition ".stripslashes($val1);
			
			/*2ND SET*/
			switch($cond2){
				case 'equal': 
					$condition2 = '=';
				break;
				case 'containing': 
					$condition2 = 'like';
				break;
				case 'in': 
					$condition2 = 'in';
				break;
				case 'greaterThan': 
					$condition2 = '>=';
				break;
				case 'lessThan': 
					$condition2 = '<=';
				break;
				case 'between': 
					$condition2 = 'between';
				break;
			}
			
			
			//VIEWS
			if(($opt2=='num_views' OR $opt2=='UnitPrice' OR $opt2=='USD_Price') AND $val2!='' AND $condition2!='like'){ 
				if($condition2=='in' AND (is_numeric($val2) OR strpos($val2,',')==TRUE))
					$cond .= " $operator $opt2 $condition2 ('" . str_replace(",", "','", $val2) . "')";
				elseif($condition2!='in' AND is_numeric($val2))
					$cond .= " $operator $opt2 $condition2 $val2";
				elseif($condition2=='between' & $this->checkStr($val2)==TRUE)
					$cond .= " $operator $opt2 $condition2 ".stripslashes($val2);
			}
			
			if(($opt2=='itemCode' OR $opt2=='itemName' OR $opt2=='ptype' OR $opt2=='poutlet_status' OR $opt2=='ppremium_type' OR $opt2=='pmaterial' OR $opt2=='full_name') AND $val2!='')
			{
				if($condition2=='=')
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='>=')
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='<=')
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator $opt2 $condition2 '%$val2%'";
				if($condition2=='in')
					$cond .= " $operator $opt2 $condition2 ('" . str_replace(",", "','", $val2) . "')";
			}
			
			//PUBLISH
			if(($opt2=='publish') AND $val2!='')
			{
				if($condition2=='=')
					$val2 = ($val2=='yes' OR $val2=='Yes') ? 'y' : 'n';
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator $opt2 $condition2 '%$val2%'";
			}
			
			//dateReleased
			if($opt2=='dUploaded' AND $val2!='' AND $condition2!='like' AND $condition2!='between') 
				$cond .= " $operator dUploaded $condition2 '$val2'";
			if($opt2=='dUploaded' AND $val2!='' AND $condition2=='between' & $this->checkStr($val2)==TRUE) 
				$cond .= " $operator dUploaded $condition2 ".stripslashes($val2);
				
			$cond = ($cond=="") ? "" : "AND ($cond) ";
			$WHERE  .=  " $cond ";
		}
		
		$data['POST'] = $_POST;
		$limit =isset($selpage)? $selpage:0;
		if(isset($Reset)){
			$data['POST'] = array();
			$cond="WHERE ec_item_reports.ecID = $ecID ";
			$limit=0;
		}
		
		//ORDER
		$ORDER = $this->sortingRef_eCat('0-D','query');
		$data['order'] = $this->sortingRef_eCat('0-D','Orig_code');
		$order_code = '0-D';
		$label = "Views";
		if(isset($order)){
			$ORDER = $this->sortingRef_eCat($order,'query');
			$data['order'] = $this->sortingRef_eCat($order,'Orig_code');
			$order_code = $order;
			$label = $this->sortingRef_eCat($order,'label');
		}	
		
		$sql = "SELECT iID as itemID, ecID, itemName, ptype, poutlet_status, ppremium_type, pmaterial,
				itemCode, num_views, 
				item_image, dUploaded, full_name, publish,  UnitPrice, USD_Price, dReleased, ecName as Brand
				FROM ec_item_reports 
				$WHERE ORDER BY $ORDER";
		
		$sql_csv = $this->db->query("SELECT num_views as Views, itemCode as Item_Code, itemName as Item_Name, ptype as Item_Type, poutlet_status as Outlet_Status, ppremium_type as Premium_Type, pmaterial as Material_Type,
									 full_name as User, publish as Publish, UnitPrice as Unit_Price, ecName as Brand, USD_Price as USD_Price, dUploaded as Date_Uploaded, dReleased as Date_Released  
									 FROM ec_item_reports 
									 $WHERE ORDER BY $ORDER");
		//SORT STATUS
		$limit = ($sort)=='y' ? 0 : $limit;
		if(isset($Submit)) $limit=0;
		//echo $sql;
	
		$ctr = $this->db->query($sql);
		$ctr = $ctr->result_array();
		$data['totrec'] = count($ctr);
		$data['limit']  = $limit;
		
		$sql 	 = $this->db->query($sql." LIMIT $limit,20");
		$items	 = $sql->result_array();
		
		//generate csv file
		$csv  = "eCatalogue Report in Details\n";
		$csv .= "No, Views, Item Code,  Item Name, Type, Outlet Status, Premium Type, Material Type, Brand, User, Publish, Local Price, USD Price, Date Uploaded, Date Released\n";
		$sql_csv = $sql_csv->result_array(); $x=0;
		foreach($sql_csv as $s)
		{ extract($s); $x++;
		  $Publish 	  	 = ($Publish=='y')    ? "Yes" : "No";
		  $Date_Uploaded =  $this->convertDate('date',"$Date_Uploaded 00:00:00");
		  $Date_Released =  $this->convertDate('date',"$Date_Released 00:00:00");
		  $Item_Name     =  str_replace(",","-",$Item_Name);
		  $csv .= "$x, $Views, $Item_Code, $Item_Name, $Item_Type, $Outlet_Status, $Premium_Type, $Material_Type, $Brand, $User, $Publish, $Unit_Price, $USD_Price,  $Date_Uploaded, $Date_Released\n";
		}
	    write_file(getcwd()."/files/csv/eCatalogue_Report_in_Details".$this->reportCode().".csv",$csv);
		$data['csvFile'] = "eCatalogue_Report_in_Details".$this->reportCode().".csv";
	
		$table= "<table id='large2' cellpadding='0' cellspacing='0' border=1 style='width:120%;font-size:12px;' class='iLike_Result_Table2'>
				<tr>
					<th style='width:10px;text-align:center;color:white;padding:0px' bgcolor='#bb1d1d'>   					   <b>No 		 		  </b></th> 
					<th style='width:40px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"0-D\")'>       <b>Views  	  		  </b></th> 
					<th style='width:100px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' onclick='sortBy(\"1-A\")'>       <b>Item Code  	  	  </b></th> 
					<th style='width:53px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Image   	 	      </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' onclick='sortBy(\"2-A\")'>       <b>Name  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' onclick='sortBy(\"3-A\")'>       <b>Item Type  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' onclick='sortBy(\"4-A\")'>       <b>Outlet Type 	  	  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' onclick='sortBy(\"5-A\")'>       <b>Premium Type  	  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' onclick='sortBy(\"6-A\")'>       <b>Material  	  	  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'>       <b>Brand  	  	  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' onclick='sortBy(\"10-A\")'>       <b>User  	  		  </b></th> 
					<th style='width:40px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' onclick='sortBy(\"11-A\")'>       <b>Publish  	  		  </b></th> 
					<th style='width:70px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' onclick='sortBy(\"12-D\")'>       <b>Local Price  	  	  </b></th> 
					<th style='width:70px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' onclick='sortBy(\"13-D\")'>       <b>USD Price  	  	  </b></th> 
					<th style='width:75px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' onclick='sortBy(\"14-A\")'>       <b>Uploaded  	  	  </b></th> 
					<th style='width:75px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' onclick='sortBy(\"15-A\")'>       <b>Released  	  	  </b></th> 
				</tr>";
		//REPLACE ORDER
		$table = str_replace($order_code,$this->sortingRef_eCat($order_code,'Rev_code'),$table);
		$table = str_replace($label,$this->sortingRef_eCat($order_code,'label_symbol'),$table);
		
					$x = $limit;			
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					foreach($items as $r) { 
					extract($r);
					$ptype = ($ptype=='') 	   ? 'Uncategorize' : $ptype;
					$publish = ($publish=='y') ? 'Yes' : 'No';
					$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 	
		$table.= "<tr>
				  <td $c>													$x      																		</td>
				  <td $c style='text-align:center;'>						 <a onclick=\"viewDialog('eCatalogue',$itemID)\" style='cursor:pointer;'> $num_views </a></td> 
				  <td $c>													$itemCode      																	</td>
				  <td $c style='text-align:center;'>			    		<img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 	</td>
				  <td $c style='text-align:left;padding-left:5px;'>		<a href='".HTTP_PATH."gallery/itemInfoECatalog/$ecID/$itemID' target='_blank'>		$itemName</a>  </td>
				  <td $c style='text-align:left;padding-left:5px;'>		$ptype																			</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$poutlet_status																			</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$ppremium_type																			</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$pmaterial																			</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$Brand																			</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$full_name																		</td>
				  <td $c style='text-align:center;'>						$publish																		</td>
				  <td $c style='text-align:center;'>						$UnitPrice																		</td>
				  <td $c style='text-align:center;'>						$USD_Price																		</td>
				  <td $c style='text-align:center;'>				        ". $this->convertDate('date',"$dUploaded 00:00:00") ." </td>
				  <td $c style='text-align:center;'>				        ". $this->convertDate('date',"$dReleased 00:00:00") ."	</td>
				</tr>";}
					if(!$items)
						$table.=  "<tr><td colspan='16'>No match found, please check search parameters.</td></tr>";
		$table.= "</table>";	
		
		
		$data['table'] 		= $table;
		$data['vfile']		= 'eCatalogue_report_details.php';
		$data['title']		= 'Items Preview';
	
	
		if($this->modules->access_checker()==TRUE)
		{
			$this->load->view('innerPages',$data); 
		}else{
			$data['vfile']				= 'login.php';
			$data['title']				= 'SMBi System Log-in | SMBi';
			$data['page_title']			= 'SMBi System Log-in';
			$data['meta_description']	= 'San Miguel Brewing International';
			$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
			$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
			$this->load->view('login',$data); 	
		}
	}
	
	function eCatalogue_per_Year($ecID='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(69,'REVIEW');
		
		extract($_POST);
		$filter_WHERE='';
		
		$table						= 'eCatalogue_per_Year';
		$data['vfile']				= 'eCatalogue_per_Year.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/eCatalogue_index> eCatalogue Report </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/eCatalogue_per_Year/$ecID'> eCatalogue Yearly Report </a>";
		
		//TOTAL NUMBER OF ROWS			
		
		$WHERE 	 =  $filter_WHERE;
		
		$sql = "SELECT e_catalog.title  as eTitle, YEAR(ec_items.dateAdded) as year, COUNT(ec_items.id) as eCatalogueItems, 
				avg(ec_items.UnitPrice) as avgUnitPrice, 
				avg(ec_items.USD_Price)  as avgUSDPrice	
				FROM ec_items 
				LEFT JOIN e_catalog ON e_catalog.id = ec_items.ecID
				WHERE ec_items.ecID = $ecID
				GROUP BY YEAR(ec_items.dateAdded), e_catalog.title;   
				";
		$sql   = $this->db->query($sql);
				
		$data['POST']   = $_POST;
		$data['reports']= $sql->result_array();
		$data['ecID']	= $ecID;

		$viewer = 'innerPages';	    
		if($this->modules->access_checker()==TRUE)
		{
			$this->load->view($viewer,$data); 
		}else{
			$data['vfile']				= 'login.php';
			$data['title']				= 'SMBi System Log-in | SMBi';
			$data['page_title']			= 'SMBi System Log-in';
			$data['meta_description']	= 'San Miguel Brewing International';
			$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
			$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
			$this->load->view('login',$data); 	
		}
	}
	
	function eCatalogue_per_Month($ecID='',$year)
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(69,'REVIEW');
		
		extract($_POST);
		
		$filter_WHERE='';
		
		$table						= 'eCatalogue_per_Month';
		$data['vfile']				= 'eCatalogue_per_Month.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/eCatalogue_index> eCatalogue Report </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/eCatalogue_per_Year/$ecID'> eCatalogue Yearly Report </a>";
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/eCatalogue_per_Month/$ecID/$year'> eCatalogue Monthly Report </a>";
		
		//TOTAL NUMBER OF ROWS			
		
		$WHERE 	 =  $filter_WHERE;
		
		$sql = "SELECT e_catalog.title  as eTitle, YEAR(ec_items.dateAdded) as year, DATE_FORMAT(ec_items.dateAdded,'%M') as month, MONTH(ec_items.dateAdded) AS mID,  COUNT(ec_items.id) as eCatalogueItems, 
				avg(ec_items.UnitPrice) as avgUnitPrice, 
				avg(ec_items.USD_Price)  as avgUSDPrice	
				FROM ec_items 
				LEFT JOIN e_catalog ON e_catalog.id = ec_items.ecID
				WHERE ec_items.ecID = $ecID AND YEAR(ec_items.dateAdded)=$year
				GROUP BY MONTH(ec_items.dateAdded), YEAR(ec_items.dateAdded);   
				";
		$sql   = $this->db->query($sql);
				
		$data['POST']   = $_POST;
		$data['reports']= $sql->result_array();
		$data['ecID']	= $ecID;
		$data['year']	= $year;

		$viewer = 'innerPages';	    
		if($this->modules->access_checker()==TRUE)
		{
			$this->load->view($viewer,$data); 
		}else{
			$data['vfile']				= 'login.php';
			$data['title']				= 'SMBi System Log-in | SMBi';
			$data['page_title']			= 'SMBi System Log-in';
			$data['meta_description']	= 'San Miguel Brewing International';
			$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
			$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
			$this->load->view('login',$data); 	
		}
	}
	
	function eCatalogue_items($view='',$ecID='',$year='',$month='')
	{
		$data['typeView']  = $view;
		$data['month']     = ($month=='') 	  ? 'null' : $month;
		$data['year']      = ($year=='') 	  ? 'all' :  $year;
		$data['month_in_word'] = $this->month($month);
		$data['ecID']	   = $ecID;
		
		//eCatalogue Name
		$sql = $this->db->query("SELECT title FROM e_catalog WHERE id= $ecID LIMIT 0,1");
		$sql = $sql->row();
		$data['ecTitle'] = $sql->title;
		
		//print_r($_POST);
		extract($_POST);
		$cond="WHERE ec_item_reports.ecID = $ecID";
		$having="";
		if(isset($Submit) OR isset($selpage))
		{	
			$val1 = mysql_real_escape_string($val1);
			$val1 = trim($val1);
			$val2 = mysql_real_escape_string($val2);
			$val2 = trim($val2);
			
			$condition = '';
			switch($cond1){
				case 'equal': 
					$condition = '=';
				break;
				case 'containing': 
					$condition = 'like';
				break;
				case 'greaterThan': 
					$condition = '>=';
				break;
				case 'lessThan': 
					$condition = '<=';
				break;
			}
			
			//ITEM CODE
			if($opt1=='itemCode' AND $val1!='')
			{
				if($condition=='=')
					$cond .= " AND itemCode $condition '$val1'";
				if($condition=='like')
					$cond .= " AND itemCode $condition '%$val1%'";
			}
			
			//ITEM NAME
			if($opt1=='itemName')
			{
				if($condition=='=')
					$cond .= " AND itemName $condition '$val1'";
				if($condition=='like')
					$cond .= " AND itemName $condition '%$val1%'";
			}
			
			//ITEM TYPE
			if($opt1=='itemType')
			{
				if($condition=='=')
					$cond .= " AND ptype $condition '$val1'";
				if($condition=='like')
					$cond .= " AND ptype $condition '%$val1%'";
			}
			
			//USER NAME
			if($opt1=='user_id')
			{
				if($condition=='=')
					$cond .= " AND full_name $condition '$val1'";
				if($condition=='like')
					$cond .= " AND full_name $condition '%$val1%'";
			}
			
			//PUBLISH
			if($opt1=='publish')
			{
				if($condition=='=')
					$val1 = ($val1=='yes' OR $val1=='Yes') ? 'y' : 'n';
					$cond .= " AND publish $condition '$val1'";
				if($condition=='like')
					$cond .= " AND publish $condition '%$val1%'";
			}
			
			//VIEWS
			if($opt1=='num_views' AND $val1!='' AND $condition!='like' AND is_numeric($val1)) 
				$cond .= " AND num_views $condition $val1";
			
			//LOCAL PRICE
			if($opt1=='UnitPrice' AND $val1!='' AND $condition!='like' AND is_numeric($val1)) 
				$cond .= " AND UnitPrice $condition $val1";
			
			//USD PRICE
			if($opt1=='USD_Price' AND $val1!='' AND $condition!='like' AND is_numeric($val1)) 
				$cond .= " AND USD_Price $condition $val1";
			
			//dateAdded
			if($opt1=='dateAdded' AND $val1!='' AND $condition!='like') 
				$cond .= " AND dUploaded $condition '$val1'";
				
			//dateReleased
			if($opt1=='dateReleased' AND $val1!='' AND $condition!='like') 
				$cond .= " AND dReleased $condition '$val1'";
			
			/*2ND SET*/
			switch($cond2){
				case 'equal': 
					$condition2 = '=';
				break;
				case 'containing': 
					$condition2 = 'like';
				break;
				case 'greaterThan': 
					$condition2 = '>=';
				break;
				case 'lessThan': 
					$condition2 = '<=';
				break;
			}
			
			//ITEM CODE
			if($opt2=='itemCode' AND $val2!='')
			{
				if($condition2=='=')
					$cond .= " $operator itemCode $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator itemCode $condition2 '%$val2%'";
			}
			
			//ITEM NAME
			if($opt2=='itemName' AND $val2!='')
			{
				if($condition2=='=')
					$cond .= " $operator itemName $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator itemName $condition2 '%$val2%'";
			}
			
			//ITEM TYPE
			if($opt2=='itemType' AND $val2!='')
			{
				if($condition2=='=')
					$cond .= " $operator ptype $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator ptype $condition2 '%$val2%'";
			}
			
			//USER NAME
			if($opt2=='user_id' AND $val2!='')
			{
				if($condition2=='=')
					$cond .= " $operator full_name $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator full_name $condition2 '%$val2%'";
			}
			
			//PUBLISH
			if($opt2=='publish' AND $val2!='')
			{
				if($condition2=='=')
					$val2  = ($val2=='yes' OR $val2=='Yes') ? 'y' : 'n';
					$cond .= " $operator publish $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator publish $condition2 '%$val2%'";
			}
			
			
			//LOCAL PRICE
			if($opt2=='UnitPrice' AND $val2!='' AND $condition2!='like' AND is_numeric($val2)) 
				$cond .= "$operator UnitPrice $condition2 $val2";
			
			//USD PRICE
			if($opt2=='USD_Price' AND $val2!='' AND $condition2!='like' AND is_numeric($val2)) 
				$cond .= " $operator USD_Price $condition2 $val2";
			
			//SPECIAL CASE
			if($opt1=='dateAdded' AND $val1!='' AND $condition=='>=' AND $opt2=='dateAdded' AND $val2!='' AND $condition2=='<=')
				$operator = 'AND';
			
			if($opt1=='dateReleased' AND $val1!='' AND $condition=='>=' AND $opt2=='dateReleased' AND $val2!='' AND $condition2=='<=')
				$operator = 'AND';
			
			if($opt1=='num_views' AND $val1!='' AND $condition=='>=' AND $opt2=='num_views' AND $val2!='' AND $condition2=='<=' AND $condition2!='like' AND is_numeric($val2))
				$operator = 'AND';
			
			//Views 
			$op="";
			if($opt2=='num_views' AND $val2!='' AND $condition2!='like')
				$having .= " $operator num_views $condition2 $val2";

			
			//dateAdded 
			if($opt2=='dateAdded' AND $val2!='' AND $condition2!='like') 
				$cond .= " $operator dUploaded $condition2 '$val2'";
				
			//dateReleased
			if($opt2=='dateReleased' AND $val2!='' AND $condition2!='like') 
				$cond .= " $operator dReleased $condition2 '$val2'";
		}
		
		$data['POST'] = $_POST;
		
		if(isset($Reset)){
			$data['POST'] = array();
			$cond="WHERE ec_item_reports.ecID = $ecID ";
		}
		
		//echo $cond;
		$limit =isset($selpage)? $selpage:0;
		if($view=='default'){
		$sql = "SELECT iID as itemID, ecID, itemName, ptype, itemCode, num_views, 
				item_image, dUploaded, full_name, publish,  UnitPrice, USD_Price, dReleased
				FROM ec_item_reports 
				$cond ORDER BY iID DESC";
		}
		elseif($view=='gYear')
		{
			$sql = "SELECT iID as itemID, ecID, itemName, ptype, itemCode,  num_views,
					item_image, dUploaded, full_name, publish, UnitPrice, USD_Price, dReleased 
					FROM ec_item_reports 
					$cond AND YEAR(dUploaded) = $year ORDER BY itemID DESC";
		}
		elseif($view=='gMonth')
		{
			$sql = "SELECT iID as itemID, ecID, itemName, ptype, itemCode, num_views,
					item_image, dUploaded, full_name, publish, UnitPrice, USD_Price, dReleased 
					FROM ec_item_reports 
					$cond AND MONTH(dUploaded) = $month AND YEAR(dUploaded) = $year ORDER BY itemID DESC";
		}
		
		if(isset($Submit)) $limit=0;
		//echo $sql;
	
		$ctr = $this->db->query($sql);
		$ctr = $ctr->result_array();
		$data['totrec'] = count($ctr);
		$data['limit']  = $limit;
		
		$sql 	 = $this->db->query($sql." LIMIT $limit,20");
		$items	 = $sql->result_array();
		
	
		$table= "<table id='large' cellpadding='0' cellspacing='0' border=1 style='width:120%;font-size:12px;' class='iLike_Result_Table tablesorter'>
				<thead>
				<tr>
					<th style='width:10px;text-align:center;color:white;padding:0px' bgcolor='#bb1d1d'>   					   <b>No 		 		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Item Code  	  	  </b></th> 
					<th style='width:19px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Image   	 	      </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Name  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Type  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>User  	  		  </b></th> 
					<th style='width:40px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Publish  	  		  </b></th> 
					<th style='width:40px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Views  	  		  </b></th> 
					<th style='width:70px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Local Price  	  	  </b></th> 
					<th style='width:70px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>USD Price  	  	  </b></th> 
					<th style='width:75px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Uploaded  	  	  </b></th> 
					<th style='width:75px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Released  	  	  </b></th> 
				</tr>
				</thead>
				<tbody>";
				 
					$x = $limit;			
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					foreach($items as $r) { 
					extract($r);
					$ptype = ($ptype=='') 	   ? 'Uncategorize' : $ptype;
					$publish = ($publish=='y') ? 'Yes' : 'No';
					$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 	
		$table.= "<tr>
				  <td>													$x      																		</td>
				  <td>													$itemCode      																	</td>
				  <td style='text-align:center;'>			    		<img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 	</td>
				  <td style='text-align:left;padding-left:5px;'>		<a href='".HTTP_PATH."gallery/itemInfoECatalog/$ecID/$itemID' target='_blank'>		$itemName</a>  </td>
				  <td style='text-align:left;padding-left:5px;'>		$ptype																			</td>
				  <td style='text-align:left;padding-left:5px;'>		$full_name																		</td>
				  <td style='text-align:center;'>						$publish																		</td>
				  <td style='text-align:center;'>						 <a onclick=\"viewDialog('eCatalogue',$itemID)\" style='cursor:pointer;'> $num_views </a> 																		</td>
				  <td style='text-align:center;'>						$UnitPrice																		</td>
				  <td style='text-align:center;'>						$USD_Price																		</td>
				  <td style='text-align:center;'>				        ". $dUploaded ."										</td>
				  <td style='text-align:center;'>				        ". $dReleased ."										</td>
				</tr>";}
		$table.= "</tbody>";
					if(!$items)
						$table.=  "<tr><td colspan='14'>No match found.</td></tr>";
		$table.= "</table>";	
		
		
		$data['table'] 		= $table;
		$data['vfile']		= 'eCatalogue_items.php';
		$data['title']		= 'Items Preview';
		
		//BREAD CRUMBS
		$HTTP_PATH 					 = HTTP_PATH."report/iLike";
		$data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
		$data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/eCatalogue_index>  eCatalogue Items </a>';
	
		if($this->modules->access_checker()==TRUE)
		{
			$this->load->view('innerPages',$data); 
		}else{
			$data['vfile']				= 'login.php';
			$data['title']				= 'SMBi System Log-in | SMBi';
			$data['page_title']			= 'SMBi System Log-in';
			$data['meta_description']	= 'San Miguel Brewing International';
			$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
			$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
			$this->load->view('login',$data); 	
		}
	}
	
	function eCatalogue_item_division($view='',$ecID='',$year='',$month='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(73,'REVIEW');
		//eCatalogue Name
		$sql = $this->db->query("SELECT title FROM e_catalog WHERE id= $ecID LIMIT 0,1");
		$sql = $sql->row();
		$data['ecTitle'] = $sql->title;
		
		$filter_WHERE='WHERE ';
		if($view=='default'){
			$filter_WHERE .= " ec_items.ecID = $ecID";
			$groupBy	   = " ec_items.ecID";
			$data['vType'] = $view;
		}elseif($view=='gYear'){
			$filter_WHERE .= " ec_items.ecID = $ecID AND YEAR(ec_items.dateAdded) = $year";
			$data['vType'] = $view;
			$groupBy	   = " YEAR(ec_items.ecID)";
		}elseif($view=='gMonth'){
			$filter_WHERE .= " ec_items.ecID = $ecID AND YEAR(ec_items.dateAdded) = $year AND MONTH(ec_items.dateAdded) = $month";
			$data['vType'] = $view;
			$groupBy	   = " MONTH(ec_items.ecID)";
		}	
		
		$table						= 'eCatalogue_item_division';
		$data['vfile']				= 'eCatalogue_item_division.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		$data['ecID']			= $ecID;
		$data['cyear']				= $year;	
		$data['previewType']		= $view;
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/eCatalogue_index> eCatalogue Item Division </a>';
		
		//POSM TYPE;
		$arr = '';
		$sql   = $this->db->query("SELECT DISTINCT(ec_items.POSMTypeID) as fieldValue, (SELECT typeName FROM POSM_Type WHERE POSM_Type.id = ec_items.POSMTypeID) as col, AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice, 
								   COUNT(ec_items.id) as num_ec_items, DATE_FORMAT(ec_items.dateAdded,'%M') as month, MONTH(ec_items.dateAdded) AS mID, YEAR(ec_items.dateAdded)  as year, country.id as cID 
								   FROM   ec_items 	
								   LEFT   JOIN country 		ON country.id  = ec_items.countryID
								   $filter_WHERE
								   GROUP BY ec_items.POSMTypeID, $groupBy ORDER BY YEAR(ec_items.dateAdded) DESC, MONTH(ec_items.dateAdded) DESC");
		$arr[] = array('table'=>'POSM TYPE',
					   'fld'=>'POSMTypeID',
					   'rows'=>$sql->result_array());
	
		
		//OUTLET TYPE
		$sql   = $this->db->query("SELECT DISTINCT(ec_items.OUTLETStatusID) as fieldValue, (SELECT statusName FROM OUTLET_Status WHERE OUTLET_Status.id = ec_items.OUTLETStatusID) as col, AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice, 
								   COUNT(ec_items.id) as num_ec_items, DATE_FORMAT(ec_items.dateAdded,'%M') as month, MONTH(ec_items.dateAdded) AS mID, YEAR(ec_items.dateAdded)  as year, country.id as cID 
								   FROM   ec_items 	
								   LEFT   JOIN country 		ON country.id  = ec_items.countryID
								   $filter_WHERE
								   GROUP BY ec_items.OUTLETStatusID, $groupBy ORDER BY YEAR(ec_items.dateAdded) DESC, MONTH(ec_items.dateAdded) DESC");
		$arr[] = array('table'=>'SERVICE ITEM OUTLET TYPE',
					   'fld'=>'OUTLETStatusID',
					   'rows'=>$sql->result_array());
		
		//PREMIUM ITEM TYPE
		$sql   = $this->db->query("SELECT DISTINCT(ec_items.PremiumTypeID) as fieldValue, (SELECT premiumTypeName FROM premiumItemType WHERE premiumItemType.id = ec_items.PremiumTypeID) as col, AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice, 
								   COUNT(ec_items.id) as num_ec_items, DATE_FORMAT(ec_items.dateAdded,'%M') as month, MONTH(ec_items.dateAdded) AS mID, YEAR(ec_items.dateAdded)  as year, country.id as cID 
								   FROM   ec_items 	
								   LEFT   JOIN country 		ON country.id  = ec_items.countryID
								   $filter_WHERE
								   GROUP BY ec_items.PremiumTypeID, $groupBy ORDER BY YEAR(ec_items.dateAdded) DESC, MONTH(ec_items.dateAdded) DESC");
		$arr[] = array('table'=>'PREMIUM ITEM TYPE',
					   'fld'=>'PremiumTypeID',
					   'rows'=>$sql->result_array());
		
		//MATERIAL TYPE
		$sql   = $this->db->query("SELECT DISTINCT(ec_items.MaterialTypeID) as fieldValue, (SELECT materialName FROM MATERIAL_Type WHERE MATERIAL_Type.id = ec_items.MaterialTypeID) as col, AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice, 
								   COUNT(ec_items.id) as num_ec_items, DATE_FORMAT(ec_items.dateAdded,'%M') as month, MONTH(ec_items.dateAdded) AS mID, YEAR(ec_items.dateAdded)  as year, country.id as cID 
								   FROM   ec_items 	
								   LEFT   JOIN country 		ON country.id  = ec_items.countryID
								   $filter_WHERE
								   GROUP BY ec_items.MaterialTypeID, $groupBy ORDER BY YEAR(ec_items.dateAdded) DESC, MONTH(ec_items.dateAdded) DESC");
		$arr[] = array('table'=>'MATERIAL TYPE',
						'fld'=>'MaterialTypeID',
						'rows'=>$sql->result_array());
	
		
		$data['results'] = $arr;
		
		$viewer = 'innerPages';	    
		if($this->modules->access_checker()==TRUE)
		{
			$this->load->view($viewer,$data); 
		}else{
			$data['vfile']				= 'login.php';
			$data['title']				= 'SMBi System Log-in | SMBi';
			$data['page_title']			= 'SMBi System Log-in';
			$data['meta_description']	= 'San Miguel Brewing International';
			$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
			$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
			$this->load->view('login',$data); 	
		}
	}
	
	function ecitem_distribution_Preview($view='',$ecID='',$month='',$year='',$fld='',$fld_val='')
	{
		$table='';
		$fld_val = ($fld_val=="") ? 0 : $fld_val;
		$data['typeView']  = $view;
		$data['ecID']      = $ecID;
		$data['fld']       = $fld;
		$data['fld_val']   = $fld_val;
		$data['month']     = ($month=='') 	  ? 'null' : $this->month($month);
		$data['cyear']      = ($year=='') 	  ? 'null' : $year;
		
		//eCatalogue Name
		$sql = $this->db->query("SELECT title FROM e_catalog WHERE id= $ecID LIMIT 0,1");
		$sql = $sql->row();
		$data['ecTitle'] = $sql->title;
		
		//POSM TYPE
		//echo $fld;
		switch($fld){
		case 'POSMTypeID':
			$itm_type = "(SELECT typeName FROM POSM_Type WHERE POSM_Type.id = ec_items.POSMTypeID)";
		break;		
		case 'OUTLETStatusID':
			$itm_type = "(SELECT statusName FROM OUTLET_Status WHERE OUTLET_Status.id = ec_items.OUTLETStatusID)";
		break;
		case 'PremiumTypeID':
			$itm_type = "(SELECT premiumTypeName FROM premiumItemType WHERE premiumItemType.id = ec_items.PremiumTypeID)";
		break;
		case 'MaterialTypeID':
			$itm_type = "(SELECT materialName FROM MATERIAL_Type WHERE MATERIAL_Type.id = ec_items.MaterialTypeID)";
		break;
		}
		
		$fld = "AND ec_items.$fld = '$fld_val'"; 
		
		$data['month_in_word'] = $this->month($month);
		
		//print_r($_POST);
		extract($_POST);
		$data['user_id'] = isset($user_id) ? $user_id : 0;
		$cond='';
		
		if(isset($Submit) OR isset($selpage))
		{	
			$val1 = mysql_real_escape_string($val1);
			$val2 = mysql_real_escape_string($val2);
			
			$condition = '';
			switch($cond1){
				case 'equal': 
					$condition = '=';
				break;
				case 'containing': 
					$condition = 'like';
				break;
				case 'greaterThan': 
					$condition = '>=';
				break;
				case 'lessThan': 
					$condition = '<=';
				break;
			}
			
			//ITEM CODE
			if($opt1=='itemCode' AND $val1!='')
			{
				if($condition=='=')
					$cond = " AND ec_items.itemCode $condition '$val1'";
				if($condition=='like')
					$cond = " AND ec_items.itemCode $condition '%$val1%'";
			}
			
			//ITEM NAME
			if($opt1=='itemName')
			{
				if($condition=='=')
					$cond = " AND ec_items.itemName $condition '$val1'";
				if($condition=='like')
					$cond = " AND ec_items.itemName $condition '%$val1%'";
			}
			
			//USER NAME
			if($opt1=='user_id')
			{
				if($condition=='=')
					$cond = " AND admin_users.full_name $condition '$val1'";
				if($condition=='like')
					$cond = " AND admin_users.full_name $condition '%$val1%'";
			}
			
			//PUBLISH
			if($opt1=='publish')
			{
				if($condition=='=')
					$val1 = ($val1=='yes' OR $val1=='Yes') ? 'y' : 'n';
					$cond = " AND ec_items.publish $condition '$val1'";
				if($condition=='like')
					$cond = " AND ec_items.publish $condition '%$val1%'";
			}
			
			//LOCAL PRICE
			if($opt1=='UnitPrice' AND $val1!='' AND $condition!='like') 
				$cond = " AND ec_items.UnitPrice $condition $val1";
			
			//USD PRICE
			if($opt1=='USD_Price' AND $val1!='' AND $condition!='like') 
				$cond = " AND ec_items.USD_Price $condition $val1";
			
			//dateAdded
			if($opt1=='dateAdded' AND $val1!='' AND $condition!='like') 
				$cond = " AND ec_items.dateAdded $condition '$val1'";
				
			//dateReleased
			if($opt1=='dateReleased' AND $val1!='' AND $condition!='like') 
				$cond = " AND ec_items.dateReleased $condition '$val1'";
			
			/*2ND SET*/
			switch($cond2){
				case 'equal': 
					$condition2 = '=';
				break;
				case 'containing': 
					$condition2 = 'like';
				break;
				case 'greaterThan': 
					$condition2 = '>=';
				break;
				case 'lessThan': 
					$condition2 = '<=';
				break;
			}
			
			//ITEM CODE
			if($opt2=='itemCode' AND $val2!='')
			{
				if($condition2=='=')
					$cond .= " $operator ec_items.itemCode $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator ec_items.itemCode $condition2 '%$val2%'";
			}
			
			//ITEM NAME
			if($opt2=='itemName' AND $val2!='')
			{
				if($condition2=='=')
					$cond .= " $operator ec_items.itemName $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator ec_items.itemName $condition2 '%$val2%'";
			}
			
			//USER NAME
			if($opt2=='user_id' AND $val2!='')
			{
				if($condition2=='=')
					$cond .= " $operator admin_users.full_name $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator admin_users.full_name $condition2 '%$val2%'";
			}
			
			//PUBLISH
			if($opt2=='publish' AND $val2!='')
			{
				if($condition2=='=')
					$val2  = ($val2=='yes' OR $val2=='Yes') ? 'y' : 'n';
					$cond .= " $operator ec_items.publish $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator ec_items.publish $condition2 '%$val2%'";
			}
			
			
			//LOCAL PRICE
			if($opt2=='UnitPrice' AND $val2!='') 
				$cond .= "$operator ec_items.UnitPrice $condition2 $val2";
			
			//USD PRICE
			if($opt2=='USD_Price' AND $val2!='') 
				$cond .= " $operator ec_items.USD_Price $condition2 $val2";
			
			//SPECIAL CASE
			if($opt1=='dateAdded' AND $val1!='' AND $condition=='>=' AND $opt2=='dateAdded' AND $val2!='' AND $condition2=='<=')
				$operator = 'AND';
			
			if($opt1=='dateReleased' AND $val1!='' AND $condition=='>=' AND $opt2=='dateReleased' AND $val2!='' AND $condition2=='<=')
				$operator = 'AND';
			
			//dateAdded 
			if($opt2=='dateAdded' AND $val2!='' AND $condition2!='like') 
				$cond .= " $operator ec_items.dateAdded $condition2 '$val2'";
				
			//dateReleased
			if($opt2=='dateReleased' AND $val2!='' AND $condition2!='like') 
				$cond .= " $operator ec_items.dateReleased $condition2 '$val2'";
		}
		
		$data['POST'] = $_POST;
		
		if(isset($Reset)){
			$data['POST'] = array();
			$cond='';
		}
		
		//echo $cond;
		$limit =isset($selpage)? $selpage:0;
		
		if($view=='default'){
			
			$sql = "SELECT ec_items.id as itemID, itemName, $itm_type as ptype, itemCode, 
					(SELECT image FROM ecitems_images WHERE defaultStatus = 1 AND itemID = ec_items.id) as item_image, ec_items.dateAdded as dUploaded, full_name, publish,
					UnitPrice, USD_Price, ec_items.dateReleased as dReleased
					FROM ec_items LEFT JOIN admin_users ON admin_users.id = ec_items.user_id
					WHERE ec_items.ecID = $ecID $fld $cond ORDER BY ec_items.id DESC";
		}if($view=='gYear'){
			$sql = "SELECT ec_items.id as itemID, itemName, $itm_type as ptype, itemCode, 
					(SELECT image FROM ecitems_images WHERE defaultStatus = 1 AND itemID = ec_items.id) as item_image, ec_items.dateAdded as dUploaded, full_name, publish,
					UnitPrice, USD_Price, ec_items.dateReleased as dReleased
					FROM ec_items LEFT JOIN admin_users ON admin_users.id = ec_items.user_id
					WHERE ec_items.ecID = $ecID $fld $cond AND YEAR(ec_items.dateAdded)=$year ORDER BY ec_items.id DESC";
		}if($view=='gMonth'){
			$sql = "SELECT ec_items.id as itemID, itemName, $itm_type as ptype, itemCode, 
					(SELECT image FROM ecitems_images WHERE defaultStatus = 1 AND itemID = ec_items.id) as item_image, ec_items.dateAdded as dUploaded, full_name, publish,
					UnitPrice, USD_Price, ec_items.dateReleased as dReleased
					FROM ec_items LEFT JOIN admin_users ON admin_users.id = ec_items.user_id
					WHERE ec_items.ecID = $ecID $fld $cond AND YEAR(ec_items.dateAdded)=$year AND MONTH(ec_items.dateAdded)=$month ORDER BY ec_items.id DESC";
		}
		
		$ctr = $this->db->query($sql);
		$ctr = $ctr->result_array();
		$data['totrec'] = count($ctr);
		$data['limit']  = $limit;
		
		$sql 	 = $this->db->query($sql." LIMIT $limit,20");
		$items	 = $sql->result_array();
	
		//print_r($items);
			
		$table = "<table id='large' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:12px;' class='iLike_Result_Table tablesorter'>
				<thead>
				<tr>
					<th style='width:10px;text-align:center;color:white;padding:0px' bgcolor='#bb1d1d'>   					   <b>No 		 		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Item Code  	  	  </b></th> 
					<th style='width:19px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Image   	 	      </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Name  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Type  	  		  </b></th> 
					<th style='width:30px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>User  	  		  </b></th> 
					<th style='width:30px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Publish  	  		  </b></th> 
					<th style='width:70px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Local Price  	  		  </b></th> 
					<th style='width:70px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>USD Price  	  		  </b></th> 
					<th style='width:75px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Uploaded  	  </b></th> 
					<th style='width:75px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Released  	  </b></th> 
				</tr>
				</thead>
				<tbody>";
				 
					$x = 0;			
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					foreach($items as $r) { 
					extract($r);
					$ptype = ($ptype=='') 	   ? 'Uncategorize' : $ptype;
					$publish = ($publish=='y') ? 'Yes' : 'No';
					$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 	
		$table .="<tr>
				  <td >													$x      																		</td>
				  <td >													$itemCode      																	</td>
				  <td  style='text-align:center;'>			    		<img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 			</td>
				  <td  style='text-align:left;padding-left:5px;'>		<a href='".HTTP_PATH."gallery/itemInfo2/$itemID' target='_blank'>		$itemName</a>   </td>
				  <td  style='text-align:left;padding-left:5px;'>		$ptype											</td>
				   <td  style='text-align:center;'>						$full_name										</td>
				  <td  style='text-align:center;'>						$publish										</td>
				  <td style='text-align:center;'>						$UnitPrice																		</td>
				  <td style='text-align:center;'>						$USD_Price																		</td>
				  <td style='text-align:center;'>				        ". $dUploaded ."										</td>
				  <td style='text-align:center;'>				        ". $dReleased ."										</td>
				</tr>";}
		$table .="</tbody>";
					if(!$items)
						$table .= "<tr><td colspan='11'>No match found.</td></tr>";
		$table .="</table>";
		
		$data['POST']		= $_POST;
		$data['table'] 		= $table;
		$data['vfile']		= 'ecitem_distribution_Preview.php';
		$data['title']		= 'Items Preview';
		
		//BREAD CRUMBS
		$HTTP_PATH 					 = HTTP_PATH."report/iLike";
		$data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
		$data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/eCatalogue_index> eCatalogue Item Division </a>';
		
	
		if($this->modules->access_checker()==TRUE)
		{
			$this->load->view('innerPages',$data); 
		}else{
			$data['vfile']				= 'login.php';
			$data['title']				= 'SMBi System Log-in | SMBi';
			$data['page_title']			= 'SMBi System Log-in';
			$data['meta_description']	= 'San Miguel Brewing International';
			$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
			$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
			$this->load->view('login',$data); 	
		}
	}
	
	function BU_activeness()
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(69,'REVIEW');
		
		extract($_POST);
		
		$filter_WHERE='';
		if($_SESSION['super_admin']!='y'){
			$filter_WHERE 		= "WHERE items.countryID =".$_SESSION['countryID'] ." AND YEAR(items.dateAdded) = ".DATE("Y");
			$data['cyear']		= isset($cyear) ? $cyear : DATE("Y");
		}else{
			$filter_WHERE 		= "WHERE YEAR(items.dateAdded) = ".DATE("Y");
			$data['cyear']		= isset($cyear) ? $cyear : DATE("Y");;
		}
		
		$table						= 'BU_activeness';
		$data['vfile']				= 'BU_activeness.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/BU_activeness> Activeness of Business Units </a>';
		
		//TOTAL NUMBER OF ROWS			
		
		$WHERE 	 =  $filter_WHERE;
		
		$data['per_country'] = TRUE;
		$sql   = $this->db->query("SELECT country.countryName AS cName, country.id AS cID,  
								   COUNT(items.id) as num_items, target, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year 
								   FROM   items 		 
								   LEFT   JOIN country 		ON country.id  = items.countryID 
								   LEFT   JOIN target_items ON target_items.countryID = items.countryID 
								   $WHERE
								   GROUP BY country.id ORDER BY YEAR(items.dateAdded) DESC");
		
		if(isset($filter))
		{   
			$data['per_country'] = FALSE;
			$WHERE 	 = '';
			if($countryID!='' AND $countryID!='all') $WHERE  .=  " items.countryID = $countryID AND";
			if($countryID=='all')					 $WHERE   =  "";
			if($cyear!=''  AND $cyear!='all') 	 	 $WHERE  .=  " YEAR(items.dateAdded) = $cyear AND";		
			if($WHERE!='')  $WHERE  =  substr("WHERE ".$WHERE,0,-3);
			
			$sql   = $this->db->query("SELECT country.countryName AS cName, country.id AS cID,  
									   COUNT(items.id) as num_items, target, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year 
									   FROM items 		 
									   LEFT   JOIN country 		ON country.id  = items.countryID 
									   LEFT   JOIN target_items ON target_items.countryID = items.countryID 
									   $WHERE
									   GROUP BY cName, YEAR(items.dateAdded) ORDER BY YEAR(items.dateAdded) DESC");
		}
		
		$data['POST'] = $_POST;
			
		
		$data['reports']   = $sql->result_array();
		

		$viewer = 'innerPages';	    
		if($this->modules->access_checker()==TRUE)
		{
			$this->load->view($viewer,$data); 
		}else{
			$data['vfile']				= 'login.php';
			$data['title']				= 'SMBi System Log-in | SMBi';
			$data['page_title']			= 'SMBi System Log-in';
			$data['meta_description']	= 'San Miguel Brewing International';
			$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
			$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
			$this->load->view('login',$data); 	
		}
	}
	
	function BU_activeness_per_Year($cID='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(69,'REVIEW');
		$c='';
		
		if($cID!=''){
			$data['countryID'] = $cID;
			$c = "WHERE country.id = $cID"; 
		}
		if($cID==0){
			$c = "WHERE country.id!=0";
		}
		
		$filter_WHERE	= $c;
		
		$table						= 'BU_activeness';
		$data['vfile']				= 'BU_activeness_per_Year.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					 = HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/BU_activeness_index> Activeness of Business Units Index</a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/BU_activeness_per_Year/$cID'> Activeness of Business Units per Year</a>";
		//TOTAL NUMBER OF ROWS			
		
		extract($_POST);
		$WHERE 	 =  $filter_WHERE;
		
		$data['per_country'] = TRUE;
		
		$sql   = $this->db->query("SELECT country.countryName AS cName, country.id AS cID,  
								   COUNT(items.id) as num_items, target, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year,
								   AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice
								   FROM   items 		 
								   LEFT   JOIN country 		ON country.id  = items.countryID 
								   LEFT   JOIN target_items ON target_items.countryID = items.countryID 
								   $WHERE
								   GROUP BY YEAR(items.dateAdded) ORDER BY YEAR(items.dateAdded) DESC");

		if(isset($filter))
		{   
			$data['per_country'] = FALSE;
			$data['countryID'] = $countryID;
			$WHERE 	 = '';
			if($countryID!='' AND $countryID!='all') $WHERE  .=  " items.countryID = $countryID AND";
			if($countryID=='all')					 $WHERE   =  "";
			if($cyear!=''  AND $cyear!='all') 	 	 $WHERE  .=  " YEAR(items.dateAdded) = $cyear AND";
			/*
			if($fmonth!='' AND $fmonth!='all' AND $tmonth!='' AND $tmonth!='all') 	 	 
					$WHERE  .=  " MONTH(items.dateAdded) >= $fmonth AND  MONTH(items.dateAdded) <= $tmonth AND";
			*/
			if($WHERE!='')  $WHERE  =  substr("WHERE ".$WHERE,0,-3);
			
			$sql   = $this->db->query("SELECT country.countryName AS cName, country.id AS cID,  
									   COUNT(items.id) as num_items, target, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year,
									   AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice
									   FROM items 		 
									   LEFT   JOIN country 		ON country.id  = items.countryID 
									   LEFT   JOIN target_items ON target_items.countryID = items.countryID 
									   $WHERE
									   GROUP BY YEAR(items.dateAdded) ORDER BY YEAR(items.dateAdded) DESC");
		}
		
		$data['POST'] = $_POST;
		$data['reports']   = $sql->result_array();
		

		$viewer = 'innerPages';	    
		if($this->modules->access_checker()==TRUE)
		{
			$this->load->view($viewer,$data); 
		}else{
			$data['vfile']				= 'login.php';
			$data['title']				= 'SMBi System Log-in | SMBi';
			$data['page_title']			= 'SMBi System Log-in';
			$data['meta_description']	= 'San Miguel Brewing International';
			$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
			$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
			$this->load->view('login',$data); 	
		}
	}
	
	function BU_activeness_per_Month($cID='',$year='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(69,'REVIEW');
		$c='';
		
		
		if($cID!=''){
			$data['countryID'] = $cID;
			$c = " country.id = $cID AND"; 
		}
		if($cID==0){
			$c = " country.id!=0 AND";
		}
		
		$filter_WHERE	= "";
		$filter_WHERE 	= "WHERE $c YEAR(items.dateAdded) = ".$year;
		$data['cyear']	= $year;
		//die();
		
		$table						= 'BU_activeness';
		$data['vfile']				= 'BU_activeness_per_Month.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					 = HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/BU_activeness_index> Activeness of Business Units Index</a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/BU_activeness_per_Year/$cID'> Activeness of Business Units per Year</a>";
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/BU_activeness_per_Month/$cID/$year'> Activeness of Business Units per Month</a>";
		
		//TOTAL NUMBER OF ROWS			
		
		extract($_POST);
		$WHERE 	 =  $filter_WHERE;
		
		$data['per_country'] = TRUE;
		$sql   = $this->db->query("SELECT country.countryName AS cName, country.id AS cID,  
								   COUNT(items.id) as num_items, target, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year,
								   AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice
								   FROM   items 		 
								   LEFT   JOIN country 		ON country.id  = items.countryID 
								   LEFT   JOIN target_items ON target_items.countryID = items.countryID 
								   $WHERE
								   GROUP BY MONTH(items.dateAdded), YEAR(items.dateAdded) ORDER BY MONTH(items.dateAdded) DESC, YEAR(items.dateAdded) DESC");

		if(isset($filter))
		{   
			$data['per_country'] = FALSE;
			$data['countryID'] = $countryID;
			$WHERE 	 = '';
			if($countryID!='' AND $countryID!='all') $WHERE  .=  " items.countryID = $countryID AND";
			if($countryID=='all')					 $WHERE   =  "";
			if($cyear!=''  AND $cyear!='all') 	 	 $WHERE  .=  " YEAR(items.dateAdded) = $cyear AND";
			if($fmonth!='' AND $fmonth!='all' AND $tmonth!='' AND $tmonth!='all') 	 	 
					$WHERE  .=  " MONTH(items.dateAdded) >= $fmonth AND  MONTH(items.dateAdded) <= $tmonth AND";
		
			if($WHERE!='')  $WHERE  =  substr("WHERE ".$WHERE,0,-3);
			
			$sql   = $this->db->query("SELECT country.countryName AS cName, country.id AS cID,  
									   COUNT(items.id) as num_items, target, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year,
									   AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice
									   FROM items 		 
									   LEFT   JOIN country 		ON country.id  = items.countryID 
									   LEFT   JOIN target_items ON target_items.countryID = items.countryID 
									   $WHERE
									   GROUP BY MONTH(items.dateAdded), YEAR(items.dateAdded) ORDER BY YEAR(items.dateAdded) DESC");
		}
		
		$data['POST'] = $_POST;
		
		$data['reports']   = $sql->result_array();
		

		$viewer = 'innerPages';	    
		if($this->modules->access_checker()==TRUE)
		{
			$this->load->view($viewer,$data); 
		}else{
			$data['vfile']				= 'login.php';
			$data['title']				= 'SMBi System Log-in | SMBi';
			$data['page_title']			= 'SMBi System Log-in';
			$data['meta_description']	= 'San Miguel Brewing International';
			$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
			$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
			$this->load->view('login',$data); 	
		}
	}
	
	function BU_activeness_Users()
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(68,'REVIEW');
		
		//USER MANUAL
	    $data['USER_MANUAL'] = $this->modules->user_manual(43);
		
		$table						= 'BU_activeness_Users';
		$data['vfile']				= 'BU_activeness_Users.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/BU_activeness_Users> Activeness of Business Units Index </a>';
		
		//TOTAL NUMBER OF ROWS			
		
		extract($_POST);
		$WHERE="";
		if($_SESSION['super_admin']!='y' AND $_SESSION['countryID']!=0){
			$WHERE 		= " WHERE cID =".$_SESSION['countryID'] ." AND forArchiving='n' AND forPurging='n' AND (itemAge <".$this->modules->itemsAge().") AND";
		}else{
			$WHERE 		= " WHERE cID != 0 AND forArchiving='n' AND forPurging='n' AND (itemAge <".$this->modules->itemsAge().") AND";
		}
		
		extract($_POST);
		
		$data['quarterStr'] = "";
		$data['DateFrom'] = "null";
		$data['DateTo'] = "null";
		if((isset($DateFrom) AND isset($DateTo)) AND ($DateFrom!='' AND $DateTo!='') AND !isset($Reset)){
			$WHERE   .= " (dUploaded >= '$DateFrom' AND dUploaded <= '$DateTo') ";
			$data['DateFrom'] = $DateFrom;
			$data['DateTo']   = $DateTo;
			//DETECT QUARTER STRING
			if($DateFrom==$this->perQuarterMonths('prevMonth') & $DateTo==$this->perQuarterMonths('nextMonth'))
			 $data['quarterStr'] = $this->quaterStr('condition');
			//PREVIOUS QUARTER
			if($DateFrom==$this->setPreviousQTR($DateFrom,'prevMonth') & $DateTo==$this->setPreviousQTR($DateTo,'nextMonth'))
			 $data['quarterStr'] = $this->SetPrevQuaterStr($DateFrom);
			//PREVIOUS QUARTER
			if($DateFrom==$this->minMaxDate('prevMonth') & $DateTo==$this->minMaxDate('nextMonth'))
			 $data['quarterStr'] = $this->minMaxDate('condition');
		}else{
			$WHERE   		   .= $this->minMaxDate('filter');
			$data['quarterStr'] = $this->minMaxDate('condition');
			$data['DateFrom']   = $this->minMaxDate('prevMonth');
			$data['DateTo']     = $this->minMaxDate('nextMonth');
		}
	
		$HAVING="";
		$cond = "";
		if(isset($Submit) OR isset($selpage))
		{	
			$val1 = mysql_real_escape_string($val1);
			$val1 = trim($val1);
			$val2 = mysql_real_escape_string($val2);
			$val2 = trim($val2);
			
			$condition = '';
			switch($cond1){
				case 'equal': 
					$condition = '=';
				break;
				case 'containing': 
					$condition = 'like';
				break;
				case 'in': 
					$condition = 'in';
				break;
				case 'greaterThan': 
					$condition = '>=';
				break;
				case 'lessThan': 
					$condition = '<=';
				break;
				case 'between': 
					$condition = 'between';
				break;
			}
			
			if(($opt1=='pcountry' OR $opt1=='full_name') AND $val1!='')
			{
				if($condition=='=')
					$cond = " AND $opt1 $condition '$val1'";
				if($condition=='like')
					$cond = " AND $opt1 $condition '%$val1%'";
				if($condition=='in')
					$cond = " AND $opt1 $condition ('" . str_replace(",", "','", $val1) . "')";
			}
			
			//PUBLISH
			$field="";
			if($opt1=='Uploaded_Items')
				$field=" COUNT(itemID) ";
			if($opt1=='Publish')
				$field=" SUM(publish='y') ";
			if($opt1=='Not_Yet_Publish')
				$field=" SUM(publish='n' and disapprove='n')";
			if($opt1=='Disapprove')
				$field=" SUM(disapprove='y')";
			if($opt1=='AVG_Local_Price')
				$field=" ROUND(AVG(UnitPrice),2)";
			if($opt1=='AVG_USD_Price')
				$field=" ROUND(AVG(USD_Price),2)";
			if(($opt1=='Uploaded_Items' OR $opt1=='Publish' OR $opt1=='Not_Yet_Publish' OR  $opt1=='Disapprove' OR  $opt1=='AVG_Local_Price' OR $opt1=='AVG_USD_Price') AND $val1!='' AND is_numeric($val1))
			{				
				if($condition=='=')
					$HAVING = " HAVING $field $condition $val1";
				if($condition=='>=')
					$HAVING = " HAVING $field $condition $val1";
				if($condition=='<=')
					$HAVING = " HAVING $field $condition $val1";
			}
			
			if($condition=='between' & $this->checkStr($val1)==TRUE)
				$HAVING = " HAVING $field $condition ".stripslashes($val1);
			
			/*2ND SET*/
			switch($cond2){
				case 'equal': 
					$condition2 = '=';
				break;
				case 'containing': 
					$condition2 = 'like';
				break;
				case 'in': 
					$condition2 = 'in';
				break;
				case 'greaterThan': 
					$condition2 = '>=';
				break;
				case 'lessThan': 
					$condition2 = '<=';
				break;
				case 'between': 
					$condition2 = 'between';
				break;
			}
			
			if(($opt2=='pcountry' OR $opt2=='full_name') AND $val2!='')
			{
				if($condition2=='=')
					$cond .= "  $operator $opt2 $condition2 '$val2'";
				if($condition2=='like')
					$cond .= "  $operator $opt2 $condition2 '%$val2%'";
				if($condition2=='in')
					$cond .= "  $operator $opt2 $condition2 ('" . str_replace(",", "','", $val2) . "')";
			}
			
			
			$field="";
			if($opt2=='Uploaded_Items')
				$field=" COUNT(itemID) ";
			if($opt2=='Publish')
				$field=" SUM(publish='y') ";
			if($opt2=='Not_Yet_Publish')
				$field=" SUM(publish='n' and disapprove='n') ";
			if($opt2=='Disapprove')
				$field=" SUM(disapprove='y') ";
			if($opt2=='AVG_Local_Price')
				$field=" ROUND(AVG(UnitPrice),2) ";
			if($opt2=='AVG_USD_Price')
				$field=" ROUND(AVG(USD_Price),2) ";
			if(($opt2=='Uploaded_Items' OR $opt2=='Publish' OR $opt2=='Not_Yet_Publish' OR  $opt2=='Disapprove' OR  $opt2=='AVG_Local_Price' OR $opt2=='AVG_USD_Price') AND $val2!='' AND (is_numeric($val2) OR $this->checkStr($val2)==TRUE))
			{				
				$HAVING .= ($HAVING=="") ? "HAVING" : "$operator";
				if($condition2=='=')
					$HAVING .= " $field $condition2 $val2";
				if($condition2=='>=')
					$HAVING .= " $field $condition2 $val2";
				if($condition2=='<=')
					$HAVING .= " $field $condition2 $val2";
			}
			
			if($condition2=='between' & $this->checkStr($val2)==TRUE)
					$HAVING .= " $field $condition2 ".stripslashes($val2);
			
			$cond = ($cond=="") ? "" : "  $cond ";
			$WHERE  .=  " $cond";
		}
		
		$data['POST']      = $_POST;
		if(isset($Reset)){
			$data['POST'] = array();
			$data['quarterStr'] = $this->minMaxDate('condition');
			$data['DateFrom'] = $this->minMaxDate('prevMonth');
			$data['DateTo']   = $this->minMaxDate('nextMonth');
		}
		
		
		//TOTAL NUMBER OF ROWS					
		$data['per_country'] = TRUE;
		
		$sql   = "SELECT cID, userID, pcountry, full_name, COUNT(itemID) AS num_items, SUM(publish='y') AS publish_items, SUM(publish='n' AND disapprove='n') AS not_publish, SUM(disapprove='y') AS disapprove_items, 
				  AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice
				  FROM item_db_reports $WHERE GROUP BY userID $HAVING ORDER BY pcountry ASC";
		
		$sql_csv = "SELECT pcountry as Country, full_name as User, COUNT(itemID) AS Uploaded_Items, SUM(publish='y') AS Publish_Items, SUM(publish='n' AND disapprove='n') AS Not_Publish, SUM(disapprove='y') AS Disapprove_items,
				    AVG(UnitPrice) AS AVG_Local_Price, AVG(USD_Price) AS AVG_US_Price
				    FROM item_db_reports $WHERE GROUP BY userID $HAVING ORDER BY pcountry ASC";
		
		//generate csv file
		$this->generateCSVFile('item_views',$sql_csv,"SMBi_BU_Activeness_Users_Index".$this->reportCode().".csv");
		$data['csvFile']			= "SMBi_BU_Activeness_Users_Index".$this->reportCode().".csv";
		
		$sql   = $this->db->query($sql);
		
		$data['reports']   = $sql->result_array();
		
		$viewer = 'innerPages';	    
		if($this->modules->access_checker()==TRUE)
		{
			$this->load->view($viewer,$data); 
		}else{
			$data['vfile']				= 'login.php';
			$data['title']				= 'SMBi System Log-in | SMBi';
			$data['page_title']			= 'SMBi System Log-in';
			$data['meta_description']	= 'San Miguel Brewing International';
			$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
			$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
			$this->load->view('login',$data); 	
		}
	}
	
	function BU_activeness_Users_details($cID='',$userID='',$DateFrom='',$DateTo='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(69,'REVIEW');
		
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(43);
		
		
		//$votingCampaignID 	= $id;
		$csv = "";
		$table						= 'BU_activeness_Users_details';
		$data['vfile']				= 'BU_activeness_Users_details.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/BU_activeness_Users> BU Activeness of Users Index </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/BU_activeness_Users_details/$cID/$userID/$DateFrom/$DateTo'> BU Activeness of Users in Details </a>";
		
		//COUNTRY
		$data['country_name'] = "Country: All Country";
		if($cID!=0){
			$sql = $this->db->query("SELECT countryName FROM country WHERE id = $cID LIMIT 0,1");
			$row = $sql->row();
			$data['country_name'] = "Country: ".$row->countryName;
			
			//FULL NAME
			$sql = $this->db->query("SELECT full_name FROM admin_users WHERE id = $userID LIMIT 0,1");
			$row = $sql->row();
			$data['full_name'] = "<i>".$row->full_name."</i>";
		}
		
		//TOTAL NUMBER OF ROWS	
		$sort='n';
		extract($_POST);
		$data['cID']   = $cID;
		$data['userID']= $userID;
		$cond="";
		$having="";
		$limit =isset($selpage)? $selpage:0;
		
		$WHERE  = " WHERE forArchiving='n' AND forPurging='n' AND (itemAge <".$this->modules->itemsAge().") AND";
		if($cID!=0)
			$WHERE  = " WHERE userID=$userID AND forArchiving='n' AND forPurging='n' AND (itemAge <".$this->modules->itemsAge().") AND";
			
		$data['quarterStr'] = "";
		$data['DateFrom'] = "";
		$data['DateTo'] = "";
		//print_r($_POST);
		if((isset($DateFrom) AND isset($DateTo)) AND ($DateFrom!='' AND $DateTo!='') AND !isset($Reset)){
			$WHERE   .= " (dUploaded >= '$DateFrom' AND dUploaded <= '$DateTo') ";
			$data['DateFrom'] = $DateFrom;
			$data['DateTo']   = $DateTo;
			//DETECT QUARTER STRING
			if($DateFrom==$this->perQuarterMonths('prevMonth') & $DateTo==$this->perQuarterMonths('nextMonth'))
			 $data['quarterStr'] = $this->quaterStr('condition');
			//PREVIOUS QUARTER
			if($DateFrom==$this->setPreviousQTR($DateFrom,'prevMonth') & $DateTo==$this->setPreviousQTR($DateTo,'nextMonth'))
			 $data['quarterStr'] = $this->SetPrevQuaterStr($DateFrom);
			//PREVIOUS QUARTER
			if($DateFrom==$this->minMaxDate('prevMonth') & $DateTo==$this->minMaxDate('nextMonth'))
			 $data['quarterStr'] = $this->minMaxDate('condition');
		}elseif(!$_POST){
			$WHERE   		   .= $this->minMaxDate('filter');
			$data['quarterStr'] = $this->minMaxDate('condition');
			$data['DateFrom']   = $this->minMaxDate('prevMonth');
			$data['DateTo']     = $this->minMaxDate('nextMonth');
		}
		
		if($_POST AND $DateFrom=='' AND $DateTo=='') $WHERE = substr($WHERE,0,-3);
		
		if(isset($Submit) OR isset($selpage) AND !isset($Reset))
		{	
			$val1 = mysql_real_escape_string($val1);
			$val1 = trim($val1);
			$val2 = mysql_real_escape_string($val2);
			$val2 = trim($val2);
			
			$condition = '';
			switch($cond1){
				case 'equal': 
					$condition = '=';
				break;
				case 'containing': 
					$condition = 'like';
				break;
				case 'in': 
					$condition = 'in';
				break;
				case 'greaterThan': 
					$condition = '>=';
				break;
				case 'lessThan': 
					$condition = '<=';
				break;
				case 'between': 
					$condition = 'between';
				break;
			}
			
			
			if(($opt1=='itemCode' OR $opt1=='itemName' OR $opt1=='pstatus' OR $opt1=='ptype' OR $opt1=='poutlet_status' OR $opt1=='ppremium_type' OR $opt1=='pmaterial' OR $opt1=='pbrand' OR $opt1=='full_name' OR $opt1=='pcountry' OR $opt1=='price_rangeName') AND $val1!='')
			{
				if($condition=='=')
					$cond = "  $opt1 $condition '$val1'";
				if($condition=='>=')
					$cond = "  $opt1 $condition '$val1'";
				if($condition=='<=')
					$cond = "  $opt1 $condition '$val1'";
				if($condition=='like')
					$cond = "  $opt1 $condition '%$val1%'";
				if($condition=='in')
					$cond = "  $opt1 $condition ('" . str_replace(",", "','", $val1) . "')";
				if($condition=='between' & $this->checkStr($val1)==TRUE)
					$cond = "  $opt1 $condition ".stripslashes($val1);
			}
			
			//PUBLISH
			if(($opt1=='publish' OR $opt1=='disapprove') AND $val1!='')
			{
				if($condition=='=')
					$val1 = ($val1=='yes' OR $val1=='Yes') ? 'y' : 'n';
					$cond = "  $opt1 $condition '$val1'";
				if($condition=='like')
					$cond = "  $opt1 $condition '%$val1%'";
			}
			
			
			//VIEWS
			if(($opt1=='num_views' OR $opt1=='UnitPrice' OR $opt1=='likes' OR $opt1=='wants' OR $opt1=='USD_Price') AND $val1!='' AND $condition!='like'){ 
				if($condition=='in' AND (is_numeric($val1) OR strpos($val1,',')==TRUE))
					$cond = "  $opt1 $condition ('" . str_replace(",", "','", $val1) . "')";
				elseif($condition!='in' AND is_numeric($val1))
					$cond = "  $opt1 $condition $val1";
				elseif($condition=='between' & $this->checkStr($val1)==TRUE)
					$cond = "  $opt1 $condition ".stripslashes($val1);
			}
		
			//dateReleased
			if($opt1=='dateAdded' AND $val1!='' AND $condition!='like' AND $condition!='between') 
				$cond = "  dateAdded $condition '$val1'";
			if($opt1=='dateAdded' AND $val1!='' AND $condition=='between' & $this->checkStr($val1)==TRUE) 
				$cond = "  dateAdded $condition ".stripslashes($val1);
			
			/*2ND SET*/
			switch($cond2){
				case 'equal': 
					$condition2 = '=';
				break;
				case 'containing': 
					$condition2 = 'like';
				break;
				case 'in': 
					$condition2 = 'in';
				break;
				case 'greaterThan': 
					$condition2 = '>=';
				break;
				case 'lessThan': 
					$condition2 = '<=';
				break;
				case 'between': 
					$condition2 = 'between';
				break;
			}
			
			//CHECK IF VAL1 IS SET
			if($val1=="")  $condition2 ="";
			
			if(($opt2=='itemCode' OR $opt2=='itemName' OR $opt2=='pstatus' OR $opt2=='ptype' OR $opt2=='poutlet_status' OR $opt2=='ppremium_type' OR $opt2=='pmaterial' OR $opt2=='pbrand' OR $opt2=='full_name' OR $opt2=='pcountry' OR $opt2=='price_rangeName') AND $val2!='')
			{
				if($condition2=='=')
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator $opt2 $condition2 '%$val2%'";
				if($condition2=='>=')
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='<=')
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='in')
					$cond .= " $operator $opt2 $condition2 ('" . str_replace(",", "','", $val2) . "')";
				if($condition2=='between'  & $this->checkStr($val2)==TRUE)
					$cond .= "  $operator $opt2 $condition2 ".stripslashes($val2);
			}
			
			
			//PUBLISH
			if(($opt2=='publish' OR $opt2=='disapprove') AND $val2!='')
			{
				if($condition2=='=')
					$val2 = ($val2=='yes' OR $val2=='Yes') ? 'y' : 'n';
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator $opt2 $condition2 '%$val2%'";
			}
			
			//LOCAL PRICE
			//VIEWS
			if(($opt2=='num_views' OR $opt2=='UnitPrice' OR $opt2=='likes' OR $opt2=='wants' OR $opt2=='USD_Price') AND $val2!='' AND $condition2!='like'){ 
				if($condition2=='in' AND (is_numeric($val2) OR strpos($val2,',')==TRUE))
					$cond .= " $operator $opt2 $condition2 ('" . str_replace(",", "','", $val2) . "')";
				elseif($condition2!='in' AND is_numeric($val2))
					$cond .= " $operator $opt2 $condition2 $val2";
				elseif($condition2=='between' & $this->checkStr($val2)==TRUE)
					$cond .= "  $operator $opt2 $condition2 ".stripslashes($val2);
			}
	
				
			//dUploaded
			if($opt2=='dateAdded' AND $val2!='' AND $condition2!='like' AND $condition2!='between') 
				$cond .= " $operator dateAdded $condition2 '$val2'";
			if($opt2=='dateAdded' AND $val2!='' AND $condition2=='between' & $this->checkStr($val2)==TRUE) 
				$cond .= "  $operator dateAdded $condition2 ".stripslashes($val2);
				
			$cond = ($cond=="") ? "" : " AND ($cond) ";
			$WHERE  .=  " $cond ";
		}
		
		
		//IF THEIR NO POSSIBLE RESULT
		$valid_query=TRUE;
		if(isset($Submit)){
			if($cond=="" AND ($val1!='' OR $val2!='')){ 
			$valid_query=FALSE;
			$WHERE = "WHERE itemID=0";
			}
		}
		
		$data['POST']      = $_POST;
		if(isset($Reset)){
			$WHERE = substr($WHERE,0,-3);
			$data['POST'] = array();
			$data['quarterStr'] = $this->minMaxDate('condition');
			$data['DateFrom'] = $this->minMaxDate('prevMonth');
			$data['DateTo']   = $this->minMaxDate('nextMonth');
		}
		
		//ORDER
		$ORDER 			= $this->sortingRef('0-D','query');
		$data['order']  = $this->sortingRef('0-D','Orig_code');
		$order_code 	= '0-D';
		$label 			= "Views";
		if(isset($order)){
			$ORDER = $this->sortingRef($order,'query');
			$data['order'] = $this->sortingRef($order,'Orig_code');
			$order_code = $order;
			$label = $this->sortingRef($order,'label');
		}	
		
		$sql = "SELECT itemID, num_views, likes, wants, itemCode, item_image, itemName, pstatus, ptype, poutlet_status, ppremium_type, pmaterial, pbrand, full_name, cName, publish, UnitPrice, USD_Price,price_rangeName, dUploaded, dReleased, disapprove
				FROM item_db_reports		 
				$WHERE ORDER BY $ORDER";
				
		$sql_csv = $this->db->query("SELECT cName as Country, num_views as Views, likes as Likes, wants as Wants, itemCode as Item_Code, itemName as Item_Name, pstatus as Status, ptype as Type, poutlet_status as Outlet_Status, 
									ppremium_type as Premium_Type, pmaterial as Material_Type, pbrand as Brand, full_name as User, publish as Publish, disapprove as Disapprove, UnitPrice, USD_Price, price_rangeName as Price_Category, dUploaded as Date_Uploaded, dReleased as Date_Released
									FROM item_db_reports		 
									$WHERE ORDER BY $ORDER");
		
		//generate csv file
		$csv  = "Activeness of Users in Details\n";
		$csv .= "No, User, Views, Likes, Wants, Item Code,  Item Name, Status, Type, Outlet Status, Premium Type, Material Type, Brand, Country , Publish, Disapprove, UnitPrice, USD Price, Price Category, Date Uploaded, Date Released\n";
		$sql_csv = $sql_csv->result_array(); $x=0;
		foreach($sql_csv as $s)
		{ extract($s); $x++;
		  $Publish 	  	 = ($Publish=='y')    ? "Yes" : "No";
		  $Disapprove 	 = ($Disapprove=='y') ? "Yes" : "No";
		  $Date_Uploaded =  $this->convertDate('date',"$Date_Uploaded 00:00:00");
		  $Date_Released =  $this->convertDate('date',"$Date_Released 00:00:00");
		  $Item_Name     =  str_replace(",","-",$Item_Name);
		  $csv .= "$x, $User, $Views, $Likes, $Wants, $Item_Code, $Item_Name, $Status, $Type, $Outlet_Status, $Premium_Type, $Material_Type, $Brand, $Country, $Publish, $Disapprove, $UnitPrice, $USD_Price, $Price_Category, $Date_Uploaded, $Date_Released\n";
		}
	    write_file(getcwd()."/files/csv/BU_activeness_Users_details".$this->reportCode().".csv",$csv);
		$data['csvFile'] = "BU_activeness_Users_details".$this->reportCode().".csv";
	
		$all_items = $this->db->query($sql);
		$all_items = $all_items->result_array();
		
		//SORT STATUS
		$limit = ($sort)=='y' ? 0 : $limit;
	
		$limit_items = $this->db->query($sql." LIMIT $limit,20");
		$limit_items = $limit_items->result_array();
		$data['totrec'] = count($all_items);
		$data['limit']  = $limit;
		
		$items	 = $limit_items;
		
		$table= "<table id='large2' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:11px;' class='iLike_Result_Table2'>
				<tr style='height: 40px;'>
					<th style='width:25px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'>	   No.  	  		  				  					  </th> 
					<th style='width:120px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"11-A\")'>          <b>User  	  	      </b></th> 
					<th style='width:90px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"0-A\")'>           <b>Views  	   	  </b></th> 
					<th style='width:83px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"1-D\")'>           <b>Likes  	  	  </b></th> 
					<th style='width:83px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"2-D\")'>           <b>Wants  	  	  </b></th> 
					<th style='width:191px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"3-A\")'>           <b>Item Code  	  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'>  <b>Image   	 	     							  	  </b></th> 
					<th style='width:195px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"4-A\")'>            <b>Item Name  	  </b></th> 
					<th style='width:120px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"5-A\")'>           <b>Status  	  	  </b></th> 
					<th style='width:120px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"6-A\")'>           <b>Type  	  	  </b></th> 
					<th style='width:153px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"7-D\")'>   	       <b>Outlet  	  	  </b></th> 
					<th style='width:125px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"8-D\")'>    		   <b>Premium  	  	  </b></th> 
					<th style='width:114px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"9-A\")'>           <b>Material  	  </b></th> 
					<th style='width:110px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"10-A\")'>          <b>Brand  	  	  </b></th> 
					<th style='width:96px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"12-A\")'>          <b>Country  	  	  </b></th> 
					<th style='width:73px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"13-A\")'>           <b>Publish  	  	  </b></th>
					<th style='width:73px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"21-D\")'>           <b>Disapprove  	  	  </b></th>
					<th style='width:100px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"14-A\")'>          <b>L. Price  	  </b></th> 
					<th style='width:116px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"15-A\")'>          <b>US. Price  	  </b></th>
					<th style='width:60px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"20-A\")'>          <b>Price Category   </b></th> 
					<th style='width:100px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"16-A\")'>          <b>Uploaded  	  </b></th> 
					<th style='width:100px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"17-A\")'>          <b>Released  	  </b></th> 
				</tr>";
		
		//REPLACE ORDER
		$table = str_replace($order_code,$this->sortingRef($order_code,'Rev_code'),$table);
		$table = str_replace($label,$this->sortingRef($order_code,'label_symbol'),$table);
				 
					$x = $limit;			
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					$orig_itemName="";
					if($valid_query==TRUE)
					{
					 foreach($items as $r) { 
					 extract($r);
					 $ptype = ($ptype=='') 	   ? 'Uncategorize' : $ptype;
					 $publish 	 = ($publish=='y') ? 'Yes' : 'No';
					 $disapprove = ($disapprove=='y') ? 'Yes' : 'No';
					 $c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
					 $orig_itemName=$itemName;
					 if(strlen($itemName)>=15)
							$itemName = substr($itemName,0,15)."...";
							
					 $likes 			= ($likes=="") 		? 0 : $likes;
					 $wants 		    = ($wants=="") 		? 0 : $wants;
		$table.= "<tr>
				  <td $c> $x </td>
				  <td $c style='text-align:left;padding-left:5px;'>		$full_name																			</td>
				  <td $c>													<a onclick=\"viewDialog('item_database',$itemID)\" style='cursor:pointer;'><b>$num_views</b> </a> </td>
				  <td $c>													<a onclick=\"showVoters('iLike',$itemID)\" style='cursor:pointer;'><b>$likes</b></a>      																</td>
				  <td $c>													<a onclick=\"showVoters('iWant',$itemID)\" style='cursor:pointer;'><b>$wants</b></a>      																</td>
				  <td $c>													$itemCode      																	</td>
				  <td $c style='text-align:center;'>			    		<img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 	</td>
				  <td $c style='text-align:left;padding-left:5px;' title='$orig_itemName'>		<a href='".HTTP_PATH."gallery/itemInfo2/$itemID' target='_blank'>		$itemName</a>  </td>
				  <td $c style='text-align:left;padding-left:5px;'>		$pstatus																		</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$ptype																			</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$poutlet_status																	</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$ppremium_type																	</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$pmaterial																	    </td>
				  <td $c style='text-align:left;padding-left:5px;'>		$pbrand																	    	</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$cName																			</td>
				  <td $c style='text-align:center;'>						$publish																		</td>
				  <td $c style='text-align:center;'>						$disapprove																		</td>
				  <td $c style='text-align:center;'>						$UnitPrice																		</td>
				  <td $c style='text-align:center;'>						$USD_Price																		</td>
				  <td $c style='text-align:center;'>						$price_rangeName																		</td>
				  <td $c style='text-align:center;'>				        ". $this->convertDate('date',"$dUploaded 00:00:00") ."					</td>
				  <td $c style='text-align:center;'>				        ". $this->convertDate('date',"$dReleased 00:00:00") ."	 				</td>
				</tr>";}
				   }
		$table.= "</tbody>";
					if(!$items OR $valid_query==FALSE)
						$table.=  "<tr><td colspan='20'>Sorry no items found, check your search parameters.</td></tr>";
		$table.= "</table>";	
		
		$data['table'] 		= $table;
		
		$viewer = 'innerPages';	    
		if($this->modules->access_checker()==TRUE)
		{
			$this->load->view($viewer,$data); 
		}else{
			$data['vfile']				= 'login.php';
			$data['title']				= 'SMBi System Log-in | SMBi';
			$data['page_title']			= 'SMBi System Log-in';
			$data['meta_description']	= 'San Miguel Brewing International';
			$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
			$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
			$this->load->view('login',$data); 	
		}
	}
	
	function BU_activeness_Users_per_Year($user_id='',$cID='',$year='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(68,'REVIEW');
		
		extract($_POST);
		$filter_WHERE='';
		$filter_WHERE 		= "WHERE admin_users.id = $user_id";
 		
		$table						= 'BU_activeness_Users';
		$data['vfile']				= 'BU_activeness_Users_per_Year.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/BU_activeness_Users> Activeness of Users Index  </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/BU_activeness_Users_per_Year/$user_id/$cID'> Activeness of Users per Year  </a>";
		//TOTAL NUMBER OF ROWS			
		
		
		$WHERE 	 		  = $filter_WHERE;
		$data['per_user'] = TRUE;
		$sql   = $this->db->query("SELECT country.countryName AS cName, admin_users.full_name as fname, admin_users.id as uID, country.id AS cID,  
								   COUNT(items.id) as num_items, target, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year 
								   FROM items 		 
								   LEFT   JOIN country 		ON country.id  = items.countryID 
								   LEFT   JOIN target_items ON target_items.countryID = items.countryID 
								   LEFT   JOIN admin_users  ON admin_users.id  = items.user_id 
								   $WHERE
								   GROUP BY YEAR(items.dateAdded) ORDER BY YEAR(items.dateAdded) DESC");
		
		if(isset($filter))
		{
			$data['per_user'] = FALSE;
			$WHERE 	 = '';
			if($countryID!='' AND $countryID!='all') $WHERE  .=  " items.countryID = $countryID AND";
			if($countryID=='all')					 $WHERE   =  "";
			if($user_id!=''  AND $user_id!='all') 	 $WHERE  .=  " items.user_id = $user_id     AND";
			/*
			if($cyear!=''  AND $cyear!='all') 	 	 $WHERE  .=  " YEAR(items.dateAdded) = $cyear AND";
			if($fmonth!='' AND $fmonth!='all' AND $tmonth!='' AND $tmonth!='all') 	 	 
					$WHERE  .=  " MONTH(items.dateAdded) >= $fmonth AND  MONTH(items.dateAdded) <= $tmonth AND";
			*/
			if($WHERE!='')  $WHERE  =  substr("WHERE ".$WHERE,0,-3);
			
			$sql   = $this->db->query("SELECT country.countryName AS cName, admin_users.full_name as fname, admin_users.id as uID, country.id AS cID,  
								   COUNT(items.id) as num_items, target, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year 
								   FROM items 		 
								   LEFT   JOIN country 		ON country.id  = items.countryID 
								   LEFT   JOIN target_items ON target_items.countryID = items.countryID 
								   LEFT   JOIN admin_users  ON admin_users.id  = items.user_id 
								   $WHERE
								   GROUP BY YEAR(items.dateAdded) ORDER BY YEAR(items.dateAdded) DESC");
		}
		
		$data['user_id']    = $user_id;
		$data['countryID']  = $cID;
		$data['POST'] = $_POST;		
		$data['reports']   = $sql->result_array();
		

		$viewer = 'innerPages';	    
		if($this->modules->access_checker()==TRUE)
		{
			$this->load->view($viewer,$data); 
		}else{
			$data['vfile']				= 'login.php';
			$data['title']				= 'SMBi System Log-in | SMBi';
			$data['page_title']			= 'SMBi System Log-in';
			$data['meta_description']	= 'San Miguel Brewing International';
			$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
			$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
			$this->load->view('login',$data); 	
		}
	}
	
	function BU_activeness_Users_per_Month($user_id='',$cID='',$year='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(68,'REVIEW');
		
		extract($_POST);
		$filter_WHERE='';
		$filter_WHERE 		= "WHERE admin_users.id = $user_id AND YEAR(items.dateAdded) = ".$year;
 		
		$table						= 'BU_activeness_Users';
		$data['vfile']				= 'BU_activeness_Users_per_Month.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/BU_activeness_Users> Activeness of Users Index  </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/BU_activeness_Users_per_Year/$user_id/$cID'> Activeness of Users per Year  </a>";
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/BU_activeness_Users_per_Month/$user_id/$cID/$year'> Activeness of Users per Month  </a>";
		//TOTAL NUMBER OF ROWS			
		
		
		$WHERE 	 		  = $filter_WHERE;
		$data['per_user'] = TRUE;
		$sql   = $this->db->query("SELECT country.countryName AS cName, admin_users.full_name as fname, admin_users.id as uID, country.id AS cID,  
								   COUNT(items.id) as num_items, target, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year 
								   FROM items 		 
								   LEFT   JOIN country 		ON country.id  = items.countryID 
								   LEFT   JOIN target_items ON target_items.countryID = items.countryID 
								   LEFT   JOIN admin_users  ON admin_users.id  = items.user_id 
								   $WHERE
								   GROUP BY MONTH(items.dateAdded), YEAR(items.dateAdded) ORDER BY MONTH(items.dateAdded) DESC, YEAR(items.dateAdded) DESC");
		
		if(isset($filter))
		{
			$data['per_user'] = FALSE;
			$WHERE 	 = '';
			if($countryID!='' AND $countryID!='all') $WHERE  .=  " items.countryID = $countryID AND";
			if($countryID=='all')					 $WHERE   =  "";
			if($user_id!=''  AND $user_id!='all') 	 $WHERE  .=  " items.user_id = $user_id     AND";
			if($cyear!=''  AND $cyear!='all') 	 	 $WHERE  .=  " YEAR(items.dateAdded) = $cyear AND";
			if($fmonth!='' AND $fmonth!='all' AND $tmonth!='' AND $tmonth!='all') 	 	 
					$WHERE  .=  " MONTH(items.dateAdded) >= $fmonth AND  MONTH(items.dateAdded) <= $tmonth AND";
					
			if($WHERE!='')  $WHERE  =  substr("WHERE ".$WHERE,0,-3);
			
			$sql   = $this->db->query("SELECT country.countryName AS cName, admin_users.full_name as fname, admin_users.id as uID, country.id AS cID,  
								   COUNT(items.id) as num_items, target, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year 
								   FROM items 		 
								   LEFT   JOIN country 		ON country.id  = items.countryID 
								   LEFT   JOIN target_items ON target_items.countryID = items.countryID 
								   LEFT   JOIN admin_users  ON admin_users.id  = items.user_id 
								   $WHERE
								   GROUP BY MONTH(items.dateAdded), YEAR(items.dateAdded) ORDER BY MONTH(items.dateAdded) DESC, YEAR(items.dateAdded) DESC");
		}
		
		$data['user_id']    = $user_id;
		$data['countryID']  = $cID;
		$data['cyear']  	= $year;
		$data['POST'] 		= $_POST;		
		$data['reports']    = $sql->result_array();
		

		$viewer = 'innerPages';	    
		if($this->modules->access_checker()==TRUE)
		{
			$this->load->view($viewer,$data); 
		}else{
			$data['vfile']				= 'login.php';
			$data['title']				= 'SMBi System Log-in | SMBi';
			$data['page_title']			= 'SMBi System Log-in';
			$data['meta_description']	= 'San Miguel Brewing International';
			$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
			$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
			$this->load->view('login',$data); 	
		}
	}
	
	//AVG Price per Item
	function AVG_Price_index()
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(69,'REVIEW');
		extract($_POST);
		
		$filter_WHERE='';
		if($_SESSION['super_admin']!='y'){
			$filter_WHERE 		= "WHERE items.countryID =".$_SESSION['countryID'];
		}
		
		$table						= 'AVG_Price_index';
		$data['vfile']				= 'AVG_Price_index.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/AVG_Price_index> Average Price Index </a>';
		
		//TOTAL NUMBER OF ROWS			
		
		$WHERE 	 =  $filter_WHERE;
		
		$data['per_country'] = TRUE;
		$sql   = $this->db->query("SELECT country.countryName AS cName, country.id AS cID,  
								   COUNT(items.id) as num_items, target, DATE_FORMAT(items.dateAdded,'%M') as month, 
								   MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year,
								   AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice
								   FROM   items 		 
								   LEFT   JOIN country 		ON country.id  = items.countryID 
								   LEFT   JOIN target_items ON target_items.countryID = items.countryID 
								   $WHERE
								   GROUP BY country.id  ORDER BY YEAR(items.dateAdded) DESC");
		
		if(isset($filter))
		{   
			$data['per_country'] = FALSE;
			$WHERE 	 = '';
			if($countryID!='' AND $countryID!='all') $WHERE  .=  " items.countryID = $countryID AND";
			if($countryID=='all')					 $WHERE   =  "";	
			if($WHERE!='')  $WHERE  =  substr("WHERE ".$WHERE,0,-3);
			
			$sql   = $this->db->query("SELECT country.countryName AS cName, country.id AS cID,  
									   COUNT(items.id) as num_items, target, DATE_FORMAT(items.dateAdded,'%M') as month, 
									   MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year,
									   AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice
									   FROM items 		 
									   LEFT   JOIN country 		ON country.id  = items.countryID 
									   LEFT   JOIN target_items ON target_items.countryID = items.countryID 
									   $WHERE
									   GROUP BY cName ORDER BY YEAR(items.dateAdded) DESC");
		}
		
		$data['POST'] = $_POST;
		$data['reports']   = $sql->result_array();
		

		$viewer = 'innerPages';	    
		if($this->modules->access_checker()==TRUE)
		{
			$this->load->view($viewer,$data); 
		}else{
			$data['vfile']				= 'login.php';
			$data['title']				= 'SMBi System Log-in | SMBi';
			$data['page_title']			= 'SMBi System Log-in';
			$data['meta_description']	= 'San Miguel Brewing International';
			$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
			$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
			$this->load->view('login',$data); 	
		}
	}
	
	function AVG_Price_per_Year($cID='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(69,'REVIEW');
		extract($_POST);
		
		$filter_WHERE='';
		$filter_WHERE 		= "WHERE items.countryID =$cID";
		$data['countryID']	= $cID;
		
		$table						= 'AVG_Price_per_Year';
		$data['vfile']				= 'AVG_Price_per_Year.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/AVG_Price_index> Average Price per Year </a>';
		
		//TOTAL NUMBER OF ROWS			
		
		$WHERE 	 =  $filter_WHERE;
		
		$data['per_country'] = TRUE;
		$sql   = $this->db->query("SELECT country.countryName AS cName, country.id AS cID,  
								   COUNT(items.id) as num_items, target, DATE_FORMAT(items.dateAdded,'%M') as month, 
								   MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year,
								   AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice
								   FROM   items 		 
								   LEFT   JOIN country 		ON country.id  = items.countryID 
								   LEFT   JOIN target_items ON target_items.countryID = items.countryID 
								   $WHERE
								   GROUP BY YEAR(items.dateAdded) ORDER BY YEAR(items.dateAdded) DESC");
		
		if(isset($filter))
		{   
			$data['per_country'] = FALSE;
			$WHERE 	 = '';
			if($countryID!='' AND $countryID!='all') $WHERE  .=  " items.countryID = $countryID AND";
			if($countryID=='all')					 $WHERE   =  "";	
			if($WHERE!='')  $WHERE  =  substr("WHERE ".$WHERE,0,-3);
			
			$sql   = $this->db->query("SELECT country.countryName AS cName, country.id AS cID,  
									   COUNT(items.id) as num_items, target, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year 
									   FROM items 		 
									   LEFT   JOIN country 		ON country.id  = items.countryID 
									   LEFT   JOIN target_items ON target_items.countryID = items.countryID 
									   $WHERE
									   GROUP BY YEAR(items.dateAdded) ORDER BY YEAR(items.dateAdded) DESC");
		}
		
		$data['POST'] = $_POST;
		$data['reports']   = $sql->result_array();
		

		$viewer = 'innerPages';	    
		if($this->modules->access_checker()==TRUE)
		{
			$this->load->view($viewer,$data); 
		}else{
			$data['vfile']				= 'login.php';
			$data['title']				= 'SMBi System Log-in | SMBi';
			$data['page_title']			= 'SMBi System Log-in';
			$data['meta_description']	= 'San Miguel Brewing International';
			$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
			$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
			$this->load->view('login',$data); 	
		}
	}
	
	function AVG_Price_per_Month($cID='',$year='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(69,'REVIEW');
		extract($_POST);
		
		$filter_WHERE='';
		$filter_WHERE 		= "WHERE items.countryID =$cID AND YEAR(items.dateAdded) = $year";
		$data['countryID']	= $cID;
		$data['cyear']		= $year;
		
		$table						= 'AVG_Price_per_Month';
		$data['vfile']				= 'AVG_Price_per_Month.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/AVG_Price_index> Average Price per Month </a>';
		
		//TOTAL NUMBER OF ROWS			
		
		$WHERE 	 =  $filter_WHERE;
		
		$data['per_country'] = TRUE;
		$sql   = $this->db->query("SELECT country.countryName AS cName, country.id AS cID,  
								   COUNT(items.id) as num_items, target, DATE_FORMAT(items.dateAdded,'%M') as month, 
								   MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year,
								   AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice
								   FROM   items 		 
								   LEFT   JOIN country 		ON country.id  = items.countryID 
								   LEFT   JOIN target_items ON target_items.countryID = items.countryID 
								   $WHERE
								   GROUP BY MONTH(items.dateAdded), YEAR(items.dateAdded) ORDER BY MONTH(items.dateAdded) DESC, YEAR(items.dateAdded) DESC");
		
		if(isset($filter))
		{   
			$data['per_country'] = FALSE;
			$WHERE 	 = '';
			if($countryID!='' AND $countryID!='all') $WHERE  .=  " items.countryID = $countryID AND YEAR(items.dateAdded) = $year AND";
			if($countryID=='all')					 $WHERE   =  "";
			if($fmonth!='' AND $fmonth!='all' AND $tmonth!='' AND $tmonth!='all') 	 	 
					$WHERE  .=  " MONTH(items.dateAdded) >= $fmonth AND  MONTH(items.dateAdded) <= $tmonth AND";
					
			if($WHERE!='')  $WHERE  =  substr("WHERE ".$WHERE,0,-3);
			
			$sql   = $this->db->query("SELECT country.countryName AS cName, country.id AS cID,  
									   COUNT(items.id) as num_items, target, DATE_FORMAT(items.dateAdded,'%M') as month, 
									   MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year,
									   AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice
									   FROM items 		 
									   LEFT   JOIN country 		ON country.id  = items.countryID 
									   LEFT   JOIN target_items ON target_items.countryID = items.countryID 
									   $WHERE
									   GROUP BY MONTH(items.dateAdded), YEAR(items.dateAdded) ORDER BY MONTH(items.dateAdded) DESC, YEAR(items.dateAdded) DESC");
		}
		
		$data['POST'] = $_POST;
		$data['reports']   = $sql->result_array();
		

		$viewer = 'innerPages';	    
		if($this->modules->access_checker()==TRUE)
		{
			$this->load->view($viewer,$data); 
		}else{
			$data['vfile']				= 'login.php';
			$data['title']				= 'SMBi System Log-in | SMBi';
			$data['page_title']			= 'SMBi System Log-in';
			$data['meta_description']	= 'San Miguel Brewing International';
			$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
			$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
			$this->load->view('login',$data); 	
		}
	}
	
	function detectQuater()
	{
		return $quater = CEIL(date('m')/3);
	}
	
	function quaterStr()
	{
		$quater = CEIL(date('m')/3);
		$str="";
		switch($quater)
		{
			case 1:
				$str="1st Quarter: January-March";
			break; 
			case 2:
				$str="2nd Quarter: April-June";
			break; 
			case 3:
				$str="3rd Quarter: July-September";
			break; 
			case 4:
				$str="4th Quarter: October-December";
			break; 
		}
		
		return $str;
	}
	
	function csv_from_result($query, $delim = ",", $newline = "\n", $enclosure = '"')
	{
		if ( ! is_object($query) OR ! method_exists($query, 'list_fields'))
		{
			show_error('You must submit a valid result object');
		}

		$out = '';

		// First generate the headings from the table column names
		foreach ($query->list_fields() as $name)
		{
			$out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, $name).$enclosure.$delim;
		}

		$out = rtrim($out);
		$out .= $newline;

		// Next blast through the result array and build out the rows
		foreach ($query->result_array() as $row)
		{
			foreach ($row as $item)
			{
				$out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, $item).$enclosure.$delim;
			}
			$out = rtrim($out);
			$out .= $newline;
		}

		return $out;
	}
	
	function generateCSVFile($view='',$sql='',$fileName='')
	{
		$query = $this->db->query($sql);
		$new_report = $this->csv_from_result($query,",","\n");
		write_file(getcwd()."/files/csv/$fileName",$new_report);
	}
	
	function downloadCSV($fileName='')
	{
		force_download($fileName, file_get_contents(getcwd()."/files/csv/$fileName"));
	}
	
	function sortingRef($id='',$returnType='')
	{
		$query	  ='';
		$Orig_code='';
		$Rev_code ='';
		switch($id)
		{
		 case '0-A':
		  $query 	 	 = "num_views ASC";
		  $Orig_code 	 = "0-A";
		  $Rev_code  	 = "0-D";
		  $label	 	 = "Views ";
		  $label_symbol	 = "Views &#x25B2;";
		  //$label
		 break;
		 case '0-D':
		  $query 	 	 = "num_views DESC"; 
		  $Orig_code 	 = "0-D";
		  $Rev_code  	 = "0-A";
		  $label	 	 = "Views ";
		  $label_symbol	 = "Views &#x25BC;";
		 break;
		 case '1-A':
		  $query = " likes ASC"; 
		  $Orig_code = "1-A";
		  $Rev_code  = "1-D";
		  $label	 	 = "Likes ";
		  $label_symbol	 = "Likes &#x25B2;";
		 break;
		 case '1-D':
		  $query     	 = "likes DESC"; 
		  $Orig_code 	 = "1-D";
		  $Rev_code  	 = "1-A";
		  $label	 	 = "Likes ";
		  $label_symbol	 = "Likes &#x25BC;";
		 break;
		 case '2-A':
		  $query     	 = "wants ASC"; 
		  $Orig_code 	 = "2-A";
		  $Rev_code  	 = "2-D";
		  $label	 	 = "Wants ";
		  $label_symbol	 = "Wants &#x25B2;";
		 break;
		 case '2-D':
		  $query     = "wants DESC"; 
		  $Orig_code = "2-D";
		  $Rev_code  = "2-A";
		  $label	 	 = "Wants ";
		  $label_symbol	 = "Wants &#x25BC;";
		 break;
		 case '3-A':
		  $query     = "itemCode ASC";
	      $Orig_code = "3-A";
		  $Rev_code  = "3-D";
		  $label	 	 = "Item Code ";
		  $label_symbol	 = "Item Code &#x25B2;";
		 break;
		 case '3-D':
		  $query     = "itemCode DESC"; 
		  $Orig_code = "3-D";
		  $Rev_code  = "3-A";
		   $label	 	 = "Item Code ";
		  $label_symbol	 = "Item Code &#x25BC;";
		 break;
		 case '4-A':
		  $query 	 = "itemName ASC"; 
		  $Orig_code = "4-A";
		  $Rev_code  = "4-D";
		  $label	 	 = "Item Name";
		  $label_symbol	 = "Item Name &#x25B2;";
		 break;
		 case '4-D':
		  $query = " itemName DESC";
		  $Orig_code = "4-D";
		  $Rev_code  = "4-A";
		  $label	 	 = "Item Name";
		  $label_symbol	 = "Item Name &#x25BC;";
		 break;
		 case '5-A':
		  $query 	 	 = " pstatus ASC"; 
		  $Orig_code 	 = "5-A";
		  $Rev_code  	 = "5-D";
		  $label	 	 = "Status";
		  $label_symbol	 = "Status &#x25B2;";
		 break;
		 case '5-D':
		  $query 		 = " pstatus DESC";
		  $Orig_code 	 = "5-D";
		  $Rev_code  	 = "5-A";
		  $label	 	 = "Status";
		  $label_symbol	 = "Status &#x25BC;";
		 break;
		 case '6-A':
		  $query = " ptype ASC";
		  $Orig_code = "6-A";
		  $Rev_code  = "6-D";
		  $label	 	 = "Type";
		  $label_symbol	 = "Type &#x25B2;";
		 break;
		 case '6-D':
		  $query = " ptype DESC"; 
		  $Orig_code = "6-D";
		  $Rev_code  = "6-A";
		  $label	 	 = "Type";
		  $label_symbol	 = "Type &#x25BC;";
		 break;
		 case '7-A':
		  $query = " poutlet_status ASC"; 
		  $Orig_code = "7-A";
		  $Rev_code  = "7-D";
		  $label	 	 = "Outlet";
		  $label_symbol	 = "Outlet &#x25B2;";
		 break;
		 case '7-D':
		  $query = " poutlet_status DESC"; 
		  $Orig_code = "7-D";
		  $Rev_code  = "7-A";
		  $label	 	 = "Outlet";
		  $label_symbol	 = "Outlet &#x25BC;";
		 break;
		 case '8-A':
		  $query = " ppremium_type ASC"; 
		  $Orig_code = "8-A";
		  $Rev_code  = "8-D";
		  $label	 	 = "Premium";
		  $label_symbol	 = "Premium &#x25B2;";
		 break;
		 case '8-D':
		  $query = " ppremium_type DESC"; 
		  $Orig_code = "8-D";
		  $Rev_code  = "8-A";
		  $label	 	 = "Premium";
		  $label_symbol	 = "Premium &#x25BC;";
		 break;
		 case '9-A':
		  $query = " pmaterial ASC";
		  $Orig_code = "9-A";
		  $Rev_code  = "9-D";
		  $label	 	 = "Material";
		  $label_symbol	 = "Material &#x25B2;";
		 break;
		 case '9-D':
		  $query = " pmaterial DESC"; 
		  $Orig_code = "9-D";
		  $Rev_code  = "9-A";
		  $label	 	 = "Material";
		  $label_symbol	 = "Material &#x25BC;";
		 break;
		 case '10-A':
		  $query = " pbrand ASC"; 
		  $Orig_code = "10-A";
		  $Rev_code  = "10-D";
		  $label	 	 = "Brand";
		  $label_symbol	 = "Brand &#x25B2;";
		 break;
		 case '10-D':
		  $query = " pbrand DESC"; 
		  $Orig_code = "10-D";
		  $Rev_code  = "10-A";
		  $label	 	 = "Brand";
		  $label_symbol	 = "Brand &#x25BC;";
		 break;
		 case '11-A':
		  $query = " full_name ASC"; 
		  $Orig_code = "11-A";
		  $Rev_code  = "11-D";
		  $label	 	 = "User";
		  $label_symbol	 = "User &#x25B2;";
		 break;
		 case '11-D':
		  $query = " full_name DESC"; 
		  $Orig_code = "11-D";
		  $Rev_code  = "11-A";
		  $label	 	 = "User";
		  $label_symbol	 = "User &#x25BC;";
		 break;
		 case '12-A':
		  $query = " pcountry ASC"; 
		  $Orig_code = "12-A";
		  $Rev_code  = "12-D";
		  $label	 	 = "Country";
		  $label_symbol	 = "Country &#x25B2;";
		 break;
		 case '12-D':
		  $query = " pcountry DESC"; 
		  $Orig_code = "12-D";
		  $Rev_code  = "12-A";
		  $label	 	 = "Country";
		  $label_symbol	 = "Country &#x25BC;";
		 break;
		 case '13-A':
		  $query = " publish ASC"; 
		  $Orig_code = "13-A";
		  $Rev_code  = "13-D";
		  $label	 	 = "Publish";
		  $label_symbol	 = "Publish &#x25B2;";
		 break;
		 case '13-D':
		  $query = " publish DESC"; 
		  $Orig_code = "13-D";
		  $Rev_code  = "13-A";
		  $label	 	 = "Publish";
		  $label_symbol	 = "Publish &#x25BC;";
		 break;
		 case '14-A':
		  $query = " UnitPrice ASC"; 
		  $Orig_code = "14-A";
		  $Rev_code  = "14-D";
		  $label	 	 = "L. Price";
		  $label_symbol	 = "L. Price &#x25B2;";
		 break;
		 case '14-D':
		  $query = " UnitPrice DESC"; 
		  $Orig_code = "14-D";
		  $Rev_code  = "14-A";
		  $label	 	 = "L. Price";
		  $label_symbol	 = "L. Price &#x25BC;";
		 break;
		 case '15-A':
		  $query = " USD_Price ASC"; 
		  $Orig_code = "15-A";
		  $Rev_code  = "15-D";
		  $label	 	 = "US. Price";
		  $label_symbol	 = "US. Price &#x25B2;";
		 break;
		 case '15-D':
		  $query = " USD_Price DESC"; 
		  $Orig_code = "15-D";
		  $Rev_code  = "15-A";
		  $label	 	 = "US. Price";
		  $label_symbol	 = "US. Price &#x25BC;";
		 break;
		 case '16-A':
		  $query = " dUploaded ASC"; 
		  $Orig_code = "16-A";
		  $Rev_code  = "16-D";
		  $label	 	 = "Uploaded";
		  $label_symbol	 = "Uploaded &#x25B2;";
		 break;
		 case '16-D':
		  $query = " dUploaded DESC"; 
		  $Orig_code = "16-D";
		  $Rev_code  = "16-A";
		  $label	 	 = "Uploaded";
		  $label_symbol	 = "Uploaded &#x25BC;";
		 break;
		 case '17-A':
		  $query = " dReleased ASC"; 
		  $Orig_code = "17-A";
		  $Rev_code  = "17-D";
		  $label	 	 = "Released";
		  $label_symbol	 = "Released &#x25B2;";
		 break;
		 case '17-D':
		  $query = " dReleased DESC"; 
		  $Orig_code = "17-D";
		  $Rev_code  = "17-A";
		  $label	 	 = "Released";
		  $label_symbol	 = "Released &#x25BC;";
		 break;
		 case '18-A':
		  $query = " gallery_views ASC"; 
		  $Orig_code = "18-A";
		  $Rev_code  = "18-D";
		  $label	 	 = "Views";
		  $label_symbol	 = "Views &#x25B2;";
		 break;
		 case '18-D':
		  $query = " gallery_views DESC"; 
		  $Orig_code = "18-D";
		  $Rev_code  = "18-A";
		  $label	 	 = "Views";
		  $label_symbol	 = "Views &#x25BC;";
		 break;
		 case '19-A':
		  $query = " common_views ASC"; 
		  $Orig_code = "19-A";
		  $Rev_code  = "19-D";
		  $label	 	 = "Views";
		  $label_symbol	 = "Views &#x25B2;";
		 break;
		 case '19-D':
		  $query = " common_views DESC"; 
		  $Orig_code = "19-D";
		  $Rev_code  = "19-A";
		  $label	 	 = "Views";
		  $label_symbol	 = "Views &#x25BC;";
		 break;
		 case '20-A':
		  $query = " price_level ASC"; 
		  $Orig_code = "20-A";
		  $Rev_code  = "20-D";
		  $label	 	 = "Price Category";
		  $label_symbol	 = "Price Category &#x25B2;";
		 break;
		 case '20-D':
		  $query = " price_level DESC"; 
		  $Orig_code = "20-D";
		  $Rev_code  = "20-A";
		  $label	 	 = "Price Category";
		  $label_symbol	 = "Price Category &#x25BC;";
		 break;
		  case '21-A':
		  $query = " disapprove ASC"; 
		  $Orig_code = "21-A";
		  $Rev_code  = "21-D";
		  $label	 	 = "Disapprove";
		  $label_symbol	 = "Disapprove &#x25B2;";
		 break;
		 case '21-D':
		  $query = " disapprove DESC"; 
		  $Orig_code = "21-D";
		  $Rev_code  = "21-A";
		  $label	 	 = "Disapprove";
		  $label_symbol	 = "Disapprove &#x25BC;";
		 break;
		}
		if($returnType=='query')
			return $query;
		elseif($returnType=='Orig_code')
			return $Orig_code;
		elseif($returnType=='Rev_code')
			return $Rev_code;
		elseif($returnType=='label')
			return $label;
		elseif($returnType=='label_symbol')
			return $label_symbol;
	}
	
	function commonGallery_views($view='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(79,'REVIEW');
		//USER MANUAL
	    $data['USER_MANUAL'] = $this->modules->user_manual(43);
		
		//DETECT COUNTRY
		$cond = "";
		$HAVING="";
		$WHERE="WHERE cID != 0 AND  publish='y' AND pbrandID IN (SELECT brandID FROM commonGalleryBrands) AND forArchiving='n' AND forPurging='n' AND (itemAge <".$this->modules->itemsAge().")  ";
		$data['cID']=0;
		$data['sa']=TRUE;
		if($_SESSION['super_admin']!='y' AND $_SESSION['countryID']!=0){
			$WHERE 		= " WHERE cID =".$_SESSION['countryID']." AND publish='y' AND pbrandID IN (SELECT brandID FROM commonGalleryBrands) AND forArchiving='n' AND forPurging='n' AND (itemAge <".$this->modules->itemsAge().")  ";
			$data['cID']= $_SESSION['countryID'];
			$data['sa']=FALSE;
		}
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/commonGallery_views>  Number of Views from Common Gallery   </a>';
		
		$table						= 'item_division';
		$data['vfile']				= 'commonGallery_views.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//TOTAL NUMBER OF ROWS	
		$sort='n';
		extract($_POST);
		$data['DateFrom'] = "";
		$data['DateTo'] = "";
		$data['quarterStr'] = "";
		if((isset($DateFrom) AND isset($DateTo)) AND ($DateFrom!='' AND $DateTo!='') AND !isset($Reset)){
		$WHERE   .= "AND dReleased >= '$DateFrom' AND dReleased <= '$DateTo' ";
		$data['DateFrom'] = $DateFrom;
		$data['DateTo']   = $DateTo;

		//DETECT QUARTER STRING
		if($DateFrom==$this->perQuarterMonths('prevMonth') & $DateTo==$this->perQuarterMonths('nextMonth'))
		 $data['quarterStr'] = $this->quaterStr('condition');
		//PREVIOUS QUARTER
		if($DateFrom==$this->setPreviousQTR($DateFrom,'prevMonth') & $DateTo==$this->setPreviousQTR($DateTo,'nextMonth'))
		 $data['quarterStr'] = $this->SetPrevQuaterStr($DateFrom);
		//PREVIOUS QUARTER
		if($DateFrom==$this->minMaxDatePublished('prevMonth') & $DateTo==$this->minMaxDatePublished('nextMonth'))
		 $data['quarterStr'] = $this->minMaxDatePublished('condition');
		}else{
		$WHERE   		   .= "AND ".$this->minMaxDatePublished('filter');
		$data['quarterStr'] = $this->minMaxDatePublished('condition');
		$data['DateFrom']   = $this->minMaxDatePublished('prevMonth');
		$data['DateTo']     = $this->minMaxDatePublished('nextMonth');
		}
		
		if(isset($Submit) OR isset($selpage))
		{	
			$val1 = mysql_real_escape_string($val1);
			$val1 = trim($val1);
			$val2 = mysql_real_escape_string($val2);
			$val2 = trim($val2);
			
			$condition = '';
			switch($cond1){
				case 'equal': 
					$condition = '=';
				break;
				case 'containing': 
					$condition = 'like';
				break;
				case 'in': 
					$condition = 'in';
				break;
				case 'greaterThan': 
					$condition = '>=';
				break;
				case 'lessThan': 
					$condition = '<=';
				break;
				case 'between': 
					$condition = 'between';
				break;
			}
			
			//COUNTRY NAME
			if(($opt1=='cName' OR $opt1=='pstatus' OR $opt1=='ptype' OR $opt1=='poutlet_status' OR $opt1=='ppremium_type' OR $opt1=='pmaterial' OR $opt1=='pbrand') AND $val1!='')
			{
				if($condition=='=')
					$cond = " AND $opt1 $condition '$val1'";
				if($condition=='>=')
					$cond = " AND $opt1 $condition '$val1'";
				if($condition=='<=')
					$cond = " AND $opt1 $condition '$val1'";
				if($condition=='like')
					$cond = " AND $opt1 $condition '%$val1%'";
				if($condition=='in')
					$cond = " AND $opt1 $condition ('" . str_replace(",", "','", $val1) . "')";
			}

			//PUBLISH
			$field="";
			if($opt1=='uploaded')
				$field=" COUNT(itemID) ";
			if($opt1=='num_views')
				$field=" SUM(num_views)";
			if(($opt1=='uploaded' OR $opt1=='num_views') AND $val1!='' AND is_numeric($val1))
			{	
				if($condition=='=')
					$HAVING = " HAVING $field $condition $val1 ";
				if($condition=='>=')
					$HAVING = " HAVING $field $condition $val1 ";
				if($condition=='<=')
					$HAVING = " HAVING $field $condition $val1 ";
				if($condition=='in' AND (is_numeric($val1) OR strpos($val1,',')==TRUE))
					$HAVING = "   HAVING $field $condition ('" . str_replace(",", "','", $val1) . "')";
			}
			
			if($condition=='between' & $this->checkStr($val1)==TRUE)
					$HAVING = " HAVING $field $condition ".stripslashes($val1);
			
			/*2ND SET*/
			switch($cond2){
				case 'equal': 
					$condition2 = '=';
				break;
				case 'containing': 
					$condition2 = 'like';
				break;
				case 'in': 
					$condition2 = 'in';
				break;
				case 'greaterThan': 
					$condition2 = '>=';
				break;
				case 'lessThan': 
					$condition2 = '<=';
				break;
				case 'between': 
					$condition2 = 'between';
				break;
			}
			
			//COUNTRY NAME
			if(($opt2=='cName' OR $opt2=='pstatus' OR $opt2=='ptype' OR $opt2=='poutlet_status' OR $opt2=='ppremium_type' OR $opt2=='pmaterial' OR $opt2=='pbrand') AND $val2!='')
			{
				if($condition2=='=')
					$cond .= " $operator  $opt2 $condition2 '$val2'";
				if($condition2=='>=')
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='<=')
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator $opt2 $condition2 '%$val2%'";
				if($condition2=='in')
					$cond .= " $operator $opt2 $condition2 ('" . str_replace(",", "','", $val2) . "')";
			}			
			
			$field="";
			if($opt2=='uploaded')
				$field=" COUNT(itemID) ";
			if($opt2=='num_views')
				$field=" SUM(num_views)";
			if(($opt2=='uploaded' OR $opt2=='num_views') AND $val2!='' AND (is_numeric($val2) OR $this->checkStr($val2)==TRUE))
			{	
				$HAVING .= ($HAVING=="") ? "HAVING" : "$operator";
				if($condition2=='=')
					$HAVING .= " $field $condition2 $val2 ";
				if($condition2=='>=')
					$HAVING .= " $field $condition2 $val2 ";
				if($condition2=='<=')
					$HAVING .= " $field $condition2 $val2 ";
				if($condition2=='in' AND (is_numeric($val2) OR strpos($val2,',')==TRUE))
					$HAVING .= "  $field $condition2 ('" . str_replace(",", "','", $val2) . "')";
			}

			if($condition2=='between' & $this->checkStr($val2)==TRUE)
				$HAVING .= " $field $condition2 ".stripslashes($val2);	
				
			$cond = ($cond=="") ? "" : " $cond ";
			$WHERE  .=  " $cond ";
		}
		
		//IF THEIR NO POSSIBLE RESULT
		$valid_query=TRUE;
		if(isset($Submit)){
			if(($cond=="" AND $HAVING=="") AND ($val1!='' OR $val2!='')){ 
			$valid_query=FALSE;
			$WHERE = " WHERE cID=-1";
			}
		}
		
		$data['POST']      = $_POST;
		if(isset($Reset)) $data['POST'] = array();
		
		//POSM STATUS;
		$arr = '';
		$sql_csv='';
		switch($view)
		{
		case '':
		case 'POSM_STATUS':
		$data['tab'] = 'POSM_STATUS';
		$q = "SELECT 
		     cName 			  as Country_Name, cID,
		     pstatus 		  as fldVal,
		     pStatusID 		  as fldID,
		     COUNT(itemID) 	  as Uploaded_Items , 
		     SUM(num_views) as myViews
		     FROM item_db_reports
			 $WHERE
		     GROUP BY cName, pstatus $HAVING ORDER BY pstatus ASC, cName ASC";
		$csv = "SELECT 
		     cName 			  as Country_Name, 
		     pstatus 		  as POSM_STATUS,
		     COUNT(itemID) 	  as Uploaded_Items , 
		     SUM(num_views) as myViews
		     FROM item_db_reports
			 $WHERE
		     GROUP BY cName, pstatus $HAVING ORDER BY pstatus ASC, cName ASC";
		$sql      = $this->db->query($q);
		$arr[]    = array('table'=>'POSM Status',
					   'fld'=>'pStatusID',
					   'rows'=>$sql->result_array());
		break;
		case 'POSM_TYPE':
		//POSM TYPE;
		$data['tab'] = 'POSM_TYPE';
		$q = "SELECT 
			   cName   			as Country_Name, cID,
			   ptype 			as fldVal,
			   ptypeID 			as fldID,
			   COUNT(itemID) 	  as Uploaded_Items , 
			   SUM(num_views) as myViews
			   FROM item_db_reports
			   $WHERE
			   GROUP BY cName, ptype $HAVING ORDER BY ptype ASC, cName ASC";
		$csv = "SELECT 
			   cName   			as Country_Name, 
			   ptype 			as POSM_TYPE,
			   COUNT(itemID) 	  as Uploaded_Items , 
			   SUM(num_views) as myViews
			   FROM item_db_reports
			   $WHERE
			   GROUP BY cName, ptype $HAVING ORDER BY ptype ASC, cName ASC";
		$sql   = $this->db->query($q);
		$arr[] = array('table'=>'POSM Type',
					   'fld'=>'ptypeID',
					   'rows'=>$sql->result_array());
		break;
		case 'OUTLET_TYPE':
		//OUTLET TYPE;
		$data['tab'] = 'OUTLET_TYPE';
		$q = "SELECT 
				cName   	  	 as Country_Name, cID, 
				poutlet_status 	 as fldVal,
				poutlet_statusID as fldID,
				COUNT(itemID) 	  as Uploaded_Items , 
				SUM(num_views) as myViews
				FROM item_db_reports
				$WHERE
				GROUP BY cName, poutlet_status $HAVING ORDER BY poutlet_status ASC, cName ASC";
		$csv = "SELECT 
				cName   	  	 as Country_Name, 
				poutlet_status 	 as POSM_STATUS,
				COUNT(itemID) 	  as Uploaded_Items , 
				SUM(num_views) as myViews
				FROM item_db_reports
				$WHERE
				GROUP BY cName, poutlet_status $HAVING ORDER BY poutlet_status ASC, cName ASC";
		$sql = $this->db->query($q);
		$arr[] = array('table'=>'OUTLET TYPE',
					   'fld'=>'poutlet_statusID',
					   'rows'=>$sql->result_array());
		break;
		case 'PREMIUM_TYPE':
		//PREMIUM TYPE;
		$data['tab'] = 'PREMIUM_TYPE';
		$q = "SELECT 
			   cName   	  		as Country_Name, cID,
			   ppremium_type 	as fldVal,
			   ppremium_typeID 	as fldID,
			   COUNT(itemID) 	  as Uploaded_Items , 
		       SUM(num_views) as myViews
			   FROM item_db_reports
			   $WHERE
			   GROUP BY cName, ppremium_type  $HAVING ORDER BY ppremium_type ASC, cName ASC";
		$csv = "SELECT 
			   cName   	  		as Country_Name, 
			   ppremium_type 	as PREMIUM_TYPE,
			   COUNT(itemID) 	  as Uploaded_Items , 
			   SUM(num_views) as myViews
			   FROM item_db_reports
			   $WHERE
			   GROUP BY cName, ppremium_type  $HAVING ORDER BY ppremium_type ASC, cName ASC";
		$sql   = $this->db->query($q);
		$arr[] = array('table'=>'PREMIUM Type',
					   'fld'=>'ppremium_typeID',
					   'rows'=>$sql->result_array());
		break;
		case 'MATERIAL_TYPE':
		//MATERIAL TYPE;
		$data['tab'] = 'MATERIAL_TYPE';
		$q = "SELECT 
			   cName   	  		as Country_Name, cID,
			   pmaterial 		as fldVal,
			   pmaterialID 		as fldID,
			   COUNT(itemID) 	  as Uploaded_Items , 
			   SUM(num_views) as myViews
			   FROM item_db_reports
			   $WHERE
			   GROUP BY cName, pmaterial  $HAVING ORDER BY pmaterial ASC, cName ASC";
		$csv = "SELECT 
			   cName   	  		as Country_Name,
			   pmaterial 		as MATERIAL_TYPE,
			   COUNT(itemID) 	  as Uploaded_Items , 
		       SUM(num_views) as myViews
			   FROM item_db_reports
			   $WHERE
			   GROUP BY cName, pmaterial  $HAVING ORDER BY pmaterial ASC, cName ASC";
		$sql   = $this->db->query($q);
		$arr[] = array('table'=>'MATERIAL Type',
					   'fld'=>'pmaterialID',
					   'rows'=>$sql->result_array());
		break;
		case 'BRAND_TYPE':
		//BRAND TYPE;
		$data['tab'] = 'BRAND_TYPE';
		$q="SELECT 
			  cName   	  		as Country_Name, cID,
			  pbrand 			as fldVal,
			  pbrandID 		 	as fldID,
			  COUNT(itemID) 	  as Uploaded_Items , 
		      SUM(num_views) as myViews
			  FROM item_db_reports
			  $WHERE
			  GROUP BY cName, pbrand  $HAVING ORDER BY pbrand ASC, cName ASC";
		$csv="SELECT 
			  cName   	  		as Country_Name,
			  pbrand 			as BRAND_TYPE,
			  COUNT(itemID) 	  as Uploaded_Items , 
		      SUM(num_views) as myViews
			  FROM item_db_reports
			  $WHERE
			  GROUP BY cName, pbrand  $HAVING ORDER BY pbrand ASC, cName ASC";
		$sql   = $this->db->query($q);
		$arr[] = array('table'=>'BRAND Type',
					   'fld'=>'pbrandID',
					   'rows'=>$sql->result_array());
		break;
		}
		
		
		$data['results'] = $arr;
		
		//generate csv file
		$this->generateCSVFile('item_views',$csv,"Number_of_Views_from_Common_Gallery".$this->reportCode().".csv");
		$data['csvFile']			= "Number_of_Views_from_Common_Gallery".$this->reportCode().".csv";
		
		$viewer = 'innerPages';	    
		if($this->modules->access_checker()==TRUE)
		{
			$this->load->view($viewer,$data); 
		}else{
			$data['vfile']				= 'login.php';
			$data['title']				= 'SMBi System Log-in | SMBi';
			$data['page_title']			= 'SMBi System Log-in';
			$data['meta_description']	= 'San Miguel Brewing International';
			$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
			$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
			$this->load->view('login',$data); 	
		}
	}
	
	function commonGallery_details($cID='',$fldName='',$fldVal='',$DateFrom='',$DateTo='')
	{	
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(79,'REVIEW');
		$csv = "";
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(43);
		
		$table						= 'commonGallery_details';
		$data['vfile']				= 'commonGallery_details.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/commonGallery_views> Number of Views from Common Gallery </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/commonGallery_details/$cID/$fldName/$fldVal/$DateFrom/$DateTo'> Number of Views from Common Gallery in Details </a>";
		
		//TOTAL NUMBER OF ROWS
		$data['cID'] 	 = $cID;
		$data['fieldName'] = $fldName;
		$data['fieldVal']  = $fldVal;
		$sort='n';
		extract($_POST);
		//COUNTRY
		$data['country_name'] = "Country: All Country";
		if($cID!=0){
			$sql = $this->db->query("SELECT countryName FROM country WHERE id = $cID LIMIT 0,1");
			$row = $sql->row();
			$data['country_name'] = "Country: ".$row->countryName;
		}	
		//FIELD SWITCHER
		$fValue=($fldVal==0) ? "Uncategorized" : $this->fieldSwitcher('fldValue',$fldName,$fldVal);
		$data['fldName'] = "(<i>".$this->fieldSwitcher('fldName',$fldName)." $fValue</i>)";		
		
		//COUNTRY ID
		$WHERE="WHERE $fldName = '$fldVal' AND cID != '0' AND publish='y' AND  pbrandID IN (SELECT brandID FROM commonGalleryBrands) AND forArchiving='n' AND forPurging='n' AND (itemAge <".$this->modules->itemsAge().") AND";
		if($fldVal=='ALL'){
			$WHERE="WHERE cID != '0' AND publish='y' AND pbrandID IN (SELECT brandID FROM commonGalleryBrands) AND forArchiving='n' AND forPurging='n' AND (itemAge <".$this->modules->itemsAge().") AND";
			$data['fldName'] = "";
		}if($cID!=0)
			$WHERE .= " cID = '$cID' AND publish='y' AND pbrandID IN (SELECT brandID FROM commonGalleryBrands) AND";
	
		$cond="";
		$having="";
		$limit =isset($selpage)? $selpage:0;
	
		$data['quarterStr'] = "";
		$data['DateFrom'] = "";
		$data['DateTo']   = "";
		$data['quarterStr'] = "";
		if((isset($DateFrom) AND isset($DateTo)) AND ($DateFrom!='' AND $DateTo!='') AND !isset($Reset)){
			$WHERE   .= " (dReleased >= '$DateFrom' AND dReleased <= '$DateTo') ";
			$data['DateFrom'] = $DateFrom;
			$data['DateTo']   = $DateTo;
			//DETECT QUARTER STRING
			if($DateFrom==$this->perQuarterMonths('prevMonth') & $DateTo==$this->perQuarterMonths('nextMonth'))
			 $data['quarterStr'] = $this->quaterStr('condition');
			//PREVIOUS QUARTER
			if($DateFrom==$this->setPreviousQTR($DateFrom,'prevMonth') & $DateTo==$this->setPreviousQTR($DateTo,'nextMonth'))
			 $data['quarterStr'] = $this->SetPrevQuaterStr($DateFrom);
			//PREVIOUS QUARTER
			if($DateFrom==$this->minMaxDatePublished('prevMonth') & $DateTo==$this->minMaxDatePublished('nextMonth'))
			 $data['quarterStr'] = $this->minMaxDatePublished('condition');
		}elseif(!$_POST){
			$WHERE   		   .= $this->minMaxDatePublished('filter');
			$data['quarterStr'] = $this->minMaxDatePublished('condition');
			$data['DateFrom']   = $this->minMaxDatePublished('prevMonth');
			$data['DateTo']     = $this->minMaxDatePublished('nextMonth');
		}
		
		if($_POST AND $DateFrom=='' AND $DateTo=='') $WHERE = substr($WHERE,0,-3);
		
		if(isset($Submit) OR isset($selpage) AND !isset($Reset))
		{	
			$val1 = mysql_real_escape_string($val1);
			$val1 = trim($val1);
			$val2 = mysql_real_escape_string($val2);
			$val2 = trim($val2);
			
			$condition = '';
			switch($cond1){
				case 'equal': 
					$condition = '=';
				break;
				case 'containing': 
					$condition = 'like';
				break;
				case 'in': 
					$condition = 'in';
				break;
				case 'greaterThan': 
					$condition = '>=';
				break;
				case 'lessThan': 
					$condition = '<=';
				break;
				case 'between': 
					$condition = 'between';
				break;
			}
			
			
			if(($opt1=='itemCode' OR $opt1=='itemName' OR $opt1=='pstatus' OR $opt1=='ptype' OR $opt1=='poutlet_status' OR $opt1=='ppremium_type' OR $opt1=='pmaterial' OR $opt1=='pbrand' OR $opt1=='full_name' OR $opt1=='pcountry' OR $opt1=='price_rangeName') AND $val1!='')
			{
				if($condition=='=')
					$cond = "  $opt1 $condition '$val1'";
				if($condition=='>=')
					$cond = "  $opt1 $condition '$val1'";
				if($condition=='<=')
					$cond = "  $opt1 $condition '$val1'";
				if($condition=='like')
					$cond = "  $opt1 $condition '%$val1%'";
				if($condition=='in')
					$cond = "  $opt1 $condition ('" . str_replace(",", "','", $val1) . "')";
				if($condition=='between' & $this->checkStr($val1)==TRUE)
					$cond = "  $opt1 $condition ".stripslashes($val1);
			}
			
			//PUBLISH
			if(($opt1=='publish' OR $opt1=='disapprove') AND $val1!='')
			{
				if($condition=='=')
					$val1 = ($val1=='yes' OR $val1=='Yes') ? 'y' : 'n';
					$cond = "  $opt1 $condition '$val1'";
				if($condition=='like')
					$cond = "  $opt1 $condition '%$val1%'";
			}
			
			
			//VIEWS
			if(($opt1=='num_views' OR $opt1=='UnitPrice' OR $opt1=='likes' OR $opt1=='wants' OR $opt1=='USD_Price') AND $val1!='' AND $condition!='like'){ 
				if($condition=='in' AND (is_numeric($val1) OR strpos($val1,',')==TRUE))
					$cond = "  $opt1 $condition ('" . str_replace(",", "','", $val1) . "')";
				elseif($condition!='in' AND is_numeric($val1))
					$cond = "  $opt1 $condition $val1";
				elseif($condition=='between' & $this->checkStr($val1)==TRUE)
					$cond = "  $opt1 $condition ".stripslashes($val1);
			}
		
			//dateReleased
			if($opt1=='dUploaded' AND $val1!='' AND $condition!='like' AND $condition!='between') 
				$cond = "  dUploaded $condition '$val1'";
			if($opt1=='dUploaded' AND $val1!='' AND $condition=='between' & $this->checkStr($val1)==TRUE) 
				$cond = "  dUploaded $condition ".stripslashes($val1);
			
			/*2ND SET*/
			switch($cond2){
				case 'equal': 
					$condition2 = '=';
				break;
				case 'containing': 
					$condition2 = 'like';
				break;
				case 'in': 
					$condition2 = 'in';
				break;
				case 'greaterThan': 
					$condition2 = '>=';
				break;
				case 'lessThan': 
					$condition2 = '<=';
				break;
				case 'between': 
					$condition2 = 'between';
				break;
			}
			
			//CHECK IF VAL1 IS SET
			if($val1=="")  $condition2 ="";
			
			if(($opt2=='itemCode' OR $opt2=='itemName' OR $opt2=='pstatus' OR $opt2=='ptype' OR $opt2=='poutlet_status' OR $opt2=='ppremium_type' OR $opt2=='pmaterial' OR $opt2=='pbrand' OR $opt2=='full_name' OR $opt2=='pcountry' OR $opt2=='price_rangeName') AND $val2!='')
			{
				if($condition2=='=')
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator $opt2 $condition2 '%$val2%'";
				if($condition2=='>=')
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='<=')
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='in')
					$cond .= " $operator $opt2 $condition2 ('" . str_replace(",", "','", $val2) . "')";
				if($condition2=='between'  & $this->checkStr($val2)==TRUE)
					$cond .= "  $operator $opt2 $condition2 ".stripslashes($val2);
			}
			
			
			//PUBLISH
			if(($opt2=='publish' OR $opt2=='disapprove') AND $val2!='')
			{
				if($condition2=='=')
					$val2 = ($val2=='yes' OR $val2=='Yes') ? 'y' : 'n';
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator $opt2 $condition2 '%$val2%'";
			}
			
			//LOCAL PRICE
			//VIEWS
			if(($opt2=='num_views' OR $opt2=='UnitPrice' OR $opt2=='likes' OR $opt2=='wants' OR $opt2=='USD_Price') AND $val2!='' AND $condition2!='like'){ 
				if($condition2=='in' AND (is_numeric($val2) OR strpos($val2,',')==TRUE))
					$cond .= " $operator $opt2 $condition2 ('" . str_replace(",", "','", $val2) . "')";
				elseif($condition2!='in' AND is_numeric($val2))
					$cond .= " $operator $opt2 $condition2 $val2";
				elseif($condition2=='between' & $this->checkStr($val2)==TRUE)
					$cond .= "  $operator $opt2 $condition2 ".stripslashes($val2);
			}
	
				
			//dUploaded
			if($opt2=='dUploaded' AND $val2!='' AND $condition2!='like' AND $condition2!='between') 
				$cond .= " $operator dUploaded $condition2 '$val2'";
			if($opt2=='dUploaded' AND $val2!='' AND $condition2=='between' & $this->checkStr($val2)==TRUE) 
				$cond .= "  $operator dUploaded $condition2 ".stripslashes($val2);
				
			$cond = ($cond=="") ? "" : " AND ($cond) ";
			$WHERE  .=  " $cond ";
		}
		
		
		//IF THEIR NO POSSIBLE RESULT
		$valid_query=TRUE;
		if(isset($Submit)){
			if($cond=="" AND ($val1!='' OR $val2!='')){ 
			$valid_query=FALSE;
			$WHERE = "WHERE itemID=0";
			}
		}
		
		$data['POST']      = $_POST;
		if(isset($Reset)){
			$WHERE = substr($WHERE,0,-3);
			$data['POST'] = array();
			$data['quarterStr'] = $this->minMaxDatePublished('condition');
			$data['DateFrom'] = $this->minMaxDatePublished('prevMonth');
			$data['DateTo']   = $this->minMaxDatePublished('nextMonth');
		}
		
		//ORDER
		$ORDER 			= $this->sortingRef('0-D','query');
		$data['order']  = $this->sortingRef('0-D','Orig_code');
		$order_code 	= '0-D';
		$label 			= "Views";
		if(isset($order)){
			$ORDER = $this->sortingRef($order,'query');
			$data['order'] = $this->sortingRef($order,'Orig_code');
			$order_code = $order;
			$label = $this->sortingRef($order,'label');
		}
		
		$sql = "SELECT itemID, num_views, likes, wants, itemCode, item_image, itemName, pstatus, ptype, poutlet_status, ppremium_type, pmaterial, pbrand, full_name, cName, publish, UnitPrice, USD_Price,price_rangeName, dUploaded, dReleased
				FROM item_db_reports		 
				$WHERE ORDER BY $ORDER";
				
		$sql_csv = $this->db->query("SELECT cName as Country, num_views as Views, likes as Likes, wants as Wants, itemCode as Item_Code, itemName as Item_Name, pstatus as Status, ptype as Type, poutlet_status as Outlet_Status, 
									 ppremium_type as Premium_Type, pmaterial as Material_Type, pbrand as Brand, full_name as User, publish as Publish, UnitPrice, USD_Price, price_rangeName as Price_Category, dUploaded as Date_Uploaded, dReleased as Date_Released
									 FROM item_db_reports		 
									 $WHERE ORDER BY $ORDER");
		
		$all_items = "";
		//generate csv file
		$csv  = "Number of Views from Common Gallery in Details\n";
		$csv .= "No, Views, Likes, Wants,  Item Code,  Item Name, Status, Type, Outlet Status, Premium Type, Material Type, Brand, User,Country, Publish, UnitPrice, USD Price, Price Category, Date Uploaded, Date Released\n";
		$sql_csv = $sql_csv->result_array(); $x=0;
		foreach($sql_csv as $s)
		{ extract($s); $x++;
		  $Publish 	  	 = ($Publish=='y')    ? "Yes" : "No";
		  $Date_Uploaded =  $this->convertDate('date',"$Date_Uploaded 00:00:00");
		  $Date_Released =  $this->convertDate('date',"$Date_Released 00:00:00");
		  $Item_Name     =  str_replace(",","-",$Item_Name);
		  $csv .= "$x, $Views, $Likes, $Wants, $Item_Code, $Item_Name, $Status, $Type, $Outlet_Status, $Premium_Type, $Material_Type, $Brand, $User, $Country, $Publish, $UnitPrice, $USD_Price, $Price_Category, $Date_Uploaded, $Date_Released\n";
		}
		write_file(getcwd()."/files/csv/Number_of_views_from_Common_Gallery_in_details".$this->reportCode().".csv",$csv);
		$data['csvFile']			= "Number_of_views_from_Common_Gallery_in_details".$this->reportCode().".csv";
	
		$all_items = $this->db->query($sql);
		$all_items = $all_items->result_array();
		
		//SORT STATUS
		$limit = ($sort)=='y' ? 0 : $limit;
		
	
		$limit_items = $this->db->query($sql." LIMIT $limit,20");
		$limit_items = $limit_items->result_array();
		$data['totrec'] = count($all_items);
		$data['limit']  = $limit;
		
		$items	 = $limit_items;
		
		$table= "<table id='large2' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:11px;' class='iLike_Result_Table2'>
				<tr style='height: 40px;'>
					<th style='width:25px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'>	   No.  	  		  				  					  </th> 
					<th style='width:67px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"0-A\")'>           <b>Views  	   	  </b></th> 
					<th style='width:65px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"1-D\")'>           <b>Likes  	  	  </b></th> 
					<th style='width:65px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"2-D\")'>           <b>Wants  	  	  </b></th> 
					<th style='width:120px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"3-A\")'>           <b>Item Code  	  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'>  <b>Image   	 	     							  	  </b></th> 
					<th style='width:120px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"4-A\")'>           <b>Item Name  	  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"5-A\")'>           <b>Status  	  	  </b></th> 
					<th style='width:98px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"6-A\")'>        	  <b>Type  	  		  </b></th> 
					<th style='width:153px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"7-D\")'>   	      <b>Outlet  	  	  </b></th> 
					<th style='width:125px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"8-D\")'>    		  <b>Premium  	  	  </b></th> 
					<th style='width:77px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"9-A\")'>        	  <b>Material  	  	  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"10-A\")'>          <b>Brand  	  	  </b></th> 
					<th style='width:120px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"11-A\")'>          <b>User  	  		  </b></th> 
					<th style='width:80px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"12-A\")'>          <b>Country  	  	  </b></th> 
					<th style='width:69px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"13-A\")'>          <b>Publish  	  	  </b></th>  
					<th style='width:60px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"14-A\")'>          <b>L. Price  	  	  </b></th> 
					<th style='width:60px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"15-A\")'>          <b>US. Price  	  </b></th> 
					<th style='width:60px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"20-A\")'>          <b>Price Category   </b></th> 
					<th style='width:90px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"16-A\")'>          <b>Uploaded  	  	  </b></th> 
					<th style='width:90px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"17-A\")'>          <b>Released  	  	  </b></th> 
				</tr>";
		
		//REPLACE ORDER
		$table = str_replace($order_code,$this->sortingRef($order_code,'Rev_code'),$table);
		$table = str_replace($label,$this->sortingRef($order_code,'label_symbol'),$table);
		
					$x = $limit;			
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					$orig_itemName="";
					if($valid_query==TRUE)
					{
					 foreach($items as $r) { 
					 extract($r);
					 $ptype = ($ptype=='') 	   ? 'Uncategorize' : $ptype;
					 $publish = ($publish=='y') ? 'Yes' : 'No';
					 $c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
					 $orig_itemName=$itemName;
					 if(strlen($itemName)>=15)
							$itemName = substr($itemName,0,15)."...";
							
					 $likes 			= ($likes=="") 		? 0 : $likes;
					 $wants 		    = ($wants=="") 		? 0 : $wants;
		$table.= "<tr>
				  <td $c>													$x </td>
				  <td $c>													<a onclick=\"viewDialog('item_database',$itemID)\" style='cursor:pointer;'><b>$num_views</b> </a> </td>
				  <td $c>													<a onclick=\"showVoters('iLike',$itemID)\" style='cursor:pointer;'><b>$likes</b></a>      																</td>
				  <td $c>													<a onclick=\"showVoters('iWant',$itemID)\" style='cursor:pointer;'><b>$wants</b></a>      																</td>
				  <td $c>													$itemCode      																	</td>
				  <td $c style='text-align:center;'>			    		<img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 	</td>
				  <td $c style='text-align:left;padding-left:5px;' title='$orig_itemName'>		<a href='".HTTP_PATH."gallery/itemInfo2/$itemID' target='_blank'>		$itemName</a>  </td>
				  <td $c style='text-align:left;padding-left:5px;'>		$pstatus																		</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$ptype																			</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$poutlet_status																	</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$ppremium_type																	</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$pmaterial																	    </td>
				  <td $c style='text-align:left;padding-left:5px;'>		$pbrand																	    	</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$full_name																		</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$cName																			</td>
				  <td $c style='text-align:center;'>						$publish																		</td>
				  <td $c style='text-align:center;'>						$UnitPrice																		</td>
				  <td $c style='text-align:center;'>						$USD_Price																		</td>
				  <td $c style='text-align:center;'>						$price_rangeName																		</td>
				  <td $c style='text-align:center;'>				       ". $this->convertDate('date',"$dUploaded 00:00:00") ."	</td>
				  <td $c style='text-align:center;'>				       ". $this->convertDate('date',"$dReleased 00:00:00") ."	</td>
				</tr>";}
				    }
					if(!$items OR $valid_query==FALSE)
						$table.=  "<tr><td colspan='21'>Sorry no items found, check your search parameters.</td></tr>";
		$table.= "</table>";	
		
		$data['table'] 		= $table;
		
		$viewer = 'innerPages';	    
		if($this->modules->access_checker()==TRUE)
		{
			$this->load->view($viewer,$data); 
		}else{
			$data['vfile']				= 'login.php';
			$data['title']				= 'SMBi System Log-in | SMBi';
			$data['page_title']			= 'SMBi System Log-in';
			$data['meta_description']	= 'San Miguel Brewing International';
			$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
			$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
			$this->load->view('login',$data); 	
		}
	}
	
	function myGallery_views($view='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(78,'REVIEW');
		//USER MANUAL
	    $data['USER_MANUAL'] = $this->modules->user_manual(43);
		
		//DETECT COUNTRY
		$cond = "";
		$WHERE="WHERE cID != 0 AND  publish='y' AND publish='y' AND forArchiving='n' AND forPurging='n' AND (itemAge <".$this->modules->itemsAge().")  ";
		$HAVING="";
		$data['cID']=0;
		$data['sa']=TRUE;
		if($_SESSION['super_admin']!='y' AND $_SESSION['countryID']!=0){
			$WHERE 		= " WHERE cID =".$_SESSION['countryID']." AND publish='y' AND publish='y' AND forArchiving='n' AND forPurging='n' AND (itemAge <".$this->modules->itemsAge().") ";
			$data['cID']= $_SESSION['countryID'];
			$data['sa']=FALSE;
		}
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/myGallery_views>  Number of Views from My Gallery   </a>';
		
		$table						= 'item_division';
		$data['vfile']				= 'myGallery_views.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//TOTAL NUMBER OF ROWS	
		$sort='n';
		extract($_POST);
		$data['DateFrom'] = "";
		$data['DateTo'] = "";
		$data['quarterStr'] = "";
		if((isset($DateFrom) AND isset($DateTo)) AND ($DateFrom!='' AND $DateTo!='') AND !isset($Reset)){
		$WHERE   .= "AND dReleased >= '$DateFrom' AND dReleased <= '$DateTo' ";
		$data['DateFrom'] = $DateFrom;
		$data['DateTo']   = $DateTo;
		$data['months']	  = $this->monthDiff($DateFrom,$DateTo);

		//DETECT QUARTER STRING
		if($DateFrom==$this->perQuarterMonths('prevMonth') & $DateTo==$this->perQuarterMonths('nextMonth'))
		 $data['quarterStr'] = $this->quaterStr('condition');
		//PREVIOUS QUARTER
		if($DateFrom==$this->setPreviousQTR($DateFrom,'prevMonth') & $DateTo==$this->setPreviousQTR($DateTo,'nextMonth'))
		 $data['quarterStr'] = $this->SetPrevQuaterStr($DateFrom);
		//PREVIOUS QUARTER
		if($DateFrom==$this->minMaxDatePublished('prevMonth') & $DateTo==$this->minMaxDatePublished('nextMonth'))
		 $data['quarterStr'] = $this->minMaxDatePublished('condition');
		}else{
		$WHERE   		   .= "AND ".$this->minMaxDatePublished('filter');
		$data['quarterStr'] = $this->minMaxDatePublished('condition');
		$data['months']	    = $this->monthDiff($this->minMaxDatePublished('prevMonth'),$this->minMaxDatePublished('nextMonth'));
		$data['DateFrom']   = $this->minMaxDatePublished('prevMonth');
		$data['DateTo']     = $this->minMaxDatePublished('nextMonth');
		}
		
		if(isset($Submit) OR isset($selpage))
		{	
			$val1 = mysql_real_escape_string($val1);
			$val1 = trim($val1);
			$val2 = mysql_real_escape_string($val2);
			$val2 = trim($val2);
			
			$condition = '';
			switch($cond1){
				case 'equal': 
					$condition = '=';
				break;
				case 'containing': 
					$condition = 'like';
				break;
				case 'in': 
					$condition = 'in';
				break;
				case 'greaterThan': 
					$condition = '>=';
				break;
				case 'lessThan': 
					$condition = '<=';
				break;
				case 'between': 
					$condition = 'between';
				break;
			}
			
			//COUNTRY NAME
			if(($opt1=='cName' OR $opt1=='pstatus' OR $opt1=='ptype' OR $opt1=='poutlet_status' OR $opt1=='ppremium_type' OR $opt1=='pmaterial' OR $opt1=='pbrand') AND $val1!='')
			{
				if($condition=='=')
					$cond = " AND  $opt1 $condition '$val1'";
				if($condition=='>=')
					$cond = " AND $opt1 $condition '$val1'";
				if($condition=='<=')
					$cond = " AND $opt1 $condition '$val1'";
				if($condition=='like')
					$cond = " AND $opt1 $condition '%$val1%'";
				if($condition=='in')
					$cond = " AND $opt1 $condition ('" . str_replace(",", "','", $val1) . "')";
			}

			//PUBLISH
			$field="";
			if($opt1=='uploaded')
				$field=" COUNT(itemID) ";
			if($opt1=='num_views')
				$field=" SUM(num_views)";
			if(($opt1=='uploaded' OR $opt1=='num_views') AND $val1!='' AND is_numeric($val1))
			{
				if($condition=='=')
					$HAVING = " HAVING $field $condition $val1 ";
				if($condition=='>=')
					$HAVING = " HAVING $field $condition $val1 ";
				if($condition=='<=')
					$HAVING = " HAVING $field $condition $val1 ";
				if($condition=='in' AND (is_numeric($val1) OR strpos($val1,',')==TRUE))
					$HAVING = "   HAVING $field $condition ('" . str_replace(",", "','", $val1) . "')";
			}
			
			if($condition=='between' & $this->checkStr($val1)==TRUE)
					$HAVING = " HAVING $field $condition ".stripslashes($val1);
			
			/*2ND SET*/
			switch($cond2){
				case 'equal': 
					$condition2 = '=';
				break;
				case 'containing': 
					$condition2 = 'like';
				break;
				case 'in': 
					$condition2 = 'in';
				break;
				case 'greaterThan': 
					$condition2 = '>=';
				break;
				case 'lessThan': 
					$condition2 = '<=';
				break;
				case 'between': 
					$condition2 = 'between';
				break;
			}
			
			//COUNTRY NAME
			if(($opt2=='cName' OR $opt2=='pstatus' OR $opt2=='ptype' OR $opt2=='poutlet_status' OR $opt2=='ppremium_type' OR $opt2=='pmaterial' OR $opt2=='pbrand') AND $val2!='')
			{
				if($condition2=='=')
					$cond .= " $operator  $opt2 $condition2 '$val2'";
				if($condition2=='>=')
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='<=')
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator $opt2 $condition2 '%$val2%'";
				if($condition2=='in')
					$cond .= " $operator $opt2 $condition2 ('" . str_replace(",", "','", $val2) . "')";
			}			
			
			$field="";
			if($opt2=='uploaded')
				$field=" COUNT(itemID) ";
			if($opt2=='num_views')
				$field=" SUM(num_views)";
			if(($opt2=='uploaded' OR $opt2=='num_views') AND $val2!='' AND (is_numeric($val2) OR $this->checkStr($val2)==TRUE))
			{	
				$HAVING .= ($HAVING=="") ? "HAVING" : "$operator";
				if($condition2=='=')
					$HAVING .= " $field $condition2 $val2 ";
				if($condition2=='>=')
					$HAVING .= " $field $condition2 $val2 ";
				if($condition2=='<=')
					$HAVING .= " $field $condition2 $val2 ";
				if($condition2=='in' AND (is_numeric($val2) OR strpos($val2,',')==TRUE))
					$HAVING .= "  $field $condition2 ('" . str_replace(",", "','", $val2) . "')";
			}		
			
			if($condition2=='between' & $this->checkStr($val2)==TRUE)
				$HAVING .= " $field $condition2 ".stripslashes($val2);
			
			$cond = ($cond=="") ? "" : " $cond ";
			$WHERE  .=  " $cond ";
		}
		
		//IF THEIR NO POSSIBLE RESULT
		$valid_query=TRUE;
		if(isset($Submit)){
			if(($cond=="" AND $HAVING=="") AND ($val1!='' OR $val2!='')){ 
			$valid_query=FALSE;
			$WHERE = " WHERE cID=-1";
			}
		}
		
	
		$data['POST']      = $_POST;
		if(isset($Reset)) $data['POST'] = array();
		
		//POSM STATUS;
		$arr = '';
		$sql_csv='';
		switch($view)
		{
		case '':
		case 'POSM_STATUS':
		$data['tab'] = 'POSM_STATUS';
		$q = "SELECT 
		     cName 			  as Country_Name, cID,
		     pstatus 		  as fldVal,
		     pStatusID 		  as fldID,
		     COUNT(itemID) 	  as Uploaded_Items , 
		     SUM(num_views) as myViews
		     FROM item_db_reports
		     $WHERE
		     GROUP BY cName, pstatus $HAVING ORDER BY pstatus ASC, cName ASC";
		$csv = "SELECT 
		     cName 			  as Country_Name, 
		     pstatus 		  as POSM_STATUS,
		     COUNT(itemID) 	  as Uploaded_Items , 
		     SUM(num_views) as myViews
		     FROM item_db_reports
		     $WHERE
		     GROUP BY cName, pstatus $HAVING ORDER BY pstatus ASC, cName ASC";
		$sql      = $this->db->query($q);
		$arr[]    = array('table'=>'POSM Status',
					   'fld'=>'pStatusID',
					   'rows'=>$sql->result_array());
		break;
		case 'POSM_TYPE':
		//POSM TYPE;
		$data['tab'] = 'POSM_TYPE';
		$q = "SELECT 
			   cName   			as Country_Name, cID,
			   ptype 			as fldVal,
			   ptypeID 			as fldID,
			   COUNT(itemID) 	  as Uploaded_Items , 
			   SUM(num_views) as myViews
			   FROM item_db_reports
			   $WHERE
			   GROUP BY cName, ptype $HAVING ORDER BY ptype ASC, cName ASC";
		$csv = "SELECT 
			   cName   			as Country_Name, 
			   ptype 			as POSM_TYPE,
			   COUNT(itemID) 	  as Uploaded_Items , 
			   SUM(num_views) as myViews
			   FROM item_db_reports
			   $WHERE
			   GROUP BY cName, ptype $HAVING ORDER BY ptype ASC, cName ASC";
		$sql   = $this->db->query($q);
		$arr[] = array('table'=>'POSM Type',
					   'fld'=>'ptypeID',
					   'rows'=>$sql->result_array());
		break;
		case 'OUTLET_TYPE':
		//OUTLET TYPE;
		$data['tab'] = 'OUTLET_TYPE';
		$q = "SELECT 
				cName   	  	 as Country_Name, cID, 
				poutlet_status 	 as fldVal,
				poutlet_statusID as fldID,
				COUNT(itemID) 	  as Uploaded_Items , 
				SUM(num_views) as myViews
				FROM item_db_reports
				$WHERE
				GROUP BY cName, poutlet_status $HAVING ORDER BY poutlet_status ASC, cName ASC";
		$csv = "SELECT 
				cName   	  	 as Country_Name, 
				poutlet_status 	 as POSM_STATUS,
				COUNT(itemID) 	  as Uploaded_Items , 
				SUM(num_views) as myViews
				FROM item_db_reports
				$WHERE
				GROUP BY cName, poutlet_status $HAVING ORDER BY poutlet_status ASC, cName ASC";
		$sql = $this->db->query($q);
		$arr[] = array('table'=>'OUTLET TYPE',
					   'fld'=>'poutlet_statusID',
					   'rows'=>$sql->result_array());
		break;
		case 'PREMIUM_TYPE':
		//PREMIUM TYPE;
		$data['tab'] = 'PREMIUM_TYPE';
		$q = "SELECT 
			   cName   	  		as Country_Name, cID,
			   ppremium_type 	as fldVal,
			   ppremium_typeID 	as fldID,
			   COUNT(itemID) 	  as Uploaded_Items , 
		       SUM(num_views) as myViews
			   FROM item_db_reports
			   $WHERE
			   GROUP BY cName, ppremium_type  $HAVING ORDER BY ppremium_type ASC, cName ASC";
		$csv = "SELECT 
			   cName   	  		as Country_Name, 
			   ppremium_type 	as PREMIUM_TYPE,
			   COUNT(itemID) 	  as Uploaded_Items , 
			   SUM(num_views) as myViews
			   FROM item_db_reports
			   $WHERE
			   GROUP BY cName, ppremium_type  $HAVING ORDER BY ppremium_type ASC, cName ASC";
		$sql   = $this->db->query($q);
		$arr[] = array('table'=>'PREMIUM Type',
					   'fld'=>'ppremium_typeID',
					   'rows'=>$sql->result_array());
		break;
		case 'MATERIAL_TYPE':
		//MATERIAL TYPE;
		$data['tab'] = 'MATERIAL_TYPE';
		$q = "SELECT 
			   cName   	  		as Country_Name, cID,
			   pmaterial 		as fldVal,
			   pmaterialID 		as fldID,
			   COUNT(itemID) 	  as Uploaded_Items , 
			   SUM(num_views) as myViews
			   FROM item_db_reports
			   $WHERE
			   GROUP BY cName, pmaterial  $HAVING ORDER BY pmaterial ASC, cName ASC";
		$csv = "SELECT 
			   cName   	  		as Country_Name,
			   pmaterial 		as MATERIAL_TYPE,
			   COUNT(itemID) 	  as Uploaded_Items , 
		       SUM(num_views) as myViews
			   FROM item_db_reports
			   $WHERE
			   GROUP BY cName, pmaterial  $HAVING ORDER BY pmaterial ASC, cName ASC";
		$sql   = $this->db->query($q);
		$arr[] = array('table'=>'MATERIAL Type',
					   'fld'=>'pmaterialID',
					   'rows'=>$sql->result_array());
		break;
		case 'BRAND_TYPE':
		//BRAND TYPE;
		$data['tab'] = 'BRAND_TYPE';
		$q="SELECT 
			  cName   	  		as Country_Name, cID,
			  pbrand 			as fldVal,
			  pbrandID 		 	as fldID,
			  COUNT(itemID) 	  as Uploaded_Items , 
		      SUM(num_views) as myViews
			  FROM item_db_reports
			  $WHERE
			  GROUP BY cName, pbrand  $HAVING ORDER BY pbrand ASC, cName ASC";
		$csv="SELECT 
			  cName   	  		as Country_Name,
			  pbrand 			as BRAND_TYPE,
			  COUNT(itemID) 	  as Uploaded_Items , 
		      SUM(num_views) as myViews
			  FROM item_db_reports
			  $WHERE
			  GROUP BY cName, pbrand  $HAVING ORDER BY pbrand ASC, cName ASC";
		$sql   = $this->db->query($q);
		$arr[] = array('table'=>'BRAND Type',
					   'fld'=>'pbrandID',
					   'rows'=>$sql->result_array());
		break;
		}
		
		
		$data['results'] = $arr;
		
		//generate csv file
		$this->generateCSVFile('item_views',$csv,"Number_of_Views_from_My_Gallery".$this->reportCode().".csv");
		$data['csvFile']			= "Number_of_Views_from_My_Gallery".$this->reportCode().".csv";
		
		$viewer = 'innerPages';	    
		if($this->modules->access_checker()==TRUE)
		{
			$this->load->view($viewer,$data); 
		}else{
			$data['vfile']				= 'login.php';
			$data['title']				= 'SMBi System Log-in | SMBi';
			$data['page_title']			= 'SMBi System Log-in';
			$data['meta_description']	= 'San Miguel Brewing International';
			$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
			$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
			$this->load->view('login',$data); 	
		}
	}
	
	function myGallery_details($cID='',$fldName='',$fldVal='',$DateFrom='',$DateTo='')
	{	
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(78,'REVIEW');
		
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(43);
		
		$table						= 'myGallery_details';
		$data['vfile']				= 'myGallery_details.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/myGallery_views> Number of Views from My Gallery </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/myGallery_details/$cID/$fldName/$fldVal/$DateFrom/$DateTo'> Number of Views from My Gallery in Details </a>";
		
		//TOTAL NUMBER OF ROWS
		$data['cID'] 	 = $cID;
		$data['fieldName'] = $fldName;
		$data['fieldVal']  = $fldVal;
		$sort='n';
		extract($_POST);
		//COUNTRY
		$data['country_name'] = "Country: All Country";
		if($cID!=0){
			$sql = $this->db->query("SELECT countryName FROM country WHERE id = $cID LIMIT 0,1");
			$row = $sql->row();
			$data['country_name'] = "Country: ".$row->countryName;
		}	
		//FIELD SWITCHER
		$fValue=($fldVal==0) ? "Uncategorized" : $this->fieldSwitcher('fldValue',$fldName,$fldVal);
		$data['fldName'] = "(<i>".$this->fieldSwitcher('fldName',$fldName)." $fValue</i>)";		
		
		//COUNTRY ID
		$WHERE="WHERE $fldName = '$fldVal' AND cID != '0' AND publish='y' AND forArchiving='n' AND forPurging='n' AND (itemAge <".$this->modules->itemsAge().") AND";
		if($fldVal=='ALL'){
			$WHERE="WHERE cID != '0' AND publish='y' AND forArchiving='n' AND forPurging='n' AND (itemAge <".$this->modules->itemsAge().") AND";
			$data['fldName'] = "";
		}if($cID!=0)
			$WHERE .= " cID = '$cID' AND publish='y' AND";
	
		$cond="";
		$having="";
		$limit =isset($selpage)? $selpage:0;
	
		$data['quarterStr'] = "";
		$data['DateFrom'] = "null";
		$data['DateTo']   = "null";
		
		if((isset($DateFrom) AND isset($DateTo)) AND ($DateFrom!='' AND $DateTo!='') AND !isset($Reset)){
			$WHERE   .= " (dReleased >= '$DateFrom' AND dReleased <= '$DateTo') ";
			$data['DateFrom'] = $DateFrom;
			$data['DateTo']   = $DateTo;
			//DETECT QUARTER STRING
			if($DateFrom==$this->perQuarterMonths('prevMonth') & $DateTo==$this->perQuarterMonths('nextMonth'))
			 $data['quarterStr'] = $this->quaterStr('condition');
			//PREVIOUS QUARTER
			if($DateFrom==$this->setPreviousQTR($DateFrom,'prevMonth') & $DateTo==$this->setPreviousQTR($DateTo,'nextMonth'))
			 $data['quarterStr'] = $this->SetPrevQuaterStr($DateFrom);
			//PREVIOUS QUARTER
			if($DateFrom==$this->minMaxDatePublished('prevMonth') & $DateTo==$this->minMaxDatePublished('nextMonth'))
			 $data['quarterStr'] = $this->minMaxDatePublished('condition');
		}elseif(!$_POST){
			$WHERE   		   .= $this->minMaxDatePublished('filter');
			$data['quarterStr'] = $this->minMaxDatePublished('condition');
			$data['DateFrom']   = $this->minMaxDatePublished('prevMonth');
			$data['DateTo']     = $this->minMaxDatePublished('nextMonth');
		}
		
		if($_POST AND $DateFrom=='' AND $DateTo=='') $WHERE = substr($WHERE,0,-3);
		
		if(isset($Submit) OR isset($selpage) AND !isset($Reset))
		{	
			$val1 = mysql_real_escape_string($val1);
			$val1 = trim($val1);
			$val2 = mysql_real_escape_string($val2);
			$val2 = trim($val2);
			
			$condition = '';
			switch($cond1){
				case 'equal': 
					$condition = '=';
				break;
				case 'containing': 
					$condition = 'like';
				break;
				case 'in': 
					$condition = 'in';
				break;
				case 'greaterThan': 
					$condition = '>=';
				break;
				case 'lessThan': 
					$condition = '<=';
				break;
				case 'between': 
					$condition = 'between';
				break;
			}
			
			
			if(($opt1=='itemCode' OR $opt1=='itemName' OR $opt1=='pstatus' OR $opt1=='ptype' OR $opt1=='poutlet_status' OR $opt1=='ppremium_type' OR $opt1=='pmaterial' OR $opt1=='pbrand' OR $opt1=='full_name' OR $opt1=='pcountry' OR $opt1=='price_rangeName') AND $val1!='')
			{
				if($condition=='=')
					$cond = "  $opt1 $condition '$val1'";
				if($condition=='>=')
					$cond = "  $opt1 $condition '$val1'";
				if($condition=='<=')
					$cond = "  $opt1 $condition '$val1'";
				if($condition=='like')
					$cond = "  $opt1 $condition '%$val1%'";
				if($condition=='in')
					$cond = "  $opt1 $condition ('" . str_replace(",", "','", $val1) . "')";
				if($condition=='between' & $this->checkStr($val1)==TRUE)
					$cond = "  $opt1 $condition ".stripslashes($val1);
			}
			
			//PUBLISH
			if(($opt1=='publish' OR $opt1=='disapprove') AND $val1!='')
			{
				if($condition=='=')
					$val1 = ($val1=='yes' OR $val1=='Yes') ? 'y' : 'n';
					$cond = "  $opt1 $condition '$val1'";
				if($condition=='like')
					$cond = "  $opt1 $condition '%$val1%'";
			}
			
			
			//VIEWS
			if(($opt1=='num_views' OR $opt1=='UnitPrice' OR $opt1=='likes' OR $opt1=='wants' OR $opt1=='USD_Price') AND $val1!='' AND $condition!='like'){ 
				if($condition=='in' AND (is_numeric($val1) OR strpos($val1,',')==TRUE))
					$cond = "  $opt1 $condition ('" . str_replace(",", "','", $val1) . "')";
				elseif($condition!='in' AND is_numeric($val1))
					$cond = "  $opt1 $condition $val1";
				elseif($condition=='between' & $this->checkStr($val1)==TRUE)
					$cond = "  $opt1 $condition ".stripslashes($val1);
			}
		
			//dateReleased
			if($opt1=='dUploaded' AND $val1!='' AND $condition!='like' AND $condition!='between') 
				$cond = "  dUploaded $condition '$val1'";
			if($opt1=='dUploaded' AND $val1!='' AND $condition=='between' & $this->checkStr($val1)==TRUE) 
				$cond = "  dUploaded $condition ".stripslashes($val1);
			
			/*2ND SET*/
			switch($cond2){
				case 'equal': 
					$condition2 = '=';
				break;
				case 'containing': 
					$condition2 = 'like';
				break;
				case 'in': 
					$condition2 = 'in';
				break;
				case 'greaterThan': 
					$condition2 = '>=';
				break;
				case 'lessThan': 
					$condition2 = '<=';
				break;
				case 'between': 
					$condition2 = 'between';
				break;
			}
			
			//CHECK IF VAL1 IS SET
			if($val1=="")  $condition2 ="";
			
			if(($opt2=='itemCode' OR $opt2=='itemName' OR $opt2=='pstatus' OR $opt2=='ptype' OR $opt2=='poutlet_status' OR $opt2=='ppremium_type' OR $opt2=='pmaterial' OR $opt2=='pbrand' OR $opt2=='full_name' OR $opt2=='pcountry' OR $opt2=='price_rangeName') AND $val2!='')
			{
				if($condition2=='=')
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator $opt2 $condition2 '%$val2%'";
				if($condition2=='>=')
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='<=')
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='in')
					$cond .= " $operator $opt2 $condition2 ('" . str_replace(",", "','", $val2) . "')";
				if($condition2=='between'  & $this->checkStr($val2)==TRUE)
					$cond .= "  $operator $opt2 $condition2 ".stripslashes($val2);
			}
			
			
			//PUBLISH
			if(($opt2=='publish' OR $opt2=='disapprove') AND $val2!='')
			{
				if($condition2=='=')
					$val2 = ($val2=='yes' OR $val2=='Yes') ? 'y' : 'n';
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator $opt2 $condition2 '%$val2%'";
			}
			
			//LOCAL PRICE
			//VIEWS
			if(($opt2=='num_views' OR $opt2=='UnitPrice' OR $opt2=='likes' OR $opt2=='wants' OR $opt2=='USD_Price') AND $val2!='' AND $condition2!='like'){ 
				if($condition2=='in' AND (is_numeric($val2) OR strpos($val2,',')==TRUE))
					$cond .= " $operator $opt2 $condition2 ('" . str_replace(",", "','", $val2) . "')";
				elseif($condition2!='in' AND is_numeric($val2))
					$cond .= " $operator $opt2 $condition2 $val2";
				elseif($condition2=='between' & $this->checkStr($val2)==TRUE)
					$cond .= "  $operator $opt2 $condition2 ".stripslashes($val2);
			}
	
				
			//dUploaded
			if($opt2=='dUploaded' AND $val2!='' AND $condition2!='like' AND $condition2!='between') 
				$cond .= " $operator dUploaded $condition2 '$val2'";
			if($opt2=='dUploaded' AND $val2!='' AND $condition2=='between' & $this->checkStr($val2)==TRUE) 
				$cond .= "  $operator dUploaded $condition2 ".stripslashes($val2);
				
			$cond = ($cond=="") ? "" : " AND ($cond) ";
			$WHERE  .=  " $cond ";
		}
		
		
		//IF THEIR NO POSSIBLE RESULT
		$valid_query=TRUE;
		if(isset($Submit)){
			if($cond=="" AND ($val1!='' OR $val2!='')){ 
			$valid_query=FALSE;
			$WHERE = "WHERE itemID=0";
			}
		}
		
		$data['POST']      = $_POST;
		if(isset($Reset)){
			$WHERE = substr($WHERE,0,-3);
			$data['POST'] = array();
			$data['quarterStr'] = $this->minMaxDatePublished('condition');
			$data['DateFrom'] = $this->minMaxDatePublished('prevMonth');
			$data['DateTo']   = $this->minMaxDatePublished('nextMonth');
		}
		
		//ORDER
		$ORDER 			= $this->sortingRef('0-D','query');
		$data['order']  = $this->sortingRef('0-D','Orig_code');
		$order_code 	= '0-D';
		$label 			= "Views";
		if(isset($order)){
			$ORDER = $this->sortingRef($order,'query');
			$data['order'] = $this->sortingRef($order,'Orig_code');
			$order_code = $order;
			$label = $this->sortingRef($order,'label');
		}	
		
		$sql = "SELECT itemID, num_views, likes, wants, itemCode, item_image, itemName, pstatus, ptype, poutlet_status, ppremium_type, pmaterial, pbrand, full_name, cName, publish, UnitPrice, USD_Price,price_rangeName, dUploaded, dReleased
				FROM item_db_reports		 
				$WHERE ORDER BY $ORDER";
				
		$sql_csv = $this->db->query("SELECT cName as Country, num_views as Views, likes as Likes, wants as Wants, itemCode as Item_Code, itemName as Item_Name, pstatus as Status, ptype as Type, poutlet_status as Outlet_Status, 
									 ppremium_type as Premium_Type, pmaterial as Material_Type, pbrand as Brand, full_name as User, publish as Publish, UnitPrice, USD_Price, price_rangeName as Price_Category, dUploaded as Date_Uploaded, dReleased as Date_Released
									 FROM item_db_reports		 
									 $WHERE ORDER BY $ORDER");
		//generate csv file
		$csv  = "Number of Views from My Gallery in Details\n";
		$csv .= "No, Views, Likes, Wants, Item Code,  Item Name, Status, Type, Outlet Status, Premium Type, Material Type, Brand, User, Country, Publish, UnitPrice, USD Price, Price Category, Date Uploaded, Date Released\n";
		$sql_csv = $sql_csv->result_array(); $x=0;
		foreach($sql_csv as $s)
		{ extract($s); $x++;
		  $Publish 	  	 = ($Publish=='y')    ? "Yes" : "No";
		  $Date_Uploaded =  $this->convertDate('date',"$Date_Uploaded 00:00:00");
		  $Date_Released =  $this->convertDate('date',"$Date_Released 00:00:00");
		  $Item_Name     =  str_replace(",","-",$Item_Name);
		  $csv .= "$x, $Views, $Likes, $Wants, $Item_Code, $Item_Name, $Status, $Type, $Outlet_Status, $Premium_Type, $Material_Type, $Brand, $User, $Country, $Publish,  $UnitPrice, $USD_Price, $Price_Category, $Date_Uploaded, $Date_Released\n";
		}
	    write_file(getcwd()."/files/csv/Number_of_Views_from_My_Gallery_in_Details".$this->reportCode().".csv",$csv);
		$data['csvFile']			= "Number_of_Views_from_My_Gallery_in_Details".$this->reportCode().".csv";
		
		$all_items = $this->db->query($sql);
		$all_items = $all_items->result_array();
		
		//SORT STATUS
		$limit = ($sort)=='y' ? 0 : $limit;
		
	
		$limit_items = $this->db->query($sql." LIMIT $limit,20");
		$limit_items = $limit_items->result_array();
		$data['totrec'] = count($all_items);
		$data['limit']  = $limit;
		
		$items	 = $limit_items;
		
		$table= "<table id='large2' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:11px;' class='iLike_Result_Table2'>
				<tr style='height: 40px;'>
					<th style='width:25px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'>	   No.  	  		  				  					  </th> 
					<th style='width:67px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"0-A\")'>           <b>Views  	   	  </b></th> 
					<th style='width:65px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"1-D\")'>           <b>Likes  	  	  </b></th> 
					<th style='width:65px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"2-D\")'>           <b>Wants  	  	  </b></th> 
					<th style='width:120px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"3-A\")'>           <b>Item Code  	  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'>  <b>Image   	 	     							  	  </b></th> 
					<th style='width:120px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"4-A\")'>           <b>Item Name  	  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"5-A\")'>           <b>Status  	  	  </b></th> 
					<th style='width:98px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"6-A\")'>        	  <b>Type  	  		  </b></th> 
					<th style='width:153px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"7-D\")'>   	      <b>Outlet  	  	  </b></th> 
					<th style='width:125px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"8-D\")'>    		  <b>Premium  	  	  </b></th> 
					<th style='width:77px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"9-A\")'>        	  <b>Material  	  	  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"10-A\")'>          <b>Brand  	  	  </b></th> 
					<th style='width:120px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"11-A\")'>          <b>User  	  		  </b></th> 
					<th style='width:80px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"12-A\")'>          <b>Country  	  	  </b></th> 
					<th style='width:69px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"13-A\")'>          <b>Publish  	  	  </b></th>  
					<th style='width:60px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"14-A\")'>          <b>L. Price  	  	  </b></th> 
					<th style='width:60px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"15-A\")'>          <b>US. Price  	  </b></th>
					<th style='width:60px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"20-A\")'>          <b>Price Category   </b></th> 
					<th style='width:90px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"16-A\")'>          <b>Uploaded  	  	  </b></th> 
					<th style='width:90px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"17-A\")'>          <b>Released  	  	  </b></th> 
				</tr>";
		
		//REPLACE ORDER
		$table = str_replace($order_code,$this->sortingRef($order_code,'Rev_code'),$table);
		$table = str_replace($label,$this->sortingRef($order_code,'label_symbol'),$table);
		
					$x = $limit;			
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					$orig_itemName="";
					if($valid_query==TRUE)
					{
					 foreach($items as $r) { 
					 extract($r);
					 $ptype = ($ptype=='') 	   ? 'Uncategorize' : $ptype;
					 $publish = ($publish=='y') ? 'Yes' : 'No';
					 $c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
					 $orig_itemName=$itemName;
					 if(strlen($itemName)>=15)
							$itemName = substr($itemName,0,15)."...";
							
					 $likes 			= ($likes=="") 		? 0 : $likes;
					 $wants 		    = ($wants=="") 		? 0 : $wants;
		$table.= "<tr>
				  <td $c>													$x </td>
				  <td $c>													<a onclick=\"viewDialog('item_database',$itemID)\" style='cursor:pointer;'><b>$num_views</b> </a> </td>
				  <td $c>													<a onclick=\"showVoters('iLike',$itemID)\" style='cursor:pointer;'><b>$likes</b></a>      																</td>
				  <td $c>													<a onclick=\"showVoters('iWant',$itemID)\" style='cursor:pointer;'><b>$wants</b></a>      																</td>
				  <td $c>													$itemCode      																	</td>
				  <td $c style='text-align:center;'>			    		<img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 	</td>
				  <td $c style='text-align:left;padding-left:5px;' title='$orig_itemName'>		<a href='".HTTP_PATH."gallery/itemInfo2/$itemID' target='_blank'>		$itemName</a>  </td>
				  <td $c style='text-align:left;padding-left:5px;'>		$pstatus																		</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$ptype																			</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$poutlet_status																	</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$ppremium_type																	</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$pmaterial																	    </td>
				  <td $c style='text-align:left;padding-left:5px;'>		$pbrand																	    	</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$full_name																		</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$cName																			</td>
				  <td $c style='text-align:center;'>						$publish																		</td>
				  <td $c style='text-align:center;'>						$UnitPrice																		</td>
				  <td $c style='text-align:center;'>						$USD_Price																		</td>
				  <td $c style='text-align:center;'>						$price_rangeName																		</td>
				  <td $c style='text-align:center;'>				        ". $this->convertDate('date',"$dUploaded 00:00:00") ."	</td>
				  <td $c style='text-align:center;'>				        ". $this->convertDate('date',"$dReleased 00:00:00") ."	</td>
				</tr>";}
				    }
					if(!$items OR $valid_query==FALSE)
						$table.=  "<tr><td colspan='21'>Sorry no items found, check your search parameters.</td></tr>";
		$table.= "</table>";	
		
		$data['table'] 		= $table;
		
		$viewer = 'innerPages';	    
		if($this->modules->access_checker()==TRUE)
		{
			$this->load->view($viewer,$data); 
		}else{
			$data['vfile']				= 'login.php';
			$data['title']				= 'SMBi System Log-in | SMBi';
			$data['page_title']			= 'SMBi System Log-in';
			$data['meta_description']	= 'San Miguel Brewing International';
			$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
			$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
			$this->load->view('login',$data); 	
		}
	}
	
	function minMaxDatePublished($type='')
	{
		//GET CURRENT
		$curDate = date('Y-m-d');
		if($_SESSION['super_admin']!='y' AND $_SESSION['countryID']!=0){
			$sql = $this->db->query("SELECT min(dateReleased) AS minDate FROM items WHERE countryID =".$_SESSION['countryID'] ." AND publish='y' LIMIT 0,1");
		}else{
			$sql = $this->db->query("SELECT min(dateReleased) AS minDate FROM items WHERE countryID != 0 AND publish='y' LIMIT 0,1");
		}
		$sql	   = $sql->row();
		$prevMonth = $sql->minDate;
		
		if($_SESSION['super_admin']!='y' AND $_SESSION['countryID']!=0){
			$sql = $this->db->query("SELECT max(dateReleased) AS maxDate FROM items WHERE countryID =".$_SESSION['countryID'] ." AND publish='y' LIMIT 0,1");
		}else{
			$sql = $this->db->query("SELECT max(dateReleased) AS maxDate FROM items WHERE countryID != 0 AND publish='y' LIMIT 0,1");
		}
		$sql	   = $sql->row();
		$nextMonth = $sql->maxDate;
		
		$str = "";
		if($type=='prevMonth')
			$str = "$prevMonth";
		if($type=='nextMonth')
			$str = $nextMonth;
		if($type=='filter')
			$str = " (dReleased BETWEEN '$prevMonth' AND '$nextMonth') ";
		if($type=='condition')
		    $str = "Report as of Today: $curDate";
		
		return $str;
	}
	
	function checkStr($str='')
	{
		if(strpos($str, 'and')==TRUE OR strpos($str, 'And')==TRUE OR strpos($str, 'AND')==TRUE OR strpos($str, '&')==TRUE)
		 return TRUE;
		else
		 return FALSE;
	}
	
	function item_views($action='',$id='')
	{	
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(70,'REVIEW');
		$csv = "";
		//print_r($_POST);
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(43);
		
		$filter_WHERE="";
		if($_SESSION['super_admin']!='y'  AND $_SESSION['countryID']!=0)
			$filter_WHERE = "WHERE cID =".$_SESSION['countryID']." ";
		
		//$votingCampaignID 	= $id;
		
		$table						= 'item_views';
		$data['vfile']				= 'item_views.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/item_views>  Number of Views per Item  </a>';
		
		//TOTAL NUMBER OF ROWS	
		$sort='n';
		extract($_POST);
		
		$WHERE="";
		if($_SESSION['super_admin']!='y' AND $_SESSION['countryID']!=0){
			$WHERE 		= " WHERE (cID =".$_SESSION['countryID'] ." AND publish='y') AND forArchiving='n' AND forPurging='n' AND (itemAge <".$this->modules->itemsAge().") AND";
		}else{
			$WHERE 		= " WHERE (cID != 0 AND  publish='y') AND forArchiving='n' AND forPurging='n' AND (itemAge < ".$this->modules->itemsAge().") AND";
		}
		
		$cond="";
		$having="";
		$limit =isset($selpage)? $selpage:0;
	
		$data['quarterStr'] = "";
		$data['DateFrom'] = "";
		$data['DateTo'] = "";
		//print_r($_POST);
		if((isset($DateFrom) AND isset($DateTo)) AND ($DateFrom!='' AND $DateTo!='') AND !isset($Reset)){
			$WHERE   .= " (dReleased >= '$DateFrom' AND dReleased <= '$DateTo') ";
			$data['DateFrom'] = $DateFrom;
			$data['DateTo']   = $DateTo;
			//DETECT QUARTER STRING
			if($DateFrom==$this->perQuarterMonths('prevMonth') & $DateTo==$this->perQuarterMonths('nextMonth'))
			 $data['quarterStr'] = $this->quaterStr('condition');
			//PREVIOUS QUARTER
			if($DateFrom==$this->setPreviousQTR($DateFrom,'prevMonth') & $DateTo==$this->setPreviousQTR($DateTo,'nextMonth'))
			 $data['quarterStr'] = $this->SetPrevQuaterStr($DateFrom);
			//PREVIOUS QUARTER
			if($DateFrom==$this->minMaxDatePublished('prevMonth') & $DateTo==$this->minMaxDatePublished('nextMonth'))
			 $data['quarterStr'] = $this->minMaxDatePublished('condition');
		}elseif(!$_POST){
			$WHERE   		   .= $this->minMaxDatePublished('filter');
			$data['quarterStr'] = $this->minMaxDatePublished('condition');
			$data['DateFrom']   = $this->minMaxDatePublished('prevMonth');
			$data['DateTo']     = $this->minMaxDatePublished('nextMonth');
		}
		
		if($_POST AND $DateFrom=='' AND $DateTo=='') $WHERE = substr($WHERE,0,-3);
		
		if(isset($Submit) OR isset($selpage) AND !isset($Reset))
		{	
			$val1 = mysql_real_escape_string($val1);
			$val1 = trim($val1);
			$val2 = mysql_real_escape_string($val2);
			$val2 = trim($val2);
			
			$condition = '';
			switch($cond1){
				case 'equal': 
					$condition = '=';
				break;
				case 'containing': 
					$condition = 'like';
				break;
				case 'in': 
					$condition = 'in';
				break;
				case 'greaterThan': 
					$condition = '>=';
				break;
				case 'lessThan': 
					$condition = '<=';
				break;
				case 'between': 
					$condition = 'between';
				break;
			}
			
			
			if(($opt1=='itemCode' OR $opt1=='itemName' OR $opt1=='pstatus' OR $opt1=='ptype' OR $opt1=='poutlet_status' OR $opt1=='ppremium_type' OR $opt1=='pmaterial' OR $opt1=='pbrand' OR $opt1=='full_name' OR $opt1=='pcountry' OR $opt1=='price_rangeName') AND $val1!='')
			{
				if($condition=='=')
					$cond = "  $opt1 $condition '$val1'";
				if($condition=='>=')
					$cond = "  $opt1 $condition '$val1'";
				if($condition=='<=')
					$cond = "  $opt1 $condition '$val1'";
				if($condition=='like')
					$cond = "  $opt1 $condition '%$val1%'";
				if($condition=='in')
					$cond = "  $opt1 $condition ('" . str_replace(",", "','", $val1) . "')";
				if($condition=='between' & $this->checkStr($val1)==TRUE)
					$cond = "  $opt1 $condition ".stripslashes($val1);
			}
			
			//PUBLISH
			if(($opt1=='publish' OR $opt1=='disapprove') AND $val1!='')
			{
				if($condition=='=')
					$val1 = ($val1=='yes' OR $val1=='Yes') ? 'y' : 'n';
					$cond = "  $opt1 $condition '$val1'";
				if($condition=='like')
					$cond = "  $opt1 $condition '%$val1%'";
			}
			
			
			//VIEWS
			if(($opt1=='num_views' OR $opt1=='UnitPrice' OR $opt1=='likes' OR $opt1=='wants' OR $opt1=='USD_Price') AND $val1!='' AND $condition!='like'){ 
				if($condition=='in' AND (is_numeric($val1) OR strpos($val1,',')==TRUE))
					$cond = "  $opt1 $condition ('" . str_replace(",", "','", $val1) . "')";
				elseif($condition!='in' AND is_numeric($val1))
					$cond = "  $opt1 $condition $val1";
				elseif($condition=='between' & $this->checkStr($val1)==TRUE)
					$cond = "  $opt1 $condition ".stripslashes($val1);
			}
		
			//dateReleased
			if($opt1=='dUploaded' AND $val1!='' AND $condition!='like' AND $condition!='between') 
				$cond = "  dUploaded $condition '$val1'";
			if($opt1=='dUploaded' AND $val1!='' AND $condition=='between' & $this->checkStr($val1)==TRUE) 
				$cond = "  dUploaded $condition ".stripslashes($val1);
			
			/*2ND SET*/
			switch($cond2){
				case 'equal': 
					$condition2 = '=';
				break;
				case 'containing': 
					$condition2 = 'like';
				break;
				case 'in': 
					$condition2 = 'in';
				break;
				case 'greaterThan': 
					$condition2 = '>=';
				break;
				case 'lessThan': 
					$condition2 = '<=';
				break;
				case 'between': 
					$condition2 = 'between';
				break;
			}
			
			//CHECK IF VAL1 IS SET
			if($val1=="")  $condition2 ="";
			
			if(($opt2=='itemCode' OR $opt2=='itemName' OR $opt2=='pstatus' OR $opt2=='ptype' OR $opt2=='poutlet_status' OR $opt2=='ppremium_type' OR $opt2=='pmaterial' OR $opt2=='pbrand' OR $opt2=='full_name' OR $opt2=='pcountry' OR $opt2=='price_rangeName') AND $val2!='')
			{
				if($condition2=='=')
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator $opt2 $condition2 '%$val2%'";
				if($condition2=='>=')
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='<=')
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='in')
					$cond .= " $operator $opt2 $condition2 ('" . str_replace(",", "','", $val2) . "')";
				if($condition2=='between'  & $this->checkStr($val2)==TRUE)
					$cond .= "  $operator $opt2 $condition2 ".stripslashes($val2);
			}
			
			
			//PUBLISH
			if(($opt2=='publish' OR $opt2=='disapprove') AND $val2!='')
			{
				if($condition2=='=')
					$val2 = ($val2=='yes' OR $val2=='Yes') ? 'y' : 'n';
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator $opt2 $condition2 '%$val2%'";
			}
			
			//LOCAL PRICE
			//VIEWS
			if(($opt2=='num_views' OR $opt2=='UnitPrice' OR $opt2=='likes' OR $opt2=='wants' OR $opt2=='USD_Price') AND $val2!='' AND $condition2!='like'){ 
				if($condition2=='in' AND (is_numeric($val2) OR strpos($val2,',')==TRUE))
					$cond .= " $operator $opt2 $condition2 ('" . str_replace(",", "','", $val2) . "')";
				elseif($condition2!='in' AND is_numeric($val2))
					$cond .= " $operator $opt2 $condition2 $val2";
				elseif($condition2=='between' & $this->checkStr($val2)==TRUE)
					$cond .= "  $operator $opt2 $condition2 ".stripslashes($val2);
			}
	
				
			//dUploaded
			if($opt2=='dUploaded' AND $val2!='' AND $condition2!='like' AND $condition2!='between') 
				$cond .= " $operator dUploaded $condition2 '$val2'";
			if($opt2=='dUploaded' AND $val2!='' AND $condition2=='between' & $this->checkStr($val2)==TRUE) 
				$cond .= "  $operator dUploaded $condition2 ".stripslashes($val2);
				
			$cond = ($cond=="") ? "" : " AND ($cond) ";
			$WHERE  .=  " $cond ";
		}
		
		
		//IF THEIR NO POSSIBLE RESULT
		$valid_query=TRUE;
		if(isset($Submit)){
			if($cond=="" AND ($val1!='' OR $val2!='')){ 
			$valid_query=FALSE;
			$WHERE = "WHERE itemID=0";
			}
		}
		
		$data['POST']      = $_POST;
		if(isset($Reset)){
			$WHERE = substr($WHERE,0,-3);
			$data['POST'] = array();
			$data['quarterStr'] = $this->minMaxDatePublished('condition');
			$data['DateFrom'] = $this->minMaxDatePublished('prevMonth');
			$data['DateTo']   = $this->minMaxDatePublished('nextMonth');
		}
		
		//ORDER
		$ORDER 			= $this->sortingRef('0-D','query');
		$data['order']  = $this->sortingRef('0-D','Orig_code');
		$order_code 	= '0-D';
		$label 			= "Views";
		if(isset($order)){
			$ORDER = $this->sortingRef($order,'query');
			$data['order'] = $this->sortingRef($order,'Orig_code');
			$order_code = $order;
			$label = $this->sortingRef($order,'label');
		}	
		
		$sql = "SELECT itemID, num_views, likes, wants, itemCode, item_image, itemName, pstatus, ptype, poutlet_status, ppremium_type, pmaterial, pbrand, full_name, cName, publish, UnitPrice, USD_Price,price_rangeName, dUploaded, dReleased
				FROM item_db_reports		 
				$WHERE ORDER BY $ORDER";
				
		$sql_csv = $this->db->query("SELECT cName as Country, num_views as Views, likes as Likes, wants as Wants, itemCode as Item_Code, itemName as Item_Name, pstatus as Status, ptype as Type, poutlet_status as Outlet_Status, 
									ppremium_type as Premium_Type, pmaterial as Material_Type, pbrand as Brand, full_name as User, publish as Publish, UnitPrice, USD_Price, price_rangeName as Price_Category, dUploaded as Date_Uploaded, dReleased as Date_Released
									FROM item_db_reports		 
									$WHERE ORDER BY $ORDER");
		
		$all_items = "";
		
		//generate csv file
		$csv  = "Number of Views per Item\n";
		$csv .= "No,  Views, Likes, Wants, Item Code,  Item Name, Status, Type, Outlet Status, Premium Type, Material Type, Brand, User, Country, Publish, UnitPrice, USD Price, Price Category, Date Uploaded, Date Released\n";
		$sql_csv = $sql_csv->result_array(); $x=0;
		foreach($sql_csv as $s)
		{ extract($s); $x++;
		  $Publish 	  	 = ($Publish=='y')    ? "Yes" : "No";
		  $Date_Uploaded =  $this->convertDate('date',"$Date_Uploaded 00:00:00");
		  $Date_Released =  $this->convertDate('date',"$Date_Released 00:00:00");
		  $Item_Name     =  str_replace(",","-",$Item_Name);
		  $csv .= "$x,  $Views, $Likes, $Wants, $Item_Code, $Item_Name, $Status, $Type, $Outlet_Status, $Premium_Type, $Material_Type, $Brand, $User,$Country, $Publish,  $UnitPrice, $USD_Price, $Price_Category, $Date_Uploaded, $Date_Released\n";
		}
	    write_file(getcwd()."/files/csv/Number_of_Views_per_Item".$this->reportCode().".csv",$csv);
		$data['csvFile']			= "Number_of_Views_per_Item".$this->reportCode().".csv";
	
		$all_items = $this->db->query($sql);
		$all_items = $all_items->result_array();
		//SORT STATUS
		$limit = ($sort)=='y' ? 0 : $limit;
		
		$limit_items = $this->db->query($sql." LIMIT $limit,20");
		$limit_items = $limit_items->result_array();
		$data['totrec'] = count($all_items);
		$data['limit']  = $limit;
		
		$items	 = $limit_items;
		
		$table= "<table id='large2' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:11px;' class='iLike_Result_Table2'>
				<tr style='height: 40px;'>
					<th style='width:25px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'>	   No.  	  		  				  					  </th> 
					<th style='width:67px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"0-D\")'>           <b>Views  	   	  </b></th> 
					<th style='width:65px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"1-D\")'>           <b>Likes  	  	  </b></th> 
					<th style='width:65px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"2-D\")'>           <b>Wants  	  	  </b></th> 
					<th style='width:120px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"3-A\")'>           <b>Item Code  	  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'>  <b>Image   	 	     							  	  </b></th> 
					<th style='width:120px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"4-A\")'>           <b>Item Name  	  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"5-A\")'>           <b>Status  	  	  </b></th> 
					<th style='width:98px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"6-A\")'>        	  <b>Type  	  		  </b></th> 
					<th style='width:153px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"7-D\")'>   	      <b>Outlet  	  	  </b></th> 
					<th style='width:125px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"8-D\")'>    		  <b>Premium  	  	  </b></th> 
					<th style='width:77px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"9-A\")'>        	  <b>Material  	  	  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"10-A\")'>          <b>Brand  	  	  </b></th> 
					<th style='width:120px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"11-A\")'>          <b>User  	  		  </b></th> 
					<th style='width:80px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"12-A\")'>          <b>Country  	  	  </b></th> 
					<th style='width:69px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"13-A\")'>          <b>Publish  	  	  </b></th>  
					<th style='width:60px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"14-A\")'>          <b>L. Price  	  	  </b></th> 
					<th style='width:60px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"15-A\")'>          <b>US. Price  	  </b></th> 
					<th style='width:60px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"20-A\")'>          <b>Price Category   </b></th> 
					<th style='width:90px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"16-A\")'>          <b>Uploaded  	  	  </b></th> 
					<th style='width:90px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"17-A\")'>          <b>Released  	  	  </b></th> 
				</tr>";
		
		//REPLACE ORDER
		$table = str_replace($order_code,$this->sortingRef($order_code,'Rev_code'),$table);
		$table = str_replace($label,$this->sortingRef($order_code,'label_symbol'),$table);
		
					$x = $limit;			
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					$orig_itemName="";
					if($valid_query==TRUE)
					{
					 foreach($items as $r) { 
					 extract($r);
					 $ptype = ($ptype=='') 	   ? 'Uncategorize' : $ptype;
					 $publish = ($publish=='y') ? 'Yes' : 'No';
					 $c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
					 $orig_itemName=$itemName;
					 if(strlen($itemName)>=15)
							$itemName = substr($itemName,0,15)."...";
							
					 $likes 			= ($likes=="") 		? 0 : $likes;
					 $wants 		    = ($wants=="") 		? 0 : $wants;
		$table.= "<tr>
				  <td $c>													$x </td>
				  <td $c>													<a onclick=\"viewDialog('item_database',$itemID)\" style='cursor:pointer;'><b>$num_views</b> </a> </td>
				  <td $c>													<a onclick=\"showVoters('iLike',$itemID)\" style='cursor:pointer;'><b>$likes</b></a>      																</td>
				  <td $c>													<a onclick=\"showVoters('iWant',$itemID)\" style='cursor:pointer;'><b>$wants</b></a>      																</td>
				  <td $c>													$itemCode      																	</td>
				  <td $c style='text-align:center;'>			    		<img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 	</td>
				  <td $c style='text-align:left;padding-left:5px;' title='$orig_itemName'>		<a href='".HTTP_PATH."gallery/itemInfo2/$itemID' target='_blank'>		$itemName</a>  </td>
				  <td $c style='text-align:left;padding-left:5px;'>		$pstatus																		</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$ptype																			</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$poutlet_status																	</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$ppremium_type																	</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$pmaterial																	    </td>
				  <td $c style='text-align:left;padding-left:5px;'>		$pbrand																	    	</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$full_name																		</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$cName																			</td>
				  <td $c style='text-align:center;'>						$publish																		</td>
				  <td $c style='text-align:center;'>						$UnitPrice																		</td>
				  <td $c style='text-align:center;'>						$USD_Price																		</td>
				  <td $c style='text-align:center;'>						$price_rangeName																		</td>
				  <td $c style='text-align:center;'>	 ". $this->convertDate('date',"$dUploaded 00:00:00") ."										</td>
				  <td $c style='text-align:center;'>	 ". $this->convertDate('date',"$dReleased 00:00:00") ."										</td>
				</tr>";}
				    }
					if(!$items OR $valid_query==FALSE)
						$table.=  "<tr><td colspan='21'>No items found, please check your search parameters.</td></tr>";
		$table.= "</table>";	
		
		$data['table'] 		= $table;
		
		$viewer = 'innerPages';	    
		if($this->modules->access_checker()==TRUE)
		{
			$this->load->view($viewer,$data); 
		}else{
			$data['vfile']				= 'login.php';
			$data['title']				= 'SMBi System Log-in | SMBi';
			$data['page_title']			= 'SMBi System Log-in';
			$data['meta_description']	= 'San Miguel Brewing International';
			$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
			$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
			$this->load->view('login',$data); 	
		}
	}
	
	function fieldSwitcher($returnType='',$fldCode='',$fldID='')
	{
		$fName="";
		$fValue="";
		$fieldList = array(
					 array('fCode'=>'pStatusID', 		  'fldName'=>'Item Status:',   'q'=>"SELECT statusName as name 		FROM POSM_Status  	 WHERE id = $fldID"),
					 array('fCode'=>'ptypeID',   		  'fldName'=>'Item Type:',     'q'=>"SELECT typeName   as name	    FROM POSM_Type    	 WHERE id = $fldID"),
					 array('fCode'=>'poutlet_statusID',   'fldName'=>'Outlet Type:',   'q'=>"SELECT statusName as name	    FROM OUTLET_Status 	 WHERE id = $fldID"),
					 array('fCode'=>'ppremium_typeID',    'fldName'=>'Premium Type:',  'q'=>"SELECT premiumTypeName as name FROM premiumItemType WHERE id = $fldID"),
					 array('fCode'=>'pmaterialID', 	      'fldName'=>'Material Type',  'q'=>"SELECT materialName  as name  FROM MATERIAL_Type 	 WHERE id = $fldID"),
					 array('fCode'=>'pbrandID',           'fldName'=>'Brand:',  	   'q'=>"SELECT brandName     as name  FROM brands 		 WHERE id = $fldID"));
		foreach($fieldList as $fl)
		{extract($fl);
		 if($fldCode==$fCode){
			$fName=$fldName;
			if($returnType=='fldValue'){
			 $sql = $this->db->query("$q LIMIT 0,1");
			 $row = $sql->row();
			 if(isset($row->name))$fValue=$row->name;
			}
		 }
		}
		
		if($returnType=='fldName')
			return $fName;
		if($returnType=='fldValue')
			return $fValue;
	}
	
	function item_summary_details($cID='',$fldName='',$fldVal='',$DateFrom='',$DateTo='')
	{	
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(76,'REVIEW');
		
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(43);
		
		$table						= 'item_summary_details';
		$data['vfile']				= 'item_summary_details.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/item_summary> Item Summary </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/item_summary_details/$cID/$fldName/$fldVal/$DateFrom/$DateTo'> Item Summary In Details </a>";
		
		//TOTAL NUMBER OF ROWS
		$data['cID'] 	 = $cID;
		$data['fieldName'] = $fldName;
		$data['fieldVal']  = $fldVal;
		$sort='n';
		extract($_POST);
		//COUNTRY
		$data['country_name'] = "Country: All Country";
		if($cID!=0){
			$sql = $this->db->query("SELECT countryName FROM country WHERE id = $cID LIMIT 0,1");
			$row = $sql->row();
			$data['country_name'] = "Country: ".$row->countryName;
		}	
		//FIELD SWITCHER
		$fValue=($fldVal==0) ? "Uncategorized" : $this->fieldSwitcher('fldValue',$fldName,$fldVal);
		$data['fldName'] = "(<i>".$this->fieldSwitcher('fldName',$fldName)." $fValue</i>)";		
		
		//COUNTRY ID
		$WHERE="WHERE $fldName = '$fldVal' AND cID != '0' AND forArchiving='n' AND forPurging='n' AND (itemAge <".$this->modules->itemsAge().") AND";
		if($fldVal=='ALL'){
			$WHERE="WHERE cID != '0' AND forArchiving='n' AND forPurging='n' AND (itemAge <".$this->modules->itemsAge().") AND";
			$data['fldName'] = "";
		}if($cID!=0)
			$WHERE .= " cID = '$cID' AND";
	
		$cond="";
		$having="";
		$limit =isset($selpage)? $selpage:0;
		
		$data['quarterStr'] = "";
		$data['DateFrom'] = "";
		$data['DateTo'] = "";
		//print_r($_POST);
		if((isset($DateFrom) AND isset($DateTo)) AND ($DateFrom!='null' AND $DateTo!='null') AND !isset($Reset)){
			$WHERE   .= " (dUploaded >= '$DateFrom' AND dUploaded <= '$DateTo') ";
			$data['DateFrom'] = $DateFrom;
			$data['DateTo']   = $DateTo;
			//DETECT QUARTER STRING
			if($DateFrom==$this->perQuarterMonths('prevMonth') & $DateTo==$this->perQuarterMonths('nextMonth'))
			 $data['quarterStr'] = $this->quaterStr('condition');
			//PREVIOUS QUARTER
			if($DateFrom==$this->setPreviousQTR($DateFrom,'prevMonth') & $DateTo==$this->setPreviousQTR($DateTo,'nextMonth'))
			 $data['quarterStr'] = $this->SetPrevQuaterStr($DateFrom);
			//PREVIOUS QUARTER
			if($DateFrom==$this->minMaxDate('prevMonth') & $DateTo==$this->minMaxDate('nextMonth'))
			 $data['quarterStr'] = $this->minMaxDate('condition');
		}elseif(!$_POST){
			$WHERE   		   .= $this->minMaxDate('filter');
			$data['quarterStr'] = $this->minMaxDate('condition');
			$data['DateFrom']   = $this->minMaxDate('prevMonth');
			$data['DateTo']     = $this->minMaxDate('nextMonth');
		}
		
		if($_POST AND $DateFrom=='' AND $DateTo=='') $WHERE = substr($WHERE,0,-3);
		
		
		if(isset($Submit) OR isset($selpage) AND !isset($Reset))
		{	
			$val1 = mysql_real_escape_string($val1);
			$val1 = trim($val1);
			$val2 = mysql_real_escape_string($val2);
			$val2 = trim($val2);
			
			$condition = '';
			switch($cond1){
				case 'equal': 
					$condition = '=';
				break;
				case 'containing': 
					$condition = 'like';
				break;
				case 'in': 
					$condition = 'in';
				break;
				case 'greaterThan': 
					$condition = '>=';
				break;
				case 'lessThan': 
					$condition = '<=';
				break;
				case 'between': 
					$condition = 'between';
				break;
			}
			
			
			if(($opt1=='itemCode' OR $opt1=='itemName' OR $opt1=='pstatus' OR $opt1=='ptype' OR $opt1=='poutlet_status' OR $opt1=='ppremium_type' OR $opt1=='pmaterial' OR $opt1=='pbrand' OR $opt1=='full_name' OR $opt1=='pcountry' OR $opt1=='price_rangeName') AND $val1!='')
			{
				if($condition=='=')
					$cond = "  $opt1 $condition '$val1'";
				if($condition=='>=')
					$cond = "  $opt1 $condition '$val1'";
				if($condition=='<=')
					$cond = "  $opt1 $condition '$val1'";
				if($condition=='like')
					$cond = "  $opt1 $condition '%$val1%'";
				if($condition=='in')
					$cond = "  $opt1 $condition ('" . str_replace(",", "','", $val1) . "')";
				if($condition=='between' & $this->checkStr($val1)==TRUE)
					$cond = "  $opt1 $condition ".stripslashes($val1);
			}
			
			//PUBLISH
			if(($opt1=='publish' OR $opt1=='disapprove') AND $val1!='')
			{
				if($condition=='=')
					$val1 = ($val1=='yes' OR $val1=='Yes') ? 'y' : 'n';
					$cond = "  $opt1 $condition '$val1'";
				if($condition=='like')
					$cond = "  $opt1 $condition '%$val1%'";
			}
			
			
			//VIEWS
			if(($opt1=='num_views' OR $opt1=='UnitPrice' OR $opt1=='likes' OR $opt1=='wants' OR $opt1=='USD_Price') AND $val1!='' AND $condition!='like'){ 
				if($condition=='in' AND (is_numeric($val1) OR strpos($val1,',')==TRUE))
					$cond = "  $opt1 $condition ('" . str_replace(",", "','", $val1) . "')";
				elseif($condition!='in' AND is_numeric($val1))
					$cond = "  $opt1 $condition $val1";
				elseif($condition=='between' & $this->checkStr($val1)==TRUE)
					$cond = "  $opt1 $condition ".stripslashes($val1);
			}
		
			//dateReleased
			if($opt1=='dateAdded' AND $val1!='' AND $condition!='like' AND $condition!='between') 
				$cond = "  dateAdded $condition '$val1'";
			if($opt1=='dateAdded' AND $val1!='' AND $condition=='between' & $this->checkStr($val1)==TRUE) 
				$cond = "  dateAdded $condition ".stripslashes($val1);
			
			/*2ND SET*/
			switch($cond2){
				case 'equal': 
					$condition2 = '=';
				break;
				case 'containing': 
					$condition2 = 'like';
				break;
				case 'in': 
					$condition2 = 'in';
				break;
				case 'greaterThan': 
					$condition2 = '>=';
				break;
				case 'lessThan': 
					$condition2 = '<=';
				break;
				case 'between': 
					$condition2 = 'between';
				break;
			}
			
			//CHECK IF VAL1 IS SET
			if($val1=="")  $condition2 ="";
			
			if(($opt2=='itemCode' OR $opt2=='itemName' OR $opt2=='pstatus' OR $opt2=='ptype' OR $opt2=='poutlet_status' OR $opt2=='ppremium_type' OR $opt2=='pmaterial' OR $opt2=='pbrand' OR $opt2=='full_name' OR $opt2=='pcountry' OR $opt2=='price_rangeName') AND $val2!='')
			{
				if($condition2=='=')
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator $opt2 $condition2 '%$val2%'";
				if($condition2=='>=')
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='<=')
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='in')
					$cond .= " $operator $opt2 $condition2 ('" . str_replace(",", "','", $val2) . "')";
				if($condition2=='between'  & $this->checkStr($val2)==TRUE)
					$cond .= "  $operator $opt2 $condition2 ".stripslashes($val2);
			}
			
			
			//PUBLISH
			if(($opt2=='publish' OR $opt2=='disapprove') AND $val2!='')
			{
				if($condition2=='=')
					$val2 = ($val2=='yes' OR $val2=='Yes') ? 'y' : 'n';
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator $opt2 $condition2 '%$val2%'";
			}
			
			//LOCAL PRICE
			//VIEWS
			if(($opt2=='num_views' OR $opt2=='UnitPrice' OR $opt2=='likes' OR $opt2=='wants' OR $opt2=='USD_Price') AND $val2!='' AND $condition2!='like'){ 
				if($condition2=='in' AND (is_numeric($val2) OR strpos($val2,',')==TRUE))
					$cond .= " $operator $opt2 $condition2 ('" . str_replace(",", "','", $val2) . "')";
				elseif($condition2!='in' AND is_numeric($val2))
					$cond .= " $operator $opt2 $condition2 $val2";
				elseif($condition2=='between' & $this->checkStr($val2)==TRUE)
					$cond .= "  $operator $opt2 $condition2 ".stripslashes($val2);
			}
	
				
			//dUploaded
			if($opt2=='dateAdded' AND $val2!='' AND $condition2!='like' AND $condition2!='between') 
				$cond .= " $operator dateAdded $condition2 '$val2'";
			if($opt2=='dateAdded' AND $val2!='' AND $condition2=='between' & $this->checkStr($val2)==TRUE) 
				$cond .= "  $operator dateAdded $condition2 ".stripslashes($val2);
				
			$cond = ($cond=="") ? "" : " AND ($cond) ";
			$WHERE  .=  " $cond ";
		}
		
		
		//IF THEIR NO POSSIBLE RESULT
		$valid_query=TRUE;
		if(isset($Submit)){
			if($cond=="" AND ($val1!='' OR $val2!='')){ 
			$valid_query=FALSE;
			$WHERE = "WHERE itemID=0";
			}
		}
		
		$data['POST']      = $_POST;
		if(isset($Reset)){
			$WHERE = substr($WHERE,0,-3);
			$data['POST'] = array();
			$data['quarterStr'] = $this->minMaxDate('condition');
			$data['DateFrom'] = $this->minMaxDate('prevMonth');
			$data['DateTo']   = $this->minMaxDate('nextMonth');
		}
		
		//ORDER
		$ORDER 			= $this->sortingRef('0-D','query');
		$data['order']  = $this->sortingRef('0-D','Orig_code');
		$order_code 	= '0-D';
		$label 			= "Views";
		if(isset($order)){
			$ORDER = $this->sortingRef($order,'query');
			$data['order'] = $this->sortingRef($order,'Orig_code');
			$order_code = $order;
			$label = $this->sortingRef($order,'label');
		}	
		
		$sql = "SELECT itemID, num_views, likes, wants, itemCode, item_image, itemName, pstatus, ptype, poutlet_status, ppremium_type, pmaterial, pbrand, full_name, cName, publish, disapprove, UnitPrice, USD_Price,price_rangeName, dUploaded, dReleased
				FROM item_db_reports		 
				$WHERE ORDER BY $ORDER";
				
		$sql_csv = $this->db->query("SELECT cName as Country, num_views as Views, likes as Likes, wants as Wants, itemCode as Item_Code, itemName as Item_Name, pstatus as Status, ptype as Type, poutlet_status as Outlet_Status, 
									 ppremium_type as Premium_Type, pmaterial as Material_Type, pbrand as Brand, full_name as User, publish as Publish, disapprove as Disapprove,  UnitPrice, USD_Price, price_rangeName as Price_Category, dUploaded as Date_Uploaded, dReleased as Date_Released
									 FROM item_db_reports		 
								     $WHERE ORDER BY $ORDER");
		
		$all_items = "";
		//generate csv file
		$csv  = "Summary of Items in Details\n";
		$csv .= "No,  Views, Likes, Wants, Item Code,  Item Name, Status, Type, Outlet Status, Premium Type, Material Type, Brand, User, Country, Publish, Disapprove, UnitPrice, USD Price, Price Category, Date Uploaded, Date Released\n";
		$sql_csv = $sql_csv->result_array(); $x=0;
		foreach($sql_csv as $s)
		{ extract($s); $x++;
		  $Publish 	  	 = ($Publish=='y')    ? "Yes" : "No";
		  $Disapprove 	 = ($Disapprove=='y') ? "Yes" : "No";
		  $Date_Uploaded =  $this->convertDate('date',"$Date_Uploaded 00:00:00");
		  $Date_Released =  $this->convertDate('date',"$Date_Released 00:00:00");
		  $Item_Name     =  str_replace(",","-",$Item_Name);
		  $csv .= "$x, $Views, $Likes, $Wants, $Item_Code, $Item_Name, $Status, $Type, $Outlet_Status, $Premium_Type, $Material_Type, $Brand, $User, $Country, $Publish, $Disapprove, $UnitPrice, $USD_Price, $Price_Category, $Date_Uploaded, $Date_Released\n";
		}
		 write_file(getcwd()."/files/csv/Summary_of_Items_in_details".$this->reportCode().".csv",$csv);
		$data['csvFile']			= "Summary_of_Items_in_details".$this->reportCode().".csv";
		 
		$all_items = $this->db->query($sql);
		$all_items = $all_items->result_array();
		//SORT STATUS
		$limit = ($sort)=='y' ? 0 : $limit;
		
		$limit_items = $this->db->query($sql." LIMIT $limit,20");
		$limit_items = $limit_items->result_array();
		$data['totrec'] = count($all_items);
		$data['limit']  = $limit;
		
		$items	 = $limit_items;
		
	
		$table= "<table id='large2' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:11px;' class='iLike_Result_Table2'>
				<tr style='height: 40px;'>
					<th style='width:25px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'>	   No.  	  		  				  					  </th> 
					<th style='width:67px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"0-D\")'>           <b>Views  	   	  </b></th> 
					<th style='width:65px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"1-D\")'>           <b>Likes  	  	  </b></th> 
					<th style='width:65px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"2-D\")'>           <b>Wants  	  	  </b></th> 
					<th style='width:120px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"3-A\")'>           <b>Item Code  	  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'>  <b>Image   	 	     							  	  </b></th> 
					<th style='width:120px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"4-A\")'>           <b>Item Name  	  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"5-A\")'>           <b>Status  	  	  </b></th> 
					<th style='width:98px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"6-A\")'>        	  <b>Type  	  		  </b></th> 
					<th style='width:153px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"7-D\")'>   	      <b>Outlet  	  	  </b></th> 
					<th style='width:125px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"8-D\")'>    		  <b>Premium  	  	  </b></th> 
					<th style='width:77px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"9-A\")'>        	  <b>Material  	  	  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"10-A\")'>          <b>Brand  	  	  </b></th> 
					<th style='width:120px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"11-A\")'>          <b>User  	  		  </b></th> 
					<th style='width:80px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"12-A\")'>          <b>Country  	  	  </b></th> 
					<th style='width:69px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"13-A\")'>          <b>Publish  	  	  </b></th>  
					<th style='width:69px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"21-D\")'>          <b>Disapprove  	  	  </b></th>  
					<th style='width:60px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"14-A\")'>          <b>L. Price  	  	  </b></th> 
					<th style='width:60px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"15-A\")'>          <b>US. Price  	  </b></th> 
					<th style='width:60px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"20-A\")'>          <b>Price Category   </b></th> 
					<th style='width:90px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"16-A\")'>          <b>Uploaded  	  	  </b></th> 
					<th style='width:90px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"17-A\")'>          <b>Released  	  	  </b></th> 
				</tr>";
		
		//REPLACE ORDER
		$table = str_replace($order_code,$this->sortingRef($order_code,'Rev_code'),$table);
		$table = str_replace($label,$this->sortingRef($order_code,'label_symbol'),$table);
		
					$x = $limit;			
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					$orig_itemName="";
					if($valid_query==TRUE)
					{
					 foreach($items as $r) { 
					 extract($r);
					 $ptype = ($ptype=='') 	   ? 'Uncategorize' : $ptype;
					 $publish 	 = ($publish=='y') ? 'Yes' : 'No';
					 $disapprove = ($disapprove=='y') ? 'Yes' : 'No';
					 $c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
					 $orig_itemName=$itemName;
					 if(strlen($itemName)>=15)
							$itemName = substr($itemName,0,15)."...";
							
					 $likes 			= ($likes=="") 		? 0 : $likes;
					 $wants 		    = ($wants=="") 		? 0 : $wants;
		$table.= "<tr>
				  <td $c>													$x </td>
				  <td $c>													<a onclick=\"viewDialog('item_database',$itemID)\" style='cursor:pointer;'><b>$num_views</b> </a> </td>
				  <td $c>													<a onclick=\"showVoters('iLike',$itemID)\" style='cursor:pointer;'><b>$likes</b></a>      																</td>
				  <td $c>													<a onclick=\"showVoters('iWant',$itemID)\" style='cursor:pointer;'><b>$wants</b></a>      																</td>
				  <td $c>													$itemCode      																	</td>
				  <td $c style='text-align:center;'>			    		<img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 	</td>
				  <td $c style='text-align:left;padding-left:5px;' title='$orig_itemName'>		<a href='".HTTP_PATH."gallery/itemInfo2/$itemID' target='_blank'>		$itemName</a>  </td>
				  <td $c style='text-align:left;padding-left:5px;'>		$pstatus																		</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$ptype																			</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$poutlet_status																	</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$ppremium_type																	</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$pmaterial																	    </td>
				  <td $c style='text-align:left;padding-left:5px;'>		$pbrand																	    	</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$full_name																		</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$cName																			</td>
				  <td $c style='text-align:center;'>						$publish																		</td>
				  <td $c style='text-align:center;'>						$disapprove																		</td>
				  <td $c style='text-align:center;'>						$UnitPrice																		</td>
				  <td $c style='text-align:center;'>						$USD_Price																		</td>
				   <td $c style='text-align:center;'>						$price_rangeName																		</td>
				  <td $c style='text-align:center;'>				        ". $this->convertDate('date',"$dUploaded 00:00:00") ."	</td>
				  <td $c style='text-align:center;'>				        ". $this->convertDate('date',"$dReleased 00:00:00") ."	</td>
				</tr>";}
				    }
					if(!$items OR $valid_query==FALSE)
						$table.=  "<tr><td colspan='22'>Sorry no items found, check your search parameters.</td></tr>";
		$table.= "</table>";	
		
		$data['table'] 		= $table;
		
		$viewer = 'innerPages';	    
		if($this->modules->access_checker()==TRUE)
		{
			$this->load->view($viewer,$data); 
		}else{
			$data['vfile']				= 'login.php';
			$data['title']				= 'SMBi System Log-in | SMBi';
			$data['page_title']			= 'SMBi System Log-in';
			$data['meta_description']	= 'San Miguel Brewing International';
			$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
			$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
			$this->load->view('login',$data); 	
		}
	}

	
	function price_range_summary($POSMTypeID='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(76,'REVIEW');
		//USER MANUAL
	    $data['USER_MANUAL'] = $this->modules->user_manual(43);
		
		//DETECT COUNTRY
		$cond = "";
		$WHERE="WHERE cID != 0 AND forArchiving='n' AND forPurging='n' AND (itemAge <".$this->modules->itemsAge().") ";
		$HAVING="";
		$data['cID']=0;
		$data['sa']=TRUE;
		if($_SESSION['super_admin']!='y' AND $_SESSION['countryID']!=0){
			$WHERE 		= " WHERE  cID =".$_SESSION['countryID']." AND forArchiving='n' AND forPurging='n' AND (itemAge <".$this->modules->itemsAge().") ";
			$data['cID']= $_SESSION['countryID'];
			$data['sa'] =FALSE;
		}
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/price_range_summary> Price Category - Summary </a>';
		
		$table						= 'item_division';
		$data['vfile']				= 'price_range_summary.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//TOTAL NUMBER OF ROWS	
		$sort='n';
		extract($_POST);
		$data['DateFrom'] = "";
		$data['DateTo'] = "";
		$data['quarterStr'] = "";
		if((isset($DateFrom) AND isset($DateTo)) AND ($DateFrom!='' AND $DateTo!='') AND !isset($Reset)){
			$WHERE   .= " AND dUploaded >= '$DateFrom' AND dUploaded <= '$DateTo' ";
			$data['DateFrom'] = $DateFrom;
			$data['DateTo']   = $DateTo;
			$data['months']	  = $this->monthDiff($DateFrom,$DateTo);
			
			//DETECT QUARTER STRING
			if($DateFrom==$this->perQuarterMonths('prevMonth') & $DateTo==$this->perQuarterMonths('nextMonth'))
			 $data['quarterStr'] = $this->quaterStr('condition');
			//PREVIOUS QUARTER
			if($DateFrom==$this->setPreviousQTR($DateFrom,'prevMonth') & $DateTo==$this->setPreviousQTR($DateTo,'nextMonth'))
			 $data['quarterStr'] = $this->SetPrevQuaterStr($DateFrom);
			//PREVIOUS QUARTER
			if($DateFrom==$this->minMaxDate('prevMonth') & $DateTo==$this->minMaxDate('nextMonth'))
			 $data['quarterStr'] = $this->minMaxDate('condition');
		}else{
			$WHERE   		   .= " AND ".$this->minMaxDate('filter');
			$data['quarterStr'] = $this->minMaxDate('condition');
			$data['months']	    = $this->monthDiff($this->minMaxDate('prevMonth'),$this->minMaxDate('nextMonth'));
			$data['DateFrom']   = $this->minMaxDate('prevMonth');
			$data['DateTo']     = $this->minMaxDate('nextMonth');
		}
		
		if(isset($Submit) OR isset($selpage))
		{	
			$val1 = mysql_real_escape_string($val1);
			$val1 = trim($val1);
			$val2 = mysql_real_escape_string($val2);
			$val2 = trim($val2);
			
			$condition = '';
			switch($cond1){
				case 'equal': 
					$condition = '=';
				break;
				case 'containing': 
					$condition = 'like';
				break;
				case 'in': 
					$condition = 'in';
				break;
				case 'greaterThan': 
					$condition = '>=';
				break;
				case 'lessThan': 
					$condition = '<=';
				break;
				case 'between': 
					$condition = 'between';
				break;
			}
			
			//COUNTRY NAME
			if(($opt1=='cName' OR $opt1=='pstatus' OR $opt1=='ptype' OR $opt1=='poutlet_status' OR $opt1=='ppremium_type' OR $opt1=='pmaterial' OR $opt1=='pbrand') AND $val1!='')
			{
				if($condition=='=')
					$cond = " AND $opt1 $condition '$val1'";
				if($condition=='>=')
					$cond = " AND $opt1 $condition '$val1'";
				if($condition=='<=')
					$cond = " AND $opt1 $condition '$val1'";
				if($condition=='like')
					$cond = " AND $opt1 $condition '%$val1%'";
				if($condition=='in')
					$cond = " AND $opt1 $condition ('" . str_replace(",", "','", $val1) . "')";
			}

			//PUBLISH
			$field="";
			if($opt1=='uploaded')
				$field=" COUNT(itemID) ";
			if($opt1=='published')
				$field=" SUM(publish='y')";
			if($opt1=='for_approval')
				$field=" SUM(publish='n')";
			if($opt1=='num_views')
				$field=" SUM(num_views)";
			if(($opt1=='uploaded' OR $opt1=='num_views' OR  $opt1=='published' OR $opt1=='for_approval') AND $val1!='' AND is_numeric($val1))
			{				
				if($condition=='=')
					$HAVING = " HAVING $field $condition $val1 ";
				if($condition=='>=')
					$HAVING = " HAVING $field $condition $val1 ";
				if($condition=='<=')
					$HAVING = " HAVING $field $condition $val1 ";
				if($condition=='in' AND (is_numeric($val1) OR strpos($val1,',')==TRUE))
					$HAVING = "   HAVING $field $condition ('" . str_replace(",", "','", $val1) . "')";
			}
			
			if($condition=='between' & $this->checkStr($val1)==TRUE)
					$HAVING = " HAVING $field $condition ".stripslashes($val1);
			
			/*2ND SET*/
			switch($cond2){
				case 'equal': 
					$condition2 = '=';
				break;
				case 'containing': 
					$condition2 = 'like';
				break;
				case 'in': 
					$condition2 = 'in';
				break;
				case 'greaterThan': 
					$condition2 = '>=';
				break;
				case 'lessThan': 
					$condition2 = '<=';
				break;
				case 'between': 
					$condition2 = 'between';
				break;
			}
			
			//COUNTRY NAME
			if(($opt2=='cName' OR $opt2=='pstatus' OR $opt2=='ptype' OR $opt2=='poutlet_status' OR $opt2=='ppremium_type' OR $opt2=='pmaterial' OR $opt2=='pbrand') AND $val2!='')
			{
				if($condition2=='=')
					$cond .= " $operator  $opt2 $condition2 '$val2'";
				if($condition2=='>=')
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='<=')
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator $opt2 $condition2 '%$val2%'";
				if($condition2=='in')
					$cond .= " $operator $opt2 $condition2 ('" . str_replace(",", "','", $val2) . "')";
			}			
			
			$field="";
			if($opt2=='uploaded')
				$field=" COUNT(itemID) ";
			if($opt2=='published')
				$field=" SUM(publish='y')";
			if($opt2=='for_approval')
				$field=" SUM(publish='n')";
			if($opt2=='num_views')
				$field=" SUM(num_views)";
			if(($opt2=='uploaded' OR $opt2=='num_views' OR $opt2=='published' OR $opt2=='for_approval') AND $val2!='' AND (is_numeric($val2) OR $this->checkStr($val2)==TRUE))
			{
				$HAVING .= ($HAVING=="") ? "HAVING" : "$operator";
				if($condition2=='=')
					$HAVING .= " $field $condition2 $val2 ";
				if($condition2=='>=')
					$HAVING .= " $field $condition2 $val2 ";
				if($condition2=='<=')
					$HAVING .= " $field $condition2 $val2 ";
				if($condition2=='in' AND (is_numeric($val2) OR strpos($val2,',')==TRUE))
					$HAVING .= "  $field $condition2 ('" . str_replace(",", "','", $val2) . "')";
			}

			if($condition2=='between' & $this->checkStr($val2)==TRUE)
				$HAVING .= " $field $condition2 ".stripslashes($val2);
				
			$cond = ($cond=="") ? "" : " $cond ";
			$WHERE  .=  " $cond ";
		}
		
		//IF THEIR NO POSSIBLE RESULT
		$valid_query=TRUE;
		if(isset($Submit)){
			if(($cond=="" AND $HAVING=="") AND ($val1!='' OR $val2!='')){ 
			$valid_query=FALSE;
			$WHERE = " WHERE cID=-1";
			}
		}
		
		$data['POST']      = $_POST;
		if(isset($Reset)) $data['POST'] = array();
		
		//POSM STATUS;
		$arr 		= '';
		$sql_csv	= '';
		$typeName   = '';
		if($POSMTypeID=='') $sql = $this->db->query("SELECT id as POSMTypeID, typeName FROM POSM_Type ORDER BY id DESC LIMIT 0,1");
		else				$sql = $this->db->query("SELECT id as POSMTypeID, typeName FROM POSM_Type WHERE id = '$POSMTypeID' ORDER BY id DESC LIMIT 0,1");
		
		$sql 		= $sql->row();
		$POSMTypeID = $sql->POSMTypeID;
		$typeName   = $sql->typeName;
		$data['tab'] = $typeName;
		
		$csv = "SELECT 
		     cName 			  as Country_Name, 
		     price_rangeName  as Price_Category,
		     COUNT(itemID) 	  as Uploaded_Items , 
		     SUM(publish='y') as Published_Items,
		     SUM(publish='n') as For_Approval,
			 SUM(num_views)   as Views
		     FROM item_db_reports
		     $WHERE AND ptypeID = $POSMTypeID
		     GROUP BY cName, price_rangeID $HAVING ORDER BY price_rangeID DESC, cName ASC";
		$sql      = $this->db->query($q);
		$arr[]    = array('table'	=>'Price Range Category',
						  'fld'		=>'pStatusID',
						  'rows'	=>$sql->result_array());
	
		
		$data['results'] = $arr;
		
		//generate csv file
		$this->generateCSVFile('item_views',$csv,"Item_Summary_Index".$this->reportCode().".csv");
		$data['csvFile']			= "Item_Summary_Index".$this->reportCode().".csv";
		
		$viewer = 'innerPages';	    
		if($this->modules->access_checker()==TRUE)
		{
			$this->load->view($viewer,$data); 
		}else{
			$data['vfile']				= 'login.php';
			$data['title']				= 'SMBi System Log-in | SMBi';
			$data['page_title']			= 'SMBi System Log-in';
			$data['meta_description']	= 'San Miguel Brewing International';
			$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
			$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
			$this->load->view('login',$data); 	
		}
	}
	
	function item_summary($view='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(76,'REVIEW');
		//USER MANUAL
	    $data['USER_MANUAL'] = $this->modules->user_manual(43);
		
		//DETECT COUNTRY
		$cond = "";
		$WHERE="WHERE cID != 0 AND forArchiving='n' AND forPurging='n' AND (itemAge <".$this->modules->itemsAge().") ";
		$HAVING="";
		$data['cID']=0;
		$data['sa']=TRUE;
		if($_SESSION['super_admin']!='y' AND $_SESSION['countryID']!=0){
			$WHERE 		= " WHERE  cID =".$_SESSION['countryID']." AND forArchiving='n' AND forPurging='n' AND (itemAge <".$this->modules->itemsAge().") ";
			$data['cID']= $_SESSION['countryID'];
			$data['sa']=FALSE;
		}
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/item_summary> Item Database - Summary </a>';
		
		$table						= 'item_division';
		$data['vfile']				= 'item_summary.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//TOTAL NUMBER OF ROWS	
		$sort='n';
		extract($_POST);
		$data['DateFrom'] = "";
		$data['DateTo'] = "";
		$data['quarterStr'] = "";
		if((isset($DateFrom) AND isset($DateTo)) AND ($DateFrom!='' AND $DateTo!='') AND !isset($Reset)){
			$WHERE   .= " AND dUploaded >= '$DateFrom' AND dUploaded <= '$DateTo' ";
			$data['DateFrom'] = $DateFrom;
			$data['DateTo']   = $DateTo;
			$data['months']	  = $this->monthDiff($DateFrom,$DateTo);
			
			//DETECT QUARTER STRING
			if($DateFrom==$this->perQuarterMonths('prevMonth') & $DateTo==$this->perQuarterMonths('nextMonth'))
			 $data['quarterStr'] = $this->quaterStr('condition');
			//PREVIOUS QUARTER
			if($DateFrom==$this->setPreviousQTR($DateFrom,'prevMonth') & $DateTo==$this->setPreviousQTR($DateTo,'nextMonth'))
			 $data['quarterStr'] = $this->SetPrevQuaterStr($DateFrom);
			//PREVIOUS QUARTER
			if($DateFrom==$this->minMaxDate('prevMonth') & $DateTo==$this->minMaxDate('nextMonth'))
			 $data['quarterStr'] = $this->minMaxDate('condition');
		}else{
			$WHERE   		   .= " AND ".$this->minMaxDate('filter');
			$data['quarterStr'] = $this->minMaxDate('condition');
			$data['months']	    = $this->monthDiff($this->minMaxDate('prevMonth'),$this->minMaxDate('nextMonth'));
			$data['DateFrom']   = $this->minMaxDate('prevMonth');
			$data['DateTo']     = $this->minMaxDate('nextMonth');
		}
		
		if(isset($Submit) OR isset($selpage))
		{	
			$val1 = mysql_real_escape_string($val1);
			$val1 = trim($val1);
			$val2 = mysql_real_escape_string($val2);
			$val2 = trim($val2);
			
			$condition = '';
			switch($cond1){
				case 'equal': 
					$condition = '=';
				break;
				case 'containing': 
					$condition = 'like';
				break;
				case 'in': 
					$condition = 'in';
				break;
				case 'greaterThan': 
					$condition = '>=';
				break;
				case 'lessThan': 
					$condition = '<=';
				break;
				case 'between': 
					$condition = 'between';
				break;
			}
			
			//COUNTRY NAME
			if(($opt1=='cName' OR $opt1=='pstatus' OR $opt1=='ptype' OR $opt1=='poutlet_status' OR $opt1=='ppremium_type' OR $opt1=='pmaterial' OR $opt1=='pbrand') AND $val1!='')
			{
				if($condition=='=')
					$cond = " AND $opt1 $condition '$val1'";
				if($condition=='>=')
					$cond = " AND $opt1 $condition '$val1'";
				if($condition=='<=')
					$cond = " AND $opt1 $condition '$val1'";
				if($condition=='like')
					$cond = " AND $opt1 $condition '%$val1%'";
				if($condition=='in')
					$cond = " AND $opt1 $condition ('" . str_replace(",", "','", $val1) . "')";
			}

			//PUBLISH
			$field="";
			if($opt1=='uploaded')
				$field=" COUNT(itemID) ";
			if($opt1=='published')
				$field=" SUM(publish='y')";
			if($opt1=='for_approval')
				$field=" SUM(publish='n' and disapprove='n')";
			if($opt1=='disapprove')
				$field=" SUM(disapprove='y')";
			if($opt1=='num_views')
				$field=" SUM(num_views)";
			if(($opt1=='uploaded' OR $opt1=='num_views' OR  $opt1=='published' OR $opt1=='for_approval') AND $val1!='' AND is_numeric($val1))
			{				
				if($condition=='=')
					$HAVING = " HAVING $field $condition $val1 ";
				if($condition=='>=')
					$HAVING = " HAVING $field $condition $val1 ";
				if($condition=='<=')
					$HAVING = " HAVING $field $condition $val1 ";
				if($condition=='in' AND (is_numeric($val1) OR strpos($val1,',')==TRUE))
					$HAVING = "   HAVING $field $condition ('" . str_replace(",", "','", $val1) . "')";
			}
			
			if($condition=='between' & $this->checkStr($val1)==TRUE)
					$HAVING = " HAVING $field $condition ".stripslashes($val1);
			
			/*2ND SET*/
			switch($cond2){
				case 'equal': 
					$condition2 = '=';
				break;
				case 'containing': 
					$condition2 = 'like';
				break;
				case 'in': 
					$condition2 = 'in';
				break;
				case 'greaterThan': 
					$condition2 = '>=';
				break;
				case 'lessThan': 
					$condition2 = '<=';
				break;
				case 'between': 
					$condition2 = 'between';
				break;
			}
			
			//COUNTRY NAME
			if(($opt2=='cName' OR $opt2=='pstatus' OR $opt2=='ptype' OR $opt2=='poutlet_status' OR $opt2=='ppremium_type' OR $opt2=='pmaterial' OR $opt2=='pbrand') AND $val2!='')
			{
				if($condition2=='=')
					$cond .= " $operator  $opt2 $condition2 '$val2'";
				if($condition2=='>=')
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='<=')
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator $opt2 $condition2 '%$val2%'";
				if($condition2=='in')
					$cond .= " $operator $opt2 $condition2 ('" . str_replace(",", "','", $val2) . "')";
			}			
			
			$field="";
			if($opt2=='uploaded')
				$field=" COUNT(itemID) ";
			if($opt2=='published')
				$field=" SUM(publish='y')";
			if($opt2=='for_approval')
				$field=" SUM(publish='n' and disapprove='n')";
			if($opt2=='disapprove')
				$field=" SUM(disapprove='y')";
			if($opt2=='num_views')
				$field=" SUM(num_views)";
			if(($opt2=='uploaded' OR $opt2=='num_views' OR $opt2=='published' OR $opt2=='for_approval') AND $val2!='' AND (is_numeric($val2) OR $this->checkStr($val2)==TRUE))
			{
				$HAVING .= ($HAVING=="") ? "HAVING" : "$operator";
				if($condition2=='=')
					$HAVING .= " $field $condition2 $val2 ";
				if($condition2=='>=')
					$HAVING .= " $field $condition2 $val2 ";
				if($condition2=='<=')
					$HAVING .= " $field $condition2 $val2 ";
				if($condition2=='in' AND (is_numeric($val2) OR strpos($val2,',')==TRUE))
					$HAVING .= "  $field $condition2 ('" . str_replace(",", "','", $val2) . "')";
			}

			if($condition2=='between' & $this->checkStr($val2)==TRUE)
				$HAVING .= " $field $condition2 ".stripslashes($val2);
				
			$cond = ($cond=="") ? "" : " $cond ";
			$WHERE  .=  " $cond ";
		}
		
		//IF THEIR NO POSSIBLE RESULT
		$valid_query=TRUE;
		if(isset($Submit)){
			if(($cond=="" AND $HAVING=="") AND ($val1!='' OR $val2!='')){ 
			$valid_query=FALSE;
			$WHERE = " WHERE cID=-1";
			}
		}
		
		$data['POST']      = $_POST;
		if(isset($Reset)) $data['POST'] = array();
		
		//POSM STATUS;
		$arr = '';
		$sql_csv='';
		switch($view)
		{
		case '':
		case 'POSM_STATUS':
		$data['tab'] = 'POSM_STATUS';
		$q = "SELECT 
		     cName 			  as Country_Name, cID,
		     pstatus 		  as fldVal,
		     pStatusID 		  as fldID,
		     COUNT(itemID) 	  as Uploaded_Items , 
		     SUM(publish='y') as Published_Items,
		     SUM(publish='n' AND disapprove='n') as For_Approval,
		     SUM(disapprove='y') as Disapprove_Items,
			 SUM(num_views)   as myViews
		     FROM item_db_reports
		     $WHERE
		     GROUP BY cName, pstatus $HAVING ORDER BY pstatus ASC, cName ASC";
		$csv = "SELECT 
		     cName 			  as Country_Name, 
		     pstatus 		  as POSM_STATUS,
		     COUNT(itemID) 	  as Uploaded_Items , 
		     SUM(publish='y') as Published_Items,
		     SUM(publish='n' AND disapprove='n') as For_Approval,
		     SUM(disapprove='y') as Disapprove_Items,
			 SUM(num_views)   as Views
		     FROM item_db_reports
		     $WHERE
		     GROUP BY cName, pstatus $HAVING ORDER BY pstatus ASC, cName ASC";
		$sql      = $this->db->query($q);
		$arr[]    = array('table'=>'POSM Status',
					   'fld'=>'pStatusID',
					   'rows'=>$sql->result_array());
		break;
		case 'POSM_TYPE':
		//POSM TYPE;
		$data['tab'] = 'POSM_TYPE';
		$q = "SELECT 
			   cName   			as Country_Name, cID,
			   ptype 			as fldVal,
			   ptypeID 			as fldID,
			   COUNT(itemID)    as Uploaded_Items , 
			   SUM(publish='y') as Published_Items,
			   SUM(publish='n' AND disapprove='n') as For_Approval,
				SUM(disapprove='y') as Disapprove_Items,
			   SUM(num_views)   as myViews
			   FROM item_db_reports
			   $WHERE
			   GROUP BY cName, ptype $HAVING ORDER BY ptype ASC, cName ASC";
		$csv = "SELECT 
			   cName   			as Country_Name, 
			   ptype 			as POSM_TYPE,
			   COUNT(itemID)    as Uploaded_Items , 
			   SUM(publish='y') as Published_Items,
			   SUM(publish='n' AND disapprove='n') as For_Approval,
			   SUM(disapprove='y') as Disapprove_Items,
			   SUM(num_views)   as Views
			   FROM item_db_reports
			   $WHERE
			   GROUP BY cName, ptype $HAVING ORDER BY ptype ASC, cName ASC";
		$sql   = $this->db->query($q);
		$arr[] = array('table'=>'POSM Type',
					   'fld'=>'ptypeID',
					   'rows'=>$sql->result_array());
		break;
		case 'OUTLET_TYPE':
		//OUTLET TYPE;
		$data['tab'] = 'OUTLET_TYPE';
		$q = "SELECT 
				cName   	  	 as Country_Name, cID, 
				poutlet_status 	 as fldVal,
				poutlet_statusID as fldID,
				COUNT(itemID) 	 as Uploaded_Items , 
				SUM(publish='y') as Published_Items,
				SUM(publish='n' AND disapprove='n') as For_Approval,
				SUM(disapprove='y') as Disapprove_Items,
				SUM(num_views)   as myViews
				FROM item_db_reports
				$WHERE
				GROUP BY cName, poutlet_status $HAVING ORDER BY poutlet_status ASC, cName ASC";
		$csv = "SELECT 
				cName   	  	 as Country_Name, 
				poutlet_status 	 as POSM_STATUS,
				COUNT(itemID) 	 as Uploaded_Items , 
				SUM(publish='y') as Published_Items,
				SUM(publish='n' AND disapprove='n') as For_Approval,
				SUM(disapprove='y') as Disapprove_Items,
				SUM(num_views)   as Views
				FROM item_db_reports
				$WHERE
				GROUP BY cName, poutlet_status $HAVING ORDER BY poutlet_status ASC, cName ASC";
		$sql = $this->db->query($q);
		$arr[] = array('table'=>'OUTLET TYPE',
					   'fld'=>'poutlet_statusID',
					   'rows'=>$sql->result_array());
		break;
		case 'PREMIUM_TYPE':
		//PREMIUM TYPE;
		$data['tab'] = 'PREMIUM_TYPE';
		$q = "SELECT 
			   cName   	  		as Country_Name, cID,
			   ppremium_type 	as fldVal,
			   ppremium_typeID 	as fldID,
			   COUNT(itemID) 	as Uploaded_Items , 
			   SUM(publish='y') as Published_Items,
			   SUM(publish='n' AND disapprove='n') as For_Approval,
			   SUM(disapprove='y') as Disapprove_Items,
			   SUM(num_views)   as myViews
			   FROM item_db_reports
			   $WHERE
			   GROUP BY cName, ppremium_type  $HAVING ORDER BY ppremium_type ASC, cName ASC";
		$csv = "SELECT 
			   cName   	  		as Country_Name, 
			   ppremium_type 	as PREMIUM_TYPE,
			   COUNT(itemID) 	as Uploaded_Items , 
			   SUM(publish='y') as Published_Items,
			   SUM(publish='n' AND disapprove='n') as For_Approval,
		       SUM(disapprove='y') as Disapprove_Items,
			   SUM(num_views)   as Views
			   FROM item_db_reports
			   $WHERE
			   GROUP BY cName, ppremium_type  $HAVING ORDER BY ppremium_type ASC, cName ASC";
		$sql   = $this->db->query($q);
		$arr[] = array('table'=>'PREMIUM Type',
					   'fld'=>'ppremium_typeID',
					   'rows'=>$sql->result_array());
		break;
		case 'MATERIAL_TYPE':
		//MATERIAL TYPE;
		$data['tab'] = 'MATERIAL_TYPE';
		$q = "SELECT 
			   cName   	  		as Country_Name, cID,
			   pmaterial 		as fldVal,
			   pmaterialID 		as fldID,
			   COUNT(itemID) 	as Uploaded_Items , 
			   SUM(publish='y') as Published_Items,
			   SUM(publish='n' AND disapprove='n') as For_Approval,
		     SUM(disapprove='y') as Disapprove_Items,
			   SUM(num_views)   as myViews
			   FROM item_db_reports
			   $WHERE
			   GROUP BY cName, pmaterial  $HAVING ORDER BY pmaterial ASC, cName ASC";
		$csv = "SELECT 
			   cName   	  		as Country_Name,
			   pmaterial 		as MATERIAL_TYPE,
			   COUNT(itemID) 	as Uploaded_Items , 
			   SUM(publish='y') as Published_Items,
			   SUM(publish='n' AND disapprove='n') as For_Approval,
		     SUM(disapprove='y') as Disapprove_Items,
			   SUM(num_views)   as Views
			   FROM item_db_reports
			   $WHERE
			   GROUP BY cName, pmaterial  $HAVING ORDER BY pmaterial ASC, cName ASC";
		$sql   = $this->db->query($q);
		$arr[] = array('table'=>'MATERIAL Type',
					   'fld'=>'pmaterialID',
					   'rows'=>$sql->result_array());
		break;
		case 'BRAND_TYPE':
		//BRAND TYPE;
		$data['tab'] = 'BRAND_TYPE';
		$q="SELECT 
			  cName   	  		as Country_Name, cID,
			  pbrand 			as fldVal,
			  pbrandID 		 	as fldID,
			  COUNT(itemID) 	as Uploaded_Items , 
			  SUM(publish='y')  as Published_Items,
			  SUM(publish='n' AND disapprove='n') as For_Approval,
		     SUM(disapprove='y') as Disapprove_Items,
			  SUM(num_views)   as myViews
			  FROM item_db_reports
			  $WHERE
			  GROUP BY cName, pbrand  $HAVING ORDER BY pbrand ASC, cName ASC";
		$csv="SELECT 
			  cName   	  		as Country_Name,
			  pbrand 			as BRAND_TYPE,
			  COUNT(itemID) 	as Uploaded_Items , 
			  SUM(publish='y')  as Published_Items,
			  SUM(publish='n' AND disapprove='n') as For_Approval,
		      SUM(disapprove='y') as Disapprove_Items,
			  SUM(num_views)   as Views
			  FROM item_db_reports
			  $WHERE
			  GROUP BY cName, pbrand  $HAVING ORDER BY pbrand ASC, cName ASC";
		$sql   = $this->db->query($q);
		$arr[] = array('table'=>'BRAND Type',
					   'fld'=>'pbrandID',
					   'rows'=>$sql->result_array());
		break;
		}
		
		$data['results'] = $arr;
		
		//generate csv file
		$this->generateCSVFile('item_views',$csv,"Item_Summary_Index".$this->reportCode().".csv");
		$data['csvFile']			= "Item_Summary_Index".$this->reportCode().".csv";
		
		$viewer = 'innerPages';	    
		if($this->modules->access_checker()==TRUE)
		{
			$this->load->view($viewer,$data); 
		}else{
			$data['vfile']				= 'login.php';
			$data['title']				= 'SMBi System Log-in | SMBi';
			$data['page_title']			= 'SMBi System Log-in';
			$data['meta_description']	= 'San Miguel Brewing International';
			$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
			$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
			$this->load->view('login',$data); 	
		}
	}
	
	function sortingRef_Voters($id='',$returnType='')
	{
		$query	  ='';
		$Orig_code='';
		$Rev_code ='';
		switch($id)
		{
		 case '0-A':
		  $query 	 	 = "fname ASC";
		  $Orig_code 	 = "0-A";
		  $Rev_code  	 = "0-D";
		  $label	 	 = "First Name ";
		  $label_symbol	 = "First Name &#x25B2;";
		  //$label
		 break;
		 case '0-D':
		  $query 	 	 = "fname DESC"; 
		  $Orig_code 	 = "0-D";
		  $Rev_code  	 = "0-A";
		  $label	 	 = "First Name ";
		  $label_symbol	 = "First Name &#x25BC;";
		 break;
		 case '1-A':
		  $query 	 = "lname ASC"; 
		  $Orig_code = "1-A";
		  $Rev_code  = "1-D";
		  $label	 	 = "Last Name ";
		  $label_symbol	 = "Last Name &#x25B2;";
		 break;
		 case '1-D':
		  $query     	 = "lname DESC"; 
		  $Orig_code 	 = "1-D";
		  $Rev_code  	 = "1-A";
		  $label	 	 = "Last Name";
		  $label_symbol	 = "Last Name &#x25BC;";
		 break;
		 case '2-A':
		  $query     	 = "gender ASC"; 
		  $Orig_code 	 = "2-A";
		  $Rev_code  	 = "2-D";
		  $label	 	 = "Gender ";
		  $label_symbol	 = "Gender &#x25B2;";
		 break;
		 case '2-D':
		  $query     	= "gender DESC"; 
		  $Orig_code 	= "2-D";
		  $Rev_code  	= "2-A";
		  $label	 	 = "Gender ";
		  $label_symbol	 = "Gender &#x25BC;";
		 break;
		 case '3-A':
		  $query     = "email ASC";
	      $Orig_code = "3-A";
		  $Rev_code  = "3-D";
		  $label	 	 = "Email ";
		  $label_symbol	 = "Email &#x25B2;";
		 break;
		 case '3-D':
		  $query     = "email DESC"; 
		  $Orig_code = "3-D";
		  $Rev_code  = "3-A";
		   $label	 	 = "Email";
		  $label_symbol	 = "Email &#x25BC;";
		 break;
		 case '4-A':
		  $query 	 = "department ASC"; 
		  $Orig_code = "4-A";
		  $Rev_code  = "4-D";
		  $label	 	 = "Department";
		  $label_symbol	 = "Department &#x25B2;";
		 break;
		 case '4-D':
		  $query = " department DESC";
		  $Orig_code = "4-D";
		  $Rev_code  = "4-A";
		  $label	 	 = "Department";
		  $label_symbol	 = "Department &#x25BC;";
		 break;
		 case '5-A':
		  $query 	 	 = " year_of_birth ASC"; 
		  $Orig_code 	 = "5-A";
		  $Rev_code  	 = "5-D";
		  $label	 	 = "Year of Birth ";
		  $label_symbol	 = "Year of Birth &#x25B2;";
		 break;
		 case '5-D':
		  $query 		 = " year_of_birth DESC";
		  $Orig_code 	 = "5-D";
		  $Rev_code  	 = "5-A";
		  $label	 	 = "Year of Birth ";
		  $label_symbol	 = "Year of Birth &#x25BC;";
		 break;
		 case '6-A':
		  $query = " age ASC";
		  $Orig_code = "6-A";
		  $Rev_code  = "6-D";
		  $label	 	 = "Age";
		  $label_symbol	 = "Age &#x25B2;";
		 break;
		 case '6-D':
		  $query = " age DESC"; 
		  $Orig_code = "6-D";
		  $Rev_code  = "6-A";
		  $label	 	 = "Age";
		  $label_symbol	 = "Age &#x25BC;";
		 break;
		 case '7-A':
		  $query = " campaignType ASC"; 
		  $Orig_code = "7-A";
		  $Rev_code  = "7-D";
		  $label	 	 = "Campaign Type";
		  $label_symbol	 = "Campaign Type &#x25B2;";
		 break;
		 case '7-D':
		  $query = " campaignType DESC"; 
		  $Orig_code = "7-D";
		  $Rev_code  = "7-A";
		  $label	 	 = "Campaign Type";
		  $label_symbol	 = "Campaign Type &#x25BC;";
		 break;
		 case '8-A':
		  $query = " campaignName ASC"; 
		  $Orig_code = "8-A";
		  $Rev_code  = "8-D";
		  $label	 	 = "Campaign Name";
		  $label_symbol	 = "Campaign Name &#x25B2;";
		 break;
		 case '8-D':
		  $query = " campaignName DESC"; 
		  $Orig_code = "8-D";
		  $Rev_code  = "8-A";
		  $label	 	 = "Campaign Name";
		  $label_symbol	 = "Campaign Name &#x25BC;";
		 break;
		 case '9-A':
		  $query = " countryName ASC";
		  $Orig_code = "9-A";
		  $Rev_code  = "9-D";
		  $label	 	 = "Country";
		  $label_symbol	 = "Country &#x25B2;";
		 break;
		 case '9-D':
		  $query = " countryName DESC"; 
		  $Orig_code = "9-D";
		  $Rev_code  = "9-A";
		  $label	 	 = "Country";
		  $label_symbol	 = "Country &#x25BC;";
		 break;
		 case '10-A':
		  $query = " tdate ASC"; 
		  $Orig_code = "10-A";
		  $Rev_code  = "10-D";
		  $label	 	 = "Date";
		  $label_symbol	 = "Date &#x25B2;";
		 break;
		 case '10-D':
		  $query = " tdate DESC"; 
		  $Orig_code = "10-D";
		  $Rev_code  = "10-A";
		  $label	 	 = "Date";
		  $label_symbol	 = "Date &#x25BC;";
		 break;
		}
		if($returnType=='query')
			return $query;
		elseif($returnType=='Orig_code')
			return $Orig_code;
		elseif($returnType=='Rev_code')
			return $Rev_code;
		elseif($returnType=='label')
			return $label;
		elseif($returnType=='label_symbol')
			return $label_symbol;
	}
	
	function encode_base64($sData){
		$sBase64 = base64_encode($sData);
		return str_replace('=', '', strtr($sBase64, '+/', '-_'));
	}
	
	function reportCode()
	{
		$encryptID = $this->encode_base64($_SESSION['user_id']);
		return "-".date('Y-m-d')."-$encryptID";
	}
	
	function voters_summary($action='',$id='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(75,'REVIEW');
		
		//USER MANUAL
		$data['USER_MANUAL'] 	  = $this->modules->user_manual(43);
		$data['PUBLISH_CAMPAIGN'] =  $this->modules->crud_checker(29,'PUBLISH CAMPAIGN');
		
		$WHERE  = " WHERE ";
		if($_SESSION['super_admin']!='y'  AND $_SESSION['countryID']!=0)
			$WHERE .= "  countryID = ".$_SESSION['countryID']." AND ";
		
		//$votingCampaignID 	= $id;
		
		$table						= 'item_views';
		$data['vfile']				= 'voters_summary.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/voters_summary> Voters Summary </a>';
		
		//TOTAL NUMBER OF ROWS
		$sort='n';
		extract($_POST);
		$cond="";
		$having="";
		$limit =isset($selpage)? $selpage:0;
		$campaignType = "";
		
		if((isset($DateFrom) AND isset($DateTo)) AND ($DateFrom!='' AND $DateTo!='') AND !isset($Reset)){
			$WHERE   .= "  (tdate >= '$DateFrom' AND tdate <= '$DateTo') AND ";
			$data['DateFrom'] = $DateFrom;
			$data['DateTo']   = $DateTo;

		}else{
			$data['DateFrom'] = "";
			$data['DateTo']   = "";
		}
		
		if(!isset($Submit) AND !isset($selpage) OR isset($Reset)){
			$data['iLike'] = 'iLike';
			$data['iWant'] = 'iWant';
		}
		
		$valid_query=TRUE;
		if(isset($Submit) OR isset($selpage))
		{	
			$val1 = mysql_real_escape_string($val1);
			$val1 = trim($val1);
			$val2 = mysql_real_escape_string($val2);
			$val2 = trim($val2);
			
			$condition = '';
			switch($cond1){
				case 'equal': 
					$condition = '=';
				break;
				case 'containing': 
					$condition = 'like';
				break;
				case 'greaterThan': 
					$condition = '>=';
				break;
				case 'lessThan': 
					$condition = '<=';
				break;
				case 'in': 
					$condition = 'in';
				break;
				case 'between': 
					$condition = 'between';
				break;
			}
			
			//fname, lname, gender, email, department, camapignName, countryName
			if(($opt1=='fname' OR $opt1=='lname' OR $opt1=='gender' OR $opt1=='email' OR $opt1=='department' OR $opt1=='campaignName' OR $opt1=='countryName') AND $val1!='')
			{
				if($condition=='=' OR $condition=='>=' OR $condition=='<=')
					$cond = "  $opt1 $condition '$val1'";
				if($condition=='like')
					$cond = "  $opt1 $condition '%$val1%'";
				if($condition=='in')
					$cond = "  $opt1 $condition ('" . str_replace(",", "','", $val1) . "')";	
			}
			
			
			//Year of birth, age
			if(($opt1=='year_of_birth' OR $opt1=='age') AND $val1!='' AND $condition!='like' AND is_numeric($val1)){ 
				
				if($condition=='in' AND (is_numeric($val1) OR strpos($val1,',')==TRUE))
					$cond = "  $opt1 $condition ('" . str_replace(",", "','", $val1) . "')";
				elseif($condition!='in' AND is_numeric($val1))
					$cond = "  $opt1 $condition $val1";
			}
			
			if($condition=='between' & $this->checkStr($val1)==TRUE)
					$cond = "  $opt1 $condition ".stripslashes($val1);
			
			/*2ND SET*/
			switch($cond2){
				case 'equal': 
					$condition2 = '=';
				break;
				case 'containing': 
					$condition2 = 'like';
				break;
				case 'greaterThan': 
					$condition2 = '>=';
				break;
				case 'lessThan': 
					$condition2 = '<=';
				break;
				case 'in': 
					$condition2 = 'in';
				break;
				case 'between': 
					$condition2 = 'between';
				break;
			}
			
			if($val1!=""){
				//fname, lname, gender, email, department, camapignName, countryName
				if(($opt2=='fname' OR $opt2=='lname' OR $opt2=='gender' OR $opt2=='email' OR $opt2=='department' OR $opt2=='campaignName'  OR $opt2=='countryName') AND $val2!='')
				{
					if($condition2=='=' OR $condition2=='>=' OR $condition2=='<=')
						$cond .= " $operator $opt2 $condition2 '$val2'";
					if($condition2=='like')
						$cond .= " $operator $opt2 $condition2 '%$val2%'";
					if($condition2=='in')
						$cond .= " $operator $opt2 $condition2 ('" . str_replace(",", "','", $val2) . "')";
				}
				
				//Year of birth, age
				if(($opt2=='year_of_birth' OR $opt2=='age') AND $val2!='' AND $condition2!='like' AND is_numeric($val2))
				{
					if($condition2=='in' AND (is_numeric($val2) OR strpos($val2,',')==TRUE))
						$cond .= " $operator $opt2 $condition2 ('" . str_replace(",", "','", $val2) . "')";
					elseif($condition2!='in' AND is_numeric($val2) AND $condition2!='between')
						$cond .= " $operator $opt2 $condition2 $val2";
				}
				
				if($condition2=='between' & $this->checkStr($val2)==TRUE)
						$cond .= "  $operator $opt2 $condition2 ".stripslashes($val2);
			}
			
			//IF THEIR NO POSSIBLE RESULT
			if(isset($Submit)){
				if($cond=="" AND ($val1!='' OR $val2!='')) 
					$valid_query=FALSE;
			}
			
			//CAMPAIGN TYPE
			if($cond!="") $cond = " ($cond) ";
			$cond .= ($cond!="") ? " AND " : "";
			if(isset($iLike) AND isset($iWant))
				$cond .= " (campaignType='iLike' OR  campaignType='iWant') ";
			elseif(isset($iLike) AND !isset($iWant))
				$cond .= " (campaignType='iLike') ";
			elseif(isset($iWant) AND !isset($iLike))
				$cond .= " (campaignType='iWant') ";
			if(!isset($iLike) AND !isset($iWant))
				$cond .= " (campaignType!='iLike' AND  campaignType!='iWant') ";
			
			$cond = ($cond=="") ? "" : " $cond ";
			$WHERE  .=  " $cond AND ";
		}
		
		$WHERE = substr($WHERE,0,-4);
	
		
		$data['POST'] = $_POST;
		if(isset($Reset)){
			$data['POST'] = array();
			$WHERE  = "";
			if($_SESSION['super_admin']!='y'  AND $_SESSION['countryID']!=0){
				$WHERE = "WHERE countryID =".$_SESSION['countryID']." ";
			}
			$limit=0;
		}
		
		if(isset($Submit)) $limit=0;
		if($WHERE==" WHERE ") $WHERE="";
		
		//ORDER
		$ORDER = $this->sortingRef_Voters('10-D','query');
		$data['order'] = $this->sortingRef_Voters('10-D','Orig_code');
		$order_code = '10-D';
		$label = "Date";
		if(isset($order)){
			$ORDER = $this->sortingRef_Voters($order,'query');
			$data['order'] = $this->sortingRef_Voters($order,'Orig_code');
			$order_code = $order;
			$label = $this->sortingRef_Voters($order,'label');
		}
		
		//VALID QUERY
		if($valid_query!=TRUE) $WHERE = "WHERE campaignID = 0";
		
		$sql  = "SELECT campaignID, vID, fname, lname, gender, email, department, year_of_birth, age, 
				  tdate, campaignType, campaignName, countryName
				  FROM voters_reports
				  $WHERE  ORDER BY $ORDER";
		
		$sql_csv = $this->db->query("SELECT fname AS First_Name, lname as Last_Name, gender as Gender, email as Email, department as Department, year_of_birth as Year_of_Birth, age as Age, 
									tdate as Campaign_Date, campaignType AS Campaign_Type, campaignName Campaign_Name, countryName AS Country
									FROM voters_reports
									$WHERE  ORDER BY $ORDER");
		$sql_csv = $sql_csv->result_array();
		//generate csv file
		$csv  ="All Items\n";
		$csv .= "No, First_Name, Last_Name, Gender, Email, Department, Year_of_Birth, Age, Campaign_Date, Campaign_Type, Campaign_Name, Country\n";
		$x=0; $Age=0;
		foreach($sql_csv as $i)
		{ extract($i);	$x++;  $Age = date('Y')-$Year_of_Birth;
		 $csv .= "$x, $First_Name, $Last_Name, $Gender, $Email, $Department, $Year_of_Birth, $Age, ". $this->convertDate('date',"$Campaign_Date 00:00:00").", $Campaign_Type, $Campaign_Name, $Country\n";
		}
		write_file(getcwd()."/files/csv/Voters_Summary".$this->reportCode().".csv",$csv);
		$data['csvFile'] = "Voters_Summary".$this->reportCode().".csv";
		
		//SORT STATUS
		$limit = ($sort)=='y' ? 0 : $limit;
		
		$ctr = $this->db->query($sql);
		$ctr = $ctr->result_array();
		$data['totrec'] = count($ctr);
		$data['limit']  = $limit;
	
		
		$sql 	 = $this->db->query($sql." LIMIT $limit,20");
		$items	 = $sql->result_array();
		
	
		$table= "<table id='voter_summary' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:12px;' class='iLike_Result_Table2'>
				<tr>
					<th style='width:10px;text-align:center;color:white;padding:0px' bgcolor='#bb1d1d'>   					   <b>No 		 		  	</b></th> 
					<th style='width:25px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"0-A\")'>       <b>First Name  	  		</b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"1-A\")'>       <b>Last Name  	  	  	</b></th> 
					<th style='width:19px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"2-A\")'>   	   <b>Gender   	 	      	</b></th> 
					<th style='width:100px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' onclick='sortBy(\"3-A\")'>      <b>Email  	  		  	</b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"4-A\")'>       <b>Department  	  		</b></th> 
					<th style='width:50px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"5-A\")'>       <b>Year of Birth 	  	</b></th> 
					<th style='width:80px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"6-A\")'>       <b>Age  	  		  		</b></th> 
					<th style='width:50px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"7-A\")'>       <b>Campaign Type  	  	</b></th> 
					<th style='width:253px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"8-A\")'>       <b>Campaign Name  	  	</b></th> 
					<th style='width:100px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"9-A\")'>       <b>Country  	  			</b></th> 
					<th style='width:100px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' onclick='sortBy(\"10-A\")'>      <b>Date  	  	  		</b></th> 
					<th style='width:50px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"11-A\")'>       <b>Votes  	  	  		</b></th> 
				</tr>";
				
		//REPLACE ORDER
		$table = str_replace($order_code,$this->sortingRef_Voters($order_code,'Rev_code'),$table);
		$table = str_replace($label,$this->sortingRef_Voters($order_code,'label_symbol'),$table);		 
		
					$x = $limit;			
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					$orig_itemName="";
					foreach($items as $r) { 
					extract($r);
					$c = (($x++)%2) == 0 ? "class='alter'" :  "";
					
					$TD = "<td $c style='text-align:center;'> <a onclick=\"viewDialog('$campaignType',$campaignID,$vID)\" style='cursor:pointer;'> Details </a>	</td>";
					if($data['PUBLISH_CAMPAIGN']!=TRUE AND $campaignType=='iWant')
						$TD = "<td style='text-align:center;'> <a style='cursor:pointer;'> N/A </a>	</td>";
		$table.= "<tr>
				  <td $c>													$x      		</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$fname  		</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$lname  		</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$gender  		</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$email  		</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$department  	</td>
				  <td $c style='text-align:center;padding-left:5px;'>	$year_of_birth  </td>
				  <td $c style='text-align:center;padding-left:5px;'>	$age  			</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$campaignType  	</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$campaignName  	</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$countryName  	</td>
				  <td $c style='text-align:left;padding-left:5px;'>	". $this->convertDate('date',"$tdate 00:00:00") ."</td>
					$TD";
				  }
		$table.= "</tr>";
					if(!$items)
						$table.=  "<tr><td colspan='14'>No match found, please check report parameters.</td></tr>";
		$table.= "</table>";	
		
		
		$data['table'] 		= $table;

		$viewer = 'innerPages';	    
		if($this->modules->access_checker()==TRUE)
		{
			$this->load->view($viewer,$data); 
		}else{
			$data['vfile']				= 'login.php';
			$data['title']				= 'SMBi System Log-in | SMBi';
			$data['page_title']			= 'SMBi System Log-in';
			$data['meta_description']	= 'San Miguel Brewing International';
			$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
			$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
			$this->load->view('login',$data); 	
		}
	}
	
	function item_views_eCat($action='',$id='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(70,'REVIEW');
		
		$filter_WHERE='WHERE ec_items.id IN (SELECT DISTINCT(itemID) FROM ecitem_views)';
		
		//$votingCampaignID 	= $id;
		
		$table						= 'item_views';
		$data['vfile']				= 'item_views_eCat.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/item_views_eCat> eCatalogue Views per Item </a>';
		
		//TOTAL NUMBER OF ROWS			
		
		extract($_POST);
		$WHERE 	 =  $filter_WHERE;
	
		if(isset($filter))
		{
			$WHERE 	 = '';
			if($cyear!=''  AND $cyear!='all') 	 	 $WHERE  .=  " YEAR(ec_items.dateReleased) = $cyear AND";
			
			if($fmonth!='' AND $fmonth!='all' AND $tmonth!='' AND $tmonth!='all'){ 	 	 
					$WHERE  .=  " MONTH(ec_items.dateAdded) >= $fmonth AND  MONTH(ec_items.dateAdded) <= $tmonth AND";
			}
			$WHERE  =  substr("WHERE ec_items.id IN (SELECT DISTINCT(itemID) FROM ecitem_views) AND ".$WHERE,0,-4);
		}
		
		$data['POST'] = $_POST;
		
		$sql   = $this->db->query("SELECT ec_items.id as itemID, itemCode, ecID,
								  (SELECT image FROM ecitems_images WHERE defaultStatus = 1 AND itemID = ec_items.id) as item_image,
								   itemName, (SELECT COUNT(id) FROM ecitem_views WHERE ecitem_views.itemID = ec_items.id) num_views, dateReleased
								   FROM ec_items 		 
								   $WHERE
								   GROUP BY ec_items.id ORDER BY num_views DESC");
		$data['reports']   = $sql->result_array();
		

		$viewer = 'innerPages';	    
		if($this->modules->access_checker()==TRUE)
		{
			$this->load->view($viewer,$data); 
		}else{
			$data['vfile']				= 'login.php';
			$data['title']				= 'SMBi System Log-in | SMBi';
			$data['page_title']			= 'SMBi System Log-in';
			$data['meta_description']	= 'San Miguel Brewing International';
			$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
			$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
			$this->load->view('login',$data); 	
		}
	}
	
	function item_distribution_Preview($view='',$countryID='',$month='',$year='',$fld='',$fld_val='')
	{
		$table='';
		$data['typeView']      = $view;
		$data['fld']       = $fld;
		$data['fld_val']   = $fld_val;
		$data['month']     = ($month=='') 	  ? 'null' : $this->month($month);
		$data['year']      = ($year=='') 	  ? 'null' : $year;
		$data['countryID'] = ($countryID=='') ? 'null' : $countryID;
		
		//POSM TYPE
		//echo $fld;
		switch($fld){
		case 'POSMTypeID':
			$itm_type = "(SELECT typeName FROM POSM_Type WHERE POSM_Type.id = items.POSMTypeID)";
		break;		
		case 'POSMStatusID':
			$itm_type = "(SELECT statusName FROM POSM_Status WHERE POSM_Status.id = items.POSMStatusID)";
		break;
		case 'OUTLETStatusID':
			$itm_type = "(SELECT statusName FROM OUTLET_Status WHERE OUTLET_Status.id = items.OUTLETStatusID)";
		break;
		case 'PremiumTypeID':
			$itm_type = "(SELECT premiumTypeName FROM premiumItemType WHERE premiumItemType.id = items.PremiumTypeID)";
		break;
		case 'MaterialTypeID':
			$itm_type = "(SELECT materialName FROM MATERIAL_Type WHERE MATERIAL_Type.id = items.MaterialTypeID)";
		break;
		case 'brandID':
			$itm_type = "(SELECT brandName FROM brands WHERE brands.id = items.brandID)";
		break;
		}
		
		$fld = "AND items.$fld = $fld_val"; 
		
		$data['month_in_word'] = $this->month($month);
		
		//print_r($_POST);
		extract($_POST);
		$data['user_id'] = isset($user_id) ? $user_id : 0;
		$cond='';
		
		if(isset($Submit) OR isset($selpage))
		{	
			$val1 = mysql_real_escape_string($val1);
			$val2 = mysql_real_escape_string($val2);
			
			$condition = '';
			switch($cond1){
				case 'equal': 
					$condition = '=';
				break;
				case 'containing': 
					$condition = 'like';
				break;
				case 'greaterThan': 
					$condition = '>=';
				break;
				case 'lessThan': 
					$condition = '<=';
				break;
			}
			
			//ITEM CODE
			if($opt1=='itemCode' AND $val1!='')
			{
				if($condition=='=')
					$cond = " AND items.itemCode $condition '$val1'";
				if($condition=='like')
					$cond = " AND items.itemCode $condition '%$val1%'";
			}
			
			//ITEM NAME
			if($opt1=='itemName')
			{
				if($condition=='=')
					$cond = " AND items.itemName $condition '$val1'";
				if($condition=='like')
					$cond = " AND items.itemName $condition '%$val1%'";
			}
			
			//USER NAME
			if($opt1=='user_id')
			{
				if($condition=='=')
					$cond = " AND admin_users.full_name $condition '$val1'";
				if($condition=='like')
					$cond = " AND admin_users.full_name $condition '%$val1%'";
			}
			
			//PUBLISH
			if($opt1=='publish')
			{
				if($condition=='=')
					$val1 = ($val1=='yes' OR $val1=='Yes') ? 'y' : 'n';
					$cond = " AND items.publish $condition '$val1'";
				if($condition=='like')
					$cond = " AND items.publish $condition '%$val1%'";
			}
			
			//LOCAL PRICE
			if($opt1=='UnitPrice' AND $val1!='' AND $condition!='like' AND is_numeric($val1)) 
				$cond = " AND items.UnitPrice $condition $val1";
			
			//USD PRICE
			if($opt1=='USD_Price' AND $val1!='' AND $condition!='like' AND is_numeric($val1)) 
				$cond = " AND items.USD_Price $condition $val1";
			
			//dateAdded
			if($opt1=='dateAdded' AND $val1!='' AND $condition!='like') 
				$cond = " AND items.dateAdded $condition '$val1'";
				
			//dateReleased
			if($opt1=='dateReleased' AND $val1!='' AND $condition!='like') 
				$cond = " AND items.dateReleased $condition '$val1'";
			
			/*2ND SET*/
			switch($cond2){
				case 'equal': 
					$condition2 = '=';
				break;
				case 'containing': 
					$condition2 = 'like';
				break;
				case 'greaterThan': 
					$condition2 = '>=';
				break;
				case 'lessThan': 
					$condition2 = '<=';
				break;
			}
			
			//ITEM CODE
			if($opt2=='itemCode' AND $val2!='')
			{
				if($condition2=='=')
					$cond .= " $operator items.itemCode $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator items.itemCode $condition2 '%$val2%'";
			}
			
			//ITEM NAME
			if($opt2=='itemName' AND $val2!='')
			{
				if($condition2=='=')
					$cond .= " $operator items.itemName $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator items.itemName $condition2 '%$val2%'";
			}
			
			//USER NAME
			if($opt2=='user_id' AND $val2!='')
			{
				if($condition2=='=')
					$cond .= " $operator admin_users.full_name $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator admin_users.full_name $condition2 '%$val2%'";
			}
			
			//PUBLISH
			if($opt2=='publish' AND $val2!='')
			{
				if($condition2=='=')
					$val2 = ($val2=='yes' OR $val2=='Yes') ? 'y' : 'n';
					$cond .= " $operator items.publish $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator items.publish $condition2 '%$val2%'";
			}
			
			
			//LOCAL PRICE
			if($opt2=='UnitPrice' AND $val2!='' AND $condition2!='like' AND is_numeric($val2)) 
				$cond .= "$operator items.UnitPrice $condition2 $val2";
			
			//USD PRICE
			if($opt2=='USD_Price' AND $val2!='' AND $condition2!='like' AND is_numeric($val2)) 
				$cond .= " $operator items.USD_Price $condition2 $val2";
			
			//SPECIAL CASE
			if($opt1=='dateAdded' AND $val1!='' AND $condition=='>=' AND $opt2=='dateAdded' AND $val2!='' AND $condition2=='<=')
				$operator = 'AND';
			
			if($opt1=='dateReleased' AND $val1!='' AND $condition=='>=' AND $opt2=='dateReleased' AND $val2!='' AND $condition2=='<=')
				$operator = 'AND';
			
			//dateAdded 
			if($opt2=='dateAdded' AND $val2!='' AND $condition2!='like') 
				$cond .= " $operator items.dateAdded $condition2 '$val2'";
				
			//dateReleased
			if($opt2=='dateReleased' AND $val2!='' AND $condition2!='like') 
				$cond .= " $operator items.dateReleased $condition2 '$val2'";
		}
		
		$data['POST'] = $_POST;
		
		if(isset($Reset)){
			$data['POST'] = array();
			$cond='';
		}
		
		//BREAD CRUMBS
		$HTTP_PATH 					 = HTTP_PATH."report/iLike";
		$data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
		$data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/BU_activeness_index> Activeness of Business Units Index </a>';
		
		$limit =isset($selpage)? $selpage:0;
		
		$WHERE = "WHERE items.countryID = $countryID";
		if($countryID==0)
			$WHERE = "WHERE items.countryID != 0";
		
		if($view=='gCountry'){
			
			$sql = "SELECT items.id as itemID, itemName, $itm_type as ptype, itemCode, 
					(SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, items.dateAdded as dUploaded, full_name, publish,
					UnitPrice, USD_Price, items.dateReleased as dReleased
					FROM items LEFT JOIN admin_users ON admin_users.id = items.user_id
					$WHERE $fld $cond ORDER BY items.id DESC";
		}if($view=='gYear'){
			$sql = "SELECT items.id as itemID, itemName, $itm_type as ptype, itemCode, 
					(SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, items.dateAdded as dUploaded, full_name, publish,
					UnitPrice, USD_Price, items.dateReleased as dReleased
					FROM items LEFT JOIN admin_users ON admin_users.id = items.user_id
					$WHERE $fld $cond AND YEAR(items.dateAdded)=$year ORDER BY items.id DESC";
					
			$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
			$data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/BU_activeness_per_Year/$countryID'> Activeness of Business Units per Year</a>";		
		}if($view=='gMonth'){
			$sql = "SELECT items.id as itemID, itemName, $itm_type as ptype, itemCode, 
					(SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, items.dateAdded as dUploaded, full_name, publish,
					UnitPrice, USD_Price, items.dateReleased as dReleased
					FROM items LEFT JOIN admin_users ON admin_users.id = items.user_id
					$WHERE $fld $cond AND YEAR(items.dateAdded)=$year AND MONTH(items.dateAdded)=$month ORDER BY items.id DESC";
					
			$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
			$data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/BU_activeness_per_Year/$countryID'> Activeness of Business Units per Year</a>";
			$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
			$data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/BU_activeness_per_Month/$countryID/$year'> Activeness of Business Units per Month</a>";
		}
		
		$ctr = $this->db->query($sql);
		$ctr = $ctr->result_array();
		$data['totrec'] = count($ctr);
		$data['limit']  = $limit;
		
		$sql 	 = $this->db->query($sql." LIMIT $limit,20");
		$items	 = $sql->result_array();
	
		//print_r($items);
			
		$table = "<table id='large' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:12px;' class='iLike_Result_Table tablesorter'>
				<thead>
				<tr>
					<th style='width:10px;text-align:center;color:white;padding:0px' bgcolor='#bb1d1d'>   					   <b>No 		 		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Item Code  	  	  </b></th> 
					<th style='width:19px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Image   	 	      </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Name  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Type  	  		  </b></th> 
					<th style='width:30px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>User  	  		  </b></th> 
					<th style='width:30px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Publish  	  		  </b></th> 
					<th style='width:70px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Local Price  	  		  </b></th> 
					<th style='width:70px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>USD Price  	  		  </b></th> 
					<th style='width:75px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Uploaded  	  </b></th> 
					<th style='width:75px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Released  	  </b></th> 
				</tr>
				</thead>
				<tbody>";
				 
					$x = 0;			
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					foreach($items as $r) { 
					extract($r);
					$ptype = ($ptype=='') 	   ? 'Uncategorize' : $ptype;
					$publish = ($publish=='y') ? 'Yes' : 'No';
					$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 	
		$table .="<tr>
				  <td >													$x      																		</td>
				  <td >													$itemCode      																	</td>
				  <td  style='text-align:center;'>			    		<img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 			</td>
				  <td  style='text-align:left;padding-left:5px;'>		<a href='".HTTP_PATH."gallery/itemInfo2/$itemID' target='_blank'>		$itemName</a>   </td>
				  <td  style='text-align:left;padding-left:5px;'>		$ptype											</td>
				   <td  style='text-align:center;'>						$full_name										</td>
				  <td  style='text-align:center;'>						$publish										</td>
				  <td style='text-align:center;'>						$UnitPrice																		</td>
				  <td style='text-align:center;'>						$USD_Price																		</td>
				  <td style='text-align:center;'>				        ". $dUploaded ."										</td>
				  <td style='text-align:center;'>				        ". $dReleased ."										</td>
				</tr>";}
		$table .="</tbody>";
					if(!$items)
						$table .= "<tr><td colspan='11'>No match found.</td></tr>";
		$table .="</table>";
		
		$data['POST']		= $_POST;
		$data['table'] 		= $table;
		$data['vfile']		= 'item_distribution_Preview.php';
		$data['title']		= 'Items Preview';
		
		
		
	
		if($this->modules->access_checker()==TRUE)
		{
			$this->load->view('innerPages',$data); 
		}else{
			$data['vfile']				= 'login.php';
			$data['title']				= 'SMBi System Log-in | SMBi';
			$data['page_title']			= 'SMBi System Log-in';
			$data['meta_description']	= 'San Miguel Brewing International';
			$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
			$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
			$this->load->view('login',$data); 	
		}
	}
	
	function BU_items($view='',$countryID='',$month='',$year='',$user_id='')
	{
		$data['typeView']  = $view;
		$data['month']     = ($month=='') 	  ? 'null' : $month;
		$data['year']      = ($year=='') 	  ? 'all' :  $year;
		$data['user_id']   = ($user_id=='')   ? 'null' : $user_id;
		$data['countryID'] = ($countryID=='') ? 'null' : $countryID;
		$data['month_in_word'] = $this->month($month);
		
		//print_r($_POST);
		extract($_POST);
		$cond='';
		
		if(isset($Submit) OR isset($selpage))
		{	
			$val1 = mysql_real_escape_string($val1);
			$val1 = str_replace("'","",$val1);
			$val1 = trim($val1);
			$val2 = mysql_real_escape_string($val2);
			$val2 = str_replace("'","",$val2);
			$val2 = trim($val2);
			
			$condition = '';
			switch($cond1){
				case 'equal': 
					$condition = '=';
				break;
				case 'containing': 
					$condition = 'like';
				break;
				case 'greaterThan': 
					$condition = '>=';
				break;
				case 'lessThan': 
					$condition = '<=';
				break;
			}
			
			//COUNTRY
			if($opt1=='cName' AND $val1!='')
			{
				if($condition=='=')
					$cond = " AND cName $condition '$val1'";
				if($condition=='like')
					$cond = " AND cName $condition '%$val1%'";
			}
			
			//ITEM CODE
			if($opt1=='itemCode' AND $val1!='')
			{
				if($condition=='=')
					$cond = " AND itemCode $condition '$val1'";
				if($condition=='like')
					$cond = " AND itemCode $condition '%$val1%'";
			}
			
			//ITEM NAME
			if($opt1=='itemName')
			{
				if($condition=='=')
					$cond = " AND itemName $condition '$val1'";
				if($condition=='like')
					$cond = " AND itemName $condition '%$val1%'";
			}
			
			//ITEM TYPE
			if($opt1=='itemType')
			{
				if($condition=='=')
					$cond = " AND ptype $condition '$val1'";
				if($condition=='like')
					$cond = " AND ptype $condition '%$val1%'";
			}
			
			//USER NAME
			if($opt1=='user_id')
			{
				if($condition=='=')
					$cond = " AND full_name $condition '$val1'";
				if($condition=='like')
					$cond = " AND full_name $condition '%$val1%'";
			}
			
			//PUBLISH
			if($opt1=='publish')
			{
				if($condition=='=')
					$val1 = ($val1=='yes' OR $val1=='Yes') ? 'y' : 'n';
					$cond = " AND publish $condition '$val1'";
				if($condition=='like')
					$cond = " AND publish $condition '%$val1%'";
			}
			
			//LOCAL PRICE
			if($opt1=='UnitPrice' AND $val1!='' AND $condition!='like' AND is_numeric($val1)) 
				$cond = " AND UnitPrice $condition $val1";
			
			//USD PRICE
			if($opt1=='USD_Price' AND $val1!='' AND $condition!='like' AND is_numeric($val1)) 
				$cond = " AND USD_Price $condition $val1";
			
			//dateAdded
			if($opt1=='dateAdded' AND $val1!='' AND $condition!='like') 
				$cond = " AND dUploaded $condition '$val1'";
				
			//dateReleased
			if($opt1=='dateReleased' AND $val1!='' AND $condition!='like') 
				$cond = " AND dReleased $condition '$val1'";
			
			/*2ND SET*/
			switch($cond2){
				case 'equal': 
					$condition2 = '=';
				break;
				case 'containing': 
					$condition2 = 'like';
				break;
				case 'greaterThan': 
					$condition2 = '>=';
				break;
				case 'lessThan': 
					$condition2 = '<=';
				break;
			}
			
			//COUNTRY
			if($opt2=='cName' AND $val2!='')
			{
				if($condition2=='=')
					$cond .= " $operator cName $condition '$val1'";
				if($condition2=='like')
					$cond .= " $operator cName $condition '%$val1%'";
			}
			
			//ITEM CODE
			if($opt2=='itemCode' AND $val2!='')
			{
				if($condition2=='=')
					$cond .= " $operator itemCode $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator itemCode $condition2 '%$val2%'";
			}
			
			//ITEM NAME
			if($opt2=='itemName' AND $val2!='')
			{
				if($condition2=='=')
					$cond .= " $operator itemName $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator itemName $condition2 '%$val2%'";
			}
			
			//ITEM TYPE
			if($opt2=='itemType' AND $val2!='')
			{
				if($condition2=='=')
					$cond .= " $operator ptype $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator ptype $condition2 '%$val2%'";
			}
			
			//USER NAME
			if($opt2=='user_id' AND $val2!='')
			{
				if($condition2=='=')
					$cond .= " $operator full_name $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator full_name $condition2 '%$val2%'";
			}
			
			//PUBLISH
			if($opt2=='publish' AND $val2!='')
			{
				if($condition2=='=')
					$val2  = ($val2=='yes' OR $val2=='Yes') ? 'y' : 'n';
					$cond .= " $operator publish $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator publish $condition2 '%$val2%'";
			}
			
			
			//LOCAL PRICE
			if($opt2=='UnitPrice' AND $val2!='' AND $condition2!='like'  AND is_numeric($val2)) 
				$cond .= "$operator UnitPrice $condition2 $val2";
			
			//USD PRICE
			if($opt2=='USD_Price' AND $val2!='' AND $condition2!='like'  AND is_numeric($val2)) 
				$cond .= " $operator USD_Price $condition2 $val2";
			
			//SPECIAL CASE
			if($opt1=='dateAdded' AND $val1!='' AND $condition=='>=' AND $opt2=='dateAdded' AND $val2!='' AND $condition2=='<=')
				$operator = 'AND';
			
			if($opt1=='dateReleased' AND $val1!='' AND $condition=='>=' AND $opt2=='dateReleased' AND $val2!='' AND $condition2=='<=')
				$operator = 'AND';
			
			//dateAdded 
			if($opt2=='dateAdded' AND $val2!='' AND $condition2!='like') 
				$cond .= " $operator dUploaded $condition2 '$val2'";
				
			//dateReleased
			if($opt2=='dateReleased' AND $val2!='' AND $condition2!='like') 
				$cond .= " $operator dReleased $condition2 '$val2'";
		}
		
		$data['POST'] = $_POST;
		
		if(isset($Reset)){
			$data['POST'] = array();
			$cond='';
		}
		
		$WHERE = "WHERE cID = $countryID";
		if($countryID==0){
			$WHERE = "WHERE cID!=0";
		}
		
		//BREAD CRUMBS
		$data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	
		
		$limit =isset($selpage)? $selpage:0;
		if($view=='gCountry'){
			$sql = "SELECT itemID, itemName, ptype, itemCode, cName,
			    item_image, dUploaded, full_name, publish,  UnitPrice, USD_Price,  dReleased
				FROM item_db_reports
				$WHERE $cond ORDER BY iID DESC";	
			$data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/BU_activeness_index> Activeness of Business Units Index </a>';

		}
		elseif($view=='gYear'){
		
			$sql = "SELECT itemID, itemName, ptype, itemCode, cName,
					item_image, dUploaded, full_name, publish, UnitPrice, USD_Price,  dReleased 
					FROM item_db_reports 
					$WHERE $cond AND YEAR(dUploaded) = $year ORDER BY iID DESC";
			$data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/BU_activeness_index> Activeness of Business Units Index </a>';		
			$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
			$data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/BU_activeness_per_Year/$countryID'> Activeness of Business Units per Year</a>";
		}
		elseif($view=='gMonth')
		{
			$sql = "SELECT itemID, itemName, ptype, itemCode, cName,
					item_image, dUploaded, full_name, publish, UnitPrice, USD_Price,  dReleased 
				    FROM item_db_reports 
					$WHERE $cond AND MONTH(dUploaded) = $month AND YEAR(dUploaded) = $year ORDER BY iID DESC";
			$data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/BU_activeness_index> Activeness of Business Units Index </a>';		
			$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
			$data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/BU_activeness_per_Year/$countryID'> Activeness of Business Units per Year</a>";
			$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
			$data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/BU_activeness_per_Month/$countryID/$year'> Activeness of Business Units per Month</a>";
		}
		elseif($view=='BU_activeness_Users')
		{
			$data['disable_user'] = TRUE;
			$sql = "SELECT itemID, itemName, ptype, itemCode, cName,
					item_image, dUploaded, full_name, publish, UnitPrice, USD_Price,  dReleased 
					FROM item_db_reports 
					WHERE cID = $countryID AND userID = $user_id $cond ORDER BY iID DESC";
					
			$data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/BU_activeness_Users>  Activeness of Users Index</a>';
		}
		elseif($view=='BU_activeness_Users_per_Year')
		{
			$data['disable_user'] = TRUE;
			$sql = "SELECT itemID, itemName, ptype, itemCode, cName,
					item_image, dUploaded, full_name, publish, UnitPrice, USD_Price,  dReleased 
					FROM item_db_reports 
					WHERE cID = $countryID AND userID = $user_id AND YEAR(dUploaded) = $year $cond ORDER BY iID DESC";
			
			$data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
			$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
			$data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/BU_activeness_Users> Activeness of Users Index  </a>';
			$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
			$data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/BU_activeness_Users_per_Year/$user_id/$countryID'> Activeness of Users per Year  </a>";

		}
		elseif($view=='BU_activeness_Users_per_Month')
		{
			$data['disable_user'] = TRUE;
			$sql = "SELECT itemID, itemName, ptype, itemCode, cName,
					item_image, dUploaded, full_name, publish, UnitPrice, USD_Price,  dReleased 
					FROM item_db_reports 
					WHERE cID = $countryID AND userID = $user_id AND YEAR(dUploaded) = $year AND MONTH(dUploaded) = $month $cond ORDER BY iID DESC";
			
			$data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
			$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
			$data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/BU_activeness_Users> Activeness of Users Index  </a>';
			$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
			$data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/BU_activeness_Users_per_Year/$user_id/$countryID'> Activeness of Users per Year  </a>";
			$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
			$data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/BU_activeness_Users_per_Month/$user_id/$countryID/$year'> Activeness of Users per Month  </a>";
		}
	
		$ctr = $this->db->query($sql);
		$ctr = $ctr->result_array();
		$data['totrec'] = count($ctr);
		$data['limit']  = $limit;
		
		$sql 	 = $this->db->query($sql." LIMIT $limit,20");
		$items	 = $sql->result_array();
		
	
		$table= "<table id='large' cellpadding='0' cellspacing='0' border=1 style='width:120%;font-size:12px;' class='iLike_Result_Table tablesorter'>
				<thead>
				<tr>
					<th style='width:10px;text-align:center;color:white;padding:0px' bgcolor='#bb1d1d'>   					   <b>No 		 		  </b></th> 
					<th style='width:10px;text-align:center;color:white;padding:0px' bgcolor='#bb1d1d'>   					   <b>Country 		 	  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Item Code  	  	  </b></th> 
					<th style='width:19px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Image   	 	      </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Name  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Type  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>User  	  		  </b></th> 
					<th style='width:30px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Publish  	  		  </b></th> 
					<th style='width:70px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Local Price  	  	  </b></th> 
					<th style='width:70px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>USD Price  	  	  </b></th> 
					<th style='width:75px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Uploaded  	  	  </b></th> 
					<th style='width:75px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Released  	  	  </b></th> 
				</tr>
				</thead>
				<tbody>";
				 
					$x = $limit;			
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					foreach($items as $r) { 
					extract($r);
					$ptype = ($ptype=='') 	   ? 'Uncategorize' : $ptype;
					$publish = ($publish=='y') ? 'Yes' : 'No';
					$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 	
		$table.= "<tr>
				  <td>													$x      																		</td>
				  <td>													$cName      																	</td>
				  <td>													$itemCode      																	</td>
				  <td style='text-align:center;'>			    		<img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 	</td>
				  <td style='text-align:left;padding-left:5px;'>		<a href='".HTTP_PATH."gallery/itemInfo2/$itemID' target='_blank'>		$itemName</a>  </td>
				  <td style='text-align:left;padding-left:5px;'>		$ptype																			</td>
				  <td style='text-align:left;padding-left:5px;'>		$full_name																		</td>
				  <td style='text-align:center;'>						$publish																		</td>
				  <td style='text-align:center;'>						$UnitPrice																		</td>
				  <td style='text-align:center;'>						$USD_Price																		</td>
				  <td style='text-align:center;'>				        ". $dUploaded ."										</td>
				  <td style='text-align:center;'>				        ". $dReleased ."										</td>
				</tr>";}
		$table.= "</tbody>";
					if(!$items)
						$table.=  "<tr><td colspan='12'>No match found.</td></tr>";
		$table.= "</table>";	
		
		
		$data['table'] 		= $table;
		$data['vfile']		= 'BU_items.php';
		$data['title']		= 'Items Preview';
		
		
	
		if($this->modules->access_checker()==TRUE)
		{
			$this->load->view('innerPages',$data); 
		}else{
			$data['vfile']				= 'login.php';
			$data['title']				= 'SMBi System Log-in | SMBi';
			$data['page_title']			= 'SMBi System Log-in';
			$data['meta_description']	= 'San Miguel Brewing International';
			$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
			$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
			$this->load->view('login',$data); 	
		}
	}
	
	function month($month)
	{
		switch ($month) {
            case 1:  $monthString = "January";
                     break;
            case 2:  $monthString = "February";
                     break;
            case 3:  $monthString = "March";
                     break;
            case 4:  $monthString = "April";
                     break;
            case 5:  $monthString = "May";
                     break;
            case 6:  $monthString = "June";
                     break;
            case 7:  $monthString = "July";
                     break;
            case 8:  $monthString = "August";
                     break;
            case 9:  $monthString = "September";
                     break;
            case 10: $monthString = "October";
                     break;
            case 11: $monthString = "November";
                     break;
            case 12: $monthString = "December";
                     break;
            default: $monthString = "All";
                     break;
        }
		
		return $monthString;
	}
	
	function items_price($view='',$countryID='',$month='',$year='')
	{
		$data['typeView']  = $view;
		$data['month']     = ($month=='') 	  ? 'null' : $this->month($month);
		$data['year']      = ($year=='') 	  ? 'null' : $year;
		$data['countryID'] = ($countryID=='') ? 'null' : $countryID;
		
		//print_r($_POST);
		$table='';
		extract($_POST);
		$cond='';
		if(isset($Submit))
		{
			$cond 	 = '';
			if($itemCode!='') 		$cond  .=  " items.itemCode LIKE  '%$itemCode%' AND";
			if($itemName!='') 		$cond  .=  " items.itemName LIKE  '%$itemName%' AND";
			if($selPOSMType!='')	$cond  .=  " items.POSMTypeID =   $selPOSMType  AND";
			if($publish!='')		$cond  .=  " items.publish =      '$publish'  	AND";
			
			if($localPriceFrom!='' AND $localPriceTo!='') 	 	 
					$cond  .=  " items.UnitPrice >= '$localPriceFrom' AND  items.UnitPrice <= '$localPriceTo' AND";
			
			if($USDFrom!='' AND $USDTo!='') 	 	 
					$cond  .=  " items.USD_Price >= '$USDFrom' AND  items.USD_Price <= '$USDTo' AND";
			
			if($DateFrom!='' AND $DateTo!='') 	 	 
					$cond  .=  " date(items.dateAdded) >= '$DateFrom' AND  date(items.dateAdded) <= '$DateTo' AND";
					
			if($cond!='')  $cond  =  substr("AND ".$cond,0,-3);
		}
		
		$data['POST'] = $_POST;
		
		if($view=='GroupByCountry_Price'){
		$sql = "SELECT items.id as itemID, itemName, (SELECT typeName FROM POSM_Type where POSM_Type.id=items.POSMTypeID) as ptype, itemCode, 
					(SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, items.dateAdded as dUploaded, full_name, publish,
					 UnitPrice, USD_Price
					 FROM items LEFT JOIN admin_users ON admin_users.id = items.user_id
					 WHERE items.countryID = $countryID $cond ORDER BY items.id DESC";
		}
		if($view=='AVG_Price_per_Year'){
		$sql = "SELECT items.id as itemID, itemName, (SELECT typeName FROM POSM_Type where POSM_Type.id=items.POSMTypeID) as ptype, itemCode, 
					(SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, items.dateAdded as dUploaded, full_name, publish,
					 UnitPrice, USD_Price
					 FROM items LEFT JOIN admin_users ON admin_users.id = items.user_id
					 WHERE items.countryID = $countryID AND YEAR(items.dateAdded)=$year $cond ORDER BY items.id DESC";
		}if($view=='AVG_Price_per_Month'){
		$sql = "SELECT items.id as itemID, itemName, (SELECT typeName FROM POSM_Type where POSM_Type.id=items.POSMTypeID) as ptype, itemCode, 
					(SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, items.dateAdded as dUploaded, full_name, publish,
					 UnitPrice, USD_Price
					 FROM items LEFT JOIN admin_users ON admin_users.id = items.user_id
					 WHERE items.countryID = $countryID AND YEAR(items.dateAdded)=$year AND MONTH(items.dateAdded)=$month $cond ORDER BY items.id DESC";
		}
		
		$sql 	 = $this->db->query($sql);
		$items	 = $sql->result_array();
		//print_r($items);
			
		$table.= "<table id='large' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:12px;' class='iLike_Result_Table tablesorter'>
				<thead>
				<tr>
					<th style='width:10px;text-align:center;color:white;padding:0px' bgcolor='#bb1d1d'>   					   <b>No 		 		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Item Code  	  	  </b></th> 
					<th style='width:19px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Image   	 	      </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Name  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Type  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Local Price  	  	  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>USD   Price  	  	  </b></th> 
					<th style='width:30px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Publish  	  		  </b></th> 
					<th style='width:75px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Date uploaded  	  </b></th> 
				</tr>
				</thead>
				<tbody>";
				 
					$x = 0;			
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					$sumUnitPrice=0;
					$sumUSD_Price=0;
					foreach($items as $r) { 
					extract($r);
					$ptype = ($ptype=='') 	   ? 'Uncategorize' : $ptype;
					$publish = ($publish=='y') ? 'Yes' : 'No';
					$c = (($x++)%2) == 0 ? "class='alter'" :  "";
					$sumUnitPrice += number_format($UnitPrice, 2, '.', '');
					$sumUSD_Price +=  number_format($USD_Price, 2, '.', '');
		$table.= 	"<tr>
				  <td $c>													$x      																		</td>
				  <td $c>													$itemCode      																	</td>
				  <td $c style='text-align:center;'>			    <img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 			</td>
				  <td $c style='text-align:left;padding-left:5px;'>	<a href='".HTTP_PATH."gallery/itemInfo2/$itemID' target='_blank'>		$itemName</a>   </td>
				  <td $c style='text-align:left;padding-left:5px;'>	$ptype																					</td>
				  <td $c style='text-align:right;padding-right:20px;'>	". number_format($UnitPrice, 2, '.', '') ."											</td>
				  <td $c style='text-align:right;padding-right:20px;'>	". number_format($USD_Price, 2, '.', '')."											</td>
				  <td $c style='text-align:center;'>	$publish																							</td>
				  <td $c style='text-align:center;'>				". date("M d, Y", strtotime($dUploaded)) ."												</td>
				</tr>";}
		$table.= 	"<tr> 
					<td>Average</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td style='text-align:right;padding-right:20px;'>". number_format($sumUnitPrice, 2, '.', '') ."</td>
					<td style='text-align:right;padding-right:20px;'>". number_format($sumUSD_Price, 2, '.', '') ."</td>
					<td></td>
					<td></td>
				</tr>";		
		$table.=	"</tbody>";
					if(!$items)
						$table.= "<tr><td colspan='9'>No match found.</td></tr>"; 
		$table.=	"</table>";
		
		$data['POST']		= $_POST;
		$data['table'] 		= $table;
		$data['vfile']		= 'items_price.php';
		$data['title']		= 'Items Preview';
		
		//BREAD CRUMBS
		$HTTP_PATH 					 = HTTP_PATH."report/iLike";
		$data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
		$data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/AVG_Price_index> Average Price Index </a>';
		
	
		if($this->modules->access_checker()==TRUE)
		{
			$this->load->view('innerPages',$data); 
		}else{
			$data['vfile']				= 'login.php';
			$data['title']				= 'SMBi System Log-in | SMBi';
			$data['page_title']			= 'SMBi System Log-in';
			$data['meta_description']	= 'San Miguel Brewing International';
			$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
			$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
			$this->load->view('login',$data); 	
		}
	}
	
	function viewItems($view='',$countryID='',$month='',$year='',$user_id='')
	{
		if($view=='per_countryAVG')
		{
			$sql = "SELECT items.id as itemID, itemName, (SELECT typeName FROM POSM_Type where POSM_Type.id=items.POSMTypeID) as ptype, itemCode, UnitPrice, USD_Price, 
						(SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, items.dateAdded as dUploaded, full_name, publish 
						 FROM items LEFT JOIN admin_users ON admin_users.id = items.user_id
						 WHERE items.countryID = $countryID AND YEAR(items.dateAdded) = $year ORDER BY UnitPrice DESC";
			$sql 	 = $this->db->query($sql);
			$items	 = $sql->result_array();
			//print_r($items);
			
		echo "<table id='large' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:12px;' class='iLike_Result_Table tablesorter'>
				<thead>
				<tr>
					<th style='width:10px;text-align:center;color:white;padding:0px' bgcolor='#bb1d1d'>   					   <b>No 		 		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Item Code  	  	  </b></th> 
					<th style='width:19px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Image   	 	      </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Name  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Type  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Local Price  	  	  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>USD   Price  	  	  </b></th> 
					<th style='width:30px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Publish  	  		  </b></th> 
					<th style='width:75px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Date uploaded  	  </b></th> 
				</tr>
				</thead>
				<tbody>";
				 
					$x = 0;			
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					$sumUnitPrice=0;
					$sumUSD_Price=0;
					foreach($items as $r) { 
					extract($r);
					$ptype = ($ptype=='') 	   ? 'Uncategorize' : $ptype;
					$publish = ($publish=='y') ? 'Yes' : 'No';
					$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
					$sumUnitPrice += number_format($UnitPrice, 2, '.', '');
					$sumUSD_Price +=  number_format($USD_Price, 2, '.', '');
		echo 	"<tr>
				  <td $c>													$x      																		</td>
				  <td $c>													$itemCode      																	</td>
				  <td $c style='text-align:center;'>			  <img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 	</td>
				  <td $c style='text-align:left;padding-left:5px;'>	<a href='".HTTP_PATH."gallery/itemInfo2/$itemID' target='_blank'>		$itemName</a>  </td>
				  <td $c style='text-align:left;padding-right:20px;'>	$ptype											</td>
				  <td $c style='text-align:right;padding-right:20px;'>	". number_format($UnitPrice, 2, '.', '') ."										</td>
				  <td $c style='text-align:right;padding-right:20px;'>	". number_format($USD_Price, 2, '.', '')."										</td>
				  <td $c style='text-align:center;'>	$publish										</td>
				  <td $c style='text-align:center;'>				". date("M d, Y", strtotime($dUploaded)) ."			</td>
				</tr>";}
		echo 	"<tr> 
					<td>Average</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td style='text-align:right;padding-right:20px;'>". number_format($sumUnitPrice/$x, 2, '.', '') ."</td>
					<td style='text-align:right;padding-right:20px;'>". number_format($sumUSD_Price/$x, 2, '.', '') ."</td>
					<td></td>
					<td></td>
				</tr>";		
				
		echo	"</tbody>";
					if(!$items)
						echo "<tr><td colspan='7'>No match found.</td></tr>";
		echo	"</table>";
			
		}
		elseif($view=='GroupByCountry_Price')
		{
			$sql = "SELECT items.id as itemID, itemName, (SELECT typeName FROM POSM_Type where POSM_Type.id=items.POSMTypeID) as ptype, itemCode, 
					(SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, items.dateAdded as dUploaded, full_name, publish,
					 UnitPrice, USD_Price
					 FROM items LEFT JOIN admin_users ON admin_users.id = items.user_id
					 WHERE items.countryID = $countryID ORDER BY items.id DESC";
			$sql 	 = $this->db->query($sql);
			$items	 = $sql->result_array();
			//print_r($items);
			
		echo "<table id='large' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:12px;' class='iLike_Result_Table tablesorter'>
				<thead>
				<tr>
					<th style='width:10px;text-align:center;color:white;padding:0px' bgcolor='#bb1d1d'>   					   <b>No 		 		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Item Code  	  	  </b></th> 
					<th style='width:19px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Image   	 	      </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Name  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Type  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Local Price  	  	  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>USD   Price  	  	  </b></th> 
					<th style='width:30px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Publish  	  		  </b></th> 
					<th style='width:75px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Date uploaded  	  </b></th> 
				</tr>
				</thead>
				<tbody>";
				 
					$x = 0;			
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					$sumUnitPrice=0;
					$sumUSD_Price=0;
					foreach($items as $r) { 
					extract($r);
					$ptype = ($ptype=='') 	   ? 'Uncategorize' : $ptype;
					$publish = ($publish=='y') ? 'Yes' : 'No';
					$c = (($x++)%2) == 0 ? "class='alter'" :  "";
					$sumUnitPrice += number_format($UnitPrice, 2, '.', '');
					$sumUSD_Price +=  number_format($USD_Price, 2, '.', '');
		echo 	"<tr>
				  <td $c>													$x      																		</td>
				  <td $c>													$itemCode      																	</td>
				  <td $c style='text-align:center;'>			    <img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 			</td>
				  <td $c style='text-align:left;padding-left:5px;'>	<a href='".HTTP_PATH."gallery/itemInfo2/$itemID' target='_blank'>		$itemName</a>   </td>
				  <td $c style='text-align:left;padding-left:5px;'>	$ptype																					</td>
				  <td $c style='text-align:right;padding-right:20px;'>	". number_format($UnitPrice, 2, '.', '') ."											</td>
				  <td $c style='text-align:right;padding-right:20px;'>	". number_format($USD_Price, 2, '.', '')."											</td>
				  <td $c style='text-align:center;'>	$publish																							</td>
				  <td $c style='text-align:center;'>				". date("M d, Y", strtotime($dUploaded)) ."												</td>
				</tr>";}
		echo 	"<tr> 
					<td>Average</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td style='text-align:right;padding-right:20px;'>". number_format($sumUnitPrice/$x, 2, '.', '') ."</td>
					<td style='text-align:right;padding-right:20px;'>". number_format($sumUSD_Price/$x, 2, '.', '') ."</td>
					<td></td>
					<td></td>
				</tr>";		
		echo	"</tbody>";
					if(!$items)
						echo "<tr><td colspan='7'>No match found.</td></tr>";
		echo	"</table>";
			
		}
		elseif($view=='AVG_Price_per_Year')
		{
			$sql = "SELECT items.id as itemID, itemName, (SELECT typeName FROM POSM_Type where POSM_Type.id=items.POSMTypeID) as ptype, itemCode, 
					(SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, items.dateAdded as dUploaded, full_name, publish,
					 UnitPrice, USD_Price
					 FROM items LEFT JOIN admin_users ON admin_users.id = items.user_id
					 WHERE items.countryID = $countryID AND YEAR(items.dateAdded)=$year ORDER BY items.id DESC";
			$sql 	 = $this->db->query($sql);
			$items	 = $sql->result_array();
			//print_r($items);
			
		echo "<table id='large' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:12px;' class='iLike_Result_Table tablesorter'>
				<thead>
				<tr>
					<th style='width:10px;text-align:center;color:white;padding:0px' bgcolor='#bb1d1d'>   					   <b>No 		 		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Item Code  	  	  </b></th> 
					<th style='width:19px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Image   	 	      </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Name  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Type  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Local Price  	  	  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>USD   Price  	  	  </b></th> 
					<th style='width:30px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Publish  	  		  </b></th> 
					<th style='width:75px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Date uploaded  	  </b></th> 
				</tr>
				</thead>
				<tbody>";
				 
					$x = 0;			
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					$sumUnitPrice=0;
					$sumUSD_Price=0;
					foreach($items as $r) { 
					extract($r);
					$ptype = ($ptype=='') 	   ? 'Uncategorize' : $ptype;
					$publish = ($publish=='y') ? 'Yes' : 'No';
					$c = (($x++)%2) == 0 ? "class='alter'" :  "";
					$sumUnitPrice += number_format($UnitPrice, 2, '.', '');
					$sumUSD_Price +=  number_format($USD_Price, 2, '.', '');
		echo 	"<tr>
				  <td $c>													$x      																		</td>
				  <td $c>													$itemCode      																	</td>
				  <td $c style='text-align:center;'>			    <img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 			</td>
				  <td $c style='text-align:left;padding-left:5px;'>	<a href='".HTTP_PATH."gallery/itemInfo2/$itemID' target='_blank'>		$itemName</a>   </td>
				  <td $c style='text-align:left;padding-left:5px;'>	$ptype																					</td>
				  <td $c style='text-align:right;padding-right:20px;'>	". number_format($UnitPrice, 2, '.', '') ."											</td>
				  <td $c style='text-align:right;padding-right:20px;'>	". number_format($USD_Price, 2, '.', '')."											</td>
				  <td $c style='text-align:center;'>	$publish																							</td>
				  <td $c style='text-align:center;'>				". date("M d, Y", strtotime($dUploaded)) ."												</td>
				</tr>";}
		echo 	"<tr> 
					<td>Average</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td style='text-align:right;padding-right:20px;'>". number_format($sumUnitPrice/$x, 2, '.', '') ."</td>
					<td style='text-align:right;padding-right:20px;'>". number_format($sumUSD_Price/$x, 2, '.', '') ."</td>
					<td></td>
					<td></td>
				</tr>";		
		echo	"</tbody>";
					if(!$items)
						echo "<tr><td colspan='7'>No match found.</td></tr>";
		echo	"</table>";
			
		}
		elseif($view=='AVG_Price_per_Month')
		{
			$sql = "SELECT items.id as itemID, itemName, (SELECT typeName FROM POSM_Type where POSM_Type.id=items.POSMTypeID) as ptype, itemCode, 
					(SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, items.dateAdded as dUploaded, full_name, publish,
					 UnitPrice, USD_Price
					 FROM items LEFT JOIN admin_users ON admin_users.id = items.user_id
					 WHERE items.countryID = $countryID AND MONTH(items.dateAdded)=$month AND YEAR(items.dateAdded)=$year ORDER BY items.id DESC";
			$sql 	 = $this->db->query($sql);
			$items	 = $sql->result_array();
			//print_r($items);
			
		echo "<table id='large' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:12px;' class='iLike_Result_Table tablesorter'>
				<thead>
				<tr>
					<th style='width:10px;text-align:center;color:white;padding:0px' bgcolor='#bb1d1d'>   					   <b>No 		 		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Item Code  	  	  </b></th> 
					<th style='width:19px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Image   	 	      </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Name  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Type  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Local Price  	  	  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>USD   Price  	  	  </b></th> 
					<th style='width:30px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Publish  	  		  </b></th> 
					<th style='width:75px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Date uploaded  	  </b></th> 
				</tr>
				</thead>
				<tbody>";
				 
					$x = 0;			
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					$sumUnitPrice=0;
					$sumUSD_Price=0;
					foreach($items as $r) { 
					extract($r);
					$ptype = ($ptype=='') 	   ? 'Uncategorize' : $ptype;
					$publish = ($publish=='y') ? 'Yes' : 'No';
					$c = (($x++)%2) == 0 ? "class='alter'" :  "";
					$sumUnitPrice += number_format($UnitPrice, 2, '.', '');
					$sumUSD_Price +=  number_format($USD_Price, 2, '.', '');
		echo 	"<tr>
				  <td $c>													$x      																		</td>
				  <td $c>													$itemCode      																	</td>
				  <td $c style='text-align:center;'>			    <img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 			</td>
				  <td $c style='text-align:left;padding-left:5px;'>	<a href='".HTTP_PATH."gallery/itemInfo2/$itemID' target='_blank'>		$itemName</a>   </td>
				  <td $c style='text-align:left;padding-left:5px;'>	$ptype																					</td>
				  <td $c style='text-align:right;padding-right:20px;'>	". number_format($UnitPrice, 2, '.', '') ."											</td>
				  <td $c style='text-align:right;padding-right:20px;'>	". number_format($USD_Price, 2, '.', '')."											</td>
				  <td $c style='text-align:center;'>	$publish																							</td>
				  <td $c style='text-align:center;'>				". date("M d, Y", strtotime($dUploaded)) ."												</td>
				</tr>";}
		echo 	"<tr> 
					<td>Average</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td style='text-align:right;padding-right:20px;'>". number_format($sumUnitPrice/$x, 2, '.', '') ."</td>
					<td style='text-align:right;padding-right:20px;'>". number_format($sumUSD_Price/$x, 2, '.', '') ."</td>
					<td></td>
					<td></td>
				</tr>";		
		echo	"</tbody>";
					if(!$items)
						echo "<tr><td colspan='7'>No match found.</td></tr>";
		echo	"</table>";
			
		}
		elseif($view=='GroupByCountry')
		{
			$sql = "SELECT items.id as itemID, itemName, (SELECT typeName FROM POSM_Type where POSM_Type.id=items.POSMTypeID) as ptype, itemCode, 
					(SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, items.dateAdded as dUploaded, full_name, publish 
					 FROM items LEFT JOIN admin_users ON admin_users.id = items.user_id
					 WHERE items.countryID = $countryID ORDER BY items.id DESC";
			$sql 	 = $this->db->query($sql);
			$items	 = $sql->result_array();
			//print_r($items);
			
		echo "<table id='large' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:12px;' class='iLike_Result_Table tablesorter'>
				<thead>
				<tr>
					<th style='width:10px;text-align:center;color:white;padding:0px' bgcolor='#bb1d1d'>   					   <b>No 		 		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Item Code  	  	  </b></th> 
					<th style='width:19px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Image   	 	      </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Name  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Type  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>User  	  		  </b></th> 
					<th style='width:30px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Publish  	  		  </b></th> 
					<th style='width:75px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Date uploaded  	  </b></th> 
				</tr>
				</thead>
				<tbody>";
				 
					$x = 0;			
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					foreach($items as $r) { 
					extract($r);
					$ptype = ($ptype=='') 	   ? 'Uncategorize' : $ptype;
					$publish = ($publish=='y') ? 'Yes' : 'No';
					$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 	
		echo 	"<tr>
				  <td $c>													$x      																		</td>
				  <td $c>													$itemCode      																	</td>
				  <td $c style='text-align:center;'>			    	<img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 	</td>
				  <td $c style='text-align:left;padding-left:5px;'>	<a href='".HTTP_PATH."gallery/itemInfo2/$itemID' target='_blank'>		$itemName</a>  </td>
				  <td $c style='text-align:left;padding-left:5px;'>	$ptype											</td>
				  <td $c style='text-align:left;padding-left:5px;'>	$full_name										</td>
				  <td $c style='text-align:center;'>	$publish										</td>
				  <td $c style='text-align:center;'>				". date("M d, Y", strtotime($dUploaded)) ."			</td>
				</tr>";}
		echo	"</tbody>";
					if(!$items)
						echo "<tr><td colspan='7'>No match found.</td></tr>";
		echo	"</table>";
			
		}
		elseif($view=='per_country')
		{
			$sql = "SELECT items.id as itemID, itemName, (SELECT typeName FROM POSM_Type where POSM_Type.id=items.POSMTypeID) as ptype, itemCode, 
						(SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, items.dateAdded as dUploaded, full_name, publish 
						 FROM items LEFT JOIN admin_users ON admin_users.id = items.user_id
						 WHERE items.countryID = $countryID AND YEAR(items.dateAdded) = $year ORDER BY items.id DESC";
			$sql 	 = $this->db->query($sql);
			$items	 = $sql->result_array();
			//print_r($items);
			
		echo "<table id='large' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:12px;' class='iLike_Result_Table tablesorter'>
				<thead>
				<tr>
					<th style='width:10px;text-align:center;color:white;padding:0px' bgcolor='#bb1d1d'>   					   <b>No 		 		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Item Code  	  	  </b></th> 
					<th style='width:19px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Image   	 	      </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Name  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Type  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>User  	  		  </b></th> 
					<th style='width:30px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Publish  	  		  </b></th> 
					<th style='width:75px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Date uploaded  	  </b></th> 
				</tr>
				</thead>
				<tbody>";
				 
					$x = 0;			
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					foreach($items as $r) { 
					extract($r);
					$ptype = ($ptype=='') 	   ? 'Uncategorize' : $ptype;
					$publish = ($publish=='y') ? 'Yes' : 'No';
					$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 	
		echo 	"<tr>
				  <td $c>													$x      																		</td>
				  <td $c>													$itemCode      																	</td>
				  <td $c style='text-align:center;'>			    	<img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 	</td>
				  <td $c style='text-align:left;padding-left:5px;'>	<a href='".HTTP_PATH."gallery/itemInfo2/$itemID' target='_blank'>		$itemName</a>  </td>
				  <td $c style='text-align:left;padding-left:5px;'>	$ptype											</td>
				  <td $c style='text-align:left;padding-left:5px;'>	$full_name										</td>
				  <td $c style='text-align:center;'>	$publish										</td>
				  <td $c style='text-align:center;'>				". date("M d, Y", strtotime($dUploaded)) ."			</td>
				</tr>";}
		echo	"</tbody>";
					if(!$items)
						echo "<tr><td colspan='7'>No match found.</td></tr>";
		echo	"</table>";
			
		}
		elseif($view=='default')
		{
			$sql = "SELECT items.id as itemID, itemName, (SELECT typeName FROM POSM_Type where POSM_Type.id=items.POSMTypeID) as ptype, itemCode, 
						(SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, items.dateAdded as dUploaded, full_name, publish 
						 FROM items LEFT JOIN admin_users ON admin_users.id = items.user_id
						 WHERE items.countryID = $countryID AND YEAR(items.dateAdded) = $year ORDER BY items.id DESC";
			$sql 	 = $this->db->query($sql);
			$items	 = $sql->result_array();
			//print_r($items);
			
		echo "<table id='large' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:12px;' class='iLike_Result_Table tablesorter'>
				<thead>
				<tr>
					<th style='width:10px;text-align:center;color:white;padding:0px' bgcolor='#bb1d1d'>   					   <b>No 		 		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Item Code  	  	  </b></th> 
					<th style='width:19px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Image   	 	      </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Name  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Type  	  		  </b></th> 
					<th style='width:30px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Publish  	  		  </b></th> 
					<th style='width:75px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Date uploaded  	  </b></th> 
				</tr>
				</thead>
				<tbody>";
				 
					$x = 0;			
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					foreach($items as $r) { 
					extract($r);
					$ptype = ($ptype=='') 	   ? 'Uncategorize' : $ptype;
					$publish = ($publish=='y') ? 'Yes' : 'No';
					$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 	
		echo 	"<tr>
				  <td $c>													$x      																		</td>
				  <td $c>													$itemCode      																	</td>
				  <td $c style='text-align:center;'>			    <img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 	</td>
				  <td $c style='text-align:left;padding-left:5px;'>	<a href='".HTTP_PATH."gallery/itemInfo2/$itemID' target='_blank'>		$itemName</a>  </td>
				  <td $c style='text-align:left;padding-left:5px;'>	$ptype											</td>
				  <td $c style='text-align:center;'>	$publish										</td>
				  <td $c style='text-align:center;'>				". date("M d, Y", strtotime($dUploaded)) ."			</td>
				</tr>";}
		echo	"</tbody>";
					if(!$items)
						echo "<tr><td colspan='7'>No match found.</td></tr>";
		echo	"</table>";
			
		}
		elseif($view=='BU_activeness_per_Month')
		{
			$sql = "SELECT items.id as itemID, itemName, (SELECT typeName FROM POSM_Type where POSM_Type.id=items.POSMTypeID) as ptype, itemCode, 
						(SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, items.dateAdded as dUploaded, full_name, publish 
						 FROM items LEFT JOIN admin_users ON admin_users.id = items.user_id
						 WHERE items.countryID = $countryID AND MONTH(items.dateAdded) = $month AND YEAR(items.dateAdded) = $year ORDER BY items.id DESC";
			$sql 	 = $this->db->query($sql);
			$items	 = $sql->result_array();
			//print_r($items);
			
		echo "<table id='large' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:12px;' class='iLike_Result_Table tablesorter'>
				<thead>
				<tr>
					<th style='width:10px;text-align:center;color:white;padding:0px' bgcolor='#bb1d1d'>   					   <b>No 		 		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Item Code  	  	  </b></th> 
					<th style='width:19px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Image   	 	      </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Name  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Type  	  		  </b></th> 
					<th style='width:30px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Publish  	  		  </b></th> 
					<th style='width:75px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Date uploaded  	  </b></th> 
				</tr>
				</thead>
				<tbody>";
				 
					$x = 0;			
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					foreach($items as $r) { 
					extract($r);
					$ptype = ($ptype=='') 	   ? 'Uncategorize' : $ptype;
					$publish = ($publish=='y') ? 'Yes' : 'No';
					$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 	
		echo 	"<tr>
				  <td $c>													$x      																		</td>
				  <td $c>													$itemCode      																	</td>
				  <td $c style='text-align:center;'>			    <img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 	</td>
				  <td $c style='text-align:left;padding-left:5px;'>	<a href='".HTTP_PATH."gallery/itemInfo2/$itemID' target='_blank'>		$itemName</a>  </td>
				  <td $c style='text-align:left;padding-left:5px;'>	$ptype											</td>
				  <td $c style='text-align:center;'>	$publish										</td>
				  <td $c style='text-align:center;'>				". date("M d, Y", strtotime($dUploaded)) ."			</td>
				</tr>";}
		echo	"</tbody>";
					if(!$items)
						echo "<tr><td colspan='7'>No match found.</td></tr>";
		echo	"</table>";
			
		}
		elseif($view=='BU_activeness_Users')
		{
			$sql = "SELECT items.id as itemID, itemName, (SELECT typeName FROM POSM_Type where POSM_Type.id=items.POSMTypeID) as ptype, itemCode,
						(SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, items.dateAdded as dUploaded, full_name, publish 
						 FROM items LEFT JOIN admin_users ON admin_users.id = items.user_id
						 WHERE items.countryID = $countryID AND items.user_id = $user_id
						 ORDER BY items.id DESC";
			$sql 	 = $this->db->query($sql);
			$items	 = $sql->result_array();
			//print_r($items);
			
		echo "<table id='large' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:12px;' class='iLike_Result_Table tablesorter'>
				<thead>
				<tr>
					<th style='width:10px;text-align:center;color:white;padding:0px' bgcolor='#bb1d1d'>   					   <b>No 		 		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Item Code  	  	  </b></th> 
					<th style='width:19px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Image   	 	      </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Name  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Type  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>User  	  		  </b></th> 
					<th style='width:30px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Publish  	  		  </b></th> 
					<th style='width:75px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Date uploaded  	  </b></th> 
				</tr>
				</thead>
				<tbody>";
				 
					$x = 0;			
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					foreach($items as $r) { 
					extract($r);
					$ptype = ($ptype=='') 	   ? 'Uncategorize' : $ptype;
					$publish = ($publish=='y') ? 'Yes' : 'No';
					$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 	
		echo 	"<tr>
				  <td $c>													$x      																		</td>
				  <td $c>													$itemCode      																	</td>
				  <td $c style='text-align:center;'>			    	<img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 	</td>
				  <td $c style='text-align:left;padding-left:5px;'>	<a href='".HTTP_PATH."gallery/itemInfo2/$itemID' target='_blank'>		$itemName</a>  </td>
				  <td $c style='text-align:left;padding-left:5px;'>	$ptype											</td>
				  <td $c style='text-align:left;padding-left:5px;'>	$full_name										</td>
				  <td $c style='text-align:center;'>	$publish										</td>
				  <td $c style='text-align:center;'>				". date("M d, Y", strtotime($dUploaded)) ."			</td>
				</tr>";}
		echo	"</tbody>";
					if(!$items)
						echo "<tr><td colspan='7'>No match found.</td></tr>";
		echo	"</table>";
			
		}
		elseif($view=='BU_activeness_Users_per_Year')
		{
			$sql = "SELECT items.id as itemID, itemName, (SELECT typeName FROM POSM_Type where POSM_Type.id=items.POSMTypeID) as ptype, itemCode, 
						(SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, items.dateAdded as dUploaded, full_name, publish 
						 FROM items LEFT JOIN admin_users ON admin_users.id = items.user_id
						 WHERE items.user_id = $user_id AND YEAR(items.dateAdded) = $year ORDER BY items.id DESC";
			$sql 	 = $this->db->query($sql);
			$items	 = $sql->result_array();
			//print_r($items);
			
		echo "<table id='large' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:12px;' class='iLike_Result_Table tablesorter'>
				<thead>
				<tr>
					<th style='width:10px;text-align:center;color:white;padding:0px' bgcolor='#bb1d1d'>   					   <b>No 		 		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Item Code  	  	  </b></th> 
					<th style='width:19px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Image   	 	      </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Name  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Type  	  		  </b></th> 
					<th style='width:30px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Publish  	  		  </b></th> 
					<th style='width:75px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Date uploaded  	  </b></th> 
				</tr>
				</thead>
				<tbody>";
				 
					$x = 0;			
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					foreach($items as $r) { 
					extract($r);
					$ptype = ($ptype=='') 	   ? 'Uncategorize' : $ptype;
					$publish = ($publish=='y') ? 'Yes' : 'No';
					$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 	
		echo 	"<tr>
				  <td $c>													$x      																		</td>
				  <td $c>													$itemCode      																	</td>
				  <td $c style='text-align:center;'>			    <img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 	</td>
				  <td $c style='text-align:left;padding-left:5px;'>	<a href='".HTTP_PATH."gallery/itemInfo2/$itemID' target='_blank'>		$itemName</a>  </td>
				  <td $c style='text-align:left;padding-left:5px;'>	$ptype											</td>
				  <td $c style='text-align:center;'>	$publish										</td>
				  <td $c style='text-align:center;'>				". date("M d, Y", strtotime($dUploaded)) ."			</td>
				</tr>";}
		echo	"</tbody>";
					if(!$items)
						echo "<tr><td colspan='7'>No match found.</td></tr>";
		echo	"</table>";
			
		}
		elseif($view=='groupByUser')
		{
			$sql = "SELECT items.id as itemID, itemName, (SELECT typeName FROM POSM_Type where POSM_Type.id=items.POSMTypeID) as ptype, itemCode,
						(SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, items.dateAdded as dUploaded, full_name, publish 
						 FROM items LEFT JOIN admin_users ON admin_users.id = items.user_id
						 WHERE items.countryID = $countryID AND items.user_id = $user_id
						 ORDER BY items.id DESC";
			$sql 	 = $this->db->query($sql);
			$items	 = $sql->result_array();
			//print_r($items);
			
		echo "<table id='large' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:12px;' class='iLike_Result_Table tablesorter'>
				<thead>
				<tr>
					<th style='width:10px;text-align:center;color:white;padding:0px' bgcolor='#bb1d1d'>   					   <b>No 		 		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Item Code  	  	  </b></th> 
					<th style='width:19px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Image   	 	      </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Name  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Type  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>User  	  		  </b></th> 
					<th style='width:30px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Publish  	  		  </b></th> 
					<th style='width:75px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Date uploaded  	  </b></th> 
				</tr>
				</thead>
				<tbody>";
				 
					$x = 0;			
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					foreach($items as $r) { 
					extract($r);
					$ptype = ($ptype=='') 	   ? 'Uncategorize' : $ptype;
					$publish = ($publish=='y') ? 'Yes' : 'No';
					$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 	
		echo 	"<tr>
				  <td $c>													$x      																		</td>
				  <td $c>													$itemCode      																	</td>
				  <td $c style='text-align:center;'>			    	<img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 	</td>
				  <td $c style='text-align:left;padding-left:5px;'>	<a href='".HTTP_PATH."gallery/itemInfo2/$itemID' target='_blank'>		$itemName</a>  </td>
				  <td $c style='text-align:left;padding-left:5px;'>	$ptype											</td>
				  <td $c style='text-align:left;padding-left:5px;'>	$full_name										</td>
				  <td $c style='text-align:center;'>	$publish										</td>
				  <td $c style='text-align:center;'>				". date("M d, Y", strtotime($dUploaded)) ."			</td>
				</tr>";}
		echo	"</tbody>";
					if(!$items)
						echo "<tr><td colspan='7'>No match found.</td></tr>";
		echo	"</table>";
			
		}
		
	}
	
	function hits($view='',$itemID)
	{
		if($view=='item_database')
		{
			$sql = "SELECT countryName, full_name, department_name, date_time FROM item_views 
					LEFT JOIN admin_users  ON admin_users.id  = item_views.user_id 
					LEFT JOIN country	   ON country.id      = admin_users.countryID
					LEFT JOIN departments  ON departments.id  = admin_users.department_id
					WHERE item_views.itemID = $itemID ORDER BY date_time DESC";
		}if($view=='eCatalogue')
		{
			$sql = "SELECT countryName, full_name, department_name, date_time FROM ecitem_views 
					LEFT JOIN admin_users  ON admin_users.id  = ecitem_views.user_id 
					LEFT JOIN country	   ON country.id      = admin_users.countryID
					LEFT JOIN departments  ON departments.id  = admin_users.department_id
					WHERE ecitem_views.itemID = $itemID ORDER BY date_time DESC";
		}
		if($view=='my_gallery')
		{
			$sql = "SELECT countryName, full_name, department_name, date_time FROM myGallery_views 
					LEFT JOIN admin_users  ON admin_users.id  = myGallery_views.user_id 
					LEFT JOIN country	   ON country.id      = admin_users.countryID
					LEFT JOIN departments  ON departments.id  = admin_users.department_id
					WHERE myGallery_views.itemID = $itemID ORDER BY date_time DESC";
		}
		if($view=='common_gallery')
		{
			$sql = "SELECT countryName, full_name, department_name, date_time FROM commonGallery_views 
					LEFT JOIN admin_users  ON admin_users.id  = commonGallery_views.user_id 
					LEFT JOIN country	   ON country.id      = admin_users.countryID
					LEFT JOIN departments  ON departments.id  = admin_users.department_id
					WHERE commonGallery_views.itemID = $itemID ORDER BY date_time DESC";
		}
		
		$sql = $this->db->query($sql);
		$rows = $sql->result_array();
		
		echo "<table id='large' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:12px;' class='iLike_Result_Table tablesorter'>
			<thead>
			<tr>
				<th style='width:10px;text-align:center;color:white;padding:0px' bgcolor='#bb1d1d'>   					   <b>No 		 		  </b></th> 
				<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Country  	  	      </b></th> 
				<th style='width:19px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Full Name   	 	  </b></th> 
				<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Department  	  	  </b></th> 
				<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Date Time  	  	  </b></th> 
			</tr>
			</thead>
			<tbody>";
			 
				$x = 0;			
				$y=1;
				$z=1;
				$total=0;
				$total_target=0;
				foreach($rows as $r) { 
				extract($r);
				$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 	
		echo 	"<tr>
				  <td $c>											    $x      	 </td>
				  <td $c style='text-align:left;padding-left:5px;'>	$countryName     </td>
				  <td $c style='text-align:left;padding-left:5px;'>	$full_name 		 </td>
				  <td $c style='text-align:left;padding-left:5px;'>	$department_name </td>
				  <td $c style='text-align:left;padding-left:5px;'>	$date_time		 </td>
				</tr>";}
		echo	"</tbody>";
		echo	"</table>";
		
	}
	
	function generateCSVFileTemp($view='',$sql='',$fileName='')
	{
		$query = $this->db->query($sql);
		return $new_report = $this->csv_from_result($query,",","\n");
	}
	
	function iLike_ReportCSV($cID='')
	{
	//CAMPAIGN TITLE
	$csv  = "iLike Camapign\n";
	$sql  = "select  campaignType as Campaign_Type, campaignName as Campaign_Name, DateFrom as Date_From, DateTo as Date_To, uname as Created_By
			from campaign as c inner join admin_users as u on c.adminCreatorID=u.id where c.id='$cID'   ";
	$csv .= $this->generateCSVFileTemp("",$sql);
	
	//WINNING ITEMS
	$csv .= "\nWinning Items\n";
	$csv .= "No, Item Type, Price Category, Item Code, Item Name, Total Vote\n";
	$sql= $this->db->query("SELECT POSM_Type.typeName as Item_Type, extra_label as Price_Category, itemCode as Item_Code, itemName as Item_Name,
						   (SELECT totvote FROM iLikeResultRef WHERE itemID = items.id AND campaignID = $cID) AS Total_Vote
						   FROM items
						   LEFT JOIN POSM_Type ON items.POSMTypeID = POSM_Type.id
						   LEFT JOIN price_range ON price_range.id = items.price_rangeID
						   WHERE  items.id IN (SELECT itemID FROM iLikeResultRef WHERE campaignID = $cID) ORDER BY Item_Type ASC, price_range.xOrder ASC, Total_Vote DESC");
	$sql = $sql->result_array(); $x=0;
	foreach($sql as $s)
	{ extract($s); $x++;
	  $csv .= "$x, $Item_Type, $Price_Category, $Item_Code, $Item_Name, $Total_Vote\n";
	}

	
	//VOTERS
	$sql  = $this->db->query("SELECT fname as First_Name, lname as Last_Name, gender as Gender, department as Department, email as Email_Address, year_of_birth as Year_of_Birth FROM voters WHERE campaignID = $cID");
	$csv .="\n\nVoters\n";
	$csv .= "No, First Name, Last Name, Gender, Department, Email Address, Year of Birth, Age\n";
	$sql = $sql->result_array(); $x=0; $age=0;
	foreach($sql as $s)
	{ extract($s); $x++;
	  $age = date('Y')-$Year_of_Birth;
	  $csv .= "$x, $First_Name, $Last_Name, $Gender, $Department, $Email_Address, $Year_of_Birth, $age\n";
	}
	
	//CAMPAIGN ITEMS		   
	$sql  = $this->db->query("SELECT IFNULL((select typeName from POSM_Type as pt where pt.id=i.POSMTypeID),'-') as Item_Type, extra_label as Price_Category, 
							IFNULL(itm.itemCode,'-') as Item_Code, IFNULL(itm.itemName,'Sorry this is has been purged') as Item_Name, 
							 (SELECT COUNT( id ) 
							 FROM votexRef AS vref WHERE vref.itemID = itemREF.itemID AND vote =  'yes' and vref.campaignID =$cID 
							 AND vref.voterID IN (SELECT voters.id FROM voters WHERE voters.id = vref.voterID AND votingStatus = 'done')) AS Total_Vote
							 FROM  `campaignItemsXref` AS itemREF 
							 LEFT JOIN items AS i ON itemREF.itemID = i.id  
							 left join items as itm on itemREF.itemID=itm.id 
							 LEFT JOIN price_range ON price_range.id = itm.price_rangeID
							 where   itemREF.campaignID=$cID 
							 ORDER BY Item_Type ASC, price_range.xOrder ASC, `Total_Vote` DESC");
	$csv .="\nAll Items\n";
	$csv .= "No, Item_Type, Price_Category, Item_Code, Item_Name, Total_Vote\n";
	$sql = $sql->result_array(); $x=0;
	foreach($sql as $s)
	{ extract($s); $x++;
	  $csv .= "$x, $Item_Type, $Price_Category, $Item_Code, $Item_Name, $Total_Vote\n";
	}
	
	//CAMPAIGN RULES
	$csv .="\nCampaign Description\n";
	$sql  = "SELECT fieldValue as Campaign_Items FROM iLike_Rules_Ref WHERE campaignID = $cID"; 
	$csv .= $this->generateCSVFileTemp("",$sql);
	
	$sql  = "SELECT num_commitee as Minimum_Number_of_Nomination_Committee FROM iLike_Rules_No_Committes_Ref WHERE campaignID = $cID"; 
	$csv .="\n";
	$csv .= $this->generateCSVFileTemp("",$sql);
	
	$csv .= "\nVoting Rules";
	$csv .="\nNo, Rules, Current Items\n";
	$sql = "SELECT *, iLikeVotingRulesRef.price_rangeID as pRangeID FROM iLikeVotingRulesRef WHERE campaignID = $cID 
			ORDER BY price_rangeID ASC, fieldID DESC, rel DESC, val ASC";
	$sql 			   = $this->db->query($sql);
	$iLikeVotingRules = $sql->result_array();
	$campaignID = $cID;
	foreach($iLikeVotingRules as $iL)
	{
		extract($iL);
		$itemDB = "SELECT count(items.id) as tot_items
				   FROM items  
				   LEFT JOIN campaignItemsXref ON campaignItemsXref.itemID = items.id  
				   WHERE campaignItemsXref.campaignID = $cID
				   AND (items.$fieldName = $fieldID) AND items.price_rangeID =  $pRangeID";
		
		$orig_fieldName = $fieldName;
		switch($fieldName){
				case("POSMTypeID"):
					$tableName	= "Item Type";;
					$fieldName  = 'typeName';
					$table 		= 'POSM_Type';
				break;
		}
		
		$query 		= $this->db->query("SELECT $fieldName FROM $table WHERE id=$fieldID LIMIT 0,1");
		$row 		= $query->row();
		$name_Field = $row->$fieldName;
		
		//GET PRICE RANGE NAME
		$query 		= $this->db->query("SELECT extra_label FROM price_range WHERE id=$price_rangeID LIMIT 0,1");
		$row 		= $query->row();
		$extra_label = $row->extra_label;
		
		$s 		   		= $this->db->query($itemDB);
		$data['items']  = $s->result_array();		
		//print_r($data['items']);
		$status="Good";
		
		//TEST EVERY RULE
		$tot_items = $data['items'][0]['tot_items'];
		$min_vote = $min_val;
		$max_vote = $max_val;
		if($cond1=="==") $cond1="=";
		if($cond2=="==") $cond2="=";
		if(strpos($min_val,".")==TRUE)
			$min_vote = $min_val * 100;
		if(strpos($max_val,".")==TRUE)
			$max_vote = $max_val * 100;
		
		$min_number_of_items = $min_vote;
		
		if($logical_operator!="" AND $max_vote!="" AND $cond2!=""){
		$sql = $this->db->query("SELECT $tot_items >= $min_vote  as result LIMIT 0,1");			
		}else{
		$sql = $this->db->query("SELECT $tot_items >= $min_vote as result LIMIT 0,1");
		}
		$sql = $sql->row();
		if($sql->result==0) $status="Not Good";
		
		//PERCENTAGE
		if(strpos($min_val,".")==TRUE)
		 $min_val  = "$min_val% (". round($min_val * $data['items'][0]['tot_items']) .")";
		if(strpos($max_val,".")==TRUE)
		 $max_val  = "$max_val% (". round($max_val * $data['items'][0]['tot_items']) .")";	
		
		
		$rules[] = array(
						'cond1'				=>$cond1,
						'min_val'			=>$min_val,
						'logical_operator'	=>$logical_operator,
						'cond2'				=>$cond2,
						'max_val'			=>$max_val,
						'price_rangeName'	=>$extra_label,
						'table'				=>$table,
						'fieldName'			=>$orig_fieldName, 
						'fieldID'			=>$fieldID, 
						'fieldValue'		=>$name_Field, 
						'min_number_of_items'=>$min_number_of_items, 
						'stat'				=>$status,
						'current_num_items' =>$data['items'][0]['tot_items']
						);
	}
	$VotingRules = $rules;
	
	$x=0;
	$ctr = count($VotingRules);
	foreach($VotingRules as $i)
	{ extract($i);	 $x++; 
	 $csv .= "$x, $fieldValue ($price_rangeName):  $cond1 $min_val $logical_operator $cond2 $max_val , $current_num_items \n";
	}
	
	$csv .="\nCanvassing Rules";
	$csv .="\nNo, Rules, Current Items\n";
	$sql 				  = "SELECT *, iLikeCanvassingRulesXref.price_rangeID as pRangeID FROM iLikeCanvassingRulesXref WHERE campaignID = $cID 
							 ORDER BY price_rangeID ASC, fieldID DESC, rel DESC, val ASC";
	$sql 				  = $this->db->query($sql);
	$iLikeCanvassingRules = $sql->result_array();	
	foreach($iLikeCanvassingRules as $iL)
	{
		extract($iL);		
		$orig_fieldName = $fieldName;
		switch($fieldName){
				case("POSMTypeID"):
					$tableName	= "Item Type";
					$fieldName  = 'typeName';
					$table 		= 'POSM_Type';
				break;
		}
		
		//TOTAL ITEMS
		$itemDB = "SELECT items.id as iID
				   FROM items  
				   LEFT JOIN campaignItemsXref ON campaignItemsXref.itemID = items.id  
				   WHERE campaignItemsXref.campaignID = $campaignID
				   AND (items.POSMTypeID = $fieldID) AND items.price_rangeID =  $price_rangeID";
		$s 		   		= $this->db->query($itemDB);
		$data['items']  = $s->result_array();
	
		
		$query 		= $this->db->query("SELECT $fieldName FROM $table WHERE id=$fieldID LIMIT 0,1");
		$row 		= $query->row();
		$name_Field = $row->$fieldName;
		
		//GET PRICE RANGE NAME
		$query 		= $this->db->query("SELECT extra_label FROM price_range WHERE id=$price_rangeID LIMIT 0,1");
		$row 		= $query->row();
		$extra_label = $row->extra_label;
		
		$status="";
		$original_val =$val;
		if(strpos($min_val,".")==TRUE) $min_val = $min_val."% (".round(count($data['items'])*$min_val).")";
		if(strpos($max_val,".")==TRUE) $max_val = $max_val."% (".round(count($data['items'])*$max_val).")";
		
		$rules2[] = array(
						'cond1'				=>$cond1,
						'min_val'			=>$min_val,
						'logical_operator'	=>$logical_operator,
						'cond2'				=>$cond2,
						'max_val'			=>$max_val,
						'price_rangeName'	=>$extra_label,
						'table'				=>$table,
						'fieldName'			=>$orig_fieldName, 
						'fieldID'			=>$fieldID, 
						'fieldValue'		=>$name_Field, 
						'current_num_items'	=>count($data['items']), 
						'rel'				=>$rel,
						'val'				=>"$original_val",
						'lrel'				=>$lrel,
						'stat'				=>$status
						);
	}
	
	$x=0;
	$ctr = count($rules2);
	foreach($rules2 as $i)
	{ extract($i);	$x++;  
	   $csv .= "$x, $fieldValue ($price_rangeName):  $cond1 $min_val $logical_operator $cond2 $max_val , $current_num_items \n";
	}

	//print_r($csv);
	write_file(getcwd()."/files/csv/iLike_Report".$this->reportCode().".csv",$csv);
	}
	
	function iLike_Items($id)
    {
	 $data['EDIT'] 	=  $this->modules->crud_checker(18,'EDIT');
	 $campaignID = $id;
	 $data['cID']= $id;
	 
	 $this->iLike_ReportCSV($id); 
	 $data['csvFile'] = "iLike_Report".$this->reportCode().".csv";
	 
     $CI2 =& get_instance();
	 $CI2->load->library('fv');
	 $sqlSTr="SELECT items.id as iID, (SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, itemName, extra_label,
			(SELECT totvote FROM iLikeResultRef WHERE itemID = iID AND campaignID = $id) AS voteTot,
			POSM_Type.typeName as POSM_TypeName 
			FROM items
			LEFT JOIN POSM_Type ON items.POSMTypeID = POSM_Type.id 
			LEFT JOIN price_range ON price_range.id = items.price_rangeID
			WHERE  items.id IN (SELECT itemID FROM iLikeResultRef WHERE campaignID = $id) 
			ORDER BY POSM_TypeName ASC, price_range.id ASC, voteTot DESC";
	$voters = $this->db->query($sqlSTr);
	$data['topItems'] = $voters->result_array();
	
	$sql 			   = $this->db->query("SELECT POSM_Type.id as pID, typeName FROM POSM_Type ORDER BY typeName ASC");
	$data['POSM_Type'] = $sql->result_array();
	 
	 $sql = "SELECT itemID, IFNULL((itm.itemName),'Sorry this is has been purged') as itemName , IFNULL(itm.itemCode,'-') as iCode, campaignID,
			   (SELECT COUNT( id ) FROM votexRef AS vref WHERE vref.itemID = itemREF.itemID AND vote =  'yes' and vref.campaignID =$id 
			   AND vref.voterID IN (SELECT voters.id FROM voters WHERE voters.id = vref.voterID)) AS voteTot,
			   IFNULL((select typeName from POSM_Type as pt where pt.id=i.POSMTypeID),'-') as ptype, 
			   IFNULL((SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = i.id),'blank.png') as item_image, extra_label, itm.USD_Price as uPrice,  countryName, itm.dateReleased as dReleased
			   FROM  `campaignItemsXref` AS itemREF 
			   LEFT JOIN items AS i ON itemREF.itemID = i.id  
			   left join items as itm on itemREF.itemID=itm.id 
			   LEFT JOIN price_range ON price_range.id = itm.price_rangeID
			   LEFT JOIN country ON country.id =  itm.countryID
			   where   itemREF.campaignID=$id 
			   ORDER BY ptype ASC, price_range.id ASC, voteTot DESC";
	 
	 $report    = $this->db->query($sql);  
	 $rep       = $report->result_array(); 
	 
	 
	 $sql       = "select *,full_name from campaign as c inner join admin_users as u on c.adminCreatorID=u.id where c.id='$id'   ";
	 $header    = $this->db->query($sql);  
	 $header    = $header->result_array(); 
	 
	 $sql   		  = "SELECT * FROM voters WHERE campaignID = $id"; 
	 $voters    	  = $this->db->query($sql);  
	 $data['voters']  = $voters->result_array();
	 
	 //CAMPAIGN REF
	 $sql   		  = "SELECT * FROM iLike_Rules_Ref WHERE campaignID = $id"; 
	 $voters    	  = $this->db->query($sql);  
	 $data['iLike_Rules_Ref']  = $voters->result_array();
	 
	 $sql   		  = "SELECT * FROM iLike_Rules_No_Committes_Ref WHERE campaignID = $id"; 
	 $voters    	  = $this->db->query($sql);  
	 $data['iLike_Rules_No_Committes_Ref']  = $voters->result_array();
	 
	 $sql   		  = "SELECT * FROM iLikeCanvassingRulesXref WHERE campaignID = $id"; 
	 $voters    	  = $this->db->query($sql);  
	 $data['iLikeCanvassingRulesXref']  = $voters->result_array();
	 
	 $sql   		  = "SELECT * FROM iLikeVotingRulesRef WHERE campaignID = $id"; 
	 $voters    	  = $this->db->query($sql);  
	 $data['iLikeVotingRulesRef']  = $voters->result_array();
	 
	 $sql = "SELECT *, iLikeVotingRulesRef.price_rangeID as pRangeID FROM iLikeVotingRulesRef WHERE campaignID = $id 
			ORDER BY price_rangeID ASC, fieldID DESC, rel DESC, val ASC";
	$sql 			   = $this->db->query($sql);
	$iLikeVotingRules = $sql->result_array();
	$campaignID = $id;
	foreach($iLikeVotingRules as $iL)
	{
		extract($iL);
		$itemDB = "SELECT count(items.id) as tot_items
				   FROM items  
				   LEFT JOIN campaignItemsXref ON campaignItemsXref.itemID = items.id  
				   WHERE campaignItemsXref.campaignID = $campaignID
				   AND (items.$fieldName = $fieldID) AND items.price_rangeID =  $pRangeID";
		
		$orig_fieldName = $fieldName;
		switch($fieldName){
				case("POSMTypeID"):
					$tableName	= $CI2->fv->label(4);;
					$fieldName  = 'typeName';
					$table 		= 'POSM_Type';
				break;
				case("POSMStatusID"):
					$tableName	= 'ITEM STATUS';
					$fieldName  = 'statusName';
					$table 		= 'POSM_Status';
				break;
				case("OUTLETStatusID"):
					$tableName	= $CI2->fv->label(6); ;
					$fieldName  = 'statusName';
					$table 		= 'OUTLET_Status';
				break;
				case("PremiumTypeID"):
					$tableName	= $CI2->fv->label(7);;
					$fieldName  = 'premiumTypeName';
					$table 		= 'premiumItemType';
				break;
				case("MaterialTypeID"):
					$tableName	= $CI2->fv->label(9);;
					$fieldName  = 'materialName';
					$table 		= 'MATERIAL_Type';
				break;
				case("brandID"):
					$tableName	= $CI2->fv->label(3);;
					$fieldName  = 'brandName';
					$table 		= 'brands';
				break;
		}
		
		$query 		= $this->db->query("SELECT $fieldName FROM $table WHERE id=$fieldID LIMIT 0,1");
		$row 		= $query->row();
		$name_Field = $row->$fieldName;
		
		//GET PRICE RANGE NAME
		$query 		= $this->db->query("SELECT extra_label FROM price_range WHERE id=$price_rangeID LIMIT 0,1");
		$row 		= $query->row();
		$extra_label = $row->extra_label;
		
		$s 		   		= $this->db->query($itemDB);
		$data['items']  = $s->result_array();		
		//print_r($data['items']);
		$status="Good";
		
		//TEST EVERY RULE
		$tot_items = $data['items'][0]['tot_items'];
		$min_vote = $min_val;
		$max_vote = $max_val;
		if($cond1=="==") $cond1="=";
		if($cond2=="==") $cond2="=";
		if(strpos($min_val,".")==TRUE)
			$min_vote = round($min_val * $data['items'][0]['tot_items']);
		if(strpos($max_val,".")==TRUE)
			$max_vote = round($min_val * $data['items'][0]['tot_items']);
		
		$min_number_of_items = $min_vote;
		
		if($logical_operator!="" AND $max_vote!="" AND $cond2!=""){
		$sql = $this->db->query("SELECT $tot_items >= $min_vote  as result LIMIT 0,1");			
		}else{
		$sql = $this->db->query("SELECT $tot_items >= $min_vote as result LIMIT 0,1");
		}
		$sql = $sql->row();
		if($sql->result==0) $status="Not Good";
		
		//PERCENTAGE
		if(strpos($min_val,".")==TRUE)
		 $min_val  = "$min_val% (". round($min_val * $data['items'][0]['tot_items']) .")";
		if(strpos($max_val,".")==TRUE)
		 $max_val  = "$max_val% (". round($max_val * $data['items'][0]['tot_items']) .")";	
		
		
		$rules[] = array(
						'cond1'				=>$cond1,
						'min_val'			=>$min_val,
						'logical_operator'	=>$logical_operator,
						'cond2'				=>$cond2,
						'max_val'			=>$max_val,
						'price_rangeName'	=>$extra_label,
						'table'				=>$table,
						'fieldName'			=>$orig_fieldName, 
						'fieldID'			=>$fieldID, 
						'fieldValue'		=>$name_Field, 
						'min_number_of_items'=>$min_number_of_items, 
						'stat'				=>$status,
						'current_num_items' =>$data['items'][0]['tot_items']
						);
	}
	$data['VotingRules'] 		   = $rules;
	
	$sql 				  = "SELECT *, iLikeCanvassingRulesXref.price_rangeID as pRangeID FROM iLikeCanvassingRulesXref WHERE campaignID = $campaignID 
							 ORDER BY price_rangeID ASC, fieldID DESC, rel DESC, val ASC";
	$sql 				  = $this->db->query($sql);
	$iLikeCanvassingRules = $sql->result_array();	
	foreach($iLikeCanvassingRules as $iL)
	{
		extract($iL);		
		$orig_fieldName = $fieldName;
		switch($fieldName){
				case("POSMTypeID"):
					$tableName	= $CI2->fv->label(4);;
					$fieldName  = 'typeName';
					$table 		= 'POSM_Type';
				break;
				case("POSMStatusID"):
					$tableName	= 'ITEM STATUS';
					$fieldName  = 'statusName';
					$table 		= 'POSM_Status';
				break;
				case("OUTLETStatusID"):
					$tableName	= $CI2->fv->label(6); ;
					$fieldName  = 'statusName';
					$table 		= 'OUTLET_Status';
				break;
				case("PremiumTypeID"):
					$tableName	= $CI2->fv->label(7);;
					$fieldName  = 'premiumTypeName';
					$table 		= 'premiumItemType';
				break;
				case("MaterialTypeID"):
					$tableName	= $CI2->fv->label(9);;
					$fieldName  = 'materialName';
					$table 		= 'MATERIAL_Type';
				break;
				case("brandID"):
					$tableName	= $CI2->fv->label(3);;
					$fieldName  = 'brandName';
					$table 		= 'brands';
				break;
		}
		
		//TOTAL ITEMS
		$itemDB = "SELECT items.id as iID
				   FROM items  
				   LEFT JOIN campaignItemsXref ON campaignItemsXref.itemID = items.id  
				   WHERE campaignItemsXref.campaignID = $campaignID
				   AND (items.POSMTypeID = $fieldID) AND items.price_rangeID =  $price_rangeID";
		$s 		   		= $this->db->query($itemDB);
		$data['items']  = $s->result_array();
	
		
		$query 		= $this->db->query("SELECT $fieldName FROM $table WHERE id=$fieldID LIMIT 0,1");
		$row 		= $query->row();
		$name_Field = $row->$fieldName;
		
		//GET PRICE RANGE NAME
		$query 		= $this->db->query("SELECT extra_label FROM price_range WHERE id=$price_rangeID LIMIT 0,1");
		$row 		= $query->row();
		$extra_label = $row->extra_label;
		
		$status="";
		$original_val =$val;
		if(strpos($min_val,".")==TRUE) $min_val = $min_val."% (".round(count($data['items'])*$min_val).")";
		if(strpos($max_val,".")==TRUE) $max_val = $max_val."% (".round(count($data['items'])*$max_val).")";
		
		$rules2[] = array(
						'cond1'				=>$cond1,
						'min_val'			=>$min_val,
						'logical_operator'	=>$logical_operator,
						'cond2'				=>$cond2,
						'max_val'			=>$max_val,
						'price_rangeName'	=>$extra_label,
						'table'				=>$table,
						'fieldName'			=>$orig_fieldName, 
						'fieldID'			=>$fieldID, 
						'fieldValue'		=>$name_Field, 
						'rel'				=>$rel,
						'val'				=>"$original_val",
						'lrel'				=>$lrel,
						'stat'				=>$status
						);
	}
	$data['iLikeCanvassingRulesXref']  = $rules2;
	 
     $data['vfile']			= 'iLike_Items.php';
	 $data['title']			= 'iLike Report';
	 $data['rep']			= $rep;
	 $data['repHeader']		= $header;
	 //BREAD CRUMBS
	 $HTTP_PATH 					 = HTTP_PATH."report/iLike";
	 $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
	 $data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	 $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/iLike> iLike Report </a>';
	 $data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	 $data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/iLike_Items/$campaignID'>". $header[0]['campaignName'] ."</a>";
	if($this->modules->access_checker()==TRUE)
	{
		$this->load->view('innerPages',$data); 
	}else{
		$data['vfile']				= 'login.php';
		$data['title']				= 'SMBi System Log-in | SMBi';
		$data['page_title']			= 'SMBi System Log-in';
		$data['meta_description']	= 'San Miguel Brewing International';
		$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
		$this->load->view('login',$data); 	
	}

   }  
    
	function iWant($action='',$id='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(48,'REVIEW');
		$filter_WHERE 		= $this->modules->country();
		$filter_AND 		= $this->modules->country2();
		//$votingCampaignID 	= $id;
		
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(46);
		
		$table						= 'campaign';
		$data['vfile']				= 'iWant_report.php';
	    $data['title']				= 'iWant Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					 = HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/iWant> iWant Report </a>';
		
		//TOTAL NUMBER OF ROWS			
		$data['active_page']=1;
		$sql       = $this->db->query("SELECT id FROM campaign WHERE campaignType='iWant' and status='done'");
		$sql       = $sql->result_array();
		$total_rec = count($sql);

		$pagenum = 1;
		$data['page_rows'] = 10; 
		$data['last'] = ceil($total_rec/$data['page_rows']);		
		$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];

	
		extract($_POST);
		if($action=="page")
		{
			$this->modules->module_checker(27,'REVIEW');
			$pagenum = $id;
			$data['active_page'] = $id; 
			$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		}

		$filter_WHERE = $this->modules->iLike_Campaign_country();
     	$sql = $this->db->query("SELECT *,campaign.id AS iWantCampaignID FROM $table  
								LEFT JOIN admin_users 
								ON campaign.adminCreatorID = admin_users.id
								WHERE campaignType='iWant' AND status='done' ORDER BY iWantCampaignID  DESC $max");
								
		$data['campaigns'] = $sql->result_array();
		
		
		$viewer = 'innerPages';
	    if($this->modules->access_checker()==TRUE)
		{
			$this->load->view('innerPages',$data); 
		}else{
			$data['vfile']				= 'login.php';
			$data['title']				= 'SMBi System Log-in | SMBi';
			$data['page_title']			= 'SMBi System Log-in';
			$data['meta_description']	= 'San Miguel Brewing International';
			$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
			$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
			$this->load->view('login',$data); 	
		}
	}
	
	function iWant_ReportCSV($cID='')
	{
	//CAMPAIGN TITLE
	$csv  = "iWant Camapign\n";
	$sql  = "select  campaignType as Campaign_Type, campaignName as Campaign_Name, DateFrom as Date_From, DateTo as Date_To, uname as Created_By
			from campaign as c inner join admin_users as u on c.adminCreatorID=u.id where c.id='$cID'   ";
	$csv .= $this->generateCSVFileTemp("",$sql);
	
	//WINNING ITEMS
	$csv .= "\nWinning Items\n";
	$csv .= "No, Item Type, Price Category, Item Code, Item Name, Total Vote\n";
	$sql= $this->db->query("SELECT POSM_Type.typeName as Item_Type, extra_label as Price_Category, itemCode as Item_Code, itemName as Item_Name,
							(SELECT totvote FROM iWantResultRef WHERE itemID = items.id AND campaignID = $cID) AS Total_Vote
							FROM items
							LEFT JOIN POSM_Type ON items.POSMTypeID = POSM_Type.id
							LEFT JOIN price_range ON price_range.id = items.price_rangeID
							WHERE  items.id IN (SELECT itemID FROM iWantResultRef WHERE campaignID = $cID) ORDER BY Item_Type ASC, price_range.id ASC, Total_Vote DESC");
	$sql = $sql->result_array(); $x=0;
	foreach($sql as $s)
	{ extract($s); $x++;
	  $csv .= "$x, $Item_Type, $Price_Category, $Item_Code, $Item_Name, $Total_Vote\n";
	}
	
	//VOTERS
	$sql  = $this->db->query("SELECT fname as First_Name, lname as Last_Name, gender as Gender, department as Department, countryName as Country, email as Email_Address, year_of_birth as Year_of_Birth FROM voters 
							  LEFT JOIN country on country.id = voters.Fields001
							  WHERE campaignID = $cID order by Fields001 ASC");
	$csv .="\n\nVoters\n";
	$csv .= "No, First Name, Last Name, Gender, Department, Email Address, Year of Birth, Age, Country\n";
	$sql = $sql->result_array(); $x=0; $age=0;
	foreach($sql as $s)
	{ extract($s); $x++;
	  $age = date('Y')-$Year_of_Birth;
	  $csv .= "$x, $First_Name, $Last_Name, $Gender, $Department, $Email_Address, $Year_of_Birth, $age, $Country\n";
	}
	
	//CAMPAIGN ITEMS		   
	$sql  = $this->db->query("SELECT IFNULL((select typeName from POSM_Type as pt where pt.id=i.POSMTypeID),'-') as Item_Type, extra_label as Price_Category, IFNULL(itm.itemCode,'-') as Item_Code, IFNULL(itm.itemName,'Sorry this is has been purged') as Item_Name, 
							  (SELECT COUNT( id ) 
							  FROM votexRef AS vref WHERE vref.itemID = itemREF.itemID AND vote =  'yes' and vref.campaignID =$cID 
							  AND vref.voterID IN (SELECT voters.id FROM voters WHERE voters.id = vref.voterID )) AS Total_Vote
							  FROM  `campaignItemsXref` AS itemREF 
							  LEFT JOIN items AS i ON itemREF.itemID = i.id  
							  left join items as itm on itemREF.itemID=itm.id 
							  LEFT JOIN price_range ON price_range.id = itm.price_rangeID
							  where   itemREF.campaignID=$cID 
							  ORDER BY Item_Type ASC,  price_range.xOrder ASC, `Total_Vote` DESC");
	$csv .="\nAll Items\n";
	$csv .= "No, Item_Type, Price_Category, Item_Code, Item_Name, Total_Vote\n";
	$sql = $sql->result_array(); $x=0;
	foreach($sql as $s)
	{ extract($s); $x++;
	  $csv .= "$x, $Item_Type, $Price_Category, $Item_Code, $Item_Name, $Total_Vote\n";
	}
	
	
	$sql  = "SELECT num_commitee as Number_of_Screening_Committee_per_Country FROM iWantCampaignNumber_of_commitees_ref WHERE campaignID = $cID"; 
	$csv .="\n";
	$csv .= $this->generateCSVFileTemp("",$sql);
	
	$csv .= "\nVoting Rules";
	$csv .="\nNo, Rules, Current Items\n";
	$sql = "SELECT *, iWantVotingRulesRef.price_rangeID as pRangeID FROM iWantVotingRulesRef WHERE campaignID = $cID 
			ORDER BY price_rangeID ASC, fieldID DESC, rel DESC, val ASC";
	$sql 			   = $this->db->query($sql);
	$iWantVotingRules = $sql->result_array();
	$campaignID = $cID;
	foreach($iWantVotingRules as $iL)
	{
		extract($iL);
		$itemDB = "SELECT count(items.id) as tot_items
				   FROM items  
				   LEFT JOIN campaignItemsXref ON campaignItemsXref.itemID = items.id  
				   WHERE campaignItemsXref.campaignID = $cID
				   AND (items.$fieldName = $fieldID) AND items.price_rangeID =  $pRangeID";
		
		$orig_fieldName = $fieldName;
		switch($fieldName){
				case("POSMTypeID"):
					$tableName	= "Item Type";;
					$fieldName  = 'typeName';
					$table 		= 'POSM_Type';
				break;
		}
		
		$query 		= $this->db->query("SELECT $fieldName FROM $table WHERE id=$fieldID LIMIT 0,1");
		$row 		= $query->row();
		$name_Field = $row->$fieldName;
		
		//GET PRICE RANGE NAME
		$query 		= $this->db->query("SELECT extra_label FROM price_range WHERE id=$price_rangeID LIMIT 0,1");
		$row 		= $query->row();
		$extra_label = $row->extra_label;
		
		$s 		   		= $this->db->query($itemDB);
		$data['items']  = $s->result_array();		
		//print_r($data['items']);
		$status="Good";
		
		//TEST EVERY RULE
		$tot_items = $data['items'][0]['tot_items'];
		$min_vote = $min_val;
		$max_vote = $max_val;
		if($cond1=="==") $cond1="=";
		if($cond2=="==") $cond2="=";
		if(strpos($min_val,".")==TRUE)
			$min_vote = $min_val * 100;
		if(strpos($max_val,".")==TRUE)
			$max_vote = $max_val * 100;
		
		$min_number_of_items = $min_vote;
		
		if($logical_operator!="" AND $max_vote!="" AND $cond2!=""){
		$sql = $this->db->query("SELECT $tot_items >= $min_vote  as result LIMIT 0,1");			
		}else{
		$sql = $this->db->query("SELECT $tot_items >= $min_vote as result LIMIT 0,1");
		}
		$sql = $sql->row();
		if($sql->result==0) $status="Not Good";
		
		//PERCENTAGE
		if(strpos($min_val,".")==TRUE)
		 $min_val  = "$min_val% (". round($min_val * $data['items'][0]['tot_items']) .")";
		if(strpos($max_val,".")==TRUE)
		 $max_val  = "$max_val% (". round($max_val * $data['items'][0]['tot_items']) .")";	
		
		
		$rules[] = array(
						'cond1'				=>$cond1,
						'min_val'			=>$min_val,
						'logical_operator'	=>$logical_operator,
						'cond2'				=>$cond2,
						'max_val'			=>$max_val,
						'price_rangeName'	=>$extra_label,
						'table'				=>$table,
						'fieldName'			=>$orig_fieldName, 
						'fieldID'			=>$fieldID, 
						'fieldValue'		=>$name_Field, 
						'min_number_of_items'=>$min_number_of_items, 
						'stat'				=>$status,
						'current_num_items' =>$data['items'][0]['tot_items']
						);
	}
	$VotingRules = $rules;
	
	$x=0;
	$ctr = count($VotingRules);
	foreach($VotingRules as $i)
	{ extract($i);	  $x++; 
	 $csv .= "$x, $fieldValue ($price_rangeName):  $cond1 $min_val $logical_operator $cond2 $max_val , $current_num_items \n";
	}
	
	$csv .="\nCanvassing Rules";
	$csv .="\nNo, Rules, Current Items\n";
	$sql  = "SELECT *, iWantCanvassingRulesRef.price_rangeID as pRangeID FROM iWantCanvassingRulesRef WHERE campaignID = $cID 
			 ORDER BY price_rangeID ASC, fieldID DESC, rel DESC, val ASC";
	$sql 				  = $this->db->query($sql);
	$iLikeCanvassingRules = $sql->result_array();	
	foreach($iLikeCanvassingRules as $iL)
	{
		extract($iL);		
		$orig_fieldName = $fieldName;
		switch($fieldName){
				case("POSMTypeID"):
					$tableName	= "Item Type";
					$fieldName  = 'typeName';
					$table 		= 'POSM_Type';
				break;
		}
		
		//TOTAL ITEMS
		$itemDB = "SELECT items.id as iID
				   FROM items  
				   LEFT JOIN campaignItemsXref ON campaignItemsXref.itemID = items.id  
				   WHERE campaignItemsXref.campaignID = $campaignID
				   AND (items.POSMTypeID = $fieldID) AND items.price_rangeID =  $price_rangeID";
		$s 		   		= $this->db->query($itemDB);
		$data['items']  = $s->result_array();
	
		
		$query 		= $this->db->query("SELECT $fieldName FROM $table WHERE id=$fieldID LIMIT 0,1");
		$row 		= $query->row();
		$name_Field = $row->$fieldName;
		
		//GET PRICE RANGE NAME
		$query 		= $this->db->query("SELECT extra_label FROM price_range WHERE id=$price_rangeID LIMIT 0,1");
		$row 		= $query->row();
		$extra_label = $row->extra_label;
		
		$status="";
		$original_val =$val;
		if(strpos($min_val,".")==TRUE) $min_val = $min_val."% (".round(count($data['items'])*$min_val).")";
		if(strpos($max_val,".")==TRUE) $max_val = $max_val."% (".round(count($data['items'])*$max_val).")";
		
		$rules2[] = array(
						'cond1'				=>$cond1,
						'min_val'			=>$min_val,
						'logical_operator'	=>$logical_operator,
						'cond2'				=>$cond2,
						'max_val'			=>$max_val,
						'price_rangeName'	=>$extra_label,
						'table'				=>$table,
						'fieldName'			=>$orig_fieldName, 
						'fieldID'			=>$fieldID, 
						'fieldValue'		=>$name_Field, 
						'current_num_items'	=>count($data['items']), 
						'rel'				=>$rel,
						'val'				=>"$original_val",
						'lrel'				=>$lrel,
						'stat'				=>$status
						);
	}
	
	$x=0;
	$ctr = count($rules2);
	foreach($rules2 as $i)
	{ extract($i);	$x++;  
	   $csv .= "$x, $fieldValue ($price_rangeName):  $cond1 $min_val $logical_operator $cond2 $max_val , $current_num_items \n";
	}

	//print_r($csv);
	write_file(getcwd()."/files/csv/iWant_Report".$this->reportCode().".csv",$csv);
	}
	
	function per_country()
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		//$this->modules->module_checker(27,'REVIEW');
		$filter_WHERE 		= $this->modules->country();
		$filter_AND 		= $this->modules->country2();
		//$votingCampaignID 	= $id;
		
		$table						= 'campaign';
		$data['vfile']				= 'per_country.php';
	    $data['title']				= 'Per Country Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/iLike> iLike Report </a>';
		
		//TOTAL NUMBER OF ROWS			
		$data['active_page']=1;
		$sql       = $this->db->query("SELECT itemID FROM iLikeResultRef WHERE campaign = (SELECT id FROM campaign WHERE campaignType='iLike' and status='done' $filter_AND)");
		$sql       = $sql->result_array();
		$total_rec = count($sql);

		$pagenum = 1;
		$data['page_rows'] = 10; 
		$data['last'] = ceil($total_rec/$data['page_rows']);		
		$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];

	
		extract($_POST);
		if($action=="page")
		{
			$this->modules->module_checker(27,'REVIEW');
			$pagenum = $id;
			$data['active_page'] = $id; 
			$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		}

		$filter_WHERE = $this->modules->iLike_Campaign_country();
     	$sql = $this->db->query("SELECT *,campaign.id AS iLikeCampaignID FROM $table  
								LEFT JOIN admin_users 
								ON campaign.adminCreatorID = admin_users.id
								WHERE campaignType='iLike' AND status='done' $filter_WHERE ORDER BY iLikeCampaignID  DESC $max");
								
		$data['campaigns'] = $sql->result_array();
		
	    if($this->modules->access_checker()==TRUE)
		{
			$this->load->view('innerPages',$data); 
		}else{
			$data['vfile']				= 'login.php';
			$data['title']				= 'SMBi System Log-in | SMBi';
			$data['page_title']			= 'SMBi System Log-in';
			$data['meta_description']	= 'San Miguel Brewing International';
			$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
			$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
			$this->load->view('login',$data); 	
		}
	}
	
	function iLike_Voters($cID,$iID)
	{
		$sql = "SELECT voterID, fname, lname, gender, email, department, year_of_birth, tdate, ttime  FROM voters
				LEFT JOIN votexRef ON votexRef.voterID = voters.id 
				WHERE voters.campaignID = $cID AND votexRef.itemID=$iID AND votexRef.vote = 'yes' 
				";
		
		$sql = $this->db->query($sql);
		$voters = $sql->result_array();
		
		if($voters){
		echo "<table cellpadding='0' cellspacing='0' border=1 style='width:100%;margin: 0px auto;font-size:12px;' class='iLike_Result_Table'>
			<tr style='border-radius: 6px;'>
				<td style='width:10px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>No 		   </b></td> 
				<td style='width:100px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>   <b>First Name  </b></td> 
				<td style='width:100px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>   <b>Last  Name  </b></td> 
				<td style='width:30px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Gender  	   </b></td> 
				<td style='width:223px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>   <b>Email  	   </b></td> 
				<td style='width:50px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Department  </b></td> 
				<td style='width:50px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Year 	   </b></td> 
				<td style='width:50px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Age 	   </b></td> 
				<td style='width:90px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Date 	   </b></td> 
				<td style='width:30px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Time 	   </b></td> 
			</tr>";
		 
			$x = 0;
			$total = 0;
			foreach($voters as $v) { 
			extract($v);
			$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
		 
			echo	"<tr>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>	$x 				</td>
					  <td $c  style='text-align:left;padding: 0px 2px 0px 10px;'>	$fname 			</td>
					  <td $c  style='text-align:left;padding: 0px 2px 0px 10px;'>	$lname  		</td>
					  <td $c  style='text-align:left;padding: 0px 2px 0px 10px;'>	$gender  		</td>
					  <td $c  style='text-align:left;padding: 0px 2px 0px 10px;'>		$email  		</td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 10px;'>		$department  	</td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>		$year_of_birth  </td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>";
			echo 	  (date('Y')-$year_of_birth);		  
			echo      "</td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>		". $this->convertDate('date',"$tdate $ttime") ."  		</td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>		". $this->convertDate('time',"$tdate $ttime") ."  		</td>
					</tr>";
			}
		echo"</table>";
		}else{
			echo "<label style='margin-bottom: 1px;font-size: 11px;'>Sorry there is no vote for this item.</label>";
		}
		
		
	}
	
	function iWant_Voters($cID,$iID)
	{	
		$voters="";
		
		$sql = "SELECT voterID, fname, lname, gender, email, department, year_of_birth, tdate, ttime  FROM voters
				LEFT JOIN votexRef ON votexRef.voterID = voters.id 
				WHERE voters.campaignID = $cID AND votexRef.itemID=$iID AND votexRef.vote = 'yes' 
				";

		$sql = $this->db->query($sql);
		$voters = $sql->result_array();
		
		//print_r($voters);
		if($voters){
		echo "<table cellpadding='0' cellspacing='0' border=1 style='width:100%;margin: 0px auto;font-size:12px;' class='iLike_Result_Table'>
			<tr style='border-radius: 6px;'>
				<td style='width:10px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding:  2px 2px 2px 5px;'>    <b>No 		   	</b></td> 
				<td style='width:130px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>First Name  	</b></td> 
				<td style='width:130px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Last  Name  	</b></td> 
				<td style='width:80px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding:  2px 2px 2px 5px;'>    <b>Gender  	   	</b></td> 
				<td style='width:230px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Email  	   	</b></td> 
				<td style='width:50px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding:  2px 2px 2px 5px;'>    <b>Department  	</b></td> 
				<td style='width:50px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding:  2px 2px 2px 5px;'>    <b>Year 	   		</b></td> 
				<td style='width:50px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding:  2px 2px 2px 5px;'>    <b>Age 	   		</b></td> 
				<td style='width:90px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding:  2px 2px 2px 5px;'>    <b>Date 	   		</b></td> 
				<td style='width:30px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding:  2px 2px 2px 5px;'>    <b>Time 	   		</b></td> 
			</tr>";
		 
			$x = 0;
			$total = 0;
			foreach($voters as $v) { 
			extract($v);
			$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
		 
			echo	"<tr>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>		$x 				</td>
					  <td $c  style='text-align:left;padding: 0px 2px 0px 10px;'>		$fname 			</td>
					  <td $c  style='text-align:left;padding: 0px 2px 0px 10px;'>		$lname  		</td>
					  <td $c  style='text-align:left;padding: 0px 2px 0px 10px;'>		$gender  		</td>
					  <td $c  style='text-align:left;padding: 0px 2px 0px 10px;'>		$email  		</td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 10px;'>		$department  	</td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>		$year_of_birth  </td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>";
			echo 	  (date('Y')-$year_of_birth);		  
			echo      "</td>
					    <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>		". $this->convertDate('date',"$tdate $ttime") ."  		</td>
						<td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>		". $this->convertDate('time',"$tdate $ttime") ."  		</td>
					</tr>";
			}
		echo"</table>";
		}else{
			echo "<label style='margin-bottom: 1px;font-size: 11px;'>Sorry there is no vote for this item.</label>";
		}
		
	}
	
	function sortingRef_winningItems($id='',$returnType='')
	{
		$query	  ='';
		$Orig_code='';
		$Rev_code ='';
		switch($id)
		{
		 case '0-A':
		  $query 	 	 = "ptype ASC, price_rangeID ASC, totalVote DESC";
		  $Orig_code 	 = "0-A";
		  $Rev_code  	 = "0-D";
		  $label	 	 = "Vote ";
		  $label_symbol	 = "Vote &#x25B2;";
		  //$label
		 break;
		 case '0-D':
		  $query 	 	 = "ptype DESC, price_rangeID ASC, totalVote DESC"; 
		  $Orig_code 	 = "0-D";
		  $Rev_code  	 = "0-A";
		  $label	 	 = "Vote ";
		  $label_symbol	 = "Vote &#x25BC;";
		 break;
		 case '1-A':
		  $query 		= "itemCode ASC"; 
		  $Orig_code 	= "1-A";
		  $Rev_code  	= "1-D";
		  $label	 	= "Item Code ";
		  $label_symbol	= "Item Code &#x25B2;";
		 break;
		 case '1-D':
		  $query     	 = "itemCode DESC"; 
		  $Orig_code 	 = "1-D";
		  $Rev_code  	 = "1-A";
		  $label	 	 = "Item Code ";
		  $label_symbol	 = "Item Code &#x25BC;";
		 break;
		 case '2-A':
		  $query     	 = "itemName ASC"; 
		  $Orig_code 	 = "2-A";
		  $Rev_code  	 = "2-D";
		  $label	 	 = "Item Name ";
		  $label_symbol	 = "Item Name &#x25B2;";
		 break;
		 case '2-D':
		  $query     	 = "itemName DESC"; 
		  $Orig_code 	 = "2-D";
		  $Rev_code  	 = "2-A";
		  $label	 	 = "Item Name ";
		  $label_symbol	 = "Item Name &#x25BC;";
		 break;
		 case '3-A':
		  $query         = "ptype ASC";
	      $Orig_code 	 = "3-A";
		  $Rev_code  	 = "3-D";
		  $label	 	 = "Item Type ";
		  $label_symbol	 = "Item Type &#x25B2;";
		 break;
		 case '3-D':
		  $query     	= "ptype DESC"; 
		  $Orig_code 	= "3-D";
		  $Rev_code  	= "3-A";
		  $label	 	 = "Item Type ";
		  $label_symbol	 = "Item Type &#x25BC;";
		 break;
		 case '4-A':
		  $query 	 = "country_Name ASC"; 
		  $Orig_code = "4-A";
		  $Rev_code  = "4-D";
		  $label	 	 = "Country";
		  $label_symbol	 = "Country &#x25B2;";
		 break;
		 case '4-D':
		  $query 	 = " country_Name DESC";
		  $Orig_code = "4-D";
		  $Rev_code  = "4-A";
		  $label	 	 = "Country";
		  $label_symbol	 = "Country &#x25BC;";
		 break;
		 case '5-A':
		  $query 	 	 = "campaign_name ASC"; 
		  $Orig_code 	 = "5-A";
		  $Rev_code  	 = "5-D";
		  $label	 	 = "Campaign Name";
		  $label_symbol	 = "Campaign Name &#x25B2;";
		 break;
		 case '5-D':
		  $query 		 = "campaign_name DESC";
		  $Orig_code 	 = "5-D";
		  $Rev_code  	 = "5-A";
		  $label	 	 = "Campaign Name";
		  $label_symbol	 = "Campaign Name &#x25BC;";
		 break;
		 case '6-A':
		  $query = " campaign_Date ASC";
		  $Orig_code = "6-A";
		  $Rev_code  = "6-D";
		  $label	 	 = "Date";
		  $label_symbol	 = "Date &#x25B2;";
		 break;
		 case '6-D':
		  $query = " campaign_Date DESC"; 
		  $Orig_code = "6-D";
		  $Rev_code  = "6-A";
		  $label	 	 = "Date";
		  $label_symbol	 = "Date &#x25BC;";
		 break;
		  case '7-A':
		  $query = " extra_label ASC";
		  $Orig_code = "7-A";
		  $Rev_code  = "7-D";
		  $label	 	 = "Price Category";
		  $label_symbol	 = "Price Category &#x25B2;";
		 break;
		 case '7-D':
		  $query = " extra_label DESC"; 
		  $Orig_code = "7-D";
		  $Rev_code  = "7-A";
		  $label	 	 = "Price Category";
		  $label_symbol	 = "Price Category &#x25BC;";
		 break;
		}
		if($returnType=='query')
			return $query;
		elseif($returnType=='Orig_code')
			return $Orig_code;
		elseif($returnType=='Rev_code')
			return $Rev_code;
		elseif($returnType=='label')
			return $label;
		elseif($returnType=='label_symbol')
			return $label_symbol;
	}
	
	function campaign_items_summary($view='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(49,'REVIEW');
		
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(43);
		$data['PUBLISH_CAMPAIGN'] =  $this->modules->crud_checker(29,'PUBLISH CAMPAIGN');
		
		$filter="";
		$WHERE="";
		if($_SESSION['super_admin']!='y'  AND $_SESSION['countryID']!=0)
			$WHERE = "AND TotalVotes.countryID =".$_SESSION['countryID']." ";
		
		//$votingCampaignID 	= $id;
		
		$table						= '';
		$data['vfile']				= 'voters_summary.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/campaign_items_summary/$view'> Winning Items Summary </a>";
		
		//TOTAL NUMBER OF ROWS	
		$sort='n';
		extract($_POST);
		$cond="";
		$having="";
		$campaignType = $view;
		$limit =isset($selpage)? $selpage:0;
		
		if((isset($DateFrom) AND isset($DateTo)) AND ($DateFrom!='' AND $DateTo!='') AND !isset($Reset)){
			$WHERE   .= " AND campaign.DatePublished >= '$DateFrom' AND campaign.DatePublished <= '$DateTo' ";
			$data['DateFrom'] = $DateFrom;
			$data['DateTo']   = $DateTo;

		}else{
			$data['DateFrom'] = "";
			$data['DateTo']   = "";
		}
		
		if($view=="iLike")
			$table = "iLikeResultRef";
		else
			$table = "iWantResultRef";
			
		if(isset($Submit) OR isset($selpage))
		{	
			$val1 = mysql_real_escape_string($val1);
			$val1 = trim($val1);
			$val2 = mysql_real_escape_string($val2);
			$val2 = trim($val2);
			
			
			$condition = '';
			switch($cond1){
				case 'equal': 
					$condition = '=';
				break;
				case 'containing': 
					$condition = 'like';
				break;
				case 'greaterThan': 
					$condition = '>=';
				break;
				case 'lessThan': 
					$condition = '<=';
				break;
				case 'in': 
					$condition = 'in';
				break;
				case 'between': 
					$condition = 'between';
				break;
			}
			
			//itemcode, itemName, countryName
			if(($opt1=='itemCode' OR $opt1=='itemName' OR $opt1=='country_Name' OR $opt1=='ptype' OR $opt1=='campaign_Name' OR $opt1=='extra_label') AND $val1!='')
			{
				if($condition=='=' OR $condition=='>=' OR $condition=='<=')
					$cond = "  $opt1 $condition '$val1'";
				if($condition=='like')
					$cond = " $opt1  $condition '%$val1%'";
				if($condition=='in')
					$cond = "  $opt1 $condition ('" . str_replace(",", "','", $val1) . "')";
				if($condition=='between' & $this->checkStr($val1)==TRUE)
					$cond = "  $opt1 $condition ".stripslashes($val1);
			}
		
			//vote
			if(($opt1=='totalVote') AND $val1!='' AND $condition!='like'){
				if($condition=='in' AND (is_numeric($val1) OR strpos($val1,',')==TRUE))
					$cond = "  $opt1 $condition ('" . str_replace(",", "','", $val1) . "')";
				elseif($condition!='in' AND is_numeric($val1))
					$cond = "  $opt1 $condition $val1";
				if($condition=='between' & $this->checkStr($val1)==TRUE)
					$cond = "  $opt1 $condition ".stripslashes($val1);
			}

			
			/*2ND SET*/
			switch($cond2){
				case 'equal': 
					$condition2 = '=';
				break;
				case 'containing': 
					$condition2 = 'like';
				break;
				case 'greaterThan': 
					$condition2 = '>=';
				break;
				case 'lessThan': 
					$condition2 = '<=';
				break;
				case 'in': 
					$condition2 = 'in';
				break;
				case 'between': 
					$condition2 = 'between';
				break;
			}
			
			//itemcode, itemName, countryName
			if(($opt2=='itemCode' OR $opt2=='itemName' OR $opt2=='country_Name' OR $opt2=='ptype' OR $opt2=='campaign_Name' OR $opt2=='extra_label') AND $val2!='')
			{
				if($condition2=='=' OR $condition2=='>=' OR $condition2=='<=')
					$cond .= " $operator $opt2  $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator $opt2  $condition2 '%$val2%'";
				if($condition2=='in')
					$cond .= " $operator $opt2  $condition2 ('" . str_replace(",", "','", $val2) . "')";
				if($condition2=='between' & $this->checkStr($val2)==TRUE)
					$cond .= "  $operator $opt2 $condition ".stripslashes($val2);
			}
			
			//vote
			if(($opt2=='totalVote') AND $val2!='' AND $condition2!='like'){
				if($condition2=='in' AND (is_numeric($val2) OR strpos($val2,',')==TRUE))
					$cond .= " $operator $opt2 $condition2 ('" . str_replace(",", "','", $val2) . "')";
				elseif($condition2!='in' AND is_numeric($val2))
					$cond .= " $operator $opt2 $condition2 $val2";
				elseif($condition2=='between' & $this->checkStr($val2)==TRUE)
					$cond .= " $operator $opt2 $condition2 ".stripslashes($val2);
			}
	
			$cond = ($cond=="") ? "" : " AND ($cond) ";
			$WHERE  .=  " $cond ";
		}
		
		//IF THEIR NO POSSIBLE RESULT
		$valid_query=TRUE;
		if(isset($Submit)){
			if($cond=="" AND ($val1!='' OR $val2!=''))
				$valid_query=FALSE;
		}
		
		
		$data['POST'] = $_POST;
		if(isset($Reset)){
			$data['POST'] = array();
			if($_SESSION['super_admin']!='y'  AND $_SESSION['countryID']!=0){
				$WHERE = " AND TotalVotes.countryID =".$_SESSION['countryID']." ";
			}else{
			   $WHERE = "";
			}
		}
		
		//ORDER
		$ORDER = $this->sortingRef_winningItems('0-A','query');
		$data['order'] = $this->sortingRef_winningItems('0-A','Orig_code');
		$order_code = '0-A';
		$label = "Vote";
		if(isset($order)){
			$ORDER = $this->sortingRef_winningItems($order,'query');
			$data['order'] = $this->sortingRef_winningItems($order,'Orig_code');
			$order_code = $order;
			$label = $this->sortingRef_winningItems($order,'label');
		}
		
		if($view=='iLike'){
		$data['ViewType'] = "iLike";
		$data['ViewLabel'] = "WINNING ITEMS FROM ILIKE";
		
		if($valid_query==TRUE){
		 $sql = "SELECT TotalVotes.itemID AS iID, itemCode, ptype, itemName, extra_label,
				totVote, 
				(SELECT image FROM items_images WHERE defaultStatus = 1 AND items_images.itemID = TotalVotes.itemID LIMIT 0,1) as item_image, 
				country_Name, campaign_Name, campaign_Date
				FROM TotalVotes 
				LEFT JOIN iLikeResultRef ON iLikeResultRef.itemID = TotalVotes.itemID  
				LEFT JOIN campaign       ON campaign.id 		  = TotalVotes.campaignID
				LEFT JOIN price_range	 ON price_range.id		  = TotalVotes.price_rangeID
				WHERE TotalVotes.itemID IN
				(SELECT DISTINCT(iLikeResultRef.itemID) FROM iLikeResultRef WHERE totvote!=0)  
				$WHERE AND campaign_Type = 'iLike'
				ORDER BY $ORDER";
		  $sql_csv = $this->db->query("SELECT totVote as Vote, itemCode as Item_Code, ptype as Item_Type, extra_label as Price_Category, itemName as Item_Name, 
				country_Name as Country, campaign_Name as Campaign_Name, campaign_Date as Date
				FROM TotalVotes 
				LEFT JOIN iLikeResultRef ON iLikeResultRef.itemID = TotalVotes.itemID  
				LEFT JOIN campaign       ON campaign.id 		  = TotalVotes.campaignID  
				LEFT JOIN price_range	 ON price_range.id		  = TotalVotes.price_rangeID
				WHERE TotalVotes.itemID IN
				(SELECT DISTINCT(iLikeResultRef.itemID) FROM iLikeResultRef WHERE totvote!=0)  
				$WHERE AND campaign_Type = 'iLike'
				ORDER BY $ORDER");
		 }else{
		 $sql = "SELECT TotalVotes.itemID AS iID, itemCode, ptype, itemName, extra_label,
				totVote, 
				(SELECT image FROM items_images WHERE defaultStatus = 1 AND items_images.itemID = TotalVotes.itemID LIMIT 0,1) as item_image, 
				country_Name, campaign_Name, campaign_Date
				FROM TotalVotes 
				LEFT JOIN iLikeResultRef ON iLikeResultRef.itemID = TotalVotes.itemID  
				LEFT JOIN campaign       ON campaign.id 		  = TotalVotes.campaignID 
				LEFT JOIN price_range	 ON price_range.id		  = TotalVotes.price_rangeID
				WHERE TotalVotes.itemID IN
				(SELECT DISTINCT(iLikeResultRef.itemID) FROM iLikeResultRef WHERE totvote!=0)  
				$WHERE AND campaign_Type = 'iDontLike'
				ORDER BY $ORDER";
		 $sql_csv = $this->db->query("SELECT totVote as Vote, itemCode as Item_Code, ptype as Item_Type, extra_label as Price_Category, itemName as Item_Name, 
				country_Name as Country, campaign_Name as Campaign_Name, campaign_Date as Date
				FROM TotalVotes 
				LEFT JOIN iLikeResultRef ON iLikeResultRef.itemID = TotalVotes.itemID  
				LEFT JOIN campaign       ON campaign.id 		  = TotalVotes.campaignID  
				LEFT JOIN price_range	 ON price_range.id		  = TotalVotes.price_rangeID
				WHERE TotalVotes.itemID IN
				(SELECT DISTINCT(iLikeResultRef.itemID) FROM iLikeResultRef WHERE totvote!=0)  
				$WHERE AND campaign_Type = 'iDontLike'
				ORDER BY $ORDER");
		 }
		
	    }
		else{
		$data['ViewType'] = "iWant";
		$data['ViewLabel'] = "WINNING ITEMS FROM IWANT";
		 
		 if($valid_query==TRUE){
		 	$sql = "SELECT TotalVotes.itemID AS iID, itemCode, ptype, itemName, extra_label, totVote, 
				(SELECT image FROM items_images WHERE defaultStatus = 1 AND items_images.itemID = TotalVotes.itemID LIMIT 0,1) as item_image, 
				country_Name, campaign_Name, campaign_Date
				FROM TotalVotes 
				LEFT JOIN iWantResultRef ON iWantResultRef.itemID = TotalVotes.itemID  
				LEFT JOIN campaign       ON campaign.id 		  = TotalVotes.campaignID  
				LEFT JOIN price_range	 ON price_range.id		  = TotalVotes.price_rangeID
				WHERE TotalVotes.itemID IN
				(SELECT DISTINCT(iWantResultRef.itemID) FROM iWantResultRef)  
				$WHERE AND campaign_Type = 'iWant'
				ORDER BY $ORDER";
		
		  $sql_csv = $this->db->query("SELECT totVote as Vote, itemCode as Item_Code, ptype as Item_Type, extra_label as Price_Category, itemName as Item_Name, 
									country_Name as Country, campaign_Name as Campaign_Name, campaign_Date as Date
									FROM TotalVotes 
									LEFT JOIN iWantResultRef ON iWantResultRef.itemID = TotalVotes.itemID  
									LEFT JOIN campaign       ON campaign.id 		  = TotalVotes.campaignID
									LEFT JOIN price_range	 ON price_range.id		  = TotalVotes.price_rangeID
									WHERE TotalVotes.itemID IN
									(SELECT DISTINCT(iWantResultRef.itemID) FROM iWantResultRef)  
									$WHERE AND campaign_Type = 'iWant'
									ORDER BY $ORDER");
		 }else{
		 $sql = "SELECT TotalVotes.itemID AS iID, itemCode, ptype, itemName, extra_label, totVote, 
				(SELECT image FROM items_images WHERE defaultStatus = 1 AND items_images.itemID = TotalVotes.itemID LIMIT 0,1) as item_image, 
				country_Name, campaign_Name, campaign_Date
				FROM TotalVotes 
				LEFT JOIN iWantResultRef ON iWantResultRef.itemID = TotalVotes.itemID  
				LEFT JOIN campaign       ON campaign.id 		  = TotalVotes.campaignID  
				LEFT JOIN price_range	 ON price_range.id		  = TotalVotes.price_rangeID
				WHERE TotalVotes.itemID IN
				(SELECT DISTINCT(iWantResultRef.itemID) FROM iWantResultRef)  
				$WHERE AND campaign_Type = 'iDontWant'
				ORDER BY $ORDER";
		
		  $sql_csv = $this->db->query("SELECT totVote as Vote, itemCode as Item_Code, ptype as Item_Type, extra_label as Price_Category, itemName as Item_Name, 
									 country_Name as Country, campaign_Name as Campaign_Name, campaign_Date as Date
									 FROM TotalVotes 
									 LEFT JOIN iWantResultRef ON iWantResultRef.itemID = TotalVotes.itemID  
									 LEFT JOIN campaign       ON campaign.id 		  = TotalVotes.campaignID  
									 LEFT JOIN price_range	 ON price_range.id		  = TotalVotes.price_rangeID
									 WHERE TotalVotes.itemID IN
									 (SELECT DISTINCT(iWantResultRef.itemID) FROM iWantResultRef)  
									 $WHERE AND campaign_Type = 'iDontWant'
									 ORDER BY $ORDER");
		 }
	    }
		
		//generate csv file
		$csv  ="";
		$csv .= $data['ViewLabel']."\n";
		$csv .= "No, Vote, Item Code, Item Name, Item Type, Price Category, Country, Campaign Name, Date\n";
		$sql_csv = $sql_csv->result_array(); $x=0;
		foreach($sql_csv as $i)
		{ extract($i);	 $x++; 
			$csv .= "$x, $Vote, $Item_Code, $Item_Name, $Item_Type, $Price_Category, $Country, $Campaign_Name, $Date\n";
		}
		write_file(getcwd()."/files/csv/".str_replace(" ","_",$data['ViewLabel'])."".$this->reportCode().".csv",$csv); 
		$data['csvFile'] = str_replace(" ","_",$data['ViewLabel']).$this->reportCode().".csv";
		$data['vfile']	 = 'campaign_items_summary.php';
		
		//SORT STATUS
		$limit = ($sort)=='y' ? 0 : $limit;
		
		$all_items = $this->db->query($sql);
		$all_items = $all_items->result_array();
		
		//SORT STATUS
		$limit = ($sort)=='y' ? 0 : $limit;
	
		$limit_items = $this->db->query($sql." LIMIT $limit,20");
		$limit_items = $limit_items->result_array();
		$data['totrec'] = count($all_items);
		$data['limit']  = $limit;
		
		$items	 = $limit_items;
		
		$table= "<table id='large2' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:12px;' class='iLike_Result_Table2'>
				<tr>
					<th style='width:10px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>No.  	  			    </b></th>  
					<th style='width:10px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' onclick='sortBy(\"0-A\")'>       <b>Vote  	  			</b></th>  
					<th style='width:66px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' onclick='sortBy(\"1-A\")'>       <b>Item Code  	  		</b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Image  	  	  		</b></th> 
					<th style='width:50px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"2-A\")'>   	<b>Item Name   	 	    </b></th> 
					<th style='width:50px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"3-A\")'>   	<b>Item Type   	 	    </b></th> 
					<th style='width:50px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"7-A\")'>   	<b>Price Category   	</b></th> 
					<th style='width:100px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' onclick='sortBy(\"4-A\")'>      <b>Country  	  		</b></th> 
					<th style='width:163px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' onclick='sortBy(\"5-A\")'>      <b>Campaign Name  	  	</b></th> 
					<th style='width:68px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' onclick='sortBy(\"6-D\")'>       <b>Date 		  	  	 </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  >       <b>Details  	  	    </b></th>  
				</tr>";
				
		//REPLACE ORDER
		$table = str_replace($order_code,$this->sortingRef_winningItems($order_code,'Rev_code'),$table);
		$table = str_replace($label,$this->sortingRef_winningItems($order_code,'label_symbol'),$table);		 
		
					$x = $limit;			
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					$orig_itemName="";
					foreach($items as $r) { 
					extract($r);
					$c = (($x++)%2) == 0 ? "class='alter'" :  "";
					
		$table.= "<tr>
				  <td $c style='text-align:center;'>		$x  			</td>
				  <td $c style='text-align:center;'>		<b>$totVote</b>  			</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$itemCode  		</td>
				  <td $c style='text-align:center;'>			    		<img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 	</td>
				  <td $c style='text-align:left;padding-left:5px;' title='$orig_itemName'>		<a href='".HTTP_PATH."gallery/itemInfo2/$iID' target='_blank'>		$itemName </a>  </td>
				  <td $c style='text-align:left;padding-left:5px;'>		$ptype  </td>
				  <td $c style='text-align:left;padding-left:5px;'>		$extra_label  </td>
				  <td $c style='text-align:left;padding-left:5px;'>		$country_Name  	</td>
				  <td $c style='text-align:left;padding-left:5px;'>		$campaign_Name  	</td>
				  <td $c style='text-align:center;padding-left:5px;'>		$campaign_Date  </td>";
				  if($data['PUBLISH_CAMPAIGN']==TRUE OR $view!='iWant') $table.="<td $c style='text-align:center;'> <a onclick=\"showVoters('$campaignType',$iID)\" style='cursor:pointer;'> Details </a>	</td>";
				  else $table.= "<td $c style='text-align:center;'> <a style='cursor:pointer;'> N/A </a>	</td>";
				  
				  }
		$table.= "</tr>";
					if(!$items)
						$table.=  "<tr><td colspan='14'>No match found, please check your search parameters.</td></tr>";
		$table.= "</table>";	
		
		$data['table'] 		= $table;

		$viewer = 'innerPages';	    
		if($this->modules->access_checker()==TRUE)
		{
			$this->load->view($viewer,$data); 
		}else{
			$data['vfile']				= 'login.php';
			$data['title']				= 'SMBi System Log-in | SMBi';
			$data['page_title']			= 'SMBi System Log-in';
			$data['meta_description']	= 'San Miguel Brewing International';
			$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
			$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
			$this->load->view('login',$data); 	
		}
	}
	
	function Summay_iLike_Voters($iID)
	{
		$sql = "SELECT voterID, fname, lname, gender, email, department, year_of_birth, tdate, ttime  FROM votexRef
				LEFT JOIN voters   ON votexRef.voterID    = voters.id 
				LEFT JOIN campaign ON votexRef.campaignID = campaign.id 
				WHERE votexRef.itemID=$iID AND votexRef.vote = 'yes' AND voters.votingStatus = 'done' AND campaign.campaignType = 'iLike'
				GROUP BY voters.id";
		
		$sql = $this->db->query($sql);
		$voters = $sql->result_array();
		
		//print_r($voters);
		
		echo "<table cellpadding='0' cellspacing='0' border=1 style='width:100%;margin: 0px auto;font-size:12px;' class='iLike_Result_Table'>
			<tr style='border-radius: 6px;'>
				<td style='width:10px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>No 		   	</b></td> 
				<td style='width:130px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>   <b>First Name  	</b></td> 
				<td style='width:130px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>   <b>Last  Name  	</b></td> 
				<td style='width:30px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Gender  	   	</b></td> 
				<td style='width:230px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>   <b>Email  	   	</b></td> 
				<td style='width:50px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Department  	</b></td> 
				<td style='width:50px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Year 	   		</b></td> 
				<td style='width:50px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Age 	   		</b></td> 
				<td style='width:100px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Date 	   		</b></td> 
				<td style='width:30px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Time 	   		</b></td> 
			</tr>";
		 
			$x = 0;
			$total = 0;
			foreach($voters as $v) { 
			extract($v);
			$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
		 
			echo	"<tr>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>		$x 				</td>
					  <td $c  style='text-align:left;padding: 0px 2px 0px 10px;'>		$fname 			</td>
					  <td $c  style='text-align:left;padding: 0px 2px 0px 10px;'>		$lname  		</td>
					  <td $c  style='text-align:left;padding: 0px 2px 0px 10px;'>		$gender  		</td>
					  <td $c  style='text-align:left;padding: 0px 2px 0px 10px;'>		$email  		</td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 10px;'>		$department  	</td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>		$year_of_birth  </td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>".	(date('Y')-$year_of_birth) ."</td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>		". $this->convertDate('date',"$tdate $ttime") ."  		</td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>		". $this->convertDate('time',"$tdate $ttime") ."  		</td>
					</tr>";
			}
		echo"</table>";
		
	}

	function Summay_iWant_Voters($iID)
	{
		$sql = "SELECT voterID, fname, lname, gender, email, department, year_of_birth, tdate, ttime  FROM voters
				LEFT JOIN votexRef   ON votexRef.voterID    = voters.id 
				LEFT JOIN campaign   ON votexRef.campaignID = campaign.id
				LEFT JOIN iWantResultRef ON iWantResultRef.campaignID = campaign.id
				WHERE (votexRef.itemID=$iID AND votexRef.vote = 'yes' AND voters.votingStatus = 'done' AND campaign.campaignType = 'iWant' AND iWantResultRef.itemID = $iID)
				GROUP BY voters.id";
			
		$sql = $this->db->query($sql);
		$voters = $sql->result_array();
	
		echo "<table cellpadding='0' cellspacing='0' border=1 style='width:100%;margin: 0px auto;font-size:12px;' class='iLike_Result_Table'>
			<tr style='border-radius: 6px;'>
				<td style='width:10px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>No 		    </b></td> 
				<td style='width:130px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>   <b>First Name  	</b></td> 
				<td style='width:130px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>   <b>Last  Name  	</b></td> 
				<td style='width:30px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Gender  	   	</b></td> 
				<td style='width:230px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>   <b>Email  	   	</b></td> 
				<td style='width:50px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Department  	</b></td> 
				<td style='width:50px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Year 	   		</b></td> 
				<td style='width:50px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Age 	   		</b></td> 
				<td style='width:100px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Date 	   		</b></td> 
				<td style='width:30px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Time 	   		</b></td>  
			</tr>";
		 
			$x = 0;
			$total = 0;
			foreach($voters as $v) { 
			extract($v);
			$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
		 
			echo	"<tr>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>		$x 				</td>
					  <td $c  style='text-align:left;padding: 0px 2px 0px 10px;'>		$fname 			</td>
					  <td $c  style='text-align:left;padding: 0px 2px 0px 10px;'>		$lname  		</td>
					  <td $c  style='text-align:left;padding: 0px 2px 0px 10px;'>		$gender  		</td>
					  <td $c  style='text-align:left;padding: 0px 2px 0px 10px;'>		$email  		</td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 10px;'>		$department  	</td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>		$year_of_birth  </td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>".	(date('Y')-$year_of_birth) ."</td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>		". $this->convertDate('date',"$tdate $ttime") ."  		</td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>		". $this->convertDate('time',"$tdate $ttime") ."  		</td>
					</tr>";
			}
		echo"</table>";
		
	}
	
	function Summay_iWant_Voters2($iID)
	{
		$sql = "SELECT voterID, fname, lname, gender, email, department, year_of_birth, tdate, ttime  FROM voters
				LEFT JOIN votexRef   ON votexRef.voterID    = voters.id 
				LEFT JOIN campaign   ON votexRef.campaignID = campaign.id
				LEFT JOIN iWantResultRef ON iWantResultRef.campaignID = campaign.id
				WHERE (votexRef.itemID=$iID AND votexRef.vote = 'yes' AND voters.votingStatus = 'done' AND campaign.campaignType = 'iWant')
				GROUP BY voters.id";
			
		$sql = $this->db->query($sql);
		$voters = $sql->result_array();
	
		echo "<table cellpadding='0' cellspacing='0' border=1 style='width:100%;margin: 0px auto;font-size:12px;' class='iLike_Result_Table'>
			<tr style='border-radius: 6px;'>
				<td style='width:10px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>No 		    </b></td> 
				<td style='width:130px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>   <b>First Name  	</b></td> 
				<td style='width:130px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>   <b>Last  Name  	</b></td> 
				<td style='width:30px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Gender  	   	</b></td> 
				<td style='width:230px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>   <b>Email  	   	</b></td> 
				<td style='width:50px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Department  	</b></td> 
				<td style='width:50px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Year 	   		</b></td> 
				<td style='width:50px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Age 	   		</b></td> 
				<td style='width:100px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Date 	   		</b></td> 
				<td style='width:30px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Time 	   		</b></td>  
			</tr>";
		 
			$x = 0;
			$total = 0;
			foreach($voters as $v) { 
			extract($v);
			$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
		 
			echo	"<tr>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>		$x 				</td>
					  <td $c  style='text-align:left;padding: 0px 2px 0px 10px;'>		$fname 			</td>
					  <td $c  style='text-align:left;padding: 0px 2px 0px 10px;'>		$lname  		</td>
					  <td $c  style='text-align:left;padding: 0px 2px 0px 10px;'>		$gender  		</td>
					  <td $c  style='text-align:left;padding: 0px 2px 0px 10px;'>		$email  		</td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 10px;'>		$department  	</td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>		$year_of_birth  </td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>".	(date('Y')-$year_of_birth) ."</td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>		$tdate  		</td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>		$ttime  		</td>
					</tr>";
			}
		echo"</table>";
		
	}
	
	function item_division($view='',$cID='',$year='',$month='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(73,'REVIEW');
		
		$filter_WHERE="WHERE items.countryID = $cID";
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/BU_activeness_index> Activeness of Business Units Index </a>';
		
		$groupBy      = "";
		if($view=='gCountry'){
			$data['vType'] = $view;
		}elseif($view=='gYear'){
			$filter_WHERE .= "  AND YEAR(items.dateAdded) = $year";
			$data['vType'] = $view;
			$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
			$data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/BU_activeness_per_Year/$cID'> Activeness of Business Units per Year</a>";
		}elseif($view=='gMonth'){
			$filter_WHERE .= " AND YEAR(items.dateAdded) = $year AND MONTH(items.dateAdded) = $month";
			$data['vType'] = $view;
			
			$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
			$data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/BU_activeness_per_Year/$cID'> Activeness of Business Units per Year</a>";
			$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
			$data['breadCrumbs']		.= "<a href='".HTTP_PATH."report/BU_activeness_per_Month/$cID/$year'> Activeness of Business Units per Month</a>";
		}	
		
		if($cID==0){
			$filter_WHERE = "WHERE items.countryID != 0";
		}if($cID==0 AND $view=='gYear'){
			$filter_WHERE = "WHERE items.countryID != 0 AND YEAR(items.dateAdded) = $year";
		}if($cID==0 AND $view=='gMonth'){
			$filter_WHERE = "WHERE items.countryID != 0 AND YEAR(items.dateAdded) = $year AND MONTH(items.dateAdded) = $month";
		}
		
		
		$table						= 'item_division';
		$data['vfile']				= 'item_division.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		$data['countryID']			= $cID;
		$data['cyear']				= $year;	
		$data['previewType']		= $view;
		
		
		//POSM TYPE;
		$arr = '';
		$sql   = $this->db->query("SELECT DISTINCT(items.POSMTypeID) as fieldValue, (SELECT typeName FROM POSM_Type WHERE POSM_Type.id = items.POSMTypeID) as col, AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice, 
								   COUNT(items.id) as num_items, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year, country.id as cID 
								   FROM   items 	
								   LEFT   JOIN country 		ON country.id  = items.countryID
								   $filter_WHERE
								   GROUP BY items.POSMTypeID ORDER BY YEAR(items.dateAdded) DESC, MONTH(items.dateAdded) DESC");
		$arr[] = array('table'=>'POSM TYPE',
					   'fld'=>'POSMTypeID',
					   'rows'=>$sql->result_array());
		
		//POSM STATUS
		$sql   = $this->db->query("SELECT DISTINCT(items.POSMStatusID) as fieldValue, (SELECT statusName FROM POSM_Status WHERE POSM_Status.id = items.POSMStatusID) as col, AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice, 
								   COUNT(items.id) as num_items, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year, country.id as cID 
								   FROM   items 	
								   LEFT   JOIN country 		ON country.id  = items.countryID
								   $filter_WHERE
								   GROUP BY items.POSMStatusID ORDER BY YEAR(items.dateAdded) DESC, MONTH(items.dateAdded) DESC");
		$arr[] = array('table'=>'POSM STATUS',
					   'fld'=>'POSMStatusID',
					   'rows'=>$sql->result_array());
		
		//OUTLET TYPE
		$sql   = $this->db->query("SELECT DISTINCT(items.OUTLETStatusID) as fieldValue, (SELECT statusName FROM OUTLET_Status WHERE OUTLET_Status.id = items.OUTLETStatusID) as col, AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice, 
								   COUNT(items.id) as num_items, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year, country.id as cID 
								   FROM   items 	
								   LEFT   JOIN country 		ON country.id  = items.countryID
								   $filter_WHERE
								   GROUP BY items.OUTLETStatusID ORDER BY YEAR(items.dateAdded) DESC, MONTH(items.dateAdded) DESC");
		$arr[] = array('table'=>'SERVICE ITEM OUTLET TYPE',
					   'fld'=>'OUTLETStatusID',
					   'rows'=>$sql->result_array());
		
		//PREMIUM ITEM TYPE
		$sql   = $this->db->query("SELECT DISTINCT(items.PremiumTypeID) as fieldValue, (SELECT premiumTypeName FROM premiumItemType WHERE premiumItemType.id = items.PremiumTypeID) as col, AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice, 
								   COUNT(items.id) as num_items, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year, country.id as cID 
								   FROM   items 	
								   LEFT   JOIN country 		ON country.id  = items.countryID
								   $filter_WHERE
								   GROUP BY items.PremiumTypeID ORDER BY YEAR(items.dateAdded) DESC, MONTH(items.dateAdded) DESC");
		$arr[] = array('table'=>'PREMIUM ITEM TYPE',
					   'fld'=>'PremiumTypeID',
					   'rows'=>$sql->result_array());
		
		//MATERIAL TYPE
		$sql   = $this->db->query("SELECT DISTINCT(items.MaterialTypeID) as fieldValue, (SELECT materialName FROM MATERIAL_Type WHERE MATERIAL_Type.id = items.MaterialTypeID) as col, AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice, 
								   COUNT(items.id) as num_items, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year, country.id as cID 
								   FROM   items 	
								   LEFT   JOIN country 		ON country.id  = items.countryID
								   $filter_WHERE
								   GROUP BY items.MaterialTypeID ORDER BY YEAR(items.dateAdded) DESC, MONTH(items.dateAdded) DESC");
		$arr[] = array('table'=>'MATERIAL TYPE',
						'fld'=>'MaterialTypeID',
						'rows'=>$sql->result_array());
		
		//BRAND
		$sql   = $this->db->query("SELECT DISTINCT(items.brandID) as fieldValue, (SELECT brandName FROM brands WHERE brands.id = items.brandID) as col, AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice, 
								   COUNT(items.id) as num_items, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year, country.id as cID 
								   FROM   items 	
								   LEFT   JOIN country 		ON country.id  = items.countryID
								   $filter_WHERE
								   GROUP BY items.brandID ORDER BY YEAR(items.dateAdded) DESC, MONTH(items.dateAdded) DESC");
		$arr[] = array('table'=>'BRAND',
					   'fld'=>'brandID',
					   'rows'=>$sql->result_array());
		
		$data['results'] = $arr;
		
		$viewer = 'innerPages';	    
		if($this->modules->access_checker()==TRUE)
		{
			$this->load->view($viewer,$data); 
		}else{
			$data['vfile']				= 'login.php';
			$data['title']				= 'SMBi System Log-in | SMBi';
			$data['page_title']			= 'SMBi System Log-in';
			$data['meta_description']	= 'San Miguel Brewing International';
			$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
			$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
			$this->load->view('login',$data); 	
		}
	}
	
	function Distribution_Items($action='',$id='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(73,'REVIEW');
		
		$filter_WHERE='';
		if($_SESSION['super_admin']!='y')
			$filter_WHERE 		= "WHERE items.countryID =".$_SESSION['countryID'] ." AND YEAR(items.dateAdded) = ".DATE("Y");
		else
			$filter_WHERE 		= "WHERE YEAR(items.dateAdded) = ".DATE("Y");
		
		$table						= 'Distribution_Items';
		$data['vfile']				= 'Distribution_Items.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/Distribution_Items> Distribution of Promo Items </a>';
		
		//TOTAL NUMBER OF ROWS			
		
		extract($_POST);
		$WHERE 	 =  $filter_WHERE;
		
		$data['per_country'] = TRUE;
		$sql   = $this->db->query("SELECT country.countryName AS cName, country.id AS cID, AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice, 
								   COUNT(items.id) as num_items, target, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year 
								   FROM   items 		 
								   LEFT   JOIN country 		ON country.id  = items.countryID 
								   LEFT   JOIN target_items ON target_items.countryID = items.countryID 
								   $WHERE
								   GROUP BY country.id ORDER BY YEAR(items.dateAdded) DESC");
		
		if(isset($filter))
		{   
			$data['per_country'] = FALSE;
			$WHERE 	 = '';
			if($countryID!='' AND $countryID!='all') $WHERE  .=  " items.countryID = $countryID AND";
			if($countryID=='all')					 $WHERE   =  "";
			if($cyear!=''  AND $cyear!='all') 	 	 $WHERE  .=  " YEAR(items.dateAdded) = $cyear AND";
			
			if($WHERE!='')  $WHERE  =  substr("WHERE ".$WHERE,0,-3);
			
			$sql   = $this->db->query("SELECT country.countryName AS cName, country.id AS cID,  AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice, 
									   COUNT(items.id) as num_items, target, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year 
									   FROM items 		 
									   LEFT   JOIN country 		ON country.id  = items.countryID 
									   LEFT   JOIN target_items ON target_items.countryID = items.countryID 
									   $WHERE
									   GROUP BY cName, YEAR(items.dateAdded) ORDER BY YEAR(items.dateAdded) DESC");
		}
		
		$data['POST'] = $_POST;
		$data['reports']   = $sql->result_array();
		

		$viewer = 'innerPages';	    
		if($this->modules->access_checker()==TRUE)
		{
			$this->load->view($viewer,$data); 
		}else{
			$data['vfile']				= 'login.php';
			$data['title']				= 'SMBi System Log-in | SMBi';
			$data['page_title']			= 'SMBi System Log-in';
			$data['meta_description']	= 'San Miguel Brewing International';
			$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
			$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
			$this->load->view('login',$data); 	
		}
	}
	
	function Distribution_Items_in_Details($cID='',$category='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(73,'REVIEW');
		$c='';
		
		if($cID!=''){
			$data['countryID'] = $cID;
			$c = "country.id = $cID AND"; 
		}	
			
		$filter_WHERE	= "";
		$filter_WHERE 	= "WHERE $c YEAR(items.dateAdded) = ".DATE("Y");
		
		//die();
		
		$table						= 'BU_activeness';
		$data['vfile']				= 'Distribution_Items_in_Details.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					 = HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report/Distribution_Items> Distribution of Promo Items in Details </a>';
		
		//TOTAL NUMBER OF ROWS			
		
		extract($_POST);
		$WHERE 	 =  $filter_WHERE;
		
		$data['default'] = TRUE;
		$groupBy = "items.POSMTypeID";
		if(isset($filter))
		{   
			$data['default'] = FALSE;
			$data['countryID'] = $countryID;
			$WHERE 	 = '';
			if($countryID!='' AND $countryID!='all') $WHERE  .=  " items.countryID = $countryID AND";
			if($countryID=='all')					 $WHERE   =  "";
			if($cyear!=''  AND $cyear!='all') 	 	 $WHERE  .=  " YEAR(items.dateAdded) = $cyear AND";
			if($fmonth!='' AND $fmonth!='all' AND $tmonth!='' AND $tmonth!='all') 	 	 
					$WHERE  .=  " MONTH(items.dateAdded) >= $fmonth AND  MONTH(items.dateAdded) <= $tmonth AND";
			
			if($WHERE!='')  $WHERE  =  substr("WHERE ".$WHERE,0,-3);
			$groupBy =  "items.POSMTypeID, MONTH(items.dateAdded)";
		}
		
		switch($category)
		{
			case 'POSMTypeID':
			$data['category'] = 'POSMTypeID';
			$data['category_label'] = 'POSM TYPE';
			$sql   = $this->db->query("SELECT DISTINCT(items.POSMTypeID) as fieldValue, (SELECT typeName FROM POSM_Type WHERE POSM_Type.id = items.POSMTypeID) as col, AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice, 
									   COUNT(items.id) as num_items, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year, country.id as cID 
									   FROM   items 	
									   LEFT   JOIN country 		ON country.id  = items.countryID
									   $WHERE
									   GROUP BY $groupBy ORDER BY YEAR(items.dateAdded) DESC, MONTH(items.dateAdded) DESC");
			break;
			case 'POSMStatusID':
			$data['category'] = 'POSMStatusID';
			$data['category_label'] = 'POSM STATUS';
			$sql   = $this->db->query("SELECT DISTINCT(items.POSMStatusID) as fieldValue, (SELECT statusName FROM POSM_Status WHERE POSM_Status.id = items.POSMStatusID) as col, AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice, 
									   COUNT(items.id) as num_items, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year, country.id as cID 
									   FROM   items 	
									   LEFT   JOIN country 		ON country.id  = items.countryID
									   $WHERE
									   GROUP BY items.POSMStatusID, MONTH(items.dateAdded) ORDER BY YEAR(items.dateAdded) DESC, MONTH(items.dateAdded) DESC");
			break;
			case 'OUTLETStatusID':
			$data['category'] = 'OUTLETStatusID';
			$data['category_label'] = 'SERVICE ITEM OUTLET TYPE';
			$sql   = $this->db->query("SELECT DISTINCT(items.OUTLETStatusID) as fieldValue, (SELECT statusName FROM OUTLET_Status WHERE OUTLET_Status.id = items.OUTLETStatusID) as col, AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice, 
									   COUNT(items.id) as num_items, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year, country.id as cID 
									   FROM   items 	
									   LEFT   JOIN country 		ON country.id  = items.countryID
									   $WHERE
									   GROUP BY items.OUTLETStatusID, MONTH(items.dateAdded) ORDER BY YEAR(items.dateAdded) DESC, MONTH(items.dateAdded) DESC");
			break;
			case 'PremiumTypeID':
			$data['category'] = 'PremiumTypeID';
			$data['category_label'] = 'PREMIUM ITEM TYPE';
			$sql   = $this->db->query("SELECT DISTINCT(items.PremiumTypeID) as fieldValue, (SELECT premiumTypeName FROM premiumItemType WHERE premiumItemType.id = items.PremiumTypeID) as col, AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice, 
									   COUNT(items.id) as num_items, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year, country.id as cID 
									   FROM   items 	
									   LEFT   JOIN country 		ON country.id  = items.countryID
									   $WHERE
									   GROUP BY items.PremiumTypeID, MONTH(items.dateAdded) ORDER BY YEAR(items.dateAdded) DESC, MONTH(items.dateAdded) DESC");
			break;
			case 'MaterialTypeID':
			$data['category'] 		= 'MaterialTypeID';
			$data['category_label'] = 'MATERIAL TYPE';
			$sql   = $this->db->query("SELECT DISTINCT(items.MaterialTypeID) as fieldValue, (SELECT materialName FROM MATERIAL_Type WHERE MATERIAL_Type.id = items.MaterialTypeID) as col, AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice, 
									   COUNT(items.id) as num_items, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year, country.id as cID 
									   FROM   items 	
									   LEFT   JOIN country 		ON country.id  = items.countryID
									   $WHERE
									   GROUP BY items.MaterialTypeID, MONTH(items.dateAdded) ORDER BY YEAR(items.dateAdded) DESC, MONTH(items.dateAdded) DESC");
			break;
			case 'brandID':
			$data['category'] = 'brandID';
			$data['category_label'] = 'BRAND';
			$sql   = $this->db->query("SELECT DISTINCT(items.brandID) as fieldValue, (SELECT brandName FROM brands WHERE brands.id = items.brandID) as col, AVG(UnitPrice) AS localPrice, AVG(USD_Price) AS usPrice, 
									   COUNT(items.id) as num_items, DATE_FORMAT(items.dateAdded,'%M') as month, MONTH(items.dateAdded) AS mID, YEAR(items.dateAdded)  as year, country.id as cID 
									   FROM   items 	
									   LEFT   JOIN country 		ON country.id  = items.countryID
									   $WHERE
									   GROUP BY items.brandID, MONTH(items.dateAdded) ORDER BY YEAR(items.dateAdded) DESC, MONTH(items.dateAdded) DESC");
			break;
			
			$data['countryID'] = $countryID;
		}
		
		$data['POST'] = $_POST;			
		$data['reports']   = $sql->result_array();
		

		$viewer = 'innerPages';	    
		if($this->modules->access_checker()==TRUE)
		{
			$this->load->view($viewer,$data); 
		}else{
			$data['vfile']				= 'login.php';
			$data['title']				= 'SMBi System Log-in | SMBi';
			$data['page_title']			= 'SMBi System Log-in';
			$data['meta_description']	= 'San Miguel Brewing International';
			$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
			$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
			$this->load->view('login',$data); 	
		}
	}
	
	function Distribution_Items_Preview($view='',$category='',$fieldValue='',$countryID='',$month='',$year='')
	{
		echo "<!DOCTYPE HTML>
			  <html>
			  <head>";
		echo "<script type='text/javascript' src='".HTTP_PATH ."js/jquery-1.8.0.js'></script>";
		echo "<script type='text/javascript' src='".HTTP_PATH ."js/jquery.tablesorter.js'></script>";
		echo "<script type='text/javascript'>
				$(function() {
						$('table').tablesorter({widgets: ['zebra'] })
				});
			</script>
			</head>
			</body>";
		
		$field  ='';
		$filter ='';
		switch($category)
		{
			case 'POSMTypeID':
			$field   = "(SELECT typeName FROM POSM_Type where POSM_Type.id=items.POSMTypeID) as ptype";
			$filter  = "items.POSMTypeID = $fieldValue";
			$label 	 = "POSM TYPE";
			break;
			
			case 'POSMStatusID':
			$data['category'] = 'POSMStatusID';
			$field   = "(SELECT statusName FROM POSM_Status where POSM_Status.id=items.POSMStatusID) as ptype";
			$filter  = "items.POSMStatusID = $fieldValue";
			$label 	 = "POSM STATUS";
			break;
			
			case 'OUTLETStatusID':
			$data['category'] = 'OUTLETStatusID';
			$field   = "(SELECT statusName FROM OUTLET_Status where OUTLET_Status.id=items.OUTLETStatusID) as ptype";
			$filter  =  "items.OUTLETStatusID = $fieldValue";
			$label 	 = "OUTLET TYPE";
			break;
			
			case 'PremiumTypeID':
			$data['category'] = 'PremiumTypeID';
			$field   = "(SELECT premiumTypeName FROM premiumItemType where premiumItemType.id=items.PremiumTypeID) as ptype";
			$filter  =  "items.PremiumTypeID = $fieldValue";
			$label 	 = "PREMIUM TYPE";
			break;
			
			case 'MaterialTypeID':
			$data['category'] 		= 'MaterialTypeID';
			$field   = "(SELECT materialName FROM MATERIAL_Type where MATERIAL_Type.id=items.MaterialTypeID) as ptype";
			$filter  =  "items.MaterialTypeID = $fieldValue";
			$label 	 = "MATERIAL";
			break;
			
			case 'brandID':
			$data['category'] = 'brandID';
			$field   = "(SELECT brandName FROM brands where brands.id=items.brandID) as ptype";
			$filter  =  "items.brandID = $fieldValue";
			$label 	 = "BRAND";
			break;
			
			$data['countryID'] = $countryID;
		}
		
		if($view=='default'){
			$sql = "SELECT items.id as itemID, $field, itemName,  itemCode, UnitPrice, USD_Price, 
						(SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, items.dateAdded as dUploaded, full_name, publish 
						 FROM items LEFT JOIN admin_users ON admin_users.id = items.user_id
						 WHERE items.countryID = $countryID AND YEAR(items.dateAdded) = $year  
						 AND $filter
						 ORDER BY UnitPrice DESC";
		}else{
			$sql = "SELECT items.id as itemID, $field, itemName,  itemCode, UnitPrice, USD_Price, 
						(SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = items.id) as item_image, items.dateAdded as dUploaded, full_name, publish 
						 FROM items LEFT JOIN admin_users ON admin_users.id = items.user_id
						 WHERE items.countryID = $countryID AND MONTH(items.dateAdded) = $month AND YEAR(items.dateAdded) = $year  
						 AND $filter
						 ORDER BY UnitPrice DESC";
		}
			$sql 	 = $this->db->query($sql);
			$items	 = $sql->result_array();
			//print_r($items);
			
		echo "<table id='large' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:12px;' class='iLike_Result_Table tablesorter'>
				<thead>
				<tr>
					<th style='width:10px;text-align:center;color:white;padding:0px' bgcolor='#bb1d1d'>   					   <b>No 		 		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Item Code  	  	  </b></th> 
					<th style='width:19px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >   	   <b>Image   	 	      </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Name  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>$label  	  		  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Local Price  	  	  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>USD   Price  	  	  </b></th> 
					<th style='width:30px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Publish  	  		  </b></th> 
					<th style='width:75px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d' >       <b>Date uploaded  	  </b></th> 
				</tr>
				</thead>
				<tbody>";
				 
					$x = 0;			
					$y=1;
					$z=1;
					$total=0;
					$total_target=0;
					$sumUnitPrice=0;
					$sumUSD_Price=0;
					foreach($items as $r) { 
					extract($r);
					$ptype = ($ptype=='') 	   ? 'Uncategorize' : $ptype;
					$publish = ($publish=='y') ? 'Yes' : 'No';
					$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
					$sumUnitPrice += number_format($UnitPrice, 2, '.', '');
					$sumUSD_Price +=  number_format($USD_Price, 2, '.', '');
		echo 	"<tr>
				  <td $c>													$x      																		</td>
				  <td $c>													$itemCode      																	</td>
				  <td $c style='text-align:center;'>			  <img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 	</td>
				  <td $c style='text-align:left;padding-left:5px;'>	<a href='".HTTP_PATH."gallery/itemInfo2/$itemID' target='_blank'>		$itemName</a>  </td>
				  <td $c style='text-align:left;padding-right:20px;'>	$ptype											</td>
				  <td $c style='text-align:right;padding-right:20px;'>	". number_format($UnitPrice, 2, '.', '') ."										</td>
				  <td $c style='text-align:right;padding-right:20px;'>	". number_format($USD_Price, 2, '.', '')."										</td>
				  <td $c style='text-align:center;'>	$publish										</td>
				  <td $c style='text-align:center;'>				". date("M d, Y", strtotime($dUploaded)) ."			</td>
				</tr>";}
		echo 	"<tr> 
					<td>Average</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td style='text-align:right;padding-right:20px;'>". number_format($sumUnitPrice/$x, 2, '.', '') ."</td>
					<td style='text-align:right;padding-right:20px;'>". number_format($sumUSD_Price/$x, 2, '.', '') ."</td>
					<td></td>
					<td></td>
				</tr>";		
				
		echo	"</tbody>";
					if(!$items)
						echo "<tr><td colspan='7'>No match found.</td></tr>";
		echo	"</table>";	
		
		
		echo "</body>
			  </html>";
	}
	
}