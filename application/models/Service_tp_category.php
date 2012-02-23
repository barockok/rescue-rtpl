<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Service_tp_category extends ActiveRecord\Model
{
	static $table_name = 'service_tp_category';
	static $has_many = array(
		array('tour_package', 'class_name' => 'Service_tp', 'foreign_key' => 'cat_id'),
	);
}