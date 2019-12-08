<?php

class FilesTable
{
	private $pdo;

	public function __construct()
	{
		$this->pdo = include("src/dbConnect.php");
	}

	public function getFile(int $id): File
	{
		$statement = $this->pdo->prepare("SELECT * FROM files WHERE id = :id");
		$statement->bindValue(':id', $id);
		$statement->execute();
		$result = $statement->fetch(PDO::FETCH_OBJ);

		$file = new File(
			$result->id,
			$result->name,
			$result->link,
			$result->comment,
			$result->type
		);

		return $file;
	}

}