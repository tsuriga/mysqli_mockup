<?php

namespace app\services;

use app\models\User;

class UserService
{
    /** @var mysqli */
    private $db;

    public function __construct(\mysqli $db)
    {
        $this->db = $db;
    }

    public function getUsers(int $offset = 0, int $limit = 0): \Generator
    {
        $userResult = $this->getUserSet($offset, $limit);

        // Uncomment this to introduce access to a public read-only property
        // $userResult->num_rows;

        while ($row = $userResult->fetch_array()) {
            yield new User($row['name']);
        }
    }

    private function getUserSet(int $offset, int $limit): \mysqli_result
    {
        $sqlSelectNames = rtrim(sprintf(
            'SELECT name FROM user %s %s',
            $limit !== 0 ? sprintf('LIMIT %d', $limit) : '',
            $offset !== 0 && $limit !== 0 ? sprintf('OFFSET %d', $offset) : ''
        ));

        return $this->db->query($sqlSelectNames);
    }
}
