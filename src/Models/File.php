<?php

class File 
{
	private $id;
	private $name;
	private $link;
	private $comment;
	private $type;

	public function __construct(int $id, string $name, string $link, string $comment, string $type)
	{
		$this->id = $id;
		$this->name = $name;
		$this->link = $link;
		$this->comment = $comment;
		$this->type = $type;
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
}