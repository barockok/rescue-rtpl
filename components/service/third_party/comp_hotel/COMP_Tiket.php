<?

if (! defined('BASEPATH')) exit('No direct script access');

class Tiket extends Comp_hotel_base {

	//php 5 constructor
	function __construct() {
		parent::__construct();
		ini_set('memory_limit','128M');
		ini_set('max_execution_time',18000);
		$this->_cookies_file = dirname(__FILE__)."/cookies/tiket.txt";
		
		$this->get_location_url = 'http://www.tiket.com/search/autocomplete/hotel?q=';
	}
	
	//php 4 constructor
	
	function index() {
		echo 'tiket.com';
	}
	
	function autoComplete($text){ 
		$url = 'http://www.tiket.com/search/autocomplete/hotel?q='.$text;
		$conf = array(
				'url' => $url,
				'cookiejar' 		=> $this->_cookies_file,
				'cookiefile' 		=> $this->_cookies_file,
				'header'		=> 0,
				'nobody'	=> false,
				'returntransfer' => 1,
				'timeout'			=> 150,
			//	'returntransfer' => 1
		);
		
		$this->_ci->my_curl->setup($conf);
		$exc = $this->_ci->my_curl->exc();
		$replace1 = str_replace('[','',$exc);
		$replace2 = str_replace(']','',$replace1);
		$a = explode('},',$replace2);
		if (array_key_exists('1',$a)) {
			$b = element('0',$a).'}';
			return json_decode($b,true);
		}else{
			return json_decode($replace2,true);
		}
		//return json_decode($replace2,true);
	}
	
	function gotoPage($url){
		$conf = array(
				'url' => $url,
				'cookiejar' 		=> $this->_cookies_file,
				'cookiefile' 		=> $this->_cookies_file,
				'header'		=> 0,
				'nobody'	=> false,
				'returntransfer' => 1,
				'timeout'			=> 150,
			//	'returntransfer' => 1
		);
		$this->_ci->my_curl->setup($conf);
		$exc = $this->_ci->my_curl->exc();
		$page = str_get_html($exc);
		return $page;
		
	}
	
	function detailParsing($url){
	//	$url = 'http://www.tiket.com/gowongan-inn?startdate=2012-02-14&enddate=2012-02-15&night=1&room=1&adult=2&child=0&uid=business%3A13';
		$page = $this->gotoPage($url);
		$data = array();
		
		if ($page->find('div[class=wrap] div[class=mainbar]',0)) {
			$wrap = $page->find('div[class=wrap] div[class=mainbar]',0);
			//echo $wrap;
			if ($wrap->find('div[class=more-less]')) {
				$description = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$wrap->find('div[class=more-less] div[class=more-block] p',0)->plaintext);
			}else{
				$description = '';
			}
			
			
			$picture = $wrap->find('div[class=photos] a');
			$resultBox = $wrap->find('div[class=searchresult] div[class=resultbox]',0);
			$class = $resultBox->find('span[itemprop=offerDetails]');
			$accorditionHotel = $wrap->find('div[class=accordion-content]',0)->find('ul',0)->find('li');
			$accorditionRoom = $wrap->find('div[class=accordion-content]',1)->find('ul',0)->find('li');
			$accorditionSport = $wrap->find('div[class=accordion-content]',2)->find('ul',0)->find('li');
			
			$data['description'] = $description;
			if ($wrap->find('div[class=polifees]')) {
				$policies = $wrap->find('div[class=polifees] ul',0)->find('li');
				for ($i=0; $i < count($policies); $i++) {
					$policy = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$policies[$i]->plaintext);
					$data['policies'][$i] = $policy;
				}
			}else{
				$data['policies'] = array();
			}
			
			for ($i=0; $i < count($picture); $i++) { 
				$pic = str_replace('_L.l.jpg','.jpg',$picture[$i]->getAttribute('href'));
				$data['pictures'][$i] = $pic;
			}
			
			$indexHotel = 0;
			for ($i=0; $i < count($accorditionHotel); $i++) { 
				if ($accorditionHotel[$i]->getAttribute('class')=='off') {continue;}
				$accordhotel = $accorditionHotel[$i]->plaintext;
				$data['facilities']['hotel'][$indexHotel] = $accordhotel;
				$indexHotel++;
			}
			$indexRoom = 0;
			for ($i=0; $i < count($accorditionRoom); $i++) { 
				if ($accorditionRoom[$i]->getAttribute('class')=='off') {continue;}
				$accordroom = $accorditionRoom[$i]->plaintext;
				$data['facilities']['room'][$indexRoom] = $accordroom;
				$indexRoom++;
			}
			$indexSport = 0;
			for ($i=0; $i < count($accorditionSport); $i++) { 
				if ($accorditionSport[$i]->getAttribute('class')=='off') {continue;}
				$accordsport = $accorditionSport[$i]->plaintext;
				$data['facilities']['sport'][$indexSport] = $accordsport;
				$indexSport++;
			}
			
			for ($i=0; $i < count($class); $i++) { 
				
				$className = str_replace('_room','',str_replace(' ','_',strtolower(str_replace(' Room ','',
				preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',
				$class[$i]->find('div[class=roomWrapper] div[class=roomlist] div[class=roomDesc] h4 a',0)->plaintext)))));
				
				$picvar = $i."_L.s.jpg";
				$classPic = $class[$i]->find('div[class=roomWrapper] div[class=roomlist] a img[class=thumb]',0)->getAttribute('src');
				
