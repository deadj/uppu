<?php

class ListController
{
	private $view;
	private $db;	
	private FilesTable $filesTable;

	public function __construct(\Slim\Views\Twig $view, $db)
	{
		$this->view = $view;
		$this->db = $db;
		$this->filesTable = new FilesTable($db);
	}

	public function printPage($request, $response)
	{
		$filesList = $this->filesTable->getFilesList();

		return $this->view->render($response, 'list.phtml', ['filesList' => $filesList]);
	}
}

