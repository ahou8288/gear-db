<?php
class admin_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        $this->load->database();
    }

    public function get_admin_level($post){
        // This function checks a users credentials against users in the database
        $username=$post['username'];
        $password_hash=md5($post['password']); //Use a weak password hash so that plain text is not stored in the database and passwords are not exposed.
        // dbg($password_hash);
        // SQL query;
        $this->db->select('*');
        $this->db->from('user');
        $this->db->where('username',$username);
        $this->db->where('password',$password_hash);
        $query=$this->db->get();

        //Return the result of the query
        return $query->row_array();
    }
}