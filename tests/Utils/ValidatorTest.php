<?php

namespace App\Tests\Utils;

use App\Tests\UnitTestCase;
use App\Utils\Validator;
use Symfony\Component\HttpFoundation\ParameterBag;

class ValidatorTest extends UnitTestCase
{
    public function testIsParameterValid()
    {
        $validator = new Validator(['id' => 1]);

        $validator->isParameterValid('id', 1);

        $this->assertNotEmpty($validator);
    }

    public function testIsParameterValidWithEmptyArray()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'Invalid id',
            1
        );

        $validator = new Validator([]);

        $validator->isParameterValid('id', 1);
    }

    public function testIsParameterValidWithEmptyParameterBag()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'Invalid id',
            1
        );

        $validator = new Validator(new ParameterBag());

        $validator->isParameterValid('id', 1);
    }
}
