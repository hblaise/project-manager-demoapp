<?php

namespace App\Common;

class View
{
    private string $view;
    private array $data;

    public function __construct(string $view, array $data = [])
    {
        $this->view = $view;
        $this->data = $data;
    }

    public function render(): void
    {
        extract($this->data);
        $contentView = __DIR__ . "/../Views/{$this->view}.php";
        include __DIR__ . "/../Views/layout.php";
    }
}
