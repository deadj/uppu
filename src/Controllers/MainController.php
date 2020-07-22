<?php

use Slim\Http\UploadedFile;

class MainController
{
    private $view;
    private $db;
    private string $fileDirectory = 'files/';
    private FilesTable $filesTable;
    private SphinxSearch $sphinxSearch;
    private getID3 $getID3;

    public function __construct(\Slim\Views\Twig $view, $db)
    {
        $this->view = $view;
        $this->db = $db;
        $this->filesTable = new FilesTable($db);
        $this->sphinxSearch = new SphinxSearch();
        $this->getID3 = new getID3;
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

        if ($data['name'] == "") {
            return $response->withRedirect("http://localhost/notify=emptyName");
        }

        $uploadedFiles = $request->getUploadedFiles();
        $uploadedFile = $uploadedFiles['file'];

        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            $folderPath = $this->fileDirectory . date("m_d_y");
            $extension = $this->getExtension($uploadedFile->getClientFilename());

            if (!file_exists($folderPath)) {
                mkdir($folderPath);
            }

            $helper = new Helper($this->db);
            
            $nameId = $helper->moveUploadedFile($folderPath . "/", $uploadedFile, $extension);
            $name = strval(trim($data['name']));
            $type = $this->getFileType($uploadedFile->getClientMediaType());
            $link = $this->createFilesLink($folderPath, $nameId, $uploadedFile->getClientFilename());

            if ($type == "video") {
                $fileData = $this->getID3->analyze($link);

                if (!isset($this->fileData['video']['fourcc_lookup']) || 
                    !preg_match('/H[.]264/iu', $this->fileData['video']['fourcc_lookup'])) {

                    $gearmanClient = new GearmanCLient();
                    $gearmanClient->addServer();
                    $res = $gearmanClient->doBackground('convertVideo', $link);

                    $metadata = MediaInfo::getNullMetadataForVideo();
                    $size = 0;
                    $uploadIsDone = "null";
                } else {
                    $metadata = MediaInfo::getMetadata($type, $link);
                    $size = MediaInfo::getSize($link);
                    $uploadIsDone = "done";
                }

                $link = preg_replace('/[.]\\w*/', '.mp4', $link);
            } else {
                $metadata = MediaInfo::getMetadata($type, $link);
                $size = MediaInfo::getSize($link);
                $uploadIsDone = "done";
            }
            
            if ($extension == "php" || $extension == "phtml") {
                $link = preg_replace('/[.](php|phtml)$/', '.txt', $link);
            }

            $comment = trim(mb_substr(strval($data['comment']), 0, 30));
            $date = date("Y-m-d H:i:s");
            
            $file = new File($nameId, $name, $link, $comment, $type, $date, $size, $metadata, $uploadIsDone);
            $fileId = $this->filesTable->addFile($file);
            $this->sphinxSearch->add($fileId, $file);

            if (isset($data['notjs'])) {
                return $response->withRedirect("http://localhost/file/" . $nameId);
            } else {
                return $response->getBody()->write($nameId);
            }
        }  else {
            return $response->getBody()->write("error");
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

    private function getFileType(string $type): string
    {
        if (preg_match('/video/', $type)) {
            return "video";
        } elseif (preg_match('/image/', $type)) {
            return "image";
        } elseif (preg_match('/audio/', $type)) {
            return "audio";
        } else {
            return "other";
        }   
    }
}