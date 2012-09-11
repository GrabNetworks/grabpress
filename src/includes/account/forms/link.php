<fieldset id = "account">
	<legend><?php echo $_POST[ 'action' ] == 'switch' ? 'Another' : 'Existing' ?> Account</legend>
	<form id="link-existing" method="post" action="">
		<table>
			<input type="hidden" name="action" id="action" value="link-user"/>
			<tr><td>Email address<input name="email" id="id_email" type="text"/></td></tr>
			<tr><td>Password<input name="password" id="id_password" type="password"/></td></tr>
			<tr><td class = "account-help">
					<a href="#">I don't remember my password</a>
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
				console.log( 'validate');
				var email_valid =  $( '#id_email' ).val().match(/[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}/i );
				console.log( 'email:' + email_valid );
				var pass_valid = ( $('#id_password').val().length > 5 ) ;
				console.log( 'pass:' + pass_valid );
			
				var valid = email_valid && pass_valid;
				console.log('valid:'+ valid )
				return valid
			}
			function doValidation(){
		    	console.log( 'valid?');
				$( '#submit_button' ).css('display',validate()?'block':'none');
			}
		    $("input").keyup(doValidation);
		    $("input").click(doValidation);
		    $("select").change(doValidation);
			$('#submit_button').click(function(){
				$('#link-existing').submit();
			})
			
			$('#cancel_button').click(function(){
				$('#action').val('default');
				$('#link-existing').submit();
			})
		})( jQuery )
</script>