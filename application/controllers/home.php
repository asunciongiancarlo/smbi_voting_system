<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {
 
	public function __construct()
    {
		parent::__construct();
		date_default_timezone_set('UTC');
		session_start();
		$this->load->model('c3model');
		$this->load->library('modules');
		$this->output->enable_profiler(FALSE);
		
		$this->modules->session_handler();
    }
   
   public function index()
   {			
	 
	   $data['vfile']		= 'home.php';
	   $data['title']		= 'San Miguel Brewing International';
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
	   
	   
	   //CHECK IF NEW USER
	   $num=0;
	   if(isset($_SESSION['user_id'])){
		   $sql = "SELECT new_id FROM admin_users WHERE id = ". $_SESSION['user_id'];
		   $sql = $this->db->query($sql);
		   $row = $sql->row();
		   $num = $row->new_id;
		   //print_r($row);
		   if($num == 1)
		   {
			$data['id'] = $_SESSION['user_id'];
			$data['vfile']	= 'personal_accountFORM.php';
			$data['breadCrumbs'] = '';
			$data['msg'] = array('msg_type'=>'alert-warning','msg_desc'=>'Welcome to SMBi POSM System! as a new User you are required to change your password. For you to access the system.');
		   }
		}
	   
	
	   
	   if($this->modules->access_checker()==TRUE)
	   {
	    if($num == 1)
			$this->load->view('innerPages',$data);
		else
			$this->load->view('index',$data); 
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
	

} ?>