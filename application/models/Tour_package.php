<?
class Tour_package extends ActiveRecord\Model
{
	static $table_name = 'tour_packages';
	static $has_many= array(
		array('medias', 'class_name' => 'Tour_package_media', 'foreign_key' => 'package_id'),
	);
	static $belongs_to = array(
		array('category', 'class_name' => 'Tour_package_category', 'foreign_key' => 'cat_id')
	);
	
	static $before_create = array('_before_create');
	static $before_update = array('_before_update');
	
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
	public function _before_create()
	{
		$this->c_time = date('Y-m-d H:i:s');
	}
	public function _before_update()
	{
		$this->m_time = date('Y-m-d H:i:s');
	}

}