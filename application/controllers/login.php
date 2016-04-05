<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class login extends CI_Controller {
	public function __construct()
	{
        parent::__construct();
		$this->load->helper('application_helper');
		$this->load->model('admin_model');
        $this->load->view('templates/header');
        $this->load->view('templates/footer');
	}

	public function login()
	{
		render('login/login');
	}

	public function check_details(){
		$userStatus=$this->admin_model->get_admin_level($_POST);
		// dbg($userStatus);
		if ($userStatus['admin']>0){
			if (session_status() == PHP_SESSION_NONE) {
			    session_start();
			}
			$_SESSION['admin']=$userStatus['admin'];
			redirect('gear/view');
		} else {
			render('login/login');
		}
	}

		public function logout()
	{
		session_destroy();
		render('login/logout');
	}
}