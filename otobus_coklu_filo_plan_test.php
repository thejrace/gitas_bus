<?php

	require 'inc/init.php';

	$SAYFA_DATA = array(
		'title' 	=> 'Filo Plan',
		'action_id' => Actions::FILO_PLAN_ERISIM
	);

	// izin kontrolu
	if( !in_array( $SAYFA_DATA["action_id"], Active_User::get_perm_actions() ) ) {
		header("Location: index.php");
	}

	$OTOBUSLER = DB::getInstance()->query("SELECT id, kod, plaka, hat FROM " . DBT_OTOBUSLER )->results();
	foreach( $OTOBUSLER as $key => $otobus ){
		$Hat = new Hat($OTOBUSLER[$key]['hat'] );
		$OTOBUSLER[$key]['hat'] = $Hat->get_details('hat');
	} 


	$JSMASONRY = true;
	require 'inc/header.php';
?>
	
	<div class="section-header">
		Filo Plan Gözetim
	</div>

	<div id="filo-takip-uyari-toggle"><img src="<?php echo URL_RES_IMG ?>ico_uyari_toggle.png" title="UYARILAR" alt="UYARILAR"/><span id="alarm-info" class="alarm-animate">0</span></div>

	<div class="uyarilar-section">
		
		<ul id="alarm-liste">
<!-- 			<li class="kirmizi">
				<span class="saat">15:56</span>
				<span class="uyari-kod">
					B-1744
				</span>
				<span class="uyari-icerik">SEFER İPTALİ! ( AR )</span>
			</li>

			<li class="kirmizi">
				<span class="saat">15:56</span>
				<span class="uyari-kod">
					B-1744
				</span>
				<span class="uyari-icerik">SEFER İPTALİ! ( AR )</span>
			</li>
			<li class="sari goruldu">
				<span class="saat">15:52</span>
				<span class="uyari-kod">
					A-1636
				</span>
				<span class="uyari-icerik">BİR SONRAKİ SEFERİNE YETİŞEMEYECEK!</span>
			</li>

			<li class="mavi goruldu">
				<span class="saat">15:53</span>
				<span class="uyari-kod">
					C-1892
				</span>
				<span class="uyari-icerik">CİHAZ ARIZASI!</span>
			</li>
			<li class="sari goruldu">
				<span class="saat">15:52</span>
				<span class="uyari-kod">
					A-1636
				</span>
				<span class="uyari-icerik">BİR SONRAKİ SEFERİNE YETİŞEMEYECEK!</span>
			</li>
			<li class="kirmizi goruldu">
				<span class="saat">15:56</span>
				<span class="uyari-kod">
					B-1744
				</span>
				<span class="uyari-icerik">SEFER İPTALİ! ( AR )</span>
			</li>

			<li class="sari goruldu">
				<span class="saat">15:52</span>
				<span class="uyari-kod">
					A-1636
				</span>
				<span class="uyari-icerik">BİR SONRAKİ SEFERİNE YETİŞEMEYECEK!</span>
			</li>
			<li class="kirmizi goruldu">
				<span class="saat">15:56</span>
				<span class="uyari-kod">
					B-1744
				</span>
				<span class="uyari-icerik">SEFER İPTALİ! ( AR )</span>
			</li>

