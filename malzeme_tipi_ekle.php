<?php

	require 'inc/init.php';

	$SAYFA_DATA = array(
		'title' 	=> 'Malzeme Tipi Ekle',
		'action_id' => Actions::MALZEME_TIPI_EKLE
	);

	// izin kontrolu
	if( !in_array( $SAYFA_DATA["action_id"], Active_User::get_perm_actions() ) ) {
		header("Location: index.php");
	}

	$NAV_ACTION_BUTTONS = new Nav_Action_Buttons(
		array(
			array(
				"action" 	=> Actions::MALZEME_EKLE
			),
			array(
				"action" 	=> Actions::STOK_ERISIM
			)
		)
	);

	require 'inc/header.php';
?>
	
	<div class="section">
		<div class="section-header">
			<?php echo $SAYFA_DATA["title"] ?>
		</div>

		<div class="dt-nav-container">		
			<?php echo $NAV_ACTION_BUTTONS->get_buttons() ?>
		</div>

		<div class="section-content">
			<div class="main-form-notf"></div>
			<form action="" method="post" id="ekle">

				<div class="input-container">
					<label for="ekle_malzeme_tipi">Malzeme Tipi</label>
					<input type="text" name="malzeme_tipi" id="ekle_malzeme_tipi" class="req" />
				</div>

				<div class="input-container submit-center">
					<input type="hidden" name="type" value="malzeme_tipi_ekle" />
					<input type="submit" class="navbtn orange" value="Ekle"/>
				</div>

			</form>

		</div>
	</div>

	<script type="text/javascript">

		AHReady(function(){


			var EkleNotf = new FormNotf( $AH('ekle') );

			add_event( $AH("ekle"), "submit", function(ev){

				var form = this;
				if( FormValidation.check( this ) ){
					//console.log(serialize(this));
					Popup.start_loader();
					AHAJAX_V3.req( Base.AJAX_URL + 'malzeme.php', serialize(this), function(res){
						//console.log(res);
						if( res.ok ){
							EkleNotf.init(1, res.text );
							window.scrollTo(0, 0);
							form.reset();

						} else {
							FormValidation.show_serverside_errors( res.inputret );
						}
						Popup.off();
					});
				}
				event_prevent_default( ev );
			});


		});


	</script>

<?php

	require 'inc/footer.php';