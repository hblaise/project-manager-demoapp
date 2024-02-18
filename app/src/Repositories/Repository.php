<?php

namespace App\Repositories;

use App\Contracts\IRepository;
use App\Common\DatabaseConfig;
use PDO;

class Repository implements IRepository
{
    protected PDO $pdo;

    public function __construct()
    {
        $this->pdo = new PDO(DatabaseConfig::getDsn(), DatabaseConfig::getUsername(), DatabaseConfig::getPassword());
    }

    public function getPDO(): PDO
    {
        return $this->pdo;
    }
}
