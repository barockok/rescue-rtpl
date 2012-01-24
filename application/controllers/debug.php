<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* 
*/
class Debug extends MX_Controller
{
	
	function __construct()
	{
		parent::__construct();
			$this->load->library('rest', array(
				'server' => 'http://app.dev-rumahtiket.com/', 
				'http_user' => 'admin',
				'http_pass' => '1234',
				'http_auth' => 'basic',
				
				)
			);
		$this->rest->api_key('abc');
	}
	public function maskapai()
	{
		$maskapai 	= $this->uri->rsegment(3);
		$func 	 	= $this->uri->rsegment(4);
		if(!$maskapai){
			echo ('no maskapai specify');
			exit;
		}
		$this->load->library('service/comp_maskapai');
		$fac = $this->comp_maskapai->load($maskapai);
		
		if(!$func){
			 echo ('not function specify');
		}else{
			printDebug($fac->$func());
		}
		$fac->closing();
		
		
	}
	
	public function test1()
	{
		
		$q = Article::all(array('conditions' => array("content LIKE ?", '%garuda%')));
		printDebug($q);
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
	public function testcart()
	{
		$new_cart = array(
			'cart_id' => 'd07623166ff9454c5f0be22fd3b2722e', 
			'name' => 'test 2',
			'qty' => 1
			
		);
		
		$cart = new Cart_item($new_cart);
		$cart->save();
		printDebug(
			$cart->cart->to_array(
				array('include' => array('items') )
				) 
			);
		
		
	}
	public function testvalidatearray()
	{
		$should = array('id', 'name', 'address_details');
		$check = array('id' => 56, 'name' => 'zidni');
		$validate = validate_array($should, $check);
		if($validate->is_valid) printDebug($validate->data) ;
		else echo 'you got wrong, check this '.$validate->unvalid_text;
	}
	public function testcreatecart()
	{
			$this->load->library('rest', array(
				'server' => 'http://app.dev-rumahtiket.com/', 
				'http_user' => 'admin',
				'http_pass' => '1234',
				'http_auth' => 'basic',
				
				)
			);
		$this->rest->api_key('abc');
		$this->rest->post('service/shoppingcart/create/json', array('user_id' => 26, 'currency'=> 'IDR'));
		$this->rest->debug();
	}
	public function testretrivecart()
	{
	
		$this->rest->get('service/shoppingcart/view/fdb6f2bcaf62f77dc76fe5c8c928740a/format/json');
		$this->rest->debug();
	}
	public function testdeletecart()
	{
		$this->rest->delete('service/shoppingcart/delete/fdb6f2bcaf62f77dc76fe5c8c928740a/format/json');
		$this->rest->debug();
	}
	public function testcartadditem()
	{
		$this->rest->post('service/shoppingcart/add_item/ba79988f0db02a15d4f00304fd7dcb4c/format/json', array(
			'qty' => 3,
			'name' => 'wede',
			'price' => 15000,
			'subtotal' => 5000,
			'type' => 'airlines	'
			));
		$this->rest->debug();
	}
	public function testcartupdateitem()
	{
		$this->rest->post('service/shoppingcart/update_item/092c30453bf313d6343034f86fac25a6/format/json', array('name' => 'product sample2', 'qty' => null));
		$this->rest->debug();
	}
	public function testcartdeleteitem()
	{
		$this->rest->delete('service/shoppingcart/delete_item/cd566e55898c40e970fb203c501fc40c/format/json');
		$this->rest->debug();
	}
	public function testcarthookcallpost()
	{
		$this->rest->post('service/shoppingcart/hook_call/format/json');
		$this->rest->debug();
	}
	public function testdevnull()
	{
	
		printDebug($this->rest->get('service/airlines/test2', FALSE));
		echo 'test me';
	}
	public function testairlinessearchpost()
	{
		
			$posted = array(
				'depart' 	=> '2012-02-10',
				'return' 	=> '2012-02-15',
				'from' 	=> 'CGK',
				'to'    	=> 'SUB',
				'passengers'	=> 1,
				'airlines'  => 'Sriwijaya,Batavia,Garuda,Merpati,Citilink ',
				'max_fare'		=> 10,
			);
		$this->rest->post('service/airlines/search/format/json', $posted);
		$this->rest->debug();
	}
	public function testairlinessearhexec()
	{	$maskapai = $this->uri->rsegment(3);
		$this->rest->get('service/airlines/exec_search/187/'.$maskapai);
		$this->rest->debug();
	}
}
