<?

class Service_train_item extends ActiveRecord\Model
{
	static $table_name = "service_train_item";
	static $belongs_to = array(
		array('log', 'class_name' => 'Service_train_log', 'foreign_key' => 'log_id')
	);

}