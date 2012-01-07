<?
if (! defined('BASEPATH')) exit('No direct script access');

class Batavia extends Comp_maskapai_base {

	private $username = 'jkttravel';
	private $password = '12345';
	private $_login_url = 'https://222.124.141.100/MyPage/loginproses.php';
	private $_referer_url = 'https://222.124.141.100/MyPage/logout.php';
	private $_search_url = 'https://222.124.141.100/MyPage/booking/index.php';
	private $_detail_url = 'https://222.124.141.100/MyPage/booking/cekHarga.php';
	private $_start_url = 'https://222.124.141.100/MyPage/login.php';
	private $_booking_url = 'https://222.124.141.100/MyPage/booking/process_booking_me.php';
	
	var $route_from;
	var $route_to;
	var $date_depart;
	var $date_return;
	

	//php 5 constructor
	function __construct() {
		parent::__construct();
		$this->roundTrip = false;
	
		$this->_ci->load->library('my_curl');
		$this->_cookies_file 	= "./components/partner/third_party/comp_maskapai/cookies/batavia_airline.txt";
		$this->login();
	}
		
		function index() {}

		function convertDayMonth($number){
			if ($number < 10) {
				return str_replace('0','',$number);
			}else{
				return $number;
			}
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
		
		function login(){
			$post_data = array(
				'useridmp'		=> $this->username,
				'passwordmp'	=> $this->password,
			);

			$conf = array(
				'url'				=> $this->_login_url,
				'timeout'			=> 30,
				'header'			=> 0,
				'followlocation'	=> 1,
				'cookiejar'			=> $this->_cookies_file,
				'cookiefile'		=> $this->_cookies_file,
				'returntransfer'	=> 1,
				'post'				=> true,
				'referer'			=> $this->_referer_url,
				'ssl_verifyhost'	=> 0,
				'SSL_VERIFYPEER'	=> 0,
				'postfields'		=> http_build_query($post_data),
				'useragent'			=> 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:6.0.2) Gecko/20100101 Firefox/6.0.2',
			);

			$this->_ci->my_curl->setup($conf);
			$this->_ci->my_curl->exc();
			$res_info = $this->_ci->my_curl->res_info();
			$this->topage($res_info->url, false);
		}

