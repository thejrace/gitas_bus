<?php
    class Filo_Senkronizasyon {

        private $pdo, $table = DBT_ORER_TARIH_LOG, $frekans = 1000, $guncellenme = "", $aktif_tarih = "BEKLEMEDE", $son_unix = 0, $otobusler = array(), $bolgeler = array( "A" => "dk_oasa", "B" => "dk_oasb", "C" => "dk_oasc");

        public function __construct(){
            $this->pdo = DB::getInstance();

            $aktif_gun_query = $this->pdo->query("SELECT * FROM orer_gecerlilik WHERE durum = ?", array( 1 ) )->results();
            $this->aktif_tarih = $aktif_gun_query[0]['tarih'];
        }
        
        public function get_otobusler(){
            return $this->otobusler;
        }

        public function kayit_guncelle( $simdi_saat, $simdi_unix, $aktif_tarih){
            $this->pdo->query("UPDATE ".$this->table." SET guncellenme = ?, guncellenme_unix = ? WHERE tarih = ?", array( $simdi_saat, $simdi_unix, $aktif_tarih ));
        }

        // bir onceki gunun kaydinin gecerliligi bitmisse o tarihli orer kayitlari guncellemeyi durduruyoruz
        // bir sonraki gunun gecerlilik ayarini iste saat 06:00 da baslaticaz boylece bir onceki gunu verisi
        // yeni gune gecmeyecek
        public function gecerlilik_ayari_yap( $son_saat ){
            $aktif_gun_query = $this->pdo->query("SELECT * FROM orer_gecerlilik WHERE durum = ?", array( 1 ) )->results();
            if( count($aktif_gun_query) == 1 ){
                // onceki gun kayit alinmis
                // onceki gunu gecerlilik bitmisse
                if( $aktif_gun_query[0]['gecerlilik'] < time() ){
                     // onceki gunun kaydini 0 yap
                    $this->pdo->query("UPDATE orer_gecerlilik SET durum = ? WHERE id = ?", array( 0, $aktif_gun_query[0]['id'] ) );
                    // eger saat sabah 6 olmussa yeni gunun gecerliligini hesapla ( yeni veriyle )
                    if( $aktif_gun_query[0]['sonraki_gun_hesaplama'] < time() ){
                        // yeni gunun kaydini ekle
                        $this->pdo->insert( "orer_gecerlilik", array(
                            'tarih'                 => Common::get_current_date(),
                            'son_orer'              => $son_saat,
                            'gecerlilik'            => strtotime("+1 day ".$son_saat . ":00" ),
                            'sonraki_gun_hesaplama' => strtotime("+1 day 05:00"),
                            'durum'                 => 1,
                            'son_guncellenme'       => Common::get_current_datetime()
                        ));

                        // aktif_tarihi al
                        $this->aktif_tarih = Common::get_current_date();
                    } else {
                        // yeni gun hesaplama saati gelmemisse BEKLEMEDE yapiyorum tarihi
                        // senkron.php de bunu gorunce guncelleme yapmiyor
                        $this->aktif_tarih = 'BEKLEMEDE';
                    }
                } else {
                    // aktif gunun gecerliligi devam ediyorsa onun tarihini aliyoruz
                    $this->aktif_tarih = $aktif_gun_query[0]['tarih'];
                }
            } else {
                // onceki gun kaydi yok
                $this->pdo->insert( 'orer_gecerlilik', array(
                    'tarih'                 => Common::get_current_date(),
                    'son_orer'              => $son_saat,
                    'gecerlilik'            => strtotime("+1 day " . $son_saat . ":00" ),
                    'sonraki_gun_hesaplama' => strtotime("+1 day 05:00"),
                    'durum'                 => 1,
                    'son_guncellenme'       => Common::get_current_datetime()
                ));
                 $this->aktif_tarih = Common::get_current_date();
            }
        }

        public function get_aktif_tarih(){
            return $this->aktif_tarih;
        }

        // senk kayitta yapiyoruz bunu
        public function get_aktif_tarih_old(){
            $aktif_gun_query = $this->pdo->query("SELECT * FROM ".$this->table." WHERE durum = ?", array(1))->results();
            if( count($aktif_gun_query) > 0 ){
                // eger gecerlilik yani son guncellenen günün ertesi gününün 04:00 saati geçmişse
                if( $aktif_gun_query[0]['gecerlilik'] < time() ){
                    // bugunun tarihide yeni bir log ekliyoruz
                    // eski aktif logun durumunu 0 yapiyoruz
                    $this->pdo->query("UPDATE ". $this->table ." SET durum = 0 WHERE id = ?", array( $aktif_gun_query[0]['id']));

                    // yeni log ekle
                    $this->pdo->insert($this->table, array(
                        'tarih'            => Common::get_current_date(),
                        'gecerlilik'       => strtotime("+1 day 05:00"), // ertesi gun 06:00 e kadar
                        'durum'            => 1,
                        'guncellenme'      => date("H:i:s"),
                        'guncellenme_unix' => time()
                    ));
                    //yeni tarih
                    $AKTIF_TARIH = Common::get_current_date();
                } else {
                    // gecerlilik devam ediyorsa timestamp aktif logun tarihi olacak
                    $AKTIF_TARIH = $aktif_gun_query[0]['tarih'];
                }
            } else {
                // ilk ekleme -- yapmama gerek yok ama yeni sistem kurarken rahatlık olur
                $this->pdo->insert($this->table, array(
                    'tarih'      => Common::get_current_date(),
                    'gecerlilik' => strtotime("+1 day 05:00"), // ertesi gun 04:00 e kadar
                    'durum'      => 1,
                    'guncellenme'=> date("H:i:s"),
                    'guncellenme_unix' => time()
                ));
                $AKTIF_TARIH = Common::get_current_date();
            }
            return $AKTIF_TARIH;
        }

        public function get_guncellenme(){
            return $this->guncellenme;
        }

        public function get_orer_frekans(){
            return $this->frekans;
        }   

        public function get_son_guncellenme_unix(){
            return $this->son_unix;
        }

    }