<?php

namespace TaskPlannerBundle\Controller;

use TaskPlannerBundle\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;
use TaskPlannerBundle\Entity\Task;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


/**
 * Comment controller.
 *
 * @Route("comment")
 */
class CommentController extends Controller
{
    /**
     * Lists all comment entities.
     *
     * @Route("/", name="comment_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $loggedUser = $this->getUser();                     //zalogowany użytkownik
        $tasks = $em->getRepository('TaskPlannerBundle:Task')->findByUser($loggedUser);     //taski użytkownika

        $comments = $em->getRepository('TaskPlannerBundle:Comment')->findByTask($tasks);    //wszystkie komentarze dla danego tasków użytkownika

        return $this->render('comment/index.html.twig', array(
            'comments' => $comments,
        ));
    }

    /**
     * Creates a new comment entity.
     *
     * @Route("/new", name="comment_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $comment = new Comment();
        $loggedUser = $this->getUser();
        $tasks = $em->getRepository('TaskPlannerBundle:Task')->findByUser($loggedUser);     //taski użytkownika

        $form = $this->createFormBuilder($comment)
            ->add('comment','text')
            ->add('task', 'entity', array(                      //wszystkie taski stworzone przez użytkownika jako lista rozwijana
                    'class'=>'TaskPlannerBundle:Task',
                    'choices' => $tasks,
                ))

            ->getForm();

        $form->handleRequest($request);

        //dodanie i walidacja nowego komentarza do tasku
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush($comment);

            return $this->redirectToRoute('comment_show', array('id' => $comment->getId()));
        }

        return $this->render('comment/new.html.twig', array(
            'comment' => $comment,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a comment entity.
     *
     * @Route("/{id}", name="comment_show")
     * @Method("GET")
     */
    public function showAction(Comment $comment)            //poszczególny komentarz - bez moich zmian
    {
        $deleteForm = $this->createDeleteForm($comment);

        return $this->render('comment/show.html.twig', array(
            'comment' => $comment,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing comment entity.
     *
     * @Route("/{id}/edit", name="comment_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Comment $comment)      //edycja komentarza
    {

        $em = $this->getDoctrine()->getManager();


        $loggedUser = $this->getUser();
        $tasks = $em->getRepository('TaskPlannerBundle:Task')->findByUser($loggedUser); //taski zalogowanego użytkownika

        $editForm = $this->createFormBuilder($comment)
            ->add('comment','text')
            ->add('task', 'entity', array(                  //wszystkie taski stworzone przez użytkownika jako lista rozwijana
                'class'=>'TaskPlannerBundle:Task',
                'choices' => $tasks,
            ))

            ->getForm();

        $deleteForm = $this->createDeleteForm($comment);
        //$editForm = $this->createForm('TaskPlannerBundle\Form\CommentType', $comment);
        $editForm->handleRequest($request);

        //walidacja i dodanie zmian do bazy
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('comment_edit', array('id' => $comment->getId()));
        }

        return $this->render('comment/edit.html.twig', array(
            'comment' => $comment,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a comment entity.
     *
     * @Route("/{id}", name="comment_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Comment $comment)        //usunięcie - bez moich zmian
    {
        $form = $this->createDeleteForm($comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($comment);
            $em->flush($comment);
        }

        return $this->redirectToRoute('comment_index');
    }

    /**
     * Creates a form to delete a comment entity.
     *
     * @param Comment $comment The comment entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Comment $comment)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('comment_delete', array('id' => $comment->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }


}
