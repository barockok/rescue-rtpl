<?
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class MY_Input extends CI_Input {

	//original, unprocessed $_FILES array
    var $_files = array();
    
    //parsed array of file data
    var $files = array();

	public function __construct()
	{
		parent::__construct();
		$this->_clean_input_files();
	}

    //parse and reorder $_FILES array
    function _clean_input_files()
    {
        //save current $_FILES array
        $this->_files = $_FILES;
        
        //reset array of parsed files
        $this->files = array();
        
        //check $_FILES array is valid
        if(is_array($this->_files) && count($this->_files) > 0)
        {
            //reset $_FILES array
            $_FILES = array();
            
            //loop through array of $_FILES
            foreach($this->_files as $outer_key => $outer_value)
            {
                //count array of files
                $count = (is_array($outer_value['name']) ? count($outer_value['name']) : 0);
                
                //check outer value for array of file data
                if($count > 0)
                {
                    //loop through array of file data
                    foreach($outer_value['name'] as $inner_key => $inner_value)
                    {
                        //compile file data array key
                        $key = $outer_key . ($count > 1 ? "_" . $inner_key : "");
                        
                        //array of file data
                        $file = array();
                        
                        //gather file data from array of outer values
                        $file['error'] = $outer_value['error'][$inner_key];
                        $file['name'] = $outer_value['name'][$inner_key];
                        $file['size'] = $outer_value['size'][$inner_key];
                        $file['tmp_name'] = $outer_value['tmp_name'][$inner_key];
                        $file['type'] = $outer_value['type'][$inner_key];
                        
                        //append file key
                        $file['key'] = $key;
                        
                        //save file data to $_FILES array
                        $_FILES[$key] = $file;
                        
                        //check for single file
                        if($count == 1)
                        {
                            //save file data to array of parsed data
                            $this->files[$outer_key] = $file;
                        }
                        else
                        {
                            //save file data to array of parsed data
                            $this->files[$outer_key][$inner_key] = $file;
                        }
                    }
                }
                else
                {
                    //append file key
                    $outer_value['key'] = $outer_key;
                    
                    //save file data to $_FILES array
                    $_FILES[$outer_key] = $outer_value;
                    
                    //save file data to array of parsed data
                    $this->files[$outer_key] = $outer_value;
                }
            }
        }
    }
    
    /*
    //*******
    //
    //    helper methods
    //
    //*******
    */
    
    //fetch an item from parsed array of file data
    function file($index = "", $xss_clean = false)
    {
        //return item from parsed array of files
        return $this->_fetch_from_array($this->files, $index, $xss_clean);
    }
    
    //fetch an item from original array of $_FILES data
    function _file($index = "", $xss_clean = false)
    {
        //return item from $_FILES array
        return $this->_fetch_from_array($this->_files, $index, $xss_clean);
    }
    
    //return array of parsed file data
    function files()
    {
        //return parsed file array
        return $this->files;
    }
    
    //return array of original $_FILES
    function _files()
    {
        //return original $_FILES array
        return $this->_files;
    }
}
