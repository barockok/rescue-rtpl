<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Db extends REST_Controller
{
	public function __construct()
	{
		parent::__construct();
	}
	public function find_get()
	{
		$table 	= $this->uri->rsegment(3);
		$id 	= (!$this->uri->rsegment(4)) ? 'all' : $this->uri->rsegment(4);
		
		$class_name = ucfirst($table);
		try {
			$q = $class_name::find($id);
			
			if($id == 'all'){
				$res = array();
				foreach($q as $i)
					array_push($res, $i->to_array());	
			}else{
				$res = $q->to_array();
			}

			$this->response($res, 200);
			
		} catch (Exception $e) {
			$this->response(array($e->getMessage()), 500);
		}
		
	}
	public function find_post()
	{
		$table 	= $this->uri->rsegment(3);
		$method = (!$this->uri->rsegment(4)) ? 'all' : $this->uri->rsegment(4);
		$class_name = ucfirst($table);
		if($this->post('options')){
			$params = elements(
			array('conditions','order','group','select','from','having','joins', 'include', 'limit', 'offset'),
			$this->post('options'),
			FALSE
			);
			foreach($params as $key => $val ) if($val == false ) unset($params[$key]) ;
			
		}else{
			$params = false;
		}
		
		try {
			
			$q = ($params != FALSE ) ? $class_name::$method($params) : $class_name::$method() ;
			$res = array();
			if($this->post('serialize')){
				$serialize = elements(array('only', 'except', 'methods' , 'include', 'only_method', 'skip_instruct'), $this->post('serialize'), FALSE);
				foreach($serialize as $key => $val ) if($val == false ) unset($serialize[$key]) ;
			}else{
				$serialize = false;
			}
			foreach($q as $item){
				if($serialize != false){
					array_push($res, $item->to_array($serialize));
				}else{
					array_push($res, $item->to_array());
				}
				
			}
			$this->response($res, 200);
			
		} catch (Exception $e) {
			$this->response($e->getMessage(), 500);
		}
			
	}
	public function create_post()
	{
		# code...
	}
	public function update_put()
	{
		# code...
	}
	public function delete_delete()
	{
		# code...
	}
	
}
