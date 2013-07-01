
<fieldset id="account" class="create">
	<legend>Create Account</legend>
	<form action="" method="post" id="register">
		<input id='id_action' type="hidden" name="action" value="create-user"/>
		<table>
	    	<tr><td>Email *</td><td><input id="id_email" type="text" name="email" maxlength="255"></td><td><img src="<?php echo GrabPress::get_green_icon_src( 'Ok' ); ?>" style="visibility:hidden" id="email_ok"/></td></tr>
			<tr><td>Create Password* (minimum 6 characters)</td><td><input type="password" name="password" id="id_password"></td><td><img src="<?php echo GrabPress::get_green_icon_src( 'Ok' ); ?>" style="visibility:hidden" id="pass1_ok"/></td></tr>
			<tr><td>Re-enter Password*</td><td><input type="password" name="password2" id="id_password2"></td><td><img src="<?php echo GrabPress::get_green_icon_src( 'Ok' ); ?>" style="visibility:hidden" id="pass2_ok"/></td></tr>
			<tr><td>First Name*</td><td><input id="id_first_name" type="text" name="first_name" maxlength="255"></td><td><img src="<?php echo GrabPress::get_green_icon_src( 'Ok' ); ?>" style="visibility:hidden" id="first_ok"/></td></tr>
			<tr><td>Last Name*</td><td><input id="id_last_name" type="text" name="last_name" maxlength="255"></td><td><img src="<?php echo GrabPress::get_green_icon_src( 'Ok' ); ?>" style="visibility:hidden" id="last_ok"/></td></tr>
			<tr><td>Company</td><td><input id="company" type="text" name="company" maxlength="255"></td></tr>			
			<tr><td>Content Category</td><td><select name="publisher_category_id" id="publisher_category_id"><?php
    			$category_arr = array('2'=>"Entertainment",'3'=>"Fashion & Beauty",'4'=>"Food & Beverage",'5'=>"Gaming",'6'=>"Health",'8'=>"Lifestyle General",'9'=>"Men's Lifestyle",'10'=>"Business & Finance",'11'=>"News",'15'=>"Sports",'16'=>"Technology",'17'=>"Woman's Lifestyle");
				$string_category = '<option value="1">All Categories</option>';
		        foreach($category_arr as $key => $val){
		            $string_category .= '<option value="'.$key.'">'.$val.'</option>'."\n";
		        }
		        echo $string_category;
			?></select></td></tr>
			<tr><td>Address 1*</td><td><input id="id_address1" type="text" name="address1" maxlength="255"></td><td><img src="<?php echo GrabPress::get_green_icon_src( 'Ok' ); ?>" style="visibility:hidden" id="address_ok"/></td></tr>
			<tr><td>Address 2</td><td><input id="id_address2" type="text" name="address2" maxlength="255"></td></tr>
			<tr><td>City*</td><td><input id="id_city" type="text" name="city" maxlength="255"></td><td><img src="<?php echo GrabPress::get_green_icon_src( 'Ok' ); ?>" style="visibility:hidden" id="city_ok"/></td></tr>
			<tr><td>State*</td><td><select name="state" id="id_state"><?php
    			$states_arr = array('AL'=>"Alabama",'AK'=>"Alaska",'AZ'=>"Arizona",'AR'=>"Arkansas",'CA'=>"California",'CO'=>"Colorado",'CT'=>"Connecticut",'DE'=>"Delaware",'DC'=>"District Of Columbia",'FL'=>"Florida",'GA'=>"Georgia",'HI'=>"Hawaii",'ID'=>"Idaho",'IL'=>"Illinois", 'IN'=>"Indiana", 'IA'=>"Iowa",  'KS'=>"Kansas",'KY'=>"Kentucky",'LA'=>"Louisiana",'ME'=>"Maine",'MD'=>"Maryland", 'MA'=>"Massachusetts",'MI'=>"Michigan",'MN'=>"Minnesota",'MS'=>"Mississippi",'MO'=>"Missouri",'MT'=>"Montana",'NE'=>"Nebraska",'NV'=>"Nevada",'NH'=>"New Hampshire",'NJ'=>"New Jersey",'NM'=>"New Mexico",'NY'=>"New York",'NC'=>"North Carolina",'ND'=>"North Dakota",'OH'=>"Ohio",'OK'=>"Oklahoma", 'OR'=>"Oregon",'PA'=>"Pennsylvania",'RI'=>"Rhode Island",'SC'=>"South Carolina",'SD'=>"South Dakota",'TN'=>"Tennessee",'TX'=>"Texas",'UT'=>"Utah",'VT'=>"Vermont",'VA'=>"Virginia",'WA'=>"Washington",'WV'=>"West Virginia",'WI'=>"Wisconsin",'WY'=>"Wyoming");
				$string = '<option value="">Select Your State</option>';
		        foreach($states_arr as $k => $v){
		            $string .= '<option value="'.$k.'">'.$v.'</option>'."\n";
		        }
		        echo $string;
			?></select></td><td><img src="<?php echo GrabPress::get_green_icon_src( 'Ok' ); ?>" style="visibility:hidden" id="state_ok"/></td></tr>
			<tr><td>Zip*</td><td><input id="id_zip" type="text" name="zip" maxlength="255"></td><td><img src="<?php echo GrabPress::get_green_icon_src( 'Ok' ); ?>" style="visibility:hidden" id="zip_ok"/></td></tr>
			<tr><td>Phone Number</td><td><input id="id_phone_number" type="text" name="phone_number" maxlength="255"></td></tr>
			<tr><td>Paypal ID</td><td><input id="id_paypal_id" type="text" name="paypal_id" maxlength="255"></td></tr>
			<tr><td>Website Domain*<select id= "id_protocol"><option>http://</option><option>https://</option></select></td><td><input id="id_site" type="text" maxlength="255"></td><td><img src="<?php echo GrabPress::get_green_icon_src( 'Ok' ); ?>" style="visibility:hidden" id="url_ok"/></td></tr>
			<input type="hidden" name = 'url' id='id_url' />
			<tr valign="bottom"><td/><td id ="id_tos">I agree to Grab Networks' <a href="http://www.grab-media.com/terms/" target="_blank">Terms of Service*</a></td><td><input type="checkbox" name="tos" id="id_agree"></td><td>
		</table>
	</form>
	<div id="buttons" >
		<span id="required" class = "account-help" >Note: All fields marked with an asterisk* are required.</span>
		<a id = "clear-form" href ="#">clear form</a>	
		<input type="button" class="button-primary" disabled="disabled" id="submit-button" value="<?php _e( $text = ($request[ 'action' ] == 'switch') ? 'Change' : 'Link'.' Account') ?>"/>
		<input type="button" class="button-secondary" id="cancel-button" value="<?php _e('Cancel') ?>"/>
	</div>
