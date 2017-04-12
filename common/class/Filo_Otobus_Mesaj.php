<?php


	class Filo_Otobus_Mesaj {


		private $pdo, $details = array(), $table = DBT_FILO_OTOBUS_MESAJLAR, $ok = true;

		public function __construct( $id = null ){
			$this->pdo = DB::getInstance();
		}

		public function add( $data ){

			$this->pdo->insert( $this->table, array(
				'kapi_kodu' => $data['kapi_kodu'],
				'saat' 		=> $data['saat'],
				'mesaj' 	=> $data['mesaj'],
				'tarih'		=> $data['tarih']
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