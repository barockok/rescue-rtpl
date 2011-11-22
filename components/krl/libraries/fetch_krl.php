<?
class Fetch_krl{
	
	public function __construct()
	{
		$this->_ci =& get_instance();
		$this->_ci->load->library('comp/maskapai/my_curl');
		$this->_url_src = 'http://www.kereta-api.co.id/jadwal-ka.html';
		$this->_ci->load->helper('domize');
		
		
	}
	function setSrcOption($opt){
		foreach($opt as $key => $val){
			$this->$key = $val;
		}
	}
	function srcJadwal(){
		$posted = array(
			'tanggal' 		=> '20111123#Rabu, 23 November 2011',
			'origination' 	=> 'GMR#GAMBIR',
			'destination'	=> 'SGU#SURABAYA GUBENG'
		);
		$conf = array(
			'url' => $this->_url_src,
			'post' => true,
			'postfields' => http_build_query($posted),
			'returntransfer' => 1
		);
		
		$this->_ci->my_curl->setup($conf);
		$res = $this->_ci->my_curl->exc();
	//	echo $res;
		$dom = str_get_html($res);
		echo $dom
		->find('div[id=middle-column] div[class=inside]',0)
		->find('table',1)
		->outertext;
	}
	
	
}
