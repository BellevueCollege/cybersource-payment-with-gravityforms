<?php
add_action('admin_init', 'cybersourceoptions_init' );
add_action('admin_menu', 'cybersourceoptions_add_page');

// Init plugin options to white list our options
function cybersourceoptions_init(){
	register_setting( 'cybersourceoptions_options', 'cybersource', 'cybersourceoptions_validate' );
}

// Add menu page
function cybersourceoptions_add_page() {
	add_options_page('CyberSource Options', 'CyberSource', 'manage_options', 'cybersourceoptions', 'cybersourceoptions_do_page');
}

// Draw the menu page itself
function cybersourceoptions_do_page() {
	?>
	<div class="wrap">
		<h2>Cyber Source Options</h2>
		<form method="post" action="options.php">
			<?php settings_fields('cybersourceoptions_options'); ?>
			<?php $options = get_option('cybersource'); ?>
			<table class="form-table">
				<tr valign="top"><th scope="row">Cyber Source Access Key</th>
					<td><input name="cybersource[cybersource_access_key]" type="text" value="<?php echo $options['cybersource_access_key']; ?>" /></td>
				</tr>
				<tr valign="top"><th scope="row">Cyber Source Form Post URL</th>
					<td><input type="text" name="cybersource[cybersource_form_post_url]" value="<?php echo $options['cybersource_form_post_url']; ?>" /></td>
				</tr>
                                <tr valign="top"><th scope="row">Cyber Source Locale</th>
					<td><input type="text" name="cybersource[cybersource_locale]" value="<?php echo $options['cybersource_locale']; ?>" /></td>
				</tr>
                                <tr valign="top"><th scope="row">Cyber Source Profile ID</th>
					<td><input type="text" name="cybersource[cybersource_profile_id]" value="<?php echo $options['cybersource_profile_id']; ?>" /></td>
				</tr>
                                <tr valign="top"><th scope="row">Cyber Source Secret Key</th>
					<td><input type="text" name="cybersource[cybersource_secret_key]" value="<?php echo $options['cybersource_secret_key']; ?>" /></td>
				</tr>
                                <tr valign="top"><th scope="row">Bill to Address Country</th>
					<td><input type="text" name="cybersource[cybersource_bill_to_address_country]" value="<?php echo $options['cybersource_bill_to_address_country']; ?>" /></td>
				</tr>
                                <tr valign="top"><th scope="row">Bill to Address State </th>
					<td><input type="text" name="cybersource[cybersource_bill_to_address_state]" value="<?php echo $options['cybersource_bill_to_address_state']; ?>" /></td>
				</tr>
                                <tr valign="top"><th scope="row">Currency</th>
					<td><input type="text" name="cybersource[cybersource_currency]" value="<?php echo $options['cybersource_currency']; ?>" /></td>
				</tr>
                                <tr valign="top"><th scope="row">Transaction Type</th>
					<td><input type="text" name="cybersource[cybersource_transaction_type]" value="<?php echo $options['cybersource_transaction_type']; ?>" /></td>
				</tr>
                                
<!--                                <tr valign="top"><th scope="row">Cyber Source Merchant ID</th>
					<td><input type="text" name="cybersource[cybersource_merchant_id]" value="<?php echo $options['cybersource_merchant_id']; ?>" /></td>
				</tr>
                                <tr valign="top"><th scope="row">Cyber Source Report User</th>
					<td><input type="text" name="cybersource[cybersource_report_user]" value="<?php echo $options['cybersource_report_user']; ?>" /></td>
				</tr>
                                <tr valign="top"><th scope="row">Cyber Source Report Password</th>
					<td><input type="text" name="cybersource[cybersource_report_password]" value="<?php echo $options['cybersource_report_password']; ?>" /></td>
				</tr>
                                <tr valign="top"><th scope="row">Cyber Source Report URL</th>
					<td><input type="text" name="cybersource[cybersource_report_url]" value="<?php echo $options['cybersource_report_url']; ?>" /></td>
				</tr>
                                <tr valign="top"><th scope="row">Cyber Source Shared Secret</th>
					<td><input type="text" name="cybersource[cybersource_shared_secret]" value="<?php echo $options['cybersource_shared_secret']; ?>" /></td>
				</tr>-->
			</table>
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
	</div>
	<?php	
}

// Sanitize and validate input. Accepts an array, return a sanitized array.
function cybersourceoptions_validate($input) {
	// Our first value is either 0 or 1
	//$input['access_key'] = $input['access_key'];
	
	// Say our second option must be safe text with no HTML tags
	//$input['form_post_url'] =  wp_filter_nohtml_kses($input['form_post_url']);
	
	return $input;
}
?>