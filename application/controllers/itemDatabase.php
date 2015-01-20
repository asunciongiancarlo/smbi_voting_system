<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class ItemDatabase extends CI_Controller {
	
	var $filter='';
	
	public function __construct()
    {  
		parent::__construct();
		date_default_timezone_set('UTC');
		session_start();
		$this->load->model('c3model');
		$this->load->library('security');
		$this->load->library('email');
		$this->load->library('modules');
		$this->load->library('image_lib');
		$this->load->library('smtp');
		$this->load->helper('url');

		$this->load->library('forms');
		$this->load->library('fv');
		
		$this->output->enable_profiler(FALSE);
		$this->modules->session_handler();
		error_reporting(0);
	}
	   
    public function index()
	{	
	  
	   $this->modules->module_checker(17,'REVIEW');
	   
	   $data['vfile']		        = 'itemDatabase.php';
	   $data['title']		        = 'San Miguel Brewing International';
	   $data['page_title']	        = 'ITEM DATABASE';
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
	   $data['breadCrumbs']			= '<a href='.$HTTP_PATH.'itemDatabase> Item Database </a>';
	   
	   
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
	
	function iTem_inquiry()
	{
		extract($_POST);
		
		/*ADDED AUTHENTICATION FOR EMAIL*/
		$config = Array(		
		    'protocol' => 'smtp',
		    'smtp_host' => 'ssl://smtp.googlemail.com',
		    'smtp_port' => 465,
		    'smtp_user' => 'gian.asuncion@ph.c3-interactive.com',
		    'smtp_pass' => 'G14nc4rl0',
		    'smtp_timeout' => '4',
		    'mailtype'  => 'html', 
		    'charset'   => 'iso-8859-1'
		);  
 
		$this->email->initialize($config);
		$this->email->set_newline("\r\n");
		/*ADDED AUTHENTICATION FOR EMAIL*/
		
		$sql = "SELECT * FROM admin_users WHERE id=$From_user_id";
		$sql = $this->db->query($sql);
		$from = $sql->row();
		
		$sql = "SELECT * FROM items WHERE id=$itemID";
		$sql = $this->db->query($sql);
		$item = $sql->row();
		
		$sql = "SELECT image FROM items_images WHERE id = (SELECT ID FROM items_images WHERE itemID = $itemID AND defaultStatus=1) LIMIT 0,1";
		$sql = $this->db->query($sql);
		$item_img = $sql->row();
		$item_img = isset($item_img->image) ? $item_img->image : 'blank.png';
		$img_src = getcwd() ."/img/big/".$item_img;
		
		
		$msg   = "Hi $To_full_name, <br/>From: ".$from->full_name ."<br/>";
		$msg  .= "Email Address: ".$from->email_address ."<br/>  Please review comments for the item you that have uploaded under Item Database!<br/>";
		$msg  .= "Item Code: ".$item->itemCode ."<br/>";
		$msg  .= "Item Name: ".$item->itemName ."<br/>";
		$msg  .= "Short Description: ".$item->Short_Description ."<br/>";
		$msg  .= "With this Message: <br>".$message ."<br/>";
		$msg   .= "Attached is the image of the item for your reference. <br/>";
		
		//SEND EMAIL
		$this->email->clear();
		$this->email->from('do.not.reply@smg.sanmiguel.com.ph', 'San Miguel Beer International, Item Inquiry');
		$this->email->to($To_email_address); 

		$this->email->subject('Item Inquiry');
		$this->email->message($msg);	
		$this->email->attach($img_src);
	
		
		$this->email->send();
		
		//echo $this->email->print_debugger();
	}
	
	function POSM_status($action='',$id='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		
		$this->modules->module_checker(21,'REVIEW');
		$table= "POSM_Status";
		$data['vfile']				= 'POSM_status.php';
	    $data['title']				= 'POSM Status | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
	    $data['breadCrumbs']		= '<a href='.$HTTP_PATH.'users> Admin </a>';
	    $data['breadCrumbs']	   .= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']	   .= "<a href='".$HTTP_PATH."itemDatabase/POSM_status'> POSM Status </a>";
		
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(16);
		
		//CRUD
		$data['ADD'] 	=  $this->modules->crud_checker(21,'ADD');
		$data['EDIT'] 	=  $this->modules->crud_checker(21,'EDIT');
		$data['DELETE'] =  $this->modules->crud_checker(21,'DELETE');
		
		//CSRF
		$data['csrf'] = "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>"; 
		
		
		//PAGINATION
		//PAGINATION
		//TOTAL NUMBER OF ROWS
		$data['active_page']=1;
		$sql = $this->db->query("SELECT id FROM $table");
		$sql = $sql->result_array();
		$total_rec = count($sql);

		$pagenum = 1;
		$data['page_rows'] = 15; 
		$data['last'] = ceil($total_rec/$data['page_rows']);		
		$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		
		extract($_POST);

		
	    if($action=="insert")
		{
			$this->modules->module_checker(21,'ADD');
			//INSERT FIELD
			$dbFields['statusName'] = $statusName;
			$dbFields['dateAdded']  = date('Y-m-d');
			
			//MSG
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Status has been save.');
			$res = $this->c3model->c3crud("insert",$table,$dbFields,'');
			
			//LOGS
			$sql		= "select max(id) as max_id FROM $table";
			$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
			$maxID 		= $lastID[0]['max_id'];
			$CI->rec_logs->w($maxID,$statusName,'Admin','POSM Status','add');
			
		}
		elseif($action=="deleteOneItem")
		{
			$this->modules->module_checker(21,'DELETE');
			
			$tables = array(
					  array('tbl'=>'items',
							'fld'=>'POSMStatusID'),
					  array('tbl'=>'ec_items',
							'fld'=>'POSMStatusID'),
					  array('tbl'=>'POSM_status_fields',
							'fld'=>'POSM_statusID'));
							
			if($this->modules->attr($tables,$id)==0)
			{
				//LOGS
				$sql = "SELECT statusName FROM $table WHERE id = $id";
				$sql = $this->db->query($sql);
				$sql = $sql->row();
				$CI->rec_logs->w($id,$sql->statusName,'Admin','POSM Status','delete');
			
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Status has been deleted.');
				$this->c3model->c3crud('delete',$table,'',$id,'');
			}else{
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Status cannot be delete, it being use in Item Database and eCatalogue Items.');
			}
		}
		elseif($action=="edit")
		{
			$this->modules->module_checker(21,'EDIT');
			$data['id'] = $id;
		}
		elseif($action=="update")
		{
			$this->modules->module_checker(21,'EDIT');
			$dbFields['statusName'] 	 = $statusName;
			$dbFields['dateLastEdited']  = date('Y-m-d');
			
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Status has been updated.');
			$this->c3model->c3crud("update",$table,$dbFields,$id);
			
			$CI->rec_logs->w($id,$statusName,'Admin','POSM Status','edit');
		}
		elseif($action=="deleteSelectedItem")
		{
			$this->modules->module_checker(21,'DELETE');
			
			$ctr=0;
			foreach($checkBoxVar as $cbr => $value)
			{
			 $tables = array(
					   array('tbl'=>'items',
							 'fld'=>'POSMStatusID'),
					   array('tbl'=>'ec_items',
							 'fld'=>'POSMStatusID'),
					   array('tbl'=>'POSM_status_fields',
							 'fld'=>'POSM_statusID'));
					
				if($this->modules->attr($tables,$value)!=0)
					$ctr++;
			}
			
			if($ctr==0)
			{
				foreach($checkBoxVar as $cbr => $value){
				$sql = "SELECT statusName FROM $table WHERE id = $value";
				$sql = $this->db->query($sql);
				$sql = $sql->row();
				$CI->rec_logs->w($value,$sql->statusName,'Admin','POSM Status','delete');
				
				$this->c3model->c3crud('delete',$table,'',$value,'');
				}
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Multiple Status has been deleted.');
			}else{
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Multiple Status cannot been delete, because it is being use in Item Database and eCatalogue Items.');
			}
		}
		elseif($action=="page")
		{
			$this->modules->module_checker(21,'REVIEW');
			$pagenum = $id;
			$data['active_page'] = $id; 
			$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		}
		
		//STATUS LISTS
		$sql 			= $this->db->query("SELECT * FROM $table $max");
		$data['status'] = $sql->result_array();
		
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
	
	function POSM_type($action='',$id='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	
		$this->modules->module_checker(20,'REVIEW');
		
		$table= "POSM_Type";
		$data['vfile']				= 'POSM_type.php';
	    $data['title']				= 'POSM Type | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//CRUD
		$data['ADD'] 	=  $this->modules->crud_checker(20,'ADD');
		$data['EDIT'] 	=  $this->modules->crud_checker(20,'EDIT');
		$data['DELETE'] =  $this->modules->crud_checker(20,'DELETE');
		
		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
	    $data['breadCrumbs']		= '<a href='.$HTTP_PATH.'users> Admin </a>';
	    $data['breadCrumbs']	   .= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']	   .= '<a href='.$HTTP_PATH.'itemDatabase/POSM_type> POSM Type </a>';
		
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(18);
		
		//CSRF
		$data['csrf'] = "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>"; 
		
		//PAGINATION
		//PAGINATION
		//TOTAL NUMBER OF ROWS
		$data['active_page']=1;
		$sql = $this->db->query("SELECT id FROM $table");
		$sql = $sql->result_array();
		$total_rec = count($sql);

		$pagenum = 1;
		$data['page_rows'] = 15; 
		$data['last'] = ceil($total_rec/$data['page_rows']);		
		$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		
		extract($_POST);

		
	    if($action=="insert")
		{
			$this->modules->module_checker(20,'ADD');
			//INSERT FIELD
			$dbFields['typeName']  = $typeName;
			$dbFields['dateAdded'] = date('Y-m-d');
			
			//MSG
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Item Type has been save.');
			$res = $this->c3model->c3crud("insert",$table,$dbFields,'');
			
			//LOGS
			$sql		= "select max(id) as max_id FROM $table";
			$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
			$maxID 		= $lastID[0]['max_id'];
			$CI->rec_logs->w($maxID,$typeName,'Admin','POSM Type','add');
		}
		elseif($action=="deleteOneItem")
		{
			$this->modules->module_checker(20,'DELETE');
			
			$tables = array(
					  array('tbl'=>'items',
							'fld'=>'POSMTypeID'),
					  array('tbl'=>'ec_items',
							'fld'=>'POSMTypeID'),
					  array('tbl'=>'ecitemType_POSM_table_fields',
							'fld'=>'POSM_TypeID'),
					  array('tbl'=>'itemType_POSM_table_fields',
							'fld'=>'POSM_TypeID'),
					  array('tbl'=>'featured_items',
							'fld'=>'item_typeID'));
							
			if($this->modules->attr($tables,$id)==0)
			{
				//LOGS
				$sql = "SELECT typeName FROM $table WHERE id = $id";
				$sql = $this->db->query($sql);
				$sql = $sql->row();
				$CI->rec_logs->w($id,$sql->typeName,'Admin','POSM Type','delete');
				
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Item Type has been deleted.');
				$this->c3model->c3crud('delete',$table,'',$id,'');
			}else{
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Item Type cannot be delete, because it is being use in Item Database and eCatalogue Items.');
			}
		}
		elseif($action=="edit")
		{
			$this->modules->module_checker(20,'EDIT');
			$data['id'] = $id;
		}
		elseif($action=="update")
		{
			$this->modules->module_checker(20,'UPDATE');
			$dbFields['typeName'] 		= $typeName;
			$dbFields['dateLastEdited'] = date('Y-m-d');
			
			//LOGS
			$CI->rec_logs->w($id,$typeName,'Admin','POSM Type','edit');
			
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Item Type has been updated.');
			$this->c3model->c3crud("update",$table,$dbFields,$id);
		}
		elseif($action=="deleteSelectedItem")
		{
			$this->modules->module_checker(20,'DELETE');
			
			$ctr=0;
			foreach($checkBoxVar as $cbr => $value)
			{
			  	$tables = array(
					  array('tbl'=>'items',
							'fld'=>'POSMTypeID'),
					  array('tbl'=>'ec_items',
							'fld'=>'POSMTypeID'),
					  array('tbl'=>'ecitemType_POSM_table_fields',
							'fld'=>'POSM_TypeID'),
					  array('tbl'=>'itemType_POSM_table_fields',
							'fld'=>'POSM_TypeID'),
					  array('tbl'=>'featured_items',
							'fld'=>'item_typeID'));
							
				if($this->modules->attr($tables,$value)!=0)
					$ctr++;
			}
			
			if($ctr==0)
			{
				foreach($checkBoxVar as $cbr => $value){
				//LOGS
				$sql = "SELECT typeName FROM $table WHERE id = $value";
				$sql = $this->db->query($sql);
				$sql = $sql->row();
				$CI->rec_logs->w($value,$sql->typeName,'Admin','POSM Type','delete');
			
				$this->c3model->c3crud('delete',$table,'',$value,'');
				}
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Multiple Item Type has been deleted.');
			}else{
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Multiple Item Type cannot been delete, because it is being use in Item Database and eCatalogue Items.');
			}
		}
		elseif($action=="page")
		{
			$this->modules->module_checker(20,'REVIEW');
			$pagenum = $id;
			$data['active_page'] = $id; 
			$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		}
		
		//STATUS LISTS
		$sql 			= $this->db->query("SELECT * FROM $table $max");
		$data['status'] = $sql->result_array();
		

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
	
	function OUTLET_Status($action='',$id='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		
		$this->modules->module_checker(22,'REVIEW');
		
		$table= "OUTLET_Status";
		$data['vfile']				= 'OUTLET_status.php';
	    $data['title']				= 'OUTLET Status | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
	    $data['breadCrumbs']		= '<a href='.$HTTP_PATH.'users> Admin </a>';
	    $data['breadCrumbs']	   .= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']	   .= '<a href='.$HTTP_PATH.'itemDatabase/OUTLET_Status> Outlet Type </a>';
	    
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(19);
		
		//CRUD
		$data['ADD'] 	=  $this->modules->crud_checker(22,'ADD');
		$data['EDIT'] 	=  $this->modules->crud_checker(22,'EDIT');
		$data['DELETE'] =  $this->modules->crud_checker(22,'DELETE');
		
		//CSRF
		$data['csrf'] = "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>"; 
		
		//PAGINATION
		//PAGINATION
		//TOTAL NUMBER OF ROWS
		$data['active_page']=1;
		$sql = $this->db->query("SELECT id FROM $table");
		$sql = $sql->result_array();
		$total_rec = count($sql);
		
		$pagenum = 1;
		$data['page_rows'] = 15; 
		$data['last'] = ceil($total_rec/$data['page_rows']);		
		$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		
		extract($_POST);

		
	    if($action=="insert")
		{
			$this->modules->module_checker(22,'ADD');
			//INSERT FIELD
			$dbFields['statusName'] = $statusName;
			$dbFields['dateAdded']  = date('Y-m-d');
			
			//LOGS
			$sql		= "select max(id) as max_id FROM $table";
			$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
			$maxID 		= $lastID[0]['max_id'];
			$CI->rec_logs->w($maxID,$statusName,'Admin','OUTLET Status','add');
			
			//MSG
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Type of Outlet has been save.');
			$res = $this->c3model->c3crud("insert",$table,$dbFields,'');
		}
		elseif($action=="deleteOneItem")
		{
			$this->modules->module_checker(22,'DELETE');
			$tables = array(
					  array('tbl'=>'items',
							'fld'=>'OUTLETStatusID'),
					  array('tbl'=>'ec_items',
							'fld'=>'OUTLETStatusID'));
							
			if($this->modules->attr($tables,$id)==0)
			{
				//LOGS
				$sql = "SELECT statusName FROM $table WHERE id = $id";
				$sql = $this->db->query($sql);
				$sql = $sql->row();
				$CI->rec_logs->w($id,$sql->statusName,'Admin','OUTLET Status','delete');
				
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Type of Outlet has been deleted.');
				$this->c3model->c3crud('delete',$table,'',$id,'');
			}else{
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Type of Outlet cannot be delete, because it is being use in Item Database and eCatalogue Items.');
			}
		}
		elseif($action=="edit")
		{
			$this->modules->module_checker(22,'EDIT');
			$data['id'] = $id;
		}
		elseif($action=="update")
		{
			$this->modules->module_checker(22,'EDIT');
			$dbFields['statusName'] 	= $statusName;
			$dbFields['dateLastEdited'] = date('Y-m-d');
			
			$CI->rec_logs->w($id,$statusName,'Admin','OUTLET Status','edit');
			
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Type of Outlet has been updated.');
			$this->c3model->c3crud("update",$table,$dbFields,$id);
		}
		elseif($action=="deleteSelectedItem")
		{
			$this->modules->module_checker(22,'DELETE');
			
			$ctr=0;
			foreach($checkBoxVar as $cbr => $value)
			{
			  $tables = array(
					    array('tbl'=>'items',
							'fld'=>'OUTLETStatusID'),
					    array('tbl'=>'ec_items',
							'fld'=>'OUTLETStatusID'));	
				if($this->modules->attr($tables,$value)!=0)
					$ctr++;
			}
			
			if($ctr==0)
			{
				foreach($checkBoxVar as $cbr => $value){
				//LOGS
				$sql = "SELECT statusName FROM $table WHERE id = $value";
				$sql = $this->db->query($sql);
				$sql = $sql->row();
				$CI->rec_logs->w($value,$sql->statusName,'Admin','OUTLET Status','delete');
			
				$this->c3model->c3crud('delete',$table,'',$value,'');
				}
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Multiple Type of Outlet has been deleted.');
			}else{
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Multiple Type of Outlet cannot been delete, because it is being use in Item Database and eCatalogue Items.');
			}
			
		}
		elseif($action=="page")
		{
			$this->modules->module_checker(22,'REVIEW');
			$pagenum = $id;
			$data['active_page'] = $id; 
			$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		}
		
		//STATUS LISTS
		$sql 			= $this->db->query("SELECT * FROM $table $max");
		$data['status'] = $sql->result_array();
		
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
	
	function MATERIAL_Type($action='',$id='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	
		$this->modules->module_checker(23,'REVIEW');
		$table= "MATERIAL_Type";
		$data['vfile']				= 'MATERIAL_type.php';
	    $data['title']				= 'MATERIAL Type | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
	    $data['breadCrumbs']		= '<a href='.$HTTP_PATH.'users> Users </a>';
	    $data['breadCrumbs']	   .= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']	   .= '<a href='.$HTTP_PATH.'itemDatabase/MATERIAL_Type> Material Type </a>';
	    
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(18);
		
		//CRUD
		$data['ADD'] 	=  $this->modules->crud_checker(23,'ADD');
		$data['EDIT'] 	=  $this->modules->crud_checker(23,'EDIT');
		$data['DELETE'] =  $this->modules->crud_checker(23,'DELETE');
		
		//CSRF
		$data['csrf'] = "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>"; 
		
		//PAGINATION
		//TOTAL NUMBER OF ROWS
		$data['active_page']=1;
		$sql = $this->db->query("SELECT id FROM $table");
		$sql = $sql->result_array();
		$total_rec = count($sql);

		$pagenum = 1;
		$data['page_rows'] = 15; 
		$data['last'] = ceil($total_rec/$data['page_rows']);		
		$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		
		extract($_POST);

		
	    if($action=="insert")
		{
			$this->modules->module_checker(23,'ADD');
			//INSERT FIELD
			$dbFields['materialName'] = $materialName;
			$dbFields['dateAdded'] 	  = date('Y-m-d');
			
			//LOGS
			$sql		= "select max(id) as max_id FROM $table";
			$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
			$maxID 		= $lastID[0]['max_id'];
			$CI->rec_logs->w($maxID,$materialName,'Admin','Material Type','add');
			
			//MSG
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Type of Outlet has been save.');
			$res = $this->c3model->c3crud("insert",$table,$dbFields,'');
		}
		elseif($action=="deleteOneItem")
		{
			$this->modules->module_checker(23,'DELETE');
			
			$tables = array(
			  array('tbl'=>'items',
					'fld'=>'MaterialTypeID'),
			  array('tbl'=>'ec_items',
					'fld'=>'MaterialTypeID'));
							
			if($this->modules->attr($tables,$id)==0)
			{
				//LOGS
				$sql = "SELECT materialName FROM $table WHERE id = $id";
				$sql = $this->db->query($sql);
				$sql = $sql->row();
				$CI->rec_logs->w($id,$sql->materialName,'Admin','Material Type','delete');
				
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Material Type has been deleted.');
				$this->c3model->c3crud('delete',$table,'',$id,'');
			}else{
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Material Type cannot be delete, because it is being use in Item Database and eCatalogue Items.');
			}
			
		}
		elseif($action=="edit")
		{
			$this->modules->module_checker(23,'EDIT');
			$data['id'] = $id;
		}
		elseif($action=="update")
		{
			$this->modules->module_checker(23,'EDIT');
			$dbFields['materialName'] 	= $materialName;
			$dbFields['dateLastEdited'] = date('Y-m-d');
			
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Type of Outlet has been updated.');
			$this->c3model->c3crud("update",$table,$dbFields,$id);
			
			$CI->rec_logs->w($id,$materialName,'Admin','Material Type','edit');
		}
		elseif($action=="deleteSelectedItem")
		{
			$this->modules->module_checker(23,'DELETE');
			
			$ctr=0;
			foreach($checkBoxVar as $cbr => $value)
			{
			 $tables = array(
			  array('tbl'=>'items',
					'fld'=>'MaterialTypeID'),
			  array('tbl'=>'ec_items',
					'fld'=>'MaterialTypeID'));
					
				if($this->modules->attr($tables,$value)!=0)
					$ctr++;
			}
			
			if($ctr==0)
			{
				foreach($checkBoxVar as $cbr => $value){
				//LOGS
				$sql = "SELECT materialName FROM $table WHERE id = $value";
				$sql = $this->db->query($sql);
				$sql = $sql->row();
				$CI->rec_logs->w($value,$sql->materialName,'Admin','Material Type','delete');
				$this->c3model->c3crud('delete',$table,'',$value,'');
				}
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Multiple Material Type has been deleted.');
			}else{
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Multiple Material Type cannot been delete, because it is being use in Item Database and eCatalogue Items.');
			}
			
		}
		elseif($action=="page")
		{
			$this->modules->module_checker(23,'REVIEW');
			$pagenum = $id;
			$data['active_page'] = $id; 
			$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		}
		
		//STATUS LISTS
		$sql 			= $this->db->query("SELECT * FROM $table $max");
		$data['status'] = $sql->result_array();
		
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
	
	function Country($action='',$id='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	
		$this->modules->module_checker(24,'REVIEW');
		
		$table= "country";
		$data['vfile']				= 'country.php';
	    $data['title']				= 'Country | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
	    $data['breadCrumbs']		= '<a href='.$HTTP_PATH.'users> Users </a>';
	    $data['breadCrumbs']	   .= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']	   .= '<a href='.$HTTP_PATH.'itemDatabase/country> Country </a>';
	    
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(24);
		
		//CRUD
		$data['ADD'] 	=  $this->modules->crud_checker(24,'ADD');
		$data['EDIT'] 	=  $this->modules->crud_checker(24,'EDIT');
		$data['DELETE'] =  $this->modules->crud_checker(24,'DELETE');
		
		//CSRF
		$data['csrf'] = "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>"; 
		
		//PAGINATION
		//PAGINATION
		//TOTAL NUMBER OF ROWS
		$data['active_page']=1;
		$sql = $this->db->query("SELECT id FROM $table");
		$sql = $sql->result_array();
		$total_rec = count($sql);

		$pagenum = 1;
		$data['page_rows'] = 15; 
		$data['last'] = ceil($total_rec/$data['page_rows']);		
		$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		
		extract($_POST);

		
	    if($action=="insert")
		{
			$this->modules->module_checker(24,'ADD');
			//INSERT FIELD
			$dbFields['countryName'] = $country;
			$dbFields['countryCode'] = $countryCode;
			$dbFields['time_zone'] = $time_zone;
			$dbFields['dateAdded']   = date('Y-m-d');
			
			//LOGS
			$sql		= "select max(id) as max_id FROM $table";
			$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
			$maxID 		= $lastID[0]['max_id'];
			$CI->rec_logs->w($maxID,$country,'Admin','BU Country','add');
			
			//MSG
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Country has been save.');
			$res = $this->c3model->c3crud("insert",$table,$dbFields,'');
		}
		elseif($action=="deleteOneItem")
		{
			$this->modules->module_checker(24,'DELETE');
			
			$tables = array(
			  array('tbl'=>'items',
					'fld'=>'countryID'),
			  array('tbl'=>'ec_items',
					'fld'=>'countryID'),
			  array('tbl'=>'admin_users',
					'fld'=>'countryID'),
			  array('tbl'=>'campaign',
					'fld'=>'countryID'),
			  array('tbl'=>'logs',
					'fld'=>'country_id'),
			  array('tbl'=>'vendors',
					'fld'=>'countryID'));
							
			if($this->modules->attr($tables,$id)==0)
			{
				//LOGS
				$sql = "SELECT countryName FROM $table WHERE id = $id";
				$sql = $this->db->query($sql);
				$sql = $sql->row();
				$CI->rec_logs->w($id,$sql->countryName,'Admin','BU Country','delete');
				
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Country has been deleted.');
				$this->c3model->c3crud('delete',$table,'',$id,'');
			}else{
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Country cannot be delete, because it is being use in Item Database and eCatalogue Items.');
			}
		}
		elseif($action=="edit")
		{
			$this->modules->module_checker(23,'EDIT');
			$data['id'] = $id;
		}
		elseif($action=="update")
		{
			$this->modules->module_checker(23,'EDIT');
			$dbFields['countryName'] = $country;
			$dbFields['countryCode'] = $countryCode;
			$dbFields['time_zone'] = $time_zone;
			$dbFields['dateAdded']   = date('Y-m-d');
		
			$CI->rec_logs->w($id,$country,'Admin','BU Country','edit');
			
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Country has been updated.');
			$this->c3model->c3crud("update",$table,$dbFields,$id);
		}
		elseif($action=="deleteSelectedItem")
		{
			$this->modules->module_checker(23,'DELETE');
			
			$ctr=0;
			foreach($checkBoxVar as $cbr => $value)
			{
		
			$tables = array(
			  array('tbl'=>'items',
					'fld'=>'countryID'),
			  array('tbl'=>'ec_items',
					'fld'=>'countryID'),
			  array('tbl'=>'admin_users',
					'fld'=>'countryID'),
			  array('tbl'=>'campaign',
					'fld'=>'countryID'),
			  array('tbl'=>'logs',
					'fld'=>'country_id'),
			  array('tbl'=>'vendors',
					'fld'=>'countryID'));
					
				if($this->modules->attr($tables,$value)!=0)
					$ctr++;
			}
			
			if($ctr==0)
			{
				foreach($checkBoxVar as $cbr => $value)
				{
					//LOGS
					$sql = "SELECT countryName FROM $table WHERE id = $value";
					$sql = $this->db->query($sql);
					$sql = $sql->row();
					$CI->rec_logs->w($value,$sql->countryName,'Admin','BU Country','delete');
					
					$this->c3model->c3crud('delete',$table,'',$value,'');
				}
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Multiple Countries has been deleted.');
			}else{
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Multiple Countries cannot been delete, because it is being use in Item Database and eCatalogue Items.');
			}
			
		}
		elseif($action=="page")
		{
			$this->modules->module_checker(23,'REVIEW');
			$pagenum = $id;
			$data['active_page'] = $id; 
			$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		}
		
		
		//STATUS LISTS
		$sql 			= $this->db->query("SELECT * FROM $table $max");
		$data['status'] = $sql->result_array();
		
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
	
	function brandPerCountry($action='',$id='')
	{
		$this->modules->module_checker(19,'REVIEW');
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		
		$table= "country";
		$data['vfile']				= 'brandPerCountry.php';
	    $data['title']				= 'brandPerCountry | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
	    $data['breadCrumbs']		= '<a href='.$HTTP_PATH.'users> Users </a>';
	    $data['breadCrumbs']	   .= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']	   .= '<a href='.$HTTP_PATH.'itemDatabase/brandPerCountry> Brand Per Country </a>';
	    
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(22);
		
		//CRUD
		$data['ADD'] 	=  $this->modules->crud_checker(19,'ADD');
		$data['EDIT'] 	=  $this->modules->crud_checker(19,'EDIT');
		$data['DELETE'] =  $this->modules->crud_checker(19,'DELETE');
		
		//CSRF
		$data['csrf'] = "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>"; 
		
		//PAGINATION
		//PAGINATION
		//TOTAL NUMBER OF ROWS
		$data['active_page']=1;
		$sql = $this->db->query("SELECT id FROM $table");
		$sql = $sql->result_array();
		$total_rec = count($sql);

		$pagenum = 1;
		$data['page_rows'] = 15; 
		$data['last'] = ceil($total_rec/$data['page_rows']);		
		$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		
		extract($_POST);

		
	    if($action=="insert")
		{
			$this->modules->module_checker(19,'ADD');
			//INSERT FIELD
			$sql = $this->db->query("select * from brandXref where brandID='$selBrand' and countryID='$selCountry' ");
			if($sql->num_rows()==0)
			{
			 $dbFields['countryID'] = $selCountry;
			 $dbFields['brandID']   = $selBrand;
			 $res = $this->c3model->c3crud("insert",'brandXref',$dbFields,'');
			 
			//LOGS
			$sql		= "select max(id) as max_id FROM brandXref";
			$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
			$maxID 		= $lastID[0]['max_id'];
			
			//COUNTRY
			$sql = "SELECT countryName FROM country WHERE id = $selCountry";
			$sql = $this->db->query($sql);
			$sql = $sql->row();
			
			$sql1 = "SELECT brandName FROM brands WHERE id = $selBrand";
			$sql1 = $this->db->query($sql1);
			$sql1 = $sql1->row();
			
			$CI->rec_logs->w($maxID,$sql->countryName ."-". $sql1->brandName ,'Admin','Brand per Country','add');
			 
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Country has been save.');
			}else
			 $data['msg'] = array('msg_type'=>'alert-info','msg_desc'=>'Record already exist.');
			
		}
		elseif($action=="deleteOneItem")
		{
			$this->modules->module_checker(19,'DELETE');
			
			//LOGS
			$sql		= "select brandID, countryID FROM brandXref WHERE id = $id";
			$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
			
			//COUNTRY
			$sql = "SELECT id, countryName FROM country WHERE id = ".$lastID[0]['countryID'] ;
			$sql = $this->db->query($sql);
			$sql = $sql->row();
			
			$sql1 = "SELECT id, brandName FROM brands WHERE id = ".$lastID[0]['brandID'];
			$sql1 = $this->db->query($sql1);
			$sql1 = $sql1->row();
			
			$sql2 = $this->db->query("select * from items where brandID='".$sql1->id."' and countryID='".$sql->id."' ");
			if($sql2->num_rows()==0)
			{
				$CI->rec_logs->w($id,$sql->countryName ."-". $sql1->brandName ,'Admin','Brand per Country','add');
				
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Brand per Country has been deleted.');
				$this->c3model->c3crud('delete','brandXref','',$id,'');
			}else{
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Brand per Country cannot delete, beacause it is being use in Item Database and eCatalogue Items.');
			}
		}
		elseif($action=="addItem")
		{
			$data['countryID'] = $id;
			$data['vfile']				= 'brandPerCountry.php';
		}
		elseif($action=="page")
		{
			$this->modules->module_checker(19,'REVIEW');
			$pagenum = $id;
			$data['active_page'] = $id; 
			$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		}
		
		//STATUS LISTS
		$sql 			= $this->db->query("SELECT * FROM country");
		$data['status'] = $sql->result_array();
		$sql 			= $this->db->query("SELECT * FROM brands order by brandName asc");
		$data['brand']  = $sql->result_array();
		$sql 			= $this->db->query("select ref.id,c.countryName, b.brandName from brandXref as ref left join country as c on c.id =ref.countryID left join brands as b   on b.id = ref.brandID order by c.countryName asc , b.brandName asc ");
		$data['brandxref']  = $sql->result_array();
		
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
	
	function PremiumItemType($action='',$id='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		
		$this->modules->module_checker(25,'REVIEW');
		$table= "premiumItemType";
		$data['vfile']				= 'premiumItemType.php';
	    $data['title']				= 'Premium Item Type | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
	    $data['breadCrumbs']		= '<a href='.$HTTP_PATH.'users> Users </a>';
	    $data['breadCrumbs']	   .= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']	   .= '<a href='.$HTTP_PATH.'itemDatabase/PremiumItemType> Premium Item Type </a>';
	    
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(21);
		
		//CRUD
		$data['ADD'] 	=  $this->modules->crud_checker(25,'ADD');
		$data['EDIT'] 	=  $this->modules->crud_checker(25,'EDIT');
		$data['DELETE'] =  $this->modules->crud_checker(25,'DELETE');
		
		//CSRF
		$data['csrf'] = "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>"; 
		
		extract($_POST);	
	    if($action=="insert")
		{
			$this->modules->module_checker(25,'ADD');
			/*REFERENCE*/
			$dbFields['premiumTypeName'] 	= $premiumTypeName;
			$dbFields['dateAdded'] 			= date('Y-m-d');
			$res = $this->c3model->c3crud("insert",$table,$dbFields,'');
			
			$sql		= "select max(id) as max_id FROM premiumItemType";
			$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
			$menu_id 	= $lastID[0]['max_id'];
			
			$refFields['parentID']=  $sel_parent;
			$refFields['childID'] =  $menu_id;
			
			//LOGS
			$sql		= "select max(id) as max_id FROM $table";
			$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
			$maxID 		= $lastID[0]['max_id'];
			$CI->rec_logs->w($maxID,$premiumTypeName,'Admin','Premium Type','add');
			
			$res = $this->c3model->c3crud("insert",'premiumItemTypeRef',$refFields,'');	

			//MSG
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Premium Item has been save.');
		}
		elseif($action=="deleteOneItem")
		{
			$this->modules->module_checker(25,'DELETE');
			
			$tables = array(
			  array('tbl'=>'items',
					'fld'=>'PremiumTypeID'),
			  array('tbl'=>'ec_items',
					'fld'=>'PremiumTypeID'));
							
			if($this->modules->attr($tables,$id)==0)
			{
				//LOGS
				$sql = "SELECT premiumTypeName FROM $table WHERE id = $id";
				$sql = $this->db->query($sql);
				$sql = $sql->row();
				$CI->rec_logs->w($id,$sql->premiumTypeName,'Admin','Premium Type','delete');
				
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Premium Item Type has been deleted.');
				$this->c3model->c3crud("no-res",'','','',"DELETE FROM premiumItemTypeRef WHERE childID='$id'");
				$this->c3model->c3crud("no-res",'','','',"DELETE FROM premiumItemType WHERE id='$id'");
			}else{
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Premium Item Type cannot be delete, because it is being use in Item Database and eCatalogue Items.');
			}
		}
		elseif($action=="edit")
		{
			$this->modules->module_checker(25,'EDIT');
			$data['id'] = $id;
		}
		elseif($action=="update")
		{
			$this->modules->module_checker(25,'UPDATE');
			$dbFields['premiumTypeName'] = $premiumTypeName;
			$dbFields['dateLastEdited']  = date('Y-m-d');
			$this->c3model->c3crud("update",$table,$dbFields,$id);
			
			//LOGS
			$sql = "SELECT premiumTypeName FROM $table WHERE id = $id";
			$sql = $this->db->query($sql);
			$sql = $sql->row();
			$CI->rec_logs->w($id,$sql->premiumTypeName,'Admin','Premium Type','edit');
			
			$crefFields['parentID']= $sel_parent;
			$crefFields['childID'] = $id;
			$res = $this->c3model->c3crud("update","premiumItemTypeRef",$crefFields ,array('childID'=>$id));
			
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Premium Item Type has been updated.');
		}

		//STATUS LISTS
		$sql 			= $this->db->query("SELECT premiumItemType.id AS premiumID, premiumItemType.premiumTypeName AS premiumName  
											FROM premiumItemTypeRef INNER JOIN premiumItemType 
											ON premiumItemType.id =  premiumItemTypeRef.childID WHERE premiumItemTypeRef.parentID='0'");
		$data['data'] = $sql->result_array();
		
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
	
	function vendors($action='',$id='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		
		$this->modules->module_checker(26,'REVIEW');
		$filter = $this->modules->country();
		
		$table= "vendors";
		$data['vfile']				= 'vendors.php';
	    $data['title']				= 'Vendors | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//CRUD
		$data['ADD'] 	=  $this->modules->crud_checker(26,'ADD');
		$data['EDIT'] 	=  $this->modules->crud_checker(26,'EDIT');
		$data['DELETE'] =  $this->modules->crud_checker(26,'DELETE');
		
		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
	    $data['breadCrumbs']		= '<a href='.$HTTP_PATH.'itemDatabase> Item Database </a>';
	    $data['breadCrumbs']	   .= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']	   .= '<a href='.$HTTP_PATH.'itemDatabase/vendors> Vendors </a>';
	    
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(6);
		
		//CSRF
		$data['csrf'] = "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>"; 
		
		
		//PAGINATION
		//TOTAL NUMBER OF ROWS
		$data['active_page']=1;
		$sql = $this->db->query("SELECT id FROM $table $filter");
		$sql = $sql->result_array();
		$total_rec = count($sql);

		$pagenum = 1;
		$data['page_rows'] = 15; 
		$data['last'] = ceil($total_rec/$data['page_rows']);		
		$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		
		extract($_POST);
	    if($action=="insert")
		{
			$this->modules->module_checker(26,'ADD');
			
			//INSERT FIELD
			$dbFields['company_name'] = $company_name; 
			$dbFields['fname'] 		  = $fname; 
			$dbFields['mname'] 		  = $mname; 
			$dbFields['lname'] 		  = $lname; 
			$dbFields['email'] 		  = $email; 
			$dbFields['telephone']    = $telephone; 
			$dbFields['countryID'] 	  = $countryID; 
			$dbFields['billing_address'] = $billing_address; 
			$dbFields['postal_code']  = $postal_code; 
			$dbFields['city_state']   = $city_state;
			$dbFields['dateAdded']        = date('Y-m-d');
			
			//MSG
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Vendor has been save.');
			$res = $this->c3model->c3crud("insert",$table,$dbFields,'');
			
			//LOGS
			$sql		= "select max(id) as max_id FROM $table";
			$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
			$maxID 		= $lastID[0]['max_id'];
			$CI->rec_logs->w($maxID,$company_name,'Item Database','Item Database Vendors',isset($duplicate) ? 'duplicate' : 'add');
		}
		elseif($action=="deleteOneItem")
		{
		  $this->modules->module_checker(26,'DELETE');
		  
		  $tables = array(
			  array('tbl'=>'itemVendorsRef',
					'fld'=>'vendorID'));
		
			if($this->modules->attr($tables,$id)==0)
			{
			//LOGS
			$sql = "SELECT company_name FROM vendors WHERE id = $id";
			$sql = $this->db->query($sql);
			$sql = $sql->row();		
			$CI->rec_logs->w($id,$sql->company_name,'Item Database','Item Database Vendors','delete');
		
			$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Vendor has been deleted.');
			$this->c3model->c3crud('delete',$table,'',$id,'');
			}else{
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Vendor cannot be delete because it is being link to an items/s.');
			}
		
		}
		elseif($action=="edit")
		{
			$this->modules->module_checker(26,'EDIT');
			
			//USER MANUAL
			$data['USER_MANUAL'] = $this->modules->user_manual(6);
			
			$data['id'] = $id;
			$data['vfile']	= 'vendorFORM.php';
		}
		elseif($action=="duplicate")
		{
			$this->modules->module_checker(26,'ADD');
			$data['id'] = $id;
			$data['duplicateID'] = $id;
			$data['vfile']	= 'vendorFORM.php';
		}
		elseif($action=="update")
		{
			$this->modules->module_checker(26,'EDIT');
			
			$dbFields['company_name'] = $company_name; 
			$dbFields['fname'] 		  = $fname; 
			$dbFields['mname'] 		  = $mname; 
			$dbFields['lname'] 		  = $lname; 
			$dbFields['email'] 		  = $email; 
			$dbFields['telephone']    = $telephone; 
			$dbFields['countryID'] 	  = $countryID; 
			$dbFields['billing_address'] = $billing_address; 
			$dbFields['postal_code']  = $postal_code; 
			$dbFields['city_state']   = $city_state;
			$dbFields['dateLastEdited']= date('Y-m-d');
			
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Vendor has been updated.');
			$this->c3model->c3crud("update",$table,$dbFields,$id);
			
			$CI->rec_logs->w($id,$company_name,'Item Database','Item Database Vendors','edit');
		}
		elseif($action=="page")
		{
			$this->modules->module_checker(26,'REVIEW');
			$pagenum = $id;
			$data['active_page'] = $id; 
			$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		}
		elseif($action=="add")
		{
			$this->modules->module_checker(26,'ADD');
			//USER MANUAL
			$data['USER_MANUAL'] = $this->modules->user_manual(6);
			$data['vfile']	= 'vendorFORM.php';
		}
		
		$sql 			= $this->db->query("SELECT *, vendors.id as vID FROM $table INNER JOIN country ON country.id = vendors.countryID $filter ORDER BY vID DESC $max");
		$data['data'] = $sql->result_array();
		
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

	function BU_Marketing_Items_review($action='',$id='',$txtsearch='',$selPOSMType='',$selPOSMStatus='',$selPremiumType='',$seloutlet='',$selCountry='',$selBrand='',$selMaterial='',$items_date='',$nviews='',$sort_by_price='',$priceRange='',$priceFrom='',$priceTo='',$year='',$month='',$priceRangeID='',$msg='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		
		$this->modules->module_checker(55,'REVIEW');
		$filter = $this->modules->country2();
		$filter_add = $this->modules->country();
		
		$table= "items";
		$data['vfile']				= 'BU_Marketing_Items_review.php';
	    $data['title']				= 'BU Marketing Items Review | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//CRUD
		$data['ADD'] 	=  $this->modules->crud_checker(18,'ADD');
		$data['EDIT'] 	=  $this->modules->crud_checker(18,'EDIT');
		$data['DELETE'] =  $this->modules->crud_checker(18,'DELETE');
		$data['APPROVE'] =  $this->modules->crud_checker(18,'APPROVE');
		$data['DISABLE_BUTTONS'] =  FALSE;
		$data['TAG_AS_POPULAR'] =  $this->modules->crud_checker(18,'POPULAR');
		$data['VENDORS_EDIT'] 	=  $this->modules->crud_checker(26,'EDIT');
		$data['SAVE_FOR_LATER'] =  $this->modules->BU_Logistics();
		
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(10);
		
		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
	    $data['breadCrumbs']		= '<a href='.$HTTP_PATH.'itemDatabase> Item Database </a>';
	    $data['breadCrumbs']	   .= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']	   .= '<a href='.$HTTP_PATH.'itemDatabase/BU_Marketing_Items_review> BU Marketing: Items Review </a>';

		//CSRF
		$data['csrf'] = "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>"; 
		$data['searchAction'] = HTTP_PATH. "itemDatabase/BU_Marketing_Items_review.html"; 
		
		
		//search filter
		$data['post'] = $_POST; $filter2="";
		$order = "items.id DESC,";
        
		$data['redirectTo']	= "BU_Marketing_Items_review";
        if(($txtsearch!='' OR $selPOSMType!='' OR $selPOSMStatus!='' OR $selPremiumType!='' OR $seloutlet!='' OR $selCountry!='' OR $selBrand!='' OR $selMaterial!='' OR $items_date=!'' OR $nviews!='' OR $sort_by_price!='' OR $priceRange!='' OR $priceRangeID!=''))		
		{ 
			extract($_POST);  
			//$_SESSION['txtsearch']="";
              if($selPOSMType!='null' 	 AND $selPOSMType!='')     	$filter2 .= " AND POSMTypeID		='$selPOSMType'";
              if($selPOSMStatus!='null'  AND $selPOSMStatus!='')   	$filter2 .= " AND POSMStatusID		='$selPOSMStatus'";
			  if($selPremiumType!='null' AND $selPremiumType!='')  	$filter2 .= " AND PremiumTypeID		='$selPremiumType'";
			  if($seloutlet!='null' 	 AND $seloutlet!='')       	$filter2 .= " AND OUTLETStatusID	='$seloutlet'";
			  if($selCountry!='null'	 AND $selCountry!='')      	$filter2 .= " AND countryID			='$selCountry'";
			  if($selBrand!='null' 		 AND $selBrand!='')        	$filter2 .= " AND brandID 			='$selBrand'";
			  if($selMaterial!='null' 	 AND $selMaterial!='')     	$filter2 .= " AND MaterialTypeID	='$selMaterial'";
			  if($priceRangeID!='null' AND $priceRangeID!='')    $filter2 .= " AND price_rangeID	    ='$priceRangeID'";
			  
			  
			  if($txtsearch!='' AND $txtsearch!='null'){
				$txtsearch = addslashes($txtsearch);
				$txtsearch = trim($txtsearch);
				$txtsearch = str_replace("%20"," ",$txtsearch);
				$filter2 .= " AND ( itemCode like  '%$txtsearch%'  or itemName like  '%$txtsearch%'  or Short_Description like '%$txtsearch%' or Long_Description like '%$txtsearch%')";
			  }   
			  

			  //PRICE RANGE	
			  if(($priceRange!='' AND $priceRange!='null') AND is_numeric($priceFrom) AND is_numeric($priceTo))
					$filter2 = "AND $priceRange >= $priceFrom AND $priceRange <= $priceTo ";
			  
			  
			  //DATE RANGE
			  $m=0;
			  if($year!='' AND $year!='null')
				$m = $year * 12;  
			  if($month!='' AND $month!='null')
				$m += $month;
			  
			  if(($year!='' AND $year!='null' AND $year!=0)  OR ($month!='' AND $month!='null' AND $month!=0)){
				$filter2 .= " AND items.dateAdded <= CURDATE() AND items.dateAdded >= (SELECT CURDATE() - INTERVAL $m MONTH) ";
			  }
			  
			  
			   //SORT BY DATE
			   if(($nviews!='' AND $nviews!='null') OR ($sort_by_price!='' AND $sort_by_price!='null') AND ($items_date!=''  OR $items_date!='null'))
					$order = "";
			 
			   if($nviews!='null' 	 	 AND $nviews!='')          $order  .= " COUNT(items.id) $nviews,";
			   if($sort_by_price!='null' AND $sort_by_price!='')   $order  .= str_replace("-"," ",$sort_by_price.","); 
			   if($items_date!='null' 	 AND $items_date!='')      $order  .= " items.id $items_date,"; 	  
		} 
		$order = substr($order, 0,-1);
		
		$data['post'] = $_POST; 
		
		if($filter=='' & $filter2!='') $filter = " AND ". substr($filter2,4);
		else $filter = "$filter $filter2"; 
		
		
		//TOTAL NUMBER OF ROWS
		$data['active_page']= 1;
		if($id!='') 
			$data['active_page']= $id;
		
	
		//NEW FUNCTION
		$url="";
		$data['txtsearch'] 		= "null";
		$data['selPOSMType'] 	= "null";
		$data['selPOSMStatus'] 	= "null";
		$data['selPremiumType'] = "null";
		$data['seloutlet'] 		= "null";
		$data['selCountry'] 	= "null";
		$data['selBrand'] 		= "null";
		$data['selMaterial'] 	= "null";
		$data['items_date'] 	= "null";
		$data['nviews'] 		= "null";
		$data['sort_by_price'] 	= "null";
		$data['priceRange'] 	= "null";
		$data['priceFrom'] 		= "null";
		$data['priceTo'] 		= "null";
		$data['year'] 			= "null";
		$data['month'] 			= "null";
		$data['priceRangeID'] 	= "null";


		if($action=="page")
		{
			$this->modules->module_checker(18,'REVIEW');
			//REPOST DATA
			if($txtsearch!='')   	
				$data['txtsearch'] 		=  $txtsearch;
			if($selPOSMType!='')   	
				$data['selPOSMType'] 	=  $selPOSMType;
			if($selPOSMStatus!='')   	
				$data['selPOSMStatus'] 	=  $selPOSMStatus;	
			if($selPremiumType!='')   	
				$data['selPremiumType'] =  $selPremiumType;	
			if($seloutlet!='')   	
				$data['seloutlet'] 		=  $seloutlet;
			if($selCountry!='')   	
				$data['selCountry'] 	=  $selCountry;
			if($selBrand!='')   	
				$data['selBrand'] 		=  $selBrand;
			if($selMaterial!='')   	
				$data['selMaterial'] 	=  $selMaterial;
			if($items_date!='')   	
				$data['items_date'] 	=  $items_date;
			if($nviews!='')   	
				$data['nviews'] 		=  $nviews;
			if($sort_by_price!='')   	
				$data['sort_by_price'] 	=  $sort_by_price;
			if($priceRange!='')   	
				$data['priceRange'] 	=  $priceRange;
			if($priceFrom!='')   	
				$data['priceFrom'] 		=  $priceFrom;
			if($priceTo!='')   	
				$data['priceTo'] 		=  $priceTo;
			if($year!='')   	
				$data['year'] 			=  $year;
			if($month!='')   	
				$data['month'] 			=  $month;
			if($priceRangeID!='')   	
				$data['priceRangeID'] 	=  $priceRangeID;
		}
		
		//SEARCH ACTION
		$data['url']  = $data['txtsearch']."/".$data['selPOSMType']."/".$data['selPOSMStatus']."/".$data['selPremiumType']."/".$data['seloutlet']."/".$data['selCountry']."/".$data['selBrand']."/".$data['selMaterial'].'/';
		$data['url'] .= $data['items_date']."/".$data['nviews']."/".$data['sort_by_price']."/".$data['priceRange']."/".$data['priceFrom']."/".$data['priceTo']."/".$data['year']."/".$data['month']."/".$data['priceRangeID'] ; 
		$data['searchAction'] = HTTP_PATH. "itemDatabase/redirect_link/".$data['redirectTo']."/page/1/".$data['url']; 
		
		if($action=="insert_sucess")								$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Item has been save.");
		elseif($action=="update_success"    	  			     || $msg=="update_success")							$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Item has been updated.");
		elseif($action=="cannot_be_purge"   	  			     || $msg=="cannot_be_purge")						 $data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>"Item cannot be purge because it is a result of campaign or featured in Common Gallery.");
		elseif($action=="cannot_be_archive" 	  			     || $msg=="cannot_be_archive")					 	 $data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>"Item cannot be archive because it is a result of campaign or featured in Common Gallery.");
		elseif($action=="tagged_as_unpopular" 	  			     || $msg=="tagged_as_unpopular")  				 	 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Item has been tagged as unpopular.");
		elseif($action=="tagged_as_popular_items" 			     || $msg=="tagged_as_popular_items")  			 	 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Tagged as popular items.");
		elseif($action=="tagged_as_popular_item"  			     || $msg=="tagged_as_popular_item")  				 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Tagged as popular item.");
		elseif($action=="tagged_as_not_popular_item"  		     || $msg=="tagged_as_not_popular_item")  			 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Tagged as not popular item.");
		elseif($action=="items_has_been_submitted_for_purging"   || $msg=="items_has_been_submitted_for_purging")  	 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Items has been submitted for purging.");
		elseif($action=="item_has_been_submitted_for_purging"    || $msg=="item_has_been_submitted_for_purging")   	 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Item has been submitted for purging.");
		elseif($action=="tagged_as_not_popular_items"  		     || $msg=="tagged_as_not_popular_items")   			 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Items has tagged as not popular items.");
		elseif($action=="items_has_been_submitted_for_archiving" || $msg=="items_has_been_submitted_for_archiving")   $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Items has been submitted for archiving.");
		elseif($action=="item_has_been_submitted_for_archiving"  || $msg=="item_has_been_submitted_for_archiving")    $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Item has been submitted for archiving.");
		elseif($action=="no_selected_item" 						 || $msg=="no_selected_item")    					 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"No selected item.");
		elseif($action=="tagged_as_disapproved" 			     || $msg=="tagged_as_disapproved")    				 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Tagged as disapproved.");
		elseif($action=="item_has_been_published"   			 || $msg=="item_has_been_published")   				 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Item has been published.");
		elseif($action=="item_has_been_disapproved" 			 || $msg=="item_has_been_disapproved")   			 $data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>"Item has been disapproved.");
		elseif($action=="disapprove" 							 || $msg=="disapprove")								 $data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>"Item has been tag as irrelevant.");
		elseif($action=="deleteOneItem")
	    {
			$this->modules->module_checker(18,'DELETE');
			
			$tables = array(
			  array('tbl'=>'itemVendorsRef',
					'fld'=>'itemID'),
			  array('tbl'=>'iLikeResultRef',
					'fld'=>'itemID'),
			  array('tbl'=>'iWantResultRef',
					'fld'=>'itemID'),
			  array('tbl'=>'votexRef',
					'fld'=>'itemID'),
			  array('tbl'=>'campaignItemsXref',
					'fld'=>'itemID')
					);
		
			if($this->modules->attr($tables,$id)==0)
			{
			//LOGS
			$sql = "SELECT itemName, itemCode FROM items WHERE id = $id";
			$sql = $this->db->query($sql);
			$sql = $sql->row();
			if(isset($sql->itemName))
				$CI->rec_logs->w($id,$sql->itemName,'Item Database','Items','delete',$sql->itemCode);
			
			$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Item has been deleted.');
			$this->c3model->c3crud("no-res",'','','',"DELETE FROM items WHERE id='$id'");
	 	    $this->c3model->c3crud("no-res",'','','',"DELETE FROM itemVendorsRef WHERE itemID='$id'");
	 	    $this->c3model->c3crud("no-res",'','','',"DELETE FROM items_images WHERE itemID='$id'");
			
			}else{
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Item cannot be delete because it is being link to vendors or campaign.');
			}
			
			$data['active_page']= 1;
		}
		elseif($action=="deleteSelectedItem")
		{
			$this->modules->module_checker(18,'DELETE');
			
			$ctr=0;
			if(isset($selectedItems)){
				foreach($selectedItems as $cbr => $value)
				{
			
				$tables = array(
				  array('tbl'=>'itemVendorsRef',
						'fld'=>'itemID'),
				  array('tbl'=>'iLikeResultRef',
						'fld'=>'itemID'),
				  array('tbl'=>'iWantResultRef',
						'fld'=>'itemID'),
				  array('tbl'=>'votexRef',
						'fld'=>'itemID'),
				  array('tbl'=>'campaignItemsXref',
						'fld'=>'itemID')
						);
						
					if($this->modules->attr($tables,$value)!=0)
						$ctr++;
				}
			}
			
			if($ctr==0)
			{
				if(isset($selectedItems)){
					foreach($selectedItems as $sItems => $value)
					{
						//LOGS
						$sql = "SELECT itemName, itemCode FROM items WHERE id = $value";
						$sql = $this->db->query($sql);
						$sql = $sql->row();
						if(isset($sql->itemName))
							$CI->rec_logs->w($value,$sql->itemName,'Item Database','Items','delete',$sql->itemCode);
						
						$this->c3model->c3crud("no-res",'','','',"DELETE FROM items WHERE id='$value'");
						$this->c3model->c3crud("no-res",'','','',"DELETE FROM itemVendorsRef WHERE itemID='$value'");
						$this->c3model->c3crud("no-res",'','','',"DELETE FROM items_images WHERE itemID='$value'");
					}
					$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Multiple Items has been deleted.');
				}
			}else{
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Multiple Items cannot been delete, because it is being use in campaign or link to vendor/s.');
			}
			
			$data['active_page']= 1;
		}
		elseif($action=="edit")
		{
			$this->modules->module_checker(18,'EDIT');
			$data['id'] 	 		= $id;
			$data['POSM_statusID']	= isset($sID) ? $sID : NULL;
			
			//REFERER
			//UPDATE SUCCESS ALREADY
			$data['referrer_link']	 = $this->tag_for_purgingRedirect($_SERVER['HTTP_REFERER'],'item_has_been_published');
			//SWITCH VALIDATION
			if(isset($referrer_link)) $data['referrer_link'] = $referrer_link;
			
			$filter = $this->modules->country2();
			$data['referrer'] = $txtsearch;
			$sql 			 		= $this->db->query("SELECT *, vendors.id as vID FROM vendors INNER JOIN country ON country.id = vendors.countryID $filter");
			$data['vendors'] 		= $sql->result_array();
			
			$sql 			 		= $this->db->query("SELECT * FROM items_images WHERE itemID = $id ORDER BY defaultStatus DESC");
			$data['items_images'] 	= $sql->result_array();
			if($data['APPROVE']==TRUE)
				$data['vfile']	= 'itemFORM_BUManager.php';
			else
				$data['vfile']	= 'itemFORM.php';
		}
		elseif($action=="preview")
		{
			$this->modules->module_checker(18,'REVIEW');
			
			$data['id'] 	 = $id;
			$data['POSM_statusID']	= isset($sID) ? $sID : NULL;
			$sql 			 = $this->db->query("SELECT *, vendors.id as vID FROM vendors INNER JOIN country ON country.id = vendors.countryID $filter");
			$data['vendors'] = $sql->result_array();
			
			$sql 			 = $this->db->query("SELECT * FROM items_images WHERE itemID = $id");
			$data['items_images'] = $sql->result_array();
			$data['vfile']	 = 'itemPreviewFORM.php';
		}
		elseif($action=="duplicate")
		{
			$this->modules->module_checker(18,'ADD');
			
			$data['id'] 	 	 = $id;
			$data['duplicate'] 	 = $id;
			$data['POSM_statusID']	= isset($sID) ? $sID : NULL;
			$sql 			 	 = $this->db->query("SELECT *, vendors.id as vID FROM vendors INNER JOIN country ON country.id = vendors.countryID $filter");
			$data['vendors'] 	 = $sql->result_array();
			
			$sql 			 	 = $this->db->query("SELECT * FROM items_images WHERE itemID = $id ORDER BY defaultStatus DESC");
			$data['items_images']= $sql->result_array();
			
			$data['vfile']	 = 'itemFORM.php';
		}
		elseif($action=="add")
		{
			$this->modules->module_checker(18,'ADD');
			$POSM_statusID			= isset($sID)			? $sID : NULL;
			$data['POSM_statusID']	= isset($POSM_statusID) ? $POSM_statusID : 154;
			$data['vfile']			= 'itemFORM.php';
			$sql 					= $this->db->query("SELECT *, vendors.id as vID FROM vendors INNER JOIN country ON country.id = vendors.countryID $filter");
			$data['vendors'] 		= $sql->result_array();
		}
		elseif($action=="update")
		{
			if($_POST==NULL){
				redirect(HTTP_PATH.'itemDatabase/BU_Marketing_Items_review', 'location', 301);
				die();
			}
			
			//$this->modules->module_checker(18,'EDIT');
			isset($Long_Description) ? $dbFields['Long_Description'] 	= $Long_Description : '';
			isset($brandID) 		 ? $dbFields['brandID'] 		  	= $brandID 			: '';
			isset($POSMTypeID) 	 	 ? $dbFields['POSMTypeID'] 	  		= $POSMTypeID 		: '';
			isset($POSMStatusID) 	 ? $dbFields['POSMStatusID'] 	  	= $POSMStatusID 	: '';
			isset($OUTLETStatusID) 	 ? $dbFields['OUTLETStatusID']   	= $OUTLETStatusID 	: $dbFields['OUTLETStatusID']   	= 0;
			isset($PremiumTypeID) 	 ? $dbFields['PremiumTypeID']    	= $PremiumTypeID 	: $dbFields['PremiumTypeID']    	= 0;
			isset($MaterialTypeID) 	 ? $dbFields['MaterialTypeID']   	= $MaterialTypeID 	: '';
			isset($countryID) 		 ? $dbFields['countryID'] 		  	= $countryID 		: '';
			isset($itemName) 	     ? $dbFields['itemName']         	= addslashes($itemName) : '';
			isset($Short_Description)? $dbFields['Short_Description'] 	= $Short_Description: '';
			isset($UnitPrice) 		 ? $dbFields['UnitPrice'] 		  	= $UnitPrice 		: '';
			isset($USD_Price) 		 ? $dbFields['USD_Price'] 		  	= $USD_Price 		: '';
			isset($MOQ) 			 ? $dbFields['MOQ'] 			  	= $MOQ 				: '';
			isset($UOM) 		     ? $dbFields['UOM'] 			  	= $UOM 				: '';
			isset($price_rangeID) 	 ? $dbFields['price_rangeID'] 		= $price_rangeID 	: '';
			isset($Fields0001) 		 ? $dbFields['Fields0001'] 	  = $Fields0001 : '';
			isset($Fields0002) 		 ? $dbFields['Fields0002'] 	  = $Fields0002 : '';
			isset($Fields0003) 		 ? $dbFields['Fields0003'] 	  = $Fields0003 : '';
			isset($Fields0004) 		 ? $dbFields['Fields0004'] 	  = $Fields0004 : '';
			isset($Fields0005) 		 ? $dbFields['Fields0005'] 	  = $Fields0005 : '';
			isset($estimated_production_lead_time) ? $dbFields['estimated_production_lead_time'] = $estimated_production_lead_time : '';
			isset($price_validity) 	? $dbFields['price_validity'] = $price_validity : '';
			
			isset($publish) 	 	 		? $dbFields['publish'] 		  			= $publish 				 : '';
			isset($irrelevant) 	 	 		? $dbFields['irrelevant'] 		  		= $irrelevant 			 : 'n';
			isset($publish_other_country) 	? $dbFields['publish_other_country']  	= $publish_other_country : '';
			isset($country_of_origin) 		? $dbFields['country_of_origin'] 		= $country_of_origin 	 : '';
			
			isset($plant_inventory) 		? $dbFields['plant_inventory'] 			= $plant_inventory 		  : '';
			isset($supplier_stock_on_hand) 	? $dbFields['supplier_stock_on_hand'] 	= $supplier_stock_on_hand : '';
			isset($date_first_issue) 		? $dbFields['date_first_issue'] 		= $date_first_issue 	  : '';
			isset($date_last_used) 			? $dbFields['date_last_used'] 			= $date_last_used 		  : '';
			isset($activity_event_use) 		? $dbFields['activity_event_use'] 		= $activity_event_use 	  : '';
			$dbFields['dateLastEdited']   	= date('Y-m-d');
			$dbFields['dateReleased'] 		= ($publish=='y') ? date('Y-m-d') : '0000-00-00';
			
			
			//CHECK REQUIRED FIELDS
			if($publish=='y' AND $this->field_checker($_POST)!=''){
				$data['USER_MANUAL'] = $this->modules->user_manual(13);
			
				$data['id'] 	 		= $id;
				$data['POSM_statusID']	= isset($sID) ? $sID : NULL;
				$sql 			 		= $this->db->query("SELECT *, vendors.id as vID FROM vendors INNER JOIN country ON country.id = vendors.countryID $filter_add");
				$data['vendors'] 		= $sql->result_array();
				
				$sql 			 		= $this->db->query("SELECT * FROM items_images WHERE itemID = $id ORDER BY defaultStatus DESC");
				$data['items_images'] 	= $sql->result_array();
				$data['vfile']			= 'itemFORM_BUManager.php';
				
				$data['POST']			= $_POST;
				$data['msg']			= array('msg_type'=>'alert-warning','msg_desc'=>$this->field_checker($_POST)); 
			}
			else
			{
				//UPDATE ITEM CODE
				$itemField['itemCode'] = $this->generate_ItemCode($id,$countryID,isset($POSMTypeID) ? $POSMTypeID : 0);
				$this->c3model->c3crud("update",$table,$itemField,$id);
				
				//IMAGE
				$config['upload_path'] = FCPATH2.'/img/items/';
				$config['allowed_types'] = 'gif|jpg|png';
				$this->upload->initialize($config);
				
				if($this->upload->do_multi_upload("files")){
					//Print data for all uploaded files.
					//print_r($this->upload->get_multi_upload_data());
					
					$files = $this->upload->get_multi_upload_data();
					foreach($files as $f)
					{
						extract($f); 
						//GENERATE IMAGE CODE
						$new_file_name=$this->generateImgCode($id,$itemField['itemCode'],$f['file_name']);
						$this->imageResize($file_name,$new_file_name,$f['file_path']);
						$refdbFields['itemID'] = $id; 
						$refdbFields['image']  = $new_file_name; 
						
						$res = $this->c3model->c3crud("insert",'items_images',$refdbFields,'');
					}
					
					//TAG LAST IMAGE
					$this->db->query("UPDATE  `items_images` SET  `defaultStatus` =  '0' WHERE  itemID = $id");
					$query = $this->db->query("SELECT MAX(items_images.id) as lastImageID FROM items_images WHERE itemID = $id LIMIT 1");
					$row = $query->row();

					$this->db->query("UPDATE  `items_images` SET  `defaultStatus` =  '1' WHERE  `items_images`.`id` = ". $row->lastImageID );
				}
				
				$this->c3model->c3crud("update",$table,$dbFields,$id);
				
				//VENDOR REFERENCE
				//DELETE PREVIUOS VENDORS
				if($data['VENDORS_EDIT']==TRUE){
					$this->c3model->c3crud("no-res",'','','',"DELETE FROM itemVendorsRef WHERE itemID='$id'");
					$refFields['itemID'] =  $id;
					
					if(isset($multipleVendors)){
						foreach($multipleVendors as $mV => $value)
						{
							$refFields['vendorID'] = $value;
							$res = $this->c3model->c3crud("insert",'itemVendorsRef',$refFields,'');
						}
					}
				}
			
				//LOGS
				if(!isset($itemName)){
					$sql = "SELECT itemName FROM items WHERE id = $id";
					$sql = $this->db->query($sql);
					$row = $sql->row();
					$itemName = $row->itemName;
				}
					
				$CI->rec_logs->w($id,$itemName,'Item Database','Items','edit',$itemField['itemCode']);
				
				$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Item has been updated.');
				
				//DISAPPROVED
				if($dbFields['irrelevant']=='y')
					$this->tag_for_purgingRedirect($_SERVER['HTTP_REFERER'],'item_has_been_disapproved');
				//PUBLISHED
				if(isset($referrer_link)) redirect($referrer_link, 'location', 301);				
			}
		}
		
		
		$sqlSTr =  "SELECT *,
			OUTLET_Status.statusName as OutletStatusName, 
			POSM_Status.statusName as POSMStatusName,
			POSM_Type.typeName as POSM_TypeName,
			items.id as itemID 
			FROM items 
			LEFT JOIN POSM_Type 		ON items.POSMTypeID = POSM_Type.id 
			LEFT JOIN POSM_Status 		ON items.POSMStatusID = POSM_Status.id 
			LEFT JOIN OUTLET_Status 	ON items.OUTLETStatusID = OUTLET_Status.id
			LEFT JOIN premiumItemType 	ON items.PremiumTypeID = premiumItemType.id 
			LEFT JOIN MATERIAL_Type 	ON items.MaterialTypeID = MATERIAL_Type.id 
			LEFT JOIN country 			ON items.countryID = country.id 
			LEFT JOIN brands  			ON items.brandID = brands.id 
			LEFT JOIN item_views  		ON items.id 	 = item_views.itemID
			WHERE publish ='n' AND irrelevant='n' AND items.purge='n' $filter  
			GROUP BY items.id ORDER BY $order";

		$sql = $this->db->query($sqlSTr);
		$sql = $sql->result_array();
		$total_rec = count($sql);

		$data['total_rec'] = $total_rec;
		$pagenum = $data['active_page'];
		$data['page_rows'] = 15; 
		$data['last'] = ceil($total_rec/$data['page_rows']);		
		$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		
		//echo $sqlSTr ." ". $max;
		$sql = $this->db->query($sqlSTr ." ". $max );
		$data['data'] = $sql->result_array();
	   
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
	
	function BU_Logistics_Items_review($action='',$id='',$txtsearch='',$selPOSMType='',$selPOSMStatus='',$selPremiumType='',$seloutlet='',$selCountry='',$selBrand='',$selMaterial='',$items_date='',$nviews='',$sort_by_price='',$priceRange='',$priceFrom='',$priceTo='',$year='',$month='',$priceRangeID='',$msg='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		
		$this->modules->module_checker(56,'REVIEW');
		$filter = $this->modules->country2();
		
		$table= "items";
		$data['vfile']				= 'BU_Logistics_Items_review.php';
	    $data['title']				= 'BU Logictics Items Review | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//CRUD
		$data['ADD'] 	=  $this->modules->crud_checker(18,'ADD');
		$data['EDIT'] 	=  $this->modules->crud_checker(18,'EDIT');
		$data['DELETE'] =  $this->modules->crud_checker(18,'DELETE');
		$data['APPROVE'] =  $this->modules->crud_checker(18,'APPROVE');
		$data['TAG_AS_POPULAR'] =  $this->modules->crud_checker(77,'ADD');
		
		$data['VENDORS_EDIT'] 	=  $this->modules->crud_checker(26,'EDIT');
		$data['SAVE_FOR_LATER'] =  $this->modules->BU_Logistics();
		$data['DISABLE_BUTTONS'] =  FALSE;
		
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(11);
		
		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
	    $data['breadCrumbs']		= '<a href='.$HTTP_PATH.'itemDatabase> Item Database </a>';
	    $data['breadCrumbs']	   .= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']	   .= '<a href='.$HTTP_PATH.'itemDatabase/BU_Logistics_Items_review> BU Logictics: Items Review </a>';

		//CSRF
		$data['csrf'] = "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>"; 
		$data['searchAction'] = HTTP_PATH. "itemDatabase/BU_Logistics_Items_review.html"; 
		
		
		//search filter
		$data['post'] = $_POST; $filter2="";
		$order = "items.dateAdded DESC,";
		
        $data['redirectTo']	= "BU_Logistics_Items_review";
        if(($txtsearch!='' OR $selPOSMType!='' OR $selPOSMStatus!='' OR $selPremiumType!='' OR $seloutlet!='' OR $selCountry!='' OR $selBrand!='' OR $selMaterial!='' OR $items_date=!'' OR $nviews!='' OR $sort_by_price!='' OR $priceRange!='' OR $priceRangeID!=''))		
		{ 
			extract($_POST);  
			//$_SESSION['txtsearch']="";
              if($selPOSMType!='null' 	 AND $selPOSMType!='')     	$filter2 .= " AND POSMTypeID		='$selPOSMType'";
              if($selPOSMStatus!='null'  AND $selPOSMStatus!='')   	$filter2 .= " AND POSMStatusID		='$selPOSMStatus'";
			  if($selPremiumType!='null' AND $selPremiumType!='')  	$filter2 .= " AND PremiumTypeID		='$selPremiumType'";
			  if($seloutlet!='null' 	 AND $seloutlet!='')       	$filter2 .= " AND OUTLETStatusID	='$seloutlet'";
			  if($selCountry!='null'	 AND $selCountry!='')      	$filter2 .= " AND countryID			='$selCountry'";
			  if($selBrand!='null' 		 AND $selBrand!='')        	$filter2 .= " AND brandID 			='$selBrand'";
			  if($selMaterial!='null' 	 AND $selMaterial!='')     	$filter2 .= " AND MaterialTypeID	='$selMaterial'";
			  if($priceRangeID!='null' AND $priceRangeID!='')    $filter2 .= " AND price_rangeID	    ='$priceRangeID'";
			  
			  if($txtsearch!='' AND $txtsearch!='null'){
				$txtsearch = addslashes($txtsearch);
				$txtsearch = trim($txtsearch);
				$txtsearch = str_replace("%20"," ",$txtsearch);
				$filter2 .= " AND ( itemCode like  '%$txtsearch%'  or itemName like  '%$txtsearch%'  or Short_Description like '%$txtsearch%' or Long_Description like '%$txtsearch%')";
			  }   
			  

			  //PRICE RANGE	
			  if(($priceRange!='' AND $priceRange!='null') AND is_numeric($priceFrom) AND is_numeric($priceTo))
					$filter2 = "AND $priceRange >= $priceFrom AND $priceRange <= $priceTo ";
			  
			  
			  //DATE RANGE
			  $m=0;
			  if($year!='' AND $year!='null')
				$m = $year * 12;  
			  if($month!='' AND $month!='null')
				$m += $month;
			  
			  if(($year!='' AND $year!='null' AND $year!=0)  OR ($month!='' AND $month!='null' AND $month!=0)){
				$filter2 .= " AND items.dateAdded <= CURDATE() AND items.dateAdded >= (SELECT CURDATE() - INTERVAL $m MONTH) ";
			  }
			  
			  
			   //SORT BY DATE
			   if(($nviews!='' AND $nviews!='null') OR ($sort_by_price!='' AND $sort_by_price!='null') AND ($items_date!=''  OR $items_date!='null'))
					$order = "";
			 
			   if($nviews!='null' 	 	 AND $nviews!='')          $order  .= " COUNT(items.id) $nviews,";
			   if($sort_by_price!='null' AND $sort_by_price!='')   $order  .= str_replace("-"," ",$sort_by_price.","); 
			   if($items_date!='null' 	 AND $items_date!='')      $order  .= " items.id $items_date,"; 
			  
		} 
		$order = substr($order, 0,-1);
		
		$data['post'] = $_POST; 
		
		
		if($filter=='' & $filter2!='') $filter = " AND ". substr($filter2,4);
		else $filter = "$filter $filter2"; 
		
		
		//TOTAL NUMBER OF ROWS
		$data['active_page']= 1;
		if($id!='') 
			$data['active_page']= $id;
		
		
		//NEW FUNCTION
		$url="";
		$data['txtsearch'] 		= "null";
		$data['selPOSMType'] 	= "null";
		$data['selPOSMStatus'] 	= "null";
		$data['selPremiumType'] = "null";
		$data['seloutlet'] 		= "null";
		$data['selCountry'] 	= "null";
		$data['selBrand'] 		= "null";
		$data['selMaterial'] 	= "null";
		$data['items_date'] 	= "null";
		$data['nviews'] 		= "null";
		$data['sort_by_price'] 	= "null";
		$data['priceRange'] 	= "null";
		$data['priceFrom'] 		= "null";
		$data['priceTo'] 		= "null";
		$data['year'] 			= "null";
		$data['month'] 			= "null";
		$data['priceRangeID'] 	= "null";


		if($action=="page")
		{
			$this->modules->module_checker(18,'REVIEW');
			//REPOST DATA
			if($txtsearch!='')   	
				$data['txtsearch'] 		=  $txtsearch;
			if($selPOSMType!='')   	
				$data['selPOSMType'] 	=  $selPOSMType;
			if($selPOSMStatus!='')   	
				$data['selPOSMStatus'] 	=  $selPOSMStatus;	
			if($selPremiumType!='')   	
				$data['selPremiumType'] =  $selPremiumType;	
			if($seloutlet!='')   	
				$data['seloutlet'] 		=  $seloutlet;
			if($selCountry!='')   	
				$data['selCountry'] 	=  $selCountry;
			if($selBrand!='')   	
				$data['selBrand'] 		=  $selBrand;
			if($selMaterial!='')   	
				$data['selMaterial'] 	=  $selMaterial;
			if($items_date!='')   	
				$data['items_date'] 	=  $items_date;
			if($nviews!='')   	
				$data['nviews'] 		=  $nviews;
			if($sort_by_price!='')   	
				$data['sort_by_price'] 	=  $sort_by_price;
			if($priceRange!='')   	
				$data['priceRange'] 	=  $priceRange;
			if($priceFrom!='')   	
				$data['priceFrom'] 		=  $priceFrom;
			if($priceTo!='')   	
				$data['priceTo'] 		=  $priceTo;
			if($year!='')   	
				$data['year'] 			=  $year;
			if($month!='')   	
				$data['month'] 			=  $month;
			if($priceRangeID!='')   	
				$data['priceRangeID'] 	=  $priceRangeID;
		}
		
		//SEARCH ACTION
		$data['url']  = $data['txtsearch']."/".$data['selPOSMType']."/".$data['selPOSMStatus']."/".$data['selPremiumType']."/".$data['seloutlet']."/".$data['selCountry']."/".$data['selBrand']."/".$data['selMaterial'].'/';
		$data['url'] .= $data['items_date']."/".$data['nviews']."/".$data['sort_by_price']."/".$data['priceRange']."/".$data['priceFrom']."/".$data['priceTo']."/".$data['year']."/".$data['month']."/".$data['priceRangeID'] ; 
		$data['searchAction'] = HTTP_PATH. "itemDatabase/redirect_link/".$data['redirectTo']."/page/1/".$data['url']; 
		
		
	    if($action=="insert_sucess")	   							$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Item has been save.");
		elseif($action=="update_success"    	  			     || $msg=="update_success")							$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Item has been updated.");
		elseif($action=="cannot_be_purge"   	  			     || $msg=="cannot_be_purge")						 $data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>"Item cannot be purge because it is a result of campaign or featured in Common Gallery.");
		elseif($action=="some_cannot_be_purge"   	  			 || $msg=="some_cannot_be_purge")					  $data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>"Some items cannot be purge because it is a result of campaign or featured in Common Gallery.");
		elseif($action=="cannot_be_archive" 	  			     || $msg=="cannot_be_archive")					 	 $data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>"Item cannot be archive because it is a result of campaign or featured in Common Gallery.");
		elseif($action=="tagged_as_unpopular" 	  			     || $msg=="tagged_as_unpopular")  				 	 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Item has been tagged as unpopular.");
		elseif($action=="tagged_as_popular_items" 			     || $msg=="tagged_as_popular_items")  			 	 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Tagged as popular items.");
		elseif($action=="tagged_as_popular_item"  			     || $msg=="tagged_as_popular_item")  				 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Tagged as popular item.");
		elseif($action=="tagged_as_not_popular_item"  		     || $msg=="tagged_as_not_popular_item")  			 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Tagged as not popular item.");
		elseif($action=="items_has_been_submitted_for_purging"   || $msg=="items_has_been_submitted_for_purging")  	 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Items has been submitted for purging.");
		elseif($action=="item_has_been_submitted_for_purging"    || $msg=="item_has_been_submitted_for_purging")   	 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Item has been submitted for purging.");
		elseif($action=="tagged_as_not_popular_items"  		     || $msg=="tagged_as_not_popular_items")   			 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Items has tagged as not popular items.");
		elseif($action=="items_has_been_submitted_for_archiving" || $msg=="items_has_been_submitted_for_archiving")   $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Items has been submitted for archiving.");
		elseif($action=="item_has_been_submitted_for_archiving"  || $msg=="item_has_been_submitted_for_archiving")    $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Item has been submitted for archiving.");
		elseif($action=="no_selected_item" 						 || $msg=="no_selected_item")    					 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"No selected item.");
		elseif($action=="tagged_as_disapproved" 			     || $msg=="tagged_as_disapproved")    				 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Tagged as disapproved.");
		elseif($action=="item_has_been_published"   			 || $msg=="item_has_been_published")   				 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Item has been published.");
		elseif($action=="item_has_been_disapproved" 			 || $msg=="item_has_been_disapproved")   			 $data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>"Item has been disapproved.");
		elseif($action=="disapprove" 							 || $msg=="disapprove")								 $data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>"Item has been tag as irrelevant.");
		elseif($action=="deleteOneItem")
	    {
			$this->modules->module_checker(18,'DELETE');
			
			$tables = array(
			  array('tbl'=>'itemVendorsRef',
					'fld'=>'itemID'),
			  array('tbl'=>'iLikeResultRef',
					'fld'=>'itemID'),
			  array('tbl'=>'iWantResultRef',
					'fld'=>'itemID'),
			  array('tbl'=>'votexRef',
					'fld'=>'itemID'),
			  array('tbl'=>'campaignItemsXref',
					'fld'=>'itemID')
					);
		
			if($this->modules->attr($tables,$id)==0)
			{
			//LOGS
			$sql = "SELECT itemName, itemCode FROM items WHERE id = $id";
			$sql = $this->db->query($sql);
			$sql = $sql->row();
			if(isset($sql->itemName))
				$CI->rec_logs->w($id,$sql->itemName,'Item Database','Items','delete',$sql->itemCode);
			
			$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Item has been deleted.');
			$this->c3model->c3crud("no-res",'','','',"DELETE FROM items WHERE id='$id'");
	 	    $this->c3model->c3crud("no-res",'','','',"DELETE FROM itemVendorsRef WHERE itemID='$id'");
	 	    $this->c3model->c3crud("no-res",'','','',"DELETE FROM items_images WHERE itemID='$id'");
			
			}else{
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Item cannot be delete because it is being link to vendors or campaign.');
			}
			
			$data['active_page']= 1;
		}
		elseif($action=="deleteSelectedItem")
		{
			$this->modules->module_checker(18,'DELETE');
			
			$ctr=0;
			foreach($selectedItems as $cbr => $value)
			{
		
			$tables = array(
			  array('tbl'=>'itemVendorsRef',
					'fld'=>'itemID'),
			  array('tbl'=>'iLikeResultRef',
					'fld'=>'itemID'),
			  array('tbl'=>'iWantResultRef',
					'fld'=>'itemID'),
			  array('tbl'=>'votexRef',
					'fld'=>'itemID'),
			  array('tbl'=>'campaignItemsXref',
					'fld'=>'itemID')
					);
					
				if($this->modules->attr($tables,$value)!=0)
					$ctr++;
			}
			
			if($ctr==0)
			{
				foreach($selectedItems as $sItems => $value)
				{
					//LOGS
					$sql = "SELECT itemName, itemCode FROM items WHERE id = $value";
					$sql = $this->db->query($sql);
					$sql = $sql->row();
					if(isset($sql->itemName))
						$CI->rec_logs->w($value,$sql->itemName,'Item Database','Items','delete',$sql->itemCode);
					
					$this->c3model->c3crud("no-res",'','','',"DELETE FROM items WHERE id='$value'");
					$this->c3model->c3crud("no-res",'','','',"DELETE FROM itemVendorsRef WHERE itemID='$value'");
					$this->c3model->c3crud("no-res",'','','',"DELETE FROM items_images WHERE itemID='$value'");
				}
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Multiple Items has been deleted.');
			}else{
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Multiple Items cannot been delete, because it is being use in campaign or link to vendor/s.');
			}
			
			$data['active_page']= 1;
		}
		elseif($action=="edit")
		{
			$this->modules->module_checker(18,'EDIT');
			$data['id'] 	 		= $id;
			$data['POSM_statusID']	= isset($sID) ? $sID : NULL;
			
			//REFERER
			//UPDATE SUCCESS ALREADY
			$data['referrer_link']	 = $this->tag_for_purgingRedirect($_SERVER['HTTP_REFERER'],'update_success');
			//SWITCH VALIDATION
			if(isset($referrer_link)) $data['referrer_link'] = $referrer_link;
			
			$filter = $this->modules->country2();
			$data['referrer'] = $txtsearch;
			$sql 			 		= $this->db->query("SELECT *, vendors.id as vID FROM vendors INNER JOIN country ON country.id = vendors.countryID $filter");
			$data['vendors'] 		= $sql->result_array();
			
			$sql 			 		= $this->db->query("SELECT * FROM items_images WHERE itemID = $id ORDER BY defaultStatus DESC");
			$data['items_images'] 	= $sql->result_array();
			$data['vfile']	 		= 'itemFORM.php';
		}
		elseif($action=="preview")
		{
			$this->modules->module_checker(18,'REVIEW');
			
			$data['id'] 	 = $id;
			$data['POSM_statusID']	= isset($sID) ? $sID : NULL;
			$sql 			 = $this->db->query("SELECT *, vendors.id as vID FROM vendors INNER JOIN country ON country.id = vendors.countryID $filter");
			$data['vendors'] = $sql->result_array();
			
			$sql 			 = $this->db->query("SELECT * FROM items_images WHERE itemID = $id");
			$data['items_images'] = $sql->result_array();
			$data['vfile']	 = 'itemPreviewFORM.php';
		}
		elseif($action=="duplicate")
		{
			$this->modules->module_checker(18,'ADD');
			
			$data['id'] 	 	 = $id;
			$data['duplicate'] 	 = $id;
			$data['POSM_statusID']	= isset($sID) ? $sID : NULL;
			$sql 			 	 = $this->db->query("SELECT *, vendors.id as vID FROM vendors INNER JOIN country ON country.id = vendors.countryID $filter");
			$data['vendors'] 	 = $sql->result_array();
			
			$sql 			 	 = $this->db->query("SELECT * FROM items_images WHERE itemID = $id ORDER BY defaultStatus DESC");
			$data['items_images']= $sql->result_array();
			
			$data['vfile']	 = 'itemFORM.php';
		}
	   
	   $sqlSTr =  "SELECT *,
			OUTLET_Status.statusName as OutletStatusName, 
			POSM_Status.statusName as POSMStatusName,
			POSM_Type.typeName as POSM_TypeName,
			items.id as itemID 
			FROM items 
			LEFT JOIN POSM_Type 		ON items.POSMTypeID = POSM_Type.id 
			LEFT JOIN POSM_Status 		ON items.POSMStatusID = POSM_Status.id 
			LEFT JOIN OUTLET_Status 	ON items.OUTLETStatusID = OUTLET_Status.id
			LEFT JOIN premiumItemType 	ON items.PremiumTypeID = premiumItemType.id 
			LEFT JOIN MATERIAL_Type 	ON items.MaterialTypeID = MATERIAL_Type.id 
			LEFT JOIN country 			ON items.countryID = country.id 
			LEFT JOIN brands  			ON items.brandID = brands.id 
			LEFT JOIN item_views  		ON items.id 	 = item_views.itemID
			WHERE publish ='n' AND irrelevant='n' AND items.purge='n' 
			$filter  
			GROUP BY items.id ORDER BY $order";
		
		$sql = $this->db->query($sqlSTr);
		$sql = $sql->result_array();
		$total_rec = count($sql);

		$data['total_rec'] = $total_rec;
		$pagenum = $data['active_page'];
		$data['page_rows'] = 15; 
		$data['last'] = ceil($total_rec/$data['page_rows']);		
		$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];


		
		//echo $sqlSTr ." ". $max;
		$sql = $this->db->query($sqlSTr ." ". $max );
		$data['data'] = $sql->result_array();
	   
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
	
	function disapproved_items($action='',$id='',$txtsearch='',$selPOSMType='',$selPOSMStatus='',$selPremiumType='',$seloutlet='',$selCountry='',$selBrand='',$selMaterial='',$items_date='',$nviews='',$sort_by_price='',$priceRange='',$priceFrom='',$priceTo='',$year='',$month='',$priceRangeID='',$msg='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		
		$this->modules->module_checker(18,'REVIEW');
		$filter = $this->modules->country2();
		
		$table= "items";
		$data['vfile']				= 'disapproved_items.php';
	    $data['title']				= 'BU Marketing Items Review | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//CRUD
		$data['ADD'] 	 =  $this->modules->crud_checker(18,'ADD');
		$data['EDIT'] 	 =  $this->modules->crud_checker(18,'EDIT');
		$data['DELETE']  =  $this->modules->crud_checker(18,'DELETE');
		$data['APPROVE'] =  $this->modules->crud_checker(18,'APPROVE');
		$data['DISABLE_BUTTONS'] =  $this->modules->crud_checker(18,'APPROVE');
		
		$data['VENDORS_EDIT'] 	=  $this->modules->crud_checker(26,'EDIT');
		$data['SAVE_FOR_LATER'] =  $this->modules->BU_Logistics();
		
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(10);
		
		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
	    $data['breadCrumbs']		= '<a href='.$HTTP_PATH.'itemDatabase> Item Database </a>';
	    $data['breadCrumbs']	   .= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']	   .= '<a href='.$HTTP_PATH.'itemDatabase/disapproved_items> Disapproved Items </a>';

		//CSRF
		$data['csrf'] = "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>"; 
		$data['searchAction'] = HTTP_PATH. "itemDatabase/disapproved_items.html"; 
		
		//search filter
		$data['post'] = $_POST; $filter2="";
		$order = "items.id DESC,";
		
        $data['redirectTo']	= "disapproved_items";
        if(($txtsearch!='' OR $selPOSMType!='' OR $selPOSMStatus!='' OR $selPremiumType!='' OR $seloutlet!='' OR $selCountry!='' OR $selBrand!='' OR $selMaterial!='' OR $items_date=!'' OR $nviews!='' OR $sort_by_price!='' OR $priceRange!='' OR $priceRangeID!=''))		
		{ 
			extract($_POST);  
			//$_SESSION['txtsearch']="";
              if($selPOSMType!='null' 	 AND $selPOSMType!='')     	$filter2 .= " AND POSMTypeID		='$selPOSMType'";
              if($selPOSMStatus!='null'  AND $selPOSMStatus!='')   	$filter2 .= " AND POSMStatusID		='$selPOSMStatus'";
			  if($selPremiumType!='null' AND $selPremiumType!='')  	$filter2 .= " AND PremiumTypeID		='$selPremiumType'";
			  if($seloutlet!='null' 	 AND $seloutlet!='')       	$filter2 .= " AND OUTLETStatusID	='$seloutlet'";
			  if($selCountry!='null'	 AND $selCountry!='')      	$filter2 .= " AND countryID			='$selCountry'";
			  if($selBrand!='null' 		 AND $selBrand!='')        	$filter2 .= " AND brandID 			='$selBrand'";
			  if($selMaterial!='null' 	 AND $selMaterial!='')     	$filter2 .= " AND MaterialTypeID	='$selMaterial'";
			  if($priceRangeID!='null' AND $priceRangeID!='')    $filter2 .= " AND price_rangeID	    ='$priceRangeID'";
			  
			  if($txtsearch!='' AND $txtsearch!='null'){
				$txtsearch = addslashes($txtsearch);
				$txtsearch = trim($txtsearch);
				$txtsearch = str_replace("%20"," ",$txtsearch);
				$filter2 .= " AND ( itemCode like  '%$txtsearch%'  or itemName like  '%$txtsearch%'  or Short_Description like '%$txtsearch%' or Long_Description like '%$txtsearch%')";
			  }   
			  

			  //PRICE RANGE	
			  if(($priceRange!='' AND $priceRange!='null') AND is_numeric($priceFrom) AND is_numeric($priceTo))
					$filter2 = "AND $priceRange >= $priceFrom AND $priceRange <= $priceTo ";
			  
			  
			  //DATE RANGE
			  $m=0;
			  if($year!='' AND $year!='null')
				$m = $year * 12;  
			  if($month!='' AND $month!='null')
				$m += $month;
			  
			  if(($year!='' AND $year!='null' AND $year!=0)  OR ($month!='' AND $month!='null' AND $month!=0)){
				$filter2 .= " AND items.dateAdded <= CURDATE() AND items.dateAdded >= (SELECT CURDATE() - INTERVAL $m MONTH) ";
			  }
			  
			  
			   //SORT BY DATE
			   if(($nviews!='' AND $nviews!='null') OR ($sort_by_price!='' AND $sort_by_price!='null') AND ($items_date!=''  OR $items_date!='null'))
					$order = "";
			 
			   if($nviews!='null' 	 	 AND $nviews!='')          $order  .= " COUNT(item_views.itemID) $nviews,";
			   if($sort_by_price!='null' AND $sort_by_price!='')   $order  .= str_replace("-"," ",$sort_by_price.","); 
			   if($items_date!='null' 	 AND $items_date!='')      $order  .= " items.id $items_date,"; 
			  
		} 
		$order = substr($order, 0,-1);
		
		
		$data['post'] = $_POST; 
		
		if($filter=='' & $filter2!='') $filter = " AND ". substr($filter2,4);
		else $filter = "$filter $filter2"; 
	
		
		//TOTAL NUMBER OF ROWS
		$data['active_page']= 1;
		if($id!='') 
			$data['active_page']= $id;
		
	
		
		//NEW FUNCTION
		$url="";
		$data['txtsearch'] 		= "null";
		$data['selPOSMType'] 	= "null";
		$data['selPOSMStatus'] 	= "null";
		$data['selPremiumType'] = "null";
		$data['seloutlet'] 		= "null";
		$data['selCountry'] 	= "null";
		$data['selBrand'] 		= "null";
		$data['selMaterial'] 	= "null";
		$data['items_date'] 	= "null";
		$data['nviews'] 		= "null";
		$data['sort_by_price'] 	= "null";
		$data['priceRange'] 	= "null";
		$data['priceFrom'] 		= "null";
		$data['priceTo'] 		= "null";
		$data['year'] 			= "null";
		$data['month'] 			= "null";
		$data['priceRangeID'] 	= "null";

		if($action=="page")
		{
			$this->modules->module_checker(18,'REVIEW');
			//REPOST DATA
			if($txtsearch!='')   	
				$data['txtsearch'] 		=  $txtsearch;
			if($selPOSMType!='')   	
				$data['selPOSMType'] 	=  $selPOSMType;
			if($selPOSMStatus!='')   	
				$data['selPOSMStatus'] 	=  $selPOSMStatus;	
			if($selPremiumType!='')   	
				$data['selPremiumType'] =  $selPremiumType;	
			if($seloutlet!='')   	
				$data['seloutlet'] 		=  $seloutlet;
			if($selCountry!='')   	
				$data['selCountry'] 	=  $selCountry;
			if($selBrand!='')   	
				$data['selBrand'] 		=  $selBrand;
			if($selMaterial!='')   	
				$data['selMaterial'] 	=  $selMaterial;
			if($items_date!='')   	
				$data['items_date'] 	=  $items_date;
			if($nviews!='')   	
				$data['nviews'] 		=  $nviews;
			if($sort_by_price!='')   	
				$data['sort_by_price'] 	=  $sort_by_price;
			if($priceRange!='')   	
				$data['priceRange'] 	=  $priceRange;
			if($priceFrom!='')   	
				$data['priceFrom'] 		=  $priceFrom;
			if($priceTo!='')   	
				$data['priceTo'] 		=  $priceTo;
			if($year!='')   	
				$data['year'] 			=  $year;
			if($month!='')   	
				$data['month'] 			=  $month;
			if($priceRangeID!='')   	
				$data['priceRangeID'] 	=  $priceRangeID;
		}
		
		//SEARCH ACTION
		$data['url']  = $data['txtsearch']."/".$data['selPOSMType']."/".$data['selPOSMStatus']."/".$data['selPremiumType']."/".$data['seloutlet']."/".$data['selCountry']."/".$data['selBrand']."/".$data['selMaterial'].'/';
		$data['url'] .= $data['items_date']."/".$data['nviews']."/".$data['sort_by_price']."/".$data['priceRange']."/".$data['priceFrom']."/".$data['priceTo']."/".$data['year']."/".$data['month']."/".$data['priceRangeID'];
		$data['searchAction'] = HTTP_PATH. "itemDatabase/redirect_link/".$data['redirectTo']."/page/1/".$data['url']; 
		
		
		if($action=="insert_sucess")								$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Item has been save.");
		elseif($action=="update_success"    	  			     || $msg=="update_success")							$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Item has been updated.");
		elseif($action=="cannot_be_purge"   	  			     || $msg=="cannot_be_purge")						 $data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>"Item cannot be purge because it is a result of campaign or featured in Common Gallery.");
		elseif($action=="cannot_be_archive" 	  			     || $msg=="cannot_be_archive")					 	 $data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>"Item cannot be archive because it is a result of campaign or featured in Common Gallery.");
		elseif($action=="tagged_as_unpopular" 	  			     || $msg=="tagged_as_unpopular")  				 	 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Item has been tagged as unpopular.");
		elseif($action=="tagged_as_popular_items" 			     || $msg=="tagged_as_popular_items")  			 	 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Tagged as popular items.");
		elseif($action=="tagged_as_popular_item"  			     || $msg=="tagged_as_popular_item")  				 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Tagged as popular item.");
		elseif($action=="tagged_as_not_popular_item"  		     || $msg=="tagged_as_not_popular_item")  			 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Tagged as not popular item.");
		elseif($action=="items_has_been_submitted_for_purging"   || $msg=="items_has_been_submitted_for_purging")  	 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Items has been submitted for purging.");
		elseif($action=="item_has_been_submitted_for_purging"    || $msg=="item_has_been_submitted_for_purging")   	 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Item has been submitted for purging.");
		elseif($action=="tagged_as_not_popular_items"  		     || $msg=="tagged_as_not_popular_items")   			 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Items has tagged as not popular items.");
		elseif($action=="items_has_been_submitted_for_archiving" || $msg=="items_has_been_submitted_for_archiving")   $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Items has been submitted for archiving.");
		elseif($action=="item_has_been_submitted_for_archiving"  || $msg=="item_has_been_submitted_for_archiving")    $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Item has been submitted for archiving.");
		elseif($action=="no_selected_item" 						 || $msg=="no_selected_item")    					 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"No selected item.");
		elseif($action=="tagged_as_disapproved" 			     || $msg=="tagged_as_disapproved")    				 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Tagged as disapproved.");
		elseif($action=="item_has_been_published"   			 || $msg=="item_has_been_published")   				 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Item has been published.");
		elseif($action=="item_has_been_disapproved" 			 || $msg=="item_has_been_disapproved")   			 $data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>"Item has been disapproved.");
		elseif($action=="disapprove" 							 || $msg=="disapprove")								 $data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>"Item has been tag as irrelevant.");
	    elseif($action=="deleteOneItem")
	    {
			$this->modules->module_checker(18,'DELETE');
			
			$tables = array(
			  array('tbl'=>'itemVendorsRef',
					'fld'=>'itemID'),
			  array('tbl'=>'iLikeResultRef',
					'fld'=>'itemID'),
			  array('tbl'=>'iWantResultRef',
					'fld'=>'itemID'),
			  array('tbl'=>'votexRef',
					'fld'=>'itemID'),
			  array('tbl'=>'campaignItemsXref',
					'fld'=>'itemID')
					);
		
			if($this->modules->attr($tables,$id)==0)
			{
			//LOGS
			$sql = "SELECT itemName, itemCode FROM items WHERE id = $id";
			$sql = $this->db->query($sql);
			$sql = $sql->row();
			if(isset($sql->itemName))
				$CI->rec_logs->w($id,$sql->itemName,'Item Database','Items','delete',$sql->itemCode);
			
			$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Item has been deleted.');
			$this->c3model->c3crud("no-res",'','','',"DELETE FROM items WHERE id='$id'");
	 	    $this->c3model->c3crud("no-res",'','','',"DELETE FROM itemVendorsRef WHERE itemID='$id'");
	 	    $this->c3model->c3crud("no-res",'','','',"DELETE FROM items_images WHERE itemID='$id'");
			
			}else{
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Item cannot be delete because it is being link to vendors or campaign.');
			}
			$data['active_page']= 1;
		}
		elseif($action=="deleteSelectedItem")
		{
			$this->modules->module_checker(18,'DELETE');
			
			$ctr=0;
			if(isset($selectedItems)){
				foreach($selectedItems as $cbr => $value)
				{
			
				$tables = array(
				  array('tbl'=>'itemVendorsRef',
						'fld'=>'itemID'),
				  array('tbl'=>'iLikeResultRef',
						'fld'=>'itemID'),
				  array('tbl'=>'iWantResultRef',
						'fld'=>'itemID'),
				  array('tbl'=>'votexRef',
						'fld'=>'itemID'),
				  array('tbl'=>'campaignItemsXref',
						'fld'=>'itemID')
						);
						
					if($this->modules->attr($tables,$value)!=0)
						$ctr++;
				}
			}
			
			if($ctr==0)
			{
				if(isset($selectedItems)){
					foreach($selectedItems as $sItems => $value)
					{
						//LOGS
						$sql = "SELECT itemName, itemCode FROM items WHERE id = $value";
						$sql = $this->db->query($sql);
						$sql = $sql->row();
						if(isset($sql->itemName))
							$CI->rec_logs->w($value,$sql->itemName,'Item Database','Items','delete',$sql->itemCode);
						
						$this->c3model->c3crud("no-res",'','','',"DELETE FROM items WHERE id='$value'");
						$this->c3model->c3crud("no-res",'','','',"DELETE FROM itemVendorsRef WHERE itemID='$value'");
						$this->c3model->c3crud("no-res",'','','',"DELETE FROM items_images WHERE itemID='$value'");
					}
					$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Multiple Items has been deleted.');
				}
			}else{
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Multiple Items cannot been delete, because it is being use in campaign or link to vendor/s.');
			}
			
			$data['active_page']= 1;
		}
		elseif($action=="edit")
		{
			$this->modules->module_checker(18,'EDIT');
			$data['id'] 	 		= $id;
			$data['POSM_statusID']	= isset($sID) ? $sID : NULL;
			
			//REFERER
			//UPDATE SUCCESS ALREADY
			$data['referrer_link']	 = $this->tag_for_purgingRedirect($_SERVER['HTTP_REFERER'],'update_success');
			//SWITCH VALIDATION
			if(isset($referrer_link)) $data['referrer_link'] = $referrer_link;
			
			$filter = $this->modules->country2();
			$data['referrer'] = $txtsearch;
			$sql 			 		= $this->db->query("SELECT *, vendors.id as vID FROM vendors INNER JOIN country ON country.id = vendors.countryID $filter");
			$data['vendors'] 		= $sql->result_array();
			
			$sql 			 		= $this->db->query("SELECT * FROM items_images WHERE itemID = $id ORDER BY defaultStatus DESC");
			$data['items_images'] 	= $sql->result_array();
			if($data['APPROVE']==TRUE)
				$data['vfile']	= 'itemFORM_BUManager.php';
			else
				$data['vfile']	= 'itemFORM.php';;
		}
		elseif($action=="preview")
		{
			$this->modules->module_checker(18,'REVIEW');
			
			$data['id'] 	 = $id;
			$data['POSM_statusID']	= isset($sID) ? $sID : NULL;
			$sql 			 = $this->db->query("SELECT *, vendors.id as vID FROM vendors INNER JOIN country ON country.id = vendors.countryID $filter");
			$data['vendors'] = $sql->result_array();
			
			$sql 			 = $this->db->query("SELECT * FROM items_images WHERE itemID = $id");
			$data['items_images'] = $sql->result_array();
			$data['vfile']	 = 'itemPreviewFORM.php';
		}
		elseif($action=="duplicate")
		{
			$this->modules->module_checker(18,'ADD');
			
			$data['id'] 	 	 = $id;
			$data['duplicate'] 	 = $id;
			$data['POSM_statusID']	= isset($sID) ? $sID : NULL;
			$sql 			 	 = $this->db->query("SELECT *, vendors.id as vID FROM vendors INNER JOIN country ON country.id = vendors.countryID $filter");
			$data['vendors'] 	 = $sql->result_array();
			
			$sql 			 	 = $this->db->query("SELECT * FROM items_images WHERE itemID = $id ORDER BY defaultStatus DESC");
			$data['items_images']= $sql->result_array();
			
			$data['vfile']	 = 'itemFORM.php';
		}
		elseif($action=="add")
		{
			$this->modules->module_checker(18,'ADD');
			$POSM_statusID			= isset($sID)			? $sID : NULL;
			$data['POSM_statusID']	= isset($POSM_statusID) ? $POSM_statusID : 154;
			$data['vfile']			= 'itemFORM.php';
			$sql 					= $this->db->query("SELECT *, vendors.id as vID FROM vendors INNER JOIN country ON country.id = vendors.countryID $filter");
			$data['vendors'] 		= $sql->result_array();
		}
		
		$sqlSTr =  "SELECT *,
			OUTLET_Status.statusName as OutletStatusName, 
			POSM_Status.statusName as POSMStatusName,
			POSM_Type.typeName as POSM_TypeName,
			items.id as itemID 
			FROM items 
			LEFT JOIN POSM_Type 		ON items.POSMTypeID = POSM_Type.id 
			LEFT JOIN POSM_Status 		ON items.POSMStatusID = POSM_Status.id 
			LEFT JOIN OUTLET_Status 	ON items.OUTLETStatusID = OUTLET_Status.id
			LEFT JOIN premiumItemType 	ON items.PremiumTypeID = premiumItemType.id 
			LEFT JOIN MATERIAL_Type 	ON items.MaterialTypeID = MATERIAL_Type.id 
			LEFT JOIN country 			ON items.countryID = country.id 
			LEFT JOIN brands  			ON items.brandID = brands.id 
			LEFT JOIN item_views  		ON items.id 	 = item_views.itemID
			WHERE publish ='n' AND irrelevant='y' AND items.purge='n' $filter  
			GROUP BY items.id ORDER BY $order";

		$sql = $this->db->query($sqlSTr);
		$sql = $sql->result_array();
		$total_rec = count($sql);

		$data['total_rec'] = $total_rec;
		$pagenum = $data['active_page'];
		$data['page_rows'] = 15; 
		$data['last'] = ceil($total_rec/$data['page_rows']);		
		$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		
		//echo $sqlSTr ." ". $max;
		$sql = $this->db->query($sqlSTr ." ". $max );
		$data['data'] = $sql->result_array();
	   
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
	
	function redirect_link($view='',$action='',$id='',$txtsearch='',$selPOSMType='',$selPOSMStatus='',$selPremiumType='',$seloutlet='',$selCountry='',$selBrand='',$selMaterial='',$items_date='',$nviews='',$sort_by_price='',$priceRange='',$priceFrom='',$priceTo='',$year='',$month='',$priceRangeID='')
	{
		extract($_POST); 
		//NEW FUNCTION
		$url="";
		$data['txtsearch'] 		= "null";
		$data['selPOSMType'] 	= "null";
		$data['selPOSMStatus'] 	= "null";
		$data['selPremiumType'] = "null";
		$data['seloutlet'] 		= "null";
		$data['selCountry'] 	= "null";
		$data['selBrand'] 		= "null";
		$data['selMaterial'] 	= "null";
		$data['items_date'] 	= "null";
		$data['nviews'] 		= "null";
		$data['sort_by_price'] 	= "null";
		$data['priceRange'] 	= "null";
		$data['priceFrom'] 		= "null";
		$data['priceTo'] 		= "null";
		$data['year'] 			= "null";
		$data['month'] 			= "null";
		$data['price_range'] 	= "null";
		$data['priceRangeID'] 	= "null";


		if($action=="page")
		{
			$this->modules->module_checker(18,'REVIEW');
			//REPOST DATA
			$txtsearch = addslashes($txtsearch);
			$txtsearch = trim($txtsearch);
			$txtsearch = str_replace("%20"," ",$txtsearch);
			$txtsearch = str_replace("'","",$txtsearch);
			$txtsearch = str_replace("/","",$txtsearch);
			if($txtsearch!='')   	
				$data['txtsearch'] 		=  $txtsearch;
			if($selPOSMType!='')   	
				$data['selPOSMType'] 	=  $selPOSMType;
			if($selPOSMStatus!='')   	
				$data['selPOSMStatus'] 	=  $selPOSMStatus;	
			if($selPremiumType!='')   	
				$data['selPremiumType'] =  $selPremiumType;	
			if($seloutlet!='')   	
				$data['seloutlet'] 		=  $seloutlet;
			if($selCountry!='')   	
				$data['selCountry'] 	=  $selCountry;
			if($selBrand!='')   	
				$data['selBrand'] 		=  $selBrand;
			if($selMaterial!='')   	
				$data['selMaterial'] 	=  $selMaterial;
			if($items_date!='')   	
				$data['items_date'] 	=  $items_date;
			if($nviews!='')   	
				$data['nviews'] 		=  $nviews;
			if($sort_by_price!='')   	
				$data['sort_by_price'] 	=  $sort_by_price;
			if($priceRange!='')   	
				$data['priceRange'] 	=  $priceRange;
			if($priceFrom!='')   	
				$data['priceFrom'] 		=  $priceFrom;
			if($priceTo!='')   	
				$data['priceTo'] 		=  $priceTo;
			if($year!='')   	
				$data['year'] 			=  $year;
			if($month!='')   	
				$data['month'] 			=  $month;
			if($priceRangeID!='')   	
				$data['priceRangeID'] 	=  $priceRangeID;
		}
		
		
		$data['url']  = $data['txtsearch']."/".$data['selPOSMType']."/".$data['selPOSMStatus']."/".$data['selPremiumType']."/".$data['seloutlet']."/".$data['selCountry']."/".$data['selBrand']."/".$data['selMaterial'].'/';
		$data['url'] .= $data['items_date']."/".$data['nviews']."/".$data['sort_by_price']."/".$data['priceRange']."/".$data['priceFrom']."/".$data['priceTo']."/".$data['year']."/".$data['month']."/".$data['priceRangeID']; 
		if($view=='itemDatabase')
		{			
			$data['searchAction'] = HTTP_PATH. "itemDatabase/items/page/$id/".$data['url']; 	
		}
		elseif($view=='BU_Marketing_Items_review')
		{
			$data['searchAction'] = HTTP_PATH. "itemDatabase/BU_Marketing_Items_review/page/$id/".$data['url']; 
		}
		elseif($view=='BU_Logistics_Items_review')
		{
			$data['searchAction'] = HTTP_PATH. "itemDatabase/BU_Logistics_Items_review/page/$id/".$data['url']; 
		}
		elseif($view=='disapproved_items')
		{
			$data['searchAction'] = HTTP_PATH. "itemDatabase/disapproved_items/page/$id/".$data['url']; 
		}
		elseif($view=='popular_items')
		{
			$data['searchAction'] = HTTP_PATH. "itemDatabase/popular_items/page/$id/".$data['url']; 
		}
		elseif($view=='items_for_purging')
		{
			$data['searchAction'] = HTTP_PATH. "itemDatabase/items_for_purging/page/$id/".$data['url']; 
		}
		elseif($view=='items_for_archiving')
		{
			$data['searchAction'] = HTTP_PATH. "itemDatabase/items_for_archiving/page/$id/".$data['url']; 
		}
		redirect($data['searchAction'], 'location', 301);
	}
	
	function label($id)
	{
		$sql ="SELECT label FROM table_fields WHERE id ='$id'";
		$query = $this->db->query($sql);
		$row = $query->row();
		return $row->label;
	}
	
	function fieldChecker($POSM_statusID,$POSM_FieldID)
	{
		$sql ="SELECT * FROM POSM_status_fields WHERE POSM_statusID = $POSM_statusID AND POSM_FieldID = $POSM_FieldID";
		$query = $this->db->query($sql);
		$row = $query->result_array();
	
		if($row!= NULL)
			return "y";
	}
	
	function field_checker($POST)
	{
		extract($POST);
		$msg="The following fields are required: ";
		
		if($POSMStatusID=='' OR $POSMStatusID==0)
			$msg.="Set Item Status, ";
		if($countryID=='') 		
			$msg.="Set Country ID, ";
		//ITEM NAME
		if($this->fieldChecker($POSMStatusID,12)=='y'){
			if($itemName=='')
				$msg.="Set Item Name, ";
		}
		//SHORT DESCRIPTION
		if($this->fieldChecker($POSMStatusID,13)=='y'){
			if($Short_Description=='')
				$msg.="Set Short Description, ";	
		}
		//ITEM TYPE
		if($POSMTypeID=='' OR $POSMTypeID==0) 		
			$msg.="Set Item Type, ";
		if($POSMTypeID==23){
			if($this->fieldChecker($POSMStatusID,6)=='y'){
				if(isset($OUTLETStatusID))
					if($OUTLETStatusID=="" OR $OUTLETStatusID==0)
						$msg.="Set Outlet Type, ";
			}
		}
		if($POSMTypeID==29){
			if($this->fieldChecker($POSMStatusID,7)=='y'){
				if(isset($PremiumTypeID))
					if($PremiumTypeID=="" OR $PremiumTypeID==0)
						$msg.="Set Premium Type, ";
			}
		}
		//MATERIAL TYPE
		if($this->fieldChecker($POSMStatusID,10)=='y'){
			if($MaterialTypeID=='' OR $MaterialTypeID==0)
				$msg.="Set Material Type, ";
		}
		//BRAND
		if($POSMStatusID==147){
			if($this->fieldChecker($POSMStatusID,4)=='y'){
				if(isset($brandID))
					if($brandID=="" OR $brandID==0)
						$msg.="Set Brand Type, ";
			}
		}
		//BRAND
		if($POSMStatusID==147 AND !isset($brandID)){
			$msg.="Set Brand Type, ";
		}
		//EXTRA FIELDS
		//FIELDS001
		if($this->fieldChecker($POSMStatusID,17)=='y'){
			if($Fields0001=='')
				$msg.="Set ".$this->label(16).", ";
		}
		//FIELDS002
		if($this->fieldChecker($POSMStatusID,18)=='y'){
			if($Fields0002=='')
				$msg.="Set ".$this->label(17).", ";
		}
		//FIELDS003
		if($this->fieldChecker($POSMStatusID,19)=='y'){
			if($Fields0003=='')
				$msg.="Set ".$this->label(18).", ";
		}
		//FIELDS004
		if($this->fieldChecker($POSMStatusID,20)=='y'){
			if($Fields0004=='')
				$msg.="Set ".$this->label(19).", ";
		}
		//FIELDS004
		if($this->fieldChecker($POSMStatusID,21)=='y'){
			if($Fields0005=='')
				$msg.="Set ".$this->label(20).", ";
		}
		
		
		$msg = ($msg=="The following fields are required: ") ? "" : substr($msg,0,-2);
		return $msg;
	}
	
	function popular_items($action='',$id='',$txtsearch='',$selPOSMType='',$selPOSMStatus='',$selPremiumType='',$seloutlet='',$selCountry='',$selBrand='',$selMaterial='',$items_date='',$nviews='',$sort_by_price='',$priceRange='',$priceFrom='',$priceTo='',$year='',$month='',$priceRangeID='',$msg='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		
		$this->modules->module_checker(18,'POPULAR');
		$filter = $this->modules->itemdb_country();
		$filter_add = $this->modules->country();
	
		$table= "items";
		$data['vfile']				= 'popular_items.php';
	    $data['title']				= 'Items | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//CRUD
		$data['ADD'] 	=  $this->modules->crud_checker(18,'ADD');
		$data['EDIT'] 	=  $this->modules->crud_checker(18,'EDIT');
		$data['DELETE'] =  $this->modules->crud_checker(18,'DELETE');
		$data['APPROVE'] =  $this->modules->crud_checker(18,'APPROVE');
		$data['ADD_TO_ARCHIVE'] =  $this->modules->crud_checker(18,'ARCHIVE');
		$data['ADD_FOR_PURGING'] =  $this->modules->crud_checker(18,'ARCHIVE');
		$data['TAG_AS_POPULAR'] =  $this->modules->crud_checker(18,'POPULAR');
		$data['DISABLE_BUTTONS'] =  FALSE;
		
		$data['VENDORS_EDIT'] 	=  $this->modules->crud_checker(26,'EDIT');
		$data['SAVE_FOR_LATER'] =  $this->modules->BU_Logistics();
		
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(5);

		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
	    $data['breadCrumbs']		= '<a href='.$HTTP_PATH.'itemDatabase> Item Database </a>';
	    $data['breadCrumbs']	   .= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']	   .= '<a href='.$HTTP_PATH.'itemDatabase/popular_items> Popular Items </a>';

		//CSRF
		$data['csrf'] = "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>"; 
		$data['searchAction'] = HTTP_PATH. "itemDatabase/popular_items.html"; 
		
		
		//search filter
		$data['post'] = $_POST; $filter2="";
		$order = "items.dateReleased DESC,";
		//print_r($_POST);
		
		$data['redirectTo']	= "popular_items";
        if(($txtsearch!='' OR $selPOSMType!='' OR $selPOSMStatus!='' OR $selPremiumType!='' OR $seloutlet!='' OR $selCountry!='' OR $selBrand!='' OR $selMaterial!='' OR $items_date=!'' OR $nviews!='' OR $sort_by_price!='' OR $priceRange!='' OR $priceRangeID!=''))		
		{ 
			extract($_POST);  
			//$_SESSION['txtsearch']="";
              if($selPOSMType!='null' 	 AND $selPOSMType!='')     	$filter2 .= " AND POSMTypeID		='$selPOSMType'";
              if($selPOSMStatus!='null'  AND $selPOSMStatus!='')   	$filter2 .= " AND POSMStatusID		='$selPOSMStatus'";
			  if($selPremiumType!='null' AND $selPremiumType!='')  	$filter2 .= " AND PremiumTypeID		='$selPremiumType'";
			  if($seloutlet!='null' 	 AND $seloutlet!='')       	$filter2 .= " AND OUTLETStatusID	='$seloutlet'";
			  if($selCountry!='null'	 AND $selCountry!='')      	$filter2 .= " AND countryID			='$selCountry'";
			  if($selBrand!='null' 		 AND $selBrand!='')        	$filter2 .= " AND brandID 			='$selBrand'";
			  if($selMaterial!='null' 	 AND $selMaterial!='')     	$filter2 .= " AND MaterialTypeID	='$selMaterial'";
			  if($priceRangeID!='null' AND $priceRangeID!='')    $filter2 .= " AND price_rangeID	    ='$priceRangeID'";
			  
			  if($txtsearch!='' AND $txtsearch!='null'){
				$txtsearch = addslashes($txtsearch);
				$txtsearch = trim($txtsearch);
				$txtsearch = str_replace("%20"," ",$txtsearch);
				$filter2 .= " AND ( itemCode like  '%$txtsearch%'  or itemName like  '%$txtsearch%'  or Short_Description like '%$txtsearch%' or Long_Description like '%$txtsearch%')";
			  }   
			  

			  //PRICE RANGE	
			  if(($priceRange!='' AND $priceRange!='null') AND is_numeric($priceFrom) AND is_numeric($priceTo))
					$filter2 = "AND $priceRange >= $priceFrom AND $priceRange <= $priceTo ";
			  
			  
			  //DATE RANGE
			  $m=0;
			  if($year!='' AND $year!='null')
				$m = $year * 12;  
			  if($month!='' AND $month!='null')
				$m += $month;
			  
			  if(($year!='' AND $year!='null' AND $year!=0)  OR ($month!='' AND $month!='null' AND $month!=0)){
				$filter2 .= " AND items.dateAdded <= CURDATE() AND items.dateAdded >= (SELECT CURDATE() - INTERVAL $m MONTH) ";
			  }
			  
			  
			   //SORT BY DATE
			   if(($nviews!='' AND $nviews!='null') OR ($sort_by_price!='' AND $sort_by_price!='null') AND ($items_date!=''  OR $items_date!='null'))
					$order = "";
			 
			   if($nviews!='null' 	 	 AND $nviews!='')          $order  .= " COUNT(item_views.itemID) $nviews,";
			   if($sort_by_price!='null' AND $sort_by_price!='')   $order  .= str_replace("-"," ",$sort_by_price.","); 
			   if($items_date!='null' 	 AND $items_date!='')      $order  .= " items.id $items_date,"; 
			  
		} 
		$order = substr($order, 0,-1);
		
		$data['post'] = $_POST; 
		
		if($filter=='' & $filter2!='') $filter = " WHERE  ". substr($filter2,4)." AND items.popular = 'y' AND items.archive = 'n' AND DATEDIFF(CURDATE(),items.dateReleased) < ".$this->modules->itemsAge()." ";
		else $filter = "$filter  $filter2 AND items.popular = 'y' AND items.archive = 'n' AND DATEDIFF(CURDATE(),items.dateReleased) < ".$this->modules->itemsAge()." "; 

		
		//TOTAL NUMBER OF ROWS
		$data['active_page']= 1;
		if($id!='') 
			$data['active_page']= $id;
		
		//NEW FUNCTION
		$url="";
		$data['txtsearch'] 		= "null";
		$data['selPOSMType'] 	= "null";
		$data['selPOSMStatus'] 	= "null";
		$data['selPremiumType'] = "null";
		$data['seloutlet'] 		= "null";
		$data['selCountry'] 	= "null";
		$data['selBrand'] 		= "null";
		$data['selMaterial'] 	= "null";
		$data['items_date'] 	= "null";
		$data['nviews'] 		= "null";
		$data['sort_by_price'] 	= "null";
		$data['priceRange'] 	= "null";
		$data['priceFrom'] 		= "null";
		$data['priceTo'] 		= "null";
		$data['year'] 			= "null";
		$data['month'] 			= "null";
		$data['priceRangeID'] 	= "null";

		if($action=="page")
		{
			$this->modules->module_checker(18,'REVIEW');
			//REPOST DATA
			if($txtsearch!='')   	
				$data['txtsearch'] 		=  $txtsearch;
			if($selPOSMType!='')   	
				$data['selPOSMType'] 	=  $selPOSMType;
			if($selPOSMStatus!='')   	
				$data['selPOSMStatus'] 	=  $selPOSMStatus;	
			if($selPremiumType!='')   	
				$data['selPremiumType'] =  $selPremiumType;	
			if($seloutlet!='')   	
				$data['seloutlet'] 		=  $seloutlet;
			if($selCountry!='')   	
				$data['selCountry'] 	=  $selCountry;
			if($selBrand!='')   	
				$data['selBrand'] 		=  $selBrand;
			if($selMaterial!='')   	
				$data['selMaterial'] 	=  $selMaterial;
			if($items_date!='')   	
				$data['items_date'] 	=  $items_date;
			if($nviews!='')   	
				$data['nviews'] 		=  $nviews;
			if($sort_by_price!='')   	
				$data['sort_by_price'] 	=  $sort_by_price;
			if($priceRange!='')   	
				$data['priceRange'] 	=  $priceRange;
			if($priceFrom!='')   	
				$data['priceFrom'] 		=  $priceFrom;
			if($priceTo!='')   	
				$data['priceTo'] 		=  $priceTo;
			if($year!='')   	
				$data['year'] 			=  $year;
			if($month!='')   	
				$data['month'] 			=  $month;
			if($priceRangeID!='')   	
				$data['priceRangeID'] 	=  $priceRangeID;
		}
		
		//SEARCH ACTION
		$data['url']  = $data['txtsearch']."/".$data['selPOSMType']."/".$data['selPOSMStatus']."/".$data['selPremiumType']."/".$data['seloutlet']."/".$data['selCountry']."/".$data['selBrand']."/".$data['selMaterial'].'/';
		$data['url'] .= $data['items_date']."/".$data['nviews']."/".$data['sort_by_price']."/".$data['priceRange']."/".$data['priceFrom']."/".$data['priceTo']."/".$data['year']."/".$data['month']."/".$data['priceRangeID']; 
		$data['searchAction'] = HTTP_PATH. "itemDatabase/redirect_link/".$data['redirectTo']."/page/1/".$data['url']; 
		
	
		//extract($_POST);
		
	    if($action=="insert_sucess")								$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Item has been save.");
		elseif($action=="update_success"    	  			     || $msg=="update_success")							$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Item has been updated.");
		elseif($action=="cannot_be_purge"   	  			     || $msg=="cannot_be_purge")						 $data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>"Item cannot be purge because it is a result of campaign or featured in Common Gallery.");
		elseif($action=="some_cannot_be_purge"   	  			 || $msg=="some_cannot_be_purge")					  $data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>"Some items cannot be purge because it is a result of campaign or featured in Common Gallery.");
		elseif($action=="cannot_be_archive" 	  			     || $msg=="cannot_be_archive")					 	 $data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>"Item cannot be archive because it is a result of campaign or featured in Common Gallery.");
		elseif($action=="some_cannot_be_archive" 	  			 || $msg=="some_cannot_be_archive")					  $data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>"Some items cannot be archive because it is a result of campaign or featured in Common Gallery.");
		elseif($action=="tagged_as_unpopular" 	  			     || $msg=="tagged_as_unpopular")  				 	 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Item has been tagged as unpopular.");
		elseif($action=="tagged_as_popular_items" 			     || $msg=="tagged_as_popular_items")  			 	 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Tagged as popular items.");
		elseif($action=="tagged_as_popular_item"  			     || $msg=="tagged_as_popular_item")  				 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Tagged as popular item.");
		elseif($action=="tagged_as_not_popular_item"  		     || $msg=="tagged_as_not_popular_item")  			 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Tagged as not popular item.");
		elseif($action=="items_has_been_submitted_for_purging"   || $msg=="items_has_been_submitted_for_purging")  	 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Items has been submitted for purging.");
		elseif($action=="item_has_been_submitted_for_purging"    || $msg=="item_has_been_submitted_for_purging")   	 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Item has been submitted for purging.");
		elseif($action=="tagged_as_not_popular_items"  		     || $msg=="tagged_as_not_popular_items")   			 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Items has tagged as not popular items.");
		elseif($action=="items_has_been_submitted_for_archiving" || $msg=="items_has_been_submitted_for_archiving")   $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Items has been submitted for archiving.");
		elseif($action=="item_has_been_submitted_for_archiving"  || $msg=="item_has_been_submitted_for_archiving")    $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Item has been submitted for archiving.");
		elseif($action=="no_selected_item" 						 || $msg=="no_selected_item")    					 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"No selected item.");
		elseif($action=="tagged_as_disapproved" 			     || $msg=="tagged_as_disapproved")    				 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Tagged as disapproved.");
		elseif($action=="item_has_been_published"   			 || $msg=="item_has_been_published")   				 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Item has been published.");
		elseif($action=="item_has_been_disapproved" 			 || $msg=="item_has_been_disapproved")   			 $data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>"Item has been disapproved.");
		elseif($action=="disapprove" 							 || $msg=="disapprove")								 $data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>"Item has been tag as irrelevant.");
		elseif($action=="insert")
		{
			$this->modules->module_checker(18,'ADD');
			
			if($_POST==NULL){
				redirect(HTTP_PATH.'itemDatabase/items/add', 'location', 301);
				die();
			}
			
			isset($Long_Description) ? $dbFields['Long_Description'] 	= $Long_Description : '';
			isset($brandID) 		 ? $dbFields['brandID'] 		  	= $brandID 			: '';
			isset($POSMTypeID) 	 	 ? $dbFields['POSMTypeID'] 	  		= $POSMTypeID 		: '';
			isset($POSMStatusID) 	 ? $dbFields['POSMStatusID'] 	  	= $POSMStatusID 	: '';
			isset($OUTLETStatusID) 	 ? $dbFields['OUTLETStatusID']   	= $OUTLETStatusID 	: '';
			isset($PremiumTypeID) 	 ? $dbFields['PremiumTypeID']    	= $PremiumTypeID 	: '';
			isset($MaterialTypeID) 	 ? $dbFields['MaterialTypeID']   	= $MaterialTypeID 	: '';
			isset($countryID) 		 ? $dbFields['countryID'] 		  	= $_SESSION['countryID'] : '';
			isset($itemName) 	     ? $dbFields['itemName']         	= addslashes($itemName) 		: '';
			isset($Short_Description)? $dbFields['Short_Description'] 	= $Short_Description: '';
			isset($UnitPrice) 		 ? $dbFields['UnitPrice'] 		  	= $UnitPrice 		: '';
			isset($USD_Price) 		 ? $dbFields['USD_Price'] 		  	= $USD_Price 		: '';
			isset($MOQ) 			 ? $dbFields['MOQ'] 			  	= $MOQ 				: '';
			isset($UOM) 		     ? $dbFields['UOM'] 			  	= $UOM 				: '';
			isset($price_rangeID) 	 ? $dbFields['price_rangeID'] 		= $price_rangeID 	: '';
			$dbFields['dateAdded']    = date('Y-m-d');
			$dbFields['dateReleased'] = ($publish=='y') ? date('Y-m-d') : '0000-00-00';
			$dbFields['user_id'] 	  = $_SESSION['user_id'];
			
			isset($Fields0001) 		 ? $dbFields['Fields0001'] 	  = $Fields0001 : '';
			isset($Fields0002) 		 ? $dbFields['Fields0002'] 	  = $Fields0002 : '';
			isset($Fields0003) 		 ? $dbFields['Fields0003'] 	  = $Fields0003 : '';
			isset($Fields0004) 		 ? $dbFields['Fields0004'] 	  = $Fields0004 : '';
			isset($Fields0005) 		 ? $dbFields['Fields0005'] 	  = $Fields0005 : '';
			isset($estimated_production_lead_time) ? $dbFields['estimated_production_lead_time'] = $estimated_production_lead_time : '';
			isset($price_validity) 	? $dbFields['price_validity'] = $price_validity : '';
			
			isset($publish) 	 	 		? $dbFields['publish'] 		  			= $publish 				 : '';
			isset($publish_other_country) 	? $dbFields['publish_other_country']  	= $publish_other_country : '';
			isset($country_of_origin) 		? $dbFields['country_of_origin'] 		= $country_of_origin 	 : '';
			
			isset($plant_inventory) 		? $dbFields['plant_inventory'] 			= $plant_inventory 		  : '';
			isset($supplier_stock_on_hand) 	? $dbFields['supplier_stock_on_hand'] 	= $supplier_stock_on_hand : '';
			isset($date_first_issue) 		? $dbFields['date_first_issue'] 		= $date_first_issue 	  : '';
			isset($date_last_used) 			? $dbFields['date_last_used'] 			= $date_last_used 		  : '';
			isset($activity_event_use) 		? $dbFields['activity_event_use'] 		= $activity_event_use 	  : '';
			
			
			//CHECK REQUIRED FIELDS
			if($publish=='y' AND $this->field_checker($_POST)!=''){
				$data['USER_MANUAL'] = $this->modules->user_manual(13);
			
				$data['POSM_statusID']	= isset($sID) ? $sID : NULL;
				$sql 			 		= $this->db->query("SELECT *, vendors.id as vID FROM vendors INNER JOIN country ON country.id = vendors.countryID $filter_add");
				$data['vendors'] 		= $sql->result_array();
				
				$data['items_images'] 	= array();
				$data['vfile']			= 'itemFORM.php';
				
				$data['POST']			= $_POST;
				$data['msg']			= array('msg_type'=>'alert-warning','msg_desc'=>$this->field_checker($_POST)); 
			}
			else
			{
				$res = $this->c3model->c3crud("insert",$table,$dbFields,'');
				
				//GET MAX ID
				$sql		= "select max(id) as max_id FROM $table";
				$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
				$maxID 	= $lastID[0]['max_id'];
				
				//UPDATE ITEM CODE
				$itemField['itemCode'] = $this->generate_ItemCode($maxID,$countryID,isset($POSMTypeID) ? $POSMTypeID : 0);
				$this->c3model->c3crud("update",$table,$itemField,$maxID);
		
				//IMAGE
				$config['upload_path'] = FCPATH2.'/img/items/';
				$config['allowed_types'] = 'gif|jpg|png';
				$this->upload->initialize($config);
				
				if($this->upload->do_multi_upload("files")){
					$files = $this->upload->get_multi_upload_data();
					
					foreach($files as $f)
					{
						extract($f);
						//GENERATE IMAGE CODE
						$new_file_name=$this->generateImgCode($maxID,$itemField['itemCode'],$f['file_name']);
						$this->imageResize($file_name,$new_file_name,$f['file_path']);
						$refdbFields['itemID'] = $maxID; 
						$refdbFields['image']  = $new_file_name; 
						
						$res = $this->c3model->c3crud("insert",'items_images',$refdbFields,'');
					}
					
					//TAG LAST IMAGE
					$query = $this->db->query("SELECT MAX(items_images.id) as lastImageID FROM items_images WHERE itemID = $maxID LIMIT 1");
					$row = $query->row();
					
					$this->db->query("UPDATE `items_images` SET  `defaultStatus` =  '1' WHERE  `items_images`.`id` = ". $row->lastImageID );
				}
				
				
				//VENDOR REFERENCE
				if(isset($multipleVendors))
				{
					$sql		= "select max(id) as max_id FROM items";
					$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
					$menu_id 	= $lastID[0]['max_id'];
					$refFields['itemID'] =  $menu_id;
					
					foreach($multipleVendors as $mV => $value)
					{
						$refFields['vendorID'] = $value;
						$res = $this->c3model->c3crud("insert",'itemVendorsRef',$refFields,'');
					}
				}
				
				//DUPLICATE IMAGES
				if(isset($duplicate)){
					$sql = "SELECT image FROM items_images WHERE itemID = $duplicate";
					$img = $this->db->query($sql);
					$img = $img->result_array();
					
					if($img){
						foreach($img as $i){
							extract($i);
							$refdbFields['itemID'] = $maxID; 
							$refdbFields['image']  = $image; 
							$res = $this->c3model->c3crud("insert",'items_images',$refdbFields,'');
						}
					}
					
					//TAG LAST IMAGE
					$this->db->query("UPDATE  `items_images` SET  `defaultStatus` =  '0' WHERE  `items_images`.`itemID` = ". $maxID );
					$query = $this->db->query("SELECT MIN(items_images.id) as lastImageID FROM items_images WHERE itemID = $maxID LIMIT 0,1");
					$row = $query->row();
					if(isset($row->lastImageID))
						$this->db->query("UPDATE  `items_images` SET  `defaultStatus` =  '1' WHERE  `items_images`.`id` = ". $row->lastImageID );				
				}	
				
				
				//LOGS
				$CI->rec_logs->w($maxID,$itemName,'Item Database','Items',isset($duplicate) ? 'duplicate' : 'add',$itemField['itemCode']);
				
				//die();
				if($publish=='n'){
					if($this->modules->module_checker2(55,'REVIEW')==true)
						redirect(HTTP_PATH.'itemDatabase/BU_Marketing_Items_review/insert_sucess', 'location', 301);
					if($this->modules->module_checker2(56,'REVIEW')==true)
						redirect(HTTP_PATH.'itemDatabase/BU_Logistics_Items_review/insert_sucess', 'location', 301);
				}else{
					redirect(HTTP_PATH.'itemDatabase/items/insert_sucess', 'location', 301);
				}
			}
		}
		elseif($action=="deleteOneItem")
		{
			$this->modules->module_checker(18,'DELETE');
			
			$tables = array(
			  array('tbl'=>'iLikeResultRef',
					'fld'=>'itemID'),
			  array('tbl'=>'iWantResultRef',
					'fld'=>'itemID'),
			  array('tbl'=>'votexRef',
					'fld'=>'itemID'),
			  array('tbl'=>'campaignItemsXref',
					'fld'=>'itemID')
					);
		
			if($this->modules->attr($tables,$id)==0)
			{
			//LOGS
			$sql = "SELECT itemName, itemCode FROM items WHERE id = $id";
			$sql = $this->db->query($sql);
			$sql = $sql->row();
			if(isset($sql->itemName))
				$CI->rec_logs->w($id,$sql->itemName,'Item Database','Items','delete',$sql->itemCode);
			
			$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Item has been deleted.');
			$this->c3model->c3crud("no-res",'','','',"DELETE FROM items WHERE id='$id'");
	 	    $this->c3model->c3crud("no-res",'','','',"DELETE FROM itemVendorsRef WHERE itemID='$id'");
	 	    $this->c3model->c3crud("no-res",'','','',"DELETE FROM items_images   WHERE itemID='$id'");
	 	    $this->c3model->c3crud("no-res",'','','',"DELETE FROM item_views     WHERE itemID='$id'");
			
			}else{
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Item cannot be delete because it is being link to campaign.');
			}
			$data['active_page']= 1;
		}
		elseif($action=="deleteSelectedItem")
		{
			$this->modules->module_checker(18,'DELETE');
			
			$ctr=0;
			if(isset($selectedItems)){
				foreach($selectedItems as $cbr => $value)
				{
			
				$tables = array(
				  array('tbl'=>'iLikeResultRef',
						'fld'=>'itemID'),
				  array('tbl'=>'iWantResultRef',
						'fld'=>'itemID'),
				  array('tbl'=>'votexRef',
						'fld'=>'itemID'),
				  array('tbl'=>'campaignItemsXref',
						'fld'=>'itemID')
						);
						
					if($this->modules->attr($tables,$value)!=0)
						$ctr++;
				}
			}
			
			if($ctr==0)
			{
				if(isset($selectedItems)){
					foreach($selectedItems as $sItems => $value)
					{
						//LOGS
						$sql = "SELECT itemName, itemCode FROM items WHERE id = $value";
						$sql = $this->db->query($sql);
						$sql = $sql->row();
						if(isset($sql->itemName))
							$CI->rec_logs->w($value,$sql->itemName,'Item Database','Items','delete',$sql->itemCode);
						
						$this->c3model->c3crud("no-res",'','','',"DELETE FROM items 		 WHERE id='$value'");
						$this->c3model->c3crud("no-res",'','','',"DELETE FROM itemVendorsRef WHERE itemID='$value'");
						$this->c3model->c3crud("no-res",'','','',"DELETE FROM items_images   WHERE itemID='$value'");
						$this->c3model->c3crud("no-res",'','','',"DELETE FROM item_views     WHERE itemID='$id'");
					}
					$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Multiple Items has been deleted.');
				}
			}else{
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Multiple Items cannot been delete, because it is being use in campaign.');
			}
			$data['active_page']= 1;
		}
		elseif($action=="edit")
		{
			$this->modules->module_checker(18,'EDIT');
			
			//USER MANUAL
			$data['USER_MANUAL'] = $this->modules->user_manual(13);
			
			//REFERER
			//UPDATE SUCCESS ALREADY
			$data['referrer_link']	 = $this->tag_for_purgingRedirect($_SERVER['HTTP_REFERER'],'update_success');
			//SWITCH VALIDATION
			if(isset($referrer_link)) $data['referrer_link'] = $referrer_link;
			
			$data['id'] 	 		= $id;
			$data['POSM_statusID']	= isset($sID) ? $sID : NULL;
			$sql 			 		= $this->db->query("SELECT *, vendors.id as vID FROM vendors INNER JOIN country ON country.id = vendors.countryID $filter_add");
			$data['vendors'] 		= $sql->result_array();
			
			$sql 			 		= $this->db->query("SELECT * FROM items_images WHERE itemID = $id ORDER BY defaultStatus DESC");
			$data['items_images'] 	= $sql->result_array();
			$data['vfile']	 		= 'itemFORM.php';
		}
		elseif($action=="preview")
		{
			$this->modules->module_checker(18,'REVIEW');
			
			$data['id'] 	 = $id;
			$data['POSM_statusID']	= isset($sID) ? $sID : NULL;
			$sql 			 = $this->db->query("SELECT *, vendors.id as vID FROM vendors INNER JOIN country ON country.id = vendors.countryID $filter_add");
			$data['vendors'] = $sql->result_array();
			
			$sql 			 = $this->db->query("SELECT * FROM items_images WHERE itemID = $id");
			$data['items_images'] = $sql->result_array();
			$data['vfile']	 = 'itemPreviewFORM.php';
		}
		elseif($action=="duplicate")
		{
			$this->modules->module_checker(18,'ADD');
			
			$data['id'] 	 	 = $id;
			$data['duplicate'] 	 = $id;
			$data['POSM_statusID']	= isset($sID) ? $sID : NULL;
			$sql 			 	 = $this->db->query("SELECT *, vendors.id as vID FROM vendors INNER JOIN country ON country.id = vendors.countryID $filter_add");
			$data['vendors'] 	 = $sql->result_array();
			
			$sql 			 	 = $this->db->query("SELECT * FROM items_images WHERE itemID = $id ORDER BY defaultStatus DESC");
			$data['items_images']= $sql->result_array();
			
			$data['vfile']	 = 'itemFORM.php';
		}
		elseif($action=="add")
		{
			$this->modules->module_checker(18,'ADD');
			
			//USER MANUAL
			$data['USER_MANUAL'] = $this->modules->user_manual(13);
			
			$POSM_statusID			= isset($sID)			? $sID : NULL;
			$data['POSM_statusID']	= isset($POSM_statusID) ? $POSM_statusID : 154;
			$data['vfile']			= 'itemFORM.php';
			$sql 					= $this->db->query("SELECT *, vendors.id as vID FROM vendors INNER JOIN country ON country.id = vendors.countryID $filter_add");
			$data['vendors'] 		= $sql->result_array();
		}
		elseif($action=="update")
		{
			if($_POST==NULL){
				redirect(HTTP_PATH.'itemDatabase/items', 'location', 301);
				die();
			}
			
			//$this->modules->module_checker(18,'EDIT');
			isset($Long_Description) ? $dbFields['Long_Description'] 	= $Long_Description : '';
			isset($brandID) 		 ? $dbFields['brandID'] 		  	= $brandID 			: '';
			isset($POSMTypeID) 	 	 ? $dbFields['POSMTypeID'] 	  		= $POSMTypeID 		: '';
			isset($POSMStatusID) 	 ? $dbFields['POSMStatusID'] 	  	= $POSMStatusID 	: '';
			isset($OUTLETStatusID) 	 ? $dbFields['OUTLETStatusID']   	= $OUTLETStatusID 	: $dbFields['OUTLETStatusID']   	= 0;
			isset($PremiumTypeID) 	 ? $dbFields['PremiumTypeID']    	= $PremiumTypeID 	: $dbFields['PremiumTypeID']    	= 0;
			isset($MaterialTypeID) 	 ? $dbFields['MaterialTypeID']   	= $MaterialTypeID 	: '';
			isset($countryID) 		 ? $dbFields['countryID'] 		  	= $countryID 		: '';
			isset($itemName) 	     ? $dbFields['itemName']         	= addslashes($itemName) : '';
			isset($Short_Description)? $dbFields['Short_Description'] 	= $Short_Description: '';
			isset($UnitPrice) 		 ? $dbFields['UnitPrice'] 		  	= $UnitPrice 		: '';
			isset($USD_Price) 		 ? $dbFields['USD_Price'] 		  	= $USD_Price 		: '';
			isset($MOQ) 			 ? $dbFields['MOQ'] 			  	= $MOQ 				: '';
			isset($UOM) 		     ? $dbFields['UOM'] 			  	= $UOM 				: '';
			isset($price_rangeID) 	 ? $dbFields['price_rangeID'] 		= $price_rangeID 	: '';
			isset($Fields0001) 		 ? $dbFields['Fields0001'] 	  = $Fields0001 : '';
			isset($Fields0002) 		 ? $dbFields['Fields0002'] 	  = $Fields0002 : '';
			isset($Fields0003) 		 ? $dbFields['Fields0003'] 	  = $Fields0003 : '';
			isset($Fields0004) 		 ? $dbFields['Fields0004'] 	  = $Fields0004 : '';
			isset($Fields0005) 		 ? $dbFields['Fields0005'] 	  = $Fields0005 : '';
			isset($estimated_production_lead_time) ? $dbFields['estimated_production_lead_time'] = $estimated_production_lead_time : '';
			isset($price_validity) 	? $dbFields['price_validity'] = $price_validity : '';
			
			isset($publish) 	 	 		? $dbFields['publish'] 		  			= $publish 				 : '';
			isset($irrelevant) 	 	 		? $dbFields['irrelevant'] 		  		= $irrelevant 			 : 'n';
			isset($publish_other_country) 	? $dbFields['publish_other_country']  	= $publish_other_country : '';
			isset($country_of_origin) 		? $dbFields['country_of_origin'] 		= $country_of_origin 	 : '';
			
			isset($plant_inventory) 		? $dbFields['plant_inventory'] 			= $plant_inventory 		  : '';
			isset($supplier_stock_on_hand) 	? $dbFields['supplier_stock_on_hand'] 	= $supplier_stock_on_hand : '';
			isset($date_first_issue) 		? $dbFields['date_first_issue'] 		= $date_first_issue 	  : '';
			isset($date_last_used) 			? $dbFields['date_last_used'] 			= $date_last_used 		  : '';
			isset($activity_event_use) 		? $dbFields['activity_event_use'] 		= $activity_event_use 	  : '';
			$dbFields['dateLastEdited']   	= date('Y-m-d');
			$dbFields['dateReleased'] 		= ($publish=='y') ? date('Y-m-d') : '0000-00-00';
			
			//SET AS POPULAR ITEM
			isset($tag_as_popular) 			? $dbFields['popular'] 			        = 'y' 		  : '';
			isset($tag_as_unpopular) 		? $dbFields['popular'] 			        = 'n' 		  : '';
			
			//CHECK REQUIRED FIELDS
			if($publish=='y' AND $this->field_checker($_POST)!=''){
				$data['USER_MANUAL'] = $this->modules->user_manual(13);
			
				$data['id'] 	 		= $id;
				$data['POSM_statusID']	= isset($sID) ? $sID : NULL;
				$sql 			 		= $this->db->query("SELECT *, vendors.id as vID FROM vendors INNER JOIN country ON country.id = vendors.countryID $filter_add");
				$data['vendors'] 		= $sql->result_array();
				
				$sql 			 		= $this->db->query("SELECT * FROM items_images WHERE itemID = $id ORDER BY defaultStatus DESC");
				$data['items_images'] 	= $sql->result_array();
				$data['vfile']			= 'itemFORM.php';
				
				$data['POST']			= $_POST;
				$data['msg']			= array('msg_type'=>'alert-warning','msg_desc'=>$this->field_checker($_POST)); 
			}
			else
			{
				//UPDATE ITEM CODE
				$itemField['itemCode'] = $this->generate_ItemCode($id,$countryID,isset($POSMTypeID) ? $POSMTypeID : 0);
				$this->c3model->c3crud("update",$table,$itemField,$id);
				
				//IMAGE
				$config['upload_path'] = FCPATH2.'/img/items/';
				$config['allowed_types'] = 'gif|jpg|png';
				$this->upload->initialize($config);
				
				if($this->upload->do_multi_upload("files")){
					//Print data for all uploaded files.
					//print_r($this->upload->get_multi_upload_data());
					
					$files = $this->upload->get_multi_upload_data();
					foreach($files as $f)
					{
						extract($f); 
						//GENERATE IMAGE CODE
						$new_file_name=$this->generateImgCode($id,$itemField['itemCode'],$f['file_name']);
						$this->imageResize($file_name,$new_file_name,$f['file_path']);
						$refdbFields['itemID'] = $id; 
						$refdbFields['image']  = $new_file_name; 
						
						$res = $this->c3model->c3crud("insert",'items_images',$refdbFields,'');
					}
					
					//TAG LAST IMAGE
					$this->db->query("UPDATE  `items_images` SET  `defaultStatus` =  '0' WHERE  itemID = $id");
					$query = $this->db->query("SELECT MAX(items_images.id) as lastImageID FROM items_images WHERE itemID = $id LIMIT 1");
					$row = $query->row();

					$this->db->query("UPDATE  `items_images` SET  `defaultStatus` =  '1' WHERE  `items_images`.`id` = ". $row->lastImageID );
				}
				
				$this->c3model->c3crud("update",$table,$dbFields,$id);
				
				//VENDOR REFERENCE
				//DELETE PREVIUOS VENDORS
				if($data['VENDORS_EDIT']==TRUE){
					$this->c3model->c3crud("no-res",'','','',"DELETE FROM itemVendorsRef WHERE itemID='$id'");
				
					$refFields['itemID'] =  $id;
					
					if(isset($multipleVendors)){
						foreach($multipleVendors as $mV => $value)
						{
							$refFields['vendorID'] = $value;
							$res = $this->c3model->c3crud("insert",'itemVendorsRef',$refFields,'');
						}
					}
				}
			
				//LOGS
				if(!isset($itemName)){
					$sql = "SELECT itemName FROM items WHERE id = $id";
					$sql = $this->db->query($sql);
					$row = $sql->row();
					$itemName = $row->itemName;
				}
					
				$CI->rec_logs->w($id,$itemName,'Item Database','Items','edit',$itemField['itemCode']);
				
				$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Item has been updated.');
				
				if($dbFields['irrelevant']=='y')
					redirect(HTTP_PATH.'itemDatabase/disapproved_items/disapprove', 'refresh');
				
				if($publish=='n'){
					if($this->modules->module_checker2(55,'REVIEW')==true)
						redirect(HTTP_PATH.'itemDatabase/BU_Marketing_Items_review/update_success', 'location', 301);
					if($this->modules->module_checker2(56,'REVIEW')==true)
						redirect(HTTP_PATH.'itemDatabase/BU_Logistics_Items_review/update_success', 'location', 301);
				}else{
					redirect(HTTP_PATH.'itemDatabase/items/update_success', 'location', 301);
				}
			}
		}
		
		$sqlSTr =  "SELECT *,
					OUTLET_Status.statusName as OutletStatusName, 
					POSM_Status.statusName as POSMStatusName,
					POSM_Type.typeName as POSM_TypeName,
					items.id as itemID,  count(item_views.itemID) as iViews 
					FROM items 
					LEFT JOIN POSM_Type 		ON items.POSMTypeID = POSM_Type.id 
					LEFT JOIN POSM_Status 		ON items.POSMStatusID = POSM_Status.id 
					LEFT JOIN OUTLET_Status 	ON items.OUTLETStatusID = OUTLET_Status.id
					LEFT JOIN premiumItemType 	ON items.PremiumTypeID = premiumItemType.id 
					LEFT JOIN MATERIAL_Type 	ON items.MaterialTypeID = MATERIAL_Type.id 
					LEFT JOIN country 			ON items.countryID = country.id 
					LEFT JOIN brands  			ON items.brandID = brands.id 
					LEFT JOIN item_views  		ON items.id 	 = item_views.itemID
					$filter 
					GROUP BY items.id ORDER BY $order";

		$sql = $this->db->query($sqlSTr);
		$sql = $sql->result_array();
		$total_rec = count($sql);
		
		$data['total_rec'] = $total_rec;
		$pagenum 		   = $data['active_page'];
		$data['page_rows'] = 15; 
		$data['last'] 	   = ceil($total_rec/$data['page_rows']);		
		$max 			   = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		
		$sql = $this->db->query($sqlSTr ." ". $max );
		$data['data'] = $sql->result_array();
	   
	    if($this->modules->access_checker()==TRUE)
	    {
			$this->load->view('innerPages',$data); 
		}
		else
		{
		$data['vfile']				= 'login.php';
		$data['title']				= 'SMBi System Log-in | SMBi';
		$data['page_title']			= 'SMBi System Log-in';
		$data['meta_description']	= 'San Miguel Brewing International';
		$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
		$this->load->view('login',$data); 	
	   }
	}
	
	function SetAsNotPopularItems($referrer='',$id='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		
		extract($_POST);
		if(isset($selectedItems)){
			foreach($selectedItems as $sItems => $value)
			{
				//LOGS
				$sql = "SELECT itemName, itemCode FROM items WHERE id = $value";
				$sql = $this->db->query($sql);
				$sql = $sql->row();
				if(isset($sql->itemName))
					$CI->rec_logs->w($value,$sql->itemName,'Item Database','Items','unpopular',$sql->itemCode);
				
				$this->c3model->c3crud("no-res",'','','',"UPDATE items SET popular='n', dateLastEdited='CURDATE()' WHERE id='$value'");
			}
			redirect($this->tag_for_purgingRedirect($_SERVER['HTTP_REFERER'],'tagged_as_not_popular_items'),'location',301);
		}
		elseif($id!='')
		{
			//LOGS
			$sql = "SELECT itemName, itemCode FROM items WHERE id = $id";
			$sql = $this->db->query($sql);
			$sql = $sql->row();
			if(isset($sql->itemName))
				$CI->rec_logs->w($id,$sql->itemName,'Item Database','Items','popular',$sql->itemCode);
			
			$this->c3model->c3crud("no-res",'','','',"UPDATE items SET popular='n', dateLastEdited='CURDATE()' WHERE id='$id'");
			redirect($this->tag_for_purgingRedirect($_SERVER['HTTP_REFERER'],'tagged_as_not_popular_item'),'location',301);
		}
		else
		{
			redirect($this->tag_for_purgingRedirect($_SERVER['HTTP_REFERER'],'no_selected_item'),'location',301);
		}
	}
	
	function SetAsPopularItems($id='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		
		extract($_POST);
		if(isset($selectedItems)){
			foreach($selectedItems as $sItems => $value)
			{
				//LOGS
				$sql = "SELECT itemName, itemCode FROM items WHERE id = $value";
				$sql = $this->db->query($sql);
				$sql = $sql->row();
				if(isset($sql->itemName))
					$CI->rec_logs->w($value,$sql->itemName,'Item Database','Items','popular',$sql->itemCode);
				
				$this->c3model->c3crud("no-res",'','','',"UPDATE items SET popular='y', dateLastEdited='CURDATE()' WHERE id='$value'");
			}
			redirect($this->tag_for_purgingRedirect($_SERVER['HTTP_REFERER'],'tagged_as_popular_items'),'location',301);
		}
		elseif($id!='')
		{
			//LOGS
			$sql = "SELECT itemName, itemCode FROM items WHERE id = $id";
			$sql = $this->db->query($sql);
			$sql = $sql->row();
			if(isset($sql->itemName))
				$CI->rec_logs->w($id,$sql->itemName,'Item Database','Items','popular',$sql->itemCode);
			
			$this->c3model->c3crud("no-res",'','','',"UPDATE items SET popular='y', dateLastEdited='CURDATE()' WHERE id='$id'");
			redirect($this->tag_for_purgingRedirect($_SERVER['HTTP_REFERER'],'tagged_as_popular_item'),'location',301);
		}
		else
		{
			redirect($this->tag_for_purgingRedirect($_SERVER['HTTP_REFERER'],'no_selected_item'),'location',301);
		}
	}
	
	function tag_for_purging($referer='',$action='',$id='')
	{
		$this->modules->module_checker(18,'DELETE');
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		
		$current_date=date('Y-m-d');
		extract($_POST);
		switch($referer){
			case 'items':
			//ONE ITEM
			if($action=='purgeOneItem')
			{
				$tables = array(
				  array('tbl'=>'iLikeResultRef',
						'fld'=>'itemID'),
				  array('tbl'=>'iWantResultRef',
						'fld'=>'itemID'));
			
				if($this->modules->attr($tables,$id)==0 AND $this->modules->common_gallery_item($id)==FALSE)
				{
					//LOGS
					$sql = "SELECT itemName, itemCode FROM items WHERE id = $id";
					$sql = $this->db->query($sql);
					$sql = $sql->row();
					
					$this->db->query("UPDATE items SET `purge` = 'y' , `archive` = 'n',`popular` = 'n', dateLastEdited='$current_date' WHERE id = $id");
					if(isset($sql->itemName))
						$CI->rec_logs->w($id,$sql->itemName,'Item Database','Items','for purging',$sql->itemCode);
					
					//PURGE ARCHIVE REFERENCE
					$this->purgeArchive_Ref($id,"submit for purging");
					redirect($this->tag_for_purgingRedirect($_SERVER['HTTP_REFERER'],"item_has_been_submitted_for_purging"), 'location', 301);
				}else{
					redirect($this->tag_for_purgingRedirect($_SERVER['HTTP_REFERER'],"cannot_be_purge"), 'location', 301);
				}
			}
			if($action=='purgeSelectedItems')
			{
				//MULTIPLE
				$ctr=0;
				if(isset($selectedItems)){
					foreach($selectedItems as $cbr => $value)
					{
					 $tables = array(
					  array('tbl'=>'iLikeResultRef',
							'fld'=>'itemID'),
					  array('tbl'=>'iWantResultRef',
							'fld'=>'itemID'));
					  
					  if($this->modules->attr($tables,$value)==0 AND $this->modules->common_gallery_item($value)==FALSE){
						//LOGS
						$sql = "SELECT itemName, itemCode FROM items WHERE id = $value";
						$sql = $this->db->query($sql);
						$sql = $sql->row();
						
						$this->db->query("UPDATE items SET `purge` = 'y',`archive` = 'n',`popular` = 'n', dateLastEdited='$current_date' WHERE id = $value");
						if(isset($sql->itemName))
							$CI->rec_logs->w($value,$sql->itemName,'Item Database','Items','for purging',$sql->itemCode);
						
						//PURGE ARCHIVE REFERENCE
						$this->purgeArchive_Ref($value,"submit for purging");
					  }else{
					    $ctr++;
					  }  
					}
				}
				
				if($ctr==0)
				{
					if(isset($selectedItems)){
						redirect($this->tag_for_purgingRedirect($_SERVER['HTTP_REFERER'],"items_has_been_submitted_for_purging"), 'location', 301);
					}
					redirect($this->tag_for_purgingRedirect($_SERVER['HTTP_REFERER'],"no_selected_item"), 'location', 301);
				}else{
					redirect($this->tag_for_purgingRedirect($_SERVER['HTTP_REFERER'],"some_cannot_be_purge"), 'location', 301);
				}
			}
			break;
			case 'popular_items':
			//ONE ITEM
			if($action=='purgeOneItem')
			{
				$tables = array(
				  array('tbl'=>'iLikeResultRef',
						'fld'=>'itemID'),
				  array('tbl'=>'iWantResultRef',
						'fld'=>'itemID'));
			
				if($this->modules->attr($tables,$id)==0 AND $this->modules->common_gallery_item($id)==FALSE)
				{
					//LOGS
					$sql = "SELECT itemName, itemCode FROM items WHERE id = $id";
					$sql = $this->db->query($sql);
					$sql = $sql->row();
					
					$this->db->query("UPDATE items SET `purge` = 'y' , `archive` = 'n',`popular` = 'n', dateLastEdited='$current_date' WHERE id = $id");
					if(isset($sql->itemName))
						$CI->rec_logs->w($id,$sql->itemName,'Item Database','Items','for purging',$sql->itemCode);
					
					//PURGE ARCHIVE REFERENCE
					$this->purgeArchive_Ref($id,"submit for purging");
					redirect($this->tag_for_purgingRedirect($_SERVER['HTTP_REFERER'],"item_has_been_submitted_for_purging"), 'location', 301);
				}else{
					redirect($this->tag_for_purgingRedirect($_SERVER['HTTP_REFERER'],"cannot_be_purge"), 'location', 301);
				}
			}
			if($action=='purgeSelectedItems')
			{
				//MULTIPLE
				$ctr=0;
				if(isset($selectedItems)){
					foreach($selectedItems as $cbr => $value)
					{
					 $tables = array(
					  array('tbl'=>'iLikeResultRef',
							'fld'=>'itemID'),
					  array('tbl'=>'iWantResultRef',
							'fld'=>'itemID'));
					  
					  if($this->modules->attr($tables,$value)==0 AND $this->modules->common_gallery_item($value)==FALSE){
						//LOGS
						$sql = "SELECT itemName, itemCode FROM items WHERE id = $value";
						$sql = $this->db->query($sql);
						$sql = $sql->row();
						
						$this->db->query("UPDATE items SET `purge` = 'y',`archive` = 'n',`popular` = 'n', dateLastEdited='$current_date' WHERE id = $value");
						if(isset($sql->itemName))
							$CI->rec_logs->w($value,$sql->itemName,'Item Database','Items','for purging',$sql->itemCode);
						
						//PURGE ARCHIVE REFERENCE
						$this->purgeArchive_Ref($value,"submit for purging");
					  }else{
					    $ctr++;
					  }  
					}
				}
				
				if($ctr==0)
				{
					if(isset($selectedItems)){
						redirect($this->tag_for_purgingRedirect($_SERVER['HTTP_REFERER'],"items_has_been_submitted_for_purging"), 'location', 301);
					}
					redirect($this->tag_for_purgingRedirect($_SERVER['HTTP_REFERER'],"no_selected_item"), 'location', 301);
				}else{
					redirect($this->tag_for_purgingRedirect($_SERVER['HTTP_REFERER'],"some_cannot_be_purge"), 'location', 301);
				}
			}
			break;
			case 'items_for_archiving':
			//ONE ITEM
			if($action=='purgeOneItem')
			{
				$tables = array(
				  array('tbl'=>'iLikeResultRef',
						'fld'=>'itemID'),
				  array('tbl'=>'iWantResultRef',
						'fld'=>'itemID'));
			
				if($this->modules->attr($tables,$id)==0 AND $this->modules->common_gallery_item($id)==FALSE)
				{
					//LOGS
					$sql = "SELECT itemName, itemCode FROM items WHERE id = $id";
					$sql = $this->db->query($sql);
					$sql = $sql->row();
					
					$this->db->query("UPDATE items SET `purge` = 'y',`archive` = 'n',`popular` = 'n', dateLastEdited='$current_date' WHERE id = $id");
					if(isset($sql->itemName))
						$CI->rec_logs->w($id,$sql->itemName,'Item Database','Items','for purging',$sql->itemCode);
					
					//PURGE ARCHIVE REFERENCE
					$this->purgeArchive_Ref($id,"submit for purging");
					redirect($this->tag_for_purgingRedirect($_SERVER['HTTP_REFERER'],"item_has_been_submitted_for_purging"), 'location', 301);
				}else{
					redirect($this->tag_for_purgingRedirect($_SERVER['HTTP_REFERER'],"cannot_be_purge"), 'location', 301);
				}
			}
			if($action=='purgeSelectedItems')
			{
				//MULTIPLE
				$ctr=0;
				if(isset($selectedItems)){
					foreach($selectedItems as $cbr => $value)
					{
					 $tables = array(
					  array('tbl'=>'iLikeResultRef',
							'fld'=>'itemID'),
					  array('tbl'=>'iWantResultRef',
							'fld'=>'itemID'));
					  
					  if($this->modules->attr($tables,$value)==0 AND $this->modules->common_gallery_item($value)==FALSE){
						//LOGS
						$sql = "SELECT itemName, itemCode FROM items WHERE id = $value";
						$sql = $this->db->query($sql);
						$sql = $sql->row();
						
						$this->db->query("UPDATE items SET `purge` = 'y',`archive` = 'n',`popular` = 'n', dateLastEdited='$current_date' WHERE id = $value");
						if(isset($sql->itemName))
							$CI->rec_logs->w($value,$sql->itemName,'Item Database','Items','for purging',$sql->itemCode);
						
						//PURGE ARCHIVE REFERENCE
						$this->purgeArchive_Ref($value,"submit for purging");
					  }else{
					    $ctr++;
					  }  
					}
				}
				
				if($ctr==0)
				{
					if(isset($selectedItems)){
						redirect($this->tag_for_purgingRedirect($_SERVER['HTTP_REFERER'],"items_has_been_submitted_for_purging"), 'location', 301);
					}
					redirect($this->tag_for_purgingRedirect($_SERVER['HTTP_REFERER'],"no_selected_item"), 'location', 301);
				}else{
					redirect($this->tag_for_purgingRedirect($_SERVER['HTTP_REFERER'],"some_cannot_be_purge"), 'location', 301);
				}
			}
			break;
			case 'BU_Marketing_Items_review':
			//ONE ITEM
			if($action=='purgeOneItem')
			{
				//LOGS
				$sql = "SELECT itemName, itemCode FROM items WHERE id = $id";
				$sql = $this->db->query($sql);
				$sql = $sql->row();
						
				$this->db->query("UPDATE items SET `purge` = 'y',`archive` = 'n',`popular` = 'n', dateLastEdited='$current_date' WHERE id = $id");
				if(isset($sql->itemName))
					$CI->rec_logs->w($id,$sql->itemName,'Item Database','Items','for purging',$sql->itemCode);
				
				//PURGE ARCHIVE REFERENCE
				$this->purgeArchive_Ref($id,"submit for purging");
				redirect($this->tag_for_purgingRedirect($_SERVER['HTTP_REFERER'],"item_has_been_submitted_for_purging"), 'location', 301);
			}
			if($action=='purgeSelectedItems')
			{
				//MULTIPLE				
				if(isset($selectedItems)){
				 foreach($selectedItems as $sItems => $value)
				 {
				  //LOGS
				  $sql = "SELECT itemName, itemCode FROM items WHERE id = $value";
				  $sql = $this->db->query($sql);
				  $sql = $sql->row();
		
				  $this->db->query("UPDATE items SET `purge` = 'y',`archive` = 'n',`popular` = 'n', dateLastEdited='$current_date' WHERE id = $value");
				  if(isset($sql->itemName))
				  $CI->rec_logs->w($value,$sql->itemName,'Item Database','Items','for purging',$sql->itemCode);
				 
				  //PURGE ARCHIVE REFERENCE
				  $this->purgeArchive_Ref($value,"submit for purging");
				 }
				 redirect($this->tag_for_purgingRedirect($_SERVER['HTTP_REFERER'],"items_has_been_submitted_for_purging"), 'location', 301);
				}else{
					redirect($this->tag_for_purgingRedirect($_SERVER['HTTP_REFERER'],"cannot_be_purge"), 'location', 301);
				}
			}
			break;
			case 'disapproved_items':
			//ONE ITEM
			if($action=='purgeOneItem')
			{
				//LOGS
				$sql = "SELECT itemName, itemCode FROM items WHERE id = $id";
				$sql = $this->db->query($sql);
				$sql = $sql->row();
						
				$this->db->query("UPDATE items SET `purge` = 'y',`archive` = 'n',`popular` = 'n', dateLastEdited='$current_date' WHERE id = $id");
				if(isset($sql->itemName))
					$CI->rec_logs->w($id,$sql->itemName,'Item Database','Items','for purging',$sql->itemCode);
				
				//PURGE ARCHIVE REFERENCE
				$this->purgeArchive_Ref($id,"submit for purging");
				redirect($this->tag_for_purgingRedirect($_SERVER['HTTP_REFERER'],"item_has_been_submitted_for_purging"), 'location', 301);
			}
			if($action=='purgeSelectedItems')
			{
				//MULTIPLE				
				if(isset($selectedItems)){
				 foreach($selectedItems as $sItems => $value)
				 {
				  //LOGS
				  $sql = "SELECT itemName, itemCode FROM items WHERE id = $value";
				  $sql = $this->db->query($sql);
				  $sql = $sql->row();
		
				  $this->db->query("UPDATE items SET `purge` = 'y',`archive` = 'n',`popular` = 'n', dateLastEdited='$current_date' WHERE id = $value");
				  if(isset($sql->itemName))
				  $CI->rec_logs->w($value,$sql->itemName,'Item Database','Items','for purging',$sql->itemCode);
				  
				  //PURGE ARCHIVE REFERENCE
				  $this->purgeArchive_Ref($value,"submit for purging");
				 }
				 redirect($this->tag_for_purgingRedirect($_SERVER['HTTP_REFERER'],"items_has_been_submitted_for_purging"), 'location', 301);
				}else{
					redirect($this->tag_for_purgingRedirect($_SERVER['HTTP_REFERER'],"cannot_be_purge"), 'location', 301);
				}
			}
			case 'BU_Logistics_Items_review':
			//ONE ITEM
			if($action=='purgeOneItem')
			{
				//LOGS
				$sql = "SELECT itemName, itemCode, publish FROM items WHERE id = $id";
				$sql = $this->db->query($sql);
				$row = $sql->row();
			    
				//PUBLISH
				if($row->publish=='y')
				{
					$tables = array(
					  array('tbl'=>'iLikeResultRef',
							'fld'=>'itemID'),
					  array('tbl'=>'iWantResultRef',
							'fld'=>'itemID'));
				
					if($this->modules->attr($tables,$id)==0 AND $this->modules->common_gallery_item($id)==FALSE)
					{
						//LOGS
						$sql = "SELECT itemName, itemCode FROM items WHERE id = $id";
						$sql = $this->db->query($sql);
						$sql = $sql->row();
						
						$this->db->query("UPDATE items SET `purge` = 'y',`archive` = 'n',`popular` = 'n', dateLastEdited='$current_date' WHERE id = $id");
						if(isset($sql->itemName))
							$CI->rec_logs->w($id,$sql->itemName,'Item Database','Items','for purging',$sql->itemCode);
						
						//PURGE ARCHIVE REFERENCE
						$this->purgeArchive_Ref($id,"submit for purging");
						redirect($this->tag_for_purgingRedirect($_SERVER['HTTP_REFERER'],"item_has_been_submitted_for_purging"), 'location', 301);
					}else{
						redirect($this->tag_for_purgingRedirect($_SERVER['HTTP_REFERER'],"cannot_be_purge"), 'location', 301);
					}
				}
				else
				{
					$this->db->query("UPDATE items SET `purge` = 'y',`archive` = 'n',`popular` = 'n', dateLastEdited='$current_date' WHERE id = $id");
					if(isset($row->itemName))
						$CI->rec_logs->w($id,$row->itemName,'Item Database','Items','for purging',$row->itemCode);
					
					//PURGE ARCHIVE REFERENCE
					$this->purgeArchive_Ref($id,"submit for purging");
					redirect($this->tag_for_purgingRedirect($_SERVER['HTTP_REFERER'],"item_has_been_submitted_for_purging"), 'location', 301);
				}
			
			}
			if($action=='purgeSelectedItems')
			{
				//MULTIPLE
				$ctr=0;
				if(isset($selectedItems)){
					foreach($selectedItems as $cbr => $value)
					{
					 $tables = array(
					  array('tbl'=>'iLikeResultRef',
							'fld'=>'itemID'),
					  array('tbl'=>'iWantResultRef',
							'fld'=>'itemID'));
					  
					  if($this->modules->attr($tables,$value)==0 AND $this->modules->common_gallery_item($value)==FALSE){
						//LOGS
						$sql = "SELECT itemName, itemCode FROM items WHERE id = $value";
						$sql = $this->db->query($sql);
						$sql = $sql->row();
						
						$this->db->query("UPDATE items SET `purge` = 'y',`archive` = 'n',`popular` = 'n', dateLastEdited='$current_date' WHERE id = $value");
						if(isset($sql->itemName))
							$CI->rec_logs->w($value,$sql->itemName,'Item Database','Items','for purging',$sql->itemCode);
						
						//PURGE ARCHIVE REFERENCE
						$this->purgeArchive_Ref($value,"submit for purging");
					  }else{
					    $ctr++;
					  }  
					}
				}
				
				if($ctr==0)
				{
					if(isset($selectedItems)){
						redirect($this->tag_for_purgingRedirect($_SERVER['HTTP_REFERER'],"items_has_been_submitted_for_purging"), 'location', 301);
					}
					redirect($this->tag_for_purgingRedirect($_SERVER['HTTP_REFERER'],"no_selected_item"), 'location', 301);
				}else{
					redirect($this->tag_for_purgingRedirect($_SERVER['HTTP_REFERER'],"some_cannot_be_purge"), 'location', 301);
				}
			}
			break;
		}
		
	}
	
	function purgeArchive_Ref($itemID='',$action='',$msg='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		//PURGE
		$dbFields['itemID'] = $itemID;
		$dbFields['userID'] = $_SESSION['user_id'];
		//restore
		if($action=='restore'){
		 $dbFields['notes']	= $msg;
		 $sql = $this->db->query("SELECT userID FROM purgeArchive_Ref WHERE itemID = $itemID AND purgeArchive_Ref.table='PURGE' AND action='submit for purging'");
		 $sql = $sql->row();
		 if($sql->userID)$dbFields['userID'] = $sql->userID;
		 //DEL SUBMIT FOR PURGING DATA
		 $this->db->query("DELETE FROM purgeArchive_Ref WHERE itemID = $itemID AND purgeArchive_Ref.table='PURGE' AND action='submit for purging'");
		 //INSERT TO LOGS
		 $sql = "SELECT itemName, itemCode FROM items WHERE id = $itemID";
		 $sql = $this->db->query($sql);
		 $sql = $sql->row();
		 $CI->rec_logs->w($itemID,$sql->itemName,'Item Database','Items','restore from purging',$sql->itemCode);
		}
		elseif($action=='submit for purging')
		{
		//DELETE RESTORE DATA
		$this->db->query("DELETE FROM purgeArchive_Ref WHERE itemID = $itemID AND purgeArchive_Ref.table='PURGE' AND action='restore'");
		}
		$dbFields['table']  = "PURGE";
		$dbFields['action'] = $action;
		$dbFields['date']   = date('Y-m-d');
		$dbFields['time']   = date('H:m:s');
		$res = $this->c3model->c3crud("insert","purgeArchive_Ref",$dbFields,'');
	}
	
	function items_for_purgingFORM($action='',$id='',$gallery='')
	{
	   $data['DELETE'] 	   	   =  $this->modules->crud_checker(80,'DELETE');
	   $data['RESTORE'] 	   =  $this->modules->crud_checker(80,'RESTORE');
	   $data['VENDORS_REVIEW'] = $this->modules->crud_checker(26,'REVIEW');
	   
	   $data['jUi'] 	     = true;
	   $data['id'] 	         = $id;
	   $sql 			     = $this->db->query("SELECT * FROM items_images WHERE itemID = $id ORDER BY defaultStatus DESC");
	   $data['items_images'] = $sql->result_array();

	   $sqlSTr="SELECT *,
				OUTLET_Status.statusName as OutletStatusName, 
				POSM_Status.statusName   as POSMStatusName,
				items.id 				 as itemID,  
				admin_users.full_name 	 as fname,
				items.dateAdded as uploaded_Date,
				items.dateReleased as released_Date,
				(SELECT COUNT(id) FROM item_views WHERE item_views.itemID = items.id) as tot_views
				FROM items  
				LEFT JOIN POSM_Type 		ON items.POSMTypeID = POSM_Type.id 
				LEFT JOIN POSM_Status 		ON items.POSMStatusID = POSM_Status.id 
				LEFT JOIN OUTLET_Status 	ON items.OUTLETStatusID = OUTLET_Status.id
				LEFT JOIN premiumItemType 	ON items.PremiumTypeID = premiumItemType.id 
				LEFT JOIN MATERIAL_Type 	ON items.MaterialTypeID = MATERIAL_Type.id 
				LEFT JOIN country 			ON items.countryID = country.id 
				LEFT JOIN brands  			ON items.brandID = brands.id  
				LEFT JOIN countries  		ON items.country_of_origin = countries.id  
				LEFT JOIN admin_users  		ON items.user_id = admin_users.id where items.id='$id'";
	   $sql 			     = $this->db->query($sqlSTr);
	  
	   $item = $sql->result_array();
	  
	   $data['item'] 			 = $item;
	   
	   $data['galTitle']	     = isset($item[0]['itemName']) ? $item[0]['itemName'] : 'No Title';
	   $itemName				 = isset($item[0]['itemName']) ? $item[0]['itemName'] : 'No Title';
	   $data['breadCrumbs']		 = "";
	  
		switch($gallery){
			case 'purge':
				$data['breadCrumbs']	= '<li><img src="'. HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
				$data['breadCrumbs']   .= '<a href='. HTTP_PATH.'itemDatabase/items> Item Database </a>';
				$data['breadCrumbs']   .= '<li><img src="'. HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
				$data['breadCrumbs']   .= '<a href='. HTTP_PATH.'itemDatabase/items_for_purging> Items for Purging </a>';
			break;
		}
		
		extract($_POST);
		if($action=='edit')
		{
		 $data['vfile']	 = 'items_for_purgingFORM.php';
		 $data['id'] 	 = $id;
		}
		elseif($action=='update')
		{
		 if(isset($restore_to_item_db))
		 {
		  $this->purgeArchive_Ref($id,'restore');
		  $itemField['purge'] = 'n';
		  $this->c3model->c3crud("update","items",$itemField,$id);
		  //ITEMS FOR PURGING
		  redirect(HTTP_PATH.'itemDatabase/items_for_purging/restore_to_item_db', 'location', 301);
		 }
		 elseif(isset($permanent_delete))
		 {
			$this->c3model->c3crud("no-res",'','','',"DELETE FROM purgeArchive_Ref  WHERE itemID='$id' AND action='submit for purging' AND purgeArchive_Ref.table='PURGE'");
			$this->c3model->c3crud("no-res",'','','',"DELETE FROM items 		    WHERE id='$id'");
	 	    $this->c3model->c3crud("no-res",'','','',"DELETE FROM itemVendorsRef    WHERE itemID='$id'");
	 	    $this->c3model->c3crud("no-res",'','','',"DELETE FROM items_images      WHERE itemID='$id'");
	 	    $this->c3model->c3crud("no-res",'','','',"DELETE FROM item_views        WHERE itemID='$id'");
			//ITEMS FOR PURGING
			redirect(HTTP_PATH.'itemDatabase/items_for_purging/permanent_delete', 'location', 301);
		 }
		}
	   
	   $data['breadCrumbs']    .= '<li><img src="'. HTTP_PATH .'img/arrow.png" width="3" height="5"></li> '.$itemName;
	   $data['itemPreview']	   = true;
	   
	   $sql = $this->db->query("SELECT *, vendors.id as vID, (SELECT countryName FROM country WHERE country.id = vendors.countryID) AS cName FROM itemVendorsRef 
								LEFT JOIN vendors ON vendors.id = itemVendorsRef.vendorID 
								WHERE itemVendorsRef.itemID = $id");
	   $data['vendors'] = $sql->result_array();
	   
	    if($this->modules->access_checker()==TRUE)
	    {
			$this->load->view('galleryHeader_Item_info',$data); 
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
	
	function minMaxDateUploaded($type='')
	{
		//GET CURRENT
		$curDate = date('Y-m-d');
		if($_SESSION['super_admin']!='y' AND $_SESSION['countryID']!=0){
			$sql = $this->db->query("SELECT min(dUploaded) AS minDate FROM item_db_reports WHERE cID =".$_SESSION['countryID'] ." LIMIT 0,1");
		}else{
			$sql = $this->db->query("SELECT min(dUploaded) AS minDate FROM item_db_reports WHERE cID != 0 LIMIT 0,1");
		}
		$sql	   = $sql->row();
		$prevMonth = $sql->minDate;
		
		if($_SESSION['super_admin']!='y' AND $_SESSION['countryID']!=0){
			$sql = $this->db->query("SELECT max(dUploaded) AS maxDate FROM item_db_reports WHERE cID =".$_SESSION['countryID'] ." LIMIT 0,1");
		}else{
			$sql = $this->db->query("SELECT max(dUploaded) AS maxDate FROM item_db_reports WHERE cID != 0 LIMIT 0,1");
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
	
	function items_for_purgingActions($action='',$id='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		extract($_POST);
		if($action=="deleteOneItem")
		{
			$this->modules->module_checker(18,'PURGE APPROVAL');
			
			//LOGS
			$sql = "SELECT itemName, itemCode FROM items WHERE id = $id";
			$sql = $this->db->query($sql);
			$sql = $sql->row();
			$CI->rec_logs->w($id,$sql->itemName,'Item Database','Items','delete',$sql->itemCode);
			
			$this->c3model->c3crud("no-res",'','','',"DELETE FROM purgeArchive_Ref  WHERE itemID='$id' AND action='submit for purging' AND purgeArchive_Ref.table='PURGE'");
			$this->c3model->c3crud("no-res",'','','',"DELETE FROM items 		    WHERE id	='$id'");
			$this->c3model->c3crud("no-res",'','','',"DELETE FROM itemVendorsRef    WHERE itemID='$id'");
			$this->c3model->c3crud("no-res",'','','',"DELETE FROM items_images      WHERE itemID='$id'");
			$this->c3model->c3crud("no-res",'','','',"DELETE FROM item_views        WHERE itemID='$id'");
			//ITEMS FOR PURGING
			redirect(HTTP_PATH.'itemDatabase/items_for_purging/permanent_delete_item', 'location', 301);
		}
		elseif($action=="deleteSelectedItem")
		{
			$this->modules->module_checker(18,'PURGE APPROVAL');
			
			$ctr=0;
			if(isset($selectedItems)){
				foreach($selectedItems as $cbr => $value)
				{
				//LOGS
				$sql = "SELECT itemName, itemCode FROM items WHERE id = $value";
				$sql = $this->db->query($sql);
				$sql = $sql->row();
				$CI->rec_logs->w($value,$sql->itemName,'Item Database','Items','delete',$sql->itemCode);
				
				$this->c3model->c3crud("no-res",'','','',"DELETE FROM purgeArchive_Ref  WHERE itemID='$value' AND action='submit for purging' AND purgeArchive_Ref.table='PURGE'");
				$this->c3model->c3crud("no-res",'','','',"DELETE FROM items 		    WHERE id='$value'");
				$this->c3model->c3crud("no-res",'','','',"DELETE FROM itemVendorsRef    WHERE itemID='$value'");
				$this->c3model->c3crud("no-res",'','','',"DELETE FROM items_images      WHERE itemID='$value'");
				$this->c3model->c3crud("no-res",'','','',"DELETE FROM item_views        WHERE itemID='$value'");
				}
				//ITEMS FOR PURGING
				redirect(HTTP_PATH.'itemDatabase/items_for_purging/permanent_delete_items', 'location', 301);
			}
			
			redirect(HTTP_PATH.'itemDatabase/items_for_purging/no_selected_item', 'location', 301);
		}
		elseif($action=="restoreOneItem")
		{
			$this->modules->module_checker(18,'PURGE APPROVAL');

			$this->purgeArchive_Ref($singleVal,'restore',$msg);
			$itemField['purge'] = 'n';
			$this->c3model->c3crud("update","items",$itemField,$singleVal);
			
			//ITEMS FOR PURGING
			redirect(HTTP_PATH.'itemDatabase/items_for_purging/item_restored', 'location', 301);
		}
		elseif($action=="restoreSelectedItem")
		{
			$this->modules->module_checker(18,'PURGE APPROVAL');
			
			$ctr=0;
			if(isset($selectedItems)){
				foreach($selectedItems as $cbr => $value)
				{
				$this->purgeArchive_Ref($value,'restore',$msg);
				$itemField['purge'] = 'n';
				$this->c3model->c3crud("update","items",$itemField,$value);
				}
				//ITEMS FOR PURGING
				redirect(HTTP_PATH.'itemDatabase/items_for_purging/items_restored', 'location', 301);
			}
			redirect(HTTP_PATH.'itemDatabase/items_for_purging/no_selected_item', 'location', 301);
		}
	}
	
	function items_for_purging($action='',$id='')
	{	
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(18,'PURGE APPROVAL');
		$data['DELETE'] =  $this->modules->crud_checker(18,'PURGE APPROVAL');
		$data['RESTORE']=  $this->modules->crud_checker(18,'PURGE APPROVAL');
		
		$filter_WHERE="";
		if($_SESSION['super_admin']!='y'  AND $_SESSION['countryID']!=0)
			$filter_WHERE = "WHERE cID =".$_SESSION['countryID']." ";
		
		//$votingCampaignID 	= $id;
		
		$table						= 'item_views';
		$data['vfile']				= 'items_for_purging.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'itemDatabase>Item Database </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'itemDatabase/items_for_purging>  Items for Purging  </a>';
		
		//ACTIONS
		if($action=="permanent_delete_item")  					  $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Item has been delete permanently.");
		if($action=="permanent_delete_items") 					  $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Items has been delete permanently.");
	    if($action=="item_restored") 		  					  $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Item has been restore to database.");
	    if($action=="items_restored") 		  					  $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Items has been restore to database.");
	    if($action=="items_has_been_save_and_downloaded") 		  $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Items has been save and downloaded.");
	    if($action=="item_has_been_save_and_downloaded") 		  $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Item has been save and downloaded.");
	    if($action=="no_selected_item") 		  				  $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"No selected item.");
		
		
		//TOTAL NUMBER OF ROWS	
		$sort='n';
		//print_r($_POST);
		extract($_POST);
		$data['POST'] = $_POST;
		$WHERE="";
		if($_SESSION['super_admin']!='y' AND $_SESSION['countryID']!=0){
			$WHERE 			   = " WHERE cID =".$_SESSION['countryID'] ." AND forPurging='y' AND";
		}else{
			$WHERE 			   = " WHERE cID != 0 AND forPurging='y' AND";
		}
		
		
		$cond="";
		$having="";
		$limit =isset($selpage)? $selpage:0;
		
		$data['quarterStr'] = "";
		$data['DateFrom'] = "";
		$data['DateTo'] = "";
		if((isset($DateFrom) AND isset($DateTo)) AND ($DateFrom!='' AND $DateTo!='') AND !isset($Reset)){
			$WHERE   		   	 .= " (dUploaded >= '$DateFrom' AND dUploaded <= '$DateTo') ";
			$data['DateFrom'] 	  = $DateFrom;
			$data['DateTo']   	  = $DateTo;
			$_SESSION['DateFrom'] = $DateFrom;
			$_SESSION['DateTo']   = $DateTo;
		}else{
			$WHERE   		   .= $this->minMaxDateUploaded('filter');
			$data['DateFrom']   = $this->minMaxDateUploaded('prevMonth');
			$data['DateTo']     = $this->minMaxDateUploaded('nextMonth');
		}
		
		
		if(isset($Submit) OR isset($selpage) AND !isset($Reset))
		{	
			$_SESSION['WHERE'] = "";
			$val1 = mysql_real_escape_string($val1);
			$val1 = trim($val1);
			$val2 = mysql_real_escape_string($val2);
			$val2 = trim($val2);
			
			$condition = '';
			switch($cond1){
				case 'equal': 
					$condition = '=';
					$_SESSION['cond1'] = $cond1;
				break;
				case 'containing': 
					$condition = 'like';
					$_SESSION['cond1'] = $cond1;
				break;
				case 'in': 
					$condition = 'in';
					$_SESSION['cond1'] = $cond1;
				break;
				case 'greaterThan': 
					$condition = '>=';
					$_SESSION['cond1'] = $cond1;
				break;
				case 'lessThan': 
					$condition = '<=';
					$_SESSION['cond1'] = $cond1;
				break;
			}
			
			
			if(($opt1=='itemCode' OR $opt1=='itemName' OR $opt1=='pstatus' OR $opt1=='ptype' OR $opt1=='full_name' OR $opt1=='pcountry') AND $val1!='')
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
					
				//STORE TO SESSION
				$_SESSION['opt1'] = $opt1;
				$_SESSION['val1'] = $val1;
			}
			
			//PUBLISH
			if(($opt1=='publish' OR $opt1=='disapprove') AND $val1!='')
			{
				if($condition=='=')
					$val1 = ($val1=='yes' OR $val1=='Yes') ? 'y' : 'n';
					$cond = "  $opt1 $condition '$val1'";
				if($condition=='like')
					$cond = "  $opt1 $condition '%$val1%'";
				//STORE TO SESSION
				$_SESSION['opt1'] = $opt1;
				$_SESSION['val1'] = $val1;
			}
			
			
			//VIEWS
			if(($opt1=='UnitPrice' OR $opt1=='USD_Price') AND $val1!='' AND $condition!='like'){ 
				if($condition=='in' AND (is_numeric($val1) OR strpos($val1,',')==TRUE))
					$cond = "  $opt1 $condition ('" . str_replace(",", "','", $val1) . "')";
				elseif($condition!='in' AND is_numeric($val1))
					$cond = "  $opt1 $condition $val1";
				
				//STORE TO SESSION
				$_SESSION['opt1'] = $opt1;
				$_SESSION['val1'] = $val1;
			}
		
			//dateReleased
			if($opt1=='dReleased' AND $val1!='' AND $condition!='like'){ 
				$cond = "  dReleased $condition '$val1'";
			
				//STORE TO SESSION
				$_SESSION['opt1'] = $opt1;
				$_SESSION['val1'] = $val1;
			}
			/*2ND SET*/
			switch($cond2){
				case 'equal': 
					$condition2 = '=';
					$_SESSION['cond2'] = $cond2;
				break;
				case 'containing': 
					$condition2 = 'like';
					$_SESSION['cond2'] = $cond2;
				break;
				case 'in': 
					$condition2 = 'in';
					$_SESSION['cond2'] = $cond2;
				break;
				case 'greaterThan': 
					$condition2 = '>=';
					$_SESSION['cond2'] = $cond2;
				break;
				case 'lessThan': 
					$condition2 = '<=';
					$_SESSION['cond2'] = $cond2;
				break;
				
				//STORE TO SESSION
				$_SESSION['cond2'] = $cond2;
			}
			
			
			if(($opt2=='itemCode' OR $opt2=='itemName' OR $opt2=='pstatus' OR $opt2=='ptype' OR $opt2=='full_name' OR $opt2=='pcountry') AND $val2!='')
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
					
				//STORE TO SESSION
				$_SESSION['operator'] = $operator;
				$_SESSION['opt2'] 	  = $opt2;
				$_SESSION['val2'] 	  = $val2;
			}
			
			
			//PUBLISH
			if(($opt2=='publish' OR $opt2=='disapprove') AND $val2!='')
			{
				if($condition2=='=')
					$val2 = ($val2=='yes' OR $val2=='Yes') ? 'y' : 'n';
					$cond .= " $operator $opt2 $condition2 '$val2'";
				if($condition2=='like')
					$cond .= " $operator $opt2 $condition2 '%$val2%'";
				//STORE TO SESSION
				$_SESSION['operator'] = $operator;
				$_SESSION['opt2'] 	  = $opt2;
				$_SESSION['val2'] 	  = $val2;
			}
			
			//LOCAL PRICE
			//VIEWS
			if(($opt2=='UnitPrice' OR $opt2=='USD_Price') AND $val2!='' AND $condition2!='like'){ 
				if($condition2=='in' AND (is_numeric($val2) OR strpos($val2,',')==TRUE))
					$cond .= " $operator $opt2 $condition2 ('" . str_replace(",", "','", $val2) . "')";
				elseif($condition2!='in' AND is_numeric($val2))
					$cond .= " $operator $opt2 $condition2 $val2";
				//STORE TO SESSION
				$_SESSION['operator'] = $operator;
				$_SESSION['opt2'] 	  = $opt2;
				$_SESSION['val2'] 	  = $val2;
			}
	
			//dUploaded
			if($opt2=='dReleased' AND $val2!='' AND $condition2!='like'){ 
				$cond .= " $operator dReleased $condition2 '$val2'";
				//STORE TO SESSION
				$_SESSION['operator'] = $operator;
				$_SESSION['opt2'] 	  = $opt2;
				$_SESSION['val2'] 	  = $val2;
			}
			$cond = ($cond=="") ? "" : " AND ($cond) ";
			$WHERE  .=  " $cond ";
			$_SESSION['WHERE'] .= $WHERE;
		}
		
		
		//IF THEIR NO POSSIBLE RESULT
		$valid_query=TRUE;
		if(isset($Submit)){
			if($cond=="" AND ($val1!='' OR $val2!='')){ 
			$valid_query=FALSE;
			$WHERE = "WHERE itemID=0";
			}
		}
		
		
		
		$data['POST'] = $_POST;
		if(isset($Reset)){
			$_SESSION['WHERE'] ="";
			$data['POST'] 	   = array();
			$data['DateFrom']  = $this->minMaxDateUploaded('prevMonth');
			$data['DateTo']    = $this->minMaxDateUploaded('nextMonth');
			
			$_SESSION['opt1']	 ="";
			$_SESSION['cond1']	 ="";
			$_SESSION['val1']	 ="";
			$_SESSION['operator']="";
			$_SESSION['DateFrom']="";
			$_SESSION['DateTo']  ="";
			$_SESSION['opt2']    ="";
			$_SESSION['cond2']   ="";
			$_SESSION['val2']    ="";
		}
		
		if($_SESSION['WHERE']!='') $WHERE = $_SESSION['WHERE'];
		
		//ORDER
		$ORDER 			= $this->sortingRef('12-A','query');
		$data['order']  = $this->sortingRef('12-A','Orig_code');
		$order_code 	= '12-A';
		$label 			= "Country";
		if(isset($order)){
			$ORDER 			   		= $this->sortingRef($order,'query');
			$_SESSION['ORDER'] 		= $ORDER;
			$data['order'] 	   		= $this->sortingRef($order,'Orig_code');
			$order_code 	   		= $order;
			$_SESSION['order_code'] = $order_code;
			$label 			   		= $this->sortingRef($order,'label');
			$_SESSION['label'] 		= $label;
		}	
		
		if($_SESSION['ORDER']!='') 		$ORDER 		= $_SESSION['ORDER'];
		if($_SESSION['order_code']!='') $order_code = $_SESSION['order_code'];
		if($_SESSION['label']!='')	 	$label 		= $_SESSION['label'];
		
		//echo "WHERE $WHERE"; 
		
		$sql = "SELECT itemID, num_views, likes, wants, itemCode, item_image, itemName, pstatus, ptype, poutlet_status, ppremium_type, pmaterial, pbrand, full_name, cName, publish, UnitPrice, USD_Price, dUploaded, dReleased
				FROM item_db_reports		 
				$WHERE ORDER BY $ORDER";
				
		$sql_csv = "SELECT cName as Country, num_views as Views, likes as Likes, wants as Wants, itemCode as Item_Code, itemName as Item_Name, pstatus as Status, ptype as Type, poutlet_status as Outlet_Status, 
					ppremium_type as Premium_Type, pmaterial as Material_Type, pbrand as Brand, full_name as User, publish as Publish, UnitPrice, USD_Price, dUploaded as Date_Uploaded, dReleased as Date_Released
				    FROM item_db_reports		 
				    $WHERE ORDER BY $ORDER";
	
		
		$all_items = $this->db->query($sql);
		$all_items = $all_items->result_array();
		
		//SORT STATUS
		$limit = ($sort)=='y' ? 0 : $limit;
		if(isset($Reset)) $limit = 0;
		
		$limit_items = $this->db->query($sql." LIMIT $limit,20");
		$limit_items = $limit_items->result_array();
		$data['totrec'] = count($all_items);
		$data['limit']  = $limit;
		
		$items	 = $limit_items;
		$th_w="width:332px;";
		$options="";
		if($_SESSION['super_admin']=='y'){ $th_w="width:60px;"; $options='No options'; }
		$table= "<table id='large2' cellpadding='0' cellspacing='0' border=1 style='width:100%;font-size:11px;' class='iLike_Result_Table2'>
				<tr style='height: 40px;'>
					<th style='width:25px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'>	   No.  	  		  				  					  </th> 
					<th style='$th_w;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'>	 Options  	  		  				  					  </th> 
					<th style='width:80px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"12-D\")'>          <b>Country  	  	  </b></th> 
					<th style='width:80px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"3-A\")'>           <b>Item Code  	  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'>  <b>Image   	 	     							  	  </b></th> 
					<th style='width:100px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"4-A\")'>           <b>Item Name  	  </b></th> 
					<th style='width:64px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"5-A\")'>           <b>Status  	  	  </b></th> 
					<th style='width:98px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"6-A\")'>        	  <b>Type  	  		  </b></th> 
					<th style='width:120px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'  onclick='sortBy(\"11-A\")'>          <b>User  	  		  </b></th> 
					<th style='width:69px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"13-A\")'>          <b>Publish  	  	  </b></th>  
					<th style='width:60px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"14-A\")'>          <b>L. Price  	  	  </b></th> 
					<th style='width:60px;text-align:center;color:white;padding:0px;cursor:pointer;' bgcolor='#bb1d1d'   onclick='sortBy(\"15-A\")'>          <b>US. Price  	  </b></th> 
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
					
					$td="";
					if($_SESSION['super_admin']!='y')				     $td.= "<input type='checkbox' name='selectedItems[]' value='$itemID' id='checkBoxVar' style='padding-bottom:3px;margin-top: -1px;' onclick='clearSingleId()'>  | "; 
					if($data['DELETE'] & $_SESSION['super_admin']!='y')  $td.= "<a onclick='deleteOneItem($itemID)' 	style='cursor:pointer;'>Delete Permanently	 </a> | ";
					if($data['DELETE'] & $_SESSION['super_admin']!='y')  $td.= "<a onclick='saveToDiskOneItem($itemID)' style='cursor:pointer;'>Save to Disk & Delete</a> | ";
					if($data['RESTORE'] & $_SESSION['super_admin']!='y') $td.= "<a onclick='restoreOneItem($itemID)' 	style='cursor:pointer;'>Restore				 </a> ";
					
		$table.= "<tr>
				  <td $c>													$x </td>
				  <td $c>	
					$td $options
				  </td>
				  <td $c style='text-align:left;padding-left:5px;'>			$cName																			</td>  
				  <td $c>													$itemCode      																	</td>
				  <td $c style='text-align:center;'>			    		<img src='".HTTP_PATH."img/thumb/$item_image' style='width:30px;height:30px'> 	</td>
				  <td $c style='text-align:left;padding-left:5px;' title='$orig_itemName'>		<a href='".HTTP_PATH."gallery/itemInfo2/$itemID' target='_blank'>		$itemName</a>  </td>
				  <td $c style='text-align:left;padding-left:5px;'>			$pstatus																		</td>
				  <td $c style='text-align:left;padding-left:5px;'>			$ptype																			</td>
				  <td $c style='text-align:left;padding-left:5px;'>			$full_name																		</td>
				  <td $c style='text-align:center;'>						$publish																		</td>
				  <td $c style='text-align:center;'>						$UnitPrice																		</td>
				  <td $c style='text-align:center;'>						$USD_Price																		</td>
				  <td $c style='text-align:center;'>				        ". $dUploaded ."										</td>
				  <td $c style='text-align:center;'>				        ". $dReleased ."										</td>
				</tr>";}
				    }
					if(!$items OR $valid_query==FALSE)
						$table.=  "<tr><td colspan='20'>No items found, database is empty or your seacrh parameter is incorrect. Please check!</td></tr>";
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
	
	function restore_pointsResources()
	{
	$CI =& get_instance();
	$CI->load->library('rec_logs');
	// 5 minutes execution time
	@set_time_limit(5 * 60);

	// Uncomment this one to fake upload time
	// usleep(5000);

	// Settings
	echo $targetDir =  getcwd()."/purging/restore_point";
	//$targetDir = 'uploads';
	$cleanupTargetDir = true; // Remove old files
	$maxFileAge = 5 * 3600; // Temp file age in seconds


	// Create target dir
	if (!file_exists($targetDir)) {
		@mkdir($targetDir);
	}

	// Get a file name
	if (isset($_REQUEST["name"])) {
		$fileName = $_REQUEST["name"];
	} elseif (!empty($_FILES)) {
		$fileName = $_FILES["file"]["name"];
	} else {
		$fileName = uniqid("file_");
	}
	
	$fileName = str_replace(array('`','~','@','#','$','%','^','&','*','(',')','+','=','{','}','[',']',':',';','<','>','?',"/"),"",$fileName);
	
	$filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

	// Chunking might be enabled
	$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
	$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;


	// Remove old temp files	
	if ($cleanupTargetDir) {
		if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
			die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
		}

		while (($file = readdir($dir)) !== false) {
			$tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

			// If temp file is current file proceed to the next
			if ($tmpfilePath == "{$filePath}.part") {
				continue;
			}

			// Remove temp file if it is older than the max age and is not the current file
			if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge)) {
				@unlink($tmpfilePath);
			}
		}
		closedir($dir);
	}	


	// Open temp file
	if (!$out = @fopen("{$filePath}.part", $chunks ? "ab" : "wb")) {
		die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
	}

	if (!empty($_FILES)) {
		if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
			die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
		}

		// Read binary input stream and append it to temp file
		if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
			die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
		}
	} else {	
		if (!$in = @fopen("php://input", "rb")) {
			die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
		}
	}

	while ($buff = fread($in, 4096)) {
		fwrite($out, $buff);
	}

	@fclose($out);
	@fclose($in);

	// Check if file has been uploaded
	if (!$chunks || $chunk == $chunks - 1) {
		// Strip the temp .part suffix off 
		rename("{$filePath}.part", $filePath);
	}
	
	if(isset($fileName))
	{
	$dbFields['name'] 				= $fileName;
	$dbFields['userID'] 			= $_SESSION['user_id'];
	$dbFields['countryID'] 			= $_SESSION['countryID'];
	$dbFields['date'] 				= date('Y-m-d');
	$this->c3model->c3crud("insert","restore_point",$dbFields,'');
	//GET MAX ID
	$sql		= "select max(id) as max_id FROM restore_point";
	$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
	$maxID 		= $lastID[0]['max_id'];
	//LOGS
	$CI->rec_logs->w($maxID,$fileName,'Item Database','Restore Point','add');
	redirect(HTTP_PATH.'itemDatabase/restore_points/restore_point_save', 'location', 301);
	}
	
	// Return Success JSON-RPC response
	die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
	}

	function restore_points($action='',$id='')
	{	
		//print_r($_SESSION);
		//echo FCPATH2 .'/purging/restore_point/';
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	   
		$this->modules->module_checker(18,'PURGE APPROVAL');
		$data['DELETE'] =  $this->modules->crud_checker(18,'PURGE APPROVAL');
		$data['RESTORE']=  $this->modules->crud_checker(18,'PURGE APPROVAL');
		$data['ADD']	=  $this->modules->crud_checker(18,'PURGE APPROVAL');
		$data['RESTORE_POINT'] = TRUE;
		
		$filter_WHERE="";
		if($_SESSION['super_admin']!='y'  AND $_SESSION['countryID']!=0)
			$filter_WHERE = "WHERE restore_point.countryID =".$_SESSION['countryID']." ";
		
		//$votingCampaignID 	= $id;
		
		$table						= 'item_views';
		$data['vfile']				= 'restore_points.php';
	    $data['title']				= 'iLike Campaign Report | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH 					= HTTP_PATH."report/iLike";
	    $data['breadCrumbs']		 = '<a href='.HTTP_PATH.'itemDatabase>Item Database </a>';
		$data['breadCrumbs']	    .= '<li><img src="'.HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']		.= '<a href='.HTTP_PATH.'itemDatabase/restore_points>  Restore Points  </a>';
		
		//ACTIONS
		if($action=="restore_point_save")  					$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Restore point has been save.");
		if($action=="delete_successfully") 					$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Restore point has been delete.");
		if($action=="restored") 		   					$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Restore point has been restored.");
		if($action=="restore_point_invalid_file_directory") $data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>"Restore point: Sorry you're trying to uploaded an invalid file.");
		if($action=="invalid_file"){
		 $sql = $this->db->query("SELECT name FROM restore_point WHERE id = $id LIMIT 0,1");
		 $sql = $sql->row();
		 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"".$sql->name." is an invalid file, please upload only restore point that was downloaded from the system.");
		}
		
		//TOTAL NUMBER OF ROWS	
		$sort='n';
		extract($_POST);
		
		$WHERE="";
		if($_SESSION['super_admin']!='y' AND $_SESSION['countryID']!=0){
			$WHERE 		= " WHERE country.id  = ".$_SESSION['countryID'];
		}
		
		if($action=="deleteOneItem")
		{
		 //LOGS
		 $sql = "SELECT name FROM restore_point WHERE id = $id";
		 $sql = $this->db->query($sql);
		 $sql = $sql->row();
		 $CI->rec_logs->w($id,$sql->name,'Item Database','Restore Point','delete');
		 unlink(getcwd().'/purging/restore_point/'.$sql->name);
		 $this->c3model->c3crud("no-res",'','','',"DELETE FROM restore_point  	   WHERE id='$id'");
		 $this->c3model->c3crud("no-res",'','','',"DELETE FROM items_checker_ref   WHERE refTable2='".substr($sql->name,0,-4)."'");
		 $this->c3model->c3crud("no-res",'','','',"DELETE FROM images_checker_ref  WHERE refTable2='".substr($sql->name,0,-4)."'");
		 $this->deleteDir(getcwd()."/purging/restore_point_preview/".substr($sql->name,0,-4));
		 $this->deleteDir(getcwd()."/purging/restore_point_upload_checker/".substr($sql->name,0,-4));
		 redirect(HTTP_PATH.'itemDatabase/restore_points/delete_successfully', 'location', 301);
		}
		elseif($action=="insert")
		{
			$config['upload_path'] = FCPATH2.'/purging/restore_point/';
			$config['allowed_types'] = 'zip';
			$this->upload->initialize($config);
			
			if($this->upload->do_upload('restorePoinFile'))
			{
				$imt2=$this->upload->data();
				$fileName = str_replace(array('`','~','@','#','$','%','^','&','*','(',')','+','=','{','}','[',']',':',';','<','>','?',"/"),"",$imt2['file_name']);
				copy($imt2['full_path'],FCPATH2.'/purging/restore_point/'.$fileName);
				$dbFields['name'] 				= $fileName;
				$dbFields['userID'] 			= $_SESSION['user_id'];
				$dbFields['countryID'] 			= $_SESSION['countryID'];
				$dbFields['date'] 				= date('Y-m-d');
				$this->c3model->c3crud("insert","restore_point",$dbFields,'');
				//CHECK IF FILE IS VALID
				//GET MAX ID
				$sql		= "select max(id) as max_id FROM restore_point";
				$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
				$maxID 		= $lastID[0]['max_id'];
				if($this->restore_pointTest($fileName)==TRUE)
				{
				//LOGS
				$CI->rec_logs->w($maxID,$fileName,'Item Database','Restore Point','add');
				redirect(HTTP_PATH.'itemDatabase/restore_points/restore_point_save', 'location', 301);
				}
				else
				{
				$this->db->query("DELETE FROM restore_point WHERE id = $maxID");
				unlink(getcwd()."/purging/restore_point/$fileName");
				$this->deleteDir(getcwd()."/purging/restore_point_upload_checker/".substr($fileName,0,-4)."/");
				redirect(HTTP_PATH.'itemDatabase/restore_points/restore_point_invalid_file_directory', 'location', 301);
				}
			}else{
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'File failed to upload use .zip file type only or '.$this->upload->display_errors());
			}
		}
		
		$sql = $this->db->query("SELECT restore_point.id as rID,name, restore_point.date as tdate, full_name, countryName
				FROM restore_point
				LEFT JOIN admin_users ON admin_users.id = restore_point.userID
				LEFT JOIN country 	  ON country.id     = restore_point.countryID
				$filter_WHERE ORDER BY tdate DESC");
		$data['restore_points'] = $sql->result_array();		
		
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
	
	function deleteDir($dirPath) {
	//echo $dirPath;
	if (! is_dir($dirPath)) {
		//throw new InvalidArgumentException("$dirPath must be a directory");
	}
	if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
		$dirPath .= '/';
	}
	$files = glob($dirPath . '*', GLOB_MARK);
	foreach ($files as $file) {
		if (is_dir($file)) {
			self::deleteDir($file);
		} else {
			unlink($file);
		}
	}
	rmdir($dirPath);
	}

	
	function unzip($src_file='', $dest_dir=false, $create_zip_name_dir=true, $overwrite=true) 
	{
	  if ($zip = zip_open($src_file)) 
	  {
		if ($zip) 
		{
		  $splitter = ($create_zip_name_dir === true) ? "." : "/";
		  if ($dest_dir === false) $dest_dir = substr($src_file, 0, strrpos($src_file, $splitter))."/";
		  
		  // Create the directories to the destination dir if they don't already exist
		  $this->create_dirs($dest_dir);

		  // For every file in the zip-packet
		  while ($zip_entry = zip_read($zip)) 
		  {
			// Now we're going to create the directories in the destination directories
			
			// If the file is not in the root dir
			$pos_last_slash = strrpos(zip_entry_name($zip_entry), "/");
			if ($pos_last_slash !== false)
			{
			  // Create the directory where the zip-entry should be saved (with a "/" at the end)
			  $this->create_dirs($dest_dir.substr(zip_entry_name($zip_entry), 0, $pos_last_slash+1));
			}

			// Open the entry
			if (zip_entry_open($zip,$zip_entry,"r")) 
			{
			  
			  // The name of the file to save on the disk
			  $file_name = $dest_dir.zip_entry_name($zip_entry);
			  
			  // Check if the files should be overwritten or not
			  if ($overwrite === true || $overwrite === false && !is_file($file_name))
			  {
				//if(is_file($file_name)){
				// Get the content of the zip entry
				$fstream = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));

				file_put_contents($file_name, $fstream );
				// Set the rights
				chmod($file_name, 0777);
				//echo "save: ".$file_name."<br />";
				//}
			  }
			  
			  // Close the entry
			  zip_entry_close($zip_entry);
			}       
		  }
		  // Close the zip-file
		  zip_close($zip);
		}
	  } 
	  else
	  {
		return false;
	  }
	  
	  return true;
	}
	
	function create_dirs($path)
	{
	  if (!is_dir($path))
	  {
		$directory_path = "";
		$directories = explode("/",$path);
		array_pop($directories);
		
		foreach($directories as $directory)
		{
		  $directory_path .= $directory."/";
		  if (!is_dir($directory_path))
		  {
			mkdir($directory_path);
			chmod($directory_path, 0777);
		  }
		}
	  }
	}
	
	function restore_pointTest($fileName='')
	{
	//restore_point_upload_checker
	$src_file = getcwd()."/purging/restore_point/".$fileName;
	$dest_dir = getcwd()."/purging/restore_point_upload_checker/".substr($fileName,0,-4)."/";
	if($this->unzip($src_file,$dest_dir)==true AND $this->Directory_File_Checker($dest_dir,substr($fileName,0,-4))==true) return TRUE;
	else return FALSE;
	}
	
	function Directory_File_Checker($dir='',$ref='')
	{
	 //echo $dir;
	 $error=0;
	 //DIRECTORY CHECKER
	 if(!file_exists($dir."database") OR !file_exists($dir."database/items.txt") OR !file_exists($dir."database/items_images.txt"))   $error++;
	 if(!file_exists($dir."big")) 		  $error++;
	 if(!file_exists($dir."galleryImg"))  $error++;
	 if(!file_exists($dir."small")) 	  $error++;
	 if(!file_exists($dir."thumb")) 	  $error++;
	 //CHECK FOR ERROR
	 if(file_exists($dir."database/items_images.txt"))
	 {
	 $userID = $_SESSION['user_id'];
	 $action = 'temp file';
	 $table  = $ref;
	 $date   = date('Y-m-d');
	 $time   = date('H:i:s');
	 $this->db->query("LOAD DATA INFILE '".$dir."database/items_images.txt"."' IGNORE 
					   INTO TABLE images_checker_ref
					   FIELDS TERMINATED BY '	'
					   LINES TERMINATED BY '\r\n'
					   (id, itemID, temporary_file, refTable)
					   SET refTable = '$table'");
	 $images = $this->db->query("SELECT temporary_file FROM images_checker_ref WHERE refTable='$table'");
	 $images = $images->result_array();
	 foreach($images as $img)
	 {  extract($img);
		if(!file_exists($dir."big/".$temporary_file) 		 || file_exists(getcwd()."/img/big/".$temporary_file)) 		   $error++;
		if(!file_exists($dir."galleryImg/".$temporary_file)  || file_exists(getcwd()."/img/gelleryImg/".$temporary_file))  $error++;
		if(!file_exists($dir."small/".$temporary_file) 	 	 || file_exists(getcwd()."/img/small/".$temporary_file)) 	   $error++;
		if(!file_exists($dir."thumb/".$temporary_file) 	 	 || file_exists(getcwd()."/img/thumb/".$temporary_file)) 	   $error++;
	 }
	  $this->db->query("DELETE FROM images_checker_ref WHERE refTable='$table'");
	  //CHECK FOR ERROR
	  if($error==0)
	  { 
	   return TRUE;
	  }else{		   
	   return FALSE;
	  }
	 }
	}
	
	function purgeArchive_Ref2($itemID='',$action='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		//PURGE
		$dbFields['itemID'] = $itemID;
		$dbFields['userID'] = $_SESSION['user_id'];
		$reborn=FALSE;
		//restore
		if($action=='restore'){
		 $sql = $this->db->query("SELECT userID FROM purgeArchive_Ref WHERE itemID = $itemID AND purgeArchive_Ref.table='ARCHIVE' AND action='submit for archiving'");
		 $sql = $sql->row();
		 //IF SOMEONE HAS TAGGED FOR IT
		 if($sql->userID){
		 $dbFields['userID'] = $sql->userID;
		 $reborn=TRUE;
		 }
		 //DEL SUBMIT FOR PURGING DATA
		 $this->db->query("DELETE FROM purgeArchive_Ref WHERE itemID = $itemID AND purgeArchive_Ref.table='ARCHIVE' AND action='submit for archiving'");
		 //INSERT TO LOGS
		 $sql = "SELECT itemName, itemCode FROM items WHERE id = $itemID";
		 $sql = $this->db->query($sql);
		 $sql = $sql->row();
		 $CI->rec_logs->w($itemID,$sql->itemName,'Item Database','Items','restore from archive',$sql->itemCode);
		}
		elseif($action=='submit for purging')
		{
		//DELETE RESTORE DATA
		$this->db->query("DELETE FROM purgeArchive_Ref WHERE itemID = $itemID AND purgeArchive_Ref.table='ARCHIVE' AND action='restore'");
		}
		$dbFields['table']  = "ARCHIVE";
		$dbFields['action'] = $action;
		$dbFields['date']   = date('Y-m-d');
		$dbFields['time']   = date('H:m:s');
		$res = $this->c3model->c3crud("insert","purgeArchive_Ref",$dbFields,'');
		
		return $reborn;
	}
	
	function restoreFromArchiving($action='',$id='')
	{
	    extract($_POST);
		if($action=="restoreSelectedItem")
		{
			$this->modules->module_checker(18,'ARCHIVE');
			
			$ctr=0;
			$row='';
			if(isset($selectedItems)){
				foreach($selectedItems as $cbr => $value)
				{
				$this->purgeArchive_Ref2($value,'restore');
				$itemField['archive'] 	   = 'n';
				//CHECK IF MANUALLY TAGGED
				$sql = $this->db->query("SELECT count(rec_id) as ctr FROM logs WHERE rec_id = $value AND action='for archiving' LIMIT 0,1");
				$sql = $sql->row();
				if($sql->ctr==0) $itemField['dateReleased'] = date('Y-m-d');
				
				$this->c3model->c3crud("update","items",$itemField,$value);
				}
				//ITEMS FOR PURGING
				redirect($this->tag_for_purgingRedirect($_SERVER['HTTP_REFERER'],'restore_to_item_db'), 'location', 301);
			}
			redirect($this->tag_for_purgingRedirect($_SERVER['HTTP_REFERER'],'no_selected_item'), 'location', 301);
		}
		elseif($action=="restoreOneItem")
		{
			$this->modules->module_checker(18,'ARCHIVE');
			
			$this->purgeArchive_Ref2($id,'restore');
			$itemField['archive'] 	   = 'n';
			//CHECK IF MANUALLY TAGGED
			$sql = $this->db->query("SELECT count(rec_id) as ctr FROM logs WHERE rec_id = $id AND action='for archiving' LIMIT 0,1");
			$sql = $sql->row();
			if($sql->ctr==0) $itemField['dateReleased'] = date('Y-m-d');
			
			$this->c3model->c3crud("update","items",$itemField,$id);
			//ITEMS FOR PURGING
			redirect($this->tag_for_purgingRedirect($_SERVER['HTTP_REFERER'],'restore_to_item_db'), 'location', 301);
		}
	}
	
	function tag_for_archiving($referer='',$action='',$id='')
	{
		$this->modules->module_checker(18,'ARCHIVE');
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		
		$current_date=date('Y-m-d');
		extract($_POST);
		switch($referer){
			case 'items':
			case 'popular_items':
			//ONE ITEM
			if($action=='archiveOneItem')
			{
				$tables = array(
				  array('tbl'=>'iLikeResultRef',
						'fld'=>'itemID'),
				  array('tbl'=>'iWantResultRef',
						'fld'=>'itemID'));
			
				if($this->modules->attr($tables,$id)==0 AND $this->modules->common_gallery_item($id)==FALSE)
				{
					//LOGS
					$sql = "SELECT itemName, itemCode FROM items WHERE id = $id";
					$sql = $this->db->query($sql);
					$sql = $sql->row();
					
					$this->db->query("UPDATE items SET `archive` = 'y',  dateLastEdited='$current_date' WHERE id = $id");
					if(isset($sql->itemName))
						$CI->rec_logs->w($id,$sql->itemName,'Item Database','Items','for archiving',$sql->itemCode);
					
					//PURGE ARCHIVE REFERENCE
					$this->purgeArchive_Ref2($id,"submit for archiving");
					redirect($this->tag_for_purgingRedirect($_SERVER['HTTP_REFERER'],'item_has_been_submitted_for_archiving'), 'location', 301);
				}else{
					redirect($this->tag_for_purgingRedirect($_SERVER['HTTP_REFERER'],'cannot_be_archive'), 'location', 301);
				}
			}
			if($action=='archiveSelectedItems')
			{
				//MULTIPLE
				$ctr=0;
				if(isset($selectedItems)){
					foreach($selectedItems as $cbr => $value)
					{
					 $tables = array(
					  array('tbl'=>'iLikeResultRef',
							'fld'=>'itemID'),
					  array('tbl'=>'iWantResultRef',
							'fld'=>'itemID'));
					  
					  if($this->modules->attr($tables,$value)==0 AND $this->modules->common_gallery_item($value)==FALSE){
						//LOGS
						$sql = "SELECT itemName, itemCode FROM items WHERE id = $value";
						$sql = $this->db->query($sql);
						$sql = $sql->row();
						
						$this->db->query("UPDATE items SET `archive` = 'y', dateLastEdited='$current_date' WHERE id = $value");
						if(isset($sql->itemName))
							$CI->rec_logs->w($value,$sql->itemName,'Item Database','Items','for purging',$sql->itemCode);
						
						//PURGE ARCHIVE REFERENCE
						$this->purgeArchive_Ref2($value,"submit for archiving");
					  }else{
					    $ctr++;
					  }  
					}
				} 
				
				if($ctr==0)
				{
					if(isset($selectedItems)){
						redirect($this->tag_for_purgingRedirect($_SERVER['HTTP_REFERER'],"items_has_been_submitted_for_archiving"), 'location', 301);
					}
					redirect($this->tag_for_purgingRedirect($_SERVER['HTTP_REFERER'],"no_selected_item"), 'location', 301);
				}else{
					redirect($this->tag_for_purgingRedirect($_SERVER['HTTP_REFERER'],"some_cannot_be_archive"), 'location', 301);
				}
			}
			break;
		}
		
	}
	
	function ItemsForArchiveQualifications()
	{
	 //SELECT ARCHIVE PERIOD
	 $age=0; 
	 $sql = $this->db->query("SELECT defaultRange, defaultDate FROM archive_filtering WHERE id=1 LIMIT 0,1");
	 $sql = $sql->row();
	 $list_of_items="";
	 if($sql->defaultRange==1){
	  $sql = $this->db->query("SELECT tyear as DaysInYear, tmonth as DaysInMonth FROM archive_filtering WHERE id=1");
	  $sql = $sql->row();
	  $age = $sql->DaysInYear + $sql->DaysInMonth;
	  //GENERATE STRING
	  $list_of_items = "Items that has an age of Years: ".$sql->DaysInYear." Months: ".$sql->DaysInMonth." or greater <br/>or items that has been submitted for archiving.";
	 }else{
	  $sql 		= $this->db->query("SELECT dateFrom, dateTo FROM archive_filtering WHERE id=1");
	  $sql 		= $sql->row();
	  $dateFrom = $sql->dateFrom;
	  $dateTo   = $sql->dateTo;
	  //GENERATE STRING
	  $list_of_items = "Items that was published from ".$sql->dateFrom."  to ".$sql->dateTo ." <br/>or items that has been submitted for archiving.";
	 }
	 
     return $list_of_items;
	}
	
	function items_for_archiving($action='',$id='',$txtsearch='',$selPOSMType='',$selPOSMStatus='',$selPremiumType='',$seloutlet='',$selCountry='',$selBrand='',$selMaterial='',$items_date='',$nviews='',$sort_by_price='',$priceRange='',$priceFrom='',$priceTo='',$year='',$month='',$priceRangeID='',$msg='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		
		$this->modules->module_checker(18,'ARCHIVE');
		$filter = $this->modules->itemdb_country();
		$filter_add = $this->modules->country();
	
		$table= "items_for_archiving";
		$data['vfile']				= 'items_for_archiving.php';
	    $data['title']				= 'Items | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//PURGING
		$data['RESTORE'] =  $this->modules->crud_checker(18,'ARCHIVE');
		$data['DELETE'] =  $this->modules->crud_checker(18,'DELETE');
		
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(5);

		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
	    $data['breadCrumbs']		= '<a href='.$HTTP_PATH.'itemDatabase> Item Database </a>';
	    $data['breadCrumbs']	   .= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']	   .= '<a href='.$HTTP_PATH.'itemDatabase/items_for_archiving> Archived Items </a>';

		//CSRF
		$data['csrf'] = "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>"; 
		$data['searchAction'] = HTTP_PATH. "itemDatabase/items.html"; 
		
		
		//search filter
		$data['post'] = $_POST; $filter2="";
		$order = "items.dateReleased DESC,";
		//print_r($_POST);
		
		$data['redirectTo']	= "items_for_archiving";
        if(($txtsearch!='' OR $selPOSMType!='' OR $selPOSMStatus!='' OR $selPremiumType!='' OR $seloutlet!='' OR $selCountry!='' OR $selBrand!='' OR $selMaterial!='' OR $items_date=!'' OR $nviews!='' OR $sort_by_price!='' OR $priceRange!='' OR $priceRangeID!=''))		
		{ 
			extract($_POST);  
			//$_SESSION['txtsearch']="";
              if($selPOSMType!='null' 	 AND $selPOSMType!='')     	$filter2 .= " AND POSMTypeID		='$selPOSMType'";
              if($selPOSMStatus!='null'  AND $selPOSMStatus!='')   	$filter2 .= " AND POSMStatusID		='$selPOSMStatus'";
			  if($selPremiumType!='null' AND $selPremiumType!='')  	$filter2 .= " AND PremiumTypeID		='$selPremiumType'";
			  if($seloutlet!='null' 	 AND $seloutlet!='')       	$filter2 .= " AND OUTLETStatusID	='$seloutlet'";
			  if($selCountry!='null'	 AND $selCountry!='')      	$filter2 .= " AND countryID			='$selCountry'";
			  if($selBrand!='null' 		 AND $selBrand!='')        	$filter2 .= " AND brandID 			='$selBrand'";
			  if($selMaterial!='null' 	 AND $selMaterial!='')     	$filter2 .= " AND MaterialTypeID	='$selMaterial'";
			  if($priceRangeID!='null'   AND $priceRangeID!='')    $filter2 .= " AND price_rangeID	    ='$priceRangeID'";
			  
			  if($txtsearch!='' AND $txtsearch!='null'){
				$txtsearch = addslashes($txtsearch);
				$txtsearch = trim($txtsearch);
				$txtsearch = str_replace("%20"," ",$txtsearch);
				$filter2 .= " AND ( itemCode like  '%$txtsearch%'  or itemName like  '%$txtsearch%'  or Short_Description like '%$txtsearch%' or Long_Description like '%$txtsearch%')";
			  }   
			  

			  //PRICE RANGE	
			  if(($priceRange!='' AND $priceRange!='null') AND is_numeric($priceFrom) AND is_numeric($priceTo))
					$filter2 = "AND $priceRange >= $priceFrom AND $priceRange <= $priceTo ";
			  
			  
			  //DATE RANGE
			  $m=0;
			  if($year!='' AND $year!='null')
				$m = $year * 12;  
			  if($month!='' AND $month!='null')
				$m += $month;
			  
			  if(($year!='' AND $year!='null' AND $year!=0)  OR ($month!='' AND $month!='null' AND $month!=0)){
				$filter2 .= " AND items.dateAdded <= CURDATE() AND items.dateAdded >= (SELECT CURDATE() - INTERVAL $m MONTH) ";
			  }
			  
			  
			   //SORT BY DATE
			   if(($nviews!='' AND $nviews!='null') OR ($sort_by_price!='' AND $sort_by_price!='null') AND ($items_date!=''  OR $items_date!='null'))
					$order = "";
			 
			   if($nviews!='null' 	 	 AND $nviews!='')          $order  .= " COUNT(item_views.itemID) $nviews,";
			   if($sort_by_price!='null' AND $sort_by_price!='')   $order  .= str_replace("-"," ",$sort_by_price.","); 
			   if($items_date!='null' 	 AND $items_date!='')      $order  .= " items.dateReleased $items_date,"; 
			  
		} 
		$order = substr($order, 0,-1);
		
		$data['post'] = $_POST; 
		$this->modules->generateItemsForArchive();
		if($filter=='' & $filter2!='') $filter = "WHERE  ". substr($filter2,4)." AND items.id IN (".$this->modules->generateItemsForArchive().")";
		else $filter = "$filter   $filter2  AND items.id IN (".$this->modules->generateItemsForArchive().")"; 

		//TOTAL NUMBER OF ROWS
		$data['active_page']= 1;
		if($id!='') 
			$data['active_page']= $id;
					
		//NEW FUNCTION
		$url="";
		$data['txtsearch'] 		= "null";
		$data['selPOSMType'] 	= "null";
		$data['selPOSMStatus'] 	= "null";
		$data['selPremiumType'] = "null";
		$data['seloutlet'] 		= "null";
		$data['selCountry'] 	= "null";
		$data['selBrand'] 		= "null";
		$data['selMaterial'] 	= "null";
		$data['items_date'] 	= "null";
		$data['nviews'] 		= "null";
		$data['sort_by_price'] 	= "null";
		$data['priceRange'] 	= "null";
		$data['priceFrom'] 		= "null";
		$data['priceTo'] 		= "null";
		$data['year'] 			= "null";
		$data['month'] 			= "null";
		$data['priceRangeID'] 	= "null";
		
		//ARCHIVE QUALIFICATION
		$data['qualifications'] = $this->ItemsForArchiveQualifications();

		if($action=="page")
		{
			$this->modules->module_checker(18,'REVIEW');
			//REPOST DATA
			if($txtsearch!='')   	
				$data['txtsearch'] 		=  $txtsearch;
			if($selPOSMType!='')   	
				$data['selPOSMType'] 	=  $selPOSMType;
			if($selPOSMStatus!='')   	
				$data['selPOSMStatus'] 	=  $selPOSMStatus;	
			if($selPremiumType!='')   	
				$data['selPremiumType'] =  $selPremiumType;	
			if($seloutlet!='')   	
				$data['seloutlet'] 		=  $seloutlet;
			if($selCountry!='')   	
				$data['selCountry'] 	=  $selCountry;
			if($selBrand!='')   	
				$data['selBrand'] 		=  $selBrand;
			if($selMaterial!='')   	
				$data['selMaterial'] 	=  $selMaterial;
			if($items_date!='')   	
				$data['items_date'] 	=  $items_date;
			if($nviews!='')   	
				$data['nviews'] 		=  $nviews;
			if($sort_by_price!='')   	
				$data['sort_by_price'] 	=  $sort_by_price;
			if($priceRange!='')   	
				$data['priceRange'] 	=  $priceRange;
			if($priceFrom!='')   	
				$data['priceFrom'] 		=  $priceFrom;
			if($priceTo!='')   	
				$data['priceTo'] 		=  $priceTo;
			if($year!='')   	
				$data['year'] 			=  $year;
			if($month!='')   	
				$data['month'] 			=  $month;
			if($priceRangeID!='')   	
				$data['priceRangeID'] 	=  $priceRangeID;
		}
		
		//SEARCH ACTION
		$data['url']  = $data['txtsearch']."/".$data['selPOSMType']."/".$data['selPOSMStatus']."/".$data['selPremiumType']."/".$data['seloutlet']."/".$data['selCountry']."/".$data['selBrand']."/".$data['selMaterial'].'/';
		$data['url'] .= $data['items_date']."/".$data['nviews']."/".$data['sort_by_price']."/".$data['priceRange']."/".$data['priceFrom']."/".$data['priceTo']."/".$data['year']."/".$data['month']."/".$data['priceRangeID'];
		$data['searchAction'] = HTTP_PATH. "itemDatabase/redirect_link/".$data['redirectTo']."/page/1/".$data['url']; 
		
	
		//extract($_POST);
	    if($action=="insert_sucess")			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Item has been save.");
		elseif($action=="update_success"    	  			     || $msg=="update_success")							$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Item has been updated.");
		elseif($action=="cannot_be_purge"   	  			     || $msg=="cannot_be_purge")						 $data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>"Item cannot be purge because it is a result of campaign or featured in Common Gallery.");
		elseif($action=="cannot_be_archive" 	  			     || $msg=="cannot_be_archive")					 	 $data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>"Item cannot be archive because it is a result of campaign or featured in Common Gallery.");
		elseif($action=="some_cannot_be_purge"   	  			 || $msg=="some_cannot_be_purge")					  $data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>"Some items cannot be purge because it is a result of campaign or featured in Common Gallery.");
		elseif($action=="tagged_as_unpopular" 	  			     || $msg=="tagged_as_unpopular")  				 	 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Item has been tagged as unpopular.");
		elseif($action=="tagged_as_popular_items" 			     || $msg=="tagged_as_popular_items")  			 	 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Tagged as popular items.");
		elseif($action=="tagged_as_popular_item"  			     || $msg=="tagged_as_popular_item")  				 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Tagged as popular item.");
		elseif($action=="tagged_as_not_popular_item"  		     || $msg=="tagged_as_not_popular_item")  			 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Tagged as not popular item.");
		elseif($action=="items_has_been_submitted_for_purging"   || $msg=="items_has_been_submitted_for_purging")  	 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Items has been submitted for purging.");
		elseif($action=="item_has_been_submitted_for_purging"    || $msg=="item_has_been_submitted_for_purging")   	 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Item has been submitted for purging.");
		elseif($action=="tagged_as_not_popular_items"  		     || $msg=="tagged_as_not_popular_items")   			 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Items has tagged as not popular items.");
		elseif($action=="items_has_been_submitted_for_archiving" || $msg=="items_has_been_submitted_for_archiving")  $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Items has been submitted for archiving.");
		elseif($action=="item_has_been_submitted_for_archiving"  || $msg=="item_has_been_submitted_for_archiving")   $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Item has been submitted for archiving.");
		elseif($action=="no_selected_item" 						 || $msg=="no_selected_item")    					 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"No selected item.");
		elseif($action=="tagged_as_disapproved" 			     || $msg=="tagged_as_disapproved")    				 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Tagged as disapproved.");
		elseif($action=="item_has_been_published"   			 || $msg=="item_has_been_published")   				 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Item has been published.");
		elseif($action=="item_has_been_disapproved" 			 || $msg=="item_has_been_disapproved")   			 $data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>"Item has been disapproved.");
		elseif($action=="disapprove" 							 || $msg=="disapprove")								 $data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>"Item has been tag as irrelevant.");
		elseif($action=="restore_to_item_db" 					 || $msg=="restore_to_item_db")						 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Item has been restored to Item Database.");
		elseif($action=="insert")
		{
			$this->modules->module_checker(18,'ADD');
			
			if($_POST==NULL){
				redirect(HTTP_PATH.'itemDatabase/items/add', 'location', 301);
				die();
			}
			
			isset($Long_Description) ? $dbFields['Long_Description'] 	= $Long_Description : '';
			isset($brandID) 		 ? $dbFields['brandID'] 		  	= $brandID 			: '';
			isset($POSMTypeID) 	 	 ? $dbFields['POSMTypeID'] 	  		= $POSMTypeID 		: '';
			isset($POSMStatusID) 	 ? $dbFields['POSMStatusID'] 	  	= $POSMStatusID 	: '';
			isset($OUTLETStatusID) 	 ? $dbFields['OUTLETStatusID']   	= $OUTLETStatusID 	: '';
			isset($PremiumTypeID) 	 ? $dbFields['PremiumTypeID']    	= $PremiumTypeID 	: '';
			isset($MaterialTypeID) 	 ? $dbFields['MaterialTypeID']   	= $MaterialTypeID 	: '';
			isset($countryID) 		 ? $dbFields['countryID'] 		  	= $_SESSION['countryID'] : '';
			isset($itemName) 	     ? $dbFields['itemName']         	= addslashes($itemName) 		: '';
			isset($Short_Description)? $dbFields['Short_Description'] 	= $Short_Description: '';
			isset($UnitPrice) 		 ? $dbFields['UnitPrice'] 		  	= $UnitPrice 		: '';
			isset($USD_Price) 		 ? $dbFields['USD_Price'] 		  	= $USD_Price 		: '';
			isset($MOQ) 			 ? $dbFields['MOQ'] 			  	= $MOQ 				: '';
			isset($UOM) 		     ? $dbFields['UOM'] 			  	= $UOM 				: '';
			isset($price_rangeID) 	 ? $dbFields['price_rangeID'] 		= $price_rangeID 	: '';
			$dbFields['dateAdded']    = date('Y-m-d');
			$dbFields['dateReleased'] = ($publish=='y') ? date('Y-m-d') : '0000-00-00';
			$dbFields['user_id'] 	  = $_SESSION['user_id'];
			
			isset($Fields0001) 		 ? $dbFields['Fields0001'] 	  = $Fields0001 : '';
			isset($Fields0002) 		 ? $dbFields['Fields0002'] 	  = $Fields0002 : '';
			isset($Fields0003) 		 ? $dbFields['Fields0003'] 	  = $Fields0003 : '';
			isset($Fields0004) 		 ? $dbFields['Fields0004'] 	  = $Fields0004 : '';
			isset($Fields0005) 		 ? $dbFields['Fields0005'] 	  = $Fields0005 : '';
			isset($estimated_production_lead_time) ? $dbFields['estimated_production_lead_time'] = $estimated_production_lead_time : '';
			isset($price_validity) 	? $dbFields['price_validity'] = $price_validity : '';
			
			isset($publish) 	 	 		? $dbFields['publish'] 		  			= $publish 				 : '';
			isset($publish_other_country) 	? $dbFields['publish_other_country']  	= $publish_other_country : '';
			isset($country_of_origin) 		? $dbFields['country_of_origin'] 		= $country_of_origin 	 : '';
			
			isset($plant_inventory) 		? $dbFields['plant_inventory'] 			= $plant_inventory 		  : '';
			isset($supplier_stock_on_hand) 	? $dbFields['supplier_stock_on_hand'] 	= $supplier_stock_on_hand : '';
			isset($date_first_issue) 		? $dbFields['date_first_issue'] 		= $date_first_issue 	  : '';
			isset($date_last_used) 			? $dbFields['date_last_used'] 			= $date_last_used 		  : '';
			isset($activity_event_use) 		? $dbFields['activity_event_use'] 		= $activity_event_use 	  : '';
			
			
			//CHECK REQUIRED FIELDS
			if($publish=='y' AND $this->field_checker($_POST)!=''){
				$data['USER_MANUAL'] = $this->modules->user_manual(13);
			
				$data['POSM_statusID']	= isset($sID) ? $sID : NULL;
				$sql 			 		= $this->db->query("SELECT *, vendors.id as vID FROM vendors INNER JOIN country ON country.id = vendors.countryID $filter_add");
				$data['vendors'] 		= $sql->result_array();
				
				$data['items_images'] 	= array();
				$data['vfile']			= 'itemFORM.php';
				
				$data['POST']			= $_POST;
				$data['msg']			= array('msg_type'=>'alert-warning','msg_desc'=>$this->field_checker($_POST)); 
			}
			else
			{
				//DUPLICATE
				if(isset($duplicate)) $dbFields['publish'] = 'n';
				
				$res = $this->c3model->c3crud("insert",$table,$dbFields,'');
				
				//GET MAX ID
				$sql		= "select max(id) as max_id FROM $table";
				$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
				$maxID 	= $lastID[0]['max_id'];
				
				//UPDATE ITEM CODE
				$itemField['itemCode'] = $this->generate_ItemCode($maxID,$countryID,isset($POSMTypeID) ? $POSMTypeID : 0);
				$this->c3model->c3crud("update",$table,$itemField,$maxID);
		
				//IMAGE
				$config['upload_path'] = FCPATH2.'/img/items/';
				$config['allowed_types'] = 'gif|jpg|png';
				$this->upload->initialize($config);
				
				if($this->upload->do_multi_upload("files")){
					$files = $this->upload->get_multi_upload_data();
					
					foreach($files as $f)
					{
						extract($f);
						//GENERATE IMAGE CODE
						$new_file_name=$this->generateImgCode($maxID,$itemField['itemCode'],$f['file_name']);
						$this->imageResize($file_name,$new_file_name,$f['file_path']);
						$refdbFields['itemID'] = $maxID; 
						$refdbFields['image']  = $new_file_name; 
						
						$res = $this->c3model->c3crud("insert",'items_images',$refdbFields,'');
					}
					
					//TAG LAST IMAGE
					$query = $this->db->query("SELECT MAX(items_images.id) as lastImageID FROM items_images WHERE itemID = $maxID LIMIT 1");
					$row = $query->row();
					
						$this->db->query("UPDATE `items_images` SET  `defaultStatus` =  '1' WHERE  `items_images`.`id` = ". $row->lastImageID );
				}
				
				
				//VENDOR REFERENCE
				if(isset($multipleVendors))
				{
					$sql		= "select max(id) as max_id FROM items";
					$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
					$menu_id 	= $lastID[0]['max_id'];
					$refFields['itemID'] =  $menu_id;
					
					foreach($multipleVendors as $mV => $value)
					{
						$refFields['vendorID'] = $value;
						$res = $this->c3model->c3crud("insert",'itemVendorsRef',$refFields,'');
					}
				}
				
				//DUPLICATE IMAGES
				if(isset($duplicate)){
					$sql = "SELECT image FROM items_images WHERE itemID = $duplicate";
					$img = $this->db->query($sql);
					$img = $img->result_array();
					
					if($img){
						foreach($img as $i){
							extract($i);
							$refdbFields['itemID'] = $maxID; 
							$refdbFields['image']  = $image; 
							$res = $this->c3model->c3crud("insert",'items_images',$refdbFields,'');
						}
					}
					
					//TAG LAST IMAGE
					$this->db->query("UPDATE  `items_images` SET  `defaultStatus` =  '0' WHERE  `items_images`.`itemID` = ". $maxID );
					$query = $this->db->query("SELECT MIN(items_images.id) as lastImageID FROM items_images WHERE itemID = $maxID LIMIT 0,1");
					$row = $query->row();
					if(isset($row->lastImageID))
						$this->db->query("UPDATE  `items_images` SET  `defaultStatus` =  '1' WHERE  `items_images`.`id` = ". $row->lastImageID );				
				}	
				
				
				//LOGS
				$CI->rec_logs->w($maxID,$itemName,'Item Database','Items',isset($duplicate) ? 'duplicate' : 'add',$itemField['itemCode']);
				
				//die();
				if($publish=='n'){
					if($this->modules->module_checker2(55,'REVIEW')==true)
						redirect(HTTP_PATH.'itemDatabase/BU_Marketing_Items_review/insert_sucess', 'location', 301);
					if($this->modules->module_checker2(56,'REVIEW')==true)
						redirect(HTTP_PATH.'itemDatabase/BU_Logistics_Items_review/insert_sucess', 'location', 301);
				}else{
					redirect(HTTP_PATH.'itemDatabase/items/insert_sucess', 'location', 301);
				}
			}
		}
		elseif($action=="deleteOneItem")
		{
			$this->modules->module_checker(18,'DELETE');
			
			$tables = array(
			  array('tbl'=>'iLikeResultRef',
					'fld'=>'itemID'),
			  array('tbl'=>'iWantResultRef',
					'fld'=>'itemID'),
			  array('tbl'=>'votexRef',
					'fld'=>'itemID'),
			  array('tbl'=>'campaignItemsXref',
					'fld'=>'itemID')
					);
		
			if($this->modules->attr($tables,$id)==0)
			{
			//LOGS
			$sql = "SELECT itemName, itemCode FROM items WHERE id = $id";
			$sql = $this->db->query($sql);
			$sql = $sql->row();
			if(isset($sql->itemName))
				$CI->rec_logs->w($id,$sql->itemName,'Item Database','Items','delete',$sql->itemCode);
			
			$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Item has been deleted.');
			$this->c3model->c3crud("no-res",'','','',"DELETE FROM items 		 WHERE id='$id'");
	 	    $this->c3model->c3crud("no-res",'','','',"DELETE FROM itemVendorsRef WHERE itemID='$id'");
	 	    $this->c3model->c3crud("no-res",'','','',"DELETE FROM items_images   WHERE itemID='$id'");
	 	    $this->c3model->c3crud("no-res",'','','',"DELETE FROM item_views     WHERE itemID='$id'");
			
			}else{
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Item cannot be delete because it is being link to campaign.');
			}
			$data['active_page']= 1;
		}
		elseif($action=="deleteSelectedItem")
		{
			$this->modules->module_checker(18,'DELETE');
			
			$ctr=0;
			if(isset($selectedItems)){
				foreach($selectedItems as $cbr => $value)
				{
			
				$tables = array(
				  array('tbl'=>'iLikeResultRef',
						'fld'=>'itemID'),
				  array('tbl'=>'iWantResultRef',
						'fld'=>'itemID'),
				  array('tbl'=>'votexRef',
						'fld'=>'itemID'),
				  array('tbl'=>'campaignItemsXref',
						'fld'=>'itemID')
						);
						
					if($this->modules->attr($tables,$value)!=0)
						$ctr++;
				}
			}
			
			if($ctr==0)
			{
				if(isset($selectedItems)){
					foreach($selectedItems as $sItems => $value)
					{
						//LOGS
						$sql = "SELECT itemName, itemCode FROM items WHERE id = $value";
						$sql = $this->db->query($sql);
						$sql = $sql->row();
						if(isset($sql->itemName))
							$CI->rec_logs->w($value,$sql->itemName,'Item Database','Items','delete',$sql->itemCode);
						
						$this->c3model->c3crud("no-res",'','','',"DELETE FROM items 		 WHERE id='$value'");
						$this->c3model->c3crud("no-res",'','','',"DELETE FROM itemVendorsRef WHERE itemID='$value'");
						$this->c3model->c3crud("no-res",'','','',"DELETE FROM items_images   WHERE itemID='$value'");
						$this->c3model->c3crud("no-res",'','','',"DELETE FROM item_views     WHERE itemID='$id'");
					}
					$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Multiple Items has been deleted.');
				}
			}else{
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Multiple Items cannot been delete, because it is being use in campaign.');
			}
			$data['active_page']= 1;
		}
		elseif($action=="edit")
		{
			$this->modules->module_checker(18,'EDIT');
			
			//USER MANUAL
			$data['USER_MANUAL'] = $this->modules->user_manual(13);
			
			$data['id'] 	 		= $id;
			$data['POSM_statusID']	= isset($sID) ? $sID : NULL;
			$sql 			 		= $this->db->query("SELECT *, vendors.id as vID FROM vendors INNER JOIN country ON country.id = vendors.countryID $filter_add");
			$data['vendors'] 		= $sql->result_array();
			
			$sql 			 		= $this->db->query("SELECT * FROM items_images WHERE itemID = $id ORDER BY defaultStatus DESC");
			$data['items_images'] 	= $sql->result_array();
			$data['vfile']	 		= 'itemFORM.php';
		}
		elseif($action=="preview")
		{
			$this->modules->module_checker(18,'REVIEW');
			
			$data['id'] 	 = $id;
			$data['POSM_statusID']	= isset($sID) ? $sID : NULL;
			$sql 			 = $this->db->query("SELECT *, vendors.id as vID FROM vendors INNER JOIN country ON country.id = vendors.countryID $filter_add");
			$data['vendors'] = $sql->result_array();
			
			$sql 			 = $this->db->query("SELECT * FROM items_images WHERE itemID = $id");
			$data['items_images'] = $sql->result_array();
			$data['vfile']	 = 'itemPreviewFORM.php';
		}
		elseif($action=="duplicate")
		{
			$this->modules->module_checker(18,'ADD');
			
			$data['id'] 	 	 = $id;
			$data['duplicate'] 	 = $id;
			$data['POSM_statusID']	= isset($sID) ? $sID : NULL;
			$sql 			 	 = $this->db->query("SELECT *, vendors.id as vID FROM vendors INNER JOIN country ON country.id = vendors.countryID $filter_add");
			$data['vendors'] 	 = $sql->result_array();
			
			$sql 			 	 = $this->db->query("SELECT * FROM items_images WHERE itemID = $id ORDER BY defaultStatus DESC");
			$data['items_images']= $sql->result_array();
			
			$data['vfile']	 = 'itemFORM.php';
		}
		elseif($action=="add")
		{
			$this->modules->module_checker(18,'ADD');
			
			//USER MANUAL
			$data['USER_MANUAL'] = $this->modules->user_manual(13);
			
			$POSM_statusID			= isset($sID)			? $sID : NULL;
			$data['POSM_statusID']	= isset($POSM_statusID) ? $POSM_statusID : 154;
			$data['vfile']			= 'itemFORM.php';
			$sql 					= $this->db->query("SELECT *, vendors.id as vID FROM vendors INNER JOIN country ON country.id = vendors.countryID $filter_add");
			$data['vendors'] 		= $sql->result_array();
		}
		elseif($action=="update")
		{
			if($_POST==NULL){
				redirect(HTTP_PATH.'itemDatabase/items', 'location', 301);
				die();
			}
			
			//$this->modules->module_checker(18,'EDIT');
			isset($Long_Description) ? $dbFields['Long_Description'] 	= $Long_Description : '';
			isset($brandID) 		 ? $dbFields['brandID'] 		  	= $brandID 			: '';
			isset($POSMTypeID) 	 	 ? $dbFields['POSMTypeID'] 	  		= $POSMTypeID 		: '';
			isset($POSMStatusID) 	 ? $dbFields['POSMStatusID'] 	  	= $POSMStatusID 	: '';
			isset($OUTLETStatusID) 	 ? $dbFields['OUTLETStatusID']   	= $OUTLETStatusID 	: $dbFields['OUTLETStatusID']   	= 0;
			isset($PremiumTypeID) 	 ? $dbFields['PremiumTypeID']    	= $PremiumTypeID 	: $dbFields['PremiumTypeID']    	= 0;
			isset($MaterialTypeID) 	 ? $dbFields['MaterialTypeID']   	= $MaterialTypeID 	: '';
			isset($countryID) 		 ? $dbFields['countryID'] 		  	= $countryID 		: '';
			isset($itemName) 	     ? $dbFields['itemName']         	= addslashes($itemName) : '';
			isset($Short_Description)? $dbFields['Short_Description'] 	= $Short_Description: '';
			isset($UnitPrice) 		 ? $dbFields['UnitPrice'] 		  	= $UnitPrice 		: '';
			isset($USD_Price) 		 ? $dbFields['USD_Price'] 		  	= $USD_Price 		: '';
			isset($MOQ) 			 ? $dbFields['MOQ'] 			  	= $MOQ 				: '';
			isset($UOM) 		     ? $dbFields['UOM'] 			  	= $UOM 				: '';
			isset($price_rangeID) 	 ? $dbFields['price_rangeID'] 		= $price_rangeID 	: '';
			isset($Fields0001) 		 ? $dbFields['Fields0001'] 	  = $Fields0001 : '';
			isset($Fields0002) 		 ? $dbFields['Fields0002'] 	  = $Fields0002 : '';
			isset($Fields0003) 		 ? $dbFields['Fields0003'] 	  = $Fields0003 : '';
			isset($Fields0004) 		 ? $dbFields['Fields0004'] 	  = $Fields0004 : '';
			isset($Fields0005) 		 ? $dbFields['Fields0005'] 	  = $Fields0005 : '';
			isset($estimated_production_lead_time) ? $dbFields['estimated_production_lead_time'] = $estimated_production_lead_time : '';
			isset($price_validity) 	? $dbFields['price_validity'] = $price_validity : '';
			
			isset($publish) 	 	 		? $dbFields['publish'] 		  			= $publish 				 : '';
			isset($irrelevant) 	 	 		? $dbFields['irrelevant'] 		  		= $irrelevant 			 : 'n';
			isset($publish_other_country) 	? $dbFields['publish_other_country']  	= $publish_other_country : '';
			isset($country_of_origin) 		? $dbFields['country_of_origin'] 		= $country_of_origin 	 : '';
			
			isset($plant_inventory) 		? $dbFields['plant_inventory'] 			= $plant_inventory 		  : '';
			isset($supplier_stock_on_hand) 	? $dbFields['supplier_stock_on_hand'] 	= $supplier_stock_on_hand : '';
			isset($date_first_issue) 		? $dbFields['date_first_issue'] 		= $date_first_issue 	  : '';
			isset($date_last_used) 			? $dbFields['date_last_used'] 			= $date_last_used 		  : '';
			isset($activity_event_use) 		? $dbFields['activity_event_use'] 		= $activity_event_use 	  : '';
			$dbFields['dateLastEdited']   	= date('Y-m-d');
			
			//SET AS POPULAR ITEM
			isset($tag_as_popular) 			? $dbFields['popular'] 			        = 'y' 		  : '';
			isset($tag_as_unpopular) 		? $dbFields['popular'] 			        = 'n' 		  : '';
			
			//CHECK IF POSSIBLE FOR DATE RELEASED UPDATE
			$sql = $this->db->query("SELECT dateReleased FROM items WHERE id = $id LIMIT 0,1");
			$row = $sql->row();
			if($publish=='y' AND $row->dateReleased=='0000-00-00') $dbFields['dateReleased'] = date('Y-m-d');
			
			//CHECK REQUIRED FIELDS
			if($publish=='y' AND $this->field_checker($_POST)!=''){
			
				$data['id'] 	 		= $id;
				$data['POSM_statusID']	= isset($sID) ? $sID : NULL;
				$sql 			 		= $this->db->query("SELECT *, vendors.id as vID FROM vendors INNER JOIN country ON country.id = vendors.countryID $filter_add");
				$data['vendors'] 		= $sql->result_array();
				
				$sql 			 		= $this->db->query("SELECT * FROM items_images WHERE itemID = $id ORDER BY defaultStatus DESC");
				$data['items_images'] 	= $sql->result_array();
				$data['vfile']			= 'itemFORM.php';
				
				$data['POST']			= $_POST;
				$data['msg']			= array('msg_type'=>'alert-warning','msg_desc'=>$this->field_checker($_POST)); 
			}
			else
			{
				//UPDATE ITEM CODE
				$itemField['itemCode'] = $this->generate_ItemCode($id,$countryID,isset($POSMTypeID) ? $POSMTypeID : 0);
				$this->c3model->c3crud("update",$table,$itemField,$id);
				
				//IMAGE
				$config['upload_path'] = FCPATH2.'/img/items/';
				$config['allowed_types'] = 'gif|jpg|png';
				$this->upload->initialize($config);
				
				if($this->upload->do_multi_upload("files")){			
					$files = $this->upload->get_multi_upload_data();
					foreach($files as $f)
					{
						extract($f); 
						//GENERATE IMAGE CODE
						$new_file_name=$this->generateImgCode($id,$itemField['itemCode'],$f['file_name']);
						$this->imageResize($file_name,$new_file_name,$f['file_path']);
						$refdbFields['itemID'] = $id; 
						$refdbFields['image']  = $new_file_name; 
						
						$res = $this->c3model->c3crud("insert",'items_images',$refdbFields,'');
					}
					
					//TAG LAST IMAGE
					$this->db->query("UPDATE  `items_images` SET  `defaultStatus` =  '0' WHERE  itemID = $id");
					$query = $this->db->query("SELECT MAX(items_images.id) as lastImageID FROM items_images WHERE itemID = $id LIMIT 1");
					$row = $query->row();

					$this->db->query("UPDATE  `items_images` SET  `defaultStatus` =  '1' WHERE  `items_images`.`id` = ". $row->lastImageID );
				}
				
				$this->c3model->c3crud("update",$table,$dbFields,$id);
				
				//VENDOR REFERENCE
				//DELETE PREVIUOS VENDORS
				if($data['VENDORS_EDIT']==TRUE){
					$this->c3model->c3crud("no-res",'','','',"DELETE FROM itemVendorsRef WHERE itemID='$id'");
				
					$refFields['itemID'] =  $id;
					
					if(isset($multipleVendors)){
						foreach($multipleVendors as $mV => $value)
						{
							$refFields['vendorID'] = $value;
							$res = $this->c3model->c3crud("insert",'itemVendorsRef',$refFields,'');
						}
					}
				}
			
				//LOGS
				if(!isset($itemName)){
					$sql = "SELECT itemName FROM items WHERE id = $id";
					$sql = $this->db->query($sql);
					$row = $sql->row();
					$itemName = $row->itemName;
				}
				//LOGS	
				$action = "edit";
				if(isset($tag_as_popular)) 		 $action = "popular";
				if(isset($tag_as_unpopular)) 	 $action = "unpopular";
				if($dbFields['irrelevant']=='y') $action = "disapprove";
				
				$CI->rec_logs->w($id,$itemName,'Item Database','Items',$action,$itemField['itemCode']);
				$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Item has been updated.');
				
				if(isset($tag_as_popular))
					redirect(HTTP_PATH.'itemDatabase/popular_items/tagged_as_popular', 'location', 301);
				if(isset($tag_as_unpopular))
					redirect(HTTP_PATH.'itemDatabase/items/tagged_as_unpopular', 'location', 301);
				if($dbFields['irrelevant']=='y')
					redirect(HTTP_PATH.'itemDatabase/disapproved_items/disapprove', 'refresh');
				//POPULAR ITEMS
				$sql = $this->db->query("SELECT popular FROM items WHERE id = $id");
				$row = $sql->row();
				if($publish=='n'){
					if($this->modules->module_checker2(55,'REVIEW')==true)
						redirect(HTTP_PATH.'itemDatabase/BU_Marketing_Items_review/update_success', 'location', 301);
					if($this->modules->module_checker2(56,'REVIEW')==true)
						redirect(HTTP_PATH.'itemDatabase/BU_Logistics_Items_review/update_success', 'location', 301);
				}
				elseif($publish=='y' AND $row->popular=='n'){
					redirect(HTTP_PATH.'itemDatabase/BU_Logistics_Items_review/update_success', 'location', 301);
				}
				elseif($publish=='y' AND $row->popular=='y'){
					redirect(HTTP_PATH.'itemDatabase/popular_items/update_success', 'location', 301);
				}
			}
		}
		
		
		$sqlSTr =  "SELECT *,
					OUTLET_Status.statusName as OutletStatusName, 
					POSM_Status.statusName as POSMStatusName,
					POSM_Type.typeName as POSM_TypeName,
					items.id as itemID,  count(item_views.itemID) as iViews 
					FROM items 
					LEFT JOIN POSM_Type 		ON items.POSMTypeID = POSM_Type.id 
					LEFT JOIN POSM_Status 		ON items.POSMStatusID = POSM_Status.id 
					LEFT JOIN OUTLET_Status 	ON items.OUTLETStatusID = OUTLET_Status.id
					LEFT JOIN premiumItemType 	ON items.PremiumTypeID = premiumItemType.id 
					LEFT JOIN MATERIAL_Type 	ON items.MaterialTypeID = MATERIAL_Type.id 
					LEFT JOIN country 			ON items.countryID = country.id 
					LEFT JOIN brands  			ON items.brandID = brands.id 
					LEFT JOIN item_views  		ON items.id 	 = item_views.itemID
					$filter 
					GROUP BY items.id ORDER BY $order";

		$sql = $this->db->query($sqlSTr);
		$sql = $sql->result_array();
		$total_rec = count($sql);
		
		$data['total_rec'] = $total_rec;
		$pagenum 		   = $data['active_page'];
		$data['page_rows'] = 15; 
		$data['last'] 	   = ceil($total_rec/$data['page_rows']);		
		$max 			   = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		
		$sql = $this->db->query($sqlSTr ." ". $max );
		$data['data'] = $sql->result_array();
	   
	    if($this->modules->access_checker()==TRUE)
	    {
			$this->load->view('innerPages',$data); 
		}
		else
		{
		$data['vfile']				= 'login.php';
		$data['title']				= 'SMBi System Log-in | SMBi';
		$data['page_title']			= 'SMBi System Log-in';
		$data['meta_description']	= 'San Miguel Brewing International';
		$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
		$this->load->view('login',$data); 	
	   }
	}
	
	function generateImgCode($itemID='',$itemCode='',$file_name='')
	{ $ctr = 0;
	  $row = $this->db->query("SELECT COUNT(id) as ctr FROM items_images WHERE itemID = $itemID");
	  $row = $row->row();
	  $ctr = $row->ctr++;
	  $file_name = substr($file_name,-4);
	  $new_filename = $itemCode."-".$ctr.$file_name;
	  //CHECK IF EXIST
	  $sql =  $this->db->query("SELECT COUNT(id) as ctr2 FROM items_images WHERE itemID = $itemID AND image = '$new_filename'");
      $sql =  $sql->row();
	  if($sql->ctr2>=1) $ctr = $ctr++;
	  
	  $new_filename = $itemCode."-".$ctr.$file_name;
	 
	  $nameChecker=false;
	  $present = 0;
	  while($nameChecker!=true)
	  {
	   //DOUBLE CHECK IF EXIST
	   $sql =  $this->db->query("SELECT COUNT(id) as present FROM items_images WHERE itemID = $itemID AND image = '$new_filename'");
	   $sql =  $sql->row();
	 
	   if($sql->present>=1){ 
	    $ctr = $ctr+1;
		$new_filename = $itemCode."-".$ctr.$file_name;
		$nameChecker=false;
	   }else{
	    $nameChecker=true;
	    return $new_filename = $itemCode."-".$ctr.$file_name;
	   }
	  }
	}
	
	function strCut($str=''){
	 $str = substr($str,7);
	 $str = explode("/",$str);
	 $new_Str="http://";
	 foreach($str as $s)
	  if($s!=end($str)) $new_Str.="$s/";

	 return substr($new_Str,0,-1);
	}
	
	function tag_for_purgingRedirect($referer,$initail_val)
	{
	 //echo $_SERVER['HTTP_REFERER'];
	 $ref = $referer;
	 $ref = substr($ref,7);
	 $ref = explode("/",$ref);
	 $new_Str="http://";
	 //IF cannot_be_purge || item_has_been_submitted_for_purging 
	 if(end($ref)=="cannot_be_purge" 
	 OR end($ref)=="item_has_been_submitted_for_purging" 
	 OR end($ref)=="items_has_been_submitted_for_purging" 
	 OR end($ref)=="item_has_been_submitted_for_archiving" 
	 OR end($ref)=="items_has_been_submitted_for_archiving" 
	 OR end($ref)=="cannot_be_archive" 
	 OR end($ref)=="some_cannot_be_archive" 
	 OR end($ref)=="some_cannot_be_purge" 
	 OR end($ref)=="tagged_as_popular_item" 
	 OR end($ref)=="tagged_as_popular_items" 
	 OR end($ref)=="tagged_as_not_popular_item" 
	 OR end($ref)=="tagged_as_not_popular_items" 
	 OR end($ref)=="no_selected_item" 
	 OR end($ref)=="item_has_been_published" 
	 OR end($ref)=="restore_to_item_db" 
	 OR end($ref)=="item_has_been_disapproved" 
	 OR end($ref)=="insert_sucess" 
	 OR end($ref)=="update_success")
	 {
	 foreach($ref as $s){
	  if($s!=end($ref)) $new_Str.="$s/";
	 }
	 $new_Str .= $initail_val."/";
	 }elseif(end($ref)=="items")
	 {
	  $new_Str = $referer."/".$initail_val."/";
	 }else{
	  $new_Str = $referer."/".$initail_val."/";
	 }
	 return substr($new_Str,0,-1);
	}
		
	function items($action='',$id='',$txtsearch='',$selPOSMType='',$selPOSMStatus='',$selPremiumType='',$seloutlet='',$selCountry='',$selBrand='',$selMaterial='',$items_date='',$nviews='',$sort_by_price='',$priceRange='',$priceFrom='',$priceTo='',$year='',$month='',$priceRangeID='',$msg='')
	{
		//print_r($_SERVER);
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		//print_r($_SESSION);
		$this->modules->module_checker(18,'REVIEW');
		$filter = $this->modules->itemdb_country();
		$filter_add = $this->modules->country();
	
		$table= "items";
		$data['vfile']				= 'items.php';
	    $data['title']				= 'Items | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//CRUD
		$data['ADD'] 	=  $this->modules->crud_checker(18,'ADD');
		$data['EDIT'] 	=  $this->modules->crud_checker(18,'EDIT');
		//PURGING
		$data['DELETE'] 		 =  $this->modules->crud_checker(18,'DELETE');
		$data['ADD_TO_ARCHIVE']  =  $this->modules->crud_checker(18,'ARCHIVE');
		$data['APPROVE'] 		 =  $this->modules->crud_checker(18,'APPROVE');
		$data['TAG_AS_POPULAR']  =  $this->modules->crud_checker(18,'POPULAR');
		$data['DISABLE_BUTTONS'] =  FALSE;
		
		$data['VENDORS_EDIT'] 	=  $this->modules->crud_checker(26,'EDIT');
		$data['SAVE_FOR_LATER'] =  $this->modules->BU_Logistics();
		
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(5);

		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
	    $data['breadCrumbs']		= '<a href='.$HTTP_PATH.'itemDatabase> Item Database </a>';
	    $data['breadCrumbs']	   .= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']	   .= '<a href='.$HTTP_PATH.'itemDatabase/items> Items </a>';

		//CSRF
		$data['csrf'] = "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>"; 
		$data['searchAction'] = HTTP_PATH. "itemDatabase/items.html"; 
		
		
		//search filter
		$data['post'] = $_POST; $filter2="";
		$order = "items.dateReleased DESC,";
		//print_r($_POST);
		//echo $priceRangeID;
		$data['redirectTo']	= "itemDatabase";
        if(($txtsearch!='' OR $selPOSMType!='' OR $selPOSMStatus!='' OR $selPremiumType!='' OR $seloutlet!='' OR $selCountry!='' OR $selBrand!='' OR $selMaterial!='' OR $items_date=!'' OR $nviews!='' OR $sort_by_price!='' OR $priceRange!='' OR $priceRangeID!=''))		
		{ 
			extract($_POST);  
			//$_SESSION['txtsearch']="";
              if($selPOSMType!='null' 	 AND $selPOSMType!='')     	$filter2 .= " AND POSMTypeID		='$selPOSMType'";
              if($selPOSMStatus!='null'  AND $selPOSMStatus!='')   	$filter2 .= " AND POSMStatusID		='$selPOSMStatus'";
			  if($selPremiumType!='null' AND $selPremiumType!='')  	$filter2 .= " AND PremiumTypeID		='$selPremiumType'";
			  if($seloutlet!='null' 	 AND $seloutlet!='')       	$filter2 .= " AND OUTLETStatusID	='$seloutlet'";
			  if($selCountry!='null'	 AND $selCountry!='')      	$filter2 .= " AND countryID			='$selCountry'";
			  if($selBrand!='null' 		 AND $selBrand!='')        	$filter2 .= " AND brandID 			='$selBrand'";
			  if($selMaterial!='null' 	 AND $selMaterial!='')     	$filter2 .= " AND MaterialTypeID	='$selMaterial'";
			  if($priceRangeID!='null' 	 AND $priceRangeID!='')     $filter2 .= " AND price_rangeID	    ='$priceRangeID'";
			  //if($price_range!='null' 	 AND $price_range!='')     	$filter2 .= " AND price_rangeID	    ='$price_range'";
			  
			  
			  if($txtsearch!='' AND $txtsearch!='null'){
				$txtsearch = addslashes($txtsearch);
				$txtsearch = trim($txtsearch);
				$txtsearch = str_replace("%20"," ",$txtsearch);
				$filter2 .= " AND ( itemCode like  '%$txtsearch%'  or itemName like  '%$txtsearch%'  or Short_Description like '%$txtsearch%' or Long_Description like '%$txtsearch%')";
			  }   
			  

			  //PRICE RANGE	
			  if(($priceRange!='' AND $priceRange!='null') AND is_numeric($priceFrom) AND is_numeric($priceTo))
					$filter2 = "AND $priceRange >= $priceFrom AND $priceRange <= $priceTo ";
			  
			  
			  //DATE RANGE
			  $m=0;
			  if($year!='' AND $year!='null')
				$m = $year * 12;  
			  if($month!='' AND $month!='null')
				$m += $month;
			  
			  if(($year!='' AND $year!='null' AND $year!=0)  OR ($month!='' AND $month!='null' AND $month!=0)){
				$filter2 .= " AND items.dateAdded <= CURDATE() AND items.dateAdded >= (SELECT CURDATE() - INTERVAL $m MONTH) ";
			  }
			  
			  
			   //SORT BY DATE
			   if(($nviews!='' AND $nviews!='null') OR ($sort_by_price!='' AND $sort_by_price!='null') AND ($items_date!=''  OR $items_date!='null'))
					$order = "";
			 
			   if($nviews!='null' 	 	 AND $nviews!='')          $order  .= " COUNT(item_views.itemID) $nviews,";
			   if($sort_by_price!='null' AND $sort_by_price!='')   $order  .= str_replace("-"," ",$sort_by_price.","); 
			   if($items_date!='null' 	 AND $items_date!='')      $order  .= " items.dateReleased $items_date,"; 
			  
		} 
		$order = substr($order, 0,-1);
		
		$data['post'] = $_POST; 
		
		if($filter=='' & $filter2!='') $filter = "WHERE  ". substr($filter2,4)."  AND items.purge = 'n' AND items.archive = 'n'  AND DATEDIFF(CURDATE(),items.dateReleased) < ".$this->modules->itemsAge()." ";
		else $filter = "$filter   $filter2   AND items.purge = 'n' AND items.archive = 'n' AND DATEDIFF(CURDATE(),items.dateReleased) < ".$this->modules->itemsAge()." ";

		//TOTAL NUMBER OF ROWS
		$data['active_page']= 1;
		if($id!='') 
			$data['active_page']= $id;
					
		//NEW FUNCTION
		$url="";
		$data['txtsearch'] 		= "null";
		$data['selPOSMType'] 	= "null";
		$data['selPOSMStatus'] 	= "null";
		$data['selPremiumType'] = "null";
		$data['seloutlet'] 		= "null";
		$data['selCountry'] 	= "null";
		$data['selBrand'] 		= "null";
		$data['selMaterial'] 	= "null";
		$data['items_date'] 	= "null";
		$data['nviews'] 		= "null";
		$data['sort_by_price'] 	= "null";
		$data['priceRange'] 	= "null";
		$data['priceFrom'] 		= "null";
		$data['priceTo'] 		= "null";
		$data['year'] 			= "null";
		$data['month'] 			= "null";
		$data['price_range'] 	= "null";
		$data['priceRangeID'] 	= "null";


		if($action=="page")
		{
			$this->modules->module_checker(18,'REVIEW');
			//REPOST DATA
			if($txtsearch!='')   	
				$data['txtsearch'] 		=  $txtsearch;
			if($selPOSMType!='')   	
				$data['selPOSMType'] 	=  $selPOSMType;
			if($selPOSMStatus!='')   	
				$data['selPOSMStatus'] 	=  $selPOSMStatus;	
			if($selPremiumType!='')   	
				$data['selPremiumType'] =  $selPremiumType;	
			if($seloutlet!='')   	
				$data['seloutlet'] 		=  $seloutlet;
			if($selCountry!='')   	
				$data['selCountry'] 	=  $selCountry;
			if($selBrand!='')   	
				$data['selBrand'] 		=  $selBrand;
			if($selMaterial!='')   	
				$data['selMaterial'] 	=  $selMaterial;
			if($items_date!='')   	
				$data['items_date'] 	=  $items_date;
			if($nviews!='')   	
				$data['nviews'] 		=  $nviews;
			if($sort_by_price!='')   	
				$data['sort_by_price'] 	=  $sort_by_price;
			if($priceRange!='')   	
				$data['priceRange'] 	=  $priceRange;
			if($priceFrom!='')   	
				$data['priceFrom'] 		=  $priceFrom;
			if($priceTo!='')   	
				$data['priceTo'] 		=  $priceTo;
			if($year!='')   	
				$data['year'] 			=  $year;
			if($month!='')   	
				$data['month'] 			=  $month;
			if($priceRangeID!='')   	
				$data['priceRangeID'] 	=  $priceRangeID;
		}
		
		//SEARCH ACTION
		$data['url']  = $data['txtsearch']."/".$data['selPOSMType']."/".$data['selPOSMStatus']."/".$data['selPremiumType']."/".$data['seloutlet']."/".$data['selCountry']."/".$data['selBrand']."/".$data['selMaterial'].'/';
		$data['url'] .= $data['items_date']."/".$data['nviews']."/".$data['sort_by_price']."/".$data['priceRange']."/".$data['priceFrom']."/".$data['priceTo']."/".$data['year']."/".$data['month']."/".$data['priceRangeID']; 
		$data['searchAction'] = HTTP_PATH. "itemDatabase/redirect_link/".$data['redirectTo']."/page/1/".$data['url']; 
		
	
		//extract($_POST);
	    if($action=="insert_sucess")			 			 														  $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Item has been save.");
		elseif($action=="update_success"    	  			     || $msg=="update_success")							  $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Item has been updated.");
		elseif($action=="cannot_be_purge"   	  			     || $msg=="cannot_be_purge")						  $data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>"Items cannot be purge because it is a result of campaign or featured in Common Gallery.");
		elseif($action=="some_cannot_be_purge"   	  			 || $msg=="some_cannot_be_purge")					  $data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>"Some items cannot be purge because it is a result of campaign or featured in Common Gallery.");
		elseif($action=="cannot_be_archive" 	  			     || $msg=="cannot_be_archive")					 	  $data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>"Item cannot be archive because it is a result of campaign or featured in Common Gallery.");
		elseif($action=="some_cannot_be_archive" 	  			 || $msg=="some_cannot_be_archive")					  $data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>"Some items cannot be archive because it is a result of campaign or featured in Common Gallery.");
		elseif($action=="tagged_as_unpopular" 	  			     || $msg=="tagged_as_unpopular")  				 	  $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Item has been tagged as unpopular.");
		elseif($action=="tagged_as_popular_items" 			     || $msg=="tagged_as_popular_items")  			 	  $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Tagged as popular items.");
		elseif($action=="tagged_as_popular_item"  			     || $msg=="tagged_as_popular_item")  				  $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Tagged as popular item.");
		elseif($action=="tagged_as_not_popular_item"  		     || $msg=="tagged_as_not_popular_item")  			  $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Tagged as not popular item.");
		elseif($action=="items_has_been_submitted_for_purging"   || $msg=="items_has_been_submitted_for_purging")  	  $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Items has been submitted for purging.");
		elseif($action=="item_has_been_submitted_for_purging"    || $msg=="item_has_been_submitted_for_purging")   	  $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Item has been submitted for purging.");
		elseif($action=="tagged_as_not_popular_items"  		     || $msg=="tagged_as_not_popular_items")   			  $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Items has tagged as not popular items.");
		elseif($action=="items_has_been_submitted_for_archiving" || $msg=="items_has_been_submitted_for_archiving")   $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Items has been submitted for archiving.");
		elseif($action=="item_has_been_submitted_for_archiving"  || $msg=="item_has_been_submitted_for_archiving")    $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Item has been submitted for archiving.");
		elseif($action=="no_selected_item" 						 || $msg=="no_selected_item")    					  $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"No selected item.");
		elseif($action=="tagged_as_disapproved" 			     || $msg=="tagged_as_disapproved")    				  $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Tagged as disapproved.");
		elseif($action=="item_has_been_published"   			 || $msg=="item_has_been_published")   				  $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>"Item has been published.");
		elseif($action=="item_has_been_disapproved" 			 || $msg=="item_has_been_disapproved")   			  $data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>"Item has been disapproved.");
		elseif($action=="disapprove" 							 || $msg=="disapprove")								  $data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>"Item has been tag as irrelevant.");
		elseif($action=="insert")
		{
			$this->modules->module_checker(18,'ADD');
			
			if($_POST==NULL){
				redirect(HTTP_PATH.'itemDatabase/items/add', 'location', 301);
				die();
			}
			
			isset($Long_Description) ? $dbFields['Long_Description'] 	= $Long_Description : '';
			isset($brandID) 		 ? $dbFields['brandID'] 		  	= $brandID 			: '';
			isset($POSMTypeID) 	 	 ? $dbFields['POSMTypeID'] 	  		= $POSMTypeID 		: '';
			isset($POSMStatusID) 	 ? $dbFields['POSMStatusID'] 	  	= $POSMStatusID 	: '';
			isset($OUTLETStatusID) 	 ? $dbFields['OUTLETStatusID']   	= $OUTLETStatusID 	: '';
			isset($PremiumTypeID) 	 ? $dbFields['PremiumTypeID']    	= $PremiumTypeID 	: '';
			isset($MaterialTypeID) 	 ? $dbFields['MaterialTypeID']   	= $MaterialTypeID 	: '';
			isset($countryID) 		 ? $dbFields['countryID'] 		  	= $_SESSION['countryID'] : '';
			isset($itemName) 	     ? $dbFields['itemName']         	= addslashes($itemName) 		: '';
			isset($Short_Description)? $dbFields['Short_Description'] 	= $Short_Description: '';
			isset($UnitPrice) 		 ? $dbFields['UnitPrice'] 		  	= $UnitPrice 		: '';
			isset($USD_Price) 		 ? $dbFields['USD_Price'] 		  	= $USD_Price 		: '';
			isset($MOQ) 			 ? $dbFields['MOQ'] 			  	= $MOQ 				: '';
			isset($UOM) 		     ? $dbFields['UOM'] 			  	= $UOM 				: '';
			isset($price_rangeID) 	 ? $dbFields['price_rangeID'] 		= $price_rangeID 	: '';
			$dbFields['dateAdded']    = date('Y-m-d');
			$dbFields['dateReleased'] = ($publish=='y') ? date('Y-m-d') : '0000-00-00';
			$dbFields['user_id'] 	  = $_SESSION['user_id'];
			
			isset($Fields0001) 		 ? $dbFields['Fields0001'] 	  = $Fields0001 : '';
			isset($Fields0002) 		 ? $dbFields['Fields0002'] 	  = $Fields0002 : '';
			isset($Fields0003) 		 ? $dbFields['Fields0003'] 	  = $Fields0003 : '';
			isset($Fields0004) 		 ? $dbFields['Fields0004'] 	  = $Fields0004 : '';
			isset($Fields0005) 		 ? $dbFields['Fields0005'] 	  = $Fields0005 : '';
			isset($estimated_production_lead_time) ? $dbFields['estimated_production_lead_time'] = $estimated_production_lead_time : '';
			isset($price_validity) 	? $dbFields['price_validity'] = $price_validity : '';
			
			isset($publish) 	 	 		? $dbFields['publish'] 		  			= $publish 				 : '';
			isset($publish_other_country) 	? $dbFields['publish_other_country']  	= $publish_other_country : '';
			isset($country_of_origin) 		? $dbFields['country_of_origin'] 		= $country_of_origin 	 : '';
			
			isset($plant_inventory) 		? $dbFields['plant_inventory'] 			= $plant_inventory 		  : '';
			isset($supplier_stock_on_hand) 	? $dbFields['supplier_stock_on_hand'] 	= $supplier_stock_on_hand : '';
			isset($date_first_issue) 		? $dbFields['date_first_issue'] 		= $date_first_issue 	  : '';
			isset($date_last_used) 			? $dbFields['date_last_used'] 			= $date_last_used 		  : '';
			isset($activity_event_use) 		? $dbFields['activity_event_use'] 		= $activity_event_use 	  : '';
			
			
			//CHECK REQUIRED FIELDS
			if($publish=='y' AND $this->field_checker($_POST)!=''){
				$data['USER_MANUAL'] = $this->modules->user_manual(13);
			
				$data['POSM_statusID']	= isset($sID) ? $sID : NULL;
				$sql 			 		= $this->db->query("SELECT *, vendors.id as vID FROM vendors INNER JOIN country ON country.id = vendors.countryID $filter_add");
				$data['vendors'] 		= $sql->result_array();
				
				$data['items_images'] 	= array();
				$data['vfile']			= 'itemFORM.php';
				
				$data['POST']			= $_POST;
				$data['msg']			= array('msg_type'=>'alert-warning','msg_desc'=>$this->field_checker($_POST)); 
			}
			else
			{
				//DUPLICATE
				if(isset($duplicate) & $_SESSION['countryID']!=0) $dbFields['publish'] = 'n';
				
				$res = $this->c3model->c3crud("insert",$table,$dbFields,'');
				
				//GET MAX ID
				$sql		= "select max(id) as max_id FROM $table";
				$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
				$maxID 	= $lastID[0]['max_id'];
				
				//UPDATE ITEM CODE
				$itemField['itemCode'] = $this->generate_ItemCode($maxID,$countryID,isset($POSMTypeID) ? $POSMTypeID : 0);
				$this->c3model->c3crud("update",$table,$itemField,$maxID);
		
				//IMAGE
				$config['upload_path'] = FCPATH2.'/img/items/';
				$config['allowed_types'] = 'gif|jpg|png';
				$this->upload->initialize($config);
				
				if($this->upload->do_multi_upload("files")){
					$files = $this->upload->get_multi_upload_data();
					
					foreach($files as $f)
					{
						extract($f);
						//GENERATE IMAGE CODE
						$new_file_name=$this->generateImgCode($maxID,$itemField['itemCode'],$f['file_name']);
						$this->imageResize($file_name,$new_file_name,$f['file_path']);
						$refdbFields['itemID'] = $maxID; 
						$refdbFields['image']  = $new_file_name; 
						
						$res = $this->c3model->c3crud("insert",'items_images',$refdbFields,'');
					}
					
					//TAG LAST IMAGE
					$query = $this->db->query("SELECT MAX(items_images.id) as lastImageID FROM items_images WHERE itemID = $maxID LIMIT 1");
					$row = $query->row();
					$this->db->query("UPDATE `items_images` SET  `defaultStatus` =  '1' WHERE  `items_images`.`id` = ". $row->lastImageID );
				}
				
				
				//VENDOR REFERENCE
				if(isset($multipleVendors))
				{
					$sql		= "select max(id) as max_id FROM items";
					$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
					$menu_id 	= $lastID[0]['max_id'];
					$refFields['itemID'] =  $menu_id;
					
					foreach($multipleVendors as $mV => $value)
					{
						$refFields['vendorID'] = $value;
						$res = $this->c3model->c3crud("insert",'itemVendorsRef',$refFields,'');
					}
				}
				
				//DUPLICATE IMAGES
				if(isset($duplicate)){
					$sql = "SELECT image FROM items_images WHERE itemID = $duplicate";
					$img = $this->db->query($sql);
					$img = $img->result_array();
					
					if($img){
						foreach($img as $i){
							extract($i);
							$refdbFields['itemID'] = $maxID; 
							$refdbFields['image']  = $image; 
							$res = $this->c3model->c3crud("insert",'items_images',$refdbFields,'');
						}
					}
					
					//TAG LAST IMAGE
					$this->db->query("UPDATE  `items_images` SET  `defaultStatus` =  '0' WHERE  `items_images`.`itemID` = ". $maxID );
					$query = $this->db->query("SELECT MIN(items_images.id) as lastImageID FROM items_images WHERE itemID = $maxID LIMIT 0,1");
					$row = $query->row();
					if(isset($row->lastImageID))
						$this->db->query("UPDATE  `items_images` SET  `defaultStatus` =  '1' WHERE  `items_images`.`id` = ". $row->lastImageID );				
				}	
				
				
				//LOGS
				$CI->rec_logs->w($maxID,$itemName,'Item Database','Items',isset($duplicate) ? 'duplicate' : 'add',$itemField['itemCode']);
				
				//die();
				if($publish=='n'){
					if($this->modules->module_checker2(55,'REVIEW')==true)
						redirect(HTTP_PATH.'itemDatabase/BU_Marketing_Items_review/insert_sucess', 'location', 301);
					if($this->modules->module_checker2(56,'REVIEW')==true)
						redirect(HTTP_PATH.'itemDatabase/BU_Logistics_Items_review/insert_sucess', 'location', 301);
				}else{
					redirect(HTTP_PATH.'itemDatabase/items/insert_sucess', 'location', 301);
				}
			}
		}
		elseif($action=="deleteOneItem")
		{
			$this->modules->module_checker(18,'DELETE');
			
			$tables = array(
			  array('tbl'=>'iLikeResultRef',
					'fld'=>'itemID'),
			  array('tbl'=>'iWantResultRef',
					'fld'=>'itemID'),
			  array('tbl'=>'votexRef',
					'fld'=>'itemID'),
			  array('tbl'=>'campaignItemsXref',
					'fld'=>'itemID')
					);
		
			if($this->modules->attr($tables,$id)==0)
			{
			//LOGS
			$sql = "SELECT itemName, itemCode FROM items WHERE id = $id";
			$sql = $this->db->query($sql);
			$sql = $sql->row();
			if(isset($sql->itemName))
				$CI->rec_logs->w($id,$sql->itemName,'Item Database','Items','delete',$sql->itemCode);
			
			$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Item has been deleted.');
			$this->c3model->c3crud("no-res",'','','',"DELETE FROM items 		 WHERE id='$id'");
	 	    $this->c3model->c3crud("no-res",'','','',"DELETE FROM itemVendorsRef WHERE itemID='$id'");
	 	    $this->c3model->c3crud("no-res",'','','',"DELETE FROM items_images   WHERE itemID='$id'");
	 	    $this->c3model->c3crud("no-res",'','','',"DELETE FROM item_views     WHERE itemID='$id'");
			
			}else{
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Item cannot be delete because it is being link to campaign.');
			}
			$data['active_page']= 1;
		}
		elseif($action=="deleteSelectedItem")
		{
			$this->modules->module_checker(18,'DELETE');
			
			$ctr=0;
			if(isset($selectedItems)){
				foreach($selectedItems as $cbr => $value)
				{
			
				$tables = array(
				  array('tbl'=>'iLikeResultRef',
						'fld'=>'itemID'),
				  array('tbl'=>'iWantResultRef',
						'fld'=>'itemID'),
				  array('tbl'=>'votexRef',
						'fld'=>'itemID'),
				  array('tbl'=>'campaignItemsXref',
						'fld'=>'itemID')
						);
						
					if($this->modules->attr($tables,$value)==0){
						$sql = "SELECT itemName, itemCode FROM items WHERE id = $value";
						$sql = $this->db->query($sql);
						$sql = $sql->row();
						if(isset($sql->itemName))
							$CI->rec_logs->w($value,$sql->itemName,'Item Database','Items','delete',$sql->itemCode);
						
						$this->c3model->c3crud("no-res",'','','',"DELETE FROM items 		 WHERE id='$value'");
						$this->c3model->c3crud("no-res",'','','',"DELETE FROM itemVendorsRef WHERE itemID='$value'");
						$this->c3model->c3crud("no-res",'','','',"DELETE FROM items_images   WHERE itemID='$value'");
						$this->c3model->c3crud("no-res",'','','',"DELETE FROM item_views     WHERE itemID='$id'");
					}else{
						$ctr++;
					}
				}
			}
			
			if($ctr==0)
			{
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Multiple Items has been deleted.');
			}else{
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Multiple Items cannot been delete, because it is being use in campaign.');
			}
			$data['active_page']= 1;
		}
		elseif($action=="edit")
		{
			$this->modules->module_checker(18,'EDIT');
			//USER MANUAL
			$data['USER_MANUAL'] = $this->modules->user_manual(13);
			//REFERER
			//UPDATE SUCCESS ALREADY
			$data['referrer_link']	 = $this->tag_for_purgingRedirect($_SERVER['HTTP_REFERER'],'update_success');
			//SWITCH VALIDATION
			if(isset($referrer_link)) $data['referrer_link'] = $referrer_link;
			
			$data['id'] 	 		= $id;
			$data['POSM_statusID']	= isset($sID) ? $sID : NULL;
			$filter = $this->modules->country2();
			$data['referrer'] 		= $txtsearch;
			$sql 			 		= $this->db->query("SELECT *, vendors.id as vID FROM vendors INNER JOIN country ON country.id = vendors.countryID $filter_add");
			$data['vendors'] 		= $sql->result_array();
			
			$sql 			 		= $this->db->query("SELECT * FROM items_images WHERE itemID = $id ORDER BY defaultStatus DESC");
			$data['items_images'] 	= $sql->result_array();
			$data['vfile']	 		= 'itemFORM.php';
		}
		elseif($action=="preview")
		{
			$this->modules->module_checker(18,'REVIEW');
			
			$data['id'] 	 = $id;
			$data['POSM_statusID']	= isset($sID) ? $sID : NULL;
			$sql 			 = $this->db->query("SELECT *, vendors.id as vID FROM vendors INNER JOIN country ON country.id = vendors.countryID $filter_add");
			$data['vendors'] = $sql->result_array();
			
			$sql 			 = $this->db->query("SELECT * FROM items_images WHERE itemID = $id");
			$data['items_images'] = $sql->result_array();
			$data['vfile']	 = 'itemPreviewFORM.php';
		}
		elseif($action=="duplicate")
		{
			$this->modules->module_checker(18,'ADD');
			
			$data['id'] 	 	 = $id;
			$data['duplicate'] 	 = $id;
			$data['POSM_statusID']	= isset($sID) ? $sID : NULL;
			$sql 			 	 = $this->db->query("SELECT *, vendors.id as vID FROM vendors INNER JOIN country ON country.id = vendors.countryID $filter_add");
			$data['vendors'] 	 = $sql->result_array();
			
			$sql 			 	 = $this->db->query("SELECT * FROM items_images WHERE itemID = $id ORDER BY defaultStatus DESC");
			$data['items_images']= $sql->result_array();
			
			$data['vfile']	 = 'itemFORM.php';
		}
		elseif($action=="add")
		{
			$this->modules->module_checker(18,'ADD');
			
			//USER MANUAL
			$data['USER_MANUAL'] = $this->modules->user_manual(13);
			
			$POSM_statusID			= isset($sID)			? $sID : NULL;
			$data['POSM_statusID']	= isset($POSM_statusID) ? $POSM_statusID : 154;
			$data['vfile']			= 'itemFORM.php';
			$sql 					= $this->db->query("SELECT *, vendors.id as vID FROM vendors INNER JOIN country ON country.id = vendors.countryID $filter_add");
			$data['vendors'] 		= $sql->result_array();
		}
		elseif($action=="update")
		{
			if($_POST==NULL){
				redirect(HTTP_PATH.'itemDatabase/items', 'location', 301);
				die();
			}
			
			//$this->modules->module_checker(18,'EDIT');
			isset($Long_Description) ? $dbFields['Long_Description'] 	= $Long_Description : '';
			isset($brandID) 		 ? $dbFields['brandID'] 		  	= $brandID 			: '';
			isset($POSMTypeID) 	 	 ? $dbFields['POSMTypeID'] 	  		= $POSMTypeID 		: '';
			isset($POSMStatusID) 	 ? $dbFields['POSMStatusID'] 	  	= $POSMStatusID 	: '';
			isset($OUTLETStatusID) 	 ? $dbFields['OUTLETStatusID']   	= $OUTLETStatusID 	: $dbFields['OUTLETStatusID']   	= 0;
			isset($PremiumTypeID) 	 ? $dbFields['PremiumTypeID']    	= $PremiumTypeID 	: $dbFields['PremiumTypeID']    	= 0;
			isset($MaterialTypeID) 	 ? $dbFields['MaterialTypeID']   	= $MaterialTypeID 	: '';
			isset($countryID) 		 ? $dbFields['countryID'] 		  	= $countryID 		: '';
			isset($itemName) 	     ? $dbFields['itemName']         	= addslashes($itemName) : '';
			isset($Short_Description)? $dbFields['Short_Description'] 	= $Short_Description: '';
			isset($UnitPrice) 		 ? $dbFields['UnitPrice'] 		  	= $UnitPrice 		: '';
			isset($USD_Price) 		 ? $dbFields['USD_Price'] 		  	= $USD_Price 		: '';
			isset($MOQ) 			 ? $dbFields['MOQ'] 			  	= $MOQ 				: '';
			isset($UOM) 		     ? $dbFields['UOM'] 			  	= $UOM 				: '';
			isset($price_rangeID) 	 ? $dbFields['price_rangeID'] 		= $price_rangeID 	: '';
			isset($Fields0001) 		 ? $dbFields['Fields0001'] 	  = $Fields0001 : '';
			isset($Fields0002) 		 ? $dbFields['Fields0002'] 	  = $Fields0002 : '';
			isset($Fields0003) 		 ? $dbFields['Fields0003'] 	  = $Fields0003 : '';
			isset($Fields0004) 		 ? $dbFields['Fields0004'] 	  = $Fields0004 : '';
			isset($Fields0005) 		 ? $dbFields['Fields0005'] 	  = $Fields0005 : '';
			isset($estimated_production_lead_time) ? $dbFields['estimated_production_lead_time'] = $estimated_production_lead_time : '';
			isset($price_validity) 	? $dbFields['price_validity'] = $price_validity : '';
			
			isset($publish) 	 	 		? $dbFields['publish'] 		  			= $publish 				 : '';
			isset($irrelevant) 	 	 		? $dbFields['irrelevant'] 		  		= $irrelevant 			 : 'n';
			isset($publish_other_country) 	? $dbFields['publish_other_country']  	= $publish_other_country : '';
			isset($country_of_origin) 		? $dbFields['country_of_origin'] 		= $country_of_origin 	 : '';
			
			isset($plant_inventory) 		? $dbFields['plant_inventory'] 			= $plant_inventory 		  : '';
			isset($supplier_stock_on_hand) 	? $dbFields['supplier_stock_on_hand'] 	= $supplier_stock_on_hand : '';
			isset($date_first_issue) 		? $dbFields['date_first_issue'] 		= $date_first_issue 	  : '';
			isset($date_last_used) 			? $dbFields['date_last_used'] 			= $date_last_used 		  : '';
			isset($activity_event_use) 		? $dbFields['activity_event_use'] 		= $activity_event_use 	  : '';
			$dbFields['dateLastEdited']   	= date('Y-m-d');
			
			//SET AS POPULAR ITEM
			isset($tag_as_popular) 			? $dbFields['popular'] 			        = 'y' 		  : '';
			isset($tag_as_unpopular) 		? $dbFields['popular'] 			        = 'n' 		  : '';
			
			//CHECK IF POSSIBLE FOR DATE RELEASED UPDATE
			$sql = $this->db->query("SELECT dateReleased FROM items WHERE id = $id LIMIT 0,1");
			$row = $sql->row();
			if($publish=='y' AND $row->dateReleased=='0000-00-00') $dbFields['dateReleased'] = date('Y-m-d');
			
			//CHECK REQUIRED FIELDS
			if($publish=='y' AND $this->field_checker($_POST)!=''){
			
				$data['id'] 	 		= $id;
				$data['POSM_statusID']	= isset($sID) ? $sID : NULL;
				$sql 			 		= $this->db->query("SELECT *, vendors.id as vID FROM vendors INNER JOIN country ON country.id = vendors.countryID $filter_add");
				$data['vendors'] 		= $sql->result_array();
				
				$sql 			 		= $this->db->query("SELECT * FROM items_images WHERE itemID = $id ORDER BY defaultStatus DESC");
				$data['items_images'] 	= $sql->result_array();
				$data['vfile']			= 'itemFORM.php';
				
				$data['POST']			= $_POST;
				$data['msg']			= array('msg_type'=>'alert-warning','msg_desc'=>$this->field_checker($_POST)); 
			}
			else
			{
				//UPDATE ITEM CODE
				$itemField['itemCode'] = $this->generate_ItemCode($id,$countryID,isset($POSMTypeID) ? $POSMTypeID : 0);
				$this->c3model->c3crud("update",$table,$itemField,$id);
				
				//IMAGE
				$config['upload_path'] = FCPATH2.'/img/items/';
				$config['allowed_types'] = 'gif|jpg|png';
				$this->upload->initialize($config);
				
				if($this->upload->do_multi_upload("files")){			
					$files = $this->upload->get_multi_upload_data();
					foreach($files as $f)
					{
						extract($f); 
						//GENERATE IMAGE CODE
						$new_file_name=$this->generateImgCode($id,$itemField['itemCode'],$f['file_name']);
						$this->imageResize($file_name,$new_file_name,$f['file_path']);
						$refdbFields['itemID'] = $id; 
						$refdbFields['image']  = $new_file_name; 
						$res = $this->c3model->c3crud("insert",'items_images',$refdbFields,'');
					}
					
					//TAG LAST IMAGE
					$this->db->query("UPDATE  `items_images` SET  `defaultStatus` =  '0' WHERE  itemID = $id");
					$query = $this->db->query("SELECT MAX(items_images.id) as lastImageID FROM items_images WHERE itemID = $id LIMIT 1");
					$row = $query->row();

					$this->db->query("UPDATE  `items_images` SET  `defaultStatus` =  '1' WHERE  `items_images`.`id` = ". $row->lastImageID );
				}
				
				$this->c3model->c3crud("update",$table,$dbFields,$id);
				
				//VENDOR REFERENCE
				//DELETE PREVIUOS VENDORS
				if($data['VENDORS_EDIT']==TRUE){
					$this->c3model->c3crud("no-res",'','','',"DELETE FROM itemVendorsRef WHERE itemID='$id'");
				
					$refFields['itemID'] =  $id;
					
					if(isset($multipleVendors)){
						foreach($multipleVendors as $mV => $value)
						{
							$refFields['vendorID'] = $value;
							$res = $this->c3model->c3crud("insert",'itemVendorsRef',$refFields,'');
						}
					}
				}
			
				//LOGS
				if(!isset($itemName)){
					$sql = "SELECT itemName FROM items WHERE id = $id";
					$sql = $this->db->query($sql);
					$row = $sql->row();
					$itemName = $row->itemName;
				}
				//LOGS	
				$action = "edit";
				if(isset($tag_as_popular)) 		 $action = "popular";
				if(isset($tag_as_unpopular)) 	 $action = "unpopular";
				if($dbFields['irrelevant']=='y') $action = "disapprove";
				
				$CI->rec_logs->w($id,$itemName,'Item Database','Items',$action,$itemField['itemCode']);
				$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Item has been updated.');
				
				//SET AS POPULAR
				if(isset($tag_as_popular))   $referrer_link = $this->strCut($referrer_link)."/tagged_as_popular_item";
				if(isset($tag_as_unpopular)) $referrer_link = $this->strCut($referrer_link)."/tagged_as_not_popular_item";
				if(!isset($tag_as_unpopular) AND !isset($tag_as_unpopular)) $referrer_link = $this->strCut($referrer_link)."/update_success";
				
				if(isset($referrer_link)) redirect($referrer_link, 'location', 301);
				/*
				if(isset($tag_as_popular))
					redirect(HTTP_PATH.'itemDatabase/items/tagged_as_popular_item', 'location', 301);
				if(isset($tag_as_unpopular))
					redirect(HTTP_PATH.'itemDatabase/items/tagged_as_unpopular', 'location', 301);
				if($dbFields['irrelevant']=='y')
					redirect(HTTP_PATH.'itemDatabase/items/tagged_as_disapproved', 'refresh');
				if($txtsearch=='item_db')
					redirect(HTTP_PATH.'itemDatabase/items/update_success', 'location', 301);
				if($txtsearch=='logistics')
					redirect(HTTP_PATH.'itemDatabase/BU_Logistics_Items_review/update_success', 'location', 301);
				if($txtsearch=='disapproved_items')
					redirect(HTTP_PATH.'itemDatabase/disapproved_items/item_has_been_resubmit_for_review', 'location', 301);
				if($txtsearch=='popular_items')
					redirect(HTTP_PATH.'itemDatabase/popular_items/update_success', 'location', 301);
				*/
			}
		}
		
		$sqlSTr =  "SELECT *,
					OUTLET_Status.statusName as OutletStatusName, 
					POSM_Status.statusName as POSMStatusName,
					POSM_Type.typeName as POSM_TypeName,
					items.id as itemID,  count(item_views.itemID) as iViews 
					FROM items 
					LEFT JOIN POSM_Type 		ON items.POSMTypeID = POSM_Type.id 
					LEFT JOIN POSM_Status 		ON items.POSMStatusID = POSM_Status.id 
					LEFT JOIN OUTLET_Status 	ON items.OUTLETStatusID = OUTLET_Status.id
					LEFT JOIN premiumItemType 	ON items.PremiumTypeID = premiumItemType.id 
					LEFT JOIN MATERIAL_Type 	ON items.MaterialTypeID = MATERIAL_Type.id 
					LEFT JOIN country 			ON items.countryID = country.id 
					LEFT JOIN brands  			ON items.brandID = brands.id 
					LEFT JOIN item_views  		ON items.id 	 = item_views.itemID
					$filter 
					GROUP BY items.id ORDER BY $order";

		$sql = $this->db->query($sqlSTr);
		$sql = $sql->result_array();
		$total_rec = count($sql);
		
		$data['total_rec'] = $total_rec;
		$pagenum 		   = $data['active_page'];
		$data['page_rows'] = 15; 
		$data['last'] 	   = ceil($total_rec/$data['page_rows']);		
		$max 			   = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		
		$sql = $this->db->query($sqlSTr ." ". $max );
		$data['data'] = $sql->result_array();
	   
	    if($this->modules->access_checker()==TRUE)
	    {
			$this->load->view('innerPages',$data); 
		}
		else
		{
		$data['vfile']				= 'login.php';
		$data['title']				= 'SMBi System Log-in | SMBi';
		$data['page_title']			= 'SMBi System Log-in';
		$data['meta_description']	= 'San Miguel Brewing International';
		$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Please login.');   
		$this->load->view('login',$data); 	
	   }
	}
	
	function generate_ItemCode($itemID,$item_Country_ID,$POSMTypeID)
	{
		$sql 	   = "SELECT *, id as cID FROM country";
		$sql 	   = $this->db->query($sql);
		$countries = $sql->result_array();
		
		$itemCode='';
		/*COUNTRY CODE*/
		foreach($countries as $country)
		{ extract($country);
		  if($cID == $item_Country_ID)
			$itemCode .= $countryCode;
		}
		
		/*ITEM TYPE*/
		if($POSMTypeID==23){
			$itemCode .= "-SI-";
		}elseif($POSMTypeID==29){
			$itemCode .= "-PI-";
		}else{
			$itemCode .= "-XX-";
		}
		
		/*SERIES*/
		$coding = "%04d";
		if($itemID>9999)
			$coding = "%0".strlen($itemID)."d";
		
		$itemCode .= sprintf($coding, $itemID);
	
		return $itemCode;
	}
	
	function brands($action='',$id='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		
		$this->modules->module_checker(19,'REVIEW');
		
		$table= "brands";
		$data['vfile']				= 'brands.php';
	    $data['title']				= 'Brands | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
	    $data['breadCrumbs']		= '<a href='.$HTTP_PATH.'users> Users </a>';
	    $data['breadCrumbs']	   .= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']	   .= '<a href='.$HTTP_PATH.'itemDatabase/brands> Brands </a>';
	    
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(17);
		
		//CRUD
		$data['ADD'] 	=  $this->modules->crud_checker(19,'ADD');
		$data['EDIT'] 	=  $this->modules->crud_checker(19,'EDIT');
		$data['DELETE'] =  $this->modules->crud_checker(19,'DELETE');
		
		//CSRF
		$data['csrf'] = "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>"; 
		
		//PAGINATION
		//PAGINATION
		//TOTAL NUMBER OF ROWS
		$data['active_page']=1;
		$sql = $this->db->query("SELECT id FROM $table");
		$sql = $sql->result_array();
		$total_rec = count($sql);

		$pagenum = 1;
		$data['page_rows'] = 15; 
		$data['last'] = ceil($total_rec/$data['page_rows']);		
		$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		
		extract($_POST);

		
	    if($action=="insert")
		{
			$this->modules->module_checker(19,'ADD');
			
			//INSERT FIELD
			$dbFields['brandName'] = $brandName;
			$dbFields['dateAdded'] = date('Y-m-d');
			
			//MSG
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Brand name has been save.');
			$res = $this->c3model->c3crud("insert",$table,$dbFields,'');
			
			//LOGS
			$sql		= "select max(id) as max_id FROM $table";
			$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
			$maxID 		= $lastID[0]['max_id'];
			$CI->rec_logs->w($maxID,$brandName,'Admin','Brands','add');
		}
		elseif($action=="deleteOneItem")
		{
			$this->modules->module_checker(19,'DELETE');
			
			$tables = array(
					  array('tbl'=>'items',
							'fld'=>'brandID'),
					  array('tbl'=>'ec_items',
							'fld'=>'brandID'),
					  array('tbl'=>'brandXref',
							'fld'=>'brandID'),
					  array('tbl'=>'commonGalleryBrands',
							'fld'=>'brandID'));
							
			if($this->modules->attr($tables,$id)==0)
			{
				$sql = "SELECT brandName FROM $table WHERE id = $id";
				$sql = $this->db->query($sql);
				$sql = $sql->row();
				$CI->rec_logs->w($id,$sql->brandName,'Admin','Brands','delete');
				
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Brand name has been deleted.');
				$this->c3model->c3crud('delete',$table,'',$id,'');
			}else{
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Brand name cannot be delete, because it is being use in Item Database and eCatalogue Items.');
			}
		}
		elseif($action=="edit")
		{
			$this->modules->module_checker(19,'EDIT');
			$data['id'] = $id;
		}
		elseif($action=="update")
		{
			$this->modules->module_checker(19,'EDIT');
			$dbFields['brandName'] 		= $brandName;
			$dbFields['dateLastEdited'] = date('Y-m-d');
			
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Brand name has been updated.');
			$this->c3model->c3crud("update",$table,$dbFields,$id);
			
			$CI->rec_logs->w($id,$brandName,'Admin','Brands','edit');
		}
		elseif($action=="deleteSelectedItem")
		{
			$this->modules->module_checker(19,'DELETE');
				
			$ctr=0;
			foreach($checkBoxVar as $cbr => $value)
			{
			  $tables = array(
				  array('tbl'=>'items',
						'fld'=>'brandID'),
				  array('tbl'=>'ec_items',
						'fld'=>'brandID'),
				  array('tbl'=>'brandXref',
						'fld'=>'brandID'),
				  array('tbl'=>'commonGalleryBrands',
						'fld'=>'brandID'));
							
				if($this->modules->attr($tables,$value)!=0)
					$ctr++;
			}
			
			if($ctr==0)
			{
				foreach($checkBoxVar as $cbr => $value){
				$sql = "SELECT brandName FROM $table WHERE id = $value";
				$sql = $this->db->query($sql);
				$sql = $sql->row();
				$CI->rec_logs->w($value,$sql->brandName,'Admin','Brands','delete');
				
				$this->c3model->c3crud('delete',$table,'',$value,'');
				}
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Multiple Brands has been deleted.');
			}else{
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Multiple Brands cannot been delete, because it is being use in Item Database and eCatalogue Items.');
			}
		}
		elseif($action=="page")
		{
			$this->modules->module_checker(19,'REVIEW');
			$pagenum = $id;
			$data['active_page'] = $id; 
			$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		}
		
		//STATUS LISTS
		$sql 			= $this->db->query("SELECT * FROM $table ORDER BY brandName ASC $max");
		$data['status'] = $sql->result_array();
		
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
	
	function brandsInCommonGallery($action='',$id='')
	{
		$this->modules->module_checker(19,'REVIEW');
		
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		
		$table= "brands";
		$data['vfile']				= 'brandsInCommonGallery.php';
	    $data['title']				= 'Brands | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
	    $data['breadCrumbs']		= '<a href='.$HTTP_PATH.'users> Users </a>';
	    $data['breadCrumbs']	   .= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']	   .= '<a href='.$HTTP_PATH.'itemDatabase/brandsInCommonGallery> Featured Brands </a>';
	    
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(31);
		
		//CSRF
		$data['csrf'] = "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>"; 
		
		//PAGINATION
		//TOTAL NUMBER OF ROWS
		$data['active_page']=1;
		$sql = $this->db->query("SELECT id FROM $table");
		$sql = $sql->result_array();
		$total_rec = count($sql);

		$pagenum = 1;
		$data['page_rows'] = 100; 
		$data['last'] = ceil($total_rec/$data['page_rows']);		
		$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		
		extract($_POST);

		
		if($action=="update")
		{
			$this->modules->module_checker(19,'EDIT');
			//INSERT IMAGES
			$this->c3model->c3crud("no-res",'','','',"DELETE FROM commonGalleryBrands WHERE id > 0");
			
			if(isset($checkBoxVar))
			{
				foreach($checkBoxVar as $ck => $value)
				{
					$refdbFields['brandID']  = $value; 
					$res = $this->c3model->c3crud("insert",'commonGalleryBrands',$refdbFields,'');
				}
			}
			//LOGS
			$CI->rec_logs->w(1,'Featured Brands','Admin','Featured Brands','edit');
			
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Common Gallery - Featured Brands has has been updated.');
			
		}
		elseif($action=="page")
		{
			$this->modules->module_checker(19,'REVIEW');
			$pagenum = $id;
			$data['active_page'] = $id; 
			$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		}
		
		//STATUS LISTS
		$sql 			= $this->db->query("SELECT * FROM $table ORDER BY brandName ASC $max");
		$data['status'] = $sql->result_array();
		

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
	
	/*ITEMS DELETE IMAGE*/
	function deleteOneImg($id,$itemID)
	{
		//CHECK IF AN ITE IS DEFAULT
		$query = $this->db->query("SELECT defaultStatus FROM items_images WHERE id = '$id' LIMIT 1");
		$row = $query->row();
		
		if($row->defaultStatus==1){
			
			//DELETE CURRENT
			$this->c3model->c3crud("no-res",'','','',"DELETE FROM items_images WHERE id='$id'");
			
			//TAG LAST IMAGE
			$query = $this->db->query("SELECT MAX(items_images.id) as lastImageID FROM items_images WHERE itemID = $itemID LIMIT 1");
			$row = $query->row();
			if($row!=NULL)
				$this->db->query("UPDATE  `items_images` SET  `defaultStatus` =  '1' WHERE  `items_images`.`id` = ". $row->lastImageID );
		}else{
			$this->c3model->c3crud("no-res",'','','',"DELETE FROM items_images WHERE id='$id'");
		}
		
		echo true;
	}
	/*ITEMS DELETE IMAGE*/
	
	/*SET DEFAULT IMAGE*/
	function setDefaultImg($id,$itemID)
	{
		//UNSET ALL ITEM IMAGES TO 0
		$this->db->query("UPDATE items_images SET defaultStatus=0 WHERE itemID = $itemID");
		$this->db->query("UPDATE  `items_images` SET  `defaultStatus` =  '1' WHERE  `items_images`.`id` = ". $id );
		
		echo true;
	}
	/*SET DEFAULT IMAGE*/
	
	private function imageResize($filename,$new_file_name,$path)
	{
	    copy($path.$filename,getcwd() .'/img/big/'. $new_file_name);
		$config['image_library'] = 'gd2';
		$config['source_image']	=getcwd() .'/img/big/'. $new_file_name;
		$config['maintain_ratio'] = TRUE;
		$config['quality'] = '100%';
		$config['width']	 =800;
		$config['height']	 =600;

		$this->image_lib->initialize($config);   
		$this->image_lib->resize();
		
		copy($path.$filename,getcwd() .'/img/small/'. $new_file_name);
	    $config['source_image']	=getcwd() .'/img/small/'. $new_file_name;
		$config['width']	 =400;
		$config['height']	 =300;
		$config['quality'] = '100%';
		$this->image_lib->initialize($config);   
		$this->image_lib->resize();
		
		copy($path.$filename,getcwd() .'/img/galleryImg/'. $new_file_name);
	    $config['source_image']	=getcwd() .'/img/galleryImg/'. $new_file_name;
		$config['create_thumb'] = TRUE;
		$config['height']	 =165;
		$config['quality'] = '100%';
		$config['maintain_ratio'] = TRUE;
		$this->image_lib->initialize($config);   
		$this->image_lib->resize();
		
		
		copy($path.$filename,getcwd() .'/img/thumb/'. $new_file_name);
		$config['source_image']	=getcwd() .'/img/thumb/'. $new_file_name;
		$config['width']	 =45;
		$config['height']	 =45;
		$config['quality'] = '100%';
	    $config['maintain_ratio'] = TRUE;
		$this->image_lib->initialize($config);   
		$this->image_lib->resize();
	}

} ?>