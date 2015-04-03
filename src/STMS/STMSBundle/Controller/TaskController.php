<?php

namespace STMS\STMSBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use STMS\STMSBundle\Entity\Task;
use STMS\STMSBundle\Form\TaskType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Task controller.
 *
 */
class TaskController extends Controller
{

    /**
     * Lists all Task entities.
     *
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $tasks = $em->getRepository('STMSBundle:Task')->findAll();

        $normalizer = new GetSetMethodNormalizer();

        $normalizer->setCallbacks(array('date' => function ($dateTime) {
            return $dateTime->format("Y-m-d");
        }));

        $serializer = new Serializer(array($normalizer), array(new JsonEncoder()));

        $json = $serializer->serialize($tasks, 'json');

        return new Response($json);
    }

    /**
     * Finds and displays a Task entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $task = $em->getRepository('STMSBundle:Task')->find($id);

        if (!$task) {
            return new JsonResponse(array(
                "result" => "error",
                "messages" => array("No task found for the given ID")));
        }

        $normalizer = new GetSetMethodNormalizer();

        $normalizer->setCallbacks(array('date' => function ($dateTime) {
            return $dateTime->format("Y-m-d");
        }));

        $serializer = new Serializer(array($normalizer), array(new JsonEncoder()));
        $json = $serializer->serialize($task, 'json');

        return new Response($json);
    }

    /**
     * Creates a new Task entity.
     *
     */
    public function addAction(Request $request)
    {
        $task = new Task();
        $form = $this->createAddForm($task);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $user = $this->get('security.context')->getToken()->getUser();
            $task->setUser($user);

            $em->persist($task);
            $em->flush();

            return new JsonResponse(array("result" => "success"));
        }

        $errorMessages = array();

        foreach($form->getErrors(true) as $error) {
            $errorMessages[] = $error->getMessage();
        }

        return new JsonResponse(array(
            "result" => "error",
            "messages" => $errorMessages)
        );
    }


    /**
     * Edits an existing Task entity.
     *
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $task = $em->getRepository('STMSBundle:Task')->find($id);

        if (!$task) {
            return new JsonResponse(array(
                "result" => "error",
                "message" => "No task found for the given ID"));
        }

        $form = $this->createEditForm($task);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->flush();

            return new JsonResponse(array("result" => "success"));
        }

        $errorMessages = array();

        foreach($form->getErrors(true) as $error) {
            $errorMessages[] = $error->getMessage();
        }

        return new JsonResponse(array(
                "result" => "error",
                "messages" => $errorMessages)
        );
    }

    /**
     * Deletes a Task entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $task = $em->getRepository('STMSBundle:Task')->find($id);

        if (!$task) {
            return new JsonResponse(array(
                "result" => "error",
                "message" => "No task found for the given ID"));
        }

        $em->remove($task);
        $em->flush();

        return new JsonResponse(array("result" => "success"));
    }

    /**
     * Creates a form to create a Task entity.
     *
     * @param Task $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createAddForm(Task $entity)
    {
        $form = $this->createForm(new TaskType(), $entity, array(
            'action' => $this->generateUrl('task_add'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Creates a form to edit a Task entity.
     *
     * @param Task $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Task $entity)
    {
        $form = $this->createForm(new TaskType(), $entity, array(
            'action' => $this->generateUrl('task_edit', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }
}
