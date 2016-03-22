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
        $query = $this->db->get('people');
        return $query->result();
    }

/*    public function save_new_asset($data){
        $this->db->insert('gear',$data);
    }

    public function edit_asset($data, $id){
        $this->db->where('ID',$id);
        $this->db->update('gear',$data);
    }*/
}