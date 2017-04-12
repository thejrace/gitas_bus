<?php
	
	class Malzeme {
		private $pdo, $details = array(), $return_text, $table = DBT_MALZEMELER, $valid = true;
		private $durumlar = array( 0=>'Silinmiş', 1=>'Aktif Stokta', 2=>'Kullanılmış' );


		public function __construct( $id = null ){
			$this->pdo = DB::getInstance();

			if( isset($id) ){
				$query = $this->pdo->query("SELECT * FROM " . $this->table . " WHERE id = ?", array( $id ) )->results();
				if( count($query) == 1 ){
					$this->details = $query[0];
				} else {
					$query = $this->pdo->query("SELECT * FROM " . $this->table . " WHERE stok_kodu = ?", array($id))->results();
					if( count($query) == 1 ){
						$this->details = $query[0];
					} else {
						$this->valid = false;
					}
					
				}
			}
		}



		public function is_valid(){
			return $this->valid;
		}

		public function add( $data ){

			if( $this->pdo->insert($this->table, array(
				"malzeme_tipi" => $data["malzeme_tipi"],
				"marka"		   => $data["marka"],
				"fiyat"		   => $data["fiyat"],
				"aciklama"	   => $data["aciklama"],
				"stok_kodu"	   => $data["stok_kodu"]
			)) ){

				Active_User::aktivite_kaydet( array( 'aktivite' => Actions::MALZEME_EKLE ) );

				$this->return_text = "Malzeme eklendi";
				return true;
			}
			return false;
		}

		public function delete(){
			//$this->pdo->query("DELETE FROM " . $this->table . " WHERE id = ?",array($this->details["id"])
			// malzeme silindiğinde, otobus parca gecmisinde veriyi gorebilmek icin silmiyoruz, durumunu
			// pasif hale getiriyoruz
			if( $this->pdo->query("UPDATE ". $this->table. " SET durum = ? WHERE id = ?", array( 0, $this->details["id"])) ){
				Active_User::aktivite_kaydet( array( 'aktivite' => Actions::MALZEME_SIL ) );
				$this->return_text = 'Malzeme silindi.';
				return true;
			}
			return false;
		}

		public function edit( $data ){
			if( $this->pdo->query("UPDATE " . $this->table . " SET
				malzeme_tipi = ?, 
				marka = ?,
				stok_kodu = ?,
				fiyat = ?,
				aciklama = ?
				WHERE id = ?", array(
					$data["malzeme_tipi"],
					$data["marka"],
					$data["stok_kodu"],
					$data["fiyat"],
					$data["aciklama"],
					$this->details["id"]
				)) ){
				Active_User::aktivite_kaydet( array( 'aktivite' => Actions::MALZEME_DUZENLE ) );
				$this->return_text = "Malzeme düzenlendi.";
				return true;
			}
			return false;
		}

		public function get_details( $key = null ){
			if( isset($key) ) return $this->details[$key];
			return $this->details;
		}

		public function get_return_text(){
			return $this->return_text;
		}

		public function adet_guncelle( $kullanilan ){
			$kalan = $this->details["adet"] - $kullanilan;
			if( $kalan < 0 ){
				return false;
			}
			if( !$this->pdo->query("UPDATE " . $this->table . " SET adet = ? WHERE id = ?", array($kalan, $this->details["id"])) ){
				return false;
			}
			return true;
		}



	}