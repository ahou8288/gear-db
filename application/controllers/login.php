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
			$_SESSION['user']=$_POST['username']; //Currently unused, but might be need later
			redirect('login/home');
		} else {
			render('login/login');
		}
	}

	public function logout()
	{
		session_destroy();
		render('login/logout');
	}

	public function home(){
		$links=return_links();

		//Get main links (displayed across page)
		$output['main_links']=array(
			array('title'=>'Gear','link'=>$links['Gear']),
			array('title'=>'Borrow','link'=>$links['Borrow']),
			array('title'=>'People','link'=>$links['People']));
		
		unset($links['Home']);
		unset($links['Gear']);
		unset($links['Borrow']);
		unset($links['People']);
		// Get minor links (displayed below)
		$output['minor_links']=array();
		foreach($links as $title => $link){
			array_push($output['minor_links'],array('title'=>$title,'link'=>$link));
		}
		// dbg($output);
		render('login/home',$output);
	}
}