<?php

namespace TaskPlannerBundle\Controller;

use TaskPlannerBundle\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * EmailService controller.
 *
 * @Route("serv")
 */
class EmailServiceController extends Controller
{


    /**
     * Checking unfinished tasks and sending e-mail to users
     *
     * @Route("/sendemails", name="task_check")
     * @Method("GET")
     */
    public function sendEmailAction(){                          //skończyć: poprawić to spagetti, twig, metoda konsolowa(?)
        $em = $this->getDoctrine()->getManager();
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

                    $this->get('mailer')->send($message);
                }
            }
        }

        return new Response(
            '<html><body>e-mail wysłane..</body></html>'
        );

    }

}
