<?php

class CommentsTable
{
	private $pdo;

	public function __construct($db)
	{
		$this->pdo = $db;
	}

	public function addComment(string $fileId, string $text, string $date): void
	{
		$statement = $this->pdo->prepare("INSERT INTO comments (
			text,
			fileId,
			date
		) VALUES (
			:text,
			:fileId,
			:date
		)");

		$statement->bindValue(':text', $text);
		$statement->bindValue(':fileId', $fileId);
		$statement->bindValue(':date', $date);

		$statement->execute();
	}

	public function getList(): array
	{
		$statement = $this->pdo->prepare("SELECT * FROM comments");
		$statement->execute();

		$list = array();

		while ($row = $statement->fetch(PDO::FETCH_OBJ)) {
			$list[] = $this->createCommnet($row);
		}

		return $list;
	}

	public function getListForFile(string $fileId): array
	{
		$statement = $this->pdo->prepare("SELECT * FROM comments WHERE fileId = :fileId");
		$statement->bindValue(':fileId', $fileId);
		$statement->execute();

		$list = array();

		while ($row = $statement->fetch(PDO::FETCH_OBJ)) {
			$list[] = $this->createCommnet($row);
		}

		return $list;
	}

	private function createCommnet(object $row): object
	{
		$comment = new Comment(
			$row->id,
			$row->fileId,
			$row->text,
			$row->date,
			$row->parentId
		);

		return $comment;
	}
}