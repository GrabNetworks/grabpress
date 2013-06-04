<fieldset id = "account">
	<legend><?php echo $request[ 'action' ] == 'switch' ? 'Another' : 'Existing' ?> Account</legend>
	<form id="link-existing" method="post" action="">
		<table>
			<input id="action-link-user" type="hidden" name="action" value="link-user" />
			<tr>
				<td>
					Email address<input name="email" id="email" type="text" value="<?php echo $email = (isset($request['email']) && ($request['email'] !== NULL)) ? $request['email'] : ''; ?>" />
				</td>
			</tr>

			<tr>
				<td>
					Password<input name="password" id="password" type="password"/>
				</td>
			</tr>
			<tr valign="bottom"><td class = "account-help">
					<a href="http://www.grab-media.com/publisherAdmin/forgotpw" target="_blank">Forgot password?</a>
					<input type="button" class="button-primary" disabled="disabled" id="submit_button" value="<?php _e( ($request[ 'action' ] == 'switch' ? 'Change' : 'Link').' Account') ?>"/>
					
					<input type="button" class="button-secondary" id="cancel_button" value="<?php _e('Cancel') ?>"/>
				</td>
			</tr>
		</table>
	</form>
</fieldset>
<script>
	//console = //console || { log:function(){}};
	(function( $ ){
			function notEmpty( id ){
				return ( $( '#' + id ).val() != '' );
			}
			function validate(){
				//console.log( 'validate');
				var email_valid =  $( '#email' ).val().match(/[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}/i );
				//console.log( 'email:' + email_valid );
				var pass_valid = ( $('#password').val().length > 0 ) ;
				//console.log( 'pass:' + pass_valid );
				var valid = email_valid && pass_valid;
                                if (jQuery("#message p").text() == "There was an error connecting to the API! Please try again later!") {
                                    jQuery(":input").attr('disabled', 'disabled');
                                    valid = false;
                                }
				//console.log('valid:'+ valid )
				return valid
			}
			function doValidation(){
		    	//console.log( 'valid?'); 
				if ( validate() ){
					$( '#submit_button' ).removeAttr('disabled');
					$('#submit_button').click(function(){
						$('#link-existing').submit();
					});
					
				} else {
					$( '#submit_button' ).attr('disabled', 'disabled');
					if( $( '#submit_button' ).off ){
						$( '#submit_button' ).off('click');
					}else{
						$( '#submit_button' ).unbind('click');
					}
				}
			}
                        function setConfirmUnload(on) {
                            window.onbeforeunload = (on) ? unloadMessage : null;
                        }

                        function unloadMessage() {
                            return 'You have entered new data in this page.' +
                            ' If you navigate away from this page without' +
                            ' first saving your data, the changes will be' +
                            ' lost.';
                          }
		    $("input").keyup(doValidation);
		    $("input").click(doValidation);
		    $("select").change(doValidation);
                    $(':text,:password', 'form').bind("change", function () {
                        setConfirmUnload(true);
                    });
                    jQuery('#link-existing').submit(function(){
                        window.onbeforeunload = null;
                    });
                    
                    $(document).ready(function(){
                        //if we have an API connection error disable all inputs
                        if (jQuery("#message p").text() == "There was an error connecting to the API! Please try again later!") {
                            jQuery(":input").attr('disabled', 'disabled');
                        };                        
                    });
			$('#cancel_button').click(function(e){
				if(window.confirm('Are you sure you want to cancel linking?\n\n' +
					<?php 
                                        try {    
                                            $user = GrabPressAPI::get_user();
                                        } catch(Exception $e) {
                                            GrabPress::log('API call exception: '.$e->getMessage());
                                        }
					$linked = isset( $user->email );
					if( $linked ){?>
						'Money earned with this installation will continue to be credited to the account associated with the email address <?php echo $user->email; ?>.'
					<?php }else{ ?>
						'Ads played due to this plug-in installation will not earn you any money.'
					<?php } ?>
					)){
						
					$('#link-existing')[0].reset()
					//$('#action').attr('value', 'default');
					$('#action-link-user').val('default');
					$('#link-existing').submit();
				}
			});
		})( jQuery )
                
</script>