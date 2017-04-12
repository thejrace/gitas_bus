<?php

	require 'inc/init.php';

	$SAYFA_DATA = array(
		'title' 	=> 'Düzenle',
		'action_id' => Actions::MALZEME_DUZENLE
	);

	// izin kontrolu
	if( !in_array( $SAYFA_DATA["action_id"], Active_User::get_perm_actions() ) ) {
		header("Location: index.php");
	}

	if( Input::get("mid") == "" ) header("Location: index.php");

	$MALZEME = new Malzeme( Input::get("mid") );

	$NAV_ACTION_BUTTONS = new Nav_Action_Buttons(
		array(
			array(
				"action" 	=> Actions::STOK_ERISIM
			)
		)
	);


	require 'inc/header.php';
?>
	
	<div class="section">
		<div class="section-header">
			Malzemeyi <?php echo $SAYFA_DATA["title"] ?>
		</div>

		<div class="dt-nav-container">
			<?php echo $NAV_ACTION_BUTTONS->get_buttons() ?>
		</div>


		<div class="section-content">
			<div class="main-form-notf"></div>
			<form action="" method="post" id="duzenle">
				
								<div class="input-container">
					<label for="ekle_malzeme_tipi">Malzeme Tipi</label>
					<select name="malzeme_tipi" id="ekle_malzeme_tipi" class="select_no_zero">
						<option value="0">Seçiniz</option>
						
						<?php

							$MALZEME_TIPLERI = DB::getInstance()->query("SELECT * FROM " . DBT_MALZEME_TIPLERI . " ORDER BY malzeme_tipi" )->results();
							foreach( $MALZEME_TIPLERI as $tip ){
								$selected = '';
								$MTipi = new Malzeme_Tipi( $tip["id"] );
								if( $MTipi->get_details("malzeme_tipi") == $MALZEME->get_details("malzeme_tipi") ) $selected = "selected";
								echo '<option '.$selected.' value="'.$MTipi->get_details("malzeme_tipi").'">'.$MTipi->get_details("malzeme_tipi").'</option>';
							}
						?>

					</select>
				</div>

				<div class="input-container">
					<label for="duzenle_stok_kodu">Stok Kodu</label>
					<input type="text" name="stok_kodu" id="duzenle_stok_kodu" class="req" value="<?php echo $MALZEME->get_details("stok_kodu")?>" />
				</div>

				<div class="input-container">
					<label for="duzenle_marka">Marka</label>
					<select name="marka" id="duzenle_marka" class="select_no_zero" >
						<option value="0">Seçiniz</option>

						<?php
							$selected = '';
							if( $MALZEME->get_details("marka") == "Genel" ) $selected = "selected";
						?>
						<option <?php echo $selected ?> value="Genel">Genel</option>
						
						<?php

							$MARKALAR = DB::getInstance()->query("SELECT * FROM " . DBT_OTOBUS_MARKALARI . " ORDER BY marka" )->results();
							foreach( $MARKALAR as $marka ){
								$selected = '';
								$OMarka = new Otobus_Markasi( $marka["id"] );
								if( $OMarka->get_details("marka") == $MALZEME->get_details("marka") ) $selected = "selected";
								echo '<option '.$selected.' value="'.$OMarka->get_details("marka").'">'.$OMarka->get_details("marka").'</option>';
							}
						?>
					</select>
				</div>

				
				<div class="input-container">
					<label for="duzenle_fiyat">Birim Fiyat</label>
					<input type="text" name="fiyat" id="duzenle_fiyat" class="req posnum" value="<?php echo $MALZEME->get_details("fiyat")?>" />
				</div>

				
				<div class="input-container">
					<label for="duzenle_aciklama">Açıklama</label>
					<textarea name="aciklama" id="duzenle_aciklama"><?php echo $MALZEME->get_details("aciklama")?></textarea>
				</div>




				<div class="input-container submit-center">
				<input type="hidden" name="type" value="malzeme_duzenle" />
				<input type="hidden" name="item_id" value="<?php echo Input::get("mid") ?>" />
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
					console.log(serialize(this));
					Popup.start_loader();
					AHAJAX_V3.req( Base.AJAX_URL + 'malzeme.php', serialize(this), function(res){
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