<?
class Client extends ActiveRecord\Model
{
	static $connection 	= "api";
	static $table 		= "clients";
	static $has_many	= array(
		array('app', 'class_name' => 'client_app', 'foreign_key' => 'client_id')
	);
}