<?php

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;

class ListController
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

	public function printPage(): Response
	{
		$filesList = $this->filesTable->getFilesList();

		return $this->twig->render($this->response, 'list.phtml', ['filesList' => $filesList]);
	}
}

