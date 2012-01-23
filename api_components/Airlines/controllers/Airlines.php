<?

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Airlines extends API_Controller {

	function __construct(){
		parent::__construct();
	}
	function test_post(){
		$this->response($this->load->module('comp/maskapai')->someFunc(), 200);
	}
	

}
