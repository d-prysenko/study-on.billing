<?php

namespace App\Controller;

use App\DTO\CourseDTO;
use App\DTO\UserDto;
use App\Entity\Course;
use App\Entity\Transaction;
use App\Entity\User;
use App\Exception\AlreadyExistsException;
use App\Exception\NotEnoughFundsException;
use App\Repository\CourseRepository;
use App\Repository\TransactionRepository;
use App\Service\PaymentService;
use JMS\Serializer\SerializerBuilder;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiCourseController extends ApiAbstractController
{
    public function getCourses(CourseRepository $courseRepository): Response
    {
        return $this->json($courseRepository->findAll());
    }

    public function getCourse(string $code, CourseRepository $courseRepository): Response
    {
        return $this->json($courseRepository->findByCode($code));
    }

    public function getUserCourses(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $userRep = $em->getRepository(User::class);
        $transactionRep = $em->getRepository(Transaction::class);

        $user = $userRep->fetchUserByRequest($request);

        return $this->json(
            $transactionRep->findUserCourses($user)
        );
    }

    public function createCourse(Request $request, CourseRepository $courseRep): Response
    {
        $em = $this->getDoctrine()->getManager();

        $serializer = SerializerBuilder::create()->build();
        $courseDto = $serializer->deserialize($request->getContent(), CourseDTO::class, 'json');

        $course = $em->getRepository(Course::class)->findByCode($courseDto->code);

        if (!is_null($course))
        {
            return $this->jsonMessage(400,'The course code already in use!');
        }

        $course = Course::fromDTO($courseDto);

        if ($course->getType() === COURSE_TYPE_RENT && $course->getDuration() === null) {
            return $this->jsonMessage(400, 'If the type of the course is "rent" than "duration" must be set!');
        }

        $em->persist($course);
        $em->flush();

        return $this->jsonMessage(201, 'Success!');
    }

    public function deleteCourse(string $code, Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $courseRep = $em->getRepository(Course::class);

        $course = $courseRep->findByCode($code);

        if (is_null($course)) {
            return $this->jsonMessage(404, 'There is no such course');
        }

        $em->remove($course);
        $em->flush();

        return $this->jsonMessage(200, 'Course deleted');
    }

    public function editCourse(string $code, Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $courseRep = $em->getRepository(Course::class);

        $course = $courseRep->findByCode($code);

        if (is_null($course)) {
            return $this->jsonMessage(404, 'There is no such course');
        }

        $serializer = SerializerBuilder::create()->build();
        $courseDto = $serializer->deserialize($request->getContent(), CourseDTO::class, 'json');

        if ($courseDto->name !== null) {
            $course->setName($courseDto->name);
        }

        if (($courseDto->price !== null ||
            $course->getCost() !== 0) &&
            ($courseDto->type === "buy" ||
            $course->getType() === COURSE_TYPE_BUY ||
            $courseDto->type === "rent" ||
            $course->getType() === COURSE_TYPE_RENT)
        )
        {
            if ($courseDto->price !== null) {
                $course->setCost($courseDto->price);
            }

            if ($courseDto->type !== null) {
                $course->setType(Course::stringTypeToInt($courseDto->type));
            }
        }

        if ($course->getType() === COURSE_TYPE_RENT) {
            if ($courseDto->duration !== null) {
                $course->setDuration($courseDto->duration);
            } else {
                $course->setDuration(new \DateInterval('P1M'));
            }
        }

        $em->flush();

        try {
            return $this->json([
                'code' => 200,
                'course' => json_encode($course, JSON_THROW_ON_ERROR),
            ]);
        } catch (\JsonException $e) {
            return $this->jsonMessage(500, 'Unable to encode response');
        }
    }

    public function buyCourse(string $code, Request $request, PaymentService $paymentService): Response
    {
        $em = $this->getDoctrine()->getManager();
        $userRep = $em->getRepository(User::class);
        $courseRep = $em->getRepository(Course::class);

        $course = $courseRep->findByCode($code);

        if (is_null($course)) {
            return $this->jsonMessage(404, 'There is no such course');
        }

        $user = $userRep->fetchUserByRequest($request);

        try {
            $paymentService->pay($user, $course);
        } catch (NotEnoughFundsException $e) {
            return $this->jsonMessage(400, 'Not enough funds!');
        } catch (AlreadyExistsException $e) {
            return $this->jsonMessage(400, 'You already have this course!');
        }

        return $this->json([
            'code' => 200,
            'course_type' => Course::intTypeToString($course->getType()),
            'balance' => $user->getBalance()
        ]);
    }
}
