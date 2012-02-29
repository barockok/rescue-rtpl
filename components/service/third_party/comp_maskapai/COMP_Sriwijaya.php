<? if (! defined('BASEPATH')) exit('No direct script access');

class Sriwijaya extends Comp_maskapai_base{
	
	private $username 			= 'idbooking6';
	private $password 			= 'indonesia';
	private $_default_url 		= 'https://agent.sriwijayaair.co.id/b2b/secure/home.jsp';
	private $_login_url 		= 'https://agent.sriwijayaair.co.id/b2b/secure/j_security_check';
	private $_user_agent 		= 'Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1';
	private $_logout_url 		= 'https://agent.sriwijayaair.co.id/b2b/secure/logout.jsp';
	private $_src_action_url 	= 'https://agent.sriwijayaair.co.id/b2b/secure/AvailabilityAction';
	private $_booking_url		= 'https://agent.sriwijayaair.co.id/b2b/secure/PNRAction';
	private $_src_result_url 	= 'https://agent.sriwijayaair.co.id/b2b/secure/createpnr.jsp';
	private $_start_url 		= 'https://agent.sriwijayaair.co.id/b2b/secure/';
	
	function __construct() {
		parent::__construct();
		$this->roundTrip = false;
		$this->_cookies_file = "./components/service/third_party/comp_maskapai/cookies/sriwijaya_airline.txt";
		$this->login();
	}
	
	function index() {}
	
		function dateConvertMin($month){
			return $month - 1;
		}

		function monthConvert($month){

			$month_number = "";
			for($i=1;$i<=12;$i++){ 
				if(date("M", mktime(0, 0, 0, $i, 1, 0)) == $month){ 
					$month_number = $i; 
					break; 
				} 
			}
			return $month_number;
		}
		
	function mainPage(){
		$conf = array(
			'url'				=> $this->_default_url,
			'timeout'			=> 30,
			'header'			=> 0,
			'followlocation'	=> 1,
			'cookiejar'			=> $this->_cookies_file,
			'cookiefile'		=> $this->_cookies_file,
			'returntransfer'	=> 1,
			'ssl_verifyhost'	=> 0,
			'useragent'			=> $this->_user_agent,
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
			//	'returntransfer' => 1
			);
		$this->_ci->my_curl->setup($conf);
		$exc = $this->_ci->my_curl->exc();
		if($return = true ) return $this->_ci->my_curl;
		echo $exc;
	}
	public function start()
	{
		$conf = array(
			'url' => $this->_start_url,
			'cookiejar' 		=> $this->_cookies_file,
			'cookiefile' 		=> $this->_cookies_file,
			'header'		=> 0,
			'nobody'	=> true,
		//	'returntransfer' => 1
		);
		$this->_ci->my_curl->setup($conf);
		$this->_ci->my_curl->exc();
		
	}
	function login(){
		$this->start();
		$post_data = array(
			'j_username' => $this->username,
			'j_password' => $this->password,
			
		);
		$conf = array(
			'url' 				=> $this->_login_url,
			'timeout' 			=> 30,
		
			'header' 			=> 1,
			'nobody' 			=> false,
			'followlocation' 	=> 1,
			'cookiejar' 		=> $this->_cookies_file,
			'cookiefile' 		=> $this->_cookies_file,
			'returntransfer' 	=> true,
			'post' 				=> true,
			'MAXREDIRS' 		=> 10,
			'ssl_verifyhost' 	=> 0,
			//'referer' 			=> $this->referer_url,
			'AUTOREFERER' 		=> true,
			'FAILONERROR' 		=> false,
			'postfields' 		=> http_build_query( $post_data , NULL, '&' ),
		);
		$this->_ci->my_curl->setup($conf);
		$this->_ci->my_curl->exc();
		
		
		//https://agent.sriwijayaair.co.id/b2b/secure/logout.jsp
	
		
	}
	function somepage(){
		$conf = array(
				'url' => 'https://agent.sriwijayaair.co.id/b2b/secure/home.jsp',
				'cookiejar' 		=> $this->_cookies_file,
				'cookiefile' 		=> $this->_cookies_file,
				'header'		=> 0,
				'nobody'	=> false,
			//	'returntransfer' => 1
			);
		$this->_ci->my_curl->setup($conf);
		echo $this->_ci->my_curl->exc();
	}
	function logout(){
		
			$conf = array(
					'url' => 'https://agent.sriwijayaair.co.id/b2b/secure/logout.jsp',
					'cookiejar' 		=> $this->_cookies_file,
					'cookiefile' 		=> $this->_cookies_file,
					'header'		=> 0,
					'nobody'	=> true,


				//	'returntransfer' => 1
				);
			$this->_ci->my_curl->setup($conf);
			$this->_ci->my_curl->exc();
	}
	
