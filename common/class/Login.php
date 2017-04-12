<?php

	class Login {

		private $pdo, $return_text;

		public function __construct(){
			$this->pdo = DB::getInstance();
		}

		// beni hatirla
		public function auto_action( $user_id ){

			$data_query = $this->pdo->query("SELECT * FROM " . DBT_KULLANICILAR . " WHERE id = ?", array($user_id) )->results();
			Active_User::init( array(
				"id" => $data_query[0]["id"],
				"email" => $data_query[0]["email"],
				"user_name" => $data_query[0]["user_name"],
				"perm_level" => $data_query[0]["perm_level"]
			));
		}

		// normal login
		public function action( $input ){
			// eposta kontrolu
			$email_query = $this->pdo->query("SELECT * FROM " . DBT_KULLANICILAR . " WHERE email = ?", array( $input["email"] ) );
			if( $email_query->count() == 1 ){
				$user_data = $email_query->results();
				$user_salt = $user_data[0]["salt"];
				$user_pass = $user_data[0]["pass"];
				$user_id   = $user_data[0]["id"];

			} else {
				// $this->record_failed_login( $input["email"] );

				$this->pdo->insert('failed_logins', array(
					'email' => $input['email'],
					'ip'    => $_SERVER['REMOTE_ADDR'],
					'durum'		=> 'Eposta yanlış',
					'tarih' => Common::get_current_datetime()
				));


				$this->return_text = "Eposta veya şifre yanlış. Lütfen tekrar kontrol ediniz.";
				return false;
			}

			// sifre kontrolu
			$input_pass = hash( 'sha256', $user_salt . $input["pass"] );
			if( $input_pass != $user_pass ){
				// $this->record_failed_login( $input["email"] );

				$this->pdo->insert('failed_logins', array(
					'email' => $input['email'],
					'ip'    => $_SERVER['REMOTE_ADDR'],
					'durum'		=> 'Şifre yanlış',
					'tarih' => Common::get_current_datetime()
				));

				$this->return_text = "Eposta veya şifre yanlış. Lütfen tekrar kontrol ediniz.";
				return false;
			}

			// remember me kontrolu
			if( isset( $input["remember_me"] ) ){
				$Auto_Login = new Auto_Login;
				$Auto_Login->update_remember_me_token($user_id);
			}

			Active_User::init( array(
				"id" => $user_id,
				"email" => $input["email"],
				"user_name" => $user_data[0]["user_name"],
				"perm_level" => $user_data[0]["perm_level"]
			));

			Active_User::aktivite_kaydet( array( 'aktivite' => Actions::GIRIS ) );

			$this->pdo->query("UPDATE " . DBT_KULLANICILAR . " SET last_login = ? WHERE id = ?", array( Common::get_current_datetime(), $user_id) );

			return true;
		}

		public function get_return_text(){
			return $this->return_text;
		}

	}