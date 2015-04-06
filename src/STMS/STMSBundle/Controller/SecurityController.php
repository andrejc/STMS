<?php

namespace STMS\STMSBundle\Controller;

use STMS\STMSBundle\Entity\User;
use STMS\STMSBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

class SecurityController extends Controller
{
    public function loginAction(Request $request) {

        /**
         * Redirect authenticated users to the homepage
         */
        $securityContext = $this->container->get('security.context');
        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return new RedirectResponse($this->generateUrl('index'));
        }

        /**
         * Display login and sign-up forms
         */
        return $this->render('STMSBundle:Security:login.html.twig');
    }

    public function registerAction(Request $request) {
        $user = new User();

        $form = $this->createForm(new UserType(), $user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
            $em = $this->getDoctrine()->getManager();

            $user->setPassword($this->encodePassword($user, $user->getPassword()));

            $em->persist($user);
            $em->flush();

            return new JsonResponse(array("result" => "success"));
        }

        $errorMessages = array();

        foreach($form->getErrors(true) as $error) {
            $errorMessages[] = $error->getMessage();
        }

        return new JsonResponse(array("result" => "error", "messages" => $errorMessages));
    }

    public function userDataAction() {
        $user = $this->get('security.context')->getToken()->getUser();

        $normalizer = new GetSetMethodNormalizer();
        $normalizer->setIgnoredAttributes(array('password', 'roles', 'salt'));

        $serializer = new Serializer(array($normalizer), array(new JsonEncoder()));

        $json = $serializer->serialize($user, 'json');

        return new Response($json);
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
