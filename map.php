<html>
<head>
<script
	src="https://s3-us-west-2.amazonaws.com/nickolanackbucket/mootools/mootools-core.js"
	type="text/javascript"></script>
<script
	src="https://s3-us-west-2.amazonaws.com/nickolanackbucket/mootools/mootools-more.js"
	type="text/javascript"></script>
<script
	src="https://s3-us-west-2.amazonaws.com/nickolanackbucket/mootools/mootools_compat.js"
	type="text/javascript"></script>
<script type="text/javascript"
	src="bower_components/js-simplekml/KmlReader.js">
    </script>


<style>
#map {
	width: 100%;
	height: 100%;
}

body {
	margin: 0;
}


</style>
</head>
<body>



	<div id="map"></div>


	<script type="text/javascript">


function initMap(){



var map = new google.maps.Map(document.getElementById('map'), {
	disableDefaultUI:true,
    center: {
        lat:50.50359739949432,
        lng:-125.25016503906248
        },
    zoom: 6,
    mapTypeId:google.maps.MapTypeId.ROADMAP,
    panControl:true,
    zoomControl:true
  });

var layer=<?php 


if(key_exists('site',$_GET)){

	$site=$_GET['site'];

	if(key_exists('layer',$_GET)){

		include_once __DIR__.'/MapResources.php';

		if(key_exists('cat',$_GET)){

			$layer=$_GET['layer'];
			$cat=$_GET['cat'];
			
			echo json_encode(MapResources::GetResourceArcLayerKml($site, $layer, $cat));


		}else{


			$layer=$_GET['layer'];
			echo json_encode(MapResources::GetResourceLayerKml($site, $layer));
		}
	}
}





?>;



var infowindow = new google.maps.InfoWindow({
  });


(new KmlReader(layer)).parsePolygons(function(polygonParams, xmlSnippet){
	//console.log(polygonParams);

	var polygon= new google.maps.Polygon((function(){
		var polygonOpts={
				paths:(function(){


					var paths=polygonParams.coordinates.map(function(coord){
						return {lat:parseFloat(coord[0]), lng:parseFloat(coord[1])};
					});
					return paths;
				})(),
				fillColor:'rgba(100, 149, 237, 0.70)', //cornflowerblue;
				fillOpacity:0.7,
				strokeColor:'#000000',
				strokeWeight:1,
				strokeOpacity:0.7
		};
		return polygonOpts;
	})());

	polygon.setMap(map);

	google.maps.event.addListener(polygon, 'click',function(e){
		infowindow.setContent(polygonParams.description||'');
		infowindow.setPosition(polygon.getPath().getAt(0));
		infowindow.open(map);
	});


}).parseMarkers(function(markerParams, xmlSnippet){
	//console.log(polygonParams);

	var marker= new google.maps.Marker((function(){
		var markerOpts={
			position: {lat:parseFloat(markerParams.coordinates[0]), lng:parseFloat(markerParams.coordinates[1])},
			title: markerParams.name||'unnammed'
		};

		// if(markerParams.icon){
		 	markerOpts.icon='https://storage.googleapis.com/support-kms-prod/SNP_2752125_en_v0';//markerParams.icon;
		// }
		
		return markerOpts;
	})());

	marker.setMap(map);

	google.maps.event.addListener(marker, 'click',function(e){
		infowindow.setContent(markerParams.description||'');
		infowindow.open(map, marker);
	});

});



}

   </script>
	<script async defer
		src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBnTJCsJO2piovlyQfpmemfQXVjwkdB7R4&callback=initMap"></script>
</body>

</html>
