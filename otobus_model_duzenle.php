<?php

	require 'inc/init.php';

	$SAYFA_DATA = array(
		'title' 	=> 'Düzenle',
		'action_id' => Actions::OTOBUS_MODEL_DUZENLE
	);

	// izin kontrolu
	if( !in_array( $SAYFA_DATA["action_id"], Active_User::get_perm_actions() ) ) {
		header("Location: index.php");
	}

	if( Input::get('moid') == "" ) header("Location: index.php");

	$query = DB::getInstance()->query("SELECT * FROM " . DBT_OTOBUS_MODELLER ." WHERE id = ?", array( Input::get("moid")))->results();
	$MODEL_NAME = $query[0]['model'];

	$MARKA = new Otobus_Markasi( $query[0]['marka'] );

	$NAV_ACTION_BUTTONS = new Nav_Action_Buttons(
		array(
			array(
				"action" 	=> Actions::OTOBUS_EKLE,
				"title"		=> "+ Otobüs Ekle"
			),
			array(
				"action" 	=> Actions::OTOBUS_MODELLER_VERI_ERISIM,
				"title"	    => $MARKA->get_details("marka") . " Modellerine Dön",
				"url"		=> URL_OTOBUS_MODELLER.$MARKA->get_details("id")
			)
		)
	);


	require 'inc/header.php';
?>
	
	<div class="section">
		<div class="section-header">
			<?php echo $MARKA->get_details('marka') . ' "' . $MODEL_NAME . '" Modelini ' .$SAYFA_DATA["title"] ?>
		</div>

		<div class="dt-nav-container">
			<?php echo $NAV_ACTION_BUTTONS->get_buttons() ?>
		</div>

		<div class="section-content">
			<div class="main-form-notf"></div>
			<form action="" method="post" id="duzenle">
				

				<div class="input-container">
					<label for="duzenle_model">Model</label>
					<input type="text" name="model" id="duzenle_model" class="req" value="<?php echo $MARKA->get_model_name( Input::get("moid") ) ?>" />
				</div>

				<div class="input-container submit-center">
					<input type="hidden" name="type" value="model_duzenle" />
					<input type="hidden" name="marka_id" value="<?php echo $MARKA->get_details('id') ?>" />
					<input type="hidden" name="model_id" value="<?php echo Input::get('moid') ?>" />
					<input type="submit" class="navbtn orange" value="Kaydet"/>
				</div>

			</form>

		</div>
	</div>


	<script type="text/javascript">

		


		AHReady(function(){


			var EkleNotf = new FormNotf( $AH('duzenle') );
			add_event( $AH("duzenle"), "submit", function(ev){

				var form = this;
				if( FormValidation.check( this ) ){
					Popup.start_loader();
					AHAJAX_V3.req( Base.AJAX_URL + 'marka_model.php', serialize(this), function(res){
						// console.log(res);
						if( res.ok ){
							EkleNotf.init(1, res.text );
							window.scrollTo(0, 0);
						} else {
							EkleNotf.init(0, res.text );
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