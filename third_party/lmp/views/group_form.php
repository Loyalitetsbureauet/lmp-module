<?php 
	if( isset($id) )
	{
		echo form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=lmp'.AMP.'method=edit_group'.AMP.'group_id='.$id);
	}
	else
	{
		echo form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=lmp'.AMP.'method=add_group');
	}
	
	
	$this->table->set_template($cp_table_template);		
	$this->table->set_heading(
		'&nbsp;',
		''
    );
	
	$this->table->add_row( lang('group_name'), 	form_input('name', set_value('name', $name) ) );
	echo $this->table->generate();		
	
	echo form_submit(array('name' => 'submit', 'value' => (isset($id)) ? lang('update') : lang('create'), 'class' => 'submit'));	
	echo form_close();
?>