-->
		</ul>





	</div>

	<div class="section-content">
		<div class="filo-orer data-table">
			<div class="pagination-container">
				<div class="pagination-center clearfix">
					
					<div class="pagination-col">
						<img class="filter-loader" src="http://ahsaphobby.net/granit/res/img/rolling.gif"  />
					</div>

					<div class="pagination-col">
						<button type="button" class="filterbtn kirmizi" id="otobus_sec">OTOBÜS SEÇ</button>
					</div>

					<div class="pagination-col">
						<span>Seferleri Filtrele</span>
						<button type="button" class="obarey-cb selected" filter="B">BEKLEYEN</button>
						<button type="button" class="obarey-cb selected" filter="A">AKTİF</button>
						<button type="button" class="obarey-cb selected" filter="T">TAMAMLANAN</button>
						<button type="button" class="obarey-cb selected" filter="I">İPTAL</button>
						<button type="button" class="obarey-cb selected" filter="Y">YARIM KALMIŞ</button>
						<button type="button" class="obarey-cb selected" filter="ES">EK SEFER</button>
					</div>
				</div>
			</div>


			<div class="table-legend">
				<ul>
					<li><span class="legsq tamam"></span>TAMAMLANDI</li>
					<li><span class="legsq aktif"></span>AKTİF</li>
					<li><span class="legsq bekleyen"></span>BEKLEYEN</li>
					<li><span class="legsq eksefer"></span>EK SEFER</li>
					<li><span class="legsq iptal"></span>İPTAL</li>
					<li><span class="legsq yarim"></span>YARIM KALMIŞ</li>
					<li><span class="legsq eksik_bilgi"></span>EKSİK BİLGİ</li>
				</ul>
			</div>

			
		</div>
	</div>

	<script type="text/javascript">


		var OTOBUS_DATA = <?php echo json_encode($OTOBUSLER) ?>,
			SECILEN_OTOBUSLER = [],
			SENKRONIZASYON_OTOBUS_DATA = {},
			TABLOLAR = {},
			FILTER_DATA = { "Y": true, "B":true, "A":true, "T":true, "I":true },
			DURAK_IZDUSUM_FREKANS = 30000,
			DURAK_IZDUSUM_INTERVAL = false,
			CFT_REFRESH_INTERVAL = false;

		function makeid(){
		    var text = "";
		    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
		    for( var i=0; i < 5; i++ )
		        text += possible.charAt(Math.floor(Math.random() * possible.length));

		    return text;
		}

		console.log( sefer_hesapla("00:15", "01:45"));


		var ALARMLAR = {
			GORULMEMISLER: 0,
			TIPLER: {
				SEFER_IPTAL: 0,
				SEFER_YARIM: 1,
				CIHAZ_ARIZA: 2,
				GEC_KALMA: 3
			},
			UYARILAR:[
				{ text:"SEFER İPTALİ!", "class":"kirmizi" },
				{ text:"SEFER YARIM KALDI!", "class":"kirmizi" },
				{ text:"CİHAZ ARIZASI!", "class":"mavi" },
				{ text:"BİR SONRAKİ SEFERİNE YETİŞEMEYEBİLİR!", "class":"mavi" },
				{ text:"SEFERİNE VAKTİNDE BAŞLAMADI", "class":"mavi" }
			],
			DATA: [],
			EKLE: function( alarm_obj ){
				var current_date = new Date();
				alarm_obj.data_id = makeid();
				alarm_obj.saat = current_date.getHours()+':'+current_date.getMinutes();
				var html = '<li class="'+ALARMLAR.UYARILAR[alarm_obj.tip].class+' '+alarm_obj.data_id+'">'+
								'<span class="saat">'+alarm_obj.saat+'</span>'+
								'<span class="uyari-kod">'+
									alarm_obj.otobus+
								'</span>'+
								'<span class="uyari-icerik">'+ALARMLAR.UYARILAR[alarm_obj.tip].text+" "+alarm_obj.ext_data+'</span>'+
							'</li>';
				prepend_html( $AH('alarm-liste'), html );
				ALARMLAR.GORULMEMISLER++;
				set_html( $AH('alarm-info'), ALARMLAR.GORULMEMISLER );
				// Popup.on( '<ul class="popup-uyari-section">'+html+'</ul>', "UYARI" );

			},
			GORULDU_ISARETLE: function( kod, sefer_no, tip ){

			},
			TUMUNU_GORULDU_ISARETLE: function(){

			}
			
		}

		

		function durak_izdusum_refresh(){
			if( Object.size(TABLOLAR) > 0 ){
				for( var kapi in TABLOLAR ){
					TABLOLAR[kapi].takip_guncelle();
				} 
			}
		}	

		function filo_coklu_refresh(){
			if( Object.size(TABLOLAR) <= 0 ) return;
			
			AHAJAX_V3.req( Base.AJAX_URL + 'coklu_filo_plan.php', manual_serialize({type:'coklu_veri_al', kodlar:JSON.stringify(SECILEN_OTOBUSLER) }), function(res){
				set_html( $AH('coklu-tables'), "" );
				console.log(TABLOLAR[kapi]);
				for( var kapi in TABLOLAR ){
					TABLOLAR[kapi].reset();
					// TABLOLAR[kapi].set_JSON_data( res.data[kapi] );
					TABLOLAR[kapi].set_data(res.data[kapi].table_data);
					TABLOLAR[kapi].set_mesajlar(res.data[kapi].mesajlar);
					TABLOLAR[kapi].set_info(res.data[kapi].info);
					TABLOLAR[kapi].init();
				} 
				// masonry init
				var msnry = new Masonry( '#coklu-tables');
			});

		}

		var Filo_Table = function( options ){
			this.data = sort_by_key(options.data, 'no', 'numeric' );
			this.mesajlar = [];
			this.info = {};
			this.container = options.container;
			this.old_data = [];
			this.hidden = false;
			this.table_guncelle = false;
			this.kapi_kodu = "";
			this.hat_link = "";
			this.ilk_init = true;
			this.guzergah_yon = "";
			this.aktif_sefer_var = false;
			this.hat_no = "";
			this.durak_data = "";
			this.bitis_data = "";
			this.sefer_istatistikleri = { TOPLAM: 0, T:0, A:0, I:0, ES:0, B:0 };
			this.tr_class_list = {T:'tamam', A:'aktif', Y:'yarim', B:'bekleyen', "ES":'Ek Sefer', "I":"iptal" };
			this.reset = function(){
				for( var ist in this.sefer_istatistikleri ) this.sefer_istatistikleri[ist] = 0;
			},
			this.takip_guncelle = function(){
				if( !this.hidden && this.aktif_sefer_var ){
					var this_ref = this;
					Filo_Senkronizasyon.ORER_Sefer_Takip( this.kapi_kodu, function( d, s){
						this_ref.durak_data = d;
						this_ref.bitis_data = s;

						set_html( find_elem( $AHC(this_ref.kapi_kodu), '.saat-data' ), s );
						set_html( find_elem( $AHC(this_ref.kapi_kodu), '.durak-data' ), d );
					});
					
				}
			},
			this.set_info = function( data ){
				this.info = data;
			},
			this.set_mesajlar = function( data ){
				this.mesajlar = data;
			},
			this.set_data = function( data ){
				this.old_data = this.data;
				this.data = sort_by_key( data, 'no', 'numeric');
				this.ilk_init = false;
				this.aktif_sefer_var = false;
			},
			this.set_JSON_data = function( data ){
				// for( var x = 0; x < data.length; x++ ){
				// 	data[x] = JSON.parse( data[x] );
				// }
				this.data = sort_by_key( data, 'no', 'numeric');
				this.ilk_init = false;
				this.aktif_sefer_var = false;
			},
			this.setup_table = function(){
				var body = "", hat_init = "", hat_id = "", bekleme_suresi = 0, sefer_suresi = 0, gecikme_class = "";
				for( var x = 0; x < this.data.length; x++ ){
					item = this.data[x];
					hat_init = "";
					gecikme_class = "";
					this.sefer_istatistikleri[item.durum]++;
					this.sefer_istatistikleri['TOPLAM']++;
					if( in_object( item.durum, FILTER_DATA ) || in_object( item.durum_kodu, FILTER_DATA ) ){

						// hat duzenlemesi
						//if( this.ilk_init ){
							// db den aliyoruz direk hat id geliyor
						this.hat_link = '<span>'+Filo_Senkronizasyon.HATLAR[item.hat].hat+'</span>';
						this.guzergah_yon = item.guzergah.substr( Filo_Senkronizasyon.HATLAR[item.hat].hat.length + 1, 1 );

						/*} else {
							for( var key in Filo_Senkronizasyon.HATLAR ){
								if( item.hat == Filo_Senkronizasyon.HATLAR[key].hat ) {
									hat_id = key;
									break;
								}
 							}
							this.hat_link = '<a href="#" hat-id="'+hat_id+'">'+item.hat+'</a>';
						}*/

						tr_class = this.tr_class_list[item.durum]
						if( item.durum == 'A' ) {
							harita_link = '<a href="" title="Haritada Takip Et">TAKİP</a>';
							if( !this.aktif_sefer_var ) this.aktif_sefer_var = true;

							if( this.data[x+1] != undefined && sefer_hesapla(this.data[x+1].orer, item.tahmin) > 0 ){

								gecikme_class = " gecikme-bg";
							}	
						}


						sefer_suresi   = sefer_hesapla(item.gidis, item.bitis );
						bekleme_suresi = sefer_hesapla( item.gelis, item.orer );

						if( Filo_Senkronizasyon.SOFORLER[item.surucu] != undefined  ){
							var surucu_td = '<td tdata="Sürücü" style="cursor:pointer;" onmouseover="Obarey_Tooltip(\'text\', \' İsim: <b>'+Filo_Senkronizasyon.SOFORLER[item.surucu].isim+'</b> <br> Telefon: <b>'+Filo_Senkronizasyon.SOFORLER[item.surucu].telefon+'</b>\', this, event);" >'+Filo_Senkronizasyon.SOFORLER[item.surucu].syn+'</td>';
							
						} else {
							var surucu_td =  '<td>YOK</td>';
							
						}

						body += '<tr class="'+tr_class+'" >'+
								'<td tdata="Sıra">'+item.no+'</td>'+
								//'<td tdata="Servis">'+item.servis+'</td>'+
								//'<td tdata="Hat">'+hat_init+'</td>'+
								'<td tdata="Güzergah">'+this.guzergah_yon+'</td>'+
								surucu_td+
								'<td tdata="Geliş">'+item.gelis+'</td>'+
								'<td tdata="ORER"><b>'+item.orer+'</b></td>'+
								'<td tdata="Bekleme">'+bekleme_suresi+' DK</td>'+
								'<td tdata="Amir">'+item.amir+'</td>'+
								'<td tdata="Gidiş">'+item.gidis+'</td>'+
								'<td tdata="Tahmin">'+item.tahmin+'</td>'+
								'<td tdata="Bitiş" class="'+gecikme_class+'"><b>'+item.bitis+'</b></td>'+
								'<td tdata="Durum Kodu"><b>'+item.durum_kodu+'</b></td>'+
								'<td tdata="Süre">'+sefer_suresi+' DK</td></tr>';
					}
				}
				
				return body;
			},

			this.init = function(){
				
				this.hidden = false;
				var item, html = "", body = this.setup_table(), tr_class, harita_link;
				if( body == "" ) {
					this.hidden = true;
					return;
				}

				var elem = document.createElement( 'DIV' );
				elem.className = 'filo-table-container ' + this.kapi_kodu;

				if( this.durak_data == "" ) this.durak_data = "Veri yok.";
				if( this.bitis_data == "" ) this.bitis_data = "Veri yok.";

				html = 
								'<div class="table-header">'+
									'<button type="button" class="tablo-kapat">KAPAT</button>'+
									'<div class="otobus-data-header">'+this.kapi_kodu+' ( Hat: ' +this.hat_link+' )</div>'+
									'<div class="otobus-data-stats"><span class="seferstat toplam">[ Toplam Sefer: '+this.sefer_istatistikleri.TOPLAM+' ]</span> <span class="seferstat tamamlanan">[ Tamamlanan: '+this.sefer_istatistikleri.T+' ]</span> <span class="seferstat aktif">[ Aktif: '+this.sefer_istatistikleri.A+' ]</span> <span class="seferstat eksefer">[ Ek Sefer: '+this.sefer_istatistikleri.ES+' ]</span> <span class="seferstat iptal">[ İptal: '+this.sefer_istatistikleri.I+' ]</span></div>'+
								'</div>'+
								'<div class="table-height-cont"><table class="filo-table">'+
								'<thead>'+
									'<tr>'+
										'<td>SIRA</td>'+
										//'<td>SERVİS</td>'+
										//'<td>HAT</td>'+
										'<td>YÖN</td>'+
										'<td>SÜRÜCÜ</td>'+
										'<td>GELİŞ</td>'+
										'<td>ORER</td>'+
										'<td>BEKLEME</td>'+
										'<td>AMİR</td>'+
										'<td>GİDİŞ</td>'+
										'<td>TAHMİN</td>'+
										'<td>BİTİŞ</td>'+
										'<td>DKODU</td>'+
										'<td>SÜRE</td>'+
									'</tr>'+
								'</thead>'+
								'<tbody>'+ body + '</tbody></table></div>'+
					'<div class="table-details">'+
						'<div class="filo-table-nav">'+
							'<button type="button" class="filo-nav-ico durak" onmouseover="Obarey_Tooltip(\'text\', \' '+this.durak_data+' \', this, event);" data="'+this.kapi_kodu+'"></button>'+
							'<button type="button" class="filo-nav-ico alarm" alt="Alarmları Görüntüle" title="Alarmları Görüntüle" data="'+this.kapi_kodu+'"></button>'+
							'<button type="button" class="filo-nav-ico mesaj" alt="Filo Mesajlarını Görüntüle" title="Filo Mesajlarını Görüntüle" data="'+this.kapi_kodu+'"></button>'+
							'<button type="button" class="filo-nav-ico info" onmouseover="Obarey_Tooltip(\'text\', \' '+this.tooltip_info()+' \', this, event);" data="'+this.kapi_kodu+'"></button>'+
							'<button type="button" class="filo-nav-ico stats" alt="Sefer İstatistikleri" title="Sefer İstatistikleri" data="'+this.kapi_kodu+'"></button>'+
						'</div>'+
					'</div>';
				set_html( elem, html );
				// append_html( $AH('coklu-tables'), html );
				$AH('coklu-tables').appendChild( elem );
			},
			this.popup_mesaj_data = function(){
				var html = '<div class="popup-filo-mesaj"><ul>';
				for( var x = 0; x < this.mesajlar.length; x++ ){
					html += '<li class="clearfix">'+
								'<div class="mesaj-header">'+
									'<span class="mesaj-kaynak">'+this.mesajlar[x].kaynak+'</span>'+
									'<span class="mesaj-saat"> - '+this.mesajlar[x].saat+'</span>'+
								'</div>'+
								'<div class="mesaj-icerik">'+
									this.mesajlar[x].mesaj+
								'</div>'+
							'</li>';
				}
				html += '</ul></div>';
				Popup.on( html, this.kapi_kodu + " Filo Mesajları");
			},
			this.tooltip_info = function(){
				var html = 	'<center><span><b>Otobüs</b></span></center><div><center>Sefer Yüzdesi: '+this.info.otobus.sefer_yuzdesi+'</center></div><div><center>Toplam KM: '+this.info.otobus.toplam_km+'</center></div><center><span><b>Hat</b></span></center><div><center>Uzunluk: '+this.info.hat.uzunluk+'</center></div><center><span><b>Sürücü</b></span></center><div><center>Sefer Yüzdesi: Veri Yok</center></div><div><center>Planlanan Seferleri: Veri Yok</center></div><div><center>İptal Seferleri: Veri Yok</center></div><div><center>Yarım Seferleri: Veri Yok</center></div>';

				return html;
			}


		}
		AHReady(function(){

			ALARMLAR.EKLE( { otobus:"B-1744", saat: "15:40", sefer_no: 4, tip:ALARMLAR.TIPLER.SEFER_IPTAL, ext_data: "( AR )", "goruldu":0 } );


			add_event_on( $AH('coklu-tables'), ".filo-nav-ico", "click", function(targ,ev){

				if( hasClass( targ, 'alarm') ){
					Popup.on("", targ.getAttribute("data") + " Alarmlar");
				} else if( hasClass( targ, 'stats') ){
					console.log('stats link');
				} else if( hasClass( targ, 'mesaj') ){
					TABLOLAR[targ.getAttribute("data")].popup_mesaj_data();
				}

			});

			add_event( document, "scroll", function(ev){


				if( document.body.scrollTop <= 300 ){
					hide( $AH('filo-takip-uyari-toggle'));
				} else {
					show( $AH('filo-takip-uyari-toggle'));	
				}

			});

			add_event( $AH('filo-takip-uyari-toggle'), 'click', function(ev){

				window.scrollTo(0, 0);

			});

			add_event( $AH('otobus_sec'), 'click', function(){
				var html = '<div class="otobus-sec-container"><div style="margin-bottom:30px; text-align:center"><button type="button" class="navbtn orange bolge-sec" data="A">A Bölgesi</button><button type="button" class="navbtn orange bolge-sec" data="B">B Bölgesi</button><button type="button" class="navbtn orange bolge-sec" data="C">C Bölgesi</button><button type="button" class="navbtn main-red bolge-sec" data="temizle" >SEÇİMLERİ TEMİZLE</button></div>', selected = "";
				for( var x = 0; x < OTOBUS_DATA.length; x++ ){
					selected = "";
					if( in_array( OTOBUS_DATA[x].kod, SECILEN_OTOBUSLER ) ) selected = 'selected';
					html += '<button type="button" data="'+OTOBUS_DATA[x].kod+'" class="obarey-cb '+selected+'"><b>'+OTOBUS_DATA[x].kod+'</b> - '+OTOBUS_DATA[x].hat+' - '+OTOBUS_DATA[x].plaka+'</button>';
				}
				html += '<div style="margin-top:30px; text-align:center"><button type="button" class="navbtn orange" id="otobus_sec_action">TAMAM</button><button type="button" class="navbtn main-red" onclick="Popup.off()">İPTAL</button></div></div>';
				Popup.on( html, 'Otobüs Seç' );
			});

			add_event_on($AH('popup'), '#otobus_sec_action', 'click', function(targ,ev){

				SECILEN_OTOBUSLER = [];
				TABLOLAR = {};
				clearInterval( DURAK_IZDUSUM_INTERVAL );
				// sayfayı temizle
				set_html( $AH('coklu-tables'), "" );
				var selected = find_elem( $AHC('otobus-sec-container'), '.selected' );
				// secilenleri listele
				if( selected.length > 1 ){
					for( var x = 0; x < selected.length; x++ ) SECILEN_OTOBUSLER.push( selected[x].getAttribute("data") );
				} else {
					SECILEN_OTOBUSLER.push( selected.getAttribute("data") );
				}
				Popup.start_loader();
				AHAJAX_V3.req( Base.AJAX_URL + 'coklu_filo_plan.php', manual_serialize({type:'coklu_veri_al', kodlar:JSON.stringify(SECILEN_OTOBUSLER) }), function(res){
					console.log(res.data);
					for( var kapi_kod in res.data){
						TABLOLAR[kapi_kod] = new Filo_Table({data:res.data[kapi_kod].table_data});
						TABLOLAR[kapi_kod].kapi_kodu = kapi_kod;
						TABLOLAR[kapi_kod].set_mesajlar( res.data[kapi_kod].mesajlar );
						TABLOLAR[kapi_kod].set_info(res.data[kapi_kod].info);
						TABLOLAR[kapi_kod].init();
					}
					// CFT_REFRESH_INTERVAL = setInterval( filo_coklu_refresh, 20000 );
					// DURAK_IZDUSUM_INTERVAL = setInterval( durak_izdusum_refresh, DURAK_IZDUSUM_FREKANS );
					// masonry init
					var msnry = new Masonry( '#coklu-tables');
					Popup.off();
				});
			});

			// otobus secin cb leri
			add_event_on( $AH('popup'), '.obarey-cb', 'click', function(targ,ev){
				toggle_class(targ, 'selected');
			});

			add_event_on( $AH('popup'), '.bolge-sec', 'click', function(targ, ev){

				var cbs = find_elem( $AHC('otobus-sec-container'), '.obarey-cb' ), data = targ.getAttribute('data');
				if( data == 'temizle' ){
					for( var x = 0; x < cbs.length; x++ ) removeClass(cbs[x], 'selected');
				} else {
					for( var x = 0; x < cbs.length; x++ ){
						if( data == cbs[x].getAttribute('data').substr(0, 1) ){
							addClass( cbs[x], 'selected');
						}
					}
				}
			});

			add_event_on( $AH('coklu-tables'), '.tablo-kapat', 'click', function(targ,ev){
				delete TABLOLAR[targ.parentNode.parentNode.className.substr(21)];
				remove_elem( targ.parentNode.parentNode);
				var msnry = new Masonry( '#coklu-tables');
			});

			add_event( $AHC('obarey-cb'), 'click', function(){
				if( hasClass(this, 'selected') ){
					delete FILTER_DATA[this.getAttribute("filter")];
				} else {
					FILTER_DATA[this.getAttribute("filter")] = true;
				}
				toggle_class( this, 'selected');
				set_html( $AH('coklu-tables'), "");
				for( var key in TABLOLAR ) {
					TABLOLAR[key].reset();
					TABLOLAR[key].init();
				}
				var msnry = new Masonry( '#coklu-tables');	
			});

		});

	</script>

<?php
	require 'inc/footer.php';