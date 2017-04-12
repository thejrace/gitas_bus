<?php

	require 'inc/init.php';

	$SAYFA_DATA = array(
		'title' 	=> 'Otobüs Markaları',
		'action_id' => Actions::OTOBUS_MARKALAR_VERI_ERISIM
	);

	// izin kontrolu
	if( !in_array( $SAYFA_DATA["action_id"], Active_User::get_perm_actions() ) ) {
		header("Location: index.php");
	}

	$MARKALAR = DB::getInstance()->query("SELECT * FROM " . DBT_OTOBUS_MARKALARI. " ORDER BY marka")->results();

	$NAV_ACTION_BUTTONS = new Nav_Action_Buttons(
		array(
			array(
				"action" 	=> Actions::OTOBUS_MARKA_EKLE
			),
			array(
				"action" 	=> Actions::OTOBUS_DATA_ERISIM,
				"title"	    => "Otobüslere Git"
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
				<div class="markalar-pagin pagination-center clearfix">			

				</div>
			</div>

			<div class="pagination-container">
				<button type="button" class="pagin-toggle">Filtrele</button>	
				<div class="markalar-filter pagination-center clearfix">
					<form id="filter_form">
						<div class="pagination-col">
							<span>Marka</span>
							<input type="text" class="pagininput text"  id="dt_marka" />
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
			
			<ul class="markalar-data-table">

				
			</ul>

		</div>
	</div>
	

	<script type="text/javascript">

		var ORG_DATA = <?php echo json_encode($MARKALAR) ?>,
			TABLE_CONTAINER = $AHC('markalar-data-table'),
			Marka_Table = new DataTable({
			container : TABLE_CONTAINER,
			pagin_container : $AHC('markalar-pagin'),
			filter_container : $AHC('markalar-filter'),
			data : ORG_DATA,
			header_keys: [ 'marka' ],
			icon_class  : 'ico bus-big',
			nav_headers :
				{'MODELLER':
					{	pl: <?php echo Actions::OTOBUS_MODELLER_VERI_ERISIM ?>,
						href : '<?php echo URL_OTOBUS_MODELLER ?>',
						class : 'turuncu' },
				'MODEL EKLE':
					{ 	pl: <?php echo Actions::OTOBUS_MODEL_EKLE ?>,
						href : '<?php echo URL_OTOBUS_MODEL_EKLE ?>',
						class : 'turuncu' },
				'DÜZENLE' :
					{ 	pl: <?php echo Actions::OTOBUS_MARKA_DUZENLE ?>,
						href : '<?php echo URL_OTOBUS_MARKA_DUZENLE ?>',
						class : 'sari' },
				'SİL' : 
					{ 	pl: <?php echo Actions::OTOBUS_MARKA_SIL ?>,
						href : '',
						class : 'kirmizi'}	},
			data_headers : { 'Marka' : 'marka' },
			jx_delete: function( targ ){
				var kayit_id = targ.getAttribute('item-id');
				if( confirm_alert("Otobüs markasını silmek istediğinizden emin misiniz?") ){
					Popup.start_loader();
					AHAJAX_V3.req( Base.AJAX_URL + 'marka_model.php', manual_serialize({type:'marka_sil', item_id:kayit_id}), function(res){
						if( res.ok ){
							Marka_Table.delete_item( 'id', kayit_id ); 
							Marka_Table.init();
							Popup.off();
						}
					});
				}
			},
			filter_inputs: ['dt_marka']
		});

		AHReady(function(){

			Marka_Table.init();
			Marka_Table.init_events();


		});

	</script>

<?php
	require 'inc/footer.php';