<?php

class FileController
{
	private $view;
	private $db;
	private FilesTable $filesTable;
	private CommentsTable $commentsTable;

	public function __construct(\Slim\Views\Twig $view, $db)
	{
		$this->view = $view;
		$this->db = $db;

		$this->filesTable = new FilesTable($db);
		$this->commentsTable = new CommentsTable($db);
	}

	public function printPage($request, $response, $args)
	{
		$nameId = $args['nameId'];

		$file = $this->filesTable->getFileThroughNameId($nameId);

		if ($file === null) {
			return $this->view->render($response, '404.html');
		} 

		$comments = $this->commentsTable->getListForFile($nameId);

		return $this->view->render($response, 'file.phtml', [
			'filesData' => array(
				'nameId' => $file->getNameId(),
				'name' => $file->getName(),
				'link' => $file->getLink(),
				'comment' => $file->getComment(),
				'date' => $file->getDate(),
				'type' => $file->getType(),
				'size' => $file->getSize(),
				'metadata' => $file->getMetadata(),
				'uploadIsDone' => $file->getUploadIsDone()
			),
			'comments' => $this->createCommentsTree($comments)
		]);
	}

	public function addComment($request, $response)
	{
		$data = $request->getParsedBody();

		$fileId = $data['fileId'];
		$comment = $data['comment'];
		$parentId = $data['parentId'];

		if ($comment != "") {
			if ($parentId === 'NULL') {
				$parentId = NULL;
			}

			$date = date("Y-m-d H:i:s");
			$this->commentsTable->addComment($fileId, $comment, $date, $parentId);

			$result = true;
		} else {
			$result = false;
		}

		return $response->getBody()->write($result);
	}

	public function getCommentsList($request, $response)
	{
		$data = $request->getParsedBody();
		$fileId = $data['fileId'];

		$comments = $this->commentsTable->getListForFile($fileId);
		$comments = $this->convertCommentsObjectToArray($comments);
		$commentsTree = $this->createCommentsTreeForJSUpdate($comments);

		return $response->withJson($commentsTree);		
	}
	
	private function createCommentsTree(array $comments): array
	{
		$parents = array();

		if (!empty($comments)) {
			foreach ($comments as $comment) {
				$parents[$comment->getParentId()][$comment->getId()] = $comment;
			}

			$treeElem = reset($parents);
			$this->generateElemTree($treeElem, $parents);

			return $treeElem;			
		} else {
			return array();
		}

	}

	private function generateElemTree(&$treeElem, array $parents): void
	{
		foreach ($treeElem as $key => $comment)
		{
			if (!isset($comment->children)) {
				$treeElem[$key]->children = [];
			}

			if (array_key_exists($key, $parents)) {
				$treeElem[$key]->children = $parents[$key];
				$this->generateElemTree($treeElem[$key]->children, $parents);
			}
		}
	}

	private function convertCommentsObjectToArray(array $comments): array
	{
		$commentsArray = array();

		foreach ($comments as $comment) {
			$commentsArray[] = array(
				'id' => $comment->getId(),
				'text' => $comment->getText(),
				'date' => $comment->getDate(),
				'parentId' => $comment->getParentId()
			);
		}

		return $commentsArray;
	}

	private function createCommentsTreeForJSUpdate(array $comments): array
	{
		$parents = array();

		if (!empty($comments)) {
			foreach ($comments as $comment) {
				$parents[$comment['parentId']][$comment['id']] = $comment;
			}

			$treeElem = reset($parents);
			$this->generateElemTreeForJSUpdate($treeElem, $parents);

			return $treeElem;			
		} else {
			return array();
		}
	}

	private function generateElemTreeForJSUpdate(&$treeElem, array $parents): void
	{
		foreach ($treeElem as $key => $comment)
		{
			if (!isset($comment->children)) {
				$treeElem[$key]['children'] = [];
			}

			if (array_key_exists($key, $parents)) {
				$treeElem[$key]['children'] = $parents[$key];
				$this->generateElemTreeForJSUpdate($treeElem[$key]['children'], $parents);
			}
		}
	}

}