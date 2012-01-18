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
		
		$option = $this->get('options');
		$serialize = $this->get('serialize');	
		$class_name = ucfirst($table);
		try {
			
			$q = $class_name::find($id);		
			
			if($id == 'all'){
				$res = array();
				foreach($q as $i){	
					if($serialize)
						array_push($res, $i->to_array($serialize));	
					else
						array_push($res, $i->to_array());
				}
			}else{
				if($serialize)
					$res = $q->to_array($serialize);
				else
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
			if($params != FALSE) {
				$param_b = $params;
			
				if(array_key_exists('limit', $param_b)) unset($param_b['limit']);
				if(array_key_exists('offset', $param_b)) unset($param_b['offset']);
			
			}	
			
			$q_count = ($params != FALSE ) ? $class_name::count($param_b) : 1 ; 
			
			$res = array();
			if($this->post('serialize')){
				$serialize = elements(array('only', 'except', 'methods' , 'include', 'only_method', 'skip_instruct'), $this->post('serialize'), FALSE);
				foreach($serialize as $key => $val ) if($val == false ) unset($serialize[$key]) ;
			}else{
				$serialize = false;
			}
			if($method == 'all'){
			
				foreach($q as $item){
					if($serialize != false){
						array_push($res, $item->to_array($serialize));
					}else{
						array_push($res, $item->to_array());
					}	
				}
				if(count($res) < 1) $this->response(array('error' => 'no records found'), 500);
				
				$res = array(
					'results' => $res,
					'found_rows' => $q_count, 
				);
			}else{
				$res = (!$serialize) ? $q : $q->to_array($serialize);
			}
			
			$this->response($res, 200);
			
		} catch (Exception $e) {
			$this->response($e->getMessage(), 500);
		}
			
	}
	public function create_post()
	{
		if(! $table = $this->uri->rsegment(3) ) $this->response('no table specify', 500);
		if( ! $data = $this->post('data', FALSE) ) $this->response('no data passed', 500);
		
		$table_name = ucfirst($table);
		try {
			$q = new $table_name($data);
			if(!$q->is_valid())
				$this->response(array('error' => $q->errors->full_messages()), 500);
			else
				$q->save();
			$this->response($q->to_array(),200);
		} catch (Exception $e) {
			$this->response('something not good, sorry : ( )', 500);
		}
	}
	public function update_post()
	{
		if(! $table = $this->uri->rsegment(3) ) $this->response('no table specify', 500);
		if(! $id = $this->uri->rsegment(4)) $this->response('no ID provide', 500);
		if(! $data = $this->post('data', FALSE) ) $this->response('no data passed', 500);
		$class_name = ucfirst($table);
		try {
		
			$object = $class_name::find($id);
			$object->update_attributes($data);
			if(!$object->is_valid()) 
				$this->response($object->error->full_messages(), 500);
			else
				$object->save();
			$this->response($object->to_array(), 200);
			
		} catch (Exception $e) {
			$this->response(array('error' => true, 'message' => $e->getMessage()), 500);
		}
	}
	public function delete_delete()
	{
		if(! $table = $this->uri->rsegment(3) ) $this->response('no table specify', 500);
		if(! $id = $this->uri->rsegment(4)) $this->response('no ID provide', 500);
		$class_name = ucfirst($table);
		try {
			$q = $class_name::find($id);
			$q->delete();
			$this->response($q->to_array(), 200);
		} catch (Exception $e) {
			$this->response(array('error' => true, 'message' => $e->getMessage()), 500);
		}
	}
	
}
