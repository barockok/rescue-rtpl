<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* 
*/
class Article extends ActiveRecord\Model
{
	static $table_name = 'article';
	static $belongs_to= array(
		array('author' , 'class_name' => 'User', 'foreign_key' => 'author_id'),
		array('category', 'class_name' => 'Article_category', 'foreign_key' => 'cat_id')
	);
	
}
