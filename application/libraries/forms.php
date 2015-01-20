<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Forms extends CI_Controller
 { 
	public function __construct()
    {
		parent::__construct();
		$this->load->model('c3model');
		$this->load->library('security');
    }
	 
	/*HEADER*/ 
	function form_header($name,$id,$action)
	{
		$this->load->library('security');
		$csrf = " <input type='hidden' name='".$this->security->get_csrf_token_name()."' value='".$this->security->get_csrf_hash()."'>";
		
		$form  = "<form name='$name' id='$id' method='POST' action='$action'  enctype='multipart/form-data' width='100px;'>";
		$form .= $csrf;
		
		return $form;
	}
	/*HEADER*/
	
	/*YELLOW FIELDS */
	function form_fields($type,$name,$value,$placeholder,$v)
	{
		switch ($v) {
			case 'r':
				$validation = "data-parsley-trigger='change' data-parsley-required='true' placeholder='required field'";
				break;
			case 'r_integer':
				$validation = "data-parsley-trigger='change' data-parsley-required='true' data-parsley-type='number'  placeholder='required field'";
				break;
			case 'integer':
				$validation = "data-parsley-trigger='change' data-parsley-type='number'  placeholder='inetger only'";
				break;
			case 'email':
				$validation = "data-parsley-trigger='change' data-parsley-type='email'  placeholder='email only'";
				break;
			case 'date':
				$validation = "data-parsley-trigger='change' data-parsley-date data-parsley-required='true' id='datepicker'  placeholder='date only'";
				break;
			case 'link':
				$validation = "data-parsley-trigger='change' data-parsley-required='true' data-parsley-type='url'";
				break;
			case 'year':
				$validation = "data-parsley-trigger='change' data-parsley-required='true' data-parsley-type='number' data-min='4'";
				break;
		}
		
		if($type=="text")
		{
			$field = "
			<div style='float:left;'>
				<p style='color:#dfd9d9; font-size:10px; margin-bottom:0px;'> $placeholder</p>
				<input type='$type' name='$name' class='fl' value='$value' placeholder='$placeholder' $validation data-error-container='#errorContainer' style='width:280px;height:55px;margin-right:15px;font-size:18px;background-color:#fffbcb;border-radius:6px;float: left;'>
			</div>
			";
		}
		 
		elseif($type=="select_country")
		{
			$field = "<select style='width:30%;height:63px;margin-right:15px;font-size:18px;background-color:#fffbcb;border-radius:6px;' name='$name' class='fl'>";
			//GET PARENT ID IN REF
			if($value!=0){
				$sql ="SELECT parentID  
						FROM premiumItemTypeRef WHERE childID='$value'";

				$query = $this->db->query($sql);
				$row = $query->row();
				$value = $row->parentID;
			}
			
			$field .=  	$this->menu_parent($value);		
			$field .= "</select>";
		}
		
		return $field;
	}
	
	/*YELLOW FIELDS */
	
	
	/*DEFAULT*/
	function form_fields2($type,$name,$value,$label,$v,$disable='')
	{
		switch ($v) {
			case 'o':
				$validation = "placeholder='Optional'";
				break;
			case 'r':
				$validation = "data-parsley-trigger='change' data-parsley-required='true' placeholder='Required field'";
				break;
			case 'r_integer':
				$validation = "data-parsley-trigger='change' data-parsley-required='true' data-parsley-type='number'  placeholder='required field'";
				break;
			case 'integer':
				$validation = "data-parsley-trigger='change' data-parsley-type='number'  placeholder='inetger only'";
				break;
			case 'email':
				$validation = "data-parsley-trigger='change' data-parsley-type='email'  placeholder='email only'";
				break;
			case 'date':
				$validation = "data-parsley-trigger='change' data-parsley-date data-parsley-required='true' id='datepicker'  placeholder='date only' class='datepicker'";
				break;
			case 'date2':
				$validation = "data-parsley-trigger='change' data-parsley-date data-parsley-required='true' id='datepicker2'  placeholder='date only' class='datepicker'";
				break;
			case 'date3':
				$validation = "data-parsley-trigger='change' data-parsley-date data-parsley-required='true' id='datepicker3'  placeholder='date only' class='datepicker'";
				break;
			case 'date4':
				$validation = "data-parsley-trigger='change' data-parsley-date data-parsley-required='true' id='datepicker4'  placeholder='date only' ";
				break;
			case 'DateFrom':
				$validation = "data-parsley-trigger='focus' id='DateFrom'  data-parsley-date data-parsley-required='true' placeholder='date only' class='datepicker'";
				break;
			case 'DateTo':
				$validation = "data-parsley-trigger='focus' id='DateTo' data-parsley-date data-parsley-required='true' placeholder='date only' class='datepicker'";
				break;
			case 'link':
				$validation = "data-parsley-trigger='change' data-parsley-required='true' data-parsley-type='url'";
				break;
			case 'year':
				$validation = "data-parsley-trigger='change' data-parsley-required='true' data-parsley-type='number' data-min='4'";
				break;
			case 'password_1st_field':
				$validation = "data-parsley-trigger='change' data-parsley-required='true' placeholder='required field' id='eqalToModel' data-parsley-equalto='#eqalToModel'";
				break;
			case 'password_2nd_field':
				$validation = "data-parsley-trigger='change' data-parsley-required='true' placeholder='required field' id='data-parsley-equalto' data-parsley-equalto='#eqalToModel'";
				break;
			case 'password_1st_field_optional':
				$validation = "data-parsley-trigger='change' id='eqalToModel' data-parsley-equalto='#eqalToModel'";
				break;
			case 'password_2nd_field_optional':
				$validation = "data-parsley-trigger='focusin focusout' id='data-parsley-equalto' data-parsley-equalto='#eqalToModel'";
				break;
		}
		
		if($type=="text")
		{	
			$field  = "";
			$field .= "<h2> $label </h2>";
			$field .= "<input name='$name' value='$value' type='text' $validation $disable> ";
		}
		elseif($type=="text_short")
		{	
			$field  = "<div class='fl' style='margin-left:10px;'>";
			$field .= "<h2 class='form'> $label </h2>";
			$field .= "
							<input name='$name' value='$value' type='text' class='fill-input2' $validation $disable>    ";
			$field .= "</div>";
		}
		elseif($type=="select")
		{
				$field = "";
				$field .= "<h2> $label </h2>";
				$field .= "<select name='$name' class='fl' style='width:100%;' $validation $disable>";

				
				if($value!=0){
					$sql ="SELECT parentID  
							FROM premiumItemTypeRef WHERE childID='$value'";

					$query = $this->db->query($sql);
					$row = $query->row();
					$value = $row->parentID;
				}
				
				
				$field .=  	$this->menu_parent($value);		
				$field .= "</select>";
			
		}
		elseif($type=="select_premium")
		{
			$field  = "";
				$field .= "<h2> $label </h2>"; 
				$field .= "<select name='$name' class='fl' style='width:100%;' $validation $disable>";
				$field .=  	$this->menu_parent($value);		
				$field .= "</select>";
		}
		elseif($type=='textarea')
		{
			$field  = "";
			$field .= "<h2> $label </h2>";
			$field .= "<textarea name='$name' rows='3' $validation $disable>$value</textarea> ";
	
		}
		elseif($type=='file')
		{
			$field  = "";
			$field .= "<h2> $label </h2>";
			$field .= "<input type='FILE' name='$name'> ";
			
		}
		elseif($type=='multiple_files')
		{
			$field  = "";
			$field .= "<h2> $label </h2>";
			$field .= " <input type='FILE' name='$name' id='".str_replace('[]','',$name)."ID' multiple='true' $validation style='margin-bottom:10px;'/> ";
		}
		elseif($type=='password')
		{
			$field  = "";
			$field .= "<h2> $label </h2>";
			$field .= "<input type='password' name='$name' $validation>";
			
		}
		elseif($type=='publish')
		{
			$field  = "";
			$field .= "<h2> $label </h2>";
			$field .= ""; 
					$field .= "<select name='$name' class='fl' style='width:100%;'>";
					
					$s='';
					if($value=='y')
					{
						$s = "selected";
					}
					$field .=  	"<option $s value='y'> Yes </option>";	
					$s='';
					if($value=='n')
					{
						$s = "selected";
					}
					$field .=  	"<option $s value='n'> No  </option>";	
					$field .= "</select>";   
			$field .= "";
			
		}
		
		
		return $field;
	}
	/*DEFAULT*/
	
	
	/*PREMIUM MULTI LEVEL FORM*/
	function menu_parent($value)
	{
	  $this->load->database();
	  $sql ="SELECT premiumItemType.id AS premiumID, premiumItemType.premiumTypeName AS premiumName  
			FROM premiumItemTypeRef INNER JOIN premiumItemType 
			ON premiumItemType.id =  premiumItemTypeRef.childID WHERE premiumItemTypeRef.parentID='0'";

	  $query = $this->db->query($sql);
	  $row = $query->result_array();
	  
	  
	  $sel= "<option  value=''> [Parent] </option>";
	  $sFlag="";
	  //DETERMIN parent
	  if($value==0){  $sel ="<option value=''> [Parent] </option>"; }
	  
		foreach($row as $r)
		{
			$id   =$r['premiumID'];
			$name =$r['premiumName'];
			if($id==$value)	$sFlag="selected";			
			$sel .="<option $sFlag value='$id'>$name</option>";
			$sFlag="";
			$sel .= $this->menu_child($id,'_',$value);
		}
	  
	  return $sel;
	}
	
	function menu_child($id,$extension,$value)
	{
		$this->load->database();
		$sql ="SELECT premiumItemType.id AS premiumID, premiumItemType.premiumTypeName AS premiumName  
			   FROM premiumItemTypeRef INNER JOIN premiumItemType 
			   ON premiumItemType.id =  premiumItemTypeRef.childID WHERE premiumItemTypeRef.parentID='$id'";
		$query = $this->db->query($sql);
		$row = $query->result_array();
		
		$sel="";
		$sFlag="";
		foreach($row as $r)
		{
			$id   =$r['premiumID'];
			$name =$r['premiumName'];
			if($id==$value)	$sFlag="selected";	
			$sel .="<option $sFlag value='$id'>".$extension."".$name."</option>";
			$sFlag="";
			$sel .= $this->menu_child($id,$extension."_",$value);
		}
		
		return $sel;
	}
	/*PREMIUM MULTI LEVEL FORM*/
 
	/*SELECT FUNCTIONS FOR DYNAMIC TABLES*/
	function select($name,$table,$field,$label,$selected,$v,$disable='',$cond='')
	{
	  switch ($v) {
			case 'o':
				$validation = "placeholder='Optional'";
				break;
			case 'r':
				$validation = "data-parsley-trigger='change' data-parsley-required='true' placeholder='Required field'";
				break;
	  }
		
	  $this->load->database();
	  
	  //FILTER
	  $filter =     '';
	  if($table=='country')
		$filter = '';
 
	  $sql = "SELECT * FROM $table $filter $cond ORDER BY $field ASC";
	  $query = $this->db->query($sql);
	  
	  
	  $sel  = "";
		  $sel .= "<h2> $label </h2>";
		  $sel .= "";
		  
			$sel .= "<select name='$name' $disable $validation style='width:100%;float:left'>";
			$sel .= "<option value=''> [SELECT] </option>";
			$sFlag="";
			//DETERMIN parent			  
				foreach($query->result() as $r)
				{
					$id   = $r->id;
					$name = $r->$field;
					if($id==$selected)	$sFlag="selected";			
					$sel .="<option $sFlag value='$id'>$name</option>";
					$sFlag="";
				}
				
			$sel .= "</select>";
		$sel .= "";
	
	  
	  return $sel;
	}
	/*SELECT FUNCTIONS FOR DYNAMIC TABLES*/
	
	function selectPriceRange($name,$table,$field,$label,$selected,$v,$disable='',$cond='')
	{
	  switch ($v) {
			case 'o':
				$validation = "placeholder='Optional'";
				break;
			case 'r':
				$validation = "data-parsley-trigger='change' data-parsley-required='true' placeholder='Required field'";
				break;
	  }
		
	  $this->load->database();
	  $sql = "SELECT *  FROM $table $cond ORDER BY xOrder ASC";
	  $query = $this->db->query($sql);
	  $query = $query->result_array();
	  
	  $sel  = "";
		  $sel .= "<h2> $label </h2>";
		  $sel .= "";
		  
			$sel .= "<select name='$name' $disable $validation style='width:100%;float:left' id='$name'>";
			$sel .= "<option value=''> [SELECT] </option>";
			$sFlag="";
			//DETERMIN parent			  
				foreach($query as $q)
				{
					extract($q);
					if($id==$selected)	$sFlag="selected";
					$sel .="<option $sFlag value='$id'> $level_name: $extra_label </option>";
					$sFlag="";
				}
				
			$sel .= "</select>";
		$sel .= "";
	
	  
	  return $sel;
	}
	
	/*SUBMIT BUTTONS*/
	function form_submit($form_name)
	{
		$field = "<input name='btnsubmit2' type='submit' class='fl sub-link'  onclick='javascript:$(\"#$form_name\").data-parsley(\"validate\")' value='+ Save' style='width:39%;height:63px;color:white;float:left'>";
		return $field;
	}
	
	function form_submit2($form_name)
	{
		$field = "<input name='btnsubmit2' type='submit' class='fl sub-link'  onclick='javascript:$(\"#$form_name\").data-parsley(\"validate\")' value='+ Save' style='width:27%;height:63px;color:white;'>";
		return $field;
	}
	
	function buttons($type,$form_name,$Botton_type='')
	{
		if($Botton_type=='') $Botton_type='submit';
		
		if($type=="save")
		{
			$field = "<input name='btnsubmit2' type='submit' class='nav-REMOTE-btn1 fl' onclick='javascript:$(\"#$form_name\").data-parsley(\"validate\")' value='Save' style='color:white;margin-top:20px;'>";
		}
		elseif($type=="reset")
		{
			$field = "<input name='btnsubmit2' type='reset' class='nav-REMOTE-btn1 fl' value='Reset' style='color:white;margin-top:20px;'>";
		}
		elseif($type=='StopPar')
		{
			$field  = "<div class='fl'>";
			$field  .= "<input name='btnsubmit1' onclick='StopPar()' type='submit' class='nav-REMOTE-btn1 fl' value='Save as draft' style='color:white;margin-top:20px;'> <br/><br/> ";
			$field  .= "<p style='color:white;margin-top:20px;font-size:12px;width:220px;margin-left:5px;'> *Will not check for all the required fields, and will save an item.</p>";
			$field	.= "</div>";
			
		}
		elseif($type=='irrelevant')
		{
			$field  = "<div class='fl'>";
			$field  .= "<input name='btnsubmit1' onclick='ir()' type='submit' class='nav-REMOTE-btn1 fl' value='Disapprove Item' style='color:white;margin-top:20px;'> <br/><br/> ";
			$field  .= "<p style='color:white;margin-top:20px;font-size:12px;width:260px;margin-left:5px;'> *Tag item as irrelevant and email the one who upload it.</p>";
			$field	.= "</div>";
			
		}
		elseif($type=='GoPar')
		{
			$field  = "<div class='fl'>";
			$field  .= "<input name='btnsubmit2' onclick='javascript:$(\"#$form_name\").parsley(\"validate\")' type='submit' class='nav-REMOTE-btn1 fl' value='Save' style='color:white;margin-top:20px;'> <br/><br/> ";
			$field  .= "<p style='color:white;margin-top:20px;font-size:12px;width:164px;margin-left:5px;'> *Will check for all the required fields and will publish the content.</p>";
			$field	.= "</div>";
		}
		elseif($type=='approve_item')
		{
			$field  = "<div class='fl'>";
			$field  .= "<input name='btnsubmit2' onclick='approve_item()' type='$Botton_type' class='nav-REMOTE-btn1 fl' value='Approve Item' style='color:white;margin-top:20px;'> <br/><br/> ";
			$field  .= "<p style='color:white;margin-top:20px;font-size:12px;width:164px;margin-left:5px;'> *Will check for all the required fields and will publish the item.</p>";
			$field	.= "</div>";
		}
		elseif($type=='permanent_delete')
		{
			$field  = "<div class='fl'>";
			$field  .= "<input name='permanent_delete' type='submit' class='nav-REMOTE-btn1 fl' value='Delete Permanently' style='color:white;margin-top:25px;'> <br/><br/> ";
			$field  .= "<p style='color:white;margin-top:20px;font-size:12px;width:274px;margin-left:5px;'> *Delete permanently from item database.</p>";
			$field	.= "</div>";
		}
		elseif($type=='restore_to_item_db')
		{
			$field  = "<div class='fl'>";
			$field  .= "<input name='restore_to_item_db' type='submit' class='nav-REMOTE-btn1 fl' value='Restore Item' style='color:white;margin-top:25px;'> <br/><br/> ";
			$field  .= "<p style='color:white;margin-top:20px;font-size:12px;width:224px;margin-left:5px;'> *Restore to Item database.</p>";
			$field	.= "</div>";
		}
		elseif($type=='save_Item')
		{
			$field  = "<div class='fl'>";
			$field  .= "<input name='btnsubmit2' onclick='javascript:$(\"#$form_name\").parsley(\"validate\")' type='submit' class='nav-REMOTE-btn1 fl' value='Save' style='color:white;margin-top:20px;'> <br/><br/> ";
			$field  .= "<p style='color:white;margin-top:20px;font-size:12px;width:164px;margin-left:5px;'> *Will check for all the required fields and will publish the item.</p>";
			$field	.= "</div>";
		}
		elseif($type=='save_Button')
		{
			$field  = "<div class='fl'>";
			$field  .= "<input name='btnsubmit2' onclick='save_Button()' type='$Botton_type' class='nav-REMOTE-btn1 fl' value='Save' style='color:white;margin-top:20px;'> <br/><br/> ";
			$field  .= "<p style='color:white;margin-top:20px;font-size:12px;width:164px;margin-left:5px;'> *Will check for all the required fields and will publish the item.</p>";
			$field	.= "</div>";
		}
		elseif($type=='tag_as_popular')
		{
			$field  = "<div class='fl'>";
			$field  .= "<input name='tag_as_popular' onclick='tag_as_popular()' type='$Botton_type' class='nav-REMOTE-btn1 fl' value='Tag as Popular' style='color:white;margin-top:20px;'> <br/><br/> ";
			$field  .= "<p style='color:white;margin-top:20px;font-size:12px;width:164px;margin-left:5px;'> *Tag item as Popular Item.</p>";
			$field	.= "</div>";
		}
		elseif($type=='tag_as_unpopular')
		{
			$field  = "<div class='fl'>";
			$field  .= "<input name='tag_as_unpopular' onclick='tag_as_unpopular()' type='$Botton_type' class='nav-REMOTE-btn1 fl' value='Tag as UnPopular' style='color:white;margin-top:20px;'> <br/><br/> ";
			$field  .= "<p style='color:white;margin-top:20px;font-size:12px;width:200px;margin-left:5px;'> *Tag item as not popular Item.</p>";
			$field	.= "</div>";
		}
		elseif($type=='item_for_approval')
		{
			$field  = "<div class='fl'>";
			$field  .= "<input name='btnsubmit2' onclick='item_for_approval()' type='$Botton_type' class='nav-REMOTE-btn1 fl' value='Save Item For Approval' style='color:white;margin-top:20px;'> <br/><br/> ";
			$field  .= "<p style='color:white;margin-top:20px;font-size:12px;width:289px;margin-left:5px;'> *Will check for all the required fields and submit item for approval.</p>";
			$field	.= "</div>";
		}
		elseif($type=='pub')
		{
			$field  = "<div class='fl'>";
			$field  .= "<input name='btnpublish' onclick='javascript:$(\"#$form_name\").parsley(\"validate\")' type='submit' class='nav-REMOTE-btn1 fl' value='Publish' style='color:white;margin-top:20px;'> <br/><br/> ";
			$field  .= "<p style='color:white;margin-top:20px;font-size:12px;width:260px;margin-left:5px;'> * .</p>";
			$field	.= "</div>";
		}
		elseif($type=='Save_and_Notify')
		{
			$field  = "<div class='fl'>";
			$field  .= "<input name='Save_and_Notify' onclick='Save_Notify()' type='submit' class='nav-REMOTE-btn1 fl' value='Save & Notify BU' style='color:white;margin-top:20px;'> <br/><br/> ";
			$field  .= "<p style='color:white;margin-top:20px;font-size:12px;width:260px;margin-left:5px;'> *This will save all campaign information and notify BUs to upload their screening commitees.</p>";
			$field	.= "</div>";
		}
		elseif($type=='Save_iWant')
		{
			$field  = "<div class='fl'>";
			$field  .= "<input name='Save_and_Notify' onclick='javascript:$(\"#$form_name\").parsley(\"validate\")' type='submit' class='nav-REMOTE-btn1 fl' value='Save as Draft' style='color:white;margin-top:20px;'> <br/><br/> ";
			$field  .= "<p style='color:white;margin-top:20px;font-size:12px;width:169px;margin-left:5px;'> *This will save all campaign information.</p>";
			$field	.= "</div>";
		}
		
		
		return $field;
	}
	/*SUBMIT BUTTONS*/
	
	
	function yes_no($name,$value)
	{
		$field = '';
		$field .= "<select name='$name' class='fl' style='width:200px;'>";
			$s='';
			if($value=='y'){ $s = "selected";}
			
			$field .=  	"<option $s value='y'> Yes </option>";	
			$s='';
			if($value=='n'){ $s = "selected"; }
			
			$field .=  	"<option $s value='n'> No  </option>";	
		$field .= "</select>"; 
		
		return $field;
	}
	
	
	function super_adminYN($name,$value)
	{
		$validation = "data-parsley-trigger='change' data-parsley-required='true' placeholder='Required field'";
	
		$field = "";
		$field .= "<select name='$name' $validation style='width:410px;'>";
			
			$field .= "<option value> Select </option>";
			
			$s='';
			if($value=='y'){ $s = "selected";}
			
			$field .=  	"<option $s value='y'> Yes </option>";	
			$s='';
			if($value=='n'){ $s = "selected"; }
			
			$field .=  	"<option $s value='n'> No  </option>";	
		$field .= "</select>"; 
		$field .= ""; 
		
		return $field;
	}
	
	
	function validation_rules($name,$value)
	{
		$validations = array(array('l'=>'o','v'=>'Optional'),
							 array('l'=>'r','v'=>'Required'),
							 array('l'=>'integer','v'=>'Integer'),
							 array('l'=>'r_integer','v'=>'Required Integer'),
							 array('l'=>'email','v'=>'Email Address'),
							 array('l'=>'date','v'=>'Date'),
							 array('l'=>'date2','v'=>'Date 2'),
							 array('l'=>'date3','v'=>'Date 3'),
							 array('l'=>'year','v'=>'Year'),
							 array('l'=>'link','v'=>'Link')
							);
		$field = '';
		$field .= "<select name='$name' class='fl' style='width:200px;'>";
			$sFlag = '';
			foreach($validations as $v)
			{
				extract($v);
				if($l==$value)	$sFlag="selected";
				$field .= "<option value='$l' $sFlag> $v </option>";
				$sFlag = '';
			}
		$field .= "</select>"; 
		
		return $field;
	}
	
	function validation_rules2($name,$POSM_statusID,$POSM_FieldID,$value)
	{
		$validations = array(array('val'=>'o', 		 	'l'=>"o|$POSM_statusID|$POSM_FieldID"			,'v'=>'Optional'),
							 array('val'=>'r', 		 	'l'=>"r|$POSM_statusID|$POSM_FieldID"			,'v'=>'Required'),
							 array('val'=>'integer', 	'l'=>"integer|$POSM_statusID|$POSM_FieldID"		,'v'=>'Integer'),
							 array('val'=>'r_integer', 	'l'=>"r_integer|$POSM_statusID|$POSM_FieldID"	,'v'=>'Required Integer'),
							 array('val'=>'email', 		'l'=>"email|$POSM_statusID|$POSM_FieldID"		,'v'=>'Email Address'),
							 array('val'=>'date', 		'l'=>"date|$POSM_statusID|$POSM_FieldID"		,'v'=>'Date'),
							 array('val'=>'date2', 		'l'=>"date2|$POSM_statusID|$POSM_FieldID"		,'v'=>'Date 2'),
							 array('val'=>'date3', 		'l'=>"date3|$POSM_statusID|$POSM_FieldID"		,'v'=>'Date 3'),
							 array('val'=>'year', 		'l'=>"year|$POSM_statusID|$POSM_FieldID"		,'v'=>'Year'),
							 array('val'=>'link', 		'l'=>"link|$POSM_statusID|$POSM_FieldID"		,'v'=>'Link')
							);
		$field = '';
		$field .= "<select name='$name' class='fl' style='width:200px;'>";
			$sFlag = '';
			foreach($validations as $v)
			{
				extract($v);
				if($val==$value)	$sFlag="selected";
				$field .= "<option value='$l' $sFlag> $v </option>";
				$sFlag = '';
			}
		$field .= "</select>"; 
		
		return $field;
	}
	
	
 }
 
?>