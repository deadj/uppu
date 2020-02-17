<?php

class FilesTable
{
    private $pdo;

    public function __construct($db)
    {
        $this->pdo = $db;
    }

    public function getFile(string $nameId): File
    {
        $statement = $this->pdo->prepare("SELECT * FROM files WHERE nameId = :nameId");
        $statement->bindValue(':nameId', $nameId);
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_OBJ);

        return $this->createFile($result);
    }


    public function addFile(File $file): void
    {
        $statement = $this->pdo->prepare("INSERT INTO files (nameId, name, link, comment, type, date, size) "
                . "VALUES (:nameId, :name, :link, :comment, :type, :date, :size)");

        $statement->bindValue(':nameId', $file->getNameId());
        $statement->bindValue(':name', $file->getName());
        $statement->bindValue(':link', $file->getLink());
        $statement->bindValue(':comment', $file->getComment());
        $statement->bindValue(':type', $file->getType());
        $statement->bindValue(':date', $file->getDate());
        $statement->bindValue(':size', $file->getSize());

        $statement->execute();
    }

    public function getFilesList(): array
    {
        $statement = $this->pdo->prepare("SELECT * FROM files ORDER BY date DESC LIMIT 100");
        $statement->execute();;

        $filesList = array();

        while ($row = $statement->fetch(PDO::FETCH_OBJ)) {
            $filesList[] = $this->createFile($row);
        }

        return $filesList;
    }

    private function createFile(object $row): File
    {
        $file = new File(
            $row->nameId,
            $row->name,
            $row->link,
            $row->comment,
            $row->type,
            $row->date,
            $row->size
        );

        return $file;       
    }
}