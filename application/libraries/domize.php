<?
/**
* 
*/
include_once APPPATH.'helpers/domize_helper.php';
class Domize
{
	var $html;
	
	function __construct($source)
	{
	 	$this->html =& str_get_html($source);
	}
	public function __call($func, $args)
	{
		if(method_exists($this->html, $func)){
				$ancall = call_user_func_array(array($this->html, $func), $args);
				if($ancall != null )
					return $this->html;
				else
					throw new Exception("Error Processing Request", 1);
		}
		else
			throw new Exception("Error Processing Request", 1);
			
	}
	public function __get($property)
	{
		if($this->html->$property)
			return $this->html->$property;
		else
			return 'not set';
	}
	

}

