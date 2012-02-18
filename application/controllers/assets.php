<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* 
*/
class Assets extends Base_Controller
{
	
	function __construct()
	{
		parent::__construct();
	}
	public function thumb()
	{
		$url = $this->uri->uri_string();
		$w = $this->uri->segment(3);
		$h = $this->uri->segment(4);
		$c = ($ur4 = $this->uri->segment(5) == 'no') ? false : true;
		
		$crop = ($c == true) ? 'c-' :  ''; 
		$source = str_replace('/d/', '', strstr($url, '/d/'));

		if(!is_file('./assets/media/'.$source)) $source = 'def_img.png';


		$thumb = $this->load->library('PhpThumbFactory');
		$pre_s = explode('/', $source);
		$file_name = end($pre_s); array_pop($pre_s);
		$cache_file_name = $crop.$file_name;
		$req_path = implode('/', $pre_s);
		$file_dir_cache = './assets/media/thumb/'.$w.'-'.$h.'/'.date('Ymd_H-i').'/'	;
		$file_dir_origin = './assets/media/'.$req_path.'/';

		if(is_file($file_dir_cache.$cache_file_name) && (filemtime($file_dir_origin.$file_name) > filemtime($file_dir_cache.$cache_file_name)))
		{
			 unlink($file_dir_cache.$cache_file_name);
			 clearstatcache();
			$image = $thumb->create($file_dir_origin.$file_name);
			if($c == true){
				$image->adaptiveResize($w, $h);
			}elseif($c == false){
				$image->resize($w, $h);
			}
			if(is_dir($file_dir_cache)){
				$image->save($file_dir_cache.$cache_file_name);
			}else{
				mkdir($file_dir_cache,  0777, true);
				$image->save($file_dir_cache.$cache_file_name);
			}
			$image->show();
		}elseif(!is_file($file_dir_cache.$cache_file_name)){
			$image = $thumb->create($file_dir_origin.$file_name);
			if($c == true){
				$image->adaptiveResize($w, $h);
			}elseif($c == false){
				$image->resize($w, $h);
			}
			if(is_dir($file_dir_cache)){
				$image->save($file_dir_cache.$cache_file_name);
			}else{
				mkdir($file_dir_cache,  0777, true);
				$image->save($file_dir_cache.$cache_file_name);
			}  
			$image->show();
		}else{
        	$image = $thumb->create($file_dir_cache.$cache_file_name);
        	$image->show();
        }



	}
	public function hotel()
	{	$thumb = $this->load->library('PhpThumbFactory');
		$this->load->library('service/comp_tiketcom');
		$w = null;
		$h = null;
		if( is_numeric($this->uri->rsegment(3)) && is_numeric($this->uri->rsegment(4)) && $this->uri->total_rsegments() >= 3){
			$path = $this->uri->rsegment(5);
			$w = $this->uri->rsegment(3);
			$h = $this->uri->rsegment(4);
		}elseif(is_numeric($this->uri->rsegment(3)) && !is_numeric($this->uri->rsegment(4)) && $this->uri->total_rsegments() >= 3){
			$path = $this->uri->rsegment(5);
			$w = $this->uri->rsegment(3);
			$h = $w;
		}elseif($this->uri->total_rsegments() >= 2 && is_numeric($this->uri->rsegment(3))){
			$path = $this->uri->rsegment(4);
			$w = $this->uri->rsegment(3);
			$h = $w;
		}else{
			$path = $this->uri->rsegment(3);
		}
		
		

		$path =  $this->comp_tiketcom->_decrypt_img_path($path);
//		$path = './assets/media/mine.jpg';
		$img = $thumb->create($path);
		if($w != null && $h != null) $img->adaptiveResize($w, $h);
		$img->show();
		
	}
	
}
