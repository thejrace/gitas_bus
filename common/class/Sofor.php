<?php


	class Sofor {


		private $pdo, $details = array(), $table = DBT_SOFORLER, $ok = true;

		public function __construct( $id = null ){
			$this->pdo = DB::getInstance();
			if( isset($id) ){
				$kontrol_keyler = array( 'id', 'sicil_no', 'isim' );
				foreach( $kontrol_keyler as $key ){
					$query = $this->pdo->query("SELECT * FROM " . $this->table . " WHERE ".$key." = ?", array( $id ) )->results();
					if( count($query) == 1 ){
						$this->details = $query[0];
						$this->ok = true;
						break;
					} else {
						$this->ok = false;
					}
				}
			}
		}

		public function add( $data ){

			$this->pdo->insert( $this->table, array(
				'isim' 		=> $data['isim'],
				'sicil_no' 	=> $data['sicil_no'],
				'telefon' 	=> $data['telefon']
			));

		}

		public function is_ok(){
			return $this->ok;
		}
		
		
		public function get_details( $key = null ){
			if( isset($key) ) return $this->details[$key];
			return $this->details;
		}

		public function get_return_text(){
			return $this->return_text;
		}

	}