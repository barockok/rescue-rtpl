<?
class User_detail extends ActiveRecord\Model
{
	static $table_name = 'user_details';
	static $belongs_to = array(
		array('user', 'class_name' => 'User','foreign_key' => 'user_id')
	);
}