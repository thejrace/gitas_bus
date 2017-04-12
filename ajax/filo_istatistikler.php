<?php

	require '../inc/init.php';

	if( $_POST ){

		// ajax output degiskenleri
		$OK    = 1;
		$TEXT  = "";
		$input_output = array();
		$DATA = array();
		// OBAREY veya kapi_kodu
		$DEPO = new Otobus_Sefer_Depo( Input::get('kapi_kodu') );
		$DEPO->tum_seferler();
		$HFILTER = false;
		// filo plan icin url
		$STAT_URL = URL_OTOBUS_FILO_PLAN . Input::get('kapi_kodu').'&tfilter='.Input::get('tfilter').'&tarih='.Input::get('tarih').'&data=';
		if( Input::get('tfilter') != 'full' ){
			if( !$DEPO->zaman_ayarli_ayikla( Input::get('tfilter'), Input::get('tarih') ) ){
				$OK = 0;
			}
		}
		if( Input::get('hfilter') != '' ){
			$HFILTER = true;
			$STAT_URL = URL_OTOBUS_FILO_PLAN . Input::get('kapi_kodu').'&hfilter='.Input::get('hfilter').'&tfilter='.Input::get('tfilter').'&tarih='.Input::get('tarih').'&data=';
			if( !$DEPO->hat_ayikla( Input::get('hfilter') ) ){
				$OK = 0;
			}
		}

		// Otobüs İstatistik Data Table
		$OTOBUS_STAT_DATA_TABLE = array();
		// otobüs kapı koduyla veri listelerken yok 

		// sefer tamamlama yüzdelerini hesapliyoruz ona gore DESC diziyoruz
		$KEYS = array( 'A' => 'aktif_seferler', 'ES' => 'ek_seferler', 'Y' => 'yarim_seferler', 'T' => 'tamamlanan_seferler', 'B' => 'bekleyen_seferler', 'I' => 'iptal_seferler', 'EB' => 'tanimsiz_seferler' );
		if( Input::get('kapi_kodu') == "" || Input::get('kapi_kodu') == 'OBAREY' ){
			foreach( $DEPO->get_tum_seferler() as $orer_data ){
				if( isset($OTOBUS_STAT_DATA_TABLE[$orer_data['oto']] ) ){
					if( $orer_data['durum_kodu']  != 'ES' ) $OTOBUS_STAT_DATA_TABLE[$orer_data['oto']]['planlanan_seferler']++;
					if( $orer_data['durum_kodu'] == 'ES') $OTOBUS_STAT_DATA_TABLE[$orer_data['oto']]['ek_seferler']++;
					$OTOBUS_STAT_DATA_TABLE[$orer_data['oto']][ $KEYS[$orer_data['durum']]]++;
					// her loop dan sonra guncelliyoruz sefer yuzdesini
					$OTOBUS_STAT_DATA_TABLE[$orer_data['oto']]['sefer_yuzdesi'] = ceil ($OTOBUS_STAT_DATA_TABLE[$orer_data['oto']]['tamamlanan_seferler'] * 100 / ( $OTOBUS_STAT_DATA_TABLE[$orer_data['oto']]['planlanan_seferler'] + $OTOBUS_STAT_DATA_TABLE[$orer_data['oto']]['ek_seferler'] - $OTOBUS_STAT_DATA_TABLE[$orer_data['oto']]['bekleyen_seferler'] - $OTOBUS_STAT_DATA_TABLE[$orer_data['oto']]['aktif_seferler'] ) );
				} else {
					$OTOBUS_STAT_DATA_TABLE[$orer_data['oto']] = array(
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
					if( $orer_data['durum_kodu']  != 'ES' ) $OTOBUS_STAT_DATA_TABLE[$orer_data['oto']]['planlanan_seferler']++;
					if( $orer_data['durum_kodu'] == 'ES') $OTOBUS_STAT_DATA_TABLE[$orer_data['oto']]['ek_seferler']++;
					$OTOBUS_STAT_DATA_TABLE[$orer_data['oto']][$KEYS[$orer_data['durum']]]++;
					$OTOBUS_STAT_DATA_TABLE[$orer_data['oto']]['sefer_yuzdesi'] =  ceil ($OTOBUS_STAT_DATA_TABLE[$orer_data['oto']]['tamamlanan_seferler'] * 100 / ( $OTOBUS_STAT_DATA_TABLE[$orer_data['oto']]['planlanan_seferler'] + $OTOBUS_STAT_DATA_TABLE[$orer_data['oto']]['ek_seferler'] - $OTOBUS_STAT_DATA_TABLE[$orer_data['oto']]['bekleyen_seferler'] - $OTOBUS_STAT_DATA_TABLE[$orer_data['oto']]['aktif_seferler'] )  );
				}
			}
			$JS_OSTATS_ARRAY = array();
			foreach( $OTOBUS_STAT_DATA_TABLE as $kapi_kodu => $data_array ){
				$taze_array = array('kapi_kodu' => $kapi_kodu );
				$JS_OSTATS_ARRAY[] = array_merge( $taze_array, $data_array );
			}
		}


		if( $OK ){

			$DATA = array(
				'PLANLANAN_SEFERLER'	  	=> count( $DEPO->planlanan_seferler() ),
				'EK_SEFERLER'			  	=> count( $DEPO->ek_seferler() ),
				'TAMAMLANAN_SEFERLER'	  	=> count( $DEPO->tamamlanan_seferler() ),
				'IPTAL_SEFERLER'		  	=> count( $DEPO->iptal_seferler() ),
				'BEKLEYEN_SEFERLER' 		=> count( $DEPO->bekleyen_seferler() ),
				'AKTIF_SEFERLER'  			=> count( $DEPO->aktif_seferler() ),
				'TANIMSIZ_SEFERLER' 		=> count( $DEPO->tanimsiz_seferler() ),
				'YARIM_SEFERLER' 			=> count( $DEPO->yarim_seferler() ),
				'TOPLAM_SURE' 				=> $DEPO->toplam_sure(), 
				'TOPLAM_KM'		 			=> $DEPO->toplam_km(),
				'FAVORI_HAT'			  	=> $DEPO->get_favori_hat(),
				'FAVORI_IPTAL_SEBEBI'	  	=> $DEPO->get_favori_iptal_sebebi(),
				'EN_UZUN_SEFER'			  	=> $DEPO->en_uzun_sefer()
			);
			$DATA['SEFER_YUZDESI'] =  ceil ($DATA['TAMAMLANAN_SEFERLER'] * 100 / ( $DATA['PLANLANAN_SEFERLER'] + $DATA['EK_SEFERLER'] - $DATA['BEKLEYEN_SEFERLER'] - $DATA['AKTIF_SEFERLER'] ) );

			// HAT, HAT + OTOBUS istatistikleri
			if( $HFILTER ){
				$DYN_DORTLU_DATA = array(
					array( 'title' => 'FAVORİ SEFER İPTAL SEBEBİ', 'data' => $DATA["FAVORI_IPTAL_SEBEBI"]['durum_kodu'], 'ext_data'=>$DATA["FAVORI_IPTAL_SEBEBI"]['sefer']. " Sefer" ,'url' => $STAT_URL . 'iptal_seferler' ), 
					//array( 'title' => 'EN UZUN SEFER SÜRESİ', 'data' => $DATA["EN_UZUN_SEFER"]['sure'] . " DK", 'url' => '#' ), 
					array( 'title' => 'CEZA SAYISI', 'data' => 'VERİ YOK', 'url' => $STAT_URL . 'iptal_seferler' )
				);
			} else {

				$DYN_DORTLU_DATA = array(
					array( 'title' => 'EN ÇOK ÇALIŞTIĞI HAT', 'data' => $DATA["FAVORI_HAT"]['hat'], 'ext_data' => $DATA["FAVORI_HAT"]['sefer'] . " Sefer",'url' => $STAT_URL . '&hfilter=' . $DATA['FAVORI_HAT']['id'] ), 
					array( 'title' => 'FAVORİ SEFER İPTAL SEBEBİ', 'data' => $DATA["FAVORI_IPTAL_SEBEBI"]['durum_kodu'], 'ext_data'=>$DATA["FAVORI_IPTAL_SEBEBI"]['sefer']. " Sefer" ,'url' => $STAT_URL . 'iptal_seferler' ), 
					array( 'title' => 'EN UZUN SEFER SÜRESİ', 'data' => $DATA["EN_UZUN_SEFER"]['hat'], 'ext_data' => $DATA["EN_UZUN_SEFER"]['sure'] . " DK",'url' => '#' ), 
					array( 'title' => 'CEZA SAYISI', 'data' => 'VERİ YOK', 'url' => $STAT_URL . 'iptal_seferler' )
				);
			}

			// JS moduna ceviriyoruz
			$DATA = array(
				array( 
					'cls'  => 'dortlu',
					'data' => array(
						array( 'title' => 'PLANLANAN SEFERLER', 'data' => $DATA["PLANLANAN_SEFERLER"], 'url' => $STAT_URL . 'planlanan_seferler' ), 
						array( 'title' => 'TAMAMLANAN SEFERLER', 'data' => $DATA["TAMAMLANAN_SEFERLER"], 'url' => $STAT_URL . 'tamamlanan_seferler' ), 
						array( 'title' => 'BEKLEYEN SEFERLER', 'data' => $DATA["BEKLEYEN_SEFERLER"], 'url' => $STAT_URL . 'bekleyen_seferler' ), 
						array( 'title' => 'AKTİF SEFERLER', 'data' => $DATA["AKTIF_SEFERLER"], 'url' => $STAT_URL . 'aktif_seferler' )
					)
				),
				array( 
					'cls'  => 'dortlu',
					'data' => array(
						array( 'title' => 'İPTAL SEFERLER', 'data' => $DATA["IPTAL_SEFERLER"], 'url' => $STAT_URL . 'iptal_seferler' ), 
						array( 'title' => 'EK SEFERLER', 'data' => $DATA["EK_SEFERLER"], 'url' => $STAT_URL . 'ek_seferler' ), 
						array( 'title' => 'TANIMSIZ SEFERLER', 'data' => $DATA["TANIMSIZ_SEFERLER"], 'url' => $STAT_URL . 'tanimsiz_seferler' ), 
						array( 'title' => 'YARIM KALMIŞ SEFERLER', 'data' => $DATA["YARIM_SEFERLER"], 'url' => $STAT_URL . 'yarim_seferler' )
					)
				),
				array( 
					'cls'  => 'tekli-orta',
					'data' => array(
						array( 'title' => 'SEFER TAMAMLAMA', 'data' => '%'.$DATA['SEFER_YUZDESI'], 'url' => "#" )
					)
				),
				array( 
					'cls'  => 'ikili',
					'data' => array(
						array( 'title' => 'TOPLAM KM', 'data' => $DATA["TOPLAM_KM"] . " KM", 'url' => "#" ), 
						array( 'title' => 'TOPLAM SURE', 'data' => $DATA["TOPLAM_SURE"] . " DK", 'url' => "#" )
					)
				),
				array( 
					'cls'  => 'dortlu',
					'data' => $DYN_DORTLU_DATA
				)
			);

		}
		
		$output = json_encode(array(
			"ok"            => $OK,	    	 // istek tamam mi
			"text" 		    => $TEXT,    	 // bildirim
			"data"		    => $DATA,
			'otobus_dt_data'=> $JS_OSTATS_ARRAY,
			"inputret"      => $input_output, // form input errorlari
			"oh"            => $_POST
		));

		echo $output;
		die;

	}