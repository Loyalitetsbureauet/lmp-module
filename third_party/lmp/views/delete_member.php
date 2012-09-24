<h1><?php echo lang('delete_member_header'); ?></h1>

<p><?php echo lang('delete_member_text'); ?></p>

<?php 
	echo form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=lmp'.AMP.'method=delete_member'.AMP.'member_id='.$member->id);
	echo form_submit(array('name' => 'confirm_delete', 'value' => lang('confirm'), 'class' => 'submit'));
	echo form_close();
?>
