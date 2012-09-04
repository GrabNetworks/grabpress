<fieldset id = "account">
	<legend><?php echo $_POST[ 'action' ] == 'switch' ? 'Another' : 'Existing' ?> Account</legend>
	<form id="link-existing" method="post" action="">
		<table>
			<input type="hidden" name="action" value="link-user"/>
			<tr><td>Email address<input name="email" type="text"/></td></tr>
			<tr><td>Password<input name="password" type="password"/></td></tr>
			<tr><td class = "account-help">
					<a href="#">I don't remember my password</a>
					<input type="button" class="button-primary" value="<?php _e( ($_POST[ 'action' ] == 'switch' ? 'Change' : 'Link').' Account') ?>"/>
					<input type="button" class="button-secondary" value="<?php _e('Cancel') ?>"/>
				</td>
			</tr>
		</table>
	</form>
</fieldset>
<script>
	(function($){
		$('#link-existing input.button-primary').click(function(){
			$('#link-existing').submit();
		})
	})( jQuery )
</script>