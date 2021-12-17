<?php

namespace App\Controller\Front;

use App\Entity\Like;
use App\Repository\CategoryRepository;
use App\Repository\LikeRepository;
use App\Repository\MediaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class MediaController extends AbstractController
{



  
    public function listMedia(MediaRepository $mediaRepository)
    {
        $medias = $mediaRepository->findAll();

        return $this->render('front/medias.html.twig', ['medias' => $medias]);
    }


    
  
    public function showMedia($id, MediaRepository $mediaRepository)
    {
        $media = $mediaRepository->find($id);

        return $this->render('front/media.html.twig', ['media' => $media]);
    }



   
    public function frontSearch(MediaRepository $mediaRepository, Request $request)
    {
        //recuperer els donnes du tableau
        $term = $request->query->get('term');// query car le form est en get. si form en post alors use request au lieu de query

        $medias = $mediaRepository->searchByTerm($term);
        
        
        return $this->render('front/search.html.twig', ['medias' => $medias]);
    }





   
    public function likeMedia($id, MediaRepository $mediaRepository, EntityManagerInterface $entityManagerInterface, LikeRepository $likeRepository)
    {
        $media = $mediaRepository->find($id);
        $user = $this->getUser();

        if(!$user)
        {
            return $this->json([
                'code' => 403,
                'message' => "Vous devez être connecté"
            ], 403);
        }

        if($media->isLikedByUser($user))
        {
            $like = $likeRepository->findOneBy([
                'media' => $media,
                'user' => $user
            ]);
            $entityManagerInterface->remove($like);
            $entityManagerInterface->flush();

            return $this->json([
                'code' => 200,
                'message' => "Le like a été supprimé",
                'likes' => $likeRepository->count(['media' => $media])
            ], 200);
        }

        $like = new Like();
        $like->setMedia($media);
        $like->setUser($user);

        $entityManagerInterface->persist($like);
        $entityManagerInterface->flush();

        return $this->json([
            'code' => 200,
            'message' => "Le like a été enregistré",
            'likes' => $likeRepository->count(['media' => $media])
        ], 200);

    }




}