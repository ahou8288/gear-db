<?php
class borrow_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        $this->load->database();
    }
    
    function largest_borrow_number()
    {
        $this->db->select_max('borrow_group_id');
        $query = $this->db->get('borrow');
        return $query->row_array();
    }

    public function insert($data){
        $borrow_number=$this->largest_borrow_number()['borrow_group_id']+1;
        // dbg($borrow_number);
        foreach($data as $entry){
            $this->db->set('date_borrow', 'NOW()', FALSE);
            $this->db->set('borrow_group_id',$borrow_number);
            $this->db->insert('borrow',$entry);
        }
    }

    function get_stuff()
    {
        $this->db->select('borrow.*, gear.name as gear_name, people.name');
        $this->db->join('gear','gear.id = borrow.gear_id');
        $this->db->join('people','people.id = borrow.person_id');
        $query = $this->db->get('borrow');
        return $query->result();
    }

    // public function edit_asset($data, $id){
    //     $this->db->where('ID',$id);
    //     $this->db->update('gear',$data);
    // }
}