	public function _search()
	{
		$date = explode('-',$this->_opt->date_depart);	
		$posted = array(
			'isReturn' 			=> false,
			'from'		 		=> $this->_opt->route_from,
			'to'				=> $this->_opt->route_to,
			'departDate1' 		=> element('2',$date),
			'departDate2'		=> $this->dateConvertMin(element('1',$date)).'-'.element('0',$date),
			'adult' 			=> $this->_opt->passengers,
			'child' 			=> 0,
			'infant'			=> 0,
			'returndaterange' 	=> 0,
			'Submit' 			=> 'Search',
		);
		
		$conf = array(
			'url' 				=> $this->_src_action_url,
			'post' 				=> true,
			'postfields' 		=> http_build_query($posted),
			'timeout'			=> 150,
			'header'			=> 0,
			'followlocation'	=> true,
			'maxredirs'			=> 10,
			'cookiefile'		=> $this->_cookies_file,
			'returntransfer'	=> 1,
			'post'				=> true,
			'referer'			=> $this->_default_url,
			'ssl_verifyhost'	=> 0,
			'useragent'			=> $this->_user_agent,
		);
		
		$this->_ci->my_curl->setup($conf);
		$this->_ci->my_curl->exc();
		$res = $this->topage($this->_ci->my_curl->res_info('url'));
		return $res->res();
	}
	
	function selectSummary($var){
		$url = 'https://agent.sriwijayaair.co.id/b2b/secure/selectedsummary.jsp?selected='.$var.'&extracover=true';
			$conf = array(
				'url'				=> $url,
				'timeout'			=> 30,
				'header'			=> 0,
				'followlocation'	=> 1,
				'cookiejar'			=> $this->_cookies_file,
				'cookiefile'		=> $this->_cookies_file,
				'returntransfer'	=> 1,
				'ssl_verifyhost'	=> 0,
				'useragent'			=> $this->_user_agent,
			);
		$this->_ci->my_curl->setup($conf);
		$html = $this->_ci->my_curl->exc();
		$page =str_get_html($html);
	}
	
	function detail($var){
		$this->selectSummary($var);
		$url = 'https://agent.sriwijayaair.co.id/b2b/secure/faredetails.jsp';
		$conf = array(
			'url'				=> $url,
			'timeout'			=> 30,
			'header'			=> 0,
			'followlocation'	=> 1,
			'cookiejar'			=> $this->_cookies_file,
			'cookiefile'		=> $this->_cookies_file,
			'returntransfer'	=> 1,
			'ssl_verifyhost'	=> 0,
			'useragent'			=> $this->_user_agent,
		);
		$this->_ci->my_curl->setup($conf);
		$html = $this->_ci->my_curl->exc();
		$page = str_get_html($html);
		$data = array();
		$fare = $page->find('div[id=fareDetail] div');
		for ($i=0; $i < count($fare); $i++) { 
			$fare_type = strtoupper(element('2',explode(' ',$fare[$i]->find('dl',0)->find('dt',0)->plaintext)));
			$price_per_pax = str_replace(',','',
			$fare[$i]->find('dl',0)->find('dd',0)->find('span[class=priceDetail]',0)->plaintext);
			
			$pajak = str_replace(',','',
			$fare[$i]->find('dl',0)->find('dd',1)->find('span[class=priceDetail]',0)->plaintext);
			
			$iwjr = str_replace(',','',
			$fare[$i]->find('dl',0)->find('dd',2)->find('span[class=priceDetail]',0)->plaintext);
			
			$extraCover = str_replace(',','',
			$fare[$i]->find('dl',0)->find('dd',3)->find('span[class=priceDetail]',0)->plaintext);
			
			$data[$i]['passanger_type'] = $fare_type;
			$data[$i]['price_per_pax'] = $price_per_pax;
			$data[$i]['pajak'] = $pajak;
			$data[$i]['iwjr'] = $iwjr;
			$data[$i]['extra_cover'] = $extraCover;
		}
		
		$price = str_replace(array(",","IDR"),'',
		$page->find('div[id=fareDetailAmount]',0)->find('span[class=priceDetail bold]',0)->plaintext);
		
		//$data['price'] = $price;
		$metaArray = json_decode(element('meta_data',$this->fare_data),1);
		
		$meta = array(
			'comapny'			=>	element('company',$this->fare_data),
			'flight_no'			=>	element('flight_no',$this->fare_data),
			't_depart'			=>	element('t_depart',$this->fare_data),
			't_arrive'			=>	element('t_arrive',$this->fare_data),
			't_transit_arrive'	=>	element('t_transit_arrive',$this->fare_data),
			't_transit_depart'	=>	element('t_transit_depart',$this->fare_data),
			'type'				=>	element('type',$this->fare_data),
			'price'				=>	$price,
			'class'				=>	element('class',$this->fare_data),
			'route'				=>	element('route',$this->fare_data),
			'log_id'			=>	element('log_id',$this->fare_data),
			'arrayIndex'		=>	element('arrayIndex',$metaArray),
			'passangers'		=>	$this->_opt->passengers,
			//'adult'				=>	$this->_opt->adult,
			//'child'				=> 	$this->_opt->child,
			//'infant'			=>	$this->_opt->infant,
			'time_depart'		=>	$this->_opt->date_depart,
			'radio_value'		=>	$this->_opt->radio_value,
			'price_detail'		=>	$data
		);
		
		$fare_data['id'] = element('id',$this->fare_data);
		$fare_data['log_id'] = element('log_id',$this->fare_data);
		$fare_data['company'] = element('company',$this->fare_data);
		$fare_data['flight_no'] = element('flight_no',$this->fare_data);
		$fare_data['t_depart'] = element('t_depart',$this->fare_data);
		$fare_data['t_arrive'] = element('t_arrive',$this->fare_data);
		$fare_data['type'] = element('type',$this->fare_data);
		$fare_data['class'] = element('class',$this->fare_data);
		$fare_data['route'] = element('route',$this->fare_data);
		$fare_data['t_transit_arrive'] = element('t_transit_arrive',$this->fare_data);
		$fare_data['t_transit_depart'] = element('t_transit_depart',$this->fare_data);
		$fare_data['price'] = $price;
		$fare_data['meta_data'] = json_encode($meta);
		$fare_data['log'] = element('log',$this->fare_data);
		return $fare_data;
	}
	
