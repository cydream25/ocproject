<?php

namespace Cydream\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('CydreamPlatformBundle:Default:index.html.twig');
    }
}
