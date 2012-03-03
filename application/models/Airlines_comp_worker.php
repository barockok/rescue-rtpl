<?
/**
* 
*/
class Airlines_comp_worker extends ActiveRecord\Model
{
	static $table_name = 'airlines_comp_worker';
	static $before_create = array('_before_create');
	
	public function _before_create()
	{
		$this->status = 'onprogress';
	}
	
}

?>