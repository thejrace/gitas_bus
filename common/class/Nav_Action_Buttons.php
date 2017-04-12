<?php

	class Nav_Action_Buttons {

		private
			$html = "",
			$def_actions = array(
				Actions::OTOBUS_EKLE => array(
					"title" => "+ Yeni Ekle",
					"url"	=> URL_OTOBUS_EKLE,
					"class"	=> 'orange',
				),
				Actions::STOK_ERISIM => array(
					"title" => "Stok",
					"url"	=> URL_STOK,
					"class"	=> 'main-red'
				),
				Actions::MALZEME_EKLE => array(
					"title" => "+ Malzeme Ekle",
					"url"	=> URL_MALZEME_EKLE,
					"class"	=> 'orange'
				),
				Actions::OTOBUS_DATA_ERISIM => array(
					"title" => "Otobüsler",
					"url"	=> URL_OTOBUSLER,
					"class"	=> 'main-red'
				),
				Actions::OTOBUS_MARKALAR_VERI_ERISIM => array(
					"title" => "Markalar",
					"url"	=> URL_OTOBUS_MARKALAR,
					"class"	=> 'main-red'
				),
				Actions::OTOBUS_MARKA_EKLE => array(
					"title" => "+ Yeni Ekle",
					"url"	=> URL_OTOBUS_MARKA_EKLE,
					"class"	=> 'orange'
				),
				Actions::OTOBUS_MODELLER_VERI_ERISIM => array(
					"title" => "",
					"url"	=> "",
					"class"	=> 'main-red'
				),
				Actions::OTOBUS_MODEL_EKLE => array(
					"title" => "+ Yeni Ekle",
					"url"	=> URL_OTOBUS_MODELLER,
					"class"	=> 'orange'
				),
				Actions::OTOBUS_PARCA_VERI_GIRME => array(
					"title" => "+ Yeni Ekle",
					"url"	=> "",
					"class"	=> 'orange'
				),
				Actions::MALZEME_TIPI_EKLE  => array(
					"title" => "+ Yeni Ekle",
					"url"	=> URL_MALZEME_TIPI_EKLE,
					"class"	=> 'orange'
				),
				Actions::OTOBUS_YAKIT_VERI_GIRME  => array(
					"title" => "+ Yeni Ekle",
					"url"	=> "",
					"class"	=> 'orange'
				),
				Actions::YAKIT_GECMISI_DATA_ERISIM  => array(
					"title" => "",
					"url"	=> "",
					"class"	=> 'main-red'
				),
				Actions::OTOBUS_HATLAR_ERISIM  => array(
					"title" => "Otobüs Hatları",
					"url"	=> URL_OTOBUS_HATLAR,
					"class"	=> 'main-red'
				),
				Actions::OTOBUS_HAT_EKLE => array(
					"title" => "+ Yeni Ekle",
					"url"	=> URL_OTOBUS_HAT_EKLE,
					"class"	=> 'orange'
				),
				Actions::OTOBUS_SEFER_ISTATISTIK_ERISIM => array(
					"title" => "Sefer İstatistikleri",
					"url"	=> URL_OTOBUS_SEFER_ISTATISTIKLERI,
					"class"	=> 'orange'
				)
			),
			$action_keys = array( "title", "url", "class");



		public function __construct( $actions ){

			foreach( $actions as $action ){
				// yetki kontrolu
				if( in_array( $action["action"], Active_User::get_perm_actions() ) ){
					foreach( $this->action_keys as $key ){
						// ozelliklerden girilmeyen varsa default ayarlardan aliyoruz
						if( !isset($action[$key]) ){
							$action[$key] = $this->def_actions[$action["action"]][$key];
						}
					}
					$this->html .= '<a href="'.$action["url"].'" class="navbtn '.$action["class"].'">'.$action["title"].'</a>';
				}
			}
		}


		public function get_buttons(){
			return $this->html;
		}

	}