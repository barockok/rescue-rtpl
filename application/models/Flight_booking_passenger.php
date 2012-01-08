<?
class Flight_booking_passenger extends ActiveRecord\Model
{
	static $table_name = 'flight_booking_passengers';
	static $validates_presence_of = array(
	     array('name'),
	     array('no_id'),
		 array('title')
	    );
}