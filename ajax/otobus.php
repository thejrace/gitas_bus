<?php

	require '../inc/init.php';

	if( $_POST ){

		// ajax output degiskenleri
		$OK    = 1;
		$TEXT  = "";
		$input_output = array();

		$INPUT_LIST = array(
			"marka" 				=> array( array( "req" => true, 'not_zero' => true )  ,"" ),
			"model" 				=> array( array( "req" => true, 'not_zero' => true )  ,"" ),
			"model_yili" 			=> array( array( "req" => true )  ,"" ),
			"hat_kodu" 				=> array( array( "req" => true )  ,"" )
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
					
					$Otobus = new Otobus;
					if( !$Otobus->add( Input::escape($_POST) ) ){
						$OK = 0;
					}
					$TEXT = $Otobus->get_return_text();

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
					
					$Otobus = new Otobus( Input::get("item_id"));
					if( !$Otobus->edit( Input::escape($_POST) ) ){
						$OK = 0;
					}
					$TEXT = $Otobus->get_return_text();

				}

			break;

			case 'sil':

				$Otobus = new Otobus( Input::get("item_id") );
				if( !$Otobus->delete() ){
					$OK = 0;
				}
				$TEXT = $Otobus->get_return_text();

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