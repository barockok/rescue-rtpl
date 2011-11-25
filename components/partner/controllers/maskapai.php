<?

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Maskapai extends Platform_Controller {

	function __construct(){
		parent::__construct();
	}
	function index(){
		$this->load->library('comp/partner/comp_maskapai');
		$this->comp_maskapai->doSearch();
		echo '<textarea style="width:100%; height: 100%; border: none">';
		print_r($this->comp_maskapai->base->getResult());
		echo '</textarea>';
	}
	function doSearch($conf){
		$this->load->library('comp/partner/comp_maskapai');
		$this->comp_maskapai->doSearch($conf);
		return $this->comp_maskapai->base->getResult();
	}
	

}