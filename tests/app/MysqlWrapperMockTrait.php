<?php

namespace tests\app;

use app\wrappers\MysqlDatabase;
use app\wrappers\MysqlResult;

trait MysqlWrapperMockTrait
{
    private function getMysqlWrapperMock(array $mockData): MysqlDatabase
    {
        $mysqli = $this->getMockBuilder(MysqlDatabase::class)
            ->setMethods(['query'])
            ->disableOriginalConstructor()
            ->getMock();

        $mysqli->expects($this->any())
            ->method('query')
            ->will($this->returnCallback(function($query) use ($mockData) {
                $results = $mockData[$query];

                $mysqliResult = $this->getMockBuilder(MysqlResult::class)
                    ->setMethods(['fetchArray', 'getNumRows'])
                    ->disableOriginalConstructor()
                    ->getMock();

                $mysqliResult
                    ->expects($this->any())
                    ->method('fetchArray')
                    ->will($this->returnCallback(function() use ($results) {
                        static $i = 0;
                        return isset($results[$i]) ? $results[$i++] : false;
                    }));

                $mysqliResult
                    ->expects($this->any())
                    ->method('getNumRows')
                    ->will($this->returnCallback(function() use ($results) {
                        return count($results);
                    }));

                return $mysqliResult;
            }));

        return $mysqli;
    }
}
