<?php

class MediaInfo 
{
	public static function getMetadata(string $type, string $link): array
	{	
		$getID3 = new getID3;
		$fileData = $getID3->analyze($link);
		$data = array();

		if ($type == "video") {
			$data = array(
				array(
					"dataName" => strval("Ширина"), 
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
		} elseif ($type == "image") {
			$data = array(
				array(
					"dataName" => "Шинина", 
					"value" => $fileData['video']['resolution_x']
				), array(
					"dataName" => "Высота", 
					"value" => $fileData['video']['resolution_y']
				)
			);			
		} elseif ($type == "audio") {
			$data = array(
				array(
					"dataName" => "Битрейт", 
					"value" => $fileData['audio']['bitrade'] / 1000
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
		$getID3 = new getID3;
		$fileData = $getID3->analyze($link);

		return $fileData['filesize'] / 1000;
	}
}