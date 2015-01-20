<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Report2 extends CI_Controller {
   public function __construct()
    {
	 parent::__construct();
	 date_default_timezone_set('UTC');
	 session_start();
	 $this->load->model('c3model');
	 $this->output->enable_profiler(FALSE);
	 $this->load->library('modules');
	 $this->load->helper('url');
	 $this->load->helper('file');
	 $this->load->helper('download');
	 $this->modules->session_handler();
	 //$this->load->library('db_check');
    }

	public function index()
	{
	   $this->modules->access_checker();
	   $this->modules->module_checker(1,'REVIEW');
	   
	   $data		= $this->featured();
	   $data['vfile']		= 'userSubMenu.php';
	   $data['title']		= 'user Management | SMBi';
	   $data['page_title']	= 'ADMIN';
	   $data['meta_description']	= 'San Miguel Brewing International';
	   $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
	   
	   $HTTP_PATH = HTTP_PATH;
	   $data['breadCrumbs']			= '<a href='.$HTTP_PATH.'users> Users </a>';
	   
	   
	   $this->load->view('menu',$data); 
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
	
	function checkStr($str='')
	{
		if(strpos($str, 'and')==TRUE OR strpos($str, 'And')==TRUE OR strpos($str, 'AND')==TRUE OR strpos($str, '&')==TRUE)
		 return TRUE;
		else
		 return FALSE;
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
	
	function price_range_summary($POSMTypeID='',$DateFrom='',$DateTo='')
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
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report2/price_range_summary> Price Category - Summary </a>';
		
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
			
			$DateFrom = $this->minMaxDate('prevMonth');
			$DateTo   = $this->minMaxDate('nextMonth');
		}
		
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
			
			//COUNTRY NAME
			if(($opt1=='cName' OR $opt1=='price_level' OR $opt1=='price_rangeName') AND $val1!='')
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
			if(($opt2=='cName' OR $opt2=='price_level' OR $opt2=='price_rangeName') AND $val2!='')
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
		$limit =isset($selpage)? $selpage:0;
		
		//POSM STATUS;
		$arr 		= '';
		$sql_csv	= '';
		$typeName   = '';
		$countryName = '';
		$POSM_Type 	 = '';
		
		//STATUS LISTS
		$user_country = '';
		if($_SESSION['super_admin']!='y' AND $_SESSION['countryID']!=0) $user_country = " AND cID =".$_SESSION['countryID'];

		$dbSQL 		= "SELECT DISTINCT(cID) as cID, cName FROM item_db_reports $WHERE ORDER BY cName ASC";
		$sql 		= $this->db->query($dbSQL);
		$countries  = $sql->result_array();
		
		
		$all_items = array();
		//SORT STATUS
		$limit = ($sort)=='y' ? 0 : $limit;
	
		$limit_items = $this->db->query($dbSQL." LIMIT $limit,2");
		$limit_items = $limit_items->result_array();
		$data['totrec'] = count($countries);
		$data['limit']  = $limit;
		
		
		$sql 	   = $this->db->query("SELECT POSM_Type.id as pID, typeName FROM POSM_Type ORDER BY typeName ASC");
		$POSM_Type = $sql->result_array();
		
		$sql 	     = $this->db->query("SELECT POSM_Status.id as POSM_StatusID, statusName FROM POSM_Status ORDER BY statusName DESC");
		$POSM_Status = $sql->result_array();
		
		//print_r($POSM_Status);
		
		$x=0; 
		$table='<table cellpadding="0" cellspacing="0" style="width:100%;margin-top: -25px;margin-bottom: 20px;" bgcolor="white" id="large">';
		$cName='';
		foreach($limit_items as $country)
		{extract($country);
			//HEADER
			if($countryName!=$cName){ 
			$table .= "<tr><td colspan='6' style='height: 10px;'> <td></tr>
						 <tr>   
							<td colspan='9' style='clear:both;text-align: left;background: #bb1d1d;margin: 0;color: #fff;padding: 2px 10px;font-size:15px;' class='alter'><b>Country: $cName </b></td> 
						</tr>";
			
			 foreach($POSM_Status as $p_status)
			 { extract($p_status);
				$table .= "<tr><td colspan='9' style='clear:both;text-align: left;background: white;margin: 0;color: black;padding: 2px 10px;font-size:14px;height:5px;' class='alter'> </td>  </tr>";
				$x=0;
				foreach($POSM_Type as $p_type)
				{ extract($p_type);
				  $table .= "<tr>
								<td colspan='9' style='clear:both;text-align: left;background: #d3d3d3;margin: 0;color: black;padding: 2px 10px;font-size:14px;' class='alter'><b> Item Status: $statusName | Item Type: $typeName </b></td>  
							</tr>";
				  $sql = $this->db->query("SELECT i.id as priceID, xOrder, level_name, extra_label, campaign_label,   
										   i.cond1 AS icond1, i.min_val as imin_val, i.logical_operator as ilogical_operator, i.cond2 as icond2, i.max_val as imax_val
										   FROM price_range as i
										   WHERE POSMTypeID = $pID ORDER BY xOrder ASC");
				  
				  $sql = $sql->result_array();
				   $sql[] = array(
								'priceID' => '0',
								'xOrder' => '-',
								'level_name' 		=> 'Uncategorized',
								'extra_label' 		=> 'Uncategorized',
								'campaign_label' 	=> 'Level 1',
								'icond1' 			=> '=',
								'imin_val' 			=> '0',
								'ilogical_operator' => '',
								'icond2' 			=> '', 
								'imax_val' 			=> '');
				  $table .= "<tr style='border-radius: 6px;'>
							<th class='row-title'  style='width:50px;text-align:center;font-size: 12px;padding: 0;'> 			No. 	     		 	 </th> 
							<th class='row-title' style='text-align:center;font-size: 12px;padding: 0;'>						Price Label 		 	 </th> 
							<th class='row-title' style='text-align:center;font-size: 12px;padding: 0;'>						Price Category 		 	 </th> 
							<th class='row-title' style='width:130px;text-align:center;font-size: 12px;padding: 0;'> 			Uploaded Items 	     	 </th> 
							<th class='row-title' style='width:130px;text-align:center;font-size: 12px;padding: 0;'> 			For Approval 	         </th> 
							<th class='row-title' style='width:130px;text-align:center;font-size: 12px;padding: 0;'> 			Disapproved 	         </th> 
							<th class='row-title' style='width:130px;text-align:center;font-size: 12px;padding: 0;'> 			Published Items 	     </th> 
							<th class='row-title' style='width:130px;text-align:center;font-size: 12px;padding: 0;'> 			Views 	 				 </th>  
							<th class='row-title' style='width: 141px;text-align:center;font-size: 12px;padding: 0;'> 		    Action 		 	 	     </th>   
						</tr>";
				  $q = '';
				  $TotalUploaded_Items    = 0;
				  $TotalPublished_Items   = 0;
				  $TotalFor_Approval 	  = 0;
				  $TotalmyViews 		  = 0;
				  $TotalDisapproved_Items = 0;
				 
				  $cond="";
				  foreach($sql as $s)
				  { extract($s);
				    //CREATE CONDITION
					if($icond1=="==") $icond1="=";
					if($icond2=="==") $icond2="=";
					if($ilogical_operator!="" AND $icond2!="" AND $imax_val!="")$cond = "OR (USD_Price $icond1 $imin_val $ilogical_operator USD_Price $icond2 $imax_val)";
					else														$cond = "OR (USD_Price $icond1 $imin_val)";
					//if('level_name')
					
					$q = $this->db->query("SELECT 
										 cName 			  as Country_Name, cID,
										 price_rangeName  as fldVal,
										 pStatusID 		  as fldID,
										 COUNT(itemID) 	  as Uploaded_Items , 
										 SUM(publish='y') as Published_Items,
										 SUM(publish='n' AND disapprove='n') as For_Approval,
										 SUM(disapprove='y') as Disapproved_Items,
										 SUM(num_views)   as myViews 
										 FROM item_db_reports
										 $WHERE AND 
										 cID = $cID AND ptypeID = $pID AND pstatusID = $POSM_StatusID  AND (price_rangeID = $priceID)
										 GROUP BY cName, price_rangeID $HAVING");
					$q = $q->result_array();
					//print_r($q);
					$Uploaded_Items  = 0;
					$Published_Items = 0;
					$For_Approval 	 = 0;
					$Disapproved_Items 	 = 0;
					$myViews 		 = 0;		 
					if($q){
					$Uploaded_Items  = $q[0]['Uploaded_Items'];
					$Published_Items = $q[0]['Published_Items'];
					$For_Approval 	 = $q[0]['For_Approval'];
					$Disapproved_Items 	 = $q[0]['Disapproved_Items'];
					$myViews 		 = $q[0]['myViews'];
					$TotalUploaded_Items  += $q[0]['Uploaded_Items'];
					$TotalPublished_Items += $q[0]['Published_Items'];
					$TotalFor_Approval 	  += $q[0]['For_Approval'];
					$TotalDisapproved_Items  += $q[0]['Disapproved_Items'];
					$TotalmyViews 		  += $q[0]['myViews'];
					}
					
					$c = (($x++)%2) != 0 ? "class='alter alter-2'" :  ""; 
					//IF Q HAS CONTENT
					//if($q){
					$table .= "<tr>
								<td $c style='text-align:center;font-size: 12px;'>".   $xOrder			."</td>
								<td $c style='text-align:left;font-size: 12px;'>". $level_name  		."</td>
								<td $c style='text-align:left;font-size: 12px;'>". $extra_label  		."</td>
								<td $c style='font-size: 12px;'>". $Uploaded_Items 						."</td>
								<td $c style='font-size: 12px;'>". $For_Approval 						."</td>
								<td $c style='font-size: 12px;'>". $Disapproved_Items 						."</td>
								<td $c style='font-size: 12px;'>". $Published_Items 					."</td>
								<td $c style='font-size: 12px;'>". $myViews 							."</td>
								<td $c style='text-align:center;font-size: 12px;'> 
									<a href='".HTTP_PATH."report2/price_range_summary_details/$cID/pstatusID/$POSM_StatusID/ptypeID/$pID/$priceID/$DateFrom/$DateTo'>Details</a>
								</td>";
					$table .= "</tr>";
					//}
				  }
				  $c = (($x++)%2) != 0 ? "class='alter alter-2'" :  ""; 
				  $table .= "<tr>
								<td $c style='text-align:left;font-size: 12px;padding: 0;'> <b>Total </b></td>
								<td $c style='text-align:left;font-size: 12px;padding: 0;'></td>
								<td $c style='text-align:left;font-size: 12px;padding: 0;'></td>
								<td $c style='font-size: 12px;padding: 0;'> <b>". $TotalUploaded_Items 					." </b></td>
								<td $c style='font-size: 12px;padding: 0;'> <b>". $TotalFor_Approval 						." </b></td>
								<td $c style='font-size: 12px;padding: 0;'> <b>". $TotalDisapproved_Items 						." </b></td>
								<td $c style='font-size: 12px;padding: 0;'> <b>". $TotalPublished_Items 					." </b></td>
								<td $c style='font-size: 12px;padding: 0;'> <b>". $TotalmyViews 							." </b></td>
								<td $c style='text-align:center;padding: 0;'>  </td>";
					$table .= "</tr>";
				}
			
			 }
			}
		}
		$table .= "</table>";
		
		$data['table'] = $table;
		
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
	
	function generateCSVFile($view='',$sql='',$fileName='')
	{
		$query = $this->db->query($sql);
		$new_report = $this->csv_from_result($query,",","\n");
		write_file(getcwd()."/files/csv/$fileName",$new_report);
	}
	
	function price_range_summary_details($cID='',$pstatusID='',$POSM_StatusID='',$fldName='',$fldVal='',$priceRangeID='',$DateFrom='',$DateTo='')
	{	
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(76,'REVIEW');
		$csv = "";
		//USER MANUAL
		$data['FORMcID'] 		  = $cID;
		$data['FORMfldName'] 	  = $fldName;
		$data['FORMfldVal'] 	  = $fldVal;
		$data['FORMpriceRangeID'] = $priceRangeID;
		$data['POSM_StatusID'] 	 = $POSM_StatusID;
		$data['USER_MANUAL'] = $this->modules->user_manual(43);
		
		$table						= 'item_summary_details';
		$data['vfile']				= 'price_range_summary_details.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report2/price_range_summary> Price Category Summary </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= "<a href='".HTTP_PATH."report2/price_range_summary_details/$cID/pstatusID/$POSM_StatusID/$fldName/$fldVal/$priceRangeID/$DateFrom/$DateTo'> Price Category Summary In Details </a>";
		
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
		$extra_label=""; $cond3="";
		$fValue=($fldVal==0) ? "Uncategorized" : $this->fieldSwitcher('fldValue',$fldName,$fldVal);
		$sql = $this->db->query("SELECT extra_label, cond1 as icond1, min_val as imin_val, logical_operator as ilogical_operator, cond2 as icond2, max_val as imax_val FROM price_range WHERE id = $priceRangeID LIMIT 0,1");
		$row = $sql->row();
		if(!isset($row->extra_label)) $extra_label = "Uncategorized";
		else						  $extra_label = $row->extra_label;
		
		//GENERATE CONDITION
		if(isset($row->extra_label)){
		 $icond1	  		= $row->icond1;
		 $imin_val 		    = $row->imin_val;
		 $ilogical_operator = $row->ilogical_operator;
		 $icond2 		    = $row->icond2;
		 $imax_val 		    = $row->imax_val;
		
		 if($row->icond1=="==") $icond1="=";
		 if($row->icond2=="==") $icond2="=";
		 if($ilogical_operator!="" AND $icond2!="" AND $imax_val!="")$cond3 = "OR (USD_Price $icond1 $imin_val $ilogical_operator USD_Price $icond2 $imax_val)";
		 else														 $cond3 = "OR (USD_Price $icond1 $imin_val)";
		}else{
		 $cond3 = "OR (USD_Price = 0)";
		}
		
		$sql1 = $this->db->query("SELECT statusName FROM POSM_Status WHERE id = $POSM_StatusID LIMIT 0,1");
		$row1 = $sql1->row();
		
		$data['fldName'] = " Item Status:  ". $row1->statusName ." | ".$this->fieldSwitcher('fldName',$fldName)." $fValue | Price Category: ".$extra_label."  ";		
		
		//COUNTRY ID
		$WHERE="WHERE pstatusID = '$POSM_StatusID' AND $fldName = '$fldVal' AND cID != '0' AND forArchiving='n' AND forPurging='n' AND (itemAge <".$this->modules->itemsAge().") AND (price_rangeID = $priceRangeID ) AND";
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
			if(($opt1=='publish' OR $opt1=='disapproved') AND $val1!='')
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
			if(($opt2=='publish' OR $opt2=='disapproved') AND $val2!='')
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
				
		$sql_csv = $this->db->query("SELECT cName as Country, num_views as Views, likes as Likes, wants as Wants, itemCode as Item_Code, itemName as Item_Name, pstatus as Status, ptype as Type, poutlet_status as Outlet_Status, 
									 ppremium_type as Premium_Type, pmaterial as Material_Type, pbrand as Brand, full_name as User, publish as Publish, disapprove as Disapproved, UnitPrice, USD_Price, price_rangeName as Price_Category, dUploaded as Date_Uploaded, dReleased as Date_Released
									 FROM item_db_reports		 
									 $WHERE ORDER BY $ORDER");
		$all_items = "";
		//generate csv file
		$csv  = "Price Range Summary in Details\n";
		$csv .= "No, Country, Views, Likes, Wants, Item Code,  Item Name, Status, Type, Outlet Status, Premium Type, Material Type, Brand, User, Publish, Disapprove, UnitPrice, USD Price, Price Category, Date Uploaded, Date Released\n";
		$sql_csv = $sql_csv->result_array(); $x=0;
		foreach($sql_csv as $s)
		{ extract($s); $x++;
		  $Publish 	  	 = ($Publish=='y')    ? "Yes" : "No";
		  $Disapproved 	 = ($Disapproved=='y') ? "Yes" : "No";
		  $Date_Uploaded =  $this->convertDate('date',"$Date_Uploaded 00:00:00");
		  $Date_Released =  $this->convertDate('date',"$Date_Released 00:00:00");
		  $Item_Name     =  str_replace(",","-",$Item_Name);
		  $csv .= "$x, $Country, $Views, $Likes, $Wants, $Item_Code, $Item_Name, $Status, $Type, $Outlet_Status, $Premium_Type, $Material_Type, $Brand, $User, $Publish, $Disapproved, $UnitPrice, $USD_Price, $Price_Category, $Date_Uploaded, $Date_Released\n";
		}
		write_file(getcwd()."/files/csv/Price_Category_Summary_of_Items_in_details".$this->reportCode().".csv",$csv);
		$data['csvFile']			= "Price_Category_Summary_of_Items_in_details".$this->reportCode().".csv";
		

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
					<th style='width:69px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"21-D\")'>          <b>Disapproved  	  </b></th>  
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
				  <td $c style='text-align:center;'>				        ". $this->convertDate('date',"$dUploaded 00:00:00") ."										</td>
				  <td $c style='text-align:center;'>				        ". $this->convertDate('date',"$dReleased 00:00:00") ."										</td>
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
	
	function fieldSwitcher($returnType='',$fldCode='',$fldID='')
	{
		$fName="";
		$fValue="";
		$fieldList = array(
					 array('fCode'=>'price_rangeID', 	  'fldName'=>'Price Category:','q'=>"SELECT extra_label as name 	FROM price_range  	 WHERE id = $fldID"),
					 array('fCode'=>'pStatusID', 		  'fldName'=>'Item Status:',   'q'=>"SELECT statusName as name 		FROM POSM_Status  	 WHERE id = $fldID"),
					 array('fCode'=>'ptypeID',   		  'fldName'=>'Item Type:',     'q'=>"SELECT typeName   as name	    FROM POSM_Type    	 WHERE id = $fldID"),
					 array('fCode'=>'poutlet_statusID',   'fldName'=>'Outlet Type:',   'q'=>"SELECT statusName as name	    FROM OUTLET_Status 	 WHERE id = $fldID"),
					 array('fCode'=>'ppremium_typeID',    'fldName'=>'Premium Type:',  'q'=>"SELECT premiumTypeName as name FROM premiumItemType WHERE id = $fldID"),
					 array('fCode'=>'pmaterialID', 	      'fldName'=>'Material Type',  'q'=>"SELECT materialName  as name   FROM MATERIAL_Type 	 WHERE id = $fldID"),
					 array('fCode'=>'pbrandID',           'fldName'=>'Brand:',  	   'q'=>"SELECT brandName     as name   FROM brands 		 WHERE id = $fldID"));
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
		  $label	 	 = "Disapproved";
		  $label_symbol	 = "Disapproved &#x25B2;";
		 break;
		 case '21-D':
		  $query = " disapprove DESC"; 
		  $Orig_code = "21-D";
		  $Rev_code  = "21-A";
		  $label	 	 = "Disapproved";
		  $label_symbol	 = "Disapproved &#x25BC;";
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
	
	function reportCode()
	{
		$encryptID = $this->encode_base64($_SESSION['user_id']);
		return "-".date('Y-m-d')."-$encryptID";
	}
	
	function encode_base64($sData){
		$sBase64 = base64_encode($sData);
		return str_replace('=', '', strtr($sBase64, '+/', '-_'));
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
	
	function campaign_summary($POSMTypeID='',$DateFrom='',$DateTo='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(76,'REVIEW');
		//USER MANUAL
	    $data['USER_MANUAL'] = $this->modules->user_manual(43);
		
		//DETECT COUNTRY
		$cond = "";
		$WHERE = "WHERE campaignType='iLike' AND";
		$HAVING="";
		$data['cID']=0;
		$data['sa']=TRUE;
		if($_SESSION['super_admin']!='y' AND $_SESSION['countryID']!=0){
			$WHERE 	.= " countryID = ".$_SESSION['countryID']." AND ";
		}
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'report>Analytics </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'report2/price_range_summary> Price Category - Summary </a>';
		
		$table						= 'item_division';
		$data['vfile']				= 'campaign_summary.php';
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
			$WHERE   		 .= " (campaignDate >= '$DateFrom' AND campaignDate <= '$DateTo') AND";
			$data['DateFrom'] = $DateFrom;
			$data['DateTo']   = $DateTo;
		}else{
			$WHERE .= " campaignName IN( SELECT MAX(campaignName) FROM campaign_summary 
										WHERE countryID IN (
										SELECT DISTINCT(countryID) FROM campaign_summary) GROUP BY campaignName) ";
			$data['DateFrom']   = "";
			$data['DateTo']     = "";
		}
		
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
			
			//COUNTRY NAME
			if(($opt1=='countryName' OR $opt1=='campaignName' OR $opt1=='extra_label' OR $opt1=='uploaded_items' OR $opt1=='winning_items') AND $val1!='')
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
			if(($opt2=='countryName' OR $opt2=='campaignName' OR $opt2=='extra_label' OR $opt2=='uploaded_items' OR $opt2=='winning_items') AND $val2!='')
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
			
			$cond = ($cond=="") ? "" : " $cond ";
			$WHERE  .=  " $cond ";
		}
		
		//IF THEIR NO POSSIBLE RESULT
		$valid_query=TRUE;
		if(isset($Submit)){
			if(($cond=="" AND $HAVING=="") AND ($val1!='' OR $val2!='')){ 
			$valid_query=FALSE;
			$WHERE = " WHERE countryID=-1";
			}
		}
		
		if($WHERE=="WHERE ") $WHERE="";		
		
		$data['POST']      = $_POST;
		if(isset($Reset)) $data['POST'] = array();
		
		echo "SELECT DISTINCT(campaignName) as cName FROM campaign_summary $WHERE";
		$campaigns = $this->db->query("SELECT DISTINCT(campaignName) as cName,campaignID FROM campaign_summary $WHERE");
		$campaigns = $campaigns->result_array();
		//ITEM TYPE 
		$sql 		= $this->db->query("SELECT DISTINCT(typeName) AS iType FROM campaign_summary $WHERE");
		$item_types = $sql->result_array();
		//ITEM TYPE 
		$sql 		 = $this->db->query("SELECT DISTINCT(extra_label) AS eLabel, price_rangeID FROM campaign_summary $WHERE");
		$item_levels = $sql->result_array();
		$table     = "<table>";
		//HEADER
		$countries=""; $cName=""; $levels=""; $eLabel="";
		$header = "<td>Campaign:</td>";
		foreach($campaigns as $campaign)
		{ extract($campaign);
		  $header .= "<td colspan='2'> $cName </td>";
		}
		
		
		foreach($item_types as $i)
		{ extract($i);
		  $levels .= "<tr><td> $iType </td>";
		  //ADD TYPE OF ITEM
			foreach($campaigns as $c)
			{ extract($c);
				$items = $this->db->query("SELECT uploaded_items, winning_items FROM campaign_summary WHERE campaignName='$cName' AND typeName='$iType' AND extra_label='$eLabel'");
				$items = $items->row();
				$levels .="<td>Uploaded</td><td>Win</td>";
			}
			$levels .="</tr>";
			
		  foreach($item_levels as $l)
		  { extract($l);
		    $sum_uploaded=0; $sum_winning_items=0;
			$levels .="<tr><td>$eLabel</td>";
			foreach($campaigns as $c)
			{ extract($c);
				$items = $this->db->query("SELECT uploaded_items, winning_items FROM campaign_summary WHERE campaignName='$cName' AND typeName='$iType' AND extra_label='$eLabel'");
				$items = $items->row();
				$levels .="<td> <a href=".HTTP_PATH."report2/campaign_summary_items/$campaignID/$price_rangeID>".$items->uploaded_items."</a></td>
						   <td>".$items->winning_items."</td>";
				$sum_uploaded += $items->uploaded_items; $sum_winning_items += $items->winning_items;
			}
			$levels .="</tr>";
		  }
		  $levels .= "<tr> <td>Total</td>";
		   foreach($campaigns as $c)
			 {  extract($c);
				$items = $this->db->query("SELECT sum(uploaded_items) as sum_uploaded, sum(winning_items) as sum_winning FROM campaign_summary WHERE campaignName='$cName' AND typeName='$iType'");
				$items = $items->row();
				$levels .=" <td> ".$items->sum_uploaded."</td><td> ".$items->sum_winning."</td>";
			 }
		 $levels .= "</tr>";
		}
		
		
		$table		  .= "<tr> $header </tr>";
		$table		  .= $levels;
		$table 		  .= "</table>";
		$data['table'] = $table;
		
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
	
}