<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Semrush API Class
 *
 * This class enables interaction with semrush API servies
 *
 * @package		Criticone
 * @subpackage	Libraries
 * @category	Libraries
 * @author		Gayan Chathuranga <gayan@about.me>
 * @link		http://codeigniter.com/user_guide/libraries/calendar.html
 */

class Semrush {

	/* ######################################################################
   ################# Set some parameters ################################
   ###################################################################### */
	
	// 1) $query - Your request. This can be one of three:
	// 		1) domain name, e.g.: ebay.com
	// 		2) keyphrase, e.g.: money
	// 		3) URL, e.g.: http://www.ebay.com/
	public $query		= 'ebay.com';
	
	// 2) $type - Type of report. This can take different values, depending on the query.
	// 
	// If your query is a domain name, the $type can be:
	// 		1) domain_organic
	// 		2) domain_adwords
	// 		3) domain_organic_organic
	// 		4) domain_adwords_adwords
	// 		5) domain_organic_adwords
	// 		6) domain_adwords_organic
	// 		7) domain_rank
	// 
	// If your query is a keyphrase, the $type can be:
	//		1) phrase_related
	//		2) phrase_this
	// 
	// If your query is URL, the $type can be:
	//		1) url_organic
	//		2) url_adwords
	public $type		= 'domain_organic';
	
	// 3) $request_type - type of the request. This shows what type of query you are using.
	// This can be, depending on your query:
	//		1) domain
	//		2) phrase
	//		3) url
	public $request_type = 'domain';
	
	// 4) $api_key - Your unique SEMRush API key
	//
	private $api_key	= 'd1908bae291563e851469af1cba70741';
	
	// 5) $db - Database (for the moment can be: en, uk, ru, de, fr, es)
	public $db			= 'us';
	
	// 6) $limit - How many results to return
	public $limit		= 10;
	
	// 7) $offset - How many results to skip from the beginning
	public $offset		= 0;
	
	// 8) $export_columns - which columns should be returened in what order
	// Values, replicating SEMRush web interface by report type:
	// 		1) for domain_organic - $export_columns=Ph,Po,Nq,Cp,Ur,Tr,Tc,Co,Nr,Td
	// 		2) for domain_adwords - $export_columns=Ph,Po,Nq,Cp,Vu,Tr,Tc,Co,Nr,Td
	// 		3) for domain_organic_organic - $export_columns=Dn,Np,Or,Ot,Oc,Ad
	// 		4) for domain_adwords_adwords - $export_columns=Dn,Np,Ad,At,Ac,Or
	// 		5) for domain_organic_adwords - $export_columns=Dn,Np,Ad,At,Ac,Or
	// 		6) for domain_adwords_organic - $export_columns=Dn,Np,Or,Ot,Oc,Ad
	// 		7) for domain_rank - $export_columns=Dn,Rk,Or,Ot,Oc,Ad,At,Ac
	//		8) for phrase_related - $export_columns=Ph,Nq,Cp,Co,Nr,Td
	//		9) for phrase_this - $export_columns=Ph,Nq,Cp,Co,Nr
	//		10) for url_organic - $export_columns=Ph,Po,Nq,Cp,Co,Tr,Tc,Nr,Td
	//		11) for url_adwords - $export_columns=Ph,Po,Nq,Cp,Co,Tr,Tc,Nr,Td
	public $export_columns = 'Ph,Po,Nq,Cp,Ur,Tr,Tc,Co,Nr,Td';
	

	public function set_data($config){
		$this->db = $config['db'];
		$this->type = $config['type'];
		$this->request_type = $config['request_type'];
		$this->query = $config['query'];
		$this->limit = $config['limit'];
		$this->offset = $config['offset'];
		$this->export_columns = $config['export_columns'];
	}
	/**
	 * Class API request perform
	 *
	 * Loads the calendar language file and sets the default time reference.
	 *
	 * @uses	
	 *
	 * @param	array	$params	request parameters
	 * @return	void
	 */
    public function performRequest ( $params )
	{
		$url				 = 'http://' . $this->db . '.api.semrush.com/?action=report&type=' . $this->type . '&' . $this->request_type . '=' . urlencode($this->query) . '&key=' . $this->api_key . '&display_limit=' . $this->limit . '&display_offset=' . $this->offset . '&export=api&export_columns=' . $this->export_columns;
		$cUrl				 = curl_init			();
							   curl_setopt			( $cUrl, CURLOPT_URL, $url );
							   curl_setopt			( $cUrl, CURLOPT_RETURNTRANSFER, 1 );
							   curl_setopt			( $cUrl, CURLOPT_TIMEOUT, 30 );
							   curl_setopt			( $cUrl, CURLOPT_HTTPHEADER, array ( 'X-Real-IP', $params [ 'uip' ] ) );
		$answer				 = curl_exec			( $cUrl );
		if ( curl_getinfo ( $cUrl, CURLINFO_HTTP_CODE ) == 200 )
		{
							   curl_close			( $cUrl );
			return $answer;	// Return request results
		}
		elseif ( curl_errno ( $cUrl ) && curl_errno ( $cUrl ) != 28 )
		{
							   curl_close			( $cUrl );
			return false;	// Error occured during request
		}
							   curl_close			( $cUrl );
		return false;		// Request timed out
	}


	public function filterData($result){
		$api_data = array();

		if ( preg_match ( "/^ERROR\s[0-9]+\s::[a-zA-Z0-9\s]+/i", $result ) )
		{
			return $result;
			
		}else{

			$data = explode ( "\n", trim ( $result ) );
			$fields = explode ( ";", array_shift ( $data ) );
			
			if ( count ( $data ) > 0 )
			{
				$field_data = array();
				foreach ( $fields as $field )
				{
					$field_data[] = $field ;
				}
				array_push($api_data, $field_data);

				foreach ( $data as $line )
				{
					$values = explode ( ";", $line, count ( $fields ) );
					$line_data = array();
					foreach ( $values as $value )
					{
						$line_data[] = $value;

					}
					array_push($api_data, $line_data);
				}
			}
			return $api_data;
		}


	}
	
}

/* End of file Someclass.php */