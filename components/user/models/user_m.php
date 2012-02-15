<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class User_m extends CI_Model {
	
	public function __construct()
	{
		parent::__construct();
	}
	public function create($data)
	{
		if( $this->db->where('email', element('email', $data))
					 ->get('user')->num_rows() != 0) return false;
		
		$data['c_date'] = date('Y-m-d H:i:s');
		$this->db->insert('user', $data);
		return $this->getbyid($this->db->insert_id());
	}
	public function update($id, $data)
	{
		if( $this->db->where('email', element('email', $data))
		->where('id !=', $id)
		->get('user')
		->num_rows() != 0
		) return false;
		
		$data['m_date'] = date('Y-m-d H:i:s');
		$this->db->where('id', $id);
		$this->db->update('user', $data);
		
		return $this->getbyid($id);
	}
	public function getbyid($id)
	{
		$this->db->where('id', $id);
		$q = $this->db->get('user');
		if($q->num_rows() != 1) return false;
		return $q->row();
	}
	public function getbyhash($hash)
	{
		$this->db->where('hash', $hash);
		$q = $this->db->get('user');
		if($q->num_rows() != 1) return false;
		return $q->row();
	}
	public function search()
	{
		$q = $this->db->get('user');
		if($q->num_rows() == 0) return false;
		return $q->result();
	}
	
	
}
