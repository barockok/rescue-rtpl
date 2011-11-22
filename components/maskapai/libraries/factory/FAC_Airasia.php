<?
if (! defined('BASEPATH')) exit('No direct script access');

class Airasia extends Factory {

	public function __construct()
	{
		parent::__construct();
	}
	public function search()
	{
		echo $this->_ci->uri->segment(2);
	}
}