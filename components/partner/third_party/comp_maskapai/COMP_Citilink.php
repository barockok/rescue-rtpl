<? 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Citilink extends Comp_maskapai_base {

	var $_opt;

	function __construct(){
		parent::__construct();
		$this->_cookies_file = realpath('./cookies/citilink_airline.txt');
		$this->login_url = 'https://www.citilink.co.id/giaidb2b/agent.aspx';
		$this->_refer_url = 'https://www.citilink.co.id/giaidb2b/agent.aspx';
		$this->src_url = 'https://www.citilink.co.id/giaidb2b/agent.aspx';
		
		//define variable
		$this->_opt = new stdClass();
		$this->_opt->date_depart =  '2011-11-19';
		$this->_opt->date_return =  '2011-11-20';
		$this->_opt->passengers = 5;
		$this->_opt->route_from = 'CGK';
		$this->_opt->route_to = 'DPS';
		
	}
	
	function login(){
		//$this->dom->find('input[name=__VIEWSTATEKEY]', 0)->getAttribute('value');	
		$post_data = array(		
			'__EVENTTARGET' => 'ctrPageHeader:lnkAgentAvailability',
			'__EVENTARGUMENT' => '',
			'__VIEWSTATEKEY' => $this->dom->find('input[name=__VIEWSTATEKEY]', 0)->getAttribute('value'),			
			'__VIEWSTATE' => '',
			'ctrLogonBase:tboAgentLogon' => 'mandiri4',
			'ctrLogonBase:tboAgentPassword' => 'booking',
			'ctrLogonBase:tboAgentAgencyCode' => 'cgkprimaag01',
			'ctrLogonBase:btmLogonTravelAgent.x' => '0',
			'ctrLogonBase:btmLogonTravelAgent.y' => '0'
		);
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
			
			//change depart date
			$this->_opt->date_depart = $this->_opt->date_return;
			
			print_r($return_flight = $this->src('return'));
		}else{
			print_r($depart_flight = $this->src('depart'));
		}	
		$this->addResFlight($return_flight);
		$this->addResFlight($depart_flight);
		
	}
	
	function src($flight_type){		
		//$this->login();
		/*$post_data = array(		
			'__EVENTTARGET' => 'ctrPageHeader:lnkAgencySalesReport',
			'__EVENTARGUMENT' => '',
			'__VIEWSTATEKEY' => $this->dom->find('input[name=__VIEWSTATEKEY]', 0)->getAttribute('value'),
			'__VIEWSTATE' => '',
			'ctrAgentBase:ctrAgentHome:ctrAgentFlightAvailability:ctrFlightSearchBase:FlightType' => 'rdoReturn',
			'ctrAgentBase:ctrAgentHome:ctrAgentFlightAvailability:ctrFlightSearchBase:cmbOrigin' => 'CGK',
			'ctrAgentBase:ctrAgentHome:ctrAgentFlightAvailability:ctrFlightSearchBase:cmbDestination' => 'DPS',
			'ctrAgentBase:ctrAgentHome:ctrAgentFlightAvailability:ctrFlightSearchBase:ctlDepatureDate:cmbMonthYear' => '201111',
			'ctrAgentBase:ctrAgentHome:ctrAgentFlightAvailability:ctrFlightSearchBase:ctlDepatureDate:cmbDay' => '12',
			'ctrAgentBase:ctrAgentHome:ctrAgentFlightAvailability:ctrFlightSearchBase:ctlReturnDate:cmbMonthYear' => '201111',
			'ctrAgentBase:ctrAgentHome:ctrAgentFlightAvailability:ctrFlightSearchBase:ctlReturnDate:cmbDay' => '15',
			'ctrAgentBase:ctrAgentHome:ctrAgentFlightAvailability:ctrFlightSearchBase:ctlPassengerType:cmbAdult'=>'1',
			'ctrAgentBase:ctrAgentHome:ctrAgentFlightAvailability:ctrFlightSearchBase:ctlPassengerType:cmbChild'=>'0',
			'ctrAgentBase:ctrAgentHome:ctrAgentFlightAvailability:ctrFlightSearchBase:ctlPassengerType:cmbInfant'=>'0',
			'ctrAgentBase:ctrAgentHome:ctrAgentFlightAvailability:ctrFlightSearchBase:CmbAvailabilityModes'=>'111',
			'ctrAgentBase:ctrAgentHome:ctrAgentFlightAvailability:ctrFlightSearchBase:btmSearchm.x' => '33',
			'ctrAgentBase:ctrAgentHome:ctrAgentFlightAvailability:ctrFlightSearchBase:btmSearchm.y' => '5'
		);*/
		
		$file_arr = array(
			'./components/partner/third_party/comp_maskapai/citilink_html/ct_1/Citilink.htm',
			'./components/partner/third_party/comp_maskapai/citilink_html/ct_2/Citilink.htm',
			'./components/partner/third_party/comp_maskapai/citilink_html/ct_3/Citilink.htm',
			'./components/partner/third_party/comp_maskapai/citilink_html/ct_4/Citilink.htm'
		);				
		
		shuffle($file_arr);		
						
		$dom = file_get_html($file_arr[1]);
		if (!$html = $dom->find('table[id=FlightAvailability0] tbody',0)) return array(); 
		
		$total_flight = count($dom->find('table[id=FlightAvailability0] tbody tr')); //get total flight by count rows in table
		
		$final_data = array();					
		//parse data from table
		for ($i= 1; $i < $total_flight; $i++) {
		
			if($dom->find('table[id=FlightAvailability0] tbody tr',$i)->find('td',0)->plaintext != ''){
				$temp_flight_number = $dom->find('table[id=FlightAvailability0] tbody tr',$i)->find('td',0)->plaintext;
				$temp_departure_time = $dom->find('table[id=FlightAvailability0] tbody tr',$i)->find('td',3)->plaintext;
				$temp_arrival_time = $dom->find('table[id=FlightAvailability0] tbody tr',$i)->find('td',4)->plaintext;
				$temp_departure_date = $dom->find('table[id=FlightAvailability0] tbody tr',$i)->find('td',2)->plaintext;				
			}
			
			$flight_number = $temp_flight_number;
			$departure_time = $temp_departure_time;
			$arrival_time = $temp_arrival_time;
			$departure_date = $temp_departure_date;
			$arrival_date = $temp_departure_date;
			
			$price = preg_replace(array('/IDR/','/\s/','/,/'),'',$dom->find('table[id=FlightAvailability0] tbody tr',$i)->find('td',8)->plaintext); // remove all uneeded character from price
			$price_appendix = substr($price,-3); // get .00 appendix in price
			$price = str_replace($price_appendix,'',$price); //remove appendix from price
			
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
				'route' => $this->_opt->route_from.",".$this->_opt->route_to,
				'meta_data' => json_encode(array(
					'seat_available' => $dom->find('table[id=FlightAvailability0] tbody tr',$i)->find('td',7)->plaintext,
					'flight_number' => $flight_number,
					'flight_number_transit' => '',
					'passenger' => $this->_opt->passengers
				))
			);			
			
			if($final_data[$i-1]['meta_data']['seat_available']=='C') unset($final_data[$i-1]);
		}			
		//print_r($final_data);
		return $final_data;										
										
	}
	function index(){
		echo 'index';
	}
	// API REQUIREMNET 
	public function doSearch()
	{
		$this->addResult($this->src_flight());
	}
	
	

}