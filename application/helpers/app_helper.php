<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function printDebug($object, $height = 500)	
{
	echo '<pre>';
	print_r($object);
	echo '</pre>';
}
function show_date($date, $format = 'l, F j,  Y'){
/*
	$date = new DateTime($date);
	$date = DateTime('Y-m-d H:i:s', $date);

	$str_date = $date->format('l, F j,  Y');
*/
    $date = new datetime($date, new datetimezone('UTC'));
    $str_date = $date->format($format);

	return (!is_string($format)) ? $date : $str_date;
}
function validate_array($should = array(), $check = array())
{
	$ci =& get_instance();
	$ci->load->helper(array('array', 'inflector', 'string'));
	$combine = elements($should, $check, NULL);
	$continue = TRUE;
	$errors = array();
	foreach($combine as $key => $val) {
		if($val == NULL) {
			$continue = FALSE;
			array_push($errors , humanize($key));
		}
	}
	$return = new stdClass;
	$return->is_valid = $continue;
	$return->unvalid_indexs = $errors;
	$return->unvalid_text = implode(',', $errors);
	$return->data = $combine;
	return $return;
}
function suicide($endpoint='', $bgvoid = TRUE , $pre = '', $post = "")
{
	$bgflag = ($bgvoid == TRUE) ? DEV_NULL : '';
	if($bgvoid == TRUE)
			exec(CURL_BIN_LOC.' '.$pre.' --header "X-API-KEY : '.SELF_API_KEY.' " '.site_url().$endpoint.' '.$post.' '.$bgflag);
	else{
			exec(CURL_BIN_LOC.' '.$pre.' --header "X-API-KEY : '.SELF_API_KEY.'" '.site_url().$endpoint.' '.$post.' '.$bgflag, $return);
			return $return;
		}

}