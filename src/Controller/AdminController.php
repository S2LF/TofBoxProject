<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Photo;
use App\Entity\Report;
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
use Symfony\Component\Filesystem\Filesystem;




    /**
     * @Route("/admin")
     * @IsGranted("ROLE_ADMIN")
     */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="admin")
     * Get all informations for display in admin panel
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

        $reports = $this->getDoctrine()
        ->getRepository(Report::class)
        ->getAll();

        $activeReports = $this->getDoctrine()
        ->getRepository(Report::class)
        ->getReportsActive();

        return $this->render('admin/index.html.twig', [
            'categories' => $categories,
            'comments' => $comments,
            'photos'=> $photos,
            'users'=> $users,
            'reports' => $reports,
            'activeReports' => $activeReports
        ]);
    }

    /**
     * @Route("/report", name="admin_reports")
     * Get all Reports for display
     * Active reports are ON
     * Inactive reports are OFF
     */
    public function adminReports()
    {
        $reports = $this->getDoctrine()
        ->getRepository(Report::class)
        ->getAll();

        $activeReports = $this->getDoctrine()
        ->getRepository(Report::class)
        ->getReportsActive();

        $inactiveReports = $this->getDoctrine()
        ->getRepository(Report::class)
        ->getReportsInactive();

        return $this->render('admin/report.html.twig', [
            'reports' => $reports,
            'activeReports' => $activeReports,
            'inactiveReports' => $inactiveReports
        ]);
    }

    /**
     * @Route("/users", name="admin_users")
     * Get all infos from Users for display
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
     * Get Categories for display
     * Create form for add category
    */
    public function adminCategories(Request $request, EntityManagerInterface $em)
    {
        $categories = $this->getDoctrine()
        ->getRepository(Category::class)
        ->getAll();

        $file = new FileSystem();
        try{
            if($file->exists($this->getParameter('private_directory').'cat_suggest/suggests.txt')){

                $path = $this->getParameter('private_directory').'cat_suggest/suggests.txt';
                
                $f = fopen($path, 'a+');
                $suggests = unserialize(fgets($f)); ;

                $nb_suggests = array_count_values($suggests);

                fclose($f);
            } else {
                $nb_suggests = null;
            }
         } catch( FileException $e) {
            $this->addFlash("error", "Un problème est survenu lors du chargement des suggestions");
         }


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
            "form" => $form->createView(),
            'nb_suggests' => $nb_suggests
        ]);
    }
    
    /**
     * @Route("/ajax/cat_edit", name="ajax_cat_edit")
     * Ajax request
     * Create form for edit category
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
     * Catch response from category edit form
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
     * Delete a category
     */
    public function deleteCat(Request $request, EntityManagerInterface $em, CategoryRepository $crepo)
    {
        $crepo->deleteOne($request->attributes->get("id"));

        $em->flush();
        
        $this->addFlash('success', 'La catégorie a bien été supprimée');
        return $this->redirectToRoute('admin_categories');
    }

    /**
     * @Route("/cat/delete_suggest/{key}", name="cat_delete_suggest")
     * 
     * Delete a suggest from the .txt
     */
    public function deleteSuggest(Request $request){

        $key = $request->attributes->get('key');



            $file = new FileSystem();
            try{
                    $path = $this->getParameter('private_directory').'cat_suggest/suggests.txt';
                    
                    $f = fopen($path, 'a+');
                    $suggests = unserialize(fgets($f)); ;

                    // unset($suggests[$key]);
                    $arr = [$key];

                    $suggests = array_diff($suggests, $arr);

                    // On serialize
                    $s = serialize($suggests);

                    // On clos le fichier
                    fclose($f);

                    // On écrase l'array existant et on met le nouveau
                    file_put_contents($path, $s);

            } catch( FileException $e) {
                $this->addFlash("error", "Un problème est survenu lors du chargement des suggestions");
            }
            $this->addFlash('success', 'La suggestion a bien été supprimée');
            return $this->redirectToRoute('admin_categories');

    }
    
}
