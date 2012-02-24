<?
class  Service_hotel_item extends	ActiveRecord\Model
{
	static $belongs_to = array(
		array( 'log', 'class_name' => 'Service_hotel_log', 'foreign_key' => 'log_id' )
	);
	

}
