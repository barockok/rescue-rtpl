<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/* load the MX_Router class */
require APPPATH."third_party/MX/Router.php";

class MY_Router extends MX_Router {
	
	function __construct(){
		parent::__construct();
	}
	public function _validate_request($segments) {

		if (count($segments) == 0) return $segments;
		
		/* locate module controller */
		if ($located = $this->locate($segments)->segments) return $located;
		
		/* use a default 404_override controller */
		if (isset($this->routes['404_override']) AND $this->routes['404_override']) {
			$segments = explode('/', $this->routes['404_override']);
			if ($located = $this->locate($segments)->segments) return $located;
		}
		
		/* no controller found */
		show_404();
	}
	public function locate($segments, $state = null) {	
		$locMod = Modules::$locations;
		
		if($state == 'api') $locMod = $this->config->item('api_component_locations');
		if($state == 'comp') $locMod = $this->config->item('modules_locations');
		
		if($state == null){
			$this->module = '';
			$this->directory = '';
		}
		$ret_dir = '';
		$ret_mod = '';
		
		$ext = $this->config->item('controller_suffix').EXT;
		
		/* use module route if available */
		if (isset($segments[0]) AND $routes = Modules::parse_routes($segments[0], implode('/', $segments))) {
			$segments = $routes;
		}
	
		/* get the segments array elements */
		list($module, $directory, $controller) = array_pad($segments, 3, NULL);

		/* check modules */
		foreach ($locMod as $location => $offset) {
		
			/* module exists? */
			if (is_dir($source = $location.$module.'/controllers/')) {
				
			 	$ret_mod = $module;
				$ret_dir = $offset.$module.'/controllers/';
				
				/* module sub-controller exists? */
				if($directory AND is_file($source.$directory.$ext)) {
					$segments =  array_slice($segments, 1);
				}
					
				/* module sub-directory exists? */
				if($directory AND is_dir($source.$directory.'/')) {

					$source = $source.$directory.'/'; 
					if($state == null ) $this->directory .= $directory.'/';

					/* module sub-directory controller exists? */
					if(is_file($source.$directory.$ext)) {
						$segments =  array_slice($segments, 1);
					}
				
					/* module sub-directory sub-controller exists? */
					if($controller AND is_file($source.$controller.$ext))	{
						$segments =  array_slice($segments, 2);
					}
				}
				
				/* module controller exists? */			
				if(is_file($source.$module.$ext)) {
					$segments =  $segments;
				}
			}
		}
		
		/* application controller exists? */			
		if (is_file(APPPATH.'controllers/'.$module.$ext)) {
			$segments =  $segments;
		}
		
		/* application sub-directory controller exists? */
		if($directory AND is_file(APPPATH.'controllers/'.$module.'/'.$directory.$ext)) {
			$ret_dir = $module.'/';
			$segments =  array_slice($segments, 1);
		}
		
		/* application sub-directory default controller exists? */
		if (is_file(APPPATH.'controllers/'.$module.'/'.$this->default_controller.$ext)) {
			$ret_dir = $this->directory = $module.'/';
			$segments =  array($this->default_controller);
		}
		
		if($state == null){
			$this->directory = $ret_dir;
			$this->module = $ret_mod;
		}

		$return = new stdClass;
		$return->directory = $ret_dir;
		$return->segments = $segments;
		return $return;
	}
}