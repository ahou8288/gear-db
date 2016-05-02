<?php
class gear_model extends CI_Model {

	function __construct()
	{
		// Call the Model constructor
		parent::__construct();
		$this->load->model('u_model');
		$this->load->database();
	}
	
	function get_fields($deleted=FALSE,$retired=FALSE,$radio=FALSE,$availiable=FALSE){
		$query = $this->db->query('
			SHOW FIELDS
			FROM gear');

		$tmp=$query->result_array();
		array_push($tmp,array('Field'=>'cat'));

		// A list of the fields which should not be displayed
		$non_display=array('id'=>FALSE,'type'=>FALSE);
		if (!$retired) $non_display['retired']=FALSE;
		if (!$deleted) $non_display['deleted']=FALSE; 

		// These names appear at the top of datatables as column headings
		$display_names=array(
			'name'=>'Gear Name',
			'cat'=>'Category',
			'age'=>'Item Age',
			'retired'=>'Retired',
			'deleted'=>'Deleted',);

		/*
		Format of $radio_inputs is array('field_name',
			array(array('option 1 value','option 1 text'),
			array('option 2 value','option 2 text')),
			'post_field'
			);

		Also, I want the input for category to post (during form input) to the type column, so the post_filed option is used for that.
		*/
		$yes_no=array(array(0,'No'),array(1,'Yes'));
		$radio_inputs=array(
			'retired'=> array($yes_no,'retired'),
			'deleted'=> array($yes_no,'deleted'),
			'cat'=>     array($this->u_model->get_cat(),'type'),
			);

		$output=array();

		foreach ($tmp as $sql_field){
			$field_name=$sql_field['Field'];
			if (!isset($non_display[$field_name])){ //Only deal with the fields which will be displayed
				$entry['name']=$field_name;// Create a space to build all the info needed
				if (array_key_exists($field_name, $display_names)){
					$entry['display']=$display_names[$field_name];
				} else {
					$entry['display']=$field_name;
				}
				if ($radio){
					if (isset($radio_inputs[$field_name])){ // Fill in all the info about the radio buttons
						$radio_info=&$radio_inputs[$field_name];
						$entry['radio']=1;
						$entry['post_name']=$radio_info[1];
						$entry['options']=$radio_info[0];
						$entry['radio']=1;
					} else {
						$entry['radio']=0;
					}
				}
				array_push($output,$entry);
			}
		}
		if ($availiable) array_push($output, array(
			'name'=>'availiable',
			'display'=>'Currently _gred'));
		// dbg($output);
		return $output;
	}

	function get_avaliable()
	{
		//This function gets all the gear which is availiable (not currrently borrowed)

		$data=array(); // create a place to store rows

		//Get all the gear that was once borrowed but has been returned.
		$query = $this->db->query('
			SELECT `gear`.*,categories.name as cat
			FROM `gear`
			JOIN categories ON categories.id = gear.type
			LEFT OUTER JOIN `borrow` ON `gear`.`id` = `borrow`.`gear_id`
			WHERE gear.retired = 0
			AND gear.deleted = 0
			GROUP BY `id`
			HAVING (min(`returned`) = 1)');
		$tmp=$query->result_array();
		foreach($tmp as $row){ //store all the rows from the first query
			array_push($data,$row);
		}

		//Get all the gear that was never borrowed.
		$query = $this->db->query('
			SELECT `gear`.*,categories.name as cat
			FROM `gear`
			JOIN categories ON categories.id = gear.type
			LEFT OUTER JOIN `borrow` ON `gear`.`id` = `borrow`.`gear_id`
			WHERE (`returned` IS NULL)
			AND gear.retired = 0
			AND gear.deleted = 0;');
		$tmp=$query->result_array();
		foreach($tmp as $row){ //add the rows from the second query to the rows from the first query
			array_push($data,$row);
		}

		return $data; //Return all the rows that were selected by both querys
	}
	function get_stuff($table='gear',$id=null)
	{
		// This function can be used to get all the information from a table.
		// It works differently if the gear table is selected.

		if ($table=='gear'){ //Include the name of the category as well as the category number.
			$this->db->select('gear.*,categories.name as cat');
			$this->db->join('categories','categories.id=gear.type');
		}
		$query = $this->db->get($table);
		return $query->result();
	}

	function get_all_id($id)
	{
		//This function gets the gear with a specific id.
		//It should return a single item.

		$this->db->select('gear.*,categories.name as cat');
		$this->db->join('categories','categories.id=gear.type');
		$this->db->where('gear.id',$id);
		$query = $this->db->get('gear');
		return $query->row_array();
	}

	public function save_new_asset($data){
		// This function adds new data to the database
		// dbg($data);
		$this->db->insert('gear',$data);
	}

	public function edit_asset($data, $id){
		// This function changes the entry in the database with a specific id.
		$this->db->where('ID',$id);
		$this->db->set($data);
		$this->db->update('gear');
	}
}