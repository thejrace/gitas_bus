<?php

    require '../inc/init.php';

    if( $_POST ){

        // ajax output degiskenleri
        $OK    = 1;
        $TEXT  = "";
        $input_output = array();
        $DATA = array();

        switch( $_POST['type'] ){

            /*

                
                Filodan verileri çektik, her otobus icin kontrol ederken;
                    - bitmemiş seferi varsa, normal; eski veriyi silip yenisini ekliyoruz
                    - eger tum seferleri bitmisse artik guncellemiyoruz

                --- gece 4 ten sonra verileri yeni güne atıyacağı için, filo yeni verileri ekleyene kadar
                bir önceki günün verileri, geçerli gün için aktif gözükecek bir süre, ama filo yeni
                verileri eklediginde seferlerin durumlarda A,B olacagı icin eski datanın üzerine yazılacak yeni ve doğru veri

                    bu sistemle bir gün için tüm seferlerini tamamlamış bir otobüsün verilerini hiçbir şekilde değiştiremiyoruz ve kalıcı oluyor
                    kayıplardan kurtulmak için en iyi cozum su anda bu


                
    

            */

            case 'filo_orer_senkronizasyon':
                class Filo_Data_Guncelle {
                    private $pdo, $table = DBT_ORER_DATA, $kapi_no;
                    public function __construct( $kapi_no ){
                        $this->pdo = DB::getInstance();
                        $this->kapi_no = $kapi_no;
                    }

                 

                    // her otobus icin
                    public function action( $yeni_data, $AKTIF_TARIH ){
                        if( $yeni_data != "BOS" ){
                            $seferler_ok = true;
                            $ilk_insert = false;
                            $eski_data = $this->pdo->query("SELECT * FROM " . $this->table . " WHERE oto = ? && tarih = ?", array( $this->kapi_no, $AKTIF_TARIH ) )->results();
                            if( count($eski_data) > 0 ){
                                foreach( $eski_data as $kayit ){
                                    // bir tane bile uymazsa demek hala sefer var o yuzden asagida guncelleme yapicaz 
                                    if( $kayit['durum'] == 'A' || $kayit['durum'] == 'B' ) $seferler_ok = false;
                                }

                                // sabah tüm seferleri arıza girilmiş bi otobüs vardı
                                // aksama dogru duzelttiler fakat seferler_ok kontrolu yaparken yalnızca yukarıdaki kontrolu
                                // yapınca yeni verileri alamiyodu.
                                // o yuzden eski verileri + yeni verileri kontrol ediyoruz seferlerin bitip bitmedigine karar verirken
                                foreach( $yeni_data as $data ){
                                    $ITEM = json_decode($data);
                                    if( $ITEM->durum == 'A' || $ITEM->durum == 'B' ){
                                        $seferler_ok = false;
                                        break;
                                    }
                                }

                            } else {
                                // eski data yoksa seferler tamamlanmamis gibi yapip yeni veriyi alicaz
                                // $seferler_ok = false;
                                $ilk_insert = true;
                            }

                            if( $ilk_insert ){
                                $otobus_hat_guncelle = false;


                                foreach( $yeni_data as $data ){
                                    $ITEM = json_decode($data);

                                    $Sofor = new Sofor( $ITEM->surucu );
                                    if( !$Sofor->is_ok() ){
                                        if( $ITEM->surucu != "" ){
                                            $Sofor->add( array( 'isim' => $ITEM->surucu, 'sicil_no' => $ITEM->surucu_sicil_no, 'telefon' => $ITEM->surucu_tel ) );
                                            $Sofor = new Sofor( $ITEM->surucu );
                                            $sofor_id = $Sofor->get_details('id');
                                        } else {
                                            $sofor_id = 5; // belirsiz sürücü
                                        }
                                    } else {
                                        $sofor_id = $Sofor->get_details('id');
                                    }

                                    $hat_id_query = $this->pdo->query("SELECT * FROM ".DBT_HATLAR." WHERE hat = ?", array( $ITEM->hat ) )->results();
                                    $hat_id = $hat_id_query[0]['id'];
                                    // otobuse hatti ekle
                                    if( !$otobus_hat_guncelle ){
                                        $hat = $this->pdo->query("SELECT * FROM ".DBT_HATLAR." WHERE hat = ?", array( $ITEM->hat ) )->results();
                                        $this->pdo->query("UPDATE ".DBT_OTOBUSLER." SET hat = ? WHERE kod = ?", array( $hat[0]['id'], $ITEM->oto ) );
                                        $otobus_hat_guncelle = true;
                                    }

                                    $this->pdo->insert( $this->table, array( 
                                        'no'            => $ITEM->no,
                                        'hat'           => $hat_id,
                                        'servis'        => $ITEM->servis,
                                        'guzergah'      => $ITEM->guzergah,
                                        'oto'           => $ITEM->oto,
                                        'surucu'        => $sofor_id,
                                        'gelis'         => $ITEM->gelis,
                                        'orer'          => $ITEM->orer,
                                        'amir'          => $ITEM->amir,
                                        'gidis'         => $ITEM->gidis,
                                        'tahmin'        => $ITEM->tahmin,
                                        'bitis'         => $ITEM->bitis,
                                        'durum'         => $ITEM->durum,
                                        'sure'          => $ITEM->sure,
                                        'durum_kodu'    => $ITEM->durum_kodu,
                                        'tarih'         => $AKTIF_TARIH
                                    ));

                                }

                            }

                            // seferler tamamlanmamissa normal islemleri yapiyoruz
                            if( !$seferler_ok ){
                               


                                // eski verileri sil yenileriyle degistirmek için
                                // foreach( $eski_data as $kayit ){
                                    // if( $kayit['durum'] != 'T' ){
                                        // $this->pdo->query("DELETE FROM " . $this->table . " WHERE id = ?", array( $kayit['id'])); 
                                    // }
                                    
                                // }
                                $otobus_hat_guncelle = false;
                                // yeni verileri ekliyoruz
                                foreach( $yeni_data as $data ){
                                    $ITEM = json_decode($data);

                                    // if( $ITEM->durum == 'T' ) continue;

                                    $Sofor = new Sofor( $ITEM->surucu );
                                    if( !$Sofor->is_ok() ){
                                        if( $ITEM->surucu != "" ){
                                            $Sofor->add( array( 'isim' => $ITEM->surucu, 'sicil_no' => $ITEM->surucu_sicil_no, 'telefon' => $ITEM->surucu_tel ) );
                                            $Sofor = new Sofor( $ITEM->surucu );
                                            $sofor_id = $Sofor->get_details('id');
                                        } else {
                                            $sofor_id = 5; // belirsiz sürücü
                                        }
                                    } else {
                                        $sofor_id = $Sofor->get_details('id');
                                    }

                                    

                                    $hat_id_query = $this->pdo->query("SELECT * FROM ".DBT_HATLAR." WHERE hat = ?", array( $ITEM->hat ) )->results();
                                    $hat_id = $hat_id_query[0]['id'];
                                    // otobuse hatti ekle
                                    if( !$otobus_hat_guncelle ){
                                        $hat = $this->pdo->query("SELECT * FROM ".DBT_HATLAR." WHERE hat = ?", array( $ITEM->hat ) )->results();
                                        $this->pdo->query("UPDATE ".DBT_OTOBUSLER." SET hat = ? WHERE kod = ?", array( $hat[0]['id'], $ITEM->oto ) );
                                        $otobus_hat_guncelle = true;
                                    }

                                    $eski_veri = array();
                                    foreach( $eski_data as $eski ){
                                        if( $eski['no'] == $ITEM->no ){
                                            $eski_veri = $eski;
                                            break;
                                        }

                                    }

                                    if( !isset($eski_veri['durum']) || !isset($eski_veri['durum_kodu'])) continue;                    

                                    if( $eski_veri['durum'] != $ITEM->durum || $eski_veri['durum_kodu'] != $ITEM->durum_kodu ){

                                        $this->pdo->query("UPDATE " . $this->table . " SET 
                                            no = ?,
                                            hat = ?,
                                            servis = ?,
                                            guzergah = ?,
                                            surucu = ?,
                                            gelis = ?,
                                            orer = ?,
                                            amir = ?,
                                            gidis = ?,
                                            tahmin = ?,
                                            bitis = ?,
                                            durum = ?,
                                            sure = ?,
                                            durum_kodu = ? WHERE ( oto = ? && no = ? && tarih = ? )",
                                            array(
                                                $ITEM->no,
                                                $hat_id,
                                                $ITEM->servis,
                                                $ITEM->guzergah,
                                                $sofor_id, 
                                                $ITEM->gelis,
                                                $ITEM->orer,
                                                $ITEM->amir,
                                                $ITEM->gidis,
                                                $ITEM->tahmin,
                                                $ITEM->bitis,
                                                $ITEM->durum,
                                                $ITEM->sure,
                                                $ITEM->durum_kodu,
                                                $ITEM->oto,
                                                $ITEM->no,
                                                $AKTIF_TARIH
                                            )
                                        );
                                    }
                                   
                                }
                            } // seferler ok
                        } // yeni data BOS
                    } // metod

                } // class

                // timestamp al
                $SIMDI_UNIX = time();
                $SIMDI_SAAT = date("H:i:s");
                // aktif tarih kontrolu yap
                // $Filo_Senkronizasyon = new Filo_Senkronizasyon;
                $AKTIF_TARIH = $Filo_Senkronizasyon->get_aktif_tarih();
                // verileri ekle
                foreach( json_decode( $_POST['items'] ) as $bolge ){
                    foreach( $bolge as $otobus => $dataset ){
                        $Filo_Data_Guncelle = new Filo_Data_Guncelle( $otobus );
                        $Filo_Data_Guncelle->action( $dataset, $AKTIF_TARIH  );
                    }
                }
                // komple guncelleme varsa aktif kaydı guncelle
                if( $_POST['log_type'] == 'komple' ) $Filo_Senkronizasyon->kayit_guncelle( $SIMDI_SAAT, $SIMDI_UNIX, $AKTIF_TARIH );  
                

            break;


        }
   

        $output = json_encode(array(
            "ok"           => $OK,           // istek tamam mi
            "text"         => $TEXT,         // bildirim
            "data"         => array(),
            "son_saat"     => $SIMDI_SAAT,
            "son_unix"     => $SIMDI_UNIX,
            'aktif_tarih'  => $AKTIF_TARIH,
            "oh"           => $_POST
        ));

        echo $output;
        die;

    }

  