<?php

	class Otobus_Markasi {

		private $pdo, $details = array(), $table = DBT_OTOBUS_MARKALARI, $return_text;

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

			$check = $this->pdo->query("SELECT * FROM " . $this->table . " WHERE marka = ?", array($data['marka']))->results();

			if( count($check) > 0 ){
				$this->return_text = "Böyle bir marka zaten mevcut.";
				return false;
			}
			$this->pdo->insert($this->table, array(
				'marka' => $data['marka']
			));
			Active_User::aktivite_kaydet( array( 'aktivite' => Actions::OTOBUS_MARKA_EKLE ) );
			$this->return_text = "Marka eklendi.";
			return true;
		}

		public function edit( $data ){
			$check = 0;
			if( $this->details['marka'] != $data['marka'] ){
				$check = count($this->pdo->query("SELECT * FROM " . $this->table . " WHERE marka = ?", array($data['marka']))->results());
			}
			if( $check > 0 ){
				$this->return_text = "Böyle bir marka zaten mevcut.";
				return false;
			}
			if ( $this->pdo->query("UPDATE " . $this->table . " SET marka = ? WHERE id = ?", array( $data['marka'], $this->details['id'] ) ) ){
				Active_User::aktivite_kaydet( array( 'aktivite' => Actions::OTOBUS_MARKA_DUZENLE ) );
				$this->return_text = 'Marka düzenlendi.';
				return true;
			}
			return false;
		}

		public function edit_model( $data ){

			$check = $this->pdo->query("SELECT * FROM " . DBT_OTOBUS_MODELLER . " WHERE model = ? && marka = ?", array($data['model'], $this->details['id']) )->results();
			// kendisi harici ayni modelden varsa hata veriyoruz
			if( count($check) > 0 ){
				if( $data['model_id'] != $check[0]['id'] ){
					$this->return_text = "Böyle bir model zaten mevcut.";
					return false;
				}
			}

			if( $this->pdo->query("UPDATE " . DBT_OTOBUS_MODELLER . " SET model = ? WHERE id = ?", array( $data['model'], $data['model_id'] ) ) ){
				Active_User::aktivite_kaydet( array( 'aktivite' => Actions::OTOBUS_MODEL_DUZENLE ) );
				$this->return_text = 'Model düzenlendi.';
				return true;
			}
			return false;



		}

		public function delete(){

			if( $this->pdo->query("DELETE FROM " . $this->table . " WHERE id = ?", array( $this->details["id"])) ){
				
				// modelleri sil
				foreach( $this->get_models() as $model ){
					$this->pdo->query("DELETE FROM " . DBT_OTOBUS_MODELLER . " WHERE id = ?", array( $model["id"])  );
				}
				Active_User::aktivite_kaydet( array( 'aktivite' => Actions::OTOBUS_MARKA_SIL ) );
				$this->return_text = 'Marka silindi.';
				return true;
			}
			return false;

		}

		public function model_ekle( $data ){
			$this->pdo->insert(DBT_OTOBUS_MODELLER, array(
				'model' => $data['model'],
				'marka'	=> $this->details['id']
			));
			Active_User::aktivite_kaydet( array( 'aktivite' => Actions::OTOBUS_MODEL_EKLE ) );
			$this->return_text = "Model eklendi.";
			return true;

		}

		public function get_model_name( $id ){
			$model_query = $this->pdo->query("SELECT * FROM " . DBT_OTOBUS_MODELLER ." WHERE id = ?", array($id) )->results();
			if( count($model_query) > 0 ){
				return $model_query[0]['model'];
			}
			return false;
		}

		public function get_models(){
			$model_query = $this->pdo->query("SELECT * FROM " . DBT_OTOBUS_MODELLER ." WHERE marka = ?", array($this->details['id']) )->results();
			if( count($model_query) > 0 ){
				return $model_query;
			}
			return false;

		}

		public function delete_model( $modid ){
			return $this->pdo->query("DELETE FROM " . DBT_OTOBUS_MODELLER . " WHERE id = ?", array($modid));
		}

		public function get_details( $key = null ){
			if( isset($key) ) return $this->details[$key];
			return $this->details;
		}

		public function get_return_text(){
			return $this->return_text;
		}
	}