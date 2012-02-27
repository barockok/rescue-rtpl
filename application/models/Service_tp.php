<?
class Service_tp extends ActiveRecord\Model
{
	static $table_name = 'service_tp';
	static $has_many= array(
		array('medias', 'class_name' => 'Service_tp_media', 'foreign_key' => 'package_id'),
	);
	static $belongs_to = array(
		array('category', 'class_name' => 'Service_tp_category', 'foreign_key' => 'cat_id')
	);
	static $validates_presence_of = array(array('title'), array('cat_id'), array('l_desc'), array('stock'), array('price'));
	
	public function get_default_media()
	{
		try {
			$medias = $this->medias;
		} catch (Exception $e) {
			$medias = false;
		}
		if(!$medias) return 'not_found.jpg';
		foreach($medias as $item){
			if($item->type == 'pic'){
				return $item->path;
				break;
			}
		}
	}


}