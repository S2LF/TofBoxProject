<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @Route("/category")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/", name="category")
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


    /**
     * @Route("/suggest", name="suggest_cat")
     * @IsGranted("ROLE_USER")
     */
    public function suggest_cat()
    {

        return $this->render('category/suggest.html.twig');

    }

    /**
     * @Route("/ajax/suggest_return", name="ajax_form_suggest")
     * @IsGranted("ROLE_USER")
     */
    public function form_suggest_cat(Request $request)
    {

        $suggests = $request->request->get("suggests");

        try {
        $file = new FileSystem();
        $path = $this->getParameter('private_directory').'cat_suggest/suggests.txt';

        if(!$file->exists($path)){

            $serializeSuggests = serialize($suggests);

            $file->dumpFile($path, $serializeSuggests);
        } else {

            $myFile = fopen($path, 'a+');


            // On récupère le fichier
            $f = fgets($myFile);

            // Si 1 array est déjà crée
            if(is_array(unserialize($f))){

                // On récupère l'array
                $u = unserialize($f);

                // On rajoute notre message crée plus haut
                $merge = array_merge($u, $suggests);

                // On serialize
                $s = serialize($merge);

                // On clos le fichier
                fclose($myFile);

                // On écrase l'array existant et on met le nouveau
                file_put_contents($path, $s);


            // $file->appendToFile($path, $suggests);
            }
        }

            $this->addFlash('success', "Vos suggestions ont bien été enregistré");

        } catch( FileException $e) {
            $this->addFlash("error", "Un problème est survenu lors de l'enregistrement des suggestions");
        }

        return $this->redirectToRoute('profil', ['id' => $this->getUser()->getId()]);

    }

}
