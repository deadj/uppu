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

			// echo "gfdgfdg";
			// exit;
	        $res = $gearmanClient->doBackground('convertVideo', $link);
	        // echo $res;
	        // exit;
	        

	        // if (!$process->isSuccessful()) {
	        //     //echo $process->getErrorOutput();
	        //     //Добавить сохранение логов
	        //     //Добавить вывод ошибки при загрузке

	        //     echo "error convert";
	        //     exit;
	        // } 
        } else {
        	echo "ihgjfihf";
        	exit;
        }		
	}
}