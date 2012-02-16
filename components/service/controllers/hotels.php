<?

if (! defined('BASEPATH')) exit('No direct script access');

class Hotels extends REST_Controller {

	//php 5 constructor
	function __construct() {
		parent::__construct();
	}
	
	//php 4 constructor
	public function search_post(){
		$posted = array(
			'checkin'		=>	$this->post('checkin'),
			'checkout'		=>	$this->post('checkout'),
			'passangers'	=>	$this->post('passangers'),
			'comp_search'	=>	($hotl = $this->post('comp_search')) ? $this->post('comp_search') : null ,
			'actor'			=>  ($actor = $this->post('actor')) ? $actor : 'CUS',
			'city'			=>	$this->post('city'),
		);
		
		$log = new Search_hotel_log($posted);
		if(!$log->is_valid()){
			$this->response($log->errors->full_messages(), 500);
		}else{
			$log->save();
		}
		suicide('service/hotels/exec_search/'.$log->id);
		$this->response($log->to_array());
	}
	
	public function search_get(){
		$data = array(
			'test'	=> 'tester',
		);
		$this->response($data);
	}
	
	public function exc_search_get(){
		$id = $this->uri->rsegment(3);
		//$hotel = $this->uri->rsegment(3);
		try {
			$log = Search_hotel_log::find($id);
		} catch (Exception $e) {
			$this->response(array('no log found'));
		}
		$param = $log->to_array();
		$post_data = array(
			'city'	=> element('city',$param),
			'checkin'	=>	show_date(element('checkin',$param), 'Y-m-d'),
			'checkout'	=>	show_date(element('checkout',$param),'Y-m-d'),
			'passengers'	=>	element('passangers',$param),
			'id'	=> element('id',$param),
		);
		$this->load->library('comp_hotel');
		$comp 	= $this->comp_hotel->load('Kamar');
		$result =  $comp->doSearch($post_data);
		foreach($result as $candidate_item)
		{
			$new_item = new Search_hotel_item($candidate_item);
			$new_item->save();
		}
		$this->response($result);
		
	}

}