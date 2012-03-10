<?
/**
* 
*/
class Temp_object extends ActiveRecord\Model
{
	static $table_name = 'temp_object';
	static $before_create = array('_before_create');
	public function _before_create()
	{
		$this->token = md5(date('Y/m/d H:i:s'));
	}
}

?>