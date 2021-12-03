<?php

namespace App\Controller;

use App\DTO\UserDto;
use App\Entity\User;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use JMS\Serializer\SerializerBuilder;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;

class ApiRegistrationController extends ApiAbstractController
{
    /**
     * @OA\Post(
     *	  summary="Registrates new user",
     *	  @OA\RequestBody(
     *	 	  @OA\MediaType(
     *			  mediaType="application/json",
     *			  @OA\Schema(
     *				  @OA\Property(
     *					  property="username",
     *					  type="string"
     *				  ),
     *				  @OA\Property(
     *					  property="password",
     *					  type="string"
     *				  ),
     *				  example={"username": "user@study.on", "password": "SoHardPassword123"}
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
     * @Security(name="Bearer")
     */
    public function register(
        Request $request,
        RefreshTokenManagerInterface $refreshTokenManager,
        UserPasswordHasherInterface $passwordHasher,
        ValidatorInterface $validator,
        JWTTokenManagerInterface $JWTManager
    ): Response {

        $em = $this->getDoctrine()->getManager();

        $serializer = SerializerBuilder::create()->build();
        $userDto = $serializer->deserialize($request->getContent(), UserDto::class, 'json');

        $errors = $validator->validate($userDto);

        if (count($errors) > 0)
        {
            return $this->jsonMessage(401, $errors->get(0)->getMessage());
        }

        $user = $em->getRepository(User::class)->findByEmail($userDto->username);

        if (!is_null($user))
        {
            return $this->jsonMessage(401, 'Email already in use!');
        }

        $user = User::fromDto($userDto);
        $user->setPassword($passwordHasher->hashPassword($user, $userDto->password));

        $em->persist($user);
        $em->flush();

        $refreshToken = $refreshTokenManager->create();
        $refreshToken->setUsername($user->getEmail());
        $refreshToken->setRefreshToken();
        $refreshToken->setValid((new \DateTime())->modify('+1 month'));
        $refreshTokenManager->save($refreshToken);

        return $this->json([
            'code' => 201,
            'token' => $JWTManager->create($user),
            'refresh_token' => $refreshToken->getRefreshToken()
        ], 201);
    }
}
