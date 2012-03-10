<?

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lion extends Comp_maskapai_base {
	function __construct(){
		parent::__construct();
		ini_set('memory_limit', '120M');
		$this->_cookies_file = realpath('./components/service/third_party/comp_maskapai/cookies/lion_air.txt');		
		$this->login_url = 'https://agent.lionair.co.id/LionAirAgentsPortal/Default.aspx';
		$this->_refer_url = 'https://agent.lionair.co.id/LionAirAgentsPortal/Default.aspx';
		$this->src_url = 'https://agent.lionair.co.id/LionAirAgentsIBE/Step1.aspx';
		$this->_start_url = 'https://agent.lionair.co.id/LionAirAgentsPortal/Default.aspx';
		$this->_refer2_url = 'https://agent.lionair.co.id/LionAirAgentsIBE/OnlineBooking.aspx';
		$this->step2_url = 'https://agent.lionair.co.id/LionAirAgentsIBE/Step2Availability.aspx';
		$this->book_url = 'https://agent.lionair.co.id/LionAirAgentsIBE/Step3NoTicketing.aspx';
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
			'chkRememberMe' 				=> 'off',
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
		foreach($opt as $key => $val) $this->_opt->$key = $val;
		$fare_result = $this->src_flight();
				
		if($fare_result == null) 
			throw new ResultNotFound();
		else 
			return $fare_result;
	}
	
	function getDetail($flight_data){
		$this->login();
		
		$meta_data = json_decode($flight_data['meta_data']);
		
		$post_data = array(
			'ScriptManager1' => 'upnlTotalTripCost|btnPriceSelection',
			'__EVENTTARGET' => 'btnPriceSelection',
			'__EVENTARGUMENT' => '', 
			'__LASTFOCUS' => '',			
			'txtUpdateInsurance' => 'Yes',
			'Insurance$txtInsPostbackRequired' => 'no',
			'txtPricingResponse' => '',
			'txtOutFBCsUsed' => '',
			'txtInFBCsUsed' => '',
			'txtTaxBreakdown' => '',
			'UcFlightSelection$TripType' => 'rbOneWay',
			'UcFlightSelection$DateFlexibility' => 'rbMustTravel',			
			'UcFlightSelection$txtSelOri' => $flight_data['route_from'],
			'UcFlightSelection$txtOri' => $flight_data['route_from'],
			'UcFlightSelection$ddlDepMonth' => date('M Y', strtotime($flight_data['t_depart'])),//'Apr 2012',
			'UcFlightSelection$ddlDepDay' => date('d', strtotime($flight_data['t_depart'])),//'20',
			'UcFlightSelection$ddlADTCount' => $flight_data['adult'],
			'UcFlightSelection$txtSelDes' => $flight_data['route_to'],
			'UcFlightSelection$txtDes' => $flight_data['route_to'],
			'UcFlightSelection$ddlRetMonth' => date('M Y', strtotime($flight_data['t_depart'])),//'Apr 2012',
			'UcFlightSelection$ddlRetDay' => date('d', strtotime($flight_data['t_depart'])),//'20',
			'UcFlightSelection$ddlCNNCount' => $flight_data['child'],
			'UcFlightSelection$ddlINFCount' => $flight_data['infant'],
			'UcFlightSelection$txtDepartureDate' => date('d M Y', strtotime($flight_data['t_depart'])),//'Apr 2012',,
			'UcFlightSelection$txtReturnDate' => date('M Y', strtotime($flight_data['t_depart'])),//'21 Apr 2012',
			'txtOBNNCellID' => $meta_data->cellID,
			'txtIBNNCellID' => 'oneway',
			'txtOBNNRowID' => $meta_data->rowID,									
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
		
		$flight_data['price'] = $price;
		
		return $flight_data;
		$this->logout();
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
			'UcFlihtSelection$txtOri' => $this->_opt->route_from,
			'UcFlightSelection$ddlDepMonth' => $this->dep_month, //Nov 2011
			'UcFlightSelection$ddlDepDay' => $this->dep_day, //17
			'UcFlightSelection$ddlADTCount' => $this->_opt->adult,
			'UcFlightSelection$txtSelDes' => $this->_opt->route_to,
			'UcFlightSelection$txtDes' => $this->_opt->route_to,
			'UcFlightSelection$ddlCNNCount' => $this->_opt->child,
			'UcFlightSelection$ddlINFCount' => $this->_opt->infant,
			'UcFlightSelection$txtDepartureDate' => $this->dep_date,  //17 Nov 2011
			'UcFlightSelection$txtReturnDate' => $this->dep_date,
		);
					
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
				
				$class = substr($class_cell->find('span',0)->getAttribute('title'),0,3);
				$fare_class = substr($class,0,1);
				$flight_no = $sel_text[1];
				$seat_available = $class_cell->find('label',0)->plaintext;
				
				//define return variable
				$final_data[$idx]=array(
					//'pre_meta_fare' => array('cell' => $cellID , 'row' => $rowID),
				 	'company' => 'LION',					
				 	't_depart' => $t_depart,//depart from origin location
				 	't_arrive' => $t_arrive,
					'class' => $fare_class,
				 	'route' => $route,					
					'meta_data' => json_encode(array(
						'seat_available' => $seat_available,
						'flight_number_transit' => '',
						'rowID' => $rowID,
						'cellID' => $cellID,
						'passenger' => $this->_opt->adult +	$this->_opt->child + $this->_opt->infant,
						'txtOutFBCsUsed' => $class,
					)),
					't_transit_arrive' => '', //arrive in transit airport
					't_transit_depart' => '', //depart from transit airport
					'price' => $this->getPrice($vKey,$cellID,$rowID),					
					'flight_no' => $flight_no,					
					'created_at' => '',
					'updated_at' => '',
					'route_from' => $this->_opt->route_from,
					'route_to' => $this->_opt->route_to,
					'adult' => $this->_opt->adult,
					'child' => $this->_opt->child,
					'infant' => $this->_opt->infant,					
					'price_final' => 1,
					'price_meta' => array(
						'adult' => '',
						'infant' => '',
						'child' => ''
					),
					
				);												
				
				$idx++;
				if($idx==($this->_opt->max_fare)) break;
			}																			
		}
		return $final_data;					
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
			'UcFlightSelection$ddlADTCount' => $this->_opt->adult,//2,
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
		
	function preBook($flight_data){
				
		$meta_data = json_decode($flight_data['meta_data']);
												
		$post_data = array(			
			'__EVENTARGUMENT' => '', 
			'__LASTFOCUS' => '',
			'__VIEWSTATE' => '',
			'txtUpdateInsurance' => '',
			'Insurance$rblInsurance' => 'Yes',
			'Insurance$txtInsPostbackRequired' => 'no',
			'txtPricingResponse' => 'OK',
			'txtOutFBCsUsed' => $meta_data->txtOutFBCsUsed,
			'txtInFBCsUsed' => '',
			'txtTaxBreakdown' => '',
			'lbContinue.x' => '84',
			'lbContinue.y' => '9',
			'UcFlightSelection$TripType' => 'rbOneWay',
			'UcFlightSelection$DateFlexibility' => 'rbMustTravel',
			'UcFlightSelection$txtSelOri' => $flight_data['route_from'],
			'UcFlightSelection$txtOri' => $flight_data['route_from'],
			'UcFlightSelection$ddlDepMonth' => date('M Y', strtotime($flight_data['t_depart'])),//'Apr 2012',
			'UcFlightSelection$ddlDepDay' => date('d', strtotime($flight_data['t_depart'])),//'20',
			'UcFlightSelection$ddlADTCount' => $flight_data['adult'],
			'UcFlightSelection$txtSelDes' => $flight_data['route_to'],
			'UcFlightSelection$txtDes' => $flight_data['route_to'],
			'UcFlightSelection$ddlCNNCount' => $flight_data['child'],
			'UcFlightSelection$ddlINFCount' => $flight_data['infant'],
			'UcFlightSelection$txtDepartureDate' => date('d M Y', strtotime($flight_data['t_depart'])),//'Apr 2012',,
			'UcFlightSelection$txtReturnDate' => date('M Y', strtotime($flight_data['t_depart'])),//'21 Apr 2012',
			'txtOBNNCellID' => $meta_data->cellID,
			'txtIBNNCellID' => 'oneway',
			'txtOBNNRowID' => $meta_data->rowID,
			'txtIBNNRowID' => '',
			'txtUserSelectedOneway' => '',
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
		
		$this->vKeyPreBook = str_get_html($res)->find('input[id=__VIEWSTATE]', 0)->getAttribute('value');
		$price = null;
		$price = str_replace(',','',str_get_html($res)->find('td[id=tdAmtTotal]',0)->plaintext);
		
		if($price == null){
			$message = 'fare not found , its sold out , perhaps :)';
				throw new BookingFailed($flight_data, $message);
		}				
		
		if($price>$flight_data['price']){
			$flight_data['price'] = $price;
			throw new BookingFarePriceChanged($flight_data,$price);
		}else if($price<=$flight_data['price']){
			return 1;
		}else{
			return FALSE;
		}
				
	}
	
	function doBook($flight_data,$passenger_data,$contact_data){
		$this->login();
		$preBookResult = $this->preBook($flight_data);		
		if($preBookResult==FALSE){
			$message = 'fare not found , its sold out , perhaps :)';
				throw new BookingFailed($flight_data, $message);
		}
					
		$contact_name =  explode(' ',$contact_data['name'],2);
		$contact_fname = $contact_name[0];
		$contact_lname = $contact_name[1];
		
		$country_code =  substr($contact_data['mobile'],0,2);
		$area_code = substr($contact_data['mobile'],2,3);
						
		$post_data = array(
			'__EVENTTARGET' => 'lbContinue',
			'__EVENTARGUMENT' => '',
			'__VIEWSTATE' => $this->vKeyPreBook,
			'ContactTitle' => $contact_data['title'],
			'ContactFirstName' => $contact_fname,
			'ContactLastName' => $contact_lname,
			'txtAddress1' => '', 
			'txtAddress2' => '',
			'ddlCountry' => 'ID',
			'txtCity' => '',
			'txtPostCode' => '',
			'txtCountryCode1' => $country_code, //'62',
			'txtAreaCode1' => $area_code, //'856',
			'txtPhoneNumber1' => '97586581',
			'ddlOriNumber' => 'M',
			'txtCountryCode3'=> '',
			'txtPhoneNumber3' => '',
			'txtEmailAddress1' => 'smmandiri@gmail.com',
			'txtEmailAddress2' => 'smmandiri@gmail.com',
			'chkSpecialOffers' => 'on',
			'txtRemark' => '',
			'AcceptFareConditions' => 'on',
			'InsuranceDeclaration$chkTermsRead' => 'on',
			'FlightInfo' => '',
			'AXTotal' => '',
			'DCTotal' => '',
			'OtherTotal' => '',
			'nameMismatch' => '',		
		);
		
		$total_pax = $flight_data['adult'] + $flight_data['child'] + $flight_data['infant'];
		$airline = substr($flight_data['flight_no'],0,2); //airline
		
		for($i=0;$i<$total_pax;$i++){
			$j = $i+1;
			
			$pax_name = explode(' ',$passenger_data[$i]['name'],2);
			$pax_fname = $pax_name[0]; //first name
			$pax_lname = $pax_name[1]; //last name
			
			$pax = array(
				'NameBlock'.$j.'$ddlTitle' => $passenger_data[$i]['title'],
				'NameBlock'.$j.'$txtFirstName' => $pax_fname,
				'NameBlock'.$j.'$txtLastName' => $pax_lname,
				'NameBlock'.$j.'$ddlAirline' => $airline,
				'NameBlock'.$j.'$ddlSpecRequest' => 'NA',
				'NameBlock'.$j.'$txtFFNo' => '',
				'NameBlock'.$j.'$ddlMealRequest' =>'No Preference'
					);
			
			if($passenger_data[$i]['type']!='adult'){
				$additional_pax = array(
					'NameBlock'.$j.'$ddlDOBDay' => date('d', strtotime($passenger_data[$i]['birthday'])),
					'NameBlock'.$j.'$ddlDOBMonth' => date('M', strtotime($passenger_data[$i]['birthday'])),
					'NameBlock'.$j.'$ddlDOBYear' => date('Y', strtotime($passenger_data[$i]['birthday'])),
						);					
				$pax = array_merge($pax,$additional_pax);
				
				if($passenger_data[$i]['gender']=='M'){
					$pax['NameBlock'.$j.'$ddlTitle'] = 'Mstr';
				}else{
					$pax['NameBlock'.$j.'$ddlTitle'] = 'Miss';
				}
			}
			
			if($passenger_data[$i]['type']=='infant'){
				$pax['NameBlock'.$j.'$ddlMealRequest'] = 'BBML';
			}
						
			$post_data = array_merge($post_data,$pax);
		}
				
								
		$conf = array(
			'url' 				=> $this->book_url,
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
			'useragent'			=> 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.56 Safari/535.11',
		);				
		
		$this->_ci->my_curl->setup($conf);
		$res  = $this->_ci->my_curl->exc();
			
		return $res;
	}
	
	function logout(){
		$this->topage($this->_start_url);
	}
	
	function closing(){
		$this->logout();
	}
	
}