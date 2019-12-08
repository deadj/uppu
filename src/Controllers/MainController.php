<?php

class MainController
{
	private $view;
	private $response;

	public function __construct($view, $response)
	{
		$this->view = $view;
		$this->response = $response;
	}

	public function printPage()
	{
		$this->response = $this->view->render($this->response, 'main.phtml');
		return $this->response;
	}
}