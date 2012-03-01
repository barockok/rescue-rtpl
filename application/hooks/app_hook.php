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
		if(WORKING_STATUS != true){
			ini_set('display_errors','Off'); 
			error_reporting(E_USER_ERROR | E_RECOVERABLE_ERROR | E_ERROR);
			register_shutdown_function('_shutdown_handler');
			set_error_handler('_error_handler');
			set_exception_handler('exception_handler');
			
		}
		else
			ini_set('display_errors','On'); 
	}
	public function post_controller_constructor()
	{
		$ci =& get_instance();
		$ci->output->set_header('X-Powered-By:Phusion Passenger (mod_rails/mod_rack) 2.2.15');
		$ci->output->set_header('X-RTPlatform-V:0.1');
	}
}
function exception_handler($exception) {
  	
		$output = json_encode(array('error' => RTException::handler($exception)));
		header('HTTP/1.1: ' . 500);
		header('Status: ' . 500);
		header('Content-Length: ' . strlen($output));
		exit ($output);
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
				$return['ERROR_CONSTANT'] = 'E_USER_ERROR';
		     	_output_error(array('error' => 'unknow'), 500, $return , TRUE, TRUE, TRUE);
		        break;

		    case E_USER_WARNING:
				$return['ERROR_CONSTANT'] = 'E_USER_ERROR';
	      		_output_error(array('error' => 'unknow'), 500, $return , FALSE);
		        break;

		    case E_USER_NOTICE:
				$return['ERROR_CONSTANT'] = 'E_USER_ERROR';
		      	_output_error(array('error' => 'unknow'),  500, $return, FALSE );
		        break;

		    default:
				$return['ERROR_CONSTANT'] = 'E_UNKNOW';
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
		header('X-Powered-By:Phusion Passenger (mod_rails/mod_rack) 2.2.15');
		header('X-RTPlatform-V:0.1');
		header('Content-Type:application/json');
		if($exit == TRUE) exit($output);
		echo print_r($data);
	
}

function _shutdown_handler()
{
	
	$error = error_get_last();
	if($error['type'] == E_ERROR){
	//	exit(json_encode($error));
		_error_handler(E_USER_ERROR, $error['message'], $error['file'], $error['line']);
	}
	
}