<?php

class File 
{
    private string $name;
    private string $nameId;
    private string $link;
    private string $comment;
    private string $type;
    private string $date;
    private float $size;
    private array $metadata;
    private string $uploadIsDone;

    public function __construct(
        string $nameId, 
        string $name, 
        string $link, 
        string $comment, 
        string $type, 
        string $date, 
        int $size, 
        array $metadata,
        string $uploadIsDone
    ){
        $this->nameId = $nameId;
        $this->name = $name;
        $this->link = $link;
        $this->comment = $comment;
        $this->type = $type;
        $this->date = $date;
        $this->size = $size;
        $this->metadata = $metadata;
        $this->uploadIsDone = $uploadIsDone;
    }

    public function getNameId(): string
    {
        return $this->nameId;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getDate(): string
    {
        return $this->date;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function getUploadIsDone(): string
    {
        return $this->uploadIsDone;
    }
}