<?
/**
* 
*/
class PropertySetterException extends Exception{
	public function __construct($prop, $value, $class, $msg = null)
	{
		$this->message = '"'.$value.'" is not valid value for '.$prop. ' on '.$class; 
	}
}
class SearchOpt 
{
	protected $abstract_opt = array('route_from', 'route_to', 'date_depart', 'adult', 'child', 'infant');
	protected $opt_required = array('route_from', 'route_to', 'date_depart', 'adult')
	public function __construct($array)
	{
		
	}
	private function init($array)
	{
		
	}
	public function __set($prop, $value)
	{
		if($prop == 'date_return')
			if(!validate_date($value))
				throw new PropertySetterException($prop, $value, get_class(), 'not kind of valid date');
		if($prop == 'route_from' || $prop == 'route_to')
			if(!Service_fare_item::find('last', array('condition' => array('code =?', $value))))
				throw new PropertySetterException($prop, $value, get_class(), 'not kind of route to');
		if(in_array($prop, array('child', 'infant')))
			if(!is_int($value) AND $value == '0')
				throw new PropertySetterException($prop, $value, get_class(), 'not valid integer value form '.$prop);
		if($prop == 'adult')
			if(!is_int($value))
				throw new PropertySetterException($prop, $value, get_class(), 'not valid integer value form '.$prop);
		
	}
}

?>