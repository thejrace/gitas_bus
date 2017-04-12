<?php



	class Sefer_Sure {
		// orer - kalkis
		// bitis - gidis
		public function hesapla( $from, $to ){
			if( trim($from) == "" || trim($to) == "" ) return 0;
			$from_exp = explode( ":", $from );
			$to_exp = explode( ":", $to );
			$from_saat = (int)$from_exp[0];
			$to_saat = (int)$to_exp[0];

			if( count($to_exp) < 2 || count($from_exp) < 2 ) {
				Session::set( "hederey", Session::get("hederey") . " <br>|  F" . $from . "   T" . $to  ); 
				return 0;		
			}

			$from_dk = (int)$from_exp[1];
			$to_dk = (int)$to_exp[1];
			$dakika = 0;	
			$saat = 0;
			$eksi = false;



			if( $from_saat == $to_saat ) return $to_dk - $from_dk;
			
			if( $from_saat == $to_saat ) return $to_dk - $from_dk;
			if( $to_saat == 0 ) $to_saat = 24;
			if( $from_saat == 0 ) $from_saat = 24;
			if( $to_saat < $from_saat ){
				$to_saat_temp = $to_saat;
				$to_dk_temp = $to_dk;
				$to_saat = $from_saat;
				$from_saat = $to_saat_temp;
				$to_dk = $from_dk;
				$from_dk = $to_dk_temp;
				$eksi = true;
				
			}
			$saat = $from_saat;
			$dakika += $to_dk;
			$dakika += 60 - $from_dk;
			$saat++;
			while( $saat != $to_saat ){
				$dakika += 60;
				$saat++;
			}
			if( $eksi ) return $dakika*-1;
			return $dakika;
		}


	}	