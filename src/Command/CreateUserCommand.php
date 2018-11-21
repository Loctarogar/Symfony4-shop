<?php declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use App\Services\UserService;

/**
 * Class CreateUserCommand
 * @package App\Command
 */
class CreateUserCommand extends Command
{
    /**
     * @var
     */
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;

        parent::__construct();
    }

    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('app:create-user')
            ->setDescription('Creates a new user.')
            ->addArgument('name', InputArgument::REQUIRED, 'Name')
            ->addArgument('password', InputArgument::REQUIRED, 'Password')
            ->addArgument('email', InputArgument::REQUIRED, 'Email')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $text = 'User ->'.$input->getArgument('name').'<- Has been successfully created with password ->'.$input->getArgument('password').'<-';
        $this->userService->createUser(
            $input->getArgument('name'),
            $input->getArgument('password'),
            $input->getArgument('email')
        );

        $output->writeln([
            'User creating',
            '=============',
        ]);
        $output->writeln($text.' !');
    }
}
