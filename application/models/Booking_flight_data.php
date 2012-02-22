<?
class Booking_flight_data extends ActiveRecord\Model
{
	static $table_name = 'booking_flight_data';
	static $belongs_to = array(
		array('booking_flight', 'class_name' => 'booking_flight', 'foreign_key', 'booking_id'),
		array('fare_data', 'class_name' => 'Search_fare_item', 'foreign_key' => 'fare_id'),
	);
	
	
}
