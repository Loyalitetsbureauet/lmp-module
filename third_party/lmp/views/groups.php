<?php

	$this->table->set_template($cp_table_template);	

	$this->table->set_columns(array(
    'group_id'  => array('header' => lang('group_id')),
    'group_name' => array('header' => lang('group_name')),
    'tools'  => array('header' => '')
	));
	

	$data = array();
	foreach($groups as $group)
	{
		$data[] = array(								
			'group_id' => $group->id,
			'group_name' => $group->name,
			'tools' => '<a href="'. BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=lmp'.AMP.'method=edit_group'.AMP.'group_id='.$group->id .'">'.lang('edit').'</a> | <a href="'. BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=lmp'.AMP.'method=delete_group'.AMP.'group_id='.$group->id .'">'.lang('delete').'</a>'
		);
	}
		
	$this->table->set_data($data);
	echo $this->table->generate();	
	
?>
<div class="tableSubmit">
	<button type="button" class="submit" style="cursor: pointer;" onclick="javascript: window.location.href='<?php echo BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=lmp'.AMP.'method=add_group'; ?>';"><?php echo lang('add_group'); ?></button>
</div>
	