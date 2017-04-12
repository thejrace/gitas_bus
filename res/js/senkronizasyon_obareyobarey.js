var Filo_Senkronizasyon = {

	BOLGELER: { A:"dk_oasa", B:"dk_oasb", C:"dk_oasc" },
	REFRESH_INTERVAL_FREKANS: 7000,
	DB_KAYDET_INTERVAL_FREKANS: 1000,
	OTO_REFRESH_FREKANS: 1800, // server la pc nin saat farkından saçmalıyo mal 400 -> 4.25dk
	REQUEST_COUNTER:0,
	TOTAL_REQ_COUNT:0,
	SENKRONIZASYON_YAPILIYOR: false,
	ITEM_DATA: {},
	FORM_DATA: new FormData(),
	STATUS: "",
	DB_KAYDET_INTERVAL:false,
	REFRESH_INTERVAL: false,
	OTOBUSLER: {},
	SON_UNIX:0,
	GUNCELLE_FLAG: false,
	REFRESH_AFTER_CB: false,
	MANUEL_TETIK_FLAG: false,
    HATLAR: [],
	
	INIT: function( otobus_data,  son_unix ){
		this.OTOBUSLER = otobus_data;
		this.SON_UNIX = son_unix;
		this.REFRESH_INTERVAL = setInterval(this.Interval_Kontrol,this.REFRESH_INTERVAL_FREKANS);
	},
	// filo plan izlemede kendimiz manuel yapiyoruz seçili otobüsleri alması ve
	// tablolari guncellemek icin
	Manuel_Tetik: function( otobus_data, frekans, cb ){
		clearInterval( this.REFRESH_INTERVAL );

		this.OTOBUSLER = otobus_data;
		this.OTO_REFRESH_FREKANS = frekans;
		this.SON_UNIX = Math.floor(Date.now()/1000);
		this.MANUEL_TETIK_FLAG = true;
		this.REFRESH_INTERVAL = setInterval(this.Interval_Kontrol,this.REFRESH_INTERVAL_FREKANS);
		this.REFRESH_AFTER_CB = cb;
	},
	ORER_Refresh: function( otobus_data, logtype ){
        console.log("Güncelleniyor" );
        show($AH('header-loader'));
        clearInterval( this.REFRESH_INTERVAL );
        this.OTOBUSLER = otobus_data;
        this.DB_KAYDET_INTERVAL = setInterval(this.DB_Kaydet, this.DB_KAYDET_INTERVAL_FREKANS);
        this.FORM_DATA = new FormData();
        this.ITEM_DATA = {};
        this.SENKRONIZASYON_YAPILIYOR = true;
        this.FORM_DATA.append('type', 'filo_orer_senkronizasyon');
        this.FORM_DATA.append('log_type', logtype );
        // her bolge icin, tum hatlarin bilgileri alinacak
        for( var bolge in this.OTOBUSLER ){
            this.ITEM_DATA[bolge] = {};
            // her hat icin veriyi filodan alip listeye ekliyoruz
            for( var i = 0; i < this.OTOBUSLER[bolge].length; i++ ){
                this.ORER_Request( i, bolge );
                this.TOTAL_REQ_COUNT++;
            }
        }
	},

	ORER_Request: function( index, bolge ){
		var TRDATA = [];
        var status_str = "Bölge: " + bolge + " / " + " KapıNo: " + this.OTOBUSLER[bolge][index];
        //console.log(status_str + " veri isteği yapılıyor..." );
        this.ITEM_DATA[bolge][this.OTOBUSLER[bolge][index]] = [];
        // filoya bolge ve kapi no ile istek yapiyoruz
        AHAJAX_V3_TEXT.req( "http://ahsaphobby.net/otobus/iett/filo_veri_download/request.php", manual_serialize({ type:'filo_orer_guncelle', bolge: bolge, kapi_no:this.OTOBUSLER[bolge][index]}), function(res){
            //console.log( status_str + " veri alındı, işleniyor..");  
            // her istekte rowlari tuttugumuz array i resetliyoruz
            TRDATA = [];
            // gelen veriyi dive aldık islem yapmak icin
            set_html( $AH('senkronizasyon_container'), res );
            // row un altindaki td ler
            var tr = find_elem( $AH('senkronizasyon_container'), "tbody" ).childNodes;
            // her bir td nin tuttugu veriyi alip listeliyoruz
            for( var j = 0; j < tr.length; j++ ){
                // td lerin 3 ayri class i var herhangi birine uyani aliyoruz( text node lari almamak icin )
                if( hasClass( tr[j], "yazid") || hasClass(tr[j], "yazim" )|| hasClass(tr[j], 'yazit') || hasClass(tr[j], "yazik") || hasClass(tr[j], "yazi")){
                // kriterimize uyan tum tdleri listeledik
                    TRDATA.push( tr[j] );
                }
            }   
            // console.log(res);
            // simdi td lerimizi filtreleyip, istedigimiz verileri aktif kapi_no lu otobuse prop olarak ekliyoruz
            var nodes;
            if( TRDATA.length > 0 ) {
                // deneyerek buldum hangi veri hangi node da
                for( var x = 0; x < TRDATA.length; x++ ){
                    nodes = TRDATA[x].childNodes;
                    var trstr = TRDATA[x].innerHTML;
                    var hat = nodes[1].innerText.trim();
                    if( hat.indexOf( "*" ) > -1 ){
                    	hat = hat.substr(1);
                    	hat = hat.substr( 0, hat.indexOf("*") );
                    } else if( hat.indexOf("!") > -1 ){
                    	hat = hat.substr( 1);
                    	hat = hat.substr( 0, hat.indexOf("!") );
                    } else if( hat.indexOf("#") > -1 ){
                    	hat = hat.substr(1);
                    	hat = hat.substr( 0, hat.indexOf("#") );
                    }

                    var guzergah = nodes[3].innerText.trim();
                    if( guzergah.indexOf(" ") > -1 ) guzergah = guzergah.substr( 0, guzergah.indexOf(" ") );
                    Filo_Senkronizasyon.ITEM_DATA[ bolge ][Filo_Senkronizasyon.OTOBUSLER[bolge][index]].push( JSON.stringify({
                        no: nodes[0].innerText.trim(),
                        hat: hat,
                        servis: nodes[2].innerText.trim(),
                        guzergah: guzergah,
                        oto: nodes[4].innerText.trim().substr( 2 ),
                        surucu: nodes[5].innerText.trim().substr(1),
                        gelis: nodes[6].innerText.trim(),
                        orer: nodes[7].innerText.trim(),
                        amir: nodes[8].innerText.trim(),
                        gidis: nodes[9].innerText.trim(),
                        tahmin: nodes[10].innerText.trim(),
                        bitis: nodes[11].innerText.trim(),
                        durum_kodu: nodes[13].innerText.substr( 6 ).trim(),
                        sure: trstr.substr( trstr.indexOf('Sefer süresi:') +14 , trstr.indexOf('dk.') - trstr.indexOf('Sefer süresi:') - 14 ).trim(),
                        durum: nodes[12].innerText.trim()
                    }));
                }
            } else {
                Filo_Senkronizasyon.ITEM_DATA[ bolge ][Filo_Senkronizasyon.OTOBUSLER[bolge][index]] = "BOS";
            }
            Filo_Senkronizasyon.REQUEST_COUNTER++;
        });
	},

	ORER_Sefer_Takip: function( kapi_no, cb ){
		show($AH('header-loader'));
		var TRDATA = [],
			bolge = this.BOLGELER[kapi_no.substr(0, 1)],
        	status_str = "Bölge: " + bolge + " / " + " KapıNo: " + kapi_no;
        //console.log(status_str + " veri isteği yapılıyor..." );
        // filoya bolge ve kapi no ile istek yapiyoruz
        AHAJAX_V3_TEXT.req( "http://ahsaphobby.net/otobus/iett/filo_veri_download/request.php", manual_serialize({ type:'filo_orer_guncelle', bolge: bolge, kapi_no : kapi_no }), function(res){
            //console.log( status_str + " veri alındı, işleniyor.." );  
            // her istekte rowlari tuttugumuz array i resetliyoruz
            TRDATA = [];
            // gelen veriyi dive aldık islem yapmak icin
            set_html( $AH('senkronizasyon_container'), res );
            // row un altindaki td ler
            var tr = find_elem( $AH('senkronizasyon_container'), "tbody" ).childNodes;

            if( tr != undefined ){
                // her bir td nin tuttugu veriyi alip listeliyoruz
                for( var j = 0; j < tr.length; j++ ){
                    // td lerin 3 ayri class i var herhangi birine uyani aliyoruz( text node lari almamak icin )
                    if( hasClass( tr[j], "yazid") || hasClass(tr[j], "yazim" )|| hasClass(tr[j], "yazik") || hasClass(tr[j], "yazi")){
                    // kriterimize uyan tum tdleri listeledik
                        TRDATA.push( tr[j] );
                    }
                }   
                //console.log(res);
                // simdi td lerimizi filtreleyip, istedigimiz verileri aktif kapi_no lu otobuse prop olarak ekliyoruz
                var nodes;
                if( TRDATA.length > 0 ) {
                    // deneyerek buldum hangi veri hangi node da
                    for( var x = 0; x < TRDATA.length; x++ ){
                        nodes = TRDATA[x].childNodes;
                        if( nodes[12].innerText != 'A' ) continue;
                        if( nodes[3].innerHTML.indexOf('Durak izdusumu') > -1 ){
                            var durak_izdusumu = nodes[3].innerHTML.substr( nodes[3].innerHTML.indexOf('Durak izdusumu: ') + 16, nodes[3].innerHTML.indexOf(' ve ilk Durak') - nodes[3].innerHTML.indexOf('Durak izdusumu: ') - 16 );
                        }
                        if( nodes[7].innerHTML.indexOf( 'Sonraki Sefer Saati') > -1 ){
                            var beklenen_bitis = "Beklenen ortalama tamamlama saati: " + nodes[7].innerHTML.substr( nodes[7].innerHTML.indexOf('Beklenen ortalama tamamlama saati  :') +36 , nodes[7].innerHTML.indexOf('Beklenen ortalama tamamlama saati  :') +41 - nodes[7].innerHTML.indexOf('Beklenen ortalama tamamlama saati  :') -36 );
                        }
                    }
               }
               if( durak_izdusumu == undefined ) durak_izdusumu = "Veri yok.";
               if( beklenen_bitis == undefined ) beklenen_bitis = "Veri yok.";
               console.log( kapi_no +' Durak İzdüşüm : ' + durak_izdusumu );
               cb( durak_izdusumu, beklenen_bitis );
               hide($AH('header-loader'));
            }
        });
	},
	ORER_Harita_Takip_Refresh: function( kapi_nolar ){
		

	},
	ORER_Harita_Takip_Request: function(){

	},
	DB_Kaydet: function(){
		
		 if( Filo_Senkronizasyon.SENKRONIZASYON_YAPILIYOR ){
            if( Filo_Senkronizasyon.REQUEST_COUNTER > 0 && Filo_Senkronizasyon.REQUEST_COUNTER == Filo_Senkronizasyon.TOTAL_REQ_COUNT ){
                Filo_Senkronizasyon.FORM_DATA.append("items", JSON.stringify( Filo_Senkronizasyon.ITEM_DATA) );
                console.log( "Veritabanına kaydediliyor..");
                AHAJAX_V3.req( Base.AJAX_URL + 'filo_senkronizasyon.php', Filo_Senkronizasyon.FORM_DATA, function(res){
                    //console.log( "Senkronizasyon tamamlandı.");
                    console.log( "VT Güncel");
                    console.log(res);

                    // tetik manuelsi son unix an olacak
                    if( Filo_Senkronizasyon.MANUEL_TETIK_FLAG ) {
                    	Filo_Senkronizasyon.SON_UNIX = Math.floor(Date.now()/1000);
                    } else {
                    	Filo_Senkronizasyon.SON_UNIX = res.son_unix;
                    }
                    Filo_Senkronizasyon.REFRESH_INTERVAL = setInterval(Filo_Senkronizasyon.Interval_Kontrol,Filo_Senkronizasyon.REFRESH_INTERVAL_FREKANS);
                    //css($AH('guncelle'), { display:'inline-block'});
                    //location.reload();
                    hide($AH('header-loader'));
                    if( typeof Filo_Senkronizasyon.REFRESH_AFTER_CB === 'function' ) Filo_Senkronizasyon.REFRESH_AFTER_CB(Filo_Senkronizasyon.ITEM_DATA);
                });
                console.log(Filo_Senkronizasyon.ITEM_DATA);
                clearInterval( Filo_Senkronizasyon.DB_KAYDET_INTERVAL );
                Filo_Senkronizasyon.REQUEST_COUNTER = 0;
                Filo_Senkronizasyon.TOTAL_REQ_COUNT = 0;
                Filo_Senkronizasyon.SENKRONIZASYON_YAPILIYOR = false;
                set_html($AH('senkronizasyon_container'), "");
            }
        }
	},
	Interval_Kontrol: function(){
		console.log('Güncelleme kontrolü ( Kalan süre : ' + ( Filo_Senkronizasyon.OTO_REFRESH_FREKANS - (Math.floor(Date.now()/1000) - Filo_Senkronizasyon.SON_UNIX) ) + ' saniye )');
        if( Math.floor(Date.now()/1000) - Filo_Senkronizasyon.SON_UNIX >= Filo_Senkronizasyon.OTO_REFRESH_FREKANS ) Filo_Senkronizasyon.ORER_Refresh( Filo_Senkronizasyon.OTOBUSLER, 'komple' ); 
	}


}