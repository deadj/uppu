<?php

class SearchController
{
	private $view;
	private $db;
	private $sphinxSearch;
	private $filesTable;

	public function __construct(\Slim\Views\Twig $view, $db)
	{
		$this->view = $view;
		$this->db = $db;
		$this->sphinxSearch = new SphinxSearch();
		$this->filesTable = new FilesTable($db);
	}

	public function search($request, $response, $args)
	{
		if ($request->getParam('text') == "") {
			return $this->view->render($response, 'main.phtml');
		}

		$filesId = $this->sphinxSearch->search($request->getParam('text'));
		$filesArray = array();

		if (!empty($filesId)) {
			foreach ($filesId as $id) {
				$filesArray[] = $this->filesTable->getFileThroughId(intval($id));
			}
		}

		return $this->view->render($response, 'list.phtml', ['filesList' => $filesArray]); 
	}
}