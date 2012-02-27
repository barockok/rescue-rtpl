<?
/**
* 
*/
class AirlinesExceptions extends Exception {}
class ResultFareNotFound extends AirlinesExceptions{
	public function __construct($log)
	{
		$this->message = 'result fare not found for  on log id = '.$log['id'];
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