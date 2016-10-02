<?php

namespace app\wrappers;

class MysqlDatabase
{
    /** @var \mysqli Original database object */
    private $db;

    public function __construct(\mysqli $db)
    {
        $this->db = $db;
    }

    public function query(string $query): MysqlResult
    {
        return new MysqlResult($this->db->query($query));
    }
}
