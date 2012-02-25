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
		$id = ($id = $this->uri->rsegment(3) AND is_numeric($id)) ? $id : $this->reponse(array('error' => 'please provide id'), 500);
		try {
			$user = User::find($id);
			$this->response($user->to_array(
					array(
					'include' => array('user_detail', 'role'),
					'except' => array('password'), 
					), 
					200)
				);
		} catch (Exception $e) {
			$this->response(array(
				'error' => $e->getMessage(),
			));
		}
	}

}
