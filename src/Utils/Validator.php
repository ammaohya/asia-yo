<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\ParameterBag;

class Validator
{
    /**
     * 參數陣列
     *
     * @param array
     */
    private array $array;

    /**
     * Validator constructor.
     *
     * @param array|ParameterBag $query
     */
    public function __construct(ParameterBag|array $query)
    {
        $this->array = [];

        if (is_array($query))  {
            $this->array = $query;
        }

        if ($query instanceof ParameterBag)  {
            $this->array = $query->all();
        }
    }

    /**
     * 驗證參數
     *
     * @param string $param 參數
     * @param int $code 錯誤碼
     *
     * @return Validator
     */
    public function isParameterValid(string $param, int $code): static
    {
        if (!isset($this->array[$param])) {
            throw new \InvalidArgumentException('Invalid ' . $param, $code);
        }

        return $this;
    }
}
