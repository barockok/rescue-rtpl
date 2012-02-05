<?
class Ssh {
	function __construct(){
		$thi->CI =& get_instance();
		ini_set('include_path',
		ini_get('include_path') . PATH_SEPARATOR . realpath(PLATPATH . 'libraries/phpseclib/'));
	}
	function login(){
		include('Net/SSH2.php');
		$ssh = new Net_SSH2('119.235.31.27');
		if (!$ssh->login('barock', 'alzid4ever')) {
		    exit('Login Failed');
		}else{
			echo $ssh->exec('cd /var/www/vhosts/rumahtiket/rumahtiket.com/httpdocs/testgit/platform-rumahtiket/ && git pull' );
			
	
		}
		
	}
}
