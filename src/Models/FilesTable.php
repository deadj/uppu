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

    public function getFilesArrayThroughId(string $stringId): array
    {
        $statement = $this->pdo->prepare("SELECT * FROM files WHERE id IN " . $stringId);
        $statement->execute();

        $filesList = array();

        while ($row = $statement->fetch(PDO::FETCH_OBJ)) {
            $filesList[] = $this->createFile($row);
        }

        return $filesList;
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
            metadata,
            uploadIsDone
        ) VALUES (
            :nameId, 
            :name, 
            :link, 
            :comment, 
            :type, 
            :date, 
            :size,
            :metadata,
            :uploadIsDone
        )");

        $statement->bindValue(':nameId', $file->getNameId());
        $statement->bindValue(':name', $file->getName());
        $statement->bindValue(':link', $file->getLink());
        $statement->bindValue(':comment', $file->getComment());
        $statement->bindValue(':type', $file->getType());
        $statement->bindValue(':date', $file->getDate());
        $statement->bindValue(':size', $file->getSize());
        $statement->bindValue(':metadata', json_encode($file->getMetadata()));
        $statement->bindValue(':uploadIsDone', $file->getUploadIsDone());

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

    public function updateMetadata(
        string $nameId, 
        $metadata, 
        int $size, 
        string $uploadIsDone
    ): void
    {
        $statement = $this->pdo->prepare("UPDATE files SET 
            size = :size, 
            metadata = :metadata,
            uploadIsDone = :uploadIsDone 
        WHERE nameId = :nameId");

        $statement->bindValue(':size', $size);
        $statement->bindValue(':metadata', json_encode($metadata));
        $statement->bindValue(':uploadIsDone', $uploadIsDone);
        $statement->bindValue(':nameId', $nameId);

        $statement->execute();
    }

    public function getErrorFilesList(): array
    {
        $statement = $this->pdo->prepare("SELECT * FROM files WHERE uploadIsDone = 'error'");
        $statement->execute();

        $list = array();

        if ($statement->rowCount() != 0) {
            while ($row = $statement->fetch(PDO::FETCH_OBJ)) {
                $list[] = $this->createFile($row);
            }

            return $list;
        } else {
            return $list;
        }
    }

    public function deleteFile(string $nameId): void
    {
        $statement = $this->pdo->prepare("DELETE FROM files WHERE nameId = :nameId");
        $statement->bindValue(":nameId", $nameId);
        $statement->execute();
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
            $row->size,
            json_decode($row->metadata),
            $row->uploadIsDone
        );

        return $file;       
    }
}