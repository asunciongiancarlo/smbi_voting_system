<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Fv extends CI_Controller
 { 
	public function __construct()
    {
		parent::__construct();
		$this->load->model('c3model');
		$this->load->library('security');
    }

	function label($id)
	{
		$sql ="SELECT label FROM table_fields WHERE id ='$id'";
		$query = $this->db->query($sql);
		$row = $query->row();
		return $row->label;
	}
	
	function v($id)
	{
		$sql ="SELECT validation_rule FROM table_fields WHERE id ='$id'";
		$query = $this->db->query($sql);
		$row = $query->row();
		return $row->validation_rule;
	}
	
	function v2($POSM_statusID,$POSM_FieldID)
	{
		$sql ="SELECT validation FROM POSM_status_fields WHERE POSM_statusID = $POSM_statusID AND POSM_FieldID = $POSM_FieldID LIMIT 0,1";
		$query = $this->db->query($sql);
		$row   = $query->row();
		if($row) return $row->validation;
		else 	 return "o";
	}
	
	function sh($id)
	{
		$sql ="SELECT show_hide FROM table_fields WHERE id ='$id'";
		$query = $this->db->query($sql);
		$row = $query->row();
		return $row->show_hide;
	}
	
	function fieldChecker($POSM_statusID,$POSM_FieldID)
	{
	
		$sql ="SELECT * FROM POSM_status_fields WHERE POSM_statusID = $POSM_statusID AND POSM_FieldID = $POSM_FieldID";
		$query = $this->db->query($sql);
		$row = $query->result_array();
	
		
		if($row!= NULL)
		{
			return "y";
		}
	}
	
	function ecItemField_Checker($fieldID)
	{
	
		$sql ="SELECT id FROM ec_item_fields WHERE id = $fieldID AND active=1";
		$query = $this->db->query($sql);
		$row = $query->result_array();
	
		
		if($row!= NULL)
		{
			return "y";
		}
	}
	
 }
 
?>