	public function getDetail($fare_data = array())
	{
		$fare_data = array(
			'id'		=>	7323,
			'log_id'	=>	34,
			'company'	=>	'SRIWIJAYA',
			't_depart'	=>	'2012-03-21 09:05',
			't_arrive'	=>	'2012-03-21 11:20',
			'type'		=>	'depart',
			'class'		=>	'Q',
			'route'		=>	'CGK,MES',
			'meta_data'	=>	 '{"company":"SRIWIJAYA","flight_no":"SJ 010","t_depart":"2012-03-21 09:05","t_arrive":"2012-03-21 11:20","type":"depart","class":"Q","price":850000,"route":"CGK,MES","t_transit_arrive":null,"t_transit_depart":null,"log_id":1,"arrayIndex":"0,7","radio_value":"689e369a-f95e-47f9-b966-08f5adff60f1|ec5e26ed-cf55-4074-85ab-7a04e58ce92a|2138465f-705b-4bba-9e1c-1cbabbcc115a","time_depart":"2012-3-21","passangers":1}',
			't_transit_arrive'	=>	'',
			't_transit_depart'	=>	'',
			'price'				=>	'850000',
			'flight_no'			=>	'SJ 010',
			'log'				=>	array(
				'id'				=>	34,
				'date_depart'		=>	'2012-01-31 00:00:00',
				'date_return'		=>	'',
				'route_from'		=>	'CGK',
				'route_to'			=>	'MES',
				'passangers'		=>	1,
				'comp_include'		=>	'["Sriwijaya","Garuda","Merpati","Batavia","Citilink"]',
				'c_time'			=>	'2011-12-20 11:56:15',
				'max_fare'			=>	5,
				'actor'				=> 'CUS',
			),
		);
		
		$meta_data = json_decode(element('meta_data',$fare_data),1);
		$log = element('log',$fare_data);
		
		$this->_opt->route_from 	= element('route_from',$log);
		$this->_opt->route_to 		= element('route_to',$log);
		$this->_opt->date_depart 	= element('time_depart',$meta_data);
		$this->_opt->date_return 	= null;
		$this->_opt->passengers 	= element('passangers',$meta_data);
		$this->_opt->id		= element('log_id',$meta_data);
		$this->fare_data = $fare_data;
		$searchResult = $this->forBooking();
		$arrayIndex = element('arrayIndex',$meta_data);
		$newMeta = array();
		for ($i=0; $i < count($searchResult); $i++) {
			$newMetaSearch[$i] = json_decode(element('meta_data',element($i,$searchResult)),1);
		}
		$newIndex = $this->multidimensional_search($newMetaSearch,array('arrayIndex' => $arrayIndex));
		$newMetaData = element($newIndex,$newMetaSearch);
		$radioValue = explode('|',element('radio_value',$newMetaData));
		$this->_opt->radio_value = element('radio_value',$newMetaData);
		
		$detail = $this->detail(element(count($radioValue)-1,$radioValue));
		$this->closing();
		return $detail;
	}
	
