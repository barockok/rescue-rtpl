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
	}
	function extends_controller(){
		include PLATPATH.'extends_controllers/API_Controller'.EXT;
	}
}

