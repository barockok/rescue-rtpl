<?

class Error_log extends Office_Controller
{
	
	function __construct()
	{
		parent::__construct();
	}
	public function index()
	{
		echo 'suh';
	//	$data = $this->rest->get('db/find/error_log/all', array('options' => array('limit' => , 'order' => 'id DESC')));
	//	printDebug($data);
		//$this->theme->render('error_log', $data)
	}
}

