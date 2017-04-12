<?php

    require 'inc/init.php';



    // print_r(Active_User::get_details());

    // DB::getInstance()->query("DELETE FROM orer_kayit WHERE tarih = ?", array( '2017-01-17'));

    // $q = DB::getInstance()->query("SELECT * FROM hat_guzergah_koordinatlari WHERE hat = ? ORDER BY sira", array(3))->results();

    // $str = array();
    // foreach( $q as $kor ){

    // 	$str[] = "{ lat: " . $kor['n_koordinat'] . ", lng:" . $kor['e_koordinat'] . "}";

    // }

    // echo implode( ", ", $str );


    require 'inc/header.php';
?>

<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">

    <title>Simple Polylines</title>
    <style>
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
      #map {
        height: 500px;
        width:1000px;
      }
      /* Optional: Makes the sample page fill the window. */
      html, body {
        height: 100%;
        margin: 0;
        padding: 0;
      }
    </style>
  </head>
  <body>
    <div id="map"></div>
    <script>

      // This example creates a 2-pixel-wide red polyline showing the path of William
      // Kingsford Smith's first trans-Pacific flight between Oakland, CA, and
      // Brisbane, Australia.

      function initMap() {
        var map = new google.maps.Map(document.getElementById('map'), {
          zoom: 3,
          center: {lat: 41.030810656566, lng: 29.032795606061},
          mapTypeId: 'terrain'
        });

        var flightPlanCoordinates = [
         { lat: 41.0036095, lng:28.8151646}, { lat: 41.0027189, lng:28.8155723}, { lat: 41.0019901, lng:28.8158941}, { lat: 41.0015043, lng:28.8163877}, { lat: 41.001229, lng:28.8165808}, { lat: 41.0008242, lng:28.8187051}, { lat: 41.0005975, lng:28.8197351}, { lat: 41.0005813, lng:28.8208079}, { lat: 41.0005489, lng:28.8211298}, { lat: 41.0005003, lng:28.8216448}, { lat: 41.0004193, lng:28.8221598}, { lat: 41.0005327, lng:28.8224816}, { lat: 41.0015043, lng:28.8222027}, { lat: 41.0030589, lng:28.8218164}, { lat: 41.0052126, lng:28.8215375}, { lat: 41.0064433, lng:28.8217735}, { lat: 41.0068886, lng:28.8218808}, { lat: 41.0068157, lng:28.821162}, { lat: 41.0067348, lng:28.8202822}, { lat: 41.0066133, lng:28.8194561}, { lat: 41.0064838, lng:28.8188338}, { lat: 41.0064028, lng:28.8185549}, { lat: 41.0066538, lng:28.818233}, { lat: 41.007172, lng:28.8180399}, { lat: 41.0092527, lng:28.8179541}, { lat: 41.0106695, lng:28.8179433}, { lat: 41.0112524, lng:28.817879}, { lat: 41.0121267, lng:28.8195312}, { lat: 41.0129686, lng:28.8214839}, { lat: 41.0133087, lng:28.8224387}, { lat: 41.0132601, lng:28.8233936}, { lat: 41.0132682, lng:28.8237906}, { lat: 41.0133127, lng:28.8247132}, { lat: 41.0134139, lng:28.8250887}, { lat: 41.013422, lng:28.8252604}, { lat: 41.0132884, lng:28.8254213}, { lat: 41.012491, lng:28.8265747}, { lat: 41.0114628, lng:28.8281465}, { lat: 41.0108233, lng:28.8290852}, { lat: 41.0105035, lng:28.8295358}, { lat: 41.0095401, lng:28.8308609}, { lat: 41.0094429, lng:28.8310432}, { lat: 41.0092284, lng:28.8311076}, { lat: 41.0087952, lng:28.8310057}, { lat: 41.0082488, lng:28.8308179}, { lat: 41.0079006, lng:28.8308287}, { lat: 41.0062206, lng:28.8310647}, { lat: 41.0054434, lng:28.831113}, { lat: 41.0048564, lng:28.8310191}, { lat: 41.0039658, lng:28.8309145}, { lat: 41.0031804, lng:28.8306248}, { lat: 41.0025164, lng:28.8303673}, { lat: 41.001893, lng:28.8302171}, { lat: 41.001225, lng:28.8302171}, { lat: 41.0007554, lng:28.83026}, { lat: 40.9997716, lng:28.8304049}, { lat: 40.9991967, lng:28.8305122}, { lat: 40.9982574, lng:28.8307643}, { lat: 40.997642, lng:28.8309252}, { lat: 40.9973909, lng:28.8311076}, { lat: 40.9966784, lng:28.8312417}, { lat: 40.9958038, lng:28.8312793}, { lat: 40.9953058, lng:28.8313276}, { lat: 40.9949131, lng:28.8313919}, { lat: 40.994743, lng:28.8315797}, { lat: 40.9946134, lng:28.8317406}, { lat: 40.9945446, lng:28.8321}, { lat: 40.9945041, lng:28.8325721}, { lat: 40.9944555, lng:28.8340634}, { lat: 40.9944717, lng:28.8346159}, { lat: 40.9944191, lng:28.8349324}, { lat: 40.9943219, lng:28.8351309}, { lat: 40.9940668, lng:28.8352007}, { lat: 40.9937875, lng:28.8352758}, { lat: 40.993257, lng:28.8354743}, { lat: 40.9927631, lng:28.8355923}, { lat: 40.9918844, lng:28.8356674}, { lat: 40.9914147, lng:28.8358927}, { lat: 40.9911556, lng:28.8363004}, { lat: 40.9910908, lng:28.8367188}, { lat: 40.9912204, lng:28.8370514}, { lat: 40.9913358, lng:28.8371721}, { lat: 40.9917751, lng:28.837679}, { lat: 40.9918581, lng:28.8378453}, { lat: 40.991929, lng:28.8379714}, { lat: 40.9918784, lng:28.8389853}, { lat: 40.991852, lng:28.8393018}, { lat: 40.9917184, lng:28.8411766}, { lat: 40.9917265, lng:28.8418686}, { lat: 40.9915929, lng:28.8442075}, { lat: 40.9915605, lng:28.8447601}, { lat: 40.9914957, lng:28.8463318}, { lat: 40.9915362, lng:28.8498831}, { lat: 40.9916253, lng:28.851707}, { lat: 40.9918277, lng:28.8527691}, { lat: 40.9948483, lng:28.8635731}, { lat: 40.9956257, lng:28.8657618}, { lat: 40.9968727, lng:28.8697314}, { lat: 40.9975205, lng:28.8719416}, { lat: 40.9981035, lng:28.8745809}, { lat: 41.0002574, lng:28.8815975}, { lat: 41.0012938, lng:28.8850093}, { lat: 41.0031399, lng:28.8912106}, { lat: 41.0041925, lng:28.892777}, { lat: 41.0052612, lng:28.8943648}, { lat: 41.0060061, lng:28.8952017}, { lat: 41.0063138, lng:28.8960385}, { lat: 41.0086455, lng:28.8992143}, { lat: 41.0119972, lng:28.9041924}, { lat: 41.0153811, lng:28.9096856}, { lat: 41.0170811, lng:28.9126468}, { lat: 41.0183115, lng:28.9158869}, { lat: 41.0188781, lng:28.918376}, { lat: 41.0191857, lng:28.9198565}, { lat: 41.0194124, lng:28.9213157}, { lat: 41.0194933, lng:28.9228177}, { lat: 41.0195743, lng:28.9238691}, { lat: 41.0193962, lng:28.9245987}, { lat: 41.0189429, lng:28.9255428}, { lat: 41.0137782, lng:28.9360785}, { lat: 41.0131629, lng:28.9373446}, { lat: 41.0127905, lng:28.9381599}, { lat: 41.0120134, lng:28.9392543}, { lat: 41.0116733, lng:28.9403272}, { lat: 41.0104104, lng:28.944962}, { lat: 41.0101351, lng:28.9464641}, { lat: 41.0097303, lng:28.9480519}, { lat: 41.0092446, lng:28.9505625}, { lat: 41.0092284, lng:28.9516997}, { lat: 41.0091798, lng:28.9525366}, { lat: 41.0089855, lng:28.9531159}, { lat: 41.0081435, lng:28.9533412}, { lat: 41.0075768, lng:28.953352}, { lat: 41.0067752, lng:28.9534378}, { lat: 41.0058684, lng:28.9534593}, { lat: 41.0055527, lng:28.9534163}, { lat: 41.0052612, lng:28.9533627}, { lat: 41.0050993, lng:28.9534056}, { lat: 41.0050406, lng:28.9535317}, { lat: 41.0050406, lng:28.9536551}, { lat: 41.0051256, lng:28.953765}, { lat: 41.0052288, lng:28.9537945}, { lat: 41.0053563, lng:28.9537972}, { lat: 41.0056256, lng:28.953765}
        ];
        var flightPath = new google.maps.Polyline({
          path: flightPlanCoordinates,
          geodesic: true,
          strokeColor: '#FF0000',
          strokeOpacity: 1.0,
          strokeWeight: 2
        });

        var lengthInMeters = google.maps.geometry.spherical.computeLength(flightPath.getPath());
    	console.log("polyline is "+lengthInMeters+" long");

        flightPath.setMap(map);
      }
    </script>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBPDDy6ZpsPxE917UsoZDxPELfdOd9ZOKw&callback=initMap&libraries=geometry">
    </script>

  </body>
</html>
