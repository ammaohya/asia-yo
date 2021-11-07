<?php

namespace App\Tests\Rate\Controller;

use App\Tests\WebTestCase;

class RateControllerTest extends WebTestCase
{
    // It's just functional test, I lost Unit test, I'm so sorry. I am more familiar with symfony3, It's my first time to use symfony5, so I have some problem in this.
    public function testGetRate()
    {
        $this->assertOk($this->getGET('/api/rate'));
        $this->assertOk($this->getGET('/api/rate', ['currency' => 'twd']));
    }

    public function testGetRateInValid()
    {
        $expected = [
            'result' => 'error',
            'code' => 1001001,
            'msg' => 'Invalid currency',
        ];


        $actual = $this->assertError($this->getGET('/api/rate', ['currency' => 'tt']));
        $this->assertEquals($expected, $actual);
    }

    public function testExchangeRate()
    {
        $params = [
            'from_currency' => 'twd',
            'to_currency' => 'twd',
            'amount' => '1',
        ];

        $this->assertOk($this->getPOST('/api/rate/exchange', $params));
    }

    public function testExchangeRateWithInValid()
    {
        // test 1001002
        $params = [
            'to_currency' => 'twd',
            'amount' => '1',
        ];

        $expected = [
            'result' => 'error',
            'code' => 1001002,
            'msg' => 'Invalid from_currency',
        ];

        $actual = $this->assertError($this->getPOST('/api/rate/exchange', $params));
        $this->assertEquals($expected, $actual);

        // test 1001003
        $params = [
            'from_currency' => 'twd',
            'amount' => '1',
        ];

        $expected = [
            'result' => 'error',
            'code' => 1001003,
            'msg' => 'Invalid to_currency',
        ];

        $actual = $this->assertError($this->getPOST('/api/rate/exchange', $params));
        $this->assertEquals($expected, $actual);

        // test 1001004
        $params = [
            'from_currency' => 'twd',
            'to_currency' => 'twd',
        ];

        $expected = [
            'result' => 'error',
            'code' => 1001004,
            'msg' => 'Invalid amount',
        ];

        $actual = $this->assertError($this->getPOST('/api/rate/exchange', $params));
        $this->assertEquals($expected, $actual);

        // test 1001005
        $params = [
            'from_currency' => 'owo',
            'to_currency' => 'twd',
            'amount' => '1',
        ];

        $expected = [
            'result' => 'error',
            'code' => 1001005,
            'msg' => 'No such from_currency',
        ];

        $actual = $this->assertError($this->getPOST('/api/rate/exchange', $params));
        $this->assertEquals($expected, $actual);

        // test 1001006
        $params = [
            'from_currency' => 'twd',
            'to_currency' => 'owo',
            'amount' => '1',
        ];

        $expected = [
            'result' => 'error',
            'code' => 1001006,
            'msg' => 'No such to_currency',
        ];

        $actual = $this->assertError($this->getPOST('/api/rate/exchange', $params));
        $this->assertEquals($expected, $actual);
    }
}
