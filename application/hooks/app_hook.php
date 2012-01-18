<?
class App_hook
{
	public function setting_locale()
	{
		$timezone = 'Asia/Jakarta';
		date_default_timezone_set($timezone);
	}
	public function initial_overide_php_setting()
	{
//		self::_output_error(array('ERROR' => 'Unkown'), 500, array('error_constant' => 'Unknow'), TRUE);
	//	ini_set('display_errors','Off'); 
//		set_error_handler('App_hook::_error_handler', E_ALL);
//		register_shutdown_function('App_hook::_shutdown_handler');
	}
	static function _error_handler($errno, $errstr, $errfile, $errline)
	{
		error_reporting(E_ALL);
		ini_set('display_errors','Off'); 
		if (!(error_reporting() & $errno)) {
		        // This error code is not included in error_reporting
		        return;
		    }
		
			// prepare data
			$return = array(
				'ERROR_NO' => $errno,
				'ERROR_MESSAGE' => $errstr,
				'ERROR_FILE' => $errfile,
				'ERROR_LINE' => $errline, 
			);
			switch ($errno) {
				case E_NOTICE:
				case E_USER_NOTICE:
				break;
			    case E_USER_ERROR:
					$return['error_constant'] = 'E_USER_ERROR';
			     	self::_output_error(array('error' => 'unknow'), 500, $return , TRUE, TRUE, TRUE);
			        break;

			    case E_USER_WARNING:
					$return['error_constant'] = 'E_USER_ERROR';
		      		self::_output_error(array('error' => 'unknow'), 500, $return , TRUE);
			        break;

			    case E_USER_NOTICE:
					$return['error_constant'] = 'E_USER_ERROR';
			      	self::_output_error(array('error' => 'unknow'),  500, $return, TRUE );
			        break;

			    default:
					$return['error_constant'] = 'E_UNKNOW';
			     	self::_output_error(array('error' => 'unknow'),  500, $return , TRUE, TRUE, TRUE);
			        break;
			    }

	 	return true;
	
	}
	public function _output_error($data = array('error' => 'UNKNOW'), $http_code = 500, $error_data = FALSE, $db = FALSE, $exit = FALSE , $email = FALSE)
	{	
	
		
		//	if($exit == FALSE ) echo 'FATAL';
			if($db == TRUE):
				try {
					$dbdata = array(
						'meta' => json_encode($error_data),
						'type' => $error_data['error_constant'],
					);
					$log = new Error_log($dbdata);
					$log->save();
				} catch (Exception $e) {
					echo $e->getMessage();
				}
				
			endif;
			
			// sending email
			if($email == TRUE){
				mail('caffeinated@example.com', 'My Subject', $message);
			}

			$output = json_encode($data);
			header('HTTP/1.1: ' . $http_code);
			header('Status: ' . $http_code);
			header('Content-Length: ' . strlen($output));
			
			if($exit == TRUE) exit($output);
			
			echo $output;
		
		
	}

	public function _shutdown_handler()
	{
		
		
		$error = error_get_last();
		$error['error_constant'] = 'E_FATAL';
	//	printDebug($error);
		self::_output_error(array('error' => 'SHUTDOWN'), 500,  $error, TRUE);
	
	
		
	}
	static function _exception_handler(){
		
	}
}