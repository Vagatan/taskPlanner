<?php

namespace TaskPlannerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $loggedUser = $this->getUser();
        $tasks = $em->getRepository('TaskPlannerBundle:Task')->findByUser($loggedUser);
        $tasksUnfinished = $em->getRepository('TaskPlannerBundle:Task')->findByDone(false);
        $tasksUnf = 0;    //zmienna pomocnicza - licznik niezakończonych tasków
        foreach($tasksUnfinished as $tc){
            if($tc->getUser() == $loggedUser){
                $tasksUnf++;
            }
        }

        return $this->render('TaskPlannerBundle:Default:index.html.twig', array('tasksUnf'=>$tasksUnf));
    }
}
