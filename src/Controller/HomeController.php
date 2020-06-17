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
     * 
     * Get photos with pagination
     * Get categories for slide
     * Display home page
     */
    public function index(PaginatorInterface $paginator, Request $request)
    {

        // Get all categories
        $categories = $this->getDoctrine()
        ->getRepository(Category::class)
        ->getAll();




        $lastsPhotos = $this->getDoctrine()->getRepository(Photo::class)->findLasts(4);
        $lastsPhotosByPop = $this->getDoctrine()->getRepository(Photo::class)->findLastsByPop(4);
        // if($this->getUser()){

            $followers = $this->getUser()->getFollowByUsers();;


            $photos = [];

            foreach($followers as $follower){
                $photos = array_merge($photos, $follower->getFollowedUsers()->getPhotos()->toArray());
            }

        return $this->render('home/index.html.twig', [
            'lastsPhotos' => $lastsPhotos,
            'lastsByPop' => $lastsPhotosByPop,
            'Followphoto' => $photos,
            // 'lastsByFollow' => $followers,
            'categories' => $categories,
        ]);
    }


    /**
     * @Route("/lasts", name="home_lasts")
     * 
     * Get photos with pagination
     * Get categories for slide
     * Display home page
     */
    public function getPhotosByLasts(PaginatorInterface $paginator, Request $request)
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

        return $this->render('home/lasts.html.twig', [
            'photos' => $photos,
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/home_cat/{cat_id}", name="home_cat")
     * 
     * Get current category
     * Get photos from this category with pagination
     * Get categories for slide
     */
    public function getPhotosByCategory(Request $request, PaginatorInterface $paginator, EntityManagerInterface $em){

        $catId = $request->attributes->get("cat_id");

         $category = $em->getRepository(Category::class)->findOneBy(['id' => $catId ]);

            // Get all categories
             $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->getAll();
            

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

    /**
     * @Route("/home_pop", name="home_pop")
     */
    public function getPhotosByPopular(Request $request, PaginatorInterface $paginator){
    
        // Get all categories
            $categories = $this->getDoctrine()
        ->getRepository(Category::class)
        ->getAll();

        // Get all categories
        $categories = $this->getDoctrine()
        ->getRepository(Category::class)
        ->getAll();

        $photos = $paginator->paginate(
            $this->getDoctrine()->getRepository(Photo::class)->getPopularPages(),
            $request->query->getInt('page', 1),
            12
        );

        return $this->render('home/pop.html.twig', [
            'photos' => $photos,
            'categories' => $categories,
        ]);
    }

    public function getPhotosByFollowed(){}
}
