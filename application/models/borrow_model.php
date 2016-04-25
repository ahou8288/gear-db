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
        // This function returns the largest borrow group id that is currently in the database.
        // It is used to allow the program to select a number which has not been used previously.
        $this->db->select_max('borrow_group_id');
        $query = $this->db->get('borrow');
        return $query->row_array();
    }

    public function insert($data){
        // This function is used to insert a new borrow entry (ie record when somebody borrows something)

        // The data is all entered under 1 borrow number even if there are multiple items
        // This way gear which is borrowed at the same time can be returned at the same time.
        $borrow_number=$this->largest_borrow_number()['borrow_group_id']+1;
        
        // Insert each item individualy
        foreach($data as $entry){
            $this->db->set('date_borrow', 'NOW()', FALSE); //Record the date of borrowing
            $this->db->set('borrow_group_id',$borrow_number);//Record the borrow group number with each item.
            $this->db->insert('borrow',$entry);
        }
    }

    function get_stuff($conditions=null)
    {
        // this function is used to collect data to show in the borrow tables

        $this->db->select('borrow.*, gear.name as gear_name, people.name'); //Chose which fields to display
        $this->db->join('gear','gear.id = borrow.gear_id'); //Link the gear and borrow tables on gear.id
        $this->db->join('people','people.id = borrow.person_id'); //Link the borrow and people tables on people.id
        
        if ($conditions){ //Allow the function call to specify conditions
            $this->db->where($conditions); 
        }
        $this->db->where('borrow.deleted=0'); //Don't include deleted rows (this might not be uniformly done accross all functions)
        $query = $this->db->get('borrow');

        //Return the result of the query
        return $query->result();
    }

    function get_overdue($day_limit,$only_borrowed=null)
    {
        //This function gets the gear which is overdue.
        $this->db->select('borrow.*, gear.name as gear_name, people.name, people.email');
        $this->db->join('gear','gear.id = borrow.gear_id');
        $this->db->join('people','people.id = borrow.person_id');

        if ($only_borrowed) { //If only borrowed argument is selected then only gear which is currently out of the locker will be returned
            $this->db->where('returned = 0 AND CURRENT_DATE() - borrow.date_borrow > '.$day_limit);
        } else { //Otherwise gear that was returned after the due date will be returned as well as gear that is currently overdue.
            $this->db->where('(returned = 1 AND (borrow.date_return - borrow.date_borrow) > '.$day_limit.' ) OR
                (returned = 0 AND CURRENT_DATE() - borrow.date_borrow > '.$day_limit.')');
        }
        $query = $this->db->get('borrow');
        return $query->result_array();
    }

    public function borrow_group_gear($id){
        //This function returns all the gear which is from a specific borrow group.
        $this->db->select('gear.*, borrow.*');
        $this->db->from('borrow');
        $this->db->where('borrow.borrow_group_id',$id);
        $this->db->join('gear','borrow.gear_id=gear.id'); //link the gear table so that more information can be displayed.
        $query=$this->db->get();
        return $query->result_array();
    }
    public function process_return($borrow_group,$gear_id){
        //This function processes the return of a single gear item.
        //It sets returned to 1 and also updates the date of returning gear.
        $this->db->where('borrow_group_id',$borrow_group);
        $this->db->where('gear_id',$gear_id);
        $this->db->set('returned',1);
        $this->db->set('date_return', 'NOW()', FALSE);
        $query=$this->db->update('borrow');
    }
}