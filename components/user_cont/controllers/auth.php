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
				$this->response_warning('this account not active yet');
			
			$user_res = $user->to_array(array('except' => array('password', 'actv_key'),'include' => array('role') ) );
			$this->response($user_res);
			
		} catch (Exception $e) {
			$this->response_error($e->getMessage());
		}
		
	}
	public function logout_post()
	{
		
	}
	
}
git commit --amend --author='Zidni Mubarock <zidmubarock@gmail.com>'

