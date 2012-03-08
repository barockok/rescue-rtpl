<?
/**
* 
*/
class db extends REST_Controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->options_avail = array(
			'conditions', 'limit', 'offset', 'order', 'group', 'select', 'from' ,'having', 'joins',
		);
	}
	public function find_get()
	{
	
		if(!$table = $this->uri->rsegment(3)) $this->response_error('no table name provide');
		$table = ucfirst(strtolower($table));
		if(!class_exists($table))
			$this->response_error('there no table name '.$table);
		
		$states = array('all', 'last', 'first');
		$state = $this->uri->rsegment(4);
		if(!$state)
			$state = 'all';
		if(!is_numeric($state))
			if(!in_array($state, $states))
				$this->response_error('unrecognize state');

		$options = element('options', $this->get());
		$serialize = element('serialize', $this->get());
		
		try {
			if($options == false)
				$db = $table::find($state);
			else
				$db = $table::find($state, $options);
			
			if(!$db)
				throw new Exception('No Result Found', 1);
			
			if($serialize == false)
				if($state != 'all')
					$this->response($db->to_array());
				else{
					$this->response($this->db_util->multiple_to_array($db));
				}
			else
				if($state != 'all')
					$this->response($db->to_array($serialize));
				else{
					$this->response($this->db_util->multiple_to_array($db, $serialize));
				}
		} catch (Exception $e) {
			$this->response_error($e);
		}
		
	}
	public function create_post()
	{
		if(!$table = $this->uri->rsegment(3)) $this->response_error('no table name provide');
		$table = ucfirst(strtolower($table));
		$state = $this->uri->rsegment(4);
		if(!class_exists($table))
			$this->response_error('there no table name '.$table);
		
		$data = $this->post('data');
		$serialize = $this->post('searialize');
		if(!$data)
			$this->response_error('no data passed to create new record');
		else if($data and !is_array($data))
			$this->response_error('not valid type data for new db record');
		
		try {
			$new = new $table($data);
			if(!$new->is_valid())
				$this->response_error(implode(', ', $new->errors->full_message()));
			$new->save();
	
			if($serialize)
				$this->response($new->to_array($serialize));
			else
				$this->response($new->to_array());
		} catch (Exception $e) {
			$this->response_error($e);
		}
		
		
	}
	public function update_post()
	{
		
		if(!$table = $this->uri->rsegment(3)) $this->response_error('no table name provide');
		$table = ucfirst(strtolower($table));
		$id = $this->uri->rsegment(4);
		if(!class_exists($table))
			$this->response_error('there no table name '.$table);
		$conditions = $this->post('conditions');
		if(!$id and !$conditions)
			$this->response_error('no identifier record');
		
		$data = $this->post('data');
		$serialize = $this->post('searialize');
		
		
		if(!$data)
				$this->response_error('no data passed to update record');
		else if($data and !is_array($data))
				$this->response_error('not valid type data for update db record');
		
		try {
			if(!$id and $conditions )
				$db = $table::find( 'last', array('conditions' => $conditions) );
			else if( ($id and $conditions) or ($id and !$conditions) ) 
				$db = $table::find($id);
			
		
			$db->update_attributes($data);
			if(!$db->is_valid())
				$this->response_error(implode(', ', $db->errors->full_message()));
			$db->save();
			
			if($serialize) 
			 	$this->response($db->to_array($serialize)) ;
			else
			 	$this->response($db->to_array());
			
				
		} catch (Exception $e) {
			$this->response_error($e);
		}
		
	}
	private function _clean_options($options)
	{
		$elements = elements($this->options_avail, $options, 'nope');
		foreach($elements as $key => $val)
			if($val == 'nope')
				unset($elements[$key]);
		if(count($elements) <1)
			return false;
	
		return $elements;
	}
	private function _build_query_option($options)
	{
		$options = $this->_clean_options($options);
		
		if($options == false)
			return false;
		return $options;
			
	}
	
}

?>