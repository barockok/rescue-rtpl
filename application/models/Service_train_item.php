<?

class Service_train_item extends ActiveRecord\Model
{
	static $belongs_to = array(
		array('log', 'class_name' => 'Search_train_log', 'foreign_key' => 'log_id')
	);

}