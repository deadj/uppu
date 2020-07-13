<?php

class Comment
{
	private $id;
	private $fileId;
	private $text;
	private $date;
	private $parentId;
	private $childrenComments;

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