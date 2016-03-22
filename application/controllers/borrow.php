<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class borrow extends CI_Controller {
	public function __construct()
	{
        parent::__construct();
		// echo("test");
		$this->load->helper('application_helper');
		$this->load->model('gear_model');
		$this->load->model('people_model');
        $this->load->view('templates/header');
        $this->load->view('templates/footer');
	}

	public function borrow()
	{
		$output['data']['gear']= $this->gear_model->get_stuff();
		$output['data']['people']= $this->people_model->get_stuff();
		// dbg($output);
        render('borrow/borrow',$output);
	}

	public function save($id=null){
		dbg($_POST);
		$postData=json_decode($_POST['gear_selected']);

		// get borrow id
		// get person
		// get deposit (now())
		// get date
		dbg($postData);
		//TODO data validation
		//EG if empty

		if($id){
			// save the edited entry
			$this->gear_model->edit_asset($_POST,$id);
		} else {
			// save the new entry
			unset($_POST[0]); //This randomly gets added and it's easier to fix like this :)
			$this->gear_model->save_new_asset($_POST);
		}
		redirect('gear/view');
	}
}