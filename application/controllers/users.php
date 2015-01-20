<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users extends CI_Controller {
   public function __construct()
    {
	 parent::__construct();
	 date_default_timezone_set('UTC');
	 session_start();
	 $this->load->model('c3model');
	 $this->output->enable_profiler(FALSE);
	 $this->load->library('modules');
	 $this->load->helper('url');
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
	
	function target_items($action='',$id='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		
		$this->modules->module_checker(71,'REVIEW');
		
		$table = "target_items";
		$data['vfile']				= 'target_items.php';
	    $data['title']				= 'Target Items | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
	    $data['breadCrumbs']		= '<a href='.$HTTP_PATH.'users> Users </a>';
	    $data['breadCrumbs']	   .= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']	   .= '<a href='.$HTTP_PATH.'users/target_items> Target Items per Month </a>';
	    
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(17);
		
		//CRUD
		$data['ADD'] 	=  $this->modules->crud_checker(71,'ADD');
		$data['EDIT'] 	=  $this->modules->crud_checker(71,'EDIT');
		$data['DELETE'] 	=  $this->modules->crud_checker(71,'DELETE');
		
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

		
	   if($action=="edit")
		{
			$this->modules->module_checker(71,'EDIT');
			$data['id'] = $id;
		}
		elseif($action=="update")
		{
			$this->modules->module_checker(71,'EDIT');
			$dbFields['target'] = $target;
			
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Target items has been updated.');
			$this->c3model->c3crud("update",$table,$dbFields,$id);
			
			$CI->rec_logs->w($id,$countryName.' - '.$target,'Admin','Target Items','edit');
		}
		elseif($action=="insert")
		{
			$this->modules->module_checker(71,'ADD');
			//INSERT FIELD
			$dbFields['countryID'] = $countryID;
			$dbFields['target'] = $target;
			
			//LOGS
			$sql		= "select max(id) as max_id FROM $table";
			$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
			$maxID 		= $lastID[0]['max_id'];
			
			$sql		= "select countryName FROM country WHERE id=$countryID";
			$country 	= $this->c3model->c3crud("select",'','','',$sql);
			$countryName = $country[0]['countryName'];
			
			$CI->rec_logs->w($maxID,$countryName,'Item Database','Target Items','add');
			
			//MSG
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Target items has been save.');
			$res = $this->c3model->c3crud("insert",$table,$dbFields,'');
		}
		elseif($action=="deleteOneItem")
		{
			$this->modules->module_checker(71,'DELETE');

			//LOGS
			$sql	= "select countryName FROM country 
					   left join $table ON $table.countryID = country.id
					   WHERE $table.id=$id";
			$country 	= $this->c3model->c3crud("select",'','','',$sql);
			$countryName = $country[0]['countryName'];
			
			$sql = "DELETE FROM $table WHERE id = $id";
			$sql = $this->db->query($sql);
			
			$CI->rec_logs->w($id,$countryName,'Item Database','Target Items','delete');
			
			$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Target items has been deleted.');
	
		}
		
		
		//STATUS LISTS
		$sql 			= $this->db->query("SELECT *,target_items.id as iID FROM $table, country WHERE country.id = $table.countryID");
		$data['target'] = $sql->result_array();
		
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
	
	function forgot_password_email_receiver($action='',$id='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		
		$this->modules->module_checker(63,'REVIEW');
		
		$table					    = "forgot_password_email_receiver";
		$data['vfile']				= 'forgot_password_email_receiver.php';
	    $data['title']				= 'Brands | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
	    $data['breadCrumbs']		= '<a href='.$HTTP_PATH.'users> Users </a>';
	    $data['breadCrumbs']	   .= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']	   .= '<a href='.$HTTP_PATH.'users/forgot_password_email_receiver> Forgot Password: Email Recipient </a>';
	    
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(17);
		
		//CRUD
		$data['ADD'] 	=  $this->modules->crud_checker(63,'ADD');
		$data['EDIT'] 	=  $this->modules->crud_checker(63,'EDIT');
		$data['DELETE'] =  $this->modules->crud_checker(63,'DELETE');
		
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
			$this->modules->module_checker(63,'ADD');
			
			//INSERT FIELD
			$dbFields['full_name']     = $full_name;
			$dbFields['email_address'] = $email_address;
			$dbFields['main_default'] = $main_default;
			$dbFields['dateAdded'] = date('Y-m-d');
			
			//MSG
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Email recipient has been save.');
			$res = $this->c3model->c3crud("insert",$table,$dbFields,'');
			
			//LOGS
			$sql		= "select max(id) as max_id FROM $table";
			$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
			$maxID 		= $lastID[0]['max_id'];
			$CI->rec_logs->w($maxID,$full_name."-".$email_address,'Admin','Forgot Password: Email Recipient','add');
		}
		elseif($action=="deleteOneItem")
		{
			$this->modules->module_checker(63,'DELETE');
			
			$sql = "SELECT full_name, email_address FROM $table WHERE id = $id";
			$sql = $this->db->query($sql);
			$sql = $sql->row();
			$CI->rec_logs->w($id,$sql->full_name ."-". $sql->email_address ,'Admin','Forgot Password: Email Recipient','delete');
			
			$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Email recipient has been deleted.');
			$this->c3model->c3crud('delete',$table,'',$id,'');
			
		}
		elseif($action=="edit")
		{
			$this->modules->module_checker(63,'EDIT');
			$data['id'] = $id;
		}
		elseif($action=="update")
		{
			$this->modules->module_checker(63,'EDIT');
			$dbFields['full_name']     = $full_name;
			$dbFields['email_address'] = $email_address;
			$dbFields['main_default'] = $main_default;
			$dbFields['dateLastEdited'] = date('Y-m-d');
			
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Email recipient has been updated.');
			$this->c3model->c3crud("update",$table,$dbFields,$id);
			
			$CI->rec_logs->w($id,$full_name."-".$email_address,'Admin','Forgot Password: Email Recipient','edit');
		}
		elseif($action=="page")
		{
			$this->modules->module_checker(63,'REVIEW');
			$pagenum = $id;
			$data['active_page'] = $id; 
			$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		}
		
		//STATUS LISTS
		$sql 			= $this->db->query("SELECT * FROM $table ORDER BY full_name ASC $max");
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
	
	function departments($action='',$id='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		
		$this->modules->module_checker(62,'REVIEW');
		
		$table= "departments";
		$data['vfile']				= 'departments.php';
	    $data['title']				= 'departments | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
	    $data['breadCrumbs']		= '<a href='.$HTTP_PATH.'users> Users </a>';
	    $data['breadCrumbs']	   .= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']	   .= '<a href='.$HTTP_PATH.'users/departments> Departments </a>';
	    
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(17);
		
		//CRUD
		$data['ADD'] 	=  $this->modules->crud_checker(62,'ADD');
		$data['EDIT'] 	=  $this->modules->crud_checker(62,'EDIT');
		$data['DELETE'] =  $this->modules->crud_checker(62,'DELETE');
		
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
		$data['page_rows'] = 25; 
		$data['last'] = ceil($total_rec/$data['page_rows']);		
		$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		
		extract($_POST);

		
	    if($action=="insert")
		{
			$this->modules->module_checker(62,'ADD');
			
			//INSERT FIELD
			$dbFields['department_name'] = $department_name;
			$dbFields['dateAdded'] = date('Y-m-d');
			
			//MSG
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Department has been save.');
			$res = $this->c3model->c3crud("insert",$table,$dbFields,'');
			
			//LOGS
			$sql		= "select max(id) as max_id FROM $table";
			$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
			$maxID 		= $lastID[0]['max_id'];
			$CI->rec_logs->w($maxID,$department_name,'Admin','Departments','add');
		}
		elseif($action=="deleteOneItem")
		{
			$this->modules->module_checker(62,'DELETE');
			
			$tables = array(
					  array('tbl'=>'admin_users',
							'fld'=>'department_id'));
							
			if($this->modules->attr($tables,$id)==0)
			{
				$sql = "SELECT department_name FROM $table WHERE id = $id";
				$sql = $this->db->query($sql);
				$sql = $sql->row();
				$CI->rec_logs->w($id,$sql->department_name,'Admin','Departments','delete');
				
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Department has been deleted.');
				$this->c3model->c3crud('delete',$table,'',$id,'');
			}else{
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Department cannot be delete, because it is being use in User Accounts.');
			}
		}
		elseif($action=="edit")
		{
			$this->modules->module_checker(62,'EDIT');
			$data['id'] = $id;
		}
		elseif($action=="update")
		{
			$this->modules->module_checker(62,'EDIT');
			$dbFields['department_name'] 		= $department_name;
			$dbFields['dateLastEdited'] = date('Y-m-d');
			
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Department has been updated.');
			$this->c3model->c3crud("update",$table,$dbFields,$id);
			
			$CI->rec_logs->w($id,$department_name,'Admin','Departments','edit');
		}
		elseif($action=="deleteSelectedItem")
		{
			$this->modules->module_checker(62,'DELETE');
				
			$ctr=0;
			foreach($checkBoxVar as $cbr => $value)
			{
			  $tables = array(
				  array('tbl'=>'admin_users',
						'fld'=>'department_id'));
							
				if($this->modules->attr($tables,$value)!=0)
					$ctr++;
			}
			
			if($ctr==0)
			{
				foreach($checkBoxVar as $cbr => $value){
				$sql = "SELECT department_name FROM $table WHERE id = $value";
				$sql = $this->db->query($sql);
				$sql = $sql->row();
				$CI->rec_logs->w($value,$sql->department_name,'Admin','Departments','delete');
				
				$this->c3model->c3crud('delete',$table,'',$value,'');
				}
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Multiple departments has been deleted.');
			}else{
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Multiple departments cannot been delete, because it is being use in Item Database and eCatalogue Items.');
			}
		}
		elseif($action=="page")
		{
			$this->modules->module_checker(62,'REVIEW');
			$pagenum = $id;
			$data['active_page'] = $id; 
			$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		}
		
		//STATUS LISTS
		$sql 				 = $this->db->query("SELECT * FROM $table ORDER BY department_name ASC $max");
		$data['departments'] = $sql->result_array();
		
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
	
	function voters_department($action='',$id='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		
		$this->modules->module_checker(62,'REVIEW');
		
		$table= "voters_department";
		$data['vfile']				= 'voters_department.php';
	    $data['title']				= 'departments | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
	    $data['breadCrumbs']		= '<a href='.$HTTP_PATH.'users> Users </a>';
	    $data['breadCrumbs']	   .= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']	   .= '<a href='.$HTTP_PATH.'users/voters_department> Voters Department </a>';
	    
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(17);
		
		//CRUD
		$data['ADD'] 	=  $this->modules->crud_checker(62,'ADD');
		$data['EDIT'] 	=  $this->modules->crud_checker(62,'EDIT');
		$data['DELETE'] =  $this->modules->crud_checker(62,'DELETE');
		
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
		$data['page_rows'] = 25; 
		$data['last'] = ceil($total_rec/$data['page_rows']);		
		$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		
		extract($_POST);

		
	    if($action=="insert")
		{
			$this->modules->module_checker(62,'ADD');
			
			//INSERT FIELD
			$dbFields['department_name'] = $department_name;
			$dbFields['dateAdded'] = date('Y-m-d');
			
			//MSG
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Department has been save.');
			$res = $this->c3model->c3crud("insert",$table,$dbFields,'');
			
			//LOGS
			$sql		= "select max(id) as max_id FROM $table";
			$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
			$maxID 		= $lastID[0]['max_id'];
			$CI->rec_logs->w($maxID,$department_name,'Admin','Departments','add');
		}
		elseif($action=="deleteOneItem")
		{
			$this->modules->module_checker(62,'DELETE');
			
			$sql = "SELECT department_name FROM $table WHERE id = $id";
			$sql = $this->db->query($sql);
			$sql = $sql->row();
			$CI->rec_logs->w($id,$sql->department_name,'Admin','Departments','delete');
			
			$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Department has been deleted.');
			$this->c3model->c3crud('delete',$table,'',$id,'');
			
		}
		elseif($action=="edit")
		{
			$this->modules->module_checker(62,'EDIT');
			$data['id'] = $id;
		}
		elseif($action=="update")
		{
			$this->modules->module_checker(62,'EDIT');
			$dbFields['department_name'] 		= $department_name;
			$dbFields['dateLastEdited'] = date('Y-m-d');
			
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Department has been updated.');
			$this->c3model->c3crud("update",$table,$dbFields,$id);
			
			$CI->rec_logs->w($id,$department_name,'Admin','Departments','edit');
		}
		elseif($action=="deleteSelectedItem")
		{
			$this->modules->module_checker(62,'DELETE');
				
			$ctr=0;

			foreach($checkBoxVar as $cbr => $value){
			$sql = "SELECT department_name FROM $table WHERE id = $value";
			$sql = $this->db->query($sql);
			$sql = $sql->row();
			$CI->rec_logs->w($value,$sql->department_name,'Admin','Departments','delete');
			
			$this->c3model->c3crud('delete',$table,'',$value,'');
			}
			$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Multiple departments has been deleted.');
		}
		elseif($action=="page")
		{
			$this->modules->module_checker(62,'REVIEW');
			$pagenum = $id;
			$data['active_page'] = $id; 
			$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		}
		
		//STATUS LISTS
		$sql 				 = $this->db->query("SELECT * FROM $table ORDER BY department_name ASC $max");
		$data['departments'] = $sql->result_array();
		
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
	
	function profile($action='',$id='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		
		$this->modules->module_checker(2,'REVIEW');
		
		$table= "user_profile";
		$data['vfile']				= 'userProfile.php';
	    $data['title']				= 'Profile | San Miguel Brewing International';
	    
		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
	    $data['breadCrumbs']			 = '<a href='.$HTTP_PATH.'users> Users </a> ';
		$data['breadCrumbs']	   		.= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']			.= '<a href='.$HTTP_PATH.'users/profile>  Profile </a>';
	    
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(23);
		
		//CRUD
		$data['ADD'] 	=  $this->modules->crud_checker(2,'ADD');
		$data['EDIT'] 	=  $this->modules->crud_checker(2,'EDIT');
		$data['DELETE'] =  $this->modules->crud_checker(2,'DELETE');
		
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
			$this->modules->module_checker(2,'ADD');
			if($_POST==NULL){
				redirect(HTTP_PATH.'users/profile', 'location', 301);
				die();
			}
			
			//INSERT FIELD
			$dbFields['profile_name'] 	= $profile_name;
			$dbFields['dateAdded'] 		= date('Y-m-d');
			$res = $this->c3model->c3crud("insert",$table,$dbFields,'');
			//MSG
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'User profile has been save.');
			
			
			$sql		= "select max(id) as max_id FROM user_profile";
			$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
			$user_profileID 	= $lastID[0]['max_id'];
			
			
			foreach($chkmod as $cMod => $value)
			{
				$refdbFields['user_profileID'] =   $user_profileID;
				$refdbFields['system_modID']   = 	$value;  
				$res = $this->c3model->c3crud("insert",'user_profileRef',$refdbFields,'');
			}
			
			//LOGS
			$sql		= "select max(id) as max_id FROM $table";
			$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
			$maxID 		= $lastID[0]['max_id'];
			$CI->rec_logs->w($maxID,$profile_name,'Admin','Profiles','add');
		}
		elseif($action=="deleteOneItem")
		{
			$this->modules->module_checker(2,'DELETE');
			
			$tables = array(
				array('tbl'=>'user_profileRef',
					'fld'=>'user_profileID'));
							
			if($this->modules->attr($tables,$id)==0)
			{
				$sql = "SELECT profile_name FROM user_profile WHERE id = $id";
				$sql = $this->db->query($sql);
				$sql = $sql->row();
				$CI->rec_logs->w($id,$sql->profile_name,'Admin','Profiles','delete');
				
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'User Profile has been deleted.');
				$this->c3model->c3crud('delete',$table,'',$id,'');
				$this->c3model->c3crud("no-res",'','','',"DELETE FROM user_profileRef WHERE user_profileID='$id'");
			}else{
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'User Profile cannot be delete, because it is being use by the system.');
			}	
			
			
		}
		elseif($action=="edit")
		{
			$this->modules->module_checker(2,'EDIT');
			
			$data['vfile']		= 'userProfileFORM.php';
			$data['id'] = $id;
		}
		elseif($action=="add")
		{
			$this->modules->module_checker(2,'ADD');
			$data['vfile']			= 'userProfileFORM.php';
		}
		elseif($action=="update")
		{
			$this->modules->module_checker(2,'EDIT');
			if($_POST==NULL){
				redirect(HTTP_PATH.'users/profile', 'location', 301);
				die();
			}
			
			$dbFields['profile_name'] 	= $profile_name;
			$dbFields['dateLastEdited'] = date('Y-m-d');
			$this->c3model->c3crud("update",$table,$dbFields,$id);
			
			$this->c3model->c3crud("no-res",'','','',"DELETE FROM user_profileRef WHERE user_profileID ='$id'");
			
			if(isset($chkmod)){
				foreach($chkmod as $cMod => $value)
				{
					$refdbFields['user_profileID'] =   $id;
					$refdbFields['system_modID']   = 	$value;  
					$res = $this->c3model->c3crud("insert",'user_profileRef',$refdbFields,'');
				}
			}
			
			//LOGS
			$CI->rec_logs->w($id,$profile_name,'Admin','Profiles','edit');
			
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Profile has been updated.');
		}
		elseif($action=="page")
		{
			$this->modules->module_checker(2,'REVIEW');
			$pagenum = $id;
			$data['active_page'] = $id; 
			$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		}
		
		//STATUS LISTS
		$sql 			= $this->db->query("SELECT * FROM $table ORDER BY profile_name ASC $max");
		$data['uprofile'] = $sql->result_array();
		
		
	    $this->load->view('innerPages',$data); 
	}
	
	function history($auID)
	{
		$sql = $this->db->query("SELECT uname, full_name, email_address, country.countryName as cName, departments.department_name as dName, admin_usersRef.dateAdded as tdate  FROM admin_usersRef
				LEFT JOIN departments ON admin_usersRef.department_id = departments.id 
				LEFT JOIN country ON admin_usersRef.countryID = country.id 
				WHERE admin_usersRef.admin_userID = $auID");
		$rows = $sql->result_array();
		
		echo "<table cellpadding='0' cellspacing='0' border=1 style='width:100%;margin: 0px auto;font-size:12px;' class='iLike_Result_Table'>
			<tr style='border-radius: 6px;'>
				<td style='width:10px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>No 		   	   </b></td> 
				<td style='width:100px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>   <b>User Name  	   </b></td> 
				<td style='width:100px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>   <b>Full Name  	   </b></td> 
				<td style='width:30px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Email Address     </b></td> 
				<td style='width:150px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>   <b>Country  	   	   </b></td> 
				<td style='width:150px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Department  	   </b></td> 
				<td style='width:100px;text-align:center;background:rgb(175, 168, 168);color:white;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Date Added 	   </b></td> 
			</tr>";
		 
			$x = 0;
			$total = 0;
			foreach($rows as $v) { 
			extract($v);
			$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
			echo	"<tr>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>	$x 				</td>
					  <td $c  style='text-align:left;padding: 0px 2px 0px 10px;'>	$uname 			</td>
					  <td $c  style='text-align:left;padding: 0px 2px 0px 10px;'>	$full_name  		</td>
					  <td $c  style='text-align:left;padding: 0px 2px 0px 10px;'>	$email_address  		</td>
					  <td $c  style='text-align:left;padding: 0px 2px 0px 10px;'>		$cName  		</td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 10px;'>		$dName  	</td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>		$tdate  </td>
					</tr>";
			}
		echo"</table>";
	}
	
	function check_username($uname='')
	{
		$sql = $this->db->query("SELECT uname FROM admin_users WHERE uname='$uname'");
		$rows = $sql->result_array();
		
		if($rows)
			echo "exist";
		else
			echo "good";
	}
	
    function admin_users($action='',$id='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		
		$this->modules->module_checker(4,'REVIEW');
		$filter_WHERE = $this->modules->country();
		$filter_AND = $this->modules->country2();
		
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(14);
		
		$table= "admin_users";
		$data['vfile']				= 'admin_users.php';
	    $data['title']				= 'Admin Users | SMBi';
	    
		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
	    $data['breadCrumbs']			 = '<a href='.$HTTP_PATH.'users> Users </a>';
	    $data['breadCrumbs']	   		.= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']			.= '<a href='.$HTTP_PATH.'users/admin_users>  Admin Users </a>';
	    
		
		//CRUD
		$data['ADD'] 	=  $this->modules->crud_checker(4,'ADD');
		$data['EDIT'] 	=  $this->modules->crud_checker(4,'EDIT');
		$data['DELETE'] =  $this->modules->crud_checker(4,'DELETE');
		
		
		//CSRF
		$data['csrf'] = "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>"; 
		
		//PAGINATION
		//PAGINATION
		//TOTAL NUMBER OF ROWS
		$data['active_page']=1;
		$sql = $this->db->query("SELECT id FROM $table $filter_WHERE");
		$sql = $sql->result_array();
		$total_rec = count($sql);

		$pagenum = 1;
		$data['page_rows'] = 1000; 
		$data['last'] = ceil($total_rec/$data['page_rows']);		
		$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		
		$oldpassword='';
		extract($_POST);
	    
		if($action=='update_uname_email_success'){
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'All items has been transfered to new user.'); 
		}elseif($action=='delete_success'){
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'User has been deleted.');
		}elseif($action=='update_success'){
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'User has been updated.');
		}
		elseif($action=="insert")
		{
			$this->modules->module_checker(4,'ADD');
			
			if($_POST==NULL){
				redirect(HTTP_PATH.'users/admin_users/add', 'location', 301);
				die();
			}
			
			//INSERT FIELD
			$dbFields['countryID'] 		= $countryID;
			$dbFields['uname'] 	   		= str_replace('"',"",addslashes(trim($uname)));
			$dbFields['password']  		= md5(trim($password));
			$dbFields['full_name'] 		= isset($full_name) ?  str_replace('"',"",addslashes(trim($full_name))) : NULL;
			$dbFields['email_address'] 	= isset($email_address) ? $email_address : NULL;
			$dbFields['countViews'] 	= isset($countViews) ? $countViews : NULL;
			$dbFields['fields001'] 		= isset($Fields001) ? $Fields001 : NULL;
			$dbFields['fields002'] 		= isset($Fields002) ? $Fields002 : NULL;
			$dbFields['fields003'] 		= isset($Fields003) ? $Fields003 : NULL;
			$dbFields['fields004'] 		= isset($Fields004) ? $Fields004 : NULL;
			$dbFields['fields005'] 		= isset($Fields005) ? $Fields005 : NULL;
			$dbFields['fields006'] 		= isset($Fields006) ? $Fields006 : NULL;
			$dbFields['super_admin'] 	= isset($super_admin) ?  $super_admin : NULL;
			$dbFields['department_id'] 	= isset($department_id) ?  $department_id : NULL;
			$dbFields['new_id'] 		= 1;
			$dbFields['dateAdded'] 		= date('Y-m-d');
			isset($active)				? $dbFields['active'] = $active : NULL;
			
			//MSG
			$sqloldpass = "select uname from admin_users where uname='".addslashes(trim($uname))."'";
			$q = $this->db->query($sqloldpass);
			$row = $q->row();
			
			//print_r($_POST);
			if($q->num_rows > 0 OR $password=='' OR $confirm_password=='')
			{
				$data['POST'] = $_POST;
				$data['vfile'] = 'adminUserFORM.php';
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'User Name Already Exist or you didn\'t supply password or the confirm password field.');
			}
			else
			{
				$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'User has been save.');
				$res = $this->c3model->c3crud("insert",$table,$dbFields,'');	
				
				$sql		= "select max(id) as max_id FROM admin_users";
				$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
				$admin_userID = $lastID[0]['max_id'];
				
				if(isset($roles)){
					foreach($roles as $r => $value)
					{
						$refdbFields['admin_userID'] =   $admin_userID;
						$refdbFields['roleID']   	 =   $value;  
						$res = $this->c3model->c3crud("insert",'admin_usersRoles',$refdbFields,'');
					}
				}
			}
			
			//LOGS
			$sql		= "select max(id) as max_id FROM $table";
			$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
			$maxID 		= $lastID[0]['max_id'];
			$CI->rec_logs->w($maxID, addslashes(trim($uname)),'Admin','Users','add');
			
		}
		elseif($action=="deleteOneItem")
		{
			$this->modules->module_checker(4,'DELETE');
			
			$tables = array(
			  array('tbl'=>'ec_items',
					'fld'=>'user_id'),
			  array('tbl'=>'items',
					'fld'=>'user_id'),
			  array('tbl'=>'campaign',
					'fld'=>'adminCreatorID'),
			  array('tbl'=>'campaign',
					'fld'=>'adminLAstEditorID'));
				
			if($this->modules->attr($tables,$id)==0)
			{
				$sql = "SELECT uname FROM admin_users WHERE id = $id";
				$sql = $this->db->query($sql);
				$sql = $sql->row();
				$CI->rec_logs->w($id,$sql->uname,'Admin','Users','delete');
				
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'User has been deleted.');
				$this->c3model->c3crud('delete',$table,'',$id,'');
				$this->c3model->c3crud("no-res",'','','',"DELETE FROM admin_usersRoles WHERE admin_userID='$id'");
				//$this->c3model->c3crud("no-res",'','','',"DELETE FROM logs WHERE rec_id='$id' AND table_name = 'Users' AND module_name = 'System Logs'");
				
				//ITEM VIEWS
				$this->c3model->c3crud("no-res",'','','',"DELETE FROM item_views   WHERE user_id='$id'");
				$this->c3model->c3crud("no-res",'','','',"DELETE FROM ecitem_views WHERE user_id='$id'");
				$this->c3model->c3crud("no-res",'','','',"DELETE FROM admin_usersRef WHERE admin_userID='$id'");
				redirect(HTTP_PATH.'users/admin_users/delete_success', 'location', 301);
			}else{
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'User cannot be delete, because it is being use by the system.');
			}			
		}
		elseif($action=="add")
		{
			$this->modules->module_checker(4,'ADD');
			$data['vfile']	= 'adminUserFORM.php';
			$data['hide_checkbox'] = TRUE;
		}
		elseif($action=="edit")
		{
			$this->modules->module_checker(4,'EDIT');
			$data['id'] = $id;
			$data['vfile']	= 'adminUserFORM.php';
			
			//SELECT COUNTRY ID 
			$sql = $this->db->query("SELECT countryID FROM admin_users WHERE id = $id LIMIT 0,1");
			$row = $sql->row();
			$countryID = $row->countryID;
			
			$sql = $this->db->query("SELECT admin_users.id as aID, countryName, full_name, department_name  FROM admin_users 
									 LEFT JOIN departments ON admin_users.department_id = departments.id 
									 LEFT JOIN country    ON  admin_users.countryID     = country.id 
									 WHERE admin_users.countryID = $countryID AND admin_users.id != $id
									 ORDER BY country.id DESC, department_name ASC, full_name ASC");
			$data['list_of_users'] = $sql->result_array();
		}
		elseif($action=="update")
		{
			$this->modules->module_checker(4,'EDIT');
			if($_POST==NULL){
				redirect(HTTP_PATH.'users/admin_users', 'location', 301);
				die();
			}
			
			$dbFields['countryID'] 		= $countryID;
			$dbFields['uname'] 	   		= str_replace('"',"",addslashes(trim($uname)));
			$dbFields['full_name'] 		= isset($full_name) ? str_replace('"',"",addslashes(trim($full_name))) : NULL;
			$dbFields['email_address'] 	= isset($email_address) ? $email_address : NULL;
			$dbFields['countViews'] 	= isset($countViews) ? $countViews : NULL;
			$dbFields['fields001'] 		= isset($Fields001) ? $Fields001 : NULL;
			$dbFields['fields002'] 		= isset($Fields002) ? $Fields002 : NULL;
			$dbFields['fields003'] 		= isset($Fields003) ? $Fields003 : NULL;
			$dbFields['fields004'] 		= isset($Fields004) ? $Fields004 : NULL;
			$dbFields['fields005'] 		= isset($Fields005) ? $Fields005 : NULL;
			$dbFields['fields006'] 		= isset($Fields006) ? $Fields006 : NULL;
			$dbFields['super_admin'] 	= isset($super_admin) ?  $super_admin : NULL;
			$dbFields['department_id'] 	= isset($department_id) ?  $department_id : NULL;
			$dbFields['dateLastEdited'] 		= date('Y-m-d');
			isset($active)				? $dbFields['active'] = $active : NULL;
			
			//print_r($_POST);
			if($_SESSION['super_admin']=='y' AND isset($passwordReset) AND isset($password) AND $confirm_password!='')
			{
				//DELETE FROM ADMIN_USERS
				$this->c3model->c3crud("no-res",'','','',"DELETE FROM admin_usersRoles WHERE admin_userID='$id'");
				$admin_userID = $id;
					
				if(isset($roles)){
				 foreach($roles as $r => $value)
				 {
					$refdbFields['admin_userID'] =   $id;
					$refdbFields['roleID']   	 =   $value;  
					$res = $this->c3model->c3crud("insert",'admin_usersRoles',$refdbFields,'');
				 }
				}
		
				$dbFields['password']  = md5(trim($password));
				$dbFields['new_id']    = 1;
				$this->c3model->c3crud("update",$table,$dbFields,$id);
				$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Super Admin Successfully update the profile including password.');  
				
			}
			else
			{
				$sqloldpass = "select password from $table where id='$id'";
				$q = $this->db->query($sqloldpass);
				$row = $q->row();
				if($row->password == md5($oldpassword))
				{
				  $dbFields['password']  = md5(trim($password));
				  $this->c3model->c3crud("update",$table,$dbFields,$id);
				  $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Profile has been upated including password.');
				  
				  //DELETE FROM ADMIN_USERS
				  $this->c3model->c3crud("no-res",'','','',"DELETE FROM admin_usersRoles WHERE admin_userID='$id'");
				  $admin_userID = $id;
					
				  if(isset($roles)){
					 foreach($roles as $r => $value)
					 {
						$refdbFields['admin_userID'] =   $admin_userID;
						$refdbFields['roleID']   	 =   $value;  
						$res = $this->c3model->c3crud("insert",'admin_usersRoles',$refdbFields,'');
					 }
					}
				}
				else
				{
				   $this->c3model->c3crud("update",$table,$dbFields,$id);
				   $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Profile has been updated, but not the password.');
				   
					//DELETE FROM ADMIN_USERS
					$this->c3model->c3crud("no-res",'','','',"DELETE FROM admin_usersRoles WHERE admin_userID='$id'");
					$admin_userID = $id;
					
					if(isset($roles)){
					 foreach($roles as $r => $value)
					 {
						$refdbFields['admin_userID'] =   $admin_userID;
						$refdbFields['roleID']   	 =   $value;  
						$res = $this->c3model->c3crud("insert",'admin_usersRoles',$refdbFields,'');
					 }
					}
				}
			
			}
			
			$CI->rec_logs->w($id,addslashes(trim($uname)),'Admin','Users','edit');
			
			redirect(HTTP_PATH.'users/admin_users/update_success', 'location', 301);
		}
		elseif($action=="page")
		{
			$this->modules->module_checker(4,'REVIEW');
			$pagenum = $id;
			$data['active_page'] = $id; 
			$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		}
		elseif($action=="duplicate")
		{
			$this->modules->module_checker(4,'EDIT');
			$data['id']     	   = $id;
			$data['duplicate']     = TRUE;
			$data['vfile']	= 'adminUserFORM.php';
		}
		elseif($action=="update_uname_email")
		{
			$this->modules->module_checker(4,'EDIT');
			if($_POST==NULL){
				redirect(HTTP_PATH.'users/admin_users', 'location', 301);
				die();
			}
			//NEW INFO
			$dbFields['uname'] 				= isset($uname) 			? addslashes(trim($uname)) : NULL;
			$dbFields['password'] 			= isset($password) 			? md5(trim($password))     : NULL;
			$dbFields['full_name'] 			= isset($full_name) 		? $full_name     : NULL;
			$dbFields['email_address'] 		= isset($email_address) 	? $email_address : NULL;
			$dbFields['new_id'] 			= 1;
			$dbFields['dateLastEdited'] 	= date('Y-m-d');
			$this->c3model->c3crud("update",$table,$dbFields,$id);
			
			//OLD INFO
			$dbFields2['admin_userID'] 		= $id;
			$dbFields2['uname'] 			= isset($old_uname) 		? $old_uname  : NULL;
			$dbFields2['password'] 			= isset($old_password) 		? $old_password  : NULL;
			$dbFields2['full_name'] 		= isset($oldfull_name) 		? $oldfull_name  : NULL;
			$dbFields2['email_address'] 	= isset($oldemail_address)  ? $oldemail_address : NULL;
			$dbFields2['countryID'] 	    = isset($countryID)  		? $countryID : NULL;
			$dbFields2['department_id'] 	= isset($department_id)  	? $department_id : NULL;
			$dbFields2['dateAdded'] 		= date('Y-m-d');
			$res = $this->c3model->c3crud("insert","admin_usersRef",$dbFields2,'');
			
			$CI->rec_logs->w($id,$full_name,'Admin','Users','edit');
			redirect(HTTP_PATH.'users/admin_users/update_uname_email_success', 'location', 301);
		}
		elseif($action=="replace_user")
		{
			$this->modules->module_checker(4,'EDIT');
			if($_POST==NULL){
				redirect(HTTP_PATH.'users/admin_users', 'location', 301);
				die();
			}
			
			//OLD INFO
			$dbFields2['admin_userID'] 		= $id;
			//SELECT NEW INFORMATION
			$sql = $this->db->query("SELECT uname, password, countryID, department_id, email_address, full_name FROM admin_users WHERE admin_users.id = $aID");
			$row = $sql->row();
		
			$dbFields2['new_admin_userID'] 	= $aID;
			$dbFields2['uname'] 			= $row->uname;
			$dbFields2['password'] 			= $row->password;
			$dbFields2['full_name'] 		= $row->full_name;
			$dbFields2['email_address'] 	= $row->email_address;
			$dbFields2['countryID'] 	    = $row->countryID;
			$dbFields2['department_id'] 	= $row->department_id;
			$dbFields2['dateAdded'] 		= date('Y-m-d');
			$res = $this->c3model->c3crud("insert","admin_usersRef",$dbFields2,'');
			
			//UPDATE THE ITEMS UPLOADER			
			$this->itemsTurnOver($aID,$id);
			
			$CI->rec_logs->w($id,$old_uname,'Admin','Users','replace as uploader');
			redirect(HTTP_PATH.'users/admin_users/update_uname_email_success', 'location', 301);
		}
		
		//STATUS LISTS
		$sql = $this->db->query("SELECT *, admin_users.id as adminID FROM $table 
								LEFT JOIN country     ON country.id     = admin_users.countryID  
								LEFT JOIN departments ON departments.id = admin_users.department_id 
								$filter_WHERE ORDER BY countryName ASC, department_name ASC, full_name ASC $max");
		$data['admin_users'] = $sql->result_array();

	
	    $this->load->view('innerPages',$data); 
	}
	
	function itemsTurnOver($new_ID,$old_ID)
	{
	 $CI =& get_instance();
	 $CI->load->library('rec_logs');
	 //ITEM DATABASE ITEMS
	 $sql = $this->db->query("SELECT items.id as itemID, itemCode, itemName FROM items WHERE user_id = $old_ID");	 
	 $sql = $sql->result_array();
	 foreach($sql as $itm)
	 { extract($itm);
	   //EXIST IN TURN OVER REF
	   $fresh 		  = $this->db->query("SELECT itemID FROM itemsTurnOverRef WHERE itemID = $itemID AND transfer_type='original' AND item_type='item_db' LIMIT 0,1");
	   $fresh 		  = $fresh->row();
	   
	   $transfer_type = "turn over";
	   $userID		  = $new_ID;
	   if(!isset($fresh->itemID)){
		$transfer_type = "original";
	    $userID		   = $old_ID;
	   }
		//INSERT TO TURN OVER REF & UPDATE UPLOADER
		$dbFields['itemID'] 		= $itemID;
		$dbFields['userID'] 		= $userID;
		$dbFields['item_type'] 		= "item_db";
		$dbFields['transfer_type'] 	= $transfer_type;
		$dbFields['date'] 			= date('Y-m-d');
		$dbFields['date_time'] 		= date('Y-m-d')." ".date('H:m:i');
		$this->c3model->c3crud("insert","itemsTurnOverRef",$dbFields,'');
		//UPDATE UPLOADER
		$this->db->query("UPDATE items SET user_id='$new_ID' WHERE id=$itemID");
	    //LOGS
		if($transfer_type=="original"){
		$dbFields['transfer_type'] 	= "turn over";
		$dbFields['userID'] 		= $new_ID;
		$this->c3model->c3crud("insert","itemsTurnOverRef",$dbFields,'');
		}
		$CI->rec_logs->w($itemID,$itemName,'Item Database','Items','transfer',$itemCode);
	 }
	 
	 //eCATALOGUE ITEMS
	 $sql = $this->db->query("SELECT ec_items.id as itemID, itemCode, itemName FROM ec_items WHERE user_id = $old_ID");	 
	 $sql = $sql->result_array();
	 foreach($sql as $itm)
	 { extract($itm);
	   //EXIST IN TURN OVER REF
	   $fresh 		  = $this->db->query("SELECT itemID FROM itemsTurnOverRef WHERE itemID = $itemID AND transfer_type='original' AND item_type='ec_item' LIMIT 0,1");
	   $fresh 		  = $fresh->row();
	   
	   $transfer_type = "turn over";
	   $userID		  = $new_ID;
	   if(!isset($fresh->itemID)){
		$transfer_type = "original";
	    $userID		   = $old_ID;
	   }
		//INSERT TO TURN OVER REF & UPDATE UPLOADER
		$dbFields['itemID'] 		= $itemID;
		$dbFields['userID'] 		= $userID;
		$dbFields['item_type'] 		= "ec_item";
		$dbFields['transfer_type'] 	= $transfer_type;
		$dbFields['date'] 			= date('Y-m-d');
		$dbFields['date_time'] 		= date('Y-m-d')." ".date('H:m:i');
		$this->c3model->c3crud("insert","itemsTurnOverRef",$dbFields,'');
		//UPDATE UPLOADER
		$this->db->query("UPDATE ec_items SET user_id='$new_ID' WHERE id=$itemID");
	    //LOGS
		if($transfer_type=="original"){
		$dbFields['transfer_type'] 	= "turn over";
		$dbFields['userID'] 		= $new_ID;
		$this->c3model->c3crud("insert","itemsTurnOverRef",$dbFields,'');
		}
		$CI->rec_logs->w($itemID,$itemName,'eCatalogue','eCatalogue Items','transfer',$itemCode);
	 }
	
	}
	
	function personal_account($action='',$id='')
	{
		$data['breadCrumbs'] = '';
		$table = 'admin_users';
		extract($_POST);
		
		if($action=="view")
		{
			//$this->modules->module_checker(40,'REVIEW');
			$data['id'] = $id;
			$data['vfile']	= 'personal_accountFORM.php';
		}
		elseif($action=="update")
		{
			//$this->modules->module_checker(40,'EDIT');
			/*
			$dbFields['uname'] 	   		= isset($uname) ? addslashes(trim($uname)) ? ;
			$dbFields['full_name'] 		= isset($fullname)      ? $fullname : NULL;
			$dbFields['email_address'] 	= isset($email_address) ? $email_address : NULL;
			$dbFields['fields001'] 		= isset($Fields001) ? $Fields001 : NULL;
			$dbFields['fields002'] 		= isset($Fields002) ? $Fields002 : NULL;
			$dbFields['fields003'] 		= isset($Fields003) ? $Fields003 : NULL;
			$dbFields['fields004'] 		= isset($Fields004) ? $Fields004 : NULL;
			$dbFields['fields005'] 		= isset($Fields005) ? $Fields005 : NULL;
			$dbFields['fields006'] 		= isset($Fields006) ? $Fields006 : NULL;
			*/
			
			$sqloldpass = "select password from $table where id='$id'";
			$q = $this->db->query($sqloldpass);
			$row = $q->row();
		
			
			if($row->password == md5($oldpassword))
			{
			  $dbFields['new_id'] 	 = 0;
			  $dbFields['password']  = md5(trim($password));
			  $this->c3model->c3crud("update",$table,$dbFields,$id);
			  $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Congratulations, Password has been upated.');
			}
			else
			{
			   //$this->c3model->c3crud("update",$table,$dbFields,$id);
			   $data['msg'] 	= array('msg_type'=>'alert-warning','msg_desc'=>'Failed to update password: Your old password is incorrect.');
			   $data['id'] 	    = $id;
			   $data['vfile']	= 'personal_accountFORM.php';
			   $action = "view";
			}
			
		}
		
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
		
		//$data['vfile'] = 'home.php';
		
		if($action == 'view')
			$this->load->view('innerPages',$data);
		else
			$this->load->view('index',$data);
	}
	
	function form_validation($action='',$id='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		
		$this->modules->module_checker(31,'REVIEW');
		
		$table= "table_fields";
		$data['vfile']				= 'form_validation.php';
	    $data['title']				= 'Form Validation | SMBi';
	    
		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
	    $data['breadCrumbs']			 = '<a href='.$HTTP_PATH.'users> Users </a>';
	    $data['breadCrumbs']	   		.= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']			.= '<a href='.$HTTP_PATH.'users/form_validation>  Form Validation </a>';
	    
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(25);
		
		//CRUD
		$data['EDIT'] 	=  $this->modules->crud_checker(31,'EDIT');
	
		
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
	    if($action=="")
		{
			$this->modules->module_checker(31,'REVIEW');
			$data['id'] = $id;
			$data['vfile']	= 'form_validation.php';
		}
		elseif($action=="update")
		{
			$this->modules->module_checker(31,'EDIT');
			
			//VOTERS
			$form_validations = array();
			$i=0;
			
			foreach($ids as $key => $value)
			{
				$form_validations[$i]['id']=$value;
				$i++;
			}

			$j=0;
			foreach($labels as $key => $value)
			{
				$form_validations[$j]['label']=$value;
				$j++;
			}
			
			$k=0;
			foreach($show as $key => $value)
			{
				$form_validations[$k]['show_hide']=$value;
				$k++;
			}
			
			$l=0;
			foreach($validations as $key => $value)
			{
				$form_validations[$l]['validation_rule']=$value;
				$l++;
			}
		
			
			foreach($form_validations as $fv)
			{
				extract($fv);
				$fvID 					      =   $id;
				$dbFields['label']  	   	  =   $label;  
				$dbFields['show_hide']  	  =   $show_hide;  
				$dbFields['validation_rule']  =   $validation_rule;  
				$dbFields['dateLastEdited']   =   date('Y-m-d');  
				$this->c3model->c3crud("update","table_fields",$dbFields,$fvID);
			}
			
			$CI->rec_logs->w($id,'Form Validation','Admin','Form Validation','edit');
			
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Form Validations has been updated.');
			
		}

		
		//STATUS LISTS
		$sql 				  = $this->db->query("SELECT * FROM table_fields WHERE id NOT IN (43,44,45,46,47,48) ORDER BY field_name ASC");
		$data['table_fields'] = $sql->result_array();
		
	
	    $this->load->view('innerPages',$data); 
	}
	
	/*ROLES 2*/
	function roles($action='',$id='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		
		$this->modules->module_checker(3,'REVIEW');
		
		$table= "roles";
		$data['vfile']				= 'roles.php';
	    $data['title']				= 'Roles | San Miguel Brewing International';
	    
		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
	    $data['breadCrumbs']			 = '<a href='.$HTTP_PATH.'users> Users </a> ';
		$data['breadCrumbs']	   		.= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']			.= '<a href='.$HTTP_PATH.'users/roles>  Roles </a>';
	    
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(15);
		
		//CRUD
		$data['ADD'] 	=  $this->modules->crud_checker(3,'ADD');
		$data['EDIT'] 	=  $this->modules->crud_checker(3,'EDIT');
		$data['DELETE'] =  $this->modules->crud_checker(3,'DELETE');
		
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
		$user_profileID='';
		
	
		extract($_POST);
	    if($action=='update_success'){
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Roles has been updated.');
		}elseif($action=="insert")
		{
			$this->modules->module_checker(3,'ADD');
			if($_POST==NULL){
				redirect(HTTP_PATH.'users/roles', 'location', 301);
				die();
			}
			
			//INSERT FIELD
			$dbFields['roleName'] 		= $roleName;
			$dbFields['user_profileID'] = $user_profileID;
			$dbFields['dateAdded'] 		= date('Y-m-d');
			$res = $this->c3model->c3crud("insert",$table,$dbFields,'');
			
	
			$sql		= "select max(id) as max_id FROM $table";
			$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
			$roleID 	= $lastID[0]['max_id'];
			
			if(isset($permission)){
				foreach($permission as $cMod => $value)
				{
					$pieces = explode("|",$value);
					$refdbFields['roleID'] =   $roleID;
					$refdbFields['systemModID']   = $pieces[0];  
					$refdbFields['function']   = 	$pieces[1]; 
					$refdbFields['profileID']   = 	$pieces[2]; 
					$res = $this->c3model->c3crud("insert",'roles_userProfilesRef',$refdbFields,'');
				}
			}
			
			//LOGS
			$sql		= "select max(id) as max_id FROM $table";
			$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
			$maxID 		= $lastID[0]['max_id'];
			$CI->rec_logs->w($maxID,$roleName,'Admin','Roles','add');
			
			//MSG
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'User Role has been save.');
		}
		elseif($action=="deleteOneItem")
		{
			$this->modules->module_checker(3,'DELETE');
			
			$tables = array(
			  array('tbl'=>'admin_usersRoles',
					'fld'=>'roleID'),
			  array('tbl'=>'roles_userProfilesRef',
					'fld'=>'roleID'));
							
			if($this->modules->attr($tables,$id)==0)
			{
				$sql = "SELECT roleName FROM roles WHERE id = $id";
				$sql = $this->db->query($sql);
				$sql = $sql->row();
				$CI->rec_logs->w($id,$sql->roleName,'Admin','Roles','delete');
				
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'User Role has been deleted.');
				$this->c3model->c3crud('delete',$table,'',$id,'');
				$this->c3model->c3crud("no-res",'','','',"DELETE FROM roles_userProfilesRef WHERE roleID='$id'");
			}else{
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'User Role cannot be delete, because it is being use by the system.');
			}	
			
			
		}
		elseif($action=="edit")
		{
			$this->modules->module_checker(3,'EDIT');
			$data['vfile']		= 'roleFORM.php';
			$data['id'] = $id;
		}
		elseif($action=="add")
		{
			$this->modules->module_checker(3,'ADD');
			$data['vfile']			= 'roleFORM.php';
			
			if(isset($user_profileID))
			{
				$data['selectedProfileID'] = $user_profileID;
			}
			
			if(isset($ready))
			{
				$data['ready'] = $ready;
			}
		}
		elseif($action=="update")
		{
			$this->modules->module_checker(3,'EDIT');
			if($_POST==NULL){
				redirect(HTTP_PATH.'users/roles', 'location', 301);
				die();
			}
			
			$dbFields['roleName'] 			= $roleName;
			$dbFields['user_profileID'] 	= $user_profileID;
			$dbFields['dateLastEdited'] 	= date('Y-m-d');;
			$this->c3model->c3crud("update",$table,$dbFields,$id);
			
			$this->c3model->c3crud("no-res",'','','',"DELETE FROM roles_userProfilesRef WHERE roleID ='$id'");
			if(isset($permission))
			{
				foreach($permission as $cMod => $value)
				{
					$pieces = explode("|",$value);
					$refdbFields['roleID'] =   $id;
					$refdbFields['systemModID']   = $pieces[0];  
					$refdbFields['function']   = 	$pieces[1];
					$refdbFields['profileID']   = 	$pieces[2]; 
					$res = $this->c3model->c3crud("insert",'roles_userProfilesRef',$refdbFields,'');
				}
			}
			
			$CI->rec_logs->w($id,$roleName,'Admin','Roles','edit');
			redirect(HTTP_PATH.'users/roles/update_success', 'location', 301);
		}
		elseif($action=="page")
		{
			$this->modules->module_checker(3,'REVIEW');
			
			$pagenum = $id;
			$data['active_page'] = $id; 
			$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		}
		
		
		//STATUS LISTS
		$sql 			= $this->db->query("SELECT * FROM $table ORDER BY roleName ASC $max");
		$data['roles'] = $sql->result_array();
		
		
	    $this->load->view('innerPages',$data); 
	}
	
	/*ROLES 2*/
	function module($id,$roleID)
	{
		$sql = "SELECT *, system_mod.id as moduleID  FROM user_profileRef 
							   LEFT JOIN system_mod 
							   ON system_mod.id = user_profileRef.system_modID
							   WHERE user_profileID = $id ";
		$sql 	= $this->db->query($sql);
		$profiles = $sql->result_array();
		//$roleID = $id;
		
		foreach($profiles as $p)
		{
			extract($p);
			echo $modname."<br/>";
			echo "<input name='permission[]' type='checkbox' value='$moduleID|ADD' ". $this->moduleChecker($moduleID,'ADD',$roleID) .		" style='vertical-align:baseline'> 	<span style='margin-left:5px;margin-right:10px;;padding-right:10px;'> ADD </span> 
				  <input name='permission[]' type='checkbox'  value='$moduleID|EDIT' ". $this->moduleChecker($moduleID,'EDIT',$roleID) .	" style='vertical-align:baseline'> 	<span style='margin-left:5px;margin-right:10px;;padding-right:10px;'> EDIT </span> 
				  <input name='permission[]' type='checkbox'  value='$moduleID|DELETE' ". $this->moduleChecker($moduleID,'DELETE',$roleID) ." style='vertical-align:baseline'> 	<span style='margin-left:5px;margin-right:10px;;padding-right:10px;'> DELETE </span> 
				  <input name='permission[]' type='checkbox'  value='$moduleID|REVIEW' ". $this->moduleChecker($moduleID,'REVIEW',$roleID) ." style='vertical-align:baseline'> 	<span style='margin-left:5px;margin-right:10px;;padding-right:10px;'> REVIEW </span> 
				  <div class='clear' style='height:15px;'></div>";
				  
			echo "<br/>";
		}

	}
	
	//MODULE CHECKER
	function moduleChecker($moduleID,$function,$roleID)
	{
		$sql = $this->db->query("SELECT * FROM roles_userProfilesRef 
							    WHERE roleID = $roleID AND
							    systemModID = $moduleID AND
							    function = '$function'");
		$roles = $sql->result_array();
		
		if($roles!=NULL)
		{
			return "checked";
		}
	}

	function POSM_fields($action='',$id='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		
		$this->modules->module_checker(33,'REVIEW');
		
		$table= "POSM_status_fields";
		$data['vfile']				= 'POSM_fields.php';
	    $data['title']				= 'POSM Fields | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//CRUD
		$data['ADD'] 	=  $this->modules->crud_checker(33,'ADD');
		$data['EDIT'] 	=  $this->modules->crud_checker(33,'EDIT');
		$data['DELETE'] =  $this->modules->crud_checker(33,'DELETE');
		
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(26);
		
		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
	    $data['breadCrumbs']		= '<a href='.$HTTP_PATH.'users> Users </a>';
	    $data['breadCrumbs']	   .= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']	   .= '<a href='.$HTTP_PATH.'users/POSM_fields> POSM Fields </a>';
	    
		
		//CSRF
		$data['csrf'] = "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>"; 
		
		extract($_POST);
	    if($action=="insert")
		{
			$this->modules->module_checker(33,'ADD');
			//INSERT FIELD
			foreach($fields as $f => $value)
			{
				$refFields['POSM_statusID'] = $POSM_statusID;
				$refFields['POSM_FieldID']  = $value;
				$refFields['validation']    = $validations[$f];
				$res = $this->c3model->c3crud("insert",$table,$refFields,'');
			}
			
			foreach($validations as $v => $val)
			{ 
			  $val_exploded = explode("|",$val);
			  //CHECK IF RECORD EXIST
			  $sql = $this->db->query("UPDATE POSM_status_fields set validation='".$val_exploded[0]."' WHERE POSM_statusID = ".$val_exploded[1]." AND POSM_FieldID=".$val_exploded[2]);
			}
			
			//LOGS
			$sql		= "select max(id) as max_id FROM $table";
			$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
			$maxID 		= $lastID[0]['max_id'];
			
			//LOGS
			echo $sql = "SELECT statusName FROM POSM_Status WHERE id = $POSM_statusID";
			$sql = $this->db->query($sql);
			$sql = $sql->row();
			
			$CI->rec_logs->w($maxID,$sql->statusName,'Admin','POSM Fields','add');
			
			//MSG
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'POSM STATUS fields has been save.');
		}
		elseif($action=="deleteOneItem")
		{
			$this->modules->module_checker(33,'DELETE');
			
			//LOGS
			$sql = "SELECT statusName FROM POSM_Status WHERE id = $id";
			$sql = $this->db->query($sql);
			$sql = $sql->row();
			
			$CI->rec_logs->w($id,$sql->statusName,'Admin','POSM Fields','delete');
			
			$this->c3model->c3crud("no-res",'','','',"DELETE FROM POSM_status_fields WHERE POSM_statusID = '$id'");
			$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'POSM status fields has been deleted.');
			
		}
		elseif($action=="edit")
		{
			$this->modules->module_checker(33,'EDIT');
			$data['vfile']				= 'POSM_fieldsFORM.php';
			$data['id'] = $id;
		}
		elseif($action=="add")
		{
			$this->modules->module_checker(33,'ADD');
			$data['vfile']				= 'POSM_fieldsFORM.php';
		}
		elseif($action=="update")
		{
			$this->modules->module_checker(33,'EDIT');
			$this->c3model->c3crud("no-res",'','','',"DELETE FROM POSM_status_fields WHERE POSM_statusID = '$id'");
		
			foreach($fields as $f => $value)
			{
				$refFields['POSM_statusID'] = $POSM_statusID;
				$refFields['POSM_FieldID']  = $value;
				$res = $this->c3model->c3crud("insert",$table,$refFields,'');
			}
			
			foreach($validations as $v => $val)
			{ 
			  $val_exploded = explode("|",$val);
			  //CHECK IF RECORD EXIST
			  $sql = $this->db->query("UPDATE POSM_status_fields set validation='".$val_exploded[0]."' WHERE POSM_statusID = ".$val_exploded[1]." AND POSM_FieldID=".$val_exploded[2]);
			}
			
			//LOGS
			$sql = "SELECT statusName FROM POSM_Status WHERE id = $POSM_statusID";
			$sql = $this->db->query($sql);
			$sql = $sql->row();
			
			$CI->rec_logs->w($id,$sql->statusName,'Admin','POSM Fields','edit');
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'POSM STATUS FIELDS has been updated.');
		}

		//STATUS LISTS
		$sql 			= $this->db->query("SELECT DISTINCT POSM_statusID, POSM_Status.statusName
										    FROM  POSM_status_fields
										    LEFT JOIN  POSM_Status
										    ON POSM_status_fields.POSM_statusID = POSM_Status.id ");
		$data['POSM_fields_rec'] = $sql->result_array();
		
		$sql 			= $this->db->query("SELECT * FROM POSM_fields ORDER BY fieldName ASC");
		$data['POSM_fields'] = $sql->result_array();
	
	    $this->load->view('innerPages',$data); 
	}
	
	function countrySelect($opt='')
	{		
	  $validation = "data-trigger='change' data-required='true' placeholder='Required field'";

	  $this->load->database();
	  if($opt=='y'){
		$sql = "SELECT * FROM country WHERE id = 0";
		$selected = 0;
	  }else{
		$sql = "SELECT * FROM country WHERE id!=0";
		$selected = '';
	  }
	 
	  $query = $this->db->query($sql);
	  
	  $sel  = "<div class='fl'>";
		  $sel .= "<h2 class='form'> COUNTRY NAME </h2>";
		  $sel .= " ";
		  
			$sel .= "<select name='countryID'  $validation style='width:410px;'>";
			$sFlag="";
			//DETERMIN parent			  
				foreach($query->result() as $r)
				{
					$id   = $r->id;
					$name = $r->countryName;
					if($id==$selected)	$sFlag="selected";			
					$sel .="<option $sFlag value='$id'>$name</option>";
					$sFlag="";
				}
				
			$sel .= "</select>";
		$sel .= "";
	  $sel .= "</div>";
	  
	  echo $sel;
	}
	
	function featured()
	{
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
	   return $data;
	}
	
	function iLikeCanvassingRules($action='',$id='',$POSM_TypeID='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		$filter_WHERE = $this->modules->country();
		$this->modules->module_checker(64,'REVIEW');
		
		$data['vfile']				= 'iLikeCanvassingRules.php';
	    $data['title']				= 'iLike Campaign Rules | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//CRUD
		$data['ADD'] 	=  $this->modules->crud_checker(64,'ADD');
		$data['EDIT'] 	=  $this->modules->crud_checker(64,'EDIT');
		$data['DELETE'] =  $this->modules->crud_checker(64,'DELETE');
		
		$data['USER_MANUAL'] = $this->modules->user_manual(37);
		
		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
	    $data['breadCrumbs']		= '<a href='.$HTTP_PATH.'users> Users </a>';
	    $data['breadCrumbs']	   .= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']	   .= '<a href='.$HTTP_PATH.'users/iLikeCanvassingRules> iLike Canvassing Rules </a>';

		//CSRF
		$data['csrf'] = "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>"; 
		
		//TOTAL NUMBER OF ROWS
		$data['active_page']=1;
		$sql = $this->db->query("SELECT country.id as cID, countryName FROM country WHERE country.id != 0 ORDER BY countryName ASC");
		$sql = $sql->result_array();
		$total_rec = count($sql);

		$pagenum = 1;
		$data['page_rows'] = 2; 
		$data['last'] = ceil($total_rec/$data['page_rows']);		
		$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		
		extract($_POST);
	    if($action=="insert")
		{
			$error=0;
			$this->modules->module_checker(64,'ADD');
			$dbFields['countryID'] =  $countryID;
			
			//INSERT FIELD
			if($POSMTypeID!=NULL){
				$dbFields['price_rangeID'] 	    =  $price_rangeID;
				$dbFields['fieldName'] 		    =  'POSMTypeID';
				$dbFields['fieldID']   		    =  $POSMTypeID;
				$dbFields['cond1']   	  		=  $cond1;
				$dbFields['min_val']   	  		=  $min_val;
				$dbFields['logical_operator']   =  $logical_operator;
				$dbFields['cond2']   			=  $cond2;
				$dbFields['max_val']   			=  $max_val;
				$dbFields['dateAdded'] 		    =  date('Y-m-d');
				//DO NOT INSER IF ALREADY Exist
				if($this->validateInput($min_val)==0 AND $price_rangeID!="")
					$res = $this->c3model->c3crud("insert",'iLikeCanvassingRules',$dbFields,'');
				else
				 $error++;
			}

			//LOGS
			$sql		= "select max(id) as max_id FROM iLikeCanvassingRules";
			$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
			$maxID 		= $lastID[0]['max_id'];
			$CI->rec_logs->w($maxID,'iLike Canvassing Rules','iLike','iLike Canvassing Rules','add');
			
			//MSG
			if($error==0){
			 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'iLike Canvassing Rules has been save.');
			}else{
			 $data['msg'] 	= array('msg_type'=>'alert-warning','msg_desc'=>'iLike Canvassing Rules is invalid');
			 $data['vfile']		   = 'iLikeCanvassingRulesFORM.php';
			 $data['POST'] 		   = $_POST;
			}
		}
		elseif($action=="deleteOneItem")
		{
			$this->modules->module_checker(64,'DELETE');
			
			$CI->rec_logs->w($id,'iLike Canvassing Rules','iLike','iLike Rules','delete');
			
			$this->c3model->c3crud("no-res",'','','',"DELETE FROM iLikeCanvassingRules WHERE id = '$id'");
			$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'iLike Canvassing Rule has been delete.');
			
		}
		elseif($action=="add")
		{
			$this->modules->module_checker(64,'ADD');
			$data['vfile']	= 'iLikeCanvassingRulesFORM.php';
			$data['country_ID']   	= $id;
			$data['pID'] 		 	= $POSM_TypeID;
		}
		elseif($action=="edit")
		{
			$this->modules->module_checker(64,'EDIT');
			$data['iLikeCanvassingRulesID']		= $id;
			$data['vfile']					= 'iLikeCanvassingRulesFORM.php';
		}
		elseif($action=="update")
		{
		    $error=0;
			$this->modules->module_checker(64,'EDIT');
			$dbFields['countryID'] =  $countryID;
			$table="iLikeCanvassingRules";
			
			//INSERT FIELD
			if($POSMTypeID!=NULL){
				$dbFields['price_rangeID'] 	    =  $price_rangeID;
				$dbFields['fieldName'] 		    =  'POSMTypeID';
				$dbFields['fieldID']   		    =  $POSMTypeID;
				$dbFields['cond1']   	  		=  $cond1;
				$dbFields['min_val']   	  		=  $min_val;
				$dbFields['logical_operator']   =  $logical_operator;
				$dbFields['cond2']   			=  $cond2;
				$dbFields['max_val']   			=  $max_val;
				$dbFields['dateAdded'] 		    =  date('Y-m-d');
				//DO NOT INSER IF ALREADY Exist
				if($this->validateInput($min_val)==0 AND $price_rangeID!="")
					$this->c3model->c3crud("update","iLikeCanvassingRules",$dbFields,$id);
				else
				 $error++;
			}
			
			//LOGS
			$CI->rec_logs->w($id,'iLike Canvassing Rules','iLike','iLike Canvassing Rules','edit');
			
			if($error==0){
			 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'iLike Canvassing Rules has been updated.');
			}else{
			 $data['msg'] 	= array('msg_type'=>'alert-warning','msg_desc'=>'iLike Canvassing Rules is invalid, please try again.');
			 $data['vfile']	= 'iLikeCanvassingRulesFORM.php';
			 $data['POST'] 	= $_POST;
			 $data['iLikeCanvassingRulesID'] 	= $id;
			}	
		}
		elseif($action=="page")
		{
			$this->modules->module_checker(64,'REVIEW');
			$pagenum = $id;
			$data['active_page'] = $id; 
			$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		}
		
		
		//STATUS LISTS
		$sql 	= $this->db->query("SELECT country.id as cID, countryName FROM country WHERE country.id != 0 ORDER BY countryName ASC $max");
		$data['countries'] = $sql->result_array();
		
		$sql 			   = $this->db->query("SELECT POSM_Type.id as pID, typeName FROM POSM_Type ORDER BY typeName ASC");
		$data['POSM_Type'] = $sql->result_array();
		
	    $this->load->view('innerPages',$data); 
	}
	
	function iWantCanvassingRules($action='',$id='',$POSM_TypeID='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		$filter_WHERE = $this->modules->country();
		$this->modules->module_checker(65,'REVIEW');
		
		$data['vfile']				= 'iWantCanvassingRules.php';
	    $data['title']				= 'iWant Campaign Rules | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//CRUD
		$data['ADD'] 	=  $this->modules->crud_checker(65,'ADD');
		$data['EDIT'] 	=  $this->modules->crud_checker(65,'EDIT');
		$data['DELETE'] =  $this->modules->crud_checker(65,'DELETE');
		
		$data['USER_MANUAL'] = $this->modules->user_manual(39);
		
		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
	    $data['breadCrumbs']		= '<a href='.$HTTP_PATH.'users> Users </a>';
	    $data['breadCrumbs']	   .= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']	   .= '<a href='.$HTTP_PATH.'users/iWantCanvassingRules> iWant Canvassing Rules </a>';

		//CSRF
		$data['csrf'] = "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>"; 
		
		//TOTAL NUMBER OF ROWS
		$data['active_page']=1;
		$sql = $this->db->query("SELECT country.id as cID, countryName FROM country WHERE country.id = 0 ORDER BY countryName ASC");
		$sql = $sql->result_array();
		$total_rec = count($sql);

		$pagenum = 1;
		$data['page_rows'] = 2; 
		$data['last'] = ceil($total_rec/$data['page_rows']);		
		$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		
		extract($_POST);
	    if($action=="insert")
		{
			$error=0;
			$this->modules->module_checker(65,'ADD');
			$dbFields['countryID'] =  $countryID;
			
			//INSERT FIELD
			if($POSMTypeID!=NULL){
				$dbFields['price_rangeID'] 	    =  $price_rangeID;
				$dbFields['fieldName'] 		    =  'POSMTypeID';
				$dbFields['fieldID']   		    =  $POSMTypeID;
				$dbFields['cond1']   	  		=  $cond1;
				$dbFields['min_val']   	  		=  $min_val;
				$dbFields['logical_operator']   =  $logical_operator;
				$dbFields['cond2']   			=  $cond2;
				$dbFields['max_val']   			=  $max_val;
				$dbFields['dateAdded'] 		    =  date('Y-m-d');
				//DO NOT INSER IF ALREADY Exist
				if($this->validateInput($min_val)==0 AND $price_rangeID!="")
					$res = $this->c3model->c3crud("insert",'iWantCanvassingRules',$dbFields,'');
				else
				 $error++;
			}

			//LOGS
			$sql		= "select max(id) as max_id FROM iWantCanvassingRules";
			$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
			$maxID 		= $lastID[0]['max_id'];
			$CI->rec_logs->w($maxID,'iWant Canvassing Rules','iWant','iWant Canvassing Rules','add');
			
			//MSG
			if($error==0){
			 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'iWant Canvassing Rules has been save.');
			}else{
			 $data['msg'] 	= array('msg_type'=>'alert-warning','msg_desc'=>'iWant Canvassing Rules is invalid');
			 $data['vfile']		   = 'iWantCanvassingRulesFORM.php';
			 $data['POST'] 		   = $_POST;
			}
		}
		elseif($action=="deleteOneItem")
		{
			$this->modules->module_checker(65,'DELETE');
			
			$CI->rec_logs->w($id,'iWant Canvassing Rules','iWant','iWant Rules','delete');
			
			$this->c3model->c3crud("no-res",'','','',"DELETE FROM iWantCanvassingRules WHERE id = '$id'");
			$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'iWant Canvassing Rule has been delete.');
			
		}
		elseif($action=="add")
		{
			$this->modules->module_checker(65,'ADD');
			$data['vfile']	= 'iWantCanvassingRulesFORM.php';
			$data['country_ID']   	= $id;
			$data['POSMTypeID'] 	= $POSM_TypeID;
		}
		elseif($action=="edit")
		{
			$this->modules->module_checker(65,'EDIT');
			$data['iWantCanvassingRulesID']		= $id;
			$data['vfile']					= 'iWantCanvassingRulesFORM.php';
		}
		elseif($action=="update")
		{
		    $error=0;
			$this->modules->module_checker(65,'EDIT');
			$dbFields['countryID'] =  $countryID;
			$table="iWantCanvassingRules";
			
			if($POSMTypeID!=NULL){
				$dbFields['price_rangeID'] 	    =  $price_rangeID;
				$dbFields['fieldName'] 		    =  'POSMTypeID';
				$dbFields['fieldID']   		    =  $POSMTypeID;
				$dbFields['cond1']   	  		=  $cond1;
				$dbFields['min_val']   	  		=  $min_val;
				$dbFields['logical_operator']   =  $logical_operator;
				$dbFields['cond2']   			=  $cond2;
				$dbFields['max_val']   			=  $max_val;
				$dbFields['dateAdded'] 		    =  date('Y-m-d');
				//DO NOT INSER IF ALREADY Exist
				if($this->validateInput($min_val)==0 AND $price_rangeID!="")
					$this->c3model->c3crud("update","iWantCanvassingRules",$dbFields,$id);
				else
				 $error++;
			}
		
			
			//LOGS
			$CI->rec_logs->w($id,'iWant Canvassing Rules','iWant','iWant Canvassing Rules','edit');
			
			if($error==0){
			 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'iWant Canvassing Rules has been updated.');
			}else{
			 $data['msg'] 	= array('msg_type'=>'alert-warning','msg_desc'=>'iWant Canvassing Rules is invalid, please try again.');
			 $data['vfile']	= 'iLikeVotingRulesFORM.php';
			 $data['POST'] 	= $_POST;
			 $data['iLikeVotingRulesID'] = $id;
			}	
		}
		elseif($action=="page")
		{
			$this->modules->module_checker(65,'REVIEW');
			$pagenum = $id;
			$data['active_page'] = $id; 
			$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		}
		
		
		//STATUS LISTS
		$sql 	= $this->db->query("SELECT country.id as cID, countryName FROM country WHERE country.id = 0 ORDER BY countryName ASC $max");
		$data['countries'] = $sql->result_array();
		
		$sql 			   = $this->db->query("SELECT POSM_Type.id as pID, typeName FROM POSM_Type ORDER BY typeName ASC");
		$data['POSM_Type'] = $sql->result_array();
		
	    $this->load->view('innerPages',$data); 
	}
	
	function iWantRanking($action='',$id='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		$filter_WHERE = $this->modules->country();
		$this->modules->module_checker(66,'REVIEW');
		
		$table= "iWantRanking";
		$data['vfile']				= 'iWantRanking.php';
	    $data['title']				= 'iWant Ranking | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		$data['USER_MANUAL'] = $this->modules->user_manual(40);
		
		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
	    $data['breadCrumbs']		= '<a href='.$HTTP_PATH.'users> Users </a>';
	    $data['breadCrumbs']	   .= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']	   .= '<a href='.$HTTP_PATH.'users/iWantRanking> iWant Ranking </a>';
	    
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(2);
		
		//CRUD
		$data['ADD'] 	=  $this->modules->crud_checker(66,'ADD');
		$data['EDIT'] 	=  $this->modules->crud_checker(66,'EDIT');
		$data['DELETE'] =  $this->modules->crud_checker(66,'DELETE');
		
		//CSRF
		$data['csrf'] = "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>"; 
		
		//PAGINATION
		//PAGINATION
		//TOTAL NUMBER OF ROWS
		$data['active_page']=1;
		$sql = $this->db->query("SELECT id FROM $table $filter_WHERE");
		$sql = $sql->result_array();
		$total_rec = count($sql);

		extract($_POST);		
		if($action=="edit")
		{
			$this->modules->module_checker(66,'EDIT');
			$data['id'] = $id;
		}
		elseif($action=="update")
		{
			$this->modules->module_checker(66,'EDIT');
			
			if($minRank >= $maxRank OR $maxRank<=2)
			{	
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'iWant Ranking value is incorrect.');
			}else{
				$dbFields['countryID']   = $countryID;
				//$dbFields['minRank']     = $minRank;
				$dbFields['maxRank']     = $maxRank;
			
				$CI->rec_logs->w($id,'iWant Ranking Setting','iWant','iWant Ranking Setting','edit');
				
				$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'iWant Ranking has been updated.');
				$this->c3model->c3crud("update",$table,$dbFields,$id);
			}
		}
		
		
		//STATUS LISTS
		$sql 				  = $this->db->query("SELECT *, $table.id as iWantRankingID FROM $table 
												  LEFT JOIN country ON country.id = $table.countryID $filter_WHERE");
		$data['iWantRanking'] = $sql->result_array();
		
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
	
	function validateInput($input)
	{
		$error=0;
		//BLANK EMPTY .
		if($input=="" OR $input==" "){
		 $error++;
		 if($input[0]==".")
		  $error++;
		}

		if(strpos($input,".")==TRUE){
			//GREATER THAN 1.0
			if($input>=1.0) $error++;
			//4 DECIMAL PLACES
			if(isset($input[4])){
				if($input[0]=="0" AND $input[1]=="." AND $input[4]!="")
					$error++;
			}
			//POINT SOMETHING
			if($input[0]=="." AND $input[3]!="")
			    $error++;
		}
		
		if($input<0 OR $input==0)
		 $error++;
		
		return $error;
	}
    
	function price_range($action='',$id='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		$filter_WHERE = $this->modules->country();
		$this->modules->module_checker(83,'REVIEW');
		
		$data['vfile']				= 'price_range.php';
	    $data['title']				= 'iLike Campaign Rules | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//CRUD
		$data['ADD'] 	=  $this->modules->crud_checker(83,'ADD');
		$data['EDIT'] 	=  $this->modules->crud_checker(83,'EDIT');
		$data['DELETE'] =  $this->modules->crud_checker(83,'DELETE');
		
		$data['USER_MANUAL'] = $this->modules->user_manual(35);
		
		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
	    $data['breadCrumbs']		= '<a href='.$HTTP_PATH.'users> Users </a>';
	    $data['breadCrumbs']	   .= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']	   .= '<a href='.$HTTP_PATH.'users/price_range> US Dollar - Price Range </a>';

		//CSRF
		$data['csrf'] = "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>"; 

		extract($_POST);
	    if($action=="insert")
		{
			$error=0;
			$this->modules->module_checker(83,'ADD');
		
			//INSERT FIELD
			$dbFields['xOrder']   	  		=  $xOrder;
			$dbFields['POSMTypeID']   	  	=  $POSMTypeID;
			$dbFields['level_name']   		=  $level_name;
			$dbFields['extra_label']   		=  $extra_label;
			$dbFields['campaign_label']   	=  $campaign_label;
			$dbFields['cond1']   	  		=  $cond1;
			$dbFields['min_val']   	  		=  $min_val;
			$dbFields['logical_operator']   =  $logical_operator;
			$dbFields['cond2']   			=  $cond2;
			$dbFields['max_val']   			=  $max_val;
			$dbFields['dateAdded'] 			=  date('Y-m-d');
			$res = $this->c3model->c3crud("insert","price_range",$dbFields,'');
			
			//LOGS
			$sql		= "select max(id) as max_id FROM price_range";
			$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
			$maxID 		= $lastID[0]['max_id'];
			$CI->rec_logs->w($maxID,"$level_name",'Item Attribute','Price Range','add');
			
			//MSG
			if($error==0){
			 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'USD Price Range has been save.');
			}else{
			 $data['msg'] 	= array('msg_type'=>'alert-warning','msg_desc'=>'USD Price Range is invalid.');
			 $data['vfile']	= 'price_range.php';
			 $data['POST'] 	= $_POST;
			 $data['POST_FieldID'] = $dbFields['fieldID'];
			 $data['POST_rel'] 	   = $dbFields['rel'];
			 $data['value']   	   = $dbFields['val'] ;
			}
		}
		elseif($action=="deleteOneItem")
		{
			$this->modules->module_checker(83,'DELETE');
			
			$tables = array(
							array('tbl'=>'items',
								  'fld'=>'price_rangeID'));
				
			if($this->modules->attr($tables,$id)==0)
			{
				$sql		= "select level_name FROM price_range WHERE id='$id'";
				$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
				$level_name = $lastID[0]['level_name'];
				$CI->rec_logs->w($id,"$level_name",'Item Attribute','Price Range','delete');
				$CI->rec_logs->w($id,'','iLike','iLike Rules','delete');
				
				$this->c3model->c3crud("no-res",'','','',"DELETE FROM price_range WHERE id = '$id'");
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Price Range has been delete.');
			}else{
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Price Range cannot be delete because it is linked to items.');
			}
		}
		elseif($action=="add")
		{
			$this->modules->module_checker(83,'ADD');
			$data['vfile']			  	   = 'price_rangeFORM.php';
			$data['ParamPOSMTypeID']   =  $id;
		}
		elseif($action=="edit")
		{
			$this->modules->module_checker(83,'EDIT');
			$data['price_rangeID']	= $id;
			$data['vfile']			= 'price_rangeFORM.php';
		}
		elseif($action=="update")
		{
		    $error=0;
			$this->modules->module_checker(83,'EDIT');
			//INSERT FIELD
			$dbFields['POSMTypeID']   	  	=  $POSMTypeID;
			$dbFields['xOrder']   	  		=  $xOrder;
			$dbFields['level_name']   		=  $level_name;
			$dbFields['extra_label']   		=  $extra_label;
			$dbFields['campaign_label']   	=  $campaign_label;
			$dbFields['cond1']   	  		=  $cond1;
			$dbFields['min_val']   	  		=  $min_val;
			$dbFields['logical_operator']   =  $logical_operator;
			$dbFields['cond2']   			=  $cond2;
			$dbFields['max_val']   			=  $max_val;
			$dbFields['dateLastEdited'] 	=  date('Y-m-d');
			$this->c3model->c3crud("update","price_range",$dbFields,$id);
	
			//LOGS
			$CI->rec_logs->w($id,"$level_name",'Item Attribute','Price Range','add');
			
			if($error==0){
			 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Price Range has been updated.');
			}else{
			 $data['msg'] 	= array('msg_type'=>'alert-warning','msg_desc'=>'Price Range is invalid, please try again.');
			 $data['vfile']	= 'price_range.php';
			}	
		}
		//STATUS LISTS
		$sql = $this->db->query("SELECT *,POSM_Type.id as POSM_TypeID  FROM POSM_Type ORDER BY typeName ASC");
		$data['POSMTypes'] = $sql->result_array();
	
	    $this->load->view('innerPages',$data); 
	}

	function iLikeVotingRules($action='',$id='',$POSM_TypeID='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		$filter_WHERE = $this->modules->country();
		$this->modules->module_checker(59,'REVIEW');
		
		$data['vfile']				= 'iLikeVotingRules.php';
	    $data['title']				= 'iLike Campaign Rules | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//CRUD
		$data['ADD'] 	=  $this->modules->crud_checker(59,'ADD');
		$data['EDIT'] 	=  $this->modules->crud_checker(59,'EDIT');
		$data['DELETE'] =  $this->modules->crud_checker(59,'DELETE');
		
		$data['USER_MANUAL'] = $this->modules->user_manual(35);
		
		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
	    $data['breadCrumbs']		= '<a href='.$HTTP_PATH.'users> Users </a>';
	    $data['breadCrumbs']	   .= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']	   .= '<a href='.$HTTP_PATH.'users/iLikeVotingRules> iLike Voting Rules </a>';

		//CSRF
		$data['csrf'] = "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>"; 
		
		//TOTAL NUMBER OF ROWS
		$data['active_page']=1;
		$sql = $this->db->query("SELECT country.id as cID, countryName FROM country WHERE country.id != 0 ORDER BY countryName ASC");
		$sql = $sql->result_array();
		$total_rec = count($sql);

		$pagenum = 1;
		$data['page_rows'] = 2; 
		$data['last'] = ceil($total_rec/$data['page_rows']);		
		$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		
		extract($_POST);
	    if($action=="insert")
		{
			$error=0;
			$this->modules->module_checker(59,'ADD');
			$dbFields['countryID'] =  $countryID;
			
			//INSERT FIELD
			if($POSMTypeID!=NULL){
				$dbFields['price_rangeID'] 	    =  $price_rangeID;
				$dbFields['fieldName'] 		    =  'POSMTypeID';
				$dbFields['fieldID']   		    =  $POSMTypeID;
				$dbFields['cond1']   	  		=  $cond1;
				$dbFields['min_val']   	  		=  $min_val;
				$dbFields['logical_operator']   =  $logical_operator;
				$dbFields['cond2']   			=  $cond2;
				$dbFields['max_val']   			=  $max_val;
				$dbFields['dateAdded'] 		    =  date('Y-m-d');
				//DO NOT INSER IF ALREADY Exist
				if($this->validateInput($min_val)==0 AND $price_rangeID!="")
					$res = $this->c3model->c3crud("insert",'iLikeVotingRules',$dbFields,'');
				else
				 $error++;
			}

			//LOGS
			$sql		= "select max(id) as max_id FROM iLikeVotingRules";
			$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
			$maxID 		= $lastID[0]['max_id'];
			$CI->rec_logs->w($maxID,'iLike Voting Rules','iLike','iLike Voting Rules','add');
			
			//MSG
			if($error==0){
			 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'iLike Voting Rules has been save.');
			}else{
			 $data['msg'] 	= array('msg_type'=>'alert-warning','msg_desc'=>'iLike Voting Rules is invalid');
			 $data['vfile']		   = 'iLikeVotingRulesFORM.php';
			 $data['POST'] 		   		 =  $_POST;
			}
		}
		elseif($action=="deleteOneItem")
		{
			$this->modules->module_checker(59,'DELETE');
			
			$CI->rec_logs->w($id,'iLike Voting Rules','iLike','iLike Rules','delete');
			
			$this->c3model->c3crud("no-res",'','','',"DELETE FROM iLikeVotingRules WHERE id = '$id'");
			$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'iLike Voting Rule has been deleted.');
			
		}
		elseif($action=="add")
		{
			$this->modules->module_checker(59,'ADD');
			$data['vfile']	= 'iLikeVotingRulesFORM.php';
			$data['countryID']   	= $id;
			$data['POSMTypeID']     = $POSM_TypeID;
		}
		elseif($action=="edit")
		{
			$this->modules->module_checker(59,'EDIT');
			$data['iLikeVotingRulesID']		= $id;
			$data['vfile']					= 'iLikeVotingRulesFORM.php';
		}
		elseif($action=="update")
		{
		    $error=0;
			$this->modules->module_checker(41,'EDIT');
			$dbFields['countryID'] =  $countryID;
			$table="iLikeVotingRules";
			
			if($POSMTypeID!=NULL){
				$dbFields['price_rangeID'] 	    =  $price_rangeID;
				$dbFields['fieldName'] 		    =  'POSMTypeID';
				$dbFields['fieldID']   		    =  $POSMTypeID;
				$dbFields['cond1']   	  		=  $cond1;
				$dbFields['min_val']   	  		=  $min_val;
				$dbFields['logical_operator']   =  $logical_operator;
				$dbFields['cond2']   			=  $cond2;
				$dbFields['max_val']   			=  $max_val;
				$dbFields['dateLastEdited'] 	=  date('Y-m-d');
				//DO NOT INSER IF ALREADY Exist
				if($this->validateInput($min_val)==0 AND $price_rangeID!="")
					$this->c3model->c3crud("update",$table,$dbFields,$id);
				else
				 $error++;
			}
			
			//LOGS
			$CI->rec_logs->w($id,'iLike Voting Rules','iLike','iLike Voting Rules','edit');
			
			if($error==0){
			 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'iLike Voting Rules has been updated.');
			}else{
			 $data['msg'] 	= array('msg_type'=>'alert-warning','msg_desc'=>'iLike Voting Rules is invalid, please try again.');
			 $data['vfile']	= 'iLikeVotingRulesFORM.php';
			 $data['POST'] 	= $_POST;
			  $data['iLikeVotingRulesID'] 	= $id;
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
		$sql 	= $this->db->query("SELECT country.id as cID, countryName FROM country WHERE country.id != 0 ORDER BY countryName ASC $max");
		$data['countries'] = $sql->result_array();
		
		$sql 			   = $this->db->query("SELECT POSM_Type.id as pID, typeName FROM POSM_Type ORDER BY typeName ASC");
		$data['POSM_Type'] = $sql->result_array();
		
	    $this->load->view('innerPages',$data); 
	}

	function iLikeCampaignRules($action='',$id='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		$filter_WHERE = $this->modules->country();
		$this->modules->module_checker(41,'REVIEW');
		
		$data['vfile']				= 'iLikeCampaignRules.php';
	    $data['title']				= 'iLike Campaign Rules | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation,San Miguel,company , Philippines ,Southeast Asia';
		
		//CRUD
		$data['ADD'] 	=  $this->modules->crud_checker(41,'ADD');
		$data['EDIT'] 	=  $this->modules->crud_checker(41,'EDIT');
		$data['DELETE'] =  $this->modules->crud_checker(41,'DELETE');
		
		$data['USER_MANUAL'] = $this->modules->user_manual(34);
		
		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
	    $data['breadCrumbs']		= '<a href='.$HTTP_PATH.'users> Users </a>';
	    $data['breadCrumbs']	   .= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']	   .= '<a href='.$HTTP_PATH.'users/iLikeCampaignRules> iLike Campaign Rules </a>';

		//CSRF
		$data['csrf'] = "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>"; 

		extract($_POST);
	    if($action=="insert")
		{
			$this->modules->module_checker(41,'ADD');
			$dbFields['countryID'] =  $countryID;
			
			//INSERT FIELD
			if($POSMTypeID!=NULL){
				$dbFields['fieldName'] = 'POSMTypeID';
				$dbFields['fieldID']   =  $POSMTypeID;
				$dbFields['rel']   	   =  $POSMTypeCond;
				$dbFields['val']   	   =  $POSMTypeVal;
				$dbFields['dateAdded'] =  date('Y-m-d');
				
				//DO NOT INSER IF ALREADY Exist
				//if($this->fieldChecker('POSMTypeID',$POSMTypeID,1))
					$res = $this->c3model->c3crud("insert",'iLikeCampaignRules',$dbFields,'');
			}
			
			if($POSMStatusID!=NULL){
				$dbFields['fieldName'] = 'POSMStatusID';
				$dbFields['fieldID']   =  $POSMStatusID;
				$dbFields['rel']   	   =  $POSMStatusCond;
				$dbFields['val']   	   =  $POSMStatusVal;
				$dbFields['dateAdded'] =  date('Y-m-d');
				//DO NOT INSER IF ALREADY Exist
				//if($this->fieldChecker('POSMStatusID',$POSMStatusID,1))
					$res = $this->c3model->c3crud("insert",'iLikeCampaignRules',$dbFields,'');
			}
			
			if($OUTLETStatusID!=NULL){
				$dbFields['fieldName'] = 'OUTLETStatusID';
				$dbFields['fieldID']   =  $OUTLETStatusID;
				$dbFields['rel']   	   =  $OUTLETStatusCond;
				$dbFields['val']   	   =  $OUTLETStatusVal;
				$dbFields['dateAdded'] =  date('Y-m-d');
				//DO NOT INSER IF ALREADY Exist
				//if($this->fieldChecker('OUTLETStatusID',$OUTLETStatusID,1))
					$res = $this->c3model->c3crud("insert",'iLikeCampaignRules',$dbFields,'');
			}
			
			if($PremiumTypeID!=NULL){
				$dbFields['fieldName'] = 'PremiumTypeID';
				$dbFields['fieldID']   =  $PremiumTypeID;
				$dbFields['rel']   	   =  $PremiumTypeCond;
				$dbFields['val']   	   =  $PremiumTypeVal;
				$dbFields['dateAdded'] =  date('Y-m-d');
				//DO NOT INSER IF ALREADY Exist
				//if($this->fieldChecker('PremiumTypeID',$PremiumTypeID,1))
					$res = $this->c3model->c3crud("insert",'iLikeCampaignRules',$dbFields,'');
			}
			
			if($MaterialTypeID!=NULL){
				$dbFields['fieldName'] = 'MaterialTypeID';
				$dbFields['fieldID']   =  $MaterialTypeID;
				$dbFields['rel']   	   =  $MaterialTypeCond;
				$dbFields['val']   	   =  $MaterialTypeVal;
				$dbFields['dateAdded'] =  date('Y-m-d');
				//DO NOT INSER IF ALREADY Exist
				//if($this->fieldChecker('MaterialTypeID',$MaterialTypeID,1))
					$res = $this->c3model->c3crud("insert",'iLikeCampaignRules',$dbFields,'');
			}
			
			if($brandID!=NULL){
				$dbFields['fieldName'] = 'brandID';
				$dbFields['fieldID']   =  $brandID;
				$dbFields['rel']   	   =  $brandCond;
				$dbFields['val']   	   =  $brandVal;
				$dbFields['dateAdded'] =  date('Y-m-d');
				//DO NOT INSER IF ALREADY Exist
				//if($this->fieldChecker('brandID',$brandID,1))
					$res = $this->c3model->c3crud("insert",'iLikeCampaignRules',$dbFields,'');
			}
			
			//LOGS
			$sql		= "select max(id) as max_id FROM iLikeCampaignRules";
			$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
			$maxID 		= $lastID[0]['max_id'];
			$CI->rec_logs->w($maxID,'iLike Campaign Rules','iLike','iLike Campaign Rules','add');
			
			//MSG
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'iLike Campaign Rules has been save.');
		}
		elseif($action=="deleteOneItem")
		{
			$this->modules->module_checker(41,'DELETE');
			
			$CI->rec_logs->w($id,'iLike Campaign Rules','iLike','iLike Campaign Rules','delete');
			
			$this->c3model->c3crud("no-res",'','','',"DELETE FROM iLikeCampaignRules WHERE id = '$id'");
			$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'iLike Campaign Rule has been deleted.');
			
		}
		elseif($action=="add")
		{
			$this->modules->module_checker(41,'ADD');
			$data['vfile']				= 'iLikeCampaignRulesFORM_ADD.php';
		}
		elseif($action=="edit")
		{
			$this->modules->module_checker(41,'EDIT');
			$data['iLikeCampaignRulesID']	= $id;
			$data['vfile']				= 'iLikeCampaignRulesFORM.php';
		}
		elseif($action=="update")
		{
			$this->modules->module_checker(41,'EDIT');
			$dbFields['countryID'] =  $countryID;
			$table="iLikeCampaignRules";
			
			//INSERT FIELD
			if(isset($POSMTypeID)){
				$dbFields['fieldName'] 		= 'POSMTypeID';
				$dbFields['fieldID']   		=  $POSMTypeID;
				$dbFields['rel']   	   		=  $POSMTypeCond;
				$dbFields['val']   	   		=  $POSMTypeVal;
				$dbFields['dateLastEdited'] =  date('Y-m-d');
				$this->c3model->c3crud("update",$table,$dbFields,$id);
			}
			
			if(isset($POSMStatusID)){
				$dbFields['fieldName'] = 'POSMStatusID';
				$dbFields['fieldID']   =  $POSMStatusID;
				$dbFields['rel']   	   =  $POSMStatusCond;
				$dbFields['val']   	   =  $POSMStatusVal;
				$dbFields['dateLastEdited'] =  date('Y-m-d');
				$this->c3model->c3crud("update",$table,$dbFields,$id);
			}
			
			if(isset($OUTLETStatusID)){
				$dbFields['fieldName'] = 'OUTLETStatusID';
				$dbFields['fieldID']   =  $OUTLETStatusID;
				$dbFields['rel']   	   =  $OUTLETStatusCond;
				$dbFields['val']   	   =  $OUTLETStatusVal;
				$dbFields['dateLastEdited'] =  date('Y-m-d');
				$this->c3model->c3crud("update",$table,$dbFields,$id);
			}
			
			if(isset($PremiumTypeID)){
				$dbFields['fieldName'] = 'PremiumTypeID';
				$dbFields['fieldID']   =  $PremiumTypeID;
				$dbFields['rel']   	   =  $PremiumTypeCond;
				$dbFields['val']   	   =  $PremiumTypeVal;
				$dbFields['dateLastEdited'] =  date('Y-m-d');
				$this->c3model->c3crud("update",$table,$dbFields,$id);
			}
			
			if(isset($MaterialTypeID)){
				$dbFields['fieldName'] = 'MaterialTypeID';
				$dbFields['fieldID']   =  $MaterialTypeID;
				$dbFields['rel']   	   =  $MaterialTypeCond;
				$dbFields['val']   	   =  $MaterialTypeVal;
				$dbFields['dateLastEdited'] =  date('Y-m-d');
				$this->c3model->c3crud("update",$table,$dbFields,$id);
			}
			
			if(isset($brandID)){
				$dbFields['fieldName'] = 'brandID';
				$dbFields['fieldID']   =  $brandID;
				$dbFields['rel']   	   =  $brandCond;
				$dbFields['val']   	   =  $brandVal;
				$dbFields['dateLastEdited'] =  date('Y-m-d');
				$this->c3model->c3crud("update",$table,$dbFields,$id);
			}
			
			//LOGS
			$CI->rec_logs->w($id,'iLike Campaign Rules','iLike','iLike Campaign Rules','edit');
			
			//MSG
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'iLike Campaign Rules has been updated.');
		}

		//STATUS LISTS
		$sql 	= $this->db->query("SELECT *, iLikeCampaignRules.id as iLikeCampaignRulesID  FROM iLikeCampaignRules 
									LEFT JOIN country ON country.id = iLikeCampaignRules.countryID 
								    $filter_WHERE  
									ORDER BY country.id DESC");
		$data['iLikeCampaignRules'] = $sql->result_array();
	
	    $this->load->view('innerPages',$data); 
	}
	
	function iWantCampaignRules($action='',$id='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		$filter_WHERE = $this->modules->country();
		$this->modules->module_checker(42,'REVIEW');
		
		$data['vfile']				= 'iWantCampaignRules.php';
	    $data['title']				= 'iWant Campaign Rules | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//CRUD
		$data['ADD'] 	=  $this->modules->crud_checker(42,'ADD');
		$data['EDIT'] 	=  $this->modules->crud_checker(42,'EDIT');
		$data['DELETE'] =  $this->modules->crud_checker(42,'DELETE');
		
		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
	    $data['breadCrumbs']		= '<a href='.$HTTP_PATH.'users> Users </a>';
	    $data['breadCrumbs']	   .= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']	   .= '<a href='.$HTTP_PATH.'users/iWantCampaignRules> iWant Campaign Rules </a>';
	    
		
		//CSRF
		$data['csrf'] = "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>"; 
		

		extract($_POST);
	    if($action=="insert")
		{
			$this->modules->module_checker(42,'ADD');
			$dbFields['countryID'] =  $countryID;
			
			//INSERT FIELD
			if($POSMTypeID!=NULL){
				$dbFields['fieldName'] = 'POSMTypeID';
				$dbFields['fieldID']   =  $POSMTypeID;
				$dbFields['rel']   	   =  $POSMTypeCond;
				$dbFields['val']   	   =  $POSMTypeVal;
				
				//DO NOT INSER IF ALREADY Exist
				//if($this->fieldChecker('POSMTypeID',$POSMTypeID,1))
					$res = $this->c3model->c3crud("insert",'iWantCampaignRules',$dbFields,'');
			}
			
			if($POSMStatusID!=NULL){
				$dbFields['fieldName'] = 'POSMStatusID';
				$dbFields['fieldID']   =  $POSMStatusID;
				$dbFields['rel']   	   =  $POSMStatusCond;
				$dbFields['val']   	   =  $POSMStatusVal;
				
				//DO NOT INSER IF ALREADY Exist
				//if($this->fieldChecker('POSMStatusID',$POSMStatusID,1))
					$res = $this->c3model->c3crud("insert",'iWantCampaignRules',$dbFields,'');
			}
			
			if($OUTLETStatusID!=NULL){
				$dbFields['fieldName'] = 'OUTLETStatusID';
				$dbFields['fieldID']   =  $OUTLETStatusID;
				$dbFields['rel']   	   =  $OUTLETStatusCond;
				$dbFields['val']   	   =  $OUTLETStatusVal;
				
				//DO NOT INSER IF ALREADY Exist
				//if($this->fieldChecker('OUTLETStatusID',$OUTLETStatusID,1))
					$res = $this->c3model->c3crud("insert",'iWantCampaignRules',$dbFields,'');
			}
			
			if($PremiumTypeID!=NULL){
				$dbFields['fieldName'] = 'PremiumTypeID';
				$dbFields['fieldID']   =  $PremiumTypeID;
				$dbFields['rel']   	   =  $PremiumTypeCond;
				$dbFields['val']   	   =  $PremiumTypeVal;
				
				//DO NOT INSER IF ALREADY Exist
				//if($this->fieldChecker('PremiumTypeID',$PremiumTypeID,1))
					$res = $this->c3model->c3crud("insert",'iWantCampaignRules',$dbFields,'');
			}
			
			if($MaterialTypeID!=NULL){
				$dbFields['fieldName'] = 'MaterialTypeID';
				$dbFields['fieldID']   =  $MaterialTypeID;
				$dbFields['rel']   	   =  $MaterialTypeCond;
				$dbFields['val']   	   =  $MaterialTypeVal;
				
				//DO NOT INSER IF ALREADY Exist
				//if($this->fieldChecker('MaterialTypeID',$MaterialTypeID,1))
					$res = $this->c3model->c3crud("insert",'iWantCampaignRules',$dbFields,'');
			}
			
			if($brandID!=NULL){
				$dbFields['fieldName'] = 'brandID';
				$dbFields['fieldID']   =  $brandID;
				$dbFields['rel']   	   =  $brandCond;
				$dbFields['val']   	   =  $brandVal;
				
				//DO NOT INSER IF ALREADY Exist
				//if($this->fieldChecker('brandID',$brandID,1))
					$res = $this->c3model->c3crud("insert",'iWantCampaignRules',$dbFields,'');
			}
			
			//LOGS
			$sql		= "select max(id) as max_id FROM iWantCampaignRules";
			$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
			$maxID 		= $lastID[0]['max_id'];
			$CI->rec_logs->w($maxID,'iWant Campaign Rules','iWant','iWant Rules','add');
			
			//MSG
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'iWant Campaign Rules has been save.');
		}
		elseif($action=="deleteOneItem")
		{
			$this->modules->module_checker(42,'DELETE');
			
			$CI->rec_logs->w($id,'iWant Campaign Rules','iWant','iWant Rules','delete');
			
			$this->c3model->c3crud("no-res",'','','',"DELETE FROM iWantCampaignRules WHERE id = '$id'");
			$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'iWant Campaign Rule has been deleted.');
			
		}
		elseif($action=="add")
		{
			$this->modules->module_checker(42,'ADD');
			$data['vfile']				= 'iWantCampaignRulesFORM.php';
		}


		//STATUS LISTS
		$sql 	= $this->db->query("SELECT *, iWantCampaignRules.id as iWantCampaignRulesID  FROM iWantCampaignRules 
									LEFT JOIN country ON country.id = iWantCampaignRules.countryID 
								    $filter_WHERE  
									ORDER BY country.id DESC");
		$data['iWantCampaignRules'] = $sql->result_array();
	
	    $this->load->view('innerPages',$data); 
	}
	
	function iWantVotingRules($action='',$id='',$POSM_TypeID='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		$filter_WHERE = $this->modules->country();
		$this->modules->module_checker(72,'REVIEW');
		
		$data['vfile']				= 'iWantVotingRules.php';
	    $data['title']				= 'iLike Campaign Rules | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//CRUD
		$data['ADD'] 	=  $this->modules->crud_checker(72,'ADD');
		$data['EDIT'] 	=  $this->modules->crud_checker(72,'EDIT');
		$data['DELETE'] =  $this->modules->crud_checker(72,'DELETE');
		
		$data['USER_MANUAL'] = $this->modules->user_manual(35);
		
		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
	    $data['breadCrumbs']		= '<a href='.$HTTP_PATH.'users> Users </a>';
	    $data['breadCrumbs']	   .= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']	   .= '<a href='.$HTTP_PATH.'users/iWantVotingRules> iWant Voting Rules </a>';

		//CSRF
		$data['csrf'] = "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>"; 
		
		//TOTAL NUMBER OF ROWS
		$data['active_page']=1;
		$sql = $this->db->query("SELECT country.id as cID, countryName FROM country WHERE country.id = 0");
		$sql = $sql->result_array();
		$total_rec = count($sql);

		$pagenum = 1;
		$data['page_rows'] = 2; 
		$data['last'] = ceil($total_rec/$data['page_rows']);		
		$max = 'limit ' .($pagenum - 1) * $data['page_rows'] .',' .$data['page_rows'];
		
		extract($_POST);
	    if($action=="insert")
		{
			$error=0;
			$this->modules->module_checker(72,'ADD');
			$dbFields['countryID'] =  $countryID;
			
			//INSERT FIELD
			if($POSMTypeID!=NULL){
				$dbFields['price_rangeID'] 	    =  $price_rangeID;
				$dbFields['fieldName'] 		    =  'POSMTypeID';
				$dbFields['fieldID']   		    =  $POSMTypeID;
				$dbFields['cond1']   	  		=  $cond1;
				$dbFields['min_val']   	  		=  $min_val;
				$dbFields['logical_operator']   =  $logical_operator;
				$dbFields['cond2']   			=  $cond2;
				$dbFields['max_val']   			=  $max_val;
				$dbFields['dateAdded'] 		    =  date('Y-m-d');
				//DO NOT INSER IF ALREADY Exist
				if($this->validateInput($min_val)==0 AND $price_rangeID!="")
					$res = $this->c3model->c3crud("insert",'iWantVotingRules',$dbFields,'');
				else
				 $error++;
			}

			//LOGS
			$sql		= "select max(id) as max_id FROM iWantVotingRules";
			$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
			$maxID 		= $lastID[0]['max_id'];
			$CI->rec_logs->w($maxID,'iWant Voting Rules','iWant','iWant Voting Rules','add');
			
			//MSG
			if($error==0){
			 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'iWant Voting Rules has been save.');
			}else{
			 $data['msg'] 	= array('msg_type'=>'alert-warning','msg_desc'=>'iWant Voting Rules is invalid');
			 $data['vfile']		   = 'iWantVotingRulesFORM.php';
			 $data['POST'] 		   = $_POST;
			}
		}
		elseif($action=="deleteOneItem")
		{
			$this->modules->module_checker(72,'DELETE');
			
			$CI->rec_logs->w($id,'iWant Voting Rules','iWant','iWant Rules','delete');
			
			$this->c3model->c3crud("no-res",'','','',"DELETE FROM iWantVotingRules WHERE id = '$id'");
			$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'iWant Voting Rule has been deleted.');
			
		}
		elseif($action=="add")
		{
			$this->modules->module_checker(72,'ADD');
			$data['vfile']			= 'iWantVotingRulesFORM.php';
			$data['countryID']   	= $id;
			$data['POSMTypeID']     = $POSM_TypeID;
		}
		elseif($action=="edit")
		{
			$this->modules->module_checker(72,'EDIT');
			$data['iWantVotingRulesID']		= $id;
			$data['vfile']					= 'iWantVotingRulesFORM.php';
		}
		elseif($action=="update")
		{
		    $error=0;
			$this->modules->module_checker(72,'EDIT');
			$dbFields['countryID'] =  $countryID;
			$table="iWantVotingRules";
			
			if($POSMTypeID!=NULL){
				$dbFields['price_rangeID'] 	    =  $price_rangeID;
				$dbFields['fieldName'] 		    =  'POSMTypeID';
				$dbFields['fieldID']   		    =  $POSMTypeID;
				$dbFields['cond1']   	  		=  $cond1;
				$dbFields['min_val']   	  		=  $min_val;
				$dbFields['logical_operator']   =  $logical_operator;
				$dbFields['cond2']   			=  $cond2;
				$dbFields['max_val']   			=  $max_val;
				$dbFields['dateLastEdited'] 	=  date('Y-m-d');
				//DO NOT INSER IF ALREADY Exist
				if($this->validateInput($min_val)==0 AND $price_rangeID!="")
					$this->c3model->c3crud("update",$table,$dbFields,$id);
				else
				 $error++;
			}
			
			//LOGS
			$CI->rec_logs->w($id,'iWant Voting Rules','iWant','iWant Voting Rules','edit');
			
			if($error==0){
			 $data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'iWant Voting Rules has been updated.');
			}else{
			 $data['msg'] 	= array('msg_type'=>'alert-warning','msg_desc'=>'iWant Voting Rules is invalid, please try again.');
			 $data['vfile']	= 'iWantVotingRulesFORM.php';
			 $data['POST'] 		   = $_POST;
			 $data['iWantVotingRulesID']		= $id;
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
		$sql 	= $this->db->query("SELECT country.id as cID, countryName FROM country WHERE country.id = 0 ORDER BY countryName ASC $max");
		$data['countries'] = $sql->result_array();
		
		$sql 			   = $this->db->query("SELECT POSM_Type.id as pID, typeName FROM POSM_Type ORDER BY typeName ASC");
		$data['POSM_Type'] = $sql->result_array();
		
	    $this->load->view('innerPages',$data); 
	}

	function fieldChecker($fieldName,$fieldID,$table)
	{
		if($table==1)
			$table = 'iLikeCampaignRules';
		else
			$table = 'iWantVotingRules';
			
		$sql = "select id from $table where fieldName='$fieldName' AND fieldID = $fieldID";
		$q = $this->db->query($sql);
		$row = $q->row();
		
		if($row)
			return false;
		else
			return true;
		
	}
	
	function ec_item_fields($action='',$id='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		
		$this->modules->module_checker(45,'REVIEW');
		
		$table= "ec_item_fields";
		$data['vfile']				= 'ec_item_fields.php';
	    $data['title']				= 'eC-Item Fields | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//CRUD
		$data['ADD'] 	=  $this->modules->crud_checker(45,'ADD');
		$data['EDIT'] 	=  $this->modules->crud_checker(45,'EDIT');
		$data['DELETE'] =  $this->modules->crud_checker(45,'DELETE');
		
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(27);
		
		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
	    $data['breadCrumbs']		= '<a href='.$HTTP_PATH.'users> Users </a>';
	    $data['breadCrumbs']	   .= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']	   .= '<a href='.$HTTP_PATH.'users/ec_item_fields/edit> eCatague Item Fields </a>';
	    
		
		//CSRF
		$data['csrf'] = "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>"; 
		
		
		extract($_POST);

		if($action=="edit")
		{
			$this->modules->module_checker(45,'EDIT');
			$data['vfile']				= 'ec_item_fields.php';
			$data['id'] = $id;
		}
		elseif($action=="update")
		{
			$this->modules->module_checker(45,'EDIT');
			$this->c3model->c3crud("no-res",'','','',"UPDATE ec_item_fields SET active = 0");
			
			foreach($fields as $f => $value)
			{
				$refFields['active'] = 1;
				$this->c3model->c3crud("update","ec_item_fields",$refFields,$value);
			}
			
			$CI->rec_logs->w(1,'eCatague Item Fields','Admin','eCatague Item Fields','edit');
			
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>' eCatalogue Field has been update.');
		}

		
		$sql 	= $this->db->query("SELECT * FROM ec_item_fields");
		$data['ec_item_fields'] = $sql->result_array();
	
	    $this->load->view('innerPages',$data); 
	}

	function Item_Database_Type_Other_fields($action='',$id='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		
		$this->modules->module_checker(50,'REVIEW');
		
		$table= "itemType_POSM_table_fields";
		$data['vfile']				= 'Item_Database_Type_Other_fields.php';
	    $data['title']				= 'Item Database: Item Type - Other Fields | San Miguel Brewing International';
	    
		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
	    $data['breadCrumbs']			 = '<a href='.$HTTP_PATH.'users> Users </a> ';
		$data['breadCrumbs']	   		.= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']			.= '<a href='.$HTTP_PATH.'users/Item_Database_Type_Other_fields>  Item Database: Item Type - Other Fields  </a>';
	    
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(28);
		
		//CRUD
		$data['ADD'] 	=  $this->modules->crud_checker(50,'ADD');
		$data['EDIT'] 	=  $this->modules->crud_checker(50,'EDIT');
		$data['DELETE'] =  $this->modules->crud_checker(50,'DELETE');
		
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
			$this->modules->module_checker(50,'ADD');
			
			//INSERT FIELD
			$dbFields['POSM_TypeID'] 		= $POSM_TypeID;
			$dbFields['table_fieldsID'] 	= $table_fieldsID;
			$dbFields['dateAdded'] 			= date('Y-m-d');
			
			//check if already exist
			$sql = "SELECT id FROM itemType_POSM_table_fields WHERE POSM_TypeID = $POSM_TypeID AND table_fieldsID=$table_fieldsID";
			$sql = $this->db->query($sql);
			$a = $sql->result_array();
			
			if(empty($a))
			{
				$res = $this->c3model->c3crud("insert",$table,$dbFields,'');
				//MSG
				$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Item Type - Other fields link has been save.');
				
				$sql = "SELECT typeName FROM POSM_Type WHERE id = $POSM_TypeID";
				$sql = $this->db->query($sql);
				$t   = $sql->row();
				
				$sql = "SELECT fieldName FROM POSM_fields WHERE id = $table_fieldsID";
				$sql = $this->db->query($sql);
				$p   = $sql->row();
				
				//LOGS
				$sql		= "select max(id) as max_id FROM $table";
				$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
				$maxID 		= $lastID[0]['max_id'];
				$CI->rec_logs->w($maxID, $t->typeName ."-". $p->fieldName ,'Admin','Item Type - Other fields link','add');
			}else{
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Item Type - Other fields link has already exist.');
			}
		}
		elseif($action=="deleteOneItem")
		{
			$this->modules->module_checker(50,'DELETE');
			
			$tables = array(
					  array('tbl'=>'items',
							'fld'=>'POSMTypeID'));
			
			$sql = "SELECT POSM_TypeID, table_fieldsID FROM itemType_POSM_table_fields WHERE id = $id";
			$sql = $this->db->query($sql);
			$x   = $sql->row();				
							
			if($this->modules->attr($tables,$x->POSM_TypeID)==0)
			{
				$sql = "SELECT typeName FROM POSM_Type WHERE id = ". $x->POSM_TypeID;
				$sql = $this->db->query($sql);
				$t   = $sql->row();
				
				$sql = "SELECT fieldName FROM POSM_fields WHERE id = ". $x->table_fieldsID;
				$sql = $this->db->query($sql);
				$p   = $sql->row();
				
				
				$sql = "SELECT profile_name FROM user_profile WHERE id = $id";
				$sql = $this->db->query($sql);
				$sql = $sql->row();
				$CI->rec_logs->w($id, $t->typeName ."-". $p->fieldName ,'Admin','Item Type - Other fields link','delete');
				
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Item Type - Other fields link has been deleted.');
				$this->c3model->c3crud('delete',$table,'',$id,'');
				$this->c3model->c3crud("no-res",'','','',"DELETE FROM itemType_POSM_table_fields WHERE id='$id'");
			}else{
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Item Type cannot be delete, because it is being use in Item Database.');
			}
		}
		
		$result = $this->db->query("SELECT * FROM POSM_Type");
		$data['POSM_Status'] = $result->result_array();
		
		$result = $this->db->query("SELECT * FROM POSM_fields WHERE id = 6 OR id = 7");
		$data['POSM_fields'] = $result->result_array();
		
		$sql = "SELECT itemType_POSM_table_fields.id as iID, typeName, fieldName 
				FROM itemType_POSM_table_fields, POSM_fields, POSM_Type 
				WHERE POSM_TypeID = POSM_Type.id AND 
				POSM_fields.id = itemType_POSM_table_fields.table_fieldsID";
				
		$result = $this->db->query($sql);
		$data['itemType_POSM_table_fields'] = $result->result_array();	
	
	    $this->load->view('innerPages',$data); 
	}
	
	function featured_items($action='',$id='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		
		$this->modules->module_checker(52,'REVIEW');
		
		$table= "featured_items";
		$data['vfile']				= 'featured_items.php';
	    $data['title']				= 'Featured Items | San Miguel Brewing International';
	    
		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
	    $data['breadCrumbs']			 = '<a href='.$HTTP_PATH.'users> Users </a> ';
		$data['breadCrumbs']	   		.= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']			.= '<a href='.$HTTP_PATH.'users/featured_items>  Featured Items Setting </a>';
	    
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(32);
		
		//CRUD
		$data['ADD'] 	=  $this->modules->crud_checker(52,'ADD');
		$data['EDIT'] 	=  $this->modules->crud_checker(52,'EDIT');
		$data['DELETE'] =  $this->modules->crud_checker(52,'DELETE');
		
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
			$this->modules->module_checker(52,'ADD');
			
			//INSERT FIELD
			$dbFields['item_typeID'] 		= $item_typeID;
			$dbFields['position'] 		    = $position;
			$dbFields['dateAdded'] 			= date('Y-m-d');
			
			//check if already exist
			$sql = "SELECT id FROM $table WHERE item_typeID = $item_typeID AND position = '$position'";
			$sql = $this->db->query($sql);
			$a = $sql->result_array();
			
			if(empty($a))
			{
				$res = $this->c3model->c3crud("insert",$table,$dbFields,'');
				//MSG
				$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Featured Items has been saved.');
				
				$sql = "SELECT typeName FROM POSM_Type WHERE id = $item_typeID";
				$sql = $this->db->query($sql);
				$t   = $sql->row();
				
				
				//LOGS
				$sql		= "select max(id) as max_id FROM $table";
				$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
				$maxID 		= $lastID[0]['max_id'];
				$CI->rec_logs->w($maxID, $t->typeName ."-". $position ,'Admin','Featured Items','add');
			}else{
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Featured Item is already exist.');
			}
		}
		elseif($action=="deleteOneItem")
		{
			$this->modules->module_checker(52,'DELETE');
			
			$sql = "SELECT item_typeID, position FROM $table WHERE id = $id";
			$sql = $this->db->query($sql);
			$x   = $sql->row();
			
			$sql = "SELECT typeName FROM POSM_Type WHERE id = ". $x->item_typeID;
			$sql = $this->db->query($sql);
			$t   = $sql->row();
			
			$sql = "SELECT profile_name FROM user_profile WHERE id = $id";
			$sql = $this->db->query($sql);
			$sql = $sql->row();
			$CI->rec_logs->w($id, $x->item_typeID ."-". $x->position ,'Admin','Featured Items','delete');
			
			$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Featured Item has been deleted.');
			$this->c3model->c3crud('delete',$table,'',$id,'');
		}
		
		$result = $this->db->query("SELECT * FROM POSM_Type");
		$data['POSM_Status'] = $result->result_array();
		
		
		$sql = "SELECT $table.id as iID, POSM_Type.typeName as pName, position 
				FROM $table, POSM_Type 
				WHERE featured_items.item_typeID = POSM_Type.id";
				
		$result = $this->db->query($sql);
		$data['featured_items'] = $result->result_array();	
	
	    $this->load->view('innerPages',$data); 
	}
	
	function eCatalogue_Type_Other_fields($action='',$id='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		
		$this->modules->module_checker(51,'REVIEW');
		
		$table= "ecitemType_POSM_table_fields";
		$data['vfile']				= 'eCatalogue_Type_Other_fields.php';
	    $data['title']				= 'eCatague: Item Type - Other Fields | San Miguel Brewing International';
	    
		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
	    $data['breadCrumbs']			 = '<a href='.$HTTP_PATH.'users> Users </a> ';
		$data['breadCrumbs']	   		.= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']			.= '<a href='.$HTTP_PATH.'users/Item_Database_Type_Other_fields>  eCatalogue: Item Type - Other Fields  </a>';
	    
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(29);
		
		//CRUD
		$data['ADD'] 	=  $this->modules->crud_checker(51,'ADD');
		$data['EDIT'] 	=  $this->modules->crud_checker(51,'EDIT');
		$data['DELETE'] =  $this->modules->crud_checker(51,'DELETE');
		
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
			$this->modules->module_checker(51,'ADD');
			
			//INSERT FIELD
			$dbFields['POSM_TypeID'] 		= $POSM_TypeID;
			$dbFields['table_fieldsID'] 	= $table_fieldsID;
			$dbFields['dateAdded'] 			= date('Y-m-d');
			
			//check if already exist
			$sql = "SELECT id FROM ecitemType_POSM_table_fields WHERE POSM_TypeID = $POSM_TypeID AND table_fieldsID=$table_fieldsID";
			$sql = $this->db->query($sql);
			$a = $sql->result_array();
			
			if(empty($a))
			{
				$res = $this->c3model->c3crud("insert",$table,$dbFields,'');
				//MSG
				$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'eC Item Type - Other fields link has been save.');
				
				$sql = "SELECT typeName FROM POSM_Type WHERE id = $POSM_TypeID";
				$sql = $this->db->query($sql);
				$t   = $sql->row();
				
				$sql = "SELECT fieldName FROM POSM_fields WHERE id = $table_fieldsID";
				$sql = $this->db->query($sql);
				$p   = $sql->row();
				
				//LOGS
				$sql		= "select max(id) as max_id FROM $table";
				$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
				$maxID 		= $lastID[0]['max_id'];
				$CI->rec_logs->w($maxID, $t->typeName ."-". $p->fieldName ,'Admin','eC Item Type - Other fields link','add');
			}else{
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'eC Item Type - Other fields link has already exist.');
			}
		}
		elseif($action=="deleteOneItem")
		{
			$this->modules->module_checker(51,'DELETE');
			
			$sql = "SELECT POSM_TypeID, table_fieldsID FROM ecitemType_POSM_table_fields WHERE id = $id";
			$sql = $this->db->query($sql);
			$x   = $sql->row();
			
			$tables = array(
					  array('tbl'=>'ec_items',
							'fld'=>'POSMTypeID'));
			
			if($this->modules->attr($tables,$x->POSM_TypeID)==0)
			{
				$sql = "SELECT typeName FROM POSM_Type WHERE id = ". $x->POSM_TypeID;
				$sql = $this->db->query($sql);
				$t   = $sql->row();
				
				$sql = "SELECT fieldName FROM POSM_fields WHERE id = ". $x->table_fieldsID;
				$sql = $this->db->query($sql);
				$p   = $sql->row();
				
				
				$sql = "SELECT profile_name FROM user_profile WHERE id = $id";
				$sql = $this->db->query($sql);
				$sql = $sql->row();
				$CI->rec_logs->w($id, $t->typeName ."-". $p->fieldName ,'Admin','eC Item Type - Other fields link','delete');
				
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'eC Item Type - Other fields link has been deleted.');
				$this->c3model->c3crud('delete',$table,'',$id,'');
				$this->c3model->c3crud("no-res",'','','',"DELETE FROM ecitemType_POSM_table_fields WHERE id='$id'");
			}else{
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Item Type cannot be delete, because it is being use in eCatalogue Item Database.');
			}
		}
		
		$result = $this->db->query("SELECT * FROM POSM_Type");
		$data['POSM_Status'] = $result->result_array();
		
		$result = $this->db->query("SELECT * FROM POSM_fields WHERE id = 6 OR id = 7");
		$data['POSM_fields'] = $result->result_array();
		
		$sql = "SELECT ecitemType_POSM_table_fields.id as iID, typeName, fieldName 
				FROM ecitemType_POSM_table_fields, POSM_fields, POSM_Type 
				WHERE POSM_TypeID = POSM_Type.id AND 
				POSM_fields.id = ecitemType_POSM_table_fields.table_fieldsID";
				
		$result = $this->db->query($sql);
		$data['ecitemType_POSM_table_fields'] = $result->result_array();	
	
	    $this->load->view('innerPages',$data); 
	}
	
	function archive_filtering($action='',$id='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		
		$this->modules->module_checker(54,'REVIEW');
		
		$table= "POSM_status_fields";
		$data['vfile']				= 'archive_filtering.php';
	    $data['title']				= 'Items Filtering | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//CRUD
		$data['EDIT'] 	=  $this->modules->crud_checker(54,'EDIT');
		
		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
	    $data['breadCrumbs']		= '<a href='.$HTTP_PATH.'users> Users </a>';
	    $data['breadCrumbs']	   .= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']	   .= '<a href='.$HTTP_PATH.'users/archive_filtering> Archive Filtering </a>';
	    
		
		//CSRF
		$data['csrf'] = "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>"; 
		
		
		extract($_POST);
	    if($action=="update")
		{	
			if($tyear==0 AND $tmonth==0){
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Invalid Items Filtering value.');
			}else{
				$dbFields['tyear']  	= $tyear;
				$dbFields['tmonth'] 	= $tmonth;
				//$dbFields['dateFrom'] 	= $dateFrom;
				//$dbFields['dateTo']	  	= $dateTo;
				
				$dbFields['defaultRange'] = ($default=='defaultRange') ? 1 :0;
				$dbFields['defaultDate']  = ($default=='defaultDate')  ? 1 :0;
				$this->c3model->c3crud("update","archive_filtering",$dbFields,$id);
			
				$CI->rec_logs->w(1,'Year: '.$tyear.'-Month: '.$tmonth ,'Admin','Items Filtering','edit');
				$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Items Filtering has been updated.');
			}
		}

		//STATUS LISTS
		$sql 					 = $this->db->query("SELECT * FROM archive_filtering");
		$data['archive_filtering'] = $sql->result_array();
	    $this->load->view('innerPages',$data); 
	}
	
	function filter($db,$tbl,$id)
	{
		$sql 			= $this->db->query("SELECT startDate, endDate FROM archive_list WHERE id=$id LIMIT 0,1");
		$r 				= $sql->row();
		return $filter	= "$db.$tbl.dateAdded <= '".$r->startDate ."' AND $db.$tbl.dateAdded >= '".$r->endDate ."'"; 
	}

	function archive_list($action='',$id='')
	{
		$SMBi_Archive = "SMBi_Archive";
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		
		$this->modules->module_checker(55,'REVIEW');
		
		$table= "POSM_status_fields";
		$data['vfile']				= 'archive_list.php';
	    $data['title']				= 'Archive List | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food, beverage, packaging, Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//CRUD
		$data['EDIT'] 	=  $this->modules->crud_checker(55,'EDIT');
		
		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
	    $data['breadCrumbs']		= '<a href='.$HTTP_PATH.'users> Users </a>';
	    $data['breadCrumbs']	   .= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']	   .= '<a href='.$HTTP_PATH.'users/archive_list> Archive List </a>';
	   	
		//CSRF
		$data['csrf'] = "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>"; 
	
		
		
		//STATUS LISTS
		$sql 					   = $this->db->query("SELECT * FROM archive_list");
		$data['archive_list'] 	   = $sql->result_array();
	    $this->load->view('innerPages',$data); 
	}
	
	function archive_details($action='',$id='')
	{
		extract($_POST);
		$SMBi_DEV = "SMBi_DEV";
		$SMBi_Archive = "SMBi_Archive";
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	
		$this->modules->module_checker(55,'REVIEW');
		
		$table= "POSM_status_fields";
	    $data['title']				= 'Archive List | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food, beverage, packaging, Corporation, San Miguel,company , Philippines ,Southeast Asia';
		$data['id']					= $id;
		
		//CRUD
		$data['EDIT'] 	=  $this->modules->crud_checker(55,'EDIT');
		
		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
	    $data['breadCrumbs']		= '<a href='.$HTTP_PATH.'users> Users </a>';
	    $data['breadCrumbs']	   .= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']	   .= '<a href='.$HTTP_PATH.'users/archive_list> Archive List </a>';
	   	
		//CSRF
		$data['csrf'] = "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>"; 
		
		extract($_POST);
		if($action=='archive_catalogue'){
			
			$data['vfile'] = 'archive_catalogue.php';
			if(isset($eCat_IDs)){
				foreach($eCat_IDs as $val => $IDs)
				{
				//ITEM 
				$ITEMS = "INSERT INTO $SMBi_DEV.ec_items 
				(id, itemCode, POSMTypeID, POSMStatusID, OUTLETStatusID, PremiumTypeID, MaterialTypeID, 
				ecID, countryID, brandID, publish_other_country, itemName, Photo, publish, Short_Description, Long_Description, UnitPrice, USD_Price,
				MOQ, UOM, country_of_origin, dateAdded, dateReleased, DateLastEdited, user_id, Fields0001, Fields0002, Fields0003, Fields0004, Fields0005, estimated_production_lead_time, 	price_validity,
				plant_inventory, supplier_stock_on_hand, date_first_issue, date_last_used, activity_event_use, num_views)";
				
				$ITEMS_SEL = "SELECT id, itemCode, POSMTypeID, POSMStatusID, OUTLETStatusID, PremiumTypeID, MaterialTypeID, 
				ecID, countryID, brandID, publish_other_country, itemName, Photo, publish, Short_Description, Long_Description, UnitPrice, USD_Price,
				MOQ, UOM, country_of_origin, CURDATE(), dateReleased, DateLastEdited, user_id, Fields0001, Fields0002, Fields0003, Fields0004, Fields0005, estimated_production_lead_time, 	price_validity,
				plant_inventory, supplier_stock_on_hand, date_first_issue, date_last_used, activity_event_use, num_views 
				FROM $SMBi_Archive.ec_items WHERE $SMBi_Archive.ec_items.id = $IDs"; 	
				$this->db->query($ITEMS.$ITEMS_SEL);
				
				//IMAGES
				$ITEMS_IMAGES = "INSERT INTO $SMBi_DEV.ecitems_images 
				(id, itemID, image, defaultStatus)"; 
				
				$ITEMS_IMAGES_SEL = "SELECT id, itemID, image, defaultStatus
				FROM $SMBi_Archive.ecitems_images WHERE itemID = $IDs";
				$this->db->query($ITEMS_IMAGES.$ITEMS_IMAGES_SEL);
				
				//DELETE IMAGES
				$ITEMS_IMG_DELETE="DELETE FROM $SMBi_Archive.ecitems_images WHERE itemID = $IDs";
				$this->db->query($ITEMS_IMG_DELETE);
				
				//DELETE ITEM
				$ITEMS_IMG_DELETE="DELETE FROM $SMBi_Archive.ec_items WHERE $SMBi_Archive.ec_items.id = $IDs";
				$this->db->query($ITEMS_IMG_DELETE);
				
				//RECORD LOG IN
				$SQL = $this->db->query("SELECT id, itemCode, itemName FROM $SMBi_DEV.ec_items WHERE id= $IDs");
				$row = $SQL->row();
				$CI->rec_logs->w($row->id, $row->itemName, 'eCatagoue Items','Items','restore', $row->itemCode);
				}
				$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Items has been restored!');
			}
			
			/*eCatalogue*/
			$limit =isset($selpage)? $selpage:0;
			$table 	=  "ec_items";
			$sqlSTr =  "SELECT itemCode, $SMBi_Archive.ec_items.id AS ec_itemsID, itemName, Short_Description, $SMBi_Archive.ec_items.publish, $SMBi_Archive.ec_items.dateAdded as tdate, $SMBi_DEV.e_catalog.title as catalogue_title, 
					    (SELECT image FROM  $SMBi_Archive.ecitems_images WHERE defaultStatus = 1 AND itemID = $SMBi_Archive.ec_items.id LIMIT 0,1) AS iImg
						FROM $SMBi_Archive.ec_items
						LEFT JOIN $SMBi_DEV.e_catalog ON  $SMBi_DEV.e_catalog.id = $SMBi_Archive.ec_items.ecID
						WHERE ".$this->filter($SMBi_Archive,$table,$id)." ORDER BY catalogue_title DESC";
					
			
			$sql = $this->db->query($sqlSTr);
			$data['eCatalogue_items']  = $sql->result_array();
			$data['totrec'] = count($data['eCatalogue_items']);
			
			$sql = $this->db->query($sqlSTr." LIMIT $limit,20");
			$data['eCatalogue_items']  = $sql->result_array();
			$data['limit']  = $limit;
		}
		if($action=='archive_item_db'){
			$data['vfile'] = 'archive_item_db.php';
		    
			if(isset($itemDB_IDs)){
				foreach($itemDB_IDs as $val => $IDs)
				{
				//ITEM
				$ITEMS = "INSERT INTO $SMBi_DEV.items 
				(id, itemCode, POSMTypeID, POSMStatusID, 
				OUTLETStatusID, PremiumTypeID, MaterialTypeID, 
				countryID, brandID, publish_other_country, 
				itemName, Photo, publish, 
				irrelevant, Short_Description, Long_Description, 
				UnitPrice, 	USD_Price, MOQ, UOM, 
				country_of_origin, dateAdded, dateReleased, dateLastEdited, user_id,
				estimated_production_lead_time, price_validity,
				Fields0001, Fields0002, Fields0003, Fields0004, Fields0005, 
				plant_inventory, supplier_stock_on_hand, date_first_issue, 
				date_last_used, activity_event_use, num_views)"; 
				
				$ITEMS_SEL = "SELECT 
				id, itemCode, POSMTypeID, POSMStatusID, 
				OUTLETStatusID, PremiumTypeID, MaterialTypeID, 
				countryID, brandID, publish_other_country, 
				itemName, Photo, publish, 
				irrelevant, Short_Description, Long_Description, 
				UnitPrice, USD_Price, MOQ, UOM, 
				country_of_origin, CURDATE(), dateReleased, dateLastEdited, user_id,
				estimated_production_lead_time, price_validity,
				Fields0001, Fields0002, Fields0003, Fields0004, Fields0005, 
				plant_inventory, supplier_stock_on_hand, date_first_issue, 
				date_last_used, activity_event_use, num_views 
				FROM $SMBi_Archive.items WHERE $SMBi_Archive.items.id = $IDs";
				$this->db->query($ITEMS.$ITEMS_SEL);
				
				//IMAGES
				$ITEMS_IMAGES = "INSERT INTO $SMBi_DEV.items_images 
				(id, itemID, image, defaultStatus)"; 
				
				$ITEMS_IMAGES_SEL = "SELECT id, itemID, image, defaultStatus
				FROM $SMBi_Archive.items_images WHERE itemID = $IDs";
	
				$this->db->query($ITEMS_IMAGES.$ITEMS_IMAGES_SEL);
				
				//DELETE IMAGES
				$ITEMS_IMG_DELETE="DELETE FROM $SMBi_Archive.items_images WHERE itemID = $IDs";
				$this->db->query($ITEMS_IMG_DELETE);
				
				//DELETE ITEM
				$ITEMS_IMG_DELETE="DELETE FROM $SMBi_Archive.items WHERE $SMBi_Archive.items.id = $IDs";
				$this->db->query($ITEMS_IMG_DELETE);
				
				//RECORD LOG IN
				$SQL = $this->db->query("SELECT id, itemCode, itemName FROM $SMBi_DEV.items WHERE id= $IDs");
				$row = $SQL->row();
				$CI->rec_logs->w($row->id, $row->itemName, 'Items','Item Database','restore', $row->itemCode);
				
				}
				$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Items has been restored!');
			}
			
			/*Item Database*/
			$limit =isset($selpage)? $selpage:0;
			$table 	= "items";
			$items    = "SELECT *,$SMBi_Archive.$table.id as itemDB_ID, (SELECT image FROM $SMBi_Archive.items_images WHERE itemID = $SMBi_Archive.$table.id AND defaultStatus = 1 LIMIT 0,1) as item_image 
										FROM $SMBi_Archive.$table  
										LEFT JOIN $SMBi_DEV.country ON  $SMBi_DEV.country.id = $SMBi_Archive.items.countryID
										WHERE ".$this->filter($SMBi_Archive,$table,$id)."ORDER BY $SMBi_Archive.$table.countryID DESC";
			
			$sql = $this->db->query($items);
			$data['items']  = $sql->result_array();
			$data['totrec'] = count($data['items']);
			
			$sql = $this->db->query($items." LIMIT $limit,20");
			$data['items']  = $sql->result_array();
			$data['limit']  = $limit;
			/*Item Database*/
		}	
			$sql 					   = $this->db->query("SELECT * FROM archive_list WHERE id=$id LIMIT 0,1");
			$data['archive_info'] 	   = $sql->result_array();
			
		
			$sql 				  = $this->db->query("SELECT * FROM archive_ref WHERE archive_id = $id");
			$archive_ref    	  = $sql->row();
			
			$data['Archive_e_catalog'] 	= FALSE; 
			$data['Archive_items'] 		= FALSE;
			
			if($archive_ref->e_catalog	== 0)
				$data['Archive_e_catalog'] = TRUE; 
			if($archive_ref->items 		== 0)
				$data['Archive_items'] 	   = TRUE;
		
	    $this->load->view('innerPages',$data); 
	}
	
	function showItems($cID)
	{	
		$SMBi_Archive = "SMBi_Archive";
		$sqlSTr =  "SELECT *, $SMBi_Archive.ec_items.id AS iID, itemName, Short_Description, publish, 
					(SELECT image FROM  $SMBi_Archive.ecitems_images WHERE defaultStatus = 1 AND itemID = $SMBi_Archive.ec_items.id LIMIT 0,1) AS iImg
					FROM $SMBi_Archive.ec_items 
					WHERE $SMBi_Archive.ec_items.ecID='$cID' ORDER BY $SMBi_Archive.ec_items.id DESC";
					
		$sql 		= $this->db->query($sqlSTr);
		$items		= $sql->result_array();
				
		
		if($items){
		echo "<table cellpadding='0' cellspacing='0' border=1 style='width:100%;margin: 0px auto;font-size:12px;' class='iLike_Result_Table'>
			 <tr style='border-radius: 6px;'>
				<td style='width:10px;text-align:center;background:rgb(175,  168, 168);color:black;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>No 		   			</b></td> 
				<td style='width:50px;text-align:center;background:rgb(175,  168, 168);color:black;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Item Code 		   	</b></td> 
				<td style='width:30px;text-align:center;background:rgb(175,  168, 168);color:black;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Image  	   			</b></td> 
				<td style='width:130px;text-align:center;background:rgb(175, 168, 168);color:black;font-weight:bold;padding: 2px 2px 2px 5px;'>    <b>Item Name    			</b></td> 
				<td style='width:200px;text-align:center;background:rgb(175,  168, 168);color:black;font-weight:bold;padding: 2px 2px 2px 5px;'>   <b>Short Description  	</b></td> 
				<td style='width:50px;text-align:center;background:rgb(175,  168, 168);color:black;font-weight:bold;padding: 2px 2px 2px 5px;'>   <b>Date Added  	</b></td> 
			</tr>";
		 
			$x = 0;
			$total = 0;
			//print_r($items);
			foreach($items as $i) { 
			extract($i);
			$c = (($x++)%2) == 0 ? "class='alter'" :  ""; 
			$gray = '';
			if($publish != 'y'){
				$gray =  "style='background:#AFAFAF'";
				$c = '';
			}
		 
			echo	"<tr>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>	$x 											  </td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 0px;'>	$itemCode 											  </td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 10px;'>	
						<img src='".HTTP_PATH."img/galleryImg/$iImg' style='height:30px'>";
						if($gray!='')
							echo "<label style='color:red;font-size:10px;margin: 0;'> *Draft </label>";
			echo	 "</td>
					  <td $c  style='text-align:left;padding: 0px 2px 0px 10px;'>	$itemName  									  </td>
					  <td $c  style='text-align:left;padding: 0px 2px 0px 10px;'>	$Short_Description  						  </td>
					  <td $c  style='text-align:center;padding: 0px 2px 0px 10px;'>	$dateAdded  						  </td>
					  ";
			echo	"</tr>";
			}
		echo"</table>";
		}else{
			echo "<label> Sorry, no items found. </label>";
		}
	}
	
	function iLikeReport($id,$aID='')
    {
	$this->archive = $this->load->database('archive',TRUE);
	
    $sql = "SELECT itemID, itm.itemName, itm.Short_Description as sDescription, campaignID,(SELECT COUNT( id ) FROM votexRef AS vref WHERE vref.itemID = itemREF.itemID AND vote =  'yes' and vref.campaignID =$id ) AS voteTot,
		   (SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = itm.id LIMIT 0,1) AS iImg
		   FROM  `campaignItemsXref` AS itemREF LEFT JOIN items AS i ON itemREF.itemID = i.id  left join items as itm on itemREF.itemID=itm.id where   itemREF.campaignID=$id 
           ORDER BY `voteTot` DESC";

	 $report    = $this->archive->query($sql);  
	 $rep       = $report->result_array(); 
 
	 $sql       = "select * from campaign where campaign.id='$id'   ";
	 $header    = $this->archive->query($sql);  
	 $header    = $header->result_array(); 
	 
     $data['vfile']				= 'archive_iLike.php';
	 $data['title']				= 'iLike Report';
	 $data['rep']				= $rep;
	 $data['repHeader']			= $header;
	
	 $HTTP_PATH = HTTP_PATH;
	 $data['breadCrumbs']		= '<a href='.$HTTP_PATH.'users> Users </a>';
	 $data['breadCrumbs']	   .= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	 $data['breadCrumbs']	   .= '<a href='.$HTTP_PATH.'users/archive_list> Archive List </a>';
	 $data['breadCrumbs']	   .= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	 $data['breadCrumbs']	   .= "<a href=''> iLike Report </a>";
	 
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
   
	function iWantReport($id,$aID='')
    {
	 $this->archive = $this->load->database('archive',TRUE);
     $sql = "SELECT itemID,itm.itemName, itm.Short_Description as sDescription, campaignID,(SELECT sum(vote) FROM votexRef AS vref WHERE vref.itemID = itemREF.itemID AND vref.campaignID =$id ) AS voteTot,
		    (SELECT image FROM items_images WHERE defaultStatus = 1 AND itemID = itm.id LIMIT 0,1) AS iImg
			FROM  `campaignItemsXref` AS itemREF LEFT JOIN items AS i ON itemREF.itemID = i.id  left join items as itm on itemREF.itemID=itm.id where   itemREF.campaignID=$id 
            ORDER BY `voteTot` DESC";
			 
	 $report    = $this->archive->query($sql);  
	 $rep       = $report->result_array(); 
 
	 
	 $sql       = "select * from campaign where campaign.id='$id' ";
	 $header    = $this->archive->query($sql);  
	 $header    = $header->result_array(); 
	 
 
     $data['vfile']			= 'archive_iWant.php';
	 $data['title']			= 'iWant Report';
	 $data['rep']			= $rep;
	 $data['repHeader']		= $header;
	 $HTTP_PATH = HTTP_PATH;
	 $data['breadCrumbs']		= '<a href='.$HTTP_PATH.'users> Users </a>';
	 $data['breadCrumbs']	   .= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	 $data['breadCrumbs']	   .= '<a href='.$HTTP_PATH.'users/archive_list> Archive List </a>';
	 $data['breadCrumbs']	   .= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	 $data['breadCrumbs']	   .= "<a href=''> iWant Report </a>";
	 
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

	function user_manual($action='',$id='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
	
		$this->modules->module_checker(57,'REVIEW');
		
		$table= "user_manual";
		$data['vfile']				= 'user_manual.php';
	    $data['title']				= 'Usewr Manual | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//CRUD
		$data['ADD'] 	=  $this->modules->crud_checker(57,'ADD');
		$data['EDIT'] 	=  $this->modules->crud_checker(57,'EDIT');
		$data['DELETE'] =  $this->modules->crud_checker(57,'DELETE');
		
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(30);
		
		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
	    $data['breadCrumbs']		= '<a href='.$HTTP_PATH.'users> Admin </a>';
	    $data['breadCrumbs']	   .= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']	   .= '<a href='.$HTTP_PATH.'users/user_manual> User Manual </a>';

		//CSRF
		$data['csrf'] = "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>"; 
		
		//PAGINATION
		//PAGINATION
		//TOTAL NUMBER OF ROWS
		$data['active_page']=1;
		$sql = $this->db->query("SELECT id FROM $table");
		$sql = $sql->result_array();
		$total_rec = count($sql);

		extract($_POST);
	    if($action=="edit")
		{
			$this->modules->module_checker(57,'EDIT');
			$data['id'] = $id;
		}
		elseif($action=="update")
		{
			$this->modules->module_checker(57,'EDIT');
			
			$config['upload_path'] = FCPATH2.'/files/user_manual/';
			$config['allowed_types'] = 'gif|jpg|png|pdf';
			$this->upload->initialize($config);
				
			if (!$this->upload->do_upload())
			{
				$error = array('error' => $this->upload->display_errors());
			}	
			else
			{
				$upload_data=$this->upload->data();
				copy($upload_data['full_path'],FCPATH2 .'/files/user_manual/'.$upload_data['file_name']);
				$dbFields['user_manual'] 	= $upload_data['file_name'];
			}
			
			//LOGS
			$CI->rec_logs->w($id,$module_name,'User Manual','User Manual','edit');
			
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'User Manual has been updated.');
			$this->c3model->c3crud("update",$table,$dbFields,$id);
		}
		
		//STATUS LISTS
		$sql 			= $this->db->query("SELECT * FROM $table ORDER BY module_name ASC");
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
	
	function checkNominees($type='',$num_commitee='',$countryID='')
	{
	 $error=0;
	 if($type=='iLike')
	 { $sql = $this->db->query("SELECT MIN(min_val) min_vote FROM iLikeCanvassingRules WHERE countryID = $countryID LIMIT 0,1");
	   $sql = $sql->row();
	   if($num_commitee<$sql->min_vote) $error=1;  
	 }else{
	   $sql = $this->db->query("SELECT MIN(min_val) min_vote FROM iWantCanvassingRules WHERE countryID = $countryID LIMIT 0,1");
	   $sql = $sql->row();
	   if($num_commitee+1<$sql->min_vote) $error=1;  
	 }
	 return $error;
	}
	
	function min_number_commitees($action='',$id='')
    {
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		$filter_WHERE = $this->modules->country();
		$this->modules->module_checker(58,'REVIEW');
		
		$table= "iLikeCampaignNumber_of_commitees";
		$data['vfile']				= 'min_number_commitees.php';
	    $data['title']				= 'Minumum number of Commitees | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//CRUD
		$data['EDIT'] 	=  $this->modules->crud_checker(58,'EDIT');
		$data['DELETE'] 	=  $this->modules->crud_checker(58,'EDIT');
		
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(36);
		
		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
	    $data['breadCrumbs']		= '<a href='.$HTTP_PATH.'users> Users </a>';
	    $data['breadCrumbs']	   .= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']	   .= '<a href='.$HTTP_PATH.'users/min_number_commitees> Minumum Number of Nominees </a>';
	    
		
		//CSRF
		$data['csrf'] = "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>"; 
		

		extract($_POST);
	    if($action=="edit")
		{
			$data['id'] = $id;
		}
		elseif($action=="update")
		{
			$this->modules->module_checker(58,'EDIT');
			//CHECK IF INPUT IS NOT LOWER THAN MINIMUM NUMBER OF VOTES
			if($num_commitee>0 AND $this->checkNominees('iLike',$num_commitee,$countryID)==0)
			{
				$dbFields['num_commitee'] = $num_commitee;
				//LOGS
				$CI->rec_logs->w($id,"iLike Minimum number of commitees - $countryName",'iLike Campaign','Number of committees','edit');
				
				$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Number of committees has been updated.');
				$this->c3model->c3crud("update",$table,$dbFields,$id);
			}else{
				$data['id']	  = $id;
				$data['POST'] = $_POST;
				$data['msg']  = array('msg_type'=>'alert-warning','msg_desc'=>'Minimum number of nominees is not compatible with canvassing rules.');
			}
		}
		elseif($action=="insert")
		{
			$this->modules->module_checker(58,'ADD');
			//INSERT FIELD
			$dbFields['countryID'] = $countryID;
			$dbFields['num_commitee'] = $num_commitee;
			
			//LOGS
			$sql		= "select max(id) as max_id FROM $table";
			$lastID 	= $this->c3model->c3crud("select",'','','',$sql);
			$maxID 		= $lastID[0]['max_id'];
			
			$sql		= "select countryName FROM country WHERE id=$countryID";
			$country 	= $this->c3model->c3crud("select",'','','',$sql);
			$countryName = $country[0]['countryName'];
			
			$CI->rec_logs->w($maxID,$countryName,'iLike','Number of nominees','add');
			
			//MSG
			$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Minimum number of nominee has been save.');
			$res = $this->c3model->c3crud("insert",$table,$dbFields,'');
		}
		elseif($action=="deleteOneItem")
		{
			$this->modules->module_checker(58,'DELETE');

			//LOGS
			$sql	= "select countryName FROM country 
					   left join $table ON $table.countryID = country.id
					   WHERE $table.id=$id";
			$country 	= $this->c3model->c3crud("select",'','','',$sql);
			$countryName = $country[0]['countryName'];
			
			$sql = "DELETE FROM $table WHERE id = $id";
			$sql = $this->db->query($sql);
			
		
			$CI->rec_logs->w($id,$countryName,'iLike','Number of nominees','delete');
			
			$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Number of nominees has been deleted.');
	
		}
		


		//STATUS LISTS
		$sql 	= $this->db->query("SELECT *, iLikeCampaignNumber_of_commitees.id as cID FROM  iLikeCampaignNumber_of_commitees
									LEFT JOIN country ON country.id = iLikeCampaignNumber_of_commitees.countryID 
								    $filter_WHERE");
		$data['iLikeCampaignNumber_of_commitees'] = $sql->result_array();
	
	    $this->load->view('innerPages',$data); 
	}	
	
	function iWant_min_number_commitees($action='',$id='')
    {
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		$filter_WHERE = $this->modules->country();
		$this->modules->module_checker(61,'REVIEW');
		
		$table= "iWantCampaignNumber_of_commitees";
		$data['vfile']				= 'iWant_min_number_commitees.php';
	    $data['title']				= 'iWant Minumum number of Commitees | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//CRUD
		$data['EDIT'] 	=  $this->modules->crud_checker(40,'EDIT');
		
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(33);
		
		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
	    $data['breadCrumbs']		= '<a href='.$HTTP_PATH.'users> Users </a>';
	    $data['breadCrumbs']	   .= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']	   .= '<a href='.$HTTP_PATH.'users/iWant_min_number_commitees> iWant Minumum Number of Nominees </a>';
	    
		
		//CSRF
		$data['csrf'] = "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>"; 
		

		extract($_POST);
	    if($action=="edit")
		{
			$data['id'] = $id;
		}
		elseif($action=="update")
		{
			$this->modules->module_checker(61,'EDIT');
			
			if($num_commitee>0 AND $this->checkNominees('iWant',$num_commitee,0)==0)
			{
				$dbFields['num_commitee'] = $num_commitee;
				//LOGS
				$CI->rec_logs->w($id,"iWant Minimum number of commitees - $countryName",'iWant Campaign','Number of committees','edit');
				
				$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'Number of committees has been updated.');
				$this->c3model->c3crud("update",$table,$dbFields,$id);
			}else{
				$data['id']     = $id;
				$data['POST']	= $_POST;
				$data['msg']    = array('msg_type'=>'alert-warning','msg_desc'=>'Number of screening committees is lower than minimum number of votes in the canvassing rules.');
			}
		}


		//STATUS LISTS
		$sql 	= $this->db->query("SELECT *, iWantCampaignNumber_of_commitees.id as cID FROM  iWantCampaignNumber_of_commitees
									LEFT JOIN country ON country.id = iWantCampaignNumber_of_commitees.countryID 
								    $filter_WHERE");
		$data['iWantCampaignNumber_of_commitees'] = $sql->result_array();
	
	    $this->load->view('innerPages',$data); 
	}	
	
	function iLikeCampaign_min_num_votes($action='',$id='')
    {
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		$filter_WHERE = $this->modules->country();
		$this->modules->module_checker(58,'REVIEW');
		
		$table					    = "iLikeCampaign_min_num_votes";
		$data['vfile']				= 'iLikeCampaign_min_num_votes.php';
	    $data['title']				= 'iLike Campaign Minimum Number of Votes | San Miguel Brewing International';
	    $data['meta_description']	= 'San Miguel Brewing International';
	    $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		//CRUD
		$data['EDIT'] 	=  $this->modules->crud_checker(58,'EDIT');
		
		//USER MANUAL
		$data['USER_MANUAL'] = $this->modules->user_manual(33);
		
		//BREAD CRUMBS
		$HTTP_PATH = HTTP_PATH;
	    $data['breadCrumbs']		= '<a href='.$HTTP_PATH.'users> Users </a>';
	    $data['breadCrumbs']	   .= '<li><img src="'.$HTTP_PATH.'img/arrow.png" width="3" height="5"></li>';
	    $data['breadCrumbs']	   .= '<a href='.$HTTP_PATH.'users/min_number_commitees> iLike Minimum Number of Votes </a>';
	    
		
		//CSRF
		$data['csrf'] = "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>"; 
		

		extract($_POST);
	    if($action=="edit")
		{
			$data['id'] = $id;
		}
		elseif($action=="update")
		{
			$this->modules->module_checker(58,'EDIT');
			
			if($min_num_votes>0)
			{
				$dbFields['min_num_votes'] = $min_num_votes;
				//LOGS
				$CI->rec_logs->w($id,"iLike Minimum number of Votes - $countryName",'iLike Campaign','Min Number of likes','edit');
				
				$data['msg'] = array('msg_type'=>'alert-success','msg_desc'=>'iLike minimum number of likes has been updated.');
				$this->c3model->c3crud("update",$table,$dbFields,$id);
			}else{
				$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Number of screening committees is lower than minimum number of votes in the canvassing rules');
			}
		}

		//STATUS LISTS
		$sql 	= $this->db->query("SELECT *, iLikeCampaign_min_num_votes.id as cID FROM  iLikeCampaign_min_num_votes
									LEFT JOIN country ON country.id = iLikeCampaign_min_num_votes.countryID 
								    $filter_WHERE");
		$data['iLikeCampaign_min_num_votes'] = $sql->result_array();
	
	    $this->load->view('innerPages',$data); 
	}	
}