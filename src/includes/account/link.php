<fieldset id = "existing-account">
	<legend><?php echo $_POST[ 'action' ] == 'switch' ? 'Another' : 'Existing' ?> Account</legend>
	<form id="link-existing" method="post" action="">
		<table>
			<tr><td>Email address<input type="text"/></td></tr>
			<tr><td>Password<input type="password"/></td></tr>
			<tr><td class = "account-help">
					<a href="#">I don't remember my password</a>
					<input type="button" class="button-primary" value="<?php _e( ($_POST[ 'action' ] == 'switch' ? 'Change' : 'Link').' Account') ?>"/>
					<input type="button" class="button-secondary" value="<?php _e('Cancel') ?>"/>
				</td>
			</tr>
		</table>
	</form>
</fieldset>