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
     * Defaut path
     * 
     * Don't use in my project
     */
    public function index()
    {
        return $this->redirectToRoute('home');
    }


    /**
     * @Route("/suggest", name="suggest_cat")
     * @IsGranted("ROLE_USER")
     * 
     * Render view for suggest category
     */
    public function suggest_cat()
    {

        return $this->render('category/suggest.html.twig');

    }

    /**
     * @Route("/ajax/suggest_return", name="ajax_form_suggest")
     * @IsGranted("ROLE_USER")
     * 
     * Get suggests, create file if needed and add suggests at the end of this file
     */
    public function form_suggest_cat(Request $request)
    {

        $suggests = $request->request->get("suggests");

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

            }
        }

            $this->addFlash("success", "Vos suggestions ont bien été enregistré");

        return $this->redirectToRoute('profil', ['id' => $this->getUser()->getId()]);

    }

}
