<? 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Maskapai extends Platform_Controller {

	function __construct(){
		parent::__construct();
		$this->load->library('comp/maskapai/airlines');
		
	}
	function index(){
	
	}
	function src_flight($param = null){
		$this->airlines
		->setSrcFlight($param)
		->srcFlight()
		->resSrcFlight() ;
	}
	public function someFunc()
	{
		return array('msg' => 'This call by Modules load');
	}
	

}