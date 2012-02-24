<?
/**
* 
*/
class Order_confirmation extends ActiveRecord\Model
{
	static $table_name ='order_confirmation';
	static $belongs_to = array(
		array('order', 'class_name' => 'Order' , 'foreign_key' => 'order_id'),
		array('payment_method', 'class_name' => 'Cart_payment_method', 'foreign_key' => 'pm_id'),
	);
}
