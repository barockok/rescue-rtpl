<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* 
*/
class Uploadfile extends REST_Controller
{
	
	function __construct()
	{
		parent::__construct();
	}
	public function image_post()
	{
	
		$config['upload_path'] = './uploads/';
		$config['allowed_types'] = 'gif|jpg|png';
		$config['max_size']	= '10000';
		$config['max_width']  = '1024';
		$config['max_height']  = '768';

		$this->load->library('upload', $config);

		if ( ! $this->upload->do_upload('userfile'))
		{
			$error = array('error' => $this->upload->display_errors());
			$this->response($error, 500);
		}
		else
		{
			$this->response(array_merge($this->upload->data(), $this->post()));
		
		}
	
	
	}
}

