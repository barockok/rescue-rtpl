<?
class Page extends ActiveRecord\Model
{
	static $table_name = 'pages';
	static $before_create = array('_before_create');
	static $before_update = array('_before_update');
	static $belongs_to = array(
		array('user', 'class_name' => 'User', 'foreign_key' => 'author')
	);
	
	public function _before_create()
	{
		$this->c_time = date('Y-m-d H:i:s');
	}
	public function _before_update()
	{
		$this->m_time = date('Y-m-d H:i:s');
	}
}