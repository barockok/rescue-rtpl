<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class My_Curl {

	function __construct(){
		
	}
	/**
	 * 
	 *
	 * @param string $conf 
	 * @return void
	 * @author Zidni Mubarock
	 */
	// PROTOTYPE //
	/*
	$conf = array(
		'url' 				=> '',
		'timeout' 			=> '',
		'head' 				=> '',
		'body' 				=> '',
		'follow' 			=> '',
		'cookiejar' 		=> '',
		'cookiefile' 		=> '',
		'returntransfer' 	=> '',
		'post' 				=> '',
		'referer' 			=> '',
		'postfield' 		=> '',
		'agent' 			=> '',
	);
	*/
	function exec( $conf = array()){
	
		$s = curl_init();
		curl_setopt($s, CURLOPT_URL, element('url', $conf));
	    curl_setopt($s, CURLOPT_TIMEOUT, 			( $to = element('timeout', $conf) ) ? $to : 30 );
		curl_setopt($s, CURLOPT_HEADER, 			( $head = element('header', $conf) ) ? $header : 0 );
		curl_setopt($s, CURLOPT_NOBODY, 			( $body = element('body', $conf) ) ? $body : true ); 
		curl_setopt($s, CURLOPT_FOLLOWLOCATION, 	( $follow = element('follow', $conf) ) ? $follow : 1 );
	    curl_setopt($s, CURLOPT_COOKIEJAR, 			( $cookiejar = element('cookiejar', $conf) ) ? $cookiejar : false );
	    curl_setopt($s, CURLOPT_COOKIEFILE, 		( $cookiefile = element('cookiefile', $conf) ) ? $cookiefile : false );
		curl_setopt($s, CURLOPT_RETURNTRANSFER, 	( $returntransfer = element('returntransfer', $conf) ) ? $returntransfer : 1 );
		curl_setopt($s, CURLOPT_POST, 				( $post = element('post', $conf) ) ? $post : true );
		curl_setopt($s, CURLOPT_REFERER, 			( $referer = element('referer', $conf) ) ? $referer : false );
		curl_setopt($s,  CURLOPT_SSL_VERIFYHOST , 0);
		//curl_setopt($s, CURLOPT_POSTFIELDS, http_build_query( element('postfield', $conf) , NULL, '&' ) );
		//curl_setopt($s,  CURLOPT_SSL_ , 0);
		curl_setopt($s, CURLOPT_POSTFIELDS, element('postfield', $conf) );
		curl_setopt($s, CURLOPT_USERAGENT, 			( $agent = element('agent', $conf) ) ? $agent : "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:6.0.2) Gecko/20100101 Firefox/6.0.2");
		$res = curl_exec($s);
		curl_close($s);
		return $res;
		
	
	}
	function setup($conf = array()){
		$data = array();
		foreach($conf as $key => $value){
			$name = constant('CURLOPT_'.strtoupper($key));
			$val  = $value;
			$data[$name] = $val;
		}
		$this->opt = $data;
	}
	function exc($verbose = false){
		$cu = curl_init();
		
		if($verbose != false) {
			
			$this->verbose_file = $verbose;
			$log = @fopen($verbose, "w");
			$veropt = array(
				CURLOPT_VERBOSE =>  1,
				CURLOPT_STDERR => $log,
			);
			
			$this->opt = array_merge($this->opt, $veropt);			
		}
		
		curl_setopt_array($cu, $this->opt);
		$exec = curl_exec($cu);
		curl_close($cu);
		
		 if($verbose == false) {
			return  $exec ;
		}else{
			$this->readverbose();
		}
	}
	function readverbose(){
		$file = $this->verbose_file;	
		$handle = fopen($file, 'r');
		$result = fgets($handle, 4096);
        fclose($handle);
		echo $result;	
	}
	

}