<?php

namespace STMS\STMSBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class SettingsController extends Controller {

    /**
     * Set preferred number of daily hours for the current user
     *
     * @param $hours
     * @return JsonResponse
     */
    public function setPreferredHoursAction($hours) {
        $user = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        if($hours == "null") {
            $hours = null;
        }

        $user->setPreferredWorkingHoursPerDay($hours);
        $em->flush();

        return new JsonResponse(array("result" => "success"));
    }
}