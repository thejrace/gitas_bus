<?php

	require 'inc/init.php';

	$SAYFA_DATA = array(
		'title' 	=> 'Otobüs Hattını Düzenle',
		'action_id' => Actions::OTOBUS_HAT_DUZENLE
	);

	// izin kontrolu
	if( !in_array( $SAYFA_DATA["action_id"], Active_User::get_perm_actions() ) ) {
		header("Location: index.php");
	}
	if( Input::get("hid") == "" ) header("Location: index.php");
	
	$HAT = new Hat( Input::get("hid") );

	$NAV_ACTION_BUTTONS = new Nav_Action_Buttons(
		array(
			array(
				"action" 	=> Actions::OTOBUS_HATLAR_ERISIM,
				"title"		=> "Hatlara Dön"
			),
			array(
				"action" 	=> Actions::OTOBUS_DATA_ERISIM
			)
		)
	);

	require 'inc/header.php';
?>
	
	<div class="section">
		<div class="section-header">
			'<?php echo $HAT->get_details("hat"). "' " . $SAYFA_DATA["title"] ?>
		</div>

		<div class="dt-nav-container">		
			<?php echo $NAV_ACTION_BUTTONS->get_buttons() ?>
		</div>

		<div class="section-content">
			<div class="main-form-notf"></div>
			<form action="" method="post" id="ekle">

				<div class="input-container">
					<label for="ekle_hat">Hat</label>
					<input type="text" name="hat" id="ekle_hat" class="req" value="<?php echo $HAT->get_details("hat") ?>" />
				</div>

				<div class="input-container">
					<label for="ekle_aciklama">Açıklama</label>
					<input type="text" name="aciklama" id="ekle_aciklama" value="<?php echo $HAT->get_details("aciklama") ?>"/>
				</div>

				<div class="input-container submit-center">
					<input type="hidden" name="type" value="duzenle" />
					<input type="hidden" name="item_id" value="<?php echo Input::get("hid") ?>" />

					<input type="submit" class="navbtn orange" value="Kaydet"/>
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
					AHAJAX_V3.req( Base.AJAX_URL + 'hat.php', serialize(this), function(res){
						//console.log(res);
						if( res.ok ){
							EkleNotf.init(1, res.text );
							window.scrollTo(0, 0);
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