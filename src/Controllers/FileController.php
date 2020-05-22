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
	private $commentsTable;

	public function __construct(Twig $twig, Request $request, Response $response, $db)
	{
		$this->twig = $twig;
		$this->request = $request;
		$this->response = $response;
		$this->db = $db;

		$this->filesTable = new FilesTable($db);
		$this->commentsTable = new CommentsTable($db);
	}

	public function printPage(string $nameId): Response
	{
		$file = $this->filesTable->getFileThroughNameId($nameId);
		$comments = $this->commentsTable->getListForFile($nameId);

		return $this->twig->render($this->response, 'file.phtml', [
			'filesData' => array(
				'nameId' => $file->getNameId(),
				'name' => $file->getName(),
				'link' => $file->getLink(),
				'comment' => $file->getComment(),
				'date' => $file->getDate(),
				'type' => $file->getType(),
				'size' => $file->getSize(),
				'metadata' => $file->getMetadata()
			),
			'comments' => $comments
		]);
	}

	public function addComment(string $fileId, string $text)
	{	
		$date = date("Y-m-d H:i:s");
		$this->commentsTable->addComment($fileId, $text, $date);
	}

	public function getCommentsList(string $fileId): array
	{
		$commentsList = $this->commentsTable->getListForFile($fileId);
		return $this->convertCommentsObjectToArray($commentsList);
	}

	private function convertCommentsObjectToArray(array $comments): array
	{
		$commentsArray = array();

		foreach ($comments as $comment) {
			$commentsArray[] = array(
				'text' => $comment->getText(),
				'date' => $comment->getDate()
			);
		}

		return $commentsArray;
	}


}
