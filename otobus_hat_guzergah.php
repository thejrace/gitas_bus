<?php

	require 'inc/init.php';

	$SAYFA_DATA = array(
		'title' 	=> 'Güzergah',
		'action_id' => Actions::OTOBUS_HAT_GUZERGAH_ERISIM
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

		
	</div>
	

	<script type="text/javascript">

		ymaps.ready(init);

		function init() {
		    var myMap = new ymaps.Map("harita", {
		            center: [<?php echo $MERKEZ_KOORDS['n_koordinat']?>, <?php echo $MERKEZ_KOORDS['e_koordinat']?>],
		            zoom: 10
		        });
		    var myGeoObject = new ymaps.GeoObject({
		            geometry: {
		                type: "LineString",
		                coordinates: [
		                    <?php echo $HAT->get_guzergah_koords() ?>
		                ]
		            }
		        }, {
		            draggable: false,
		            strokeColor: "#ff0000",
		            strokeWidth: 5
		        });
		    myMap.geoObjects
		        .add(myGeoObject)

		    // yandex butonlarini temizledim
		    var sol_ust = find_elem( $AH('harita'), '.ymaps-2-1-45-controls__control_toolbar');
		    for( var i = 0; i < sol_ust.length; i++ ) hide(sol_ust[i]);
		    hide( $AHC('ymaps-2-1-45-map-copyrights-promo'));
		    hide( $AHC('ymaps-2-1-45-copyright__text'));
		    hide( $AHC('ymaps-2-1-45-copyright__agreement'));
		    remove_elem( $AHC('map-loader'));
		}

		AHReady(function(){

			

		});

	</script>

<?php
	require 'inc/footer.php';