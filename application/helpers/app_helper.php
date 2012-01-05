<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function printDebug($object, $height = 500)	
{
	echo '<textarea style="width: 100%; height : '.$height.'px; border: none">';
	print_r($object);
	echo '</textarea>';
}