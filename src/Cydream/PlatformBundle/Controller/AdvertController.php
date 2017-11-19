<?php

namespace Cydream\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;

use Cydream\PlatformBundle\Entity\Advert;
use Cydream\PlatformBundle\Entity\Image;
use Cydream\PlatformBundle\Entity\Application;
use Cydream\PlatformBundle\Form\AdvertType;
use Cydream\PlatformBundle\Form\AdvertEditType;


class AdvertController extends Controller
{
    public function indexAction($page)
    {
        $nbPerPage =    3;

        if ($page < 1) {
            throw new NotFoundHttpException("Page '$page' inexistante.");
        }
        $em = $this->getDoctrine()->getManager();
        $adverts= $em->getRepository('CydreamPlatformBundle:Advert')->getAdverts($page,$nbPerPage);

        // On calcule le nombre total de pages grâce au count($listAdverts) qui retourne le nombre total d'annonces
        $nbPages = ceil(count($adverts) / $nbPerPage);

        if ($page > $nbPages) {
            throw new NotFoundHttpException("Page '$page' inexistante.");
        }
        return $this->render('CydreamPlatformBundle:Advert:index.html.twig',[
            'listAdverts'=>$adverts,
            'page'=>$page,
            'nbPages'=>$nbPages,
        ]);
    }

    public function viewAction($id)
    {

        $em = $this->getDoctrine()
            ->getManager();

        // On récupère le repository
        $repository = $em->getRepository('CydreamPlatformBundle:Advert');

        // On récupère l'entité correspondante à l'id $id
        $advert = $repository->find($id);

        // $advert est donc une instance de Cydream\PlatformBundle\Entity\Advert
        // ou null si l'id $id  n'existe pas, d'où ce if :
        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
        }

        $applications = $em->getRepository('CydreamPlatformBundle:Application')->findBy(['advert'=>$advert]);

        $advertSkills = $em->getRepository('CydreamPlatformBundle:AdvertSkill')->findBy(['advert'=>$advert]);

        // Le render ne change pas, on passait avant un tableau, maintenant un objet
        return $this->render('CydreamPlatformBundle:Advert:view.html.twig', array(
            'advert' => $advert,
            "applications" => $applications,
            'listAdvertSkills' => $advertSkills,
        ));


    }

    public function addAction(Request $request)
    {
        // Création de l'entité
        $advert = new Advert();

        $form = $this->createForm(AdvertType::class, $advert);
        /*
        $formBuilder=$this->get('form.factory')->createBuilder(FormType::class,$advert);

        $formBuilder
            ->add('date',DateType::class)
            ->add('title',TextType::class)
            ->add('content',TextareaType::class)
            ->add('author',TextType::class)
            ->add('published',CheckboxType::class, array('required' => false))
            ->add('save',SubmitType::class);

         $form = $formBuilder->getForm();
        */
        // Si la requête est en POST
        if ($request->isMethod('POST')) {
            // On fait le lien Requête <-> Formulaire
            // À partir de maintenant, la variable $advert contient les valeurs entrées dans le formulaire par le visiteur
            $form->handleRequest($request);

            // On vérifie que les valeurs entrées sont correctes
            // (Nous verrons la validation des objets en détail dans le prochain chapitre)
            if ($form->isValid()) {

                // Ajoutez cette ligne :
                // c'est elle qui déplace l'image là où on veut les stocker
                $advert->getImage()->upload();
      
                // On enregistre notre objet $advert dans la base de données, par exemple
                $em = $this->getDoctrine()->getManager();
                $em->persist($advert);
                $em->flush();

                
                $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

                // On redirige vers la page de visualisation de l'annonce nouvellement créée
                return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
            }
        }

        // À ce stade, le formulaire n'est pas valide car :
        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - Soit la requête est de type POST, mais le formulaire contient des valeurs invalides, donc on l'affiche de nouveau
        return $this->render('CydreamPlatformBundle:Advert:add.html.twig', array(
        'form' => $form->createView(),
        ));
    }

    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        // On récupère l'annonce $id
        $advert = $em->getRepository('CydreamPlatformBundle:Advert')->find($id);
    
        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
        }
    
        $form = $this->createForm(AdvertEditType::class, $advert);        

    
        // Pour persister le changement dans la relation, il faut persister l'entité propriétaire
        // Ici, Advert est le propriétaire, donc inutile de la persister car on l'a récupérée depuis Doctrine
    
        // Étape 2 : On déclenche l'enregistrement
        $em->flush();

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée');
                return $this->redirectToRoute('oc_platform_view', ['id' => 5]);
            }
        }
        return $this->render('CydreamPlatformBundle:Advert:edit.html.twig',[
            'form'=>$form->createView(),
            'advert'=>$form->createView(),
        ]); 
    }

    public function deleteAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
            $advert = $em->getRepository('CydreamPlatformBundle:Advert')->find($id);
        
            if (null === $advert) {
              throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
            }
        
            // On crée un formulaire vide, qui ne contiendra que le champ CSRF
            // Cela permet de protéger la suppression d'annonce contre cette faille
            $form = $this->get('form.factory')->create();
        
            if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
              $em->remove($advert);
              $em->flush();
        
              $request->getSession()->getFlashBag()->add('info', "L'annonce a bien été supprimée.");
        
              return $this->redirectToRoute('oc_platform_home');
            }
            
            return $this->render('CydreamPlatformBundle:Advert:delete.html.twig', array(
              'advert' => $advert,
              'form'   => $form->createView(),
            ));    }
}