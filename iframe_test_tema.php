<?php
    
    require 'inc/init.php';

    // js icin
    $bolgeler = array( "A" => "dk_oasa", "B" => "dk_oasb", "C" => "dk_oasc");
    $OTOBUSLER = array();

    $query = DB::getInstance()->query("SELECT * FROM " . DBT_OTOBUSLER)->results();
    foreach( $query as $otobus ){
        $OTOBUSLER[ $bolgeler[substr( $otobus['kod'], 0, 1 )]  ][] = $otobus['kod'];
    }
    $LOG_TYPE = 'komple';

    $Filo_Senkronizasyon = new Filo_Senkronizasyon;
    $GUNCELLE = $Filo_Senkronizasyon->orer_frekans_kontrol();

?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge"> <!-- IE render en son versiyona gore -->
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width,initial-scale=1">
		<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400,600,300,800,700,400italic|PT+Serif:400,400italic" />
        <script type="text/javascript" src="<?php echo URL_RES_JS ?>common.js"></script>
        <script type="text/javascript" src="<?php echo URL_RES_JS ?>main.js"></script>
        <link rel="stylesheet" href="<?php echo URL_RES_CSS ?>iframe_header.css" />
        <title>BUS v2</title>


	</head>
	<body>
        <div id="popup-overlay"></div>
        <div id="popup" >
        </div>
        <div class="main-header">
            <div id="container">
                <div class="left-info"></div>
                <span><button type="button" id="guncelle" class="filterbtn gri">GÜNCELLE</button></span>
                <div class="right-tarih">Obarey Core</div>
            </div>
        </div>
        <div class="iframe" style="margin:0px;padding:0px;overflow:hidden">
            <iframe src="http://ahsaphobby.net/bus/Otobusler" id="main-iframe" frameborder="0" height="130%" width="100%"></iframe>
        </div>
        <div id="senkronizasyon_container" style="display:none"></div>

        <script type="text/javascript">

            AHReady(function(){

   
                if( GUNCELLE ) {
                    //clearInterval( REFRESH_INTERVAL );
                    Popup.start_loader();
                    set_status( "Güncelleniyor", "..." );
                    orer_refresh();
                } else {
                    set_status( "VT Güncel", "<?php echo $Filo_Senkronizasyon->get_guncellenme() ?>" );
                }

                //console.log('Guncellemeye kalan süre : ' + ( ORER_FREKANS - (Math.floor(Date.now()/1000) - SON_UNIX) ));
                add_event( $AH('guncelle'), 'click', orer_refresh );

                //REFRESH_INTERVAL = setInterval(refresh_kontrol,REFRESH_INTERVAL_FREKANS);
            });

            function set_status(status, dt){
                set_html($AHC('left-info'), status + " ( " + dt + " )" );
            }
            function refresh_kontrol(){
                console.log('Güncelleme kontrolü ( Kalan süre : ' + ( ORER_FREKANS - (Math.floor(Date.now()/1000) - SON_UNIX) ) + ' saniye )');
                if( Math.floor(Date.now()/1000) - SON_UNIX >= ORER_FREKANS ) orer_refresh(); 
            }
            
            
            var OTOBUSLER = <?php echo json_encode( $OTOBUSLER ); ?>,
                GUNCELLE = <?php echo $GUNCELLE ?>,
                SON_UNIX = <?php echo $Filo_Senkronizasyon->get_son_guncellenme_unix() ?>,
                // toplam yapilan ajax istegi sayaci
                REQUEST_COUNTER = 0,
                // toplamda yapilacak ajax istegi
                TOTAL_REQ_COUNT = 0,
                // aktif senkronizasyon kontrol bayragi
                SYNC_BASLADI = false,
                ITEM_DATA = [],
                Form_Data = new FormData(),
                DB_KAYDET_INTERVAL = "",
                REFRESH_INTERVAL = "",
                REFRESH_INTERVAL_FLAG = true,
                ORER_FREKANS = <?php echo $Filo_Senkronizasyon->get_orer_frekans() ?>,
                LOG_TYPE = "<?php echo $LOG_TYPE ?>",
                IFRAME = $AH('main-iframe'),
                REFRESH_INTERVAL_FREKANS = 10*1000;

            // senkronizasyon baslat
            function orer_refresh(){
                set_status( "Güncelleniyor", "..." );
                //clearInterval( REFRESH_INTERVAL );
                Popup.start_loader();
                DB_KAYDET_INTERVAL = setInterval(kaydet_db, 1000);
                Form_Data = new FormData();
                ITEM_DATA = {};
                SYNC_BASLADI = true;
                Form_Data.append('type', 'filo_orer_senkronizasyon');
                Form_Data.append('log_type', LOG_TYPE );
                // her bolge icin, tum hatlarin bilgileri alinacak
                for( var bolge in OTOBUSLER ){
                    ITEM_DATA[bolge] = {};
                    // her hat icin veriyi filodan alip listeye ekliyoruz
                    for( var i = 0; i < OTOBUSLER[bolge].length; i++ ){
                        get_data( i, bolge );
                        TOTAL_REQ_COUNT++;
                    }
                }
            }

            // ajaxlari for loop icinde yaptigim icin ajax isteklerini ayri bi fonksiyon yapmak zorunda kaldım
            // async oldugu icin ajaxlar loop ajax isteginden bagimsiz devam ediyor direk for icinde ajax yaparsak
            // o yuzden her bir async istegi fonksiyon icinde yapip loop taki aktif index i parametre geçip elimizde
            // tutuyoruz
            function get_data( index, bolge ){
                var TRDATA = [];
                var status_str = "Bölge: " + bolge + " / " + " KapıNo: " + OTOBUSLER[bolge][index];
                console.log( status_str + " veri isteği yapılıyor..." );
                ITEM_DATA[bolge][OTOBUSLER[bolge][index]] = [];
                // filoya bolge ve kapi no ile istek yapiyoruz
                AHAJAX_V3_TEXT.req( "http://ahsaphobby.net/otobus/iett/filo_veri_download/request.php", manual_serialize({ type:'filo_orer_guncelle', bolge: bolge, kapi_no:OTOBUSLER[bolge][index]}), function(res){
                    console.log( status_str + " veri alındı, işleniyor..");  
                    // her istekte rowlari tuttugumuz array i resetliyoruz
                    TRDATA = [];
                    // gelen veriyi dive aldık islem yapmak icin
                    set_html( $AH('senkronizasyon_container'), res );
                    // row un altindaki td ler
                    var tr = find_elem( $AH('senkronizasyon_container'), "tbody" ).childNodes;
                    // her bir td nin tuttugu veriyi alip listeliyoruz
                    for( var j = 0; j < tr.length; j++ ){
                        // td lerin 3 ayri class i var herhangi birine uyani aliyoruz( text node lari almamak icin )
                        if( hasClass( tr[j], "yazid") || hasClass(tr[j], "yazim" )|| hasClass(tr[j], "yazik") || hasClass(tr[j], "yazi")){
                        // kriterimize uyan tum tdleri listeledik
                            TRDATA.push( tr[j] );
                        }
                    }   
                    // simdi td lerimizi filtreleyip, istedigimiz verileri aktif kapi_no lu otobuse prop olarak ekliyoruz
                    var nodes;
                    // deneyerek buldum hangi veri hangi node da
                    for( var x = 0; x < TRDATA.length; x++ ){
                        nodes = TRDATA[x].childNodes;
                        var trstr = TRDATA[x].innerHTML;
                        ITEM_DATA[ bolge ][OTOBUSLER[bolge][index]].push( JSON.stringify({
                            no: nodes[0].innerText,
                            hat: nodes[1].innerText,
                            servis: nodes[2].innerText,
                            guzergah: nodes[3].innerText,
                            oto: nodes[4].innerText,
                            surucu: nodes[5].innerText,
                            gelis: nodes[6].innerText.trim(),
                            orer: nodes[7].innerText.trim(),
                            amir: nodes[8].innerText.trim(),
                            gidis: nodes[9].innerText.trim(),
                            tahmin: nodes[10].innerText.trim(),
                            bitis: nodes[11].innerText.trim(),
                            sure: trstr.substr( trstr.indexOf('Sefer süresi:') +14 , trstr.indexOf('dk.') - trstr.indexOf('Sefer süresi:') - 14 ),
                            durum: nodes[12].innerText
                        }));
                    }
                    REQUEST_COUNTER++;
                });

            }

            function kaydet_db(){
                if( SYNC_BASLADI){
                    if( REQUEST_COUNTER > 0 && REQUEST_COUNTER == TOTAL_REQ_COUNT ){
                        Form_Data.append("items", JSON.stringify(ITEM_DATA) );
                        console.log( "Veritabanına kaydediliyor..");
                        AHAJAX_V3.req( Base.AJAX_URL + 'filo_senkronizasyon.php', Form_Data, function(res){
                            console.log( "Senkronizasyon tamamlandı.");
                            set_status( "VT Güncel", res.son_saat );
                            //console.log(res.son_unix);
                            //SON_UNIX = res.son_unix;
                            //REFRESH_INTERVAL = setInterval(refresh_kontrol,REFRESH_INTERVAL_FREKANS);
                            IFRAME.src = IFRAME.src;
                            Popup.off();
                        });
                        //console.log(ITEM_DATA);
                        clearInterval( DB_KAYDET_INTERVAL );
                        REQUEST_COUNTER = 0;
                        TOTAL_REQ_COUNT = 0;
                        SYNC_BASLADI = false;
                        set_html($AH('senkronizasyon_container'), "");
                    }
                }
            }




        </script>


    </body>
</html>