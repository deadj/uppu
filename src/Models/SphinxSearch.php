<?php

class SphinxSearch
{
	private $pdo;

	public function __construct()
	{
		$this->pdo = new PDO('mysql:host=127.0.0.1;port=9306');
		$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	public function search(string $searchText): array
	{
		$statement = $this->pdo->prepare("SELECT * FROM index_files, rt_files WHERE MATCH(:searchText)");
		$statement->bindValue(':searchText', $searchText);
		$statement->execute();

		$filesId = array();

		while ($row = $statement->fetch(PDO::FETCH_OBJ)) {
			$filesId[] = $row->id;
		}

		return $filesId;
	}
}