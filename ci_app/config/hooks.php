<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/general/hooks.html
|
*/

$hook['pre_system']					= array(
                                			'class'    => 'Platform_hook',
			                                'function' => 'pre_system',
			                                'filename' => 'platform_hook.php',
			                                'filepath' => 'hooks/../../platform/hooks/'
			                                );
$hook['extends_controller']			= array(
                                			'class'    => 'Platform_hook',
			                                'function' => 'extends_controller',
			                                'filename' => 'platform_hook.php',
			                                'filepath' => 'hooks/../../platform/hooks/'
			                                );
$hook['post_router_construct'] 		= array(
	                              		  	'class'    => 'Platform_hook',
			                                'function' => 'post_router_construct',
			                                'filename' => 'platform_hook.php',
			                                'filepath' => 'hooks/../../platform/hooks/'
			                                );


/* End of file hooks.php */
/* Location: ./application/config/hooks.php */