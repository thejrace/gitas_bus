<?php

	require '../inc/init.php';

	if( $_POST ){

		// ajax output degiskenleri
		$OK    = 1;
		$TEXT  = "";
		$input_output = array();

		
		$INPUT_LIST = array(
			"fiyat" 				=> array( array( "req" => true )  ,"" ),
			"tarih" 				=> array( array( "req" => true )  ,"" ),
			"otobus_id" 			=> array( array( "req" => true )  ,"" )
		);

		switch( Input::get("type") ){

			case 'ekle':

				$Validation = new Validation( new InputErrorHandler );
				// Formu kontrol et
				$Validation->check_v2( Input::escape($_POST), $INPUT_LIST );
				if( $Validation->failed() ){
					$OK = 0;
					$input_output = $Validation->errors()->js_format();
				} else {
					
					$Kayit = new Yakit_Kaydi;
					if( !$Kayit->add( Input::escape($_POST)) ){
						$OK = 0;
					}
					$TEXT = $Kayit->get_return_text();
				}

			break;	

			case 'duzenle':

				$Validation = new Validation( new InputErrorHandler );
				// Formu kontrol et
				$Validation->check_v2( Input::escape($_POST), $INPUT_LIST );
				if( $Validation->failed() ){
					$OK = 0;
					$input_output = $Validation->errors()->js_format();
				} else {
					
					$Kayit = new Yakit_Kaydi(Input::get("item_id"));
					if( !$Kayit->edit( Input::escape($_POST)) ){
						$OK = 0;
					}
					$TEXT = $Kayit->get_return_text();
				}

			break;	

			case 'sil':

				$Kayit = new Yakit_Kaydi( Input::get("item_id") );
				if( !$Kayit->delete() ){
					$OK = 0;
				}
				$TEXT = $Kayit->get_return_text();
				
			break;

		}

		
		$output = json_encode(array(
			"ok"           => $OK,	    	 // istek tamam mi
			"text" 		   => $TEXT,    	 // bildirim
			"inputret"     => $input_output, // form input errorlari
			"oh"           => $_POST
		));

		echo $output;
		die;

	}