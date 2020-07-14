<?php

class Converter
{
	public static function convertVideo(string $link): void
	{

		$getID3 = new getID3;
		$fileData = $getID3->analyze($link);

		
		if (!isset($fileData['video']['fourcc_lookup']) || !preg_match('/H[.]264/iu', $fileData['video']['fourcc_lookup'])) {

	        $gearmanClient = new GearmanCLient();
	        $gearmanClient->addServer();

	        $res = $gearmanClient->doBackground('convertVideo', $link);
        }	
	}
}