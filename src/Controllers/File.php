<?php

class File
{
	private $view;
	private $response;

	public function __construct($view, $response)
	{
		$this->view = $view;
		$this->response = $response;
	}

	public function printPage(int $fileId)
	{
		$this->response = $this->view->render($this->response, 'file.phtml');
		return $this->response;
	}
}