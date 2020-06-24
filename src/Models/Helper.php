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

        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        return preg_replace('/[.].*/', '', $filename);
    }
}