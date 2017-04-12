<?php

    require 'inc/init.php';

    $iett = array(
    "42HM" => 16,
	"122C" => 	41,
	"122B" => 	70,
	"2" => 	31,
	"4"=> 	17,
	"97GE"=> 	41,
	"320A"=> 	70,
	"E-3"	=> 85,
	"E-9"	=> 63,
	"9ÜD"	=> 36,
	"11ÜS"=> 	66,
	"129T"=> 	43,
	"40B"	=> 47,
	"BN1"	=> 60,
	"41C"=> 	37,
	"BN2"=> 	50,
	"41E"	=> 25,
	"89S"=> 	24,
	"DT1"=> 	16,
	"DT2"=> 	17,
	"43R"=> 	17,
	"44B"	=> 34,
	"20D"=> 	41,
	"14ÇK"=> 	47,
	"69A"=> 	22,
	"14ES"=> 	50,
	"36CE"=> 	40,
	"47E"	=> 25,
	"89İ"=> 	53,
	"49Y"=> 	29,
	"MR20"=> 	30,
	"MR10"=> 	11,
	"27E"	=> 19,
	"MR11"=> 	9,
	"47Ç"	=> 25,
	"27T"	=> 14,
	"29C"	=> 35,
	"14KS"=> 	69,
	"29D"=> 	35,
	"KM2"	=> 12,
	"27SE"=> 	21,
	"41AT"=> 	53,
	"132M"	=> 55,
	"29Ş"=> 27,
	"133F"=> 	40,
	"133K"=> 	42,
	"93C"=> 	26,
	"HT20"=> 	39,
	"93T"=> 	34,
	"E-11"=> 	73,
	"EM2"	=> 13,
	"94Y"	=> 46,
	"71T"=> 	35,
	"72T"	=> 42,
	"97A"	=> 27,
	"97M"	=> 45,
	"97T"	=> 32,
	"98B"	=> 23,
	"54ÖR"=> 	16,
	"98D"=> 	19,
	"98H"	=> 72,
	"98S"	=> 29,
	"99A"	=> 17,
	"99Y"	=> 29,
	"76B"	=> 40,
	"76O"	=> 65,
	"92Ş"=> 	42,
	"15BK"=> 	68,
	"30A"	=> 10,
	"41ST"=> 	35,
	"54K"	=> 18,
	"30M"=> 	8,
	"15ÇK"=> 	38,
	"79B"=> 	66,
	"31E"	=> 40,
	"55T"	=> 20,
	"98İ"=> 	34,
	"58A"	=> 28,
	"54Ç"	=> 16,
	"58N"	=> 19,
	"35A"=> 	6,
	"35C"	=> 14,
	"11A"	=> 57,
	"14"	=> 56,
	"59N"	=> 13,
	"11L"	=> 18,
	"11M"	=> 33,
	"59R"	=> 12,
	"11Y"	=> 19,
	"28"	=> 19,
	"131"=> 	54,
	"36T"=> 	47,
	"31"	=> 36,
	"37E"	=> 25,
	"35"	=> 10,
	"36"	=> 26,
	"37T"=> 	28,
	"146"	=> 58,
	"147"	=> 43,
	"38B"=> 	31,
	"14A"	=> 61,
	"14B"=> 	43,
	"38E"	=> 29,
	"14F"=> 	22,
	"49"=> 	28,
	"14M"	=> 46,
	"153"	=> 19,
	"14R"	=> 33,
	"14Y"	=> 22,
	"39B"=> 	26,
	"52"=> 	4,
	"15B"	=>22,
	"15C"	=>16,
	"55"	=> 21,
	"15F"=> 	55,
	"15K"	=> 13,
	"15S"=> 	16,
	"39Y"=> 	30,
	"62"=> 	22,
	"63"=> 	22,
	"16B"=> 	58,
	"16F"=> 	36,
	"FB1"=> 	10,
	"FB2"	=> 9,
	"87"=> 	15,
	"8A"=> 	23,
	"8E"=> 	27,
	"90"=> 	13,
	"92"=> 	36,
	"93"=> 	29,
	"19D"	=> 40,
	"19E"=> 58,
	"19F"=> 	30,
	"98"=> 	52,
	"19S"	=> 66,
	"19T"	=> 30,
	"18Ü"=> 	73,
	"59RS"=> 	35
	);

	foreach( $iett as $hat => $uzunluk ){
		DB::getInstance()->query("UPDATE " . DBT_HATLAR . " SET iett_uzunluk = ? WHERE hat = ?", array($uzunluk, $hat));
	}

    require 'inc/header.php';

