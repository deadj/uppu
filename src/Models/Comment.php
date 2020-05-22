<?php

class Comment
{
	private $id;
	private $fileId;
	private $text;
	private $date;

	public function __construct(
		int $id,
		string $fileId,
		string $text,
		string $date
	){
		$this->id = $id;
		$this->fileId = $fileId;
		$this->text = $text;
		$this->date = $date;
	}

	public function getId(): int
	{
		return $this->id;
	}

	public function getFileId(): string
	{
		return $this->fileId;
	}

	public function getText(): string
	{
		return $this->text;
	}

	public function getDate(): string
	{
		return $this->date;
	}
}