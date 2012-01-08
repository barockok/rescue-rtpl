<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function printDebug($object, $height = 500)	
{
	echo '<pre>';
	print_r($object);
	echo '</pre>';
}