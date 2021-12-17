<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminCategoryController extends AbstractController
{

    /**
     * @Route("/admin/categories/", name="admin_list_category")
     */
    public function listCategory(CategoryRepository $categoryRepository)
    {
        $categories = $categoryRepository->findAll(); 
        return $this->render('admin/categories.html.twig', ['categories' => $categories]);
    }



    /**
     * @Route("admin/category/{id}", name="admin_show_category")
     */
    public function showCategory($id, CategoryRepository $categoryRepository)
    {
        $category = $categoryRepository->find($id); 
        
        return $this->render('admin/category.html.twig', ['category' => $category]);
    }



    /**
     * @Route("admin/add/category/", name="admin_add_category")
     */
   public function addCategory(EntityManagerInterface $entityManagerInterface, Request $request)
   {
       $category = new Category();      

       // Création du formulaire
       $categoryForm = $this->createForm(CategoryType::class, $category); 

       // Utilisation de handleRequest pour demander au formulaire de traiter les infos
       // rentrées dans le formulaire
       // Utilisation de request pour récupérer les informations rentrées dans le fromulaire
       $categoryForm->handleRequest($request);

       if($categoryForm->isSubmitted() && $categoryForm->isValid())
       {
           $entityManagerInterface->persist($category);    // pré-enregistre dans la base de données
           $entityManagerInterface->flush();           // Enregistre dans la pase de données.media category

           return $this->redirectToRoute('admin_list_category');
       }

       // redirige vers la page où le formulaire est affiché.
       return $this->render('admin/updatecategory.html.twig', ['categoryForm' => $categoryForm->createView()]);
   }




   
    /**
     * @Route("admin/update/category/{id}", name="admin_update_category")
     */                                       
    public function updateCategory($id, CategoryRepository $categoryRepository, EntityManagerInterface $entityManagerInterface, Request $request )
    {
       $category = $categoryRepository->find($id);

       
       $categoryForm = $this->createForm(CategoryType::class, $category); // a changer

       // Utilisation de handleRequest pour demander au formulaire de traiter les infos
       // rentrées dans le formulaire
       // Utilisation de request pour récupérer les informations rentrées dans le fromulaire
       $categoryForm->handleRequest($request);

       if($categoryForm->isSubmitted() && $categoryForm->isValid()){
           $entityManagerInterface->persist($category);
           $entityManagerInterface->flush();

           return $this->redirectToRoute('admin_list_category');
       }

       // redirige vers la page où le formulaire est affiché.
       return $this->render('admin/updatecategory.html.twig', ['categoryForm' => $categoryForm->createView()]);
    }



    /**
     * @Route("admin/delete/category/{id}", name="admin_delete_category")
     */
   public function deleteCategory($id, CategoryRepository $categoryRepository, EntityManagerInterface $entityManagerInterface)
   {
       $category = $categoryRepository->find($id);
       $entityManagerInterface->remove($category); // fonction remove supprime le product sélectionné
       $entityManagerInterface->flush();

       $this->addFlash(
        'notice',
        'Votre categorie a été supprimé'
        );
    
       return $this->redirectToRoute("admin_list_category");
   }


    
}