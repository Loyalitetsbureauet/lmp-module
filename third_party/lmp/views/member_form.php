<?php
	
	for($i = date("Y"); $i >= 1900; $i--)
	{
		$b_years[$i] = $i;	
	}
	
	for($i = 1; $i <= 12; $i++)
	{
		$b_months[$i] = lang('month_' . $i);	
	}	
	
	for($i = 1; $i <= 31; $i++)
	{
		$b_days[$i] = $i;	
	}		
	
	$group_selects = '<select name="groups[]" multiple="multiple">';
	foreach($groups as $group)
	{
		$selected = '';
		
		if( isset($group->in_group) && $group->in_group && !isset($_POST['groups']) ) 
		{
			$selected = ' selected="selected"';	
			//$selected = ' SELECTED';	
		}
		elseif( isset($_POST['groups']) && in_array($group->id, $_POST['groups']) )
		{
			$selected = ' selected="selected"';	
		}
		
		$group_selects .= '<option value="'.$group->id.'"'.$selected.'>'.$group->name.'</option>';		
	}
	
	$group_selects .= '</select>';
	$birthdate = new DateTime($birthday);
	
	if(isset($id))
	{
		echo form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=lmp'.AMP.'method=edit_member'.AMP.'member_id='.$id);
	}
	else
	{
		echo form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=lmp'.AMP.'method=add_member');
	}
	
	$this->table->set_template($cp_table_template);	
	
	$this->table->set_heading(
                '&nbsp;',
                ''
    );	
		
	$this->table->add_row( lang('member_name'), 	form_input('name', set_value('name', $name) ) );
	$this->table->add_row( lang('member_email'),	form_input('email', set_value('email', $email) ) );
	$this->table->add_row( lang('member_mobile'),	form_input('mobile', set_value('mobile', ($mobile != 0) ? $mobile : '') ) );
	$this->table->add_row( lang('member_address'),	form_input('address', set_value('address', $address) ) );
	$this->table->add_row( lang('member_zipcode'),	form_input('zipcode', set_value('zipcode', $zipcode) ) );
	$this->table->add_row( lang('member_city'),		form_input('city', set_value('city', $city) ) );	
	$this->table->add_row( lang('member_country'),	form_input('country', set_value('country', $country) ) );
	$this->table->add_row( lang('member_sex'),		form_dropdown('sex', array('m' => lang('gender_m'), 'f' => lang('gender_f')), set_value('sex', $sex)) );		
	$this->table->add_row( lang('member_birthday'),	form_dropdown('b_year', $b_years, set_value('b_year', $birthdate->format('Y'))) . form_dropdown('b_month', $b_months, set_value('b_month', $birthdate->format('n')) ) . form_dropdown('b_day', $b_days, set_value('b_day', $birthdate->format('j')))  );
	$this->table->add_row( lang('member_groups'), 	$group_selects );
						
	echo $this->table->generate();	
	echo form_submit(array('name' => 'submit', 'value' => (isset($id)) ? lang('update') : lang('create'), 'class' => 'submit'));

	echo form_close();

?>