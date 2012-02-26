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
		$source = file_get_html($this->sources[0]);
	
		return $this->_oneway_extract($source);
	}
	private function _rountrip_extract($source)
	{
	
	}
	private function _oneway_extract($source)
	{
		$return_vars = array();
		// get row
		$rows = $source->find('table#fareTable1_4 tbody tr');
	
		// there is no fare listed
		$fares_count = count($rows)-1;
		if($fares_count < 2) return false;
	//	echo $fares_count;
		for ($i=1; $i < $fares_count ; $i++) { 
			$a_row = $rows[$i];
			// setup price
			$coll_price = $a_row->find('td', 2);
			$list_price_raw = $coll_price->find('.resultFare2', 0);
			$the_price = $list_price_raw->find('.paxPriceDisplay');
			$the_price_type = $list_price_raw->find('.paxTypeDisplay');
			// decalar the camdidate price array var
			$list_price  = array();
		
				// ectract the price form array dom
				for ($i=0; $i < count($the_price); $i++) { 
					$a_price = array(
						'value' => filter_var(str_replace('.00', '',$the_price[$i]->plaintext), FILTER_SANITIZE_NUMBER_INT),
						'type'	=> $the_price_type[$i]->plaintext
						);
					array_push($list_price, $a_price);
				} 
			//return $list_price;
		
			
			
			$a_fare = array(
		
			  	'price' => '',
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
	
}
