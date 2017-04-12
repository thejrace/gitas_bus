<?php

	require 'inc/init.php';

	$SAYFA_DATA = array(
		'title' 	=> 'Sefer İstatistikleri',
		'action_id' => Actions::OTOBUS_SEFER_ISTATISTIK_ERISIM
	);

	// izin kontrolu
	if( !in_array( $SAYFA_DATA["action_id"], Active_User::get_perm_actions() ) ) {
		header("Location: index.php");
	}


	$KAPI_KODU = Input::get("kapi_kodu"); 

	// ...OtobusSeferIstatistikleri/  					=> Baştan sona tüm filo istatistiklerini alır ( OBAREY kapı no oluyor )
	// ...OtobusSeferIstatistikleri/OBAREY  			=> Baştan sona tüm filo istatistiklerini alır ( OBAREY kapı no oluyor )
	// ...OtobusSeferIstatistikleri/B-1744  			=> Otobüslerin kapınolarına göre istatistiklerini alır
	// ...OtobusSeferIstatistikleri/B-1744?cbf_hat=14   => B-1744 otobusun 14 ID li hattındanki istatistiklerini alır
	// ...OtobusSeferIstatistikleri/?cbf_hat=14  	    => 14 ID li hattın istatistiklerini alır

	if( $KAPI_KODU == "" || $KAPI_KODU == 'OBAREY'){
		$KAPI_KODU = 'OBAREY';
		if( Input::get('cbf_hat') != "" ){
			// hat
			$Hat = new Hat( Input::get('cbf_hat') );
			$HEADER = '"'.$Hat->get_details('hat') . '" Hattının Sefer İstatistikleri';
		} else {
			// tüm filo
			$HEADER = 'Filo İstatistikleri';
		}
	} else {
		if( Input::get('cbf_hat') != "" ){
			// otobus - hat
			$Hat = new Hat( Input::get('cbf_hat') );
			$HEADER = '"'.$KAPI_KODU . '" Kodlu Otobüsün "' . $Hat->get_details('hat') . '" Hattındaki Sefer İstatistikleri'; 
		} else {
			// otobus
			$HEADER = '"'.$KAPI_KODU . '" Kodlu Otobüsün Sefer İstatistikleri';
		}
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


	$JQUERYUI = true;
	require 'inc/header.php';
?>
	
	<div class="section-header">
		<?php echo $HEADER ?>
	</div>

	<div class="section-content">

		<div class="filter-container">
			<div class="filter-center active clearfix">

				<div class="filter-row">
					<div class="filter-col">
						<button type="button" class="filterbtn kirmizi" id="filter_tumu">TÜMÜNÜ GÖRÜNTÜLE</button>
					</div>


					<div class="filter-col">
						<span>Günlük</span>
						<input type="text" class="pagininput text" value="<?php echo Common::date_reverse($INPUT_VAL_GUNLUK) ?>" id="dt_gunluk" />
					</div>
					<div class="filter-col">
						<button type="button" class="filterbtn kirmizi" id="filter_uygula_gunluk">UYGULA</button>
					</div>

					
					<div class="filter-col">
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
					<div class="filter-col">
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
					<div class="filter-col">
						<button type="button" class="filterbtn kirmizi" id="filter_uygula_ay_yil">UYGULA</button>
					</div>
				</div>
			</div>
		</div>

		<div class="filo-istatistik">

		</div>

		<div class="filo-istatistik-tables"></div>

	</div>
	

	<script type="text/javascript">

		var TFILTERFROM = '<?php echo $TFILTERFROM ?>',
			TFILTERTO = '<?php echo $TFILTERTO ?>',
			CBFILTER = JSON.parse('<?php echo json_encode( $CBFILTER )?>'),
			KAPI_KODU = '<?php echo $KAPI_KODU ?>';


		var Stat_Grupv2 = function(options){

			this.data = options.data;
			this.init = function(){
				var html = "", ext_data = "", main_data = "";
				for( var x = 0; x < this.data.length; x++ ){
					html += '<ul class="'+this.data[x].class+' clearfix">';

					for( var y = 0; y < this.data[x].data.length; y++ ){
						if( Array.isArray( this.data[x].data[y].data ) ) {
							ext_data = this.data[x].data[y].data[1];
							main_data = this.data[x].data[y].data[0];
						} else {
							ext_data = "";
							main_data = this.data[x].data[y].data;
						}
						html += '<li><a href="'+this.data[x].data[y].url+'"><span>'+this.data[x].data[y].header+'</span><div>'+main_data+'<small>'+ext_data+'</small></div></a></li>';
					}
					html += '</ul>';
				}
				set_html( $AHC('filo-istatistik'), html );
			}

		};


		var StatsDT = function( options ){
			this.data  = options.data;
			this.thead = options.thead;
			this.container = options.container;
			this.init = function(){
				this.data = sort_by_key(this.data, 'sefer_yuzdesi', 'numeric').reverse();
				var html = "<table class='filo-table stats-table hidden'><thead><tr>";
				for( var x = 0; x < this.thead.length; x++ ){
					html += '<td>'+this.thead[x]+'</td>';
				}
				html += '</tr></thead><tbody>';
				for( var x = 0; x < this.data.length; x++ ){
					html += '<tr class="stats-row">';
					for( var key in this.data[x] ){
						if( key == 'hat' ){
							html += '<td><a href="?cbf_hat='+this.data[x][key]+'" class="header-key">'+Filo_Senkronizasyon.HATLAR[this.data[x][key]].hat+'</a></td>';
						} else if( key == 'oto' ){
							html += '<td><a href="'+this.data[x][key]+'" class="header-key">'+this.data[x][key]+'</a></td>';
						} else {
							html += '<td>'+this.data[x][key]+'</td>';
						}
					}
					html += '</tr>';
				}
				html += '</tbody></table>';
			
				set_html( this.container, '<div class="section-header stats-table-header table-toggle">'+options.header+'</div>'+html );
			}
		};



		function depodan_veri_cek( datagrup ){
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

			AHAJAX_V3.req( Base.AJAX_URL + 'filo_istatistikler_2.php', data, function(res){
				console.log(res);
				if( res.ok ){
					datagrup.data = res.fdata;
					datagrup.init();
					set_html( $AHC('filo-istatistik-tables'), "" );
					for( var key in res.tables ){
						append_html( $AHC('filo-istatistik-tables'), '<div class="'+key+'"></div>');
						var dt = new StatsDT({ data: res.tables[key].data, thead: res.tables[key].thead, header:res.tables[key].header, container: $AHC(key) });
						dt.init();
					}
				} else {
					set_html($AHC('filo-istatistik-tables'), "" );
					set_html($AHC('filo-istatistik'), "<div class='section-error'>VERİ YOK</div>" );
				}
				Popup.off();
			});

		}

		AHReady(function(){


			var Data_Grup = new Stat_Grupv2({data: []});
			Data_Grup.init();

			depodan_veri_cek( Data_Grup );

			add_event_on( $AHC('filo-istatistik-tables'), '.table-toggle', 'click', function(targ,ev){
				toggle_class( targ.nextSibling, 'hidden' );
				window.scrollTo(0, get_coords(targ).top);
			});

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
					depodan_veri_cek( Data_Grup );
				}
			});
			add_event( $AH('filter_uygula_gunluk'), 'click', function(){ 
				if( $AH('dt_gunluk').value != "" ){
					TFILTERFROM = reverse_date( $AH('dt_gunluk').value );
					$AH('dt_ay').value = -1;
					$AH('dt_yil').value = -1;
					window.scrollTo(0, 0);
					depodan_veri_cek( Data_Grup );
				}
			});
			add_event( $AH('filter_tumu'), 'click', function(){
				TFILTERFROM = "";
				$AH('dt_ay').value = -1;
				$AH('dt_yil').value = -1;
				$AH('dt_gunluk').value = "";
				window.scrollTo(0, 0);
				depodan_veri_cek( Data_Grup );
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