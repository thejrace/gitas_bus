<?php

	require 'inc/init.php';

	$SAYFA_DATA = array(
		'title' 	=> ' Otobüs Modelleri',
		'action_id' => Actions::OTOBUS_MODELLER_VERI_ERISIM
	);

	// izin kontrolu
	if( !in_array( $SAYFA_DATA["action_id"], Active_User::get_perm_actions() ) ) {
		header("Location: index.php");
	}

	
	$MARKA = new Otobus_Markasi(Input::get("mid"));
	$MODELLER = $MARKA->get_models();

	$NAV_ACTION_BUTTONS = new Nav_Action_Buttons(
		array(
			
			array(
				"action" 	=> Actions::OTOBUS_MODEL_EKLE,
				"url"		=> URL_OTOBUS_MODEL_EKLE.Input::get("mid")
			),
			array(
				"action" 	=> Actions::OTOBUS_MARKALAR_VERI_ERISIM,
				"title"		=> "Markalara Dön"
			),
			array(
				"action" 	=> Actions::OTOBUS_DATA_ERISIM,
				"title"		=> "Otobüslere Git"
			)
		)
	);

	require 'inc/header.php';
?>
	
	<div class="section-header">
		<?php echo $MARKA->get_details('marka') . $SAYFA_DATA["title"] ?>
	</div>

	<div class="section-content">
		<div class="otobus data-table">

			<div class="pagination-container ">
				<button type="button" class="pagin-toggle">Sayfalama</button>
				<div class="modeller-pagin pagination-center clearfix">			
					
				</div>
			</div>

			<div class="pagination-container">
				<button type="button" class="pagin-toggle">Filtrele</button>	
				<div class="modeller-filter pagination-center clearfix">
					<form id="filter_form">
						<div class="pagination-col">
							<span>Model</span>
							<input type="text" class="pagininput text"  id="dt_model" />
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
			
			<ul class="modeller-data-table">

				
			</ul>

		</div>
	</div>
	

	<script type="text/javascript">

		var ORG_DATA = <?php echo json_encode($MODELLER) ?>,
			TABLE_CONTAINER = $AHC('modeller-data-table'),
			Model_Table = new DataTable({
			container : TABLE_CONTAINER,
			pagin_container : $AHC('modeller-pagin'),
			filter_container : $AHC('modeller-filter'),
			data : ORG_DATA,
			header_keys: [ 'model' ],
			icon_class  : 'ico bus-big',
			nav_headers :
				{'DÜZENLE' :
					{ 	pl: <?php echo Actions::OTOBUS_MODEL_DUZENLE ?>,
						href : '<?php echo URL_OTOBUS_MODEL_DUZENLE ?>',
						class : 'sari' },
				'SİL' : 
					{ 	pl: <?php echo Actions::OTOBUS_MODEL_SIL ?>,
						href : '',
						class : 'kirmizi'}	},
			data_headers : { 'Model' : 'model' },
			jx_delete: function( targ ){
				var kayit_id = targ.getAttribute('item-id');
				if( confirm_alert("Otobüs modelini silmek istediğinizden emin misiniz?") ){
					Popup.start_loader();
					AHAJAX_V3.req( Base.AJAX_URL + 'marka_model.php', manual_serialize({type:'model_sil', marka_id : <?php echo Input::get("mid")?> ,item_id:kayit_id}), function(res){
						if( res.ok ){
							Model_Table.delete_item( 'id', kayit_id ); 
							Model_Table.init();
							Popup.off();
						}

					});
				}
			},
			filter_inputs: [ 'dt_model']
		});

		AHReady(function(){

			Model_Table.init();
			Model_Table.init_events();
		});

	</script>

<?php
	require 'inc/footer.php';