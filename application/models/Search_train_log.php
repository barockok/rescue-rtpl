<?
class Search_train_log extends ActiveRecord\Model
{
	static $has_many = array(
		array('items' , 'class_name' => 'Search_train_item', 'foreing_key' => 'log_id')
	);
	static $validates_presence_of = array(
		array('route_from'),
		array('route_to'),
		array('passengers'),
		array('date_depart'),
	);

	
}