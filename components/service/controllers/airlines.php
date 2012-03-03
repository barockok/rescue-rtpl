<?
/**
* 
*/
class Airlines extends REST_Controller
{
	protected $archive_interval = '3 days';
	protected $search_interval = '1 days';
	function __construct()
	{
		parent::__construct();
		$this->fetch_time_limit = 5;
		$this->load->library('comp_maskapai');
		$this->airlines_comp = array('citilink', 'batavia', 'garuda', 'merpati', 'sriwijaya', 'lion');
		$this->archive_interval_time = 	date('Y-m-d H:i:s', strtotime('-'.$this->archive_interval, strtotime(date('Y-m-d H:i:s'))));
		$this->search_interval_time = 	date('Y-m-d H:i:s', strtotime('-'.$this->search_interval, strtotime(date('Y-m-d H:i:s'))));
		
	}
	public function test_get()
	{
		$this->response(array('no error'));
	}
	public function test2_get()
	{
		$this->response(suicide('service/airlines/test', FALSE));
	}
	public function backup_search_post()
	{
		$posted = array(
			'date_depart' 	=> $this->post('depart'),
			'date_return' 	=> $this->post('return'),
			'route_from' 	=> $this->post('from'),
			'route_to'    	=> $this->post('to'),
			'passengers'	=> $this->post('passengers'),
			'comp_include'  => 'batavia,garuda,merpati,sriwijaya,lion,citilink',
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
	public function search_post()
	{
		
		$params  = elements(array('from', 'to', 'depart', 'return', 'adult', 'child', 'infant'), $this->uri->uri_to_assoc(3), null);
		$comps = (!$this->post('airlines') ) ? array(): explode(',', $this->post('airlines'));
		$depart_exclude =   (!$this->post('ex_depart') ) ? null: explode(',', $this->post('ex_depart'));
		$return_exclude =   (!$this->post('ex_return') ) ? null: explode(',', $this->post('ex_return'));
		
		foreach($comps as $key => $val)
			if(!in_array($val, $this->airlines_comp)) unset($comps[$key]);
	
		if(count($comps) == 0)
			$comps = $this->airlines_comp;
	
		$required = array('from', 'to', 'depart','adult');
		$miss_required = array();
		foreach($params as $key => $val)
			if(in_array($key, $required) AND is_null($val))
				array_push($miss_required, $key.' is required');			
		foreach($params as $key => $val)
			if(is_null($val)) 
				unset($params[$key]);
		
		
	//	$this->response($params);
		// check date depart
		if(!validate_date(element('depart', $params)))
				array_push($miss_required, 'depart date is not valid');
		if(element('return', $params) AND !validate_date(element('return', $params)))
				array_push($miss_required, 'depart date is not valid');
		if(element('adult', $params) < 1)
				array_push($miss_required, 'adult value must be greater than 0');
				
		if(isset($params['child']) )
				if($params['child'] < 1)
					array_push($miss_required, 'child value must be greater than 0');
		if(isset($params['infant']) )
				if($params['infant'] < 1)
					array_push($miss_required, 'infant value must be greater than 0');			
		// check route
		$route_to = Ext_data_airport::find('last', array('conditions' => array('code = ?', strtoupper(element('to', $params)))));
		$route_from = Ext_data_airport::find('last', array('conditions' => array('code = ?', strtoupper(element('from', $params)))));
		if(!$route_to)
			array_push($miss_required, 'not valid destination airport code');	
		if(!$route_from)
			array_push($miss_required, 'not valid original airport code');	
		
		if(count($miss_required) > 0)
			$this->response_error(implode(', ', $miss_required));
	
		// build the log
		
		$param_depart = array(
			'route_from'	 	=> element('from', $params),
			'route_to'	 		=> element('to', $params),
			'date_depart' 		=> element('depart', $params),
			'adult' 			=> element('adult', $params),
			'infant' 			=> (element('infant', $params)) ? element('infant', $params) : 0,
			'child' 			=> (element('child', $params)) ? element('child', $params) : 0,
		);
			$depart_fares = $this->do_theSeacrh($param_depart, $comps, $depart_exclude);
	
		if(element('return', $params)){
			$param_return =  array(
				'route_from'	 	=> element('to', $params),
				'route_to'	 		=> element('from', $params),
				'date_depart' 		=> element('return', $params),
				'adult' 			=> element('adult', $params),
				'infant' 			=> (element('infant', $params)) ? element('infant', $params) : 0,
				'child' 			=> (element('child', $params)) ? element('child', $params) : 0,
			);
			
			$return_fares = $this->do_theSeacrh($param_return, $comps, $return_exclude);
		}
		
		// rountrip res
		if(element('return', $params)){
				$this->response(array(
					'depart' => array(
						'fares' => $depart_fares,
					),
					'return' => array(
						'fares' => $return_fares
					)
				));
		}
		//oneway res
		else{
				$this->response(array(
					'depart' => array(
						'fares' => $depart_fares,
					)
				));
		}
	
	}
	public function do_theSeacrh($param, $comps, $exclude = null)
	{
		
		// mark as acrhive all old item if there;
		try {
		$old_fares = Service_fare_item::find('all', array('conditions' =>  array('created_at < and archive = ?', $this->archive_interval_time, 'N')) ) ;

		if(count($old_fares) > 0)
			foreach($old_fares as $old_fare) 
				 $old_fare->archive = 'Y'; $old_fare->save();
		} catch (Exception $e) {
			
		}
	
			
		
		// now we need to determine the which comp with assing to curl
		$assign_to_curl = array();
		foreach($this->airlines_comp as $comp){
			$comp = strtoupper($comp);
			$count_comp_fares = Service_fare_item::count( 
				array(
					'conditions' => array(
						'company = ? and date_depart = ? and route_from = ? and route_to = ? and adult = ? and child = ? and infant = ? and created_at > ? and archive = ?',
						$comp, $param['date_depart'], $param['route_from'], $param['route_to'], $param['adult'], $param['child'], $param['infant'],
						$this->search_interval_time, 'N'
					)
				)
			);
			// fares was 0, and there is no worker are current doin the job with same signature, so we regiter i to curl;
			if($count_comp_fares == 0 and $this->_check_worker($comp, 'search', $param))
				array_push($assign_to_curl, $comp);
		}
		if(count($assign_to_curl) > 0)
			$this->_register_workers($assign_to_curl, 'doSearch', $param);
		
		// howerver, currently event there or not the fare items just search and return this fucking stuff
		if( is_null($exclude) ){
			$query = array(
				'conditions' => array(
					'date_depart = ? and route_to =? and route_from = ? and adult = ? and child = ? and infant = ? and archive = ?',
					$param['date_depart'], $param['route_to'], $param['route_from'], $param['adult'], $param['child'], $param['infant'], 'N'
					),
				'order' => 'price asc',
			);
			$fares = Service_fare_item::find('all', $query);
		}else{
			$query = array(
				'conditions' => array(
					'date_depart = ? and route_to =? and route_from =? and adult =? and child =? and infant =? and archive = ? and id not in (?)',
					$param['date_depart'], $param['route_to'], $param['route_from'], $param['adult'], $param['child'], $param['infant'], 'N', $exclude
					),
				'order' => 'price asc',
			);
			$fares = Service_fare_item::find('all', $query);
		}
		return (count($fares) > 0 ) ? $this->db_util->multiple_to_array($fares) : array();
		
		
	}
	private function _register_workers($company, $job, $param)
	{
		$company = (!is_array($company)) ? array($company) : $company ;
		foreach($company as $comp)
			$this->_register_worker($comp, $job, $param);
		
	}
	public function _register_worker($company, $job, $param)
	{
		$param = http_build_query(array('params' => $param));
			suicide('service/airlines/execute_worker/'.$company.'/'.$job.'/?'.$param);
	}
	private function _worker_progress($company, $job, $param)
	{
		ksort($param);
		$sig = implode('_', array_values($param));
		$new = array(
			'airlines' => strtolower($company),
			'job' => $job,
			'signature' => $sig,
		);
		$worker = new Airlines_comp_worker($new);
		$worker->save();
		return $worker;
	}

	public function execute_worker_get()
	{
		$air_comp = $this->uri->rsegment(3); 
		$job = $this->uri->rsegment(4);
		$params = $this->get('params');
		// START FETCHING
		$worker = $this->_worker_progress($company, $job, $param);
		try {
			$comp 	= $this->comp_maskapai->_load($air_comp);
			$result =  $comp->doSearch($params);
			$comp->closing();
			if(is_array($result) && count($result) > 0 ) {
					// PUSHING RESULT to DB
				foreach($result as $candidate_item)
				{
					$new_item = new Service_fare_item($candidate_item);
					$new_item->save();
				}
			}
			$worker->status = "complete";
			$worker->save();
		} catch (Exception $e) {
			$worker->log_error = $e->getMessage();
			$worker->status = "complete";
			$worker->save();
		}
	
	
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
			$this->response_error($e);
		}
	}
	public function fare_get()
	{
		if (!$id = $this->uri->rsegment(3)) $this->response_error('Please provide error');
		try {
			$fare = Service_fare_item::find($id);
			$this->response($fare->to_array(array('include' => array('log'))));
		} catch (Exception $e) {
			$this->response_error($e);
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
			$this->response_error($e);
		}
		
	}
	public function search_promo_get()
	{
		$id = $this->uri->rsegment(3);
		$limit = ($limit = $this->get('limit')) ? $limit : 3;
		try {
			$promo_log = Service_fare_promo_log::find($id);
		} catch (Exception $e) {
			$this->response_error($e);
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
				$this->response_error($e);
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
	

		
		$limit = ($limit = $this->get('limit')) ? $limit : 10;
		$airlines  = array('sriwijaya', 'merpati', 'lion', 'citilink', 'batavia');
		$fares = array();
		foreach($airlines as $comp){
			$fares_comp = Service_fare_item::find('all',
					array(
						'conditions' => array(
							'company = ?', 
							strtoupper($comp),
						),
						'limit' => $limit,
						'order' => 'price asc'
					)
				);
			if(count($fares_comp) > 0) 
				foreach($this->db_util->multiple_to_array($fares_comp) as $afare)
					$fares[] = $afare;
		}
		shuffle($fares);
		$fares = array_slice($fares, 0, $limit);
		$fares = array_values(array_sort($fares, 'price', SORT_ASC));
		
		$this->response($fares);
				
		
		
	}
	
	public function _sc_hook_add_item($cart_item)
	{
		$option = ($opt = element('option', $cart_item)) ? $opt : array();
		if(!element('depart_id', $option))
			throw new Exception("Please Provide the Fare Id for Departure", 1);
		if(!element('passengers_data', $option))
			throw new Exception("Please Prove the Passenger data", 1);
	//	if(!element('contact_data', $option))
	//		throw new Exception("Please provide contact data");
			
		$booking_data = array();
		try {
			$depart_fare = Service_fare_item::find(element('depart_id', $option));
			$comp 	= $this->comp_maskapai->_load($depart_fare->company);
			$result =  $comp->doBooking($depart_fare->to_array(array('include' => array('log'))));
			$comp->closing();
			$boooking_data['departure'] = $result;
		} catch (Exception $e) {
			throw new Exception("fare Id for departure not valid", 1);	
		}
		if(element('return_id', $option)){
			try {
				
			} catch (Exception $e) {
				
			}
		}
	}
	public function _sc_hook_delete_item($cart_item)
	{
	//	throw new Exception("Error Processing Request", 1);
		return;
	}
	public function _sc_hook_update_item($cart_item)
	{
		throw new Exception("Error Processing Request", 1);

	}
	private function _check_worker($comp, $job, $param = null)
	{
		
		if($param == null){
			if(Airlines_comp_worker::count(
				array(
					'conditions' => array(
						'airlines = ? and job = ? and status = ?',
						strtoupper($comp), $job, 'onprogress',
						)
					)
				) == 0 ){
				return true;
			}
			else
				return false;
		}
		else{
			ksort($param);
		 	$sig = implode('_', array_values($param));
			if(Airlines_comp_worker::count(
				array(
					'conditions' => array(
						'airlines = ? and job = ? and status = ? and signature =? ',
						strtoupper($comp), $job, 'onprogress', $sig
						)
					)
				) == 0 ){
				return true;
			}
			else
				return false;
		}
	}

	
	
}

