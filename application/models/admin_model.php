<?php
class admin_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        $this->load->database();
    }
    public function get_admin_level($post){
        $username=$post['username'];
        $password_hash=md5($post['password']);
        // dbg($password_hash);

        $this->db->select('*');
        $this->db->from('user');
        $this->db->where('username',$username);
        $this->db->where('password',$password_hash);
        $query=$this->db->get();
        return $query->result_array();
    }
}