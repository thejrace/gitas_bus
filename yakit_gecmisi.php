<?php

	require 'inc/init.php';

	$SAYFA_DATA = array(
		'title' 	=> 'Yakıt Geçmişi',
		'action_id' => Actions::YAKIT_GECMISI_DATA_ERISIM
	);

	// izin kontrolu
	if( !in_array( $SAYFA_DATA["action_id"], Active_User::get_perm_actions() ) ) {
		header("Location: index.php");
	}

	if( Input::get("oid") == "" ) header("Location: index.php");


	$OTOBUS = new Otobus( Input::get("oid") );
	$DATA = DB::getInstance()->query("SELECT * FROM " . DBT_OTOBUS_YAKIT_GECMISLERI . " WHERE otobus_id = ? ORDER BY tarih DESC",array(Input::get("oid")))->results();
	foreach( $DATA as $key => $val ){
		$DATA[$key]['fiyat'] = Common::dot_to_comma($DATA[$key]['fiyat']) . " TL ";
		$DATA[$key]['tarih'] = Common::date_reverse($DATA[$key]['tarih']);
	}

	$NAV_ACTION_BUTTONS = new Nav_Action_Buttons(
		array(
			array(
				"action" 	=> Actions::OTOBUS_YAKIT_VERI_GIRME,
				"url"		=> URL_YAKIT_KAYDI_EKLE.Input::get("oid")
			),
			array(
				"action" 	=> Actions::OTOBUS_DATA_ERISIM,
				"title"		=> "Otobüslere Dön"
			)
		)
	);

	require 'inc/header.php';
?>
	
	<div class="section-header">
		"<?php echo $OTOBUS->get_details("kod") ?>" Hat Kodlu Otobüsün <?php echo $SAYFA_DATA["title"] ?>
	</div>

	<div class="section-content">
		<div class="otobus data-table">

			<div class="pagination-container ">
				<button type="button" class="pagin-toggle">Sayfalama</button>
				<div class="yakit-pagin pagination-center clearfix">			
					
				</div>
			</div>


<!-- 			<div class="pagination-container">
				<button type="button" class="pagin-toggle">Filtrele</button>	
				<div class="pagination-center clearfix">
					<div class="pagination-col">
						<span>Tarih Aralığı</span>
						
					</div>
					<div class="pagination-col">
						<button type="button" class="filterbtn kirmizi"  id="filter_uygula">UYGULA</button>
					</div>
				</div>
			</div> -->

			<div class="dt-nav-container">
				<?php echo $NAV_ACTION_BUTTONS->get_buttons() ?>
			</div>
			
			<ul class="yakit-data-table">

				
			</ul>

		</div>
	</div>
	

	<script type="text/javascript">

		AHReady(function(){

			var ORG_DATA = <?php echo json_encode($DATA) ?>,
				TABLE_CONTAINER = $AHC('yakit-data-table'),
				Yakit_Table = new DataTable({
				container : TABLE_CONTAINER,
				pagin_container : $AHC('yakit-pagin'),
				data : ORG_DATA,
				header_keys: [ 'tarih', 'fiyat' ],
				icon_class  : 'ico yakit-big',
				nav_headers :
					{'DÜZENLE' :
						{ pl:<?php echo Actions::YAKIT_GIRISI_DUZENLEME ?>, href : '<?php echo URL_YAKIT_KAYDI_DUZENLE ?>', class : 'sari' },
					'SİL' : 
						{ pl:<?php echo Actions::YAKIT_GIRISI_SIL ?>, href : '', class : 'kirmizi'}	},
				data_headers : { 'Fiyat' : 'fiyat', 'Tarih' : 'tarih', 'Miktar' : 'miktar' },
				jx_delete: function(targ){
					var kayit_id = targ.getAttribute('item-id');
					if( confirm_alert("Yakıt girişini silmek istediğinizden emin misiniz?") ){
						Popup.start_loader();
						AHAJAX_V3.req( Base.AJAX_URL + 'yakit_kaydi.php', manual_serialize({type:'sil', item_id:kayit_id}), function(res){
							if( res.ok ){
								Yakit_Table.delete_item( 'id', kayit_id ); 
								Yakit_Table.init();
								Popup.off();
							}
						});
					}
				}
			});


			Yakit_Table.init();
			Yakit_Table.init_events();


		});

	</script>

<?php
	require 'inc/footer.php';