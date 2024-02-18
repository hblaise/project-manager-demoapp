<?php

namespace App\Contracts;

interface ILogger
{
    public function info(string $message);
    public function error(string $message);
    public function warning(string $message);
    public function debug(string $message);
}
