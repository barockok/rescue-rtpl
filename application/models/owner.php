<?
class Owner extends ActiveRecord\Model
{
	static $has_many = array(
		array('companies'),
	);
	
}
class Company extends ActiveRecord\Model
{
	static $belongs_to = array(
		array('person', 'class_name' => 'Owner')
	);
	static $before_create = array('asuh');
	public function asuh()
	{
		$this->name = strtoupper($this->name);
		# code...
	}
}