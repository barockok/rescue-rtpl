<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Comp_maskapai_base {
	
	static $srcResult = array();
	static $_opt;
	public function __construct()
	{
		$this->_ci =& get_instance();
		$this->_3th_party = './components/partner/third_party/comp_maskapai/';
		
	

	}
	public function setSearch($array = array())
	{
		self::$_opt = $array;
	}
	public function extractOptSrc()
	{
		foreach(parent::$_opt as $key => $val ){
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
	public function closing()
	{
		# please override me
	}
	public function insert_fare($data)
	{
		if(!is_array($data)) return;
		try {
			$fare =  new Service_fare_item($data);
			if($fare->is_valid()) 
				$fare->save();
		} catch (Exception $e) {
			return;
		}
	
		
	}
	
	
	
	
}
