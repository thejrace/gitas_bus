<?php

	require 'inc/init.php';

	$SAYFA_DATA = array(
		'title' 	=> 'Ayarlar',
		'action_id' => Actions::AYARLAR
	);

	// izin kontrolu
	if( !in_array( $SAYFA_DATA["action_id"], Active_User::get_perm_actions() ) ) {
		header("Location: index.php");
	}

	require 'inc/header.php';
?>
	
	<div class="section-header">
		Ayarlar
	</div>

	<div class="section-content">
	
		
	</div>
	

	<script type="text/javascript">

		AHReady(function(){

			

		});

	</script>

<?php
	require 'inc/footer.php';