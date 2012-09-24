
<h2><?php echo sprintf(lang('current_members_overview'), count($members)); ?></h2>
<?php

	$this->table->set_template($cp_table_template);	

	$this->table->set_columns(array(
    'member_name'  => array('header' => lang('member_name')),
    'member_email' => array('header' => lang('member_email')),
    'member_mobile'  => array('header' => lang('member_mobile')),
    'member_joined'  => array('header' => lang('member_joined')),
    'member_updated'  => array('header' => lang('member_updated')),
    'edit_member'  => array('header' => '')
	));
	

	$data = array();
	foreach($members as $member)
	{
		$data[] = array(								
			'member_name' => $member->name,
			'member_email' => $member->email,
			'member_mobile' => ($member->mobile) != 0 ? $member->mobile : '',
			'member_joined' => $member->joined,
			'member_updated' => $member->updated,
			'edit_member' => '<a href="'. BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=lmp'.AMP.'method=edit_member'.AMP.'member_id='.$member->id .'">'.lang('edit').'</a> | <a href="'. BASE.AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=lmp'.AMP.'method=delete_member'.AMP.'member_id='.$member->id .'">'.lang('delete').'</a>'
		);
	}
		
	$this->table->set_data($data);
	echo $this->table->generate();	
	


?>