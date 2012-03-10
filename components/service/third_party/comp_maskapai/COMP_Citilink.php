<?

if (! defined('BASEPATH')) exit('No direct script access');

class Citilink extends Comp_maskapai_base {

	//php 5 constructor
	private $_refer = 'https://booking.citilink.co.id/b2b/user.aspx';
	
	function __construct() {
		parent::__construct();
		$this->_cookies_file = "./components/service/third_party/comp_maskapai/cookies/citilink_airline.txt";
	}
	
	function index()
	{
		echo "citilink";
	}
	
	function dateAdd($date){
		$length = strlen($date);
		if ($length<2) {
			return '0'.$date;
		}else{
			return $date;
		}
	}
	
	function curl($url,$post_data = array(),$header = array()){
		if ($post_data != null) {
			$conf = array(
				'url'				=> $url,
				'timeout'			=> 150,
				'header'			=> 0,
				'followlocation'	=> 1,
				'cookiejar'			=> $this->_cookies_file,
				'cookiefile'		=> $this->_cookies_file,
				'returntransfer'	=> 1,
				'post'				=> true,
				'SSL_VERIFYPEER'	=> 0,
				'ssl_verifyhost'	=> 0,
				'postfields'		=> json_encode($post_data),
				'useragent'			=> 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:6.0.2) Gecko/20100101 Firefox/6.0.2',
			);
		}else{
			$conf = array(
				'url'				=> $url,
				'timeout'			=> 150,
				'header'			=> 0,
				'followlocation'	=> 1,
				'cookiejar'			=> $this->_cookies_file,
				'cookiefile'		=> $this->_cookies_file,
				'returntransfer'	=> 1,
				'post'				=> true,
				'referer'			=> $this->_refer,
				'VERBOSE'			=> 1,
				'SSL_VERIFYPEER'	=> 1,
				'ssl_verifyhost'	=> 0,
				'postfields'		=> json_encode(array()),
				'useragent'			=> 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:6.0.2) Gecko/20100101 Firefox/6.0.2',
			);
		}
		
		if ($header != null) {
			$head = array(
				'httpheader'	=>	$header,
			);
			$conf = array_merge($conf,$head);
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
	
	function mainPage(){
		$header = array(
			"Accept:text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
			"Host:booking.citilink.co.id",
		);
		$conf = array(
			'httpheader'		=>	$header,
			'url'				=> 'https://booking.citilink.co.id/b2b/user.aspx',
			'timeout'			=> 30,
			'header'			=> 1,
			'followlocation'	=> 1,
			'cookiejar'			=> $this->_cookies_file,
			'cookiefile'		=> $this->_cookies_file,
			'returntransfer'	=> 1,
			'referer'			=> $this->_refer,
			'SSL_VERIFYPEER'	=> 0,
			'ssl_verifyhost'	=> 0,
			'useragent'			=> 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:6.0.2) Gecko/20100101 Firefox/6.0.2',
		);
		$this->_ci->my_curl->setup($conf);
		$exc = $this->_ci->my_curl->exc();
		$page = str_get_html($exc);
		//return $page;
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
	
	function login(){
		$this->mainPage();
		$post_data = array(
			'userName'		 => 'tiket0001',
			'PassWord'		 => '1234rewq1',
			'companyname'	 => 'CGKTICKET801',
		);
		$header = array(
			"Content-Type: application/json; charset=UTF-8",
			"Host:booking.citilink.co.id",
			'Content-Length:'.strlen(json_encode($post_data)),
		);
		$url = 'https://booking.citilink.co.id/b2b/WebService/BaseService.asmx/UserLogOn';
		
		$exc = $this->curl($url,$post_data,$header);
		$this->mainPage();
	}
	
	function logout(){
		$header = array(
			"application/json; charset=UTF-8",
			//"Origin:https://booking.citilink.co.id",
			"Host:booking.citilink.co.id",
			'Content-Length:'.strlen(''),
		);
		$url = 'https://booking.citilink.co.id/b2b/WebService/BaseService.asmx/Logout';
		$exc = $this->curl($url,null,$header);
		//unlink($this->_cookies_file) ;
	}
	
	function search(){
		$dateExplode = explode('-',$this->_opt->date_depart);
		$post_data = array(
			'fromAirport'					=>	$this->_opt->route_from,
			'toAirport'						=>	$this->_opt->route_to,
			'dateFrom'						=>	element('0',$dateExplode).$this->dateAdd(element('1',$dateExplode)).$this->dateAdd(element('2',$dateExplode)),
			'dateTo'						=>	'',
			'iAdult'						=>	$this->_opt->adult,
			'iChild'						=>	$this->_opt->child,
			'iInfant'						=>	$this->_opt->infant,
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
			'OriginName'					=>	'',
			'DestinationName'				=>	'',
		);
		
		
		$header = array(
			"Content-Type:application/json; charset=UTF-8",
			"Host:booking.citilink.co.id",
			'Content-Length:'.strlen(json_encode($post_data)),
		);
		$url = 'https://booking.citilink.co.id/b2b/WebService/BaseService.asmx/getFlightAvailabilityForm';
		$exc = $this->curl($url,$post_data,$header);
		return $exc;
	}
	
	function searchResult(){
		$array = json_decode($this->search(),1);
		//echo implode($array);
		if (!is_array($array)) {return false;}
		//echo implode($array);
		$page = str_get_html(implode($array));
		if (!$page) {return false;}
		$table = $page->find('div[class=WrapperBody] div[id=dvGridFlight] table tbody',0);
		if (!$table) {return false;}
		if ( $tr = count($table->find('tr')) < 2) { return false;}
		if ($table->find('tr',1)->find('td',0)->plaintext == 'We could not find any flights or seats available on the date selected') {
			return false;
		}
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
			$price = str_replace('.00','',$price_di);
			$jml_kursi = str_split(preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$flight_data[$i]->find('td',6)->plaintext),1);
			$time_depart = str_split(preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$flight_data[$i]->find('td',2)->plaintext),5);
			$time_arrive = str_split(preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$flight_data[$i]->find('td',3)->plaintext),5);

			if (count($jml_kursi) > 1) {
				$flight_number = str_split($flightNum,5);
				$flightNo = $flight_number[0].' '.$flight_number[1];
				
				$t_depart = element('2',$date).'-'.element('1',$date).'-'.element('0',$date).' '.element('0',$time_depart);
				$t_arrive = element('2',$date).'-'.element('1',$date).'-'.element('0',$date).' '.element('1',$time_arrive);
				$t_transit_depart = element('2',$date).'-'.element('1',$date).'-'.element('0',$date).' '.element('1',$time_depart);
				$t_transit_arrive = element('2',$date).'-'.element('1',$date).'-'.element('0',$date).' '.element('0',$time_arrive);

			}else{
				$flightNo = $flightNum;
				$t_d_depart = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$flight_data[$i]->find('td',2)->plaintext);
				$t_d_arrive = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$flight_data[$i]->find('td',3)->plaintext);
				$t_depart = element('2',$date).'-'.element('1',$date).'-'.element('0',$date).' '.$t_d_depart;
				$t_arrive = element('2',$date).'-'.element('1',$date).'-'.element('0',$date).' '.$t_d_arrive;
				$t_transit_depart = NULL;
				$t_transit_arrive = NULL;
			}

			
			$radio_value = $flight_data[$i]->find('td',7)->find('input',0)->getAttribute('value');
			
			$meta = array(
				'company'			=>	'CITILINK',
				'flight_no'			=>	$flightNo,
				't_depart'			=>	$t_depart,
				't_arrive'			=>	$t_arrive,
				't_transit_depart'	=>	$t_transit_depart,
				't_transit_arrive'	=>	$t_transit_arrive,					
				'class'				=>	$class,
				'price'				=>	$price,
				'route'				=>	$this->_opt->route_from.','.$this->_opt->route_to,
				'radio_value'		=>	$radio_value,

				'arrayIndex'		=>	$i,
				'time_depart'		=>	element('2',$date).'-'.element('1',$date).'-'.element('0',$date),
				'passangers'		=>	$this->_opt->adult + $this->_opt->child + $this->_opt->infant,
				'adult'				=> 	$this->_opt->adult,
				'child'				=>	$this->_opt->child,
				'infant'			=>	$this->_opt->infant,
			);
			$data[$i]['company'] 			= 'CITILINK';
			$data[$i]['t_depart'] 			= $t_depart;
			$data[$i]['t_arrive']			= $t_arrive;
			$data[$i]['class'] 				= $class;
			$data[$i]['route'] 				= $this->_opt->route_from.','.$this->_opt->route_to;
			$data[$i]['meta_data']			= json_encode($meta);
			$data[$i]['t_transit_depart']   = $t_transit_depart;
			$data[$i]['t_transit_arrive'] 	= $t_transit_arrive;
			$data[$i]['price'] 				= $price;
			$data[$i]['flight_no'] 			= $flightNo;
			$data[$i]['route_from']			= $this->_opt->route_from;
			$data[$i]['route_to']			= $this->_opt->route_to;
			$data[$i]['adult']				= $this->_opt->adult;
			$data[$i]['child']				= $this->_opt->child;
			$data[$i]['infant']				= $this->_opt->child;
			$data[$i]['price_final']		= 0;			
			$data[$i]['date_depart']		= $this->_opt->date_depart;
		}
		return $data;
	}
	
	public function doSearch($opt = array(), $debug = false){
		
		$this->_opt->route_from 	= 'CGK';
		$this->_opt->route_to 		= 'MES';
		$this->_opt->date_depart 	= '2012-03-21';
		$this->_opt->date_return 	= NULL;
		$this->_opt->adult		 	= 2;
		$this->_opt->child 			= 1;
		$this->_opt->infant		 	= 1;
		
		foreach($opt as $key => $val ){$this->_opt->$key = $val;}
		$this->login();
		$final = $this->searchResult();
		$this->logout();
		if (count($final) == 0 || is_array($final) == false) {
			throw new ResultFareNotFound($opt);
		}
		return array_values($final);
	}
	
	function forBooking(){
		$final = $this->searchResult();
		return $final;
	}
	
	function getStep3(){
		$time = explode('-',$this->_opt->date_depart);
		$post_data = array(
			'OutwardFlightFareId'	=>	$this->_opt->radioValue,
			'ReturnFlightFareID'	=>	'',
			'OutWardDateFligh'		=>	element('0',$time).element('1',$time).element('2',$time).'_'.$this->_opt->time_depart,
			'OutSelectType'			=>	'FIRM',
			'RetSelectType'			=>	'FIRM',
		);
		$header = array(
			"Content-Type:application/json; charset=UTF-8",
			"Host:booking.citilink.co.id",
			'Content-Length:'.strlen(json_encode($post_data)),
		);
		$url = 'https://booking.citilink.co.id/b2b/WebService/BaseService.asmx/GetSelectFlight';
		$exc = $this->curl($url,$post_data,$header);
		$array = json_decode($exc,1);
		$str = implode($array);
		$page = str_get_html($str);
	}
	
	function detail(){
		$this->getStep3();
		$header = array(
			"Content-Type:application/json; charset=UTF-8",
			"Host:booking.citilink.co.id",
			'Content-Length:'.strlen(''),
		);
		
		$url = 'https://booking.citilink.co.id/b2b/WebService/UtilService.asmx/loadstep3';
		$exc = $this->curl($url,null,$header);
		$this->mainPage();
		$array = json_decode($exc,1);
		$html = implode($array);
		return str_get_html($html);
	}

	function backToResult(){
		$header = array(
			"Content-Type:application/json; charset=UTF-8",
			"Host:booking.citilink.co.id",
			'Content-Length:'.strlen(''),
		);
		$url = 'https://booking.citilink.co.id/b2b/WebService/BaseService.asmx/LoadFlightAvailability';
		$this->curl($url,null,$header);
		
	}
	
	function parsDetail(){
		$page = $this->detail();
		$errPage = $page->find('div[class=WrapperTBLStep3] span[id=ctl00_GridItinerary]',0)->plaintext;
		if ($errPage == 'Missing xml data and xls file') {return array();}
		$table1 = $page->find('div[class=WrapperTBLStep3] span tbody',0);
		$table2 = $page->find('div[class=WrapperTBLStep3] div[id=dvQuoute] span tbody',0);
		if(!$table1 && !$table2) return array();
		
		$cnt_detailDataPenerbangan = count($table1->find('tr',0)->find('td'));
		$cnt_detailJmlPenerbangan = count($table1->find('tr'));
		$cnt_detailPassanger = count($table2->find('tr'));
		$passDetail = array();
		$price_meta = array();
		$index = 0;
		for ($i=1; $i < $cnt_detailPassanger-1; $i++) { 
			$passType = preg_replace('/[^(\x20-\x7F)]*/','',
			preg_replace('#^\d+#','',$table2->find('tr',$i)->find('td',0)->plaintext));
			
			$pricePerson = str_replace(array(",",".00"),'',$table2->find('tr',$i)->find('td',1)->plaintext);
			$tax = str_replace(array(",",".00"),'',$table2->find('tr',$i)->find('td',2)->plaintext);
			$ppn = str_replace(array(",",".00"),'',$table2->find('tr',$i)->find('td',3)->plaintext);
			$passDetail[$index]['passanger_type'] = $passType;
			$passDetail[$index]['price_per_pax'] = $pricePerson;
			$passDetail[$index]['pax']	=	$tax;
			$passDetail[$index]['ppn']	=	$ppn;
			if ($passType=='ADULT') {
				$key = 'adult';
				$price = ($pricePerson+$tax+$ppn)*$this->_opt->adult;
			}else if($passType=='CHD'){
				$price = ($pricePerson+$tax+$ppn)*$this->_opt->child;
				$key = 'child';
			}else if ($passType=='INF') {
				$price = ($pricePerson+$tax+$ppn)*$this->_opt->infant;
				$key = 'infant';
			}
			$passDetail[$index]['total_per_type'] = $price;
			$totalPirice[$index] = $price;
			$passTotalPrice['price'] = array_sum($totalPirice);
			$price_meta[$key] = $pricePerson + $tax + $ppn;
			$index++;
		}
		//$price = array_sum($totalPirice);
		
		$metaArray = json_decode(element('meta_data',$this->fare_data),1);
		$meta = array(
			'id'				=> element('id',$this->fare_data),
			'log_id'			=>	element('log_id',$this->fare_data),
			'comapny'			=>	element('company',$this->fare_data),
			't_depart'			=>	element('t_depart',$this->fare_data),
			't_arrive'			=>	element('t_arrive',$this->fare_data),
			'class'				=>	element('class',$this->fare_data),
			'route'				=>	element('route',$this->fare_data),
			't_transit_arrive'	=>	element('t_transit_arrive',$this->fare_data),
			't_transit_depart'	=>	element('t_transit_depart',$this->fare_data),
			'price'				=>	element('price',$passTotalPrice),
			'flight_no'			=>	element('flight_no',$this->fare_data),
			'route_from'		=>	element('route_from',$this->fare_data),
			'route_to'			=>	element('route_to',$this->fare_data),
			'adult'				=>	$this->_opt->adult,
			'child'				=> 	$this->_opt->child,
			'infant'			=>	$this->_opt->infant,
			'arrayIndex'		=>	element('arrayIndex',$metaArray),
			'passangers'		=>	$this->_opt->adult+$this->_opt->child+$this->_opt->infant,
			'time_depart'		=>	$this->_opt->date_depart,
			'radio_value'		=>	$this->_opt->radioValue,
			'price_meta'		=>	$price_meta,
		);
		
		$fare_data['id']		= element('id',$this->fare_data);
		$fare_data['log_id']	= element('log_id',$this->fare_data);
		$fare_data['company'] = element('company',$this->fare_data);
		$fare_data['t_depart'] = element('t_depart',$this->fare_data);
		$fare_data['t_arrive'] = element('t_arrive',$this->fare_data);
		$fare_data['class'] = element('class',$this->fare_data);
		$fare_data['route'] = element('route',$this->fare_data);
		$fare_data['t_transit_arrive'] = element('t_transit_arrive',$this->fare_data);
		$fare_data['t_transit_depart'] = element('t_transit_depart',$this->fare_data);
		$fare_data['price'] = element('price',$passTotalPrice);
		$fare_data['flight_no'] = element('flight_no',$this->fare_data);
		$fare_data['route_to'] = element('route_to',$this->fare_data);
		$fare_data['route_from'] = element('route_from',$this->fare_data);
		$fare_data['adult']	= $this->_opt->adult;
		$fare_data['child'] = $this->_opt->child;
		$fare_data['infant'] = $this->_opt->infant;
		$fare_data['price_final'] = 1;
		$fare_data['meta_data'] = json_encode($meta);
		$fare_data['price_meta'] = $price_meta;
		$this->backToResult();
		return $fare_data;
	}
	
	public function getDetail($fare_data = array()){
		/*$fare_data = Array( 
		   		'company' => 'CITILINK',
		        't_depart' => '2012-03-21 05:20',
		        't_arrive' => '2012-03-21 07:35',
		        'class' => 'K',
		        'route' => 'CGK,MES',
		        'meta_data' => '{"company":"CITILINK","flight_no":"GA040","t_depart":"2012-03-21 05:20","t_arrive":"2012-03-21 07:35","t_transit_depart":null,"t_transit_arrive":null,"type":"depart","class":"K","price":"947000.00","route":"CGK,MES","radio_value":"{5B68272E-283A-4FE1-9571-C7CD85B46A89}|{9A68EF3F-5F0C-11DF-8E35-18A905E04790}||","arrayIndex":1,"time_depart":"2012-03-21","passangers":2,"adult":2,"child":0,"infant":0}',
		        't_transit_depart' => '', 
		        't_transit_arrive' => '',
		        'price' => '947000.00',
		        'flight_no' => 'GA040',
		        'route_from' => 'CGK',
		        'route_to' => 'MES',
		        'adult' => '2',
		        'child' => '1',
		        'infant' => '1',
		        'price_final' => '0',
		);*/
		
		
		$meta_data = json_decode(element('meta_data',$fare_data),1);
		$log = element('log',$fare_data);
		
		$this->_opt->radioValue = element('radio_value',$meta_data);
		$this->_opt->date_depart = element('time_depart',$meta_data);
		$this->_opt->time_depart = str_replace(":","_",element('1',explode(' ',element('t_depart',$meta_data))));
		$this->_opt->route_from 	= element('route_from',$fare_data);
		$this->_opt->route_to 		= element('route_to',$fare_data);
		
		$this->_opt->adult			= element('adult',$fare_data);
		$this->_opt->child			= element('child',$fare_data);
		$this->_opt->infant			= element('infant',$fare_data);
		$this->_opt->id				= element('id',$fare_data);
		//$this->_opt->passengers		= element('passangers',$meta_data);

		$this->fare_data = $fare_data;
		$this->login();
		$searchRes = $this->forBooking();
 		$res = $this->parsDetail();
		$this->logout();
		return $res;
	}
	
	function loadStep4(){
		$header = array(
			"Content-Type:application/json; charset=UTF-8",
			"Host:booking.citilink.co.id",
			'Content-Length:'.strlen(''),
		);
		
		$url = 'https://booking.citilink.co.id/b2b/WebService/UtilService.asmx/loadstep4';
		$exc = $this->curl($url,null,$header);
		$array = json_decode($exc,1);
		if (!is_array($array)){return false;}			
		$html = implode($array);
		$page = str_get_html($html);
		$table =$page->find('table[id=PassengerList] tbody',0);
		if (!$table) {return false;}
		$data = array();
		$index = 0;
		$counter = $this->_opt->adult + $this->_opt->child + $this->_opt->infant;
		for ($i=1; $i <= $counter; $i++) {
			$passId = $table->find('input[id=uxPassengerID]',$i-1)->getAttribute('value');
			$passType = $table->find('input[id=uxPassengerType_'.$i.']',0)->getAttribute('value');
			$data[$index] = array(
				'PassengerID'	=>	$passId,
				'PassengerType'	=>	$passType,
			);
			$index++;
		}
		return $data;
	}
	
	function savestep4(){
		$passid = $this->loadStep4();
		$arrayXml = array();
		
		$i = 0;
		foreach ($this->passangers as $key => $value) {
			
			if (element('type',$value) == 'adult') {
				$id = 'I';
				$title = element('title',$value);
				
			}else if(element('type',$value) == 'child'){
				$id = 'B';
				$title = 'CHD';
			}else if(element('type',$value)== 'infant'){
				$id = 'B';
				$title = 'INF';
			}
			$name = explode(' ',element('name',$value));
			$gender = element('gender', $value);
			$idNumber = element('no_id',$value);
			$arrayXml[$i] = array(
				'Passenger'	=> array(
					'passenger_id'				=>	element('PassengerID',element($i,$passid)),
					'client_number'				=>	'',
					'client_profile_id'			=>	'00000000-0000-0000-0000-000000000000',
					'passenger_profile_id'		=>	'00000000-0000-0000-0000-000000000000',
					'passenger_type_rcd'		=>	element('PassengerType',element($i,$passid)),
					'employee_number'			=>	'',
					'title_rcd'					=>	strtoupper($title).'|'.$gender,
					'lastname'					=>	strtoupper($name[1]),
					'firstname'					=>	strtoupper($name[0]),
					'nation'					=>	'ID',
					'documenttype'				=>	$id,
					'documentnumber'			=>	$idNumber,
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
				$i++;
		}
			
			
		$contactXML = array(
			'ContactPerson'		=>	element('name',$this->contact),
			'HomePhone'			=>	'HomePhone',
			'Email'				=>	element('email',$this->contact),
			'MobilePhone'		=>	element('phone',$this->contact),
			'BusinessPhone'		=>	element('phone',$this->contact),
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
			"Host:booking.citilink.co.id",
			'Content-Length:'.strlen(json_encode($post_data)),
		);
		
		$url = 'https://booking.citilink.co.id/b2b/WebService/UtilService.asmx/savestep4';
		$this->curl($url,$post_data,$header);
	}
	
	function loadssr(){
		$header = array(
			"Content-Type:application/json; charset=UTF-8",
			"Host:booking.citilink.co.id",
			'Content-Length:'.strlen(''),
		);
		$url = 'https://booking.citilink.co.id/b2b/WebService/UtilService.asmx/loadssr';
		$exc = $this->curl($url,null,$header);
	}
	
	function loadStep5(){
		$header = array(
			"Content-Type:application/json; charset=UTF-8",
			"Host:booking.citilink.co.id",
			'Content-Length:'.strlen(''),
		);
		$url = 'https://booking.citilink.co.id/b2b/WebService/UtilService.asmx/loadstep5';
		$exc = $this->curl($url,null,$header);
	}
	
	function step5postpaid(){
		$header = array(
			"Content-Type:application/json; charset=UTF-8",
			"Host:booking.citilink.co.id",
			'Content-Length:'.strlen(''),
		);
		$url = 'https://booking.citilink.co.id/b2b/WebService/UtilService.asmx/loadstep5';
		$exc = $this->curl($url,null,$header);
	}
	
	function refreshCaptha(){
		$header = array(
			"Content-Type:application/json; charset=UTF-8",
			"Host:booking.citilink.co.id",
			'Content-Length:'.strlen(''),
		);
		$url = 'https://booking.citilink.co.id/b2b/WebService/UtilService.asmx/refreshCaptcha';
		$exc = $this->curl($url,null,$header);
		$array = json_decode($exc,1);
		return element('d',$array);
	}
	
	function checkCapthaSecurity(){
		$word = $this->refreshCaptha();
		
		$post_data = array(
			'strSecurityCode'	=>	$word,
			'caseSensitive'		=>	true,
		);
		
		$header = array(
			"Content-Type:application/json; charset=UTF-8",
			"Host:booking.citilink.co.id",
			'Content-Length:'.strlen(json_encode($post_data)),
		);
		$url = 'https://booking.citilink.co.id/b2b/WebService/UtilService.asmx/checkCaptchaSecurity';
		$this->curl($url,$post_data,$header);
	}
	
	function payleter(){
		$header = array(
			"Content-Type:application/json; charset=UTF-8",
			"Host:booking.citilink.co.id",
			'Content-Length:'.strlen(''),
		);
		$url = 'https://booking.citilink.co.id/b2b/WebService/Payment.asmx/Paylater';
		$exc = $this->curl($url,null,$header);
		$array = json_decode($exc,1);
		if (!is_array($array)) {return false;}
		$html = implode($array);
		$page = str_get_html($html);
		//$html = "./components/service/third_party/comp_maskapai/citilink/bookingDone.html";
		//$page = file_get_html($html);

		if (!$page) {return array();}
		$table = $page->find('div[class=WrapperBody] table');
		$cntTable = count($table);
		if ($cntTable == 0) {return array();}
		$bookingCode = $page->find('div[class=WrapperBody] div[class=BookingRefItenerary]',0)->
		find('span[class=BookingRefId]',0)->plaintext;
		
		$bookingDate = $page->find('div[class=WrapperBody] div[class=BookingRefIteneraryDate]',0)->plaintext;
		$bookingInfo = $table[0];
		$passangerInfo = $table[1];
		$detailInfo = $table[2];
		$countPassanger = count($passangerInfo->find('tr'));
		$data = array();
		
		$price = $this->cleanString(preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',str_replace(',','',str_replace('.00','',str_replace('Total Harga','',$table[2]->find('tr',6)->find('td',4)->find('span[class=FooterTotalLabel]',0)->plaintext)))));
		
		$flightNumber = $bookingInfo->find('tr',1)->find('td',0)->plaintext;
		$routeFrom = $bookingInfo->find('tr',1)->find('td',1)->plaintext;
		$routeTo = $bookingInfo->find('tr',1)->find('td',2)->plaintext;
		$date = explode('/',$bookingInfo->find('tr',1)->find('td',3)->plaintext);
		$time = element('2',$date).'-'.element('1',$date).'-'.element('0',$date);
		$departTime = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$bookingInfo->find('tr',1)->find('td',4)->plaintext);
		$arrTime = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$bookingInfo->find('tr',1)->find('td',5)->plaintext);
		$status = $bookingInfo->find('tr',1)->find('td',6)->plaintext;
		
		$data['fare_id']			=	$this->fare_id;
		$data['booking_number']  	=	$bookingCode;
		$data['meta_data']			=	json_encode($this->meta_data);
		
		//$data['passangers']			=	$this->passangers;
		//$data['final_price']		=	$price;
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
		return $data;
	}

	function booking(){
		$this->search();
		$this->detail();
		$this->savestep4();
		$this->loadssr();
		$this->loadstep5();
		$this->step5postpaid();
		$this->checkCapthaSecurity();
		return $this->payleter();
	}
	
	function doBooking($fare_data = array(),$passangers_data = array(),$contact_data = array()){
		/*$fare_data = Array
		(
		    'id' => 77757,
		    'company' => 'CITILINK',
		    't_depart' => '2012-03-21 05:20',
		    't_arrive' => '2012-03-21 07:35',
		    'class'	 => 'K',
		    'route' => 'CGK,MES',
		    't_transit_arrive'	 =>	'', 
		    't_transit_depart'	 =>	'', 
		    'price' => 2996845,
		    'flight_no'	=> 'GA040',
		    'route_to'	 => 'MES',
		    'route_from' => 'CGK',
		    'adult'	 => 2,
		    'child'	 => 1,
		    'infant' => 1,
		    'price_final' => 1,
		    'meta_data' => '{"comapny":"CITILINK","t_depart":"2012-03-21 05:20","t_arrive":"2012-03-21 07:35","class":"K","route":"CGK,MES","t_transit_arrive":false,"t_transit_depart":false,"price":2996845,"flight_no":"GA040","route_from":"CGK","route_to":"MES","adult":"2","child":"1","infant":"1","arrayIndex":1,"passangers":4,"time_depart":"2012-03-21","radio_value":"{5B68272E-283A-4FE1-9571-C7CD85B46A89}|{9A68EF3F-5F0C-11DF-8E35-18A905E04790}||","price_meta":{"ADULT":1048700,"CHD":788275,"INF":111170}}',
		    'price_meta' => array
		        (
		            'ADULT' => 1048700,
		            'CHD' => 788275,
		            'INF' => 111170,
		        ),

		);
		
		$passangers_data = array(
			array(
					'name' 				=>	'Zidni Mubarock',
					'no_id'				=>	'3671081902880001',
					'title' 			=>	'Mr',
					'gender'			=>	'M',
					'birthday'			=> 	'1988-01-19',
					'type'				=>	'adult',
			),
			array(
					'name' 				=>	'Zidni Mubarock',
					'no_id'				=>	'3671081902880001',
					'title' 			=>	'Mr',
					'gender'			=>	'M',
					'birthday'			=> 	'1988-01-19',
					'type'				=>	'adult',
			),
			array(
					'name' 				=>	'Zidni Mubarock',
					'no_id'				=>	'3671081902880001',
					'title' 			=>	'Mr',
					'gender'			=>	'M',
					'birthday'			=> 	'2007-01-19',
					'type'				=>	'child',					
			),
			array(
					'name' 				=>	'Zidni Mubarock',
					'no_id'				=>	'3671081902880001',
					'title' 			=>	'Mr',
					'gender'			=>	'M',
					'birthday'			=> 	'2011-01-19',
					'type'				=>	'infant',
			),
		);
				
		$contact_data = Array
		(
			'name' => 'Zidni Mubarock',
		    'no_id' => '6429364294293',
		    'title' => 'Mr',
		    'gender' => 'M',
		    'birthday' => '1988-01-19',
		    'phone' => '2342342234',
		    'mobile' => '32382398232',
		    'email' => 'me@mail.com',
		);*/
		
		$this->passangers = $passangers_data;
		$this->contact = $contact_data;
	
		$forBooking = json_decode($fare_data['meta_data'],1);
		$route = explode(',',$forBooking['route']);
		
		$route_from = element('route_from',$fare_data);
		$route_to = element('route_to',$fare_data);
		$this->fare_id = element('id',$fare_data);
		
		$this->meta_data = $forBooking;
		$time_depart = str_replace(':','_',element('1',explode(' ',element('t_depart',$forBooking))));
		
		$this->_opt->route_from 	= $route_from;
		$this->_opt->route_to 		= $route_to;
		$this->_opt->date_depart 	= element('time_depart',$forBooking);
		$this->_opt->date_return 	= NULL;
		//$this->_opt->passengers		= element('passangers',$forBooking);
		$this->_opt->adult		 	= element('adult',$forBooking);
		$this->_opt->child		 	= element('child',$forBooking);
		$this->_opt->infant		 	= element('infant',$forBooking);

		$this->_opt->radioValue		= element('radio_value',$forBooking);
		$this->_opt->time_depart	= $time_depart;
		//$this->_opt->passangerTotal = $this->_opt->passengers+$this->_opt->child+$this->_opt->child;
		$this->login();
		$data = $this->booking();
		$this->logout();
		if (is_array($data) == false) {
			throw new BookingFailed($fare_data);
		}
		if (element('final_price',$data) > element('price',$fare_data)) {
			throw new BookingFarePriceChanged($fare_data, element('final_price',$booking));
		}
		return $data;
	}
}