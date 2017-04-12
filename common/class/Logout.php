<?php

	class Logout {
		private $pdo, $user_id;
		public function __construct(){
			$this->pdo = DB::getInstance();
			$this->user_id = Session::get("user_id");
		}

		public function action(){
			$this->destroy_sessions();
			$this->destroy_cookie();
		}

		private function destroy_sessions(){
			Session::destroy( "user_id" );
			Session::destroy( "user_name" );
		}

		private function destroy_cookie(){
			Cookie::destroy("sgrmetoken");
			if( ! $this->pdo->query("DELETE FROM ". DBT_AUTH_TOKENS . " WHERE user_id = ?", array($this->user_id)) ) return false;
		}
	}