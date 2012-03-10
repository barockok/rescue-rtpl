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
		array('route')
    );
	static $belongs_to = array(
	array('original' , 'class_name' => 'Ext_data_airport', 'foreign_key' => 'route_from' , 'primary_key' => 'code'),
	array('destination' , 'class_name' => 'Ext_data_airport', 'foreign_key' => 'route_to', 'primary_key' => 'code')
	);
	static $validates_numericality_of = array(
	  array('price', 'greater_than' => 10000)
	);
	static $before_create = array('_before_create');
	static $before_update = array('_before_update');
	public function _before_create()
	{
		$this->archive = 'N';
	
	}
	public function _before_update()
	{
	//	$this->price_meta = json_encode($this->price_meta);
	}
	


	
}
