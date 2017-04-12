<?php

	class Malzeme_Tipi {

		private $pdo, $table = DBT_MALZEME_TIPLERI, $details = array(), $return_text;

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

			if( $this->pdo->insert($this->table, array( "malzeme_tipi" => $data["malzeme_tipi"]))){
				Active_User::aktivite_kaydet( array( 'aktivite' => Actions::MALZEME_TIPI_EKLE ) );
				$this->return_text = "Malzeme tipi eklendi.";
				return true;
			}
			return false;

		}

		public function delete(){

		}

		public function edit( $data ){

		}

		public function get_details( $key = null ){
			if( isset($key) ) return $this->details[$key];
			return $this->details;
		}

		public function get_return_text(){
			return $this->return_text;
		}




	}