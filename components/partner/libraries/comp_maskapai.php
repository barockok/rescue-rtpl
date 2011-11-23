<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Comp_maskapai {
	function __construct(){
	
		$this->ci =& get_instance();
		$this->comp_path = './components/partner/third_party/comp_maskapai/';
		include $this->comp_path.'comp_maskapai_base'.EXT;
		$this->comp_base = new Comp_maskapai_base ;
	}

}