<?php

namespace App\Common;

class JsonResponse
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function send(): void
    {
        header('Content-Type: application/json');
        echo json_encode($this->data);
    }
}
