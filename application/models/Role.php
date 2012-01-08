<?
class Role extends ActiveRecord\Model
{
	static $has_many = array(
		array('user', 'class_name' => 'User', 'foreign_key' => 'role_id')
	);
}