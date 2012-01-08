<?
class Flight_booking extends ActiveRecord\Model
{
	static $has_many = array(
		array(
			'booking_data' , 'class_name' => 'Flight_booking_data' , 'foreign_key' => 'flight_booking_id' 
		),
	);
	static $belongs_to = array(
		array(
			'order_item' , 'class_name' => 'Order_item', 'foreign_key' => 'order_item_id'
		),
		array(
			'depart_fare', 'class_name' => 'Search_fare_item' , 'foreign_key' => 'depart_fare_id'
		),
		array(
			'return_fare', 'class_name' => 'Search_fare_item', 'foreign_key' => 'return_fare_id', 'conditions' => array('return_fare_id <> ?', null)
		),
		array(
			'customer', 'class_name' => 'User', 'foreign_key' => 'user_id'
		)
	);
}