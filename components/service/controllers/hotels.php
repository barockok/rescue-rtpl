<?

if (! defined('BASEPATH')) exit('No direct script access');

class Hotels extends REST_Controller {

	//php 5 constructor
	function __construct() {
		parent::__construct();
	}
	
	//php 4 constructor
	public function search_post(){
		
	}
	
	public function search_get(){
		$data = array(
			'test'	=> 'tester',
		);
		$this->response($data);
	}

}