<?php

namespace STMS\STMSBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class SettingsController extends Controller {

    /**
     * Set preferred number of daily hours for the current user
     */
    public function setPreferredHoursAction($hours) {
        $user = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        $user->setPreferredWorkingHoursPerDay($hours);
        $em->flush();

        return new JsonResponse(array("result" => "success"));
    }
}