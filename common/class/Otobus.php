<?php

	
	class Otobus {

		private $pdo, $details = array(), $return_text, $table = DBT_OTOBUSLER, $exists = true;

		public function __construct( $id = null ){

			$this->pdo = DB::getInstance();

			if( isset($id) ){
				$query = $this->pdo->query("SELECT * FROM " . $this->table . " WHERE id = ?", array( $id ) )->results();
				if( count($query) == 1 ){
					$this->details = $query[0];
				} else {
					// kapi kodundan bulma
					$query = $this->pdo->query("SELECT * FROM " . $this->table . " WHERE kod = ?", array($id))->results();
					if( count($query) ==  1 ){
						$this->details = $query[0];
					} else {
						$this->exists = false;
					}
				}
			}

		}

		public function is_valid(){
			return $this->exists;
		}

		public function get_hat_no(){
			if( $this->details["hat"] == 0 || $this->details["hat"] == NULL ){
				$this->details["hat"] = "Hat Atanmamış";
			} else {
				$Hat = new Hat( $this->details["hat"] );
				$this->details["hat"] = $Hat->get_details("hat");
			}
			
		}

		public function get_sahip_isim(){

			if( $this->details['sahip'] == 0 ){
				$this->details['sahip'] = 'Sahip Yok';
			} else {
				$Sahip = new Otobus_Sahibi( $this->details['sahip'] );
				$this->details['sahip'] = $Sahip->get_details('isim');
			}

		}

		public function add( $input ){
			$Marka = new Otobus_Markasi( $input['marka'] );
			$this->pdo->insert( $this->table, array(

				'kod' 			 	=> $input['kod'],
				'plaka' 		 	=> $input['plaka'],
				'tip' 			 	=> $input['tip'],
				'ogs' 			 	=> $input['ogs'],
				'aciklama' 		 	=> $input['aciklama'],
				'sahip' 		 	=> $input['sahip'],
				'plaka_aciklama' 	=> $input['plaka_aciklama'],
				'marka' 			=> $Marka->get_details('marka'),
				'model' 			=> $Marka->get_model_name( $input['model'] ),
				'model_yili' 		=> $input['model_yili'],
				'hat' 				=> $input['hat']
			));
			Active_User::aktivite_kaydet( array( 'aktivite' => Actions::OTOBUS_EKLE ) );
			$this->return_text = "Otobüs eklendi.";
			return true;
		}

		public function edit( $input ){

			$Marka = new Otobus_Markasi( $input['marka'] );

			$this->pdo->query("UPDATE " . $this->table ." SET
				kod = ?,
				plaka = ?,
				tip = ?,
				ogs = ?,
				aciklama = ?,
				sahip = ?,
				plaka_aciklama = ?,
				marka = ?,
				model = ?,
				model_yili = ?,
				hat = ?
				WHERE id = ?", array(
				$input['kod'],
				$input['plaka'],
				$input['tip'],
				$input['ogs'],
				$input['aciklama'],
				$input['sahip'],
				$input['plaka_aciklama'],
				$Marka->get_details('marka'),
				$Marka->get_model_name( $input['model'] ),
				$input['model_yili'],
				$input['hat'],
				$this->details['id']
			));
			Active_User::aktivite_kaydet( array( 'aktivite' => Actions::OTOBUS_DUZENLE ) );
			$this->return_text = "Otobüs düzenlendi.";
			return true;

		}

		public function delete(){
			if( $this->pdo->query("UPDATE " . $this->table . " SET durum = ? WHERE id = ?", array(0, $this->details["id"])) ){
				Active_User::aktivite_kaydet( array( 'aktivite' => Actions::OTOBUS_SIL ) );
				$this->return_text = "Otobüs silindi.";
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

	}