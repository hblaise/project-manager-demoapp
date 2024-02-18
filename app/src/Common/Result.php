<?php

namespace App\Common;

class Result
{
    public bool $success = false;
    public ?string $message;
    public ?array $data;
}
