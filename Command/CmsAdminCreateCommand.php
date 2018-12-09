<?php

namespace Mbarbey\CmsBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Entity\CmsAdminUser;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\ORM\EntityManagerInterface;

class CmsAdminCreateCommand extends Command
{
    protected static $defaultName = 'cms:admin:create';

    private $encoder;
    private $em;

    public function __construct(UserPasswordEncoderInterface $encoder, EntityManagerInterface $em)
    {
        $this->encoder = $encoder;
        $this->em = $em;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Create a super admin account');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $email = $io->ask('Enter your email');
        $password = $io->askHidden('Enter your desired password');

        $user = new CmsAdminUser();
        $user->setEmail($email)
             ->setRoles(['ROLE_SUPER_ADMIN'])
             ->setPassword($this->encoder->encodePassword($user, $password));

        $this->em->persist($user);
        $this->em->flush();

        $io->success("User $email created! You can now connect yourself on the /login page");
    }
}
