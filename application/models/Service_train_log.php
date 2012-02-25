<?
class Service_train_log extends ActiveRecord\Model
{
	static $table_name = 'service_train_log';
	static $table_alias = 'Search Log Train';
	static $has_many = array(
		array('fares' , 'class_name' => 'Service_train_item', 'foreign_key' => 'log_id')
	);
	static $belongs_to = array(
		array('original_stasiun', 'class_name' => 'Ext_data_stasiun', 'foreign_key' => 'route_from', 'primary_key' => 'code'),
		array('destination_stasiun', 'class_name' => 'Ext_data_stasiun', 'foreign_key' => 'route_to', 'primary_key' => 'code'),
	);
	static $validates_presence_of = array(
		array('route_from'),
		array('route_to'),
		array('passengers'),
		array('date_depart'),
	);
	public function validate()
	{
		try {
			$from  = Ext_data_stasiun::find($this->route_from);
		} catch (Exception $e) {
			$this->errors->add('route_from', "is not valid");
		}
		try {
			$to    = Ext_data_stasiun::find($this->route_to);
		} catch (Exception $e) {
			$this->errors->add('route_to', "is not valid");
		}
	}

	
}