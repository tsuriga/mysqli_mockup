<?php

namespace app\services;

use app\models\User;
use app\wrappers\MysqlDatabase;
use app\wrappers\MysqlResult;

class UserService
{
    /** @var MysqlDatabase */
    private $db;

    public function __construct(MysqlDatabase $db)
    {
        $this->db = $db;
    }

    /**
     * @throws \Exception If the limit is too high for the result count
     */
    public function getUsers(int $offset = 0, int $limit = 0): \Generator
    {
        $userResult = $this->getUserSet($offset, $limit);

        if ($limit > $userResult->getNumRows()) {
            throw new \Exception('Not enough results to meet the limit');
        }

        while ($row = $userResult->fetchArray()) {
            yield new User($row['name']);
        }
    }

    private function getUserSet(int $offset, int $limit): MysqlResult
    {
        $sqlSelectNames = rtrim(sprintf(
            'SELECT name FROM user %s %s',
            $limit !== 0 ? sprintf('LIMIT %d', $limit) : '',
            $offset !== 0 && $limit !== 0 ? sprintf('OFFSET %d', $offset) : ''
        ));

        return $this->db->query($sqlSelectNames);
    }
}
