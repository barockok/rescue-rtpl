<?

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lion extends Comp_maskapai_base {


	function __construct(){
		parent::__construct();
		$this->_cookies_file = realpath('./components/partner/third_party/comp_maskapai/cookies/lion_air.txt');
		$this->login_url = 'https://agent.lionair.co.id/LionAirAgentsPortal/Default.aspx';
		$this->_refer_url = 'https://agent.lionair.co.id/LionAirAgentsPortal/Default.aspx';
		//$this->src_url = 'https://www.citilink.co.id/giaidb2b/agent.aspx';
		
		foreach(parent::$_opt as $key => $val ){
			$this->_opt->$key = $val;
		}
		
	}
	
	function index(){
		echo "Lion";
	}
	
	function login(){
		$post_data = array(		
			'__EVENTTARGET' => '',
			'__EVENTARGUMENT' => '',
			'__VIEWSTATEKEY' => '',
			'__VIEWSTATE' => '',
			'txtLoginName' => 'toplima',
			'txtPassword' => 'mitra2011',
			'chkRememberMe' => 'on',
			'NameReqExtend_ClientState' => '',
			'PasswordReqExtend_ClientState' => ''
		);
		
		$conf = array(
			'url' 				=> $this->login_url,
			'timeout'			=> 30,
			'header'			=> 0,
			'nobody'			=> true,
			'followlocation'	=> 1,
			'cookiejar' 		=> $this->_cookies_file,
			'cookiefile' 		=> $this->_cookies_file,
			'returntransfer'	=> 1,
			'post'				=> true,
			'referer' 			=> $this->_refer_url,
			'postfields' 		=> http_build_query($post_data , NULL, '&'),
			'useragent'			=> 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:6.0.2) Gecko/20100101 Firefox/6.0.2',
			'SSL_VERIFYPEER'    => false
		);
		
		$data = $this->my_curl->setup($conf);
		$res = $this->my_curl->exc($data);
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
			
			$this->_opt->date_depart = $this->_opt->date_return;
			
			$return_flight = $this->src('return');
		}else{
			$depart_flight = $this->src('depart');
		}
		/*		
		echo "<h2>Depart</h2>";
		//print_r($depart_flight);
		echo "<h2>Return</h2>";
		//print_r($return_flight);
		*/
		return array_merge($depart_flight, $return_flight);
	}
	
	function src($flight_type){
		
		$this->format_date(); //adjust the date format
		/*$post_data = array(
			'__EVENTTARGET' => '',
			'__EVENTARGUMENT' => '',
			'__VIEWSTATEKEY' => '',
			'__VIEWSTATE' => '',
			'UcFlightSelection$TripType' => 'rbOneWay',
			'UcFlightSelection$DateFlexibility' => 'rbMustTravel',
			'UcFlightSelection$txtSelOri' => $this->_opt->route_from,
			'UcFlightSelection$txtOri' => 'Denpasar (Bali) (DPS)',
			'UcFlightSelection$ddlDepMonth' => $this->dep_month, //Nov 2011
			'UcFlightSelection$ddlDepDay' => $this->dep_day, //17
			'UcFlightSelection$ddlADTCount' => $this->_opt->passengers,
			'UcFlightSelection$txtSelDes' => $this->_opt->route_from,
			'UcFlightSelection$txtDes' => 'Jakarta (CGK)',
			'UcFlightSelection$ddlCNNCount' => '0',
			'UcFlightSelection$ddlINFCount' => '0',
			'UcFlightSelection$txtDepartureDate' => $this->dep_date,  //17 Nov 2011
			'UcFlightSelection$txtReturnDate' => $this->dep_date, //
		);*/
		
		$file_arr = array(
			'./components/partner/third_party/comp_maskapai/lion_html/li_1/lion.htm',			
			'./components/partner/third_party/comp_maskapai/lion_html/li_3/lion.htm',
			'./components/partner/third_party/comp_maskapai/lion_html/li_4/lion.htm',
			'./components/partner/third_party/comp_maskapai/lion_html/li_5/lion.htm',
		);				
		
		$final_data = array();
		shuffle($file_arr);	
		
		$dom = file_get_html(realpath($file_arr[1]));
		
		$flight_table = $dom->find('table[id=tblOutFlightBlocks] tbody',0);				
		$total_flight = count($dom->find('table[id=tblOutFlightBlocks] tbody tr')) - 4; //get total flight by count rows in table
		$idx = 0;
		if($total_flight>0){
			$is_transit = $this->checkTransit($flight_table,$total_flight);
			
			for ($i= 0; $i < $total_flight; $i++) {										
				if($is_transit){ //check whether the flight is transit								
					if($i%2==0){
						$j=$i/2;
						$a = 0;
					}else{
						$a = 1;
					}
					$row_id = 'tr[id=RM0_C'.$j.'_F'.$a.']'; //define id each row
					$coloumn_id = 'td[id=RM0_C'.$j.'_F'.$a.'_selText]'; //define id each coloumn
					
					$sel_text = explode('|', $dom->find('table[id=tblOutFlightBlocks] tbody',0)->find($row_id,0)->find($coloumn_id,0)->plaintext);//split to array			
													
					if($i%2==0){
						$route = substr($sel_text[6],-6,3).','.substr($sel_text[6],-3); //define route
						$t_ = explode(' ',$sel_text[8]); //split dep_time and arr_time
						$flight_number = $sel_text[1];
						
						//format departure time
						$dep_time_a = substr($t_[0],-2);
						$dep_time_b = substr($t_[0],-4,2);
						$dep_time = $dep_time_b.":".$dep_time_a;
						
						//format transit arrival time
						$arr_trans_time_a = substr($t_[1],-2);
						$arr_trans_time_b = substr($t_[1],-4,2);
						$arr_trans_time = $arr_trans_time_b.":".$arr_trans_time_a;											
						
						$class_avail = $dom->find('table[id=tblOutFlightBlocks] tbody',0)->find($row_id,0)->find('td[class=step2farecell]');
						print_r($class_avail);
						
						
					}else{ 
						$flight_transit_number = $sel_text[1];
						$route .= ",".substr($sel_text[6],-3);
						$t_ = explode(' ',$sel_text[8]); //split dep_time and arr_time
						
						//format transit departure time
						$dep_trans_time_a = substr($t_[0],-2);
						$dep_trans_time_b = substr($t_[0],-4,2);
						$dep_trans_time = $dep_trans_time_b.":".$dep_trans_time_a;
						
						//format arrival time
						$arr_time_a = substr($t_[1],-2);
						$arr_time_b = substr($t_[1],-4,2);
						$arr_time = $arr_time_b.":".$arr_time_a;
						
						$class_avail = $dom->find('table[id=tblOutFlightBlocks] tbody',0)->find($row_id,0)->find('td[class=step2farecell]');
						//print_r($class_avail);
						
						foreach ($class_avail as $j => $class) {
							$class_cell = $dom->find('table[id=tblOutFlightBlocks] tbody',0)->find($row_id,0)->find('td[class=step2farecell]',$j);
							
							if($class_cell->find('input[type=radio]',0)->getAttribute('disabled') == 'disabled'){
								continue;
							}//end if
							$final_data[$idx] = array(
								'company' => 'LION',
								't_depart' => $this->_opt->date_depart." ".$dep_time,//depart from origin location
								't_transit_arrive' => $this->_opt->date_depart." ".$arr_trans_time, //arrive in transit airport
								't_transit_depart' => $this->_opt->date_depart." ".$dep_trans_time, //depart from transit airport
								't_arrive' => $this->_opt->date_depart." ".$arr_time,
								'type' => $flight_type, //depart or return
								'class' => substr($class_cell->find('span',0)->getAttribute('title'),0,1),
								'route' => $route,
								'meta_data' => json_encode(array(					
									'flight_number' => $flight_number,
									'flight_number_transit' => $flight_transit_number,
									'passenger' => $this->_opt->passengers
								))
							);
							$idx++;
						}//end foreach
					}												
				}else{//flight isn't transit
					
					$row_id = 'tr[id=RM0_C'.$i.'_F0]'; //define id each row
					$coloumn_id = 'td[id=RM0_C'.$i.'_F0_selText]'; //define id each coloumn
					$sel_text = explode('|', $dom->find('table[id=tblOutFlightBlocks] tbody',0)->find($row_id,0)->find($coloumn_id,0)->plaintext);//split to array
					
					$route = substr($sel_text[6],-6,3).','.substr($sel_text[6],-3); //define route
					$t_ = explode(' ',$sel_text[8]); //split dep_time and arr_time
					
					//format departure time
					$dep_time_a = substr($t_[0],-2);
					$dep_time_b = substr($t_[0],-4,2);
					$dep_time = $dep_time_b.":".$dep_time_a;
					
					//format arrival time
					$arr_time_a = substr($t_[1],-2);
					$arr_time_b = substr($t_[1],-4,2);
					$arr_time = $arr_time_b.":".$arr_time_a;
					
					$class_avail = $dom->find('table[id=tblOutFlightBlocks] tbody',0)->find($row_id,0)->find('td[class=step2farecell]');
					
					foreach ($class_avail as $j => $class) {
						$class_cell = $dom->find('table[id=tblOutFlightBlocks] tbody',0)->find($row_id,0)->find('td[class=step2farecell]',$j);
						//echo $class_cell->plaintext;
						if($class_cell->plaintext == 'No Fares' || $class_cell->find('input[type=radio]',0)->getAttribute('disabled') == 'disabled'){						
							continue;
						}//end if
						$final_data[$idx] = array(
							'company' => 'LION',
							't_depart' => $this->_opt->date_depart." ".$dep_time,//depart from origin location
							't_transit_arrive' => '', //arrive in transit airport
							't_transit_depart' => '', //depart from transit airport
							't_arrive' => $this->_opt->date_depart." ".$arr_time,
							'type' => $flight_type, //depart or return
							'class' => substr($class_cell->find('span',0)->getAttribute('title'),0,1),
							'route' => $route,
							'meta_data' => json_encode(array(					
								'flight_number' => $sel_text[1],
								'flight_number_transit' => '',
								'passenger' => $this->_opt->passengers
							))
						);
						$idx++;
					}//end foreach
				} //end else																				
			} //end outer for
		}//end if
						
	//	print_r($final_data);
		
		return $final_data;
	}
	
	function plain(){
		echo $dom = file_get_html(realpath('./components/partner/third_party/comp_maskapai/lion_html/li_4/lion.htm'));
	}
	
	function checkTransit($flight_table,$total_flight){		
		$tr_id_a = substr($flight_table->find('tr',2)->getAttribute('id'),0,6);
		$tr_id_b = substr($flight_table->find('tr',3)->getAttribute('id'),0,6);
		if($tr_id_a == $tr_id_b){
			return true;
		}else{
			return false;
		}
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
	// API REQUIREMENT 
	public function doSearch()
	{
			$this->addResult($this->cleanObject('Lion/src_flight', array()));
	}
	
}
