<div class="wrap">
			<img src="http://grab-media.com/corpsite-static/images/grab_logo.jpg"/>
			<h2>GrabPress: Earn money with a Grab Publisher Account</h2>
			<?php
			$linked = FALSE;
			$action = 'continue';
			if ( isset( $_POST[ 'action' ] ) ){
				$action = $_POST[ 'action' ];	
			}
			switch( $action ) {
				case 'continue' : ?>
			<?php		
			} 
			echo $linked ? GrabPress::fetch('includes/account/chooser/linked.php') : GrabPress::fetch('includes/account/chooser/unlinked.php');
			?>
			<script>
				(function( $ ){
					$( '#account-chooser input' ).click( function(){
						$( '#account-chooser').submit();
					})
				})( jQuery )
			</script>