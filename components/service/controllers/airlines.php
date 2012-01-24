<?
/**
* 
*/
class Airlines extends REST_Controller
{
	
	function __construct()
	{
		parent::__construct();
	}
	public function test_get()
	{
		$this->response(array('no error'));
	}
	public function test2_get()
	{
		$this->response(suicide('service/airlines/test', FALSE));
	}
	public function search_post()
	{
		$posted = array(
			'date_depart' 	=> $this->post('depart'),
			'date_return' 	=> $this->post('return'),
			'route_from' 	=> $this->post('from'),
			'route_to'    	=> $this->post('to'),
			'passengers'	=> $this->post('passengers'),
			'comp_include'  => ($airl = $this->post('airlines')) ? $this->post('airlines') : null ,
			'max_fare'		=> ($max_fare = $this->post('max_fare')) ? $max_fare : 10,
			'actor'			=> ($actor = $this->post('actor')) ? $actor : 'CUS',
		);
		$log = new Search_fare_log($posted);
		if(!$log->is_valid()){
			$this->response($log->errors->full_messages(), 500);
		}else{
			$log->save();
		}
		// execute all maskapai simultanous on the background
		foreach(json_decode($log->comp_include) as $comp)
			suicide('service/airlines/exec_search/'.$log->id.'/'.$comp);
		
		$this->response($log->to_array());
		
	}
	// only rest suicide will call this function
	public function exec_search_get()
	{
		$id = $this->uri->rsegment(3);
		$maskapai = $this->uri->rsegment(4);
		try {
			$log = Search_fare_log::find($id);
		} catch (Exception $e) {
			
		}
		$param = $log->to_array();
		// reformat the date
		foreach($param as $key => $val){
			if($key == 'date_return' || $key == 'date_depart'){
				if($val != null){
				$param[$key] = show_date($val, 'Y-m-d');
				}
			}
		}
		if(
			Search_fare_item::count(
				array(
					'conditions' => 'log_id = '.$id.' && company = "'.strtoupper($maskapai).'"' 
					)
				) == 0 &&
			!$this->_flag_comp_is_done($id, $maskapai)
			){
				// START FETCHING
				$this->load->library('comp_maskapai');
				$comp 	= $this->comp_maskapai->_load($maskapai);
				$result =  $comp->doSearch($param);
				$comp->closing();
				// END FETCHING
				// if result is count = 0 so flag as false and exit
				if(count($result) == 0 ) {
					$this->_flag_comp_to_done($id, $maskapai, false);
					exit();
				}
				// PUSHING RESULT to DB
				foreach($result as $candidate_item)
				{
					$new_item = new Search_fare_item($candidate_item);
					$new_item->save();
				}
				// push to db and result fetch count != 0
				$this->_flag_comp_to_done($id, $maskapai, true);
				exit();
			
			}
	}
	private function _flag_comp_is_done($id, $maskapai)
	{
		try {
			$log = Search_fare_log::find($id);		
		} catch (Exception $e) {
			return TRUE;
		}
		$complete = ($log->complete_comp == null) ? FALSE : json_decode($log->complete_comp, true);
		
		if($complete == FALSE) return FALSE;
	
		if(isset($complete[$maskapai]) && $complete[$makapai] == TRUE)
			return TRUE;
		else
			return FALSE;
	}
	private function _flag_comp_to_done($id, $maskapai, $value = true)
	{
		try {
			$log = Search_fare_log::find($id);		
		} catch (Exception $e) {
			return ;
		}
		$complete = ($log->complete_comp == null) ? array() : json_decode($log->complete_comp, true) ;
		if($value = TRUE ){
			$complete[$maskapai] = TRUE;
		}else{
			$complete[$maskapai] = FALSE;
		}
		$log->complete_comp = json_encode($complete);
		$log->save();
	}
	public function search_get()
	{
		// validate log id
		if(!$id_log = $this->get('id')) $this->response(array('error' => 'please provide the id log search'), 500);
		if(!$maskapai = $this->get('airlines')) $this->response(array('error' => 'please provide the airlines'), 500);
		// check that log really exists
		try {
			$log = Search_fare_log::find($this->get('id'));
		} catch (Exception $e) {
			$this->response($e->getMessage(), 500);
		}
		
		$param = $log->to_array();
		
		// reformat the date
		foreach($param as $key => $val){
			if($key == 'date_return' || $key == 'date_depart'){
				if($val != null){
				$param[$key] = show_date($val, 'Y-m-d');
				}
			}
		}
		
		// only FETCH when there is no redcord found in db
		if(
			Search_fare_item::count(
				array(
					'conditions' => 'log_id = '.$id_log.' && company = "'.strtoupper($maskapai).'"' 
					)
				) == 0
			){
				// START FETCHING
				$this->load->library('comp_maskapai');
				$comp 	= $this->comp_maskapai->_load($maskapai);
				$result =  $comp->doSearch($param);
				$comp->closing();
				// END FETCHING
				if(count($result) == 0 )$this->response('no fare found', 500);
				// PUSHING RESULT to DB
				foreach($result as $candidate_item)
				{
					$new_item = new Search_fare_item($candidate_item);
					$new_item->save();
				}
			
		}
	//	$this->response($result);
		
		
		// START RETRIVE RESULT FROM DB
		
		if(element('type', $param) == 'roundtrip'){
			$res = $this->_retrive_roundtrip_result($param, $maskapai);
		}else{
			$res = $this->_retrive_roundtrip_result($param, $maskapai);
		}
		if($res == FALSE) $this->response('null', 500);
		$res['log'] = $param;
		$this->response($res);
		
	}
	public function book_post()
	{
	
		try {
			$fare = Search_fare_item::find($this->input('fare_id'));
			$fare_data = $fare->to_array(array('include' => array('log')));
		} catch (Exception $e) {
			$this->response('there is no valid fare with id posted' ,500);
		}
		
		$this->load->library('comp_maskapai');
		$maskapai = $this->comp_maskapai->load( element('company', $fare_data) );
		$book = $maskapai->doBooking($fare_data, $passengers_data, $customer_data);
		
		switch (TRUE) {
			case (is_numeric($book)):
				// price change ..
				// update selected fare price
				$fare->price = $book;
				$fare->save();
				// need tobe rebook
				$this->response(array('info' => 'price change', 'new_price' => $fare->price ), 500);
				break;
			case(is_array($book)):
				// book success
				
				break;
			default:
				// book failed
				break;
		}
		
		
		
		
	}
	
