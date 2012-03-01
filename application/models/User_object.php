<?
class User_object extends ActiveRecord\Model
{
	static $table_name = 'user_object';

	static $validates_presence_of  = array('group_obj', 'name', 'value', 'user_id') ;
//	static $attr_protected = array('group_obj', 'name', 'user_id');
	  
	static $belongs_to = array(
		array('user', 'class_name' => 'User', 'foreign_key' => 'user_id'),
	);
}
