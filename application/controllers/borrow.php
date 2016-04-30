<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class borrow extends CI_Controller {
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

	public function borrow(){
		// Borrow function:
		// get's the data needed to show the user the options for borrowing
		// it gets a list of gear and people
		$output['data']['gear']= $this->gear_model->get_avaliable();
		$output['data']['people']= $this->u_model->get_table('people');
        render('borrow/borrow',$output); //Send the data to the webpage
	}

	public function save($id=null){
		//Save function:
		// Records what gear was selected and what person is borrowing and writes that information to the database.

		// Collect the data the website sent
		$postData['gear']=json_decode($_POST['gear_selected'],TRUE);
		$postData['person']=json_decode($_POST['person_borrowing'],TRUE);
		$borrow_insert_data=array();

		//Go through the data that was sent and change it into the correct format for inserting into the database.
		foreach($postData['gear'] as $val){
			$temp_row=array( // Create a row to add to the array
				'gear_id'		=>$val['id'],
				'person_id'		=>$postData['person']['id'],
				'deposit'		=>$_POST['deposit'],
				'comment'		=>$_POST['comments'],
				'returned'		=>0);
			array_push($borrow_insert_data,$temp_row); // Store the row in the array
		}

		$this->borrow_model->insert($borrow_insert_data); // Send the array with all the data to the model to be added to the database

		redirect('borrow/view'); // Send the user to look at borrow tables once finished.
	}

	public function get_borrow_table(){
		// This function returns a list of fields that are displayed to the user.
		// It is neater to store them in a seperate function (less code clutter)
		return array(
				array('Fields'=>'name',				'DisplayName'=>'Borrower Name'),
				array('Fields'=>'gear_name',		'DisplayName'=>'Item Name'),
				array('Fields'=>'deposit',			'DisplayName'=>'Deposit'),
				array('Fields'=>'date_borrow',		'DisplayName'=>'Date of Borrowing'),
				array('Fields'=>'date_return',		'DisplayName'=>'Date of Returning'),
				array('Fields'=>'returned',			'DisplayName'=>'Returned'),
				// array('Fields'=>'borrow_group_id',	'DisplayName'=>'Borrow Group Number'),
				array('Fields'=>'comment',			'DisplayName'=>'Comment'),
			);
	}

	public function view()
	{
        // This function collects all the data from the model to display a few tables to the user.

		$output['data'][0]['page_title']='Click on a heading to view the tables.';
		$gear_fields=$this->get_borrow_table(); //Get the fields which we normally display from a function

		$output['data'][0]['row_data']= $this->borrow_model->get_stuff(array('returned'=>'0')); // Data in the table is stored here
		$output['data'][0]['title']='Borrowed Gear'; //Heading for the table is stored here
		$output['data'][0]['subtitle']='Borrowed gear which has not been returned yet'; //Sub heading here
		$output['data'][0]['Fields']=$gear_fields; // A list of fields for the table to render.

		$output['data'][1]['row_data']= $this->borrow_model->get_stuff();
		$output['data'][1]['title']='All borrowing events';
		$output['data'][1]['subtitle']='All borrow events recorded in this system are included in this table';
		$output['data'][1]['Fields']=$gear_fields;

		$output['data'][2]['row_data']= $this->borrow_model->get_stuff(array('returned'=>'1'));
		$output['data'][2]['title']='Previously Returned Gear';
		$output['data'][2]['subtitle']='This is a record of when gear has been returned';
		$output['data'][2]['Fields']=$gear_fields;

		$output['data'][3]['row_data']= $this->borrow_model->get_overdue(14);
		$output['data'][3]['title']='Overdue Gear';
		$output['data'][3]['subtitle']='Borrowed gear which has not been returned yet and is overdue, and also gear that was overdue when returned';
		$output['data'][3]['Fields']=$gear_fields;
		// dbg($output);
        render('borrow/view',$output); //Send all the data to the view to be made into a webpage
	}

	public function view_return()
	{
		// This function shows a table which lets users pick out gear to return.
		$output['data']['rows']= $this->borrow_model->get_stuff(array('returned'=>'0'));
		$output['data']['Fields']=$this->get_borrow_table();
        render('borrow/return_table',$output);
	}

	public function gear_return($id){
		// This function collects the information about a group of gear which was all borrowed at once.
		// This function sends that information to the return page so that the user can choose which items to return.
		if ($id){
			$output['data']['gear']=$this->borrow_model->borrow_group_gear($id);
			// dbg($output);
			render('borrow/return',$output);
		} else {
			redirect('borrow/view');
		}
	}

	public function save_return(){
		// This function collects the data about what gear the user has returned.
		// It then returns these items one by one.
		// Read the data which the webpage sent.
		$postData['gear']=json_decode($_POST['selectedGear'],TRUE);

		// Find out the borrow group this gear belongs to.
		$borrow_group=$postData['gear'][0]['borrow_group_id'];

		// Use a for loop to return each gear item seperately.
		foreach ($postData['gear'] as $temp_row){
			$this->borrow_model->process_return($borrow_group,$temp_row['gear_id']);
		}

		//Check if borrow group is completely returned.
		$remaining_gear=$this->u_model->get_table('borrow',array(
			'returned'=>0,
			'borrow_group_id'=>$borrow_group
			));
		
		// dbg(sizeof($remaining_gear));

		if (sizeof($remaining_gear)==0){
			// dbg(sizeof($remaining_gear));
			render('borrow/return_finished',$postData['gear'][0]);
		} else {
			redirect('borrow/view');
		}
	}
	public function email_list(){
		// Print out the email addresses of people with overdue gear.

		$overdue['email_list']=$this->borrow_model->get_overdue(14,TRUE); //Get a list of the overdue items
		// dbg($overdue);
		render('borrow/email_list',$overdue);
	}
}