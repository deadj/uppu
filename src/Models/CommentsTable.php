<?php

class CommentsTable
{
	private $pdo;

	public function __construct($db)
	{
		$this->pdo = $db;
	}

	public function addComment(
		string $fileId, 
		string $text, 
		string $date, 
		$parentId
	): void
	{
		$statement = $this->pdo->prepare("INSERT INTO comments (
			text,
			fileId,
			date,
			parentId
		) VALUES (
			:text,
			:fileId,
			:date,
			:parentId
		)");

		$statement->bindValue(':text', $text);
		$statement->bindValue(':fileId', $fileId);
		$statement->bindValue(':date', $date);
		$statement->bindValue(':parentId', $parentId);

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

	public function deleteListForFile(string $fileId): void
	{
		$statement = $this->pdo->prepare("DELETE FROM comments WHERE fileId = :fileId");
		$statement->bindValue(':fileId', $fileId);
		$statement->execute();
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