<?php

namespace Cydream\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ByeController extends Controller
{
  public function indexAction()
  {
    $content = $this->get('templating')->render('CydreamPlatformBundle:Bye:index.html.twig');
    
    return new Response($content);
  }
}