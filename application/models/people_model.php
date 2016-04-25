<?php
class People_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        $this->load->database();
    }
    
    function get_stuff()
    {
        //this function gets everything from the people table
        $query = $this->db->get('people');
        return $query->result();
    }

    function get_all_id($id)
    {
        //This function gets the person with a specific id from the database
        $this->db->where('id',$id);
        $query = $this->db->get('people');
        return $query->row_array();
    }

    public function save_new_asset($data){
        // This function inserts a new person into the database
        $this->db->insert('people',$data);
    }

    public function edit_asset($data, $id){
        // This function changes a person's information in the database
        $this->db->where('ID',$id);
        $this->db->update('people',$data);
    }
}