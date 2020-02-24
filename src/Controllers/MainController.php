<?php

use Slim\Http\Response;
use Slim\Http\Request;
use Slim\Http\UploadedFile;
use Slim\Views\Twig;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;


class MainController
{
    private $twig;
    private $response;
    private $request;
    private $db;
    private $fileDirectory = 'public/files/';
    private $filesTable;
    private $fileController;

    public function __construct(Twig $twig, Request $request, Response $response, $db)
    {
        $this->twig = $twig;
        $this->request = $request;
        $this->response = $response;
        $this->db = $db;

        $this->filesTable = new FilesTable($db);
        $this->fileController = new FileController($twig, $request, $response, $db);
    }

    public function printPage(): Response
    {
        return $this->twig->render($this->response, 'main.phtml');
    }

    public function uploadFile(): void
    {

        $uploadedFiles = $this->request->getUploadedFiles();
        $uploadedFile = $uploadedFiles['file'];

        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
            $folderPath = $this->fileDirectory . date("m_d_y");
            
            if (!file_exists($folderPath)) {
                mkdir($folderPath);
            }

            $nameId = $this->moveUploadedFile($folderPath . "/", $uploadedFile);
            $name = strval(trim($_POST['name']));
            $type = preg_replace('/\\/\\w*/', '', $uploadedFile->getClientMediaType());
            $link = $folderPath . "/" . $nameId . '.' . preg_replace('/.*[.]/', '', $uploadedFile->getClientFilename());

            if ($type == "video") {
                Converter::convertVideo($link);
                $link = preg_replace('/[.]\\w*/', '.mp4', $link);
            }

            $comment = trim(mb_substr(strval($_POST['comment']), 0, 30));
            $date = date("Y-m-d H:i:s");
            $metadata = MediaInfo::getMetadata($type, $link);
            $size = MediaInfo::getSize($link);

            $file = new File($nameId, $name, $link, $comment, $type, $date, $size, $metadata);
            $this->filesTable->addFile($file);

            header('Location: /' . $nameId);
            exit;

        } elseif ($uploadedFile->getError() == 1) {
            echo "Слишком большой размер файла";
        } elseif ($uploadedFile->getError() == 4) {
            echo "Файл не выбран";
        } else {
            echo "Error";
        }
    }

    private function moveUploadedFile(string $directory, UploadedFile $uploadedFile): string
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8));
        $filename = sprintf('%s.%0.8s', $basename, $extension);

        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        return preg_replace('/[.].*/', '', $filename);
    }
}