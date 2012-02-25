<?
class Service_fare_log extends ActiveRecord\Model
{
	static $table_name = 'service_fare_log';
	static $has_many = array(
		array(
		'items', 
		'class_name' => 'Service_fare_item', 
		'foreign_key' => 'log_id'
		)
	);
	static $belongs_to = array(
		array('departure_airport' , 'class_name' => 'Ext_data_airport', 'foreign_key' => 'route_from' , 'primary_key' => 'code'),
		array('destination_airport' , 'class_name' => 'Ext_data_airport', 'foreign_key' => 'route_to', 'primary_key' => 'code')
		);
	static $validates_presence_of = array(
		array('date_depart', 'message' => 'you must set the depart date'),
		array('passengers', 'message' => 'how many passengers will flight'),
		array('comp_include', 'message' => 'which airlines'),
		array('route_from', 'message' => 'where are you will flight from'),
		array('route_to', 'message' => 'where is your destination ?'),
	    );
	static $before_create = array('_before_create');
	
	public function _before_create()
	{
		$this->last_try = date('Y-m-d H:i:s');
		if($this->date_return == null){
			$this->type = 'oneway';
		}else{
			$this->type = 'roundtrip';
		}
		$this->comp_include = json_encode(explode(',', str_replace(' ', '', strtolower($this->comp_include))));
	}
	public function _before_update()
	{
		if($this->comp_complete != null){
			$this->comp_complete = json_encode(str_replace(' ', '', strtolower($this->comp_complete)));
		}
	}

	
	
}