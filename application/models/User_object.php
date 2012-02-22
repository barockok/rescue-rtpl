<?
class User_object extends ActiveRecord\Model
{
	static $table_name = 'user_object';
	static $belongs_to = array(
		array('user', 'class_name' => 'User', 'foreign_key' => 'user_id'),
	);
}
