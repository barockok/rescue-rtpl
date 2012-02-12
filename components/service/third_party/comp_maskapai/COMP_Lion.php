<?

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lion extends Comp_maskapai_base {
	function __construct(){
		parent::__construct();
		ini_set('memory_limit', '120M');
		$this->_cookies_file = './components/service/third_party/comp_maskapai/cookies/lion_air.txt';
		$this->login_url = 'https://agent.lionair.co.id/LionAirAgentsPortal/Default.aspx';
		$this->_refer_url = 'https://agent.lionair.co.id/LionAirAgentsPortal/Default.aspx';
		$this->src_url = 'https://agent.lionair.co.id/LionAirAgentsIBE/Step1.aspx';
		$this->_start_url = 'https://agent.lionair.co.id/LionAirAgentsPortal/Default.aspx';
		$this->_refer2_url = 'https://agent.lionair.co.id/LionAirAgentsIBE/OnlineBooking.aspx';
		$this->step2_url = 'https://agent.lionair.co.id/LionAirAgentsIBE/Step2Availability.aspx';
		$this->username = 'smmsatu';
		$this->password = 'sukses2011';
		$this->_ci->load->library('my_curl');
	}
	
	function topage($url , $return = true){
		$conf = array(
				'url' => $url,
				'cookiejar' 		=> $this->_cookies_file,
				'cookiefile' 		=> $this->_cookies_file,
				'header'			=> 0,
				'nobody'			=> false,
				'returntransfer'	=> 1,
				'SSL_VERIFYPEER'	=> 0,
				'ssl_verifyhost'	=> 0,			
			);
		$this->_ci->my_curl->setup($conf);
		$exc = $this->_ci->my_curl->exc();
		if($return == true ) return $this->_ci->my_curl;
		return $exc;
	}
	
	function sp(){
		$this->login();
		$this->topage('https://agent.lionair.co.id/LionAirAgentsIBE/OnlineBooking.aspx?consID=53298', false);
		echo $this->topage('https://agent.lionair.co.id/LionAirAgentsIBE/OnlineBooking.aspx', false);
		$this->logout();
	}
	
	function start(){
		return str_get_html($this->topage($this->_start_url, false));
	}
	
	function login(){
		$start = $this->start();
		$vkey = $start->find('input[id=__VIEWSTATE]', 0)->getAttribute('value');
		$vVal = $start->find('input[id=__EVENTVALIDATION]', 0)->getAttribute('value');
		$post_data = array(		
			'__EVENTTARGET' 				=> 'btnLogin',
			'__EVENTARGUMENT' 				=> '',
			'__VIEWSTATEKEY' 				=> '',
			'__VIEWSTATE' 					=> $vkey,
			'__EVENTVALIDATION'				=> $vVal,
			'txtLoginName' 					=> $this->username,
			'txtPassword' 					=> $this->password,
			'chkRememberMe' 				=> 'on',
			'NameReqExtend_ClientState' 	=> '',
			'PasswordReqExtend_ClientState' => ''
		);
		
		$conf = array(
			'url' 				=> $this->login_url,
			'timeout'			=> 30,
			'header'			=> 0,
			'nobody'			=> false,
			'followlocation'	=> true,
			'cookiejar' 		=> $this->_cookies_file,
			'cookiefile' 		=> $this->_cookies_file,
			'returntransfer'	=> 1,
			'post'				=> true,
			'referer' 			=> $this->_refer_url,
			'SSL_VERIFYPEER'	=> 0,
			'ssl_verifyhost'	=> 0,
			'postfields' 		=> http_build_query($post_data , NULL, '&'),
			'useragent'			=> 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:6.0.2) Gecko/20100101 Firefox/6.0.2',
		
		);
		
		$this->_ci->my_curl->setup($conf);
		$res = $this->_ci->my_curl->exc();		
	}
	
		
	public function doSearch($opt=array()){		
		//$this->_opt->date_depart =  '2012-01-25';
		$this->_opt->date_depart =  '2012-03-16';
		$this->_opt->date_return =  NULL;
		$this->_opt->passengers = 5;
		$this->_opt->route_from = 'CGK';
		$this->_opt->route_to = 'DPS';
		$this->_opt->id = 1;
		$this->_opt->max_fare = 5;		

		foreach($opt as $key => $val) $this->_opt->$key = $val;
		return $this->src_flight();
	}
	
	function src_flight(){
		$depart_flight = array();
		$return_flight = array();
		if($this->_opt->date_return!=null){
			$depart_flight = $this->src('depart');
			
			//swap route
			$temp = $this->_opt->route_from;
			$this->_opt->route_from = $this->_opt->route_to;
			$this->_opt->route_to = $temp;
			
			//change date
			$this->_opt->date_depart = $this->_opt->date_return;
			
			$return_flight = $this->src('return');
			
		}else{			
			$depart_flight = $this->src('depart');
		}
		
		$this->logout(); //logout after search fare
		return array_merge($depart_flight, $return_flight);
	}
	
	function format_date(){
		$this->formatted_date = str_replace('-','',$this->_opt->date_depart); //remove the (-) from date
		$this->dep_year = substr($this->formatted_date,-8,4); //get year		
		$this->dep_day = substr($this->formatted_date,-2); //get day		
		$this->dep_month = substr($this->formatted_date,-4,2);	//convert numeric month to abbr
		switch ($this->dep_month) {
			case '01':
				$this->dep_month = 'Jan';
				break;
			case '02':
				$this->dep_month = 'Feb';
				break;
			case '03':
				$this->dep_month = 'Mar';
				break;
			case '04':
				$this->dep_month = 'Apr';
				break;
			case '05':
				$this->dep_month = 'Mei';
				break;
			case '06':
				$this->dep_month = 'Jun';
				break;
			case '07':
				$this->dep_month = 'Jul';
				break;
			case '08':
				$this->dep_month = 'Aug';
				break;
			case '09':
				$this->dep_month = 'Sep';
				break;
			case '10':
				$this->dep_month = 'Oct';
				break;
			case '11':
				$this->dep_month = 'Nov';
				break;
			case '12':
				$this->dep_month = 'Dec';
				break;
			default:
				$this->dep_month = 'Unknown';
				break;
		}
		$this->dep_month = $this->dep_month." ".$this->dep_year;
		$this->dep_date = $this->dep_day." ".$this->dep_month;		
	}
	
	function preSearch(){
		$this->login();
		$this->topage('https://agent.lionair.co.id/LionAirAgentsIBE/OnlineBooking.aspx?consID=53298', false);
		$start =  $this->topage('https://agent.lionair.co.id/LionAirAgentsIBE/OnlineBooking.aspx', false);
		$vKey = str_get_html($start)->find('input[id=__VIEWSTATE]', 0)->getAttribute('value');
		
		$this->format_date(); //adjust the date format
		$post_data = array(
			'__EVENTTARGET' => 'UcFlightSelection$lbSearch',
			'__EVENTARGUMENT' => '',			
			'__VIEWSTATE' => $vKey,
			'UcFlightSelection$TripType' => 'rbOneWay',
			'UcFlightSelection$DateFlexibility' => 'rbMustTravel',
			'UcFlightSelection$txtSelOri' => $this->_opt->route_from,
			'UcFlightSelection$txtOri' => $this->_opt->route_from,
			'UcFlightSelection$ddlDepMonth' => $this->dep_month, //Nov 2011
			'UcFlightSelection$ddlDepDay' => $this->dep_day, //17
			'UcFlightSelection$ddlADTCount' => $this->_opt->passengers,
			'UcFlightSelection$txtSelDes' => $this->_opt->route_to,
			'UcFlightSelection$txtDes' => $this->_opt->route_to,
			'UcFlightSelection$ddlCNNCount' => '0',
			'UcFlightSelection$ddlINFCount' => '0',
			'UcFlightSelection$txtDepartureDate' => $this->dep_date,  //17 Nov 2011
			'UcFlightSelection$txtReturnDate' => $this->dep_date,
		);
						
		/*$post_data = array(
			'__EVENTTARGET' =>  'UcFlightSelection$lbSearch',
			'__EVENTARGUMENT' => '',
			'__VIEWSTATE' => $vKey,
			'UcFlightSelection$TripType' => 'rbOneWay',
			'UcFlightSelection$DateFlexibility' => 'rbMustTravel',
			'UcFlightSelection$txtSelOri' => 'BDJ',
			'UcFlightSelection$txtOri' => 'Banjarmasin (BDJ)',
			'UcFlightSelection$ddlDepMonth' => 'Mar 2012',
			'UcFlightSelection$ddlDepDay' => '16',
			'UcFlightSelection$ddlADTCount' => '2',
			'UcFlightSelection$txtSelDes' => 'SUB',
			'UcFlightSelection$txtDes' => 'Surabaya (SUB)',
			'UcFlightSelection$ddlCNNCount' => '0',
			'UcFlightSelection$ddlINFCount' => '0',
			'UcFlightSelection$txtDepartureDate' => '16 Mar 2012',
			'UcFlightSelection$txtReturnDate' => '24 Mar 2012',			
				);*/
		
		//print_r($post_data);
		
		
		$conf = array(
			'url' 				=> $this->src_url,
			'timeout'			=> 150,
			'header'			=> 1,
			'nobody'			=> false,
			'followlocation'	=> true,
			'cookiejar' 		=> $this->_cookies_file,
			'cookiefile' 		=> $this->_cookies_file,
			'returntransfer'	=> 1,
			'post'				=> true,
			'referer' 			=> $this->_refer2_url,
			'SSL_VERIFYPEER'	=> 0,
			'ssl_verifyhost'	=> 0,
			'postfields' 		=> http_build_query($post_data , NULL, '&'),
			'useragent'			=> 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:6.0.2) Gecko/20100101 Firefox/6.0.2',
		);
		
		$this->_ci->my_curl->setup($conf);
		$res  = $this->_ci->my_curl->exc();
		$info = $this->_ci->my_curl->res_info();
						
		if($info->http_code == 302) 
			$res = $this->topage($info->url);
		return $res;
	}

	function src($flight_type){
		

								
		$final_data = array();
		$dom = str_get_html($this->preSearch());
		
		$vKey = $dom->find('input[id=__VIEWSTATE]', 0)->getAttribute('value');
			
		$flight_table = $dom->find('table[id=tblOutFlightBlocks] tbody',0);
		$total_flight = count($dom->find('table[id=tblOutFlightBlocks] tbody tr')) - 2; //get total flight by count rows in table
		$idx = 0;				
		
		for($i=21;$i>=4;$i--){
			
			if($idx==($this->_opt->max_fare)) break; //jika loop searching fare sudah mencukupi
											
			for($j=2;$j<$total_flight;$j++){
																								
				$row = $flight_table->find('tr',$j);
				$rowID = $row->getAttribute('id');
				
									
				if($row->find('td',$i)->getAttribute('rowspan')==1) continue; //jika tidak ada flight
																
				$class_cell = $row->find('td',$i);								
				$cellID = $class_cell->find('input',0)->getAttribute('value');
				
				if($class_cell->find('input[type=radio]',0)->getAttribute('disabled') == 'disabled') continue; //jika flight penuh
								
				$sel_text = explode('|', $row->find('td',1)->plaintext);					
				//print_r($sel_text);
																				
				$route = substr($sel_text[6],-6,3).','.substr($sel_text[6],-3); //define route
				$t_ = explode(' ',$sel_text[8]); //split dep_time and arr_time
				//print_r($t_);
					
				//format departure time
				$dep_time_a = substr($t_[0],-2);
				$dep_time_b = substr($t_[0],-4,2);
				$dep_time = $dep_time_b.":".$dep_time_a;
				$t_depart = date('Y-m-d H:i:s',strtotime($this->_opt->date_depart." ".$dep_time));
																
				//format arrival time
				$arr_time_a = substr($t_[1],-2);
				$arr_time_b = substr($t_[1],-4,2);
				$arr_time = $arr_time_b.":".$arr_time_a;
				$t_arrive = date('Y-m-d H:i:s',strtotime($this->_opt->date_depart." ".$arr_time));
				
				$fare_class = substr($class_cell->find('span',0)->getAttribute('title'),0,1);
				$flight_no = $sel_text[1];
				$seat_available = $class_cell->find('label',0)->plaintext;
				
				//define return variable
				$final_data[$idx]=array(
					'pre_meta_fare' => array('cell' => $cellID , 'row' => $rowID),
				 // 'price' => $this->getPrice($vKey,$cellID,$rowID),
					'company' => 'LION',
					't_depart' => $t_depart,	//depart from origin location
					't_transit_arrive' => '', //arrive in transit airport
					't_transit_depart' => '', //depart from transit airport
					't_arrive' => $t_arrive,
					'type' => $flight_type, //depart or return
					'class' => $fare_class,
					'flight_no' => $flight_no,
					'log_id' => $this->_opt->id,					
					'route' => $route,
					'meta_data' => json_encode(array(
						'seat_available' => $seat_available,
						'flight_number_transit' => '',
						'rowID' => $rowID,
						'cellID' => $cellID,
						'passenger' => $this->_opt->passengers					
					))
				);												
				$idx++;
				if($idx==($this->_opt->max_fare)) break;
			}																			
		}
		// return $final_data;			
		// FINAL PRICE is HERE					
		return $this->simultan_fetch_price($final_data, $vKey);
	}
	public function simultan_fetch_price($pre_fare_data, $vKey)
	{
		if(is_array($pre_fare_data) && count($pre_fare_data) > 0 ){
			$limit = count($pre_fare_data);
			
			############# Preparation ###############
			for ($i=0; $i < $limit; $i++) { 
				$prefare_meta = element('pre_meta_fare' , $pre_fare_data[$i] );
				$dirty_curl_opt = $this->_prepare_price($vKey, element('cell', $prefare_meta), element('row', $prefare_meta ));
				// covert to curl option constant
				$clean_curl_opt = array();
				foreach($dirty_curl_opt as $key => $value){
					$name = constant('CURLOPT_'.strtoupper($key));
					$val  = $value;
					$clean_curl_opt[$name] = $val;
				}
				
				// declare the subprocsee curll 
				${'getting_fare_'.$i} = curl_init();
				// Flag as dynamic variable in loop
				$sub_curl = ${'getting_fare_'.$i};
				// adding clean option
				curl_setopt_array($sub_curl, $clean_curl_opt);
			}
			############# Declare Master Curl 	###############
			$master_process = curl_multi_init();
			
			############# Add sub process to master ###############
			for ($i=0; $i < $limit ; $i++) { 
				$sub = 	${'getting_fare_'.$i};
				curl_multi_add_handle($master_process,$sub);
			}
			
			######### execute the all pros with master parallely ###############
			$active = null;

			do {
			    $mrc = curl_multi_exec($master_process, $active);
			} while ($mrc == CURLM_CALL_MULTI_PERFORM);

			while ($active && $mrc == CURLM_OK) {
			    if (curl_multi_select($master_process) != -1) {
			        do {
			            $mrc = curl_multi_exec($master_process, $active);
			        } while ($mrc == CURLM_CALL_MULTI_PERFORM);
			    }
			}
			
			##### remove all sub processs from master #######
			for ($i=0; $i < $limit ; $i++) { 
				$sub = 	${'getting_fare_'.$i};
				curl_multi_remove_handle($master_process, $sub);
			}
			
			##### EXTRACt EACH RESULt ###/
			$whole_result = array();
			for ($i=0; $i < $limit; $i++) { 
				$sub = 	${'getting_fare_'.$i};
				array_push($whole_result, curl_multi_getcontent($sub));
			}
			
			#### CLOSING MASTER PROCESS #####
			curl_multi_close($master_process);
			
			### place the price base on this index ###
			for ($i=0; $i < $limit; $i++) { 
				$pre_fare_data[$i]['price'] = $this->_clean_price($whole_result[$i]) ;
				// remove pre_meta_fare
				unset($pre_fare_data[$i]['pre_meta_fare']);
			}
			
			return $pre_fare_data;
			
		}else{
			return array();
		}
	}
	private function _clean_price($raw_result = null)
	{
		if(!is_string($raw_result) || $raw_result == null ) return '0';
		
		return str_replace(',','',str_get_html($raw_result)->find('td[id=tdAmtTotal]',0)->plaintext);
	}
	private function _prepare_price($vKey=null,$cellID,$rowID)
	{
		$post_data = array(
			'ScriptManager1' => 'upnlTotalTripCost|btnPriceSelection',
			'__EVENTTARGET' => 'btnPriceSelection',
			'__EVENTARGUMENT' => '', 
			'__LASTFOCUS' => '',
			//'__VIEWSTATE' => $vKey,
			'txtUpdateInsurance' => 'Yes',
			'Insurance$txtInsPostbackRequired' => 'no',
			'txtPricingResponse' => '',
			'txtOutFBCsUsed' => '',
			'txtInFBCsUsed' => '',
			'txtTaxBreakdown' => '',
			'UcFlightSelection$TripType' => 'rbOneWay',
			'UcFlightSelection$DateFlexibility' => 'rbMustTravel',
			'UcFlightSelection$txtSelOri' => $this->_opt->route_from,//'BDJ',
			'UcFlightSelection$txtOri' => $this->_opt->route_from,//'Banjarmasin (BDJ)',
			'UcFlightSelection$ddlDepMonth' => $this->dep_month,//'Mar 2012',
			'UcFlightSelection$ddlDepDay' => $this->dep_day,//'16',
			'UcFlightSelection$ddlADTCount' => $this->_opt->passengers,//2,
			'UcFlightSelection$txtSelDes' => $this->_opt->route_to,//'SUB',
			'UcFlightSelection$txtDes' => $this->_opt->route_to,//'Surabaya (SUB)',
			'UcFlightSelection$ddlRetMonth' => $this->dep_month,//'Mar 2012',
			'UcFlightSelection$ddlRetDay' => $this->dep_day,//'17',
			'UcFlightSelection$ddlCNNCount' => 0,
			'UcFlightSelection$ddlINFCount' => 0,
			'UcFlightSelection$txtDepartureDate' => $this->dep_date,//'16 Mar 2012',
			'UcFlightSelection$txtReturnDate' => $this->dep_date,//'17 Mar 2012',
			'txtOBNNCellID' => $cellID,
			'txtIBNNCellID' => 'oneway',
			'txtOBNNRowID' => $rowID,
			'txtIBNNRowID' => '',
			'txtUserSelectedOneway' => '',
			'__ASYNCPOST' => true,
		);
		
		$conf = array(
			'url' 				=> $this->step2_url,
			'timeout'			=> 150,
			'header'			=> 1,
			'nobody'			=> false,
			'followlocation'	=> true,
			'cookiejar' 		=> $this->_cookies_file,
			'cookiefile' 		=> $this->_cookies_file,
			'returntransfer'	=> 1,
			'post'				=> true,
			'referer' 			=> $this->_refer2_url,
			'SSL_VERIFYPEER'	=> 0,
			'ssl_verifyhost'	=> 0,
			'postfields' 		=> http_build_query($post_data , NULL, '&'),
			'useragent'			=> 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:6.0.2) Gecko/20100101 Firefox/6.0.2',
		);
		return $conf;
	}
	function getPrice($vKey=null,$cellID,$rowID){		
		$post_data = array(
			'ScriptManager1' => 'upnlTotalTripCost|btnPriceSelection',
			'__EVENTTARGET' => 'btnPriceSelection',
			'__EVENTARGUMENT' => '', 
			'__LASTFOCUS' => '',
			//'__VIEWSTATE' => $vKey,
			'txtUpdateInsurance' => 'Yes',
			'Insurance$txtInsPostbackRequired' => 'no',
			'txtPricingResponse' => '',
			'txtOutFBCsUsed' => '',
			'txtInFBCsUsed' => '',
			'txtTaxBreakdown' => '',
			'UcFlightSelection$TripType' => 'rbOneWay',
			'UcFlightSelection$DateFlexibility' => 'rbMustTravel',
			'UcFlightSelection$txtSelOri' => $this->_opt->route_from,//'BDJ',
			'UcFlightSelection$txtOri' => $this->_opt->route_from,//'Banjarmasin (BDJ)',
			'UcFlightSelection$ddlDepMonth' => $this->dep_month,//'Mar 2012',
			'UcFlightSelection$ddlDepDay' => $this->dep_day,//'16',
			'UcFlightSelection$ddlADTCount' => $this->_opt->passengers,//2,
			'UcFlightSelection$txtSelDes' => $this->_opt->route_to,//'SUB',
			'UcFlightSelection$txtDes' => $this->_opt->route_to,//'Surabaya (SUB)',
			'UcFlightSelection$ddlRetMonth' => $this->dep_month,//'Mar 2012',
			'UcFlightSelection$ddlRetDay' => $this->dep_day,//'17',
			'UcFlightSelection$ddlCNNCount' => 0,
			'UcFlightSelection$ddlINFCount' => 0,
			'UcFlightSelection$txtDepartureDate' => $this->dep_date,//'16 Mar 2012',
			'UcFlightSelection$txtReturnDate' => $this->dep_date,//'17 Mar 2012',
			'txtOBNNCellID' => $cellID,
			'txtIBNNCellID' => 'oneway',
			'txtOBNNRowID' => $rowID,
			'txtIBNNRowID' => '',
			'txtUserSelectedOneway' => '',
			'__ASYNCPOST' => true,
		);
		
		$conf = array(
			'url' 				=> $this->step2_url,
			'timeout'			=> 150,
			'header'			=> 1,
			'nobody'			=> false,
			'followlocation'	=> true,
			'cookiejar' 		=> $this->_cookies_file,
			'cookiefile' 		=> $this->_cookies_file,
			'returntransfer'	=> 1,
			'post'				=> true,
			'referer' 			=> $this->_refer2_url,
			'SSL_VERIFYPEER'	=> 0,
			'ssl_verifyhost'	=> 0,
			'postfields' 		=> http_build_query($post_data , NULL, '&'),
			'useragent'			=> 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:6.0.2) Gecko/20100101 Firefox/6.0.2',
		);
		
		$this->_ci->my_curl->setup($conf);
		$res  = $this->_ci->my_curl->exc();
		
		$price = str_replace(',','',str_get_html($res)->find('td[id=tdAmtTotal]',0)->plaintext);
		
		return $price;
		
	}	
	
	function doBook(){
		
	}
	
	function logout(){
		$this->topage($this->_start_url);
	}
	
	function closing(){
		$this->logout();
	}
	
}