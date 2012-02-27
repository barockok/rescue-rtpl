<?
/**
* 
*/
class Service_hotel_log extends ActiveRecord\Model
{	
	static $table_name = 'service_hotel_log';
	static $validates_presence_of = array(
		array('checkin', 'message' => 'you must set the checkin date'),
		array('checkout', 'message' => 'you must set the checkout date'),
		array('room','message'	=>	'you must set how many rooom'),
		array('adult', 'message' => 'how many guees (adult) will stay'),
		array('query', 'message' => 'what term you will search')
	);
	

}

