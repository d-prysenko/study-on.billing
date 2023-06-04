<?php

namespace App\Service;

use App\Entity\Course;
use App\Entity\Transaction;
use App\Entity\User;
use App\Exception\AlreadyExistsException;
use App\Exception\NotEnoughFundsException;
use Doctrine\ORM\EntityManagerInterface;
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

        $transactionRep = $this->em->getRepository(Transaction::class);

        $transactionForTheCourse = $transactionRep->findOneBy(['billing_user' => $user, 'course' => $course]);

        if (!is_null($transactionForTheCourse) && $course->getType() !== COURSE_TYPE_RENT) {
            throw new AlreadyExistsException();
        }

        $this->em->wrapInTransaction(function($em) use ($user, $course) {
            $user->setBalance($user->getBalance() - $course->getCost());

            $transaction = (new Transaction())
                ->setCourse($course)
                ->setAmount($course->getCost())
                ->setBillingUser($user)
                ->setType(TRANSACTION_PAYMENT)
            ;

            if ($course->getType() === COURSE_TYPE_RENT) {
                $transaction->setExpiration((new \DateTime())->add($course->getDuration()));
            }

            $em->persist($transaction);
        });
    }

    public function deposit(User $user, int $amount): void
    {
        $this->em->wrapInTransaction(function($em) use ($user, $amount) {
            $user->setBalance($user->getBalance() + $amount);

            $transaction = (new Transaction())
                ->setAmount($amount)
                ->setBillingUser($user)
                ->setType(TRANSACTION_DEPOSIT)
            ;

            $em->persist($transaction);
        });
    }

}