<fieldset id = "account">
	<legend>Are You Sure?</legend>
	<form id="unlink" method="post" action="">
			<input id="action" type="hidden" name="action" value="unlink-user"/>
		<table>
			<tr><td><span class="warning">WARNING:</span> You will no longer earn money using GrabPress on this site!</td></tr>
			<tr><td id="acknowledge" class="account-help">I understand and still want to unlink my Publisher account<input id="confirm" name="confirm" type="checkbox"/></td></tr>
			<tr valign="bottom"><td class="account-help"><input type="button" id="submit_button" disabled="disabled" class="button-primary" value="Unlink Account"/><input type="button" class="button-secondary" id= "cancel_button" value="<?php _e('Cancel') ?>"/>
		</table>
	</form>
</fieldset>
<script>
	console = console || { log:function(){}};
	(function($){
		$('#confirm').click(function(){
                        if ( $('#confirm').attr('checked')){
				$( '#submit_button' ).removeAttr('disabled');
				$('#submit_button').click(function(){
					$('#unlink').submit();
				})				
			} else {
				$( '#submit_button' ).attr('disabled', 'disabled');	
				if( $( '#submit_button' ).off ){
					$( '#submit_button' ).off('click');
				}else{
					$( '#submit_button' ).unbind('click');
				}
			}
		})
		
		$('#cancel_button').click(function(){
			$('#unlink')[0].reset();
			$('#action').attr('value', 'default');
			$('#unlink').submit();
		})
	})( jQuery )
</script>
