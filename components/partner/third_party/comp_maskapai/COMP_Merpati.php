<?if (! defined('BASEPATH')) exit('No direct script access');
class Merpati extends Comp_maskapai_base {

	private $username = 'jkttravelindo';
	private $password = 'jkt123456';
	private $kodeagen = '13552';
	private $_login_url = 'https://www.merpati.co.id/b2b/WebService/BaseService.asmx/UserLogOn';
	private $_refer = 'https://www.merpati.co.id/b2b/user.aspx';
	private $_search_url = 'https://www.merpati.co.id/b2b/WebService/BaseService.asmx/getFlightAvailabilityForm';
	private $_detail_flight_url = 'https://www.merpati.co.id/b2b/WebService/BaseService.asmx/GetSelectFlight';
	private $_load_step_url = 'https://www.merpati.co.id/b2b/WebService/UtilService.asmx/loadstep3';
	private $_logout_url = 'https://www.merpati.co.id/b2b/WebService/BaseService.asmx/Logout';
	private $_booking_url = 'https://www.merpati.co.id/b2b/WebService/UtilService.asmx/savestep4';
	private $_loadStep4_url = 'https://www.merpati.co.id/b2b/WebService/UtilService.asmx/loadstep4';
	private $_loadStep3_url = 'https://www.merpati.co.id/b2b/WebService/UtilService.asmx/loadstep3';
	private $_loadssr_url = 'https://www.merpati.co.id/b2b/WebService/UtilService.asmx/loadssr';
	private $_loadStep5_url = 'https://www.merpati.co.id/b2b/WebService/UtilService.asmx/loadstep5';
	private $_step5postpaid_url = 'https://www.merpati.co.id/b2b/WebService/UtilService.asmx/loadstep5PostPaid';
	private $_payleter_url = 'https://www.merpati.co.id/b2b/WebService/Payment.asmx/Paylater';
	
	function __construct() {
		parent::__construct();
		$this->_ci->load->library('my_curl');
		$this->_cookies_file = "./components/partner/third_party/comp_maskapai/cookies/merpati_airline.txt";		
		$this->_headerData = array(
			'Content-Type: application/json; charset=UTF-8',
		);
		$this->roundTrip = false;
	}
	
	
		function index() {
			echo 'Merpati';
		}

		function dateAdd($date){
			$length = strlen($date);
			if ($length<2) {
				return '0'.$date;
			}else{
				return $date;
			}
		}

