<?
class App_hook
{
	public function setting_locale()
	{
		$timezone = 'Asia/Jakarta';
		date_default_timezone_set($timezone);
	}
}