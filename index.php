<?php

/**
 * 
 */

include_once __DIR__.'/ArcServer.php';
$url='http://maps.gov.bc.ca/arcgis/rest/services/';

$server=new ArcServer($url);
if(key_exists('site',$_GET)){

	$site=$_GET['site'];

	if(key_exists('layer',$_GET)){

		


			$layer=$_GET['layer'];
			
			echo htmlspecialchars($server->getResourceLayerKml($site, $layer));

	}else{

		
		echo implode('<br/>', array_map(function($layer) use($site){
			echo '<pre>'.print_r($layer,true).'</pre>';
			return '<a href="?site='.urlencode($site).'&layer='.$layer['id'].(key_exists('catid', $layer)?'&cat='.$layer['catid']:'').'">'.$layer['name'].'</a> | '.
			'<a href="map.php?site='.urlencode($site).'&layer='.$layer['id'].(key_exists('catid', $layer)?'&cat='.$layer['catid']:'').'">map</a>';
		}, $server->getResourceLayers($site)));


	}

}else{
	
	echo implode('<br/>', array_map(function($site){
		return '<a href="?site='.urlencode($site['id']).'">'.$site['name'].'</a>';
	}, $server->getList()));
	
}

