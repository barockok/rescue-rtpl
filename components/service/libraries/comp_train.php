<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Comp_train {
	
	var $_src_url = 'http://www.kereta-api.co.id/jadwal-ka/jadwal-ka.html';
	public function __construct()
	{
		$this->CI =& get_instance();
		$this->_sample = array(
			'./mod_office/o_partner/third_party/comp_train/jadwal_KA_1.html',
			'./mod_office/o_partner/third_party/comp_train/jadwal_KA_2.html',
		//	'./mod_office/o_partner/third_party/comp_train/jadwal_KA_3.html',
		);
		$this->CI->load->library('my_curl');
		shuffle($this->_sample);
		
	}
	public function doSearch($conf = null)
	{
		$return = array();
		$depart = array();
		$final_data = array();
		
			if(element('date_return', $conf) != null){
				$conf_r = array(
					'tanggal' 		=> str_replace('-', '', element('date_return', $conf)),
					'origination' 	=> element('route_to', $conf),
					'destination' 	=> element('route_from', $conf),
					'log_id'		=> element('id', $conf),
					'passengers'	=> 1,
					'original_date' => date('Y-m-d', strtotime(element('date_return', $conf))),
				);	
				if( $return = $this->_doSearch($conf_r, 'return') ) $final_data = array_merge($final_data, $return );
			}
		
		$conf_d = array(
			'tanggal' 		=> str_replace('-', '', element('date_depart', $conf) ),
			'origination' 	=> element('route_from', $conf),
			'destination' 	=> element('route_to', $conf),
			'log_id'		=> element('id', $conf),
			'passengers'	=> element('passengers', $conf),
			'original_date' => date('Y-m-d', strtotime(element('date_depart', $conf))),
		);
		if( $depart = $this->_doSearch( $conf_d ) ) $final_data = array_merge($final_data, $depart );
		
	
		
		return (count($final_data) > 0) ? $final_data : false ;
	
		
	}
	
	public function _doSearch($conf = array() , $type = 'depart' )	
	{
		$log_id = element('log_id', $conf);
		unset($conf['log_id']);
		
		$conf['Submit'] = 'Tampilkan';
		
		$curlOPT = array(
			'url' => $this->_src_url,
			'post' => true,
			'postfields' => http_build_query($conf, NULL, '&'),
			'returntransfer' => true,
		);
		$this->CI->my_curl->setup($curlOPT);
		$html = str_get_html($this->CI->my_curl->exc());
		
		if( !$raw = $html->find('div[id=middle-column] div[class=inside] table', 1) ) return false;
		
		$i = 0;
		$return = array();
		
		foreach($raw->find('tr[class=itRowTable0]') as $comp){
		
			$compName = ucwords(strtolower($comp->plaintext));
			$compCont = $raw->find('tr[class=itRowTable1]', $i);
			$no_ka 	  = $compCont->find('td', 0)->plaintext;
			$t_depart = $compCont->find('td', 1)->plaintext;
			$t_arrive = $compCont->find('td', 2)->plaintext;
			
			$items = $compCont->find('td', 3)->find('table tbody tr');
			$j = 0;
				foreach($items as $item){
					$route = ($type == 'depart') ? element('origination', $conf).','.element('destination', $conf) : element('destination', $conf).','.element('origination', $conf);
					$finalItem = array(
						'company'  	=> $compName,
						'price'	  	=> (str_replace('.', '',$item->find('td', 2)->plaintext)) *  element('passengers', $conf),
						't_depart' 	=> $this->detectDate(element('original_date', $conf), $t_depart),
						't_arrive' 	=> $this->detectDate(element('original_date', $conf), $t_depart, $t_arrive),
						'class'  	=>  ucwords(strtolower($item->find('td', 0)->plaintext)),
						'no_ka'		=> $no_ka,
						'type'		=> $type,
						'route'		=> $route,
						'log_id'	=> $log_id
					);
					array_push($return , $finalItem);
				// increment
				$j ++;	
				}
			// increment 
			$i++;
			
		}
		return $return;
	}
	
	private function detectDate($date, $t_depart = null , $t_arrive = null)
	{	
		
		$old_t = date($date.' '.$t_depart.':00');
		if($t_arrive == null ) return $old_t;
		$t_depart = explode(':', $t_depart );
		$t_h = (element(0, element(0, $t_depart )) == '0') ? element(1, element(0, $t_depart )) : element(0, $t_depart ) ;
		$t_m = (element(0, element(1, $t_depart )) == '0') ? element(1, element(1, $t_depart )) : element(1, $t_depart ) ;
	
		$t_arrive = explode(':', $t_arrive );
		$a_h = (element(0, element(0, $t_arrive)) == '0') ? element(1, element(0, $t_arrive)) : element(0, $t_arrive) ;
		$a_m = (element(0, element(1, $t_arrive)) == '0') ? element(1, element(1, $t_arrive)) : element(1, $t_arrive) ;
		
		$h_long = ( (23 - $t_h )  +  $a_h );
		$m_long = ( (60 - $t_m ) +  $a_m );
		//return '+'.$h_long.' hours +'.$m_long.' minutes';
		return date('Y-m-d H:i:s', strtotime('+'.$h_long.' hours +'.$m_long.' minutes', strtotime($old_t)));
		
		
	}
	
}
