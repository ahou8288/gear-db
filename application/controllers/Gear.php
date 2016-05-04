<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Gear extends CI_Controller {
	public function __construct()
	{
        parent::__construct();
        //Load the models (which handle database queries)
		$this->load->helper('application_helper');
		$this->load->model('gear_model');
		$this->load->model('u_model');
		
		//Check that the user has the correct rights to access the page, otherwise redirect to login page
        if ($_SESSION['admin']!=1){
			redirect('login/login');
		}
	}

	public function view()
	{
        // This function collects all the data from the model to display a few tables to the user.
		$gear_fields=$this->gear_model->get_fields(FALSE,TRUE,FALSE,TRUE);
		$all_gear_table= $this->u_model->get_table('gear');

		$availiable_gear=$this->gear_model->get_avaliable();
		$availiable_list=array();
		foreach($availiable_gear as $gear_item){
			$availiable_list[$gear_item['id']]=TRUE;
		}

		foreach($all_gear_table as $index => $gear_item){
			if (array_key_exists($gear_item['id'], $availiable_list)){
				$all_gear_table[$index]['availiable']="Yes";
			} else {
				$all_gear_table[$index]['availiable']="No";
			}
		}

		$output['data']['row_data']=$all_gear_table;
		$output['data']['title']='All Gear';
		$output['data']['subtitle']='This table displays all the gear in the database.';
		$output['data']['fields']=$gear_fields;
		$output['data']['url']='';
		$output['data']['url_id']='';
		// dbg($output);
        render('gear/view',$output);
	}

	public function edit_table(){
        // Collect the about every entry and display it in a table so that the user can choose which entry to edit.

		$gear_fields=$this->gear_model->get_fields(TRUE,TRUE,FALSE,FALSE);

		$output['data']['row_data']=$this->gear_model->get_stuff();
		$output['data']['title']='Edit gear';
		$output['data']['subtitle']='Click on a row to edit the gear item.';
		$output['data']['fields']=$gear_fields;
		$output['data']['url']='edit/';
		$output['data']['url_id']='id';
        render('gear/view',$output);
	}

	public function edit($id=null)
	{
		// This function deals with editing an entry or creating a new entry.

		//Create an array with a list of fields.
		$output['data']['fields_list']=$this->gear_model->get_fields(TRUE,TRUE,TRUE,FALSE);
		if ($id){
			$output['data']['title']="Edit gear in the database";
			//Collect the information specific to this gear item.
			$output['data']['id']=$id;
			$output['data']['url']=base_url("/gear/save/".$id);
			$gear_data= $this->gear_model->get_all_id($id);
			foreach ($output['data']['fields_list'] as $temp_num => $items){//move the data from a seperate array into the same array as the fields.
				//The is way each field also has a value associated with it.
				if ($items['radio']=='0'){
					$output['data']['fields_list'][$temp_num]['value']=$gear_data[$items['name']];
				} else {
					$output['data']['fields_list'][$temp_num]['value']=$gear_data[$items['post_name']];
				}
			}
		} else {
			foreach ($output['data']['fields_list'] as $temp_num => $items){
				//put a default value into each field.
				$output['data']['fields_list'][$temp_num]['value']='';
			}
			$output['data']['title']="Insert new gear into the database";
			$output['data']['url']=base_url("/gear/save/");
		}
		// dbg($output);
        render('gear/edit',$output);
	}

	public function save($id=null){
		// Save the information that was added/ changed when editing.
		// dbg($_POST);
		if($id){
			$this->gear_model->edit_gear($_POST,$id);
		} else {
			$this->gear_model->save_new_gear($_POST);
		}
		redirect('gear/edit_table');
	}

	public function backup(){
		// Save the information that was added/ changed when editing.
		
        $output['message']="Database backup download initiated.";
        render('gear/download',$output);
	}

	public function download()
	{
		$this->load->dbutil();

        $prefs = array(     
                'format'      => 'zip',             
                'filename'    => 'gear_db.sql'
              );

        $db_name = 'backup-on-'. date("Y-m-d-H-i-s") .'.zip';
        $this->load->helper('download');
        force_download($db_name, $this->dbutil->backup($prefs)); 

	}
}