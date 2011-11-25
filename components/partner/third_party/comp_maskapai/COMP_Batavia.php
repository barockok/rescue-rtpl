<?
if (! defined('BASEPATH')) exit('No direct script access');

class Batavia extends Comp_maskapai_base {

	private $username = 'jkttravel';
	private $password = '12345';
	private $_login_url = 'http://222.124.141.100/MyPage/loginproses.php';
	private $_referer_url = 'http://222.124.141.100/MyPage/logout.php';
	private $_search_url = 'http://222.124.141.100/MyPage/booking/index.php';
	private $_detail_url = 'http://222.124.141.100/MyPage/booking/cekHarga.php';
	var $route_from;
	var $route_to;
	var $date_depart;
	var $date_return;
	

	//php 5 constructor
	function __construct() {
		parent::__construct();
		
		foreach(parent::$_opt as $key => $val ){
			$this->_opt->$key = $val;
		}
	
		$this->_ci->load->library('my_curl');
		$this->_cookies_file 	= "./components/partner/third_party/comp_maskapai/cookies/batavia_airline.txt";
		$this->login();
	
	}
	function test(){
		echo 'tester';
	}
	function index() {
		echo $this->route;
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
			'postfields'		=> http_build_query($post_data),
			'useragent'			=> 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:6.0.2) Gecko/20100101 Firefox/6.0.2',
		);
		
		$this->_ci->my_curl->setup($conf);
		$this->_ci->my_curl->exc();
	}
	
	function search(){
		$roundTrip = false;
		$dateExplode = explode('-',$this->_opt->date_depart);
		$this->_ci->load->helper('array');
		$year = str_split($dateExplode[0]);
		
		$post_data = array(
			'ruteBerangkat' 		=> $this->_opt->route_from,
			'ruteTujuan' 			=> $this->_opt->route_to,
			'ruteKembali' 			=> 'kembali',
			'tglBerangkatPergi' 	=> $dateExplode[2],
			'blnBerangkatPergi' 	=> $dateExplode[1],
			'thnBerangkatPergi' 	=> $year[2].$year[3],
			'jmlPenumpang' 			=> $this->_opt->passengers,
			'jmlInfant'				=> 0,
		);
		
		$conf = array(
			'url'				=> $this->_search_url,
			'timeout'			=> 30,
			'header'			=> 0,
			'followlocation'	=> 1,
			'cookiejar'			=> $this->_cookies_file,
			'cookiefile'		=> $this->_cookies_file,
			'returntransfer'	=> 1,
			'post'				=> true,
			'referer'			=> $this->_referer_url,
			'ssl_verifyhost'	=> 0,
			'postfields'		=> http_build_query($post_data),
			'useragent'			=> 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:6.0.2) Gecko/20100101 Firefox/6.0.2',
		);
		$this->_ci->my_curl->setup($conf);
		$html = $this->_ci->my_curl->exc();
		$page = str_get_html($html);
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
				
				if ($roundTrip) {
					$type = 'Return';				
				}else{
					$type = 'Depart';
				}
				$tl = $go_wrap->find('tr',$j)->find('td',3)->find('div',0)->plaintext;
				$transit_location = explode('-',$tl);
				if (count($transit_location) > 1) {
					
				}
				if ($tl != '-') {
					$t_transit_arive 	= 'Unknown';
					$t_transit_depart 	= 'Unknown';
					$transitLocation	= ','.$tl;
				}else{
					$t_transit_arive 	= NULL;
					$t_transit_depart	= NULL;
					$transitLocation	= '';
				}			
				
				
				$data[$j][$index]['company'] 			='BATAVIA';
				$data[$j][$index]['t_depart']			= $t_depart;
				$data[$j][$index]['t_arive']			= $t_arrive;
				$data[$j][$index]['t_transit_arive']	= $t_transit_arive;
				$data[$j][$index]['t_transit_depart']	= $t_transit_depart;
				$data[$j][$index]['type']				= $type;
				$data[$j][$index]['price'] 				= $price;
				$data[$j][$index]['class']				= element('0', $head);
				$data[$j][$index]['route']				= $post_data['ruteBerangkat'].','.$post_data['ruteTujuan'].$transitLocation;				
				$data[$j][$index]['meta_key'] 			= $cell->find('input', 0 )->getAttribute('value');
				$index ++;
			}
			
		}
		$data_dep = $data;
		//print_r($data_dep);
		//return $data_dep;
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
		$post_data = array(
			'flightBerangkatPergi'		=> 14574584,
			'tglBerangkatPergi'			=> 30,
			'blnBerangkatPergi'			=> 11,
			'thnBerangkatPergi'			=> 11,
			'classPergi'				=> 'L',
			'jmlPenumpang'				=> 2,
			'jmlInfant'					=> 0,
			'ruteBerangkat'				=> 'CGK',
			'ruteTujuan'				=> 'LUW',
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
			'postfields'		=> http_build_query($post_data),
			'useragent'			=> 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:6.0.2) Gecko/20100101 Firefox/6.0.2',
		);
		
		$this->_ci->my_curl->setup($conf);
		$html = $this->_ci->my_curl->exc();
		//echo $html;
		$page = str_get_html($html);
		$ret = $page->find('div[id=centerright] form[id=cekHarga] table',0);
		$ret1 = $page->find('div[id=centerright] form[id=cekHarga] table',1);
		$countData = count($ret->find('tr',2)->find('td'));
		
		$data['perjalanan'] = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$ret->find('tr',2)->find('td',0)->plaintext);
		$data['tanggal'] = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$ret->find('tr',2)->find('td',1)->plaintext);
		$data['Hari'] = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$ret->find('tr',2)->find('td',2)->plaintext);
		$data['transit'] = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$ret->find('tr',2)->find('td',3)->plaintext);
		$data['rute'] = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$ret->find('tr',2)->find('td',4)->plaintext);
		$data['noPenerbangan'] = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$ret->find('tr',2)->find('td',5)->plaintext);
		$data['keterangan'] = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$ret->find('tr',2)->find('td',6)->plaintext);
		$data['price'] = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$ret1->find('tr',2)->find('td',4)->plaintext);
		$data['class']	= $post_data['classPergi'];
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
	public function doSearch()
	{
		$this->addResult($this->cleanObject('Batavia/search', array()));
	}
}