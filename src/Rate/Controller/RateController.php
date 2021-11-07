<?php

namespace App\Rate\Controller;

use App\Utils\JsonResponse;
use App\Utils\Validator;
use Nelmio\ApiDocBundle\Annotation\Operation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

class RateController extends AbstractController
{
    private string $code = '1001';

    /**
     * @Route("/rate",
     *     name = "api_get_rate",
     *     methods={"GET"})
     *
     * @Operation(
     *     tags={"Rate"},
     *     summary="取得匯率",
     *     @OA\Parameter(name="currency", in="query", description="幣別", @OA\Schema(type="string")),
     *     @OA\Response(
     *         response=200,
     *         description="取得匯率結果",
     *         content={@OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(type="object",properties={
     *                 @OA\Property(property="result",type="string",description="結果",example="ok"),
     *                 @OA\Property(property="ret",type="object",properties={
     *                     @OA\Property(property="TWD",type="object",description="幣別",properties={
     *                         @OA\Property(property="JPY",type="string",description="幣別匯率",example=3.669)
     *                     })
     *                 })
     *             })
     *         )}
     *     )
     * )
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function getRate(Request $request): JsonResponse
    {
        $query = $request->query;

        $currency = strtoupper($query->get('currency'));

        $data = $this->getRateJson();

        if ($currency) {
            if (!isset($data[$currency])) {
                throw new \InvalidArgumentException('Invalid currency', "{$this->code}001");
            }

            $data = $data[$currency];
        }

        return JsonResponse::ok($data);
    }

    /**
     * @Route("rate/exchange",
     *     name = "api_post_rate_exchange",
     *     methods={"POST"})
     *
     * @Operation(
     *     tags={"Rate"},
     *     summary="轉換匯率",
     *     @OA\RequestBody(
     *         content={@OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(type="object",properties={
     *                 @OA\Property(property="from_currency",type="string",description="來源幣別"),
     *                 @OA\Property(property="to_currency",type="string",description="目標幣別"),
     *                 @OA\Property(property="amount",type="number",description="金額"),
     *             })
     *         )},
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="轉換結果",
     *         content={@OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(type="object",properties={
     *                 @OA\Property(property="result",type="string",description="結果",example="ok"),
     *                 @OA\Property(property="ret",type="object",properties={
     *                     @OA\Property(property="from_currency",type="string",description="來源幣別",example="TWD"),
     *                     @OA\Property(property="to_currency",type="string",description="目標幣別",example="TWD"),
     *                     @OA\Property(property="rate",type="number",description="匯率",example=1),
     *                     @OA\Property(property="amount",type="string",description="轉換後金額",example="100.00"),
     *                 })
     *             })
     *         )}
     *     )
     * )
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function exchangeRate(Request $request): JsonResponse
    {
        $post = $request->request;

        $fromCurrency = strtoupper($post->get('from_currency'));
        $toCurrency = strtoupper($post->get('to_currency'));
        $amount = floatval($post->get('amount'));

        $validator = new Validator($post);
        $validator->isParameterValid('from_currency', "{$this->code}002")
            ->isParameterValid('to_currency', "{$this->code}003")
            ->isParameterValid('amount', "{$this->code}004");

        $data = $this->getRateJson();

        if (!isset($data[$fromCurrency])) {
            throw new \RuntimeException('No such from_currency', "{$this->code}005");
        }

        if (!isset($data[$toCurrency])) {
            throw new \RuntimeException('No such to_currency', "{$this->code}006");
        }

        $amount = bcmul($amount, $data[$fromCurrency][$toCurrency], 2);

        $out = [
            'from_currency' => $fromCurrency,
            'to_currency' => $toCurrency,
            'rate' => $data[$fromCurrency][$toCurrency],
            'amount' => number_format($amount, 2),
        ];

        return JsonResponse::ok($out);
    }

    /**
     * 取得匯率資料
     *
     * @return array
     */
    private function getRateJson(): array
    {
        $rootDir = $this->getParameter('kernel.project_dir');

        $petsJson = file_get_contents($rootDir . '/src/Resources/rate.json');

        return json_decode($petsJson, true)['currencies'];
    }
}