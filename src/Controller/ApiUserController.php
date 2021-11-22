<?php

namespace App\Controller;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

class ApiUserController extends AbstractController
{
    /**
     *
     * @OA\Get(
     *     summary="Returns info for user",
     *  @OA\Response(
     *     response="200",
     *     description="JW token",
     *
     *     @OA\MediaType(
     *       mediaType="application/json",
     *       @OA\Schema(
     *         @OA\Property(
     *           property="code",
     *           type="int"
     *         ),
     *         @OA\Property(
     *           property="username",
     *           type="string"
     *         ),
     *         @OA\Property(
     *           property="roles",
     *           type="array",
     *           @OA\Items(items={"ROLE_USER", "ROLE_ADMIN"})
     *         ),
     *         @OA\Property(
     *           property="balance",
     *           type="float"
     *         ),
     *        example={"code": 200, "username": "user@test.com", "roles": {"ROLE_USER", "ROLE_SUPER_USER"}, "balance": 20.0}
     *       )
     * )
     * )
     * )
     * * @OA\Parameter(
     *     name="Authorization",
     *     in="header",
     *     description="Header must contain JWT Authorization token",
     *     @OA\Schema(type="string"), example="Bearer <token>"
     * )
     * @Security(name="Bearer")
     */
    public function current(Request $request, JWTEncoderInterface $jwtEncoder): Response
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $userRep = $em->getRepository(User::class);

            $token = $request->headers->get('authorization');
            $token = explode(" ", $token)[1];

            $tokenData = $jwtEncoder->decode($token);

            if (isset($tokenData['username'])) {
                $user = $userRep->findOneBy(['email' => $tokenData['username']]);

                if ($user) {
                    return $this->json([
                        'code' => 200,
                        'username' => $user->getEmail(),
                        'roles' => $user->getRoles(),
                        'balance' => $user->getBalance()
                    ]);
                }
            }
            return $this->json([
                'code' => 400,
                'message' => 'Cannot find user for this email',
            ]);

        } catch (JWTDecodeFailureException $ex) {
            return $this->json([
                'code' => 400,
                'message' => $ex->getMessage(),
            ]);
        }

    }
}
