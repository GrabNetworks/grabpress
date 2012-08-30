			<p class="account-help">From here you can:</p>
			<form method="post" action="" id="account-chooser">
				<table>
					<tr>
						<?php $checked = $_POST['action'] == 'default' ? 'checked="checked" ' : ''; ?>
						<td><input type="radio" name="action" value="default" <?php echo $checked ?>/>Continue without Linking</td>
					</tr>
					<tr>
						<?php $checked = $_POST['action'] == 'link' ? ' checked="checked" ' : ''; ?>
						<td><input type="radio" name="action" value="link" <?php echo $checked ?>/>Link to an existing Publisher account</td>
					</tr>
					<tr>
						<?php $checked = $_POST['action'] == 'create' ? ' checked="checked" ' : ''; ?>
						<td><input type="radio" name="action" value="create" <?php echo $checked ?>/>Create and link to a new account</td>
					</tr>
				</table>
			</form>
