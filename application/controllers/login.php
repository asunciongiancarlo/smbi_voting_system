<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');  
 
class Login extends CI_Controller {
	
	public function __construct()
    {
	parent::__construct();
	date_default_timezone_set('UTC');
	session_start();
	$this->load->model('c3model');
	$this->output->enable_profiler(FALSE);
    }
	

	public function index()
	{
	   $data['vfile']		= 'login.php';
	   $data['title']		= 'SMBi System Log-in | SMBi';
	   $data['page_title']	= 'SMBi System Log-in';
	   $data['meta_description']	= 'San Miguel Brewing International';
	   $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
	   
	   $this->load->view('login',$data); 
	}
	
	function inactivity()
	{
		//DELETE ALL INACTIVE SESSION
		$sql = "SELECT * FROM active_session";
		$query = $this->db->query($sql);
		$active_sessions = $query->result_array();
		

		if($active_sessions){
			foreach($active_sessions as $a)
			{
				extract($a);	
				//300 = 5 min  
				//(min*60) = timestamp
				if((time() - $timestamp) >= ini_get('session.gc_maxlifetime'))
				{
					$this->db->query("DELETE FROM active_session WHERE user_id = $user_id");
				}
			}
		}
	}
	
	function link_expired()
	{
	   $data['vfile']		= 'login.php';
	   $data['title']		= 'SMBi System Log-in | SMBi';
	   $data['page_title']	= 'SMBi System Log-in';
	   $data['meta_description']	= 'San Miguel Brewing International';
	   $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
	   $data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Link has been expired, try to log-in manually<br/> Thank you!'); 
	   $this->load->view('login',$data); 
	}
	

	function decode_base64($sData){
		$sBase64 = strtr($sData, '-_', '+/');
		return base64_decode($sBase64.'==');
	}
	
	function authenticate()
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		
		$this->inactivity();
		$num_rows = 0;
		$active   = 'y';
		isset($_POST['txtusername'])? "" : $_POST['txtusername']="";
		isset($_POST['txtpassword'])? "" : $_POST['txtpassword']="";
		 
		 
		$uname = addslashes(trim($_POST['txtusername']));
		$pword = md5(trim($_POST['txtpassword']));
		$uname = mysql_real_escape_string($uname); 
		 
