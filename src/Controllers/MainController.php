<?php

use Slim\Http\UploadedFile;

class MainController
{
    private $view;
    private $uploader;
    private Helper $helper;

    public function __construct(
        \Slim\Views\Twig $view, 
        Uploader $uploader,
        Helper $helper
    ){
        $this->view = $view;
        $this->uploader = $uploader;
        $this->helper = $helper;
    }

    public function printPage($request, $response, $args)
    {
        $dataForView = array();

        if (isset($args['notify']) && $args['notify'] == "emptyName") {
            $dataForView['notify'] = "emptyName";
        }

        return $this->view->render($response, 'main.phtml', $dataForView);
    }

    public function uploadFile($request, $response)
    {
        $data = $request->getParsedBody();
        $name = $data['name'];
        $comment = $data['comment'];

        if ($data['name'] == "") {
            return $response->withRedirect("http://localhost/notify=emptyName");
        }

        $uploadedFiles = $request->getUploadedFiles();
        $uploadedFile = $uploadedFiles['file'];

        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            $nameId = $this->uploader->uploadFile(
                $uploadedFile->file,
                $uploadedFile->getClientFilename(),
                $name, 
                $comment
            );

            if (isset($data['notjs'])) {
                return $response->withRedirect("http://localhost/file/" . $nameId);
            } else {
                return $response->getBody()->write($nameId);
            }
        }  else {
            return $response->getBody()->write($uploadedFile->getError());
        }
    }

    private function createFilesLink(string $folderPath, string $nameId, string $extension): string
    {
        return $folderPath . "/" . $nameId . '.' . $extension;
    }

    private function getExtension(string $filename): string
    {
        return pathinfo($filename, PATHINFO_EXTENSION);
    }

    private function getFileType(string $type): string
    {
        if (preg_match('/video/', $type)) {
            return File::TYPE_VIDEO;
        } elseif (preg_match('/image/', $type)) {
            return File::TYPE_IMAGE;
        } elseif (preg_match('/audio/', $type)) {
            return File::TYPE_AUDIO;
        } else {
            return File::TYPE_OTHER;
        }   
    }
}