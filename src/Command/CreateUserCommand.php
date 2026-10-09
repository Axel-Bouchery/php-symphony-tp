<?php
namespace App\Command;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-user',
    description: 'Créer un utilisateur pour tester le login',
)]
class CreateUserCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $io = new SymfonyStyle($input, $output);

        $user = $this->entityManager
            ->getRepository(Utilisateur::class)
            ->findOneBy(['email' => 'test@example.com']);

        if (!$user) {
            $user = new Utilisateur();
            $user->setNom('Test');
            $user->setPrenom('User');
            $user->setEmail('test@example.com');
        }
        $user->setNom('Test');
        $user->setPrenom('User');
        $user->setEmail('test@example.com');

        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            'Test1234!'
        );

        $user->setPassword($hashedPassword);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('Utilisateur créé avec succès.');

        return Command::SUCCESS;
    }
}
