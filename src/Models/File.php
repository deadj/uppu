<?php

class File 
{
    private $id;
    private $name;
    private $nameId;
    private $link;
    private $comment;
    private $type;
    private $date;
    private $size;

    public function __construct(string $nameId, string $name, string $link, string $comment, string $type, string $date, int $size)
    {
        $this->nameId = $nameId;
        $this->name = $name;
        $this->link = $link;
        $this->comment = $comment;
        $this->type = $type;
        $this->date = $date;
        $this->size = $size;
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

}