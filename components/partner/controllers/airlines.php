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
		$this->response($log->to_array());
		
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
	public function book_post()
	{
		# code...
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

