<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Myforms2 extends CI_Controller
 { 
    
	function get_form_vertical($fieldcaption,$formname,$action_url)
	  {
	    $formname =str_replace(' ','_',$formname);
	 	
		 $caption="";$ctrl="";
		 $items="";
		 foreach($fieldcaption as $f)
		   {
			 $validation = "";
					
					switch ($f['v']) {
						case 'r':
							$validation = "data-trigger='change' data-required='true' placeholder='required field'";
							break;
						case 'r_integer':
							$validation = "data-trigger='change' data-required='true' data-type='number'  placeholder='required field'";
							break;
						case 'integer':
							$validation = "data-trigger='change' data-type='number'  placeholder='inetger only'";
							break;
						case 'email':
							$validation = "data-trigger='change' data-type='email'  placeholder='email only'";
							break;
						case 'date':
							$validation = "data-trigger='change' data-type='dateIso' data-required='true' id='datepicker'  placeholder='date only'";
							break;
						case 'link':
							$validation = "data-trigger='change' data-required='true' data-type='url'";
							break;
						case 'year':
							$validation = "data-trigger='change' data-required='true' data-type='number' data-min='4'";
							break;
					}
					
		     if($f['type']=='text')
			    {
				  $items .="<tr>";
				  $items.= "<th bgcolor='#d6cfcf'>".$f['caption']."</th>";
				  $items.= "<td bgcolor='#fff'><input id='txt".str_replace(' ','_',$f['caption'])."' value='".$f['value']."'  type='text' name='txt".str_replace(' ','_',$f['caption'])."' value='".$f['value']."' $validation></td>"; 
				  $items .= "</tr>";
				} 
			 else if($f['type']=='password')
			    { 
				  $items .= "<tr>";
  				  $items.="<th bgcolor='#d6cfcf'>".$f['caption']."</th>";
				  $items.= "<td  bgcolor='#fff><input type='password' name='txt".str_replace(' ','_',$f['caption'])."' value='".$f['value']."'></td>"; 
				  $items .= "</tr>";
			    }
			else if($f['type']=='select')
			    {
				   $items .="<tr>";
				   $items.= "<th bgcolor='#d6cfcf'>".$f['caption']."</th>";
				   $items.= "<td bgcolor='#fff'>". $this->select_input($f['option'],$f['selected'],"sel_".str_replace(' ','_',$f['caption'])) ."</td>";
				   $items .= "</tr>";
				 }
			else if($f['type']=='select2')
			    {
				  $items .= "<tr>";
				   $items.= "<th bgcolor='#d6cfcf'>".$f['caption']."</th>";
				   $items.= "<td bgcolor='#fff'>". $this->select_input2($f['option'],$f['selected'],"sel_".str_replace(' ','_',$f['caption'])) ."</td>";
				  $items .= "</tr>";
				 }
		    else if($f['type']=='categtree')
			    {
				  $items .= "<tr>";
			       $items.=  "<th bgcolor='#d6cfcf'>".$f['caption']."</th>";
				   $items.="<td bgcolor='#fff'>". $this->categ_tree($f['selected'],"sel_".str_replace(' ','_',$f['caption']),'',$f['filter']) . "</td> ";
				  $items .= "</tr>";
				}
			 else if($f['type']=='upload')
			    {
				  $items .="<tr>";
			      $items.=  "<th bgcolor='#d6cfcf'>".$f['caption']."</th>";
				  $items.="<td bgcolor='#fff'><input type='FILE' name='userfile'></td>";
				  $items .= "</tr>";
				}
			  else if($f['type']=='upload2')
			    {
				  $items .="<tr>";
			      $items .=  "<th bgcolor='#d6cfcf'>".$f['caption']."</th>";
				  $items .="<td bgcolor='#fff'><input type='FILE' name='".str_replace(' ','_',$f['caption'])."'></td>";
				  $items .= "</tr>";
				}
			
				
		   }
		  
		 $this->load->library('security');
		 $csrf = "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>";
		
	    echo "<form name='$formname' id='$formname' method='POST' action='$action_url'  enctype='multipart/form-data' data-validate='parsley'>
					
					$csrf
					
		           <table class='tblData' style='width:400px' bgcolor='#000' cellspacing='1' cellpadding='5' >
				   <tr> <td bgcolor='#fff' align='center' colspan='2' align='right'>  <input name='btnsubmit' type='submit' onclick='javascript:$(\"#$formname\").parsley(\"validate\")' value='Save'></td> </tr>
				   $items";
		   $f=$fieldcaption[count($fieldcaption)-1];
		   if($f['type'] == 'fckeditor')
			  {
			     echo  "<tr><th bgcolor='#d6cfcf' colspan=".count($fieldcaption).">".$f['caption']."</th></tr>";
			     echo  "<tr><td width='700' bgcolor='#fff' colspan=".count($fieldcaption).">";
				 include( FCPATH2 . "/editor/fckeditor.php") ;
				$sBasePath = FCPATH2 ."/editor/";
				$oFCKeditor = new FCKeditor('htmlcontent') ;
				$oFCKeditor->BasePath	=  HTTP_PATH . "/editor/";
				$oFCKeditor->Value		= $f['value'];
				$oFCKeditor->width		= 600;
				$oFCKeditor->Height		= 500;
				$oFCKeditor->Create() ;
			   echo  "</td></tr>";
			  }
			  
			 if($f['type'] == 'custom')
			  {
			     echo  "<tr><th bgcolor='#d6cfcf' colspan='2'>".$f['caption']."</th></tr>"; 
			     echo  "<tr><td width='700' bgcolor='#fff' colspan='2'>";
				 echo $f['value'];
			     echo  "</td></tr>";
			  }
			  
			/* 2 custom*/
			$f=$fieldcaption[count($fieldcaption)-2];
		    if($f['type'] == 'fckeditor')
			  {
			     echo  "<tr><th bgcolor='#d6cfcf' colspan=".count($fieldcaption).">".$f['caption']."</th></tr>";
			     echo  "<tr><td width='700' bgcolor='#fff' colspan=".count($fieldcaption).">";
				 include( FCPATH2 . "/editor/fckeditor.php") ;
				$sBasePath = FCPATH2 ."/editor/";
				$oFCKeditor = new FCKeditor('htmlcontent') ;
				$oFCKeditor->BasePath	=  HTTP_PATH . "/editor/";
				$oFCKeditor->Value		= $f['value'];
				$oFCKeditor->width		= 600;
				$oFCKeditor->Height		= 500;
				$oFCKeditor->Create() ;
			   echo  "</td></tr>";
			  }
			  
			 if($f['type'] == 'custom')
			  {
			     echo  "<tr><th bgcolor='#d6cfcf' colspan=".count($fieldcaption).">".$f['caption']."</th></tr>"; 
			     echo  "<tr><td width='700' bgcolor='#fff' colspan=".count($fieldcaption).">";
				 echo $f['value'];
			     echo  "</td></tr>";
			  }
			echo "<tr> <td bgcolor='#ffffff' colspan='".count($fieldcaption)."' align='center'>  <input name='btnsubmit2' type='submit' onclick='javascript:$(\"#$formname\").parsley(\"validate\")' value='Save'></td> </tr></table>";
		
		
		echo  "</form>"; 
	  }
	  
	function subscribe_Newsletter_form($fieldcaption,$formname,$action_url)
	  {
		$formname =str_replace(' ','_',$formname);
	 	
		 $caption="";$ctrl="";

		 foreach($fieldcaption as $f)
		   {
		     
			$validation = "data-trigger='change' data-required='true' data-type='email'  placeholder='Subscribe to Newsletter'";
				if($f['type']=='text')
				{
				$ctrl    .= "<input type='email' name='txt".str_replace(' ','_',$f['caption'])."' value='".$f['value']."' $validation >"; 
				}
			}
			
			echo "<form name='$formname' id='$formname' method='POST' action='$action_url'  enctype='multipart/form-data'  data-validate='parsley'>
				    $ctrl ";
			echo " <input name='btnsubmit' type='submit' value='Subscribe Now' onclick='javascript:$(\"#$formname\").parsley(\"validate\")'>";
		echo  "</form>";
	   }
	  
	function special_form($fieldcaption,$formname,$action_url)
	  {
		$formname =str_replace(' ','_',$formname);
	 	
		 $caption="";$ctrl="";

		 foreach($fieldcaption as $f)
		   {
		     $validation = "";
					
					switch ($f['v']) {
						case 'email':
							$validation = "data-trigger='change' data-required='true' data-type='email'  placeholder='email only'";
							break;
						case 'link':
							$validation = "data-trigger='change' data-required='true' data-type='url'";
							break;
						case 'r':
							$validation = "data-trigger='change' data-required='true' placeholder='required field'";
							break;
					}

			 if($f['type']=='text')
			 {
				$caption .= "<th bgcolor='#d6cfcf'>".$f['caption']."</th>";
				$ctrl    .= "<td bgcolor='#ffffff' style='height:60px' valign='top'><input type='text' name='txt".str_replace(' ','_',$f['caption'])."' value='".$f['value']."' $validation ></td>"; 
			 }
			}
			
			$this->load->library('security');
			$csrf = "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>";
			
			echo "<form name='$formname' id='$formname' method='POST' action='$action_url'  enctype='multipart/form-data'  data-validate='parsley'>
					$csrf
		           <table class='tblData' bgcolor='#000' cellspacing='1' cellpadding='5' >
				   <tr> <td bgcolor='#ffffff' align='center' colspan='".count($fieldcaption)."'>  <input name='btnsubmit' type='submit' onclick='javascript:$(\"#$formname\").parsley(\"validate\")' value='Save'></td> </tr>
				   <tr> $caption</tr>
				   <tr> $ctrl</tr>";
			
			echo "<tr> <td bgcolor='#ffffff' colspan='".count($fieldcaption)."' align='center'>  <input name='btnsubmit2' type='submit' value='Save' onclick='javascript:$(\"#$formname\").parsley(\"validate\")'></td> </tr></table>";
	   
		echo  "</form>";
	   }
	  
	function get_form_horizontal($fieldcaption,$formname,$action_url)
	  {
	    $formname =str_replace(' ','_',$formname);
	 	
		 $caption="";$ctrl="";

		 foreach($fieldcaption as $f)
		   {
		     $validation = "";
					
					switch ($f['v']) {
						case 'r':
							$validation = "data-trigger='change' data-required='true' placeholder='required field'";
							break;
						case 'r_integer':
							$validation = "data-trigger='change' data-required='true' data-type='number'  placeholder='required field'";
							break;
						case 'integer':
							$validation = "data-trigger='change' data-type='number'  placeholder='inetger only'";
							break;
						case 'email':
							$validation = "data-trigger='change' data-type='email'  placeholder='email only'";
							break;
						case 'date':
							$validation = "data-trigger='change' data-type='dateIso' data-required='true' id='datepicker'  placeholder='date only'";
							break;
						case 'link':
							$validation = "data-trigger='change' data-required='true' data-type='url'";
							break;
						case 'year':
							$validation = "data-trigger='change' data-required='true' data-type='number' data-min='4'";
							break;
						case 'password_1st_field':
							$validation = "data-trigger='change' data-required='true' placeholder='required field' id='eqalToModel' data-equalto='#eqalToModel'";
							break;
						case 'password_2nd_field':
							$validation = "data-trigger='change' data-required='true' placeholder='required field' id='data-equalto' data-equalto='#eqalToModel'";
							break;
					}

			 if($f['type']=='text')
			    {
					$caption .= "<th bgcolor='#d6cfcf'>".$f['caption']."</th>";
					 
					$ctrl    .= "<td bgcolor='#ffffff' style='height:60px' valign='top'><input type='text' name='txt".str_replace(' ','_',$f['caption'])."' value='".$f['value']."' $validation ></td>"; 
				} 
			 else if($f['type']=='password')
			    { 
  				  if($f['v'] == 'r')
					{
						$validation = "data-required='true' placeholder='required field'";
					}
					
					$caption .= "<th bgcolor='#d6cfcf'>".$f['caption']."</th>";
					$ctrl    .= "<td bgcolor='#ffffff' style='height:60px' valign='top'><input type='password' name='txt".str_replace(' ','_',$f['caption'])."' value='".$f['value']."' $validation ></td>";
			    }
			else if($f['type']=='select')
			    {
				  $caption .= "<th bgcolor='#d6cfcf'>".$f['caption']."</th>";
				  $ctrl    .= "<td bgcolor='#ffffff' style='height:60px' valign='top'>". $this->select_input($f['option'],$f['selected'],"sel_".str_replace(' ','_',$f['caption'])) ."</td>";
				 }
			else if($f['type']=='select2')
			    {
				  $caption .= "<th bgcolor='#d6cfcf'>".$f['caption']."</th>";
				  $ctrl    .= "<td bgcolor='#ffffff' style='height:60px' valign='top'>". $this->select_input2($f['option'],$f['selected'],"sel_".str_replace(' ','_',$f['caption'])) ."</td>";
				 }
		    else if($f['type']=='categtree')
			    {
					$category_tree = "categ_tree";
					
					if ($f['v']=='r_article')
					{
						$category_tree = "categ_tree2";
					}
					
					$caption .=  "<th bgcolor='#d6cfcf'>".$f['caption']."</th>";
					$ctrl    .="<td bgcolor='#ffffff' style='height:60px' valign='top'>". $this->$category_tree($f['selected'],"sel_".str_replace(' ','_',$f['caption']),'',$f['filter']) . "</td> ";
				}
			else if($f['type']=='menu_tree')
			    {
					
					$caption .=  "<th bgcolor='#d6cfcf'>".$f['caption']."</th>";
					$ctrl    .="<td bgcolor='#ffffff' style='height:60px' valign='top'>". $this->menu_tree($f['selected'],$f['caption']) . "</td> ";
				}
			else if($f['type']=='upload')
			    {			      
				  $validation = "";
				  
				  if($f['v'] == 'r')
				  {
					$validation = "data-required='true' ";
				  }
				  
			      $caption .=  "<th bgcolor='#d6cfcf'>".$f['caption']."</th>";
				  $ctrl    .="<td bgcolor='#ffffff'><input type='FILE' name='userfile' $validation></td>";
				}
			else if($f['type']=='upload2')
			    {

				  $caption .=  "<th bgcolor='#d6cfcf'>".$f['caption']."</th>";
				  $ctrl    .="<td bgcolor='#ffffff'><input type='FILE' name='".str_replace(' ','_',$f['caption'])."'></td>";
				}
				
		   }
		   
		   
		   $this->load->library('security');
		   $csrf = "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>";
			
			
		   echo "<form name='$formname' id='$formname' method='POST' action='$action_url'  enctype='multipart/form-data'  data-validate='parsley'>
					$csrf
		           <table class='tblData' bgcolor='#000' cellspacing='1' cellpadding='5' >
				   <tr> <td bgcolor='#ffffff' align='center' colspan='".count($fieldcaption)."'>  <input name='btnsubmit' type='submit' onclick='javascript:$(\"#$formname\").parsley(\"validate\")' value='Save'></td> </tr>
				   <tr> $caption</tr>
				   <tr> $ctrl</tr>";
				   
		    $f=$fieldcaption[count($fieldcaption)-1];
		    if($f['type'] == 'fckeditor')
			{
			    echo  "<tr><th bgcolor='#d6cfcf' colspan=".count($fieldcaption).">".$f['caption']."</th></tr>";
			    echo  "<tr><td width='700' bgcolor='#ffffff' colspan=".count($fieldcaption).">";
				include( FCPATH2 . "/editor/fckeditor.php") ;
				$sBasePath = FCPATH2 ."/editor/";
				$oFCKeditor = new FCKeditor('htmlcontent') ;
				$oFCKeditor->BasePath	=  HTTP_PATH . "/editor/";
				$oFCKeditor->Value		= $f['value'];
				$oFCKeditor->width		= 600;
				$oFCKeditor->Height		= 500;
				$oFCKeditor->Create() ;
			   echo  "</td></tr>";
			}
			  
			if($f['type'] == 'custom')
			{
				echo  "<tr><th bgcolor='#d6cfcf' colspan=".count($fieldcaption).">".$f['caption']."</th></tr>"; 
				echo  "<tr><td width='700' bgcolor='#ffffff' colspan=".count($fieldcaption).">";
				echo $f['value'];
				echo  "</td></tr>";
			}
		  
		 
			$f=$fieldcaption[count($fieldcaption)-2];
		    if($f['type'] == 'fckeditor')
			{
			    echo  "<tr><th bgcolor='#d6cfcf' colspan=".count($fieldcaption).">".$f['caption']."</th></tr>";
			    echo  "<tr><td width='700' bgcolor='#ffffff' colspan=".count($fieldcaption).">";
				include( FCPATH2 . "/editor/fckeditor.php") ;
				$sBasePath = FCPATH2 ."/editor/";
				$oFCKeditor = new FCKeditor('htmlcontent1') ;
				$oFCKeditor->BasePath	=  HTTP_PATH . "/editor/";
				$oFCKeditor->Value		= $f['value'];
				$oFCKeditor->width		= 600;
				$oFCKeditor->Height		= 500;
				$oFCKeditor->Create() ;
			   echo  "</td></tr>";
			}
			  
			if($f['type'] == 'custom')
			{
			    echo  "<tr><th bgcolor='#d6cfcf' colspan=".count($fieldcaption).">".$f['caption']."</th></tr>"; 
			    echo  "<tr><td width='700' bgcolor='#ffffff' colspan=".count($fieldcaption).">";
				echo $f['value'];
			    echo  "</td></tr>";
			}
			
			 
			$f=$fieldcaption[count($fieldcaption)-3];
		    if($f['type'] == 'fckeditor')
			{
			    echo  "<tr><th bgcolor='#d6cfcf' colspan=".count($fieldcaption).">".$f['caption']."</th></tr>";
			    echo  "<tr><td width='700' bgcolor='#ffffff' colspan=".count($fieldcaption).">";
				include( FCPATH2 . "/editor/fckeditor.php") ;
				$sBasePath = FCPATH2 ."/editor/";
				$oFCKeditor = new FCKeditor('htmlcontent2') ;
				$oFCKeditor->BasePath	=  HTTP_PATH . "/editor/";
				$oFCKeditor->Value		= $f['value'];
				$oFCKeditor->width		= 600;
				$oFCKeditor->Height		= 500;
				$oFCKeditor->Create() ;
			   echo  "</td></tr>";
			}
			  
			if($f['type'] == 'custom')
			{
			    echo  "<tr><th bgcolor='#d6cfcf' colspan=".count($fieldcaption).">".$f['caption']."</th></tr>"; 
			    echo  "<tr><td width='700' bgcolor='#ffffff' colspan=".count($fieldcaption).">";
				echo $f['value'];
			    echo  "</td></tr>";
			}
			  
			echo "<tr> <td bgcolor='#ffffff' colspan='".count($fieldcaption)."' align='center'>  <input name='btnsubmit2' type='submit' value='Save' onclick='javascript:$(\"#$formname\").parsley(\"validate\")'></td> </tr></table>";
	   
		echo  "</form>";

	  }
	 
	function menu_tree($selected="",$caption="")
	{
		$this->load->database();
		
		$sql = "SELECT  mesa_menu_manager.fsan_id as id, mesa_menu_manager.fsan_name as name 
				FROM mesa_menu_manager 
				INNER JOIN mesa_menu_manager_xref 
				ON mesa_menu_manager_xref.fsan_menu_child = mesa_menu_manager.fsan_id
				WHERE mesa_menu_manager_xref.fsan_menu_parent = '0' ORDER BY fsan_ctr ASC";
		
		$query = $this->db->query($sql);
		$row = $query->result_array();
		$sel = "";
		if($selected!='none') $sel ="<option  value='0'>--parent</option>";
		foreach($row as $r)
		{
			$id   = $r['id'];
			$name = $r['name'];
			$s = $selected == $id ? "selected":"";
			$sel .= "<option $s value='$id' > $name </option>";
			$sel .= $this->get_menu_child($id,'_',$selected);
		}
		return "<select name='$caption' data-required='true' >".$sel."</select>";
	}
	
	function get_menu_child($id,$dd,$selected)
	{
		$this->load->database();
		
		$sql = "SELECT  mesa_menu_manager.fsan_id as id, mesa_menu_manager.fsan_name as name 
				FROM mesa_menu_manager 
				INNER JOIN mesa_menu_manager_xref 
				ON mesa_menu_manager_xref.fsan_menu_child = mesa_menu_manager.fsan_id
				WHERE mesa_menu_manager_xref.fsan_menu_parent = '$id' ORDER BY fsan_ctr ASC";
		
		$query = $this->db->query($sql);
		$row = $query->result_array();
		$child="";
		foreach($row as $r)
		{
			$id   = $r['id'];
			$name = $r['name'];
			$s = $selected == $id ? "selected":"";
			$child .= "<option $s value='$id' > $dd$name </option>";
			$child .= $this->get_menu_child($id,$dd."_",$selected);
		}
	
		return $child;
	}
	  
	function categ_tree($selected='',$caption='',$option='',$filter='')
	    {
		  $this->load->database();
		  $sql ="SELECT c.`fsan_category_name` , c.fsan_category_id
				 FROM `mesa_category` AS c
				 INNER JOIN mesa_category_xref AS cref ON 
				 cref.fsan_category_child_id = c.fsan_category_id
				 where cref.fsan_category_parent_id = '0' $filter ";

		  $query = $this->db->query($sql);
          $row = $query->result_array();
		  $sel= "";
		  if($selected!='none') $sel ="<option  value='0'>--parent</option>";
		  foreach($row as $r)
		   {
		     $id   =$r['fsan_category_id'];
			 $name =$r['fsan_category_name'];
			 $s = $selected == $id ? "selected":"";
		     $sel .="<option $s value='$id'>$name</option>";
		     $sel .=$this->get_child($id,'',$selected,'_') ;
		   }
		  return "<select $option name='$caption' data-required='true' >".$sel."</select>";
		}
	
		
	function categ_tree2($selected='',$caption='',$option='',$filter='')
	    {
		  $this->load->database();
		  $sql ="SELECT c.`fsan_category_name` , c.fsan_category_id
	        FROM `mesa_category` AS c
			INNER JOIN mesa_category_xref AS cref ON cref.fsan_category_child_id = c.fsan_category_id
			where cref.fsan_category_parent_id = '0' $filter ";

		  $query = $this->db->query($sql);
          $row = $query->result_array();
		  $sel= "";
		  if($selected!='none') $sel ="<option  value>--Parent</option>";
		  foreach($row as $r)
		   {
		     $id   =$r['fsan_category_id'];
			 $name =$r['fsan_category_name'];
			 $s = $selected == $id ? "selected":"";
		     $sel .="<option $s value='$id'>$name</option>";
		     $sel .=$this->get_child($id,'',$selected,'_') ;
		   }
		  return "<select $option name='$caption' data-required='true' >".$sel."</select>";
		}
		
	function get_child($id,$child='',$selected='',$dd)
	  {
	    $this->load->database();
	    
	    $sql ="SELECT c.`fsan_category_name` , c.fsan_category_id
	        FROM `mesa_category` AS c
			INNER JOIN mesa_category_xref AS cref ON cref.fsan_category_child_id = c.fsan_category_id
			where cref.fsan_category_parent_id = '$id'";
	    $query = $this->db->query($sql);
		 $row = $query->result_array();
		foreach($row as $r)
		   {
		     $id=$r['fsan_category_id'];
			 $name =$r['fsan_category_name'];
			 $s = $selected == $id ? "selected":"";
		     $child .="<option $s value='$id'> $dd$name </option>";
		     $child .=$this->get_child($id,'',$selected,$dd . "_") ;
		   }
		return $child;
	  }
	  
	function select_input($option,$selected,$name)
	    {

		 $s ="<select name='$name' data-required='true'>";
		 $s .="<option value>--Select One--</option>";
		 foreach($option as $r)
		   {
		     $sel="";
		     if($selected==$r[0]) $sel ="selected='true'";
		     $s .="<option $sel value='".$r[0]."'>".$r[1]."</option>";
		   }
		  $s .="</select>";
		  return $s;
		}
		
	function select_input2($item,$selected,$name)
	    {
		 
		 $s ="<select name='$name' data-required='true'>";
		 $s .="<option value>--Select One--</option>";
		 foreach($item as $r)
		   {
		     $sel="";
		     if($selected==$r) $sel ="selected='true'";
		     $s .="<option $sel value='$r'>".$r."</option>";
		   }
		  $s .="</select>";
		  return $s;
		}
	
	
	function get_resume_form($fieldcaption,$formname,$action_url)
	  {
	    $formname =str_replace(' ','_',$formname);
	 	
		 $caption="";$ctrl="";

		 foreach($fieldcaption as $f)
		   {
		     $validation = "";
					
					switch ($f['v']) {
						case 'r':
							$validation = "data-trigger='change' data-required='true' placeholder='required field'";
							break;
						case 'email':
							$validation = "data-trigger='change' data-required='true' data-type='email'  placeholder='email only'";
							break;
					}

			if($f['type']=='text')
			{
				if($f['caption']=="email")
				{
					$caption = "Email address";
				}
				else
				{
					$caption = $f['caption'];
				}
				
				$ctrl    .= ucfirst($caption)."</br>"."<input type='text' name='txt".str_replace(' ','_',$f['caption'])."' value='".$f['value']."' $validation size='50'></br></br>";  
			}
			elseif($f['type']=='captcha')
			{
				/*LOAD DATABASE*/
				$CI =& get_instance();
				$CI->load->database('default');
	
				$this->load->helper('captcha');
				$http_path = HTTP_PATH."image/captcha/";
				
				$char1 = rand(49,57);
				$char2 = rand(49,57);
				$char3 = rand(49,57);
				$char4 = rand(49,57);
				$char5 = rand(49,57);
				$char6 = rand(49,57);
				
				$word = chr($char1) . chr($char2) . chr($char3) . chr($char4);
				
				 $vals = array(
					'word' => $word,
					'img_path'	 => './image/captcha/',
					'img_url'	 => $http_path,
					'font_path'	 => './assets/fonts/MyriadPro-Regular.ttf',
					'img_width'	 => 200,
					'img_height' => 80,
					'expiration' => 7200
				);

				$cap = create_captcha($vals);
				
				
				$data = array(
					'fsan_captcha_time'	=> $cap['time'],
					'fsan_ip_address'	=> $this->input->ip_address(),
					'fsan_word'	 => $cap['word']
				);
				
				/*print_r($data);*/
				$query = $this->db->insert_string('mesa_captcha', $data);
				$this->db->query($query);
				
				$captcha_img = $cap['image'];
				$ctrl    .= "Submit the word you see below: </br>$captcha_img</br>";
				$ctrl    .= '<input type="text" name="captcha" value="" data-required="true" placeholder="type captcha here"/>';
			}
			elseif($f['type']=='select')
			{
				$ctrl    .= ucfirst($f['caption'])."</br>".$this->select_input($f['option'],$f['selected'],"sel_".str_replace(' ','_',$f['caption']))."</br></br>";
			}
			elseif($f['type']=='upload')
			{			      
				 $validation = "";
				  
				if($f['v'] == 'r')
				{
					$validation = "data-required='true' ";
				}  
			    $ctrl    .= ucfirst('Submit your resume: (max. of 2MB, .pdf or .doc/.docx file)')."</br><input type='FILE' name='userfile' $validation></br></br>";
			}
		}
		
		   $this->load->library('security');
		   $csrf = "<input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>";
		 
		   echo "<form name='$formname' id='$formname' method='POST' action='$action_url'  enctype='multipart/form-data'  data-validate='parsley'>
					$csrf
				    $ctrl </br></br>";
		   echo "<input name='btnsubmit2' type='submit' value='Submit' onclick='javascript:$(\"#$formname\").parsley(\"validate\")'>";
	   
		echo  "</form>";

	  }
 
 }
?>
