<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Comp_maskapai_base {
	
	static $srcResult = array();
	var $_opt;
	public function __construct()
	{
		$this->_ci =& get_instance();
		$this->_3th_party = './components/partner/third_party/comp_maskapai/';
		
			$this->_opt = new stdClass();
			$this->_opt->date_depart = '2011-11-30';
			$this->_opt->date_return = '2011-12-04';
			$this->_opt->passengers = 1;
			$this->_opt->route_from = 'CGK';
			$this->_opt->route_to = 'PKY';

	}
	public function setSearch($array = array())
	{
		foreach($array as $key => $val){
			$this->_opt->$key = $val;
		}
	}
	public function addResult($array = array())
	{
		self::$srcResult = array_merge(self::$srcResult, $array);
	}
	public function doSearch()
	{
		# code...
	}
	public function getResult()
	{
		return self::$srcResult;
	}
	public function cleanObject($callback, $return = null)
	{
		$segment = explode('/', $callback);
		$class = element(0, $segment);
		$method = element(1, $segment);
		ob_start();
		$args = func_get_args();
		$output = call_user_func_array(array($class, $method), array());
		$buffer = ob_get_clean();
		return ($output !== NULL) ? $output : $return;
	}
	
	
}
