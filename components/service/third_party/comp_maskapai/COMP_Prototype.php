<?
/**
* 
*/
class Prototype extends COMP_Maskapai_Base
{
	
	function __construct()
	{
		parent::__construct();
	}

	public function doSearch($log)
	{
		
		// determine the result, if result count Zero just throw the ResultNotFond;
		throw new ResultFareNotFound($log);
	}
	public function doBooking($fare_data,$passangers_data,$customer_data){
		
	
		// execution to each maskapai goes here
	
		
		//******* Scenario Of Booking Final Price are Change and Higer than price from Fare data ******/
		
		// the rest of parsing price from execution
		$finalBookingPrice = 7897899
		if($finalBookingPrice > $fare_data['price'])
			// throw execption that price are change and higer than price form fare_data
			throw new BookingFarePriceChanged($fare_data, $finalBookingPrice);
		
		
		//******* Scenario when booking failed for varous reason ******/
	
		// determine that booking was failed with adaption your own code
		$bookingSuccess = FALSE
		
		if($bookingSuccess == FALSE)
			// when booking is failed than throw the exception BookingFailed
			// second parameter ($message) is optional 
			$message = 'fare not found , its sold out , perhaps :) ';
				throw new BookingFailed($fare_data, $message);
			
		
		
		
	}
	
}

?>