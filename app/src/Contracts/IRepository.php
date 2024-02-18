<?php

namespace App\Contracts;

use PDO;

interface IRepository
{
    public function getPDO(): PDO;
}
