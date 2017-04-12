<?php


	class Otobus_Sahibi {


		private $pdo, $details = array(), $table = DBT_OTOBUS_SAHIPLERI;

		public function __construct( $id = null ){
			$this->pdo = DB::getInstance();
			if( isset($id) ){
				$query = $this->pdo->query("SELECT * FROM " . $this->table . " WHERE id = ?", array( $id ) )->results();
				if( count($query) == 1 ){
					$this->details = $query[0];
				}
			}
		}

		
		
		public function get_details( $key = null ){
			if( isset($key) ) return $this->details[$key];
			return $this->details;
		}

		public function get_return_text(){
			return $this->return_text;
		}

	}