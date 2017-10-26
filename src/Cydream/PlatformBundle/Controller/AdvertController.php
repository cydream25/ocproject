<?php

namespace Cydream\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdvertController extends Controller
{
  public function indexAction($page)
  {
    if ($page<1) {
      throw new NotFoundHttpException("Page '$page' inexistante.");
    }
    return $this->render('CydreamPlatformBundle:Advert:index.html.twig');
  }

  public function viewAction($id)
  {
    return $this->render('CydreamPlatformBundle:Advert:view.html.twig',['id'=>$id]);
  }

  public function addAction(Request $request) {
    if ($request->isMethod('POST')) {
      $request->getSession()->getFlashBag()->add('notice','Annonce bien enregistrée.');
      return $this->redirectToRoute('oc_platform_view',['id'=>5]);
    }
    return $this->render('CydreamPlatformBundle:Advert:add.html.twig');
  }

  public function editAction($id, Request $request) {
    if ($request->isMethod('POST')) {
      $request->getSession()->getFlashBag()->add('notice','Annonce bien modifiée');
      return $this->redirectToRoute('oc_platform_view',['id'=>5]);
    }    
    return $this->render('CydreamPlatformBundle:Advert:edit.html.twig'); //Pas de parametre pour peupler le formulaire d'edition ???
  }

  public function deleteAction($id) {
    return $this->render('CydreamPlatformBundle:Advert:delete.html.twig');
  }
}