<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Comp_maskapai {
	
	var $comp_base;
	var $comp_available ;
	
	function __construct(){
	
		$this->ci =& get_instance();
		$this->comp_available = array('Garuda', 'Citilink', 'Batavia', 'Sriwijaya', 'Lion', 'Merpati');
		$this->comp_path = './components/partner/third_party/comp_maskapai/';
		include $this->comp_path.'Comp_Maskapai_Base'.EXT;
		$this->base = new Comp_maskapai_base ;
	}

	public function doSearch($conf = array())
	{
	
		$this->base->setSearch($conf);
		
		foreach($this->comp_available as $file){
			
			$file_name = 'COMP_'.$file;
			include $this->comp_path.$file_name.EXT;
			$class = $file;
			$comp = new $class;
			$comp->doSearch();
		}
	}	

	public function getSrcParam()
	{
		return $this->_srcParam;
	}
	public function load($maskapai, $func = null)
	{
		if(!is_file($file = $this->comp_path.'COMP_'.ucFirst($maskapai).EXT)) return false;
		include $file;
	
		$class = ucFirst($maskapai);
		$fac = new $class;
		if(!method_exists($fac, $func)) return false;
		call_user_func(array($fac, $func));
		return true;
		
	}

}