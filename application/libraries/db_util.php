<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* 
*/
class Db_util
{
	
	function __construct()
	{
		$this->CI =& get_instance();
	}
	public function multiple_to_array($model_res, $serialize = array())
	{	
		$new_res = array();
		foreach($model_res as $item) array_push($new_res , $item->to_array($serialize));
		return $new_res;
	}
}
