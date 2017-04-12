<?php

	require 'inc/init.php';

	$SAYFA_DATA = array(
		'title' 	=> 'Otobüs Ekle',
		'action_id' => Actions::OTOBUS_EKLE
	);

	// izin kontrolu
	if( !in_array( $SAYFA_DATA["action_id"], Active_User::get_perm_actions() ) ) {
		header("Location: index.php");
	}

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
			<?php echo $SAYFA_DATA["title"] ?>
		</div>

		<div class="dt-nav-container">
			<?php echo $NAV_ACTION_BUTTONS->get_buttons() ?>
		</div>

		<div class="section-content">
			<div class="main-form-notf"></div>
			<form action="" method="post" id="ekle">
				
				<div class="input-container">
					<label for="ekle_marka">Marka</label>
					<select name="marka" id="ekle_marka" class="select_no_zero" >
						<option value="0">Seçiniz</option>
						
						<?php

							$MARKALAR = DB::getInstance()->query("SELECT * FROM " . DBT_OTOBUS_MARKALARI . " ORDER BY marka" )->results();
							foreach( $MARKALAR as $marka ){
								$OMarka = new Otobus_Markasi( $marka["id"] );
								echo '<option value="'.$marka["id"].'">'.$OMarka->get_details("marka").'</option>';
							}
						?>
					</select>
				</div>

				<div class="input-container model-select">
					<label for="ekle_model">Model</label>
					<select name="model" id="ekle_model" class="select_no_zero" disabled>
						<option value="0">Marka Seçiniz...</option>
					</select>
				</div>

				<div class="input-container">
					<label for="ekle_model_yili">Model Yılı</label>
					<input type="text" name="model_yili" id="ekle_model_yili" class="req posnum" />
				</div>

				<div class="input-container">
					<label for="ekle_hat">Hat</label>
					<select name="hat" id="ekle_hat" >
						<option value="0">Seçiniz</option>
						<?php

							$HATLAR = DB::getInstance()->query("SELECT * FROM " . DBT_HATLAR . " ORDER BY hat" )->results();
							foreach( $HATLAR as $hat ){
								$Hat = new Hat( $hat["id"] );
								echo '<option value="'.$hat["id"].'">'.$Hat->get_details("hat"). " - " .$Hat->get_details("aciklama").'</option>';
							}

						?>
					</select>
				</div>

				<div class="input-container">
					<label for="ekle_kod">Kapı Kodu</label>
					<input type="text" name="kod" id="ekle_kod" class="req" />
				</div>

				<div class="input-container">
					<label for="ekle_plaka">Plaka</label>
					<input type="text" name="plaka" id="ekle_plaka" class="req" />
				</div>

				<div class="input-container">
					<label for="ekle_sahip">Sahip</label>
					<input type="text" name="sahip" id="ekle_sahip" class="req"/>
				</div>

				<div class="input-container">
					<label for="ekle_ogs">OGS</label>
					<input type="text" name="ogs" id="ekle_ogs" class="posnum"/>
				</div>

				<div class="input-container">
					<label for="ekle_tip">Tip</label>
					<input type="text" name="tip" id="ekle_tip" />
				</div>

				<div class="input-container">
					<label for="ekle_aciklama">Açıklama</label>
					<textarea name="aciklama" id="ekle_aciklama" cols="50" rows="5"></textarea>
				</div>

				<div class="input-container">
					<label for="ekle_plaka_aciklama">Plaka Açıklama</label>
					<textarea name="plaka_aciklama" id="ekle_plaka_aciklama" cols="50" rows="5"></textarea>
				</div>

				<div class="input-container submit-center">
					<input type="hidden" name="type" value="ekle" />
					<input type="submit" class="navbtn orange" value="Ekle"/>
				</div>

			</form>

		</div>
	</div>


	<script type="text/javascript">

		


		AHReady(function(){


			var EkleNotf = new FormNotf( $AH('ekle') ),
				PREV_SELECTED_MODEL = 0,
				MODEL_SELECT = $AH('ekle_model');

			add_event( $AH("ekle"), "submit", function(ev){

				var form = this;
				if( FormValidation.check( this ) ){
					// console.log(serialize(this));
					Popup.start_loader();
					AHAJAX_V3.req( Base.AJAX_URL + 'otobus.php', serialize(this), function(res){
						if( res.ok ){
							EkleNotf.init(1, res.text );
							window.scrollTo(0, 0);
							add_select_option( MODEL_SELECT, "", 'Marka seçiniz...', true );
							MODEL_SELECT.disabled = true;
							form.reset();

						} else {
							FormValidation.show_serverside_errors( res.inputret );
						}
						Popup.off();
					});
				}
				event_prevent_default( ev );
			});

			add_event( $AH('ekle_marka'), 'change', function(){
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