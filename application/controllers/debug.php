<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* 
*/
class Debug extends MX_Controller
{
	
	function __construct()
	{
		parent::__construct();
	}
	public function test1()
	{
		$clients = Client::find(1);
		printDebug($clients);
	}
	
}
