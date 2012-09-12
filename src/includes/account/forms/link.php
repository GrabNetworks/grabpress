<fieldset id = "account">
	<legend><?php echo $_POST[ 'action' ] == 'switch' ? 'Another' : 'Existing' ?> Account</legend>
	<form id="link-existing" method="post" action="">
		<table>
			<input type="hidden" name="action" id="action" value="link-user"/>
			<tr><td>Email address<input name="email" id="email" type="text" value="<?php echo $_POST['email'] !== NULL ? $_POST['email'] : '';?>" /></td></tr>
			<tr><td>Password<input name="password" id="password" type="password"/></td></tr>
			<tr><td class = "account-help">
					<a href="#">I don't remember my password</a>
					<input type="button" class="button-primary" disabled="disabled" id="submit_button" value="<?php _e( ($_POST[ 'action' ] == 'switch' ? 'Change' : 'Link').' Account') ?>"/>
					
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
				var email_valid =  $( '#email' ).val().match(/[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}/i );
				console.log( 'email:' + email_valid );
				var pass_valid = ( $('#password').val().length > 0 ) ;
				console.log( 'pass:' + pass_valid );
			
				var valid = email_valid && pass_valid;
				console.log('valid:'+ valid )
				return valid
			}
			function doValidation(){
		    	// console.log( 'valid?');
				if ( validate() ){
					$( '#submit_button' ).removeAttr('disabled');
					$('#submit_button').click(function(){
						$('#link-existing').submit();
					});
					
				} else {
					$( '#submit_button' ).attr('disabled', 'disabled');
					$( '#submit_button' ).off('click');
				}
			}
		    $("input").keyup(doValidation);
		    $("input").click(doValidation);
		    $("select").change(doValidation);
			
			$('#cancel_button').click(function(){
				$('#action').val('default');
				$('#email').val('');
				$('#password').val('');
				$('#link-existing').submit();
			})
		})( jQuery )
</script>