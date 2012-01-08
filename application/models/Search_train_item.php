<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');	
class Search_train_item extends ActiveRecord\Model
{
	static $belongs_to = array(
		array('log', 'class_name' => 'Search_train_log', 'foreign_key' => 'log_id')
	);
}