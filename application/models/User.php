<?
class User extends ActiveRecord\Model
{
	static $table_name = 'users';
	static $belongs_to = array(
		array('role','class_name' => 'Role', 'foreign_key' => 'role_id')
	);
	static $has_many = array(
		array('pages', 'class_name' => 'Page', 'foreign_key' => 'author')
		);
	static $has_one = array(
		array('user_detail', 'class_name' => 'User_detail', 'foreign_key' => 'user_id')
	);
	static $before_create = array('_before_create');
	static $before_update = array('_before_update');
	static $after_create = array('_after_create');
	static $validates_format_of = array(
	     array('email', 'with' =>
	        '/^[^0-9][A-z0-9_]+([.][A-z0-9_]+)*[@][A-z0-9_]+([.][A-z0-9_]+)*[.][A-z]{2,4}$/', 'message' => 'not Valid email')
	    );
	
	public function _after_create()
	{
			$this->_ci()->load->act_listener('user/after_create', $this->to_array());
			
	}
	public function _before_create()
	{
		if($this->role_id == null) $this->role_id = 2;
		$this->c_time = date('Y-m-d H:i:s');
		$this->password = md5($this->password);
		$this->actv_key = md5(date('Y-m-d H:i:s'));
		
	}
	public function _before_update()
	{
		
		
		$this->m_time = date('Y-m-d H:i:s');
		//detect password change
		$u = self::find($this->id);
		if($u->password != $this->password){
			$this->password = md5($this->password);
		}
	}
	public function get_full_name()
	{
		return $this->f_name.' '.$this->l_name;
	}
	public function validate()
	{
		// check duplicated email on register
		if($this->id == null){
			if(self::exists(
				array(
					'conditions' => array(
						'email = ?', $this->email
						)
					)
				)
			) $this->errors->add('email', 'email already registered');	
		}
		
		// check email update duplicated
		if($this->id != null){
			$user = self::find($this->id);
			if($user->email != $this->email) 
				if(self::count(array('conditions'=> array('email = ? ', $this->email) ) ) > 0 )
					$this->errors->add('email', 'email already registred');
		}
	}

}