	function search(){
		if ($this->roundTrip) {
			$type = 'return';
		}else{
			$type = 'depart';
		}
		
		$page = str_get_html($this->_search());
		if (!$page) {
			return false;
		}
		if(!$table = $page->find('div[id=pagewrapper] div[id=mainWrapper]',0)->find('form[action=./PNRAction]',0)) return false;
		if(!$flight = $page->find('table[id=table_go]',0)->find('tr table[class=flightInfo]')) return false;
		
		//$class = $page->find('table[id=table_go] table[class=classTable] td');
		$classTable = $page->find('table[id=table_go] table[class=classTable]');
		$Table = $page->find('table[id=table_go]',0);
		$date = $page->find('span[class=avTableLabel2]',0)->plaintext;
		$cdate = explode('-',$date);
		$dateFormated = '20'.element('2',$cdate).'-'.$this->monthConvert(element('1',$cdate)).'-'.element('0',$cdate);
		$insideFlight = $flight[0]->find('tr');
		$cntInsideFlight = count($insideFlight);
		$cnt_flight = count($flight);
		//$cnt_classFlight = count($class);
		if (!$Table) return false;
		$data = array();
		$index=0;
 		$cell = $table->find('table[id=table_go]',0)->find('tbody tr td[class=rightTD]');	
		for ($i=0; $i < count($classTable); $i++) {
			$class = $classTable[$i]->find('td');
			//return array('cnt'=> count($class));
			for ($j=0; $j < count($class); $j++) { 
				//echo $i.','.$j.'<br/>';
				if ($cell[$i]->find('div[class=avcell] input[class=avcellRadio]',$j)->getAttribute('disabled') == 'disabled') {continue;}
				
				$clas = $class[$j]->find('span',0)->plaintext;
				$price = $class[$j]->find('span',2)->plaintext;
				$t_depart = $flight[$i]->find('tr',0)->find('td',1)->find('span',1)->plaintext;
				$route_from = $flight[$i]->find('tr',0)->find('td',1)->find('span',0)->plaintext;
				$flight_number = $flight[$i]->find('tr',0)->find('td',0)->find('span',0)->plaintext;

				if ($cnt_flight == 1 || $cntInsideFlight ==1) {
					$route_arr = $flight[$i]->find('tr',0)->find('td',2)->find('span',0)->plaintext;
					$route_transit = '';
					$t_transit_arrive_time = NULL;
					$t_transit_depart_time = NULL;
					$t_arival = $flight[$i]->find('tr',0)->find('td',2)->find('span',1)->plaintext;
					$time_arrive = strtotime($dateFormated.' '.$t_arival);
					$ttarrive = $t_transit_arrive_time;
					$ttdepart = $t_transit_depart_time;
				}else {
					$route_arr = $flight[$i]->find('tr',1)->find('span',4)->plaintext;
					$route_transit =  ','.$flight[$j]->find('tr',0)->find('td',2)->find('span',0)->plaintext;
					$t_transit_arrive_time = $dateFormated.' '.$flight[$i]->find('tr',0)->find('td',2)->find('span',1)->plaintext;
					$t_transit_depart_time = $dateFormated.' '.$flight[$i]->find('tr',1)->find('td',1)->find('span',1)->plaintext;
					$t_arival = $flight[$i]->find('tr',1)->find('span',5)->plaintext;
					$time_arrive = strtotime($dateFormated.' '.$t_arival);
					$time_transit_arrive = strtotime($t_transit_arrive_time);
					$time_transit_depart = strtotime($t_transit_depart_time);
					$ttarrive = date("Y-m-d h:i",$time_transit_arrive);
					$ttdepart = date("Y-m-d h:i",$time_transit_depart);
				}
				$nprice = str_replace(',','',$price).'000';
				$fp = $nprice*$this->_opt->passengers;
				$activeCell = $cell[$i]->find('div input',$j)->getAttribute('value');
				$time_depart = strtotime($dateFormated.' '.$t_depart);
				
				$var = element('2',explode('|',$activeCell));
				//$detail = $this->getDetail($var);
				//$price = element('price',$detail);

				$meta = array(
					'company'				=> 	'SRIWIJAYA',
					'flight_no'				=> 	$flight_number,
					't_depart'				=>	date("Y-m-d h:i",$time_depart),
					't_arrive'				=>	date("Y-m-d h:i",$time_arrive),
					'type'					=>	$type,
					'class'					=>	$clas,
					'price'					=>	$fp,
					'route'					=>	$route_from.$route_transit.','.$route_arr,
					't_transit_arrive'		=>	$ttarrive,
					't_transit_depart'		=>	$ttdepart,
					'log_id'				=>	$this->_opt->id,
					'arrayIndex'			=>	$i.','.$j,
					'radio_value'			=>	$activeCell,
					'time_depart'			=>	$dateFormated,
					'passangers'			=>	$this->_opt->passengers,
					//'adult'					=>	$this->_opt->adult,
					//'child'					=>	$this->_opt->child,
					//'infant'				=>	$this->_opt->infant,
					//'detail'				=>	$detail,
				);

				$data[$index][$j]['company'] 				= 'SRIWIJAYA';
				$data[$index][$j]['flight_no']				= $flight_number;
				$data[$index][$j]['t_depart'] 				= date("Y-m-d h:i",$time_depart)	;
				$data[$index][$j]['t_arrive']				= date("Y-m-d h:i",$time_arrive);
				$data[$index][$j]['type'] 					= $type;
				$data[$index][$j]['class'] 					= $clas;
				$data[$index][$j]['price'] 					= $fp;
				$data[$index][$j]['route'] 					= $route_from.$route_transit.','.$route_arr;
				$data[$index][$j]['t_transit_arrive'] 		= $ttarrive;
				$data[$index][$j]['t_transit_depart'] 		= $ttdepart;
				$data[$index][$j]['log_id']					= $this->_opt->id;
				$data[$index][$j]['meta_data']				= json_encode($meta);
			}
			$index++;
		}
		
		$final = array();
		$i  = 0;
		foreach($data as $fl => $item ){
			foreach($item as $fare){
				$final[$i] = $fare;
				$i++;
			}
		}
		
		return $final;
	}
	
	
	
