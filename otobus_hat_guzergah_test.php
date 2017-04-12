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
		<div>Uzunluk: <?php echo ( $HAT->get_details('uzunluk') / 1000 ) ?> KM</div>
	</div>

	<div class="section-content">
		
		<div id="harita">
				
			<img class="map-loader" src="http://ahsaphobby.net/granit/res/img/rolling.gif"  />

		</div>

		
	</div>
	

	<script type="text/javascript">


		function initMap() {
	        var map = new google.maps.Map(document.getElementById('harita'), {
	          zoom: 9,
	          center: {lat: <?php echo $MERKEZ_KOORDS['latitude'] ?> , lng: <?php echo $MERKEZ_KOORDS['longitude'] ?>},
	          mapTypeId: 'terrain'
	        });

	        var flightPlanCoordinates = [ <?php echo $HAT->get_guzergah_koords_google() ?> ];
	        var flightPath = new google.maps.Polyline({
	          path: flightPlanCoordinates,
	          geodesic: true,
	          strokeColor: '#FF0000',
	          strokeOpacity: 1.0,
	          strokeWeight: 2
	        });

	        var lengthInMeters = google.maps.geometry.spherical.computeLength(flightPath.getPath());
	    	console.log("uzunluk "+lengthInMeters+" metre");

	        flightPath.setMap(map);
      	}

		AHReady(function(){

			

		});

	</script>
	<script async defer src="https://maps.googleapis.com/maps/api/js?sensor=false&key=AIzaSyBPDDy6ZpsPxE917UsoZDxPELfdOd9ZOKw&callback=initMap&libraries=geometry"></script>

<?php
	require 'inc/footer.php';