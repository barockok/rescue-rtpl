<?
/**
* 
*/
class RTBootstrap 
{
	static $ci;
	function __construct()
	{
		if(class_exists('CI_Controller'))
			self::$ci =& get_instance();
	}
	public function init()
	{
		// load all system
		spl_autoload_register('RT_autoloader');	
	}
	public function ci()
	{	
		if(!is_null(self::$ci))
		 	return self::$ci;
		else
			throw new Exception("Ci Not Init yet", 1);
			
	}

}

function RT_autoloader()
{
	$system_path = APPPATH.'third_party/RT-Core/system/';
	foreach (scandir($system_path) as $file) {
		if(strpos($file, 'RT') !== FALSE)
			include_once $system_path.$file;
	}
}

?>