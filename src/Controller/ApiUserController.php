<?php

namespace App\Controller;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiUserController extends AbstractController
{
    public function current(Request $request, JWTEncoderInterface $jwtEncoder): Response
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $userRep = $em->getRepository(User::class);

            $token = $request->headers->get('authorization');
            $token = explode(" ", $token)[1];

            $tokenData = $jwtEncoder->decode($token);

            $user = $userRep->findOneBy(['email' => $tokenData['username']]);

            if ($user) {
                return $this->json([
                    'username' => $user->getEmail(),
                    'roles' => $user->getRoles(),
                    'balance' => $user->getBalance()
                ]);
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
