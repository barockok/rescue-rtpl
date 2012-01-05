<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Partner extends MX_Controller
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
		printDebug($result);
	}
}