<?php

namespace App\Common;

class Request
{
    private array $get;
    private array $post;
    private array $server;
    private array $files;

    public function __construct(array $get, array $post, array $server, array $files)
    {
        $this->get = $get;
        $this->post = $post;
        $this->server = $server;
        $this->files = $files;
    }

    public function get(): ?array
    {
        return $this->get;
    }

    public function post(): ?array
    {
        return $this->post;
    }

    public function server(): ?array
    {
        return $this->server;
    }

    public function files(): ?array
    {
        return $this->files;
    }
}
