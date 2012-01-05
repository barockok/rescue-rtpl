<?
class Client_app extends ActiveRecord\Model
{
	static $connection 	= "api";
	static $table 		= "client_apps";
	static $belongs_to	= array(
		array('client', 'class_name' => 'clients', 'foreign_key' => 'client_id')
	);
}