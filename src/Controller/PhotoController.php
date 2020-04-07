<?php

namespace App\Controller;

use App\Entity\Photo;
use App\Form\PhotoType;
use App\Form\PhotoEditType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * @Route("/photo")
 */
class PhotoController extends AbstractController
{
    /**
     * @Route("/", name="photo")
     */
    public function index()
    {

        return $this->redirectToRoute("home");

        // $photos = $this->getDoctrine()
        //             ->getRepository(Photo::class)
        //             ->getAll();

        // return $this->render('photo/index.html.twig', [
        //     'photos' => $photos,
        // ]);
    }
    /**
     * @Route("/add", name="add_photo")
     */
    public function add(Request $request, EntityManagerInterface $em)
    {
        $photo = new Photo();

        $form = $this->createForm(PhotoType::class, $photo);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if($photo->getUser() == null){
                $photo->setUser($this->getUser());
            }


        /** @var UploadedFile $imageFile */
        $imageFile = $form->get('photo')->getData();
        // Rename file with SafeName + UniqId
        $safeFileName = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $photo->getTitle());
        $newFilename = $safeFileName.'-'.uniqid().'.'.$imageFile->guessExtension();

        // Move the file to the directory public/img
        try {
            $path = $photo->getUser()->getId();
            $imageFile->move(
                // img_directory is define in services.yaml
                $this->getParameter('img_directory').$path,
                $newFilename
            );
            $photo->setPath($path."/".$newFilename);
        } catch( FileException $e) {
            $this->addFlash("error", "Une problème est survenu lors de l'upload de l'image");
        }
        $photo->setDateCreation(new \Datetime('now', new \DateTimeZone('Europe/Paris')  ));
        $photo->setNbLike('0');

        $em->persist($photo);
        $em->flush();

        $this->addFlash("success", "La photo a bien été ajouté, merci !");
        return $this->redirectToRoute('home');
        }

        return $this->render('photo/form.html.twig', [
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit_photo")
     */
    public function editPhoto(Photo $photo, Request $request, EntityManagerInterface $em)
    {
        
        if( $this->getUser() == $photo->getUser()){
            $form = $this->createForm(PhotoEditType::class, $photo);

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {
                $photo->setDateUpdate(new \DateTime('now', new \DateTimeZone('Europe/Paris') ));
                $em->flush();
                $this->addFlash("success", "La photo a bien été modifié !");
                return $this->redirectToRoute("profil", array('id' => $this->getUser()->getId()));
            } 

            return $this->render('photo/form.html.twig', [
                "form" => $form->createView(),
                "photo" => $photo
            ]);
        } else {
                $this->addFlash("error", "La photo ne vous appartient pas !");
                return $this->redirectToRoute('home');
            }

    }

    /**
     * @Route("/delete/{id}", name="delete_photo")
     */
    public function deletePhoto(Photo $photo, EntityManagerInterface $em )
    {
        $fileSystem = new Filesystem();

        if ( $this->getUser() == $photo->getUser()){
            try {
                $em->remove($photo);
                // TODO Si on arrive pas à supprimer la photo il ne faut pas supprimer la photo du dossier
                $fileSystem->remove($this->getParameter('img_directory').$photo->getPath());
                $em->flush();
                $this->addFlash("success", "Photo supprimée avec succès !");
            } catch (FileException $e){
                $this->addFlash("error", "Un problème est survenu lors de la suppression");
            }
           
        } else {
            $this->addFlash("error", "La photo ne vous appartient pas !");
        }
        return $this->redirectToRoute("profil", array('id' => $this->getUser()));
    }

    /**
     * @Route("/show/ajax", name="ajax_show", methods={"GET"})
     */
    public function ajax_show(Request $request, EntityManagerInterface $em){

        $photoid = $request->query->get("photoid");
        $photo = $em->getRepository(Photo::class)->findOneBy(['id' => $photoid]);


        $html = $this->renderView("photo/ajaxShow.html.twig", [

            "photo" => $photo

        ]);

        return new Response($html);

    }

        // TODO
        // add like
        // remove like
}
