<?php

namespace App\Controller;

use App\Entity\Photo;
use App\Entity\Category;
use App\Repository\PhotoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
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
    public function index(PaginatorInterface $paginator, Request $request)
    {

        // Get all categories
        $categories = $this->getDoctrine()
        ->getRepository(Category::class)
        ->getAll();

        // Get all photos

        $photos = $paginator->paginate(
            $this->getDoctrine()->getRepository(Photo::class)->getAllPages(),
            $request->query->getInt('page', 1),
            12
        );
        // ->getRepository(Photo::class)
        // ->getAll();


        // $photos = $this->getDoctrine()
        // ->getRepository(Photo::class)
        // ->getAll();

        return $this->render('home/index.html.twig', [
            'photos' => $photos,
            'categories' => $categories,
        ]);
    }




    /**
     * @Route("/home_cat/{cat_id}", name="home_cat")
     */
    public function getPhotosByCategory(Request $request, PaginatorInterface $paginator, EntityManagerInterface $em){

        $catId = $request->attributes->get("cat_id");

         $category = $em->getRepository(Category::class)->findOneBy(['id' => $catId ]);

            // Get all categories
             $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->getAll();


            // Get all categories
            // $categories = $this->getDoctrine()
            // ->getRepository(Category::class)
            // ->getPhotosFromCat();
            // ->getAll();

            $photos = $paginator->paginate(
                $this->getDoctrine()->getRepository(Category::class)->getPhotosFromCat($catId),
                $request->query->getInt('page', 1),
                12
            );

            return $this->render('home/photocat.html.twig', [
                'photos' => $photos,
                'category' => $category,
                'categories' => $categories,
            ]);

        }
}
