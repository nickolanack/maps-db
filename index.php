<?php

/**
 * 
 */

include_once __DIR__.'/MapResources.php';



if(key_exists('site',$_GET)){

	$site=$_GET['site'];

	if(key_exists('layer',$_GET)){

		if(key_exists('cat',$_GET)){

			$layer=$_GET['layer'];
			$cat=$_GET['cat'];
			echo '<h3>'.MapResources::GetResourceArcLayerKmlUrl($site, $layer, $cat).'</h3>'.'<br/>';
			echo htmlspecialchars(MapResources::GetResourceArcLayerKml($site, $layer, $cat));


		}else{


			$layer=$_GET['layer'];
			echo '<h3>'.MapResources::GetResourceLayerKmlUrl($site, $layer).'</h3>'.'<br/>';
			echo htmlspecialchars(MapResources::GetResourceLayerKml($site, $layer));
		}

	}else{

		echo '<h3>'.MapResources::GetResourceLayersUrl($site).'</h3>'.'<br/>';
		echo implode('<br/>', array_map(function($layer) use($site){
			echo '<pre>'.print_r($layer,true).'</pre>';
			return '<a href="?site='.$site.'&layer='.$layer['id'].(key_exists('catid', $layer)?'&cat='.$layer['catid']:'').'">'.$layer['name'].'</a> | '.
			'<a href="map.php?site='.$site.'&layer='.$layer['id'].(key_exists('catid', $layer)?'&cat='.$layer['catid']:'').'">map</a>';
		}, MapResources::GetResourceLayers($site)));


	}

}else{
	echo MapResources::GetListUrl().'<br/>';
	echo implode('<br/>', array_map(function($site){
		return '<a href="?site='.$site['id'].'">'.$site['name'].'</a>';
	}, MapResources::GetList()));
	
}