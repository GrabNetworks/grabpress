<fieldset id = "account">
	<legend>Are You Sure?</legend>
	<form id="unlink" method="post" action="">
			<input id="action" type="hidden" name="action" value="unlink-user"/>
		<table>
			<tr><td><span class="warning">WARNING:</span> You will no longer earn money using GrabPress on this site!</td></tr>
			<tr><td id="acknowledge" class="account-help">I understand and still want to unlink my Publisher account<input id="confirm" name="confirm" type="checkbox"/></td></tr>
			<tr><td class="account-help"><input type="button" id="submit_button" disabled="disabled" class="button-primary" value="Unlink Account"/><input type="button" class="button-secondary" id= "cancel_button" value="<?php _e('Cancel') ?>"/>
		</table>
	</form>
</fieldset>
<script>
	(function($){
		$('#confirm').click(function(){
	    	console.log( 'valid?');
			if ( $('#confirm').attr('checked') == 'checked' ){
				$( '#submit_button' ).removeAttr('disabled');
				$('#submit_button').click(function(){
					$('#unlink').submit();
				})				
			} else {
				$( '#submit_button' ).attr('disabled', 'disabled');	
				$( '#submit_button' ).off('click');
			}
		})
		
		$('#cancel_button').click(function(){
			$('#action').val('default');
			$('#unlink').submit();
		})
	})( jQuery )
</script>
