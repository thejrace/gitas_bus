<?php

	/* Register 05.02.16 Obarey Inc.
	*/	
	class Register {
		private $pdo, $return_text;

		public function action( $input ){
			// salt olustur
			$salt = utf8_encode( mcrypt_create_iv( 64, MCRYPT_DEV_URANDOM ) );
			// PHP 5.1.2 ve sonrasinda var hash() fonksiyonu
			// sifre ve salti seviştir
			$hash = hash( 'sha256', $salt . $input["pass_1"] );
			$date = Common::get_current_datetime();
			if( !DB::getInstance()->insert( DBT_KULLANICILAR, array(
				"user_name" 		=> $input["name"],
				"pass" 		=> $hash,
				"salt" 		=> $salt,
				"perm_level" => $input["perm_level"],
				"email" 	 => $input["email"]
			)) ){
				$this->return_text = "Bir hata oluştu. Lütfen tekrar deneyin.";
				return false;
			}
			return true;
		}



		public function get_return_text(){
			return $this->return_text;
		}
	}