		function login(){
			$post_data = array(
				'userName' => $this->username,
				'PassWord' => $this->password,
				'companyname' => $this->kodeagen,
			);

			$conf = array(
				'httpheader'		=> $this->_headerData,
				'url'				=> $this->_login_url,
				'timeout'			=> 30,
				'header'			=> 0,
				'followlocation'	=> 1,
				'cookiejar'			=> $this->_cookies_file,
				'cookiefile'		=> $this->_cookies_file,
				'returntransfer'	=> 1,
				'post'				=> true,
				'referer'			=> $this->_refer,
				'ssl_verifyhost'	=> 0,
				'postfields'		=> json_encode($post_data),
				'useragent'			=> 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:6.0.2) Gecko/20100101 Firefox/6.0.2',
			);
			$this->_ci->my_curl->setup($conf);
			$this->_ci->my_curl->exc();
			//echo $this->mainPage();
		}

		function logout(){
			$conf = array(
				'httpheader'		=> $this->_headerData,
				'url'				=> $this->_logout_url,
				'timeout'			=> 30,
				'header'			=> 0,
				'followlocation'	=> 1,
				'cookiejar'			=> $this->_cookies_file,
				'cookiefile'		=> $this->_cookies_file,
				'returntransfer'	=> 1,
				'referer'			=> $this->_refer,
				'ssl_verifyhost'	=> 0,
				'useragent'			=> 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:6.0.2) Gecko/20100101 Firefox/6.0.2',
			);
			$this->_ci->my_curl->setup($conf);
			$this->_ci->my_curl->exc();
			
		}

		function mainPage(){
			$conf = array(
				'url'				=> $this->_refer,
				'timeout'			=> 30,
				'header'			=> 0,
				'followlocation'	=> 1,
				'cookiefile'		=> $this->_cookies_file,
				'returntransfer'	=> 1,
				'referer'			=> $this->_refer,
				'ssl_verifyhost'	=> 0,
				'useragent'			=> 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:6.0.2) Gecko/20100101 Firefox/6.0.2',
			);
			$this->_ci->my_curl->setup($conf);
			return $this->_ci->my_curl->exc();
		}
		
		function topage($url , $return = true){
			$conf = array(
					'url' => $url,
					'cookiejar' 		=> $this->_cookies_file,
					'cookiefile' 		=> $this->_cookies_file,
					'header'		=> 0,
					'nobody'	=> false,
					'returntransfer' => 1,
					'timeout'			=> 30,
				//	'returntransfer' => 1
				);
			$this->_ci->my_curl->setup($conf);
			$exc = $this->_ci->my_curl->exc();
			if($return == true ) return $this->_ci->my_curl;
			return $exc;
		}
		
		function _search(){
			$this->login();
			$dateExplode = explode('-',$this->_opt->date_depart);
			$post_data = array(
				'fromAirport'					=>	$this->_opt->route_from,
				'toAirport'						=>	$this->_opt->route_to,
				'dateFrom'						=>	$dateExplode[0].$this->dateAdd($dateExplode[1]).$this->dateAdd($dateExplode[2]),
				'dateTo'						=>	'',
				'iAdult'						=>	$this->_opt->passengers,
				'iChild'						=>	0,
				'iInfant'						=>	0,
				'BDClass'						=>	'Y',
				'isSearchGroup'					=>	0,
				'FareSelect'					=>	'',
				'dayRange'						=>	0,
				'transit_flag'					=>	0,
				'direct_flag'					=>	0,
				'require_passenger_title_flag'	=>	0,
				'require_passenger_gender_flag'	=>	0,
				'require_date_of_birth_flag'	=>	0,
				'require_document_details_flag'	=>	0,
				'require_passenger_weight_flag'	=>	0,
				'OriginName'					=>	'',//'Jakarta,Cengkareng',
				'DestinationName'				=>	'',//'Denpasar, Bali',
			);
			$header = array(
				"Content-Type:application/json; charset=UTF-8",
				'Content-Length:'.strlen(json_encode($post_data)),
			);
			
			$conf = array(
				'httpheader'		=> $header,
				'url'				=> $this->_search_url,
				'timeout'			=> 150,
				'header'			=> 0,
				'followlocation'	=> 1,
				'cookiejar'			=> $this->_cookies_file,
				'cookiefile'		=> $this->_cookies_file,
				'returntransfer'	=> 1,
				'post'				=> true,
				'referer'			=> $this->_refer,
				'ssl_verifyhost'	=> 0,
				'postfields'		=> json_encode($post_data),
				'useragent'			=> 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:6.0.2) Gecko/20100101 Firefox/6.0.2',
			);

			$this->_ci->my_curl->setup($conf);
			$this->_ci->my_curl->exc();
			return $this->mainPage();
		}
		
		function search(){
			$page = str_get_html($this->_search());
			if (!$page) {return array();}
			$table = $page->find('div[id=dvGridFlight] table tbody',0);
			if (!$table) return array();
			
			if ( $tr = count($table->find('tr')) < 2) { return array();}
			if ($table->find('tr',1)->find('td',0)->plaintext == 'We could not find any flights or seats available on the date selected') {
				return array();
			}
			//echo $table;
			$data = array();
			$flight_data = $table->find('tr');
			for ($i=1; $i < count($table->find('tr')); $i++) {
				$no_penerbangan = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$flight_data[$i]->find('td',0)->plaintext);
				$flightNum  = $this->cleanString($no_penerbangan);
				if ($flightNum  != '') {
					$temp = $flightNum ;
				}
				$flightNum = $temp;
				
				$dirty_date =  preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$flight_data[1]->find('td',1)->plaintext);
				$date = explode('/',$dirty_date);
				$class = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$flight_data[$i]->find('td',5)->plaintext);
				$price_dirty = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$flight_data[$i]->find('td',7)->plaintext);
				$price_dirt = str_replace('.00 IDR','',$price_dirty);
				$price_dir = str_replace(',','',$price_dirt);
				$price_di = str_replace('</input>','',$price_dir);
				$price = ($price_di+10000+6000)*$this->_opt->passengers;
				$jml_kursi = str_split(preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$flight_data[$i]->find('td',6)->plaintext),1);
				
				if (count($jml_kursi) > 1) {
					$flight_number = str_split($flightNum,5);
					$flightNo = $flight_number[0].' '.$flight_number[1];
					$time_depart = str_split(preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$flight_data[$i]->find('td',2)->plaintext),5);
					$time_arrive = str_split(preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$flight_data[$i]->find('td',3)->plaintext),5);
					$t_depart = $time_depart[0];
					$t_arrive = $time_arrive[1];
					$t_transit_depart = $time_depart[1];
					$t_transit_arrive = $time_arrive[0];
				}else{
					$flightNo = $flightNum	 ;
					$t_depart = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$flight_data[$i]->find('td',2)->plaintext);
					$t_arrive = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$flight_data[$i]->find('td',3)->plaintext);
					$t_transit_depart = NULL;
					$t_transit_arrive = NULL;
				}
				
				if ($this->roundTrip) {
					$type = 'return';
				}else{
					$type = 'depart';
				}
				$radio_value = $flight_data[$i]->find('td',7)->find('input',0)->getAttribute('value');
				
				$meta = array(
					'company'			=>	'MERPATI',
					'flight_no'			=>	$flightNo,
					't_depart'			=>	$date[2].'-'.$date[1].'-'.$date[0].' '.$t_depart,
					't_arrive'			=>	$date[2].'-'.$date[1].'-'.$date[0].' '.$t_arrive,
					't_transit_depart'	=>	$date[2].'-'.$date[1].'-'.$date[0].' '.$t_transit_depart,
					't_transit_arrive'	=>	$date[2].'-'.$date[1].'-'.$date[0].' '.$t_transit_arrive,					
					'type'				=>	$type,
					'class'				=>	$class,
					'price'				=>	$price,
					'route'				=>	$this->_opt->route_from.','.$this->_opt->route_to,
					'radio_value'		=>	$radio_value,
					'log_id'			=>	$this->_opt->id,
					'arrayIndex'		=>	$i,
					'time_depart'		=>	$date[2].'-'.$date[1].'-'.$date[0],
					'passangers'		=>	$this->_opt->passengers,
				);
				$data[$i]['company'] 			= 'MERPATI';
				$data[$i]['flight_no'] 			= $flightNo;
				$data[$i]['t_depart'] 			= $date[2].'-'.$date[1].'-'.$date[0].' '.$t_depart;
				$data[$i]['t_arrive']			= $date[2].'-'.$date[1].'-'.$date[0].' '.$t_arrive;
				$data[$i]['t_transit_depart']   = $date[2].'-'.$date[1].'-'.$date[0].' '.$t_transit_depart;
				$data[$i]['t_transit_arrive'] 	= $date[2].'-'.$date[1].'-'.$date[0].' '.$t_transit_arrive;
				$data[$i]['type'] 				= $type;
				$data[$i]['class'] 				= $class;
				$data[$i]['price'] 				= $price;
				$data[$i]['route'] 				= $this->_opt->route_from.','.$this->_opt->route_to;
				$data[$i]['log_id']				= $this->_opt->id;
				$data[$i]['meta_data']			= json_encode($meta);
				
			}
			return $data;	
		}			
		//don't delete this

		function getStep3(){
			$time = explode('-',$this->_opt->date_depart);
			
			$post_data = array(
				'OutwardFlightFareId'	=>	$this->_opt->radioValue,
				'ReturnFlightFareID'	=>	'',
				'OutWardDateFligh'		=>	$time[0].$time[1].$time[2].'_05_30',
				'OutSelectType'			=>	'FIRM',
				'RetSelectType'			=>	'FIRM',
			);
						
			$header = array(
				"Content-Type:application/json; charset=UTF-8",
				//"Host:www.merpati.co.id",
				'Content-Length:'.strlen(json_encode($post_data)),
			);
						
			$conf = array(
				'httpheader'		=> $header,
				'url'				=> $this->_detail_flight_url,
				'timeout'			=> 150,
				'header'			=> 0,
				'followlocation'	=> 1,
				'cookiejar'			=> $this->_cookies_file,
				'cookiefile'		=> $this->_cookies_file,
				'returntransfer'	=> 1,
				'post'				=> true,
				'referer'			=> $this->_refer,
				'VERBOSE'			=> 1,
				'ssl_verifyhost'	=> 0,
				'postfields'		=> json_encode($post_data),
				'useragent'			=> 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:6.0.2) Gecko/20100101 Firefox/6.0.2',
			);
			
			$this->_ci->my_curl->setup($conf);
			$this->_ci->my_curl->exc();
			//$page = json_decode($html,1);
			//echo implode($page);		
		}
		
		function detail(){
			$this->getStep3();
			$header = array(
				"Content-Type:application/json; charset=UTF-8",
				"Host:www.merpati.co.id",
				'Content-Length:'.strlen(''),
			);
			
			$conf = array(
				'httpheader'		=> $header,
				'url'				=> $this->_loadStep3_url,
				'timeout'			=> 150,
				'header'			=> 0,
				'followlocation'	=> 1,
				'cookiejar'			=> $this->_cookies_file,
				'cookiefile'		=> $this->_cookies_file,
				'returntransfer'	=> 1,
				'post'				=> true,
				'referer'			=> $this->_refer,
				'VERBOSE'			=> 1,
				'ssl_verifyhost'	=> 0,
				//'postfields'		=> json_encode($post_data),
				'useragent'			=> 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:6.0.2) Gecko/20100101 Firefox/6.0.2',
			);
			
			$this->_ci->my_curl->setup($conf);
			$f = $this->_ci->my_curl->exc();
			$array = json_decode($f,1);
			$html = implode($array);
			$page = str_get_html($html);
			$errPage = $page->find('div[class=WrapperTBLStep3] span[id=ctl00_GridItinerary]',0)->plaintext;
			if ($errPage == 'Missing xml data and xls file') {return array();}
			$table1 = $page->find('div[class=WrapperTBLStep3] span tbody',0);
			$table2 = $page->find('div[class=WrapperTBLStep3] div[id=dvQuoute] span tbody',0);
			if(!$table1 && !$table2) return array();
			
			$cnt_detailDataPenerbangan = count($table1->find('tr',0)->find('td'));
			$cnt_detailJmlPenerbangan = count($table1->find('tr'));
			$data = array();
			
			for ($i=1; $i < $cnt_detailJmlPenerbangan; $i++) { 
				for ($j=1; $j <= $cnt_detailDataPenerbangan; $j++) { 				
					$data[$i]['penerbangan'] = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$table1->find('tr',$i)->find('td',1)->plaintext);
					$data[$i]['from'] = $table1->find('tr',$i)->find('td',2)->plaintext;
					$data[$i]['to'] = $table1->find('tr',$i)->find('td',3)->plaintext;
					$data[$i]['date'] = $table1->find('tr',$i)->find('td',4)->plaintext;
					$data[$i]['departureTime'] = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$table1->find('tr',$i)->find('td',5)->plaintext);
					$data[$i]['arrivalTime'] = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$table1->find('tr',$i)->find('td',6)->plaintext);
					$data[$i]['status'] = $table1->find('tr',$i)->find('td',8)->plaintext;
				}			
			}
			
			$detail = $data;
			$finnal = array();

			foreach ($detail as $dta => $item) {
				foreach ($item as $fare) {
					$final[$i]	=	$fare;
					$i++;
				}
			}
			
			return $final;
			
		}
				
		public function closing(){
			$this->logout();
		}
		
		public function doSearch($opt)
		//public function doSearch()
		{
			$this->_opt->route_from 	= 'CGK';
			$this->_opt->route_to 		= 'DPS';
			$this->_opt->date_depart 	= '2011-12-31';
			$this->_opt->date_return 	= NULL;
			$this->_opt->passengers 	= 2;
			$this->_opt->id			= 1;
			
			foreach($opt as $key => $val ){$this->_opt->$key = $val;}
			
			if ($this->_opt->date_return) {
				$result1 = $this->search();
				
				$temp = '';
				$temp = $this->_opt->route_from;
				$this->_opt->route_from = $this->_opt->route_to;
				$this->_opt->route_to = $temp;
				$this->_opt->date_depart = $this->_opt->date_return;
				$this->roundTrip = true;
				
				$result2 = $this->search();
				$this->closing();
				$final = array_merge($result1,$result2);
			}else{
				$final = $this->search();
				$this->closing();
			}
			return array_values($final);
		}
		
		function array2xml($array,&$xml){
		   	foreach($array as $key => $value) {		
		        	if(is_array($value)) {
		            	if(!is_numeric($key)){
		                	$subnode = $xml->addChild("$key");
		                	$this->array2xml($value, $subnode);
		            	}else{
		                	$this->array2xml($value, $xml);
		            	}
		        	}else {
		            	$xml->addChild("$key","$value");
		        	}
		    }
			return $xml->asXML();
		}
				
		function loadStep4(){
			$header = array(
				"Content-Type:application/json; charset=UTF-8",
				"Host:www.merpati.co.id",
				'Content-Length:'.strlen(''),
			);
			
			$conf = array(
				'httpheader'		=> $header,
				'url'				=> $this->_loadStep4_url,
				'timeout'			=> 30,
				'header'			=> 0,
				'followlocation'	=> 1,
				'cookiejar'			=> $this->_cookies_file,
				'cookiefile'		=> $this->_cookies_file,
				'returntransfer'	=> 1,
				'post'				=> true,
				'referer'			=> $this->_refer,
				//'postfields'		=> json_encode($postData),
				'ssl_verifyhost'	=> 0,
				'useragent'			=> 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:6.0.2) Gecko/20100101 Firefox/6.0.2',
			);
			$this->_ci->my_curl->setup($conf);
			$html =  $this->_ci->my_curl->exc();
			$array = json_decode($html,1);
			//print_r($array);
			$html = implode($array);
			$page = str_get_html($html);
			$table =$page->find('table[id=PassengerList] tbody',0);
			if (!$table) {return array();}
			$passangerId = array();
			$passId = $table->find('input[id=uxPassengerID]');
			for ($i=0; $i < count($passId); $i++) { 
				$passangerId['uxPassengerID_'.$i] = $passId[$i]->getAttribute('value');
			}
			return $passangerId;
		}
				
		function abooking(){
			$this->forBooking();
			$this->detail();
			$passid = $this->loadStep4();
			$arrayXml = array();
		
			
			for ($i=0; $i < count($passid); $i++) { 
				$name = explode(' ',$this->passangers[$i]['name']);
				$title = $this->passangers[$i]['title'];
				
				if ($title == 'Mr' || $title == 'MR' ) {
					$gender = 'M';
				}elseif($title == 'Mrs'|| $title == 'MRS' || $title == 'Ms'|| $title == 'MS'){
					$gender = 'F';
				}
				
				$arrayXml[$i] = array(
					'Passenger'	=> array(
						'passenger_id'				=>	$passid['uxPassengerID_'.$i],
						'client_number'				=>	'',
						'client_profile_id'			=>	'00000000-0000-0000-0000-000000000000',
						'passenger_profile_id'		=>	'00000000-0000-0000-0000-000000000000',
						'passenger_type_rcd'		=>	'ADULT',
						'employee_number'			=>	'',
						'title_rcd'					=>	strtoupper($title).'|'.$gender,
						'lastname'					=>	strtoupper($name[1]),
						'firstname'					=>	strtoupper($name[0]),
						'nation'					=>	'ID',
						'documenttype'				=>	'B',
						'documentnumber'			=>	'',
						'issueplace'				=>	'',	
						'issuedate'					=>	'',
						'expireddate'				=>	'',
						'DOB'						=>	'',
						'company_phone_business'	=>	'',
						'company_phone_mobile'		=>	'',
						'company_phone_home'		=>	'',
						'contact_name'				=>	'',
						'passport_birth_place'		=>	'',
						'passenger_weight'			=>	'',
						'wheelchair_flag'			=>	'',
						'vip_flag'					=>	'',
						'window_seat_flag'			=>	'',
						'address_line1'				=>	'',
						'address_line2'				=>	'',
						'street'					=>	'',
						'province'					=>	'',
						'city'						=>	'',
						'zip_code'					=>	'',
						'po_box'					=>	'',
						'country_rcd'				=>	'',
					),

				);
			}
				
				
			$contactXML = array(
				'ContactPerson'		=>	$this->user['f_name'].' '.$this->user['l_name'],
				'HomePhone'			=>	'HomePhone',
				'Email'				=>	$this->user['email'],
				'MobilePhone'		=>	$this->user['user_detail']['mobile'],
				'BusinessPhone'		=>	$this->user['user_detail']['mobile'],
				'Language'			=>	'ID',
				'GroupName'			=>	'',
				'CostCenter'		=>	'',
				'PurchaseOrder'		=>	'',
				'ProjectNumber'		=>	''
				
			);
			$xmlPassanger = new SimpleXMLElement('<Passengers></Passengers>');
			$xmlContact	= new SimpleXMLElement("<contact></contact>");
			
			for ($i=0; $i < count($arrayXml); $i++) { 
				$passangerDataXml = $this->array2xml($arrayXml[$i],$xmlPassanger);
			}
						
			$post_data = array(
				'passengerXml'	=> 	$passangerDataXml,
				'Remark'		=>	'',
				'Remark2'		=>	'',
				'strContact'	=>	$this->array2xml($contactXML,$xmlContact),
				'xmlMailList'	=>	''
			);
			
			$header = array(
				"Content-Type:application/json; charset=UTF-8",
				"Host:www.merpati.co.id",
				'Content-Length:'.strlen(json_encode($post_data)),
			);
										
			$conf = array(
				'httpheader'		=> $header,
				'url'				=> $this->_booking_url,
				'timeout'			=> 150,
				'header'			=> 0,
				'followlocation'	=> 1,
				'cookiejar'			=> $this->_cookies_file,
				'cookiefile'		=> $this->_cookies_file,
				'returntransfer'	=> 1,
				'post'				=> true,
				'referer'			=> $this->_refer,
				'ssl_verifyhost'	=> 0,
				'postfields'		=> json_encode($post_data),
				'useragent'			=> 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:6.0.2) Gecko/20100101 Firefox/6.0.2',
			);
			$this->_ci->my_curl->setup($conf);
			$this->_ci->my_curl->exc();
			$this->loadssr();
			$this->loadstep5();
			$this->step5postpaid();
			return $this->payleter();
		}
		
		function loadssr(){
			$header = array(
				"Content-Type:application/json; charset=UTF-8",
				"Host:www.merpati.co.id",
				'Content-Length:'.strlen(''),
			);
				
			$conf = array(
				'httpheader'		=> $header,
				'url'				=> $this->_loadssr_url,
				'timeout'			=> 150,
				'header'			=> 0,
				'followlocation'	=> 1,
				'cookiejar'			=> $this->_cookies_file,
				'cookiefile'		=> $this->_cookies_file,
				'returntransfer'	=> 1,
				'post'				=> true,
				'referer'			=> $this->_refer,
				'ssl_verifyhost'	=> 0,
				//'postfields'		=> json_encode($post_data),
				'useragent'			=> 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:6.0.2) Gecko/20100101 Firefox/6.0.2',
			);
			$this->_ci->my_curl->setup($conf);
			$f = $this->_ci->my_curl->exc();
			$array = json_decode($f,1);
			$html = implode($array);
		}
		
		function loadstep5(){
			$header = array(
				"Content-Type:application/json; charset=UTF-8",
				"Host:www.merpati.co.id",
				'Content-Length:'.strlen(''),
			);
				
			$conf = array(
				'httpheader'		=> $header,
				'url'				=> $this->_loadStep5_url,
				'timeout'			=> 150,
				'header'			=> 0,
				'followlocation'	=> 1,
				'cookiejar'			=> $this->_cookies_file,
				'cookiefile'		=> $this->_cookies_file,
				'returntransfer'	=> 1,
				'post'				=> true,
				'referer'			=> $this->_refer,
				'ssl_verifyhost'	=> 0,
				//'postfields'		=> json_encode($post_data),
				'useragent'			=> 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:6.0.2) Gecko/20100101 Firefox/6.0.2',
			);
			$this->_ci->my_curl->setup($conf);
			$f = $this->_ci->my_curl->exc();
			$array = json_decode($f,1);
			$html = implode($array);
		}
		
		function step5postpaid(){
			$header = array(
				"Content-Type:application/json; charset=UTF-8",
				"Host:www.merpati.co.id",
				'Content-Length:'.strlen(''),
			);
				
			$conf = array(
				'httpheader'		=> $header,
				'url'				=> $this->_step5postpaid_url,
				'timeout'			=> 150,
				'header'			=> 0,
				'followlocation'	=> 1,
				'cookiejar'			=> $this->_cookies_file,
				'cookiefile'		=> $this->_cookies_file,
				'returntransfer'	=> 1,
				'post'				=> true,
				'referer'			=> $this->_refer,
				'ssl_verifyhost'	=> 0,
				//'postfields'		=> json_encode($post_data),
				'useragent'			=> 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:6.0.2) Gecko/20100101 Firefox/6.0.2',
			);
			$this->_ci->my_curl->setup($conf);
			$f = $this->_ci->my_curl->exc();
			$array = json_decode($f,1);
			$html = implode($array);
		}
		
		function payleter(){
			$header = array(
				"Content-Type:application/json; charset=UTF-8",
				"Host:www.merpati.co.id",
				'Content-Length:'.strlen(''),
			);
				
			$conf = array(
				'httpheader'		=> $header,
				'url'				=> $this->_payleter_url,
				'timeout'			=> 150,
				'header'			=> 0,
				'followlocation'	=> 1,
				'cookiejar'			=> $this->_cookies_file,
				'cookiefile'		=> $this->_cookies_file,
				'returntransfer'	=> 1,
				'post'				=> true,
				'referer'			=> $this->_refer,
				'ssl_verifyhost'	=> 0,
				//'postfields'		=> json_encode($post_data),
				'useragent'			=> 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:6.0.2) Gecko/20100101 Firefox/6.0.2',
			);
			$this->_ci->my_curl->setup($conf);
			$f = $this->_ci->my_curl->exc();
			$array = json_decode($f,1);
			$html = implode($array);
			return $html;
		}
		
		function booking(){
			//$html = "./components/partner/third_party/comp_maskapai/ojankillbooking_data/HTML/merpati/booking.html";
			$page = str_get_html($this->abooking());
			if (!$page) {return array();}
			$table = $page->find('div[class=WrapperBody] table');
			$cntTable = count($table);
			if ($cntTable == 0) {return array();}
			$bookingCode = $page->find('div[class=WrapperBody] div[class=BookingRefItenerary]',0)->find('span[class=BookingRefId]',0)->plaintext;
			$bookingDate = $page->find('div[class=WrapperBody] div[class=BookingRefIteneraryDate]',0)->plaintext;
			$bookingInfo = $table[0];
			$passangerInfo = $table[1];
			$countPassanger = count($passangerInfo->find('tr'));
			$data = array();
			$price = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',str_replace(',','',str_replace('.00','',str_replace('Total Harga','',$table[2]->find('tr',6)->find('td',4)->find('span[class=FooterTotalLabel]',0)->plaintext))));
			
			$flightNumber = $bookingInfo->find('tr',1)->find('td',0)->plaintext;
			$routeFrom = $bookingInfo->find('tr',1)->find('td',1)->plaintext;
			$routeTo = $bookingInfo->find('tr',1)->find('td',2)->plaintext;
			$date = explode('/',$bookingInfo->find('tr',1)->find('td',3)->plaintext);
			$time = $date[2].'-'.$date[1].'-'.$date[0];
			$departTime = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$bookingInfo->find('tr',1)->find('td',4)->plaintext);
			$arrTime = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$bookingInfo->find('tr',1)->find('td',5)->plaintext);
			$status = $bookingInfo->find('tr',1)->find('td',6)->plaintext;
			
			$data['booking_number']  	=	$bookingCode;
			$data['fare_id']			=	$this->fare_id;
			$data['meta_data']			=	json_encode($this->meta_data);
			$data['passangers']			=	$this->passangers;
			$data['final_price']		=	$this->meta_data['price'];
			//$data['flightNumber'] = $flightNumber;
			//$data['price']	=	$price;
			//$data['routeFrom'] = $routeFrom;
			//$data['routeTo'] = $routeTo;
			//$data['date'] = $time;
			//$data['departTime'] = $departTime;
			//$data['arrTime'] = $arrTime;
			//$data['status']	= $status;
			
			$ip = 1;
			for ($i=1; $i < $countPassanger; $i++) {
				$name = $passangerInfo->find('tr',$i)->find('td',3)->plaintext.' '.$passangerInfo->find('tr',$i)->find('td',1)->plaintext.' '.$passangerInfo->find('tr',$i)->find('td',2)->plaintext;
				$tipe = $passangerInfo->find('tr',$i)->find('td',4)->plaintext;
				//$data['passanger_'.$ip.'_name'] = $name;
				//$data['tipe_passanger_'.$ip] = $tipe;
				$ip++;
			}
			
			for ($i=0; $i < $cntTable; $i++) { 
				//echo $table[$i];
			}
			$this->closing();
			return $data;
			
		}
		
		function cleanString($text) {
		    // 1) convert á ô => a o
		    $text = preg_replace("/[áàâãªä]/u","a",$text);
		    $text = preg_replace("/[ÁÀÂÃÄ]/u","A",$text);
		    $text = preg_replace("/[ÍÌÎÏ]/u","I",$text);
		    $text = preg_replace("/[íìîï]/u","i",$text);
		    $text = preg_replace("/[éèêë]/u","e",$text);
		    $text = preg_replace("/[ÉÈÊË]/u","E",$text);
		    $text = preg_replace("/[óòôõºö]/u","o",$text);
		    $text = preg_replace("/[ÓÒÔÕÖ]/u","O",$text);
		    $text = preg_replace("/[úùûü]/u","u",$text);
		    $text = preg_replace("/[ÚÙÛÜ]/u","U",$text);
		    $text = preg_replace("/[’‘‹›‚]/u","'",$text);
		    $text = preg_replace("/[“”«»„]/u",'"',$text);
		    $text = str_replace("–","-",$text);
		    $text = str_replace(" "," ",$text);
		    $text = str_replace("ç","c",$text);
		    $text = str_replace("Ç","C",$text);
		    $text = str_replace("ñ","n",$text);
		    $text = str_replace("Ñ","N",$text);

		    //2) Translation CP1252. &ndash; => -
		    $trans = get_html_translation_table(HTML_ENTITIES);
		    $trans[chr(130)] = '&sbquo;';    // Single Low-9 Quotation Mark
		    $trans[chr(131)] = '&fnof;';    // Latin Small Letter F With Hook
		    $trans[chr(132)] = '&bdquo;';    // Double Low-9 Quotation Mark
		    $trans[chr(133)] = '&hellip;';    // Horizontal Ellipsis
		    $trans[chr(134)] = '&dagger;';    // Dagger
		    $trans[chr(135)] = '&Dagger;';    // Double Dagger
		    $trans[chr(136)] = '&circ;';    // Modifier Letter Circumflex Accent
		    $trans[chr(137)] = '&permil;';    // Per Mille Sign
		    $trans[chr(138)] = '&Scaron;';    // Latin Capital Letter S With Caron
		    $trans[chr(139)] = '&lsaquo;';    // Single Left-Pointing Angle Quotation Mark
		    $trans[chr(140)] = '&OElig;';    // Latin Capital Ligature OE
		    $trans[chr(145)] = '&lsquo;';    // Left Single Quotation Mark
		    $trans[chr(146)] = '&rsquo;';    // Right Single Quotation Mark
		    $trans[chr(147)] = '&ldquo;';    // Left Double Quotation Mark
		    $trans[chr(148)] = '&rdquo;';    // Right Double Quotation Mark
		    $trans[chr(149)] = '&bull;';    // Bullet
		    $trans[chr(150)] = '&ndash;';    // En Dash
		    $trans[chr(151)] = '&mdash;';    // Em Dash
		    $trans[chr(152)] = '&tilde;';    // Small Tilde
		    $trans[chr(153)] = '&trade;';    // Trade Mark Sign
		    $trans[chr(154)] = '&scaron;';    // Latin Small Letter S With Caron
		    $trans[chr(155)] = '&rsaquo;';    // Single Right-Pointing Angle Quotation Mark
		    $trans[chr(156)] = '&oelig;';    // Latin Small Ligature OE
		    $trans[chr(159)] = '&Yuml;';    // Latin Capital Letter Y With Diaeresis
		    $trans['euro'] = '&euro;';    // euro currency symbol
		    ksort($trans); 

		    foreach ($trans as $k => $v) {
		        $text = str_replace($v, $k, $text);
		    }
		    // 3) remove <p>, <br/> ...
		    $text = strip_tags($text); 
		    // 4) &amp; => & &quot; => '
		    $text = html_entity_decode($text);
		    // 5) remove Windows-1252 symbols like "TradeMark", "Euro"...
		    $text = preg_replace('/[^(\x20-\x7F)]*/','', $text); 
		    $targets=array('\r\n','\n','\r','\t');
		    $results=NULL;
		    $text = str_replace($targets,'',$text);
		    return $text;
		}
		
		//function preBooking(){
		function preBooking($fare_data){		
			/*$fare_data = array(
				'id'		=>	7323,
				'log_id'	=>	34,
				'company'	=>	'MERPATI',
				't_depart'	=>	'2011-12-31 05:30',
				't_arrive'	=>	'2011-12-31 08:10',
				'type'		=>	'depart',
				'class'		=>	'K',
				'route'		=>	'CGK,DPS',
				'meta_data'	=>	 '{"company":"MERPATI","flight_no":"MZ640","t_depart":"2011-12-31 05:30","t_arrive":"2011-12-31 08:10","t_transit_depart":"2011-12-31 ","t_transit_arrive":"2011-12-31 ","type":"depart","class":"K","price":929000,"route":"CGK,DPS","radio_value":"{E50DFBFD-BD76-11DF-995B-0019DBB9D31C}|{FB651EAA-5CA7-11DF-9F19-0050BA01BA7A}||","log_id":1,"arrayIndex":11,"time_depart":"2011-12-31","passangers":2}',
				't_transit_arrive'	=>	'',
				't_transit_depart'	=>	'',
				'price'				=>	'929000',
				'flight_no'			=>	'MZ640',
				'log'				=>	array(
					'id'				=>	34,
					'date_depart'		=>	'2011-12-31 00:00:00',
					'date_return'		=>	'',
					'route_from'		=>	'CGK',
					'route_to'			=>	'DPS',
					'passangers'		=>	1,
					'comp_include'		=>	'["Sriwijaya","Garuda","Merpati","Batavia","Citilink"]',
					'c_time'			=>	'2011-12-20 11:56:15',
					'max_fare'			=>	5,
					'actor'				=> 'CUS',
				),
			);*/

			
			$forBooking = json_decode($fare_data['meta_data'],1);
			$route = explode(',',$forBooking['route']);
			$route_from = $route[0];
			$route_to = $route[count($route)-1];
			
			$this->_opt->route_from 	= $route_from;
			$this->_opt->route_to 		= $route_to;
			$this->_opt->date_depart 	= $forBooking['time_depart'];
			$this->_opt->date_return 	= NULL;
			$this->_opt->passengers 	= $forBooking['passangers'];
			$this->_opt->id				= $forBooking['log_id'];
			
			//search again
			$reSearch = $this->forBooking();
			for ($i=0; $i < count($reSearch); $i++) { 
				$meta[$i] = json_decode($reSearch[$i]['meta_data'],1);
			}
			print_r($meta);
			//echo $arrayIndex = $this->multidimensional_search($meta,array('arrayIndex' => $forBooking['arrayIndex']));
			$classArray = $this->multidimensional_search($meta,array('class' => $forBooking['class']));
			if ($classArray == 'nothing') {
				$this->logout();
				return false;
			}else{
				$price = $meta[$classArray-1]['price'];
				if ($price > $forBooking['price']) {
					$this->logout();
					return $price;
				}else{
					$this->logout();
					return true;
				}

			}			
		}
		
		//function doBooking(){
		function doBooking($fare_data,$passangers_data,$customer_data){
			$this->login();
			/*$fare_data = array(
				'id'		=>	7323,
				'log_id'	=>	34,
				'company'	=>	'MERPATI',
				't_depart'	=>	'2011-12-31 05:30',
				't_arrive'	=>	'2011-12-31 08:10',
				'type'		=>	'depart',
				'class'		=>	'K',
				'route'		=>	'CGK,DPS',
				'meta_data'	=>	 '{"company":"MERPATI","flight_no":"MZ640","t_depart":"2011-12-31 05:30","t_arrive":"2011-12-31 08:10","t_transit_depart":"2011-12-31 ","t_transit_arrive":"2011-12-31 ","type":"depart","class":"M","price":1330000,"route":"CGK,DPS","radio_value":"{E50DFBFD-BD76-11DF-995B-0019DBB9D31C}|{8C0CE44C-7297-11DF-ABFC-0019DBB9AC4D}||","log_id":1,"arrayIndex":1,"time_depart":"2011-12-31","passangers":2}',
				't_transit_arrive'	=>	'',
				't_transit_depart'	=>	'',
				'price'				=>	'929000',
				'flight_no'			=>	'MZ640',
				'log'				=>	array(
					'id'				=>	34,
					'date_depart'		=>	'2011-12-31 00:00:00',
					'date_return'		=>	'',
					'route_from'		=>	'CGK',
					'route_to'			=>	'DPS',
					'passangers'		=>	1,
					'comp_include'		=>	'["Sriwijaya","Garuda","Merpati","Batavia","Citilink"]',
					'c_time'			=>	'2011-12-20 11:56:15',
					'max_fare'			=>	5,
					'actor'				=> 'CUS',
				),
			);
			$passangers_data = array(
				array(
						'title' 			=>	'Mr',
						'name' 				=>	'Zidni Mubarock',
						'no_id'				=>	'3671081902880001',

				),
				array(
						'title' 			=>	'Mr',
						'name' 				=>	'Fauzan Qadri',
						'no_id'				=>	'3671081902880001',
				),
			);
			
			$customer_data = array(
				'f_name'	=>	'Zidni',
				'l_name'	=>	'Mubarok',
				'email'		=>	'zidmubarock@gmail.com',
				'password'	=>	'aca9fd21ff5e08cf88a3929ef5c4f346',
				'role_id'	=>	1,
				'c_time'	=>	'2011-12-11 21:04:04',
				'm_time'	=>	'',
				'status'	=>	'active',
				'actv_key'	=>	'',

				'user_detail'	=> 	array(
					'user_id'	=>	26,
					//'no_id'		=>	'3671081902880001'
					'phone'		=>	'0215579315134',
					'mobile'	=>	'0215579315134',
					'address'	=>	'jalan anggrek no',
					'gender'	=>	'M',
									
				),
				
			);*/
			$this->passangers = $passangers_data;
			$this->user = $customer_data;
			
			$forBooking = json_decode($fare_data['meta_data'],1);
			$route = explode(',',$forBooking['route']);
			$route_from = $route[0];
			$route_to = $route[count($route)-1];
			$this->fare_id = $fare_data['id'];
			$this->meta_data = $forBooking;
			
			$this->_opt->route_from 	= $route_from;
			$this->_opt->route_to 		= $route_to;
			$this->_opt->date_depart 	= $forBooking['time_depart'];
			$this->_opt->date_return 	= NULL;
			$this->_opt->passengers 	= $forBooking['passangers'];
			$this->_opt->id				= $forBooking['log_id'];
			$this->_opt->radioValue		= $forBooking['radio_value'];
			$this->forBooking();
			
			$booking = $this->booking();
			$this->logout();
			return $booking;
		}
		
		function multidimensional_search($parents, $searched) { 
		  if (empty($searched) || empty($parents)) { 
		    return 'nothing'; 
		  } 

		  foreach ($parents as $key => $value) { 
		    $exists = true; 
		    foreach ($searched as $skey => $svalue) { 
		      $exists = ($exists && IsSet($parents[$key][$skey]) && $parents[$key][$skey] == $svalue); 
		    } 
		    if($exists){ return $key+1; } 
		  } 

		  return 'nothing'; 
		}
		
		private function forBooking(){
			if ($this->_opt->date_return) {
				$result1 = $this->search();
				
				$temp = '';
				$temp = $this->_opt->route_from;
				$this->_opt->route_from = $this->_opt->route_to;
				$this->_opt->route_to = $temp;
				$this->_opt->date_depart = $this->_opt->date_return;
				$this->roundTrip = true;
				
				$result2 = $this->search();
				$this->closing();
				$final = array_merge($result1,$result2);
			}else{
				$final = $this->search();
				$this->closing();
			}
			return array_values($final);
		}
	}
