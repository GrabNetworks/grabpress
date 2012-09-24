
<fieldset id="account" class="create">
	<legend>Create Account</legend>
	<form action="" method="post" id="register">
		<input id='id_action' type="hidden" name="action" value="create-user"/>
		<table>
	    	<tr><td>Email *</td><td><input id="id_email" type="text" name="email" maxlength="255"></td><td><img src="<?php echo GrabPress::get_green_icon_src( 'Ok' ); ?>" style="display:none" id="email_ok"/></td></tr>
			<tr><td>Create Password* (minimum 6 characters)</td><td><input type="password" name="password" id="id_password"></td><td><img src="<?php echo GrabPress::get_green_icon_src( 'Ok' ); ?>" style="display:none" id="pass1_ok"/></td></tr>
			<tr><td>Re-enter Password*</td><td><input type="password" name="password2" id="id_password2"></td><td><img src="<?php echo GrabPress::get_green_icon_src( 'Ok' ); ?>" style="display:none" id="pass2_ok"/></td></tr>
			<tr><td>First Name*</td><td><input id="id_first_name" type="text" name="first_name" maxlength="255"></td><td><img src="<?php echo GrabPress::get_green_icon_src( 'Ok' ); ?>" style="display:none" id="first_ok"/></td></tr>
			<tr><td>Last Name*</td><td><input id="id_last_name" type="text" name="last_name" maxlength="255"></td><td><img src="<?php echo GrabPress::get_green_icon_src( 'Ok' ); ?>" style="display:none" id="last_ok"/></td></tr>
			<tr><td>Address 1*</td><td><input id="id_address1" type="text" name="address1" maxlength="255"></td><td><img src="<?php echo GrabPress::get_green_icon_src( 'Ok' ); ?>" style="display:none" id="address_ok"/></td></tr>
			<tr><td>Address 2</td><td><input id="id_address2" type="text" name="address2" maxlength="255"></td></tr>
			<tr><td>City*</td><td><input id="id_city" type="text" name="city" maxlength="255"></td><td><img src="<?php echo GrabPress::get_green_icon_src( 'Ok' ); ?>" style="display:none" id="city_ok"/></td></tr>
			<tr><td>State*</td><td><input id="id_state" type="text" name="state" maxlength="255"></td><td><img src="<?php echo GrabPress::get_green_icon_src( 'Ok' ); ?>" style="display:none" id="state_ok"/></td></tr>
			<tr><td>Zip*</td><td><input id="id_zip" type="text" name="zip" maxlength="255"></td><td><img src="<?php echo GrabPress::get_green_icon_src( 'Ok' ); ?>" style="display:none" id="zip_ok"/></td></tr>
			<tr><td>Phone Number*</td><td><input id="id_phone_number" type="text" name="phone_number" maxlength="255"></td><td><img src="<?php echo GrabPress::get_green_icon_src( 'Ok' ); ?>" style="display:none" id="phone_ok"/></td></tr>
			<tr><td>Paypal ID</td><td><input id="id_paypal_id" type="text" name="paypal_id" maxlength="255"></td></tr>
			<tr><td>Website Domain*<select id= "id_protocol"><option>http://</option><option>https://</option></select></td><td><input id="id_site" type="text" maxlength="255"></td><td><img src="<?php echo GrabPress::get_green_icon_src( 'Ok' ); ?>" style="display:none" id="url_ok"/></td></tr>
			<input type="hidden" name = 'url' id='id_url' />
			<tr><td/><td id ="id_tos">I agree to Grab Networks' <a href="http://www.grab-media.com/terms/" target="_blank">Terms of Service*</a></td><td><input type="checkbox" name="tos" id="id_agree"></td><td>
		</table>
	</form>
	<div id="buttons" >
		<span id="required" class = "account-help" >Note: All fields marked with an asterisk* are required.</span>
		<a id = "clear-form" href ="#">clear form</a>
		<input type="button" class="button-primary" disabled="disabled" id="submit-button" value="<?php _e( ($_REQUEST[ 'action' ] == 'switch' ? 'Change' : 'Link').' Account') ?>"/>
		<input type="button" class="button-secondary" id="cancel-button" value="<?php _e('Cancel') ?>"/>
	</div>
