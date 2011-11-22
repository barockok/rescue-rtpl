<? 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Asuh extends Platform_Controller {

	function __construct(){
		parent::__construct();
	
	}
	function index(){
	
	}
	public function someFunc()
	{
		return 'This call by Modules load';
	}
	
}