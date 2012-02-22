<?
class Booking_flight_passenger extends ActiveRecord\Model
{
	static $table_name = 'booking_flight_passenger';
	static $validates_presence_of = array(
	     array('name'),
	     array('no_id'),
		 array('title')
	    );
}