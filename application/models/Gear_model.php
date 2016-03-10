<?php
class gear_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        $this->load->database();
    }
    
    function get_stuff()
    {
        $query = $this->db->get('gear');
        return $query->result();
    }
}