</fieldset>
	<script>
	console = console || { log:function(){}};
	(function( $ ){
			function notEmpty( id ){
				$( '#' + id ).val( $( '#' + id ).val().replace(/^\s*/, ''));
				return ( $( '#' + id ).val() != '' );
			}
			function validate(){
				//console.log( 'validate');
				var pass = $('#id_password').val();
				//console.log( pass);
				var phone = $( '#id_phone_number' ).val();
				//console.log(phone);
				var url = $( '#id_protocol' ).val() + $( '#id_site' ).val();
				//console.log(url);
				//$('#id_url').val(url);
				var email_valid =  $( '#id_email' ).val().match(/[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}/i );
				$( '#email_ok' ).css( 'visibility', email_valid ? 'visible' : 'hidden' );
				//console.log( 'email:' + email_valid );
				var pass_valid = ( pass.length > 5 );
				$( '#pass1_ok' ).css( 'visibility', pass_valid ? 'visible' : 'hidden' );
				var pass_match = pass_valid && $('#id_password2').val() == pass;
				$( '#pass2_ok' ).css( 'visibility', pass_match ? 'visible' : 'hidden' );
				//console.log( 'pass:' + pass_valid );
				var first_valid = notEmpty( 'id_first_name' );
				$( '#first_ok' ).css( 'visibility', first_valid ? 'visible' : 'hidden' );
				//console.log( 'first:' + first_valid );
				var last_valid = notEmpty( 'id_last_name' );
				$( '#last_ok' ).css( 'visibility', last_valid ? 'visible' : 'hidden' );
				//console.log( 'last:' + last_valid );
				var address_valid = notEmpty( 'id_address1' );
				$( '#address_ok' ).css( 'visibility', address_valid ? 'visible' : 'hidden' );
				//console.log( 'address:' + address_valid );
				var city_valid = notEmpty( 'id_city'  );
				$( '#city_ok' ).css( 'visibility', city_valid ? 'visible' : 'hidden' );
				//console.log( 'city:' + city_valid );
				var state_valid = notEmpty( 'id_state' );
				$( '#state_ok' ).css( 'visibility', state_valid ? 'visible' : 'hidden' );
				//console.log( 'state:' + state_valid );
				var zip_valid = $('#id_zip').val().match(/^\d{5}(-\d{4})?$/);
				$( '#zip_ok' ).css( 'visibility', zip_valid ? 'visible' : 'hidden' );
				//console.log( 'zip:' + zip_valid );
				var agree_valid = $('#id_agree').attr('checked');
				//console.log( 'agree:' + agree_valid );
				//var phone_valid = phone.length > 9 && phone.match(/^(1-?)?(\([2-9]\d{2}\)|[2-9]\d{2})-?[2-9]\d{2}-?\d{4}$/);
				//$( '#phone_ok' ).css( 'visibility', phone_valid ? 'visible' : 'hidden' );
				//console.log( 'phone:' + phone_valid );
				var url_valid = url.match( /([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}/gi);
				$( '#url_ok' ).css( 'visibility', url_valid ? 'visible' : 'hidden' );
				//console.log( 'url:' + url_valid );
				//var valid = email_valid && pass_valid && pass_match &&first_valid &&last_valid && address_valid && city_valid && state_valid &&zip_valid && agree_valid && phone_valid && url_valid;
				var valid = email_valid && pass_valid && pass_match &&first_valid &&last_valid && address_valid && city_valid && state_valid &&zip_valid && agree_valid && url_valid;
                                if (jQuery("#message p").text() == "There was an error connecting to the API! Please try again later!") {
                                    valid = false;
                                    jQuery(":input").attr('disabled', 'disabled');
                                }
				return valid
			}
			//$('#submit-button').bind('click', function(e){
			$('#submit-button').click(function(){
				$('#register').submit();
			});
                        var formChanged = false;
                        jQuery(':input', 'form').bind("change", function () {
                            formChanged = true;
                        });    
			$('#cancel-button').click(function(){
                            if (formChanged) {
				var confirm = window.confirm('Are you sure you want to cancel creation?\n\nAds played due to this plug-in will continue to not earn you any money, and your changes to this form will be lost.')
				if(confirm){
					$('#register')[0].reset();
					$('#id_action').val('default');
					$('#register').submit();
				}
                            } else {
                                window.location = "admin.php?page=gp-account";
                            }
			});
			function doValidation(){
		    	 //console.log( 'valid?');
				if ( validate() ){
					$( '#submit-button' ).removeAttr('disabled');
					$( '#submit-button' ).on('click');
					
				} else {
					$( '#submit-button' ).attr('disabled', 'disabled');
					if( $( '#submit_button' ).on ){
						$( '#submit_button' ).on('click');
					}else{
						$( '#submit_button' ).bind('click');
					}
				}
			};
                        function setConfirmUnload(on) {
                            window.onbeforeunload = (on) ? unloadMessage : null;
                        }

                        function unloadMessage() {
                            return 'You have entered new data on this page.' +
                            ' If you navigate away from this page without' +
                            ' first saving your data, the changes will be' +
                            ' lost.';
                          }
		    $("input").keyup(doValidation);
		    $("input").click(doValidation);
		    $("select").change(doValidation);
		    $("#clear-form").click(function() {
				$('#register')[0].reset();
		    	doValidation();
		    });
                    $('input:text, input:password, input:checkbox, select', 'form').bind("change", function () {
                        setConfirmUnload(true);
                    });                    
                    jQuery('#register').submit(function(){
                        window.onbeforeunload = null;
                    });
                    $(document).ready(function(){
                        //if we have an API connection error disable all inputs
                        if (jQuery("#message p").text() == "There was an error connecting to the API! Please try again later!") {
                            jQuery(":input").attr('disabled', 'disabled');
                        }
                    });		
		   })(jQuery)
	</script>
