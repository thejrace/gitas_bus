<?php

	require '../inc/init.php';

	if( $_POST ){

		// ajax output degiskenleri
		$OK    = 1;
		$TEXT  = "";
		$input_output = array();
		$DATA = array();

		
		$INPUT_LIST = array(
			"marka" 				=> array( array( "req" => true ) ,"" ),
			"model" 				=> array( array( "req" => true ), "" ),
			"marka_id" 				=> array( array( "req" => true )  ,"" )
		);


		switch( Input::get("type") ){

			case 'marka_ekle':

				$Validation = new Validation( new InputErrorHandler );
				// Formu kontrol et
				$Validation->check_v2( Input::escape($_POST), $INPUT_LIST );
				if( $Validation->failed() ){
					$OK = 0;
					$input_output = $Validation->errors()->js_format();
				} else {
					
					$Marka = new Otobus_Markasi;
					if( !$Marka->add( Input::escape($_POST)) ){
						$OK = 0;
					}
					$TEXT = $Marka->get_return_text();
				}

			break;	

			case 'model_ekle':

				$Validation = new Validation( new InputErrorHandler );
				// Formu kontrol et
				$Validation->check_v2( Input::escape($_POST), $INPUT_LIST );
				if( $Validation->failed() ){
					$OK = 0;
					$input_output = $Validation->errors()->js_format();
				} else {
					
					$Marka = new Otobus_Markasi(Input::get('marka_id'));
					if( !$Marka->model_ekle( Input::escape($_POST)) ){
						$OK = 0;
					}
					$TEXT = $Marka->get_return_text();
				}

			break;	

			case 'get_models':

				$Marka = new Otobus_Markasi(Input::get('marka_id') );
				$DATA = $Marka->get_models();

			break;


			case 'marka_duzenle':

				$Marka = new Otobus_Markasi( Input::get("marka_id") );
				if( !$Marka->edit( Input::escape($_POST) ) ){
					$OK = 0;
				}
				$TEXT = $Marka->get_return_text();
			break;

			case 'marka_sil':

				$Marka = new Otobus_Markasi( Input::get("item_id") );
				if( !$Marka->delete() ){
					$OK = 0;
				}
				$TEXT = $Marka->get_return_text();

			break;

			case 'model_duzenle':

				$Marka = new Otobus_Markasi( Input::get("marka_id") );
				if( !$Marka->edit_model( Input::escape($_POST) ) ){
					$OK = 0;
				}
				$TEXT = $Marka->get_return_text();

			break;

			case 'model_sil':

				$Marka = new Otobus_Markasi( Input::get("marka_id") );
				if( !$Marka->delete_model( Input::get("item_id") ) ){
					$OK = 0;
				}
				$TEXT = $Marka->get_return_text();

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