				if ($class[$i]->find('div[class=roomWrapper] div[class=rateroom] div[itemprop=availability]',0)) {
						$availability = element('1',explode(' ',
						$class[$i]->find('div[class=roomWrapper] div[class=rateroom] div[itemprop=availability]',0)->plaintext));
				}else{
					$availability = 0;
				}
			
				
				$price = str_replace('.00','',
				$class[$i]->find('div[class=roomWrapper] div[class=rateroom] h3 span[class=currency]',0)->getAttribute('rel')); 
				
				$data['class'][$className] = array(
					'price'	=>	$price,
					'facility'	=>	element('room',element('facilities',$data)),
					'discount'	=>	0,
					'picture'	=>	$classPic,
					'availability'	=>	$availability,
					'includes'	=>	'',
					'room_id'	=>	'',
				);
			}		
		}
		
		return $data;
	}
	
	function searchParsing($url){
		$page = $this->gotoPage($url);
		$data = array();
		if ($page->find('div[class=wrap] ul[class=searchresult]',0)) {
			$result = $page->find('div[class=wrap] ul[class=searchresult]',0)->find('li');

			if ($page->find('div[class=wrap] ul[class=searchresult]',0)->find('li[data-id=-1]')) {
				$cntResult = count($result) - 1;
			}else{
				$cntResult = count($result);
			}
			
			for ($i=1; $i < $cntResult; $i++) { 
				$hotelName =  preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$result[$i]
				->find('div[class=itemarea] div[class=itemDetail] h3 a',0)->plaintext);
				
				$urlDetail = $result[$i]->find('div[class=itemarea] div[class=itemDetail] h3 a',0)->getAttribute('href');

				$findDetail = $this->detailParsing($urlDetail);
				
				$mapUrl = $result[$i]->find('div[class=itemarea] div[class=itemDetail] a[class=lightbox notextdecor]',0)
				->getAttribute('href');
				
				$coordinat = explode(',',str_replace('&hl=en&z=15&lightbox[width]=610&lightbox[height]=360','',
				str_replace('http://maps.google.com/maps?daddr=','',$mapUrl)));
				
				$startingPrice = str_replace('.00','',
				$result[$i]->find('div[class=selectarea] h3 span[class=currency]',0)->getAttribute('rel'));
				
				$data[$i]['name'] = $hotelName;
				$data[$i]['address'] = '';
				$data[$i]['map_coordinate'] = element('0',$coordinat).','.element('1',$coordinat);
				$data[$i]['description'] = element('description',$findDetail);
				$data[$i]['policies'] = element('policies',$findDetail);
				$data[$i]['start_price'] = $startingPrice;
				$data[$i]['number_of_rooms'] = 1;
				$data[$i]['pictures'] = element('pictures',$findDetail);
				$data[$i]['facilities'] = element('facilities',$findDetail);
				$data[$i]['class'] = element('class',$findDetail);
				$data[$i]['meta_data'] = json_encode(array());
		
			}
			return $data;
		}
	}
	
	function search(){
		$beArray = $this->autoComplete($this->_opt->city);
		$uid = element('id',$beArray);		
		$conf = array(
			'q'	=>	element('value',$beArray),
			'startdate'	=>	$this->_opt->checkin,
			'night'	=>	$this->dateInterval($this->_opt->checkin,$this->_opt->checkout),
			'enddate'	=>	$this->_opt->checkout,
			'room'	=>	$this->_opt->room,
			'adult'	=>	$this->_opt->passangers,
			'child'	=>	0,
			'uid'	=>	$uid,
		);
		
		$url = 'http://www.tiket.com/search/hotel?'.http_build_query($conf);
		$page = $this->gotoPage($url);
		
		$data = array();
		$final = array();		
		if ($page->find('div[class=wrap] ul[class=searchresult]',0)) {
			
			if ($page->find('div[class=wrap] ul[class=searchresult]',0)->find('li[data-id=-1] a')) {
								
				$pagination = $page->find('div[class=wrap] ul[class=searchresult]',0)->
				find('li[data-id=-1] div[class=pagination]',0)->find('a');

				$numberLink = count($pagination);
				$paging = $pagination[$numberLink-2]->plaintext;
				for ($i=1; $i <= $paging; $i++) {
					$conf1 = array(
						'q'	=>	element('value',$beArray),
						'uid'	=>	$uid,
						'startdate'	=>	$this->_opt->checkin,
						'night'	=>	$this->dateInterval($this->_opt->checkin,$this->_opt->checkout),
						'enddate'	=>	$this->_opt->checkout,
						'room'	=>	$this->_opt->room,
						'adult'	=>	$this->_opt->passangers,
						'child'	=>	0,
						'minstar'	=> 0,
						'maxstar'	=> 5,
						'minprice'	=> 0,
						'maxprice'	=> 1000000000,
						'hotelname'	=> 0,
						'page' => $i,
					);
					$url1 = 'http://www.tiket.com/search/hotel?'.http_build_query($conf1);
					$data[$i] = $this->searchParsing($url1);
				}
				$final = array();
				$i = 0;
				foreach ($data as $dta => $item) {
					foreach ($item as $fare) {
						$final[$i]	=	$fare;
						$i++;
					}
				}
			}else{
				
				$final = $this->searchParsing($url);
			}	
		}
		return $final;	
	}
	
	function doSearch($opt = array()){
		$this->_opt->city 			= 'medan';
		$this->_opt->checkin 		= '2012-02-16';
		$this->_opt->passangers 	= 2;
		$this->_opt->checkout		= '2012-02-18';
		$this->_opt->room			= 1;
		$this->_opt->id				= 1;
		foreach($opt as $key => $val ) $this->_opt->$key = $val;
		$result = $this->search();
		return $result;
	}
	
	function dateInterval($datein,$dateout){
		$date1 = new DateTime($datein);
		$date2 = new DateTime($dateout);
		$interval = $date2->diff($date1);
		return $interval->days;
	}

}