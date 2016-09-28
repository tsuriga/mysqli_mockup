<?php

namespace tests\app\services;

use app\services\UserService;
use app\models\User;

use tests\app\MysqliMockTrait;

class UserServiceTest extends \PHPUnit_Framework_TestCase
{
    use MysqliMockTrait;

    /**
     * @dataProvider mysqliMockProvider
     */
    public function testGetUsersReturnsAllUsersWithoutLimits($mysqliMock)
    {
        $this->assertUsersFound(
            $mysqliMock, 0, 0, ['John', 'Pam', 'Leslie', 'Ann', 'Rick']
        );
    }

    /**
     * @dataProvider mysqliMockProvider
     */
    public function testGetUsersReturnsASubsetOfUsersWithLimits($mysqliMock)
    {
        $this->assertUsersFound(
            $mysqliMock, 3, 2, ['Ann', 'Rick']
        );
    }

    private function assertUsersFound(
        \mysqli $mysqliMock,
        int $offset,
        int $limit,
        array $expectedUsers
    ) {
        // Arrange
        $userCounter = 0;
        $userService = new UserService($mysqliMock);

        // Act
        $users = $userService->getUsers($offset, $limit);

        // Assert
        foreach ($users as $user) {
            $this->assertInstanceOf(User::class, $user);

            $this->assertEquals(
                $expectedUsers[$userCounter++],
                $user->getName()
            );
        }

        $this->assertEquals(count($expectedUsers), $userCounter);
    }

    public function mysqliMockProvider(): array
    {
        $mockData = [
            'SELECT name FROM user' => [
                ['name' => 'John'],
                ['name' => 'Pam'],
                ['name' => 'Leslie'],
                ['name' => 'Ann'],
                ['name' => 'Rick'],
            ],
            'SELECT name FROM user LIMIT 2 OFFSET 3' => [
                ['name' => 'Ann'],
                ['name' => 'Rick'],
            ],
        ];

        $mysqliMock = $this->getMysqliMock($mockData);

        return [
            [$mysqliMock]
        ];
    }
}