	// PRIVATE FUNCTION //
	private function _retrive_oneway_result($log, $maskapai)
	{
		try {
				$depart_q = Search_fare_item::find(
						'all',
						array(
							'conditions' => array(  'log_id = ? AND company = ? AND type = ?', $log['id'], $maskapai , 'depart'),
							'order' => 'price asc',
							'limit' => element('max_fare', $log)
							)
						);

				$final_data = array(
					'depart' => array(
						'fares' 		=> $this->db_util->multiple_to_array($depart_q),
						'count_fares' 	=> count($depart_q),
						'count_flight' 	=> $this->_count_flight($depart_q),
					)
					);
			return $final_data;
		} catch (Exception $e) {
			return false;
		}
	}
	private function _retrive_roundtrip_result($log, $maskapai)
	{
		try {
			$return_q = Search_fare_item::find(
					'all',
					array(
						'conditions' => array(  'log_id = ? AND company = ? AND type = ?', $log['id'], $maskapai , 'return'),
						'order' => 'price asc',
						'limit' => element('max_fare', $log)
						)
					);
				
			$depart_q = Search_fare_item::find(
					'all',
					array(
						'conditions' => array(  'log_id = ? AND company = ? AND type = ?', $log['id'], $maskapai , 'depart'),
						'order' => 'price asc',
						'limit' => element('max_fare', $log)
						)
					);
				
			$final_data = array(
				'depart' => array(
					'fares' 		=> $this->db_util->multiple_to_array($depart_q),
					'count_fares' 	=> count($depart_q),
					'count_flight' 	=> $this->_count_flight($depart_q),
				),
				'return' => array(
					'fares' 		=> $this->db_util->multiple_to_array($return_q),
					'count_fares' 	=> count($depart_q),
					'count_flight' 	=> $this->_count_flight($return_q),
				)
				
			);
			return $final_data;
		} catch (Exception $e) {
			return FALSE;
		}
	}
	
	private function _count_flight($model)
	{
		$flight_coll = array();
		foreach($model as $item){
			if(in_array($item->flight_no, $flight_coll)) continue;
			array_push($flight_coll, $item->flight_no);
		}
		return count($flight_coll);
	}

	
	
	
}

