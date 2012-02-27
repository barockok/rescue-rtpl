<?
/**
* 
*/
class Airlines extends REST_Controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->fetch_time_limit = 5;
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
			'comp_include'  => 'batavia,garuda,merpati,sriwijaya,lion',
			'max_fare'		=>  5,
			'actor'			=> ($actor = $this->post('actor')) ? $actor : 'CUS',
		);
		$log = new Service_fare_log($posted);
		if(!$log->is_valid()){
			$this->response($log->errors->full_messages(), 500);
		}else{
			$log->save();
		}
		// execute all maskapai simultanous on the background
		suicide('service/airlines/process_search/'.$log->id);
		
		$this->response($log->to_array());
		
	}
	
	public function process_search_get()
	{
			$id = $this->uri->rsegment(3);
			try {
				$log = Service_fare_log::find($id);
			} catch (Exception $e) {
				// CRETA LOG;
				
				exit();
			}
			
			//log find, get what to execute
			$should = json_decode($log->comp_include);
			$complete_source = ($complete = $log->complete_comp != null) ? json_decode($complete) : FALSE;
			$will_process = array();
			
			if(is_array($complete_source)){
				foreach($should as $comp){
					if(in_array($comp, array_keys($complete_source)) AND $complete_source[$comp] == TRUE) continue;
					array_push($will_process, $comp);
				}
			}else{
				$will_process = $should;	
			}
			$limit = count($will_process);
			
			//$this->response($will_process);
			
			for ($i=0; $i < $limit ; $i++) { 
				${'subprocess_'.$will_process[$i]} = curl_init();
				$sub = 	${'subprocess_'.$will_process[$i]};
				curl_setopt($sub, CURLOPT_URL, 			 site_url().'service/airlines/exec_search/'.$id.'/'.$will_process[$i] );
				curl_setopt($sub, CURLOPT_HTTPHEADER, 	array('X-API-KEY:'.SELF_API_KEY) );
			}
			// Declare master process
			$master_process = curl_multi_init();
			
			// add sub process (each maskapai opt) to master
			for ($i=0; $i < $limit ; $i++) { 
				$sub = 	${'subprocess_'.$will_process[$i]};
				curl_multi_add_handle($master_process,$sub);
			}
			
			######### execute the all pros with master parallely ###############
			$active = null;
		
			do {
			    $mrc = curl_multi_exec($master_process, $active);
			} while ($mrc == CURLM_CALL_MULTI_PERFORM);

			while ($active && $mrc == CURLM_OK) {
			    if (curl_multi_select($master_process) != -1) {
			        do {
			            $mrc = curl_multi_exec($master_process, $active);
			        } while ($mrc == CURLM_CALL_MULTI_PERFORM);
			    }
			}
			
			// remove all sub processs
			for ($i=0; $i < $limit ; $i++) { 
				$sub =	${'subprocess_'.$will_process[$i]};
				curl_multi_remove_handle($master_process, $sub);
			}
			// close master process
			curl_multi_close($master_process);
			echo $mrc;
			
			//TODO : Grap all result to a log ..
			
			
	}
	
	// only rest suicide will call this function
	public function exec_search_get()
	{
		$id = $this->uri->rsegment(3);
		$maskapai = $this->uri->rsegment(4);
		try {
			$log = Service_fare_log::find($id);
		} catch (Exception $e) {
			$this->response_error(array('no log found'));
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
			Service_fare_item::count(
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
				if(is_array($result) && count($result) > 0 ) {
						// PUSHING RESULT to DB
						foreach($result as $candidate_item)
						{
							$new_item = new Service_fare_item($candidate_item);
							$new_item->save();
						}
						// push to db and result fetch count != 0
						$this->_flag_comp_to_done($id, $maskapai, true);
						$this->response($result);
						exit();
				}else{
					$this->_flag_comp_to_done($id, $maskapai, false);
					exit();
				}
			}else{
				$this->response($param);
			}
	}
	private function _flag_comp_is_done($id, $maskapai)
	{
		try {
			$log = Service_fare_log::find($id);		
		} catch (Exception $e) {
			return TRUE;
		}
		$complete = ($log->complete_comp == null) ? FALSE : json_decode($log->complete_comp, true);
		
		if($complete == FALSE) return FALSE;
	
		if(isset($complete[$maskapai]) && $complete[$maskapai])
			return TRUE;
		else
			return FALSE;
	}
	private function _flag_comp_to_done($id, $maskapai, $value = true)
	{
		try {
			$log = Service_fare_log::find($id);		
		} catch (Exception $e) {
			return ;
		}
		$complete = ($log->complete_comp == null) ? array() : json_decode($log->complete_comp, true) ;
		if($value){
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
			$log = Service_fare_log::find($id_log);
		
			$_s_current_time = date('Y-m-d H:i:s');
			$current_time 	= new DateTime($_s_current_time);
			$last_try 		= new DateTime($log->last_try);
			$interval = $current_time->diff($last_try)->format('%i') ;
			$should = json_decode($log->comp_include, true);
			$complete = ($log->complete_comp != null ) ? array_keys(json_decode($log->complete_comp, true)) : array();
		
			if($interval >= $this->fetch_time_limit ){
				foreach($should as $key )
					if( !in_array($key, $complete) )
						$this->_flag_comp_to_done($log->id, $key , FALSE);
				$log->last_try = $current_time;
				$log->save();
			}
		
			// re-retrive log
			$log = Service_fare_log::find($id_log);


			// check flage commplete
			$should = json_decode($log->comp_include, true);
			$complete = ($log->complete_comp != null) ? array_keys(json_decode($log->complete_comp, true)) : array();
			asort($should); asort($complete);
			$not_complete = array();
			
			foreach($should as  $val){
				if(!in_array($val, $complete) ) array_push($not_complete , $val);
			}
			$status = array(
				'not_complete' =>$not_complete,
				'should' => $should,
				'complete' => ($log->complete_comp != null) ? array_keys(json_decode($log->complete_comp, true)) : null,
			);
			
			if(count($not_complete) == 0 ) $status = 'complete'; 	

		} catch (Exception $e) {
			$this->response($e->getMessage(), 500);
		}
		
		$param = $log->to_array();
		$limit_each_maskapai = (!$l = element('limit', $uri)) ? 5 : $l;

		$final_res['interval'] 	= $interval;
		$final_res['log'] 		= $param;
		$final_res['status'] 	= $status;
		$final_res['results'] 	= $this->_fetch_formula($log->id);
		$this->response($final_res);
		
	}
	public function log_get()
	{
		if(!$id = $this->uri->rsegment(3)) $this->response_error('please provide id');
		try {
			$log = Service_fare_log::find($id);
			$this->response($log->to_array( array('include' => array('departure_airport', 'destination_airport')) ));
		} catch (Exception $e) {
			$this->response_error($e->getMessage());
		}
	}
	public function fare_get()
	{
		if (!$id = $this->uri->rsegment(3)) $this->response_error('Please provide error');
		try {
			$fare = Service_fare_item::find($id);
			$this->response($fare->to_array(array('include' => array('log'))));
		} catch (Exception $e) {
			$this->response_error($e->getMessage());
		}
	}
	public function book_post()
	{
	
		try {
			$fare = Service_fare_item::find($this->input('fare_id'));
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
	// PRIVATE FUNCTION //
	private function _retrive_oneway_result($log)
	{
		
			$log = Service_fare_log::find(element('id', $log));
			$depart_q = array();
			$comps = ($log->complete_comp != null) ? json_decode($log->complete_comp, FALSE) : FALSE ;
			
			if($comps == FALSE) return array();
			foreach ($comps as $comp => $status) {
				if($status == FALSE) continue;
					$depart_q_item = Service_fare_item::find('all', array(
							'conditions' => array(
								'log_id = ? AND type = ? AND company = ?',
								$log->id, 'depart', strtoupper($comp)
								),
							'limit' => $log->max_fare,
							'order' => 'price asc',
						)
				);
			
				if(count($depart_q_item) > 0 )
					foreach ($this->db_util->multiple_to_array($depart_q_item, array('except'=> array('meta_data'))) as $real_item) array_push($depart_q, $real_item);
			
			
			}
			
	
			$final_data = array(
					'depart' => array(
						'fares' => $depart_q,
						'count_fares' => count($depart_q),
						'count_flight' => $this->_count_flight($depart_q, false)
					),
				
				);
			return $final_data;
		
			

	
	}
	private function _retrive_roundtrip_result($log)
	{
		
			$log = Service_fare_log::find(element('id', $log));		
			$comps = ($log->complete_comp != null) ? json_decode($log->complete_comp, FALSE) : FALSE ;
			if($comps == FALSE) return array();
			$depart_q = array(); $return_q = array();
			foreach ($comps as $comp => $status) {
				if(!$status) continue;
				
				// Retrive Depart
				$depart_q_item = Service_fare_item::find('all', array(
							'conditions' => array(
								'log_id = ? AND type = ? AND company = ?',
								$log->id, 'depart', strtoupper($comp)
								),
							'limit' => $log->max_fare,
							'order' => 'price desc',
						)
					);
			
				if(count($depart_q_item) > 0 )
					foreach ($this->db_util->multiple_to_array($depart_q_item, array('except'=> array('meta_data'))) as $real_item) array_push($depart_q, $real_item);
				
				// Retrive return	
				$return_q_item = Service_fare_item::find('all', array(
							'conditions' => array(
								'log_id = ? AND type = ? AND company = ?',
								$log->id, 'return', strtoupper($comp)
								),
							'limit' => $log->max_fare,
							'order' => 'company asc',
						)
				);
				if(count($depart_q_item) > 0 )
					foreach ($this->db_util->multiple_to_array($return_q_item, array('except' => array('meta_data'))) as $real_item) array_push($return_q, $real_item);
			
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

	public function _fetch_formula($id, $limit = FALSE)
	{
		//try {
			$log = Service_fare_log::find($id);
			$comps = json_decode($log->comp_include);
			$limit = (is_numeric($limit)) ? $limit : $log->max_fare;
			$depart_ids = array(); $return_ids = array();
				$d_q = '';
				for($i = 0 ; $i < count($comps) ; $i++){
						$comp = $comps[$i] ;
						$d_q .= "(select * from service_fare_item where company = '".$comp."' and log_id = ".$id." AND type = 'depart' ORDER BY price ASC limit ".$limit."  )";
						$d_q .= (($i+1) < count($comps)) ? "UNION ALL" : "";
				}
				$d_q .= ' order by price ASC';

				$d_q = Service_fare_log::find_by_sql($d_q);
				if(count($d_q) > 0 ) foreach($d_q as $item) array_push($depart_ids, $item->to_array());
			
			if($log->type == 'roundtrip'){
				
				$r_q = '';
				for($i = 0 ; $i < count($comps) ; $i++){
						$comp = $comps[$i] ;
						$r_q .= "(select * from service_fare_item where company = '".$comp."' and log_id = ".$id." AND type = 'return'  ORDER BY price ASC limit ".$limit." )";
						$r_q .= (($i+1) < count($comps)) ? "UNION ALL" : "";
				}
				$r_q .= ' order by price ASC';

				$r_q = Service_fare_log::find_by_sql($r_q);
				if(count($r_q) > 0 ) foreach($r_q as $item) array_push($return_ids, $item->to_array());
				
				return array(
						'best_combine' => $this->_get_best_combine_fares($depart_ids, $return_ids),
						'depart' => array(
							'fares' => $depart_ids,
							'count_fares' => count($depart_ids),
							'count_flight' => $this->_count_flight($depart_ids, false)
						),
						'return' => array(
							'fares' => $return_ids,
							'count_fares' => count($return_ids),
							'count_flight' => $this->_count_flight($return_ids, false)
						),
				);
			
			}
			return array(
				'depart' => array(
					'fares' => $depart_ids,
					'count_fares' => count($depart_ids),
					'count_flight' => $this->_count_flight($depart_ids, false)
				),
			);
			
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
	private function _get_best_combine_fares($departs, $returns, $limit = FALSE)
	{
		$dep_limit = (is_numeric($limit)) ? $limit : count($departs);
		$ret_limit = (is_numeric($limit)) ? $limit : count($returns);
		if((count($departs) < 1) || (count($returns) < 1 )) return array();
	
		$best_candidate = array();
		for($i = 0 ; $i < $dep_limit ; $i ++){
			$depart = $departs[$i];
			for($j = 0 ; $j < $ret_limit ; $j++){
				
				$return = $returns[$j];
				$new_data = array(
						'combine_price' => element('price', $depart) + element('price', $return),
						'depart' => $depart,
						'return' => $return,
					);

				array_push($best_candidate, $new_data);	
			}
			
		}
		$best_sort = array_values(array_sort($best_candidate, 'combine_price'));
		// limit the result
		$res_limit = (is_numeric($limit)) ? $limit : 5;
		$best_limit = array();
		
		for ($i=0; $i < $res_limit; $i++) { 
			array_push($best_limit, $best_sort[$i]);
		}
		return $best_limit;
	
	}
	public function testa_get()
	{
		echo 'loaded';
	}
	public function search_promo_post()
	{
		if(!$post = $this->post('src')) $this->response_error('Please Provide the variable');
		// check first in db that all ready have one;
		$log = Service_fare_promo_log::find('last',
			array('conditions' => 
				array(
					'original = ? and destination = ? and start_date = ? and end_date = ?', 
					element('original', $post ),
					element('destination', $post),
					element('start_date', $post),
					element('end_date', $post)
					) 
				)
			);
			
			if($log){
				$promo_log = $log->to_array(array('include' => array('destination_airport', 'departure_airport')));
				$this->response($promo_log);
			}
		
		// if not found , so create new one
		
		try {
			$log = new Service_fare_promo_log($post);
			if(!$log->is_valid()) $this->response_error($log->errors->full_messages());
			$log->save();
			$promo_log = $log->to_array(array('include' => array('destination_airport', 'departure_airport')));
			// exceute promo
			$this->_execute_promo($promo_log);
			
			$this->response($promo_log);
			
		} catch (Exception $e) {
			$this->response_error($e->getMessage());
		}
		
	}
	public function search_promo_get()
	{
		$id = $this->uri->rsegment(3);
		$limit = ($limit = $this->get('limit')) ? $limit : 3;
		try {
			$promo_log = Service_fare_promo_log::find($id);
		} catch (Exception $e) {
			$this->response_error($e->getMessage());
		}
		
		$promo_log = $promo_log->to_array(array('include' => array('search')));
		$fares = array();
		foreach(element('search', $promo_log) as $a_log_search){
			$raw = $this->_retrive_oneway_result($a_log_search);
			foreach(element('fares', element('depart', $raw)) as $fare) array_push($fares, $fare);
		}
		// sort by price
		$fares = array_sort($fares, 'price', SORT_ASC);
		// limited result
		$fares = array_slice($fares, 0, $limit);
		
		$this->response($fares);
		
	}
	private function _execute_promo($promo_log = array())
	{
		$formated_start_date = show_date(element('start_date', $promo_log), 'Y-m-d');
		$formated_end_date = show_date(element('end_date', $promo_log), 'Y-m-d');		
		for($i = 0 ; $i < element('count_day', $promo_log) ; $i++ ){
			$depart_date = date('Y-m-d', strtotime('+'.$i.' day', strtotime($formated_start_date))) ;
			$search_log = array(
				'date_depart' 	=> $depart_date,
				'route_from' 	=> element('original', $promo_log),
				'route_to'    	=> element('destination', $promo_log),
				'passengers'	=> 1,
				'comp_include'  => 'batavia,garuda,merpati,sriwijaya,lion',
				'max_fare'		=>  5,
				'actor'			=>  element('actor', $promo_log),
				'src_promo_id' => element('id', $promo_log),
				
			);
			$log_search = new Service_fare_log($search_log);
			if(!$log_search->is_valid()){
				//	$this->response($log->errors->full_messages(), 500);
				}else{
				$log_search->save();
			}
			
			suicide('service/airlines/process_promo_search/'.element('id', $promo_log).'/'.$log_search->id);
			
		}
	}
	public function process_promo_search_get()
	{
		$promo_id = $this->uri->rsegment(3);
		$log_id   = $this->uri->rsegment(4);
		
		try {
			$promo = Service_fare_promo_log::find($promo_id);
		} catch (Exception $e) {
			// create log
			exit();
		}
		
		suicide('service/airlines/process_search/'.$log_id, FALSE);
		// falg this as coplete
		// calculate percentage
		$current = $promo->status;
		$total = $promo->count_day;
		$a_value = ((1*100)/$total);
		$promo->status = $current+$a_value;
		$promo->save();
	
	}
	public function _last_promo_get()
	{
		
		$limit = ($limit = $this->get('limit')) ? $limit : 100;
		try {
				$promo_log = Service_fare_promo_log::last();
		} catch (Exception $e) {
				$this->response_error($e->getMessage());
		}

		$promo_log = $promo_log->to_array(array('include' => array('search')));
		$fares = array();
		foreach(element('search', $promo_log) as $a_log_search){
				$raw = $this->_retrive_oneway_result($a_log_search);
				foreach(element('fares', element('depart', $raw)) as $fare) array_push($fares, $fare);
		}
			// sort by price
		$fares = array_sort($fares, 'price', SORT_ASC);
			// limited result
		$fares = array_slice($fares, 0, $limit);

		$this->response($fares);
	}
	
	public function last_promo_get()
	{
		
		$limit = ($limit = $this->get('limit')) ? $limit : 100;
	
		try {
				$promo_log = Service_fare_promo_log::last();
		} catch (Exception $e) {
				$this->response_error($e->getMessage());
		}
		//$this->response(array('asu'));
		
		$promo_log = $promo_log->to_array(array('include' => array('search')));
		$fares = array();
		//$this->response($promo_log);
		
		// building the search id
		$search_ids = array();
		//$this->response(array('asu'));
		
		foreach(element('search', $promo_log) as $a_log_search) {
			$a_search = $this->_fetch_formula(element('id', $a_log_search), $limit) ;
			if(count($a_search['depart']['fares']) > 0)
				foreach($a_search['depart']['fares'] as $fare)
				array_push($fares, $fare);
		}
		
		shuffle($fares);
		$fares = array_slice($fares, 0, $limit);
		$this->response($fares);
		
	}
	
	
	
	
	
}

