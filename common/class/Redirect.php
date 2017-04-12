<?php

	class Redirect {


		public function go_index(){

			header("Location: ". URL_MAIN_URL );

		}


		public function go_login(){
			header("Location: " . URL_LOGIN );
		}

	}