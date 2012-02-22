<?
class Booking_flight extends ActiveRecord\Model
{
	static $table_name = 'booking_flight';
	static $has_many = array(
		array(
			'booking_data' , 'class_name' => 'Booking_flight_data' , 'foreign_key' => 'flight_booking_id' 
		),
	);
	static $validates_presence_of = array(
			array('bill_name'),
			array('bill_email'),
			array('bill_phone'),
			array('bill_mobile'),
			array('user_id'),
			array('depart_fare_id')
	    );
	static $belongs_to = array(
		array(
			'order_item' , 'class_name' => 'Order_item', 'foreign_key' => 'order_item_id'
		),
		array(
			'depart_fare', 'class_name' => 'Search_fare_item' , 'foreign_key' => 'depart_fare_id'
		),
		array(
			'return_fare', 'class_name' => 'Search_fare_item', 'foreign_key' => 'return_fare_id', 'conditions' => array('return_fare_id <> ?', null)
		),
		array(
			'customer', 'class_name' => 'User', 'foreign_key' => 'user_id'
		)
	);


	public function validate()
	{
		
		$depart = false;
		$return = false;
		try {
			$user = User::find($this->user_id);
		} catch (Exception $e) {
			$this->errors->add('user_id', 'is not valid');
			return;
		}
		try {
			$depart = Search_fare_item::find($this->depart_fare_id);
			if($depart->type != 'depart') $this->errors->add('depart_fare_id', "is not valid");
		} catch (Exception $e) {
			$this->errors->add('depart_fare_id', "is not valid");
			return ;
		}
		
		if($depart != FALSE && $this->return_fare_id == null){
			$this->errors->add('return_fare_id', 'is required when flight roundtrip');
			return ;
		}
		
		try {
			$return = Search_fare_item::find($this->return_fare_id);
			if($return->type != 'return') $this->errors->add('return_fare_id', 'is not valid');
		} catch (Exception $e) {
			$this->errors->add('return_fare_id', 'is not valid');
			return ;
		}
		
		if($return != FALSE && $depart->log_id != $return->log_id){
			$this->errors->add('return_fare_id', 'is not valid combination between depart fare id and return fare id');
			return;
		}
		
	}
}