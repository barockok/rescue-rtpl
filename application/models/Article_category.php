<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* 
*/
class Article_category extends ActiveRecord\Model
{
	static $table_name = 'article_categories';
	static $has_many = array(
		array('articles', 'class_name' => 'Article', 'foreign_key' => 'cat_id')
	) ;
	
	
}
