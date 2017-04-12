<?php
	
	class Hat {
		private $pdo, $details = array(), $return_text, $table = DBT_HATLAR;

		public function __construct( $id = null ){
			$this->pdo = DB::getInstance();

			if( isset($id) ){
				$query = $this->pdo->query("SELECT * FROM " . $this->table . " WHERE id = ?", array( $id ) )->results();
				if( count($query) == 1 ){
					$this->details = $query[0];
				}
			}
		}

		public function get_merkez_koords(){
			$query = $this->pdo->query("SELECT * FROM " . DBT_HAT_MERKEZ_KOORDINATLAR . " WHERE hat = ?", array( $this->details['id']))->results();
			return array( "latitude" => $query[0]['latitude'], "longitude" => $query[0]['longitude']);
		}
		
		// direk js olarak string donuyorum bunu
		public function get_durak_koords( $liste = false ){

			$query = $this->pdo->query("SELECT * FROM " . DBT_HAT_DURAKLAR ." WHERE hat = ?", array( $this->details['id']))->results();
			if( !$liste ){
				$js = "";
				foreach( $query as $durak ){
					$js .= 'var circlePlacemark = new ymaps.Placemark(['.$durak['latitude'].','.$durak['longitude'].'],{hintContent: "<b>Sıra:</b> '.$durak['sira'].'<br><b>Durak: </b> '. $durak["ad"].'<br><b>Kod: </b> '.$durak['kod'].'"}, {iconLayout: circleLayout,iconShape: {type: "Circle",coordinates: [0, 0],radius: 10}});map.geoObjects.add(circlePlacemark);';
				}
			} else {
				return $query;
			}

			return $js;

		}

		public function get_guzergah_koords(){
			$query = $this->pdo->query("SELECT * FROM " . DBT_HAT_GUZERGAH_KOORDINATLAR ." WHERE hat = ?", array( $this->details['id']))->results();
			$array = array();
			foreach( $query as $ikili ){
				$array[] = "[ ". $ikili['latitude'] . ", " . $ikili['longitude'] . " ]";
			}
			return implode( ",", $array );

		}

		public function get_guzergah_koords_google(){
			$q = DB::getInstance()->query("SELECT * FROM hat_guzergah_koordinatlari WHERE hat = ? ORDER BY sira", array( $this->details['id'] ))->results();
		    $str = array();
		    foreach( $q as $kor ){
		    	$str[] = "{ lat: " . $kor['latitude'] . ", lng:" . $kor['longitude'] . "}";
		    }
		    return implode( ", ", $str );
		}


		public function uzunluk_guncelle( $uzunluk ){

			return $this->pdo->query("UPDATE " . $this->table . " SET uzunluk = ? WHERE id = ?",array( $uzunluk, $this->details["id"] ) );

		}


		public function add( $data ){

			if( $this->pdo->insert($this->table, array(
				"hat" 		=> $data["hat"],
				"aciklama"  => $data["aciklama"],
				"uzunluk"	=> 0
			)) ){
				Active_User::aktivite_kaydet( array( 'aktivite' => Actions::OTOBUS_HAT_EKLE ) );
				$this->return_text = "Hat eklendi";
				return true;
			}
			return false;
		}

		public function delete(){

			if( $this->pdo->query("DELETE FROM " . $this->table . " WHERE id = ?",array($this->details["id"])) ){
				Active_User::aktivite_kaydet( array( 'aktivite' => Actions::OTOBUS_HAT_SIL ) );
				$this->return_text = 'Hat silindi.';
				return true;
			}
			return false;
		}

		public function edit( $data ){
			if( $this->pdo->query("UPDATE " . $this->table . " SET
				hat = ?, 
				aciklama = ?
				WHERE id = ?", array(
					$data["hat"],
					$data["aciklama"],
					$this->details["id"]
				)) ){
				Active_User::aktivite_kaydet( array( 'aktivite' => Actions::OTOBUS_HAT_DUZENLE ) );
				$this->return_text = "Hat düzenlendi.";
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