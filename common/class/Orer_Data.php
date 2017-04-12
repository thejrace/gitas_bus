<?php
	
	class Orer_Data {

		private $pdo, $details = array(), $table = DBT_ORER_DATA, $tarih_table = DBT_ORER_TARIH_LOG, $return_text;

		public function __construct( $id = null ){
			$this->pdo = DB::getInstance();
			if( isset($id) ){
				$query = $this->pdo->query("SELECT * FROM " . $this->table . " WHERE id = ?", array( $id ) )->results();
				if( count($query) == 1 ){
					$this->details = $query[0];
				}
			}
		}

		public function add($data){

		}

		public function edit($data){
			if( $this->pdo->query("UPDATE " . $this->table . " SET no = ?, servis = ?, guzergah = ?, surucu = ?, gelis = ?, amir = ?, gidis = ?, tahmin = ?, bitis = ?, durum = ?, durum_kodu = ? WHERE id = ?", array(
				$data['no'],
				$data['servis'],
				$data['guzergah'],
				$data['surucu'],
				$data['gelis'],
				$data['amir'],
				$data['gidis'],
				$data['tahmin'],
				$data['bitis'],
				$data['durum'],
				$data['durum_kodu'],
				$this->details['id']
			)) ){
				$this->return_text = "Kayıt düzenlendi.";
				return true;
			}
			$this->return_text = "Bir hata oluştu. Lütfen tekrar deneyin.";
			return false;
		}

		public function delete(){

		}

		public function get_return_text(){
			return $this->return_text;
		}

		public function get_details( $key = null ){
			if( isset($key) ) return $this->details[$key];
			return $this->details;
		}

	}