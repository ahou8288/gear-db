<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class borrow extends CI_Controller {
	public function __construct()
	{
        parent::__construct();
		$this->load->helper('application_helper');

		$this->load->model('gear_model');
		$this->load->model('people_model');
		$this->load->model('borrow_model');

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
		// dbg($_POST);
		$postData['gear']=json_decode($_POST['gear_selected'],TRUE);
		$postData['person']=json_decode($_POST['person_borrowing'],TRUE);
// dbg($postData);
		$borrow_insert_data=array();

		foreach($postData['gear'] as $val){
			// dbg($val);
			$temp_row=array(
				'gear_id'		=>$val['id'],
				'person_id'		=>$postData['person']['id'],
				'deposit'		=>'$20',
				'returned'		=>0);
			array_push($borrow_insert_data,$temp_row);
		}

		$this->borrow_model->insert($borrow_insert_data);

		redirect('gear/view');
	}
}