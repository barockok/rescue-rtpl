<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Comp_hotel {
	
	var $comp_base;
	var $comp_available ;
	
	function __construct(){
	
		$this->ci =& get_instance();
		$this->comp_available = array('Kamar');
		shuffle($this->comp_available);
		$this->comp_path = dirname(__FILE__).'/../third_party/comp_hotel/';
		include $this->comp_path.'COMP_Hotel_Base'.EXT;
		$this->base = new Comp_hotel_base;
	}

	public function doSearch($conf, $hotel)
	{
		$this->base->setSearch($conf);	
		$file_name = 'COMP_'.$hotel;
		include $this->comp_path.$file_name.EXT;
		$class = $file;
		$comp = new $class;	
		$comp->doSearch();
		$comp->closing();
	}	

	public function getSrcParam()
	{
		return $this->_srcParam;
	}
	public function load($hotel)
	{
		$hotel = ucFirst(strtolower($hotel));
		if(!is_file($file = $this->comp_path.'COMP_'.$hotel.EXT)) return false;
		include $file;
	
		$class = $hotel;
		if(!class_exists($class)) return false;
		$fac = new $class;
		// Closing
	//	call_user_func(array($fac, 'closing'));
		return $fac;
		
	}
	public function _load($maskapai)	
	{
		if(!is_file($file = $this->comp_path.'COMP_'.ucFirst($maskapai).EXT)) return false;
		include $file;
		$class = ucFirst($maskapai);
		$fac = new $class;
		return $fac;
	}


}