<?php

namespace STMS\STMSBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('STMSBundle:Default:index.html.twig');
    }
}
