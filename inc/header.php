<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge"> <!-- IE render en son versiyona gore -->
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width,initial-scale=1">
		<link rel="stylesheet" href="<?php echo URL_RES_CSS ?>main.css" />

        <?php
            if( isset($JQUERYUI) ){ ?>
                <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
                <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
                <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
            <?php }
        ?>


        <?php
            if( isset($YANDEXMAPAPI)){ ?> 
                <script src="https://api-maps.yandex.ru/2.1/?lang=tr_TR" type="text/javascript"></script>
            <?php }
        ?>

        <?php

            if( isset($JSMASONRY)){

        ?>
            <script type="text/javascript" src="<?php echo URL_RES_JS ?>masonry.js"></script>
        <?php
            }
        ?>

        <script type="text/javascript" src="<?php echo URL_RES_JS ?>common_obareyobarey.js"></script>
        <!-- <script type="text/javascript" src="<?php //echo URL_RES_JS ?>"</script> -->
		<script type="text/javascript" src="<?php echo URL_RES_JS ?>main_obareyobarey.js"></script>
		<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400,600,300,800,700,400italic|PT+Serif:400,400italic" />

        <title>BUS v2</title>


        <!-- <link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
        <link rel="icon" type="image/png" href="/android-chrome-192x192.png" sizes="192x192">
        <link rel="apple-touch-icon" sizes="180x180" h ref="/apple-touch-icon-180x180.png">-->


	</head>
	<body>

		<div id="popup-overlay"></div>
    	<div id="popup" >
        </div>
        <div id="senkronizasyon_container" style="display:none"></div>
        <div id="senkronizasyon_sofor_container" style="display:none"></div>
        <!-- <div id="senkron-iframe">
            <iframe style="width:100%; height:30px" src="http://ahsaphobby.net/bus/senkron_iframe.php"></iframe>
         </div>-->

    	<div id="wrapper">
        
        <div class="header">
            <div id="container" class="clearfix">
                
                <ul class="left-nav">
                    <li class="logo">OBAREY <img id="header-loader" src="http://ahsaphobby.net/granit/res/img/rolling.gif"  /></li>
                </ul>

                <ul class="right-nav">
                    <li class="dropdown">
                        <div class="dd-button"><i class="ico header-user"></i><span><?php echo Active_User::get_details("user_name")?></span></div>
                       <!--  <div class="dd-content">
                            <ul class="dd-menu">
                                <li><a href="">Ayarlar</a></li>
                                <li><a href="">Çıkış Yap</a></li>
                            </ul>
                        </div> -->
                    </li>
                </ul>

                

            </div>
        </div>
        <div id="coklu-takip-container"><div id="coklu-tables" class="clearfix" data-masonry='{ "percentPosition": true, "columnWidth": ".filo-table-container", "itemSelector": ".filo-table-container" }'></div></div>
        <div id="container">
            
            <div class="main-nav">
                
                <button type="button" class="main-nav-toggle">
                    <i class="ico menu-lines"></i><span>MENÜ</span>
                </button>

                <div class="main-nav-list">
                    <ul>

                    <?php switch( Active_User::get_title() ) {

                            case 'Admin': ?>

                            <li><a href="<?php echo URL_OTOBUSLER ?>">OTOBÜSLER</a></li>
                            <li><a href="<?php echo URL_FILO_TAKIP ?>">FİLO TAKİP</a></li>
                            <li><a href="<?php echo URL_OTOBUS_FILO_PLAN ?>">FİLO SEFER PLANLARI</a></li>
                            <li><a href="<?php echo URL_FILO_ISTATISTIKLER ?>">FİLO İSTATİSTİKLERİ</a></li>
                            <!-- <li><a href="<?php //echo URL_STOK ?>">STOK</a></li> -->
                            <!-- <li><a href="<?php //echo URL_OTOBUS_MARKALAR ?>">OTOBÜS MARKA - MODEL</a></li> -->
                            <li><a href="<?php echo URL_OTOBUS_HATLAR ?>">OTOBÜS HATLAR</a></li>
                        
                        <?php break; ?>

                        <?php case 'Stokçu': ?>

                            <li><a href="<?php echo URL_OTOBUSLER ?>">OTOBÜSLER</a></li>
                            <li><a href="<?php echo URL_STOK ?>">STOK</a></li>
                            <li><a href="<?php echo URL_OTOBUS_MARKALAR ?>">OTOBÜS MARKA - MODEL</a></li>

                        <?php break; ?>

                        <?php case 'Muhasebeci': ?>

                            <li><a href="<?php echo URL_OTOBUSLER ?>">OTOBÜSLER</a></li>
                            <li><a href="<?php echo URL_STOK ?>">STOK</a></li>
                            <li><a href="<?php echo URL_OTOBUS_MARKALAR ?>">OTOBÜS MARKA - MODEL</a></li>
                        
                        <?php break; ?>


                    <?php  } ?>
                        <!-- <li><a href="<?php //echo URL_AYARLAR ?>">AYARLAR</a></li> -->
                        <li><a href="<?php echo URL_LOGOUT ?>">ÇIKIŞ YAP</a></li>
                    </ul>
                </div>


            </div>
      

        <script type="text/javascript">

            <?php

                $query_sof = DB::getInstance()->query("SELECT * FROM " . DBT_SOFORLER)->results();
                $SOFORLER = array();
                foreach( $query_sof as $sofor ){

                    $syn = "";
                    $exp1 = explode(" ", trim($sofor['isim']) );
                    foreach( $exp1 as $kel ){
                        $exp2 = Common::utf8_str_split($kel, 0);
                        $syn .= $exp2[0] . ".";
                    }
                    $SOFORLER[$sofor['id']] = array( 'isim' => $sofor['isim'], 'syn' => $syn, 'telefon' => $sofor['telefon'] );
                }

            ?>

            Base.PERMS = <?php echo json_encode(Active_User::get_perm_actions())?>;
            //Filo_Senkronizasyon.INIT( <?php //echo json_encode( $Filo_Senkronizasyon->get_otobusler() )?>, <?php //echo $Filo_Senkronizasyon->get_son_guncellenme_unix() ?> );
            Filo_Senkronizasyon.HATLAR = <?php echo json_encode($TUM_HATLAR) ?>;
            Filo_Senkronizasyon.SOFORLER = <?php echo json_encode($SOFORLER) ?>;

            AHReady( function(){  
                add_event( $AHC("main-nav-toggle"), "click", function(){
                    toggle_class($AHC("main-nav-list"), "active");
                    fade_in( $AHC("main-nav-list") );
                   
                });

                // add_event( $AHC("dd-button"), "click", function(){

                //     var content = find_elem( this.parentNode, ".dd-content" );
                //     toggle_class(content, "active");
                        

                // });

            });

        </script>