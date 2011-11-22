<?

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Factory  {

	static $srcResult = array();
	var $opt;
	
	function __construct(){
		$this->_ci =& get_instance();

	}
	public function setSrcFlight($json_string)
	{
	//	$this->opt = json_decode($json_string);
	return;
	}
	public function setBooking($value='')
	{
			
	}
	public function srcFlight()
	{
		# code...
	}
	public function addResFlight($new_result)
	{
		self::$srcResult = array_merge(	self::$srcResult, $new_result);
	}
	public function getResFligt()
	{
		return self::$srcResult;
	}
	public function doBooking()
	{
		# code...
	}
	public function doConfirm()
	{
		# code...
	}

}
