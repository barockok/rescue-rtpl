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
		$user_data = $this->post('new_user');
		$objects = false;
		if(isset($user_data['object'])){
			$objects = $user_data['object'];
			$objects = (is_string( element('0', array_values($objects)))) ? array($objects) : $objects;
			unset($user_data['object']);
		}
		try {
			$new_user = new User($user_data);
			if(!$new_user->is_valid())
				throw new Exception(implode(',', $new_user->errors->full_messages() ), 1);
			$new_user->save();
			$new_objects = null;
			if(is_array($objects)){
				for ($i=0; $i < count($objects); $i++) { 
					$objects[$i]['user_id'] = $new_user->id;
				}	
				$new_objetcs = $this->_objects_create($objects);
			}
			$return = $new_user->to_array(array('include' => array('role'), 'except' => array('password', 'actv_key')));
			if(!is_null($new_objects))
				$return['objects'] = $new_objects;
			$this->response($return);
			
		} catch (Exception $e) {
			$this->response_error($e);
		}
	}
	public function edit_post()
	{
		$user_data = $this->post('user');
		$objects = false;
		if(isset($user_data['object'])){
			$objects = $user_data['object'];
			$objects = (is_string( element('0', array_values($objects)))) ? array($objects) : $objects;
			unset($user_data['object']);
		}
		try {
			$the_user = User::find(CustomerSession::getModel()->id);
			$the_user->update_attributes($user_data);
			if(!$the_user->is_valid())
				throw new Exception(implode(',', $the_user->errors->full_messages() ), 1);
			$the_user->save();
			$the_objects = null;
			
			
			$return = $the_user->to_array(array('include' => array('role'), 'except' => array('password', 'actv_key')));
			try {
				if(is_array($objects)){
					for ($i=0; $i < count($objects); $i++) { 
						$objects[$i]['user_id'] = $the_user->id;
					}	
					$the_objetcs = $this->_objects_create($objects);
				}
			} catch (Exception $e) {
				$return['object_warning'] = array($e->getMessage());
			}
			if(!is_null($the_objects))
				$return['objects'] = $the_objects;
			$this->response($return);
		} catch (Exception $e) {
			$this->response_error($e);
		}
	}
	public function profile_get()
	{
		$id = CustomerSession::getModel()->id;
		try {
			$user = User::find($id);
			$user = $user->to_array(
					array(
					'include' => array('role'),
					'except' => array('password', 'actv_key'), 
				));

			$objects = $this->get('object');
	
			if((is_string($objects) or is_array($objects)) and $objects != 'true') {
				
				$objects = (is_string($objects)) ? array($objects) : $objects;
				foreach($objects as $object_group){
					try {
						$user['objects'][$object_group] = $this->_object_get($id, $object_group);
					} catch (Exception $e) {
						$user['objects'][$object_group]['warning'] = $e->getMessage();
					}
				}
			}else if(is_string($objects) and $objects == 'true'){
				
				try {
					$user['objects'] = $this->_object_get($id);
				} catch (Exception $e) {
					$user['objects']['warning'] = $e->getMessage();
				}
			
			}
			$this->response($user);
			
		} catch (Exception $e) {
			$this->response(array(
				'error' => $e->getMessage(),
			));
		}
	}
	public function object_get()
	{
		try {
			$object_group = (!$og = $this->uri->rsegment(3)) ? null : $og;
			$object_key = (!$ok = $this->uri->rsegment(4)) ? null : $ok;
			$user_id = (!$ui = CustomerSession::getModel()->id) ? null : $ui;
			
			$object = $this->_object_get($user_id, $object_group, $object_key);
			
			if($object)
				$this->response($object);
			
		} catch (Exception $e) {
			$this->response_error($e);
		}
	}
	private function _object_get($user_id = null, $object_group = null, $object_key = null)
	{
			try {
				if($object_key != null and $object_group != null)
					$object = User_object::find('last', array('conditions' => array('user_id = ? and group_obj = ? and name = ?', $user_id, $object_group, $object_key)));
				elseif($object_key == null and $object_group != null)
					$object = User_object::find('all', array('conditions' => array('user_id = ? and group_obj = ?', $user_id, $object_group)));
				elseif($user_id != null)
					$object = User_object::find('all', array('conditions' => array('user_id = ?', $user_id)));
				else
					throw new Exception("Cannot Determine Object", 1);
					

				if($object && !is_array($object))
						 return $object->to_array(array('only' => array('name', 'value', 'id', 'group_obj')));
				elseif($object && is_array($object)){
					    $return = $this->db_util->multiple_to_array($object, array('only' => array('name', 'value', 'id', 'group_obj')));
						if(is_null($object_group ) and is_null($object_key))
							return array_group($return, 'group_obj');
						else
							return $return;
						
					}
				else	
					return false;
			} catch (Exception $e) {
				 throw $e;
			}
	}
	public function object_edit_post()
	{
		$post = $this->post('object');
		$post = (is_string(element('0', array_values($post))) ) ? array($post) : $post;
		foreach($post as $a_data){
			$a_data['user_id'] = CustomerSession::getModel()->id;
			$data[] = $a_data;
		}
		try {
			$objects = $this->_objects_edit($data);
			$this->response($objects);
		} catch (Exception $e) {
			$this->response_error($e);
		}

	}
	public function object_new_post()
	{
		
		$post = $this->post('object');
		$post = (is_string(element('0', array_values($post))) ) ? array($post) : $post;
		$data = array();
		
		foreach($post as $a_data){
			$a_data['user_id'] = CustomerSession::getModel()->id;
			$data[] = $a_data;
		}
		//$this->response($data);
		try {
			$objects = $this->_objects_create($data);
			$this->response($objects);
		} catch (Exception $e) {
			$this->response_error($e);
		}
	}
	private function _objects_edit($datas)
	{	$objects = array();
		foreach($datas as $data)
			$objects[] = $this->_object_edit($data);
		return $objects;
	}
	private function _object_edit($data)
	{
		try {
			if(element('id', $data))
				$object = User_object::find(element('id', $data));
			else{
				$object = User_object::find('last', 
								array('conditions' => 
										array(
											'user_id = ? and group_obj = ? and name = ?', 
											element('user_id', $data),
											element('group_obj', $data),
											element('name', $data)
											) 
								)
							);
				if(!$object)
					throw new Exception("No Object Define", 1);
					
			}
			$object->update_attributes($data);
			$object->save();
			return $object->to_array();
		} catch (Exception $e) {
			throw $e;
		}
	}
	private function _objects_create($datas)
	{ 
	
		$return = array();
		foreach($datas as $data)
			$return[] = $this->_object_create($data);
		return $return;
	}
	private function _object_create($data)
	{
	
		
		try {
			// detect first is there before, if there, just update the old one
			$object = User_object::find('last', 
						array('conditions' => 
							array(
									'user_id = ? and group_obj = ? and name = ?', 
									element('user_id', $data ), 
									element('group_obj', $data), 
									element('name', $data)
								)
						)
					);
			
			if($object){
					$data['id'] = $object->id;
					return $this->_object_edit($data);
			}else{
				//	return $data;
					$new_object = new User_object($data);
					if(!$new_object->is_valid())
						throw new Exception(implode(',', $new_object->errors->full_messages()), 1);
					$new_object->save();
					return $new_object->to_array();
			}		
		} catch (Exception $e) {
			throw $e;
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
			$this->response_error($e);
		}
	}

}
