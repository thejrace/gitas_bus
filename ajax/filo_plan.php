<?php

	require '../inc/init.php';

	if( $_POST ){

		// ajax output degiskenleri
		$OK    = 1;
		$TEXT  = "";
		$input_output = array();
		$DATA = array();
		$FILO_DATA = array();
		
		// tum seferleri cekip, filtreliyorum 
		$DEPO = new Otobus_Sefer_Depo( Input::get('kapi_kodu') );

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
		$FILO_DATA = $DEPO->get_tum_seferler();
		$DATA['filo_data'] = $FILO_DATA;
		$DATA['ks'] = count($FILO_DATA);

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