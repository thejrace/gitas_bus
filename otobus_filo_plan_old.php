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


	// ...OtobusFiloPlan/  	      => Tüm filo verilerini verir
    // ...OtobusFiloPlan/OBAREY   => Tüm filo verilerini verir
	// ...OtobusFiloPlan/50   	  => ID'si 50 olan otobüsün aynı günü filo verilerini verir
	// ...OtobusFiloPlan/B-1844   => B-1844 kapı nolu otobus aynı gün filo verilerini verir
	// ...OtobusFiloPlan/?hfilter=HATID    => Hattın tüm filo verilerini verir
	// ...OtobusFiloPlan/|OID-KAPINO-OBAREY|?data=aktif_seferler  => örn. hattın tüm aktif seferlerini verir
	// ...OtobusFiloPlan/|OID-KAPINO-OBAREY|?tfilter=gunluk&tarih=2016-12-06&data=iptal_seferler => örn. hattın 06-12-2016 tarihli iptal sefer verisini verir ( eger tarih yoksa aktif günün verisin alır )
 		

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

	// default olarak gunluk veriyi cekiyoruz
	$DEPO_DATA_TYPE = '';
	if( Input::get('data') != "" ) $DEPO_DATA_TYPE = Input::get('data');

	$HFILTER = '';
	if( Input::get('hfilter') != "" ) $HFILTER = Input::get('hfilter');

	// tfilter;
	// aylik, yillik, gunluk, full ( full filo )
	$TFILTER_TARIH = "";
	// input text in value si
	$TFILTER_GUNLUK = "";
	if( Input::get('tfilter') == "" ) {
		$TFILTER = 'full';
	} else {
		// gecerli filtreler yoksa full veriyi aldir
		if( Input::get('tfilter') == 'full' || Input::get('tfilter') == 'gunluk' || Input::get('tfilter') == 'aylik' || Input::get('tfilter') == 'yillik' ){
			$TFILTER = Input::get('tfilter');
		} else {
			$TFILTER = 'full';
		}
		if( Input::get('tarih') != "" ){
			if( $TFILTER == 'gunluk' ){
				if( strlen( Input::get('tarih')) == 10 ){
					$TFILTER_TARIH = Input::get('tarih');
				} else {
					$TFILTER_TARIH = Common::get_current_date();
				}
				$TFILTER_GUNLUK = $TFILTER_TARIH;
			} else if( $TFILTER == 'aylik' ){
				if( strlen( Input::get('tarih')) == 7 ){
					$TFILTER_TARIH = Input::get('tarih');
				} else {
					$TFILTER_TARIH = Common::get_current_monthyear();
				}
			} else if( $TFILTER == 'yillik' ){
				if( strlen( Input::get('tarih')) == 4 ){
					$TFILTER_TARIH = Input::get('tarih');
				} else {
					$TFILTER_TARIH = Common::get_current_year();
				}
			}
		} else {
			// tarih yoksa aynı günün tarihini al
			$TFILTER_TARIH = Common::get_current_date();
		}
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
						<button type="button" class="obarey-cb" type="filter" filter="B">BEKLEYEN</button>
						<button type="button" class="obarey-cb" type="filter" filter="A">AKTİF</button>
						<button type="button" class="obarey-cb" type="filter" filter="T">TAMAMLANAN</button>
						<button type="button" class="obarey-cb" type="filter" filter="I">İPTAL</button>
						<button type="button" class="obarey-cb" type="filter" filter="Y">YARIM KALMIŞ</button>
						<button type="button" class="obarey-cb" type="filter" filter="EB">EKSİK BİLGİ</button>
						<!-- <button type="button" class="obarey-cb" filter="ES">EK SEFER</button> -->
					</div>

					<div class="clearfix" style="clear:both; padding:10px 0">

						<div class="pagination-col">
							<button type="button" class="filterbtn kirmizi" id="filter_tumu">TÜMÜNÜ GÖRÜNTÜLE</button>
						</div>
							
						<div class="pagination-col">
							<span>Günlük</span>
							<input type="text" class="pagininput text" value="<?php echo Common::date_reverse($TFILTER_GUNLUK) ?>" id="dt_gunluk" />
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
										if( $TFILTER == 'aylik' ){
											if( substr($TFILTER_TARIH, 5) == $ay ) $selected = 'selected';
										}else if( $TFILTER == 'yillik' ){
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
										if( $TFILTER == 'yillik' ){
											if( $TFILTER_TARIH == $yil ) $selected = 'selected';
										} else if( $TFILTER == 'aylik' ){
											if( substr( $TFILTER_TARIH, 0, 4 ) == $yil ) $selected = 'selected';
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
							foreach( $Durum_Kodlari->get() as $kod => $aciklama ) echo '<button type="button" class="obarey-cb" title="'.$aciklama.'" type="dfilter" dfilter="'.$kod.'">'.$kod.'</button>';
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
			FILTER_DATA = {},
			DFILTER_DATA = {},
			HFILTER = '<?php echo $HFILTER ?>',
			KOMPLE = <?php echo $KOMPLE ?>,
			DEPO_DATA_TYPE = '<?php echo $DEPO_DATA_TYPE ?>',
			KAPI_KODU = '<?php echo $KAPI_KODU ?>',
			TFILTER_TYPE = '<?php echo $TFILTER ?>',
			TFILTER_TARIH = '<?php echo $TFILTER_TARIH ?>',			
			 /*TABLE = $AHC('filo-table'),
			TBODY = find_elem( TABLE, 'tbody'),
			TBODY_C = [],
			TRS   = [],*/
			TABLE_CONTAINER = $AHC('table-container');
		function depodan_veri_cek( table ){
			//Popup.start_loader();
			/*AHAJAX_V3.req( Base.AJAX_URL + 'filo_plan.php', manual_serialize({dfilter:JSON.stringify(FILTER_DATA), dkfilter: JSON.stringify(DFILTER_DATA), depo_data_type:DEPO_DATA_TYPE, kapi_kodu:KAPI_KODU, tfilter:TFILTER_TYPE, tarih:TFILTER_TARIH, hfilter:HFILTER }), function(res){
				console.log(res);
				table.data = res.data.filo_data;
				//filter_init( 'filter', res.data.filter_data );
				//filter_init( 'dfilter', res.data.dfilter_data );
				set_html( $AHC('filter-ks'), res.data.ks );
				table.tarih_goster = true;
				table.init();
				Popup.off();
			});*/

		}

		// GET lerden cbleri seçme fonksiyonu
		function filter_init( attr, filter_data ){
			if( attr == 'filter' ){
				var f_data = FILTER_DATA;
			} else if( attr == 'dfilter'){
				var f_data = DFILTER_DATA;
			} 
			var cbs = find_elem( $AHC('filo-orer'), '['+attr+']' );
			for( var x = 0; x < cbs.length; x++ ){
				if( filter_data[cbs[x].getAttribute(attr)] == undefined ) continue;
				for( var filter in filter_data ){
					if( cbs[x].getAttribute(attr) == filter && filter_data[filter] ){
						addClass(cbs[x], 'selected');
						f_data[filter] = true;
						break;
					}
				}
			}
		}
		function cb_filter( table, cb ){
			if( cb.getAttribute('dfilter') != undefined ){
				DFILTER_DATA[cb.getAttribute("dfilter")] = !hasClass(cb, 'selected');
			} else if( cb.getAttribute('filter') != undefined){
				FILTER_DATA[cb.getAttribute("filter")] = !hasClass(cb, 'selected');
			}
			toggle_class( cb, 'selected');
			depodan_veri_cek( table );
			// table.init();
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
					if( !FILTER_DATA[item.durum] ) continue;
					if( item.durum_kodu != "" && !DFILTER_DATA[item.durum_kodu] ) continue;
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
										'<td tdata="Hat"><a href="" class="hat-link" hat-id="'+item.hat+'">'+Filo_Senkronizasyon.HATLAR[item.hat].hat+'</a</td>'+
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

		function filter_info_check(){
			if( KAPI_KODU == 'OBAREY' && HFILTER != "" ){
				set_html( $AHC('filter-info'), Filo_Senkronizasyon.HATLAR[HFILTER].hat + " hattının filo verileri listelendi.");
			} else if( KAPI_KODU == 'OBAREY' ){
				set_html( $AHC('filter-info'), "Tüm filo verileri listelendi.");
			} else  if( KAPI_KODU != 'OBAREY' && HFILTER != "" ){
				set_html( $AHC('filter-info'), KAPI_KODU + " kapı kodlu otobüsün " + Filo_Senkronizasyon.HATLAR[HFILTER].hat + " hattındaki filo verileri listelendi.");
			} else {
				set_html( $AHC('filter-info'), KAPI_KODU + " kapı kodlu otobüsün filo verileri listelendi.");
			}
		}


		
		AHReady(function(){

			var FTable = new Filo_Table({
				data: [],
				container: TABLE_CONTAINER
			});
			FTable.init();

			depodan_veri_cek( FTable );			
			filter_info_check();

			// 3 filtre listelenen veriyi tarihsel olarak filtreliyor, filtrestatusla işleri yok 
			add_event( $AH('filter_uygula_ay_yil'), 'click', function(){ 
				var ay = $AH('dt_ay').value,
					yil = $AH('dt_yil').value;
				if( ay != -1 && yil != -1 ){
					// tüm yıl
					if( ay == 0 ){
						TFILTER_TYPE = 'yillik';
						TFILTER_TARIH = yil;
					} else {
						TFILTER_TYPE = 'aylik';
						TFILTER_TARIH = yil + "-" + ay;
					}
					$AH('dt_gunluk').value = "";
					window.scrollTo(0, 0);
					depodan_veri_cek( FTable );
				}
			});
			add_event( $AH('filter_uygula_gunluk'), 'click', function(){ 
				if( $AH('dt_gunluk').value != "" ){
					TFILTER_TYPE = 'gunluk';
					TFILTER_TARIH = reverse_date( $AH('dt_gunluk').value );
					$AH('dt_ay').value = -1;
					$AH('dt_yil').value = -1;
					window.scrollTo(0, 0);
					depodan_veri_cek( FTable );
				}
			});
			add_event( $AH('filter_tumu'), 'click', function(){
				TFILTER_TYPE = 'full';
				TFILTER_TARIH = "";
				$AH('dt_ay').value = -1;
				$AH('dt_yil').value = -1;
				$AH('dt_gunluk').value = "";
				window.scrollTo(0, 0);
				depodan_veri_cek( FTable );
			});


			add_event_on($AHC('table-container'), '.tarih_link', 'click', function(targ, ev){
				TFILTER_TYPE = 'gunluk';
				TFILTER_TARIH = reverse_date(targ.innerText);
				// tarihe basildiginda tarihteki tum veriyi alicaz
				KAPI_KODU = 'OBAREY';
				HFILTER = "";
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
				HFILTER = "";
				window.scrollTo(0, 0);
				depodan_veri_cek( FTable );
				filter_info_check();
				event_prevent_default( ev );
			});

			add_event_on($AHC('table-container'), '.hat-link', 'click', function(targ, ev){
				KAPI_KODU = 'OBAREY';
				HFILTER = targ.getAttribute('hat-id');
				window.scrollTo(0, 0);
				depodan_veri_cek( FTable );
				filter_info_check();
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

			// mobile - desktop tema aksiyonlari
			// rowlari buluyoruz
			/*function yeni_dom_al(){
				TBODY = find_elem( TABLE, 'tbody');
				TBODY_C = get_children( TBODY );
				for( var i = 0; i < TBODY_C.length; i++ ){
					if( hasClass(TBODY_C[i], 'bekleyen') || 
						hasClass(TBODY_C[i], 'tamam') ||
						hasClass(TBODY_C[i], 'aktif') ||
						hasClass(TBODY_C[i], 'amir') ||
						hasClass(TBODY_C[i], 'iptal') ||
						hasClass(TBODY_C[i], 'ariza') 
					) TRS.push( TBODY_C[i]);
				}
			}
			// desktop
			function table_default(){
				yeni_dom_al();
				// headerlari kaldirip, td leri gosteriyorum tekrar
				var headers = find_elem( TBODY, '.row-header');
				for( var y = 0; y < headers.length; y++ ) remove_elem( headers[y]);
				for( var i = 0; i < TRS.length; i++ ){
					var tds = find_elem( TRS[i], 'td');
					for( var x = 0; x < tds.length; x++ ) css( tds[x], { display:'table-cell' });
				}
			}
			// mobile cevir
			function table_convert_mobile(){
				yeni_dom_al();
				for( var i = 0; i < TRS.length; i++ ){
					// tr lerde mob-heade dan ozet olarak baslik aliyorum
					prepend_html( TRS[i], '<div class="row-header">'+TRS[i].getAttribute('mob-header')+'</div>');
					var tds = find_elem( TRS[i], 'td');
					for( var x = 0; x < tds.length; x++ ) hide( tds[x] );
				}
			}
			add_event_on( TABLE, ".row-header", "click", function(targ, ev){
				var tds = find_elem( targ.parentNode, 'td');
				if( !hasClass(targ, 'active') ){
					for( var x = 0; x < tds.length; x++ ) css( tds[x], { display:'block' });
					addClass(targ, 'active');
					window.scrollTo(0, get_coords(targ).top);
				} else {
					for( var x = 0; x < tds.length; x++ ) hide( tds[x] );
					removeClass(targ, 'active');
				}
			});
			// ilk acilis kontrolu
			if(window.innerWidth <= 767){
				table_convert_mobile();
				MOBILE_CHECK = true
			} else{
				MOBILE_CHECK = false;
			}
			// boyut degistirilirse kontrol
			add_event( window, "resize", function(ev){
				if( window.innerWidth <= 767 ){
					if( !MOBILE_CHECK ) table_convert_mobile();
					MOBILE_CHECK = true;
				} else {
					if( MOBILE_CHECK ) table_default();
					MOBILE_CHECK = false;
				}
			});*/

		});

	</script>

<?php
	require 'inc/footer.php';