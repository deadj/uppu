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

	public function printPage(string $nameId): Response
	{
		$file = $this->filesTable->getFile($nameId);

		return $this->twig->render($this->response, 'file.phtml', [
			'name' => $file->getName(),
			'link' => $file->getLink(),
			'comment' => $file->getComment(),
			'date' => $file->getDate(),
			'type' => $file->getType(),
			'size' => $file->getSize(),
			'metadata' => $file->getMetadata()
		]);
	}


}
