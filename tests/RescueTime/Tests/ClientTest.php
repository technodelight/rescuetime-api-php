<?php
namespace RescueTime\Tests;

use RescueTime\RequestQueryParameters as Params;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $apiKey = 'secret';
        $this->Client = new \RescueTime\Client($apiKey);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();
        unset($this->Client);
    }

    public function testGetByRank()
    {
        $data = file_get_contents(__DIR__ . '/Fakes/rank.json');

        $requestParams = array('perspective' => 'rank');

        $httpClient = $this->getMockBuilder('\RescueTime\HttpClient')
            ->setConstructorArgs(array($requestParams))
            ->setMethods(array('request'))
            ->getMock();
        $httpClient->expects($this->once())
            ->method('request')
            ->will($this->returnValue(json_decode($data, true)));

        $this->Client->httpClient = $httpClient;

        $activities = $this->Client->getActivities(new Params($requestParams));

        $this->assertTrue(is_array($activities), "Expected activities to be an array");
        $this->assertEquals(15, count($activities), "Expected to return 15 activities");
    }

    public function testGetByInterval()
    {
        $data = file_get_contents(__DIR__ . '/Fakes/interval.json');

        $requestParams = array('perspective' => 'interval');

        $httpClient = $this->getMockBuilder('\RescueTime\HttpClient')
            ->setConstructorArgs(array($requestParams))
            ->setMethods(array('request'))
            ->getMock();
        $httpClient->expects($this->once())
            ->method('request')
            ->will($this->returnValue(json_decode($data, true)));

        $this->Client->httpClient = $httpClient;

        $activities = $this->Client->getActivities(new Params($requestParams));

        $this->assertTrue(is_array($activities), "Expected activities to be an array");
        $this->assertEquals(14, count($activities), "Expected to return 14 activities");
    }

    public function testGetByMember()
    {
        $data = file_get_contents(__DIR__ . '/Fakes/member.json');

        $requestParams = array('perspective' => 'member');

        $httpClient = $this->getMockBuilder('\RescueTime\HttpClient')
            ->setConstructorArgs(array($requestParams))
            ->setMethods(array('request'))
            ->getMock();
        $httpClient->expects($this->once())
            ->method('request')
            ->will($this->returnValue(json_decode($data, true)));

        $this->Client->httpClient = $httpClient;

        $activities = $this->Client->getActivities(new Params($requestParams));

        $this->assertTrue(is_array($activities), "Expected activities to be an array");
        $this->assertEquals(17, count($activities), "Expected to return 17 activities");
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Perspective must be one of rank, interval, member
     */
    public function testInvalidPerspective()
    {
        $this->Client->getActivities(new Params(['perspective' => 'invalid_perspective_name']));
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Resolution time must be one of month, week, day, hour
     */
    public function testInvalidResolutionTime()
    {
        $this->Client->getActivities(new Params(['resolution_time' => 'invalid_resolution_time']));
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage Restrict kind must be one of category, activity, productivity
     */
    public function testInvalidRestrictKind()
    {
        $this->Client->getActivities(new Params(['restrict_kind' => 'invalid_restrict_kind']));
    }
}
