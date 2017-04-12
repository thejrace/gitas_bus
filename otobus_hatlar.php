<?php

	require 'inc/init.php';

	$SAYFA_DATA = array(
		'title' 	=> 'Otobüs Hatları',
		'action_id' => Actions::OTOBUS_HATLAR_ERISIM
	);

	// izin kontrolu
	if( !in_array( $SAYFA_DATA["action_id"], Active_User::get_perm_actions() ) ) {
		header("Location: index.php");
	}



	$NAV_ACTION_BUTTONS = new Nav_Action_Buttons(
		array(
			array(
				"action" 	=> Actions::OTOBUS_HAT_EKLE
			),
			array(
				"action" 	=> Actions::OTOBUS_DATA_ERISIM,
				"title"		=> "Otobüslere Dön"
			)
		)
	);

	$DATA = DB::getInstance()->query("SELECT * FROM " . DBT_HATLAR . " ORDER by hat ")->results();

	require 'inc/header.php';
?>
	
	<div class="section-header">
		<?php echo $SAYFA_DATA["title"] ?>
	</div>

	<div class="section-content">
		<div class="otobus data-table">
			
			<div class="pagination-container ">
				<button type="button" class="pagin-toggle">Sayfalama</button>
				<div class="hat-pagin pagination-center clearfix">			
					
				</div>
			</div>

			<div class="pagination-container">
				<button type="button" class="pagin-toggle">Filtrele</button>	
				<div class="hat-filter pagination-center clearfix">
					<form id="filter_form">
						<div class="pagination-col">
							<span>Hat</span>
							<input type="text" class="pagininput text"  id="dt_hat" />
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
			
			<ul class="hat-data-table">

				
			</ul>

		</div>
	</div>
	

	<script type="text/javascript">

		AHReady(function(){

			var ORG_DATA = <?php echo json_encode($DATA) ?>,
				TABLE_CONTAINER = $AHC('hat-data-table'),
				Yakit_Table = new DataTable({
				container : TABLE_CONTAINER,
				pagin_container : $AHC('hat-pagin'),
				filter_container : $AHC('hat-filter'),
				data : ORG_DATA,
				header_keys: [ 'hat', 'aciklama' ],
				icon_class  : 'ico bus-big',
				nav_headers :
					{
					'İSTATİSTİKLER' :
						{ pl:<?php echo Actions::OTOBUS_SEFER_ISTATISTIK_ERISIM ?>, href : '<?php echo URL_OTOBUS_SEFER_ISTATISTIKLERI ?>?cbf_hat=', class : 'kirmizi' },
					'GÜZERGAH' :
						{ pl:<?php echo Actions::OTOBUS_HAT_GUZERGAH_ERISIM ?>, href : '<?php echo URL_HAT_GUZERGAH ?>', class : 'kirmizi' },
					'DURAKLAR' :
						{ pl:<?php echo Actions::OTOBUS_HAT_DURAKLAR_ERISIM ?>, href : '<?php echo URL_HAT_DURAKLAR ?>', class : 'kirmizi' },
					'DÜZENLE' :
						{ pl:<?php echo Actions::OTOBUS_HAT_DUZENLE ?>, href : '<?php echo URL_OTOBUS_HAT_DUZENLE ?>', class : 'sari' },
					'SİL' : 
						{ pl:<?php echo Actions::OTOBUS_HAT_SIL ?>, href : '', class : 'kirmizi'}	},
				data_headers : { 'Hat' : 'hat', 'Açıklama' : 'aciklama' },
				jx_delete : function( targ ){
					var kayit_id = targ.getAttribute('item-id');
					if( confirm_alert("Hattı silmek istediğinizden emin misiniz?") ){
						Popup.start_loader();
						AHAJAX_V3.req( Base.AJAX_URL + 'hat.php', manual_serialize({type:'sil', item_id:kayit_id}), function(res){
							if( res.ok ){
								Yakit_Table.delete_item( 'id', kayit_id ); 
								Yakit_Table.init();
								Popup.off();
							}
						});
					}
				},
				filter_inputs: [ 'dt_hat' ]
			});


			Yakit_Table.init();
			Yakit_Table.init_events();


		});

	</script>

<?php
	require 'inc/footer.php';