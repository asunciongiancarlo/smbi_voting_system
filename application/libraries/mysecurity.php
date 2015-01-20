<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Mysecurity extends CI_Controller
 {
 
	public function __construct()
       {
            parent::__construct();
			$this->load->model('c3model');
       }
	   
	function URLChecker($url)
	{
		$length = strlen($url);
		
		$j=0;
		$i=0;
		$prefix_url='';
		
		for($i=$length-1;$j<=4;)
		{
			$prefix_url .= $url[$i];
			$i--; $j++;
		}
		
		//print_r(strrev($prefix_url));
		
		if(strrev($prefix_url)!=".html")
		{
			//show()
			//$this->load->view('404');
			header('Location: '.HTTP_PATH.'pageNotFound.html');
		}
	}   
	   
	
	/*IMAGE RESOLUTION*/
	function resolution_checker($directory)
	{
		/*GET IMAGE SIZE*/
		
		list($width, $height, $type, $attr) = getimagesize($directory);
		
		$error['message'] = '';
		$error['num'] = 0;
		
		/*RESOLUTION CHECKER*/
		if($width < ($height*1.2))
		{
			$error['message'] = "Image Resolution has to be atleast 2 times the width of it's height";
			$error['num'] = 1;
		}
		
		return $error;
	}
	
	
	
	/*INVALID ARTICLE NAME*/
	function invalid_article_name($txtarticle_name)
	{
		$arr = str_split($txtarticle_name);
	
		$invalid_characters = "`!@#$^*()+={}|/?<>";
		$characters="";
		$invalid_article_name['num']=0;
		$invalid_article_name['message']="";
			
		$ic = str_split($invalid_characters);
		foreach($arr as $a)
		{
			foreach($ic as $i)
			{
				if($a==$i)
				{
				 $characters .= ",".$i;
				}
			}
		}
		
		if($characters!="")
		{
			$invalid_article_name['num'] = 1;
			$invalid_article_name['message'] = "Following invalid characters encountered: ".$characters." in Article name.";
		}
		
		return $invalid_article_name;
	}
	
	
	/*ARTICLES*/
	function check_same_article_name($table='',$name='')
		{
		  $name = addslashes($name);	
		  $sql = "SELECT * FROM $table WHERE fsan_blog_name = '$name'";
		  $res = $this->c3model->c3crud("select",'','','',$sql);
		  $same_category_error['error']="";
		  $same_category_error['message']="";
		  
		  if($res!=null)
		  {
			  foreach($res as $r)
			  {
				if($r['fsan_blog_name'] == $name)
				{ 
					$same_category_error['error'] = 1;
					$same_category_error['message'] = "Duplicate blog name.";
				}
			  }
		  }
		  
		  return $same_category_error;
		}
	
	
	 
	function check_same_category($txtcategory_name='',$type='')
		{
		  
		  $sql1 = "SELECT * FROM mesa_category WHERE fsan_category_type = '$type' and fsan_category_name = '$txtcategory_name'"; 
		
		  $res = $this->c3model->c3crud("select",'','','',$sql1);
		  $same_category_error['error']="";
		  $same_category_error['message']="";
		  
		  if($res!=null)
		  {
			  foreach($res as $r)
			  {
				$same_category_error['error'] = 1;
				$same_category_error['message'] = "Duplicate category name.";
			  }
		  }
		  
		  return $same_category_error;
		}
		
	   
	function deletable_category($id='')
	{
	    $num_rows['num']=0;
	    $num_rows['message']="";
	  
	    $sql = "SELECT * FROM  `mesa_category_xref` 
			    WHERE  `fsan_category_parent_id` = $id";
			  
	    $res = $this->c3model->c3crud("select",'','','',$sql);
		
		
		foreach($res as $r)
		{ 
			$num_rows['num']+=1;
		}
		
		if($num_rows['num']>=1)
		{
			$num_rows['message'] = "Cannot delete Parent category.";
		}
		  
		return $num_rows;
	}
	  
	  
	function deletable_category2($type,$table,$id)
	{
		$num_articles['num'] =0;
		$num_articles['message'] ="";
		
		$table = "mesa_".$type."_article";
		
		$sql = "SELECT count(*) as num FROM $table WHERE fsan_category_id = $id";
		
		$a = $this->c3model->c3crud("select",'','','',$sql);
		
		$num_articles['num'] = $a[0]['num'];
		 
		
		if($num_articles['num']>0)
		{
			$num_articles['message'] = "Cannot delete Category ID of a Blog/Article.";
		}
		return $num_articles;
	} 
	

	function check_int($txtctr='')
	{
		$security_error['message']="";
		$security_error['error']="";
		
		$arr_txtctr = str_split($txtctr);
		foreach($arr_txtctr as $a)
		  {
			if(!is_numeric($a))
			{
				$security_error['error'] = 1;
				$security_error['message'] = "Ctr only accept interger.";
			}
		}
		
		return $security_error;
	}			
 } 

?>