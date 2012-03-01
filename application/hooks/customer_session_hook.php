<?
	function initialize_customer_session()
	{
		
			# code...
			$ci =& get_instance();
			if(isset($_SERVER['HTTP_X_CUSTOMER_KEY'])){
				$token = $_SERVER['HTTP_X_CUSTOMER_KEY'];
				CustomerSession::$token = $token;
				try {
					$user = User::find('last', array('conditions' => array('token = ?', array($token))));
					if($user)
						CustomerSession::$model = $user;
					else 
						throw new CustomerSessionTokenException($token);
				} catch (Exception $e) {
					throw $e;
				}
			}
	}

/**
* 
*/
class CustomerSessionException extends Exception{}
class CustomerSessionTokenException extends Exception{
	public function __construct()
	{
		$this->message = "failed get customer session with token = ".CustomerSession::$token;
	}
}
class CustomerSession
{
	static $model ;
	static $token ;
	public static function getModel()
	{
		if(empty(self::$model))
			throw new CustomerSessionException("No Customer Session Token Provided", 1);
		
		if(self::$model instanceof User == FALSE)
			throw new CustomerSessionException("Error Processing Request", 1);
		
		return self::$model;	
			
	}
	
}



?>