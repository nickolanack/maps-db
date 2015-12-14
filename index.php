<?php

class MapResources{


	public static function GetList(){	

		$text= file_get_contents(self::GetListUrl());
		$doc=new DOMDocument();
		$doc->loadXml($text);


		$siteEntries=$doc->getElementsByTagName('SiteEntry');


		$siteEntriesArr=[];

		foreach($siteEntries as $siteEntry){
			$siteEntriesArr[]=$siteEntry;
		}


		return array_map(function($siteEntry){

			return array(
				'id'=>$siteEntry->getElementsByTagName('siteId')->item(0)->nodeValue, 
				'name'=>$siteEntry->getElementsByTagName('siteName')->item(0)->nodeValue 
				);

		}, $siteEntriesArr);

	}
	
	public static function GetListUrl(){
		return  'http://apps.gov.bc.ca/pub/dmf-rest-api/resources/sites';
	}

	public static function GetResourceMetadata($site){

		echo $text= file_get_contents('http://apps.gov.bc.ca/pub/dmf-rest-api/resources/sites/'.$site.'');
		//$doc=new DOMDocument();
		//$doc->loadXml($text);

	}

	public static function GetResourceLayers($site){

		$text= file_get_contents(self::GetResourceLayersUrl($site));
		
		$doc=new DOMDocument();
		$doc->loadXml($text);

		$layers=array();
		foreach($doc->getElementsByTagName('kmlLayer') as $layerElement){
			$layers[]=array(
				'id'=>$layerElement->getElementsByTagName('id')->item(0)->nodeValue,
				'name'=>$layerElement->getElementsByTagName('name')->item(0)->nodeValue,
			);
		}


		return $layers;

	}

	public static function GetResourceLayersUrl($site){
		return 'http://apps.gov.bc.ca/pub/dmf-rest-api/resources/sites/'.$site.'/layers/';
	}
	


	public static function GetResourceLayerKml($site, $layer){

		//echo 'kml for site: '.$site.' layer: '.$layer."\n";
		return (file_get_contents(self::GetResourceLayerKmlUrl($site, $layer)));

	}

	public static function GetResourceLayerKmlUrl($site, $layer){
		return 'http://apps.gov.bc.ca/pub/dmf-rest-api/resources/sites/'.$site.'/layers/'.$layer.'/data/?media';
	}

	public static function GetResourceLayerKmlHeaders($site, $layer){
		return get_headers(self::GetResourceLayerKmlUrl($site, $layer),1);
	}

}



if(key_exists('site',$_GET)){

	$site=$_GET['site'];

	if(key_exists('layer',$_GET)){

		$layer=$_GET['layer'];
		echo MapResources::GetResourceLayerKmlUrl($site, $layer);
		echo MapResources::GetResourceLayerKml($site, $layer);

	}else{

		echo MapResources::GetResourceLayersUrl($site).'<br/>';
		echo implode('<br/>', array_map(function($layer) use($site){
			return '<a href="?site='.$site.'&layer='.$layer['id'].'">'.$layer['name'].'</a>';
		}, MapResources::GetResourceLayers($site)));


	}

}else{
	echo MapResources::GetListUrl().'<br/>';
	echo implode('<br/>', array_map(function($site){
		return '<a href="?site='.$site['id'].'">'.$site['name'].'</a>';
	}, MapResources::GetList()));
	
}