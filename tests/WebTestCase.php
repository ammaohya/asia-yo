<?php

namespace App\Tests;

use App\Utils\JsonResponse;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;
use Symfony\Component\HttpFoundation\Request;

abstract class WebTestCase extends BaseWebTestCase
{
    /**
     * @var KernelBrowser
     */
    protected KernelBrowser $client;

    public function setUp(): void
    {
        parent::setUp();
        self::ensureKernelShutdown();
        $this->client = static::createClient();
    }

    /**
     * 給定方法、網址、參數，將執行結果解開成陣列回傳
     *
     * @param string $method 方法
     * @param string $url 網址
     * @param array $parameters 參數
     * @param array $server
     *
     * @return array
     */
    protected function getResponse(string $method, string $url, array $parameters = [], array $server = []): array
    {
        $this->client->request($method, $url, $parameters, [], $server);

        $this->assertJson($this->client->getResponse()->getContent());

        return json_decode($this->client->getResponse()->getContent(), true);
    }

    /**
     * @param string $url
     * @param array $parameters
     * @param array $server
     *
     * @return array
     */
    protected function getPOST(string $url, array $parameters = [], array $server = []): array
    {
        return $this->getResponse(Request::METHOD_POST, $url, $parameters, $server);
    }

    /**
     * @param string $url
     * @param array $parameters
     * @param array $server
     *
     * @return array
     */
    protected function getDELETE(string $url, array $parameters = [], array $server = []): array
    {
        return $this->getResponse(Request::METHOD_DELETE, $url, $parameters, $server);
    }

    /**
     * @param string $url
     * @param array $parameters
     * @param array $server
     *
     * @return array
     */
    protected function getPUT(string $url, array $parameters = [], array $server = []): array
    {
        return $this->getResponse(Request::METHOD_PUT, $url, $parameters, $server);
    }

    /**
     * @param string $url
     * @param array $parameters
     * @param array $server
     *
     * @return array
     */
    protected function getGET(string $url, array $parameters = [], array $server = []): array
    {
        return $this->getResponse(Request::METHOD_GET, $url, $parameters, $server);
    }

    /**
     * @param string $exceptionName 預期的例外類型
     * @param string $exceptionMessage 預期的例外訊息
     * @param integer $exceptionCode 預期的例外代碼
     */
    public function setExpectedException(string $exceptionName, string $exceptionMessage = '', int $exceptionCode = 0)
    {
        $this->expectException($exceptionName);

        if ($exceptionMessage) {
            $this->expectExceptionMessage($exceptionMessage);
        }

        if ($exceptionCode) {
            $this->expectExceptionCode($exceptionCode);
        }
    }

    /**
     * 斷言正常返回
     *
     * @param array|JsonResponse $result
     * @param string $expectedParam
     *
     * @return mixed
     */
    protected function assertOk(JsonResponse|array $result, string $expectedParam = 'ret'): mixed
    {
        $message = 'Incorrect OK Response';

        if ($result instanceof JsonResponse) {
            $result = json_decode($result->getContent(), true);
        }

        $this->assertIsArray($result);
        $this->assertArrayHasKey('result', $result, $message);
        $this->assertArrayHasKey($expectedParam, $result, $message);
        $this->assertEquals('ok', $result['result'], $message);
        $this->assertResponseIsSuccessful();

        return $result[$expectedParam];
    }

    /**
     * 斷言錯誤返回
     *
     * @param array|JsonResponse $result
     *
     * @return array
     */
    protected function assertError(JsonResponse|array $result): JsonResponse|array
    {
        $message = 'Incorrect Error Response';

        if ($result instanceof JsonResponse) {
            $result = json_decode($result->getContent(), true);
        }

        $this->assertArrayHasKey('result', $result, $message);
        $this->assertArrayHasKey('code', $result, $message);
        $this->assertArrayHasKey('msg', $result, $message);
        $this->assertEquals('error', $result['result'], $message);

        return $result;
    }

    /**
     * 測試成功或失敗都會調用此function
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->clearLog();
    }

    /**
     * 清除當前環境底下所有
     */
    private function clearLog()
    {
        $logsDir = $this->getContainer()->getParameter('kernel.logs_dir') . DIRECTORY_SEPARATOR;
        array_map('unlink', glob($logsDir . '/*.log'));
    }
}
