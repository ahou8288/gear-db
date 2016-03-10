<?php
class Vision_model extends CI_Model {

        public function __construct()
        {
                
        }

    public function get_LD($startTime, $finishTime, $projectsArray)
    {
        $proj_num_array = array();
        foreach($projectsArray as $proj){
            array_push($proj_num_array, $proj['Project_Number']);
        }

        //dbg($proj_num_array);
        
        $this->db2 = $this->load->database('vision', TRUE);

        //$this->db2->where('WBS1', 'T-14428.04');
        $this->db2->where('Transdate >=', $startTime);
        $this->db2->where('Transdate <=', $finishTime);
        $this->db2->where_in('WBS1', $proj_num_array);
        $query = $this->db2->get('LD');
        return $query->result_array();
        //return "test";

    }

    public function get_LD_summary($projectsArray){
        
        $proj_num_array = array();
        
        foreach($projectsArray as $proj){
            array_push($proj_num_array, $proj['Project_Number']);
        }

        $this->db2 = $this->load->database('vision', TRUE);

        $this->db2->select('WBS1, WBS2, Employee, SUM(RegHrs) as Hours_Total');
        $this->db2->group_by('WBS1, WBS2, Employee');
        
        //Should this condition be added? - how should negative hours be handled?
        //$this->db2->where('RegHrs>0');
        
        $this->db2->where_in('WBS1', $proj_num_array);
        //$this->db2->limit(10);

        $query = $this->db2->get('LD');

        return $query->result_array();
    }

	public function save_defect($data){

	}

    public function get_people_Levels(){
        //Takes an array of people and returns their levels
        $this->db2 = $this->load->database('vision', TRUE);
/*
        $query = $this->db2->query("
            SELECT DISTINCT employees.Employee, employees.BillingCategory
            FROM EM employees
            JOIN LD labour ON labour.Employee = employees.Employee
            WHERE labour.WBS1 LIKE '%14428%'");
*/

        //It is faster to get all the employees
        $query = $this->db2->query("
            SELECT Employee, BillingCategory, FirstName,LastName
            FROM EM
            WHERE TerminationDate IS NULL OR TerminationDate > '01/07/2015'");

        return $query->result_array();
    }
}