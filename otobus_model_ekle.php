<?php

	require 'inc/init.php';

	$SAYFA_DATA = array(
		'title' 	=> ' Markasına Model Ekle',
		'action_id' => Actions::OTOBUS_MODEL_EKLE
	);

	// izin kontrolu
	if( !in_array( $SAYFA_DATA["action_id"], Active_User::get_perm_actions() ) ) {
		header("Location: index.php");
	}

	if( Input::get('mid') == "" ) header("Location: index.php");

	$MARKA = new Otobus_Markasi(Input::get('mid') );

	$NAV_ACTION_BUTTONS = new Nav_Action_Buttons(
		array(
			
			array(
				"action" 	=> Actions::OTOBUS_MODELLER_VERI_ERISIM,
				"title"	    => $MARKA->get_details("marka") . " Modellerine Dön",
				"url"		=> URL_OTOBUS_MODELLER.$MARKA->get_details("id")
			),
			array(
				"action" 	=> Actions::OTOBUS_MARKALAR_VERI_ERISIM,
				"title"		=> "Markalara Git"
			),
			array(
				"action" 	=> Actions::OTOBUS_EKLE,
				"title"		=> "+ Otobüs Ekle"
			)
		)
	);

	require 'inc/header.php';
?>
	
	<div class="section">
		<div class="section-header">
			<?php echo $MARKA->get_details('marka') . $SAYFA_DATA["title"] ?>
		</div>

		<div class="dt-nav-container">
			<?php echo $NAV_ACTION_BUTTONS->get_buttons() ?>
		</div>

		<div class="section-content">
			<div class="main-form-notf"></div>
			<form action="" method="post" id="ekle">
				

				<div class="input-container">
					<label for="ekle_model">Model</label>
					<input type="text" name="model" id="ekle_model" class="req" />
				</div>

				<div class="input-container submit-center">
					<input type="hidden" name="type" value="model_ekle" />
					<input type="hidden" name="marka_id" value="<?php echo Input::get("mid") ?>" />
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
					Popup.start_loader();
					AHAJAX_V3.req( Base.AJAX_URL + 'marka_model.php', serialize(this), function(res){
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