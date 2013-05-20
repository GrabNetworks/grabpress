<div class="wrap">
			<img src="http://grab-media.com/corpsite-static/images/grab_logo.jpg"/>
			<h2>GrabPress: Earn money with a Grab Publisher Account</h2>
			<?php
				try {
                                    $user = GrabPressAPI::get_user();
                                } catch(Exception $e) {
                                    GrabPress::log('API call exception: '.$e->getMessage());
                                }
				$linked = isset( $user->email);
				if( $linked ){?>
			<p class="account-help">This installation is linked to <?php echo $user->email; ?></p>					
				<?php }else{?>					
			<p class="account-help">This installation is not linked to a Publisher account. <a href="#">what is that?</a><br/>
			Linking GrabPress to your account allows us to keep track of the video ads displayed with your Grab content and make sure you get paid.</p>
				<?php }?>
			<p class="account-help">From here you can:</p>
			<?php
				echo $linked ? GrabPress::fetch('includes/account/chooser/linked.php', array("request" =>$request)) : GrabPress::fetch('includes/account/chooser/unlinked.php', array("request" =>$request));
			?>
			<script>
				(function( $ ){                                    
					$( '#account-chooser input' ).click( function(){
						$( '#account-chooser').submit();
					});                                        
				})( jQuery )
			</script>
			<?php switch( $request[ 'action' ] ){
					case 'default':
					case NULL:
						if($linked){
							break;
						}
					case 'switch':
						echo GrabPress::fetch('includes/account/forms/link.php', array("request" =>$request));
						break;
					case 'create':
						echo GrabPress::fetch('includes/account/forms/create.php', array("request" =>$request));
						break;
					case 'unlink':
						echo GrabPress::fetch('includes/account/forms/unlink.php', array("request" =>$request));
						break;
				}
			?>
</div>
