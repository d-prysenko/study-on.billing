<?php

namespace App\Command;

use App\Repository\TransactionRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class PaymentEndingNotification extends Command
{
    protected static $defaultName = 'payment:ending:notification';
    private MailerInterface $mailer;
    private TransactionRepository $transactionRepository;

    public function __construct(MailerInterface $mailer, TransactionRepository $transactionRepository)
    {
        $this->mailer = $mailer;
        $this->transactionRepository = $transactionRepository;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setHelp("This command sends hello email");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Sending email..");

        $entries = $this->transactionRepository->findAllPreExpiredCourses();
dd($entries);
        foreach ($entries as $entry) {
            $email = (new TemplatedEmail())
                ->from('notification@studyon.com')
                ->to($entry['email'])
                ->subject('Аренда курса заканчивается')
                ->text("Подписка на курс '{$entry['name']}' заканчивается завтра")
                ->htmlTemplate('emails/membership_ending.html.twig')
                ->context([
                    'course_name' => $entry['name'],
                    'expiration_date' => $entry['expiration']
                ])
            ;

            try {
                $this->mailer->send($email);
            } catch (TransportExceptionInterface $e) {
                $output->writeln("Failed to send email");
                $output->writeln($e->getMessage());
                return Command::FAILURE;
            }
        }



        return Command::SUCCESS;
    }
}