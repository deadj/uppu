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
                imagegif($preview, $savePath . '.gif');
            } else {
                imagejpeg($preview, $savePath . '.jpeg');     
            }
        } 
    }
}

