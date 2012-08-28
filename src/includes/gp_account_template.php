<div class="wrap">
			<img src="http://grab-media.com/corpsite-static/images/grab_logo.jpg"/>
			<h2>GrabPress: Earn money with a Grab Publisher Account</h2>
			<?php
			$linked = FALSE;
			echo $linked ? GrabPress::fetch('includes/account/chooser/linked.php') : GrabPress::fetch('includes/account/chooser/unlinked.php');
			?>
			<script>
				(function( $ ){
					$( '#account-chooser input' ).click( function(){
						$( '#account-chooser').submit();
					})
				})( jQuery )
			</script>
			
			<?php switch( $_POST[ 'action' ] ){
					case 'link':
					case 'switch':
						echo GrabPress::fetch('includes/account/link.php');
						break;
					case 'create':
						echo GrabPress::fetch('includes/account/create.php');
						break;
					case 'unlink':
						echo GrabPress::fetch('includes/account/unlink.php');
						break;
				}
			?>