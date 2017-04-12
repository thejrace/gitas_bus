<?php

	require 'inc/init.php';

	class Logout {

		public function init(){
			Active_User::aktivite_kaydet( array( 'aktivite' => Actions::CIKIS ) );
			Session::destroy("details");
			Session::destroy("perm_actions");
			Session::destroy("perm_title");
			Session::destroy("login_session");
			Cookie::setwithtime("obarey_rm", "", time()-86400*365 );
		}

	}

	$Logout = new Logout;
	$Logout->init();

	header("Location: index.php");