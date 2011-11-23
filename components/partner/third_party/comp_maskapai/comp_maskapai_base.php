<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Comp_maskapai_base {
	
	static $srcResult = array();
	var $_optSrc;
	public function __construct()
	{
		$this->_ci =& get_instace();
		$this->_3th_party = './components/partner/third_party/comp_maskapai/';
		
	}
	public function setSearch($array)
	{
		$this->_optSrc = new stdClass;
		foreach($array as $key => $va){
			$this->_optSrc->$key = $val;
		}
	}
	
}
