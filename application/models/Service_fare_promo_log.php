<?
/**
* 
*/
class Service_fare_promo_log extends ActiveRecord\Model
{
	static $table_name = "service_fare_promo_log";
	static $validates_presence_of = array(array('start_date'), array('end_date'), array('destination'), array('original'));

	static $belongs_to = array(
		array('departure_airport' , 'class_name' => 'Ext_data_airport', 'foreign_key' => 'original' , 'primary_key' => 'code'),
		array('destination_airport' , 'class_name' => 'Ext_data_airport', 'foreign_key' => 'destination', 'primary_key' => 'code')
		);
	static $has_many = array(
		array('search', 'class_name' => 'Service_fare_log', 'foreign_key' => 'src_promo_id'),
	);

	static $before_create = array('_before_create');

	public function _before_create()
	{
		if($this->actor == null ) $this->actor = 'CUS';
		
		// set count day
		$start  = date('Y-m-d H:i:s',strtotime($this->start_date)); 
		$end    = date('Y-m-d H:i:s',strtotime($this->end_date)); 
		$d_start    = new DateTime($start); 
		$d_end      = new DateTime($end); 
		$diff       = $d_start->diff($d_end);
		$this->count_day = $diff->format('%d')+1;
	}
	public function validate()
	{
		if($this->start_date != null && $this->end_date != null) {
			$start  = date('Y-m-d H:i:s',strtotime($this->start_date)); 
			$end    = date('Y-m-d H:i:s',strtotime($this->end_date)); 
			$d_start    = new DateTime($start); 
			$d_end      = new DateTime($end); 
			$diff       = $d_start->diff($d_end); 
	
			$meta_diff = array(
				'year' => $diff->format('%y'),
				'month' => $diff->format('%m'),
				'day' => $diff->format('%d'),
				'hour' => $diff->format('%h'),
				'min' => $diff->format('%i'),
				'sec' => $diff->format('%s'),
			);
			$overed_flag = FALSE;
			foreach($meta_diff as $key => $val){
				$over_flag = array('year', 'month');
				if(in_array($key, $over_flag) && $val != 0) $overed_flag = TRUE;
				if($key == 'day' && $val+1 > 7) $overed_flag = TRUE;
			}
			if($overed_flag == TRUE) $this->errors->add('end_date', 'should lower or same as a week since date start');
		
			if($this->end_date <= $this->start_date){
				$this->errors->add('end_date', 'should more than start date');
			}
		}
	}
	
}

?>