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


class ApiRegistrationController extends AbstractController
{
    /**
     * @Route(name="api_v1_register", path="/api/v1/register")
     * @OA\Post(
     *     path="/api/v1/register",
     *     method="POST",
     *     summary="Register user",
     *     description="kekw"
     *
     * )
     * @Security(name="Bearer")
     */
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator, JWTTokenManagerInterface $JWTManager): Response
    {
        $em = $this->getDoctrine()->getManager();

        $serializer = SerializerBuilder::create()->build();
        $userDto = $serializer->deserialize($request->getContent(), UserDto::class, 'json');

        $errors = $validator->validate($userDto);

        if (count($errors) > 0)
        {
            return $this->json([
                'code' => 401,
                'message' => $errors->get(0)->getMessage(),
            ], 401);
        }

        $user = $em->getRepository(User::class)->findOneBy([
            'email' => $userDto->username,
        ]);

        if (!is_null($user))
        {
            return $this->json([
                'code' => 401,
                'message' => 'Email already in use!',
            ], 401);
        }

        $user = User::fromDto($userDto);
        $user->setPassword($passwordHasher->hashPassword($user, $userDto->password));

        $em->persist($user);
        $em->flush();

        return $this->json([
            'code' => 201,
            'token' => $JWTManager->create($user)
        ], 201);
    }

}
