<?
class Order_item extends ActiveRecord\Model
{
	static $table_name = 'order_item';
	static $belongs_to = array(
		array('order', 'class_name' => 'order', 'foreign_key' => 'order_id'),
	);
	
	static $before_create = array('_before_create');

	public function _before_create()
	{
		if($this->options != null)
			$this->options = json_encode($this->options);
	}
	
	
}
