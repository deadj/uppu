<?php

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;

class FileController
{
	private $twig;
	private $response;
	private $request;
	private $db;

	private $filesTable;

	public function __construct(Twig $twig, Request $request, Response $response, $db)
	{
		$this->twig = $twig;
		$this->request = $request;
		$this->response = $response;
		$this->db = $db;

		$this->filesTable = new FilesTable($db);
	}

	public function printPage(string $nameId)
	{
		$file = $this->filesTable->getFile($nameId);

		$fileData = array('Название' => $file->getName());

		if (preg_match('/video/', $file->getType())) {
			$getID3 = new getID3;
			$videoData = $getID3->analyze($file->getLink());

			$fileData['Ширина'] = $videoData['video']['resolution_x'];
			$fileData['Высота'] = $videoData['video']['resolution_y'];
			$fileData['Кадр/сек'] = $videoData['video']['frame_rate']; 
		} elseif (preg_match('/audio/', $file->getType())) {
			$getID3 = new getID3;
			$videoData = $getID3->analyze($file->getLink());

			$fileData['Битрейт'] = $videoData['audio']['bitrate'] / 1000;
		}

		if ($file->getSize() <= 1000) {
			$fileData['Размер'] = strval($file->getSize()) . " Кб.";
		} else {
			$fileData['Размер'] = strval($file->getSize() / 1000) . " Мб.";
		}

		$fileData['Дата'] = $file->getDate();
		$fileData['Комментарий'] = $file->getComment();

		return $this->twig->render($this->response, 'file.phtml', [
			'type' => $file->getType(),
			'fileData' => $fileData,
			'link' => $file->getLink() 
		]);
	}


}
