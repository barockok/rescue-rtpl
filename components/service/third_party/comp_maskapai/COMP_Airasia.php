<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Airasia extends Comp_maskapai_base {
	
	public function __construct()
	{
		parent::__construct();
	}
	public function doSearch()
	{
	//	$this->addResult(array('a', 'b', 'c'));
	return array('sug', 'sih');
	}
	public function test()
	{
		$afare = Search_fare_item::last();
		$afare = $afare->to_array();
		unset($afare['id']);
		$this->insert_fare($afare);
	}
	
}
