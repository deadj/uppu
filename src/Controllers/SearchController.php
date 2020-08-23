<?php

class SearchController
{
	private $view;
	private $db;
	private SphinxSearch $sphinxSearch;
	private FilesTable $filesTable;

	public function __construct(\Slim\Views\Twig $view, $db, SphinxSearch $sphinxSearch, FilesTable $filesTable)
	{
		$this->view = $view;
		$this->db = $db;
		$this->sphinxSearch = $sphinxSearch;
		$this->filesTable = $filesTable;
	}

	public function search($request, $response)
	{
		if ($request->getParam('text') == "") {
			return $this->view->render($response, 'main.phtml');
		}

		$filesId = $this->sphinxSearch->search($request->getParam('text'));
		$filesArray = array();

		if (!empty($filesId)) {
			$filesArray = $this->filesTable->getFilesArrayThroughId($filesId);
		}

		return $this->view->render($response, 'list.phtml', ['filesList' => $filesArray]); 
	}
}