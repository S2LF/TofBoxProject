<?php

namespace App\Controller;

use App\Entity\Photo;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        $photos = $this->getDoctrine()
        ->getRepository(Photo::class)
        ->getAll();

return $this->render('home/index.html.twig', [
'photos' => $photos,
]);

        // return $this->render('home/index.html.twig', [
        //     'controller_name' => 'HomeController',
        // ]);
    }

    // TODO ?
    public function isConnected()
    {}
}
