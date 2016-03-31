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
		session_destroy(0);
		render('login/login')
	}

	public function check_details(){
		if ($_POST['username'] == 'SURMC' && $_POST['password'] == 'SURMC'){
			session_start(0);
			$_SESSION['admin']=1;
			render('gear/view');
		} else {
			render('login/login');
		}
	}
}