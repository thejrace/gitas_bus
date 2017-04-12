<?php

	require 'inc/init.php';

	$SAYFA_DATA = array(
		'title' 	=> 'Filo Plan ',
		'action_id' => Actions::FILO_PLAN_ERISIM
	);

	// izin kontrolu
	if( !in_array( $SAYFA_DATA["action_id"], Active_User::get_perm_actions() ) ) {
		header("Location: ". MAIN_URL );
	}

 		

	// OtobusFiloPlan/B-1744&cbf_durum=T,Y,I,EB&cbf_durum_kodu=RK,CA&cbf_hat=15&tfrom=[TF]&tto=[TT]
	// cbf_[KEY] = val1, val2, val3
	// from ve to varsa aralik veya seçili tarih, yoksa full
	// OtobusFiloPlan/B-1744  => kosulsuz B-1744 ün tüm verilerini alır
	// OtobusFiloPlan/B-1744&tfrom=2016-12	=> 12.2016 verilerini alır
	// OtobusFiloPlan/B-1744&tfrom=2016-11&tto=2016-12  => 11.2016 - 12.2016 arası alır

	

 	// tum veriyi cekerken kapı kodlarını gostermek icin aliyoruz
	$KOMPLE = 0;
	$OID = Input::get('oid');
 	if( $OID == "" || $OID == 'OBAREY' ){
		$KAPI_KODU = 'OBAREY';
		$HEADER = "Tüm Filo Plan";
		$KOMPLE = 1;
	} else {
		$OTOBUS = new Otobus( Input::get('oid') );
		$KAPI_KODU = $OTOBUS->get_details('kod');
		// geçersiz birşey girildiyse anasafaya dön
		if( !isset($KAPI_KODU) ) header("Location: index.php");
		$HEADER = $KAPI_KODU . " " . $SAYFA_DATA['title'];
	}


	// filtre degiskenleri
	$CBFILTER = array();
	foreach( $_GET as $key => $val ){
		$cbs = substr( $key, 0, 4 );
		if( $cbs == 'cbf_' ) $CBFILTER[ substr($key, 4 ) ] = explode( ",", $_GET[$key] );
	}
	// zaman degiskenleri
	$TFILTERFROM = '';
	$TFILTERTO = '';
	$INPUT_VAL_GUNLUK = '';
	$INPUT_VAL_AYLIK  = '';
	$INPUT_VAL_YILLIK = '';

	if( Input::get('tfrom') != "" ){
		$TFILTERFROM = Input::get('tfrom');
		if( strlen($TFILTERFROM) == 10 ){
			$INPUT_VAL_GUNLUK = $TFILTERFROM;
		} else if( strlen($TFILTERFROM) == 7 ){
			$INPUT_VAL_AYLIK = substr($TFILTERFROM, 5, 6);
			$INPUT_VAL_YILLIK = substr($TFILTERFROM, 0, 4 );
		} else if( strlen($TFILTERFROM) == 4 ){
			$INPUT_VAL_AYLIK = 0; // tüm yıl
			$INPUT_VAL_YILLIK = $TFILTERFROM;
		}
		// sitede henuz aralık belirleme yok herseyi hazir sadece siteye GUI yapmak lazım
		if( Input::get('tto') != "" ) $TFILTERTO   = Input::get('tto');	
	}

	
	$TFILTER_AYLAR = array( "-1" => 'Seçiniz', "0" => 'Tüm Yıl', "01" => 'Ocak', "02" => 'Şubat', "03" => 'Mart', "04" => 'Nisan', "05" => 'Mayıs', "06" => 'Haziran', "07" => 'Temmuz', "08" => "Ağustos", "09" => "Eylül", "10" => 'Ekim', "11" => 'Kasım', "12" => "Aralık" );
	$TFILTER_YILLAR = array( "-1" => 'Seçiniz', "2016" => '2016', "2017" => '2017' );

	$NAV_ACTION_BUTTONS = new Nav_Action_Buttons(
		array(
			array(
				"action" 	=> Actions::OTOBUS_SEFER_ISTATISTIK_ERISIM, // filtrelere uygun degiscek dinamik olarak ama filtre sisteminde kontrol mekanizmasi kurmak lazim 
				"url"		=> URL_OTOBUS_SEFER_ISTATISTIKLERI,			// tumunu goruntule de falan onceki secimleri dikkate almadan linki degistremeyiz
				'class'		=> 'orange sefer-stat-link'
			)
		)
	);



	$JQUERYUI = true;
	require 'inc/header.php';



