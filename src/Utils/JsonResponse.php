<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\JsonResponse as BaseJsonResponse;

class JsonResponse extends BaseJsonResponse
{
    /**
     * 回傳成功JsonResponse
     *
     * @param array $data
     * @param array ...$args
     *
     * @return JsonResponse
     */
    public static function ok(array $data, array ...$args): JsonResponse
    {
        $output = [
            'result' => 'ok',
            'ret' => $data,
        ];

        foreach ($args as $arg) {
            foreach ($arg as $key => $value) {
                $output[$key] = $value;
            }
        }

        return new JsonResponse($output);
    }
}
