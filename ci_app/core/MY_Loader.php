<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/* load the MX_Loader class */
require APPPATH."third_party/MX/Loader.php";

class MY_Loader extends MX_Loader {
	
		public function __construct()
		{
			parent::__construct();
			$this->_ci_library_paths 	= array(APPPATH, BASEPATH, PLATPATH);
			$this->_ci_helper_paths 	= array(APPPATH, BASEPATH, PLATPATH);
			$this->_ci_model_paths 		= array(APPPATH, PLATPATH);
			$this->_ci_view_paths 		= array(
												APPPATH.'views/'	=> TRUE
												);
		}
		public function module($module, $params = NULL)	{
			
			if (is_array($module)) return $this->modules($module);
			
			$segment = detectState($module);
			
			$_alias = strtolower(basename($segment->module));
		
			CI::$APP->$_alias = Modules::load(array($segment->module => $params), $segment->state);
			return CI::$APP->$_alias;
		}

		/** Load an array of controllers **/
		public function modules($modules, $state = null) {
			foreach ($modules as $_module) $this->module($_module, $state = nulll);	
		}

}