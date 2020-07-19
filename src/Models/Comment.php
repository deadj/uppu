<?php

class Comment
{
	private int $id;
	private string $fileId;
	private string $text;
	private string $date;
	private ?int $parentId;

	public function __construct(
		int $id,
		string $fileId,
		string $text,
		string $date,
		$parentId
	){
		$this->id = $id;
		$this->fileId = $fileId;
		$this->text = $text;
		$this->date = $date;
		$this->parentId = $parentId;
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

	public function getParentId()
	{
		return $this->parentId;
	}
}