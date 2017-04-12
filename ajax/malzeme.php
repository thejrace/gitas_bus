<?php

	require '../inc/init.php';

	if( $_POST ){

		// ajax output degiskenleri
		$OK    = 1;
		$TEXT  = "";
		$input_output = array();

		$INPUT_LIST = array(
			"marka" 				=> array( array( "req" => true, 'not_zero' => true )  ,"" ),
			"malzeme_tipi" 			=> array( array( "req" => true )  ,"" ),
			"fiyat" 				=> array( array( "req" => true )  ,"" ),
			"adet" 					=> array( array( "req" => true )  ,"" )
			
		);

		switch( Input::get("type") ){

			case 'malzeme_tipi_ekle':

				$Malzeme_Tipi = new Malzeme_Tipi;

				if( !$Malzeme_Tipi->add( Input::escape($_POST)) ){
					$OK = 0;
				}
				$TEXT = $Malzeme_Tipi->get_return_text();
			break;	

			case 'malzeme_ekle':

				$Malzeme = new Malzeme;
				if( !$Malzeme->add( Input::escape($_POST)) ){
					$OK = 0;
				}
				$TEXT = $Malzeme->get_return_text();

			break;

			case 'malzeme_duzenle':

				$Malzeme = new Malzeme(Input::get("item_id"));
				if( !$Malzeme->edit( Input::escape($_POST)) ){
					$OK = 0;
				}
				$TEXT = $Malzeme->get_return_text();

			break;

			case 'malzeme_sil':

				$Malzeme = new Malzeme(Input::get("item_id"));
				if( !$Malzeme->delete( Input::escape($_POST)) ){
					$OK = 0;
				}
				$TEXT = $Malzeme->get_return_text();

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