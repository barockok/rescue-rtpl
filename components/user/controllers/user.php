<?

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends Platform_Controller {

	function __construct(){
		parent::__construct();
		$this->m = $this->load->model('comp/user_m');
	}
	function index(){
		$data = array(
			'name_f' => 'Zidni',
			'name_l' => 'Mubarock',
			'email' => 'zidmubarock@gmail.com',
			'password'=> md5('alzid4ever'),
		);
	}
	

}
