<?php

use Slim\Http\UploadedFile;

class Helper
{
    const EXTENSION_PNG  = "png";
    const EXTENSION_JPG  = "jpg";
    const EXTENSION_JPEG = "jpeg";
    const EXTENSION_GIF  = "gif";
    const EXTENSION_BMP  = "bmp";
    const EXTENSION_WEBP = "webp";

    const EXTENSIONS_ARRAY = array(
        Helper::EXTENSION_PNG,
        Helper::EXTENSION_JPG,
        Helper::EXTENSION_JPEG,
        Helper::EXTENSION_GIF,
        Helper::EXTENSION_BMP,
        Helper::EXTENSION_WEBP
    );

    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

	public function moveUploadedFile(
        string $directory, 
        UploadedFile $uploadedFile, 
        string $extension
    ): string
    {
        if ($extension == "php" || $extension == "phtml") {
            $extension = "txt";
        }
        
        $basename = bin2hex(random_bytes(8));
        $filename = sprintf('%s.%0.8s', $basename, $extension);

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

    public function createImagePreview(string $link, string $nameId): void 
    {   
        $link = '../public/' . $link;
        $savePath = '../public/files/imgPreviews/' . $nameId;
        $fileExtension = pathinfo($link, PATHINFO_EXTENSION);

        foreach (Helper::EXTENSIONS_ARRAY as $extension) {
            if ($fileExtension == $extension) {
                if ($fileExtension == Helper::EXTENSION_JPG) {
                    $fileExtension = "jpeg";
                    $createImgFunction = 'imagecreatefromjpeg';
                } else {
                    $createImgFunction = 'imagecreatefrom' . $extension;
                }

                break;
            }
        }

        if (isset($createImgFunction)) {
            $img = $createImgFunction($link);

            if (getimagesize($link)[0] > 500) {
                $preview = imagescale($img, 500);
            } else {
                $preview = $img;
            }

            if ($fileExtension == Helper::EXTENSION_PNG) {
                imagepng($preview, $savePath . '.png');
            } elseif ($fileExtension == Helper::EXTENSION_GIF) {
                imagegif($preview, $savePath . 'gif');
            } else {
                imagejpeg($preview, $savePath . 'jpeg');     
            }
        } 
    }
}

