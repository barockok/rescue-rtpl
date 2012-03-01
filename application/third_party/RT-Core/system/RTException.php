<?
/**
* 
*/
class RTException extends RTBootstrap
{
	public static function handler($e)
	{
		$exceptionType =  get_class($e);
		if($exceptionType == "ActiveRecord\\DatabaseException"){

			return 'Verry Bad, we gonna fix it imedietly';
			
		}
		else{
		
			return $e->getMessage();
		
		}
	}
	
}

?>