<? 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Maskapai extends Platform_Controller {

	function __construct(){
		parent::__construct();
	}
	function index(){
		echo ' its Platfrom Child';
	}
	

}