<?
class User_role extends ActiveRecord\Model
{
	static $table_name = 'user_role';
	static $has_many = array(
		array('user', 'class_name' => 'User', 'foreign_key' => 'role_id')
	);
}