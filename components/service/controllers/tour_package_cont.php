<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* 
*/
class Tour_package_cont extends REST_Controller
{
	
	function __construct()
	{
		parent::__construct();
	}
	public function create_post()
	{
		$main = elements(array('title', 'cat_id', 'l_desc', 'stock', 'price'), $this->post());
		$list_file = array();
		$virfile = (count($_FILES) == 1 ) ? array($_FILES) : $_FILES;
		foreach($_FILES as $key => $val){
			if(strpos($key, 'media_file_') !== FALSE ) array_push($list_file, $key);
		}
		try {
			$new_tp = new Tour_package($main);
			if(!$new_tp->is_valid())
				$this->response(array('error' => $new_tp->errors->full_messages()), 500);
			$new_tp->save();
			// upload the file
			$this->load->library('upload');
			$config['upload_path'] = './assets/media';
			$config['allowed_types'] = 'gif|jpg|png|gif';
			$config['max_size']	= '10000';
			$config['encrypt_name'] = true;
			
			$warning_files = array();
			$success_files = array();
	
			foreach($list_file as $file){
				$this->upload->initialize($config);
				if(!$this->upload->do_upload($file)){
					$warning_file = array(
						'file_name' => $_FILES[$file]['name'],
						'error_msg' => $this->upload->display_errors(),
					);
					array_push($warning_files, $warning_file );	
				}else{
					$updata = $this->upload->data();
					$success_file = array(
						'file_path' => '/assets/media/'.element('file_name', $updata),
						'index'		=> str_replace('media_file_', '', $file),
					);
					array_push($success_files, $success_file);
				}
			}
			$error_db_files = array();
			foreach($success_files as $file){
				//TODO : merging with the meta data form media with identifier success_file index
				$mediadb = array(
					'path' => $file['file_path'],
					'package_id' => $new_tp->id,
					'status' => 'publish',
					'desc' => 'caption',
					'type' => 'pic',
				);
				$new_media = new Tour_package_media($mediadb);
				if(!$new_media->is_valid())
					array_push($error_db_files, $mediadb);
				else
					$new_media->save();
			}
			$final_data = array(
				'new_tp' => $new_tp->to_array(array('inlcude' => array('medias'))),
				'warning_file' => (count($warning_files) > 0) ? $warning_files : FALSE, 
				'error_db_file' => (count($error_db_files) > 0) ? $error_db_files : FALSE,
			);
			$this->response($final_data, 200);
			
		} catch (Exception $e) {
			$this->response(array('error' => $e->getMessage()));
		}
		
	}
	public function view_get()
	{
		$id = ($id = $this->uri->rsegment(3)) ? $id : 'all';
		$options = ($opt = $this->get('options')) ? $opt : FALSE ;
		
		$conf = array(
			'include' => array(
				'medias' => array('path', 'desc', 'type'), 
				'category'  => array('name', 'id')
			)
		);
		
		try {
			if($id == 'all'){
				$q = ($options == FALSE ) ? Tour_package::all() : Tour_package::all($options) ;

				$count_opt = $options;
				if(isset($count_opt['limit'])) unset($count_opt['limit']);
				if(isset($count_opt['offset'])) unset($count_opy['offset']);
				$count = Tour_package::count($count_opt);
				
				$all = array();
				foreach($q as $item){
					array_push($all, $item->to_array($conf));
				}
				$final_res = array('results' => $all , 'founds' => $count);
				$this->response($final_res, 200);
			}elseif(is_numeric($id)){
				$q = Tour_package::find($id);
				$final_res = $q->to_array($conf);
				$this->response($final_res, 200);
			}else{
				$this->response(array('error' => 'not provide id') , 500);
			}
		} catch (Exception $e) {
			$final_res = array('error' => $e->getMessage());
			$this->response($final_res, 500);
		}
		
	}
}
