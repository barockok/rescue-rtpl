<? 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Citilink extends Comp_maskapai_base {


	function __construct(){
		parent::__construct();
		$this->_cookies_file = dirname(__FILE__).'/cookies/citilink.txt';
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
	
	public function doSearch($opt=null){
		$this->_opt->date_depart =  '2011-12-16';
		$this->_opt->date_return =  NULL;
		$this->_opt->passengers = 2;
		$this->_opt->route_from = 'BDJ';
		$this->_opt->route_to = 'SUB';
		$this->_opt->id = null;
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
			'__EVENTARGUMENT' => '',
			'__VIEWSTATEKEY' => $vKey,
			'__VIEWSTATE' => '',
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
			//'postfields' 		=> ,
			'postfields'		=> $post_fields,
			'post'				=> true,
			'referer'			=> $this->_url,
			'useragent'			=> 'User-Agent:Mozilla/5.0 (Windows NT 6.1) AppleWebKit/535.2 (KHTML, like Gecko) Chrome/15.0.874.121 Safari/535.2'
		);
		
		$this->_ci->my_curl->setup($conf);
		$dom = str_get_html($this->_ci->my_curl->exc());									

		//echo $this->vKey_book = $dom->find('input[name=__VIEWSTATEKEY]', 0)->getAttribute('value');
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
						
			//define return variable
			$final_data[$i-1]=array(
				'company' => 'CITILINK',
				't_depart' => $this->_opt->date_depart." ".$departure_time,	//depart from origin location
				't_transit_arrive' => '', //arrive in transit airport
				't_transit_depart' => '', //depart from transit airport
				't_arrive' => $this->_opt->date_depart." ".$arrival_time,
				'type' => $flight_type, //depart or return
				'class' => str_replace(' ','',$dom->find('table[id=FlightAvailability0] tbody tr',$i)->find('td',6)->plaintext),
				'price' => $price,
				'flight_no' => $flight_number,
				'log_id' => $this->_opt->id,
				'route' => $this->_opt->route_from.",".$this->_opt->route_to,
				'meta_data' => json_encode(array(
					'seat_available' => $seat_available,					
					'flight_number_transit' => '',
					'passenger' => $this->_opt->passengers
				))
			);
		}			
		//print_r($final_data);
		return $final_data;																				
	}
	
	function getFlightDetail($vKey=null){
		$this->login();					
		$postfields = '__EVENTTARGET=&____VIEWSTATEKEY='.$vKey.'&EVENTARGUMENT=&__VIEWSTATE=&ctrAgentBase_ctrAgentHome_ctrAgentFlightAvailability_ctrFlightAvailabilityBase_ctrAvailabilityGridOutward_GrdAvailabilityFights_FlightSelect=ef2a5d19-d1cc-11df-8f38-18a905e04790%7Ce7a1e1ae-5eab-11df-8e34-18a905e04790';
		
		
		$conf = array(
			'url' 				=> $this->_url,
			'post' 				=> true,
			'postfields' 		=> $postfields,
			'timeout'			=> 150,
			'header'			=> 1,
			'followlocation'	=> true,
			'maxredirs'			=> 10,
			'cookiefile'		=> $this->_cookies_file,
			'cookiejar'			=> $this->_cookies_file,
			'returntransfer'	=> 1,
			'post'				=> true,
			'referer'			=> $this->_url,
			'ssl_verifyhost'	=> 0,
			'SSL_VERIFYPEER'	=> 0,
		);
		
		$this->_ci->my_curl->setup($conf);
		$res  = $this->_ci->my_curl->exc();
		$info = $this->_ci->my_curl->res_info();				
		if($info->http_code == 302) return $this->topage($info->url);
		//print_r($res);
		return $res;
	}
	
	
	function book_flight(){
		
	}
	
	
	
	
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
