<?php
namespace App\DataFixtures;

use App\Service\PaymentService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;
    private PaymentService $paymentService;

    public function __construct(UserPasswordHasherInterface $hasher, PaymentService $paymentService)
    {
        $this->passwordHasher = $hasher;
        $this->paymentService = $paymentService;
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user
            ->setEmail("user@email.com")
            ->setPassword($this->passwordHasher->hashPassword($user, 'qwerty'))
            ->setRoles(["ROLE_USER"])
        ;

        $manager->persist($user);

        $this->paymentService->deposit($user, 250);

        $user = new User();
        $user
            ->setPassword($this->passwordHasher->hashPassword($user, 'plain_password'))
            ->setEmail("super_admin@email.com")
            ->setRoles(["ROLE_SUPER_ADMIN"])
        ;

        $manager->persist($user);

        $this->paymentService->deposit($user, 500);

        $manager->flush();
    }
}