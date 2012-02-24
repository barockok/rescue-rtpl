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
		$datas = elements(array('email', 'password'), $this->post(), FALSE);
		foreach($datas as $key => $val) if($val == FALSE) $this->response(array('error' => 'PLease complete the data required'), 500);
		// check user 
		try {
			$user = User::find('last', array(
				'conditions' => array(
					'email = ? AND password = ?', 
					element('email', $datas),
					md5(element('password', $datas))
					)
				)
			);
			if($user){
				if($user->status != 'active'){
					$this->response(array('warning' => 'your account not active yet, we already resend the activation key to your email ('.$user->email.')'), 200);
				}else{
					$this->response($user->to_array(array('except' => array('password'), 'include' => array('role'))), 200);
				}
			}else{
				$this->response(array('error' => 'combination failed'), 500);
			}
			
		} catch (Exception $e) {
			$this->response(array('error' => $e->getMessage()), 500);
		}
	}
}

