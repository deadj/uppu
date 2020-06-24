<?php

use Slim\Http\UploadedFile;
// use Symfony\Component\Process\Process;
// use Symfony\Component\Process\Exception\ProcessFailedException;

class MainController
{
    private $view;
    private $fileDirectory = 'files/';
    private $filesTable;
    private $db;
    private $sphinxSearch;

    public function __construct(\Slim\Views\Twig $view, $db)
    {
        $this->view = $view;
        $this->db = $db;
        $this->filesTable = new FilesTable($db);
        $this->sphinxSearch = new SphinxSearch();
    }

    public function printPage($request, $response, $args)
    {
        return $this->view->render($response, 'main.phtml');
    }

    public function uploadFile($request, $response, $args)
    {
        $data = $request->getParsedBody();
        $uploadedFiles = $request->getUploadedFiles();
        $uploadedFile = $uploadedFiles['file'];

        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            $folderPath = $this->fileDirectory . date("m_d_y");
            $extension = $this->getExtension($uploadedFile->getClientFilename());
            
            if (!file_exists($folderPath)) {
                mkdir($folderPath);
            }

            $nameId = Helper::moveUploadedFile($folderPath . "/", $uploadedFile, $extension);
            $name = strval(trim($data['name']));
            $type = preg_replace('/\\/\\w*/', '', $uploadedFile->getClientMediaType());
            $link = $this->createFilesLink($folderPath, $nameId, $uploadedFile->getClientFilename());

            if ($type == "video") {
                Converter::convertVideo($link);
                $link = preg_replace('/[.]\\w*/', '.mp4', $link);
            }
            
            if ($extension == "php" || $extension == "phtml") {
                $link = preg_replace('/[.](php|phtml)$/', '.txt', $link);
            }

            $comment = trim(mb_substr(strval($data['comment']), 0, 30));
            $date = date("Y-m-d H:i:s");
            $metadata = MediaInfo::getMetadata($type, $link);
            $size = MediaInfo::getSize($link);
            
            $file = new File($nameId, $name, $link, $comment, $type, $date, $size, $metadata);
            $fileId = $this->filesTable->addFile($file);
            $this->sphinxSearch->add($fileId, $file);

            return $response->getBody()->write($nameId);
        }  else {
            echo "Error";
        }
    }

    private function createFilesLink(string $folderPath, string $nameId, string $clientFilename): string
    {
        return $folderPath . "/" . $nameId . '.' . preg_replace('/.*[.]/', '', $clientFilename);
    }

    private function getExtension(string $filename): string
    {
        return pathinfo($filename, PATHINFO_EXTENSION);
    }

    //сохранение и переименование файла
    private function moveUploadedFile(string $directory, UploadedFile $uploadedFile, string $extension): string
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