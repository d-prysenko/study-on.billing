<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\PaymentService;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\UserNotFoundException;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

class ApiUserController extends ApiAbstractController
{
    /**
     * @OA\Get(
     *	  summary="Returns information about current user",
     *	  @OA\Response(
     *		 response="200",
     *		 description="JW token",
     *		 @OA\MediaType(
     *			 mediaType="application/json",
     *			 @OA\Schema(
     *				 @OA\Property(
     *					 property="code",
     *					 type="int"
     *				 ),
     *				 @OA\Property(
     *					 property="username",
     *					 type="string"
     *				 ),
     *				 @OA\Property(
     *					 property="roles",
     *					 type="array",
     *                   @OA\Items(items={"ROLE_USER", "ROLE_ADMIN"})
     *				 ),
     *				 @OA\Property(
     *					 property="balance",
     *					 type="float"
     *				 ),
     *				 example={"code": 200, "username": "user@test.com", "roles": {"ROLE_USER", "ROLE_SUPER_USER"}, "balance": 20.0}
     *			 )
     *		 )
     *	  )
     * )
     *
     * @OA\Parameter(
     *     name="Authorization",
     *     in="header",
     *     description="JWT Authorization token",
     *     required=true,
     *     @OA\Schema(type="string", example="Bearer *token*")
     * )
     *
     * @Security(name="Bearer")
     */
    public function current(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $userRep = $em->getRepository(User::class);


        $user = $userRep->fetchUserByRequest($request);


        return $this->json([
            'code' => 200,
            'username' => $user->getEmail(),
            'roles' => $user->getRoles(),
            'balance' => $user->getBalance()
        ]);
    }
}
