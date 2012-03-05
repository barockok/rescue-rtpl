<?
class Service_fare_item extends	ActiveRecord\Model
{
	static $table_name = 'service_fare_item';
	static $validates_presence_of = array(
     	array('class'),
		array('price'),
		array('flight_no'),
		array('company'),
		array('t_depart'),
		array('t_arrive'),
		array('type'),
		array('route')
    );
	static $belongs_to = array(
	array('original' , 'class_name' => 'Ext_data_airport', 'foreign_key' => 'route_from' , 'primary_key' => 'code'),
	array('destination' , 'class_name' => 'Ext_data_airport', 'foreign_key' => 'route_to', 'primary_key' => 'code')
	);
	static $validates_numericality_of = array(
	  array('price', 'greater_than' => 10000)
	);
	


	
}
