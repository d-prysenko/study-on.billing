<?php

namespace App\Controller;

use App\DTO\UserDto;
use App\Entity\User;
use JMS\Serializer\SerializerBuilder;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;


class ApiRegistrationController extends AbstractController
{
    /**
     * @OA\Post(path="/api/v1/register", method="POST")

     * @OA\Response(
     *     response="200",
     *     description="JW token"
     * )
     * @Security(name="Bearer")
     */
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator): Response
    {
        $em = $this->getDoctrine()->getManager();
//
//        $parameters = json_decode($request->getContent(), true);
//
//        $user = new User();
//
//        $hashedPassword = $passwordHasher->hashPassword($user, $parameters['password']);
//        $user->setPassword($hashedPassword);
//        $user->setEmail($parameters['username']);
//        $user->setRoles(["ROLE_USER"]);
//
//        $em->persist($user);
//        $em->flush();

        $serializer = SerializerBuilder::create()->build();
        $userDto = $serializer->deserialize($request->getContent(), UserDto::class, 'json');

        $user = new User();

        $errors = $validator->validate($userDto);

        dd($errors);

        if (count($errors) > 0)
        {
            return $this->json([
                'code' => 400,
                'message' => $errors->get(0)->getMessage(),
            ]);
        }

        $user = User::fromDto($userDto);


        $em->persist($user);
        $em->flush();

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ApiRegistrationController.php',
        ]);
    }

}
