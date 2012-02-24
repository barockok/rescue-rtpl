<?
/**
* 
*/
class Train extends REST_Controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->library('comp_krl');
	}
	public function search_post()
	{
		if(!$post_src = $this->post('src')) $this->response_error('Please Provide variable');
		try {
			$log = new Search_train_log($post_src);
			if(!$log->is_valid()) $this->response_error($log->errors->full_messages());
			
			$log->save();
			$this->response($log->to_array());
		} catch (Exception $e) {
			$this->response_error($e->getMessage());
		}
		
	}
	public function search_get()
	{
	
		
	}
	public function book_post()
	{
		# code...
	}
}

