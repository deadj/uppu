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

	public function __construct(Twig $twig, Request $request, Response $response, $db)
	{
		$this->twig = $twig;
		$this->request = $request;
		$this->response = $response;
		$this->db = $db;
		
		$this->sphinxSearch = new SphinxSearch();
	}

	public function search()
	{
		if ($this->request->getParam('text') == "") {
			return $this->twig->render($this->response, 'main.phtml');
		}
	}
}