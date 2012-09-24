<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* The module file for the LMP Module
*
* @author Dan Storm <ds@loyalitetsbureauet.dk>
* @version 1.0 
*/

class Lmp {
	
	public $EE;
	public $return_data = '';
	
	public function __construct()
	{		
		
		$this->EE =& get_instance();
		$this->EE->load->library('lmpapi');	
		$this->EE->lang->loadfile('lmp');
	}
	
	public function add()
	{							
		$groups = $this->EE->TMPL->fetch_param('groups', '');
		$return = $this->EE->TMPL->fetch_param('return', $this->EE->functions->form_backtrack('0'));
		$dateformat = $this->EE->TMPL->fetch_param('dateformat', 'Y-m-d');		
		$birthday_fields = $this->EE->TMPL->fetch_param('birthday_fields', '');	
		$required = $this->EE->TMPL->fetch_param('required', '');
				
		$fixed_groups = array_filter(explode("|", $groups));
		$variables = array();
		
		$hidden_fields['ACT'] = $this->EE->functions->fetch_action_id('Lmp', 'member_query');
		$hidden_fields['RET'] = base64_encode($return);
		$hidden_fields['MET'] = base64_encode('add');
		$hidden_fields['DF'] = base64_encode($dateformat);
		if( !empty($required) )
		{
			$hidden_fields['RQ'] = base64_encode($required);
		}
		
		if( !empty($fixed_groups) )
		{
			foreach($fixed_groups as $key => $group_id)
			{
				$hidden_fields['groups['.$key.']'] = $group_id;
			}
		}
		
		if( !empty($birthday_fields) )
		{
			$hidden_fields['BF'] = base64_encode($birthday_fields);	
		}
		
       		
		$html = '';
		
		$form_details = array(
				  'id'             => $this->EE->TMPL->form_id,
                  'class'          => $this->EE->TMPL->form_class,
                  'hidden_fields'  => $hidden_fields,
                  'secure'         => TRUE,
                  //'onsubmit'       => "validate_form(); return false;"
                  );
				
		$html .= $this->EE->functions->form_declaration($form_details);
		
		try{
			$api_groups = $this->EE->lmpapi->get_groups();
			
			foreach($api_groups as $group)
			{
				$variables[0]['groups'][] = array('id' => $group->id, 'name' => $group->name);
			}
			
		}
		catch( Exception $e)
		{
			show_error($e->getMessage());	
		}
				
		$html .= $this->EE->TMPL->parse_variables($this->EE->TMPL->tagdata, $variables);				
		return $html . '</form>';
	}
	
	
	public function edit()
	{			
		$variables = array();
		$groups = $this->EE->TMPL->fetch_param('groups', '');
		$return = $this->EE->TMPL->fetch_param('return', $this->EE->functions->form_backtrack('0'));
		$dateformat = $this->EE->TMPL->fetch_param('dateformat', 'Y-m-d');		
		$birthday_fields = $this->EE->TMPL->fetch_param('birthday_fields', '');	
		$required = $this->EE->TMPL->fetch_param('required', '');

		if( $this->EE->input->get('v') )
		{
			$id = $this->EE->input->get('v');
			try{
				$member = $this->EE->lmpapi->get_member($id);				
				foreach($member->groups as $group)
				{
					$variables[0]['groups'][] = array('id' => $group->id, 'name' => $group->name, 'in_group' => $group->in_group);
				}
				
				
				
				$member = (array)$member;
				unset($member['groups']);
				foreach($member as $key => $value)
				{
					if( $key == 'birthday')
					{											
						if($value == '0000-00-00')
						{
							$variables[0][$key] = '';
							if( !empty($birthday_fields) )
							{
								$b_fields = explode("|", $birthday_fields);							
								$variables[0][$b_fields[0]] = "0000";
								$variables[0][$b_fields[1]] = "00";
								$variables[0][$b_fields[2]] = "00";		
							}
						}
						else
						{
							$birthdate = new DateTime($value);						
							$variables[0][$key] = $birthdate->format($dateformat);
							if( !empty($birthday_fields) )
							{
								$b_fields = explode("|", $birthday_fields);							
								$variables[0][$b_fields[0]] = $birthdate->format('Y');
								$variables[0][$b_fields[1]] = $birthdate->format('m');
								$variables[0][$b_fields[2]] = $birthdate->format('d');
							}
							
						}
						continue;
		
					}					

					$variables[0][$key] = $value;

				}
				
			}
			catch( Exception $e )
			{
				show_error($e->getMessage());
			}
			
		}		

		$fixed_groups = array_filter(explode("|", $groups));
			
		
		$hidden_fields['ACT'] = $this->EE->functions->fetch_action_id('Lmp', 'member_query');
		$hidden_fields['RET'] = base64_encode($return);
		$hidden_fields['MET'] = base64_encode('edit');
		$hidden_fields['DF'] = base64_encode($dateformat);
		$hidden_fields['V'] = $this->EE->input->get('v');
		
		if( !empty($required) )
		{
			$hidden_fields['RQ'] = base64_encode($required);
		}
						
		if( !empty($fixed_groups) )
		{
			foreach($fixed_groups as $key => $group_id)
			{
				$hidden_fields['groups['.$key.']'] = $group_id;
			}
		}
		
		if( !empty($birthday_fields) )
		{
			$hidden_fields['BF'] = base64_encode($birthday_fields);	
		}
        
		
		$html = '';

		$form_details = array(
				  'id'             => $this->EE->TMPL->form_id,
                  'class'          => $this->EE->TMPL->form_class,
                  'hidden_fields'  => $hidden_fields,
                  'secure'         => TRUE,
                  //'onsubmit'       => "validate_form(); return false;"
                  );
				
		$html .= $this->EE->functions->form_declaration($form_details);
				
				
		$html .= $this->EE->TMPL->parse_variables($this->EE->TMPL->tagdata, $variables);				
		return $html . '</form>';
	}	
	
