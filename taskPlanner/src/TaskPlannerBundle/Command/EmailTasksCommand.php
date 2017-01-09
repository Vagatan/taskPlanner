<?php
namespace TaskPlannerBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\HttpFoundation\Response;

class EmailTasksCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:create-message')
            // the short description shown while running "php bin/console list"
            ->setDescription('Sending emails..')
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp("This command sending emails about tasks to users...")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)  //skończyć: poprawić to spagetti,
    {


        $em =$this->getContainer()->get('doctrine')->getEntityManager();

        $users = $em->getRepository('TaskPlannerBundle:User')->findAll();

        foreach($users as $usa){
            $tl = $em->getRepository('TaskPlannerBundle:Task')->findByUser($usa);
            foreach($tl as $t){
                if(!$t->getDone()){
                    $message = \Swift_Message::newInstance()
                        ->setSubject('Witaj użytkowniku')
                        ->setFrom('mmatyska75@gmail.com')
                        ->setTo($t->getUser()->getEmail())
                        ->setBody('You have unfinished tasks');
                    ;

                    $this->getContainer()->get('mailer')->send($message);
                    $output->writeln('Wysłany email!');
                }
            }
        }
    }
}