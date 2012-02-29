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

$hook['pre_system'][]					= array(
                                			'class'    => 'App_hook',
			                                'function' => 'setting_locale',
			                                'filename' => 'app_hook.php',
			                                'filepath' => 'hooks/'
			                                );



$hook['pre_controller'][] 			= array(
										    'class'    => '',
										    'function' => 'initialize_php_activerecord',
										    'filename' => 'ActiveRecord.php',
										    'filepath' => 'third_party/php-activerecord'                                
										);
$hook['pre_controller'][] = array(
												'class'    => '',
				                                'function' => 'initialize_customer_session',
				                                'filename' => 'customer_session_hook.php',
				                                'filepath' => 'hooks/'
										);
$hook['post_controller_constructor'] = array(
												'class'    => 'App_hook',
				                                'function' => 'post_controller_constructor',
				                                'filename' => 'app_hook.php',
				                                'filepath' => 'hooks/'
										);

	
$hook['pre_system'][]					= array(
                                			'class'    => 'App_hook',
			                                'function' => 'initial_overide_php_setting',
			                                'filename' => 'app_hook.php',
			                                'filepath' => 'hooks/'
			                                );


/* End of file hooks.php */
/* Location: ./application/config/hooks.php */