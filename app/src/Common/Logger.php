<?php

namespace App\Common;

use App\Contracts\ILogger;

class Logger implements ILogger
{
    private string $fileName = 'app';
    private string $filePath;

    public function __construct()
    {
        $this->filePath = __DIR__ . '/../../logs/' . $this->fileName . '.log';
    }

    private function log(string $logLevel, string $message): void
    {
        $date = date('Y-m-d H:i:s');
        file_put_contents($this->filePath, "[$date] [$logLevel] $message\n", FILE_APPEND);
    }

    public function info(string $message): void
    {
        $this->log('INFO', $message);
    }

    public function error(string $message): void
    {
        $this->log('ERROR', $message);
    }

    public function warning(string $message): void
    {
        $this->log('WARNING', $message);
    }

    public function debug(string $message): void
    {
        $this->log('DEBUG', $message);
    }
}
