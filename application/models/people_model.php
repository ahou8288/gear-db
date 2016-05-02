<?php
class People_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        $this->load->database();
    }
    
    function get_fields($deleted=FALSE,$radio=FALSE){
        $query = $this->db->query('
            SHOW FIELDS
            FROM people');

        $tmp=$query->result_array();

        // A list of the fields which should not be displayed
        $non_display=array('id'=>FALSE);
        if (!$deleted) $non_display['deleted']=FALSE; 

        // These names appear at the top of datatables as column headings
        $display_names=array(
            'name'=>'Name',
            'deleted'=>'Deleted',);

        /*
        Format of $radio_inputs is array('field_name',
            array(array('option 1 value','option 1 text'),
            array('option 2 value','option 2 text')),
            'post_field'
            );

        Also, I want the input for category to post (during form input) to the type column, so the post_filed option is used for that.
        */
        $yes_no=array(array(0,'No'),array(1,'Yes'));
        $radio_inputs=array(
            'deleted'=> array($yes_no,'deleted'),
            );

        $output=array();

        foreach ($tmp as $sql_field){
            $field_name=$sql_field['Field'];
            if (!isset($non_display[$field_name])){ //Only deal with the fields which will be displayed
                $entry['name']=$field_name;// Create a space to build all the info needed
                if (array_key_exists($field_name, $display_names)){
                    $entry['display']=$display_names[$field_name];
                } else {
                    $entry['display']=$field_name;
                }
                if ($radio){
                    if (isset($radio_inputs[$field_name])){ // Fill in all the info about the radio buttons
                        $radio_info=&$radio_inputs[$field_name];
                        $entry['radio']=1;
                        $entry['post_name']=$radio_info[1];
                        $entry['options']=$radio_info[0];
                        $entry['radio']=1;
                    } else {
                        $entry['radio']=0;
                    }
                }
                array_push($output,$entry);
            }
        }

        // dbg($output);
        return $output;
    }

    function get_stuff()
    {
        //this function gets everything from the people table
        $query = $this->db->get('people');
        return $query->result();
    }

    function get_all_id($id)
    {
        //This function gets the person with a specific id from the database
        $this->db->where('id',$id);
        $query = $this->db->get('people');
        return $query->row_array();
    }

    public function save_new_asset($data){
        // This function inserts a new person into the database
        $this->db->insert('people',$data);
    }

    public function edit_asset($data, $id){
        // This function changes a person's information in the database
        $this->db->where('ID',$id);
        $this->db->update('people',$data);
    }
}