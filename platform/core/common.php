<?
 function detectState($module){
		$return = new stdClass;
		$return->state = null;
		$return->module = $module;

		$segment = explode('/', $module);	
		if(!in_array($segment[0], array('api', 'comp'))) return $return;	
		
		$return->state = $segment[0];
		$return->module = implode('/', array_slice($segment, 1));
	
		return $return;
	}

?>