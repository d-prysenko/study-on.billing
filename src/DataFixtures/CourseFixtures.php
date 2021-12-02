<?php

namespace App\DataFixtures;

use App\Entity\Course;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CourseFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $course1 = (new Course())
            ->setName("Базы данных")
            ->setCode("db")
            ->setType(COURSE_TYPE_BUY)
            ->setCost(100)
        ;

        $manager->persist($course1);

        $course2 = (new Course())
            ->setName("Алгебра")
            ->setCode("math")
            ->setType(COURSE_TYPE_RENT)
            ->setCost(100)
            ->setDuration(new \DateInterval("P3M"))
        ;

        $manager->persist($course2);

        $manager->flush();
    }
}
