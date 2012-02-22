<?
class Order extends ActiveRecord\Model
{
	static $table_name ='order';
	static $belongs_to = array(
		array('customer', 'class_name' => 'User', 'foreign_key' => 'user_id'),
	);
	static $has_many = array(
		array('items', 'class_name' => 'Order_item', 'foreign_key' => 'order_id'),
		array('payment', 'class_name' => 'Order_payment', 'foreign_key' => 'order_id'),
		array('confirmation', 'class_name' => 'Order_confirmation', 'foreign_key' => 'order_id')
		
	);

	
}
?>