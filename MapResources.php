<?php

class MapResources{

	protected $url=null;
	public function __construct($url){

		$this->url=rtrim($url,'/');
	}


	public function getList(){	

		$text= file_get_contents($this->getListUrl());
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
	
	public function getListUrl(){
		return  'http://apps.gov.bc.ca/pub/dmf-rest-api/resources/sites';
	}

	public function getResourceMetadata($site){

		echo $text= file_get_contents($this->url.'/'.$site.'');
		//$doc=new DOMDocument();
		//$doc->loadXml($text);

	}

	public function getResourceLayers($site){

		$text= file_get_contents($this->getResourceLayersUrl($site));
		
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

	public function getResourceLayersUrl($site){
		return $this->url.'/'.$site.'/layers/';
	}
	
	public function getResourceLayerKml($site, $layer){

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
		$response = file_get_contents($this->getResourceLayerKmlUrl($site, $layer), false, $context);
		
		file_put_contents($file, $response);
		
		return $response;

	}

	public function getResourceLayerKmlUrl($site, $layer){
		return $this->url.'/'.$site.'/layers/'.$layer.'/data/';
	}

	public function getResourceArcLayerKml($site, $layer, $cat){

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
		$response = file_get_contents($this->getResourceArcLayerKmlUrl($site, $layer, $cat), false, $context);

		file_put_contents($file, $response);

		return $response;

	}

	public function getResourceArcLayerKmlUrl($site, $layer, $cat){
		return $this->url.'/'.$site.'/layers/'.$layer.'/data/?agsId='.$cat;
	}

	public function getResourceLayerKmlHeaders($site, $layer){
		return get_headers($this->getResourceLayerKmlUrl($site, $layer),1);
	}

}