<?
/**
* 
*/
class Auth extends REST_Controller
{
	
	function __construct()
	{
		parent::__construct();
	}
	public function login_post()
	{
		if(!$this->post('user')) $this->response_error(array('no user variable posted'));
		$post = elements(array('email', 'passsword'), $this->post('user'), null);
		
		try {
			$user = User::find('last', array(
									'conditions' => array(
										'email =? and password = ?', 
										element('email', $post), 
										md5(element('password', $post))
										)
									)
								);
			$this->response($user->to_array());
		} catch (Exception $e) {
			$this->response_error($e->getMesasge);
		}
		
	}
	public function logout_post()
	{
	
	}
	
}

