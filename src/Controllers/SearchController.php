<?php

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;

class SearchController
{
	private $twig;
	private $response;
	private $request;
	private $db;
	private $sphinxSearch;
	private $filesTable;

	public function __construct(Twig $twig, Request $request, Response $response, $db)
	{
		$this->twig = $twig;
		$this->request = $request;
		$this->response = $response;
		$this->db = $db;
		
		$this->sphinxSearch = new SphinxSearch();
		$this->filesTable = new FilesTable($db);
	}

	public function search()
	{
		if ($this->request->getParam('text') == "") {
			return $this->twig->render($this->response, 'main.phtml');
		}

		$filesId = $this->sphinxSearch->search($this->request->getParam('text'));
		$filesArray = array();

		// var_dump($filesId);

		if (!empty($filesId)) {
			foreach ($filesId as $id) {
				$filesArray[] = $this->filesTable->getFileThroughId(intval($id));
			}
		}

		return $this->twig->render($this->response, 'list.phtml', ['filesList' => $filesArray]); 
	}
}