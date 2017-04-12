<?php

	require 'inc/init.php';

	$SAYFA_DATA = array(
		'title' 	=> 'Düzenle',
		'action_id' => Actions::OTOBUS_DUZENLE
	);

	// izin kontrolu
	if( !in_array( $SAYFA_DATA["action_id"], Active_User::get_perm_actions() ) ) {
		header("Location: index.php");
	}

	if( Input::get("oid") == "" ) header("Location: index.php");

	$OTOBUS = new Otobus( Input::get("oid") );
	$MARKA_ID = 0;

	$NAV_ACTION_BUTTONS = new Nav_Action_Buttons(
		array(
			array(
				"action" 	=> Actions::OTOBUS_DATA_ERISIM,
				"title"	    => "Otobüslere Dön"
			)
		)
	);

	require 'inc/header.php';
?>
	
	<div class="section">
		<div class="section-header">
			<?php echo $OTOBUS->get_details("kod") ?> kodlu otobüsü <?php echo $SAYFA_DATA["title"] ?>
		</div>

		<div class="dt-nav-container">
			<?php echo $NAV_ACTION_BUTTONS->get_buttons() ?>
		</div>

		<div class="section-content">
			<div class="main-form-notf"></div>
			<form action="" method="post" id="duzenle">
				
				<div class="input-container">
					<label for="duzenle_marka">Marka</label>
					<select name="marka" id="duzenle_marka"  class="select_no_zero">
						<option value="0">Seçiniz</option>
						<?php

							$MARKALAR = DB::getInstance()->query("SELECT * FROM " . DBT_OTOBUS_MARKALARI . " ORDER BY marka" )->results();
							foreach( $MARKALAR as $marka ){
								$OMarka = new Otobus_Markasi( $marka["id"] );
								$selected = '';
								if( $OTOBUS->get_details("marka") == $marka["marka"] ){
									$MARKA_ID = $marka['id'];
									$selected = ' selected';
								} 
								echo '<option '.$selected.' value="'.$marka["id"].'">'.$OMarka->get_details("marka").'</option>';
							}

						?>
					</select>
				</div>

				<div class="input-container model-select">
					<label for="duzenle_model">Model</label>
					<select name="model" id="duzenle_model" class="select_no_zero">
						<option value="0">Marka Seçiniz...</option>

						<?php

							$Marka = new Otobus_Markasi( $MARKA_ID );
							foreach( $Marka->get_models() as $model ){
								$selected = '';
								if( $Marka->get_model_name($model['id']) == $OTOBUS->get_details('model') ){
									$selected = ' selected';
								} 
								echo '<option '.$selected.' value="'.$model["id"].'">'.$model['model'].'</option>';

							}

						?>

					</select>
				</div>

				<div class="input-container">
					<label for="duzenle_model_yili">Model Yılı</label>
					<input type="text" name="model_yili" id="duzenle_model_yili" class="req posnum" value="<?php echo $OTOBUS->get_details("model_yili") ?>" />
				</div>

				<div class="input-container">
					<label for="duzenle_hat">Hat</label>

					<select name="hat" id="duzenle_hat" class="hat-select">
						<option value="0">Seçiniz</option>
						<?php

							$HATLAR = DB::getInstance()->query("SELECT * FROM " . DBT_HATLAR . " ORDER BY hat" )->results();
							foreach( $HATLAR as $hat ){
								$Hat = new Hat( $hat["id"] );
								$selected = '';
								if( $OTOBUS->get_details("hat") == $hat["id"] ){
									$selected = ' selected';
								} 
								echo '<option '.$selected.' value="'.$hat["id"].'">'.$Hat->get_details("hat"). " - " .$Hat->get_details("aciklama").'</option>';
							}

						?>
					</select>
				</div>

				<div class="input-container">
					<label for="duzenle_kod">Kapı Kodu</label>
					<input type="text" name="kod" id="duzenle_kod" class="req" value="<?php echo $OTOBUS->get_details("kod") ?>" />
				</div>

				<div class="input-container">
					<label for="duzenle_plaka">Plaka</label>
					<input type="text" name="plaka" id="duzenle_plaka" class="req" value="<?php echo $OTOBUS->get_details("plaka") ?>" />
				</div>

				<div class="input-container">
					<label for="duzenle_sahip">Sahip</label>
					<input type="text" name="sahip" id="duzenle_sahip" class="req" value="<?php echo $OTOBUS->get_details("sahip") ?>" />
				</div>

				<div class="input-container">
					<label for="duzenle_ogs">OGS</label>
					<input type="text" name="ogs" id="duzenle_ogs" class="posnum" value="<?php echo $OTOBUS->get_details("ogs") ?>" />
				</div>

				<div class="input-container">
					<label for="duzenle_tip">Tip</label>
					<input type="text" name="tip" id="duzenle_tip" value="<?php echo $OTOBUS->get_details("tip") ?>" />
				</div>

				<div class="input-container">
					<label for="duzenle_aciklama">Açıklama</label>
					<textarea name="aciklama" id="duzenle_aciklama" cols="50" rows="5"><?php echo $OTOBUS->get_details("aciklama") ?></textarea>
				</div>

				<div class="input-container">
					<label for="duzenle_plaka_aciklama">Plaka Açıklama</label>
					<textarea name="plaka_aciklama" id="duzenle_plaka_aciklama" cols="50" rows="5"><?php echo $OTOBUS->get_details("plaka_aciklama") ?></textarea>
				</div>

				<div class="input-container submit-center">
					<input type="hidden" name="item_id" value="<?php echo $OTOBUS->get_details("id") ?>" />
					<input type="hidden" name="type" value="duzenle" />
					<input type="submit" class="navbtn orange" value="Kaydet"/>
				</div>

			</form>

		</div>
	</div>

	<script type="text/javascript">

		


		AHReady(function(){


			var EkleNotf = new FormNotf( $AH('duzenle') ),
				PREV_SELECTED_MODEL = 0,
				MODEL_SELECT = $AH('duzenle_model');

			add_event( $AH("duzenle"), "submit", function(ev){

				var form = this;
				if( FormValidation.check( this ) ){
					// console.log(serialize(this));
					Popup.start_loader();
					AHAJAX_V3.req( Base.AJAX_URL + 'otobus.php', serialize(this), function(res){
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

			add_event( $AH('duzenle_marka'), 'change', function(){
				if( this.value == 0 ){
					// seçiniz seçildiyse
					// selecti temizle
					add_select_option( MODEL_SELECT, "", 'Marka seçiniz...', true );
					MODEL_SELECT.disabled = true;
					PREV_SELECTED_MODEL = 0;
				} else if( this.value != 0 && PREV_SELECTED_MODEL != this.value ){
					// seçiniz harici veya bir önceki seçenekten farklı birşey secildiyse
					// selecti guncelliyoruz
					Popup.start_loader();
					AHAJAX_V3.req( Base.AJAX_URL + 'marka_model.php', manual_serialize({type:'get_models',marka_id:this.value}), function(res){
						// model selecti temizle
						clear_select_options( MODEL_SELECT );
						if( res.data.length > 0 ){
							// model girilmişse selecte yeni optionlari ekle
							for( var i = 0; i < res.data.length; i++ ) add_select_option( MODEL_SELECT, res.data[i].id, res.data[i].model, false );
							// kilidi ac
							MODEL_SELECT.disabled = false;
							// onceden error varsa temizle
							if( hasClass(MODEL_SELECT, "redborder") ) {
								FormValidation.hide_error( MODEL_SELECT );
								removeClass(MODEL_SELECT,"redborder");
							}
						} else {
							// model yoksa kitle selecti
							MODEL_SELECT.disabled = true;
							add_select_option( MODEL_SELECT, "", 'Model eklenmemiş...', true );
						}
					});
					Popup.off();
					PREV_SELECTED_MODEL = this.value;
				}

			});

		});
	</script>



<?php

	require 'inc/footer.php';