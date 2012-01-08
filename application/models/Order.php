<?
class Order extends ActiveRecord\Model
{
	static $belongs_to = array(
		array('customer', 'class_name' => 'User', 'foreign_key' => 'user_id'),
	);

	static $has_many = array(
		array('order_item', 'class_name' => 'Order_item', 'foreign_key' => 'order_id'),
	);
	static $before_create = array('_before_create');
	
	public function _before_create()
	{
		$this->c_time = date('Y-m-d H:i:s');
	}
	public function _before_update()
	{
		$this->m_time = date('Y-m-d H:i:s');
	}

}
?>