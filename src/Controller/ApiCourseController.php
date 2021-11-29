<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Transaction;
use App\Entity\User;
use App\Exception\NotEnoughFundsException;
use App\Repository\CourseRepository;
use App\Service\PaymentService;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiCourseController extends AbstractController
{
    private function jsonMessage(int $code, string $message): JsonResponse
    {
        return $this->json([
            'code' => $code,
            'message' => $message,
        ]);
    }

    public function getCourses(CourseRepository $courseRepository): Response
    {
        return $this->json($courseRepository->findAll());
    }

    public function getCourse(string $code, CourseRepository $courseRepository): Response
    {
        return $this->json($courseRepository->findOneBy(['code' => $code]));
    }

    public function buyCourse(string $code, Request $request, PaymentService $paymentService): Response
    {
        $em = $this->getDoctrine()->getManager();
        $userRep = $em->getRepository(User::class);
        $courseRep = $em->getRepository(Course::class);

        $course = $courseRep->findOneBy(['code' => $code]);

        if (is_null($course)) {
            return $this->jsonMessage(404, 'There is no such course');
        }

        $user = $userRep->fetchUserByRequest($request);

        try {
            $paymentService->pay($user, $course);
        } catch (NotEnoughFundsException $e) {
            return $this->jsonMessage(400, 'Not enough funds!');
        } catch (\Throwable $e) {
        }

        return $this->json([
            'code' => 200,
            'course_type' => (($course->getType() == 0) ? 'buy' : 'rent'),
            'balance' => $user->getBalance()
        ]);
    }
}
