<?
class Nav extends ActiveRecord\Model
{
	static $has_many = array(
		array('nav_item','class_name' => 'Nav_item', 'foreign_key' => 'nav_id')
	);
}