<?

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Airlines  {

	function __construct(){
	
		$this->ci =& get_instance();
		$this->factoryPath = './components/maskapai/libraries/factory/';
		include 'factory'.EXT;
		$this->lib = new Factory;
	}
	public function setSrcFlight($object)	
	{
		$this->lib->setSrcFlight($object);
		return $this;
	}
	public function srcFlight()
	{
		foreach(scandir($this->factoryPath) as $file){

			if(strpos($file, 'FAC_') === FALSE) continue;			

			include $this->factoryPath.$file;
			$class = ucFirst(str_replace('FAC_', '',str_replace('.php', '', $file)));
			$fac = new $class;
			$fac->srcFlight();	
		}
	
		return $this;
	}
	public function resSrcFlight()
	{
		return $this->lib->getResFligt();
	}
	public function load($maskapai, $func = null)
	{
		if(!is_file($file = $this->factoryPath.'FAC_'.ucFirst($maskapai).EXT)) return false;
		include $file;
	
		$class = ucFirst($maskapai);
		$fac = new $class;
		if(!method_exists($fac, $func)) return false;
		call_user_func(array($fac, $func));
		return true;
		
	}
	

	
	
	

}