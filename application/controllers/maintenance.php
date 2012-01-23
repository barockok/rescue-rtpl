<?
/**
* 
*/
class Maintenance extends MX_Controller
{
	
	function __construct()
	{
		parent::__construct();
	}
	public function db_backup()
	{
		$this->load->database('api');
		$this->load->dbutil();
		
		$backup =& $this->dbutil->backup(); 

		// Load the file helper and write the file to your server
		$this->load->helper('file');
		write_file('./dbscheme/backup.gz', $backup); 

		// Load the download helper and send the file to your desktop
		//$this->load->helper('download');
		//force_download('mybackup.gz', $backup);
	}
}
