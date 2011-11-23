<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Comp_maskapai {
	
	var $comp_base;
	function __construct(){
	
		$this->ci =& get_instance();
		$this->comp_path = './components/partner/third_party/comp_maskapai/';
		include $this->comp_path.'comp_maskapai_base'.EXT;
		$this->base = new Comp_maskapai_base ;
	}
	public function setSearch($array = array())
	{
		$this->base->setSearch($array);
		return $this;
	}
	public function doSearch()
	{
		foreach(scandir($this->comp_path) as $file){
			if(strpos($file, 'COMP_') === false) continue; 
			if($file == 'COMP_Maskapai_Base'.EXT) continue;
			include $this->comp_path.$file;
			$class = ucFirst(str_replace('.php', '', str_replace('COMP_', '', $file)));
			$comp = new $class;
			$comp->doSearch();
		}
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