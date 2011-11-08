<? if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * platform_hook.php
 *
 * @author Zidni Mubarock
 */

/**
* Platform_hook
*/
class Platform_hook
{
	
	function __construct()
	{
		
	}
	function pre_system(){
		include 'platform/core/constant'.EXT;
	//	$this->segmented_component();
	}
	function extends_controller(){
		include PLATPATH.'extends_controllers/API_Controller'.EXT;
	}
	function segmented_component()
	{
		global $CFG;
		
		
		$subdomain_arr = explode('.', $_SERVER['HTTP_HOST'], 2); //creates the various parts  
		$subdomain_name = $subdomain_arr[0]; //assigns the first part
		if($subdomain_name == 'api'){
			Modules::set_location($CFG->item('api_component_locations'));
			Modules::set_state('back');
		} 
	}
}

