<?php

use Phalcon\Mvc\Controller;

class ErrorsController extends Controller
{
    public function show404Action()
    {
        $this->response->setStatusCode(404, 'Not Found');
    }

    public function show500Action()
    {
        $this->response->setStatusCode(500, 'Internal Server Error');
    }
}