<?php
class Defect_model extends CI_Model {

        public function __construct()
        {
                $this->load->database();
        }

    public function get_defect($field = FALSE, $value = FALSE)
    {
        if ($field === FALSE || $value === FALSE){
            $query = $this->db->get('Defects');
            return $query->result_array();
        }
        if($field === 'ID'){
            $query = $this->db->get_where('Defects', array($field => $value));
            return $query->row_array();  
        }

        $this->db->order_by('Type', 'asc');
        $query = $this->db->get_where('Defects', array($field => $value));
        return $query->result_array();
    }

	public function save_defect($data){
        // echo json_encode($data);
        $this->db->where('ID', $data['ID']);
        $this->db->update('Defects', $data);
	}
}