<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class login extends CI_Controller {
	public function __construct()
	{
        parent::__construct();
		$this->load->helper('application_helper');
        $this->load->view('templates/header');
        $this->load->view('templates/footer');
	}

	public function login()
	{
		render('login/login');
	}

	public function check_details(){
		if ($_POST['username'] == '' && $_POST['password'] == ''){
			if (session_status() == PHP_SESSION_NONE) {
			    session_start();
			}
			$_SESSION['admin']=1;
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