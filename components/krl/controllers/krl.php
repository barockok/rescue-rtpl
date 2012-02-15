<?

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Krl extends Platform_Controller {

	function __construct(){
		parent::__construct();
	
		
	}
	function index(){
		echo 'KRL Component';
	}
	function search(){
		echo $this->load->library('comp/krl/fetch_krl')->srcJadwal();
	}
	

}
