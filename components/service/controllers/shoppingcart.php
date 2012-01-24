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
		$post = $this->post();
		$post['cart_id'] = $cart->id;
		try {
			$new_item = new Cart_item($post);
			if(!$new_item->is_valid())
				$this->response(array('error' => $new_item->errors->full_messages()));
			$new_item->save();
				$this->response_warning('not provide options',$new_item->to_array(array('include' => array('cart'))));
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
					throw new Exception($item->errors->full_messages());
				$item->save();
				$this->response($item->to_array(array('include' => array('cart'))));
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
			$this->response($item->to_array());
		} catch (Exception $e) {
			$this->response_error($e->getMessage());
		}
	}
	public function add_payment_post()
	{
		# code...
	}
	
}
