<?php

class ListController
{
	private $view;
	private $db;	
	private FilesTable $filesTable;

	public function __construct(\Slim\Views\Twig $view, $db, FilesTable $filesTable)
	{
		$this->view = $view;
		$this->db = $db;
		$this->filesTable = $filesTable;
	}

	public function printPage($request, $response)
	{
		$filesList = $this->filesTable->getFilesList();

		return $this->view->render($response, 'list.phtml', ['filesList' => $filesList]);
	}
}

