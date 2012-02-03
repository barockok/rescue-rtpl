<?

if (! defined('BASEPATH')) exit('No direct script access');

class Kamar extends Comp_hotel_base {
	
	private $mainUrl = 'http://www.rajakamar.com';
	private $searchToPage = 'http://www.rajakamar.com/SearchHotel.aspx';
	private $referer = 'http://rajakamar.com/';
	//php 5 constructor
	function __construct() {
		parent::__construct();
		$this->_cookies_file = dirname(__FILE__)."/cookies/kamar.txt";
		//$this->load->libraries('database');
	}
	
	//php 4 constructor
	
	function index() {
		echo 'rajakamar.com';
		$this->_opt->date = '2012-01-31';
		$date = explode('-',$this->_opt->date);
		//echo element('1',$date);
		echo $date_post =  element('2',$date).'-'.$this->monthConvert(element('1',$date)).'-'.element('0',$date);
		
	}

	function firstPage(){
		$conf = array(
				'url' => $this->mainUrl,
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
		$page = str_get_html($exc);
		$eventValidation = $page->find('input[name=__EVENTVALIDATION]',0)->getAttribute('value');
		$myViewState = $page->find('input[name=__MYVIEWSTATE]',0)->getAttribute('value');
		
		$data = array(
			'eventValidation'	=>	$eventValidation,
			'myViewState'		=>	$myViewState,
		);
		return $data;
	}
	
	
	function updateCity(){
		$eventValidation = element('eventValidation',$this->firstPage());
		$myViewState = element('myViewState',$this->firstPage());
		$date = explode('-',$this->_opt->checkin);
		$date_post =  element('2',$date).'-'.$this->monthConvert(element('1',$date)).'-'.element('0',$date);
		$nite = $this->dateInterval($this->_opt->checkin,$this->_opt->checkout);
						
		$post_data = array(
			'ctl00$Scriptmanager1'	=>	'ctl00$updateCity|ctl00$City',
			'__EVENTVALIDATION'		=>	$eventValidation,
			'__EVENTTARGET'			=>	'ctl00$City',
			'__EVENTARGUMENT'		=>	'',
			'__LASTFOCUS'			=>	'',
			'__MYVIEWSTATE'			=>	$myViewState,
			'__VIEWSTATE'			=>	'',
			'ctl00$Country'			=>	26,
			'ctl00$City'			=>	$this->_opt->city,
			'ctl00$Area'			=>	'-1',
			'ctl00$PaxPassport'		=>	26,
			'ctl00$CheckInDate'		=>	$date_post,//'31-Jan-2012',
			'ctl00$Nite'			=>	$nite,
			'ctl00$HotelName'		=>	'',
			'ctl00$HotelRating'		=>	'-1',
			'ctl00$cpHtl$advHtlFav$ctl01$CollapsiblePanelExtender1_ClientState'	=>	'true',
			'ctl00$cpHtl$advHtlFav$ctl02$CollapsiblePanelExtender1_ClientState'	=>	'true',
			'ctl00$cpHtl$advHtlFav$ctl03$CollapsiblePanelExtender1_ClientState'	=>	'true',
			'ctl00$cpHtl$advHtlFav$ctl04$CollapsiblePanelExtender1_ClientState'	=>	'true',
			'__VIEWSTATEENCRYPTED'	=>	'',
			'__ASYNCPOST'			=>	'true',
			''	=>	'',
		);
		
		//return http_build_query($post_data);
		$header = array(
			"Content-Length:".strlen(http_build_query($post_data)),
		);
		
		$conf = array(
			'url'				=> $this->mainUrl,
			'httpheader'		=> $header,
			'timeout'			=> 60,
			'header'			=> 0,
			'followlocation'	=> 1,
			'cookiejar'			=> $this->_cookies_file,
			'cookiefile'		=> $this->_cookies_file,
			'returntransfer'	=> 1,
			'post'				=> true,
			'referer'			=> $this->mainUrl,
			'ssl_verifyhost'	=> 0,
			'SSL_VERIFYPEER'	=> 0,
			'postfields'		=> http_build_query($post_data),
			'useragent'			=> 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:6.0.2) Gecko/20100101 Firefox/6.0.2',
		);
		$this->_ci->my_curl->setup($conf);
		$html = $this->_ci->my_curl->exc();
		$page = str_get_html($html);
		$to_array = explode('|',$page);
		//print_r($to_array);
		$arrayIndexEventValidation = array_search('__EVENTVALIDATION',$to_array);
		$newEventValidation = element($arrayIndexEventValidation+1,$to_array);
		$arrayIndexMyViewState = array_search('__MYVIEWSTATE',$to_array);
		$newMyViewState = element($arrayIndexMyViewState+1,$to_array);
		$data = array(
			'eventValidation'	=>	$newEventValidation,
			'myViewState'		=>	$newMyViewState,
		);
		return $data;
	}
	
