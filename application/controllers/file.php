<?
/**
* 
*/
class File extends REST_Controller
{
	
	function __construct()
	{
		parent::__construct();
	}
	public function upload_post()
	{	
		$this->load->helper('string');
		
		$config['upload_path'] = './assets/media';
		$config['allowed_types'] = 'gif|jpg|png|gif';
		$config['max_size']	= '10000';
		$config['file_name'] = date('Ymd').'_'.random_string('alnum', 32);
		$config['encrypt_name'] = true;
		
		$this->load->library('upload', $config);

		
			if ( ! $this->upload->do_upload('file'))
			{
				 $data = array(
				 	'error' => true,
					'message' => $this->upload->display_errors(),
				 );	
				$this->response($data, 500);
			}
			else
			{
				$origin_data = $this->upload->data();
				$origin_data['relative_path'] = '/assets/media/'.$origin_data['file_name'];
				$data = array(
					'error' => false,
					'data' => $origin_data,
				);
				$this->response($data, 200);
			}
	
		
	
		
	}
}
