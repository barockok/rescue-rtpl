<?
/**
* 
*/
class Train extends REST_Controller
{
	
	function __construct()
	{
		parent::__construct();
	
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
		if(!$id_log = $this->uri->rsegment(3)) $this->response_error('No Log Id Provide');
		try {
			$log = Search_train_log::find($id_log);
			$log = $log->to_array();
		} catch (Exception $e) {
			$this->response_error($e->getMessage());
		}
		
		
	}
	public function book_post()
	{
		# code...
	}
}

