<?php

namespace App\Command;

use App\Repository\UserRepository;
use App\Repository\VideoGameRepository;
use App\Service\MailerService;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Attribute\Option;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\MailerInterface;

#[AsCommand(
    name: 'app:send-newsletter',
    description: 'Add a short description for your command',
)]
class SendNewsletterCommand{
    public function __invoke( SymfonyStyle $io, MailerService $mailerService, UserRepository $userRepository, MailerInterface $mailer, VideoGameRepository $videoGameRepository,): int 
    {
        $date = new \DateTime('today');
        $date2 = new \DateTime();
        $date2->modify('+1 week');
        $futureVideoGames = $videoGameRepository->findReleasingBetweenDates($date, $date2);
        $usersSubscribed = $userRepository->findSubscribedUsers();
        foreach($usersSubscribed as $user){
            $mailerService->sendEmail($user->getEmail(), 'Liste des jeux vidéos qui sortent dans les 7 prochains jours', $futureVideoGames);
        }
        $io->success("L'email a été envoyé avec succès!");

        return Command::SUCCESS;
    }
}
