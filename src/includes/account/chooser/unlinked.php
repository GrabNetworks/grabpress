<p class="account-help">This installation is not linked to a Publisher account. <a href="#">what is that?</a><br/>
Linking GrabPress to your account allows us to keep track of the video ads displayed with your Grab content and make sure you get paid.</p>
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
