<?php

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class Converter
{
	private $pdo;

	public function __construct($pdo)
	{
		$this->pdo = $pdo;
	}

	public function convert(string $link): void
	{
		$getID3 = new getID3;
		$fileData = $getID3->analyze($link);

		$filesTable = new FilesTable($this->pdo);

	    $nameId = preg_replace('~files\\/\\d{2}_\\d{2}_\\d{2}\\/~ui',"", $link);
	    $nameId = preg_replace('/[.]\\S*/', "", $nameId);
	    $linkForConvert = "public/" . $link;
	    $newLinkForConvert = preg_replace('/[.]\\w*/', 'buf.mp4', $linkForConvert);

		$process = new Process(['ffmpeg', '-i', $linkForConvert, '-q:v', '1', '-c:v', 'h264', '-y', $newLinkForConvert]);
	    $process->run();

	    if ($process->isSuccessful()) {
	        $metadata = MediaInfo::getMetadata("video", $newLinkForConvert);
	        $size = MediaInfo::getSize($newLinkForConvert);

	        $filesTable->updateMetadata($nameId, $metadata, $size, 1);
	    } else {
	        $filesTable->updateMetadata($nameId, array(), 0, 2);
	    }

    	unlink($linkForConvert);
    	rename($newLinkForConvert, str_replace('buf', '', $newLinkForConvert));
	}				
}