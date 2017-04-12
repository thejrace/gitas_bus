<?php

	require '../inc/init.php';

	if( $_POST ){

		// ajax output degiskenleri
		$OK    = 1;
		$TEXT  = "";
		$input_output = array();

			
		// $INPUT_LIST = array(
		// 	"malzeme_tipi" 			=> array( array( "req" => true )  ,"" ),
		// 	"fiyat" 				=> array( array( "req" => true )  ,"" ),
		// 	"adet" 					=> array( array( "req" => true )  ,"" )
		// );


		switch( Input::get("type") ){

			case 'otobus_sec':

				$Otobus = new Otobus( Input::get('kapi_no') );
				if( $Otobus->is_valid() ){
					$OTOBUS_DATA = $Otobus->get_details();
					$DATA['otobus_data'] = array(
						'id'		=> $OTOBUS_DATA['id'],
						'plaka' 	=> $OTOBUS_DATA['plaka'],
						'marka' 	=> $OTOBUS_DATA['marka'],
						'tip' 		=> $OTOBUS_DATA['tip'],
						'model' 	=> $OTOBUS_DATA['model'],
						'motor_no' 	=> "XXXXXXXX",
						'sasi_no'	=> "XXXXXXX",
						'arac_no' 	=> "XXXXX",
						'km' 		=> "XXXX"
					);

					$Musteri = new Otobus_Sahibi( $OTOBUS_DATA['sahip'] );
					$MUSTERI_DATA = $Musteri->get_details(); 
					$DATA['musteri_data'] = array(
						'id'			=> $MUSTERI_DATA['id'],
						'isim' 			=> $MUSTERI_DATA['isim'],
						'adres' 		=> $MUSTERI_DATA['adres'],
						'vergi_data' 	=> $MUSTERI_DATA['vergi_dairesi'] . " / " . $MUSTERI_DATA['vergi_no'],
						'telefonlar' 	=> $MUSTERI_DATA['sabit_telefon'],
						'fax_gsm' 		=> $MUSTERI_DATA['fax'] . " / " . $MUSTERI_DATA['gsm'],
						'parca_tutari' 	=> "XXXXX"
					);
				} else {
					$DATA['musteri_data'] = array();
					$DATA['otobus_data']  = array();
					$OK = 0;
					$TEXT = 'Böyle bir otobüs yok.';
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