<?

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Debug extends MX_Controller {

	function __construct(){
		parent::__construct();
	}
	function index(){
		echo 'playground here hayooo lagi';
		echo 'playground here hayooo lagi';
	}
	function maskapai(){
		$maskapai = $this->uri->segment(3);
		$func 	 = $this->uri->segment(4);
		$this->load->library('partner/comp_maskapai');
		$fac = $this->comp_maskapai->load($maskapai, $func);
		
		if( $fac == false ){
			echo 'Somthing wrong Func or Class Not Exist';
		}
		print_r($this->comp_maskapai->base->getResult());
		
		
	}
	function maskapai_search(){
		
	}

	function git_fetch(){
		$this->load->library('ssh');
		$this->ssh->login();
	}
	

}
?>