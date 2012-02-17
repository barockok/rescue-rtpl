<?
/*
*/
class TiketcomException extends Exception{}
class SerachOptNullException extends TiketcomException{
	public function __construct($null_property)
	{
		$this->message = 'this cannot be empty '.implode(', ', $null_property);
	}
}
