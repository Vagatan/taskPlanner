<?php

namespace TaskPlannerBundle\Controller;

use TaskPlannerBundle\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\DateType;

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

        $tasksUnf = 0;                              //zmienna pomocnicza - licznik niezakończonych tasków,
        foreach($tasksUnfinished as $tc){           // niezbyt to ładne ale chcę jak najmniej robić w twigu (twig ma tylko prezentować to co dostaje)
            if($tc->getUser() == $loggedUser){
                $tasksUnf++;
            }
        }

        //do twigu wysyłane są: taski danego usera, zalogowany user, ilość niezakończonych tasków
        return $this->render('task/index.html.twig', array(
            'tasks' => $tasks, 'user'=>$loggedUser, 'tasksUnfinished'=>$tasksUnf
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
        $tasks = $em->getRepository('TaskPlannerBundle:Category')->findByUser($loggedUser);     //lista kategorii stworzonych przez danego usera

        $form = $this->createFormBuilder($task)
            ->add('name','text')
            ->add('description','text')
            ->add('done')
            ->add('dueDate','date',array(
                'years' => range(date('Y') , date('Y') + 5),        //todo
                'months' => range(date('m') , date('m') + 12),      //takie ustawienie wymusza ustawienia kalendarza od aktualnej daty w 'górę'
                'days' => range(date('d') , date('m') )             //to jeszcze do przerobienia bo na bank jest sensowniejszy sposób..
            ))
//            ->add('dueDate', DateType::class, [                    //todo datepicker, stąd ta modyfikacja tego pola
//                'widget' => 'single_text'
//            ])
            ->add('category', 'entity', array(                      //pole zmodyfikowane - wysyła tylko kategorie danego użytkownika
                'class'=>'TaskPlannerBundle:Category',
                'choices' => $tasks,
            ))

            ->getForm();

        $form->handleRequest($request);

        //zakomentowany 'standardowy' createForm - czasem mi się przydaje do sprawdzania, więc nie usuwam ale nie jest już potrzebny
/*
        $form = $this->createForm('TaskPlannerBundle\Form\TaskType', $task);
        $form->handleRequest($request);
*/
        //dodanie (po walidacji) nowego tasku i przekierowanie do twigu wyświetlającego nowy task
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
    public function showAction(Task $task)                      //wyswietlenie danego taska - tu nic nie zmieniałem
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
    public function editAction(Request $request, Task $task)       //edycja konkretnego taska wybranego przez zalogowanego użytkownika
    {

        $em = $this->getDoctrine()->getManager();
        $loggedUser = $this->getUser();
        $tasks = $em->getRepository('TaskPlannerBundle:Category')->findByUser($loggedUser); //taski użytkownika

        $deleteForm = $this->createDeleteForm($task);

        $editForm = $this->createFormBuilder($task)
            ->add('name','text')
            ->add('description','text')
            ->add('done')
            ->add('dueDate')
            ->add('category', 'entity', array(                      //kategorie danego użytkownika
                'class'=>'TaskPlannerBundle:Category',
                'choices' => $tasks,
            ))

            ->getForm();
        $editForm->handleRequest($request);

        //dodanie zaktualizowanych danych do bazy i przekierowanie
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('task_edit', array('id' => $task->getId()));
        }

        //do twigu leci konkretny task, z uwzględnieniem kategorii tylko stworzonych przez użytkownika i usuwanie danego tasku
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
    public function deleteAction(Request $request, Task $task)          //usuwanie tasków - nic nie zmieniałem
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
    private function createDeleteForm(Task $task)                       //usuwanie tasków formularz - nic nie zmieniałem
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('task_delete', array('id' => $task->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
