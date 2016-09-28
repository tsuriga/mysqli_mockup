<?php

namespace tests\app;

trait MysqliMockTrait
{
    /**
     * Adapted from a method written by Maurits van der Schee at
     * https://www.leaseweb.com/labs/2015/03/unit-testing-phpunit-mocking-mysqli/
     */
    private function getMysqliMock(array $mockData): \mysqli
    {
        $mysqli = $this->getMockBuilder('mysqli')
            ->setMethods(['query'])
            ->getMock();

        $mysqli->expects($this->any())
            ->method('query')
            ->will($this->returnCallback(function($query) use ($mockData) {
                $results = $mockData[$query];

                $mysqliResult = $this->getMockBuilder('mysqli_result')
                    ->setMethods(['fetch_array'])
                    //->setMethods(['fetch_array', '__get'])
                    ->disableOriginalConstructor()
                    ->getMock();

                $mysqliResult
                    ->expects($this->any())
                    ->method('fetch_array')
                    ->will($this->returnCallback(function() use ($results) {
                        static $i = 0;
                        return isset($results[$i]) ? $results[$i++] : false;
                    }));

                /*
                $mysqliResult
                    ->expects($this->any())
                    ->method('__get')
                    ->with($this->equalTo('num_rows'))
                    ->will($this->returnValue(count($results)));
                */

                /*
                $reflection = new \ReflectionClass($mysqliResult);
                $numRowsProperty = $reflection->getProperty('num_rows');
                $numRowsProperty->setAccessible(true);
                $numRowsProperty->setValue($mysqliResult, count($results));
                */

                return $mysqliResult;
            }));

        return $mysqli;
    }
}
