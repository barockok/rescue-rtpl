<?
/**
* 
*/

require_once BASEPATH.'helpers/string_helper.php';
class Cart extends ActiveRecord\Model
{
	static $table_name = 'cart';
	static $has_many   = array(
		array('items', 'class_name' => 'Cart_item', 'foreign_key' => 'cart_id'),
	);
	static $before_create = array('generate_id');
	
	public function generate_id()
	{
		$this->id = random_string('unique');
	}
	
	public function validate()
	{
		try {
			$user = User::find($this->user_id);
		} catch (Exception $e) {
			$this->errors->add('user_id', $e->getMessage());
		}
		// valid currency
		$currencies = array('IDR');
		if(!in_array($this->currency, $currencies )) $this->errors->add('currency', 'we curently cannot serve with '.$this->currency.' currency');
	}
}
