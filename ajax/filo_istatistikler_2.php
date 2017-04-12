<?php

	require '../inc/init.php';

	if( $_POST ){

		// ajax output degiskenleri
		$OK    = 1;
		$TEXT  = "";
		$input_output = array();
		$DATA = array();
		$FDATA = array();
		$TABLES = array();
		// tum seferleri cekip, filtreliyorum 
		$DEPO = new Otobus_Sefer_Depo( Input::get('kapi_kodu') );
		$DEPO->stats_switch();
		$CBFILTER = array();
		$FEXPLODE = explode('&', Input::get('cbf') );
		foreach( $FEXPLODE as $exp ){
			if( $exp != "" && $exp != 'amp;'){ // en sondaki & heralde kafa yormadım
				$exp2 = explode('=', $exp );
				if(  substr( $exp2[0], 0, 4 ) == 'amp;' ){
					$CBFILTER[ substr( $exp2[0], 4 ) ] = explode(",", $exp2[1]);
				} else {
					$CBFILTER[ $exp2[0] ] = explode(",", $exp2[1]);
				}
			}	
		}

		$DEPO->cb_filter( $CBFILTER, Input::get('tfrom'), Input::get('tto'));

		if( $DEPO->isset_data() ){

			$URL_PREFIX = URL_OTOBUS_FILO_PLAN . Input::get('kapi_kodu') . '?tfrom='.Input::get('tfrom').'&tto='.Input::get('tto').'&';
			// hat stats bakiyosa get parametresini çakıyorum
			if( isset( $CBFILTER['hat'] ) ) $URL_PREFIX .= 'cbf_hat='.$CBFILTER['hat'][0]."&";

			// dir => metod degeri direk donuyorsa
			// count => metod array donuyor uzunlugu al
			// array => metod array donuyor 
			$TABS = array(
				0  => array( 'method' => 'planlanan_seferler',			 	'data_type' => 'count', 	'upar' => $URL_PREFIX.'cbf_durum=A,T,B,I,Y,EB',		'header' => 'PLANLANAN SEFERLER' ),
				1  => array( 'method' => 'ek_seferler', 				 	'data_type' => 'count',  	'upar' => $URL_PREFIX.'cbf_durum_kodu=ES',			'header' => 'EK SEFERLER'),
				2  => array( 'method' => 'aktif_seferler', 				 	'data_type' => 'count',  	'upar' => $URL_PREFIX.'cbf_durum=A',				'header' => 'AKTİF SEFERLER'),
				3  => array( 'method' => 'tamamlanan_seferler', 		 	'data_type' => 'count',  	'upar' => $URL_PREFIX.'cbf_durum=T',				'header' => 'TAMAMLANAN SEFERLER'),
				4  => array( 'method' => 'bekleyen_seferler', 		     	'data_type' => 'count',  	'upar' => $URL_PREFIX.'cbf_durum=B',				'header' => 'BEKLEYEN SEFERLER'),
				5  => array( 'method' => 'iptal_seferler', 				 	'data_type' => 'count',  	'upar' => $URL_PREFIX.'cbf_durum=I',				'header' => 'İPTAL SEFERLER'),
				6  => array( 'method' => 'yarim_seferler', 				 	'data_type' => 'count',  	'upar' => $URL_PREFIX.'cbf_durum=Y',				'header' => 'YARIM SEFERLER'),
				7  => array( 'method' => 'tanimsiz_seferler', 			 	'data_type' => 'count',  	'upar' => $URL_PREFIX.'cbf_durum=EB',				'header' => 'TANIMSIZ SEFERLER'),
				8  => array( 'method' => 'toplam_km', 					 	'data_type' => 'dir',  		'upar' => '#',										'header' => 'TOPLAM KM'),
				9  => array( 'method' => 'toplam_sure', 				 	'data_type' => 'dir',  		'upar' => '#',										'header' => 'TOPLAM SÜRE'),
				10 => array( 'method' => 'get_favori_hat', 					'data_type' => 'dir',  		'upar' => '#',										'header' => 'FAVORİ HAT'),  // otobüs istatistikleri için
				11 => array( 'method' => 'sefer_yuzdesi', 				 	'data_type' => 'dir',		'upar' => '#',										'header' => 'SEFER YÜZDESİ'),
				12 => array( 'method' => 'get_favori_iptal_sebebi', 		'data_type' => 'dir',		'upar' => $URL_PREFIX.'cbf_durum=I',				'header' => 'FAVORİ İPTAL SEBEBİ'),
				13 => array( 'method' => 'en_cok_sefer_yapan_hat', 	     	'data_type' => 'dir',		'upar' => '#',										'header' => 'EN ÇOK SEFER YAPAN HAT'), // İPTAL İPTAL
				14 => array( 'method' => 'en_cok_iptal_olan_hat',        	'data_type' => 'dir',		'upar' => '#',										'header' => 'EN ÇOK İPTAL OLAN HAT'),
				15 => array( 'method' => 'en_cok_sefer_yapan_otobus_dk',    'data_type' => 'dir',		'upar' => '#',										'header' => 'EN UZUN SEFERDE OLAN'), // dk hesabı
				16 => array( 'method' => 'en_cok_sefer_yapan_otobus_sefer', 'data_type' => 'dir',		'upar' => '#',										'header' => 'EN ÇOK SEFER YAPAN') // Sefer sayisi hesabı
			);

			$TABLOLAR = array(
				0 => 'OTOBÜS SEFER İSTATİSTİKLERİ',
				1 => 'HAT İSTATİSTİKLERİ',
				2 => 'DURUM KODLARI İSTATİSTİKLERİ',
				3 => 'HATTA ÇALIŞAN OTOBÜSLER İSTATİSTİKLER'
			);

			$PLAN = array(
				'Tüm İstatistikler' => array(
					/*
						PLANLANAN SEFERLER-------|
						EK SEFERLER              |  DÖRTLÜ
						AKTİF SEFERLER           |
						TAMAMLANAN SEFERLER------|
						
						BEKLEYEN SEFERLER--------|
						İPTAL SEFERLER           |  DÖRTLÜ
						YARIM SEFERLER           |
						TANIMSIZ SEFERLER--------|
						
						SEFER YÜZDESİ------------|  TEKLİ ORTA
		
						TOPLAM KM----------------|  İKİLİ
						TOPLAM SÜRE--------------|

						EN ÇOK SEFER YAPAN HAT---------------|
						EN ÇOK İPTAL OLAN HAT                |  DÖRTLÜ
						EN ÇOK SEFERE YAPAN OTOBÜS DK        |
						EN ÇOK SEFER YAPAN OTOBÜS SEFER -----|
					*/
					'TABS' => array( 
						0  => array( 'class' => 'dortlu', 			'data' => array( $TABS[0], $TABS[1], $TABS[2], $TABS[3] ) ),
						1  => array( 'class' => 'dortlu', 			'data' => array( $TABS[4], $TABS[5], $TABS[6], $TABS[7] )),
						2  => array( 'class' => 'tekli-orta', 		'data' => array( $TABS[11] ) ),
						3  => array( 'class' => 'ikili', 			'data' => array( $TABS[8], $TABS[9] )),
						4  => array( 'class' => 'dortlu', 			'data' => array( $TABS[10], $TABS[14], $TABS[15], $TABS[16] ))
					),
					'TABLOLAR' => array( $TABLOLAR[0], $TABLOLAR[1] )
				),
				'Hat İstatistikleri' => array(
					/*
						PLANLANAN SEFERLER-------|
						EK SEFERLER              |  DÖRTLÜ
						AKTİF SEFERLER           |
						TAMAMLANAN SEFERLER------|
						
						BEKLEYEN SEFERLER--------|
						İPTAL SEFERLER           |  DÖRTLÜ
						YARIM SEFERLER           |
						TANIMSIZ SEFERLER--------|
						
						SEFER YÜZDESİ------------|  TEKLİ ORTA
		
						TOPLAM KM----------------|  İKİLİ
						TOPLAM SÜRE--------------|
						
						FAVORİ İPTAL SEBEBİ------------------|  
						EN ÇOK SEFERE YAPAN OTOBÜS DK        |  DÖRTLÜ .
						EN ÇOK SEFER YAPAN OTOBÜS SEFER -----|
					*/
					'TABS' => array( 
						0  => array( 'class' => 'dortlu', 			'data' => array( $TABS[0], $TABS[1], $TABS[2], $TABS[3] ) ),
						1  => array( 'class' => 'dortlu', 			'data' => array( $TABS[4], $TABS[5], $TABS[6], $TABS[7] )),
						2  => array( 'class' => 'tekli-orta', 		'data' => array( $TABS[11] ) ),
						3  => array( 'class' => 'ikili', 			'data' => array( $TABS[8], $TABS[9] )),
						4  => array( 'class' => 'dortlu', 			'data' => array( $TABS[12], $TABS[15], $TABS[16] ))
					),

					'TABLOLAR' => array( $TABLOLAR[3], $TABLOLAR[2] )
				),
				'Otobüs - Hat İstatistikleri' => array(
					/*
						PLANLANAN SEFERLER-------|
						EK SEFERLER              |  DÖRTLÜ
						AKTİF SEFERLER           |
						TAMAMLANAN SEFERLER------|
						
						BEKLEYEN SEFERLER--------|
						İPTAL SEFERLER           |  DÖRTLÜ
						YARIM SEFERLER           |
						TANIMSIZ SEFERLER--------|
						
						SEFER YÜZDESİ------------|  TEKLİ ORTA
		
						TOPLAM KM----------------|  İKİLİ
						TOPLAM SÜRE--------------|
						
						FAVORİ İPTAL SEBEBİ------|  DÖRTLÜ ..
					*/
					'TABS' => array( 
						0  => array( 'class' => 'dortlu', 			'data' => array( $TABS[0], $TABS[1], $TABS[2], $TABS[3] ) ),
						1  => array( 'class' => 'dortlu', 			'data' => array( $TABS[4], $TABS[5], $TABS[6], $TABS[7] )),
						2  => array( 'class' => 'tekli-orta', 		'data' => array( $TABS[11] ) ),
						3  => array( 'class' => 'ikili', 			'data' => array( $TABS[8], $TABS[9] )),
						4  => array( 'class' => 'dortlu', 			'data' => array( $TABS[12] ))
					),

					'TABLOLAR' => array( $TABLOLAR[2] )		
				),
				'Otobüs İstatistikleri' => array(
					/*
						PLANLANAN SEFERLER-------|
						EK SEFERLER              |  DÖRTLÜ
						AKTİF SEFERLER           |
						TAMAMLANAN SEFERLER------|
						
						BEKLEYEN SEFERLER--------|
						İPTAL SEFERLER           |  DÖRTLÜ
						YARIM SEFERLER           |
						TANIMSIZ SEFERLER--------|
						
						SEFER YÜZDESİ------------|  TEKLİ ORTA
		
						TOPLAM KM----------------|  İKİLİ
						TOPLAM SÜRE--------------|
						
						FAVORİ İPTAL SEBEBİ------|  DÖRTLÜ ..
						FAVORİ HAT---------------|
					*/
					'TABS' => array( 
						0  => array( 'class' => 'dortlu', 			'data' => array( $TABS[0], $TABS[1], $TABS[2], $TABS[3] ) ),
						1  => array( 'class' => 'dortlu', 			'data' => array( $TABS[4], $TABS[5], $TABS[6], $TABS[7] )),
						2  => array( 'class' => 'tekli-orta', 		'data' => array( $TABS[11] ) ),
						3  => array( 'class' => 'ikili', 			'data' => array( $TABS[8], $TABS[9] )),
						4  => array( 'class' => 'dortlu', 			'data' => array( $TABS[12], $TABS[10] ))
					),
					'TABLOLAR' => array( $TABLOLAR[1], $TABLOLAR[2] )
				)
			);

			function istatistik_plan( Otobus_Sefer_Depo $depo, $plan ){
				$final = array();
				// Session::set('rev', $plan['TABS'] );
				foreach( $plan['TABS'] as $tab_item => $tab_array ){
					$gen_data = array();
					foreach( $tab_array['data'] as $tab_item ){
						if( method_exists( $depo, $tab_item['method'] ) ){
							if( $tab_item['data_type'] == 'count'){
								$DATA = count(call_user_func( array($depo, $tab_item['method'] ) ));
							} else if( $tab_item['data_type'] == 'dir') {
								$DATA = call_user_func( array($depo, $tab_item['method'] ) );
								
							}
							$gen_data[] = array( 'header' => $tab_item['header'], 'data' => $DATA, 'url' => $tab_item['upar'] );
						} else {
							$gen_data[] = array( 'header' => $tab_item['header'], 'data' => 'METHOD YOK', 'url' => $tab_item['upar'] );
						}
					}
					$final[] = array( 'class' => $tab_array['class'], 'data' => $gen_data );
				}
				return $final;
			}

			
			function sefer_stats_table( $data_key, $seferler ){
				$KEYS = array( 'A' => 'aktif_seferler', 'ES' => 'ek_seferler', 'Y' => 'yarim_seferler', 'T' => 'tamamlanan_seferler', 'B' => 'bekleyen_seferler', 'I' => 'iptal_seferler', 'EB' => 'tanimsiz_seferler' );
				$OTOBUS_STAT_DATA_TABLE = array();
				$DATA_KEY = $data_key;
				foreach( $seferler as $orer_data ){
					if( isset($OTOBUS_STAT_DATA_TABLE[$orer_data[$data_key]] ) ){
						if( $orer_data['durum_kodu']  != 'ES' ) $OTOBUS_STAT_DATA_TABLE[$orer_data[$data_key]]['planlanan_seferler']++;
						if( $orer_data['durum_kodu'] == 'ES') $OTOBUS_STAT_DATA_TABLE[$orer_data[$data_key]]['ek_seferler']++;
						$OTOBUS_STAT_DATA_TABLE[$orer_data[$data_key]][ $KEYS[$orer_data['durum']]]++;
						// her loop dan sonra guncelliyoruz sefer yuzdesini

						$payda = ( $OTOBUS_STAT_DATA_TABLE[$orer_data[$data_key]]['planlanan_seferler'] + $OTOBUS_STAT_DATA_TABLE[$orer_data[$data_key]]['ek_seferler'] - $OTOBUS_STAT_DATA_TABLE[$orer_data[$data_key]]['bekleyen_seferler'] - $OTOBUS_STAT_DATA_TABLE[$orer_data[$data_key]]['aktif_seferler'] );
						if( $payda <= 0 ){
							$payda = 10000;
						}

						$OTOBUS_STAT_DATA_TABLE[$orer_data[$data_key]]['sefer_yuzdesi'] = ceil ($OTOBUS_STAT_DATA_TABLE[$orer_data[$data_key]]['tamamlanan_seferler'] * 100 / $payda );
					} else {
						$OTOBUS_STAT_DATA_TABLE[$orer_data[$data_key]] = array(
							'planlanan_seferler' => 0,
							'ek_seferler' => 0,
							'tamamlanan_seferler' => 0,
							'bekleyen_seferler' => 0,
							'aktif_seferler' => 0,
							'iptal_seferler' => 0,
							'yarim_seferler' => 0,
							'tanimsiz_seferler' => 0,
							'sefer_yuzdesi' => 0		
						);
						if( $orer_data['durum_kodu']  != 'ES' ) $OTOBUS_STAT_DATA_TABLE[$orer_data[$data_key]]['planlanan_seferler']++;
						if( $orer_data['durum_kodu'] == 'ES') $OTOBUS_STAT_DATA_TABLE[$orer_data[$data_key]]['ek_seferler']++;
						$OTOBUS_STAT_DATA_TABLE[$orer_data[$data_key]][$KEYS[$orer_data['durum']]]++;
						$payda =  ( $OTOBUS_STAT_DATA_TABLE[$orer_data[$data_key]]['planlanan_seferler'] + $OTOBUS_STAT_DATA_TABLE[$orer_data[$data_key]]['ek_seferler'] - $OTOBUS_STAT_DATA_TABLE[$orer_data[$data_key]]['bekleyen_seferler'] - $OTOBUS_STAT_DATA_TABLE[$orer_data[$data_key]]['aktif_seferler'] );
						if( $payda > 0 ){
							$OTOBUS_STAT_DATA_TABLE[$orer_data[$data_key]]['sefer_yuzdesi'] =  ceil ($OTOBUS_STAT_DATA_TABLE[$orer_data[$data_key]]['tamamlanan_seferler'] * 100 / $payda  );
						} else {
							$OTOBUS_STAT_DATA_TABLE[$orer_data[$data_key]]['sefer_yuzdesi'] =  0;
						}
						
					}
				}
				$JS_OSTATS_ARRAY = array();
				foreach( $OTOBUS_STAT_DATA_TABLE as $kapi_kodu => $data_array ){
					$taze_array = array( $DATA_KEY => $kapi_kodu );
					$JS_OSTATS_ARRAY[] = array_merge( $taze_array, $data_array );
				}
				return $JS_OSTATS_ARRAY;
			}



			if( empty($CBFILTER) && Input::get('kapi_kodu') == 'OBAREY' ){
				// tum istatistikleri

				$FDATA = istatistik_plan( $DEPO, $PLAN['Tüm İstatistikler']); 

				$TABLES['tum_otobusler_sefer_istatistikleri'] = array( 'data' => sefer_stats_table('oto', $DEPO->get_tum_seferler()), 'thead' => array( 'Kapı Kodu', 'Planlanan', 'Ek', 'Tamamlanan', 'Bekleyen', 'Aktif', 'İptal', 'Yarım', 'Tanımsız','Sefer Yüzdesi' ), 'header' => 'Tüm Otobüsler Sefer İstatistikleri' );

				$TABLES['hat_istatistikleri'] = array( 'data' => sefer_stats_table('hat', $DEPO->get_tum_seferler()), 'thead' => array( 'Hat', 'Planlanan', 'Ek', 'Tamamlanan', 'Bekleyen', 'Aktif', 'İptal', 'Yarım', 'Tanımsız', 'Sefer Yüzdesi' ), 'header' => 'Hat İstatistikleri' );



			} else if( empty( $CBFILTER ) && Input::get('kapi_kodu') != 'OBAREY'){
				// otobus istatistikleri

				$FDATA = istatistik_plan( $DEPO, $PLAN['Otobüs İstatistikleri'] ); 

				$TABLES['hat_istatistikleri'] = array( 'data' => sefer_stats_table('hat', $DEPO->get_tum_seferler()), 'thead' => array( 'Hat', 'Planlanan', 'Ek', 'Tamamlanan', 'Bekleyen', 'Aktif', 'İptal', 'Yarım', 'Tanımsız', 'Sefer Yüzdesi' ), 'header' => 'Hat İstatistikleri' );
				
			} else if( isset($CBFILTER['hat']) && Input::get('kapi_kodu') != 'OBAREY' ){
				// otobus - hat istatistikleri

				$FDATA = istatistik_plan( $DEPO, $PLAN['Otobüs - Hat İstatistikleri']); 
			} else if( isset($CBFILTER['hat']) && Input::get('kapi_kodu') == 'OBAREY' ){
				// hat istatistikleri

				$FDATA = istatistik_plan( $DEPO, $PLAN['Hat İstatistikleri']); 

				$TABLES['tum_otobusler_sefer_istatistikleri'] = array( 'data' => sefer_stats_table('oto', $DEPO->get_tum_seferler()), 'thead' => array( 'Kapı Kodu', 'Planlanan', 'Ek', 'Tamamlanan', 'Bekleyen', 'Aktif', 'İptal', 'Yarım', 'Tanımsız','Sefer Yüzdesi' ), 'header' => 'Hatta Çalışan Otobüs İstatistikleri' );

			}
		
		} else {
			$OK = 0;
		}
		
		

		$output = json_encode(array(
			"ok"           => $OK,	    	 // istek tamam mi
			"text" 		   => $TEXT,    	 // bildirim
			"data"		   => $DATA,
			"fdata"		   => $FDATA,
			"tables"	   => $TABLES,
			"inputret"     => $input_output, // form input errorlari
			"oh"           => $_POST
		));

		echo $output;
		die;

	}