<?
/**
* 
*/
class Suicide extends REST_Controller
{
	
	function __construct()
	{
		parent::__construct();
	}
	public function index()
	{
		echo "Dont kill me, I'll do it by my self ";
	}
	
	public function system_get()
	{
		$ch1 = curl_init();
		$ch2 = curl_init();
		curl_setopt($ch1, CURLOPT_URL, site_url('suicide/system2'));
		curl_setopt($ch1, CURLOPT_HTTPHEADER, array('X-API-KEY:abc'));
	//	curl_setopt($ch1, CURLOPT_HEADER, 0);
		curl_setopt($ch2, CURLOPT_URL, site_url('suicide/system3'));
		curl_setopt($ch2, CURLOPT_HTTPHEADER, array('X-API-KEY:abc'));
	//	curl_setopt($ch2, CULROPT_POST, 1);
	//	curl_setopt($ch2,CURLOPT_POSTFIELDS, httpquerybuild(array('_method' => 'suicide'))
	//	curl_setopt($ch2, CURLOPT_HEADER, 0);
		
		$mh = curl_multi_init();
		
		curl_multi_add_handle($mh,$ch1);
		curl_multi_add_handle($mh,$ch2);

		$active = null;
		//execute the handles
		do {
		    $mrc = curl_multi_exec($mh, $active);
		} while ($mrc == CURLM_CALL_MULTI_PERFORM);

		while ($active && $mrc == CURLM_OK) {
		    if (curl_multi_select($mh) != -1) {
		        do {
		            $mrc = curl_multi_exec($mh, $active);
		        } while ($mrc == CURLM_CALL_MULTI_PERFORM);
		    }
		}


		curl_multi_remove_handle($mh, $ch1);
		curl_multi_remove_handle($mh, $ch2);
		curl_multi_close($mh);
	
	}
	
	public function system2_get()
	{
		for($i = 0 ; $i < 1000 ; $i++):
			$new = new Ext_data_airport(array('code' => 'SQTYU' , 'name' => 'Wewewe_'.$i));
			$new->save();
		endfor;
	}
	public function system3_get()
	{
		for($i = 0 ; $i < 1000 ; $i++):
			$new = new Ext_data_stasiun(array('code' => 'SQTYUas' , 'name' => 'ASuhhh'.$i));
			$new->save();
		endfor;
	}
}

?>