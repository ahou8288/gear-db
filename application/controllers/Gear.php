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

	public function return_gear_fields($deleted=FALSE){
		//Return a list of the fields to display when viewing gear tables
		$gear_fields=array(
			array('Fields'=>'name',		'DisplayName'=>'Gear Name'),
			array('Fields'=>'cat',		'DisplayName'=>'Category'),
			array('Fields'=>'age',		'DisplayName'=>'Item Age'),
			array('Fields'=>'retired',	'DisplayName'=>'Retired'),);
		if ($deleted) array_push($gear_fields,array('Fields'=>'deleted',	'DisplayName'=>'Deleted')); //only show the deleted field if the function argument is TRUE, otherwise by default leave it out.
		return $gear_fields;
	}

	public function view()
	{
        // This function collects all the data from the model to display a few tables to the user.

		$output['data'][0]['page_title']="Open the table you want to view";

		$gear_fields=$this->return_gear_fields();
		$all_gear_table= $this->u_model->get_table('gear');

		$output['data'][0]['row_data']= $this->gear_model->get_avaliable();
		$output['data'][0]['title']='Availiable Gear';
		$output['data'][0]['subtitle']='Gear which is availiable for borrowing';
		$output['data'][0]['Fields']=$gear_fields;
		// dbg($output);
		$output['data'][1]['row_data']= $this->u_model->get_table('gear',array('retired'=>'0'));
		$output['data'][1]['title']='Non Retired Display';
		$output['data'][1]['subtitle']='Gear which is in the locker or borrowed out.';
		$output['data'][1]['Fields']=$gear_fields;

		$output['data'][2]['row_data']=$all_gear_table;
		$output['data'][2]['title']='All gear';
		$output['data'][2]['subtitle']='Including gear which is retired (and no longer in the locker).';
		$output['data'][2]['Fields']=$gear_fields;
		// dbg($output);
        render('gear/view',$output);
	}

	public function edit_table(){
        // Collect the about every entry and display it in a table so that the user can choose which entry to edit.

		$output['data'][0]['page_title']="Click on the gear item you want to edit";

		$gear_fields=$this->return_gear_fields(TRUE);

		$output['data'][0]['row_data']=$this->gear_model->get_stuff();
		$output['data'][0]['title']='Edit gear';
		$output['data'][0]['subtitle']='Click on a row to edit the gear item.';
		$output['data'][0]['Fields']=$gear_fields;
        render('gear/view',$output);
	}

	public function edit($id=null)
	{
		// This function deals with editing an entry or creating a new entry.

		// Collect the options that should be generated as radio buttons in the web page.
		$categories=$this->u_model->get_cat();
		$yes_no=array(
			array(0,'No'),
			array(1,'Yes'),
			);

		//Create an array with a list of fields.
		$output['data']['fields_list']=$gear_fields=array(
				'name'=>array('Gear Name','name',0),
				'age'=>array('Item Age','age',0),
				'type'=>array('Category','type',1, $categories), //As this is a radio button input put a 0 instead of a 1 and include all the options to display
				'retired'=>array('Retired','retired',1, $yes_no),//As this is a radio button input put a 0 instead of a 1 and include all the options to display
				'deleted'=>array('Deleted','deleted',1, $yes_no),//As this is a radio button input put a 0 instead of a 1 and include all the options to display
			);

		if ($id){
			$output['data']['title']="Edit gear in the database";
			//Collect the information specific to this gear item.
			$output['data']['id']=$id;
			$output['data']['url']=base_url("/gear/save/".$id);
			$gear_data= $this->gear_model->get_all_id($id);

			foreach ($output['data']['fields_list'] as $temp_num => $items){//move the data from a seperate array into the same array as the fields.
				//The is way each field also has a value associated with it.
				array_push($output['data']['fields_list'][$temp_num],$gear_data[$temp_num]);
			}
		} else {
			foreach ($output['data']['fields_list'] as $temp_num => $items){
				//put a default value into each field.
				array_push($output['data']['fields_list'][$temp_num],'');
			}
			$output['data']['title']="Insert new gear into the database";
			$output['data']['url']=base_url("/gear/save/");
		}

        render('gear/edit',$output);
	}

	public function save($id=null){
		// Save the information that was added/ changed when editing.
		
		if($id){
			$this->gear_model->edit_asset($_POST,$id);
		} else {
			$this->gear_model->save_new_asset($_POST);
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