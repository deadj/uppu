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

	public function add(int $id, File $file): void
	{

		$statement = $this->pdo->prepare("INSERT INTO rt_files (
			id,
            nameId, 
            name, 
            link, 
            comment, 
            type, 
            date, 
            size,
            metadata
        ) VALUES (
        	:id,
            :nameId, 
            :name, 
            :link, 
            :comment, 
            :type, 
            :date, 
            :size,
            :metadata
        )");

		$statement->bindValue(':id', $id);
        $statement->bindValue(':nameId', $file->getNameId());
        $statement->bindValue(':name', $file->getName());
        $statement->bindValue(':link', $file->getLink());
        $statement->bindValue(':comment', $file->getComment());
        $statement->bindValue(':type', $file->getType());
        $statement->bindValue(':date', $file->getDate());
        $statement->bindValue(':size', $file->getSize());
        $statement->bindValue(':metadata', json_encode($file->getMetadata()));

        $statement->execute();
	}
}