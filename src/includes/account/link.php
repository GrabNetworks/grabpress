<fieldset id = "account">
	<legend><?php echo $_POST[ 'action' ] == 'switch' ? 'Another' : 'Existing' ?> Account</legend>
	<form id="link-existing" method="post" action="">
		<table>
			<input type="hidden" name="action" id="action" value="link-user"/>
			<tr><td>Email address<input name="email" type="text"/></td></tr>
			<tr><td>Password<input name="password" type="password"/></td></tr>
			<tr><td class = "account-help">
					<a href="#">I don't remember my password</a>
					<input type="button" class="button-primary" id="submit_button" value="<?php _e( ($_POST[ 'action' ] == 'switch' ? 'Change' : 'Link').' Account') ?>"/>
					
					<input type="button" class="button-secondary" id="cancel_button" value="<?php _e('Cancel') ?>"/>
				</td>
			</tr>
		</table>
	</form>
</fieldset>
<script>
	(function($){
		$('#submit_button').click(function(){
			$('#unlink').submit();
		})
		
		$('#cancel_button').click(function(){
			$('#action').val('default');
			$('#unlink').submit();
		})
	})( jQuery )
</script>