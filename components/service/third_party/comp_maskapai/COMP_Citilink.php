<? 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Citilink extends Comp_maskapai_base {


	function __construct(){
		parent::__construct();
		$this->_cookies_file = './components/service/third_party/comp_maskapai/cookies/citilink.txt';
		$this->_url = 'https://www.citilink.co.id/giaidb2b/agent.aspx';
		$this->agency_code = 'cgkprimaag01';
		$this->username = 'mandiri4';
		$this->password = 'booking';
		$this->insurance = 7000;
		$this->_ci->load->library('my_curl');
	}
	
	function topage($url , $return = true){
		$conf = array(
				'url' 				=> $url,
				'cookiejar' 		=> $this->_cookies_file,
				'cookiefile' 		=> $this->_cookies_file,
				'header'			=> 0,
				'nobody'			=> false,
				'returntransfer' 	=> 1,
				'SSL_VERIFYPEER'	=> 0,
				'ssl_verifyhost'	=> 0,
		);
		$this->_ci->my_curl->setup($conf);
		$exc = $this->_ci->my_curl->exc();
		if($return == true ) return $this->_ci->my_curl;
		return $exc;
	}
	
	
	function login(){
		$start = str_get_html($this->topage($this->_url,false));
		echo $start;
		$vKey = $start->find('input[name=__VIEWSTATEKEY]', 0)->getAttribute('value');
				
		$post_data = array(		
			'__EVENTTARGET' => '',
			'__EVENTARGUMENT' => '',
			'__VIEWSTATEKEY' => $vKey,
			'__VIEWSTATE' => '',
			'ctrLogonBase:tboAgentLogon' => $this->username,
			'ctrLogonBase:tboAgentPassword' => $this->password,
			'ctrLogonBase:tboAgentAgencyCode' => $this->agency_code,
			'ctrLogonBase:btmLogonTravelAgent.x' => '38',
			'ctrLogonBase:btmLogonTravelAgent.y' => '7'
		);		
		
		$conf = array(
			'url' 				=> $this->_url,
			'cookiejar' 		=> $this->_cookies_file,
			'cookiefile' 		=> $this->_cookies_file,
			'timeout'			=> 150,
			'header'			=> 0,
			'nobody'			=> false,
			'returntransfer' 	=> 1,
			'maxredirs'			=> 10,
			'followlocation'	=> 1,
			'SSL_VERIFYPEER'	=> 0,
			'ssl_verifyhost'	=> 0,
			'postfields' 		=> '__EVENTTARGET=&__EVENTARGUMENT=&__VIEWSTATEKEY='.$vKey.'&__VIEWSTATE=&ctrLogonBase%3AtboAgentLogon=mandiri4&ctrLogonBase%3AtboAgentPassword=booking&ctrLogonBase%3AtboAgentAgencyCode=cgkprimaag01&ctrLogonBase%3AbtmLogonTravelAgent.x=38&ctrLogonBase%3AbtmLogonTravelAgent.y=7',
			//'postfields'		=> http_build_query($post_data),
			'post'				=> true,
			'referer'			=> $this->_url,
			'useragent'			=> 'User-Agent:Mozilla/5.0 (Windows NT 6.1) AppleWebKit/535.2 (KHTML, like Gecko) Chrome/15.0.874.121 Safari/535.2'
		);
		
		$this->_ci->my_curl->setup($conf);
		return $exc = str_get_html($this->_ci->my_curl->exc());
		
	}
	
	
	function toSearchForm(){
		$vKey = $this->login()->find('input[name=__VIEWSTATEKEY]', 0)->getAttribute('value');
		
		$conf = array(
			'url' 				=> $this->_url,
			'cookiejar' 		=> $this->_cookies_file,
			'cookiefile' 		=> $this->_cookies_file,
			'timeout'			=> 150,
			'header'			=> 0,
			'nobody'			=> false,
			'returntransfer' 	=> 1,
			'maxredirs'			=> 10,
			'followlocation'	=> 1,
			'SSL_VERIFYPEER'	=> 0,
			'ssl_verifyhost'	=> 0,
			'postfields' 		=> '__EVENTTARGET=ctrPageHeader%3AlnkAgentAvailability&__EVENTARGUMENT=&__VIEWSTATEKEY='.$vKey.'&__VIEWSTATE=',
			'post'				=> true,
			'referer'			=> $this->_url,
			'useragent'			=> 'User-Agent:Mozilla/5.0 (Windows NT 6.1) AppleWebKit/535.2 (KHTML, like Gecko) Chrome/15.0.874.121 Safari/535.2'
		);
		
		$this->_ci->my_curl->setup($conf);
		return $exc = str_get_html($this->_ci->my_curl->exc());
	}
	
	public function doSearch($opt=array()){		
		$this->_opt->date_depart =  '2012-02-10';
		$this->_opt->date_return =  NULL;
		$this->_opt->passengers = 2;
		$this->_opt->route_from = 'BDJ';
		$this->_opt->route_to = 'SUB';
		$this->_opt->id = 1;
		$this->_opt->max_fare = 5;
		//$this->addResult($this->cleanObject('Citilink/src_flight', array()));
		//print_r($this->src_flight());
		foreach($opt as $key => $val) $this->_opt->$key = $val;
		return $this->src_flight();
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
			
			//change date
			$this->_opt->date_depart = $this->_opt->date_return;
			
			$return_flight = $this->src('return');
			
		}else{			
			$depart_flight = $this->src('depart');
		}
		
		/*echo "<h3>Depart</h3>";
		print_r($depart_flight);
		echo "<h3>Return</h3>";
		print_r($return_flight);*/
		
		return array_merge($depart_flight, $return_flight);
	}

			
	function src($flight_type){			
		$date_withoutdash = str_replace('-','',$this->_opt->date_depart);
		$cmbMonthYear = substr($date_withoutdash,0,6);
		$cmbDay = substr($date_withoutdash,-2,2);
				
		$vKey = $this->toSearchForm()->find('input[name=__VIEWSTATEKEY]', 0)->getAttribute('value');
					
		/*$post_data = array(		
			'__EVENTTARGET' => 'ctrPageHeader:lnkAgencySalesReport',
			'__EVENTARGUMENT' => null,
			'__VIEWSTATEKEY' => $vKey,
			'__VIEWSTATE' => null,
			'ctrAgentBase:ctrAgentHome:ctrAgentFlightAvailability:ctrFlightSearchBase:FlightType' => 'rdoReturn',
			'ctrAgentBase:ctrAgentHome:ctrAgentFlightAvailability:ctrFlightSearchBase:cmbOrigin' => $this->_opt->route_from,
			'ctrAgentBase:ctrAgentHome:ctrAgentFlightAvailability:ctrFlightSearchBase:cmbDestination' => $this->_opt->route_to,
			'ctrAgentBase:ctrAgentHome:ctrAgentFlightAvailability:ctrFlightSearchBase:ctlDepatureDate:cmbMonthYear' => $cmbMonthYear,
			'ctrAgentBase:ctrAgentHome:ctrAgentFlightAvailability:ctrFlightSearchBase:ctlDepatureDate:cmbDay' => $cmbDay,
			'ctrAgentBase:ctrAgentHome:ctrAgentFlightAvailability:ctrFlightSearchBase:ctlReturnDate:cmbMonthYear' => '',
			'ctrAgentBase:ctrAgentHome:ctrAgentFlightAvailability:ctrFlightSearchBase:ctlReturnDate:cmbDay' => '',
			'ctrAgentBase:ctrAgentHome:ctrAgentFlightAvailability:ctrFlightSearchBase:ctlPassengerType:cmbAdult'=> $this->_opt->passengers,
			'ctrAgentBase:ctrAgentHome:ctrAgentFlightAvailability:ctrFlightSearchBase:ctlPassengerType:cmbChild'=> '0',
			'ctrAgentBase:ctrAgentHome:ctrAgentFlightAvailability:ctrFlightSearchBase:ctlPassengerType:cmbInfant'=>'0',
			'ctrAgentBase:ctrAgentHome:ctrAgentFlightAvailability:ctrFlightSearchBase:CmbAvailabilityModes'=>'111',
			'ctrAgentBase:ctrAgentHome:ctrAgentFlightAvailability:ctrFlightSearchBase:btmSearchm.x' => '33',
			'ctrAgentBase:ctrAgentHome:ctrAgentFlightAvailability:ctrFlightSearchBase:btmSearchm.y' => '5'
				);*/

		$post_fields = '__EVENTTARGET=&__EVENTARGUMENT=&__VIEWSTATEKEY='.$vKey.'&__VIEWSTATE=&ctrAgentBase%3ActrAgentHome%3ActrAgentFlightAvailability%3ActrFlightSearchBase%3AFlightType=rdoOneway&ctrAgentBase%3ActrAgentHome%3ActrAgentFlightAvailability%3ActrFlightSearchBase%3AcmbOrigin='.$this->_opt->route_from.'&ctrAgentBase%3ActrAgentHome%3ActrAgentFlightAvailability%3ActrFlightSearchBase%3AcmbDestination='.$this->_opt->route_to.'&ctrAgentBase%3ActrAgentHome%3ActrAgentFlightAvailability%3ActrFlightSearchBase%3ActlDepatureDate%3AcmbMonthYear='.$cmbMonthYear.'&ctrAgentBase%3ActrAgentHome%3ActrAgentFlightAvailability%3ActrFlightSearchBase%3ActlDepatureDate%3AcmbDay='.$cmbDay.'&ctrAgentBase%3ActrAgentHome%3ActrAgentFlightAvailability%3ActrFlightSearchBase%3ActlReturnDate%3AcmbMonthYear='.$cmbMonthYear.'&ctrAgentBase%3ActrAgentHome%3ActrAgentFlightAvailability%3ActrFlightSearchBase%3ActlReturnDate%3AcmbDay'.$cmbDay.'=&ctrAgentBase%3ActrAgentHome%3ActrAgentFlightAvailability%3ActrFlightSearchBase%3ActlPassengerType%3AcmbAdult='.$this->_opt->passengers.'&ctrAgentBase%3ActrAgentHome%3ActrAgentFlightAvailability%3ActrFlightSearchBase%3ActlPassengerType%3AcmbChild=0&ctrAgentBase%3ActrAgentHome%3ActrAgentFlightAvailability%3ActrFlightSearchBase%3ActlPassengerType%3AcmbInfant=0&ctrAgentBase%3ActrAgentHome%3ActrAgentFlightAvailability%3ActrFlightSearchBase%3ACmbAvailabilityModes=111&ctrAgentBase%3ActrAgentHome%3ActrAgentFlightAvailability%3ActrFlightSearchBase%3AbtmSearchm.x=40&ctrAgentBase%3ActrAgentHome%3ActrAgentFlightAvailability%3ActrFlightSearchBase%3AbtmSearchm.y=12';
					
				
		$conf = array(
			'url' 				=> $this->_url,
			'cookiejar' 		=> $this->_cookies_file,
			'cookiefile' 		=> $this->_cookies_file,
			'timeout'			=> 150,
			'header'			=> 0,
			'nobody'			=> false,
			'returntransfer' 	=> 1,
			'maxredirs'			=> 10,
			'followlocation'	=> 1,
			'SSL_VERIFYPEER'	=> 0,
			'ssl_verifyhost'	=> 0,
			//'postfields' 		=> http_build_query($post_data, NULL, '&'),
			'postfields'		=> $post_fields,
			'post'				=> true,
			'referer'			=> $this->_url,
			'useragent'			=> 'User-Agent:Mozilla/5.0 (Windows NT 6.1) AppleWebKit/535.2 (KHTML, like Gecko) Chrome/15.0.874.121 Safari/535.2'
		);
		
		$this->_ci->my_curl->setup($conf);
		$dom = str_get_html($this->_ci->my_curl->exc());									

		$this->vkey_prebook = $dom->find('input[name=__VIEWSTATEKEY]', 0)->getAttribute('value');
		//echo $this->getFlightDetail($this->vKey_book);
		
				
		if (!$html = $dom->find('table[id=FlightAvailability0] tbody',0)) return array(); 		
		$total_flight = count($dom->find('table[id=FlightAvailability0] tbody tr')); //get total flight by count rows in table		
		$final_data = array(); //init
							
		//parse data from table
		for ($i= 1; $i < $total_flight; $i++) {		
			if($dom->find('table[id=FlightAvailability0] tbody tr',$i)->find('td',0)->plaintext != ''){
				$temp_flight_number = $dom->find('table[id=FlightAvailability0] tbody tr',$i)->find('td',0)->plaintext;
				$temp_departure_time = $dom->find('table[id=FlightAvailability0] tbody tr',$i)->find('td',3)->plaintext;
				$temp_arrival_time = $dom->find('table[id=FlightAvailability0] tbody tr',$i)->find('td',4)->plaintext;
				$temp_departure_date = $dom->find('table[id=FlightAvailability0] tbody tr',$i)->find('td',2)->plaintext;	
			}
			
			$seat_available = $dom->find('table[id=FlightAvailability0] tbody tr',$i)->find('td',7)->plaintext;
			$radio_btn = $dom->find('table[id=FlightAvailability0] tbody tr',$i)->find('td',9);
			
			//echo $dom->find('table[id=FlightAvailability0] tbody tr',$i)->find('td',9)->getAttribute('value')->plaintext;
			if($seat_available=='C' || $radio_btn->plaintext == 'Call') continue; //SKIP IF NO SEAT AVAILABLE
			
			$val =  $radio_btn->find('input',0)->getAttribute('value');
			
			
			$flight_number = $temp_flight_number;
			$departure_time = $temp_departure_time;
			$arrival_time = $temp_arrival_time;
			$departure_date = $temp_departure_date;
			$arrival_date = $temp_departure_date;
			
			//PARSE PRICE
			$dirty_price = preg_replace(array('/IDR/','/\s/','/,/'),'',$dom->find('table[id=FlightAvailability0] tbody tr',$i)->find('td',8)->plaintext); // remove all uneeded character from price
			$price_appendix = substr($dirty_price,-3); // get .00 appendix in price
			$raw_price = str_replace($price_appendix,'',$dirty_price); //remove appendix from price
			$price = ($raw_price*$this->_opt->passengers) + ($this->insurance*$this->_opt->passengers) + (($raw_price*$this->_opt->passengers)*0.1);
			//
			
			$t_depart = date('Y-m-d H:i:s',strtotime($this->_opt->date_depart." ".$departure_time));
			$t_arrive = date('Y-m-d H:i:s',strtotime($this->_opt->date_depart." ".$arrival_time));
			
									
			//define return variable
			$final_data[$i-1]=array(
				'company' => 'CITILINK',
				't_depart' => $t_depart,	//depart from origin location
				't_transit_arrive' => '', //arrive in transit airport
				't_transit_depart' => '', //depart from transit airport
				't_arrive' => $t_arrive,
				'type' => $flight_type, //depart or return
				'class' => str_replace(' ','',$dom->find('table[id=FlightAvailability0] tbody tr',$i)->find('td',6)->plaintext),
				'price' => $price,
				'flight_no' => $flight_number,
				'log_id' => $this->_opt->id,
				//'log_id' => '',
				'route' => $this->_opt->route_from.",".$this->_opt->route_to,
				/*
				'log' => array(
					'id' => '',
					'date_depart' => $t_depart,
					'date_return' => '',
					'route_from' => $this->_opt->route_from,
					'route_to' => $this->_opt->route_to,
					'passengers' => $this->_opt->passengers,
					'comp_include' => json_encode(array("Sriwijaya","Garuda","Merpati","Batavia","Citilink")),
					'c_time' => '',
					'max_fare' => $this->_opt->max_fare,
					'actor' => 'CUS',
				),
				*/
				'meta_data' => json_encode(array(
					'seat_available' => $seat_available,					
					'flight_number_transit' => '',
					'passenger' => $this->_opt->passengers,
					'val' => $val, //get input value					
				))
			);
		}			
		//print_r($final_data);
		return $final_data;																				
	}	
	//END OF SEARCH FLIGHT FUNCTION
	
	function prebook($flight_detail=array()){
		
		$flightID = json_decode($flight_detail['meta_data'])->val;
						
		$this->doSearch();
		//echo $this->vkeyBook;	
		$postfields = '__EVENTTARGET=&__EVENTARGUMENT=&__VIEWSTATEKEY='.$this->vkey_prebook.'&__VIEWSTATE=&ctrAgentBase_ctrAgentHome_ctrAgentFlightAvailability_ctrFlightAvailabilityBase_ctrAvailabilityGridOutward_GrdAvailabilityFights_FlightSelect='.$flightID.'&ctrAgentBase%3ActrAgentHome%3ActrAgentFlightAvailability%3ActrFlightAvailabilityBase%3AbtmSubmitFlightSelect.x=33&ctrAgentBase%3ActrAgentHome%3ActrAgentFlightAvailability%3ActrFlightAvailabilityBase%3AbtmSubmitFlightSelect.y=17';
				
		$conf = array(
			'url' 				=> $this->_url,
			'cookiejar' 		=> $this->_cookies_file,
			'cookiefile' 		=> $this->_cookies_file,
			'timeout'			=> 150,
			'header'			=> 0,
			'nobody'			=> false,
			'returntransfer' 	=> 1,
			'maxredirs'			=> 10,
			'followlocation'	=> 1,
			'SSL_VERIFYPEER'	=> 0,
			'ssl_verifyhost'	=> 0,
			'postfields' 		=> $postfields,			
			'post'				=> true,
			'referer'			=> $this->_url,
			'useragent'			=> 'User-Agent:Mozilla/5.0 (Windows NT 6.1) AppleWebKit/535.2 (KHTML, like Gecko) Chrome/15.0.874.121 Safari/535.2'
		);
		
		$this->_ci->my_curl->setup($conf);
		$res  = $this->_ci->my_curl->exc();
		//$info = $this->_ci->my_curl->res_info();				
		/*if($info->http_code == 302){			
			return $this->topage($info->url);
		} */
		//print_r($res);
		
		$dom = str_get_html($res);
		
		//check whether the flight is still available
		
		if($dom->find("div[class='MsgLink']")!=null){
			return FALSE;
		}
		
		//PARSE PRICE								
		$dirty_price = preg_replace(array('/\s/','/,/'),'',$dom->find('span[id=ctrBooking_ctrItineraryBase_ctrQuoteGrid_grdQuote__ctl4_Label10]',0)->plaintext); // remove all uneeded character from price
		$price_appendix = substr($dirty_price,-3); // get .00 appendix in price
		$prebook_price = str_replace($price_appendix,'',$dirty_price); //remove appendix from price				
		
		$vkey_book = $dom->find('input[name=__VIEWSTATEKEY]', 0)->getAttribute('value');
				
		if($prebook_price<=$flight_detail['price']){
			return array('1',$vkey_book);
		}else{
			return array($prebook_price,$vkey_book);
		}						
	}
	//END OF PREBOOK
	
	
	function testBook(){
		
		$customer_data = array(
			'id' => '55',
			'f_name' => 'Zidni',
			'l_name' => 'Mubarock',
			'email' => 'lavalyst@gmail.com',
			'password' => 'aca9fd21ff5e08cf88a3929ef5c4f346',
			'role_id' => 1,
			'c_time' => date('Y-m-d H:i:s',strtotime('2011-12-11 21:04:04')),
			'm_time' => '',
			'status' => 'active',
			'actv_key' => '',
			'user_detail' => array(
				'id' => 10,
				'user_id' => 26,
				'phone' => '02518329245',
				'mobile' => '085697586581',
				'address' => 'Jl. Pajajaran Bogor',
				'gender' => 'M',
			)						
		);
		
		$passengers_data = array(
			0 => array(
				'name' => 'Zidni Mubarock',
				'no_id' => '3426238910220',
				'title' => 'Mr',
			),
			1 => array(
				'name' => 'Zidni Mubarok',
				'no_id' => '34231625612399',
				'title' => 'Mr'
			),
		);
				
		$flight_detail = array(
			'company' => 'CITILINK',
            't_depart' => date('Y-m-d H:i:s',strtotime('2012-03-16 09:45:00')),
	        't_transit_arrive' => '',
	        't_transit_depart' => '',
	        't_arrive' => date('Y-m-d H:i:s',strtotime('2012-03-16 09:50:00')),
            'type' => 'depart',
            //'class' => 'Q',
            'class' => 'O',
            'price' => '546400',
            'flight_no' => 'GA 051',
            'id' => '',
            'log_id' => '1',
            'route' => 'BDJ,SUB',
            'meta_data' => '{"seat_available":"4","flight_number_transit":"","passenger":2,"val":"5df3f694-68ec-4c00-9dea-b9f2e807a6e9|61887d48-5eac-11df-8e34-18a905e04790"}'
				);				
				
		return $this->doBook($flight_detail,$passengers_data,$customer_data);
	}
	
		
	function doBook($flight_detail,$passengers_data,$customer_data){
				
		//do prebook, check for price changes
		$prebook_res = $this->prebook($flight_detail);
		
		if($prebook_res==false){ //if flight no more valid
			return false;
		}
		
		$final_price = $prebook_res[0];
		$vkey_book = $prebook_res[1];
				
		if($final_price==1){ //if there is no price changes
			$final_price = $flight_detail['price'];
		}
								
		//init variable
		$contactName = $customer_data['f_name']." ".$customer_data['l_name'];
		$phoneHome = $customer_data['user_detail']['phone'];
		$phoneMobile = $customer_data['user_detail']['mobile'];
		$email = $customer_data['email'];
		
		$post_data = array(					
			'PaymentTabs_Value' => 'NoPayment',
			'__EVENTTARGET'=> '', 
			'__EVENTARGUMENT'=> '',
			'__VIEWSTATEKEY' => $vkey_book,
			'__VIEWSTATE' => '',																				
			'ctrBooking:ctrPassengerInfosBase:ctlPassengerContactInfo:contact_name' => $contactName,
			'ctrBooking:ctrPassengerInfosBase:ctlPassengerContactInfo:phone_mobile' => $phoneMobile,
			'ctrBooking:ctrPassengerInfosBase:ctlPassengerContactInfo:phone_home' => $phoneHome,		
			'ctrBooking:ctrPassengerInfosBase:ctlPassengerContactInfo:contact_email' => $email,
			'ctrBooking:ctrPassengerInfosBase:ctlPassengerContactInfo:language_rcd' => 'ID',				
			'ctrBooking:ctrPassengerInfosBase:btmSubmitBooking.x' => '8',
			'ctrBooking:ctrPassengerInfosBase:btmSubmitBooking.y' => '5',						
		);
		
		//ADDITIONAL DATA FOR PASSENGER DETAIL
		for ($i=0; $i < $this->_opt->passengers; $i++) {
			
			$pax_name = explode(' ',$passengers_data[$i]['name'],2);
			$pax_title = strtoupper(str_replace('.','',$passengers_data[$i]['title']));
			
			if($pax_title == 'MR'){
				$pax_title = $pax_title.'|M';
			}elseif($pax_title == 'MS' || $pax_title == 'MRS' || $pax_title == 'MISS'){
				$pax_title = $pax_title.'|F';
			}
			
			$pax_fname = $pax_name[0];
			$pax_lname = $pax_name[1];
			$paxID = $passengers_data[$i]['no_id'];
			
			$j = $i+1;
			
			$all_passengers = array(
				'ctrBooking:ctrPassengerInfosBase:ctlPassengerList:PassengerList:_ctl'.$j.':Lastname' => $pax_lname,
				'ctrBooking:ctrPassengerInfosBase:ctlPassengerList:PassengerList:_ctl'.$j.':Firstname' => $pax_fname,
				'ctrBooking:ctrPassengerInfosBase:ctlPassengerList:PassengerList:_ctl'.$j.':PassengerTitel' => $pax_title,		
				'ctrBooking:ctrPassengerInfosBase:ctlPassengerList:PassengerList:_ctl'.$j.':nationality_rcd' => 'ID',
				'ctrBooking:ctrPassengerInfosBase:ctlPassengerList:PassengerList:_ctl'.$j.':passport_number' => $paxID,
			);
			
			$post_data = array_merge($post_data,$all_passengers);			
		}//END FOR
									
		//return $post_data;
		
		$conf = array(
			'url' 				=> $this->_url,
			'cookiejar' 		=> $this->_cookies_file,
			'cookiefile' 		=> $this->_cookies_file,
			'timeout'			=> 150,
			'header'			=> 0,
			'nobody'			=> false,
			'returntransfer' 	=> 1,
			'maxredirs'			=> 10,
			'followlocation'	=> 1,
			'SSL_VERIFYPEER'	=> 0,
			'ssl_verifyhost'	=> 0,
			'postfields' 		=> http_build_query($post_data, NULL, '&'),
			'post'				=> true,
			'referer'			=> $this->_url,
			'useragent'			=> 'User-Agent:Mozilla/5.0 (Windows NT 6.1) AppleWebKit/535.2 (KHTML, like Gecko) Chrome/15.0.874.121 Safari/535.2'
		);
		
		$this->_ci->my_curl->setup($conf);
		$res  = $this->_ci->my_curl->exc();
		$dom = str_get_html($res);
		
		$bookingCode = $dom->find('span[id=ctrBooking_ctrBookingSummery_CtrPassengerContactInfoView_labBookingRef]',0)->plaintext;
		$paymentCode = $dom->find('span[id=ctrBooking_ctrBookingSummery_CtrPassengerContactInfoView_booking_number]',0)->plaintext;
		
		$return_var = array(
			'booking_number' => $bookingCode,
			'fare_id' => $flight_detail['id'],
			'meta_data' => json_encode(array(
				'payment_code'	=>	$paymentCode,
				)),
			'final_price' => (int) $final_price
		);
		
		return $return_var;		
	}
	
		
	//LOGOUT FUNCTION
	function logout(){
		$vKey = $this->login()->find('input[name=__VIEWSTATEKEY]', 0)->getAttribute('value');
		$conf = array(
			'url' 				=> $this->_url,
			'cookiejar' 		=> $this->_cookies_file,
			'cookiefile' 		=> $this->_cookies_file,
			'timeout'			=> 150,
			'header'			=> 0,
			'nobody'			=> false,
			'returntransfer' 	=> 1,
			'maxredirs'			=> 10,
			'followlocation'	=> 1,
			'SSL_VERIFYPEER'	=> 0,
			'ssl_verifyhost'	=> 0,
			//'postfields' 		=> ,
			'postfields'		=> ' __EVENTTARGET=&__EVENTARGUMENT=&__VIEWSTATEKEY=8c2ca19c-93fc-4e79-aa03-04e979db9197&__VIEWSTATE=&ctrPageHeader%3AbtmLogOffAgent.x=44&ctrPageHeader%3AbtmLogOffAgent.y=1',
			'post'				=> true,
			'referer'			=> $this->_url,
			'useragent'			=> 'User-Agent:Mozilla/5.0 (Windows NT 6.1) AppleWebKit/535.2 (KHTML, like Gecko) Chrome/15.0.874.121 Safari/535.2'
		);
		
		$this->_ci->my_curl->setup($conf);
		$this->_ci->my_curl->exc();
	}
	
	
	function index(){
		echo 'index';
	}
	// API REQUIREMNET 
	
	
	function closing(){
		$this->logout();
	}
	
}
