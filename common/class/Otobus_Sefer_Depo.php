<?php

	class Otobus_Sefer_Depo {

		private $pdo, $kapi_kodu, $table, $sql, $sql_vals, $stats = false, $isset_data = true;
		public $tum_seferler = array();

		public function __construct( $kapi_kodu ){
			$this->pdo = DB::getInstance();
			$this->kapi_kodu = $kapi_kodu;
			$this->table = DBT_ORER_DATA;

		}

		public function isset_data(){
			return $this->isset_data;
		}


		// stat mi filo plan mi aliyoruz 
		public function stats_switch(){
			$this->stats = true;
		}

		// obarey-cb
		// @cbs -> array( 'durum' => array( 'A', 'B', ... ), 'durum_kodu' => array( 'AR' ... ) )
		public function cb_filter( $FILTER, $TFROM, $TTO ){
			$SYNTAX_ARRAY = array();
			$VAL_ARRAY = array();
			$FIN_SQL_ARRAY = array();

			if( empty($FILTER) && !$this->stats ){
				$this->tum_seferler = array();
				return;
			}
			
			// sql key -> val sirasinda önce CBF ler sonra tarih
			// örn. SELECT * FROM orer_kayit WHERE ( hat = ? ) && ( tarih >= ? && tarih <= ? ) ORDER BY tarih DESC, oto, no [ Array ( [0] => 102 [1] => 2016-11-30 [2] => 2016-12-07 )]
			
			$where_flag = false;
			foreach( $FILTER as $db_key => $filter_array ){
				foreach( $filter_array as $f_key  ){
					if( isset( $SYNTAX_ARRAY[ $db_key ]  ) ){
						$SYNTAX_ARRAY[ $db_key ]++;
					} else {
						$SYNTAX_ARRAY[ $db_key ] = 1;
					}
					// sirali ekledigimiz icin val array e key siz ekliyoruz degerleri
					$VAL_ARRAY[] = $f_key;
				}
			}
			foreach( $SYNTAX_ARRAY as $db_key => $count ){
				$psql = ' ( ';
				for( $i = 0; $i < $count; $i++ ){
					$sym = '';
					if( $i != $count - 1 ) $sym = ' || ';
					$psql .= $db_key . ' = ? ' . $sym;
				}
				$psql .= ' ) ';
				$FIN_SQL_ARRAY[] = $psql;
				$where_flag = true;
			}
			


			$tarih_where = "";
			if( $TFROM != ''  ){
				$TSQL = $this->tarih_sql_ayari( $TFROM, $TTO );
				$tarih_where = " ( ".$TSQL[0]." ) ";
				foreach( $TSQL[1] as $val ) $VAL_ARRAY[] = $val;
				$where_flag = true;
			}
			$where = "";
			if( $where_flag || $this->kapi_kodu != 'OBAREY' ) $where = " WHERE ";


			if( $this->kapi_kodu == 'OBAREY'){
				$KOSULLAR = array( implode( ' && ', $FIN_SQL_ARRAY ), $tarih_where );
				foreach( $KOSULLAR as $x => $kosul ) if( $kosul == "" ) unset($KOSULLAR[$x]);
				$sql =  "SELECT * FROM " . $this->table . $where. implode( ' && ', $KOSULLAR ) ." ORDER BY tarih DESC, oto, no";
				$query = $this->pdo->query($sql, $VAL_ARRAY )->results();
			} else {
				$KOSULLAR = array( ' ( oto = ? ) ', implode( ' && ', $FIN_SQL_ARRAY ), $tarih_where );
				foreach( $KOSULLAR as $x => $kosul ) if( $kosul == "" ) unset($KOSULLAR[$x]);
				$sql =  "SELECT * FROM " . $this->table .$where. implode( ' && ', $KOSULLAR ) ." ORDER BY tarih DESC, oto, no";
				$query = $this->pdo->query($sql, array_merge(array( $this->kapi_kodu ), $VAL_ARRAY ))->results();
			}
			// SELECT * FROM orer_kayit WHERE oto = ? && ( tarih = ? ) && ( hat = ? )
			// SELECT * FROM orer_kayit WHERE ( tarih = ? ) && ( hat = ? )

			Session::set('hederoy', $sql );
			Session::set('sikeroy', $VAL_ARRAY );
			if( count($query) > 0 ){ 
				$this->tum_seferler = $query;
			} else {
				$this->isset_data = false;
			}
		}


		// @from -> req baslangic tarihi
		// @to    -> opt bitis tarihi, eger yoksa fromdaki tarihin verilerini çekiyoruz
		public function tarih_sql_ayari( $from, $to ){
			// kriterlere uymayan tarihler girerse bos donduruyoruz
			$sql = "";
			$vals = array();
			if( strlen($from) == 10 ){
				// gunluk [ GET = 2016-12-08 ]
				if( $to != '' && strlen($to) == 10 && $from != $to ){
					// sql: WHERE tarih >= 2016-12-07 && tarih <= 2016-12-09
					$sql = ' tarih >= ? && tarih <= ? '; 
					$vals = array( $from, $to );
				} else {
					// sql: WHERE tarih = 2016-11-01
					$sql = ' tarih = ? ';
					$vals = array( $from );
				}
			} else if( strlen($from) == 7 ){
				// aylik [ GET = 2016-12 ]
				$sql = ' tarih >= ? && tarih <= ? '; 
				if( $to != '' && strlen($to) == 7 && $from != $to  ){
					// seçilen aralıgın verisi [ örn. 2016-09 - 2016-12 ]
					// sql: WHERE tarih >= 2016-11-01 && tarih <= 2016-12-01
					$vals = array( $from."-01", $to."-01" );
				} else {
					// seçilen ayın verisi [ örn. 2016-09 ]
					// sql: WHERE tarih >= 2016-11-01 && tarih <= 2016-11-31
					$vals = array( $from . "-01",  $from . "-31" );
				}
			} else if( strlen($from) == 4 ){
				// yillik [ GET = 2016 ]
				$sql = ' tarih >= ? && tarih <= ? '; 
				if( $to != '' && strlen($to) == 4  && $from != $to ){
					// seçilen aralıgın verisi [ örn. 2015 - 2016 ]
					// sql: WHERE tarih >= 2015-01-01 && tarih <= 2016-12-31
					$vals = array( $from."-01-01", $to."-12-31" );
				} else {
					// seçilen yilin verisi [ örn. 2016 ]
					// sql: WHERE tarih >= 2016-01-01 && tarih <= 2016-12-31
					$vals = array( $from. "-01-01", $from."-12-31" );
				}
			}
			return array( $sql, $vals );
		}


		public function toplam_sure(){
			$sure = 0;
			foreach( $this->tum_seferler as $sefer ){
				if( $sefer['durum'] == "T" ){
					$sure += Sefer_Sure::hesapla( $sefer['gidis'], $sefer['bitis'] );
				}
			}
			return $sure . "<small> DK</small>";
		}
		public function toplam_km(){

			$hatlar = array();

			foreach( $this->tum_seferler as $sefer ){
				if( $sefer['durum'] == 'T' ){
					if( isset($hatlar[$sefer['hat']])){
						$hatlar[$sefer['hat']]++;
					} else {
						$hatlar[$sefer['hat']] = 1;
					}
				}
			}

			$toplam_km = 0;
			foreach( $hatlar as $hat => $sefer_sayisi ){
				$Hat = new Hat( $hat );
				$toplam_km += (int)($Hat->get_details("uzunluk") * $sefer_sayisi / 1000 );
			}

			return $toplam_km;
		}
		public function en_cok_sefer_yapan_otobus_dk(){

			$arr = array();
			foreach( $this->tamamlanan_seferler() as $sefer ){
				$arr[] = $sefer['oto'];
			}

			$temp_count = 0;
			$temp_oto = "Veri Yok";
			$sure = 0;
			/*foreach( array_count_values($arr) as $key => $count ){
				if( $count > $temp_count ) {
					$temp_count = $count;
					$temp_oto = $key;
				}
			}
			$sure = 0;
			foreach( $this->tamamlanan_seferler() as $sefer ){
				if( $sefer['oto'] == $temp_oto ) $sure += $sefer['sure'];
			}*/



			return array( $temp_oto, '( '. $sure . ' DK )' );
		}
		public function en_cok_sefer_yapan_otobus_sefer(){
			$arr = array();
			foreach( $this->tamamlanan_seferler() as $sefer ){
				$arr[] = $sefer['oto'];
			}
			$temp_count = 0;
			$temp_oto = "Veri Yok";
			foreach( array_count_values($arr) as $key => $count ){
				if( $count > $temp_count ) {
					$temp_count = $count;
					$temp_oto = $key;
				}
			}
			return array( $temp_oto, '( '. $temp_count . ' Sefer )' );
		}
		public function sefer_yuzdesi(){
			$payda = ( count($this->planlanan_seferler()) + count($this->ek_seferler()) - count($this->bekleyen_seferler()) - count($this->aktif_seferler()) );
			if( $payda <= 0 ) $payda = 1;
			return "%" . ceil ( count($this->tamamlanan_seferler()) * 100 / $payda );
		}
		public function yarim_seferler(){
			$array = array();
			foreach( $this->tum_seferler as $sefer ){
				if( $sefer['durum'] == "Y" ) $array[] = $sefer;
			}
			return $array;
		}
		public function en_uzun_sefer(){
			$temp_sure = 0;
			foreach( $this->tum_seferler as $sefer ){
				$aktif_sure = Sefer_Sure::hesapla( $sefer['gidis'], $sefer['bitis'] );
				if( $aktif_sure > $temp_sure ){
					$temp_sure = $aktif_sure;
					$temp_hat = $sefer['hat'];
				}
			}
			$HAT = new Hat( $temp_hat );
			return array( $temp_sure, '( Hat: ' . $HAT->get_details('hat') . ' )' );
		}
		public function get_favori_iptal_sebebi(){
			$array = array();
			foreach( $this->tum_seferler as $sefer ){
				if( $sefer['durum'] == 'I' ){
					if( $sefer['durum_kodu'] != "" ){
						$array[] = $sefer['durum_kodu'];
					} 
				}
			}
			$temp_count = 0;
			$temp_kod = 'Veri yok.';
			foreach( array_count_values($array) as $key => $count ){
				if( $count > $temp_count ){
					$temp_count = $count;
					$temp_kod = $key;
				}
			}
			return array( $temp_kod,  '( ' . $temp_count . ' Sefer )' );
		}
		public function en_cok_iptal_olan_hat(){
			$arr = array();
			foreach( $this->iptal_seferler() as $sefer ){
				$arr[] = $sefer['hat'];
			}
			$temp_count = 0;
			foreach( array_count_values($arr) as $key => $count ){
				if( $count > $temp_count ) {
					$temp_count = $count;
					$temp_hat = $key;
				}
			}
			$HAT = new Hat( $temp_hat );
			return array( $HAT->get_details('hat'), '( '. $temp_count . ' Sefer )' );
		}
		public function get_favori_hat(){
			$array = array();
			foreach( $this->tum_seferler as $sefer ){
				$array[] = $sefer['hat'];
			}
			$temp_count = 0;
			foreach( array_count_values($array) as $key => $count ){
				if( $count > $temp_count ) {
					$temp_count = $count;
					$temp_hat = $key;
				}
			}
			$HAT = new Hat( $temp_hat );
			return array( $HAT->get_details('hat'), '( '. $temp_count . ' Sefer )' );
		}
		public function planlanan_seferler(){
			$array = array();
			foreach( $this->tum_seferler as $sefer ){
				if( $sefer['durum_kodu'] != "ES" ) $array[] = $sefer;
			}
			return $array;
		}
		public function iptal_seferler(){
			$array = array();
			foreach( $this->tum_seferler as $sefer ){
				if( $sefer['durum'] == "I" ) $array[] = $sefer;
			}
			return $array;
		}
		public function ek_seferler(){
			$array = array();
			foreach( $this->tum_seferler as $sefer ){
				if( $sefer['durum_kodu'] == "ES" ) $array[] = $sefer;
			}
			return $array;
		}
		public function tamamlanan_seferler(){
			$array = array();
			foreach( $this->tum_seferler as $sefer ){
				if( $sefer['durum'] == "T" ) $array[] = $sefer;
			}
			return $array;
		}
		public function aktif_seferler(){
			$array = array();
			foreach( $this->tum_seferler as $sefer ){
				if( $sefer['durum'] == "A" ) $array[] = $sefer;
			}
			return $array;
		}
		public function bekleyen_seferler(){
			$array = array();
			foreach( $this->tum_seferler as $sefer ){
				if( $sefer['durum'] == "B" ) $array[] = $sefer;
			}
			return $array;
		}
		public function tanimsiz_seferler(){
			$array = array();
			foreach( $this->tum_seferler as $sefer ){
				if( $sefer['durum'] == "EB" ) $array[] = $sefer;
			}
			return $array;
		}
		public function get_tum_seferler(){
			return $this->tum_seferler;
		}

	}