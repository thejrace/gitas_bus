<?php

	class Active_User {

		public static function init( $data ){
			Session::set("details", $data);
			Session::set("perm_actions", Perm_Level::$levels[$data["perm_level"]]["actions"]);
			Session::set("perm_title",  Perm_Level::$levels[$data["perm_level"]]["title"]);
			Session::set( "login_session", true );
		}

		public static function get_details($key = null){
			
			if( isset($key) ){
				$data = Session::get("details");
				return $data[$key];
			} 
			return Session::get("details");
		}


		public static function get_perm_actions(){
			return Session::get("perm_actions");
		}

		public static function get_title(){
			return Session::get("perm_title");
		}

		public static function aktivite_kaydet( $data ){

			DB::getInstance()->insert(DBT_AKTIVITELER, array(
				'user_id' => self::get_details("id"),
				'aktivite' => $data["aktivite"],
				'tarih'	   => Common::get_current_datetime()
			));

		}

	}