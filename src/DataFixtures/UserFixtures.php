<?php
namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;

class UserFixtures extends Fixture
{


    public function load(ObjectManager $manager): void
    {
        //$hasher = static::$container->get('security.user_password_hasher');

        $user = new User();
        $user->setEmail("user@email.com");
        $user->setPassword('$2y$13$/WS6LxZffztCfrlAoxvQkeW92PaBiy.g2WABvA02ms0KQc8UJXfsO'); // "qwerty"
        $user->setBalance(100.0);
        $user->setRoles(["ROLE_USER"]);
        $manager->persist($user);

        $user = new User();
        $user->setPassword('$2y$13$gRmt4U/L8Y1.sT1JpWT7L.rqy7mHr5RWbex3Y7r5F8lvWLQEf05.y'); // "plain_password"
        $user->setEmail("super_admin@email.com");
        $user->setBalance(50.0);
        $user->setRoles(["ROLE_SUPER_ADMIN"]);
        $manager->persist($user);

        $manager->flush();
    }
}