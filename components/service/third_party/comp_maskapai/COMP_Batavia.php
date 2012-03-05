<?
if (! defined('BASEPATH')) exit('No direct script access');

class Batavia extends Comp_maskapai_base {

	private $username = 'jktsystem';
	private $password = 'net256jfa';
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
		$this->_cookies_file 	= "./components/service/third_party/comp_maskapai/cookies/batavia_airline.txt";
		$this->login();
	}
		
		function index() {}
		
		function curl($url,$post_data = array(),$header = array())
		{
			if ($post_data != null) {
				$conf = array(
					'url'				=> $url,
					'timeout'			=> 60,
					'header'			=> 0,
					'followlocation'	=> 1,
					'cookiejar'			=> $this->_cookies_file,
					'cookiefile'		=> $this->_cookies_file,
					'returntransfer'	=> 1,
					'post'				=> true,
					'ssl_verifyhost'	=> 0,
					'referer'			=> $this->_referer_url,
					'SSL_VERIFYPEER'	=> 0,
					'postfields'		=> http_build_query($post_data),
					'useragent'			=> 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:6.0.2) Gecko/20100101 Firefox/6.0.2',
				);
			}else{
				$conf = array(
					'url'				=> $url,
					'timeout'			=> 60,
					'header'			=> 0,
					'cookiejar'			=> $this->_cookies_file,
					'cookiefile'		=> $this->_cookies_file,
					'returntransfer'	=> 1,
					'ssl_verifyhost'	=> 0,
					//'referer'			=> $this->_referer_url,
					'SSL_VERIFYPEER'	=> 0,
					'useragent'			=> 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:6.0.2) Gecko/20100101 Firefox/6.0.2',
				);
			}
			
			if ($header != null) {
				$head = array(
					'httpheader'	=>	$header,
				);
				$conf = array_merge($header,$conf);
			}
			$this->_ci->my_curl->setup($conf);
			$exc = $this->_ci->my_curl->exc();
			$res_info = $this->_ci->my_curl->res_info();
			if ($res_info->http_code == 302) {
				$this->curl($res_info->url,null,null);
			}else if ($res_info->http_code == (400|404|403)) {
				return false;
			}
			$page = str_get_html($exc);
			return $page;
		}

		function convertDayMonth($number){
			if ($number < 10) {
				return str_replace('0','',$number);
			}else{
				return $number;
			}
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
		
		function lastname($array,$startIndex){
			$name = '';
			if (array_key_exists($startIndex,$array)) {
				for ($i=$startIndex; $i < count($array); $i++) { 
					$name .= element($i,$array).' ';
				}
			}
			return $name;
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
				
		function login(){
			$post_data = array(
				'useridmp'		=> $this->username,
				'passwordmp'	=> $this->password,
			);
			
			$url = 'https://222.124.141.100/MyPage/loginproses.php';	
			$this->curl($url,$post_data,null);
		}

		function logout(){
			$url = 'https://222.124.141.100/MyPage/logout.php';
			$this->curl($url,null,null);
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
				'tglBerangkatPergi' 	=> $this->convertDayMonth(element('2',$dateExplode)),
				'blnBerangkatPergi' 	=> $this->convertDayMonth(element('1',$dateExplode)),
				'thnBerangkatPergi' 	=> element('2',$year).element('3',$year),
				'jmlPenumpang' 			=> $this->_opt->adult,
				'jmlInfant'				=> 0,
			);
			
			$url = 'https://222.124.141.100/MyPage/booking/index.php';
			$page = $this->curl($url,$post_data,null);
				
			if (!$page) {return false;}
			$qty = $post_data['jmlPenumpang'];
			if(!$go_wrap = $page->find('div[id=pilihPenerbanganPergi] table tbody tr td table tbody', 0)) return false;
			$date = $page->find('div[id=pilihPenerbanganPergi] table tbody',0)->find('tr',2)->plaintext;
			$cdate = explode(':',$date);
			$fullDate = explode('-',$cdate[1]);
			$month = $this->monthConvert($fullDate[1]);		
			$cnt_flight = count($go_wrap->find('tr'));
			$cnt_class = count($go_wrap->find('tr', 0)->find('td'));
			$data = array();
			$index = 0;

			for($i = 4 ; $i < $cnt_class ; $i ++){
				for($j = 1 ; $j < $cnt_flight ; $j ++  ){				
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
					
					$radioValue = $cell->find('input', 0 )->getAttribute('value');
					
					$this->_opt->radio_value = $radioValue;
					$this->_opt->class = element('0', $head);
					//$passangerscount = $this->_opt->adult + $this->_opt->child + $this->_opt->infant;					
					//$fPrice = ($price+($price*0.1)+5000+5500)*$this->_opt->adult;
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
						'price'				=>	$price*$this->_opt->adult,
						'class'				=>	element('0', $head),
						'route'				=>	$post_data['ruteBerangkat'].$transitLocation.','.$post_data['ruteTujuan'],
						'arrayIndex'		=>	$j.','.$i,
						'passangers'		=>	$this->_opt->adult,
						//'adult'				=>	$this->_opt->adult,
						//'child'				=> 	$this->_opt->child,
						//'infant'			=>	$this->_opt->infant,
						'time_depart'		=>	$year.'-'.$month.'-'.$day,
						'radio_value'		=>	$radioValue,
					);
					
					$data[$j][$index]['company'] 			='BATAVIA';
					$data[$j][$index]['flight_no']			= $cFlightNumber;
					$data[$j][$index]['t_depart']			= date("Y-m-d h:i",$time_depart);
					$data[$j][$index]['t_arrive']			= date("Y-m-d h:i",$time_arrive);
					$data[$j][$index]['t_transit_arrive']	= $t_transit_arive;
					$data[$j][$index]['t_transit_depart']	= $t_transit_depart;
					$data[$j][$index]['type']				= $type;
					$data[$j][$index]['price'] 				= $price*$this->_opt->adult;
					$data[$j][$index]['class']				= element('0', $head);
					$data[$j][$index]['route']				= element('ruteBerangkat',$post_data).','.element('ruteTujuan',$post_data);
					$data[$j][$index]['log_id']				= $this->_opt->id;				
					$data[$j][$index]['meta_data'] 			= json_encode($meta);
					$data[$j][$index]['route_from']			= $this->_opt->route_from;
					$data[$j][$index]['route_to']			= $this->_opt->route_to;
					$data[$j][$index]['date_depart']		= $this->_opt->date_depart;
					$data[$j][$index]['adult']				= $this->_opt->adult;
					$data[$j][$index]['child']				= $this->_opt->child;
					$data[$j][$index]['infant']				= $this->_opt->infant;
					$data[$j][$index]['price_final']		= 0;
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
		
		public function doSearch($opt = array())
		{
			$this->_opt->child			= 0;
			$this->_opt->infant			= 1;
			$this->_opt->route_from 	= 'CGK';
			$this->_opt->route_to 		= 'PLM';
			$this->_opt->date_depart 	= '2012-03-14';
			$this->_opt->date_return 	= '2012-03-17';
			$this->_opt->adult			= 1;
			$this->_opt->id				= 1;
			
			foreach($opt as $key => $val ) $this->_opt->$key = $val;			
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
			if (count($final) == 0 || is_array($final) == false) {
				throw new ResultFareNotFound($opt);
			}
			return array_values($final);
		}

		function _bakup_detail(){
			//$this->_opt->infant = 0;
			
			$date = explode('-',$this->_opt->date_depart);
			$year = str_split($date[0]);
			$post_data = array(
				'flightBerangkatPergi'		=> $this->_opt->radio_value,
				'tglBerangkatPergi'			=> $this->convertDayMonth(element('2',$date)),
				'blnBerangkatPergi'			=> $this->convertDayMonth(element('1',$date)),
				'thnBerangkatPergi'			=> element('2',$year).element('3',$year),
				'classPergi'				=> $this->_opt->class,
				'jmlPenumpang'				=> $this->_opt->adult,
				'jmlInfant'					=> 0,
				'ruteBerangkat'				=> $this->_opt->route_from,
				'ruteTujuan'				=> $this->_opt->route_to,
				'ruteKembali'				=> 'kembali',
			);
			
			$url = 'https://222.124.141.100/MyPage/booking/cekHarga.php';
			$page = $this->curl($url,$post_data,null);
			try {
				
					if (!$page) 
						// fare_was not found ; * todo determine if this fare is actually sold old
						throw new DetailFareNotFound();	
					
					$table = $page->find('div[id=centerright] form[id=cekHarga] table');
					
					if ( count($table) ==0 )
						throw new DetailFareNotFound();	
						
					$ret = $page->find('div[id=centerright] form[id=cekHarga] table',0);
					$ret1 = $page->find('div[id=centerright] form[id=cekHarga] table',1);
					$countData = count($ret->find('tr',2)->find('td'));
					$flight_number = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$ret->find('tr',2)->find('td',5)->plaintext);
					$adult_price_per_pax = str_replace(array(",",'.00 IDR'),'',$ret1->find('tr',2)->find('td',4)->plaintext);
					$pax = str_replace(array(",",'.00 IDR'),'',$ret1->find('tr',2)->find('td',6)->find('div',0)->plaintext);
					$iwjr =  str_replace(array(",",'.00 IDR'),'',$ret1->find('tr',2)->find('td',7)->find('div',0)->plaintext);
					$another_pax = str_replace(array(",",'.00 IDR'),'',$ret1->find('tr',2)->find('td',9)->plaintext);

					$data[0]['passanger_type'] = 'ADULT';
					$data[0]['price_per_pax']  = $adult_price_per_pax;
					$data[0]['pax']			= $pax;
					$data[0]['iwjr']		= $iwjr;
					$data[0]['another_pax'] = $another_pax;

					if (element('jmlInfant',$post_data) == 0) {
						$cleanPrice = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',str_replace(array(",",'.00 IDR'),'',
						$ret1->find('tr',5)->find('td',1)->plaintext));
					}else{
						$infant_price_per_pax = str_replace(array(",",'.00 IDR'),'',
						$ret1->find('tr',6)->find('td',4)->plaintext);

						$pax = str_replace(array(",",'.00 IDR'),'',
						$ret1->find('tr',6)->find('td',6)->find('div',0)->plaintext);

						$iwjr =  str_replace(array(",",'.00 IDR'),'',
						$ret1->find('tr',6)->find('td',7)->find('div',0)->plaintext);

						$another_pax = 0;

						$cleanPrice = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',str_replace(array(",",'.00 IDR'),'',
						$ret1->find('tr',9)->find('td',1)->plaintext));

						$data[1]['passanger_type'] = 'INFANT';
						$data[1]['price_per_pax']  = $infant_price_per_pax;
						$data[1]['pax']			= $pax;
						$data[1]['iwjr']		= $iwjr;
						$data[1]['another_pax'] = $another_pax;
					}
			} catch (Exception $e) {
				
			}
		
			//$data['totalPrice']	= $cleanPrice;
			$metaArray = json_decode(element('meta_data',$this->fare_data),1);
			$meta = array(
				'comapny'			=>	'BATAVIA',
				'flight_no'			=>	$flight_number,
				't_depart'			=>	element('t_depart', $this->fare_data),
				't_arrive'			=>	element('t_arrive',$this->fare_data),
				't_transit_arrive'	=>	element('t_transit_arrive',$this->fare_data),
				't_transit_depart'	=>	element('t_transit_depart',$this->fare_data),
				'type'				=>	element('type',$this->fare_data),
				'price'				=>	$cleanPrice,
				'class'				=>	element('class',$this->fare_data),
				'route'				=>	element('route',$this->fare_data),
				'log_id'			=>	element('log_id',$this->fare_data),
				'arrayIndex'		=>	element('arrayIndex',$metaArray),
				'passangers'		=>	$this->_opt->adult,
				'adult'				=>	$this->_opt->adult,
				'child'				=> 	$this->_opt->child,
				'infant'			=>	$this->_opt->infant,
				'time_depart'		=>	$this->_opt->date_depart,
				'radio_value'		=>	$this->_opt->radio_value,
				'price_detail'		=>	$data,
			);
			
			$fare_data['id'] 				= element('id',$this->fare_data);
			$fare_data['log_id'] 			= element('log_id',$this->fare_data);
			$fare_data['company'] 			= element('company',$this->fare_data);
			$fare_data['flight_no'] 		= $flight_number;
			$fare_data['t_depart'] 			= element('t_depart',$this->fare_data);
			$fare_data['t_arrive'] 			= element('t_arrive',$this->fare_data);
			$fare_data['type'] 				= element('type',$this->fare_data);
			$fare_data['class'] 			= element('class',$this->fare_data);
			$fare_data['route'] 			= element('route',$this->fare_data);
			$fare_data['t_transit_arrive'] 	= element('t_transit_arrive',$this->fare_data);
			$fare_data['t_transit_depart'] 	= element('t_transit_depart',$this->fare_data);
			$fare_data['price'] 			= $cleanPrice;
			$fare_data['meta_data'] 		= json_encode($meta);
			$fare_data['route_from'] 		= element('route_from', $this->fare_data);
			$fare_data['route_to'] 			= element('route_to', $this->fare_data);
			$fare_data['date_depart'] 		= element('date_depart', $this->fare_data);
			$fare_data['adult'] 			= element('adult', $this->fare_data);
			$fare_data['child'] 			= element('child', $this->fare_data);
			$fare_data['infant'] 			= element('infant', $this->fare_data);
			$fare_data['price_final']		= 1;
			return $fare_data;
		}
		public function getDetail($fare_data)
		{
			return $this->detail($fare_data);
		}
		public function detail($fare_data)
		{
				$meta = json_decode(element('meta_data', $fare_data), 1);
				$date = explode('-', element('date_depart', $fare_data));
				$year = str_split($date[0]);
				$post_data = array(
					'flightBerangkatPergi'		=> element('radio_value', $meta),
					'tglBerangkatPergi'			=> $this->convertDayMonth(element('2',$date)),
					'blnBerangkatPergi'			=> $this->convertDayMonth(element('1',$date)),
					'thnBerangkatPergi'			=> element('2',$year).element('3',$year),
					'classPergi'				=> element('class', $fare_data),
					'jmlPenumpang'				=> element('adult', $fare_data) + element('child', $fare_data),
					'jmlInfant'					=> element('child', $fare_data) ,
					'ruteBerangkat'				=> element('route_from', $fare_data),
					'ruteTujuan'				=> element('route_to', $fare_data),
					'ruteKembali'				=> 'kembali',
				);

				$url = 'https://222.124.141.100/MyPage/booking/cekHarga.php';
				$page = $this->curl($url,$post_data,null);
				try {

						if (!$page) 
							// fare_was not found ; * todo determine if this fare is actually sold old
							throw new DetailFareNotFound();	

						$table = $page->find('div[id=centerright] form[id=cekHarga] table');
						if ( count($table) ==0 )

							throw new DetailFareNotFound();	

						$ret = $page->find('div[id=centerright] form[id=cekHarga] table',0);
						$ret1 = $page->find('div[id=centerright] form[id=cekHarga] table',1);
						$countData = count($ret->find('tr',2)->find('td'));
						$flight_number = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$ret->find('tr',2)->find('td',5)->plaintext);
						$adult_price_per_pax = str_replace(array(",",'.00 IDR'),'',$ret1->find('tr',2)->find('td',4)->plaintext);
						$pax = str_replace(array(",",'.00 IDR'),'',$ret1->find('tr',2)->find('td',6)->find('div',0)->plaintext);
						$iwjr =  str_replace(array(",",'.00 IDR'),'',$ret1->find('tr',2)->find('td',7)->find('div',0)->plaintext);
						$another_pax = str_replace(array(",",'.00 IDR'),'',$ret1->find('tr',2)->find('td',9)->plaintext);

						$data[0]['passanger_type'] = 'ADULT';
						$data[0]['price_per_pax']  = $adult_price_per_pax;
						$data[0]['pax']			= $pax;
						$data[0]['iwjr']		= $iwjr;
						$data[0]['another_pax'] = $another_pax;

						if (element('jmlInfant',$post_data) == 0) {
							$cleanPrice = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',str_replace(array(",",'.00 IDR'),'',
							$ret1->find('tr',5)->find('td',1)->plaintext));
						}else{
							$infant_price_per_pax = str_replace(array(",",'.00 IDR'),'',
							$ret1->find('tr',6)->find('td',4)->plaintext);

							$pax = str_replace(array(",",'.00 IDR'),'',
							$ret1->find('tr',6)->find('td',6)->find('div',0)->plaintext);

							$iwjr =  str_replace(array(",",'.00 IDR'),'',
							$ret1->find('tr',6)->find('td',7)->find('div',0)->plaintext);

							$another_pax = 0;

							$cleanPrice = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',str_replace(array(",",'.00 IDR'),'',
							$ret1->find('tr',9)->find('td',1)->plaintext));

							$data[1]['passanger_type'] = 'INFANT';
							$data[1]['price_per_pax']  = $infant_price_per_pax;
							$data[1]['pax']			= $pax;
							$data[1]['iwjr']		= $iwjr;
							$data[1]['another_pax'] = $another_pax;
						}
				} catch (Exception $e) {
					if($e instanceof DetailFareNotFound)
						throw $e;
					else{
						if($debug)
							return false;
						else 
							return $fare_data;
					}
				}

				//$data['totalPrice']	= $cleanPrice;
			//	$metaArray = json_decode(element('meta_data',$fare_data),1);
				$meta = array(
					'comapny'			=>	'BATAVIA',
					'flight_no'			=>	$flight_number,
					't_depart'			=>	element('t_depart', $fare_data),
					't_arrive'			=>	element('t_arrive',$fare_data),
					't_transit_arrive'	=>	element('t_transit_arrive',$fare_data),
					't_transit_depart'	=>	element('t_transit_depart',$fare_data),
					'type'				=>	element('type',$fare_data),
					'price'				=>	$cleanPrice,
					'class'				=>	element('class',$fare_data),
					'route'				=>	element('route',$fare_data),
					'log_id'			=>	element('log_id',$fare_data),
					'arrayIndex'		=>	elemet('arrayIndex', $meta),
					'adult'				=>	element('adult', $fare_data),
					'child'				=> 	element('child', $fare_data),
					'infant'			=>	element('infant', $fare_data),
					'time_depart'		=>	element('date_depart', $fare_data),
					'radio_value'		=>	element('radio_value', $fare_data),
					'price_detail'		=>	json_encode($data),
				);

				$fare_data['id'] 				= element('id',$fare_data);
				$fare_data['log_id'] 			= element('log_id',$fare_data);
				$fare_data['company'] 			= element('company',$fare_data);
				$fare_data['flight_no'] 		= $flight_number;
				$fare_data['t_depart'] 			= element('t_depart',$fare_data);
				$fare_data['t_arrive'] 			= element('t_arrive',$fare_data);
				$fare_data['type'] 				= element('type',$fare_data);
				$fare_data['class'] 			= element('class',$fare_data);
				$fare_data['route'] 			= element('route',$fare_data);
				$fare_data['t_transit_arrive'] 	= element('t_transit_arrive',$fare_data);
				$fare_data['t_transit_depart'] 	= element('t_transit_depart',$fare_data);
				$fare_data['price'] 			= $cleanPrice;
				$fare_data['meta_data'] 		= json_encode($meta);
				$fare_data['route_from'] 		= element('route_from', $fare_data);
				$fare_data['route_to'] 			= element('route_to', $fare_data);
				$fare_data['date_depart'] 		= element('date_depart', $fare_data);
				$fare_data['adult'] 			= element('adult', $fare_data);
				$fare_data['child'] 			= element('child', $fare_data);
				$fare_data['infant'] 			= element('infant', $fare_data);
				$fare_data['final_price']		= 1;
				return $fare_data;
		}
		public function _backup_getDetail($fare_data = array())
		{
			/*$fare_data = array(
				'id'		=>	7323,
				'log_id'	=>	34,
				'company'	=>	'BATAVIA',
				't_depart'	=>	'2012-03-14 01:00',
				't_arrive'	=>	'2012-03-14 02:00',
				'type'		=>	'depart',
				'class'		=>	'E',
				'route'		=>	'CGK,PLM',
				'meta_data'	=>	 '{"comapny":"BATAVIA","flight_no":"515","t_depart":"2012-03-14 01:00","t_arrive":"2012-03-14 02:00","t_transit_arrive":null,"t_transit_depart":null,"type":"depart","price":231000,"class":"E","route":"CGK,PLM","log_id":1,"arrayIndex":"1,4","passangers":1,"time_depart":"2012-3-14","radio_value":"29744808"}',
				't_transit_arrive'	=>	'',
				't_transit_depart'	=>	'',
				'price'				=>	'687800',
				'flight_no'			=>	'515',
				'log'				=>	array(
					'id'				=>	34,
					'date_depart'		=>	'2012-01-28 00:00:00',
					'date_return'		=>	'',
					'route_from'		=>	'CGK',
					'route_to'			=>	'PLM',
					'passangers'		=>	1,
					'comp_include'		=>	'["Sriwijaya","Garuda","Merpati","Batavia","Citilink"]',
					'c_time'			=>	'2011-12-20 11:56:15',
					'max_fare'			=>	5,
					'actor'				=> 'CUS',
				),
			);*/
			
		//	$log = element('log',$fare_data);
			$meta = json_decode(element('meta_data',$fare_data),1);
			$this->fare_data = $fare_data;
			
			$this->_opt->radio_value  	= element('radio_value',$meta);
			$this->_opt->class 		  	= element('class',$meta);
 			$this->_opt->adult 			= element('passangers',$meta);
			$this->_opt->route_from 	= element('route_from',$fare_data);
			$this->_opt->route_to 		= element('route_to',$fare_data);
			$this->_opt->date_depart 	= element('time_depart',$fare_data);	
			$this->_opt->child 			= element('child', $fare_data);
			$this->_opt->infant			= element('infant', $fare_data);		
			$final = $this->detail();
			$this->logout();
			
			if (is_array($final) == false || count($final)==0) {
				return $this;
			}
			return $final;
		}
		 
		
		function booking(){		
			$dataPassanger = array();
			/*$ip = 1;
			$adultCount = 0;
			$childCount = 0;
			$infantCount = 0;
			
			if ($adult = element('ADULT',$this->passangers)) {
				$adultCount = count($adult);
				foreach ($adult as $value) {					
					$name = explode(' ',element('name',$value));
					$dataPassanger['title'.$ip] = $value['title'].'.';
					$dataPassanger['tfPaxdepan'.$ip] = element('0',$name);
					$dataPassanger['tfPaxbelakang'.$ip] = $this->lastname($name,1);
					$dataPassanger['noID'.$ip] = $value['no_id'];
					$ip++;
				}
				
			}
			
			if ($child = element('CHILD',$this->passangers)) {
				$childCount = count($child);
				foreach ($child as $value) {
					$name = explode(' ',element('name',$value));
					$dataPassanger['title'.$ip] = $value['title'].'.';
					$dataPassanger['tfPaxdepan'.$ip] = element('0',$name);
					$dataPassanger['tfPaxbelakang'.$ip] = $this->lastname($name,1);
					$dataPassanger['noID'.$ip] = $value['no_id'];
					$ip++;
				}
				
			}
			
			if ($infant = element('INFANT',$this->passangers)) {
				$infantCount = count($infant);
				foreach ($child as $value) {
					$name = explode(' ',element('name',$value));
					$dataPassanger['tfInfantdepan'.$ip] = element('0',$name);
					$dataPassanger['tfInfantbelakang'.$ip] = $this->lastname($name,1);
					$dataPassanger['tfReferensi'.$ip] = 1;
					$ip++;
				}
				
			}*/
			$ip = 1;
			foreach ($this->passangers as $key => $value){
				$name = explode(' ',element('name',$value));				
				$dataPassanger['title'.$ip] = $value['title'].'.';
				$dataPassanger['tfPaxdepan'.$ip] = element('0',$name);
				$dataPassanger['tfPaxbelakang'.$ip] = $this->lastname($name,1);
				$dataPassanger['noID'.$ip] = $value['no_id'];
				$ip++;
			}
					
			$dataContact = array(
				'receivedF'			=>	element('f_name',$this->user).' '.element('l_name',$this->user),
				'telp_pax'			=>	$this->user['user_detail']['mobile'],
			);
			
			$currencyData = array(
				'defaultCurr'		=>	'YES',
				'curr'				=>	'IDR',
			);		
			
			$date = explode('-',$this->_opt->date_depart);
			$year = str_split(element('0',$date));
			
			$flightRouteInfoData = array(
				'flightBerangkat'	=>	$this->_opt->radio_value, //value dari radio
				'flightKembali'		=>	'',
				'jmlPenumpang'		=>	$adultCount+$childCount,
				'jmlInfant'			=>	$infantCount,
				'childInt'			=>	'',
				'tglBerangkat'		=>	$this->convertDayMonth($date[2]),
				'blnBerangkat'		=>	$this->convertDayMonth($date[1]),
				'thnBerangkat'		=>	element('2',$year).element('3',$year),
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
			
			$url = 'https://222.124.141.100/MyPage/booking/cekHarga.php';
			$page = $this->curl($url,$post_data,null);
			
			if (!$page) {return false;}
			$table = $page->find('div[id=centerright] table tbody tr td table tbody');
			$cntTable = count($table);
			if ($cntTable == 0) {return false;}
			$bookingData = $table[0];
			$passangerData = $table[1];
			$flightData = $table[2];
			$detailData = $table[3];
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
			
			$cleanPrice = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',str_replace(array(",",'.00 IDR'),'',
			$detailData->find('tr',5)->find('td',1)->find('div',0)->plaintext));
						
			$data['booking_number'] = $booking_id;
			$data['fare_id']		= $this->fare_id;
			$data['meta_data']		= json_encode($this->meta_data);
			$data['passangers']		= $this->passangers;
			$data['final_price']	= $cleanPrice;
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
				't_depart'	=>	'2012-03-14 01:00',
				't_arrive'	=>	'2012-03-14 02:00',
				'type'		=>	'depart',
				'class'		=>	'E',
				'route'		=>	'CGK,PLM',
				'meta_data'	=>	 '{"comapny":"BATAVIA","flight_no":"515","t_depart":"2012-03-14 01:00","t_arrive":"2012-03-14 02:00","t_transit_arrive":null,"t_transit_depart":null,"type":"depart","price":231000,"class":"E","route":"CGK,PLM","log_id":1,"arrayIndex":"1,4","passangers":1,"time_depart":"2012-3-14","radio_value":"29744808"}',
				't_transit_arrive'	=>	'',
				't_transit_depart'	=>	'',
				'price'				=>	'687800',
				'flight_no'			=>	'515',
				'log'				=>	array(
					'id'				=>	34,
					'date_depart'		=>	'2012-01-28 00:00:00',
					'date_return'		=>	'',
					'route_from'		=>	'CGK',
					'route_to'			=>	'PLM',
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
			$route_to	= element('route_to',$log);
			$date_depart = element('time_depart',$forBooking);
			$adult		 = element('adult',$forBooking);
			$child		 = element('child',$forBooking);
			$infant		 = element('infant',$forBoking);
						
			$opt = array(
				'route_from' 	=> $route_from,
				'route_to' 		=> $route_to,
				'date_depart' 	=> $date_depart,
				'date_return' 	=> NULL,
				'adult'		 	=> $adult,
				'child'			=> $child,
				'infant'		=> $infant,
				'id'			=> element('log_id',$forBooking),
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
		
		function doBooking($fare_data = array(),$passangers_data = array(),$customer_data = array()){
			/*$fare_data = array(
				'id'		=>	7323,
				'log_id'	=>	34,
				'company'	=>	'BATAVIA',
				't_depart'	=>	'2012-03-14 01:00',
				't_arrive'	=>	'2012-03-14 02:00',
				'type'		=>	'depart',
				'class'		=>	'E',
				'route'		=>	'CGK,PLM',
				'meta_data'	=>	 '{"comapny":"BATAVIA","flight_no":"515","t_depart":"2012-03-14 01:00","t_arrive":"2012-03-14 02:00","t_transit_arrive":null,"t_transit_depart":null,"type":"depart","price":231000,"class":"E","route":"CGK,PLM","log_id":1,"arrayIndex":"1,4","passangers":1,"time_depart":"2012-3-14","radio_value":"29744808"}',
				't_transit_arrive'	=>	'',
				't_transit_depart'	=>	'',
				'price'				=>	'687800',
				'flight_no'			=>	'515',
				'log'				=>	array(
					'id'				=>	34,
					'date_depart'		=>	'2012-01-28 00:00:00',
					'date_return'		=>	'',
					'route_from'		=>	'CGK',
					'route_to'			=>	'PLM',
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
										'name' 				=>	'asdd adasdasdas',
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
			$this->passangers = $passangers_data;
			$this->user	= $customer_data;		
										
			$this->login();
			$forBooking = json_decode($fare_data['meta_data'],1);
			$log = element('log',$fare_data);
			$route_from = element('route_from',$log);
			$route_to	= element('route_to',$log);
			$date_depart = element('time_depart',$forBooking);
			//$adult		 = element('adult',$forBooking);
			//$child		 = element('child',$forBooking);
			//$infant		 = element('infant',$forBoking);
			$this->_opt->adult = count($passangers_data);

			$this->fare_id = element('id',$fare_data);
			$this->meta_data = $forBooking;
			
			$this->_opt->radio_value		= element('radio_value',$forBooking);
			$this->_opt->route_from 		= $route_from;
			$this->_opt->route_to 			= $route_to;
			$this->_opt->date_depart 		= $date_depart;
			$this->_opt->class 				= element('class',$forBooking);
			//$this->_opt->passangerscount	= count($this->passangers);
			$flightDetail = $this->detail();
			$this->_opt->no_penerbangan		= element('flight_no',$flightDetail);
			$book = $this->booking();
			$this->closing();
			if (is_array($book) == false) {
				throw new BookingFailed($fare_data);
			}
			
			if (element('final_price',$book) > element('price',$forBooking)) {
				throw new BookingFarePriceChanged($fare_data, element('final_price',$booking));
			}
			return $book;
		}

		
	}
