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
	$url = null;
	if(is_array($endpoint))
		foreach($endpoint as $ae)
			$url .= site_url($ae).' ';
	elseif(is_string($endpoint))
			$url = site_url($endpoint);
		
	
	
	
	if($bgvoid == TRUE)
			system(CURL_BIN_LOC.' '.$pre.' --header "X-API-KEY:'.SELF_API_KEY.'" '.$url.' '.$post.' '.$bgflag);
	else{
			system(CURL_BIN_LOC.' '.$pre.' --header "X-API-KEY:'.SELF_API_KEY.'" '.$url.' '.$post.' '.$bgflag, $return);
			return $return;
		}

}
function array_sort($array, $on, $order=SORT_ASC)
{
    $new_array = array();
    $sortable_array = array();

    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }

        switch ($order) {
            case SORT_ASC:
                asort($sortable_array);
            break;
            case SORT_DESC:
                arsort($sortable_array);
            break;
        }

        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }

    return $new_array;
}
function encrypt($sData, $secretKey){
    $sResult = '';
    for($i=0;$i<strlen($sData);$i++){
        $sChar    = substr($sData, $i, 1);
        $sKeyChar = substr($secretKey, ($i % strlen($secretKey)) - 1, 1);
        $sChar    = chr(ord($sChar) + ord($sKeyChar));
        $sResult .= $sChar;
    }
    return encode_base64($sResult);
}
function decrypt($sData, $secretKey){
    $sResult = '';
    $sData   = decode_base64($sData);
    for($i=0;$i<strlen($sData);$i++){
        $sChar    = substr($sData, $i, 1);
        $sKeyChar = substr($secretKey, ($i % strlen($secretKey)) - 1, 1);
        $sChar    = chr(ord($sChar) - ord($sKeyChar));
        $sResult .= $sChar;
    }
    return $sResult;
}
function encode_base64($sData){
	$sBase64 = base64_encode($sData);
	return str_replace('=', '', strtr($sBase64, '+/', '-_'));
}

function decode_base64($sData){
	$sBase64 = strtr($sData, '-_', '+/');
	return base64_decode($sBase64.'==');
}
function cleanup_string($string)
{
	return filter_var(
				$string,
				FILTER_SANITIZE_STRING,
				FILTER_FLAG_STRIP_LOW
			);
}


