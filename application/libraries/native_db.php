<?
/**
* 
*/
class Native_db
{
	
	function __construct($args)
	{


		if( is_string($args)){
			
		}elseif(is_array($args) AND isset()){
			$this->initialize($args);	
		}
	}
}