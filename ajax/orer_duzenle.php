<?php

	require '../inc/init.php';

	if( $_POST ){

		// ajax output degiskenleri
		$OK    = 1;
		$TEXT  = "";
		$input_output = array();
		$DATA = array();
		
		switch( Input::get("type") ){


			case 'orer_duzenle':
				$Orer_Data = new Orer_Data( Input::get("orer_id") );
				if( !$Orer_Data->edit( Input::escape($_POST) ) ){
					$OK = 0;
				}
				$TEXT = $Orer_Data->get_return_text();

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