<?
/**
* 
*/
class Garuda extends Factory
{
	
	function __construct()
	{
		parent::__construct();
		
	}
	function srcFlight(){
		
		$class = array('A', 'B', 'D','F', 'G');
		$price = array(550000, 569000, 789000, 467000,810000);
		$datetime = array('2011-06-30 16:55:00', '2011-06-30 18:55:00',  '2011-06-30 11:55:00', '2011-06-30 03:55:00', '2011-06-30 07:55:00');
		$type = array('return', 'depart');
		
		for($i = 1; $i < 15 ; $i++){
			shuffle($class);shuffle($price);shuffle($datetime);shuffle($type);
			$data[$i]['company'] = 'GARUDA';
			$data[$i]['route'] = 'CKG => BDJ => SBY';
			$data[$i]['class'] = element(1,$class);
			$data[$i]['price'] = element(1, $price);
			$data[$i]['depart_time'] = element(0, $datetime);
			$data[$i]['type']	= element(0,$type);
		}
		
		
		
		
		$this->addResFlight($data);
	
	}
}

