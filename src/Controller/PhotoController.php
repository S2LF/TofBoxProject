<?php

namespace App\Controller;

use App\Entity\Photo;
use App\Entity\Comment;
use App\Form\PhotoType;
use App\Form\PhotoEditType;
use App\Form\AddCommentType;
use App\Repository\PhotoRepository;
use App\Repository\FollowRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * @Route("/photo")
 */
class PhotoController extends AbstractController
{
    /**
     * @Route("/", name="photo")
     * 
     * Redirect to homepage
     * Not use in the project
     */
    public function index()
    {

        return $this->redirectToRoute("home");
    }
    /**
     * @Route("/add", name="add_photo")
     * 
     * Display form to add photo
     * Get this form
     * Rename and move img
     * Set informations
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

            if(count($form->get('categories')->getData()) == 0){
                $this->addFlash("error", "Veuillez ajouter au moins une catégorie");
                return $this->render('photo/form.html.twig', [
                    "form" => $form->createView()
                ]);
            } else {
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

                $em->persist($photo);
                $em->flush();
                $this->addFlash("success", "La photo a bien été ajouté, merci !");
            }

        return $this->redirectToRoute('user_photos', array('id' => $photo->getUser()->getId()) );
        }

        return $this->render('photo/form.html.twig', [
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit_photo")
     * @IsGranted("ROLE_USER")
     * 
     * Check if User can edit this photo
     * Display edit photo form
     * Save informations
     */
    public function editPhoto(Photo $photo, Request $request, EntityManagerInterface $em)
    {
        
        if( $this->isGranted('ROLE_ADMIN') || $this->getUser() == $photo->getUser() ){
            $form = $this->createForm(PhotoEditType::class, $photo);

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {
                $photo->setDateUpdate(new \DateTime('now', new \DateTimeZone('Europe/Paris') ));

                if(count($form->get('categories')->getData()) == 0){
                    $this->addFlash("error", "Veuillez ajouter au moins une catégorie");
                    return $this->render('photo/form.html.twig', [
                        "form" => $form->createView(),
                        "photo" => $photo
                    ]);
                } else {
                    $em->flush();
                    $this->addFlash("success", "La photo a bien été modifiée !");
                    return $this->redirectToRoute('user_photos', array('id' => $photo->getUser()->getId()));
                }
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
     * @IsGranted("ROLE_USER")
     * 
     * Check if User can delete this photo
     * Remove photo & remove file
     */
    public function deletePhoto(Photo $photo, EntityManagerInterface $em )
    {
        $fileSystem = new Filesystem();

        if ( $this->isGranted('ROLE_ADMIN') || $this->getUser() == $photo->getUser()){
            try {
                $em->remove($photo);
                $fileSystem->remove($this->getParameter('img_directory').$photo->getPath());
                $em->flush();

                $this->addFlash("success", "Photo supprimée avec succès !");
            } catch (FileException $e){
                $this->addFlash("error", "Un problème est survenu lors de la suppression");
            }
           
        } else {
            $this->addFlash("error", "La photo ne vous appartient pas !");
        }
        return $this->redirectToRoute('user_photos', array('id' => $photo->getUser()->getId()));
    }

    /**
     * @Route("/show/ajax", name="ajax_show")
     * 
     * Ajax request
     * 
     * get photo & last 4 photos
     * get isLiking status
     * get isFollow status
     * 
     * Display photo in a modal
     * 
     */
    public function ajax_show(Request $request, EntityManagerInterface $em, PhotoRepository $prepo, FollowRepository $frepo){

        // On récupère le Photo ID passé en argument
        $photoid = $request->query->get("photoid");
        // On appelle la méthode 'findOneBy' dans le Repository lié à l'Entité Photo
        $photo = $em->getRepository(Photo::class)->findOneBy(['id' => $photoid]);
        // On récupère les 4 dernièrs photos grâce à la méthode 'findLasts'
        $lastsPhotos = $prepo->findLastsByUser(4, $photo->getUser()->getId());

        // Si l'utilisateur courant 'like' la photo
        // $isLiking = true ou false
        if($photo->getLikeUsers()->contains($this->getUser())){
            $isLiking = true;
        } else {
            $isLiking = false;
        };

        // On actualise isFollow
        if($this->getUser()){
            $isFollow = $frepo->isFollow($photo->getUser()->getId(), $this->getUser()->getId());
        } else {
            // Si il n'y a pas de User courant on renvoie false
            $isFollow = false;
        }

        // On renvoie les informations nécessaires à l'affichage vers la Vue, ici 'photo/ajaxShow.html.twig'
        $html = $this->renderView("photo/ajaxShow.html.twig", [
            "lastsPhotos" => $lastsPhotos,
            "photo" => $photo,
            "isLiking" => $isLiking,
            "isFollow" => $isFollow
        ]);
        
        // On retourne une réponse qui contient la VUe et les informations nécessaire.
        return new Response($html);
    }

    /**
     * @Route("/del/ajax", name="ajax_del", methods={"GET"})
     * 
     * Display photos modal 
     * Need to confirm to delete pthis hoto
     * 
     */
    public function ajax_del(Request $request, EntityManagerInterface $em, PhotoRepository $prepo, FollowRepository $frepo){
        $photoid = $request->query->get("photoid");
        $photo = $em->getRepository(Photo::class)->findOneBy(['id' => $photoid]);
        $lastsPhotos = $prepo->findLastsByUser(4, $photo->getUser()->getId());

        if($photo->getLikeUsers()->contains($this->getUser())){
            $isLiking = true;
        } else {
            $isLiking = false;
        };

                // On actualise isFollow
                if($this->getUser()){
                    $isFollow = $frepo->isFollow($photo->getUser()->getId(), $this->getUser()->getId());
                } else {
                    // Si il n'y a pas de User courant on renvoie false
                    $isFollow = false;
                }

        $html = $this->renderView("photo/ajaxDel.html.twig", [
            "lastsPhotos" => $lastsPhotos,
            "photo" => $photo,
            "isLiking" => $isLiking,
            "isFollow" => $isFollow

        ]);

        return new Response($html);
    }

    /**
     * @Route("/ajax/like", name="like")
     * 
     * Ajax request
     * Get Like status & change this status
     */
    public function Like(Request $request, EntityManagerInterface $em)
    {
        $photoid = $request->request->get("photoId");
        $photo = $em->getRepository(Photo::class)->findOneBy(['id' => $photoid]);

        // Si le CurrentUser like la photo => On Unlike
        if($photo->getLikeUsers()->contains($this->getUser())){
            $photo->removeLikeUser($this->getUser());
            $em->flush();
        }
        // Sinon le CurrentUser like la photo
        elseif(!$photo->getLikeUsers()->contains($this->getUser())){
            $like = $photo->addLikeUser($this->getUser());
            $em->persist($like);
            $em->flush();
        }

        // On actualise isLiking
        if($photo->getLikeUsers()->contains($this->getUser())){
            $isLiking = true;
        } else {
            $isLiking = false;
        };


        $html = $this->renderView("photo/ajaxLike.html.twig", [
            "isLiking" => $isLiking,
            "photo" => $photo
        ]);

        
        return new Response($html);
    }

}