	public function remove()
	{
		if( ! $this->EE->input->get('v') )
		{
			show_404();	
		}
		
		try
		{
			$member = $this->EE->lmpapi->get_member($this->EE->input->get('v'));
		}catch( Exception $e )
		{
			show_error($e->getMessage());	
		}
			
		$html = '';
		$variables = array();
		$variables[0]['id'] = $member->id;
		$variables[0]['name'] = $member->name;
		$variables[0]['mobile'] = $member->mobile;
		$variables[0]['email'] = $member->email;
		
				
		$auto_remove = $this->EE->TMPL->fetch_param('auto', 'no');
		$return = $this->EE->TMPL->fetch_param('return', '');
		
		if( $auto_remove == "yes" )
		{
			try
			{
				$this->EE->lmpapi->delete_member( $this->EE->input->get('v') );				
			}catch( Exception $e )
			{
				show_error($e->getMessage());
			}
			
			if(! empty($return) )
			{
				$this->EE->functions->redirect($return);				
			}
		}
		else
		{
			
			$html = '';
			$hidden_fields['ACT'] = $this->EE->functions->fetch_action_id('Lmp', 'member_query');
			$hidden_fields['RET'] = base64_encode($return);
			$hidden_fields['MET'] = base64_encode('remove');
			$hidden_fields['V'] = $this->EE->input->get('v');
			$form_details = array(
					  'id'             => $this->EE->TMPL->form_id,
	                  'class'          => $this->EE->TMPL->form_class,
	                  'hidden_fields'  => $hidden_fields,
	                  'secure'         => TRUE,
	                  //'onsubmit'       => "validate_form(); return false;"
	                  );
					
			$html .= $this->EE->functions->form_declaration($form_details);
			$form = true;
			
		}
		if( isset($form) )
		{
			return $html .= $this->EE->TMPL->parse_variables($this->EE->TMPL->tagdata, $variables) . '</form>';
		}

		return $this->EE->TMPL->parse_variables($this->EE->TMPL->tagdata, $variables);		
	}
	
