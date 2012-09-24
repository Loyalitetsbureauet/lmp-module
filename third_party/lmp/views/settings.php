<?php
	
	echo form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=lmp'.AMP.'method=settings');

	
	$this->table->set_template($cp_table_template);	
	$this->table->set_heading(
		lang('get_api_token'),
		''		
	);
	
	$this->table->add_row(
		lang('your_api_token'),
		form_input('api_token', $api_token)
	);
	
	echo $this->table->generate();
	
	echo form_submit(array('name' => 'submit', 'value' => lang('update'), 'class' => 'submit'));
?>