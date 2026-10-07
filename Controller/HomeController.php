<?php

require_once __DIR__ . '/BaseController.php';

class HomeController extends BaseController
{
    public function index(): void
    {
        $this->view('home/index');
    }


    public function unauthorized(): void
    {
        http_response_code(403);

        $this->view('errors/unauthorized');
    }


    public function notFound(): void
    {
        http_response_code(404);

        $this->view('errors/404');
    }
}
