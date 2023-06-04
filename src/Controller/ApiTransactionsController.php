<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Entity\User;
use App\Service\PaymentService;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Nelmio\ApiDocBundle\Annotation as Nelmio;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiTransactionsController extends ApiAbstractController
{
    /**
     * @Nelmio\Operation(
     *    tags={"User"},
     *	  summary="Get all transactions"
     * )
     *
     * @Nelmio\Security(name="Bearer")
     */
    public function getTransactions(Request $request, JWTEncoderInterface $jwtEncoder, PaymentService $paymentService): Response
    {
        $em = $this->getDoctrine()->getManager();
        $transactionRep = $em->getRepository(Transaction::class);
        $userRep = $em->getRepository(User::class);

        $user = $userRep->fetchUserByRequest($request);

        return $this->json($transactionRep->findAllByUser($user));
    }
}
