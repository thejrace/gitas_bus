<?php

	require 'inc/init.php';

	$SAYFA_DATA = array(
		'title' 	=> 'Otobüsler',
		'action_id' => Actions::OTOBUS_DATA_ERISIM
	);

	// izin kontrolu
	if( !in_array( $SAYFA_DATA["action_id"], Active_User::get_perm_actions() ) ) {
		header("Location: index.php");
	}

	$OTOBUSLER = array();
	$otobus_query = DB::getInstance()->query("SELECT * FROM " . DBT_OTOBUSLER . " WHERE durum = ? ORDER BY id DESC",array(1))->results();
	foreach( $otobus_query as $otobus ){
		$otobuscuk = new Otobus( $otobus["id"] );
		$otobuscuk->get_hat_no();
		$otobuscuk->get_sahip_isim();
		$OTOBUSLER[] = $otobuscuk->get_details();
	}

	$NAV_ACTION_BUTTONS = new Nav_Action_Buttons(
		array(
			array(
				"action" 	=> Actions::OTOBUS_EKLE
			)
		)
	);


	require 'inc/header.php';
?>
	
	<div class="section-header">
		<?php echo $SAYFA_DATA["title"] ?>
	</div>


	<div class="section-content">
		<div class="otobus data-table">

			<div class="pagination-container ">
				<button type="button" class="pagin-toggle">Sayfalama</button>
				<div class="otobus-pagin pagination-center clearfix">			
					
				</div>
			</div>


			<div class="pagination-container">
				<button type="button" class="pagin-toggle">Filtrele</button>	
				<div class="otobus-filter pagination-center clearfix">
					<form id="filter_form">
						
						<div class="pagination-col">
							<span>Kapı Kodu</span>
							<input type="text" class="pagininput text"  id="dt_kod" />
						</div>
						<div class="pagination-col">
							<span>Hat</span>
							<select id="dt_hat" class="pagininput select" >
								<option value="0">Seçiniz..</option>
								<option value="Hat Atanmamış">Hat Atanmamış</option>
								<?php

									$HATLAR = DB::getInstance()->query("SELECT * FROM " . DBT_HATLAR . " ORDER BY hat" )->results();
									foreach( $HATLAR as $hat ){
										$Hat = new Hat( $hat["id"] );
										echo '<option value="'.$Hat->get_details("hat").'">'.$Hat->get_details("hat")." - ".$Hat->get_details("aciklama").'</option>';
									}

								?>
							</select>
						</div>
						<div class="pagination-col">
							<span>Plaka</span>
							<input type="text" class="pagininput text"  id="dt_plaka" />
						</div>

						<div class="pagination-col">
							<span>OGS</span>
							<input type="text" class="pagininput text"  id="dt_ogs" />
						</div>
						
						<div class="pagination-col">
							<button type="button" class="filterbtn kirmizi"  id="filter_uygula">UYGULA</button>
							<button type="button" class="filterbtn gri"  id="filter_reset">TEMİZLE</button>
						</div>
					</form>
				</div>
			</div>

			<div class="dt-nav-container">
				<?php echo $NAV_ACTION_BUTTONS->get_buttons() ?>
			</div>
			
			<ul class="otobus-data-table">

				
			</ul>

		</div>
	</div>
	

	<script type="text/javascript">


		var nav_headers = 
				{
				'FİLO PLAN':
					{ 	pl:<?php echo Actions::FILO_PLAN_ERISIM ?>,
						href : '<?php echo URL_OTOBUS_FILO_PLAN ?>',
						class : 'kirmizi' },
				/*'HARİTA TAKİP':
					{ 	pl:<?php echo Actions::OTOBUS_YAKIT_VERI_GIRME ?>,
						href : '<?php echo URL_YAKIT_KAYDI_EKLE ?>',
						class : 'kirmizi' },
				'YAKIT GİRİŞİ':
					{ 	pl:<?php echo Actions::OTOBUS_YAKIT_VERI_GIRME ?>,
						href : '<?php echo URL_YAKIT_KAYDI_EKLE ?>',
						class : 'turuncu' },
				*/'YAKIT GEÇMİŞİ':
					{ 	pl:<?php echo Actions::YAKIT_GECMISI_DATA_ERISIM ?>,
						href : '<?php echo URL_YAKIT_GECMISI ?>',
						class : 'turuncu' },
				/*'PARÇA GİRİŞİ':
					{ 	pl:<?php echo Actions::OTOBUS_PARCA_VERI_GIRME ?>,
						href : '<?php echo URL_STOK ?>?parca_kaydi=true&oid=',
						class : 'mavi' },*/
				'SERVİS GEÇMİŞİ' :
					{ 	pl:<?php echo Actions::PARCA_GECMISI_DATA_ERISIM ?>,
						href : '<?php echo URL_PARCA_GECMISI ?>',
						class : 'mavi' },
				'DÜZENLE' :
					{ 	pl:<?php echo Actions::OTOBUS_DUZENLE ?>,
						href : '<?php echo URL_OTOBUS_DUZENLE ?>',
						class : 'sari' }
				// 'SİL' : 
				// 	{ 	pl:<?php echo Actions::OTOBUS_SIL ?>,
				// 		href : '',
				// 		class : 'kirmizi'}
				};


		var ORG_DATA = <?php echo json_encode($OTOBUSLER) ?>,
			TABLE_CONTAINER = $AHC('otobus-data-table'),
			Otobus_Table = new DataTable({
			container : TABLE_CONTAINER,
			pagin_container : $AHC('otobus-pagin'),
			filter_container : $AHC('otobus-filter'),
			data : ORG_DATA,
			header_keys: [ 'plaka', 'kod', 'hat' ],
			icon_class  : 'ico bus-big',
			nav_headers : nav_headers,
			data_headers : { 'Kapı Kodu' : 'kod', 'Hat': 'hat', 'Sahip' : 'sahip', 'OGS' : 'ogs' ,'Marka' : 'marka', 'Model' : 'model', 'Model Yılı' : 'model_yili' },
			jx_delete : function( targ ){
				var kayit_id = targ.getAttribute('item-id');
				if( confirm_alert("Otobüsü silmek istediğinizden emin misiniz?") ){
					Popup.start_loader();
					AHAJAX_V3.req( Base.AJAX_URL + 'otobus.php', manual_serialize({type:'sil', item_id:kayit_id}), function(res){
						if( res.ok ){
							Otobus_Table.delete_item( 'id', kayit_id ); 
							Otobus_Table.init();
							Popup.off();
						}

					});
				}
			},
			filter_inputs: [ 'dt_plaka', 'dt_ogs', 'dt_kod', 'dt_hat' ]
		});
	

		AHReady(function(){

			Otobus_Table.init();
			Otobus_Table.init_events();

		});

	</script>

<?php
	require 'inc/footer.php';