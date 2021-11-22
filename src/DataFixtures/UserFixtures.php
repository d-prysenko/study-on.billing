<?php
namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->passwordHasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        //$hasher = static::$container->get('security.user_password_hasher');

        $user = new User();
        $user->setEmail("user@email.com");
        $user->setPassword($this->passwordHasher->hashPassword($user, 'qwerty'));
        $user->setBalance(100.0);
        $user->setRoles(["ROLE_USER"]);
        $manager->persist($user);

        $user = new User();
        $user->setPassword($this->passwordHasher->hashPassword($user, 'plain_password'));
        $user->setEmail("super_admin@email.com");
        $user->setBalance(50.0);
        $user->setRoles(["ROLE_SUPER_ADMIN"]);
        $manager->persist($user);

        $manager->flush();
    }
}