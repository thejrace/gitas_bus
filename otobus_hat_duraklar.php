<?php

	require 'inc/init.php';

	$SAYFA_DATA = array(
		'title' 	=> 'Duraklar',
		'action_id' => Actions::OTOBUS_HAT_DURAKLAR_ERISIM
	);

	// izin kontrolu
	if( !in_array( $SAYFA_DATA["action_id"], Active_User::get_perm_actions() ) ) {
		header("Location: index.php");
	}

	if( Input::get("hid") == "" ) header("Location: index.php");

	$HAT = new Hat( Input::get("hid") );
	$MERKEZ_KOORDS = $HAT->get_merkez_koords();



	$YANDEXMAPAPI = true;



	require 'inc/header.php';
?>
	
	<div class="section-header">
		<?php echo $HAT->get_details("hat") . " " . $SAYFA_DATA["title"] ?>
		<div><?php echo $HAT->get_details('aciklama') ?></div>
	</div>

	<div class="section-content">
		<div id="harita">
				
			<img class="map-loader" src="http://ahsaphobby.net/granit/res/img/rolling.gif"  />

		</div>


		<div class="durak-listesi koordinat-table-container">
			<div class="section-header">Duraklar</div>
			<table>
				<thead>
					<tr>
						<th>Sıra</th>
						<th>Kod</th>
						<th>Durak</th>
						<th>N Koordinat</th>
						<th>E Koordinat</th>
						<th></th>
						<th></th>
					</tr>
				</thead>

				<tbody id="durak_listesi">
					
					<tr>
						<td><input type="text" disabled value="1" class="pagininput text" /></td>
						<td><input type="text" disabled value="283719" class="pagininput text" /></td>
						<td><input type="text" disabled value="Alvarlızade Camii" class="pagininput text" /></td>
						<td><input type="text" disabled value="28.93818381" class="pagininput text" /></td>
						<td><input type="text" disabled value="41.31458300" class="pagininput text" /></td>
						<td><button type="button" class="filterbtn kirmizi"  id="filter_uygula">KAYDET</button></td>
						<td><button type="button" class="filterbtn gri"  id="filter_reset">GERİ AL</button></td>
					</tr>

				</tbody>

			</table>

		</div>


	</div>
	

	<script type="text/javascript">

		ymaps.ready(init);

		var DURAK_DATA = <?php echo json_encode( $HAT->get_durak_koords( true ) ) ?>;


		function init() {
			var map = new ymaps.Map('harita', {
		        center: [<?php echo $MERKEZ_KOORDS['n_koordinat']?>, <?php echo $MERKEZ_KOORDS['e_koordinat']?>],
		        zoom: 10,
		        controls: []
		    });

		 
		    var circleLayout = ymaps.templateLayoutFactory.createClass('<div class="placemark-durak"><div class="circle-layout">D</div></div>');
		    <?php echo $HAT->get_durak_koords() ?>

		    // yandex butonlarini temizledim
		    var sol_ust = find_elem( $AH('harita'), '.ymaps-2-1-45-controls__control_toolbar');
		    for( var i = 0; i < sol_ust.length; i++ ) hide(sol_ust[i]);
		    hide( $AHC('ymaps-2-1-45-map-copyrights-promo'));
		    hide( $AHC('ymaps-2-1-45-copyright__text'));
		    hide( $AHC('ymaps-2-1-45-copyright__agreement'));
		    remove_elem( $AHC('map-loader'));
		}

		AHReady(function(){

			var durak_html = "";
			for( var x = 0; x < DURAK_DATA.length; x++ ){
				durak_html += '<tr>'+
						'<td><input type="text" id="'+DURAK_DATA[x].id+'_sira" disabled value="'+DURAK_DATA[x].sira+'" class="pagininput text"  style="width:40px"/></td>'+
						'<td><input type="text" id="'+DURAK_DATA[x].id+'_kod" disabled value="'+DURAK_DATA[x].kod+'" class="pagininput text" style="width:80px" /></td>'+
						'<td><input type="text" id="'+DURAK_DATA[x].id+'_durak" disabled value="'+DURAK_DATA[x].ad+'" class="pagininput text"  style="width:95%"/></td>'+
						'<td><input type="text" id="'+DURAK_DATA[x].id+'_nkoord" disabled value="'+DURAK_DATA[x].n_koordinat+'" class="pagininput text" style="width:100px"/></td>'+
						'<td><input type="text" id="'+DURAK_DATA[x].id+'_ekoord" disabled value="'+DURAK_DATA[x].e_koordinat+'" class="pagininput text" style="width:100px"/></td>'+
						'<td><button type="button" class="filterbtn kirmizi"  id="filter_uygula">KAYDET</button></td>'+
						'<td><button type="button" class="filterbtn gri"  id="filter_reset">GERİ AL</button></td>'+
					'</tr>';
			}
			set_html($AH('durak_listesi'), durak_html );

		});


	</script>

<?php
	require 'inc/footer.php';