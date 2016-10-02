<?php

namespace app\wrappers;

class MysqlResult
{
    /** @var \mysqli_result Original database result object */
    private $result;

    public function __construct(\mysqli_result $result)
    {
        $this->result = $result;
    }

    public function getNumRows(): int
    {
        return $this->result->num_rows;
    }

    /**
     * @return array|bool
     */
    public function fetchArray()
    {
        return $this->result->fetch_array();
    }
}
