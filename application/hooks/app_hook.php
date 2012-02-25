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
		ini_set('display_errors','On'); 
		/*
		error_reporting(E_USER_ERROR | E_RECOVERABLE_ERROR | E_ERROR);
		register_shutdown_function('_shutdown_handler');
		set_error_handler('_error_handler');
		set_exception_handler('exception_handler');
		*/
	}
	public function post_controller_constructor()
	{
		$ci =& get_instance();
		$ci->output->set_header('X-Powered-By:Phusion Passenger (mod_rails/mod_rack) 2.2.15');
		$ci->output->set_header('X-RTPlatform-V:0.1');
	}


}
function exception_handler($exception) {
  echo "Uncaught exception: " , $exception->getMessage(), "\n";
}


function _error_handler($errno, $errstr, $errfile, $errline)
{
//	error_reporting(E_ALL);
	
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
		     	_output_error(array('error' => 'unknow'), 500, $return , TRUE, TRUE, TRUE);
		        break;

		    case E_USER_WARNING:
				$return['error_constant'] = 'E_USER_ERROR';
	      		_output_error(array('error' => 'unknow'), 500, $return , FALSE);
		        break;

		    case E_USER_NOTICE:
				$return['error_constant'] = 'E_USER_ERROR';
		      	_output_error(array('error' => 'unknow'),  500, $return, FALSE );
		        break;

		    default:
				$return['error_constant'] = 'E_UNKNOW';
		     	_output_error(array('error' => 'unknow'),  500, $return , TRUE, TRUE, TRUE);
		        break;
		    }

 	return true;

}
function _output_error($data = array(), $http_code = 500, $error_data = FALSE, $db = FALSE, $exit = FALSE , $email = FALSE)
{	
	
		if($db == TRUE):

			
		endif;
		
		// sending email
	
		$output = json_encode($data);
		header('HTTP/1.1: ' . $http_code);
		header('Status: ' . $http_code);
		header('Content-Length: ' . strlen($output));
		if($exit == TRUE) exit($output);
		echo $output;
	
}

function _shutdown_handler()
{
	
	$error = error_get_last();
	if($error['type'] == E_ERROR){
		trigger_error($error['message'], E_USER_ERROR);
	}

	
}