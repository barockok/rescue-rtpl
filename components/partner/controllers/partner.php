<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Partner extends REST_Controller
{
	public function __construct()
	{
		parent::__construct();
	}
	public function index()
	{
		echo phpinfo();
	}
	public function test()
	{
		$this->benchmark->mark('code_start');
		$param = array(
			'route_from' => 'CGK',
			'route_to'	 => 'DPS',
			'date_depart' => '2012-01-20',
			'date_return' => null,
			'id'		 => 1,
			'passengers' => 1
		) ;
		$this->load->library('comp_maskapai');
		$comp 	= $this->comp_maskapai->_load('batavia');
		$result = $comp->doSearch($param);
		$comp->closing();
			if(count($result) == 0) return false;
			// put result to database
		$this->benchmark->mark('code_end');
		printDebug($result);
		echo 'Running Time : '.$this->benchmark->elapsed_time('code_start', 'code_end');
	}
	public function search_get()
	{
		$params = elements(array('route_from', 'route_to', 'date_depart', 'date_return', 'id', 'passengers'), $this->get(), NULL);
		$this->load->library('comp_maskapai');
		$comp 	= $this->comp_maskapai->_load($this->get('maskapai'));
		$result = $comp->doSearch($params);
		if(count($result) == 0) $this->response('null', 500);
		$this->response($result, 200);
		
	}
}