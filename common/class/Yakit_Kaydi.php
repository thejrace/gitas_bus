<?php

	class Yakit_Kaydi {

		private $pdo, $details = array(), $return_text, $table = DBT_OTOBUS_YAKIT_GECMISLERI;

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

			
			$this->pdo->insert($this->table, array(
				'otobus_id' => $data['otobus_id'],
				'fiyat'		=> $data['fiyat'],
				'miktar'	=> $data['miktar'],
				'tarih'		=> Common::date_reverse( $data["tarih"] ),
				'ekleyen'	=> Active_User::get_details('id')
			));
			Active_User::aktivite_kaydet( array( 'aktivite' => Actions::OTOBUS_YAKIT_VERI_GIRME ) );
			$this->return_text = "Kayıt eklendi.";
			return true;
		}

		public function delete(){

			if( $this->pdo->query("DELETE FROM " . $this->table . " WHERE id = ?",array($this->details["id"])) ){
				Active_User::aktivite_kaydet( array( 'aktivite' => Actions::YAKIT_GIRISI_SIL ) );
				$this->return_text = 'Kayıt silindi.';
				return true;
			}
			return false;
		}

		public function edit( $data ){
			$this->pdo->query("UPDATE " . $this->table . " SET fiyat = ?, miktar = ?, tarih = ? WHERE id = ?", array( $data['fiyat'], $data['miktar'], Common::date_reverse( $data["tarih"] ), $this->details['id']));
			Active_User::aktivite_kaydet( array( 'aktivite' => Actions::YAKIT_GIRISI_DUZENLEME ) );
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