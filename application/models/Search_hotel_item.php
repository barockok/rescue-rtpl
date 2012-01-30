<?
class  search_hotel_item extends	ActiveRecord\Model
{
	static $before_create = array('_before_create');
	static $belongs_to = array(
		array(
		'log', 
		'class_name' => 'Search_hotel_log',
		'foreign_key' => 'log_id'
		)
	);
	
	function _before_create(){
	
	}
}
?>