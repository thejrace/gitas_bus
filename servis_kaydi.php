<?php

    require 'inc/init.php';

    $SAYFA_DATA = array(
		'title' 	=> 'Servis Kaydı',
		'action_id' => Actions::OTOBUS_PARCA_VERI_GIRME
	);

	// izin kontrolu
	if( !in_array( $SAYFA_DATA["action_id"], Active_User::get_perm_actions() ) ) {
		header("Location: index.php");
	}


    require 'inc/header.php';

?>


<div class="section">
		<div class="section-header">
			<?php echo $SAYFA_DATA["title"] ?>
		</div>


		<div class="section-content servis-kayit-form">
			
			<ul class="servis-kayit-tabs">

				<li>
					<button type="button" class="servis-tab">OTOBÜS / MÜŞTERİ BİLGİ</button>
					<div class="servis-tab-content">

						<div class="row otobus-sec">
							<div class="input-container">
								<input type="text" placeholder="Otobüs Kapı No Girin" id="otobus_kapi_no"/>
								<button type="button" class="filterbtn kirmizi" id="btn_otobus_kapi_no_init">SEÇ</button>
							</div>
							<div class="otobus-error-notf"></div>
						</div>

						<div class="row">
							<div class="col wp_50">
								<div class="col-header">Otobüs Bilgileri</div>
								<div class="col-content">
									
									<ul class="man-table otobus-bilgileri">
									</ul>
								</div>
							</div>

							<div class="col wp_50">
								<div class="col-header">Müşteri Bilgileri</div>
								<div class="col-content">
									<ul class="man-table musteri-bilgileri">
									</ul>
								</div>
							</div>

						</div>
					</div>
				</li>

				<li>
					<button type="button" class="servis-tab">ARIZA DETAYLARI</button>
					<div class="servis-tab-content">
						
						<div class="row">
							<div class="col wp_100">
								<div class="col-content">
									<div class="input-container form-full">
										<label>ARIZANIN TANIMI</label>
										<textarea id="ariza_tanimi" rows="7"></textarea>
									</div>
								</div>
							</div>

							<div class="col wp_100">
								<div class="col-content">
									<div class="input-container form-full">
										<label>ARIZANIN TESPİTİ</label>
										<textarea id="ariza_tespiti" rows="7"></textarea>
									</div>
								</div>
							</div>

							<div class="col wp_100">
								<div class="col-content">
									<div class="input-container form-full">
										<label>ARIZANIN NEDENİ</label>
										<textarea id="ariza_nedeni" rows="7"></textarea>
									</div>
								</div>
							</div>

							<div class="col wp_100">
								<div class="col-content">
									<div class="input-container form-full">
										<label>YAPILAN ONARIM / İYİLEŞTİRME ÖNERİSİ</label>
										<textarea id="ariza_onarim" rows="7"></textarea>
									</div>
								</div>
							</div>

						</div>

					</div>
				</li>

				<li>
					<button type="button" class="servis-tab">STOK / PARÇA KULLANIM</button>
					<div class="servis-tab-content">
						<div class="row">
							<div class="parca-button"><button type="button" class="filterbtn kirmizi" id="stok_parca_sec">+ PARÇA EKLE</button></div>
							<table class="parca-listesi">
								<thead>
									<tr>
										<td>STOK KODU</td>
										<td>MALZEME</td>
										<td>ADET</td>
										<td></td>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>#XXXXXX</td>
										<td>Fren Balatası</td>
										<td>x 3</td>
										<td><button type="button" class="stok_parca_cikar filterbtn kirmizi" item-index="10" item-id="5">SİL</button></td>
									</tr>
									<tr>
										<td>#XXXXXX</td>
										<td>Fren Balatası</td>
										<td>x 3</td>
										<td><button type="button" class="stok_parca_cikar filterbtn kirmizi" item-index="10" item-id="5">SİL</button></td>
									</tr>
									<tr>
										<td>#XXXXXX</td>
										<td>Fren Balatası</td>
										<td>x 3</td>
										<td><button type="button" class="stok_parca_cikar filterbtn kirmizi" item-index="10" item-id="5">SİL</button></td>
									</tr>
								</tbody>
							</table>
							
						</div>
					</div>
				</li>

				<li>
					<button type="button" class="servis-tab">REVİZE / HURDA PARÇALAR</button>
					<div class="servis-tab-content">
						<div class="row">
							<div class="parca-button"><button type="button" class="filterbtn kirmizi" id="cikma_parca_ekle">+ ÇIKMA PARÇA EKLE</button></div>
							<table class="parca-listesi">
								<thead>
									<tr>
										<td>MALZEME TİPİ</td>
										<td>MALZEME</td>
										<td>AÇIKLAMA</td>
										<td>ADET</td>
										<td>DURUM</td>
										<td></td>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>Fren Balatası</td>
										<td>DX92912</td>
										<td>Taşlanacak</td>
										<td>x 2</td>
										<td>Revize</td>
										<td><button type="button" class="cikma_parca_sil filterbtn kirmizi" item-index="10" item-id="5">SİL</button></td>
									</tr>
									<tr>
										<td>Fren Balatası</td>
										<td>DX92912</td>
										<td>Taşlanacak</td>
										<td>x 2</td>
										<td>Revize</td>
										<td><button type="button"  class="cikma_parca_sil filterbtn kirmizi" item-index="10" item-id="5">SİL</button></td>
									</tr>
									<tr>
										<td>Fren Balatası</td>
										<td>DX92912</td>
										<td>Taşlanacak</td>
										<td>x 2</td>
										<td>Hurda</td>
										<td><button type="button"  class="cikma_parca_sil filterbtn kirmizi" item-index="10" item-id="5">SİL</button></td>
									</tr>
								</tbody>
							</table>
							
						</div>
					</div>
				</li>

				<li>
					<button type="button" class="servis-tab">FOTOĞRAF EKLE</button>
					<div class="servis-tab-content">
						<div class="row">
							
							<div class="form-foto-yukle">
								
								<ul>
									<li>
										<div class="input-container">
											<input type="file" id="f1_foto" />
										</div>
									</li>

									<li>
										<div class="input-container">
											<input type="file" id="f2_foto" />
										</div>
									</li>

									<li>
										<div class="input-container">
											<input type="file" id="f3_foto" />
										</div>
									</li>

									<li>
										<div class="input-container">
											<input type="file" id="f4_foto" />
										</div>
									</li>

									<li>
										<div class="input-container">
											<input type="file" id="f5_foto" />
										</div>
									</li>
								</ul>

							</div>

						</div>
					</div>
				</li>

			</ul>
	
			<div class="finalize">
				<button type="button" class="navbtn orange" id="finalize">KAYDET</button>
			</div>

		</div>
	</div>

	<script type="text/javascript">



		var Servis_Kayit = function( options ){

			this.data = {};
			this.otobus_data_init = function(){



			}

			this.olustur = function(){

			}

		};


		var INPUTS = {},
			BUTTONS = {},
			ERROR_CONTS = {},
			OTOBUS_DATA_KEYS = { plaka:'PLAKA', marka:'MARKA', tip:'TİP', model:'MODEL', motor_no:'MOTOR NO', sasi_no:'ŞASİ NO', arac_no:'ARAÇ NO', km:'KM' },
			MUSTERI_DATA_KEYS = { isim:'İSİM', adres:'ADRES', vergi_data:'VERGİ DAİRESİ / NO', telefonlar:'TELEFONLAR', fax_gsm:'FAX / GSM', parca_tutari:'TAHSİS İŞÇ / PARÇA TUTARI' },
			RES_DATA_KEYS = {
				OTOBUS: 'otobus_data',
				MUSTERI: 'musteri_data'
			},
			AJAX_URL = 'servis_kaydi.php';

		function man_table_add_tr( key, val ){
			if( val == "" ) val = 'Belirtilmemiş';
			return '<li><div class="sol-header">'+key+'</div><div class="sag-body">'+val+'</div></li>';
		}

		AHReady(function(){

			var Servis_Form = new Servis_Kayit({});


			INPUTS = {
				OTOBUS_KAPI_NO:$AH('otobus_kapi_no'),
				ARIZA_TANIMI:$AH('ariza_tanimi'),
				ARIZA_TESPITI:$AH('ariza_tespiti'),
				ARIZA_NEDENI:$AH('ariza_nedeni'),
				ARIZA_ONARIM:$AH('ariza_onarim')
			};
			BUTTONS = {
				OTOBUS_SEC:$AH('btn_otobus_kapi_no_init'),
				STOK_PARCA_SEC:$AH('stok_parca_sec'),
				STOK_PARCA_SIL:$AHC('stok_parca_cikar'),
				CIKAN_PARCA_EKLE:$AH('cikma_parca_ekle'),
				CIKAN_PARCA_SIL:$AHC('cikma_parca_sil'),
				KAYDET:$AH('finalize')
			};
			ERROR_CONTS = {
				OTOBUS:$AHC('otobus-error-notf')
			};


			add_event( BUTTONS.OTOBUS_SEC, 'click', function(){
				set_html( ERROR_CONTS.OTOBUS, "" );
				kontrol = FormValidation.custom_check( INPUTS.OTOBUS_KAPI_NO, "Boş bırakılamaz.", function(val){
					return FormValidation.req( val );
				});
				if( !kontrol ) return;
				Popup.start_loader();
				AHAJAX_V3.req( Base.AJAX_URL + AJAX_URL, manual_serialize({ type:'otobus_sec', kapi_no:INPUTS.OTOBUS_KAPI_NO.value.toUpperCase()}), function(res){
					//console.log( res.data );
					if( res.ok ){
						var otobus_html = "",
							musteri_html = "";
						for( var key in OTOBUS_DATA_KEYS ) otobus_html  += man_table_add_tr(OTOBUS_DATA_KEYS[key], res.data[RES_DATA_KEYS.OTOBUS][key] );
						for( var key in MUSTERI_DATA_KEYS ) musteri_html += man_table_add_tr(MUSTERI_DATA_KEYS[key], res.data[RES_DATA_KEYS.MUSTERI][key] );
						set_html( $AHC('otobus-bilgileri'), otobus_html );
						set_html( $AHC('musteri-bilgileri'), musteri_html );
						Servis_Form.data['musteri'] = res.data[RES_DATA_KEYS.MUSTERI]['id'];
						Servis_Form.data['otobus']  = res.data[RES_DATA_KEYS.OTOBUS]['id'];
					} else {
						set_html( ERROR_CONTS.OTOBUS, res.text );
					}
					Popup.off();
				});
			});

			

			// tablar
			add_event( $AHC('servis-tab'), "click", function(){
				toggle_class( find_elem(this.parentNode, ".servis-tab-content"), "active" );
			});

		});


	</script>


<?php
    require 'inc/footer.php';