<?
/*
	This is a simple script for intergating data
	from SEMRush.com API.
	Please, find more information at http://www.semrush.com/api.html
*/
	
	set_time_limit ( 0 );
	
/* ######################################################################
   ################# Set some parameters ################################
   ###################################################################### */
	
	// 1) $query - Your request. This can be one of three:
	// 		1) domain name, e.g.: ebay.com
	// 		2) keyphrase, e.g.: money
	// 		3) URL, e.g.: http://www.ebay.com/
	$query		= 'ebay.com';
	
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
	$type		= 'domain_organic';
	
	// 3) $request_type - type of the request. This shows what type of query you are using.
	// This can be, depending on your query:
	//		1) domain
	//		2) phrase
	//		3) url
	$request_type = 'domain';
	
	// 4) $api_key - Your unique SEMRush API key
	//
	$api_key	= 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
	
	// 5) $db - Database (for the moment can be: en, uk, ru, de, fr, es)
	$db			= 'us';
	
	// 6) $limit - How many results to return
	$limit		= 10;
	
	// 7) $offset - How many results to skip from the beginning
	$offset		= 0;
	
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
	$export_columns = 'Ph,Po,Nq,Cp,Ur,Tr,Tc,Co,Nr,Td';
	
/* ######################################################################
   ################# End setting parameters #############################
   ###################################################################### */
	
	
	function performRequest ( $params )
	{
		$url				 = 'http://' . $params [ 'db' ] . '.api.semrush.com/?action=report&type=' . $params [ 'type' ] . '&' . $params [ 'request_type' ] . '=' . $params [ 'q' ] . '&key=' . $params [ 'key' ] . '&display_limit=' . $params [ 'limit' ] . '&display_offset=' . $params [ 'offset' ] . '&export=api&export_columns=' . $params [ 'export_columns' ];
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
	
	$ip_address	= $_SERVER['SERVER_ADDR'];
	
	$params = Array
	(
		'request_type'		=> $request_type,
		'type'				=> $type,
		'q'					=> urlencode ( $query ),
		'key'				=> $api_key,
		'uip'				=> $ip_address,
		'db'				=> $db,
		'limit'				=> $limit,
		'offset'			=> $offset,
		'export_columns'	=> $export_columns
	);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<style type="text/css">
		.output		{ border: 1px solid #000; font: 11px Tahoma; }
		.output th	{ font-weight: bold; padding: 5px 8px; background-color: #e0e0e0; text-align: center; }
		.output td	{ padding: 3px 5px; background-color: #f0f0f0; }
	</style>
</head>
<body>
<?
	if ( false !== ( $result = performRequest ( $params ) ) )
	{
		if ( preg_match ( "/^ERROR\s[0-9]+\s::[a-zA-Z0-9\s]+/i", $result ) )
		{
			echo $result;
		}
		else
		{
			$data = explode ( "\n", trim ( $result ) );
			$fields = explode ( ";", array_shift ( $data ) );
			
			if ( count ( $data ) > 0 )
			{
?>
	<table class="output">
		<tr>
<?
				foreach ( $fields as $field )
				{
?>
			<th><?= $field; ?></th>
<?
				}
?>
		</tr>
<?
				foreach ( $data as $line )
				{
					$values = explode ( ";", $line, count ( $fields ) );
?>
		<tr>
<?
					foreach ( $values as $value )
					{
?>
			<td><?= $value; ?></td>
<?
					}
?>
		</tr>
<?
				}
?>
	</table>
<?
			}
			else
			{
?>
No data found for your request
<?
			}
		}
	}
	else
	{
?>
Error occured during request or connection timed out.
<?
	}
?>
</body>
</html>