	function abooking(){
		$passangerData = array();
		/*if ($adult = element('ADULT',$this->passangers)) {
			$ip = 1;
			foreach ($adult as $value) {
				$passangerData['adult.title.'.$ip] 					= element('title',$value);
				$passangerData['adult.name.'.$ip] 					= element('name',$value);
				$passangerData['adult.id.'.$ip]						= element('no_id',$value);
				$passangerData['adult.specialRequestSelect.'.$ip]	= '';
				$ip++;
			}
		}
		if ($child = element('CHILD',$this->passangers)) {
			$ip = 1;
			foreach ($child as $value) {
				$passangerData['child.title.'.$ip] 					= element('title',$value);
				$passangerData['child.name.'.$ip] 					= element('name',$value);
				$passangerData['child.id.'.$ip]						= "<Identity No>";
				$passangerData['child.specialRequestSelect.'.$ip]	= '';
				$passangerData['infant.dateofbirth.'.$ip]			= '';
				$passangerData['infant.monthofbirth.'.$ip]			= '';
				$passangerData['infant.yearofbirth.'.$ip]			= '';
				$ip++;
			}
		}
		if ($infant = element('INFANT',$this->passangers)) {
			$ip = 1;
			foreach ($infant as $value) {
				$passangerData['infant.name.'.$ip] 					= element('name',$value);
				$passangerData['infant.id.'.$ip]					= "<Identity No>";
				$passangerData['infant.dateofbirth.'.$ip]			= '';
				$passangerData['infant.monthofbirth.'.$ip]			= '';
				$passangerData['infant.yearofbirth.'.$ip]			= '';
				$passangerData['infant.assocSelect.'.$ip]			= 1;
				$ip++;
			}
		}*/
		
		$ip = 1;
		foreach ($this->passangers as $key => $value) {
			$passangerData['adult.title.'.$ip] 					= element('title',$value);
			$passangerData['adult.name.'.$ip] 					= element('name',$value);
			$passangerData['adult.id.'.$ip]						= element('no_id',$value);
			$passangerData['adult.specialRequestSelect.'.$ip]	= '';
			$ip++;
		}
		
		$contactData = array(
			'contactcustomer.name'			=>	element('f_name',$this->contact).' '.element('l_name',$this->contact),
			'contactcustomer.phone'			=>	$this->contact['user_detail']['phone'],
			'contactcustomer.otherphone'	=>	'<Other Phone>',
		);
		
		
		$Agentdata = array(
				'contactagent.name'		=>	'PT. REA TOUR',
				'contactagent.email'	=>	'boyarie_zag@yahoo.com',
				'contactagent.phone'	=>	'081252676799',
				'extracover'			=>	'extracover',
				'term'					=>	1,
				'procceedType'			=>	'Book'			
		);
		
		$post_data = array_merge($this->data,$passangerData,$contactData,$Agentdata);
		
		$conf = array(
			'url' 				=> $this->_booking_url,
			'post' 				=> true,
			'postfields' 		=> http_build_query($post_data),
			'timeout'			=> 150,
			'header'			=> 0,
			'followlocation'	=> true,
			'maxredirs'			=> 10,
			'cookiefile'		=> $this->_cookies_file,
			'returntransfer'	=> 1,
			'post'				=> true,
			'referer'			=> $this->_src_result_url,
			'ssl_verifyhost'	=> 0,
			'useragent'			=> $this->_user_agent,
		);
		
		$this->_ci->my_curl->setup($conf);
		$this->_ci->my_curl->exc();
		$res = $this->topage($this->_ci->my_curl->res_info('url'));
		return $res->res();
	}
	
	
	
