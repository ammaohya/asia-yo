<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        $request = $event->getRequest();

        $pathInfo = $request->getPathInfo();

        // 暫時利用網址開頭/api/判定需輸出json格式的錯誤訊息，避免使用者沒代到format
        if (preg_match_all("/^\/api\//", $pathInfo)) {
            $request->setRequestFormat('json');
        }

        // 非api輸出預設錯誤畫面
        if ($request->getRequestFormat() != 'json') {
            return;
        }

        $exception = $event->getThrowable();
        $code = $exception->getCode();
        $message = $exception->getMessage();

        $value = [
            'result' => 'error',
            'code' => $code,
            'msg' => $message
        ];

        $content = json_encode($value);

        /**
         * As Symfony ensures that the Response status code is set to
         * the most appropriate one depending on the exception,
         * setting the status on the response won't work.
         * If you want to overwrite the status code (which you should not
         * without a good reason), set the X-Status-Code header:
         */
        $exceptionType = get_class($exception);
        $expectedType = [
            'InvalidArgumentException',
            'RuntimeException',
        ];

        $statusCode = 200;

        if (!in_array($exceptionType, $expectedType)) {
            $statusCode = 500;
        }

        $response = new Response($content, $statusCode);

        $event->setResponse($response);
    }
}
