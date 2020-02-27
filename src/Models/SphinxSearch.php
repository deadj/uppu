<?php

class SphinxSearch
{
	private $pdo;

	public function __construct()
	{
		// $sphinxParameters = parse_ini_file('src/sphinxConfig.ini');
		$this->pdo = new PDO('mysql:host=127.0.0.1;port=9306');
		// $this->pdo = new PDO('mysql:host=localhost;dbname=uppu', 'pmauser', 1596321);
		$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	public function search(string $searchText): array
	{
		$statement = $this->pdo->prepare("SELECT id FROM index_files, rt_files WHERE MATCH(:searchText) DESC");
		$statement->bindValue(':searchText', $searchText);
		$statement->execute();

		$filesId = array();

		while ($row = $statement->fetch(PDO::FETCH_OBJ)) {
			$filesId[] = $row->id;
		}

		var_dump($filesId);
		exit;

		return $filesId;
	}
}