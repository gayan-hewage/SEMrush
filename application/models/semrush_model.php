<?php
class Semrush_model extends CI_Model {

        public function __construct()
        {
                $this->load->database();
        }

        public function get_all(){
        	$query = $this->db->get('srh_domain_organic');
        	return $query->result_array();
        }
}