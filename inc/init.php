<?php

	
	
	// Error output log
	ini_set('error_log', "/home/ahsaphobby.net/httpdocs/bus/error.log");

	require 'defs.php';

	// Otomatik class include
	function autoload_main_classes($class_name){
		$file = CLASS_DIR . $class_name. '.php';
	    if (file_exists($file)) require_once($file);
	}
	spl_autoload_register( 'autoload_main_classes' );

	
	Session::start();

	// perm kontrolleri yapilacak
	if( !Session::exists("login_session") ) {
		$Auto_Login = new Auto_Login;
		if( $Auto_Login->check() ){
			$Login = new Login;
			$Login->auto_action( $Auto_Login->get_user_id() );
		} else {
			if( !isset($LOGIN_PROCESS) ){
				$IFRAME_HIDE = true;
				header("Location: login.php" );
				exit;
			}
			
		}
	}




	class Actions {

		const 	OTOBUS_EKLE 						= 1,
				OTOBUS_DUZENLE 						= 2,
				OTOBUS_SIL 							= 4,
				OTOBUS_YAKIT_VERI_GIRME 			= 5,
				OTOBUS_PARCA_VERI_GIRME 			= 6,
			  	MALZEME_EKLE 						= 7,
			  	MALZEME_DUZENLE 					= 8,
			  	MALZEME_SIL 						= 9,
			  	MALZEME_TIPI_EKLE 					= 10,
			    OTOBUS_DATA_ERISIM 					= 11,
			    STOK_ERISIM 						= 12,
			    YAKIT_GECMISI_DATA_ERISIM 			= 13,
			    PARCA_GECMISI_DATA_ERISIM 			= 14,
			    YAKIT_GIRISI_DUZENLEME 				= 15,
			    OTOBUS_MARKA_EKLE 					= 16,
			    OTOBUS_MODEL_EKLE 					= 17,
			    OTOBUS_MARKALAR_VERI_ERISIM 		= 18,
			    OTOBUS_MODELLER_VERI_ERISIM 		= 19,
			    OTOBUS_MARKA_DUZENLE 				= 20,
			    OTOBUS_MODEL_DUZENLE 				= 21,
			    AYARLAR 							= 22,
			    GIRIS 								= 23,
			    CIKIS 								= 24,
			    KAYIT 								= 25,
			    OTOBUS_PARCA_KAYIT_SILME 			= 26,
			    OTOBUS_PARCA_KAYIT_DUZENLEME 		= 27,
			    OTOBUS_MARKA_SIL					= 28,
			    OTOBUS_MODEL_SIL					= 29,
			    YAKIT_GIRISI_SIL					= 30,
			    OTOBUS_HAT_EKLE						= 31,
			    OTOBUS_HAT_DUZENLE					= 32,
			    OTOBUS_HAT_SIL						= 33,
			    OTOBUS_HATLAR_ERISIM				= 34,
			    FILO_PLAN_ERISIM					= 35,
			    FILO_PLAN_DUZENLEME					= 36,
			    HARITA_TAKIP_ERISIM					= 37,
			    OTOBUS_HAT_GUZERGAH_ERISIM			= 38,
			    OTOBUS_HAT_DURAKLAR_ERISIM			= 39,
			    OTOBUS_HAT_GUZERGAH_DUZENLEME		= 40,
			    OTOBUS_HAT_GUZERGAH_EKLE			= 41,
			    OTOBUS_HAT_DURAKLAR_EKLE			= 42,
			    OTOBUS_HAT_DURAKLAR_SIL				= 43,
			    OTOBUS_HAT_DURAKLAR_DUZENLE			= 44,
			    OTOBUS_HAT_GUZERGAH_SIL				= 45,
			    OTOBUS_SEFER_ISTATISTIK_ERISIM      = 46,
			    SERVIS_KAYDI						= 47;
	}


	class Perm_Level {

		public static $levels = array(
			1 => array(
				"title" => "Admin",
				"actions" => array(
					Actions::OTOBUS_EKLE,
					Actions::OTOBUS_DUZENLE,
					Actions::OTOBUS_SIL,
					Actions::OTOBUS_DATA_ERISIM,
					Actions::OTOBUS_PARCA_VERI_GIRME, 
					Actions::OTOBUS_PARCA_KAYIT_SILME,
					Actions::OTOBUS_PARCA_KAYIT_DUZENLEME,
					Actions::PARCA_GECMISI_DATA_ERISIM,
					Actions::MALZEME_EKLE,
					Actions::MALZEME_DUZENLE,
					Actions::MALZEME_SIL,
					Actions::MALZEME_TIPI_EKLE,
					Actions::STOK_ERISIM,
					Actions::YAKIT_GECMISI_DATA_ERISIM,
					Actions::YAKIT_GIRISI_DUZENLEME,
					Actions::OTOBUS_YAKIT_VERI_GIRME,
					Actions::YAKIT_GIRISI_SIL,
					Actions::OTOBUS_MARKA_EKLE,
					Actions::OTOBUS_MODEL_EKLE,
					Actions::OTOBUS_MARKALAR_VERI_ERISIM,
					Actions::OTOBUS_MODELLER_VERI_ERISIM,
					Actions::OTOBUS_MARKA_DUZENLE,
					Actions::OTOBUS_MODEL_DUZENLE,
					Actions::OTOBUS_MARKA_SIL,
					Actions::OTOBUS_MODEL_SIL,
					Actions::AYARLAR,
					Actions::OTOBUS_HAT_EKLE,
					Actions::OTOBUS_HAT_DUZENLE,
					Actions::OTOBUS_HAT_SIL,
					Actions::OTOBUS_HATLAR_ERISIM,
					Actions::FILO_PLAN_ERISIM,
					Actions::FILO_PLAN_DUZENLEME,
					Actions::HARITA_TAKIP_ERISIM,
					Actions::OTOBUS_HAT_GUZERGAH_ERISIM,
					Actions::OTOBUS_HAT_DURAKLAR_ERISIM,
					Actions::OTOBUS_HAT_GUZERGAH_DUZENLEME,
					Actions::OTOBUS_HAT_GUZERGAH_EKLE,
					Actions::OTOBUS_HAT_DURAKLAR_EKLE,
					Actions::OTOBUS_HAT_DURAKLAR_SIL,
					Actions::OTOBUS_HAT_DURAKLAR_DUZENLE,
					Actions::OTOBUS_SEFER_ISTATISTIK_ERISIM,
					Actions::OTOBUS_HAT_GUZERGAH_SIL,
					Actions::SERVIS_KAYDI

				)
			),
			2 => array( 
				"title" => "Stokçu",
				"actions" => array(
					Actions::OTOBUS_PARCA_VERI_GIRME,
					Actions::STOK_ERISIM,
					Actions::OTOBUS_DATA_ERISIM,
					Actions::PARCA_GECMISI_DATA_ERISIM
				)
			),
			3 => array(
				"title" => "Muhasebeci",
				"actions" => array(
					Actions::MALZEME_EKLE,
					Actions::MALZEME_DUZENLE,
					Actions::MALZEME_SIL,
					Actions::MALZEME_TIPI_EKLE,
					Actions::OTOBUS_DATA_ERISIM,
					Actions::STOK_ERISIM
				)
			)
		);

	}


    // Filo senkronizasyon degiskenler ve kontrol verileri
    $Filo_Senkronizasyon = new Filo_Senkronizasyon;
    define( "ORER_AKTIF_TARIH", $Filo_Senkronizasyon->get_aktif_tarih() );
    // filo tablolar icin hatları al tek tek foreach yapma 
    $hat_query = DB::getInstance()->query("SELECT * FROM " . DBT_HATLAR )->results();
    $TUM_HATLAR = array();
    foreach( $hat_query as $data ){
    	$TUM_HATLAR[ $data['id'] ] = array( 'hat' => $data['hat'], 'aciklama' => $data['aciklama'] ); 
    }