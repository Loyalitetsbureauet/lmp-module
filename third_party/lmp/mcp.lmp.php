<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Control panel file for the LMP Module
*
* @author Dan Storm <ds@loyalitetsbureauet.dk>
* @version 1.0 
*/

class Lmp_mcp {
	
	protected $EE;
	
	private $api_token = '';		
	
	public function __construct()
	{
		$this->EE =& get_instance();
		$this->EE->load->library('lmpapi');	
		$this->EE->load->library('table');
		$this->_get_api_token();
		$this->EE->cp->set_right_nav(array(			
			lang('overview') => BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=lmp',
			lang('groups') => BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=lmp'.AMP.'method=groups',
			lang('add_member_menu') => BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=lmp'.AMP.'method=add_member',			
			lang('settings') => BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=lmp'.AMP.'method=settings'
			
		));		
		
		$this->_get_api_token();
		
	}

	public function index()
	{
		$data = array();			
		try
		{
			$data['members'] = $this->EE->lmpapi->get_members();				
		} 
		catch( Exception $e )
		{
			$this->EE->session->set_flashdata('message_error', $e->getMessage());
			$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=lmp'.AMP.'method=settings');
		}
		
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('overview'));
		$this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=lmp', $this->EE->lang->line('lmp_module_name'));

		return $this->EE->load->view('quick_stats', $data, TRUE);
	}
	
	public function delete_member()
	{
		$data = array();		
		try
		{			
			$data['member'] = $this->EE->lmpapi->get_member( $this->EE->input->get('member_id') );	
			$this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=lmp', $this->EE->lang->line('lmp_module_name'));
			$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('delete_member'));
			
		} 
		catch( Exception $e )
		{
			show_404();
		}		
		
		if( $this->EE->input->post('confirm_delete') )
		{
			try
			{
				$this->EE->lmpapi->delete_member( $data['member']->id );			
				$this->EE->session->set_flashdata('message_success', $this->EE->lang->line('deleted_member'));
				$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=lmp');							
			}
			catch( Exception $e )
			{
				$this->EE->javascript->output('$.ee_notice([ {message:"'. $e->getMessage() .'", type: "error"} ]);');	
			}		
			
			
		}
		
		return $this->EE->load->view('delete_member', $data, TRUE);
	}
	
	public function edit_member()
	{
		$data = array();

		try
		{			
			$member = $this->EE->lmpapi->get_member( $this->EE->input->get('member_id') );	
			$this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=lmp', $this->EE->lang->line('lmp_module_name'));
			$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('edit') . ' ' . $member->name);
			
			// Data member fields
			$tmp = (array)$member;
			$data = array_merge($data, $tmp);
		} 
		catch( Exception $e )
		{
			show_404();
		}
		
		$this->EE->load->library('form_validation');
		
		$this->EE->form_validation->set_rules('name', 'lang:member_name', 'trim|required');	
		$this->EE->form_validation->set_rules('email', 'lang:member_email', 'trim|required|valid_email');	
		$this->EE->form_validation->set_error_delimiters('{message:"', '", type: "error"}, ');
		
		if ($this->EE->form_validation->run() != FALSE)
		{
			
			$date = DateTime::createFromFormat('j-n-Y', $_POST['b_day'] . '-' . $_POST['b_month'] . '-' . $_POST['b_year']);						
			$member_data = array_merge($_POST, array('id' => $member->id, 'birthday' => $date->format('Y-m-d')));									
			try
			{
				$res = $this->EE->lmpapi->edit_member( $member_data );
				$this->EE->session->set_flashdata('message_success', $this->EE->lang->line('updated_member'));
				$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=lmp');							
			}
			catch( Exception $e )
			{
				$this->EE->javascript->output('$.ee_notice([ {message:"'. $e->getMessage() .'", type: "error"} ]);');	
			}
			
		}
		else
		{
	        $this->EE->javascript->output('$.ee_notice([' . substr(validation_errors(), 0, -2) .']);');		
		}		
		
		return $this->EE->load->view('member_form', $data, TRUE);
	}
	
	public function add_member()
	{
		$data = array();
		$data['birthday'] = '0000-00-00';
		$data['name'] = '';
		$data['email'] = '';
		$data['mobile'] = '';
		$data['address'] = '';
		$data['city'] = '';
		$data['zipcode'] = '';
		$data['country'] = '';
		$data['sex'] = '';
			
		$this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=lmp', $this->EE->lang->line('lmp_module_name'));
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('add_member') );
		
		try
		{			
			$data['groups'] = $member = $this->EE->lmpapi->get_groups();				
		} 
		catch( Exception $e )
		{
			show_error( $e->getMessage() );
		}
				
		$this->EE->load->library('form_validation');
		
		$this->EE->form_validation->set_rules('name', 'lang:member_name', 'trim|required');	
		$this->EE->form_validation->set_rules('email', 'lang:member_email', 'trim|required|valid_email');	
		$this->EE->form_validation->set_error_delimiters('{message:"', '", type: "error"}, ');
		
		if ($this->EE->form_validation->run() != FALSE)
		{
			
			$date = DateTime::createFromFormat('j-n-Y', $_POST['b_day'] . '-' . $_POST['b_month'] . '-' . $_POST['b_year']);						
			$member_data = array_merge($_POST, array('id' => $member->id, 'birthday' => $date->format('Y-m-d')));									
			try
			{
				$res = $this->EE->lmpapi->add_member( $member_data );
				$this->EE->session->set_flashdata('message_success', $this->EE->lang->line('created_member'));
				$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=lmp');							
			}
			catch( Exception $e )
			{
				$this->EE->javascript->output('$.ee_notice([ {message:"'. $e->getMessage() .'", type: "error"} ]);');	
			}
			
		}
		else
		{
	        $this->EE->javascript->output('$.ee_notice([' . substr(validation_errors(), 0, -2) .']);');		
		}		
				
		return $this->EE->load->view('member_form', $data, TRUE);
	}
	
	public function groups()
	{
		$data = array();
		$this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=lmp', $this->EE->lang->line('lmp_module_name'));
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('groups') );
		
		try
		{			
			$data['groups'] = $this->EE->lmpapi->get_groups();				
		} 
		catch( Exception $e )
		{
			show_error( $e->getMessage() );
		}
				
		return $this->EE->load->view('groups', $data, TRUE);
		
	}
	
	public function edit_group()
	{
		$data = array();
		$this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=lmp' ,$this->EE->lang->line('lmp_module_name'));
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('edit_group') );
		
		try
		{			
			$groups = $this->EE->lmpapi->get_groups();			
		} 
		catch( Exception $e )
		{
			show_error( $e->getMessage() );
		}
		
		foreach($groups as $group)
		{
			if( $group->id == $this->EE->input->get('group_id') )
			{
				$data['id'] = $group->id;
				$data['name'] = $group->name;
				$found = TRUE;
				break;
			}
		}
			
		if( !isset($found) )
		{
			show_404();	
		}
		

		$this->EE->load->library('form_validation');		
		$this->EE->form_validation->set_rules('name', 'lang:member_name', 'trim|required');	
		
		if ($this->EE->form_validation->run() != FALSE)
		{		
			try
			{
				$res = $this->EE->lmpapi->edit_group( $data['id'], set_value('name') );
				$this->EE->session->set_flashdata('message_success', $this->EE->lang->line('updated_group'));
				$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=lmp'.AMP.'method=groups');
			}
			catch( Exception $e )
			{
				$this->EE->javascript->output('$.ee_notice([ {message:"'. $e->getMessage() .'", type: "error"} ]);');	
			}
			
		}
		else
		{
	        $this->EE->javascript->output('$.ee_notice([' . substr(validation_errors(), 0, -2) .']);');		
		}		
		
				
		return $this->EE->load->view('group_form', $data, TRUE);		
	}
	
	public function add_group()
	{
		$data = array('name' => '');
		$this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=lmp' ,$this->EE->lang->line('lmp_module_name'));
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('add_group') );
		
		$this->EE->load->library('form_validation');		
		$this->EE->form_validation->set_rules('name', 'lang:member_name', 'trim|required');	
		
		if ($this->EE->form_validation->run() != FALSE)
		{		
			try
			{
				$res = $this->EE->lmpapi->add_group( set_value('name') );
				$this->EE->session->set_flashdata('message_success', $this->EE->lang->line('created_group'));
				$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=lmp'.AMP.'method=groups');
			}
			catch( Exception $e )
			{
				$this->EE->javascript->output('$.ee_notice([ {message:"'. $e->getMessage() .'", type: "error"} ]);');	
			}
			
		}
		else
		{
	        $this->EE->javascript->output('$.ee_notice([' . substr(validation_errors(), 0, -2) .']);');		
		}		
		
				
		return $this->EE->load->view('group_form', $data, TRUE);		
	}	
	
	public function delete_group()
	{
		$data = array();		
		$this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=lmp' ,$this->EE->lang->line('lmp_module_name'));
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('delete_group') );
		
		try
		{			
			$groups = $this->EE->lmpapi->get_groups();			
		} 
		catch( Exception $e )
		{
			show_error( $e->getMessage() );
		}
		
		foreach($groups as $group)
		{
			if( $group->id == $this->EE->input->get('group_id') )
			{
				$data['id'] = $group->id;
				$data['name'] = $group->name;
				$found = TRUE;
				break;
			}
		}
			
		if( !isset($found) )
		{
			show_404();	
		}
		
		if( $this->EE->input->post('confirm_delete') )
		{
			try
			{
				$this->EE->lmpapi->delete_group( $data['id'] );			
				$this->EE->session->set_flashdata('message_success', $this->EE->lang->line('deleted_group'));
				$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=lmp'.AMP.'method=groups');							
			}
			catch( Exception $e )
			{
				$this->EE->javascript->output('$.ee_notice([ {message:"'. $e->getMessage() .'", type: "error"} ]);');	
			}								
		}
		
		return $this->EE->load->view('delete_group', $data, TRUE);
	}	
			
	public function settings()
	{			
		$this->EE->load->library('table');
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('lmp_module_name'));
		$this->EE->cp->set_breadcrumb(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=lmp', $this->EE->lang->line('lmp_module_name'));
		if($this->EE->input->post('api_token'))
		{
			$this->_set_api_token( $this->EE->input->post('api_token') );
			$this->EE->session->set_flashdata('message_success', $this->EE->lang->line('updated_api_token'));
			$this->EE->functions->redirect(BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=lmp');
		}
		
		return $this->EE->load->view('settings', array('api_token' => $this->api_token), TRUE);
	}
	
	private function _get_api_token()
	{
		$query = $this->EE->db->get('lmp');
		
		if( $query->num_rows() != 0 )
		{
			$this->api_token = $query->row()->api_key;
		} 
	}
	
	private function _set_api_token( $token )
	{
		$this->EE->db->truncate('lmp');
		$this->EE->db->insert('lmp', array('api_key' => $token));		
	}	
	
}