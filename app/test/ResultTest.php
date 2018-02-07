<?php

namespace app\test;

/**
 * Testing Response class
 *
 * @author Andriy Ivanchenko
 */
class ResponseTest extends \PHPUnit\Framework\TestCase {

    public function testGetDataReturnsDataExpectedValues() {
        $data = $expectedData = [
            'param1' => 'value1',
            'param2' => 'param2'
        ];

        $result = new \app\Response(\app\Response::STATUS_SUCCESS, 'Success!', $data);
        $currentData = $result->getData();

        $this->assertArraySubset($expectedData, $currentData);
    }

    public function testGetMessageReturnsMessageExpectedValue() {
        $expectedMessage = $message = 'Success!';
        $result = new \app\Response(\app\Response::STATUS_SUCCESS, $message);
        $actualMessage = $result->getMessage();

        $this->assertEquals($expectedMessage, $actualMessage);
    }

    public function testIsSuccessReturnsTrue() {
        $result = new \app\Response(\app\Response::STATUS_SUCCESS, 'Success!');

        $this->assertTrue($result->isSuccess());
    }

    public function testIsSuccessReturnsFalse() {
        $result = new \app\Response(\app\Response::STATUS_FAIL, 'Failure!');

        $this->assertFalse($result->isSuccess());
    }

    public function testCreateSuccessReturnsSuccessfulResponse() {
        $result = \app\Response::createSuccess('Some message!');

        $this->assertInstanceOf(\app\Response::class, $result);
        $this->assertTrue($result->isSuccess());
    }

    public function testCreateFailReturnsFailureResponse() {
        $result = \app\Response::createFail('Some message!');

        $this->assertInstanceOf(\app\Response::class, $result);
        $this->assertFalse($result->isSuccess());
    }

    public function testToArrayReturnsArrayExpectedValues() {
        $result = new \app\Response(\app\Response::STATUS_SUCCESS, 'Some message!');
        $actualArray = $result->toArray();

        $this->assertArrayHasKey('status', $actualArray);
        $this->assertArrayHasKey('message', $actualArray);
        $this->assertArrayHasKey('data', $actualArray);
    }

}
