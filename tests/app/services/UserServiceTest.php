<?php

namespace tests\app\services;

use app\services\UserService;
use app\models\User;
use app\wrappers\MysqlDatabase;

use tests\app\MysqlWrapperMockTrait;

class UserServiceTest extends \PHPUnit_Framework_TestCase
{
    use MysqlWrapperMockTrait;

    /**
     * @dataProvider mysqlMockProvider
     */
    public function testGetUsersReturnsAllUsersWithoutLimits($databaseMock)
    {
        $this->assertUsersFound(
            $databaseMock, 0, 0, ['John', 'Pam', 'Leslie', 'Ann', 'Rick']
        );
    }

    /**
     * @dataProvider mysqlMockProvider
     */
    public function testGetUsersReturnsASubsetOfUsersWithLimits($databaseMock)
    {
        $this->assertUsersFound(
            $databaseMock, 3, 2, ['Ann', 'Rick']
        );
    }

    /**
     * @dataProvider mysqlMockProvider
     * @expectedException \Exception
     */
    public function testGetUsersThrowsAnExceptionWhenLimitIsLargerThanResults(
        $databaseMock
    ) {
        $this->assertUsersFound(
            $databaseMock, 4, 2, ['Rick']
        );
    }

    private function assertUsersFound(
        MysqlDatabase $databaseMock,
        int $offset,
        int $limit,
        array $expectedUsers
    ) {
        // Arrange
        $userCounter = 0;
        $userService = new UserService($databaseMock);

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

    public function mysqlMockProvider(): array
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
            'SELECT name FROM user LIMIT 2 OFFSET 4' => [
                ['name' => 'Rick'],
            ],
        ];

        $databaseMock = $this->getMysqlWrapperMock($mockData);

        return [
            [$databaseMock]
        ];
    }
}
