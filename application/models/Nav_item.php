<?
class Nav_item extends ActiveRecord\Model
{
	static $belongs_to = array(
		array('nav', 'class_name' => 'Nav', 'foreign_key' => 'nav_id')
	);
	
}