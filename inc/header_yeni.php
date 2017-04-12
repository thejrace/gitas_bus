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

        <script type="text/javascript" src="<?php echo URL_RES_JS ?>common.js"></script>
        <script type="text/javascript" src="<?php echo URL_RES_JS ?>senkronizasyon.js"></script>
		<script type="text/javascript" src="<?php echo URL_RES_JS ?>main.js"></script>
		<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400,600,300,800,700,400italic|PT+Serif:400,400italic" />

        <title>BUS v2</title>


        <!-- <link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
        <link rel="icon" type="image/png" href="/android-chrome-192x192.png" sizes="192x192">
        <link rel="apple-touch-icon" sizes="180x180" h ref="/apple-touch-icon-180x180.png">-->

        <style>

            .yeni-nav {

            }


            .yeni-nav .top-full-nav {
                margin-top:20px;
            }

            .yeni-nav .top-full-nav > li a {
                width:100%;
                display: block;
                font-size: 18px;
                background: #fff;
                border-bottom: 1px solid #d3d3d3;
                margin-bottom: 10px;
                color: #c35757;
                font-weight: 600;
                text-align: center;
                padding: 10px 0;
            }

            .yeni-nav .mid-navs {
                
            }

            .yeni-nav .mid-navs > li {
                float:left;
                padding:0 1%;
                width:31%;
                
            }
            
            .yeni-nav .mid-navs > li .mid-nav-header {
                text-align:center;
                color:#ababab;
                font-size:26px;
                font-weight:600;
                padding:10px 0 25px 0
            }  
            .yeni-nav .mid-navs > li .mid-nav-items {

            }

            .yeni-nav .mid-navs > li .mid-nav-items li a {
                display: block;
                font-size: 18px;
                background: #fff;
                border-bottom: 1px solid #d3d3d3;
                margin-bottom: 10px;
                color: #c35757;
                font-weight: 600;
                text-align: center;
                padding: 10px 0;
            }

            @media screen and ( min-width:1px ) and (max-width:767px ){
                .yeni-nav .mid-navs > li {
                    float:none!important;
                    width:98%;

                }
                 .yeni-nav .mid-navs > li .mid-nav-header {
                    padding:10px 0 !important;
                 }
            }

        </style>
        

	</head>
	<body>

		<div id="popup-overlay"></div>
    	<div id="popup" >
        </div>
        <div id="senkronizasyon_container" style="display:none"></div>

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
            
            
            <div class="yeni-nav">
                
                <ul class="top-full-nav">
                    <li><a href="">AKTİVİTE MONİTÖRÜ</a></li>
                </ul>

                <ul class="mid-navs clearfix">
                    <li>
                        <div class="mid-nav-header">FİLO TAKİP</div>
                        <ul class="mid-nav-items">
                            <li><a href="<?php echo URL_FILO_TAKIP ?>">FİLO TAKİP</a></li>
                            <li><a href="<?php echo URL_OTOBUSLER ?>">OTOBÜSLER</a></li>
                            <li><a href="">FİLO HARİTA TAKİP</a></li>
                            <li><a href="<?php echo URL_OTOBUS_HATLAR ?>">DURAKLAR VE GÜZERGAHLAR</a></li>
                            <li><a href="<?php echo URL_FILO_ISTATISTIKLER ?>">İSTATİSTİKLER</a></li>
                        </ul>
                    </li>
                    <li>
                        <div class="mid-nav-header">SERVİS - STOK</div>
                        <ul class="mid-nav-items">
                            <li><a href="<?php echo URL_STOK ?>">STOK</a></li>
                            <li><a href="">SERVİS KAYITLARI</a></li>
                            <li><a href="">MALZEME İSTEKLERİ</a></li>
                            <li><a href="">REVİZYON MALZEME SEPETİ</a></li>
                            <li><a href="">İSTATİSTİKLER</a></li>
                        </ul>
                    </li>
                    <li>
                        <div class="mid-nav-header">YAKIT DOLUM</div>
                        <ul class="mid-nav-items">
                            <li><a href="">DOLUM KAYITLARI</a></li>
                            <li><a href="">OTOBÜS YAKIT TAKİP</a></li>
                            <li><a href="">İSTATİSTİKLER</a></li>
                        </ul>
                    </li>
                </ul>

            </div>



        <?php
            // Filo senkronizasyon degiskenler ve kontrol verileri
            $bolgeler = array( "A" => "dk_oasa", "B" => "dk_oasb", "C" => "dk_oasc");
            $BASE_OTOBUSLER = array();
            $query = DB::getInstance()->query("SELECT * FROM " . DBT_OTOBUSLER)->results();
            foreach( $query as $otobus ){
                $BASE_OTOBUSLER[ $bolgeler[substr( $otobus['kod'], 0, 1 )]  ][] = $otobus['kod'];
            }
            $Filo_Senkronizasyon = new Filo_Senkronizasyon;
        ?>
        

        <script type="text/javascript">

            Base.PERMS = <?php echo json_encode(Active_User::get_perm_actions())?>;
            Filo_Senkronizasyon.INIT( <?php echo json_encode( $BASE_OTOBUSLER )?>, <?php echo $Filo_Senkronizasyon->get_son_guncellenme_unix() ?> );

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