		function logout(){
			$conf = array(
				'url'				=> $this->_referer_url,
				'timeout'			=> 30,
				'header'			=> 0,
				'followlocation'	=> 1,
				'cookiejar'			=> $this->_cookies_file,
				'cookiefile'		=> $this->_cookies_file,
				'returntransfer'	=> 1,
				'referer'			=> $this->_referer_url,
				'ssl_verifyhost'	=> 0,
				'SSL_VERIFYPEER'	=> 0,
				'useragent'			=> 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:6.0.2) Gecko/20100101 Firefox/6.0.2',
			);
			$this->_ci->my_curl->setup($conf);
			$this->_ci->my_curl->exc();
			$res_info = $this->_ci->my_curl->res_info();
			if ($res_info->http_code == 301 || 301) {
				$fin = $this->topage($res_info->url, false);
			}
		}
		
		function closing(){
			$this->logout();
		}

		function search(){
			$date_return = $this->_opt->date_return;
			$dateExplode = explode('-',$this->_opt->date_depart);
			$this->_ci->load->helper('array');
			$year = str_split($dateExplode[0]);
				$post_data = array(
					'ruteBerangkat' 		=> $this->_opt->route_from,
					'ruteTujuan' 			=> $this->_opt->route_to,
					'ruteKembali' 			=> 'kembali',
					'tglBerangkatPergi' 	=> $this->convertDayMonth($dateExplode[2]),
					'blnBerangkatPergi' 	=> $this->convertDayMonth($dateExplode[1]),
					'thnBerangkatPergi' 	=> $year[2].$year[3],
					'jmlPenumpang' 			=> $this->_opt->passengers,
					'jmlInfant'				=> 0,
				);

				$conf = array(
					'url'				=> $this->_search_url,
					'timeout'			=> 60,
					'header'			=> 0,
					'followlocation'	=> 1,
					'cookiejar'			=> $this->_cookies_file,
					'cookiefile'		=> $this->_cookies_file,
					'returntransfer'	=> 1,
					'post'				=> true,
					'referer'			=> $this->_referer_url,
					'ssl_verifyhost'	=> 0,
					'SSL_VERIFYPEER'	=> 0,
					'postfields'		=> http_build_query($post_data),
					'useragent'			=> 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:6.0.2) Gecko/20100101 Firefox/6.0.2',
				);
				$this->_ci->my_curl->setup($conf);
				$html = $this->_ci->my_curl->exc();
				$page = str_get_html($html);
				if (!$page) {return array();}
				$qty = $post_data['jmlPenumpang'];
				if(!$go_wrap = $page->find('div[id=pilihPenerbanganPergi] table tbody tr td table tbody', 0)) return array();
				$date = $page->find('div[id=pilihPenerbanganPergi] table tbody',0)->find('tr',2)->plaintext;
				$cdate = explode(':',$date);
				$fullDate = explode('-',$cdate[1]);
				$month = $this->monthConvert($fullDate[1]);		
				$cnt_flight = count($go_wrap->find('tr'));
				$cnt_class = count($go_wrap->find('tr', 0)->find('td'));
				$data = array();
				$index = 0;

				for($i = 4 ; $i < $cnt_class ; $i ++)
				{

					for($j = 1 ; $j < $cnt_flight ; $j ++  )
					{				
						$cell = $go_wrap->find('tr', $j)->find('td', $i);

						if($cell->find('input', 0 )->getAttribute('disabled') == 'disabled') { 	continue;}
						$head = $go_wrap->find('tr', 0)->find('td', $i)->plaintext;
						$head = preg_replace('/\s+/', ' ',str_replace('&nbsp;' ,' ', trim($head , ' &nbsp;. ')));
						$head = explode(' ', trim($head) );

						$dep = explode(' ', str_replace('&nbsp;', '', $go_wrap->find('tr', $j)->find('td', 1)->plaintext));
						$dep = element('0', $dep);
						$arr = explode(' ', str_replace('&nbsp;', '', $go_wrap->find('tr', $j)->find('td', 2)->plaintext));
						$arr = element('0', $arr);
						$price = element('2', $head).'000';

						$timeDep = str_replace('.',':',$dep);
						$timeArr = str_replace('.',':',$arr);
						$year = preg_replace('/\s+/', '',$fullDate[2]);
						$day = 	preg_replace('/\s+/', '',$fullDate[0]);
						$t_arrive = $year.'-'.$month.'-'.$day.' '.$timeArr;	
						$t_depart = $year.'-'.$month.'-'.$day.' '.$timeDep;
						$flight_number = $go_wrap->find('tr',$j)->find('td',0)->plaintext;

						if ($this->roundTrip) {
							$type = 'return';				
						}else{
							$type = 'depart';
						}
						$tl = $go_wrap->find('tr',$j)->find('td',3)->find('div',0)->plaintext;
						$transit_location = explode('-',$tl);
						if (count($transit_location) > 1) {

						}
						if ($tl != '-') {
							$t_transit_arive 	= NULL;
							$t_transit_depart 	= NULL;
							$transitLocation	= ','.$tl;
						}else{
							$t_transit_arive 	= NULL;
							$t_transit_depart	= NULL;
							$transitLocation	= '';
						}	
						
						$fPrice = ($price+($price*0.1)+5000+5500)*$this->_opt->passengers;
						$cFlightNumber =  str_replace('&nbsp;',"",$flight_number);
						$time_depart = strtotime($t_depart);
						$time_arrive = strtotime($t_arrive);
						//$time_transit_arrive = strtotime($t_transit_arive);
						//$time_transit_depart = strtotime($t_transit_depart);						
						$meta = array(
							'comapny'			=>	'BATAVIA',
							'flight_no'			=>	$cFlightNumber,
							't_depart'			=>	date("Y-m-d h:i",$time_depart),
							't_arrive'			=>	date("Y-m-d h:i",$time_arrive),
							't_transit_arrive'	=>	$t_transit_arive,
							't_transit_depart'	=>	$t_transit_depart,
							'type'				=>	$type,
							'price'				=>	$fPrice,
							'class'				=>	element('0', $head),
							'route'				=>	$post_data['ruteBerangkat'].$transitLocation.','.$post_data['ruteTujuan'],
							'log_id'				=>	$this->_opt->id,
							'arrayIndex'		=>	$j.','.$i,
							'passangers'			=>	$this->_opt->passengers,
							'time_depart'		=>	$year.'-'.$month.'-'.$day,
							'radio_value'		=>	$cell->find('input', 0 )->getAttribute('value'),
						);
						
						$data[$j][$index]['company'] 			='BATAVIA';
						$data[$j][$index]['flight_no']			= $cFlightNumber ;
						$data[$j][$index]['t_depart']			= date("Y-m-d h:i",$time_depart);
						$data[$j][$index]['t_arrive']			= date("Y-m-d h:i",$time_arrive);
						$data[$j][$index]['t_transit_arrive']	= $t_transit_arive;
						$data[$j][$index]['t_transit_depart']	= $t_transit_depart;
						$data[$j][$index]['type']				= $type;
						$data[$j][$index]['price'] 				= $fPrice;
						$data[$j][$index]['class']				= element('0', $head);
						$data[$j][$index]['route']				= $post_data['ruteBerangkat'].','.$post_data['ruteTujuan'].$transitLocation;
						$data[$j][$index]['log_id']					= $this->_opt->id;				
						$data[$j][$index]['route']				= $post_data['ruteBerangkat'].$transitLocation.','.$post_data['ruteTujuan'];
						$data[$j][$index]['meta_data'] 			= json_encode($meta);
						$index ++;
					}

				}
				$data_dep = $data;
				$finnal = array();

				foreach ($data_dep as $dta => $item) {
					foreach ($item as $fare) {
						$final[$i]	=	$fare;
						$i++;
					}
				}
				
				return $final;
		}

		function detail(){
			/*$this->_opt->radio_value		= '25173255';
			$this->_opt->route_from 		= 'CGK';
			$this->_opt->route_to 			= 'DPS';
			$this->_opt->date_depart 		= '2011-5-03';
			$this->_opt->class 				= 'N';
			$this->_opt->passangerscount	= 2;*/
						
			$date = explode('-',$this->_opt->date_depart);
			$year = str_split($date[0]);
			
			$post_data = array(
				'flightBerangkatPergi'		=> $this->_opt->radio_value,
				'tglBerangkatPergi'			=> $this->convertDayMonth($date[2]),
				'blnBerangkatPergi'			=> $this->convertDayMonth($date[1]),
				'thnBerangkatPergi'			=> $year[2].$year[3],
				'classPergi'				=> $this->_opt->class,
				'jmlPenumpang'				=> $this->_opt->passangerscount,
				'jmlInfant'					=> 0,
				'ruteBerangkat'				=> $this->_opt->route_from,
				'ruteTujuan'				=> $this->_opt->route_to,
				'ruteKembali'				=> 'kembali',
			);
						
			$conf = array(
				'url'				=> $this->_detail_url,
				'timeout'			=> 30,
				'header'			=> 0,
				'followlocation'	=> 1,
				'cookiejar'			=> $this->_cookies_file,
				'cookiefile'		=> $this->_cookies_file,
				'returntransfer'	=> 1,
				'post'				=> true,
				'referer'			=> $this->_referer_url,
				'ssl_verifyhost'	=> 0,
				'SSL_VERIFYPEER'	=> 0,
				'postfields'		=> http_build_query($post_data),
				'useragent'			=> 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:6.0.2) Gecko/20100101 Firefox/6.0.2',
			);

			$this->_ci->my_curl->setup($conf);
			$html = $this->_ci->my_curl->exc();
			
			$page = str_get_html($html);
			if (!$page) {return array();}
			$table = $page->find('div[id=centerright] form[id=cekHarga] table');
			if (count($table)==0) {return array();}
			$ret = $page->find('div[id=centerright] form[id=cekHarga] table',0);
			$ret1 = $page->find('div[id=centerright] form[id=cekHarga] table',1);
			$countData = count($ret->find('tr',2)->find('td'));
			$fp = explode(',',$ret1->find('tr',5)->find('td',1)->plaintext);
			$mp = str_replace('.00 IDR','',$fp);
			//$cp = explode(',',$mp);
			if (count($mp) > 2) {
				$cleanPrice = $mp[0].$mp[1].$mp[2];
			}else {
				$cleanPrice = $mp[0].$mp[1];
			}		
			$data['perjalanan'] = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$ret->find('tr',2)->find('td',0)->plaintext);
			$data['tanggal'] = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$ret->find('tr',2)->find('td',1)->plaintext);
			$data['Hari'] = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$ret->find('tr',2)->find('td',2)->plaintext);
			$data['transit'] = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$ret->find('tr',2)->find('td',3)->plaintext);
			$data['rute'] = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$ret->find('tr',2)->find('td',4)->plaintext);
			$data['noPenerbangan'] = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$ret->find('tr',2)->find('td',5)->plaintext);
			$data['keterangan'] = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$ret->find('tr',2)->find('td',6)->plaintext);
			$data['price'] = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$ret1->find('tr',2)->find('td',4)->plaintext);
			$data['class']	= $post_data['classPergi'];
			$data['totalPrice']	= $cleanPrice;
			$data['maskapai'] = 'Batavia';
			
			return $data;

		}

		function monthConvert($month){
			$month_number = "";
			for($i=1;$i<=12;$i++){ 
				if(date("F", mktime(0, 0, 0, $i, 1, 0)) == $month){ 
					$month_number = $i; 
					break; 
				} 
			}
			return $month_number;
		}

		// API REQUIREMENT

		public function doSearch($opt = array())
		//public function doSearch()
		{
			$this->_opt->route_from 	= 'CGK';
			$this->_opt->route_to 		= 'DPS';
			$this->_opt->date_depart 	= '2012-01-31';
			$this->_opt->date_return 	= NULL;
			$this->_opt->passengers 	= 2;
			$this->_opt->id				= 1;
			
			//foreach($opt as $key => $val ) $this->_opt->$key = $val;
						
			if ($this->_opt->date_return) {
				$result1 = $this->search();
				
				$temp = '';
				$temp = $this->_opt->route_from;
				$this->_opt->route_from = $this->_opt->route_to;
				$this->_opt->route_to = $temp;
				$this->_opt->date_depart = $this->_opt->date_return;				
				$this->roundTrip = true;
				
				$result2 = $this->search();
				$final = array_merge($result1,$result2);
			}else{
				$final =  $this->search();
			}
			$this->closing();
			return array_values($final);
		}
		
		function _booking(){		
			$dataPassanger = array();
			$ip = 1;
			foreach ($this->passangers as $key => $value){
				$name = explode(' ',$value['name']);
				if (count($name) > 1) {
					$namaDepan = $name[0];
					$namaBelakang = $name[1];
				}elseif (count($name) < 2){
					$namaDepan = $name[0];
					$namaBelakang = '';
				}
				
				$dataPassanger['title'.$ip] = $value['title'].'.';
				$dataPassanger['tfPaxdepan'.$ip] = $namaDepan;
				$dataPassanger['tfPaxbelakang'.$ip] = $namaBelakang;
				$dataPassanger['noID'.$ip] = $value['no_id'];
				$ip++;
			}
			
			$dataContact = array(
				'receivedF'			=>	$this->user['f_name'].' '.$this->user['l_name'],
				'telp_pax'			=>	$this->user['user_detail']['mobile'],
			);
			
			$currencyData = array(
				'defaultCurr'		=>	'YES',
				'curr'				=>	'IDR',
			);		
			
			$date = explode('-',$this->_opt->date_depart);
			$year = str_split($date[0]);
			
			$flightRouteInfoData = array(
				'flightBerangkat'	=>	$this->_opt->radio_value, //value dari radio
				'flightKembali'		=>	'',
				'jmlPenumpang'		=>	count($this->passangers),
				'jmlInfant'			=>	0,
				'childInt'			=>	'',
				'tglBerangkat'		=>	$this->convertDayMonth($date[2]),
				'blnBerangkat'		=>	$this->convertDayMonth($date[1]),
				'thnBerangkat'		=>	$year[2].$year[3],
				'ruteBerangkat'		=>	$this->_opt->route_from,
				'ruteTujuan'		=>	$this->_opt->route_to,
				'ruteKembali'		=>	'kembali',
			);
			
			
			//ga perlu masuk detail, cukup tau no penerbangan setelah Y6-XXX
			$maskapaiInfo = array(
				'penerbangan'		=>	$this->_opt->no_penerbangan.','.$this->_opt->radio_value,//'Y6-735,14574584',
				'opsi'				=>	$this->_opt->no_penerbangan,//'Y6-735',
				'kelas'				=>	$this->_opt->class,
			);

			//not needed

			
			$post_data = array_merge($dataPassanger,$dataContact,$flightRouteInfoData,$currencyData,$maskapaiInfo);
			$conf = array(
				'url'				=> $this->_booking_url,
				'timeout'			=> 30,
				'header'			=> 0,
				'followlocation'	=> 1,
				'cookiejar'			=> $this->_cookies_file,
				'cookiefile'		=> $this->_cookies_file,
				'returntransfer'	=> 1,
				'post'				=> true,
				'referer'			=> $this->_referer_url,
				'ssl_verifyhost'	=> 0,
				'SSL_VERIFYPEER'	=> 0,
				'postfields'		=> http_build_query($post_data),
				'useragent'			=> 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:6.0.2) Gecko/20100101 Firefox/6.0.2',
			);
			
			$this->_ci->my_curl->setup($conf);
		 	$exc = $this->_ci->my_curl->exc();
			$res_info = $this->_ci->my_curl->res_info();
			$this->topage($res_info->url, false);
			return $exc;
			
		}
		
		function booking(){
			$page = str_get_html($this->_booking());
			//$page = file_get_html("./components/partner/third_party/comp_maskapai/ojankillbooking_data/HTML/batavia/Booking.html");
			if (!$page) {return array();}
			$table = $page->find('div[id=centerright] table tbody tr td table tbody');
			$cntTable = count($table);
			if ($cntTable == 0) {return array();}
			$bookingData = $table[0];
			$passangerData = $table[1];
			$flightData = $table[2];
			$data = array();
			//booking data
			
			$booking_id = $bookingData->find('tr',1)->find('td',1)->plaintext;
			$limitDirty = str_replace('Hari:','',$bookingData->find('tr',2)->find('td',1)->plaintext);
			$limitRemoveDay = explode(',',$limitDirty);
			$limitDateTime = explode('.',$limitRemoveDay[1]);
			$limitDate = explode('-',$limitDateTime[0]);
			$limitTime  = str_replace('Jam:','',$limitDateTime[1]);
			$agent = $bookingData->find('tr',3)->find('td',1)->plaintext;
			$flightNumber = $flightData->find('tr',2)->find('td',1)->plaintext;
			$class = $flightData->find('tr',2)->find('td',2)->plaintext;
			$dateDirty = $flightData->find('tr',2)->find('td',3)->plaintext;
			$date = explode('-',$dateDirty);
			$routeDepart = $flightData->find('tr',2)->find('td',4)->plaintext;
			$routeArr	 = $flightData->find('tr',3)->find('td',0)->plaintext;
			$limit = $limitDate[0].'-'.$this->monthConvert($limitDate[1]).'-'.$limitDate['2'].' '.$limitTime;			
			$data['booking_number'] = $booking_id;
			$data['fare_id']		= $this->fare_id;
			$data['meta_data']		= json_encode($this->meta_data);
			$data['passangers']		= $this->passangers;
			$data['final_price']	= $this->meta_data['price'];
			//$data['limit'] = $limit;
			//$data['agent'] = $agent;
			//$data['flightNumber'] = $flightNumber;
			//$data['class'] = $class;
			//$data['date'] = $date[0].'-'.$this->monthConvert($date[1]).'-'.$date[2];
			//$data['routeDepart'] = $routeDepart;
			//$data['routeArr']	= $routeArr;
			$countPassangerData = count($passangerData->find('tr'));
			$ip = 1;
			for ($i=2; $i < $countPassangerData; $i++) { 
				$dataPenumpang = $passangerData->find('tr',$i)->find('td',1)->plaintext;
				//$data['penumpang '.$ip] = $dataPenumpang;
				$ip++;
			}
			return $data;
			
		}
				
		//function preBooking(){
		public function preBooking($fare_data){			
			/*$fare_data = array(
				'id'		=>	7323,
				'log_id'	=>	34,
				'company'	=>	'BATAVIA',
				't_depart'	=>	'2012-01-28 01:00:00',
				't_arrive'	=>	'2012-01-28 03:40:00',
				'type'		=>	'depart',
				'class'		=>	'Z',
				'route'		=>	'CGK,DPS',
				'meta_data'	=>	 '{"comapny":"BATAVIA","flight_no":"743","t_depart":"2012-01-28 01:00","t_arrive":"2012-01-28 03:40","t_transit_arrive":null,"t_transit_depart":null,"type":"depart","price":436200,"class":"Z","route":"CGK,DPS","log_id":34,"arrayIndex":"2,5","passanger":1,"time_depart":"2012-01-28","radio_value":"25171533"}',
				't_transit_arrive'	=>	'',
				't_transit_depart'	=>	'',
				'price'				=>	'436200',
				'flight_no'			=>	'743',
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
			$route = explode(',',$forBooking['route']);
			$route_from = $route[0];
			$route_to	= $route[count($route)-1];
			$date_depart = $forBooking['time_depart'];
			$passanger	 = $forBooking['passanger'];
						
			$opt = array(
				'route_from' 	=> $route_from,
				'route_to' 		=> $route_to,
				'date_depart' 	=> $date_depart,
				'date_return' 	=> NULL,
				'passengers' 	=> $passanger,
				'id'			=> $forBooking['log_id'],
			);
			$getMeta = $this->doSearch($opt);
			
			for ($i=0; $i < count($getMeta); $i++) { 
				$meta[$i] = json_decode($getMeta[$i]['meta_data'],1);
			}
			
			$arrayIndex = $this->multidimensional_search($meta,array('arrayIndex'=> $forBooking['arrayIndex']));
			
			if ($arrayIndex == 'nothing') {
				return false;
			}else{
				$price = $meta[$arrayIndex-1]['price'];
				if ($price > $forBooking['price']) {
					return $price;
				}else{
					return true;
				}
			}
		}
		
		function doBooking($fare_data,$passangers_data,$customer_data){
/*		function doBooking(){
			$fare_data = array(
				'id'		=>	7323,
				'log_id'	=>	34,
				'company'	=>	'BATAVIA',
				't_depart'	=>	'2012-12-31 01:00:00',
				't_arrive'	=>	'2012-12-31 03:40:00',
				'type'		=>	'depart',
				'class'		=>	'R',
				'route'		=>	'CGK,DPS',
				'meta_data'	=>	 '{"comapny":"BATAVIA","flight_no":"515","t_depart":"2011-12-31 01:00","t_arrive":"2011-12-31 02:05","t_transit_arrive":null,"t_transit_depart":null,"type":"depart","price":872400,"class":"X","route":"CGK,PLM","log_id":1,"arrayIndex":"1,9","passangers":2,"time_depart":"2011-12-31","radio_value":"13898970"}',
				't_transit_arrive'	=>	'',
				't_transit_depart'	=>	'',
				'price'				=>	'615500',
				'flight_no'			=>	'743',
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
				
			);
			*/
				$this->login();
				$forBooking = json_decode($fare_data['meta_data'],1);
				$route = explode(',',$forBooking['route']);
				$route_from = $route[0];
				$route_to	= $route[count($route)-1];
				$date_depart = $forBooking['time_depart'];
				$passanger	 = $forBooking['passangers'];
				$this->passangers = $passangers_data;
				$this->user	= $customer_data;
				$this->fare_id = $fare_data['id'];
				$this->meta_data = $forBooking;
				
				$this->_opt->radio_value		= $forBooking['radio_value'];
				$this->_opt->route_from 		= $route_from;
				$this->_opt->route_to 			= $route_to;
				$this->_opt->date_depart 		= $date_depart;
				$this->_opt->class 				= $forBooking['class'];
				$this->_opt->passangerscount	= count($this->passangers);
				$flightDetail = $this->detail();
				$this->_opt->no_penerbangan		= $flightDetail['noPenerbangan'];
				$book = $this->booking();
				$this->closing();
				return $book;
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
