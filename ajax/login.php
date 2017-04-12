<?php
	

	$LOGIN_PROCESS = true;
	require '../inc/init.php';


	if( $_POST ){

		// ajax output degiskenleri
		$OK    = 1;
		$TEXT  = "";
		$input_output = array();

		
		$INPUT_LIST = array(
			"email" 				=> array( array( "req" => true, "email" => true )  ,"" ),
			'pass'  				=> array( array( "req" => true ),  "" )
		);

		switch( Input::get("type") ){

			case 'login':

				$Validation = new Validation( new InputErrorHandler );
				// Formu kontrol et
				$Validation->check_v2( Input::escape($_POST), $INPUT_LIST );
				if( $Validation->failed() ){
					$OK = 0;
					$input_output = $Validation->errors()->js_format();
				} else {
					$Log = new Login;
					if( !$Log->action( Input::escape($_POST) ) ){
						
						$OK = 0;
					}
					$TEXT = $Log->get_return_text();
				}

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