	function searchToPage(){
		$header = array(
			"Host:rajakamar.com",			
		);
		
		$conf = array(
				'url' => $this->searchToPage,
				'followlocation'	=> 1,
				'cookiejar' 		=> $this->_cookies_file,
				'cookiefile' 		=> $this->_cookies_file,
				'header'		=> 0,
				'nobody'	=> false,
				'returntransfer' => 1,
				'referer'			=> $this->referer,
				'timeout'			=> 120,
				'useragent'			=> 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:6.0.2) Gecko/20100101 Firefox/6.0.2',
			);
		$this->_ci->my_curl->setup($conf);
		$exc = $this->_ci->my_curl->exc();
		//echo str_get_html($exc);
		return $exc;
	}
	
	function asearch(){
		$updateVal = $this->updateCity();
		$eventValidation = element('eventValidation',$updateVal);
		$myViewState = element('myViewState',$updateVal);
		$date = explode('-',$this->_opt->checkin);
		$date_post =  element('2',$date).'-'.$this->monthConvert(element('1',$date)).'-'.element('0',$date);
		$nite = $this->dateInterval($this->_opt->checkin,$this->_opt->checkout);
		
		$post_data = array(
			'ctl00$Scriptmanager1'	=>	'ctl00$updateBtnSearch|ctl00$Search',
			'__VIEWSTATE'			=>	'',
			'ctl00$Country'			=>	26,
			'ctl00$City'			=>	$this->_opt->city,
			'ctl00$Area'			=>	'-1',
			'ctl00$PaxPassport'		=>	26,
			'ctl00$CheckInDate'		=>	$date_post,
			'ctl00$Nite'			=>	$nite,
			'ctl00$HotelName'		=>	'',
			'ctl00$HotelRating'		=>	'-1',
			'ctl00$cpHtl$advHtlFav$ctl01$CollapsiblePanelExtender1_ClientState'	=>	'true',
			'ctl00$cpHtl$advHtlFav$ctl02$CollapsiblePanelExtender1_ClientState'	=>	'true',
			'ctl00$cpHtl$advHtlFav$ctl03$CollapsiblePanelExtender1_ClientState'	=>	'true',
			'ctl00$cpHtl$advHtlFav$ctl04$CollapsiblePanelExtender1_ClientState'	=>	'true',
			'__EVENTTARGET'	=>	'',
			'__EVENTARGUMENT'	=>	'',
			'__LASTFOCUS'	=>	'',
			'__VIEWSTATEENCRYPTED'	=>	'',
			'__EVENTVALIDATION'	=>	$eventValidation,
			'__MYVIEWSTATE'		=>	$myViewState,
			'__ASYNCPOST'		=>	'true',
			'ctl00$Search.x'	=>	87,
			'ctl00$Search.y'	=>	17,
		);
		
			$header = array(
				"Content-Length:".strlen(http_build_query($post_data)),
			);

			$conf = array(
				'url'				=> $this->mainUrl,
				'httpheader'		=> $header,
				'timeout'			=> 60,
				'header'			=> 0,
				'followlocation'	=> 1,
				'cookiejar'			=> $this->_cookies_file,
				'cookiefile'		=> $this->_cookies_file,
				'returntransfer'	=> 1,
				'post'				=> true,
				'referer'			=> $this->mainUrl,
				'ssl_verifyhost'	=> 0,
				'SSL_VERIFYPEER'	=> 0,
				'postfields'		=> http_build_query($post_data),
				'useragent'			=> 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:6.0.2) Gecko/20100101 Firefox/6.0.2',
			);
			$this->_ci->my_curl->setup($conf);
			$exc = $this->_ci->my_curl->exc();
			//echo str_get_html($exc);
			//return $this->searchToPage();	
	}
		
	function search(){
		$this->asearch();
		$html = $this->searchToPage();
		$page = str_get_html($html);
		//echo $page;
		$hotel = $page->find('table[class=reser]',0)->find('tr[class=oth]');
		//echo $hotel;
		//$pageing = $page->find('table[class=reser]',0)->find('tr',0)->find('table tr td');
		$data = array();
		for ($i=0; $i < count($hotel); $i++) { 
			$hotelName = $hotel[$i]->find('tr',1)->find('td a',0)->plaintext;
			$address = $hotel[$i]->find('tr',2)->find('td p span',0)->plaintext;
 			$dirtyPrice = $hotel[$i]->find('tr',2)->find('td',1)->find('div p span',0)->plaintext;
			$comaPrice = str_replace('IDR','',$dirtyPrice);
			$explodePrice = explode(',',$comaPrice);
			$cleanPrice = element('0',$explodePrice).element('1',$explodePrice).element('2',$explodePrice).element('3',$explodePrice);
			$y = strip_tags($cleanPrice);
			if (is_numeric($y)==false) {continue;}
			$data[$i]['hotel_name']		=	$hotelName;
			$data[$i]['address']		=	$address;
			$data[$i]['price']			=	$y;
			$data[$i]['log_id']			=	$this->_opt->id;
			$data[$i]['checkin']		=	$this->_opt->checkin;
			$data[$i]['checkout']		=	$this->_opt->checkout;
			$data[$i]['city']			=	$this->_opt->city;
			$data[$i]['meta_data']		=	'';
		}
		return $data;
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
	
	//public function doSearch(){
	public function doSearch($opt = array()){
		
		/*$this->_opt->city 			= '205';
		$this->_opt->checkin 		= '2012-02-01';
		$this->_opt->passangers 	= 2;
		$this->_opt->checkout		= '2012-02-04';
		$this->_opt->id				= 1;*/
		foreach($opt as $key => $val ) $this->_opt->$key = $val;
		$result = $this->search();
		return $result;
		//echo str_get_html($result);
		//$res = $this->dateInterval($this->_opt->checkin,$this->_opt->checkout);
		//return $result;
	}
	
	function monthConvert($month){
		return date( 'M', mktime(0, 0, 0, $month, 1) );
	}
	
	function dateInterval($datein,$dateout){
		$date1 = new DateTime($datein);
		$date2 = new DateTime($dateout);
		$interval = $date2->diff($date1);
		return $interval->days;
	}
	
}