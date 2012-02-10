<?
class search_fare_item extends	ActiveRecord\Model
{
	
	static $before_create = array('_before_create');
	static $validates_presence_of = array(
     	array('class'),
    );
	static $validates_numericality_of = array(
	  array('price', 'greater_than' => 10000)
	);
	static $belongs_to = array(
		array(
		'log', 
		'class_name' => 'Search_fare_log',
		'foreign_key' => 'log_id'
		)
	);
	

	function _before_create(){
	
	}

	
}
