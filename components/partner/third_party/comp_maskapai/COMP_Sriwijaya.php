<? if (! defined('BASEPATH')) exit('No direct script access');

class Sriwijaya extends Comp_maskapai_base{
	
	private $username 			= 'idbooking5';
	private $password 			= 'indonesia';
	private $_default_url 		= 'https://agent.sriwijayaair.co.id/b2b/secure/home.jsp';
	private $_login_url 		= 'https://agent.sriwijayaair.co.id/b2b/secure/j_security_check';
	private $_user_agent 		= 'Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1';
	private $_logout_url 		= 'https://agent.sriwijayaair.co.id/b2b/secure/logout.jsp';
	private $_src_action_url 	= 'https://agent.sriwijayaair.co.id/b2b/secure/AvailabilityAction';
	private $_src_result_url 	= 'https://agent.sriwijayaair.co.id/b2b/secure/createpnr.jsp';
	private $_start_url 		= 'https://agent.sriwijayaair.co.id/b2b/secure/';
	
	function __construct() {
		parent::__construct();
		/*
		foreach(parent::$_opt as $key => $val ){
			$this->_opt->$key = $val;
		}
		*/
		$this->_cookies_file = "./components/partner/third_party/comp_maskapai/cookies/sriwijaya_airline.txt";
		$this->_ci->load->library('my_curl');
		$this->login();
	}
	
	function index() {
		
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
			'j_username' => 'idbooking5',
			'j_password' => 'indonesia',
			
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
		$posted = array(
			'isReturn' => false,
			'from' => 'CGK',
			'to'=>'SUB',
			'departDate1' => 30,
			'departDate2'=>'10-2011',
			'adult' => 1,
			'child' => 0,
			'infant'=> 0,
			'returndaterange' => 0,
			'Submit' => 'Search',
		);
		
		$conf = array(
			'url' 				=> $this->_src_action_url,
			'post' 				=> true,
			'postfields' 		=> http_build_query($posted),
			'timeout'			=> 30,
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
		$res  = $this->_ci->my_curl->exc();
		$info = $this->_ci->my_curl->res_info();		
	
		if($info->httpcode == 302) return $this->topage($info->url);

		return $res;
	
		
		
		
	}
	
	function search(){
		
		
		$roundTrip = false;
		if ($roundTrip) {
			$type = 'return';
		}else{
			$type = 'depart';
		}
		$htmlFile = array(
			"./components/partner/third_party/comp_maskapai/sriwijaya_html/sriwijaya_airlines/createpnr.jsp.html",
			"./components/partner/third_party/comp_maskapai/sriwijaya_html/sriwijaya_noTransit/noreturn.html",
			"./components/partner/third_party/comp_maskapai/sriwijaya_html/sriwijayaSingleFlight/createpnr.jsp.html",
		);
		
		$page = str_get_html($this->_search());
		echo $page;
		$table = $page->find('div[id=pagewrapper] div[id=mainWrapper]',0)->find('form[action=./PNRAction]',0);
		$flight = $page->find('table[id=table_go]',0)->find('tr table[class=flightInfo]');
		$class = $page->find('table[id=table_go] table[class=classTable] td');
		$class1 = $page->find('table[id=table_go] table[class=classTable] td',0);
		$date = $page->find('span[class=avTableLabel2]',0)->plaintext;
		$cdate = explode('-',$date);
		$dateFormated = '20'.$cdate[2].'-'.$this->monthConvert($cdate[1]).'-'.$cdate[0];
		$insideFlight = $flight[0]->find('tr');
		$cntInsideFlight = count($insideFlight);
		$cnt_flight = count($flight);
		$cnt_classFlight = count($class);
		if (!$page) return array();
		$data = array();
		$index=0;
		//echo $cnt_classFlight;
		$cellStatus = array();
		
		for ($i=0; $i < $cnt_classFlight/$cnt_flight; $i++) {
			$cell = $class[$i]->find('input',0);
			if ($cell->getAttribute('disabled') == 'disabled') {
				continue;
			}
			for ($j=0; $j < $cnt_flight; $j++) {
				//$data[$j][$index]['cell']	=  $cell;
				//if ( ($cellStatus[$j][$index] = $cell)  == 'disabled') { continue;}
				//echo $cell[$j];
				
				$clas = $class[$i]->find('span',0)->plaintext;
				$price = $class[$i]->find('span',2)->plaintext;

				$t_depart = $flight[$j]->find('tr',0)->find('td',1)->find('span',1)->plaintext;
				$route_from = $flight[$j]->find('tr',0)->find('td',1)->find('span',0)->plaintext;
				$light_number = $flight[$j]->find('tr',0)->find('td',0)->find('span',0)->plaintext;
				
				if ($cnt_flight == 1 || $cntInsideFlight ==1) {
					$route_arr = $flight[$j]->find('tr',0)->find('td',2)->find('span',0)->plaintext;
					$route_transit = ' ';
					$t_transit_arrive_time = 'No Transit';
					$t_transit_depart_time = 'No Transit';
					$t_arival = $flight[$j]->find('tr',0)->find('td',2)->find('span',1)->plaintext;
				}else {
					$route_arr = $flight[$j]->find('tr',1)->find('span',4)->plaintext;
					$route_transit =  $flight[$j]->find('tr',0)->find('td',2)->find('span',0)->plaintext;
					$t_transit_arrive_time = $dateFormated.' '.$flight[$j]->find('tr',0)->find('td',2)->find('span',1)->plaintext;
					$t_transit_depart_time = $dateFormated.' '.$flight[$j]->find('tr',1)->find('td',1)->find('span',1)->plaintext;
					$t_arival = $flight[$j]->find('tr',1)->find('span',5)->plaintext;
				}
				$cprice = explode(',',$price);
				//print_r($cprice);
				
				$data[$j][$index]['company'] 				= 'SRIWIJAYA';
				$data[$j][$index]['t_depart'] 				= $dateFormated.' '.$t_depart;
				$data[$j][$index]['t_arrival']				= $dateFormated.' '.$t_arival;
				$data[$j][$index]['type'] 					= $type;
				$data[$j][$index]['class'] 					= $clas;
				$data[$j][$index]['price'] 					= $cprice[0].$cprice[1].'000';
				$data[$j][$index]['route'] 					= $route_from.','.$route_arr.','.$route_transit;
				$data[$j][$index]['t_transit_arive'] 		= $t_transit_arrive_time;
				$data[$j][$index]['t_transit_depart_time'] 	= $t_transit_depart_time;
				$data[$j][$index]['meta_key']				= '';
				
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
	
	public function doSearch()
	{
			$this->addResult($this->cleanObject('Sriwijaya/search', array()));
			$this->logOut();
	}
	public function closing()
	{
		$this->logOut();
	}

}