	function booking(){

		$page = str_get_html($this->aBooking());
		if (!$page) {
			return array();
		}
		$table = $page->find('div[id=mainWrapper] div[id=pnr] table tbody');
		$cntTable = count($table);
		if ($cntTable == 0) { return array();}
		$data = array();
		$bookingDetails = $table[0];
		$passangerDetail = $table[1];
		$roouteDetail = $table[2];
		$paymentDetail = $table[3];
		$bookingCode = $bookingDetails->find('tr',1)->find('td',0)->plaintext;
		
	 	$limit	= preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$bookingDetails->find('tr',1)->find('td',1)->plaintext);
		$bookingDate = $bookingDetails->find('tr',2)->find('td',0)->find('span',0)->plaintext;
		$status	= $bookingDetails->find('tr',2)->find('td',1)->plaintext;
		$routeDet = $roouteDetail->find('tr',1)->find('th',0)->plaintext;
		$flightNumber = $roouteDetail->find('tr',2)->find('td',0)->plaintext;
		$departTime = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',str_replace('Departing at','',$roouteDetail->find('tr',2)->find('td',1)->plaintext));
		$arrTime = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',str_replace('Arriving at','',$roouteDetail->find('tr',2)->find('td',2)->plaintext));
		$class = str_replace('Class : ','',$roouteDetail->find('tr',2)->find('td',3)->plaintext);
		$countPassanger = count($passangerDetail->find('tr'));
		$price = str_replace(',','',str_replace('IDR','',$paymentDetail->find('tr',4)->find('td[class=fareTotal]',0)->plaintext));
		$limit_time = explode(',',$limit);
		$dateLimit = explode('-',$limit_time[0]);
		$flghtnum = str_split($flightNumber,6);
		$data['booking_number'] 	=	$bookingCode;
		$data['fare_id']			=	$this->fare_id;
		$data['meta_data']			=	json_encode($this->meta_data);
		$data['passangers']			=	$this->passangers;
		$data['final_price']		=	$price;
		//$data['limit'] 			=	$limit_time[0].' '.str_replace(' (GMT+0700)','',$limit_time[1]);
		//$data['bookingDate']	=	$bookingDate;
		//$data['status']			=	$status;
		//$data['routeDet'] 		=	$routeDet;
		//$data['flightNumber'] 	=	$flghtnum[0];
		//$data['departTime'] 	= 	$departTime;
		//$data['arrTime']		=	$arrTime;
		//$data['class']			=	$class;
		//$data['price']			= 	$price;
		$ip = 1;
		for ($i=2; $i < $countPassanger; $i++) {
			$name = $passangerDetail->find('tr',$i)->find('td',1)->plaintext.' '.$passangerDetail->find('tr',$i)->find('td',2)->plaintext;
			$ticketNumber = $passangerDetail->find('tr',$i)->find('td',3)->plaintext;
			$specialReq = $passangerDetail->find('tr',$i)->find('td',4)->plaintext;
			//$data['penumpang_'.$ip.'_name'] = $name;
			//$data['ticketnumber_'.$ip] = $ticketNumber;
			//$data['specialRequest_'.$ip] = $specialReq;
			$ip++;
		}
		for ($i=0; $i < $cntTable; $i++) { 
			//echo $table[$i];
		}
		$this->closing();
		return $data;
	}
		
	private function forBooking(){
			//foreach($opt as $key => $val ){$this->_opt->$key = $val;}
		$final = $this->search();
		return $final;
	}
	
	function closing(){
		$this->logOut();
	}
	
	public function doSearch($opt = array())
	//public function doSearch()
	{
		$this->_opt->route_from 	= 'CGK';
		$this->_opt->route_to 		= 'MES';
		$this->_opt->date_depart 	= '2012-03-21';
		$this->_opt->date_return 	= NULL;
		$this->_opt->passengers 	= 1;
		//$this->_opt->adult = 1;
		//$this->_opt->child = 0;
		//$this->_opt->infant = 0;
		$this->_opt->id		= 1;		
		foreach($opt as $key => $val ){$this->_opt->$key = $val;}
			
		if ($this->_opt->date_return) {
			$result1 = (is_array($rs1 = $this->search())) ? $rs1 : array();
			
			$temp = '';
			$temp = $this->_opt->route_from;
			$this->_opt->route_from = $this->_opt->route_to;
			$this->_opt->route_to = $temp;
			$this->_opt->date_depart = $this->_opt->date_return;
			$this->roundTrip = true;
			
			$result2 = (is_array($rs2 = $this->search())) ? $rs2 : array();
			$this->logOut();
			//print_r(array_merge($result1,$result2));
			$final = array_merge($result1,$result2);
		}else{
			$final = $this->search();
			$this->logOut();
		}
		
		if (is_array($final) == false || count($final) == 0) {
			throw new ResultFareNotFound($opt);
		}
		return array_values($final);	
	}
	
	//function preBooking(){
	function preBooking($fare_data){
		//public function doBooking(){
			//$getMeta = $this->doSearch();
			/*$fare_data = array(
				'id'		=>	7323,
				'log_id'	=>	34,
				'company'	=>	'SRIWIJAYA',
				't_depart'	=>	'2011-12-31 10:05',
				't_arrive'	=>	'2011-12-31 10:45',
				'type'		=>	'depart',
				'class'		=>	'T',
				'route'		=>	'CGK,TKG',
				'meta_data'	=>	 '{"company":"SRIWIJAYA","flight_no":"SJ 014","t_depart":"2012-03-21 06:50","t_arrive":"2012-03-21 09:05","type":"depart","class":"W","price":"1602000","route":"CGK,MES","t_transit_arrive":null,"t_transit_depart":null,"log_id":1,"arrayIndex":"5,14","radio_value":"a8535db1-5ecc-4ee5-b78c-b3bf9684e315|6fe6ebff-01af-4a64-b645-ef896350dfab|ed664f2a-24bc-480d-bf16-904617af45ee","time_depart":"2012-3-21","passangers":1,"adult":1,"child":0,"infant":0,"detail":{"0":{"passanger_type":"ADULT","price_per_pax":"1440909","pajak":"144091","iwjr":"5000","extra_cover":"12000"},"price":"1602000"}}',
				't_transit_arrive'	=>	'',
				't_transit_depart'	=>	'',
				'price'				=>	'392000',
				'flight_no'			=>	'SJ 096',
				'log'				=>	array(
					'id'				=>	34,
					'date_depart'		=>	'2012-01-28 00:00:00',
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
			//$route = explode(',',$forBooking['route']);
			$log = element('log',$fare_data);
			$route_from = element('route_from',$log);
			$route_to = element('route_to',$log);


			$this->_opt->route_from 	= $route_from;
			$this->_opt->route_to 		= $route_to;
			$this->_opt->date_depart 	= element('time_depart',$forBooking);
			$this->_opt->date_return 	= NULL;
			$this->_opt->passengers 	= element('passangers',$forBooking);
			$this->_opt->id				= element('log_id',$forBooking);

			//search again

			$reSearch = $this->research();
			for ($i=0; $i < count($reSearch); $i++) { 
				$meta[$i] = json_decode($reSearch[$i]['meta_data'],1);
			}

			$arrayIndex = $this->multidimensional_search($meta,array('arrayIndex' => $forBooking['arrayIndex']));
			if ($arrayIndex=='nothing') {
				$this->logout();
				return false;
			}else{
				$price = $meta[$arrayIndex-1]['price'];
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
	function doBooking($fare_data=array(),$passangers_data=array(),$customer_data=array()){
		//fordebug
		/*$fare_data = array(
			'id'		=>	7323,
			'log_id'	=>	34,
			'company'	=>	'SRIWIJAYA',
			't_depart'	=>	'2012-03-21 09:05',
			't_arrive'	=>	'2012-03-21 11:20',
			'type'		=>	'depart',
			'class'		=>	'Q',
			'route'		=>	'CGK,MES',
			'meta_data'	=>	 '{"comapny":"SRIWIJAYA","flight_no":"SJ 010","t_depart":"2012-03-21 09:05","t_arrive":"2012-03-21 11:20","t_transit_arrive":false,"t_transit_depart":false,"type":"depart","price":"952000","class":"Q","route":"CGK,MES","log_id":34,"arrayIndex":"0,7","passangers":1,"time_depart":"2012-3-21","radio_value":"ade2804b-9211-4ff3-8231-36d63118cf4e|b590c16f-ecc3-4f62-978f-829fdbc50b18|101784f4-1727-45dc-a100-2c999132b279","price_detail":[{"passanger_type":"ADULT","price_per_pax":"850000","pajak":"85000","iwjr":"5000","extra_cover":"12000"}]}',
			't_transit_arrive'	=>	'',
			't_transit_depart'	=>	'',
			'price'				=>	'952000',
			'flight_no'			=>	'SJ 010',
			'log'				=>	array(
				'id'				=>	34,
				'date_depart'		=>	'2012-01-31 00:00:00',
				'date_return'		=>	'',
				'route_from'		=>	'CGK',
				'route_to'			=>	'MES',
				'passangers'		=>	1,
				'comp_include'		=>	'["Sriwijaya","Garuda","Merpati","Batavia","Citilink"]',
				'c_time'			=>	'2011-12-20 11:56:15',
				'max_fare'			=>	5,
				'actor'				=> 'CUS',
			),
		);*/
		
		/*$passangers_data = array(
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
		);*/
		
		
		/*$passangers_data = array(
			'ADULT'	=>	
				array(
					array(
						'title' 			=>	'Mr',
						'name' 				=>	'Zidni Mubarock',
						'no_id'				=>	'3671081902880001',
					),
					array(
						'title' 			=>	'Mrs',
						'name' 				=>	'Zidni Mubarock',
						'no_id'				=>	'3671081902880001',
					),
				),				
				
			'CHILD'	=>	
				array(
					array(
						'title' 			=>	'Mstr',
						'name' 				=>	'Zidni Mubarock',
						'no_id'				=>	'3671081902880001',
					),
				),
			'INFANT'	=>	
				array(
					array(
						'title' 			=>	'Mr',
						'name' 				=>	'Zidni Mubarock',
						'no_id'				=>	'3671081902880001',
					),
				),
		);*/
		
		
		
		/*$customer_data = array(
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
		
		$this->passangers	=	$passangers_data;
		$this->contact = $customer_data;
		$forBooking = json_decode($fare_data['meta_data'],1);
		$route = explode(',',$forBooking['route']);
		$log = element('log',$fare_data);
		$route_from = element('route_from',$log);
		$route_to = element('route_to',$log);
	
		$this->_opt->route_from 	= $route_from;
		$this->_opt->route_to 		= $route_to;
		$this->_opt->date_depart 	= element('time_depart',$forBooking);
		$this->_opt->date_return 	= NULL;
		$this->_opt->passengers 	= element('passangers',$forBooking);
		//$this->_opt->adult	= element('adult',$forBooking);
		//$this->_opt->child	= element('child',$forBooking);
		//$this->_opt->infant = element('infant',$forBooking);
		$this->_opt->id				= element('log_id',$forBooking);
	
	
		$this->fare_id		=	element('id',$fare_data);
		$this->meta_data	=	$forBooking;
	
		$aSearch = $this->forBooking();
					
		for ($i=0; $i < count($aSearch); $i++) { 
			$metafinnal[$i] = json_decode($aSearch[$i]['meta_data'],1);
		}
		$arrayIndex = $this->multidimensional_search($metafinnal,array('arrayIndex' => $forBooking['arrayIndex']));
		$radioValue = explode('|',$metafinnal[$arrayIndex-1]['radio_value']);
		$this->data = array(
			$radioValue[1]	=> $metafinnal[$arrayIndex-1]['radio_value'],
		);
		
		$this->selectSummary(element('2',$radioValue));
		$booking = $this->booking();
		$this->logout();
		if (is_array($booking) == false) {
			throw new BookingFailed($fare_data);
		}
		if (element('final_price',$booking) > element('price',$fare_data)) {
			throw new BookingFarePriceChanged($fare_data, element('final_price',$booking));
		}
		return $booking;
	}
	
	function research(){
		$this->login();
		if ($this->_opt->date_return) {
			$result1 = $this->search();
			
			$temp = '';
			$temp = $this->_opt->route_from;
			$this->_opt->route_from = $this->_opt->route_to;
			$this->_opt->route_to = $temp;
			$this->_opt->date_depart = $this->_opt->date_return;
			$this->roundTrip = true;
			
			$result2 = $this->search();
			$this->logOut();
			//print_r(array_merge($result1,$result2));
			$final = array_merge($result1,$result2);
		}else{
			$final = $this->search();
			$this->logOut();
		}
		return array_values($final);
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

}
