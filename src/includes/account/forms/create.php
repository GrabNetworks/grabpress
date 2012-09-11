
<fieldset id="account" class="create">
	<legend>Create Account</legend>
	<form action="" method="post" id="register">
		<input id='id_action' type="hidden" name="action" value="create-user"/>
		<table>
	    	<tr><td>Email *</td><td><input id="id_email" type="text" name="email" maxlength="255"></td></tr>
			<tr><td>Create Password* (minimum 6 characters)</td><td><input type="password" name="password" id="id_password"></td></tr>
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
			<tr><td>Website Domain*<select id= "id_protocol"><option>http://</option><option>https://</option></select></td><td><input id="id_site" type="text" maxlength="255"></td></tr>
			<input type="hidden" name = 'url' id='id_url' />
			<tr><td/><td id ="id_tos">I agree to Grab Networks' <a href="http://www.grab-media.com/terms/" target="_blank">Terms of Service*</a><input type="checkbox" name="tos" id="id_agree"></td></tr>
			<tr>
				<td class = "account-help" >Note: All fields marked with an asterisk* are required.</td>
				<td id="buttons" class = "account-help" >
					<a href ="#">clear form</a>
					<input type="button" class="button-primary" style="display:none" id="submit_button" value="<?php _e( ($_POST[ 'action' ] == 'switch' ? 'Change' : 'Link').' Account') ?>"/>
					<input type="button" class="button-secondary" id="cancel_button" value="<?php _e('Cancel') ?>"/>
				</td>
			</tr>
		</table>
	</form>
</fieldset>
	<script>
	(function( $ ){
			function notEmpty( id ){
				return ( $( '#' + id ).val() != '' );
			}
			function validate(){
				//console.log( 'validate');
				var pass = $('#id_password').val();
				//console.log( pass);
				var phone = $( '#id_phone_number' ).val();
				//console.log(phone);
				var url = $( '#id_protocol' ).val() + $( '#id_site' ).val();
				//console.log(url);
				$('#id_url').val(url);
				var email_valid =  $( '#id_email' ).val().match(/[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}/i );
				//console.log( 'email:' + email_valid );
				var pass_valid = ( pass.length > 5 ) && $('#id_password2').val() == pass;
				//console.log( 'pass:' + pass_valid );
				var first_valid = notEmpty( 'id_first_name' );
				//console.log( 'first:' + first_valid );
				var last_valid = notEmpty( 'id_last_name' );
				//console.log( 'last:' + last_valid );
				var address_valid = notEmpty( 'id_address1' );
				//console.log( 'address:' + address_valid );
				var city_valid = notEmpty( 'id_city'  );
				//console.log( 'city:' + city_valid );
				var state_valid = notEmpty( 'id_state' );
				//console.log( 'state:' + state_valid );
				var zip_valid = notEmpty( 'id_zip' );
				//console.log( 'zip:' + zip_valid );
				var agree_valid = $('#id_agree').attr('checked');
				//console.log( 'agree:' + agree_valid );
				var phone_valid = phone.length > 9 && phone.match(/^(1-?)?(\([2-9]\d{2}\)|[2-9]\d{2})-?[2-9]\d{2}-?\d{4}$/);
				//console.log( 'phone:' + phone_valid );
				var url_valid = url.match( /([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}/gi);
				//console.log( 'url:' + url_valid );
				var valid = email_valid && pass_valid &&first_valid &&last_valid && address_valid && city_valid && state_valid &&zip_valid && agree_valid && phone_valid && url_valid;
				//console.log('valid:'+ valid )
				return valid
			}
			$('#register input.button-primary').click(function(){
				$('#register').submit();
			})
			$('#register input.button-secondary').click(function(){
				$('#action').val( 'default' );
				$('#register').submit();
			})
			function doValidation(){
		    	console.log( 'valid?');
				$( '#submit_button' ).css('display',validate() ? 'block' : 'none' );
			}
		    $("input").keyup(doValidation);
		    $("input").click(doValidation);
		    $("select").change(doValidation);
		   })(jQuery)
	</script>
