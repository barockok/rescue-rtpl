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
				'server' => 'http://platform.'.DOMAIN_BASE.'/', 
				'http_user' => 'admin',
				'http_pass' => '1234',
				'http_auth' => 'basic',
				
				)
			);
		$this->rest->api_key('abc');
	}
	public function phpinfo()
	{
		echo phpinfo();
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
			if($logid = $this->uri->rsegment(5) AND $func == "doSearch"){
				echo $logid;
				try {
					$log = Search_fare_log::find($logid);
					$param = $log->to_array();
					// reformat the date
					foreach($param as $key => $val){
						if($key == 'date_return' || $key == 'date_depart'){
							if($val != null){
							$param[$key] = show_date($val, 'Y-m-d');
							}
						}
					}
					printDebug($fac->$func($param));
				} catch (Exception $e) {
					$log = false;
				}
			}elseif(!$this->uri->rsegment(5) AND $func == "doSearch"){
				printDebug($fac->$func());
			}else{
				printDebug($fac->$func());
			}
		}
		$fac->closing();
		

		
	}
	
	public function hotel(){
		$hotel 	= $this->uri->rsegment(3);
		$func 	 	= $this->uri->rsegment(4);
		if(!$hotel){
			echo ('no hotel specify');
			exit;
		}
		$this->load->library('service/comp_hotel');
		$fac = $this->comp_hotel->load($hotel);
		if(!$func){
			 echo ('not function specify');
		}else{
			printDebug($fac->$func());
		}
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
	//	http://app.dev-rumahtiket.com/
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
		$this->rest->post('service/airlines/search', $posted);
		$this->rest->debug();
	}
	public function testairlinessearhexec()
	{	$id = $this->uri->rsegment(3);
		$maskapai = $this->uri->rsegment(4);
		$this->rest->get('service/airlines/exec_search/'.$id.'/'.$maskapai);
		$this->rest->debug();
	}
	public function testgetresquery()
	{
		$test = Search_fare_item::find('all' , array(
			'limit' => 10,
			'conditions' => array('log_id = ?', 190 ),
			'order'		=> 'price desc',
			
			
			));
		$result = array();
		foreach($test as $item) array_push($result , $item->to_array() );
		printDebug($result);
	}
	public function testbestfare()
	{
		
	
		$sql = ("SELECT
		    *
		FROM (
		    SELECT
		        company,
		        id,
				price,
				log_id ,
				type,
		        @rn := CASE WHEN @prev_company = company
		                    THEN @rn + 1
		                    ELSE 1
		               END AS rn,
		        @prev_company := company
		    FROM (SELECT @prev_company := NULL) vars, search_fare_items T1
			WHERE log_id = 241
			AND type = 'depart'
		    ORDER BY company, price DESC
		) T2
		WHERE rn <= 5
		ORDER BY T2.price ASC");
	
		
		$con = mysql_connect("localhost", "root", "root");
		if (!$con)
		  {
		  die('Could not connect: ' . mysql_error());
		  }

		$db_selected = mysql_select_db("rt_pre_prod", $con);

//		$sql = "SELECT
//		    *
//		FROM search_fare_items";
		$result = mysql_query($sql, $con);

		while($r[]=mysql_fetch_array($result));

	echo "<pre>";
//= Prints $r as array =================//
print_r ($r);
//=============================//
echo "</pre>";
		//printDebug(mysql_fetch_array($result));
		mysql_close($con);
}
	public function testqueryroundtrip()
	{
		$log = Search_fare_log::find(241);
		$depart_q = array();
		foreach (json_decode($log->complete_comp) as $comp => $status) {
				if($status == FALSE) continue;

				$depart_q_item = Search_fare_item::find('all', array(
							'conditions' => array(
								'log_id = ? AND type = ? AND company = ?',
								$log->id, 'depart', strtoupper($comp)
								),
							'limit' => $log->max_fare,
							'order' => 'price asc',
						)
				);
				if(count($depart_q_item) > 0)
				foreach($this->db_util->multiple_to_array($depart_q_item) as $child_item ) array_push($depart_q, $child_item) ;
				
				
				
			}
		printDebug($depart_q);
	}
	public function test_exec()
	{
	echo	suicide('debug/airlines/test', FALSE);
	}
	public function test_exec_target()
	{
		$new = new Ext_data_airport(array('name' => 'Wdew' , 'code' =>'ZID'));
		$new->save();
	}
	public function logic()
	{
		
			printDebug($this->fetch_formula(333));
			
	}
	public function fetch_formula($id, $limit = 1)
	{
		//try {
			$log = Search_fare_log::find($id);
			$comps = json_decode($log->comp_include);
			$limit = (is_numeric($limit)) ? $limit : $log->max_fare;
			$depart_ids = array(); $return_ids = array();
				$d_q = '';
				for($i = 0 ; $i < count($comps) ; $i++){
						$comp = $comps[$i] ;
						$d_q .= "(select * from search_fare_items where company = '".$comp."' and log_id = ".$id." AND type = 'depart' ORDER BY price ASC limit ".$limit."  )";
						$d_q .= (($i+1) < count($comps)) ? "UNION ALL" : "";
				}
				$d_q .= ' order by price ASC';

				$d_q = Search_fare_log::find_by_sql($d_q);
				if(count($d_q) > 0 ) foreach($d_q as $item) array_push($depart_ids, $item->to_array());
			
			if($log->type == 'roundtrip'){
				
				$r_q = '';
				for($i = 0 ; $i < count($comps) ; $i++){
						$comp = $comps[$i] ;
						$r_q .= "(select * from search_fare_items where company = '".$comp."' and log_id = ".$id." AND type = 'return'  ORDER BY price ASC limit ".$limit." )";
						$r_q .= (($i+1) < count($comps)) ? "UNION ALL" : "";
				}
				$r_q .= ' order by price ASC';

				$r_q = Search_fare_log::find_by_sql($r_q);
				if(count($r_q) > 0 ) foreach($r_q as $item) array_push($return_ids, $item->to_array());
				
				return array(
					'depart' => $depart_ids,
					'return' => $return_ids
				);
			
			}
			return array(
				'log' => $log->to_array(),
				'depart' => $depart_ids,
			);
			
			
		
			
			
		/*	
		} catch (Exception $e) {
			return false;
		}
		*/
		
	}
	public function logic2()
	{
		$array = array(
		array('price' => 10),
		array('price' => 4),
		array('price' => 7),
		array('price' => 2),	
		);
		printDebug(array_sort($array, 'price', SORT_DESC));
	}
	public function alzid4ever()
	{
		echo md5("alzid4ever");
	}
	public function testme()
	{
			$log['log_id'] = '344';
            $log['company'] = 'BATAVIA';
            $log['t_depart'] = '2012-03-10 03:25:00';
            $log['t_arrive'] = '2012-03-10 04:45:00';
            $log['type'] = 'depart';
            $log['class'] = 'E';
            $log['route'] = 'CGK,SUB';
            $log['meta_data'] = '{"comapny":"BATAVIA","flight_no":"345","t_depart":"2012-03-10 03:25","t_arrive":"2012-03-10 04:45","t_transit_arrive":null,"t_transit_depart":null,"type":"depart","price":315200,"class":"E","route":"CGK,SUB","log_id":344,"arrayIndex":"4,4","passangers":1,"time_depart":"2012-3-10","radio_value":"29739124"}';
           
            $log['price'] = 0;
            $log['flight_no'] = 345;
			$new = new Search_fare_item($log);
			$new->save();
			printDebug($new->errors->full_messages());
	}
	public function presystem()
	{
		suicide('suicide/system');
		echo 'Complite ..';
	}
	public function api()
	{
		$endpoint = implode('/',array_slice(explode('/',substr($this->uri->ruri_string(), 1)), 2));
		$res = $this->rest->get($endpoint);
		printDebug($res);
		$this->rest->debug();
	
		
	}
	public function varplay()
	{
		$i = 1;
		$subprocess_1 = 'asuh';
		echo ${'subprocess_'.$i};
	}
	public function testTiketcom()
	{
		$debug = array(
			'name' => '(string)',
			'address' => '(string)',
			'map_coordinate' => '(string)',
			'description' => '(string) clean plain text',
			'policies' => '(array)',
			'start_price' => '(int)',
			'number_of_rooms' => '(int)',
			'pictures' => '(array) url',
			'facilites' => array(
					'hotel' => '(array)',
					'room' => '(array)',
					'sport' => '(array)',
			),
			'class' => array(
				'deluxe' => array(
					'price' => '(int)',
					'facility' => '(array)',
					'discount' => '(int) default 0',
					'picture' => '(string) url',
					'avaibility' => '(int)',
					'includes' => '(array)',
					'room_id' => '(int)',
				),
				'family' => array(
					'price' => '(int)',
					'facility' => '(array)',
					'discount' => '(int) default 0',
					'picture' => '(string) url',
					'avaibility' => '(int)',
					'includes' => '(array)',
					'room_id' => '(int)',
				)
			)
		);
		printDebug($debug);
	}
	public function testencrypt()
	{
		echo decrypt(encrypt('img/business/1/2/business-12-12-2011-13-07-55.l.jpg', 'asas'), 'asas');
	}
	public function testos()
	{
		parse_str('q=band&uid=&startdate=2012-02-18&enddate=2012-02-19&night=1&room=1&adult=2&child=0&minstar=0&maxstar=5&minprice=0&maxprice=1000000000&hotelname=0&page=2', $var);
		printDebug($var);
	}
	public function testos1()
	{
		echo substr('3-Stars Hotel', 0,1);
	}

}
