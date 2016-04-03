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

        if ($_SESSION['admin']!=1){
			redirect('login/login');
		}
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
		$output['data']['fields_list']=array(
			array('Field'=>'age','DisplayName'=>'Age of gear'),
			array('Field'=>'name','DisplayName'=>'Name'),
			array('Field'=>'type','DisplayName'=>'Item type'));
		// dbg($output);
        render('gear/edit',$output);
	}

	public function save($id=null){
		//TODO data validation
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