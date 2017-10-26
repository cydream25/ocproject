<?php

namespace CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('CoreBundle:Default:index.html.twig');
    }

    public function contactAction(Request $request)
    {
        //$this->addFlash('notice','test raccourci');
        $request->getSession()->getFlashBag()->add('notice',"Message Flash : la page de contact n'est pas encore disponible. Merci de revenir plus tard");
      return $this->redirectToRoute('core_homepage');
    }
}
