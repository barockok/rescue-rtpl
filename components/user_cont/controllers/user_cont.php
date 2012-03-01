<?
/**
* 
*/
class User_cont extends REST_Controller
{
	
	function __construct()
	{
		parent::__construct();
	}
	public function register_post()
	{
		$details = elements(array('details'), $this->post(), FALSE);
		
		if(element('details', $details) == FALSE) $this->response(array('error' => 'details not provide'), 500);
	
		// clean the details on post
		$post = $this->post();
		unset($post['details']);
	
		try {
			$new = new User($post);
			if(!$new->is_valid())
				$this->response(array('error' => $new->errors->full_messages()), 500);
			else
				$new->save();
			
			$details['user_id'] = $new->id;
			$new_details = new User_detail($details);
			$new_details->save();
			
			$this->response($new->to_array(array(
							'include' => array(
								'user_detail', 
								'role'
							)
						)
					), 200);

		} catch (Exception $e) {
			$this->response(array('error' => $e->getMessage()), 500);	
		}
	}
	public function edit_post()
	{
		$id = ($id = $this->uri->rsegment(3) AND is_numeric($id)) ? $id : $this->reponse(array('error' => 'please provide id'), 500);
		$data = $this->post();
		try {
			$user = User::find($id);
			try {
				$user->update_attributes($data);
				if(!$user->is_valid())
					$this->response(array('error' => $user->errors->full_messages()), 500);
				else
					$user->save();
				$this->response($user->to_array());
			} catch (Exception $e) {
				$this->response(array('error' => $e->getMessage()), 500);
			}
		} catch (Exception $e) {
			$this->response(array('error' => $e->getMessage()), 500);
		}
	}
	public function profile_get()
	{
		$id = CustomerSession::getModel()->id;
		try {
			
			$user = User::find($id);
			$user = $user->to_array(
					array(
					'include' => array('role', 'object'),
					'except' => array('password'), 
					));
			$this->response($user);
		} catch (Exception $e) {
			$this->response(array(
				'error' => $e->getMessage(),
			));
		}
	}
	public function object_get($object_group = null, $object_key = null, $user_id = null)
	{
		try {
			$object_group = (is_null($object_group)) ? $this->uri->rsegment(3) : $object_group;
			$object_key = (is_null($object_key)) ? $this->uri->rsegment(4) : $object_key;
			$user_id = (is_null($user_id)) ? CustomerSession::getModel()->id : $user_id;
		//	$object_group =$this->uri->rsegment(3);
		//	$object_key = $this->uri->rsegment(4);
		//	$user_id = CustomerSession::getModel()->id ;
			
			if($object_key != false and $object_group)
				$object = User_object::find('last', array('conditions' => array('user_id = ? and group_obj = ? and name = ?', $user_id, $object_group, $object_key)));
			elseif(!$object_key and $object_group)
				$object = User_object::find('all', array('conditions' => array('user_id = ? and group_obj = ?', $user_id, $object_group)));
			else
				$this->response_error('no object specify');
			
			if($object && !is_array($object))
					$this->response($object->to_array(array('only' => array('name', 'value', 'id', 'group_obj'))));
			elseif($object && is_array($object))
					$this->response($this->db_util->multiple_to_array($object, array('only' => array('name', 'value', 'id', 'group_obj'))));
			else	
				$this->response_error('cannot find user object(s)');
		} catch (Exception $e) {
			$this->response_error($e->getMessage());
		}
	}
	public function auth_post()
	{
		if(!$this->post('user')) $this->response_error(array('no user variable posted'));
		$post = elements(array('email', 'password'), $this->post('user'), null);
		
		try {
			$user = User::find(array(
									'conditions' => array(
										'email = ? and password = ?', 
										element('email', $post), 
										md5(element('password', $post)),
										)
									)
								);
			if(!$user)	
				throw new Exception("No User Found", 1);
			
			if($user->status != 'active')
				$this->response('this account not active yet', 222 );
			
			$user_res = $user->to_array(array('except' => array('password', 'actv_key'),'include' => array('role') ) );
			$this->response($user_res);
			
		} catch (Exception $e) {
			$this->response_error($e->getMessage());
		}
		
	}

}
