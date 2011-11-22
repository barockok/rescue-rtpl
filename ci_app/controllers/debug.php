<?

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Debug extends MX_Controller {

	function __construct(){
		parent::__construct();
	}
	function index(){
		echo 'playground here';
	}
	function maskapai(){
		$maskapai = $this->uri->segment(3);
		$func 	 = $this->uri->segment(4);
		$this->load->library('maskapai/airlines');
		$fac = $this->airlines->load($maskapai, $func);
		
		if( $fac == false ){
			echo 'Somthing wrong Func or Class Not Exist';
		}
		
		
	}
	

}
?>