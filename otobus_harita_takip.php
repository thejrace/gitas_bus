<?php

	require 'inc/init.php';

	$SAYFA_DATA = array(
		'title' 	=> 'Harita Takip',
		'action_id' => Actions::HARITA_TAKIP_ERISIM
	);

	// izin kontrolu
	if( !in_array( $SAYFA_DATA["action_id"], Active_User::get_perm_actions() ) ) {
		header("Location: index.php");
	}

	if( Input::get("oid") == "" ) header("Location: index.php");
	if( Input::get("orer_id") == "" ) header("Location: index.php");

	$OTOBUS = new Otobus( Input::get("oid") );


	

	$JQUERYUI = true;
	require 'inc/header.php';
?>
	
	<div class="section-header">
		<?php echo $OTOBUS->get_details("kod") . " <small>( " . $OTOBUS->get_details("plaka") . " )</small> "  . $SAYFA_DATA["title"] ?>
	</div>



	<div class="section-content">
			
		<iframe src="http://ahsaphobby.net/otobus/iett/filo_veri_download/otobus_takip.php?oid=<?php echo Input::get("oid")?>&orer_id=<?php echo Input::get("orer_id")?>" height="420px" width="100%"></iframe>

	</div>
	

	<script type="text/javascript">


		
		AHReady(function(){

			

		});

	</script>

<?php
	require 'inc/footer.php';