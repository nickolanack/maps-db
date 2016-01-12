<?php
/**
 * go to http://media.geolive.ca/bc-gov for this example
 */


/**
 * 
 */

include_once __DIR__.'/MapResources.php';
$url='http://apps.gov.bc.ca/pub/dmf-rest-api/resources/sites/';

$server=new MapResources($url);




if(key_exists('site',$_GET)){

	$site=$_GET['site'];

	if(key_exists('layer',$_GET)){

		if(key_exists('cat',$_GET)){

			$layer=$_GET['layer'];
			$cat=$_GET['cat'];
			echo '<h3>'.$server->getResourceArcLayerKmlUrl($site, $layer, $cat).'</h3>'.'<br/>';
			echo htmlspecialchars($server->getResourceArcLayerKml($site, $layer, $cat));


		}else{


			$layer=$_GET['layer'];
			echo '<h3>'.$server->getResourceLayerKmlUrl($site, $layer).'</h3>'.'<br/>';
			echo htmlspecialchars($server->getResourceLayerKml($site, $layer));
		}

	}else{

		echo '<h3>'.$server->getResourceLayersUrl($site).'</h3>'.'<br/>';
		echo implode('<br/>', array_map(function($layer) use($site){
			echo '<pre>'.print_r($layer,true).'</pre>';
			return '<a href="?site='.$site.'&layer='.$layer['id'].(key_exists('catid', $layer)?'&cat='.$layer['catid']:'').'">'.$layer['name'].'</a> | '.
			'<a href="map.php?site='.$site.'&layer='.$layer['id'].(key_exists('catid', $layer)?'&cat='.$layer['catid']:'').'">map</a>';
		}, $server->getResourceLayers($site)));


	}

}else{
	echo $server->getListUrl().'<br/>';
	echo implode('<br/>', array_map(function($site){
		return '<a href="?site='.$site['id'].'">'.$site['name'].'</a>';
	}, $server->getList()));
	
}