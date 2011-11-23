<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Comp_maskapai_base {
	
	static $srcResult = array();
	var $_optSrc;
	public function __construct()
	{
		$this->_ci =& get_instance();
		$this->_3th_party = './components/partner/third_party/comp_maskapai/';
		
	}
	public function setSearch($array)
	{
		$this->_optSrc = new stdClass;
		foreach($array as $key => $va){
			$this->_optSrc->$key = $val;
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
	
	
}
