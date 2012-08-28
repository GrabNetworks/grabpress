<fieldset id = "existing-account">
	<legend>Are You Sure?</legend>
	<form id="unlink" method="post" action="">
		<table>
			<tr><td>Email address<input type="text"/></td></tr>
			<tr><td>Password<input type="password"/></td></tr>
			<tr><td class = "account-help"><a>I don't remember my password</a><input type="button" class="button-primary" value="<?php _e( ($_POST[ 'action' ] == 'switch' ? 'Change' : 'Link').' Account') ?>"/><input type="button" class="button-secondary" value="<?php _e('Cancel') ?>"/>
		</table>
	</form>
</fieldset>