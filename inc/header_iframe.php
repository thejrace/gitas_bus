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
        <script type="text/javascript" src="<?php echo URL_RES_JS ?>common.js"></script>
		<script type="text/javascript" src="<?php echo URL_RES_JS ?>main.js"></script>
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


    	<div id="wrapper">

        <div class="header">
            <div id="container" class="clearfix">
                
                <ul class="left-nav">
                    <li class="logo">OBAREY</li>
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
                            <li><a href="<?php echo URL_STOK ?>">STOK</a></li>
                            <li><a href="<?php echo URL_OTOBUS_MARKALAR ?>">OTOBÜS MARKA - MODEL</a></li>
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
                        <li><a href="<?php echo URL_AYARLAR ?>">AYARLAR</a></li>
                        <li><a href="<?php echo URL_LOGOUT ?>">ÇIKIŞ YAP</a></li>
                    </ul>
                </div>


            </div>
            

        

        <script type="text/javascript">

            Base.PERMS = <?php echo json_encode(Active_User::get_perm_actions())?>;

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