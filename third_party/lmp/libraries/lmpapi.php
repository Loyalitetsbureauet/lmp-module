<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Connection to the version 1.0 of the LMP API
*
* @author Dan Storm <ds@loyalitetsbureauet.dk>
* @version 1.0 
*/

class Lmpapi
{
	protected $EE;
	public $api_token;
	private $module_name = "lmp";
	
	public function __construct()
	{
		$this->EE =& get_instance();
		$this->_get_api_token();
	}
	
	/**
	* Gets all members
	* 
	* @return	array
	*/
	public function get_members()
	{
		return $this->_query('get_members');
	}
	
	/**
	* Returns a single member
	*
	* @param	int	ID of the member
	* @return	object
	*/
	public function get_member( $member_id )
	{
		return $this->_query('get_member', array('id' => $member_id));
	}	
	
	/**
	* Adds a member to the LMP
	*
	* @param	array	User data as specified in the documentation
	* @return	object
	*/
	public function add_member( $data )
	{		
		if( isset($data['groups']) )
		{
			$groups = $data['groups'];
			unset($data['groups']);
			for($i = 0; $i < count($groups); $i++)
			{
				$data['groups[' . $i . ']'] = $groups[$i];
			}						
		}			
		return $this->_query('add_member', $data);
	}	
		
	/**
	* Edits a member in the LMP
	*
	* @param	array	User data as specified in the documentation
	* @return	object
	*/	
	public function edit_member( $data )
	{	
		if( isset($data['groups']) )
		{
			$groups = $data['groups'];
			unset($data['groups']);
			for($i = 0; $i < count($groups); $i++)
			{
				$data['groups[' . $i . ']'] = $groups[$i];
			}						
		}	
		return $this->_query('edit_member', $data);
	}			

	/**
	* Deletes a specific member
	*
	* @param	int	The user ID you wan't to delete
	* @return	object
	*/	
	public function delete_member( $member_id )
	{		
		return $this->_query('delete_member', array('id' => $member_id));
	}			
	
	/**
	* Gets all groups
	* 
	* @return	object
	*/	
	public function get_groups()
	{
		return $this->_query('get_groups');
	}
	
	/**
	* Adds a group
	*
	* @param	string	The name of the new group
	* @return	object
	*/	
	public function add_group( $name )
	{
		return $this->_query('add_group', array('name' => $name));
	}
	
	/**
	* Edits a specific group
	*
	* @param	int	The ID of the group
	* @param	string	The new name of the group
	* @return	object
	*/	
	public function edit_group( $id, $name )
	{
		return $this->_query('edit_group', array('id' => $id, 'name' => $name));
	}	

	/**
	* Deletes a specific group
	*
	* @param	int	The ID of the group
	* @return	object
	*/	
	public function delete_group( $id )
	{
		return $this->_query('delete_group', array('id' => $id));
	}	
	
	/**
	* Gets customer purchases
	*
	* @param	int	The ID of the member
	* @return	object
	*/
	public function get_purchases( $id )
	{
		return $this->_query('get_purchases', array('id' => $id));
	}	

	/**
	* Adds a customer purchase
	*
	* @param	array	Data according to the documentation
	* @return	object
	*/
	public function add_purchases( $data )
	{
		return $this->_query('add_purchase', $data);
	}	
	
	/**
	* This method contacts the API
	*
	* @param	string	The function we need
	* @param	array	Data to send
	* @return 	object
	* @throws	Exception
	*/
	private function _query( $function, $data = array() )
	{
		
		switch( $function )
		{
			case 'get_members':
			case 'get_member':
			case 'add_member':
			case 'edit_member':
			case 'delete_member':
				$space = "members";
				break;
			case 'get_groups':
			case 'add_group':
			case 'edit_group':
			case 'delete_group':				
				$space = "groups";
				break;
			case 'get_purchases':
			case 'add_purchase':								
				$space = "purchases";
				break;
			default:
				show_error("The function {$function} is not defined!");
				
		}
		
		$ch = curl_init();
		$fields = array(
			'token' => $this->api_token,
			'function' => $function
		);
					
		if( ! empty($data) )
		{
			$fields = array_merge($fields, $data);
		}
		
		curl_setopt($ch, CURLOPT_URL, 'http://www.loyaltymanager.dk/api/v1/'.$space);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	   
		$content = curl_exec($ch);
		$response = json_decode($content);				
		
		$info = curl_getinfo($ch);
		$errno = curl_errno($ch);
		curl_close($ch);
		
		if( $errno == 28 )
		{
			throw new Exception("API Timed out - sorry :(");
		}
		

		if( $info['http_code'] != 200 )
		{
			throw new Exception($response->error_message);
		}
		
	   return $response;
				
		
	}
	
	/**
	* This methods gets the API token from the database
	*
	* @return	string
	*/
	private function _get_api_token()
	{
		$query = $this->EE->db->get($this->module_name);
		
		if( $query->num_rows() != 0 )
		{
			$this->api_token = $query->row()->api_key;
		} 
	}	

	// encrypt and decrypt are taken directly from the class.common.php file from the LoyaltyManager
	public function encrypt( $string )
	{
		$key = 'edgljh435p98dfgl';
		$result = '';
		
		for($i=0; $i<strlen($string); $i++)
		{
			$char = substr($string, $i, 1);
			$keychar = substr($key, ($i % strlen($key))-1, 1);
			$char = chr(ord($char)+ord($keychar));
			$result.=$char;
		}
		
		$result = base64_encode( $result );
		
		return urlencode( $result );
	}

	public function decrypt( $string )
	{
		urldecode( $string );
		
		$key = 'edgljh435p98dfgl';
		$result = '';
		$string = base64_decode( $string );
	
		for($i=0; $i<strlen($string); $i++)
		{
			$char = substr($string, $i, 1);
			$keychar = substr($key, ($i % strlen($key))-1, 1);
			$char = chr(ord($char)-ord($keychar));
			$result.=$char;
		}
	  
	  	$result = substr($result, 1);
		$result = explode('&', $result);
		
		$arr = array();
		foreach ($result as $result)
		{
			$exp 				= explode('=', $result);
			$arr[$exp[0]] 	= $exp[1]; 
		}
		
		return $arr;
		
	}		
}