<?
class Flight_booking_data extends ActiveRecord\Model
{
	static $belongs_to = array(
		array('flight_booking', 'class_name' => 'Flight_booking', 'foreign_key', 'flight_booking_id'),
		array('fare_data', 'class_name' => 'Search_fare_item', 'foreign_key' => 'fare_id'),
	);
	
	
}
