<?php 
class C3model extends CI_Model{

	public function __construct()
	{
		$this->load->database();
	}
	

	public function c3crud($action,$table="",$fields="",$id="",$sql="")
	{
		
		$maxID[0]['id'] = "";
		$id_name = "id";

		if ($action=='insert')
		{
			
			//INSERT INTO DATABASE
			$response = $this->db->insert($table,$fields);
			$last_query = $this->db->last_query();
			
			//GET LAST INSERTED ID
			//echo "SELECT MAX($id_name) as id FROM $table";
			/*
			$query = $this->db->query("SELECT MAX($id_name) as id FROM $table");
			$maxID = $query->result_array();
			$id = $maxID[0]['id'];
			
			$this->client_activities($id,$table,$last_query);
			*/
		}
		elseif ($action=='select')
		{
			 
		  $query = $this->db->query($sql);
		  $response = $query->result_array();
			 
		}
		elseif ($action=='no-res')
		{
			 
		  $query = $this->db->query($sql);
		  $response="";	 
		  
		}
		elseif ($action=='delete')
		{
			
		    $this->db->where($id_name,$id);
			$response = $this->db->delete($table);
			$last_query = $this->db->last_query();
			
			//$this->client_activities($id,$table,$last_query);
		}
		elseif($action=='update')
		{
			if(is_array($id))
			{
				$response = $this->db->update($table,$fields,$id); 
				$last_query = $this->db->last_query();
			}
			else
			{
				$this->db->where($id_name,$id);
				$response = $this->db->update($table,$fields);
				
				//SAVE INTO CLIENTS ACTIVITIES
				$last_query = $this->db->last_query();
				//$this->client_activities($id,$table,$last_query);
			}
			
		}
		
		return $response;
	}
	
	function client_activities($id='',$table='',$last_query='')
	{
		//SET DATE
		$this->load->helper('date');
				
		$date = "%Y-%m-%d";
		$time = "%h:%i:%s"; 
		$date = mdate($date,time());
		$time = mdate($time,time());
					
		$dbFields['fsan_rec_id'] = $id;
		$dbFields['fsan_type'] 	= $table;
		$dbFields['fsan_action'] = $last_query;
		$dbFields['fsan_date'] 	= $date;
		$dbFields['fsan_time'] 	= $time;
		$dbFields['fsan_ip_address'] = $_SERVER['REMOTE_ADDR'];
		$dbFields['fsan_http_referer'] = $_SERVER['HTTP_REFERER'];
		$dbFields['fsan_user_id'] = isset( $_SESSION['user_id']) ? $_SESSION['user_id']:"-1" ;
		$this->db->insert('mesa_client_activities',$dbFields);
	}
	
	
	function getMetaData($table)
	  {
	    $fields = $this->db->field_data($table);
		foreach ($fields as $field)
		{
		   echo $field->name;
		   echo $field->type;
		   echo $field->max_length;
		   echo $field->primary_key;
		}
	 }
	 

}
?>