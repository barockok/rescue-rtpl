<?
/**
* 
*/
class search_hotel_log extends ActiveRecord\Model
{
	//array('departure_airport' , 'class_name' => 'Ext_data_airport', 'foreign_key' => 'route_from' , 'primary_key' => 'code'),
	static $belongs_to = array(
		array('city','class_name'	=>	'Ext_data_hotel','foreign_key'	=>	'city','primary_key'	=>	'code'),
	);
	
	/*static $validates_presence_of = array(
		array('checkin', 'message' => 'you must set the checkin date'),
		array('checkout', 'message' => 'you must set the checkout date'),
		array('comp_search', 'message' => 'which hotels'),
		array('city', 'message' => 'where are you will stay'),
	);*/
	
	static $before_create = array('_before_create');
	
	public function _before_create()
	{
		$this->c_time = date('Y-m-d H:i:s');
		//$this->comp_include = json_encode(explode(',', str_replace(' ', '', strtolower($this->comp_include))));
	}
}



?>