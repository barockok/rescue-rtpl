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