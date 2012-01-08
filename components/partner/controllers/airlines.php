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
			'actor'			=> $this->post('actor'),
		);
		$log = new Search_fare_log($posted);
		if(!$log->is_valid()){
			$this->response($log->errors->full_messages(), 500);
		}else{
			$log->save();
		}
		$this->response($log->to_array());
		
	}
	public function serch_get()
	{
		// validate log id
		if(!$this->get('id')) $this->response(array('error' => 'please provide the id log search'), 500);
		if(!$this->get('airlines')) $this->response(array('error' => 'please provide the airlines'), 500);
		
		try {
			$log = Search_fare_log::find($this->get('id'));
		} catch (Exception $e) {
			$this->response($e->getMessage(), 500);
		}
		
		
		// Declare Quilifeid
		$doFetch 		= true;
		// Declare Final Return
		$depart 		= array();
		$return 		= array();
		$depart_flight 	= 0;
		$reurn_flight 	= 0;
		$depart_fare 	= 0;
		$return_fare 	= 0;
	
		// get the parameter search 

	
		//$param =  $q->attributes();
		$param = $log->to_array();
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
					'conditions' => 'log_id = '.$id_log.' && company = "'.strtoupper($maskapai).'"' 
					)
				) > 0
			) $doFetch = false;
		
		// FETCHING to Maskapai Website
		// jika memang tidak ada didatbase sebelumnya
		if($doFetch == true ){
			$this->load->library('comp_maskapai');
			$comp 	= $this->comp_maskapai->_load($maskapai);
			$result = $comp->doSearch($param);
			$comp->closing();
			if(count($result) == 0) return false;
			// put result to database
			foreach($result  as $res){
				$item = new Search_fare_item($res);
				$item->save();	
			}
			
		//	$this->db->insert_batch('partner_search_fare_item', $result);
		}
	
		$maskapai = strtoupper($maskapai);
		$qD = array('conditions' => array(
										'company = ? AND log_id = ? AND type = ?', 
										$maskapai, 
										$id_log, 
										'depart' 
										),
					'order' => 'price asc',
					'limit' => element('max_fare', $param),
					);
		$q1 			= Search_fare_item::find('all', $qD);
		$depart 		= ( count($q1) > 0 ) ? $q1 : $depart ;
		$depart_fares 	= count($q1);
	
		$d_c = array();
		foreach($q1 as $q1r) if(!in_array($q1r->flight_no, $d_c)) array_push($d_c, $q1r->flight_no);
		$depart_flights = count($d_c);
	
	
		// check if search need roundtrip	
		if(element('date_return', $param) != null) {
			
			$qR = array('conditions' => array(
											'company = ? AND log_id = ? AND type = ?', 
											$maskapai, 
											$id_log, 
											'return' 
											),
						'order' => 'price asc',
						'limit' => element('max_fare', $param),
						);
			$q2 			= Search_fare_item::find('all', $qR);
			$return 		= ( count($q2) > 0 ) ? $q2 : $return ;
			$return_fares 	= count($q2);
			
			// count flight
			$r_c = array();
			foreach($q2 as $q2r) if(!in_array($q2r->flight_no, $r_c)) array_push($r_c, $q2r->flight_no);
			$return_flights = count($r_c);
		}
		
		return array(
				'data_fares'		=> array(
							'depart' 			=> $depart, 
							'return' 			=> $return,
						),
				'depart_flights' 	=> $depart_flights,
				'return_flights' 	=> $return_flights,
				'depart_fares' 		=> $depart_fares,
				'return_fares' 		=> $return_fares
				);
	
		
		
	}
	public function book_post()
	{
		# code...
	}

	
	
	
}

