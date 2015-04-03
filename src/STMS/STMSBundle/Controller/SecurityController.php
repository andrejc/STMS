<?php

namespace STMS\STMSBundle\Controller;

use STMS\STMSBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;

class SecurityController extends Controller
{
    public function loginAction(Request $request) {
        $session = $request->getSession();

        $error = $session->get(SecurityContextInterface::AUTHENTICATION_ERROR);
        $session->remove(SecurityContextInterface::AUTHENTICATION_ERROR);

        return $this->render(
            'STMSBundle:Security:login.html.twig',
            array(
                'last_username' => $session->get(SecurityContextInterface::LAST_USERNAME),
                'error'         => $error,
            )
        );
    }

    public function registerAction(Request $request) {
        $form = $this->createFormBuilder(null, array('csrf_protection' => false))
            ->add('email', 'text')
            ->add('fullname', 'text')
            ->add('password', 'password')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
            $em = $this->getDoctrine()->getManager();

            $user = new User();
            $user->setEmail($data['email']);
            $user->setFullname($data['fullname']);
            $user->setPassword($this->encodePassword($user, $data['password']));

            $em->persist($user);
            $em->flush();

            return new JsonResponse(array("result" => "success"));
        }

        return new JsonResponse(array(
            "result" => "error",
            "message" => "One or more input values are invalid or missing"));
    }

    private function encodePassword(User $user, $password) {
        $encoder = $this->container->get('security.encoder_factory')
            ->getEncoder($user)
        ;

        return $encoder->encodePassword($password, $user->getSalt());
    }

    public function loginCheckAction(Request $request) {
    }

    public function logoutAction() {
    }
}
