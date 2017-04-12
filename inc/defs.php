<?php

	define("DB_NAME", "bus");
	define("DB_PASS", "WAzzabii308");
	define("DB_IP", "94.73.170.238");

	define("APP_VERSION", "v0.1");


	// define("MAIN_DIR", "/home/ahsaphobby.net/httpdocs/granit/");
	// define("MAIN_DIR", realpath(dirname(__FILE__)));
	define("MAIN_DIR", $_SERVER["DOCUMENT_ROOT"] . "/bus/");
	define("COMMON_DIR", MAIN_DIR . "common/");
	define("CLASS_DIR", COMMON_DIR . "class/");

	define("MAIN_URL", "http://ahsaphobby.net/bus/");
	define("URL_LOGIN", MAIN_URL . "Giris");

	define( "DBT_KULLANICILAR", "kullanicilar" );
	define( "DBT_MALZEMELER", "malzemeler" );
	define( "DBT_MALZEME_TIPLERI", "malzeme_tipleri" );
	// define( "DBT_OTOBUSLER", "otobusler" );
	define( "DBT_OTOBUSLER", "otobusler_v2" );
	define( "DBT_OTOBUS_MARKALARI", "otobus_markalar" );
	define( "DBT_OTOBUS_MODELLER", "otobus_modeller" );
	define( "DBT_OTOBUS_YAKIT_GECMISLERI", "otobus_yakit_gecmisleri" );
	define( "DBT_OTOBUS_MALZEME_GECMISLERI", "otobus_malzeme_gecmisleri" );
	define( "DBT_AUTH_TOKENS", "auth_tokens" );
	define( "DBT_AKTIVITELER", "aktivite_kayit" );
	define( "DBT_HATLAR", "otobus_hatlar" );
	define( "DBT_OTOBUS_SAHIPLERI", "otobus_sahipleri" );
	define( "DBT_SOFORLER", "soforler" );
	define( "DBT_FILO_OTOBUS_MESAJLAR", "filo_otobus_mesajlar" );

	define( "DBT_ORER_DATA", "orer_kayit" );
	define( "DBT_ORER_TARIH_LOG", "orer_tarih_log" );

	define( "DBT_HAT_MERKEZ_KOORDINATLAR", "hat_harita_merkez_koordinatlari" );
	define( "DBT_HAT_GUZERGAH_KOORDINATLAR", "hat_guzergah_koordinatlari" );
	define( "DBT_HAT_DURAKLAR", "hat_duraklar" );

	define( "URL_OTOBUSLER", MAIN_URL . 'Otobusler' );
	define( "URL_OTOBUS_EKLE", MAIN_URL . 'OtobusEkle' );
	define( "URL_OTOBUS_DUZENLE", MAIN_URL . 'OtobusDuzenle/' );

	define( "URL_OTOBUS_HAT_DUZENLE", MAIN_URL . 'OtobusHatDuzenle/' );
	define( "URL_OTOBUS_HAT_EKLE", MAIN_URL . 'OtobusHatEkle' );
	define( "URL_OTOBUS_HATLAR", MAIN_URL . 'OtobusHatlar' );

	define( "URL_HAT_GUZERGAH", MAIN_URL . 'HatGuzergah/' );
	define( "URL_HAT_DURAKLAR", MAIN_URL . 'HatDuraklar/' );


	define( "URL_FILO_TAKIP", MAIN_URL . 'OtobusCokluFiloPlan' );
	define( "URL_HARITA_TAKIP", MAIN_URL . 'OtobusHaritaTakip/' );
	define( "URL_OTOBUS_SEFER_ISTATISTIKLERI", MAIN_URL . 'OtobusSeferIstatistikleri/' );
	define( "URL_FILO_ISTATISTIKLER", MAIN_URL . 'OtobusSeferIstatistikleri/' );




	define( "URL_STOK", MAIN_URL . 'Stok' );
	define( "URL_PARCA_GECMISI", MAIN_URL . 'OtobusParcaGecmisi/' );
	define( "URL_PARCA_KAYDI_EKLE", MAIN_URL . 'OtobusParcaKaydiEkle/' );
	// define( "URL_PARCA_KAYDI_DUZENLE", MAIN_URL . 'OtobusParcaKaydiDuzenle' );
	define( "URL_YAKIT_KAYDI_EKLE", MAIN_URL . 'OtobusYakitKaydiEkle/' );
	define( "URL_YAKIT_KAYDI_DUZENLE", MAIN_URL . 'OtobusYakitKaydiDuzenle/' );
	define( "URL_YAKIT_GECMISI", MAIN_URL . 'OtobusYakitGecmisi/' );
	define( "URL_MALZEME_EKLE", MAIN_URL . 'MalzemeEkle' );
	define( "URL_MALZEME_DUZENLE", MAIN_URL . 'MalzemeDuzenle/' );
	define( "URL_MALZEME_TIPI_EKLE", MAIN_URL . 'MalzemeTipiEkle' );
	define( "URL_OTOBUS_MARKA_EKLE", MAIN_URL . 'OtobusMarkaEkle' );
	define( "URL_OTOBUS_MARKALAR", MAIN_URL . 'OtobusMarkalar' );
	define( "URL_OTOBUS_MARKA_DUZENLE", MAIN_URL . 'OtobusMarkaDuzenle/' );
	define( "URL_OTOBUS_MODEL_EKLE", MAIN_URL . 'OtobusModelEkle/' );
	define( "URL_OTOBUS_MODELLER", MAIN_URL . 'OtobusModeller/' );
	define( "URL_OTOBUS_MODEL_DUZENLE", MAIN_URL . 'OtobusModelDuzenle/' );
	define( "URL_OTOBUS_FILO_PLAN", MAIN_URL . 'OtobusFiloPlan/' );


	define( "URL_LOGOUT", MAIN_URL . 'CikisYap' );
	define( "URL_AYARLAR", MAIN_URL . 'Ayarlar' );


	define( "DIR_RES", MAIN_DIR . "res/"  );
	
	define( "DIR_RES_IMG", DIR_RES . "img/" );

	define( "URL_RES", MAIN_URL . "res/" );
	define( "URL_RES_IMG", URL_RES . "img/" );
	define( "URL_RES_CSS", URL_RES . "css/" );
	define( "URL_RES_JS", URL_RES . "js/" );