?>
	
	<div class="section-header">
		Filo Plan
	</div>


	<div class="section-content">
		<div class="filo-orer data-table">
			<div class="pagination-container">
				<div class="pagination-center clearfix">
		
					<div class="pagination-col">
						<span>Seferleri Filtrele</span>
						<button type="button" class="obarey-cb selected" key="durum" filter="B">BEKLEYEN</button>
						<button type="button" class="obarey-cb selected" key="durum" filter="A">AKTİF</button>
						<button type="button" class="obarey-cb selected" key="durum"" filter="T">TAMAMLANAN</button>
						<button type="button" class="obarey-cb selected" key="durum" filter="I">İPTAL</button>
						<button type="button" class="obarey-cb selected" key="durum" filter="Y">YARIM KALMIŞ</button>
						<button type="button" class="obarey-cb selected" key="durum" filter="EB">EKSİK BİLGİ</button>
						<!-- <button type="button" class="obarey-cb" filter="ES">EK SEFER</button> -->
					</div>

					<div class="clearfix" style="clear:both; padding:10px 0">

						<div class="pagination-col">
							<button type="button" class="filterbtn kirmizi" id="filter_tumu">TÜMÜNÜ GÖRÜNTÜLE</button>
						</div>
							
						<div class="pagination-col">
							<span>Günlük</span>
							<input type="text" class="pagininput text" value="<?php echo Common::date_reverse($INPUT_VAL_GUNLUK) ?>" id="dt_gunluk" />
						</div>
						<div class="pagination-col">
							<button type="button" class="filterbtn kirmizi" id="filter_uygula_gunluk">UYGULA</button>
						</div>

						
						<div class="pagination-col">
							<span>Ay</span>
							<select id="dt_ay" class="pagininput select">
								<?php
									foreach( $TFILTER_AYLAR as $ay => $ay_adi ) {
										$selected = "";
										if( $INPUT_VAL_AYLIK != '' ){
											if( $INPUT_VAL_AYLIK == $ay ) $selected = 'selected';
										}else if( $INPUT_VAL_YILLIK != '' ){
											// yıllık filtre varsa tüm yılı seçicez
											if( $ay == 0 ) $selected = 'selected';
										}
										echo '<option '.$selected.' value="'.$ay.'">'.$ay_adi.'</option>';
									}
								?>
							</select>
						</div>
						<div class="pagination-col">
							<span>Yıl</span>
							<select id="dt_yil" class="pagininput select">
								<?php
									foreach( $TFILTER_YILLAR as $yil => $yil_text ) {
										$selected = "";
										if( $INPUT_VAL_YILLIK != '' ){
											if( $INPUT_VAL_YILLIK == $yil ) $selected = 'selected';
										} else if( $INPUT_VAL_AYLIK != '' ){
											if( $INPUT_VAL_YILLIK == $yil ) $selected = 'selected';
										}
										echo '<option '.$selected.' value="'.$yil.'">'.$yil_text.'</option>';
									}
								?>
							</select>
						</div>
						<div class="pagination-col">
							<button type="button" class="filterbtn kirmizi" id="filter_uygula_ay_yil">UYGULA</button>
						</div>

					</div>


					<div style="clear:both">
						
						<span>Durum Kodları</span>
						<?php
							$Durum_Kodlari = new Durum_Kodlari;
							foreach( $Durum_Kodlari->get() as $kod => $aciklama ) echo '<button type="button" class="obarey-cb selected" title="'.$aciklama.'" key="durum_kodu" filter="'.$kod.'">'.$kod.'</button>';
						?>
					</div>

				</div>
			</div>


			<div class="pagination-container">
				<div class="pagination-center clearfix">
					
					<!-- <span class="filter-info">07-12-2016 tarihli filo verileri listelendi.</span><br> -->
					<!-- <span class="filter-info">B-1744 kapı kodlu otobüsün tüm filo verilerini listelendi.</span><br> -->
					<span class="filter-info"></span><br>
					Toplam <span class="filter-ks">0</span> Kayıt Bulundu.

				</div>
			</div>

			<div class="dt-nav-container">		
				<?php echo $NAV_ACTION_BUTTONS->get_buttons() ?>
				<button type="button" class="navbtn main-red" id="orer-duzenle">Tabloyu Düzenle</button>
			</div>

			<div class="table-legend">
				<ul>
					<li><span class="legsq tamam"></span>TAMAMLANDI</li>
					<li><span class="legsq aktif"></span>AKTİF</li>
					<li><span class="legsq bekleyen"></span>BEKLEYEN</li>
					<!-- <li><span class="legsq eksefer"></span>EK SEFER</li> -->
					<li><span class="legsq iptal"></span>İPTAL</li>
					<li><span class="legsq yarim"></span>YARIM KALMIŞ</li>
					<li><span class="legsq eksik_bilgi"></span>EKSİK BİLGİ</li>
					
				</ul>
			</div>

			

			<div class="table-container"></div>

				

			</table>

		</div>
	</div>
	

	<script type="text/javascript">

		var MOBILE_CHECK = false,
			KOMPLE = <?php echo $KOMPLE ?>,
			KAPI_KODU = '<?php echo $KAPI_KODU ?>',
			CBFILTER = JSON.parse('<?php echo json_encode( $CBFILTER )?>'),
			TABLE_CONTAINER = $AHC('table-container'),
			TFILTERFROM = '<?php echo $TFILTERFROM ?>',
			TFILTERTO   = '<?php echo $TFILTERTO ?>';
		function depodan_veri_cek( table ){
			Popup.start_loader();
			var data = new FormData();
				
			CBFSTRING = "";
			for( var fkey in CBFILTER ){
				CBFSTRING += fkey+"="+CBFILTER[fkey].join(',')+"&";
			}

			data.append( 'cbf', CBFSTRING );
			data.append( 'tfrom', TFILTERFROM );
			data.append( 'tto', TFILTERTO );
			data.append( 'kapi_kodu', KAPI_KODU );

			AHAJAX_V3.req( Base.AJAX_URL + 'filo_plan.php', data, function(res){
				console.log(res);
				table.data = res.data.filo_data;
				set_html( $AHC('filter-ks'), res.data.ks );
				table.tarih_goster = true;
				table.init();
				Popup.off();
			});

		}

		// GET lerden cbleri seçme fonksiyonu
		function filter_init(){
			var f_elems = find_elem($AHC('filo-orer'), '[filter]');
			for( var x = 0; x < f_elems.length; x++ ){
				if( CBFILTER[f_elems[x].getAttribute('key')] == undefined ){
					addClass(f_elems[x], 'selected');
				} else {
					if( !in_array(f_elems[x].getAttribute('filter'), CBFILTER[f_elems[x].getAttribute('key')])  ){
						removeClass(f_elems[x], 'selected');
					} 
				}
			}
		}


		function cb_filter( table, cb ){
			var filter_key = cb.getAttribute('key'),
				filter = cb.getAttribute('filter');
			if( CBFILTER[filter_key] == undefined ){
				CBFILTER[filter_key] = [ filter ];
			} else {
				if( !hasClass(cb, 'selected' ) ){
					if( !in_array(filter, CBFILTER[filter_key] ) ) CBFILTER[filter_key].push( filter );
				} else {
					remove_from_array( filter, CBFILTER[filter_key] );
				}
			}
			toggle_class( cb, 'selected');
			depodan_veri_cek( table );
		}

		var Filo_Table = function( options ){

			this.data = [];
			this.container = options.container;
			this.old_data = [];
			this.edit = false;
			this.kapi_kodu_yaz = true;
			this.tr_class_list = {T:'tamam', A:'aktif', AZ:'ariza', Y:'yarim', B:'bekleyen', "I":"iptal", "EB":"eksik_bilgi" };
			this.durumlar = [ "T", "A", "B", "I", "EB", "Y" ];
			this.kodlar = [ "", "RK", "YK", "OY", "TO", "AR", "GA", "GG", "CA", "Cİ", "AY", "YS", "KZ", "SH", "CA", "CR" ];
			this.select_init = function( data, selected_val, name ){
				var select_html = '<select name="'+name+'">', selected = "";
				for( var x = 0; x < data.length; x++ ){
					selected = "";
					if( selected_val == data[x] ) selected = "selected";
					select_html += '<option value="'+data[x]+'" '+selected+'>'+data[x]+'</option>';
				}
				select_html += "</select>";
				return select_html;
			},
			this.init = function(){
				var item, html = "", body = "", mob_header, tr_class, harita_link, tarih_td = "", kapi_kod_td = "";
				for( var x = 0; x < this.data.length; x++ ){
					item = this.data[x];
					// toplu durumda otobus kapinolari da ekliyoruz
					kapi_kod_td = "";
					if( this.kapi_kodu_yaz ){
						kapi_kod_td = '<td tdata="Kapı Kodu"><a href="" class="kapi-link">'+item.oto+'</a></td>';
					}
					tr_class = this.tr_class_list[item.durum];
					tarih_td = "";

					if( this.edit  ){
						body +=    '<tr class="'+tr_class+'" >'+
										'<td tdata="Sıra"><input type="text" name="no" class="kisa-input posnum req" id="" value="'+item.no+'" /></td>'+
										'<td tdata="Servis"><input type="text" name="servis" class="kisa-input req" id="" value="'+item.servis+'" /></td>'+
										'<td tdata="Güzergah"><input type="text" name="guzergah" class="orta-input req" id="" value="'+item.guzergah+'" /></td>'+
										'<td tdata="Sürücü"><input type="text" name="surucu" class="orta-input req"  id="" value="'+item.surucu+'" /></td>'+
										'<td tdata="Geliş"><input type="text" name="gelis" class="kisa-input" id="" value="'+item.gelis+'" /></td>'+
										'<td tdata="ORER"><input type="text" name="orer" class="kisa-input" id="" value="'+item.orer+'" /></td>'+
										'<td tdata="Amir"><input type="text" name="amir" class="kisa-input" id="" value="'+item.amir+'" /></td>'+
										'<td tdata="Gidiş"><input type="text" name="gidis" class="kisa-input" id="" value="'+item.gidis+'" /></td>'+
										'<td tdata="Tahmin"><input type="text" name="tahmin" class="kisa-input" id="" value="'+item.tahmin+'" /></td>'+
										'<td tdata="Bitiş"><input type="text" name="bitis" class="kisa-input" id="" value="'+item.bitis+'" /></td>'+
										'<td tdata="Durum">'+this.select_init(this.durumlar, item.durum, 'durum')+'</td>'+
										'<td tdata="Durum Kodu">'+this.select_init(this.kodlar, item.durum_kodu, 'durum_kodu')+'</td>'+
										kapi_kod_td+
										'<td tdata="Tarih"><a href="" class="tarih_link">'+reverse_date(item.tarih)+'</a></td>'+
										'<td><button type="button" class="orer-kaydet filterbtn kirmizi" item-index="'+x+'" item-id="'+item.id+'">Kaydet</button></td></tr>';
					} else {


							harita_link = "";
							mob_header = ' "S('+item.no+') GE('+item.gelis+') ORER('+item.orer+') A('+item.amir+') Gİ('+item.gidis+') BİT('+item.bitis+')" ';
							if( item.durum == 'A' ) harita_link = '<a href="http://ahsaphobby.net/bus/otobus_harita_takip.php?oid=<?php echo Input::get("oid") ?>&orer_id='+item.id+'" title="Haritada Takip Et">TAKİP</a>';
							if( item.durum != 'A' && item.durum != "B" ) duzenle_td = '<td tdata="DÜZENLE"><button type="button" class="orer-duzenle filterbtn kirmizi" item-index="'+x+'" >Düzenle</button></td>';
							body +=    '<tr class="'+tr_class+'" >'+
										'<td tdata="Sıra">'+item.no+'</td>'+
										'<td tdata="Servis">'+item.servis+'</td>'+
										'<td tdata="Hat"><a href="" class="hat-link" key="hat" filter="'+item.hat+'">'+Filo_Senkronizasyon.HATLAR[item.hat].hat+'</a</td>'+
										'<td tdata="Güzergah">'+item.guzergah+'</td>'+
										'<td tdata="Sürücü">'+item.surucu+'</td>'+
										'<td tdata="HTakip">'+harita_link+'</td>'+
										'<td tdata="Geliş">'+item.gelis+'</td>'+
										'<td tdata="ORER"><b>'+item.orer+'</b></td>'+
										'<td tdata="Bekleme">'+sefer_hesapla( item.gelis, item.orer )+' DK</td>'+
										'<td tdata="Amir">'+item.amir+'</td>'+
										'<td tdata="Gidiş">'+item.gidis+'</td>'+
										'<td tdata="Tahmin">'+item.tahmin+'</td>'+
										'<td tdata="Bitiş"><b>'+item.bitis+'</b></td>'+
										'<td tdata="Durum Kodu"><b>'+item.durum_kodu+'</b></td>'+
										'<td tdata="Süre">'+sefer_hesapla( item.gidis, item.bitis )+' DK</td>'+tarih_td+
										kapi_kod_td+
										'<td tdata="Tarih"><a href="" class="tarih_link">'+reverse_date(item.tarih)+'</a></td></tr>';
					}
				}

				if( this.edit ){
					html += '<table class="filo-table"><thead>'+
						'<tr>'+
							'<td>SIRA</td>'+
							'<td>SERVİS</td>'+
							'<td>GÜZERGAH</td>'+
							'<td>SÜRÜCÜ</td>'+
							'<td>GELİŞ</td>'+
							'<td>ORER</td>'+
							'<td>AMİR</td>'+
							'<td>GİDİŞ</td>'+
							'<td>TAHMİN</td>'+
							'<td>BİTİŞ</td>'+
							'<td>DURUM</td>'+
							'<td>DKODU</td>'+
							'<td></td>'+
						'</tr>'+
					'</thead>'+
					'<tbody>'+body+
					'</tbody></table>';
				} else {

					html += '<table class="filo-table"><thead>'+
						'<tr>'+
							'<td>SIRA</td>'+
							'<td>SERVİS</td>'+
							'<td>HAT</td>'+
							'<td>GÜZERGAH</td>'+
							'<td>SÜRÜCÜ</td>'+
							'<td>HTAKİP</td>'+
							'<td>GELİŞ</td>'+
							'<td>ORER</td>'+
							'<td>BEKLEME</td>'+
							'<td>AMİR</td>'+
							'<td>GİDİŞ</td>'+
							'<td>TAHMİN</td>'+
							'<td>BİTİŞ</td>'+
							'<td>DKODU</td>'+
							'<td>SÜRE</td>'+
							'<td></td>'+
						'</tr>'+
					'</thead>'+
					'<tbody>'+body+
					'</tbody></table>';

				}

				set_html( this.container, html );
				// if( MOBILE_CHECK ){
				// 	table_convert_mobile();
				// }
			}
		}

	
		AHReady(function(){

			var FTable = new Filo_Table({
				data: [],
				container: TABLE_CONTAINER
			});
			FTable.init();

			depodan_veri_cek( FTable );	
			filter_init();
			//filter_info_check();

			// 3 filtre listelenen veriyi tarihsel olarak filtreliyor, filtrestatusla işleri yok 
			add_event( $AH('filter_uygula_ay_yil'), 'click', function(){ 
				var ay = $AH('dt_ay').value,
					yil = $AH('dt_yil').value;
				if( ay != -1 && yil != -1 ){
					// tüm yıl
					if( ay == 0 ){
						TFILTERFROM = yil;
					} else {
						TFILTERFROM = yil + "-" + ay;
					}
					$AH('dt_gunluk').value = "";
					window.scrollTo(0, 0);
					depodan_veri_cek( FTable );
				}
			});
			add_event( $AH('filter_uygula_gunluk'), 'click', function(){ 
				if( $AH('dt_gunluk').value != "" ){
					TFILTERFROM = reverse_date( $AH('dt_gunluk').value );
					$AH('dt_ay').value = -1;
					$AH('dt_yil').value = -1;
					window.scrollTo(0, 0);
					depodan_veri_cek( FTable );
				}
			});
			add_event( $AH('filter_tumu'), 'click', function(){
				TFILTERFROM = "";
				$AH('dt_ay').value = -1;
				$AH('dt_yil').value = -1;
				$AH('dt_gunluk').value = "";
				window.scrollTo(0, 0);
				depodan_veri_cek( FTable );
			});


			add_event_on($AHC('table-container'), '.tarih_link', 'click', function(targ, ev){
				TFILTERFROM = reverse_date(targ.innerText);
				// tarihe basildiginda tarihteki tum veriyi alicaz
				KAPI_KODU = 'OBAREY';
				delete CBFILTER['hat'];
				$AH('dt_ay').value = -1;
				$AH('dt_yil').value = -1;
				$AH('dt_gunluk').value = targ.innerText;
				window.scrollTo(0, 0);
				depodan_veri_cek( FTable );
				set_html( $AHC('filter-info'), targ.innerText + " tarihinin filo verileri listelendi.");
				event_prevent_default( ev );
			});


			add_event_on($AHC('table-container'), '.kapi-link', 'click', function(targ, ev){
				KAPI_KODU = targ.innerText;
				window.scrollTo(0, 0);
				depodan_veri_cek( FTable );
				//filter_info_check();
				event_prevent_default( ev );
			});

			add_event_on($AHC('table-container'), '.hat-link', 'click', function(targ, ev){
				KAPI_KODU = 'OBAREY';
				cb_filter( FTable, targ );
				window.scrollTo(0, 0);
				event_prevent_default( ev );
			});

			add_event( $AHC('obarey-cb'), 'click', function(){
				cb_filter( FTable, this );
			});

			add_event( $AH('orer-duzenle'), 'click', function(){
				if( hasClass(this, "edit-aktif") ){
					FTable.edit = false;
					set_html( this, "Tabloyu Düzenle");
				} else {
					FTable.edit = true;
					set_html( this, "Normal Görünüm");
				}
				toggle_class( this, 'edit-aktif');
				FTable.init();
			});

			add_event_on( $AHC('table-container'), '.orer-kaydet', 'click', function(targ,ev){
				var inputs = find_elem( targ.parentNode.parentNode, 'input' ),
					selects = find_elem( targ.parentNode.parentNode, 'select' ),
					TR_DATA = {},
					ELEM_DATA = [],
					ITEM_INDEX = targ.getAttribute('item-index');
				for( var j = 0; j < inputs.length; j++ ) {
					TR_DATA[ inputs[j].getAttribute('name') ] = inputs[j].value;
					ELEM_DATA.push(inputs[j]);
				}
				for( var j = 0; j < selects.length; j++ ){
					TR_DATA[ selects[j].getAttribute('name') ] = selects[j].value;
					ELEM_DATA.push(selects[j]);
				}
				TR_DATA['type'] = "orer_duzenle";
				TR_DATA['orer_id'] = targ.getAttribute("item-id");
				FormValidation.check_input(ELEM_DATA);
				if( !FormValidation.is_valid() ) {
					FormValidation.show_errors();
					FormValidation.keyup(document);
				} else {
					Popup.start_loader();
					AHAJAX_V3.req( Base.AJAX_URL + 'orer_duzenle.php', manual_serialize(TR_DATA), function(res){
						if(res.ok){
							for( var key in TR_DATA ){
								if( FTable.data[ITEM_INDEX][key] != undefined ) FTable.data[ITEM_INDEX][key] = TR_DATA[key];
							}
							FTable.init();
						}
						Popup.off();
					});
				}
			});


			// arsiv datepicker
			$( "#dt_gunluk" ).datepicker({
				dateFormat: "dd-mm-yy",
				autoSize: true,
				changeMonth: true,
				changeYear: true,
				dayNames:[ "Pazar", "Pazartesi", "Salı", "Çarşamba", "Perşembe", "Cuma", "Cumartesi" ],
				dayNamesMin: [ "Pa", "Pzt", "Sa", "Çar", "Per", "Cum", "Cmt" ],
				maxDate: "+0y+0m +0w",
				minDate: "-2y-0m -0w",
				monthNamesShort: [ "Ocak", "Şubat", "Mart", "Nisan", "Mayıs", "Haziran", "Temmuz", "Ağustos", "Eylül", "Ekim", "Kasım", "Aralık" ],
				nextText: "İleri",
				prevText: "Geri"
			});
		});

	</script>

<?php
	require 'inc/footer.php';