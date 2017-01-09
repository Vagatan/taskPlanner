<?php

namespace TaskPlannerBundle\Controller;

use TaskPlannerBundle\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Task controller.
 *
 * @Route("task")
 */
class TaskController extends Controller
{

    /**
     * Lists all task entities.
     *
     * @Route("/", name="task_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $loggedUser = $this->getUser();
        $tasks = $em->getRepository('TaskPlannerBundle:Task')->findByUser($loggedUser); //metoda 'dynamiczna' przyjmuje taski na zalogowanego usera
        //dump($loggedUser.$this->getUser());
        /*
         * przerobić to na 'styl tablicowy' będzie bardziej elegancko
            (prezentacja 12.04 str 39)
         */
        $tasksUnfinished = $em->getRepository('TaskPlannerBundle:Task')->findByDone(false); //metoda 'dynamiczna' pobierająca obiekty z niezakończonym taskiem
        $tasksUnf = 0;    //zmienna pomocnicza - licznik niezakończonych tasków
        foreach($tasksUnfinished as $tc){
            if($tc->getUser() == $loggedUser){
                $tasksUnf++;
            }
        }

        return $this->render('task/index.html.twig', array(
            'tasks' => $tasks, 'user'=>$loggedUser, 'tasksUnfinished'=>$tasksUnf//, 'comment' => $comments
        ));
    }

    /**
     * Creates a new task entity.
     *
     * @Route("/new", name="task_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $task = new Task();

        $em = $this->getDoctrine()->getManager();
        $loggedUser = $this->getUser();
        $tasks = $em->getRepository('TaskPlannerBundle:Category')->findByUser($loggedUser);


        $form = $this->createFormBuilder($task)
            ->add('name','text')
            ->add('description','text')
            ->add('done')
            ->add('dueDate')
            ->add('category', 'entity', array(
                'class'=>'TaskPlannerBundle:Category',
                'choices' => $tasks,
            ))

            ->getForm();

        $form->handleRequest($request);


/*
        $form = $this->createForm('TaskPlannerBundle\Form\TaskType', $task);
        $form->handleRequest($request);
*/

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $task->setUser($this->getUser());
            $em->persist($task);
            $em->flush($task);

            return $this->redirectToRoute('task_show', array('id' => $task->getId()));
        }

        return $this->render('task/new.html.twig', array(
            'task' => $task,
            'form' => $form->createView(),
        ));

    }

    /**
     * Finds and displays a task entity.
     *
     * @Route("/{id}", name="task_show")
     * @Method("GET")
     */
    public function showAction(Task $task)
    {
        $deleteForm = $this->createDeleteForm($task);

        return $this->render('task/show.html.twig', array(
            'task' => $task,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing task entity.
     *
     * @Route("/{id}/edit", name="task_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Task $task)
    {
        $em = $this->getDoctrine()->getManager();
        $loggedUser = $this->getUser();
        $tasks = $em->getRepository('TaskPlannerBundle:Category')->findByUser($loggedUser);

        $deleteForm = $this->createDeleteForm($task);

        $editForm = $this->createFormBuilder($task)
            ->add('name','text')
            ->add('description','text')
            ->add('done')
            ->add('dueDate')
            ->add('category', 'entity', array(
                'class'=>'TaskPlannerBundle:Category',
                'choices' => $tasks,
            ))

            ->getForm();

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('task_edit', array('id' => $task->getId()));
        }

        return $this->render('task/edit.html.twig', array(
            'task' => $task,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a task entity.
     *
     * @Route("/{id}", name="task_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Task $task)
    {
        $form = $this->createDeleteForm($task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($task);
            $em->flush($task);
        }

        return $this->redirectToRoute('task_index');
    }

    /**
     * Creates a form to delete a task entity.
     *
     * @param Task $task The task entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Task $task)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('task_delete', array('id' => $task->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
