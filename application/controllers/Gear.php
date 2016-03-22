<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Gear extends CI_Controller {
	public function __construct()
	{
        parent::__construct();
		// echo("test");
		$this->load->helper('application_helper');
		$this->load->model('gear_model');
        $this->load->view('templates/header');
        $this->load->view('templates/footer');
	}
	public function view()
	{
		$output['data']['gear']= $this->gear_model->get_stuff();
		// dbg($output);
        render('gear/view',$output);
	}

	public function edit()
	{
		// $output['data']['gear']= $this->gear_model->get_stuff();
		$output['data']['fields_list']=array('age','name','type');
		// dbg($output);
        render('gear/edit',$output);
	}
}