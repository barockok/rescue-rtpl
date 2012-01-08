<?
class search_fare_log extends ActiveRecord\Model
{
	static $has_many = array(
		array(
		'items', 
		'class_name' => 'Search_fare_item', 
		'foreign_key' => 'log_id'
		)
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
		$this->c_time = date('Y-m-d H:i:s');
		if($this->date_return == null){
			$this->type = 'oneway';
		}else{
			$this->type = 'roundtrip';
		}
		$this->comp_include = json_encode(explode(',', $this->comp_include));
	}

	
	
}