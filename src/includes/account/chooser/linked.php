<form method="post" action="" id="account-chooser">
	<table>
		<tr>
			<?php $checked =  $_POST['action'] == 'default'  ? 'checked="checked" ' : ''; ?>
			<td><input type="radio" name="action" value="default" <?php echo $checked ?>/>Continue using this account</td>
		</tr>
		<tr>
			<?php $checked = $_POST['action'] == 'unlink' ? ' checked="checked" ' : ''; ?>
			<td><input type="radio" name="action" value="unlink" <?php echo $checked ?>/>Unlink account</td>
		</tr>
		<tr>
			<?php $checked = $_POST['action'] == 'switch' ? ' checked="checked" ' : ''; ?>
			<td><input type="radio" name="action" value="switch" <?php echo $checked ?>/>Link to another Publisher account</td>
		</tr>
	</table>
</form>
<p class="account-help">To update your Publisher account information, please visit our Web site by clicking <a href="#">here</a></p>

