<?php

	require '../inc/init.php';

	if( $_POST ){

		// ajax output degiskenleri
		$OK    = 1;
		$TEXT  = "";
		$input_output = array();
		$DATA = array();
		
		switch( Input::get("type") ){
			case 'coklu_veri_al':
				$AKTIF_TARIH = ORER_AKTIF_TARIH;

				foreach( json_decode( $_POST['kodlar'] ) as $kod ){
					$data = DB::getInstance()->query("SELECT * FROM " . DBT_ORER_DATA . " WHERE oto = ? && tarih = ? ", array( $kod, $AKTIF_TARIH))->results();
					$mesajlar = DB::getInstance()->query("SELECT * FROM " . DBT_FILO_OTOBUS_MESAJLAR . " WHERE kapi_kodu = ? && tarih = ?", array( $kod, $AKTIF_TARIH ) )->results();

					$Depo = new Otobus_Sefer_Depo( $kod );
					$Depo->stats_switch();
					$Depo->cb_filter( array(), "", "");
					$info['otobus'] = array( 'sefer_yuzdesi' => $Depo->sefer_yuzdesi(), 'toplam_km' => $Depo->toplam_km() );

					$Hat = new Hat( $data[0]['hat'] );
					$info['hat'] = array( 'uzunluk' => (int)( $Hat->get_details("uzunluk") / 1000 ) . " KM" );

					$DATA[$kod] = array( "table_data" => $data, "mesajlar" => $mesajlar, "info" => $info );
				}
			break;

		}

		
		$output = json_encode(array(
			"ok"           => $OK,	    	 // istek tamam mi
			"text" 		   => $TEXT,    	 // bildirim
			"data"		   => $DATA,
			"inputret"     => $input_output, // form input errorlari
			"oh"           => $_POST
		));

		echo $output;
		die;

	}