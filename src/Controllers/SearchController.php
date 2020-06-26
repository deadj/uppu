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
			$filesArray = $this->filesTable->getFilesArrayThroughId($this->convertArrayIdToString($filesId));
		}

		return $this->view->render($response, 'list.phtml', ['filesList' => $filesArray]); 
	}

	private function convertArrayIdToString(array $filesId): string
	{
        $stringId = "(";

        foreach ($filesId as $key => $id) {
            if ($key != count($filesId) - 1) {
                $stringId = $stringId . $id . ", ";
            } else {
                $stringId = $stringId . $id . ")";
            }
        }	

        return $stringId;	
	}
}