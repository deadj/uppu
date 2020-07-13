<?php

use Slim\Http\UploadedFile;

class Helper
{
	public function moveUploadedFile(string $directory, UploadedFile $uploadedFile, string $extension): string
    {
        if ($extension == "php" || $extension == "phtml") {
            $extension = "txt";
        }
        
        $basename = bin2hex(random_bytes(8));
        $filename = sprintf('%s.%0.8s', $basename, $extension);

        // $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        $tusClient = new \TusPhp\Tus\Client('http://tus.com/TusServer.php');
        $tusClient->setApiPath('http://tus.com/TusServer.php');

        $uploadKey = uniqid();
        $tusClient->setKey($uploadKey)->file($uploadedFile->file, $filename);

        $fileSize = $tusClient->getFileSize();

        while ($tusClient->getOffset() != $fileSize) {
            $tusClient->upload(5000000);
        }

        return preg_replace('/[.].*/', '', $filename);
    }
}

