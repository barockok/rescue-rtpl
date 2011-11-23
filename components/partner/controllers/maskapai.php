<?

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Maskapai extends Platform_Controller {

	function __construct(){
		parent::__construct();
	}
	function index(){
		$this->load->library('comp/partner/comp_maskapai');
		$this->comp_maskapai->doSearch();
		print_r($this->comp_maskapai->base->getResult());
	}
	

}