?>
	

	<div class="section-content">

		<div class="test">

		</div>


		<div class="obarey-dt ">
			<div class="dt-header">Otobüs Sefer İstatistikleri</div>
			<div class="filter-container">
				<div class="filter-center active clearfix">

					<div class="filter-row sayfalama clearfix">
						<div class="filter-row-header">Sayfalama</div>
						<div class="filter-col">
				    		<span>Kayıt Sayısı</span>
				    		<select name="dt_rrp" id="dt_rrp" class="pagininput select" >
				    			<option></option>
				    		</select>
				    	</div>
				    	<div class="filter-col mobile-iblock">
					    	<button  class="pagination-btn first" ></button>
					    	<button  class="pagination-btn prev" ></button>
				    	</div>
				    	<div class="filter-col hide-mobile">
				    		<span>Sayfa</span>
				    		<select name="dt_page" id="dt_page" class="pagininput select">
							</select>
						</div>
						<div class="filter-col hide-mobile">
							<span>( 0 - 4 / 15 )</span>
						</div>
						<div class="filter-col mobile-iblock">
							<button class="pagination-btn next" ></button>
							<button class="pagination-btn last"></button>
						</div>
					</div>

					<div class="filter-row tarih-filtre clearfix">
						<div class="filter-row-header">Tarih Filtresi</div>
						<div class="filter-col">
							<button type="button" class="filterbtn kirmizi" id="filter_tumu">TÜMÜNÜ GÖRÜNTÜLE</button>
						</div>
						<div class="filter-col">
							<span>Günlük</span>
							<input type="text" class="pagininput text" id="dt_gunluk" />
						</div>
						<div class="filter-col">
							<button type="button" class="filterbtn kirmizi" id="filter_uygula_gunluk">UYGULA</button>
						</div>
						<div class="filter-col">
							<span>Ay</span>
							<select id="dt_ay" class="pagininput select">
							</select>
						</div>
						<div class="filter-col">
							<span>Yıl</span>
							<select id="dt_yil" class="pagininput select">
							</select>
						</div>
						<div class="filter-col">
							<button type="button" class="filterbtn kirmizi" id="filter_uygula_ay_yil">UYGULA</button>
						</div>
					</div>

					<div class="filter-row sefer-durum-filtre clearfix">
						<div class="filter-row-header">Sefer Durum Filtresi</div>
						<div class="filter-col">
							<button type="button" class="obarey-cb" key="durum" filter="B">BEKLEYEN</button>
							<button type="button" class="obarey-cb" key="durum" filter="A">AKTİF</button>
							<button type="button" class="obarey-cb" key="durum"" filter="T">TAMAMLANAN</button>
							<button type="button" class="obarey-cb" key="durum" filter="I">İPTAL</button>
							<button type="button" class="obarey-cb" key="durum" filter="Y">YARIM KALMIŞ</button>
							<button type="button" class="obarey-cb" key="durum" filter="EB">EKSİK BİLGİ</button>
						</div>
					</div>

				</div>
			</div>

			<div class="dt ">
				<table class="filo-table">
					<thead>
						<tr>
							<td>NO</td>
							<td>GÜZERGAH</td>
							<td>HAT</td>
							<td>ORER</td>
						</tr>
					</thead>
					<tbody>
						<tr class="tamam">
							<td>2</td>
							<td>46E9391_G</td>
							<td>46E</td>
							<td>20:00</td>
						</tr>
						<tr class="tamam">
							<td>2</td>
							<td>46E9391_G</td>
							<td>46E</td>
							<td>20:00</td>
						</tr>
						<tr class="aktif">
							<td>2</td>
							<td>46E9391_G</td>
							<td>46E</td>
							<td>20:00</td>
						</tr>
					</tbody>
				</table>
			</div>

		</div>


		<div class="obarey-dt">
			<div class="dt-header">Otobüs Sefer İstatistikleri</div>
			<div class="filter-container">
				<div class="filter-center active clearfix">

					<div class="filter-row sayfalama clearfix">
						<div class="filter-row-header">Sayfalama</div>
						<div class="filter-col">
				    		<span>Kayıt Sayısı</span>
				    		<select name="dt_rrp" id="dt_rrp" class="pagininput select" >
				    			<option></option>
				    		</select>
				    	</div>
				    	<div class="filter-col mobile-iblock">
					    	<button  class="pagination-btn first" ></button>
					    	<button  class="pagination-btn prev" ></button>
				    	</div>
				    	<div class="filter-col hide-mobile">
				    		<span>Sayfa</span>
				    		<select name="dt_page" id="dt_page" class="pagininput select">
							</select>
						</div>
						<div class="filter-col hide-mobile">
							<span>( 0 - 4 / 15 )</span>
						</div>
						<div class="filter-col mobile-iblock">
							<button class="pagination-btn next" ></button>
							<button class="pagination-btn last"></button>
						</div>
					</div>

					<div class="filter-row tarih-filtre clearfix">
						<div class="filter-row-header">Tarih Filtresi</div>
						<div class="filter-col">
							<button type="button" class="filterbtn kirmizi" id="filter_tumu">TÜMÜNÜ GÖRÜNTÜLE</button>
						</div>
						<div class="filter-col">
							<span>Günlük</span>
							<input type="text" class="pagininput text" id="dt_gunluk" />
						</div>
						<div class="filter-col">
							<button type="button" class="filterbtn kirmizi" id="filter_uygula_gunluk">UYGULA</button>
						</div>
						<div class="filter-col">
							<span>Ay</span>
							<select id="dt_ay" class="pagininput select">
							</select>
						</div>
						<div class="filter-col">
							<span>Yıl</span>
							<select id="dt_yil" class="pagininput select">
							</select>
						</div>
						<div class="filter-col">
							<button type="button" class="filterbtn kirmizi" id="filter_uygula_ay_yil">UYGULA</button>
						</div>
					</div>

					

				</div>
			</div>
			<div class="dt ">
				<table class="filo-table">
					<thead>
						<tr>
							<td>NO</td>
							<td>GÜZERGAH</td>
							<td>HAT</td>
							<td>ORER</td>
						</tr>
					</thead>
					<tbody>
						<tr class="bekleyen">
							<td>2</td>
							<td>46E9391_G</td>
							<td>46E</td>
							<td>20:00</td>
						</tr>
						<tr class="bekleyen">
							<td>2</td>
							<td>46E9391_G</td>
							<td>46E</td>
							<td>20:00</td>
						</tr>
						<tr class="bekleyen">
							<td>2</td>
							<td>46E9391_G</td>
							<td>46E</td>
							<td>20:00</td>
							<td>20:00</td>
							<td>20:00</td>
							<td>46E</td>
							<td>20:00</td>
							<td>20:00</td>
							<td>20:00</td>
							<td>46E</td>
							<td>20:00</td>
							<td>20:00</td>
							<td>20:00</td>
						</tr>
					</tbody>
				</table>
			</div>

		</div> 

	</div>


	
	

	<script type="text/javascript">

		var ObareyDTTheads = {
			FILO_ORER: [ "SIRA", "SERVİS", "HAT", "GÜZERGAH", "SÜRÜCÜ", "GELİŞ", "ORER", "BEKLEME", "AMİR", "GİDİŞ", "TAHMİN", "BİTİŞ", "DKODU", "SÜRE" ]

		};
		


		AHReady(function(){


		});


	</script>
<?php
    require 'inc/footer.php';