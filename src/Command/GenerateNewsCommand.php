<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use jcobhams\NewsApi\NewsApi;
use App\Entity\News;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Mapping as ORM;
//use Doctrine\Bundle\DoctrineBundle\Registry;

#[AsCommand(
    name: 'GenerateNews',
    description: 'Add a short description for your command',
    hidden: false,
    aliases: ['app:get_news']
)]
class GenerateNewsCommand extends Command
{

    private $em; 

    public function __construct(ManagerRegistry $registry)
    {

        $this->em = $registry->getManager();
        parent::__construct();
    }
    protected function configure(): void
    {

        // $this
        //     ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
        //     ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        // ;
        $this->setHelp('This command allows you to create a user...');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $newsapi = new NewsApi('539b9da3078d44c68df6f62b8de032c6');
        $all_articles = $newsapi->getEverything('bitcoin');
          // dump($all_articles->articles);
          // exit;
        foreach ($all_articles->articles as $value) {
            $em = $this->em;
            $news=new News();
            $news->setTitle($value->title);
            $news->setDescription($value->description);
            $date = new \DateTime($value->publishedAt);
            $news->setCreatedAt($date);
            $news->setPicture($value->urlToImage);
            $em->persist($news);
           
            $em->flush();
             }

        //$io = new SymfonyStyle($input, $output);
        //$arg1 = $input->getArgument('arg1');

        // if ($arg1) {
        //     $io->note(sprintf('You passed an argument: %s', $arg1));
        // }

        // if ($input->getOption('option1')) {
        //     // ...
        // }

        // $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}
