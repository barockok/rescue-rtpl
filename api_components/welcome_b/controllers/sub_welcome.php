<?

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Sub_welcome extends MX_Controller {

	function __construct(){
		parent::__construct();
	}
	public function someFunc()
	{
		echo 'This call by Modules load (API) ';
	}
	

}