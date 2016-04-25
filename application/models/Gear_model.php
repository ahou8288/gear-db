<?php
class gear_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        $this->load->database();
    }
    
    function get_avaliable()
    {
        //This function gets all the gear which is availiable (not currrently borrowed)

        $data=array(); // create a place to store rows

        //Get all the gear that was once borrowed but has been returned.
        $query = $this->db->query('
            SELECT `gear`.*,categories.name as cat
            FROM `gear`
            JOIN categories ON categories.id = gear.type
            LEFT OUTER JOIN `borrow` ON `gear`.`id` = `borrow`.`gear_id`
            WHERE gear.retired = 0
            AND gear.deleted = 0
            GROUP BY `id`
            HAVING (min(`returned`) = 1)');
        $tmp=$query->result();
        foreach($tmp as $row){ //store all the rows from the first query
            array_push($data,$row);
        }

        //Get all the gear that was never borrowed.
        $query = $this->db->query('
            SELECT `gear`.*,categories.name as cat
            FROM `gear`
            JOIN categories ON categories.id = gear.type
            LEFT OUTER JOIN `borrow` ON `gear`.`id` = `borrow`.`gear_id`
            WHERE (`returned` IS NULL)
            AND gear.retired = 0
            AND gear.deleted = 0;');
        $tmp=$query->result();
        foreach($tmp as $row){ //add the rows from the second query to the rows from the first query
            array_push($data,$row);
        }

        return $data; //Return all the rows that were selected by both querys
    }
    function get_stuff($table='gear',$id=null)
    {
        // This function can be used to get all the information from a table.
        // It works differently if the gear table is selected.

        if ($table=='gear'){ //Include the name of the category as well as the category number.
            $this->db->select('gear.*,categories.name as cat');
            $this->db->join('categories','categories.id=gear.type');
        }
        $query = $this->db->get($table);
        return $query->result();
    }

    function get_all_id($id)
    {
        //This function gets the gear with a specific id.
        //It should return a single item.

        $this->db->select('gear.*,categories.name as cat');
        $this->db->join('categories','categories.id=gear.type');
        $this->db->where('gear.id',$id);
        $query = $this->db->get('gear');
        return $query->row_array();
    }

    public function save_new_asset($data){
        // This function adds new data to the database
        // dbg($data);
        $this->db->insert('gear',$data);
    }

    public function edit_asset($data, $id){
        // This function changes the entry in the database with a specific id.
        $this->db->where('ID',$id);
        $this->db->set($data);
        $this->db->update('gear');
    }
}