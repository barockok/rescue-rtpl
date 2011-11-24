<? 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Garuda extends Factory {

	var $_opt;	

	function __construct(){
		parent::__construct();		
		//define variable
		$this->_cookies_file = realpath('./components/maskapai/libraries/factory/cookies/garuda.txt');
		$this->login_url = 'http://gos.garuda-indonesia.com/saci/client.php';
		$this->_refer_url = 'http://gos.garuda-indonesia.com/sac/';
		$this->src_url = 'http://gos.garuda-indonesia.com/saci/clientavail.php';
		$this->getFareCalculation_url = 'http://gos.garuda-indonesia.com/saci/clientbook.php';	
		$this->idd = '170111A';
		$this->username = 'sa3maci';
		$this->password = 'mandiri01';
		
		//opt
		$this->_opt = new stdClass();
		$this->_opt->date_depart =  '2011-11-25';
		$this->_opt->date_return = '2011-11-27';
		$this->_opt->passengers = 1;
		$this->_opt->route_from = 'BTJ';
		$this->_opt->route_to = 'PKY';
		$this->_ci->load->library('my_curl');
	}
	
		
	function login(){	
																
		$post_data = array(			
			'idd' 	   => $this->idd,
			'username' => $this->username,
			'password' => $this->password
		);		
				
		
		$conf = array(
			'url' 				=> $this->login_url,
			'timeout'			=> 30,
			'header'			=> 0,
			'nobody'			=> true,
			'followlocation'	=> 1,
			'cookiejar' 		=> $this->_cookies_file,
			'cookiefile' 		=> $this->_cookies_file,
			'returntransfer'	=> 1,
			'post'				=> true,
			'referer' 			=> $this->_refer_url,
			'postfields' 		=> http_build_query($post_data , NULL, '&'),
			'useragent'			=> 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:6.0.2) Gecko/20100101 Firefox/6.0.2'
		);
		
		$data = $this->_ci->my_curl->setup($conf);
		$res = $this->_ci->my_curl->exc($data);
				
		return json_decode($res)->Session->SessionId;							
	}
	
	function src_flight(){
		$depart_flight = array();
		$return_flight = array();
		if($this->_opt->date_return!=null){
			$depart_flight = $this->src('depart');
			
			//swap route
			$temp = $this->_opt->route_from;
			$this->_opt->route_from = $this->_opt->route_to;
			$this->_opt->route_to = $temp;
			
			$this->_opt->date_depart = $this->_opt->date_return;
			
			$return_flight = $this->src('return');
		}else{
			$depart_flight = $this->src('depart');
		}		
		echo "<h2>Depart</h2>";
		print_r($depart_flight);
		echo "<h2>Return</h2>";
		print_r($return_flight);
	}
	
	function src($flight_type){
				
		//define variable
		$this->ssx = $this->login(); //get session id
		$this->idd = '170111A';		
		
		$idx=0;										
		
		//curl economy config
		$post_data_economy = array(			
			'idd'=>$this->idd,
			'ssx'=>$this->ssx,
			'Triptype'=> 'o',
			'dOriginLocation'=> $this->_opt->route_from,
			'dDestinationLocation'=> $this->_opt->route_to,
			'dDepartureDate'=> $this->_opt->date_depart,
			'ServiceClass' => 'Economy',
			'aPassengers'=> $this->_opt->passengers,
			'cPassengers'=> '0'
		);
		
		$conf1 = array(
			'url' 				=> $this->src_url,
			'timeout'			=> 60,
			'header'			=> 0,
			'nobody'			=> true,
			'followlocation'	=> 1,
			'cookiejar' 		=> $this->_cookies_file,
			'cookiefile' 		=> $this->_cookies_file,
			'returntransfer'	=> 1,
			'post'				=> true,
			'referer' 			=> $this->_refer_url,
			'postfields' 		=> http_build_query($post_data_economy , NULL, '&'),
			'useragent'			=> 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:6.0.2) Gecko/20100101 Firefox/6.0.2'
		);
		//////
		
		//curl executive config
		$post_data_executive = array(			
			'idd'=>$this->idd,
			'ssx'=>$this->ssx,
			'Triptype'=> 'o',
			'dOriginLocation'=> $this->_opt->route_from,
			'dDestinationLocation'=> $this->_opt->route_to,
			'dDepartureDate'=> $this->_opt->date_depart,
			'ServiceClass' => 'Executive',
			'aPassengers'=> $this->_opt->passengers,
			'cPassengers'=> '0'
		);
																
		$conf2 = array(
			'url' 				=> $this->src_url,
			'timeout'			=> 60,
			'header'			=> 0,
			'nobody'			=> true,
			'followlocation'	=> 1,
			'cookiejar' 		=> $this->_cookies_file,
			'cookiefile' 		=> $this->_cookies_file,
			'returntransfer'	=> 1,
			'post'				=> true,
			'referer' 			=> $this->_refer_url,
			'postfields' 		=> http_build_query($post_data_executive , NULL, '&'),
			'useragent'			=> 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:6.0.2) Gecko/20100101 Firefox/6.0.2'
		);					
		///////
		
		
		$cu_economy = curl_init();
		$cu_executive = curl_init();		
		
		//set curl option
		$data_economy = $this->_ci->my_curl->setup_($conf1);
		$data_executive = $this->_ci->my_curl->setup_($conf2);
		
		//get returntransfer		
		$res_all = $this->_ci->my_curl->multi_curl_exc($data_economy, $data_executive);		
		$res_eco = $res_all[0];
		$res_exe = $res_all[1];									
		
		$final_data_eco = array();
		$final_data_exe = array();
		
		
		//get flight and fare info (economy)
		if(isset(json_decode($res_eco)->AvailabilityResult)){ //ensure there is a flight available
			
			$data_eco = json_decode($res_eco)->AvailabilityResult->Depart->AvailabilityInformation->Option; //get available flight
			$fare_eco = json_decode($res_eco)->AvailabilityResult->FareInfomation->FareInfo; //get available fare
			$fare_list = str_split(json_decode($res_eco)->AvailabilityResult->FareInfomation->FareList); //get fare class list		
			
			if(is_array($data_eco)){
				foreach($data_eco as $r){				
					if(is_array($r->FlightSegment)){ //check whether the flight is undirect								
						$class_avail = $r->FlightSegment[0]->BookingClassAvail;
						$upper_limit = $this->getUpperLimit($class_avail,$fare_list); //get fare class upper limit			
						for ($i=0; $i<=$upper_limit; $i++){
							foreach($fare_eco as $fare){					
								if($fare->FareClass==$fare_list[$i]){//find the match 'fare class'						
									//define return variable
									$final_data_exe[$idx] = array(
										'company' => 'Garuda',
										't_depart' => $r->FlightSegment[0]->DepartureTime,
										't_transit_arrive' => $r->FlightSegment[0]->ArrivalTime,
										't_transit_depart' => $r->FlightSegment[1]->DepartureTime,
										't_arrive' => $r->FlightSegment[1]->ArrivalTime,
										'type' => $flight_type,							
										'class' => $fare->FareClass,
										'price' => $fare->PublishFare,
										'route' => $r->FlightSegment[0]->DepartureAirport.",".$r->FlightSegment[0]->ArrivalAirport.",".$r->FlightSegment[1]->ArrivalAirport,
										'meta_data' => json_encode(array(
											'flight_number' => $r->FlightSegment[0]->FlightNumber,
											'flight_number_transit' => $r->FlightSegment[1]->FlightNumber,
											'passenger' => $this->_opt->passengers,
											'fare_code' => $fare->FareBasisCode,
											'segment_no' => $r->FlightSegment[0]->SegmentNo,
										))							
									);
								}
							}//end of inner foreach	
							$idx++;			
						}//end for
					}else{
						$upper_limit = $this->getUpperLimit($r->FlightSegment->BookingClassAvail,$fare_list); //get fare class upper limit
						for ($i=0; $i<=$upper_limit; $i++){
							foreach($fare_eco as $fare){					
								if($fare->FareClass==$fare_list[$i]){	//find the match 'fare class'											
									//define return variable
									$final_data_eco[$idx]= array(
										'company' => 'Garuda',
										't_depart' => $r->FlightSegment->DepartureTime,
										't_transit_arrive' => '',
										't_transit_depart' => '',
										't_arrive' => $r->FlightSegment->ArrivalTime,
										'type' => $flight_type,							
										'class' => $fare->FareClass,
										'price' => $fare->PublishFare,
										'route' => $r->FlightSegment->DepartureAirport.",".$r->FlightSegment->ArrivalAirport,
										'meta_data' => json_encode(array(
											'flight_number' => $r->FlightSegment->FlightNumber,
											'flight_number_transit' => '',
											'passenger' => $this->_opt->passengers,
											'fare_code' => $fare->FareBasisCode,
											'segment_no' => $r->FlightSegment->SegmentNo,
										))
									);
									$idx++;								
								}
							}//end of inner foreach			
						}//end for		
					}
				}//end of outer foreach
			}else{ //if only one flight available
				$r = $data_eco;
				if(is_array($r->FlightSegment)){ //check whether the flight is undirect								
					$class_avail = $r->FlightSegment[0]->BookingClassAvail;
					$upper_limit = $this->getUpperLimit($class_avail,$fare_list); //get fare class upper limit			
					for ($i=0; $i<=$upper_limit; $i++){
						foreach($fare_eco as $fare){					
							if($fare->FareClass==$fare_list[$i]){//find the match 'fare class'						
								//define return variable
								$final_data_exe[$idx] = array(
									'company' => 'Garuda',
									't_depart' => $r->FlightSegment[0]->DepartureTime,
									't_transit_arrive' => $r->FlightSegment[0]->ArrivalTime,
									't_transit_depart' => $r->FlightSegment[1]->DepartureTime,
									't_arrive' => $r->FlightSegment[1]->ArrivalTime,
									'type' => $flight_type,							
									'class' => $fare->FareClass,
									'price' => $fare->PublishFare,
									'route' => $r->FlightSegment[0]->DepartureAirport.",".$r->FlightSegment[0]->ArrivalAirport.",".$r->FlightSegment[1]->ArrivalAirport,
									'meta_data' => json_encode(array(
										'flight_number' => $r->FlightSegment[0]->FlightNumber,
										'flight_number_transit' => $r->FlightSegment[1]->FlightNumber,
										'passenger' => $this->_opt->passengers,
										'fare_code' => $fare->FareBasisCode,
										'segment_no' => $r->FlightSegment[0]->SegmentNo,
									))							
								);
							}
						}//end of inner foreach	
						$idx++;			
					}//end for
				}else{
					$upper_limit = $this->getUpperLimit($r->FlightSegment->BookingClassAvail,$fare_list); //get fare class upper limit
					for ($i=0; $i<=$upper_limit; $i++){
						foreach($fare_eco as $fare){					
							if($fare->FareClass==$fare_list[$i]){	//find the match 'fare class'											
								//define return variable
								$final_data_eco[$idx]= array(
									'company' => 'Garuda',
									't_depart' => $r->FlightSegment->DepartureTime,
									't_transit_arrive' => '',
									't_transit_depart' => '',
									't_arrive' => $r->FlightSegment->ArrivalTime,
									'type' => $flight_type,							
									'class' => $fare->FareClass,
									'price' => $fare->PublishFare,
									'route' => $r->FlightSegment->DepartureAirport.",".$r->FlightSegment->ArrivalAirport,
									'meta_data' => json_encode(array(
										'flight_number' => $r->FlightSegment->FlightNumber,
										'flight_number_transit' => '',
										'passenger' => $this->_opt->passengers,
										'fare_code' => $fare->FareBasisCode,
										'segment_no' => $r->FlightSegment->SegmentNo,
									))
								);
								$idx++;								
							}
						}//end of inner foreach			
					}//end for		
				}
			}//end else
		}			
	
		//-------end of processing data economy-------------------------------------//
										
		
		//get flight and fare info (executive)--------------------------------------//
		if(isset(json_decode($res_exe)->AvailabilityResult)){
			$data_exe = json_decode($res_exe)->AvailabilityResult->Depart->AvailabilityInformation->Option;
			$fare_exe = json_decode($res_exe)->AvailabilityResult->FareInfomation->FareInfo;
			
			if(is_array($data_exe)){
				foreach ($data_exe as $s) {
					if(is_array($s->FlightSegment)){ //check whether the flight is undirect							
							//define return variable for undirect route
							$final_data_exe[$idx] = array(
								'company' => 'Garuda',
								't_depart' => $s->FlightSegment[0]->DepartureTime,
								't_transit_arrive' => $s->FlightSegment[0]->ArrivalTime,
								't_transit_depart' => $s->FlightSegment[1]->DepartureTime,
								't_arrive' => $s->FlightSegment[1]->ArrivalTime,
								'type' => $flight_type,							
								'class' => $fare_exe->FareClass." (Executive)",
								'price' => $fare_exe->PublishFare,
								'route' => $s->FlightSegment[0]->DepartureAirport.",".$s->FlightSegment[0]->ArrivalAirport.",".$s->FlightSegment[1]->ArrivalAirport,
								'meta_data' => json_encode(array(
									'flight_number' => $s->FlightSegment[0]->FlightNumber,
									'flight_number_transit' => $s->FlightSegment[1]->FlightNumber,
									'passenger' => $this->_opt->passengers,
									'fare_code' => $fare_exe->FareBasisCode,
									'segment_no' => $s->FlightSegment[0]->SegmentNo,
								))							
							);												
					}else{	
						//define return variable					
						$final_data_exe[$idx] = array(								
							'company' => 'Garuda',
							't_depart' => $s->FlightSegment->DepartureTime,
							't_transit_arrive' => '',
							't_transit_depart' => '',
							't_arrive' => $s->FlightSegment->ArrivalTime,
							'type' => $flight_type,							
							'class' => $fare_exe->FareClass." (Executive)",
							'price' => $fare_exe->PublishFare,
							'route' => $s->FlightSegment->DepartureAirport.",".$s->FlightSegment->ArrivalAirport,
							'meta_data' => json_encode(array(
								'flight_number' => $s->FlightSegment->FlightNumber,
								'flight_number_transit' => '',
								'passenger' => $this->_opt->passengers,
								'fare_code' => $fare_exe->FareBasisCode,
								'segment_no' => $s->FlightSegment->SegmentNo,
							))
						);
					}				
					$idx++;
				}//end foreach	
			}else{ //if only one flight available
				$s= $data_exe;
				if(is_array($s->FlightSegment)){ //check whether the flight is undirect							
						//define return variable for undirect route
						$final_data_exe[$idx] = array(
							'company' => 'Garuda',
							't_depart' => $s->FlightSegment[0]->DepartureTime,
							't_transit_arrive' => $s->FlightSegment[0]->ArrivalTime,
							't_transit_depart' => $s->FlightSegment[1]->DepartureTime,
							't_arrive' => $s->FlightSegment[1]->ArrivalTime,
							'type' => $flight_type,							
							'class' => $fare_exe->FareClass." (Executive)",
							'price' => $fare_exe->PublishFare,
							'route' => $s->FlightSegment[0]->DepartureAirport.",".$s->FlightSegment[0]->ArrivalAirport.",".$s->FlightSegment[1]->ArrivalAirport,
							'meta_data' => json_encode(array(
								'flight_number' => $s->FlightSegment[0]->FlightNumber,
								'flight_number_transit' => $s->FlightSegment[1]->FlightNumber,
								'passenger' => $this->_opt->passengers,
								'fare_code' => $fare_exe->FareBasisCode,
								'segment_no' => $s->FlightSegment[0]->SegmentNo,
							))							
						);												
				}else{	
					//define return variable					
					$final_data_exe[$idx] = array(								
						'company' => 'Garuda',
						't_depart' => $s->FlightSegment->DepartureTime,
						't_transit_arrive' => '',
						't_transit_depart' => '',
						't_arrive' => $s->FlightSegment->ArrivalTime,
						'type' => $flight_type,							
						'class' => $fare_exe->FareClass." (Executive)",
						'price' => $fare_exe->PublishFare,
						'route' => $s->FlightSegment->DepartureAirport.",".$s->FlightSegment->ArrivalAirport,
						'meta_data' => json_encode(array(
							'flight_number' => $s->FlightSegment->FlightNumber,
							'flight_number_transit' => '',
							'passenger' => $this->_opt->passengers,
							'fare_code' => $fare_exe->FareBasisCode,
							'segment_no' => $s->FlightSegment->SegmentNo,
						))
					);
				}				
				$idx++;	
			}//end else
		}// end outer if
				
		//-----------------end of processing data executive-------------------------------------//
						
		$final_data = array_merge($final_data_eco,$final_data_exe);
		
		//print_r($final_data);			
		echo "<h2>Full Res Executive</h2>";
		echo $res_exe;				
		echo "<h2>Full Res Economy</h2>";
		echo $res_eco;
		
		return $final_data;		
	}
	
	function getUpperLimit($class_avail,$fare_list){						
		if(is_array($class_avail)){				
			switch ($class_avail[1]) {
				case $fare_list[1]:
					$upper_limit = 1;
					break;
				case $fare_list[2]:
					$upper_limit = 2;
					break;
				case $fare_list[3]:
					$upper_limit = 3;
					break;
				case $fare_list[4]:
					$upper_limit = 4;
					break;
				case $fare_list[5]:
					$upper_limit = 5;
					break;
				case $fare_list[6]:
					$upper_limit = 6;
					break;
				case $fare_list[7]:
					$upper_limit = 7;
					break;
			}			
		}else{
			if($class_avail == 'Y'){
				$upper_limit = count($fare_list)-1;	
			}			
		}	
		return $upper_limit;
	}
	
	function index(){
		echo 'index';
	}
	
	

}