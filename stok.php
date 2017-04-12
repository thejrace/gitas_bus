<?php

	require 'inc/init.php';

	$SAYFA_DATA = array(
		'title' 	=> 'Stok',
		'action_id' => Actions::STOK_ERISIM
	);

	// izin kontrolu
	if( !in_array( $SAYFA_DATA["action_id"], Active_User::get_perm_actions() ) ) {
		header("Location: index.php");
	}

	$MALZEMELER = DB::getInstance()->query("SELECT * FROM " . DBT_MALZEMELER . " ORDER BY id DESC")->results();
	$MALZEME_TIPLERI = DB::getInstance()->query("SELECT * FROM ". DBT_MALZEME_TIPLERI . " ORDER by malzeme_tipi")->results();

	$INNER_TABLE_CONTENTS = array();
	foreach( $MALZEME_TIPLERI as $malzeme_tipi ){

		foreach( $MALZEMELER as $malzeme ){

			if( $malzeme_tipi['malzeme_tipi'] == $malzeme['malzeme_tipi'] ){
				// INNER_TABLE_CONTENTS["Arka Tampon"] = [ {malzeme}, {malzeme}]
				$INNER_TABLE_CONTENTS[$malzeme_tipi['malzeme_tipi']][] = array( 'stok_kodu' => $malzeme['stok_kodu'], 'marka' => $malzeme['marka'], 'aciklama' => $malzeme['aciklama'] );
			}

		}

	}

	//print_r( $INNER_TABLE_CONTENTS);

	$NAV_ACTION_BUTTONS = new Nav_Action_Buttons(
		array(
			array(
				"action" 	=> Actions::MALZEME_EKLE,
				"title" 	=> "+ Yeni Malzeme Ekle"
			),
			array(
				"action" 	=> Actions::MALZEME_TIPI_EKLE,
				"title"     => "+ Yeni Malzeme Tipi Ekle"
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
		<?php echo $SAYFA_DATA["title"] ?>
	</div>

	<div class="section-content">
		<div class="otobus data-table">

			<div class="pagination-container ">
				<button type="button" class="pagin-toggle">Sayfalama</button>
				<div class="pagination-center stok-pagin clearfix">			

				</div>
			</div>


			<div class="pagination-container">
				<button type="button" class="pagin-toggle">Filtrele</button>	
				<div class="pagination-center stok-filter clearfix">
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
							<span>Otobüs Marka</span>
							<select id="dt_marka"  class="pagininput select" >
								<option value="0">Seçiniz..</option>
								

							<?php

								$MARKALAR = DB::getInstance()->query("SELECT * FROM " . DBT_OTOBUS_MARKALARI . " ORDER BY marka" )->results();
								foreach( $MARKALAR as $marka ){
									$OMarka = new Otobus_Markasi( $marka["id"] );
									echo '<option value="'.$OMarka->get_details("marka").'">'.$OMarka->get_details("marka").'</option>';
								}
							?>

							</select>
						</div>
						<div class="pagination-col">
							<span>Açıklama</span>
							<input type="text" class="pagininput text"  name="dt_aciklama" id="dt_aciklama" />
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
			
			<ul class="stok-data-table">

			</ul>

		</div>
	</div>
	

		<script type="text/javascript">


		<?php if( Input::get("parca_kaydi") == "" ) { ?>
			var nav_headers = {
				/*'OTOBÜSE EKLE':
					{ 	pl:<?php echo Actions::OTOBUS_PARCA_VERI_GIRME ?>,
						href : '<?php echo URL_OTOBUSLER ?>?parca_kaydi=true&mid=',
						class : 'turuncu' },*/
				'DÜZENLE' :
					{ 	pl:<?php echo Actions::MALZEME_DUZENLE ?>,
						href : '<?php echo URL_MALZEME_DUZENLE ?>',
						class : 'sari' },
				'SİL' : 
					{ 	pl:<?php echo Actions::MALZEME_SIL ?>,
						href : '',
						class : 'kirmizi'}	};
		<?php } else { ?>

			var nav_headers = 
					{'SEÇ':
						{ 	pl:<?php echo Actions::OTOBUS_PARCA_VERI_GIRME ?>,
							href : '<?php echo URL_PARCA_KAYDI_EKLE ?>?oid=<?php echo Input::get("oid")?>&mid=',
							class : 'turuncu' }	};

		<?php } ?>

		var ORG_DATA = <?php echo json_encode($MALZEME_TIPLERI) ?>,
			TABLE_CONTAINER = $AHC('stok-data-table'),
			Stok_Table = new DataTable({
			container : TABLE_CONTAINER,
			pagin_container : $AHC('stok-pagin'),
			filter_container : $AHC('stok-filter'),
			data : ORG_DATA,
			header_keys: [ 'malzeme_tipi' ],
			icon_class  : 'ico parca-big',
			//nav_headers : nav_headers,
			//data_headers : { 'Malzeme Tipi' : 'malzeme_tipi', 'Marka' : 'marka', 'Birim Fiyat' : 'fiyat', 'Açıklama' : 'aciklama' },
			jx_delete : function( targ ){
				var kayit_id = targ.getAttribute('item-id');
				if( confirm_alert("Malzemeyi silmek istediğinizden emin misiniz?") ){
					Popup.start_loader();
					AHAJAX_V3.req( Base.AJAX_URL + 'malzeme.php', manual_serialize({type:'malzeme_sil', item_id:kayit_id}), function(res){
						if( res.ok ){
							Stok_Table.delete_item( 'id', kayit_id ); 
							Stok_Table.init();
							Popup.off();
						}

					});
				}
			},
			filter_inputs : [ 'dt_malzeme_tipi', 'dt_marka', 'dt_aciklama' ],

			// alt table thead başlıkları
			inner_table_thead: { stok_kodu : 'STOK KODU', marka : 'MARKA', aciklama:'AÇIKLAMA',}, 
			// alt table veriler
			inner_table_contents:<?php echo json_encode($INNER_TABLE_CONTENTS)?>,
			
			// tr lerin sonuna nav butonlar
			inner_table_navs : [ { title:'Düzenle', ico:'edit', href:'<?php echo URL_MALZEME_DUZENLE ?>'}, { title:'Sil', ico:'delete', href:"#"} ],
			// navlara item-id attr yazilacak olan item key
			inner_table_navs_item_key: 'stok_kodu',
			inner_table_parent_key: 'malzeme_tipi'
		});

		AHReady(function(){

			Stok_Table.init();
			Stok_Table.init_events();

		});

	</script>

<?php
	require 'inc/footer.php';