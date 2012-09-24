<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Installation file for the LMP Module
*
* @author Dan Storm <ds@loyalitetsbureauet.dk>
* @version 1.0 
*/

class Lmp_upd
{
	public $version = '1.0';	
	protected $EE;
	
	public function __construct()
	{
		$this->EE =& get_instance();	
	}
	
	public function install()
	{
		$data = array(
			'module_name' => 'Lmp',
			'module_version' => $this->version,
			'has_cp_backend' => 'y',
			'has_publish_fields' => 'n'
		);
	
	    $this->EE->db->insert('modules', $data);

		$data = array(
		    'class'     => 'Lmp',
		    'method'    => 'member_query'
		);			
		$this->EE->db->insert('actions', $data);	    


	    
	    $fields = array(			
			'api_key'		=> array('type' => 'varchar', 'constraint' => '250')
	    );
	    $this->EE->load->dbforge();
	    $this->EE->dbforge->add_field($fields);
	    $this->EE->dbforge->create_table('lmp', TRUE);
	    
		return TRUE;
	}
	
	function update($current = '')
	{		    
		return TRUE;
	}
	
	function uninstall()
	{		    
		$this->EE->load->dbforge();
		$this->EE->dbforge->drop_table('lmp');
		$this->EE->load->dbforge();
		
		$this->EE->db->where('module_name', 'Lmp')->delete('modules');
		
		return TRUE;
	}		
}