<?
/**
* 
*/
class Shoppingcart extends REST_Controller
{
	
	function __construct()
	{
		parent::__construct();
	}
	public function create_post()
	{
		$post = validate_array(array('user_id', 'currency'), $this->post(), NULL);
		if(!$post->is_valid) $this->response(array('error' => 'not procide : '.$post->unvalid_text), 500);
		$data = $post->data;
		try {
			$cart  = new Cart($data);
			if(!$cart->is_valid())
				$this->response(array('error' => $cart->errors->full_messages()), 500);
			$cart->save();
			
			$this->response($cart->to_array(), 200);
		} catch (Exception $e) {
			$this->response(array('error' => $e->getMessage()), 500);
		}
		
	}
	public function view_get($self_id = null)
	{	
		if($self_id == null)
			$id = ($id = $this->uri->rsegment(3)) ? $id : $this->response(array('error' => 'provide the id please'), 500);
		else
			$id = $self_id;
			
		try {
			$cart = Cart::find($id);
			$this->response($cart->to_array(array('include' => array('items'))), 200);
		} catch (Exception $e) {
			$this->response(array('error' => $e->getMessage()));
		}
	}
	public function update_post()
	{
			
	}
	public function delete_delete()
	{
			$id = ($id = $this->uri->rsegment(3)) ? $id : $this->response(array('error' => 'provide the id please'), 500);
			try {
				$cart = Cart::find($id);
				$cart->delete();
				$this->response($cart->to_array(), 200);
			} catch (Exception $e) {
				$this->response(array('error' => $e->getMessage()));
			}
	}
	public function add_item_post()
	{
		$id = $this->uri->rsegment(3);
	
		try {
			$cart = Cart::find($id);
		} catch (Exception $e) {
			$this->response(array('error' => $e->getMessage()));
		}
	
	
		try {
			$post = $this->post();
			$post['cart_id'] = $cart->id;
			$post = $this->_hook_caller($post['type'], 'add_item', $post);
			
			$new_item = Cart_item::create($post);
			
			if(!$new_item->is_valid())
				throw new Exception(implode(',', $new_item->errors->full_messages()));
			$new_item->save();
			$this->response($new_item->to_array(array('include' => array('cart'))));
		} catch (Exception $e) {
			$this->response_error($e->getMessage());
		}
	}
	public function update_item_post()
	{
		$id = $this->uri->rsegment(3);
		$post = $this->post();
		try {
			
			$item = Cart_item::find($id);
			// what ever check the cart first
			try {
				$cart = Cart::find($item->cart_id);
			} catch (Exception $e) {
				throw $e;
			}
			
			// everything good so update the cart
			try {
				$item->update_attributes($post);
				if(!$item->is_valid())
					throw new Exception(implode(',', $item->errors->full_messages()));
				$item->save();
				$updated_item = $item->to_array(array('include' => array('cart')));
				$this->_hook_caller(element('type', $updated_item), 'update_item' , $updated_item);
				$this->response($updated_item);
			} catch (Exception $e) {
				throw $e;
			}
		} catch (Exception $e) {
			$this->response_error($e->getMessage());
		}
	}
	public function delete_item_delete()
	{
		$id = $this->uri->rsegment(3);
		try {
			$item = Cart_item::find($id);
			$item->delete();
			$old_item = $item->to_array();
			$this->_hook_caller(element('type', $old_item), 'delete_item' , $old_item);
			$this->response($$old_item);
		} catch (Exception $e) {
			$this->response_error($e->getMessage());
		}
	}
	public function hook_call_post()
	{
		$this->response($this->_hook_caller('airlines', 'test', array('data' => 'name')));
	}
	
	private function _hook_caller($sibling, $func, $param)
	{
		$file_name = strtolower($sibling).'.php';
		$func = '_sc_hook_'.$func;
		if(!is_file($inc = dirname(__FILE__).'/'.$file_name)) return $param;
		
		else{
			include_once $inc;
			if(!class_exists($class = ucfirst($sibling) ) ) return $param;
			if(!is_callable( array($class, $func) ) ) return $param;
			// TOTO : check first, to func called, if null or void, return to original $param
			$return = call_user_func_array($class.'::'.$func, array($param));
			return ($return != null) ? $return : $param;
		}
	}
	
}
