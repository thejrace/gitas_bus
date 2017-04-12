<?php

	require 'inc/init.php';

	$SAYFA_DATA = array(
		'title' 	=> 'Parça Geçmişi',
		'action_id' => Actions::PARCA_GECMISI_DATA_ERISIM
	);

	// izin kontrolu
	if( !in_array( $SAYFA_DATA["action_id"], Active_User::get_perm_actions() ) ) {
		header("Location: index.php");
	}

	if( Input::get("oid") == "" ) header("Location: ". URL_OTOBUSLER );

	$OTOBUS = new Otobus( Input::get("oid") );
	$DATA = DB::getInstance()->query("SELECT * FROM " . DBT_OTOBUS_MALZEME_GECMISLERI . " WHERE otobus_id = ? ORDER BY tarih DESC",array(Input::get("oid")))->results();
	foreach( $DATA as $key => $val ){
		$Malzeme = new Malzeme($DATA[$key]["malzeme_id"]);
		$DATA[$key]['adet'] = $DATA[$key]['adet'] . " adet";
		$DATA[$key]['malzeme_tipi'] = $Malzeme->get_details("malzeme_tipi");
		$DATA[$key]['malzeme_marka'] = $Malzeme->get_details("marka");
		$DATA[$key]['malzeme_aciklama'] = $Malzeme->get_details("aciklama");
		$DATA[$key]['birim_fiyat'] = Common::dot_to_comma($Malzeme->get_details("fiyat")) . " TL";
		$DATA[$key]['toplam_fiyat'] = Common::dot_to_comma( $DATA[$key]["adet"] * $Malzeme->get_details("fiyat") ) . " TL";
		$DATA[$key]['tarih'] = Common::datetime_reverse( $DATA[$key]["tarih"] );
	}


	$NAV_ACTION_BUTTONS = new Nav_Action_Buttons(
		array(
			
			array(
				"action" 	=> Actions::OTOBUS_PARCA_VERI_GIRME,
				"url"		=> URL_STOK."?parca_kaydi=true&oid=".Input::get("oid")
			)
		)
	);



	require 'inc/header.php';
?>
	
	<div class="section-header">
		<?php echo $OTOBUS->get_details("kod") ?> kodlu otobüsün <?php echo $SAYFA_DATA["title"] ?>
	</div>

	<div class="section-content">
		<div class="otobus data-table">

			<div class="pagination-container ">
				<button type="button" class="pagin-toggle">Sayfalama</button>
				<div class="pagination-center parca-pagin clearfix">			
					
				</div>
			</div>


			<div class="pagination-container">
				<button type="button" class="pagin-toggle">Filtrele</button>	
				<div class="pagination-center parca-filter clearfix">
					<form id="filter_form">
						<div class="pagination-col">
							<span>Malzeme Tipi</span>
							<select id="dt_malzeme_tipi"  class="pagininput select" >
								<option value="0">Seçiniz..</option>
								

								<?php

								$MALZEME_TIPLERI = DB::getInstance()->query("SELECT * FROM " . DBT_MALZEME_TIPLERI . " ORDER BY malzeme_tipi" )->results();
								foreach( $MALZEME_TIPLERI as $tip ){
									$MTipi = new Malzeme_Tipi( $tip["id"] );
									echo '<option value="'.$MTipi->get_details("malzeme_tipi").'">'.$MTipi->get_details("malzeme_tipi").'</option>';
								}
							?>
							</select>
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
			
			<ul class="parca-data-table">

				
			</ul>

		</div>
	</div>
	

	<script type="text/javascript">

		AHReady(function(){

			var ORG_DATA = <?php echo json_encode($DATA) ?>,
				TABLE_CONTAINER = $AHC('parca-data-table'),
				Parca_Table = new DataTable({
				container : TABLE_CONTAINER,
				pagin_container : $AHC('parca-pagin'),
				filter_container : $AHC('parca-filter'),
				data : ORG_DATA,
				header_keys: [ 'tarih', 'malzeme_tipi', 'adet' ],
				icon_class  : 'ico yakit-big',
				nav_headers : { 'SİL' : { pl: <?php echo Actions::OTOBUS_PARCA_KAYIT_SILME ?>, href : '', class : 'kirmizi'}	},
				data_headers : { 'Adet' : 'adet',
									'Malzeme Tipi' : 'malzeme_tipi',
									'Malzeme Marka' : 'malzeme_marka',
									'Malzeme Açıklama' : 'malzeme_aciklama',
									'Malzeme Birim Fiyat' : 'birim_fiyat',
									'Toplam Fiyat' : 'toplam_fiyat',
									'Tarih' : 'tarih',
									'Açıklama' : 'aciklama' },
				jx_delete: function(targ){
					var kayit_id = targ.getAttribute('item-id');
					if( confirm_alert("Parça kaydını silmek istediğinizden emin misiniz?") ){
						Popup.start_loader();
						AHAJAX_V3.req( Base.AJAX_URL + 'parca_kaydi.php', manual_serialize({type:'sil', item_id:kayit_id}), function(res){
							if( res.ok ){
								Parca_Table.delete_item( 'id', kayit_id ); 
								Parca_Table.init();
								Popup.off();
							}

						});
					}
				},
				filter_inputs: [ 'dt_malzeme_tipi' ]
			});
			console.log( ORG_DATA );

			Parca_Table.init();
			Parca_Table.init_events();


		});

	</script>

<?php
	require 'inc/footer.php';