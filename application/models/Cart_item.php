<?
/**
* 
*/
require_once BASEPATH.'helpers/string_helper.php';
class Cart_item extends ActiveRecord\Model
{
	static $table_name ="cart_item";
	static $belongs_to = array(
		array('cart' , 'class_name' => 'Cart', 'foreign_key' => 'cart_id'),
	);
	static $validates_presence_of = array(
		array('name'),
		array('qty'),
		array('type'),
		array('subtotal'),
		array('price'),
			
	  );
	static $before_create = array('generate_id');
	
	public function generate_id()
	{
		$this->id = random_string('unique');
	} 
	public function validate()
	{
		$valid_type = array('airlines', 'train', 'hotel', 'tourpackage');
		if(!in_array($this->type, $valid_type)) $this->errors->add('type', 'not valid type of cart item');
	}
	
}
