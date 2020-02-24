<?php

class Converter
{
	public static function convertVideo(string $link): void
	{
		$getID3 = new getID3;
		$fileData = $getID3->analyze($link);
		
		if (!isset($fileData['video']['fourcc_lookup']) || !preg_match('/H[.]264/iu', $fileData['video']['fourcc_lookup'])) {
	        $newLink = preg_replace('/[.]\\w*/', '.mp4', $link);

	        $process = new Process(['ffmpeg', '-i', $link, '-q:v', '1', '-c:v', 'h264', $newLink]);
	        $process->run();

	        if (!$process->isSuccessful()) {
	            //echo $process->getErrorOutput();
	            //Добавить сохранение логов
	            //Добавить вывод ошибки при загрузке

	            echo "error convert";
	            exit;
	        }

	        unlink($link);
        }		
	}
}