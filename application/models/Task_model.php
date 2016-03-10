<?php
class Task_model extends CI_Model {

        public function __construct()
        {
                $this->load->database();
        }

    public function get_staff_task_hours()
    {
        $query = $this->db->get('Staff_task_hours');
        return $query->result_array();
    }

    public function get_tasks()
    {
        $query = $this->db->get('Tasks');
        return $query->result_array();
    }

	public function save_defect($data){

	}
}