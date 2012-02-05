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
			$this->response(array('no log found'));
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
				$this->response($result);
				exit();
			
			}else{
				$this->response($param);
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
	
		if(isset($complete[$maskapai]) && $complete[$maskapai] == TRUE)
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
		$uri = $this->uri->ruri_to_assoc(3);
		if(!$id_log = element('id', $uri)) $this->response(array('error' => 'please provide the id log search'), 500);
		
		try {
			$log = Search_fare_log::find($id_log);

			// check flage commplete
			$should = json_decode($log->comp_include, true);
			$complete = ($log->complete_comp != null) ? array_keys(json_decode($log->complete_comp, true)) : array();
			asort($should); asort($complete);
			$not_complete = array();
			
			foreach($should as  $val){
				if(!in_array($val, $complete) ) array_push($not_complete , $val);
			}
			$status = array(
				'not_complete' => $not_complete,
				'should' => $should,
				'complete' => $complete
			);
			if(count($not_complete) == 0 ) $status = 'complete'; 	

		} catch (Exception $e) {
			$this->response($e->getMessage(), 500);
		}
		
		$param = $log->to_array();
		$limit_each_maskapai = (!$l = element('limit', $uri)) ? 5 : $l;

		if(element('type', $param) == 'roundtrip'){
			$res = $this->_retrive_roundtrip_result($param, $limit_each_maskapai);
		}else{
			$res = $this->_retrive_roundtrip_result($param, $limit_each_maskapai);
		}
		if($res == FALSE) $this->response('null', 500);
		$res['log'] = $param;
		$res['status'] = $status;
		$this->response($res);
		
	}
	public function log_get()
	{
		if(!$id = $this->uri->rsegment(3)) $this->response_error('please provide id');
		try {
			$log = Search_fare_log::find($id);
			$this->response($log->to_array( array('include' => array('departure_airport', 'destination_airport')) ));
		} catch (Exception $e) {
			$this->response_error($e->getMessage());
		}
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
	private function _retrive_oneway_result($log)
	{
		try {
			$log = Search_fare_log::find(element('id', $log));
			$result = array();
			foreach (json_decode($log->complete_comp) as $comp) {
				$_item = Search_fare_item::find(
					array(
						'conditions' => array('log_id = ? AND company = ? AND type =?', $log->id, strtoupper($comp), 'oneway'),
						'order' => 'price asc',
						'limit' => $log->max_fare,
						)
					);
				array_merge($result, $this->db_util->multiple_to_array($_item));
			}
			$depart_q = $result;
			$final_data = array(
				'depart' => array(
					'fares' => $depart_q,
					'count_fares' => count($depart_q),
					'count_flight' => $this->_count_flight($depart_q, false)
					)
				);
			return $final_data;

		} catch (Exception $e) {
			return false;
		}
	}
	private function _retrive_roundtrip_result($log)
	{
		
			$log = Search_fare_log::find(element('id', $log));
			$depart_q = array();
			$this->response(json_decode($log->complete_comp));
			foreach (json_decode($log->complete_comp) as $comp => $status) {
				if($status == FALSE) continue;
				$depart_q_item = Search_fare_item::find('all', array(
							'conditions' => array(
								'log_id = ? AND type = ? AND company = ?',
								$log->id, 'depart', strtoupper($comp)
								),
							'limit' => $log->max_fare,
							'order' => 'price asc',
						)
				);
				if(count($depart_q_item) > 0 )
					foreach ($this->db_util->multiple_to_array($depart_q_item) as $real_item) array_push($depart_q, $real_item);
			}
			
			$return_q = array();
			foreach (json_decode($log->complete_comp) as $comp => $status) {
				if($status == FALSE) continue;
				$return_q_item = Search_fare_item::find('all', array(
							'conditions' => array(
								'log_id = ? AND type = ? AND company = ?',
								$log->id, 'return', strtoupper($comp)
								),
							'limit' => $log->max_fare,
							'order' => 'price asc',
						)
				);
				if(count($depart_q_item) > 0 )
					foreach ($this->db_util->multiple_to_array($return_q_item) as $real_item) array_push($return_q, $real_item);
			}
			
			$final_data = array(
					'depart' => array(
						'fares' => $depart_q,
						'count_fares' => count($depart_q),
						'count_flight' => $this->_count_flight($depart_q, false)
					),
					'return' => array(
						'fares' => $return_q,
						'count_fares' => count($return_q),
						'count_flight' => $this->_count_flight($return_q, false)
					)
				);
			return $final_data;
		
	}
	private function bck_retrive_roundtrip_result($log, $limit)
	{
		try {
	
			
			$return_q = Search_fare_item::find(
					'all',
					array(
						'conditions' => array(  'log_id = ? AND type = ?', $log['id'], 'return'),
						'order' => 'price asc',
						'limit' => element('max_fare', $log)
						)
					);
			
			
			
			
			
			$depart_q = Search_fare_item::find(
					'all',
					array(
						'conditions' => array(  'log_id = ? AND type = ?', $log['id'], 'depart'),
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
	
	private function _count_flight($model, $object=true)
	{
		if($object == true){
			$flight_coll = array();
			foreach($model as $item){
				if(in_array($item->flight_no, $flight_coll)) continue;
				array_push($flight_coll, $item->flight_no);
			}
		
		return count($flight_coll);
		}else{
			$flight_coll = array();
			foreach($model as $item){
				if(in_array(element('flight_no', $item), $flight_coll)) continue;
				array_push($flight_coll, element('flight_no', $item));
			}
		
		return count($flight_coll);
		}
	}

	
	
	
}

