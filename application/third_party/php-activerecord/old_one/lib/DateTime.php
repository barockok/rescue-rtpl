<?php
namespace ActiveRecord;

class DateTime extends \DateTime
{
		public static $DEFAULT_FORMAT = 'db';

		/**
		 * Pre-defined format strings.
		 */
		public static $FORMATS = array(
			'db'      => 'Y-m-d H:i:s',
			'number'  => 'YmdHis',
			'time'    => 'H:i',
			'short'   => 'd M H:i',
			'long'    => 'F d, Y H:i',
			'atom'    => \DateTime::ATOM,
			'cookie'  => \DateTime::COOKIE,
			'iso8601' => \DateTime::ISO8601,
			'rfc822'  => \DateTime::RFC822,
			'rfc850'  => \DateTime::RFC850,
			'rfc1036' => \DateTime::RFC1036,
			'rfc1123' => \DateTime::RFC1123,
			'rfc2822' => \DateTime::RFC2822,
			'rfc3339' => \DateTime::RFC3339,
			'rss'     => \DateTime::RSS,
			'w3c'     => \DateTime::W3C);
	
	private $model;
	private $attribute_name;

	public function attribute_of($model, $attribute_name)
	{
		$this->model = $model;
		$this->attribute_name = $attribute_name;
	}

	private function flag_dirty()
	{
		if ($this->model)
			$this->model->flag_dirty($this->attribute_name);
	}

	public function setDate($year, $month, $day)
	{
		$this->flag_dirty();
		call_user_func_array(array($this,'parent::setDate'),func_get_args());
	}

	public function setISODate($year, $week , $day=null)
	{
		$this->flag_dirty();
		call_user_func_array(array($this,'parent::setISODate'),func_get_args());
	}

	public function setTime($hour, $minute, $second=null)
	{
		$this->flag_dirty();
		call_user_func_array(array($this,'parent::setTime'),func_get_args());
	}

	public function setTimestamp($unixtimestamp)
	{
		$this->flag_dirty();
		call_user_func_array(array($this,'parent::setTimestamp'),func_get_args());
	}
}
?>