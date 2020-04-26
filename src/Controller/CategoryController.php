<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CategoryController extends AbstractController
{
    /**
     * @Route("/category", name="category")
     */
    public function index()
    {

        $categories = $this->getDoctrine()
        ->getRepository(Category::class)
        ->getAll();

        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }


}
