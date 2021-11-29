<?php

namespace App\Service;

use App\Entity\Course;
use App\Entity\Transaction;
use App\Entity\User;
use App\Exception\NotEnoughFundsException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

class PaymentService
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @throws NotEnoughFundsException|Throwable
     */
    public function pay(User $user, Course $course): void
    {
        if ($user->getBalance() < $course->getCost()) {
            throw new NotEnoughFundsException();
        }

        $this->em->wrapInTransaction(function($em) use ($user, $course) {
            $user->setBalance($user->getBalance() - $course->getCost());

            $transaction = new Transaction();
            $transaction->setCourse($course);
            $transaction->setAmount($course->getCost());
            $transaction->setBillingUser($user);
            $transaction->setType(TRANSACTION_PAYMENT);

            $em->persist($transaction);
        });
    }

    public function deposit(User $user, int $amount): void
    {
        $this->em->wrapInTransaction(function($em) use ($user, $amount) {
            $user->setBalance($user->getBalance() + $amount);

            $transaction = new Transaction();
            $transaction->setAmount($amount);
            $transaction->setBillingUser($user);
            $transaction->setType(TRANSACTION_DEPOSIT);

            $em->persist($transaction);
        });
    }

}