<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Airasia extends Comp_maskapai_base {
	
	public function __construct()
	{
		parent::__construct();
		$this->sources = array('./Airasia.oneway.html', './Airasia.roundtrip.html');
	//	shuffle($this->sources);
	}
	public function doSearch()
	{
		$str = file_get_contents($this->sources[0]);
		$source = str_get_html($str);
	
		return $this->_oneway_extract(null);
	}
	private function _rountrip_extract($source)
	{
	
	}
	private function _oneway_extract($source)
	{
	//	return array('a', 'c');
		$return_vars = array();
		// get row
		$rows = $source->find('table[id=fareTable1_4] tbody tr');
	
		// there is no fare listed
		$fares_count = count($rows)-1;
		if($fares_count < 2) return false;
	//	echo $fares_count;
		for ($i=1; $i < $fares_count ; $i++) { 
			
			
			/*
			$a_row = $rows[$i];
		
			// setup price
			$coll_price 	= $a_row->find('td', 2);
		
		
		
		
		
			$list_price_raw = $coll_price->find('.resultFare2', 0);
			$the_price 		= $list_price_raw->find('.paxPriceDisplay');
			$the_price_type = $list_price_raw->find('.paxTypeDisplay');
			// decalar the camdidate price array var
			$list_price  = array();
			echo count($the_price); return;
				// ectract the price form array dom
				for ($j=0; $j < count($the_price); $j++) { 
					
					$a_price = array(
						'value' => filter_var(str_replace('.00', '',$the_price[$j]->plaintext), FILTER_SANITIZE_NUMBER_INT),
						'type'	=> $the_price_type[$j]->plaintext,
						);
					array_push($list_price, $a_price);
				} 
		
			return $list_price;
			
			*/
			$a_fare = array(
		
			  	'price' =>  '',
				'company' => 'AIRASIA',
				't_depart' => '',	
				't_transit_arrive' => '',
				't_transit_depart' => '', 
				't_arrive' => '',
				'type' => '', 
				'class' => '',
				'flight_no' => '',
				'log_id' => '',					
				'route' => '',
				
			);
			array_push($return_vars, $a_fare);
		}		
		return $return_vars;
		
	}
	public function test()
	{
		echo 'suh';
	}
	
}
