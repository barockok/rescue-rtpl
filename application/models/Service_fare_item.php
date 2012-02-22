<?
class Service_fare_item extends	ActiveRecord\Model
{
	static $table_name = 'service_fare_item';
	static $validates_presence_of = array(
     	array('class'),
		array('price'),
		array('flight_no'),
		array('log_id'),
		array('company'),
		array('t_depart'),
		array('t_arrive'),
		array('type'),
		array('route')
    );
	static $validates_numericality_of = array(
	  array('price', 'greater_than' => 10000)
	);
	static $belongs_to = array(
		array(
		'log', 
		'class_name' => 'Service_fare_log',
		'foreign_key' => 'log_id'
		)
	);


	
}
