<?php

namespace App\Controller;

use Gesdinet\JWTRefreshTokenBundle\Service\RefreshToken;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiRefreshTokenController extends AbstractController
{
    /**
     * @Nelmio\Operation(
     *    tags={"Authorization"},
     *	  summary="Refreshes token",
     *	  @OA\RequestBody(
     *	 	  @OA\MediaType(
     *			  mediaType="application/x-www-form-urlencoded",
     *			  @OA\Schema(
     *				  @OA\Property(
     *					  property="refresh_token",
     *					  type="string",
     *                    example="8e99b314e8473f37d32a6ecb0707d50db8bb54d66a19edfeac4d9fb97f67cbc379fe8549ae5804492d6840eeaecb0b6dc78e6bcaaf0e6c4f829787b3ffe12f85"
     *				  )
     *			  )
     *		  )
     *	  ),
     *	  @OA\Response(
     *		 response="200",
     *		 description="JW token",
     *		 @OA\MediaType(
     *			 mediaType="application/json",
     *			 @OA\Schema(
     *				 @OA\Property(
     *					 property="token",
     *					 type="string"
     *				 ),
     *				 @OA\Property(
     *					 property="refresh_token",
     *					 type="string"
     *				 ),
     *				 example={"token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE2Mzc1NDk2NTQsImV4cCI6MTYzNzU1MzI1NCwicm9sZXMiOlsiUk9MRV9TVVBFUl9BRE1JTiIsIlJPTEVfVVNFUiJdLCJ1c2VybmFtZSI6InN1cGVyX2FkbWluQGVtYWlsLmNvbSJ9.Df2fBZirWHynelRY-Ystv675Tm6_YsGMdRE6AhCUm_XSgiArD7LXrLucKl73xRVa4cxKHLICqEvLuViv4vgdOUTBDBeg81Aa0EdlA1G6wkwTVxuBUIS1q-iI1Xm9B3b-p08kxsLdbDBR3f_2l83XXdB6gJnOcesgPm9szJT9vyGgfjLTzi2-DTXr0u_2sLgiK55ouCW5FzFTp8JGwwrkDg4jq6RRwr7vWarlc4nDSGaVoqpHe7s2HByeiSa7aZRlWeZrzrTIscGgav1N_6MY7je9Hm6mSblSzBDi63bFS9sd-kBw7O_XZGA2gcoIZZj4YJ1cTvFryYrf-IPTVZQVaj8UjcujZE4-Ck_GyvZghW2F25ls4b8jJDe1kGGYkDGStFNPoi29Hw8j-emnhFr9orj8lMZesU5LWH2j7nNsQQUAnqQeBHtJhPSPl9c0muK6U5Ds9mnMHY0-5iRVjGTtF_6WgDr5qQfX1GNOuquhO6EN1kdo6SHgPIOpwzAM9ygrffi6zqO7SSRVQ-mK3hOJqu_cIG1BHn5Dopwvnh_MeL63x090_nrcmGyoHs2h2xj1bHPBAYYez61vzwagRfROEcrOsZYR4R8QpDgut4TRUUI2jgkcXFArUiWcaXOgP3NKwGfshnkLd809zb609o2ETMoMlLrQssiUWvrjOffZyf8", "refresh_token": "8e99b314e8473f37d32a6ecb0707d50db8bb54d66a19edfeac4d9fb97f67cbc379fe8549ae5804492d6840eeaecb0b6dc78e6bcaaf0e6c4f829787b3ffe12f85"}
     *			 )
     *		 )
     *	  )
     * )
     *
     */
    public function refresh(Request $request, RefreshToken $refreshService): Response
    {
        return $refreshService->refresh($request);
    }
}
