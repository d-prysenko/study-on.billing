<?php

namespace App\Controller;

use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\DTO\UserDto;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

class ApiLoginController extends AbstractController
{
    /**
     * @OA\Post(
     *     path="/api/v1/auth",
     *     summary="Authorize user",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="username",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string"
     *                 ),
     *                 example={"username": "user@study.on", "password": "SoHardPassword123"}
     *             )
     *         )
     *     ),
     *
     *  @OA\Response(
     *     response="200",
     *     description="JW token",
     *     @OA\MediaType(
     *       mediaType="application/json",
     *       @OA\Schema(
     *         @OA\Property(
     *           property="token",
     *           type="string"
     *         ),
     *        example={"token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2Mzc1NDk2NTQsImV4cCI6MTYzNzU1MzI1NCwicm9sZXMiOlsiUk9MRV9TVVBFUl9BRE1JTiIsIlJPTEVfVVNFUiJdLCJ1c2VybmFtZSI6InN1cGVyX2FkbWluQGVtYWlsLmNvbSJ9.Df2fBZirWHynelRY-Ystv675Tm6_YsGMdRE6AhCUm_XSgiArD7LXrLucKl73xRVa4cxKHLICqEvLuViv4vgdOUTBDBeg81Aa0EdlA1G6wkwTVxuBUIS1q-iI1Xm9B3b-p08kxsLdbDBR3f_2l83XXdB6gJnOcesgPm9szJT9vyGgfjLTzi2-DTXr0u_2sLgiK55ouCW5FzFTp8JGwwrkDg4jq6RRwr7vWarlc4nDSGaVoqpHe7s2HByeiSa7aZRlWeZrzrTIscGgav1N_6MY7je9Hm6mSblSzBDi63bFS9sd-kBw7O_XZGA2gcoIZZj4YJ1cTvFryYrf-IPTVZQVaj8UjcujZE4-Ck_GyvZghW2F25ls4b8jJDe1kGGYkDGStFNPoi29Hw8j-emnhFr9orj8lMZesU5LWH2j7nNsQQUAnqQeBHtJhPSPl9c0muK6U5Ds9mnMHY0-5iRVjGTtF_6WgDr5qQfX1GNOuquhO6EN1kdo6SHgPIOpwzAM9ygrffi6zqO7SSRVQ-mK3hOJqu_cIG1BHn5Dopwvnh_MeL63x090_nrcmGyoHs2h2xj1bHPBAYYez61vzwagRfROEcrOsZYR4R8QpDgut4TRUUI2jgkcXFArUiWcaXOgP3NKwGfshnkLd809zb609o2ETMoMlLrQssiUWvrjOffZyf8"}
     *       )
     * )
     * )
     * )
     * @Security(name="Bearer")
     */
    public function auth(): void
    {
    }
}
