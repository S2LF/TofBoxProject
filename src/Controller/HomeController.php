<?php

namespace App\Controller;

use App\Entity\Photo;
use App\Entity\Category;
use App\Repository\PhotoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * @Route("/ajax/home_cat", name="home_cat")
     */
    public function getPhotosByCategory(Request $request, EntityManagerInterface $em){

        $catId = $request->request->get("catId");
            // $catId = $request->attributes->get("id");

        if($catId == 'home'){

            // Get all photos
            $photos = $this->getDoctrine()
            ->getRepository(Photo::class)
            ->getAll();


            $html = $this->renderView('home/photoAll.html.twig', [
                'photos' => $photos,
                            ]);

            return new Response($html);

        } else {

            $category = $em->getRepository(Category::class)->findOneBy(['id' => $catId ]);

            // Get all categories
            $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->getAll();

            $html = $this->renderView('home/photocat.html.twig', [
                'photos' => $category,
                'categories' => $categories,
            ]);

            return new Response($html);
        }

        // $html = $this->renderView("home/ajaxLike.html.twig", [
        //     "isLiking" => $isLiking,
        //     "photo" => $photo
        // ]);

        

    }






}
