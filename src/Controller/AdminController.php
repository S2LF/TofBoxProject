<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Photo;
use App\Entity\Comment;
use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



    /**
     * @Route("/admin")
     * @IsGranted("ROLE_ADMIN")
     */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="admin")
     */
    public function index()
    {
        $categories = $this->getDoctrine()
        ->getRepository(Category::class)
        ->getAll();

        $comments = $this->getDoctrine()
        ->getRepository(Comment::class)
        ->getAll();

        $photos = $this->getDoctrine()
        ->getRepository(Photo::class)
        ->getAll();

        $users = $this->getDoctrine()
        ->getRepository(User::class)
        ->getAll();

        return $this->render('admin/index.html.twig', [
            'categories' => $categories,
            'comments' => $comments,
            'photos'=> $photos,
            'users'=> $users
        ]);
    }

    /**
     * @Route("/users", name="admin_users")
     */
    public function adminUsers()
    {
        $users = $this->getDoctrine()
        ->getRepository(User::class)
        ->getAll();

        return $this->render('admin/users.html.twig', [
            'users' => $users
        ]);
    }

    /** 
     * @Route("/categories", name="admin_categories")
    */
    public function adminCategories(Request $request, EntityManagerInterface $em)
    {
        $categories = $this->getDoctrine()
        ->getRepository(Category::class)
        ->getAll();

        $cat = new Category();

        $form = $this->createForm(CategoryType::class, $cat);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            if($this->getUser() == $this->isGranted('ROLE_ADMIN')){
                try{
                    $em->persist($cat);
                    $em->flush();
                    $this->addFlash('success', 'La catégorie a bien été créée');

                } catch( UniqueConstraintViolationException $e){
                        $this->addFlash("error", "La catégorie existe déjà !");
                    }
                return $this->redirectToRoute('admin_categories');
            }
            
        }

        return $this->render('admin/cat.html.twig', [
            'categories' => $categories,
            "form" => $form->createView()
        ]);
    }
    
    /**
     * @Route("/ajax/cat_edit", name="ajax_cat_edit")
     */
    public function ajaxEditCat(Request $request, EntityManagerInterface $em)
    {
        $catid = $request->query->get("catid");
        $cat = $em->getRepository(Category::class)->findOneBy(['id' => $catid]);

        $form = $this->createForm(CategoryType::class, $cat);


        $html = $this->renderView("admin/ajaxCatEdit.html.twig", [
            "cat" => $cat,
            "form" => $form->createView()
        ]);

        return new Response($html);
    }
    /**
     * @Route("/cat_edit", name="cat_edit")
     */
    public function editCat(Request $request, EntityManagerInterface $em)
    {
        $catEdit = $request->query->get("catedit");
        $catid = $request->query->get('catid');
        $cat = $em->getRepository(Category::class)->findOneBy(['id' => $catid]);

        $cat->setIntitule($catEdit);

        $em->flush();

        $this->addFlash('success', 'La catégorie a bien été renommée');
        return $this->redirectToRoute('admin_categories');

    }



    /**
     * @Route("/cat/delete/{id}", name="cat_delete")
     */
    public function deleteCat(Request $request, EntityManagerInterface $em, CategoryRepository $crepo)
    {
        $crepo->deleteOne($request->attributes->get("id"));

        $em->flush();
        
        $this->addFlash('success', 'La catégorie a bien été supprimée');
        return $this->redirectToRoute('admin_categories');
    }
}