		$this->load->database("default");
		$query = $this->db->query("SELECT *,admin_users.id as user_id 
								   FROM admin_users 
								   LEFT JOIN country 
								   ON   admin_users.countryID = country.id
								   where uname='$uname' and password='$pword' LIMIT 0,1");
		$row      = $query->row(); 
		$num_rows = count($row);
		
		
		if($num_rows > 0) 
		{	
			$active   = $row->active;
			//RECORD LOG IN ATTEMPTS
			$_SESSION['countryID'] = $row->countryID;
			$_SESSION['user_id']   = $row->user_id;
			$CI->rec_logs->w($row->user_id, $row->full_name, 'System Logs','Users','log-in attempt');
			
			if($active=='y')
			{
				//echo "SELECT id FROM active_session WHERE user_id = ".$row->user_id ." LIMIT 0,1";
				$s = $this->db->query("SELECT id FROM active_session WHERE user_id = ".$row->user_id ." LIMIT 0,1");
				$active_session = $s->row();
				
				//print_r($active_session);
				//die();
				
				if($pword ==  $row->password AND $uname==$row->uname AND !isset($active_session->id))
				{
				   $login     	 =  true;
				   $user_id   	 =  $row->user_id;
				   $full_name    =  $row->full_name;
				   $super_admin  =  $row->super_admin;
				   $countryID    =  $row->countryID;
				   $countryName  =  $row->countryName;
				   $ses_data  =  Array('user_id'=>$user_id,
									   'full_name'=>$full_name,
									   'admin_login'=>$login,
									   'super_admin'=>$super_admin,
									   'countryID'=>$countryID,
									   'countryName'=>$countryName,
									   'iLike_Items'=>'',
									   'iWant_Items'=>'',
									   'eCatalog_Items'=>'',
									   'LAST_ACTIVITY'=>time(),
									   'timestamp'=>time()
									   );
				   $_SESSION  =  $ses_data;
				}
				else
				{
					$login     =  false;
					$_SESSION = Array('user_id'=>'',
									  'fullname'=>'',
									  'admin_login'=>false,
									  'super_admin'=>'',
									  'countryID'=>'',
									  'countryName'=>'',
									  'LAST_ACTIVITY'=>time());
				}
				
				if($login == true)
				{					
					//INSERT USER SESSION
					$dbFields['session_id'] 	= session_id();
					$dbFields['user_id']    	= $_SESSION['user_id'];
					$dbFields['tDate'] 	    	= date('Y-m-d');
					$dbFields['created'] 		= date('H:i:s');
					$dbFields['last_activity'] 	= date('H:i:s');
					$dbFields['timestamp'] 		= time();
					
					//MSG
					$res = $this->c3model->c3crud("insert","active_session",$dbFields,'');
					
					//RECORD LOG IN
					$CI->rec_logs->w($_SESSION['user_id'], $_SESSION['full_name'], 'System Logs','Users','log-in');
					//die();
					
					header('location: '.HTTP_PATH);
					die();
				}
				else
				{
					$data['vfile']				= 'login.php';
					$data['title']				= 'SMBi System Log-in | SMBi';
					$data['page_title']			= 'SMBi System Log-in';
					$data['meta_description']	= 'San Miguel Brewing International';
					$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
					$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Account already log-in.');   
					$this->load->view('login',$data); 
				}
			}else{
				$data['vfile']				= 'login.php';
				$data['title']				= 'SMBi System Log-in | SMBi';
				$data['page_title']			= 'SMBi System Log-in';
				$data['meta_description']	= 'San Miguel Brewing International';
				$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
				$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: User account has been deactivated.');   
				$this->load->view('login',$data); 
			}	
		}
		else
		{
			$data['vfile']		= 'login.php';
			$data['title']		= 'SMBi System Log-in | SMBi';
			$data['page_title']	= 'SMBi System Log-in';
			$data['meta_description']	= 'San Miguel Brewing International';
			$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
			
			$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Invalid Username or Password.');
			$this->load->view('login',$data); 
		}
		
	}
	
	function logout()
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		
		$this->inactivity();
		
		$data['vfile']		= 'login.php';
		$data['title']		= 'SMBi System Log-in | SMBi';
		$data['page_title']	= 'SMBi System Log-in';
		$data['meta_description']	= 'San Miguel Brewing International';
		$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
		
		if($_SESSION){
			$CI->rec_logs->w($_SESSION['user_id'], $_SESSION['full_name'], 'System Logs','Users','log-out');
		}
		
		//DELETE ACCOUNT ON THE DATABASE
		if($_SESSION){
		$this->db->query("DELETE FROM active_session WHERE user_id=". $_SESSION['user_id']);
		}
		session_destroy();
		
