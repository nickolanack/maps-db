<?php
class ArcServer{



	protected $url=null;
	public function __construct($url){

		$this->url=rtrim($url,'/');
		
	}


	public function getList(){
		$tab='';
		$path=$this->url.'/?f=pjson';
		//echo $tab.$path."\n";
		$folders=json_decode($foldersStr=file_get_contents($path));
		//echo $tab.$foldersStr."\n";
		
		$list=array();
		

		foreach($folders->folders as $folder){
			$tab='   ';
			$path=$this->url.'/'.$folder.'/?f=pjson';
			//echo $tab.$path."\n";
			$folderItem=json_decode($folderItemStr=file_get_contents($path));
			//echo $tab.$folderItemStr."\n";
			if(key_exists('services', $folderItem)){
				$tab='      ';
				foreach($folderItem->services as $service){
					if($service->type==='MapServer'){

						$path=$this->url.'/'.$service->name.'/MapServer/?f=pjson';
						//echo $tab.$path."\n";
						$mapServer=json_decode($mapServerStr=file_get_contents($path));
						//echo $tab.$mapServer=file_get_contents($path);

						//echo $mapServerStr;
						$listItem=array(
							'id'=>$service->name,
							'name'=>$mapServer->documentInfo->Title
						);
						if(empty($listItem['name'])){
							$listItem['name']=$mapServer->documentInfo->Subject;
						}

						$list[]=$listItem;

						//break;
					}

				}

				//break;
			}
			

		}
		return $list;

	}

	public function getResourceLayers($site){

		$path=$this->url.'/'.$site.'/MapServer/?f=pjson';
		$mapServer=json_decode($mapServerStr=file_get_contents($path));
		//echo $mapServerStr;
		return array_map(function($layer){
			return array('name'=>$layer->name, 'id'=>$layer->id);
		}, $mapServer->layers);

	}

	public function getResourceLayerKml($site, $layer){

		$path=$this->url.'/'.$site.'/MapServer/'.$layer.'?f=pjson';
		$mapLayer=json_decode($mapLayerStr=file_get_contents($path));
		echo $mapLayerStr;

	}


}