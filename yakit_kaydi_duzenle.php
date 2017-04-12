<?php

	require 'inc/init.php';



	$SAYFA_DATA = array(
		'title' 	=> 'Yakıt Kaydını Düzenle',
		'action_id' => Actions::YAKIT_GIRISI_DUZENLEME
	);

	// izin kontrolu
	if( !in_array( $SAYFA_DATA["action_id"], Active_User::get_perm_actions() ) ) {
		header("Location: index.php");
	}

	if( Input::get("kid") == "" ) header("Location: index.php");
	$YAKIT_KAYDI = new Yakit_Kaydi( Input::get("kid") );
	$OTOBUS = new Otobus( $YAKIT_KAYDI->get_details("otobus_id"));

	$NAV_ACTION_BUTTONS = new Nav_Action_Buttons(
		array(
			array(
				"action" 	=> Actions::YAKIT_GECMISI_DATA_ERISIM,
				"url"		=> URL_YAKIT_GECMISI.$OTOBUS->get_details("id"),
				"title"		=> "Yakıt Geçmişine Dön"
			),
			array(
				"action" 	=> Actions::OTOBUS_DATA_ERISIM,
				"title"		=> "Otobüslere Dön"
			)
		)
	);

	$JQUERYUI = true;
	require 'inc/header.php';
?>
	
	<div class="section">
		<div class="section-header">
			"<?php echo $OTOBUS->get_details("kod") ?>" Hat Kodlu Otobüsün  "<?php echo $YAKIT_KAYDI->get_details('tarih') . '" Tarihli ' . $SAYFA_DATA["title"] ?>
		</div>

		<div class="section-content">
			<div class="dt-nav-container">
				<?php echo $NAV_ACTION_BUTTONS->get_buttons() ?>
			</div>
			<div class="main-form-notf"></div>
			<form action="" method="post" id="duzenle">

				<div class="input-container">
					<label for="ekle_fiyat">Fiyat</label>
					<input type="text" name="fiyat" id="ekle_fiyat" class="req posnum"  value="<?php echo $YAKIT_KAYDI->get_details('fiyat') ?>"/>
				</div>

				<div class="input-container">
					<label for="ekle_miktar">Miktar</label>
					<input type="text" name="miktar" id="ekle_miktar" placeholder="Opsiyonel"  value="<?php echo $YAKIT_KAYDI->get_details('miktar') ?>" />
				</div>

				<div class="input-container">
					<label for="ekle_tarih">Tarih</label>
					<input type="text" name="tarih" id="ekle_tarih" placeholder="GG-AA-YY" value="<?php echo Common::date_reverse( $YAKIT_KAYDI->get_details('tarih') ) ?>"/>
				</div>


				<div class="input-container submit-center">
					<input type="hidden" name="type" value="duzenle" />
					<input type="hidden" name="item_id" value="<?php echo Input::get("kid") ?>" />
					<input type="submit" class="navbtn orange" value="Kaydet"/>
				</div>

			</form>

		</div>
	</div>

	<script type="text/javascript">

		
		AHReady(function(){

			var EkleNotf = new FormNotf( $AH('duzenle') );
			add_event( $AH("duzenle"), "submit", function(ev){
				if( $AH('ekle_tarih').value == "" ) EkleNotf.init(0, 'Tarih seçiniz.' );
				var form = this;
				if( FormValidation.check( this ) ){
					Popup.start_loader();
					AHAJAX_V3.req( Base.AJAX_URL + 'yakit_kaydi.php', serialize(this), function(res){
						if( res.ok ){
							EkleNotf.init(1, res.text );
							window.scrollTo(0, 0);
						} else {
							FormValidation.show_serverside_errors( res.inputret );
						}
						Popup.off();
					});
				}
				event_prevent_default( ev );
			});

			$( "#ekle_tarih" ).datepicker({
				dateFormat: "dd-mm-yy",//tarih formatı yy=yıl mm=ay dd=gün
				autoSize: true,//inputu otomatik boyutlandırır
				changeMonth: true,//ayı elle seçmeyi aktif eder
				changeYear: true,//yılı elee seçime izin verir
				dayNames:[ "Pazar", "Pazartesi", "Salı", "Çarşamba", "Perşembe", "Cuma", "Cumartesi" ],//günlerin adı
				dayNamesMin: [ "Pa", "Pzt", "Sa", "Çar", "Per", "Cum", "Cmt" ],//kısaltmalar
				maxDate: "+0y+0m +0w",//ileri göre bilme zamanını 2 yıl 1 ay 2 hafta yaptık
				minDate: "-2y-0m -0w",//geriyi göre bilme alanını 1 yıl 1 ay 2 hafta yaptık.bunu istediğiniz gibi ayarlaya bilirsiniz
				monthNamesShort: [ "Ocak", "Şubat", "Mart", "Nisan", "Mayıs", "Haziran", "Temmuz", "Ağustos", "Eylül", "Ekim", "Kasım", "Aralık" ],//ay seçim alanın düzenledik
				nextText: "İleri",//ileri butonun türkçeleştirdik
				prevText: "Geri"//buda geri butonu için,
			});

		});

	</script>



<?php

	require 'inc/footer.php';