<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Tour_package_category extends ActiveRecord\Model
{
	static $table_name = 'tour_package_categories';
	static $has_many = array(
		array('tour_package', 'class_name' => 'Tour_package', 'foreign_key' => 'cat_id'),
	);
}