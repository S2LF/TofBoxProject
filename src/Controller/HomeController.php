<?php

namespace App\Controller;

use App\Entity\Photo;
use App\Entity\Category;
use App\Repository\PhotoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

    /**
     * @Route("/")
     */
class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {

        // Get all categories
        $categories = $this->getDoctrine()
        ->getRepository(Category::class)
        ->getAll();

        // Get all photos
        $photos = $this->getDoctrine()
        ->getRepository(Photo::class)
        ->getAll();

        return $this->render('home/index.html.twig', [
            'photos' => $photos,
            'categories' => $categories,
        ]);
    }
    /**
     * @Route("/cat/{id}", name="home_cat")
     */
    public function getPhotosByCategory(Category $category, Request $request, EntityManagerInterface $em){

        $catId = $request->attributes->get("id");
        $category = $em->getRepository(Category::class)->findOneBy(['id' => $catId ]);

        
        // Get all categories
        $categories = $this->getDoctrine()
        ->getRepository(Category::class)
        ->getAll();
        
        return $this->render('home/index.html.twig', [
            'photos' => $category,
            'categories' => $categories,
            // 'category' => $category,
        ]);
    }



}
