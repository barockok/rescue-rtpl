<?
/**
* 
*/
class Order_payment extends ActiveRecord\Model
{
	static $table_name = 'order_payment';
	static $belongs_to == array(
		array('order', 'class_name' => 'Order' , 'foreign_key' => 'order_id'),
		array('method', 'class_name' => 'Cart_payment_method', 'foreign_key' => 'pm_id')
	);
}

