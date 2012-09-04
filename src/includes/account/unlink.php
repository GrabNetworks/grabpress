<fieldset id = "account">
	<legend>Are You Sure?</legend>
	<form id="unlink" method="post" action="">
		<table>
			<tr><td><b>WARNING:</b> You will no longer earn money using GrabPress on this site!</td></tr>
			<tr><td id="acknowledge" class="account-help">I understand and still want to unlink my Publisher account<input type="checkbox"/></td></tr>
			<tr><td class="account-help">
				<input type="button" class="button-primary" value="Unlink Account"/><input type="button" class="button-secondary" value="<?php _e('Cancel') ?>"/>
			</td></tr>
		</table>
	</form>
</fieldset>
<script>
	(function($){
		$('#unlink input.button-primary').click(function(){
			$('#unlink').submit();
		})
	})( jQuery )
</script>