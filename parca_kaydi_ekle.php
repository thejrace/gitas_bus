<?php

	require 'inc/init.php';

	$SAYFA_DATA = array(
		'title' 	=> 'Parça Kaydı Ekle',
		'action_id' => Actions::OTOBUS_PARCA_VERI_GIRME
	);

	// izin kontrolu
	if( !in_array( $SAYFA_DATA["action_id"], Active_User::get_perm_actions() ) ) {
		header("Location: index.php");
	}

	if( Input::get("oid") == "" || Input::get("mid") == "" ) header("Location: index.php");

	$OTOBUS  = new Otobus( Input::get("oid") );
	$MALZEME = new Malzeme( Input::get("mid") );
	if(  $MALZEME->get_details("adet") == 0 ) {
		header("Location: " . URL_STOK );
	}

	$NAV_ACTION_BUTTONS = new Nav_Action_Buttons(
		array(
			array(
				"action" 	=> Actions::OTOBUS_DATA_ERISIM
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
			"<?php echo $OTOBUS->get_details("kod") ?>" Hat Kodlu Otobüse  <?php echo $SAYFA_DATA["title"] ?>
			<br>
			Otobüs : <?php echo $OTOBUS->get_details("marka") . " " . $OTOBUS->get_details("model")?>
			<br>
			Parça : <?php echo $MALZEME->get_details("malzeme_tipi") . " " . $MALZEME->get_details("marka")." ( ".$MALZEME->get_details("aciklama")." )"?>
		</div>

		<div class="section-content">

			<div class="dt-nav-container">
				<?php echo $NAV_ACTION_BUTTONS->get_buttons() ?>
			</div>

			<div class="main-form-notf"></div>
			<form action="" method="post" id="ekle">

				<div class="input-container">
					<label for="ekle_adet">Adet ( Stokta <?php echo $MALZEME->get_details("adet") ?> adet var )</label>
					<div class="qty-input-container">
						<button type="button" class="qtybtn azalt">-</button>
						<input type="text" name="adet" id="ekle_adet" class="req posnum" value="1" />
						<button type="button" class="qtybtn arttir">+</button>
					</div>
				</div>

				<div class="input-container">
					<label for="ekle_aciklama">Açıklama</label>
					<textarea name="aciklama" id="ekle_aciklama" rows="7" cols="30"></textarea>
				</div>


				<div class="input-container submit-center">
					<input type="hidden" name="type" value="ekle" />
					<input type="hidden" name="malzeme_id" value="<?php echo Input::get("mid")  ?>" />
					<input type="hidden" name="otobus_id" value="<?php echo Input::get("oid") ?>" />
					<input type="submit" class="navbtn orange" value="Ekle"/>
				</div>

			</form>

		</div>
	</div>

	<script type="text/javascript">

		AHReady(function(){


			var EkleNotf = new FormNotf( $AH('ekle') );
			add_event($AH('ekle'), "submit", function(ev){

				event_prevent_default(ev);
				
				if( !FormValidation.check(this) ) return;

				var form = this,
					qty_kontrol = FormValidation.custom_check($AH('ekle_adet'), "Stokta yeterli malzeme yok.", function(val){
					if( val > <?php echo $MALZEME->get_details("adet") ?> ) return false;
					return true;
				});
				if( !qty_kontrol ) return;
				Popup.start_loader();

				AHAJAX_V3.req( Base.AJAX_URL + 'parca_kaydi.php', serialize(this), function(res){
					// console.log(res);
					Popup.off();
					if( res.ok ){
						EkleNotf.init(1, res.text );
						form.reset();
						window.scrollTo(0, 0);
						setTimeout(function(){ location.reload()}, 1000);
					} else {
						FormValidation.show_serverside_errors( res.inputret );
					}

				});


				
			});


		});



	</script>

<?php

	require 'inc/footer.php';