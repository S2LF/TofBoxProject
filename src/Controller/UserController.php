<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Follow;
use App\Form\RegisterEditType;
use App\Form\RegistrationFormType;
use App\Repository\PhotoRepository;
use App\Repository\FollowRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user")
     */
    public function index()
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }



    /**
     * @Route("/edit/{id}", name="user_edit")
     * 
     */
    public function edit(User $user, Request $request, EntityManagerInterface $em)
    {
        if( $this->getUser() == $user || $this->isGranted('ROLE_ADMIN')){
             $form = $this->createForm(RegisterEditType::class, $user);

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {

                if($form->get('photo_profil')->getData()){
                    /** @var UploadedFile $imageFile */
                    $imageFile = $form->get('photo_profil')->getData();
                    // Rename file with SafeName + UniqId
                    $safeFileName = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $user->getNickname());
                    $newFilename = $safeFileName.'-'.uniqid().'.'.$imageFile->guessExtension();

                    // if ever ptofilPhoto = Remove file
                    if( $user->getPhotoProfil() != "default/default.jpg" ){
                        $fileSystem = new Filesystem();
                        try {
                            $fileSystem->remove($this->getParameter('img_directory').$user->getPhotoProfil());
                        } catch (FileException $e){
                            $this->addFlash("error", "Un problème est survenu lors de la suppression");
                        }
                    }
                    // Move the file to the directory public/img
                    try {
                        $path = $user->getId()."/profil";
                        $imageFile->move(
                            // img_directory is define in services.yaml
                            $this->getParameter('img_directory').$path,
                            $newFilename
                        );
                        $user->setPhotoProfil($path."/".$newFilename);
                    } catch( FileException $e) {
                        $this->addFlash("error", "Une problème est survenu lors de l'upload de l'image");
                    }
                }

                $em->persist($user);
                $em->flush();

                $this->addFlash("success", "Votre profil a bien été modifié");
                return $this->redirectToRoute('profil', array('id' => $user->getId() ) );
            }
            return $this->render('registration/edition.html.twig', [
                "registrationForm" => $form->createView()
            ]);
        } else {
            $this->addFlash("error", "Accès interdit !");
        }
        return $this->redirectToRoute('home');
       
    }


    /**
     * @Route("/delete/ajax", name="delete_profil_ajax", methods={"GET"} )
     */
    public function ajaxDeleteProfil(Request $request, EntityManagerInterface $em){
        $userId = $request->query->get("userId");

        $user = $em->getRepository(User::class)->findOneBy(['id' => $userId]);

        $html = $this->renderView("user/ajaxDelProfile.html.twig", [
            "user" => $user,
            
        ]);

        return new Response($html);

    }

    /**
     * @Route("/delete/{id}", name="delete_profil")
     */
    public function deleteProfil(User $user, EntityManagerInterface $em, PhotoRepository $prepo, FollowRepository $frepo )
    {

        if ( $this->isGranted('ROLE_ADMIN') || $this->getUser() == $user){

            // Supprimer ses photos & le dossier
            if( $user->getPhotos()){

                $fileSystem = new Filesystem();
                try {
                    $fileSystem->remove($this->getParameter('img_directory')."/".$user->getId());

                } catch (FileException $e){
                    $this->addFlash("error", "Un problème est survenu lors de la suppression");
                }
            }
            $prepo->deletePhoto($user->getId());

            $user->setNickname("Compte Supprimé");
            $user->setDescription(null);
            $user->setPhotoProfil('default/default.jpg');

            // Supprime les followed & followBy
            $frepo->deleteAllFollow($user->getId());
            
            $em->flush();
        } else {
            $this->addFlash("error", "Vous ne pouvez pas faire ça !");
        }
        return $this->redirectToRoute('home');

    }

    /**
     * @Route("/{id}", name="profil")
     */
    public function userProfil(User $user, Request $request, EntityManagerInterface $em, FollowRepository $frepo)
    {
        $id = $request->attributes->get('id');
        
        $currentUser = $em->getRepository(User::class)->findOneBy(['id' => $id]);

        if($this->getUser()){
            $isFollow = $frepo->isFollow($id, $this->getUser()->getId());
        } else {
            $isFollow = false;
        }

        return $this->render('user/showOne.html.twig', [
            'isFollow' => $isFollow,
            'user' => $user
        ]);
    }

    /**
     * @Route("/ajax/follow", name="follow")
     */
    public function follow(Request $request, EntityManagerInterface $em, FollowRepository $frepo)
    {
        $id = $request->request->get('userId');
        $followedUser = $em->getRepository(User::class)->findOneBy(['id' => $id]);
        $followByUser = $this->getUser();


        // Si il suit déjà la personne on arrête de suivre
        if($frepo->isFollow($id, $this->getUser()->getId()) == true){
            $frepo->deleteFollow($this->getUser()->getId(), $id);
            $em->flush();

        } 
        // Si il suit pas la personne on le faire suivre
        elseif($frepo->isFollow($id, $this->getUser()->getId()) == false) {
            $follow = new Follow();
            $follow->setFollowByUsers($followByUser);
            $follow->setFollowedUsers($followedUser);
            $follow->setDateFollow(new \Datetime('now', new \DateTimeZone('Europe/Paris')  ));

            $em->persist($follow);
            $em->flush();
        }
        
        // On actualise isFollow
        if($this->getUser()){
            $isFollow = $frepo->isFollow($id, $this->getUser()->getId());
        } else {
            // Si il n'y a pas de User courant on renvoie false
            $isFollow = false;
        }
        $followNb = count($followedUser->getFollowedUsers());
        // $html = $this->renderView("user/ajaxFollow.html.twig", [
        //     "isFollow" => $isFollow,
        //     "user" => $followedUser
        // ]);

        // return new Response($html);


        $html = [
            "isFollow" => $isFollow ,
            "followNb" => $followNb,
            "user"=> [
                "id" => $followByUser->getId(),
                "nickname"=> $followByUser->getNickname(),
                "photoProfil" => $followByUser->getPhotoProfil()
            ]];
        return new JsonResponse($html);
    }



    /**
     * @Route("/ajax/refreshlist", name="refresh_list")
     */
    public function refresh_list(Request $request, EntityManagerInterface $em){

        $id = $request->request->get('userId');
        $user = $em->getRepository(User::class)->findOneBy(['id' => $id]);

        $html = $this->renderView("user/ajaxList.html.twig", [
            "user" => $user
        ]);
        return new Response($html);
    }


}
