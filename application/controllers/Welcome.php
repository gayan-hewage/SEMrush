<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function __construct(){
		parent::__construct();
		$this->load->library('semrush');
		$this->load->model('semrush_model');
	}
	public function index()
	{
		
		// $data = array(
		// 	'domain_data' => $this->semrush_model->get_all()
		// );

		$this->semrush->set_data(array(
	        'query' => 'amazon.com',
	        'type' => 'domain_organic',
	        'request_type' => 'domain',
	        'db' => 'us',
	        'limit' => 10,
	        'offset' => 0,
	        'export_columns' => 'Ph,Po,Nq,Cp,Ur,Tr,Tc,Co,Nr,Td'
   		));
		
		$data = array(
			'domain_data' => $this->semrush->performRequest($parm = array('uip' => $_SERVER['SERVER_ADDR']))
		);
		
		$this->load->view('welcome_message',$data);
	}
}
