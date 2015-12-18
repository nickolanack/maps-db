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


		foreach($doc->getElementsByTagName('networkLinkLayer') as $layerElement){
			$layers[]=array(
				'id'=>$layerElement->getElementsByTagName('id')->item(0)->nodeValue,
				'name'=>$layerElement->getElementsByTagName('name')->item(0)->nodeValue,
			);
		}


		foreach($doc->getElementsByTagName('agsLayerCatalogLayer') as $layerCatalogElement){

				$catalog=array(
					'catid'=>$layerCatalogElement->getElementsByTagName('id')->item(0)->nodeValue,
					'catname'=>$layerCatalogElement->getElementsByTagName('name')->item(0)->nodeValue
				);

			foreach($layerCatalogElement->getElementsByTagName('subLayer') as $layerElement){
				$layers[]=array_merge(array(
					'id'=>$layerElement->getElementsByTagName('id')->item(0)->nodeValue,
					'name'=>$layerElement->getElementsByTagName('name')->item(0)->nodeValue
				),$catalog);
			}	

		}
				

		




		if(empty($layers)){

			print_r($text);
			throw new Exception('No layers');

		}

		return $layers;

	}

	public static function GetResourceLayersUrl($site){
		return 'http://apps.gov.bc.ca/pub/dmf-rest-api/resources/sites/'.$site.'/layers/';
	}
	
	public static function GetResourceLayerKml($site, $layer){

		//echo 'kml for site: '.$site.' layer: '.$layer."\n";
		$file=__DIR__.'/'.$site.'-'.$layer.'.kml';
		if(file_exists($file)){
			return file_get_contents($file);
		}
		//
		//
		$opts = array(
		  'http'=>array(
		    'method'=>"GET",
		    'header'=>"Accept: application/xml;\r\n"
		  )
		);

		$context = stream_context_create($opts);
		$response = file_get_contents(self::GetResourceLayerKmlUrl($site, $layer), false, $context);
		
		file_put_contents($file, $response);
		
		return $response;

	}

	public static function GetResourceLayerKmlUrl($site, $layer){
		return 'http://apps.gov.bc.ca/pub/dmf-rest-api/resources/sites/'.$site.'/layers/'.$layer.'/data/';
	}

	public static function GetResourceArcLayerKml($site, $layer, $cat){

		//echo 'kml for site: '.$site.' layer: '.$layer."\n";
		$file=__DIR__.'/'.$site.'-'.$layer.'-'.$cat.'.kml';
		if(file_exists($file)){
			return file_get_contents($file);
		}

		$opts = array(
		  'http'=>array(
		    'method'=>"GET",
		    'header'=>"Accept: application/xml;\r\n"
		  )
		);

		$context = stream_context_create($opts);
		$response = file_get_contents(self::GetResourceArcLayerKmlUrl($site, $layer, $cat), false, $context);

		file_put_contents($file, $response);

		return $response;

	}

	public static function GetResourceArcLayerKmlUrl($site, $layer, $cat){
		return 'http://apps.gov.bc.ca/pub/dmf-rest-api/resources/sites/'.$site.'/layers/'.$layer.'/data/?agsId='.$cat;
	}

	public static function GetResourceLayerKmlHeaders($site, $layer){
		return get_headers(self::GetResourceLayerKmlUrl($site, $layer),1);
	}

}