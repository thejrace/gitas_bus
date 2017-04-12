<?php

	class Malzeme_Kaydi {

		private $pdo, $details = array(), $return_text, $table = DBT_OTOBUS_MALZEME_GECMISLERI;

		public function __construct( $id = null ){
			$this->pdo = DB::getInstance();

			if( isset($id) ){
				$query = $this->pdo->query("SELECT * FROM " . $this->table . " WHERE id = ?", array( $id ) )->results();
				if( count($query) == 1 ){
					$this->details = $query[0];
				}
			}
		}
		public function add( $data ){
			$Malzeme = new Malzeme( $data["malzeme_id"] );
			$this->pdo->insert($this->table, array(
				'otobus_id'  => $data['otobus_id'],
				'malzeme_id' => $data['malzeme_id'],
				'adet'		 => $data['adet'],
 				'tarih'		 => Common::get_current_datetime(),
				'aciklama'	 => $data["aciklama"]
			));	

			// kullanılan kadarını stoktan düş
			if( !$Malzeme->adet_guncelle( $data["adet"] ) ) return false;

			Active_User::aktivite_kaydet( array( 'aktivite' => Actions::OTOBUS_PARCA_VERI_GIRME ) );
			
			$this->return_text = "Kayıt eklendi.";
			return true;
		}

		public function delete(){

			if( $this->pdo->query("DELETE FROM " . $this->table . " WHERE id = ?",array($this->details["id"])) ){
				Active_User::aktivite_kaydet( array( 'aktivite' => Actions::OTOBUS_PARCA_KAYIT_SILME ) );
				$this->return_text = 'Kayıt silindi.';
				return true;
			}
			return false;
		}

		public function edit( $data ){
			$this->pdo->query("UPDATE " . $this->table . " SET aciklama = ?, adet = ? WHERE id = ?", array( $data['aciklama'], $data['adet'], $this->details['id']));
			Active_User::aktivite_kaydet( array( 'aktivite' => Actions::OTOBUS_PARCA_KAYIT_DUZENLEME ) );
			$this->return_text = 'Kayıt güncellendi.';
			return true;
		}

		public function get_details( $key = null ){
			if( isset($key) ) return $this->details[$key];
			return $this->details;
		}

		public function get_return_text(){
			return $this->return_text;
		}

	}