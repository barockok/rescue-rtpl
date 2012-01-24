<?
/**
* 
*/
require_once BASEPATH.'helpers/string_helper.php';
class Cart_item extends ActiveRecord\Model
{
	static $table_name ="cart_items";
	static $belongs_to = array(
		array('cart' , 'class_name' => 'Cart', 'foreign_key' => 'cart_id'),
	);
	static $before_create = array('generate_id');
	
	public function generate_id()
	{
		$this->id = random_string('unique');
	} 
	
}
