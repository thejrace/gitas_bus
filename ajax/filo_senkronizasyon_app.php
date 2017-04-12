<?php
    
     require '../inc/init.php';
    if( $_POST ){

        // ajax output degiskenleri
        $OK    = 1;
        $TEXT  = "";
        $DATA = array();

        $Log = new Login;
        if( !$Log->action( Input::escape($_POST) ) ){
            $OK = 0;
            $TEXT = $Log->get_return_text();
        } else {
            // sifreleri al
            // otobusleri al
            // app bilgilerini al

            $query = DB::getInstance()->query("SELECT * FROM otobusler_v2" )->results();
            $OTOBUSLER = array();

            foreach( $query as $otobus ){
                $OTOBUSLER[ substr( $otobus['kod'], 0, 1 ) ][] = $otobus['kod'];
            }

            $LOGIN_DATA = array(
                "A" => array( "ka" => "dk_oasa", "s" => "oas145"),
                "B" => array( "ka" => "dk_oasb", "s" => "oas125"),
                "C" => array( "ka" => "dk_oasc", "s" => "oas165")                    
            );

            $FREKANS = 40;

            $DATA = array(
                'otobusler' => $OTOBUSLER,
                'login_data' => $LOGIN_DATA,
                'frekans' => $FREKANS

            );
        }


        $output = json_encode(array(
            "ok"           => $OK,           // istek tamam mi
            "text"         => $TEXT,         // bildirim
            "data"         => $DATA,
            "oh"           => $_POST
        ));

        echo $output;
        die;

    }

  