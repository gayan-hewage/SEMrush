<?php
class Semrush_model extends CI_Model {

        public function __construct()
        {
        	$this->load->library('cimongo');
        }

        public function get_all(){
        	
			$res = $this->cimongo->get('Domain');
            $row_dom_plan = $res->result();
			var_dump($row_dom_plan);
        }
}