		$this->load->view('login',$data); 
	}
	
	function log_in_check()
	{
	   $ses   = $_SESSION;
	   $login = isset($ses['admin_login'])?$ses['admin_login']:false;
	    
	  if($login == true)
		return  $_SESSION;
	  else
		header("location:".HTTP_PATH.'admin');
	}
	
	function automatic_log_in($type='',$id='',$uname='',$email_address='',$date_created='')
	{
		$CI =& get_instance();
		$CI->load->library('rec_logs');
		
		$this->inactivity();
		
		$date_created = $this->decode_base64($date_created);
		$id 			= $this->decode_base64($id);
		$uname 			= $this->decode_base64($uname);
		$email_address  = $this->decode_base64($email_address);
		
		//CLEAR HIS ACCOUNT DURING LOG IN
		$this->db->query("DELETE FROM active_session WHERE user_id = $id");
		
		$query = $this->db->query("SELECT *,admin_users.id as user_id 
								   FROM admin_users 
								   LEFT JOIN country 
								   ON   admin_users.countryID = country.id
								   WHERE admin_users.id=$id AND uname='$uname' and email_address='$email_address' 
								   limit 0,1");
		$row      = $query->row();
		$num_rows = count($row);
	
		
		if($num_rows > 0) 
		{	
			$active   = $row->active;
			if($active=='y')
			{
				//echo "SELECT id FROM active_session WHERE user_id = ".$row->user_id ." LIMIT 0,1";
				$s = $this->db->query("SELECT id FROM active_session WHERE user_id = ".$row->user_id ." LIMIT 0,1");
				$active_session = $s->row();
			
				//print_r($active_session);
				//die();
				
				if(!isset($active_session->id))
				{
				   $login     	 =  true;
				   $user_id   	 =  $row->user_id;
				   $full_name    =  $row->full_name;
				   $super_admin  =  $row->super_admin;
				   $countryID    =  $row->countryID;
				   $countryName  =  $row->countryName;
				   $ses_data  =  Array('user_id'=>$user_id,
									   'full_name'=>$full_name,
									   'admin_login'=>$login,
									   'super_admin'=>$super_admin,
									   'countryID'=>$countryID,
									   'countryName'=>$countryName,
									   'iLike_Items'=>'',
									   'iWant_Items'=>'',
									   'eCatalog_Items'=>'',
									   'LAST_ACTIVITY'=>time());
				   $_SESSION  =  $ses_data;
				}
				else
				{
					$login  =  false;
				}
				
				if($login == true)
				{					
					//INSERT USER SESSION
					$dbFields['session_id'] 	= session_id();
					$dbFields['user_id']    	= $_SESSION['user_id'];
					$dbFields['tDate'] 	    	= date('Y-m-d');
					$dbFields['created'] 		= date('H:i:s');
					$dbFields['last_activity'] 	= date('H:i:s');
					$dbFields['timestamp'] 		= time();
					
					//MSG
					$res = $this->c3model->c3crud("insert","active_session",$dbFields,'');
					
					//RECORD LOG IN
					$CI->rec_logs->w($_SESSION['user_id'], $_SESSION['full_name'], 'System Logs','Users','log-in');
					//die();
					
					if($type=='mktg')
						header('location: '.HTTP_PATH.'itemDatabase/BU_Marketing_Items_review');
					elseif($type=='lgtc')
						header('location: '.HTTP_PATH.'itemDatabase/BU_Logistics_Items_review');
					elseif($type=='iwant')
						header('location: '.HTTP_PATH.'iWantCampaign/iWant');
					elseif($type=='common')
						header('location: '.HTTP_PATH.'gallery/common');
					elseif($type=='eCatalogue')
						header('location: '.HTTP_PATH.'gallery/eCatalog');
					elseif($type=='item_db')
						header('location: '.HTTP_PATH.'itemDatabase');
				}
				else
				{
					$data['vfile']				= 'login.php';
					$data['title']				= 'SMBi System Log-in | SMBi';
					$data['page_title']			= 'SMBi System Log-in';
					$data['meta_description']	= 'San Miguel Brewing International';
					$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
					$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Invalid Username or Password. Or Account already log-in.');   
					$this->load->view('login',$data); 
				}
			}else{
				$data['vfile']				= 'login.php';
				$data['title']				= 'SMBi System Log-in | SMBi';
				$data['page_title']			= 'SMBi System Log-in';
				$data['meta_description']	= 'San Miguel Brewing International';
				$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
				$data['msg'] 				= array('msg_type'=>'alert-warning','msg_desc'=>'Warning: User account has been deactivated.');   
				$this->load->view('login',$data); 
			}	
		}
		else
		{
			$data['vfile']		= 'login.php';
			$data['title']		= 'SMBi System Log-in | SMBi';
			$data['page_title']	= 'SMBi System Log-in';
			$data['meta_description']	= 'San Miguel Brewing International';
			$data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,Southeast Asia';
			
			$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Warning: Invalid Username or Password.');
			$this->load->view('login',$data); 
		}
	}
	
	
} ?>