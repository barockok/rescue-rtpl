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
	
/*dummy
$dataPenerbanganBerangkat[$i]['cadanganKursi'] = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$p->find('table[id=tabOutward] tbody tr',$i)->find('td',8)->plaintext);
$dataPenerbanganBerangkat[$i]['kursi'] = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$p->find('table[id=tabOutward] tbody tr',$i)->find('td',6)->plaintext);
//$dataPenerbanganBerangkat[$i]['waktuKeberangakatan'] = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$p->find('table[id=tabOutward] tbody tr',$i)->find('td',2)->plaintext);
//$dataPenerbanganBerangkat[$i]['waktuKedatangan'] = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$p->find('table[id=tabOutward] tbody tr',$i)->find('td',3)->plaintext);
$dataPenerbanganBerangkat[$i]['codePenerbangan'] = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$p->find('table[id=tabOutward] tbody tr',$i)->find('td',0)->plaintext);

*/
	
	function __construct() {
		parent::__construct();
		$this->_ci->load->library('comp/maskapai/my_curl');
		$this->_cookies_file = "./components/partner/third_party/comp_maskapai/cookies/merpati_airline.txt";
		
		$this->_headerData = array(
			'Content-Type: application/json; charset=UTF-8',
		);
		
		$this->_opt = new stdClass();
		$this->_opt->date_depart = '2011-11-30';
		$this->_opt->passengers = 1;
		$this->_opt->route_from = 'CGK';
		$this->_opt->route_to = 'PLM';
	}
	
	
	function index() {
		
		echo 'Merpati';
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
	
	function search(){
		
		$this->login();
		$roundTrip = false;
		$dateExplode = explode('-',$this->_opt->date_depart);
		$post_data = array(
			'fromAirport'	=> $this->_opt->route_from, 
			'toAirport'		=> $this->_opt->route_to,
			'dateFrom'		=> $dateExplode[0].$dateExplode[1].$dateExplode[2], //Tanggal Keberangkatan
			'dateTo'		=> '', //Tanggal Kembali di set jika pulang pergi
			'iAdult' 		=> $this->_opt->passengers , //Jumlah tiket yang di pesan untuk orang dewasa (diatas 12 tahun)
			'iChild'		=> 0, //Jumlah tiket yang dipesan untuk anak kecil (dari umur 2 tahun sampai 12 tahun)
			'iInfant'		=> 0, 
			'BDClass'		=> 'Y', //value 'Y' untuk Ekonomi,value 'C' untuk bisnis
			'isSearchGroup'	=> '0', //value bernilai 0 atau 1
			'FareSelect'	=> '',
			'dayRange'		=> '0', //default
		);
		//echo $post_data['dateFrom'];
		$conf = array(
			'httpheader'		=> $this->_headerData,
			'url'				=> $this->_search_url,
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
		$ret = $this->mainPage();
	    $p = str_get_html($ret);
		if (!$html = $p->find('table[id=tabOutward] tbody',0)) return array();

		$jumlahPenerbangan = $p->find('table[id=tabOutward] tbody tr');
		$dataPenerbangan = $p->find('table[id=tabOutward] tbody tr',0)->find('td');
		
		$cntPenerbangan = count($jumlahPenerbangan);
		$cntDataPenerbangan = count($dataPenerbangan);
		$dataPenerbanganBerangkat = array();
		$index = 0;
		$this->_ci->load->helper('array');
		for ($i= 1; $i < $cntPenerbangan; $i++) { 
			for ($j=0; $j <= $cntDataPenerbangan; $j++) {
				$jml_kursi = $p->find('table[id=tabOutward] tbody tr',$i)->find('td');
				
				if ($jml_kursi[6] == 'C') { continue;}
				
				if ($roundTrip) {
					$t_return = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$p->find('table[id=tabOutward] tbody tr',$i)->find('td',1)->plaintext).','.preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$p->find('table[id=tabOutward] tbody tr',$i)->find('td',2)->plaintext);
					$type = 'Return';
				}else{
					$t_return = '';
					$type = 'Depart';
				}
				
				$date = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$p->find('table[id=tabOutward] tbody tr',1)->find('td',1)->plaintext);
				$departTime = str_split(preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$p->find('table[id=tabOutward] tbody tr',$i)->find('td',2)->plaintext),5);
				$arrivalTime = str_split(preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$p->find('table[id=tabOutward] tbody tr',$i)->find('td',3)->plaintext),5);
				$class = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$p->find('table[id=tabOutward] tbody tr',$i)->find('td',5)->plaintext);
				$price = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$p->find('table[id=tabOutward] tbody tr',$i)->find('td',7)->plaintext);
				
				$cprice = str_replace(',','',$price);
				$cleanPrice = explode('.',$cprice);
				$cnt_jml_kursi = str_split($jml_kursi[6]->plaintext);
				$cdate = explode('/',$date);
				$formatedDate = $cdate[2].'-'.$cdate[1].'-'.$cdate[0];
				
				if (count($cnt_jml_kursi) == 1) { 
					$t_transit_depart = NULL;
					$t_transit_arive = NULL;
					$t_arive = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$p->find('table[id=tabOutward] tbody tr',$i)->find('td',3)->plaintext);
					$cnt_jml_kursi = str_split($p->find('table[id=tabOutward] tbody tr',2)->find('td',6)->plaintext);
					$d_t_arrive = NULL;
					$d_t_depart = NULL;
				}else{
					
					
					$t_transit_depart = element(1, $departTime);
					$t_transit_arive =  element(0, $arrivalTime);
					$t_arive = element(1, $arrivalTime);
					$d_t_arrive = $formatedDate;
					$d_t_depart = $formatedDate;
				}
				
				$dataPenerbanganBerangkat[$i]['company'] 			= 'MERPATI';		
				$dataPenerbanganBerangkat[$i]['t_depart'] 			= $formatedDate.' '.element(0, $departTime);
				$dataPenerbanganBerangkat[$i]['t_transit_arrive'] 	= $d_t_arrive.' '.$t_transit_arive;
				$dataPenerbanganBerangkat[$i]['t_transit_depart']   = $d_t_depart.' '.$t_transit_depart;
				$dataPenerbanganBerangkat[$i]['t_arrive']			= $formatedDate.' '.$t_arive;
				$dataPenerbanganBerangkat[$i]['type'] 				= $type;
				$dataPenerbanganBerangkat[$i]['class'] 				= $class;
				$dataPenerbanganBerangkat[$i]['price'] 				= $cleanPrice[0];
				$dataPenerbanganBerangkat[$i]['route'] 				= $post_data['fromAirport'].','.$post_data['toAirport'];
				$dataPenerbanganBerangkat[$i]['meta_key']			= '';
				}
			}
			//return array('b', 'm');
			return $dataPenerbanganBerangkat;
		}
	
	//don't delete this
	
	function detail(){
		$page = file_get_html('./htmlDumy/merpati_dumyNoTransit/Merpati Nusantara Airlines.html');
		$table1 = $page->find('div[class=WrapperTBLStep3] span tbody',0);
		$table2 = $page->find('div[class=WrapperTBLStep3] div[id=dvQuoute] span tbody',0);
		$cnt_detailDataPenerbangan = count($table1->find('tr',0)->find('td'));
		$cnt_detailJmlPenerbangan = count($table1->find('tr'));
		$data = array();
		if(!$table1 && !$table2) return array();
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

		echo $table1;
		echo $table2;
		print_r($data);
	}
	public function doSearch()
	{
		$this->addResult($this->search());
	}

}