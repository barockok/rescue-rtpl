<?
class Search_train_log extends ActiveRecord\Model
{
	static $has_many = array(
		array('items' , 'class_name' => 'Search_train_item', 'foreing_key' => 'log_id')
	);
	static $before_create = array('_before_create');

	public function _before_create()
	{
		$this->c_time = date('Y-m-d H:i:s');
	}

}