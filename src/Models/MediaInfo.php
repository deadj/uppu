<?php

class MediaInfo 
{
	public static function getMetadata(string $type, string $link): array
	{	
		$getID3 = new getID3;
		$fileData = $getID3->analyze($link);
		$data = array();

		if ($type == File::TYPE_VIDEO) {
			$data = array(
				array(
					"dataName" => "Ширина", 
					"value" => $fileData['video']['resolution_x']
				), array(
					"dataName" => "Высота", 
					"value" => $fileData['video']['resolution_y']
				), array(
					"dataName" => "Кадр/сек", 
					"value" => $fileData['video']['frame_rate']
				), array(
					"dataName" => "Время", 
					"value" => $fileData['playtime_string']
				)
			);
		} elseif ($type == File::TYPE_IMAGE) {
			$data = array(
				array(
					"dataName" => "Шинина", 
					"value" => $fileData['video']['resolution_x']
				), array(
					"dataName" => "Высота", 
					"value" => $fileData['video']['resolution_y']
				)
			);			
		} elseif ($type == File::TYPE_AUDIO) {
			$data = array(
				array(
					"dataName" => "Битрейт", 
					"value" => $fileData['audio']['bitrate'] / 1000
				), array(
					"dataName" => "Время", 
					"value" => $fileData['playtime_string']
				)
			);
		} 

		return $data;
	}

	public static function getSize(string $link): float
	{
		return filesize($link) / 1000;
	}

	public function getNullMetadataForVideo(): array
	{
		return array(
			array(
				"dataName" => "Ширина", 
				"value" => 0
			), array(
				"dataName" => "Высота", 
				"value" => 0
			), array(
				"dataName" => "Кадр/сек", 
				"value" => 0
			), array(
				"dataName" => "Время", 
				"value" => 0
			)
		);		
	}
}