<?php

namespace App\Repository;

use App\Helper\Database;

class DomainRepository
{
    protected Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }
}