</fieldset>
	<script>
	console = console || { log:function(){}};
	(function( $ ){
			function notEmpty( id ){
				$( '#' + id ).val( $( '#' + id ).val().replace(/^\s*/, ''));
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
				$( '#email_ok' ).css( 'display', email_valid ? 'block' : 'none' );
				//console.log( 'email:' + email_valid );
				var pass_valid = ( pass.length > 5 );
				$( '#pass1_ok' ).css( 'display', pass_valid ? 'block' : 'none' );
				var pass_match = pass_valid && $('#id_password2').val() == pass;
				$( '#pass2_ok' ).css( 'display', pass_match ? 'block' : 'none' );
				//console.log( 'pass:' + pass_valid );
				var first_valid = notEmpty( 'id_first_name' );
				$( '#first_ok' ).css( 'display', first_valid ? 'block' : 'none' );
				//console.log( 'first:' + first_valid );
				var last_valid = notEmpty( 'id_last_name' );
				$( '#last_ok' ).css( 'display', last_valid ? 'block' : 'none' );
				//console.log( 'last:' + last_valid );
				var address_valid = notEmpty( 'id_address1' );
				$( '#address_ok' ).css( 'display', address_valid ? 'block' : 'none' );
				//console.log( 'address:' + address_valid );
				var city_valid = notEmpty( 'id_city'  );
				$( '#city_ok' ).css( 'display', city_valid ? 'block' : 'none' );
				//console.log( 'city:' + city_valid );
				var state_valid = notEmpty( 'id_state' );
				$( '#state_ok' ).css( 'display', state_valid ? 'block' : 'none' );
				//console.log( 'state:' + state_valid );
				var zip_valid = $('#id_zip').val().match(/^\d{5}(-\d{4})?$/);
				$( '#zip_ok' ).css( 'display', zip_valid ? 'block' : 'none' );
				//console.log( 'zip:' + zip_valid );
				var agree_valid = $('#id_agree').attr('checked');
				//console.log( 'agree:' + agree_valid );
				var phone_valid = phone.length > 9 && phone.match(/^(1-?)?(\([2-9]\d{2}\)|[2-9]\d{2})-?[2-9]\d{2}-?\d{4}$/);
				$( '#phone_ok' ).css( 'display', phone_valid ? 'block' : 'none' );
				//console.log( 'phone:' + phone_valid );
				var url_valid = url.match( /([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}/gi);
				$( '#url_ok' ).css( 'display', url_valid ? 'block' : 'none' );
				//console.log( 'url:' + url_valid );
				var valid = email_valid && pass_valid && pass_match &&first_valid &&last_valid && address_valid && city_valid && state_valid &&zip_valid && agree_valid && phone_valid && url_valid;
				//console.log('valid:'+ valid )
				return valid
			}
			$('#submit-button').click(function(){
				$('#register').submit();
			});
			$('#cancel-button').click(function(){
				var confirm = window.confirm('Are you sure you want to cancel creation?\n\nAds played due to this plug-in will continue to not earn you any money, and your changes to this form will be lost.')
				if( confirm){
					$('#id_action').attr('value', 'default');
					$('#register')[0].reset();
					$('#register').submit();
				}
			});
			function doValidation(){
		    	// console.log( 'valid?');
				if ( validate() ){
					$( '#submit-button' ).removeAttr('disabled');
					$( '#submit-button' ).off('click');
					
				} else {
					$( '#submit-button' ).attr('disabled', 'disabled');
					if( $( '#submit_button' ).off ){
						$( '#submit_button' ).off('click');
					}else{
						$( '#submit_button' ).unbind('click');
					}
				}
			}
		    $("input").keyup(doValidation);
		    $("input").click(doValidation);
		    $("select").change(doValidation);
		    $("#clear-form").click(function() {
				$('#register')[0].reset();
		    	doValidation();
		    })
		   })(jQuery)
	</script>
