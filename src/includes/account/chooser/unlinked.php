<form method="post" action="" id="account-chooser">
	<table>
		<tr>
			<?php $checked = ($request['action'] == 'default') ? ' checked="checked" ' : ''; ?>
			<td><input type="radio" name="action" value="default" <?php echo $checked ?>/>Link to an existing Publisher account</td>
		</tr>
		<tr>
			<?php $checked = ($request['action'] == 'create') ? ' checked="checked" ' : ''; ?>
			<td><input type="radio" name="action" value="create" <?php echo $checked ?>/>Create and link to a new account</td>
		</tr>
	</table>
</form>