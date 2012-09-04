
<fieldset id="account" class="create">
	<legend>Create Account</legend>
	<form action="" method="post" id="register">
		<table>
	    	<tr><td>Email *</td><td><input id="id_email" type="text" name="email" maxlength="255"></td></tr>
			<tr><td>Create Password* (minium 6 characters)</td><td><input type="password" name="password1" id="id_password1"></td></tr>
			<tr><td>Re-enter Password*</td><td><input type="password" name="password2" id="id_password2"></td></tr>
			<tr><td>First Name*</td><td><input id="id_first_name" type="text" name="first_name" maxlength="255"></td></tr>
			<tr><td>Last Name*</td><td><input id="id_last_name" type="text" name="last_name" maxlength="255"></td></tr>
			<tr><td>Address 1*</td><td><input id="id_address1" type="text" name="address1" maxlength="255"></td></tr>
			<tr><td>Address 2</td><td><input id="id_address2" type="text" name="address2" maxlength="255"></td></tr>
			<tr><td>City*</td><td><input id="id_city" type="text" name="city" maxlength="255"></td></tr>
			<tr><td>State*</td><td><input id="id_state" type="text" name="state" maxlength="255"></td></tr>
			<tr><td>Zip*</td><td><input id="id_zip" type="text" name="zip" maxlength="255"></td></tr>
			<tr><td>Phone Number*</td><td><input id="id_phone_number" type="text" name="phone_number" maxlength="255"></td></tr>
			<tr><td>Paypal ID</td><td><input id="id_paypal_id" type="text" name="paypal_id" maxlength="255"></td></tr>
			<tr><td>Website Domain*<select><option>http://</option><option>https://</option></select></td><td><input id="id_url" type="text" name="url" maxlength="255"></td></tr>
			<tr><td/><td id ="tos">I agree to Grab Networks' <a href="http://www.grab-media.com/terms/" target="_blank">Terms of Service*</a><input type="checkbox" name="tos" id="id_tos"></td></tr>
			<tr>
				<td class = "account-help" >Note: All fields marked with an asterisk* are required.</td>
				<td id="buttons" class = "account-help" >
					<a href ="#">clear form</a>
					<input type="button" class="button-primary" value="<?php _e( 'Create Account') ?>"/>
					<input type="button" class="button-secondary" value="<?php _e('Cancel') ?>"/>
				</td>
			</tr>
		</table>
	</form>
</fieldset>