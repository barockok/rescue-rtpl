<?

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class A_maskapai extends API_Controller {

	function __construct(){
		parent::__construct();
	}
	function search_post(){
		$mask = $this->load->module('comp/partner/maskapai');
		$this->response($mask->doSearch($this->request->body), 200);
	}
	public function test_post()
	{
		
	}
	

}	
