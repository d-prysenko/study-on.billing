<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Transaction;
use App\Entity\User;
use App\Repository\TransactionRepository;
use App\Service\PaymentService;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiTransactionsController extends ApiAbstractController
{
    public function getTransactions(Request $request, JWTEncoderInterface $jwtEncoder, PaymentService $paymentService): Response
    {
        $em = $this->getDoctrine()->getManager();
        $transactionRep = $em->getRepository(Transaction::class);
        $userRep = $em->getRepository(User::class);

        $user = $userRep->fetchUserByRequest($request);

        return $this->json($transactionRep->findAllByUser($user));
    }
}
