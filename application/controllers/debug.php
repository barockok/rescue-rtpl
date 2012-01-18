<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* 
*/
class Debug extends REST_Controller
{
	
	function __construct()
	{
		parent::__construct();
	}
	public function maskapai()
	{
		$maskapai 	= $this->uri->rsegment(3);
		$func 	 	= $this->uri->rsegment(4);
		if(!$maskapai){
			echo ('no maskapai specify');
			exit;
		}
		$this->load->library('partner/comp_maskapai');
		$fac = $this->comp_maskapai->load($maskapai);
		
		if(!$func){
			 echo ('not function specify');
		}else{
			printDebug($fac->$func());
		}
		$fac->closing();
		
		
	}
	public function test2()
	{
	//	$this->load->library('acurl');
		$this->load->library('rest', array(
			'server' => 'http://app.dev-rumahtiket.com/', 
			'http_user' => 'admin',
			'http_pass' => '1234',
			'http_auth' => 'basic',
			)
		);
		
		$this->rest->api_key('abc', 'X-API-KEY');
		$conf = array(
					'options' => 
							array(
								'conditions' => array('id = ?' ,82),
								'limit' => 1
							),
					'serialize' => array(
						'include' => array('items'),
					)
				
				);
		$res = $this->rest->post('db/find/search_fare_log/all', $conf, 'json');
//		$res = $this->rest->get('debug/test4',null, 'json');
		$this->rest->debug();
		printDebug($res);
		
	}
	public function test4()
	{
		$tes = Search_fare_item::all(array('include' => array('log')));
		$res = array();
		/*
		foreach($tes as $item){
			array_push($res, $item->to_array() );
		}
		*/
		printDebug($tes);
	}
	public function test3()
	{
		http://app.dev-rumahtiket.com/
		$this->load->library('rest', array(
			'server' => 'http://api.rumahtiket.com/', 
			'http_user' => 'admin',
			'http_pass' => '1234',
			'http_auth' => 'basic',
			)
		);
		$this->rest->api_key('abc', 'X-API-KEY');
		$res = $this->rest->post('db/find/user/all');
		
		
		printDebug($res);
		$this->rest->debug();
		
		
	}
	public function test5()
	{
	}
	public function test6()
	{
		echo CURLAUTH_ANY;
	}
	public function test7()
	{
		$conf = array(
			'bill_name' => 'Zidni Mubarock',
			'bill_phone' => '62363269',
			'bill_mobile' => '49648939',
			'bill_email' => 'zidmubarock@gmail.com',
			'user_id'	=> '26',
			'depart_fare_id' => '189',
			'return_fare_id' => '300',
			
		);
		$fare_data = new Flight_booking($conf);
		if(!$fare_data->is_valid()){
			printDebug($fare_data->errors->full_messages());
		}
	}
	public function test8()
	{
		$q = Flight_booking_data::find('all');
		foreach($q as $item){
		printDebug($item->to_array(array('include' => array('fare_data', 'flight_booking'))));
		}
	}
	public function test9_get()
	{	
		hj;
	}
}
