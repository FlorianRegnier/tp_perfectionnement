<?php

namespace App\Controller\Admin;

use App\Entity\Media;
use App\Form\MediaType;
use App\Repository\MediaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class AdminMediaController extends AbstractController
{

    /**
     * @Route("/admin/medias/", name="admin_list_media")
     */
    public function listMedia(MediaRepository $mediaRepository)
    {
        $medias = $mediaRepository->findAll(); 
        return $this->render('admin/medias.html.twig', ['medias' => $medias]);
    }



    /**
     * @Route("admin/media/{id}", name="admin_show_media")
     */
    public function showMedia($id, MediaRepository $mediaRepository)
    {
        $media = $mediaRepository->find($id); 
        
        return $this->render('admin/media.html.twig', ['media' => $media]);
    }



 



   
    /**
     * @Route("admin/update/media/{id}", name="admin_update_media")
     */                                       
    public function updateMedia($id, MediaRepository $mediaRepository, EntityManagerInterface $entityManagerInterface, Request $request )
    {
       $media = $mediaRepository->find($id);

       
       $mediaForm = $this->createForm(MediaType::class, $media); // a changer

       // Utilisation de handleRequest pour demander au formulaire de traiter les infos
       // rentrées dans le formulaire
       // Utilisation de request pour récupérer les informations rentrées dans le fromulaire
       $mediaForm->handleRequest($request);

       if($mediaForm->isSubmitted() && $mediaForm->isValid()){
           $entityManagerInterface->persist($media);
           $entityManagerInterface->flush();

           return $this->redirectToRoute('admin_list_media');
       }

       // redirige vers la page où le formulaire est affiché.
       return $this->render('admin/updatemedia.html.twig', ['mediaForm' => $mediaForm->createView()]);
    }





    /**
     * @Route("admin/delete/media/{id}", name="admin_delete_media")
     */
   public function deleteMedia($id, MediaRepository $mediaRepository, EntityManagerInterface $entityManagerInterface)
   {
       $media = $mediaRepository->find($id);
       $entityManagerInterface->remove($media); // fonction remove supprime le product sélectionné
       $entityManagerInterface->flush();

       $this->addFlash(
        'notice',
        'Votre image a été supprimé'
        );
    
       return $this->redirectToRoute("admin_list_media");
   }









        /*************************************************************************************** */
        /* use createmedia et pas addmedia qui gere pas le src mais je la garde pour information */ 



    /**
     * @Route("admin/add/media/", name="admin_add_media")
     */
    public function addMedia(EntityManagerInterface $entityManagerInterface, Request $request)
    {
        $media = new Media();      
 
        // Création du formulaire
        $mediaForm = $this->createForm(MediaType::class, $media); 
 
        // Utilisation de handleRequest pour demander au formulaire de traiter les infos
        // rentrées dans le formulaire
        // Utilisation de request pour récupérer les informations rentrées dans le fromulaire
        $mediaForm->handleRequest($request);
 
        if($mediaForm->isSubmitted() && $mediaForm->isValid())
        {
            $entityManagerInterface->persist($media);    // pré-enregistre dans la base de données
            $entityManagerInterface->flush();           // Enregistre dans la pase de données.
 
            return $this->redirectToRoute('admin_list_media');
        }
 
        // redirige vers la page où le formulaire est affiché.
        return $this->render('admin/updatemedia.html.twig', ['mediaForm' => $mediaForm->createView()]);
    }
 



    /** 
     * @Route("admin/create/media", name="admin_create_media")
     */
    public function createmedia(Request $request, EntityManagerInterface $entityManagerInterface, SluggerInterface $sluggerInterface)
    {

        $media = new Media();

        $mediaForm = $this->createForm(MediaType::class, $media);

        $mediaForm->handleRequest($request);

        if($mediaForm->isSubmitted() && $mediaForm->isValid())
        {
            $mediaFile = $mediaForm->get('src')->getData();

            if($mediaFile)
            {
                // on cree un nom unique avec le nom original de l image pour eviter tout pb
                $originalFilename = pathinfo($mediaFile->getClientOriginalName(), PATHINFO_FILENAME);
                // on utilise slugg sur le nom original d elimage pour avoir un nom valide
                $safeFileName = $sluggerInterface->slug($originalFilename);
                // on ajoute un id unique au nom de limage
                $newFilename = $safeFileName . '-'  . uniqid() . '.' . $mediaFile->guessExtension();
                
                // on deplace le fichier dans le dossier public/media
                //la destination du fichier est enregistre dans image_directory
                //qui est defini dans le fichier  config\services.yaml
                $mediaFile->move($this->getParameter('images_directory'), $newFilename);

                $media->setSrc($newFilename);
            }

            $media->setAlt($mediaForm->get('title')->getData());

            $entityManagerInterface->persist($media);
            $entityManagerInterface->flush();

            return $this->redirectToRoute("admin_list_media");
        }

        return $this->render('admin/updatemedia.html.twig', ['mediaForm' => $mediaForm->createView()]);
    }
    
}