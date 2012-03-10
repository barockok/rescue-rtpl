<?
/**
* 
*/
class AirlinesExceptions extends Exception {}
class ServiceDown extends AirlinesExceptions{
	public function __construct($company)
	{
		$this->message = "$company service currently down";
	}
}
class LoginFailed extends AirlinesExceptions{
	public function __construct($company)
	{
		$this->message = "$company Login Fail";
	}
}
class ResultFareNotFound extends AirlinesExceptions{
	public function __construct($log)
	{
		$this->message = "result fare not found ". $log['route_from'] ." ".$log['route_to']." to ".$log['date_return'];
	}
}
class DetailFareNotFound extends AirlinesExceptions{
	function __construct($msg)
	{
		$this->message = $msg;
	
	}
}
class BookingFarePriceChanged extends AirlinesExceptions{
	var $oldPrice , $newPrice, $fareId;
	function __construct($fare, $newPrice)
	{
		$this->oldPrice = $fare['price'];
		$this->newPrice = $newPrice;
		$this->message = 'Price change for Fare Items id '.$fare['id'];
		$this->fare_data = $fare;
	}
}
class BookingFailed extends AirlinesExceptions{

	var $fareId, $fareData;
	function __construct($fare, $message = null)
	{
		$this->message 	= ($message == null) ? 'Booking Failed for fare id ='.$fare['id'] : $message;
		$this->fareData = $fare;
		$this->fareId 	= $fare['id'];	
		
	}
}

?>