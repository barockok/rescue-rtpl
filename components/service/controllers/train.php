<?
/**
* 
*/
class Train extends REST_Controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->library('comp_train');
	}
	public function search_post()
	{
		if(!$post_src = $this->post('src')) $this->response_error('Please Provide variable');
		try {
			$log = new Service_train_log($post_src);
			if(!$log->is_valid()) $this->response_error($log->errors->full_messages());
			
			$log->save();
			$log = $log->to_array(array('include' => array('destination_stasiun', 'original_stasiun')));
			$ex = $this->comp_train->doSearch($log);
		//	if(is_array($ex)) 
			if(is_array($ex)){
					$insert = new Service_train_item;
						foreach($ex as $arate)
				 			$insert->create($arate)->save();
				}
			$this->response($log);
		} catch (Exception $e) {
			$this->response_error($e->getMessage());
		}	
	}
	public function search_get()
	{
	
		if(!$id = $this->uri->rsegment(3)) $this->response_error('Provide and ID Please');
		try {
			$log = Service_train_log::find($id);
		} catch (Exception $e) {
			$this->response_error($e->getMessage());
		}
		$log = $log->to_array(array('include' => array('destination_stasiun', 'original_stasiun')));
		$log['type'] = (element('date_return', $log) == null) ? 'oneway' : 'roundtrip';
		$fares = array();
		if($log['type'] == 'oneway'){
			$fares = Service_train_item::find(
				'all', 
				array(
					'conditions' => array('log_id =? ', element('id', $log)),
					'order' => 'price asc',
					)
				);
			$fares = $this->db_util->multiple_to_array($fares);
		}else{
			$depart = Service_train_item::find(
				'all', 
				array(
					'conditions' => array('log_id =? AND type = ?', element('id', $log) , 'depart'),
					'order' => 'price asc',
					)
				);
			$depart = $this->db_util->multiple_to_array($depart);
			
			$return = Service_train_item::find(
				'all', 
				array(
					'conditions' => array('log_id =? AND type = ?', element('id', $log), 'return'),
					'order' => 'price asc',
					)
				);
			$return = $this->db_util->multiple_to_array($return);
			$fares = array(
				'depart' => $depart,
				'return' => $return,
			);
		}
		$log['fares'] = $fares;
		$this->response($log);
	}
	public function book_post()
	{
		
	}
}

