<?php
class u_model extends CI_Model {

	function __construct()
	{
		// Call the Model constructor
		parent::__construct();
		$this->load->database();
	}
	public function get_table($table,$array=array()){
		//This function is used to return info about a table specified by the user.
		//USAGE;
		//$this->u_model->get_table('gear',array('retired'=>'0')); would return all the things in the gear table in the database where they were not retired (eg retired=0) and not deleted.

		$array['deleted']='0'; //Never return deleted items.

		if ($table=='gear'){ //If it is the gear table include the categories
			$this->db->select('gear.*, categories.name as cat');
			$this->db->join('categories','categories.id = gear.type');
		} else { //Otherwise return every field
			$this->db->select('*');
		}

		$this->db->from($table);
		$this->db->where($array); //Apply the conditions specified.
		$query=$this->db->get();
		return $query->result_array();
	}

	public function get_cat(){
		//This function returns all the categores.
		$query=$this->db->get('categories');
		$cat= $query->result_array();

		$output=array();
		foreach ($cat as $tmp){ //The format is changed to suit how the data is used.
			array_push($output, array($tmp['id'],$tmp['name']));
		}
		// dbg($output); //uncomment this line to see what the function outputs.
		return $output;
	}
}