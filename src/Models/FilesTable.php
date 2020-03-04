<?php

class FilesTable
{
    private $pdo;

    public function __construct($db)
    {
        $this->pdo = $db;
    }

    public function getFileThroughNameId(string $nameId): File
    {
        $statement = $this->pdo->prepare("SELECT * FROM files WHERE nameId = :nameId");
        $statement->bindValue(':nameId', $nameId);
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_OBJ);

        return $this->createFile($result);
    }

    public function getFileThroughId(int $id): File
    {
        $statement = $this->pdo->prepare("SELECT * FROM files WHERE id = :id");
        $statement->bindValue(':id', $id);
        $statement->execute();

        $result = $statement->fetch(PDO::FETCH_OBJ);

        return $this->createFile($result);
    }

    public function addFile(File $file): int
    {
        $statement = $this->pdo->prepare("INSERT INTO files (
            nameId, 
            name, 
            link, 
            comment, 
            type, 
            date, 
            size,
            metadata
        ) VALUES (
            :nameId, 
            :name, 
            :link, 
            :comment, 
            :type, 
            :date, 
            :size,
            :metadata
        )");

        $statement->bindValue(':nameId', $file->getNameId());
        $statement->bindValue(':name', $file->getName());
        $statement->bindValue(':link', $file->getLink());
        $statement->bindValue(':comment', $file->getComment());
        $statement->bindValue(':type', $file->getType());
        $statement->bindValue(':date', $file->getDate());
        $statement->bindValue(':size', $file->getSize());
        $statement->bindValue(':metadata', json_encode($file->getMetadata()));

        $statement->execute();

        $statement = $this->pdo->prepare("SELECT id FROM files WHERE nameId = :nameId");
        $statement->bindValue(':nameId', $file->getNameId());
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_OBJ);

        return $result->id;
    }

    public function getFilesList(): array
    {
        $statement = $this->pdo->prepare("SELECT * FROM files ORDER BY date DESC LIMIT 100");
        $statement->execute();

        $filesList = array();

        while ($row = $statement->fetch(PDO::FETCH_OBJ)) {
            $filesList[] = $this->createFile($row);
        }

        return $filesList;
    }

    private function createFile(object $row)
    {
        $file = new File(
            $row->nameId,
            $row->name,
            $row->link,
            $row->comment,
            $row->type,
            $row->date,
            $row->size,
            json_decode($row->metadata)
        );

        return $file;       
    }
}