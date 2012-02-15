<?
/**
* 
*/
class SELF_Controller extends MX_Controller
{
	
	function __construct()
	{
		parent::__construct();
		if($this->input->ip_address()  != '127.0.0.1') exit('restrited area');
	}

}
