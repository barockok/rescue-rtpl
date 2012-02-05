<?if (! defined('BASEPATH')) exit('No direct script access');
class Merpati_airlines extends MX_Controller {

	private $username 			= 'jkttravelindo';
	private $password 			= 'jkt123456';
	private $kodeagen 			= '13552';
	private $_login_url 		= 'https://www.merpati.co.id/b2b/WebService/BaseService.asmx/UserLogOn';
	private $_refer 			= 'https://www.merpati.co.id/b2b/user.aspx';
	private $_search_url 		= 'https://www.merpati.co.id/b2b/WebService/BaseService.asmx/getFlightAvailabilityForm';
	private $_detail_flight_url = 'https://www.merpati.co.id/b2b/WebService/BaseService.asmx/GetSelectFlight';
	private $_load_step_url 	= 'https://www.merpati.co.id/b2b/WebService/UtilService.asmx/loadstep3';
	
	function __construct() {
		parent::__construct();
		$this->load->library('my_curl');
		$this->load->helper('my_dom');
		$this->_cookies_file = './cookies/merpati_airline.txt';
		$this->_headerData = array(
			'Content-Type: application/json; charset=UTF-8',
		);
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
		$this->my_curl->setup($conf);
		$this->my_curl->exc();
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
		$this->my_curl->setup($conf);
		return $this->my_curl->exc();
	}
	
	function search(){
		$this->login();
			
		$post_data = array(
			'fromAirport'	=> 'CGK', 
			'toAirport'		=> 'PLM',
			'dateFrom'		=> '20111130', //Tanggal Keberangkatan
			'dateTo'		=> '', //Tanggal Kembali di set jika pulang pergi
			'iAdult' 		=> 1, //Jumlah tiket yang di pesan untuk orang dewasa (diatas 12 tahun)
			'iChild'		=> 0, //Jumlah tiket yang dipesan untuk anak kecil (dari umur 2 tahun sampai 12 tahun)
			'iInfant'		=> 0, 
			'BDClass'		=> 'C', //value 'Y' untuk Ekonomi,value 'C' untuk bisnis
			'isSearchGroup'	=> '0', //value bernilai 0 atau 1
			'FareSelect'	=> '',
			'dayRange'		=> '0', //default
		);
		
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
		$this->my_curl->setup($conf);
		$this->my_curl->exc();		
		$ret = $this->mainPage();
	    $p = str_get_html($ret);
		if (!$html = $p->find('table[id=tabOutward] tbody',0)) return array();

		$jumlahPenerbangan = $p->find('table[id=tabOutward] tbody tr');
		$dataPenerbangan = $p->find('table[id=tabOutward] tbody tr',0)->find('td');
		
		$cntPenerbangan = count($jumlahPenerbangan);
		$cntDataPenerbangan = count($dataPenerbangan);
		$dataPenerbanganBerangkat = array();
		for ($i= 1; $i < $cntPenerbangan; $i++) { 
			for ($j=0; $j <= $cntDataPenerbangan; $j++) {
				$dataPenerbanganBerangkat[$i]['codePenerbangan'] = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$p->find('table[id=tabOutward] tbody tr',$i)->find('td',0)->plaintext);		
				$dataPenerbanganBerangkat[$i]['tanggal'] = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$p->find('table[id=tabOutward] tbody tr',$i)->find('td',1)->plaintext).','.preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$p->find('table[id=tabOutward] tbody tr',$i)->find('td',2)->plaintext);
				//$dataPenerbanganBerangkat[$i]['waktuKeberangakatan'] = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$p->find('table[id=tabOutward] tbody tr',$i)->find('td',2)->plaintext);
				$dataPenerbanganBerangkat[$i]['waktuKedatangan'] = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$p->find('table[id=tabOutward] tbody tr',$i)->find('td',3)->plaintext);
				$dataPenerbanganBerangkat[$i]['kelas'] = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$p->find('table[id=tabOutward] tbody tr',$i)->find('td',5)->plaintext);
				//$dataPenerbanganBerangkat[$i]['kursi'] = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$p->find('table[id=tabOutward] tbody tr',$i)->find('td',6)->plaintext);
				$dataPenerbanganBerangkat[$i]['harga'] = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$p->find('table[id=tabOutward] tbody tr',$i)->find('td',7)->plaintext);
				//$dataPenerbanganBerangkat[$i]['cadanganKursi'] = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$p->find('table[id=tabOutward] tbody tr',$i)->find('td',8)->plaintext);
				
				if ($dataPenerbanganBerangkat[$i]['harga'] == 'Tidak tersedia' ) {
					$dataPenerbanganBerangkat[$i]['metaOutwardFlightFareId'] = 'Tidak Tersedia';
				}else {
					$dataPenerbanganBerangkat[$i]['metadata'] = preg_replace(array('/\s{2,}/', '/[\t\n]/'),'',$p->find('table[id=tabOutward] tbody tr',$i)->find('td',7)->find('div[style=display:none] input',0)->getAttribute('value'));
				}
				
				//
			}
		}
		$data = $dataPenerbanganBerangkat;
		print_r($data);		
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
		//echo 

		echo $table1;
		echo $table2;
		print_r($data);
	}
	
	function dumyDetail(){
			$this->login();
			$item = NULL;
			$post_data = array(
				'OutwardFlightFareId'		=> '420126fc-0ca6-41ac-ba3f-20472a34e42f|dc32b938-4587-49b9-b207-c848c1c07d01||',
				'ReturnFlightFareID'		=> '',
				'OutWardDateFligh'			=> '20111130_16_30',
				'OutSelectType'				=> 'FIRM',
				'RetSelectType'				=> 'FIRM',
			);
			$header = array(
				'Content-Type: application/json; charset=utf-8',
				'Host:www.merpati.co.id',
				'Origin: https://www.merpati.co.id',
				'Accept-Language: en-US,en;q=0.8',
				'Accept-Encoding:gzip,deflate,sdch',
				'Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.3',
				'Connection:keep-alive',
				'Cache-Control:	no-cache',
				'Pragma	no-cache',
				//'Accept:*/*',
				//'Content-Length: 207',
			);	


			$conf = array(
				'httpheader'		=> $header,
				'url'				=> $this->_detail_flight_url,
				'timeout'			=> 30,
				'header'			=> 1,
				'followlocation'	=> 1,
				'cookiefile'		=> $this->_cookies_file,
				'returntransfer'	=> 1,
				'post'				=> true,
				'referer'			=> $this->_refer,
				'ssl_verifyhost'	=> 0,
				'postfields'		=> json_encode($post_data),
				'useragent'			=> 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:6.0.2) Gecko/20100101 Firefox/6.0.2',
			);

			$this->my_curl->setup($conf);
			$ret = $this->my_curl->exc();
			$conf1 = array(
				'httpheader'		=> $header,
				'url'				=> $this->_load_step_url,
				'timeout'			=> 30,
				'header'			=> 0,
				'followlocation'	=> 1,
				'cookiefile'		=> $this->_cookies_file,
				'returntransfer'	=> 1,
				'post'				=> true,
				'referer'			=> $this->_refer,
				'ssl_verifyhost'	=> 0,
				'useragent'			=> 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:6.0.2) Gecko/20100101 Firefox/6.0.2',
			);


			$this->my_curl->setup($conf1);
			$ret1 = $this->my_curl->exc();
			
			echo $ret;
			//echo json_encode($post_data);
			//echo $this->_detail_flight_url;
			//echo $this->mainPage();
	}

}?>