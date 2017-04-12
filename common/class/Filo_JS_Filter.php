<?php

	class Filo_JS_Filter {

		private $default = array();
		public function __construct( $default ){
			$this->default = $default;
		}	
		// @temizle_val -> yeni filter arrayinde olmayip, default olanlari ne yapsin
		public function init( $yeni_filter, $temizle_val ){
			foreach( $this->default as $kod => $status ){
				if( isset($yeni_filter[$kod]) ){
					$this->default[$kod] = $yeni_filter[$kod];
				} else {
					$this->default[$kod] = $temizle_val;
				}
			}
			return $this->default;
		}

	}