	public function member_query()
	{												
		$method = base64_decode($_POST['MET']);
		try{
			
			if($method == 'add')
			{	
				$member_data = $this->_validate_post_data();
				$this->EE->lmpapi->add_member($member_data);
			}
			elseif($method == 'edit')
			{
				$member_data = $this->_validate_post_data();		
				$member_data['id'] = $_POST['V'];
				$this->EE->lmpapi->edit_member($member_data);
			}
			elseif($method == 'remove')
			{
				$id = $_POST['V'];
				$this->EE->lmpapi->delete_member( $id );
			}
									
			$ret = base64_decode($_POST['RET']);
			$this->EE->functions->redirect($ret);		
		}
		catch( Exception $e)
		{
			return $this->EE->output->show_user_error('general', $e->getMessage());		
		}
		
	}
	
	private function _validate_post_data()
	{
		$this->EE->load->helper('email');
		$errors = array();
		
		if( trim($_POST['name']) == '' )
		{
			$errors[] = $this->EE->lang->line('member_name_missing');
		}

		if( trim($_POST['email']) == '' )
		{
			$errors[] = $this->EE->lang->line('member_email_missing');
		}elseif( ! valid_email($_POST['email']) )
		{
			$errors[] = $this->EE->lang->line('member_email_not_valid');
		}
				
		if(isset($_POST['RQ']))
		{
			$required = explode("|", base64_decode($_POST['RQ']));
			foreach($required as $required_key)
			{
				if( ! isset($_POST[$required_key]) || empty($_POST[$required_key]) )
				{
					$errors[] = $this->EE->lang->line('member_required_fields_missing');
					break;
				}
			}
		}
		$member_data = array();
		if( isset($_POST['birthday']) )
		{					
			$birthday = DateTime::createFromFormat(base64_decode($_POST['DF']), $_POST['birthday']);
			if( $birthday )
			{
				$member_data['birthday'] = $birthday->format('Y-m-d');
			}
			else
			{
				$errors[] = $this->EE->lang->line('member_birthday_format');
			}
		}
		
		if( isset($_POST['BF']) && !isset($_POST['birthday']))
		{
			$b_fields = explode("|", base64_decode($_POST['BF']));
			$date = $_POST[$b_fields[0]] . '-' .$_POST[$b_fields[1]] . '-' .$_POST[$b_fields[2]];
			$birthday = DateTime::createFromFormat('Y-m-d', $date);
			if( $birthday )
			{
				$member_data['birthday'] = $birthday->format('Y-m-d');
			}			
			
		}
				
		
		
		if( !empty($errors) )
		{
			$this->EE->output->show_user_error('general', $errors);
		}
						
		$member_data['name'] = $_POST['name'];
		$member_data['email'] = $_POST['email'];
		$member_data['mobile'] = (isset($_POST['mobile'])) ? $_POST['mobile'] : '';
		$member_data['sex'] = (isset($_POST['sex'])) ? $_POST['sex'] : '';
		$member_data['address'] = (isset($_POST['address'])) ? $_POST['address'] : '';
		$member_data['zipcode'] = (isset($_POST['zipcode'])) ? $_POST['zipcode'] : '';
		$member_data['city'] = (isset($_POST['city'])) ? $_POST['city'] : '';
		$member_data['country'] = (isset($_POST['country'])) ? $_POST['country'] : '';					
		if(isset($_POST['groups']))
		{
			$member_data['groups'] = $_POST['groups'];
		}
				
		foreach($member_data as $key => $value)
		{
			if(empty($value))
			{
				unset($member_data[$key]);	
			}	
		}
		
		return $member_data;
	}
	
}