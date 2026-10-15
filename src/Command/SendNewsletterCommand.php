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
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsCommand(
    name: 'app:send-newsletter',
    description: "Envoie une newsletter hebdomadaire avec la liste des jeu vidéos qui sortent dans les 7 prochains jours",
)]
#[AsCronTask('30 8 * * 1', 'Europe/Paris')]
class SendNewsletterCommand{
    public function __invoke(SymfonyStyle $io, MailerService $mailerService, UserRepository $userRepository, MailerInterface $mailer, VideoGameRepository $videoGameRepository,): int 
    {
        $start = new \DateTime('today');
        $end = new \DateTime();
        $end->modify('+1 week');
        $futureVideoGames = $videoGameRepository->findReleasingBetweenDates($start, $end);
        $usersSubscribed = $userRepository->findSubscribedUsers();
        foreach($usersSubscribed as $user){
            echo "Envoi de l'email à : " . $user->getEmail() . "\n";
            $mailerService->sendEmail($user->getEmail(), 'Liste des jeux vidéos qui sortent dans les 7 prochains jours', $futureVideoGames);
        }
        $io->success("L'email a été envoyé avec succès!");

        return Command::SUCCESS;
    }
}
