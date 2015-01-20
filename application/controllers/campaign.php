<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ILikeCampaign extends CI_Controller {
 
	  public function __construct()
       {
            parent::__construct();
            date_default_timezone_set('UTC');
			session_start();
			$this->load->model('c3model');
		    $this->load->library('security');
			$this->output->enable_profiler(FALSE);
       }
	   
   public function index()
	{			
	   $data['vfile']		= 'votingGal.php';
	   $data['title']		= 'SMBi';
	   $data['page_title']	= 'I Like Campaign';
	   $data['meta_description']	= 'San Miguel Brewing International';
	   $data['meta_keyword']		= 'Food,beverage,packaging,Corporation, San Miguel,company , Philippines ,South-east Asia';
	   
	   $HTTP_PATH = HTTP_PATH;
	   $data['breadCrumbs']			= '<a href='.$HTTP_PATH.'iLikeCampaign> I Like Campaign </a>';
	   
	   $this->load->view('votingGal.php',$data); 
	}

	
} ?>