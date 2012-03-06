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
		$this->message = 'result fare not found for  on log id = '.$log['id'];
	}
}
class DetailFareNotFound extends AirlinesExceptions{
	function __construct($fare_date )
	{
		$this->fare_data = $fare_data;
		$this->message = 'fare not available anymore';
	}
}
class BookingFarePriceChanged extends AirlinesExceptions{
	var $oldPrice , $newPrice, $fareId;
	function __construct($fare, $newPrice)
	{
		$this->oldPrice = $fare['price'];
		$this->newPrice = $newPrice;
		$this->message = 'Price change for Fare Items id '.$fare['id'];
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