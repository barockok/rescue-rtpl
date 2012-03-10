<?
/**
* 
*/
class Maskapai_unit_test extends MX_Controller
{
	private $conf ;
	private $airlines_comp = array('sriwijaya', 'batavia', 'garuda', 'lion', 'citilink', 'merpati');
	function __construct()
	{
		parent::__construct();

	
		
	}
	public function index()
	{
		echo 'Maskapai Unit Test';
	}
	public function dosearch()
	{
		$test = $this->airlines->doSearch(element('dosearch', $this->conf));
		printDebug($test);
		$this->airlines->closing();
	}
	public function getdetail()
	{
		$test = $this->airlines->getDetail(element('getdetail', $this->conf));
		printDebug($test);
		$this->airlines->closing();
	}
	public function dobooking($value='')
	{
		
		
		$doBooking_conf = element('dobooking', $this->conf);
		$test = $this->airlines->doBooking(
			$doBooking_conf['fare_data'],
			$doBooking_conf['passengers_data'],
			$doBooking_conf['contact_data']
			);
		printDebug($test);
		$this->airlines->closing();
	}
	public function _remap($object, $param)
	{
		if(!in_array($object, $this->airlines_comp))
			exit("$object is not in maskapai List");
		$func = $this->uri->rsegment(3);
		if(!method_exists($this, $func))
			exit("$func is not on test unit");
		$conf_file = "./components/service/third_party/comp_maskapai/test_unit_conf/$object.php";
		if(!is_file($conf_file))
			exit("there is no configuration file for the airlines, please create configuration file , and put on this $conf_file");
		include_once($conf_file);
		$this->conf = $conf;
		$this->load->library('service/comp_maskapai');
		$this->airlines = $this->comp_maskapai->load($object);
		$this->$func();
	}

}

?>