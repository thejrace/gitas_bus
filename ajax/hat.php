<?php

	require '../inc/init.php';

	if( $_POST ){

		// ajax output degiskenleri
		$OK    = 1;
		$TEXT  = "";
		$input_output = array();

		
		$INPUT_LIST = array(
			"adet" 					=> array( array( "req" => true, "numerik" => true, "not_zero" => true )  ,"" ),
			"aciklama" 				=> array( array()  ,"" ),
			"otobus_id" 			=> array( array( "req" => true )  ,"" ),
			"malzeme_id" 			=> array( array( "req" => true )  ,"" )
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
					
					$Hat = new Hat;
					if( !$Hat->add( Input::escape($_POST)) ){
						$OK = 0;
					}
					$TEXT = $Hat->get_return_text();
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
					
					$Hat = new Hat(Input::get("item_id"));
					if( !$Hat->edit( Input::escape($_POST)) ){
						$OK = 0;
					}
					$TEXT = $Hat->get_return_text();
				}

			break;	

			case 'sil':

				$Hat = new Hat( Input::get("item_id") );
				if( !$Hat->delete() ){
					$OK = 0;
				}
				$TEXT = $Hat->get_return_text();
				
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