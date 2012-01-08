<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Comp_maskapai {
	
	var $comp_base;
	var $comp_available ;
	
	function __construct(){
	
		$this->ci =& get_instance();
		$this->comp_available = array('Citilink');
		shuffle($this->comp_available);
		$this->comp_path = dirname(__FILE__).'/../third_party/comp_maskapai/';
		include $this->comp_path.'COMP_Maskapai_Base'.EXT;
		$this->base = new Comp_maskapai_base ;
	}

	public function doSearch($conf, $maskapai)
	{
		$this->base->setSearch($conf);	
		$file_name = 'COMP_'.$maskapai;
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
	public function load($maskapai)
	{
		$maskapai = ucFirst(strtolower($maskapai));
		if(!is_file($file = $this->comp_path.'COMP_'.$maskapai.EXT)) return false;
		include $file;
	
		$class = $maskapai;
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