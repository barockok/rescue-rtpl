<?
/**
* 
*/

require_once BASEPATH.'helpers/string_helper.php';
class Cart extends ActiveRecord\Model
{
	static $table_name = 'carts';
	static $has_many   = array(
		array('items', 'class_name' => 'Cart_item', 'foreign_key' => 'cart_id'),
	);
	static $before_create = array('generate_id');
	
	public function generate_id()
	{
		$this->id = random_string('unique');
	}
}
