<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class people extends CI_Controller {
	public function __construct()
	{
        parent::__construct();
        //Load the models (which handle database queries)
		$this->load->model('gear_model');
		$this->load->model('people_model');
		$this->load->model('borrow_model');
		$this->load->model('u_model');

		//Check that the user has the correct rights to access the page, otherwise redirect to login page
		if ($_SESSION['admin']!=1){
			redirect('login/login');
		}
	}

	public function get_borrow_table($deleted=TRUE){
		// This function returns a list of fields that are displayed to the user.
		// It is neater to store them in a seperate function (less code clutter)
		$fields=array(array('Fields'=>'name', 'DisplayName'=>'Name'));
		if ($deleted) {
				array_push($fields,array('Fields'=>'deleted','DisplayName'=>'Deleted'));
		}
		return $fields;
	}

	public function view()
	{
        // This function collects all the data from the model to display a few tables to the user.
        
		$gear_fields=$this->get_borrow_table(FALSE);
		$output['data'][0]['page_title']='People Information';

		$output['data'][0]['row_data']= $this->u_model->get_table('people');
		$output['data'][0]['title']='All people';
		$output['data'][0]['subtitle']='People in the database are visible here unless they are deleted.';
		$output['data'][0]['Fields']=$gear_fields;
		// dbg($output);
        render('gear/view',$output);
	}
	public function edit_table()
	{
        // Collect the about every entry and display it in a table so that the user can choose which entry to edit.

		$gear_fields=$this->get_borrow_table();
		$output['data'][0]['page_title']='Edit people';

		$output['data'][0]['row_data']= $this->people_model->get_stuff();
		$output['data'][0]['title']='Edit people';
		$output['data'][0]['subtitle']='All people in the database are visible here.';
		$output['data'][0]['Fields']=$gear_fields;
		// dbg($output);
        render('gear/view',$output);
	}

	public function edit($id=null)
	{
		// This function deals with editing an entry or creating a new entry.

		$output['data']['fields_list']=array( //create a list of fields with info display name and also the database name of each column.
				'name'=>array('Person Name','name',0),
				'deleted'=>array('Deleted','deleted',1,array(array(0,'No'),array(1,'Yes'),)), //The number 1 means that this item is a radio button. (yes/no)
			);

		if ($id){
			// Put the data relating to the item in the variable $output.
			$output['data']['id']=$id;
			$output['data']['title']="Edit people";
			$output['data']['url']=base_url("/people/save/".$id);
			$gear_data= $this->people_model->get_all_id($id);

			foreach ($output['data']['fields_list'] as $temp_num => $items){ //move the data from a seperate array into the same array as the fields.
				//The is way each field also has a value associated with it.
				array_push($output['data']['fields_list'][$temp_num],$gear_data[$temp_num]);
			}
		} else {
			foreach ($output['data']['fields_list'] as $temp_num => $items){
				//put a default value into each field.
				array_push($output['data']['fields_list'][$temp_num],'');
			}
			$output['data']['title']="Insert new person";
			$output['data']['url']=base_url("/people/save/");
		}

        render('gear/edit',$output);
	}

	public function save($id=null){
		// Save the information that was added/ changed when editing.
		if($id){
			$this->people_model->edit_asset($_POST,$id);
		} else {
			$this->people_model->save_new_asset($_POST);
		}
		redirect('people/view');
	}
}